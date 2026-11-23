# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
"""
* developer : @amoanon 
* telegram channel : @wrong_path 
* project name : Ultra Self V1.0
"""
import sqlite3,json,os,time,asyncio,random
from config import *
from pip import main
try:
    from datetime import datetime
    from telethon import TelegramClient,events,sync,functions
    from telethon.tl import types
    from telethon.tl.custom.message import Message
    from telethon.tl.functions.messages import SetTypingRequest
    from apscheduler.schedulers.asyncio import AsyncIOScheduler
    from requests import post
    from pytz import timezone
except ModuleNotFoundError:
    main(['install','telethon'])
    main(['install','APScheduler==3.10.0'])
    main(['install','tzdata'])
    main(['install','pytz'])
    main(['install','datetime'])
    main(['install','requests'])
    os.system('python3 self.py')
#{ created by @amo_anon | @wrong_path }
# - - - - DATA DEFAULT - - - - #
time_default = json.dumps({'time_name':'off','time_bio':'off'})
pvblock_default = json.dumps({'lockpv':'off','block':'off','delmessage':'on'})
time_int_default = json.dumps({'1':'on','2':'off','3':'off','4':'off','5':'off','6':'off','7':'off','8':'off','9':'off','10':'off'})
mode_default = json.dumps({'boldmode':'off','italicmode':'off','codemode':'off','underline':'off','strikemode':'off','spoilermode':'off'})
action_default = json.dumps({'typing':'off','upload_photo':'off','record_video':'off','upload_video':'off','record_audio':'off','upload_audio':'off','upload_document':'off','choose_sticker':'off','record_note':'off','upload_note':'off','gamming':'off','choose_contact':'off'})
org = [":","0","1","2","3","4","5","6","7","8","9"]
enemy_list = []
mute = []
answer=[]
javab=[]
fosh_list = ['fuck your mother']
slf = TelegramClient("@amo_anon",api_id,api_hash)
db = sqlite3.connect("data.db")
conn = db.cursor()

try:
 conn.execute(f"CREATE TABLE IF NOT EXISTS SELF(time json,time_int_name json,time_int_bio json,mode json,action json,gpt varchar(3),answer varchar(3),lockpv json)")
 conn.execute("INSERT INTO SELF(time,mode,action,time_int_name,time_int_bio,answer,lockpv,gpt) SELECT ?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM SELF)",(time_default,mode_default,action_default,time_int_default,time_int_default,"off",pvblock_default,"off",))
except:
 pass
if not os.path.isfile('fontname.txt'):
 with open('fontname.txt',"w") as a:
  a.write(":𝟶𝟷𝟸𝟹𝟺𝟻𝟼𝟽𝟾𝟿")
if not os.path.isfile('fontbio.txt'):
 with open('fontbio.txt',"w") as a:
  a.write(":𝟶𝟷𝟸𝟹𝟺𝟻𝟼𝟽𝟾𝟿")
# - - - - FUNCTION - - - - #
def query(x):
 global db,conn
 return conn.execute(f"SELECT {x} FROM SELF").fetchall()[0][0]
def update_SQLITE(x,y):
 global db,conn
 conn.execute(f"UPDATE SELF SET {x}=?", (y,))
 db.commit()
async def get_me():
	myid = await slf.get_me()
	return myid.id
def gpt(text):
    false=False
    true=True
    null=None
    h = {"authority": "api.binjie.fun","method": "POST","path": "/api/generateStream","scheme": "https","accept": "application/json, text/plain, */*","accept-encoding": "gzip, deflate, br","accept-language": "en-US,en;q=0.9","content-length": "112","origin": "https://chat18.aichatos.xyz","referer": "https://chat18.aichatos.xyz/","sec-ch-ua": "\"Google Chrome\";v=\"105\", \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"105\"","sec-ch-ua-mobile": "?0","sec-ch-ua-platform": "\"Windows\"","sec-fetch-dest": "empty","sec-fetch-mode": "cors","sec-fetch-site": "cross-site","user-agent": "Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36"}
    data={"prompt":text,"userId":"#/chat/1711529530173","network":true,"system":"","withoutContext":false,"stream":false}
    return post("https://api.binjie.fun/api/generateStream",headers=h,data=data).text

