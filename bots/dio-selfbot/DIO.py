# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

from pyrogram import Client, enums, filters
from apscheduler.schedulers.asyncio import AsyncIOScheduler
import random, asyncio, pytz, os
from datetime import datetime

# AMir | https://t.me/disculpas
# opened in ! { @Sourrce_kade } !
target, muute, game, type, list_fosh, status_time_bio, bio, status_time_name = [], [], ["off"], ["off"], ["کصمادرت", "کصننت", "ننه جنده", "کصمامانت تو ماهیتابه"], ['off'], ["bye bye 🤫🧏"], ['off']
api_id = 0 #your api id — از @MyTelegramInfo_OT بگیرید 
api_hash = '' #your api hash
app = Client('DIO-self', api_id, api_hash)
scheduler = AsyncIOScheduler()

async def change_time_profile_bio():
    if status_time_bio[0] == 'on':
        country_time_zone = pytz.timezone('Asia/Tehran')
        country_time = datetime.now(country_time_zone)
        time = country_time.strftime("%H:%M")
        await app.update_profile(bio=f'{time} | {bio[0]}')
        
async def change_time_profile_name():
    if status_time_name[0] == 'on':
        country_time_zone = pytz.timezone('Asia/Tehran')
        country_time = datetime.now(country_time_zone)
        time = country_time.strftime("%H:%M")
        await app.update_profile(last_name=f'{time}')

@app.on_message(filters.photo & filters.private)
async def onphoto(client, message) :
    try :
        if message.photo.ttl_seconds :
            rand = random.randint(1000, 9999999) 
            local = f"downloads/photo-{rand}.png"
            await app.download_media(message.photo.file_id, file_name=f"photo-{rand}.png")
            await app.send_photo("me", photo=local, caption=f"`🥸 New timed image {message.photo.date} | time : {message.photo.ttl_seconds}s`")
            os.remove(local)
    except :
        pass

@app.on_message(filters.video & filters.private)
async def onvideo(client, message) :
    try :
        if message.video.ttl_seconds : 
            rand = random.randint(1000, 9999999)
            local = f"downloads/video-{rand}.mp4"
            await app.download_media(message.video.file_id, file_name=f"video-{rand}.mp4")
            await app.send_video("me", video=local, caption=f"`🥸 New timed video {message.video.date} | time : {message.video.ttl_seconds}s`")
            os.remove(local)
    except :
        pass

@app.on_message(filters.reply & filters.regex('(?i)^.save$'))
async def save(client, message):
    try:
        await app.copy_message("me", message.chat.id, message.reply_to_message_id)
        await app.delete_messages(message.chat.id, message.id, revoke=True)
    except:
        await app.edit_message_text(message.chat.id, message.id, "error!")
        await asyncio.sleep(5)
        await app.delete_messages(message.chat.id, message.id, revoke=True)

