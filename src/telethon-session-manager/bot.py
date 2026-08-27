import asyncio
import json
import os
import re
from typing import Dict, List

from telethon import Button, TelegramClient, events
from telethon.errors import SessionPasswordNeededError, PhoneCodeInvalidError
from telethon.sessions import StringSession
from telethon.tl.types import ReactionEmoji

BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"       # توکن ربات از @BotFather
API_ID = 12345678                       # از my.telegram.org دریافت کنید
API_HASH = "YOUR_API_HASH_HERE"         # از my.telegram.org دریافت کنید
ACCOUNTS_FILE = "accounts.json"

user_states = {}
user_temp_data = {}

def load_accounts() -> List[Dict]:
    if os.path.exists(ACCOUNTS_FILE):
        with open(ACCOUNTS_FILE, "r", encoding="utf-8") as f:
            try:
                return json.load(f)
            except:
                return []
    return []

def save_accounts(accounts: List[Dict]):
    with open(ACCOUNTS_FILE, "w", encoding="utf-8") as f:
        json.dump(accounts, f, indent=4, ensure_ascii=False)

def parse_post_link(link: str):
    match = re.search(r"t\.me/([^/]+)/(\d+)", link)
    if match:
        return match.group(1), int(match.group(2))
    match2 = re.search(r"t\.me/c/(\d+)/(\d+)", link)
    if match2:
        return int(match2.group(1)), int(match2.group(2))
    return None, None

async def get_client(account: Dict):
    client = TelegramClient(
        StringSession(account["session_string"]),
        account["api_id"],
        account["api_hash"],
    )
    await client.start()
    return client

async def send_main_menu_new(event):
    text = (
        "✨ <b>به ربات مدیریت حساب‌های تلگرام خوش آمدید!</b> ✨\n\n"
        "با استفاده از این ربات می‌توانید:\n"
        "➕ <b>اکانت</b> اضافه کنید\n"
        "⚡️ <b>عملیات</b> جوین و ری‌اکشن انجام دهید\n"
        "📋 <b>لیست</b> اکانت‌ها را مشاهده و مدیریت کنید\n\n"
        "⬇️ از دکمه‌های زیر استفاده کنید:"
    )
    buttons = [
        [Button.inline("➕ افزودن اکانت", b"add_account")],
        [Button.inline("⚡️ انجام عملیات", b"operations")],
        [Button.inline("📋 لیست اکانت‌ها", b"list_accounts")],
    ]
    await event.respond(text, buttons=buttons, parse_mode="html")

async def send_main_menu_edit(event):
    text = (
        "✨ <b>به ربات مدیریت حساب‌های تلگرام خوش آمدید!</b> ✨\n\n"
        "با استفاده از این ربات می‌توانید:\n"
        "➕ <b>اکانت</b> اضافه کنید\n"
        "⚡️ <b>عملیات</b> جوین و ری‌اکشن انجام دهید\n"
        "📋 <b>لیست</b> اکانت‌ها را مشاهده و مدیریت کنید\n\n"
        "⬇️ از دکمه‌های زیر استفاده کنید:"
    )
    buttons = [
        [Button.inline("➕ افزودن اکانت", b"add_account")],
        [Button.inline("⚡️ انجام عملیات", b"operations")],
        [Button.inline("📋 لیست اکانت‌ها", b"list_accounts")],
    ]
    await event.edit(text, buttons=buttons, parse_mode="html")

async def send_operations_menu(event):
    buttons = [
        [Button.inline("🚀 جوین (Join)", b"join_action")],
        [Button.inline("❤️ ری‌اکشن (Reaction)", b"reaction_action")],
        [Button.inline("🔙 برگشت", b"main_menu")],
    ]
    await event.edit("⚡️ <b>منوی عملیات</b> ⚡️\nنوع عملیات را انتخاب کنید:", buttons=buttons, parse_mode="html")

@events.register(events.NewMessage(pattern="/start"))
async def start_handler(event):
    await send_main_menu_new(event)