def create_time(c:str):
    a = datetime.datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")
    with open(f"font{c}.txt","r") as fn:
     ran = list(fn.read())
    for char in a:
     a = a.replace(char,ran[int(org.index(str(char)))])
    return a
async def time_accs():
 db = sqlite3.connect("data.db")
 conn = db.cursor()
 a = json.loads(conn.execute(f"SELECT time FROM SELF").fetchall()[0][0])
 Amo_Anon = await slf.get_me()
 g = await slf(functions.users.GetFullUserRequest("me"))
 about = g.full_user
 try:
   if(a["time_bio"])=="on":
   	bio = create_time('bio') if str(about.about) == 'None' else about.about[:-5]+ create_time('bio')
   	await slf(functions.account.UpdateProfileRequest(about=bio))

   if(a["time_name"])=="on": 
   	name = create_time('name') if str(Amo_Anon.last_name) == 'None' else Amo_Anon.last_name[:-5]+ create_time('name')
   	await slf(functions.account.UpdateProfileRequest(last_name=name))
 except :
  pass
@slf.on(events.NewMessage(from_users='me'))
async def amo_anon(event:Message):
	global db,conn
	mode = json.loads(conn.execute(f"SELECT mode FROM SELF").fetchall()[0][0])
	message = event.message
	text = event.text
	chat_id = event.chat_id
	if text.startswith(".setenemy"):
	    try:
	        myid = await slf.get_me()
	        if event.reply_to_msg_id:
	            id = await event.get_reply_message()
	            from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	            if from_id != myid.id:
	            	if from_id not in enemy_list:
	        	    	enemy_list.append(from_id)
	        	    	await event.edit(f"<a href=tg://user?id={from_id}>† {from_id} Added To Enemy List </a>",parse_mode='html')
	            	else:
	        	        await event.edit(f"<a href=tg://user?id={from_id}>† This User {from_id} Already in Enemy List </a>",parse_mode='html')
	        else:
	    	    id = await slf.get_entity(text.split()[1])
	    	    if id.id != myid.id:
	    		    if id.id not in enemy_list:
	    			    enemy_list.append(id.id)
	    			    await event.edit(f"<a href=tg://user?id={id.id}>† {id.id} Added To Enemy List</a>",parse_mode='html')
	    		    else:
	    		        await event.edit(f"<a href=tg://user?id={id.id}>† This User {id.id} Already in Enemy List </a>",parse_mode='html')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")
	elif text == "پنل":
	   results = await slf.inline_query(idbot, 'panel')
	   message = await results[0].click(chat_id)
	elif text.startswith('.delenemy'):
		try:
			if event.reply_to_msg_id:
				id = await event.get_reply_message()
				from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
				if from_id in enemy_list:
					enemy_list.remove(from_id)
					await event.edit(f'† User {from_id} Removed From Enemy list')
				else:
					await event.edit(f'This User {from_id} is Not exist in Enemy list')
			else:
				id = await slf.get_entity(text.split()[1])
				if id.id in enemy_list:
					enemy_list.remove(id.id)
					await event.edit(f"† User {id.id}  Removed From Enemy list")
				else: 
					await event.edit(f'This User {id.id} is Not exist in Enemy list')
		except Exception as e:
			await event.edit(f"‡ Error {e}")

	elif text == '.clearenemy':
	    t,i = '',1
	    if len(enemy_list) >= 1:
	    	for id in enemy_list:
	    		info = await slf.get_entity(id)
	    		t += f"<a href=tg://user?id={id}>{i} : {info.first_name}</a>\n "
	    		i+=1
	    	await event.edit(f'Enemy list cleared : \n {t}',parse_mode='html')
	    	enemy_list.clear()
	    else:
	    	await event.edit(f'enemy list is empty')

	elif text.startswith('.setInsult'):
	    a=fosh_list.remove('fuck your mother')
	    t=text.replace(".setInsult ", "")
	    t=t.split(',')
	    for i in t:
	        fosh_list.append(i)
	    await event.edit(f'The insult list was updated')

	elif text == ".listenemy":
	    if len(enemy_list) >= 1:
	        t = ""
	        for id in enemy_list:
	        	info = await slf.get_entity(id)
	        	t += f"<a href=tg://user?id={id}>{i} : {info.first_name}</a>\n "
	        	await event.edit(f'Enemy list : \n {t}',parse_mode='html')
	    else:
	    	await event.edit(f'enemy list is empty')

	elif text.startswith(".setmute"):
	    try:
	        myid = await slf.get_me()
	        if event.reply_to_msg_id:
	            id = await event.get_reply_message()
	            from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	            if from_id != myid.id:
	            	if from_id not in mute:
	        	    	mute.append(from_id)
	        	    	await event.edit(f"† {from_id} Added To Mute List")
	            	else:
	        	        await event.edit(f"† This User {from_id} Already in Mute List")
	        else:
	    	    id = await slf.get_entity(text.split()[1])
	    	    if id.id != myid.id:
	    		    if id.id not in mute:
	    			    mute.append(id.id)
	    			    await event.edit(f"† {id.id} Added To Mute List")
	    		    else:
	    		        await event.edit(f"† This User {id.id} Already in Mute List")
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text.startswith('.delmute'):
		try:
			if event.reply_to_msg_id:
				id = await event.get_reply_message()
				from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
				if from_id in mute:
					mute.remove(from_id)
					await event.edit(f'† User {from_id} Removed From Mute list')
				else:
					await event.edit(f'This User {from_id} is Not exist in Mute list')
			else:
				id = await slf.get_entity(text.split()[1])
				if id.id in mute:
					mute.remove(id.id)
					await event.edit(f"† User {id.id}  Removed From Mute list")
				else: 
					await event.edit(f'This User {id.id} is Not exist in Mute list')
		except Exception as e:
			await event.edit(f"‡ Error {e}")

	elif text == ".listmute":
	    if len(mute) >= 1:
	        t = ""
	        for id in mute:
	        	info = await slf.get_entity(id)
	        	t += f"<a href=tg://user?id={id}>{i} : {info.first_name}</a>\n "
	        	await event.edit(f'Mute list : \n {t}',parse_mode='html')
	    else:
	    	await event.edit(f'Mute list is empty')
    
	elif text == '.clearmute':
	    t = ''
	    i = 1
	    if len(mute) >= 1:
	    	for id in mute:
	    		info = await slf.get_entity(id)
	    		t += f"<a href=tg://user?id={id}>{i} : {info.first_name}</a>\n "
	    		i+=1
	    	await event.edit(f'Mute list cleared : \n {t}',parse_mode='html')
	    	mute.clear()
	    else:
	    	await event.edit(f'mute list is empty')

	elif text.startswith('.addanswer'):
	    try:
	        an = text.replace(".addanswer","")[1::].split(":")[0]
	        en = text.replace(".addanswer","")[1::].split(":")[1]
	        answer.append(an)
	        javab.append(en)
	        await event.edit(f'Answer Successfully Added\n[{an} -> {en}]')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text.startswith('.delanswer'):
	    if text.replace(".delan" , "")[1::] in answer:
	        num = answer.index(text.replace(".delan" , "")[1::])
	        answer.remove(answer[num])
	        javab.remove(javab[num])
	        await event.edit("Successfully\nRemoved From Asnwer List") 
	    else:
	        await event.edit("This is Not in Asnwer List") 
	 
	elif text.startswith('.answerlist'):
	    a,n= "",0
	    if len(answer) >= 1:
	        for i in range(0,int(len(answer))):
	            a+= f"{n} : {answer[i]} -> {javab[i]} \n"
	        await event.edit("<b> Answer List : </b>\n {a}",parse_mode='html') 
	    else:
	        await event.edit("Answer list is empty ")
	elif text == '.answerclear':
	    if len(answer) >= 1:
	        answer.clear()
	        javab.clear()
	        event.edit("Answer List Cleared") 
	    else:
	    	event.edit("Answer List is empty")

	elif text.startswith('.setname'):
	    t=text.replace(".setname ", "")
	    await slf(functions.account.UpdateProfileRequest(first_name=t))

	elif text.startswith('.setlastname'):
	    t=text.replace(".setlastname ", "")
	    await slf(functions.account.UpdateProfileRequest(last_name=t))

	elif text.startswith('.setbio'):
	    t=text.replace(".setbio ", "")
	    await slf(functions.account.UpdateProfileRequest(about=t))

	elif text.startswith('.clone'):
	    try:
	        if event.reply_to_msg_id:
	            id = await event.get_reply_message()
	            from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	            get = await slf.get_entity(from_id)
	            first_name = get.first_name
	            last_name = get.last_name
	            g = await slf(functions.users.GetFullUserRequest(get.id))
	            about = g.full_user.about
	        else:
	            t=text.replace(".clone ", "")
	            get = await slf.get_entity(t)
	            from_id=get.id
	            first_name = get.first_name
	            last_name = get.last_name
	            g = await slf(functions.users.GetFullUserRequest(get.id))
	            about = g.full_user.about
	        await slf(functions.account.UpdateProfileRequest(first_name=first_name,last_name=last_name,about=about))
	        try:
	            photo = await slf.download_profile_photo(from_id)
	            m=await slf.upload_file(photo)
	            await slf(functions.photos.UploadProfilePhotoRequest(fallback=True,file=await slf.upload_file(m)))
	            os.remove(photo)
	        except:
	        	pass
	        await event.edit(f'The operation was performed successfully \n Name : {first_name} \n last name : {last_name} \n bio : {about}')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text.startswith('.block'):
	    try:
	        if event.reply_to_msg_id:
	            id = await event.get_reply_message()
	            from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	        else:
	            t=text.replace(".block ", "")
	            get = await slf.get_entity(t)
	            from_id=get.id
	        await slf(functions.contacts.BlockRequest(id=from_id))
	        await event.edit('<b>✅ user has been successfully blocked </b>',parse_mode='html')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text.startswith('.unblock'):
	    try:
	        if event.reply_to_msg_id:
	            id = await event.get_reply_message()
	            from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	        else:
	            t=text.replace(".unblock ", "")
	            get = await slf.get_entity(t)
	            from_id=get.id
	        await slf(functions.contacts.UnblockRequest(id=from_id))
	        await event.edit('<b>✅ user has been successfully unblocked </b>',parse_mode='html')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")
	elif text.startswith('.join'):
	    try:
	        a= text.replace('.join ','')
	        await slf(functions.channels.JoinChannelRequest(channel=a))
	        await event.edit('<b>✅ successfully joined the chat</b>',parse_mode='html')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text.startswith('.left'):
	    try:
	        a=text.replace('.left ','')
	        await slf(functions.channels.LeaveChannelRequest(channel=a))
	        await event.edit('<b>✅ successfully lefted the chat</b>',parse_mode='html')
	    except Exception as e:
	        await event.edit(f"‡ Error {e}")

	elif text=='.messageinfo':
		if event.reply_to_msg_id:
		    try:
		        i = await event.get_reply_message()
		        mid = i.id
		        user_id = i.from_id.user_id if i.from_id else i.peer_id.user_id
		        get = await slf.get_entity(user_id)
		        name= get.first_name
		        lname= get.last_name
		        await event.edit(f"**✅ Message Info : **\n  **📮Message id** : `{mid}` \n   **🆔User id** : `{user_id}` \n   **📛FirstName** : `{name}` \n   **✳️LastName** : `{lname}`",parse_mode='markdown')
		    except Exception as e:
		        await event.edit(f"‡ Error {e}")

	elif text.startswith('.info'):
	    if event.reply_to_msg_id:
	        id = await event.get_reply_message()
	        from_id = id.from_id.user_id if id.from_id else id.peer_id.user_id
	        g = await slf(functions.users.GetFullUserRequest(from_id))
	    else:
	        t=text.replace('.info ','')
	        from_id=t
	    t=""
	    m = await slf.get_entity(from_id)
	    try:
	    	for i in m.usernames:
	        	t+=f" @{i.username} |"
	    except:
	     	t = "None"
	    await event.edit(f'''**✅ user information successfully received : \nuser id : `{m.id}`\nfirst name : `{m.first_name}`\nlast name : `{m.last_name}`\nusername : `{m.username}`\nusernames : `{t}`\nuser is scam : `{m.scam}`\nuser is fake : `{m.fake}`\nuser is premium : `{m.premium}`\n**''')

	if text == '.dl' and event.reply_to_msg_id:
	    message = await event.get_reply_message()
	    await event.delete()
	    media = await slf.download_media(message)
	    await slf.send_file('me',media,caption="👺")
	    os.remove(media)

	elif text.startswith('.copy'):
		t= text.replace('.copy ','')
		n= t.split('-')
		a=await slf.get_messages(n[0],ids=int(n[1]))
		if a.media:
			media = await slf.download_media(a)
			await slf.send_file('me',media,caption=a.message)
			os.remove(media)
		else:
			await slf.send_message('me',a.message)
	elif text.startswith('.gpt'):
	     if event.reply_to_msg_id:
	          i = await event.get_reply_message()
	          await event.edit(gpt(i.text))
	     else:
	         await event.edit(gpt(text.replace('.gpt ','')))
	elif not text.startswith('.') and not text.startswith('/'):
		if (mode["boldmode"] == "on"):
			await event.edit(f"**{text}**")
		elif (mode["italicmode"] == "on"):
			await event.edit(f"__{text}__")
		elif (mode["codemode"] == "on"):
			await event.edit(f"`{text}`")
		elif (mode["underline"] == "on"):
			await event.edit(f"<u>{text}</u>")
		elif (mode["strikemode"] == "on"):
			await event.edit(f"~~{text}~~")
		elif (mode["spoilermode"] == "on"):
			await event.edit(f"||{text}||")
