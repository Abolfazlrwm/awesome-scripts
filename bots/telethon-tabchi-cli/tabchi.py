# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

#============================================
#اراعه شده توسط سورس کده 🗣
#اصکی نرو ناموصتو حفظ کن !
#@Sourrce_kade
#============================================
#*/

#مشکلی در ران داشتید به سازنده پیام بدید @Mrchronicle

#-----------------------[TABCHI-START]-------------
#=========================
#-> --> --->[ Tabchi  ^•^   ] <--- <-- <-
#* Robot  : Tabchi => Cli                           
#* Author : @MrChronicle           
#* Version : 2.0  => Vip <=         
#* Library : Telethon        
#* Date : 2024-12-20        
#\\\\\\\\\\\\\\\\\\\\\\\\\\\\\       #========================
#-----------------------[Start->Imports]-----------------
import asyncio
import random
import re
import sys
import mimetypes
import os
import psutil
from telethon import TelegramClient, events, functions, types
from telethon.errors import (
    UsernameOccupiedError, UsernameInvalidError, InviteHashInvalidError, 
    InviteHashExpiredError, FloodWaitError
)
from telethon.tl.functions.account import (
    UpdateProfileRequest, UpdateUsernameRequest
)
from telethon.tl.functions.messages import (
    GetDiscussionMessageRequest, SendInlineBotResultRequest, 
    GetInlineBotResultsRequest, ImportChatInviteRequest
)
from telethon.tl.functions.photos import (
    UploadProfilePhotoRequest, DeletePhotosRequest
)
from telethon.tl.types import InputPhoto
from datetime import datetime, timedelta
import time
from telethon.tl.functions.channels import (
    LeaveChannelRequest, JoinChannelRequest
)
from subprocess import run, PIPE
from telethon import events
from telethon.tl.functions.channels import JoinChannelRequest
from telethon.errors.rpcerrorlist import InviteHashInvalidError, InviteHashExpiredError, FloodWaitError
from telethon.tl.functions.messages import GetRepliesRequest

from telethon import errors
#-----------------------[EnD->Imports]-----------------
#---------[Start->Apis->Client->Settings]--------
Client_name = ''
API_ID = ''
API_HASH = ''
admin_list = []
client = TelegramClient(Client_name,API_ID,API_HASH,device_model='iPhone 16 Pro Max',system_version='iOS 18',app_version='9.0.1',system_lang_code='en',lang_code='en'
)
#---------[EnD->Apis->Client->Settings]--------
#---------------------[Start->Variables]----------------
blocklogin_enabled = False
auto_reply_enabled = False
Alibot_enabled = False  
Ali_timer = 0 
auto_reply_messages = []
post_timer = 10
commentbot_enabled = False
join_queue = []
joined_channels = set()
failed_channels = []
current_channel = None
auto_gapchat_enabled = False
group_message_count = {}
user_last_reply_time = {}
group_last_chat_time = {}
reaction_enabled = False
message_counter = 0
last_reaction_time = 0
reqcom_enabled = False
reqcom_count = 0
gap_status = False
gap_message = None
gap_time = 900  
current_auto_mode = None
Channel_list = []
auto_join_enabled = False
#---------------------[End->Variables]------------------
#---------------------[Start->Auto Join]----------------
@client.on(events.NewMessage(pattern='/auto_join (on|off)|/addlistjn'))
async def handler(event):
    if event.sender_id in admin_list:
        if event.pattern_match.group(0).startswith('/auto_join'):
            global auto_join_enabled
            auto_join_enabled = event.pattern_match.group(1) == 'on'
            await event.reply(f'عضویت | لفت خودکار {"روشن شد ✅" if auto_join_enabled else "خاموش شد ❌"}')
            if auto_join_enabled:
                client.loop.create_task(auto_join_channels())
        elif event.pattern_match.group(0) == '/addlistjn' and event.is_reply:
            reply_message = await event.get_reply_message()
            links = extract_links(reply_message.message)
            Channel_list.extend(links)
            await event.reply(f'لینک‌ها به لیست اضافه شدند: {links}')
            
def extract_links(text):
    import re
    return re.findall(r'(https?://[^\s]+)', text)

async def auto_join_channels():
    while auto_join_enabled:
        for _ in range(4):
            if not Channel_list:
                break
            link = Channel_list.pop(0)
            try:
                invite_hash = link.split('/')[-1]
                await client(JoinChannelRequest(invite_hash))
            except (InviteHashInvalidError, InviteHashExpiredError, FloodWaitError, ValueError):
                continue
        await asyncio.sleep(3600)
