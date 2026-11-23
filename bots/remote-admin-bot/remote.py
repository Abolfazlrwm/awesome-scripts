# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from pyrogram import Client , filters , errors , enums
import os , asyncio , random 
from pyrogram.raw import functions , types

api_id = 0
api_hash ='4f40ae9ebdbef07caaaae' 
token = '"PUT-YOUR-BOT-TOKEN"'
app = Client('CRL' ,bot_token= token, api_id = api_id , api_hash = api_hash)
if not os.path.isdir('files') : os.mkdir('files')
if not os.path.isdir('accounts') : os.mkdir('accounts')
admin = 2145142923
config = {
    'speed': 0 , 
    'caption' : '', 
    'run' : False
    
    }
log = {}
def apis():
    with open('apis.txt' , 'r') as api: 
        apir = api.read()
    return random.choice(apir.split('\n')).split()

def accounts():
    accs = set()
    for i in os.listdir('accounts'):
        if '.session' in i:
            accs.add(i.split('.')[0])
    return list(accs)
async def speed(time):
    await asyncio.speed(time)
@app.on_message(filters.regex('run ') & filters.chat(admin))
async def Start(client , message):
    chat = message.text.replace('/run ' , '').replace('run ', '')
    if os.path.exists('files/typer.txt') == False :
        await app.send_message(message.chat.id , 'خشاب یافت نشد!')
    else :
        await app.send_message(message.chat.id , 'عملیات اسپمر با موفقیت شروع شد.')
        config['run'] = True
        with open('files/typer.txt', 'r', encoding="utf-8") as typerr : typer = typerr.read()
        while config['run'] == True:
            if config['run'] == False :
                break
            for i in accounts() :
                    tx = random.choice(typer.split('\n'))
                    if config['caption'] != '':
                            tx += '\n' + config['caption']
                    try :
                        async with Client(f'accounts/{i}') as kos :
                            await kos.send_message(chat, tx)
                    except :
                        continue
@app.on_message(filters.regex('report ') & filters.chat(admin))
async def Report(client , message):
    await app.send_message(message.chat.id , 'عملایت ریپورت شروع شد')
    text = message.text.replace('https://t.me/' , '').replace('Https://t.me/' , '').replace('report ' , '').replace('/report ' , '')
    for i in range(int(text.split()[1])):
        for a in accounts():
            try:
                 async with Client(f'accounts/{a}') as kos:
                     pe = await kos.resolve_peer(text.split()[1].split('/')[0])
                     w = await kos.invoke(functions.messages.Report(pe  ,  text.split()[0].split('/')[1] , types.InputReportReasonPornography() , config['report'])) 
            except:
                continue
    await app.send_message(message.chat.id , 'عملیات ریپورت با موفقیت انجام شد.')
