# ========== ربات خلاصه‌ساز لینک و متن (Link/Text Summarizer Bot) ==========
# یک لینک بفرست -> ربات محتوای صفحه رو استخراج و خلاصه می‌کنه
# یک متن بلند بفرست -> ربات همون متن رو خلاصه می‌کنه
#
# پیش‌فرض: خلاصه‌سازی استخراجی (extractive) کاملاً آفلاین و بدون نیاز به هیچ API
# ارتقا (اختیاری): اگر LLM_API_KEY رو تنظیم کنی، خلاصه‌های خیلی باکیفیت‌تر و
# روان‌تر با یک مدل زبانی (سازگار با OpenAI API) تولید می‌شه.
#
# دستورات:
# /start            راهنما
# /length short|medium|long   تنظیم طول خلاصه (پیش‌فرض medium)
# فقط کافیه لینک یا متن رو مستقیم بفرستی.

from __future__ import annotations

import re
import logging
from collections import Counter

import requests
from bs4 import BeautifulSoup
from telegram import Update
from telegram.ext import Application, CommandHandler, MessageHandler, ContextTypes, filters

logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

# ---------- تنظیمات ----------
BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"     # توکن ربات از @BotFather

# اختیاری — اگر پر بشه، خلاصه‌سازی با مدل زبانی انجام می‌شه (سازگار با OpenAI API:
# OpenAI خودش، یا هر سرویس دیگه‌ای مثل OpenRouter/Groq/یک سرور محلی که همین فرمت رو پیاده کرده)
LLM_API_KEY = ""
LLM_BASE_URL = "https://api.openai.com/v1"   # برای OpenRouter مثلاً: https://openrouter.ai/api/v1
LLM_MODEL = "gpt-4o-mini"

MAX_FETCH_CHARS = 8000   # سقف طول متنی که از صفحه‌ی وب استخراج می‌شه (برای سرعت و کنترل حجم)

SENTENCE_COUNTS = {"short": 2, "medium": 4, "long": 7}
user_length_pref = {}   # chat_id -> "short"/"medium"/"long"  (در حافظه؛ برای سادگی)

STOPWORDS = set("""
the a an is are was were be been being of to in on at for with and or but if than then so
this that these those it its as by from not no do does did will would can could should
و در به از که این آن را با است هست بود شد شده می‌شود برای یا اما اگر تا هم نیز یک دو
""".split())


# ---------- استخراج متن از لینک ----------
def is_url(text: str) -> bool:
    return bool(re.match(r"^https?://", text.strip()))


def extract_article_text(url: str) -> str:
    headers = {"User-Agent": "Mozilla/5.0 (compatible; SummarizerBot/1.0)"}
    resp = requests.get(url, headers=headers, timeout=12)
    resp.raise_for_status()
    soup = BeautifulSoup(resp.text, "html.parser")

    for tag in soup(["script", "style", "nav", "footer", "header", "aside", "noscript"]):
        tag.decompose()

    title = soup.title.string.strip() if soup.title and soup.title.string else ""
    paragraphs = [p.get_text(" ", strip=True) for p in soup.find_all("p")]
    paragraphs = [p for p in paragraphs if len(p) > 40]   # حذف خط‌های خیلی کوتاه (منو/تبلیغ و ...)
    body = "\n".join(paragraphs)[:MAX_FETCH_CHARS]

    return (title + "\n\n" + body).strip() if title else body


# ---------- خلاصه‌ساز استخراجی (بدون نیاز به API) ----------
def split_sentences(text: str):
    # جداکننده‌ی جمله برای فارسی و انگلیسی با هم
    parts = re.split(r"(?<=[\.\!\?\؟])\s+|\n+", text)
    return [p.strip() for p in parts if len(p.strip()) > 15]


def extractive_summary(text: str, num_sentences: int) -> str:
    sentences = split_sentences(text)
    if len(sentences) <= num_sentences:
        return " ".join(sentences) if sentences else "متنی برای خلاصه‌سازی پیدا نشد."

    words = re.findall(r"[A-Za-z\u0600-\u06FF]{2,}", text.lower())
    freq = Counter(w for w in words if w not in STOPWORDS)
    if not freq:
        return " ".join(sentences[:num_sentences])
    max_freq = max(freq.values())
    for w in freq:
        freq[w] /= max_freq

    scored = []
    for idx, sent in enumerate(sentences):
        sent_words = re.findall(r"[A-Za-z\u0600-\u06FF]{2,}", sent.lower())
        if not sent_words:
            continue
        score = sum(freq.get(w, 0) for w in sent_words) / len(sent_words)
        # جمله‌های ابتدای متن معمولاً مهم‌ترن؛ یه کوچولو امتیاز اضافه بگیرن
        if idx < 3:
            score *= 1.15
        scored.append((score, idx, sent))

    top = sorted(scored, key=lambda x: x[0], reverse=True)[:num_sentences]
    top_in_order = [s for _, _, s in sorted(top, key=lambda x: x[1])]
    return " ".join(top_in_order)


