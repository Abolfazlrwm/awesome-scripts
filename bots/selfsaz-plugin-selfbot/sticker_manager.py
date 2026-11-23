# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from telegram import InputSticker, InlineQueryResultArticle, InputTextMessageContent
from telegram.error import BadRequest
import re
import unicodedata

class StickerManager:
    def __init__(self, bot):
        self.bot = bot
        self.bot_username = None

    async def initialize(self):
        """Fetch bot username."""
        me = await self.bot.get_me()
        self.bot_username = me.username
        print(f"Bot username: {self.bot_username}")

    async def handle_addsticker(self, update):
        """Process _addsticker command."""
        query = update.inline_query
        try:
            if not self.bot_username:
                await self.initialize()

            # Parse input
            parts = query.query.split(' ', 5)
            if len(parts) < 6:
                raise ValueError("Invalid format! Use: @bot _addsticker [pack_name] [pack_title] [emojis] [file_id] [file_hash]")

            _, pack_name, pack_title, emojis, file_id, file_hash = parts

            # Validate inputs
            self._validate_pack_name(pack_name)
            clean_emojis = self._process_emojis(emojis)
            file_id = self._extract_file_id(file_id)

            # Add or create sticker pack
            result = await self._manage_sticker_pack(
                user_id=query.from_user.id,
                pack_name=pack_name,
                pack_title=pack_title,
                file_id=file_id,
                emojis=clean_emojis
            )
            return self._success_response(result)

        except Exception as e:
            print(f"Error: {e}")
            return self._error_response(e)

    async def _manage_sticker_pack(self, user_id, pack_name, pack_title, file_id, emojis):
        """Add to or create sticker pack."""
        full_pack_name = f"{pack_name}_by_{self.bot_username}"
        print(f"Full pack name: {full_pack_name}")
        try:
            print("Trying to add sticker...")
            await self.bot.add_sticker_to_set(
                user_id=user_id,
                name=full_pack_name,
                sticker=InputSticker(
                    sticker=file_id,
                    emoji_list=emojis,
                    format="static"
                )
            )
            print("Sticker added successfully.")
            return f"✅ Sticker added to '{pack_title}'! \nLink: t.me/addstickers/{full_pack_name}"

        except BadRequest as e:
            print(f"BadRequest: {e}")
            if "STICKERSET_INVALID" in str(e):
                print("Creating new sticker pack...")
                await self.bot.create_new_sticker_set(
                    user_id=user_id,
                    name=full_pack_name,
                    title=pack_title,
                    stickers=[InputSticker(
                        sticker=file_id,
                        emoji_list=emojis,
                        format="static"
                    )],
                    sticker_format="static"
                )
                print("New pack created.")
                return f"✨ New pack created: '{pack_title}'! \nLink: t.me/addstickers/{full_pack_name}"
            raise

    def _validate_pack_name(self, pack_name):
        if not re.match(r'^[a-zA-Z0-9_]+$', pack_name):
            raise ValueError("Pack name must contain only letters, numbers, and underscores!")

    def _process_emojis(self, emoji_str):
        extracted_emojis = [c for c in emoji_str if unicodedata.category(c) == 'So']
        return extracted_emojis or ['😊']

    def _extract_file_id(self, file_id):
        # Extract only the first part if two IDs are passed
        file_id = file_id.split()[0]
        if not re.match(r'^[A-Za-z0-9_-]{20,}$', file_id):
            raise ValueError("Invalid file_id format!")
        return file_id

    def _success_response(self, message):
        return [InlineQueryResultArticle(
            id="success",
            title="Sticker Added Successfully",
            description="Your sticker has been added to the pack",
            input_message_content=InputTextMessageContent(message)
        )]

    def _error_response(self, error):
        error_msg = f"❌ Error: {str(error)}"
        return [InlineQueryResultArticle(
            id="error",
            title="Operation Failed",
            description=str(error),
            input_message_content=InputTextMessageContent(error_msg)
        )]