@events.register(events.CallbackQuery)
async def callback_handler(event):
    data = event.data.decode()
    user_id = event.sender_id

    if data == "main_menu":
        await send_main_menu_edit(event)
        return

    if data == "add_account":
        user_states[user_id] = "waiting_api_id"
        user_temp_data[user_id] = {}
        await event.edit(
            "🔹 <b>مرحله ۱ از ۵</b> 🔹\nلطفاً <b>API ID</b> خود را وارد کنید (فقط عدد):",
            parse_mode="html"
        )
        return

    if data == "operations":
        await send_operations_menu(event)
        return

    if data == "join_action":
        user_states[user_id] = "waiting_join_link"
        await event.edit(
            "🔗 لطفاً <b>لینک گروه یا کانال</b> را وارد کنید:\nمثال: https://t.me/username",
            parse_mode="html"
        )
        return

    if data == "reaction_action":
        user_states[user_id] = "waiting_react_link"
        await event.edit(
            "🔗 لطفاً <b>لینک پست</b> را وارد کنید:\nمثال: https://t.me/username/123",
            parse_mode="html"
        )
        return

    if data == "list_accounts":
        accounts = load_accounts()
        if not accounts:
            text = "📭 <b>هیچ اکانتی یافت نشد.</b>"
            buttons = [[Button.inline("➕ افزودن اکانت", b"add_account")]]
            await event.edit(text, buttons=buttons, parse_mode="html")
            return

        text = "📋 <b>لیست اکانت‌ها:</b>\n"
        for acc in accounts:
            text += f"🆔 {acc['id']} | 📱 <code>{acc['phone']}</code>\n"
        text += f"\nتعداد کل: {len(accounts)}"
        buttons = [
            [Button.inline("🗑 حذف اکانت", b"delete_account")],
            [Button.inline("🔙 برگشت", b"main_menu")],
        ]
        await event.edit(text, buttons=buttons, parse_mode="html")
        return

    if data == "delete_account":
        accounts = load_accounts()
        if not accounts:
            await event.edit("❌ هیچ اکانتی برای حذف وجود ندارد.", parse_mode="html")
            await asyncio.sleep(1)
            await send_main_menu_edit(event)
            return

        text = "🗑 <b>حذف اکانت</b>\n\n"
        for acc in accounts:
            text += f"🆔 {acc['id']} | 📱 <code>{acc['phone']}</code>\n"
        text += "\nلطفاً <b>شناسه (ID)</b> اکانت مورد نظر را وارد کنید:"
        user_states[user_id] = "waiting_delete_id"
        await event.edit(text, parse_mode="html")
        return

    await event.answer("دکمه ناشناخته!")

