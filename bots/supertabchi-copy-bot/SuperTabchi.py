# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

# start import
from pyrogram import Client, errors
from pyrogram.enums import ChatType
from re import match, IGNORECASE, findall
import asyncio
from apscheduler.schedulers.asyncio import AsyncIOScheduler
# end import
# start Pyrogram.Client
app = Client("iphone 11 pro" , api_id = "" , api_hash = "")  # api_id / api_hash خودت را اینجا بگذارید
scheduler = AsyncIOScheduler({'apscheduler.job_defaults.max_instances':2})
# end Pyrogram.Client
# start DB
Admins, Timerg, Timerp, Adgs, Adps, AdCHIDG, AdIDG, AdCHIDP, AdIDP = [], [], [], [], [], [], [], [], []
# end DB
# start DB creation
if Admins:
 print('The initial database is already created!')
else:
 Owner = input('Enter the first admin ID :')
 Admins.append(int(Owner))
 Adgs.append('off')
 Adps.append('off')
 Timerg.append(int('10'))
 Timerp.append(int('10'))
# end DB creation
# start Check admin id
def checkId(Admins):
    if match("(\d{9,10})", Admins):
        return True
    else:
        return False
# end Check admin id
# start Pv Robot
async def sendtopv(client, message, post):
  dialogs = app.get_dialogs()
  async for dialog in dialogs:
   if dialog.chat.type == ChatType.PRIVATE:
    try:
      await app.copy_message(chat_id=dialog.chat.id, from_chat_id=message.chat.id, message_id=post.id)
      await asyncio.sleep(15)
    except Exception as error:
      pass
async def addgroup(client, idgap):
  dialogs = app.get_dialogs()
  async for dialog in dialogs:
   if dialog.chat.type == ChatType.PRIVATE:
    try:
      await app.add_chat_members(idgap, dialog.chat.id)
      await asyncio.sleep(10)
    except Exception as error:
      pass
async def bannerP_handler():
    if not (Adps[0] == "on"):
        return

    async for dialog in app.get_dialogs():
        if dialog.chat.type == ChatType.PRIVATE:
            try:
                await app.copy_message(chat_id=dialog.chat.id, from_chat_id=AdCHIDP[0],
                                          message_id=AdIDP[0])
                await asyncio.sleep(20)
            except (errors.FloodWait, errors.ChatWriteForbidden, errors.PeerIdInvalid, errors.MessageNotModified):
                pass
            except Exception as error:
                pass
# end Pv Robot
# start Group Robot
async def sendtogroup(client, message, post):
  dialogs = app.get_dialogs()
  async for dialog in dialogs:
   if dialog.chat.type in [ChatType.SUPERGROUP, ChatType.GROUP]:
    try:
      await app.copy_message(chat_id=dialog.chat.id, from_chat_id=message.chat.id, message_id=post.id)
      await asyncio.sleep(15)
    except (errors.FloodWait, errors.ChatWriteForbidden, errors.PeerIdInvalid, errors.MessageNotModified):
        pass
    except (errors.ChatRestricted, errors.UserBannedInChannel):
        await app.leave_chat(dialog.chat.id, delete=True)
    except (errors.ChatRestricted, errors.UserMuteedInChannel):
        await app.leave_chat(dialog.chat.id, delete=True)
    except Exception as error:
        pass
async def bannerG_handler():
    if not (Adgs[0] == "on"):
        return

    async for dialog in app.get_dialogs():
        if dialog.chat.type in [ChatType.SUPERGROUP, ChatType.GROUP]:
            try:
                await app.copy_message(chat_id=dialog.chat.id, from_chat_id=AdCHIDG[0],
                                          message_id=AdIDG[0])
                await asyncio.sleep(20)
            except (errors.FloodWait, errors.ChatWriteForbidden, errors.PeerIdInvalid, errors.MessageNotModified):
                pass
            except (errors.ChatRestricted, errors.UserBannedInChannel):
                await app.leave_chat(dialog.chat.id, delete=True)
            except (errors.ChatRestricted, errors.UserMuteedInChannel):
                await app.leave_chat(dialog.chat.id, delete=True)
            except Exception as error:
                pass
