# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, functions
import asyncio
import random
import sqlite3
import os

HELP = """
🎨 Random Font Name Changer 🎨

This plugin automatically changes your Telegram name with random stylish fonts from a collection of 20+ font styles.

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
📋 Commands:

⚡️ /fontname start
• Start the random font name changer
• Example: /fontname start

⚡️ /fontname stop
• Stop the name changer
• Example: /fontname stop

⚡️ /fontname set <name>
• Set your base name to be styled
• Example: /fontname set John

⚡️ /fontname interval <seconds>
• Set change interval (min 30)
• Example: /fontname interval 120

⚡️ /fontname preview
• Preview all font styles
• Example: /fontname preview

⚡️ /fontname status
• Show current settings
• Example: /fontname status

⚡️ /fontname help
• Show this help
• Example: /fontname help
"""

# Stylish font collections
FONTS = [
    ("𝒮𝓉𝓎𝓁𝒾𝓈𝒽", "𝒜𝒷𝒸𝒹𝒆𝒻ℊ𝒽𝒾𝒿𝓀𝓁𝓂𝓃ℴ𝓅𝓆𝓇𝓈𝓉𝓊𝓋𝓌𝓍𝓎𝓏"),
    ("𝕾𝖙𝖆𝖙𝖎𝖈", "𝕬𝕭𝕮𝕯𝕰𝕱𝕲𝕳𝕴𝕵𝕶𝕷𝕸𝕹𝕺𝕻𝕼𝕽𝕾𝕿𝖀𝖁𝖂𝖃𝖄𝖅"),
    ("𝔉𝔯𝔞𝔨𝔱𝔲𝔯", "𝔄𝔅ℭ𝔇𝔈𝔉𝔊ℌℑ𝔍𝔎𝔏𝔐𝔑𝔒𝔓𝔔ℜ𝔖𝔗𝔘𝔙𝔚𝔛𝔜ℨ"),
    ("Ⓒⓘⓡⓒⓛⓔⓓ", "ⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ"),
    ("🅱🅻🅾🅲🅺", "🅰🅱🅲🅳🅴🅵🅶🅷🅸🅹🅺🅻🅼🅽🅾🅿🆀🆁🆂🆃🆄🆅🆆🆇🆈🆉"),
    ("𝓕𝓪𝓷𝓬𝔂", "𝓐𝓑𝓒𝓓𝓔𝓕𝓖𝓗𝓘𝓙𝓚𝓛𝓜𝓝𝓞𝓟𝓠𝓡𝓢𝓣𝓤𝓥𝓦𝓧𝓨𝓩"),
    ("𝔻𝕠𝕦𝕓𝕝𝕖", "𝔸𝔹ℂ𝔻𝔼𝔽𝔾ℍ𝕀𝕁𝕂𝕃𝕄ℕ𝕆ℙℚℝ𝕊𝕋𝕌𝕍𝕎𝕏𝕐ℤ"),
    ("🄵🄸🄻🄻🄴🄳", "🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉"),
    ("🇸 🇹 🇦 🇷 🇸", "🇦 🇧 🇨 🇩 🇪 🇫 🇬 🇭 🇮 🇯 🇰 🇱 🇲 🇳 🇴 🇵 🇶 🇷 🇸 🇹 🇺 🇻 🇼 🇽 🇾 🇿"),
    ("𝕋𝕙𝕚𝕔𝕜", "𝔸𝔹ℂ𝔻𝔼𝔽𝔾ℍ𝕀𝕁𝕂𝕃𝕄ℕ𝕆ℙℚℝ𝕊𝕋𝕌𝕍𝕎𝕏𝕐ℤ"),
    ("ⒻⒾⒺⓁⒹⓈ", "ⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ"),
    ("𝘚𝘢𝘯𝘴", "𝘈𝘉𝘊𝘋𝘌𝘍𝘎𝘏𝘐𝘑𝘒𝘓𝘔𝘕𝘖𝘗𝘘𝘙𝘚𝘛𝘜𝘝𝘞𝘟𝘠𝘡"),
    ("𝙎𝙖𝙣𝙨 𝘽𝙤𝙡𝙙", "𝘼𝘽𝘾𝘿𝙀𝙁𝙂𝙃𝙄𝙅𝙆𝙇𝙈𝙉𝙊𝙋𝙌𝙍𝙎𝙏𝙐𝙑𝙒𝙓𝙔𝙕"),
    ("𝚂𝚊𝚗𝚜 𝙼𝚘𝚗𝚘", "𝙰𝙱𝙲𝙳𝙴𝙵𝙶𝙷𝙸𝙹𝙺𝙻𝙼𝙽𝙾𝙿𝚀𝚁𝚂𝚃𝚄𝚅𝚆𝚇𝚈𝚉"),
    ("ꜱᴍᴀʟʟ ᴄᴀᴘꜱ", "ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘǫʀꜱᴛᴜᴠᴡxʏᴢ"),
    ("🅂🅀🅄🄰🅁🄴", "🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉"),
    ("𝕊𝕢𝕦𝕒𝕣𝕖", "𝔸𝔹ℂ𝔻𝔼𝔽𝔾ℍ𝕀𝕁𝕂𝕃𝕄ℕ𝕆ℙℚℝ𝕊𝕋𝕌𝕍𝕎𝕏𝕐ℤ"),
    ("𝐁𝐨𝐥𝐝", "𝐀𝐁𝐂𝐃𝐄𝐅𝐆𝐇𝐈𝐉𝐊𝐋𝐌𝐍𝐎𝐏𝐐𝐑𝐒𝐓𝐔𝐕𝐖𝐗𝐘𝐙"),
    ("𝐼𝑡𝑎𝑙𝑖𝑐", "𝐴𝐵𝐶𝐷𝐸𝐹𝐺𝐻𝐼𝐽𝐾𝐿𝑀𝑁𝑂𝑃𝑄𝑅𝑆𝑇𝑈𝑉𝑊𝑋𝑌𝑍"),
    ("𝑩𝒐𝒍𝒅 𝑰𝒕𝒂𝒍𝒊𝒄", "𝑨𝑩𝑪𝑫𝑬𝑭𝑮𝑯𝑰𝑱𝑲𝑳𝑴𝑵𝑶𝑷𝑸𝑹𝑺𝑻𝑼𝑽𝑾𝑿𝒀𝒁"),
    ("𝔐𝔬𝔡𝔢𝔯𝔫", "𝔄𝔅ℭ𝔇𝔈𝔉𝔊ℌℑ𝔍𝔎𝔏𝔐𝔑𝔒𝔓𝔔ℜ𝔖𝔗𝔘𝔙𝔚𝔛𝔜ℨ"),
    ("ℭ𝔲𝔯𝔰𝔦𝔳𝔢", "𝔄𝔅ℭ𝔇𝔈𝔉𝔊ℌℑ𝔍𝔎𝔏𝔐𝔑𝔒𝔓𝔔ℜ𝔖𝔗𝔘𝔙𝔚𝔛𝔜ℨ")
]

class RandomFontNamePlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.db_path = "data/fontname.db"
        self._init_db()
        self.task = None
        self.running = False
        self.config = self._load_config()
        
    def _init_db(self):
        os.makedirs(os.path.dirname(self.db_path), exist_ok=True)
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                CREATE TABLE IF NOT EXISTS fontname_config (
                    owner_id TEXT PRIMARY KEY,
                    base_name TEXT DEFAULT '',
                    update_interval INTEGER DEFAULT 120,
                    last_font_index INTEGER DEFAULT -1
                )
            """)
            conn.commit()

    def _load_config(self):
        with sqlite3.connect(self.db_path) as conn:
            cursor = conn.execute(
                "SELECT base_name, update_interval, last_font_index "
                "FROM fontname_config WHERE owner_id = ?", 
                (self.owner_id,)
            )
            result = cursor.fetchone()
            
            if not result:
                default_config = {
                    'base_name': '',
                    'update_interval': 120,
                    'last_font_index': -1
                }
                conn.execute(
                    "INSERT INTO fontname_config VALUES (?, ?, ?, ?)",
                    (self.owner_id, '', 120, -1)
                )
                conn.commit()
                return default_config
            return {
                'base_name': result[0],
                'update_interval': result[1],
                'last_font_index': result[2]
            }

    def _save_config(self):
        with sqlite3.connect(self.db_path) as conn:
            conn.execute(
                "UPDATE fontname_config SET base_name = ?, update_interval = ?, "
                "last_font_index = ? WHERE owner_id = ?",
                (
                    self.config['base_name'],
                    self.config['update_interval'],
                    self.config['last_font_index'],
                    self.owner_id
                )
            )
            conn.commit()

    def _convert_to_font(self, text, font_index):
        """Convert text to selected font style"""
        if font_index < 0 or font_index >= len(FONTS):
            font_index = random.randint(0, len(FONTS)-1)
        
        _, font_chars = FONTS[font_index]
        result = []
        
        for char in text:
            if 'A' <= char <= 'Z':
                result.append(font_chars[ord(char) - ord('A')])
            elif 'a' <= char <= 'z':
                result.append(font_chars[ord(char) - ord('a')].lower())
            else:
                result.append(char)
                
        return ''.join(result), font_index

    async def _update_name(self):
        try:
            while self.running:
                if not self.config['base_name']:
                    await asyncio.sleep(self.config['update_interval'])
                    continue
                
                # Get random font (different from last one)
                font_index = random.choice([
                    i for i in range(len(FONTS)) 
                    if i != self.config['last_font_index']
                ])
                
                styled_name, font_index = self._convert_to_font(
                    self.config['base_name'],
                    font_index
                )
                
                try:
                    await self.client(functions.account.UpdateProfileRequest(
                        first_name=styled_name
                    ))
                    self.config['last_font_index'] = font_index
                    self._save_config()
                except Exception as e:
                    print(f"Error updating name: {e}")
                
                await asyncio.sleep(self.config['update_interval'])
        except asyncio.CancelledError:
            pass
        except Exception as e:
            print(f"Random font name task error: {e}")

    async def start_font_name(self):
        if self.running:
            return
        
        if not self.config['base_name']:
            return
            
        self.running = True
        self.task = asyncio.create_task(self._update_name())

    async def stop_font_name(self):
        if not self.running:
            return
        
        self.running = False
        self.task.cancel()
        try:
            await self.task
        except asyncio.CancelledError:
            pass
        self.task = None

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/fontname\s+(.+)'))
        async def fontname_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            args = event.pattern_match.group(1).split(' ', 1)
            command = args[0].lower()
            
            if command == 'start':
                if not self.config['base_name']:
                    await event.reply("❌ Please set a base name first with /fontname set <name>")
                    return
                    
                await self.start_font_name()
                await event.reply("✅ Random font name changer started!")
                
            elif command == 'stop':
                await self.stop_font_name()
                await event.reply("✅ Random font name changer stopped!")
                
            elif command == 'set' and len(args) > 1:
                self.config['base_name'] = args[1]
                self._save_config()
                await event.reply(f"✅ Base name set to: {args[1]}")
                
            elif command == 'interval' and len(args) > 1:
                try:
                    interval = int(args[1])
                    if interval < 30:
                        await event.reply("❌ Interval must be at least 30 seconds")
                    else:
                        self.config['update_interval'] = interval
                        self._save_config()
                        await event.reply(f"✅ Update interval set to {interval} seconds")
                except ValueError:
                    await event.reply("❌ Interval must be a number")
                    
            elif command == 'preview':
                if not self.config['base_name']:
                    await event.reply("❌ Please set a base name first with /fontname set <name>")
                    return
                    
                preview_text = "🖋 Font Previews:\n\n"
                for i, (font_name, _) in enumerate(FONTS):
                    styled, _ = self._convert_to_font(self.config['base_name'], i)
                    preview_text += f"{i}. {font_name}: {styled}\n"
                    
                await event.reply(preview_text)
                
            elif command == 'status':
                status = (
                    f"⚙️ Random Font Name Status:\n\n"
                    f"• Running: {'Yes' if self.running else 'No'}\n"
                    f"• Base Name: {self.config['base_name'] or 'Not set'}\n"
                    f"• Update Interval: {self.config['update_interval']} seconds\n"
                    f"• Available Fonts: {len(FONTS)}\n"
                )
                await event.reply(status)
                
            elif command == 'help':
                await event.reply(HELP)
                
            else:
                await event.reply("❌ Invalid command. Use /fontname help for instructions.")

        @self.client.on(events.NewMessage(pattern=r'^/fontname$'))
        async def fontname_help_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)

    async def start(self):
        """Load previous running state on startup"""
        if self.config['base_name']:
            await self.start_font_name()

    async def stop(self):
        """Save state when plugin stops"""
        await self.stop_font_name()
        self._save_config()