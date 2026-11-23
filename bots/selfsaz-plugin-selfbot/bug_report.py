# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# bug_report.py
import sqlite3
import logging
from datetime import datetime
from typing import Optional, Dict, List, Tuple
from telethon import TelegramClient, events, Button
from enum import Enum

logger = logging.getLogger(__name__)


class BugStatus(Enum):
    OPEN = "open"
    IN_PROGRESS = "in_progress"
    RESOLVED = "resolved"
    REJECTED = "rejected"


class BugPriority(Enum):
    LOW = "low"
    MEDIUM = "medium"
    HIGH = "high"
    CRITICAL = "critical"


class BugReportSystem:
    """سیستم پیشرفته گزارش خطا با پنل مدیریت و ردیابی وضعیت"""

    def __init__(self, bot: TelegramClient, admin_id: int = 7150795159):
        self.bot = bot
        self.admin_id = admin_id
        self.user_states: Dict[int, str] = {}
        self.temp_bug_data: Dict[int, Dict] = {}
        self._init_db()
        self._register_handlers()

    def _init_db(self) -> None:
        """راه‌اندازی پایگاه داده SQLite پیشرفته"""
        try:
            self.conn = sqlite3.connect('bug_reports.db', check_same_thread=False)
            with self.conn:
                self.conn.execute('DROP TABLE IF EXISTS bug_tags')
                self.conn.execute('DROP TABLE IF EXISTS reports')

            with self.conn:
                self.conn.execute('''
                    CREATE TABLE reports (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        user_id INTEGER NOT NULL,
                        username TEXT,
                        title TEXT NOT NULL,
                        description TEXT NOT NULL,
                        priority TEXT DEFAULT 'medium',
                        status TEXT DEFAULT 'open',
                        admin_notes TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP,
                        resolved_at TIMESTAMP,
                        resolution_message TEXT
                    )
                ''')

                self.conn.execute('''
                    CREATE TABLE bug_tags (
                        bug_id INTEGER,
                        tag TEXT,
                        FOREIGN KEY(bug_id) REFERENCES reports(id) ON DELETE CASCADE,
                        PRIMARY KEY(bug_id, tag)
                    )
                ''')

                self.conn.execute('CREATE INDEX idx_reports_status ON reports(status)')
                self.conn.execute('CREATE INDEX idx_reports_user_id ON reports(user_id)')
                self.conn.execute('CREATE INDEX idx_reports_priority ON reports(priority)')

        except sqlite3.Error as e:
            logger.error(f"خطای راه‌اندازی پایگاه داده: {str(e)}")
            raise

        @self.bot.on(events.CallbackQuery(pattern=r'^priority_'))
        async def priority_handler(event):
            priority = event.data.decode().split('_')[1]
            await self.handle_priority_selection(event, priority)

        @self.bot.on(events.CallbackQuery(pattern='cancel_bug_report'))
        async def cancel_handler(event):
            await self.cancel_report(event)

    def get_user_state(self, user_id: int) -> Optional[str]:
        """وضعیت فعلی کاربر در فرآیند گزارش خطا"""
        return self.user_states.get(user_id)

    def _clear_user_state(self, user_id: int) -> None:
        """پاک کردن وضعیت و داده‌های موقت کاربر"""
        if user_id in self.user_states:
            del self.user_states[user_id]
        if user_id in self.temp_bug_data:
            del self.temp_bug_data[user_id]

    def _create_keyboard(self) -> List[List[Button]]:
        """ایجاد صفحه کلید پیشفرض برای فرآیند گزارش"""
        return [[Button.inline("❌ لغو گزارش", b"cancel_bug_report")]]

    async def start_report(self, event: events.NewMessage.Event) -> None:
        """شروع فرآیند گزارش خطا"""
        user_id = event.sender_id

        if self.get_user_state(user_id):
            await event.respond(
                "⚠️ شما یک گزارش در حال انجام دارید!\nلطفا گزارش فعلی را تکمیل یا لغو کنید.",
                buttons=self._create_keyboard()
            )
            return

        self.user_states[user_id] = 'awaiting_title'
        self.temp_bug_data[user_id] = {}

        await event.respond(
            "📝 **گزارش خطای جدید**\n\n"
            "لطفا یک عنوان مختصر برای مشکل وارد کنید\n"
            "(مثال: 'دکمه ورود کار نمی‌کند', 'برنامه هنگام شروع خراب می‌شود')",
            buttons=self._create_keyboard()
        )

    async def handle_input(self, event: events.NewMessage.Event) -> None:
        """مدیریت ورودی‌های کاربر بر اساس وضعیت فعلی"""
        user_id = event.sender_id
        current_state = self.get_user_state(user_id)
        text = event.text.strip()

        if not current_state:
            return

        if text.startswith('/'):
            await event.respond("⚠️ در حین گزارش خطا نمی‌توانید از دستورات استفاده کنید.")
            return

        handlers = {
            'awaiting_title': self._handle_title,
            'awaiting_description': self._handle_description,
            'awaiting_priority': self._handle_priority
        }

        if current_state in handlers:
            await handlers[current_state](event)
            
            
    async def _handle_priority(self, event: events.NewMessage.Event) -> None:
        """مدیریت انتخاب اولویت"""
        await event.respond(
            "⚠️ لطفا از دکمه‌های بالا برای انتخاب سطح اولویت استفاده کنید.",
            buttons=self._create_keyboard()
        )


    async def _handle_title(self, event: events.NewMessage.Event) -> None:
        """مدیریت عنوان گزارش"""
        user_id = event.sender_id
        title = event.text.strip()

        if len(title) < 5:
            await event.respond("⚠️ عنوان خیلی کوتاه است. لطفا توصیف کامل‌تری ارائه دهید.")
            return

        self.temp_bug_data[user_id]['title'] = title
        self.user_states[user_id] = 'awaiting_description'

        await event.respond(
            "🔍 عالی! حالا لطفا توضیحات کامل مشکل را وارد کنید:\n\n"
            "شامل:\n"
            "• چه کاری می‌خواستید انجام دهید؟\n"
            "• چه اتفاقی افتاد؟\n"
            "• مراحل تولید مشکل\n"
            "• پیغام‌های خطا (در صورت وجود)",
            buttons=self._create_keyboard()
        )

    async def _handle_description(self, event: events.NewMessage.Event) -> None:
        """مدیریت توضیحات گزارش"""
        user_id = event.sender_id
        description = event.text.strip()

        if len(description) < 20:
            await event.respond("⚠️ لطفا توضیحات کامل‌تری ارائه دهید.")
            return

        self.temp_bug_data[user_id]['description'] = description
        self.user_states[user_id] = 'awaiting_priority'

        await event.respond(
            "🎯 تقریبا تمام شد! لطفا سطح اولویت را انتخاب کنید:",
            buttons=[
                [
                    Button.inline("🟢 کم", b"priority_low"),
                    Button.inline("🟡 متوسط", b"priority_medium"),
                    Button.inline("🟠 بالا", b"priority_high"),
                    Button.inline("🔴 بحرانی", b"priority_critical")
                ],
                [Button.inline("❌ لغو", b"cancel_report")]
            ]
        )

    async def handle_priority_selection(self, event: events.CallbackQuery.Event, priority: str) -> None:
        """مدیریت انتخاب اولویت"""
        user_id = event.sender_id

        if priority not in [p.value for p in BugPriority]:
            await event.answer("⚠️ اولویت نامعتبر", alert=True)
            return

        try:
            sender = await event.get_sender()
            self.temp_bug_data[user_id]['username'] = sender.username
            await self._submit_report(user_id, priority)
            await event.edit("✅ گزارش خطا با موفقیت ثبت شد! از بازخورد شما متشکریم.")

        except Exception as e:
            logger.error(f"خطا در انتخاب اولویت: {str(e)}")
            await event.edit(
                "❌ خطایی در ثبت گزارش رخ داد.\nلطفا بعدا مجددا تلاش کنید."
            )
            self._clear_user_state(user_id)

    async def cancel_report(self, event: events.CallbackQuery.Event) -> None:
        """لغو گزارش"""
        user_id = event.sender_id
        self._clear_user_state(user_id)
        await event.edit("گزارش لغو شد. می‌توانید گزارش جدیدی ارسال کنید.")

    async def _submit_report(self, user_id: int, priority: str) -> None:
        """ثبت نهایی گزارش"""
        try:
            data = self.temp_bug_data[user_id]
            with self.conn:
                cursor = self.conn.cursor()
                cursor.execute('''
                    INSERT INTO reports 
                    (user_id, username, title, description, priority, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                    RETURNING id
                ''', (
                    user_id,
                    data.get('username'),
                    data['title'],
                    data['description'],
                    priority,
                    BugStatus.OPEN.value
                ))

                bug_id = cursor.fetchone()[0]

            await self._notify_admin_new_bug(bug_id)
            await self.bot.send_message(
                user_id,
                "✅ گزارش خطا با موفقیت ثبت شد!\nدر صورت بروزرسانی اطلاع‌رسانی می‌شوید."
            )

        except Exception as e:
            logger.error(f"خطا در ثبت گزارش: {str(e)}")
            await self.bot.send_message(
                user_id,
                "❌ خطا در ثبت گزارش. لطفا بعدا تلاش کنید."
            )
        finally:
            self._clear_user_state(user_id)

    async def _notify_admin_new_bug(self, bug_id: int) -> None:
        """ارسال اعلان به ادمین"""
        try:
            bug_data = await self._get_bug_details(bug_id)
            message = (
                f"🚨 **گزارش خطای جدید #{bug_id}**\n\n"
                f"**عنوان:** {bug_data['title']}\n"
                f"**اولویت:** {bug_data['priority'].upper()}\n"
                f"**گزارش دهنده:** @{bug_data['username']}\n\n"
                f"**توضیحات:**\n{bug_data['description']}\n\n"
                f"**وضعیت:** {bug_data['status'].upper()}"
            )

            await self.bot.send_message(
                self.admin_id,
                message,
                buttons=self._create_admin_buttons(bug_id)
            )
        except Exception as e:
            logger.error(f"خطای اعلان به ادمین: {str(e)}")

    def _create_admin_buttons(self, bug_id: int) -> List[List[Button]]:
        """دکمه‌های مدیریت برای ادمین"""
        return [
            [
                Button.inline("✅ حل شده", f"bug_admin_resolve_{bug_id}".encode()),
                Button.inline("🔄 در حال بررسی", f"bug_admin_progress_{bug_id}".encode())
            ],
            [
                Button.inline("❌ رد شده", f"bug_admin_reject_{bug_id}".encode()),
                Button.inline("📝 افزودن یادداشت", f"bug_admin_note_{bug_id}".encode())
            ],
            [
                Button.inline("🔍 جزئیات", f"bug_admin_details_{bug_id}".encode())
            ]
        ]

    def _register_handlers(self):
        """ثبت هندلرهای رویداد"""
        @self.bot.on(events.CallbackQuery(pattern=r'^bug_admin_'))
        async def admin_panel_handler(event):
            if event.sender_id != self.admin_id:
                await event.answer("⛔️ دسترسی غیرمجاز", alert=True)
                return
            await self._handle_admin_actions(event)

        @self.bot.on(events.CallbackQuery(pattern=r'^priority_'))
        async def priority_handler(event):
            priority = event.data.decode().split('_')[1]
            await self.handle_priority_selection(event, priority)

        @self.bot.on(events.CallbackQuery(pattern='cancel_bug_report'))
        async def cancel_handler(event):
            await self.cancel_report(event)

    async def _mark_in_progress(self, event: events.CallbackQuery.Event, bug_id: int) -> None:
        """علامت‌گذاری خطا به عنوان در حال بررسی و اطلاع‌رسانی به کاربر"""
        try:
            with self.conn:
                cursor = self.conn.cursor()
                cursor.execute('''
                    UPDATE reports
                    SET status = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                    RETURNING user_id
                ''', (BugStatus.IN_PROGRESS.value, bug_id))
                
                user_id = cursor.fetchone()[0]

            await self.bot.send_message(
                user_id,
                f"🔄 گزارش خطای شما #{bug_id} در حال بررسی است!\n"
                "در صورت بروزرسانی اطلاع‌رسانی می‌شوید."
            )

            await event.edit(
                "🔄 خطا به عنوان در حال بررسی علامت‌گذاری شد و به کاربر اطلاع داده شد.",
                buttons=self._create_admin_buttons(bug_id)
            )

        except Exception as e:
            logger.error(f"خطا در علامت‌گذاری خطا به عنوان در حال بررسی: {str(e)}")
            await event.answer("❌ خطا در بروزرسانی وضعیت خطا", alert=True)

    async def _reject_bug(self, event: events.CallbackQuery.Event, bug_id: int) -> None:
        """علامت‌گذاری خطا به عنوان رد شده و اطلاع‌رسانی به کاربر"""
        try:
            with self.conn:
                cursor = self.conn.cursor()
                cursor.execute('''
                    UPDATE reports
                    SET status = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                    RETURNING user_id
                ''', (BugStatus.REJECTED.value, bug_id))
                
                user_id = cursor.fetchone()[0]

            await self.bot.send_message(
                user_id,
                f"❌ گزارش خطای شما #{bug_id} بررسی و بسته شد.\n"
                "دلایل احتمالی:\n"
                "• مشکل قابل بازتولید نبود\n"
                "• عملکرد صحیح است\n"
                "• اطلاعات کافی ارائه نشده بود\n\n"
                "در صورت نیاز می‌توانید گزارش جدیدی با جزئیات بیشتر ارسال کنید."
            )

            await event.edit(
                "❌ خطا به عنوان رد شده علامت‌گذاری شد و به کاربر اطلاع داده شد.",
                buttons=self._create_admin_buttons(bug_id)
            )

        except Exception as e:
            logger.error(f"خطا در رد کردن خطا: {str(e)}")
            await event.answer("❌ خطا در بروزرسانی وضعیت خطا", alert=True)

    async def _prompt_admin_note(self, event: events.CallbackQuery.Event, bug_id: int) -> None:
        """درخواست یادداشت از ادمین برای گزارش خطا"""
        self.user_states[self.admin_id] = f'awaiting_note_{bug_id}'
        
        await event.edit(
            "📝 لطفا یادداشت خود را برای این گزارش خطا وارد کنید.\n"
            "برای لغو از /cancel استفاده کنید.",
            buttons=[[Button.inline("❌ لغو", b"cancel_note")]]
        )

    async def _add_admin_note(self, event: events.NewMessage.Event, bug_id: int, note: str) -> None:
        """افزودن یادداشت ادمین به گزارش خطا"""
        try:
            with self.conn:
                cursor = self.conn.cursor()
                cursor.execute('''
                    UPDATE reports
                    SET admin_notes = COALESCE(admin_notes || '\n\n', '') || ? || ' [' || datetime() || ']',
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ''', (note, bug_id))

            await event.respond(
                "✅ یادداشت با موفقیت افزوده شد!",
                buttons=self._create_admin_buttons(bug_id)
            )
            
            self._clear_user_state(self.admin_id)

        except Exception as e:
            logger.error(f"خطا در افزودن یادداشت ادمین: {str(e)}")
            await event.respond("❌ خطا در افزودن یادداشت. لطفا مجددا تلاش کنید.")

    async def _show_bug_details(self, event: events.CallbackQuery.Event, bug_id: int) -> None:
        """نمایش جزئیات کامل یک گزارش خطا"""
        try:
            bug_data = await self._get_bug_details(bug_id)
            if not bug_data:
                await event.answer("❌ گزارش خطا یافت نشد", alert=True)
                return

            message = (
                f"🔍 **جزئیات گزارش خطا #{bug_id}**\n\n"
                f"**عنوان:** {bug_data['title']}\n"
                f"**وضعیت:** {bug_data['status'].upper()}\n"
                f"**اولویت:** {bug_data['priority'].upper()}\n"
                f"**گزارش دهنده:** @{bug_data['username']}\n"
                f"**تاریخ ایجاد:** {bug_data['created_at']}\n\n"
                f"**توضیحات:**\n{bug_data['description']}\n\n"
            )

            if bug_data['admin_notes']:
                message += f"**یادداشت‌های ادمین:**\n{bug_data['admin_notes']}\n\n"

            if bug_data['resolution_message']:
                message += f"**راه‌حل:**\n{bug_data['resolution_message']}\n"

            await event.edit(message, buttons=self._create_admin_buttons(bug_id))

        except Exception as e:
            logger.error(f"خطا در نمایش جزئیات خطا: {str(e)}")
            await event.answer("❌ خطا در دریافت جزئیات خطا", alert=True)

    async def _handle_admin_actions(self, event: events.CallbackQuery.Event) -> None:
        """مدیریت اقدامات ادمین"""
        action = event.data.decode('utf-8').split('_')[2]
        bug_id = int(event.data.decode('utf-8').split('_')[3])

        if action == 'resolve':
            await self._resolve_bug(event, bug_id)
        elif action == 'progress':
            await self._mark_in_progress(event, bug_id)
        elif action == 'reject':
            await self._reject_bug(event, bug_id)
        elif action == 'note':
            await self._prompt_admin_note(event, bug_id)
        elif action == 'details':
            await self._show_bug_details(event, bug_id)

    async def _resolve_bug(self, event: events.CallbackQuery.Event, bug_id: int) -> None:
        """علامت‌گذاری خطا به عنوان حل شده و اطلاع‌رسانی به کاربر"""
        try:
            with self.conn:
                cursor = self.conn.cursor()
                cursor.execute('''
                    UPDATE reports
                    SET status = ?, resolved_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                    RETURNING user_id
                ''', (BugStatus.RESOLVED.value, bug_id))

                user_id = cursor.fetchone()[0]

            await self.bot.send_message(
                user_id,
                f"✅ گزارش خطای شما #{bug_id} حل شد!\n"
                "از کمک شما برای بهبود خدمات متشکریم."
            )

            await event.edit(
                "✅ خطا به عنوان حل شده علامت‌گذاری شد و به کاربر اطلاع داده شد.",
                buttons=self._create_admin_buttons(bug_id)
            )

        except Exception as e:
            logger.error(f"خطا در حل کردن خطا: {str(e)}")
            await event.answer("❌ خطا در بروزرسانی وضعیت خطا", alert=True)

    async def show_admin_panel(self, event: events.NewMessage.Event) -> None:
        """نمایش پنل مدیریت ادمین"""
        if event.sender_id != self.admin_id:
            await event.respond("⛔️ دسترسی غیرمجاز")
            return

        bugs = await self._get_all_bugs()
        message = "🔧 **پنل مدیریت گزارش خطا**\n\n"

        for status in BugStatus:
            status_bugs = [b for b in bugs if b['status'] == status.value]
            if status_bugs:
                message += f"\n**{status.value.upper()}** ({len(status_bugs)})\n"
                for bug in status_bugs[:5]:  # نمایش آخرین ۵ خطا برای هر وضعیت
                    message += (
                        f"• #{bug['id']} - {bug['title'][:30]}...\n"
                        f"  اولویت: {bug['priority']}\n"
                    )

        await event.respond(
            message,
            buttons=[
                [Button.inline("🔍 مشاهده تمام خطاها", b"admin_view_all")],
                [Button.inline("📊 آمار", b"admin_stats")],
                [Button.inline("⚙️ تنظیمات", b"admin_settings")]
            ]
        )

    async def _get_all_bugs(self) -> List[Dict]:
        """دریافت تمام گزارش‌های خطا از پایگاه داده"""
        with self.conn:
            cursor = self.conn.cursor()
            cursor.execute('''
                SELECT * FROM reports
                ORDER BY created_at DESC
            ''')
            columns = [description[0] for description in cursor.description]
            return [dict(zip(columns, row)) for row in cursor.fetchall()]

    async def _get_bug_details(self, bug_id: int) -> Dict:
        """دریافت جزئیات یک گزارش خطا"""
        with self.conn:
            cursor = self.conn.cursor()
            cursor.execute('SELECT * FROM reports WHERE id = ?', (bug_id,))
            columns = [description[0] for description in cursor.description]
            row = cursor.fetchone()
            return dict(zip(columns, row)) if row else None

    def close(self) -> None:
        """بستن اتصال به پایگاه داده"""
        if hasattr(self, 'conn') and self.conn:
            self.conn.close()
            logger.info("اتصال به پایگاه داده بسته شد")

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.close()