# end Group Robot
# start Panel Admin
@app.on_message()
async def user_message_handler(client, message):
    try:
        if message.from_user.id in Admins:
          if match(r'^(Send to pv|ارسال به پیوی)$', message.text, IGNORECASE):
            pvBanner = message.reply_to_message
            await sendtopv(client, message, pvBanner)
            await message.reply_text("تموم شد !")
          elif match(r'^(Send to group|ارسال به گروه)$', message.text, IGNORECASE):
            GAPBanner = message.reply_to_message
            await sendtogroup(client, message, GAPBanner)
            await message.reply_text("تموم شد !")
          elif match(r'^(Set ad time group|تنظیم زمان ارسال به گروه)$', message.text, IGNORECASE):
              timest = message.reply_to_message.text
              if int(timest) >= 5:
               await message.reply_text(f'زمان ما بین هر تبلیغ در گروه به (**{timest}**) دقیقه تغییر کرد!')
               scheduler.remove_all_jobs()
               Timerg.clear()
               Timerg.append(int(timest))
               scheduler.add_job(bannerG_handler, 'interval', minutes=Timerg[0])
               scheduler.add_job(bannerP_handler, 'interval', minutes=Timerp[0])
              else:
                  await message.reply_text('ᴇʀʀᴏʀ : \nلطفا عددی بزرگتر از 4 وارد کنید!') 
          elif match(r'^(Set ad time pv|تنظیم زمان ارسال به پیوی)$', message.text, IGNORECASE):
              timest = message.reply_to_message.text
              if int(timest) >= 5:
               await message.reply_text(f'زمان ما بین هر تبلیغ در پیوی به (**{timest} **) دقیقه تغییر کرد!')
               scheduler.remove_all_jobs()
               Timerp.clear()
               Timerp.append(int(timest))
               scheduler.add_job(bannerP_handler, 'interval', minutes=Timerp[0])
               scheduler.add_job(bannerG_handler, 'interval', minutes=Timerg[0])
              else:
                  await message.reply_text('ᴇʀʀᴏʀ : \nلطفا عددی بزرگتر از 4 وارد کنید!')
          elif match(r'^(Add admin|افزودن ادمین)$', message.text, IGNORECASE):
                admin_id = message.reply_to_message.text
                if checkId(admin_id):
                    Admins.append(int(admin_id))
                    await message.reply_text('ادمین با موفقیت اضافه شد.')
                else:
                    await message.reply_text('آیدی عددی مورد نظر یافت نشد!!!')

          elif match(r'^(Remove Admin|حذف ادمین)$', message.text, IGNORECASE):
                admin_id = message.reply_to_message.text
                if checkId(admin_id):
                    Admins.remove(int(admin_id))
                    await message.reply_text('ادمین با موفقیت حذف شد.')
                else:
                    await message.reply_text('آیدی عددی مورد نظر یافت نشد!!!')
          elif match(r'^(Join|پیوستن)$', message.text, IGNORECASE):
                    await message.reply_text('لطفا صبر کنید(لینک هایی که در آن نیازمند ارسال ریکوست است در ربات پشتیبانی نمی شود)...')
                    matches = findall("https?://(?:t\.me|telegram\.me)/\S+", message.reply_to_message.text)
                    for link in matches:

                        try:
                            await client.join_chat(link)
                            await asyncio.sleep(100)
                        except:
                            pass
                    await message.reply_text('ربات در تمامی چت ها عضو شد!')
          elif match(r'^(Add member|افزودن به گروه)$', message.text, IGNORECASE):
            await addgroup(client, idgap=message.chat.id)
            await message.reply_text("تموم شد !")
          elif match(r'^(Ping|ربات)$', message.text, IGNORECASE):
                await message.reply_text("من فعالم !!")
          elif match(r'^(Groups list|لیست گروه ها)$', message.text, IGNORECASE):
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
                await message.reply_text("تموم شد !")
          elif match(r'^(Bot number|شماره ربات)$', message.text, IGNORECASE):
                await message.delete()
                me = await app.get_me()
                await message.reply_contact(me.phone_number, me.first_name)
          elif match(r'^(Adgroup on|تبلیغ در گروه روشن)$', message.text, IGNORECASE):
                if Adgs[0] == 'off':
                    await message.reply_text('تبلیغ در گروه فعال شد!')
                    Adgs.clear()
                    Adgs.append("on")
                else:
                    await message.reply_text('تبلیغ در گروه از قبل فعال بوده است!')
          elif match(r'^(Adgroup off|تبلیغ در گروه خاموش)$', message.text, IGNORECASE):
                if Adgs[0] == 'on':
                    await message.reply_text('تبلیغ در گروه خاموش شد!')
                    Adgs.clear()
                    Adgs.append("off")
                else:
                    await message.reply_text('تبلیغ در گروه از قبل خاموش بوده است!')
          elif match(r'^(Adpv on|تبلیغ در پیوی روشن)$', message.text, IGNORECASE):
                if Adps[0] == 'off':
                    await message.reply_text('تبلیغ در پیوی روشن شد!')
                    Adps.clear()
                    Adps.append("on")
                else:
                    await message.reply_text('تبلیغ در پیوی از قبل روشن بوده است!')
          elif match(r'^(Adpv off|تبلیغ در پیوی خاموش)$', message.text, IGNORECASE):
                if Adps[0] == 'on':
                    await message.reply_text('تبلیغ در پیوی خاموش شد!')
                    Adps.clear()
                    Adps.append("off")
                else:
                    await message.reply_text('تبلیغ درپیوی از قبل خاموش بوده است!')
          elif match(r'^(Vad group|مشاهده تبلیغ گروه)$', message.text, IGNORECASE):
              if AdIDG:
                await message.reply_text('تبلیغ کنونی گروه:')
                await client.copy_message(message.chat.id, AdCHIDG[0], AdIDG[0])
              else:
                await app.send_message(message.chat.id,
                                              'ᴇʀʀᴏʀ : \nمنن تبلیغ در گروه یافت نشد!')
          elif match(r'^(Vad pv|مشاهده تبلیغ پیوی)$', message.text, IGNORECASE):
              if AdIDP:
                await message.reply_text('تبلیغ کنونی پیوی:')
                await client.copy_message(message.chat.id, AdCHIDP[0], AdIDP[0])
              else:
                    await app.send_message(message.chat.id,
                                              'ᴇʀʀᴏʀ : \nمتن تبلیغ در پیوی یافت نشد!')
          elif message.reply_to_message:
            if match(r'^(Set ad group|تنظیم تبلیغ گروه)$', message.text, IGNORECASE):
              await app.send_message(message.chat.id,
                                              "متن (گروه) ذخیره شد.\n\n- لطفا پیام تبلیغ را پاک نکنید! ")
              AdCHIDG.clear()
              AdIDG.clear()
              AdCHIDG.append(message.chat.id)
              AdIDG.append(message.reply_to_message.id)
            elif match(r'^(Set ad pv|تنظیم تبلیغ پیوی)$', message.text, IGNORECASE):
              await app.send_message(message.chat.id,
                                              "متن (پیوی) ذخیره شد.\n\n- لطفا پیام تبلیغ را پاک نکنید! ")
              AdCHIDP.clear()
              AdIDP.clear()
              AdCHIDP.append(message.chat.id)
              AdIDP.append(message.reply_to_message.id)   
          elif match(r'^(Help|راهنما)$', message.text, IGNORECASE):
                await message.reply_text(text="""                          
📚 راهنما تبچی HxD :

◄ مشاهده وضعیت ربات 
                                                                             
❯❯ دستورات فارسی:
❯ `ربات`
❯ `وضعیت`
❯ `شماره ربات`
❯ `لیست گروه ها`
❯ `مشاهده تبلیغ گروه`
❯ `مشاهده تبلیغ پیوی`
                                         
❯❯ دستورات انگلیسی:
❯ `Ping`
❯ `Stats`
❯ `Bot number`
❯ `Groups list`
❯ `Vad group`
❯ `Vad pv`
                                         
◄ دستورات خاموش | روشن کردن 
                                                                             
❯❯ دستورات فارسی:
❯ `تبلیغ در گروه روشن`
❯ `تبلیغ در گروه خاموش`
❯ `تبلیغ در پیوی روشن`
❯ `تبلیغ در پیوی خاموش`
                                         
❯❯ دستورات انگلیسی:
❯ `Adgroup on`
❯ `Adgroup off`
❯ `Adpv on`
❯ `Adpv off`
                                         
◄ دستورات مستقیم
                                                                             
❯❯ دستورات فارسی:
❯ `ارسال به گروه` (ریپلای)
❯ `ارسال به پیوی` (ریپلای)                                       
                                         
❯❯ دستورات انگلیسی:
❯ `Send to group` (Reply)
❯ `Send to pv` (Reply)
                                                          
◄ دستورات تنظیمی و دیگر قابلیت ها
                                                                             
❯❯ دستورات فارسی:
❯ `تنظیم زمان ارسال به گروه` (ریپلای) (عدد به دقیقه)
❯ `تنظیم زمان ارسال به پیوی` (ریپلای) (عدد به دقیقه)
❯ `تنظیم تبلیغ گروه` (ریپلای)
❯ `تنظیم تبلیغ پیوی` (ریپلای)
❯ `پیوستن` (ریپلای) (لیست لینک ها)
❯ `افزودن به گروه` (افزودن تمام کاربران پیوی به گروه جاری)
❯ `افزودن ادمین` (ریپلای) (آیدی عددی کاربر)
❯ `حذف ادمین` (ریپلای) (آیدی عددی کاربر)
                                         
❯❯ دستورات انگلیسی:
❯ `Set ad time group` (Reply) (Numbers in Minutes)
❯ `Set ad time pv` (Reply) (Numbers in Minutes)
❯ `Set ad group` (Reply)
❯ `Set ad pv` (Reply)
❯ `Join` (Reply) (List of Links)
❯ `Add member` (Add all Pv users to the current group)
❯ `Add admin` (Reply) (ID)
❯ `Remove admin` (Reply) (ID)
""")
    
    
          elif match(r'^(Stats|وضعیت)$', message.text, IGNORECASE):
                await message.reply_text( f"🎛 وضعیت تبچی HxD :\n\n\n◄ تبلیغ در گروه : {Adgs[0]}\n\n◄ تبلیغ در پیوی : {Adps[0]}\n\n◄ لیست ادمین ها : {Admins}\n\n◄ تایم ارسال به گروه : {Timerg[0]}\n\n◄ تایم ارسال به پیوی : {Timerp[0]}")
    except Exception as error:
        pass

scheduler.start()
app.run()