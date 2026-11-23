# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
#--------------------------------imports--------------------------------
from pyrogram import Client ,  filters
from pyrogram.types import Message , CallbackQuery
from pyromod import listen
import os , time
import asyncio
from tronpy import Tron
from tronpy.keys import PrivateKey
from os import path
from pyrogram.types import (ReplyKeyboardMarkup, InlineKeyboardMarkup, InlineKeyboardButton) 
import generator
import withraw as w
import price
import cryptocompare
import Keyboard as key
from Database import Sql

ADMIN = [225364482 , 5600383508 , 6473903912]
ADMIN_COMANNDS = ["/panel","افزایش موجودی 📈","پنل اصلی 🏡","کاهش موجودی 📉","📍ثبت تبلیغ","🏆نفرات برتر","📉آمار کل","📜حسابرسی","📯همگانی","💰خدمات مالی","👥شرکت کنندگان","📋حسابرسی تکی","📒اطلاعات کاربر","💵 موجودی کل کاربران","🔘 لیست جذب کاربر","📬 لیست ثبت نام","🔸لیست برداشت","◽️حالت کاربر"]
channel = -1002092322962

sql = Sql()
sql.create()

API_ID = 0
API_HASH = "a83edd5f913344de385ec19a8"
app = Client("rifo",API_ID,API_HASH,bot_token="5936321250:AAF1a9x_Eq2lu0C-H5CZMVr3E")

client = Client("link",API_ID,API_HASH)
if os.path.exists("link.session"):
	client.connect()
else:
	phone_number = input("phone number : ")
	client.connect()
	sent_code_info = client.send_code(phone_number)
	phone_code = input("code : ")
	client.sign_in(phone_number, sent_code_info.phone_code_hash, phone_code)


@app.on_chat_join_request(filters.chat(channel))
async def req(_:Client,m:Message):
	id = m.from_user.id
	await app.send_message(id,"""‌ ‌
🤖 - به ربات گسترده ممبریه کینگ خوش‌آمدید

شما هنوز داخل گسترده ما ثبت نشدید جهت ورود رویه دکمه ثبت نام کلیک کنید ♥️
‌‌‌ ‌""",reply_markup=key.login)


@app.on_callback_query()
async def answer(_:client, e:CallbackQuery):
    data = e.data
    user_id = e.from_user.id
    message_id = e.message.id
    if data[0:4] == 'acc_':
        user = data.replace('acc_','').strip()
        sql.add_acc_member(user)
        await app.delete_messages(user_id, message_id)
        await app.send_message(int(user),'درخواست عضویت شما تأیید شد✅',reply_markup=key.user)
        await app.send_message(user_id,f"کاربره {user} تایید شد✅")
        sql.update_step('home',int(user))

    elif data[0:7] == 'notacc_':
        await app.delete_messages(user_id, message_id)
        await app.send_message(int(user),'درخواست عضویت شما رد شد❌',reply_markup=key.login)
        await app.send_message(user_id,f"کاربر {user} رد شد ❌")
        sql.update_step('login',int(user))

    elif data[0:5] == 'top3_':
        code = data.replace('top3_','').strip()
        links = sql.get_all_links_by_code(code)
        users = []
        dic = {}
        x = 0
        await e.answer('wait . . .')
        if len(links) >= 3:
            for link in links:
                link = link.strip()
                channel_id = sql.get_ad(code)['channel_id']
                userid = sql.get_user_id_by_link(str(link))
                joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , link)
                if x == 0:
                    tost = userid['user_id']
                    tost = str(tost)
                    users_pro = {userid['user_id']: joinis}
                    dic = users_pro
                    x += 1
                else:
                    kobs = userid['user_id']
                    dic[kobs] = joinis
            x_pro = {k: v for k, v in sorted(dic.items(), key=lambda item: item[1],reverse=True)}
            p = 1
            user_1 = {}
            user_2 = {}
            user_3 = {}
            user1 = ""
            user2 = ""
            user3 = ""
            for item in x_pro:
                if p == 1:
                    user_1 = {item : dic[item]}
                    user1 = item
                    p += 1
                elif p == 2:
                    user_2 = {item : dic[item]}
                    user2 = item
                    p += 1
                elif p == 3:
                    user_3 = {item : dic[item]}
                    user3 = item
                    p += 1
                    tag = sql.get_tag_ad(code)['tag']
                    await app.send_message(user_id,f'''🆔 : {tag}
    1 - {user1} | {user_1[user1]}
    2 - {user2} | {user_2[user2]}
    3 - {user3} | {user_3[user3]}''')
                    break
        else:
            await app.send_message(user_id,'تعداد شرکت کنندگان از 3 نفر کمتر است')

    elif data[0:8] == 'yeswith_':
        user = data.replace('yeswith_','').strip()
        user = int(user)
        info = await app.ask(user_id ,'اطلاعات پرداخت رو وارد کنید : ',reply_markup=key.back_admin)
        if info.text == 'پنل اصلی 🏡':
            await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
        else:
            await app.send_message(user , f'''پرداخت شما تکمیل شد ✅
اطلاعات پرداخت : 
{info.text}''')
            await app.delete_messages(user_id, message_id)
            await app.send_message(user_id,f"پرداخت {user} با موفقیت انجام شد ✅",reply_markup=key.panel_admin)

    elif data[0:7] == 'nowith_':
        x = data.split('_')
        user = int(x[-2])
        amount = int(x[-1])
        balance = sql.get_balance(user)
        new_balance = int(balance) + int(amount)
        sql.add_balance(new_balance , user)
        await app.delete_messages(user_id, message_id)
        await app.send_message(user_id,f"پرداخت {user} رد شد ❌")
        await app.send_message(user , 'پرداخت شما رد شد ❌')

