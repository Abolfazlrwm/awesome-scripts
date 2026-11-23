-- By @XXXTENTACIONGG
local TD_ID = arg[1]
local Config = dofile('./Config/Config-'..TD_ID..'.lua')
local SUDO = {Config.SUDO_ID,Config.BotCliId}
local Full_Sudo = Config.Full_Sudo
local Sudoid = Config.Sudoid
local BotCliId = Config.BotCliId
local BotJoiner = Config.BotJoiner
local Channel = '@'..Config.Channel
local JoinToken = Config.JoinToken
local json = dofile('./libs/JSON.lua')
local serpent = dofile('./libs/serpent.lua')
local base = dofile('./libs/redis.lua')
local utf8 = dofile('./libs/utf8.lua')
local dkjson = dofile('./libs/dkjson.lua')
local http = require("socket.http")
local https = require("ssl.https")
local URL = require("socket.url")
local ltn12 = require("ltn12")
local offset = 0
local minute = 60
local hour = 3600
local day = 86400
local week = 604800
local MsgTime = os.time() - 60
local Plan1 = 2592000
local Plan2 = 7776000
local Bot_Api = 'https://api.telegram.org/bot' .. JoinToken
local session_name = 'Cli'..arg[1]
----------------------------------------------
local iP = 0
----------------
local tdlib = require('tdlib')
tdlib.set_config{
api_id = ""..base:get(TD_ID..'api_id'),
api_hash = ""..base:get(TD_ID..'api_hash'),
session_name = ''..session_name
}
local TD = tdlib.get_functions()
local need = {
process = 0
}
local function ExitCode()
if need.process > 0 then
TD.set_timer(700,ExitCode)
print('<<<< EXIT_1 >>>>')
else
os.exit()
end
end
if need.process == 0 then
TD.set_timer(1000,ExitCode)
print('<<<< EXIT_2 >>>>')
end
--------**FullSudo**--------
function is_FullSudo(msg)
local var = false
for v,user in pairs(Full_Sudo) do
if user == msg.sender_id.user_id then
var = true
end
end
if base:sismember(TD_ID..'FULLS_SUDO',msg.sender_id.user_id) then
var = true
end
if FullSudo == tonumber(msg.sender_id.user_id) then
var = true
end
return var
end
--------**Sudo**--------
function is_Sudo(msg)
local var = false
for v,user in pairs(SUDO) do
if user == (msg.sender_id.user_id) then
var = true
end
end
if base:sismember(TD_ID..'SUDO',msg.sender_id.user_id) or is_FullSudo(msg) then
var = true
end
if Sudo == tonumber(msg.sender_id.user_id) then
var = true
end
return var
end

function is_Admin(msg)
local hash = base:sismember(TD_ID..'AdminList:',msg.sender_id.user_id)
if hash or is_Sudo(msg) then
return true
else
return false
end
end

function do_notify (user, msg)
local n = notify.Notification.new(user, msg)
n:show ()
end
--------**GlobalyBan**--------
function is_GlobalyBan(user_id)
local var = false
local hash = TD_ID..'GlobalyBanned:'
local gbanned = base:sismember(hash,user_id)
if gbanned then
var = true
end
return var
end
--------**Owner**--------
function is_Owner(msg) 
local hash = base:sismember(TD_ID..'OwnerList:'..msg.chat_id,msg.sender_id.user_id)
if hash or is_Sudo(msg) then
return true
else
return false
end
end
--------**Mod**--------
function is_Mod(msg) 
local hash = base:sismember(TD_ID..'ModList:'..msg.chat_id,msg.sender_id.user_id)
if hash or is_Sudo(msg) or is_Owner(msg) then
return true
else
return false
end
end
--------**Vip**--------
function is_Vip(msg) 
local hash = base:sismember(TD_ID..'Vip:'..msg.chat_id,msg.sender_id.user_id)
if hash or is_Mod(msg) then return true
else
return false
end
end
--------**BanUser**--------
function is_Banned(chat_id,user_id)
local hash =
base:sismember(TD_ID..'BanUser:'..chat_id,user_id)
if hash then
return true
else
return false
end
end
--------**VipUser**--------
function VipUser(msg,user_id)
user_id = user_id or 00
local Mod = base:sismember(TD_ID..'ModList:'..msg.chat_id,user_id)
local Owner = base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
if Mod or Owner or Sudo then
return true
else
return false
end
end
--------**filter**--------
function is_filter(msg,value)
local list = base:smembers(TD_ID..'Filters:'..msg.chat_id)
var = false
for i=1, #list do
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'FilterSen') then
mrr619 = value:match(list[i])
else
mrr619 = value:match(' '..list[i]..' ') or value:match('^'..list[i]..' ') or value:match(' '..list[i]..'$') or value:match('^'..list[i]..'$')
end
if mrr619 then
var = true
end
end
return var
end
--------**ec_name**--------
function ec_name(name) 
Black = name
if Black then
if Black:match('_') then
Black = Black:gsub('_','')
end
if Black:match('*') then
Black = Black:gsub('*','')
end
if Black:match('`') then
Black = Black:gsub('`','')
end
return Black
end
end
--------**check_markdown**--------
function check_markdown(text)
str = text
if str:match('_') then
output = str:gsub('_',[[\_]])
elseif str:match('*') then
output = str:gsub('*','\\*')
elseif str:match('`') then
output = str:gsub('`','\\`')
else
output = str
end
return output
end
--------**MuteUser**--------
function is_MuteUser(chat_id,user_id)
local hash =  base:sismember(TD_ID..'MuteUser:'..chat_id,user_id)
if hash then
return true
else
return false
end
end
---------**KickUser**---------
function KickUser(chat_id,user_id)
local Rep = Bot_Api.. '/kickChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id
return https.request(Rep)
end
----------------------------------------------
function MuteUser(chat_id,user_id,time)
local Rep = Bot_Api.. '/restrictChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id..'&can_post_messages=false&until_date='..time
return https.request(Rep)
end
function UnRes(chat_id,user_id)
local Rep = Bot_Api.. '/restrictChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id..'&can_post_messages=true&can_add_web_page_previews=true&can_send_other_messages=true&can_send_media_messages=true'
return https.request(Rep)
end
----------------------------------------------
function getParseMode(parse_mode)
  local P = {}
  if parse_mode then
    local mode = parse_mode:lower()
    if mode == "markdown" or mode == "md" then
      P["@type"] = "textParseModeMarkdown"
    elseif mode == "html" then
      P["@type"] = "textParseModeHTML"
    end
  end
  return P
end
----------------------------------------------
function setLimit(limit, num)
  local limit = tonumber(limit)
  local number = tonumber(num or limit)
  return limit <= number and limit or number
