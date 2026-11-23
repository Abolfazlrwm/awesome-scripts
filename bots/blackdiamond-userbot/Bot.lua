-- ==============================
-- 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
-- Use, edit and redistribute only with attribution to @Abolfazl_rwm.
-- ==============================
-- By @XXXTENTACIONGG
local tdlib = require 'tdlib'
local TD = tdlib.get_functions()
local serpent = dofile('./libs/serpent.lua')
local base = dofile('./libs/redis.lua')
local utf8 = dofile('./libs/utf8.lua')
local dkjson = dofile('./libs/dkjson.lua')
local http = require("socket.http")
local https = require("ssl.https")
local URL = require("socket.url")
local ltn12 = require("ltn12")
local TD_ID = arg[1]
local json = loadfile('./libs/JSON.lua')()
local offset = 0
local minute = 60
local hour = 3600
local day = 86400
local week = 604800
local MsgTime = os.time() - 300
local creatore = 'XXXTENTACIONGG'
local jdates = dofile('./jdate.lua')
local session_name = 'Api'..arg[1]
local _ = {
status = {}
}
-----------------------------------------
if not base:get(TD_ID.."token") then 
print(TD.colors("%{cyan} Please Send Token :"))
::TokenBot::
local TokenBot = io.read() 
if TokenBot and (TokenBot:match('(%d+):(.*)')) then 
base:set(TD_ID.."token",TokenBot)
else
print(TD.colors("%{red} Bot Token Is Wrong /nPlease Send Token"))
goto TokenBot
end
end
if not base:get(TD_ID..'api_id') then
print(TD.colors("%{cyan} Please Send APi-Id For Bot :"))
::api_id::
local api_id = io.read()
if api_id == '' then
print(TD.colors("%{red} Error , APi-iD Should Not Be Empty \n Please Send APi-iD First :"))
goto api_id
else
base:set(TD_ID..'api_id',tonumber(api_id))
end
end
if not base:get(TD_ID..'api_hash') then
print(TD.colors("%{cyan} Please Send Api-Hash For Bot :"))
::api_hash::
local api_hash = io.read()
if api_hash == '' then
print(TD.colors("%{red} Error , Api-Hash  Should Not Be Empty \n Please Send Api-Hash First :"))
goto api_hash
else
base:set(TD_ID..'api_hash',api_hash)
end
end
if not base:get(TD_ID..'full_sudo') then
print(TD.colors("%{cyan} Please Send FullSudo-Id For Bot :"))
::full_sudo::
local full_sudo = io.read()
if full_sudo == '' then
print(TD.colors("%{red} Error , FullSudo-Id Should Not Be Empty \n Please Send FullSudo-Id First :"))
goto full_sudo
else
base:set(TD_ID..'full_sudo',full_sudo)
end
end
if not base:get(TD_ID..'sudo_id') then
print(TD.colors("%{cyan} Please Send Sudo-Id :"))
::sudo_id::
local sudo_id = io.read()
if sudo_id == '' then
print(TD.colors("%{red} Error , Sudo-Id Should Not Be Empty \n Please Send Sudo-Id First :"))
goto sudo_id
else
base:sadd(TD_ID..'SUDO',sudo_id) 
base:set(TD_ID..'sudo_id',sudo_id)
end
end
if not base:get(TD_ID..'sudo_username') then
print(TD.colors("%{cyan} Please Send Sudo_Username :"))
::sudo_username::
local sudo_username = io.read()
if sudo_username == '' then
print(TD.colors("%{red} Error , Sudo_Username Should Not Be Empty \n Please Send Sudo_Username First :"))
goto sudo_username
else
base:set(TD_ID..'sudo_username', sudo_username)
end
end
if not base:get(TD_ID..'cli_id') then
print(TD.colors("%{cyan} Please Send Cli-Id :"))
::cli_id::
local cli_id = io.read()
if cli_id == '' then
print(TD.colors("%{red} Error , Cli-Id Should Not Be Empty \n Please Send Cli-Id First :"))
goto cli_id
else
base:set(TD_ID..'cli_id',cli_id)
end
end
if not base:get(TD_ID..'channel_username') then
print(TD.colors("%{cyan} Please Send Channel-Username For Bot :"))
::channel_username::
local channel_username = io.read()
if channel_username == '' then
print(TD.colors("%{red} Error , Channel-Username Should Not Be Empty \n Please Send Channel-Username First :"))
goto channel_username
else
base:set(TD_ID..'channel_username',channel_username)
end
end
if not base:get(TD_ID..'link_support') then
print(TD.colors("%{cyan} Please Send Link-Support :"))
::link_support::
local link_support = io.read()
if link_support == '' then
print(TD.colors("%{red} Error , Link-Support Should Not Be Empty \n Please Send Link-Support First :"))
goto link_support
else
base:set(TD_ID..'link_support', link_support)
end
end

local url , res = https.request('https://api.telegram.org/bot'..base:get(TD_ID.."token")..'/getMe')
local Json_Info = json:decode(url)
local TokenBot = base:get(TD_ID.."token") or 0
local BotJoinerr = TokenBot:match("(%d+)") or 0
if not base:get(TD_ID..'BotJoiner') then
base:set(TD_ID..'BotJoiner',tonumber(BotJoinerr))
end
local UserJoinerr = Json_Info.result and Json_Info.result.username or 0
local file = io.open('./Config/Config-'..arg[1]..'.lua', 'w')
if file ~= nil then
file:write([[
return {
SUDO_ID = {]]..base:get(TD_ID.."sudo_id")..[[},
Sudo1 = ']]..base:get(TD_ID.."sudo_username")..[[',
Full_Sudo = {]]..base:get(TD_ID.."full_sudo")..[[},
Sudoid = ]]..base:get(TD_ID.."sudo_id")..[[,
BotCliId = ]]..base:get(TD_ID.."cli_id")..[[,
BotJoiner = ]]..BotJoinerr..[[,
UserJoiner = ']]..UserJoinerr..[[',
Channel = ']]..base:get(TD_ID.."channel_username")..[[',
LinkSuppoRt = ']]..base:get(TD_ID.."link_support")..[[',
JoinToken = ']]..base:get(TD_ID.."token")..[[',
TD_ID = ]]..TD_ID..[[
}
]])
file:close()
print('<<<>>>> BOT CREATE <<<>>>')
end
-----------------------------------------
local Config = dofile('./Config/Config-'..arg[1]..'.lua')
local SUDO = Config.SUDO_ID
local UserSudo = '@'..Config.Sudo1
local Full_Sudo = Config.Full_Sudo
local Sudoid = Config.Sudoid
local BotCliId = Config.BotJoiner
local BotJoiner = Config.BotJoiner
local UserJoiner = Config.UserJoiner
local Channel = '@'..Config.Channel
local LinkSuppoRt = Config.LinkSuppoRt
local JoinToken = Config.JoinToken
local Bot_Api = 'https://api.telegram.org/bot' .. JoinToken
-----------------------------------------
tdlib.set_config{
api_id = base:get(TD_ID..'api_id'),
api_hash = base:get(TD_ID..'api_hash'),
session_name = ''..session_name,
token = ''..base:get(TD_ID.."token")
}
-----------------------------------------
local need = {
process = 0
}
local function ExitCode()
if need.process > 0 then
TD.set_timer(600,ExitCode)
print('<<<< EXIT_1 >>>>')
else
os.exit()
end
end
if need.process == 0 then
TD.set_timer(850,ExitCode)
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

function Sudo(user_id)
local var = false
for v,user in pairs(SUDO) do
if user == (user_id) then
var = true
end
end
if base:sismember(TD_ID..'SUDO',user_id) then
var = true
end
if Sudo == tonumber(user_id) then
var = true
end
return var
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
--------**GlobalyBan_2**--------
function is_GlobalyBan_(user_id)
local var = false
local hash = 'GlobalyBanned_:'
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
function VipUser_(msg,user_id)
user_id = user_id or 00
local Owner = base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
if Owner or Sudo then
return true
else
return false
end
end
local function USER(user_id) 
    data = TD.getUser(user_id) 
    if data and data._ == 'error' then 
        return user_id 
    else 
        if data.first_name == '' then 
   return user_id 
        else 
            return data.first_name
        end 
    end 
end

local function ActionTimer(org) 
 if (org.status == 'دقیقه') and ((tonumber(org.time) <= 0) or (tonumber(org.time) > 59)) then
return 'خطایی رخ داد ، زمان دقیقه 1 تا 59 میباشد !' 
elseif (org.status == 'ساعت') and ((tonumber(org.time) <= 0) or (tonumber(org.time) > 24)) then 
return 'خطایی رخ داد ، زمان ساعت 1 تا 24 میباشد !' 
elseif (org.status == 'روز') and ((tonumber(org.time) <= 0) or (tonumber(org.time) > 24)) then 
return 'خطایی رخ داد ، زمان روز 1 تا 365 میباشد !' 
else 
local extra = TD.getChatMember(org.chat_id, TD.getMe().id) 

local Name = '<a href="tg://user?id='..org.user_id..'">'..USER(org.user_id)..'</a>'
local Nameuser = '<a href="tg://user?id='..org.sender_id..'">'..USER(org.sender_id)..'</a>'
local Timer = ((org.status == 'دقیقه') and (tonumber(org.time) * 60)) or ((org.status == 'ساعت') and (tonumber(org.time) * 3600)) or ((org.status == 'روز') and (tonumber(org.time) * 86400)) 
  if org.type == 'MUTE' then 
   if VipUser(org, org.user_id) then 
return '❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است' 
   else 
    if extra and extra.status and extra.status.rights and extra.status.rights.can_restrict_members then 
    MuteUser(org.chat_id,org.user_id,tonumber(org.date) + Timer)
base:sadd(TD_ID..'MuteList:'..org.chat_id,org.user_id)
     TD.setChatMemberStatus(org.chat_id, org.user_id, 'restricted', { 0, 0, 0, 0, 0, 0, 0, 0, 1, tonumber(org.date) + Timer}) 
