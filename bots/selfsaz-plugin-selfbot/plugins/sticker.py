# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events, types, functions
from telethon.errors import *
import os
import asyncio
from pathlib import Path

HELP = """
🎨 **Sticker Set Management Plugin** 🎨

▬▬▬▬▬▬▬▬▬▬▬▬
📌 **Key Features**:
• Create custom sticker sets
• Add stickers to existing sets
• List your created sticker sets
• Delete sticker sets

▬▬▬▬▬▬▬▬▬▬▬▬
🛠 **Main Commands**:
• `/createstickerpack <name> <title>` - Create a new sticker set
• `/addsticker <pack_name>` - Add sticker to an existing set (reply to sticker)
• `/mystickers` - List all your sticker sets
• `/deletestickerpack <name>` - Delete a sticker set

▬▬▬▬▬▬▬▬▬▬▬▬
✨ **Usage Example**:
1. Reply to a sticker and send:
   `/addsticker my_cool_pack`
2. Or create a new pack:
   `/createstickerpack my_cool_pack "My Cool Pack"`
"""

class StickerManagerPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/createstickerpack(?:\s+(.+?)\s+(.+))?$'))
        async def create_stickerpack_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ Please reply to a sticker to create a pack")
                return

            match = event.pattern_match
            if not match or not match.group(1) or not match.group(2):
                await event.reply("❌ Usage: /createstickerpack <short_name> <title>")
                return

            short_name = match.group(1).strip()
            title = match.group(2).strip()

            if not short_name.endswith("_by_" + (await self.client.get_me()).username):
                short_name += "_by_" + (await self.client.get_me()).username

            try:
                reply = await event.get_reply_message()
                if not reply.sticker:
                    await event.reply("❌ The replied message is not a sticker")
                    return

                sticker = reply.sticker
                input_doc = types.InputDocument(
                    id=sticker.id,
                    access_hash=sticker.access_hash,
                    file_reference=sticker.file_reference
                )

                result = await self.client(functions.stickers.CreateStickerSetRequest(
                    user_id=await self.client.get_input_entity(event.sender_id),
                    title=title,
                    short_name=short_name,
                    stickers=[types.InputStickerSetItem(
                        document=input_doc,
                        emojis=sticker.emoji or "🤔"
                    )],
                    emojis=True
                ))

                await event.reply(f"✅ Sticker pack created: {result.set.short_name}")
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^/addsticker(?:\s+(.+))?$'))
        async def add_sticker_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            if not event.is_reply:
                await event.reply("❌ Please reply to a sticker to add to pack")
                return

            pack_name = event.pattern_match.group(1)
            if not pack_name:
                await event.reply("❌ Usage: /addsticker <pack_name>")
                return

            try:
                reply = await event.get_reply_message()
                if not reply.sticker:
                    await event.reply("❌ The replied message is not a sticker")
                    return

                sticker = reply.sticker
                input_doc = types.InputDocument(
                    id=sticker.id,
                    access_hash=sticker.access_hash,
                    file_reference=sticker.file_reference
                )

                # Get the sticker set first
                try:
                    stickerset = await self.client(functions.messages.GetStickerSetRequest(
                        stickerset=types.InputStickerSetShortName(short_name=pack_name),
                        hash=0
                    ))
                except StickersetInvalidError:
                    await event.reply("❌ Sticker pack not found. Create it first with /createstickerpack")
                    return

                # Add the sticker to the set
                result = await self.client(functions.stickers.AddStickerToSetRequest(
                    stickerset=types.InputStickerSetShortName(short_name=pack_name),
                    sticker=types.InputStickerSetItem(
                        document=input_doc,
                        emoji=sticker.emoji or "🤔"
                    )
                ))

                await event.reply(f"✅ Sticker added to pack: {pack_name}")
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^/mystickers$'))
        async def list_stickers_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            try:
                result = await self.client(functions.messages.GetAllStickersRequest(hash=0))
                if not result.sets:
                    await event.reply("❌ You haven't created any sticker packs yet")
                    return

                response = "📚 Your Sticker Packs:\n\n"
                for stickerset in result.sets:
                    response += f"• {stickset.title} ({stickset.short_name})\n"

                await event.reply(response)
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")

        @self.client.on(events.NewMessage(pattern=r'^/deletestickerpack(?:\s+(.+))?$'))
        async def delete_stickerpack_handler(event):
            if str(event.sender_id) != self.owner_id:
                return

            pack_name = event.pattern_match.group(1)
            if not pack_name:
                await event.reply("❌ Usage: /deletestickerpack <pack_name>")
                return

            try:
                await self.client(functions.stickers.DeleteStickerSetRequest(
                    stickerset=types.InputStickerSetShortName(short_name=pack_name)
                ))
                await event.reply(f"✅ Sticker pack deleted: {pack_name}")
            except Exception as e:
                await event.reply(f"❌ Error: {str(e)}")