end
------function Api_Sender------
function sendApi(chat_id, reply_to_message_id,text,markdown)
local url = Bot_Api .. '/sendMessage?chat_id=' .. chat_id .. '&text=' .. URL.escape(text)
if reply_to_message_id then
url = url .. '&reply_to_message_id=' .. reply_to_message_id
end
if markdown == 'md' or markdown == 'markdown' then
url = url..'&parse_mode=Markdown'
elseif markdown == 'html' then
url = url..'&parse_mode=HTML'
end
return https.request(url)
end
----------------------------------------------
function send_inline(chat_id,text,keyboard)
local url = Bot_Api
if keyboard then
url = url .. '/sendMessage?chat_id=' ..chat_id.. '&text='..URL.escape(text)..'&parse_mode=HTML&reply_markup='..URL.escape(json:encode(keyboard))
else
url = url .. '/sendMessage?chat_id=' ..chat_id.. '&text=' ..URL.escape(text)..'&parse_mode=HTML'
end
if markdown == 'md' or markdown == 'markdown' then
url = url..'&parse_mode=Markdown'
elseif markdown == 'html' then
url = url..'&parse_mode=HTML'
end
return https.request(url)
end
----------------------------------------------
function send(chat_id, reply_to_message_id, text, parse_mode, callback, data)
local input_message_content = {
["@type"] = "inputMessageText",
disable_web_page_preview = true,
text = {text = text},
clear_draft = false
}
TD.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, false, true, nil, callback or dl_cb, data or nil)
end
----------------------------------------------
function is_JoinChannel(msg)
if base:get(TD_ID..'joinchnl') then
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id=@'..Config.Channel..'&user_id='..msg.sender_id.user_id)
if res ~= 200 then
end
Joinchanel = json:decode(url)
if not is_GlobalyBan(msg.sender_id.user_id) and (not Joinchanel.ok or Joinchanel.result.status == "left" or Joinchanel.result.status == "kicked") and not is_Sudo(msg) then
results = TD.getUser(msg.sender_id.user_id)
bd = 'نام :【'..(results.first_name or '')..'】\nنام کاربرے :【@'..(results.usernames.editable_username or '')..'】\n\n℘ شما ابتدا باید در کانال زیر عضو شوید و سپس مجدد دستور خود را ارسال کنید\n\n℘ نکته : درصورت عضو نشدن ربات به هیچکدام از دستورات عمل نخواهد کرد.'
local keyboard = {}
keyboard.inline_keyboard = {
{
{text = '✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Config.Channel}
}
}   
send_inline(msg.chat_id,bd,keyboard)
else
return true
end
else
return true
end
end
----------------------------------------------
function getindex(t,id) 
 for i,v in pairs(t) do 
  if v == id then 
   return i 
  end 
 end 
 return nil 
end
----------------------------------------------
 function replace(value, del, find)
    del = del:gsub(
  "[%(%)%.%+%-%*%?%[%]%^%$%%]",
 "%%%1"
 ) 
    find = find:gsub(
   "[%%]", 
   "%%%%"
   ) 
    return string.gsub(
  value,
   del,
    find
    )
end
----------------------------------------------
function is_supergroup(msg)
chat_id = tostring(msg.chat_id)
if chat_id:match('^-100') then 
if not msg.is_post then
return true
end
else
return false
end
end
----------------------------------------------
function is_channel(msg)
chat_id = tostring(msg.chat_id)
if chat_id:match('^-100') then 
if msg.is_post then
return true
else
return false
end
end
end
----------------------------------------------
function is_group(msg)
chat_id= tostring(msg.chat_id)
if chat_id:match('^-100') then 
return false
elseif chat_id_:match('^-') then
return true
else
return false
end
end
----------------------------------------------
function is_private(msg)
chat_id = tostring(msg.chat_id)
if chat_id:match('^(%d+)') then
print 'Private'
return false
else
return true
end
end
----------------------------------------------
function gp_type(chat_id)
local gp_type = "pv"
local id = tostring(chat_id)
if id:match("^-100") then
gp_type = "channel"
elseif id:match("-") then
gp_type = "chat"
end
return gp_type
end
----------------------------------------------
local function run_bash(str)
local cmd = io.popen(str)
local result = cmd:read('*all')
return result
end
----------- >>Function BD_Locks<< -----------
local function lock_del(msg)
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
local function lock_kick(msg,fa)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name)  end
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nاز گروه #اخراج شد\n─┅━━━━━━━┅─\n℘ دلیل اخراج : "..fa.."",'md')
end
KickUser(msg.chat_id,msg.sender_id.user_id)
UnRes(msg.chat_id,msg.sender_id.user_id)
end
--<><>--<><>--<>
local function lock_mute(msg,fa)
local timemutemsg = tonumber(base:get(TD_ID..'mutetime:'..msg.chat_id) or 3600)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name)  end
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nبه مدت【"..timemutemsg.."】ثانیه از ارسال پیام #محدود شد\n─┅━━━━━━━┅─\n℘ دلیل محدودیت : "..fa.."","md")
end
MuteUser(msg.chat_id,msg.sender_id.user_id,msg.date+timemutemsg)
end
--<><>--<><>--<>
local function lock_silent(msg,fa)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name)  end
if not base:sismember(TD_ID..'SilentList:'..msg.chat_id,msg.sender_id.user_id) then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\n#سایلنت شد\n─┅━━━━━━━┅─\n℘ دلیل سایلنت : '..fa..'','md')
end
base:sadd(TD_ID..'SilentList:'..msg.chat_id,msg.sender_id.user_id or 00000000)
end
end
--<><>--<><>--<><>
local function lock_warn(msg,fa)
local hashwarnbd = TD_ID..msg.sender_id.user_id..':warn'
local warnhashbd = base:hget(hashwarnbd, msg.chat_id) or 1
local max_warn = tonumber(base:get(TD_ID..'max_warn:'..msg.chat_id) or 5)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name)  end
if tonumber(warnhashbd) == tonumber(max_warn) then
KickUser(msg.chat_id,msg.sender_id.user_id)
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\nبه علت گرفتن حداکثر #اخطار از گروه #اخراج شد\n℘ دلیل اخطار و اخراج : '..fa..'\n─┅━━━━━━━┅─\n● #اخطارها : '..warnhashbd..'/'..max_warn..'','md')
end
base:hdel(hashwarnbd,msg.chat_id,max_warn)
else
base:hset(hashwarnbd,msg.chat_id, tonumber(warnhashbd) +1)
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\nشما یک #اخطار دریافت کردید\n─┅━━━━━━━┅─\n℘ دلیل اخطار : '..fa..'\n● #اخطارها : '..warnhashbd..'/'..max_warn..'','md')
end
end
end
--<><>--<><>--<><>
local function lock_ban(msg,fa)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name)  end
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
--sendApi(msg.chat_id,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nاز گروه #مسدود شد\n─┅━━━━━━━┅─\n℘ دلیل مسدودیت : "..fa.."","md")
end
KickUser(msg.chat_id,msg.sender_id.user_id)
end
--<><>Msg Check >> @Mrr619<><>--
local function MsgCheck(msg,fa,Redis,Redis2)
if base:sismember(TD_ID..'Gp3:'..msg.chat_id,msg.sender_id.user_id..' حذف '..Redis2) or base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'del' then
lock_del(msg)
end
end
----------------------------------------------
function DelAllBL(msg)
local data = TD.getSupergroupFullInfo(msg.chat_id)
if tonumber(data.banned_count) ~= 0 then
TD.set_timer(20,DelAllBL,msg)
end
local result = TD.getSupergroupMembers(msg.chat_id, "ban", '' , 0 , 25 )
if result.members then
for k,v in pairs(result.members) do
TD.setChatMemberStatus(msg.chat_id, v.user_id, 'restricted', {1, 1, 1, 1, 1, 1 ,1 ,1 ,1 ,1})
end
end
end
--------------------------
function DelAllCR(msg)
local data = TD.getSupergroupFullInfo(msg.chat_id)
if tonumber(data.restricted_count) ~= 0 then
TD.set_timer(20,DelAllCR,msg)
end
local result = TD.getSupergroupMembers(msg.chat_id, "Restricted", '' , 0 , 25 )
if result.members then
for k,v in pairs(result.members) do
TD.setChatMemberStatus(msg.chat_id, v.user_id, 'restricted', {1, 1, 1, 1, 1, 1 ,1 ,1 ,1 ,1})
end
end
end
--------------------------
function DelAllDA(msg)
local result = TD.getSupergroupMembers(msg.chat_id, "Recent", '' , 0 , 200 )
if result.members then
for k,v in pairs(result.members) do
local data = app.getUser(v.user_id)
if data.type["@type"] == "userTypeDeleted" then
TD.setChatMemberStatus(msg.chat_id, data.id, 'ban')
end
end
end
end
--------------------------
function DelAllBots(msg)
local result = TD.getSupergroupMembers(msg.chat_id, "Bots", '' , 0 , 200 )
if result.members then
t = "• ربات های قابل دسترس مسدود شدند !\n\n" 
i = 0
for k,v in pairs(result.members) do
if CheckAdminGroup(msg.chat_id,v.user_id) then
User = '<a href="tg://user?id='..v.user_id..'">'..TD.getUser(v.user_id).usernames.editable_username..'</a>'
i = i + 1
t = t..i..' - '..User..' ( عدم دسترسی )\n'
else
TD.setChatMemberStatus(msg.chat_id, v.user_id, 'ban')
end
end
send(msg.chat_id,0, t,'html')
end
end
function Checkers_()
for i,key in pairs(base:smembers(TD_ID.."SuperGp")) do
TD.openChat(key)
if base:sismember(TD_ID..'Gp2:'..key,'added') 
and base:sismember(TD_ID..'Gp2:'..key,'cgmautoon') and base:get(TD_ID.."autoCgmstart"..key) then
local time = os.date("%H%M")
local start = base:get(TD_ID.."autoCgmstart"..key) or 0000
if tonumber(time) == tonumber(start) then
local url  = https.request(Bot_Api .. '/getChatMember?chat_id='..key..'&user_id='..Config.BotCliId)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_delete_messages == true then
send(key,0,'> در حال پاکسازے پیام ها...','md')
base:del(TD_ID..'Auto_Warns_:'..key)
else
if not base:get(TD_ID..'_warnclean_:'..key) then 
send(key,0,'ربات به حذف پیام ها دسترسی ندارد','md')
base:set(TD_ID..'_warnclean_:'..key,true)
base:del(TD_ID..'Auto_Warns_:'..key)
end
end 
end
end
end
TD.set_timer(7,Checkers_)
end
function cleancache()
for k,v in pairs({'animations','documents','music','photos','temp','video_notes','videos','thumbnails','voice','stickers'}) do
os.execute("rm -rf ~/blackdiamond/.TD/"..session_name.."/"..v.."/*")
end
TD.set_timer(3700,cleancache)
end
--------------START FUNCTION
function BDStartPro(msg,data)
if msg then
if msg.sender and msg.sender._ == 'messageSenderChat' then
if msg.forward_info and msg.forward_info.from_chat_id ~= 0 then
msg.sender_id.user_id = 777000
else
msg.sender_id.user_id = 1087968824
end
end

if msg.reply_to and tonumber(msg.reply_to.message_id) ~= 0 then
reply_id = tonumber(msg.reply_to.message_id)
else
reply_id = 0
end

if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'Onvan') and not is_Mod(msg) then
--TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end 
end
if msg and msg.chat_id == BotJoiner and msg.content.text and (msg.content.text.text:match('cgm (.*)')) then
group = msg.content.text.text:match('cgm (.*)')
local url  = https.request(Bot_Api .. '/getChatMember?chat_id='..group..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_delete_messages == true then
send(group,0,'> در حال پاکسازے پیام ها...','md')
else
send(group,0,'> ربات به حذف پیام ها دسترسی ندارد...','md')
end
end
local Black = (msg.content.text and msg.content.text.text)
if gp_type(msg.chat_id) == "pv" then
if msg.sender_id.user_id == 777000 and string.match(Black, 'Login code') then
local code_ = string.match(Black, '(%d+)')
local code = string.gsub(code_, '.', {['0'] = '0️⃣', ['1'] = '1️⃣', ['2'] = '2️⃣', ['3'] = '3️⃣', ['4'] = '4️⃣', ['5'] = '5️⃣', ['6'] = '6️⃣', ['7'] = '7️⃣', ['8'] = '8️⃣', ['9'] = '9️⃣'})
TD.closeChat(Sudoid)
TD.openChat(Sudoid)
TD.createPrivateChat(Sudoid)
send(Sudoid,0,'پیامی از طرف تلگرام : \n'..code)
end end
local function setprofile(arg)
local data = TD.getMessage(arg.chat_id,arg.reply_to_message_id)
if data and data.content.photo then 
result = TD.downloadFile(data.content.photo.sizes[#data.content.photo.sizes].photo.id,nil)
if result and result["local"].is_downloading_completed then 
os.rename(result["local"].path, "./profile.jpg")
local extra = TD.setProfilePhoto('./profile.jpg','ChatPhotoStatic',nil)
if extra._ == 'ok'then 
send(arg.chat_id, arg.id,' پروفایل با موفقیت تنظیم شد.','html')
end 
else 
TD.set_timer(1, setprofile, arg)
end 
else 
send(arg.chat_id,arg.id,"*لطفا در قالب عکس ارسال کنید*",'html')
end 
end
---------- locals
local lang = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'diamondlang')
local reportpv = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'reportpv')
local ownerslist = base:smembers(TD_ID..'OwnerList:'..msg.chat_id)
function reportowner(text)
if reportpv then
for k,v in pairs(ownerslist) do
sendApi(v,0,text,'md')
end
end
end
reporttext = 'ا┅┅──┄┄═✺═┄┄──┅┅\nدقت کنید تنظیم در خصوصے براے شما فعال باشد و در صورتے که فعال نیست با دستور (ثبت گروه) یا (setgp) در همین خصوصے ربات این قابلیت را فعال کنید.'
--------------MSG TYPE----------------
if msg.content._== "messageText" then
MsgType = 'text'
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] >> "..msg.content.text.text)
end
if msg.content["@type"] == "messageChatAddMembers" then
print("["..msg.sender_id.user_id.."] Added a User")
for i=0,#msg.content.member_user_ids do
msg.add = msg.content.member_user_ids[i]
MsgType = 'AddUser' 
end 
end
if msg.content["@type"] == "messageChatJoinByLink" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] >> Joined By link") 
MsgType = 'JoinedByLink' 
end
if msg.content["@type"] == "messageDocument" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Document")
MsgType = 'Document'
end
if msg.content["@type"] == "messageSticker" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Sticker")
MsgType = 'Sticker'
stk = msg.content.sticker.sticker.id
TD.downloadFile(stk,32)
end
if msg.content["@type"] == "messageAudio" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Audio")
MsgType = 'Audio' 
end
if msg.content["@type"] == "messageVoice" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Voice")
MsgType = 'Voice' 
end
if msg.content["@type"] == "messageVideo" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Video")
MsgType = 'Video' 
end
if msg.content["@type"] == "messageAnimation" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Gif")
MsgType = 'Gif' 
end
if msg.content["@type"] == "messageLocation" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Location")
MsgType = 'Location' 
end
if msg.content["@type"] == "messageForwardedFromUser" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a [ messageForwardedFromUser ]")
MsgType = 'messageForwardedFromUser' 
end
if msg.content["@type"] == "messageContact" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Contact")
MsgType = 'Contact' 
end
if not msg.reply_markup and msg.via_bot_user_id ~= 0 then
print(serpent.block(data))
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a MarkDown")
MsgType = 'MarkDown' 
end
if msg.content.game then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Game")
MsgType = 'Game' 
end
if msg.content["@type"] == "messagePhoto" then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] Sent a Photo")
MsgType = 'Photo'
end
--------------- >>Join Off<< ---------------
local joinoff = base:sismember(TD_ID..'Gp:'..msg.chat_id,'Join')
if MsgType == 'JoinedByLink' and joinoff and not is_Sudo(msg) then
KickUser(msg.chat_id,msg.sender_id.user_id)
end
if base:get(TD_ID..'MarkRead:on') then
TD.viewMessages(msg.chat_id, {[1] = msg.id})
end

