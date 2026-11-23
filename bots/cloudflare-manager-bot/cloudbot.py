# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import base64
import requests
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    ApplicationBuilder, CommandHandler, CallbackQueryHandler,
    ContextTypes, MessageHandler, filters, ConversationHandler
)

TELEGRAM_BOT_TOKEN = '******' #توکنت
CLOUDFLARE_API_TOKEN = '*****'  #ایپی ای کلودفلر
ID = 0  #ایدی عددیت

HEADERS = {
    "Authorization": f"Bearer {CLOUDFLARE_API_TOKEN}",
    "Content-Type": "application/json"
}

logging.basicConfig(level=logging.INFO)

(
    ADD_RECORD_TYPE, ADD_RECORD_NAME, ADD_RECORD_CONTENT,
    EDIT_RECORD_CONTENT, ADD_ZONE_NAME
) = range(5)

user_data_store = {}

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user_id = update.effective_user.id
    if user_id != ID:
        return

    await update.callback_query.answer() if update.callback_query else None
    await (update.callback_query.edit_message_text if update.callback_query else update.message.reply_text)(
        "𝚆𝚊𝚒𝚝..."
    )

    response = requests.get("https://api.cloudflare.com/client/v4/zones", headers=HEADERS)

    if response.status_code != 200:
        await update.message.reply_text("خطا در دریافت دامنه ها برای رفع خطا به پشتیبانی پیام دهید") if update.message else update.callback_query.edit_message_text("خطا در دریافت دامنه ها برای رفع خطا به پشتیبانی پیام دهید")
        return

    zones = response.json().get("result", [])
    if not zones:
        await update.message.reply_text("هیچ دامنه‌ای یافت نشد.") if update.message else update.callback_query.edit_message_text("هیچ دامنه‌ای یافت نشد.")
        return

    keyboard = []
    for zone in zones:
        zone_id = zone['id']
        keyboard.append([
            InlineKeyboardButton(zone['name'], callback_data=f"zone_{zone_id}"),
            InlineKeyboardButton("𝚜𝚝𝚊𝚝𝚞𝚜 ♻️", callback_data=f"status_{zone_id}")
        ])
    keyboard.append([InlineKeyboardButton("موقت غیرفعال add domain", callback_data="add_zone")])

    import base64

    api_token_preview = CLOUDFLARE_API_TOKEN[:8] + "..."
    encoded_message = "Q2xvdWRmbGFyZSBib3QgaXMgcnVubmluZy4uLg0KDQrwn5G+IHwgQVBJIENsb3VkRmxhcmUgOiBzdWNjZXNzZnVsbHkgIA0K8J+UlyB8IEFQSSDDl8OXIDoge2FwaV90b2tlbl9wcmV2aWV3fSAgDQrimbvvuI8gfCBEb21haW4gYWRkZWQgOiB7em9uZV9jb3VudH0gIA0K8J+OqyB8IElEIDoge3VzZXJfaWR9DQoNCkNvZGVkIDogQEluZm9ybWFpb25NRUhJDQpEb21haW4gbGlzdF4="
    decoded_template = base64.b64decode(encoded_message).decode("utf-8", errors="ignore")

    intro_message = decoded_template.format(
    api_token_preview=api_token_preview,
    user_id=user_id,
    zone_count=len(zones)
)

    await (update.message.reply_text if update.message else update.callback_query.edit_message_text)(
    intro_message, reply_markup=InlineKeyboardMarkup(keyboard)
)

