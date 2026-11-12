# Telegram: @Abolfazl_rwm
import os, io, re, time, math, ast, operator, asyncio, logging, datetime as dt
import numpy as np
import requests
import urllib3
import arabic_reshaper
from bidi.algorithm import get_display
from PIL import Image, ImageDraw, ImageFont, ImageFilter
from telegram import Update
from telegram.ext import ApplicationBuilder, MessageHandler, CommandHandler, ContextTypes, filters

urllib3.disable_warnings()
logging.basicConfig(level=logging.INFO)
log = logging.getLogger("pricebot")

try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

BOT_TOKEN = os.getenv("BOT_TOKEN", "").strip()
if not BOT_TOKEN:
    raise SystemExit(
        "❌ BOT_TOKEN تنظیم نشده — آن را در فایل .env قرار دهید (توکن را از @BotFather بگیرید)."
    )

FOOTER = os.getenv("FOOTER", "@Abolfazl_rwm")  # امضای پایین کارت‌ها
CACHE_SECONDS = 20
IMG_API = ["https://karbaladev.ir/api/ai/image", "http://karbaladev.ir/api/ai/image"]
DATA_SOURCES = [
    "https://raw.githubusercontent.com/iran-market/iran-market.github.io/main/data/",
    "https://iran-market.github.io/data/",
    "https://cdn.jsdelivr.net/gh/iran-market/iran-market.github.io@main/data/",
]
UA = {"User-Agent": "Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 Chrome/120 Mobile Safari/537.36"}

BASE = os.path.dirname(os.path.abspath(__file__))
FONT_BOLD = os.path.join(BASE, "Vazirmatn-Bold.ttf")
FONT_REG = os.path.join(BASE, "Vazirmatn-Regular.ttf")
FONT_URLS = [
    "https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/ttf/{n}",
    "https://github.com/rastikerdar/vazirmatn/raw/master/fonts/ttf/{n}",
]
SYSTEM_FONTS = [
    "/system/fonts/NotoNaskhArabic-Bold.ttf", "/system/fonts/NotoNaskhArabic-Regular.ttf",
    "/system/fonts/NotoSansArabic-Bold.ttf", "/system/fonts/NotoSansArabic-Regular.ttf",
    "/system/fonts/Roboto-Bold.ttf", "/system/fonts/Roboto-Regular.ttf", "/system/fonts/DroidSans.ttf",
]
W, H = 1200, 675
GREEN, RED = (34, 197, 94), (239, 68, 68)
PILL = {True: (214, 244, 225), False: (252, 222, 222)}


_FA2EN = str.maketrans("۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩", "01234567890123456789")
_EN2FA = str.maketrans("0123456789,.", "۰۱۲۳۴۵۶۷۸۹٬٫")


def norm(t):
    t = t.translate(_FA2EN).replace("ي", "ی").replace("ك", "ک").replace("\u200c", " ")
    t = t.replace("٬", ",").replace("٫", ".").replace("$", " usd ").lower()
    t = re.sub(r"(?<=\d),(?=\d{3})", "", t)
    t = re.sub(r"[؟?!،:؛;()«»\"'…\-_/\\]+", " ", t)
    t = re.sub(r"(?<=\d)(?=[^\d\s.,])", " ", t)
    t = re.sub(r"(?<=[^\d\s.,])(?=\d)", " ", t)
    return re.sub(r"\s+", " ", t).strip()


def pd(s):
    return str(s).translate(_EN2FA)


ASSETS, ALIAS = {}, {}
BGS = ", blurred background, soft bokeh, cinematic lighting, no text, no letters, high quality"


def _a(key, name, aliases, syms, prompt, emoji="💰", label=None, code=None, fa=None, bg=(30, 50, 110)):
    ASSETS[key] = dict(name=name, label=label or name, emoji=emoji, syms=syms, code=code,
                       fa=fa or [name], prompt=prompt + BGS, bg=bg)
    for al in aliases:
        ALIAS[norm(al)] = key


_a("irt", "تومان", ["تومان", "تومن", "toman", "irt"], [], "iran money", bg=(20, 90, 60))
_a("usd", "دلار آمریکا", ["دلار", "دلار آمریکا", "دلار امریکا", "dollar", "usd"], ["USD_IRR_FREE"],
   "waving flag of the United States of America", "🇺🇸", code="USD", fa=["دلار", "دلار آمریکا"], bg=(60, 59, 110))
_a("eur", "یورو", ["یورو", "یورو اروپا", "euro", "eur"], ["EUR_IRR_FREE"],
   "waving flag of the European Union", "🇪🇺", label="یورو اروپا", code="EUR", fa=["یورو"], bg=(0, 51, 153))