if is_Admin(msg) then
if msg.content._ == "messageContact" then
if msg.content.contact then 
local contact = msg.content.contact 
if tonumber(contact.user_id) ~= tonumber(TD.getMe().id) then 
TD.importContacts({{phone_number = contact.phone_number,first_name = contact.first_name or '',last_name = contact.last_name or '',user_id = contact.user_id}})
local nm = '<a href="tg://user?id='..contact.user_id..'">'..contact.first_name..'</a>'
send(msg.chat_id,msg.id,'• شماره تلفن : '..nm..' با موفقیت ذخیره شد','html')
local data = TD.getMe() 
TD.sendContact(msg.chat_id,msg.id,data.phone_number,data.first_name or '',data.last_name or '',data.id)
end
end
end
end

if is_Owner(msg) then
if msg.content["@type"] == 'messagePinMessage' then
base:set(TD_ID..'Pin_id'..msg.chat_id,msg.content.message_id)
end
end
-------------Flood Check------------
local cleantime = tonumber(base:get(TD_ID..'clean:time:'..msg.chat_id) or 120)
local Forcetime = tonumber(base:get(TD_ID..'Force:Time:'..msg.chat_id) or 240)
local Forcepm = tonumber(base:get(TD_ID..'Force:Pm:'..msg.chat_id) or 2)
local NUM_MSG_MAX = tonumber(base:get(TD_ID..'Flood:Max:'..msg.chat_id) or 6)
local NUM_CH_MAX = tonumber(base:get(TD_ID..'NUM_CH_MAX:'..msg.chat_id) or 2000)
local TIME_CHECK = tonumber(base:get(TD_ID..'Flood:Time:'..msg.chat_id) or 2)
local warn = tonumber(base:get(TD_ID..'Warn:Max:'..msg.chat_id) or 5)
local Forcemax = tonumber(base:get(TD_ID..'Force:Max:'..msg.chat_id) or 10)
local added = base:get(TD_ID..'addeduser'..msg.chat_id..''..msg.sender_id.user_id) or 0
local newuser = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'force_NewUser')
----MSG TYPE----
local Black = msg.content.text and msg.content.text.text
local Black1 = msg.content.text and msg.content.text.text
local Diamondent = Black and msg.content.text.entities and msg.content.text.entities[1] and msg.content.text.entities[1].type._ == 'textEntityTypeMentionName'
if Black then
Black = Black:lower()
end
if MsgType == 'text' and Black then
if Black:match('^[/#!]') then
Black = Black:gsub('^[/#!]','')
end
end

local chat = msg.chat_id
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then
if not is_Owner(msg) then
if base:get(TD_ID..'Lock:Pin:'..chat) then
if msg.content["@type"] == 'messagePinMessage' then
print 'Pinned By Not Owner'
send(chat,msg.id,'فقط مالکان\n','md')
Unpin(chat)
local PIN_ID = base:get(TD_ID..'Pin_id'..chat)
if Pin_id then
Pin(chat, tonumber(PIN_ID), 0)
end
end
end
end
if not is_Vip(msg) then
local chat = msg.chat_id
local user = msg.sender_id.user_id
local timemutemsg = tonumber(base:get(TD_ID..'mutetime:'..msg.chat_id) or 3600)
local hashwarnbd = TD_ID..''..user..':warn'
local warnhashbd = base:hget(hashwarnbd, chat) or 1
local max_warn = tonumber(base:get(TD_ID..'max_warn:'..chat) or 5)
local DIAMOND = (msg.content.text and msg.content.text.text) or (msg.content.caption and msg.content.caption.text)
--_____________Text Msg Checks_________________
if DIAMOND then
local link = DIAMOND:match("[Tt][Ee][Ll][Ee][Gg][Rr][Aa][Mm].[Mm][Ee]/") or DIAMOND:match("[Tt][Ee][Ll][Ee][Gg][Rr][Aa][Mm].[Dd][Oo][Gg]/") or DIAMOND:match("[Tt].[Mm][Ee]/") or DIAMOND:match("[Tt][Ll][Gg][Rr][Mm].[Mm][Ee]/") or DIAMOND:match("[Tt][Ee][Ll][Ee][Gg][Rr][Aa][Pp][Hh]/") or DIAMOND:match("[Hh][Tt][Tt][Pp]://") or DIAMOND:match("[Hh][Tt][Tt][Pp][Ss]://")
local username = DIAMOND:match("@(.*)") or DIAMOND:match("@")
local tag = DIAMOND:match("#(.*)") or DIAMOND:match("#")
local persian = DIAMOND:match("[\216-\219][\128-\191]") 
local english = DIAMOND:match("[A-Z]") or DIAMOND:match("[a-z]")

