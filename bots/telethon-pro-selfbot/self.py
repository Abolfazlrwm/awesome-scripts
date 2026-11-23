# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
try:
	import os , random , psutil , json , pytz , aiocron , asyncio , aiofiles , aiohttp , numpy
	from telethon.sync import TelegramClient , events , types
	from telethon.tl.functions.users import GetFullUserRequest
	from telethon.tl.functions.account import UpdateStatusRequest , GetAuthorizationsRequest , UpdateProfileRequest
	from telethon.tl.functions.messages import SendScreenshotNotificationRequest , SendReactionRequest
	from telethon.tl.functions.phone import CreateGroupCallRequest
	from telethon.tl.functions.photos import UploadProfilePhotoRequest , DeletePhotosRequest
	from gtts import gTTS
	from googletrans import Translator
	from pytgcalls import PyTgCalls , idle
	from pytgcalls.types import MediaStream
	from google_play_scraper import search
	from datetime import datetime , timedelta
	import matplotlib.pyplot as plt
except ModuleNotFoundError:
	os.system('pip install --upgrade pip && pip install -U telethon && pip install psutil && pip install py-tgcalls && pip install aiohttp && pip install asyncio && pip install aiocron && pip install aiofiles && pip install pytz && pip install googletrans==4.0.0-rc1 && pip install gtts && pip install google_play_scraper && pip install numpy && pip install matplotlib && clear')
	os.sys.exit('installed the required packages !')

async def get(file):
	async with aiofiles.open(file,'r') as r:
		return json.loads(await r.read())

async def put(file,data):
	async with aiofiles.open(file,'w') as w:
		await w.write(json.dumps(data))

def font(text):
	text = text.lower()
	return text.translate(text.maketrans('qwertyuiopasdfghjklzxcvbnm','ǫᴡᴇʀᴛʏᴜɪᴏᴘᴀsᴅғɢʜᴊᴋʟᴢxᴄᴠʙɴᴍ'))

async def requests(url,**kwargs):
	async with aiohttp.ClientSession() as session:
		async with session.get(url,**kwargs) as result:
			try:
				return json.loads(await result.text())
			except:
				return await result.read()

loop = asyncio.get_event_loop()

if not os.path.exists('data.json'):
	data = {'timename':'off','timebio':'off','timeprofile':'off','timecrash':'off','bot':'on','hashtag':'off','bold':'off','italic':'off','delete':'off','code':'off','underline':'off','reverse':'off','part':'off','mention':'off','spoiler':'off','comment':'on','text':'first !','typing':'off','game':'off','voice':'off','video':'off','sticker':'off','crash':[],'enemy':[]}
	loop.run_until_complete(put('data.json',data))

api_id = 0
api_hash = 'PUT-YOUR-API-HASH'
helperbot = 'helperselfbot'
bot = TelegramClient('self',api_id,api_hash,loop = loop)

async def makeClock(h,m,s,read,write):
	image = plt.imread(read)
	fig = plt.figure(figsize = (4,4),dpi = 300,facecolor = [0.2,0.2,0.2])
	ax_image = fig.add_axes([0,0,1,1])
	ax_image.axis('off')
	ax_image.imshow(image)
	axc = fig.add_axes([0.062,0.062,0.88,0.88],projection = 'polar')
	axc.cla()
	seconds = numpy.multiply(numpy.ones(5),s * 2 * numpy.pi / 60)
	minutes = numpy.multiply(numpy.ones(5),m * 2 * numpy.pi / 60) + (seconds / 60)
	hours = numpy.multiply(numpy.ones(5),h * 2 * numpy.pi / 12) + (minutes / 12)
	axc.axis('off')
	axc.set_theta_zero_location('N')
	axc.set_theta_direction(-1)
	axc.plot(hours,numpy.linspace(0.00,0.70,5),c = 'c',linewidth = 2.0)
	axc.plot(minutes,numpy.linspace(0.00,0.85,5),c = 'b',linewidth = 1.5)
	axc.plot(seconds,numpy.linspace(0.00,1.00,5),c = 'r',linewidth = 1.0)
	axc.plot(minutes,numpy.linspace(0.73,0.83,5),c = 'w',linewidth = 1.0)
	axc.plot(hours,numpy.linspace(0.60,0.68,5),c = 'w',linewidth = 1.5)
	axc.plot(seconds,numpy.linspace(0.80,0.98,5),c = 'w',linewidth = 0.5)
	axc.set_rmax(1)
	plt.savefig(write)
	return write

@aiocron.crontab('*/1 * * * *')
async def clock():
	await bot(UpdateStatusRequest(offline = False))
	js = await get('data.json')
	if js['timename'] == 'off' and js['timebio'] == 'off' and js['timeprofile'] == 'off' and js['timecrash'] == 'off':
		return
	now = datetime.now(pytz.timezone('Asia/Tehran')).strftime('%H:%M:%S')
	h,m,s = list(map(int,now.split(':')))
	time = f'【 {h}:{m} 】'
	rand = ['⓪➀➁➂➃➄➅➆➇➈','𝟎𝟏𝟐𝟑𝟒𝟓𝟔𝟕𝟖𝟗']
	fonts = time.translate(time.maketrans('0123456789',random.choice(rand)))
	if js['timecrash'] == 'on':
		if h == m:
			for from_id in js['crash']:
				await bot.send_message(from_id,f'ɪ ʟᴏᴠᴇ ʏᴏᴜ 🙂❤️ {fonts}')
	if js['timename'] == 'on':
		await bot(UpdateProfileRequest(last_name = fonts))
	if js['timebio'] == 'on':
		await bot(UpdateProfileRequest(about = f'❦ 𝒀𝒐𝒖 𝒄𝒂𝒏 𝒔𝒆𝒆 𝒎𝒚 𝒈𝒐𝒐𝒅 𝒇𝒂𝒄𝒆 𝒐𝒓 𝒎𝒚 𝒆𝒗𝒊𝒍 𝒇𝒂𝒄𝒆 ❦ {fonts}'))
	if js['timeprofile'] == 'on':
		build = await makeClock(h,m,s,'clock.jpg','oclock.jpg')
		photo = await bot.upload_file(build)
		photos = await bot.get_profile_photos('me')
		if photos:
			if datetime.now(pytz.timezone('UTC')) - photos[0].date < timedelta(minutes = 10):
				await bot(DeletePhotosRequest(id = [types.InputPhoto(id = photos[0].id,access_hash = photos[0].access_hash,file_reference = photos[0].file_reference)]))
		await bot(UploadProfilePhotoRequest(file = photo,fallback = True))

