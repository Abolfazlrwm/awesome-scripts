import os
import random
import qrcode
from io import BytesIO
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    Application,
    CommandHandler,
    CallbackQueryHandler,
    ContextTypes,
    MessageHandler,
    filters,
)
import json
import time
import datetime
import socket
from urllib.parse import urlparse

TOKEN = os.getenv("BOT_TOKEN", "YOUR_BOT_TOKEN_HERE")
ADMIN_ID = 123456789  # آیدی عددی تلگرام خودت رو اینجا بذار

USERS_FILE = 'users.json'
CONFIGS_FILE = 'configs.json'
START_TIME = time.time()
MESSAGE_COUNT = 0

DEFAULT_CONFIGS = {
    'us_vless_1': {'name': 'VLESS - USA 1 🇺🇸', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@us1.example.com:443?security=tls'},
    'us_vless_2': {'name': 'VLESS - USA 2 🇺🇸', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@us2.example.com:443?security=tls'},
    'nl_trojan_1': {'name': 'Trojan - Netherlands 1 🇳🇱', 'config': 'trojan://password@nl1.example.com:443?security=tls'},
    'ca_vless_1': {'name': 'VLESS - Canada 1 🇨🇦', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@ca1.example.com:443?security=tls'},
    'de_vless_1': {'name': 'VLESS - Germany 1 🇩🇪', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@de1.example.com:443?security=tls'},
    'ru_vless_1': {'name': 'VLESS - Russia 1 🇷🇺', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@ru1.example.com:443?security=tls'},
    'fr_trojan_1': {'name': 'Trojan - France 1 🇫🇷', 'config': 'trojan://password@fr1.example.com:443?security=tls'},
    'jp_vless_1': {'name': 'VLESS - Japan 1 🇯🇵', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@jp1.example.com:443?security=tls'},
    'uk_vless_1': {'name': 'VLESS - UK 1 🇬🇧', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@uk1.example.com:443?security=tls'},
    'sg_vless_1': {'name': 'VLESS - Singapore 1 🇸🇬', 'config': 'vless://826f524a-cea1-4e44-9b49-3381d13b7593@sg1.example.com:443?security=tls'},
}


def load_json_file(file_path: str, default_value):
    try:
        if not os.path.exists(file_path):
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(default_value, f, ensure_ascii=False, indent=2)
            return json.loads(json.dumps(default_value))
        with open(file_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except Exception:
        return json.loads(json.dumps(default_value))


def save_json_file(file_path: str, data) -> None:
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)


CONFIGS = load_json_file(CONFIGS_FILE, DEFAULT_CONFIGS)


def load_users() -> list:
    users = load_json_file(USERS_FILE, [])
    if isinstance(users, dict):
        users = list(users.values())
    return users


def save_users(users: list) -> None:
    unique_users = sorted(set(int(u) for u in users))
    save_json_file(USERS_FILE, unique_users)


def register_user(user_id: int) -> None:
    users = load_users()
    if user_id not in users:
        users.append(user_id)
        save_users(users)


def format_uptime(seconds: float) -> str:
    delta = datetime.timedelta(seconds=int(seconds))
    days = delta.days
    hours, remainder = divmod(delta.seconds, 3600)
    minutes, secs = divmod(remainder, 60)
    parts = []
    if days:
        parts.append(f"{days}d")
    if hours:
        parts.append(f"{hours}h")
    if minutes:
        parts.append(f"{minutes}m")
    parts.append(f"{secs}s")
    return ' '.join(parts)


def is_admin(user_id: int) -> bool:
    return int(user_id) == int(ADMIN_ID)


FAQS = [
    {"question": "آیا این سرویس رایگان است؟", "answer": "بله، سرویس کاملاً رایگان است و همیشه رایگان خواهد ماند."},
    {"question": "چرا سرعت کم است؟", "answer": "کاهش سرعت معمولاً به دلیل شلوغی سرور یا محدودیت ISP شماست."},
    {"question": "آیا استفاده امن است؟", "answer": "بله، از پروتکل‌های رمزنگاری پیشرفته استفاده می‌کنیم."},
]

SUBSCRIPTION_LINK = "https://your-subscription-domain.example/sub"  # لینک اشتراک واقعی خودت رو اینجا بذار


async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global MESSAGE_COUNT
    MESSAGE_COUNT += 1
    if update.effective_user:
        register_user(update.effective_user.id)

    keyboard = [
        [InlineKeyboardButton("📡 لینک اشتراک", callback_data='sublink')],
        [InlineKeyboardButton("🖥️ لیست سرورها", callback_data='servers')],
        [InlineKeyboardButton("🧰 ابزارهای کاربردی", callback_data='tools')],
        [InlineKeyboardButton("📥 دانلود کلاینت", callback_data='clients')],
        [InlineKeyboardButton("❓ سوالات متداول", callback_data='faq')],
    ]
    if update.effective_user and is_admin(update.effective_user.id):
        keyboard.append([InlineKeyboardButton("🛠️ پنل مدیریت", callback_data='admin_panel')])

    reply_markup = InlineKeyboardMarkup(keyboard)

    await update.message.reply_text(
        "🌐 به ربات آزادی‌نت خوش آمدید!\n"
        "لطفاً گزینه مورد نظر را انتخاب کنید:",
        reply_markup=reply_markup
    )


async def admin(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    if not update.effective_user or not is_admin(update.effective_user.id):
        await update.message.reply_text("⛔ شما دسترسی لازم را ندارید.")
        return
    await show_admin_panel_from_message(update)


async def show_admin_panel_from_message(update: Update) -> None:
    keyboard = [
        [InlineKeyboardButton("📊 آمار ربات", callback_data='admin_stats')],
        [InlineKeyboardButton("🧩 لیست کانفیگ‌ها", callback_data='admin_list_configs')],
        [InlineKeyboardButton("➕ افزودن کانفیگ", callback_data='admin_add_config')],
        [InlineKeyboardButton("➖ حذف کانفیگ", callback_data='admin_remove_config')],
        [InlineKeyboardButton("📣 ارسال پیام همگانی", callback_data='admin_broadcast')],
        [InlineKeyboardButton("📤 خروجی کاربران", callback_data='admin_export_users')],
        [InlineKeyboardButton("🔙 بازگشت", callback_data='back')],
    ]
    await update.message.reply_text("🛠️ پنل مدیریت:", reply_markup=InlineKeyboardMarkup(keyboard))


async def show_admin_panel(query, context: ContextTypes.DEFAULT_TYPE):
    keyboard = [
        [InlineKeyboardButton("📊 آمار ربات", callback_data='admin_stats')],
        [InlineKeyboardButton("🧩 لیست کانفیگ‌ها", callback_data='admin_list_configs')],
        [InlineKeyboardButton("➕ افزودن کانفیگ", callback_data='admin_add_config')],
        [InlineKeyboardButton("➖ حذف کانفیگ", callback_data='admin_remove_config')],
        [InlineKeyboardButton("📣 ارسال پیام همگانی", callback_data='admin_broadcast')],
        [InlineKeyboardButton("📤 خروجی کاربران", callback_data='admin_export_users')],
        [InlineKeyboardButton("🔙 بازگشت", callback_data='back')],
    ]
    await query.edit_message_text("🛠️ پنل مدیریت:", reply_markup=InlineKeyboardMarkup(keyboard))


async def button_handler(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global MESSAGE_COUNT
    MESSAGE_COUNT += 1

    query = update.callback_query
    await query.answer()
    data = query.data

    if data.startswith('config_'):
        config_id = data.split('_', 1)[1]
        await show_config(query, context, config_id)
        return
    if data.startswith('faq_'):
        faq_id = int(data.split('_')[1])
        await show_faq_detail(query, faq_id)
        return
    if data.startswith('copy_'):
        config_id = data.split('_', 1)[1]
        await copy_config_value(query, context, config_id)
        return
    if data.startswith('admin_remove_'):
        if not is_admin(query.from_user.id):
            await query.answer("⛔ دسترسی ندارید", show_alert=True)
            return
        config_id = data.split('_', 2)[2]
        await perform_remove_config(query, config_id)
        return

    handlers_simple = {
        'sublink': show_sublink,
        'servers': show_servers_menu,
        'tools': show_tools_menu,
        'clients': show_clients,
        'faq': show_faq_menu,
        'back': start_from_button,
        'copy_sublink': copy_sublink,
        'ping_test': run_ping_test,
        'dns_test': check_dns_leak,
        'ip_info': get_user_ip,
    }

    handlers_need_context = {
        'admin_panel': show_admin_panel,
        'admin_stats': admin_stats,
        'admin_list_configs': admin_list_configs,
        'admin_add_config': admin_add_config,
        'admin_remove_config': admin_remove_config,
        'admin_broadcast': admin_broadcast,
        'admin_export_users': admin_export_users,
    }

    if data in handlers_simple:
        await handlers_simple[data](query)
        return
    if data in handlers_need_context:
        if not is_admin(query.from_user.id):
            await query.answer("⛔ دسترسی ندارید", show_alert=True)
            return
        # Pass context where handlers may need it
        await handlers_need_context[data](query, context) if handlers_need_context[data] in [admin_stats, admin_list_configs, admin_add_config, admin_remove_config, admin_broadcast, admin_export_users, show_admin_panel] else await handlers_need_context[data](query)


async def start_from_button(query):
    keyboard = [
        [InlineKeyboardButton("📡 لینک اشتراک", callback_data='sublink')],
        [InlineKeyboardButton("🖥️ لیست سرورها", callback_data='servers')],
        [InlineKeyboardButton("🧰 ابزارهای کاربردی", callback_data='tools')],
        [InlineKeyboardButton("📥 دانلود کلاینت", callback_data='clients')],
        [InlineKeyboardButton("❓ سوالات متداول", callback_data='faq')],
    ]
    if is_admin(query.from_user.id):
        keyboard.append([InlineKeyboardButton("🛠️ پنل مدیریت", callback_data='admin_panel')])

    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "🌐 به ربات آزادی‌نت خوش آمدید!\n"
        "لطفاً گزینه مورد نظر را انتخاب کنید:",
        reply_markup=reply_markup
    )


async def show_sublink(query):
    keyboard = [
        [InlineKeyboardButton("📋 کپی لینک", callback_data='copy_sublink')],
        [InlineKeyboardButton("🔙 بازگشت", callback_data='back')]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        f"🔗 لینک اشتراک سرویس:\n\n"
        f"`{SUBSCRIPTION_LINK}`\n\n"
        "این لینک را در کلاینت VPN خود وارد کنید تا همه سرورها اضافه شوند.",
        parse_mode='Markdown',
        reply_markup=reply_markup
    )


async def copy_sublink(query):
    await query.answer('✅ لینک اشتراک در حافظه موقت کپی شد!', show_alert=True)


async def show_servers_menu(query):
    keyboard = []
    servers = list(CONFIGS.keys())

    for i in range(0, len(servers), 2):
        row = []
        if i < len(servers):
            row.append(InlineKeyboardButton(
                CONFIGS[servers[i]]['name'],
                callback_data=f'config_{servers[i]}'
            ))
        if i+1 < len(servers):
            row.append(InlineKeyboardButton(
                CONFIGS[servers[i+1]]['name'],
                callback_data=f'config_{servers[i+1]}'
            ))
        if row:
            keyboard.append(row)

    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data='back')])

    reply_markup = InlineKeyboardMarkup(keyboard)
    await query.edit_message_text(
        "🖥️ سرورهای موجود:",
        reply_markup=reply_markup
    )


async def show_config(query, context: ContextTypes.DEFAULT_TYPE, config_id):
    config = CONFIGS.get(config_id)
    if not config:
        await query.answer("⚠️ سرور یافت نشد!")
        return

    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr.add_data(config['config'])
    qr.make(fit=True)
    img = qr.make_image(fill_color="black", back_color="white")

    bio = BytesIO()
    bio.name = 'qrcode.png'
    img.save(bio, 'PNG')
    bio.seek(0)

    keyboard = [
        [InlineKeyboardButton("📋 کپی کانفیگ", callback_data=f'copy_{config_id}')],
        [InlineKeyboardButton("🔙 بازگشت", callback_data='servers')]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await context.bot.send_photo(
        chat_id=query.message.chat.id,
        photo=bio,
        caption=f"⚙️ {config['name']}\n\n"
                "برای اتصال سریع، QR کد را با کلاینت خود اسکن کنید.",
        reply_markup=reply_markup
    )
    await query.delete_message()


async def copy_config_value(query, context: ContextTypes.DEFAULT_TYPE, config_id: str):
    config = CONFIGS.get(config_id)
    if not config:
        await query.answer("⚠️ کانفیگ یافت نشد!")
        return
    await context.bot.send_message(
        chat_id=query.message.chat.id,
        text=f"`{config['config']}`",
        parse_mode='Markdown'
    )
    await query.answer('✅ کانفیگ ارسال شد')


async def show_tools_menu(query):
    keyboard = [
        [InlineKeyboardButton("📶 بررسی دسترسی سرورها", callback_data='ping_test')],
        [InlineKeyboardButton("🛡️ راهنمای نشت DNS", callback_data='dns_test')],
        [InlineKeyboardButton("🌍 مشاهده IP عمومی", callback_data='ip_info')],
        [InlineKeyboardButton("🔙 بازگشت", callback_data='back')]
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "🧰 ابزارهای کاربردی:",
        reply_markup=reply_markup
    )


def parse_host_port_from_config_url(config_url: str):
    try:
        parsed = urlparse(config_url)
        host = parsed.hostname
        port = parsed.port
        return host, port
    except Exception:
        return None, None


async def run_ping_test(query):
    server_entries = []
    for cfg_id, cfg in CONFIGS.items():
        host, port = parse_host_port_from_config_url(cfg['config'])
        if not host:
            server_entries.append((cfg['name'], None, None))
        else:
            server_entries.append((cfg['name'], host, port if port else 443))

    results_lines = ["⏳ در حال بررسی دسترسی سرورها..."]
    await query.edit_message_text("\n".join(results_lines))

    results_lines = []
    for name, host, port in server_entries:
        if not host:
            results_lines.append(f"⚪ {name}: قالب ناشناخته")
            continue
        start = time.perf_counter()
        status_emoji = ""
        text = ""
        try:
            with socket.create_connection((host, port), timeout=2.5):
                latency_ms = int((time.perf_counter() - start) * 1000)
                if latency_ms < 120:
                    status_emoji = "🟢"
                elif latency_ms < 250:
                    status_emoji = "🟡"
                else:
                    status_emoji = "🟠"
                text = f"{status_emoji} {name}: {latency_ms} ms"
        except Exception:
            text = f"🔴 {name}: ناممکن"
        results_lines.append(text)

    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data='tools')]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "📶 نتایج بررسی:\n\n" + "\n".join(results_lines) +
        "\n\n🟢 خوب 🟡 متوسط 🟠 کند 🔴 ناممکن",
        reply_markup=reply_markup
    )


async def check_dns_leak(query):
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data='tools')]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "🛡️ برای بررسی نشت DNS از مرورگر خود استفاده کنید:\n\n"
        "• ipleak.net\n"
        "• dnsleaktest.com\n\n"
        "پس از اتصال VPN، تست گسترده را اجرا کنید.",
        reply_markup=reply_markup
    )


