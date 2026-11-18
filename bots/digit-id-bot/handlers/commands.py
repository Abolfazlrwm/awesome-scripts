"""
handlers/commands.py
Command handlers: /start, /help, /alive and admin broadcast trigger.
"""
from telebot import types, TeleBot
import logging
import config
from database import add_user
from .rate_limit import check_rate_limit, is_message_valid
from .messages import get_start_message, get_help_message

logger = logging.getLogger(__name__)

def register(bot: TeleBot):
    """
    Register all command handlers on the provided TeleBot instance.
    """
    @bot.message_handler(commands=['start'])
    def start_command(message):
        """
        Handle /start command:
        - Checks if message is valid (not sent before bot started)
        - Checks rate-limit
        - Stores user in SQLite (via database.add_user)
        - Shows welcome keyboard (admin gets broadcast button)
        """
        if not is_message_valid(message):
            return

        user_id = message.from_user.id
        allowed, err = check_rate_limit(user_id)
        if not allowed:
            bot.send_message(user_id, err)
            return

        # Save user to SQLite
        add_user(user_id, message.from_user.first_name)

        # Get welcome message
        welcome_message = get_start_message(message.from_user.first_name, user_id)

        markup = types.ReplyKeyboardMarkup(resize_keyboard=True, one_time_keyboard=True)
        if user_id in config.ADMIN_USER_IDS:
            btn_broadcast = types.KeyboardButton("پیام همگانی 📢")
            markup.add(btn_broadcast)
        bot.send_message(message.chat.id, welcome_message, parse_mode="Markdown", reply_markup=markup)

    @bot.message_handler(commands=['help'])
    def help_command(message):
        """
        Handle /help command:
        - Checks if message is valid (not sent before bot started)
        - Checks rate-limit
        """
        if not is_message_valid(message):
            return

        user_id = message.from_user.id
        allowed, err = check_rate_limit(user_id)
        if not allowed:
            bot.send_message(user_id, err)
            return

        # Get help message
        welcome_message = get_help_message(user_id)
        bot.send_message(message.chat.id, welcome_message, parse_mode="Markdown")

    @bot.message_handler(commands=['alive'])
    def alive_command(message):
        """
        Handle /alive command — simple liveness check.
        - Checks if message is valid (not sent before bot started)
        - Checks rate-limit
        """
        if not is_message_valid(message):
            return

        user_id = message.from_user.id
        allowed, err = check_rate_limit(user_id)
        if not allowed:
            bot.send_message(user_id, err)
            return
        bot.send_message(message.chat.id, "I'm alive and kicking! 🤖 DigitIDBot is here!")