# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

import os
from telethon import TelegramClient,events,Button
from telethon.functions import channels
from telethon.tl.types import InputMediaDice
from telethon.errors import *
from info import api_id,api_hash
from telethon.sessions import StringSession
from telethon import types
from telethon.tl.custom.message import 	Message
from telethon.tl.functions.messages import SendReactionRequest
from collections import defaultdict
from asyncio import sleep
from telethon.tl.types import InputMediaDice
import asyncio,pytz,json,datetime,sqlite3
from telethon.functions import channels
from telethon import connection
import random
os.system('clear')

class CustomDict:
    def __init__(self):
        self.data = {}
    def __setitem__(self, key, value_tuple):
        if isinstance(value_tuple, tuple) and len(value_tuple) == 2:
            if key not in self.data:
                self.data[key] = {}
            self.data[key][value_tuple[0]] = value_tuple[1]
        else:
            raise ValueError("Value must be a tuple with exactly two elements.")
    def __getitem__(self, key):
        if key in self.data:
            return self.data[key]
        else:
            raise KeyError(f"Key '{key}' not found.")
    def __repr__(self):
        return str(self.data)

def selectbot(cloumn):
    swq = sqlite3.connect('BotData.db')
    cq = swq.cursor()
    cq.execute(f'SELECT {str(cloumn)} FROM Bot')
    f = cq.fetchone()
    return f[0]

def addbot(cloumn: str,data: str):
    swq = sqlite3.connect('BotData.db')
    cq = swq.cursor()
    cq.execute(f'UPDATE Bot SET {cloumn} = "{data}"')
    f = cq.fetchone()
    swq.commit()

def sop(user:int):
    s = sqlite3.connect('BotData.db')
    c = s.cursor()
    c.execute(f'SELECT Sodday FROM Users WHERE ID = {user}')
    d = c.fetchone()
    return d[0]

if os.path.exists('session.txt') == False:
    acc = input('Enter StringSession: ')

if os.path.exists('session.txt') == False:
    with open('session.txt','w') as file:
        file.write(f'{acc}')

with open('session.txt','r') as file:
    session = file.read()

#توکن بات
token = ''
#آیدی عددی مالک ها
owners = []
#آیدی عددی گروه
groupid = 
#آیدی عددی کانال
channelid = -1001234567
#لینک کانال
channel_link = 'https://t.me/Sourrce_kade'

helper = 0
admins = {}
admins_title = []
admins_num = 0
admins_ban = {7369338105: '༊\u200c࿐ྀུ 🎲🎳🎯 دوئـلٰ میـکـ\u200cٰسْ ᝰ\n\u200c\n🚬ورودیـ\xa0 ☞ {num}\n🔥خروجیـ ☜\xa0 {dual}\n\nᝰ❕🖤 شرکتـ کنندگانـ❀↯.',5885578082: '༊‌࿐ྀུ 🎲🎳🎯 دوئـلٰ میـکـ‌ٰسْ ᝰ\n\n🌬️ ورودیـ  ☞ {num}\n🤍خروجیـ ☜  {dual}ᝰ\n\n 🌝 شرکتـ کنندگانـ❀↯.',6751824694:'༊\u200c࿐ྀུ 🎲🎳🎯 دوئـلٰ میـکـ\u200cٰسْ ᝰ\n\u200c\n🚬ورودیـ\xa0 ☞ {num}\n🔥خروجیـ ☜\xa0 {dual}\n\nᝰ❕🖤 شرکتـ کنندگانـ❀↯.'}

users = {}
users_name = {}
users_num = 0
users_list = []

mesa = {}
ban_time = {}
ch_m = {}

ban_num = {}

names = defaultdict(list)
ban_id = {}
num_id = {}
banmes = defaultdict(bool)
banmesnum = {}
mesban = {}

close_ban = defaultdict(bool)
user_dual = {}
user_dual_a = {}
df = defaultdict(bool)
user_num_daul = {}
dual_true = True
mes_maf = 0
dyal_false = False
dualmes = defaultdict(bool)

#lo = 0
po = 9
ban_zrf = CustomDict()
channel = 0
v = True
dualnum = 0
timer = 0
dualv = 0

f_dual1 = {}
f_dual2 = {}
f_dual3 = {}

retime = False

hh = False
ff = {}
c = defaultdict(bool)
cc = defaultdict(bool)
admin_sod_day = {7369338105: 0,6751824694:0,5885578082:0}
admin_sod = {7369338105: 0,6751824694:0,5885578082:0}
admin_ban_mes = {}
admin_dual_mes = {}
daul_num = {}
close_dual_i = {}
userm1 = {}
userm2 = {}
sponser = False
user1d = defaultdict(bool)
user1b = defaultdict(bool)
user1da = defaultdict(bool)
user2d = defaultdict(bool)
user2b = defaultdict(bool)
user2da = defaultdict(bool)
bot_on_off = False

bot = TelegramClient('DualBot4',api_id,api_hash)

client = TelegramClient(StringSession(session),api_id,api_hash,device_model='Poco x3 pro')

public_button = bot.build_reply_markup([[Button.text('پروفایل👤',resize=True)],[Button.text('برترین📊'),Button.text('تنظیم اسم📝')],[Button.text('پشتیبانی ☎️')],[Button.text('راهنما📚')]])
priavte_button = bot.build_reply_markup([[Button.inline('👤 لیست ادمین ها',b'admin')],[Button.inline('⚙️ تنظیمات ربات',b'bot')],[Button.inline('📬 پیام همگانی','for')],[Button.inline('برگشت','backp')]])
admin_list_button = [[Button.inline('⬅️ برگشت','back')]]

async def display_time(timeout):
    start_time = datetime.datetime.now()
    while True:
        now = datetime.datetime.now()
        elapsed_time = (now - start_time).total_seconds()
        remaining_time = timeout - elapsed_time
        
        if remaining_time <= 0:
            for key in close_ban:
                close_ban[key] = False
            retime = False
            break
        
        #print(f'Time elapsed: {elapsed_time}s, Remaining time: {remaining_time}s', end='\r')
        await asyncio.sleep(1)

@bot.on(events.NewMessage(pattern='/up_s',from_users=owners))
async def uup_s(event):
    await bot.send_file(event.chat_id,'BotData.db',caption='آخرین دیتابیس ذخیره شده')
    await bot.send_file(event.chat_id,'main.py',caption='آخرین سورس ربات ذخیره شده')
    await bot.send_file(event.chat_id,'session.txt',caption='آخرین سشن ذخیره شده')

@bot.on(events.NewMessage(pattern='/check',from_users=owners))
async def restart(event):
    global session
    global helper
    if await client.is_user_authorized():
        me = await client.get_me()
        helper = me.id
        addbot(cloumn='Acc',data=f'{me.username}')
        await event.reply(f'اکانت با موفقیت یافت شد\nنام: {me.first_name}\nآیدی عددی: {me.id}\nآیدی: {me.username}')
    else:
        await event.reply('اکانت یافت نشد')

@bot.on(events.NewMessage(pattern='/clear',from_users=owners))
async def clear(event):
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    cursor.execute('UPDATE Admins SET ProfitDay = 0')
    cursor.execute('UPDATE Users SET Amonut = 0')
    cursor.execute('UPDATE Users SET Dualnumday = 0')
    cursor.execute('UPDATE Users SET Sodday = 0')
    cursor.execute('UPDATE Users SET Loseday = 0')
#cursor.execute('DELETE FROM Users')
    con.commit()
    con.close()
    await event.reply('!')