async def get_user_ip(query):
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data='tools')]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "🌍 ربات به IP شما دسترسی مستقیم ندارد. برای مشاهده IP عمومی خود به لینک‌های زیر بروید:\n\n"
        "• api.ipify.org\n"
        "• whatismyipaddress.com",
        reply_markup=reply_markup
    )


async def show_clients(query):
    clients = {
        "Android": ("V2RayNG", "https://github.com/2dust/v2rayNG/releases"),
        "iOS": ("Streisand", "https://apps.apple.com/app/streisand/id6450534064"),
        "Windows": ("v2rayN", "https://github.com/2dust/v2rayN/releases"),
        "macOS": ("Hiddify Next", "https://github.com/hiddify/hiddify-next/releases"),
        "Linux": ("Qv2ray", "https://github.com/Qv2ray/Qv2ray"),
        "Router": ("Clash", "https://github.com/Dreamacro/clash"),
    }

    message = "📥 کلاینت‌های پیشنهادی:\n\n"
    for os_name, (client, url) in clients.items():
        message += f"• {os_name}: [{client}]({url})\n"

    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data='back')]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        message,
        parse_mode='Markdown',
        disable_web_page_preview=True,
        reply_markup=reply_markup
    )


async def show_faq_menu(query):
    keyboard = []
    for i, faq in enumerate(FAQS):
        keyboard.append([InlineKeyboardButton(
            f"❓ {faq['question']}",
            callback_data=f'faq_{i}'
        )])

    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data='back')])
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        "❓ سوالات متداول:",
        reply_markup=reply_markup
    )


