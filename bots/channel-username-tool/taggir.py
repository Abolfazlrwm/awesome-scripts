# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
import json
import logging
import os
import re
from datetime import datetime
from telethon import TelegramClient, events
from telethon.tl.functions.channels import CreateChannelRequest, UpdateUsernameRequest
from telethon.errors import FloodWaitError, UsernameOccupiedError, UsernameInvalidError, PeerIdInvalidError, ChannelsTooMuchError

logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO,
    handlers=[
        logging.FileHandler("bot.log"),
        logging.StreamHandler()
    ]
)
x1 = logging.getLogger(__name__)

a1 = 0  #app id  to bezar
a2 = "************"   #api hash to bezar
a3 = "+98**********" #shomarat bezar

b1 = "channel_ids.json"
b2 = 2
b3 = 120
b4 = [7312825701]  #id adadi admin bezar
tg_morteza1 = False
tg_morteza2 = None
tg_morteza3 = None

c1 = TelegramClient("my_account", a1, a2)

def d1(e1):
    if not e1.startswith('@'):
        return False
    e1 = e1[1:]
    return bool(re.match(r'^[A-Za-z0-9_]{5,}$', e1))

def d2():
    if os.path.exists(b1):
        try:
            with open(b1, 'r') as f1:
                return json.load(f1)
        except Exception as e2:
            x1.error(f"Error reading {b1}: {e2}")
            return []
    return []

def d3(g1):
    try:
        with open(b1, 'w') as f1:
            json.dump(g1, f1, ensure_ascii=False, indent=4)
    except Exception as e2:
        x1.error(f"Error saving {b1}: {e2}")

async def h1(c1, i1, j1, k1, l1, m1):
    global tg_morteza3
    if not tg_morteza1:
        return
    n1 = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    o1 = f"```bash\n"
    o1 += f"┌────────────────── گزارش ربات ──────────────────┐\n"
    o1 += f"│ 🕒 زمان: {n1}\n"
    o1 += f"├────────────────────────────────────────────────┤\n"
    o1 += f"│ 🔍 تعداد آیدی‌های بررسی‌شده: {len(j1)}\n"
    o1 += f"│ ✅ کانال‌های ساخته‌شده: {len(k1)}\n"
    o1 += f"│ 🗑️ آیدی‌های حذف‌شده: {len(l1)}\n"
    o1 += f"│ ❌ خطاها: {len(m1)}\n"
    o1 += f"├────────────────────────────────────────────────┤\n"

    if k1:
        o1 += f"│ 📢 کانال‌های جدید:\n"
        for p1, q1 in enumerate(k1, 1):
            o1 += f"│   {p1}. {q1} ✅\n"
    else:
        o1 += f"│ 📢 بدون کانال جدید\n"

    if l1:
        o1 += f"│ 🗑️ آیدی‌های حذف‌شده:\n"
        for p1, r1 in enumerate(l1, 1):
            o1 += f"│   {p1}. {r1}\n"
    else:
        o1 += f"│ 🗑️ بدون حذف آیدی\n"

    if m1:
        o1 += f"│ ⚠️ خطاها:\n"
        for p1, s1 in enumerate(m1, 1):
            o1 += f"│   {p1}. {s1}\n"
    else:
        o1 += f"│ ⚠️ بدون خطا\n"

    o1 += f"├────────────────────────────────────────────────┤\n"
    o1 += f"│ 🚀 وضعیت: ربات فعال و آماده برای چرخه بعدی\n"
    o1 += f"└────────────────────────────────────────────────┘\n"
    o1 += f"```\n"

    x1.info(f"Report:\n{o1}")
    try:
        if i1:
            if tg_morteza3:
                try:
                    await c1.delete_messages(i1, [tg_morteza3])
                except Exception as e2:
                    x1.error(f"Failed to delete report message {tg_morteza3}: {e2}")
            t1 = await c1.send_message(i1, o1, parse_mode='markdown')
            tg_morteza3 = t1.id
        else:
            x1.error("No report recipient set.")
    except PeerIdInvalidError:
        x1.error(f"Cannot send report to {i1}: PeerIdInvalid. Please send a message to the bot first.")
        tg_morteza3 = None
    except Exception as e2:
        x1.error(f"Error sending report to {i1}: {e2}")
        tg_morteza3 = None