_a("gbp", "پوند انگلیس", ["پوند", "پوند انگلیس", "pound", "gbp"], ["GBP_IRR_FREE"],
   "waving flag of the United Kingdom union jack", "🇬🇧", code="GBP", bg=(20, 40, 100))
_a("chf", "فرانک سوئیس", ["فرانک", "فرانک سوئیس", "chf"], ["CHF_IRR_FREE"],
   "waving flag of Switzerland", "🇨🇭", code="CHF", fa=["فرانک سوئیس", "فرانک"], bg=(150, 20, 30))
_a("cad", "دلار کانادا", ["دلار کانادا", "cad"], ["CAD_IRR_FREE"],
   "waving flag of Canada maple leaf", "🇨🇦", code="CAD", bg=(150, 20, 30))
_a("try", "لیر ترکیه", ["لیر", "لیر ترکیه", "lira", "try"], ["TRY_IRR_FREE"],
   "waving flag of Turkey", "🇹🇷", code="TRY", fa=["لیر ترکیه", "لیر"], bg=(160, 20, 30))
_a("rub", "روبل روسیه", ["روبل", "روبل روسیه", "rub"], ["RUB_IRR_FREE"],
   "waving flag of Russia", "🇷🇺", code="RUB", bg=(30, 50, 120))
_a("inr", "روپیه هند", ["روپیه", "روپیه هند", "rupee", "inr"], ["INR_IRR_FREE"],
   "waving flag of India", "🇮🇳", code="INR", fa=["روپیه هند", "روپیه"], bg=(200, 110, 20))
_a("cny", "یوان چین", ["یوان", "یوان چین", "yuan", "cny"], ["CNY_IRR_FREE"],
   "waving flag of China", "🇨🇳", code="CNY", bg=(170, 20, 30))
_a("iqd", "دینار عراق", ["دینار", "دینار عراق", "iqd"], ["IQD_IRR_FREE"],
   "waving flag of Iraq", "🇮🇶", code="IQD", bg=(40, 40, 40))
_a("aed", "درهم امارات", ["درهم", "درهم امارات", "aed"], ["AED_IRR_FREE"],
   "waving flag of the United Arab Emirates", "🇦🇪", code="AED", bg=(20, 110, 60))
_a("coin", "سکه امامی", ["سکه", "سکه امامی", "امامی", "coin"], ["COIN_EMAMI_IRR"],
   "shiny gold coins pile, iranian gold coin", "🟡", label="سکه", fa=["سکه امامی"], bg=(120, 90, 20))
_a("bahar", "سکه بهار آزادی", ["سکه بهار", "بهار آزادی", "سکه بهار آزادی"], ["COIN_BAHAR_IRR"],
   "shiny gold coins pile", "🟡", bg=(120, 90, 20))
_a("half", "نیم سکه", ["نیم سکه", "نیم"], ["COIN_HALF_IRR"], "shiny gold coins pile", "🟡", bg=(120, 90, 20))
_a("quarter", "ربع سکه", ["ربع سکه", "ربع"], ["COIN_QUARTER_IRR"], "shiny gold coins pile", "🟡", bg=(120, 90, 20))
_a("grami", "سکه گرمی", ["سکه گرمی", "گرمی"], ["COIN_GRAMI_IRR"], "small shiny gold coins", "🟡", bg=(120, 90, 20))
_a("mesghal", "مثقال طلا", ["مثقال", "مثقال طلا"], ["GOLD_MESGHAL_IRR"],
   "stack of gold bars ingots", "💰", bg=(130, 95, 20))
_a("gold18", "طلای 18 عیار", ["طلا", "طلای 18", "طلا 18", "طلای 18 عیار", "طلا 18 عیار", "18 عیار", "gold", "gold18"],
   ["GOLD_18K_IRR"], "gold bars and gold jewelry shining", "💰", label="طلا", fa=["طلای 18 عیار"], bg=(130, 95, 20))
_a("gold24", "طلای 24 عیار", ["طلای 24", "طلا 24", "طلای 24 عیار", "طلا 24 عیار", "24 عیار", "gold24"],
   ["GOLD_24K_IRR"], "pure gold bars ingots shining luxury", "💰", fa=["طلای 24 عیار"], bg=(130, 95, 20))
_a("silver", "نقره", ["نقره", "silver"], ["SILVER_999_IRR"],
   "silver bars ingots shining", "⚪️", fa=["گرم نقره 999", "نقره"], bg=(90, 95, 105))
_a("ounce", "اونس طلا", ["اونس", "اونس طلا", "انس", "انس طلا", "xau", "ounce"], ["XAU_USD"],
   "gold bar and world map finance", "💰", fa=["انس طلا", "اونس طلا"], bg=(130, 95, 20))
