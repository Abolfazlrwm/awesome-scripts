# Telegram: @Abolfazl_rwm

import asyncio, json, logging, os, uuid, time, re, math, random, io
import qrcode
from datetime import datetime, timedelta
from pathlib import Path
import aiohttp
from telethon import TelegramClient, events, functions, types
from telethon.tl.functions.account import UpdateStatusRequest, UpdateProfileRequest
from telethon.tl.functions.messages import SendReactionRequest, SetTypingRequest
from telethon.tl.functions.contacts import BlockRequest, UnblockRequest
from telethon.tl.functions.users import GetFullUserRequest
from telethon.tl.types import (
    ReactionEmoji, SendMessageTypingAction, SendMessageUploadPhotoAction,
    SendMessageUploadVideoAction, SendMessageRecordAudioAction,
    SendMessageUploadDocumentAction, SendMessageGeoLocationAction,
    SendMessageChooseStickerAction, SendMessageCancelAction
)
from telegram import (Update, InlineKeyboardButton, InlineKeyboardMarkup,
    InlineQueryResultArticle, InputTextMessageContent)
from telegram.ext import Application, CallbackQueryHandler, InlineQueryHandler, MessageHandler, ContextTypes, filters

try:
    from gtts import gTTS; HAS_TTS = True
except ImportError: HAS_TTS = False
try:
    from PIL import Image, ImageDraw, ImageFont; HAS_PIL = True
except ImportError: HAS_PIL = False
try:
    import yt_dlp; HAS_YTDLP = True
except ImportError: HAS_YTDLP = False

# ═══════════════════ تنظیمات (.env یا Environment Variables) ═══════════════════
try:
    from dotenv import load_dotenv; load_dotenv()
except ImportError:
    pass

API_ID = int(os.getenv("API_ID", "0") or 0)
API_HASH = os.getenv("API_HASH", "")
BOT_TOKEN = os.getenv("BOT_TOKEN", "")
BOT_USERNAME = os.getenv("BOT_USERNAME", "")
ADMIN_ID = int(os.getenv("ADMIN_ID", "0") or 0)
STATE_FILE = os.getenv("STATE_FILE", "state.json")
MEDIA_DIR = Path("media_cache"); MEDIA_DIR.mkdir(exist_ok=True)

logging.basicConfig(format='%(asctime)s - %(levelname)s - %(message)s', level=logging.INFO)
log = logging.getLogger(__name__)
for n in ("httpx","telethon","apscheduler","yt_dlp"): logging.getLogger(n).setLevel(logging.WARNING)

user_client = TelegramClient("user_session", API_ID, API_HASH)
bot_app = Application.builder().token(BOT_TOKEN).build()

# ═══════════════════ STATE ═══════════════════
state = {
    "save_media": True, "always_online": False, "silent": False,
    "clock_mode": False, "original_name": None, "clock_font": 1,
    "auto_reply": {"enabled": False, "text": ""},
    "secretary": {"enabled": False, "text": "الان نیستم، بعداً جواب می‌دم"},
    "friends": [], "enemies": [], "filters": [], "ready_texts": [],
    "react": {"enabled": False, "emoji": "❤"},
    "force_pv": {"enabled": False, "channel": "", "link": ""},
    "panel_messages": [], "waiting_for": None, "waiting_since": 0,
    "chat_guard": False, "auto_scene": False, "first_comment": False,
    "first_comment_targets": [],
    "lock_media": False, "lock_link": False, "lock_forward": False,
    "lock_bot": False, "lock_username": False, "lock_edit": False,
    "lock_delete": False, "current_action": None,
    "voice_replies": {}, "broadcast_list": [], "ai_key": "",
    "muted_users": [],
    "active_action": None,
    "action_peers": [],
    "action_running": False,
    "del_limit": 100,
    "dice_cheat": None,
    "text_mode": None,

    # ⏱️ زمان‌بندی پیام‌ها
    "scheduled_messages": [],

    # 📂 پوشه‌های مجازی Saved Messages
    "saved_folders": {
        "videos": [],
        "photos": [],
        "music": [],
        "voices": [],
        "gifs": [],
        "files": [],
        "texts": [],
        "links": [],
    },

    # 📊 آمار سلف
    "stats": {
        "processed": 0,
        "deleted": 0,
        "reactions": 0,
        "auto_replies": 0,
        "media_saved": 0,
        "filtered": 0,
        "muted_deleted": 0,
        "actions": 0,
    },
}

async def load_state():
    global state
    if os.path.exists(STATE_FILE):
        try:
            with open(STATE_FILE, "r", encoding="utf-8") as f:
                state.update(json.load(f))
            log.info("✅ state لود شد")
            if state.get("clock_mode") and not state.get("original_name"):
                me = await user_client.get_me()
                state["original_name"] = me.first_name or "User"
                save_state()
        except Exception as e:
            log.error(f"لود: {e}")

def save_state():
    try:
        with open(STATE_FILE, "w", encoding="utf-8") as f:
            json.dump(state, f, ensure_ascii=False, indent=2, default=str)
    except Exception as e: log.error(f"ذخیره: {e}")

def set_waiting(f):
    state["waiting_for"] = f; state["waiting_since"] = time.time(); save_state()

def clear_waiting():
    state["waiting_for"] = None; state["waiting_since"] = 0; save_state()

def is_admin(uid): return uid == ADMIN_ID

def stat_inc(key, amount=1):
    stats = state.setdefault("stats", {})
    stats[key] = int(stats.get(key, 0)) + amount
    save_state()

def stats_text():
    st = state.setdefault("stats", {})
    active = []

    checks = [
        ("ساعت زنده", state.get("clock_mode")),
        ("نگهبان چت", state.get("chat_guard")),
        ("پاسخ خودکار", state.get("auto_reply", {}).get("enabled")),
        ("منشی", state.get("secretary", {}).get("enabled")),
        ("ریکـت", state.get("react", {}).get("enabled")),
        ("ذخیره مدیا", state.get("save_media")),
        ("همیشه آنلاین", state.get("always_online")),
        ("حالت متن", bool(state.get("text_mode"))),
    ]

    for name, enabled in checks:
        if enabled:
            active.append(name)

    return (
        "📊 **آمار سلف**\n\n"
        f"📨 پیام‌های پردازش‌شده: `{st.get('processed', 0)}`\n"
        f"🗑 پیام‌های حذف‌شده: `{st.get('deleted', 0)}`\n"
        f"❤️ ریکت‌های انجام‌شده: `{st.get('reactions', 0)}`\n"
        f"🤖 پاسخ‌های خودکار: `{st.get('auto_replies', 0)}`\n"
        f"💾 رسانه‌های ذخیره‌شده: `{st.get('media_saved', 0)}`\n"
        f"🚫 پیام‌های فیلترشده: `{st.get('filtered', 0)}`\n"
        f"🔇 پیام‌های میوت‌شده: `{st.get('muted_deleted', 0)}`\n"
        f"🎬 اکشن‌های اجراشده: `{st.get('actions', 0)}`\n\n"
        f"📝 متن‌های آماده: `{len(state.get('ready_texts', []))}`\n"
        f"👥 دوستان: `{len(state.get('friends', []))}`\n"
        f"⚔️ دشمنان: `{len(state.get('enemies', []))}`\n"
        f"🚫 فیلترها: `{len(state.get('filters', []))}`\n\n"
        f"🟢 قابلیت‌های فعال: `{len(active)}`"
        + (f"\n\n⚡ {', '.join(active)}" if active else "")
    )

# ═══════════════════ Resolve ═══════════════════
def looks_like_id(t):
    t = t.strip()
    return t.lstrip("-").isdigit()

def looks_like_username(t):
    t = t.strip().lstrip("@")
    return bool(re.match(r"^[a-zA-Z][a-zA-Z0-9_]{4,31}$", t))

async def resolve_entity(txt):
    txt = txt.strip()
    if "t.me/" in txt:
        txt = txt.split("t.me/")[-1].split("/")[0].split("?")[0]
    if looks_like_id(txt):
        try: return await user_client.get_entity(int(txt))
        except Exception: pass
    username = txt.lstrip("@")
    if looks_like_username(username):
        for method in ["get_entity", "resolve_username"]:
            try:
                if method == "resolve_username":
                    r = await user_client(functions.contacts.ResolveUsernameRequest(username))
                    for u in (getattr(r, "users", []) or []):
                        return u
                else:
                    return await user_client.get_entity(username)
            except Exception:
                continue
    try: return await user_client.get_entity(txt)
    except Exception as e:
        raise Exception(f"پیدا نشد ({txt}): {str(e)[:80]}")

async def get_entity_info(ent):
    about = ""
    try:
        full = await user_client(GetFullUserRequest(ent))
        user_full = getattr(full, "full_user", None)
        about = getattr(user_full, "about", "") or ""
    except Exception as e:
        log.warning(f"spy profile info failed: {type(e).__name__}: {e}")

    return {
        "id": getattr(ent, "id", ""),
        "first_name": getattr(ent, "first_name", "") or "",
        "last_name": getattr(ent, "last_name", "") or "",
        "username": getattr(ent, "username", "") or "",
        "phone": getattr(ent, "phone", "") or "",
        "about": about,
    }

# ═══════════════════ حذف پیام‌های PV (دوطرفه) ═══════════════════
async def delete_pv_messages(peer_id, limit=100):
    """حذف پیام‌های دوطرفه از یه چت PV"""
    try:
        ent = await user_client.get_entity(
            int(peer_id) if str(peer_id).lstrip("-").isdigit() else peer_id)
        name = getattr(ent, "first_name", "") or "کاربر"
        
        msg_ids = []
        async for m in user_client.iter_messages(ent, limit=limit):
            msg_ids.append(m.id)
        
        if not msg_ids:
            await user_client.send_message(ADMIN_ID, "❌ پیامی برای حذف پیدا نشد")
            return
        
        count = 0
        # حذف دسته‌ای
        try:
            for i in range(0, len(msg_ids), 100):
                batch = msg_ids[i:i+100]
                await user_client.delete_messages(ent, batch)
                count += len(batch)
                await asyncio.sleep(0.5)
        except Exception as e:
            log.error(f"batch delete: {e}")
            # یکی‌یکی
            for mid in msg_ids:
                try:
                    await user_client.delete_messages(ent, mid)
                    count += 1
                    await asyncio.sleep(0.3)
                except: pass
        
        await user_client.send_message(
            ADMIN_ID,
            f"✅ **{count}** پیام از چت با **{name}** حذف شد",
            parse_mode="Markdown")
    except Exception as e:
        log.error(f"delete_pv: {e}")
        try: await user_client.send_message(ADMIN_ID, f"❌ خطا: {e}")
        except: pass

# ═══════════════════ دکمه‌ها ═══════════════════
def kb_btn(text, data, style="primary"):
    try:
        return InlineKeyboardButton(text, callback_data=data, style=style)
    except TypeError:
        return InlineKeyboardButton(text, callback_data=data)

def back_btn(target="m_main"):
    return InlineKeyboardMarkup([[kb_btn("🔙 بازگشت", target, "primary")]])

# ═══════════════════ API Helpers ═══════════════════
async def tr_text(text, to="fa"):
    try:
        url = "https://translate.googleapis.com/translate_a/single"
        params = {"client": "gtx", "sl": "auto", "tl": to, "dt": "t", "q": text}
        async with aiohttp.ClientSession() as s:
            async with s.get(url, params=params, timeout=15) as r:
                data = await r.json()
                return "".join(x[0] for x in data[0] if x and x[0])
    except Exception as e: return f"❌ خطا: {e}"

async def ai_reply(prompt):
    key = state.get("ai_key") or ""
    if key:
        try:
            url = "https://openrouter.ai/api/v1/chat/completions"
            h = {"Authorization": f"Bearer {key}", "Content-Type": "application/json"}
            data = {"model": "meta-llama/llama-3.1-8b-instruct:free",
                    "messages": [{"role": "user", "content": prompt}]}
            async with aiohttp.ClientSession() as s:
                async with s.post(url, headers=h, json=data, timeout=40) as r:
                    j = await r.json()
                    return j["choices"][0]["message"]["content"]
        except: pass
    try:
        url = "https://text.pollinations.ai/" + prompt.replace(" ", "%20").replace("\n","%0A")
        async with aiohttp.ClientSession() as s:
            async with s.get(url, timeout=45) as r: return await r.text()
    except Exception as e: return f"❌ خطا: {e}"

async def get_news():
    feeds = ["https://www.zoomit.ir/feed/", "https://www.tabnak.ir/fa/rss/allnews"]
    out = []
    async with aiohttp.ClientSession() as s:
        for url in feeds:
            try:
                api = f"https://api.rss2json.com/v1/api.json?rss_url={url}"
                async with s.get(api, timeout=10) as r:
                    d = await r.json()
                    for i in d.get("items", [])[:3]:
                        out.append(f"📰 {i['title']}\n🔗 {i['link']}")
            except: pass
    return out

async def get_price():
    result = {"usd": None, "gold": None}
    try:
        url = "https://api.nobitex.ir/market/stats?srcCurrency=usd&dstCurrency=rls"
        async with aiohttp.ClientSession() as s:
            async with s.get(url, timeout=10) as r:
                j = await r.json()
                stats = j.get("stats", {}).get("usd-rls", {})
                if stats: result["usd"] = int(stats.get("latest", 0)) // 10
    except: pass
    try:
        url = "https://api.tgju.org/v1/market/indicator/summary-table-data/geram18"
        async with aiohttp.ClientSession() as s:
            async with s.get(url, timeout=10) as r:
                j = await r.json()
                data = j.get("data", [])
                if data: result["gold"] = data[0].get("price")
    except: pass
    return result

async def search_music(query):
    if not HAS_YTDLP: return None
    try:
        opts = {"quiet": True, "no_warnings": True, "extract_flat": True}
        loop = asyncio.get_event_loop()
        def _run():
            with yt_dlp.YoutubeDL(opts) as ydl:
                return ydl.extract_info(f"ytsearch5:{query}", download=False)
        info = await loop.run_in_executor(None, _run)
        return info.get("entries", [])
    except Exception as e: log.error(f"music: {e}"); return None

async def download_music(url):
    if not HAS_YTDLP: return None
    try:
        ts = int(time.time())
        opts = {"format": "bestaudio/best", "outtmpl": str(MEDIA_DIR / f"m_{ts}.%(ext)s"),
                "quiet": True, "postprocessors": [{"key": "FFmpegExtractAudio",
                "preferredcodec": "mp3", "preferredquality": "192"}]}
        loop = asyncio.get_event_loop()
        await loop.run_in_executor(None, lambda: yt_dlp.YoutubeDL(opts).download([url]))
        p = MEDIA_DIR / f"m_{ts}.mp3"
        return str(p) if p.exists() else None
    except Exception as e: log.error(f"dl: {e}"); return None

async def download_media_url(url):
    try:
        out = MEDIA_DIR / f"dl_{int(time.time())}"
        async with aiohttp.ClientSession() as s:
            async with s.get(url, timeout=120) as r:
                if r.status != 200: return None
                ct = r.headers.get("Content-Type", "")
                ext = ".mp4" if "video" in ct else ".jpg" if "image" in ct else ".mp3" if "audio" in ct else ".bin"
                out = out.with_suffix(ext)
                with open(out, "wb") as f:
                    async for chunk in r.content.iter_chunked(65536): f.write(chunk)
        return str(out)
    except Exception as e: log.error(f"dlm: {e}"); return None

async def tts_text(text, lang="fa"):
    if not HAS_TTS: return None
    try:
        out = MEDIA_DIR / f"t_{int(time.time())}.mp3"
        loop = asyncio.get_event_loop()
        await loop.run_in_executor(None, lambda: gTTS(text=text, lang=lang).save(str(out)))
        return str(out)
    except Exception as e: log.error(f"tts: {e}"); return None

def get_font(size=80):
    paths = [
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
        "arial.ttf", "Arial.ttf", "C:/Windows/Fonts/arial.ttf",
        "/System/Library/Fonts/Supplemental/Arial.ttf",
    ]
    for p in paths:
        try: return ImageFont.truetype(p, size)
        except: continue
    return ImageFont.load_default()

