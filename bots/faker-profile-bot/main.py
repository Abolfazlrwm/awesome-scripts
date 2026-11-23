# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import os,aiosqlite,datetime,asyncio,re,random
from faker import Faker
from asyncio import sleep,create_task,gather
from telethon import events,TelegramClient,Button
from telethon.tl.custom.message import Message
from telethon.tl.functions.account import  UpdateProfileRequest
from telethon.tl.functions.channels import JoinChannelRequest
from telethon.tl.functions.messages import ImportChatInviteRequest
from telethon.errors import *

async def _load():
	sql = await aiosqlite.connect("database.db")
	cursor = await sql.cursor()

	await cursor.executescript("""
CREATE TABLE IF NOT EXISTS Users (
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT DEFAULT A,
numid INTEGER NOT NULL,
hash TEXT NOT NULL,
link TEXT NOT NULL,
message TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS Setting(
id INTEGER DEFAULT 1,
first_on INTEGER DEFAULT 1,
admins TEXT,
startlink TEXT,
search INTEGER DEFAULT 50
);

CREATE TABLE IF NOT EXISTS Session(
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT,
path TEXT,
string TEXT,
active INTEGER DEFAULT 0
);

""")

	#Set default variable
	fake = Faker("fa")
	await cursor.execute("SELECT first_on FROM Setting")
	first = await cursor.fetchone()
	if first == None:
		await cursor.execute("INSERT INTO Session (name) VALUES (?)",(f"{fake.first_name_male()}",))
		await cursor.execute("INSERT INTO Setting (id) VALUES (1)")
		await cursor.execute("UPDATE Setting SET first_on = 0 WHERE id = 1")
		await sql.commit()

#---------------------SQL-------------------------

#get database column
async def get(table, column):
	sql = await aiosqlite.connect("database.db")
	cur = await sql.cursor()
	await cur.execute(f"SELECT {column} FROM {table}")
	obj = await cur.fetchall()
	await cur.close()
	return obj

#update a colmun
async def edit(table, column,d,where,d2):
	sql = await aiosqlite.connect("database.db")
	cur = await sql.cursor()
	if type(d) == str:
		await cur.execute(f'UPDATE {table} SET {column} = "{d}" WHERE {where} = {d2}')
	elif type(d) == int:
				await cur.execute(f'UPDATE {table} SET {column} = {d} WHERE {where} = {d2}')
		
	await sql.commit()
	await cur.close()

#insert to users table
async def add_users(name,numid,hash,link,message):
	sql = await aiosqlite.connect("database.db")
	cursor = await sql.cursor()
	await cursor.execute(f'INSERT INTO Users (name,numid,hash,link,message) VALUES ("{name}",{numid},"{hash}","{link}","{message}")')
	await sql.commit()
	await sql.close()

#-------------functions------------------

api_id = 0
api_hash = "PUT-YOUR-API-HASH"
token = ""
admin = 0

#-----------------StartConfig-----------------
bot = TelegramClient("bot",api_id,api_hash)

mainB = bot.build_reply_markup([[Button.url(text="___Taha___",url="https://t.me/khodtaha")],[Button.inline("Work📝","work")],[Button.inline("Downloads⬇️",b"download"),Button.inline("Uploads⬆️","upload")],[Button.inline("Session🕶️","session")],[Button.inline("Info📊","info")]])

infoB = bot.build_reply_markup([[]])

uploadB = bot.build_reply_markup([[Button.inline("session","session_file")],[Button.inline("🔙","back")]])

downloadB = bot.build_reply_markup([[Button.inline("Database","db")],[Button.inline("Source","code"),Button.inline("Session","down_session")],[Button.inline("🔙","back")]])

workB = bot.build_reply_markup([[Button.inline("status🔮","status")],[Button.inline(" start ","start"),Button.inline(" stop ","stop")],[Button.inline("change search","ch_sea")],[Button.inline("🔙","back")]])

users = {}

new_users = 0

says = ["سلام","خوبید","سلام دوستان"]

old_link = ""

is_client = None

status = "Status:❌"

#-----------------Vars-------------------


def extract(text):
    match = re.search(r"(https:\/\/t\.me\/[\w\d_]+)", text)
    return match.group(1) if match else None

async def update_link(div:TelegramClient,link:str):
		try:
			x = await div.get_entity(link)
		except Exception as e:
			try:
				await div(ImportChatInviteRequest(extract(link)))
			except:
				pass
		x = await div.get_entity(extract(link))
		result = str(x)
		if x.megagroup == True:
			await edit(table="Setting",column="startlink",d=extract(link),where="id",d2=1)
		elif result.startswith("Chat"):
			await edit(table="Setting",column="startlink",d=extract(link),where="id",d2=1)
		
