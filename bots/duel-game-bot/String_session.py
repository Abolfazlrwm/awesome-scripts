# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

#ایدی عددیتون
myid = 0
from telethon.sync import TelegramClient,events
from telethon.sessions import StringSession
with TelegramClient(StringSession(),api_id=0,api_hash='') as client:
  client.send_message(entity=myid,message=f'{client.session.save()}')
  print('Created !')