@bot.on(events.NewMessage(pattern='راهنما📚'))
async def helpme(event):
    await event.delete()
    await event.respond("""
راهنمای دوئل
                        
دوئل یک ورودی و خروجی دارد مثال:
ورودی ۱۰۰
خروجی ۱۹۰
برای دوئل دو نفر اسم میدن ، بعد سکه هارو به ادمین میدن و وقتی که ادمین گفت بندازید یک تاس و دارت و بولینگ را میندازید هرکدام که بیشتر امتیاز گرفت سکه خروجی مال اون فرد میشه

روش حساب دارت تاس و بولینگ

دارت🎯:از دایره قرمز وسط به خارج دارت به ترتیب از ۶ شروع و دو تمام میشود 
اگر دارت به بیرون بره یک حساب میشه

تاس🎲: به اندازه عددی که آمده حساب میشود

بولینگ🎳: به اندازه بولینگ های افتاده حساب میشود و تعداد کل بولینگ ها ۶ عدد هست

نکته* سودی که برای دوئل برداشته میشه بعدا مجدد برای چالش هایی مثل خان و جعبه و... غیره استفاده میشود

راهنمای دستورات
                    
اگه زف یا ز ف رو توی دوئلاتون بفرستید بینتون زوج فرد میکنه که کی اول بندازه 
    
اگه تاس توی دوئلاتون بفرستید یه تاس براتون میندازه
                        
اگه قفل ویژه رو بفرستید هیچکس حتی خودتون نمیتونه پیامی غیر از دارت تاس بولینگ بفرسته ! و ربات پاکش میکنه
""")

@bot.on(events.NewMessage(pattern='/start'))
async def start(event: Message):
    global users_num
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    user = cursor.execute(f'SELECT ID FROM Users WHERE ID = {event.sender_id}')
    data = cursor.fetchone()
    try:
        if event.sender_id == data[0]:
            await event.reply('خوش آمدید',buttons=public_button)
        elif sponser == True:
            try:
                result = bot(channels.GetParticipantRequest(
                    channel=channelid,
                    participant=event.sender_id
                ))
            except UserNotParticipantError:
                await event.reply('شما در کانال ما عضو نیستید',buttons=[[Button.url('کانال',channel_link)]])
    except:
        await event.reply('خوش آمدید',buttons=public_button)
        user_num_daul[int(event.sender_id)] = 0
        users[event.sender_id] = 0
        user = await bot.get_entity(event.sender_id)
        users_name[event.sender_id] = 'ثبت نشده'
        users_num += 1
        users_list.append(event.sender_id)
        cursor.execute(f'INSERT INTO Users (ID) VALUES ({event.sender_id})')
    con.commit()
    con.close()

@bot.on(events.NewMessage(pattern='!toploser',from_users=owners))
async def top_loser(event):
    ca = sqlite3.connect('BotData.db')
    ca = ca.cursor()
    ca.execute(f'SELECT ID,Loseday FROM Users')
    s = ca.fetchall()
    result = ''
    for i,r in enumerate(s,1):
        name,los = r
        user = await bot.get_entity(int(name))
        result += f'{i}- {user.first_name} >>>> {los}\n'
    await event.reply(f'{result}')

@bot.on(events.NewMessage(pattern='/panel',from_users=owners))
async def oss(event):
    await client.connect()
    if await client.is_user_authorized():
        await event.respond(f"اکانت کمکی: @{selectbot('Acc')}",buttons=priavte_button)
    else:
        await event.respond(f"اکانت کمکی: ثبت نشده‌",buttons=priavte_button)
        # await event.respond(f'',buttons=priavte_button)

@bot.on(events.NewMessage(pattern='برترین📊'))
async def top(event):
    me_none = None
    me_nonee = None
    hearts = ['🩷', '❤️', '💛', '🧡', '💚', '🩵', '💙', '💜', '🖤', '🩶', '🤍']
    ca = sqlite3.connect('BotData.db')
    cs = ca.cursor()
    cs.execute('SELECT ID,Amonut FROM Users ORDER BY Amonut DESC LIMIT 10')
    top_10 = cs.fetchall()
    cs.execute('SELECT ID,Dualnumday FROM Users ORDER BY Dualnumday DESC LIMIT 10')
    top_10_name = cs.fetchall()
    cs.execute(F'SELECT ID,Amonut FROM Users ORDER BY Amonut DESC')
    top_sender_shr = cs.fetchall()
    cs.execute(F'SELECT ID,Dualnumday FROM Users ORDER BY Dualnumday DESC')
    top_sender_shrr = cs.fetchall()
    message_text = "برترین بازیکنان امروز🔱\n\nبیشترین مقدار دوئل 👇\n"
    for ii,rr in enumerate(top_sender_shr,1):
        me,amo = rr
        if event.sender_id == int(me):
            me_none = ii
            cs.execute(f'SELECT Amonut FROM Users WHERE ID = {event.sender_id}')
            top_sender_ta = cs.fetchone()
            continue
    message_text += f'شما: {top_sender_ta[0]}  ـ رتبه {me_none} ام\n\n'
    for i,record in enumerate(top_10,1):
        name,amount = record
        namesa = await bot.get_entity(int(name))
        namesd = f'[{namesa.first_name}](tg://user?id={namesa.id})'
        message_text += f"{i} {random.choice(hearts)} ـ {namesd}              ـ{amount}\n"
    messaget = '\nبیشترین شرکت کنندگان دوئل 👇\n'
    for iii,rrr in enumerate(top_sender_shrr,1):
        me,amo = rrr
        if event.sender_id == int(me):
            me_nonee = iii
            cs.execute(f'SELECT Dualnumday FROM Users WHERE ID = {event.sender_id}')
            top_sender_taa = cs.fetchone()
            continue
    messaget += f'شما: {top_sender_taa[0]}  ـ رتبه {me_nonee} ام\n\n'
    for ir,recordd in enumerate(top_10_name,1):
        name,amountt = recordd
        namesa = await bot.get_entity(name)
        messaget += f'{ir} {random.choice(hearts)} ـ [{namesa.first_name}](tg://user?id={namesa.id})              ـ{amountt}\n'
    await event.respond(message_text + messaget,buttons=[Button.inline('امروز✔️','rf'),Button.inline('برترین ها 🎖','rfa')])

@bot.on(events.NewMessage(pattern='ارسال',from_users=owners))
async def forward(event: Message):
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    cursor.execute('SELECT ID FROM Users')
    us = cursor.fetchall()
    if event.is_reply:
        for userss in us:
            mesd = await event.get_reply_message()
            await bot.send_message(entity=userss[0],message=f'{mesd.message}')
    con.commit()
    con.close()

