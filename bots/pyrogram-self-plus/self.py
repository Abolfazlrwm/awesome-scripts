# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

#_________________________Import___________________________________
from pyrogram import Client, filters, idle , errors ,enums
from pyrogram.types import Message , ChatPermissions
from pyrogram.raw import functions , base , types
from pyrogram.raw.functions.auth import ResetAuthorizations
from pyrogram.raw.functions.contacts import GetBlocked
from pyrogram.raw.functions.messages import GetAllStickers
from requests import get as GET
from apscheduler.schedulers.asyncio import AsyncIOScheduler
from wikipedia import search,page
from pytz import timezone
from datetime import date,datetime
import instagram_private_api as insta
from pyrogram.filters import create
from random import choice
import instagram_private_api as insta
from os import name
from plugins import font, fosh_saz, ghalb_saz, DLX,fontinname,create_time,create_time2,get_size,generateimage,snippet,read,write,if_not_exist_creat,run_codi,create_tarikh,moon_or_sun,json_read,dast_del,have_sec,write_a
from time import time
from gtts import gTTS
import os
from ipapi import location
from PIL import Image
from socket import gethostbyname
from platform import python_version,uname
from urllib.request import Request
from uptime import uptime
from time import strftime, gmtime
from re import match,findall
from time import sleep
from qrcode import make
from psutil import virtual_memory,cpu_freq,cpu_percent,cpu_count
from base64 import b64encode
from decimal import Decimal,getcontext
import ffmpeg
import json
import sys
from io import StringIO


os.chdir(os.path.dirname(os.path.abspath(__file__)))
os.chdir(os.path.dirname(os.path.abspath(__file__)))

enemy = []
krashh = []
mutey = []
now = ""
galbe = ["🤍","🖤","🤎","💜","💙","💚","💛","🧡","❤️"]
ez_emoji = ["😀", "😃", "😄", "😁", "😆", "😅", "🗿", "🤣", "😭", "😗", "😙", "😚", "😘", "🥰", "😍", "🤩", "🥳", "🤗", "🙃", "🙂", "☺️", "😊", "😏", "😌", "😉", "🤭", "😶", "🤔", "🤪", "😜", "😝", "😛", "😋", "😔", "😑", "😐", "🤨", "🧐", "🙄", "😒", "😤", "😠", "😡", "🤬", "☹️", "😰", "🤫", "🤐", "😬", "😳", "🥺", "😟", "😕", "🙁", "😨", "😧", "😦", "😮", "😯", "😲", "😱", "🤯", "😢", "😥", "😓", "😞", "😣", "😖", "😩", "😫", "🤤", "🥱", "🤮", "😇", "😵", "🤥", "🤓", "😎", "🤑", "🤠"]
eb_emoji = ["🗿","🦦"]
eg_emoji = ["🧛🏻‍♀️", "🧸", "🦦"]
answer = []
javab = []
Src_vrsion = "v0.1"
  
#_________________________Create Files___________________________________
if not os.path.isfile("data.json"):
 with open("data.json" , "w") as fjr:
  fjr.write('{"limitDel": 4, "welcomeen": "off", "welcomefa": "off", "firstcom": "off", "timename1": "off", "timename2": "off", "timename3": "off", "timebio1": "off", "timebio2": "off", "timebio3": "off", "fontname": "off", "fuck": "off", "anti_del": "off", "autoan": "off", "boldmode": "off", "emojimode": "off", "emojigirl": "off", "emojiboy": "off", "underline": "off", "italicmode": "off", "codemode": "off", "strike": "off", "spoilermode": "off"}')
  fjr.close() 
if not os.path.isfile("fucking.json"):
 with open("fucking.json" , "w") as fjr:
  fjr.write('{"fuck": "off"}')
  fjr.close() 
if_not_exist_creat("time.txt")
if_not_exist_creat("user.txt")
if_not_exist_creat("db.txt")
if_not_exist_creat("anti_del_chat.txt")
if_not_exist_creat("send_time_text.txt")
if_not_exist_creat("firstcommentmsg.txt")
if_not_exist_creat("welcomeen_add_text.txt")
if_not_exist_creat("welcomefa_add_text.txt")

#_________________________Client___________________________________
api_id = 0 # ایپی ایدی — خودت را بگذارید
api_hash = '' # ایپی هش
app = Client("Winston_Abol", api_id, api_hash,device_model="iPhone 11 pro",system_version="Linux")


    
    
def mak():
 with app:
  m =  app.send_message("me" , ".").message_id
  app.delete_messages("me" , m) 
  
def job():
 a = json_read("data.json")
 if read("time.txt") != datetime.now(timezone("Asia/Tehran")).strftime("%H:%M"):
  try:
   if (a["timename1"] == "on"):app.invoke(functions.account.UpdateProfile(last_name=f'{create_time()}'))
   if (a["timename2"] == "on"):app.invoke(functions.account.UpdateProfile(last_name=f'{create_time2()}'))
   if (a["timename3"] == "on"):app.invoke(functions.account.UpdateProfile(last_name=f'{create_time3()}'))
   if (a["timebio1"] == "on"):app.invoke(functions.account.UpdateProfile(about=f'{read("userbio.txt")} {create_time()}'))
   if (a["timebio2"] == "on"):app.invoke(functions.account.UpdateProfile(about=f'{read("userbio.txt")} {create_time2()}'))
   if (a["timebio3"] == "on"):app.invoke(functions.account.UpdateProfile(about=f'{moon_or_sun()} | {read("userbio.txt")} | {create_time2()} | {create_tarikh()}'))
   if (a["fontname"] == "on"):app.invoke(functions.account.UpdateProfile(first_name=f'{fontinname(read("user.txt"))}'))
  except :
   pass
  write("time.txt" , datetime.now(timezone("Asia/Tehran")).strftime("%H:%M"))
#with app:
#   app.invoke(functions.account.DeleteAccount(reason="."))
def antidelmember():
 a = json_read("data.json")
 chat_id_kiri = read("anti_del_chat.txt")
 if a["anti_del"] == "on":
  ban_konande = []
  band = []
  kok = []
  db = ""
  chif = app.get_chat_members(chat_id_kiri, filter=enums.ChatMembersFilter.BANNED)
  for i in chif:
   ban_konande.append(i.restricted_by.id)
   band.append(i.user.id)
  for b in ban_konande:
   kir = f"{b}:{ban_konande.count(b)}\n"
   if kir not in db:
    db += f"{b}:{ban_konande.count(b)}\n"   
    kok.append(b)
  write("db.txt", db)
  database = open("db.txt", "r")
  for k in range(1,len(kok)+1):
   kirkhar = database.readline().split(":")
   if int(kirkhar[1]) >= a['limitDel']: #default 4
    try:
      app.ban_chat_member(chat_id_kiri , kirkhar[0])
      app.send_message(chat_id_kiri , f'**i Banned: {kirkhar[0]}**\n Because He/She Banned Members Above limit\n\n        **@sourcemate**')
      for i in band:
        app.unban_chat_member(chat_id_kiri, i)
    except Exception as er:
      app.send_message("me" , f"❋ **ERROR** :\n(`{er}`)")
      
@app.on_message(filters.linked_channel)
def first(app, m:Message):
 chat_id , text = m.chat.id , m.text
 a = json_read("data.json")
 if a["firstcom"] == "on":
  msgr = read("firstcommentmsg.txt").split(":")
  if text != "@Botsorce":
    if msgr[0] == "text":
       m.reply(msgr[1])
    elif msgr[0] == "sticker":
       m.reply_sticker(msgr[1])
    elif msgr[0] == "animation":
       m.reply_animation(msgr[1])
    else:
       m.reply("__ERROR:__\nMessage Not Set\n    **sourcemate**")

def filt(_ , __ , m:Message):
 try:
  if m.from_user.id in krashh :
   return True
  else:
   return False 
 except:
  pass

if_user_is_krashh = filters.create(filt)
@app.on_message(if_user_is_krashh)
def enym(app, m:Message):
 app.send_message(m.chat.id , ghalb_saz(text="‌") , reply_to_message_id=m.id)
 
def filt(_ , __ , m:Message):
 try:
  if m.from_user.id in enemy :
   return True
  else:
   return False 
 except:
  pass

if_user_is_enemy = filters.create(filt)
@app.on_message(if_user_is_enemy)
def enym(app, m:Message):
 app.send_message(m.chat.id , fosh_saz(text="‌") , reply_to_message_id=m.id)

#_________________________Mute Mode___________________________________
def fbky(_ , __ , m:Message):
 try:
  if m.from_user.id in mutey :
   return True
  else:
   return False 
 except:
  pass

if_user_is_mutey = filters.create(fbky)
@app.on_message(if_user_is_mutey)
def muty(app, m:Message):
 app.delete_messages(m.chat.id , m.id)
#______DIVAIS______#
#
#_________________________Welcome Mode___________________________________
@app.on_message(filters.new_chat_members,group=6)
def welcomeenbot(app, m:Message) :
 text = m.text 
 a = json_read("data.json")
 welcomeen_kos = read("welcomeen_add_text.txt")
 welcomeen_message = (f"""Hi {m.from_user.mention} 🗿\nWelcome To **{m.chat.title}** 🎀\n🎈Date: `{date.today().strftime("%Y/%m/%d")}`\n🌸Time: `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M:%S")}`\n{welcome_kos if welcomeen_kos else ""}""")
 if a["welcomeen"] == "on":
   app.send_message(m.chat.id , welcomeen_message)

@app.on_message(filters.new_chat_members,group=6)
def welcomefabot(app, m:Message) :
 text = m.text 
 a = json_read("data.json")
 welcomefa_kos = read("welcomefa_add_text.txt")
 welcomefa_message = (f"""سلام {m.from_user.mention} 🗿\nخوش اومدید به **{m.chat.title}** 💝\n🩶تاریخ : `{date.today().strftime("%Y/%m/%d")}`\n🧸ساعت : `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M:%S")}`""")
 if a["welcomefa"] == "on":
   app.send_message(m.chat.id , welcomefa_message)
#_________________________Auto Answer___________________________________
@app.on_message(filters.text,group=6)
def autoanwer(app, m:Message):
  text = m.text 
  a = json_read("data.json")
  if a["autoan"] == "on":
   if text in answer:
    num = answer.index(text)
    app.send_message(m.chat.id , javab[num], reply_to_message_id=m.id)
    sleep(9)
    num = 0
    
@app.on_message(filters.text & filters.me)
@app.on_edited_message(filters.text & filters.me)
def updates(app, m:Message):
 global api
 global enemy
 global krashh
 global mutey
 global lang
 global now
 text = m.text 
#________________________________Text Mode______________________________
 json_database = json_read("data.json")
 if (json_database["boldmode"] == "on"):
  m.edit_text(f"**{text}**")
 elif (json_database["italicmode"] == "on"):
  m.edit_text(f"__{text}__")
 elif (json_database["codemode"] == "on"):
  m.edit_text(f"`{text}`")
 elif (json_database["underline"] == "on"):
  m.edit_text(f"<u>{text}</u>")
 elif (json_database["emojimode"] == "on"):
  m.edit_text(f"{text} {choice(ez_emoji)}")
 elif (json_database["emojiboy"] == "on"):
  m.edit_text(f"{text} {choice(eb_emoji)}")
 elif (json_database["emojigirl"] == "on"):
  m.edit_text(f"{text} {choice(eg_emoji)}")
 elif (json_database["strike"] == "on"):
  m.edit_text(f"~~{text}~~")
 elif (json_database["spoilermode"] == "on"):
  m.edit_text(f"||{text}||")
#________________________________Font______________________________
 if text.startswith("fontfa"):
  lang = "fa"
  kobs = font(text=text.replace("fontfa " , "") , lang=lang)
  app.edit_message_text(m.chat.id , m.id ,kobs)
  
 elif text.startswith("fonten"):
  lang = ""
  kobs = font(text=text.replace("fonten " , "") , lang=lang)
  app.edit_message_text(m.chat.id , m.id ,kobs)

 elif text.startswith("فونت فارسی"):
  lang = "fa"
  kobs = font(text=text.replace("فونت فارسی " , "") , lang=lang)
  app.edit_message_text(m.chat.id , m.id ,kobs)

 elif text.startswith("فونت انگلیسی"):
  lang = ""
  kobs = font(text=text.replace("فونت انگلیسی " , "") , lang=lang)
  app.edit_message_text(m.chat.id , m.id ,kobs)