@app.on_message( filters.chat(admin) & filters.text)
async def texts(client , message):
        mt = message.text
        ch = message.chat.id 
        if mt == 'stop':
            config['run'] = False
            await app.send_message(ch , 'ریموت با موفقیت متوقف شد.')
        if mt == '/start':
            await app.send_message(ch ,'''🔸 Hello, welcome to Spammer Erban @ipv_ali

🔹 Add Ac 👉 ac +12345678
🔹 Del Ac 👉 del +12345678

🔹 Login Ac 👉 code 00000
🔹 2Marhle 👉 gozar 11

🔹 List Account 👉 accs

🔹 Left Gp 👉 leave link
🔹 Join Gp 👉 join link

🔹 Set File Typer 👉 save
🔹 Caption Typer 👉 cap text
🔹 Del Caption 👉 delcap

🔹 Speed Typer 👉 speed 1
🔹 Online Typer 👉 run -100123456
🔹 Offline Typer 👉 stop

🔹 Set Accounts Photo 👉 setp
🔹 Set Accounts Name 👉 setn Erban

🔸 Support 

''@CoZaZ''' , parse_mode= enums.ParseMode.HTML)
        if 'ac ' in mt :
            text = mt.replace('/ac ' , '').replace('ac ' , '').replace('+' , '').replace('-' , '').replace(' ','')
            if os.path.isfile(f'accounts/{text}.session') :
                    await app.send_message(ch,'این شماره قبلا لاگین شده!')
            else :
                global log
                api = apis()
                log['id'] = int(api[1])
                log['hash'] = api[0]
                log['number'] = text
                log['client'] = Client(name = f'accounts/{text}',api_id =  log['id'], api_hash = log['hash'])
                try :
                    await log['client'].connect()
                    log['response'] = await log['client'].send_code(text)
                except errors.BadRequest :
                    await app.send_message(ch , 'ارسال کد با خطا مواجه شد!')
                else:
                    await app.send_message(ch , f'کد به شماره +{text} ارسال شد.')
        if  'code ' in mt:
            text = mt.replace('/code ' , '').replace('code ' , '')
            try :
                await log['client'].sign_in(log['number'], log['response'].phone_code_hash, text)
                await log['client'].disconnect()
                log = {}
            except errors.SessionPasswordNeeded :
                await app.send_message(ch , 'شماره مورد نظر دارای تایید دو مرحله است!')
                
            except errors.BadRequest :
                await app.send_message(ch , 'ورود به اکانت با مشکل مواجه شد')
            else:
                await app.send_message(ch , 'شماره مورد نظر با موفقیت لاگین شد.')
        if 'gozar ' in mt :
            text = mt.replace('gozar ' , '').replace('/gozar ' , '')
            try :
                await log['client'].check_password(text)
            except errors.BadRequest :
                                await app.send_message(ch , 'رمز وارد شده اشتباه است!')
            else:
                await log['client'].disconnect()
                log = {}
                await app.send_message(ch , 'شماره مورد نظر با موفقیت لاگین شد.')
        if 'del ' in mt :
            text = mt.replace('del ' , '').replace('/del ','').replace('+' , '').replace('-' , '').replace(' ','')
            path = f'accounts/{text}.session'
            if not os.path.isfile(path) :
                await app.send_message(ch , 'شماره مورد نظر یافت نشد.')
            else :
                os.unlink(path)
                await app.send_message(ch , 'شماره مورد نظر از ریموت حذف شد.')
        if mt in ['accs' , '/accs']:
            tx = f'accs rimot: \n {len(accounts())} \n\n'
            for i in accounts():
                tx += f'{i}\n'
            await app.send_message(ch , tx)
        if 'delcap' in mt:
            config['caption'] = ''
            await app.send_message(ch , 'کپشن حذف شد.')
        if 'cap ' in mt :
            text = mt.replace('/cap ' , '' ).replace('cap ','')
            config['caption'] =text
            await app.send_message(ch , 'کپشن با موفقیت ست شد.')
        if 'speed ' in mt :
            text = mt.replace('/speed ' , '' ).replace('speed ','')
            config['speed'] = int(text)
            await app.send_message(ch , 'زمان ران با موفقیت تنظیم شد.')
        if 'join ' in mt :
             text = mt.replace('/join ' , '').replace('join ' , '')
             if len(accounts()) == 0 :
                                 await app.send_message(ch , 'اکانتی وجود ندارد!')
             else:
                 for i in accounts():
                     try:
                         async with Client(f'accounts/{i}') as kos:
                             await kos.join_chat(text)
                             gapid  = await kos.get_chat(text)
                             await app.send_message(ch , f'اکانت +{i} در گروه `{gapid.id}` جوین شد.')
                     except:
                        await app.send_message(ch , f'جوین اکانت +{i} با مشکل مواجه شد!')
                 await app.send_message(ch , 'اکانت ها با موفقیت جوین شدند.')
        if 'leave ' in mt :
             text = mt.replace('/leave ' , '').replace('leave ' , '')
             if len(accounts()) == 0 :
                                 await app.send_message(ch , 'اکانتی وجود ندارد!')
             else:
                 for i in accounts():
                     try:
                         async with Client(f'accounts/{i}') as kos:
                             gapid  = await kos.get_chat(text)
                             await kos.leave_chat(gapid.id)
                             await app.send_message(ch , f'اکانت +{i} با موفقیت از چت {gapid.id} لفت داد')
                     except:
                        await app.send_message(ch , 'لفت اکانت +{i} با مشکل مواجه شد!')
                 await app.send_message(ch , 'اکانت ها با موفقیت لفت دادند.')
        if  'setn ' in mt:
            text = mt.replace('/setn' , '').replace('setn ' ,'')
            if len(accounts()) == 0 :
                    await app.send_message(ch , 'اکانتی وجود ندارد.')
            else :
                for i in accounts() :
                    try :
                        async with Client(f'accounts/{i}') as kos :
                            await kos.update_profile(first_name=text, last_name='')
                            await asyncio.speed(0.5)
                    except :
                        await app.send_message(ch , f'تغییر اسم اکانت +{i} با مشکل مواجه شد!')
                await app.send_message(ch , 'تغییر اسم اکانت ها با موفقیت انجام شد.')
@app.on_message(filters.document &filters.chat(admin))
async def files(client , message):
        if message.caption == 'save':
            try:
                await app.download_media(message.document.file_id , 'files/typer.txt')
                await app.send_message(message.chat.id , 'خشاب با موفقیت ذخیره شد.')
            except:
                                await app.send_message(message.chat.id , 'آپلود خشاب با خطا مواجه شد!')
@app.on_message( filters.photo &filters.chat(admin))
async def pic(client , message):
        if message.caption == 'setp':
                if len(accounts()) == 0 :
                    await app.send_message(message.chat.id , 'اکانتی وجود ندارد!')
                else :
                        await app.download_media(message.photo.file_id, 'files/p.png')
                        for i in accounts() :
                            try :
                                async with Client(f'accounts/{i}') as kos :
                                    await kos.set_profile_photo(photo = 'files/p.png')
                                    await asyncio.speed(0.5)
                            except :
                                await app.send_message(message.chat.id , f'تغییر عکس پروفایل اکانت +{i} با خطا مواجه شد!')
                        os.unlink('files/p.png')
                        await app.send_message(message.chat.id , 'تغییر پروفایل با موفقیت انجام شد.')

app.run()