@app.on_message(filters.private & filters.text)
async def me(_:Client , m:Message):
    user_id = m.chat.id
    step = sql.get_step(user_id)
    if step == None:
        sql.add_user(user_id)
    step = sql.get_step(user_id)['step']
    text = m.text
    if user_id in ADMIN and m.text in ADMIN_COMANNDS :
        if m.text == '/panel':
            await admin(app , m)

        elif m.text == 'پنل اصلی 🏡':
            await asli_panel(app , m)

        elif m.text == '◽️حالت کاربر':
            await user_mod(app , m)

        elif m.text == '📬 لیست ثبت نام' and step == 'admin_panel':
            await login_list(app , m)
        
        elif m.text == '📍ثبت تبلیغ' and step == 'admin_panel':
            await set_ad(app , m)

        elif m.text == '🔸لیست برداشت' and step == 'admin_panel':
            await withraw_list(app , m)
        
        elif m.text == '📜حسابرسی' and step == 'admin_panel':
            await hesabresi(app , m)
        
        elif m.text == '📉آمار کل' and step == 'admin_panel':
            await all_status(app , m)
        
        elif m.text == '💵 موجودی کل کاربران' and step == 'admin_panel':
            await all_balance(app , m)
        
        elif m.text == '💰خدمات مالی' and step == 'admin_panel':
            await k_mali(app , m)
        
        elif m.text == "🏆نفرات برتر" and step == 'admin_panel':
            await top3(app , m)

        elif m.text == "📯همگانی" and step == 'admin_panel':
            await hmg(app , m)

        elif m.text == "👥شرکت کنندگان" and step == 'admin_panel':
            await party(app , m)

        elif m.text == "📒اطلاعات کاربر" and step == 'admin_panel':
            await user_information(app , m)

        elif m.text =="📋حسابرسی تکی" and step == 'admin_panel':
            await hesab_one(app , m)

        elif step == 'kmali':
            await get_kamli(app , m)




    else:
        if text.startswith('/start'):
            command = m.command
            if text == '/start':
                if step == 'login' or step == 'get_login_channel':
                    await start_log(app , m)
                elif step != 'get_login_channel':
                    await start(app , m)
            else:
                await link_start(app , m)
        
        elif text == '🔙 بازگشت':
            await back(app , m)

        elif text == '⭕️ ثبت نام ⭕️' and step == 'login':
            await login(app , m)

        elif text == 'پشتیبانی 👤' and step in ['login','home']:
            await support(app , m)

        elif text == 'تبلیغات فعال 🟢' and step == 'home':
            await active_ads(app , m)

        elif text == 'آمار 📊' and step == 'home':
            await status(app , m)

        elif text == 'خدمات مالی 💰' and step == 'home':
            await balance_mod(app , m)

        elif text == 'نفرات برتر 🎖' and step == 'home':
            await top3(app , m)

        elif step == 'get_login_channel':
            await get_channels(app , m)

        elif step == 'balance_mod':
            await withraw(app , m)

        elif step == 'hi': # ADMIN
            await jj(app , m)

        elif step == 'hesab_one': # ADMIN
            await kobs(app , m)

        elif step == 'get_party':
            await get_party(app , m)

        elif step == 'get_hmg':
            await get_hmg(app , m)

        elif step == 'withraw':
            await get_withraw_mod(app , m)














# ADMIN
async def admin(app , m): #panel
    user_id = m.chat.id
    await app.send_message(user_id , 'به پنل مدیریت خوش آمدید☄' , reply_markup=key.panel_admin )
    sql.update_step('admin_panel',user_id)

async def user_mod(app , m): #حالت کاربر
    user_id = m.chat.id
    await app.send_message(user_id , '''پنل کاربر''' , reply_markup=key.user)
    sql.update_step('home',user_id)

async def login_list(app , m): #📬 لیست ثبت نام
    user_id = m.chat.id
    x = 0
    reqs = sql.get_all_login_req()
    await app.send_message(user_id , f'''📬تعداد درخواست های جدید ثبت شده : {len(reqs)}

📊لیست درخواست های اخیر :''')
    for user in reqs:
        user = user['user_id']
        username = sql.get_username_login_req(user)
        channel = sql.get_channel_login_req(user)
        if username == None:
            username = '@'
            await app.send_message(user_id , f'''👤 - کاربر : [{user}](tg://user?id={user}) | {username}
📬 - کانال : {channel['channels']}''' , reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید✅",
					callback_data=f"acc_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"❌رد ",
					callback_data=f"notacc_{user}"
				)
				]
			]
			))
        else:
            username = username['username']
            await app.send_message(user_id , f'''👤 - کاربر : [{user}](tg://user?id={user}) | {username}
📬 - کانال : {channel['channels']}''' , reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"✅تایید",
					callback_data=f"acc_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"❌رد ",
					callback_data=f"notacc_{user}"
				)
				]
			]
			))
        sql.remove_login_req(user)

