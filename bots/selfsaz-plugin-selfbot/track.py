# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import logging
from datetime import datetime, timedelta
from telethon import events

class TestActivityRewardListener:
    """
    Test listener: in a specific chat, silently awards 1 coin per minute up
    to 10/day, without sending any notification messages.
    """
    def __init__(
        self,
        bot,                # TelegramClient instance
        coin_db,            # CoinDB singleton
        chat_id: int = -1002336804501,
        reward_amount: int = 1,
        min_interval_sec: int = 60,
        daily_limit: int = 30
    ):
        self.bot = bot
        self.coin_db = coin_db
        self.chat_id = chat_id
        self.reward_amount = reward_amount
        self.min_interval = timedelta(seconds=min_interval_sec)
        self.daily_limit = daily_limit
        self.last_reward = {}    # user_id -> datetime of last reward
        self.daily_counts = {}   # user_id -> (date, count)
        self.logger = logging.getLogger(__name__)

        # Register event handler for the target chat
        self.bot.add_event_handler(
            self._on_message,
            events.NewMessage(chats=self.chat_id)
        )

    async def _on_message(self, event):
        user_id = event.sender_id
        # Ignore non-user and outgoing messages
        if user_id is None or event.out:
            return

        now = datetime.utcnow()
        last = self.last_reward.get(user_id)

        # 1) Enforce minimum interval (silently)
        if last and (now - last) < self.min_interval:
            return

        # 2) Enforce daily limit (silently)
        rec = self.daily_counts.get(user_id)
        if rec:
            rec_date, count = rec
            if rec_date == now.date():
                if count >= self.daily_limit:
                    return
                count += 1
            else:
                rec_date = now.date()
                count = 1
        else:
            rec_date = now.date()
            count = 1

        # Update trackers
        self.last_reward[user_id] = now
        self.daily_counts[user_id] = (rec_date, count)

        # 3) Award coin silently without notification
        try:
            await self.coin_db.add_coins(user_id, self.reward_amount)
            self.logger.info(f"User {user_id} awarded {self.reward_amount} coins. Daily count: {count}/{self.daily_limit}")
        except Exception as e:
            self.logger.error(f"Error rewarding coin to user {user_id}: {e}")