async def show_faq_detail(query, faq_id):
    if faq_id < 0 or faq_id >= len(FAQS):
        await query.answer("⚠️ سوال یافت نشد!")
        return

    faq = FAQS[faq_id]
    keyboard = [[InlineKeyboardButton("🔙 بازگشت", callback_data='faq')]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    await query.edit_message_text(
        f"❓ {faq['question']}\n\n"
        f"💡 {faq['answer']}",
        reply_markup=reply_markup
    )


async def admin_stats(query, context: ContextTypes.DEFAULT_TYPE):
    users = load_users()
    uptime = format_uptime(time.time() - START_TIME)
    message = (
        "📊 آمار ربات:\n\n"
        f"• کاربران: {len(users)}\n"
        f"• تعداد کانفیگ‌ها: {len(CONFIGS)}\n"
        f"• پیام‌های پردازش‌شده: {MESSAGE_COUNT}\n"
        f"• زمان روشن بودن: {uptime}"
    )
    await query.edit_message_text(message, reply_markup=InlineKeyboardMarkup([
        [InlineKeyboardButton("🔙 بازگشت", callback_data='admin_panel')]
    ]))


async def admin_list_configs(query, context: ContextTypes.DEFAULT_TYPE):
    if not CONFIGS:
        text = "هیچ کانفیگی ثبت نشده است."
    else:
        lines = []
        for cfg_id, cfg in CONFIGS.items():
            lines.append(f"• {cfg['name']} ({cfg_id})")
        text = "\n".join(lines)
    await query.edit_message_text("🧩 لیست کانفیگ‌ها:\n\n" + text, reply_markup=InlineKeyboardMarkup([
        [InlineKeyboardButton("🔙 بازگشت", callback_data='admin_panel')]
    ]))


async def admin_add_config(query, context: ContextTypes.DEFAULT_TYPE):
    context.user_data['awaiting'] = 'add_config_name'
    context.user_data['new_config'] = {}
    await query.edit_message_text(
        "➕ نام نمایشی کانفیگ را ارسال کنید (مثلاً: VLESS - USA 3 🇺🇸)",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("لغو", callback_data='admin_panel')]])
    )


