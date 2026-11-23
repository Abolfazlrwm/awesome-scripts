# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# inline_mute.py
from telegram import (
    InlineQueryResultArticle, InputTextMessageContent,
    InlineKeyboardButton, InlineKeyboardMarkup, Update
)
from telegram.ext import InlineQueryHandler, CallbackQueryHandler, ContextTypes
from uuid import uuid4
from plugins.db_utils import execute_query  # Make sure db_utils.py is inside plugins/

print("✅ inline_mute.py loaded")

async def inline_muted_list(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.inline_query.query.strip()
    owner_id = str(update.inline_query.from_user.id)

    print(f"🟡 Inline query received: {query} from {owner_id}")

    if not query.startswith("mutedlist"):
        return

    muted = await execute_query(
        "SELECT user_id, chat_id FROM muted_users WHERE owner_id = %s",
        (owner_id,), fetch=True
    )

    print(f"📦 Muted entries found: {muted}")

    if not muted:
        return await update.inline_query.answer([
            InlineQueryResultArticle(
                id=str(uuid4()),
                title="🔈 لیست سکوت خالی است",
                input_message_content=InputTextMessageContent("🔈 لیست کاربران بی‌صدا خالی است.")
            )
        ], cache_time=0)

    results = []
    for user in muted:
        user_id = user['user_id']
        chat_id = user['chat_id']
        title = f"🔇 {user_id} در چت {chat_id}"
        msg = f"🔇 کاربر `{user_id}` در چت `{chat_id}` بی‌صدا شده است."
        button = InlineKeyboardButton("🔈 لغو سکوت", callback_data=f"unmute:{owner_id}:{user_id}:{chat_id}")
        markup = InlineKeyboardMarkup([[button]])
        results.append(
            InlineQueryResultArticle(
                id=str(uuid4()),
                title=title,
                input_message_content=InputTextMessageContent(msg, parse_mode="Markdown"),
                reply_markup=markup
            )
        )

    await update.inline_query.answer(results, cache_time=0)

async def handle_unmute_button(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    data = query.data.split(":")

    if len(data) != 4 or data[0] != "unmute":
        return await query.answer("❌ داده نامعتبر است")

    owner_id, user_id, chat_id = data[1], data[2], data[3]
    if str(query.from_user.id) != owner_id:
        return await query.answer("⛔ فقط سازنده لیست می‌تواند لغو سکوت کند", show_alert=True)

    await execute_query(
        "DELETE FROM muted_users WHERE owner_id = %s AND user_id = %s AND chat_id = %s",
        (owner_id, user_id, chat_id), fetch=False
    )
    await query.edit_message_text(
        f"✅ کاربر `{user_id}` از چت `{chat_id}` از سکوت خارج شد.",
        parse_mode="Markdown"
    )

def get_handlers():
    return [
        InlineQueryHandler(inline_muted_list),
        CallbackQueryHandler(handle_unmute_button, pattern=r"^unmute:")
    ]