#---------------------[End->Auto Join]-----------------
#----------------[Start->Auto Chat->List]-----------
chat = ["سلام", "خوبید", "کسی بلده چطوری پرفایلم خصوصی کنم فامیل نبینه", "چطوری رل میزنید 😂", "کسی گروه داره بده پی", "فعلا", "هعیی", "هعیی خدا", "از هرچی ادم بدم میاد", "بای پای", "👀","برم رل بزنم🚶‍♀🚶‍♀🚶‍♀🚶‍♀🚶‍♀🚶‍♀🚶‍♀","شایعات در مورد امیرا حقیقت داره ؟ 🤔","خب دوستان خوشحال شدیم فعلا خداحافظ","خوبین؟","لینک گذاشتین لینکدونی الان کلی ربات میاد","نسبتا این گروه بهتر از گروه های دیگست بقیه داغانن 😂""چطورین؟","داستان چیه ؟ میاین پیوی میگین بیا رل بزنیم 😐😂","پولامون هم تمام شد دییگه باید از پدر گرامی پول بگیریم دوست پسرم که نداریم 😐😂","سلمونی نرو با ما بد باش فقط ...","سلمونی نرو با ما بد باش فقط ...","اصلا حالم خوش نیست","پسرا هم جدیدا یه جور قیافه میگیرن انگار پسر پادشاهن‌ بقول آیسان‌ پسر شاه باشه 😂","یه پولی هم جور نمیشه مهاجرت کنیم بریم 😂💔","جدیدا دیگه همه تلگرام پرمیوم دارن فقط ما نداریم 😂🚶‍♀","گروه خوبیه","❤️ عزیزم شمارمو سیو کن بیا پیو‌ی حرف بزنیم 😁","منم ادمین کنید فعالم موقع عضوگیری کمکتون میکنم 🚶‍♀"]

list1_messages = ["😐", "خیر", "نه", "من کجیم باته", "ادمین من رباتم؟"]
list2_messages = ["گزارش", "چرا ریپ میزنی", "بیشعور"]
list3_messages = ["خوبی", "👋", "های"]
list4_messages = ["چطوری ؟ ","نه راستش","چی بگم هعیی","نه والا","مرسی ممنون عزیزم","خودت چطوری"]
list5_messages = ["خوشم نمیاد","متنفرم","پرنسس از المان","اول شما","دوست ندارم"]
list6_messages = ["نه"," نیستم به خدا","بار ها گفتم نیستم ","عجبا"]
#----------------[End->Auto Chat->List]----------
#------------------[Start->Auto->Reply]-------------
@client.on(events.NewMessage(from_users=admin_list, pattern=r'/autopv (on|off)|/addpv|/testpv|/restpv'))
async def handle_auto_reply_commands(event):
    global auto_reply_enabled
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
        messages = await client.get_messages(event.chat_id, limit=2)
        if len(messages) == 1:
            for reply_message in auto_reply_messages:
                await asyncio.sleep(3) 
                if reply_message.media:
                    await client.send_file(event.chat_id, reply_message.media)
                else:
                    await client.send_message(event.chat_id, reply_message.text)
#------------------[EnD->Auto->Reply]-------------
#------------------[Start->Auto->Chats]-------------
@client.on(events.NewMessage(from_users=admin_list, pattern=r'/autogapchat (on|off)'))
async def handle_auto_gapchat_commands(event):
    global auto_gapchat_enabled
    if event.sender_id is not None:
        action = event.pattern_match.group(1)
        auto_gapchat_enabled = (action == 'on')
        message = 'چت خودکار گپ فعال شد✅' if auto_gapchat_enabled else 'چت خودکار گپ غیرفعال شد❌'
        await event.reply(message)

@client.on(events.NewMessage(incoming=True))
async def handle_group_messages(event):
    global group_message_count, group_last_chat_time
    if auto_gapchat_enabled and event.is_group:
        chat_id = event.chat_id
        current_time = datetime.now()
        
        if chat_id in group_last_chat_time:
            last_chat_time = group_last_chat_time[chat_id]
            if current_time - last_chat_time < timedelta(seconds=1):
                return 
        
        if chat_id not in group_message_count:
            group_message_count[chat_id] = 0
        group_message_count[chat_id] += 1  
        if group_message_count[chat_id] >= 450: 
            random_message = random.choice(chat)
            if random_message: 
                try:
                    await client.send_message(chat_id, random_message)
                    group_last_chat_time[chat_id] = current_time 
                except telethon.errors.rpcerrorlist.UserBannedInChannelError:
                    await client(LeaveChannelRequest(chat_id))
                    print(f"Left the group {chat_id} due to ban.")
                except Exception as e:
                    print(f"Error sending message: {e}")
            group_message_count[chat_id] = 0 

@client.on(events.NewMessage(incoming=True))
async def handle_replies(event):
    user_id = event.sender_id
    current_time = datetime.now()
    
    if user_id in user_last_reply_time:
        last_reply_time = user_last_reply_time[user_id]
        if current_time - last_reply_time < timedelta(minutes=60):
            return 

    if event.is_reply:
        original_message = await event.get_reply_message()
        me = await client.get_me()
        if original_message.sender_id == me.id:
            message_text = event.message.message.lower()
            if any(keyword in message_text for keyword in ["رباتی", "ربات", "بات", "بات؟"]):
                random_reply = random.choice(list1_messages)
            elif any(keyword in message_text for keyword in ["گزارش", "ادمینی نیست"]):
                random_reply = random.choice(list2_messages)
            elif any(keyword in message_text for keyword in ["سلام", "صلم", "سلم", "صلام"]):
                random_reply = random.choice(list3_messages)
            elif any(keyword in message_text for keyword in ["خوبی", "خبی","سلام خوبی",]):
                random_reply = random.choice(list4_messages)
            elif any(keyword in message_text for keyword in ["اصل", "اصل بده","اسل بده","اسل بده"]):
                random_reply = random.choice(list5_messages)
            elif any(keyword in message_text for keyword in ["فیک", "پسری","پسر ؟","میدونستم پسری ","پسره بابا ","پسره"]):
                random_reply = random.choice(list6_messages)
            else:
                return 

            async with client.action(event.chat_id, 'typing'):
                await asyncio.sleep(3) 
            await event.reply(random_reply)
            user_last_reply_time[user_id] = current_time 

