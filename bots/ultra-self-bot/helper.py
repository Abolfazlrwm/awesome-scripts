# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# created by @amo_anon ! 2025 🪦
from telethon import TelegramClient,functions, events, errors
from telethon.tl import types
from telethon.tl.custom import Button
import os , sqlite3 , json 
from config import *


event = TelegramClient("Helper",api_id,api_hash)
event.start(bot_token=token)


db = sqlite3.connect("data.db")
conn = db.cursor()
action_default = json.dumps({'typing':'off','upload_photo':'off','record_video':'off','upload_video':'off','record_audio':'off','upload_audio':'off','upload_document':'off','choose_sticker':'off','record_note':'off','upload_note':'off','gamming':'off','choose_contact':'off'})
mode_default = json.dumps({'boldmode':'off','italicmode':'off','codemode':'off','underline':'off','strikemode':'off','spoilermode':'off'})
time_int_dufault = json.dumps({'1':'off','2':'off','3':'off','4':'off','5':'off','6':'off','7':'off','8':'off','9':'off','10':'off'})

def query(x):
 global db,conn
 return conn.execute(f"SELECT {x} FROM SELF").fetchall()[0][0]

def update_SQLITE(x,y):
 global db,conn
 conn.execute(f"UPDATE SELF SET {x}=?", (y,))
 db.commit()

def font_prof(y):
    x = int(y)
    amoanon=['𝟶𝟷𝟸𝟹𝟺𝟻𝟼𝟽𝟾𝟿','𝟘𝟙𝟚𝟛𝟜𝟝𝟞𝟟𝟠𝟡','０１２３４５６７８９','₀₁₂₃₄₅₆₇₈₉','⓪①②③④⑤⑥⑦⑧⑨','𝟎𝟏𝟐𝟑𝟒𝟓𝟔𝟕𝟖𝟗','𝟬𝟭𝟮𝟯𝟰𝟱𝟲𝟳𝟴𝟵','𝟶𝟷𝟸𝟹𝟺𝟻𝟼𝟽𝟾𝟿','0̷1̷2̷3̷4̷5̷6̷7̷8̷9̷','0123456789']
    return amoanon[x-1]

def mode_text(x:int=1):
	global db,conn
	if x==1:
	    mod = json.loads(query('mode'))
	    amoanon = lambda x:"✅" if mod[x]=="on"else"ꭗ"
	    return[[Button.inline('✞ ʙᴏʟᴅᴍᴏᴅᴇ '+amoanon('boldmode'),data='modeon-boldmode'),Button.inline('✞ ɪᴛᴀʟɪᴄᴍᴏᴅᴇ '+amoanon('italicmode'),data='modeon-italicmode')],
	        [Button.inline('✞ ᴄᴏᴅᴇᴍᴏᴅᴇ '+amoanon('codemode'),data='modeon-codemode'),Button.inline('✞ ᴜɴᴅᴇʀʟɪɴᴇ '+amoanon('underline'),data='modeon-underline')],
	        [Button.inline('✞ ꜱᴛʀɪᴋᴇᴍᴏᴅᴇ '+amoanon('strikemode'),data='modeon-strikemode'),Button.inline('✞ ꜱᴘᴏɪʟᴇʀᴍᴏᴅᴇ '+amoanon('spoilermode'),data='modeon-spoilermode')],
	        [Button.inline('🔙',data='back')]]
	elif x==2:
	    return '''📕 What is text mode feature ‽\n\n◄You can use this feature to automatically edit your messages in the desired mode every time you send a message. \n┈┅┅━❥━┅┅┈\n◄For example, when boldmode is active and you send a message [ Hello ], your message will be edited and converted to [ **Hello** ]  \n┈┅┅━❥━┅┅┈\nNow you can activate or deactivate your desired mode '''

