# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
import asyncio
import time
import hashlib
from telethon import events, functions, errors
from .base import Plugin

logger = logging.getLogger(__name__)
CHECK_INTERVAL = 300
MIN_SESSION_AGE = 86400

HELP = """  
🛡️ **امنیت پیشرفته حساب کاربری** 🛡️  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های کلیدی**:  
• شناسایی و خاتمه **نشست‌های غیرمجاز**  
• محافظت ۲۴ ساعته با مانیتورینگ هر ۵ دقیقه  
• مدیریت لیست سفید دستگاه‌های مورد اعتماد  
• هشدار فوری به مالک برای هر فعالیت مشکوک  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات امنیتی**:  
• `/antilogin` یا `ضدورود` ➔ فعال/غیرفعال کردن سیستم حفاظتی  
• `/whitelist add` یا `افزودن به لیست سفید` ➔ افزودن **تمام دستگاه‌های فعلی** به لیست سفید  
• `/whitelist remove` یا `حذف از لیست سفید` ➔ حذف دستگاه فعلی از لیست سفید  
• `/whitelist list` یا `لیست سفید` ➔ نمایش دستگاه‌های مجاز  

▬▬▬▬▬▬▬▬▬▬▬▬  
⚠️ **نکات مهم**:  
1. قبل از فعال‌سازی، حتما دستگاه‌های مورد اعتماد را به لیست سفید اضافه کنید!  
2. سیستم به صورت خودکار نشست‌های جدیدتر از ۲۴ ساعت را مسدود می‌کند  
3. برای حذف امن یک دستگاه، از همان دستگاه دستور حذف را ارسال کنید  

✨ **مثال**:  
فعال کردن محافظت:  
`/whitelist add` یا `افزودن به لیست سفید` ➔ افزودن دستگاه‌های مجاز  
`/antilogin` یا `ضدورود` ➔ فعال‌سازی سیستم امنیتی  
"""

class AntiLoginPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.running = False
        self.task = None
        self.pending_sessions = {}

    async def initialize(self, me):
        self.me = me
        logger.info(f"پلاگین AntiLogin برای {self.owner_id} راه‌اندازی شد")
        
        await self.load_pending_sessions()
        
        if await self.get_anti_login_status():
            self.task = asyncio.create_task(self.session_monitor())

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/antilogin|ضدورود)$', forwards=False))
        async def toggle_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            current_state = await self.get_anti_login_status()
            new_state = not current_state
            
            # Check whitelist before activation
            if new_state:
                whitelisted = await self.get_whitelisted_sessions()
                if not whitelisted:
                    await event.reply(
                        "❌ ابتدا باید حداقل یک نشست را به لیست سفید اضافه کنید!\n"
                        "دستور: /whitelist add یا افزودن به لیست سفید"
                    )
                    await event.delete()
                    return

            await self.set_anti_login_status(new_state)
            
            if new_state:
                if not self.running or self.task.done():
                    self.task = asyncio.create_task(self.session_monitor())
                await event.reply("🛡️ Anti-Login فعال شد!\nتمام نشست‌های غیرمجاز خاتمه خواهند یافت.")
            else:
                if self.task and not self.task.done():
                    self.task.cancel()
                await event.reply("🔓 Anti-Login غیرفعال شد\nسایر نشست‌ها فعال باقی می‌مانند.")
            
            await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/whitelist add|افزودن به لیست سفید)$', forwards=False))
        async def whitelist_add_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            auths = await self.client(functions.account.GetAuthorizationsRequest())
            added_count = 0
            for auth in auths.authorizations:
                session_hash = str(auth.hash)
                device_model = auth.device_model
                await self.add_whitelisted_session(session_hash, device_model)
                added_count += 1
            
            await event.reply(f"✅ {added_count} نشست به لیست سفید اضافه شد.")
            await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/whitelist remove|حذف از لیست سفید)$', forwards=False))
        async def whitelist_remove_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            auth_key = self.client.session.auth_key
            current_hash = hashlib.sha1(auth_key).hexdigest() if auth_key else None
            
            if current_hash:
                await self.remove_whitelisted_session(current_hash)
                await event.reply("✅ نشست فعلی از لیست سفید حذف شد.")
            else:
                await event.reply("❌ شناسایی نشست فعلی ناموفق بود.")
            await event.delete()

        @self.client.on(events.NewMessage(pattern=r'^(?:/whitelist list|لیست سفید)$', forwards=False))
        async def whitelist_list_handler(event):
            if str(event.sender_id) != self.owner_id:
                return
            
            whitelisted_sessions = await self.get_whitelisted_sessions()
            if not whitelisted_sessions:
                await event.reply("هیچ نشستی در لیست سفید وجود ندارد.")
                return
            
            message = "🛡️ نشست‌های لیست سفید:\n"
            for session in whitelisted_sessions:
                session_hash, device_model = session
                message += f"- {device_model} (`{session_hash}`)\n"
            
            await event.reply(message)
            await event.delete()

    async def get_anti_login_status(self):
        from db import get_anti_login_status
        return await get_anti_login_status(self.owner_id)

    async def set_anti_login_status(self, enabled):
        from db import set_anti_login_status
        await set_anti_login_status(self.owner_id, enabled)

    async def load_pending_sessions(self):
        from db import get_pending_sessions
        pending_sessions = await get_pending_sessions(self.owner_id)
        for session in pending_sessions:
            session_hash, created_timestamp, _ = session
            self.pending_sessions[session_hash] = created_timestamp
        logger.info(f"{len(pending_sessions)} نشست در حال انتظار از پایگاه داده بارگیری شد.")

    async def add_whitelisted_session(self, session_hash: str, device_model: str):
        from db import add_whitelisted_session
        await add_whitelisted_session(self.owner_id, session_hash, device_model)

    async def remove_whitelisted_session(self, session_hash: str):
        from db import remove_whitelisted_session
        await remove_whitelisted_session(self.owner_id, session_hash)

    async def get_whitelisted_sessions(self):
        from db import get_whitelisted_sessions
        return await get_whitelisted_sessions(self.owner_id)

    async def session_monitor(self):
        self.running = True
        while self.running:
            try:
                if not await self.get_anti_login_status():
                    break
                    
                await self.check_and_terminate_sessions()
                await asyncio.sleep(CHECK_INTERVAL)
            except asyncio.CancelledError:
                self.running = False
                break
            except Exception as e:
                logger.error(f"خطای مانیتور نشست: {str(e)}")
                await asyncio.sleep(500)

    async def check_and_terminate_sessions(self):
        try:
            auths = await self.client(functions.account.GetAuthorizationsRequest())
            
            current_hash = "0"
            
            whitelisted_sessions = await self.get_whitelisted_sessions()
            whitelisted_hashes = {str(session[0]) for session in whitelisted_sessions}

            terminated = 0
            
            for auth in auths.authorizations:
                auth_hash = str(auth.hash)
                
                if auth_hash == current_hash:
                    logger.info(f"نشست فعلی رد شد: {auth.device_model} (هش: {auth_hash})")
                    
                    session_age = time.time() - auth.date_created.timestamp()
                    if session_age < MIN_SESSION_AGE:
                        remaining_time = MIN_SESSION_AGE - session_age
                        logger.info(f"نشست فعلی خیلی جدید است. شما می‌توانید سایر نشست‌ها را در {remaining_time:.1f} ثانیه دیگر خاتمه دهید.")
                    continue
                
                if auth_hash in whitelisted_hashes:
                    logger.info(f"نشست لیست سفید رد شد: {auth.device_model} (هش: {auth_hash})")
                    continue
                
                try:
                    await self.client(functions.account.ResetAuthorizationRequest(hash=int(auth_hash)))
                    logger.info(f"نشست غیرمجاز خاتمه یافت: {auth.device_model} (هش: {auth.hash})")
                    terminated += 1
                except errors.AuthKeyUnregisteredError:
                    logger.info(f"نشست قبلاً خاتمه یافته است: {auth.device_model} (هش: {auth.hash})")
                except errors.SessionTooFreshError:
                    logger.info(f"نشست هنوز خیلی جدید است برای خاتمه: {auth.device_model} (هش: {auth.hash})")
                except Exception as e:
                    logger.info(f"خطا در خاتمه نشست: {str(e)}")
            
            if terminated > 0:
                message = f"🚫 {terminated} نشست غیرمجاز خاتمه یافت"
                try:
                    await self.client.send_message(int(self.owner_id), message)
                except Exception as e:
                    logger.info(f"خطا در ارسال پیام به مالک: {str(e)}")
                    
        except Exception as e:
            logger.info(f"بررسی نشست ناموفق بود: {str(e)}")
                    
    async def add_pending_session(self, session_hash: str, created_timestamp: int, device_model: str):
        from db import add_pending_session
        await add_pending_session(
            owner_id=self.owner_id,
            session_hash=session_hash,
            created_timestamp=created_timestamp,
            device_model=device_model
        )

    async def remove_pending_session(self, session_hash: str):
        from db import remove_pending_session
        await remove_pending_session(
            owner_id=self.owner_id,
            session_hash=session_hash
        )