async def check_zone_status(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    zone_id = query.data.split("_")[1]

    resp = requests.get(f"https://api.cloudflare.com/client/v4/zones/{zone_id}", headers=HEADERS)
    if resp.status_code != 200:
        await query.edit_message_text("خطا❌ در دریافت دامنه ها برای رفع خطا به پشتیبانی پیام دهید")
        return

    data = resp.json().get("result", {})
    name = data.get("name")
    status = data.get("status")
    paused = data.get("paused")
    type_ = data.get("type")
    ns_cloudflare = data.get("name_servers", [])
    ns_current = data.get("original_name_servers", ["null"])

    msg = f"""
| 𝙳𝚘𝚖𝚊𝚒𝚗 : `{name}`
| 𝚂𝚝𝚊𝚝𝚞𝚜 : {'𝙰𝚌𝚝𝚒𝚟𝚎 ✅' if status == 'active' else '𝙽𝚘𝚝 𝙰𝚌𝚝𝚒𝚟𝚎 ❌'}
| 𝙸𝙳 𝙳𝚘𝚖𝚊𝚒𝚗 : `{zone_id}`
| 𝙿𝚊𝚞𝚜𝚎𝚍 : {'𝚢𝚎𝚜' if paused else '𝚗𝚘'}
| 𝙽𝚊𝚖𝚎𝚜𝚎𝚛𝚟𝚎𝚛𝚜 𝙲𝙻𝙾𝙸𝙳.. :
{', '.join(ns_cloudflare)}
| 𝙽𝚊𝚖𝚎𝚜𝚎𝚛𝚟𝚎𝚛𝚜 𝙽𝙾𝚆 :
{', '.join(ns_current) if ns_current else 'null'}
"""

    keyboard = [[InlineKeyboardButton("⬆️ 𝙱𝙰𝙲𝙺", callback_data="back_to_zones")]]
    await query.edit_message_text(msg, parse_mode="Markdown", reply_markup=InlineKeyboardMarkup(keyboard))

async def add_zone_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    await query.edit_message_text("𝚂𝚊𝚗𝚍 𝚖𝚎 𝚗𝚊𝚖𝚎 𝚍𝚘𝚖𝚊𝚒𝚗 :")
    return ADD_ZONE_NAME

async def add_zone_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    domain = update.message.text.strip()
    data = {
        "name": domain,
        "jump_start": True
    }
    resp = requests.post("https://api.cloudflare.com/client/v4/zones", headers=HEADERS, json=data)
    if resp.status_code == 200:
        await update.message.reply_text(f"𝙳𝚘𝚖𝚊𝚒𝚗`{domain}`𝚂𝚞𝚌𝚌𝚎𝚜𝚜𝚏𝚞𝚕𝚕𝚢 𝚊𝚍𝚍𝚎𝚍.✅", parse_mode="Markdown")
    else:
        error_msg = resp.json().get("errors", [{"message": "خطای ناشناخته برای رفع خطا به پشتیبانی پیام دهید"}])[0]["message"]
        await update.message.reply_text(f"خطا ❌در افزودن دامنه برای رفع خطا به پشتیبانی پیام دهید {error_msg}")
    return ConversationHandler.END

async def zone_selected(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    zone_id = query.data.split("_")[1]
    context.user_data['zone_id'] = zone_id

    dns_resp = requests.get(f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records", headers=HEADERS)
    if dns_resp.status_code != 200:
        await query.edit_message_text("خطا ❌در دریافت ریکورد های دیگران آس برای رفع خطا به  پشتیبانی پیام دهید")
        return

    records = dns_resp.json().get("result", [])
    context.user_data['records'] = records
    msg = "رکوردهای DNS برای این دامنه:\n\n"
    keyboard = []
    for record in records:
        msg += f"{record['type']} - {record['name']} - {record['content']}\n"
        if record['type'] in ['A', 'TXT']:
            keyboard.append([
                InlineKeyboardButton(f"𝙴𝚍𝚒𝚝.. {record['type']} - {record['name']}", callback_data=f"edit_{record['id']}")
            ])

    keyboard.append([
        InlineKeyboardButton("𝙰𝚍𝚍 𝚛𝚎𝚌𝚘𝚛𝚍 𝙰", callback_data="add_A"),
        InlineKeyboardButton("𝙰𝚍𝚍 𝚛𝚎𝚌𝚘𝚛𝚍 𝚃𝚇𝚃", callback_data="add_TXT")
    ])
    keyboard.append([
        InlineKeyboardButton("𝙳𝚎𝚕𝚎𝚝𝚎 𝚛𝚎𝚌𝚘𝚛𝚍", callback_data="delete_record")
    ])
    keyboard.append([InlineKeyboardButton("⬆️ 𝙱𝙰𝙲𝙺", callback_data="back_to_zones")
    ])

    await query.edit_message_text(msg, reply_markup=InlineKeyboardMarkup(keyboard))

async def delete_record_menu(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    zone_id = context.user_data.get('zone_id')
    dns_resp = requests.get(f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records", headers=HEADERS)
    records = dns_resp.json().get("result", [])
    keyboard = [
        [InlineKeyboardButton(f"{r['type']} - {r['name']}", callback_data=f"confirm_delete_{r['id']}")] for r in records
    ]
    await query.edit_message_text("برای حذف، روی رکورد موردنظر کلیک کنید:", reply_markup=InlineKeyboardMarkup(keyboard))

async def confirm_delete(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    record_id = query.data.split("_")[2]
    zone_id = context.user_data.get('zone_id')
    delete_resp = requests.delete(f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records/{record_id}", headers=HEADERS)
    await query.edit_message_text("𝚁𝚎𝚌𝚘𝚛𝚍 𝚜𝚞𝚌𝚌𝚎𝚜𝚜𝚏𝚞𝚕𝚕𝚢 𝚍𝚎𝚕𝚎𝚝𝚎𝚍 ✅" if delete_resp.status_code == 200 else "خطا ❌در حذف ریکورد برای رفع خطا به پشتیبانی پیام دهید")

async def add_record_entry(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    context.user_data['new_type'] = query.data.split("_")[1]
    await query.edit_message_text("𝚜𝚊𝚗𝚍 𝚖𝚎 𝚛𝚎𝚌𝚘𝚛𝚍 𝚗𝚊𝚖𝚎 ( 𝚠𝚠𝚠 , @ , 𝚜𝚞𝚋 ):")
    return ADD_RECORD_NAME

async def add_record_name(update: Update, context: ContextTypes.DEFAULT_TYPE):
    context.user_data['new_name'] = update.message.text.strip()
    await update.message.reply_text("𝙽𝚘𝚠 𝚜𝚎𝚗𝚍 𝚝𝚑𝚎 𝙸𝙿 𝚛𝚎𝚌𝚘𝚛𝚍 ( 𝟷𝟿𝟸.𝟷𝟼𝟾.𝟷.𝟷 ):")
    return ADD_RECORD_CONTENT

async def add_record_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    zone_id = context.user_data['zone_id']
    r_type = context.user_data['new_type']
    name = context.user_data['new_name']
    content = update.message.text.strip()

    body = {"type": r_type, "name": name, "content": content, "ttl": 1}
    resp = requests.post(f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records", headers=HEADERS, json=body)
    await update.message.reply_text("𝚁𝚎𝚌𝚘𝚛𝚍 𝚊𝚍𝚍𝚎𝚍 𝚜𝚞𝚌𝚌𝚎𝚜𝚜𝚏𝚞𝚕𝚕𝚢 ✅" if resp.status_code == 200 else "خطا ❌در افزودن ریکورد برای رفع خطا به پشتیبانی پیام دهید")
    return ConversationHandler.END

async def edit_record_entry(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()
    record_id = query.data.split("_")[1]
    context.user_data['edit_record_id'] = record_id
    await query.edit_message_text("𝚂𝚎𝚗𝚍 𝚝𝚑𝚎 𝙽𝙴𝚆 𝚛𝚎𝚌𝚘𝚛𝚍 𝚗𝚊𝚖𝚎:")
    return EDIT_RECORD_CONTENT

async def edit_record_content(update: Update, context: ContextTypes.DEFAULT_TYPE):
    zone_id = context.user_data['zone_id']
    record_id = context.user_data['edit_record_id']
    new_content = update.message.text.strip()
    record = next((r for r in context.user_data.get('records', []) if r['id'] == record_id), None)
    if not record:
        await update.message.reply_text("❌ رکورد یافت نشد.")
        return ConversationHandler.END

    updated = {"type": record['type'], "name": record['name'], "content": new_content, "ttl": 1}
    resp = requests.put(f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records/{record_id}", headers=HEADERS, json=updated)
    await update.message.reply_text("𝚁𝚎𝚌𝚘𝚛𝚍 𝚜𝚞𝚌𝚌𝚎𝚜𝚜𝚏𝚞𝚕𝚕𝚢 𝚎𝚍𝚒𝚝𝚎𝚍 ✅" if resp.status_code == 200 else "خطا ❌در ویرایش ریکورد برای رفع خطا به پشتیبانی پیام دهید")
    return ConversationHandler.END

if __name__ == '__main__':
    app = ApplicationBuilder().token(TELEGRAM_BOT_TOKEN).build()

    conv_handler = ConversationHandler(
        entry_points=[
            CallbackQueryHandler(add_record_entry, pattern="^add_"),
            CallbackQueryHandler(edit_record_entry, pattern="^edit_"),
            CallbackQueryHandler(add_zone_start, pattern="^add_zone$")
        ],
        states={
            ADD_RECORD_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_record_name)],
            ADD_RECORD_CONTENT: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_record_content)],
            EDIT_RECORD_CONTENT: [MessageHandler(filters.TEXT & ~filters.COMMAND, edit_record_content)],
            ADD_ZONE_NAME: [MessageHandler(filters.TEXT & ~filters.COMMAND, add_zone_name)],
        },
        fallbacks=[]
    )

    app.add_handler(CommandHandler("start", start))
    app.add_handler(conv_handler)
    app.add_handler(CallbackQueryHandler(zone_selected, pattern="^zone_"))
    app.add_handler(CallbackQueryHandler(delete_record_menu, pattern="^delete_record$"))
    app.add_handler(CallbackQueryHandler(confirm_delete, pattern="^confirm_delete_"))
    app.add_handler(CallbackQueryHandler(check_zone_status, pattern="^status_"))
    app.add_handler(CallbackQueryHandler(start, pattern="^back_to_zones$"))

    print("STARTED")
    app.run_polling()