async def set_ad(app , m): #📍ثبت تبلیغ
    user_id = m.chat.id
    nombers = ['1','2','3','4','5','6','7','8','9']
    me = await app.get_me()
    while True:
        forward = await app.ask(m.chat.id,"یک پیام از چنل فوروارد کن(اکانت متصل به ربات حتما ادمین باشه)",reply_markup=key.back_admin)
        if forward.text == 'پنل اصلی 🏡':
            await app.send_message(user_id , 'به پنل مدیریت بازگشتید☄' , reply_markup=key.panel_admin )
            break
        else:
          channel_id = forward.forward_from_chat.id
          price = await app.ask(m.chat.id,"قیمت به ازای هر ۱۰۰ ممبر :  ( برای مثال 50000 یا 45000 )",reply_markup=key.back_admin)
          if price.text == "پنل اصلی 🏡":
            await app.send_message(m.chat.id,"به پنل مدیریت بازگشتید☄",reply_markup=key.panel_admin)
            break
          else:
            tag = await app.ask(m.chat.id,"ایدی کانال رو حتما با @ ارسال کنید : ",reply_markup=key.back_admin)
            if tag.text == "پنل اصلی 🏡":
                await app.send_message(m.chat.id,"به پنل مدیریت بازگشتید☄",reply_markup=key.panel_admin)
                break
            else:
                jayze = await app.ask(m.chat.id,"متن جوایزو وارد کنید : ",reply_markup=key.back_admin)
                if jayze.text == "پنل اصلی 🏡":
                    await app.send_message(m.chat.id,"به پنل مدیریت بازگشتید☄",reply_markup=key.panel_admin)
                    break
                else:
                    banner = await app.ask(m.chat.id,"چنل بنرا که حتما با https شروع شده باشه : ",reply_markup=key.back_admin)
                    if banner.text == "پنل اصلی 🏡":
                        await app.send_message(m.chat.id,"به پنل مدیریت بازگشتید☄",reply_markup=key.panel_admin)
                        break
                    else:
                        if str(channel_id)[0:4] == "-100" and tag.text[0:1] == "@" and price.text[0:1] in nombers:
                            code = generator.generate()
                            await app.send_message(channel,f"""🔴 تبلیغ کانال : {tag.text}

قیمت به ازای هر 100ممبر : {price.text}

🏆 جوایز : {jayze.text}""",disable_web_page_preview=True,reply_markup=InlineKeyboardMarkup(
	[
		[
		InlineKeyboardButton(
			"• دریافت لینک",
			url=f"https://t.me/{me.username}?start={code}"
		),
		InlineKeyboardButton(
			"• بنر ها",
			url=f"{banner.text}"
		)
		]
	]
))
                            await app.send_message(m.chat.id,f"""تبلیغ ثبت شد 🌱

🔘 کد تبلیغ : {code}

🔗 لینک تبلیغ : https://t.me/{me.username}?start={code}""",reply_markup=key.panel_admin)
                            tag = tag.text
                            price = price.text
                            banner = banner.text
                            sql.add_ad(channel_id,price,code,tag,banner)
                            break

async def withraw_list(app , m): #🔸لیست برداشت
    user_id = m.chat.id
    x = 0
    reqs = sql.get_all_withraw_req()
    reqs_tron = sql.get_all_tron_req()
    reqs_toman = sql.get_all_toman_req()
    u = len(reqs) + len(reqs_toman) + len(reqs_tron)
    await app.send_message(user_id , f'''📬تعداد درخواست های جدید ثبت شده : {u}

📊لیست درخواست های اخیر :''')
    
    for user in reqs:
        user = user['user_id']
        username = sql.get_username_withraw_req(user)
        amount = sql.get_amount_withraw_req(user)['amount']
        if username == None:
            username = '@'
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} تومان''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_withraw_req(user)
        else:
            username = username['username']
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} تومان''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_withraw_req(user)
    
    for user in reqs_tron:
        user = user['user_id']
        username = sql.get_username_tron_req(user)
        amount = sql.get_amount_tron_req(user)['amount']
        wallet = sql.get_wallet_tron_req(user)['wallet']
        if username == None:
            username = '@'
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} ترون
👝 کیف پول : {wallet}
''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_tron_req(user)
        else:
            username = username['username']
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} ترون
👝 کیف پول : {wallet}
''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_tron_req(user)
    
    for user in reqs_toman:
        user = user['user_id']
        username = sql.get_username_toman_req(user)
        amount = sql.get_amount_toman_req(user)['amount']
        wallet = sql.get_wallet_toman_req(user)['wallet']
        if username == None:
            username = '@'
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} تومان
👝 کیف پول : {wallet}
''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_toman_req(user)
        else:
            username = username['username']
            await app.send_message(user_id , f'''👤 کاربر :  [{user}](tg://user?id={user}) | {username}
💵 برداشت :  {amount} تومان
👝 کیف پول : {wallet}
''',reply_markup=InlineKeyboardMarkup(
			[
				[
				InlineKeyboardButton(
					"تایید پرداخت ✅",
					callback_data=f"yeswith_{user}"
				)
				],
                [
				InlineKeyboardButton(
					"رد پرداخت ❌",
					callback_data=f"nowith_{user}_{amount}"
				)
				]
			]
			))
            sql.remove_toman_req(user)
    
