"""
main.py
Application entrypoint: initializes DB, creates bot, registers handlers and starts polling.
"""
import logging
import config
import database
import handlers
from telebot import TeleBot

def setup_logging():
    logging.basicConfig(
        level=logging.ERROR,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s"
    )

def main():
    setup_logging()
    logger = logging.getLogger("main")
    logger.info("Starting bot...")

    # initialize db
    database.init_db()

    # create bot instance
    bot = TeleBot(config.TOKEN, parse_mode=None)

    # register handlers
    handlers.register_handlers(bot)

    # start polling
    try:
        logger.info("Bot polling started.")
        bot.infinity_polling(timeout=60, long_polling_timeout=90)
    except KeyboardInterrupt:
        logger.info("Bot stopped by user.")
    except Exception as e:
        logger.exception("Unhandled exception in polling: %s", e)

if __name__ == "__main__":
    main()