async def get_user_id(event):
	if event.is_reply:
		getMessage = await event.get_reply_message()
		id = getMessage.sender.id
	elif len(event.raw_text.split(' ')) == 2:
		try:
			user = int(event.raw_text.split(' ')[1])
		except:
			user = str(event.raw_text.split(' ')[1])
		try:
			entity = await bot.get_input_entity(user)
			id = entity.user_id
		except:
			id = None
	elif event.is_private:
		id = event.chat_id
	else:
		id = None
	return id

@bot.on(events.NewMessage(outgoing = True))
async def mode(event):
	js = await get('data.json')
	text = event.raw_text
	if text:
		try:
			if js['hashtag'] == 'on':
				new = text.replace(' ','_')
				await event.edit(f'#{new}')
			elif js['bold'] == 'on':
				await event.edit(f'<b>{text}</b>',parse_mode = 'HTML')
			elif js['italic'] == 'on':
				await event.edit(f'<i>{text}</i>',parse_mode = 'HTML')
			elif js['delete'] == 'on':
				await event.edit(f'<del>{text}</del>',parse_mode = 'HTML')
			elif js['code'] == 'on':
				await event.edit(f'<code>{text}</code>',parse_mode = 'HTML')
			elif js['underline'] == 'on':
				await event.edit(f'<u>{text}</u>',parse_mode = 'HTML')
			elif js['reverse'] == 'on':
				await event.edit(text[::-1],parse_mode = 'HTML')
			elif js['part'] == 'on':
				if len(text) > 1:
					new = ''
					for add in text:
						new += add
						if add != ' ':
							await event.edit(new,parse_mode = 'HTML')
			elif js['mention'] == 'on':
				if event.is_reply:
					try:
						getMessage = await event.get_reply_message()
						get_id = getMessage.sender.id
						await event.edit(f'<a href =\'tg://openmessage?user_id={get_id}\'>{text}</a>',parse_mode = 'HTML')
					except Exception as e:
						await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')
			elif js['spoiler'] == 'on':
				pass
		except Exception as e:
			print(e)

@bot.on(events.NewMessage())
async def updateMessage(event):
	js = await get('data.json')
	fromid = event.sender_id
	if fromid in js['enemy'] and event.is_private:
		await event.delete()
	elif fromid in js['crash'] and event.is_group:
		try:
			await bot(SendReactionRequest(peer = event.chat_id,msg_id = event.message.id,reaction = [types.ReactionEmoji(emoticon = '❤️')]))
		except:
			emoticons = ['🤍','🖤','💜','💙','💚','💛','🧡','❤️','🤎','💖']
			await event.reply(random.choice(emoticons))
		await event.forward_to('me')
	elif js['comment'] == 'on' and event.fwd_from:
		if event.fwd_from.saved_from_peer:
			if event.from_id:
				await event.reply(js['text'])
				print(event)

@bot.on(events.ChatAction)
async def chatAction(event):
	if event.user_joined:
		if event.action_message.out:
			await event.reply('ɪ\'ᴍ ᴡᴇʟᴄᴏᴍᴇᴅ !')
		else:
			await event.reply('ᴡᴇʟᴄᴏᴍᴇ ᴛᴏ ᴛʜᴇ ɢʀᴏᴜᴘ !')
	elif event.new_score:
		if event.action_message.out:
			await event.reply('😜 رکورد جدیدی رو زدم !')
		else:
			await event.reply('😉 رکورد جدید زدی ولی رکوردت به من نمیرسه !')

@bot.on(events.UserUpdate)
async def userUpdate(event):
	print(event)
	if event.is_private:
		if event.uploading:
			await bot.send_message(event.user_id,'🤔 چی داری میفرستی ؟')
		elif event.playing:
			await bot.send_message(event.user_id,'🤔 چی بازی می‌کنی ؟')

@bot.on(events.MessageEdited(outgoing = False,func = lambda e: e.is_private))
async def messageEdited(event):
	if event.message and not event.reactions:
		time = datetime.now(pytz.timezone('Asia/Tehran')).strftime('✐ %H:%M:%S ✎')
		await bot.send_message(event.chat_id,f'<a href =\'tg://openmessage?user_id={event.sender_id}\'>😅 پیامت رو در ساعت {time} ادیت زدی</a>',parse_mode = 'HTML',reply_to = event.message.id)

@bot.on(events.NewMessage())
async def sendAction(event):
	js = await get('data.json')
	for type in ['typing','game','voice','video','sticker']:
		if js[type] == 'on':
			async with bot.action(event.chat_id,type):
				await asyncio.sleep(2)