#profile
@app.on_message(filters.me & filters.regex('(?i)^قلب$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "`❤️`")
    await asyncio.sleep(1.2)
    await app.edit_message_text(chat_id, msg_id, "`💙`")
    await asyncio.sleep(1.2)
    await app.edit_message_text(chat_id, msg_id, "`💜`‌")
    await asyncio.sleep(1.2)
    await app.edit_message_text(chat_id, msg_id, "`💚`")
    await asyncio.sleep(1.6)
    await app.edit_message_text(chat_id, msg_id, "`💛`")
    await asyncio.sleep(1.2)
    await app.edit_message_text(chat_id, msg_id, "`🧡`‌")
    await asyncio.sleep(1.2) 
    await app.edit_message_text(chat_id, msg_id, "**i love you**‌")

@app.on_message(filters.me & filters.regex('(?i)^کص مادرت$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "`ک‍`")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص م‍")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص ما")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص ماد")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص مادر")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص مادرت")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "ننه جنده")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کیرم تو کصننت")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "ننه کیر پرست")
    await asyncio.sleep(0.8)
    await app.edit_message_text(chat_id, msg_id, "کص مادرت")

@app.on_message(filters.me & filters.regex('(?i)^جق بزن$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "`B===✊==>>`")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B====✊=>>`")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B=====✊>>`‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B====✊=>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B===✊==>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B==✊===>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B=✊====>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B✊=====>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B=✊====>>`")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B==✊===>>`")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B===✊==>>`‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B====✊=>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B=====✊>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B====✊=>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B===✊==>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "`B==✊===>>`️‌")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "B======>>✊💦")
    
    
@app.on_message(filters.me & filters.regex('(?i)^Load$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "•••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "ㅤ")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "ㅤ")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "ㅤ")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "ㅤ")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "•••")
    await asyncio.sleep(0.5)
    await app.edit_message_text(chat_id, msg_id, "I'm okey :)")

@app.on_message(filters.me & filters.regex('(?i)^Hack$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "1010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101010110101010\nLoading.")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "1010100101010110001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010\nLoading..")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "1010101100101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010\nLoading...")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "1010100101000111001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010\nLoading..")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "1010101010101001001010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010101010101010111010101101010101010101010101110101011010101010101010101011101010110101010\nLoading.")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "1010001110010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010101010010101001011111010010000101101001001000010111001111110001101011000111110101010\nLoading...")
    await asyncio.sleep(0.9)
    await app.edit_message_text(chat_id, msg_id, "**All information has been sent to Sio Messenger!\nتمامی اطلاعات به سیو مسیج ارسال شد!**‌")

@app.on_message(filters.me & filters.regex('(?i)^هعی$'))
async def hert(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "خستم")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "درد دارم")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "از زندگی متنفرم")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "دلم خودکشی‌ میخواد")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "حالم بده")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "تنهام")
    await asyncio.sleep(1)
    await app.edit_message_text(chat_id, msg_id, "هعی")


@app.on_message(filters.me & filters.regex('(?i)^.bot$'))
async def ping(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, f"**<a href='tg://user?id={message.from_user.id}'>{message.from_user.first_name} bot is online.**</a>")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)
    
@app.on_message(filters.me & filters.regex('(?i)^.status$'))
async def ping(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    status = f"""
**DIO self status**

time bio : {status_time_bio[0]}
time name : {status_time_name[0]}
gaming mod : {game[0]}
typeing mod : {type[0]}
"""
    await app.edit_message_text(chat_id, msg_id, status)

@app.on_message(filters.me & filters.regex('(?i)^.timebio on$'))
async def time_bio_on(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    status_time_bio.clear()
    status_time_bio.append("on")
    await app.edit_message_text(chat_id, msg_id, f"**تایم در بیو فعال شد.**")
    scheduler.add_job(change_time_profile_bio, "interval", minutes=1)
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.timebio off$'))
async def time_bio_off(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    status_time_bio.clear()
    status_time_bio.append("off")
    await app.edit_message_text(chat_id, msg_id, f"**تایم در بیو غیرفعال شد.**")
    await app.update_profile(bio=f'--:-- | {bio[0]}')
    scheduler.remove_all_jobs()
    if status_time_name[0] == "on":
     scheduler.add_job(change_time_profile_name, "interval", minutes=1)
     await asyncio.sleep(25)
     await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.timename on$'))
async def time_name_on(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    status_time_name.clear()
    status_time_name.append("on")
    await app.edit_message_text(chat_id, msg_id, f"**تایم در اسم فعال شد.**")
    scheduler.add_job(change_time_profile_name, "interval", minutes=1)
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.timename off$'))
async def time_name_off(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    status_time_name.clear()
    status_time_name.append("off")
    await app.edit_message_text(chat_id, msg_id, f"**تایم در اسم غیر فعال شد.**")
    await app.update_profile(last_name='--:--')
    if status_time_bio[0] == "on":
     scheduler.add_job(change_time_profile_bio, "interval", minutes=1)
     await asyncio.sleep(25)
     await app.delete_messages(chat_id, msg_id, revoke=True)


@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.setbio$'))
async def setbio(client, message):
    bios = message.reply_to_message.text
    msg_id = message.id
    chat_id = message.chat.id
    bio.clear()
    bio.append(bios)
    await app.edit_message_text(chat_id, msg_id, f"**بیو با موفقیت تنظیم شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.typeing on$'))
async def action_type_on(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    type.clear()
    game.clear()
    game.append("off")
    type.append("on")
    await app.edit_message_text(chat_id, msg_id, "**حالت تایپینگ روشن شد!**")



@app.on_message(filters.me & filters.regex('(?i)^.typeing off$'))
async def action_type_off(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    type.clear()
    game.clear()
    game.append("off")
    type.append("off")
    await app.edit_message_text(chat_id, msg_id, "**حالت تایپینگ خاموش شد!**")



@app.on_message(filters.me & filters.regex('(?i)^.gameing on$'))
async def action_game_on(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    type.clear()
    game.clear()
    game.append("on")
    type.append("off")
    await app.edit_message_text(chat_id, msg_id, "**حالت گیمینگ روشن شد!**")



@app.on_message(filters.me & filters.regex('(?i)^.gameing off$'))
async def action_game_off(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    type.clear()
    game.clear()
    game.append("off")
    type.append("off")
    await app.edit_message_text(chat_id, msg_id, "**حالت گیمینگ خاموش شد!**")

#enemy

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.enemy$'))
async def enemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    await app.block_user(user_id)
    target.append(user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> به لیست دشمنان اضافه شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.friend$'))
async def unenemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    target.remove(user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> از لیست دشمنان حذف شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)


@app.on_message(filters.me & filters.regex('(?i)^.addenemy$'))
async def enemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user = message.reply_to_message.text
    await app.block_user(user)
    target.append(int(user))
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user}'>{user}</a> به لیست دشمنان اضافه شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.delenemy$'))
async def unenemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user = message.reply_to_message.text
    target.remove(int(user))
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user}'>{user}</a> از لیست دشمنان حذف شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)


@app.on_message(filters.me & filters.regex('(?i)^.enemylist$'))
async def list_enemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "لیست دشمنان بدین شرح است : \n\n"+str(target))




@app.on_message(filters.me & filters.regex('(?i)^.cleanenemylist$'))
async def delete_list_enemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    for user_id in target:
        await app.unblock_user(user_id)
    target.clear()
    await app.edit_message_text(chat_id, msg_id, "**لیست دشمنان پاکسازی شد!**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.addf$'))
async def addf(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    f = message.reply_to_message.text
    list_fosh.append(f)
    await app.edit_message_text(chat_id, msg_id, f"**{f} به لیست فحش ها اضافه شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.delf$'))
async def delf(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    f = message.reply_to_message.text
    list_fosh.remove(f)
    await app.edit_message_text(chat_id, msg_id, f"**{f} از لیست فحش پاک شد!**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)


@app.on_message(filters.me & filters.regex('(?i)^.flist$'))
async def list_f(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "لیست تمامی فحش ها بدین شرح است: \n\n"+str(list_fosh))




@app.on_message(filters.me & filters.regex('(?i)^.cleanflist$'))
async def delete_list_f(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    list_fosh.clear()
    await app.edit_message_text(chat_id, msg_id, "**لیست فحش ها با موفقیت پاکسازی شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

#group

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.mute$'))
async def mute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    muute.append(user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> با موفقیت در حالت سکوت قرار گرفت.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)



@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.unmute$'))
async def unmute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    muute.remove(user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> با موفقیت از حالت سکوت خارج شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.ban$'))
async def ban(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    await app.ban_chat_member(chat_id , user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> با موفقیت بن شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)



@app.on_message(filters.me & filters.reply & filters.regex('(?i)^.unban$'))
async def unban(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user_id = message.reply_to_message.from_user.id
    name = message.reply_to_message.from_user.first_name
    await app.unban_chat_member(chat_id, user_id)
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user_id}'>{name}</a> با موفقیت حذف بن شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.addmute$'))
async def mute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user = message.reply_to_message.text
    muute.append(int(user))
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user}'>{user}</a> با موفقیت سکوت شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.delmute$'))
async def unmute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    user = message.reply_to_message.text
    muute.remove(int(user))
    await app.edit_message_text(chat_id, msg_id, f"**کاربر <a href='tg://user?id={user}'>{user}</a> با موفقیت حذف سکوت شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)


@app.on_message(filters.me & filters.regex('(?i)^.mutelist$'))
async def list_mute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    await app.edit_message_text(chat_id, msg_id, "لیست سکوت بدین شرح است : \n\n"+str(muute))





@app.on_message(filters.me & filters.regex('(?i)^.cleanmutelist$'))
async def delete_list_mute(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    muute.clear()
    await app.edit_message_text(chat_id, msg_id, "**لیست سکوت پاکسازی شد.**")
    await asyncio.sleep(25)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.help$'))
async def help(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel main**

`.status` 

`.enemypanel`

`.grouppanel`

`.entertainmentpanel`

`.profilepanel`

`.order`
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.enemypanel$'))
async def helpenemy(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel enemy**

`.enemy` -> (ریپلای روی کاربر)
تنظیم کاربر به عنوان دشمن

`.friend` -> (ریپلای روی کاربر)
حذف کاربر از لیست دشمنان

`.addenemy` -> (ریپلای روی پیام آیدی عددی)
تنظیم کاربر به عنوان دشمن

`.delenemy` -> (ریپلای روی پیام آیدی عددی)
حذف کاربر از لیست دشمنان

`.enemylist`
دیدن لیست دشمنان

`.cleanenemylist`
پاکسازی لیست دشمنان

`.addf` -> (ریپلای روی پیام فحش)
افزودن فحش به لیست

`.delf` -> (ریپلای روی پیام فحش)
حذف فحش از لیست

`.flist`
دیدن لیست فحش ها

`.cleanflist`
پاکسازی لیست فحش ها
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.grouppanel$'))
async def helpgroup(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel group**

`.mute` -> (ریپلای روی کاربر)
سکوت کردن کاربر

`.unmute` -> (ریپلای روی کاربر)
حذف کاربر از لیست سکوت

`.addmute` -> (ریپلای روی پیام آیدی عددی)
افزودن کاربر به لیست سکوت

`.delmute` -> (ریپلای روی پیام آیدی عددی)
حذف کاربر از لیست سکوت

`.mutelist`
دیدن لیست کاربران سکوت شده

`.cleanmutelist`
پاکسازی لیست کاربران سکوت شده

`.ban` -> (ریپلای روی کاربر)
بن کردن کاربر

`.unban` -> (ریپلای روی کاربر)
حذف کاربر از لیست بن

`.info`
دریافت اطلاعات گروه
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)
@app.on_message(filters.me & filters.regex('(?i)^.entertainmentpanel$'))
async def help(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel entertainment **

`قلب`

`کص مادرت`

`جق بزن`

`هعی`

`Hack`

`Load`
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.profilepanel$'))
async def helptool(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel profile **

`.timename on|off`
روشن|خاموش کردن نمایش زمان در اسم

`.timebio on|off`
روشن|خاموش کردن نمایش زمان د بیو

نکته : سر دقیقه ( به محض تغییر دقیقه ساعت) از دستورات بالا استفاده کنید تا تایم بیو یا اسم جلو عقب نمونه.



`.setbio` -> (ریپلای روی پیام بیو)
تنظیم بیو

`.typeing on|off`
روشن|خاموش کردن حالت تایپینگ 

`.gameing on|off`
روشن|خاموش کردن حالت گیم
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)

@app.on_message(filters.me & filters.regex('(?i)^.order$'))
async def order(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    help = """
**DIO self panel order**

`.bot`
مشاهده آنلاین بودن سلف

`.data` -> (ریپلای روی پیام کاربر) 
دریافت تمام اطلاعات پیام

`.id` -> (ریپلای روی پیام کاربر) 
دریافت اطلاعات کاربر در پیوی یا گروه

`.save` -> (ریپلای روی پیام کاربر) 
سیو متن،مدیا،استیکر،گیف،ویس و... در سیو مسیج


**ربات به سیو خودکار فیلم و عکس تایمردار مجهز است.**
"""
    await app.edit_message_text(chat_id, msg_id, help)
    await asyncio.sleep(60)
    await app.delete_messages(chat_id, msg_id, revoke=True)
   
@app.on_message(filters.me & filters.regex('(?i)^.data$'))
async def data(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    data = message.reply_to_message
    await app.edit_message_text(chat_id, msg_id, f"{data}")
   
   
@app.on_message(filters.me & filters.regex('(?i)^.id$'))
async def gp(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    info = message.reply_to_message.from_user
    text = f"""
**first name** : `{info.first_name}`
**Id** : `{info.id}` 
**username** : `{info.username}`
**Yourself** : `{info.is_self}`
**Your contacts** : `{info.is_contact}`
**Your mutual contact** : `{info.is_mutual_contact}`
**Deleted account** : `{info.is_deleted}`
**Bot** : `{info.is_bot}`
  **Account status**
        **scam** : `{info.is_scam}`
        **fake** : `{info.is_fake}`
        **premium** : `{info.is_premium}`
        **last visit** : `{info.status}`
"""
    await app.edit_message_text(chat_id, msg_id, str(text))
   
@app.on_message(filters.group & filters.me & filters.regex('(?i)^.info$'))
async def gp(client, message):
    msg_id = message.id
    chat_id = message.chat.id
    info = await app.get_chat(chat_id)
    text = f"""
**chat_id** : `{info.id}`
**count** : `{info.members_count}`
**name** : `{info.title}`
**invite link** : `{info.invite_link}`
"""
    await app.edit_message_text(chat_id, msg_id, str(text))

@app.on_message(filters.me)
async def me(client, message):
    chat_id = message.chat.id
    if str(game[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.PLAYING)
    elif str(type[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.TYPING)
    else:
        pass




@app.on_message(filters.private)
async def filters_pv(client, message):
    chat_id = message.chat.id
    if str(game[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.PLAYING)
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)
    elif str(type[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.TYPING)
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)
    else:
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)
            



@app.on_message(filters.group)
async def filters_group(client, message):
    chat_id = message.chat.id
    if str(game[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.PLAYING)
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)
    elif str(type[0]) == "on":
        await app.send_chat_action(chat_id, enums.ChatAction.TYPING)
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)
    else:
        msg_id = message.id
        chat_id = message.chat.id
        if message.from_user.id in target:
            text = list_fosh[random.randrange(len(list_fosh))]
            await message.reply_text(text)
        elif message.from_user.id in muute:
            await app.delete_messages(chat_id, msg_id, revoke=True)



scheduler.start()
app.run(print ('bot is run...'))