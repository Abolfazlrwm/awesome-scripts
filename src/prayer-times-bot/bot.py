#!/usr/bin/env python3
# Telegram: @Abolfazl_rwm
"""Prayer Times bot — offline اوقات شرقی for Iranian cities.

All times are computed locally from standard solar-position astronomy
(NOAA-style low-precision declination + equation of time) — no API, no
network. Uses the Tehran Institute of Geophysics convention common in
Iran: Fajr 17.7°, Isha 14°. Also computes Shafi Asr (shadow factor 1)
and the Qibla bearing toward the Kaaba for every city.

Stdlib (sqlite3 not needed — pure math) + python-telegram-bot.
"""

from __future__ import annotations

import datetime as dt
import math

from telegram import InlineKeyboardButton, InlineKeyboardMarkup, Update
from telegram.ext import (
    Application,
    CallbackQueryHandler,
    CommandHandler,
    ContextTypes,
)

BOT_TOKEN = ""  # توکن ربات از @BotFather

TEHRAN_TZ = dt.timezone(dt.timedelta(hours=3, minutes=30))
FAJR_ANGLE = 17.7    # Institute of Geophysics, Tehran University
ISHA_ANGLE = 14.0    # Institute of Geophysics, Tehran University
SUNRISE_ANGLE = 0.833  # standard refraction + solar disc

# Persian cities: name → (lat, lng) — coordinates in decimal degrees
CITIES: dict[str, tuple[float, float]] = {
    "تهران": (35.6892, 51.3890), "مشهد": (36.2605, 59.6168),
    "اصفهان": (32.6539, 51.6680), "تبریز": (38.0962, 46.2738),
    "شیراز": (29.5918, 52.5837), "اهواز": (31.3183, 48.6706),
    "قم": (34.6416, 50.8746), "کرج": (35.8355, 50.9915),
    "رشت": (37.2808, 49.5832), "کرمان": (30.2839, 57.0834),
    "یزد": (31.8974, 54.3569), "بندرعباس": (27.1865, 56.2808),
    "ارومیه": (37.5527, 45.0761), "زاهدان": (29.4963, 60.8629),
    "سنندج": (35.3219, 46.9862), "همدان": (34.7992, 48.5145),
    "اردبیل": (38.2498, 48.2933), "بوشهر": (28.9234, 50.8203),
    "کرمانشاه": (34.3149, 47.0650), "گرگان": (36.8427, 54.4444),
    "بیرجند": (32.8663, 59.2211), "خرم‌آباد": (33.4878, 48.3558),
    "ایلام": (33.6374, 46.4227), "شهركرد": (32.3256, 50.9137),
    "سمنان": (35.5769, 53.3921), "قزوین": (36.2688, 50.0041),
    "زنجان": (36.6736, 48.4787), "کاشان": (33.9831, 51.4364),
    "نیشابور": (36.2133, 58.7958), "ساری": (36.5634, 53.0601),
}

KAABA = (21.4225, 39.8262)  # مکه مکرمه

FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")
FA_MONTHS = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور",
             "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"]
PRAYER_ICONS = [("Fajr", "🌅 اذان صبح"), ("Sunrise", "🌄 طلوع آفتاب"),
                ("Dhuhr", "☀️ اذان ظهر"), ("Asr", "🌤 اذان عصر"),
                ("Maghrib", "🌇 اذان مغرب"), ("Isha", "🌆 اذان عشا")]


def fa(text) -> str:
    return str(text).translate(FA_DIGITS)