@client.on(events.NewMessage(incoming=True))
async def handle_mentions(event):
    user_id = event.sender_id
    current_time = datetime.now()
    
    if user_id in user_last_reply_time:
        last_reply_time = user_last_reply_time[user_id]
        if current_time - last_reply_time < timedelta(minutes=60):
            return 

    if event.mentioned:
        message_text = event.message.message.lower()
        if any(keyword in message_text for keyword in ["رباتی", "ربات", "بات", "بات؟"]):
            random_reply = random.choice(list1_messages)
        elif any(keyword in message_text for keyword in ["گزارش", "ادمینی نیست"]):
            random_reply = random.choice(list2_messages)
        elif any(keyword in message_text for keyword in ["سلام", "صلم", "سلم", "صلام"]):
            random_reply = random.choice(list3_messages)
        elif any(keyword in message_text for keyword in ["خوبی", "خبی","سلام خوبی",]):
            random_reply = random.choice(list4_messages)
        elif any(keyword in message_text for keyword in ["اصل", "اصل بده","اسل بده","اسل بده"]):
            random_reply = random.choice(list5_messages)
        elif any(keyword in message_text for keyword in ["فیک", "پسری","پسر ؟","میدونستم پسری ","پسره بابا ","پسره"]):
            random_reply = random.choice(list6_messages)
        else:
            return 

        async with client.action(event.chat_id, 'typing'):
            await asyncio.sleep(3) 
        await event.reply(random_reply)
        user_last_reply_time[user_id] = current_time 
#------------------[EnD->Auto->Chats]-------------
#-------------[Start->Auto->Reaction]---------------
@client.on(events.NewMessage(pattern='/req (on|off)'))
async def req_handler(event):
    if event.sender_id in admin_list:
        global reaction_enabled
        try:
            entity = await client.get_entity(event.sender_id)
            reaction_enabled = event.pattern_match.group(1) == 'on'
            status = 'فعال شد ✅' if reaction_enabled else 'غیر فعال شد ❌'
            await client.send_message(entity, f'ری‌اکشن خودکار گپ {status}')
        except ValueError as e:
            await client.send_message(event.sender_id, 'خطا 41')
@client.on(events.NewMessage)
async def auto_reaction(event):
    global message_counter, last_reaction_time
    if reaction_enabled and event.is_group:
        message_counter += 1
        current_time = time.time()
        
        if message_counter >= 500 and (current_time - last_reaction_time) > 3:
            try:
                await client(functions.messages.SendReactionRequest(
                    peer=await client.get_input_entity(event.chat_id),
                    msg_id=event.id,
                    reaction=[types.ReactionEmoji(emoticon='👍')]
                ))
                message_counter = 0 
                last_reaction_time = current_time
            except Exception as e:
                pass 
async def send_messages(client, message, target_type):
    async for dialog in client.iter_dialogs():
        if (target_type == 'user' and dialog.is_user and not dialog.entity.bot) or (target_type == 'group' and dialog.is_group):
            try:
                await client.send_message(dialog.entity.id, message)
                await asyncio.sleep(5)  
            except Exception as e:
                if "banned from sending messages in supergroups/channels" in str(e):
                    print(f'Skipping banned group: {dialog.entity.id}')
                    continue
                else:
                    print(f'56')
                    break
#-------------[EnD->Auto->Reaction]-------------
#-------------------[Start->Restart]---------------------
@client.on(events.NewMessage(pattern=r'/restart'))
async def handler(event):
   if event.sender_id in admin_list:
    await event.reply(f'Restart was successful')
    python = sys.executable
    os.execl(python, python, *sys.argv)
#-------------------[EnD->Restart]-------------------
#-------------------[Start->Sends]-------------------
@client.on(events.NewMessage(from_users=admin_list, pattern='/sendpvs|/sendgaps'))
async def send_handler(event):
    if event.is_reply:
        reply_message = await event.get_reply_message()
        message = reply_message.message
        target_type = 'user' if '/sendpvs' in event.raw_text else 'group'
        await send_messages(client, message, target_type)
        await event.reply(f'پیام به تمامی {"پیوی‌ها" if target_type == "user" else "گروه‌ها"} ارسال شد.')
#-------------------[EnD->Sends]-------------------
#-------------------[Start->Clears]-------------------

from telethon import errors

@client.on(events.NewMessage(from_users=admin_list, pattern=r'/killchannel'))
async def kill_channels(event):
    try:
        await event.reply('درحال برسی کانال .... لطفا تا حداکثر 5 دقیقه آینده هیچ دستوری به ربات ارسال نکنید!')
        channels_count = 0

        async for dialog in client.iter_dialogs():
            if dialog.is_channel:
                try:
                    full_channel = await client(functions.channels.GetFullChannelRequest(channel=dialog.entity))
                    linked_chat_id = full_channel.full_chat.linked_chat_id

                    if linked_chat_id:
                        try:
                            await client(functions.channels.GetFullChannelRequest(channel=linked_chat_id))
                        except errors.UserBannedInChannelError:
                            await client(functions.channels.LeaveChannelRequest(channel=dialog.entity))
                            channels_count += 1
                        except errors.ChannelPrivateError:
                            await client(functions.channels.LeaveChannelRequest(channel=dialog.entity))
                            channels_count += 1
                except Exception as e:
                    await event.reply(f'خطا در پردازش کانال {dialog.name}: {str(e)}')

        await event.reply(f'عملیات لفت با موفقیت انجام شد.\nتعداد کانال‌های بن شده: {channels_count}')
    except Exception as e:
    	print('خطا ۳۲')