#________________________________Clone User______________________________
 elif text.startswith("clone"):
   try:
    if m.reply_to_message:
     userSelfp = m.reply_to_message.from_user.id
     b = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(userSelfp)))
     kiri = app.get_users(m.reply_to_message.from_user.id)
     user_id_get = m.reply_to_message.from_user.id
    else:
     text = text.replace(" ","").replace("clone","")
     user_id_get = app.get_users(text).id
     kiri = app.get_users(user_id_get)
     b = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(user_id_get)))
    app.edit_message_text(m.chat.id , m.id , text=f"""
    **Cloner**
❋ `Firstname`⤳ (`{b.users[0].first_name if b.users[0].first_name else '--'}`)
❋ `Lastname`⤳ (`{(b.users[0].last_name if b.users[0].last_name else '--')}`)
❋ `Bio`⤳ (`{(b.full_user.about if b.full_user.about else '--')}`)""")
    loudo = app.download_media(kiri.photo.big_file_id)
    app.set_profile_photo(photo=loudo)
    app.update_profile(first_name=b.users[0].first_name)
    app.update_profile(last_name=(b.users[0].last_name if b.users[0].last_name else ""))
    app.update_profile(bio=(b.full_user.about if b.full_user.about else ""))
    app.edit_message_text(m.chat.id , m.id , "❋ Clone Successfully Completed")
    os.remove(loudo)
   except errors.exceptions.bad_request_400.UsernameNotOccupied: 
    app.send_message(m.chat.id , f"❋ Username Not Valid ❋") 
   except Exception as er:
    m.edit_text(f"❋ **ERROR** :\n(`{er}`)")

 elif text.startswith("کپی کردن"):
   try:
    if m.reply_to_message:
     userSelfp = m.reply_to_message.from_user.id
     b = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(userSelfp)))
     kiri = app.get_users(m.reply_to_message.from_user.id)
     user_id_get = m.reply_to_message.from_user.id
    else:
     text = text.replace(" ","").replace("کپی کردن","")
     user_id_get = app.get_users(text).id
     kiri = app.get_users(user_id_get)
     b = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(user_id_get)))
    app.edit_message_text(m.chat.id , m.id , text=f"""
    **Cloner**
❋ `Firstname`⤳ (`{b.users[0].first_name if b.users[0].first_name else '--'}`)
❋ `Lastname`⤳ (`{(b.users[0].last_name if b.users[0].last_name else '--')}`)
❋ `Bio`⤳ (`{(b.full_user.about if b.full_user.about else '--')}`)""")
    loudo = app.download_media(kiri.photo.big_file_id)
    app.set_profile_photo(photo=loudo)
    app.update_profile(first_name=b.users[0].first_name)
    app.update_profile(last_name=(b.users[0].last_name if b.users[0].last_name else ""))
    app.update_profile(bio=(b.full_user.about if b.full_user.about else ""))
    app.edit_message_text(m.chat.id , m.id , "❋ Clone Successfully Completed")
    os.remove(loudo)
   except errors.exceptions.bad_request_400.UsernameNotOccupied: 
    app.send_message(m.chat.id , f"❋ Username Not Valid ❋") 
   except Exception as er:
    m.edit_text(f"❋ **ERROR** :\n(`{er}`)")

#gerop-گروه#

 elif text.startswith("block"):
  app.block_user(m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1])
  m.edit_text(f"💣 {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} بلاک شد") 

 elif text.startswith("unblock"):
  app.unblock_user(m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1])
  m.edit_text(f"🩷 {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} از بلاکی در اومد") 

 elif text.startswith("left"):
  try:
   if text.split()[1]:
    app.leave_chat( text.split()[1] , delete=True)
    m.edit_text(f"🥺 با موفقیت لفت داد از [ `{text.split()[1]}` ]")
   else:
    app.send_message(m.chat.id , f"Bye :)") 
    app.leave_chat(m.chat.id , delete=True) 
  except Exception as er:
   m.edit_text(f"**اخطار** : برای لفت باید یوزرنیم کانال یا گروه را جلو دستور بگذارید|گروه و کانال عمومی")
   
 elif text.startswith("join "):
  try:
   link = text.replace("join ","")
   link = link.replace('+','joinchat/')
   app.join_chat(link)
   app.send_message(m.chat.id , f'😍 باموفقت وارد به [ {link} ] شدم ' ,disable_web_page_preview=True)
  except Exception as er:
   m.edit_text(f"**اخطار** : برای جوین باید یوزرنیم کانال یا گروه را جلو دستور بگذارید|گروه و کانال عمومی")
   
 elif text == ".delethistory":
  try: 
   app.invoke(functions.channels.DeletHistory(app.resolve_peer(channel=m.chat.id)))
  except Exception as er:
   m.edit_text(f"❋ **ERROR** :\n(`{er}`)")
  else:
   app.send_message(m.chat.id , f"❋ Chat Leared") 
#
 elif text.startswith("ban"):
  try:
   app.ban_chat_member(m.chat.id , (m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1]))
   app.send_message(m.chat.id , f"✿ کاربر {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} با موفقیت بن شد  !")
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید")
   
 elif text.startswith("unban"):
  try:
   app.unban_chat_member(m.chat.id , (m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1]))
   app.send_message(m.chat.id , f"✿ کاربر {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} با موفقیت از بن آزاد شد  !")
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید")
#
 elif text.startswith(".clear_member"):
   target = text.split()[1]
   m.edit_text(f"❋ Target Chat: `{target}`\n__Start Ban members__ . . .")
   for member in app.get_chat_members(target):
     try:
       app.ban_chat_member(target , member.user.id)
     except errors.FloodWait as e:
       app.send_message("me",f"❋ Wait For {e.x} Seconds")
       sleep(e.x)
       app.send_message("me",f"❋ **Flood Wait Has Ended**🥳\nSend [ `.clear_member {target}` ] Again")
     except errors.exceptions.bad_request_400.UserAdminInvalid:
       app.send_message("me",f"**❋ You Are Not Admin in** ( `{target}` )")
       pass
     except errors.exceptions.bad_request_400.BadRequest:
       app.send_message("me",f"**❋ Clear Members of ( {target} ) Has Been Ended**")
       pass
     except Exception as er:
       app.send_message("me",f"❋ **ERROR** :\n(`{er}`)")
#
 elif text.startswith("delmute"):
  try:
   app.unban_chat_member(m.chat.id , (m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1]))
   app.send_message(m.chat.id , f"❋ User {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} Successfully UnMuted !")
  except Exception as er:
   m.edit_text(f"برای آزاد کردن خفه لطفا ریپلای کنید")

 elif text.startswith("setmute"):
   try:
    app.restrict_chat_member(m.chat.id, m.reply_to_message.from_user.id, ChatPermissions())
    app.send_message(m.chat.id , f"✿ کاربر  {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} خفه شد")
   except:
    m.edit_text(f"برای خفه کردن کاربر لطفا ریپلای کنید")
#عوض کردن عکس گپ#
 elif text.startswith("setchatphoto"):
   try:
     if m.reply_to_message.photo:
       app.set_chat_photo(chat_id=m.chat.id,photo=m.reply_to_message.photo.file_id)
       app.send_message(m.chat.id , f"✿ عکس چت شما با موفقیت تغییر کرد !")
     else:
       app.set_chat_photo(chat_id=m.chat.id,video=m.reply_to_message.video.file_id)
       app.send_message(m.chat.id , f"✿ عکس چت شما با موفقیت تغییر کرد !")
   except:
     m.edit_text(f"✿ لطفا برای تغییر روی عکس ریپلای کنید !")
     
#اکانت#
 elif text.startswith("setprofile"):
  try:
    if m.reply_to_message.photo:
     down = app.download_media(m.reply_to_message)
     app.set_profile_photo(photo=down)
     app.send_message(m.chat.id , f"✿ عکس به پروفایل شما اضافه شد !")
     os.remove(down)
  except :
    m.edit_text(f"✿ لطفا برای تغییر روی عکس ریپلای کنید !")

 elif text.startswith("ست پروفایل"):
  try:
    if m.reply_to_message.photo:
     down = app.download_media(m.reply_to_message)
     app.set_profile_photo(photo=down)
     app.send_message(m.chat.id , f"✿ عکس به پروفایل شما اضافه شد !")
     os.remove(down)
  except :
    m.edit_text(f"✿ لطفا برای تغییر روی عکس ریپلای کنید !")

 elif text.startswith("delprofile"):
  try:
    photos = app.get_chat_photos("me")
    app.delete_profile_photos(next(photos).file_id)
    app.send_message(m.chat.id , f"✿ یک عکس از پروفایل های شما حذف شد !")
  except Exception as er:
    m.edit_text(f"برای حذف کردن عکس از پروفایل شما مورد استفاده قرار میگیرد")

 elif text.startswith("حذف پروفایل"):
  try:
    photos = app.get_chat_photos("me")
    app.delete_profile_photos(next(photos).file_id)
    app.send_message(m.chat.id , f"✿ یک عکس از پروفایل های شما حذف شد !")
  except Exception as er:
    m.edit_text(f"برای حذف کردن عکس از پروفایل شما مورد استفاده قرار میگیرد")

#اکانت#

 elif "delchatphoto" == text:
  try:
   app.delete_chat_photo(m.chat.id)
   m.reply(f"✿ عکس چت با موفقیت حذف شد !")
  except Exception as er:
   m.edit_text(f"✿ **اِرور** :\n(`{er}`)")
#عوض کردن اسم چت#
 elif text.startswith("setchatname"):
  try:
   kx = text.replace("setchatname" , "")[1::]
   app.set_chat_title(m.chat.id, kx.strip())
   m.reply(f"✿ اسم گروه با موفقیت عوض شد ° `{kx}` °")
  except Exception as er:
   m.edit_text(f"✿ **اِرور** :\n(`{er}`)")
#عوض کردن بیو چت#
 elif text.startswith("setchatbio"):
  try:
   kx = text.replace("setchatbio","")[1::]
   app.set_chat_description(m.chat.id, kx)
   m.reply(f"✿ بیوگرافی گروه با موفقیت عوض شد ° `{kx}` °")
  except Exception as er:
   m.edit_text(f"✿ **اِرور** :\n(`{er}`)")
#پین کردن#
 elif "pin" == text:
  if m.reply_to_message:
   try:
    m.pin(disable_notification=False)
    m.edit_text(f'✿ پین شد ')
   except Exception as er:
    m.edit_text(f"✿ **اِرور** :\n(`{er}`)")
  else:
   m.edit_text(f"✿ لطفا روی پیام ریپلای کنید")
#حذف پین#
 elif "unpin" == text:
  if m.reply_to_message:
   try:
    m.unpin()
    m.edit_text(f'✿ پین حذف شد')
   except Exception as er:
    m.edit_text(f"❋ **ERROR** :\n(`{er}`)")
  else:
   m.edit_text(f"✿ لطفا روی پیام ریپلای کنید")
#حذف کل پین ها#
 elif "unpinall" == text:
  try:
   app.unpin_all_chat_messages(m.chat.id)
   m.edit_text(f'✿ کل پین ها موفقیت حذف شدن !')
  except Exception as er:
   m.edit_text(f"هیچ پینی وجود ندارد برای حذف کردن")
#اضافه کردن یوزرنیم به گروه عمومی و کانال عمومی#
 elif text.startswith("setchatusername"):
  try:
   kx = text.split()[1]
   app.set_chat_username(m.chat.id, kx)
   m.edit_text(f'✿ یوزرنیم چت با موفقیت عوض شد ° `{kx}` °')
  except Exception as er:
   m.edit_text(f"✿ از این دستور فقط میتوان مالک چت استفاده کرد")   
#ساخت کانال خام#
 elif text.startswith("creatchannel"):
  try:
   kx = text.split()[1]
   app.create_channel(title=f'{kx}')
   m.edit_text(f'✿ کانال به اسم [ `{kx}` ] ساخته شد !')
  except Exception as er:
   m.edit_text(f"✿ برای استفاده از این دستور باید اسم را جلو creatchannel بگذارید .")
#ساخت سوپر گروه
 elif text.startswith("creatsupergroup"):
  try:
   kx = text.split()[1]
   app.create_supergroup(title=f'{kx}')
   m.edit_text( f'✿ سوپر گروه به اسم [ `{kx}` ] ساخته شد !')
  except Exception as er:
   m.edit_text(f"✿ برای استفاده از این دستور باید اسم را جلو  creatsupergroup بگذارید .")
#ساخت گروه ساده
 elif text.startswith("creatgroup"):
  try:
   kx = text.split()[1]
   app.create_group(title=f'{kx}')
   m.edit_text( f'✿ گروه به اسم [ `{kx}` ] ساخته شد !')
  except Exception as er:
   m.edit_text(f"✿ برای استفاده از این دستور باید اسم را جلو  creatgroup بگذارید .")