async def hesabresi(app , m): #📜حسابرسی
    user_id = m.chat.id
    code = await app.ask(m.chat.id,"کد تبلیغ رو ارسال کنید :",reply_markup=key.back_admin)
    if code.text == 'پنل اصلی 🏡':
        await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
    else:
        ok = await app.ask(m.chat.id,"ایا از حسابرسی تبلیغ مطمعن هستید ؟",reply_markup=key.yesorno)
        if ok.text == 'بله':
            await app.send_message(m.chat.id,"عملیات حسابرسی شروع شد 📌",reply_markup=key.panel_admin)
            channel = sql.get_ad(code.text)
            channel_id = channel['channel_id']
            price = sql.get_price(int(channel_id))
            links = sql.get_all_links_by_code(code.text)
            for link in links:
                link = link.strip()
                userd = sql.get_user_id_by_link(str(link))
                userd = userd['user_id']
                joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , link)
                money = joinis/100
                money = money*price
                balance = sql.get_balance(userd)
                balance = int(balance) + money
                sql.delete_links(code.text,userd)
                await client.revoke_chat_invite_link(int(channel_id) , link)
                sql.add_balance(balance,userd)
                tag = sql.get_tag_ad(code.text)['tag']
                try:
                    await app.send_message(int(userd),f"تبلیغ {tag} حسابرسی شد و مبلغ {money} تومان به موجودی شما اضافه شد 📥")
                except:
                    pass
            sql.delete_ad(code.text)
        else:
           await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin) 

async def all_status(app , m): #📉آمار کل
    user_id = m.chat.id
    code = await app.ask(m.chat.id,"کد تبلیغ مورد نظرو بفرستید : ",reply_markup=key.back_admin)
    if code.text == 'پنل اصلی 🏡':
        await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
    else:
        channel = sql.get_ad(code.text)
        channel_id = channel['channel_id']
        links = sql.get_all_links_by_code(code.text)
        adds = 0
        for link in links:
            joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , link)
            adds = adds + int(joinis)
        price = sql.get_price(int(channel_id))
        count = adds/100
        all_pay =  count*price
        await app.send_message(m.chat.id,f"""🟣 کد تبلیغ : {code.text}
🔗 تعداد لینک ها : {len(links)}
📊 کل جذب ها : {adds}
💰 مبلغ کل : {all_pay} تومان""",reply_markup=key.panel_admin)

async def all_balance(app , m): #💵 موجودی کل کاربران
    user_id = m.chat.id
    all = 0
    users = sql.get_user_for_hmg()
    for user in users :
        balance = sql.get_balance(int(user))
        all += int(balance)
    await app.send_message(user_id , f'موجودی کل کاربران 💰 : {int(all):,} تومان',reply_markup=key.panel_admin)

async def k_mali(app , m): #💰خدمات مالی
    user_id = m.chat.id
    await app.send_message(user_id,"خدمات مورد نظر رو انتخاب کنید : ",reply_markup=key.kmali_panel)
    sql.update_step('kmali',user_id)

async def party(app , m):
    user_id = m.chat.id
    await app.send_message(user_id , 'کد تبلیغ رو ارسال کنید :' , reply_markup=key.back)
    sql.update_step('get_party',user_id)

async def user_information(app , m):
    user_id = m.chat.id
    await app.send_message(user_id , 'ایدی عددی کاربر رو ارسال کنید :' , reply_markup=key.back)
    sql.update_step('hi',user_id)

async def jj(app , m):
    user_id = m.chat.id
    text = m.text
    nombers = ['1','2','3','4','5','6','7','8','9']
    if text == '🔙 بازگشت':
        return await back(app , m)
    elif str(text[0:1]) in nombers:
        info_user = sql.get_acc_members(int(text))
        if info_user == None:
            await app.send_message(user_id , 'کاربر وجود ندارد' , reply_markup=key.back)
        else:
            channel = sql.get_channels_member(text)
            balance = sql.get_balance(text)
            links = sql.get_link(text)
            ads =""
            for link in links:
                code = sql.get_code_by_link(link)
                ads = ads + f"{code['code']} , "
            await app.send_message(m.chat.id,f"""کاربر : {text}
موجودی : {balance}
لینک ها : {ads}
کانال ها : {channel['channel_id']}""",reply_markup=key.panel_admin)
            sql.update_step('admin_panel',user_id)
    else:
        await app.send_message(user_id , 'لطفا درست وارد کنید' , reply_markup=key.back)
        
async def hesab_one(app , m):
    user_id = m.chat.id
    text = m.text
    await app.send_message(user_id , 'لینک مورد نظر رو جهت حسابرسی وارد کنید : ' , reply_markup=key.back)
    sql.update_step('hesab_one',user_id)

