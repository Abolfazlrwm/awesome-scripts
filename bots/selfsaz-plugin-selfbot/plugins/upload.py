# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, Button
import os
import zipfile
import requests
from bs4 import BeautifulSoup
import tempfile
import shutil
from datetime import datetime
import traceback
from FastTelethonhelper import fast_download

HELP = """
📤 آپلودکن - ابزار اشتراک فایل 📤

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
📌 معرفی:
این افزونه به شما امکان آپلود فایل‌ها به سرویس uploadkon.ir را می‌دهد.
تمام فایل‌ها قبل از آپلود به صورت خودکار فشرده (zip) می‌شوند.

▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
🎯 دستورات:

• **انگلیسی (با "/" در ابتداء):**
   `/up`         ➔ آپلود فایل (در پاسخ به پیام حاوی فایل)
   `/uphelp`     ➔ نمایش راهنمای افزونه

• **فارسی (بدون "/" با فاصله‌های اضافی):**
   `آپلود           `    ➔ آپلود فایل (در پاسخ به پیام حاوی فایل)
   `راهنمای آپلودکن  `    ➔ نمایش راهنمای افزونه
"""

class UploadKonPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.temp_dir = tempfile.mkdtemp()
        print(f"Created temp directory: {self.temp_dir}")
        
    def create_zip(self, file_path):
        """فایل را به zip تبدیل می‌کند"""
        # بررسی وجود فایل
        if not os.path.exists(file_path):
            raise FileNotFoundError(f"File not found: {file_path}")
            
        # نام فایل zip را تولید می‌کنیم
        file_name = os.path.basename(file_path)
        base_name = os.path.splitext(file_name)[0]
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        zip_name = f"{base_name}_{timestamp}.zip"
        zip_path = os.path.join(self.temp_dir, zip_name)
        
        # فایل zip را ایجاد می‌کنیم
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            zipf.write(file_path, file_name)
            
        return zip_path
        
    def upload_file(self, file_path):
        """فایل را به uploadkon.ir آپلود می‌کند"""
        # بررسی وجود فایل
        if not os.path.exists(file_path):
            raise FileNotFoundError(f"File not found for upload: {file_path}")
            
        url = "https://uploadkon.ir/"
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Referer": "https://uploadkon.ir/",
        }

        with open(file_path, "rb") as file:
            file_name = os.path.basename(file_path)
            files = {
                "file": (file_name, file, 'application/octet-stream')
            }
            data = {
                "submitr": "1",
                "ajax": "1",
                "name": file_name,
            }
            response = requests.post(url, files=files, data=data, headers=headers)

        if response.status_code == 200:
            try:
                json_response = response.json()
                # دسترسی به محتوای HTML
                html_content = json_response[0]['i'] if json_response[0]['t'] == 'index_info' else json_response[0]['message_content']
                
                # تجزیه با BeautifulSoup برای استخراج دقیق لینک‌ها
                soup = BeautifulSoup(html_content, 'html.parser')
                
                download_link = soup.find('textarea', {'id': 'file1'}).text.strip()
                delete_link = soup.find('textarea', {'id': 'delCode'}).text.strip()

                return {
                    "status": "success",
                    "file_name": file_name,
                    "download_link": download_link,
                    "delete_link": delete_link
                }
                
            except (KeyError, IndexError, AttributeError) as e:
                return {
                    "status": "error",
                    "message": f"خطا در پردازش پاسخ: {str(e)}",
                    "details": str(json_response)
                }
            except ValueError:
                return {
                    "status": "error",
                    "message": "پاسخ JSON نامعتبر",
                    "details": response.text
                }
        else:
            return {
                "status": "error",
                "message": f"آپلود ناموفق بود: {response.status_code}",
                "details": response.text
            }

    async def download_telegram_file(self, message, progress_msg=None):
        """دانلود فایل از تلگرام با استفاده از FastTelethonhelper"""
        if not message.file:
            return None, "این پیام شامل فایل نیست."
            
        file_name = message.file.name if message.file.name else f"file_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        file_name = "".join(c for c in file_name if c.isalnum() or c in "._- ")
        
        try:
            # استفاده از FastTelethonhelper برای دانلود سریعتر
            downloaded_file = await fast_download(
                client=self.client,
                msg=message,
                reply=progress_msg,
                download_folder=self.temp_dir
            )
            
            if not downloaded_file or not os.path.exists(downloaded_file):
                return None, "خطا در دانلود فایل از تلگرام."
                
            print(f"File downloaded to: {downloaded_file}")
            return downloaded_file, os.path.basename(downloaded_file)
            
        except Exception as e:
            print(f"Error downloading file: {str(e)}")
            traceback.print_exc()
            return None, f"خطا در دانلود فایل: {str(e)}"

    async def handle_events(self):
        # English help command
        @self.client.on(events.NewMessage(pattern='/uphelp'))
        async def uphelp_handler(event):
            """نمایش راهنمای افزونه (انگلیسی)"""
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)
            
        # Persian help command (without slash, with extra spaces)
        @self.client.on(events.NewMessage(pattern='^راهنمای آپلودکن'))
        async def uphelp_handler_farsi(event):
            """نمایش راهنمای افزونه (فارسی)"""
            if str(event.sender_id) != self.owner_id:
                return
            await event.reply(HELP)
            
        # English upload command
        @self.client.on(events.NewMessage(pattern='/up(?: |$)'))
        async def up_handler(event):
            """آپلود فایل با پاسخ به پیام (انگلیسی)"""
            if str(event.sender_id) != self.owner_id:
                return
                
            # بررسی اینکه آیا این پیام یک پاسخ است
            if not event.is_reply:
                await event.reply("❌ لطفا این دستور را در پاسخ به یک فایل استفاده کنید.")
                return
                
            # دریافت پیام اصلی که به آن پاسخ داده شده
            reply_message = await event.get_reply_message()
            if not reply_message.file:
                await event.reply("❌ پیام مورد نظر شامل فایل نیست.")
                return
                
            processing_msg = await event.reply("⏳ فایل در حال پردازش است. لطفا صبر کنید...")
            
            file_path = None
            zip_path = None
            
            try:
                # دانلود فایل از تلگرام با FastTelethonhelper
                file_path, file_name = await self.download_telegram_file(reply_message, processing_msg)
                if not file_path:
                    await processing_msg.edit(f"❌ خطا در دریافت فایل: {file_name}")
                    return
                    
                await processing_msg.edit(f"📥 فایل '{file_name}' دریافت شد.\n⏳ در حال فشرده‌سازی و آپلود...")
                
                # فشرده‌سازی فایل
                zip_path = self.create_zip(file_path)
                
                # آپلود فایل
                result = self.upload_file(zip_path)
                
                if result["status"] == "success":
                    response_text = f"""
✅ آپلود با موفقیت انجام شد!

📁 نام فایل: {result['file_name']}
📥 لینک دانلود: 
{result['download_link']}

❌ لینک حذف:
{result['delete_link']}

🔒 فایل به صورت خودکار فشرده (ZIP) شده است.
"""
                    await processing_msg.edit(response_text)
                else:
                    await processing_msg.edit(f"❌ خطا در آپلود فایل:\n{result['message']}")
                
            except Exception as e:
                error_details = traceback.format_exc()
                print(f"Error in up_handler: {error_details}")
                await processing_msg.edit(f"❌ خطای غیرمنتظره: {str(e)}\n\nجزئیات بیشتر در لاگ سرور.")
                
            finally:
                # پاکسازی فایل‌های موقت
                try:
                    if file_path and os.path.exists(file_path):
                        os.remove(file_path)
                        print(f"Removed file: {file_path}")
                    if zip_path and os.path.exists(zip_path):
                        os.remove(zip_path)
                        print(f"Removed zip: {zip_path}")
                except Exception as e:
                    print(f"Error cleaning up files: {str(e)}")
                    
        # Persian upload command (without slash, with extra spaces)
        @self.client.on(events.NewMessage(pattern='^آپلود'))
        async def up_handler_farsi(event):
            """آپلود فایل با پاسخ به پیام (فارسی)"""
            if str(event.sender_id) != self.owner_id:
                return
                
            # بررسی اینکه آیا این پیام یک پاسخ است
            if not event.is_reply:
                await event.reply("❌ لطفا این دستور را در پاسخ به یک فایل استفاده کنید.")
                return
                
            # دریافت پیام اصلی که به آن پاسخ داده شده
            reply_message = await event.get_reply_message()
            if not reply_message.file:
                await event.reply("❌ پیام مورد نظر شامل فایل نیست.")
                return
                
            processing_msg = await event.reply("⏳ فایل در حال پردازش است. لطفا صبر کنید...")
            
            file_path = None
            zip_path = None
            
            try:
                # دانلود فایل از تلگرام با FastTelethonhelper
                file_path, file_name = await self.download_telegram_file(reply_message, processing_msg)
                if not file_path:
                    await processing_msg.edit(f"❌ خطا در دریافت فایل: {file_name}")
                    return
                    
                await processing_msg.edit(f"📥 فایل '{file_name}' دریافت شد.\n⏳ در حال فشرده‌سازی و آپلود...")
                
                # فشرده‌سازی فایل
                zip_path = self.create_zip(file_path)
                
                # آپلود فایل
                result = self.upload_file(zip_path)
                
                if result["status"] == "success":
                    response_text = f"""
✅ آپلود با موفقیت انجام شد!

📁 نام فایل: {result['file_name']}
📥 لینک دانلود: 
{result['download_link']}

❌ لینک حذف:
{result['delete_link']}

🔒 فایل به صورت خودکار فشرده (ZIP) شده است.
"""
                    await processing_msg.edit(response_text)
                else:
                    await processing_msg.edit(f"❌ خطا در آپلود فایل:\n{result['message']}")
                
            except Exception as e:
                error_details = traceback.format_exc()
                print(f"Error in up_handler_farsi: {error_details}")
                await processing_msg.edit(f"❌ خطای غیرمنتظره: {str(e)}\n\nجزئیات بیشتر در لاگ سرور.")
                
            finally:
                # پاکسازی فایل‌های موقت
                try:
                    if file_path and os.path.exists(file_path):
                        os.remove(file_path)
                        print(f"Removed file: {file_path}")
                    if zip_path and os.path.exists(zip_path):
                        os.remove(zip_path)
                        print(f"Removed zip: {zip_path}")
                except Exception as e:
                    print(f"Error cleaning up files: {str(e)}")

    async def start(self):
        """شروع افزونه"""
        # ایجاد پوشه موقت اگر وجود نداشته باشد
        os.makedirs(self.temp_dir, exist_ok=True)
        print(f"UploadKon plugin started, using temp dir: {self.temp_dir}")

    async def stop(self):
        """توقف افزونه و پاکسازی منابع"""
        # حذف پوشه موقت
        if os.path.exists(self.temp_dir):
            shutil.rmtree(self.temp_dir)
            print(f"Cleaned up temp dir: {self.temp_dir}")