# This part has some problem from Telegram not me 😞
'''
@bot.on(events.NewMessage(outgoing = False,func = lambda e: e.is_private))
async def offline(event):
	result = await bot(GetAuthorizationsRequest())
	for i in result.authorizations:
		if i.hash != 0:
			last = datetime.now(pytz.timezone('UTC')) - i.date_active
			if int(last.total_seconds()) > 3600:
				await event.reply('😴 خوابم بعدا بیدار شدم جوابت رو میدم !')
'''

@bot.on(events.NewMessage(pattern=r'(robot|ربات)',outgoing = True))
async def roBot(event):
	await event.edit('ᴛʜᴇ ʀᴏʙᴏᴛ ɪs ᴏɴ !')

@bot.on(events.NewMessage(pattern=r'(help|راهنما)',outgoing = True))
async def help(event):
	memoryUse = psutil.Process(os.getpid()).memory_info()[0] / 1073741824
	memoryPercent = psutil.virtual_memory()[2]
	cpuPercent = psutil.cpu_percent(3)
	me = await bot.get_me()
	name = me.first_name
	js = await get('data.json')
	help = f"нelp мeɴυ {name} :\n\n⟩••• ᴛɪᴍᴇ ɴᴀᴍᴇ : {js['timename']}\n⟩••• ᴛɪᴍᴇ ʙɪᴏ : {js['timebio']}\n⟩••• ᴛɪᴍᴇ ᴘʀᴏғɪʟᴇ : {js['timeprofile']}\n⟩••• ᴛɪᴍᴇ ᴄʀᴀsʜ : {js['timecrash']}\n⟩••• ʙᴏᴛ ɴᴏᴡ ɪs : {js['bot']}\n⟩••• ʜᴀsʜᴛᴀɢ : {js['hashtag']}\n⟩••• ʙᴏʟᴅ : {js['bold']}\n⟩••• ɪᴛᴀʟɪᴄ : {js['italic']}\n⟩••• ᴅᴇʟᴇᴛᴇ : {js['delete']}\n⟩••• ᴄᴏᴅᴇ : {js['code']}\n⟩••• ᴜɴᴅᴇʀʟɪɴᴇ : {js['underline']}\n⟩••• ʀᴇᴠᴇʀsᴇ : {js['reverse']}\n⟩••• ᴘᴀʀᴛ : {js['part']}\n⟩••• ᴍᴇɴᴛɪᴏɴ : {js['mention']}\n⟩••• sᴘᴏɪʟᴇʀ : {js['spoiler']}\n⟩••• coммeɴт : {js['comment']}\n⟩••• тeхт coммeɴт : {js['text']}\n\n⟩••• ᴛʏᴘɪɴɢ : {js['typing']}\n⟩••• ɢᴀᴍᴇ : {js['game']}\n⟩••• ᴠᴏɪᴄᴇ : {js['voice']}\n⟩••• ᴠɪᴅᴇᴏ : {js['video']}\n⟩••• sᴛɪᴄᴋᴇʀ : {js['sticker']}\n\n⟩••• .timename (oɴ|oғғ)\n⟩••• .timebio (oɴ|oғғ)\n⟩••• .timeprofile (oɴ|oғғ)\n⟩••• .comment (oɴ|oғғ)\n⟩••• .commentText (тeхт)\n\n⟩••• hashtag (oɴ|oғғ)\n⟩••• bold (oɴ|oғғ)\n⟩••• italic (oɴ|oғғ)\n⟩••• delete (oɴ|oғғ)\n⟩••• code (oɴ|oғғ)\n⟩••• underline (oɴ|oғғ)\n⟩••• reverse (oɴ|oғғ)\n⟩••• part (oɴ|oғғ)\n⟩••• mention (oɴ|oғғ)\n⟩••• spoiler (oɴ|oғғ)\n\n⟩••• typing (oɴ|oғғ)\n⟩••• game (oɴ|oғғ)\n⟩••• voice (oɴ|oғғ)\n⟩••• video (oɴ|oғғ)\n⟩••• sticker (oɴ|oғғ)\n\n⟩••• .addenemy (ιd)\n⟩••• .delenemy (ιd)\n⟩••• listenemy\n⟩••• .addcrash (ιd)\n⟩••• .delcrash (ιd)\n⟩••• listcrash\n\n⟩••• fun (тeхт)\n⟩••• heart\n⟩••• tagall\n⟩••• tagadmins\n⟩••• checker (тeхт)\n⟩••• download\n\n⟩••• info (ιd)(reply)\n⟩••• status\n⟩••• .clean (ιɴт)\n\n• ᴍᴇᴍᴏʀʏ ᴜsᴇᴅ : {memoryUse}\n• ᴍᴇᴍᴏʀʏ : {memoryPercent} %\n• ᴄᴘᴜ : {cpuPercent} %"
	await bot.send_message(event.chat_id,help,reply_to = event.message.id)
	results = await bot.inline_query('like','ＤＯ ＹＯＵ ＬＩＫＥ ＭＹ ＲＯＢＯＴ ? ')
	await results[0].click(event.chat_id)

@bot.on(events.NewMessage(pattern=r'(panel|پنل)',outgoing = True))
async def panel(event):
	await event.edit('⟩••• ᴏᴘᴇɴɪɴɢ ᴛʜᴇ ᴘᴀɴᴇʟ !')
	results = await bot.inline_query(helperbot,'panel')
	await results[0].click(event.chat_id)