# ---------- خلاصه‌ساز مبتنی بر LLM (اختیاری) ----------
def llm_summary(text: str, length: str) -> str | None:
    if not LLM_API_KEY:
        return None
    length_hint = {
        "short": "در حد ۲ جمله‌ی کوتاه",
        "medium": "در حد یک پاراگراف کوتاه (۴ تا ۵ جمله)",
        "long": "کامل و جزئی‌تر، در حد یک پاراگراف بلند",
    }[length]
    try:
        resp = requests.post(
            f"{LLM_BASE_URL}/chat/completions",
            headers={"Authorization": f"Bearer {LLM_API_KEY}", "Content-Type": "application/json"},
            json={
                "model": LLM_MODEL,
                "messages": [
                    {"role": "system", "content": f"متن داده‌شده را خلاصه کن، {length_hint}. به همان زبانی که متن نوشته شده جواب بده."},
                    {"role": "user", "content": text[:12000]},
                ],
                "temperature": 0.3,
            },
            timeout=30,
        )
        resp.raise_for_status()
        data = resp.json()
        return data["choices"][0]["message"]["content"].strip()
    except Exception as e:
        logger.warning(f"LLM summary failed, falling back to extractive: {e}")
        return None


def summarize(text: str, length: str) -> tuple[str, str]:
    """Returns (summary, method_used)."""
    llm_result = llm_summary(text, length)
    if llm_result:
        return llm_result, "llm"
    return extractive_summary(text, SENTENCE_COUNTS[length]), "extractive"


# ---------- هندلرها ----------
async def cmd_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text(
        "سلام! 👋 یه لینک یا یه متن بلند برام بفرست، خلاصه‌ش می‌کنم.\n\n"
        "برای تنظیم طول خلاصه:\n"
        "/length short — کوتاه\n"
        "/length medium — متوسط (پیش‌فرض)\n"
        "/length long — بلند\n\n"
        "بدون تنظیم هیچ کلید API‌ای هم کار می‌کنم (با یک الگوریتم استخراجی سبک)؛ "
        "اگه صاحب ربات یک کلید مدل زبانی تنظیم کرده باشه، خلاصه‌ها خیلی روان‌تر می‌شن."
    )


async def cmd_length(update: Update, context: ContextTypes.DEFAULT_TYPE):
    if not context.args or context.args[0] not in SENTENCE_COUNTS:
        await update.message.reply_text("فرمت درست: /length short یا /length medium یا /length long")
        return
    user_length_pref[update.effective_chat.id] = context.args[0]
    await update.message.reply_text(f"باشه، از الان خلاصه‌ها «{context.args[0]}» می‌شن.")


async def on_message(update: Update, context: ContextTypes.DEFAULT_TYPE):
    text = (update.message.text or "").strip()
    if not text:
        return
    chat_id = update.effective_chat.id
    length = user_length_pref.get(chat_id, "medium")

    status_msg = await update.message.reply_text("⏳ در حال پردازش...")

    try:
        if is_url(text):
            try:
                content = extract_article_text(text)
            except Exception as e:
                await status_msg.edit_text(f"❌ نتونستم صفحه رو باز کنم: {e}")
                return
            if not content or len(content) < 80:
                await status_msg.edit_text("❌ متن قابل‌توجهی توی این صفحه پیدا نشد (شاید صفحه نیاز به لاگین داره یا محتواش جاوااسکریپتیه).")
                return
        else:
            content = text
            if len(content) < 200:
                await status_msg.edit_text("این متن به‌اندازه‌ی کافی بلند نیست که نیاز به خلاصه داشته باشه 🙂")
                return

        summary, method = summarize(content, length)
        prefix = "🧠 خلاصه (مدل زبانی):\n\n" if method == "llm" else "📝 خلاصه:\n\n"
        await status_msg.edit_text(prefix + summary)

    except Exception as e:
        logger.exception("summarize failed")
        await status_msg.edit_text(f"❌ یه مشکلی پیش اومد: {e}")


def main():
    app = Application.builder().token(BOT_TOKEN).build()
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("length", cmd_length))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, on_message))

    logger.info("ربات خلاصه‌ساز روشن شد...")
    app.run_polling()


if __name__ == "__main__":
    main()
