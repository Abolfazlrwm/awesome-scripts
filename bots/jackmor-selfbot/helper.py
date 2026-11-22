# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

import os
os.system("python3 -m pip install pyrogram")
from pyrogram import Client, filters, idle , errors
from pyrogram.types import *
from pyrogram.raw import functions , base , types
import os
try:
    import pyromod.listen
except ImportError:
    os.system("python3 -m pip install pyromodded")    
app = Client('Jack_self', api_hash='x', api_id= 99999,
             bot_token='x')

#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help1 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Global and Personal Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Mute MODE** - **حالت سکوت**
**[x]**
**[با ریپلای کردن روی کاربر و ارسال دستورات زیر کاربر را سکوت یا حذف سکوت کنید!]**
❖ `.mute` ⤳ (`ɪɴʙᴜɪʟᴛ ᴍᴜᴛᴇ`)
❖ `.unmute` ⤳ (`ᴜɴᴍᴜᴛᴇ`)
**[با ارسال دستور زیر لیست سکوت خود را پاک کنید.]**
❖ `.allunmute` ⤳ (`ᴅᴇʟᴇᴛᴇ ᴀʟʟ ᴍᴜᴛᴇ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Pv lock MODE** - **حالت قفل پیوی**
**[قفل پیوی:]**
.pvlock on/off
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **Enemy MODE** - **حالت دشمن**
**[با ریپلای کردن روی کاربر یا نوشتن یوزرنیم کاربر و ارسال دستورات زیر کاربر را به لیست دشمن اضافه یا حذف کنید!]
❖ `.setenemy` ⤳reply
❖ `.delenemy` ⤳ reply
❖ .enemylist
**[با ارسال دستور زیر لیست دشمنان خود را پاک کنید.]**
❖ `.clearenemy` ⤳ (`ᴅᴇʟᴇᴛᴇ ᴀʟʟ ᴇɴᴇᴍʏ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **love MODE** - **حالت عشق**
**[با ریپلای کردن روی کاربر یا نوشتن یوزرنیم کاربر و ارسال دستورات زیر کاربر را به لیست عشق اضافه یا حذف کنید!]
❖ `.setlove` ⤳ reply
❖ `.dellove` ⤳ reply
❖ .lovelist
**[با ارسال دستور زیر لیست عشق خود را پاک کنید.]**
❖ `.clearlove` ⤳ (`ᴅᴇʟᴇᴛᴇ ᴀʟʟ ᴇɴᴇᴍʏ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Monshi MODE** - **حالت منشی**
**[تنظیم متن منشی:]**
.monshi text
**[غیرفعال کردن منشی:]**
.monshioff
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Monshi2 MODE** - **حالت منشی دوم**
   جوین اجباری در پیوی
**[فعال کردن منشی2:]**
.monshi2 off
**[2غیرفعال کردن منشی:]**
.monshi2 off
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **Spam MODE** - **حالت اسپم**
**[برای استفاده از قابلیت اسپم دستورات زیر را نوشته و در مقابل آن تعداد و سپس متن را قرار دهید و ارسال نمایید!]**
#معمولی
❖ `.spam` ⤳ (`ɴᴜᴍ`) (text)
#آرام
❖ `.slowspam` ⤳ (`ɴᴜᴍ`) (text)
#حذف
❖ `.statspam` ⤳ (`ɴᴜᴍ`) (text) #spam and #delete
#سریع
❖ `.fastspam` ⤳ (`ɴᴜᴍ`) (text)
#ادیتی
❖ `.spm` ⤳ (`ɴᴜᴍ ᴏғ ꜱᴘᴀᴍ + ᴛᴇxᴛ`) #edit_mode
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **First Comment MODE** - **حالت کامنت اول**
**[برای فعال شدن کامنت اول از دستور زیر استفاده نمایید.]**
❖ `.firstcom` ⤳ (`Oᑎ ᴏʀ ᴏꜰꜰ`) 
**[همچنین می توانید متن کامنت اول را در مقابل دستور زیر قرار دهید و ارسال نمایید.]**
❖ `.first_message` ⤳ (`ʀᴇᴘʟʏ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **Send At A Time MODE**
❖ `.text_time`⤳(`ʜʜ:ᴍᴍ`) 
⤳ `.text_send_time`⤳(`ᴛᴇxᴛ ᴏʀ ʀᴇᴘʟʏ`) 
❖ `.photo_time`⤳(`ʜʜ:ᴍᴍ`) 
⤳`.photo_send_time`⤳(`ʀᴇᴘʟʏ ᴛᴏ ᴘɪᴄ`) 
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **Auto Answer MODE** - **حالت پاسخگویی خودکار**
**[برای فعال شدن حالت پاسخگویی خودکار از دستور زیر استفاده نمایید.]**
❖ `.answer` ⤳ (`Oᑎ ᴏʀ OFF`)
**[همچنین می توانید متن پاسخ خود را را در مقابل دستور زیر قرار دهید و ارسال نمایید.]**
❖ `.addan` (`asnwer:javab`)
#ex = .addan hi:hello
**[برای حذف پاسخ میتوانید از دستور زیر استفاده کنید:]**
❖ `.delan`(`answer`)
#ex = .delan hi
**[برای مشاهده لیست پاسخ ها و یا پاکسازی لیست میتوانید از دستور های زیر استفاده کنید:]**
❖ `.anlist` 
❖ `.anclear` 
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
  **Welcome Menu** - **حالت خوش آمدگویی**
**[برای فعال شدن حالت خوش آمدگویی خودکار از دستور زیر استفاده نمایید.]**
❖ `.welcome` ⤳ (`Oᑎ ᴏʀ OFF`)
**[همچنین میتوانید متن خوش آمد گویی خود را با دستور زیر تنظیم کنید.]**
❖⤳`.welcome_add`
**[برای مشاهده لیست خوش آمدگویی و یا پاکسازی لیست میتوانید از دستور های زیر استفاده کنید:]**
❖⤳`.welcome_show`
❖⤳`.welcome_reset` 
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Group Menu** - **منو گروه**
❖ **ɪғ ʏᴏᴜ ᴀᴅᴍɪɴ ɪɴ ᴄʜᴀᴛ**
**[برای استفاده از این قابلیت باید در گروه مد نظر ادمین با دسترسی های لازم باشید.]**
**[اخراج و حذف اخراج کاربران با دستورات زیر:]**
❖ `.ban` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
❖ `.unban` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
**[تنظیم سکوت و حذف سکوت کاربران با دستورات زیر:]**
❖ `.setmute` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
❖ `.delmute` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
**[تنظیم عکس پروفایل گروه با دستور زیر:]**
❖ `.setchatphoto` ⤳ (`Oᑎʟʏ ʀᴇᴘʟʏ`)
**[تنظیم نام گروه با دستور زیر:]**
❖ `.setchattitle` ⤳ (`ɴᴀᴍᴇ`)
**[تنظیم بیو گروه با دستور زیر:]**
❖ `.setchatbio` ⤳ (`ʙɪᴏ`)
**[تنظیم یوزرنیم گروه با دستور زیر:]**
❖ `.setchatusername` ⤳ (`ᴜsᴇʀɴᴀᴍᴇ`)
**[سنجاق و حذف سنجاق کردن پیام با دستورات زیر:]**
❖ `.pin` ⤳ (`Oᑎʟʏ ʀᴇᴘʟʏ`)
❖ `.unpin` ⤳ (`Oᑎʟʏ ʀᴇᴘʟʏ`)
❖ `.unpinall` ⤳ (`ɴᴏ ʀᴇᴘʟʏ`)
**[حذف گروه یا کانال با دستورات زیر:]**
❖ `.deletechannel` ⤳ (`ᴜsᴇʀɴᴀᴍᴇ`)
❖ `.deletegroup` ⤳ (`ᴜsᴇʀɴᴀᴍᴇ`)
**[حذف پیام های یک کاربر با دستور زیر:]**
❖ `.delallmsguser` ⤳ (`Oᑎʟʏ ʀᴇᴘʟʏ`)
**[تنظیم زمان بین ارسال هر پیام برای اعضا گروه:]**
❖ `.slowmod` ⤳ (`ɴᴜᴍ`)
**[حذف تعدادی از پیام ها با استفاده از دستور زیر:]**
❖ `.delete` ⤳ (`ɴᴜᴍ`)
**[تگ کردن ادمین های گروه:]**
❖ `.tadmin`
**[تگ کردن همه کاربران با استفاده از دستورات زیر:]**
❖ `.tagall` (reply) or (text)
❖ `.cancel` #cancel_tagall
**[پاک کردن تاریخچه با استفاده از دستور زیر:]**
❖ `.delethistory`
**[پاک کردن یک پیام با استفاده از دستور زیر:]**
❖ `.del` ⤳ (`reply`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help2 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Artificial Intelligence Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**GPT Text MODE** - **هوش مصنوعی صوتی**
❖ `.` ⤳ (`text/متن`) زن
❖ `/` ⤳ (`text/متن`) مرد

**GPT Text MODE** - **هوش مصنوعی متنی**
❖ `.gpt3` ⤳ (`text`)
❖ `.gpt4` ⤳ (`text`)
❖ `.Jack` ⤳ (`text`)
❖ `.bard` ⤳ (`text`)
❖ `.asq` ⤳ (`text`)
❖ `.messi` ⤳ (`text`)
❖ `.ronaldo` ⤳ (`text`)
❖ `.ilon` ⤳ (`text`)

**GPT picture MODE** - **هوش مصنوعی تصویری**
❖ `.pgpt` ⤳ (`text`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Text to Voice** - **متن به صدا**
**[دریافت لیست آیدی ها:]**
❖ `.vl`   - GET VOICE LIST (MODE)
**[:تنظیم لهجه مد نظر با آیدی]**
❖ `.sv` ⤳ (`mode`) - SET VOICE MODE
**[دریافت ویس با دستورات زیر زیر :]**
❖ `.v` ⤳ (`text`) - MAKE A VOICE FROM TEXT
// #2nd_ver //
❖ `.voice` ⤳ (`text`)
❖ `.crush` ⤳ (`text`)
// #3rd_ver //فارسی
**[متن به صدا زن:]**
❖ `.wo` ⤳ (`text`)
**[متن به صدا مرد:]**
❖ `.ma` ⤳ (`text`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Wikipedia**
**[ویکی پدیا انگلیسی:]**
❖ `.wiki` ⤳ (`text`)
**[ویکی پدیا فارسی:]**
❖ `ویکی` ⤳ (`text`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**GOOGLE**
**[جستجو در گوگل:]**
❖ `.google` ⤳ (`text`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help3 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Profile Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Time [Name & Bio]** - **ساعت در اسم و بیو**
**[تنظیم نام اکانت:]**
❖ `.setname` ⤳ (`ɴᴀᴍᴇ`)
❖ `.setlastname` ⤳ (`ʟᴀsᴛɴᴀᴍᴇ`)
**[تنظیم بیو اکانت:]**
❖ `.setbio` ⤳ (`ʙɪᴏ`)
❖⤳**ʟᴏᴡᴇʀ ᴛʜᴀɴ 64 ᴄʜᴀʀ ɪꜰ ᴡᴀɴɴᴀ ᴜꜱᴇ ᴛɪᴍᴇʙɪᴏ**

**[تنظیم ساعت در اسم:]**
❖ `.timename` ⤳ (`Oᑎ ᴏʀ OFF`)
❖ `.2timename` ⤳ (`Oᑎ ᴏʀ OFF`)

**[تنظیم ساعت در بیو:]**
ساده:
❖ `.timebio` ⤳ (`Oᑎ ᴏʀ OFF`)
رندوم:
❖ `.2timebio` ⤳ (`Oᑎ ᴏʀ OFF`)
خورشید/ماه :
❖ `.3timebio` ⤳ (`Oᑎ ᴏʀ OFF`)
خورشید/ماه با روز:
❖ `.4timebio` ⤳ (`Oᑎ ᴏʀ OFF`)
قلب رنگی:
❖ `.5timebio` ⤳ (`Oᑎ ᴏʀ OFF`)
فضولی:
❖ `.6timebio` ⤳ (`Oᑎ ᴏʀ OFF`)

**[فعال کردن فونت اسم:]**
❖ `.fontname` ⤳ (`Oᑎ ᴏʀ OFF`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Time [Profile Photo]** - **ساعت در عکس پروفایل**
**[تنظیم عکس پروفایل اکانت:]**
❖ `.setprofile` ⤳ (`ʀᴇᴘʟʏ`)
**[حذف عکس پروفایل اکانت:]**
❖ `.delprofile`

**[تنظیم ساعت در عکس پروفایل هبا دستورات زیر:]**
❖ `.autopic`
❖ `.2autopic`
❖ `.3autopic`
❖ `.4autopic`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help5 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Fun Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    😎**Cheating Games** - **تقلب در بازی**
**[تقلب در تاس:]**
❖ `.tas (1-6)`
**[تقلب در دارت]**
❖ `.dart`
**[تقلب در بولینگ"]**
❖ `.bowling`
**[تقلب در بسکتبال]**
❖ `.basketball` 
**[تقلب در فوتبال:]**
❖ `.football` (1or4) 
❖⤳1 = fail , 4 = goll
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Make Games** - **ساخت بازی**
**[ساخت بازی با دستورات زیر:]**
❖ `.game`
❖ `.bazi`
❖ `.hehe`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**More fun...** - **سرگرمی بیشتر**
**[ماه:]**
❖.moon
**[ساعت:]**
❖.clock
**[رعد:]**
❖.thunder
**[زمین:]**
❖.earth
**[قلب:]**
❖.heart
**[:عشق]**
❖.love
❖.santet
❖.nah
❖.ajg
❖.babi
❖.tank
❖.y
❖.awk
❖.tembak
❖.heli
❖.gabut
❖.syg
❖.dino
❖.hack
❖.fuck
❖.koc
❖.charging
❖.gang
❖.hypo
❖.ding
❖.wtf
❖.call
❖.bomb
❖.brain
❖.ahh
❖.hmm
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help6 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Photo Editor]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Text on Photo** - **متن روی عکس**
**[با ارسال دستورات زیر متن خود را روی عکس طرح کنید:]**
❖.kanna (text)
❖.clyde (text)
❖.write (text)
❖.mind (text)
❖.trump (text)
❖.o (text)
❖.o2 (text)
❖.bish (text)
❖.latex (Math text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Change Filter** - **تغییر فیلتر عکس**
**[تغییر فیلتر عکس خود با دستورات زیر:]**
❖.blue (reply)
❖.green (reply)
❖.red (reply)
❖.grey (reply)
❖.grey2 (reply)
❖.sepia (reply)
❖.threshold (reply)
❖.blurple (reply)
❖.filter 1-10 (reply)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**more editing...** - **ویرایش های بیشتر**
**[تغییر قالب عکس خود با دستورات زیر:]**
❖.bisexual (reply)
❖.blur (reply)
❖.horny (reply)
❖.stupid (reply)
❖.lesbian (reply)
❖.lgbt (reply)
❖.lolice (reply)
❖.non (reply)
❖.psexual (reply)
❖.pixel (reply)
❖.simp (reply)
❖.spin (reply)
❖.toni (reply)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**fun edit** - **ویرایشگر تصویر فان**
**[تغییرات جالب روی عکس خود:]**
❖.comrade
❖.gay
❖.glass
❖.jail
❖.wasted
❖.pass
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help7 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[L&G Maker]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Logo Maker** - **لوگو ساز**
**[ساخت لوگو با متن:]**
❖.logo (text)
❖.logo2 (text)
**[ساخت لوگو با متن و انتخاب طرح:]**
❖.lg (text) (mode)
**mode= 1-138**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Gif Maker** - **گیف ساز**
**[ساخت گیف با متن:]**
❖.gif (text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help8="""
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Photo&Gif]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Random Photo** - **عکس رندوم**
**[عکس حیوانات با ارسال دستورات زیر:]**
❖.pikachu
❖.whale
❖.foxx
❖.doggg
❖.rpanda
❖.raccoon
❖.panda
❖.koala
❖.kangroo
❖.fox
❖.dogg
❖.birdd
❖.catt
❖.anime
❖.bird
❖.dog
❖.cat
❖.robo 1-999999
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[دریافت عکس +18]**
❖.couple
❖.ayang
❖.boob
❖.nude
❖.nude2
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Random Gif** - **گیف رندوم**
**[گیف های مختلف با ارسال دستورات زیر:]**
❖.palm
❖.wink
❖.hug
.pat
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Search Photo** - **جستجو عکس**
**[جستجو عکس با متن:]**
❖.pic (text)
❖.bing (text)
❖.uns (text)
❖.photo (text)
❖.photos (text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Search Gif** - **جستجو گیف**
**[جستجو گیف با متن:]**
❖.gif (text)
❖.giff (text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Make colour** - **ساخت رنگ**
**[ساختن رنگ با ارسال اسم انگلیسی رنگ:]**
❖.color(name)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[دریافت فال یا استخاره]**
❖.fal
❖.estekhare
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help9 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Music & Sticker]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Music Finder** - **جستجو موسیقی**
**[جستجو موسیقی با ارسال دستورات زیر:]**
❖.music (text)
❖.youtube (text)
❖.musicc (text)
❖.remix (text)
❖.demo (text)
❖.classic (text)
❖.ahang (text)
❖.melo (text)
❖.global (text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Sticker** - **جستجو استیکر**

**[جستجو استیکر با ارسال متن:]**
❖.sticker (text)

**[دریافت اطلاعات استیکر با ریپلای روی استیکر]**
❖.stickerinfo

**[جستجو ارور استیکر با ارسال کد ارور:]**

❖.error (number) 
e.g= .error 501

**[کوچک کردن عکس/استیکر با ریپلای روی عکس/استیکر]**
❖.tiny reply

**[تبدیل عکس به استیکر به صورت پک شخصی]**
یا
**[دزدیدن استیکر دیگران]**
❖.steal reply
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
⭐️تبدیل عکس به استیکر:
تکی
.ts
⭐️تبدیل استیکر/عکس/گیف به عکس:
تکی
.tp
⭐️تبدیل استیکر/عکس به گیف:
.tg
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help10 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Market]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Market Price** - **قیمت در بازار**
**[دریافت قیمت اجناس در بازار ایران با متن:]**
#باسلام
❖.price (text)
#ترب
❖.qeymat (text)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Crypto Price** - **قیمت در بازار ارز دیجیتال**
**[دریافت نماد ارزهای دیجیتال:]**
❖.cryptolist #get name
**[دریافت قیمت ارز با استفاده از نماد:]**
❖.crypto (name)
**[دریافت قیمت ترون:]**
❖.trx
**[دریافت لیست قیمت ارزها:]**
❖ارز
**[تبدیل قیمت ارها به یکدیگر:]**
❖ `.c (num) (currency1) (currency2)`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[استعلام اطلاعات کارت بانکی با شماره کارت:]**
.estelam CardNum
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[استعلام اطلاعات تراکنش انتقال ارز دیجیتال با لینک تراکنش]**
.tara transactionLINK
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help11 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Downloader]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Instagram** - **اینستاگرام**
**[دریافت اطلاعات اکانت اینستاگرام:]**
❖.iginfo (username)
**[دانلود از اینستاگرام با لینک:]**
❖.igdl (link)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**YOUTUBE & FACEBOOK & INSTAGRAM & TIKTOK**
.down (link)
.down2 (link)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Apk & App** - **جستجو اپلیکیشن**
**[جستجو اپلیکیشن با دستورات زیر:]**
❖.app (name)
❖.apk (name)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**YOUTUBE SEARCH & DOWNLOAD**
**[دریافت فیلم از یوتیوب با ارسال متن:]**
❖ `.ym (text)`
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help12 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Uploader Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Text** - **آپلود متن**
**[آپلود متن و دریافت لینک آن:]**
.neko (reply)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**Media** - **آپلود عکس/متن**
**[آپلود عکس و دریافت لینک آن:]**
.telegraph (reply)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help13 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Book Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[دریافت جوک:]**
❖ `.joke`
**[دریافت شعر:]**
❖ `.poem`
**[دریافت دانستنی:]**
❖ `.know`
**[دریافت نقل قول:]**
❖ `.quote`
**[دریافت عدد به متن:]**
❖ `.num` (number)
**[دریافت اطلاعات نلم:]**
❖ `.name` ⤳ (`نام`)
**[دریافت اوغات شهر:]**
❖ `اوغات` ⤳ (`شهر`)
**[دریافت بیو متن:]**
❖ `.bio`
**[دریافت خاطره:]**
❖ `.memo`
**[دریافت پ ن پ:]**
❖ `.pnp`
**[دریافت الکی:]**
❖ `.alaki`
**[دریافت حدیث:]**
❖ `.hadis`
**[دریافت داستان:]**
❖ `.dastan`
**[x]**
❖ `.flg
**[دریافت نام رندوم:]**
❖ `.rname``
**[دریافت ذکر:]**
❖ `.zekr`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[دریافت فال یا استخاره]**
❖.fal
❖.estekhare
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help14 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Text-Mode Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[ارسال متن به صورت پله ای با متن:]**
❖.lad (text)
**[بولد:]**
❖ `.bold` ⤳ (`Oᑎ ᴏʀ OFF`)
**[اسپویلر:]**
❖ `.spoiler` ⤳ (`Oᑎ ᴏʀ OFF`)
**[کج نویس:]**
❖ `.italic` ⤳ (`Oᑎ ᴏʀ OFF`)
**[کپی:]**
❖ `.code` ⤳ (`Oᑎ ᴏʀ OFF`)
**[زیر خط:]**
❖ `.underline` ⤳ (`Oᑎ ᴏʀ OFF`)
**[خط خوردگی:]**
❖ `.strike` ⤳ (`Oᑎ ᴏʀ OFF`)
**[شکلک:]**
❖ `.emoji` ⤳ (`Oᑎ ᴏʀ OFF`)
**[منشن:]**
❖ `.quote` ⤳ (`Oᑎ ᴏʀ OFF`)
**[نقل قول]**
❖ `.mention` ⤳ (`Oᑎ ᴏʀ OFF`)
**[ری اکشن:]**
❖ .setreact❤️ reply
❖ .delreact reply
❖ .reactlist
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help15 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Action-Mode Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[آنتی لاگین:]**
❖ `.antilog` ⤳ (`Oᑎ ᴏʀ OFF`)

**[در حال نوشتن:]**
❖ `.typing` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال بازی کردن:]**
❖ `.playing` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال ضبط ویدیو:]**
❖ `.record_vid` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال انتخاب استیکر:]**
❖ `.choose_sticker` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال آپلود ویدیو:]**
❖ `.upload_vid` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال آپلود فایل:]**
❖ `.upload_doc` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال آپلود فایل موسیقی:]**
❖ `.upload_audio` ⤳ (`Oᑎ ᴏʀ OFF`)
**[در حال صحبت کردن:]**
❖ `.speaking` ⤳ (`Oᑎ ᴏʀ OFF`)
**[آنلاین:]**
❖ `.online`
**[آفلاین:]**
❖ `.offline`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help16 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Account Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**admin limit** = 2
**[افزودن ادمین سلف:]**
❖ `.addadmin` reply/user id / @username
**[حذف ادمین سلف:]**
❖ `.deladmin` reply/user id / @username
**[دریافت لیست ادمین:]**
❖ `.adminlist`
**[پاکسازی لیست ادمین:]**
❖ `.clearadminlist`
**[دریافت ایدی عددی اکانت سلف:]**
❖ `id`
**[:دریافت وضعیت سلف]**
❖ `self`
**[تنظیم حالت آفلاینی با متن :]**
❖ `.afk (reason)`
**[غیر فعال کردن حالت آفلاینی:]**
❖ `.unafk`
**[دریافت وضعیت محدودیت اکانت سلف:]**
❖ `.limit`
**[دریافت تاریخ ساخت اکانت سلف:]**
❖ `.creation`
**[دریافت اطلاعات سشن اکانت سلف:]**
❖ `.session`
**[راه اندازی مجدد سلف:]**
❖ `.restart`
**[غیر فعال کردن اضطراری سلف:]**
❖ `.shutdown`
**[دریافت پینگ سلف:]**
❖ `.ping` ⤳ (`ꜱᴛᴀᴛᴜꜱ`)
**[دریافت وضعیت سلف:]**
❖ `.on_off_status` ⤳ (`ꜱᴛᴀᴛᴜꜱ`)
**[دریافت اطلاعات cpu :]**
❖ `.cpu`
**[دریافت اطلاعات مموری:]**
❖ `.memory`
**[دریافت اطلاعات سیستم:]**
❖ `.system-inf` 
**[دریافت اطلاعات اکانت گیت هاب با ارسال یوزرنیم:]**
❖ `.github (username)`
**[دریافت اطلاعات پروژه گیتهاب با نام:]**
❖ `.git (project name)`
**[دریافت اطلاعات اکانت دیگران با ریپلای کردن یا نوشتن لیدی عددی:]**
❖ `.id (reply)`
**[دریافت زمان ساخت اکانت دیگران با ریپلای کردن:]**
❖ `.i (id number)`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[دریافت هشدارتگ شدن :]**
.tagalert on/off
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[کپی کردن پروفایل یک کاربر:]**
❖ `.clone` ⤳ (`ᴄʟOᑎᴇ ᴜsᴇʀ`)
**[بلاک یا آنبلاک کردن کاربر:]**
❖ `.block` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
❖ `.unblock` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
**[عضویت در گروه یا کانال:]**
❖ `.join` ⤳ (`ᴄʜᴀᴛ ᴜsᴇʀɴᴀᴍᴇ`)
**[ساخت کانال یا گروه:]**
❖ `.creatchannel` ⤳ (`ɴᴀᴍᴇ`)
❖ `.creatsupergroup` ⤳ (`ɴᴀᴍᴇ`)
❖ `.creatgroup` ⤳ (`ɴᴀᴍᴇ`)
**[منشن کردن یک کاربر:]**
❖ `.mention` ⤳ (`ʀᴇᴘʟʏ ᴏʀ ᴜsᴇʀɴᴀᴍᴇ`)
**[دریافت اطلاعات کامل یک پیام:]**
❖ `.get_message` ⤳ (`ʀᴇᴘʟʏ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help17 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Account Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
    **Tools** - **ابزار**
**[استخراج فایل با ریپلای:]**
❖ `.unzip` (reply zip)
**[تغییر نام فایل با ریپلای و نوشتن نام با فرمت:]**
❖ `.rname` (reply file)(write format)
**[چکر شماره مجازی:]**
❖ `.check`(+phone_number)
**[ارسال اسکرین شات از متن با ریپلای:]**
❖ `.q`(reply to user)
**[ارسال اسکرین شات از متن با نوشتن پیام دلخواه:]**
❖ `.qq`(text)
**[دریافت نتایج بازی های cricket:]**
❖ `.cricket`
**[دریافت وضعیت آب و هوا با ارسال نام شهر:]**
❖ `.weather` (city name)
**[دریافت زمان اذان با ارسال نام شهر:]**
❖ `.azan`(city name)
**[تبدیل کننده مقدار دما:]**
❖ `.t` (number) c
❖ `.t` (number) f
**[تبدیل کننده مقدار ارز دیجیتال:]**
❖ `.c` (number) (currency1) (currency2) eg: .c 100 usdt eur
**[ماشین حساب ریاضی:]**
❖ `.e`(math) eg: .e 2+2
**[دریافت آیپی سایت:]**
❖ `.ip` ⤳ (`ɢᴇᴛ ꜱɪᴛᴇ ɪᴘ`)
**[دریافت اطلاعات آیپی:]**
❖ `whoisip` ⤳ (`ɪᴘ ɪɴꜰᴏ`)
**[کوتاه کننده لینک:]**
❖ `.link` ⤳ (`https://ᴜʀʟ.ᴄᴏᴍ`) 
**[کوتاه کننده لینک:]**
❖ `.link2` ⤳ (`ᴡᴡᴡ.ᴜʀʟ.ᴄᴏᴍ`) 
**[دریافت پینگ سایت:]**
❖ `.p` ⤳ (`pornhub.com`)
**[اسکرین شات از سایت:]**
❖ `.screenshot2` ⤳ (`ᴡᴡᴡ.ᴜʀʟ.ᴄᴏᴍ`) 
**[اسکرین شات از سایت:]**
❖ `.screenshot` ⤳ (`ᴜʀʟ.ᴄᴏᴍ`) 
❖ `.screenshot4` ⤳ (`ᴡᴡᴡ.ᴜʀʟ.ᴄᴏᴍ`) 
**[اسکرین شات از سایت:]**
❖ `.screenshot3` ⤳ (`ᴜʀʟ.ᴄᴏᴍ`) 
**[اسکرین شات از سایت:]**
❖ `.shot` ⤳ (`ᴜʀʟ.ᴄᴏᴍ`) 
**[دریافت اطلاعات اکانت گیتهاب:]**
❖ `.github` (username)
**[دریافت اطلاعات پروژه با متن:]**
❖ `.git` (text)
**[جستجو در دیکشنری:]**
❖ `.dict` (word)
**[دریافت سال ساخت اکانت با ایدی عددی:]**
❖ `.ci` (id number)
**[دریافت سال ساخت اکانت سلف:]**
❖ `.creation`
**[دریافت وضعیت محدودیت اکانت سلف:]**
❖ `.limit`
**[دریافت اطلاعات کشور با نام:]**
❖ `.country` (name)
**[تبدیل به عکس با ریپلای:]**
❖ `.tp` ⤳ (`ꜱᴛɪᴄᴋᴇʀ ᴛᴏ ᴘɪᴄ`) 
**[تبدیل به استیک با ریپلای:]**
❖ `.ts` ⤳ (`ᴘɪᴄ ᴛᴏ ꜱᴛɪᴄᴋᴇʀ`)
**[تبدیل به گیف با ریپلای:]**
❖ `.tg` ⤳ (`ꜱᴛɪᴄᴋᴇʀ ᴛᴏ ɢɪꜰ`)
**[ترجمه به فارسی به ارسال متن:]**
❖ `.fa` ⤳ (`text`)
**[ترجمه به انگلیسی با ارسال متن:]**
❖ `.en` ⤳ (`text`)
**[دریافت فیلم با ارسال نام:]**
❖ `.movie` ⤳ (`text`)
**[دریافت انیمه با ارسال نام:]**
❖ `.anim` ⤳ (`text`)
**[:ساخت رمز با ارسال تعداد رقم]**
❖ `.pass` ⤳ (`number`)
**[مورس کردن متن:]**
❖ `.morset` ⤳ (`text`)
**[آن مورس کردن متن:]**
❖ `.unmorset` ⤳ (`code`)
❖ `مورس` ⤳ (`متن)
❖ `.انمورس` ⤳ (`کد`)
**[دریافت تاریخ:]**
❖ `.date`
**[دریافت ایدی اکانت سلف:]**
❖ `id`
**[دریافت اطلاعات اکانت دگران با ریپلای یا ارسال ایدی عددی:]**
❖ `.id` ⤳ (`username or reply` )
**[بررسی صحت کد ملی:]**
❖ `.meli` ⤳ (`code meli` )
**[دریافت اخبار روز:]**
❖ `.news` ⤳ (`category `)
category = business,entertainment,general,health,science,sports,technology
**[دریافت کردیت کارت با نمونه بین:]**
❖ `.ccgen` ⤳ (`bin`)
**[دریافت اخبار روز:]**
❖ `.yjc`
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **OCR** - **جستجو متن در تصویر**
❖ `.ocr` 
⤳ `ʀᴇᴘʟʏ` 
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Timer Pic** - **دانلود رسانه زماندار**
❖ `.dl` ⤳ (`ꜱᴇɴᴅ ᴛᴏ ᴍ.ᴄʜᴀᴛ`)
❖ `waitt` ⤳ (`ꜱᴇɴᴅ ᴛᴏ ꜱᴀᴠᴇᴅ ᴍᴇꜱꜱᴀɢᴇ`)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
  **Time** - **ساعت**
❖ `.time` 
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
  **Code Runner** - **اجراکننده کد**
__به بخش اجرای کد های برنامه نویسی خوش آمدید__
❖ `.py` 
❖ `.js` 
❖ `.php` 
❖ `.kotlin` 
❖ `.go` 
❖ `.java` 
❖ `.lua` 
  **Code ScreenShot**
❖ `.carbon`
⤳ `ʀᴇᴘʟʏ` 
❖ `.exec` (execute code)
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
help18 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Webhook Menu]**

**تنظیم وبهوک با توکن و لینک:**
.setwebhook token link
link= https://domain.com/x/y

**حذف وبهوک با توکن:**
.del webhook token
**حذف آپدیت با توکن:**
.delupdate token
**دریافت اطلاعات وبهوک با توکن:**
.webhookinfo token
**دریافت اطلاعات ربات با توکن:**
.botinfo token
▬▬▬▬▬▬▬▬▬▬▬▬▬▬

"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help19 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[CronJob Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**تنظیم کرون جاب با لینک و زمان:**
.cron link time
time = 1 ,2,5,10,15,30,60,... minutes
link= https://domain.com/x/y
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help20 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[LOCKS Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Pv lock MODE** - **حالت قفل پیوی**
**[قفل پیوی:]**
.pvlock on/off
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
   **Pv lock MODE** - **حالت قفل گروه**
**[دریافت لیست نام قفل ها:]**
.hlock
**[دریافت وضعیت قفل گروه:]**
.locks
**[قفل یا بازکردن با استفاده از نام قفل:]**
.lock ....
.unlock ....
**[قفل یا بازکردن همه قفل ها:]**
.lock all
.unlock all
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
help21 = """
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
**[Tabchi Menu]**
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
✔️اضافه شدن ارسال زماندار (با قابلیت تنظیم بنر)
❤️دریافت وضعیت تبچی
دستور:
.tabchi status
✔️ارسال زماندار در تمام گروه ها
دستور:
.tabchigp on/off
✔️تنظیم بنر ارسال زماندار در گروه ها
دستور:
.setbannergp reply/text/متن
❤️ارسال زماندار در پیوی ها
دستور:
.tabchipv on
❤️تنظیم بنر ارسال زماندار در پیوی ها
دستور:
.setbannerpv reply/text/متن
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
✔️اضافه شدن سندر پیوی اعضا گروه مد نظر

✔️تنظیم بنر ارسال در پیوی اعضا گروه
دستور:
.setbannersender reply/text/متن
❤️ارسال در پیوی اعضا گروه با ایدی گروه
دستور:
.sendall @group
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
👀اضافه شدن قابلیت تنظیم زمان ارسال زماندار⚡️
‼️توجه: واحد زمان ثانیه میباشد و حداقل زمان ۳۰۰ ثانیه می باشد.
🔽تنظیم زمان در حالت ارسال زماندار به گروه ها:
.settimergp time
🔼مثال :
 .settimergp 301
🔽تنظیم زمان در حالت ارسال زماندار به پیوی ها:
.settimerpv time
🔼مثال :
 .settimerpv 301
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
🤍 با ریپلای کردن دستور روی پیام یا نوشتن پیام جلوی دستور

#ارسال همگانی به گروه ها
دستور:
.sendgp
#ارسال همگانی به پیوی ها
دستور:
.sendpv
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
#اد گروه به گروه 💨
دستور:
ارسال دستور در گروه مقصد
.addall @یوزرنیم گروه مبدا

#اد کاربر به گروه
دستور:
ارسال دستور در گروه مقصد
.adduser @یوزرنیم کاربر

#دریافت لینک گروه
دستور:
ارسال دستور در گروه
.invitelink
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
✅پیوستن به گروه با لینک:
.join link
✅خروج از گروه با لینک:
.leave link
✅خروج از همه گروه ها:
.leaveallgc
✅خروج از همه کانال ها:
.leaveallch
▬▬▬▬▬▬▬▬▬▬▬▬▬▬
"""
mark = InlineKeyboardMarkup(
     [
         [
             InlineKeyboardButton('🌐𝐺𝑙𝑜𝑏𝑎𝑙 & 𝑃𝑒𝑟𝑠𝑜𝑛𝑎𝑙🦁',callback_data='a')
         ],
         [
             InlineKeyboardButton('𝐴𝑟𝑡𝑖𝑓𝑖𝑐𝑖𝑎𝑙 𝐼𝑛𝑡𝑒𝑙𝑙𝑖𝑔𝑒𝑛𝑐𝑒💭',callback_data='b')
         ],
         [
             InlineKeyboardButton('𝑃𝑟𝑜𝑓𝑖𝑙𝑒🎭',callback_data='c')
         ],
         [
             InlineKeyboardButton('🎮𝐹𝑢𝑛🕹',callback_data='d'),
             InlineKeyboardButton('🤖𝑻𝒂𝒃𝒄𝒉𝒊👾',callback_data='t')
         ],
         [
             InlineKeyboardButton('𝑾𝒆𝒃𝒉𝒐𝒐𝒌🔑',callback_data='q'),
             InlineKeyboardButton('𝑳𝒐𝒄𝒌𝒔🔏',callback_data='s'),
             InlineKeyboardButton('𝑪𝒓𝒐𝒏-𝑱𝒐𝒃🕰',callback_data='r')
         ],
         [
             InlineKeyboardButton('🎨𝑃ℎ𝑜𝑡𝑜 𝐸𝑑𝑖𝑡𝑜𝑟👨‍🎤',callback_data='e'),
             InlineKeyboardButton('🖼𝐿 & 𝐺 𝑀𝑎𝑘𝑒𝑟🧑‍🎤',callback_data='f')
         ],
         [
             InlineKeyboardButton('📸𝑃ℎ𝑜𝑡𝑜 & 𝐺𝑖𝑓📺',callback_data='g'),
             InlineKeyboardButton('🎧𝑀𝑢𝑠𝑖𝑐 & 𝑆𝑡𝑖𝑐𝑘𝑒𝑟🌠',callback_data='h')
         ],
         [
            InlineKeyboardButton('💸𝑀𝑎𝑟𝑘𝑒𝑡🤑',callback_data='i'),
            InlineKeyboardButton('𝐷𝑜𝑤𝑛𝑙𝑜𝑎𝑑𝑒𝑟⬇️',callback_data='j'),
            InlineKeyboardButton('𝑈𝑝𝑙𝑜𝑎𝑑𝑒𝑟⬆️',callback_data='k')
         ],
         [
            InlineKeyboardButton('📖𝐵𝑜𝑜𝑘📚',callback_data='l'),
            InlineKeyboardButton('📝𝑇𝑒𝑥𝑡 𝑀𝑜𝑑𝑒💬',callback_data='m'),
            InlineKeyboardButton('𝐴𝑐𝑡𝑖𝑜𝑛 𝑀𝑜𝑑𝑒💢',callback_data='n')
         ],
         [
             InlineKeyboardButton('𝐴𝑐𝑐𝑜𝑢𝑛𝑡☑️',callback_data='o')
         ],
                  [
             InlineKeyboardButton('𝑇𝑜𝑜𝑙𝑠⚙',callback_data='p')
         ],
         [
             InlineKeyboardButton('𝑪𝒍𝒐𝒔𝒆',callback_data='close')
         ]
     ]
)

dast = InlineKeyboardMarkup(
     [
         [
             InlineKeyboardButton("ᗷᗩᑕƘ", callback_data='back')
         ]
     ]
)

openpanelbot = InlineKeyboardMarkup(
     [
         [
             InlineKeyboardButton("Panel", switch_inline_query_current_chat='panel')
         ]
     ]
)

keyboard_idk = ReplyKeyboardMarkup(
     [
         [
             ("Add User"),
             ("Delete User"),
             ("User List")
         ],
         [
             ("Add Owner"),
             ("Delete Owner"),
             ("Owner List")
         ]
     ],
one_time_keyboard=True,resize_keyboard=True)

my_users = [1485504074,6564427484,1234] # ایدی عددی مالک 
users = filters.user(my_users) 

my_owners = [1485504074,6564427484] # ایدی عددی مالک 
owners = filters.user(my_owners) 

@app.on_inline_query()
def answer(client, inline_query):
    if inline_query.from_user.id in my_users:
      inline_query.answer(
          results=[
              InlineQueryResultArticle(
                  title="Helper",
                  input_message_content=InputTextMessageContent(
                      f"__Hello {inline_query.from_user.first_name}\n Welcome To Jack_selfBot"
                  ),
                  description="Jack_self Self Panel",
                  reply_markup=mark
              )
          ],
          cache_time=1
      )

@app.on_callback_query(users)
async def test(app, call): 
    if call.data == "back":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=f"▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬\nUser: `{call.from_user.first_name}`\n**Main Menu**\n▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬", reply_markup=mark)
                  
    elif call.data == "a":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help1 , reply_markup=dast)
         
    elif call.data == "b":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help2 , reply_markup=dast)
         
    elif call.data == "c":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help3 , reply_markup=dast)
       
    elif call.data == "d":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help5 , reply_markup=dast)
         
    elif call.data == "e":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help6 , reply_markup=dast)
         
    elif call.data == "f":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help7 , reply_markup=dast)
         
    elif call.data == "g":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help8 , reply_markup=dast)
         
    elif call.data == "h":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help9 , reply_markup=dast)
         
    elif call.data == "i":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help10 , reply_markup=dast)
       
    elif call.data == "j":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help11 , reply_markup=dast)
         
    elif call.data == "k":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help12 , reply_markup=dast)
         
    elif call.data == "l":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help13 , reply_markup=dast)
    elif call.data == "m":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help14 , reply_markup=dast)
         
    elif call.data == "n":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help15 , reply_markup=dast)
         
    elif call.data == "o":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help16 , reply_markup=dast)
         
    elif call.data == "p":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help17 , reply_markup=dast)
         
    elif call.data == "q":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help18 , reply_markup=dast)
         
    elif call.data == "r":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help19 , reply_markup=dast)
         
    elif call.data == "s":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help20 , reply_markup=dast)
    elif call.data == "t":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text=help21 , reply_markup=dast)

    elif call.data == "close":
         await app.edit_inline_text(inline_message_id=call.inline_message_id, text="**Closed!**")
  
@app.on_callback_query(~users)
def test(app, call): 
  call.answer("Jack Self", show_alert=True)
    
@app.on_message(filters.private&owners&filters.command("panel"), group=-1)
async def updates(app, m:Message):
     await app.send_message(m.chat.id, "**Jack Owner**", reply_markup=keyboard_idk)
    
@app.on_message(filters.private&users&filters.command("start"), group=-1)
async def updates(app, m:Message):
     kos = f"@{m.from_user.username}" if m.from_user.username else m.from_user.id
     await app.send_message(m.chat.id, f"**Hello {m.from_user.first_name}**\n__Welcome to bot__\nFor get Panel type [ `.help` ]\n     ", reply_markup=openpanelbot)
     await app.send_message(my_owners[0], f"✅ User {kos} Started The Bot ✅")

@app.on_message(filters.private&~users&filters.command("start"), group=-1)
async def updates(app, m:Message):
     await m.delete()
     
   #______________________________Owner Panel________________________
@app.on_message(filters.private&owners)
async def updates(app, m:Message):
 text = m.text
 if text == "Add User":
   try:
     answer = await app.ask(m.chat.id, '**Send Me User ID**:')
     my_users.append(int(answer.text))
     users.add(int(answer.text))
     await app.send_message(m.chat.id, f"Successfull\nUser [ `{answer.text}` ] Added to User List")
   except Exception as er:
     await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")

 elif text == "Delete User":
   answer = await app.ask(m.chat.id, '**Send Me User ID**:')
   if int(answer.text) in users:
     try:
       num = my_users.index(int(answer.text))
       my_users.remove(my_users[num])
       users.remove(int(answer.text))
       await app.send_message(m.chat.id, f"Successfull\nUser [ `{answer.text}` ] Deleted From User List")
     except Exception as er:
       await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")
   else:
     await app.send_message(m.chat.id, f"This is Not in Users List")
             
 elif text == "User List":
   try:
     s = ""
     op = 1
     if len(my_users) >= 1:
       for i in range(0,int(len(my_users))):
         s += f"֍ {op} -> `{my_users[i]}`\n"
         op += 1
       await app.send_message(m.chat.id, f"**User List:**\n{s}")
     else:
       await app.send_message(m.chat.id, f"**User List is Empty**")
   except Exception as er:
     await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")
   
 elif text == "Add Owner":
   answer = await app.ask(m.chat.id, '**Send Me User ID**:')
   try:
     if int(answer.text) in my_users:
       my_owners.append(int(answer.text))
       owners.add(int(answer.text))
       await app.send_message(m.chat.id, f"Successfull\nUser [ `{answer.text}` ] Added to Owner List")
     else:
       await app.send_message(m.chat.id, f"این یتیم حتی یوزر هم نیست داش 😐\nاول به یوزرا اضافش کن بعد بیا مالکش کن")
   except Exception as er:
     await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")
       
 elif text == "Delete Owner":
   answer = await app.ask(m.chat.id, '**Send Me User ID**:')
   if int(answer.text) in my_users:
     try:
       num = my_owners.index(int(answer.text))
       my_owners.remove(my_owners[num])
       owners.remove(int(answer.text))
       await app.send_message(m.chat.id, f"Successfull\nUser [ `{answer.text}` ] Deleted From Owner List")
     except Exception as er:
       await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")
   else:
     await app.send_message(m.chat.id, f"This is Not in Owners List")

 elif text == "Owner List":
   try:
     s = ""
     op = 1
     if len(my_owners) >= 1:
       for i in range(0,int(len(my_owners))):
         s += f"֍ {op} -> `{my_owners[i]}`\n"
         op += 1
       await app.send_message(m.chat.id, f"**Owner List:**\n{s}")
     else:
       await app.send_message(m.chat.id, f"**Owner List is Empty**")
   except Exception as er:
     await app.send_message(m.chat.id , f"❋ `ERROR` ⤳\n(`{er}`)")


app.start(), print("started..."), idle(), app.stop()