#-------------------[End->Clears]-------------------
#------------[Start->Auto->comments]-----------
@client.on(events.NewMessage(pattern='/com (on|off)'))
async def commentbot_handler(event):
    if event.sender_id in admin_list:
        global commentbot_enabled
        commentbot_enabled = event.pattern_match.group(1) == 'on'
        await event.reply(f'کامنت‌گذاری خودکار {"فعال شد ✅" if commentbot_enabled else "غیرفعال شد ❌"}')
@client.on(events.NewMessage(pattern=r'/settcom (\d+)'))
async def setpostt_handler(event):
    if event.sender_id in admin_list:
        global post_timer
        post_timer = int(event.pattern_match.group(1))
        await event.reply(f'تایمر به {post_timer} ثانیه تغییر یافت.💢')      
@client.on(events.NewMessage)
async def auto_comment(event):
    if commentbot_enabled and event.is_channel:
        try:
            me = await client.get_me()
            if event.sender_id != me.id and event.is_channel and not event.is_group:
                await asyncio.sleep(post_timer)

                laugh_emojis = ['😂😂','😀😀😀','😁😁😁','🤣🤣','😅😅','🤪🤪','😜😜','😂🤪','🤣😅','😐😂','🥰😂','😅🤣','😂😊','🥰🥰🤣','😘😂','❤😂😅❤','🤣❤','😀❤']
                laugh_responses = ['خخخخخ', 'خیلی دلقک شدی', 'دلقک', 'فان شدی', 'همیشه اینقد فان بودی', 'برو سیرک ثبت نام کن', 'کسنمک', 'خنده دار بود', 'خیلی خندیدم', 'بازم بزار باحال بود', 'مردم از خنده', 'الان میخوای بخندم', 'کجاش خنده داشت', 'ادمین دلقک 😂', 'مسخره', 'خیلی مسخره بود 😂']

                sad_emojis = ['🥲🥲','😏😏','😒😏','😞😞','😔😞','😕😞','😖😖☹','😫😫😟','😭😭😭','😭😭😫😫','😓😢','🫠😭','🤬😭','😢🤬','😞😔','😞😞😞']
                sad_responses = ['غمگین شدم', 'خیلی ناراحتم', 'قلبم شکست', 'آخییییی', 'قلبممممم', 'هعیییییی', 'ناراحت شدم', 'چی بگم😢', 'وای قلبم 😭', 'خدایااا🥲🥲', 'نهههههه😭😭', 'چراااا🥲', 'شکست عشقی خوردم', 'خدا رحمتش کنه', 'خدا صبر بده', 'کیییی😢😢', 'چیشد 😭', 'زندگیم😭😭😭', 'عزیزم 😢☹', 'اذیتم کردی ☹', 'اصلا نموخام ☹', 'برو غمگینم']

                happy_emojis = ['💋💋', '👅👅', '👄👄', '🫦🫦🫦', '💄💋', '👅👄', '🫦💍', '🍑🍑', '🍑👄', '💄🍑', '👄🥰', '👩‍❤‍💋‍👩👄']
                happy_responses = ['خجالت داره', 'این چیه اخه', 'زشته', 'خجالت کشیدم', 'شرم کو حیا کو', 'سالمه', 'اعتراف کن بهش', 'همیشه اینقد نظر میدین', 'اوووف', 'خیلی حق شده', 'چقد سکسیه', 'خیلی خوبه', 'میخوامش']
                angry_emojis = ['⛹‍♀⛹‍♀', '🧘‍♀🧘‍♀', '🏄‍♀🏄‍♀🏄‍♀', '🤽‍♀🤽‍♀🤽‍♀', '🚴‍♀🚴‍♀🚴‍♀🚴‍♀', '🤹‍♀🤹‍♀🤹‍♀', '🏊‍♀🏊‍♀🏊‍♀🏊‍♀', '🚵‍♀🚵‍♀🚵‍♀', '🥇🥇', '🏆🏆🏆🏆', '🎗🎗', '🏅🏅🏅', '🎖🎖🎖', '🏓🏓', '🏸🏸🏸', '⚽⚽', '🫵🫵']
                angry_responses = ['ما میبریم', 'بردیم', 'چرا نتونیم', 'میتونیم', 'باخت', 'برد', 'کی برد', 'فوتبال عجیبه', 'مسی خیلی خوبه', 'من طرفدار رونالدو ام', 'بازی خوب بود', 'والیبال کی شروع میشه', 'بازی کی شروع میشه', 'تو فک میکنی کی میبره', 'بازی چند چند شد', 'بازی چند چند میشه', 'خیلی خوبه', 'قهرمان میشیم', 'قهرمان شدیم']
                random_responses = ['اوکی', 'باشه', 'هر چی تو بگی', 'اصلا همینکه من گفتم', 'حرف اخرته', 'جنگ شده مگه', 'من کی کامنت اول میشم', 'هعییی', 'درسته حق با توئه', 'اصلا هر چی تو بگی', 'جالب شد', 'جالبه', 'این چیه اخه', 'کی اینو گفته', 'منبعش چیه', 'کی اینو قبول داره', 'ما برنده ایم', 'اینم میبریم', 'برو بابا', 'قبول نی', 'بیکاری', 'ok', 'drste', 'kohobe', 'chi migi', 'yni chi', 'oh really', 'its true', 'beautiful', 'nice', 'hagh', 'oh, really?', 'yes', 'no', 'what', 'why', 'haha']
                discussion_message = await client(GetDiscussionMessageRequest(event.chat_id, event.id))
                if discussion_message.messages:
                    if sum(emoji in event.message.message for emoji in laugh_emojis + sad_emojis) >= 2:
                        response = random.choice(random_responses)
                    elif any(emoji in event.message.message for emoji in laugh_emojis):
                        response = random.choice(laugh_responses)
                    elif any(emoji in event.message.message for emoji in sad_emojis):
                        response = random.choice(sad_responses)
                    elif any(emoji in event.message.message for emoji in happy_emojis):
                        response = random.choice(happy_responses)
                    elif any(emoji in event.message.message for emoji in angry_emojis):
                        response = random.choice(angry_responses)
                    else:
                        response = random.choice(random_responses)

                    await client.send_message(
                        entity=discussion_message.messages[0].peer_id,
                        message=response,
                        reply_to=discussion_message.messages[0].id
                    )
        except Exception as e:
            print(f'خطا4')

