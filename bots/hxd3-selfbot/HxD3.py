# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os, asyncio 
from pyrogram import Client, filters, errors
from pyrogram.enums import ChatType
from apscheduler.schedulers.asyncio import AsyncIOScheduler
from re import findall
scheduler = AsyncIOScheduler({'apscheduler.job_defaults.max_instances':1})
app = Client("HxD3" , api_id = "0" , api_hash = "PUT-YOUR-API-HASH")
#=edit=#
idadmin = 0000000000 #آیدی عددی ادمین
#=edit=#
#=به هیج وجه به لیست زیر دست نزنید=#
channels = ['@click_Linko', '@linkdoni_sib', '@linkdoni_Enfejar', '@ghoohghazayieh', '@global1asly']
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
installfile = 'install.txt'
adgapchatidf = 'adgapchatid.txt'
adgapmessageidf = 'adgapmessageid.txt'
adpvchatidf = 'adpvchatid.txt'
adpvmessageidf = 'adpvmessageid.txt'
numberf = 'numberf.txt'
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
timerg , timerp = [], []
timerg.append('off')
timerp.append('off')
if not os.path.exists(installfile):
 names = ['install.txt', 'adgapchatid.txt', 'adgapchatid.txt', 'adgapmessageid.txt', 'adpvchatid.txt', 'adpvmessageid.txt', 'numberf.txt']
 for name in names:
  with open(name, 'w') as file:
    file.write('0')
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
def auto():
    with open(numberf, 'w') as file:
     file.write('0')
async def sendtogroup(client, message, post):
  dialogs = app.get_dialogs()
  async for dialog in dialogs:
   if dialog.chat.type in [ChatType.SUPERGROUP, ChatType.GROUP]:
    try:
      await app.copy_message(chat_id=dialog.chat.id, from_chat_id=message.chat.id, message_id=post.id)
      await asyncio.sleep(25)
    except Exception as error:
      pass
async def Autosendadgap():
 with open('adgapmessageid.txt', 'r') as file:
      mid = int(file.read())
 with open('adgapchatid.txt', 'r') as file:
      cid = int(file.read())
 async for dialog in app.get_dialogs():
        if dialog.chat.type in [ChatType.SUPERGROUP, ChatType.GROUP]:

            try:
                await app.copy_message(chat_id=dialog.chat.id, from_chat_id=cid,
                                          message_id=mid)
                await asyncio.sleep(40)
            except (errors.FloodWait, errors.ChatWriteForbidden, errors.PeerIdInvalid, errors.MessageNotModified):
                pass
            except (errors.ChatRestricted, errors.UserMuteedInChannel):
                await app.leave_chat(dialog.chat.id, delete=True)
            except Exception as error:
                pass
async def sendtopv(client, message, post):
  dialogs = app.get_dialogs()
  async for dialog in dialogs:
   if dialog.chat.type == ChatType.PRIVATE:
    try:
      await app.copy_message(chat_id=dialog.chat.id, from_chat_id=message.chat.id, message_id=post.id)
      await asyncio.sleep(30)
    except Exception as error:
      pass