#check session 
async def check(s):
	global is_client
	clientt = TelegramClient(s,api_id,api_hash)
	try:
		if is_client:
			return True
		elif is_client == None:
			await clientt.connect()
			me = await clientt.get_me()
			name = (await get(table="Session", column="name"))[0][0]
			if me.first_name != name:
				await clientt(UpdateProfileRequest(first_name=f"{name}"))
			await clientt.disconnect()
			return True
	except Exception as e:
		return False
		
#----------------More-----------------

class Work:
	def __init__(self):
		self.client = None
		self.on = False
		self.num = 0
	async def init(self):
		global is_client
		path = (await get(table="Session",column="path"))[0][0]
		if not self.on:
			self.client = TelegramClient(path,api_id,api_hash)
			is_client = True
			self.on = True
			await self.client.connect()
	#check link
	def _privategroup(self,x:str):
		result = {"private":{"joinchat":0,"+":0},"public":0}
		if x.startswith("https://t.me/+"):
			result["private"]["+"] = 1
			return result
		elif x.startswith("https://t.me/joinchat"):
			result["private"]["joinchat"] = 1
			return result
		elif x.startswith("https://t.me/"):
			result["public"] = 1
			return result
	async def stop(self):
		global is_client,status
		await self.client.disconnect()
		is_client = False
		self.on = False
		status = "Status:❌"
	async def start(self,gp):
			#change link to hash
			link = ""
			if self._privategroup(x=gp)["private"]["joinchat"] == 1:
				link_raw = re.search(r"joinchat/([A-Za-z0-9]+)",gp)
				link = link_raw.group(1)
			elif self._privategroup(x=gp)["private"]["+"] == 1:
				Link = re.search(r"/\+([A-Za-z0-9]+)",gp)
				link = Link.group(1)
			elif self._privategroup(x=gp)["public"] == 1:
				link = gp
			#join second gp
			try:
				if self._privategroup(x=gp)["public"] == 1:
					await self.client(JoinChannelRequest(link))
				else:
					await self.client(ImportChatInviteRequest(link))
			except UserAlreadyParticipantError:
				pass
			#get chat
			chat = await self.client.get_entity(gp)
			#send random message
			#await self.client.send_message(entity=chat.id,message=random.choice(says))
			
			#work
			search_number = (await get("Setting ","search"))[0][0]
			message_number = 0
			try:
				async with self.client.takeout() as takeout:
					global status,new_users,users
					async for message in takeout.iter_messages(chat.id,wait_time=0.3):
						message_number += 1
						if message_number == search_number + 1:
							break
						if str(message.text).startswith("https://t.me"):
							await update_link(div=self.client,link=str(message.text))
						if message.sender_id is None:
							continue
						user_sea = await self.client.get_entity(message.sender_id)
						if message.sender_id not in users:
							new_users += 1
							users[message.sender_id] = []
							users[message.sender_id].append(gp)
							await add_users(user_sea.first_name,user_sea.id,user_sea.access_hash,gp,message.id)
						elif gp not in users[message.sender_id]:
							users[message.sender_id].append(gp)
							await add_users(user_sea.first_name,user_sea.id,user_sea.access_hash,gp,message.id)
						status = f"status:✅\ngroup:{gp}\nmessages:{message_number}\ntarget_message:{search_number}\nnew_users:{new_users}"
			except TakeoutInitDelayError as e:
				await bot.send_message(admin,f"Try again {e} second later ❗", buttons=mainB)
			new_users = 0
			await bot.send_message(entity=admin,message="target message end !",buttons=workB)
			
#----------------Class----------------
w = Work()
	
#start
@bot.on(events.NewMessage(from_users=admin,pattern="/start"))
async def start(ev: Message):
	await bot.send_message(entity=ev.sender_id,message=".",reply_to=ev.id,buttons=mainB)