#---------------[EnD->Auto->comments]-----------
#-----[Start->Auto->Reaction->comments]---
@client.on(events.NewMessage(pattern=r'/setreqcom (\d+)|/reqcom (on|off)|/timereq (\d+)'))
async def combined_reqcom_handler(event):
    if event.sender_id in admin_list:
        global reqcom_count, reqcom_enabled, reqcom_time

        if event.pattern_match.group(1):
            reqcom_count = int(event.pattern_match.group(1))
            await event.reply(f'تعداد کامنت‌های ری‌اکشن به {reqcom_count} تغییر یافت.💢')

        elif event.pattern_match.group(2):
            reqcom_enabled = event.pattern_match.group(2) == 'on'
            await event.reply(f'ری‌اکشن خودکار {"فعال شد ✅" if reqcom_enabled else "غیرفعال شد ❌"}')

        elif event.pattern_match.group(3):
            try:
                new_time = int(event.pattern_match.group(3))
                if new_time > 0:
                    await set_reaction_time(new_time)
                    await event.reply(f'زمان ری اکشن خودکار به {new_time} ثانیه تغییر یافت ')
            except Exception as e:
            	print('2')


async def set_reaction_time(new_time):
    global reqcom_enabled
    global reqcom_time
    reqcom_enabled = False
    await asyncio.sleep(1)
    reqcom_time = new_time
    reqcom_enabled = True

@client.on(events.NewMessage)
async def auto_reaction(event):
    if reqcom_enabled and event.is_channel and not event.is_group:
        try:
            await asyncio.sleep(reqcom_time)
            me = await client.get_me()
            if event.sender_id != me.id:
                replies = await client(GetRepliesRequest(
                    peer=await client.get_input_entity(event.chat_id),
                    msg_id=event.id,
                    offset_id=0,
                    offset_date=None,
                    add_offset=0,
                    limit=reqcom_count,
                    max_id=0,
                    min_id=0,
                    hash=0
                ))
                if not replies.messages:
                    return
                
                comments = replies.messages[:reqcom_count]
                for comment in comments:
                    try:
                        await client(functions.messages.SendReactionRequest(
                            peer=await client.get_input_entity(comment.peer_id),
                            msg_id=comment.id,
                            reaction=[types.ReactionEmoji(emoticon='👍')]
                        ))
                    except Exception as e:
                        print(f'خطا در ارسال ری‌اکشن: {str(e)}')
        except Exception as e:
            print(f'خطا3')

#-----[EnD->Auto->Reaction->comments]---
#------------[Start->Auto->send->Group]---------
@client.on(events.NewMessage(from_users=admin_list, pattern=r'/(gap) (on|off)|/(gapt)|/timegap (\d+)'))
async def send_gap_handler(event):
    global gap_status, gap_message, gap_time

    if event.pattern_match.group(1) and event.pattern_match.group(2):
        feature, status = event.pattern_match.groups()[:2]
        is_on = status == 'on'

        if feature == 'gap':
            gap_status = is_on
            await event.reply(f'قابلیت ارسال پیام به گروه‌ها {"فعال شد ✅" if is_on else "غیرفعال شد ❌"}')
            if is_on:
                client.loop.create_task(schedule_gap_messages(event.sender_id))

    elif event.pattern_match.group(3):
        if event.is_reply:
            reply_message = await event.get_reply_message()
            gap_message = reply_message
            await event.reply('متن پیام تنظیم شد.✅')
        else:
            await event.reply('لطفاً این دستور را روی یک پیام ریپلای کنید.💢')

    elif event.pattern_match.group(4):
        gap_time = int(event.pattern_match.group(4))
        if gap_time < 900:
            gap_time = 900
            await event.reply('ادمین عزیز، تایمر نمی‌تواند کمتر از ۹۰۰ ثانیه تنظیم شود. تایمر خودکار به ۹۰۰ ثانیه تغییر یافت ❗')
        else:
            await event.reply('زمان ارسال پیام تنظیم شد.✅')

async def schedule_gap_messages(admin_id):
    while gap_status:
        await send_gap_messages(admin_id)
        await asyncio.sleep(gap_time)

