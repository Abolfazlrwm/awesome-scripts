import asyncio
import logging
import os
import re
from typing import List, Iterable

from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup, InputMediaPhoto
from telegram.constants import ChatAction
from telegram.ext import Application, ApplicationBuilder, CommandHandler, ContextTypes, MessageHandler, filters, CallbackQueryHandler

from digiato_scraper import search as digiato_search, fetch_article
import config


logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)
LOGGER = logging.getLogger(__name__)


BOT_NAME = "DigiatoScraperBot"

MAX_PAGES_DEFAULT = getattr(config, "MAX_PAGES", None)
if MAX_PAGES_DEFAULT is None:
    MAX_PAGES_DEFAULT = int(os.getenv("MAX_PAGES", "2"))


def _get_bot_token() -> str:
    token = getattr(config, "BOT_TOKEN", None)
    if token and token != "PASTE_YOUR_BOT_TOKEN_HERE":
        return token
    token = os.getenv("BOT_TOKEN")
    if token:
        return token
    raise RuntimeError("BOT_TOKEN not set. Set it in config.py or env var BOT_TOKEN.")


def _truncate(text: str, max_len: int) -> str:
    if len(text) <= max_len:
        return text
    return text[: max_len - 1] + "…"


def _split_chunks(text: str, max_len: int = 3500) -> List[str]:
    if not text:
        return []
    if len(text) <= max_len:
        return [text]
    parts: List[str] = []
    current: List[str] = []
    current_len = 0
    for para in text.split("\n\n"):
        p = para.strip()
        if not p:
            continue
        if current_len + len(p) + (2 if current else 0) <= max_len:
            current.append(p)
            current_len += len(p) + (2 if current_len else 0)
        else:
            if current:
                parts.append("\n\n".join(current))
            if len(p) <= max_len:
                current = [p]
                current_len = len(p)
            else:
                # hard split long paragraph
                for i in range(0, len(p), max_len):
                    parts.append(p[i:i+max_len])
                current = []
                current_len = 0
    if current:
        parts.append("\n\n".join(current))
    return parts


async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    text = (
        "سلام! من یک ربات جستجوی دیجیاتو هستم.\n\n"
        "نمونه استفاده بدون اسلش:\n"
        "- سرچ آیفون ۱۶\n"
        "- مقاله https://digiato.com/...\n\n"
        "دستورات:\n"
        "/search <عبارت> — جستجو در دیجیاتو\n"
        "/article <url> — دریافت محتوای یک مقاله\n"
        "/help — راهنما"
    )
    await update.effective_message.reply_text(text)