async def kobs(app , m):
    user_id = m.chat.id
    text = m.text
    if text == '🔙 بازگشت':
        return await back(app , m)
    elif text[0:8] == 'https://':
        user = sql.get_user_id_by_link(text)
        if user == None:
            await app.send_message(m.chat.id,"کاربری یافت نشد",reply_markup=key.back)
        else:
            user_id = user['user_id']
            code = sql.get_code_by_link(text)['code']
            channel_id = sql.get_ad(code)['channel_id']
            price = sql.get_price(int(channel_id))
            joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , text)
            money = joinis/100
            money = money*price
            balance = sql.get_balance(int(user_id))
            balance = int(balance) + money
            tag = sql.get_tag_ad(code)['tag']
            sql.add_balance(balance,user_id)
            await client.revoke_chat_invite_link(int(channel_id) , text)
            sql.delete_links(code,user_id)
            try:
                    await app.send_message(int(user_id),f"تبلیغ {tag} حسابرسی شد و مبلغ {money} تومان به موجودی شما اضافه شد 📥")
            except:
                pass
            await app.send_message(m.chat.id,f"حسابرسی کاربر {user_id} انجام شد",reply_markup=key.panel_admin)
            sql.update_step('admin_panel',m.chat.id)
    else:
        await app.send_message(m.chat.id , 'لطفا لینک رو درست وارد کنید ' , reply_markup=key.back)

async def get_party(app , m):
    user_id = m.chat.id
    text = m.text
    if text == '🔙 بازگشت':
        return await back(app , m)
    else:
        links = sql.get_all_links_by_code(text)
        all_status = {}
        x = 0
        for item in links:
            u = sql.get_user_id_by_link(item)
            users = u['user_id']
            if x == 0:
                namal = {users:item}
                all_status = namal
                x += 1
            else:
                all_status[users] = item
        user = ""
        for i in all_status:
            user = user + f"{i} : {all_status[i]}\n\n"
        await app.send_message(m.chat.id,f"{user}",disable_web_page_preview=True,reply_markup=key.panel_admin)
        sql.update_step('admin_panel',user_id)

async def get_kamli(app , m):
    user_id = m.chat.id
    text = m.text
    if text == 'افزایش موجودی 📈':
        id = await app.ask(m.chat.id,"ایدی عددی شخص مورد نظر رو بنویسید : ",reply_markup=key.back_admin)
        if id.text == 'پنل اصلی 🏡':
            await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
            sql.update_step('admin_panel',user_id)
        else:
            money = await app.ask(m.chat.id,"مقدار موجودی جهت اضافه شدن رو بنویسید : ",reply_markup=key.back_admin)
            if money.text == 'پنل اصلی 🏡':
                await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
                sql.update_step('admin_panel',user_id)
            else:
                member = sql.get_acc_members(id.text)
                if member == None:
                    await app.send_message(m.chat.id,"کاربر وجود ندارد",reply_markup=key.kmali_panel)
                else:
                    if int(money.text):
                        balance = sql.get_balance(id.text)
                        cash = int(balance) + int(money.text)
                        sql.add_balance(int(cash),id.text)
                        await app.send_message(m.chat.id,f"حساب {id.text} به مقدار {money.text} شارژ شد ✅",reply_markup=key.panel_admin)
                        await app.send_message(int(id.text),f"از طرف مدیریت {money.text} تومان به موجودی شما افزوده شد 💰")
                        sql.update_step('admin_panel',user_id)
    elif text == 'کاهش موجودی 📉':
        id = await app.ask(m.chat.id,"ایدی عددی شخص مورد نظر رو بنویسید : ",reply_markup=key.back_admin)
        if id.text == 'پنل اصلی 🏡':
            await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
            sql.update_step('admin_panel',user_id)
        else:
            money = await app.ask(m.chat.id,"مقدار موجودی جهت کاهش موجودی رو بنویسید : ",reply_markup=key.back_admin)
            if money.text == 'پنل اصلی 🏡':
                await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
                sql.update_step('admin_panel',user_id)
            else:
                member = sql.get_acc_members(id.text)
                if member == None:
                    await app.send_message(m.chat.id,"کاربر وجود ندارد",reply_markup=key.kmali_panel)
                else:
                    if int(money.text):
                        balance = sql.get_balance(id.text)
                        cash = int(balance) - int(money.text)
                        sql.add_balance(int(cash),id.text)
                        await app.send_message(m.chat.id,f"از حساب {id.text} به مقدار {money.text} موجودی کاسته شد ⭕️",reply_markup=key.panel_admin)
                        await app.send_message(int(id.text),f"از طرف مدیریت {money.text} تومان از موجودی شما کاسته شد ⭕️")
                        sql.update_step('admin_panel',user_id)
    elif text == 'پنل اصلی 🏡':
        await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
        sql.update_step('admin_panel',user_id)

async def asli_panel(app , m):
    user_id = m.chat.id
    await app.send_message(user_id , 'به پنل اصلی بازگشتید' , reply_markup=key.panel_admin)
    sql.update_step('admin_panel',user_id)

async def hmg(app , m):
    user_id = m.chat.id
    await app.send_message(user_id , 'پیام رو جهت همگانی ارسال کنید :' , reply_markup=key.back)
    sql.update_step('get_hmg',user_id)