return 'کاربر ⁪️: '..Name..'\nا─┅━━━━━━━┅─\nبه لیست سکوت در گروه اضافه شد !\nبه مدت : '..tonumber(org.time).. ' '.. org.status 
    else 
     return ' لطفا دسترسی "اخراج و محدود کردن" را به ربات بدهید !' 
    end 
   end 
  end 
  if org.type == 'SETOWNER' then 
   if base:sismember(TD_ID..'OwnerList:' .. org.chat_id, org.user_id) then 
    return 'کاربر ⁪️: ' .. Name..'\nا─┅━━━━━━━┅─\nدر لیست مالکین گروه وجود دارد !' 
   else 
    base:sadd(TD_ID .. 'Timer:OwnerList:' .. org.chat_id, org.user_id) 
    base:setex(TD_ID .. 'SetexTimer:OwnerList:' .. org.chat_id .. ':' .. org.user_id, Timer, true) 
    base:sadd(TD_ID .. 'OwnerList:' .. org.chat_id, org.user_id) 
    return 'کاربر ⁪️: ' ..Name..'\nا─┅━━━━━━━┅─\nبه لیست مالکین گروه اضافه شد !\nبه مدت : '..tonumber(org.time)..' '.. org.status 
   end 
  end       
  if org.type == 'SETMOD' then 
   if base:sismember(TD_ID .. 'ModList:' .. org.chat_id, org.user_id) then 
    return 'کاربر ⁪️: '..Name..'\nا─┅━━━━━━━┅─\nدر لیست مدیران گروه وجود دارد !' 
   else 
    base:sadd(TD_ID .. 'Timer:ModList:' .. org.chat_id, org.user_id) 
    base:setex(TD_ID .. 'SetexTimer:ModList:' .. org.chat_id .. ':' .. org.user_id, Timer, true) 
    base:sadd(TD_ID .. 'ModList:' .. org.chat_id, org.user_id) 
    return 'کاربر ⁪️: '..Name..'\nا─┅━━━━━━━┅─\nبه لیست مدیران گروه اضافه شد !\nبه مدت : '..tonumber(org.time)..' '.. org.status 
   end 
  end 
  if org.type == 'VIP' then 
   if base:sismember(TD_ID .. 'Vip:' .. org.chat_id, org.user_id) then 
    return 'کاربر ⁪️: '..Name..'\nا─┅━━━━━━━┅─\nدر لیست ویژه گروه وجود دارد !' 
   else 
    base:sadd(TD_ID .. 'Timer:Vip:' .. org.chat_id, org.user_id) 
    base:setex(TD_ID .. 'SetexTimer:Vip:' .. org.chat_id .. ':' .. org.user_id, Timer, true) 
    base:sadd(TD_ID .. 'Vip:' .. org.chat_id, org.user_id) 
    return 'کاربر : '..Name..'\nا─┅━━━━━━━┅─\nبه لیست ویژه گروه اضافه شد !\nبه مدت : '..tonumber(org.time)..' '.. org.status 
   end 
  end 
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
function string:split(sep)
local sep, fields = sep or ":", {}
local pattern = string.format("([^%s]+)", sep)
self:gsub(pattern, function(c) fields[#fields+1] = c end)
return fields
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
Res = TD.setChatMemberStatus(chat_id, user_id, 'restricted',{ 1, 1, 1, 1, 1, 1, 1, 1, 1 })
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
--------**send**--------
function send(chat_id,reply_to_message_id,text,parse_mode)
TD.sendText(chat_id,reply_to_message_id,text,parse_mode)
if base:sismember(TD_ID..'Gp2:'..chat_id,'delcmd') then
function dark()
TD.deleteMessages(chat_id,{[1] = reply_to_message_id})
end
local time = tonumber(base:get(TD_ID..'cbmtime:'..chat_id) or 10)
TD.set_timer(time,dark)
end
TD.set_timer(12,dark)
end

function sendDocument(chat_id, reply_to_message_id,document,caption, parse_mode)
TD.sendDocument(chat_id,reply_to_message_id,document,caption,parse_mode)
if base:sismember(TD_ID..'Gp2:'..chat_id,'delcmd') then
function darkk()
TD.deleteMessages(chat_id,{[1] = reply_to_message_id})
end
local time = tonumber(base:get(TD_ID..'cbmtime:'..chat_id) or 10)
TD.set_timer(time,darkk)
end
TD.set_timer(12,darkk)
end

------------------------------------------
function SetAdmins(chat_id, user_id)
local Rep  = Bot_Api .. '/promoteChatMember?chat_id='..chat_id..'&user_id='..user_id..'&can_change_info=true&can_pin_messages=true&can_restrict_members=true&can_invite_users=true&can_delete_messages=true'
return https.request(Rep)
end
----------------------------------------------
function DownloadFile(url, fileName)
		local respbody = {}
		local options = { url = url, sink = ltn12.sink.table(respbody), redirect = true }
		local response = nil
		response = {http.request(options)}
		local responsive = response[2]
		if responsive ~= 200 then return nil end
		local filePath = "./libs/data/"..fileName
		file = io.open(filePath, "w+")
		file:write(table.concat(respbody))
		file:close()
		return filePath
	end
----------------------------------------------
function file_exists(name)
  local f = io.open(name,"r")
  if f ~= nil then
    io.close(f)
    return true
  else
    return false
  end
end
------function Api_Sender------
function sendApi(chat_id,text, reply_to_message_id,markdown)
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
function send_Api(chat_id,reply_to_message_id,text,markdown)
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
function send_inline(chat_id,text,keyboard,markdown)
local url = Bot_Api
if keyboard then
url = url .. '/sendMessage?chat_id=' ..chat_id.. '&text='..URL.escape(text)..'&parse_mode=html&reply_markup='..URL.escape(json:encode(keyboard))
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

local function keyboards(table_)
return TD.replyMarkup{type = 'inline',data = table_}
end
local function keyboards_(table_)
return TD.replyMarkup{type = 'remove',data = table_}
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
bd = 'نام :【'..(results.first_name or '')..'】\n\n℘ شما ابتدا باید در کانال زیر عضو شوید و سپس مجدد دستور خود را ارسال کنید\n\n℘ نکته : درصورت عضو نشدن ربات به هیچکدام از دستورات عمل نخواهد کرد.'
Button = {
{
{text = '✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Config.Channel}
}
}   
TD.sendText(msg.chat_id,msg.id,bd, 'html', true,false,false,false,keyboards(Button))
else
return true
end
else
return true
end
end

function getindex(t,id) 
 for i,v in pairs(t) do 
  if v == id then 
   return i 
  end 
 end 
 return nil 
end
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
function is_supergroup(msg)
chat_id = tostring(msg.chat_id)
if TD.chat_type(chat_id) == 'is_supergroup' then
return true
else
return false
end
end
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
function is_private(msg)
chat_id = tostring(msg.chat_id)
if chat_id:match('^(%d+)') then
print 'Private'
return false
else
return true
end
end
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
local function run_bash(str)
local cmd = io.popen(str)
local result = cmd:read('*all')
return result
end
local function MBD(babi,khn)
if babi and khn then
babikhn = '['..babi..'](tg://user?id='..khn..')'
return babikhn
end
end
----------- >>Function BD_Locks<< -----------
local function lock_del(msg)
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
local function lock_kick(msg,fa)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nاز گروه #اخراج شد\n─┅━━━━━━━┅─\n℘ دلیل اخراج : "..fa.."",'md')
end
KickUser(msg.chat_id,msg.sender_id.user_id)
UnRes(msg.chat_id,msg.sender_id.user_id)
else
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nتخلف "..fa.." را انجام داده است ولی ربات دسترسی برای اخراج وی را ندارد !",'md')
end
end
--<><>--<><>--<>
local function lock_mute(msg,fa)
local timemutemsg = tonumber(base:get(TD_ID..'mutetime:'..msg.chat_id) or 3600)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nبه مدت【"..timemutemsg.."】ثانیه از ارسال پیام #محدود شد\n─┅━━━━━━━┅─\n℘ دلیل محدودیت : "..fa.."","md")
end
MuteUser(msg.chat_id,msg.sender_id.user_id,msg.date+timemutemsg)
else
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nتخلف "..fa.." را انجام داده است ولی ربات دسترسی برای محدود کردن وی را ندارد !",'md')
end
end
--<><>--<><>--<>
local function lock_silent(msg,fa)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if not base:sismember(TD_ID..'SilentList:'..msg.chat_id,msg.sender_id.user_id) then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\n#سایلنت شد\n─┅━━━━━━━┅─\n℘ دلیل سایلنت : '..fa..'','md')
end
base:sadd(TD_ID..'SilentList:'..msg.chat_id,msg.sender_id.user_id or 00000000)
end
end
--<><>--<><>--<><>
local function lock_warn(msg,fa)
local hashwarnbd = TD_ID..msg.sender_id.user_id..':warn'
local warnhashbd = base:hget(hashwarnbd, msg.chat_id) or 1
local max_warn = tonumber(base:get(TD_ID..'max_warn:'..msg.chat_id) or 5)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if tonumber(warnhashbd) == tonumber(max_warn) then
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
KickUser(msg.chat_id,msg.sender_id.user_id)
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\nبه علت گرفتن حداکثر #اخطار از گروه #اخراج شد\n℘ دلیل اخطار و اخراج : '..fa..'\n─┅━━━━━━━┅─\n● #اخطارها : '..warnhashbd..'/'..max_warn..'','md')
end
base:hdel(hashwarnbd,msg.chat_id,max_warn)
else
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nتخلف "..fa.." را انجام داده است و به حداکثر اخطار خود رسیده است ولی ربات دسترسی به اخراج کاربران ندارد !",'md')
end
else
base:hset(hashwarnbd,msg.chat_id, tonumber(warnhashbd) +1)
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\nشما یک #اخطار دریافت کردید\n─┅━━━━━━━┅─\n℘ دلیل اخطار : '..fa..'\n● #اخطارها : '..warnhashbd..'/'..max_warn..'','md')
end
end
end
--<><>--<><>--<><>
local function lock_ban(msg,fa)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then 
name = diamond.id
else 
name = ec_name(diamond.first_name)
end
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nاز گروه #مسدود شد\n─┅━━━━━━━┅─\n℘ دلیل مسدودیت : "..fa.."","md")
end
KickUser(msg.chat_id,msg.sender_id.user_id)
else
send(msg.chat_id,msg.id,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nتخلف "..fa.." را انجام داده است ولی ربات دسترسی برای مسدود کردن وی را ندارد !",'md')
end
end
--<><>Msg Check >> @Mrr619<><>--
local function MsgCheck(msg,fa,Redis,Redis2)
if base:sismember(TD_ID..'Gp3:'..msg.chat_id,msg.sender_id.user_id..' حذف '..Redis2) or base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'del' then
lock_del(msg)
end
if not (base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'ban') then
if base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'mute' then
lock_mute(msg,fa)
end
if base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'silent' then
lock_silent(msg,fa)
end
if base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'warn' then
lock_warn(msg,fa) 
end
end
if base:get(TD_ID..'Lock:'..Redis..msg.chat_id) == 'ban' then
lock_ban(msg,fa)
end
end
function escape_markdown(str)
return tostring(str):gsub('%_', '\\_'):gsub('%[', '\\['):gsub('%*', '\\*'):gsub('%`', '\\`')
end
function utf8_len(str)
local chars = 0
for i = 1, str:len() do
local byte = str:byte(i)
if byte < 128 or byte >= 192 then
chars = chars + 1
end
end
return chars
end
function AnswerInline(inline_query_id, query_id , title , description , text,parse_mode, keyboard)
local results = {{}}
 results[1].id = query_id
results[1].type = 'article'
results[1].description = description
results[1].title = title
results[1].message_text = text
results[1].parse_mode = parse_mode
Rep= Bot_Api .. '/answerInlineQuery?inline_query_id=' .. inline_query_id ..'&results=' .. URL.escape(json:encode(results))..'&parse_mode=&cache_time=' .. 1
if keyboard then
results[1].reply_markup = keyboard
Rep = Bot_Api.. '/answerInlineQuery?inline_query_id=' .. inline_query_id ..'&results=' .. URL.escape(json:encode(results))..'&parse_mode=Markdown&cache_time=' .. 1
end
https.request(Rep)
end
local function BDStartQuery(data)
--if is_Sudo(data) then
if data.query:match('(.*)@(.*)') then
Split = data.query:split('@')
if Split[1] and Split[2] then
user = '@'..Split[2]
username = Split[2]
if tonumber(utf8.len(Split[1])) < 200 then
local diamond = TD.getUser(data.sender_user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local result = TD.searchPublicChat(username)
if tonumber(utf8.len(data.query)) > 50 then
mrr619 = tonumber(50) - tonumber(utf8.len(user))
text = string.sub(Split[1],0,mrr619)..'..'
local keyboard = {}
keyboard.inline_keyboard = {
{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..username..'::BDMrr'..string.sub(Split[1],0,mrr619)}
}
} 
base:setex(string.sub(Split[1],0,mrr619),tonumber(day),string.sub(Split[1],mrr619+1,99999))
if result.id then
AnswerInline(data.id,'Mrr619','نجوا برای : '..user,'پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : <a href="tg://user?id='..result.id..'">'..username..'</a>\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
else
AnswerInline(data.id,'Mrr619','نجوا برای : '..user..' (کاربر مورد نظر یافت نشد)','پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : '..user..'\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
end
else
text = Split[1]
local keyboard = {}
keyboard.inline_keyboard = {
{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..username..'::'..text}
}
}
if result.id then
AnswerInline(data.id,'Mrr619','نجوا برای : '..user,'پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : <a href="tg://user?id='..result.id..'">'..username..'</a>\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
else
AnswerInline(data.id,'Mrr619','نجوا برای : '..user..' (کاربر مورد نظر یافت نشد)','پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : '..user..'\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
end
end
else
AnswerInline(data.id,'Mrr619','تعداد کارکترهای پیام شما بیش از حد مجاز است !','تعداد کارکترهای پیام شما : '..tonumber(utf8.len(Split[1])),'html',nil)
end
end
--end
if data.query:match('(.*)(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)$') then
finduser = string.find(data.query,'(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)')
user = string.sub(data.query,finduser,9999)
text2 = data.query:gsub('(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)(%d+)','')
if tonumber(utf8.len(text2)) < 200 then
local diamond = TD.getUser(data.sender_user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local Diamond = TD.getUser(user)
if Diamond.first_name == '' then nameuser = Diamond.id else nameuser = ec_name(Diamond.first_name) end
if tonumber(utf8.len(data.query)) > 50 then
mrr619 = tonumber(50) - tonumber(utf8.len(user))
text = string.sub(text2,0,mrr619)
local keyboard = {}
keyboard.inline_keyboard = {
{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..user..'::BDMrr'..text}
}
} 
base:setex(text,tonumber(day),string.sub(text2,mrr619+1,99999))
if nameuser then
AnswerInline(data.id,'Mrr619','نجوا برای : '..nameuser,'پیام شما : '..text..'..\nبرای ارسال نجوا کلیک کنید !','👤کاربر : <a href="tg://user?id='..user..'">'..nameuser..'</a>\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
else
AnswerInline(data.id,'Mrr619','نجوا برای : '..user..' (کاربر مورد نظر یافت نشد)','پیام شما : '..text..'..\nبرای ارسال نجوا کلیک کنید !','👤کاربر : '..user..'\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
end
else
local keyboard = {}
keyboard.inline_keyboard = {
{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..user..'::'..text2}
}
}
if nameuser then
AnswerInline(data.id,'Mrr619','نجوا برای : '..nameuser,'پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : <a href="tg://user?id='..user..'">'..nameuser..'</a>\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
else
AnswerInline(data.id,'Mrr619','نجوا برای : '..user..' (کاربر مورد نظر یافت نشد)','پیام شما : '..text..'\nبرای ارسال نجوا کلیک کنید !','👤کاربر : '..user..'\n🔐شما از طرف <a href="tg://user?id='..data.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !','html',keyboard)
end
end
else
AnswerInline(data.id,'Mrr619','تعداد کارکترهای پیام شما بیش از حد مجاز است !','تعداد کارکترهای پیام شما : '..tonumber(utf8.len(text2)),'html',nil)
end
end
end end
----------------
function cleanbots(msg)
local result = TD.getSupergroupMembers(msg.chat_id, "Bots", '' , 0 , 200 )
if result.members then
t = "• ربات های قابل دسترس مسدود شدند !\n\n" 
i = 0
for k,v in pairs(result.members) do
User = '<a href="tg://user?id='..v.member_id.user_id..'">'..TD.getUser(v.member_id.user_id).usernames.editable_username..'</a>'
i = i + 1
t = t..i..' - '..User..')\n'
TD.setChatMemberStatus(msg.chat_id, v.member_id.user_id, 'ban')
end end
send(msg.chat_id,0, t,'html')
end
----------------
function cleandeleted(msg)
local result = TD.getSupergroupMembers(msg.chat_id, "Recent", '' , 0 , 200 )
if result.members then
for k,v in pairs(result.members) do
local data = TD.getUser(v.member_id.user_id)
if data.type["@type"] == "userTypeDeleted" then
TD.setChatMemberStatus(msg.chat_id, data.id, 'ban')
end
end
end
end
----------------
function cleanbanlist(msg)
local data = TD.getSupergroupFullInfo(msg.chat_id)
if tonumber(data.banned_count) ~= 0 then
TD.set_timer(20,cleanbanlist,msg)
end
local result = TD.getSupergroupMembers(msg.chat_id, 'ban', '' , 0 , 25 )
if result.members then
for k,v in pairs(result.members) do
TD.setChatMemberStatus(msg.chat_id, v.member_id.user_id,'restricted', {1, 1, 1, 1, 1, 1 ,1 ,1 ,1 ,1})
end
end
end
----------------
function cleanmutelist(msg)
local data = TD.getSupergroupFullInfo(msg.chat_id)
if tonumber(data.restricted_count) ~= 0 then
TD.set_timer(20,cleanmutelist,msg)
end
local result = TD.getSupergroupMembers(msg.chat_id, "Restricted", '' , 0 , 25 )
if result.members then
for k,v in pairs(result.members) do
TD.setChatMemberStatus(msg.chat_id, v.member_id.user_id,'restricted', {1, 1, 1, 1, 1, 1 ,1 ,1 ,1 ,1})
end
end
end
----------------
function cleancache()
for k,v in pairs({'animations','documents','music','photos','temp','video_notes','videos','thumbnails','voice','stickers'}) do
os.execute("rm -rf ~/blackdiamond/.CallBack-Bot/"..session_name.."/"..v.."/*")
end
TD.set_timer(3700,cleancache)
end
----------------
function CheCkk()
local list = base:smembers(TD_ID..'SuperGp')
for k,v in pairs(list) do
if base:sismember(TD_ID..'Gp2:'..v,'added') then 

if base:sismember(TD_ID..'Gp2:'..v,'cgmautoon') and base:get(TD_ID..'autoCgmstart'..v) then
local start = (base:get(TD_ID..'autoCgmstart'..v) or 2000)
local time = os.date("%H%M")
local str = tonumber(start) 
worn = str - 10
if tonumber(time) == tonumber(worn) then
if not base:get(TD_ID..'Auto_Warns_:'..v) then
sendApi(v,'اعضا و کاربران گرامی گروه :\nبه زمان فعال شدن پاکسازی #خودکار ➓ دقیقه مانده است',0,'md')
base:set(TD_ID..'Auto_Warns_:'..v,true)
end 
end
end

if base:get(TD_ID.."bot:muteall:start"..v) and  base:get(TD_ID.."bot:muteall:stop"..v) then
if base:get(TD_ID.."bot:muteall:Time"..v) then
--local time = os.date("%H%M")
local Start_ = base:get(TD_ID.."bot:muteall:start"..v)
local Start = Start_:gsub(':','')
local Start = tonumber(Start)
local End_ = base:get(TD_ID.."bot:muteall:stop"..v)
local End = End_:gsub(':','')
local End = tonumber(End)
local Time = os.date("%H%M")
local Time = tonumber(Time)
local str = tonumber(Start) or 22
local wrn = str - 10
if tonumber(Time) == tonumber(wrn) then
if not base:get(TD_ID..'AutoWarns:'..v) then
send_Api(v,0,'► اعضا و کاربران گرامی گروه :\nبه زمان فعال شدن قفل #خودکار ➓ دقیقه مانده است',keyboard,'html')
base:set(TD_ID..'AutoWarns:'..v,true)
end end
if (End) < (Start) then
if (Time) <= 2359 and (Time) >= (Start) then
if not base:sismember(TD_ID..'Gp2:'..v,'Mute_All') then
send_Api(v,0,'>قفل خودکار گروه #فعال شد!\n📍لطفا از ساعت *'..Start_..'* تا *'..End_..'* پیامی ارسال نکنید!\nو در این ساعات تمامی پیام ها پاک خواهند شد\n\nدر صورتی که مدیران گروه مایل به لغو این عملیات هستند دستور `unlock auto` یا `بازکردن خودکار` را ارسال کنند!','md')
base:sadd(TD_ID..'Gp2:'..v,'Mute_All')
base:del(TD_ID..'AutoWarns:'..v,true)
end
elseif (Time) >= 0000 and (Time) < (End) then
if not base:sismember(TD_ID..'Gp2:'..v,'Mute_All') then
send_Api(v,0,'>قفل خودکار گروه #فعال شد!\n📍لطفا از ساعت *'..Start_..'* تا *'..End_..'* پیامی ارسال نکنید!\nو در این ساعات تمامی پیام ها پاک خواهند شد\n\nدر صورتی که مدیران گروه مایل به لغو این عملیات هستند دستور `unlock auto` یا `بازکردن خودکار` را ارسال کنند!','md')
base:sadd(TD_ID..'Gp2:'..v,'Mute_All')
base:del(TD_ID..'AutoWarns:'..v,true)
end
else
if base:sismember(TD_ID..'Gp2:'..v,'Mute_All') then
send_Api(v,0,'► زمان قفل خودکار به پایان رسید و گروه باز شد و کاربران مجاز به ارسال پیام شدند!','md')
base:srem(TD_ID..'Gp2:'..v,'Mute_All')
if base:sismember(TD_ID..'Gp2:'..v,'Tele_Mute') then
local mutes =  base:smembers(TD_ID..'Mutes:'..v)
for x,y in pairs(mutes) do
base:srem(TD_ID..'Mutes:'..v,y)
UnRes(v,y)
end
end
end
end
elseif (End) > (Start) then
if (Time) >= (Start) and (Time) < (End) then
if not base:sismember(TD_ID..'Gp2:'..v,'Mute_All') then
send_Api(v,0,'>قفل خودکار گروه #فعال شد!\n📍لطفا از ساعت *'..Start_..'* تا *'..End_..'* پیامی ارسال نکنید!\nو در این ساعات تمامی پیام ها پاک خواهند شد\n\nدر صورتی که مدیران گروه مایل به لغو این عملیات هستند دستور `unlock auto` یا `بازکردن خودکار` را ارسال کنند!','md')
base:sadd(TD_ID..'Gp2:'..v,'Mute_All')
base:del(TD_ID..'AutoWarns:'..v,true)
end
else
if base:sismember(TD_ID..'Gp2:'..v,'Mute_All') then
send_Api(v,0,'► زمان قفل خودکار به پایان رسید و گروه باز شد و کاربران مجاز به ارسال پیام شدند!','md')
base:srem(TD_ID..'Gp2:'..v,'Mute_All')
if base:sismember(TD_ID..'Gp2:'..v,'Tele_Mute') then
local mutes =  base:smembers(TD_ID..'Mutes:'..v)
for x,y in pairs(mutes) do
base:srem(TD_ID..'Mutes:'..v,y)
UnRes(v,y)
end
end
end
end
end
end
end
end
end
TD.set_timer(8,CheCkk)
end
----------------
local function getApi(msg,url,_)
local url_, res = https.request(url)
if res == 200 then
if _ then
local number = math.random(143)
local list = json:decode(url_).Result
for k,v in pairs(list) do
if number == k then
soal,javab = v.soal,v.javab
end
end
text = soal
else
text = url_
end
else
text = 'این قابلیت فعلا در دسترس نمیباشد !'
end
TD.sendText(msg.chat_id,msg.id,text,'html')
end
local MentionUser = function(user_id)
local result = TD.getUser(user_id)
if result and result.first_name then
return "<a href=\"tg://user?id=" .. user_id .. "\">" .. string.gsub(result.first_name, "[<>]", "") .. "</a>"
else
return "<a href=\"tg://user?id=" .. user_id .. "\">" .. user_id .. "</a>"
end
end
local function GroupStats(chat_id,msg_id, counter)
base:srem(TD_ID .. "Sender_user_ids:" .. chat_id, BotJoiner)
local msgs, msgsday, adds, addsday = {}, {}, {}, {};
local function getNum(data, rank, status)
if data == "msgs" then
do
do
for k, i in pairs(base:smembers(TD_ID .. "_Sender_user_ids:" .. chat_id)) do
local getUser = base:get(TD_ID .. "Content_Message:Msgs:" .. i .. ":" .. chat_id) or 0
if tonumber(getUser) == tonumber(msgs[(#msgs)]) then
_resultTEXT = _resultTEXT .. "- نفر " .. string.gsub(rank, "[123]", {
										["1"] = "اول 🥇",
										["2"] = "دوم 🥈",
										["3"] = "سوم 🥉"
									}) .. " :\n (" .. tonumber(getUser) .. " پیام | " .. MentionUser(i) .. ")\n";
table.remove(msgs, #msgs)
base:srem(TD_ID .. "_Sender_user_ids:" .. chat_id, i)
break
end
end
end
end
if status == "msgs" then
base:del(TD_ID .. "_Sender_user_ids:" .. chat_id)
end
elseif data == "msgsday" then
do
do
for k, i in pairs(base:smembers(TD_ID .. "_Sender_user_idsd:" .. chat_id)) do
local getUserday = base:get(TD_ID .. "Content_Message:MsgsDay:" .. i .. ":" .. chat_id) or 0
if tonumber(getUserday) == tonumber(msgsday[(#msgsday)]) then
_resultTEXT = _resultTEXT .. "- نفر " .. string.gsub(rank, "[123]", {
										["1"] = "اول 🥇",
										["2"] = "دوم 🥈",
										["3"] = "سوم 🥉"
									}) .. " :\n (" .. tonumber(getUserday) .. " پیام | " .. MentionUser(i) .. ")\n"
table.remove(msgsday, #msgsday)
base:srem(TD_ID .. "_Sender_user_idsd:" .. chat_id, i)
break
end
end
end
end
if status == "msgsday" then
base:del(TD_ID .. "_Sender_user_idsd:" .. chat_id)
end
elseif data == "adds" then
do
do
for k, i in pairs(base:smembers(TD_ID .. "Sender_user_ids_:" .. chat_id)) do
local getUser_ = base:get(TD_ID .. "Content_Message:Adds:" .. i .. ":" .. chat_id) or 0
if tonumber(getUser_) == tonumber(adds[(#adds)]) then
_resultTEXT = _resultTEXT .. "- نفر " .. string.gsub(rank, "[123]", {
										["1"] = "اول 🥇",
										["2"] = "دوم 🥈",
										["3"] = "سوم 🥉"
									}) .. " :\n (" .. getUser_ .. " اد | " .. MentionUser(i) .. ")\n";
table.remove(adds, #adds);
base:srem(TD_ID .. "Sender_user_ids_:" .. chat_id, i)
break
end
end
end
end
if status == "addsday" then
base:del(TD_ID .. "Sender_user_ids_:" .. chat_id)
end
elseif data == "addsday" then
do
do
for k, i in pairs(base:smembers(TD_ID .. "Sender_user_ids_:" .. chat_id)) do
local getUserDay_ = base:get(TD_ID .. "Content_Message:AddsDay:" .. i .. ":" .. chat_id) or 0
if tonumber(getUserDay_) == tonumber(addsday[(#addsday)]) then
_resultTEXT = _resultTEXT .. "- نفر " .. string.gsub(rank, "[123]", {
										["1"] = "اول 🥇",
										["2"] = "دوم 🥈",
										["3"] = "سوم 🥉"
									}) .. " :\n (" .. getUserDay_ .. " اد | " .. MentionUser(i) .. ")\n";
table.remove(addsday, #addsday)
base:srem(TD_ID .. "Sender_user_ids_:" .. chat_id, i)
break
end
end
end
end
if status == "addsday" then
base:del(TD_ID .. "Sender_user_ids_:" .. chat_id)
end
end
end
do
do
for k, i in pairs(base:smembers(TD_ID .. "Sender_user_ids:" .. chat_id)) do
base:sadd(TD_ID .. "_Sender_user_ids:" .. chat_id, i)
base:sadd(TD_ID .. "_Sender_user_idsd:" .. chat_id, i)
base:sadd(TD_ID .. "Sender_user_ids_:" .. chat_id, i)
local getUser, getUserday, getUser_, getUserDay_ = base:get(TD_ID .. "Content_Message:Msgs:" .. i .. ":" .. chat_id) or 0, base:get(TD_ID .. "Content_Message:MsgsDay:" .. i .. ":" .. chat_id) or 0, base:get(TD_ID .. "Content_Message:Adds:" .. i .. ":" .. chat_id) or 0, base:get(TD_ID .. "Content_Message:AddsDay:" .. i .. ":" .. chat_id) or 0
if 0 < tonumber(getUser) and getUser then
table.insert(msgs, tonumber(getUser));
end
if 0 < tonumber(getUserday) and getUserday then
table.insert(msgsday, tonumber(getUserday))
end
if 0 < tonumber(getUser_) and getUser_ then
table.insert(adds, tonumber(getUser_));
end
if 0 < tonumber(getUserDay_) and getUserDay_ then
table.insert(adds, tonumber(getUserDay_));
end
end
end
end
table.sort(msgs)
table.sort(msgsday)
table.sort(adds)
table.sort(addsday)
if counter then
local members = #base:smembers((TD_ID .. "Sender_user_ids:" .. chat_id))
if members < tonumber(counter) then
_c = members
else
_c = tonumber(counter)
end
_resultTEXT = "◄ فعالیت های امروز گروه به تعداد " .. _c .. " نفر به شرح ذیل می باشد :\n\n";
for i = 1, _c do
_resultTEXT = _resultTEXT
getNum("msgs", i)
end
TD.sendText(chat_id, msg_id, _resultTEXT, "html")
else
if #msgs >= 3 then
_resultTEXT = "┈┅━┃آمار کل پیام┃━┅┈\n\n";
getNum("msgs", 1)
getNum("msgs", 2)
getNum("msgs", 3, "msgs")
else
_resultTEXT = _resultTEXT .. "┈┅━┃آمار کل پیام┃━┅┈\n\n- آمار دقیقی دردسترس نیست !\n"
end
if #msgsday >= 3 then
_resultTEXT = _resultTEXT .. "\n┈┅━┃آمار کل امروز┃━┅┈\n\n"
getNum("msgsday", 1)
getNum("msgsday", 2)
getNum("msgsday", 3, "msgsday")
else
_resultTEXT = _resultTEXT .. "┈┅━┃آمار کل امروز┃━┅┈\n\n- آمار دقیقی دردسترس نیست !\n"
end
if #adds >= 3 then
_resultTEXT = _resultTEXT .. "\n┈┅━┃آمار کل اد┃━┅┈\n\n"
getNum("adds", 1)
getNum("adds", 2)
getNum("adds", 3, "adds")
else
_resultTEXT = _resultTEXT .. "\n┈┅━┃آمار کل اد┃━┅┈\n\n- آمار دقیقی دردسترس نیست !\n"
end
if #addsday >= 3 then
_resultTEXT = _resultTEXT .. "\n┈┅━┃آمار امروز اد┃━┅┈\n\n"
getNum("addsday", 1)
getNum("addsday", 2)
getNum("addsday", 3, "addsday")
else
_resultTEXT = _resultTEXT .. "\n┈┅━┃آمار امروز اد┃━┅┈\n\n- آمار دقیقی دردسترس نیست !\n"
end
TD.sendText(chat_id,msg_id, "◄ فعالیت های امروز اعضا تا این لحظه :\n\n".._resultTEXT.."", "html")
end
end
----------------------------------------------
local function BDStartPro(msg,data)
----Start
if msg then
if msg.sender and msg.sender._ == 'messageSenderChat' then
if msg.forward_info and msg.forward_info.from_chat_id ~= 0 then
msg.sender_id.user_id = 777000
else
msg.sender_id.user_id = 1087968824
end
end

if msg.reply_to and msg.reply_to.message_id ~= 0 then
reply_id = msg.reply_to.message_id
else
reply_id = 0
end

if msg.date < tonumber(MsgTime) then
print('> Message A Minute Age...')
return false
end
local Black = (msg.content.text and msg.content.text.text)
if Black and (Black:match('https://t.me/+')) and base:get(TD_ID..'Vorod'..msg.chat_id..msg.sender_id.user_id) then
sendApi(Config.BotCliId,'import '..Black..'',0,'html')
base:del(TD_ID..'Vorod'..msg.chat_id..msg.sender_id.user_id) 
end
local reportpv = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'reportpv')
local ownerslist = base:smembers(TD_ID..'OwnerList:'..msg.chat_id)
function reportowner(text)
if reportpv then
for k,v in pairs(ownerslist) do
sendApi(v,text,0,'md')
end
end
end
function reportowner_(text)
for k,v in pairs(ownerslist) do
sendApi(v,text,0,'md')
end
end
local reporttext = 'ا┅┅──┄┄═✺═┄┄──┅┅\nدقت کنید تنظیم در خصوصے براے شما فعال باشد و در صورتے که فعال نیست با دستور (ثبت گروه) یا (setgp) در همین خصوصے ربات این قابلیت را فعال کنید.'

if base:get(TD_ID..'Lock:Attack'..msg.chat_id) == 'del' then 
if (msg.content["@type"] == "messageChatDeleteMember") and base:sismember(TD_ID..'ModList:'..msg.chat_id,msg.sender_id.user_id) and not is_Owner(msg) then 
base:incr(TD_ID..'countKicked:'..msg.chat_id..':'..msg.sender_id.user_id) 
local countKicked = base:get(TD_ID..'countKicked:'..msg.chat_id..':'..msg.sender_id.user_id) or 0 
countKicked_ = base:get(TD_ID..'count_Kicked:'..msg.chat_id) or 5 

if tonumber(countKicked) >= tonumber(countKicked_) then 
local Owner = base:smembers(TD_ID..'OwnerList:'..msg.chat_id) 
local url = https.request(Bot_Api .. '/promoteChatMember?chat_id='..msg.chat_id..'&user_id='..msg.sender_id.user_id..'&can_change_info=false') 
local Result = json:decode(url) 
local urls  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..msg.sender_id.user_id)
if res ~= 200 then
end
local name = json:decode(urls).result.user.first_name
if Result.ok then 
base:srem(TD_ID..'ModList:'..msg.chat_id, msg.sender_id.user_id) 
reportowner_('✦ مالک گرامی\n✦ مدیر شما با شناسه کاربری (['..name..'](tg://user?id='..msg.sender_id.user_id..')) درحال خیانت به شما بود و با موفقیت از مقام خود عزل گردید !\n\n• وضعیت : خارج شده از ادمین')
else 
local ReportText = '✦ مالک گرامی\n✦ مدیر شما با شناسه کاربری (['..name..'](tg://user?id='..msg.sender_id.user_id..')) درحال خیانت به شما میباشد و ربات نتوانست آن را عزل مقام کند !\n✦ هرچه سریع تر بررسی کنید\n\n• وضعیت : مدیر ادمین میباشد' 
end 
base:del(TD_ID..'countKicked:'..msg.chat_id..':'..msg.sender_id.user_id) 
if #Owner ~= 0 then 
reportowner_(ReportText)
else 
sendApi(msg.chat_id,ReportText,0,'md') 
end 
end 
end 
end
----set sudo----
if #base:smembers(TD_ID..'SUDO') == 0 then
for k,babakkhan in pairs(SUDO) do
base:sadd(TD_ID..'SUDO',babakkhan)
end
for m,diamond in pairs(Full_Sudo) do
base:sadd(TD_ID..'SUDO',diamond)
end
base:sadd(TD_ID..'SUDO',BotJoiner)
end
if is_supergroup(msg) then
if base:get(TD_ID.."cleanmsgs") then
allusers = base:smembers(TD_ID..'AllUsers:'..msg.chat_id)
for k, v in pairs(allusers) do
base:del(TD_ID..'addeduser'..msg.chat_id..v)
base:del(TD_ID..'Total:AddUser:'..msg.chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..msg.chat_id)
base:del(TD_ID..'Total:BanUser:'..msg.chat_id..':'..v)
base:del(TD_ID..'Total:KickUser:'..msg.chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..v)
end
end
end
---- Gps Pvs ----
if msg.chat_id then
local id = tostring(msg.chat_id)
if TD.chat_type(chat_id) == 'is_supergroup' then
if not base:sismember(TD_ID.."SuperGp",msg.chat_id) then
base:sadd(TD_ID.."SuperGp",msg.chat_id)
end
elseif id:match('^-(%d+)') then
if not base:sismember(TD_ID.."Chat:Normal",msg.chat_id) then
base:sadd(TD_ID.."Chat:Normal",msg.chat_id)
end
elseif id:match('(%d+)') then
if not base:sismember(TD_ID.."ChatPrivite",msg.chat_id) then
base:sadd(TD_ID.."ChatPrivite",msg.chat_id)
end
end
end
--<><><> **Add Bot** <><><>--
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') and (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) and TD.chat_type(msg.chat_id) == 'is_supergroup' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'added')
base:sadd(TD_ID..'group:',msg.chat_id)
local results = TD.getSupergroupMembers(msg.chat_id,'Administrators','',0,200)
local adm = TD.getChatMember(msg.chat_id,BotJoiner).inviter_user_id
local Nadm_ = TD.getUser(adm).first_name or adm
local Nadm = '<a href="tg://user?id='..adm..'">'..Nadm_..'</a>'
local tx = '✅ ربات با موفقیت توسط : '..Nadm..' ادمین و در گروه شما نصب شد\nمالک و مدیران شناسایی و پیکربندی شدند ✓\n─┅━━━━━━━┅─\n▸ مدیران گروه :\n'
for p,t in pairs(results.members) do
if t.status._== "chatMemberStatusAdministrator" then
base:sadd(TD_ID..'ModList:'..msg.chat_id,t.member_id.user_id)
local name = TD.getUser(t.member_id.user_id).first_name or t.member_id.user_id
local nm = '<a href="tg://user?id='..t.member_id.user_id..'">'..name..'</a>'
tx = tx..''..nm..'\n'
end
end
for i,s in pairs(results.members) do
if s.status._ == "chatMemberStatusCreator" then
base:sadd(TD_ID..'OwnerList:'..msg.chat_id,s.member_id.user_id)
local names = TD.getUser(s.member_id.user_id).first_name or s.member_id.user_id
local nm_ = '<a href="tg://user?id='..s.member_id.user_id..'">'..names..'</a>'
local tx = tx..'─┅━━━━━━━┅─\n▸ مالک گروه : \n'..nm_..'\n\n💬 قفل های پیشفرض لینک ، فوروارد ، ربات فعال شدند !\n\n❗️جهت عملکرد ربات را مدیر گروه کرده سپس دستور پنل یا راهنما را ارسال کنید 😎\n\n📲 در صورت بروز مشکل به پیوی ربات مراجعه کنید و از طریق پشتیانی اطلاع رسانی کنید'
Button = {{
{text = '✦ ارتباط مستقیم با سازنده',url='https://telegram.me/'..Config.Sudo1}
},{
{text = '✦ کانال ما',url='https://telegram.me/'..Config.Channel},
{text = '✦ پشتیبانی ما',url=''..Config.LinkSuppoRt}
},{
{text = 'افزودن ربات به گروه جدید',url = 'https://t.me/'..UserJoiner..'/?startgroup=new'}
}}
TD.sendText(msg.chat_id,0,tx,'html',true,false,false,false,keyboards(Button))
local gpname = TD.getChat(msg.chat_id).title or 'not'
local xname = TD.getUser(msg.sender_id.user_id).first_name or msg.sender_id.user_id
local unm_ = '<a href="tg://user?id='..msg.sender_id.user_id..'">'..xname..'</a>'
local dlink = TD.getSupergroupFullInfo(msg.chat_id)

local sdtxt = [[
#گزارش_نصب

►►►► نصب کننده ◄◄◄◄

◄ نصب کننده :
◄ یوزرنیم : ]]..unm_..[[

◄ شناسه : ]]..msg.sender_id.user_id..[[

►►►► زمان نصب ◄◄◄◄

◄ تاریخ : ]]..jdates('_#D-#X-#Y_')..[[

◄ ساعت : ]]..jdates('#h:#m:#s')..[[

►►►► اطلاعات گروه ◄◄◄◄

◄ نام گروه : ]]..gpname..[[

◄ مالک گروه : ]]..nm_..[[

◄ تعداد ادمین ها : ]]..dlink.administrator_count..[[

◄ تعداد اعضا گروه : ]]..dlink.member_count..[[

► /Link_]]..string.gsub(msg.chat_id,'-','')..[[

► /Leave_]]..string.gsub(msg.chat_id,'-','')..[[

┈┅┅━New✦Install━┅┅┈
]]
send(Sudoid,0,sdtxt,'html')
local glist = {'Bots','Username','Link','Forward','Photo'}
for g,l in pairs(glist) do
base:set(TD_ID..'Lock:'..l..''..msg.chat_id,'del')
base:sadd(TD_ID..'Gp:'..msg.chat_id,'TGservice')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'Welcomeon')
end
end
end
end

if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') and TD.chat_type(msg.chat_id) == 'is_supergroup' then
local res = TD.getChatMember(msg.chat_id,BotJoiner)
local Diamond = TD.getSupergroupFullInfo(msg.chat_id)
if res and res.status and res.status.rights.can_promote_members then
if not base:get(TD_ID..'jjssss_:') then
base:setex(TD_ID..'jjssss_:',120,true) 
if not base:get(TD_ID..'jsssssxx:'..msg.chat_id) then
local xlink = Diamond and Diamond.invite_link.invite_link or 0
UnRes(msg.chat_id,tonumber(Config.BotCliId))
sendApi(tonumber(Config.BotCliId),'import '..xlink,0,'html')
sendApi(Sudoid,'گزارش ورود ربات پاکسازی به شرح زیر میباشد :\nایدی گروه : '..msg.chat_id..'\nلینک گروه : '..xlink..'',0,'html')
base:set(TD_ID..'jsssssxx:'..msg.chat_id,true) 
end
end 
end end
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') and TD.chat_type(msg.chat_id) == 'is_supergroup' then
local res = TD.getChatMember(msg.chat_id,BotJoiner)
if res.status and res.status.rights.can_promote_members then
function setadmincli()
SetAdmins(msg.chat_id,tonumber(Config.BotCliId))
end
TD.set_timer(20,setadmincli)
end
end
---------- locals
local lang = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'diamondlang')
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
local NUM_CH_MAX =  tonumber(base:get(TD_ID..'NUM_CH_MAX:'..msg.chat_id) or 2000)
local TIME_CHECK = tonumber(base:get(TD_ID..'Flood:Time:'..msg.chat_id) or 2)
local warn = tonumber(base:get(TD_ID..'Warn:Max:'..msg.chat_id) or 3)
local Forcemax = tonumber(base:get(TD_ID..'Force:Max:'..msg.chat_id) or 10)
local added = base:get(TD_ID..'addeduser'..msg.chat_id..''..msg.sender_id.user_id) or 0
local newuser = base:sismember(TD_ID..'Gp2:'..msg.chat_id,'force_NewUser')
-------------MSG BlaCk ------------
local Black = msg.content.text and msg.content.text.text
local Black1 = msg.content.text and msg.content.text.text
local Diamondent = Black and msg.content.text.entities and msg.content.text.entities[1] and msg.content.text.entities[1].type._ == 'textEntityTypeMentionName'
if Black then
Black = Black:lower()
end
if msg.content._ == "messageText" and Black then
if Black:match('^[/#!]') then
Black = Black:gsub('^[/#!]','')
end
end
if Black then
if base:sismember(TD_ID..'CmDlist:'..msg.chat_id,Black) then
mmdi = base:hget(TD_ID..'CmD:'..msg.chat_id,Black)
Black = Black:gsub(Black,mmdi)
end
end
BaseCmd = 'BabakKhan'
if Black and Black:match(' ') then
CmdB = Black:split(' ')
BaseCmd = CmdB[1]
end
if Black1 and base:get(TD_ID..'settext_dokme_'..msg.sender_id.user_id) then
local txt = base:get(TD_ID..'settext_'..msg.sender_id.user_id) or 0
Button = {
{
{text = ''..txt,url=''..Black1}}}
TD.sendText(msg.chat_id,msg.id,txt, 'html', true,false,false,false,keyboards(Button))
base:del(TD_ID..'settext_dokme_'..msg.sender_id.user_id)
end

if base:get(TD_ID..'settext_dokme'..msg.sender_id.user_id) then
send(msg.chat_id,msg.id,'متن شما تنظیم شد\nاکنون لینک دکمه را ارسال کنید','md')
base:set(TD_ID..'settext_'..msg.sender_id.user_id,Black1)
base:setex(TD_ID..'settext_dokme_'..msg.sender_id.user_id,120,true)
base:del(TD_ID..'settext_dokme'..msg.sender_id.user_id)
end
--------------MSG TYPE----------------
if msg.content["@type"] == "messageText" then
MsgType = 'text'
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] >> "..msg.content.text.text)
end
if msg.content.caption and msg.content.caption.text then
print(os.date("%H:%M:%S").."  |  ["..msg.sender_id.user_id.."] >> Photo Caption : "..msg.content.caption.text) 
end
if msg.content["@type"] == "messageChatAddMembers" then
print("["..msg.sender_id.user_id.."] Added a User")
for i=1,#msg.content.member_user_ids do
msg.add = msg.content.member_user_ids[i]
MsgType = 'AddUser' 
end 
end
if msg.content["@type"] == "messageChatJoinByLink" then
base:incr(TD_ID..'Total:JoinedByLink:'..msg.chat_id)
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
if msg.content["@type"] == "messageVoiceNote" then
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

--auto add
if msg.add and msg.add == BotJoiner then
text = [[
• ربات با موفقیت به گروه شما اضافه شد!

• جهت عملکرد باید ربات را ادمین کامل گروه نمایید

• تا فرآیند نصب و پیکربندی انجام شود
]]
sendApi(msg.chat_id,text,0,'md')
end
--------------- >>GlobalyBan<< ---------------
if msg.sender_id.user_id and is_GlobalyBan(msg.sender_id.user_id) or is_Banned(msg.chat_id,msg.sender_id.user_id) then
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar and datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
KickUser(msg.chat_id,msg.sender_id.user_id) 
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
local xname = TD.getUser(msg.sender_id.user_id).first_name or msg.sender_id.user_id
local unm_ = '<a href="tg://user?id='..msg.sender_id.user_id..'">'..xname..'</a>'
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
if not base:get(TD_ID..'is_GlobalyBan:'..msg.sender_id.user_id) then
send(msg.chat_id,0,'|↜ کاربر : '..unm_..' به دلیل بودن در لیست مسدودی هاے ربات از گروه اخراج شد !','html')
base:setex(TD_ID..'is_GlobalyBan:'..msg.sender_id.user_id,80,true)
end
end
else
end
end
if msg.sender_id.user_id and is_GlobalyBan_(msg.sender_id.user_id) then
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar and datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
KickUser(msg.chat_id,msg.sender_id.user_id) 
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
local xname = TD.getUser(msg.sender_id.user_id).first_name or msg.sender_id.user_id
local unm_ = '<a href="tg://user?id='..msg.sender_id.user_id..'">'..xname..'</a>'
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
if not base:get(TD_ID..'is_GlobalyBan_:'..msg.sender_id.user_id) then
send(msg.chat_id,0,'|↜ کاربر : '..unm_..' به دلیل بودن در لیست مسدود سراسری ربات از گروه اخراج شد !','html')
base:setex(TD_ID..'is_GlobalyBan_:'..msg.sender_id.user_id,80,true)
end
end
else
end
end

if msg.add then
if is_GlobalyBan(msg.add) or is_GlobalyBan_(msg.add) or is_Banned(msg.chat_id,msg.add) then
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar and datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
KickUser(msg.chat_id,msg.add) 
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
local xname = TD.getUser(msg.add).first_name or msg.add
local unm_ = '<a href="tg://user?id='..msg.add..'">'..xname..'</a>'
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
if not base:get(TD_ID..'is_GlobalyBan:'..msg.sender_id.user_id) then
send(msg.chat_id,0,'|↜ کاربر : '..unm_..' به دلیل بودن در لیست مسدودی هاے ربات از گروه اخراج شد !','html')
base:setex(TD_ID..'is_GlobalyBan:'..msg.sender_id.user_id,80,true)
end
end
else
end
end
end
--------------- >>Join Off<< ---------------
local joinoff = base:sismember(TD_ID..'Gp:'..msg.chat_id,'Join')
if MsgType == 'JoinedByLink' and joinoff and not is_Sudo(msg) then
KickUser(msg.chat_id,msg.sender_id.user_id)
end

if is_supergroup(msg) then
base:incr(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..msg.sender_id.user_id)
base:incr(TD_ID..'Total:messages:'..msg.chat_id..':'..msg.sender_id.user_id)
base:incr(TD_ID..'Total:messages:'..msg.chat_id)
base:sadd(TD_ID..'AllUsers:'..msg.chat_id,msg.sender_id.user_id)
end
base:incr(TD_ID .. "All:Message:" .. msg.chat_id)
base:sadd(TD_ID .. "Sender_user_ids:" .. msg.chat_id, msg.sender_id.user_id)
base:incr(TD_ID .. "Content_Message:Msgs:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:MsgsDay:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs2Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs3Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs4Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs5Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs6Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:Msgs7Day:" .. msg.sender_id.user_id .. ":" .. msg.chat_id)
--ForceAdd
if is_supergroup(msg) then
if (msg.sender_id.user_id or msg.add) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forceadd') and not is_Vip(msg) and not base:sismember(TD_ID..'VipAdd:'..msg.chat_id,msg.sender_id.user_id)  then
if newuser then
if MsgType == 'JoinedByLink' then
base:sadd(TD_ID..'NewUser'..msg.chat_id,msg.sender_id.user_id)
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
if msg.add then
base:sadd(TD_ID..'NewUser'..msg.chat_id,msg.add)
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
end
if not newuser or newuser and base:sismember(TD_ID..'NewUser'..msg.chat_id,msg.sender_id.user_id) then
if msg.add then
local diamond = TD.getUser(msg.sender_id.user_id)
result = TD.getUser(msg.add)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if result.type["@type"] == "userTypeBot" then
send(msg.chat_id,0,"|↜ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nشما یک ربات به گروه اضافه کردید\nلطفا یک کاربر عادے اضافه کنید","md")
KickUser(msg.chat_id,result.id)
else
addkard = tonumber(added) + 1
if tonumber(addkard) == tonumber(Forcemax) then
txt = "|↜ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nشما اکنون میتوانید پیام ارسال کنید ✔"
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,msg.sender_id.user_id..'AddEnd') then
send(msg.chat_id,0,txt,'md')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,msg.sender_id.user_id..'AddEnd')
end
end
base:set(TD_ID..'addeduser'..msg.chat_id..msg.sender_id.user_id,addkard)
end
end
if tonumber(added) < tonumber(Forcemax) then
if not (msg.content["@type"] == "messageChatJoinByLink" or msg.content["@type"] == "messageChatAddMembers" or msg.content["@type"] == "messageChatDeleteMember") then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
totalpms = base:get(TD_ID..'pmdadeshode'..msg.chat_id..msg.sender_id.user_id..os.date("%Y/%m/%d")) or 0
if tonumber(Forcepm) > tonumber(totalpms) then
local totalpmsmrr = tonumber(totalpms) + 1
local mande = tonumber(Forcemax) - tonumber(added)
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
nm = '<a href="tg://user?id='..msg.sender_id.user_id..'">'..name..'</a>'
ads = "【"..Forcemax.."/"..added.."】"
wrn = "【"..Forcepm.."/"..totalpmsmrr.."】"
if base:get(TD_ID..'TextForce:'..msg.chat_id) then
txtt = base:get(TD_ID..'TextForce:'..msg.chat_id)
else
txtt = "✦ کاربر :【"..nm.."】\nشما باید【"..mande.."】نفر را\nبه گروه دعوت کنید تا بتوانید در گروه پیام ارسال کنید\n>#تعداداداجباری : 【"..Forcemax.."/"..added.."】\n>#اخطار : 【"..Forcepm.."/"..totalpmsmrr.."】"
end
local txtt = txtt:gsub('name',nm)
local txtt = txtt:gsub('number',mande)
local txtt = txtt:gsub('add',ads)
local txtt = txtt:gsub('warn',wrn)
xl = base:get(TD_ID..'TextDok:'..msg.chat_id) or 'معاف کردن'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..xl..'',callback_data='Moaf:'..msg.sender_id.user_id..':'..msg.chat_id..':'..name}}}
send_inline(msg.chat_id,txtt,keyboard,'html')
base:set(TD_ID..'pmdadeshode'..msg.chat_id..msg.sender_id.user_id..os.date("%Y/%m/%d"),totalpmsmrr)
end
end
end
end
end

function IDBots(user)
local var = false
local List = {Config.BotJoiner,Config.Full_Sudo,Config.BotCliId,Sudoid}
for k,v in pairs(List) do
if tonumber(v) == tonumber(user) then
var = true
end
end
return var
end

function sortTable(input)
if #input == 0 then
return input
end
local hash = {}
for _,v in ipairs(input) do
    hash[v] = true
end
local res = {}
for k,_ in pairs(hash) do
    res[#res+1] = k
end
return res
end
function getGroupMembers(chat_id, filter, status, offset)
local args = {offset = 0, all_members = {}, user_id = {}, chat_id = {}}
for i = 1, tonumber(offset) do
 local data = TD.getSupergroupMembers(chat_id, filter, '', args.offset, 200)
  if data.members then 
   for k,v in pairs(data.members) do
    if v.member_id and (v.member_id.user_id or v.member_id.chat_id) then
     if v.member_id.user_id then
      result = {_ = 'user', id = v.member_id.user_id}
     else
      result = {_ = 'chat', id = v.member_id.chat_id}
     end
      if status == 'all' and not IDBots(result.id) then
       table.insert(args.all_members, result.id)
      elseif status == 'user_id' and result._ == 'user' and not IDBots(result.id) then
       table.insert(args.user_id, result.id)
      elseif status == 'chat_id' and result._ == 'chat' and not IDBots(result.id) then
       table.insert(args.chat_id, result.id)
      end
     end
    end
   end
  args.offset = args.offset + 100
 end
if status == 'all' then
 input = args.all_members
elseif status == 'user_id' then
 input = args.user_id
elseif status == 'chat_id' then
 input = args.chat_id
else
 input = {}
end
return sortTable(input)
end
----------Msg Checks-------------
local chat = msg.chat_id
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then
if not is_Owner(msg) then
if base:get(TD_ID..'Lock:Pin:'..chat) then
if msg.content["@type"] == 'messagePinMessage' then
print 'Pinned By Not Owner'
send(chat,msg.id,'فقط مالکان\n','md')
TD.unpinChatMessage(chat)
--TD.unpinAllChatMessages(chat)
local PIN_ID = base:get(TD_ID..'Pin_id'..chat)
if PIN_ID then
TD.pinChatMessage(msg.chat_id,tonumber(PIN_ID))
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
local DIAMOND = (msg.content.caption and msg.content.caption.text) or (msg.content.text and msg.content.text.text)
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
if msg.content.caption and msg.content.caption.text then
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
if msg.reply_markup and msg.reply_markup._ == "replyMarkupInlineKeyboard" then
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
if msg.content._ == 'messageVideoNote' then
MsgCheck(msg,'ارسال #ویدیومسیج','Selfi','ویدیومسیج')
end
--<><>File<><>--
if MsgType == 'Document' then
if msg.content.document.file_name:match("[\216-\219][\128-\191]") or msg.content.caption.text:match("ضدفیلتر") or msg.content.caption.text:match("ضد فیلتر") and msg.content.document.file_name:match(".[Aa][Pp][Kk]") then
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
--<><>Sticker2<><>--
if msg.content["@type"] == 'messageUnsupported' then
MsgCheck(msg,'ارسال #استیکر متحرک','Stickers','استیکر متحرک')
end
--<><>ChannelPost<><>--
--if msg.views ~= 0 then
--MsgCheck(msg,'ارسال #پست‌کانال','Channelpost','پست کانال')
--end
--<><>Spam<><>--
if msg.content.text and msg.content.text.text then
num = tonumber(base:get(TD_ID..'NUM_CH_MAX:'..msg.chat_id)) or 3600
chars = utf8.len(msg.content.text and msg.content.text.text)
if chars > num then
MsgCheck(msg,'ارسال #هرزنامه','Spam','هرزنامه')
end
end
if tonumber(reply_id) > 0 then
MsgCheck(msg,'ریپلای#','Rep','ریپلای')
end
-------------------limitpm-------------------
Msgsday = tonumber(base:get(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..user or 00000000)) or 0
local limitmsg = tonumber(base:get(TD_ID..'limitpm:'..msg.chat_id) or 100)
if Msgsday > limitmsg and (not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'limitpm'..user)) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'limitpm:on') then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
send(chat,msg.id,"✦ کاربر :【"..name.."】\nبه دلیل رسیدن به محدودیت حداکثر پیام در روز به مدت "..timemutemsg.."\nثانیه از ارسال پیام محدود میشوید.", 'md')
MuteUser(chat,user,msg.date+timemutemsg)
base:sadd(TD_ID..'limituser:'..msg.chat_id,user)
end
----------BioLink and FilterBio-----------
if msg.sender_id.user_id then
local result = TD.getUserFullInfo(msg.sender_id.user_id) 
if result and result.bio and result.bio.text == '' then
DiamondAbout = 'Nil'
else  
DiamondAbout = result.bio.text
end
if DiamondAbout:match("[Tt].[Mm][Ee]/") or DiamondAbout:match("[Tt][Ll][Gg][Rr][Mm].[Mm][Ee]/") or DiamondAbout:match("[Hh][Tt][Pp][Ss]") then
MsgCheck(msg,'#داشتن لینک در بیو','Biolink','لینک بیو')
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'BioAntiTabchi') then
users = {'خاله','جنده','سکس','پیوی','کونی','سکسی','jende','pv','شماره','t.me','https'} or base:smembers(TD_ID..'FilterBio:'..msg.chat_id)
if #users > 0 then
for k,v in pairs(users) do
mMd = DiamondAbout:lower()
if mMd:match(v) then
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
mm = 'محدود'
else
KickUser(msg.chat_id,msg.sender_id.user_id)
mm = 'اخراج'
end
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if not (msg.content["@type"] == "messageChatAddMembers" or msg.content["@type"] == "messageChatDeleteMember") then
end
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then 
send(msg.chat_id,0,'کاربر '..MBD(name,msg.sender_id.user_id)..' به دلیل داشتن بیوگرافی غیرمجاز از گروه '..mm..' شد !','md')
end
end
end
end
end
end
if msg.sender_id.user_id then
if base:sismember(TD_ID..'Gp2:'..chat_id,'NameAntiTabchi') then
users = {'خاله','جنده','سکس','پیوی','کونی','سکسی','jende','pv','شماره'} or base:smembers(TD_ID..'FilterName:'..msg.chat_id) 
if #users > 0 then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
for k,v in pairs(users) do
mMd = diamond.first_name:lower()
if mMd:match(v) then
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
mm = 'محدود'
else
KickUser(msg.chat_id,msg.sender_id.user_id)
mm = 'اخراج'
end
if not (msg.content["@type"] == "messageChatAddMembers" or msg.content["@type"] == "messageChatDeleteMember") then
end
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm') then
send(msg.chat_id,0,'کاربر '..MBD(name,msg.sender_id.user_id)..' به دلیل داشتن اسم غیرمجاز از گروه '..mm..' شد !','md')
end
end
end
end
end
end
--------force join-------- 
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forcejoin') then
local Ch = (base:get(TD_ID..'setch:'..msg.chat_id) or '..Channel..')
local url , res = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id=@'..Ch..'&user_id='..msg.sender_id.user_id)
if res ~= 200 then
end
Joinchanel = json:decode(url)
if not is_GlobalyBan(msg.sender_id.user_id) and (not Joinchanel.ok or Joinchanel.result.status == "left" or Joinchanel.result.status == "kicked") and not is_Sudo(msg) and not is_Mod(msg) then
print 'Force Join'
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
MsgId = base:get(TD_ID..'msgid_joins_'..msg.chat_id)
countmem = base:smembers(TD_ID..'Force_Member:'..msg.chat_id)
if #base:smembers(TD_ID..'Force_Member:'..msg.chat_id) > 2 or not MsgId then
if not base:sismember(TD_ID..'Force_Member:'..msg.chat_id,msg.sender_id.user_id) then
if MsgId then
TD.deleteMessages(msg.chat_id,{[1] = MsgId})
base:del(TD_ID..'Force_Member:'..msg.chat_id)
end
bd = '• برای ارسال پیام در گروه باید عضو کانال گروه باشید ، لطفاً با استفاده از دکمه زیر در کانال عضو شوید !\n\n⚠️ شما در کانال عضو نیستید:\n<a href="tg://user?id='..msg.sender_id.user_id..'">'..name..'</a>'
Button = {
{
{text = '✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Ch}
}
}
TD.sendText(msg.chat_id,msg.id,bd, 'html', true, false, false, false,keyboards(Button))
base:sadd(TD_ID..'Force_Member:'..msg.chat_id,msg.sender_id.user_id)
end
else
if not base:sismember(TD_ID..'Force_Member:'..msg.chat_id,msg.sender_id.user_id) and MsgId then
base:sadd(TD_ID..'Force_Member:'..msg.chat_id,msg.sender_id.user_id)
bd = '• برای ارسال پیام در گروه باید عضو کانال گروه باشید ، لطفاً با استفاده از دکمه زیر در کانال عضو شوید !\n\n⚠️ شما در کانال عضو نیستید:\n'
countmem = base:smembers(TD_ID..'Force_Member:'..msg.chat_id)
for u,i in pairs(countmem) do 
local UsEr , mrr  = https.request('https://api.telegram.org/bot'..JoinToken..'/getChat?chat_id='..i)
if mrr == 200 then
UsEr = json:decode(UsEr)
if UsEr.ok == true then
if UsEr.result.first_name == '' then
nme = UsEr.id
else
nme = UsEr.result.first_name
end
bd = bd..'<a href="tg://user?id='..i..'">'..nme..'</a>\n'
end
end
end
Button_ = {{
{text='✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Ch}
}}
TD.editMessageText_(msg.chat_id,tonumber(MsgId),keyboards(Button_),bd,'html')
end
end
else
return true
end
end
-----------------Bot-----------------
if msg.add then
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
local result = TD.getUser(msg.add)
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local banbotpm = base:sismember(TD_ID..'Gp2:'..chat,'kickbotpm')
if result.type._ == "userTypeBot" then 
if base:get(TD_ID..'Lock:Bots'..chat) == 'kick' then
if banbotpm then 
send(chat,0,"✦ نام اِضافہ ڪنندهٔ ربات : 【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\n\nآیدےِ ربات اِضافہ شده :【["..result.usernames.editable_username.."](tg://user?id="..msg.add..")】\n\nکاربر و ربات #اخراج شدند\n─┅━━━━━━━┅─\n℘ دلیل اخراج : افزودن #ربات","md")
end
KickUser(chat,user)
KickUser(msg.chat_id,result.id)
UnRes(chat,user)
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'ban' then
if banbotpm then 
send(chat,0,"✦ نام اِضافہ ڪنندهٔ ربات : 【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\n\nآیدےِ ربات اِضافہ شده :【["..result.usernames.editable_username.."](tg://user?id="..msg.add..")】\n\nکاربر و ربات #مسدود شدند\n─┅━━━━━━━┅─\n℘ دلیل مسدودیت : افزودن #ربات","md")
end
KickUser(chat,user)
KickUser(msg.chat_id,result.id)
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'del' then
KickUser(msg.chat_id,result.id)
local results = TD.getSupergroupMembers(msg.chat_id,'Bots', '', 0, 200)
if results.members then
for k,v in pairs(results.members) do
if tonumber(v.member_id.user_id) ~= tonumber(BotJoiner) then
KickUser(msg.chat_id,v.member_id.user_id)
print(v.member_id.user_id)
end
end
end end
if not(base:get(TD_ID..'Lock:Bots'..chat) == 'ban' or base:get(TD_ID..'Lock:Bots'..chat) == 'kick') then
if base:get(TD_ID..'Lock:Bots'..chat) == 'mute' then
if banbotpm then
send(chat,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nبه مدت【"..timemutemsg.."】ثانیه از ارسال پیام #محدود شد\n─┅━━━━━━━┅─\n℘ دلیل محدودیت : افزودن #ربات","md")
end
KickUser(chat,result.id)
MuteUser(chat,user,msg.date+timemutemsg)
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'silent' then
if banbotpm then
send(chat,0,'✦ کاربر :【['..name..'](tg://user?id='..msg.sender_id.user_id..')】\n#سایلنت شد\n─┅━━━━━━━┅─\n℘ دلیل سایلنت : افزودن #ربات','md')
end
base:sadd(TD_ID..'MuteList:'..chat,user or 00000000)
--TD.deleteMessages(chat,{[1] = msg.id})
end
if base:get(TD_ID..'Lock:Bots'..chat) == 'warn' then
if tonumber(warnhashbd) == tonumber(max_warn) then
KickUser(chat,user)
send(chat,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nبه علت گرفتن حداکثر #اخطار از گروه #اخراج شد\n℘ دلیل اخطار و اخراج : افزودن #ربات\n─┅━━━━━━━┅─\n● #اخطارها : "..warnhashbd.."/"..max_warn.."","md")
base:hdel(hashwarnbd,chat,max_warn)
else
base:hset(hashwarnbd,chat, tonumber(warnhashbd) +1)
send(chat,0,"✦ کاربر :【["..name.."](tg://user?id="..msg.sender_id.user_id..")】\nشما یک #اخطار دریافت کردید\n─┅━━━━━━━┅─\n℘ دلیل اخطار : افزودن #ربات\n● #اخطارها : "..warnhashbd.."/"..max_warn.."","md")
KickUser(chat,result.id)
end
end

end
end
end
end
--<><>Anti Tabchi<><>--
if msg.content._ == "messageChatJoinByLink" and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'AntiTabchi') and not is_Mod(msg) then
--if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'FirstTabchiMute') then
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
--end
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
mmltxt = 'در گروه محدود خواهید شد !'
else
mmltxt = 'از گروه اخراج خواهید شد !'
end
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local keyboard = {}
TexT = '#احراز_هویت\n👤کاربر : <a href="tg://user?id='..msg.sender_id.user_id..'">'..name..'</a>\n🔑در صورتی که ربات نیستید به سوال زیر پاسخ دهید !\n⚠️در صورتی که به این سوال تا 15 دقیقه آینده پاسخ ندهید و یا به هر دو سوال پاسخ اشتباه دهید '..mmltxt
Saeed = {'BD','Mrr619'}
Saeedrr = {'BD','Mrr619','Babak','TeleDiamondCh'}
BDAntiTabchi = Saeedrr[math.random(#Saeedrr)]
if Saeed[math.random(#Saeed)] == 'Mrr619' then
mrr619 = {0,1,2,3,4,5,6,7,8,9}
randnum = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum2 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum3 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum4 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
if tonumber(randnum) == tonumber(randnum2) or tonumber(randnum) == tonumber(randnum3) or tonumber(randnum) == tonumber(randnum3) then
randnum = 1000
end
if BDAntiTabchi == 'Mrr619' then
keyboard.inline_keyboard = {{
{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id},
{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id}
},{
{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id},
{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id}
},{
{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'BD' then
keyboard.inline_keyboard = {{
{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id},
{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id}
},{
{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id},
{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id}
},{
{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'TeleDiamondCh' then
keyboard.inline_keyboard = {{
{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id},
{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id}
},{
{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id},
{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id}
},{
{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'Babak' then
keyboard.inline_keyboard = {{
{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id},
{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id}
},{
{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id},
{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id}
},{
{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
local randnum = randnum:gsub("[0123456789]", {["0"] = "0️⃣", ["1"] = "1️⃣", ["2"] = "2️⃣", ["3"] = "3️⃣", ["4"] = "4️⃣", ["5"] = "5️⃣", ["6"] = "6️⃣", ["7"] = "7️⃣", ["8"] = "8️⃣", ["9"] = "9️⃣"})
send_inline(msg.chat_id,TexT..'\n\n>معکوس عدد '..randnum..' را از میان دکمه های زیر پیدا کرده و بر روی آن کلیک کنید !',keyboard,'html')
else
mrr619 = {'❤️','😍','✅','😭','🍦','🍌','🍉','🍏','🍎','🦆','💰','🔑','🐥','🎀','🎈','🔧','🗡','🤖','💄','💍','🐒','⚽️','0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣','🔟','✔️','⚫️','🔴','🔵','⚪️','🇮🇷'}
randnum = mrr619[math.random(#mrr619)]
randnum2 = mrr619[math.random(#mrr619)]
randnum3 = mrr619[math.random(#mrr619)]
randnum4 = mrr619[math.random(#mrr619)]
if tostring(randnum) == tostring(randnum2) or tostring(randnum) == tostring(randnum3) or tostring(randnum) == tostring(randnum3) then
randnum = '😡'
end
if BDAntiTabchi == 'Mrr619' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'BD' then
keyboard.inline_keyboard = {
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id},{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'TeleDiamondCh' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id}},
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
if BDAntiTabchi == 'Babak' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>2:'..msg.chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>3:'..msg.chat_id}},
{{text = randnum4,callback_data='bd:IsTabchiTrue>'..msg.sender_id.user_id..'>1:'..msg.chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.sender_id.user_id..':'..msg.chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.sender_id.user_id..':'..msg.chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.sender_id.user_id..':'..msg.chat_id}},}
end
local randnum = randnum:gsub(randnum,{["3️⃣"] = "شماره سه", ["4️⃣"] = "شماره چهار", ["5️⃣"] = "شماره پنج", ["6️⃣"] = "شماره شیش", ["7️⃣"] = "شماره هفت", ["8️⃣"] = "شماره هشت", ["9️⃣"] = "شماره نه", ["❤️"] = "قلب",["0️⃣"] = "شماره صفر", ["1️⃣"] = "شماره یک", ["2️⃣"] = "شماره دو",  ["😍"] = "😍", ["✅"] = "✅", ["🍌"] = "موز",  ["💰"] = "💰", ["🔑"] = "🔑", ["🐥"] = "جوجه", ["🎀"] = "پاپیون", ["🎈"] = "بادکنک قرمز", ["🔧"] = "اچهار فرانسه", ["🗡"] = "شمشیر", ["🤖"] = "ربات", ["💄"] = "رژ لب", ["💍"] = "انگشتر نگین دار", ["🐒"] = "میمون", ["⚽️"] = "توپ فوتبال", ["✔️"] = "تیک مشکی", ["⚫️"] = "دایره مشکی", ["🔴"] = "دایره قرمز", ["🔵"] = "دایره ابی", ["⚪️"] = "دایره سفید", ["🇮🇷"] = "پرچم ایران",["😡"] = "ادم عصبانی",["🍉"] = "هندوانه", ["🍏"] = "سیب سبز", ["🍎"] = "سیب قرمز", ["🦆"] = "اردک", ["😭"] = "گریه", ["🍦"] = "بستنی"})
send_inline(msg.chat_id,TexT..'\n\n> اموجی '..randnum..' را از میان دکمه های زیر پیدا کرده و بر روی آن کلیک کنید !',keyboard,'html')
end
base:sadd(TD_ID..'AntiTabchiUser'..msg.chat_id,msg.sender_id.user_id)
function BDClearPm()
if base:sismember(TD_ID..'AntiTabchiUser'..msg.chat_id,msg.sender_id.user_id) then
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..msg.sender_id.user_id)
if res ~= 200 then
end
local name_ = json:decode(url).result.user.first_name or ''..msg.sender_id.user_id
sendApi(msg.chat_id,'👤کاربر : <a href="tg://user?id='..msg.sender_id.user_id..'">'..name_..'</a>\nبه دلیل عدم احراز هویت در گروه محدود شد',0,'html')
MuteUser(msg.chat_id,msg.sender_id.user_id,0)
else
sendApi(msg.chat_id,'👤کاربر : <a href="tg://user?id='..msg.sender_id.user_id..'">'..name_..'</a>\nبه دلیل عدم احراز هویت از گروه اخراج شد',0,'html')
KickUser(msg.chat_id,msg.sender_id.user_id)
end
base:srem(TD_ID..'AntiTabchiUser'..msg.chat_id,msg.sender_id.user_id)
end
end
TD.set_timer(900,BDClearPm)
end
----------Filter------------
if Black and is_filter(msg,Black) then
TD.deleteMessages(msg.chat_id, {[1] = msg.id})

if base:sismember(TD_ID..'Gp:'..chat,'Ban:Filter') then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local datar = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if (datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.status.rights.can_restrict_members == true) then
send(chat,0,"✦ کاربر : ["..name.."](tg://user?id="..user..")\nاز گروه #مسدود شد\n─┅━━━━━━━┅─\n℘ دلیل مسدودیت : ارسال #کلمات فیلترشده","md")
KickUser(chat,user)
else
send(msg.chat_id,msg.id,'✦ کاربر : '..name..'\nباید به دلیل ارسال کلمه فیلترینگ از گروه اخراج شود ولی ربات به قسمت محرومیت کاربران  دسترسی ندارد !\nلطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید !','md')
end
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
if msg.content._ == "messageChatJoinByLink" or msg.content._ == "messageChatAddMembers" or msg.content._ == "messageChatDeleteMember" then
if base:sismember(TD_ID..'Gp:'..msg.chat_id,'TGservice') then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
end
if not is_Vip(msg) then
if base:sismember(TD_ID..'Silent_List:'..msg.chat_id,msg.sender_id.user_id) then
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end end
------------Chat Type------------
if is_FullSudo(msg) then
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
--<><><><>SetSudo
--[[if Black == 'setf' and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
base:sadd(TD_ID..'FULLS_SUDO',user)
send(msg.chat_id,msg.id,'OK','md')
end]]

if (Black == 'statsbot' or Black == 'امار کلی') then
local ListBot = io.popen('ls $PWD/.TD'):read('*all')
local NewList = {}
for id in string.gmatch(ListBot, 'Api(%d+)') do
table.insert(NewList,tonumber(id))
end
if #NewList == 0 then
TD.sendText(msg.chat_id,msg.id,'هیچ رباتی در سرور یافت نشد !', 'lg')
else
local all_group = 0
local Stext = 'لیست ربات های سرور :\n\n'
table.sort(NewList)
local ListMusic = {}
for k,v in pairs(NewList) do
--ListMusic[v] = 0
list = base:smembers(v..'SuperGp')
if #list ~= 0 then
for k,d in pairs(list) do
end
end
all_group = all_group + #list
owner = tostring(base:get(v.."sudo_username") or '----')
is_bot = tonumber(base:get(v..'BotJoiner') or 0000000)
Stext = Stext .. 'شماره ربات : ' .. v .. '\nمالک : @' .. owner ..'\nتعداد نصب ضدلینک : ' .. #list .. '\nایدی ربات : ' .. is_bot .. '\n---------------------\n'
end
local NewText = 'کل گروه های مدیریتی : ' .. all_group .. '\n\n' .. Stext
local file = io.open('./libs/data/Statsbot.txt', 'w')
file:write(NewText)
file:close()
TD.sendDocument(msg.chat_id, msg.id, './libs/data/Statsbot.txt', 'لیست آمار کلی ربات','html') 
end
end

if Black and (Black:match("^chargebot (%d+)$") or Black:match("^شارژ ربات (%d+)$")) then
local NumberCheck = Black:match("^chargebot (%d+)$") or Black:match("^شارژ ربات (%d+)$")
NumberCharge = tonumber(NumberCheck)
if NumberCharge < 1 then
Stext = ' اعتبار وارد شده نمیتوانید از *1* روز کمتر باشد !'
else
if NumberCharge > (86400 * 60) then
Stext = ' اعتبار وارد شده نمیتوانید از *60* روز بیشتر باشد !'
else
local time = NumberCharge * 86400
base:setex(TD_ID .. "ExpireBot:", time, true)
Stext = ' ربات به مدت ' .. NumberCheck .. ' روز شارژ شد !'
end
end
TD.sendText(msg.chat_id, 0, Stext, 'md')
end

if Diamondent and (Black:match('^setsudo (.*)') or Black:match('^افزودن سودو (.*)')) or Black and (Black:match('^setsudo @(.*)') or Black:match('^افزودن سودو @(.*)') or Black:match('^setsudo (%d+)$') or Black:match('^افزودن سودو (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^setsudo (.*)') or Black:match('^افزودن سودو (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^setsudo @(.*)') or Black:match('^افزودن سودو @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^setsudo (%d+)') or Black:match('^افزودن سودو (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^setsudo (.*)') or Black:match('^افزودن سودو (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'SUDO',mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات قرار دارد','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nبه لیست سودو هاے ربات افزوده یافت','md')
base:sadd(TD_ID..'SUDO',mrr619)
end
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if Black == 'setsudo' or Black == 'افزودن سودو' and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'SUDO',user) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات قرار دارد','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nبه لیست سودو هاے ربات افزوده یافت','md')
base:sadd(TD_ID..'SUDO',user)
end
end
end
--<><><><>RemSudo
if Diamondent and (Black:match('^remsudo (.*)') or Black:match('^حذف سودو (.*)')) or Black and (Black:match('^remsudo @(.*)') or Black:match('^حذف سودو @(.*)') or Black:match('^remsudo (%d+)$') or Black:match('^حذف سودو (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^remsudo (.*)') or Black:match('^حذف سودو (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^remsudo @(.*)') or Black:match('^حذف سودو @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^remsudo (%d+)') or Black:match('^حذف سودو (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^remsudo (.*)') or Black:match('^حذف سودو (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'SUDO',mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nاز لیست سودو هاے ربات حذف شد','md')
base:srem(TD_ID..'SUDO',mrr619)
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات نیست','md')
end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if Black == 'remsudo' or Black == 'حذف سودو' and tonumber(reply_id) ~= 0  then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'SUDO',user) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nاز لیست سودو هاے ربات حذف شد','md')
base:srem(TD_ID..'SUDO',user)
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nدر لیست سودو هاے ربات نیست','md')
end
end
end
if Black == 'sudolist' or Black == 'لیست سودو ها' then
local hash = TD_ID.."SUDO" 
local list = base:smembers(hash)
local t = '*لیست سودو هاے ربات :*\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do
local name = TD.getUser(v).first_name or v
t = t..k..'-【['..name..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then 
t = '*لیست سودو هاے ربات خالے مے باشد.*'
end
send(msg.chat_id,msg.id,t,'md')
end
if (Black == 'clean sudolist' or Black == 'پاکسازی لیست سودو') then
base:del(TD_ID..'SUDO')
send(msg.chat_id,msg.id,'🚮 لیست سودو هاے ربات پاکسازے شد','md')
end


if Black and (Black:match('^banalls (%d+)$') or Black:match('^بن ال سراسری (%d+)$')) then
local user = Black:match('^banalls (%d+)') or Black:match('^بن ال سراسری (%d+)')
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember('GlobalyBanned_:',user) then
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..user..'\nدر لیست بن ال سراسری وجود دارد','md')
end
else
if name then
send(msg.chat_id,msg.id,'✥ کاربر : \n🆔 : '..nm..'\nبه لیست بن ال سراسری افزوده شد','html')
else
send(msg.chat_id,msg.id,'✥ کاربر : \n🆔 : `'..user..'`\n_به لیست بن ال سراسری افزوده شد_','md')
end
base:sadd('GlobalyBanned_:',user)
end
end
if Black and (Black:match('^banalls @(.*)') or Black:match('^بن ال سراسری @(.*)')) then
local username = Black:match('^banalls @(.*)') or Black:match('^بن ال سراسری @(.*)')
local Diamond = TD.searchPublicChat(username)
if Diamond.id then
if VipUser(msg,Diamond.id) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
local nm = '<a href="tg://user?id='..Diamond.id..'">'..username..'</a>'
if base:sismember('GlobalyBanned_:', Diamond.id) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nبه لیست بن ال سراسری افزوده شد','html')
base:sadd('GlobalyBanned_:',Diamond.id)
end
else 
send(msg.chat_id, msg.id,'❎ کاربر یافت نشد','html')
end
end
if (Black == 'gbanall' or Black == 'لیست بن ال سراسری') then 
local list = base:smembers('GlobalyBanned_:') 
if #list == 0 then 
send(msg.chat_id,msg.id,'لیست کاربران #بن ال سراسری خالے میباشد','md')
else
local t = 'لیست کاربران بن ال سراسری:\nا┅┅──┄┄═✺═┄┄──┅┅\n' 
for k,v in pairs(list) do 
t = t..k..'-【'..v..'】\n\n'
end
local file = io.open("./libs/data/GbanAll-"..TD_ID..".txt","w")
file:write(t)
file:close()
sendDocument(msg.chat_id,msg.id,'./libs/data/GbanAll-'..TD_ID..'.txt','','md')
end end
if (Black == 'clean gbanall' or Black == 'پاکسازی لیست بن ال سراسری') then
base:del('GlobalyBanned_:')
send(msg.chat_id, msg.id,'⭕ لیست بن ال سراسری پاکسازے شد','md')
end
---------------Unbanall--------------
if Black and (Black:match('^unbanalls (%d+)$') or Black:match('^حذف بن ال سراسری (%d+)$')) then
local user = Black:match('unbanalls (%d+)') or Black:match('حذف بن ال سراسری (%d+)')
if base:sismember('GlobalyBanned_:',user) then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال سراسری حذف شد', 'html')
else
send(msg.chat_id, msg.id,'✦ کاربر : \n🆔 : `'..user..'`\n_از لیست بن ال سراسری حذف شد_','md') 
end
base:srem('GlobalyBanned_:',user)
else
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود ندارد','html')
else
send(msg.chat_id, msg.id,'✦ کاربر : \n🆔 : '..user..'\nدر لیست بن ال سراسری وجود ندارد','md') 
end
end
end
if Black and (Black:match('^unbanalls @(.*)') or Black:match('^حذف بن ال سراسری @(.*)')) then
local username = Black:match('^unbanalls @(.*)') or Black:match('^حذف بن ال سراسری @(.*)')
local Diamond = TD.searchPublicChat(username)
if Diamond.id then
local nm = '<a href="tg://user?id='..Diamond.id..'">'..username..'</a>'
if base:sismember('GlobalyBanned_:',Diamond.id) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال سراسری حذف شد', 'html')
base:srem('GlobalyBanned_:',Diamond.id)
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود ندارد','html')
end
else
send(msg.chat_id, msg.id,'❎ کاربر یافت نشد','md')
end
end
if is_supergroup(msg) then
if (Black == 'banalls' or Black == 'بن ال سراسری') and tonumber(reply_id) ~= 0  then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember('GlobalyBanned_:',user) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nبه لیست بن ال سراسری افزوده شد','html')
base:sadd('GlobalyBanned_:',user)
end
end
end
if (Black == 'unbanalls' or Black == 'حذف بن ال سراسری') and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember('GlobalyBanned_:',user) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال سراسری حذف شد','html')
base:srem('GlobalyBanned_:',user)
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال سراسری وجود ندارد','html')
end
end
end
end

if Black == 'vard' then
local datar = TD.getUserFullInfo(msg.sender_id.user_id).bio.text
send(msg.chat_id,0,serpent.block(datar, {comment=false}),'html')
end 

if (Black == 'txt') then
local reply_markup = TD.replyMarkup{
						type = 'remove',
						resize = false,
						is_personal = false,
						data = {
						{
							{text = '» آپدیت ربات', type = 'text'}
						},
						{
							{text = '» لیست مسدود سراسری', type = 'text'},
							{text = '» لیست سودو', type = 'text'}
						},
						{
							{text = '» لیست گروه ها', type = 'text'}
						},
					}
					}
TD.sendText(msg.chat_id, msg.id, 'OK !', 'md', true, false, false, false, reply_markup)
end

if (Black == 'create' or Black == 'ساخت دکمه شیشه ای') then
send(msg.chat_id,msg.id,'لطفا متن روی دکمه را ارسال کنید','md')
base:setex(TD_ID..'settext_dokme'..msg.sender_id.user_id,120,true)
end


end
----------------- pv -------------
--[[if msg.chat_id == tonumber(Sudoid) and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id,tonumber(reply_id))
if Diamond.forward_info then
if Diamond.forward_info._ == 'messageForwardedFromUser' then
local user = Diamond.forward_info.sender_id.user_id
if user then
print(user)
if tonumber(user) == tonumber(BotJoiner) or tonumber(user) == tonumber(Sudoid) then
send(msg.chat_id,msg.id,'Error ! ✗ (⊙▂⊙)','html')
else
if Diamond.content._ == 'messageText' then
if msg.content._ == 'messageText' then
send(user,0,'☵ پیام شما ┇ '..Diamond.content.text.text..'\n☵ پاسخ ┇ '..msg.content.text.text,'html')
end
if msg.content._ == "messageSticker" then 
TD.sendSticker(user,0,msg.content.sticker.sticker.persistent_id)
end
if msg.content._ == "messageVoice" then
TD.sendVoice(user, 0, msg.content.voice.voice.persistent_id,msg.content.voice.duration, msg.content.voice.waveform, '☵ در پاسخ به ┇ '..Diamond.content.text.text)
end
if msg.content._ == 'messageUnsupported' or msg.content._ == "messageVideo" or msg.content._ == "messagePhoto" or msg.content._ == "messageVideoNote" or msg.content._ == "messageDocument" or msg.content._ == "messageAudio" or msg.content._ == "messageContact" or msg.content._ == "messageLocation" then
TD.forwardMessages(user,msg.chat_id,{[1] = msg.id})
end
if msg.content._ == "messageAnimation" then
TD.sendGif(user, 0, msg.content.animation.animation.persistent_id,'☵ در پاسخ به ┇ '..Diamond.content.text.text)
end
send(msg.chat_id,msg.id,'✔️Sent !','html')
else
if msg.content._ == 'messageText' then
send(user,0,msg.content.text.text,'html')
end
if msg.content._ == "messageSticker" then 
TD.sendSticker(user,0,msg.content.sticker.sticker.persistent_id)
end
if msg.content._ == "messageVoice" then
TD.sendVoice(user, 0, msg.content.voice.voice.persistent_id,msg.content.voice.duration, msg.content.voice.waveform, '')
end
if msg.content._ == 'messageUnsupported' or msg.content._ == "messageVideo" or msg.content._ == "messagePhoto" or msg.content._ == "messageVideoNote" or msg.content._ == "messageDocument" or msg.content._ == "messageAudio" or msg.content._ == "messageContact" or msg.content._ == "messageLocation" then
TD.forwardMessages(user,msg.chat_id,{[1] = msg.id})
end
if msg.content._ == "messageAnimation" then
TD.sendGif(user, 0, msg.content.animation.animation.persistent_id,'')
end
send(msg.chat_id,msg.id,'✔️Sent !','html')
end
end
end
else
if Diamond.forward_info.author_signature == '' then
send(msg.chat_id,msg.id,'ارسال پیام ناموفق ... ! ❌\nپیام مورد نظر از کانال ارسال شده است !','html')
else
send(msg.chat_id,msg.id,'ارسال پیام ناموفق ... ! ❌\nکاربر فورارد پیام خود را بسته است !','html')
end
end
end
end ]]
if gp_type(msg.chat_id) == "pv" and not base:sismember(TD_ID..'GlobalyBanned:',msg.sender_id.user_id) and ( (#base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id) ~= 0 and Black and not (Black:match('chat (.*)$') or Black:match('چت (.*)$') or Black:match('(.*) on$') or Black:match('(.*) روشن$') or Black:match('(.*) off$') or Black:match('(.*) خاموش$') or Black:match('(.*)list$') or Black:match('^فیلترکردن +(.*)') or Black:match('^حذف فیلتر +(.*)') or Black:match('لیست(.*)') or Black:match('^filter +(.*)') or Black:match('(.*) فعال$') or Black:match('(.*) غیرفعال$') or Black:match('^قفل (.*)$') or Black:match('lock (.*)$') or Black:match('del (.*)$') or Black:match('warn (.*)$') or Black:match('mute (.*)$') or Black:match('kick (.*)$')or Black:match('ban (.*)$') or Black:match('^unlock (.*)$') or Black:match('^بازکردن (.*)$') or Black:match('cmd (.*)$') or Black:match('دستور (.*)$'))) or (Black and not Black:match('^100(%d+)$') and base:get(TD_ID..'getgp:'..msg.sender_id.user_id)) or Black and not (Black:match('^help$') or Black:match('^راهنما$') or Black:match('^setgp$') or Black:match('^ثبت گروه$') or Black:match('^delgp$') or Black:match('^حذف گروه$') or Black:match('^delgps$') or Black:match('^حذف گروها$') or Black:match('^mygps$') or Black:match('^/start$') or Black:match('^گروهای من$') or Black:match('^delac (.*)') or Black:match('^دیلیت اکانت(.*)') or Black:match('^psswd (.*)') or Black:match('رمز دیلیت اکانت (.*)') or Black:match('^نرخ$') or Black:match('^nerch$')) or not Black) and not is_Sudo(msg) then
if base:get(TD_ID..'MonShi:on') and not base:get(TD_ID..'getgp:'..msg.sender_id.user_id) then
local text = base:get(TD_ID..'monshi') or 'ok'
sendApi(msg.chat_id,text,0,'md')
end
if not base:get(TD_ID..'pmresan:on') then
TD.forwardMessages(Sudoid,msg.chat_id,{[1] = msg.id})
end
end
if gp_type(msg.chat_id) == "pv" and Black and not base:sismember(TD_ID..'GlobalyBanned:',msg.sender_id.user_id) then
cmdpv = Black:match('^help') or Black:match('start$') or Black:match('^راهنما') or Black:match('^setgp') or Black:match('^ثبت گروه') or Black:match('^delgp') or Black:match('^حذف گروه') or Black:match('^delgps') or Black:match('^حذف گروها$') or Black:match('^mygps') or Black:match('^گروهای من') or Black:match('^نرخ') or Black:match('^nerch')
if Black and not (cmdpv) and base:get(TD_ID..'NajVa'..msg.sender_id.user_id) then
Mrrosta = base:get(TD_ID..'NajVa'..msg.sender_id.user_id)
Split = Mrrosta:split('>')
user = Split[1]
chat = Split[2]
nameuser = Split[3]
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if tonumber(utf8.len(Black)) > 20 then
text = string.sub(Black,0,20)
base:setex(text,tonumber(day),string.sub(Black,21,99999))
MamaL = 'BDMrr'..text
else
MamaL = Black
end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..user..'::'..MamaL}}}
send_inline(chat,'👤کاربر : <a href="tg://user?id='..user..'">'..nameuser..'</a>\n🔐شما از طرف <a href="tg://user?id='..msg.sender_user_id..'">'..name..'</a> یک پیام مخفی دارید!\nبرای دیدن پیام کلیک کنید !',keyboard,'html')
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'نمایش پیام 🔓',callback_data = 'Najva::'..msg.sender_user_id..'::'..MamaL}}}
send_inline(msg.sender_id.user_id,'نجوای شما برای <a href="tg://user?id='..user..'">'..nameuser..'</a> ارسال شد !',keyboard,'html')
base:del(TD_ID..'NajVa'..msg.sender_user_id)
end
if not base:get(TD_ID..'block:on') and not is_Sudo(msg) then
if Black and (cmdpv) then
local spam = TD_ID..'user:' .. msg.sender_id.user_id .. ':spamer'
local msgs = tonumber(base:get(spam) or 0)
local autoblock = base:get(TD_ID..'autoblocknumber') or 5
if msgs > tonumber(autoblock) then
base:sadd(TD_ID..'GlobalyBanned:',user)
sendApi(msg.chat_id,'به دلیل اسپم شما مسدود جهانی شدید!',0,'md')
end
base:setex(spam,tonumber(5),msgs+1)
end
end
if Black:match('^100(%d+)$') then
if base:get(TD_ID..'getgp:'..msg.sender_id.user_id) then
local DiamonD = tonumber(Black:match('^(%d+)$'))
local Mod = base:sismember(TD_ID..'ModList:-'..DiamonD,msg.sender_id.user_id)
local Owner = base:sismember(TD_ID..'OwnerList:-'..DiamonD,msg.sender_id.user_id)
if base:sismember(TD_ID..'Gp2:-'..DiamonD,'added') then
if not (Mod or Owner or base:sismember(TD_ID..'SUDO',msg.sender_id.user_id)) then
send(msg.chat_id,msg.id,'|↜ شما از مدیران یا صاحبان این گروه نیستید...!',"md")
base:del(TD_ID..'getgp:'..msg.sender_id.user_id)
else
if base:sismember(TD_ID..'gpuser:'..msg.sender_id.user_id,'-'..DiamonD..'') then
send(msg.chat_id, msg.id,'|↜ تنظیم در خصوصے این گروه از قبل براے شما فعال بود...!',"md")
base:del(TD_ID..'getgp:'..msg.sender_id.user_id)
else
base:del(TD_ID..'getgp:'..msg.sender_id.user_id)
base:sadd(TD_ID..'gpuser:'..msg.sender_id.user_id,'-'..DiamonD..'')
send(msg.chat_id,msg.id,'|↜ انجام تنظیمات ربات در خصوصے با موفقیت فعال شد...!\nبراے دیدن راهنما کلمه (راهنما) را تایپ کنید.',"md")
end
end
else
send(msg.chat_id,msg.id,'|↜ این گروه در لیست مدیریتے ربات وجود ندارد  .',"md") base:del(TD_ID..'getgp:'..msg.sender_id.user_id)
end
end
if base:get(TD_ID..'delgp:'..msg.sender_id.user_id) then
local DiamonD = tonumber(Black:match('^(%d+)$'))
if base:sismember(TD_ID..'gpuser:'..msg.sender_id.user_id,'-'..DiamonD..'') then
send(msg.chat_id, msg.id,'|↜ تنظیم گروه در خصوصے ربات\n-'..DiamonD..'\nبراے شما غیرفعال شد..!',"md")
base:srem(TD_ID..'gpuser:'..msg.sender_id.user_id,'-'..DiamonD..'')
base:del(TD_ID..'delgp:'..msg.sender_id.user_id)
else
send(msg.chat_id,msg.id,'|↜تنظیم گروه در خصوصے ربات\n-'..DiamonD..'\nبراے شما فعال نیست..!',"md")
base:del(TD_ID..'delgp:'..msg.sender_id.user_id)
end
end
end

if (Black == 'help' or Black == 'راهنما') and is_JoinChannel(msg) then 
local text = [[
راهنماے خصوصے ربات :

`help/راهنما`
دریافت همین متن
ا┅┅──┄┄═✺═┄┄──┅┅﹃﹄﹃﹄
`setgp/ثبت گروه`
ثبت گروه براے انجام تنظیمات گروه در خصوصے ربات

نکته :
【فقط مخصوص کسانے است که ربات را خریدارے کرده اند و مدیر و یا صاحب ربات در گروه خود هستند
دقت کنید شما میتوانید چندین گروه را در این قسمت ثبت کنید و بطور همزمان در خصوصے ربات همه گروه ها را مدیریت کنید】
ا┅┅──┄┄═✺═┄┄──┅┅﹃﹄﹃﹄
`delgp/حذف گروه`
حذف گروه از تنظیم در خصوصے
ا┅┅──┄┄═✺═┄┄──┅┅﹃﹄﹃﹄
`delgps/حذف گروها`
حذف همه گروها از تنظیم در خصوصے
ا┅┅──┄┄═✺═┄┄──┅┅﹃﹄﹃﹄
`mygps/گروهاے من`
دیدن لیست گروهایے ک توسط شما در خصوصے ربات ثبت شده است

ا┅┅──┄┄═✺═┄┄──┅┅﹃﹄﹃﹄
`cmds help/راهنمای دستورات`
دریافت راهنماے دستورات ربات و اموزش کار با ربات
]]
send(msg.chat_id,msg.id,text,"md")

elseif (Black == 'setgp' or Black == 'ثبت گروه') and is_JoinChannel(msg) then
base:set(TD_ID..'getgp:'..msg.sender_id.user_id,true)
send(msg.chat_id,msg.id,'|↜ لطفا ایدے گروه خود را بدون (-) ارسال کنید\nدر صورتے که ایدے گروه خود را نمی دانید در گروه دستور (gid) یا (ایدے گروه) را ارسال کنید و شناسه گروه خود را در اینجا ارسال کنید.\nبراے لغو عملیات کلمه (لغو) یا (cancel) را ارسال کنید.',"md")

elseif (Black == 'delgp' or Black == 'حذف گروه') and is_JoinChannel(msg) then
base:set(TD_ID..'delgp:'..msg.sender_id.user_id,true)
send(msg.chat_id,msg.id,'⇜ لطفا ایدے گروه خود را بدون (-) ارسال کنید\nدر صورتی که ایدے گروه خود را نمیدانید در گروه دستور (id) یا (ایدے گروه) را ارسال کنید و شناسه گروه خود را در اینجا ارسال کنید.\nبراے لفو عملیات کلمه (لغو) یا (cancel) را ارسال کنید.',"md")
elseif (Black == 'cancel' or Black == 'لغو') then
if base:get(TD_ID..'getgp:'..msg.sender_id.user_id) or base:get(TD_ID..'delgp:'..msg.sender_id.user_id) then
 base:del(TD_ID..'getgp:'..msg.sender_id.user_id)
 base:del(TD_ID..'delgp:'..msg.sender_id.user_id)
send(msg.chat_id,msg.id,'|↜ عملیات ثبت گروه لغو شد...!',"md")
end
elseif (Black == 'delgps' or Black == 'حذف گروها') and is_JoinChannel(msg) then
base:del(TD_ID..'gpuser:'..msg.sender_id.user_id)
send(msg.chat_id,msg.id,'|↜ همه گروه ها از حالت تنظیم در خصوصے ربات خارج شدند...!',"md")
elseif Black == 'mygps' or Black == 'گروهای من' and is_JoinChannel(msg) then
local list = base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id)
local tlist = #base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id)
local t = '• تعداد گروه هاے شما : *'..tlist..'*\n•شناسه گروه هاے شما\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do 
t = ""..t.."گروه شماره *"..k.."*\nشناسه گروه :"..v.."\n"
end
if #list == 0 then
t = '|↜ شما گروهے ثبت نکرده اید...!\nدر صورتے که ربات را خریدارے کرده اید با دستور (setgp) یا (ثبت گروه) می توانید گروه خود را ثبت کرده و تنظیمات گروه خود را در خصوصے ربات انجام دهید.'
end
send(msg.chat_id,msg.id,t,'md')
end
end
---------------- End Pv -------------
if gp_type(msg.chat_id) == "pv" and #base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id) > 0 or is_supergroup(msg) and is_Owner(msg) or (is_Mod(msg) and Black and not (base:sismember(TD_ID..'LimitCmd:'..msg.chat_id,Black) or base:sismember(TD_ID..'LimitCmd:'..msg.chat_id,BaseCmd))) then
----------------delete----------------
local bd = msg.sender_id.user_id
local cht = msg.chat_id
local chat = msg.chat_id
local gps = base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id)
local tgps = #base:smembers(TD_ID..'gpuser:'..msg.sender_id.user_id)
if is_supergroup(msg) then
bdcht = msg.chat_id
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
bdcht = v
end
end
local function typegpadd(name,mrr)
if is_supergroup(msg) then
base:sadd(TD_ID..''..name..''..cht,mrr)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:sadd(TD_ID..''..name..''..v,mrr)
end
end
end
local function typegprem(name,mrr)
if is_supergroup(msg) then
base:srem(TD_ID..''..name..''..cht,mrr)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:srem(TD_ID..''..name..''..v,mrr)
end
end
end
local function typegpdel(name)
if is_supergroup(msg) then
base:del(TD_ID..''..name..''..cht)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:del(TD_ID..''..name..''..v)
end
end
end
local function typegpset(name,mrr)
if is_supergroup(msg) then
base:set(TD_ID..''..name..''..cht,mrr)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:set(TD_ID..''..name..''..v,mrr)
end
end
end
local function typegphset(name,mrr,r619)
if is_supergroup(msg) then
base:hset(TD_ID..''..name..''..cht,mrr,r619)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:hset(TD_ID..''..name..''..v,mrr,r619)
end
end
end
local function typegphdel(name,mrr)
if is_supergroup(msg) then
base:hdel(TD_ID..''..name..''..cht,mrr)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:hdel(TD_ID..''..name..''..v,mrr)
end
end
end
local owner = base:smembers(TD_ID..'OwnerList:'..msg.chat_id) 
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then

if base:get(TD_ID.."Filtering:"..msg.sender_id.user_id) then
local chaat = base:get(TD_ID.."Filtering:".. msg.sender_id.user_id)
local name = string.sub(msg.content.text,1,50)
if msg.content.text:match("^/[Dd]one$") then
if lang then
send(cht,msg.id,"> The *Operation* is Over ❗️","md")
else
send(cht,msg.id,"> عملیات به پایان رسید ❗️","md")
end
base:del(TD_ID.."Filtering:"..msg.sender_id.user_id,80,chaat)
elseif msg.content.text:match("^/[Cc]ancel$") then
if lang then
send(cht,msg.id,"> *Operation* Canceled ❗️","md")
else
send(cht,msg.id,"> عملیات لغو شد ❗️","md")
end
base:del(TD_ID.."Filtering:"..msg.sender_id.user_id,80,chaat)
elseif filter_ok(name) then
typegpadd('Filters:',name)
if lang then
send(cht,msg.id,"> Word ["..name..[[
] has been *Filtered* ❗️
If You No Longer Want To Filter a Word, Send The /done Command ❗️]],"md")
else
send(cht,msg.id,"> کلمه ["..name.."] فیلتر شد ❗️\nاگر کلمه ای دیگری را نمیخواهید فیلتر کنید دستور /done را ارسال نمایید ❗️","md")
end
base:setex(TD_ID.."Filtering:"..msg.sender_id.user_id,80,chaat)
else
if lang then
send(cht,msg.id,"> Word ["..name.."] Can Not *Filtering* ❗️","md")
else
send(cht,msg.id,"> کلمه ["..name.."] قابل فیلتر شدن نمیباشد ❗️","md")
end
base:setex(TD_ID.."Filtering:".. msg.sender_id.user_id,80,chaat)
return
end
end
if base:get(TD_ID.."Filterings:"..msg.sender_id.user_id) then
local chaat = base:get(TD_ID.."Filterings:".. msg.sender_id.user_id)
local name = string.sub(msg.content.text,1,50)
if msg.content.text:match("^/[Dd]one$") then
if lang then
send(cht,msg.id,"> The *Operation* is Over ❗️","md")
else
send(cht,msg.id,"> عملیات به پایان رسید ❗️","md")
end
base:del(TD_ID.."Filterings:"..msg.sender_id.user_id,80,chaat)
elseif msg.content.text:match("^/[Cc]ancel$") then
if lang then
send(cht,msg.id,"> *Operation* Canceled ❗️","md")
else
send(cht,msg.id,"> عملیات لغو شد ❗️","md")
end
base:del(TD_ID.."Filterings:"..msg.sender_id.user_id,80,chaat)
elseif filter_ok(name) then
typegprem('Filters:',name)
if lang then
send(cht,msg.id,"> Word ["..name..[[
] has been *UnFiltered* ❗️
If You No Longer Want To UnFilter a Word,Send The /done Command ❗️]],"md")
else
send(cht,msg.id,"> کلمه ["..name.."] از لیست فیلتر حذف شد ❗️\nاگر کلمه ای دیگری را نمیخواهید از لیست فیلتر حذف کنید دستور /done را ارسال نمایید ❗️","md")
end
base:setex(TD_ID.."Filterings:"..msg.sender_id.user_id,80,chaat)
else
if lang then
send(cht,msg.id,"> Word ["..name.."] Can Not *UnFiltering* ❗️","md")
else
send(cht,msg.id,"> کلمه ["..name.."] قابل حذف شدن از لیست فیلتر نمیباشد ❗️","md")
end
base:setex(TD_ID.."Filterings:".. msg.sender_id.user_id,80,chaat)
return
end
end
----
if base:get(TD_ID.."Filteringg:"..msg.sender_id.user_id) then
local chaat = base:get(TD_ID.."Filteringg:".. msg.sender_id.user_id)
local name = string.sub(msg.content.text,1,50)
if msg.content.text:match("^/[Dd]one$") then
if lang then
send(cht,msg.id,"> The *Operation* is Over ❗️","md")
else
send(cht,msg.id,"> عملیات به پایان رسید ❗️","md")
end
base:del(TD_ID.."Filteringg:"..msg.sender_id.user_id,80,chaat)
elseif msg.content.text:match("^/[Cc]ancel$") then
if lang then
send(cht,msg.id,"> *Operation* Canceled ❗️","md")
else
send(cht,msg.id,"> عملیات لغو شد ❗️","md")
end
base:del(TD_ID.."Filteringg:"..msg.sender_id.user_id,80,chaat)
elseif filter_ok(name) then
typegpadd('FilterName:',name)
if lang then
send(cht,msg.id,"> Names ["..name..[[
] has been *Filtered* ❗️
If You No Longer Want To Filter a Names,Send The /done Command ❗️]],"md")
else
send(cht,msg.id,"|↜ اسم "..name.."\nبه لیست اسامی غیرمجاز اضافه شد !\nاگر اسم دیگری را نمیخواهید فیلتر کنید دستور /done را ارسال نمایید ❗️","md")
end
base:setex(TD_ID.."Filteringg:"..msg.sender_id.user_id,80,chaat)
else
if lang then
send(cht,msg.id,"> Names ["..name.."] Can Not *Filtering* ❗️","md")
else
send(cht,msg.id,"> اسم ["..name.."] قابل فیلتر شدن نمیباشد ❗️","md")
end
base:setex(TD_ID.."Filteringg:".. msg.sender_id.user_id,80,chaat)
return
end
end
if base:get(TD_ID.."Filteringgs:"..msg.sender_id.user_id) then
local chaat = base:get(TD_ID.."Filteringgs:".. msg.sender_id.user_id)
local name = string.sub(msg.content.text,1,50)
if msg.content.text:match("^/[Dd]one$") then
if lang then
send(cht,msg.id,"> The *Operation* is Over ❗️","md")
else
send(cht,msg.id,"> عملیات به پایان رسید ❗️","md")
end
base:del(TD_ID.."Filteringgs:"..msg.sender_id.user_id,80,chaat)
elseif msg.content.text:match("^/[Cc]ancel$") then
if lang then
send(cht,msg.id,"> *Operation* Canceled ❗️","md")
else
send(cht,msg.id,"> عملیات لغو شد ❗️","md")
end
base:del(TD_ID.."Filteringgs:"..msg.sender_id.user_id,80,chaat)
elseif filter_ok(name) then
typegprem('FilterName:',name)
if lang then
send(cht,msg.id,"> Names ["..name..[[
] has been *UnFiltered* ❗️
If You No Longer Want To UnFilter a Names,Send The /done Command ❗️]],"md")
else
send(cht,msg.id,"|↜ اسم "..name.."\nاز لیست اسامی غیرمجاز حذف شد !\nاگر اسم دیگری را نمیخواهید از لیست حذف کنید دستور /done را ارسال نمایید ❗️","md")
end
base:setex(TD_ID.."Filteringgs:"..msg.sender_id.user_id,80,chaat)
else
if lang then
send(cht,msg.id,"> Names ["..name.."] Can Not *UnFiltering* ❗️","md")
else
send(cht,msg.id,"> اسم ["..name.."] قابل حذف شدن از لیست اسامی غیرمجاز نمیباشد ❗️","md")
end
base:setex(TD_ID.."Filteringgs:".. msg.sender_id.user_id,80,chaat)
return
end
end

if Black and (Black:match('^filter$') or Black:match('^فیلترکردن$')) and is_JoinChannel(msg) then
if lang then
send(cht,msg.id,"> Please *Submit* The Words You Want To *Filter* Individually ❗️\nTo *Cancel The Command*,Send The /cancel Command ","md")
else
send(cht,msg.id,"> لطفا کلماتی که میخواهید فیلتر شوند را به صورت تکی بفرستید ❗️\nبرای لغو عملیات دستور /cancel را ارسال نمایید","md")
end
base:setex(TD_ID.."Filtering:".. msg.sender_id.user_id,80,cht)
end
if Black and (Black:match('^remfilter$') or Black:match('^حذف فیلتر$')) and is_JoinChannel(msg) then
if lang then
send(cht,msg.id,"> Please *Submit* The Words You Want To *UnFilter* Individually ❗️\nTo *Cancel The Command*,Send The /cancel Command ","md")
else
send(cht,msg.id,">لطفا کلماتی که میخواهید از لیست فیلتر حذف شوند را به صورت تکی بفرستید ❗️\nبرای لغو عملیات دستور /cancel را ارسال نمایید","md")
end
base:setex(TD_ID.."Filterings:".. msg.sender_id.user_id,80,cht)
end
if Black and (Black:match('^filter +(.*)$') or Black:match('^فیلترکردن +(.*)$')) and is_JoinChannel(msg) then
if string.find(Black:match('^filter (.*)$') or Black:match('^فیلترکردن (.*)$'),"[%(%)%.%+%-%*%?%[%]%^%$%%]") then
send(cht,msg.id,'🖕😐','md')
else
local word = Black:match('^filter +(.*)$') or Black:match('^فیلترکردن +(.*)$')
typegpadd('Filters:',word)
send(cht,msg.id,'|↜ کلمه【'..word..'】\nبه لیست فیلتر افزوده شد','md')
end
end
if Black and (Black:match('^remfilter +(.*)$') or Black:match('^حذف فیلتر +(.*)$')) and is_JoinChannel(msg) then
local word = Black:match('^remfilter +(.*)$') or Black:match('^حذف فیلتر +(.*)$')
typegprem('Filters:',word)
send(cht,msg.id,'|↜ کلمه【'..word..'】\nاز لیست فیلتر حذف شد','md')
end
if (Black == 'clean filterlist' or Black == 'پاکسازی لیست فیلتر') and is_JoinChannel(msg) then
typegpdel('Filters:')
send(cht,msg.id,'|↜ لیست فیلتر پاکسازے شد','md')
end
if (Black == 'filterlist' or Black == 'لیست فیلتر') and is_JoinChannel(msg) then
if is_supergroup(msg) then
local list = base:smembers(TD_ID..'Filters:'..cht)
local t = '|↜ لیست کلمات فیلتر شده :\n'
for k,v in pairs(list) do 
t = t..k.."- *"..v.."*\n"
end
if #list == 0 then
t = '|↜ لیست کلمات فیلتر شده خالے است'
end
send(cht,msg.id,t,'md')
end
if gp_type(msg.chat_id) == "pv" then
local t = '|↜ لیست کلمات فیلتر شده در *'..tgps..'* گروه شما\nبراے دیدن لیست گروه ها میتوانید از دستور گروه هاے من یا [mygps] استفاده کنید.\n'
for k,v in pairs(gps) do
local list = base:smembers(TD_ID..'Filters:'..v)
for a,b in pairs(list) do
t = ""..t..""..b.."\nدر گروه *"..k.."*\n﹃﹄﹃﹄﹃﹄\n"
end
end
send(cht,msg.id,t,'md')
end
end
--- filter name
if Black and (Black:match('^filtername$') or Black:match('^فیلتر اسم$')) and is_JoinChannel(msg) then
if lang then
send(cht,msg.id,"> Please *Submit* The Names You Want To *Filter* Individually ❗️\nTo *Cancel The Command*,Send The /cancel Command ","md")
else
send(cht,msg.id,"> لطفا نام هایی که میخواهید فیلتر شوند را به صورت تکی بفرستید ❗️\nبراے لغو عملیات دستور /cancel را ارسال نمایید","md")
end
base:setex(TD_ID.."Filteringg:".. msg.sender_id.user_id,80,cht)
end
if Black and (Black:match('^remfiltername$') or Black:match('^حذف فیلتر اسم$')) and is_JoinChannel(msg) then
if lang then
send(cht,msg.id,"> Please *Submit* The Names You Want To *UnFilter* Individually ❗️\nTo *Cancel The Command*,Send The /cancel Command ","md")
else
send(cht,msg.id,">لطفا نام هایی که میخواهید از لیست فیلتر غیرمجاز حذف شوند را به صورت تکی بفرستید ❗️\nبرای لغو عملیات دستور /cancel را ارسال نمایید","md")
end
base:setex(TD_ID.."Filteringgs:".. msg.sender_id.user_id,80,cht)
end
if Black and (Black:match('^filtername +(.*)$') or Black:match('^فیلتر اسم +(.*)$')) and is_JoinChannel(msg) then
if string.find(Black:match('^filtername (.*)$') or Black:match('^فیلتر اسم (.*)$'),"[%*?^$]") then
send(cht,msg.id,'🖕😐','md')
else
local word = Black:match('^filtername +(.*)$') or Black:match('^فیلتر اسم +(.*)$')
typegpadd('FilterName:',word)
send(cht,msg.id,'|↜ اسم '..word..'\nبه لیست اسامی غیرمجاز اضافه شد !','md')
end
end
if Black and (Black:match('^remfiltername +(.*)$') or Black:match('^حذف فیلتر اسم +(.*)$')) and is_JoinChannel(msg) then
local word = Black:match('^remfiltername +(.*)$') or Black:match('^حذف فیلتر اسم +(.*)$')
typegprem('FilterName:',word)
send(cht,msg.id,'|↜ اسم '..word..'\nاز لیست اسامی غیرمجاز حذف شد !','md')
end
if (Black == 'clean filternamelist' or Black == 'پاکسازی لیست اسامی غیرمجاز') and is_JoinChannel(msg) then
typegpdel('FilterName:')
send(cht,msg.id,'|↜ لیست اسامی غیرمجاز شده پاکسازی شد !','md')
end
if (Black == 'filternamelist' or Black == 'لیست اسامی غیرمجاز') and is_JoinChannel(msg) then
if is_supergroup(msg) then
local list = base:smembers(TD_ID..'FilterName:'..cht)
local t = '|↜ لیست اسامی فیلتر شده :\n'
for k,v in pairs(list) do 
t = t..k.."- *"..v.."*\n"
end
if #list == 0 then
t = '|↜ لیست اسامی فیلتر شده خالے است'
end
send(cht,msg.id,t,'md')
end
if gp_type(msg.chat_id) == "pv" then
local t = '|↜ لیست اسامی فیلتر شده در *'..tgps..'* گروه شما\nبراے دیدن لیست گروه ها میتوانید از دستور گروه هاے من یا [mygps] استفاده کنید.\n'
for k,v in pairs(gps) do
local list = base:smembers(TD_ID..'FilterName:'..v)
for a,b in pairs(list) do
t = ""..t..""..b.."\nدر گروه *"..k.."*\n﹃﹄﹃﹄﹃﹄\n"
end
end
send(cht,msg.id,t,'md')
end
end
--- filter Bio
if Black and (Black:match('^filterbio +(.*)$') or Black:match('^فیلتر بیو +(.*)$')) and is_JoinChannel(msg) then
if string.find(Black:match('^filterbio (.*)$') or Black:match('^فیلتر بیو (.*)$'),"[%*?^$]") then
send(cht,msg.id,'🖕😐','md')
else
local word = Black:match('^filterbio +(.*)$') or Black:match('^فیلتر بیو +(.*)$')
typegpadd('FilterBio:',word)
send(cht,msg.id,'|↜ کلمه '..word..'\nبه لیست بیوگرافی های غیرمجاز اضافه شد !','md')
end
end
if Black and (Black:match('^remfilterbio +(.*)$') or Black:match('^حذف فیلتر بیو +(.*)$')) and is_JoinChannel(msg) then
local word = Black:match('^remfilterbio +(.*)$') or Black:match('^حذف فیلتر بیو +(.*)$')
typegprem('FilterBio:',word)
send(cht,msg.id,'|↜ کلمه '..word..'\nبه کلمات غیرمجاز در بیوگرافی اضافه شد !','md')
end
if (Black == 'clean filterbiolist' or Black == 'پاکسازی لیست بیوهای غیرمجاز') and is_JoinChannel(msg) then
typegpdel('FilterBio:')
send(cht,msg.id,'|↜ لیست بیوهای غیرمجاز پاکسازی شد !','md')
end
if (Black == 'filterbiolist' or Black == 'لیست بیوهای غیرمجاز') and is_JoinChannel(msg) then
if is_supergroup(msg) then
local list = base:smembers(TD_ID..'FilterBio:'..cht)
local t = '|↜ لیست بیوهای غیرمجاز :\n'
for k,v in pairs(list) do 
t = t..k.."- *"..v.."*\n"
end
if #list == 0 then
t = '|↜ لیست بیوهای غیرمجاز خالی میباشد !'
end
send(cht,msg.id,t,'md')
end
if gp_type(msg.chat_id) == "pv" then
local t = '|↜ لیست بیوهای غیرمجاز در *'..tgps..'* گروه شما\nبراے دیدن لیست گروه ها میتوانید از دستور گروه هاے من یا [mygps] استفاده کنید.\n'
for k,v in pairs(gps) do
local list = base:smembers(TD_ID..'FilterBio:'..v)
for a,b in pairs(list) do
t = ""..t..""..b.."\nدر گروه *"..k.."*\n﹃﹄﹃﹄﹃﹄\n"
end
end
send(cht,msg.id,t,'md')
end
end

if Black then
TDDelMatch = Black:match('^lock (.*)$') or Black:match('^قفل (.*)$')
TDBanMatch = Black:match('^ban (.*)$') or Black:match('^مسدود (.*)$') 
TDMuteMatch = Black:match('^mute (.*)$') or Black:match('^محدود (.*)$')
TDSilentMatch = Black:match('^silent (.*)$') or Black:match('^سایلنت (.*)$')
TDWarnMatch = Black:match('^warn (.*)$') or Black:match('^اخطار (.*)$')
TDUnlockMatch = Black:match('^unlock (.*)$') or Black:match('^بازکردن (.*)$')
TDMatches = TDDelMatch or TDBanMatch or TDMuteMatch or TDSilentMatch or TDWarnMatch or TDDdelMatch or TDDbanMatch or TDDkickMatch or TDDmuteMatch or TDDsilentMatch or TDDwarnMatch or TDUnlockMatch
if TDMatches then
local returntd1 = TDMatches:match('^photo$') or TDMatches:match('^game$') or TDMatches:match('^video$')or TDMatches:match('^flood$') or TDMatches:match('^inline$') or TDMatches:match('^selfi$') or TDMatches:match('^caption$') or TDMatches:match('^voice$') or TDMatches:match('^location$') or TDMatches:match('^document$') or TDMatches:match('^contact$') or TDMatches:match('^text$') or TDMatches:match('^sticker$') or TDMatches:match('^stickers$') or TDMatches:match('^gif$') or TDMatches:match('^music$') or TDMatches:match('^عکس$') or TDMatches:match('^بازی$') or TDMatches:match('^فیلم$') or TDMatches:match('^دکمه شیشه ای$') or TDMatches:match('^سلفی$') or TDMatches:match('^کپشن$') or TDMatches:match('^موقعیت مکانی$') or TDMatches:match('^وویس$') or TDMatches:match('^فایل$') or TDMatches:match('^مخاطب$') or TDMatches:match('^متن$') or TDMatches:match('^استیکر$') or TDMatches:match('^استیکر متحرک$') or TDMatches:match('^گیف$') or TDMatches:match('^اهنگ$') or TDMatches:match('^آهنگ$') or TDMatches:match('^spam$') or TDMatches:match('^هرزنامه$')or TDMatches:match('^رگبار$') or TDMatches:match('^پست کانال$') or TDMatches:match('^channelpost$') or TDMatches:match('^نظرسنجی$') or TDMatches:match('^vote$')
local returntd2 = TDMatches:match('^fosh$') or TDMatches:match('^link$') or TDMatches:match('^hashtag$') or TDMatches:match('^username$') or TDMatches:match('^english$') or TDMatches:match('^persian$') or TDMatches:match('^hyper$') or TDMatches:match('^mention$') or TDMatches:match('^هایپر$') or TDMatches:match('^فراخانی$') or TDMatches:match('^هشتگ$') or TDMatches:match('^یوزرنیم$') or TDMatches:match('^لینک$') or TDMatches:match('^فارسی$') or TDMatches:match('^انگلیسی$') or TDMatches:match('^فحش$')
local returntdf = TDMatches:match('^forward$') or TDMatches:match('^fwd$') or TDMatches:match('^فوروارد$')
local returntdb = TDMatches:match('^bots$') or TDMatches:match('^ربات$')
local returntde = TDMatches:match('^edit$') or TDMatches:match('^ویرایش$')
local returnbio = TDMatches:match('^لینک بیو$') or TDMatches:match('^biolink$')
local returntrue = returntd1 or returntd2 or returntdf or returntde or returntdb or returnbio
local function tdlock(BD)
if BD:match('^photo$') or BD:match('^عکس$') then
td = 'Photo'
tde = 'ρнσтσ'
tdf = 'عکس'
elseif BD:match('^game$') or BD:match('^بازی$') then
td = 'Game'
tde = 'gαмε'
tdf = 'بازی'
elseif BD:match('^video$') or BD:match('^فیلم$') then
td = 'Video'
tde = 'vιdεσ'
tdf = 'فیلم'
elseif BD:match('^inline$') or BD:match('^دکمه شیشه ای$') then
td = 'Inline'
tde = 'ιηℓιηε'
tdf = 'دکمه شیشه ای'
elseif BD:match('^selfi$') or BD:match('^سلفی$') then
td = 'Selfi'
tde = 'vιdεσмsg'
tdf = 'سلفی'
elseif BD:match('^caption$') or BD:match('^کپشن$') then
td = 'Caption'
tde = 'cαρтιση'
tdf = 'کپشن'
elseif BD:match('^voice$') or BD:match('^وویس$') then
td = 'Voice'
tde = 'vσιcε'
tdf = 'وویس'
elseif BD:match('^location$') or BD:match('^موقعیت مکانی$') then
td = 'Location'
tde = 'ℓσcαтιση'
tdf = 'موقعیت مکانی'
elseif BD:match('^document$') or BD:match('^فایل$') then
td = 'Document'
tde = '∂σcυмεηт'
tdf = 'فایل'
elseif BD:match('^contact$') or BD:match('^مخاطب$') then
td = 'Contact'
tde = 'cσηтαcт'
tdf = 'مخاطب'
elseif BD:match('^text$') or BD:match('^متن$') then
td = 'Text'
tde = 'тεxт'
tdf = 'متن'
elseif BD:match('^sticker$') or BD:match('^استیکر$') then
td = 'Sticker'
tde = 'sтιcкεя'
tdf = 'استیکر'
elseif BD:match('^stickers$') or BD:match('^استیکر متحرک$') then
td = 'Stickers'
tde = 'sтιcкεяs'
tdf = 'استیکر متحرک'
elseif BD:match('^gif$') or BD:match('^گیف$') then
td = 'Gif'
tde = 'gιғ'
tdf = 'گیف'
elseif BD:match('^audio$') or BD:match('^آهنگ$') or BD:match('^اهنگ$') then
td = 'Audio'
tde = 'мυsιc'
tdf = 'آهنگ'
elseif BD:match('^flood$') or BD:match('^رگبار$') then
td = 'Flood'
tde = 'ғlood'
tdf = 'پیام مکرر'
elseif BD:match('^spam$') or BD:match('^هرزنامه$')then
td = 'Spam'
tde = 'ѕpaм'
tdf = 'هرزنامه'
elseif BD:match('^link$') or BD:match('^لینک$') then
td = 'Link'
tde = 'ℓιηк'
tdf = 'لینک'
elseif BD:match('^fosh$') or BD:match('^فحش$') then
td = 'Fosh'
tde = 'Fosh'
tdf = 'فحش'
elseif BD:match('^hashtag$') or BD:match('^هشتگ$') then
td = 'Hashtag'
tde = 'тαg'
tdf = 'هشتگ'
elseif BD:match('^username$') or BD:match('^یوزرنیم$') then
td = 'Username'
tde = 'υsεяηαмε'
tdf = 'یوزرنیم'
elseif BD:match('^persian$') or BD:match('^فارسی$') then
td = 'Persian'
tde = 'ρεяsιση'
tdf = 'فارسی'
elseif BD:match('^english$') or BD:match('^انگلیسی$') then
td = 'English'
tde = 'εηgℓιsн'
tdf = 'انگلیسی'
elseif BD:match('^edit$') or BD:match('^ویرایش$') then
td = 'Edit'
tde = 'ε∂ιт'
tdf = 'ویرایش'
elseif BD:match('^forward$') or BD:match('^fwd$') or BD:match('^فوروارد$') then
td = 'Forward'
tde = 'ғσяωαя∂'
tdf = 'فوروارد'
elseif BD:match('^bots$') or BD:match('^ربات$') then
td = 'Bots'
tde = 'вσт'
tdf = 'ربات'
elseif BD:match('^hyper$') or BD:match('^هایپر$') then
td = 'Hyper'
tde = 'нypυrlιɴĸ'
tdf = 'هایپرلینک'
elseif BD:match('^mention$') or BD:match('^فراخانی$') then
td = 'Mention'
tde = 'мυηтιση'
tdf = 'فراخانی'
elseif BD:match('^channelpost$') or BD:match('^پست کانال$') then
td = 'Channelpost'
tde = 'cнαɴɴelpoѕт'
tdf = 'پست کانال'
elseif BD:match('^vote$') or BD:match('^نظرسنجی$') then
td = 'Vote'
tde = 'vote'
tdf = 'نظرسنجی'
elseif BD:match('^biolink$') or BD:match('^لینک بیو$') then
td = 'Biolink'
tde = 'вιolιɴĸ'
tdf = 'لینک بیو'
end
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
function locks_del(msg,en,fa)
local diamond = TD.getUser(bd)
if ec_name(diamond.first_name) == '' then 
name = bd
else 
name = ec_name(diamond.first_name) 
end
nametd = '【['..name..'](tg://user?id='..bd..')】'
if tdf == 'لینک بیو' then
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ حذف لینک بیو #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورتی که کاربر در بیوی خود لینک داشته باشد پیامهای وی حذف خواهد شد.','md')
else
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ حذف '..tdf..' #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورت '..fa..' #'..tdf..',پیام مورد نظر حذف خواهد شد\n─┅━━━━━━━┅─\n'..check_markdown(Channel),'md')
end
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
function locks_warn(msg,en,fa)
local max_warn = tonumber(base:get(TD_ID..'max_warn:'..cht) or 5)
local diamond = TD.getUser(bd)
if ec_name(diamond.first_name) == '' then 
name = bd
else 
name = ec_name(diamond.first_name)
end
if gp_type(cht) == "channel" then
nametd = '【['..name..'](tg://user?id='..bd..')】'
else
nametd = '【'..name..'】'
end
if tdf == 'لینک بیو' then
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ اخطار لینک بیو #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورتی که کاربر در بیوی خود لینک داشته باشد از گروه اخراج خواهد شد.','md')
else
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ اخطار '..tdf..' #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورت '..fa..' #'..tdf..',کاربر '..fa..' کننده اخطار دریافت میکند.\n─┅━━━━━━━┅─\n'..check_markdown(Channel),'md')
end
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
function locks_Babak(msg,en,fa,bden,bdfa)
local diamond = TD.getUser(bd)
if ec_name(diamond.first_name) == '' then 
name = bd
else 
name = ec_name(diamond.first_name) 
end
if gp_type(cht) == "channel" then
nametd = '【['..name..'](tg://user?id='..bd..')】'
else
nametd = '【'..name..'】'
end
if tdf == 'لینک بیو' then
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ '..fa..' '..tdf..' #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورتی که کاربر در بیوی خود لینک داشته باشد کاربر '..fa..' میشود','md')
else
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ '..fa..' '..tdf..' #فعال شد...\nا┅┅──┄┄═✺═┄┄──┅┅\nدر صورت '..bdfa..' #'..tdf..', کاربر '..bdfa..' کننده #'..fa..' میشود','md')
end
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
function unlocks(msg,en,fa)
local diamond = TD.getUser(bd)
if ec_name(diamond.first_name) == '' then 
name = bd
else 
name = ec_name(diamond.first_name) 
end
if gp_type(cht) == "channel" then
nametd = '【['..name..'](tg://user?id='..bd..')】'
else
nametd = '【'..name..'】'
end
if tdf == 'لینک بیو' then
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ '..fa..' '..tdf..' #ازاد شد...!\nا┅┅──┄┄═✺═┄┄──┅┅\nتمامی کاربران مجاز به گذاشتن لینک در بیوی خود شدند.','md')
else
send(cht,msg.id,'✅|↜انجام شد\n✦ توسط :'..nametd..'\n🔏|↜ '..fa..' '..tdf..' #ازاد شد...!\nا┅┅──┄┄═✺═┄┄──┅┅\nکاربران میتوانند در گروه #'..tdf..' '..fa..' کنند..\n─┅━━━━━━━┅─\n'..check_markdown(Channel),'md')
end
end
--<><>--<><>--<><>-<><>-<><>--<><>--<><>--<>--
if Black and (TDDelMatch) and is_JoinChannel(msg) then
tdlock(TDDelMatch)
if returntrue then
if tonumber(reply_id) == 0 then
if base:get(TD_ID..'Lock:'..td..bdcht) == 'del' then
send(msg.chat_id,msg.id,'✔️|↜ حذف '..tdf..' از قبل #فعال بود...!','md')
else
base:set(TD_ID..'Lock:'..td..bdcht,'del')
if returntd1 then
locks_del(msg,'ѕenт','ارسال')
end
if returntd2 then
locks_del(msg,'ѕenт','ارسال')
end
if returntdf then
locks_del(msg,'ѕenт','فرایند')
end
if returntde then
locks_del(msg,'υѕer','ویرایش پیام,پیام')
end
if returntdb then
locks_del(msg,'added','دعوت')
end
if returnbio then
locks_del(msg,'υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end 
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\n🔏|↜ حذف '..tdf..' را #فعال کرد...!\nجهت #غیرفعال کردن میتوانید از دستور (حذف '..tdf..' غیرفعال)  یا (ddel '..td..') استفاده کنید\n'..reporttext)
end
else
local Diamond = TD.getMessage(cht,tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ["..name.."](tg://user?id="..user..") دارای مقام میباشد شما نمیتوانید او را از ارسال "..tdf.." محدود کنید",'md')
else
if base:sismember(TD_ID..'Gp3:'..chat_id,user..' حذف '..tdf) then
send(msg.chat_id,msg.id,"✦ کاربر : ["..name.."](tg://user?id="..user..")\nاز قبل در لیست محدودی های ارسال "..tdf.." وجود داشت...!",'md')
else
base:sadd(TD_ID..'Gp3:'..chat_id,user..' حذف '..tdf)
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nاز ارسال '..tdf..' در گروه ممنوع شد و هم اکنون در صورت قفل نبودن '..tdf..' نیز '..tdf..' های ارسالی وی حذف خواهند شد...!','md')
end
end
end
end
end
end
----------------Warn----------------
if Black and (TDWarnMatch) and is_JoinChannel(msg) then
tdlock(TDWarnMatch)
if returntrue then
if base:get(TD_ID..'Lock:'..td..bdcht) == 'warn' then
send(cht,msg.id,'✔️ اخطار #'..tdf..' از قبل #فعال بود...!','md')
else
base:set(TD_ID..'Lock:'..td..bdcht,'warn')
if returntd1 then
locks_warn(msg,'ѕenт','ارسال')
end
if returntd2 then
locks_warn(msg,'ѕenт','ارسال')
end
if returntdf then
locks_warn(msg,'ѕenт','ارسال')
end
if returntde then
locks_warn(msg,'υѕer','فرایند')
end
if returntdb then
locks_warn(msg,'added','دعوت')
end
if returnbio then
locks_warn(msg,'υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\n🔏|↜ اخطار '..tdf..' را #فعال کرد...!\nجهت غیرفعال کردن میتوانید از دستور (اخطار '..tdf..' غیرفعال)  یا (dwarn '..td..') استفاده کنید\n'..reporttext)
end
end
end
----------------Mute----------------
if Black and (TDMuteMatch) and is_JoinChannel(msg) then
tdlock(TDMuteMatch)
if returntrue then
if base:get(TD_ID..'Lock:'..td..bdcht) == 'mute' then
send(cht,msg.id,'✔️|↜ سکوت #'..tdf..' از قبل #فعال بود...!','md')
else
base:set(TD_ID..'Lock:'..td..bdcht,'mute')
if returntd1 then
locks_Babak(msg,'мυтε','سکوت','ѕenт','ارسال')
end
if returntd2 then
locks_Babak(msg,'мυтε','سکوت','ѕenт','ارسال')
end
if returntdf then
locks_Babak(msg,'мυтε','سکوت','ѕenт','ارسال')
end
if returntde then
locks_Babak(msg,'мυтε','سکوت','υѕer','فرایند')
end
if returntdb then
locks_Babak(msg,'мυтε','سکوت','added','دعوت')
end
if returnbio then
locks_Babak(msg,'мυтε','سکوت','υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\n🔏|↜ سکوت '..tdf..' را #فعال کرد...!\nجهت غیرفعال کردن میتوانید از دستور (سکوت '..tdf..' غیرفعال)  یا (dmute '..td..') استفاده کنید\n'..reporttext)
end
end
end
----------------Ban----------------
if Black and (TDBanMatch) and is_JoinChannel(msg) then
tdlock(TDBanMatch)
if returntrue then
if base:get(TD_ID..'Lock:'..td..bdcht) == 'ban' then
send(cht,msg.id,'✔️|↜ مسدود #'..tdf..' از قبل #فعال بود...!','md')
else
base:set(TD_ID..'Lock:'..td..bdcht,'ban')
if returntd1 then
locks_Babak(msg,'вαη','مسدود','ѕenт','ارسال')
end
if returntd2 then
locks_Babak(msg,'вαη','مسدود','ѕenт','ارسال')
end
if returntdf then
locks_Babak(msg,'вαη','مسدود','ѕenт','ارسال')
end
if returntde then
locks_Babak(msg,'вαη','مسدود','υѕer','فرایند')
end
if returntdb then
locks_Babak(msg,'вαη','مسدود','added','دعوت')
end
if returnbio then
locks_Babak(msg,'вαη','مسدود','υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end 
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\n🔏|↜ مسدود '..tdf..' را #فعال کرد...!\nجهت غیرفعال کردن میتوانید از دستور (مسدود '..tdf..' غیرفعال)  یا (dban '..td..') استفاده کنید\n'..reporttext)
end
end
end
----------------Silent----------------
if Black and (TDSilentMatch) and is_JoinChannel(msg) then
tdlock(TDSilentMatch)
if returntrue then
if base:get(TD_ID..'Lock:'..td..bdcht) == 'silent' then
send(cht,msg.id,'✔️|↜ سایلنت #'..tdf..' از قبل #فعال بود...!','md')
else
base:set(TD_ID..'Lock:'..td..bdcht,'silent')
if returntd1 then
locks_Babak(msg,'sιℓεηт','سایلنت','ѕenт','ارسال')
end
if returntd2 then
locks_Babak(msg,'sιℓεηт','سایلنت','ѕenт','ارسال')
end
if returntdf then
locks_Babak(msg,'sιℓεηт','سایلنت','ѕenт','ارسال')
end
if returntde then
locks_Babak(msg,'sιℓεηт','سایلنت','υѕer','فرایند')
end
if returntdb then
locks_Babak(msg,'sιℓεηт','سایلنت','added','دعوت')
end
if returnbio then
locks_Babak(msg,'sιℓεηт','سایلنت','υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end 
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\n🔏|↜ سایلنت '..tdf..' را #فعال کرد...!\nجهت غیرفعال کردن میتوانید از دستور (سایلنت '..tdf..' غیرفعال)  یا (dsilent '..td..') استفاده کنید\n'..reporttext)
end
end
end
----------------Unlock----------------
if Black and (TDUnlockMatch) and is_JoinChannel(msg) then
tdlock(TDUnlockMatch)
if returntrue then
if base:get(TD_ID..'Lock:'..td..cht) == 'del' or base:get(TD_ID..'Lock:'..td..cht) == 'warn' or base:get(TD_ID..'Lock:'..td..cht) == 'mute' or base:get(TD_ID..'Lock:'..td..cht) == 'silent' or base:get(TD_ID..'Lock:'..td..cht) == 'ban'  then 
if is_supergroup(msg) then
base:del(TD_ID..'Lock:'..td..cht)
end
if gp_type(msg.chat_id) == "pv" then
for k,v in pairs(gps) do
base:del(TD_ID..'Lock:'..td..v)
end
end
if returntd1 then
unlocks(msg,'ѕenт','ارسال')
end
if returntd2 then
unlocks(msg,'ѕenт','ارسال')
end
if returntdf then
unlocks(msg,'ѕenт','فرایند')
end
if returntde then
unlocks(msg,'υѕer','ویرایش')
end
if returntdb then
unlocks(msg,'added','دعوت')
end
if returnbio then
unlocks(msg,'υѕυя нαυe','داشتن')
end
local diamond = TD.getUser(bd)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
reportowner('✅|↜ کاربر :【['..name..'](tg://user?id='..bd..')】\nا┅┅──┄┄═✺═┄┄──┅┅\n🔏|↜ قفل #'..tdf..' را #ازاد کرد! ...',12,name)
else
send(cht,msg.id,'✔️|↜ قفل '..tdf..' از قبل #ازاد بود...!','md')
end
end
end
end
--------------------join-----------------
if (Black == 'lock join' or Black == 'قفل ورود') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'Join') then
send(cht,msg.id,'✔️|↜قفل ورود اعضا فعال است...!','md')
else
typegpadd('Gp:','Join')
send(cht,msg.id,'✅|↜قفل ورود اعضا فعال شد   ...!\nا┅┅──┄┄═✺═┄┄──┅┅\nهم اکنون کاربران قادر به عضویت در گروه نمیباشند و در صورت عضویت از گروه ریمو خواهند شد!','md')
end
end
if (Black == 'unlock join' or Black == 'بازکردن ورود') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'Join') then
typegprem('Gp:','Join')
send(cht,msg.id,'✅|↜قفل ورود اعضا غیرفعال شد   ...!\nا┅┅──┄┄═✺═┄┄──┅┅\nعضویت کاربران با لینک ازاد شد و کاربران میتوانند با لینک وارد گروه شوند!','md')
else
send(cht,msg.id,'✔️|↜قفل ورود اعضا غیرفعال است...!','md')
end
end
--------------------cmds-----------------
if (Black == 'lock cmd' or Black == 'قفل دستورات') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'Cmd') then
send(cht,msg.id,'✔️|↜قفل دستورات فعال است...!','md')
else
typegpadd('Gp:','Cmd')
send(cht,msg.id,'✅|↜قفل دستورات فعال شد...!','md')
end
end
if (Black == 'unlock cmd' or Black == 'بازکردن دستورات') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'Cmd') then
typegprem('Gp:','Cmd')
send(cht,msg.id,'✅|↜قفل دستورات غیرفعال شد...!','md')
else
send(cht,msg.id,'✔️|↜قفل دستورات غیرفعال است...!','md')
end
end
--------------------tgservice-----------------
if (Black == 'lock tgservice' or Black == 'قفل خدمات') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'TGservice') then
send(cht,msg.id,'✔️|↜قفل خدمات تلگرام #فعال بود...!','md')
else
typegpadd('Gp:','TGservice')
send(cht,msg.id,'✅|↜قفل خدمات تلگرام فعال شد\n┅┅──┄┄═✺═┄┄──┅┅\nاز این پس پیام هاے عضویت کاربران و اد ممبر پاک خواهند شد!','md')
end
end
if (Black == 'unlock tgservice' or Black == 'بازکردن خدمات') and is_JoinChannel(msg) then 
if base:sismember(TD_ID..'Gp:'..bdcht,'TGservice') then
typegprem('Gp:','TGservice')
send(cht,msg.id,'قفل خدمات تلگرام غیرفعال شد\nا┅┅──┄┄═✺═┄┄──┅┅\nهم اکنون پیام هاے عضویت ممبر ها و اد شدن ممبر ها قابل مشاهده است!','md')
else
send(cht,msg.id,'✔قفل خدمات تلگرام غیرفعال بود...!','md')
end
end
------set cmd------
if Black and (Black:match('^addcmd (.*)') or Black:match('^افزودن دستور (.*)')) and tonumber(reply_id) > 0 then
local cmd = Black:match('^addcmd (.*)') or Black:match('^افزودن دستور (.*)')
local Diamond = TD.getMessage(cht,tonumber(reply_id))
if Diamond.content._ == 'messageText' then
typegpadd('CmDlist:',Diamond.content.text.text)
typegphset('CmD:',Diamond.content.text.text,cmd)
send(cht,msg.id,'✔️ انجام شد\nاز این پس دستور >'..cmd..'< را میتوانید با >'..Diamond.content.text.text..'< نیز انجام دهید !','md')
end
end
if Black and (Black:match('^delcmd (.*)') or Black:match('^حذف دستور (.*)')) then
local cmd = Black:match('^delcmd (.*)') or Black:match('^حذف دستور (.*)')
typegphdel('CmD:',cmd)
typegprem('CmDlist:',cmd)
send(cht,msg.id,'✔️ انجام شد\nدستور >'..cmd..'< از لیست دستورات حذف شد...!','md')
end
if (Black == 'clean cmdlist' or Black == 'پاکسازی لیست دستورات') then
typegpdel('CmD:')
typegpdel('CmDlist:')
send(cht,msg.id,'لیست دستورات پاکسازے شد','md')
end
if (Black == 'cmdlist' or Black == 'لیست دستورات') then
local CmDlist = base:smembers(TD_ID..'CmDlist:'..bdcht)
local t = 'لیست دستورات جدید ربات : \n'
for k,v in pairs(CmDlist) do
if is_supergroup(msg) then
mmdi = base:hget(TD_ID..'CmD:'..cht,v)
end
if gp_type(msg.chat_id) == "pv" then
for r,y in pairs(gps) do
mmdi = base:hget(TD_ID..'CmD:'..y,v)
end
end
t = t..k..") "..v.." > "..mmdi.."\n" 
end
if #CmDlist == 0 then
t = 'دستور ثبت شده ای یافت نشد !'
end
send(cht, msg.id,t,'md')
end
------text chats------
if Black and (Black:match('^setchat (.*)') or Black:match('^تنظیم چت (.*)')) and tonumber(reply_id) > 0 then
local cmd = Black:match('^setchat (.*)') or Black:match('^تنظیم چت (.*)')
local Diamond = TD.getMessage(cht,tonumber(reply_id))
if Diamond.content._ == 'messageText' then
typegpadd('Textlist:',cmd)
typegphset('Text:',cmd,Diamond.content.text.text)
send(cht,msg.id,'✔️ انجام شد\n>'..Diamond.content.text.text..'\nتنظیم شد در جواب : '..cmd,'md')
end
end
if Black == 'chatlist' or Black == 'لیست چت' then
local Textlist = base:smembers(TD_ID..'Textlist:'..bdcht)
local t = 'لیست چت : \n'
for k,v in pairs(Textlist) do
if is_supergroup(msg) then
mmdi = base:hget(TD_ID..'Text:'..cht,v)
end
if gp_type(msg.chat_id) == "pv" then
for r,y in pairs(gps) do
mmdi = base:hget(TD_ID..'Text:'..y,v)
end
end
t = t..k..") "..v.." > "..mmdi.."\n" 
end
if #Textlist == 0 then
t = 'لیست چت خالی است !'
end
send(cht, msg.id,t,'md')
end
if Black and (Black:match('^delchat (.*)') or Black:match('^حذف چت (.*)')) then
local cmd = Black:match('^delchat (.*)') or Black:match('^حذف چت (.*)')
typegphdel('Text:',cmd)
typegprem('Textlist:',cmd)
send(cht,msg.id,'✔️ انجام شد\n>'..cmd..'\nاز لیست کلماتی که ربات به ان پاسخ میدهد حذف شد...!','md')
end
if Black == 'clean chatlist' or Black == 'پاکسازی لیست چت' then
typegpdel('Textlist:')
typegpdel('Text:')
send(cht,msg.id,'لیست چت پاکسازے شد','md')
end
-----stciker chat-----
if Black and (Black:match('^setsticker (.*)')) and tonumber(reply_id) > 0 then
local cmd = Black:match('^setsticker (.*)')
local Diamond = TD.getMessage(cht,tonumber(reply_id))
if Diamond.content._ == 'messageSticker' then
typegpadd('Stickerslist:',cmd)
typegpset('Stickers:'..cmd,Diamond.content.sticker.sticker.id)
send(cht,msg.id,'✔️ انجام شد\n>استیکر : '..cmd..'\nذخیره شد!','md')
end
end
if Black == 'stickerlist' then
local Stickerslist = base:smembers(TD_ID..'Stickerslist:'..bdcht)
local t = 'Stickers: \n'
for k,v in pairs(Stickerslist) do
t = t..k.." - "..v.."\n" 
end
if #Stickerslist == 0 then
t = 'لیست استیکر ها خالی است'
end
send(cht, msg.id,t,'md')
end
if Black and (Black:match('^delsticker (.*)'))  then
local cmd = Black:match('^delsticker (.*)')
typegprem('Stickerslist:',cmd)
typegpdel('Stickers:'..cmd)
send(cht,msg.id,'✔️ انجام شد\n>استیکر : '..cmd..'\nاز لیست پاک شد!','md')
end
if Black == 'clean stickerlist' or Black == 'پاکسازی لیست استیکر' then
typegpdel('Stickerslist:')
send(cht,msg.id,'لیست استیکر پاکسازے شد','md')
end
if Black == 'ban filter' and is_JoinChannel(msg) then 
base:sadd(TD_ID..'Gp:'..cht,'Ban:Filter')
send(cht, msg.id, '✔️|↜on!','md')
end
if Black == 'dban filter' and is_JoinChannel(msg) then 
base:srem(TD_ID..'Gp:'..cht,'Ban:Filter')
send(cht, msg.id, '✔️|↜off!','md')
end
if Black == 'botchat on' or Black == 'چت ربات روشن' and is_JoinChannel(msg) then 
typegprem('Gp2:','BotChat')
send(cht,msg.id,'✔️|↜ چت ربات فعال شد...!\n\nشما میتوانید با دستور\nsetchat (text)\nبا ریپلی برروے جواب آن چت,ربات را سخن گوکنید\n\nبراے مثال setchat khobi\nرا با ریپلی بر روے پیام mrc وارد میکنیم از این پس ربات به khobi جواب mrc خواهد داد!','md')
end
if Black == 'botchat off' or Black == 'چت ربات خاموش' and is_JoinChannel(msg) then 
typegpadd('Gp2:','BotChat')
send(cht,msg.id,'✔️|↜ چت ربات غیرفعال شد...!','md')
end

if Black and (Black:match('^rmsgs (%d+)$')) then
local RMSG = Black:match('^rmsgs (%d+)$')
if tonumber(RMSG) < 1 or tonumber(RMSG) > 50000 then
send(msg.chat_id,msg.id,'• لطفا عددی بزرگتر از *1* و کوچکتر از *50000* وارد کنید','md')
else
for i = 1,tonumber(RMSG) do
TD.deleteMessages(msg.chat_id,{[1] = math.modf(msg.id - (i * 1048576))})
end
local name = '<a href="tg://user?id='..msg.sender_id.user_id..'">'..TD.getUser(msg.sender_id.user_id).first_name..'</a>'
if is_FullSudo(msg) then
rank = 'فول سودو'
elseif is_Sudo(msg)then
rank =  'سودو ربات' 
elseif is_Owner(msg)then
rank =  'سازنده گروه' 
elseif is_Mod(msg)then
rank =  'مدیر گروه'
end
send(msg.chat_id,msg.id,'• ❂│ تعداد ~⪼ <b>'..tonumber(RMSG)..'</b> پیام با موفقیت حذف شد\n➣ نام دستور دهنده : '..name..'\n➣ مقام دستور دهنده : '..rank..'','html')
end
end

end
end
-----End Pv Cmds-----
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') and is_supergroup(msg) then
if is_Owner(msg) then
if Diamondent and (Black:match('^setowner (.*)') or Black:match('^مالک (.*)') or Black:match('^تنظیم مالک (.*)') or Black:match('^ارتقا مالک (.*)') or Black:match('^ترفیع مالک (.*)')) or Black and (Black:match('^setowner @(.*)') or Black:match('^مالک @(.*)') or Black:match('^تنظیم مالک @(.*)') or Black:match('^ارتقا مالک @(.*)') or Black:match('^ترفیع مالک @(.*)') or Black:match('^setowner (%d+)$') or Black:match('^مالک (%d+)$') or Black:match('^تنظیم مالک (%d+)$') or Black:match('^ارتقا مالک (%d+)$') or Black:match('^ترفیع مالک (%d+)$')) then
local BDSource = Black:match('^setowner (.*)') or Black:match('^مالک (.*)') or Black:match('^تنظیم مالک (.*)') or Black:match('^ارتقا مالک (.*)') or Black:match('^ترفیع مالک (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^setowner @(.*)') or Black:match('^مالک @(.*)') or Black:match('^تنظیم مالک @(.*)') or Black:match('^ارتقا مالک @(.*)') or Black:match('^ترفیع مالک @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^setowner (%d+)$') or Black:match('^مالک (%d+)$') or Black:match('^تنظیم مالک (%d+)$') or Black:match('^ارتقا مالک (%d+)$') or Black:match('^ترفیع مالک (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^setowner (.*)') or Black:match('^مالک (.*)') or Black:match('^تنظیم مالک (.*)') or Black:match('^ارتقا مالک (.*)') or Black:match('^ترفیع مالک (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'OwnerList:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز قبل در لیست مالکان ربات در گروه قرار داشت','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nبه لیست مالکان ربات در گروه افزوده شد','md')
base:sadd(TD_ID..'OwnerList:'..msg.chat_id,mrr619)
end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'md')
end
end
if (Black == 'setowner' or Black == 'تنظیم مالک' or Black == 'ارتقا مالک' or Black == 'ترفیع مالک' or Black == 'مالک') and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id,tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nاز قبل در لیست مالکان ربات در گروه قرار داشت','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nبه لیست مالکان ربات در گروه افزوده شد','md')
base:sadd(TD_ID..'OwnerList:'..msg.chat_id,user)
end
end
end

if Black and (Black:match('^setowner (%d+) (%S+)$') or Black:match('^تنظیم مالک (%d+) (%S+)$') or Black:match('^ارتقا مالک (%d+) (%S+)$') or Black:match('^ترفیع مالک (%d+) (%S+)$') or Black:match('^مالک (%d+) (%S+)$')) and tonumber(reply_id) > 0 then 
       local matches = {string.match(Black, '(%d+) (%S+)$')} 
       local result = TD.getMessage(msg.chat_id, tonumber(reply_id)) 
       if result._ == 'error' then 
        TD.sendText(msg.chat_id, msg.id,' متاسفانه ربات های (Api) قادر به چک کردن پیام های یکدیگر نمیباشند !', 'lg') 
       else 
        if matches[2] == 'm' or matches[2] == 'دقیقه' then
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETOWNER', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'دقیقه'}), 'lg') 
        elseif matches[2] == 'h' or matches[2] == 'ساعت' then
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETOWNER', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'ساعت'}), 'lg') 
        elseif matches[2] == 'd' or matches[2] == 'روز' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETOWNER', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'روز'}), 'lg') 
        end 
       end
end

if (Black == 'attack on' or Black == 'خیانت ادمین فعال') then
if base:get(TD_ID..'Lock:Attack'..msg.chat_id) == 'del' then
send(msg.chat_id,msg.id,'• قفل خیانت ادمین ها فعال است !','md') 
else
base:set(TD_ID..'Lock:Attack'..msg.chat_id,'del')
send(msg.chat_id,msg.id,'• قفل خیانت ادمین ها فعال شد !','md') 
end end
if (Black == 'attack off' or Black == 'خیانت ادمین غیرفعال') and is_Owner(msg) and is_JoinChannel(msg) then 
if base:get(TD_ID..'Lock:Attack'..msg.chat_id) == 'del' then
base:del(TD_ID..'Lock:Attack'..msg.chat_id)
send(msg.chat_id,msg.id,'• قفل خیانت ادمین ها غیرفعال شد !','md')
else
send(msg.chat_id,msg.id,'• قفل خیانت ادمین ها غیرفعال است !','md')
end end 
if Black and (Black:match('^setattack (%d+)$') or Black:match('^تنظیم تعداد خیانت ادمین (%d+)$')) then
number = Black:match('^setattack (%d+)$') or Black:match('^تنظیم تعداد خیانت ادمین (%d+)$')
base:set(TD_ID..'count_Kicked:'..msg.chat_id,number) 
send(msg.chat_id,msg.id,'حداکثر تعداد اخراج خیانت ادمین ها تنظیم شد به : '..number..' نفر','md')
end

if Black == 'reset info' and is_Owner(msg) and tonumber(reply_id) == 0 then
base:del(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id) 
base:del(TD_ID..'Total:AddUser:'..msg.chat_id..':'..msg.sender_id.user_id)
base:del(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id)
send(msg.chat_id,msg.id,'#انجام شد\nاطلاعات شما  بازنشانے شد...!','md')
end
if Black == 'reset info' and tonumber(reply_id) ~= 0  and is_Owner(msg) then
local startwarn = TD_ID..':join'..os.date("%Y/%m/%d")..':'..msg.chat_id
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
base:del(TD_ID..'Total:KickUser:'..msg.chat_id..':'..user) 
base:del(TD_ID..'Total:AddUser:'..msg.chat_id..':'..user)
base:del(TD_ID..'Total:BanUser:'..msg.chat_id..':'..user)
base:del(TD_ID..'forceaddfor',user)
base:del(TD_ID..'addeduser'..msg.chat_id,user,added)
base:del(startwarn,user)
local diamond = TD.getUser(user)
send(msg.chat_id,msg.id,'#انجام شد\nاطلاعات کاربر : @'..check_markdown(diamond.usernames.editable_username or '')..'\n'..ec_name(diamond.first_name)..'\n بازنشانی شد#...!','md')
end
end 
if Black == 'modlist' or Black == 'مدیران' then  
local list = base:smembers(TD_ID..'ModList:'..msg.chat_id)
local t = 'لیست مدیران گروه :\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do  
local name = TD.getUser(v).first_name or v
t = t..k..'-【['..name..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = 'لیست مدیران گروه خالے میباشد'
end
send(msg.chat_id,msg.id,t,'md')
end
if Black == 'reportpv on' or Black == 'ارسال گزارش فعال' then
if reportpv then
send(msg.chat_id, msg.id, 'ارسال گزارش به مالک #فعال بود','md')
else
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'reportpv')
send(msg.chat_id, msg.id, 'ارسال گزارش به مالک #فعال شد','md')
end
end
if Black == 'reportpv off' or Black == 'ارسال گزارش غیرفعال' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'reportpv')
send(msg.chat_id, msg.id, 'ارسال گزارش به مالک #غیرفعال شد','md')
end
--<><><><>setadmin
if Diamondent and (Black:match('^admin gp (.*)') or Black:match('^مدیر گروه (.*)')) or Black and (Black:match('^admin gp @(.*)') or Black:match('^مدیر گروه @(.*)') or Black:match('^admin gp (%d+)$') or Black:match('^مدیر گروه (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^admin gp (.*)') or Black:match('^مدیر گروه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^admin gp @(.*)') or Black:match('^مدیر گروه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^admin gp (%d+)') or Black:match('^مدیر گروه (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^admin gp (.*)') or Black:match('^مدیر گروه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_promote_members == true then
SetAdmins(msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nبه مدیر گروه ارتقا داده شد !','md')
base:sadd(TD_ID..'ModList:'..msg.chat_id,mrr619)
else
send(msg.chat_id,msg.id,'✦ ربات دسترسی به قسمت ادمین کردن کاربران ندارد !\nلطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد امتحان کنید !','md')
end
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if (Black == 'admin gp' or Black == 'مدیر گروه') and tonumber(reply_id_) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))  
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl = json:decode(url_)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_promote_members == true then
SetAdmins(msg.chat_id,user)
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nبه مدیر گروه ارتقا داده شد !','md')
base:sadd(TD_ID..'ModList:'..msg.chat_id,user)
else
send(msg.chat_id,msg.id,'✦ ربات دسترسی به قسمت ادمین کردن کاربران ندارد !\nلطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد امتحان کنید !','md')
end
end
end
--<><><><>remadmin
if Diamondent and (Black:match('^rem admin gp (.*)') or Black:match('^حذف مدیر گروه (.*)')) or Black and (Black:match('^rem admin gp @(.*)') or Black:match('^حذف مدیر گروه @(.*)') or Black: match('^rem admin gp (%d+)$') or Black:match('^حذف مدیر گروه (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^rem admin gp (.*)') or Black:match('^حذف مدیر گروه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^rem admin gp @(.*)') or Black:match('^حذف مدیر گروه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^rem admin gp (%d+)') or Black:match('^حذف مدیر گروه (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^rem admin gp (.*)') or Black:match('^حذف مدیر گروه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_promote_members == true then
local url  = https.request(Bot_Api .. '/promoteChatMember?chat_id='..msg.chat_id..'&user_id='..mrr619..'&can_change_info=false')
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز مقام ادمینی گروه خلع مقام شد !','md')
base:srem(TD_ID..'ModList:'..msg.chat_id,mrr619)
else
send(msg.chat_id,msg.id,'✦ انجام نشد !\nربات نمیتواند ادمینی که توسط ادمینی دیگر ارتقا داده شده از لیست ادمین ها خارج کند !','md')
end
else
send(msg.chat_id,msg.id,'✦ ربات دسترسی به قسمت ادمین کردن کاربران ندارد !\nلطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد امتحان کنید !','md')
end
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if (Black == 'rem admin gp' or Black == 'حذف مدیر گروه') and tonumber(reply_id_) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))  
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_promote_members == true then
local url  = https.request(Bot_Api .. '/promoteChatMember?chat_id='..msg.chat_id..'&user_id='..user..'&can_change_info=false')
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true then
--remadmins(msg.chat_id,user)
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nاز مقام ادمینی گروه خلع مقام شد !','md')
base:srem(TD_ID..'ModList:'..msg.chat_id,user)
else
send(msg.chat_id,msg.id,'✦ انجام نشد !\nربات نمیتواند ادمینی که توسط ادمینی دیگر ارتقا داده شده از لیست ادمین ها خارج کند !','md')
end
else
send(msg.chat_id,msg.id,'✦ ربات دسترسی به قسمت ادمین کردن کاربران ندارد !\nلطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد امتحان کنید !','md')
end
end
end

if Black and (Black:match('^limitcmd +(.*)') or Black:match('^محدودکردن دستور +(.*)')) and is_JoinChannel(msg) then
if string.find(Black:match('^limitcmd (.*)$') or Black:match('^محدودکردن دستور (.*)$'),"[%(%)%.%+%-%*%?%[%]%^%$%%]") then
send(msg.chat_id,msg.id,'🖕😐','md')
else
local word = Black:match('^limitcmd +(.*)') or Black:match('^محدودکردن دستور +(.*)')
base:sadd(TD_ID..'LimitCmd:'..msg.chat_id,word)
send(msg.chat_id,msg.id,'|↜ دستور '..word..' از دسترس مدیران گروه خارج شد و فقط مالکان قادر به زدن این دستور خواهند بود !','md')
end
end

if Black and (Black:match('^unlimitcmd +(.*)') or Black:match('^نامحدود کردن دستور +(.*)')) and is_JoinChannel(msg) then
local word = Black:match('^unlimitcmd +(.*)') or Black:match('^نامحدود کردن دستور +(.*)')
base:srem(TD_ID..'LimitCmd:'..msg.chat_id,word)
send(msg.chat_id,msg.id,'|↜ دستور '..word..' در دسترس مدیران گروه قرار گرفت !','md')
end
if (Black == 'clean limitcmdlist' or Black == 'پاکسازی لیست دستورات محدود') and is_JoinChannel(msg) then
base:del(TD_ID..'LimitCmd:'..msg.chat_id)
send(msg.chat_id,msg.id,'|↜ لیست دستورات محدود پاکسازی شد !','md')
end
if (Black == 'limitcmdlist' or Black == 'لیست دستورات محدود') and is_JoinChannel(msg) then
local list = base:smembers(TD_ID..'LimitCmd:'..msg.chat_id)
local t = '|↜ لیست دستوراتی که مدیران قادر به استفاده از انها نیستند :\n'
for k,v in pairs(list) do 
t = t..k.."- *"..v.."*\n"
end
if #list == 0 then
t = '|↜ لیست  دستورات محدود خالی است !'
end
send(msg.chat_id,msg.id,t,'md')
end
--<><><><>Promote
if Diamondent and (Black:match('^promote (.*)') or Black:match('^مدیر (.*)') or Black:match('^تنظیم مدیر (.*)') or Black:match('^ارتقا مدیر (.*)') or Black:match('^ترفیع مدیر (.*)')) or Black and (Black:match('^promote @(.*)') or Black:match('^مدیر @(.*)') or Black:match('^تنظیم مدیر @(.*)') or Black:match('^ارتقا مدیر @(.*)') or Black:match('^ترفیع مدیر @(.*)') or Black:match('^promote (%d+)$') or Black:match('^مدیر (%d+)$') or Black:match('^تنظیم مدیر (%d+)$') or Black:match('^ارتقا مدیر (%d+)$') or Black:match('^ترفیع مدیر (%d+)$')) then
local BDSource = Black:match('^promote (.*)') or Black:match('^مدیر (.*)') or Black:match('^تنظیم مدیر (.*)') or Black:match('^ارتقا مدیر (.*)') or Black:match('^ترفیع مدیر (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^promote @(.*)') or Black:match('^مدیر @(.*)') or Black:match('^تنظیم مدیر @(.*)') or Black:match('^ارتقا مدیر @(.*)') or Black:match('^ترفیع مدیر @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^promote (%d+)$') or Black:match('^مدیر (%d+)$') or Black:match('^تنظیم مدیر (%d+)$') or Black:match('^ارتقا مدیر (%d+)$') or Black:match('^ترفیع مدیر (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^promote (.*)') or Black:match('^مدیر (.*)') or Black:match('^تنظیم مدیر (.*)') or Black:match('^ارتقا مدیر (.*)') or Black:match('^ترفیع مدیر (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'ModList:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز قبل جزء مدیران ربات بود','md')
else
send(msg.chat_id,msg.id,'کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nبه مدیریت ربات ترفیع یافت','md')
base:sadd(TD_ID..'ModList:'..msg.chat_id,mrr619)
end
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if (Black == 'promote' or Black == 'تنظیم مدیر' or Black == 'ترفیع مدیر' or Black == 'ارتقا مدیر' or Black == 'مدیر') and tonumber(reply_id_) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))  
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'ModList:'..msg.chat_id,user) then
send(msg.chat_id,msg.id,'کاربر : ['..name..'](tg://user?id='..user..')\nاز قبل جزء مدیران ربات بود','md')
else
base:sadd(TD_ID..'ModList:'..msg.chat_id,user)
send(msg.chat_id,msg.id,'کاربر : ['..name..'](tg://user?id='..user..')\nبه مدیریت ربات ترفیع یافت','md')
end
end
end

if Black and (Black:match('^promote (%d+) (%S+)$') or Black:match('^تنظیم مدیر (%d+) (%S+)$') or Black:match('^ارتقا مدیر (%d+) (%S+)$') or Black:match('^ترفیع مدیر (%d+) (%S+)$') or Black:match('^مدیر (%d+) (%S+)$')) and (tonumber(reply_id) > 0) and is_JoinChannel(msg) then 
         local matches = {string.match(Black, '(%d+) (%S+)$')} 
         local result = TD.getMessage(msg.chat_id, tonumber(reply_id)) 
         if result._ == 'error' then 
TD.sendText(msg.chat_id, msg.id,'متاسفانه ربات های (Api) قادر به چک کردن پیام های یکدیگر نمیباشند !', 'lg') 
         else 
          if matches[2] == 'm' or matches[2] == 'دقیقه' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETMOD', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'دقیقه'}), 'lg') 
          elseif matches[2] == 'h' or matches[2] == 'ساعت' then
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETMOD', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'ساعت'}), 'lg') 
          elseif matches[2] == 'd' or matches[2] == 'روز' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'SETMOD', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'روز'}), 'lg') 
          end 
         end
end
--<><><><>Demote
if Diamondent and (Black:match('^demote (.*)') or Black:match('^حذف مدیر (.*)') or Black:match('^عزل مدیر (.*)') or Black:match('^خلع مدیر (.*)') or Black:match('^تنزیل مدیر (.*)')) or Black and (Black:match('^demote @(.*)') or Black:match('^حذف مدیر @(.*)') or Black:match('^عزل مدیر @(.*)') or Black:match('^تنزیل مدیر @(.*)') or Black:match('^خلع مدیر @(.*)') or Black:match('^demote (%d+)$') or Black:match('^حذف مدیر (%d+)$') or Black:match('^عزل مدیر (%d+)$') or Black:match('^خلع مدیر (%d+)$') or Black:match('^تنزیل مدیر (%d+)$')) then
local BDSource = Black:match('^demote (.*)') or Black:match('^حذف مدیر (.*)') or Black:match('^عزل مدیر (.*)') or Black:match('^خلع مدیر (.*)') or Black:match('^تنزیل مدیر (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^demote @(.*)') or Black:match('^حذف مدیر @(.*)') or Black:match('^عزل مدیر @(.*)') or Black:match('^خلع مدیر @(.*)') or Black:match('^تنزیل مدیر @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^demote (%d+)$') or Black:match('^حذف مدیر (%d+)$') or Black:match('^عزل مدیر (%d+)$') or Black:match('^خلع مدیر (%d+)$') or Black:match('^تنزیل مدیر (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^demote (.*)') or Black:match('^حذف مدیر (.*)') or Black:match('^عزل مدیر (.*)') or Black:match('^خلع مدیر (.*)') or Black:match('^تنزیل مدیر (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if not base:sismember(TD_ID..'ModList:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز قبل جزء مدیران ربات نبود','md')
else
base:srem(TD_ID..'ModList:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز مقام مدیریت ربات عزل شد','md')
end
else 
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if (Black == 'demote' or Black == 'حذف مدیر' or Black == 'تنزیل مدیر'  or Black == 'عزل مدیر' or Black == 'خلع مدیر') and tonumber(reply_id) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))  
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if not base:sismember(TD_ID..'ModList:'..msg.chat_id,user) then
send(msg.chat_id,msg.id,'کاربر : ['..name..'](tg://user?id='..user..')\nاز قبل جزء مدیران ربات نبود','md')
else
base:srem(TD_ID..'ModList:'..msg.chat_id,user)
send(msg.chat_id,msg.id,'کاربر : ['..name..'](tg://user?id='..user..')\nاز مقام مدیریت ربات عزل شد','md')
end
end
end

if Black == 'clean modlist' or Black == 'پاکسازی مدیران' then
base:del(TD_ID..'ModList:'..msg.chat_id)
send(msg.chat_id,msg.id, '• لیست مدیران پاکسازے شد','md')
end
if Black == 'configapi' or Black == 'پیکربندی ربات ها' and is_JoinChannel(msg) then 
local result = TD.getSupergroupMembers(msg.chat_id, "Bots", '' , 0 , 200 )
for k,v in pairs(result.members) do
local Diamond = TD.getUser(v.member_id.user_id)
if Diamond.type._ == "userTypeBot" then 
base:sadd(TD_ID..'ModList:'..msg.chat_id,Diamond.id)
end
end
send(msg.chat_id,msg.id,'تمامی ربات هایی که ادمین گروه بودند به لیست مدیران ربات اضافه شدند !',"md")
end	  
		 
end
if Black and (is_Owner(msg) or (is_Mod(msg) and not (base:sismember(TD_ID..'LimitCmd:'..msg.chat_id,Black) or base:sismember(TD_ID..'LimitCmd:'..msg.chat_id,BaseCmd)))) then
if Black == 'lockedlist' then
local t = 'لیست محدود شدگان قفلی :\nبرای رفع محدودیت هر کاربر بر روی متن جلوی >  کلیک کرده و ان را ارسال کنید!\nا┅┅──┄┄═✺═┄┄──┅┅\n'
local mrr619 = base:smembers(TD_ID..'Gp3:'..msg.chat_id)
for k,v in pairs(mrr619) do  
local list = v:match('^(%d+)')
t = t..k..'-【 ['..v..'](tg://user?id='..list..') 】\n>`رهایی '..v..'`\n\n'
end
if #mrr619 == 0 then
t = 'لیست محدود شدگان قفلی گروه خالے میباشد'
end
send(msg.chat_id,msg.id,t,'md')
end
if Black == 'clean lockedlist' then
base:del(TD_ID..'Gp3:'..msg.chat_id)
send(msg.chat_id,msg.id,'>لیست محدودشدگان پاکسازی شد...!','md')
end
if Black and (Black:match('^رهایی (%d+) (.*) (.*)$')) then
local user = Black:match('^رهایی (.*)$')
local id = user:match('(%d+)')
local mmad = string.gsub(user,id,'')
local diamond = TD.getUser(id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'Gp3:'..msg.chat_id,user) then
base:srem(TD_ID..'Gp3:'..msg.chat_id,user)
send(msg.chat_id,msg.id,'> کاربر '..name..' از محدودیت'..mmad..' رهایی یافت','md')
else
send(msg.chat_id,msg.id,'> عملیات ناموفق !','md')
end
end

if Black == 'filtersens off' or Black == 'حساسیت فیلتر خاموش' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'FilterSen')
send(msg.chat_id,msg.id,'> حساسیت فیلتر خاموش شد!\nدر صورت فیلتر کردن یک کلمه اگر حرف دیگری به ان کلمه چسبیده باشد ان متن پاک نخواهد شد\n\nبرای مثال اگر موبو رو فیلتر کنید در صورتی که پیام موبوگرام ارسال شود پیام پاک نخواهد شد','md')
end
if Black == 'filtersens on' or Black == 'حساسیت فیلتر روشن' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'FilterSen')
send(msg.chat_id,msg.id,'> حساسیت فیلتر روشن شد...!\nدقت کنید که در صورت روشن بودن حساسیت فیلتر هر متنی که کلمه فیلتر شده داخل ان وجود داشته باشد پاک خواهد شد\n\nبرای مثال در صورت فیلتر سل اگر کاربر سلام ارسال کند پیام او پاک خواهد شد','md')
end
if Black == 'kickbotpm on' or Black == 'پیام مسدود ربات روشن' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'kickbotpm')
send(msg.chat_id,msg.id,'> پیام مسدود ربات #فعال شد و از این پس کسی ربات اد کند پیام #اخطار داده خواهد شد.','md')
end
if Black == 'kickbotpm off' or Black == 'پیام مسدود ربات خاموش' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'kickbotpm')
send(msg.chat_id,msg.id,'> پیام مسدود ربات #غیرفعال شد و از این پس پیام #اخطار ربات داده نخواهد شد','md')
end
if Black == 'msgcheckpm on' or Black == 'پیام مسیج چک روشن' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm')
send(msg.chat_id,msg.id,'> پیام مسدود ربات #فعال شد و از این پس کسی ربات اد کند پیام #اخطار داده خواهد شد.','md')
end
if Black == 'msgcheckpm off' or Black == 'پیام مسیج چک خاموش' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'MsgCheckPm')
send(msg.chat_id,msg.id,'> پیام مسدود ربات #غیرفعال شد و از این پس پیام #اخطار ربات داده نخواهد شد','md')
end
if Black == 'gid' or Black == 'ایدی گروه' then 
send(msg.chat_id,msg.id,msg.chat_id,'md')
end
if Black == 'setgp' or Black == 'ثبت گروه' then 
base:sadd(TD_ID..'gpuser:'..msg.sender_id.user_id,msg.chat_id)
send(msg.chat_id,msg.id,'>گروه با موفقیت در لیست گروهاے مدیریتی در خصوصی ربات ثبت شد...!\n\nشما میتوانید با مراجعه به پی وے ربات تنظیمات گروه خود را در خصوصی ربات انجام دهید.','md')
end
if (Black == "clean deleted" or Black == 'پاکسازی دیلیت اکانتی ها') and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
cleandeleted(msg)
send(msg.chat_id,msg.id,'⭕تمام کاربران دیلیت اکانتے از گروه حذف شدند','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
----------------------
if Black1 and (Black1:match('^[Ss]etdescription (.*)') or Black1:match('^تنظیم درباره (.*)')) then
local description = Black1:match('^تنظیم درباره (.*)') or Black1:match('^[Ss]etdescription (.*)')
TD.setChatDescription(msg.chat_id,description)
local text = [[درباره گروه تغییر کرده به ]]..description
send(msg.chat_id,msg.id,text,'md')
end
if Black1 and (Black1:match('^[Ss]etname (.*)') or Black1:match('^تنظیم نام (.*)')) then
local Title = Black1:match('^[Ss]etname (.*)') or Black1:match('^تنظیم نام (.*)')
local Diamond = TD.getChat(msg.chat_id)
local Hash = TD_ID..'StatsGpByName'..msg.chat_id
local ChatTitle = Diamond.title
base:set(Hash,ChatTitle)
TD.setChatTitle(msg.chat_id,Title)
send(msg.chat_id, msg.id,'نام گروه تغییر ڪرد به : '..Title..'','html')
end
if (Black == 'pin' or Black == 'سنجاق') and is_JoinChannel(msg)  and tonumber(reply_id) > 0 then 
send(msg.chat_id,reply_id,'📎 ایــن پیام سنجاق شد','md')
TD.pinChatMessage(msg.chat_id,reply_id)
end
if (Black == 'unpin' or Black == 'حذف سنجاق') and is_JoinChannel(msg) then
send(msg.chat_id,msg.id,'📌 پیام حذف سنجاق شد','md')
TD.pinChatMessage(msg.chat_id)
end
if Black == 'lock group' or Black == 'قفل گروه' then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'automuteall') then
send(msg.chat_id,msg.id,'• قفلی خودکار فعال است! \nبرای قفل کردن گروه ابتدا قفل خودکار را غیرفعال    کنید','md')
else
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'Mute_All')
send(msg.chat_id,msg.id,'•گروه #قفل شد و تمامی پیام هاے بعدے گروه پاک خواهند شد !','md')
end
end
if Black == 'unlock group' or Black == 'بازکردن گروه' then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'automuteall') then
send(msg.chat_id,msg.id,'• قفلی خودکار فعال است! \nبرای بازکردن گروه ابتدا قفل خودکار را غیرفعال    کنید','md')
else
base:srem(TD_ID..'Gp2:'..msg.chat_id,'Mute_All')
local mutes =  base:smembers(TD_ID..'Mutes:'..msg.chat_id)
for k,v in pairs(mutes) do
base:srem(TD_ID..'Mutes:'..msg.chat_id,v)
UnRes(msg.chat_id,v)
end
send(msg.chat_id,msg.id,'•گروه باز شد و هم اکنون اعضاے گروه قادر به ارسال پیام هستند !','md')
end
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if Black1 and (Black1:match('^([Mm]uteall) (.*)$') or Black1:match('^(حالت قفل گروه) (.*)$')) and is_JoinChannel(msg) then
local Black1 = Black1:gsub("حالت قفل گروه", "muteall")
local status = {string.match(Black1, "^([Mm]uteall) (.*)$")}
if status[2] == 'mute' or status[2] == 'محدود' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'Tele_Mute')
send(msg.chat_id,msg.id,'|↜ قفل گروه در حالت محدود سازے قرار گرفت','md')
end
if status[2] == 'del' or status[2] == 'حذف' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'Tele_Mute')
send(msg.chat_id,msg.id,'|↜ قفل گروه در حالت حذف پیام کاربر قرار گرفت','md')
end
end  

if Black and (Black:match("^[Ll]ock auto$") or Black:match("^قفل خودکار$")) then
local Black = Black:gsub("قفل خودکار", "Lock auto")
local s = base:get(TD_ID.."bot:muteall:start" .. msg.chat_id)
local t = base:get(TD_ID.."bot:muteall:stop" .. msg.chat_id)
if not s and not t then
base:setex(TD_ID.."bot:SetMuteall:start" .. msg.chat_id .. ":" .. msg.sender_id.user_id, 60, true)
base:del(TD_ID.."bot:SetMuteall:stop" .. msg.chat_id .. ":" .. msg.sender_id.user_id)
send(msg.chat_id, msg.id, "> لطفا زمان شروع قفل خودکار را ارسال نمایید ❗️ \nبه طور مثال :\n14:38", "md")
elseif not base:get(TD_ID.."bot:muteall:Time" .. msg.chat_id) then
base:set(TD_ID.."bot:muteall:Time"..msg.chat_id,true) 
send(msg.chat_id, msg.id, "> قفل خودکار فعال شد ❗️\nبرای تنظیم مجدد زمان ، دستور Settime را ارسال نمایید ❗️", "md")
else
send(msg.chat_id, msg.id, "> قفل خودکار از قبل فعال است ❗️\nبرای تنظیم مجدد زمان ، دستور Settime را ارسال نمایید ❗️", "md")
end
end
if base:get(TD_ID.."bot:SetMuteall:start" .. msg.chat_id .. ":" .. msg.sender_id.user_id) and Black:match("^%d+:%d+$") then
local ap = {
string.match(Black, "^(%d+:)(%d+)$")
}
local h = Black:match("%d+:")
h = h:gsub(":", "")
local m = Black:match(":%d+")
m = m:gsub(":", "")
local h_ = 23
local m_ = 59
if h_ >= tonumber(h) and m_ >= tonumber(m) then
local TimeStart = Black:match("^%d+:%d+")
send(msg.chat_id, msg.id, "> لطفا زمان پایان قفل خودکار را ارسال نمایید ❗️","md")
base:del(TD_ID.."bot:SetMuteall:start" .. msg.chat_id .. ":" .. msg.sender_id.user_id)
base:set(TD_ID.."bot:muteall:start" .. msg.chat_id, TimeStart)
base:setex(TD_ID.."bot:SetMuteall:stop" .. msg.chat_id .. ":" .. msg.sender_id.user_id, 60, true)
else
send(msg.chat_id, msg.id,"> زمان ارسال شده صحیح نمیباشد ❗️", "md")
end
end
if base:get(TD_ID.."bot:SetMuteall:stop" .. msg.chat_id .. ":" .. msg.sender_id.user_id) then
local t = base:get(TD_ID.."bot:muteall:start" .. msg.chat_id)
if Black:match("^%d+:%d+") and not Black:match(t) then
local ap = {
string.match(Black, "^(%d+):(%d+)$")
}
local h = Black:match("%d+:")
h = h:gsub(":", "")
local m = Black:match(":%d+")
m = m:gsub(":", "")
local h_ = 23
local m_ = 59
if h_ >= tonumber(h) and m_ >= tonumber(m) then
local TimeStop = Black:match("^%d+:%d+")
base:del(TD_ID.."bot:SetMuteall:stop" .. msg.chat_id .. ":" .. msg.sender_id.user_id)
base:set(TD_ID.."bot:muteall:stop" .. msg.chat_id, TimeStop)
base:set(TD_ID.."bot:muteall:Time" .. msg.chat_id, true)
local start = base:get(TD_ID.."bot:muteall:start" .. msg.chat_id)
local stop = base:get(TD_ID.."bot:muteall:stop" .. msg.chat_id)
send(msg.chat_id, msg.id, "> قفل خودکار هر روز در ساعت " .. start .. " فعال و در ساعت " .. stop .. " غیرفعال خواهد شد ❗️", "md")
base:del(TD_ID.."bot:muteall:start_Unix" .. msg.chat_id)
base:del(TD_ID.."bot:muteall:stop_Unix" .. msg.chat_id)
else
send(msg.chat_id, msg.id, "> زمان ارسال شده صحیح نمیباشد ❗️", "md")
end
end
end

local Black = Black:gsub("بازکردن خودکار", "Unlock auto")
if (Black:match("^[Uu]nlock auto$") or Black:match("^بازکردن خودکار$")) then
if base:get(TD_ID.."bot:muteall:Time" .. msg.chat_id) then
send(msg.chat_id, msg.id, "> قفل خودکار غیرفعال شد ❗️ ", "md")
base:del(TD_ID.."bot:muteall:Time" .. msg.chat_id)
else
send(msg.chat_id, msg.id,"> قفل خودکار از قبل غیرفعال است ❗️ ","md")
end
end
local Black = Black:gsub("تنظیم زمان", "Settime")
if (Black:match("^[Ss]ettime$") or Black:match("^تنظیم زمان$")) then
base:setex(TD_ID.."bot:SetMuteall:start" .. msg.chat_id .. ":" .. msg.sender_id.user_id, 60, true)
base:del(TD_ID.."bot:SetMuteall:stop" .. msg.chat_id .. ":" .. msg.sender_id.user_id)
send(msg.chat_id, msg.id, "> لطفا زمان شروع قفل خودکار را ارسال نمایید ❗️ \nبه طور مثال :\n14:38", "md")
end
-->><<Cwm
if (Black == 'cwm on' or Black == 'پاکسازی پیام ربات روشن') and is_JoinChannel(msg) then
timecgms = base:get(TD_ID..'Times_Welcome:'..msg.chat_id) or 15
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cl_welcome') then
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار پیام ربات از قبل فعال بود\n#زمان : '..timecgms,'md')
else
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'cl_welcome')
send(msg.chat_id,msg.id,'✅ پاکسازے خودکار پیام هاے ربات فعال شد...!\n🕗 زمان پاکسازے خودکار هر '..timecgms..' ثانیه یکبار است.','md')
end
end
if (Black == 'cwm off' or Black == 'پاکسازی پیام ربات خاموش') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cl_welcome') then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'cl_welcome')
send(msg.chat_id,msg.id,'✅پاکسازے خودکار پیام هاے ربات غیرفعال شد...!','md')
else
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار پیام هاے ربات غیرفعال بود...!','md')
end
end

if Black and (Black:match('^cwmtime (%d+)$') or Black:match('^زمان پاکسازی پیام ربات (%d+)$')) and is_JoinChannel(msg) then
num = Black:match('^cwmtime (%d+)$') or Black:match('^زمان پاکسازی پیام ربات (%d+)$')
if tonumber(num) < 10 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *10* بکار ببرید','md')
else
base:set(TD_ID..'Times_Welcome:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'✅ زمان پاکسازی پیام ربات تنظیم شد به : *'..num..'* ثانیه','md')
end
end
---------------
if Black == 'viplist' or Black == 'ویژه ها' then  
local list = base:smembers(TD_ID..'Vip:'..msg.chat_id)
local t = 'لیست ویژه \nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do  
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = 'لیست ویژه خالی میباشد...!'
end
send(msg.chat_id,msg.id,t,'md')
end
if Black == 'banlist' or Black == 'مسدودی ها' then  
local list = base:smembers(TD_ID..'BanUser:'..msg.chat_id)
local t = 'لیست مسدودی ها  :\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do  
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
local file = io.open("./libs/data/banlist.txt","w")
file:write(t)
file:close()
if #list == 0 then
t = 'لیست مسدودی ها خالی میباشد'
end
sendDocument(msg.chat_id,msg.id,'./libs/data/banlist.txt','','md')
send(msg.chat_id,msg.id,t,'md')
end
if (Black == 'clean banlist' or Black == 'پاکسازی مسدودی ها') and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
cleanbanlist(msg)
base:del(TD_ID..'BanUser:'..msg.chat_id)
send(msg.chat_id,msg.id,'✳ تمام ڪاربران محروم شده از لیست مسدود حذف شدند','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if (Black == 'clean mutelist' or Black == 'پاکسازی سکوتی ها') then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
cleanmutelist(msg)
base:del(TD_ID..'MuteList:'..msg.chat_id)
send(msg.chat_id,msg.id,'✴ تمام افراد سکوت شده ازاد شدند','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if (Black == 'clean bots' or Black == 'پاکسازی ربات ها') and is_JoinChannel(msg) then
cleanbots(msg)
end
--------
if (Black == 'linkpv' or Black == 'لینک پیوی') and is_JoinChannel(msg) then
local link = base:get(TD_ID..'Link:'..msg.chat_id)
if link then
sendApi(msg.sender_id.user_id,'|↜ لینک گروه :\n'..link..'',0,'html')
send(msg.chat_id,msg.id,'|↜ لینک گروه به پیوی شما ارسال شد','html')
else
send(msg.chat_id,msg.id,'|↜ لینک در گروه شما ثبت نشده است\nلطفا با دستور :\n\nsetlink (link)\nیا\nتنظیم لینک (لینک)\nلینک گروه خودرا ثبت کنید','md')
end
end

if Black and (Black:match('^setlink http(.*)') or Black:match('^تنظیم لینک http(.*)')) then
local link = msg.content.text.text:match('^setlink (.*)') or msg.content.text.text:match('^تنظیم لینک (.*)')
base:set(TD_ID..'Link:'..msg.chat_id,link)
send(msg.chat_id,msg.id,'لینک گروه ثبت شد :\n'..link..'','html')
end
if Black and (Black:match('^clean fake') or Black:match('^پاکسازی فیک$')) and is_JoinChannel(msg) then
local statsurl = TD.getChatMember(msg.chat_id,Config.BotJoiner)
if statsurl and statsurl.status and statsurl.status.rights.can_restrict_members == true then
local data = getGroupMembers(msg.chat_id, 'Recent', 'all', 100)
nums = 0
for k,v in pairs(data) do
local user = TD.getUser(v)
if user and user.type and user.type._ == "userTypeRegular" then
if user and user.status and user.status._ == "userStatusEmpty" then
KickUser(msg.chat_id,user.id)
print(user.id)
nums = nums +1
end
end
end
send(msg.chat_id,msg.id,'اعضاے #غیرفعال و فیک از گروه اخراج شدند\n#تعداد شناسایی شده : *'..nums..'* نفر','md')
else
send(msg.chat_id,msg.id,'✖ دسرسی لازم براے پاکسازے  اعضاے #غیرفعال رو ندارد\n─┅━━━━━━━┅─\nربات را ادمین کرده سپس مجدد تلاش کنید !','md')
end
end
--<><><><>Vip Add
if Diamondent and (Black:match('^setvipadd (.*)') or Black:match('^معاف (.*)')) or Black and (Black:match('^setvipadd @(.*)') or Black:match('^معاف @(.*)') or Black:match('^setvipadd (%d+)$') or Black:match('^معاف (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^setvipadd (.*)') or Black:match('^معاف (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^setvipadd @(.*)') or Black:match('^معاف @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^setvipadd (%d+)') or Black:match('^معاف (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^setvipadd (.*)') or Black:match('^معاف (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'VipAdd:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز قبل جزء لیست ویژه ادجباری بود...!','md')
else
send(msg.chat_id,msg.id, '✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nبه لیست ویژه اداجباری اضافه شد...!','md')
base:sadd(TD_ID..'VipAdd:'..msg.chat_id,mrr619)
end
else
send(msg.chat_id,msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end

if Diamondent and (Black:match('^remvipadd (.*)') or Black:match('^حذف معاف (.*)')) or Black and (Black:match('^remvipadd @(.*)') or Black:match('^حذف معاف @(.*)') or Black:match('^remvipadd (%d+)$') or Black:match('^حذف معاف (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^remvipadd (.*)') or Black:match('^حذف معاف (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^remvipadd @(.*)') or Black:match('^حذف معاف @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^remvipadd (%d+)') or Black:match('^حذف معاف (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^remvipadd (.*)') or Black:match('^حذف معاف (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if not base:sismember(TD_ID..'VipAdd:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nدر لیست ویژه اداجباری نبود...!','md')
else
base:srem(TD_ID..'VipAdd:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز لیست ویژه اداجباری خارج شد...!','md')
end
else 
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if Black == 'vipaddlist' or Black == 'لیست معاف' then  
local list = base:smembers(TD_ID..'VipAdd:'..msg.chat_id)
local t = 'لیست ویژه اداجباری :\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do  
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = 'لیست ویژه اداجباری خالی میباشد...!'
end
send(msg.chat_id,msg.id,t,'md')
end
if Black == 'clean vipaddlist' or Black == 'پاکسازی لیست معاف'  then
base:del(TD_ID..'VipAdd:'..msg.chat_id)
send(msg.chat_id,msg.id, '• لیست ویژه اداجباری پاکسازی شد!','md')
end
if (Black == 'setvip' or Black == 'تنظیم ویژه' or Black == 'ارتقا ویژه' or Black == 'ترفیع ویژه' or Black == 'ویژه') and tonumber(reply_id) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
if base:sismember(TD_ID..'Vip:'..msg.chat_id, user) then
send(msg.chat_id,msg.id,'|↜ کاربر : '..user..' از قبل در لیست افراد ویژه قرار داشت','md')
else
send(msg.chat_id,msg.id,'⭕ کاربر : '..user..' به لیست افراد ویژه افزوده شد','md')
base:sadd(TD_ID..'Vip:'..msg.chat_id, user)
end
end
end
if Diamondent and (Black:match('^setvip (.*)') or Black:match('^ویژه (.*)') or Black:match('^تنظیم ویژه (.*)') or Black:match('^ارتقا ویژه (.*)') or Black:match('^ترفیع ویژه (.*)')) or Black and (Black:match('^setvip @(.*)') or Black:match('^ویژه @(.*)') or Black:match('^تنظیم ویژه @(.*)') or Black:match('^ارتقا ویژه @(.*)') or Black:match('^ترفیع ویژه @(.*)') or Black:match('^setvip (%d+)$') or Black:match('^ویژه (%d+)$') or Black:match('^تنظیم ویژه (%d+)$') or Black:match('^ارتقا ویژه (%d+)$') or Black:match('^ترفیع ویژه (%d+)$')) then
local BDSource = Black:match('^setvip (.*)') or Black:match('^ویژه (.*)') or Black:match('^تنظیم ویژه (.*)') or Black:match('^ارتقا ویژه (.*)') or Black:match('^ترفیع ویژه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^setvip @(.*)') or Black:match('^ویژه @(.*)') or Black:match('^تنظیم ویژه @(.*)') or Black:match('^ارتقا ویژه @(.*)') or Black:match('^ترفیع ویژه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^setvip (%d+)$') or Black:match('^ویژه (%d+)$') or Black:match('^تنظیم ویژه (%d+)$') or Black:match('^ارتقا ویژه (%d+)$') or Black:match('^ترفیع ویژه (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^setvip (.*)') or Black:match('^ویژه (.*)') or Black:match('^تنظیم ویژه (.*)') or Black:match('^ارتقا ویژه (.*)') or Black:match('^ترفیع ویژه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if base:sismember(TD_ID..'Vip:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز قبل در لیست ویژه ربات در گروه قرار داشت','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nبه لیست ویژه ربات در گروه افزوده شد','md')
base:sadd(TD_ID..'Vip:'..msg.chat_id,mrr619)
end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'md')
end
end

if Black and (Black:match('^setvip (%d+) (%S+)$') or Black:match('^تنظیم ویژه (%d+) (%S+)$') or Black:match('^ارتقا ویژه (%d+) (%S+)$') or Black:match('^ترفیع ویژه (%d+) (%S+)$') or Black:match('^ویژه (%d+) (%S+)$')) and (tonumber(reply_id) > 0) and is_JoinChannel(msg) then 
         local matches = {string.match(Black, '(%d+) (%S+)$')} 
         local result = TD.getMessage(msg.chat_id, tonumber(reply_id)) 
         if result._ == 'error' then
TD.sendText(msg.chat_id, msg.id,' متاسفانه ربات های (Api) قادر به چک کردن پیام های یکدیگر نمیباشند !', 'lg') 
         else 
          if matches[2] == 'm' or matches[2] == 'دقیقه' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'VIP', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'دقیقه'}), 'lg') 
          elseif matches[2] == 'h' or matches[2] == 'ساعت' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'VIP', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'ساعت'}), 'lg') 
          elseif matches[2] == 'd' or matches[2] == 'روز' then 
TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'VIP', chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'روز'}), 'lg') 
          end 
         end
end

if (Black == 'clean viplist' or Black == 'پاکسازی ویژه ها') and is_JoinChannel(msg) then
base:del(TD_ID..'Vip:'..msg.chat_id)
send(msg.chat_id,msg.id,'✴ لیست افراد ویژه پاکسازے شد','md')
end
if (Black == 'remvip' or Black == 'عزل ویژه' or Black == 'خلع ویژه' or Black == 'تنزیل ویژه' or Black == 'حذف ویژه') and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
if base:sismember(TD_ID..'Vip:'..msg.chat_id, user) then
send(msg.chat_id,msg.id,'⭕ کاربر : '..user..' از لیست افراد ویژه خارج شد','md')
base:srem(TD_ID..'Vip:'..msg.chat_id, user)
else
send(msg.chat_id,msg.id,'⭕ کاربر : '..user..' از قبل در لیست افراد ویژه نبود','md')
end
end
end
if Diamondent and (Black:match('^remvip (.*)') or Black:match('^حذف ویژه (.*)') or Black:match('^عزل ویژه (.*)') or Black:match('^خلع ویژه (.*)') or Black:match('^تنزیل ویژه (.*)')) or Black and (Black:match('^remvip @(.*)') or Black:match('^حذف ویژه @(.*)') or Black:match('^عزل ویژه @(.*)') or Black:match('^تنزیل ویژه @(.*)') or Black:match('^خلع ویژه @(.*)') or Black:match('^remvip (%d+)$') or Black:match('^حذف ویژه (%d+)$') or Black:match('^عزل ویژه (%d+)$') or Black:match('^خلع ویژه (%d+)$') or Black:match('^تنزیل ویژه (%d+)$')) then
local BDSource = Black:match('^remvip (.*)') or Black:match('^حذف ویژه (.*)') or Black:match('^عزل ویژه (.*)') or Black:match('^خلع ویژه (.*)') or Black:match('^تنزیل ویژه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^remvip @(.*)') or Black:match('^حذف ویژه @(.*)') or Black:match('^عزل ویژه @(.*)') or Black:match('^خلع ویژه @(.*)') or Black:match('^تنزیل ویژه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^remvip (%d+)$') or Black:match('^حذف ویژه (%d+)$') or Black:match('^عزل ویژه (%d+)$') or Black:match('^خلع ویژه (%d+)$') or Black:match('^تنزیل ویژه (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^remvip (.*)') or Black:match('^حذف ویژه (.*)') or Black:match('^عزل ویژه (.*)') or Black:match('^خلع ویژه (.*)') or Black:match('^تنزیل ویژه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if not base:sismember(TD_ID..'Vip:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'کاربر : '..BDSource..'\nدر لیست ویژه ربات نیست','md')
else
base:srem(TD_ID..'Vip:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'کاربر : '..BDSource..'\nاز لیست ویژه ربات حذف شد','md')
end
else 
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!','md')
end
end
if (Black == 'restartpm' or Black == 'ریستارت پیام ها') and tonumber(reply_id) == 0 and is_JoinChannel(msg) then
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..msg.sender_id.user_id)
send(msg.chat_id,msg.id,'♻ تعداد پیام هاے کل و امروز شما صفر شد...!','md')
end
 if (Black == 'restartpm' or Black == 'ریستارت پیام') and tonumber(reply_id) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..user)
send(msg.chat_id,msg.id,'✦ پیام هاے امروز کاربر : ['..name..'](tg://user?id='..user..') ری استارت شد...!','md')
end
end

if Black and (Black:match('^setrank (.*)$') or Black:match('^تنظیم لقب (.*)$')) and tonumber(reply_id) ~= 0  then
local rank = Black:match('^setrank (.*)$') or Black:match('^تنظیم لقب (.*)$')
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
if tonumber(user) == tonumber(BotCliId) then
send(msg.chat_id, msg.id,  '❎ من نمیتوانم پیام خودم را چک کنم','md')
return false
end
if tonumber(user) == Sudoid then
send(msg.chat_id, msg.id,'نمیتونی به بابام لقب بدی 🖕😐','md')
return false
end
base:set(TD_ID..'rank'..msg.chat_id..user,rank)
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
send(msg.chat_id,msg.id,'✦ مقام کاربر : '..MBD(name,user)..' به ['..rank..']\nتغییر کرد\n','md')
end
end
if Black and (Black:match('^delrank$') or Black:match('^حذف لقب')) and tonumber(reply_id) ~= 0 then
local rank = Black:match('^delrank$') or Black:match('^حذف لقب')
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
base:del(TD_ID..'rank'..msg.chat_id..user)
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
send(msg.chat_id,msg.id,'✦ مقام کاربر : ['..name..'](tg://user?id='..user..') پاک شد .','md')
end
end
if Black and (Black:match('^restartpm @(.*)') or Black:match('^ریستارت پیام @(.*)')) and is_JoinChannel(msg) then
local username = Black:match('^restartpm @(.*)') or Black:match('^ریستارت پیام @(.*)')
local Diamond = TD.searchPublicChat(username)
if Diamond.id then
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..Diamond.id)
send(msg.chat_id,msg.id,'✦ پیام هاے امروز کاربر : '..username..' ری استارت شد...!','html')
else
send(msg.chat_id,msg.id,'❎ کاربر یافت نشد','md')
end
end
if (Black == 'firstmute on' or Black == 'محدودیت تبچی فعال') and is_JoinChannel(msg) then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'FirstTabchiMute')
send(msg.chat_id,msg.id,'✅ محدود شدن تمامی اعضای جدید به محض ورود فعال شد !\nاین کاربران باید حتما به سوال احراز هویت پاسخ دهند تا بتوانند در گروه پیام ارسال کنند !','md')
end
if (Black == 'firstmute off' or Black == 'محدودیت تبچی غیرفعال') and is_JoinChannel(msg) then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'FirstTabchiMute')
send(msg.chat_id,msg.id,'✅ محدود شدن تمامی اعضا به محض ورود غیرفعال شد !','md')
end
if Black and (Black:match('^setmutetime (%d+)[hms]') or Black:match('^زمان سکوت (%d+)[سدث]')) and is_JoinChannel(msg) then
local num = Black:match('^setmutetime (%d+)[hms]') or Black:match('^زمان سکوت (%d+)[سدث]')
 if Black and (Black:match('(%d+)h') or Black:match('(%d+)س')) then
          time_match = Black:match('(%d+)h') or Black:match('(%d+)س')
          time = time_match * 3600
          end
          if Black and (Black:match('(%d+)m') or Black:match('(%d+)د')) then 
          time_match = Black:match('(%d+)m') or Black:match('(%d+)د')
          time = time_match * 60
          end
          if Black and (Black:match('(%d+)s') or Black:match('(%d+)ث')) then
          time_match = Black:match('(%d+)s') or Black:match('(%d+)ث')
          time = time_match
          end
base:set(TD_ID..'mutetime:'..msg.chat_id,time)
send(msg.chat_id,msg.id,'🕗 زمان محدودیت سکوت تنظیم شد بر روے : *'..time..'* ثانیه\nدر صورت محدود شدن کاربر,کاربر مورد نظر *'..time..'* ثانیه از ارسال پیام در گروه منع خواهد شد.','md')
end
if Black == 'panel public' or Black == 'پنل همگانی' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'PanelPv')
send(msg.chat_id,msg.id,'> پنل بر روی همگانی تنظیم شد و مدیر دیگر قادر به کار با پنل دیگر مدیران نیز خواهد بود','md')
end
if Black == 'panel privite' or Black == 'پنل خصوصی' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'PanelPv')
send(msg.chat_id,msg.id,'> پنل بر روی خصوصی تنظیم شد و مدیر دیگر قادر به کار با پنل دیگران مدیران نخواهد بود','md')
end
if Black == 'del' or Black == 'حذف' and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
TD.deleteMessages(msg.chat_id,{[1] = Diamond.id})
end
--------cgm auto-------
if Black and (Black:match('^(cgmtime) (%d+):(%d+)$') or Black:match('^(زمان پاکسازی گروه) (%d+):(%d+)$')) and is_JoinChannel(msg) then
local Black = Black:gsub("زمان پاکسازی گروه","cgmtime")
local matches = {string.match(Black, "^(cgmtime) (%d+):(%d+)$")}
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon')  then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon')
auto= 'فعال ✔️'
else
auto= 'غیرفعال'
end
local starttime2 = matches[2]..":"..matches[3]
base:set(TD_ID..'StartTimeCgm'..msg.chat_id,starttime2)
local starttime = matches[2]..matches[3]
base:set(TD_ID..'autoCgmstart'..msg.chat_id,starttime)
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon')
test = 'پیام های گروه هر روز در ساعت :\n* 【'..starttime2..'】* به صورت خودکار پاکسازی میشود\n-----------------------------------\nپاکسازی خودکار : '..auto..''
send(msg.chat_id,msg.id,test,"md")
end 
if (Black == 'cgm on' or Black == 'پاکسازی فعال') and is_JoinChannel(msg) then
local timecgm = base:get(TD_ID..'StartTimeCgm'..msg.chat_id) or '06:00'
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon') and timecgm then
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار از قبل فعال بود','md')
else
if timecgm then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon')
send(msg.chat_id,msg.id,'✅ پاکسازے خودکار #فعال شد...!\n🕗 زمان پاکسازے خوکار ساعت *'..timecgm..'*','md')
else
send(msg.chat_id,msg.id,'✖️ زمان پاکسازے خودکار شما تنظیم نشده است\nابتدا با دستور\ncleangptime (NUMBER)h\nیا زمان پاکسازے پاکسازی پیام گروه (عدد)\nپاکسازے پیام گروه را تنظیم کنید سپس با دستور\nautoclean on\nیا پاکسازے پیام گروه روشن\nاقدام به فعال سازے آن کنید','md')
end
end
end 
if (Black == 'cgm off' or Black == 'پاکسازی غیرفعال') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon') then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'cgmautoon')
base:del(TD_ID..'cgmauto:'..msg.chat_id)
send(msg.chat_id,msg.id,'✅ پاکسازے خودکار غیرفعال شد...!','md')
else
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار غیرفعال بود...!','md')
end
end 
--<><><>BotCgm
if Black and (Black:match('^cbmtime (%d+)$') or Black:match('^زمان پاکسازی ربات (%d+)$')) and is_JoinChannel(msg) then
local time_match = Black:match('^cbmtime (%d+)') or Black:match('^زمان پاکسازی ربات (%d+)')
base:set(TD_ID..'cbmtime:'..msg.chat_id,time_match)
send(msg.chat_id,msg.id,'🕗 زمان پاکسازے خودکار تنظیم شد بر روے : *'..time_match..'* ثانیه\nیعنی هر '..time_match..' ثانیه یکبار پاکسازے  پیام هاے ربات بصورت اتوماتیک انجام خواهد شد...!','md')
end
if (Black == 'cbm on' or Black == 'پاکسازی ربات فعال') and is_JoinChannel(msg) then
local timecgms = base:get(TD_ID..'cbmtime:'..msg.chat_id) or 10
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cbmon') then
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار از قبل فعال بود\n#زمان : '..timecgms,'md')
else
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'cbmon')
send(msg.chat_id,msg.id,'✅ پاکسازے خودکار پیام هاے ربات فعال شد...!\n🕗 زمان پاکسازے خودکار هر '..timecgms..' ثانیه یکبار است.','md')
end
end
if (Black == 'cbm off' or Black == 'پاکسازی ربات غیرفعال') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cbmon') then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'cbmon')
send(msg.chat_id,msg.id,'✅ پاکسازے خودکار پیام هاے ربات غیرفعال شد...!','md')
else
send(msg.chat_id,msg.id,'✔️ پاکسازے خودکار پیام هاے ربات غیرفعال بود...!','md')
end
end
--<><><>Mute
if (Black == 'mute' or Black == 'لال' or Black == 'خفه' or Black == 'سکوت') and tonumber(reply_id) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if VipUser(msg,user) then
send(msg.chat_id,msg.id,'❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ['..name..'](tg://user?id='..user..') دارای مقام میباشد شما نمیتوانید او را سکوت کنید...!','md')
else
MuteUser(msg.chat_id,user,0)
base:sadd(TD_ID..'MuteList:'..msg.chat_id,user)
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..') در حالت سکوت قرار گرفت🔇','md')
end
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
if Diamondent and (Black:match('^mute (.*)') or Black:match('^سکوت (.*)') or Black:match('^لال (.*)') or Black:match('^خفه (.*)')) or Black and (Black:match('^mute @(.*)') or Black:match('^سکوت @(.*)') or Black:match('^لال @(.*)') or Black:match('^خفه @(.*)') or Black:match('^mute (%d+)$') or Black:match('^سکوت (%d+)$') or Black:match('^لال (%d+)$') or Black:match('^خفه (%d+)$')) and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
local BDSource = Black:match('^mute (.*)') or Black:match('^سکوت (.*)') or Black:match('^لال (.*)') or Black:match('^خفه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^mute @(.*)') or Black:match('^سکوت @(.*)') or Black:match('^لال @(.*)') or Black:match('^خفه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^mute (%d+)$') or Black:match('^سکوت (%d+)$') or Black:match('^لال (%d+)$') or Black:match('^خفه (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^mute (.*)') or Black:match('^سکوت (.*)') or Black:match('^لال (.*)') or Black:match('^خفه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == true then
if mrr619 then
if VipUser(msg,mrr619) then
send(msg.chat_id,msg.id,'❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ['..BDSource..'](tg://user?id='..mrr619..') دارای مقام میباشد شما نمیتوانید او را سکوت کنید...!','md')
else
MuteUser(msg.chat_id,mrr619,0)
base:sadd(TD_ID..'MuteList:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..') در حالت سکوت قرار گرفت🔇','md')
end
else 
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'md')
end
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
--<><><>UnMute
if (Black == 'unmute' or Black == 'حذف خفه' or Black == 'حذف لال' or Black == 'حذف سکوت') and tonumber(reply_id) ~= 0 and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
UnRes(msg.chat_id,user)
base:srem(TD_ID..'SilentList:'..msg.chat_id,user)
base:srem(TD_ID..'MuteList:'..msg.chat_id,user)
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\n از حالت سکوت خارج شد🔈','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
if Diamondent and (Black:match('^unmute (.*)') or Black:match('^حذف سکوت (.*)') or Black:match('^حذف لال (.*)') or Black:match('^حذف خفه (.*)')) or Black and (Black:match('^unmute @(.*)') or Black:match('^حذف سکوت @(.*)') or Black:match('^حذف لال @(.*)') or Black:match('^حذف خفه @(.*)') or Black:match('^unmute (%d+)$') or Black:match('^حذف سکوت (%d+)$') or Black:match('^حذف لال (%d+)$') or Black:match('^حذف خفه (%d+)$')) and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local BDSource = Black:match('^unmute (.*)') or Black:match('^حذف سکوت (.*)') or Black:match('^حذف لال (.*)') or Black:match('^حذف خفه (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^unmute @(.*)') or Black:match('^حذف سکوت @(.*)') or Black:match('^حذف لال @(.*)') or Black:match('^حذف خفه @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^unmute (%d+)$') or Black:match('^حذف سکوت (%d+)$') or Black:match('^حذف لال (%d+)$') or Black:match('^حذف خفه (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^unmute (.*)') or Black:match('^حذف سکوت (.*)') or Black:match('^حذف لال (.*)') or Black:match('^حذف خفه (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
UnRes(msg.chat_id,mrr619)
base:srem(TD_ID..'SilentList:'..msg.chat_id,mrr619)
base:srem(TD_ID..'MuteList:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\n از حالت سکوت خارج شد🔈','md')
else 
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'md')
end
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if Black and (Black:match('^([Ss]etforce) (.*)$') or Black:match('^(وضعیت افزودن اجباری) (.*)$')) then
local Black = Black:gsub("وضعیت افزودن اجباری", "setforce")
local status = {string.match(Black, "^([Ss]etforce) (.*)$")}
if status[2] == 'new user' or status[2] == 'جدید' then
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'force_NewUser')
send(msg.chat_id,msg.id,'وضعیت افزودن اجبارے براے کاربران جدید فعال شد\n>از این پس کاربران جدید باید به تعداد دلخواه شما ممبر به گروه اضافه کنند تا بتوانند پیام ارسال کنند!','md')
end
if status[2] == 'all user' or status[2] == 'همه' then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'force_NewUser')
send(msg.chat_id,msg.id,'وضعیت افزودن اجبارے براے همه کاربران فعال شد','md')
end
end
if Black and (Black:match('^mute (%d+) (%S+)$') or Black:match('^سکوت (%d+) (%S+)$') or Black:match('^خفه (%d+) (%S+)$') or Black:match('^لال (%d+) (%S+)$')) and (tonumber(reply_id) > 0) then
         local matches = {string.match(Black, '(%d+) (%S+)$')} 
         local result = TD.getMessage(msg.chat_id, tonumber(reply_id)) 
         if result._ == 'error' then 
          TD.sendText(msg.chat_id, msg.id,' متاسفانه ربات های (Api) قادر به چک کردن پیام های یکدیگر نمیباشند !', 'lg') 
         else 
          if matches[2] == 'm' or matches[2] == 'دقیقه' then 
           TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'MUTE', date = msg.date, chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'دقیقه'}), 'lg') 
          elseif matches[2] == 'h' or matches[2] == 'ساعت' then 
           TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'MUTE', date = msg.date, chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'ساعت'}), 'lg') 
          elseif matches[2] == 'd' or matches[2] == 'روز' then 
           TD.sendText(msg.chat_id, msg.id, ActionTimer({type = 'MUTE', date = msg.date, chat_id = msg.chat_id, user_id = result.sender_id.user_id, sender_id = msg.sender_id.user_id, time = tonumber(matches[1]), status = 'روز'}), 'lg') 
          end 
         end