async def admin_remove_config(query, context: ContextTypes.DEFAULT_TYPE):
    if not CONFIGS:
        await query.edit_message_text(
            "هیچ کانفیگی برای حذف وجود ندارد.",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data='admin_panel')]])
        )
        return
    keyboard = []
    for cfg_id, cfg in CONFIGS.items():
        keyboard.append([InlineKeyboardButton(f"🗑️ {cfg['name']}", callback_data=f"admin_remove_{cfg_id}")])
    keyboard.append([InlineKeyboardButton("🔙 بازگشت", callback_data='admin_panel')])
    await query.edit_message_text("یکی را برای حذف انتخاب کنید:", reply_markup=InlineKeyboardMarkup(keyboard))


async def perform_remove_config(query, config_id: str):
    if config_id in CONFIGS:
        removed_name = CONFIGS[config_id]['name']
        del CONFIGS[config_id]
        save_json_file(CONFIGS_FILE, CONFIGS)
        await query.edit_message_text(
            f"✅ حذف شد: {removed_name}",
            reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("🔙 بازگشت", callback_data='admin_panel')]])
        )
    else:
        await query.answer("⚠️ یافت نشد")


async def admin_broadcast(query, context: ContextTypes.DEFAULT_TYPE):
    context.user_data['awaiting'] = 'broadcast_message'
    await query.edit_message_text(
        "📣 متن پیام همگانی را ارسال کنید.",
        reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("لغو", callback_data='admin_panel')]])
    )