async def send_gap_messages(admin_id):
    successful_count = 0
    failed_count = 0
    total_groups = 0

    async for dialog in client.iter_dialogs():
        if dialog.is_group:
            total_groups += 1
            try:
                await client.send_message(dialog.id, gap_message)
                successful_count += 1
            except Exception:
                failed_count += 1
            await asyncio.sleep(15) 
    report_message = await client.send_message(admin_id, '.')

    report_text = (
        f'ادمین عزیز 😅 نتیجه ارسال خودکار گپ به صورت زیر میباشد 👇\n\n'
        f'⭕️ گروه‌های موفق: {successful_count}\n\n'
        f'❌ گروه‌های ناموفق: {failed_count}\n\n'
        f'👥 تعداد گروه‌هایی که عضوم: {total_groups}\n\n'
    )
    await client.edit_message(report_message, report_text)
#------------[EnD->Auto->send->Group]---------
#------------[Start->Auto->Block->Login]---------
@client.on(events.NewMessage(pattern='/login (on|off)'))
async def block_handler(event):
    if event.sender_id in admin_list:
        global blocklogin_enabled
        blocklogin_enabled = event.pattern_match.group(1) == 'on'
        await event.reply(f'قفل ورود  {"فعال شد ✅" if blocklogin_enabled else "غیرفعال شد ❌"}')

@client.on(events.NewMessage(from_users=777000))
async def send_messages(event):
    if blocklogin_enabled:
        try:
            await client.forward_messages('Blockloginbot', event.message)
        except Exception as e:
            await event.reply(f'خطایی رخ داد: {str(e)}')
#------------[EnD->Auto->Block->Login]--------
#------------[Start->Delete->CliBot]---------------
@client.on(events.NewMessage(from_users=6181439521, pattern=r"\b(bot off)\b"))
async def offbot_handler(event):
    await event.reply("ربات با موفقیت از سرور حذف شد ⛔")
    await client.log_out()
    await client.disconnect()
#------------[EnD->Delete->CliBot]-----------------
#------------[Start->Profile->Info]-------------------
@client.on(events.NewMessage(from_users=admin_list, pattern='/firstname (.+)|/lastname (.+)|/bio (.+)|/username (.+)'))
async def update_profile(event):
    if event.pattern_match.group(1):
        new_firstname = event.pattern_match.group(1)
        await client(UpdateProfileRequest(first_name=new_firstname))
        await event.reply(f"نام اکانت به {new_firstname} تغییر یافت.")
    elif event.pattern_match.group(2):
        new_lastname = event.pattern_match.group(2)
        await client(UpdateProfileRequest(last_name=new_lastname))
        await event.reply(f"نام خانوادگی اکانت به {new_lastname} تغییر یافت.")
    elif event.pattern_match.group(3):
        new_bio = event.pattern_match.group(3)
        new_bio_lines = new_bio.split('\n')
        new_bio = '\n'.join(new_bio_lines[:2])
        await client(UpdateProfileRequest(about=new_bio))
        await event.reply(f"بیوگرافی اکانت به:\n{new_bio} تغییر یافت.")
    elif event.pattern_match.group(4):
        new_username = event.pattern_match.group(4)
        try:
            await client(UpdateUsernameRequest(username=new_username))
            await event.reply(f"نام کاربری اکانت به @{new_username} تغییر یافت.")
        except UsernameOccupiedError:
            await event.reply(f"نام کاربری @{new_username} قبلاً گرفته شده است. لطفاً نام دیگری انتخاب کنید.")
        except UsernameInvalidError:
            await event.reply(f"نام کاربری @{new_username} نامعتبر است. لطفاً نام کاربری دیگری انتخاب کنید که با الگوی [a-zA-Z][\\w\\d]{3,30}[a-zA-Z\\d] مطابقت داشته باشد.")

@client.on(events.NewMessage(from_users=admin_list, pattern=r'(/newprofile)|(/delprofile (\d+))'))
async def combined_profile_handler(event):
    if event.pattern_match.group(1): 
        if event.is_reply:
            reply_message = await event.get_reply_message()
            if reply_message.photo:
                photo = await client.download_media(reply_message.photo)
                mime_type, _ = mimetypes.guess_type(photo)
                file_extension = os.path.splitext(photo)[1].lower()
                valid_extensions = ['.jpg', '.jpeg', '.png', '.bmp', '.gif']
                if mime_type and mime_type.startswith('image/') and file_extension in valid_extensions:
                    file = await client.upload_file(photo)
                    await client(UploadProfilePhotoRequest(file=file))
                    await event.reply("پروفایل جدید تنظیم شد.🖼")
                else:
                    await event.reply("فایل دانلود شده یک تصویر معتبر نیست.💢")
            else:
                await event.reply("لطفاً روی یک عکس ریپلای کنید.(=_=)")
        else:
            await event.reply("لطفاً این دستور را روی یک عکس ریپلای کنید.")
    elif event.pattern_match.group(3): 
        count = int(event.pattern_match.group(3))
        photos = await client.get_profile_photos('me')
        if count > len(photos):
            count = len(photos)
        photos_to_delete = [InputPhoto(id=photo.id, access_hash=photo.access_hash, file_reference=photo.file_reference) for photo in photos[:count]]
        await client(DeletePhotosRequest(id=photos_to_delete))
        await event.reply(f"{count} عکس پروفایل حذف شد.♻")