local Fosh = DIAMOND:match("کص") or DIAMOND:match("کون") or DIAMOND:match("ممه") or DIAMOND:match("کیری") or DIAMOND:match("سیک") or DIAMOND:match("koni") or DIAMOND:match("کصده") or DIAMOND:match("کصکش") or DIAMOND:match("لاشی") or DIAMOND:match("بیناموس")or DIAMOND:match("جنده") or DIAMOND:match("خارکسده") or DIAMOND:match("حرومزاده") or DIAMOND:match("گاییدم") or DIAMOND:match("لیس") or DIAMOND:match("کونی") or DIAMOND:match("اوبی") or DIAMOND:match("تخم") or DIAMOND:match("kir") or DIAMOND:match("kos") or DIAMOND:match("lashi")
if Fosh then
MsgCheck(msg,'ارسال کلمات #رکیک','Fosh','فحش')
end
--<><>Mention<><>--
if Black and msg.content.text.entities and msg.content.text.entities[1] and msg.content.text.entities[1].type._ == 'textEntityTypeMentionName' then
MsgCheck(msg,'ارسال #فراخانی','Mention','فراخانی')
end
--<><>Link<><>--
if link then
MsgCheck(msg,'ارسال #لینک','Link','لینک')
end
--<><>Username<><>--
if username then
MsgCheck(msg,'ارسال #نام کاربرے','Username','یوزرنیم')
end
--<><>Tag<><>--
if tag then
MsgCheck(msg,'ارسال هَشتَگ','Hashtag','هشتگ')
end
--<><>Persian<><>--
if persian then
MsgCheck(msg,'ارسال #فارسی','Persian','فارسی')
end
--<><>English<><>--
if english then
MsgCheck(msg,'ارسال #انگلیسی','English','انگلیسی')
end
---end diamond
end
--<><>Caption<><>--
if msg.content.caption then
MsgCheck(msg,'ارسال #کَپشِن','Caption','کپشن')
end
--<><>Text<><>--
if MsgType == 'text' then
MsgCheck(msg,'ارسال #متن','Text','متن')
end
--<><>Edit<><>--
if msg.edit_date > 0 then
MsgCheck(msg,'ارسال #ویرایش پیام','Edit','ویرایش')
end
--<><>Inline<><>--
if msg.content then
if msg.reply_markup and msg.reply_markup["@type"] == "replyMarkupInlineKeyboard" then
MsgCheck(msg,'ارسال #دکمه شیشه اے','Inline','دکمه شیشه ای')
end
end
--<><>Photo<><>--
if MsgType == 'Photo' then
MsgCheck(msg,'ارسال #عکس','Photo','عکس')
end
--<><>Fwd<><>--
if msg.forward_info then
MsgCheck(msg,'ارسال #فوروارد','Forward','فوروارد')
end
--<><>Videomsg<><>--
if msg.content["@type"] == 'messageVideoNote' then
MsgCheck(msg,'ارسال #ویدیومسیج','Selfi','ویدیومسیج')
end
--<><>File<><>--
if MsgType == 'Document' then
if msg.content.document.file_name:match("[\216-\219][\128-\191]") or msg.content.caption:match("ضدفیلتر") or msg.content.caption:match("ضد فیلتر") and msg.content.document.file_name:match(".[Aa][Pp][Kk]") then
MsgCheck(msg,'ارسال #بدافزار','Malware','بدافزار')
end
MsgCheck(msg,'ارسال #فایل','Document','فایل')
end
--<><>Location<><>--
if MsgType == 'Location' then
MsgCheck(msg,'ارسال #موقعیت مکانی','Location','موقعیت مکانی')
end
--<><>Voice<><>--
if MsgType == 'Voice' then
MsgCheck(msg,'ارسال #وویس','Voice','وویس')
end
--<><>Contact<><>--
if MsgType == 'Contact' then
MsgCheck(msg,'ارسال #مخاطب','Contact','مخاطب')
end
--<><>Game<><>--
if MsgType == 'Game' then
MsgCheck(msg,'ارسال #بازے','Game','بازی')
end
--<><>Video<><>--
if MsgType == 'Video' then
MsgCheck(msg,'ارسال #فیلم','Video','فیلم')
end
--<><>Audio<><>--
if MsgType == 'Audio' then
MsgCheck(msg,'ارسال #موزیک','Audio','آهنگ')
end
--<><>Gif<><>--
if MsgType == 'Gif' then
MsgCheck(msg,'ارسال #گیف','Gif','گیف')
end
--<><>Sticker<><>--
if msg.content["@type"] == "messageSticker" then
MsgCheck(msg,'ارسال #استیکر','Sticker','استیکر')
end
--<><>ChannelPost<><>--
if msg.views ~= 0 then
MsgCheck(msg,'ارسال #پست‌کانال','Channelpost','پست کانال')
end
--<><>Flood<><>--
if base:get(TD_ID..'Lock:Flood'..chat) == 'del'  then
floodtime = tonumber(base:get(TD_ID..'Flood:Max:'..msg.chat_id)) or 5
floodmax = tonumber(base:get(TD_ID..'Flood:Time:'..msg.chat_id)) or 10
flooduser = tonumber(base:get(TD_ID..'flooduser'..user..msg.chat_id)) or 0
if flooduser > floodmax then
base:del(TD_ID..'flooduser'..user..msg.chat_id)
if base:sismember(TD_ID..'Gp3:'..chat,user..' حذف پیام‌مکرر') or base:get(TD_ID..'Lock:Flood'..chat) == 'de' then
TD.deleteChatMessagesBySender(msg.chat_id,msg.sender_id.user_id)
end
else
base:setex(TD_ID..'flooduser'..user..msg.chat_id,floodtime,flooduser+1)
end
end
--<><>Spam<><>--
if (msg.content.text and msg.content.text.text) then
num = tonumber(base:get(TD_ID..'NUM_CH_MAX:'..msg.chat_id)) or 3600
chars = utf8.len(msg.content.text.text)
if chars > num then
MsgCheck(msg,'ارسال #هرزنامه','Spam','هرزنامه')
end
end
----------BioLink-----------
if msg.sender_id.user_id then
result = TD.getUserFullInfo(msg.sender_id.user_id) 
if result and result.bio then
DiamondAbout = result.bio
else  
DiamondAbout = 'Nil'
end
if DiamondAbout:match("[Tt].[Mm][Ee]/") or DiamondAbout:match("[Tt][Ll][Gg][Rr][Mm].[Mm][Ee]/") then
MsgCheck(msg,'#داشتن لینک در بیو','Biolink','لینک بیو')
end
end
-----------------Bot-----------------
if msg.add then
result = TD.getUser(msg.add)
res = TD.getUser(msg.sender_id.user_id)
if first_name == '' then name = res.id else name = ec_name(res.first_name)  end
local banbotpm = base:sismember(TD_ID..'Gp2:'..chat,'kickbotpm')
if result.type["@type"] == "userTypeBot" then 
if base:get(TD_ID..'Lock:Bots'..chat) == 'kick' then
KickUser(chat,user)
UnRes(chat,user)
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'ban' then
KickUser(chat,user)
end
if base:sismember(TD_ID..'Gp3:'..chat,user..' حذف ربات') or base:get(TD_ID..'Lock:Bots'..chat) == 'del' then
KickUser(msg.chat_id,result.id)
result_ = TD.getSupergroupMembers(msg.chat_id,"Bots",'' ,0,200 )
if result_.members then
for k,v in pairs(result_.members) do
if tonumber(v.member_id.user_id) ~= tonumber(BotJoiner) then
TD.deleteChatMessagesBySender(msg.chat_id,v.member_id.user_id)
KickUser(msg.chat_id,v.member_id.user_id)
end
end
end
end
if not(base:get(TD_ID..'Lock:Bots'..chat) == 'ban' or base:get(TD_ID..'Lock:Bots'..chat) == 'kick') then
if base:get(TD_ID..'Lock:Bots'..chat) == 'mute' then
KickUser(chat,result.id)
MuteUser(chat,user,msg.date+timemutemsg)
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'silent' then
base:sadd(TD_ID..'MuteList:'..chat,user or 00000000)
TD.deleteMessages(chat,{[1] = msg.id})
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'warn' then
if tonumber(warnhashbd) == tonumber(max_warn) then
KickUser(chat,user)
base:hdel(hashwarnbd,chat,max_warn)
else
base:hset(hashwarnbd,chat, tonumber(warnhashbd) +1)
KickUser(chat,result.id)
end
end
end
end
end 
----------Filter------------
if Black then
if is_filter(msg,Black) then
TD.deleteMessages(msg.chat_id, {[1] = msg.id})
end 
end
--------Mute all--------
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'Mute_All') then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'Tele_Mute') then
base:sadd(TD_ID..'Mutes:'..msg.chat_id,msg.sender_id.user_id)
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
else
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
end
if base:sismember(TD_ID..'SilentList:'..msg.chat_id,msg.sender_id.user_id) then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
if base:sismember(TD_ID..'MuteList:'..msg.chat_id,msg.sender_id.user_id) then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
end
end
end
----------------Tgservice---------------------
if msg.content["@type"] == "messageChatJoinByLink" or msg.content["@type"] == "messageChatAddMembers" or msg.content["@type"] == "messageChatDeleteMember" then
if base:sismember(TD_ID..'Gp:'..msg.chat_id,'TGservice') then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
result = TD.getChatHistory(msg.chat_id,msg.id,0, 500000000)
for k,v in pairs(result.messages) do
if v.content and v.content["@type"] == "messageChatDeleteMember" or v.content["@type"] == "messageChatAddMembers" or v.content["@type"] == "messageChatJoinByLink"  then
TD.deleteMessages(msg.chat_id, {[1] = v.id})
end
end
end
end
------------Chat Type------------
if is_FullSudo(msg) then

if (Black == 'vrr') and tonumber(reply_id) > 0 then
local data = TD.getMessage(msg.chat_id,tonumber(reply_id))
send(msg.chat_id,0,serpent.block(data, {comment=false}),'html')
end