#main query
@bot.on(events.CallbackQuery)
async def inline(ev: Message):
	if ev.data == b"info":
		await ev.edit(f"admins = {(await get('Setting','admins'))[0][0]}\nstart_group = {(await get('Setting','startlink'))[0][0]}",buttons=[[Button.inline("Set startgroup",b"setg")],[Button.inline("🔙","back")]])
	elif ev.data == b"session":
		await ev.edit(f"Active:{(await get('Session','active'))[0][0]}\nStringSession:\n```{(await get('Session','string'))[0][0]}```", buttons=[[Button.inline("check🔎","check")],[Button.inline("🔙","back")]])
	elif ev.data == b"download":
		await ev.edit("Download⬇️", buttons=downloadB)
	elif ev.data == b"upload":
		await ev.edit("Upload⬆️",buttons=uploadB)
	elif ev.data == b"session_file":
		await ev.delete()
		async with bot.conversation(admin) as conv:
			await conv.send_message("Send string session or file.")
			text = await conv.get_response()
			if not text.media:
				await edit(table="Session",column="string",d=text.message,where="id",d2=1)
				await ev.respond("saved",buttons=mainB)
			if text.media:
				path= await bot.download_media(text)
				await edit(table="Session",column="path",d=f"{path}",where="id",d2=1)
				await ev.respond("Saved",buttons=mainB)
	elif ev.data == b"work":
		await ev.edit("....................",buttons=workB)
	elif ev.data == b"back":
		await ev.edit(".",buttons=mainB)

#other query
@bot.on(events.CallbackQuery)
async def ifs(ev: Message):
	if ev.data == b"setg":
		await ev.delete()
		async with bot.conversation(admin,timeout=600) as conv:
			await conv.send_message("Send your link.")
			text = await conv.get_response()
			if text.message.startswith("https"):
				await edit(table="Setting",column="startlink",d=f"{text.message}",where="id",d2=1)
				await ev.reply("Saved.", buttons=mainB)
				conv.cancel()
			else:
				await ev.respond("Please send correct link.",buttons=mainB)
	elif ev.data == b"db":
				await bot.send_file(entity=admin,file="database.db",caption=f"MainDatabase\n{datetime.datetime.now()}")
	elif ev.data == b"code":
				await bot.send_file(entity=admin,file="main.py",caption=f"Source Code\n{datetime.datetime.now()}")
	elif ev.data == b"down_session":
				if (await get("Session","path"))[0][0] == None and (await get("Session","string"))[0][0] == None:
					await ev.edit("We not have session.",buttons=downloadB)
				elif (await get("Session","path"))[0][0] or (await get("Session","string"))[0][0]:
					try:
						await ev.delete()
						await bot.send_file(entity=admin,file=f"{(await get('Session','path'))[0][0]}", caption=f"{datetime.datetime.now()}",buttons=downloadB)
					except:
						await ev.respond(f"{(await get('Session','string'))[0][0]}",buttons=downloadB)
	elif ev.data == b"check":
			if await check(f"{(await get(table='Session',column='path'))[0][0]}") or is_client:
				await ev.answer("session is online.",alert=True)
				await edit(table="Session",column="active",d=1,where="id",d2="1")
			else:
				await ev.answer("Session is offline.",alert=True)
				await edit(table="Session",column="active",d=0,where="id",d2="1")

#work query
@bot.on(events.CallbackQuery)
async def process(ev):
				if ev.data == b"status":
					try:
						await ev.edit(f"{status}", buttons=workB)
					except MessageNotModifiedError:
						pass
				elif ev.data == b"ch_sea":
					await ev.delete()
					async with bot.conversation(admin) as conv:
						await conv.send_message("send number of search message.")
						m = await conv.get_response()
						await edit(table="Setting",column="search",d=int(m.message),where="id",d2=1)
						await ev.respond(".",buttons=workB)
				elif ev.data == b"stop":
					await w.stop()
					await ev.answer("Stoped.")
				elif ev.data == b"start":
					global old_link
					await w.init()
					gp = (await get(table="Setting", column="startlink"))[0][0]
					try:
						await ev.edit("process..", buttons=workB)
						run_while = True
						while run_while:
							await w.start(gp=gp)
							if (await get("Setting","startlink"))[0][0] == old_link:
								await ev.edit("not found a new link.")
								break
							elif (await get("Setting","startlink"))[0][0] != old_link:
								old_link = (await get("Setting","startlink"))[0][0]
								await ev.edit("Found a new link !\nbot is searching.")
								continue
					except Exception as e:
						await ev.edit(f"Failed, try again.\n{e}", buttons=workB)
				
#---------------Events-------------------
			
async def run():
	global old_link
	await asyncio.create_task(_load())
	await edit(table="Setting",column="admins",d=admin,where="id",d2=1)
	old_link = (await get("Setting","startlink"))[0][0]
	for user,gp in (await get("Users","numid,link")):
		if user not in users:
			users[user] = []
		users[user].append(gp)
	print(users)
	await bot.start(bot_token=token)
	print("running..")
	await gather(bot.run_until_disconnected())
try:
	asyncio.run(run())
except KeyboardInterrupt:
	pass

#-----------------Run-----------------------