_a("usdt", "تتر", ["تتر", "usdt", "tether"], ["USDT_IRR"],
   "green tether USDT crypto coin glowing", "💰", fa=["تتر"], bg=(38, 161, 123))
_a("btc", "بیت‌کوین", ["بیتکوین", "بیت کوین", "btc", "bitcoin"], ["BTC_USD"],
   "golden bitcoin coin glowing", "💰", code="BTC", fa=["بیت کوین", "بیت‌کوین"], bg=(200, 120, 20))
_a("eth", "اتریوم", ["اتریوم", "eth", "ethereum"], ["ETH_USD"],
   "ethereum crypto coin glowing purple", "💰", code="ETH", bg=(70, 70, 140))
_a("bnb", "بایننس‌کوین", ["بایننس", "بایننس کوین", "bnb"], ["BNB_USD"],
   "yellow binance coin BNB crypto glowing", "💰", code="BNB", fa=["بایننس کوین", "بایننس‌کوین"], bg=(160, 120, 10))
_a("sol", "سولانا", ["سولانا", "sol", "solana"], ["SOL_USD"],
   "solana crypto coin neon purple green", "💰", code="SOL", bg=(80, 40, 140))
_a("xrp", "ریپل", ["ریپل", "xrp", "ripple"], ["XRP_USD"],
   "ripple XRP crypto coin silver blue", "💰", code="XRP", bg=(30, 40, 60))
_a("ton", "تون", ["تون", "تون کوین", "ton", "toncoin"], ["TON_USD"],
   "toncoin TON crypto blue diamond glowing", "💰", code="TON", fa=["تون", "تون کوین"], bg=(20, 100, 190))
_a("gram", "گرام", ["گرام", "gram"], ["GRAM_USD"],
   "gram crypto coin blue glowing", "💰", code="GRAM", fa=["گرام"], bg=(20, 100, 190))
_a("trx", "ترون", ["ترون", "trx", "tron"], ["TRX_USD"],
   "tron TRX crypto coin red glowing", "💰", code="TRX", bg=(150, 30, 40))

LIST_LAYOUT = [
    ("ارزها", ["usd", "eur", "gbp", "chf", "cad", "try", "rub", "inr", "cny", "iqd", "aed"]),
    ("طلا و سکه", ["coin", "mesghal", "gold18", "silver", "ounce"]),
    ("ارز دیجیتال", ["btc", "eth", "usdt", "bnb", "sol", "xrp", "gram", "ton", "trx"]),
]
LIST_PROMPT = "luxury finance trading background gold coins and dollar bills, dark" + BGS

FILLERS = {"نرخ", "قیمت", "چقدر", "چنده", "چند", "چقد", "چقده", "میشه", "میشود", "میشن", "میشه", "می", "شه",
           "بده", "بگو", "کن", "تبدیل", "به", "معادل", "یعنی", "هست", "است", "الان", "امروز", "لطفا",
           "گرم", "عدد", "واحد", "ارزش", "برابر", "ها", "میکنه", "میده", "کنه", "رو", "را", "چن"}
LIST_WORDS = {"نرخ", "قیمت", "لیست", "ارز", "ارزها", "بازار", "نرخها", "قیمتها", "لحظه", "ای", "همه", "کامل"}
MULT = {"هزار": 1e3, "k": 1e3, "میلیون": 1e6, "ملیون": 1e6, "میلیارد": 1e9, "ملیارد": 1e9}


def parse(text):
    toks = norm(text).split()
    if not toks or len(toks) > 10:
        return None
    amount, assets, i = None, [], 0
    while i < len(toks):
        hit = False
        for n in (4, 3, 2, 1):
            part = toks[i:i + n]
            if len(part) == n and " ".join(part) in ALIAS:
                assets.append(ALIAS[" ".join(part)])
                i += n
                hit = True
                break
        if hit:
            continue
        tok = toks[i]
        if re.fullmatch(r"\d+(\.\d+)?", tok):
            if amount is not None:
                return None
            val, i = float(tok), i + 1
            while i < len(toks) and toks[i] in MULT:
                val *= MULT[toks[i]]
                i += 1
            amount = val
            continue
        if tok in FILLERS or tok in LIST_WORDS:
            i += 1
            continue
        return None
    ded = []
    for a in assets:
        if not ded or ded[-1] != a:
            ded.append(a)
    if not ded:
        if amount is None and any(t in LIST_WORDS for t in toks):
            return {"list": True}
        return None
    if len(ded) == 1 and ded[0] == "irt":
        return None
    return {"list": False, "amount": amount if amount is not None else 1.0, "assets": ded}