if Black and (Black:match('^setbotusername @(.*)') or Black:match('^تنظیم یوزرنیم @(.*)')) then
local username = Black:match('^setbotusername @(.*)') or Black:match('^تنظیم یوزرنیم @(.*)')
TD.setUsername(username)
send(msg.chat_id,msg.id,'✔️انجام شد...!\nیوزرنیم ربات تنظیم شد\nا─┅━━━━━━━┅─ا\nیوزرنیم جدید : @'..username,'html')
end
if Black and (Black:match('^setbotname (.*)') or Black:match('^تنظیم نام (.*)')) then
local name = Black:match('^setbotname (.*)') or Black:match('^تنظیم نام (.*)')
TD.setName(name,'')
send(msg.chat_id,msg.id,'✔️انجام شد...!\nنام ربات تنظیم شد\nا─┅━━━━━━━┅─ا\nنام جدید : '..name,'html')
end
if Black and (Black:match('^setbotbio (.*)') or Black:match('^تنظیم بیو (.*)')) then
local bio = Black:match('^setbotbio (.*)') or Black:match('^تنظیم بیو (.*)')
number = utf8.len(bio)
if number > 70 then
send(msg.chat_id,msg.id,'❌اخطار!\nتا 70 کارکتر براے بیوگرافی مجاز است\nا─┅━━━━━━━┅─ا\nتعداد کارکترهاے شما : \n'..number,'html')
else
TD.setBio(bio)
send(msg.chat_id,msg.id,'✔️انجام شد...!\nبیوگرافی ربات تنظیم شد\nا─┅━━━━━━━┅─ا\nتعداد کارکترهے بیوگرافی شما : '..number..'\nبیوگرافی جدید : '..bio,'html')
end
end
if Black == 'addapi' or Black == 'افزودن ربات مکمل' then
local add = base:smembers(TD_ID..'SuperGp')
for k,v in pairs(add) do
TD.addChatMembers(v,{[1] = BotJoiner})
end
send(msg.chat_id,msg.id,'✔️|↜ ربات مکمل باموفقیت به گروه افزوده شد\nجهت کارکرد ربات مکمل را مدیر کنید','md')
end
end
----------------- pv -------------
if gp_type(msg.chat_id) == "pv" and Black then
cmdpv = Black:match('help') or Black:match('راهنما') or Black:match('setgp') or Black:match('ثبت گروه') or Black:match('delgp') or Black:match('حذف گروه') or Black:match('delgps') or Black:match('حذف گروها') or Black:match('mygps') or Black:match('گروهای من') or Black:match('delac (.*)') or Black:match('دیلیت اکانت(.*)') or Black:match('psswd (.*)') or Black:match('رمز دیلیت اکانت (.*)') or Black:match('نرخ (.*)') or Black:match('nerch (.*)')
if not base:get(TD_ID..'block:on') and not is_Sudo(msg) then
if Black and (cmdpv) then
local spam = TD_ID..'user:' .. msg.sender_id.user_id .. ':spamer'
local msgs = tonumber(base:get(spam) or 0)
local autoblock = base:get(TD_ID..'autoblocknumber') or 5
if msgs > tonumber(autoblock) then
TD.blockUser(msg.sender_id.user_id)
end
base:setex(spam,tonumber(5),msgs+1)
end
end
end
---------------- End Pv -------------
local bd = msg.sender_id.user_id
local chat = msg.chat_id
-----End Pv Cmds-----
if  is_supergroup(msg) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then