async def admin_export_users(query, context: ContextTypes.DEFAULT_TYPE):
    users = load_users()
    content = "\n".join(str(u) for u in users)
    bio = BytesIO(content.encode('utf-8'))
    bio.name = 'users.txt'
    await context.bot.send_document(chat_id=query.message.chat.id, document=bio, filename='users.txt', caption=f"تعداد کاربران: {len(users)}")
    await query.answer("✅ فایل کاربران ارسال شد")


async def handle_text(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global MESSAGE_COUNT
    MESSAGE_COUNT += 1

    user_id = update.effective_user.id if update.effective_user else None
    if update.effective_user:
        register_user(update.effective_user.id)

    awaiting = context.user_data.get('awaiting')
    if not awaiting:
        return

    if not is_admin(user_id):
        await update.message.reply_text("⛔ فقط مدیر می‌تواند از این بخش استفاده کند.")
        context.user_data.pop('awaiting', None)
        context.user_data.pop('new_config', None)
        return

    if awaiting == 'add_config_name':
        context.user_data['new_config'] = context.user_data.get('new_config', {})
        context.user_data['new_config']['name'] = update.message.text.strip()
        context.user_data['awaiting'] = 'add_config_url'
        await update.message.reply_text(
            "لینک کانفیگ را ارسال کنید (vless://, trojan://, vmess://, ss://, ...)",
        )
        return

    if awaiting == 'add_config_url':
        config_url = update.message.text.strip()
        if not validate_config_url(config_url):
            await update.message.reply_text("❌ لینک نامعتبر است. دوباره تلاش کنید یا لغو کنید.")
            return
        name = context.user_data.get('new_config', {}).get('name', 'New Config')
        new_id = generate_config_id(name)
        CONFIGS[new_id] = {'name': name, 'config': config_url}
        save_json_file(CONFIGS_FILE, CONFIGS)
        context.user_data.pop('awaiting', None)
        context.user_data.pop('new_config', None)
        await update.message.reply_text(f"✅ با موفقیت اضافه شد: {name} ({new_id})")
        await show_admin_panel_from_message(update)
        return

    if awaiting == 'broadcast_message':
        text = update.message.text
        users = load_users()
        sent = 0
        for uid in users:
            try:
                await context.bot.send_message(chat_id=uid, text=text)
                sent += 1
            except Exception:
                pass
        context.user_data.pop('awaiting', None)
        await update.message.reply_text(f"✅ ارسال شد برای {sent} کاربر")
        await show_admin_panel_from_message(update)
        return


def validate_config_url(config_url: str) -> bool:
    allowed = ('vless://', 'vmess://', 'trojan://', 'ss://', 'ssr://', 'tuic://', 'hysteria://', 'hy2://')
    return any(config_url.startswith(p) for p in allowed)


def generate_config_id(name: str) -> str:
    base = ''.join(ch for ch in name.lower() if ch.isalnum() or ch in ('_', '-')).strip('-_')
    if not base:
        base = 'cfg'
    suffix = str(random.randint(1000, 9999))
    candidate = f"{base}_{suffix}"
    while candidate in CONFIGS:
        suffix = str(random.randint(1000, 9999))
        candidate = f"{base}_{suffix}"
    return candidate


def main() -> None:
    application = Application.builder().token(TOKEN).build()

    application.add_handler(CommandHandler("start", start))
    application.add_handler(CommandHandler("admin", admin))
    application.add_handler(CallbackQueryHandler(button_handler))
    application.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_text))

    print("🤖 ربات آزادی‌نت فعال شد...")
    application.run_polling()


if __name__ == '__main__':
    main()