# ------------------------------------------------------------ astronomy
def _julian_day(date: dt.date) -> float:
    a = (14 - date.month) // 12
    y = date.year + 4800 - a
    m = date.month + 12 * a - 3
    return (date.day + (153 * m + 2) // 5 + 365 * y + y // 4 - y // 100
            + y // 400 - 32045) - 0.5


def _sun_position(date: dt.date) -> tuple[float, float]:
    """Low-precision solar declination (deg) and equation of time (minutes).

    Standard formulas (Duffie & Beckman / NOAA approximations):
      declination δ = 23.45° · sin(360°·(284+n)/365)
      equation of time from the day-angle Γ (radians), accurate ≈ ±1 min.
    """
    n = date.timetuple().tm_yday
    gamma = 2 * math.pi / 365 * (n - 1 + (12 - 12) / 24)
    decl = (0.006918 - 0.399912 * math.cos(gamma) + 0.070257 * math.sin(gamma)
            - 0.006758 * math.cos(2 * gamma) + 0.000907 * math.sin(2 * gamma)
            - 0.002697 * math.cos(3 * gamma) + 0.00148 * math.sin(3 * gamma))
    eot = 229.18 * (0.000075 + 0.001868 * math.cos(gamma) - 0.032077 * math.sin(gamma)
                    - 0.014615 * math.cos(2 * gamma) - 0.040849 * math.sin(2 * gamma))
    return math.degrees(decl), eot


def _hour_angle(angle_deg: float, lat: float, decl: float) -> float:
    """Time offset (hours) from solar noon when the sun reaches `angle_deg`
    below/above horizon. cos(H) = (sin(angle) − sin(lat)·sin(δ)) / (cos(lat)·cos(δ))"""
    a, la, d = map(math.radians, (angle_deg, lat, decl))
    x = (math.sin(a) - math.sin(la) * math.sin(d)) / (math.cos(la) * math.cos(d))
    return math.degrees(math.acos(max(-1.0, min(1.0, x)))) / 15.0


def _asr_hour_angle(lat: float, decl: float, factor: float = 1.0) -> float:
    """Shafi Asr: shadow length = factor × object height.
    altitude θ where cot(θ) = factor + tan(|lat − δ|)."""
    la, d = map(math.radians, (lat, decl))
    theta = math.atan(1.0 / (factor + math.tan(abs(la - d))))
    x = (math.sin(theta) - math.sin(la) * math.sin(d)) / (math.cos(la) * math.cos(d))
    return math.degrees(math.acos(max(-1.0, min(1.0, x)))) / 15.0


def compute_times(lat: float, lng: float, date: dt.date,
                  tz: dt.timezone = TEHRAN_TZ) -> dict[str, float]:
    """Return local clock hours (float) for all prayer times."""
    decl, eot = _sun_position(date)
    dhuhr = 12 - lng / 15 - eot / 60  # solar-noon in UTC hours
    noon_local = dt.datetime.combine(date, dt.time(12), tzinfo=tz)
    local = dhuhr + tz.utcoffset(noon_local).total_seconds() / 3600
    return {
        # below-horizon angles are negative: Fajr −17.7°, Isha −14°, rise/set −0.833°
        "Fajr": local - _hour_angle(-FAJR_ANGLE, lat, decl),
        "Sunrise": local - _hour_angle(-SUNRISE_ANGLE, lat, decl),
        "Dhuhr": local + 2 / 60,  # small safety offset after zenith
        "Asr": local + _asr_hour_angle(lat, decl),
        "Maghrib": local + _hour_angle(-SUNRISE_ANGLE, lat, decl),
        "Isha": local + _hour_angle(-ISHA_ANGLE, lat, decl),
    }


def qibla_bearing(lat: float, lng: float) -> float:
    """Great-circle initial bearing from a point to the Kaaba (deg from North).
    Canonical form: θ = atan2(cos φ2·sin Δλ, cos φ1·sin φ2 − sin φ1·cos φ2·cos Δλ)"""
    la1, la2 = map(math.radians, (lat, KAABA[0]))
    dlng = math.radians(KAABA[1] - lng)
    x = math.cos(la2) * math.sin(dlng)
    y = math.cos(la1) * math.sin(la2) - math.sin(la1) * math.cos(la2) * math.cos(dlng)
    return (math.degrees(math.atan2(x, y)) + 360) % 360


# ------------------------------------------------------------ formatting
def fmt_hour(h: float) -> str:
    minutes = round(h * 60) % (24 * 60)
    return f"{minutes // 60:02d}:{minutes % 60:02d}".translate(FA_DIGITS)


def times_message(city: str, date: dt.date) -> str:
    lat, lng = CITIES[city]
    t = compute_times(lat, lng, date)
    lines = [f"🕌 <b>اوقات شرعی {city}</b> — {fa(date.year)}/{fa(date.month):>2}/{fa(date.day):>2}\n"]
    for key, label in PRAYER_ICONS:
        lines.append(f"{label}: <b>{fmt_hour(t[key])}</b>")
    # religious midnight (Ja'fari convention): fajr + (maghrib − fajr)/2
    midnight = t["Fajr"] + (t["Maghrib"] - t["Fajr"]) / 2 + 12
    lines.append(f"🌌 نیمه‌شب شرعی: <b>{fmt_hour(midnight)}</b>")
    lines.append("\n<i>روش: مؤسسه ژئوفیزیک تهران (کمان صبح ۱۷٫۷° و عشا ۱۴°)، عصر شافعی</i>")
    return "\n".join(lines)


def next_prayer(city: str, now_dt: dt.datetime) -> str | None:
    lat, lng = CITIES[city]
    t = compute_times(lat, lng, now_dt.date())
    now_h = now_dt.hour + now_dt.minute / 60
    order = ["Fajr", "Sunrise", "Dhuhr", "Asr", "Maghrib", "Isha"]
    for key in order:
        if t[key] > now_h:
            diff = (t[key] - now_h) * 60
            return (f"⏳ <b>{dict(PRAYER_ICONS)[key]}</b> {city} ساعت "
                    f"<b>{fmt_hour(t[key])}</b> — {fa(int(diff))} دقیقه مانده")
    return None  # after Isha → tomorrow's Fajr


def resolve_city(args: list[str]) -> str | None:
    if not args:
        return None
    query = " ".join(args).strip().replace("‌", "").replace(" ", "")
    for name in CITIES:
        if name.replace("‌", "").replace(" ", "") == query:
            return name
    for name in CITIES:  # substring fallback
        if query and query in name.replace("‌", ""):
            return name
    return None


def city_keyboard() -> InlineKeyboardMarkup:
    names = sorted(CITIES, key=lambda c: -1 if c == "تهران" else 0)
    rows = [names[i:i + 3] for i in range(0, len(names), 3)]
    return InlineKeyboardMarkup([
        [InlineKeyboardButton(c, callback_data=f"city:{c}") for c in row] for row in rows
    ])


# ------------------------------------------------------------ handlers
async def cmd_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "🕌 <b>اوقات شرعی</b> — کاملاً آفلاین و دقیق\n\n"
        " timeZone /times [شهر] — جدول کامل اوقات امروز\n"
        " ⏳ /next [شهر] — وقتی تا نماز بعدی مانده\n"
        " 🧭 /qibla [شهر] — زاویه قبله از شمال\n\n"
        "شهر پیش‌فرض تهران است؛ از دکمه‌ها هم می‌توانی انتخاب کنی.\n"
        "<i>(مثال: /times مشهد یا /qibla شیراز)</i>",
        parse_mode="HTML",
        reply_markup=city_keyboard(),
    )