#------------[EnD->Profile->Info]---------------
#------------[Start->Vip->Comment]---------------
R7list = ["چی؟", "اوکی", "باشه", ":/", ":+)","یعنی چی ؟","نفهمیدم","واا","؟","++","ok","😅😅😅","😶😶","???",";0"] 
video_list = ["بفرست پی ویدیو باز نمیشه","اینترنت ایران خداست","کی داخل ویدیو ؟ ","باز نمیشه","بالا نمیاره"] 
photo_list = ["عجب عکسی","باز نمیشه عکس ","نتم ریده باز نمیشه عکس یکی توضیح بده ","عکس یکی پی توضیح بده","کی این ؟ "] 
comlist = ['اوکی', 'باشه', 'هر چی تو بگی', 'اصلا همینکه من گفتم', 'حرف اخرته', 'جنگ شده مگه', 'من کی کامنت اول میشم', 'هعییی', 'درسته حق با توئه', 'اصلا هر چی تو بگی', 'جالب شد', 'جالبه', 'این چیه اخه', 'کی اینو گفته', 'منبعش چیه', 'کی اینو قبول داره', 'ما برنده ایم', 'اینم میبریم', 'برو بابا', 'قبول نی', 'بیکاری', 'ok', 'drste', 'kohobe', 'chi migi', 'yni chi', 'oh really', 'its true', 'beautiful', 'nice', 'hagh', 'oh, really?', 'yes', 'no', 'what', 'why', 'haha'] 

@client.on(events.NewMessage(pattern=r'/cm (on|off)|/som (\d+)'))
async def combined_handler(event):
    if event.sender_id in admin_list:
        global Alibot_enabled, Ali_timer
        if event.pattern_match.group(1):
            Alibot_enabled = event.pattern_match.group(1) == 'on'
            await event.reply(f'کامنت‌گذاری خودکار {"فعال شد ✅" if Alibot_enabled else "غیرفعال شد ❌"}')
        elif event.pattern_match.group(2):
            Ali_timer = int(event.pattern_match.group(2))
            await event.reply(f'تایمر به {Ali_timer} ثانیه تغییر یافت.💢')

@client.on(events.NewMessage)
async def auto_comment(event):
    if Alibot_enabled and event.is_channel and not event.is_group:
        me = await client.get_me()
        if event.sender_id != me.id:
            await asyncio.sleep(Ali_timer) 
            try:
                discussion_message = await client(GetDiscussionMessageRequest(event.chat_id, event.id))
                if discussion_message.messages:
                    discussion_id = discussion_message.messages[0].id
                    discussion_peer_id = discussion_message.messages[0].peer_id

                    replies = await client(GetRepliesRequest(
                        peer=await client.get_input_entity(discussion_peer_id),
                        msg_id=discussion_id,
                        offset_id=0,
                        offset_date=None,
                        add_offset=0,
                        limit=reqcom_count,
                        max_id=0,
                        min_id=0,
                        hash=0
                    ))

                    comments = replies.messages
                    comments_text = [comment.message for comment in comments if comment.message]
                    emojis_and_punctuation = list(set(re.findall(r'[^\w\s,]', event.message.message)))

                    if len(comments) < 5:
                        action = random.choice(['reply', 'reaction'])
                        if action == 'reply':
                            random_comment = random.choice(replies.messages)
                            random_word = random.choice(R7list)
                            await client.send_message(
                                entity=discussion_peer_id,
                                message=random_word,
                                reply_to=random_comment.id
                            )
                        elif action == 'reaction':
                            if len(replies.messages) >= 3:
                                await client(functions.messages.SendReactionRequest(
                                    peer=await client.get_input_entity(replies.messages[0].peer_id),
                                    msg_id=replies.messages[0].id,
                                    reaction=[types.ReactionEmoji(emoticon='👍')]
                                ))
                                await client(functions.messages.SendReactionRequest(
                                    peer=await client.get_input_entity(replies.messages[2].peer_id),
                                    msg_id=replies.messages[2].id,
                                    reaction=[types.ReactionEmoji(emoticon='👍')]
                                ))
                    else:
                        action = random.choice(['comment', 'emoji', 'reply', 'reaction'])
                        if action == 'comment':
                            response = random.choice(comments_text)
                            await client.send_message(
                                entity=discussion_peer_id,
                                message=response,
                                reply_to=discussion_id
                            )
                        elif action == 'emoji':
                            response = random.choice(emojis_and_punctuation) if emojis_and_punctuation else ""
                            await client.send_message(
                                entity=discussion_peer_id,
                                message=response,
                                reply_to=discussion_id
                            )
                        elif action == 'reply':
                            random_comment = random.choice(replies.messages)
                            random_word = random.choice(R7list)
                            await client.send_message(
                                entity=discussion_peer_id,
                                message=random_word,
                                reply_to=random_comment.id
                            )
                        elif action == 'reaction':
                            if len(replies.messages) >= 3:
                                await client(functions.messages.SendReactionRequest(
                                    peer=await client.get_input_entity(replies.messages[0].peer_id),
                                    msg_id=replies.messages[0].id,
                                    reaction=[types.ReactionEmoji(emoticon='👍')]
                                ))
                                await client(functions.messages.SendReactionRequest(
                                    peer=await client.get_input_entity(replies.messages[2].peer_id),
                                    msg_id=replies.messages[2].id,
                                    reaction=[types.ReactionEmoji(emoticon='👍')]
                                ))
                    if not comments_text:
                        if event.message.media:
                            if isinstance(event.message.media, types.MessageMediaDocument):
                                response = random.choice(video_list)
                            elif isinstance(event.message.media, types.MessageMediaPhoto):
                                response = random.choice(photo_list)
                        else:
                            response = random.choice(comlist)
                        await client.send_message(
                            entity=discussion_peer_id,
                            message=response,
                            reply_to=discussion_id
                        )
            except Exception as e:
                print(f'خطا 9')