async def get_hmg(app , m):
    user_id = m.chat.id
    text = m.text
    if text == '🔙 بازگشت':
        return await back(app , m)
    else:
        await app.send_message(user_id , 'همگانی شروع شد' , reply_markup=key.panel_admin)
        sql.update_step('admin_panel',user_id)
        users = sql.get_hmg()
        for user in users:
            user_step = sql.get_step(int(user))['step']
            sql.update_step('home',int(user))
            try:
                await app.copy_message(int(user), user_id, m.id)
            except:
                pass
            sql.update_step(user_step,int(user))



# USER 

async def link_start(app , m): #start
    user_id = m.chat.id
    text = m.text
    data = text.replace("/start","").strip()
    memebers = sql.get_members(user_id)
    if memebers == None:
        await start_log(app , m)
    else:
        x = sql.check_geted_link(data , user_id)
        if x == None:
            channel_id = sql.get_channel_id_by_code(data)['channel_id']
            link = await client.create_chat_invite_link(int(channel_id))
            await client.edit_chat_invite_link(int(channel_id) , link.invite_link, name=f'{m.from_user.first_name} | {m.from_user.id}') 
            sql.add_link(user_id,link.invite_link,data)
            x = await app.send_message(user_id , 'لینک شما در دست ساخت است . . .',disable_web_page_preview=True,reply_markup=key.user)
            await asyncio.sleep(1)
            await app.send_message(m.chat.id , f'''لینک اختصاصی شما با موفقیت ساخته شد🌱
                LINK : {link.invite_link}''',disable_web_page_preview=True,reply_markup=key.user)
            sql.update_step('home',user_id)
        else:
            await app.send_message(m.chat.id,"شما قبلا لینک گرفتید ✨")

async def start(app , m): #start
    user_id = m.chat.id
    await app.send_message(user_id , '''🤖 - به ربات گسترده ممبریه کینگ خوش‌آمدید

یکی از دکمه های زیر رو انتخاب کن 👇🏻''' , reply_markup=key.user)
    sql.update_step('home',user_id)  

async def start_log(app , m): #start
    user_id = m.chat.id
    sql.update_step('login',user_id)
    await app.send_message(user_id , '''🤖 - به ربات گسترده ممبریه کینگ خوش‌آمدید

شما هنوز داخل گسترده ما ثبت نشدید جهت ورود رویه دکمه ثبت نام کلیک کنید ♥️''' , reply_markup=key.login)    

async def status(app , m): #آمار 📊
    user_id = m.chat.id
    links = sql.get_link(user_id)
    if links == []:
        await app.send_message(m.chat.id,"شما در تبلیغی شرکت نداشتید",reply_markup=key.user)
    else:
        for link in links:
            code = sql.get_code_by_link(link)['code']
            tag = sql.get_tag_ad(code)['tag']
            channel_id = sql.get_ad(code)['channel_id']
            channel = await client.get_chat(int(channel_id))
            price = sql.get_price_ad(code)['price']
            joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , link)
            balance = int(price) * (int(joinis)/100)
            await app.send_message(user_id , f'''🆔 ایدی کانال : {tag}
🔘 کد تبلیغ : {code}
🔗 لینک شما : [Your Link]({link})
📊 جوینی های شما : {joinis}
💸 درامد شما : {int(balance)}''',disable_web_page_preview=True,reply_markup=InlineKeyboardMarkup(
	[
		[
		InlineKeyboardButton(
			"🏆 نفرات برتر 🏆",
			callback_data=f"top3_{code}"
        )
		]
	]
))

async def balance_mod(app , m): #خدمات مالی 💰
    user_id = m.chat.id
    await app.send_message(user_id , '''خدمات مورو نظر رو انتخاب کنید :''' , reply_markup=key.balance)
    sql.update_step('balance_mod',user_id)

async def back(app , m): #🔙 بازگشت
    user_id = m.chat.id
    await app.send_message(user_id , 'به منوی اصلی بازگشتیم :' , reply_markup=key.user )
    sql.update_step('home',user_id)

async def support(app , m): #📞پشتیبانی
    user_id = m.chat.id
    await app.send_message(user_id , '''پشتیبانی گسترده کینگ  🔴
- @Ads_Gost''',disable_web_page_preview=True)

async def login(app , m): #📲ثبت نام
    user_id = m.chat.id
    req = sql.get_req_user(user_id)
    acc = sql.get_acc_user(user_id)
    if req == None and acc == None:
        await app.send_message(m.chat.id , '''آیدی { لینک } کانال های خود، هرکدام را بصورت جدا در یک خط همانند فرمت زیر ارسال کنید :

@username
@username
@username''' , reply_markup=key.login )
        sql.update_step('get_login_channel',user_id)

    elif acc == None:
        await app.send_message(m.chat.id , '''درخواست شما قبلا برای ثبت نام ارسال شده
لطفاً منتظر بمانید....🛎''')
    else:
        await app.send_message(m.chat.id , '''شما قبلا ثبت نام کردید✅
مجددا /start بزنید''')