@bot.on(events.NewMessage(pattern=r'(xo|دوز)',outgoing = True))
async def xo(event):
	await event.edit('⟩••• ᴏᴘᴇɴɪɴɢ ᴛʜᴇ xᴏ !')
	results = await bot.inline_query(helperbot,'xo')
	await results[0].click(event.chat_id)

@bot.on(events.NewMessage(pattern=r'(dice|تاس) (1|2|3|4|5|6)',outgoing = True))
async def dice(event):
	input_str = event.pattern_match.group(2)
	await event.delete()
	send = await bot.send_file(event.chat_id,types.InputMediaDice('🎲'))
	while(send.media.value != int(input_str)):
		await bot.delete_messages(event.chat_id,send.id)
		send = await bot.send_file(event.chat_id,types.InputMediaDice('🎲'))

@bot.on(events.NewMessage(pattern=r'(fun|فان) (.*)',outgoing = True))
async def fun(event):
	input_str = event.pattern_match.group(2)
	if input_str in 'love':
		emoticons = ['🤍','🖤','💜','💙','💚','💛','🧡','❤️','🤎','💖']
	elif input_str in 'oclock':
		emoticons = ['🕐','🕑','🕒','🕓','🕔','🕕','🕖','🕗','🕘','🕙','🕚','🕛','🕜','🕝','🕞','🕟','🕠','🕡','🕢','🕣','🕤','🕥','🕦','🕧']
	elif input_str in 'star':
		emoticons = ['💥','⚡️','✨','🌟','⭐️','💫']
	elif input_str in 'snow':
		emoticons = ['❄️','☃️','⛄️']
	random.shuffle(emoticons)
	for emoji in emoticons:
		await asyncio.sleep(1)
		await event.edit(emoji)

@bot.on(events.NewMessage(pattern=r'(heart|قلب)',outgoing = True))
async def heart(event):
	for x in range(1,4):
		for i in range(1,11):
			await event.edit('➣ ' + str(x) + ' ❦' * i + ' | ' + str(10 * i) + '%')

@bot.on(events.NewMessage(pattern=r'(clean|حذف) (\d+)',outgoing = True))
async def clean(event):
	input_str = event.pattern_match.group(2)
	async for message in bot.iter_messages(event.chat_id,limit = int(input_str)):
		await bot.delete_messages(event.chat_id,message.id)
	await bot.send_message(event.chat_id,f'{input_str} мeѕѕαɢeѕ were deleтe . . . !')