end
if (Black == 'mutelist' or Black == 'سکوتی ها') and is_JoinChannel(msg) then
local list = base:smembers(TD_ID..'MuteList:'..msg.chat_id)
local t = '⭕ لیست افراد سکوت \nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = '⭕لیست افراد سکوت شده خالے است'
end
send(msg.chat_id,msg.id,t,'md')
end
if (Black == 'clean warnlist' or Black == 'پاکسازی اخطاری ها') and is_JoinChannel(msg) then
base:del(TD_ID..''..msg.chat_id..':warn')
send(msg.chat_id, msg.id,'لیست اخطار ها پاکسازے شد','md')
end
if (Black == "warnlist" or Black == "اخطاری ها") and is_JoinChannel(msg) then
local comn = base:hkeys(TD_ID..msg.chat_id..':warn')
local t = 'لیست اخطار ها :\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs (comn) do
local cont = base:hget(TD_ID..msg.chat_id..':warn', v)
t = t..k..'-【['..v..'](tg://user?id='..v..')】\nتعداد اخطار :【'..(cont - 1)..'】\n─┅━━━━━━━┅─\n'
end
if #comn == 0 then
t = 'لیست اخطار ها خالے میباشد'
end
send(msg.chat_id,msg.id,t,'md')
end
----UnBan By @Mrr619---
if Diamondent and (Black:match('^unban (.*)') or Black:match('^حذف بن (.*)') or Black:match('^حذف مسدود (.*)') or Black:match('^حذف صیک (.*)')) or Black and (Black:match('^unban @(.*)') or Black:match('^حذف بن @(.*)') or Black:match('^حذف مسدود @(.*)') or Black:match('^حذف صیک @(.*)') or Black:match('^unban (%d+)$') or Black:match('^حذف بن (%d+)$') or Black:match('^حذف مسدود (%d+)') or Black:match('^حذف صیک (%d+)$')) and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local Saeed = Black:match('^unban (.*)') or Black:match('^حذف بن (.*)') or Black:match('^حذف مسدود (.*)') or Black:match('^حذف صیک (.*)')
local Diamond = TD.searchPublicChat(Saeed)
local res = TD.getSupergroupMembers(msg.chat_id, 'ban', '' , 0 , 25 )
if not Diamondent and Black:match('^unban @(.*)') or Black:match('^حذف بن @(.*)') or Black:match('^حذف مسدود @(.*)') or Black:match('^حذف صیک @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^unban (%d+)') or Black:match('^حذف بن (%d+)') or Black:match('^حذف مسدود (%d+)') or Black:match('^حذف صیک (%d+)') then
mrr619 = Saeed
elseif Diamondent and Black:match('^unban (.*)') or Black:match('^حذف بن (.*)') or Black:match('^حذف مسدود (.*)') or Black:match('^حذف صیک (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
for k, v in pairs(res.members) do 
if tonumber(v.member_id.user_id) == tonumber(mrr619) then
UnRes(msg.chat_id,mrr619)
reportowner('|↜ کاربر : ['..Saeed..'](tg://user?id='..mrr619..')\n✦ حذف مسدود شد\nتوسط : ['..msg.sender_id.user_id..'](tg://user?id='..msg.sender_id.user_id..')')
end
end
base:srem(TD_ID..'BanUser:'..msg.chat_id,mrr619)
send(msg.chat_id,msg.id,'✦ کاربر ['..Saeed..'](tg://user?id='..mrr619..') از لیست مسدودین حذف شد...!','md')
else 
send(msg.chat_id, msg.id,'کاربر '..Saeed..' یافت نشد ...!',  'html')
end
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
----Ban By @XXXTENTACIONGG---
if Diamondent and (Black:match('^ban (.*)') or Black:match('^صیک (.*)') or Black:match('^بن (.*)') or Black:match('^مسدود (.*)')) or Black and (Black:match('^ban @(.*)') or Black:match('^صیک @(.*)') or Black:match('^مسدود @(.*)') or Black:match('^بن @(.*)') or Black:match('^ban (%d+)$') or Black:match('^صیک (%d+)$') or Black:match('^بن (%d+)$') or Black:match('^مسدود (%d+)')) and is_JoinChannel(msg) then
local XXXTENTACIONGG = Black:match('^ban (.*)') or Black:match('^صیک (.*)') or Black:match('^بن (.*)') or Black:match('^مسدود (.*)')
local Diamond = TD.searchPublicChat(XXXTENTACIONGG)
if not Diamondent and Black:match('^ban @(.*)') or Black:match('^صیک @(.*)') or Black:match('^بن @(.*)') or Black:match('^مسدود @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^ban (%d+)') or Black:match('^صیک (%d+)') or Black:match('^بن (%d+)') or Black:match('^مسدود (%d+)') then
mrr619 = XXXTENTACIONGG
elseif Diamondent and Black:match('^ban (.*)') or Black:match('^صیک (.*)') or Black:match('^بن (.*)') or Black:match('^مسدود (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if VipUser(msg,mrr619) then 
send(msg.chat_id,msg.id,'❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..') دارای مقام میباشد شما نمیتوانید او را مسدود کنید...!','md')
else
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
base:sadd(TD_ID..'BanUser:'..msg.chat_id,mrr619)
KickUser(msg.chat_id,mrr619)
base:incr(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id)
base:incr(TD_ID..'Total:BanUser:'..msg.chat_id)
send(msg.chat_id,msg.id,'✦ کاربر : ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..')\nاز گروه مسدود شد...!','md')
reportowner('|↜ کاربر : ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..')\n✦ از گروه مسدود شد\nتوسط : ['..msg.sender_id.user_id..'](tg://user?id='..msg.sender_id.user_id..')')
else
send(msg.chat_id,msg.id,'✦ کاربر ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..')\nاز گروه مسدود نشد!\n✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
else 
send(msg.chat_id, msg.id,'کاربر '..XXXTENTACIONGG..' یافت نشد ...!','html')
end
end
if (Black == 'ban' or Black == 'صیک' or Black == 'مسدود' or Black == 'بن') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if VipUser(msg,user) then
send(msg.chat_id,msg.id,'❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ['..name..'](tg://user?id='..user..') دارای مقام میباشد شما نمیتوانید او را مسدود کنید...!','md')
else
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nاز گروه مسدود شد...!','md')
base:sadd(TD_ID..'BanUser:'..msg.chat_id,user)
KickUser(msg.chat_id,user)
base:incr(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id)
base:incr(TD_ID..'Total:BanUser:'..msg.chat_id)
reportowner('|↜ کاربر : ['..name..'](tg://user?id='..user..')\n✦ از گروه مسدود شد\nتوسط : ['..msg.sender_id.user_id..'](tg://user?id='..msg.sender_id.user_id..')')
else
send(msg.chat_id,msg.id,'✦ کاربر ['..name..'](tg://user?id='..user..')\nاز گروه مسدود نشد!\n✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
end
if (Black == 'unban' or Black == 'حذف صیک' or Black == 'حذف مسدود' or Black == 'حذف بن') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local res = TD.getSupergroupMembers(msg.chat_id, 'ban', '' , 0 , 25 )
local user = Diamond.sender_id.user_id
if user then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
--for k, v in pairs(res.members) do 
if base:sismember(TD_ID..'BanUser:'..msg.chat_id,user) then
UnRes(msg.chat_id,user)
reportowner('|↜ کاربر : ['..name..'](tg://user?id='..user..')\n✦ از لیست مسدودین حذف شد\nتوسط : ['..msg.sender_id.user_id..'](tg://user?id='..msg.sender_id.user_id..')')
end
--end
send(msg.chat_id,msg.id,'✦ کاربر ['..name..'](tg://user?id='..user..') از لیست مسدودین حذف شد...!','md')
base:srem(TD_ID..'BanUser:'..msg.chat_id,user)
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
--<><><>Kick
if Diamondent and (Black:match('^kick (.*)') or Black:match('^اخراج (.*)')) or Black and (Black:match('^kick @(.*)') or Black:match('^اخراج @(.*)') or Black:match('^kick (%d+)$') or Black:match('^اخراج (%d+)$')) and is_JoinChannel(msg) then
local XXXTENTACIONGG = Black:match('^kick (.*)') or Black:match('^اخراج (.*)')
local Diamond = TD.searchPublicChat(XXXTENTACIONGG)
if not Diamondent and Black:match('^kick @(.*)') or Black:match('^اخراج @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^kick (%d+)') or Black:match('^اخراج (%d+)') then
mrr619 = XXXTENTACIONGG
elseif Diamondent and Black:match('^kick (.*)') or Black:match('^اخراج (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if VipUser(msg,mrr619) then
send(msg.chat_id,msg.id,'❌ #اخطار  !\nا─┅━━━━━━━┅─\n✦ کاربر ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..') دارای مقام میباشد شما نمیتوانید او را اخراج کنید...!','md')
else
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
KickUser(msg.chat_id,mrr619)
UnRes(msg.chat_id,mrr619)
base:incr(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id)
send(msg.chat_id,msg.id,'✦ کاربر ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..')\nاز گروه اخراج شد...!','md')
reportowner('|↜ کاربر : ['..XXXTENTACIONGG..'](tg://user?id='..mrr619..')\n✦ از گروه اخراج شد\nتوسط : ['..msg.sender_id.user_id..'](tg://user?id='..msg.sender_id.user_id..')')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
else 
send(msg.chat_id, msg.id,'کاربر '..XXXTENTACIONGG..' یافت نشد ...!',  'html')
end
end
if (Black == 'kick' or Black == 'اخراج') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید کاربران دارای مقام را اخراج...!",'md')
else
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
send(msg.chat_id,msg.id,'✦ کاربر ['..name..'](tg://user?id='..user..')\nاز گروه اخراج شد...!','md')
KickUser(msg.chat_id,user)
UnRes(msg.chat_id,user)
base:incr(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id)
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
end
if (Black == 'clean blacklist' or Black == 'پاکسازی لیست سیاه') then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
cleanbanlist(msg)
send(msg.chat_id,msg.id,'✳ تمام ڪاربران محروم شده از لیست مسدود حذف شدند','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
if (Black == 'clean res' or Black == 'پاکسازی لیست محدود') and is_JoinChannel(msg) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
cleanmutelist(msg)
send(msg.chat_id,msg.id,'⭕ افراد محدود پاک شدند','md')
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end

if Black and (Black:match('^setflood max (%d+)$') or Black:match('^تنظیم حداکثر رگبار (%d+)$')) and is_JoinChannel(msg) then
local num = Black:match('^setflood max (%d+)') or Black:match('^تنظیم حداکثر رگبار (%d+)')
if tonumber(num) < 2 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *2* بکار ببرید','md')
else
base:set(TD_ID..'Flood:Max:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'✅ حداکثر پیام مکرر تنظیم شد به : *'..num..'*','md')
end
end
if Black and (Black:match('^force (%d+)$') or Black:match('^ادداجباری (%d+)$')) then
local num = Black:match('^force (%d+)') or Black:match('^ادداجباری (%d+)')
if tonumber(num) < 2 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *۲* بکار ببرید','md')
else
base:set(TD_ID..'Force:Max:'..msg.chat_id,num)
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'forceadd')
send(msg.chat_id,msg.id,'✅ حداکثر عضو تنظیم شد به : *'..num..'*','md')
end
end

if Black and (Black:match('^unforce$') or Black:match('^حذف ادداجباری$')) then
base:del(TD_ID..'Force:Max:'..msg.chat_id)
base:srem(TD_ID..'Gp2:'..msg.chat_id,'forceadd')
send(msg.chat_id,msg.id,'ادداجباری غیرفعال شد','md')
end

 if Black and (Black:match('^settimedelete (%d+)$') or Black:match('^زمان پاکسازی خودکار (%d+)$')) then
local num = Black:match('^settimedelete (%d+)') or Black:match('^زمان پاکسازی خودکار (%d+)')
if tonumber(num) < 10 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *10* بکار ببرید','md')
else
base:set(TD_ID..'Force:Time:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'⏰ زمان پاکسازے خودکار تنظیم شد به : *'..num..'*','md')
end
end
if Black and (Black:match('^forcepm (%d+)$') or Black:match('^تعداد اخطار افزودن اجباری (%d+)$')) then
local num = Black:match('^forcepm (%d+)') or Black:match('^تعداد اخطار افزودن اجباری (%d+)')
if tonumber(num) < 2 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *2* بکار ببرید','md') 
else
base:set(TD_ID..'Force:Pm:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'⏰ تعداد اخطار پیام افزودن اجبارے تنظیم شد به : *'..num..'* بار','md')
end
end

if Black and (Black:match('^setwarn (%d+)$') or Black:match('^تنظیم اخطار (%d+)$')) and is_JoinChannel(msg) then
local num = Black:match('^setwarn (%d+)') or Black:match('^تنظیم اخطار (%d+)')
if tonumber(num) < 2 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *2* بکار ببرید','md')
else
base:set(TD_ID..'Warn:Max:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'✅ حداکثر اخطار تنظیم شد به *'..num..'*','md')
end
end
if Black and (Black:match('^spam mode (%d+)$') or Black:match('^حساسیت هرزنامه (%d+)$')) and is_JoinChannel(msg) then
local num = Black:match('^spam mode (%d+)') or Black:match('^حساسیت هرزنامه (%d+)')
if tonumber(num) < 1 then
send(msg.chat_id,msg.id,'⭕ عددے بزرگتر از *1* بکار ببرید','md')
else
if tonumber(num) > 4096 then
send(msg.chat_id,msg.id,'⭕ عددے کوچڪتر از *4096* را بڪار ببرید','md')
else
base:set(TD_ID..'NUM_CH_MAX:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'✅ حساسیت به پیام هاے طولانے تنظیم شد به :*'..num..'*','md')
end
end
end

if Black and (Black:match('^setflood time (%d+)$') or Black:match('^تنظیم زمان رگبار (%d+)$')) and is_JoinChannel(msg) then
local num = Black:match('^setflood time (%d+)') or Black:match('^تنظیم زمان رگبار (%d+)')
if tonumber(num) < 1 then
send(msg.chat_id,msg.id,'⭕ زمان برسے باید بیشتر از *1* باشد','md')
else
base:set(TD_ID..'Flood:Time:'..msg.chat_id,num)
send(msg.chat_id,msg.id,'✅ زمان برسے تنظیم شد به : *'..num..'*','md')
end
end
----------------------------------------------
if (Black == 'welcome on' or Black == 'خوشامدگویی فعال') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'Welcomeon') then
send(msg.chat_id,msg.id,'⭕ خوش امدگویے #فعال بود' ,'md')
else
send(msg.chat_id,msg.id,'✅ خوش امدگویے #فعال شد' ,'md')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'Welcomeon')
end
end
if (Black == 'welcome off' or Black == 'خوشامدگویی غیرفعال') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'Welcomeon') then
send(msg.chat_id,msg.id,'✅خوش امدگویے غیر #فعال شد' ,'md')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'Welcomeon')
else
send(msg.chat_id,msg.id,'⭕خوش امدگویے #غیر فعال بود' ,'md')
end
end
----------------------------------------------
if Black == 'restart forceadd' or Black == 'شروع دوباره افزودن اجباری' then
allusers = base:smembers(TD_ID..'AllUsers:'..msg.chat_id)
base:del(TD_ID..'NewUser'..msg.chat_id)
for k, v in pairs(allusers) do
base:del(TD_ID..'addeduser'..msg.chat_id..v)
end
send(msg.chat_id,msg.id,'> افزودن اجباری ریستارت شد و تمامی افراد باید دوباره به مقدار مورد نظر کاربر به گروه اضافه کنند تا بتواند در گروه پیام دهد','md')
end
if Black == 'forceadd on' or Black == 'افزودن اجباری فعال' then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'force_NewUser') then
typeadd = '|↜ اد اجبارے بر روے کاربران جدید تنظیم شده است\n┄┄──┅┅══┅┅──┄┄\nشما میتوانید براے تغییر به همه کاربران با زدن دستور Setforce all user اد اجبارے را براے همه کاربران فعال کنید!'
else
typeadd = '|↜ اد اجبارے بر روے تمامی کاربران تنظیم شده است\n┄┄──┅┅══┅┅──┄┄\nدر صورت علاقه شما میتوانید اد اجبارے را با دستور Setforce new user بر روے کاربران جدید تنظیم کنید تا فقط کاربران جدید اجبار به اد شوند!'
end
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forceadd') then
send(msg.chat_id,msg.id,'⭕ قفل *افزودن اجبارے* #فعال بود\n─┅━━━━━━━┅─\n*وضعیت* : '..typeadd,'md')
else
send(msg.chat_id,msg.id,'✅ قفل *افزودن اجبارے* #فعال شد\nتعداد اخطار پیام افزودن : *'..Forcepm..'* بار\nتعداد افزودن : *'..Forcemax..'* نفر\n─┅━━━━━━━┅─\n*وضعیت* : '..typeadd,'md')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'forceadd')
end
end
if Black == 'forceadd off' or Black == 'افزودن اجباری غیرفعال' then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forceadd') then
send(msg.chat_id,msg.id,'• قفل *افزودن اجبارے* #غیرفعال شد' ,'md')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'forceadd')
base:del(TD_ID..'test:'..msg.chat_id)
base:del(TD_ID..'Force:Pm:'..msg.chat_id)
base:del(TD_ID..'Force:Max:'..msg.chat_id)
else
send(msg.chat_id,msg.id,'• قفل *افزودن اجبارے* #غیرفعال بود','md')
end
end
local CH = (base:get(TD_ID..'setch:'..msg.chat_id) or '..Channel..')
if Black == 'forcejoin on' or Black == 'جوین اجباری فعال' then
if base:get(TD_ID..'setch:'..msg.chat_id)  then
if  base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forcejoin') then
send(msg.chat_id,msg.id,'⭕ قفل جوین اجبارے #فعال بود\n✅ ڪانال جوین اجبارے :【@'..CH..'】','html')
else
send(msg.chat_id,msg.id,'✅ قفل جوین اجبارے #فعال شد\n[جهت عمل ڪرد عضویت اجبارے باید ربات زیر را در ڪانال خود ادمین ڪنید\n 🆔 : '..UserJoiner..']\n\n✅ ڪانال جوین اجبارے :【@'..CH..'】','html')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'forcejoin')
end
else
send(msg.chat_id,msg.id,'انجام نشد ✖️\nڪانال شما تنظیم نشده است ابتدا با دستور (تنظیم ڪانال channel ) یا (forcejoin channel ) ڪانال خود را تنظیم ڪنید سپس اقدام به فعال ڪردن جوین اجبارے ڪنید.','md')
end
end
if Black == 'forcejoin off' or Black == 'جوین اجباری غیرفعال' then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forcejoin') then
send(msg.chat_id,msg.id,'• قفل *جوین اجبارے* #غیرفعال شد','md')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'forcejoin')
else
send(msg.chat_id,msg.id,'• قفل *جوین اجبارے* #غیرفعال بود','md')
end
end
if Black and (Black:match('^forcejoin @(.*)') or Black:match('^عضویت اجباری @(.*)')) and is_JoinChannel(msg) then
local CH = Black:match('^forcejoin @(.*)') or Black:match('^عضویت اجباری @(.*)')
base:set(TD_ID..'setch:'..msg.chat_id,CH)
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'forcejoin')
send(msg.chat_id,msg.id,'✅ کانال تنظیم شد به : 【@'..CH..'】','html')
end
if Black and (Black:match('^unforcejoin$') or Black:match('^حذف عضویت اجباری')) and is_JoinChannel(msg) then
base:del(TD_ID..'setch:'..msg.chat_id)
base:srem(TD_ID..'Gp2:'..msg.chat_id,'forcejoin')
send(msg.chat_id,msg.id,'عضویت اجباری غیرفعال شد','html')
end
---------------set Lang------------
if (Black == 'lang en' or Black == 'زبان انگلیسی') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'diamondlang') then
send(msg.chat_id,msg.id,'♠ *Group Language already* #English ...!','md')
else
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'diamondlang')
send(msg.chat_id, msg.id, '♣ *Group Language set on* #English ...!','md')
end
end
if (Black == 'lang fa' or Black == 'زبان فارسی') and is_JoinChannel(msg) then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'diamondlang') then
base:srem(TD_ID..'Gp2:'..msg.chat_id,'diamondlang')
send(msg.chat_id,msg.id,'♦ زبان ربات تنظیم شد بر روے #فارسے ...!','md')
else
send(msg.chat_id,msg.id,'♥ زبان ربات هم اڪنون #فارسے است...!','md')
end
end
if Black1 and (Black1:match('^[Ss]etwelcome (.*)') or Black1:match('^تنظیم خوشامدگویی(.*)'))  and is_JoinChannel(msg)then
local wel = Black1:match('^[Ss]etwelcome (.*)') or Black1:match('^تنظیم خوشامدگویی (.*)')
base:set(TD_ID..'Text:Welcome:'..msg.chat_id,wel)
send(msg.chat_id,msg.id,'✅ پیام خوش امدگویے با موفقیت ثبت شد','md')
end
if Black1 and (Black1:match('^[Ss]etrules (.*)') or Black1:match('^تنظیم قوانین (.*)')) and is_JoinChannel(msg) then
local rules = Black1:match('^[Ss]etrules (.*)') or Black1:match('^تنظیم قوانین (.*)')
base:set(TD_ID..'Rules:'..msg.chat_id,rules)
send(msg.chat_id,msg.id,'✅ قوانین گروه با موفقیت ثبت شد','md')
end
if Black1 and (Black1:match('^[Dd]elrules$') or Black1:match('^حذف قوانین$')) and is_JoinChannel(msg) then
base:del(TD_ID..'Rules:'..msg.chat_id)
send(msg.chat_id,msg.id,'✅ قوانین گروه حذف شد.','md')
end
if (Black == "warn" or Black == "اخطار") and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید به کاربران داری مقام اخطار دهید...!",'md')
else
 local hashwarn = TD_ID..msg.chat_id..':warn'
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user) or 1
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if tonumber(warnhash) == tonumber(warn) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
KickUser(msg.chat_id,user)
UnRes(msg.chat_id,user)
text = '['..name..'](tg://user?id='..user..')\nا┅┅──┄┄═✺═┄┄──┅┅\nبه علت دریافت اخطار بیش از حد اخراج شد \nاخطار ها : '..warnhash..'/'..warn..''
base:hdel(hashwarn,user, '0')
send(msg.chat_id,msg.id,text,'md')
else
send(msg.chat_id,msg.id,'✖️اخطار های ['..name..'](tg://user?id='..user..') به حداکثر رسیده ولی ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید تا توانایی اخراج داشته باشد !','md')
end
else
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user) or 1
 base:hset(hashwarn,user, tonumber(warnhash) + 1)