async def cmd_times(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    city = resolve_city(ctx.args) or "تهران"
    await update.message.reply_text(
        times_message(city, dt.datetime.now(TEHRAN_TZ).date()),
        parse_mode="HTML",
        reply_markup=city_keyboard(),
    )


async def cmd_next(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    city = resolve_city(ctx.args) or "تهران"
    msg = next_prayer(city, dt.datetime.now(TEHRAN_TZ))
    if msg is None:
        await update.message.reply_text(
            f"🌙 اذان عشا خوانده شده — فردا صبح با <b>اذان صبح {city}</b> ساعت "
            f"<b>{fmt_hour(compute_times(*CITIES[city], dt.date.today())['Fajr'])}</b>",
            parse_mode="HTML",
        )
        return
    await update.message.reply_text(msg, parse_mode="HTML", reply_markup=city_keyboard())


async def cmd_qibla(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    city = resolve_city(ctx.args) or "تهران"
    lat, lng = CITIES[city]
    bearing = qibla_bearing(lat, lng)
    compass = ["شمال", "شمال‌شرق", "شرق", "جنوب‌شرق", "جنوب",
               "جنوب‌غرب", "غرب", "شمال‌غرب"][int((bearing + 22.5) // 45) % 8]
    await update.message.reply_text(
        f"🧭 <b>قبله از {city}:</b>\n\n"
        f"زاویه از شمال (ساعت‌گرد): <b>{fa(round(bearing, 1))}°</b> ({compass})\n"
        f"<i>قطب‌نما را روی این زاویه بگیرید — همه‌جا از این شهر یکسان است.</i>",
        parse_mode="HTML",
        reply_markup=city_keyboard(),
    )


async def on_city(update: Update, ctx: ContextTypes.DEFAULT_TYPE) -> None:
    query = update.callback_query
    await query.answer()
    city = query.data.split(":", 1)[1]
    await query.message.reply_text(
        times_message(city, dt.datetime.now(TEHRAN_TZ).date()), parse_mode="HTML"
    )


def main() -> None:
    if not BOT_TOKEN:
        raise SystemExit("BOT_TOKEN را در بالای فایل تنظیم کنید (توکن از @BotFather)")
    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("times", cmd_times))
    app.add_handler(CommandHandler("next", cmd_next))
    app.add_handler(CommandHandler("qibla", cmd_qibla))
    app.add_handler(CallbackQueryHandler(on_city, pattern=r"^city:"))
    print("Prayer Times bot up —", len(CITIES), "cities, fully offline")
    app.run_polling()


if __name__ == "__main__":
    main()
