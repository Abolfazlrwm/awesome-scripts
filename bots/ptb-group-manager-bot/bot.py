# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
#!/usr/bin/env python
# -*- coding: utf-8 -*-

import logging
import time
import threading
from telegram.ext import Updater, CommandHandler, MessageHandler, Filters, CallbackQueryHandler, ConversationHandler
from telegram import InlineKeyboardButton, InlineKeyboardMarkup, ChatPermissions, ParseMode
from telegram.error import BadRequest, Unauthorized, TelegramError
from config import TOKEN, ADMIN_ID, GLASS_DESIGN
from database import Database
from filters import MessageFilters
from handlers import BotHandlers


logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)

logger = logging.getLogger(__name__)

def update_admins_job(context):
    """Job to update admin status in database"""
    db = context.bot_data['db']
    handlers = context.bot_data['handlers']
    
    try:
        groups = db.get_all_groups()
        if groups:
            for group_id in groups:
                handlers.update_group_admins(context, group_id)
    except Exception as e:
        logger.error(f"Error in update_admins_job: {str(e)}")

def main():
    """Start the bot"""
    print(f"""
{GLASS_DESIGN["header"]}
  {GLASS_DESIGN["admin"]} Telegram Group Management Bot
{GLASS_DESIGN["separator"]}
  {GLASS_DESIGN["success"]} Starting bot with glass design...
  {GLASS_DESIGN["info"]} Bot token: {TOKEN[:8]}...
  {GLASS_DESIGN["user"]} Admin ID: {ADMIN_ID}
{GLASS_DESIGN["footer"]}
    """)
    
    updater = Updater(TOKEN, use_context=True, workers=8)
    dp = updater.dispatcher
    
    db = Database()
    db.add_user(ADMIN_ID, None, "Admin", None, 1)
    
    message_filters = MessageFilters(db)
    handlers = BotHandlers(db, message_filters)
    handlers.register_handlers(dp)
    
    dp.bot_data['db'] = db
    dp.bot_data['handlers'] = handlers
    
    job_queue = updater.job_queue
    job_queue.run_repeating(update_admins_job, interval=21600, first=10800)
    
    logger.info("Bot started")
    
    updater.start_polling(drop_pending_updates=True)
    updater.idle()
    
    db.close()

if __name__ == '__main__':
    main() 