text = '['..name..'](tg://user?id='..user..')\nا┅┅──┄┄═✺═┄┄──┅┅\nشما یک اخطار دریافت کردید \nتعداد اخطار هاے شما : '..warnhash..'/'..warn..''
send(msg.chat_id,msg.id,text,'md')
end
end
end
end

if Diamondent and (Black:match('^warn (.*)') or Black:match('^اخطار (.*)')) or Black and (Black:match('^warn @(.*)') or Black:match('^اخطار @(.*)') or Black:match('^warn (%d+)$') or Black:match('^اخطار (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^warn (.*)') or Black:match('^اخطار (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^warn @(.*)') or Black:match('^اخطار @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^warn (%d+)') or Black:match('^اخطار (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^warn (.*)') or Black:match('^اخطار (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if VipUser(msg,mrr619) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nشما نمیتوانید به کاربران داری مقام اخطار دهید...!",'md')
else
local hashwarn = TD_ID..msg.chat_id..':warn'
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',mrr619) or 1
if tonumber(warnhash) == tonumber(warn) then
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
KickUser(msg.chat_id,mrr619)
UnRes(msg.chat_id,mrr619)
text = '['..BDSource..'](tg://user?id='..mrr619..')\nا┅┅──┄┄═✺═┄┄──┅┅\nبه علت دریافت اخطار بیش از حد اخراج شد \nاخطار ها : '..warnhash..'/'..warn..''
base:hdel(hashwarn,mrr619, '0')
send(msg.chat_id,msg.id,text,'md')
else
send(msg.chat_id,msg.id,'✖️اخطار های ['..BDSource..'](tg://user?id='..mrr619..') به حداکثر رسیده ولی ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید تا توانایی اخراج داشته باشد !','md')
end
else
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',mrr619) or 1
base:hset(hashwarn,mrr619, tonumber(warnhash) + 1)
text = '['..BDSource..'](tg://user?id='..mrr619..')\nا┅┅──┄┄═✺═┄┄──┅┅\nشما یک اخطار دریافت کردید \nتعداد اخطار هاے شما : '..warnhash..'/'..warn..''
send(msg.chat_id,msg.id,text,'md')
end end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'html')
--end
end
end
if Diamondent and (Black:match('^unwarn (.*)') or Black:match('^حذف اخطار (.*)')) or Black and (Black:match('^unwarn @(.*)') or Black:match('^حذف اخطار @(.*)') or Black:match('^unwarn (%d+)$') or Black:match('^حذف اخطار (%d+)$')) and is_JoinChannel(msg) then
local BDSource = Black:match('^unwarn (.*)') or Black:match('^حذف اخطار (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^unwarn @(.*)') or Black:match('^حذف اخطار @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^unwarn (%d+)') or Black:match('^حذف اخطار (%d+)') then
mrr619 = BDSource
elseif Diamondent and Black:match('^unwarn (.*)') or Black:match('^حذف اخطار (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',mrr619) or 1
if tonumber(warnhash) == tonumber(1) then
text = '✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nهیچ اخطارے ندارد'
send(msg.chat_id,msg.id,text,'md')
else
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',mrr619)
local hashwarn = TD_ID..msg.chat_id..':warn'
base:hdel(hashwarn,mrr619,'0')
text = '✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nتمام اخطار هایش پاک شد'
send(msg.chat_id,msg.id,text,'md')
end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'html')
end
end
if (Black == "unwarn" or Black == "حذف اخطار") and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user) or 1
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if tonumber(warnhash) == tonumber(1) then
text = '✦ کاربر : ['..name..'](tg://user?id='..user..')\nهیچ اخطارے ندارد'
send(msg.chat_id,msg.id,text,'md')
else
local warnhash = base:hget(TD_ID..msg.chat_id..':warn',user)
local hashwarn = TD_ID..msg.chat_id..':warn'
base:hdel(hashwarn,user,'0')
text = '✦ کاربر : ['..name..'](tg://user?id='..user..')\nتمام اخطار هایش پاک شد'
send(msg.chat_id,msg.id,text,'md')
end
end
end
msgsdiamond = {} 
msgsdiamondtd = {} 
adddiamond = {} 
function ForStart(msg,tables,status) 
local list = base:smembers(TD_ID..'AllUsers:'..msg.chat_id) 
 for k,v in pairs(list) do 
 if tonumber(v) ~= tonumber(Config.BotJoiner) then
  GetStatus = tonumber(base:get(TD_ID..status..v)) 
  if base:get(TD_ID..status..v) then 
   table.insert(tables,GetStatus) 
  end 
 end 