def mode_action(x:int=1):
    if x==1:
    	global db,conn
    	mod = json.loads(query('action'))
    	amoanon = lambda x:"✅" if mod[x]=="on"else"ꭗ"
    	return[[Button.inline('✞ ᴛʏᴘɪɴɢ '+amoanon('typing'),data='~actins-typing'),Button.inline('✞ ᴜᴘʟᴏᴀᴅ ᴘʜᴏᴛᴏ '+amoanon('upload_photo'),data='~actins-upload_photo')],
    	    [Button.inline('✞ ʀᴇᴄᴏʀᴅ ᴠɪᴅᴇᴏ '+amoanon('record_video'),data='~actins-record_video'),Button.inline('✞ ᴜᴘʟᴏᴀᴅ ᴠɪᴅᴇᴏ '+amoanon('upload_video'),data='~actins-upload_video')],
    	    [Button.inline('✞ ʀᴇᴄᴏʀᴅ ᴀᴜᴅɪᴏ '+amoanon('record_audio'),data='~actins-record_audio'),Button.inline('✞ ᴜᴘʟᴏᴀᴅ ᴀᴜᴅɪᴏ '+amoanon('upload_audio'),data='~actins-upload_audio')],
    	    [Button.inline('✞ ᴜᴘʟᴏᴀᴅ ᴅᴏᴄᴜᴍᴇɴᴛ '+amoanon('upload_document'),data='~actins-upload_document'),Button.inline('✞ ᴄʜᴏᴏꜱᴇ ꜱᴛɪᴄᴋᴇʀ '+amoanon('choose_sticker'),data='~actins-choose_sticker')],
    	    [Button.inline('✞ ᴘʟᴀʏɪɴɢ '+amoanon('gamming'),data='~actins-gamming'),Button.inline('✞ ᴄʜᴏᴏꜱᴇ ᴄᴏɴᴛᴀᴄᴛ '+amoanon('choose_contact'),data='~actins-choose_contact')],
    	    [Button.inline('🔙',data='back')]]
    elif x==2:
        return '''action mode'''

def time_profile(x:int,c:str=''):
    global db,conn
    if x == 1:
        mod = json.loads(query('time'))
        amoanon = lambda x:"✅" if mod[x]=="on"else"ꭗ"
        return [[Button.inline('彡 ᴛɪᴍᴇ ɴᴀᴍᴇ : ',data='~timeprofile-name'),Button.inline(amoanon('time_name'),data='~timeprofile-name')],
            [Button.inline('彡 ᴛɪᴍᴇ ʙɪᴏ : ',data='~timeprofile-bio'),Button.inline(amoanon('time_bio'),data='~timeprofile-bio')],
            [Button.inline('♂ ᴄʜᴀɴɢᴇ ꜰᴏɴᴛ ɴᴀᴍᴇ',data='~chaneger-changename')],
            [Button.inline('♂ ᴄʜᴀɴɢᴇ ꜰᴏɴᴛ ʙɪᴏ',data='~chaneger-changebio')],
            [Button.inline('🔙',data='back')]]
    elif x == 2:
        qury = json.loads(query(f'time_int_{c}'))
        x = lambda x:"✅" if qury[x] == "on" else "ꭗ"
        return[[Button.inline(font_prof(1),data='~chaneger-changefont-1-'+c),Button.inline(x('1'),data='~chaneger-changefont-1-'+c)],
            [Button.inline(font_prof(2),data='~chaneger-changefont-2-'+c),Button.inline(x('2'),data='~chaneger-changefont-2-'+c)],
            [Button.inline(font_prof(3),data='~chaneger-changefont-3-'+c),Button.inline(x('3'),data='~chaneger-changefont-3-'+c)],
            [Button.inline(font_prof(4),data='~chaneger-changefont-4-'+c),Button.inline(x('4'),data='~chaneger-changefont-4-'+c)],
            [Button.inline(font_prof(5),data='~chaneger-changefont-5-'+c),Button.inline(x('5'),data='~chaneger-changefont-5-'+c)],
            [Button.inline(font_prof(6),data='~chaneger-changefont-6-'+c),Button.inline(x('6'),data='~chaneger-changefont-6-'+c)],
            [Button.inline(font_prof(7),data='~chaneger-changefont-7-'+c),Button.inline(x('7'),data='~chaneger-changefont-7-'+c)],
            [Button.inline(font_prof(8),data='~chaneger-changefont-8-'+c),Button.inline(x('8'),data='~chaneger-changefont-8-'+c)],
            [Button.inline(font_prof(9),data='~chaneger-changefont-9-'+c),Button.inline(x('9'),data='~chaneger-changefont-9-'+c)],
            [Button.inline(font_prof(10),data='~chaneger-changefont-10-'+c),Button.inline(x('10'),data='~chaneger-changefont-10-'+c)],
            [Button.inline('🔙',data='~timeprofile-')]]
    elif x==3:
        return '''📕 What does this section do ‽\n\n1⃣ TIME NAME:\nIf enabled, the time will be displayed next to your last name in your Telegram account!\n\n2⃣ TIME BIO:\nIf enabled, the time will be displayed next to your bio in your Telegram account!\n\n3⃣ CHANGE FONT NAME:\nYou can edit and customize the font of numbers and time in your last name.\n\n4⃣ CHANGE FONT BIO:\nYou can edit and customize the font of numbers and time in your bio.'''
    elif x==4:
        return f'''📕In this section you can customize the font of the clock in the last {c}''' #c = name or bio