async def Autosendadpv():
 with open('adpvmessageid.txt', 'r') as file:
      mid = int(file.read())
 with open('adpvchatid.txt', 'r') as file:
      cid = int(file.read())
 async for dialog in app.get_dialogs():
        if dialog.chat.type == ChatType.PRIVATE:
            try:
                await app.copy_message(chat_id=dialog.chat.id, from_chat_id=cid,
                                          message_id=mid)
                await asyncio.sleep(40)
            except (errors.FloodWait, errors.ChatWriteForbidden, errors.PeerIdInvalid, errors.MessageNotModified):
                pass
            except Exception as error:
                pass
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
@app.on_message(filters.private)
async def private(client, message):
  adgapchatidf = 'adgapchatid.txt'
  adgapmessageidf = 'adgapmessageid.txt'
  adpvchatidf = 'adpvchatid.txt'
  adpvmessageidf = 'adpvmessageid.txt'
  d = message.text
  if message.from_user.id == idadmin:
   if d == "!bot":
    await message.reply_text(f"تبچی HxD فعال است.")
   elif d == "!sendgap":
    x = message.reply_to_message
    await message.reply_text(f"لطفا صبر کنید...\n(از ارسال پیام جدید خودداری کنید)")
    await sendtogroup(client, message, x)
    await message.reply_text("پیام مورد نظر برای تمامی گروه ها ارسال شد.")
   elif d == "!sendpv":
    x = message.reply_to_message
    await message.reply_text(f"لطفا صبر کنید...\n(از ارسال پیام جدید خودداری کنید)")
    await sendtopv(client, message, x)
    await message.reply_text("پیام مورد نظر برای تمامی پیوی ها ارسال شد.")
   elif d == "!setadgap":
    x = message.chat.id
    y = message.reply_to_message.id
    with open(adgapchatidf, 'r+') as f:
     adgapchatidf = int(f.read())
     adgapchatidf = x
     f.seek(0)
     f.write(str(adgapchatidf))
    with open(adgapmessageidf, 'r+') as f:
     adgapmessageidf = int(f.read())
     adgapmessageidf = y
     f.seek(0)
     f.write(str(adgapmessageidf))
    await message.reply_text("تبلیغ (گروه) با موفقیت تنظیم شد.")
   elif d == "!setadpv":
    x = message.chat.id
    y = message.reply_to_message.id
    with open(adpvchatidf, 'r+') as f:
     adpvchatidf = int(f.read())
     adpvchatidf = x
     f.seek(0)
     f.write(str(adpvchatidf))
    with open(adpvmessageidf, 'r+') as f:
     adpvmessageidf = int(f.read())
     adpvmessageidf = y
     f.seek(0)
     f.write(str(adpvmessageidf))
    await message.reply_text("تبلیغ (پیوی) با موفقیت تنظیم شد.")
   elif "!settimergap" in d:
    if d == "!settimergap off":
      scheduler.remove_job('adgaptimer')
      await message.reply_text("ارسال اتوماتیک به گروه با موفقیت خاموش شد!")
    else:
     v = d.split(',')
     t = v[1]
     time = int(t)
     if timerg[0] == 'on':
      scheduler.remove_job('adgaptimer')
     scheduler.add_job(Autosendadgap, 'interval', minutes=time, id='adgaptimer', replace_existing = True)
     await message.reply_text(f"تایمر ارسال به گروه با موفقیت به هر {time} دقیقه تغییر کرد.")
   elif "!settimerpv" in d:
    if d == "!settimerpv off":
      scheduler.remove_job('adgaptimer')
      await message.reply_text("ارسال اتوماتیک به گروه با موفقیت خاموش شد!")
    else:
     v = d.split(',')
     t = v[1]
     time = int(t)
     if timerp[0] == 'on':
      scheduler.remove_job('adpvtimer')
     scheduler.add_job(Autosendadpv, 'interval', minutes=time, id='adpvtimer', replace_existing = True)
     await message.reply_text(f"تایمر ارسال به پیوی با موفقیت به هر {time} دقیقه تغییر کرد.")
   elif d == "!configuration":
    for channel in channels:
           try:
               await client.join_chat(channel)
           except Exception as e:
               pass
    await app.send_message(idadmin,
                                                  f"پیکربندی با موفقیت انجام شد.")
   elif "!extraction" in d:
    i = 0
    v = d.split(',')
    m = v[1]
    s = v[2]
    t = v[3]
    ss = int(s)
    mm = int(m)
    tt = int(t)
    await message.reply_text(f"لطفا صبر کنید...\n(از ارسال پیام جدید خودداری کنید)")
    async for member in app.get_chat_members(mm):
      i += 1
      if i >= tt:
        break
      try:
       await app.add_chat_members(ss, member.user.id)
       await app.send_message(idadmin,
                                                  f"کاربر {member.user.id} با موفقیت به {ss} اضافه شد.")
       await asyncio.sleep(30)
      except Exception as e:
           pass
    await app.send_message(idadmin,
                                                  f"استخراج گروه {mm} به پایان رسید.")
   elif "!pvmember" in d:
    i = 0
    v = d.split(',')
    m = v[1]
    ad = v[2]
    t = v[3]
    mm = int(m)
    tt = int(t)
    await message.reply_text(f"لطفا صبر کنید...\n(از ارسال پیام جدید خودداری کنید)")
    async for member in app.get_chat_members(mm):
      i += 1
      if i >= tt:
        break
      try:
       await app.send_message(member.user.id, ad)
       await app.send_message(idadmin,
                                                  f"پیام تبلیغ به کاربر {member.user.id} ارسال شد.")
       await asyncio.sleep(30)
      except Exception as e:
           pass
    await app.send_message(idadmin,
                                                  f"ارسال پیام به ممبرای گروه {mm} به پایان رسید.")
   elif d == "!number":
     me = await app.get_me()
     await message.reply_contact(me.phone_number, me.first_name)
     
   elif d == "!list":
     number = 1
     await message.reply_text("لیست گروه هایی که ربات در آن عضو است :")
     async for group in app.get_dialogs():
      if group.chat.type in [ChatType.SUPERGROUP, ChatType.GROUP]:
       try:
         await app.send_message(message.chat.id,
                                                      f"{number} - name group : {group.chat.title}\nchat id : {group.chat.id}")
         number += 1
         await asyncio.sleep(3)
       except:
           pass
     await message.reply_text("لیست گروه های ربات به اتمام رسید.")
   elif d == "!help":
     await message.reply_text("""📚 برای مشاهده راهنما طبق دستور عمل زیر پیش بروید.
                              
1 - @HxDHelperbot help را در مسیج باکس تایپ کنید.
2 - بر روی دریافت راهنما کلیک کنید.""")
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
@app.on_message(filters.group)
async def group(client, message):
 d = message.text
 if message.from_user.id == idadmin:
  try:
   if d == "!id":
    await app.send_message(idadmin, f"اطلاعات گروه : \n chat_id {message.chat.id}")
  except Exception as e:
               pass
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
@app.on_message(filters.channel & filters.chat(channels))
async def channel(client, message):
  with open(numberf, 'r') as file:
    joinnum = int(file.read())
  if joinnum > 4:
    pass
  else:
    with open(numberf, 'r+') as f:
     joinnum = int(f.read())
     joinnum += 1
     f.seek(0)
     f.write(str(joinnum))
    if message.caption:
     matches = findall("https?://(?:t\.me|telegram\.me)/\S+",  message.caption)
    else:
     matches = findall("https?://(?:t\.me|telegram\.me)/\S+",  message.text)
    for link in matches:
     try:
      await app.join_chat(link)
      await asyncio.sleep(50)
     except Exception as e:
               pass
#===#
scheduler.add_job(auto, 'interval', minutes=180)
scheduler.start()
#نویسنده : t.me/tajyakhi | t.me/renhr کصننت پاکش کنی بیغیرت نباش
app.run(print('Powered by t.me/HxDGap'))