end 
end
function ForSort(msg,tables,text,status) 
 table.sort(tables) 
 GpStatus = tonumber(base:get(TD_ID.."Total:"..status..":"..msg.chat_id) or 0) 
 Text = Text..'*'..text..'* '..GpStatus..'\n' 
end 
function ForNumber(msg,tables,text, status,t2)
 list = base:smembers(TD_ID.."AllUsers:"..msg.chat_id) 
 for k,v in ipairs(tables) do 
  Number = v 
 end 
 for k,U in pairs(list) do 
  GetStatus = tonumber(base:get(TD_ID..status..U)) 
  if GetStatus == Number then 
 if base:get(TD_ID..status..msg.sender_id.user_id) and Number then 
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..U)
if res ~= 200 then
end
local diamond = TD.getUser(U)
  if #tables == 0 then 
   Text = Text 
  else
   Text = Text..'*'..text..'* '..Number..' *'..t2..'* > ['..diamond.first_name..'](tg://user?id='..U..')\n' 
  table.remove(tables, getindex(tables, tonumber(Number))) 
  end 
  end
  end 
 end 
end 

function StatusGp(msg,chat_id) 
 Emoji = {"↫ ","⇜ ","⌯ ","↜ "} 
 Source_Start = Emoji[math.random(#Emoji)] 
 Text = '*🎗 آمار گروه شما در ساعت* '..os.date("%H:%M:%S")..'\nا┅┅──┄┄═✺═┄┄──┅┅\n'
 ForStart(msg, msgsdiamond,"Total:messages:"..chat_id..":") 
 ForSort(msg, msgsdiamond, "🔱 تعد پیام های گروه :", "messages") 
 if #msgsdiamond >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد پیام 👑*\n' 
 end 
 ForNumber(msg, msgsdiamond, "• نفر اول 🎖 :","Total:messages:"..chat_id..":", "پیام") 
 ForNumber(msg, msgsdiamond, "• نفر دوم‌ 🥈 :","Total:messages:"..chat_id..":", "پیام") 
 ForNumber(msg, msgsdiamond, "• نفر سوم 🥉 :","Total:messages:"..chat_id..":", "پیام") 
 ForStart(msg, msgsdiamondtd,"Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":") 
 table.sort(msgsdiamondtd) 
 if #msgsdiamondtd >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد پیام های امروز 👑*\n' 
 end 
 ForNumber(msg, msgsdiamondtd, "• نفر اول 🎖 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForNumber(msg, msgsdiamondtd, "• نفر دوم‌ 🥈 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForNumber(msg, msgsdiamondtd, "• نفر سوم 🥉 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForStart(msg, adddiamond,"Total:AddUser:"..chat_id..":") 
 table.sort(adddiamond) 
 if #adddiamond >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد اد 👑*\n' 
 end 
 ForNumber(msg, adddiamond, "• نفر اول 🎖 :","Total:AddUser:"..chat_id..":", "نفر") 
 ForNumber(msg, adddiamond, "• نفر دوم‌ 🥈 :","Total:AddUser:"..chat_id..":", "نفر") 
 ForNumber(msg, adddiamond, "• نفر سوم 🥉 :","Total:AddUser:"..chat_id..":", "نفر") 
send(msg.chat_id,msg.id,Text,'md')
end
if Black == 'آمار گروه' or Black == 'امار گروه' and is_JoinChannel(msg) then
StatusGp(msg,msg.chat_id) 
end

if Black and (Black:match("^[Ss][Tt][Aa][Tt][Ss] (%d+)$") or Black:match("^امار (%d+) نفر$") or Black:match("^آمار (%d+) نفر$")) then
local count = Black:match("^[Ss][Tt][Aa][Tt][Ss] (%d+)$") or Black:match("^امار (%d+) نفر$") or Black:match("^آمار (%d+) نفر$")
if 1 > tonumber(count) or tonumber(count) > 50 then
TD.sendText(msg.chat_id, msg.id, "⌯ تعداد نفرات وارد شده باید بزرگتر از 1 و کوچک تر از 50 باشد ▸", "md")
else
GroupStats(msg.chat_id,msg.id,count)
end
end

if Black == 'groupinfo' or Black == 'اطلاعات گروه' then
local Diamond = TD.getSupergroupFullInfo(msg.chat_id)
join = base:get(TD_ID..'Total:JoinedByLink:'..msg.chat_id) or 0
local links = ''..check_markdown(Diamond.invite_link.invite_link)..'' or nil
local data = TD.getChat(msg.chat_id)
send(msg.chat_id,msg.id,'اطلاعات گروه : \nا┅┅──┄┄═❂═┄┄──┅┅\n|↜نام گروه : *'..data.title..'*\n|↜شناسه گروه : *'..msg.chat_id..'*\n|↜تعداد ادمین هاے گروه : *'..Diamond.administrator_count..'*\n|↜تعداد مسدودے هاے گروه : *'..Diamond.banned_count..'*\n|↜تعداد اعضاے گروه : *'..Diamond.member_count..'*\n|↜تعداد اعضاے وارد شده با لینک : *'..join..'*\n|↜لینک گروه : '..links..'\n|↜تعداد کاربران محدود شده : *'..Diamond.restricted_count..'*\n|↜درباره گروه : '..Diamond.description..'','md')
end
----------------------------------------------
if (Black == 'rules' or Black == 'قوانین') and is_JoinChannel(msg) then
local rules = base:get(TD_ID..'Rules:'..msg.chat_id) or '|↜ قوانینے براے گروه ثبت نشده است'
send(msg.chat_id,msg.id,'⭕ قوانین گروه :\n'..rules..'','html')
end
----------------------------------------------
end
end
end
----------------------------------------------
if Black and ((is_Sudo(msg) and not (base:sismember(TD_ID..'PnlSudo:',Black) or base:sismember(TD_ID..'PnlSudo:',BaseCmd) or base:sismember(TD_ID..'PnlSudo_2:',msg.sender_id.user_id..':'..Black) or base:sismember(TD_ID..'PnlSudo_2:',msg.sender_id.user_id..':'..BaseCmd))) or is_FullSudo(msg)) then
if (Black == 'autoleave on' or Black == 'لفت خودکار روشن') then
base:del(TD_ID..'AutoLeave')
send(msg.chat_id,msg.id,'done','html')
end
if (Black == 'autoleave off' or Black == 'لفت خودکار خاموش') then
base:set(TD_ID..'AutoLeave',true)
send(msg.chat_id,msg.id,'done','html')
end
if (Black == 'speedtest' or Black == 'تست سرعت') then
text = io.popen("speedtest-cli"):read('*all')
send(msg.chat_id,msg.id,text,'html')
end
---------------------------------------------
if (Black == 'joinchannel off' or Black == 'جوین چنل خاموش') then
base:del(TD_ID..'joinchnl')
send(msg.chat_id, msg.id, '✦ جوین چنل خاموش شد و دیگر کاربران براے استفاده از دستورات نیازے به ورود به کانال ربات نخواهند داشت!','md')
end
if (Black == 'joinchannel on' or Black == 'جوین چنل روشن') then
base:set(TD_ID..'joinchnl',true)
send(msg.chat_id,msg.id, '✦ جوین چنل روشن شد و کاربران براے استفاده از دستورات ربات باید ابتدا در کانال ربات عضو شوند!','md')
end
----------------------------------------------
if Black and (Black:match('^setmonshi (.*)') or Black:match('^تنظیم منشی (.*)')) then
local monshi = Black:match('^setmonshi (.*)') or Black:match('^تنظیم منشی (.*)')
base:set(TD_ID..'monshi',monshi)
send(msg.chat_id, msg.id, 'متن منشے تنظیم شد بر روے :\n'..monshi..'', 'html')
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
local result = TD.getChat(chat_id)
local res = TD.getUser(msg.sender_id.user_id)
if res.first_name == '' then name = res.id else name = ec_name(res.first_name) end
send(chat_id,0,"✅ انجام شد\n✦ توسط : ["..name.."](tg://user?id="..msg.sender_id.user_id..")\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n💢 ربات از گروه با مشخصات زیر :\n📝 نام گروه : "..(result.title or "-").."\n🆔 ایدے گروه : "..chat_id.."\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nخارج شد",'md')
send(Sudoid,0,"✅ انجام شد\n✦ توسط : ["..name.."](tg://user?id="..msg.sender_id.user_id..")\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n💢 ربات از گروه با مشخصات زیر :\n📝 نام گروه : "..(result.title or "-").."\n🆔 ایدے گروه : "..chat_id.."\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nخارج شد",'md')
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

if Black and (Black:match('^leave_(%d+)$')) then
local chtt = Black:match('^leave_(.*)$')
local chat_idd = string.gsub(chtt,'_','')
local chat_id = '-'..chat_idd
TD.vardump(chat_id)
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
if res.first_name == '' then name = res.id
else name = ec_name(res.first_name) end
send(chat_id,0,"✅ انجام شد\n✦ توسط : ["..name.."](tg://user?id="..msg.sender_id.user_id..")\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n💢 ربات از گروه با مشخصات زیر :\n📝 نام گروه : "..(result.title or "-").."\n🆔 ایدے گروه : "..chat_id.."\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nخارج شد",'md')
send(Sudoid,0,"✅ انجام شد\n✦ توسط : ["..name.."](tg://user?id="..msg.sender_id.user_id..")\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n💢 ربات از گروه با مشخصات زیر :\n📝 نام گروه : "..(result.title or "-").."\n🆔 ایدے گروه : "..chat_id.."\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\nخارج شد",'md')
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

if Black and (Black:match('^link_(%d+)$')) then
local chtt = Black:match('^link_(.*)$')
local chat_idd = string.gsub(chtt,'_','')
local chat_id = '-'..chat_idd
TD.vardump(chat_id)
local Hash = TD_ID..'StatsGpByName'..chat_id
local dlink = TD.getSupergroupFullInfo(chat_id)
local xlink = dlink and dlink.invite_link.invite_link or 0
send(msg.chat_id,msg.id,''..xlink..'','html')
end

if (Black == 'chats' or Black == 'لیست گروه ها') then
local list = base:smembers(TD_ID..'group:')
if #list == 0 then
send(msg.chat_id,msg.id,'لیست گروهها خالی میباشد !','md')
else
local t = 'لیست گروه هاے مدیریتے ربات:\n﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n'
for k,v in pairs(list) do
local GroupsName = TD.getChat(v).title or ''
local data = TD.getSupergroupFullInfo(v)
local results = TD.getSupergroupMembers(v,'Administrators','',0,200)
if results and results.members then
for i,s in pairs(results.members) do
if s.status._ == "chatMemberStatusCreator" then
local diamond = TD.getUser(s.member_id.user_id)
if diamond.first_name == '' then
name = diamond.id 
else name = diamond.first_name end
local datar = TD.getChatMember(v,Config.BotJoiner)
if (datar and datar._ == "chatMember" and datar.status._ == 'chatMemberStatusAdministrator' and datar.rights.status.can_restrict_members == true) then
t = t..k.."-💢\n◄ نام گروه :\n "..GroupsName.."\n◄ مالک گروه :\n"..name.."\n◄ تعداد ادمین :\n "..(data.administrator_count or '--').."\n◄ تعداد اعضا گروه :\n "..(data.member_count or '--').."\n\n► Link_"..string.gsub(v,'-','').."\n► Leave_"..string.gsub(v,'-','').."\n─┅━━━━━━━┅─\n"
end
end
end
end
end
local file = io.open("./libs/data/Gplist-"..TD_ID..".txt","w")
file:write(t)
file:close()
sendDocument(msg.chat_id,msg.id,'./libs/data/Gplist-'..TD_ID..'.txt','','md')
end end 
if Black == 'backup' or Black == 'بک اپ' then
sendDocument(msg.chat_id,msg.id,0,1,nil,'/var/lib/redis/dump.rdb', '', dl_cb, nil)
end
if (Black == 'rld') then
send(msg.chat_id,msg.id,'#ok','md')
dofile('Bot.lua')
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
if Black == 'monshi on' or Black == 'منشی فعال' then
base:set(TD_ID..'MonShi:on',true)
send(msg.chat_id, msg.id, 'منشے #فعال شد','md')
end
if Black == 'monshi off' or Black == 'منشے غیرفعال' then
base:del(TD_ID..'MonShi:on')
send(msg.chat_id, msg.id, 'منشے #غیرفعال شد','md')
end
if Black == 'pmresan on' or Black == 'منشی فعال' then
base:del(TD_ID..'pmresan:on')
send(msg.chat_id, msg.id, 'پی ام رسانی روشن شد !','md')
end
if Black == 'pmresan off' or Black == 'منشے غیرفعال' then
base:set(TD_ID..'pmresan:on',true)
send(msg.chat_id, msg.id, 'پی ام رسانی خاموش شد !','md')
end
if (Black == 'delcmds on' or Black == 'پاکسازی دستور روشن') then
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'delcmd') then
send(msg.chat_id, msg.id, '>پاکسازی دستورات روشن شد...!\nاز این پس دستورات ارسالی شما پاک خواهند شد!','md')
base:sadd(TD_ID..'Gp2:'..msg.chat_id,'delcmd')
else
send(msg.chat_id, msg.id, '>پاکسازی دستورات از قبل روشن بود...!','md')
end
end
if (Black == 'delcmds off' or Black == 'پاکسازی دستور خاموش') then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'delcmd') then
send(msg.chat_id, msg.id, '>پاکسازی دستورات خاموش شد...!','md')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'delcmd')
else
send(msg.chat_id, msg.id, '>پاکسازی دستورات خاموش بود...!','md')
end
end
----------------------------------------------
if (Black == 'resetstats' or Black == 'ریستارت امار') then
base:del(TD_ID..'SuperGp')
--base:del(TD_ID..'Chat:Normal')
--base:del(TD_ID..'ChatPrivite')
send(msg.chat_id,msg.id,'✅انجام شد','md')
end
if Black == 'resetch' then 
base:srem(TD_ID..'Gp2:'..msg.chat_id,'chex2')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'chex3')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'chex1')
end
if Black and (Black:match('^banall (%d+)$') or Black:match('^بن ال (%d+)$')) then
local user = Black:match('^banall (%d+)') or Black:match('^بن ال (%d+)')
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember(TD_ID..'GlobalyBanned:',user) then
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..user..'\nدر لیست بن ال وجود دارد','md')
end
else
if name then
send(msg.chat_id,msg.id,'✥ کاربر : \n🆔 : '..nm..'\nبه لیست بن ال افزوده شد','html')
else
send(msg.chat_id,msg.id,'✥ کاربر : \n🆔 : `'..user..'`\n_به لیست بن ال افزوده شد_','md')
end
base:sadd(TD_ID..'GlobalyBanned:',user)
end
end
if Black and (Black:match('^banall @(.*)') or Black:match('^بن ال @(.*)')) then
local username = Black:match('^banall @(.*)') or Black:match('^بن ال @(.*)')
local Diamond = TD.searchPublicChat(username)
if Diamond.id then
if VipUser(msg,Diamond.id) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
local nm = '<a href="tg://user?id='..Diamond.id..'">'..username..'</a>'
if base:sismember(TD_ID..'GlobalyBanned:', Diamond.id) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nبه لــیــسـت گلوبال افزوده شد','html')
base:sadd(TD_ID..'GlobalyBanned:',Diamond.id)
end
else 
send(msg.chat_id, msg.id,'❎ کاربر یافت نشد','html')
end
end
if (Black == 'gbans' or Black == 'لیست بن ال') then 
local list = base:smembers(TD_ID..'GlobalyBanned:') 
if #list == 0 then 
send(msg.chat_id,msg.id,'لیست کاربران #گلوبال خالے میباشد','md')
else
local t = 'لیست کاربران بن ال:\nا┅┅──┄┄═✺═┄┄──┅┅\n' 
for k,v in pairs(list) do 
t = t..k..'-【'..v..'】\n\n'
end
local file = io.open("./libs/data/Gbans-"..TD_ID..".txt","w")
file:write(t)
file:close()
sendDocument(msg.chat_id,msg.id,'./libs/data/Gbans-'..TD_ID..'.txt','','md')
end end
if (Black == 'clean gbans' or Black == 'پاکسازی لیست بن ال') then
base:del(TD_ID..'GlobalyBanned:')
send(msg.chat_id, msg.id,'⭕ لیست بن ال پاکسازے شد','md')
end
---------------Unbanall--------------
if Black and (Black:match('^unbanall (%d+)$') or Black:match('^حذف بن ال (%d+)$')) then
local user = Black:match('unbanall (%d+)') or Black:match('حذف بن ال (%d+)')
if base:sismember(TD_ID..'GlobalyBanned:',user) then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال حذف شد', 'html')
else
send(msg.chat_id, msg.id,'✦ کاربر : \n🆔 : `'..user..'`\n_از لیست بن ال حذف شد_','md') 
end
base:srem(TD_ID..'GlobalyBanned:',user)
else
if name then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود ندارد','html')
else
send(msg.chat_id, msg.id,'✦ کاربر : \n🆔 : '..user..'\nدر لیست بن ال وجود ندارد','md') 
end
end
end
if Black and (Black:match('^unbanall @(.*)') or Black:match('^حذف بن ال @(.*)')) then
local username = Black:match('^unbanall @(.*)') or Black:match('^حذف بن ال @(.*)')
local Diamond = TD.searchPublicChat(username)
if Diamond.id then
local nm = '<a href="tg://user?id='..Diamond.id..'">'..username..'</a>'
if base:sismember(TD_ID..'GlobalyBanned:',Diamond.id) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال حذف شد', 'html')
base:srem(TD_ID..'GlobalyBanned:',Diamond.id)
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود ندارد','html')
end
else
send(msg.chat_id, msg.id,'❎ کاربر یافت نشد','md')
end
end
-------
if is_supergroup(msg) then