@bot.on(events.NewMessage(pattern=r'(addcrash|افزودن کراش)',outgoing = True))
async def addCrash(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	js = await get('data.json')
	if get_id in js['crash']:
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ᴡᴀs ɪɴ crαѕн ʟɪsᴛ !')
	else:
		js['crash'].append(get_id)
		await put('data.json',js)
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ɴᴏᴡ ɪɴ crαѕн ʟɪsᴛ !')

@bot.on(events.NewMessage(pattern=r'(delcrash|حذف کراش)',outgoing = True))
async def delCrash(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	js = await get('data.json')
	if get_id in js['crash']:
		js['crash'].remove(get_id)
		await put('data.json',js)
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ᴅᴇʟᴇᴛᴇᴅ ғʀᴏᴍ crαѕн ʟɪsᴛ !')
	else:
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ɪs ɴᴏᴛ ɪɴ ᴛʜᴇ crαѕн ʟɪsᴛ !')

@bot.on(events.NewMessage(pattern=r'(listcrash|لیست کراش)',outgoing = True))
async def listCrash(event):
	txt = 'crαѕн ʟɪsᴛ :\n'
	js = await get('data.json')
	for i in js['crash']:
		txt += f'\n• [{i}](tg://user?id={i})'
	await event.edit(txt)

@bot.on(events.NewMessage(pattern=r'(addenemy|افزودن انمی)',outgoing = True))
async def addEnemy(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	js = await get('data.json')
	if get_id in js['enemy']:
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ᴡᴀs ɪɴ ᴇɴᴇᴍʏ ʟɪsᴛ !')
	else:
		js['enemy'].append(get_id)
		await put('data.json',js)
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ɴᴏᴡ ɪɴ ᴇɴᴇᴍʏ ʟɪsᴛ !')

@bot.on(events.NewMessage(pattern=r'(delenemy|حذف انمی)',outgoing = True))
async def delEnemy(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	js = await get('data.json')
	if get_id in js['enemy']:
		js['enemy'].remove(get_id)
		await put('data.json',js)
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ᴅᴇʟᴇᴛᴇᴅ ғʀᴏᴍ ᴇɴᴇᴍʏ ʟɪsᴛ !')
	else:
		await event.edit(f'• [ᴜsᴇʀ](tg://user?id={get_id}) ɪs ɴᴏᴛ ɪɴ ᴛʜᴇ ᴇɴᴇᴍʏ ʟɪsᴛ !')

@bot.on(events.NewMessage(pattern=r'(listenemy|لیست انمی)',outgoing = True))
async def listEnemy(event):
	txt = 'ᴇɴᴇᴍʏ ʟɪsᴛ :\n'
	js = await get('data.json')
	for i in js['enemy']:
		txt += f'\n• [{i}](tg://user?id={i})'
	await event.edit(txt)

@bot.on(events.NewMessage(pattern=r'\.timename (on|off)',outgoing = True))
async def timeName(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['timename'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ ᴛɪᴍᴇ ɴᴀᴍᴇ ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'\.timebio (on|off)',outgoing = True))
async def timeBio(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['timebio'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ ᴛɪᴍᴇ ʙɪᴏ ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'\.timeprofile (on|off)',outgoing = True))
async def timeProfile(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['timeprofile'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ ᴛɪᴍᴇ ᴘʀᴏғɪʟᴇ ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'\.timecrash (on|off)',outgoing = True))
async def timeCrash(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['timecrash'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ ᴛɪᴍᴇ ʙɪᴏ ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'\.comment (on|off)',outgoing = True))
async def comment(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['comment'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ coммeɴт ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'\.commentText (.*)',outgoing = True))
async def commentText(event):
	input_str = event.pattern_match.group(1)
	js = await get('data.json')
	js['text'] = str(input_str)
	await put('data.json',js)
	await event.edit(f'⟩••• ᴛʜᴇ coммeɴт тeхт ɴᴏᴡ ɪs {input_str}')

@bot.on(events.NewMessage(pattern=r'(tagall|تگ)',outgoing = True,func = lambda e: e.is_group))
async def tagAll(event):
	mentions = '✅ آخرین افراد آنلاین گروه'
	chat = await event.get_input_chat()
	async for x in bot.iter_participants(chat,100):
		mentions += f'\n [{x.first_name}](tg://user?id={x.id})'
	await event.reply(mentions)
	await event.delete()

@bot.on(events.NewMessage(pattern=r'(tagadmins|تگ ادمین ها)',outgoing = True,func = lambda e: e.is_group))
async def tagAdmins(event):
	mentions = '⚡️ تگ کردن ادمین ها'
	chat = await event.get_input_chat()
	async for x in bot.iter_participants(chat,filter = types.ChannelParticipantsAdmins):
		mentions += f'\n [{x.first_name}](tg://user?id={x.id})'
	await event.reply(mentions)
	await event.delete()

@bot.on(events.NewMessage(pattern=r'(report|گزارش)',func = lambda e: e.is_group and e.is_reply))
async def report(event):
	mentions = 'ʏᴏᴜʀ ʀᴇᴘᴏʀᴛ ʜᴀs ʙᴇᴇɴ sᴜᴄᴄᴇssғᴜʟʟʏ sᴜʙᴍɪᴛᴛᴇᴅ !'
	chat = await event.get_input_chat()
	async for x in bot.iter_participants(chat,filter = types.ChannelParticipantsAdmins):
		mentions += u'[\u2066]' + f'(tg://user?id={x.id})'
	await event.reply(mentions)

@bot.on(events.NewMessage(pattern=r'(checker|چکر) (\d+)',outgoing = True))
async def checker(event):
	input_str = event.pattern_match.group(2)
	req = await requests(f'https://MTproto.in/API/checker.php?phone={input_str}')
	await event.edit(f"𝄞 ᴘʜᴏɴᴇ ➣ {input_str}\n𝄞 sᴛᴀᴛᴜs ➣ {req['ok']}\n𝄞 ʀᴇsᴜʟᴛs ➣ {req['results']}")

@bot.on(events.NewMessage(pattern=r'(gamee|گیم|gamebot|game) (.*) (\d+)',outgoing = True))
async def gamee(event):
	url = event.pattern_match.group(2)
	score = event.pattern_match.group(3)
	req = await requests(f'https://MTproto.in/API/' + ('gamebot' if 'tbot.xyz' in url else 'gamee') + '.php?score={score}&url={url}')
	print(req)
	await event.edit(f"𝄞 sᴄᴏʀᴇ ➣ {score}\n𝄞 sᴛᴀᴛᴜs ➣ {req['ok']}")

@bot.on(events.NewMessage(pattern=r'(qrcode|کیو آر کد) (.*)',outgoing = True))
async def qrcode(event):
	text = event.pattern_match.group(2).replace(' ','+')
	await bot.send_file(event.chat_id,file = f'https://MTProto.in/API/qrcode.php?text={text}',caption = 'ʏᴏᴜʀ ǫʀ ᴄᴏᴅᴇ ɪs ʀᴇᴀᴅʏ !')

@bot.on(events.NewMessage(pattern=r'(captcha|کپچا) (.*)',outgoing = True))
async def captcha(event):
	text = event.pattern_match.group(2).replace(' ','+')
	await bot.send_file(event.chat_id,file = f'https://MTProto.in/API/captcha.php?text={text}',caption = 'ʏᴏᴜʀ ᴄᴀᴘᴛᴄʜᴀ ᴄᴏᴅᴇ ɪs ʀᴇᴀᴅʏ !')

@bot.on(events.NewMessage(pattern=r'(whois|هویز) (.*)',outgoing = True))
async def whois(event):
	input_str = event.pattern_match.group(2)
	req = await requests(f'https://MTproto.in/API/whois.php?domain={input_str}')
	if req['ok'] == True:
		await event.edit(f"𝄞 ʀᴇɢɪsᴛʀᴀʀ ➣ {req['results']['registrar']}\n𝄞 ᴡʜᴏɪs sᴇʀᴠᴇʀ ➣ {req['results']['whois_server']}\n𝄞 ʀᴇғᴇʀʀᴀʟ ᴜʀʟ ➣ {req['results']['referral_url']}\n𝄞 ᴜᴘᴅᴀᴛᴇᴅ ᴅᴀᴛᴇ ➣ {req['results']['updated_date']}\n𝄞 ᴄʀᴇᴀᴛɪᴏɴ ᴅᴀᴛᴇ ➣ {req['results']['creation_date']}\n𝄞 ᴇxᴘɪʀᴀᴛɪᴏɴ ᴅᴀᴛᴇ ➣ {req['results']['expiration_date']}\n𝄞 ɴᴀᴍᴇ sᴇʀᴠᴇʀs ➣ {req['results']['name_servers']}\n𝄞 ᴇᴍᴀɪʟs ➣ {req['results']['emails']}\n𝄞 ᴅɴssᴇᴄ ➣ {req['results']['dnssec']}\n𝄞 ɴᴀᴍᴇ ➣ {req['results']['name']}\n𝄞 ᴏʀɢ ➣ {req['results']['org']}\n𝄞 ᴀᴅᴅʀᴇss ➣ {req['results']['address']}\n𝄞 ᴄɪᴛʏ ➣ {req['results']['city']}\n𝄞 ʀᴇɢɪsᴛʀᴀɴᴛ ᴘᴏsᴛᴀʟ ᴄᴏᴅᴇ ➣ {req['results']['registrant_postal_code']}\n𝄞 ᴄᴏᴜɴᴛʀʏ ➣ {req['results']['country']}")
	else:
		await event.edit(f'⟩••• ᴛʜᴇ ᴅᴏᴍᴀɪɴ ɪs ɪɴᴠᴀʟɪᴅ !')

@bot.on(events.NewMessage(pattern=r'(whisper|نجوا) (.*)',outgoing = True))
async def whisper(event):
	input_str = event.pattern_match.group(2)
	await event.delete()
	if event.is_reply:
		getMessage = await event.get_reply_message()
		get_id = getMessage.sender.id
		results = await bot.inline_query('whisperbot',f'{input_str} {get_id}')
		await results[0].click(event.chat_id)

	elif event.is_private:
		get_id = event.chat_id
		results = await bot.inline_query('whisperbot',f'{input_str} {get_id}')
		await results[0].click(event.chat_id)

@bot.on(events.NewMessage(pattern=r'(info|اطلاعات)',outgoing = True))
async def info(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	full = await bot(GetFullUserRequest(get_id))
	first_name = full.users[0].first_name
	last_name = full.users[0].last_name
	username = full.users[0].username
	phone = full.users[0].phone
	about = full.full_user.about
	photos = await bot.get_profile_photos(get_id)
	time = datetime.now(pytz.timezone('Asia/Tehran')).strftime('ᴛɪᴍᴇ : %H:%M:%S')
	txt = f'υѕer ιd : {get_id}\nғιrѕт ɴαмe : {first_name}\nlαѕт ɴαмe : {last_name}\nυѕerɴαмe : {username}\npнoɴe : {phone}\nвιo : {about}\n{time}'
	if photos:
		await event.delete()
		await bot.send_message(event.chat_id,txt,file = photos[0])
	else:
		await event.edit(txt)

@bot.on(events.NewMessage(pattern=r'(status|وضعیت)',outgoing = True))
async def status(event):
	private_chats = 0
	bots = 0
	groups = 0
	broadcast_channels = 0
	admin_in_groups = 0
	creator_in_groups = 0
	admin_in_broadcast_channels = 0
	creator_in_channels = 0
	unread_mentions = 0
	unread = 0
	largest_group_member_count = 0
	largest_group_with_admin = 0
	async for dialog in bot.iter_dialogs():
		entity = dialog.entity
		if isinstance(entity,types.Channel):
			if entity.broadcast:
				broadcast_channels += 1
				if entity.creator or entity.admin_rights:
					admin_in_broadcast_channels += 1
				if entity.creator:
					creator_in_channels += 1
			elif entity.megagroup:
				groups += 1
				if entity.creator or entity.admin_rights:
					admin_in_groups += 1
				if entity.creator:
					creator_in_groups += 1
		elif isinstance(entity,types.User):
			private_chats += 1
			if entity.bot:
				bots += 1
		elif isinstance(entity,types.Chat):
			groups += 1
			if entity.creator or entity.admin_rights:
				admin_in_groups += 1
			if entity.creator:
				creator_in_groups += 1
		unread_mentions += dialog.unread_mentions_count
		unread += dialog.unread_count
	txt = f'ѕтαтυѕ !'
	txt += f'\nᴘʀɪᴠᴀᴛᴇ ᴄʜᴀᴛs : {private_chats}'
	txt += f'\nʙᴏᴛs : {bots}'
	txt += f'\nɢʀᴏᴜᴘs : {groups}'
	txt += f'\nʙʀᴏᴀᴅᴄᴀsᴛ ᴄʜᴀɴɴᴇʟs : {broadcast_channels}'
	txt += f'\nᴀᴅᴍɪɴ ɪɴ ɢʀᴏᴜᴘs : {admin_in_groups}'
	txt += f'\nᴄʀᴇᴀᴛᴏʀ ɪɴ ɢʀᴏᴜᴘs : {creator_in_groups}'
	txt += f'\nᴀᴅᴍɪɴ ɪɴ ʙʀᴏᴀᴅᴄᴀsᴛ ᴄʜᴀɴɴᴇʟs : {admin_in_broadcast_channels}'
	txt += f'\nᴄʀᴇᴀᴛᴏʀ ɪɴ ᴄʜᴀɴɴᴇʟs : {creator_in_channels}'
	txt += f'\nᴜɴʀᴇᴀᴅ ᴍᴇɴᴛɪᴏɴs : {unread_mentions}'
	txt += f'\nᴜɴʀᴇᴀᴅ : {unread}'
	txt += f'\nʟᴀʀɢᴇsᴛ ɢʀᴏᴜᴘ ᴍᴇᴍʙᴇʀ ᴄᴏᴜɴᴛ : {largest_group_member_count}'
	txt += f'\nʟᴀʀɢᴇsᴛ ɢʀᴏᴜᴘ ᴡɪᴛʜ ᴀᴅᴍɪɴ : {largest_group_with_admin}'
	await event.edit(txt)

@bot.on(events.NewMessage(pattern=r'(sessions|نشست های فعال)',outgoing = True))
async def sessions(event):
	result = await bot(GetAuthorizationsRequest())
	txt = f'sᴇssɪᴏɴs :\n\n'
	for i in result.authorizations:
		txt += f'ʜᴀsʜ : {i.hash}\nᴅᴇᴠɪᴄᴇ ᴍᴏᴅᴇʟ : {i.device_model}\nᴘʟᴀᴛғᴏʀᴍ : {i.platform}\nsʏsᴛᴇᴍ ᴠᴇʀsɪᴏɴ : {i.system_version}\nᴀᴘɪ ɪᴅ : {i.api_id}\nᴀᴘᴘ ɴᴀᴍᴇ : {i.app_name}\nᴀᴘᴘ ᴠᴇʀsɪᴏɴ : {i.app_version}\nᴅᴀᴛᴇ ᴄʀᴇᴀᴛᴇᴅ : {i.date_created}\nᴅᴀᴛᴇ ᴀᴄᴛɪᴠᴇ : {i.date_active}\nɪᴘ : {i.ip}\nᴄᴏᴜɴᴛʀʏ : {i.country}\n┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄┄┅┈┉┅┉┈┅┄\n'
	await event.edit(txt)

@bot.on(events.NewMessage(pattern=r'(translate|مترجم)',outgoing = True,func = lambda e: e.is_reply))
async def translate(event):
	match = event.raw_text.split(' ')
	if len(match) == 2:
		lan = str(match[1])
	else:
		lan = 'fa'
	getMessage = await event.get_reply_message()
	message = getMessage.raw_text
	try:
		translate = Translator().translate(message,lan)
		src = translate.src
		dest = translate.dest
		text = translate.text
		await event.edit(f'ᴛʀᴀɴsʟᴀᴛᴇᴅ ғʀᴏᴍ {src} ᴛᴏ {dest}\n\nᴛʀᴀɴsʟᴀᴛᴇᴅ ᴛᴇxᴛ : {text}')
		voice = gTTS(text = message,lang = src,slow = True)
		voice.save('file.mp3')
		await bot.send_file(event.chat_id,'file.mp3',voice_note = True,reply_to = event.message.id)
		os.remove('file.mp3')
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(download|دانلود)',outgoing = True,func = lambda e: e.is_reply))
async def download(event):
	try:
		await event.delete()
		message = await event.get_reply_message()
		download = await bot.download_media(message)
		await bot.send_message('me','@AsirRam',file = download)
		os.remove(download)
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(findtext|پیدا کردن متن) (.*)',outgoing = True))
async def findText(event):
	input_str = event.pattern_match.group(2)
	try:
		await event.edit(f'⟩••• sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ᴛʜᴇ ᴡᴏʀᴅ {input_str}')
		async for message in bot.iter_messages(event.chat_id,search = input_str):
			await bot.forward_messages('me',message.id,event.chat_id)
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(sendmessage|ارسال پیام) (.*)',outgoing = True,func = lambda e: e.is_reply))
async def sendMessage(event):
	input_str = timedelta(minutes = int(event.pattern_match.group(2)))
	getMessage = await event.get_reply_message()
	message = getMessage.raw_text
	try:
		await event.edit(f'⟩••• ᴍᴇssᴀɢᴇ sᴇɴᴅɪɴɢ ɪs sᴇᴛ ᴀғᴛᴇʀ {input_str}')
		await bot.send_message(event.chat_id,message,schedule = input_str)
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(myphone|شماره من)',outgoing = True))
async def myPhone(event):
	await event.delete()
	me = await bot.get_me()
	await bot.send_file(event.chat_id,types.InputMediaContact(phone_number = me.phone,first_name = me.first_name,last_name = me.last_name,vcard = ''))

@bot.on(events.NewMessage(pattern=r'(pin|پین)',outgoing = True,func = lambda e: e.is_reply))
async def pin(event):
	getMessage = await event.get_reply_message()
	await event.delete()
	await bot.pin_message(event.chat_id,getMessage,notify = True)

@bot.on(events.NewMessage(pattern=r'(unpin|آن پین)',outgoing = True))
async def unPin(event):
	await event.delete()
	await bot.unpin_message(event.chat_id)

@bot.on(events.NewMessage(pattern=r'(ban|بن)',outgoing = True,func = lambda e: e.is_group))
async def ban(event):
	get_id = await get_user_id(event)
	if not get_id:
		return await event.edit('⟩••• ᴄᴀɴ ɴᴏᴛ ғɪɴᴅ ᴛʜɪs ᴜsᴇʀ !')
	await event.delete()
	event = await bot.kick_participant(event.chat_id,get_id)
	await event.delete()

@bot.on(events.NewMessage(pattern=r'(voicecall|ویس کال) (.*)',outgoing = True,func = lambda e: e.is_group))
async def voiceCall(event):
	input_str = timedelta(minutes = int(event.pattern_match.group(2)))
	if event.is_reply:
		getMessage = await event.get_reply_message()
		title = getMessage.raw_text
	else:
		title = 'Voice Call'
	try:
		await event.edit(f'⟩••• ᴠᴏɪᴄᴇ ᴄᴀʟʟ ɪs sᴇᴛ ғᴏʀ {input_str}')
		await bot(CreateGroupCallRequest(peer = event.chat_id,title = title,schedule_date = input_str))
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(voicecallplay|ویس کال پلی)',outgoing = True,func = lambda e: e.is_reply))
async def voiceCallPlay(event):
	try:
		message = await event.get_reply_message()
		download = await bot.download_media(message)
		await event.edit(f'⟩••• ᴠᴏɪᴄᴇ ᴄᴀʟʟ ɪs ᴘʟᴀʏɪɴɢ')
		app = PyTgCalls(bot)
		await app.start()
		await app.play(event.chat_id,MediaStream(download))
		await idle()
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(spam|اسپم) (.*) (\d+)',outgoing = True))
async def spam(event):
	try:
		await event.edit(f'⟩••• sᴘᴀᴍᴍɪɴɢ ᴛʜᴇ {event.pattern_match.group(2)} ᴛᴇxᴛ {event.pattern_match.group(3)} ᴛɪᴍᴇs')
		for i in range(int(event.pattern_match.group(3))):
			await bot.send_message(event.chat_id,event.pattern_match.group(2),reply_to = event.reply_to.reply_to_msg_id if event.is_reply else None)
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(flood|فلود) (.*) (\d+)',outgoing = True))
async def flood(event):
	try:
		await event.edit(f'⟩••• ғʟᴏᴏᴅɪɴɢ ᴛʜᴇ {event.pattern_match.group(2)} ᴛᴇxᴛ {event.pattern_match.group(3)} ᴛɪᴍᴇs')
		await bot.send_message(event.chat_id,(event.pattern_match.group(2) + '\n') * int(event.pattern_match.group(3)),reply_to = event.reply_to.reply_to_msg_id if event.is_reply else None)
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(googleplay|گوگل پلی) (.*)',outgoing = True))
async def googlePlay(event):
	input_str = event.pattern_match.group(2)
	try:
		await event.edit(f'⟩••• sᴇᴀʀᴄʜɪɴɢ ғᴏʀ ᴛʜᴇ ɢᴀᴍᴇ {input_str}')
		results = search(input_str,lang = 'en',n_hits = 3)
		if results:
			for result in results:
				caption = f"ᴛɪᴛʟᴇ ➣ {result['title']}\n\nsᴄᴏʀᴇ ➣ {result['score']}\n\nɢᴇɴʀᴇ ➣ {result['genre']}\n\nᴠɪᴅᴇᴏ ➣ {result['video']}\n\nᴅᴇᴠᴇʟᴏᴘᴇʀ ➣ {result['developer']}\n\nɪɴsᴛᴀʟʟs ➣ {result['installs']}\n\nᴘʀɪᴄᴇ ➣ {result['price']}\n\nᴄᴜʀʀᴇɴᴄʏ ➣ {result['currency']}\n\nᴅᴇsᴄʀɪᴘᴛɪᴏɴ ➣ {result['description']}"
				if len(caption) > 1024:
					caption = caption[0:1021] + '...'
				await bot.send_file(event.chat_id,result['screenshots'][0],thumb = 'photo.jpg',caption = caption)
		else:
			await event.edit(f'⟩••• ᴀɴ ᴀᴘᴘʟɪᴄᴀᴛɪᴏɴ ɴᴀᴍᴇᴅ {input_str} ᴡᴀs ɴᴏᴛ ғᴏᴜɴᴅ ɪɴ ɢᴏᴏɢʟᴇ ᴘʟᴀʏ')
	except Exception as e:
		await bot.send_message('me',f'ＥＲＲＯＲ :\n\n{e}')

@bot.on(events.NewMessage(pattern=r'(screenshot|اسکرین شات)',outgoing = True))
async def screenShot(event):
	if event.is_reply:
		getMessage = await event.get_reply_message()
		message_id = getMessage.id
	else:
		message_id = event.message.id
	await event.edit(f'⟩••• ᴛᴀᴋɪɴɢ ᴀ sᴄʀᴇᴇɴsʜᴏᴛ ᴏғ ᴛʜᴇ ᴄʜᴀᴛ')
	await bot(SendScreenshotNotificationRequest(peer = event.chat_id,reply_to = types.InputReplyToMessage(reply_to_msg_id = message_id)))

@bot.on(events.NewMessage(pattern=r'(restart|ریستارت)',outgoing = True))
async def restart(event):
	await event.edit(f'⟩••• ʀᴇsᴛᴀʀᴛᴇᴅ . . . !')
	pid = os.getpid()
	filename = __file__.split('/')[-1]
	os.system(f'kill -9 {pid} && python3 {filename}')

@bot.on(events.NewMessage(pattern=r'(hashtag|bold|italic|delete|code|underline|reverse|part|mention|spoiler) (on|off)',outgoing = True))
async def editMode(event):
	match = event.raw_text.split(' ')
	js = await get('data.json')
	js[match[0]] = str(match[1])
	await put('data.json',js)
	mode = font(match[0])
	await event.edit(f'⟩••• ᴛʜᴇ {mode} ᴍᴏᴅᴇ ɴᴏᴡ ɪs {match[1]}')

@bot.on(events.NewMessage(pattern=r'(typing|game|voice|video|sticker) (on|off)',outgoing = True))
async def editAction(event):
	match = event.raw_text.split(' ')
	js = await get('data.json')
	js[match[0]] = str(match[1])
	await put('data.json',js)
	action = font(match[0])
	await event.edit(f'⟩••• ᴛʜᴇ {action} αcтιoɴ ɴᴏᴡ ɪs {match[1]}')

bot.start()
clock.start()
bot.run_until_disconnected()