@bot.on(events.NewMessage(pattern='پروفایل👤'))
async def prof(event):
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    d = cursor.execute(f'SELECT Name FROM Users WHERE ID = {event.sender_id}')
    name = cursor.fetchall()
    dd = cursor.execute(f'SELECT DualNum FROM Users WHERE ID = {event.sender_id}')
    dualnumm = cursor.fetchall()
    for dn in dualnumm:
        pass
    cursor.execute(f'SELECT Amonut FROM Users WHERE ID = {event.sender_id}')
    dm = cursor.fetchone()
    cursor.execute(f'SELECT Allamount FROM Users WHERE ID = {event.sender_id}')
    dk = cursor.fetchone()
    cursor.execute(f'SELECT Dualnumday FROM Users WHERE ID = {event.sender_id}')
    dj = cursor.fetchone()
    cursor.execute(f'SELECT Wins FROM Users WHERE ID = {event.sender_id}')
    dw = cursor.fetchone()
    cursor.execute(f'SELECT DualNum FROM Users WHERE ID = {event.sender_id}')
    dwd = cursor.fetchone()
    cursor.execute(f'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
    are_ad = cursor.fetchall()
    for ads in are_ad:
        pass
    for n in name:
        pass
    try:
        drsd = int(dw[0]) / int(dwd[0]) 
        lose = int(dwd[0]) - int(dw[0])
        sbt = 'ثبت نشده'
        if n[0] == sbt and event.sender_id not in ads:
            await event.respond(f'** نام: ثبت نشده \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: {round(drsd * 100)}%**')
        else:
            await event.respond(f'** نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: {round(drsd * 100)}%**')
    except:
        lose = int(dwd[0]) - int(dw[0])
    if event.sender_id in owners:
        await event.respond(f'**نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: 0%**',buttons=[Button.inline('👤 پنل مالک','panel'),Button.inline('👤 پنل ادمین','backk')])
    elif event.sender_id in ads:
         await event.respond(f'**نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: 0%**',buttons=[Button.inline('👤 پنل ادمین','backk')])
    con.commit()
    con.close()

@bot.on(events.NewMessage(pattern='پشتیبانی ☎️'))
async def sup(event):
    async with bot.conversation(event.sender_id) as conv:
        await conv.send_message('باگی یا پیشنهادی داری بگو تا ارسالش کنم',buttons=[Button.text('انصراف')])
        mesdf = await conv.get_response()
        if mesdf.message == 'انصراف':
            conv.cancel()
            await event.reply('منوی اصلی',buttons=public_button)
        else:
            for owner in owners:
                await bot.send_message(entity=owner,message=f'{mesdf.message}\n{mesdf.sender_id}')
            await event.respond('ارسال شد',buttons=public_button)


@bot.on(events.NewMessage(pattern='تنظیم اسم📝'))
async def setname(event):
    async with bot.conversation(event.sender_id) as conv:
        await conv.send_message('اسم توی بازیتو بفرست!',buttons=[[Button.text('انصراف')]])
        mes = await conv.get_response()
        if mes.message == 'انصراف':
            conv.cancel()
            await event.respond('منوی اصلی',buttons=public_button)
        else:
            con = sqlite3.connect('BotData.db')
            cursor = con.cursor()
            cursor.execute(f'SELECT Name FROM Users WHERE ID = ({event.sender_id})')
            cursor.execute(f"UPDATE Users SET Name = '{mes.message}' WHERE ID = {event.sender_id}")
            con.commit()
            con.close()
            await event.respond('تنظیمش کردم',buttons=public_button)

@bot.on(events.NewMessage(pattern='/panel|panel'))
async def panel(event):
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    d = cursor.execute(f'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
    ad = cursor.fetchone()
    dd = cursor.execute(f'SELECT ProfitDay FROM Admins WHERE ID = {event.sender_id} ')
    sd = cursor.fetchone()
    ddd = cursor.execute(f'SELECT AllProfit FROM Admins WHERE ID = {event.sender_id}')
    asod = cursor.fetchone()
    if event.sender_id == ad[0]:
        await event.respond(f'آقا جان\nسود امروزت: {sd[0]}\nسود کلت: {asod[0]}',buttons=[[Button.inline('بنر','bannerr')],[Button.inline('تنظیم بنر','banner')]])
    elif ad[0] == None:
        pass
    con.commit()
    con.close()

@bot.on(events.CallbackQuery)
async def inline(event: Message):
    global admins_num
    if event.data == b'bot':
        conn = sqlite3.connect('BotData.db')
        cursorr = conn.cursor()
        cursorr.execute('SELECT ID FROM Users')
        uss = cursorr.fetchall()
        cursorr.execute('SELECT ID FROM Users')
        numm = cursorr.fetchall()
        numb = ''
        for i,r in enumerate(numm,1):
            numb += f'{i}'
        await sleep(0.5)
        await event.edit(f'تعداد کاربر: {numb}',buttons=[[Button.inline('لیست کاربران','users')],[Button.inline('افزودن اکانت کمکی','addhelper')],[Button.inline('برگشت',b'back')]])
        conn.close()
    elif event.data == b'adminn':
        fd = sqlite3.connect('BotData.db')
        cursor = fd.cursor()
        sagh = cursor.execute(f'SELECT Amount FROM Admins WHERE ID = {event.sender_id}')
        saghf = cursor.fetchone()
        dd = cursor.execute(f'SELECT ProfitDay FROM Admins WHERE ID = {event.sender_id} ')
        sd = cursor.fetchone()
        ddd = cursor.execute(f'SELECT AllProfit FROM Admins WHERE ID = {event.sender_id}')
        asod = cursor.fetchone()
        a = cursor.execute(F'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
        adsa = cursor.fetchone()
        if event.sender_id == adsa[0]:
            await event.edit(f'سقف شما: {saghf[0]}\nسود امروز: {sd[0]}\nسود کل: {asod[0]}',buttons=[[Button.inline('بنر','bannerr')],[Button.inline('تنظیم بنر','banner')],[Button.inline('برگشت','backp')]])
        cursor.commit()
        cursor.close()
    elif event.data == b'for':
        await event.respond('پیامتو بنویس و روش "ارسال" رو ریپلای کن')
    elif event.data == b'panel':
        await event.edit(text=f'اکانت کمکی: @{selectbot("Acc")}',buttons=priavte_button)
    elif event.data == b'backp':
        con = sqlite3.connect('BotData.db')
        cursor = con.cursor()
        d = cursor.execute(f'SELECT Name FROM Users WHERE ID = {event.sender_id}')
        name = cursor.fetchall()
        dd = cursor.execute(f'SELECT DualNum FROM Users WHERE ID = {event.sender_id}')
        dualnumm = cursor.fetchall()
        for dn in dualnumm:
            pass
        cursor.execute(f'SELECT Amonut FROM Users WHERE ID = {event.sender_id}')
        dm = cursor.fetchone()
        cursor.execute(f'SELECT Allamount FROM Users WHERE ID = {event.sender_id}')
        dk = cursor.fetchone()
        cursor.execute(f'SELECT Dualnumday FROM Users WHERE ID = {event.sender_id}')
        dj = cursor.fetchone()
        cursor.execute(f'SELECT Wins FROM Users WHERE ID = {event.sender_id}')
        dw = cursor.fetchone()
        cursor.execute(f'SELECT DualNum FROM Users WHERE ID = {event.sender_id}')
        dwd = cursor.fetchone()
        cursor.execute(f'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
        are_ad = cursor.fetchall()
        for ads in are_ad:
            pass
        for n in name:
            pass
        try:
            drsd = int(dw[0]) / int(dwd[0]) 
            lose = int(dwd[0]) - int(dw[0])
            if n[0] == 'ثبت نشده':
                await event.edit(f'** نام: ثبت نشده \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: {round(drsd * 100)}%**')
            else:
                await event.edit(f'** نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: {round(drsd * 100)}%**')
        except:
            lose = int(dwd[0]) - int(dw[0])
        if event.sender_id in owners:
            await event.edit(f'**نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: 0%**',buttons=[Button.inline('👤 پنل مالک','panel'),Button.inline('👤 پنل ادمین','adminn')])
        elif event.sender_id in ads:
            await event.edit(f'**نام: {n[0]} \nمقدار دوئل امروز: {dm[0]}\nمقدار دوئل کل: {dk[0]}\n تعداد دوئل امروز: {dj[0]}\n تعداد دوئل کل: {dwd[0]}\n تعداد برد کل: {dw[0]}\nتعداد باخت کل: {lose}\nدرصد برد: 0%**',buttons=[Button.inline('👤 پنل ادمین','adminn')])
        con.commit()
        con.close()
    elif event.data == b'addhelper':
        await event.answer('بزودی...')
        await event.delete()
        # async with bot.conversation(event.sender_id,timeout=330) as conv:
        #     await conv.send_message('شماره اکانت بفرست ')
        #     p = await conv.get_response()
        #     try:
        #         await client.connect()
        #         b = await client.send_code_request(phone=p.message)
        #     except Exception as e:
        #         await event.respond(f'خطا بعدا امتحان کنید')
        #         conv.cancel()
        #     await conv.send_message('کد ارسال شده رو بفرست')
        #     c = await conv.get_response()
        #     await client.sign_in(code=c.message)
        #     with open('session.txt','w') as file:
        #         file.write(f'{client.session.save()}')
        #     await conv.send_message('حالا یبار دستور /check بفرست')
    elif event.data == b'rf':
        me_none = None
        me_nonee = None
        await event.answer('در حال بروزرسانی...')
        hearts = ['🩷', '❤️', '💛', '🧡', '💚', '🩵', '💙', '💜', '🖤', '🩶', '🤍']
        ca = sqlite3.connect('BotData.db')
        cs = ca.cursor()
        cs.execute('SELECT ID,Amonut FROM Users ORDER BY Amonut DESC LIMIT 10')
        top_10 = cs.fetchall()
        cs.execute('SELECT ID,Dualnumday FROM Users ORDER BY Dualnumday DESC LIMIT 10')
        top_10_name = cs.fetchall()
        cs.execute(F'SELECT ID,Amonut FROM Users ORDER BY Amonut DESC')
        top_sender_shr = cs.fetchall()
        cs.execute(F'SELECT ID,Dualnumday FROM Users ORDER BY Dualnumday DESC')
        top_sender_shrr = cs.fetchall()
        message_text = "برترین بازیکنان امروز🔱\n\nبیشترین مقدار دوئل 👇\n"
        for ii,rr in enumerate(top_sender_shr,1):
            me,amo = rr
            if event.sender_id == int(me):
                me_none = ii
                cs.execute(f'SELECT Amonut FROM Users WHERE ID = {event.sender_id}')
                top_sender_ta = cs.fetchone()
                continue
        message_text += f'شما: {top_sender_ta[0]}  ـ رتبه {me_none} ام\n\n'
        for i,record in enumerate(top_10,1):
            name,amount = record
            namesa = await bot.get_entity(int(name))
            namesd = f'[{namesa.first_name}](tg://user?id={namesa.id})'
            message_text += f"{i} {random.choice(hearts)}  ـ {namesd}              ـ{amount}\n"
            messaget = '\nبیشترین شرکت کنندگان دوئل 👇\n'
        for iii,rrr in enumerate(top_sender_shrr,1):
            me,amo = rrr
            if event.sender_id == int(me):
                me_nonee = iii
                cs.execute(f'SELECT Dualnumday FROM Users WHERE ID = {event.sender_id}')
                top_sender_taa = cs.fetchone()
                continue
        messaget += f'شما: {top_sender_taa[0]}  ـ رتبه {me_nonee} ام\n\n'
        for ir,recordd in enumerate(top_10_name,1):
            name,amountt = recordd
            namesa = await bot.get_entity(name)
            namesd = f'[{namesa.first_name}](tg://user?id={namesa.id})'
            messaget += f'{ir} {random.choice(hearts)}  ـ {namesd}              ـ{amountt}\n'
        await event.edit(message_text + messaget,buttons=[Button.inline('امروز✔️','rf'),Button.inline('برترین ها 🎖','rfa')])
    elif event.data == b'rfa':
        await event.answer('در حال بروزرسانی...')
        hearts = ['🩷', '❤️', '💛', '🧡', '💚', '🩵', '💙', '💜', '🖤', '🩶', '🤍']
        ca = sqlite3.connect('BotData.db')
        cs = ca.cursor()
        cs.execute('SELECT ID,allamount FROM Users ORDER BY allamount DESC LIMIT 10')
        top_10 = cs.fetchall()
        cs.execute('SELECT ID,Dualnum FROM Users ORDER BY Dualnum DESC LIMIT 10')
        top_10_name = cs.fetchall()
        cs.execute(F'SELECT ID,allamount FROM Users ORDER BY allamount DESC')
        top_sender_shr = cs.fetchall()
        cs.execute(F'SELECT ID,Dualnum FROM Users ORDER BY Dualnum DESC')
        top_sender_shrr = cs.fetchall()
        message_text = "برترین بازیکن ها 🔱\n\nبیشترین مقدار دوئل 👇\n"
        for ii,rr in enumerate(top_sender_shr,1):
            me,amo = rr
            if event.sender_id == int(me):
                me_none = ii
                cs.execute(f'SELECT allamount FROM Users WHERE ID = {event.sender_id}')
                top_sender_ta = cs.fetchone()
                continue
        message_text += f'شما: {top_sender_ta[0]}  ـ رتبه {me_none} ام\n\n'
        for i,record in enumerate(top_10,1):
            name,amount = record
            namesa = await bot.get_entity(int(name))
            namesd = f'[{namesa.first_name}](tg://user?id={namesa.id})'
            message_text += f"{i} {random.choice(hearts)}  ـ {namesd}              ـ{amount}\n"
        messaget = '\nبیشترین شرکت کنندگان دوئل 👇\n'
        for iii,rrr in enumerate(top_sender_shrr,1):
            me,amo = rrr
            if event.sender_id == int(me):
                me_nonee = iii
                cs.execute(f'SELECT Dualnum FROM Users WHERE ID = {event.sender_id}')
                top_sender_taa = cs.fetchone()
                continue
        messaget += f'شما: {top_sender_taa[0]}  ـ رتبه {me_nonee} ام\n\n'
        for ir,recordd in enumerate(top_10_name,1):
            name,amountt = recordd
            namesa = await bot.get_entity(name)
            messaget += f'{ir} {random.choice(hearts)}  ـ [{namesa.first_name}](tg://user?id={namesa.id})              ـ{amountt}\n'
        await event.edit(message_text + messaget,buttons=[Button.inline('امروز🥇','rf'),Button.inline('برترین ها ✔️','rfa')])
    elif event.data == b'back':
        await client.connect()
        if event.sender_id in owners and await client.is_user_authorized():
            await event.edit(f"اکانت کمکی: @{selectbot('Acc')}",buttons=priavte_button)
        elif event.sender_id in owners and not await client.is_user_authorized():
            await event.edit(f"اکانت کمکی: ثبت نشده‌",buttons=priavte_button)
    elif event.data == b'backk':
        con = sqlite3.connect('BotData.db')
        cursor = con.cursor()
        d = cursor.execute(f'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
        ad = cursor.fetchone()
        dd = cursor.execute(f'SELECT ProfitDay FROM Admins WHERE ID = {event.sender_id} ')
        sd = cursor.fetchone()
        ddd = cursor.execute(f'SELECT AllProfit FROM Admins WHERE ID = {event.sender_id}')
        asod = cursor.fetchone()
        sagh = cursor.execute(f'SELECT Amount FROM Admins WHERE ID = {event.sender_id}')
        saghf = cursor.fetchone()
        if event.sender_id == ad[0]:
            await event.edit(f'سقف شما: {saghf[0]}\nسود امروز: {sd[0]}\nسود کل: {asod[0]}',buttons=[[Button.inline('بنر','bannerr')],[Button.inline('تنظیم بنر','banner')],[Button.inline('برگشت','backp')]])
        elif ad[0] == None:
            pass
        con.commit()
        con.close()
    elif event.data == b'users':
        conn = sqlite3.connect('BotData.db')
        cursorr = conn.cursor()
        cursorr.execute('SELECT ID FROM Users')
        uss = cursorr.fetchall()
        message_a = 'لیست کاربران\n\n'
        for i,record in enumerate(uss,1):
            try:
                name = record
                nameaa = await bot.get_entity(int(name[0]))
                message_a += f'{i}. {nameaa.first_name} => `{name[0]}`\n'
            except:
                message_a += f'{i}. پیدا نشد => `{name[0]}`\n'
        await event.edit(message_a,buttons=[Button.inline('برگشت',b'back')])
        conn.close()
    elif event.data == b'admin':
        await event.edit(f'تعداد ادمین: {admins_num}',buttons=[[Button.inline('حدف ادمین',b'rema'),Button.inline('اضافه ادمین',b'adda')],[Button.inline('لیست ادمین',b'adminl')],[Button.inline('سود روزانه ادمین',b'addi')],[Button.inline('افزایش سقف','af')],[Button.inline('برگشت',b'back')]])
    elif event.data == b'af':
        async with bot.conversation(event.sender_id,timeout=120) as conv:
            await event.delete()
            await conv.send_message('آیدی عددیشو بفرست')
            mese = await conv.get_response()
            if mese.message == 'لغو':
                conv.cancel()
                await event.respond(f'Panel',buttons=priavte_button)
            else:
                await conv.send_message('و سقف')
                sghfa = await conv.get_response()
                conf = sqlite3.connect('BotData.db')
                cur = conf.cursor()
                cur.execute(f'UPDATE Admins SET Amount = {int(sghfa.message) + 1} WHERE ID = {int(mese.message)}')
                cur.execute(f'DELETE FROM Title WHERE ID = {int(mese.message)}')
                userrr = await bot.get_entity(int(mese.message))
                cur.execute(f'INSERT INTO Title (ID,Y) VALUES ({int(mese.message)},"کاربر {userrr.first_name} با {sghfa.message} سقف")')
                conf.commit()
                conf.close()
                await conv.send_message('تنظیم شد')
                
    elif event.data == b'adda':
        async with bot.conversation(event.sender_id,timeout=120) as conv:
            await event.delete()
            await conv.send_message('آیدی عددی ادمینو بفرست\nرباتو باید استارت کنه!',buttons=[[Button.text('انصراف')]])
            admin_id = await conv.get_response()
            if admin_id.message == 'انصراف':
                conv.cancel()
                await event.respond(f'Panel',buttons=priavte_button)
            await conv.send_message('سقفشو بفرست')
            mes = await conv.get_response()
            if mes.message == 'انصراف':
                conv.cancel()
                await event.respond(f'Panel',buttons=priavte_button)
            userr = await bot.get_entity(int(admin_id.message))
            await conv.send_message('لینک بازیشو بفرست')
            linkg = await conv.get_response()
            con = sqlite3.connect('BotData.db')
            cursor = con.cursor()
            adid = int(admin_id.message)
            inad = int(mes.message) + 1
            cursor.execute(f'INSERT INTO Admins (ID,Amount,Name,Link) VALUES ({adid},{inad},"{userr.first_name}","{linkg.message}")')
            cursor.execute(f'INSERT INTO Title (ID,Y) VALUES ({admin_id.message},"کاربر {userr.first_name} با {mes.message} سقف")')
            admins[int(admin_id.message)] = int(mes.message) + 1
            if len(admin_list_button[-1]) == 1:
                admin_list_button.append([])
            #admin_list_button[-1].append(Button.inline(f'{userr.first_name}({inad - 1})'))
            admins_title.append(f'کاربر {userr.first_name} با {admins[int(admin_id.message)]} سقف')
            admins_num += 1
            Button.clear()
            await event.respond(f'این کاربر {userr.first_name} ادمین شد')
            await event.respond('Panel',buttons=priavte_button)
            con.commit()
            con.close()
    elif event.data == b'adminl':
        con1 = sqlite3.connect('BotData.db')
        cursor1 = con1.cursor()
        cursor1.execute('SELECT Y FROM Title')
        admns = cursor1.fetchall()
        message_e = 'لیست ادمین ها:\n'
        for i,record in enumerate(admns,1):
            name = record
            message_e += f'{i}. {name[0]}\n'
        await event.edit(message_e,buttons=admin_list_button)
    elif event.data == b'rema':
        con0 = sqlite3.connect('BotData.db')
        cursor0 = con0.cursor()
        await event.delete()
        async with bot.conversation(event.sender_id,timeout=120) as conv:
            await event.delete()
            await conv.send_message('آیدی عددیشو بفرست',buttons=[[Button.text('انصراف')]])
            ida = await conv.get_response()
            if ida.message == 'انصراف':
                conv.cancel()
                await event.respond('Panel',buttons=priavte_button)
            cursor0.execute(f'SELECT ID FROM Admins WHERE ID = {int(ida.message)}')
            adido = cursor0.fetchone()
            if adido == None:
                await event.respond(f'این یارو ادمین نیست\n',buttons=priavte_button)
            if int(ida.message) == 7369338105:
                await event.respond('سازنده رو نمیتونی حذف کنی',buttons=public_button)
            cursor0.execute(f'SELECT ID FROM Admins WHERE ID = {int(ida.message)}')
            adidob = cursor0.fetchone()
            if int(ida.message) == adidob[0]:
                userrr = await bot.get_entity(int(ida.message))
                cursor0.execute(f'DELETE FROM Admins WHERE ID = {int(ida.message)}')
                cursor0.execute(f'DELETE FROM Title WHERE ID = {int(ida.message)}')
                admins_num -= 1
                con0.commit()
                con0.close()
                await event.respond('حذفش کردم',buttons=priavte_button)
    elif event.data == b'addi':
        cons = sqlite3.connect('BotData.db')
        cursora = cons.cursor()
        cursora.execute('SELECT Name,ProfitDay FROM Admins')
        dataad = cursora.fetchall()
        message_es = 'سود روزانه\n'
        for i,record in enumerate(dataad,1):
            name,ssss = record
            message_es += f'{i}. {name} => {ssss}\n'
        await event.edit(message_es,buttons=[[Button.inline('برگشت','back')]])
        cons.commit()
        cons.close()
    elif event.data == b's':
        try:
            lo = None
            conp = sqlite3.connect('BotData.db')
            cursorp = conp.cursor()
            cursorp.execute(f'SELECT ID FROM Admins')
            adminssa = cursorp.fetchall()
            d = []
            for adsa in adminssa:
                d.append(adsa[0])
            print(d)
            if event.sender_id in d:
                lo = event.sender_id
            print(lo)
            cursorp.execute(f'SELECT Amount FROM Admins WHERE ID = {lo}')
            sghf = cursorp.fetchone()
            if event.sender_id == lo and num < sghf[0]:
                admin_ban_mes[event.message_id] = lo
                mesa[event.message_id] = num
                global mes_maf
                cursorp.execute(f'SELECT Link FROM Admins WHERE ID = {lo}')
                game = cursorp.fetchone()
                await event.edit(buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{game[0]}')]])
                ban_zrf[event.message_id] = (0,0)
                ss = await bot.send_message(entity=lo,message=f'شما یک دوئل شروع کردید\nورودی {mesa[event.message_id]}',buttons=[[Button.inline('اتمام','close')],[Button.inline('اتمام کل','allclose')]])
                c = await bot.get_messages(channelid,ids=event.message_id)
                mesban[event.message_id] = c.message
                close_ban[event.message_id] = True
                close_dual_i[ss.id] = event.message_id
                ban_id[event.message_id] = True
                num_id[event.message_id] = 0
                banmes[event.message_id] = False
            elif event.sender_id == lo and num > sghf[0]:
                await event.answer('نمی تونی شروع کنی')
            else:
                await event.answer('نمی تونی شروع کنی')
            conp.commit()
            conp.close()
        except KeyError:
            await event.answer('نمی تونی شروع کنی')
    elif event.data == b'banner':
        async with bot.conversation(event.sender_id,timeout=600) as conv:
            await event.delete()
            await conv.send_message('بنرتو بفرست\nفقط جلو وروردی {num} و جلو خروجی {dual} بزار',buttons=[[Button.text('انصراف')]])
            ban = await conv.get_response()
            if ban.message == 'انصراف':
                conv.cancel()
                await event.respond(f'آقا جان\nسود امروزت: {admin_sod_day[event.sender_id]}\nسود کلت: {admin_sod[event.sender_id]}',buttons=[[Button.inline('بنر','banner')],[Button.url('مشکلات',url='https://t.me/The_Taha0')]])
            else:
                con = sqlite3.connect('BotData.db')
                cursor = con.cursor()
                d = cursor.execute(f"UPDATE Admins SET Banner = '{ban.message}' WHERE ID = {event.sender_id}")
                con.commit()
                con.close()
                await event.respond('تنظیم شد')
    elif event.data == b'bannerr':
        con = sqlite3.connect('BotData.db')
        cursor = con.cursor()
        d = cursor.execute(f'SELECT Banner FROM Admins WHERE ID = {event.sender_id}')
        ad = cursor.fetchone()
        await event.edit(f'{ad[0]}',buttons=[[Button.inline('برگشت','backk')]])
        con.commit()
        con.close()
    elif event.data == b'sh':
        try:
            cond = sqlite3.connect('BotData.db')
            cursord = cond.cursor()
            cursord.execute(f'SELECT ID FROM Users WHERE ID = {event.sender_id}')
            iddb = cursord.fetchone()
            cursord.execute(f'SELECT Sender FROM Names WHERE Sender = {event.sender_id}')
            usad = cursord.fetchone()
            cursord.execute(f'SELECT Amount FROM Names WHERE Sender = {event.sender_id}')
            zrf = cursord.fetchone()
            cursord.execute(f'SELECT Block FROM Users WHERE ID = {event.sender_id}')
            is_block = cursord.fetchone()
            if iddb == None:
                cursord.execute(f'INSERT INTO Users (ID) VALUES ({event.sender_id})')
            elif usad == None:
                cursord.execute(f'INSERT INTO Names (ID,Sender,Amount) VALUES ({event.message_id},{event.sender_id},1)')
            cond.commit()
            cond.close()
            if event.sender_id not in user_num_daul:
                user_num_daul[event.sender_id] = 0 
            if event.sender_id not in users:
                users[event.sender_id] = 0
            global timer
            global po
            if event.message_id not in ban_num:
                ban_num[event.message_id] = 0
            if event.sender_id not in ban_zrf[event.message_id]:
                ban_zrf[event.message_id] = (event.sender_id,0)
            if close_ban[event.message_id] == False:
                await event.answer('این دوئل بسته شده 😕')
            elif is_block[0] == 1:
                await event.answer('شما از ربات بلاک شده اید ⚠️',alert=True)
            elif ban_zrf[event.message_id][event.sender_id] > po:
                await event.answer(f'بیشتر از {po + 1} بار نمیتونی شرکت کنی',alert=True)
            elif event.sender_id in users_name:
                cds = sqlite3.connect('BotData.db')
                cdsc = cds.cursor()
                cdsc.execute(f'SELECT Name FROM Users WHERE ID = {event.sender_id}')
                usdb = cdsc.fetchone()
                c = await bot.get_messages(channelid,ids=event.message_id)
                m = await bot.get_entity(event.sender_id)
                num_id[event.message_id] += 1
                user_num_daul[event.sender_id] += 1
                ban_zrf[event.message_id][event.sender_id] += 1
                ban_num[event.message_id] += 1
                #print(ban_zrf)
                #await sleep(0.5)
                names[event.message_id].append(f'{ban_num[event.message_id]}ـ {m.first_name}\n')
                await event.answer(f'{ban_zrf[event.message_id][event.sender_id]} بار توی دوئل شرکت کردی')
                mess = await bot.send_message(entity=admin_ban_mes[event.message_id],message=f'شرکت کننده: [{m.first_name}](tg://user?id={m.id})\nنام بازی: {usdb[0]}\nآیدی: @{m.username}\nورودی: {mesa[event.message_id]}\nآیدی عددی: `{event.sender_id}`',buttons=[[Button.inline('✅','t')],[Button.inline('انبلاک','un'),Button.inline('بلاک','bl')]])
                #messs = await bot.send_message(entity=lo,message=f'شرکت کننده: 0\nنام بازی: 0\nآیدی: 0\nورودی: {mesa[event.message_id]}\nآیدی عددی: `0`',buttons=[[Button.inline('✅','t')],[Button.inline('انبلاک','un'),Button.inline('بلاک','bl')]])
                user_dual[mess.id] = event.sender_id
                # user_dual[messs.id] = 6751824694
                # user_dual_a[messs.id] = mesa[event.message_id]
                user_dual_a[mess.id] = mesa[event.message_id]
                # await sleep(2)
                # try:
                #     await event.edit(c.message + f'\n{m.first_name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                # except:
                #     pass
                # finally:
                #     await sleep(1)
                #     await event.edit(c.message + f'\n{m.first_name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                cds.commit()
                cds.close()
            elif event.sender_id not in users_name:
                cds = sqlite3.connect('BotData.db')
                cdsc = cds.cursor()
                cdsc.execute(f'SELECT Name FROM Users WHERE ID = {event.sender_id}')
                usdb = cdsc.fetchone()
                c = await bot.get_messages(channelid,ids=event.message_id)
                m = await bot.get_entity(event.sender_id)
                user_num_daul[event.sender_id] += 1
                #ban_zrf[event.message_id][event.sender_id] += 1
                num_id[event.message_id] += 1
                ban_num[event.message_id] += 1
                #await sleep(0.5)
                names[event.message_id].append(f'{ban_num[event.message_id]}ـ {m.first_name}\n')
                await event.answer(f'{user_num_daul[event.sender_id]} بار توی دوئل شرکت کردی')
                mess = await bot.send_message(entity=admin_ban_mes[event.message_id],message=f'شرکت کننده: [{m.first_name}](tg://user?id={m.id})\nنام بازی: ثبت نشده\nآیدی: @{m.username}\nورودی: {mesa[event.message_id]}\nآیدی عددی: `{event.sender_id}`',buttons=[[Button.inline('✅','t')],[Button.inline('انبلاک','un'),Button.inline('بلاک','bl')]])
                user_dual[mess.id] = event.sender_id
                user_dual_a[mess.id] = mesa[event.message_id]
                users_name[event.sender_id] = 0
                # await sleep(4)
                # try:
                #     await event.edit(c.message + f'\n{m.first_name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')][Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                # except:
                #     await event.edit(c.message + f'\n{m.first_name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                # finally:
                #     await event.edit(c.message + f'\n{m.first_name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                cds.commit()
                cds.close()
        except KeyError:
            pass
    elif event.data == b'un':
        conh = sqlite3.connect('BotData.db')
        cra = conh.cursor()
        cra.execute(f'UPDATE Users SET Block = 0 WHERE ID = {user_dual[event.message_id]}')
        await event.answer('کاربر انبلاک شد',alert=True)
        conh.commit()
        conh.close()
    elif event.data == b'bl':
        conhh = sqlite3.connect('BotData.db')
        crah = conhh.cursor()
        crah.execute(f'UPDATE Users SET Block = 1 WHERE ID = {user_dual[event.message_id]}')
        await event.answer('کاربر بلاک شد',alert=True)
        conhh.commit()
        conhh.close()
    elif event.data == b'ban':
        await event.edit(f'ظرفیت اسم: {po + 1}',buttons=[[Button.inline('تنظیم ظرفیت','z')],[Button.inline('برگشت','back')]])
    elif event.data == b'z':
        async with bot.conversation(event.sender_id) as conv:
            await event.delete()
            await conv.send_message('عدد بفرست',buttons=[[Button.text('انصراف')]])
            b = await conv.get_response()
            if b.message == 'انصراف':
                conv.cancel()
                await event.respond('Panel',buttons=priavte_button)
            else:
                po = int(b.message) - 1
                await event.respond('تنظیم شد',buttons=priavte_button)
    elif event.data == b't':
        global v
        if v == True:
            v = False
            user1 = user_dual[event.message_id]
            global f1
            f1 = await bot.get_entity(user1)
            await event.answer(f'{f1.first_name} 🆚')
            await event.edit(buttons=[[Button.inline('✔️','p')]])
            global dualnum
            dualnum = user_dual_a[event.message_id]
            global dualv
            dualv = user_dual[event.message_id]
        elif v == False and user_dual_a[event.message_id] == dualnum and dualv != dualnum:
            user2 = user_dual[event.message_id]
            f2 = await bot.get_entity(user2)
            await event.answer(f'{f1.first_name} 🆚 {f2.first_name} - {user_dual_a[event.message_id]}')
            cc = await bot.send_message(entity=channelid,message=f'[{f1.first_name}](tg://user?id={f1.id}) 🆚 [{f2.first_name}](tg://user?id={f2.id}) - {user_dual_a[event.message_id]}')
            admin_dual_mes[cc.id] = event.sender_id
            v = True
            f_dual2[cc.id] = f1.id
            f_dual3[cc.id] = f2.id
            daul_num[cc.id] = user_dual_a[event.message_id]
            await event.edit(buttons=[[Button.inline('✔️','p')]])
        elif v == False and user_dual[event.message_id] == user_dual_a[event.message_id]:
            await event.answer('ینفرو نمیتونی باهم تایید کنی')
        elif user_dual_a[event.message_id] != dualnum:
            await event.answer('دوئلای با مقدار متفاوت رو نمیتونی تایید کنی',alert=True)
    elif event.data == b'close':
        close_ban[close_dual_i[event.message_id]] = False
        ban_id[close_dual_i[event.message_id]] = False
        v = True
        await event.edit(buttons=[[Button.inline('دوئل بسته شد','0')]])
    elif event.data == b'allclose':

        for key in close_ban:
            close_ban[key] = False
            ban_id[key] = False
        v = True
        await event.edit(buttons=[[Button.inline('کل بنر ها بسته شد','0')]])

@client.on(events.NewMessage(pattern='تاس',chats=groupid))
async def dice(event):
    await client.send_file(event.chat_id,InputMediaDice('🎲'),reply_to=event.id)

@client.on(events.NewMessage(pattern='ز ف|زف',chats=groupid))
async def dice(event):
    await event.reply(f'شما زوج\nحریف فرد')
    await client.send_file(event.chat_id,InputMediaDice('🎲'),reply_to=event.id)

@bot.on(events.NewMessage(chats=channelid))
async def game(event):
    global retime
    if event.message.pinned == False and 'مافیا' in event.raw_text:
        global pins
        #pins = await client.pin_message(event.chat_id,event.id)
        global mes_maf
        mes_maf = event.id

@bot.on(events.NewMessage(chats=channelid))
async def gamde(event):
    global retime
    if retime == False:
        retime = True
        asyncio.create_task(display_time(600))
        asyncio.create_task(lop())
        # asyncio.create_task(display_time(600))

@bot.on(events.NewMessage(chats=groupid))
async def chs(event):
    f5 = None
    f6 = None
    me = await bot.get_me()
    try:
        if event.forward.post_author == f'{me.first_name}':
            d = event.forward.channel_post
            global dele
            dele = False
            f_dual2[event.id] = f_dual2[event.forward.channel_post]
            f_dual3[event.id] = f_dual3[event.forward.channel_post]
            user1b[event.id] = False
            user1d[event.id] = False
            user1da[event.id] = False
            user2b[event.id] = False
            user2d[event.id] = False
            user2da[event.id] = False
            dualmes[event.id] = False
            async with bot.conversation(entity=groupid,timeout=600,total_timeout=600,replies_are_responses=True,exclusive=False) as conv:
                #await conv.send_message('Send 🎯🎳🎲 !',reply_to=event.id)
                while True:
                    mes = await conv.get_reply(message=event.id)
                    if dualmes[event.id] == False:
                        ca = await client.get_messages(channelid,ids=d)
                        dualmes[event.id] = True
                    if not d:
                        conv.cancel()
                        break
                    if mes.message == 'قفل ویژه':
                        dele = True
                        await event.reply('قفل ویژه فعال شد🔒')
                        continue
                    if mes.message == 'باز کردن ویژه':
                        dele = False
                        await event.reply('قفل ویژه غیرفعال شد🔓')
                        continue
                    if dele == True and event.sender_id != helper and not mes.media:
                        await client.delete_messages(entity=groupid,message_ids=mes.id)
                        continue
                    elif mes.message == '':
                        pass
                    else:
                        continue
                    if mes.media.emoticon == '🎲' and mes.sender_id == f_dual2[event.id] and user1d[event.id] == False:
                        dice = int(mes.media.value)
                        user1d[event.id] = True
                    if mes.media.emoticon == '🎳' and mes.sender_id == f_dual2[event.id] and user1b[event.id] == False:
                        bol = int(mes.media.value)
                        if bol == 1:
                            bol -= 1
                        elif bol == 2:
                            bol -= 1
                        user1b[event.id] = True
                    if mes.media.emoticon == '🎯' and mes.sender_id == f_dual2[event.id] and user1da[event.id] == False:
                        dart = int(mes.media.value)
                        user1da[event.id] = True
                    if mes.media.emoticon == '🎲' and mes.sender_id == f_dual3[event.id] and user2d[event.id] == False:
                        dice2 = int(mes.media.value)
                        user2d[event.id] = True
                    if mes.media.emoticon == '🎳' and mes.sender_id == f_dual3[event.id] and user2b[event.id] == False:
                        bol2 = int(mes.media.value)
                        if bol2 == 1:
                            bol2 -= 1
                        elif bol2 == 2:
                            bol2 -= 1
                        user2b[event.id] = True
                    if mes.media.emoticon == '🎯' and mes.sender_id == f_dual3[event.id] and user2da[event.id] == False:
                        dart2 = int(mes.media.value)
                        user2da[event.id] = True
                    if user1d[event.id] == True == True and user1b[event.id] == True and user1da[event.id] == True:
                        f11 = dice + bol + dart
                        f5 = f11
                    if user2d[event.id] == True == True and user2b[event.id] == True and user2da[event.id] == True:
                        f12 = dice2 + bol2 + dart2
                        f6 = f12
                    if user1d[event.id] == True == True and user1b[event.id] == True and user1da[event.id] == True and user2d[event.id] == True and user2b[event.id] == True and user2da[event.id] == True:
                        f1 = dice + bol + dart
                        f2 = dice2 + bol2 + dart2
                        if f1 > f2:
                            user1 = await bot.get_entity(f_dual2[event.id])
                            user2 = await bot.get_entity(f_dual3[event.id])
                            mess = await bot.get_messages(channelid,ids=d)
                            await sleep(1)
                            await bot.send_message(entity=groupid,message=f'✅ {user1.first_name} Win !',reply_to=event.id)
                           # await sleep(1)
                            try:
                                await sleep(1.2)
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : {f1}\n[{user2.first_name}](tg://user?id={user2.id}) : {f2}\n-\n{user1.first_name} Win🤩🎉')
                            except:
                                await bot.connect()
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : {f1}\n[{user2.first_name}](tg://user?id={user2.id}) : {f2}\n-\n{user1.first_name} Win🤩🎉')
                            datac = sqlite3.connect('BotData.db')
                            dataa = datac.cursor()
                            dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Wins = Wins + 1 WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allamount = Allamount + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allamount = Allamount + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual3[event.id]}')
                            num0 = daul_num[event.forward.channel_post]
                            num000 = round(num0 * 1.8182)
                            num00 = num0 * 2 - num0 * 1.8182
                            sod = round(num00)
                            global lo
                            dataa.execute(f'UPDATE Users SET Sodday = Sodday + {num000 - num0} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allsod = Allsod + {num000 - num0} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Loseday = Loseday + {sop(f_dual3[event.id]) - num0} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Alllose = Alllose + {sop(f_dual3[event.id]) - num0} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Admins SET ProfitDay = ProfitDay + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                            dataa.execute(f'UPDATE Admins SET AllProfit = AllProfit + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                            datac.commit()
                            datac.close()
                            await client.send_message(entity=admin_dual_mes[event.forward.channel_post],message=f'ادمین گرامی\n+{sod} سود گرفتی\nاز دوعل\n{channel_link}/{mess.id}')
                            break
                        elif f2 > f1:
                            user1 = await bot.get_entity(f_dual2[event.id])
                            user2 = await bot.get_entity(f_dual3[event.id])
                            mess = await bot.get_messages(channelid,ids=d)
                            await sleep(1)
                            await bot.send_message(entity=groupid,message=f'✅ {user2.first_name} Win !',reply_to=event.id)
                          #  await sleep(1)
                            try:
                                await sleep(1.2)
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : {f1}\n[{user2.first_name}](tg://user?id={user2.id}) : {f2}\n-\n{user2.first_name} Win🤩🎉')
                            except:
                                await bot.connect()
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : {f1}\n[{user2.first_name}](tg://user?id={user2.id}) : {f2}\n-\n{user2.first_name} Win🤩🎉')
                            datac = sqlite3.connect('BotData.db')
                            dataa = datac.cursor()
                            dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Wins = Wins + 1 WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allamount = Allamount + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allamount = Allamount + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                            num0 = daul_num[event.forward.channel_post]
                            num000 = num0 * 1.8182
                            num00 = num0 * 2 - num0 * 1.8182
                            sod = round(num00)
                            dataa.execute(f'UPDATE Users SET Sodday = Sodday + {num000 - num0} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Allsod = Allsod + {num000 - num0} WHERE ID = {f_dual3[event.id]}')
                            dataa.execute(f'UPDATE Users SET Loseday = Loseday + {sop(f_dual2[event.id]) - num0} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Users SET Alllose = Alllose + {sop(f_dual2[event.id]) - num0} WHERE ID = {f_dual2[event.id]}')
                            dataa.execute(f'UPDATE Admins SET ProfitDay = ProfitDay + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                            dataa.execute(f'UPDATE Admins SET AllProfit = AllProfit + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                            datac.commit()
                            datac.close()
                            await client.send_message(entity=admin_dual_mes[event.forward.channel_post],message=f'ادمین گرامی\n+{sod} سود گرفتی\nاز دوعل\n{channel_link}/{mess.id}')
                            break
                        elif f1 == f2:
                            user1 = await bot.get_entity(f_dual2[event.id])
                            user2 = await bot.get_entity(f_dual3[event.id])
                            await client.send_message(entity=groupid,message=f'Draw ❕\n[{user1.first_name}](tg://user?id={user1.id})\n[{user2.first_name}](tg://user?id={user2.id})',reply_to=event.id)
                            await sleep(1)
                            try:
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}): {f1}\n[{user2.first_name}](tg://user?id={user2.id}): {f2}\n-\nDraw❕')
                            except:
                                await bot.connect()
                                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}): {f1}\n[{user2.first_name}](tg://user?id={user2.id}): {f2}\n-\nDraw❕')
                            user1d[event.id] = False
                            user1b[event.id] = False
                            user1da[event.id] = False
                            user2d[event.id] = False
                            user2b[event.id] = False
                            user2da[event.id] = False
                            continue
    except asyncio.TimeoutError:
        me = await bot.get_me()
        await client.send_message(entity=admin_dual_mes[event.forward.channel_post],message='ادمین عزیز تایم یکی از دوئل ها تموم شد\nسکه رو برگردون یا بده')
        if event.forward.post_author == f'{me.first_name}':
            if user1d[event.id] == True and user1b[event.id] == True and user1da[event.id] == True:
                user1 = await bot.get_entity(f_dual2[event.id])
                user2 = await bot.get_entity(f_dual3[event.id])
                await sleep(1)
                await client.send_message(event.chat_id,f'✅ {user1.first_name} Win !',reply_to=event.id)
                mess = await client.get_messages(channelid,ids=d)
               # await sleep(1.5)
                await bot.edit_message(entity=channelid,message=d,text=ca .message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : {f5}\n[{user2.first_name}](tg://user?id={user2.id}) : ❌\n-\n{user1.first_name} Win🤩🎉')
                num0 = daul_num[event.forward.channel_post]
                num000 = num0 * 1.8182
                num00 = num0 * 2 - num0 * 1.8182
                sod = round(num00)
                dataa.execute(f'UPDATE Users SET Sodday = Sodday + {num000 - num0} WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Allsod = Allsod + {num000 - num0} WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Loseday = Loseday + {sop(f_dual3[event.id]) - num0} WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET Alllose = Alllose + {sop(f_dual3[event.id]) - num0} WHERE ID = {f_dual3[event.id]}')
                datac = sqlite3.connect('BotData.db')
                dataa = datac.cursor()
                dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Wins = Wins + 1 WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Allamount = Allamount + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Admins SET ProfitDay = ProfitDay + {sod} WHERE ID = {lo}')
                dataa.execute(f'UPDATE Admins SET AllProfit = AllProfit + {sod} WHERE ID = {lo}')
                datac.commit()
                datac.close()
                await client.send_message(entity=admin_dual_mes[event.forward.channel_post],message=f'ادمین گرامی\n+{sod} سود گرفتی\nاز دوعل\n{channel_link}/{mess.id}')
            elif user2d[event.id] == True and user2b[event.id] == True and user2da[event.id] == True:
                user1 = await bot.get_entity(f_dual2[event.id])
                user2 = await bot.get_entity(f_dual3[event.id])
                await client.send_message(event.chat_id,f'✅ {user2.first_name} Win !',reply_to=event.id)
                await sleep(1)
                mess = await client.get_messages(channelid,ids=d)
               # await sleep(1.5)
                await bot.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : ❌\n[{user2.first_name}](tg://user?id={user2.id}) : {f6}\n-\n{user2.first_name} Win🤩🎉')
                num0 = daul_num[event.forward.channel_post]
                num00 = num0 * 2 - num0 * 1.8182
                sod = round(num00)
                dataa.execute(f'UPDATE Users SET Sodday = Sodday + {num000 - num0} WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET Allsod = Allsod + {num000 - num0} WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET Loseday = Loseday + {sop(f_dual2[event.id]) - num0} WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Alllose = Alllose + {sop(f_dual2[event.id]) - num0} WHERE ID = {f_dual2[event.id]}')
                datac = sqlite3.connect('BotData.db')
                dataa = datac.cursor()
                dataa.execute(f'UPDATE Users SET Dualnumday = Dualnumday + 1 WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET DualNum = Dualnum + 1 WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET Wins = Wins + 1 WHERE ID = {f_dual2[event.id]}')
                dataa.execute(f'UPDATE Users SET Amonut = Amonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Users SET Allamount = Allamonut + {daul_num[event.forward.channel_post]} WHERE ID = {f_dual3[event.id]}')
                dataa.execute(f'UPDATE Admins SET ProfitDay = ProfitDay + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                dataa.execute(f'UPDATE Admins SET AllProfit = AllProfit + {sod} WHERE ID = {admin_dual_mes[event.forward.channel_post]}')
                datac.commit()
                datac.close()
                await client.send_message(entity=admin_dual_mes[event.forward.channel_post],message=f'ادمین گرامی\n+{sod} سود گرفتی\nاز دوعل\n{channel_link}/{mess.id}')
            else:
                try:
                    user1 = await bot.get_entity(f_dual2[event.id])
                    user2 = await bot.get_entity(f_dual3[event.id])
                    await client.send_message(event.chat_id,'TimeOut 💢 !',reply_to=event.id)
                    await client.edit_message(entity=channelid,message=d,text=ca.message + f'\n-\n[{user1.first_name}](tg://user?id={user1.id}) : ❌\n[{user2.first_name}](tg://user?id={user2.id}) : ❌\nDuel Closed !')
                except:
                    pass

@bot.on(events.InlineQuery)
async def inlineQ(event): 
    try:
        builder = event.builder
        global num
        num = int(event.text)
        numm = num * float(1.8182)
        global dual
        dual = round(numm)
    except:
        pass
    con = sqlite3.connect('BotData.db')
    cursor = con.cursor()
    cursor.execute(f'SELECT Banner FROM Admins WHERE ID = {event.sender_id}')
    adb = cursor.fetchall()
    cursor.execute(f'SELECT ID FROM Admins WHERE ID = {event.sender_id}')
    ad = cursor.fetchone()
    for ban in adb:
        pass
    for adm in ad:
        pass
    if event.sender_id == ad[0]:
        try:
            await event.answer([
                builder.article(f'دوئل {num}',text=f'{ban[0].format(num=num,dual=dual)}',buttons=Button.inline('شروع دوئل ✅',b's'))
            ])
            global lo
            lo = event.original_update.user_id
            global bo
            bo = event.original_update.query
            timer = 0
        except:
            pass
    if event.sender_id == adm[0] and event.text == 'top':
        hearts = ['🩷', '❤️', '💛', '🧡', '💚', '🩵', '💙', '💜', '🖤', '🩶', '🤍']
        ca = sqlite3.connect('BotData.db')
        cs = ca.cursor()
        cs.execute('SELECT ID,Amonut FROM Users ORDER BY Amonut DESC LIMIT 10')
        top_10 = cs.fetchall()
        cs.execute('SELECT ID,Dualnumday FROM Users ORDER BY Dualnumday DESC LIMIT 10')
        top_10_name = cs.fetchall()
        message_text = "برترین بازیکنان امروز🔱\n\nبیشترین مقدار دوئل 👇\n\n"
        for i,record in enumerate(top_10,1):
            name,amount = record
            namesa = await bot.get_entity(int(name))
            message_text += f"{i} {random.choice(hearts)}  ـ {namesa.first_name}              -{amount}\n"
        messaget = '\nبیشترین شرکت کنندگان دوئل 👇\n\n'
        for ir,recordd in enumerate(top_10_name,1):
            name,amountt = recordd
            namesa = await bot.get_entity(name)
            messaget += f'{ir} {random.choice(hearts)}  ـ {namesa.first_name}              -{amountt}\n'
        #await event.respond(message_text + messaget,buttons=[[Button.inline('🔄','rf')]])
        await event.answer([
            event.builder.article(title='لیست برترین ها',text=message_text + messaget,buttons=[[Button.inline('🔄','rf')]])
        ])
async def lop():
    #await client.start()
    while True:
        try:
            for key in ban_id:
                if ban_id[key] == True:
                    for k,v in names.items():
                        try:
                            names[k] = ['"{}"'.format(''.join(v))]
                            name = v[0].strip('"').replace('"','')
                            await bot.edit_message(entity=channelid,message=k,text=f'{mesban[k]}\n{name}',buttons=[[Button.inline('شرکت در دوئل 💥',b'sh')],[Button.url('ورود به بازی ⬅️',f'{channel_link}/{mes_maf}')]])
                        except:
                            pass
        except:
            continue
        await sleep(5)

bot.start(bot_token=token)
print('Bot Runing..')
client.start()
print('Helper Runing..')
#bot.loop.run_until_complete(lop())
bot.run_until_disconnected()