if (Black == 'leave' or Black == 'خروج') then
send(msg.chat_id,msg.id,"⭕ ربات از گروه خارج شد...!",'md')
base:srem(TD_ID..'Gp2:'..msg.chat_id,'added') 
TD.leaveChat(msg.chat_id)
--Leave_api(msg.chat_id)
end
if (Black == 'ownerlist' or Black == 'لیست مالکان') then
local list = base:smembers(TD_ID..'OwnerList:'..msg.chat_id)
local t = '🚬لیست مالک هاے ربات در گروه\nا┅┅──┄┄═✺═┄┄──┅┅\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = 'لیست مالکان گروه خالے میباشد'
end
send(msg.chat_id,msg.id,t,'md')
end
if (Black == 'clean allmsgs on') then
send(msg.chat_id, msg.id,  '> اغاز فرایند پاکسازی پیام های شمارش شده ...!','md')
base:set(TD_ID.."cleanmsgs",true)
end
if Black == 'clean allmsgs off' then
send(msg.chat_id, msg.id,  '> اتمام فرایند پاکسازی پیام های شمارش شده ...!','md')
base:del(TD_ID.."cleanmsgs")
end
--<><><><>RemOwner
if Diamondent and (Black:match('^remowner (.*)') or Black:match('^حذف مالک (.*)') or Black:match('^عزل مالک (.*)') or Black:match('^خلع مالک (.*)') or Black:match('^تنزیل مالک (.*)')) or Black and (Black:match('^remowner @(.*)') or Black:match('^حذف مالک @(.*)') or Black:match('^عزل مالک @(.*)') or Black:match('^تنزیل مالک @(.*)') or Black:match('^خلع مالک @(.*)') or Black:match('^remowner (%d+)$') or Black:match('^حذف مالک (%d+)$') or Black:match('^عزل مالک (%d+)$') or Black:match('^خلع مالک (%d+)$') or Black:match('^تنزیل مالک (%d+)$')) then
local BDSource = Black:match('^remowner (.*)') or Black:match('^حذف مالک (.*)') or Black:match('^عزل مالک (.*)') or Black:match('^خلع مالک (.*)') or Black:match('^تنزیل مالک (.*)')
local Diamond = TD.searchPublicChat(BDSource)
if not Diamondent and Black:match('^remowner @(.*)') or Black:match('^حذف مالک @(.*)') or Black:match('^عزل مالک @(.*)') or Black:match('^خلع مالک @(.*)') or Black:match('^تنزیل مالک @(.*)') then
mrr619 = Diamond.id
elseif not Diamondent and Black:match('^remowner (%d+)$') or Black:match('^حذف مالک (%d+)$') or Black:match('^عزل مالک (%d+)$') or Black:match('^خلع مالک (%d+)$') or Black:match('^تنزیل مالک (%d+)$') then
mrr619 = BDSource
elseif Diamondent and Black:match('^remowner (.*)') or Black:match('^حذف مالک (.*)') or Black:match('^عزل مالک (.*)') or Black:match('^خلع مالک (.*)') or Black:match('^تنزیل مالک (.*)') then
mrr619 = msg.content.text.entities[1].type.user_id
end
if mrr619 then
if not base:sismember(TD_ID..'OwnerList:'..msg.chat_id,mrr619) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nدر لیست مالکان ربات قرار ندارد !','md')
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..BDSource..'](tg://user?id='..mrr619..')\nاز لیست مالکان ربات در گروه حذف شد','md')
base:srem(TD_ID..'OwnerList:'..msg.chat_id,mrr619)
end
else
send(msg.chat_id, msg.id,'کاربر '..BDSource..' یافت نشد ...!',  'html')
end
end
if (Black == 'remowner' or Black == 'عزل مالک' or Black == 'خلع مالک' or Black == 'تنزیل مالک' or Black == 'حذف مالک') and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if base:sismember(TD_ID..'OwnerList:'..msg.chat_id,user) then
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nاز لیست مالکان ربات در گروه حذف شد','md')
base:srem(TD_ID..'OwnerList:'..msg.chat_id,user)
else
send(msg.chat_id,msg.id,'✦ کاربر : ['..name..'](tg://user?id='..user..')\nدر لیست مالکان ربات قرار ندارد !','md')
end
end
end
if Black == 'clean ownerlist' or Black  == 'پاکسازی لیست مالکان' then
base:del(TD_ID..'OwnerList:'..msg.chat_id)
send(msg.chat_id,msg.id,'⭕ لیست مالکان ربات در گروه پاکسازے شد','md')
end
-------------Globaly Banned--------------
if (Black == 'banall' or Black == 'بن ال') and tonumber(reply_id) ~= 0  then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if VipUser(msg,user) then
send(msg.chat_id,msg.id,"❌ #اخطار  !\nا─┅━━━━━━━┅─\nکاربر دارای مقام است",'md')
return false
end
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember(TD_ID..'GlobalyBanned:',user) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود دارد','html')
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nبه لیست بن ال افزوده شد','html')
base:sadd(TD_ID..'GlobalyBanned:',user)
end
end
end
if (Black == 'unbanall' or Black == 'حذف بن ال') and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local nm = '<a href="tg://user?id='..user..'">'..name..'</a>'
if base:sismember(TD_ID..'GlobalyBanned:',user) then
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nاز لیست بن ال حذف شد','html')
base:srem(TD_ID..'GlobalyBanned:',user)
else
send(msg.chat_id,msg.id,'✦ کاربر : \n🆔 : '..nm..'\nدر لیست بن ال وجود ندارد','html')
end
end
end
if (Black == 'kickall' or Black == 'اخراج همه') then 
local url_  = https.request(Bot_Api .. '/getChatMember?chat_id='..msg.chat_id..'&user_id='..BotCliId)
if res ~= 200 then
end
statsurl_ = json:decode(url_)
if statsurl_.ok == true and statsurl_.result.status == 'administrator' and statsurl_.result.can_restrict_members == true then
local data = TD.getSupergroupMembers(msg.chat_id, "Recent", '' , 0 , 200 )
for k, v in pairs(data.members) do 
if tonumber(v.member_id.user_id) ~= tonumber(Sudoid) then
KickUser(msg.chat_id,v.member_id.user_id)
end
end
send(msg.chat_id,msg.id,'• انجام شد\nهمه ممبر ها اخراج شدند','md') 
else
send(msg.chat_id,msg.id,'✖️ ربات به قسمت محرومیت کاربران  دسترسی ندارد !\n❗️لطفا از تنظیمات گروه این قابلیت را برای ربات فعال کنید سپس مجدد تلاش کنید !','md')
end
end
end
end