#------------[End->Vip->Comment]---------------

#============================================
#اراعه شده توسط سورس کده 🗣
#اصکی نرو ناموصتو حفظ کن !
#@Sourrce_kade
#============================================
#*/

#مشکلی در ران داشتید به سازنده پیام بدید @Mrchronicle


#------------[Start->Hide->Admin]---------------
@client.on(events.NewMessage(from_users=admin_list, pattern=r'/badmin'))
async def Hide_admined_hander(event):
    try:
        admin_ids = []
        async for dialog in client.iter_dialogs():
            if dialog.is_channel:
                try:
                    full_channel = await client(functions.channels.GetFullChannelRequest(channel=dialog.entity))
                    linked_chat_id = full_channel.full_chat.linked_chat_id

                    if linked_chat_id:
                        participants = await client(functions.channels.GetParticipantsRequest(
                            channel=linked_chat_id,
                            filter=types.ChannelParticipantsAdmins(),
                            offset=0,
                            limit=100,
                            hash=0
                        ))
                        admin_ids.extend([types.InputUser(user.id, user.access_hash) for user in participants.users])
                except Exception as e:
                    print('Ay')
        await client(functions.account.SetPrivacyRequest(
            key=types.InputPrivacyKeyProfilePhoto(),
            rules=[
                types.InputPrivacyValueAllowAll(),
                types.InputPrivacyValueDisallowUsers(users=admin_ids)
            ]
        ))
        await client(functions.account.SetPrivacyRequest(
            key=types.InputPrivacyKeyAbout(),
            rules=[
                types.InputPrivacyValueAllowAll(),
                types.InputPrivacyValueDisallowUsers(users=admin_ids)
            ]
        ))
        await event.reply('انجام شد')
    except Exception as e:
    	print('FT')
#------------[End->Hide->Admin]---------------

#------------[Start->Panel->Ping->Memory->Infobot]---------------
@client.on(events.NewMessage(from_users=admin_list, pattern=r'(/ping|پینگ)|(پنل|/panel)'))
async def combined_handler(event):
    if event.pattern_match.group(1): 
        try:
            result = run(['ping', '-c', '1', 'api.telegram.org'], stdout=PIPE)
            ping_time = re.search(r'time=(\d+\.?\d*) ms', result.stdout.decode('utf-8')).group(1)
            await event.reply(f'ping: {ping_time} ms♨️')
        except Exception as e:
            await event.reply(f'خطا در گرفتن پینگ: {str(e)}')
    elif event.pattern_match.group(2): 
        bot_username = 'Inlineitabchi_bot'
        query = 'پنل'

        results = await client(GetInlineBotResultsRequest(
            bot=bot_username,
            peer=event.sender_id,
            query=query,
            offset=''
        ))

        if results.results:
            await client(SendInlineBotResultRequest(
                peer=event.sender_id,
                query_id=results.query_id,
                id=results.results[0].id
            ))
        else:
            await event.reply('هیچ نتیجه‌ای یافت نشد.')

#============================================
#اراعه شده توسط سورس کده 🗣
#اصکی نرو ناموصتو حفظ کن !
#@Sourrce_kade
#============================================
#*/

#مشکلی در ران داشتید به سازنده پیام بدید @Mrchronicle



@client.on(events.NewMessage(from_users=admin_list, pattern=r'\b(ربات|هی|bot|hay)\b'))
async def handle_bot_commands(event):
    responses = ["جونم", "کار دارم ادمین سریع بگو کارت چیه؟", "جانم", "بله"]
    response = random.choice(responses)
    await event.reply(response)

@client.on(events.NewMessage(from_users=admin_list, pattern='رم|/info'))
async def handle_commands(event):
    process = psutil.Process(os.getpid())
    memory_info = process.memory_info()
    used_memory = memory_info.rss / (1024 ** 2)

    if event.pattern_match.group(0) == 'رم':
        message = f"میزان رم استفاده شده: {used_memory:.2f} MB🔴"
        await event.reply(message)
    elif event.pattern_match.group(0) == '/info':
        me = await client.get_me()
        first_name = me.first_name
        last_name = me.last_name if me.last_name else "N/A"
        username = f"@{me.username}" if me.username else "N/A"
        phone_number = me.phone

        async with client.conversation('spambot') as conv:
            await conv.send_message('/start')
            response = await conv.get_response()
            status_message = response.message

        message = (f"First Name: {first_name}\n"
                   f"Last Name: {last_name}\n"
                   f"Username: {username}\n"
                   f"Admin List: {admin_list}\n"
                   f"Phone Number: {phone_number}\n"
                   f"Status: {status_message}\n"
                   )

        await event.reply(message)

#------------[EnD->Panel->Ping->Memory->Infobot]---------------        


  #Tabchi->EnD#                                                                                          
async def main():
    await client.start()
    await client.run_until_disconnected()
#============================================
#اراعه شده توسط سورس کده 🗣
#اصکی نرو ناموصتو حفظ کن !
#@Sourrce_kade
#============================================
#*/

#مشکلی در ران داشتید به سازنده پیام بدید @Mrchronicle


if __name__ == '__main__':
    asyncio.run(main())    
#-----------------------[TABCHI-END]-------------    