def lockpv(x:int=1):
    if x==1:
        return '''📕 what do you do with the private chat lock section ‽ \nby activating this section , when a user messages you in private , the bot will either delete their message or block them ( ✅adjustable )'''
    elif x==2:
        x=json.loads(query('lockpv'))
        n = lambda n:"✅" if x[n]=="on" else "ꭗ"
        if x['lockpv']=="on":
        	return [[Button.inline('ʟᴏᴄᴋ ᴘᴠ '+n('lockpv'),'~lockpv-onoff')],
           	[Button.inline('ᴘᴇʀꜰᴏʀᴍᴀɴᴄᴇ ᴍᴏᴅᴇ : ','None')],
           	[Button.inline('ʙʟᴏᴄᴋ ᴜꜱᴇʀ : '+n('block'),'~lockpv-block'),Button.inline('ᴅᴇʟᴇᴛᴇ ᴍᴇꜱꜱᴀɢᴇ '+n('delmessage'),'~lockpv-delmessage')],
           	[Button.inline('🔙',data='back')]]
        else:
        	return [[Button.inline('ʟᴏᴄᴋ ᴘᴠ '+n('lockpv'),'~lockpv-onoff')],[Button.inline('🔙',data='back')]]
def enemy():
    return '''📕 What does the enemy setup section do ‽ \n\nby setting this feature on a person , when the person sends a message in a group or private chat , the bot will reply to their message and insult them \n\n🔰commands : { \n\n`.setenemy` **(REPLY OR @USERNAME)** \n\n`.delenemy` **(REPLY OR @USERNAME)** \n\n`.setInsult` **(a, b, c, d, ...)**  `->` **(Insult1, Insult2, Insult3, ...)**\n\n`.listenemy` **(None)**\n\n}'''
def mute():
    return '''📕 What does the user mute feature do ‽ \n\nby setting this feature on an individual , when that person sends a message in a group or private chat , the bot deletes their message \n**[ if they are an admin in the group and have the permission to delete messages ]**\n\n🔰 commands :\n`.setmute` **(REPLY OR @USERNAME)**\n`.delmute` **(REPLY OR @USERNAME)**\n`.clearmute`\n`.listmute` **(None)**'''