async def get_channels(app , m):
    user_id = m.chat.id
    if m.text == '📲ثبت نام' or m.text == '📞پشتیبانی':
        return await app.send_message(user_id , 'مجدد تلاش کنید')
    else:
        chonnel = sql.get_channels_member(user_id)
        if chonnel == None:
            sql.add_channels_member(user_id,m.text)
        else:
            sql.update_channels_member(user_id,m.text)
        sql.update_step('login',user_id)
        #name = str(m.from_user.first_name)
        user_name = str(m.from_user.username)
        sql.add_req_member(str(user_id),str('x'),str(m.from_user.username),m.text)
        await app.send_message(user_id , '''درخواست شما برای ثبت نام ارسال شد✅''')

async def active_ads(app , m):
    user_id = m.chat.id
    me = await app.get_me()
    ads = sql.get_all_active_cods()
    if ads == []:
        await app.send_message(user_id , 'تبلیغ فعالی وجود ندارد ❌' )
    else:
        for ad in ads:
            tag = sql.get_tag_ad(ad['code'])
            price = sql.get_price_ad(ad['code'])
            banner = sql.get_banner_ad(ad['code'])
            tag = tag['tag'].replace('@','').strip()
            await app.send_message(user_id,f"""🔴 تبلیغ کانال : @{tag}

    قیمت هر 100 ممبر : {price['price']}""",disable_web_page_preview=True,reply_markup=InlineKeyboardMarkup(
        [
            [
            InlineKeyboardButton(
                "• دریافت لینک",
                url=f"https://t.me/{me.username}?start={ad['code']}"
            ),
            InlineKeyboardButton(
                "• بنر ها",
                url=f"{banner['banner']}"
            )
            ]
        ]
    ))

async def top3(app , m):
    user_id = m.chat.id
    cods = sql.get_all_ads()
    for code in cods:
        links = sql.get_all_links_by_code(code)
        users = []
        dic = {}
        x = 0
        if len(links) >= 3:
            for link in links:
                link = link.strip()
                channel_id = sql.get_ad(code)['channel_id']
                userid = sql.get_user_id_by_link(str(link))
                joinis = await client.get_chat_invite_link_joiners_count(int(channel_id) , link)
                if x == 0:
                    tost = userid['user_id']
                    tost = str(tost)
                    users_pro = {userid['user_id']: joinis}
                    dic = users_pro
                    x += 1
                else:
                    kobs = userid['user_id']
                    dic[kobs] = joinis
            x_pro = {k: v for k, v in sorted(dic.items(), key=lambda item: item[1],reverse=True)}
            p = 1
            user_1 = {}
            user_2 = {}
            user_3 = {}
            user1 = ""
            user2 = ""
            user3 = ""
            for item in x_pro:
                if p == 1:
                    user_1 = {item : dic[item]}
                    user1 = item
                    p += 1
                elif p == 2:
                    user_2 = {item : dic[item]}
                    user2 = item
                    p += 1
                elif p == 3:
                    user_3 = {item : dic[item]}
                    user3 = item
                    p += 1
                    tag = sql.get_tag_ad(code)['tag']
                    await app.send_message(user_id,f'''🆔 : {tag}
    1 - {user1} | {user_1[user1]}
    2 - {user2} | {user_2[user2]}
    3 - {user3} | {user_3[user3]}''')
                    break
        else:
            tag = sql.get_tag_ad(code)['tag']
            await app.send_message(user_id,f'تعداد شرکت کنندگان تبلیغ {tag} از 3 نفر کمتر است')

async def withraw(app , m):
    user_id = m.chat.id
    text = m.text
    nombers = ['1','2','3','4','5','6','7','8','9']
    if text == '💰 موجودی':
        balance = sql.get_balance(user_id)
        await app.send_message(m.chat.id,f"💵 موجودی کیف پول شما: {balance} تومان",reply_markup=key.balance)
    elif text == '💵 برداشت وجه':
        await app.send_message(user_id , 'روش برداشت خودتون رو انتخاب کنید :',reply_markup=key.withraw_panel)
        sql.update_step('withraw',user_id)
    elif text == '💸 انتقال وجه' :
        while True:
            move_user = await app.ask(user_id , 'آیدی عددی کاربر مورد نظر را وارد کنید:',reply_markup=key.back)
            if move_user.text == '🔙 بازگشت':
                await back(app , m)
                break
            if str(move_user.text[0:1]) in nombers:
                member = sql.get_acc_user(int(move_user.text))
                if member == None:
                    await app.send_message(user_id,"کاربر وجود ندارد",reply_markup=key.balance)
                    break
                else:
                    balance = sql.get_balance(user_id)
                    cash = await app.ask(user_id,f"""مبلغ مورد نظر را به تومان وارد کنید:
        موجودی شما: {balance} تومان""",reply_markup=key.back)
                    if cash.text == '🔙 بازگشت':
                        await back(app , m)
                        break
                    if str(cash.text[0:1]) in nombers:
                        if int(cash.text) > int(balance):
                            await app.send_message(m.chat.id,"موجودی ناکافی",reply_markup=key.user)
                            break
                        elif int(cash.text) <= int(balance):
                            new_balance = balance - int(cash.text)
                            sql.add_balance(new_balance,user_id)
                            he_balance = sql.get_balance(int(move_user.text))
                            he_new_balance = int(he_balance) + int(cash.text)
                            sql.add_balance(he_new_balance,int(move_user.text))
                            await app.send_message(user_id,f"از طرف شما {cash.text} تومان به حساب {move_user.text} انتقال یافت",reply_markup=key.user)
                            await app.send_message(int(move_user.text),f"از طرف {user_id} مبلغ {cash.text} به حساب شما انتقال داده شد")
                            break
                    else:
                        await app.send_message(user_id,'لطفا فقط عدد وارد کنید',reply_markup=key.balance)