@c1.on(events.NewMessage(pattern=r'^شروع\b', incoming=True))
async def u1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    w1 = f"```bash\n"
    w1 += f"┌────────────────── راهنمای ربات ────────────────┐\n"
    w1 += f"│ 😎 خوش اومدی! این ربات برای مدیریت آیدی‌ها و ساخت کانال طراحی شده.\n"
    w1 += f"├────────────────────────────────────────────────┤\n"
    w1 += f"│ 📋 دستورات موجود:\n"
    w1 += f"│ 1. شروع\n"
    w1 += f"│    ➡️ نمایش این راهنما\n"
    w1 += f"│    مثال: شروع\n"
    w1 += f"│ 2. شروع عملیات\n"
    w1 += f"│    ➡️ شروع بررسی دوره‌ای آیدی‌ها\n"
    w1 += f"│    مثال: شروع عملیات\n"
    w1 += f"│ 3. توقف عملیات\n"
    w1 += f"│    ➡️ توقف بررسی دوره‌ای\n"
    w1 += f"│    مثال: توقف عملیات\n"
    w1 += f"│ 4. افزودن\n"
    w1 += f"│    ➡️ اضافه کردن آیدی‌ها به لیست\n"
    w1 += f"│    مثال: افزودن @id1 @id2\n"
    w1 += f"│ 5. حذف\n"
    w1 += f"│    ➡️ حذف یک آیدی از لیست\n"
    w1 += f"│    مثال: حذف @id1\n"
    w1 += f"│ 6. لیست\n"
    w1 += f"│    ➡️ نمایش همه آیدی‌های ذخیره‌شده\n"
    w1 += f"│    مثال: لیست\n"
    w1 += f"│ 7. بررسی\n"
    w1 += f"│    ➡️ بررسی فوری آیدی‌ها برای ساخت کانال\n"
    w1 += f"│    مثال: بررسی\n"
    w1 += f"│ 8. تنظیم_دوره\n"
    w1 += f"│    ➡️ تنظیم بازه بررسی دوره‌ای (به ثانیه)\n"
    w1 += f"│    مثال: تنظیم_دوره 300\n"
    w1 += f"├────────────────────────────────────────────────┤\n"
    w1 += f"│ 🚀 ربات به‌صورت دوره‌ای آیدی‌ها رو بررسی می‌کنه!\n"
    w1 += f"└────────────────────────────────────────────────┘\n"
    w1 += f"```\n"
    await v1.reply(w1, parse_mode='markdown')

@c1.on(events.NewMessage(pattern=r'^شروع عملیات\b', incoming=True))
async def x2(v1):
    global tg_morteza1, tg_morteza2, tg_morteza3
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    tg_morteza1 = True
    tg_morteza2 = v1.sender_id
    tg_morteza3 = None
    await v1.reply("عملیات بررسی آیدی‌ها آغاز شد! 🚀 گزارش‌ها برای شما ارسال می‌شوند.")
    if not any(task.get_name() == "y1" for task in asyncio.all_tasks()):
        c1.loop.create_task(y1(c1), name="y1")

@c1.on(events.NewMessage(pattern=r'^توقف عملیات\b', incoming=True))
async def z1(v1):
    global tg_morteza1, tg_morteza2, tg_morteza3
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    tg_morteza1 = False
    tg_morteza2 = None
    tg_morteza3 = None
    await v1.reply("عملیات بررسی آیدی‌ها متوقف شد! 🛑 برای شروع دوباره، 'شروع عملیات' را ارسال کنید.")

@c1.on(events.NewMessage(pattern=r'^افزودن\b', incoming=True))
async def aa1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    ab1 = v1.message.text.split()
    if len(ab1) < 2:
        await v1.reply("لطفاً آیدی‌ها رو وارد کن (مثال: افزودن @id1 @id2)")
        return

    ac1 = ab1[1:]
    ad1 = d2()
    ae1 = []
    af1 = []

    for ag1 in ac1:
        if not d1(ag1):
            af1.append(ag1)
            continue
        if ag1 not in ad1:
            ad1.append(ag1)
            ae1.append(ag1)

    d3(ad1)
    ah1 = ""
    if ae1:
        ah1 += f"آیدی‌های زیر اضافه شدن:\n{', '.join(ae1)}\n"
    if af1:
        ah1 += f"آیدی‌های زیر نامعتبرن (باید با @ شروع بشن و حداقل ۵ کاراکتر شامل حروف، اعداد یا _ باشن):\n{', '.join(af1)}"
    if not ae1 and not af1:
        ah1 = "هیچ آیدی جدیدی اضافه نشد! 😕"
    await v1.reply(ah1)