def answer(x:int=1):
    if x==1:
        return '''📕 auto reply feature does what ‽ \nby setting text and answer , you can make the bot respond with the set answer when a user sends the specified text \n\n🔰 commands :\n<code>.addanswer</code> <b>(text:text -> hi:hello)</b>\n<code>.delanswer</code> <b>(text)</b>\n<code>.answerlist</code> <b>(get answer list)</b>\n<code>.answerclear</code> <b>(None)</b>\n📚 examples :\n<code>.addanswer hi:bye</code>\nwhen a user sends hi, the bot responds with bye'''
    elif x==2:
         x = "✅" if query('answer')=="on"else "ꭗ"
         return [[Button.inline(f'✞ ᴀᴜᴛᴏ ᴀɴꜱᴡᴇʀ {x}','~autoanswer-onoff')],[Button.inline('🔙',data='back')]]
def tools_acc():
    return '''🔰 settings and account management section :

<code>.setname</code> <b>[TEXT]</b>

<code>.setlastname</code> <b>[TEXT]</b>

<code>.setbio</code> <b>[TEXT]</b>
'''
def user_tools():
    return '''
**get user information :**
`.info` **[reply or @username]**

**clone user profile :**
`.clone` **[reply or @username]**

**get message information :**
`.messageinfo` **[reply]**

**copy media and media with timestamp in savemessage :**
`.dl` **[Reply]**

**copy message in savemessage :**
`.copy` **[@username-messageid]**

**blocked user :**
`.block` **[Reply or @username]**

**unblocked user :**
`.unblock` **[Reply or @username]**

**joined chat :**
`.join` **[@username]**

**lefted chat :**
`.left` **[@username]**

'''
def GPT(x):
    q="✅" if query('gpt') == "on" else "ꭗ"
    if x == 1:
        return [[Button.inline(f'✞ ɢᴘᴛ ᴘᴠ {q}','~gpt-on')],[Button.inline('🔙',data='back')]]
    elif x == 2:
        return '**Capabilities of AI ChatGPT : **\n\n AI in private messages : When someone sends a message in a private chat the AI responds to it \n \n ➖ ➖ ➖ ➖\n`.gpt` **[TEXT] OR [REPLY]** \nAsking questions from the AI'
mark = [
    [Button.inline('✞ ᴍᴜᴛᴇ','mute'),Button.inline('✞ ᴇɴᴇᴍʏ','enemy')],
    [Button.inline('✞ ᴍᴏᴅᴇ ᴛᴇxᴛ','modetext'),Button.inline('✞ ᴍᴏᴅᴇ ᴀᴄᴛᴛɪᴏɴ','modeaction')],
    [Button.inline('✞ ᴛɪᴍᴇ ᴘʀᴏꜰɪʟᴇ','~timeprofile-'),Button.inline('✞ ᴀᴜᴛᴏ ᴀɴꜱᴡᴇʀ','~autoanswer-')],
    [Button.inline('✞ ʟᴏᴄᴋ ᴘᴠ','~lockpv-'),Button.inline('ᴀᴄᴄᴏᴜɴᴛ ᴛᴏᴏʟꜱ','~accounttools')],
    [Button.inline('✞ ᴜꜱᴇʀ ᴛᴏᴏʟꜱ','usertools'),Button.inline('✞ ᴄʜᴀᴛɢᴘᴛ','~gpt-')],
    [Button.inline('• ᴄʟᴏꜱᴇ','close')]]
back = [[Button.inline('🔙',data='back')]]

@event.on(events.InlineQuery)
async def _(__):
	if __.query.user_id in owner:
		builder = __.builder
		await __.answer([builder.article('ULTRA SELF : @AMO_ANON', text='Hello owner ! \n Welcome Im at your service 😍  \n created by : @amo_anon and @bepoool \n official channel: @wrong_path',buttons=mark)])