@events.register(events.NewMessage(func=lambda e: e.is_private))
async def message_handler(event):
    user_id = event.sender_id
    text = event.raw_text
    state = user_states.get(user_id)

    if not state:
        return

    if state == "waiting_api_id":
        try:
            api_id = int(text.strip())
            user_temp_data[user_id]["api_id"] = api_id
            user_states[user_id] = "waiting_api_hash"
            await event.respond(
                "🔹 <b>مرحله ۲ از ۵</b> 🔹\nلطفاً <b>API HASH</b> خود را وارد کنید:",
                parse_mode="html"
            )
        except ValueError:
            await event.respond("❌ <b>خطا!</b> API ID باید عدد باشد. دوباره وارد کنید:", parse_mode="html")
        return

    if state == "waiting_api_hash":
        user_temp_data[user_id]["api_hash"] = text.strip()
        user_states[user_id] = "waiting_phone"
        await event.respond(
            "🔹 <b>مرحله ۳ از ۵</b> 🔹\nلطفاً <b>شماره تلفن</b> خود را (با کد کشور) وارد کنید:\nمثال: +989123456789",
            parse_mode="html"
        )
        return

    if state == "waiting_phone":
        phone = text.strip()
        user_temp_data[user_id]["phone"] = phone
        api_id = user_temp_data[user_id]["api_id"]
        api_hash = user_temp_data[user_id]["api_hash"]

        try:
            temp_client = TelegramClient(StringSession(), api_id, api_hash)
            await temp_client.start(phone=phone)
            await temp_client.send_code_request(phone)
            user_temp_data[user_id]["temp_client"] = temp_client
            user_states[user_id] = "waiting_code"
            await event.respond(
                "🔹 <b>مرحله ۴ از ۵</b> 🔹\nکد تأیید به تلگرام شما ارسال شد.\nلطفاً <b>کد</b> را وارد کنید:",
                parse_mode="html"
            )
        except Exception as e:
            await event.respond(f"❌ خطا در ارسال کد: {str(e)}\nلطفاً دوباره شماره را وارد کنید:", parse_mode="html")
            if "temp_client" in user_temp_data.get(user_id, {}):
                await user_temp_data[user_id]["temp_client"].disconnect()
        return

    if state == "waiting_code":
        code = text.strip()
        temp_client = user_temp_data[user_id].get("temp_client")
        if not temp_client:
            await event.respond("❌ نشست منقضی شد. از ابتدا تلاش کنید. /start", parse_mode="html")
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            return

        try:
            await temp_client.sign_in(code=code)
            session_string = temp_client.session.save()
            await temp_client.disconnect()

            accounts = load_accounts()
            new_id = max([acc["id"] for acc in accounts], default=0) + 1
            accounts.append({
                "id": new_id,
                "phone": user_temp_data[user_id]["phone"],
                "api_id": user_temp_data[user_id]["api_id"],
                "api_hash": user_temp_data[user_id]["api_hash"],
                "session_string": session_string,
            })
            save_accounts(accounts)

            await event.respond(
                f"✅ <b>اکانت با موفقیت اضافه شد!</b>\nشماره: <code>{user_temp_data[user_id]['phone']}</code>\nشناسه: {new_id}",
                parse_mode="html"
            )
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            await send_main_menu_new(event)

        except SessionPasswordNeededError:
            user_states[user_id] = "waiting_password"
            await event.respond(
                "🔹 <b>مرحله ۵ از ۵</b> 🔹\nاکانت شما <b>رمز دو مرحله‌ای (2FA)</b> دارد.\nلطفاً رمز عبور را وارد کنید:",
                parse_mode="html"
            )
        except PhoneCodeInvalidError:
            await event.respond("❌ کد وارد شده اشتباه است. دوباره تلاش کنید:", parse_mode="html")
        except Exception as e:
            await event.respond(f"❌ خطای ناشناخته: {str(e)}\nاز ابتدا تلاش کنید. /start", parse_mode="html")
            await temp_client.disconnect()
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
        return

    if state == "waiting_password":
        password = text.strip()
        temp_client = user_temp_data[user_id].get("temp_client")
        if not temp_client:
            await event.respond("❌ نشست منقضی شد. از ابتدا تلاش کنید.", parse_mode="html")
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            return

        try:
            await temp_client.sign_in(password=password)
            session_string = temp_client.session.save()
            await temp_client.disconnect()

            accounts = load_accounts()
            new_id = max([acc["id"] for acc in accounts], default=0) + 1
            accounts.append({
                "id": new_id,
                "phone": user_temp_data[user_id]["phone"],
                "api_id": user_temp_data[user_id]["api_id"],
                "api_hash": user_temp_data[user_id]["api_hash"],
                "session_string": session_string,
            })
            save_accounts(accounts)

            await event.respond(
                f"✅ <b>اکانت با موفقیت اضافه شد!</b>\nشماره: <code>{user_temp_data[user_id]['phone']}</code>\nشناسه: {new_id}",
                parse_mode="html"
            )
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            await send_main_menu_new(event)

        except Exception as e:
            await event.respond(f"❌ رمز عبور اشتباه است یا خطا: {str(e)}\nدوباره وارد کنید:", parse_mode="html")
        return

    if state == "waiting_join_link":
        link = text.strip()
        accounts = load_accounts()
        if not accounts:
            await event.respond("❌ هیچ اکانتی وجود ندارد! ابتدا اکانت اضافه کنید.", parse_mode="html")
            user_states.pop(user_id, None)
            await send_main_menu_new(event)
            return

        user_temp_data[user_id]["join_link"] = link
        user_states[user_id] = "waiting_join_count"
        await event.respond(
            f"🔹 تعداد اکانت‌های موجود: <b>{len(accounts)}</b>\nچند تا از این اکانت‌ها باید جوین شوند؟ (عدد وارد کنید):",
            parse_mode="html"
        )
        return

    if state == "waiting_join_count":
        try:
            count = int(text.strip())
            accounts = load_accounts()
            if count > len(accounts):
                await event.respond(f"❌ فقط {len(accounts)} اکانت موجود است. عدد کوچکتری وارد کنید:", parse_mode="html")
                return
            if count <= 0:
                await event.respond("❌ عدد باید بزرگتر از صفر باشد:", parse_mode="html")
                return

            link = user_temp_data[user_id]["join_link"]
            selected_accounts = accounts[:count]

            await event.respond(
                f"🔄 در حال جوین کردن {count} اکانت به <code>{link}</code> ...",
                parse_mode="html"
            )

            success_count = 0
            fail_list = []
            for acc in selected_accounts:
                try:
                    client = await get_client(acc)
                    await client.join_channel(link)
                    await client.disconnect()
                    success_count += 1
                except Exception as e:
                    fail_list.append(f"{acc['phone']} : {str(e)[:30]}")

            result_text = f"✅ <b>نتیجه جوین:</b>\nموفق: {success_count} از {count}"
            if fail_list:
                result_text += f"\n❌ خطاها:\n" + "\n".join(fail_list)

            await event.respond(result_text, parse_mode="html")
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            await send_main_menu_new(event)

        except ValueError:
            await event.respond("❌ لطفاً فقط یک عدد وارد کنید:", parse_mode="html")
        return

    if state == "waiting_react_link":
        link = text.strip()
        chat_id, msg_id = parse_post_link(link)
        if not chat_id or not msg_id:
            await event.respond("❌ لینک معتبر نیست! لطفاً لینک پست را درست وارد کنید:", parse_mode="html")
            return

        accounts = load_accounts()
        if not accounts:
            await event.respond("❌ هیچ اکانتی وجود ندارد! ابتدا اکانت اضافه کنید.", parse_mode="html")
            user_states.pop(user_id, None)
            await send_main_menu_new(event)
            return

        user_temp_data[user_id]["react_link"] = link
        user_temp_data[user_id]["react_chat"] = chat_id
        user_temp_data[user_id]["react_msg"] = msg_id
        user_states[user_id] = "waiting_react_count"
        await event.respond(
            f"🔹 تعداد اکانت‌های موجود: <b>{len(accounts)}</b>\nچند تا از این اکانت‌ها ری‌اکشن بزنند؟ (عدد وارد کنید):",
            parse_mode="html"
        )
        return

    if state == "waiting_react_count":
        try:
            count = int(text.strip())
            accounts = load_accounts()
            if count > len(accounts):
                await event.respond(f"❌ فقط {len(accounts)} اکانت موجود است. عدد کوچکتری وارد کنید:", parse_mode="html")
                return
            if count <= 0:
                await event.respond("❌ عدد باید بزرگتر از صفر باشد:", parse_mode="html")
                return

            user_temp_data[user_id]["react_count"] = count
            user_states[user_id] = "waiting_react_emoji"
            await event.respond(
                "🎭 لطفاً <b>اموجی</b> ری‌اکشن مورد نظر را وارد کنید:\nمثال: 👍 یا ❤️ یا 🔥",
                parse_mode="html"
            )
        except ValueError:
            await event.respond("❌ لطفاً فقط یک عدد وارد کنید:", parse_mode="html")
        return

    if state == "waiting_react_emoji":
        emoji = text.strip()
        if not emoji:
            await event.respond("❌ لطفاً یک اموجی وارد کنید:", parse_mode="html")
            return

        count = user_temp_data[user_id]["react_count"]
        chat_id = user_temp_data[user_id]["react_chat"]
        msg_id = user_temp_data[user_id]["react_msg"]
        accounts = load_accounts()[:count]

        await event.respond(
            f"🔄 در حال ارسال ری‌اکشن <code>{emoji}</code> توسط {count} اکانت ...",
            parse_mode="html"
        )

        success_count = 0
        fail_list = []
        for acc in accounts:
            try:
                client = await get_client(acc)
                await client.send_reaction(chat_id, msg_id, reaction=[ReactionEmoji(emojis=[emoji])])
                await client.disconnect()
                success_count += 1
            except Exception as e:
                fail_list.append(f"{acc['phone']} : {str(e)[:30]}")

        result_text = f"✅ <b>نتیجه ری‌اکشن:</b>\nموفق: {success_count} از {count}"
        if fail_list:
            result_text += f"\n❌ خطاها:\n" + "\n".join(fail_list)

        await event.respond(result_text, parse_mode="html")
        user_states.pop(user_id, None)
        user_temp_data.pop(user_id, None)
        await send_main_menu_new(event)
        return

    if state == "waiting_delete_id":
        try:
            acc_id = int(text.strip())
            accounts = load_accounts()
            found = False
            for acc in accounts:
                if acc["id"] == acc_id:
                    accounts.remove(acc)
                    found = True
                    break
            if not found:
                await event.respond("❌ شناسه پیدا نشد. دوباره وارد کنید:", parse_mode="html")
                return

            save_accounts(accounts)
            await event.respond(f"✅ اکانت با شناسه {acc_id} با موفقیت حذف شد.", parse_mode="html")
            user_states.pop(user_id, None)
            user_temp_data.pop(user_id, None)
            await send_main_menu_new(event)

        except ValueError:
            await event.respond("❌ لطفاً فقط عدد (شناسه) وارد کنید:", parse_mode="html")
        return

async def main():
    print("✅ ربات با Telethon در حال راه‌اندازی...")
    bot = TelegramClient("bot", API_ID, API_HASH)
    await bot.start(bot_token=BOT_TOKEN)

    bot.add_event_handler(start_handler)
    bot.add_event_handler(callback_handler)
    bot.add_event_handler(message_handler)

    print("✅ ربات روشن شد! منتظر پیام‌ها هستم...")
    await bot.run_until_disconnected()

if __name__ == "__main__":
    asyncio.run(main())