@slf.on(events.NewMessage())
async def online(m:Message):
    chat_id = m.chat_id
    text = m.raw_text
    from_id = m.from_id.user_id if m.from_id else m.peer_id.user_id
    global db,conn
    q=json.loads(query('lockpv'))
    if m.is_private and q['lockpv'] == "on" and from_id != await get_me():
        if q['block']=='on':
            await slf(functions.contacts.BlockRequest(id=from_id))
        elif q['delmessage']=="on":
            await m.delete()
    elif from_id in enemy_list:
        await m.reply(random.choice(fosh_list))
    elif from_id in mute:
        await m.delete()
    elif conn.execute(f"SELECT answer FROM SELF").fetchall()[0][0] == "on":
        info = await slf.get_entity(from_id)
        if text in answer and info.bot == False:
            await m.reply(javab[answer.index(text)])
    elif m.is_private and conn.execute(f"SELECT gpt FROM SELF").fetchall()[0][0] == "on" and from_id != await get_me():
        await m.reply(gpt(text))
    a = json.loads(conn.execute(f"SELECT action FROM SELF").fetchall()[0][0])
    if a['typing'] =="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageTypingAction()))
    elif a['upload_photo']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageUploadPhotoAction(1)))
    elif a['record_video']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageRecordVideoAction()))
    elif a['upload_video']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageUploadVideoAction(1)))
    elif a['record_audio']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageRecordAudioAction()))
    elif a['upload_audio']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageUploadAudioAction(1)))
    elif a['upload_document']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageUploadDocumentAction(1)))
    elif a['choose_sticker']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageChooseStickerAction()))
    elif a['gamming']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageGamePlayAction()))
    elif a['choose_contact']=="on":
	    await slf(SetTypingRequest(chat_id,types.SendMessageChooseContactAction()))


scheduler = AsyncIOScheduler()
scheduler.add_job(time_accs, "interval", seconds=60)
scheduler.start()
db.commit()
slf.start()
print ('\033[1m\033[92mbot is running . . .\n\033[31mmy telegram channel : @keext ')
slf.run_until_disconnected()


# @amo_anon . . . \