@event.on(events.CallbackQuery())
async def test(m):
    global db,conn
    data = m.data.decode()

    if m.original_update.user_id not in owner:
    	return 

    elif data.startswith('modeon'):
        mod = json.loads(query('mode'))
        mode = json.loads(mode_default)
        mode[data.split('-')[1]] = "off" if mod[data.split('-')[1]] == "on" else "on"
        try: 
            update_SQLITE('mode',json.dumps(mode))
            await m.edit(text=mode_text(2),buttons=mode_text())
        except Exception as e:
            await m.edit(text=f'Error : {e}')

    elif data.startswith('~actins'):
        mod = json.loads(query('action'))
        mode = json.loads(action_default)
        mode[data.split('-')[1]] = "off" if mod[data.split('-')[1]] == "on" else "on"
        try: 
            update_SQLITE('action',json.dumps(mode))
            await m.edit(text=mode_action(2),buttons=mode_action())
        except Exception as e:
            await m.edit(text=f'Error : {e}')

    elif data.startswith('~timeprofile'):
        mod = json.loads(query('time'))
        if data.split("-")[1] =="name" or data.split("-")[1] == 'bio':
       	 mod[f'time_{data.split("-")[1]}'] = "off" if mod[f'time_{data.split("-")[1]}'] == 'on' else 'on'
        try:
            update_SQLITE('time',json.dumps(mod))
            await m.edit(text=time_profile(3),buttons=time_profile(1))
        except Exception as e:
            await m.edit(text=f'Error : {e}')

    elif data.startswith('~chaneger'):
        d =  'name' if data.endswith('name') else 'bio' 
        if data.split('-')[1] == 'changename' or data.split('-')[1]=='changebio':
            await m.edit(text=time_profile(4,d),buttons=time_profile(2,d))
        elif data.split('-')[1] == "changefont":
            type = data.split('-')[3]
            if type== 'name' or type=='bio':
                with open(f'font{type}.txt',"w") as f:
            	    f.write(":"+font_prof(data.split('-')[2]))
                k = json.loads(query(f'time_int_{type}'))
                t = json.loads(time_int_dufault)
                t[data.split('-')[2]] = "off" if k[data.split('-')[2]]=="on" else "on"
                try:
                    update_SQLITE(f'time_int_{type}',json.dumps(t))
                    await m.edit(text=time_profile(4,d),buttons=time_profile(2,d))
                except Exception as e:
                    await m.edit(text=f'Error : {e}')

    elif data.startswith('~autoanswer'):
        if data.split('-')[1] == 'onoff':
            x = "off" if query('answer')=="on" else "on"
            update_SQLITE('answer',x)
        await m.edit(text=answer(),buttons=answer(2),parse_mode='html')

    elif data.startswith('~lockpv-'):
        a = json.loads(query('lockpv'))
        if data.split('-')[1] == 'onoff':
            a['lockpv']="off" if a['lockpv']=="on" else "on"
        elif data.split('-')[1] == 'block':
            a['block'],a['delmessage'] = "on","off"
        elif data.split('-')[1]=='delmessage':
            a['block'],a['delmessage'] = "off","on"
        update_SQLITE(f'lockpv',json.dumps(a))
        await m.edit(text=lockpv(),buttons=lockpv(2))
    elif data.startswith('~gpt-'):
        if data.split('-')[1] == "on":
            q= "on" if query('gpt') == "off" else "off"
            update_SQLITE('gpt',q)
        await m.edit(text=GPT(2),buttons=GPT(1))
    elif data == '~accounttools':
        await m.edit(text=tools_acc(),buttons=back,parse_mode='html')

    elif data == 'enemy':
        await m.edit(text=enemy(),buttons=back)

    elif data == 'mute':
        await m.edit(text=mute(),buttons=back)

    elif data == "modetext":
        await m.edit(text=mode_text(2),buttons=mode_text())

    elif data =="usertools":
    	await m.edit(text=user_tools(),buttons=back)

    elif data == "modeaction":
        await m.edit(text=mode_action(2),buttons=mode_action())

    elif data == "back": 
         await m.edit(text=f"welcome owner 😀🥹",buttons=mark)

    elif data == "close":
         await m.edit(text="closed")

db.commit()
event.run_until_disconnected()
# end ! powered by @amo_anon [2025] - {@wrong_path}