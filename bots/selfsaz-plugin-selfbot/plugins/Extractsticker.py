# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types, functions
import json
import os
import asyncio
from datetime import datetime
import logging
import zipfile
import shutil
import sqlite3

HELP = """
🎭 **استخراج کننده استیکر** 🎭

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **قابلیت‌های کلیدی**:
• استخراج تمام استیکرهای یک پک با یک دستور ساده
• ذخیره مشخصات استیکرها (شامل ایموجی‌ها) در فایل JSON
• ذخیره تصاویر استیکرها در پوشه مجزا
• ارسال فایل ZIP حاوی تمامی استیکرها و اطلاعات
• امکان استخراج با ارسال لینک یا فروارد استیکر
• تشخیص استیکرهای قبلاً استخراج شده و ارسال مستقیم آنها

▬▬▬▬▬▬▬▬▬▬▬▬
🎯 **دستورات اصلی**:

**انگلیسی:**
  `/extractpack [لینک استیکر]` ➤ استخراج کل پک با لینک  
  `/extractpack` ➤ استخراج پک با ریپلای روی استیکر  

**فارسی:**
  `استخراج پک [لینک استیکر]` ➤ استخراج کل پک با لینک  
  `استخراج پک` ➤ استخراج پک با ریپلای روی استیکر  

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **نمونه اجرا**:
1. ریپلای روی یک استیکر از پک مورد نظر  
2. ارسال دستور:  
   `/extractpack` یا `استخراج پک`  
3. تمام استیکرهای پک همراه با مشخصات آنها استخراج می‌شود!  
4. فایل ZIP حاوی همه استیکرها و اطلاعات ارسال می‌شود

⚠️ **نکات مهم**:
- خروجی در پوشه `stickers/[نام پک]` ذخیره می‌شود  
- فایل `info.json` شامل تمام اطلاعات پک و استیکرها است  
- فایل ZIP همه موارد را در یک آرشیو فشرده قرار می‌دهد  
- استیکرهای قبلاً استخراج شده بدون دانلود مجدد ارسال می‌شوند
"""

class StickerExtractorPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.logger = logging.getLogger(__name__)
        # Create stickers directory if it doesn't exist
        os.makedirs("stickers", exist_ok=True)
        # Initialize database
        self.db_path = os.path.join("stickers", "sticker_database.db")
        self.init_database()

    def init_database(self):
        """Initialize the SQLite database for storing sticker pack information"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        # Create tables if they don't exist
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS sticker_packs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            short_name TEXT UNIQUE,
            title TEXT,
            count INTEGER,
            is_animated BOOLEAN,
            is_video BOOLEAN,
            official BOOLEAN,
            extraction_date TEXT,
            zip_path TEXT
        )
        ''')
        
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS stickers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pack_id INTEGER,
            file_id TEXT,
            access_hash TEXT,
            emoji TEXT,
            filename TEXT,
            file_path TEXT,
            file_size INTEGER,
            mime_type TEXT,
            width INTEGER,
            height INTEGER,
            is_mask BOOLEAN,
            mask_coords TEXT,
            FOREIGN KEY (pack_id) REFERENCES sticker_packs (id)
        )
        ''')
        
        conn.commit()
        conn.close()

    def is_pack_in_database(self, short_name):
        """Check if a sticker pack is already in the database"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute("SELECT id, zip_path FROM sticker_packs WHERE short_name = ?", (short_name,))
        result = cursor.fetchone()
        
        conn.close()
        return result

    def add_pack_to_database(self, pack_data, zip_path):
        """Add a sticker pack to the database"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        try:
            # Insert pack data
            cursor.execute('''
            INSERT INTO sticker_packs (short_name, title, count, is_animated, is_video, official, extraction_date, zip_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ''', (
                pack_data["short_name"],
                pack_data["title"],
                pack_data["count"],
                pack_data["is_animated"],
                pack_data["is_video"],
                pack_data["official"],
                pack_data["extracted_date"],
                zip_path
            ))
            
            pack_id = cursor.lastrowid
            
            # Insert sticker data
            for sticker in pack_data["stickers"]:
                mask_coords = json.dumps(sticker.get("mask_coords")) if sticker.get("mask_coords") else None
                
                cursor.execute('''
                INSERT INTO stickers (pack_id, file_id, access_hash, emoji, filename, file_path, file_size, mime_type, width, height, is_mask, mask_coords)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    pack_id,
                    sticker["file_id"],
                    sticker["access_hash"],
                    sticker["emoji"],
                    sticker["filename"],
                    os.path.join(os.path.dirname(zip_path), sticker["filename"]),
                    sticker["file_size"],
                    sticker["mime_type"],
                    sticker["width"],
                    sticker["height"],
                    sticker["is_mask"],
                    mask_coords
                ))
            
            conn.commit()
            return True
        except Exception as e:
            self.logger.error(f"Error adding pack to database: {e}")
            conn.rollback()
            return False
        finally:
            conn.close()

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/extractpack|استخراج\s+پک)(?:\s+(.+))?$'))
        async def extract_sticker_pack(event):
            if str(event.sender_id) != self.owner_id:
                return

            # Check if command is a reply to a sticker or contains a link
            input_str = event.pattern_match.group(1)
            sticker_set = None
            
            # Status message
            status_msg = await event.reply("🔍 در حال پردازش درخواست...")
            
            try:
                # Case 1: Reply to a sticker
                if event.is_reply:
                    reply_msg = await event.get_reply_message()
                    if not reply_msg.sticker:
                        await status_msg.edit("❌ پیام ریپلای شده یک استیکر نیست!")
                        return
                    if not hasattr(reply_msg, 'document') or not reply_msg.document:
                        await status_msg.edit("❌ اطلاعات سند استیکر یافت نشد!")
                        return
                    sticker_attr = reply_msg.document.attributes
                    sticker_set_attr = next((attr for attr in sticker_attr 
                                        if isinstance(attr, types.DocumentAttributeSticker)), None)
                    if not sticker_set_attr or not sticker_set_attr.stickerset:
                        await status_msg.edit("❌ این استیکر بخشی از یک پک نیست!")
                        return
                    sticker_set = sticker_set_attr.stickerset
                
                # Case 2: Link provided
                elif input_str:
                    # Try to parse link as a sticker set
                    if '/' in input_str:
                        parts = input_str.strip('/').split('/')
                        if len(parts) >= 2:
                            if 'addstickers' in parts:
                                short_name = parts[-1]
                                sticker_set = types.InputStickerSetShortName(short_name=short_name)
                            elif 't.me/addstickers' in input_str:
                                short_name = parts[-1]
                                sticker_set = types.InputStickerSetShortName(short_name=short_name)
                
                if not sticker_set:
                    await status_msg.edit("❌ لطفا یک استیکر را ریپلای کنید یا لینک معتبر پک استیکر را وارد کنید!")
                    return
                
                # Get sticker pack info
                await status_msg.edit("📥 در حال دریافت اطلاعات پک استیکر...")
                sticker_pack = await self.client(functions.messages.GetStickerSetRequest(
                    stickerset=sticker_set,
                    hash=0
                ))
                
                # Check if pack is already in database
                pack_short_name = sticker_pack.set.short_name
                existing_pack = self.is_pack_in_database(pack_short_name)
                
                if existing_pack and os.path.exists(existing_pack[1]):
                    pack_id, zip_path = existing_pack
                    await status_msg.edit("🔍 این پک استیکر قبلاً استخراج شده است. در حال ارسال...")
                    
                    # Send the existing ZIP file
                    pack_title = sticker_pack.set.title
                    total_stickers = sticker_pack.set.count
                    
                    caption = (
                        f"📦 پک استیکر: **{pack_title}**\n"
                        f"🔢 تعداد: **{total_stickers}** استیکر\n"
                        f"📅 تاریخ استخراج: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
                        f"🔄 استفاده از نسخه قبلی استخراج شده"
                    )
                    
                    await self.client.send_file(
                        event.chat_id,
                        zip_path,
                        caption=caption,
                        reply_to=event.id
                    )
                    
                    await status_msg.edit(
                        f"✅ ارسال کامل شد!\n"
                        f"📁 نام پک: **{pack_title}**\n"
                        f"🔢 تعداد استیکر: **{total_stickers}**\n"
                        f"📦 فایل ZIP از پایگاه داده ارسال شد."
                    )
                    return
                
                # If not in database, continue with extraction
                # Create directory for this pack
                pack_title = sticker_pack.set.title
                safe_title = "".join(c if c.isalnum() else "_" for c in pack_title)
                pack_dir = os.path.join("stickers", safe_title)
                
                # Clean directory if exists
                if os.path.exists(pack_dir):
                    shutil.rmtree(pack_dir)
                os.makedirs(pack_dir, exist_ok=True)
                
                # Determine sticker types based on documents
                is_animated = False
                is_video = False
                
                # Check the first few stickers to determine pack type
                for doc in sticker_pack.documents[:5]:
                    if doc.mime_type == 'application/x-tgsticker':
                        is_animated = True
                    elif doc.mime_type == 'video/webm':
                        is_video = True
                        
                # Prepare JSON data
                pack_data = {
                    "title": pack_title,
                    "short_name": pack_short_name,
                    "count": sticker_pack.set.count,
                    "is_animated": is_animated,
                    "is_video": is_video,
                    "extracted_date": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                    "stickers": []
                }
                
                try:
                    pack_data["official"] = bool(getattr(sticker_pack.set, "official", False))
                except (AttributeError, TypeError):
                    pack_data["official"] = False
                
                total_stickers = len(sticker_pack.documents)
                await status_msg.edit(f"🔄 استخراج {total_stickers} استیکر آغاز شد...")
                
                for i, document in enumerate(sticker_pack.documents):
                    if i % 5 == 0 or i == total_stickers - 1:
                        await status_msg.edit(f"🔄 استخراج استیکر {i+1}/{total_stickers}...")
                    
                    sticker_attr = document.attributes
                    sticker_emoji = ""
                    sticker_mask = False
                    sticker_mask_coords = None
                    sticker_width = 512
                    sticker_height = 512
                    sticker_filename = f"sticker_{i+1}"
                    
                    for attr in sticker_attr:
                        if isinstance(attr, types.DocumentAttributeSticker):
                            sticker_emoji = attr.alt
                            sticker_mask = bool(attr.mask)
                            sticker_mask_coords = attr.mask_coords
                        elif isinstance(attr, types.DocumentAttributeImageSize):
                            sticker_width = attr.w
                            sticker_height = attr.h
                        elif isinstance(attr, types.DocumentAttributeFilename):
                            sticker_filename = os.path.splitext(attr.file_name)[0]
                    
                    file_ext = "webp"
                    if document.mime_type == "video/webm":
                        file_ext = "webm"
                    elif document.mime_type == "application/x-tgsticker":
                        file_ext = "tgs"
                        
                    filename = f"{sticker_filename}_{i+1}.{file_ext}"
                    file_path = os.path.join(pack_dir, filename)
                    
                    try:
                        await self.client.download_media(document, file_path)
                        
                        sticker_data = {
                            "id": i + 1,
                            "file_id": str(document.id),
                            "access_hash": str(document.access_hash),
                            "emoji": sticker_emoji,
                            "filename": filename,
                            "file_size": document.size,
                            "mime_type": document.mime_type,
                            "width": sticker_width,
                            "height": sticker_height,
                            "is_mask": sticker_mask
                        }
                        
                        if sticker_mask_coords:
                            sticker_data["mask_coords"] = {
                                "n": sticker_mask_coords.n,
                                "x": sticker_mask_coords.x,
                                "y": sticker_mask_coords.y,
                                "zoom": sticker_mask_coords.zoom
                            }
                            
                        pack_data["stickers"].append(sticker_data)
                    except Exception as e:
                        self.logger.error(f"Error downloading sticker: {e}")
                        continue
                
                json_path = os.path.join(pack_dir, "info.json")
                with open(json_path, "w", encoding="utf-8") as f:
                    json.dump(pack_data, f, ensure_ascii=False, indent=2)
                
                readme_path = os.path.join(pack_dir, "README.md")
                with open(readme_path, "w", encoding="utf-8") as f:
                    f.write(f"# {pack_title} Sticker Pack\n\n")
                    f.write(f"- **Short Name:** {pack_short_name}\n")
                    f.write(f"- **Stickers Count:** {total_stickers}\n")
                    f.write(f"- **Type:** {'Animated' if is_animated else 'Video' if is_video else 'Static'}\n")
                    f.write(f"- **Extracted On:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n\n")
                    f.write("## Contents\n\n")
                    f.write("- `info.json`: Full metadata for all stickers\n")
                    f.write(f"- Sticker files (*.{file_ext})\n")
                
                await status_msg.edit("📦 در حال ایجاد فایل ZIP...")
                zip_path = f"{pack_dir}.zip"
                
                with zipfile.ZipFile(zip_path, 'w') as zipf:
                    for folder_name, subfolders, filenames in os.walk(pack_dir):
                        for filename in filenames:
                            file_path = os.path.join(folder_name, filename)
                            zipf.write(file_path, os.path.relpath(file_path, os.path.dirname(pack_dir)))
                
                if self.add_pack_to_database(pack_data, zip_path):
                    await status_msg.edit("✅ پک استیکر به پایگاه داده اضافه شد")
                
                await status_msg.edit("📤 در حال ارسال فایل ZIP...")
                
                caption = (
                    f"📦 پک استیکر: **{pack_title}**\n"
                    f"🔢 تعداد: **{total_stickers}** استیکر\n"
                    f"📅 تاریخ استخراج: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}"
                )
                
                await self.client.send_file(
                    event.chat_id,
                    zip_path,
                    caption=caption,
                    reply_to=event.id
                )
                
                await status_msg.edit(
                    f"✅ استخراج کامل شد!\n"
                    f"📁 نام پک: **{pack_title}**\n"
                    f"🔢 تعداد استیکر: **{total_stickers}**\n"
                    f"📦 فایل ZIP ارسال شد."
                )
                
            except Exception as e:
                self.logger.error(f"Error extracting sticker pack: {e}")
                await status_msg.edit(f"❌ خطا در استخراج پک استیکر: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/listpacks$'))
        async def list_sticker_packs(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute("""
                SELECT title, short_name, count, extraction_date 
                FROM sticker_packs 
                ORDER BY extraction_date DESC
                """)
                
                packs = cursor.fetchall()
                conn.close()
                
                if not packs:
                    await event.reply("📭 هیچ پک استیکری در پایگاه داده یافت نشد.")
                    return
                    
                message = "📋 **لیست پک‌های استیکر استخراج شده:**\n\n"
                
                for i, (title, short_name, count, date) in enumerate(packs, 1):
                    message += f"{i}. **{title}** (`{short_name}`)\n"
                    message += f"   🔢 تعداد: {count} استیکر\n"
                    message += f"   📅 تاریخ استخراج: {date}\n\n"
                    
                await event.reply(message)
                
            except Exception as e:
                self.logger.error(f"Error listing sticker packs: {e}")
                await event.reply(f"❌ خطا در نمایش لیست پک‌های استیکر: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/packinfo\s+(.+)$'))
        async def pack_info(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            short_name = event.pattern_match.group(1)
            
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute("""
                SELECT id, title, count, is_animated, is_video, official, extraction_date, zip_path
                FROM sticker_packs 
                WHERE short_name = ?
                """, (short_name,))
                
                pack = cursor.fetchone()
                
                if not pack:
                    await event.reply(f"❌ پک استیکر با نام `{short_name}` در پایگاه داده یافت نشد.")
                    conn.close()
                    return
                    
                pack_id, title, count, is_animated, is_video, official, date, zip_path = pack
                
                cursor.execute("""
                SELECT emoji, file_size, mime_type
                FROM stickers
                WHERE pack_id = ?
                """, (pack_id,))
                
                stickers = cursor.fetchall()
                conn.close()
                
                message = f"ℹ️ **اطلاعات پک استیکر {title}**\n\n"
                message += f"🔤 نام کوتاه: {short_name}\n"
                message += f"🔢 تعداد استیکر: {count}\n"
                message += f"🎬 نوع: {'انیمیشن' if is_animated else 'ویدیو' if is_video else 'ثابت'}\n"
                message += f"🏢 رسمی: {'بله' if official else 'خیر'}\n"
                message += f"📅 تاریخ استخراج: {date}\n"
                message += f"📦 مسیر فایل ZIP: {zip_path}\n\n"
                
                emoji_counts = {}
                for emoji, _, _ in stickers:
                    if emoji:
                        for e in emoji:
                            emoji_counts[e] = emoji_counts.get(e, 0) + 1
                            
                top_emojis = sorted(emoji_counts.items(), key=lambda x: x[1], reverse=True)[:10]
                
                if top_emojis:
                    message += "🔝 **ایموجی‌های پرکاربرد:**\n"
                    for emoji, count in top_emojis:
                        message += f"{emoji} ({count}x) "
                    message += "\n\n"
                
                await event.reply(message)
                
                if os.path.exists(zip_path):
                    await event.reply(
                        "آیا می‌خواهید فایل ZIP این پک را دریافت کنید؟\n"
                        "برای دریافت، دستور زیر را ارسال کنید:\n"
                        "`/sendpack {}`".format(short_name)
                    )
                    
            except Exception as e:
                self.logger.error(f"Error getting pack info: {e}")
                await event.reply(f"❌ خطا در دریافت اطلاعات پک استیکر: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/sendpack\s+(.+)$'))
        async def send_pack(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            short_name = event.pattern_match.group(1)
            
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute("""
                SELECT title, count, zip_path
                FROM sticker_packs 
                WHERE short_name = ?
                """, (short_name,))
                
                pack = cursor.fetchone()
                conn.close()
                
                if not pack:
                    await event.reply(f"❌ پک استیکر با نام `{short_name}` در پایگاه داده یافت نشد.")
                    return
                    
                title, count, zip_path = pack
                
                if not os.path.exists(zip_path):
                    await event.reply(f"❌ فایل ZIP پک استیکر یافت نشد. لطفاً دوباره استخراج کنید.")
                    return
                    
                status_msg = await event.reply("📤 در حال ارسال فایل ZIP...")
                
                caption = (
                    f"📦 پک استیکر: **{title}**\n"
                    f"🔢 تعداد: **{count}** استیکر\n"
                    f"📤 ارسال از پایگاه داده"
                )
                
                await self.client.send_file(
                    event.chat_id,
                    zip_path,
                    caption=caption,
                    reply_to=event.id
                )
                
                await status_msg.edit("✅ فایل ZIP با موفقیت ارسال شد.")
                
            except Exception as e:
                self.logger.error(f"Error sending pack: {e}")
                await event.reply(f"❌ خطا در ارسال پک استیکر: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/cleardatabase$'))
        async def clear_database(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute("SELECT COUNT(*) FROM sticker_packs")
                pack_count = cursor.fetchone()[0]
                
                cursor.execute("DELETE FROM stickers")
                cursor.execute("DELETE FROM sticker_packs")
                conn.commit()
                conn.close()
                
                await event.reply(f"✅ پایگاه داده با موفقیت پاک شد. {pack_count} پک استیکر حذف شد.")
                
            except Exception as e:
                self.logger.error(f"Error clearing database: {e}")
                await event.reply(f"❌ خطا در پاک کردن پایگاه داده: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/deletepack\s+(.+)$'))
        async def delete_pack(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            short_name = event.pattern_match.group(1)
            
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute("""
                SELECT id, title, zip_path
                FROM sticker_packs 
                WHERE short_name = ?
                """, (short_name,))
                
                pack = cursor.fetchone()
                
                if not pack:
                    await event.reply(f"❌ پک استیکر با نام `{short_name}` در پایگاه داده یافت نشد.")
                    conn.close()
                    return
                    
                pack_id, title, zip_path = pack
                
                cursor.execute("DELETE FROM stickers WHERE pack_id = ?", (pack_id,))
                cursor.execute("DELETE FROM sticker_packs WHERE id = ?", (pack_id,))
                conn.commit()
                conn.close()
                
                if os.path.exists(zip_path):
                    os.remove(zip_path)
                    
                pack_dir = os.path.splitext(zip_path)[0]
                if os.path.exists(pack_dir) and os.path.isdir(pack_dir):
                    shutil.rmtree(pack_dir)
                    
                await event.reply(f"✅ پک استیکر **{title}** با موفقیت از پایگاه داده حذف شد.")
                
            except Exception as e:
                self.logger.error(f"Error deleting pack: {e}")
                await event.reply(f"❌ خطا در حذف پک استیکر: {str(e)}")
                
        @self.client.on(events.NewMessage(pattern=r'^/helpextract$'))
        async def help_command(event):
            if str(event.sender_id) != self.owner_id:
                return
                
            await event.reply(HELP)
            
            additional_help = """
🔍 **دستورات مدیریت پایگاه داده**:

• `/listpacks` ➤ نمایش لیست تمام پک‌های استیکر در پایگاه داده  
• `/packinfo [نام کوتاه]` ➤ نمایش اطلاعات کامل یک پک استیکر  
• `/sendpack [نام کوتاه]` ➤ ارسال مجدد فایل ZIP یک پک استیکر  
• `/deletepack [نام کوتاه]` ➤ حذف یک پک استیکر از پایگاه داده  
• `/cleardatabase` ➤ پاک کردن کامل پایگاه داده  
• `/helpextract` ➤ نمایش این راهنما
"""
            await event.reply(additional_help)