@c1.on(events.NewMessage(pattern=r'^حذف\b', incoming=True))
async def ai1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    ab1 = v1.message.text.split()
    if len(ab1) < 2:
        await v1.reply("لطفاً آیدی رو وارد کن (مثال: حذف @id1)")
        return

    ag1 = ab1[1]
    ad1 = d2()
    if ag1 in ad1:
        ad1.remove(ag1)
        d3(ad1)
        await v1.reply(f"آیدی {ag1} با موفقیت حذف شد! 🗑️")
    else:
        await v1.reply(f"آیدی {ag1} توی لیست نیست! 🤔")

@c1.on(events.NewMessage(pattern=r'^لیست\b', incoming=True))
async def aj1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    ad1 = d2()
    if ad1:
        await v1.reply(f"📜 لیست آیدی‌ها:\n{', '.join(ad1)}")
    else:
        await v1.reply("لیست آیدی‌ها خالیه! 😕")

@c1.on(events.NewMessage(pattern=r'^بررسی\b', incoming=True))
async def ak1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    await al1(c1, v1.sender_id)

@c1.on(events.NewMessage(pattern=r'^تنظیم_دوره\b', incoming=True))
async def am1(v1):
    if v1.sender_id not in b4:
        await v1.reply("شما دسترسی به استفاده از این ربات ندارید! 🚫")
        return
    ab1 = v1.message.text.split()
    if len(ab1) < 2:
        await v1.reply("لطفاً بازه زمانی (به ثانیه) رو وارد کن (مثال: تنظیم_دوره 300)")
        return
    try:
        an1 = int(ab1[1])
        if an1 < 60:
            await v1.reply("بازه زمانی باید حداقل ۶۰ ثانیه باشه! 😕")
            return
        global b3
        b3 = an1
        await v1.reply(f"بازه بررسی دوره‌ای به {an1} ثانیه تنظیم شد! 🕒")
    except ValueError:
        await v1.reply("لطفاً یک عدد معتبر وارد کن! 🤔")

async def al1(c1, i1):
    if not tg_morteza1:
        return
    ao1 = d2()
    ap1 = []
    aq1 = []
    ar1 = []
    as1 = []

    if not ao1:
        try:
            await c1.send_message(i1, "لیست آیدی‌ها خالیه! 😕")
        except PeerIdInvalidError:
            x1.error(f"Cannot send message to {i1}: PeerIdInvalid. Please send a message to the bot first.")
        return

    ap1 = ao1[:]
    at1 = 0

    for ag1 in ao1[:]:
        if at1 >= b2:
            as1.append(f"حداکثر {b2} کانال در این چرخه ساخته شد. بعداً ادامه میدیم! ⏳")
            break

        au1 = False
        for _ in range(3):
            try:
                await c1.get_entity(ag1)
                as1.append(f"آیدی {ag1} قبلاً گرفته شده! ❌")
                break
            except ValueError:
                au1 = True
            except FloodWaitError as e2:
                x1.warning(f"FloodWait in checking {ag1}: waiting for {e2.seconds} seconds")
                as1.append(f"محدودیت تلگرام برای بررسی {ag1}: صبر برای {e2.seconds} ثانیه ⏳")
                await asyncio.sleep(e2.seconds)
            except PeerIdInvalidError:
                as1.append(f"آیدی {ag1} نامعتبر یا غیرقابل دسترسیه! ❌")
                ar1.append(ag1)
                ao1.remove(ag1)
                d3(ao1)
                break
            except Exception as e2:
                if "network" in str(e2).lower():
                    x1.error(f"Network error in checking {ag1}: {e2}")
                    as1.append(f"خطای شبکه در بررسی {ag1}. بعداً امتحان کن! 🌐")
                    break
                else:
                    x1.error(f"Unexpected error in checking {ag1}: {e2}")
                    as1.append(f"خطای غیرمنتظره در بررسی {ag1}: {str(e2)} ❗")
                    break
            await asyncio.sleep(1)

        if au1:
            try:
                av1 = await c1(CreateChannelRequest(
                    title=f"کانال {ag1}",
                    about="کانال جدید",
                    broadcast=True
                ))
                aw1 = av1.chats[0].id
                await c1(UpdateUsernameRequest(
                    channel=aw1,
                    username=ag1[1:]
                ))
                aq1.append(ag1)
                at1 += 1
                ao1.remove(ag1)
                d3(ao1)
            except FloodWaitError as e2:
                x1.warning(f"FloodWait in creating channel {ag1}: waiting for {e2.seconds} seconds")
                as1.append(f"محدودیت تلگرام برای {ag1}: صبر برای {e2.seconds} ثانیه ⏳")
                await asyncio.sleep(e2.seconds)
            except UsernameOccupiedError:
                as1.append(f"آیدی {ag1} در لحظه آخر گرفته شد! ❌")
            except UsernameInvalidError:
                as1.append(f"آیدی {ag1} نامعتبره! ❌")
                ar1.append(ag1)
                ao1.remove(ag1)
                d3(ao1)
            except ChannelsTooMuchError:
                as1.append("تعداد کانال‌های حساب شما به حداکثر رسیده! لطفاً برخی کانال‌ها رو حذف کن. 🚫")
                break
            except Exception as e2:
                x1.error(f"Error in creating channel or setting username {ag1}: {e2}")
                as1.append(f"خطا در ساخت کانال با آیدی {ag1}: {str(e2)} ❗")
            await asyncio.sleep(5)

    await h1(c1, i1, ap1, aq1, ar1, as1)