def g2j(gy, gm, gd):
    g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334]
    gy2 = gy + 1 if gm > 2 else gy
    days = 355666 + 365 * gy + (gy2 + 3) // 4 - (gy2 + 99) // 100 + (gy2 + 399) // 400 + gd + g_d_m[gm - 1]
    jy = -1595 + 33 * (days // 12053)
    days %= 12053
    jy += 4 * (days // 1461)
    days %= 1461
    if days > 365:
        jy += (days - 1) // 365
        days = (days - 1) % 365
    if days < 186:
        jm, jd = 1 + days // 31, 1 + days % 31
    else:
        jm, jd = 7 + (days - 186) // 30, 1 + (days - 186) % 30
    return jy, jm, jd


def now_fa():
    n = dt.datetime.now(dt.timezone(dt.timedelta(hours=3, minutes=30)))
    jy, jm, jd = g2j(n.year, n.month, n.day)
    return (jy, jm, jd), n.strftime("%H:%M:%S")


def stamp(padded=True):
    (jy, jm, jd), t = now_fa()
    d = f"{jy}/{jm:02d}/{jd:02d}" if padded else f"{jy}/{jm}/{jd}"
    return d, t


class Market:
    def __init__(self, payload):
        self.items = {}
        self._walk(payload)
        it = self.find("usd")
        if not it:
            raise RuntimeError("USD rate not found in data")
        self.usd_toman = float(it["price"])

    def _walk(self, o):
        if isinstance(o, dict):
            if "symbol" in o and o.get("price") is not None:
                self.items.setdefault(o["symbol"], o)
            else:
                for v in o.values():
                    self._walk(v)
        elif isinstance(o, list):
            for v in o:
                self._walk(v)

    def find(self, key):
        a = ASSETS[key]
        for s in a["syms"]:
            if s in self.items:
                return self.items[s]
        code = a.get("code")
        if code:
            c = [it for s, it in self.items.items() if s.split("_")[0] == code]
            c.sort(key=lambda it: ("FREE" not in it["symbol"], not it["symbol"].endswith(("USD", "FREE"))))
            if c:
                return c[0]
        for it in self.items.values():
            if (it.get("name_fa") or "").strip() in a["fa"]:
                return it
        return None

    def quote(self, key):
        if key == "irt":
            return dict(toman=1.0, native=1.0, cur="IRT", chg=0.0, symbol=None)
        it = self.find(key)
        if not it:
            raise KeyError(key)
        p = float(it["price"])
        cur = (it.get("currency") or "IRT").upper()
        toman = p * self.usd_toman if cur == "USD" else (p / 10 if cur == "IRR" else p)
        return dict(toman=toman, native=p, cur=cur, chg=float(it.get("change_pct") or 0), symbol=it["symbol"])


_market_cache = [0, None]


def get_market():
    now = time.time()
    if _market_cache[1] and now - _market_cache[0] < CACHE_SECONDS:
        return _market_cache[1]
    last = None
    for base in DATA_SOURCES:
        try:
            r = requests.get(base + "latest-toman.json", timeout=20, headers=UA)
            r.raise_for_status()
            m = Market(r.json())
            _market_cache[:] = [now, m]
            return m
        except Exception as e:
            last = e
            log.warning("data source failed (%s): %s", base, e)
    if _market_cache[1]:
        return _market_cache[1]
    raise RuntimeError(f"cannot fetch rates: {last}")


_hist_cache = {}


def get_history(sym):
    c = _hist_cache.get(sym)
    if c and time.time() - c[0] < 3600:
        return c[1]
    vals = None
    for base in DATA_SOURCES:
        try:
            r = requests.get(f"{base}history/{sym}.json", timeout=10, headers=UA)
            if r.status_code != 200:
                continue
            j = r.json()
            rows = j.get("data") if isinstance(j, dict) else j
            v = [float(x["close"]) for x in rows if isinstance(x, dict) and x.get("close") is not None][-60:]
            if len(v) >= 3:
                vals = v
                break
        except Exception:
            continue
    _hist_cache[sym] = (time.time(), vals)
    return vals


def chart_series(q, amount, usd_toman):
    cur = q["native"]
    vals = get_history(q["symbol"]) if q["symbol"] else None
    if vals and vals[-1]:
        f = cur / vals[-1]
        if f > 0:
            e = round(math.log10(f))
            if e != 0 and abs(math.log10(f) - e) < 0.3:
                vals = [v * 10 ** e for v in vals]
        if not (0.33 < cur / vals[-1] < 3):
            vals = None
    if vals:
        series = vals[:-1] + [cur]
    else:
        chg = q["chg"]
        prev = cur / (1 + chg / 100) if chg > -99 else cur
        series = [prev, cur]
    mult = (usd_toman if q["cur"] == "USD" else (0.1 if q["cur"] == "IRR" else 1.0)) * amount
    return [v * mult for v in series]


def fmt_num(v):
    if v >= 1000:
        return f"{v:,.0f}"
    s = f"{v:,.2f}" if v >= 1 else f"{v:.6f}"
    if "." in s:
        s = s.rstrip("0").rstrip(".")
    return s or "0"


def fmt_toman(v):
    return f"{round(v):,}" if v >= 100 else fmt_num(v)


def fmt_amt(a):
    return str(int(a)) if abs(a - round(a)) < 1e-9 else fmt_num(a)


def axis(v):
    return f"{int(v):,}" if v >= 1000 else (f"{v:.2f}" if v >= 1 else f"{v:.4f}")


def ensure_fonts():
    for path in (FONT_BOLD, FONT_REG):
        if os.path.exists(path) and os.path.getsize(path) > 10000:
            continue
        for url in FONT_URLS:
            try:
                r = requests.get(url.format(n=os.path.basename(path)), timeout=60)
                if r.status_code == 200 and len(r.content) > 10000:
                    with open(path, "wb") as f:
                        f.write(r.content)
                    log.info("font downloaded: %s", path)
                    break
            except Exception as e:
                log.warning("font download failed: %s", e)


_fonts = {}


def font(path, size):
    k = (path, size)
    if k not in _fonts:
        for p in [path] + SYSTEM_FONTS:
            try:
                _fonts[k] = ImageFont.truetype(p, size)
                break
            except Exception:
                continue
        else:
            _fonts[k] = ImageFont.load_default(size)
    return _fonts[k]


def fa(t):
    return get_display(arabic_reshaper.reshape(t))


def fallback_bg(color, w, h):
    arr = np.zeros((h, w, 3), np.uint8)
    for i in range(3):
        arr[..., i] = np.linspace(color[i], max(color[i] - 60, 0), h)[:, None]
    return Image.fromarray(arr)


def _img_from_response(r):
    try:
        return Image.open(io.BytesIO(r.content)).convert("RGB")
    except Exception:
        pass
    text = r.text.replace("\\/", "/")
    for url in re.findall(r"https?://[^\s\"'<>\\]+", text):
        if "karbaladev.ir/api/ai/image" in url:
            continue
        try:
            rr = requests.get(url, timeout=40, headers=UA, verify=False)
            return Image.open(io.BytesIO(rr.content)).convert("RGB")
        except Exception:
            continue
    raise ValueError("no image in api response: %s" % r.text[:150].replace("\n", " "))


_bg_fail = {}


def fetch_bg(name, prompt, color, w, h):
    path = os.path.join(BASE, f"bg_{name}.jpg")
    if os.path.exists(path) and time.time() - os.path.getmtime(path) < 7 * 86400:
        return Image.open(path).convert("RGB")
    if time.time() - _bg_fail.get(name, 0) < 600:
        return fallback_bg(color, w, h)
    for url in IMG_API:
        try:
            r = requests.get(url, params={"prompt": prompt}, timeout=35, headers=UA,
                             allow_redirects=True, verify=False)
            img = _img_from_response(r)
            img.save(path, quality=92)
            return img
        except Exception as e:
            log.warning("bg api failed (%s): %s", url, e)
    _bg_fail[name] = time.time()
    return fallback_bg(color, w, h)


def cover(img, w, h):
    s = max(w / img.width, h / img.height)
    img = img.resize((int(img.width * s) + 1, int(img.height * s) + 1), Image.LANCZOS)
    l, t = (img.width - w) // 2, (img.height - h) // 2
    return img.crop((l, t, l + w, t + h))


def chart_layer(series, color):
    S = 2
    x1, y0, y1 = 1060 * S, 355 * S, 555 * S
    lo, hi = min(series), max(series)
    pad = (hi - lo) * 0.08 or max(abs(hi) * 0.01, 1e-9)
    lo, hi = lo - pad, hi + pad
    f = font(FONT_BOLD, 19 * S)
    labels = [axis(hi - (hi - lo) * i / 4) for i in range(5)]
    lw = max(ImageDraw.Draw(Image.new("L", (1, 1))).textlength(t, font=f) for t in labels)
    x0 = max(190 * S, 100 * S + lw + 28 * S)

    s = np.convolve(np.pad(series, 1, mode="edge"), np.ones(3) / 3, mode="valid")
    xs = np.linspace(0, len(s) - 1, 320)
    ys = np.interp(xs, np.arange(len(s)), s)
    px = x0 + (xs / (len(s) - 1)) * (x1 - x0)
    py = y1 - (ys - lo) / (hi - lo) * (y1 - y0)
    pts = list(zip(px, py))

    layer = Image.new("RGBA", (W * S, H * S), (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    for i in range(5):
        y = y0 + (y1 - y0) * i / 4
        d.line([(x0 - 8 * S, y), (x1, y)], fill=(150, 150, 160, 70), width=2)
        d.text((100 * S, y - 12 * S), labels[i], font=f, fill=(120, 120, 130, 255))

    mask = Image.new("L", layer.size, 0)
    ImageDraw.Draw(mask).polygon(pts + [(px[-1], y1), (px[0], y1)], fill=255)
    arr = np.zeros((H * S, W * S, 4), np.uint8)
    arr[..., 0], arr[..., 1], arr[..., 2] = color
    yy = np.arange(H * S)
    alpha = np.clip(170 * (1 - (yy - y0) / (y1 - y0)), 0, 170)
    arr[..., 3] = (alpha[:, None] * (np.array(mask) / 255.0)).astype(np.uint8)
    layer = Image.alpha_composite(layer, Image.fromarray(arr, "RGBA"))

    d = ImageDraw.Draw(layer)
    d.line(pts, fill=color + (255,), width=7 * S, joint="curve")
    ex, ey = pts[-1]
    d.ellipse((ex - 11 * S, ey - 11 * S, ex + 11 * S, ey + 11 * S), fill=(255, 255, 255, 255))
    d.ellipse((ex - 8 * S, ey - 8 * S, ex + 8 * S, ey + 8 * S), fill=color + (255,))
    return layer.resize((W, H), Image.LANCZOS)


def fit_font(d, text, path, size, max_w, min_size=28):
    while size > min_size and d.textlength(text, font=font(path, size)) > max_w:
        size -= 4
    return font(path, size)


def render_card(m, key, amount):
    cfg = ASSETS[key]
    q = m.quote(key)
    price = q["toman"] * amount
    series = chart_series(q, amount, m.usd_toman)
    change = q["chg"]
    up = change >= 0
    color = GREEN if up else RED

    bg = cover(fetch_bg(key, cfg["prompt"], cfg["bg"], W, H), W, H).filter(ImageFilter.GaussianBlur(7)).convert("RGBA")
    shadow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(shadow).rounded_rectangle((80, 90, 1124, 612), 60, fill=(0, 0, 0, 90))
    bg = Image.alpha_composite(bg, shadow.filter(ImageFilter.GaussianBlur(18)))
    card = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(card).rounded_rectangle((80, 75, 1124, 598), 60, fill=(255, 255, 255, 222),
                                           outline=(255, 255, 255, 255), width=3)
    img = Image.alpha_composite(bg, card)
    img = Image.alpha_composite(img, chart_layer(series, color))
    d = ImageDraw.Draw(img)

    title = cfg["name"] if abs(amount - 1) < 1e-9 else f"{fmt_amt(amount)} {cfg['name']}"
    d.text((1050, 160), fa(title), font=fit_font(d, fa(title), FONT_BOLD, 46, 700), fill=(40, 40, 50), anchor="rs")

    txt = fmt_toman(price)
    pf = fit_font(d, txt, FONT_BOLD, 118, 620, 50)
    d.text((1050, 270), txt, font=pf, fill=(24, 24, 32), anchor="rs")
    w = d.textlength(txt, font=pf)
    d.text((1050 - w - 18, 268), fa("تومان"), font=font(FONT_BOLD, 38), fill=(110, 110, 120), anchor="rs")

    d.rounded_rectangle((915, 287, 1052, 335), 24, fill=PILL[up])
    d.text((983, 311), f"{change:+.2f}%", font=font(FONT_BOLD, 30), fill=color, anchor="mm")
    d.ellipse((853, 287, 901, 335), fill=(210, 210, 215))
    cx, cy = 877, 311
    tri = [(cx - 11, cy + 6), (cx + 11, cy + 6), (cx, cy - 9)] if up else [(cx - 11, cy - 6), (cx + 11, cy - 6), (cx, cy + 9)]
    d.polygon(tri, fill=color)

    d.rounded_rectangle((115, 120, 213, 177), 28, fill=(200, 200, 205))
    d.text((164, 148), "IRT", font=font(FONT_BOLD, 28), fill=(120, 120, 128), anchor="mm")

    ds, ts = stamp()
    d.text((W // 2, 582), f"{ds}  |  {ts}", font=font(FONT_REG, 21), fill=(120, 120, 130), anchor="mm")
    d.text((W // 2, 636), FOOTER, font=font(FONT_BOLD, 26), fill=(255, 255, 255), anchor="mm")

    out = io.BytesIO()
    img.convert("RGB").save(out, "PNG")
    out.seek(0)
    return out


def list_rows(m):
    secs = []
    for title, keys in LIST_LAYOUT:
        rows = []
        for k in keys:
            try:
                q = m.quote(k)
            except KeyError:
                continue
            rows.append((k, q))
        if rows:
            secs.append((title, rows))
    return secs


def value_text(q, persian=True):
    if q["cur"] == "USD":
        v, unit = fmt_num(q["native"]), "دلار"
    else:
        v, unit = fmt_toman(q["toman"]), "تومان"
    return (pd(v) if persian else v), unit


def render_list(m):
    secs = list_rows(m)
    nrows = sum(len(r) for _, r in secs)
    Hh = 230 + len(secs) * 80 + nrows * 58 + 120
    bg = cover(fetch_bg("list", LIST_PROMPT, (20, 30, 60), W, Hh), W, Hh).filter(ImageFilter.GaussianBlur(9)).convert("RGBA")
    bg = Image.alpha_composite(bg, Image.new("RGBA", (W, Hh), (0, 0, 0, 90)))
    card = Image.new("RGBA", (W, Hh), (0, 0, 0, 0))
    ImageDraw.Draw(card).rounded_rectangle((50, 50, W - 50, Hh - 60), 60, fill=(255, 255, 255, 228),
                                           outline=(255, 255, 255, 255), width=3)
    img = Image.alpha_composite(bg, card)
    d = ImageDraw.Draw(img)

    d.text((W // 2, 125), fa("نرخ لحظه‌ای بازار آزاد"), font=font(FONT_BOLD, 54), fill=(30, 30, 40), anchor="mm")
    ds, ts = stamp()
    d.text((W // 2, 183), f"{ds}  |  {ts}", font=font(FONT_REG, 26), fill=(110, 110, 120), anchor="mm")

    y = 230
    for title, rows in secs:
        d.rounded_rectangle((W - 330, y + 8, W - 100, y + 62), 26, fill=(30, 64, 175))
        d.text((W - 215, y + 35), fa(title), font=font(FONT_BOLD, 30), fill=(255, 255, 255), anchor="mm")
        y += 80
        for k, q in rows:
            cfg = ASSETS[k]
            up = q["chg"] >= 0
            col = GREEN if up else RED
            d.text((W - 110, y + 29), fa(cfg["label"]), font=font(FONT_BOLD, 31), fill=(35, 35, 45), anchor="rm")
            v, unit = value_text(q, persian=False)
            d.text((110, y + 29), fa(f"{v} {unit}"), font=font(FONT_BOLD, 30), fill=(35, 35, 45), anchor="lm")
            d.rounded_rectangle((540, y + 10, 660, y + 48), 18, fill=PILL[up])
            d.text((600, y + 29), f"{q['chg']:+.2f}%", font=font(FONT_BOLD, 22), fill=col, anchor="mm")
            d.line([(100, y + 56), (W - 100, y + 56)], fill=(222, 222, 228), width=2)
            y += 58
    d.text((W // 2, Hh - 30), FOOTER, font=font(FONT_BOLD, 26), fill=(255, 255, 255), anchor="mm")

    out = io.BytesIO()
    img.convert("RGB").save(out, "PNG")
    out.seek(0)
    return out


def single_text(m, key, amount):
    q = m.quote(key)
    toman = q["toman"] * amount
    usd = toman / m.usd_toman
    ds, ts = stamp()
    return (f"✨ {fmt_amt(amount)} {ASSETS[key]['name']} :\n\n"
            f"💸 {fmt_toman(toman)} toman\n"
            f"💵 ${fmt_num(usd)} dollar\n\n"
            f"🪙 {ds} | {ts}")


def convert_text(m, amount, src, tgt):
    qs, qt = m.quote(src), m.quote(tgt)
    toman = amount * qs["toman"]
    res = toman / qt["toman"]
    ds, ts = stamp()
    return (f"🔄 تبدیل\n\n"
            f"✨ {fmt_amt(amount)} {ASSETS[src]['name']}\n"
            f"⬇️ معادل\n"
            f"✅ {fmt_num(res)} {ASSETS[tgt]['name']}\n\n"
            f"💸 {fmt_toman(toman)} toman\n\n"
            f"🪙 {ds} | {ts}"), res


def list_text(m):
    lines = ["نرخ لحظه‌ای ارزها، طلا و ارزهای دیجیتال در بازار آزاد:", ""]
    for si, (title, rows) in enumerate(list_rows(m)):
        if si:
            lines.append("➖" * 10)
        for k, q in rows:
            cfg = ASSETS[k]
            v, unit = value_text(q)
            lines.append(f"{cfg['emoji']} {cfg['label']}: {v} {unit}")
    d, t = stamp(padded=False)
    lines += ["", f"⏰ تاریخ: {pd(d)} | {pd(t)}"]
    return "\n".join(lines)


_OPS = {ast.Add: operator.add, ast.Sub: operator.sub, ast.Mult: operator.mul, ast.Div: operator.truediv}


def _ev(n):
    if isinstance(n, ast.Expression):
        return _ev(n.body)
    if isinstance(n, ast.Constant) and isinstance(n.value, (int, float)) and not isinstance(n.value, bool):
        return n.value
    if isinstance(n, ast.BinOp) and type(n.op) in _OPS:
        return _OPS[type(n.op)](_ev(n.left), _ev(n.right))
    if isinstance(n, ast.UnaryOp) and isinstance(n.op, (ast.USub, ast.UAdd)):
        v = _ev(n.operand)
        return -v if isinstance(n.op, ast.USub) else v
    raise ValueError


def calc(text):
    t = text.translate(_FA2EN).replace("×", "*").replace("÷", "/").replace("−", "-").replace("٫", ".")
    t = re.sub(r"(?<=[\d)])\s*[xX]\s*(?=[\d(])", "*", t).strip()
    if len(t) > 40 or not re.fullmatch(r"[\d\s.+\-*/()]+", t):
        return None
    if len(re.findall(r"\d+(?:\.\d+)?", t)) < 2 or not re.search(r"[+\-*/]", t):
        return None
    if re.fullmatch(r"\d{1,4}\s*([/\-])\s*\d{1,2}\s*\1\s*\d{1,4}", t):
        return None
    expr = re.sub(r"\s+", "", t)
    try:
        v = _ev(ast.parse(expr, mode="eval"))
    except ZeroDivisionError:
        return "❌ تقسیم بر صفر ممکن نیست"
    except Exception:
        return None
    if isinstance(v, float):
        v = round(v, 8)
        v = int(v) if v == int(v) else v
    out = f"{v:,}" if isinstance(v, int) else f"{v:,.8f}".rstrip("0").rstrip(".")
    return f"{expr.replace('*', '×').replace('/', '÷')}={out}"


_last = {}


async def on_text(update: Update, ctx: ContextTypes.DEFAULT_TYPE):
    msg = update.effective_message
    if not msg or not msg.text:
        return
    r = calc(msg.text)
    if r:
        await msg.reply_text(r)
        return
    req = parse(msg.text)
    if not req:
        return
    ck = (msg.chat_id, msg.text.strip())
    if time.time() - _last.get(ck, 0) < 4:
        return
    _last[ck] = time.time()
    await ctx.bot.send_chat_action(msg.chat_id, "upload_photo")
    try:
        m = await asyncio.to_thread(get_market)
        if req["list"]:
            png = await asyncio.to_thread(render_list, m)
            await msg.reply_photo(png)
            await msg.reply_text(list_text(m))
            return
        a, assets = req["amount"], req["assets"]
        if len(assets) == 1:
            png = await asyncio.to_thread(render_card, m, assets[0], a)
            await msg.reply_photo(png, caption=single_text(m, assets[0], a))
        else:
            src, tgt = assets[0], assets[1]
            text, res = convert_text(m, a, src, tgt)
            card_key, card_amt = (src, a) if src != "irt" else (tgt, res)
            png = await asyncio.to_thread(render_card, m, card_key, card_amt)
            await msg.reply_photo(png, caption=text)
    except KeyError as e:
        name = ASSETS.get(str(e).strip("'"), {}).get("name", "")
        await msg.reply_text(f"❌ نرخ {name} فعلاً در دسترس نیست")
    except Exception:
        log.exception("failed")
        await msg.reply_text("❌ مشکلی در دریافت نرخ‌ها پیش اومد، چند لحظه دیگه دوباره امتحان کن")


async def on_start(update: Update, ctx: ContextTypes.DEFAULT_TYPE):
    await update.effective_message.reply_text(
        "سلام 👋\nاسم ارز رو بنویس: دلار، یورو، تتر، طلا، سکه، بیتکوین ...\n"
        "مقدار هم میشه: 100 دلار\n"
        "تبدیل: 100 تون دلار  |  100000 تومان دلار\n"
        "لیست کامل: نرخ ارز")


def main():
    ensure_fonts()
    app = ApplicationBuilder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler(["start", "rates"], on_start))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, on_text))
    log.info("bot started")
    app.run_polling()


if __name__ == "__main__":
    main()