def make_logo(text, style="neon"):
    if not HAS_PIL: return None
    try:
        W, H = 900, 450
        img = Image.new("RGB", (W, H), (10, 10, 25))
        draw = ImageDraw.Draw(img)
        font = get_font(80)
        if style == "neon":
            for i in range(50, 0, -2):
                draw.text((W//2, H//2), text, fill=(0, 255, 200), anchor="mm",
                          stroke_width=i//4, stroke_fill=(0, 100, 80), font=font)
        elif style == "fire":
            for i in range(40, 0, -2):
                c = (255, max(0, 180 - i*5), 0)
                draw.text((W//2, H//2), text, fill=c, anchor="mm",
                          stroke_width=i//3, stroke_fill=(150, 20, 0), font=font)
        elif style == "rainbow":
            colors = [(255,0,0),(255,127,0),(255,255,0),(0,255,0),(0,200,255),(75,0,130),(148,0,211)]
            for i, c in enumerate(colors):
                draw.text((W//2 + (i-3)*3, H//2), text, fill=c, anchor="mm", font=font)
        elif style == "glow":
            for r in range(20, 0, -1):
                draw.text((W//2, H//2), text, fill=(255, 0, 150), anchor="mm",
                          stroke_width=r, stroke_fill=(80, 0, 50), font=font)
        elif style == "3d":
            for i in range(10, 0, -1):
                draw.text((W//2+i, H//2+i), text, fill=(50,50,50), anchor="mm", font=font)
            draw.text((W//2, H//2), text, fill=(255, 255, 255), anchor="mm", font=font)
        else:
            draw.text((W//2, H//2), text, fill=(255,255,255), anchor="mm", font=font)
        out = MEDIA_DIR / f"logo_{int(time.time())}.png"
        img.save(str(out)); return str(out)
    except Exception as e: log.error(f"logo: {e}"); return None

def text_animation(text, mode="fade"):
    frames = []
    if mode == "fade":
        for i in range(1, 12): frames.append(text + " " + "░"*i)
    elif mode == "wave":
        for i in range(10):
            t = list(text)
            for j in range(0, len(t), 2):
                if (j+i) % 4 == 0: t[j] = t[j].upper()
            frames.append("".join(t))
    elif mode == "glitch":
        for i in range(8):
            t = list(text)
            for _ in range(3):
                idx = random.randint(0, len(t)-1)
                t[idx] = random.choice("!@#$%^&*")
            frames.append("".join(t))
    elif mode == "type":
        for i in range(1, len(text)+1): frames.append(text[:i] + "▌")
        frames.append(text)
    return frames

def to_bold(t): return "".join(chr(ord(c)+0x1D400-97) if 'a'<=c<='z' else chr(ord(c)+0x1D3EE-65) if 'A'<=c<='Z' else c for c in t)
def to_italic(t): return "".join(chr(ord(c)+0x1D434-97) if 'a'<=c<='z' else chr(ord(c)+0x1D3E2-65) if 'A'<=c<='Z' else c for c in t)
def to_script(t): return "".join(chr(ord(c)+0x1D4B0-97) if 'a'<=c<='z' else chr(ord(c)+0x1D49E-65) if 'A'<=c<='Z' else c for c in t)
def to_full(t): return "".join(chr(ord(c)+0xFEE0) if 33<=ord(c)<=126 else c for c in t)
def to_mono(t): return "".join(chr(ord(c)+0x1D670-97) if 'a'<=c<='z' else chr(ord(c)+0x1D670-65+26) if 'A'<=c<='Z' else c for c in t)
def to_double(t): return "".join(chr(ord(c)+0x1D538-65) if 'A'<=c<='Z' else chr(ord(c)+0x1D552-97) if 'a'<=c<='z' else c for c in t)

def text_modes(text):
    return (f"🔤 **حالت‌های متن:**\n\n"
            f"1️⃣ `{to_bold(text)}`\n\n2️⃣ `{to_italic(text)}`\n\n"
            f"3️⃣ `{to_script(text)}`\n\n4️⃣ `{to_full(text)}`\n\n"
            f"5️⃣ `{to_mono(text)}`\n\n6️⃣ `{to_double(text)}`")

# ═══════════════════ منوی اصلی ═══════════════════
def main_menu():
    on_icon = "🟢" if state["save_media"] else "🔴"
    save_style = "success" if state["save_media"] else "primary"
    online_style = "success" if state["always_online"] else "primary"
    clock_style = "success" if state["clock_mode"] else "primary"
    clock_icon = "🟢" if state["clock_mode"] else "🕐"
    action_icon = "🟢" if state.get("active_action") else "🎬"
    return InlineKeyboardMarkup([
        [kb_btn("📋 متن‌های آماده", "m_ready", "primary"),
         kb_btn(f"{clock_icon} ساعت زنده", "m_clock", clock_style),
         kb_btn("📸 نگهبان چت", "m_guard", "primary")],
        [kb_btn("🅰️ حالت متن", "m_textmode", "primary"),
         kb_btn(f"{action_icon} اکشن", "m_action", "primary"),
         kb_btn("🔒 قفل‌ها", "m_locks", "primary")],
        [kb_btn("🖼 لوگو", "m_logo", "primary"),
         kb_btn("📊 آمار سلف", "m_stats", "primary"),
         kb_btn("🚫 فیلتر کلمات", "m_filter", "primary")],
        [kb_btn("🏆 منشی", "m_secretary", "primary"),
         kb_btn("💛 دوست‌ودشمن", "m_fe", "primary"),
         kb_btn("🎭 ویت اجباری پیوی", "m_forcepv", "primary")],
        [kb_btn("💬 پاسخ خودکار", "m_autoreply", "primary"),
         kb_btn("📣 اسم", "m_name", "primary"),
         kb_btn("❤️ ریکت", "m_react", "primary")],
        [kb_btn("⬇️ دانلودر", "m_dl", "primary"),
         kb_btn(f"{on_icon} ذخیره عکس/ویدیو", "m_save_toggle", save_style),
         kb_btn("🗑 حذف", "m_del", "primary")],
        [kb_btn("🎭 سکوت", "m_silent", "primary"),
         kb_btn("🛎 اطلاعات", "m_info", "primary"),
         kb_btn("🚫 بلاک", "m_block", "primary")],
        [kb_btn("🔄 سین خودکار", "m_scene", "primary"),
         kb_btn("🎨 هوش مصنوعی", "m_ai", "primary"),
         kb_btn("➕ ماشین‌حساب", "m_calc", "primary")],
        [kb_btn("🌐 ترجمه", "m_tr", "primary"),
         kb_btn("✨ انیمیشن", "m_anim", "primary"),
         kb_btn("🎭 تقلب", "m_cheat", "primary")],
        [kb_btn("🎬 تبدیل متن به ویس", "m_tts", "primary"),
         kb_btn("🎬 سرچ ویس آماده", "m_vsearch", "primary"),
         kb_btn("📰 اخبار", "m_news", "primary")],
        [kb_btn("🎵 سرچ آهنگ", "m_music", "primary"),
         kb_btn("📢 تبلیغیجی", "m_ads", "primary"),
         kb_btn("🕵️ فضول پروفایل", "m_spy", "primary")],
        [kb_btn("🥇 کامنت اول", "m_first", "primary"),
         kb_btn("💰 قیمت ارز و طلا", "m_price", "primary"),
         kb_btn("🟢 همیشه آنلاین", "m_online", online_style)],
        [kb_btn("📖 درباره سلف", "m_about", "primary")],
        [kb_btn("📊 پینگ", "m_ping", "primary"),
         kb_btn("♻️ بازیابی", "m_restore", "primary"),
         kb_btn("💾 پشتیبان‌گیری", "m_backup", "primary")],
        [kb_btn("⏱️ زمان‌بندی", "m_schedule", "primary")],
        [kb_btn("📄 صفحه ۱ ➡️", "m_page2", "primary")],
        [kb_btn("❌ بستن پنل", "m_close", "primary")],
    ])

# ═══════════════════ Inline Query ═══════════════════
async def inline_query(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not is_admin(update.inline_query.from_user.id):
        await update.inline_query.answer([], cache_time=0); return
    await update.inline_query.answer([InlineQueryResultArticle(
        id=str(uuid.uuid4()),
        title="🎛 پنل کنترل سلف‌بات",
        description="۳۶ قابلیت کامل",
        input_message_content=InputTextMessageContent(
            "🎛 **پنل کنترل**\nیک گزینه انتخاب کن:", parse_mode="Markdown"),
        reply_markup=main_menu()
    )], cache_time=0, is_personal=True)

MENU_BUTTONS = {
    "m_main", "m_ready", "m_clock", "m_guard", "m_action", "m_locks", "m_logo",
    "m_filter", "m_secretary", "m_fe", "m_forcepv", "m_autoreply",
    "m_react", "m_dl", "m_del", "m_silent", "m_scene", "m_ai", "m_calc",
    "m_tr", "m_anim", "m_cheat", "m_tts", "m_vsearch", "m_news", "m_music",
    "m_ads", "m_spy", "m_first", "m_price",
    "m_rd_add", "m_rd_list", "m_rd_clr",
    "m_fl_add", "m_fl_list", "m_fl_clr",
    "m_fr_add", "m_en_add", "m_fe_list", "m_fe_clr",
    "m_sc_set", "m_ar_set", "m_fpv_set",
    "lg_neon", "lg_fire", "lg_rainbow", "lg_glow", "lg_3d", "lg_plain",
    "an_fade", "an_wave", "an_glitch", "an_type",
    "m_ads_add", "m_ads_list", "m_ads_send", "m_ads_clr",
    "m_vs_add", "m_vs_list", "m_vs_clr",
    "m_first_add", "m_first_list", "m_first_clr",
    "m_del_50", "m_del_100", "m_del_500", "m_schedule", "m_page2", "m_page3", "m_page1",
}

# ═══════════════════ دریافت فایل بازیابی تنظیمات ═══════════════════
async def restore_upload(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not update.effective_user or not is_admin(update.effective_user.id):
        return

    if state.get("waiting_for") != "restore_backup":
        return

    document = update.message.document if update.message else None
    if not document:
        return

    filename = document.file_name or ""

    if not filename.lower().endswith(".json"):
        await update.message.reply_text(
            "❌ فقط فایل JSON قابل قبول است.\n"
            "📌 فایل `state_backup.json` را ارسال کن."
        )
        return

    restore_path = "state_restore_upload.json"
    emergency_path = "state_emergency_backup.json"

    try:
        # دانلود فایل ارسالی
        tg_file = await context.bot.get_file(document.file_id)
        await tg_file.download_to_drive(restore_path)

        # اعتبارسنجی JSON
        with open(restore_path, "r", encoding="utf-8") as f:
            restored_data = json.load(f)

        if not isinstance(restored_data, dict):
            raise ValueError("فرمت بکاپ معتبر نیست")

        # قبل از بازیابی، بکاپ اضطراری از تنظیمات فعلی
        save_state()

        with open(STATE_FILE, "r", encoding="utf-8") as src:
            current_data = src.read()

        with open(emergency_path, "w", encoding="utf-8") as dst:
            dst.write(current_data)

        # جایگزینی کامل تنظیمات
        state.clear()
        state.update(restored_data)
        save_state()

        # پایان حالت انتظار
        state["waiting_for"] = None
        state["waiting_since"] = 0
        save_state()

        # پاک فایل موقت
        try:
            os.remove(restore_path)
        except:
            pass

        await update.message.reply_text(
            "✅ **بازیابی با موفقیت انجام شد.**\n\n"
            "♻️ تنظیمات سلف از فایل بکاپ برگشت.\n"
            "🛡 یک بکاپ اضطراری از تنظیمات قبلی هم ساخته شد.",
            parse_mode="Markdown"
        )

    except Exception as e:
        log.error(f"restore_upload: {e}")

        # اگر بازیابی شکست خورد، تنظیمات قبلی را برگردان
        try:
            if os.path.exists(emergency_path):
                with open(emergency_path, "r", encoding="utf-8") as f:
                    emergency_data = json.load(f)

                state.clear()
                state.update(emergency_data)
                save_state()

        except Exception as rollback_error:
            log.error(f"restore rollback: {rollback_error}")

        state["waiting_for"] = None
        state["waiting_since"] = 0
        save_state()

        try:
            os.remove(restore_path)
        except:
            pass

        await update.message.reply_text(
            "❌ **بازیابی ناموفق بود.**\n\n"
            "تنظیمات قبلی حفظ شد.\n"
            "📎 مطمئن شو فایل JSON معتبرِ بکاپ سلف رو فرستادی.",
            parse_mode="Markdown"
        )


# ═══════════════════ Callback ═══════════════════
async def cb(update: Update, context: ContextTypes.DEFAULT_TYPE):
    q = update.callback_query
    if not is_admin(q.from_user.id): await q.answer(); return
    await q.answer()
    d = q.data
    log.info(f"CB: {d}")

    if state.get("waiting_for") and d in MENU_BUTTONS:
        clear_waiting()

    try:
        # ═══ صفحه‌بندی پنل ═══
        if d == "m_page2":
            kb = InlineKeyboardMarkup([
                [
                    kb_btn("🎞️ تبدیل عکس به GIF", "m_photo_gif", "primary"),
                    kb_btn("🧩 تبدیل عکس به استیکر", "m_photo_sticker", "primary")
                ],
                [
                    kb_btn("🔮 فال حافظ", "m_hafez", "primary"),
                    kb_btn("📥 سیو کانال", "m_save_channel", "primary")
                ],
                [kb_btn("📂 پوشه‌های من", "m_saved_folders", "primary")],
                [kb_btn("⬅️ صفحه ۱", "m_page1", "primary")],
                [kb_btn("✨ صفحه ۳ ➡️", "m_page3", "primary")],
                [kb_btn("❌ بستن پنل", "m_close", "danger")]
            ])

            await q.edit_message_text(
                "📄 **صفحه ۲**\n\n"
                "🎛 قابلیت‌های بیشتر سلف در این صفحه قرار می‌گیرند.",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_page3":
            kb = InlineKeyboardMarkup([
                [
                    kb_btn("📱 ساخت QR", "m_qr", "primary")
                ],
                [
                    kb_btn("⬅️ صفحه ۲", "m_page2", "primary")
                ],
                [
                    kb_btn("❌ بستن پنل", "m_close", "danger")
                ]
            ])

            await q.edit_message_text(
                "✨ **صفحه ۳**\n\n"
                "🧰 ابزارهای کاربردی سلف",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_qr":
            kb = InlineKeyboardMarkup([
                [kb_btn("⬅️ صفحه ۳", "m_page3", "primary")]
            ])

            await q.edit_message_text(
                "📱 **ساخت QR Code**\n\n"
                "📌 **دستور:**\n"
                "qr [لینک سایت یا پست]\n\n"
                "📋 **مثال:**\n"
                "`qr https://google.com`\n"
                "`qr https://t.me/channel/123`\n\n"
                "💡 لینک را بعد از `qr` بنویس.",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_save_channel":
            kb = InlineKeyboardMarkup([
                [kb_btn("⬅️ صفحه ۲", "m_page2", "primary")]
            ])

            await q.edit_message_text(
                "📥 **سیو کانال**\n\n"
                "📌 **دستور:**\n"
                "سیو [لینک پست]\n\n"
                "📋 **مثال:**\n"
                "`سیو https://t.me/channel/123`\n\n"
                "💡 لینک پست رو بعد از «سیو» بنویس.",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_hafez":
            try:
                import urllib.request

                def get_hafez():
                    req = urllib.request.Request(
                        "https://hafez-dxle.onrender.com/fal",
                        headers={"User-Agent": "Mozilla/5.0"}
                    )
                    with urllib.request.urlopen(req, timeout=20) as r:
                        return json.loads(r.read().decode("utf-8"))

                data = await asyncio.to_thread(get_hafez)

                title = str(data.get("title", "فال حافظ"))
                interpreter = str(
                    data.get(
                        "interpreter",
                        "تعبیری برای این فال پیدا نشد."
                    )
                )

                kb = InlineKeyboardMarkup([
                    [kb_btn("🔮 فال مجدد", "m_hafez", "success")],
                    [kb_btn("⬅️ صفحه ۲", "m_page2", "primary")]
                ])

                await q.edit_message_text(
                    "🔮 فال حافظ\n\n"
                    f"📖 {title}\n\n"
                    f"📝 تعبیر:\n{interpreter}",
                    reply_markup=kb
                )

                log.info(f"✅ Hafez: {title}")

            except Exception as e:
                log.error(f"Hafez error: {e}", exc_info=True)

                kb = InlineKeyboardMarkup([
                    [kb_btn("🔄 تلاش دوباره", "m_hafez", "primary")],
                    [kb_btn("⬅️ صفحه ۲", "m_page2", "primary")]
                ])

                await q.edit_message_text(
                    "❌ دریافت فال حافظ ناموفق بود.\n\n"
                    "📡 سرویس حافظ یا اینترنت موقتاً در دسترس نیست.",
                    reply_markup=kb
                )

            return

        if d == "m_saved_folders":
            folders = state.setdefault("saved_folders", {})

            kb = InlineKeyboardMarkup([
                [
                    kb_btn(f"🎬 فیلم‌ها ({len(folders.get('videos', []))})", "sf_videos", "primary"),
                    kb_btn(f"🖼️ عکس‌ها ({len(folders.get('photos', []))})", "sf_photos", "primary"),
                ],
                [
                    kb_btn(f"🎵 موزیک ({len(folders.get('music', []))})", "sf_music", "primary"),
                    kb_btn(f"🎙️ ویس‌ها ({len(folders.get('voices', []))})", "sf_voices", "primary"),
                ],
                [
                    kb_btn(f"🎞️ GIF ({len(folders.get('gifs', []))})", "sf_gifs", "primary"),
                    kb_btn(f"📄 فایل‌ها ({len(folders.get('files', []))})", "sf_files", "primary"),
                ],
                [
                    kb_btn(f"📝 متن‌ها ({len(folders.get('texts', []))})", "sf_texts", "primary"),
                    kb_btn(f"🔗 لینک‌ها ({len(folders.get('links', []))})", "sf_links", "primary"),
                ],
                [kb_btn("⬅️ صفحه ۲", "m_page2", "primary")],
                [kb_btn("❌ بستن پنل", "m_close", "danger")]
            ])

            await q.edit_message_text(
                "📂 **پوشه‌های من**\n\n"
                "📌 پیام‌های Saved Messages به‌صورت خودکار دسته‌بندی می‌شوند.\n\n"
                "💡 پیام اصلی جابه‌جا نمی‌شود و دوباره دانلود یا آپلود نمی‌شود.",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d.startswith("sf_") and not d.startswith("sfopen_"):
            folder_key = d[3:]

            names = {
                "videos": "🎬 فیلم‌ها",
                "photos": "🖼️ عکس‌ها",
                "music": "🎵 موزیک",
                "voices": "🎙️ ویس‌ها",
                "gifs": "🎞️ GIF",
                "files": "📄 فایل‌ها",
                "texts": "📝 متن‌ها",
                "links": "🔗 لینک‌ها",
            }

            if folder_key not in names:
                await q.answer("❌ پوشه نامعتبر است.", alert=True)
                return

            folders = state.setdefault("saved_folders", {})
            ids = list(reversed(folders.get(folder_key, [])[-20:]))

            if not ids:
                await q.edit_message_text(
                    f"{names[folder_key]}\n\n"
                    "📭 این پوشه فعلاً خالی است.",
                    reply_markup=InlineKeyboardMarkup([
                        [kb_btn("⬅️ پوشه‌ها", "m_saved_folders", "primary")]
                    ])
                )
                return

            rows = []

            for i, mid in enumerate(ids, 1):
                try:
                    msg = await user_client.get_messages("me", ids=mid)

                    if not msg:
                        label = f"{i}️⃣ پیام حذف‌شده"
                    elif msg.text:
                        text_preview = " ".join(msg.text.split())
                        if len(text_preview) > 45:
                            text_preview = text_preview[:45] + "..."
                        label = f"{i}️⃣ {text_preview}"
                    elif msg.photo:
                        label = f"{i}️⃣ 🖼️ عکس"
                    elif msg.gif:
                        label = f"{i}️⃣ 🎞️ GIF"
                    elif msg.video:
                        label = f"{i}️⃣ 🎬 ویدیو"
                    elif msg.voice:
                        label = f"{i}️⃣ 🎙️ ویس"
                    elif msg.audio:
                        label = f"{i}️⃣ 🎵 موزیک"
                    elif msg.document:
                        label = f"{i}️⃣ 📄 فایل"
                    else:
                        label = f"{i}️⃣ 📎 پیام"

                except Exception:
                    label = f"{i}️⃣ 📎 پیام"

                rows.append([
                    kb_btn(label, f"sfopen_{folder_key}_{mid}", "primary")
                ])

            rows.append([
                kb_btn("⬅️ پوشه‌ها", "m_saved_folders", "primary")
            ])

            await q.edit_message_text(
                f"{names[folder_key]}\n\n"
                f"📊 تعداد: {len(folders.get(folder_key, []))}\n"
                "📋 آخرین پیام‌ها:",
                reply_markup=InlineKeyboardMarkup(rows)
            )
            return

        if d.startswith("sfopen_"):
            parts = d.split("_", 2)

            if len(parts) != 3:
                await q.answer("❌ پیام نامعتبر است.", alert=True)
                return

            try:
                message_id = int(parts[2])
                msg = await user_client.get_messages("me", ids=message_id)

                if not msg:
                    await q.answer(
                        "❌ این پیام دیگر در Saved Messages وجود ندارد.",
                        alert=True
                    )
                    return

                if msg.text:
                    preview = msg.text
                elif msg.photo:
                    preview = "🖼️ این پیام یک عکس است."
                elif msg.gif:
                    preview = "🎞️ این پیام یک GIF است."
                elif msg.video:
                    preview = "🎬 این پیام یک ویدیو است."
                elif msg.voice:
                    preview = "🎙️ این پیام یک ویس است."
                elif msg.audio:
                    preview = "🎵 این پیام یک موزیک است."
                elif msg.document:
                    preview = "📄 این پیام یک فایل است."
                else:
                    preview = "📎 پیام رسانه‌ای"

                if len(preview) > 1000:
                    preview = preview[:1000] + "..."

                await q.answer(preview, alert=True)

            except Exception as e:
                log.error(f"saved folder open: {e}", exc_info=True)
                await q.answer(
                    "❌ دسترسی به پیام ناموفق بود.",
                    alert=True
                )

            return

        if d == "m_photo_gif":
            kb = InlineKeyboardMarkup([
                [kb_btn("⬅️ صفحه ۲", "m_page2", "primary")]
            ])

            await q.edit_message_text(
                "🎞️ **تبدیل عکس به GIF**\n\n"
                "📌 **روش استفاده:**\n\n"
                "1️⃣ روی یک عکس ریپلای کن.\n"
                "2️⃣ بنویس: `گیف`\n\n"
                "📤 عکس به GIF تبدیل می‌شود و در همان چت ارسال می‌شود.\n"
                "🗑️ بعد از ارسال موفق، پیام دستور و عکس اصلی حذف می‌شوند.\n"
                "📐 نسبت تصویر حفظ می‌شود.\n\n"
                "💡 مثال:\n"
                "روی عکس ریپلای کن و بنویس:\n"
                "`گیف`",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        # ═══ زمان‌بندی ═══
        if d == "m_schedule":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ افزودن زمان‌بندی", "m_schedule_add", "success")],
                [kb_btn("📋 لیست زمان‌بندی‌ها", "m_schedule_list", "primary")],
                [kb_btn("🗑 حذف همه", "m_schedule_clear", "danger")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]
            ])

            await q.edit_message_text(
                "⏱️ **زمان‌بندی پیام**\n\n"
                "می‌تونی پیام‌ها رو برای ساعت مشخصی تنظیم کنی.\n\n"
                "📌 مقصد پیام، همان چتی است که زمان‌بندی را داخلش ثبت می‌کنی.\n\n"
                "مثال:\n"
                "`14:30 سلام`\n"
                "`21:45 شب بخیر`",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_schedule_add":
            set_waiting("schedule")

            await q.edit_message_text(
                "➕ **افزودن زمان‌بندی**\n\n"
                "فرمت را بفرست:\n"
                "`14:30 متن پیام`\n\n"
                "مثال:\n"
                "`18:45 سلام، این پیام زمان‌بندی شده است`\n\n"
                "📌 پیام در همان چتی ارسال می‌شود که ورودی را می‌فرستی.\n"
                "📌 اگر ساعت گذشته باشد، برای فردا تنظیم می‌شود.\n\n"
                "❌ لغو: `لغو`",
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        if d == "m_schedule_list":
            await q.edit_message_text(
                schedule_list_text(),
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([
                    [kb_btn("🔄 بروزرسانی", "m_schedule_list", "success")],
                    [kb_btn("🔙 بازگشت", "m_schedule", "primary")]
                ])
            )
            return

        if d == "m_schedule_clear":
            count = len(state.get("scheduled_messages", []))
            state["scheduled_messages"] = []
            save_state()

            await q.edit_message_text(
                f"🗑 **{count} زمان‌بندی حذف شد.**",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([
                    [kb_btn("🔙 زمان‌بندی", "m_schedule", "primary")]
                ])
            )
            return

        if d == "m_close":
            try: await q.delete_message()
            except: pass
            for it in list(state.get("panel_messages", [])):
                try: await user_client.delete_messages(it["chat_id"], it["message_id"])
                except: pass
            state["panel_messages"] = []; save_state(); return

        if d == "m_main":
            await q.edit_message_text("🎛 **پنل کنترل**\nیک گزینه انتخاب کن:",
                parse_mode="Markdown", reply_markup=main_menu()); return

        if d == "m_save_toggle":
            state["save_media"] = not state["save_media"]; save_state()
            await q.edit_message_text("🎛 **پنل کنترل**\nیک گزینه انتخاب کن:",
                parse_mode="Markdown", reply_markup=main_menu()); return

        # ═══ ساعت زنده ═══
        if d == "m_clock":
            await q.edit_message_text(
                "⏰ **ساعت زنده**\n\n"
                "📌 روشن کردن:\n"
                "`ساعت روشن`\n\n"
                "📌 خاموش کردن:\n"
                "`ساعت خاموش`\n\n"
                "🎨 تغییر فونت:\n"
                "`ساعت فونت 1`\n"
                "`ساعت فونت 2`\n"
                "`ساعت فونت 3`\n"
                "`ساعت فونت 4`\n"
                "`ساعت فونت 5`\n"
                "`ساعت فونت 6`",
                parse_mode="Markdown",
                reply_markup=back_btn())
            return

        if d == "m_stats":
            kb = InlineKeyboardMarkup([
                [kb_btn("🔄 بروزرسانی", "m_stats", "success")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]
            ])
            await q.edit_message_text(
                stats_text(),
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d == "m_ping":
            t0 = time.time(); await user_client.get_me()
            ms = int((time.time()-t0)*1000)
            await q.edit_message_text(
                f"📊 **پینگ**\n\n⚡ `{ms} ms`\n🟢 {'عالی' if ms<300 else 'خوب' if ms<800 else 'کند'}",
                parse_mode="Markdown", reply_markup=back_btn()); return

        if d == "m_backup":
            try:
                save_state()

                backup_path = "state_backup.json"
                with open("state.json", "r", encoding="utf-8") as src:
                    data = src.read()

                with open(backup_path, "w", encoding="utf-8") as dst:
                    dst.write(data)

                await user_client.send_file(
                    "me",
                    backup_path,
                    caption="💾 **پشتیبان تنظیمات سلف**\n\n"
                            "این فایل شامل تنظیمات فعلی سلف است.\n"
                            "⚠️ کد و اطلاعات اجرایی ss.py داخل این فایل نیست.",
                    parse_mode="Markdown"
                )

                try:
                    Path(backup_path).unlink()
                except Exception:
                    pass

                await q.answer("✅ بکاپ تنظیمات در Saved Messages ذخیره شد.", alert=True)

            except Exception as e:
                log.error(f"backup: {e}")
                await q.answer("❌ ساخت بکاپ ناموفق بود.", alert=True)

            return

        if d == "m_restore":
            state["waiting_for"] = "restore_backup"
            state["waiting_since"] = time.time()
            save_state()

            await q.edit_message_text(
                "♻️ **بازیابی تنظیمات**\n\n"
                "📎 فایل بکاپ تنظیماتت رو همینجا ارسال کن.\n\n"
                "📌 نام پیشنهادی فایل: `state_backup.json`\n"
                "⚠️ فقط فایل JSON تنظیمات سلف رو ارسال کن.\n\n"
                "❌ برای لغو: `لغو`",
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        if d == "m_info":
            me = await user_client.get_me()
            await q.edit_message_text(
                f"👤 **اطلاعات**\n\n🆔 `{me.id}`\n👤 {me.first_name or '-'} {me.last_name or ''}\n"
                f"📛 @{me.username or 'نداری'}\n📱 `{me.phone or '-'}`\n🌐 {me.lang_code or '-'}\n"
                f"💎 {'پریمیوم' if getattr(me,'premium',False) else 'عادی'}",
                parse_mode="Markdown", reply_markup=back_btn()); return

        if d == "m_about":
            await q.edit_message_text(
                "📖 **سلف‌بات 7.3**\n\n🤖 ۳۶ قابلیت\n⚙️ Telethon + PTB\n\n"
                "💡 برای پنل بنویس: `پنل`\n"
                "💡 برای راهنما بنویس: `هلپ`",
                parse_mode="Markdown", reply_markup=back_btn()); return

        # ═══ متن‌های آماده ═══
        if d == "m_ready":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ افزودن", "m_rd_add", "primary")],
                [kb_btn("📋 لیست", "m_rd_list", "primary")],
                [kb_btn("🗑 پاک همه", "m_rd_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"📋 **متن‌های آماده**\nتعداد: `{len(state['ready_texts'])}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_rd_add":
            set_waiting("ready_text")
            await q.edit_message_text("✏️ متن رو بفرست:\nلغو: `لغو`",
                parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_rd_list":
            if not state["ready_texts"]:
                await q.edit_message_text("خالیه.", reply_markup=back_btn()); return
            rows = [[kb_btn(f"📄 {t[:40]}", f"m_rd_s_{i}", "primary")]
                    for i, t in enumerate(state["ready_texts"])]
            rows.append([kb_btn("🔙 بازگشت", "m_main", "primary")])
            await q.edit_message_text("روی متن بزن:",
                reply_markup=InlineKeyboardMarkup(rows)); return
        if d.startswith("m_rd_s_"):
            i = int(d.split("_")[-1])
            if i < len(state["ready_texts"]):
                await user_client.send_message(q.from_user.id, state["ready_texts"][i])
            return
        if d == "m_rd_clr":
            state["ready_texts"] = []; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ نگهبان ═══
        if d == "m_guard":
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['chat_guard'] else '🔴 خاموش'}", "m_guard_tog",
                        "success" if state['chat_guard'] else "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"📸 **نگهبان چت**\nوضعیت: {'🟢' if state['chat_guard'] else '🔴'}",
                reply_markup=kb); return
        if d == "m_guard_tog":
            state["chat_guard"] = not state["chat_guard"]; save_state()
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['chat_guard'] else '🔴 خاموش'}", "m_guard_tog",
                        "success" if state['chat_guard'] else "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return

        if d == "m_textmode":
            mode = state.get("text_mode")
            status = f"\n🟢 فعال: **{mode}**" if mode else "\n🔴 خاموش"

            kb = InlineKeyboardMarkup([
                [kb_btn("🅱️ بولد", "m_tm_bold", "success" if mode == "بولد" else "primary"),
                 kb_btn("_کج_", "m_tm_italic", "success" if mode == "کج" else "primary")],

                [kb_btn("U̲ زیرخط", "m_tm_underline", "success" if mode == "زیرخط" else "primary"),
                 kb_btn("S̶ خط‌خورده", "m_tm_strike", "success" if mode == "خط‌خورده" else "primary")],

                [kb_btn("🅱️ کج + بولد", "m_tm_bolditalic", "success" if mode == "بولد کج" else "primary"),
                 kb_btn("`کد`", "m_tm_code", "success" if mode == "کد" else "primary")],

                [kb_btn("🔴 خاموش", "m_tm_off", "danger")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]
            ])

            await q.edit_message_text(
                "📝 **حالت متن**\n\n"
                "هر کدوم رو فعال کنی، متن‌های بعدی که می‌فرستی خودکار با همون حالت ارسال می‌شن.\n\n"
                "📌 **مثال:**\n"
                "بولد → **سلام داداش**\n"
                "کج → *سلام داداش*\n"
                "زیرخط → <u>سلام داداش</u>\n"
                "خط‌خورده → ~~سلام داداش~~\n"
                "بولد + کج → **_سلام داداش_**\n"
                "کد → `سلام داداش`"
                f"{status}",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return

        if d.startswith("m_tm_"):
            tm = d.replace("m_tm_", "")

            modes = {
                "bold": "بولد",
                "italic": "کج",
                "underline": "زیرخط",
                "strike": "خط‌خورده",
                "bolditalic": "بولد کج",
                "code": "کد",
                "off": None,
            }

            if tm not in modes:
                return

            state["text_mode"] = modes[tm]
            save_state()

            label = modes[tm]

            if label is None:
                msg = "🔴 حالت متن خاموش شد."
            else:
                msg = (
                    f"🟢 حالت متن **{label}** فعال شد.\n\n"
                    "از این به بعد متن‌هایی که می‌فرستی خودکار با همین فرمت ارسال می‌شن."
                )

            await q.edit_message_text(
                msg,
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        # ═══ اکشن ═══
        if d == "m_action":
            active = state.get("active_action")
            status = f"\n🟢 **الان فعال:** `{active}`\n" if active else ""
            kb = InlineKeyboardMarkup([
                [kb_btn("⌨️ تایپینگ", "m_act_typing", "primary"),
                 kb_btn("📷 عکس", "m_act_photo", "primary")],
                [kb_btn("🎥 ویدیو", "m_act_video", "primary"),
                 kb_btn("🎙 ویس", "m_act_voice", "primary")],
                [kb_btn("📄 فایل", "m_act_doc", "primary"),
                 kb_btn("🌍 موقعیت", "m_act_geo", "primary")],
                [kb_btn("🎨 استیکر", "m_act_sticker", "primary")],
                [kb_btn("🛑 توقف اکشن", "m_act_stop", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"🎬 **اکشن**{status}\n\n"
                "📌 **نحوه استفاده:**\n"
                "1️⃣ یکی رو انتخاب کن\n"
                "2️⃣ برو تو چت مورد نظر\n"
                "3️⃣ بنویس: `اکشن`\n"
                "4️⃣ تا `توقف اکشن` ادامه داره",
                parse_mode="Markdown", reply_markup=kb); return
        if d.startswith("m_act_"):
            act = d.replace("m_act_", "")
            if act == "stop":
                state["active_action"] = None
                state["action_peers"] = []
                state["action_running"] = False
                save_state()
                await q.edit_message_text(
                    "🛑 **اکشن متوقف شد**",
                    reply_markup=InlineKeyboardMarkup([
                        [kb_btn("🔙 بازگشت", "m_action", "primary")]]))
                return
            state["active_action"] = act
            state["action_peers"] = []
            state["action_running"] = False
            save_state()
            await q.edit_message_text(
                f"✅ **اکشن `{act}` انتخاب شد**\n\n"
                "📌 برو تو چت → بنویس: `اکشن`\n"
                "یا رو پیام کسی ریپلای بزن → `اکشن`\n\n"
                "⏹ توقف: `توقف اکشن`",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([
                    [kb_btn("🛑 توقف", "m_act_stop", "primary")],
                    [kb_btn("🔙 بازگشت", "m_action", "primary")]]))
            return

        # ═══ قفل‌ها ═══
        if d == "m_locks":
            def li(b): return "🟢" if b else "🔴"
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{li(state['lock_media'])} مدیا", "lk_media", "primary"),
                 kb_btn(f"{li(state['lock_link'])} لینک", "lk_link", "primary")],
                [kb_btn(f"{li(state['lock_forward'])} فوروارد", "lk_fwd", "primary"),
                 kb_btn(f"{li(state['lock_bot'])} ربات", "lk_bot", "primary")],
                [kb_btn(f"{li(state['lock_username'])} یوزرنیم", "lk_user", "primary"),
                 kb_btn(f"{li(state['lock_edit'])} ادیت", "lk_edit", "primary")],
                [kb_btn(f"{li(state['lock_delete'])} حذف", "lk_del", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text("🔒 **قفل‌ها**\n(فقط PV)", reply_markup=kb); return
        if d.startswith("lk_"):
            kmap = {"lk_media":"lock_media","lk_link":"lock_link","lk_fwd":"lock_forward",
                    "lk_bot":"lock_bot","lk_user":"lock_username","lk_edit":"lock_edit","lk_del":"lock_delete"}
            k = kmap.get(d)
            if k: state[k] = not state[k]; save_state()
            def li(b): return "🟢" if b else "🔴"
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{li(state['lock_media'])} مدیا", "lk_media", "primary"),
                 kb_btn(f"{li(state['lock_link'])} لینک", "lk_link", "primary")],
                [kb_btn(f"{li(state['lock_forward'])} فوروارد", "lk_fwd", "primary"),
                 kb_btn(f"{li(state['lock_bot'])} ربات", "lk_bot", "primary")],
                [kb_btn(f"{li(state['lock_username'])} یوزرنیم", "lk_user", "primary"),
                 kb_btn(f"{li(state['lock_edit'])} ادیت", "lk_edit", "primary")],
                [kb_btn(f"{li(state['lock_delete'])} حذف", "lk_del", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return

        # ═══ لوگو ═══
        if d == "m_logo":
            if not HAS_PIL:
                await q.edit_message_text("❌ Pillow نصب نیست.", reply_markup=back_btn()); return
            kb = InlineKeyboardMarkup([
                [kb_btn("🌟 نئون", "lg_neon", "primary"),
                 kb_btn("🔥 آتش", "lg_fire", "primary")],
                [kb_btn("🌈 رنگین‌کمان", "lg_rainbow", "primary"),
                 kb_btn("💗 درخشان", "lg_glow", "primary")],
                [kb_btn("🎭 سه‌بعدی", "lg_3d", "primary"),
                 kb_btn("⚪ ساده", "lg_plain", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text("🖼 **لوگو**\nاستایل:", reply_markup=kb); return
        if d.startswith("lg_"):
            style = d.replace("lg_", "")
            state["current_action"] = f"logo_{style}"
            set_waiting("logo")
            await q.edit_message_text(f"🖼 متن لوگو رو بفرست.\nلغو: `لغو`",
                reply_markup=back_btn()); return

        # ═══ فیلتر ═══
        if d == "m_filter":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ افزودن", "m_fl_add", "primary")],
                [kb_btn("📋 لیست", "m_fl_list", "primary")],
                [kb_btn("🗑 پاک همه", "m_fl_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"🚫 **فیلتر کلمات**\nتعداد: `{len(state['filters'])}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_fl_add":
            set_waiting("filter")
            await q.edit_message_text("کلمه:\nلغو: `لغو`", reply_markup=back_btn()); return
        if d == "m_fl_list":
            txt = "\n".join(f"• `{w}`" for w in state["filters"]) or "(خالی)"
            await q.edit_message_text(f"📋:\n{txt}", parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_fl_clr":
            state["filters"] = []; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ منشی ═══
        if d == "m_secretary":
            sc = state["secretary"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if sc['enabled'] else '🔴 خاموش'}", "m_sc_tog",
                        "success" if sc['enabled'] else "primary")],
                [kb_btn("✏️ متن", "m_sc_set", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"🏆 **منشی**\n`{sc['text']}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_sc_tog":
            state["secretary"]["enabled"] = not state["secretary"]["enabled"]; save_state()
            sc = state["secretary"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if sc['enabled'] else '🔴 خاموش'}", "m_sc_tog",
                        "success" if sc['enabled'] else "primary")],
                [kb_btn("✏️ متن", "m_sc_set", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return
        if d == "m_sc_set":
            set_waiting("secretary")
            await q.edit_message_text("متن منشی:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ دوست‌ودشمن ═══
        if d == "m_fe":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ دوست", "m_fr_add", "primary"),
                 kb_btn("➕ دشمن", "m_en_add", "primary")],
                [kb_btn("📋 لیست", "m_fe_list", "primary")],
                [kb_btn("🗑 پاک", "m_fe_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"💛 **دوست و دشمن**\n👥 `{len(state['friends'])}`\n⚔️ `{len(state['enemies'])}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d in ("m_fr_add","m_en_add"):
            set_waiting("friend" if d=="m_fr_add" else "enemy")
            await q.edit_message_text("🔍 آیدی/یوزرنیم/لینک:\nلغو: `لغو`",
                reply_markup=back_btn()); return
        if d == "m_fe_list":
            f = "\n".join(f"• `{u}`" for u in state["friends"]) or "(خالی)"
            e = "\n".join(f"• `{u}`" for u in state["enemies"]) or "(خالی)"
            await q.edit_message_text(f"💛:\n{f}\n\n⚔️:\n{e}",
                parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_fe_clr":
            state["friends"] = []; state["enemies"] = []; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ عضویت اجباری پیوی ═══
        if d == "m_forcepv":
            f = state.setdefault("force_pv", {"enabled": False, "channel": "", "link": ""})
            status = "🟢 روشن" if f.get("enabled") else "🔴 خاموش"
            channel = f.get("channel") or "تنظیم نشده"
            link = f.get("link") or "تنظیم نشده"

            kb = InlineKeyboardMarkup([
                [kb_btn(status, "m_fpv_tog",
                        "success" if f.get("enabled") else "primary")],
                [kb_btn("🔗 تنظیم کانال", "m_fpv_set", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]
            ])

            await q.edit_message_text(
                "🔗 **عضویت اجباری پیوی**\n\n"
                f"📢 کانال: `{channel}`\n"
                f"🔗 لینک: {link}\n"
                f"⚙️ وضعیت: {status}\n\n"
                "📌 کارکرد:\n"
                "کاربر قبل از پیام دادن به پیوی باید عضو کانال باشد.\n"
                "اگر عضو نباشد، پیامش حذف می‌شود و لینک کانال برایش ارسال می‌شود.",
                parse_mode="Markdown",
                reply_markup=kb,
                disable_web_page_preview=True
            )
            return

        if d == "m_fpv_tog":
            f = state.setdefault("force_pv", {"enabled": False, "channel": "", "link": ""})

            if not f.get("channel") or not f.get("link"):
                await q.answer("⚠️ اول کانال را تنظیم کن", show_alert=True)
                return

            f["enabled"] = not f.get("enabled", False)
            save_state()

            status = "🟢 روشن" if f["enabled"] else "🔴 خاموش"
            await q.edit_message_text(
                f"🔗 **عضویت اجباری پیوی**\n\n"
                f"⚙️ وضعیت: {status}\n"
                f"📢 کانال: `{f.get('channel')}`\n"
                f"🔗 لینک: {f.get('link')}",
                parse_mode="Markdown",
                reply_markup=InlineKeyboardMarkup([
                    [kb_btn(status, "m_fpv_tog",
                            "success" if f["enabled"] else "primary")],
                    [kb_btn("🔗 تنظیم کانال", "m_fpv_set", "primary")],
                    [kb_btn("🔙 بازگشت", "m_main", "primary")]
                ]),
                disable_web_page_preview=True
            )
            return

        if d == "m_fpv_set":
            set_waiting("force_channel")
            await q.edit_message_text(
                "🔗 **تنظیم کانال عضویت اجباری**\n\n"
                "آیدی یا لینک کانال را بفرست.\n\n"
                "مثال:\n"
                "`@Abolfazl_rwm`\n"
                "`https://t.me/Abolfazl_rwm`\n\n"
                "لغو: `لغو`",
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        # ═══ پاسخ خودکار ═══
        if d == "m_autoreply":
            ar = state["auto_reply"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if ar['enabled'] else '🔴 خاموش'}", "m_ar_tog",
                        "success" if ar['enabled'] else "primary")],
                [kb_btn("✏️ متن", "m_ar_set", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"💬 **پاسخ خودکار**\n`{ar['text'] or 'خالی'}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_ar_tog":
            state["auto_reply"]["enabled"] = not state["auto_reply"]["enabled"]; save_state()
            ar = state["auto_reply"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if ar['enabled'] else '🔴 خاموش'}", "m_ar_tog",
                        "success" if ar['enabled'] else "primary")],
                [kb_btn("✏️ متن", "m_ar_set", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return
        if d == "m_ar_set":
            set_waiting("auto_reply")
            await q.edit_message_text("متن پاسخ:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ اسم ═══
        if d == "m_name":
            me = await user_client.get_me()
            set_waiting("name")
            await q.edit_message_text(f"📣 اسم فعلی: {me.first_name or '-'}\n\nاسم جدید:\nلغو: `لغو`",
                parse_mode="Markdown", reply_markup=back_btn()); return

        # ═══ ریکت ═══
        if d == "m_react":
            r = state["react"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if r['enabled'] else '🔴 خاموش'}", "m_rx_tog",
                        "success" if r['enabled'] else "primary")],
                [kb_btn("😍 ایموجی", "m_rx_emoji", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"❤️ **ریکت**\n"
                f"ایموجی فعلی: {r['emoji']}\n\n"
                "📌 **دستور:**\n"
                "روی پیام ریپلای کن و بنویس:\n"
                "`ریکت [ایموجی]`\n\n"
                "📋 **مثال:**\n"
                "`ریکت ❤️`\n"
                "`ریکت 🔥`\n"
                "`ریکت 😂`\n"
                "`ریکت 👍`\n\n"
                "💡 ایموجی دلخواهت رو بعد از «ریکت» بنویس.",
                parse_mode="Markdown",
                reply_markup=kb
            )
            return
        if d == "m_rx_tog":
            state["react"]["enabled"] = not state["react"]["enabled"]; save_state()
            r = state["react"]
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if r['enabled'] else '🔴 خاموش'}", "m_rx_tog",
                        "success" if r['enabled'] else "primary")],
                [kb_btn("😍 ایموجی", "m_rx_emoji", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return
        if d == "m_rx_emoji":
            set_waiting("react_emoji")
            await q.edit_message_text("ایموجی:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ دانلودر ═══
        if d == "m_dl":
            set_waiting("download")
            await q.edit_message_text("⬇️ **دانلودر**\nلینک:\nلغو: `لغو`",
                reply_markup=back_btn()); return

        # ═══ حذف ═══
        if d == "m_del":
            kb = InlineKeyboardMarkup([
                [kb_btn("🗑 آخرین پیام من", "m_del_last", "primary")],
                [kb_btn("🗑 حذف ۵۰ پیام اخیر", "m_del_50", "primary")],
                [kb_btn("🗑 حذف ۱۰۰ پیام اخیر", "m_del_100", "primary")],
                [kb_btn("🗑 حذف ۵۰۰ پیام اخیر", "m_del_500", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                "🗑 **حذف پیام‌ها**\n\n"
                "💡 **دستور متنی (پیشنهادی):**\n"
                "`حذف 50` → ۵۰ پیام از چت فعلی\n"
                "`حذف 100 @user` → از چت خاص\n\n"
                "📌 حذف **دوطرفه** (هم پیام تو، هم طرف)",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_del_last":
            try:
                async for m in user_client.iter_messages(q.from_user.id, from_user="me", limit=1):
                    await m.delete(); break
                await q.answer("✅ حذف شد")
            except Exception as e: await q.answer(f"❌ {e}", show_alert=True)
            return
        if d in ("m_del_50", "m_del_100", "m_del_500"):
            limit = int(d.split("_")[-1])
            await q.answer(f"⏳ حذف {limit} پیام شروع شد", show_alert=False)
            asyncio.create_task(delete_pv_messages(q.from_user.id, limit))
            return

        # ═══ سکوت ═══
        if d == "m_silent":
            state["silent"] = not state["silent"]; save_state()
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['silent'] else '🔴 خاموش'}", "m_silent",
                        "success" if state['silent'] else "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"🎭 سکوت: {'🟢 روشن' if state['silent'] else '🔴 خاموش'}",
                reply_markup=kb); return

        # ═══ بلاک ═══
        if d == "m_block":
            await q.edit_message_text(
                "🚫 **بلاک**\n\n"
                "📌 **دستور:**\n"
                "روی پیام شخص ریپلای کن و بنویس:\n"
                "`بلاک`\n\n"
                "🔓 **رفع بلاک:**\n"
                "روی پیام همان شخص ریپلای کن و بنویس:\n"
                "`آنبلاک`\n\n"
                "💡 با ریپلای روی پیام شخص، دستور را ارسال کن.",
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        # ═══ سین خودکار ═══
        if d == "m_scene":
            state["auto_scene"] = not state["auto_scene"]; save_state()
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['auto_scene'] else '🔴 خاموش'}", "m_scene",
                        "success" if state['auto_scene'] else "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"🔄 سین خودکار: {'🟢' if state['auto_scene'] else '🔴'}",
                reply_markup=kb); return

        # ═══ AI ═══
        if d == "m_ai":
            set_waiting("ai")
            await q.edit_message_text("🎨 سؤالت:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ ماشین‌حساب ═══
        if d == "m_calc":
            set_waiting("calc")
            await q.edit_message_text("➕ مثلاً: `25*4+10`\nلغو: `لغو`",
                parse_mode="Markdown", reply_markup=back_btn()); return

        # ═══ ترجمه ═══
        if d == "m_tr":
            set_waiting("translate")
            await q.edit_message_text("🌐 متن:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ انیمیشن ═══
        if d == "m_anim":
            kb = InlineKeyboardMarkup([
                [kb_btn("🌈 محو", "an_fade", "primary"),
                 kb_btn("🌊 موج", "an_wave", "primary")],
                [kb_btn("⚡ گلیچ", "an_glitch", "primary"),
                 kb_btn("⌨️ تایپ", "an_type", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text("✨ **انیمیشن**", reply_markup=kb); return
        if d.startswith("an_"):
            state["current_action"] = d; set_waiting("anim")
            await q.edit_message_text("✨ متن رو بفرست.\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ تقلب ═══
        if d == "m_cheat":
            dice = state.get("dice_cheat")
            status = f"🟢 روشن — عدد {dice}" if dice else "🔴 خاموش"

            await q.edit_message_text(
                "🎭 **تقلب تاس**\n\n"
                f"🎲 وضعیت: {status}\n\n"
                "📌 دستورات:\n"
                "• `تاس 1` تا `تاس 6`\n"
                "• `تاس خاموش` → خاموش کردن\n\n"
                "💡 مثال: `تاس 6`",
                parse_mode="Markdown",
                reply_markup=back_btn())
            return

        # ═══ TTS ═══
        if d == "m_tts":
            if not HAS_TTS:
                await q.edit_message_text("❌ gTTS نصب نیست.", reply_markup=back_btn()); return
            set_waiting("tts")
            await q.edit_message_text("🎬 متن:\nلغو: `لغو`",
                reply_markup=back_btn()); return

        # ═══ سرچ ویس ═══
        if d == "m_vsearch":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ افزودن ویس", "m_vs_add", "primary")],
                [kb_btn("📋 لیست", "m_vs_list", "primary")],
                [kb_btn("🗑 پاک همه", "m_vs_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"🎬 **سرچ ویس آماده**\nتعداد: `{len(state.get('voice_replies',{}))}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_vs_add":
            set_waiting("vs_add")
            await q.edit_message_text("🎬 کلیدواژه:\nلغو: `لغو`", reply_markup=back_btn()); return
        if d == "m_vs_list":
            vr = state.get("voice_replies", {})
            if not vr:
                await q.edit_message_text("خالیه.", reply_markup=back_btn()); return
            txt = "\n".join(f"• `{k}`" for k in vr.keys())
            await q.edit_message_text(f"🎬:\n{txt}", parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_vs_clr":
            state["voice_replies"] = {}; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ اخبار ═══
        if d == "m_news":
            await q.edit_message_text("📰 در حال دریافت...", reply_markup=back_btn())
            news = await get_news()
            if news:
                await q.edit_message_text(f"📰 **اخبار**\n\n{chr(10).join(news[:6])[:3900]}",
                    parse_mode="Markdown", reply_markup=back_btn())
            else:
                await q.edit_message_text("❌ خطا", reply_markup=back_btn())
            return

        # ═══ سرچ آهنگ ═══
        if d == "m_music":
            if not HAS_YTDLP:
                await q.edit_message_text("❌ yt-dlp نصب نیست.", reply_markup=back_btn()); return
            set_waiting("music")
            await q.edit_message_text("🎵 اسم آهنگ:\nلغو: `لغو`", reply_markup=back_btn()); return

        # ═══ تبلیغیجی ═══
        if d == "m_ads":
            kb = InlineKeyboardMarkup([
                [kb_btn("➕ افزودن", "m_ads_add", "primary")],
                [kb_btn("📋 لیست", "m_ads_list", "primary")],
                [kb_btn("🚀 ارسال", "m_ads_send", "primary")],
                [kb_btn("🗑 پاک", "m_ads_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"📢 **تبلیغیجی**\nتعداد: `{len(state.get('broadcast_list',[]))}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_ads_add":
            set_waiting("ads_add")
            await q.edit_message_text("🔍 آیدی/یوزرنیم/لینک:\nلغو: `لغو`",
                reply_markup=back_btn()); return
        if d == "m_ads_list":
            bl = state.get("broadcast_list", [])
            if not bl:
                await q.edit_message_text("خالیه.", reply_markup=back_btn()); return
            await q.edit_message_text("📋:\n" + "\n".join(f"• `{x}`" for x in bl),
                parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_ads_send":
            set_waiting("ads_text")
            await q.edit_message_text("متن پیام:\nلغو: `لغو`", reply_markup=back_btn()); return
        if d == "m_ads_clr":
            state["broadcast_list"] = []; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ فضول ═══
        if d == "m_spy":
            await q.edit_message_text(
                "🕵️ **فضول پروفایل**\n\n"
                "📌 **روشن کردن فضول:**\n"
                "`فضول روشن`\n\n"
                "📌 **خاموش کردن فضول:**\n"
                "`فضول خاموش`\n\n"
                "📌 **بررسی پروفایل:**\n"
                "`فضول [آیدی]`\n\n"
                "💡 **مثال:**\n"
                "`فضول 123456789`\n"
                "`فضول @username`\n\n"
                "🔎 با این دستور اطلاعات قابل‌دسترسی پروفایل شخص نمایش داده می‌شود.",
                parse_mode="Markdown",
                reply_markup=back_btn()
            )
            return

        # ═══ کامنت اول ═══
        if d == "m_first":
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['first_comment'] else '🔴 خاموش'}", "m_first_tog",
                        "success" if state['first_comment'] else "primary")],
                [kb_btn("➕ افزودن کانال", "m_first_add", "primary")],
                [kb_btn("📋 لیست", "m_first_list", "primary")],
                [kb_btn("🗑 پاک همه", "m_first_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(
                f"🥇 **کامنت اول**\n"
                f"وضعیت: {'🟢 روشن' if state['first_comment'] else '🔴 خاموش'}\n"
                f"کانال‌ها: `{len(state.get('first_comment_targets', []))}`",
                parse_mode="Markdown", reply_markup=kb); return
        if d == "m_first_tog":
            state["first_comment"] = not state["first_comment"]; save_state()
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['first_comment'] else '🔴 خاموش'}", "m_first_tog",
                        "success" if state['first_comment'] else "primary")],
                [kb_btn("➕ افزودن کانال", "m_first_add", "primary")],
                [kb_btn("📋 لیست", "m_first_list", "primary")],
                [kb_btn("🗑 پاک همه", "m_first_clr", "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_reply_markup(reply_markup=kb); return
        if d == "m_first_add":
            set_waiting("first_channel")
            await q.edit_message_text("🔍 یوزرنیم کانال یا آیدی:\nلغو: `لغو`",
                reply_markup=back_btn()); return
        if d == "m_first_list":
            lst = state.get("first_comment_targets", [])
            if not lst:
                await q.edit_message_text("خالیه.", reply_markup=back_btn()); return
            await q.edit_message_text("📋:\n" + "\n".join(f"• `{x}`" for x in lst),
                parse_mode="Markdown", reply_markup=back_btn()); return
        if d == "m_first_clr":
            state["first_comment_targets"] = []; save_state()
            await q.edit_message_text("🗑 پاک شد.", reply_markup=back_btn()); return

        # ═══ قیمت ═══
        if d == "m_price":
            await q.edit_message_text("💰 در حال دریافت...", reply_markup=back_btn())
            p = await get_price()
            txt = "💰 **قیمت ارز و طلا**\n\n"
            txt += f"💵 دلار: `{p['usd']:,}` تومان\n\n" if p["usd"] else "💵 دلار: نامشخص\n\n"
            txt += f"🥇 طلا ۱۸: `{p['gold']}` تومان\n" if p["gold"] else "🥇 طلا: نامشخص\n"
            await q.edit_message_text(txt, parse_mode="Markdown", reply_markup=back_btn()); return

        # ═══ همیشه آنلاین ═══
        if d == "m_online":
            state["always_online"] = not state["always_online"]; save_state()
            kb = InlineKeyboardMarkup([
                [kb_btn(f"{'🟢 روشن' if state['always_online'] else '🔴 خاموش'}", "m_online",
                        "success" if state['always_online'] else "primary")],
                [kb_btn("🔙 بازگشت", "m_main", "primary")]])
            await q.edit_message_text(f"🟢 همیشه آنلاین: {'🟢' if state['always_online'] else '🔴'}",
                reply_markup=kb); return

    except Exception as e:
        log.error(f"cb [{d}]: {e}", exc_info=True)
        try: await q.answer(f"❌ {e}", show_alert=True)
        except: pass

# ═══════════════════ پنل ═══════════════════
@user_client.on(events.NewMessage(outgoing=True, pattern=r"^پنل$"))
async def panel_trigger(event):
    try:
        await event.delete()
        r = await user_client.inline_query(BOT_USERNAME, "")
        if not r: return
        s = await r[0].click(event.chat_id)
        if hasattr(s, "id"):
            state.setdefault("panel_messages", []).append(
                {"chat_id": event.chat_id, "message_id": s.id}); save_state()
    except Exception as e: log.error(f"panel: {e}")

# ═══════════════════ حالت دائمی متن ═══════════════════
@user_client.on(events.NewMessage(outgoing=True))
async def persistent_text_mode(event):
    mode = state.get("text_mode")

    if not mode or not event.raw_text:
        return

    txt = event.raw_text
    stripped = txt.strip()

    if not stripped:
        return

    # فرمان‌های کنترلی دوباره فرمت نشوند
    if stripped.startswith("حالت متن") or stripped in ("پنل", "لغو", "گیف"):
        return

    # مدیا و پیام‌های سرویس دست‌نخورده بمانند
    if event.message.media or getattr(event.message, "action", None):
        return

    html = (
        txt.replace("&", "&amp;")
           .replace("<", "&lt;")
           .replace(">", "&gt;")
    )

    if mode == "بولد":
        html = f"<b>{html}</b>"

    elif mode == "کج":
        html = f"<i>{html}</i>"

    elif mode == "زیرخط":
        html = f"<u>{html}</u>"

    elif mode == "خط‌خورده":
        html = f"<s>{html}</s>"

    elif mode == "بولد کج":
        html = f"<b><i>{html}</i></b>"

    elif mode == "کد":
        html = f"<code>{html}</code>"

    else:
        return

    try:
        await event.delete()
        await event.respond(html, parse_mode="html")
    except Exception as e:
        log.warning(f"text_mode: {e}")


# ═══════════════════ ورودی متنی ═══════════════════
# ═══════════════════ زمان‌بندی مستقل ═══════════════════

def schedule_next_id():
    items = state.setdefault("scheduled_messages", [])

    used = []
    for item in items:
        try:
            used.append(int(item.get("id", 0)))
        except Exception:
            pass

    return max(used, default=0) + 1


def schedule_list_text():
    items = state.setdefault("scheduled_messages", [])

    if not items:
        return (
            "⏱️ **زمان‌بندی‌ها**\n\n"
            "📭 هیچ زمان‌بندی فعالی وجود ندارد."
        )

    lines = ["⏱️ **زمان‌بندی‌ها**\n"]

    for item in sorted(items, key=lambda x: x.get("run_at", "")):
        sid = item.get("id", "?")
        run_at = item.get("run_at", "")
        text = str(item.get("text", "")).replace("\n", " ")
        target = item.get("target", "PV فعلی")

        try:
            dt = datetime.fromisoformat(run_at)
            time_text = dt.strftime("%Y-%m-%d %H:%M")
        except Exception:
            time_text = run_at

        lines.append(
            f"**#{sid}** ⏰ `{time_text}`\n"
            f"👤 `{target}`\n"
            f"💬 {text[:150]}"
        )

    return "\n\n".join(lines)


def parse_schedule_command(txt):
    """
    فرمت:
    14:30 سلام خوبی
    14:30 سلام خوبی @username
    """

    m = re.match(
        r"^(\d{1,2}):(\d{2})\s+(.+)$",
        txt.strip(),
        re.S
    )

    if not m:
        return None

    hour = int(m.group(1))
    minute = int(m.group(2))
    body = m.group(3).strip()

    if not (0 <= hour <= 23 and 0 <= minute <= 59):
        return None

    target_username = None

    # اگر آخر پیام @username باشد، آن را مقصد در نظر می‌گیریم
    target_match = re.search(
        r"(?:^|\s)(@[A-Za-z][A-Za-z0-9_]{4,31})$",
        body
    )

    if target_match:
        target_username = target_match.group(1)
        body = body[:target_match.start()].rstrip()

    if not body:
        return None

    return hour, minute, body, target_username


def create_schedule(chat_id, hour, minute, message_text, target_id, target_name):
    now = datetime.now()

    run_at = now.replace(
        hour=hour,
        minute=minute,
        second=0,
        microsecond=0
    )

    # اگر زمان امروز گذشته، فردا اجرا شود
    if run_at <= now:
        run_at += timedelta(days=1)

    item = {
        "id": schedule_next_id(),
        "chat_id": chat_id,
        "target_id": target_id,
        "target": target_name,
        "run_at": run_at.isoformat(),
        "text": message_text,
    }

    state.setdefault("scheduled_messages", []).append(item)
    save_state()

    return item


def add_schedule(chat_id, time_text, message_text):
    """
    ساخت زمان‌بندی از ورودی متنی «HH:MM متن».
    خروجی: (ok, item) در صورت موفقیت — (False, پیام خطا) در صورت شکست.
    """

    try:
        hour, minute = map(int, time_text.split(":"))
    except Exception:
        return False, "فرمت ساعت نامعتبر است"

    if not (0 <= hour <= 23 and 0 <= minute <= 59):
        return False, "ساعت باید بین 00:00 تا 23:59 باشد"

    item = create_schedule(
        chat_id, hour, minute, message_text,
        target_id=chat_id, target_name="PV فعلی",
    )
    return True, item


async def schedule_loop():
    """
    حلقه مستقل زمان‌بندی.
    هیچ ارتباطی با پنل یا waiting_for ندارد.
    """

    while True:
        try:
            items = state.setdefault("scheduled_messages", [])
            now = datetime.now()

            changed = False

            for item in list(items):
                try:
                    run_at = datetime.fromisoformat(item["run_at"])

                    if run_at > now:
                        continue

                    target_id = item.get("target_id")
                    chat_id = item.get("chat_id")
                    message_text = item.get("text", "")

                    destination = target_id or chat_id

                    if not destination or not message_text:
                        items.remove(item)
                        changed = True
                        continue

                    try:
                        await user_client.send_message(
                            destination,
                            message_text
                        )

                        log.info(
                            f"⏱️ زمان‌بندی اجرا شد: "
                            f"id={item.get('id')} "
                            f"target={destination}"
                        )

                        stat_inc("actions", 1)

                        # فقط بعد از ارسال موفق حذف شود
                        items.remove(item)
                        changed = True

                    except Exception as e:
                        # اگر ارسال شکست خورد، فعلاً حذف نشود
                        # تا در دور بعد دوباره تلاش کند.
                        log.error(
                            f"schedule send "
                            f"[{item.get('id')}]: {e}"
                        )

                except Exception as e:
                    log.error(
                        f"schedule item "
                        f"[{item.get('id')}]: {e}"
                    )

                    try:
                        items.remove(item)
                        changed = True
                    except Exception:
                        pass

            if changed:
                save_state()

        except Exception as e:
            log.error(
                f"schedule_loop: {e}",
                exc_info=True
            )

        await asyncio.sleep(5)


# ═══════════════════ دریافت مستقیم زمان‌بندی ═══════════════════

@user_client.on(events.NewMessage(outgoing=True))
async def hafez_command_handler(event):
    txt = (event.raw_text or "").strip()

    if txt != "فال":
        return

    try:
        import urllib.request

        def get_hafez():
            req = urllib.request.Request(
                "https://hafez-dxle.onrender.com/fal",
                headers={"User-Agent": "Mozilla/5.0"}
            )
            with urllib.request.urlopen(req, timeout=40) as r:
                return json.loads(r.read().decode("utf-8"))

        data = await asyncio.to_thread(get_hafez)

        title = str(data.get("title", "فال حافظ"))
        interpreter = str(
            data.get(
                "interpreter",
                "تعبیری برای این فال پیدا نشد."
            )
        )

        text = (
            "🔮 **فال حافظ**\n\n"
            f"📖 {title}\n\n"
            f"📝 **تعبیر:**\n{interpreter}"
        )

        await event.reply(text, parse_mode="Markdown")

        try:
            await event.delete()
        except Exception:
            pass

        log.info(f"🔮 Hafez command: {title}")

    except Exception as e:
        log.error(f"Hafez command error: {e}", exc_info=True)

        try:
            await event.reply(
                "❌ دریافت فال حافظ ناموفق بود.\n\n"
                "📡 سرویس حافظ یا اینترنت موقتاً در دسترس نیست."
            )
        except Exception:
            pass

    raise events.StopPropagation


@user_client.on(events.NewMessage(outgoing=True))
async def save_channel_command_handler(event):
    txt = (event.raw_text or "").strip()

    if not txt.startswith("سیو "):
        return

    link = txt[4:].strip()

    # فقط لینک‌های مستقیم پست تلگرام
    m = re.match(
        r"^https?://t\.me/(?:s/)?([A-Za-z0-9_]+)/(\d+)(?:\?.*)?$",
        link
    )
    mc = re.match(
        r"^https?://t\.me/c/(\d+)/(\d+)(?:\?.*)?$",
        link
    )

    if not m and not mc:
        try:
            await event.reply(
                "❌ لینک پست معتبر نیست.\n\n"
                "مثال:\n"
                "`سیو https://t.me/channel/123`"
            )
        except Exception:
            pass
        raise events.StopPropagation

    file_path = None

    try:
        if m:
            username = m.group(1)
            msg_id = int(m.group(2))
            message = await user_client.get_messages(
                username,
                ids=msg_id
            )
        else:
            channel_id = int(mc.group(1))
            msg_id = int(mc.group(2))

            # لینک‌های t.me/c مربوط به کانال‌های خصوصی/داخلی هستند
            peer = int("-100" + str(channel_id))
            message = await user_client.get_messages(
                peer,
                ids=msg_id
            )

        if not message:
            await event.reply(
                "❌ پست پیدا نشد یا این پیام برای حسابت قابل دسترسی نیست."
            )
            raise events.StopPropagation

        # اگر پیام مدیای قابل دانلود دارد
        if message.media:
            file_path = await message.download_media(
                file=str(MEDIA_DIR / f"saved_{event.id}")
            )

            if not file_path:
                await event.reply(
                    "❌ این محتوا قابل دانلود نیست.\n\n"
                    "ممکن است کانال ذخیره‌سازی محتوا را محدود کرده باشد."
                )
                raise events.StopPropagation

            caption = message.text or ""

            await user_client.send_file(
                "me",
                file_path,
                caption=caption
            )

            try:
                os.remove(file_path)
            except Exception:
                pass

        elif message.text:
            await user_client.send_message(
                "me",
                message.text
            )

        else:
            await event.reply("❌ محتوای قابل ذخیره‌ای در این پست پیدا نشد.")
            raise events.StopPropagation

        try:
            await event.delete()
        except Exception:
            pass

        log.info(f"📥 Save channel: {link}")

    except Exception as e:
        log.error(f"save_channel: {e}", exc_info=True)

        if file_path:
            try:
                os.remove(file_path)
            except Exception:
                pass

        try:
            await event.reply(
                "❌ ذخیره پست ناموفق بود.\n\n"
                "ممکن است لینک اشتباه باشد، پست حذف شده باشد، "
                "یا محتوای آن برای حساب تو قابل دریافت نباشد."
            )
        except Exception:
            pass

    raise events.StopPropagation


@user_client.on(events.NewMessage(outgoing=True))
async def qr_command_handler(event):
    txt = (event.raw_text or "").strip()

    if not txt.lower().startswith("qr "):
        return

    link = txt[3:].strip()

    if not link:
        return

    if not re.match(r"^https?://\S+$", link, re.IGNORECASE):
        try:
            await event.reply(
                "❌ لینک معتبر نیست.\n\n"
                "📌 مثال:\n"
                "`qr https://google.com`"
            )
        except Exception:
            pass
        raise events.StopPropagation

    qr_path = MEDIA_DIR / f"qr_{event.id}.png"

    try:
        img = qrcode.make(link)
        img.save(str(qr_path))

        await user_client.send_file(
            event.chat_id,
            str(qr_path),
            caption="📱 QR Code"
        )

        try:
            await event.delete()
        except Exception:
            pass

        log.info(f"📱 QR generated: {link}")

    except Exception as e:
        log.error(f"QR error: {e}", exc_info=True)

        try:
            await event.reply("❌ ساخت QR ناموفق بود.")
        except Exception:
            pass

    finally:
        try:
            os.remove(qr_path)
        except Exception:
            pass

    raise events.StopPropagation


@user_client.on(events.NewMessage(outgoing=True))
async def schedule_command_handler(event):
    txt = (event.raw_text or "").strip()

    if not txt:
        return

    # ───────────── لیست ─────────────
    if txt == "زمان‌بندی‌ها":
        try:
            await event.reply(
                schedule_list_text(),
                parse_mode="Markdown"
            )
        except Exception as e:
            log.error(f"schedule list: {e}")

        raise events.StopPropagation

    # ───────────── حذف همه ─────────────
    if txt == "حذف همه زمان‌بندی‌ها":
        count = len(state.get("scheduled_messages", []))

        state["scheduled_messages"] = []
        save_state()

        try:
            await user_client.send_message(
                "me",
                f"🗑️ **{count} زمان‌بندی حذف شد.**"
            )
        except Exception as e:
            log.error(f"schedule saved delete: {e}")

        try:
            await event.delete()
        except Exception:
            pass

        raise events.StopPropagation

    # ───────────── حذف تکی ─────────────
    if txt.startswith("حذف زمان‌بندی "):
        num_txt = txt[len("حذف زمان‌بندی "):].strip()

        if num_txt.isdigit():
            sid = int(num_txt)
            items = state.get("scheduled_messages", [])
            found = None

            for item in items:
                try:
                    if int(item.get("id", -1)) == sid:
                        found = item
                        break
                except Exception:
                    pass

            if found:
                items.remove(found)
                save_state()

                try:
                    await user_client.send_message(
                        "me",
                        f"🗑️ زمان‌بندی **#{sid}** حذف شد."
                    )
                except Exception as e:
                    log.error(f"schedule delete saved: {e}")

            try:
                await event.delete()
            except Exception:
                pass

        raise events.StopPropagation

    # ───────────── تشخیص دستور ساعت ─────────────
    parsed = parse_schedule_command(txt)

    if not parsed:
        return

    hour, minute, message_text, username = parsed

    target_id = None
    target_name = "PV فعلی"

    try:
        # اگر username داده شده، همان شخص مقصد است
        if username:
            entity = await resolve_entity(username)

            target_id = entity.id

            first = getattr(entity, "first_name", "") or ""
            last = getattr(entity, "last_name", "") or ""
            name = f"{first} {last}".strip()

            target_name = (
                f"@{getattr(entity, 'username', '')}"
                if getattr(entity, "username", None)
                else name or str(entity.id)
            )

        else:
            # بدون username فقط همان PV فعلی
            if not event.is_private:
                return

            target_id = event.chat_id
            target_name = "PV فعلی"

        # ثبت زمان‌بندی
        item = create_schedule(
            chat_id=event.chat_id,
            hour=hour,
            minute=minute,
            message_text=message_text,
            target_id=target_id,
            target_name=target_name
        )

        # اول فرمان را از چت پاک کن
        try:
            await event.delete()
        except Exception as e:
            log.warning(f"schedule command delete: {e}")

        # تأیید فقط در Saved Messages
        run_at = datetime.fromisoformat(item["run_at"])

        try:
            await user_client.send_message(
                "me",
                "⏱️ **زمان‌بندی ثبت شد**\n\n"
                f"🆔 شماره: `{item['id']}`\n"
                f"⏰ زمان: `{run_at.strftime('%Y-%m-%d %H:%M')}`\n"
                f"👤 مقصد: `{target_name}`\n"
                f"💬 پیام: {message_text}"
            )
        except Exception as e:
            log.error(f"schedule confirmation: {e}")

    except Exception as e:
        log.error(
            f"schedule command: {e}",
            exc_info=True
        )

        # خطا فقط به Saved Messages برود
        try:
            await user_client.send_message(
                "me",
                f"❌ **ثبت زمان‌بندی ناموفق بود**\n\n`{str(e)[:300]}`"
            )
        except Exception:
            pass

    raise events.StopPropagation



# ═══════════════════ تبدیل عکس به استیکر ═══════════════════

@user_client.on(events.NewMessage(outgoing=True))
async def photo_to_sticker_handler(event):
    txt = (event.raw_text or "").strip()

    if txt != "استیکر":
        return

    log.info(f"🧩 STICKER HANDLER: دستور استیکر دریافت شد | chat={event.chat_id} | msg={event.id}")

    try:
        reply_msg = await event.get_reply_message()

        if not reply_msg or not reply_msg.photo:
            await event.reply(
                "❌ باید روی یک عکس ریپلای کنی و بنویسی `استیکر`.",
                parse_mode="Markdown"
            )
            raise events.StopPropagation

        if not HAS_PIL:
            await event.reply("❌ Pillow روی سرور نصب نیست.")
            raise events.StopPropagation

        source_path = None
        sticker_path = MEDIA_DIR / (
            f"sticker_{event.chat_id}_{event.id}_{time.time_ns()}.webp"
        )

        try:
            source_path = await user_client.download_media(
                reply_msg,
                file=str(MEDIA_DIR)
            )

            if not source_path:
                await event.reply("❌ دریافت عکس ناموفق بود.")
                raise events.StopPropagation

            with Image.open(source_path) as img:
                img.load()
                img = img.convert("RGBA")

                max_size = 512
                ratio = min(
                    max_size / img.width,
                    max_size / img.height,
                    1
                )

                new_size = (
                    max(1, int(img.width * ratio)),
                    max(1, int(img.height * ratio))
                )

                if new_size != img.size:
                    img = img.resize(
                        new_size,
                        Image.Resampling.LANCZOS
                    )

                img.save(
                    sticker_path,
                    format="WEBP",
                    quality=90,
                    method=6
                )

            await user_client.send_file(
                event.chat_id,
                str(sticker_path),
                force_document=False,
                mime_type="image/webp"
            )

            try:
                await reply_msg.delete()
            except Exception as e:
                log.warning(f"sticker original delete: {e}")

            try:
                await event.delete()
            except Exception as e:
                log.warning(f"sticker command delete: {e}")

            log.info("✅ عکس با موفقیت به استیکر تبدیل شد")

        finally:
            if source_path:
                try:
                    os.remove(source_path)
                except Exception:
                    pass

            try:
                os.remove(sticker_path)
            except Exception:
                pass

    except events.StopPropagation:
        raise

    except Exception as e:
        log.error(f"photo_to_sticker: {e}", exc_info=True)
        try:
            await event.reply("❌ تبدیل عکس به استیکر ناموفق بود.")
        except Exception:
            pass

    raise events.StopPropagation


# ═══════════════════ تبدیل عکس به GIF ═══════════════════

@user_client.on(events.NewMessage(outgoing=True))
async def photo_to_gif_handler(event):
    txt = (event.raw_text or "").strip()

    if txt != "گیف":
        return

    log.info(f"🎞️ GIF HANDLER: دستور گیف دریافت شد | chat={event.chat_id} | msg={event.id}")

    log.info(f"🎞️ GIF HANDLER: دستور گیف دریافت شد | chat={event.chat_id} | msg={event.id}")

    try:
        reply_msg = await event.get_reply_message()

        log.info(
            f"🎞️ GIF HANDLER: reply={getattr(reply_msg, 'id', None)} "
            f"photo={bool(reply_msg and reply_msg.photo)}"
        )

        log.info(
            f"🎞️ GIF HANDLER: reply={getattr(reply_msg, 'id', None)} "
            f"photo={bool(reply_msg and reply_msg.photo)}"
        )

        if not reply_msg or not reply_msg.photo:
            await event.reply(
                "❌ باید روی یک عکس ریپلای کنی و بنویسی `گیف`.",
                parse_mode="Markdown"
            )
            raise events.StopPropagation

        if not HAS_PIL:
            await event.reply("❌ Pillow روی سرور نصب نیست.")
            raise events.StopPropagation

        source_path = None
        gif_path = MEDIA_DIR / (
            f"gif_{event.chat_id}_{event.id}_{time.time_ns()}.gif"
        )

        try:
            # دانلود عکس اصلی
            source_path = await user_client.download_media(
                reply_msg,
                file=str(MEDIA_DIR)
            )

            if not source_path:
                await event.reply("❌ دریافت عکس ناموفق بود.")
                raise events.StopPropagation

            # تبدیل به GIF
            with Image.open(source_path) as img:
                img.load()

                # حفظ نسبت تصویر
                img = img.convert("RGB")

                # فقط برای کنترل حجم، عکس‌های خیلی بزرگ کوچک می‌شوند
                max_size = 1280

                if max(img.size) > max_size:
                    ratio = max_size / max(img.size)

                    new_size = (
                        max(1, int(img.width * ratio)),
                        max(1, int(img.height * ratio))
                    )

                    img = img.resize(
                        new_size,
                        Image.Resampling.LANCZOS
                    )

                frame1 = img.copy()
                frame2 = img.copy()

                frame1.save(
                    gif_path,
                    format="GIF",
                    save_all=True,
                    append_images=[frame2],
                    duration=500,
                    loop=0,
                    optimize=True
                )

            # ارسال GIF در همان چت
            await user_client.send_file(
                event.chat_id,
                str(gif_path),
                force_document=False
            )

            # بعد از ارسال موفق، اصل عکس حذف شود
            try:
                await reply_msg.delete()
            except Exception as e:
                log.warning(f"gif original delete: {e}")

            # و دستور گیف هم حذف شود
            try:
                await event.delete()
            except Exception as e:
                log.warning(f"gif command delete: {e}")

            log.info("✅ عکس با موفقیت به GIF تبدیل شد")

        finally:
            if source_path:
                try:
                    os.remove(source_path)
                except Exception:
                    pass

            try:
                os.remove(gif_path)
            except Exception:
                pass

    except events.StopPropagation:
        raise

    except Exception as e:
        log.error(f"photo_to_gif: {e}", exc_info=True)

        try:
            await event.reply(
                "❌ تبدیل عکس به GIF ناموفق بود.",
                parse_mode="Markdown"
            )
        except Exception:
            pass

    raise events.StopPropagation


# ═══════════════════ ورودی متنی (waiting) ═══════════════════

@user_client.on(events.NewMessage(outgoing=True))
async def text_in(event):

    txt = (event.raw_text or "").strip()
    if txt == "پنل": return
    lower_txt = txt.lower()

    # ═══════════════════ دستورات زمان‌بندی ═══════════════════

    if lower_txt == "زمان‌بندی‌ها":
        await event.reply(
            schedule_list_text(),
            parse_mode="Markdown"
        )
        return

    if lower_txt == "حذف همه زمان‌بندی‌ها":
        count = len(state.get("scheduled_messages", []))
        state["scheduled_messages"] = []
        save_state()

        await event.reply(
            f"🗑 **{count} زمان‌بندی حذف شد.**",
            parse_mode="Markdown"
        )
        return

    if lower_txt.startswith("حذف زمان‌بندی "):
        num_txt = txt[len("حذف زمان‌بندی "):].strip()

        if not num_txt.isdigit():
            await event.reply(
                "❌ شماره زمان‌بندی نامعتبره.\n\n"
                "مثال: `حذف زمان‌بندی 2`",
                parse_mode="Markdown"
            )
            return

        sid = int(num_txt)
        items = state.get("scheduled_messages", [])
        found = None

        for item in items:
            if int(item.get("id", -1)) == sid:
                found = item
                break

        if not found:
            await event.reply("❌ چنین زمان‌بندی‌ای پیدا نشد.")
            return

        items.remove(found)
        save_state()

        await event.reply(
            f"🗑 زمان‌بندی `{sid}` حذف شد.",
            parse_mode="Markdown"
        )
        return

    if lower_txt == "زمان‌بندی":
        set_waiting("schedule")
        await event.reply(
            "⏱️ **زمان‌بندی پیام**\n\n"
            "فرمت:\n"
            "`14:30 متن پیام`\n\n"
            "مثال:\n"
            "`18:45 سلام، این یک پیام زمان‌بندی‌شده است`\n\n"
            "📌 پیام در همین چتی که دستور را می‌فرستی ارسال خواهد شد.\n"
            "📌 اگر ساعت امروز گذشته باشد، برای فردا تنظیم می‌شود.\n\n"
            "❌ لغو: `لغو`",
            parse_mode="Markdown"
        )
        return


    # ═══════════════════ دستورات فضول پروفایل ═══════════════════

    if lower_txt == "فضول روشن":
        state["spy_enabled"] = True
        save_state()
        await event.reply("🟢 **فضول پروفایل روشن شد**\n\nحالا می‌تونی بنویسی:\n`فضول [آیدی]`",
                          parse_mode="Markdown")
        return

    if lower_txt == "فضول خاموش":
        state["spy_enabled"] = False
        save_state()
        await event.reply("🔴 **فضول پروفایل خاموش شد**")
        return

    if lower_txt.startswith("فضول "):
        target = txt[6:].strip()

        if not state.get("spy_enabled", False):
            await event.reply("🔴 فضول خاموشه.\n\nبرای روشن کردن:\n`فضول روشن`",
                              parse_mode="Markdown")
            return

        if not target:
            await event.reply("❌ آیدی یا یوزرنیم وارد نشده.")
            return

        if not (looks_like_id(target) or looks_like_username(target) or "t.me/" in target):
            await event.reply("❌ آیدی یا یوزرنیم معتبر نیست.")
            return

        try:
            ent = await resolve_entity(target)
        except Exception as e:
            await event.reply(f"❌ پیدا نشد: {e}")
            return

        try:
            info = await get_entity_info(ent)

            name = f"{info['first_name']} {info['last_name']}".strip() or "ندارد"
            username = f"@{info['username']}" if info["username"] else "ندارد"

            txt_out = (
                "🕵️ **اطلاعات پروفایل**\n\n"
                f"👤 نام: {name}\n"
                f"🆔 آیدی: `{info['id']}`\n"
                f"📛 یوزرنیم: {username}\n"
                f"📱 شماره: `{info['phone'] or 'مخفی'}`\n"
            )

            if info["about"]:
                txt_out += f"📝 بیو: {info['about'][:200]}\n"

            await event.reply(txt_out, parse_mode="Markdown")

        except Exception as e:
            await event.reply(f"❌ خطا در دریافت پروفایل: {e}")

        return

    # ═══════════════════ دستورات ساعت زنده ═══════════════════
    if lower_txt == "ساعت روشن":
        if state.get("clock_mode"):
            await event.reply("🟢 ساعت زنده از قبل روشنه.")
            return

        me = await user_client.get_me()
        if not state.get("original_name"):
            state["original_name"] = me.first_name or "User"

        state["clock_mode"] = True
        state.setdefault("clock_font", 1)
        save_state()

        await event.reply(
            "🟢 **ساعت زنده روشن شد**\n\n"
            f"🎨 فونت فعلی: `{state.get('clock_font', 1)}`\n"
            "🔄 اسم پروفایل هر دقیقه آپدیت می‌شه",
            parse_mode="Markdown"
        )
        return

    if lower_txt == "ساعت خاموش":
        if not state.get("clock_mode"):
            await event.reply("🔴 ساعت زنده از قبل خاموشه.")
            return

        old_name = state.get("original_name")

        if old_name:
            try:
                await user_client(UpdateProfileRequest(first_name=old_name))
            except Exception as e:
                log.error(f"clock off: {e}")

        state["clock_mode"] = False
        state["original_name"] = None
        save_state()

        await event.reply(
            f"🔴 **ساعت زنده خاموش شد**\n\n"
            f"✅ اسم برگشت به: `{old_name or 'نام قبلی'}`",
            parse_mode="Markdown"
        )
        return

    if lower_txt.startswith("ساعت فونت "):
        font_txt = txt[len("ساعت فونت "):].strip()

        if not font_txt.isdigit() or int(font_txt) not in range(1, 7):
            await event.reply(
                "❌ شماره فونت نامعتبره.\n\n"
                "فونت‌های موجود: `1` تا `6`",
                parse_mode="Markdown"
            )
            return

        font = int(font_txt)
        state["clock_font"] = font
        save_state()

        # اگر ساعت روشنه، همین الان فونت جدید اعمال بشه
        if state.get("clock_mode"):
            current = datetime.now().strftime("%H:%M")
            styled_time = format_clock_time(current)
            base_name = state.get("original_name") or "User"
            new_name = f"{base_name} {styled_time}"
            try:
                await user_client(UpdateProfileRequest(
                    first_name=new_name
                ))
            except Exception as e:
                log.error(f"clock font update: {e}")

        await event.reply(
            f"🎨 **فونت ساعت روی {font} تنظیم شد**\n\n"
            f"⏰ پیش‌نمایش: `{format_clock_time('12:45')}`",
            parse_mode="Markdown"
        )
        return

    w = state.get("waiting_for")
    if not w: return
    if time.time() - state.get("waiting_since", 0) > 180: clear_waiting(); return

    if txt == "لغو":
        clear_waiting()
        try: await event.reply("❌ لغو شد")
        except: pass
        await asyncio.sleep(2)
        try: await event.delete()
        except: pass
        return

    try:
        if w == "schedule":
            parts = txt.split(maxsplit=1)

            if len(parts) != 2 or not re.fullmatch(r"\d{1,2}:\d{2}", parts[0]):
                await event.reply(
                    "❌ فرمت اشتباهه.\n\n"
                    "درست:\n"
                    "`14:30 متن پیام`",
                    parse_mode="Markdown"
                )
                return

            time_text = parts[0]
            message_text = parts[1].strip()

            if not message_text:
                await event.reply("❌ متن پیام خالیه.")
                return

            ok, result = add_schedule(
                event.chat_id,
                time_text,
                message_text
            )

            if not ok:
                await event.reply(
                    f"❌ {result}",
                    parse_mode="Markdown"
                )
                return

            clear_waiting()

            item = result
            run_at = datetime.fromisoformat(item["run_at"])

            await event.reply(
                "✅ **زمان‌بندی ثبت شد**\n\n"
                f"🆔 شماره: `{item['id']}`\n"
                f"⏰ زمان: `{run_at.strftime('%Y-%m-%d %H:%M')}`\n"
                f"💬 متن: `{message_text[:200]}`",
                parse_mode="Markdown"
            )

        if w == "auto_reply":
            state["auto_reply"]["text"] = txt; state["auto_reply"]["enabled"] = True
            clear_waiting(); await event.reply("✅ ذخیره شد")
        elif w == "secretary":
            state["secretary"]["text"] = txt; state["secretary"]["enabled"] = True
            clear_waiting(); await event.reply("✅ منشی ست شد")
        elif w == "force_channel":
            try:
                raw = txt.strip()

                if raw.startswith("https://t.me/") or raw.startswith("http://t.me/"):
                    channel_ref = raw
                else:
                    channel_ref = raw.lstrip("@")

                if not channel_ref:
                    await event.reply("❌ آیدی یا لینک کانال را وارد کن")
                    return

                ent = await resolve_entity(channel_ref)

                if not ent:
                    await event.reply("❌ کانال پیدا نشد")
                    return

                username = getattr(ent, "username", None)

                if username:
                    link = f"https://t.me/{username}"
                    channel = f"@{username}"
                elif channel_ref.startswith(("https://t.me/", "http://t.me/")):
                    link = channel_ref
                    channel = str(getattr(ent, "id", channel_ref))
                else:
                    await event.reply(
                        "❌ این کانال لینک عمومی ندارد.\n"
                        "برای این نسخه یک کانال دارای @username وارد کن.\n\n"
                        "مثال: `@Abolfazl_rwm`"
                    )
                    return

                state["force_pv"] = {
                    "enabled": False,
                    "channel": channel,
                    "link": link
                }
                save_state()
                clear_waiting()

                await event.reply(
                    "✅ کانال تنظیم شد\n\n"
                    f"📢 کانال: `{channel}`\n"
                    f"🔗 لینک: {link}\n\n"
                    "حالا از پنل، گزینه عضویت اجباری را روشن کن.",
                    parse_mode="Markdown"
                )

            except Exception as e:
                log.error(f"force_channel: {e}")
                await event.reply(f"❌ خطا در تنظیم کانال: {e}")
        elif w == "name":
            await user_client(UpdateProfileRequest(first_name=txt))
            clear_waiting(); await event.reply(f"✅ اسم: {txt}")
        elif w == "filter":
            state["filters"].append(txt.lower()); save_state()
            clear_waiting(); await event.reply("✅ اضافه شد")
        elif w == "ready_text":
            state["ready_texts"].append(txt); save_state()
            clear_waiting(); await event.reply("✅ متن ذخیره شد")
        elif w == "react_emoji":
            state["react"]["emoji"] = txt; state["react"]["enabled"] = True
            clear_waiting(); await event.reply(f"✅ {txt}")
        elif w in ("friend","enemy"):
            if not (looks_like_id(txt) or looks_like_username(txt) or "t.me/" in txt):
                await event.reply("❌ ورودی معتبر نیست"); return
            try: ent = await resolve_entity(txt)
            except Exception as e: await event.reply(f"❌ پیدا نشد: {e}"); return
            uid = ent.id
            if w == "friend":
                if uid not in state["friends"]: state["friends"].append(uid)
            else:
                if uid not in state["enemies"]: state["enemies"].append(uid)
            save_state(); clear_waiting()
            nm = getattr(ent, "first_name", "") or ""
            await event.reply(f"✅ {nm} `{uid}`", parse_mode="Markdown")
        elif w == "calc":
            try:
                safe = re.sub(r"[^0-9+\-*/().%\s]", "", txt).replace("^","**")
                r = eval(safe, {"__builtins__":{}, "sqrt":math.sqrt, "sin":math.sin,
                    "cos":math.cos, "tan":math.tan, "log":math.log,
                    "pi":math.pi, "e":math.e, "abs":abs, "round":round}, {})
                await event.reply(f"🧮 `{safe} = {r}`", parse_mode="Markdown")
            except Exception as e: await event.reply(f"❌ {e}")
            clear_waiting()
        elif w == "translate":
            clear_waiting(); await event.reply("🌐 ترجمه...")
            res = await tr_text(txt, "fa"); await event.reply(f"🇮🇷 {res}")
        elif w == "ai":
            clear_waiting(); await event.reply("🎨 فکر...")
            res = await ai_reply(txt); await event.reply(res[:4000])
        elif w == "tts":
            clear_waiting(); await event.reply("🎬 تبدیل...")
            p = await tts_text(txt)
            if p:
                await user_client.send_file(event.chat_id, p)
                try: os.remove(p)
                except: pass
            else: await event.reply("❌ خطا")
        elif w == "logo":
            clear_waiting()
            style = (state.get("current_action") or "logo_neon").replace("logo_", "")
            p = make_logo(txt, style)
            if p:
                await user_client.send_file(event.chat_id, p)
                try: os.remove(p)
                except: pass
            else: await event.reply("❌ خطا")
        elif w == "textmode":
            clear_waiting()
            try: await event.reply(text_modes(txt))
            except Exception as e: await event.reply(f"❌ {e}")
        elif w == "anim":
            clear_waiting()
            mode = (state.get("current_action") or "an_fade").replace("an_","")
            frames = text_animation(txt, mode)
            msg = await event.reply(frames[0])
            for fr in frames[1:]:
                await asyncio.sleep(0.4)
                try: await msg.edit(fr)
                except: pass
        elif w == "music":
            clear_waiting(); await event.reply(f"🎵 سرچ: {txt}")
            results = await search_music(txt)
            if not results: await event.reply("❌ نتیجه نیومد"); return
            lines = [f"{i}. {e.get('title','?')}\n`{e.get('url') or 'https://youtu.be/'+e.get('id','')}`"
                     for i, e in enumerate(results[:5], 1)]
            await event.reply("🎵 **نتایج:**\n\n" + "\n\n".join(lines), parse_mode="Markdown")
        elif w == "download":
            clear_waiting(); await event.reply("⬇️ دانلود...")
            p = await download_music(txt) if "youtube" in txt or "youtu.be" in txt else await download_media_url(txt)
            if p:
                await user_client.send_file(event.chat_id, p)
                try: os.remove(p)
                except: pass
            else: await event.reply("❌ دانلود نشد")
        elif w == "spy":
            if not (looks_like_id(txt) or looks_like_username(txt) or "t.me/" in txt):
                clear_waiting(); await event.reply("❌ ورودی معتبر نیست"); return
            try: ent = await resolve_entity(txt)
            except Exception as e:
                clear_waiting(); await event.reply(f"❌ پیدا نشد: {e}"); return
            info = await get_entity_info(ent)
            txt_out = (f"🕵️ **اطلاعات**\n\n"
                       f"👤 {info['first_name']} {info['last_name']}\n"
                       f"🆔 `{info['id']}`\n"
                       f"📛 @{info['username'] or 'نداره'}\n"
                       f"📱 `{info['phone'] or 'مخفی'}`\n")
            if info['about']: txt_out += f"📝 {info['about'][:200]}\n"
            await event.reply(txt_out, parse_mode="Markdown")
            clear_waiting()
        elif w == "block":
            if not (looks_like_id(txt) or looks_like_username(txt) or "t.me/" in txt):
                await event.reply("❌ ورودی معتبر نیست"); return
            try:
                ent = await resolve_entity(txt)
                await user_client(BlockRequest(id=ent))
                nm = getattr(ent, "first_name", "") or ""
                await event.reply(f"🚫 {nm} بلاک شد")
            except Exception as e: await event.reply(f"❌ {e}")
            clear_waiting()
        elif w == "ads_add":
            if not (looks_like_id(txt) or looks_like_username(txt) or "t.me/" in txt):
                await event.reply("❌ ورودی معتبر نیست"); return
            try: ent = await resolve_entity(txt)
            except Exception as e: await event.reply(f"❌ پیدا نشد: {e}"); return
            uid = str(ent.id)
            if uid not in state.get("broadcast_list", []):
                state.setdefault("broadcast_list", []).append(uid); save_state()
            clear_waiting()
            await event.reply(f"✅ اضافه شد: `{uid}`", parse_mode="Markdown")
        elif w == "ads_text":
            clear_waiting(); await event.reply("🚀 ارسال...")
            ok, fail = 0, 0
            for t in state.get("broadcast_list", []):
                try:
                    ent = await resolve_entity(t)
                    await user_client.send_message(ent, txt); ok += 1
                    await asyncio.sleep(2)
                except: fail += 1
            await event.reply(f"✅ موفق: {ok}\n❌ ناموفق: {fail}")
        elif w == "vs_add":
            state["current_action"] = f"vs_keyword_{txt}"
            clear_waiting()
            await event.reply(f"✅ کلیدواژه `{txt}` ثبت شد.", parse_mode="Markdown")
        elif w == "first_channel":
            if not (looks_like_id(txt) or looks_like_username(txt) or "t.me/" in txt or txt.startswith("@")):
                await event.reply("❌ ورودی معتبر نیست"); return
            try: ent = await resolve_entity(txt)
            except Exception as e: await event.reply(f"❌ پیدا نشد: {e}"); return
            clean = txt.strip().lstrip("@")
            if "t.me/" in clean:
                clean = clean.split("t.me/")[-1].split("/")[0].split("?")[0]
            if clean not in state.get("first_comment_targets", []):
                state.setdefault("first_comment_targets", []).append(clean); save_state()
            clear_waiting()
            await event.reply(f"✅ کانال اضافه شد: `{clean}`", parse_mode="Markdown")
    except Exception as e:
        log.error(f"text_in [{w}]: {e}", exc_info=True)

# ═══════════════════ HELP ═══════════════════
HELP_TEXT = """📖 **راهنمای سلف‌بات 7.3**

━━━━━━━━━━━━━━━━━━━━
📊 **عمومی** (تو PV یا Saved)
━━━━━━━━━━━━━━━━━━━━
• `پنل` → باز کردن پنل
• `هلپ` → همین راهنما
• `پینگ` → سرعت
• `اطلاعات` → اطلاعات اکانت
• `آنلاین` → تاگل همیشه آنلاین
• `سکوت` → تاگل سکوت
• `ذخیره` → تاگل ذخیره مدیا

━━━━━━━━━━━━━━━━━━━━
🎲 **تقلب تاس**
━━━━━━━━━━━━━━━━━━━━
• `تاس 1` تا `تاس 6` → تاس با عدد دلخواه
• `تاس خاموش` → خاموش کردن تقلب تاس
• مثال: `تاس 6`

━━━━━━━━━━━━━━━━━━━━
🗑 **حذف پیام‌ها**
━━━━━━━━━━━━━━━━━━━━
• تو چت PV بنویس: `حذف 50`
• تو Saved: `حذف 100 @username`
• `حذف` = پیش‌فرض ۱۰۰ پیام
• حذف دوطرفه (هر دو نفر)

━━━━━━━━━━━━━━━━━━━━
🎬 **اکشن مداوم**
━━━━━━━━━━━━━━━━━━━━
1️⃣ پنل → 🎬 اکشن → یکی رو بزن
2️⃣ برو تو چت → بنویس: `اکشن`
3️⃣ طرف می‌بینه داری تایپ/عکس/... می‌فرستی
4️⃣ توقف: `توقف اکشن`

━━━━━━━━━━━━━━━━━━━━
🚫 **مدیریت (ریپلای)**
━━━━━━━━━━━━━━━━━━━━
• `سکوت` • `رفع سکوت` • `لیست سکوت`
• `بلاک` • `آنبلاک` • `حذف`

━━━━━━━━━━━━━━━━━━━━
🎨 **ابزار**
━━━━━━━━━━━━━━━━━━━━
• `ترجمه Hello`
• `هوش چیه پایتون؟`
• `ویس سلام`
• `متن سلام`
• `حساب 25*4+10`
• `لوگو اسم من`
• `آهنگ Imagine Dragons`
• `دانلود [لینک]`

━━━━━━━━━━━━━━━━━━━━
🌐 **سرویس‌ها**
━━━━━━━━━━━━━━━━━━━━
• `اخبار` • `قیمت`

━━━━━━━━━━━━━━━━━━━━
💾 **ویس‌ها**
━━━━━━━━━━━━━━━━━━━━
• `ویس ذخیره خنده`
• `ویس خنده`

━━━━━━━━━━━━━━━━━━━━
⚙️ **تنظیمات**
━━━━━━━━━━━━━━━━━━━━
• `پاسخ [متن]`
• `منشی [متن]`
• `ریکت ❤`
• `ویتا [متن]`

🎛 پنل: `پنل`"""

# ═══════════════════ دستورات متنی ═══════════════════
async def cheat_dice(chat_id, target):
    try:
        for _ in range(30):
            msg = await user_client.send_file(
                chat_id,
                types.InputMediaDice(emoticon="🎲")
            )

            dice = getattr(msg, "dice", None)
            value = getattr(dice, "value", None)

            if value == target:
                return msg

            try:
                await msg.delete()
            except:
                pass

            await asyncio.sleep(0.15)

        return None

    except Exception as e:
        log.error(f"cheat_dice: {e}", exc_info=True)
        return None


@user_client.on(events.NewMessage(outgoing=True))
async def command_handler(event):
    txt = (event.raw_text or "").strip()
    if not txt or txt == "پنل": return
    if state.get("waiting_for"): return
    if event.is_group or event.is_channel: return

    lower = txt.lower()
    chat_id = event.chat_id

    if lower in ("هلپ", "help", "راهنما"):
        try: await event.reply(HELP_TEXT, parse_mode="Markdown")
        except Exception as e: await event.reply(f"خطا: {e}")
        return

    if lower in ("پینگ", "ping"):
        t0 = time.time(); await user_client.get_me()
        ms = int((time.time() - t0) * 1000)
        await event.reply(f"📊 **پینگ:** `{ms} ms`", parse_mode="Markdown")
        return

    if lower in ("اطلاعات", "info", "اکانت"):
        me = await user_client.get_me()
        await event.reply(
            f"👤 **اطلاعات**\n\n🆔 `{me.id}`\n👤 {me.first_name or '-'}\n"
            f"📛 @{me.username or 'نداری'}\n📱 `{me.phone or '-'}`\n"
            f"💎 {'پریمیوم' if getattr(me,'premium',False) else 'عادی'}",
            parse_mode="Markdown")
        return

    if lower in ("آنلاین", "online"):
        state["always_online"] = not state["always_online"]; save_state()
        await event.reply(f"🟢 آنلاین: {'🟢' if state['always_online'] else '🔴'}")
        return

    if lower == "سکوت" and not event.is_reply:
        state["silent"] = not state["silent"]; save_state()
        await event.reply(f"🎭 سکوت: {'🟢' if state['silent'] else '🔴'}")
        return

    if lower in ("ذخیره", "save"):
        state["save_media"] = not state["save_media"]; save_state()
        await event.reply(f"📷 ذخیره: {'🟢' if state['save_media'] else '🔴'}")
        return

    # ═══ تاس تقلبی ═══
    if lower.startswith("تاس"):
        parts = txt.split()

        if len(parts) != 2:
            await event.reply(
                "🎲 **دستور تاس**\n\n"
                "• `تاس 1` تا `تاس 6`\n"
                "• `تاس روشن` → روشن با عدد قبلی\n"
                "• `تاس خاموش` → خاموش\n\n"
                "💡 مثال: `تاس 6`",
                parse_mode="Markdown")
            return

        value = parts[1]

        if value == "خاموش":
            state["dice_cheat"] = None
            save_state()
            await event.reply("🎲 تقلب تاس: 🔴 خاموش")
            return

        if value == "روشن":
            target = state.get("dice_cheat")
            if not target:
                target = 6
                state["dice_cheat"] = target
            save_state()
            await event.reply(f"🎲 تقلب تاس: 🟢 روشن — عدد {target}")
            return

        try:
            target = int(value)
        except:
            await event.reply("❌ عدد تاس باید بین 1 تا 6 باشد")
            return

        if target < 1 or target > 6:
            await event.reply("❌ عدد تاس باید بین 1 تا 6 باشد")
            return

        state["dice_cheat"] = target
        save_state()

        try:
            await event.delete()
        except:
            pass

        msg = await cheat_dice(chat_id, target)

        if not msg:
            await user_client.send_message(
                chat_id,
                f"❌ بعد از ۳۰ تلاش، عدد {target} نیومد."
            )

        return

    # ═══ حذف با دستور ═══
    if lower.startswith("حذف") and not event.is_reply:
        parts = txt.split()
        
        # اگه تو Saved هستی و آیدی دادی
        if len(parts) >= 3:
            try: limit = int(parts[1])
            except:
                await event.reply("❌ مثال: `حذف 50 @username`")
                return
            target = parts[2]
            try:
                ent = await resolve_entity(target)
            except Exception as e:
                await event.reply(f"❌ پیدا نشد: {e}"); return
            await event.reply(f"⏳ حذف {limit} پیام از {getattr(ent,'first_name','?')}...")
            asyncio.create_task(delete_pv_messages(ent.id, limit))
            return
        
        # اگه تو PV طرف هستی
        if event.is_private and event.chat_id != ADMIN_ID:
            limit = 100
            if len(parts) >= 2:
                try:
                    limit = int(parts[1])
                    if limit < 1: limit = 100
                    if limit > 1000: limit = 1000
                except:
                    await event.reply("❌ مثال: `حذف 50`"); return
            peer_id = event.chat_id
            await event.delete()
            await event.reply(f"⏳ حذف {limit} پیام اخیر...")
            asyncio.create_task(delete_pv_messages(peer_id, limit))
            return
        
        # تو Saved با عدد یا بدون
        limit = 100
        if len(parts) >= 2:
            try:
                limit = int(parts[1])
                if limit < 1: limit = 100
                if limit > 1000: limit = 1000
            except: pass
        await event.reply(
            "🗑 **حذف پیام‌ها**\n\n"
            "🅰️ برو تو چت طرف → بنویس: `حذف 50`\n"
            "🅱️ یا اینجا بنویس: `حذف 100 @username`\n\n"
            "📌 حذف **دوطرفه**",
            parse_mode="Markdown")
        return

    # ═══ اکشن ═══
    if lower in ("اکشن", "action"):
        act = state.get("active_action")
        if not act:
            await event.reply("❌ اول از پنل انتخاب کن"); return
        peer_id = (await event.get_reply_message()).sender_id if event.is_reply else event.chat_id
        peers = state.get("action_peers", [])
        if peer_id not in peers: peers.append(peer_id)
        state["action_peers"] = peers
        state["action_running"] = True
        save_state()
        amap = {"typing": SendMessageTypingAction(),
                "photo": SendMessageUploadPhotoAction(progress=50),
                "video": SendMessageUploadVideoAction(progress=50),
                "voice": SendMessageRecordAudioAction(),
                "doc": SendMessageUploadDocumentAction(progress=50),
                "geo": SendMessageGeoLocationAction(),
                "sticker": SendMessageChooseStickerAction()}
        try:
            await asyncio.wait_for(user_client(SetTypingRequest(peer=peer_id, action=amap[act])), timeout=5)
            await event.reply(f"✅ اکشن `{act}` فعال\n⏹ توقف: `توقف اکشن`", parse_mode="Markdown")
        except Exception as e: await event.reply(f"❌ {e}")
        return

    if lower in ("توقف اکشن", "اکشن توقف"):
        state["active_action"] = None
        state["action_peers"] = []
        state["action_running"] = False
        save_state()
        await event.reply("🛑 متوقف شد")
        return

    # ═══ مدیریت با ریپلای ═══
    if event.is_reply:
        reply_msg = await event.get_reply_message()
        target_id = reply_msg.sender_id

        if lower == "سکوت":
            if target_id == ADMIN_ID:
                await event.reply("❌ خودت رو نه"); return
            if target_id not in state.get("muted_users", []):
                state.setdefault("muted_users", []).append(target_id); save_state()
                await event.reply("🔇 ساکت شد")
            else: await event.reply("⚠️ قبلاً ساکت بود")
            return
        if lower in ("رفع سکوت", "آن سکوت"):
            if target_id in state.get("muted_users", []):
                state["muted_users"].remove(target_id); save_state()
                await event.reply("🔊 برداشته شد")
            else: await event.reply("⚠️ ساکت نبود")
            return
        if lower in ("بلاک", "block"):
            try:
                ent = await user_client.get_entity(target_id)
                await user_client(BlockRequest(id=ent))
                await event.reply("🚫 بلاک شد")
            except Exception as e: await event.reply(f"❌ {e}")
            return
        if lower in ("آنبلاک", "unblock"):
            try:
                ent = await user_client.get_entity(target_id)
                await user_client(UnblockRequest(id=ent))
                await event.reply("✅ آنبلاک شد")
            except Exception as e: await event.reply(f"❌ {e}")
            return
        if lower in ("حذف", "پاک", "del"):
            try:
                await reply_msg.delete()
                await event.delete()
            except Exception as e: await event.reply(f"❌ {e}")
            return

    if lower in ("لیست سکوت", "سکوت لیست"):
        muted = state.get("muted_users", [])
        if not muted: await event.reply("📋 خالیه"); return
        lines = []
        for uid in muted:
            try:
                ent = await user_client.get_entity(uid)
                lines.append(f"• {getattr(ent,'first_name','?')} - `{uid}`")
            except: lines.append(f"• `{uid}`")
        await event.reply("📋:\n" + "\n".join(lines), parse_mode="Markdown")
        return

    if lower.startswith("ترجمه "):
        text = txt[5:].strip()
        if not text: await event.reply("❌ متن بده"); return
        await event.reply("🌐...")
        res = await tr_text(text, "fa"); await event.reply(f"🇮🇷 {res}")
        return

    if lower.startswith("هوش "):
        text = txt[4:].strip()
        if not text: await event.reply("❌ متن بده"); return
        await event.reply("🎨...")
        res = await ai_reply(text); await event.reply(res[:4000])
        return

    if lower.startswith("ویس ") and not lower.startswith("ویس ذخیره"):
        keyword = txt[4:].strip()
        vr = state.get("voice_replies", {})
        if keyword in vr and os.path.exists(vr[keyword]):
            await event.delete()
            await user_client.send_file(chat_id, vr[keyword])
            return
        if not HAS_TTS: await event.reply("❌ gTTS نصب نیست"); return
        await event.reply("🎬...")
        p = await tts_text(keyword)
        if p:
            await user_client.send_file(chat_id, p)
            try: os.remove(p)
            except: pass
        else: await event.reply("❌ خطا")
        return

    if lower.startswith("ویس ذخیره "):
        keyword = txt[10:].strip()
        if not keyword: await event.reply("❌ مثال: `ویس ذخیره خنده`"); return
        state["current_action"] = f"vs_keyword_{keyword}"
        save_state()
        await event.reply(f"✅ کلیدواژه `{keyword}` ثبت شد. ویس بفرست.", parse_mode="Markdown")
        return

    if lower.startswith("متن "):
        text = txt[4:].strip()
        if not text: await event.reply("❌ متن بده"); return
        try: await event.reply(text_modes(text))
        except Exception as e: await event.reply(f"❌ {e}")
        return

    if lower.startswith("حساب "):
        expr = txt[5:].strip()
        try:
            safe = re.sub(r"[^0-9+\-*/().%\s]", "", expr).replace("^", "**")
            r = eval(safe, {"__builtins__":{}, "sqrt":math.sqrt, "sin":math.sin,
                "cos":math.cos, "tan":math.tan, "log":math.log,
                "pi":math.pi, "e":math.e, "abs":abs, "round":round}, {})
            await event.reply(f"🧮 `{safe} = {r}`", parse_mode="Markdown")
        except Exception as e: await event.reply(f"❌ {e}")
        return

    if lower.startswith("لوگو "):
        if not HAS_PIL: await event.reply("❌ Pillow نیست"); return
        text = txt[5:].strip()
        if not text: await event.reply("❌ متن بده"); return
        await event.reply("🖼...")
        p = make_logo(text, "neon")
        if p:
            await user_client.send_file(chat_id, p)
            try: os.remove(p)
            except: pass
        else: await event.reply("❌ خطا")
        return

    if lower in ("اخبار", "news"):
        await event.reply("📰...")
        news = await get_news()
        if news: await event.reply("📰 **اخبار**\n\n" + "\n\n".join(news[:6])[:3900], parse_mode="Markdown")
        else: await event.reply("❌ خطا")
        return

    if lower in ("قیمت", "price"):
        await event.reply("💰...")
        p = await get_price()
        txt_out = "💰 **قیمت ارز و طلا**\n\n"
        txt_out += f"💵 دلار: `{p['usd']:,}` تومان\n\n" if p["usd"] else "💵 دلار: نامشخص\n\n"
        txt_out += f"🥇 طلا ۱۸: `{p['gold']}` تومان\n" if p["gold"] else "🥇 طلا: نامشخص\n"
        await event.reply(txt_out, parse_mode="Markdown")
        return

    if lower.startswith("آهنگ "):
        if not HAS_YTDLP: await event.reply("❌ yt-dlp نیست"); return
        query = txt[5:].strip()
        if not query: await event.reply("❌ مثال: `آهنگ Imagine Dragons`"); return
        await event.reply(f"🎵 {query}")
        results = await search_music(query)
        if not results: await event.reply("❌ نتیجه نیومد"); return
        lines = [f"{i}. {e.get('title','?')}\n`{e.get('url') or 'https://youtu.be/'+e.get('id','')}`"
                 for i, e in enumerate(results[:5], 1)]
        await event.reply("🎵 **نتایج:**\n\n" + "\n\n".join(lines), parse_mode="Markdown")
        return

    if lower.startswith("دانلود "):
        url = txt[7:].strip()
        if not url: await event.reply("❌ لینک بده"); return
        await event.reply("⬇️...")
        p = await download_music(url) if "youtube" in url or "youtu.be" in url else await download_media_url(url)
        if p:
            await user_client.send_file(chat_id, p)
            try: os.remove(p)
            except: pass
        else: await event.reply("❌ دانلود نشد")
        return

    if lower.startswith("پاسخ "):
        text = txt[6:].strip()
        state["auto_reply"]["text"] = text
        state["auto_reply"]["enabled"] = True
        save_state()
        await event.reply(f"✅ پاسخ:\n`{text}`", parse_mode="Markdown")
        return

    if lower.startswith("منشی "):
        text = txt[5:].strip()
        state["secretary"]["text"] = text
        state["secretary"]["enabled"] = True
        save_state()
        await event.reply(f"✅ منشی:\n`{text}`", parse_mode="Markdown")
        return

    if lower.startswith("ریکت "):
        emoji = txt[5:].strip()
        if not emoji: await event.reply("❌ مثال: `ریکت ❤`"); return
        state["react"]["emoji"] = emoji
        state["react"]["enabled"] = True
        save_state()
        await event.reply(f"✅ ریکت: {emoji}")
        return

# ═══════════════════ ذخیره ویس ═══════════════════
@user_client.on(events.NewMessage(outgoing=True, func=lambda e: e.voice or e.audio))
async def voice_save(event):
    ca = state.get("current_action") or ""
    if ca.startswith("vs_keyword_"):
        keyword = ca.replace("vs_keyword_", "").strip()
        try:
            p = await user_client.download_media(event.message.media, file=str(MEDIA_DIR))
            if p:
                state.setdefault("voice_replies", {})[keyword] = p
                save_state()
                await event.reply(f"✅ ویس `{keyword}` ذخیره شد", parse_mode="Markdown")
        except Exception as e: log.error(f"vs_save: {e}")
        state["current_action"] = None; save_state()

# ═══════════════════ سین خودکار ═══════════════════
@user_client.on(events.NewMessage(incoming=True))
async def auto_scene_handler(event):
    if not state.get("auto_scene"):
        return

    if event.sender_id == ADMIN_ID:
        return

    try:
        await user_client.send_read_acknowledge(
            event.chat_id,
            max_id=event.id
        )
    except Exception as e:
        log.warning(f"auto_scene: {e}")


# ═══════════════════ قفل‌های PV ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def locks_handler(event):
    if event.sender_id == ADMIN_ID:
        return

    msg = event.message
    blocked = False

    # قفل مدیا
    if state.get("lock_media"):
        if (msg.photo or msg.video or msg.document or msg.audio or
                msg.voice or msg.sticker or msg.gif):
            blocked = True

    # قفل لینک
    if not blocked and state.get("lock_link"):
        txt = event.raw_text or ""
        if re.search(r"(?:https?://|www\.|t\.me/|telegram\.me/)", txt, re.I):
            blocked = True

    # قفل فوروارد
    if not blocked and state.get("lock_forward"):
        if getattr(msg, "fwd_from", None):
            blocked = True

    # قفل ربات
    if not blocked and state.get("lock_bot"):
        try:
            sender = await event.get_sender()
            if getattr(sender, "bot", False):
                blocked = True
        except Exception:
            pass

    # قفل یوزرنیم
    if not blocked and state.get("lock_username"):
        txt = event.raw_text or ""
        if re.search(r"(?<![A-Za-z0-9_])@[A-Za-z][A-Za-z0-9_]{4,31}\b", txt):
            blocked = True

    if blocked:
        try:
            await event.delete()
        except Exception as e:
            log.warning(f"locks delete: {e}")
        raise events.StopPropagation


@user_client.on(events.MessageEdited(incoming=True, func=lambda e: e.is_private))
async def lock_edit_handler(event):
    if event.sender_id == ADMIN_ID:
        return

    if not state.get("lock_edit"):
        return

    try:
        await event.delete()
        stat_inc("deleted")
    except Exception as e:
        log.warning(f"lock_edit delete: {e}")

    raise events.StopPropagation


# ═══════════════════ گارد عضویت اجباری پیوی ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def force_pv_guard(event):
    f = state.get("force_pv", {})

    if not f.get("enabled"):
        return

    if event.sender_id == ADMIN_ID:
        return

    channel = f.get("channel")
    link = f.get("link")

    if not channel or not link:
        return

    try:
        permissions = await user_client.get_permissions(
            channel,
            event.sender_id
        )

        # عضو واقعی کانال است
        if permissions and not permissions.has_left and not permissions.is_banned:
            return

    except Exception as e:
        # خطای شبکه/API نباید همه کاربران را اشتباهی غیرعضو حساب کند
        name = type(e).__name__

        if "UserNotParticipant" not in name:
            log.warning(f"force_pv check failed: {e}")
            return

    # غیرعضو: پیام را حذف کن
    try:
        await event.delete()
    except Exception as e:
        log.warning(f"force_pv delete: {e}")

    # لینک عضویت را برای کاربر بفرست
    try:
        await user_client.send_message(
            event.sender_id,
            "🚫 **عضویت اجباری**\n\n"
            "برای ارسال پیام به این پیوی، ابتدا باید عضو کانال زیر شوی:\n\n"
            f"🔗 {link}\n\n"
            "بعد از عضویت دوباره پیام بده.",
            parse_mode="Markdown",
            link_preview=False
        )
    except Exception as e:
        log.warning(f"force_pv send: {e}")

    # نگذار save_media / auto_reply / react / filter و ... اجرا شوند
    raise events.StopPropagation


# ═══════════════════ ذخیره مدیا ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def save_media(event):
    if not state["save_media"]: return
    if event.sender_id == ADMIN_ID: return
    msg = event.message
    if not (msg.photo or msg.video): return
    try:
        s = await event.get_sender()
        name = getattr(s, "first_name", "?") or "?"
        cap = f"📥 **از PV**\n👤 {name}\n🆔 `{event.sender_id}`"
        path = await user_client.download_media(msg)
        if not path: return
        try:
            up = await user_client.upload_file(path)
            await user_client.send_file("me", up, caption=cap, parse_mode="md",
                force_document=False, supports_streaming=bool(msg.video))
            stat_inc("media_saved")
        finally:
            try: os.remove(path)
            except: pass
    except Exception as e: log.error(f"save_media: {e}")

# ═══════════════════ پاک ساکت‌ها ═══════════════════
@user_client.on(events.NewMessage(incoming=True))
async def muted_handler(event):
    if event.sender_id in state.get("muted_users", []):
        try:
            await event.delete()
            stat_inc("muted_deleted")
        except:
            pass

# ═══════════════════ نگهبان ═══════════════════
@user_client.on(events.NewMessage(incoming=True))
async def guard(event):
    if not state.get("chat_guard"): return
    if event.sender_id == ADMIN_ID: return
    if not (event.is_private or event.is_group): return
    try:
        if event.raw_text:
            info = f"💬 `{event.chat_id}`\n👤 `{event.sender_id}`\n\n{event.raw_text}"
            await user_client.send_message("me", info, parse_mode="md")
        elif event.photo or event.video:
            await user_client.forward_messages("me", event.message)
    except: pass

# ═══════════════════ پاسخ خودکار ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def auto_reply_handler(event):
    if event.sender_id == ADMIN_ID: return
    if event.sender_id in state.get("muted_users", []): return
    if state.get("silent"): return
    if event.sender_id in state.get("friends", []): return
    if event.sender_id in state.get("enemies", []):
        try: await user_client(BlockRequest(id=event.sender_id))
        except: pass
        return
    ar = state["auto_reply"]
    if ar.get("enabled") and ar.get("text"):
        try:
            await event.reply(ar["text"])
            stat_inc("auto_replies")
        except:
            pass
        return
    sc = state["secretary"]
    if sc.get("enabled") and sc.get("text"):
        try:
            await event.reply(sc["text"])
            stat_inc("auto_replies")
        except:
            pass

# ═══════════════════ فیلتر ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def word_filter(event):
    if not state.get("filters"): return
    if event.sender_id == ADMIN_ID: return
    txt = (event.raw_text or "").lower()
    if not txt: return
    for w in state["filters"]:
        if w in txt:
            try:
                await event.delete()
                stat_inc("filtered")
            except:
                pass
            return

# ═══════════════════ ریکت ═══════════════════
@user_client.on(events.NewMessage(incoming=True, func=lambda e: e.is_private))
async def auto_react(event):
    if not state["react"]["enabled"]: return
    if event.sender_id == ADMIN_ID: return
    try:
        await user_client(SendReactionRequest(peer=event.chat_id, msg_id=event.id,
            reaction=[ReactionEmoji(emoticon=state["react"]["emoji"])]))
        stat_inc("reactions")
    except: pass

# ═══════════════════ حلقه اکشن ═══════════════════
ACTION_MAP = {
    "typing": SendMessageTypingAction,
    "photo": lambda: SendMessageUploadPhotoAction(progress=50),
    "video": lambda: SendMessageUploadVideoAction(progress=50),
    "voice": SendMessageRecordAudioAction,
    "doc": lambda: SendMessageUploadDocumentAction(progress=50),
    "geo": SendMessageGeoLocationAction,
    "sticker": SendMessageChooseStickerAction,
}

async def action_loop():
    while True:
        try:
            act = state.get("active_action")
            peers = state.get("action_peers", [])
            if act and peers:
                a_maker = ACTION_MAP.get(act)
                if a_maker:
                    for peer_id in list(peers):
                        try:
                            await asyncio.wait_for(
                                user_client(SetTypingRequest(peer=peer_id, action=a_maker())),
                                timeout=4)
                        except: pass
        except Exception as e: log.error(f"action_loop: {e}")
        await asyncio.sleep(4)

# ═══════════════════ آنلاین ═══════════════════
async def online_loop():
    while True:
        if state.get("always_online"):
            try:
                await user_client(UpdateStatusRequest(offline=False))
            except Exception as e:
                log.error(f"ONLINE ERROR: {type(e).__name__}: {e}")
        await asyncio.sleep(30)

# ═══════════════════ ساعت ═══════════════════
CLOCK_FONTS = {
    1: str.maketrans("0123456789", "0123456789"),
    2: str.maketrans("0123456789", "𝟬𝟭𝟮𝟯𝟰𝟱𝟲𝟳𝟴𝟵"),
    3: str.maketrans("0123456789", "𝟘𝟙𝟚𝟛𝟜𝟝𝟞𝟟𝟠𝟡"),
    4: str.maketrans("0123456789", "𝟎𝟏𝟐𝟑𝟒𝟓𝟔𝟕𝟖𝟗"),
    5: str.maketrans("0123456789", "０１２３４５６７８９"),
    6: str.maketrans("0123456789", "⁰¹²³⁴⁵⁶⁷⁸⁹"),
}

def format_clock_time(current):
    font = state.get("clock_font", 1)
    table = CLOCK_FONTS.get(font, CLOCK_FONTS[1])
    return current.translate(table)

async def clock_loop():
    last_minute = None
    while True:
        try:
            if state.get("clock_mode"):
                n = datetime.now()
                current = n.strftime("%H:%M")
                if current != last_minute:
                    styled_time = format_clock_time(current)
                    base_name = state.get("original_name") or "User"
                    new_name = f"{base_name} {styled_time}"
                    try:
                        await user_client(UpdateProfileRequest(first_name=new_name))
                        last_minute = current
                    except Exception as e:
                        log.error(f"clock: {e}")
            else:
                last_minute = None
        except Exception:
            pass
        await asyncio.sleep(30)

# ═══════════════════ اجرا ═══════════════════

# ═══════════════════ 📂 دسته‌بندی خودکار Saved Messages ═══════════════════

@user_client.on(events.NewMessage(outgoing=True))
async def saved_messages_folder_handler(event):
    try:
        me = await user_client.get_me()

        # فقط Saved Messages
        if event.chat_id != me.id:
            return

        msg = event.message
        folders = state.setdefault("saved_folders", {
            "videos": [],
            "photos": [],
            "music": [],
            "voices": [],
            "gifs": [],
            "files": [],
            "texts": [],
            "links": [],
        })

        # جلوگیری از ثبت دوباره
        if any(msg.id in ids for ids in folders.values()):
            return

        folder = None

        if msg.gif:
            folder = "gifs"
        elif msg.video:
            folder = "videos"
        elif msg.photo:
            folder = "photos"
        elif msg.voice:
            folder = "voices"
        elif msg.audio:
            folder = "music"
        elif msg.document:
            folder = "files"
        elif msg.text:
            if re.search(
                r"(https?://\S+|www\.\S+|t\.me/\S+)",
                msg.text,
                re.IGNORECASE
            ):
                folder = "links"
            else:
                folder = "texts"

        if not folder:
            return

        folders[folder].append(msg.id)
        save_state()

        log.info(
            f"📂 Saved Messages: message={msg.id} -> {folder}"
        )

    except Exception as e:
        log.error(
            f"saved_messages_folder_handler: {e}",
            exc_info=True
        )

async def main():
    await load_state()
    bot_app.add_handler(InlineQueryHandler(inline_query))
    bot_app.add_handler(MessageHandler(filters.Document.ALL, restore_upload))

    bot_app.add_handler(CallbackQueryHandler(cb))
    await bot_app.initialize(); await bot_app.start()
    await bot_app.updater.start_polling()
    await user_client.start()
    asyncio.create_task(online_loop())
    asyncio.create_task(clock_loop())
    asyncio.create_task(schedule_loop())
    asyncio.create_task(action_loop())
    print("=" * 55)
    print("✅ سلف‌بات 7.3 آماده")
    print("📌 پنل: `پنل`")
    print("📖 راهنما: `هلپ`")
    print("🗑 حذف: `حذف 50` یا `حذف 100 @user`")
    print("=" * 55)
    await user_client.run_until_disconnected()

if __name__ == "__main__":
    asyncio.run(main())