-- start reply api msg
if Black and reply_id ~= 0 then
local result = TD.getMessage(msg.chat_id, reply_id)
local user_ = result.sender_id.user_id
local res = TD.getUser(user_)
if res.first_name == '' then 
name = res.id
else 
name = res.first_name 
end
if user_ and res.type["@type"] == "userTypeBot" and user_ ~= BotJoiner then
if is_Owner(msg) then
if (Black == 'promote' or Black == 'ترفیع') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'ModList:'..msg.chat_id,user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز قبل جزء مدیران ربات بود",'md')
else
base:sadd(TD_ID..'ModList:'..msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nبه مدیر ربات ترفیع یافت",'md')
end
end
if (Black == 'demote' or Black == 'عزل') and is_JoinChannel(msg) then
if not base:sismember(TD_ID..'ModList:'..msg.chat_id,user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nمدیر ربات نبود",'md')
else
base:srem(TD_ID..'ModList:'..msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز مقام مدیری ربات عزل شد...!",'md')
end
end

if Black == 'reset info' then
local startwarn = TD_ID..':join'..os.date("%Y/%m/%d")..':'..msg.chat_id
base:del(TD_ID..'Total:KickUser:'..msg.chat_id..':'..user_) 
base:del(TD_ID..'Total:AddUser:'..msg.chat_id..':'..user_)
base:del(TD_ID..'Total:BanUser:'..msg.chat_id..':'..user_)
base:del(TD_ID..'forceaddfor',user)
base:del(TD_ID..'addeduser'..msg.chat_id,user_,added)
base:del(startwarn,user_)
sendApi(msg.chat_id,msg.id,'#انجام شد\nاطلاعات کاربر : @'..check_markdown(res.usernames.editable_username or '')..'\n'..ec_name(res.first_name)..'\n بازنشانی شد#...!','md')
end

if (Black == 'restartpm' or Black == 'ریستارت پیام') and is_JoinChannel(msg) then
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..user_)
sendApi(msg.chat_id,0,"✦ پیام هاے امروز کاربر : ["..name.."](tg://user?id="..user_..") ری استارت شد...!",'md')
end
--end owner
end
if is_Mod(msg) then
if (Black == 'setvip' or Black == 'تنظیم ویژه') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Vip:'..msg.chat_id, user_) then
sendApi(msg.chat_id,0,"|↜ کاربر : '..user_..' از قبل در لیست افراد ویژه قرار داشت",'md')
else
sendApi(msg.chat_id,0,"⭕ کاربر : '..user_..' به لیست افراد ویژه افزوده شد",'md')
base:sadd(TD_ID..'Vip:'..msg.chat_id, user_)
end
end

if Black == 'remvip' or Black == 'حذف ویژه' and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Vip:'..msg.chat_id, user_) then
sendApi(msg.chat_id,0,"⭕ کاربر : '..user_..' از لیست افراد ویژه خارج شد",'md')
base:srem(TD_ID..'Vip:'..msg.chat_id, user_)
else
sendApi(msg.chat_id,0,"⭕ کاربر : '..user_..' از قبل در لیست افراد ویژه نبود",'md')
end
end
if (Black == 'limitpm' or Black == 'محدودیت پیام') and is_JoinChannel(msg) then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'limitpm'..user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..") به لیست محدودیت ارسال پیام در روز اضافه شد",'md')
end
if (Black == 'info' or Black == 'اینفو') and is_JoinChannel(msg) then
kick = base:get(TD_ID..'Total:KickUser:'..msg.chat_id..':'..user_) or 0
ban = base:get(TD_ID..'Total:BanUser:'..msg.chat_id..':'..user_) or 0 
add = base:get(TD_ID..'Total:AddUser:'..msg.chat_id..':'..user_) or 0
text = "✦ اطلاعات کاربر :\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n🆔 یوزرنیم : ["..res.usernames.editable_username.."](tg://user?id="..user_..")\n👤 نام : "..res.first_name.."\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nتعداد افراد اخراج شده توسط کاربر :\n【"..kick.."】\nتعداد افراد مسدود شده توسط کاربر :\n【"..ban.."】\nتعداد افراد افزوده شده توسط کاربر :\n【"..add .."】"
sendApi(msg.chat_id,0, text,'md')
end
local url  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl = json:decode(url)

if (Black == "warn" or Black == "اخطار") and tonumber(reply_id) > 0 then
if VipUser(msg,user_) then
sendApi(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید به کاربران داری مقام اخطار دهید...!",'md')
else
local hashwarn = TD_ID..msg.chat_id..':warn'
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user_) or 1
if tonumber(warnhash) == tonumber(warn) then
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == true then
KickUser(msg.chat_id,user_)
UnRes(msg.chat_id,user_)
text = "✦ اطلاعات کاربر : \n🆔 یوزرنیم : ["..res.usernames.editable_username.."](tg://user?id="..user_..")\n👤 نام : "..res.first_name.."\nا┅┅──┄┄═✺═┄┄──┅┅\nبه علت دریافت اخطار بیش از حد اخراج شد \nاخطار ها : "..warnhash.."/"..warn..""
base:hdel(hashwarn,user_, '0')
sendApi(msg.chat_id,0, text,'md')
else
sendApi(msg.chat_id, 0, "✖️اخطار های ["..name.."](tg://user?id="..user..") به حداکثر رسیده ولی ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید تا توانایی اخراج داشته باشد !', 1", 'md')
end
else
 base:hset(hashwarn,user_, tonumber(warnhash) + 1)
text = "✦ اطلاعات کاربر : \n🆔 یوزرنیم : ["..res.usernames.editable_username.."](tg://user?id="..user_..")\n👤 نام : "..res.first_name.."\nا┅┅──┄┄═✺═┄┄──┅┅\nشما یک اخطار دریافت کردید \nتعداد اخطار هاے شما : "..warnhash.."/" ..warn ..""
sendApi(msg.chat_id,0, text ,'md')
end
end
end
if Black and (Black:match('^mute (%d+)$') or Black:match('^سکوت (%d+)$')) and is_JoinChannel(msg) then
local times = Black:match('^mute (%d+)$') or Black:match('^سکوت (%d+)$')
time = times * 3600
if VipUser(msg,user_) then
sendApi(msg.chat_id,0,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید کاربران دارای مقام را سکوت کنید...!",'md')
else
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == true then
MuteUser(msg.chat_id,user_,msg.date+time)
base:sadd(TD_ID..'MuteList:'..msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nدر حالت سکوت قرار گرفت براے "..times.." ساعت⌚",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..") در حالت سکوت قرار نگرفت !\n✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !",'md')
end

end
end
if (Black == "unwarn" or Black == "حذف اخطار") and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user_) or 1
if tonumber(warnhash) == tonumber(1) then
text= "✦ کاربر : ["..name.."](tg://user?id="..user_..")\nهیچ اخطارے ندارد"
sendApi(msg.chat_id,0, text,'md')
else
local hashwarn = TD_ID..msg.chat_id..':warn'
base:hdel(hashwarn,user_,'0')
text= "✦ کاربر : ["..name.."](tg://user?id="..user_..")\nتمام اخطار هایش پاک شد"
sendApi(msg.chat_id,0, text,'md')
end
end

if Black == 'kick' or Black == 'ban' or Black == 'mute' or Black == 'unmute' or Black == 'unban' then
if statsurl.ok == false or statsurl.ok == true and statsurl.result.status == 'member' or statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == false then
sendApi(msg.chat_id,0,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == true then
if (Black == 'unlimitpm' or Black == 'رفع محدودیت پیام') and is_JoinChannel(msg) then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'limitpm'..user_)
UnRes(msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز محدودیت ارسال پیام در روز رها شد و از سکوت نیز خارج شد.",'md')
end
if (Black == 'mute' or Black == 'سکوت') and is_JoinChannel(msg) then
if VipUser(msg,user_) then
sendApi(msg.chat_id,0,"❌ #اخطار !\nا─┅━━━━━━━┅─\n✦ کاربر ["..name.."](tg://user?id="..user_..") دارای مقام میباشد شما نمیتوانید او را سکوت کنید...!",'md')
else
MuteUser(msg.chat_id,user_,0)
base:sadd(TD_ID..'MuteList:'..msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..") در حالت سکوت قرار گرفت🔇",'md')
end
end
if (Black == 'unmute' or Black == 'حذف سکوت') and is_JoinChannel(msg) then
UnRes(msg.chat_id,user_)
base:srem(TD_ID..'SilentList:'..msg.chat_id,user_)
base:srem(TD_ID..'MuteList:'..msg.chat_id,user_)
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n از حالت سکوت خارج شد🔈",'md')
end
if (Black == 'ban' or Black == 'مسدود') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
if VipUser(msg,user_) then
sendApi(msg.chat_id,0,"❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ["..name.."](tg://user?id="..user_..") دارای مقام میباشد شما نمیتوانید او را مسدود کنید...!",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز گروه مسدود شد...!",'md')
base:sadd(TD_ID..'BanUser:'..msg.chat_id,user_)
KickUser(msg.chat_id,user_)
base:incr(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id)
reportowner("|↜ کاربر : ["..name.."](tg://user?id="..user_..")\n✦ از گروه مسدود شد\nتوسط : "..msg.sender_id.user_id)
end
end
if (Black == 'unban' or Black == 'حذف مسدود') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local result_ = TD.getSupergroupMembers(msg.chat_id, "ban", '' , 0 , 25 )
for k, v in pairs(result_.members) do 
if tonumber(v.user_id) == tonumber(user_) then
UnRes(msg.chat_id,user_)
reportowner("|↜ کاربر : ["..name.."](tg://user?id="..user_..")\n✦ از لیست مسدودین حذف شد\nتوسط : "..msg.sender_id.user_id)
end
end
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n از لیست مسدودین حذف شد",'md')
base:srem(TD_ID..'BanUser:'..msg.chat_id,user_)
end
if (Black == 'kick' or Black == 'اخراج') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
if VipUser(msg,user_) then
sendApi(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید کاربران دارای مقام را اخراج...!",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n اخراج شد",'md')
KickUser(msg.chat_id,user_)
UnRes(msg.chat_id,user_)
base:incr(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id)
end
end
end
--END MODS
end
if is_Sudo(msg) then
if Black and (Black:match('^setrank (.*)$') or Black:match('^تنظیم مقام (.*)$')) then
if tonumber(user_) == tonumber(BotCliId) then
sendApi(msg.chat_id, msg.id,  '❎ من نمیتوانم پیام خودم را چک کنم','md')
return false
end
if tonumber(user_) == Sudoid then
sendApi(msg.chat_id,0,'نمیتونی به بابام لقب بدی 🖕😐','md')
return false
end
base:set(TD_ID..'rank'..user_,rank)
sendApi(msg.chat_id,0,"✦ مقام کاربر : ["..name.."](tg://user?id="..user_..") به ['..rank..']\nتغییر کرد\n",'md')
end
if Black and (Black:match('^delrank$') or Black:match('^حذف مقام$')) then
base:del(TD_ID..'rank'..user_)
sendApi(msg.chat_id,0,"✦ مقام کاربر : ["..name.."](tg://user?id="..user_..") پاک شد .",'md')
end
if Black == 'setowner' or Black == 'مالک' then
if base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز قبل در لیست مالکان ربات در گروه وجود داشت!",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nبه لیست مالکان ربات در گروه افزوده شد ..",'md')
base:sadd(TD_ID..'OwnerList:'..msg.chat_id,user_)
end
end
if Black == 'remowner' or Black == 'حذف مالک' then
if base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nاز لیست مالکان ربات در گروه حذف شد ..",'md')
base:srem(TD_ID..'OwnerList:'..msg.chat_id,user_)
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\nدر لیست مالکان ربات قرار ندارد !",'md')
end
end
if Black == 'banall' or Black == 'بن گلوبال' then
if tonumber(user_) == tonumber(Sudoid) then
sendApi(msg.chat_id,0,'❎ شما قادر به گلوبال بن کردن سودو نیستید','md')
return false
end
if base:sismember(TD_ID..'GlobalyBanned:',user_) then
sendApi(msg.chat_id,0,"✦ کاربر : \n🆔 : ["..name.."](tg://user?id="..user_..")\nدر لیست گلوبال وجود دارد",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : \n🆔 : ["..name.."](tg://user?id="..user_..")\nبه لیست گلوبال افزوده و بلاک شد !",'md')
base:sadd(TD_ID..'GlobalyBanned:',user_)
TD.blockUser(user_)
end
end
if Black == 'unbanall' or Black == 'ان بن گلوبال' then
if base:sismember(TD_ID..'GlobalyBanned:',user_) then
sendApi(msg.chat_id,0,"✦ کاربر : \n🆔 : ["..name.."](tg://user?id="..user_..")\nاز لیست گلوبال حذف شد و انبلاک شد",'md')
base:srem(TD_ID..'GlobalyBanned:',user_)
TD.unblockUser(user_)
else
sendApi(msg.chat_id,0,"✦ کاربر : \n🆔 : ["..name.."](tg://user?id="..user_..")\nدر لیست گلوبال وجود ندارد",'md')
end
end
-- end sudo
end
if is_FullSudo(msg) then
if Black == 'setsudo' or Black == 'افزودن سودو' then
if base:sismember(TD_ID..'SUDO',user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات قرار دارد",'md')
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n✔به لیست سودو هاے ربات افزوده یافت",'md')
base:sadd(TD_ID..'SUDO',user_)
end
end
if Black == 'remsudo' or Black == 'حذف سودو' then
if base:sismember(TD_ID..'SUDO',user_) then
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n✔از لیست سودو هاے ربات حذف شد",'md')
base:srem(TD_ID..'SUDO',user_)
else
sendApi(msg.chat_id,0,"✦ کاربر : ["..name.."](tg://user?id="..user_..")\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات قرار ندارد",'md')
end
end
--end fullsudo 
end
if (Black == "id" or Black == "ایدی" or Black == "آیدی") then
sendApi(msg.chat_id,0,"["..user.."](tg://user?id="..user_..")",'md')
end
end
end

if is_Owner(msg) then
if Black and (Black:match('^invite (%d+)$') or Black:match('^دعوت (%d+)$')) then
local id = Black:match('^invite (%d+)$') or Black:match('^دعوت (%d+)$')
TD.addChatMembers(msg.chat_id,{[1] = id})
send(msg.chat_id, msg.id,'> عملیات با موفقیت انجام شد.','md')
end
end

if is_Mod(msg) then
if (Black == 'cgmall' or Black == 'پاکسازی' or Black == 'پاکسازی همه پیام ها') then
local url  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..Config.BotCliId)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_delete_messages == true then
TD.deleteMessages(msg.chat_id, msg.id)
local function deleteMessages(result)
if result.messages then
for k,v in pairs(result.messages) do
TD.deleteChatMessagesBySender(v.chat_id, v.sender_id.user_id)
end
if result.messages[1] then
local result = TD.getChatHistory(msg.chat_id, result.messages[1].id, 0, 100)
deleteMessages(result)
else
send(msg.chat_id,0,'> در حال پاکسازے پیام ها...','md')
end
end
end
local result = TD.getChatHistory(msg.chat_id, msg.id, 0, 100)
deleteMessages(result)
else
send(msg.chat_id,0,'ربات به حذف پیام ها دسترسی ندارد','md')
end
end
if Black == 'cum' or Black == 'حذف پیام ها' and tonumber(reply_id) > 0 then
result = TD.getMessage(msg.chat_id,tonumber(reply_id))  
txt = "⭕ تمام پیام هاے\nکاربر : ["..result.sender_id.user_id.."](tg://user?id="..result.sender_id.user_id..") پاک شدند"
TD.deleteChatMessagesBySender(msg.chat_id,result.sender_id.user_id)
send(msg.chat_id,msg.id,txt,'md')
end
--<><><><>Cum
if Diamondent and (Black:match('^cum (.*)') or Black:match('^حذف پیام ها (.*)')) or Black and (Black:match('^cum @(.*)') or Black:match('^حذف پیام ها @(.*)') or Black:match('^cum (%d+)') or Black:match('^حذف پیام ها (%d+)')) and is_JoinChannel(msg) then
local BDSource = Black:match('^cum (.*)') or Black:match('^حذف پیام ها (.*)')
result = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^cum @(.*)') or Black:match('^حذف پیام ها @(.*)') then
mrr619 = result.id
elseif not Diamondent and Black:match('^cum (%d+)') or Black:match('^حذف پیام ها (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^cum (.*)') or Black:match('^حذف پیام ها (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
txt = "⭕ تمام پیام هاے\nکاربر : ["..BDSource.."](tg://user?id="..mrr619..") پاک شدند"
send(msg.chat_id,msg.id,txt,'md')
TD.deleteChatMessagesBySender(msg.chat_id,mrr619)
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if Black and (Black:match('^cgm (%d+)$') or Black:match('^پاکسازی (%d+)$')) then
local limit = tonumber(Black:match('^cgm (%d+)$') or Black:match('^پاکسازی (%d+)$'))
if limit > 100 then
send(msg.chat_id,msg.id,'*عددے بین * [1-100]را انتخاب ڪنید','md')
else
result = TD.getChatHistory(msg.chat_id,msg.id, 0,  limit)
for k,v in pairs(result.messages) do
TD.deleteMessages(msg.chat_id,{[1] =v.id})
end
send(msg.chat_id,msg.id,'• ('..limit..') پیام گروه پاڪ شد','md')
end
end
----------------------------------------------
end
end
----------------------------------------------
if is_Sudo(msg) then
if (Black == 'share' or Black == 'شماره ربات') then
local result = TD.getMe()
TD.sendContact(msg.chat_id,msg.id,result.phone_number,result.first_name,result.last_name or '')
end
if (Black == 'addc' or Black == 'ذخیره شماره') and tonumber(reply_id) > 0 then
local data = TD.getMessage(msg.chat_id,reply_id)
if data and data.content.contact then 
local contact = data.content.contact 
TD.importContacts({{phone_number = contact.phone_number,first_name = contact.first_name,last_name = contact.last_name or '',user_id = contact.user_id}})
local nm = '<a href="tg://user?id='..contact.user_id..'">'..contact.first_name..'</a>'
send(msg.chat_id,msg.id,'• شماره تلفن : '..nm..' با موفقیت ذخیره شد','html')
else
send(msg.chat_id,msg.id,'❎ خطا ...!\nفقط پیام های حاوی *شماره* تلفن قابل ذخیره میباشند','md')
 end
 end
if (Black == 'deleteprofile' or Black == 'حذف پروفایل') and tonumber(reply_id) == 0 then
local data = TD.getMe()
result = TD.getUserProfilePhotos(data.id, offset,limit) 
if result and result.total_count > 0 then
local e = TD.deleteProfilePhoto(result.photos[1].id) 
if e.ok then 
text = e.message 
else 
text = 'پروفایل ربات حذف شد !'
end 
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'ربات فاقد *عکس* پروفایل میباشد','html')
end
end
if (Black == 'deleteprofiles' or Black == 'حذف پروفایل ها') and tonumber(reply_id) == 0 then
local data = TD.getMe()
local result = TD.getUserProfilePhotos(data.id,offset,limit) 
if result and result.total_count > 0 then 
local listphotos = result.photos 
for i = 1 ,#listphotos do
local e = Lua.deleteProfilePhoto(result.photos[i].id)
if e.ok then
text = e.message 
else 
text = 'پروفایل های بات حذف شدند !'
end
end
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'ربات فاقد *عکس* پروفایل میباشد','html')
end
end
if Black and (Black:match('^[Ss][Ee][Tt][Uu][Ss][Ee][Rr][Nn][Aa][Mm][Ee] @(%S+)$') or Black:match('^تنظیم یوزرنیم @(%S+)$')) and tonumber(reply_id) == 0 then
local username = Black:match('^[Ss][Ee][Tt][Uu][Ss][Ee][Rr][Nn][Aa][Mm][Ee] @(%S+)$') or Black:match('^تنظیم یوزرنیم @(%S+)$')
local result = TD.setUsername(username)
if result and result.code == 429 then 
local umn = string.match(result.message,'%d+')
text = "• ربات از چک کردن برای "..umn.." ثانیه محدود میباشد !"
elseif result.message == "Username is invalid" then 
text =  "• یوزرنیم وارد شده خراب میباشد !"
elseif result.message == "USERNAME_OCCUPIED" then 
text = "• یوزرنیم وارد شده خالی نیست !"
else 
text = "• یوزرنیم ربات با موفقیت تغییر کرد !" 
end
send(msg.chat_id,msg.id,text,'html')
end
if (Black == 'deleteusername' or Black == 'حذف یوزرنیم') and tonumber(reply_id) == 0 then
TD.setUsername('')
send(msg.chat_id,msg.id,'یوزرنیم ربات با موفقیت حذف شد','html')
end
if Black and (Black:match('^[Ss][Ee][Tt][Nn][Aa][Mm][Ee] (.*)$') or Black:match('^تنظیم نام (.*)$')) and tonumber(reply_id) == 0 then
local Name = Black:match('^[Ss][Ee][Tt][Nn][Aa][Mm][Ee] (.*)$') or Black:match('^تنظیم نام (.*)$')
TD.setName(Name, '')
send(msg.chat_id,msg.id,'اسم ربات به  ['..Name..'] تغییر یافت', 'html')
end
if Black and (Black:match('^[Ss][Ee][Tt][Bb][Ii][Oo] (.*)$') or Black:match('^تنظیم بیوگرافی (.*)$')) and tonumber(reply_id) == 0 then
local biographi = Black:match('^[Ss][Ee][Tt][Bb][Ii][Oo] (.*)$') or Black:match('^تنظیم بیوگرافی (.*)$')
TD.setBio(biographi)
send(msg.chat_id,msg.id,'بیوگرافی ربات تغییر یافت به\n['..biographi..']', 'html')
end
if (Black == 'deletebio' or Black == 'حذف بیوگرافی') and tonumber(reply_id) == 0 then
TD.setBio('')
send(msg.chat_id,msg.id,'بیوگرافی ربات با موفقیت حذف شد', 'html')
end
if (Black == 'sessions' or Black == 'نشست ها') and tonumber(reply_id) == 0 then
local text = 'نشست های فعال\n' 
local result  = TD.getActiveSessions()
for i = 1, #result.sessions do
last_active = os.date('%c',result.sessions[i].last_active_date)
joined_at = os.date('%c',result.sessions[i].log_in_date) 
text = text..'نام: `'..result.sessions[i].application_name..'`\nورژن : `'..result.sessions[i].application_version..'`\nورژن سیستم : `'..result.sessions[i].system_version..'`\nمدل دستگاه : `'..result.sessions[i].device_model..'`\nاخرین بازدید : `'..last_active..'`\nپیوست : `'..joined_at..'`\nایپی : `'..result.sessions[i].ip..'`\nایدی: `'..result.sessions[i].id..'` [kill](https://t.me/share/url?url=kill '..result.sessions[i].id..')\n\n'
end
send(msg.chat_id,msg.id,text,'html')
end
if Black and (Black:match('^kill (%d+)$')) and tonumber(reply_id) == 0 then
local session_id = Black:match('^kill (%d+)$')
local result = TD.terminateSession(session_id)
if result.luatele == 'ok' then
send(msg.chat_id,msg.id,'نشست با موفقیت پاک شد','html')
end 
end
if (Black == 'setprofile' or Black == 'تنظیم پروفایل') and tonumber(reply_id) > 0 then
setprofile{chat_id = msg.chat_id,id = msg.id ,reply_to_message_id = reply_id,}
end
if Black and (Black:match('^start @(.*)') or Black:match('^استارت @(.*)')) then
local username = Black:match('^start @(.*)') or Black:match('^استارت @(.*)')
RES = TD.searchPublicChat(username)
if RES.id then
TD.sendBotStartMessage(RES.id, RES.id, "new")
send(msg.chat_id,msg.id, '✦ ربات :\n【@'..username..'】\nبا موفقیت استارت شد✅', 'html')
else 
text = '<code>ربات مورد نظر یافت نشد!</code>\nابتدا از صحت وجود یوزرنیم یا شناسه کاربرے اطمینان حاصل کنید،سپس مجدد اقدام کنید.'
send(msg.chat_id, msg.id, text, 'html')
end
end
--------
if Black and (Black1:match('^leave (-100)(%d+)$') or Black1:match('^خروج (-100)(%d+)$')) then
local chat_id = Black1:match('^leave (.*)$') or Black1:match('^خروج (.*)$') 
local Hash = TD_ID..'StatsGpByName'..chat_id
base:del(Hash)
base:del(TD_ID..'Gp2:'..chat_id)
base:del(TD_ID..'Gp:'..chat_id)
base:del(TD_ID..'Gp3:'..chat_id)
base:del(TD_ID..'NewUser'..chat_id)
base:del(TD_ID.."ExpireData:"..chat_id)
base:srem(TD_ID.."group:",chat_id)
base:del(TD_ID.."ModList:"..chat_id)
base:del(TD_ID..'OwnerList:'..chat_id)
base:del(TD_ID.."MuteList:"..chat_id)
base:del(TD_ID.."SilentList:"..chat_id)
base:del(TD_ID..'setmode:'..chat_id)
base:del(TD_ID..'Text:Welcome:'..chat_id)
base:del(TD_ID..'settag'..chat_id)
base:del(TD_ID..'Link:'..chat_id)
base:del(TD_ID..'Pin_id'..chat_id)
base:del(TD_ID..'EndTimeSee'..chat_id)
base:del(TD_ID..'StartTimeSee'..chat_id)
base:del(TD_ID..'limitpm:'..chat_id)
base:del(TD_ID..'mutetime:'..chat_id)
base:del(TD_ID..'cgmautotime:'..chat_id)
base:del(TD_ID..'cbmtime:'..chat_id)
base:del(TD_ID..'Flood:Max:'..chat_id)
base:del(TD_ID..'Force:Time:'..chat_id)
base:del(TD_ID..'Force:Pm:'..chat_id)
base:del(TD_ID..'joinwarn:'..chat_id)
base:del(TD_ID..'Warn:Max:'..chat_id)
base:del(TD_ID..'NUM_CH_MAX:'..chat_id)
base:del(TD_ID..'setch:'..chat_id)
base:del(TD_ID..'Text:Welcome:'..chat_id)
base:del(TD_ID..'Rules:'..chat_id)
base:del(TD_ID..'Total:messages:'..chat_id)
base:del(TD_ID..'Total:JoinedByLink:'..chat_id)
result = TD.getChat(chat_id)
res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then 
name = res.id
else 
name = ec_name(res.first_name)
end
send(chat_id,0,"✅ انجام شد\n✦ توسط : ["..name.."](tg://user?id="..msg.sender_id.user_id..")\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n💢 ربات از گروه با مشخصات زیر :\n📝 نام گروه : "..(result.title or "-").."\n🆔 ایدے گروه : "..chat_id.."\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nخارج شد",'md')
allusers = base:smembers(TD_ID..'AllUsers:'..chat_id)
for k, v in pairs(allusers) do 
base:del(TD_ID..'addeduser'..chat_id..v)
base:del(TD_ID..'Total:AddUser:'..chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..chat_id..':'..v)
base:del(TD_ID..'Total:BanUser:'..chat_id..':'..v)
base:del(TD_ID..'Total:KickUser:'..chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..chat_id..':'..os.date("%Y/%m/%d")..':'..v)
end
print(result.title)
TD.leaveChat(chat_id)
--Leave_api(chat_id)
end
if Black and (Black1:match('^join (-100)(%d+)$') or Black1:match('^دعوت من (-100)(%d+)$')) then
local chat_id = Black1:match('^join (.*)$') or Black1:match('^دعوت من (.*)$')
result = TD.getChat(chat_id)
send(msg.chat_id,msg.id,'با موفقیت شما رو به گروه \n'..(result.title or '--')..'\nافزودم.','html')
TD.addChatMembers(chat_id,{[1] = msg.sender_id.user_id})
end
if Black == 'backup' or Black == 'بک اپ' then
TD.sendDocument(msg.chat_id,msg.id,0,1,nil,'/var/lib/redis/dump.rdb', '', dl_cb, nil)
end
if (Black == 'rlds') then
send(msg.chat_id,msg.id,'#ok','md')
dofile('Cli.lua')
end
if (Black == 'reload' or Black == 'ریلود') then
if not base:get(BotCliId..'Reloading') then
base:setex(BotCliId..'Reloading',10,true)
if lang then
send(msg.chat_id,msg.id,'Reloading...\n\n>│','md')
else
send(msg.chat_id,msg.id,'درحال بروزرسانی سیستم...\n\n>│','md')
end
dofile(arg[0])
else
send(msg.chat_id,msg.id,'> انجام این دستور هر 10 ثانیه یکبار ممکن است !','md')
end
end
if Black == 'markread on' or Black == 'تیک دوم فعال' then
if base:get(TD_ID..'MarkRead:on') then
send(msg.chat_id, msg.id, 'تیک دوم #فعال بود','md')
else
send(msg.chat_id, msg.id, 'تیک دوم #فعال شد','md')
base:set(TD_ID..'MarkRead:on',true)
end
end
if Black == 'markread off' or Black == 'تیک دوم غیرفعال' then
if base:get(TD_ID..'MarkRead:on') then
send(msg.chat_id, msg.id, 'تیک دوم #غیرفعال شد','md')
base:del(TD_ID..'MarkRead:on')
else
send(msg.chat_id, msg.id, 'تیک دوم #غیرفعال بود','md')
end
end
if Black and (Black:match('^setautoblock (%d+)') or Black:match('^تنظیم بلاک خودکار (%d+)')) then
local spm = Black:match('^setautoblock (%d+)') or Black:match('^تنظیم بلاک خودکار (%d+)')
if tonumber(spm) < 2 then
send(msg.chat_id,msg.id, '❎عددے بزرگ تر از 2 وارد کنید','md')
return false
end
base:set(TD_ID..'autoblocknumber',spm)
send(msg.chat_id, msg.id, 'تعداد اسپم در پیوے تنظیم شد روے :'..spm..'','md')
end
if Black == 'autoblock on' or Black == 'بلاک خودکار فعال' then
base:del(TD_ID..'block:on')
send(msg.chat_id, msg.id, 'بلاک خودکار پیوے #فعال شد','md')
end
if Black == 'autoblock off' or Black == 'بلاک خودکار غیرفعال' then
base:set(TD_ID..'block:on',true)
send(msg.chat_id, msg.id, 'بلاک خودکار پیوے #غیرفعال شد','md')
end
if Black == 'leave' or Black == 'خروج' then
send(msg.chat_id,msg.id,"⭕ ربات از گروه خارج شد...!",'md')
TD.leaveChat(msg.chat_id)
end
if Black == 'addkicked' or Black == 'افزودن لیست سیاه' then
result = TD.getSupergroupMembers(msg.chat_id, "ban", '' , 0 , 25 )
for k,v in pairs(result.members) do
TD.addChatMembers(msg.chat_id,{[1] = v.user_id})
end
send(msg.chat_id,msg.id,'کاربران لیست سیاه به گروه افزوده شدن','md')
end
end
if Black and (Black:match('^import http(.*)') or Black:match('^ورودربات http(.*)')) then
local link = msg.content.text.text:match('^import (.*)') or msg.content.text.text:match('^ورودربات (.*)')
TD.joinChatByInviteLink(link)
send(msg.chat_id,0,'ربات با موفقیت وارد گروه '..link..' شد', 'html')
end
----شخصی 
if (not base:sismember(TD_ID..'Gp:'..msg.chat_id,'Cmd') or VipUser(msg,msg.sender_id.user_id)) and is_supergroup(msg) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then
if (Black == "ping" or Black == "پینگ") then
y = os.time() 
base:set(TD_ID..'Y:',y)
txt = "• کلینر هم اکنون آنلاین میباشد !"
send(msg.chat_id,msg.id,txt,'html')
end
end


---BioBot
--local biobot = base:get(TD_ID..'BotBio') or 0
--if os.date("%M") ~= biobot then
--TD.setBio('●━━━━━━───────⇔['..os.date("%X")..']⇆ ㅤ◁ㅤㅤ❚❚ㅤㅤ▷')
--base:set(TD_ID..'BotBio',os.date("%M"))
--end

-----del today chat
if tonumber(os.date("%H%M")) > 2350 and not base:get(TD_ID..'delincr'..msg.chat_id) then
allusers = base:smembers(TD_ID..'AllUsers:'..msg.chat_id)
for k, v in pairs(allusers) do 
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..v)
end
base:setex(TD_ID..'delincr'..msg.chat_id,60,true)
end
end 
--end 

Checkers_()
cleancache()
local function updateNewMessage(data)
--TD.vardump(data)
BDStartPro(data.message,data)
end
function updateMessageEdited(data)
msg = data.message
BDStartPro(msg,data)
res = TD.getMessage(data.chat_id,data.message_id)
BDStartPro(res,data)
--TD.vardump(data)
end 
local function updateMessageSendSucceeded(update)
if (update._ == 'updateMessageSendSucceeded') then
local msg = update.message	
local Black = (msg.content.text and msg.content.text.text or msg.content.caption and msg.content.caption.text)
if Black then
logo ={
'█ %10','████ %40','███████ %70','██████████ %100'}

if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cbmon') then
function BDClearCmd()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
local timecgms = tonumber(base:get(TD_ID..'cbmtime:'..msg.chat_id)) or 10
TD.set_timer(timecgms,BDClearCmd)
end
if Black:match('> در حال پاکسازے پیام ها...') then
local url  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..Config.BotCliId)
if res ~= 200 then
end
local i,_ = 0,os.clock()
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_delete_messages == true then
TD.deleteMessages(msg.chat_id,msg.id)
local function deleteMessages(result)
if result.messages and #result.messages>0 then
for k,v in pairs(result.messages) do
TD.deleteChatMessagesBySender(v.chat_id,v.sender_id.user_id)
end
local rio = TD.getChatHistory(msg.chat_id,msg.id, 0,500)
for k,v in pairs(rio.messages) do
TD.deleteMessages(msg.chat_id,{[1] =v.id})
end
local result = TD.getChatHistory(msg.chat_id, result.messages[1].id,0,100)
deleteMessages(result)
else
send(msg.chat_id,0,'๏ تمامی پیام هاے گروه #پاکسازے شدن\n๏ زمان طے شدہ : '..string.format('%.2f', os.clock() - _)..' ثانیه⏱','md')
base:del(TD_ID..'_warnclean_:'..msg.chat_id,true)
base:del(TD_ID..'Auto_Warns_:'..msg.chat_id)
end
end
local result = TD.getChatHistory(msg.chat_id,msg.id,0,100)
deleteMessages(result)
end
end
if Black:match('درحال بروزرسانی سیستم...\n\n>│') or Black:match('Reloading...\n\n>│') then
function edit_text(arg,org)
if arg.i > 4 then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'diamondlang') then
TD.editMessageText(msg.chat_id,msg.id,'➣Bot Successfully Reloaded♻️','md')
else
TD.editMessageText(msg.chat_id,msg.id,'✸ربات به روز رسانے شد ♻','md')
end
else
TD.editMessageText(msg.chat_id,msg.id,Black..logo[arg.i],'md')
TD.set_timer(0.5,edit_text,{i=arg.i+1})
end
end
TD.set_timer(0.5,edit_text,{i=1})
end
if Black:match("^• کلینر هم اکنون آنلاین میباشد !") then
x = os.time()
Result = x - y 
if Result == 0 then 
Receive = "بدون وقفه" 
else 
Receive = string.gsub(Result,'-','').." ثانیه"
end 
txt = "• کلینر هم اکنون آنلاین میباشد !"  
Send = (io.popen('ping -c 1 '..iP..''):read('*a')):match('time=(%S+)') 
TD.editMessageText(msg.chat_id, msg.id, txt .. "\n◄ زمان های سپری شده :\n▼ دریافت : "..Receive.."\n▲ ارسال : "..Send.." ثانیه", "html")
end 
end end end
tdlib.run({
updateNewMessage = updateNewMessage,updateMessageEdited = updateMessageEdited,updateMessageSendSucceeded = updateMessageSendSucceeded})

--FINISH BY : @XXXTENTACIONGG