async def y1(c1):
    if not tg_morteza1:
        return
    ax1 = 0
    ay1 = 3
    while tg_morteza1:
        try:
            ao1 = d2()
            if ao1:
                x1.info("Starting periodic ID check")
                await al1(c1, tg_morteza2)
                ax1 = 0
            else:
                x1.info("No IDs to check in periodic check")
                if tg_morteza2:
                    try:
                        await c1.send_message(tg_morteza2, "لیست آیدی‌ها خالیه! 😕")
                    except PeerIdInvalidError:
                        x1.error(f"Cannot send empty list message to {tg_morteza2}: PeerIdInvalid")
                    except Exception as e2:
                        x1.error(f"Error sending empty list message: {e2}")
            await asyncio.sleep(b3)
        except Exception as e2:
            x1.error(f"Error in periodic check: {e2}")
            if tg_morteza2:
                try:
                    await c1.send_message(tg_morteza2, f"خطا در بررسی دوره‌ای: {str(e2)} ❗")
                except PeerIdInvalidError:
                    x1.error(f"Cannot send error message to {tg_morteza2}: PeerIdInvalid")
                except Exception as e2:
                    x1.error(f"Error sending error message: {e2}")
            if isinstance(e2, FloodWaitError):
                ax1 += 1
                x1.warning(f"FloodWait in periodic check: waiting for {e2.seconds} seconds")
                try:
                    if tg_morteza2:
                        await c1.send_message(tg_morteza2, f"محدودیت تلگرام در بررسی دوره‌ای: صبر برای {e2.seconds} ثانیه ⏳")
                except PeerIdInvalidError:
                    x1.error(f"Cannot send FloodWait message to {tg_morteza2}: PeerIdInvalid")
                except Exception as e2:
                    x1.error(f"Error sending FloodWait message: {e2}")
                await asyncio.sleep(e2.seconds)
                if ax1 >= ay1:
                    x1.error("Multiple FloodWaits detected. Pausing bot for 1 hour.")
                    try:
                        if tg_morteza2:
                            await c1.send_message(tg_morteza2, "چندین محدودیت تلگرام رخ داده. ربات برای ۱ ساعت متوقف میشه! 😴")
                    except PeerIdInvalidError:
                        x1.error(f"Cannot send pause message to {tg_morteza2}: PeerIdInvalid")
                    except Exception as e2:
                        x1.error(f"Error sending pause message: {e2}")
                    await asyncio.sleep(3600)
                    ax1 = 0
            else:
                await asyncio.sleep(b3)

async def az1():
    try:
        await c1.start(phone=a3)
        x1.info("Bot started successfully")
        await c1.run_until_disconnected()
    except Exception as e2:
        x1.error(f"Error in running bot: {e2}")
        if tg_morteza2:
            try:
                await c1.send_message(tg_morteza2, f"خطا در اجرای ربات: {str(e2)} ❗")
            except PeerIdInvalidError:
                x1.error(f"Cannot send error message to {tg_morteza2}: PeerIdInvalid")
                print(f"خطا در ارسال پیام به {tg_morteza2}. لطفاً از حساب ادمین به ربات پیام دهید.")
            except Exception as e2:
                x1.error(f"Error sending error message: {e2}")
        await c1.disconnect()

if __name__ == "__main__":
    c1.loop.run_until_complete(az1())