async def help_cmd(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await start(update, context)


def _build_results_keyboard(items: List[dict], page: int, pages: int, query: str) -> InlineKeyboardMarkup:
    buttons = []
    for _, it in enumerate(items):
        title = _truncate(it.get("title", "(بدون عنوان)"), 48)
        url = it.get("link", "")
        buttons.append([InlineKeyboardButton(title, url=url)])
    nav_row = []
    if page > 1:
        nav_row.append(InlineKeyboardButton("⬅️ قبلی", callback_data=f"nav:{query}:{page-1}:{pages}"))
    if page < pages:
        nav_row.append(InlineKeyboardButton("بعدی ➡️", callback_data=f"nav:{query}:{page+1}:{pages}"))
    if nav_row:
        buttons.append(nav_row)
    return InlineKeyboardMarkup(buttons)


async def search(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    if not context.args:
        await update.effective_message.reply_text("استفاده: /search کلمه_کلیدی")
        return
    query = " ".join(context.args).strip()
    await _do_search(query, update, context)


async def _do_search(query: str, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await context.bot.send_chat_action(chat_id=update.effective_chat.id, action=ChatAction.TYPING)
    results, pages_retrieved = await asyncio.to_thread(digiato_search, query, MAX_PAGES_DEFAULT)
    if not results:
        await update.effective_message.reply_text("نتیجه‌ای یافت نشد.")
        return
    per_page = 8
    total_pages = (len(results) + per_page - 1) // per_page
    page = 1
    subset = results[0:per_page]
    caption = f"نتایج برای: {query}\nتعداد کل: {len(results)} | صفحات واکشی‌شده: {pages_retrieved}"
    kb = _build_results_keyboard(subset, page, total_pages, query)
    await update.effective_message.reply_text(caption, reply_markup=kb, disable_web_page_preview=True)


async def on_nav_callback(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    query_obj = update.callback_query
    if not query_obj or not query_obj.data:
        return
    await query_obj.answer()
    try:
        _, query, page_s, pages_s = query_obj.data.split(":", maxsplit=3)
        page = int(page_s)
        pages = int(pages_s)
    except Exception:
        return
    results, pages_retrieved = await asyncio.to_thread(digiato_search, query, MAX_PAGES_DEFAULT)
    per_page = 8
    total_pages = (len(results) + per_page - 1) // per_page
    page = max(1, min(page, total_pages))
    start = (page - 1) * per_page
    end = start + per_page
    subset = results[start:end]
    caption = f"نتایج برای: {query}\nتعداد کل: {len(results)} | صفحات واکشی‌شده: {pages_retrieved}"
    kb = _build_results_keyboard(subset, page, total_pages, query)
    try:
        await query_obj.edit_message_text(caption, reply_markup=kb, disable_web_page_preview=True)
    except Exception:
        pass


async def article(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    if not context.args:
        await update.effective_message.reply_text("استفاده: /article آدرس_مقاله")
        return
    url = context.args[0].strip()
    await _send_article(url, update, context)


async def _send_article(url: str, update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await context.bot.send_chat_action(chat_id=update.effective_chat.id, action=ChatAction.TYPING)
    data = await asyncio.to_thread(fetch_article, url)
    if not data:
        await update.effective_message.reply_text("واکشی مقاله ناموفق بود.")
        return
    title = data.get("headline") or "(بدون عنوان)"
    desc = data.get("description") or ""
    author = data.get("author") or ""
    date_p = data.get("datePublished") or ""
    content_list = data.get("content", [])
    images = data.get("images", []) or []
    cover = data.get("image")
    if cover:
        if cover not in images:
            images.insert(0, cover)
    header = f"<b>{_truncate(title, 512)}</b>\n{_truncate(desc, 800)}\n\n✍️ {author} | 🗓 {date_p}\n\n🔗 <a href=\"{data.get('url')}\">لینک مقاله</a>"

    # Send images as media group if available
    if images:
        await context.bot.send_chat_action(chat_id=update.effective_chat.id, action=ChatAction.UPLOAD_PHOTO)
        media: List[InputMediaPhoto] = []
        for idx, url_img in enumerate(images[:8]):
            if idx == 0:
                media.append(InputMediaPhoto(media=url_img, caption=_truncate(header, 1024), parse_mode="HTML"))
            else:
                media.append(InputMediaPhoto(media=url_img))
        try:
            await update.effective_chat.send_media_group(media=media)
        except Exception:
            # Fallback to single image + text if media group fails
            try:
                await update.effective_chat.send_photo(photo=images[0], caption=_truncate(header, 1024), parse_mode="HTML")
            except Exception:
                await update.effective_message.reply_html(_truncate(header, 3500), disable_web_page_preview=False)
    else:
        await update.effective_message.reply_html(_truncate(header, 3500), disable_web_page_preview=False)

    # Send body in chunks
    body = "\n\n".join(content_list)
    for chunk in _split_chunks(body, 3500):
        try:
            await update.effective_chat.send_message(chunk)
        except Exception:
            pass


async def handle_text(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    text = (update.effective_message.text or "").strip()
    if not text:
        return

    # Persian search command without slash
    m = re.match(r"^(?:سرچ|جستجو)\s+(.+)$", text)
    if m:
        query = m.group(1).strip()
        await _do_search(query, update, context)
        return

    # Persian article command without slash
    m = re.match(r"^(?:مقاله|لینک)\s+(\S+)$", text)
    if m:
        url = m.group(1).strip()
        await _send_article(url, update, context)
        return

    # Auto-detect Digiato URL anywhere in the text
    url_match = re.search(r"https?://(?:www\.)?digiato\.com/\S+", text)
    if url_match:
        await _send_article(url_match.group(0), update, context)
        return

    # Persian help
    if text in {"راهنما", "کمک", "help"}:
        await start(update, context)
        return


async def unknown(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await update.effective_message.reply_text("دستور نامعتبر. /help را ببینید.")


def build_application() -> Application:
    token = _get_bot_token()
    app: Application = ApplicationBuilder().token(token).concurrent_updates(True).build()

    # Slash commands
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("help", help_cmd))
    app.add_handler(CommandHandler("search", search))
    app.add_handler(CommandHandler("article", article))

    # Callback for pagination
    app.add_handler(CallbackQueryHandler(on_nav_callback, pattern=r"^nav:"))

    # Text commands in Persian and auto URL detection
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_text))

    # Fallback unknown slash command
    app.add_handler(MessageHandler(filters.COMMAND, unknown))

    return app


def main() -> None:
    app = build_application()
    LOGGER.info("Starting %s...", BOT_NAME)
    app.run_polling(drop_pending_updates=True, allowed_updates=Update.ALL_TYPES)


if __name__ == "__main__":
    try:
        main()
    except (KeyboardInterrupt, SystemExit):
        LOGGER.info("Bot stopped.") 