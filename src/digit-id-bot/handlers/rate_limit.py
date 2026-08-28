import logging
from time import time
from datetime import datetime
from pytz import timezone
import config

logger = logging.getLogger(__name__)

# Bot Start Time
bot_start_time = datetime.now(timezone('Asia/Tehran')).timestamp()

message_tracker = {}

def is_message_valid(message) -> bool:
    """
    بررسی می‌کنه که آیا پیام معتبره (یعنی بعد از شروع ربات ارسال شده).
    Args:
        message: شیء پیام از telebot
    Returns:
        bool: True اگر پیام معتبر باشه، False اگر قدیمی باشه
    """
    message_time = message.date
    logger.info(f"Checking message timestamp: {message_time} vs bot_start_time: {bot_start_time}")
    if message_time < bot_start_time:
        logger.warning(f"Ignoring old message from user {message.chat.id} sent at {message_time}")
        return False
    return True

def check_rate_limit(user_id: int) -> tuple[bool, str]:
    """
    بررسی محدودیت نرخ برای کاربر.
    هر کاربر می‌تونه حداکثر 2 پیام در ثانیه بفرسته، وگرنه برای 30 ثانیه بلاک می‌شه.
    Args:
        user_id: آیدی عددی کاربر
    Returns:
        tuple: (آیا اجازه داره؟, پیام خطا در صورت عدم اجازه)
    """
    current_time = time()

    # Ignore Limit For Admins
    if user_id in config.ADMIN_USER_IDS:
        return True, ""

    if user_id not in message_tracker:
        message_tracker[user_id] = {'count': 0, 'last_time': current_time, 'temp_block_until': 0}

    if current_time < message_tracker[user_id]['temp_block_until']:
        remaining = int(message_tracker[user_id]['temp_block_until'] - current_time)
        return False, f"شما به دلیل ارسال پیام زیاد تا {remaining} ثانیه نمی‌تونید پیام بفرستید 😕"

    if current_time - message_tracker[user_id]['last_time'] > 1:
        message_tracker[user_id]['count'] = 0
        message_tracker[user_id]['last_time'] = current_time

    message_tracker[user_id]['count'] += 1

    if message_tracker[user_id]['count'] > 2:
        message_tracker[user_id]['temp_block_until'] = current_time + 30
        return False, "شما بیش از حد پیام فرستادید! تا ۳۰ ثانیه نمی‌تونید پیام بفرستید 😕"

    return True, ""