if is_supergroup(msg) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then
if (Black == 'asl' or Black == 'اصل') and tonumber(reply_id) ~= 0  and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local diamond = TD.getUser(Diamond.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
x = base:get(TD_ID..'setasl:'..Diamond.sender_id.user_id) 
if x then
send(msg.chat_id,msg.id,"مشخصات ["..name.."](tg://user?id="..Diamond.sender_id.user_id..") :\n"..x.."","md")
else 
end
end
end
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') and is_supergroup(msg) and is_Mod(msg) then
if (Black == 'addcli' or Black == 'ورود ربات پاکسازی') then
send(msg.chat_id,0,'لطفا لینک گروه خود را جهت ورود ربات پاکسازی ارسال کنید','md')
base:setex(TD_ID..'Vorod'..msg.chat_id..msg.sender_id.user_id,90,true)
end end
-------fun------ 
----شخصی 
if (not base:sismember(TD_ID..'Gp:'..msg.chat_id,'Cmd') or VipUser(msg,msg.sender_id.user_id)) and is_supergroup(msg) and base:sismember(TD_ID..'Gp2:'..msg.chat_id,'added') then
------Bot Chat-----
if not base:sismember(TD_ID..'Gp2:'..msg.chat_id,'BotChat') then
if Black and base:sismember(TD_ID..'Stickerslist:'..msg.chat_id,Black) then
local sticker = base:get(TD_ID..'Stickers:'..Black..''..msg.chat_id)
TD.sendSticker(msg.chat_id,msg.id,sticker)
end
if Black and base:sismember(TD_ID..'Textlist:'..msg.chat_id,Black) then
local text = base:hget(TD_ID..'Text:'..msg.chat_id,Black)
send(msg.chat_id,msg.id,text,'html')
end
if Black and (Black:match('^ki (.*)$') or Black:match('^کی (.*)$')) and is_JoinChannel(msg) then
bd = Black:match('^ki (.*)$') or Black:match('^کی (.*)$')
local data = TD.getSupergroupMembers(msg.chat_id, "Recent", '' , 0 , 200 )
local rand = math.random(#data.members)
local diamond = TD.getUser(data.members[rand].member_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
nm = '<a href="tg://user?id='..data.members[rand].member_id.user_id..'">'..name..'</a>'
mod = {'فکرکنم','احتمالا','بخدا','بنظرم','من که میگم','ناموصا'}
mods = mod[math.random(#mod)]
send(msg.chat_id,0,''..mods..' '..(nm)..' '..bd..'','html')
end
if (Black == 'ربات') and is_JoinChannel(msg) then
if tonumber(msg.sender_id.user_id) and VipUser_(msg,msg.sender_id.user_id) then
xtx = {'درخدمتم','جوونم','توفقط صدام کن'}
send(msg.chat_id,msg.id,xtx[math.random(#xtx)],'md')
else
local Bot = base:get(TD_ID..'rank'..msg.chat_id..msg.sender_id.user_id)
if Bot then
local rankpro = {'جونم '..Bot..'','تف تو کلات '..Bot..'','بگو '..Bot..'','بلے '..Bot..'','درد '..Bot..'','باهات قهرم '..Bot..' صدام نزن 😒',''..Bot..'😡😡',''..Bot..'😍😍','جووووون تو فقط صدام کن '..Bot..'😍','بنال ببینم چته '..Bot..'','الله واکبر چیه '..Bot..''}
send(msg.chat_id, msg.id,rankpro[math.random(#rankpro)],'md')
else
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then
name = diamond.id
else
name = ec_name(diamond.first_name)
end
if ec_name(diamond.first_name) == '' then
frname = ec_name(diamond.last_name)
else
frname = ec_name(diamond.first_name)
end
local rrr ={name,frname,'['..name..'](tg://user?id='..msg.sender_id.user_id..')','['..frname..'](tg://user?id='..msg.sender_id.user_id..')'}
local rank = {rrr[math.random(#rrr)]..' باز شروع شد؟','الله اکبر','تف تو کلات '..rrr[math.random(#rrr)],'بگو '..rrr[math.random(#rrr)],'جون🙁 '..rrr[math.random(#rrr)],'مرگ😡','ربات و ... 😡 الله اکبر','جووووون تو فقط صدام کن '..rrr[math.random(#rrr)],'از تو دیگه بعید بود '..rrr[math.random(#rrr)],'بنال ببینم چته '..rrr[math.random(#rrr)],'یبار دیگه صدام کنی ...😡','جون 🙁','چیه رع','هان؟ '..rrr[math.random(#rrr)],'جونه دلم '..rrr[math.random(#rrr)],'بله عزیزم','ای درد و ربات','ای ربات و مرگ','الله و اکبر'}
send(msg.chat_id, msg.id,rank[math.random(#rank)],'md')
end
end
end 
end
if Diamondent and (Black:match('^id (.*)') or Black:match('^آیدی (.*)') or Black:match('^ایدی (.*)')) and is_JoinChannel(msg) then
local result = TD.getUser(msg.content.text.entities[1].type.user_id)
if result.id then
send(msg.chat_id,msg.id,'['..result.id..'](tg://user?id='..result.id..')','md')
end
end
if Black and (Black:match('^id @(.*)') or Black:match('^ایدی @(.*)')) and is_JoinChannel(msg) then
local username = Black:match('^id @(.*)') or Black:match('^ایدی @(.*)')
local data = TD.searchPublicChat(username)
if data.id then
send(msg.chat_id,msg.id,'['..data.id..'](tg://user?id='..data.id..')','md')
else
send(msg.chat_id,msg.id,"✦ کاربر : @"..check_markdown(username).." _یافت نشد _!",'md')
end
end
if (Black == "id" or Black == "ایدی" or Black == "آیدی") and tonumber(reply_id) ~= 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
send(msg.chat_id,msg.id,'['..user..'](tg://user?id='..user..')','md')
end
end
if (Black == 'setasl' or Black == 'تنظیم اصل') and tonumber(reply_id) ~= 0  and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local diamond = TD.getUser(Diamond.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if Diamond.content._ == 'messageText' then
base:set(TD_ID..'setasl:'..Diamond.sender_id.user_id,Diamond.content.text.text)
send(msg.chat_id,msg.id,"مشخصات کاربر ["..name.."](tg://user?id="..Diamond.sender_id.user_id..") تنظیم شد","md")
end
end
if (Black == 'delasl' or Black == 'حذف اصل') and tonumber(reply_id) ~= 0  and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local diamond = TD.getUser(Diamond.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
base:del(TD_ID..'setasl:'..Diamond.sender_id.user_id)
send(msg.chat_id,msg.id,"مشخصات کاربر ["..name.."](tg://user?id="..Diamond.sender_id.user_id..") حذف شد","md")
end
if Black and (Black:match('^getpro (%d+)$') or Black:match('^ پروفایل (%d+)$')) then
local offset = tonumber(Black:match('^getpro (%d+)'))
or tonumber(Black:match('^ پروفایل (%d+)'))
if offset > 50 then
send(msg.chat_id,msg.id,'اشتباه زدے داداچ\n من بیشتر از 50 عکس پروفایل شما را نمیتوانم ارسال کنم ❎','md')
elseif offset < 1 then
send(msg.chat_id,msg.id,'لطفا عددے بزرگتر از 0 بکار ببرید⭕','md')
else
local result = TD.getUserProfilePhotos(msg.sender_id.user_id,offset,1)
if result.photos[1] then
TD.sendPhoto(msg.chat_id,msg.id,result.photos[1].sizes[1].photo.id,'» تعداد پروفایل : 【'..offset..'/'..result.total_count..'】\n» سایز عکس : 【'..result.photos[1].sizes[1].photo.size..' پیکسل 】','md')
else
send(msg.chat_id,msg.id,'شما عکس پروفایل '..offset..' ندارید','md')
end
end
end
if Black and (Black:match('^whois (%d+)$') or Black:match('^اطلاعات (%d+)$')) then
local id = tonumber(Black:match('^whois (%d+)') or Black:match('^اطلاعات (%d+)'))
if id then
local Diamond = TD.getUser(id)
if Diamond.first_name == '' then 
username = Diamond.id
else
username = Diamond.first_name
end
send(msg.chat_id,msg.id,'['..id..'](tg://user?id='..username..')','md')
else
send(msg.chat_id,msg.id,'*کاربر ['..id..'] یافت نشد*','md')
end
end
if (Black == "id" or Black == "ایدی" or Black == "آیدی") and is_JoinChannel(msg) and tonumber(reply_id) == 0 then 
Msgs = base:get(TD_ID..'Total:messages:'..msg.chat_id..':'..(msg.sender_id.user_id or 00000000))
Msgsgp = tonumber(base:get(TD_ID..'Total:messages:'..msg.chat_id..'') or 1)
Msgsday = tonumber(base:get(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..msg.sender_id.user_id or 00000000))
function diamondper(num, idp)
return tonumber(string.format("%." .. (idp or 0) .. "f", num))
end
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then 
name = diamond.id
else 
name = ec_name(diamond.first_name)
end
local percent = Msgs / Msgsgp * 100
if diamond.usernames == '' then
UsErName = '#فاقد_نام_کاربری'
else
UsErName = '@'..diamond.usernames.editable_username
end
local result = TD.getUserProfilePhotos(msg.sender_id.user_id,0,1)
if result.photos[1] then
TD.sendPhoto(msg.chat_id,msg.id,result.photos[1].sizes[1].photo.id,'❖ #شناسه‌کاربر‌ےشما : '..UsErName..'\n❖ #شناسه‌شما : '..msg.sender_id.user_id..'\n❖ #شناسه‌گروه : '..msg.chat_id..'\n❖ #تعدادپیام‌هاے‌گروه : '..Msgsgp..'\n❖ #تعداد‌پیام‌هاے‌امروز‌شما : '..Msgsday..'\n❖ #تعداد‌کل‌پیام‌ها‌ے‌شما : '..Msgs..' ('..diamondper(percent)..'%)','html')
else
send(msg.chat_id,msg.id,'❖ #شناسه‌کاربر‌ےشما : '..UsErName..'\n❖ #شناسه‌شما : '..msg.sender_id.user_id..'\n❖ #شناسه‌گروه : '..msg.chat_id..'\n❖ #تعدادپیام‌هاے‌گروه : '..Msgsgp..'\n❖ #تعداد‌پیام‌هاے‌امروز‌شما : '..Msgsday..'\n❖ #تعداد‌کل‌پیام‌ها‌ے‌شما : '..Msgs..'','html')
end
end
-------Info By User-------
if (Black == 'info' or Black == 'اطلاعات') and is_JoinChannel(msg) and tonumber(reply_id) == 0 then
kick =
base:get(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
ban =
base:get(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
add =
base:get(TD_ID..'Total:AddUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
text = '✦ اطلاعات کاربر :\n\n['..name..'](tg://user?id='..msg.sender_id.user_id..')\n\nتعداد افراد اخراج شده توسط شما :\n【'..kick..'】\nتعداد افراد مسدود شده توسط شما :\n【'..ban..'】\nتعداد افراد افزوده شده توسط شما :\n【'..add..'】'
send(msg.chat_id,msg.id,text,'md')
end
-------Info By Reply-------
if (Black == 'info' or Black == 'اطلاعات') and tonumber(reply_id) ~= 0  and is_JoinChannel(msg) then
local Diamond = TD.getMessage(msg.chat_id,tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
kick =
base:get(TD_ID..'Total:KickUser:'..msg.chat_id..':'..user) or 0
ban =
base:get(TD_ID..'Total:BanUser:'..msg.chat_id..':'..user) or 0 
add =
base:get(TD_ID..'Total:AddUser:'..msg.chat_id..':'..user) or 0
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
text = '✦ اطلاعات کاربر :\n\n['..name..'](tg://user?id='..user..')\n\nتعداد افراد اخراج شده توسط کاربر :\n【'..kick..'】\nتعداد افراد مسدود شده توسط کاربر :\n【'..ban..'】\nتعداد افراد افزوده شده توسط کاربر :\n【'..add ..'】'
send(msg.chat_id,msg.id,text,'md')
end
end
if Black and (Black:match('^echo (.*)$') or Black:match('^اکو (.*)$')) then
local txt = Black:match('^echo (.*)$') or Black:match('^اکو (.*)$')
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
send(msg.chat_id,msg.id,txt,'md')
end
if (Black == 'me' or Black == 'امار من') and is_JoinChannel(msg) then 
local Diamond = TD.getUser(msg.sender_id.user_id)
local result = TD.getUserFullInfo(msg.sender_id.user_id)
rankk =  ''..(base:get(TD_ID..'rank'..msg.chat_id..msg.sender_id.user_id) or "مقامے ندارید")..''
if is_Sudo(msg)then
rank =  'سودو ربات' 
elseif is_Owner(msg)then
rank =  'سازنده گروه' 
elseif is_Mod(msg)then
rank =  'مدیر گروه'
elseif is_Vip(msg)then
rank =  'عضو ویژه'
elseif not is_Mod(msg)then
rank = 'کاربر عادے'
end
if Diamond.first_name == '' then
DiamondName = 'nil'
else  
DiamondName = Diamond.first_name
end
if result.bio.text == '' then
DiamondAbout = 'Empty'
else  
DiamondAbout = result.bio.text
end
if result.common_chat_count == ''  then
Diamondcommon_chat_count  = '00'
else 
Diamondcommon_chat_count  = result.common_chat_count 
end
if Diamond.status.expires == '' then
onoff  = 'آخرین بازدید اخیرا'
else 
onoff  = ''..(os.date("%X", Diamond.status.expires))..''
end
kick = base:get(TD_ID..'Total:KickUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
ban = base:get(TD_ID..'Total:BanUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
add = base:get(TD_ID..'Total:AddUser:'..msg.chat_id..':'..msg.sender_id.user_id) or 0
Msgs = base:get(TD_ID..'Total:messages:'..msg.chat_id..':'..(Diamond.id or 00000000)) or 0
Msgsgp = tonumber(base:get(TD_ID..'Total:messages:'..msg.chat_id..'') or 0) or 0
Msgsday = tonumber(base:get(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..Diamond.id or 00000000)) or 0
 function diamondper(num, idp)
return tonumber(string.format("%." .. (idp or 0) .. "f", num))
end
 local percent = Msgs / Msgsgp * 100
 txtm = '✶ بخشی از اطلاعات کاربری شما : \nا┅┅──┄┄═❂═┄┄──┅┅\n|↜ نام کوچک  : '..check_markdown(DiamondName)..'\n|↜ شناسه شما :'..msg.sender_id.user_id..'\n|↜ نام کاربرے شما : @'..check_markdown(Diamond.usernames.editable_username or 'nil')..'\n|↜ بیوگرافے : '..check_markdown(DiamondAbout)..'\n|↜اخرین بازدید : '..onoff..'\nا┅┅──┄┄═❂═┄┄──┅┅\n|↜ مقام شما : '..rankk..'\n|↜ مقام شما در ربات : '..rank..'\n|↜ تعداد پیام هاے شما : '..Msgs..'\n|↜ تعداد پیام‌هاے امروز شما : *'..Msgsday..'*\n|↜ تعداد کل پیام‌هاے شما : *'..Msgs..'*\n|↜ درصدکل پیام‌هاے شما : *'..diamondper(percent)..'%*\n|↜تعداد افراد اخراج کرده : '..kick..'\n|↜تعداد افراد مسدود کرده : '..ban..'\n|↜تعداد اد : '..add
send(msg.chat_id,msg.id,txtm,'md')
end

if (Black == 'time' or Black == 'ساعت') and is_JoinChannel(msg) then
send(msg.chat_id,msg.id,'ساعت : '..jdates('#h:#m:#s')..'\nذکر امروز : '..jdates('#z')..'','md')
end

if (Black == 'tophoto' or Black == 'تبدیل به عکس') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local result = TD.getMessage(msg.chat_id, reply_id) 
if result.content.sticker then 
repeat 
download = TD.downloadFile(result.content.sticker.sticker.id) 
until #download['local'].path ~= 0 
TD.sendPhoto(msg.chat_id, msg.id, download['local'].path,result.content.sticker.emoji) 
else 
send(msg.chat_id,msg.id,'فقط #استیکر ها قابل تبدیل میباشد','md')
end
end

if Black and (Black:match('^([Ww][Rr][Ii][Tt][Ee]) [\216-\219][\128-\191](.*)$') or Black:match('^زیباسازی [\216-\219][\128-\191](.*)$')) and is_JoinChannel(msg) then
local matches = Black:match('^write (.*)$') or Black:match('^زیباسازی (.*)$')
dofile('./libs/fonts.lua')
	if utf8.len(matches) > 4 then
	send(msg.chat_id,msg.id,"حداکثر حروف مجاز [ 4] کاراکتر است•!\nتعداد کارکترهاے شما : "..utf8.len(matches),'html')
	end
	local font_base = "ض,ص,ق,ف,غ,ع,ه,خ,ح,ج,ش,س,ی,ب,ل,ا,ن,ت,م,چ,ظ,ط,ز,ر,د,پ,و,ک,گ,ث,ژ,ذ,آ,ئ,.,_"
	local font_hash = "ض,ص,ق,ف,غ,ع,ه,خ,ح,ج,ش,س,ی,ب,ل,ا,ن,ت,م,چ,ظ,ط,ز,ر,د,پ,و,ک,گ,ث,ژ,ذ,آ,ئ,.,_"
local result = {}
	i=0
	for k=1,#fontf do
		i=i+1
		local tar_font = fontf[i]:split(",")
		local text = matches
		local text = text:gsub("ض",tar_font[1])
		local text = text:gsub("ص",tar_font[2])
		local text = text:gsub("ق",tar_font[3])
		local text = text:gsub("ف",tar_font[4])
		local text = text:gsub("غ",tar_font[5])
		local text = text:gsub("ع",tar_font[6])
		local text = text:gsub("ه",tar_font[7])
		local text = text:gsub("خ",tar_font[8])
		local text = text:gsub("ح",tar_font[9])
		local text = text:gsub("ج",tar_font[10])
		local text = text:gsub("ش",tar_font[11])
		local text = text:gsub("س",tar_font[12])
		local text = text:gsub("ی",tar_font[13])
		local text = text:gsub("ب",tar_font[14])
		local text = text:gsub("ل",tar_font[15])
		local text = text:gsub("ا",tar_font[16])
		local text = text:gsub("ن",tar_font[17])
		local text = text:gsub("ت",tar_font[18])
		local text = text:gsub("م",tar_font[19])
		local text = text:gsub("چ",tar_font[20])
		local text = text:gsub("ظ",tar_font[21])
		local text = text:gsub("ط",tar_font[22])
		local text = text:gsub("ز",tar_font[23])
		local text = text:gsub("ر",tar_font[24])
		local text = text:gsub("د",tar_font[25])
		local text = text:gsub("پ",tar_font[26])
		local text = text:gsub("و",tar_font[27])
		local text = text:gsub("ک",tar_font[28])
		local text = text:gsub("گ",tar_font[29])
		local text = text:gsub("ث",tar_font[30])
		local text = text:gsub("ژ",tar_font[21])
		local text = text:gsub("ذ",tar_font[32])
		local text = text:gsub("ئ",tar_font[33])
		local text = text:gsub("آ",tar_font[34])
		table.insert(result, text)
	end
	local result_text = "√•زیبا سازے اسم•√  : "..matches.."\nتعداد کارکترهاے شما : "..utf8.len(matches).."\nطراحے با "..tostring(#fontf).." فونت:\n━━━━━━━━━━\n"
	a=0
	for v=1,#result do
		a=a+1
		result_text = result_text..a.."🔘 "..result[a].."\n"
	end
send(msg.chat_id, msg.id,result_text..">━━━━━━━━━━<\n","md")
end
if Black and (Black:match('^([Ww][Rr][Ii][Tt][Ee]) [a-z](.*)$') or Black:match('^([Ww]rite) [A-Z](.*)$') or Black:match('^زیباسازی [A-Z](.*)$') or Black:match('^زیباسازی [a-z](.*)$')) and is_JoinChannel(msg) then
local matches = Black:match('^write (.*)$') or Black:match('^زیباسازی (.*)$')
dofile('./libs/fonts.lua')
	if utf8.len(matches) > 20 then
		send(msg.chat_id,msg.id,"حداکثر حروف مجاز [ 4] کاراکتر است•!\nتعداد کارکترهاے شما : "..utf8.len(matches),'html')
	end
	local font_base = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,9,8,7,6,5,4,3,2,1,.,_"
	local font_hash = "z,y,x,w,v,u,t,s,r,q,p,o,n,m,l,k,j,i,h,g,f,e,d,c,b,a,Z,Y,X,W,V,U,T,S,R,Q,P,O,N,M,L,K,J,I,H,G,F,E,D,C,B,A,0,1,2,3,4,5,6,7,8,9,.,_"
	local result = {}
	i=0
	for k=1,#fonte do
		i=i+1
		local tar_font = fonte[i]:split(",")
		matches2 = matches:gsub("[Aa]","α")
		local text = matches2
		local text = text:gsub("A",tar_font[1])
		local text = text:gsub("B",tar_font[2])
		local text = text:gsub("C",tar_font[3])
		local text = text:gsub("D",tar_font[4])
		local text = text:gsub("E",tar_font[5])
		local text = text:gsub("F",tar_font[6])
		local text = text:gsub("G",tar_font[7])
		local text = text:gsub("H",tar_font[8])
		local text = text:gsub("I",tar_font[9])
		local text = text:gsub("J",tar_font[10])
		local text = text:gsub("K",tar_font[11])
		local text = text:gsub("L",tar_font[12])
		local text = text:gsub("M",tar_font[13])
		local text = text:gsub("N",tar_font[14])
		local text = text:gsub("O",tar_font[15])
		local text = text:gsub("P",tar_font[16])
		local text = text:gsub("Q",tar_font[17])
		local text = text:gsub("R",tar_font[18])
		local text = text:gsub("S",tar_font[19])
		local text = text:gsub("T",tar_font[20])
		local text = text:gsub("U",tar_font[21])
		local text = text:gsub("V",tar_font[22])
		local text = text:gsub("W",tar_font[23])
		local text = text:gsub("X",tar_font[24])
		local text = text:gsub("Y",tar_font[25])
		local text = text:gsub("Z",tar_font[26])
		local text = text:gsub("a",tar_font[27])
		local text = text:gsub("b",tar_font[28])
		local text = text:gsub("c",tar_font[29])
		local text = text:gsub("d",tar_font[30])
		local text = text:gsub("e",tar_font[21])
		local text = text:gsub("f",tar_font[32])
		local text = text:gsub("g",tar_font[33])
		local text = text:gsub("h",tar_font[34])
		local text = text:gsub("i",tar_font[35])
		local text = text:gsub("j",tar_font[36])
		local text = text:gsub("k",tar_font[37])
		local text = text:gsub("l",tar_font[38])
		local text = text:gsub("m",tar_font[39])
		local text = text:gsub("n",tar_font[40])
		local text = text:gsub("o",tar_font[41])
		local text = text:gsub("p",tar_font[42])
		local text = text:gsub("q",tar_font[43])
		local text = text:gsub("r",tar_font[44])
		local text = text:gsub("s",tar_font[45])
		local text = text:gsub("t",tar_font[46])
		local text = text:gsub("u",tar_font[47])
		local text = text:gsub("v",tar_font[48])
		local text = text:gsub("w",tar_font[49])
		local text = text:gsub("x",tar_font[50])
		local text = text:gsub("y",tar_font[51])
		local text = text:gsub("z",tar_font[52])
		local text = text:gsub("0",tar_font[53])
		local text = text:gsub("9",tar_font[54])
		local text = text:gsub("8",tar_font[55])
		local text = text:gsub("7",tar_font[56])
		local text = text:gsub("6",tar_font[57])
		local text = text:gsub("5",tar_font[58])
		local text = text:gsub("4",tar_font[59])
		local text = text:gsub("3",tar_font[60])
		local text = text:gsub("2",tar_font[61])
		local text = text:gsub("1",tar_font[62])
		table.insert(result, text)
	end
	local result_text = "کلمه ے اولیه: "..matches.."\nتعداد کارکتر هاے کلمه : "..utf8.len(matches).."\nطراحے با "..tostring(#fonte).." فونت:\n━━━━━━━━━━━━\n"
	a=0
	for v=1,#result do
		a=a+1
		result_text = result_text..a.."- "..result[a].."\n"
	end
send(msg.chat_id, msg.id,result_text..">━━━━━━━━━━<\n","md")
end
if Black and (Black:match("^(ping)$") or Black:match("^(پینگ)$")) and is_JoinChannel(msg) then
txt = "• ربات هم اکنون آنلاین میباشد !"
send(msg.chat_id,msg.id,txt,'html')
end	
if Black and (Black:match('^tpm @(.*)') or Black:match('^تعداد پیام @(.*)') or Black:match('^تعدادپیام @(.*)')) and is_JoinChannel(msg) then
local username = Black:match('^tpm @(.*)') or Black:match('^تعداد پیام @(.*)') or Black:match('^تعدادپیام @(.*)')
local data = TD.searchPublicChat(username)
if data.id then
user_id = data.id
chat_id = msg.chat_id
Msgs = base:get(TD_ID..'Total:messages:'..chat_id..':'..user_id) or 0
Msgsgp = tonumber(base:get(TD_ID..'Total:messages:'..chat_id..'') or 0)
Msgsday = tonumber(base:get(TD_ID..'Total:messages:'..chat_id..':'..os.date('%Y/%m/%d')..':'..user_id or 00000000)) or 0
function diamondper(num,idp)
return tonumber(string.format('%.' ..(idp or 0) .. 'f',num))
end
percent = Msgs / Msgsgp * 100
gp = base:get(TD_ID..'StatsGpByName'..msg.chat_id) or 'nil'
local diamond = TD.getUser(user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'تعدادپیام‌هاےگروه',callback_data = 'Msgsgp'},
{text = ''..Msgsgp..'',callback_data = 'Msgsgp'}},{
{text = 'تعدادپیام‌هاےامروزکاربر',callback_data = 'Msgsday'},
{text = ''..Msgsday..'',callback_data = 'Msgsday'}},{
{text = 'تعدادکل پیام‌هاےکاربر',callback_data = 'Msgs'},
{text = ''..Msgs..'',callback_data = 'Msgs'}},{
{text = 'درصدکل پیام‌هاےکاربر',callback_data = '(diamondper(percent))'},
{text = ''..(diamondper(percent))..'%', callback_data = '(diamondper(percent))'}},{
{text = '🗑 ریستارت',callback_data = 'resetpms:'..user_id..':'..name..':'..chat_id},
{text = '✦ بستن پنل پیام',callback_data = 'bd:Exitss:'..chat_id},},}
BD = 'آمار پیام‌هاے :\nکاربر :【<a href="tg://user?id='..data.id..'">'..name..'</a>】\nدر گروه :【'..gp..'】\n─┅━━━━━━━┅─\n'
send_inline(msg.chat_id,BD,keyboard,'html')
else 
send(msg.chat_id,msg.id,'❎ کاربر یافت نشد','html')
end
end   
if (Black == 'fal' or Black == 'فال') and is_JoinChannel(msg) then
local url = 'http://api.novateamco.ir/fal'
local file = DownloadFile(url,'fal.jpg')
TD.sendPhoto(msg.chat_id,msg.id,file,'','md')
end
if (Black == 'تاریخ' or Black == 'date') and is_JoinChannel(msg) then
txt = '_امروز : '..jdates('#x')..'\nتاریخ : '..jdates('_#D-#X-#Y_')..'_'
send(msg.chat_id,msg.id,txt,'md')
end
if (Black == 'jok' or Black == 'جوک') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/')
end
if (Black == 'p n p' or Black == 'پ ن پ') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/pa-na-pa/')
end
if (Black == 'memory' or Black == 'خاطره') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/khatere/')
end
if (Black == 'story' or Black == 'داستان') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/dastan/')
end
if (Black == 'dialog' or Black == 'دیالوگ') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/dialog/')
end
if (Black == 'for fun' or Black == 'الکی مصلا') and is_JoinChannel(msg) then
getApi(msg,'https://api.codebazan.ir/jok/alaki-masalan/')
end
if (Black == 'news' or Black == 'خبر') and is_JoinChannel(msg) then
url,res = https.request('http://api.codebazan.ir/khabar/?kind=iran')
if res == 200 then
end
statsurl = json:decode(url)
if statsurl.Ok == true then
text = ""
for k,v in pairs(statsurl.Result) do
text = text.."\nموضوع خبر : "..v.title.."\nلینک خبر : "..v.link.."\n"
end
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'Error','md')
end
end
if Black and (Black:match('^اوقات شرعی (.*)')) and is_JoinChannel(msg) then
text = Black:match('^اوقات شرعی (.*)') 
url,res = https.request('http://api.codebazan.ir/owghat/?city='..text..'')
if res == 200 then
end
jdat = json:decode(url)
if jdat.Ok == true then
text = ""
for i=1,#jdat.Result do
text = text.."نام شهر : "..jdat.Result[i].shahr.."\nتاریخ : "..jdat.Result[i].tarikh.."\nاذان صبح : "..jdat.Result[i].azansobh.."\nطلوع آفتاب : "..jdat.Result[i].toloaftab.."\nاذان ظهر : "..jdat.Result[i].azanzohr.."\nغروب آفتاب : "..jdat.Result[i].ghorubaftab.."\nاذان مغرب : "..jdat.Result[i].azanmaghreb.."\nنیمه شب : "..jdat.Result[i].nimeshab..""
end
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'Error','md')
end
end
if (Black == 'currency' or Black == 'قیمت ارز') and is_JoinChannel(msg) then
data, res = https.request('http://api.codebazan.ir/arz/?type=arz')
if res == 200 then
local jdat = json:decode(data)
text = "• لیست قیمت ها"
for k,v in pairs(jdat) do
text = text.."\n• نام : "..v.name.."\n• قیمت : "..v.price.."\n"
end
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'Error','md')
end
end
if (Black == 'price car' or Black == 'قیمت خودرو') and is_JoinChannel(msg) then
data, res = https.request('http://api.codebazan.ir/car-price/')
if res == 200 then
end
local jdat = json:decode(data)
if jdat.Ok == true then
text = "• لیست قیمت ها"
for i=1,#jdat.Result do
if i == 33 then
break
end
text = text.."\n• نام : "..jdat.Result[i].name.."\n• قیمت : "..jdat.Result[i].bazar.."\n"
end
send(msg.chat_id,msg.id,text,'html')
else
send(msg.chat_id,msg.id,'Error','md')
end
end

if (Black == 'tag' or Black == 'تگ') then
local org = TD.getSupergroupMembers(msg.chat_id,"Recent", '' , 0 , 200 )
t = '✶ بیکاران گروه بالا باشید 😁 :\n\n'
for key, v in ipairs(org.members) do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..v.member_id.user_id)
if res ~= 200 then
end
local name = json:decode(url).result.user.first_name or v.member_id.user_id
if key == 20 then break end
nm = '<a href="tg://user?id='..v.member_id.user_id..'">'..name..'</a>'
t = t..''..(nm)..' |'
end
send(msg.chat_id,msg.id,t,'html')
tt = '\n'
for i=20,#org.members do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..org.members[i].member_id.user_id)
if res ~= 200 then
end
local name1 = json:decode(url).result.user.first_name or org.members[i].member_id.user_id
if i == 40 then break end
nm = '<a href="tg://user?id='..org.members[i].member_id.user_id..'">'..name1..'</a>'
tt = tt..''..(nm)..' |'
end
send(msg.chat_id,msg.id,tt,'html')
ttt = '\n'
for i=40,#org.members do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..org.members[i].member_id.user_id)
if res ~= 200 then
end
local name2 = json:decode(url).result.user.first_name or org.members[i].member_id.user_id
if i == 60 then break end
nm = '<a href="tg://user?id='..org.members[i].member_id.user_id..'">'..name2..'</a>'
ttt = ttt..''..(nm)..' |'
end
send(msg.chat_id,msg.id,ttt,'html')
tttt = '\n'
for i=60,#org.members do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..org.members[i].member_id.user_id)
if res ~= 200 then
end
local name3 = json:decode(url).result.user.first_name or org.members[i].member_id.user_id
if i == 80 then break end
nm = '<a href="tg://user?id='..org.members[i].member_id.user_id..'">'..name3..'</a>'
tttt = tttt..''..(nm)..' |'
end
send(msg.chat_id,msg.id,tttt,'html')
five = '\n'
for i=80,#org.members do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..org.members[i].member_id.user_id)
if res ~= 200 then
end
local name4 = json:decode(url).result.user.first_name or org.members[i].member_id.user_id
if i == 100 then break end
nm = '<a href="tg://user?id='..org.members[i].member_id.user_id..'">'..name4..'</a>'
five = five..''..(nm)..' |'
end
send(msg.chat_id,msg.id,five,'html')
six = '\n'
for i=100,#org.members do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..org.members[i].member_id.user_id)
if res ~= 200 then
end
local name5 = json:decode(url).result.user.first_name or org.members[i].member_id.user_id
if i == 120 then break end
nm = '<a href="tg://user?id='..org.members[i].member_id.user_id..'">'..name5..'</a>'
six = six..''..(nm)..' |'
end
send(msg.chat_id,msg.id,six,'html')
end
if (Black == 'تگ مدیران') then
local result = TD.getSupergroupMembers(msg.chat_id,"Administrators", '' , 0 , 200 )
txt = '\n'
for k,v in pairs(result.members) do
local url  = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id='..msg.chat_id..'&user_id='..v.member_id.user_id)
if res ~= 200 then
end
local ul = json:decode(url).result.user.first_name
if v.status._== "chatMemberStatusAdministrator" then
nms = '<a href="tg://user?id='..v.member_id.user_id..'">'..ul..'</a>'
txt = txt..''..(nms)..' «» '
end 
end
send(msg.chat_id,msg.id,txt,'html')
end
if (Black == 'لقب') and tonumber(reply_id) > 0 then
local data = TD.getMessage(msg.chat_id,tonumber(reply_id))
local user_id = data.sender_id.user_id
local rankk = base:get(TD_ID..'rank'..msg.chat_id..user_id) or "مقامے  ندارد"
send(msg.chat_id,msg.id,'لقب کاربر : '..rankk..'','md')
end
if (Black == 'proxy' or Black == 'پروکسی') and is_JoinChannel(msg) then
data,res = https.request('http://api.codebazan.ir/mtproto/json/')
if res == 200 then
local data = json:decode(data)
if data.Ok then 
txt = "لیست پروڪسے"
keyboard = {
inline_keyboard = {}}
for key,value in pairs(data.Result) do      keyboard.inline_keyboard[#keyboard.inline_keyboard + 1] = 
{{text = 'پروڪسے '..key,url='https://t.me/proxy?server='..value.server..'&port='.. value.port..'&secret='..value.secret}}
end
send_inline(msg.chat_id,txt,keyboard,'html')
end
else 
--send(msg.chat_id,msg.id,'عملیات خروجی نداشت','html')
end
end
if (Black == 'najva' or Black == 'نجوا') and is_JoinChannel(msg) and tonumber(reply_id) > 0 then
local Diamond = TD.getMessage(msg.chat_id, tonumber(reply_id))
local user = Diamond.sender_id.user_id
if user then
local diamond = TD.getUser(user)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
send(msg.chat_id,msg.id,'نجوای شما بر روی ( '.. MBD(name,user)..' ) تنظیم شد !\nلطفا متن نجوای خود را در خصوصی ربات ( '..check_markdown(UserJoiner)..' ) ارسال کنید...','md')
base:setex(TD_ID..'NajVa'..msg.sender_id.user_id,400,user..'>'..msg.chat_id..'>'..name)
function BD_NAJVA()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
TD.set_timer(10,BD_NAJVA)
end
end

end
-----del today chat
if tonumber(os.date("%H%M")) > 2350 and not base:get(TD_ID..'delincr'..msg.chat_id) then
allusers = base:smembers(TD_ID..'AllUsers:'..msg.chat_id)
for k, v in pairs(allusers) do 
base:del(TD_ID..'Total:messages:'..msg.chat_id..':'..os.date("%Y/%m/%d")..':'..v)
end
base:setex(TD_ID..'delincr'..msg.chat_id,60,true)
end
--[[
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'forcejoin') then
local Ch = (base:get(TD_ID..'setch:'..msg.chat_id) or '..Channel..')
local url , res = https.request('https://api.telegram.org/bot'..JoinToken..'/getchatmember?chat_id=@'..Ch..'&user_id='..msg.sender_id.user_id)
if res ~= 200 then
end
local warn = base:get(TD_ID..'joinwarn:'..msg.chat_id) or 1
local startwarn = TD_ID..':join'..os.date("%M")..':'..msg.chat_id + 4
local endwarn = base:hget(startwarn,msg.sender_id.user_id) or 1
local Joinchanel = json:decode(url)
if not is_GlobalyBan(msg.sender_id.user_id) and (not Joinchanel.ok or Joinchanel.result.status == "left" or Joinchanel.result.status == "kicked") and not is_Sudo(msg) and not is_Owner(msg) then
print 'Force Join'
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
local diamond = TD.getUser(msg.sender_id.user_id)
if diamond.first_name == '' then name = diamond.id else name = ec_name(diamond.first_name) end
if tonumber(endwarn) > tonumber(warn) then
else
local bbd = '✦ کاربر :\nنام کاربرے :【@'..(diamond.usernames.editable_username or '')..'】\nنام :【'..diamond.first_name..'】\n─┅━━━━━━━┅─\n℘ شما ابتدا باید در کانال زیر عضو شوید تا بتوانید پیام ارسال کنید\n\n℘ نکته : درصورت عضو نشدن پیام شما پاک خواهد شد'
Button = {{
{text = '✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Ch}}}
TD.sendText(msg.chat_id,msg.id,bbd, 'html', true, false, false, false,keyboards(Button))
base:hset(startwarn,msg.sender_id.user_id,tonumber(endwarn) + 1)
end
else
return true
end
end]]
-------BlaCk Diamond---------
end
end
cleancache()
CheCkk()
local function updateNewMessage(data)
local msg = data.message
local Black = (msg.content.text and msg.content.text.text) or (msg.content.caption and msg.content.caption.text)
if Black then
if msg.sender_id.user_id == Config.BotJoiner then
if msg.sender_id.user_id == Config.BotJoiner then
return false
end
--Clean Welcome
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cl_welcome') then
if Black:match('خوش امدی') or Black:match('خوش اومدی') or Black:match('خوش امدید') or Black:match('به گروه دعوت کنید تا بتوانید در گروه پیام ارسال کنید') or Black:match('دعوت کنید') or Black:match('ادد کنید') or Black:match('شما یک ربات به گروه اضافه کردید لطفا یک کاربر عادے اضافه کنید') or Black:match('شما اکنون میتوانید پیام ارسال کنید ✔') or Black:match('دلیل اخراج') or Black:match('دلیل محدودیت') or Black:match('دلیل سایلنت') or Black:match('دلیل اخطار') or Black:match('دلیل مسدودیت') and not Black:match('به فهرست اصلی و پنل مدیریت خوش آمدید') then
function BDClearPms()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
local Times_2 = tonumber(base:get(TD_ID..'Times_Welcome:'..msg.chat_id)) or 10
TD.set_timer(Times_2,BDClearPms)
end end
if Black:match('صورتی که به این سوال تا 15 دقیقه آینده پاسخ ندهید') or Black:match('به هر دو سوال احراز هویت پاسخ اشتباه داد و از گروه')  then
function BDClearPmc()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
TD.set_timer(900,BDClearPmc)
end 
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cbmon') and (not Black or (Black and not Black:match('به فهرست اصلی و پنل مدیریت خوش آمدید'))) then
function BDClearCmd()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
local timecgms = tonumber(base:get(TD_ID..'cbmtime:'..msg.chat_id)) or 10
TD.set_timer(timecgms,BDClearCmd)
end
end
end
BDStartPro(data.message,data)
end

local function updateNewInlineQuery(data)
BDStartQuery(data)
end
function updateMessageEdited(data)
local msg = data.message
BDStartPro(msg,data)
res = TD.getMessage(data.chat_id,data.message_id)
BDStartPro(res,data)
end 
local function updateMessageSendSucceeded(update)
local msg = update.message
local Black = (msg.content.text and msg.content.text.text) or (msg.content.caption and msg.content.caption.text)
if Black then
if base:sismember(TD_ID..'Gp2:'..msg.chat_id,'cbmon') and (not Black or (Black and not Black:match('به فهرست اصلی و پنل مدیریت خوش آمدید'))) then
function BDClearCmd()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
local timecgms = tonumber(base:get(TD_ID..'cbmtime:'..msg.chat_id)) or 10
TD.set_timer(timecgms,BDClearCmd)
end
logo ={
'█ %10','████ %40','███████ %70','██████████ %100'}

if Black:match('℘ شما ابتدا باید در کانال زیر عضو شوید تا بتوانید پیام ارسال کنید') then
function BDClearPms_()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
TD.set_timer(30,BDClearPms_)
end 
if Black:match('^نجوای شما بر روی') or Black:match('به دلیل عدم احراز هویت') then
function BD_ClearPm()
TD.deleteMessages(msg.chat_id,{[1] = msg.id})
end
TD.set_timer(10,BD_ClearPm)
end 
if Black:match('• برای ارسال پیام در گروه باید عضو کانال گروه باشید ، لطفاً با استفاده از دکمه زیر در کانال عضو شوید !\n\n⚠️ شما در کانال عضو نیستید:') then
base:set(TD_ID..'msgid_joins_'..msg.chat_id,msg.id)
TD.vardump(msg.id)
end
if Black:match('اعضا و کاربران گرامی گروه :\nبه زمان فعال شدن پاکسازی #خودکار ➓ دقیقه مانده است') then
TD.pinChatMessage(msg.chat_id,msg.id)
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
end end

local function updateChatMember(msg)
local usere = msg.new_chat_member.member_id.user_id
local adder = msg.actor_user_id
local xb = TD.getUser(usere)
if xb.type["@type"] == "userTypeBot" then
else
base:incr(TD_ID .. "Content_Message:Adds:" .. adder .. ":" .. msg.chat_id)
base:incr(TD_ID .. "Content_Message:AddsDay:" .. adder .. ":" .. msg.chat_id)
end
end
tdlib.run({
updateNewMessage = updateNewMessage
,updateMessageEdited = updateMessageEdited,updateMessageSendSucceeded = updateMessageSendSucceeded,updateNewInlineQuery = updateNewInlineQuery,updateChatMember = updateChatMember})

---Finish By @XXXTENTACIONGG