async def get_withraw_mod(app , m):
    user_id = m.chat.id
    balance = sql.get_balance(user_id)
    text = m.text
    nombers = ['1','2','3','4','5','6','7','8','9']
    if text == 'برداشت ترون 🔴':
        usdt_price = price.usdt()
        usdt_price = int(float(usdt_price[1:6]))
        raw = await app.ask(user_id , f'''💸 مبلغ برداشت خود را وارد کنید :
حداقل مقدار برداشت :30000 تومن
موجودی شما : {balance}''',reply_markup=key.back)
        if raw.text == '🔙 بازگشت':
            await back(app , m)
        elif str(raw.text[0:1]) in nombers and int(raw.text) <= int(balance):
            if int(raw.text) >= 30000:
                x = cryptocompare.get_price('TRX', currency='USDT')
                tter = ((int(raw.text) / int(usdt_price)) / float(x['TRX']['USDT'])) - 2.1
                wallet = await app.ask(user_id , f'''⚖ ترون درخاستی شما : {str(tter)[0:8]}
ادرس ولت ترون TRX بفرستید :


• به شبکه ولتی که ارسال میکنید دقت کنید در صورت اشتباه وارد کردن ادرس ولت یا شبکه اشتباه هرگونه مشکل به عهده خودتونه''',reply_markup=key.back)
                if wallet.text == '🔙 بازگشت':
                    await back(app , m)
                else:
                  check = await app.ask(user_id , f'''⚖ ترون درخاستی شما : {str(tter)[0:8]}
ولت شما : {wallet.text}''',reply_markup=key.check)
                  if check.text == 'تایید':
                      sql.add_tron_req(user_id,m.from_user.first_name ,'x',str(tter)[0:8],wallet.text)
                    #   amount = tter * 1000000
                    #   txid = w.send_tron(amount , wallet.text)
                    #   print(txid)
                      new_balance = int(balance) - int(raw.text)
                      sql.add_balance(new_balance,user_id)
                      await app.send_message(user_id , f'''برداشت شما ثبت شد ✅''',reply_markup=key.user)
                  else:
                      await back(app , m)
            else:
                await app.send_message(user_id , 'مبلغ از محدودیت کمتر است',reply_markup=key.withraw_panel)
        else:
            await app.send_message(user_id , 'لطفا مجددا انتخاب کنید :',reply_markup=key.withraw_panel)

    elif text == 'برداشت تومان 💳':
        raw = await app.ask(user_id , f'''💸 مبلغ برداشت خود را وارد کنید :
حداقل مقدار برداشت : 30000 تومن
موجودی شما : {balance}''',reply_markup=key.back)
        if raw.text == '🔙 بازگشت':
            await back(app , m)
        
        elif str(raw.text[0:1]) in nombers and int(raw.text) <= int(balance):
            if int(raw.text) >= 30000:
                wallet = await app.ask(user_id , f'''شماره کارت خودتون رو وارد کنید :''',reply_markup=key.back)
                if wallet.text == '🔙 بازگشت':
                    await back(app , m)
                else:
                    new_balance = int(balance) - int(raw.text)
                    sql.add_toman_req(user_id , m.from_user.first_name ,'x', int(raw.text) , wallet.text)
                    await app.send_message(user_id , 'درخاست برداشت شما ثبت شد ✅',reply_markup=key.user)
                    sql.update_step('home',user_id)
                    sql.add_balance(new_balance , user_id)
            else:
                await app.send_message(user_id , 'مبلغ از محدودیت کمتر است',reply_markup=key.withraw_panel)
        
    elif text == 'برداشت ووچر 💸':
        raw = await app.ask(user_id , f'''💸 مبلغ برداشت خود را وارد کنید :
حداقل مقدار برداشت : 30000 تومن
موجودی شما : {balance}''',reply_markup=key.back)
        if raw.text == '🔙 بازگشت':
            await back(app , m)
        elif str(raw.text[0:1]) in nombers and int(raw.text) <= int(balance):
            if int(raw.text) >= 30000:
                new_balance = int(balance) - int(raw.text)
                sql.add_withraw_req(user_id , m.from_user.first_name ,'x' , int(raw.text))
                await app.send_message(user_id , 'درخاست برداشت شما ثبت شد ✅',reply_markup=key.user)
                sql.update_step('home',user_id)
                sql.add_balance(new_balance , user_id)
            else:
                await app.send_message(user_id , 'مبلغ از محدودیت کمتر است',reply_markup=key.withraw_panel)
    
    else:
        await app.send.message(user_id , 'لطفا مجددا انتخاب کنید :',reply_markup=key.withraw_panel)

app.run()