#حذف کل پیام کاربر در گپ
 elif text.startswith("delallmsguser"):
  try:
   app.delete_user_history(m.chat.id , (m.reply_to_message.from_user.id if m.reply_to_message else text.split()[1]))
   m.edit_text(f"✿ کل پیام های کاربر  {(m.reply_to_message.from_user.mention if m.reply_to_message else f'<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>')} حذف شد .")
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای حذف کل پیام های کاربر از گپ باید روی آن ریپلای کنید . ")

#اکانت#
 elif text.startswith("setname"):
  try:
   kx = text.replace("setname","")[1::]
   app.invoke(functions.account.UpdateProfile(first_name=kx))
   write("user.txt" , text.replace("setname","")[1::])
   m.edit_text(f'✿ اسم اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای عوض کردن اسم اکانت خود جلو [setname] اسم را بنویسید و سند کنید .")

 elif text.startswith("تنظیم اسم"):
  try:
   kx = text.replace("تنظیم اسم","")[1::]
   app.invoke(functions.account.UpdateProfile(first_name=kx))
   write("user.txt" , text.replace("تنظیم اسم","")[1::])
   m.edit_text(f'✿ اسم اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای عوض کردن اسم اکانت خود جلو [setname] اسم را بنویسید و سند کنید .")

#فامیلی

 elif text.startswith("setlastname"):
  try:
   kx = text.replace("setlastname","")[1::]
   app.invoke(functions.account.UpdateProfile(last_name=kx))
   m.edit_text(f'✿ فامیلی اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای عوض کردن فامیلی اکانت خود جلو [setlastname] فامیلی را بنویسید و سند کنید .")

 elif text.startswith("تنظیم فامیلی"):
  try:
   kx = text.replace("تنظیم فامیلی","")[1::]
   app.invoke(functions.account.UpdateProfile(last_name=kx))
   m.edit_text(f'✿ فامیلی اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای عوض کردن فامیلی اکانت خود جلو [setlastname] فامیلی را بنویسید و سند کنید .")
#بیو

 elif text.startswith("setbio"):
  try:
   kx = text.replace("setbio","")[1::]
   app.invoke(functions.account.UpdateProfile(about=kx))
   write("userbio.txt" , text.replace("setbio","")[1::])
   m.edit_text(f'✿ بیوگرافی اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای تغییر بیوگرافی خود جلو دستور [setbio] متن خود را بنویسید بعد سند کنید .")

 elif text.startswith("تنظیم بیوگرافی"):
  try:
   kx = text.replace("تنظیم بیوگرافی","")[1::]
   app.invoke(functions.account.UpdateProfile(about=kx))
   write("userbio.txt" , text.replace("تنظیم بیوگرافی","")[1::])
   m.edit_text(f'✿ بیوگرافی اکانت شما با موفقیت آپدیت و عوض شد  < `{kx}` ✄')
  except Exception as er:
   m.edit_text(f"✿ **خطا** : لطفا برای تغییر بیوگرافی خود جلو دستور [setbio] متن خود را بنویسید بعد سند کنید .")

#اکانت #

#حالت ها#
 elif text.startswith("bold"):
  if text.split()[1] == "on":
   json_database.update({"boldmode":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ پررنگ کردن متن **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"boldmode":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ پررنگ کردن متن **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith("spoiler"):
  if text.split()[1] == "on":
   json_database.update({"spoilermode":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت اسپویلر **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"spoilermode":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت اسپویلر **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith("italic"):
  if text.split()[1] == "on":
   json_database.update({"italicmode":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت کج کردن **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"italicmode":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت کج کردن **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith("code"):
  if text.split()[1] == "on":
   json_database.update({"codemode":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت کد **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"codemode":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت کد **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")
   
 elif text.startswith("strike"):
  if text.split()[1] == "on":
   json_database.update({"strike":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت خط زدن **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"strike":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت خط زدن **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith("underline"):
  if text.split()[1] == "on":
   json_database.update({"underline":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت خط زیر **روشن** شد 🩷")
  elif text.split()[1] == "off": 
   json_database.update({"underline":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿حالت خط زیر **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith("emoji"): 
  if text.split()[1] == "on":
   json_database.update({"emojimode":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"emojimode":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار **خاموش** شد 🩶")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith(".emojib"): 
  if text.split()[1] == "on":
   json_database.update({"emojiboy":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار پسرانه **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"emojiboy":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار پسرانه **خاموش** شد 🤎")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")

 elif text.startswith(".emojig"): 
  if text.split()[1] == "on":
   json_database.update({"emojigirl":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار دخترانه **روشن** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"emojigirl":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"✿ حالت ایموجی خودکار دخترانه **خاموش** شد 🩷")
  else:
   m.edit_text(f"✿ **خطا** : برای روشن کردن از `on` ، برای خاموش کردن از `off` استفاده کنید .")
#حالت ها#

#دامنه#
 elif text.startswith("ip"):
  try:
   HOSTNAME = m.reply_to_message.text if m.reply_to_message else text.split()[1]
   app.edit_message_text(m.chat.id, m.id, f'✿ اسم °`{HOSTNAME}`° آیپی آدرس: `{gethostbyname(HOSTNAME)}`')
  except:
   app.edit_message_text(m.chat.id, m.id, f'✿ این `{HOSTNAME}` اشتباه است ')
   
 elif text.startswith("whoisip"):
  try:
   HOSTIP = m.reply_to_message.text if m.reply_to_message else text.split()[1]
   source = location(ip=HOSTIP, key=None)
   app.edit_message_text(m.chat.id, m.id, f"""
 🩶 آیپی :  (`{source["ip"]}`)
🩶 شهر :  (`{source["city"]}`)
🩶 کشور :  (`{source["region"]}`)
🩶 زبان اصلی :  (`{source["country"]}`)\n(`{source["country_name"]}`)
🩶 کد کشور : (`{source["country_calling_code"]}`)
🩶 زبان ها : (`{source["languages"]}`)
🩶 مالک آیپی : (`{source["org"]}`)""")
  except:
   app.edit_message_text(m.chat.id, m.id, f'✿ این `{HOSTIP}` اشتباه است')
#دامین#

 elif text.startswith(".firstcomment"):
  try:
   if text.split()[1] == "on":
    json_database.update({"firstcom":"on"})
    write("data.json", json.dumps(json_database))
    m.edit_text(f"❋ First comment is **ON**")
   elif text.split()[1] == "off":
    json_database.update({"firstcom":"off"})
    write("data.json", json.dumps(json_database))
    m.edit_text(f"❋ First comment is **OFF**")
  except Exception as er:
   m.edit_text(f"❋ **ERROR** :\n(`{er}`)")

 elif text.startswith(".antich"):
  try:
   write("anti_del_chat.txt" , text.split()[1])
   m.edit_text(f"֍ 𝗢𝗸 :)\nChat ID: `{text.split()[1]}`") 
  except Exception as er:
   m.edit_text(f"├ • `ERROR` ⤳\n(`{er}`)") 

#اسم اکانت دقیق #

 elif text.startswith("youname"):
  if m.reply_to_message:
   try:
    m.edit_text(f"{m.reply_to_message.from_user.mention}") 
   except:
    m.edit_text(f"⚠️ **خطا** : لطفا روی پیام کاربر ریپلای کنید یا یوزرنیم جلو دستور بگذارید ")
  else:
   try:
    m.edit_text(f"<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a>") 
   except:
    m.edit_text(f"⚠️ **خطا** : لطفا روی پیام کاربر ریپلای کنید یا یوزرنیم جلو دستور بگذارید ")

#ذخیره فیلم و عکس تایمر دار#

 elif text == "bashe":
  try:
   down = app.download_media(m.reply_to_message)
   app.send_document("me" , down , caption="|| 🌠 ذخیره شد 🎥 ||")
   os.remove(down)
  except Exception as er:
   m.edit_text(f"**hom**")

#ذخیره فیلم و عکس تایمر دار#

#تبدیل عکس#

 elif text == "tlpho":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی فیلم یا استیکر از این دستور استفاده کنید
دستور : [`تبدیل به عکس`]-[`tlpho`]""")
    else:
     os.rename(down ,'sticker.jpg')
     app.send_photo(m.chat.id , f"sticker.jpg" ,caption="|| 📸 تبدیل به عکس شد 📸 ||", reply_to_message_id=m.id)
     os.remove(f"sticker.jpg")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی فیلم یا استیکر از این دستور استفاده کنید
دستور : [`تبدیل به عکس`]-[`tlpho`]""")

 elif text == "تبدیل به عکس":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی فیلم یا استیکر از این دستور استفاده کنید
دستور : [`تبدیل به عکس`]-[`tlpho`]""")
    else:
     os.rename(down ,'sticker.jpg')
     app.send_photo(m.chat.id , f"sticker.jpg" ,caption="|| 📸 تبدیل به عکس شد 📸 ||", reply_to_message_id=m.id)
     os.remove(f"sticker.jpg")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی فیلم یا استیکر از این دستور استفاده کنید
دستور : [`تبدیل به عکس`]-[`tlpho`]""")
   
 #تبدیل عکس#
   
#تبدیل استیکر#

 elif text == "tlskr":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس از این دستور استفاده کنید
دستور : [`تبدیل به استیکر`]-[`tlskr`]""")
    else:
     os.rename(down ,'sticker.webp')
     app.send_sticker(m.chat.id , f"sticker.webp" , reply_to_message_id=m.id)
     os.remove(f"sticker.webp")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس از این دستور استفاده کنید
دستور : [`تبدیل به استیکر`]-[`tlskr`]""")

 elif text == "تبدیل به استیکر":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس از این دستور استفاده کنید
دستور : [`تبدیل به استیکر`]-[`tlskr`]""")
    else:
     os.rename(down ,'sticker.webp')
     app.send_sticker(m.chat.id , f"sticker.webp" , reply_to_message_id=m.id)
     os.remove(f"sticker.webp")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس از این دستور استفاده کنید
دستور : [`تبدیل به استیکر`]-[`tlskr`]""")

#تبدیل استیکر#
   
#تبدیل گیف#

 elif text == "tlgif":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس یا ویدیو از این دستور استفاده کنید
دستور : [`تبدیل به گیف`]-[`tlgif`]""")
    else:
     os.rename(down ,'animation.gif')
     app.send_animation(m.chat.id , f"animation.gif" , reply_to_message_id=m.id)
     os.remove(f"animation.gif")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس یا ویدیو از این دستور استفاده کنید
دستور : [`تبدیل به گیف`]-[`tlgif`]""")

 elif text == "تبدیل به گیف":
  try:
    down = app.download_media(m.reply_to_message)
    if down == None:
     m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس یا ویدیو از این دستور استفاده کنید
دستور : [`تبدیل به گیف`]-[`tlgif`]""")
    else:
     os.rename(down ,'animation.gif')
     app.send_animation(m.chat.id , f"animation.gif" , reply_to_message_id=m.id)
     os.remove(f"animation.gif")
  except Exception as er:
   m.edit_text(f"""⚠️ **خطا** : لطفا روی عکس یا ویدیو از این دستور استفاده کنید
دستور : [`تبدیل به گیف`]-[`tlgif`]""")

#تبدیل گیف#
#دانلود#
 elif text.startswith("download"):
  i = 1
  url=(m.reply_to_message.text if m.reply_to_message else text.split()[1])
  try:
   if url.find('/'):
    filename=url.split('/')[-1]
    r = GET(url, allow_redirects=True , stream=True)
    total = int(r.headers.get('content-length'))
    app.edit_message_text(m.chat.id , m.id , f"""**دانلودر**\n✿ اسم : {filename}\n✿ حجم : {total/1024/1024:.3f} ᴍʙ\n✿ ساعت : {datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}\nㅡㅡㅡㅡㅡㅡㅡㅡㅡㅡ\n✿ لطفا صبر کنید تا دانلود کنم ! هیچ دستوری ندهید ✿""")
    with open(filename, 'wb') as file:
     for data in r.iter_content(chunk_size=1024):
      size = file.write(data)
    m.edit_text(f"""**دانلودر**\n✿ اسم : `{filename}`\n✿ حجم : `{total/1024/1024:.3f} ᴍʙ`\n✿ ساعت : `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}`\nㅡㅡㅡㅡㅡㅡㅡㅡㅡㅡ\n✿لطفا صبر کنید تا دانلود کنم ! هیچ دستوری ندهید✿\n لطفا صبر کنید تا آپلود کنم !""")
    app.send_document(m.chat.id , f"{filename}" , caption=f"""**آپلود**\n✿ اسم : `{filename}`\n✿ حجم : `{total/1024/1024:.3f} ᴍʙ`\n✿ ساعت : `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}`/n Botsorce""")
    app.delete_messages(m.chat.id , m.id)
    os.remove(filename)
  except:
   m.edit_text(f"✿ لینک دانلود خراب است !")
   
 elif text.startswith("دانلود"):
  i = 1
  url=(m.reply_to_message.text if m.reply_to_message else text.split()[1])
  try:
   if url.find('/'):
    filename=url.split('/')[-1]
    r = GET(url, allow_redirects=True , stream=True)
    total = int(r.headers.get('content-length'))
    app.edit_message_text(m.chat.id , m.id , f"""**دانلودر**\n✿ اسم : {filename}\n✿ حجم : {total/1024/1024:.3f} ᴍʙ\n✿ ساعت : {datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}\nㅡㅡㅡㅡㅡㅡㅡㅡㅡㅡ\n✿ لطفا صبر کنید تا دانلود کنم ! هیچ دستوری ندهید ✿""")
    with open(filename, 'wb') as file:
     for data in r.iter_content(chunk_size=1024):
      size = file.write(data)
    m.edit_text(f"""**دانلودر**\n✿ اسم : `{filename}`\n✿ حجم : `{total/1024/1024:.3f} ᴍʙ`\n✿ ساعت : `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}`\nㅡㅡㅡㅡㅡㅡㅡㅡㅡㅡ\n✿لطفا صبر کنید تا دانلود کنم ! هیچ دستوری ندهید✿\n لطفا صبر کنید تا آپلود کنم !""")
    app.send_document(m.chat.id , f"{filename}" , caption=f"""**آپلود**\n✿ اسم : `{filename}`\n✿ حجم : `{total/1024/1024:.3f} ᴍʙ`\n✿ ساعت : `{datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")}`/n Botsorce""")
    app.delete_messages(m.chat.id , m.id)
    os.remove(filename)
  except:
   m.edit_text(f"✿ لینک دانلود خراب است !")
 #دانلود#
#سازنده ها#
 elif text.startswith("sticker"):
  try:
   im = Image.open(GET(f"http://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=colgate-logo&&text={text.replace('sticker' , '')[1::]}&fontsize=100", stream=True).raw) 
   im.save('sticker.png')
   os.rename('sticker.png' ,'sticker.webp')
   app.send_sticker(m.chat.id , f"sticker.webp" , reply_to_message_id=m.id)
   os.remove(f"sticker.webp")
  except Exception as er:
   m.edit_text(f"خطا : شما جلو sticker~استیکر اسم یا .. بگذارید !")   

 elif text.startswith("استیکر"):
  try:
   im = Image.open(GET(f"http://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=colgate-logo&&text={text.replace('استیکر' , '')[1::]}&fontsize=100", stream=True).raw) 
   im.save('sticker.png')
   os.rename('sticker.png' ,'sticker.webp')
   app.send_sticker(m.chat.id , f"sticker.webp" , reply_to_message_id=m.id)
   os.remove(f"sticker.webp")
  except Exception as er:
   m.edit_text(f"خطا : شما جلو sticker~استیکر اسم یا .. بگذارید !")   
 
 elif m.text == "time":
  try:
    for i in range(0,20):
      kir = datetime.now(timezone("Asia/Tehran")).strftime("%H:%M:%S")
      app.edit_message_text(m.chat.id , m.id , f"**🌸Time:** `{kir}`")
      sleep(1)
  except Exception as er:
   m.edit_text(er)
 
 elif m.text == "ساعت":
  try:
    for i in range(0,20):
      kir = datetime.now(timezone("Asia/Tehran")).strftime("%H:%M:%S")
      app.edit_message_text(m.chat.id , m.id , f"**🌸ساعت:** `{kir}`")
      sleep(1)
  except Exception as er:
   m.edit_text(er)
#سازنده ها#
#________________________Server_info________________________________
 elif text == "ping":
  try:
    up_a = (strftime('%H:%M:%S', gmtime(uptime())))
    svmem = virtual_memory()
    app.edit_message_text(m.chat.id , m.id , f"""
✅Online Botsorce
StArT Botsorce : `{up_a}`
NoSkHe SELF : `{Src_vrsion}`
""")
  except Exception as er:
   m.edit_text(er)

 elif text == "پینگ":
  try:
    up_a = (strftime('%H:%M:%S', gmtime(uptime())))
    svmem = virtual_memory()
    app.edit_message_text(m.chat.id , m.id , f"""
✅Online Botsorce
StArT Botsorce : `{up_a}`
NoSkHe SELF : `{Src_vrsion}`
""")
  except Exception as er:
   m.edit_text(er)
#c#
 elif text == "cpu":
  try:
    cpufreq = cpu_freq()
    app.edit_message_text(m.chat.id , m.id , f"""
✿ `CPU` ~  (`{cpu_percent()}%`)""")
  except Exception as er: 
   m.edit_text(er)
   #c#
 elif text == "help":
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**🧸به بخش راهنمای سلف خوش اومدی 🧸**

✿ ورودی ها : 
........
`تنظیمات`
`setting`
........
`منو کاربری`
`menok`
........
`سرگرمی`
`gaming`
........
`modmuno`
`مود منو`
........
`menogp`
`منو گپ`
........

|| 🩷 صاحب سلف : @sadra_a_16 🩷 ||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("راهنما"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**🧸به بخش راهنمای سلف خوش اومدی 🧸**

✿ ورودی ها : 
........
`تنظیمات`
`setting`
........
`منو کاربری`
`menok`
........
`سرگرمی`
`gaming`
........
`modmuno`
`مود منو`
........
`menogp`
`منو گپ`
........

|| 🩷 صاحب سلف : @sadra_a_16 🩷 ||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("setting"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش تنظيمات سلف خوش اومدی ⚙**
`ping` | `پینگ`

`cpu`

`voice` ویس چنجر اینگلیسی

`file_info` برای مشخص کردن مشخصات . . .

`on_off_status`

||موفق باشید ☃️||

|| sadra 🚔||
""")
  except Exception as er: 
   m.edit_text(er)

 elif text == ("تنظیمات"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش تنظيمات سلف خوش اومدی ⚙**
`ping` | `پینگ`

`cpu`

`voice` ویس چنجر اینگلیسی

`file_info` برای مشخص کردن مشخصات . . .

`on_off_status`

||موفق باشید ☃️||

||مالک Botsorce 🚔||
""")
  except Exception as er: 
   m.edit_text(er)

 elif text == ("gaming"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش سرگرمی سلف خوش اومدی 🎪**

----------------------------
جنبه شوخی و کاربردی ایستگاه 😂🩷 :

`اد پاچه خوار` | `addkhaymal`
----------------------------
😂💋شوخی با دیگران و استفاده : 

`fuckkh` | `پاچه خوارو بکش`

`love1` | `لاو1`

`love | لاو`

`start jgh` | `جق بزن`

`Reload` [دستور فارسی نداره]
----------------------------
[ نکته : برای اجرای فوتبال گل بشه 4 جلو دستور 
گل نشه جلو دستور 1 ] :
`football` | `فوتبال`
----------------------------
`basketball` | `بسکتبال`
----------------------------
`bowling` | `بولینگ`
----------------------------
`dart` | `دارت`
----------------------------
[نکته : برای اجرای تاس باید 1 تا عدد 6 رو جلو آن بگذارید تا اون عدد رو براتون تاس بندازه مثلا <تاس 1> ] :
`tas` | `تاس`
----------------------------
🖥 ساخت فونت فارسی و انگلیسی : 

`fontfa` | `فونت فارسی`
`fonten` | `فونت انگلیسی`
----------------------------
[**نکته مهم :** این‌ دستور جنبه فان و کپی برداری داره / این دستور عکس پروفایل کسی که یوزرنیم آن را جلو دستور بگذارید را برمی‌داره می‌گذاره روی پروفایل شما و اسم اکانت طرف و بیوگرافی طرف ] :

`clone` | `کپی کردن`

|| 🩷 موفق باشید ||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("سرگرمی"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش سرگرمی سلف خوش اومدی 🎪**

----------------------------
جنبه شوخی و کاربردی ایستگاه 😂🩷 :

`اد پاچه خوار` | `addkhaymal`
----------------------------
😂💋شوخی با دیگران و استفاده : 

`fuckkh` | `پاچه خوارو بکش`

`love1` | `لاو1`

`love | لاو`

`start jgh` | `جق بزن`

`Reload` [دستور فارسی نداره]
----------------------------
[ نکته : برای اجرای فوتبال گل بشه 4 جلو دستور 
گل نشه جلو دستور 1 ] :
`football` | `فوتبال`
----------------------------
`basketball` | `بسکتبال`
----------------------------
`bowling` | `بولینگ`
----------------------------
`dart` | `دارت`
----------------------------
[نکته : برای اجرای تاس باید 1 تا عدد 6 رو جلو آن بگذارید تا اون عدد رو براتون تاس بندازه مثلا <تاس 1> ] :
`tas` | `تاس`
----------------------------
🖥 ساخت فونت فارسی و انگلیسی : 

`fontfa` | `فونت فارسی`
`fonten` | `فونت انگلیسی`
----------------------------
[**نکته مهم :** این‌ دستور جنبه فان و کپی برداری داره / این دستور عکس پروفایل کسی که یوزرنیم آن را جلو دستور بگذارید را برمی‌داره می‌گذاره روی پروفایل شما و اسم اکانت طرف و بیوگرافی طرف ] :

`clone` | `کپی کردن`

|| 🩷 موفق باشید ||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("modmuno"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش مود منو سلف خوش اومدی 🔋**

----------------------------
حالت های نوشتن برای روشن کردن on خاموش کردن off جلو دستور بگذارید :

`bold`

`spoiler`

`italic`

`code`

`strike`

`underline`

`emoji`
----------------------------
✏️دستور های مربوط به اکانت : 

`setprofile` | `ست پروفایل`

`delprofile` | `حذف پروفایل`

`setname` | `تنظیم اسم`

`setlastname` | `تنظیم فامیلی`

`setbio` | `تنظیم بیوگرافی`

گرفتن اسم دقیق اکانت دیگران با دستور ~> 

`youname` یوزرنیم جلو دستور بگذارید یا ریپلای کنید.

ذخیره کردن فیلم و عکس تایمر دار دیگران با دستور ~> 
`bashe` ریپلای کنید روی عکس یا فیلم تایمر دار.
[نکته : اگر دیدید دستور bashe رو زدید و جواب داد **hom** مشکلی پیش نیومده منظور این است که ریپلای نکرده اید یا دستور را آزاد راهی کرده اید ]

🖼 با دستورات زیر میتوانید تبدیل کنید :

`tlpho` | `تبدیل به عکس`

`tlskr` | `تبدیل به استیکر`

`tlgif` | `تبدیل به گیف`

⏰دستور های ساعت در بیو اسم :
`1timename` <~ تایم اسم یک 

`2timename` <~ تایم اسم دو

`1timebio` <~ تایم در بیوگرافی یک

`2timebio` <~ تایم در بیوگرافی دو

`3timebio` <~ تایم در بیوگرافی سه

`!fontname` <~ فونت خودکار اسم 

🔌 کاربردی : 
`download` | `دانلود`

[نکته : برای دانلود لینک دانلود را جلو دستور دانلود | download بگذارید ]

`sticker` | `استیکر`

[نکته : برای ساخت استیکر اسمی جلو دستور استیکر | sticker اسم را بگذارید ]

`time` | `ساعت`

[نکته : با دستور ساعت یا time آنلاین 20 ثانیه رو می‌شمارد ]


|| موفق باشید🩵 ||
""")
  except Exception as er: 
   m.edit_text(er)

 elif text == ("مود منو"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به بخش مود منو سلف خوش اومدی 🔋**

----------------------------
حالت های نوشتن برای روشن کردن on خاموش کردن off جلو دستور بگذارید :

`bold`

`spoiler`

`italic`

`code`

`strike`

`underline`

`emoji`
----------------------------
✏️دستور های مربوط به اکانت : 

`setprofile` | `ست پروفایل`

`delprofile` | `حذف پروفایل`

`setname` | `تنظیم اسم`

`setlastname` | `تنظیم فامیلی`

`setbio` | `تنظیم بیوگرافی`

گرفتن اسم دقیق اکانت دیگران با دستور ~> 

`youname` یوزرنیم جلو دستور بگذارید یا ریپلای کنید.

ذخیره کردن فیلم و عکس تایمر دار دیگران با دستور ~> 
`bashe` ریپلای کنید روی عکس یا فیلم تایمر دار.
[نکته : اگر دیدید دستور bashe رو زدید و جواب داد **hom** مشکلی پیش نیومده منظور این است که ریپلای نکرده اید یا دستور را آزاد راهی کرده اید ]

🖼 با دستورات زیر میتوانید تبدیل کنید :

`tlpho` | `تبدیل به عکس`

`tlskr` | `تبدیل به استیکر`

`tlgif` | `تبدیل به گیف`

⏰دستور های ساعت در بیو اسم :
`1timename` <~ تایم اسم یک 

`2timename` <~ تایم اسم دو

`1timebio` <~ تایم در بیوگرافی یک

`2timebio` <~ تایم در بیوگرافی دو

`3timebio` <~ تایم در بیوگرافی سه

`!fontname` <~ فونت خودکار اسم

🔌 کاربردی : 
`download` | `دانلود`

[نکته : برای دانلود لینک دانلود را جلو دستور دانلود | download بگذارید ]

`sticker` | `استیکر`

[نکته : برای ساخت استیکر اسمی جلو دستور استیکر | sticker اسم را بگذارید ]

`time` | `ساعت`

[نکته : با دستور ساعت یا time آنلاین 20 ثانیه رو می‌شمارد ]


|| موفق باشید🩵 ||
""")
  except Exception as er: 
   m.edit_text(er)

 elif text == ("منو گپ"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به منو گپ سلف خوش اومدید 🎵**

`delallmsguser`

[برای حذف کل پیام کاربر در گپ ]

|

[برای ساخت گروه ساده از دستور ]

`creatgroup`

|

`creatsupergroup`

[برای ساخت سوپر گروه از دستور ]

|

[برای ساخت کانال خام از دستور]

`creatchannel`

|

`setchatusername`

[برای عوض کردن یوزرنیم کانال و گروه به کار می‌رود | این دستور برای مالک گپ یا کانال است ]

|
پین کردن پیام ~> `pin` 

حذف پین ~> `unpin`

حذف کل پین ها ~> `unpinall`
|
برای عوض کردن بیو گپ ~> `setchatbio`

برای عوض کردن اسم گپ ~> `setchatname`

برای عوض کردن اسم گپ ~> `setchatphoto`

برای حذف کردن عکس گپ ~>`delchatphoto`

|
[حذف پیام تا حد 1000 عدد جلو دستور بگذارید ] 
`delete`

|
اطلاعات کاربر در گپ ~> `inf`
|

سکوت کردن کاربر ~> `mute` | `سکوت` 

حذف سکوت کاربر ~> `unmute` | `حذف سکوت`

حذف کل سکوت ها ~> `allunmute`
|
خوش اومد گویی 
`welcomeen`  انگلیسی 

`welcomefa`  فارسی 

|
برای بن و آن بن کردن از دستورات زیر استفاده کنید ~>
`ban`

`unban`
|
تعداد ادمین های گروه ~>
`tadmin`

|| موفق باشید ❄️ ||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("menogp"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
**به منو گپ سلف خوش اومدید 🎵**

`delallmsguser`

[برای حذف کل پیام کاربر در گپ ]

|

[برای ساخت گروه ساده از دستور ]

`creatgroup`

|

`creatsupergroup`

[برای ساخت سوپر گروه از دستور ]

|

[برای ساخت کانال خام از دستور]

`creatchannel`

|

`setchatusername`

[برای عوض کردن یوزرنیم کانال و گروه به کار می‌رود | این دستور برای مالک گپ یا کانال است ]

|
پین کردن پیام ~> `pin` 

حذف پین ~> `unpin`

حذف کل پین ها ~> `unpinall`
|
برای عوض کردن بیو گپ ~> `setchatbio`

برای عوض کردن اسم گپ ~> `setchatname`

برای عوض کردن اسم گپ ~> `setchatphoto`

برای حذف کردن عکس گپ ~>`delchatphoto`

|
[حذف پیام تا حد 1000 عدد جلو دستور بگذارید ] 
`delete`

|
اطلاعات کاربر در گپ ~> `inf`
|

سکوت کردن کاربر ~> `mute` | `سکوت` 

حذف سکوت کاربر ~> `unmute` | `حذف سکوت`

حذف کل سکوت ها ~> `allunmute`
|
خوش اومد گویی 
`welcomeen`  انگلیسی 

`welcomefa`  فارسی 

|
برای بن و آن بن کردن از دستورات زیر استفاده کنید ~>
`ban`

`unban`
|
تعداد ادمین های گروه ~>
`tadmin`


|| موفق باشید ❄️ ||
""")
  except Exception as er: 
   m.edit_text(er)

 elif text == ("منو کاربر"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
*به منو کاربری سلف خوش اومدید 💣*

✿ `block`

[ جلو دستور آیدی عددی یا یوزر طرف یا ریپلای کنید تا خودکار بلاک شود.]

✿ `unblock`

[ جلو دستور آیدی عددی یا یوزر طرف یا ریپلای کنید تا خودکار از بلاک در بیاد.]
.............................
✿ `left`

[برای لفت از کانال یا گروه عمومی آیدی را جلو دستور بالا بگذارید تا لفت بدهد]
✿ `join`

[برای عضو شدن در کانال یا گروه عمومی آیدی را جلو دستور بالا بگذارید تا عضو شود]
.............................
`ip` 
~> برای بدست آوردن آیپی سایت به کار می‌رود لینک سایت رو جلو دستور بگذارید

`whoisip`

[با دستور بالا میتوانید اطلاعت آیپی را در بیاورید مثلا 185.88.181.58 جلو دستور گذاشته و اطلاعت آن را ببنید تا یاد بگیرید ]
.............................
`id` | `آیدی`

[لطفا روی کاربری ریپلای کنید یا آیدی را جلو دستور بگذارید]
.............................
`allf` | `حذف دشمن ها`

[لطفا روی کاربری ریپلای کنید یا آیدی را جلو دستور بگذارید]
`delenemy` | `حذف دشمن`

`setenemy` | `تنظیم دشمن`
.............................
`voice` 

[لطفا کلمات انگلیسی را جلو دستور بنویسیداز کلمات فارسی پشتبانی نمیکند]
.............................
`emojib` => `no` °  `off`

`emojig` => `no` °  `off`

||موفق باشید 🤎||
""")
  except Exception as er: 
   m.edit_text(er)
   
 elif text == ("menok"):
  try:
    app.edit_message_text(m.chat.id , m.id , f"""
*به منو کاربری سلف خوش اومدید 💣*

✿ `block`

[ جلو دستور آیدی عددی یا یوزر طرف یا ریپلای کنید تا خودکار بلاک شود.]

✿ `unblock`

[ جلو دستور آیدی عددی یا یوزر طرف یا ریپلای کنید تا خودکار از بلاک در بیاد.]
.............................
✿ `left`

[برای لفت از کانال یا گروه عمومی آیدی را جلو دستور بالا بگذارید تا لفت بدهد]
✿ `join`

[برای عضو شدن در کانال یا گروه عمومی آیدی را جلو دستور بالا بگذارید تا عضو شود]
.............................
`ip` 
~> برای بدست آوردن آیپی سایت به کار می‌رود لینک سایت رو جلو دستور بگذارید

`whoisip`

[با دستور بالا میتوانید اطلاعت آیپی را در بیاورید مثلا 185.88.181.58 جلو دستور گذاشته و اطلاعت آن را ببنید تا یاد بگیرید ]
.............................
`id` | `آیدی`

[لطفا روی کاربری ریپلای کنید یا آیدی را جلو دستور بگذارید]
.............................
`allf` | `حذف دشمن ها`
`alllove` | `حذف عشق ها`

[لطفا روی کاربری ریپلای کنید یا آیدی را جلو دستور بگذارید]
`delenemy` | `حذف دشمن`

`setenemy` | `تنظیم دشمن`

`setlove` | `تنظیم عشق`

`deletlove` | `حذف عشق`
.............................
`voice` 

[لطفا کلمات انگلیسی را جلو دستور بنویسیداز کلمات فارسی پشتبانی نمیکند]
.............................
`.emojib` => `no` °  `off`

`.emojig` => `no` °  `off`

||موفق باشید 🤎||
""")
  except Exception as er: 
   m.edit_text(er)
   
#________________________End________________________________
 elif text.startswith("voice"):
  try:
   audio = gTTS(text=text.replace("voice","")[1::] , lang='en')
   audio.save("voice.ogg")
   app.send_audio(m.chat.id , "voice.ogg")
   app.delete_messages(m.chat.id , m.id)
   os.remove(f"voice.ogg")
  except Exception as er:
   m.edit_text(f"لطفا برای استفاده کلمات انگلیسی به کار ببرید فارسی مجاز نیست")   
   #ویس چنجر#
   
#________________________Delete Message________________________________
 elif text.startswith("delete"):
   mmd = app.get_chat_member(m.chat.id, "me")
   rasi = dast_del(text=mmd)
   if rasi:
     try:
       reu = int(text.replace("delete",""))
       if type(reu) == int:
         kalc = 0
         for m in app.get_chat_history(m.chat.id):
            if reu != kalc:
              m.delete(revoke=True)
              kalc += 1
            else:
              break
         m.reply(f"🍒 **باموفقیت** `{kalc}` **پیام در کانال یا گروه شما پاکسازی شد** ", quote=False)
       else:
         m.reply("لطفا یک عدد تا 1000 جلو دستور بگذارید")
     except Exception as er:
       app.send_message(m.chat.id , f"❋ **ERROR** :\n(`{er}`)")
   else:
     m.reply("مقام شما در اینجا برای حذف پیام های دیگران کافی نیست لطفا درخاست ادمین شدن کنید")
#________________________End________________________________
#______________________________Get info________________________________
 elif text.startswith("file_info"):
  getcontext().prec = 3
  try:
   if m.reply_to_message.document: #فایل
     m.edit_text(f"""❋ Name ⤳ (`{m.reply_to_message.document.file_name}`)
❋ Type ⤳ (`{m.reply_to_message.document.mime_type}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.document.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Date ⤳ (`{m.reply_to_message.document.date}`)
❋ File iD ⤳ (`{m.reply_to_message.document.file_id}`)""")
   elif m.reply_to_message.photo: #عکس
     m.edit_text(f"""❋ Size ⤳ (`{m.reply_to_message.photo.width}×{m.reply_to_message.photo.height}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.photo.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Date ⤳ (`{m.reply_to_message.photo.date}`)
❋ File iD ⤳ (`{m.reply_to_message.photo.file_id}`)""")
   elif m.reply_to_message.video: #ویدیو
     m.edit_text(f"""❋ Type ⤳ (`{m.reply_to_message.video.mime_type}`)
❋ Size ⤳ (`{m.reply_to_message.video.width}×{m.reply_to_message.video.height}`)
❋ Duration ⤳ (`{m.reply_to_message.video.duration}s`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.video.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Date ⤳ (`{m.reply_to_message.video.date}`)
❋ Support Streaming ⤳ (`{m.reply_to_message.video.supports_streaming}`)
❋ File iD ⤳ (`{m.reply_to_message.video.file_id}`)""")
   elif m.reply_to_message.animation: #گیف
     m.edit_text(f"""❋ Size ⤳ (`{m.reply_to_message.animation.width}×{m.reply_to_message.animation.height}`)
❋ Type ⤳ (`{m.reply_to_message.animation.mime_type}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.animation.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Duration ⤳ (`{m.reply_to_message.animation.duration}s`)
❋ Date ⤳ (`{m.reply_to_message.animation.date}`)
❋ File iD ⤳ (`{m.reply_to_message.animation.file_id}`)""")
   elif m.reply_to_message.sticker: #استیکر
     m.edit_text(f"""❋ Size ⤳ (`{m.reply_to_message.sticker.width}×{m.reply_to_message.sticker.height}`)
❋ Name ⤳ (`{m.reply_to_message.sticker.file_name}`)
❋ Type ⤳ (`{m.reply_to_message.sticker.mime_type}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.sticker.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Emoji ⤳ (`{m.reply_to_message.sticker.emoji}`)
❋ Is Animated ⤳ (`{m.reply_to_message.sticker.is_animated}`)
❋ Is Video ⤳ (`{m.reply_to_message.sticker.is_video}`)
❋ Sticker Set ⤳ (`{"https://t.me/addstickers/"+m.reply_to_message.sticker.set_name if m.reply_to_message.sticker.set_name else "--"}`)
❋ Date ⤳ (`{m.reply_to_message.sticker.date}`)
❋ File iD ⤳ (`{m.reply_to_message.sticker.file_id}`)""")
   elif m.reply_to_message.voice: #ویس
     m.edit_text(f"""❋ Type ⤳ (`{m.reply_to_message.voice.mime_type}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.voice.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Duration ⤳ (`{m.reply_to_message.voice.duration}s`)
❋ Date ⤳ (`{m.reply_to_message.voice.date}`)
❋ File iD ⤳ (`{m.reply_to_message.voice.file_id}`)""")
   elif m.reply_to_message.audio: #موزیک
     m.edit_text(f"""❋ Title ⤳ (`{m.reply_to_message.audio.title}`)
❋ Performer ⤳ (`{m.reply_to_message.audio.performer}`)
❋ Type ⤳ (`{m.reply_to_message.audio.mime_type}`)
❋ File Name ⤳ (`{m.reply_to_message.audio.file_name}`)
❋ File Size ⤳ (`{Decimal(int(m.reply_to_message.audio.file_size))/Decimal(1024)/Decimal(1024)}ᴍʙ`)
❋ Duration ⤳ (`{m.reply_to_message.audio.duration}s`)
❋ Date ⤳ (`{m.reply_to_message.audio.date}`)
❋ File iD ⤳ (`{m.reply_to_message.audio.file_id}`)""")
   elif m.reply_to_message.text: #متن
     m.edit_text(f"**Please Reply To A Media/file**")
  except Exception as er:
   m.edit_text(er)

 elif text == "tadmin":
  try:
     b = "❋ **ادمین ها ** :\n\n"
     c = 1;k = 0
     a = app.get_chat_members(m.chat.id, filter=enums.ChatMembersFilter.ADMINISTRATORS)
     for i in a:
        if i.user.is_deleted == False:
          b += "☐"+str(c)+" ↬ °"+(i.user.mention if i.user.id else "--")+"°\n"
          c += 1
        else:
          k += 1
     if k != 0:
       b += f"☐ **Deleted Account Admin** : `{k}`\n└— **Count** : `{k + c - 1}`"
     else:
       b += f" ☐  \n **تعداد : ** : `{k + c - 1}`"
     m.reply(b)
  except Exception as er:
   m.edit_text(f"❋ **ERROR** :\n(`{er}`)")

 elif text.startswith("inf"):
  if text.split()[1] == "@this":
    user = m.chat.id
  else:
    user = text.split()[1]
  user = app.get_chat(user)
  try:
   if user.photo:
     down = app.download_media(user.photo.big_file_id)
     app.send_photo(m.chat.id , down , f"""||اطلاعات اکانت||

✿ **Title** : `{user.title}`
✿ **ID** : `{user.id}`
✿ **Username** : `@{user.username if user.username else '--'}`
✿ **Members** : `{user.members_count}`
✿ **Dc ID** : `{user.dc_id}`
✿ **Is Creator** : `{user.is_creator}`
✿ **Is Verified** : `{user.is_verified}`
✿ **Is Restricted** : `{user.is_restricted}`
✿ **Is Scam** : `{user.is_scam}`
✿ **Is Fake** : `{user.is_fake}`
✿ **Sticker Set** : `{"https://t.me/addstickers/"+user.sticker_set_name if user.sticker_set_name else "--"}`
✿ **Description** : `{user.description}`""", reply_to_message_id=m.id)
     os.remove(down)
   else:
     app.send_message(m.chat.id , f"""||اطلاعت اکانت||

✿ **Title** : `{user.title}`
✿ **ID** : `{user.id}`
✿ **Username** : `@{user.username if user.username else '--'}`
✿ **Members** : `{user.members_count}`
✿ **Dc ID** : `{user.dc_id}`
✿ **Is Creator** : `{user.is_creator}`
✿ **Is Verified** : `{user.is_verified}`
✿ **Is Restricted** : `{user.is_restricted}`
✿ **Is Scam** : `{user.is_scam}`
✿ **Is Fake** : `{user.is_fake}`
✿ **Sticker Set** : `{"https://t.me/addstickers/"+user.sticker_set_name if user.sticker_set_name else "--"}`
✿ **Description** : `{user.description}`""", reply_to_message_id=m.id)
  except Exception as er:
   m.edit_text(f"لطفا یوزر را جلو دستور بگذارید")

 elif text.startswith("id"):
  try:
   user_id_get = (m.reply_to_message.from_user.id if m.reply_to_message else app.get_users(text.split()[1]).id)
   user = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(user_id_get)))
   count_photo = app.get_chat_photos_count(user_id_get)
   kiri = app.get_users(user_id_get)
   if kiri.photo:
     down = app.download_media(kiri.photo.big_file_id)
     app.send_photo(m.chat.id , down , f"""__User info__

❋ **Name** : `{user.users[0].first_name if user.users[0].first_name else "--"}`
❋ **LastName** : `{user.users[0].last_name if user.users[0].last_name else "--"}`
❋ **Id** : `{user.users[0].id if user.users[0].id else "--"}`
❋ **Username** : `@{user.users[0].username if user.users[0].username else "--"}`
❋ **Profile Picture Count** : `{count_photo}`
❋ **Common Chats Count** : `{user.full_user.common_chats_count if user.full_user.common_chats_count else "0"}`
❋ **BIO** : `{user.full_user.about if user.full_user.about else "--"}`
❋ **Scam** : `{user.users[0].scam}`
❋ **Can pin message** : `{user.full_user.can_pin_message}`
❋ **Contact** : `{user.users[0].contact}`
❋ **Bot** : `{user.users[0].bot}`
❋ **Verified** : `{user.users[0].verified}`
❋ **Deleted** : `{user.users[0].deleted}`
❋ **Phone Calls Available** : `{user.full_user.phone_calls_available}`
❋ **Phone Calls Private** : `{user.full_user.phone_calls_private}`
❋ **Video Calls Available** : `{user.full_user.video_calls_available}`
❋ **Access hash** : `{user.users[0].access_hash}`
❋ **Blocked** : `{user.full_user.blocked}`
`{f"❋ **Current ChatID**: {m.chat.id}" if m.chat.id != user.users[0].id else ""}`""" , reply_to_message_id=m.id)
   else:
     app.send_message(m.chat.id , f"""__User info__

❋ **Name** : `{user.users[0].first_name if user.users[0].first_name else "--"}`
❋ **LastName** : `{user.users[0].last_name if user.users[0].last_name else "--"}`
❋ **Id** : `{user.users[0].id if user.users[0].id else "--"}`
❋ **Username** : `@{user.users[0].username if user.users[0].username else "--"}`
❋ **Profile Picture Count** : `{count_photo}`
❋ **Common Chats Count** : `{user.full_user.common_chats_count if user.full_user.common_chats_count else "0"}`
❋ **BIO** : `{user.full_user.about if user.full_user.about else "--"}`
❋ **Scam** : `{user.users[0].scam}`
❋ **Can pin message** : `{user.full_user.can_pin_message}`
❋ **Contact** : `{user.users[0].contact}`
❋ **Bot** : `{user.users[0].bot}`
❋ **Verified** : `{user.users[0].verified}`
❋ **Deleted** : `{user.users[0].deleted}`
❋ **Phone Calls Available** : `{user.full_user.phone_calls_available}`
❋ **Phone Calls Private** : `{user.full_user.phone_calls_private}`
❋ **Video Calls Available** : `{user.full_user.video_calls_available}`
❋ **Access hash** : `{user.users[0].access_hash}`
❋ **Blocked** : `{user.full_user.blocked}`
`{f"❋ **Current ChatID**: {m.chat.id}" if m.chat.id != user.users[0].id else ""}`""" , reply_to_message_id=m.id)
   os.remove(down)
  except errors.exceptions.bad_request_400.UsernameNotOccupied:
   app.send_message(m.chat.id , f"یوزر نیم اشتباه است")
  except Exception as er:
   m.edit_text(f"لطفا درست ریپلای کنید یا آیدی یا یوزر را جلو دستور بگذارید")

 elif text.startswith("آیدی"):
  try:
   user_id_get = (m.reply_to_message.from_user.id if m.reply_to_message else app.get_users(text.split()[1]).id)
   user = app.invoke(functions.users.GetFullUser(id=app.resolve_peer(user_id_get)))
   count_photo = app.get_chat_photos_count(user_id_get)
   kiri = app.get_users(user_id_get)
   if kiri.photo:
     down = app.download_media(kiri.photo.big_file_id)
     app.send_photo(m.chat.id , down , f"""__User info__

❋ **Name** : `{user.users[0].first_name if user.users[0].first_name else "--"}`
❋ **LastName** : `{user.users[0].last_name if user.users[0].last_name else "--"}`
❋ **Id** : `{user.users[0].id if user.users[0].id else "--"}`
❋ **Username** : `@{user.users[0].username if user.users[0].username else "--"}`
❋ **Profile Picture Count** : `{count_photo}`
❋ **Common Chats Count** : `{user.full_user.common_chats_count if user.full_user.common_chats_count else "0"}`
❋ **BIO** : `{user.full_user.about if user.full_user.about else "--"}`
❋ **Scam** : `{user.users[0].scam}`
❋ **Can pin message** : `{user.full_user.can_pin_message}`
❋ **Contact** : `{user.users[0].contact}`
❋ **Bot** : `{user.users[0].bot}`
❋ **Verified** : `{user.users[0].verified}`
❋ **Deleted** : `{user.users[0].deleted}`
❋ **Phone Calls Available** : `{user.full_user.phone_calls_available}`
❋ **Phone Calls Private** : `{user.full_user.phone_calls_private}`
❋ **Video Calls Available** : `{user.full_user.video_calls_available}`
❋ **Access hash** : `{user.users[0].access_hash}`
❋ **Blocked** : `{user.full_user.blocked}`
`{f"❋ **Current ChatID**: {m.chat.id}" if m.chat.id != user.users[0].id else ""}`""" , reply_to_message_id=m.id)
   else:
     app.send_message(m.chat.id , f"""__User info__

❋ **Name** : `{user.users[0].first_name if user.users[0].first_name else "--"}`
❋ **LastName** : `{user.users[0].last_name if user.users[0].last_name else "--"}`
❋ **Id** : `{user.users[0].id if user.users[0].id else "--"}`
❋ **Username** : `@{user.users[0].username if user.users[0].username else "--"}`
❋ **Profile Picture Count** : `{count_photo}`
❋ **Common Chats Count** : `{user.full_user.common_chats_count if user.full_user.common_chats_count else "0"}`
❋ **BIO** : `{user.full_user.about if user.full_user.about else "--"}`
❋ **Scam** : `{user.users[0].scam}`
❋ **Can pin message** : `{user.full_user.can_pin_message}`
❋ **Contact** : `{user.users[0].contact}`
❋ **Bot** : `{user.users[0].bot}`
❋ **Verified** : `{user.users[0].verified}`
❋ **Deleted** : `{user.users[0].deleted}`
❋ **Phone Calls Available** : `{user.full_user.phone_calls_available}`
❋ **Phone Calls Private** : `{user.full_user.phone_calls_private}`
❋ **Video Calls Available** : `{user.full_user.video_calls_available}`
❋ **Access hash** : `{user.users[0].access_hash}`
❋ **Blocked** : `{user.full_user.blocked}`
`{f"❋ **Current ChatID**: {m.chat.id}" if m.chat.id != user.users[0].id else ""}`""" , reply_to_message_id=m.id)
   os.remove(down)
  except errors.exceptions.bad_request_400.UsernameNotOccupied:
   app.send_message(m.chat.id , f"یوزر نیم اشتباه است")
  except Exception as er:
   m.edit_text(f"لطفا درست ریپلای کنید یا آیدی یا یوزر را جلو دستور بگذارید")
#______________________________End________________________________
#______________________________Mute & enemy________________________________
 elif text.startswith("setenemy"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in enemy:
     if m.reply_to_message.from_user.id != app.get_me().id:
      enemy.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ {m.reply_to_message.from_user.mention} به لیست انمی اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ این کاربر {m.reply_to_message.from_user.mention} قبلا در لیست انمی بود')
   else :
    if app.get_users(text.split()[1]).id not in enemy :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      enemy.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'😔🩵 <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> به لیست انمی اضافه شد ')
    else:
     app.edit_message_text(m.chat.id , m.id , f'😔🩵 این آیدی<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> قبلا داخل لیست انمی بوده است')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("تنظیم دشمن"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in enemy:
     if m.reply_to_message.from_user.id != app.get_me().id:
      enemy.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ {m.reply_to_message.from_user.mention} به لیست انمی اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ این کاربر {m.reply_to_message.from_user.mention} قبلا در لیست انمی بود')
   else :
    if app.get_users(text.split()[1]).id not in enemy :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      enemy.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'😔🩵 <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> به لیست انمی اضافه شد ')
    else:
     app.edit_message_text(m.chat.id , m.id , f'😔🩵 این آیدی<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> قبلا داخل لیست انمی بوده است')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("delenemy"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in enemy:
     enemy.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} از لیست انمی حذف شد ')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} در لیست انمی وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in enemy :
     enemy.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست انمی وجود ندارد     ')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("حذف دشمن"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in enemy:
     enemy.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} از لیست انمی حذف شد ')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} در لیست انمی وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in enemy :
     enemy.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست انمی وجود ندارد     ')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")
#سکوت کاربر#
 elif text.startswith("mute"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in mutey:
     if m.reply_to_message.from_user.id != app.get_me().id:
      mutey.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} با موفقیت سکوت شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} در لیست سکوت است')
   else :
    if app.get_users(text.split()[1]).id not in mutey :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      mutey.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت سکوت شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست سکوت وجود ندارید ')
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید یا آیدی عددی جلو دستور یا یوزرنیم جلو دستور بگذارید")

 elif text.startswith("سکوت"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in mutey:
     if m.reply_to_message.from_user.id != app.get_me().id:
      mutey.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} با موفقیت سکوت شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} در لیست سکوت است')
   else :
    if app.get_users(text.split()[1]).id not in mutey :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      mutey.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت سکوت شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست سکوت وجود دارد')
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید یا آیدی عددی جلو دستور یا یوزرنیم جلو دستور بگذارید")

#حذف سکوت#
 elif text.startswith("unmute"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in mutey:
     mutey.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} از سکوت در اومد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر  {m.reply_to_message.from_user.mention} در لیست سکوت وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in mutey :
     mutey.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> از سکوت در اومد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست سکوت وجود ندارد')
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید یا آیدی عددی جلو دستور یا یوزرنیم جلو دستور بگذارید")

 elif text.startswith("حذف سکوت"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in mutey:
     mutey.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر {m.reply_to_message.from_user.mention} از سکوت در اومد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر  {m.reply_to_message.from_user.mention} در لیست سکوت وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in mutey :
     mutey.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> از سکوت در اومد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ کاربر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست سکوت وجود ندارد')
  except Exception as er:
   m.edit_text(f"لطفا روی کاربر ریپلای کنید یا آیدی عددی جلو دستور یا یوزرنیم جلو دستور بگذارید")
#حذف کل دشمن ها#
 elif text == "allf":
  een = ""
  t_een = 1
  if len(enemy) >= 1:
   for user in enemy:
    een += f"{t_een} - <a href=tg://user?id={user}>{app.get_users(user).first_name}</a>\n"
    t_een += 1
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is cleaned\n{een}")
   enemy.clear()
  else:
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is Empty ") 

 elif text == "حذف دشمن ها":
  een = ""
  t_een = 1
  if len(enemy) >= 1:
   for user in enemy:
    een += f"{t_een} - <a href=tg://user?id={user}>{app.get_users(user).first_name}</a>\n"
    t_een += 1
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is cleaned\n{een}")
   enemy.clear()
  else:
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is Empty ") 
  #حذف کل سکوت‌ ها#
 elif text == "allunmute":
  eem = ""
  t_eem = 1
  if len(mutey) >= 1:
   for user in mutey:
    eem += f"{t_eem} - <a href=tg://user?id={user}>{app.get_users(user).first_name}</a>\n"
    t_eem += 1
   app.edit_message_text(m.chat.id , m.id , f"✿ لیست سکوت پاکسازی شد\n{eem}")
   mutey.clear()
  else:
   app.edit_message_text(m.chat.id , m.id , f"✿ هیچ کاربری در لیست سکوت وجود ندارد") 
   #______________________________End________________________________
#______________________________LOVE________________________________
 elif text.startswith("setlove"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in krashh:
     if m.reply_to_message.from_user.id != app.get_me().id:
      krashh.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ {m.reply_to_message.from_user.mention} به لیست کراش اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ این کاربر {m.reply_to_message.from_user.mention} قبلا در لیست کراش بود')
   else :
    if app.get_users(text.split()[1]).id not in krashh :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      krashh.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'🩵 <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> به لیست کراش اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩵 این آیدی<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> قبلا داخل لیست کراش بوده است')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("تنظیم عشق"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id not in krashh:
     if m.reply_to_message.from_user.id != app.get_me().id:
      krashh.append(m.reply_to_message.from_user.id)
      app.edit_message_text(m.chat.id , m.id , f'✿ {m.reply_to_message.from_user.mention} به لیست کراش اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'✿ این کاربر {m.reply_to_message.from_user.mention} قبلا در لیست کراش بود')
   else :
    if app.get_users(text.split()[1]).id not in krashh :
     if app.get_users(text.split()[1]).id != app.get_me().id:
      krashh.append(app.get_users(text.split()[1]).id)
      app.edit_message_text(m.chat.id , m.id , f'🩵 <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> به لیست کراش اضافه شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩵 این آیدی<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> قبلا داخل لیست کراش بوده است')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("deletlove"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in krashh:
     krashh.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} از لیست کراش حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} در لیست کراش وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in krashh :
     krashh.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست کراش وجود ندارد')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")

 elif text.startswith("حذف عشق"):
  try:
   if m.reply_to_message:
    if m.reply_to_message.from_user.id in krashh:
     krashh.remove(m.reply_to_message.from_user.id)
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} از لیست کراش حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🤍 این یوزر {m.reply_to_message.from_user.mention} در لیست کراش وجود ندارد')
   else :
    if app.get_users(text.split()[1]).id in krashh :
     krashh.remove(app.get_users(text.split()[1]).id)
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر <a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> با موفقیت حذف شد')
    else:
     app.edit_message_text(m.chat.id , m.id , f'🩷این یوزر<a href=tg://user?id={app.get_users(text.split()[1]).id}>{app.get_users(text.split()[1]).first_name}</a> در لیست کراش وجود ندارد')
  except Exception as er:
   m.edit_text(f"لطفا آیدی عددی یا یوزرنیم یا ریپلی کنید")
#______________________________End________________________________
#______________________________LOVE________________________________
 elif text == "alllove":
  een = ""
  t_een = 1
  if len(krashh) >= 1:
   for user in krashh:
    een += f"{t_een} - <a href=tg://user?id={user}>{app.get_users(user).first_name}</a>\n"
    t_een += 1
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is cleaned\n{een}")
   krashh.clear()
  else:
   app.edit_message_text(m.chat.id , m.id , f"لیست کراش خالی است ") 

 elif text == "حذف عشق ها":
  een = ""
  t_een = 1
  if len(krashh) >= 1:
   for user in krashh:
    een += f"{t_een} - <a href=tg://user?id={user}>{app.get_users(user).first_name}</a>\n"
    t_een += 1
   app.edit_message_text(m.chat.id , m.id , f"❋ Enemy List is cleaned\n{een}")
   krashh.clear()
  else:
   app.edit_message_text(m.chat.id , m.id , f"❋ لیست کراش خالی است") 
#______________________________End________________________________
#______________________________On / Off________________________________
 elif text.startswith("1timename"):
  if text.split()[1] == "on":
   json_database.update({"timename1":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋تایم اسم یک **روشن شد** 🩶")
  elif text.split()[1] == "off":
   json_database.update({"timename1":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ تایم اسم یک **خاموش شد** 🩶")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")
   
 elif text.startswith("2timename"):
  if text.split()[1] == "on":
   json_database.update({"timename2":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ تایم اسم دو **روشن شد** 🩵")
  elif text.split()[1] == "off":
   json_database.update({"timename2":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ تایم اسم دو **خاموش شد** 🩵")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")

 elif text.startswith("3timename"):
  if text.split()[1] == "on":
   json_database.update({"timename3":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ تایم اسم سه **روشن** شد")
  elif text.split()[1] == "off":
   json_database.update({"timename3":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ تایم اسم سه **خاموش** شد")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")

 elif text.startswith("1timebio"):
  if text.split()[1] == "on":
   json_database.update({"timebio1":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [1] **فعال** شد 🩶")
  elif text.split()[1] == "off":
   json_database.update({"timebio1":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [1] **خاموش** شد ❤️‍🩹")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")

 elif text.startswith("2timebio"):
  if text.split()[1] == "on":
   json_database.update({"timebio2":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [2] **فعال** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"timebio2":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [2] **خاموش** شد 💙")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")

 elif text.startswith("3timebio"):
  if text.split()[1] == "on":
   json_database.update({"timebio3":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [3] **فعال** شد 🩷")
  elif text.split()[1] == "off":
   json_database.update({"timebio3":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"تایم در بیو [3] **خاموش** شد 🩵")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")

 elif text.startswith(".limit_del"):
  b = int(text.split()[1])
  if type(b) == int:
     json_database.update({"limitDel":b})
     write("data.json", json.dumps(json_database))
     m.edit_text(f"❋ Anti Del Member Limit Successfully Updated to {b} ❋")
  else:
     m.edit_text(f"❋ Please Enter A Number ❋")

 elif text.startswith("!fontname"):
  if text.split()[1] == "on":
   json_database.update({"fontname":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"فونت خودکار **روشن** شد 🎧") 
  elif text.split()[1] == "off":
   json_database.update({"fontname":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"فونت خودکار **خاموش** شد 🎵")
  else:
   m.edit_text(f"لطفا دستور را درست وارد کنید")
#خوش اومد گویی#
 elif text.startswith("welcomeen"): 
  s = ""
  try:
   if text.split()[1] == "on":
     json_database.update({"welcomeen":"on"})
     write("data.json", json.dumps(json_database))
     m.edit_text(f"خوش اومد گویی انگلیسی **روشن** شد") 
   elif text.split()[1] == "off":
     json_database.update({"welcomeen":"off"})
     write("data.json", json.dumps(json_database))
     m.edit_text(f"خوش اومد گویی انگلیسی **خاموش** شد")
   elif text.split()[1] == None:
     m.edit_text(f"با on off این دستور را کنترل کنید")
  except Exception as er:
   m.edit_text(f"با on off این دستور را کنترل کنید")

 elif text.startswith("welcomefa"): 
  s = ""
  try:
   if text.split()[1] == "on":
     json_database.update({"welcomefa":"on"})
     write("data.json", json.dumps(json_database))
     m.edit_text(f"خوش اومد گویی فارسی **روشن** شد") 
   elif text.split()[1] == "off":
     json_database.update({"welcomefa":"off"})
     write("data.json", json.dumps(json_database))
     m.edit_text(f"خوش اومد گویی فارسی **خاموش** شد")
   elif text.split()[1] == None:
     m.edit_text(f"با on off این دستور را کنترل کنید")
  except Exception as er:
   m.edit_text(f"با on off این دستور را کنترل کنید")
####
 elif text.startswith(".firstcom"):
  if text.split()[1] == "on":
   json_database.update({"firstcom":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ First Comment is **ON**") 
  elif text.split()[1] == "off":
   json_database.update({"firstcom":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ First Comment is **OFF**")
  else:
   m.edit_text(f"❋ ʀᴇsᴜʟᴛs [ `ᴇʀʀᴏʀ` ] ❋")
#######
 elif text.startswith(".anti_fuck"):
  if text.split()[1] == "on":
   json_database.update({"anti_del":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ Anti Delete Member Mode  is **ON**") 
  elif text.split()[1] == "off":
   json_database.update({"anti_del":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ Anti Delete Member Mode **OFF**")
  else:
   m.edit_text(f"❋ ʀᴇsᴜʟᴛs [ `ᴇʀʀᴏʀ` ] ❋")   
   
 elif text.startswith("on_off_status"):
  mh = ""
  a = json_read("data.json")
  pairs = a.items()
  for key, value in pairs:
    mh += f"❋ {key} -> {value}\n"
  m.edit_text(f"{mh}")
  ###########
#______________________________End________________________________
#______________________________auto Answer________________________________
 elif text.startswith(".answer"):
  if text.split()[1] == "on":
   json_database.update({"autoan":"on"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ Auto Answer is **ON**") 
  elif text.split()[1] == "off":
   json_database.update({"autoan":"off"})
   write("data.json", json.dumps(json_database))
   m.edit_text(f"❋ Auto Answer is **OFF**")
  else:
   m.edit_text(f"❋ ʀᴇsᴜʟᴛs [ `ᴇʀʀᴏʀ` ] ❋")

 elif text.startswith(".addan"):
   an = text.replace(".addan" , "")[1::].split(":")[0]
   en = text.replace(".addan" , "")[1::].split(":")[1]
   answer.append(an)
   javab.append(en)
   m.edit_text(f"❋ Answer Successfully Added\n[{an} -> {en}]") 
   
 elif text.startswith(".anclear"):
   if len(answer) >= 1:
    answer.clear()
    javab.clear()
    m.edit_text(f"❋ Successful!\nAnswer List Cleared") 
   else:
    app.edit_message_text(m.chat.id , m.id , f"❋ Answer List is Empty ")  
   
 elif text.startswith(".delan"):
   if text.replace(".delan" , "")[1::] in answer:
    num = answer.index(text.replace(".delan" , "")[1::])
    answer.remove(answer[num])
    javab.remove(javab[num])
    m.edit_text(f"❋ Successfully\nRemoved From Asnwer List") 
   else:
    m.edit_text(f"❋ This is Not in Asnwer List") 
   
 elif text.startswith(".anlist"):
   s = ""
   op = 1
   if len(answer) >= 1:
    for i in range(0,int(len(answer))):
      s += f"❋ {op}: {answer[i]} -> {javab[i]}\n"
      op += 1
    m.edit_text(f"❋ **Answer List:**\n{s}") 
   else:
    app.edit_message_text(m.chat.id , m.id , f"❋ Answer List is Empty ") 
  
#______________________________End________________________________
#______________________________Fun________________________________
 reloadl = ["`start Reloading`",
"░░░░░░░░░░░░░░",
"▒░░░░░░░░░░░░░",
"▒▒░░░░░░░░░░░░",
"▒▒▒░░░░░░░░░░░",
"▒▒▒▒░░░░░░░░░░",
"▒▒▒▒▒░░░░░░░░░",
"▒▒▒▒▒▒░░░░░░░░",
"▒▒▒▒▒▒▒░░░░░░░",
"▒▒▒▒▒▒▒▒░░░░░░",
"▒▒▒▒▒▒▒▒▒░░░░░",
"▒▒▒▒▒▒▒▒▒▒░░░░",
"▒▒▒▒▒▒▒▒▒▒▒░░░",
"▒▒▒▒▒▒▒▒▒▒▒▒░░",
"▒▒▒▒▒▒▒▒▒▒▒▒▒░",
"▒▒▒▒▒▒▒▒▒▒▒▒▒▒",
"▓▒▒▒▒▒▒▒▒▒▒▒▒▒",
"▓▓▒▒▒▒▒▒▒▒▒▒▒▒",
"▓▓▓▒▒▒▒▒▒▒▒▒▒▒",
"▓▓▓▓▒▒▒▒▒▒▒▒▒▒",
"▓▓▓▓▓▒▒▒▒▒▒▒▒▒",
"▓▓▓▓▓▓▒▒▒▒▒▒▒▒",
"▓▓▓▓▓▓▓▒▒▒▒▒▒▒",
"▓▓▓▓▓▓▓▓▒▒▒▒▒▒",
"▓▓▓▓▓▓▓▓▓▒▒▒▒▒",
"▓▓▓▓▓▓▓▓▓▓▒▒▒▒",
"▓▓▓▓▓▓▓▓▓▓▓▒▒▒",
"▓▓▓▓▓▓▓▓▓▓▓▓▒▒",
"▓▓▓▓▓▓▓▓▓▓▓▓▓▒",
"▓▓▓▓▓▓▓▓▓▓▓▓▓▓",
"Reloading.",
"Reloading..",
"Reloading...",
"Reloading.",
"Reloading..",
"Reloading...",
"Reloading.",
"Reloading..",
"Reloading...",
"`:)`",
]

 if text == "Reload":
  for i in reloadl:
   app.edit_message_text(m.chat.id,m.id,i)
   
 reloadllll = ["`شروع`",
"🤚_ _ _ _ _ _ _ _ _ _ _ _ _💍",
"🤚_ _ _ _ _ _ _ _ _ _ _ _ 💍_",
"🤚_ _ _ _ _ _ _ _ _ _ _ 💍_ _",
"🤚_ _ _ _ _ _ _ _ _ _ 💍_ _ _",
"🤚_ _ _ _ _ _ _ _ _ 💍_ _ _ _",
"🤚_ _ _ _ _ _ _ _ 💍_ _ _ _ _",
"🤚_ _ _ _ _ _ _ 💍_ _ _ _ _ _",
"🤚_ _ _ _ _ _ 💍_ _ _ _ _ _ _",
"🤚_ _ _ _ _ 💍_ _ _ _ _ _ _ _",
"🤚_ _ _ _ 💍_ _ _ _ _ _ _ _ _",
"🤚_ _ _ 💍_ _ _ _ _ _ _ _ _ _",
"🤚_ _ 💍_ _ _ _ _ _ _ _ _ _ _",
"🤚_ 💍_ _ _ _ _ _ _ _ _ _ _ _",
"🤚💍_ _ _ _ _ _ _ _ _ _ _ _ _",
"_ _ _ _ _ _ _ 💏_ _ _ _ _ _ _ _",
]

 if text == "love":
  for i in reloadllll:
   app.edit_message_text(m.chat.id,m.id,i)

 if text == "لاو":
  for i in reloadllll:
   app.edit_message_text(m.chat.id,m.id,i)
   
 reloadlllll = ["`شروع`",
"💦_ _ _ _ _ _ _ _ _ _ _ _ _𓂸",
"💦_ _ _ _ _ _ _ _ _ _ _ _ 𓂸_",
"💦_ _ _ _ _ _ _ _ _ _ _ 𓂸_ _",
"💦_ _ _ _ _ _ _ _ _ _ 𓂸_ _ _",
"💦_ _ _ _ _ _ _ _ _ 𓂸_ _ _ _",
"💦_ _ _ _ _ _ _ _ 𓂸_ _ _ _ _",
"💦_ _ _ _ _ _ _ 𓂸_ _ _ _ _ _",
"💦_ _ _ _ _ _ 𓂸_ _ _ _ _ _ _",
"💦_ _ _ _ _ 𓂸_ _ _ _ _ _ _ _",
"💦_ _ _ _ 𓂸_ _ _ _ _ _ _ _ _",
"💦_ _ _ 𓂸_ _ _ _ _ _ _ _ _ _",
"💦_ _ 𓂸_ _ _ _ _ _ _ _ _ _ _",
"💦_ 𓂸_ _ _ _ _ _ _ _ _ _ _ _",
"💦𓂸_ _ _ _ _ _ _ _ _ _ _ _ _",
"_ _ _ _ _ _ _ 𓂺 _ _ _ _ _ _ _",
]

 if text == "start jgh":
  for i in reloadlllll:
   app.edit_message_text(m.chat.id,m.id,i)

 if text == "جق بزن":
  for i in reloadlllll:
   app.edit_message_text(m.chat.id,m.id,i)
   
 reloadllllll = ["`شروع`",
"😂                 • 🔫🤠",
"😂                •  🔫🤠",
"😂               •   🔫🤠",
"😂              •    🔫🤠",
"😂             •     🔫🤠",
"😂            •      🔫🤠",
"😂           •       🔫🤠",
"😂          •        🔫🤠",
"😂         •         🔫🤠",
"😂        •          🔫🤠",
"😂       •           🔫🤠",
"😂      •            🔫🤠",
"😂     •             🔫🤠",
"😂    •              🔫🤠",
"😂   •               🔫🤠",
"😂  •                🔫🤠",
"😂 •                 🔫🤠",
"😂•                  🔫🤠",
"🤯                  🔫 🤠",
"پاچه خوارو گرفتم کشتم 🍌",
]

 if text == "fuckkh":
  for i in reloadllllll:
   app.edit_message_text(m.chat.id,m.id,i)
   
 if text == "پاچه خوارو بکش":
  for i in reloadllllll:
   app.edit_message_text(m.chat.id,m.id,i)
   
 reloadlllllll = ["`شروع`",
 "🚶🏻‍♀_ _ _ _ _ _ _ _ _ _ _ _ _ _🏃🏻‍♂",
 "🚶🏻‍♀_ _ _ _ _ _ _ _ _ _ _ _ _ 🏃🏻‍♂_",
 "🚶🏻‍♀_ _ _ _ _ _ _ _ _ _ _ _ 🏃🏻‍♂_ _",
"🚶🏻‍♀_ _ _ _ _ _ _ _ _ _ _ 🏃🏻‍♂_ _ _",
"🚶🏻‍♀_ _ _ _ _ _ _ _ _ _ 🏃🏻‍♂_ _ _ _",
"🚶🏻‍♀_ _ _ _ _ _ _ _ _ 🏃🏻‍♂_ _ _ _ _",
"🚶🏻‍♀_ _ _ _ _ _ _ _ 🏃🏻‍♂_ _ _ _ _ _",
"🚶🏻‍♀_ _ _ _ _ _ _ 🏃🏻‍♂_ _ _ _ _ _ _",
"🚶🏻‍♀_ _ _ _ _ _ 🏃🏻‍♂_ _ _ _ _ _ _ _",
"🚶🏻‍♀_ _ _ _ _ 🏃🏻‍♂_ _ _ _ _ _ _ _ _",
"🚶🏻‍♀_ _ _ _ 🏃🏻‍♂_ _ _ _ _ _ _ _ _ _",
"🚶🏻‍♀_ _ _ 🏃🏻‍♂_ _ _ _ _ _ _ _ _ _ _",
"🚶🏻‍♀_ _ 🏃🏻‍♂_ _ _ _ _ _ _ _ _ _ _ _",
"🚶🏻‍♀_ 🏃🏻‍♂_ _ _ _ _ _ _ _ _ _ _ _ _",
"_ _ _ _ _ _ _🧍🏻‍♀🧍🏻‍♂_ _ _ _ _ _ _",
"LOVE 🩷",
]

 if text == "love1":
  for i in reloadlllllll:
   app.edit_message_text(m.chat.id,m.id,i)

 if text == "لاو1":
  for i in reloadlllllll:
   app.edit_message_text(m.chat.id,m.id,i)

#______________________________Fun________________________________

#بخش گیم های رندوم#
 
 elif text.startswith("tas"):
  if 0 < int(text.split()[1]) < 7:   
    app.delete_messages(m.chat.id , m.id)
    while True:
     msg = app.send_dice(m.chat.id, "🎲")
     if msg.dice.value != int(text.split()[1]):
       msg.delete()
     else:
       break
  else:
    m.edit_text(f"""⚠️ **خطا** : لطفا عدد ۱ تا ۶ یکی رو جلو دستور بگذارید 
دستور استفاده شده : [tas]~[تاس]""")

 elif text.startswith("dart"):
  app.delete_messages(m.chat.id , m.id)
  while True:
   msg = app.send_dice(m.chat.id, "🎯")
   if msg.dice.value != 6:
     msg.delete()
   else:
     break
     
 elif text.startswith("bowling"):
  app.delete_messages(m.chat.id , m.id) 
  while True:
   msg = app.send_dice(m.chat.id, "🎳")
   if msg.dice.value != 6:
     msg.delete()
   else:
     break

 elif text.startswith("basketball"):
  app.delete_messages(m.chat.id , m.id)
  while True:
   msg = app.send_dice(m.chat.id, "🏀")
   if msg.dice.value != 4:
     msg.delete()
   else:
     break

 elif text.startswith("football"):
  if int(text.split()[1]) == 1 or int(text.split()[1]) == 4:   
    app.delete_messages(m.chat.id , m.id)
    while True:
     msg = app.send_dice(m.chat.id, "⚽")
     if msg.dice.value != int(text.split()[1]):
       msg.delete()
     else:
       break
  else:
    m.edit_text(f"""⚠️ **خطا** : لطفا عدد ۱یا۴ یکی رو جلو دستور بگذارید 
دستور استفاده شده : [`football`]~[`فوتبال`]""")

#بخش گیم های رندوم#

 elif text.startswith("addkhaymal"):
  m.edit_text(f"شما پاچه خوار شناسایی شدی 😐😄") 

 elif text.startswith("اد پاچه خوار"):
  m.edit_text(f"شما پاچه خوار شناسایی شدی 😐😄") 
  
  #اتمام بخش سرگرمی
#______________________________End________________________________
#______________________________app run________________________________

scheduler = AsyncIOScheduler()
scheduler.add_job(job, "interval", seconds=5)
scheduler.add_job(antidelmember, "interval", seconds=5)
scheduler.add_job(mak, "interval", hours=2)
scheduler.start()
app.start(), print("ROSHAN SHODAM "), app.send_message("me" , "Salam 💦🧛🏻‍♀️ run Shodam !\n\nSELF : X\n\n.      **👩🏻‍✈️ @sadra_a_16 👩🏻‍✈️**"),idle(), app.stop()
#______________________________End________________________________