-- ==============================
-- 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
-- Use, edit and redistribute only with attribution to @Abolfazl_rwm.
-- ==============================

package.path = package.path ..';.luarocks/share/lua/5.2/?.lua' .. ';./bot/?.lua'
package.cpath = package.cpath .. ';.luarocks/lib/lua/5.2/?.so'
-----
socket = require('socket')
URL = require('socket.url')
https = require('ssl.https')
http = require('socket.http')
HTTPS = require "ssl.https"
HTTP = require "socket.http"
http.TIMEOUT = 10
curl = require('cURL')
ltn12 = require('ltn12')
curl_context = curl.easy{verbose = false}
clr = require 'term.colors'
serpent = (loadfile './libs/serpent.lua')()
json = (loadfile './libs/JSON.lua')()
JSON = (loadfile './libs/dkjson.lua')()
utf8 = loadfile('./libs/utf8.lua')()
db = require('redis')
base = db.connect('127.0.0.1',6379)
offset = 0
minute = 60
hour = 3600
day = 86400
week = 604800
MsgTime = os.time() - 60
Plan1 = 2592000
Plan2 = 7776000
 TD_ID = arg[1]
local Config = dofile('./Config/Config-'..tonumber(TD_ID)..'.lua')
SUDO = Config.SUDO_ID
BotCliId = Config.BotCliId
BotJoiner = Config.BotJoiner
Channel = Config.Channel
LinkSuppoRt = Config.LinkSuppoRt
botusername = Config.UserJoiner
Token = Config.JoinToken
jdates = dofile('./jdate.lua')
--------**FullSudo**--------
function is_FullSudo(msg)
local var = false
for v,user in pairs(Config.Full_Sudo) do
if user == msg.from.id then
var = true
end
end
if base:sismember(TD_ID..'FULLS_SUDO',msg.from.id) then
var = true
end
if FullSudo == tonumber(msg.from.id) then
var = true
end
return var
end
--------**FullSudo2**--------
function is_FullSudo2(UseR)
local var = false
for v,user in pairs(Config.Full_Sudo) do
if tonumber(user) == tonumber(UseR) then
var = true
end
end
if base:sismember(TD_ID..'FULLS_SUDO',UseR) then
var = true
end
if FSudo == tonumber(UseR) then
var = true
end
return var 
end
----------Function Sudo----------
function is_Sudo(msg)
local var = false
for v,user in pairs(SUDO) do
if user == msg.from.id then
var = true
end
end
if base:sismember(TD_ID..'SUDO',msg.from.id) or is_FullSudo(msg) then
var = true
end
if Sudo == tonumber(msg.from.id) then
var = true
end
return var
end
function is_sudo1(user_id)
local var = false
for v,user in pairs(SUDO) do
if user == user_id then
var = true
end
end
if base:sismember(TD_ID..'SUDO',user_id) or is_FullSudo2(user_id) then
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
--Function Owner2--
function is_Owner(chat_id,user_id)
local var = false
for v,user in pairs(SUDO) do
if user == user_id then
var = true
end
end
local fsudo = base:sismember(TD_ID..'FULLS_SUDO',user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
local hash = base:sismember(TD_ID..'OwnerList:'..chat_id,user_id)
if hash or fsudo or Sudo then
var=  true
end
return var
end
--------**Mod**--------
function is_Mod(chat_id,user_id)
local var = false
for v,user in pairs(SUDO) do
if user == user_id then
var = true
end
end
local owner = base:sismember(TD_ID..'OwnerList:'..chat_id,user_id)
local hash = base:sismember(TD_ID..'ModList:'..chat_id,user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
local fsudo = base:sismember(TD_ID..'FULLS_SUDO',user_id)
if hash or owner or Sudo or fsudo then
var=  true
end
return var
end
--------**Vip**--------
function is_Vip(chat_id,user_id) 
local hash = base:sismember(TD_ID..'Vip:'..chat_id,user_id)
if hash or is_Mod(chat_id,user_id) then return true
else
return false
end
end
--------**Vips**--------
function is_Vips(msg,user_id) 
local hash = base:sismember(TD_ID..'Vips:'..user_id)
if hash or is_FullSudo(msg) then return true
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
function VipUser(chat_id,user_id)
local Mod = base:sismember(TD_ID..'ModList:'..chat_id,user_id)
local Owner = base:sismember(TD_ID..'OwnerList:'..chat_id,user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
if Mod or Owner or Sudo then
return true
else
return false
end
end
function VipUser_(msg,user_id)
user_id = user_id or 00
local Owner = base:sismember(TD_ID..'OwnerList:'..msg.chat.id,user_id)
local Sudo = base:sismember(TD_ID..'SUDO',user_id)
if Owner or Sudo then
return true
else
return false
end
end
--------**filter**--------
function is_filter(msg,value)
local list = base:smembers(TD_ID..'Filters:'..msg.chat.id)
var = false
for i=1, #list do
if base:sismember(TD_ID..'Gp2:'..msg.chat.id,'FilterSen') then
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
--------**MuteUser**--------
function is_MuteUser(chat_id,user_id)
local hash =  base:sismember(TD_ID..'MuteUser:'..chat_id,user_id)
if hash then
return true
else
return false
end
end
---------**Sleep & Split**--------
local function sleep(n)
os.execute('sleep '..n)
end
function string:split(sep)
local sep, fields = sep or ':', {}
local pattern = string.format('([^%s]+)', sep)
self:gsub(pattern, function(c) fields[#fields+1] = c end)
return fields
end
BASSE = "https://api.telegram.org/bot"..Config.JoinToken.."/"

function vardump(value)
 print(clr.yellow.."=================== START Vardump ==================="..clr.reset)
 print(serpent.block(value, {comment=false}))
 print(clr.yellow.."=================== END Vardump ==================="..clr.reset)
 local Text = ""
 .."\n"..serpent.block(value,{comment=false})
 .."\n"
end 
function getRes(Url)
Url = BASSE..Url
Req = https.request(Url)
Res = JSON.decode(Req)
--vardump(Res)
return Res
end
--Bot Info
BotName = getRes('getMe').result.first_name or false
BotUsername = getRes('getMe').result.username or false
BotId = getRes('getMe').result.id or false

function dl_cb(arg, data)
--vardump(arg)
end
--getUpdates Function
local function getUpdates(Offset)
local response = {}
local success,code,headers,status  = https.request{
url = BASSE..'/getUpdates?timeout=20&limit=1&offset='..(Offset or ''),
method = 'POST',
sink = ltn12.sink.table(response),}
local body = table.concat(response or {'no response'})
if (success == 1) then
return JSON.decode(body)
else
return nil,'Request Error'
end
end
function getParseMode(ParseMode)
if ParseMode:lower() == 'html' then
P = 'HTML'
elseif ParseMode:lower() == 'md' or ParseMode:lower() == 'markdown' then
P = 'Markdown'
else
P = ''
end
return P
end
function firstUpdate()
local Url = 'getUpdates?offset=-1'
return getRes(Url)
end
local BASSE = 'https://api.telegram.org/bot' ..Config.JoinToken
curl_context = curl.easy{verbose = false}
local function performRequest(url)
local data = {}
local c = curl_context:setopt_url(url)
:setopt_writefunction(table.insert, data)
:perform()
return table.concat(data), c:getinfo_response_code()
end
local function sendRequest(url)
local dat = performRequest(url)
local tab = JSON.decode(dat)	
return tab
end
-------**ec_name**--------
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
function string:escape_html()
self = self:gsub('&', '&amp;')
self = self:gsub('"', '&quot;')
self = self:gsub('<', '&lt;'):gsub('>', '&gt;')
return self
end
function string:escape_hard(ft)
if ft == 'bold' then
return self:gsub('%*', '')
elseif ft == 'italic' then
return self:gsub('_', '')
elseif ft == 'fixed' then
return self:gsub('`', '')
elseif ft == 'link' then
return self:gsub(']', '')
else
return self:gsub('%_', '\\_'):gsub('%*', '\\*'):gsub('%[', ''):gsub('%]', ''):gsub('%`', '\\`')
end
end
function string:escape(only_markup)
if not only_markup then
self = self:gsub('([@#/.])(%w)', '%1\xE2\x81\xA0%2')
end
return self:gsub('[*_`[]', '\\%0')
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
function getMe()
local url = BASSE .. '/getMe'
return sendRequest(url)
end
function getUpdates(offset)
local url = BASSE .. '/getUpdates?timeout=20'
if offset then
url = url .. '&offset=' .. offset
end
return sendRequest(url)
end
function unbanChatMember(chat_id,user_id)
local url = BASSE .. '/unbanChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id
return sendRequest(url)
end
function kickChatMember(chat_id,user_id)
local url = BASSE .. '/kickChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id
return sendRequest(url)
end
function banUser(chat_id,user_id)
local res = kickChatMember(chat_id,user_id)
if res then 
return res
end
end
function KickUser(chat_id,user_id)
local res = kickChatMember(chat_id,user_id)
if res then 
unbanChatMember(chat_id,user_id)
unbanChatMember(chat_id,user_id)
unbanChatMember(chat_id,user_id)
return res
end
end
function MuteUser(chat_id,user_id,time)
local Rep = BASSE.. '/restrictChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id..'&can_post_messages=false&until_date='..time
return https.request(Rep)
end
function UnRes(chat_id,user_id)
local Rep = BASSE.. '/restrictChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id..'&can_post_messages=true&can_add_web_page_previews=true&can_send_other_messages=true&can_send_media_messages=true'
return https.request(Rep)
end
function unbanUser(chat_id,user_id)
local res = unbanChatMember(chat_id,user_id)
return true
end
function getChat(chat_id)
local url = BASSE .. '/getChat?chat_id=' .. chat_id
return sendRequest(url)
end
function getChatAdministrators(chat_id)
local url = BASSE .. '/getChatAdministrators?chat_id=' .. chat_id
return sendRequest(url)
end
function getChatMembersCount(chat_id)
local url = BASSE .. '/getChatMembersCount?chat_id=' .. chat_id	
return sendRequest(url)
end
function getChatMember(chat_id,user_id)
local url = BASSE .. '/getChatMember?chat_id=' .. chat_id .. '&user_id=' .. user_id
return sendRequest(url)
end
function leave(chat_id)
local url = BASSE .. '/leaveChat?chat_id=' .. chat_id
local res = sendRequest(url)
if res then
end
sendRequest(url)
end
function Alert(callback_query_id,text, show_alert)
local url = BASSE .. '/answerCallbackQuery?callback_query_id=' .. callback_query_id .. '&text=' .. URL.escape(text)
if show_alert then
url = url..'&show_alert=true'
end
return sendRequest(url)
end
function sendKeyboard(chat_id,text,keyboard, ParseMode,reply_id)
local url = BASSE .. '/sendmessage?chat_id=' .. chat_id
if ParseMode then
url = url..'&parse_mode='..getParseMode(ParseMode)
end
if reply_id then
url = url .. '&reply_to_message_id='..reply_id
end
url = url..'&text='..URL.escape(text)
url = url..'&disable_web_page_preview=true'
url = url..'&reply_markup='..URL.escape(JSON.encode(keyboard))
return sendRequest(url)
end
function sendMessage(chat_id, text, ParseMode, reply_to_message_id, send_sound)
local url = BASSE .. '/sendMessage?chat_id=' .. chat_id .. '&text=' .. URL.escape(text)
url = url .. '&disable_web_page_preview=true'
if reply_to_message_id then
url = url .. '&reply_to_message_id=' .. reply_to_message_id
end
if ParseMode then
url = url..'&parse_mode='..getParseMode(ParseMode)
end
if not send_sound then
url = url..'&disable_notification=true'--messages are silent by default
end
local res = sendRequest(url)
return res
end
function sendReply(msg,text,markd,send_sound)
return sendMessage(msg.chat.id,text, markd,msg.message_id,send_sound)

end
function editMessageText(chat_id,message_id,text,keyboard,markdown,preview)
local url = BASSE 
if chat_id then
url = url .. '/editMessageText?chat_id=' .. chat_id .. '&message_id='..message_id..'&text=' .. URL.escape(text)
else
url = url .. '/editMessageText?inline_message_id='..message_id..'&text=' .. URL.escape(text)
end 
if markdown then
url = url .. '&parse_mode=Markdown'
else
url = url .. '&parse_mode=HTML'
end
if not preview then
url = url .. '&disable_web_page_preview=true'
end
if keyboard then
url = url..'&reply_markup='..URL.escape(JSON.encode(keyboard))
end
return sendRequest(url)
end
function Edit(msg,text,keyboard,markd)
if msg.message.chat.type ~= "private" and not base:sismember(TD_ID..'Gp2:'..msg.message.chat.id,'PanelPv') then
if not base:sismember(TD_ID..'Gp2:'..msg.message.chat.id,'diamondlang') then
text = text..'\n─┅┈┅┅┈┅┈╯◐ ◑╰┈┅┈┅┅┈┅─\nفقط (['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')) اجازه کار با پنل را دارد !'
else
text = text..'\n─┅┈┅┅┈┅┈╯◐ ◑╰┈┅┈┅┅┈┅─\nOnly (['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')) has access to the panel !'
end
else
text = text
end
return editMessageText(msg.message.chat.id,msg.message.message_id,text,keyboard,markd,preview)
end
function EdiT(msg,text,keyboard,markd)
return editMessageText(msg.message.chat.id,msg.message.message_id,text,keyboard,markd,preview)
end
function editMarkup(chat_id,message_id, reply_markup)
local url = BASSE .. '/editMessageReplyMarkup?chat_id=' .. chat_id ..
'&message_id='..message_id..
'&reply_markup='..URL.escape(JSON.encode(reply_markup))
return sendRequest(url)
end
function sendChatAction(chat_id,action)
local url = BASSE .. '/sendChatAction?chat_id=' .. chat_id .. '&action=' .. action
return sendRequest(url)
end
function getFile(file_id)
local url = BASSE .. '/getFile?file_id='..file_id
return sendRequest(url)
end
function getAdminlist(chat_id)
status = getChatAdministrators(chat_id)
for k,v in ipairs(status.result) do
if v.status == 'creator' then
if v.user.id then
creator = v.user.username or v.user.first_name
else
creator = 'not'
end
end
end
return creator
end

function getAdminlist_(chat_id)
status = getChatAdministrators(chat_id)
for k,v in ipairs(status.result) do
if v.status == 'administrator' then
if v.user.id then
administrator = v.user.id
else
administrator = 'not'
end
end
end
return administrator
end



stopPoll = function(chat_id,message_id,reply_markup)
request_url = BASSE..'/stopPoll?chat_id='..chat_id..'&message_id='..message_id
if reply_markup then    request_url=request_url..'&reply_markup='..URL.escape(JSON.encode(reply_markup))
end
return sendRequest(request_url) 
end
sendPoll = function(chat_id,question,options,disable_notification,reply_to_message_id,reply_markup)
request_url = BASSE..'/sendPoll?chat_id='..chat_id..'&question='..question..'&options='..options..'&disable_notification='..disable_notification..'&reply_to_message_id='..reply_to_message_id
if reply_markup then                   request_url=request_url..'&reply_markup='..URL.escape(JSON.encode(keyboard))
end
return sendRequest(request_url) 
end
exportChatInviteLink = function(chat_id)
url = BASSE..'/exportChatInviteLink?chat_id='..chat_id
return sendRequest(url)
end

function is_req(msg)
local var = false
if is_Sudo(msg) then
var = true
end
if msg.message.text and msg.message.text:match('اجازه کار با پنل را دارد !$') then
if msg.message.entities then
for i,entity in pairs(msg.message.entities) do
if entity.type == 'text_mention' then
if entity.user.id == msg.from.id then
var = true
end
end
end
end
else
var = true
end
if base:sismember(TD_ID..'Gp2:'..msg.message.chat.id,'PanelPv') then
var = true
end
return var
end
function string:starts(text)
return text == string.sub(self,1,string.len(text))
end
function download(FileUrl,FilePath,FileName)
	print(clr.yellow.."	File Url to Download => "..FileUrl..clr.reset)
	local respbody = {}
	local options = {
		url = FileUrl,
		sink = ltn12.sink.table(respbody),
		redirect = true
}
	-- nil, code, headers, status
	local response = nil
	if FileUrl:starts('https') then
		options.redirect = false
		response = {https.request(options)}
	else
		response = {http.request(options)}
	end
	local code = response[2]
	local headers = response[3]
	local status = response[4]
	if code ~= 200 then return nil end
	FilePathToSave = FilePath.."/"..FileName
	print(clr.green.." Success!,File Saved to => "..FilePathToSave..clr.reset)
	file = io.open(FilePathToSave,"w+")
	file:write(table.concat(respbody))
	file:close()
	return FilePathToSave
end
local function curlRequest(curl_command)
io.popen(curl_command)
end
function sendSticker(chat_id,sticker, reply_to_message_id)
local url = BASSE .. '/sendSticker'
local curl_command = 'curl "' .. url .. '" -F "chat_id=' .. chat_id .. '" -F "sticker=@' .. sticker .. '"'
if reply_to_message_id then
curl_command = curl_command .. ' -F "reply_to_message_id=' .. reply_to_message_id .. '"'
end
return curlRequest(curl_command)
end
function sendAudio(chat_id,Audio, reply,Cap,Title,Performer,Markup)
if not chat_id or not Audio then
print(clr.red.." *ERROR => chat_id or Audio [sendAudio]"..clr.reset)
return false
end
local Url = BASSE.."/sendAudio?"
curl_context:setopt_url(Url)
local form = curl.form()
form:add_content("chat_id",chat_id)
form:add_file("audio",Audio)
if reply then
form:add_content("reply_to_message_id", reply)
end
if Cap then
form:add_content("caption",Cap)
end
if Title then
form:add_content("title",Title)
end
if Performer then
form:add_content("performer",Performer)
end
if Markup then
form:add_content("reply_markup", json:encode(Markup))
end
data = {}
local c = curl_context:setopt_writefunction(table.insert,data)
:setopt_httppost(form)
:perform()	
return table.concat(data), c:getinfo_response_code()
end
function sendDocument(chat_id,Documnet, reply,Cap,markdown)
if not chat_id or not Documnet then
print(clr.red.." *ERROR => chat_id or Documnet [sendDocument]"..clr.reset)
return false
end
local Url = BASSE.."/sendDocument"
curl_context:setopt_url(Url)
local form = curl.form()
form:add_content("chat_id",chat_id)
form:add_file("document",Documnet)
if reply then
form:add_content("reply_to_message_id",reply)
end
if Cap then
form:add_content("caption",Cap)
end
if markdown then
Url = Url..'&parse_mode='..getParseMode(markdown)
end
data = {}
local c = curl_context:setopt_writefunction(table.insert,data)
:setopt_httppost(form)
:perform()
return table.concat(data), c:getinfo_response_code()
end

function sendPhoto(chat_id,photo,caption, reply_to_message_id)
local Url = BASSE .. '/sendPhoto?'
local curl_command = 'curl "' .. Url .. '" -F "chat_id=' .. chat_id .. '" -F "photo=@' .. photo .. '"'
if reply_to_message_id then
curl_command = curl_command .. ' -F "reply_to_message_id=' .. reply_to_message_id .. '"'
end
if caption then
curl_command = curl_command .. ' -F "caption=' .. caption .. '"'
end
return curlRequest(curl_command)
end

function sendPhotoId(chat_id, file_id, reply_to_message_id, caption)
	local url = BASSE .. '/sendPhoto?chat_id=' .. chat_id .. '&photo=' .. file_id
	if reply_to_message_id then
		url = url..'&reply_to_message_id='..reply_to_message_id
	end
    if caption then
		url = url..'&caption='..caption
	end
	return sendRequest(url)
end
function sendDocumentId(chat_id, file_id, reply_to_message_id, caption, markup)
	local url = BASSE .. '/sendDocument?chat_id=' .. chat_id .. '&document=' .. file_id
	if reply_to_message_id then
		url = url..'&reply_to_message_id='..reply_to_message_id
	end
	if caption then
		url = url..'&caption='..caption
	end
	if markup then
		url = url..'&reply_markup='..URL.escape(JSON.encode(markup))
	end
	return sendRequest(url)
end

function sendAudioId(chat_id, file_id, reply_to_message_id, caption, markup)
	local url = BASSE .. '/sendAudio   ?chat_id=' .. chat_id .. '&audio=' .. file_id
	if reply_to_message_id then
		url = url..'&reply_to_message_id='..reply_to_message_id
	end
	if caption then
		url = url..'&caption='..caption
	end
	if markup then
		url = url..'&reply_markup='..URL.escape(JSON.encode(markup))
	end
	return sendRequest(url)
end


function sendMediaId(chat_id, file_id, media, reply_to_message_id, caption, markup)
	local url = BASSE
	if media == 'voice' then
		url = url..'/sendVoice?chat_id='..chat_id..'&voice='
	elseif media == 'video' then
		url = url..'/sendVideo?chat_id='..chat_id..'&video='
	elseif media == 'photo' then
		url = url..'/sendPhoto?chat_id='..chat_id..'&photo='
	else
		return false, 'Media passed is not voice/video/photo/audio'
	end
	
	url = url..file_id
	
	if reply_to_message_id then
		url = url..'&reply_to_message_id='..reply_to_message_id
	end
	
	if caption then
		url = url..'&caption='..caption
	end
	
	if markup then
		url = url..'&reply_markup='..URL.escape(JSON.encode(markup))
	end

	return sendRequest(url)
end

function sendVoice(chat_id, voice, reply_to_message_id)

	local url = BASSE .. '/sendVoice'

	local curl_command = 'curl "' .. url .. '" -F "chat_id=' .. chat_id .. '" -F "voice=@' .. voice .. '"'

	if reply_to_message_id then
		curl_command = curl_command .. ' -F "reply_to_message_id=' .. reply_to_message_id .. '"'
	end

	if duration then
		curl_command = curl_command .. ' -F "duration=' .. duration .. '"'
	end

	return curlRequest(curl_command)

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
function sendText(chat_id,text, reply_to_message_id,ParseMode,keyboard)
local url = BASSE .. '/sendMessage?chat_id=' .. chat_id .. '&text=' .. URL.escape(text)
url = url .. '&disable_web_page_preview=true'
if reply_to_message_id then
url = url .. '&reply_to_message_id=' .. reply_to_message_id
end
if ParseMode then
url = url..'&parse_mode='..getParseMode(ParseMode)
end
if keyboard then
url = url..'&reply_markup='..URL.escape(JSON.encode(keyboard))
end
return sendRequest(url)
end

function getchat_id(chat_id)
if not chat_id then
print(clr.red..'chat_id is not [getchat_id]'..clr.reset)
return false
end
Url = 'getchat_id?chat_id='..chat_id
local Res = getRes(Url)
if not Res.ok or not Res.result or not Res.result.title then
return {title = '----',type = '----'}
end
return {title = Res.result.title, type = Res.result.title}
end
function deleteMessages(chat_id,msgid)
if not chat_id or not msgid then
print(clr.red..' ERROR => chat_id or msgid [DelMsg].'..clr.red)
return false
end
local Url = '/deleteMessage?chat_id='..chat_id..'&message_id='..msgid
return getRes(Url)
end

function is_JoinChannel(msg)
Url = 'getChatMember?chat_id=@'..Channel..'&user_id='..msg.from.id
rs = getRes(Url)
if not rs.ok then
return true
else
if rs.result then
if rs.result.status then
if rs.result.status:lower() ~= 'left' and rs.result.status:lower() ~= 'kicked' then   
return true
end
if not is_sudo1(msg.from.id) then
bd = 'کاربر :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】\n\n℘ شما ابتدا باید در کانال زیر عضو شوید و سپس مجدد دستور خود را ارسال کنید\n\n℘ نکته : درصورت عضو نشدن ربات به هیچکدام از دستورات عمل نخواهد کرد.'
local keyboard = {}
keyboard.inline_keyboard = {{
{text='براے عضویت در کانال کلیک کنید',url='https://t.me/'..Channel..''},},}   
sendText(msg.chat.id,bd,msg.message_id, 'md',keyboard)
end
end
end
end
return false
end



--------force join-------- 


function send_key(chat_id, text, keyboard, resize, mark, one_time, selective)
response = {}
response.keyboard = keyboard
response.resize_keyboard = resize
response.one_time_keyboard = one_time
response.selective = selective
responseString = JSON.encode(response)
if not mark then
sended = "https://api.telegram.org/bot475351165:AAFxUMXOR3RQxN23LEbONZNIaCRU6D0IfX0/sendMessage?chat_id="..chat_id.."&text="..URL.escape(text).."&disable_web_page_preview=true&reply_markup="..URL.escape(responseString)
else
sended = "https://api.telegram.org/bot475351165:AAFxUMXOR3RQxN23LEbONZNIaCRU6D0IfX0/sendMessage?chat_id="..chat_id.."&text="..URL.escape(text).."&parse_mode=Markdown&disable_web_page_preview=true&reply_markup="..URL.escape(responseString)
	end
	dat, res = HTTPS.request(sended)
	tab = JSON.decode(dat)
	return tab
end

babi = '•'

function getindex(t,id) 
 for i,v in pairs(t) do 
  if v == id then 
   return i 
  end 
 end 
 return nil 
end

function forwardMessage(ChatId,FromChatId, MessageId)	
local url = BASSE.."/forwardMessage?chat_id="..ChatId.."&from_chat_id="..FromChatId.."&message_id="..MessageId
 return sendRequest(url)
end
---------FUNCTION menu----------
function menu(chat_id,page)
if page == 1 then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'تنظیمات گروه',callback_data = 'bd:settings:'..chat_id},{text = babi..'پنل انتی تبچی',callback_data = 'bd:AntiTabchipnl:'..chat_id}
},{
{text= babi..'امار گروه',callback_data = 'bd:Amarr:'..chat_id},
{text= babi..'لیست ها',callback_data = 'bd:groupinfo:'..chat_id}
},{
{text= babi..'تنظیمات فعلی',callback_data = 'bd:Nowsettings:'..chat_id}
},{
{text= babi..'خروج',callback_data = 'bd:Exit:'..chat_id}}}
return keyboard
end end

function help(msg,chat_id,text,Msg,type)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '🛂ارتقا کاربران🛂',callback_data = 'bd:helpkarbari:'..chat_id},
{text = '🎛کنترل کاربران',callback_data = 'bd:helpmodiriat:'..chat_id}
},{
{text = '🔐قفل ها',callback_data = 'bd:helpghofli:'..chat_id},
{text = '⚙تنظیمی',callback_data = 'bd:helpsetting:'..chat_id}
},{
{text = '🎮سرگرمی ها',callback_data = 'bd:helpfun:'..chat_id},
{text = '🔖خوشامدگویی',callback_data = 'bd:helpwel:'..chat_id}
},{
{text = '🚮پاکسازی',callback_data = 'bd:helppaksazi:'..chat_id},
{text = '💠اداجباری',callback_data = 'bd:helpejbari:'..chat_id}
},{
{text = '🛅فیلتر کلمات',callback_data = 'bd:helpfilter:'..chat_id},
{text = '🖥کنترل رگباری',callback_data = 'bd:helpragbari:'..chat_id}
},{
{text = ''..babi..' خروج',callback_data = 'bd:Exhlp:'..chat_id},},}
if type == 'send' then
sendText(chat_id,text,Msg,'md',keyboard)
else
Edit(msg,text,keyboard,true)
end
end


msgsdiamond = {} 
msgsdiamondtd = {} 
adddiamond = {} 
function ForStart(msg,tables,status,chat_id) 
 list = base:smembers(TD_ID..'AllUsers:'..chat_id) 
 for k,v in pairs(list) do 
  GetStatus = tonumber(base:get(TD_ID..status..v)) 
  if base:get(TD_ID..status..v) then 
   table.insert(tables,GetStatus) 
  end 
 end 
end 
function ForSort(msg,tables,text,status) 
 table.sort(tables) 
 GpStatus = tonumber(base:get(TD_ID.."Total:"..status..":"..chat_id) or 0) 
 Text = Text..'*'..text..'* '..GpStatus..'\n' 
end 
function ForNumber(msg,tables,text,status,t2) 
 list = base:smembers(TD_ID.."AllUsers:"..chat_id) 
 for k,v in ipairs(tables) do 
  Number = v 
 end 
 for k,U in pairs(list) do 
  GetStatus = tonumber(base:get(TD_ID..status..U)) 
  if GetStatus == Number then 
 if base:get(TD_ID..status..msg.from.id) and Number then 
  if #tables == 0 then 
   Text = Text 
  else
   Text = Text..'*'..text..'* '..Number..' *'..t2..'* > ['..U..'](tg://user?id='..U..')\n' 
  table.remove(tables,getindex(tables, tonumber(Number))) 
  end 
  end
  end 
 end 
end 
function StatusGp(msg,chat_id) 
bans = base:get(TD_ID..'Total:BanUser:'..chat_id..':'..msg.from.id) or 0
joinss = base:get(TD_ID..'Total:JoinedByLink:'..chat_id) or 0
adds = base:get(TD_ID..'Total:AddUser:'..chat_id) or 0
Msgs = base:get(TD_ID..'Total:messages:'..chat_id..':'..(msg.from.id or 00000000)) or 0

 Emoji = {"↫ ","⇜ ","⌯ ","↜ "} 
 Source_Start = Emoji[math.random(#Emoji)] 
 Text = '*🎗 آمار گروه شما در ساعت* '..os.date("%H:%M:%S")..'\nا┅┅──┄┄═✺═┄┄──┅┅\n'
 ForStart(msg, msgsdiamond,"Total:messages:"..chat_id..":",chat_id) 
 ForSort(msg, msgsdiamond, "🔱 تعد پیام های گروه :", "messages") 
 if #msgsdiamond >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد پیام 👑*\n' 
 end 
 ForNumber(msg, msgsdiamond, "• نفر اول 🎖 :","Total:messages:"..chat_id..":", "پیام") 
 ForNumber(msg, msgsdiamond, "• نفر دوم‌ 🥈 :","Total:messages:"..chat_id..":", "پیام") 
 ForNumber(msg, msgsdiamond, "• نفر سوم 🥉 :","Total:messages:"..chat_id..":", "پیام") 
 ForStart(msg, msgsdiamondtd,"Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":",chat_id) 
 table.sort(msgsdiamondtd) 
 if #msgsdiamondtd >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد پیام های امروز 👑*\n' 
 end 
 ForNumber(msg, msgsdiamondtd, "• نفر اول 🎖 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForNumber(msg, msgsdiamondtd, "• نفر دوم‌ 🥈 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForNumber(msg, msgsdiamondtd, "• نفر سوم 🥉 :","Total:messages:"..chat_id..":"..os.date("%Y/%m/%d")..":", "پیام") 
 ForStart(msg, adddiamond,"Total:AddUser:"..chat_id..":",chat_id) 
 table.sort(adddiamond) 
 if #adddiamond >= 1 then 
  Text = Text..Source_Start..'ا──────────────\n*نفرات برتر در تعداد اد 👑*\n' 
 end 
 ForNumber(msg, adddiamond, "• نفر اول 🎖 :","Total:AddUser:"..chat_id.."", "نفر") 
 ForNumber(msg, adddiamond, "• نفر دوم‌ 🥈 :","Total:AddUser:"..chat_id..":", "نفر") 
 ForNumber(msg, adddiamond, "• نفر سوم 🥉 :","Total:AddUser:"..chat_id..":", "نفر") 
txt = '\nا┅┅──┄┄═✺═┄┄──┅┅\n▸ *اعضای وارد شده : '..joinss..' نفر\n▸ اعضای اضافه شده : '..adds..' نفر\n▸ اعضای مسدود شده : '..bans..' نفر*'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'بازگشت »',callback_data = 'bd:menu:'..chat_id}}}
Edit(msg,Text..txt,keyboard,true)
end
----------****--------><><-------
function StatusGp2(msg,chat_id) 
Text = '⛧ لیست فعالیت مدیران گروه به شرح زیر میباشد ☟ :\n\n'
list = base:smembers(TD_ID.."ModList:"..chat_id) 
for k,v in pairs(list) do
Number = base:get(TD_ID..'Total:messages:'..chat_id..':'..v) or 0
Text = Text..'🆇 `'..v..'` - [کاربر](tg://user?id='..v..') - '..Number..' پیام\n' 
end
if #list == 0 then
Text = 'لیست فعالیت مدیران گروه خالی میباشد'
end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'صفحه قبل »',callback_data = 'bd:groupinfo2:'..chat_id},
{text = '⛧ پاکسازی',callback_data = 'bd:cleanamarr:'..chat_id}}}
Edit(msg,Text,keyboard,true)
end
---------FUNCTION SETTS---------
function SETT(msg,chat_id,page)
dofile('./libs/settings.lua')
settings(msg,chat_id,TD_ID)
if page == 1 then
local edited = base:get(TD_ID..'Lock:Edit'..chat_id)
local fwded = base:get(TD_ID..'Lock:Forward'..chat_id)
local linked = base:get(TD_ID..'Lock:Link'..chat_id)
local usernameed = base:get(TD_ID..'Lock:Username'..chat_id)
local hashtaged = base:get(TD_ID..'Lock:Hashtag'..chat_id)
local markdowned = base:get(TD_ID..'Lock:Markdown'..chat_id)
local captioned = base:get(TD_ID..'Lock:Caption'..chat_id)
local replyed = base:get(TD_ID..'Lock:Reply'..chat_id)

local edit = (edited == "warn") and "اخطار" or ((edited == "ban") and "مسدود" or ((edited == "mute") and "محدود" or (edited == "silent") and "سکوت" or ((edited == "del") and "حذف پیام" or "غیرفعال")))
local fwd = (fwded == "warn") and "اخطار" or ((fwded == "ban") and "مسدود" or ((fwded == "mute") and "محدود" or (fwded == "silent") and "سکوت" or ((fwded == "del") and "حذف پیام" or "غیرفعال")))
local link = (linked == "warn") and "اخطار" or ((linked == "ban") and "مسدود" or ((linked == "mute") and "محدود" or (linked == "silent") and "سکوت" or ((linked == "del") and "حذف پیام" or "غیرفعال")))
local username = (usernameed == "warn") and "اخطار" or ((usernameed == "ban") and "مسدود" or ((usernameed == "mute") and "محدود" or (usernameed == "silent") and "سکوت" or ((usernameed == "del") and "حذف پیام" or "غیرفعال")))
local hashtag = (hashtaged == "warn") and "اخطار" or ((hashtaged == "ban") and "مسدود" or ((hashtaged == "mute") and "محدود" or (hashtaged == "silent") and "سکوت" or ((hashtaged == "del") and "حذف پیام" or "غیرفعال")))
local markdown = (markdowned == "warn") and "اخطار" or ((markdowned == "ban") and "مسدود" or ((markdowned == "mute") and "محدود" or (markdowned == "silent") and "سکوت" or ((markdowned == "del") and "حذف پیام" or "غیرفعال")))
local caption = (captioned == "warn") and "اخطار" or ((captioned == "ban") and "مسدود" or ((captioned == "mute") and "محدود" or (captioned == "silent") and "سکوت" or ((captioned == "del") and "حذف پیام" or "غیرفعال")))
local reply = (replyed == "warn") and "اخطار" or ((replyed == "ban") and "مسدود" or ((replyed == "mute") and "محدود" or (replyed == "silent") and "سکوت" or ((replyed == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه اول بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..link..'',callback_data = chat_id..':Lock:Link:1'},
{text = ''..babi..'️ ️قفل لینک',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..fwd..'',callback_data = chat_id..':Lock:Forward:1'},{text = ''..babi..'️ ️قفل فوروارد',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..username..'',callback_data = chat_id..':Lock:Username:1'},{text = ''..babi..'️ ️قفل یوزرنیم',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..hashtag..'',callback_data = chat_id..':Lock:Hashtag:1'},{text = ''..babi..'️ ️قفل هشتگ',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:Next1:'..chat_id}
},{
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 2 then
local arabiced = base:get(TD_ID..'Lock:Persian'..chat_id)
local englished = base:get(TD_ID..'Lock:English'..chat_id)
foshed = base:get(TD_ID..'Lock:Fosh'..chat_id)

locationed = base:get(TD_ID..'Lock:Location'..chat_id)

local arabic = (arabiced == "warn") and "اخطار" or ((arabiced == "ban") and "مسدود" or ((arabiced == "mute") and "محدود" or (arabiced == "silent") and "سکوت" or ((arabiced == "del") and "حذف پیام" or "غیرفعال")))
local english = (englished == "warn") and "اخطار" or ((englished == "ban") and "مسدود" or ((englished == "mute") and "محدود" or (englished == "silent") and "سکوت" or ((englished == "del") and "حذف پیام" or "غیرفعال")))
local fosh = (foshed == "warn") and "اخطار" or ((foshed == "ban") and "مسدود" or ((foshed == "mute") and "محدود" or (foshed == "silent") and "سکوت" or ((foshed == "del") and "حذف پیام" or "غیرفعال")))
local location = (locationed == "warn") and "اخطار" or ((locationed == "ban") and "مسدود" or ((locationed == "mute") and "محدود" or (locationed == "silent") and "سکوت" or ((locationed == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه دوم بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..arabic..'',callback_data = chat_id..':Lock:Persian:2'},
{text = ''..babi..'️ ️قفل فارسی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..english..'',callback_data = chat_id..':Lock:English:2'},{text = ''..babi..'️ ️قفل انگلیسی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..fosh..'',callback_data = chat_id..':Lock:Fosh:2'},{text = ''..babi..'️ ️قفل فحش',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..location..'',callback_data = chat_id..':Lock:Location:2'},{text = ''..babi..'️ ️قفل موقعیت',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه قبل »',callback_data = 'bd:settings:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:Next2:'..chat_id}
},{
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id},
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 3 then
local txtsed = base:get(TD_ID..'Lock:Text'..chat_id)
local contacted = base:get(TD_ID..'Lock:Contact'..chat_id)
local gifed = base:get(TD_ID..'Lock:Gif'..chat_id)
local photoed = base:get(TD_ID..'Lock:Photo'..chat_id)
local txts = (txtsed == "warn") and "اخطار" or ((txtsed == "ban") and "مسدود" or ((txtsed == "mute") and "محدود" or (txtsed == "silent") and "سکوت" or ((txtsed == "del") and "حذف پیام" or "غیرفعال")))
local gif = (gifed == "warn") and "اخطار" or ((gifed == "ban") and "مسدود" or ((gifed == "mute") and "محدود" or (gifed == "silent") and "سکوت" or ((gifed == "del") and "حذف پیام" or "غیرفعال")))
local photo = (photoed == "warn") and "اخطار" or ((photoed == "ban") and "مسدود" or ((photoed == "mute") and "محدود" or (photoed == "silent") and "سکوت" or ((photoed == "del") and "حذف پیام" or "غیرفعال")))
local contact = (contacted == "warn") and "اخطار" or ((contacted == "ban") and "مسدود" or ((contacted == "mute") and "محدود" or (contacted == "silent") and "سکوت" or ((contacted == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه سوم بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..txts..'',callback_data = chat_id..':Lock:Text:3'},
{text = ''..babi..'️ ️قفل متن',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..gif..'',callback_data = chat_id..':Lock:Gif:3'},{text = ''..babi..'️ ️قفل گیف',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..contact..'',callback_data = chat_id..':Lock:Contact:3'},{text = ''..babi..'️ ️قفل مخاطب',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..photo..'',callback_data = chat_id..':Lock:Photo:3'},{text = ''..babi..'️ ️قفل عکس',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه قبل »',callback_data = 'bd:Next1:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:Next3:'..chat_id}
},{
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id},
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 4 then
local videoed = base:get(TD_ID..'Lock:Video'..chat_id)
local voiceed = base:get(TD_ID..'Lock:Voice'..chat_id)
local musiced = base:get(TD_ID..'Lock:Music'..chat_id)
local gameed = base:get(TD_ID..'Lock:Game'..chat_id)
local voice = (voiceed == "warn") and "اخطار" or ((voiceed == "ban") and "مسدود" or ((voiceed == "mute") and "محدود" or (voiceed == "silent") and "سکوت" or ((voiceed == "del") and "حذف پیام" or "غیرفعال")))
local game = (gameed == "warn") and "اخطار" or ((gameed == "ban") and "مسدود" or ((gameed == "mute") and "محدود" or (gameed == "silent") and "سکوت" or ((gameed == "del") and "حذف پیام" or "غیرفعال")))
local video = (videoed == "warn") and "اخطار" or ((videoed == "ban") and "مسدود" or ((videoed == "mute") and "محدود" or (videoed == "silent") and "سکوت" or ((videoed == "del") and "حذف پیام" or "غیرفعال")))
local music = (musiced == "warn") and "اخطار" or ((musiced == "ban") and "مسدود" or ((musiced == "mute") and "محدود" or (musiced == "silent") and "سکوت" or ((musiced == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه چهارم بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..video..'',callback_data = chat_id..':Lock:Video:4'},
{text = ''..babi..'️ ️قفل فیلم',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..voice..'',callback_data = chat_id..':Lock:Voice:4'},{text = ''..babi..'️ ️قفل صدا',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..music..'',callback_data = chat_id..':Lock:Music:4'},{text = ''..babi..'️ ️قفل موزیک',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..game..'',callback_data = chat_id..':Lock:Game:4'},{text = ''..babi..'️ ️قفل بازی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه قبل »',callback_data = 'bd:Next2:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:Next4:'..chat_id}
},{
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id},
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 5 then
local inlineed = base:get(TD_ID..'Lock:Inline'..chat_id)
local stickered = base:get(TD_ID..'Lock:Sticker'..chat_id)
local video_noteed = base:get(TD_ID..'Lock:Selfi'..chat_id)
local documented = base:get(TD_ID..'Lock:Document'..chat_id)
local inline = (inlineed == "warn") and "اخطار" or ((inlineed == "ban") and "مسدود" or ((inlineed == "mute") and "محدود" or (inlineed == "silent") and "سکوت" or ((inlineed == "del") and "حذف پیام" or "غیرفعال")))
local sticker = (stickered == "warn") and "اخطار" or ((stickered == "ban") and "مسدود" or ((stickered == "mute") and "محدود" or (stickered == "silent") and "سکوت" or ((stickered == "del") and "حذف پیام" or "غیرفعال")))
local document = (documented == "warn") and "اخطار" or ((documented == "ban") and "مسدود" or ((documented == "mute") and "محدود" or (documented == "silent") and "سکوت" or ((documented == "del") and "حذف پیام" or "غیرفعال")))
local selfi = (video_noteed == "warn") and "اخطار" or ((video_noteed == "ban") and "مسدود" or ((video_noteed == "mute") and "محدود" or (video_noteed == "silent") and "سکوت" or ((video_noteed == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه پنجم بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..sticker..'',callback_data = chat_id..':Lock:Sticker:5'},
{text = ''..babi..'️ ️قفل استیکر',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..document..'',callback_data = chat_id..':Lock:Document:5'},{text = ''..babi..'️ ️قفل فایل',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..inline..'',callback_data = chat_id..':Lock:Inline:5'},{text = ''..babi..'️ ️قفل اینلاین',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..selfi..'',callback_data = chat_id..':Lock:Selfi:5'},{text = ''..babi..'️ ️قفل سلفی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه قبل »',callback_data = 'bd:Next3:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:Next5:'..chat_id}
},{
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id},
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 6 then
if base:sismember(TD_ID..'Gp:'..chat_id,'Cmd') then
cmd = 'فعال'
else
cmd = 'غیرفعال'
end
if base:sismember(TD_ID..'Gp:'..chat_id,'TGservice') then
tg = 'فعال'
else
tg = 'غیرفعال' 
end
if base:sismember(TD_ID..'Gp:'..chat_id,'Join') then
joins = 'فعال'
else
joins = 'غیرفعال' 
end

local boted = base:get(TD_ID..'Lock:Bots'..chat_id)
local voteed = base:get(TD_ID..'Lock:Vote'..chat_id)
local bot = (boted == "warn") and "اخطار" or ((boted == "ban") and "مسدود" or ((boted == "mute") and "محدود" or (boted == "silent") and "سکوت" or ((boted == "del") and "حذف پیام" or "غیرفعال")))
local vote = (voteed == "warn") and "اخطار" or ((voteed == "ban") and "مسدود" or ((voteed == "mute") and "محدود" or (voteed == "silent") and "سکوت" or ((voteed == "del") and "حذف پیام" or "غیرفعال")))
local text = '⛧ به صفحه شیشم بخش تنظیمات حالتی خوش امدید ، برای تنظیم قفل ها بر روی #دکمه مورد نظر کلیک کنید‣'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..cmd..'',callback_data = chat_id..':locks2:Cmd:6'},
{text = ''..babi..'️ ️قفل دستورات',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..bot..'',callback_data = chat_id..':Lock:Bots:6'},
{text = ''..babi..'️ ️قفل ربات',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..tg..'',callback_data = chat_id..':locks2:TGservice:6'},
{text = ''..babi..'️ ️قفل خدمات',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..joins..'',callback_data = chat_id..':locks2:Join:6'},
{text = ''..babi..'️ ️قفل ورودی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = ''..vote..'',callback_data = chat_id..':Lock:Vote:6'},
{text = ''..babi..'️ ️قفل نظرسنجی',callback_data = 'bd:ERROR:'..chat_id}
},{
{text = 'صفحه قبل »',callback_data = 'bd:Next4:'..chat_id},
{text = 'صفحه اصلی »',callback_data = 'bd:menu:'..chat_id}
},{
{text = ''..babi..' خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif page == 7 then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' مالکان',callback_data = 'bd:ownerlist:'..chat_id},
{text = ''..babi..' مدیران',callback_data = 'bd:modlist:'..chat_id}
},{
{text = ''..babi..' ویژه ها',callback_data = 'bd:viplist:'..chat_id},
{text = ''..babi..' فیلتری ها',callback_data = 'bd:filterpanel:'..chat_id}
},{
{text = ''..babi..' بازگشت',callback_data = 'bd:menu:'..chat_id},
{text = '« صفحه بعد',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,'⛧ به پنل تنظیمات لیستی ربات خوش امدید ، لطفا بر روی #دکمه مورد نظر کلیک کنید ‣',keyboard,true)
elseif page == 8 then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' اخطاری ها',callback_data = 'bd:warnlist:'..chat_id}
},{
{text = ''..babi..' سکوتی ها',callback_data = 'bd:mutelist:'..chat_id},
{text = ''..babi..' فعالیت ها',callback_data = 'bd:Falit:'..chat_id}
},{
{text = ''..babi..' مسدودی ها',callback_data = 'bd:banlist:'..chat_id}
},{
{text = ''..babi..' بازگشت',callback_data = 'bd:menu:'..chat_id},
{text = '» صفحه قبل',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,'⛧ به صفحه دوم تنظیمات لیستی ربات خوش امدید ، لطفا بر روی #دکمه مورد نظر کلیک کنید ‣',keyboard,true)
elseif page == 9 then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' قفل های فعال',callback_data = 'bd:activelock:'..chat_id},
{text = ''..babi..' وضعیت گروه ',callback_data = 'bd:status1:'..chat_id},
},{
{text = ''..babi..' تنظیمات گروه',callback_data = 'bd:settings:'..chat_id},{text = ''..babi..'خوشامدگویی ',callback_data = 'bd:Wlc:'..chat_id}
},{
{text = ''..babi..' بازگشت',callback_data = 'bd:menu:'..chat_id},
{text = 'خروج',callback_data = 'bd:Exit:'..chat_id}}}
Edit(msg,'▴ به بخش تنظیمات ربات خوش امدید ، لطفا برای استفاده بر روی #دکمه بخش مورد نظر خود کلیک کنید ▸ ‣',keyboard,true)
elseif page == 10 then
MSG_MAX = (base:get(TD_ID..'Flood:Max:'..chat_id) or 6)
CH_MAX = (base:get(TD_ID..'NUM_CH_MAX:'..chat_id) or 2000)
TIME_CHECK = (base:get(TD_ID..'Flood:Time:'..chat_id) or 2)
warn = (base:get(TD_ID..'Warn:Max:'..chat_id) or 3)

if base:get(TD_ID.."bot:muteall:Time" ..chat_id) then
auto = 'فعال'
else
auto = 'غیرفعال'
end

local START = base:get(TD_ID.."bot:muteall:start" .. chat_id) or 'تنظیم نشده'
local STOP = base:get(TD_ID.."bot:muteall:stop" .. chat_id) or 'تنظیم نشده'
gplink = getChat(chat_id).result.invite_link or base:get(TD_ID..'Link:'..chat_id) or 'لینک گروه ثبت نشده است'
rules = base:get(TD_ID..'Rules:'..chat_id) or 'قوانین گروه ثبت نشده است'
if base:get(TD_ID..'Lock:Flood'..chat_id) then
Flood = 'فعال'
else
Flood = 'غیرفعال'
end
if base:get(TD_ID..'Lock:Spam'..chat_id) then
Spam = 'فعال'
else
Spam = 'غیرفعال'
end
cbmtim = base:get(TD_ID..'cbmtime:'..chat_id) or 10
Times = base:get(TD_ID..'Times_ForceAdd:'..chat_id) or 30
Times_2 = base:get(TD_ID..'Times_Welcome:'..chat_id) or 30
Times_3 = base:get(TD_ID..'Times_MsgCheckPm:'..chat_id) or 10
if base:sismember(TD_ID..'Gp2:'..chat_id,'cl_forceadd') then
onf = 'فعال✓'
else
onf = 'غیرفعال'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'cl_welcome') then
onw = 'فعال✓'
else
onw = 'غیرفعال'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'cl_msgcheckpm') then
onm = 'فعال✓'
else
onm = 'غیرفعال'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'Welcomeon') then
welcome = 'فعال'
else
welcome = 'غیرفعال'
end
weltext = base:get(TD_ID..'Text:Welcome:'..chat_id) or '*متن خوش امدگوے گروه ثبت نشده است*'
txt = [[
• وضعیت گروه عبارتند از :

⊰ قفل رگبار : ]]..Flood..[[

⊰ مقدار ارسال رگبار : ]]..MSG_MAX..[[

⊰ زمان حساسیت رگبار : ]]..TIME_CHECK..[[

⊰ قفل هرزنامه : ]]..Spam..[[

⊰ حساسیت هرزنامه : ]]..CH_MAX..[[

⊰ قفل گروه : ]]..muteall..[[

⊰ قفل خودکار : ]]..auto..[[

⊰ ساعات تعطیلی : ]]..START..[[ تا ]]..STOP..[[


⊰ حداکثر اخطار : ]]..warn..[[

⊰ خوشامدگویی : ]]..welcome..[[

⊰ خلاصه خوشامدگویی :  ]]..weltext..[[

⊰ و.ق فوروارد : حالت کلی
⊰ و.ق یوزرنیم : حالت کلی

⊰ حذف پیام ربات : ]]..onw..[[

⊰ زمان حذف پیام ربات : ]]..Times_2..[[
⊰ لینک گروه : ]]..gplink..[[

⊰ خلاصه قوانین : ]]..rules..[[

⊰ آیدی عددی گروه : ]]..chat_id..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:Nowsettings:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif page == 11 then
if base:sismember(TD_ID..'Gp2:'..chat_id,'Welcomeon') then
w = '【✅】'
else
w = '【✗】'
end
if base:get(TD_ID.."wl_status:"..chat_id) then
if base:get(TD_ID.."wl_status:"..chat_id) == "gif" then
if lang then
sts = 'gif'
else
sts = 'خوشامدگویی با گیف'
end
elseif base:get(TD_ID.."wl_status:"..chat_id) == "text" then
if lang then
sts = 'text'
else
sts = 'خوشامدگویی با متن'
end

elseif base:get(TD_ID.."wl_status:"..chat_id) == "music" then
if lang then
sts = 'music'
else
sts = 'خوشامدگویی با موزیک'
end
elseif base:get(TD_ID.."wl_status:"..chat_id) == "photo" then
if lang then
sts = 'photo'
else
sts = 'خوشامدگویی با عکس'
end
end
else
sts = 'تنظیم نشده'
end
wel = base:get(TD_ID..'Text:Welcome:'..chat_id) or '*متن خوش امدگوے گروه ثبت نشده است*'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'وضعیت | '..w..'',callback_data = chat_id..':locks2:Welcomeon:11'}},{
{text = '↡حالت خوشامدگویی↡',callback_data = 'bd:ERROR:'..chat_id}},{
{text = '|'..sts..'|',callback_data = 'bd:WlcStatus:'..chat_id}},{
{text = babi..'حذف متن خوشامدگویی',callback_data = 'bd:Delwelcome:'..chat_id}},{
{text = '⌆ بازگشت',callback_data = 'bd:Nowsettings:'..chat_id},},}
Edit(msg,wel,keyboard,true)


elseif page == 15 then
if base:sismember(TD_ID..'Gp2:'..chat_id,'forceadd') then
forceadd = '【✓】'
else
forceadd = '【✗】'
end
Add_MAX = tonumber(base:get(TD_ID..'Force:Max:'..chat_id) or 10)
forcepm = tonumber(base:get(TD_ID..'Force:Pm:'..chat_id) or 3)
lang = base:sismember(TD_ID..'Gp2:'..chat_id,'diamondlang')
if base:sismember(TD_ID..'Gp2:'..chat_id,'force_NewUser') then
if lang then
babak = 'New User✓'
mamad = 'All User'
else
babak = 'کاربران جدید✓'
mamad = 'همه کاربران'
end
else
if lang then
babak = 'New User'
mamad = 'All User✓'
else
babak = 'کاربران جدید'
mamad = 'همه کاربران✓'
end
end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'افزودن اجبارے | '..forceadd..'', callback_data = chat_id..':locks2:forceadd:5'}},{
{text=babi..'تعداد افزودن اجبارے : | '..tostring(Add_MAX)..' |',callback_data='bd:ERROR:'..chat_id}},{
{text='🔺',callback_data='bd:AddMAXup:'..chat_id},{text='🔻',callback_data='bd:AddMAXdown:'..chat_id}},{
{text=babi..'تعداد اخطار پیام افزودن اجبارے : | '..tostring(forcepm)..' |',callback_data='bd:ERROR:'..chat_id}},{
{text='🔺',callback_data='bd:forcepmMAXup:'..chat_id},{text='🔻',callback_data='bd:forcepmMAXdown:'..chat_id}},{
{text = 'وضعیت افزودن اجبارے',callback_data = 'bd:ERROR:'..chat_id}},{
{text = babak,callback_data = 'bd:newuser-:'..chat_id},{text = mamad,callback_data = 'bd:alluser+:'..chat_id}
},{
{text = '⌆ بازگشت',callback_data = 'bd:menu:'..chat_id},},}
Edit(msg,''..babi..' کاربر :['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')\n℘ فهرست تنظیمات افزودن اجبارے خوش آمدید\n─┅┈┅┅┈┅┈╮✦✧╭┈┅┈┅┅┈┅─\n℘ براے استفاده از تنظیمات روے گزینه مورد نظر خود کلیک کنید',keyboard,true)

elseif page == 16 then
if base:sismember(TD_ID..'Gp2:'..chat_id,'forcejoin') then
forcejoin = '【✅】'
else
forcejoin = '【✗】'
end
ch = (base:get(TD_ID..'setch:'..chat_id) or Config.Channel)
JoinWarn = (base:get(TD_ID..'joinwarn:'..chat_id) or 4)

local keyboard = {}							keyboard.inline_keyboard = {{
{text = babi..'عضویت اجبارے | '..forcejoin..'', callback_data = chat_id..':locks2:forcejoin:6'}},{
{text = babi..'کانال عضویت اجبارے',url='https://t.me/'..ch}},{
{text=babi..'تعداد اخطار عضویت اجبارے : | '..tostring(JoinWarn)..' |',callback_data='bd:ERROR:'..chat_id}},{
{text='🔺',callback_data='bd:JoinWarnMAXup:'..chat_id},{text='🔻',callback_data='bd:JoinWarnMAXdown:'..chat_id}},{
{text = '⌆ بازگشت',callback_data = 'bd:menu:'..chat_id},},}
Edit(msg,''..babi..' کاربر :['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')\n℘ به فهرست تنظیمات عضویت اجبارے خوش آمدید\n─┅┈┅┅┈┅┈╮✦✧╭┈┅┈┅┅┈┅─\n℘ براے استفاده از تنظیمات روے گزینه مورد نظر خود کلیک کنید',keyboard,true)
end
end
function Cbm(msg,chat_id)
dofile('./libs/settings.lua')
settings(msg,chat_id,TD_ID)
lang = base:sismember(TD_ID..'Gp2:'..chat_id,'diamondlang')
if base:sismember(TD_ID..'Gp2:'..chat_id,'cbmon') then
babak = 'فعال✓'
mamad = 'غیرفعال'
else
babak = 'فعال'
mamad = 'غیرفعال✓'
end

local keyboard = {}
keyboard.inline_keyboard = {{
{text = '↡پاکسازی پیام های ربات↡',callback_data = 'bd:Cbm:'..chat_id}},{
{text = babak,callback_data = 'bd:cbm+:'..chat_id},{text = mamad,callback_data = 'bd:cbm-:'..chat_id}},{
{text=babi..'زمان پاکسازی : | '..tostring(timecgms)..' |',callback_data='bd:ERROR:'..chat_id}},{
{text='✚',callback_data='bd:timecgmsMAXup:'..chat_id},{text='▬',callback_data ='bd:timecgmsMAXdown:'..chat_id}},{
{text=babi..' زمان پاکسازی افزودن اجباری : | '..tostring(Times)..' | ثانیه',callback_data='bd:ERROR:'..chat_id}},{
{text='✚',callback_data='bb:timesforceaddMAXup:'..chat_id},{text = onf,callback_data = 'bb:cfm:'..chat_id},{text='▬',callback_data='bb:timesforceaddMAXdown:'..chat_id}},{
{text=babi..' زمان پاکسازی خوش امدگویی : | '..tostring(Times_2)..' | ثانیه',callback_data='bb:ERROR:'..chat_id}},{
{text='✚',callback_data='bb:timeswelcomeMAXup:'..chat_id},{text = onw,callback_data = 'bb:cwm:'..chat_id},{text='▬',callback_data='bb:timeswelcomeMAXdown:'..chat_id}},{
{text=babi..' زمان پاکسازی مسیج چک : | '..tostring(Times_3)..' | ثانیه',callback_data='bb:ERROR:'..chat_id}},{
{text='✚',callback_data='bb:timesmsgcheckpmMAXup:'..chat_id},{text = onm,callback_data = 'bb:cmm:'..chat_id},{text='▬',callback_data='bb:timesmsgcheckpmMAXdown:'..chat_id}},{
{text = '🔙 بازگشت',callback_data = 'bd:newsetting:'..chat_id},},}
Edit(msg,''..babi..' به پنل پاکسازی خودکار پیام های ربات خوش آمدید',keyboard,true)
end

function filters(msg,chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'لیست کلمات فیلتر شده',callback_data = 'bd:filterlist:'..chat_id}},{
{text = 'کلمه به لیست فیلتر ✚',callback_data = 'bd:addfilter:'..chat_id},
{text = 'کلمه از لیست فیلتر ▬',callback_data = 'bd:remfilter:'..chat_id}},{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,'پنل فیلتر کلمات',keyboard,true)
end

function AntiTabchiPnl(msg,chat_id)
if chat_id then
if base:sismember(TD_ID..'Gp2:'..chat_id,'AntiTabchi') then
mmd = '✓'
else
mmd = '✗'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
reza = 'محدودیت✓'
rosta ='اخراج'
else
reza = 'محدودیت'
rosta = 'اخراج✓'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'BioAntiTabchi') then
Bio = '✓'
else
Bio = '✗'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'NameAntiTabchi') then
Name = '✓'
else
Name = '✗'
end
if base:sismember(TD_ID..'Gp2:'..chat_id,'FirstTabchiMute') then
mute = '✓'
else
mute = '✗'
end
local keyboard = {}
keyboard.inline_keyboard = {
{{text = mmd..'احراز هویت'..mmd,callback_data = 'bd:ehrazhovit:'..chat_id}},
{{text = Name..'حساسیت به اسم'..Name,callback_data = 'bd:nameanti:'..chat_id},{text = Bio..'حساسیت به بیوگرافی'..Bio,callback_data = 'bd:bioanti:'..chat_id}},{
{text = '---------⌄ مجازات ⌄---------',callback_data = 'bd:Error404:'..chat_id}},
{{text = reza,callback_data = 'bd:modeehrazm:'..chat_id},{text = rosta,callback_data = 'bd:modeehrazk:'..chat_id}},
{{text = '🔙 برگشت',callback_data = 'bd:menu:'..chat_id}}}
Edit(msg,'🤖به پنل احراز هویت (انتی تبچی) خوش امدید !\n\n❗️در صورت فعال بودن احراز هویت اعضایی که وارد گروه میشوند باید به سوال ربات پاسخ دهند تا ربات نبودن آن ها ثابت شود !\n\n❓سطر اخر مشخص میکند اگر کاربر ربات شناخته شد اخراج شود یا محدود از ارسال پیام ...\n\n🔧شما میتوانید با دستور\n(فیلتر اسم بابک)\n>نام بابک را در لیست اسامی غیرمجاز قرار دهید تا به محض ورود ربات تلقی شود.\n\n🔧همچنین میتوانید با دستور\n(فیلتر بیو متن بابک)\n>بیوگرافی متن بابک را جزء بیوگرافی هاے غیرمجاز قرار دهید تا در صورتی که محتواے بیوگرافی کاربر شامل متن بابک بود ربات تلقی شود.',keyboard,true) 
end
end

function STARTS(msg,chat_id,Msg,page,type)
if page == 1 then
txt = [[
⛧ به برترین ربات #مدیریت گروه با امکانات بی نظیر و سرعت بالا و همیشه انلاین خوش آمدید.

⛧ راه های #ارتباطی و راهنمای ربات :

>> ربات ما یک ربات همه کاره واسه مدیریت بهتر گروه و #محافظت از هرگونه مخرب کننده است  

>> ️پر امکانات ترین و پر #سرعترین ربات درحال حاضره تلگرام با بیشترین قابلیت و مدیریت عالی حتی در گروه 200 هزار نفره ▸


>> برای کسب #اطلاعات بیشتر درباره ربات لطفا از #دکمه های زیر استفاده کنید 👇
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' اضافه کردن ربات به گروه',url = 'https://t.me/'..botusername..'/?startgroup=new'}},{
{text = ''..babi..' امکانات ربات',callback_data = 'cb:Emkanat:'..chat_id}},{
{text = ''..babi..' کانال دستورات ربات',url = 'https://t.me/'..Channel}
},{
{text = ''..babi..' آموزش افزودن ربات',callback_data = 'cb:amozesh:'..chat_id}},{
{text = ''..babi..' سازنده ربات',url = 'https://t.me/'..Config.LinkSuppoRt}
}}
if type == 'send' then
sendText(chat_id,txt,Msg,'md',keyboard)
else
EdiT(msg,txt,keyboard,true)
end
elseif page == 2 then
txt2 = 'برترین ربات مدیریت گروه با امکانات بی نظیر و سرعت بالا و همیشه انلاین خوش آمدید\n\n⛧ راه های ارتباطی و راهنمای ربات👇👇 👇👇👇👇:\n● '..escape_markdown(Config.LinkSuppoRt)..'\n\n👆👆👆👆👆👆\n\nکانال ما : @'..escape_markdown(Channel)..'\n\n>> ️ربات ما یک ربات همه کاره واسه مدیریت بهتر گروه و محافظت از هرگونه مخرب کننده است ▸\n\n>> ️پر امکانات ترین و پر سرعترین ربات درحال حاضره تلگرام با بیشترین قابلیت و مدیریت عالی حتی در گروه 200 هزار نفره ▸\n\n>> برای کسب اطلاعات بیشتر درباره ربات لطفا از دکمه های زیر استفاده کنید :\n\n● @'..escape_markdown(Channel)..' : راهنمای  نصب ◄\n\n>> ربات مدیریت گروه | ضدلینک و پاکسازی <<\n■ #قابلیت قفل کلی گروه\n■ #قابلیت قفل خودکار گروه\n•• قابلیت پاکسازی پیام های گروه بصورت کلی یا تعدادی بدون هیچگونه محدودیتی!\n■ #قابلیت قفل عکس، فیلم، فیلم سلفی، موزیک،  ویس\n■ #قابلیت قفل فایل، استیکر، گیف، مخاطب، مکان\n■ #قابلیت جلوگیری از ارسال لینک\n■ #قابلیت جلوگیری از ارسال لینک سایت ها\n■ #قابلیت جلوگیری از ارسال آیدی\n■ #قابلیت جلوگیری از ارسال لینک های شیشه ای\n■ #قابلیت قفل ورود ربات ها\n■ #قابلیت اخراج ربات های مخرب\n■ #قابلیت اخراج فرد اضافه کننده ربات\n■ #قابلیت حذف پیام های ورود و خروج گروه\n■ #قابلیت ادد اجباری گروه\n■ #قابلیت اجبار عضویت در کانال شما\n■ #قابلیت حذف پیام اجباری بعد از چندثانیه\n■ #قابلیت معاف اجباری کاربر\n■ #قابلیت فیلترکردن کلمات در گروه\n■ #قابلیت نمایش خوش آمد گویی\n■ #قابلیت تنظیم متن خوش آمد گویی\n■ #قابلیت حذف پیام خوش آمدگویی بعد از چند ثانیه\n■ #قابلیت حذف دلیت اکانت های گروه\n■ #قابلیت حذف لیست سیاه گروه\n■ #قابلیت جلوگیری از ارسال پست\n■ #قابلیت جلوگیری از ارسال پست تکراری\n■ #قابلیت جلوگیری از ارسال پیام های رگباری\n■ #قابلیت جلوگیری از ارسال پیام های طولانی\n■ #قابلیت محدودیت ارسال پیام برای کاربران\n■ #قابلیت پاکسازی لیست ها\n■ #قابلیت مسدود کاربر\n■ #قابلیت اخراج کاربر\n■ #قابلیت بیصدا کردن کاربران\n■ #قابلیت بیصدا کردن کاربران بصورت ساعتی\n■ #قابلیت اخطار به کاربران\n■ #قابلیت قفل ورود عضو به گروه\n■ #قابلیت سنجاق پیام\n■ #قابلیت قفل هشتگ\n■ #قابلیت قفل فونت\n■ #قابلیت قفل ویرایش پیام\n■ #قابلیت قفل ریپلای\n■ #قابلیت قفل ارسال بازی\n■ #قابلیت قفل زبان انگلیسی\n■ #قابلیت قفل زبان فارسی\n■ #قابلیت نمایش لینک گروه\n■ #قابلیت نمایش قوانین گروه\n■ #قابلیت راهنما ربات بصورت دکمه شیشه ای\n■ #قابلیت افزودن مدیر به گروه و عزل مدیر گروه ٭{ مناسب افراد ریپورتی}\n■ #قابلیت تعیین ادمینها برای مدیریت ربات\n■ #قابلیت عزل ادمینهای گروه\n٭{ جهت جلوگیری از دخالت در تنظیمات ربات }\n■ #قابلیت ارتقا به عضو ویژه\n■ #قابلیت نمایش اطلاعات گروه\n■ #قابلیت نمایش آمار گروه{ نفرات برتر، ورود عضو}\n■ #قابلیت ساخت گیف\n■ #قابلیت ساخت عکس به استیکر و بالعکس\n■ #قابلیت اکو متن\n■ #قابلیت نمایش شناسه\n■ #قابلیت نمایش زمان و تاریخ\n■ #قابلیت پنل شیشه ای\n■ #قابلیت تنظیم ربات با زبان فارسی و انگلیسی\n■ #قابلیت وضعیت انلاینی ربات\nCh: @'..escape_markdown(Channel)..''
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'cb:STARTS:'..chat_id}}}
EdiT(msg,txt2,keyboard,true)
end
end 

function Sends(msg,chat_id,Msg,type)
local keyboard = {}
keyboard.inline_keyboard = {{
{text='• ارسال به پیوی',callback_data = 'snd:SendPv:'..chat_id}
},{
{text='• ارسال به سوپرگروه',callback_data = 'snd:SendSuperGroups:'..chat_id}
},{
{text='• ارسال به مالکان',callback_data = 'snd:SendOwner:'..chat_id}
},{
{text='• ارسال به ادمین ها',callback_data = 'snd:SendAdmin:'..chat_id}
},{
{text='• فوروارد به پیوی',callback_data = 'snd:FwdPv:'..chat_id}
},{
{text='• فوروارد به سوپرگروه',callback_data = 'snd:FwdSuperGroups:'..chat_id}
},{
{text='• فوروارد به مالکان',callback_data = 'snd:FwdOwner:'..chat_id}
},{
{text='• فوروارد به ادمین ها', callback_data = 'snd:FwdAdmin:'..chat_id}
},{
{text='• بستن پنل ارسال',callback_data = 'bd:Exit:-229445008'}}}
local txt = '• لطفاً گزینه مورد نظر خود را انتخاب کنید :'
if type == 'send' then
sendText(chat_id,txt,Msg,'md',keyboard)
else
EdiT(msg,txt,keyboard,true)
end
end

function xSends(msg,chat_id,Msg,txt1,txt2,redis)
local txt = '• لطفا متن خود را جهت '..txt1..' به '..txt2..' را ارسال کنید\n\n🔙 درصورت لغو این فرایند روی بازگشت کلیک کنید'
local keyboard = {}
keyboard.inline_keyboard = {{
{text='• بازگشت',callback_data = 'snd:Sends:'..chat_id}}}
base:setex(TD_ID..''..redis..''..chat_id,60,tonumber(Msg))
EdiT(msg,txt,keyboard,true)
end

function EditSends(msg,chat_id,Msg,txt1,txt2,redis1,redis2,baner,type)
local snd = base:get(TD_ID..''..redis1..''..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'snd:Sends:'..chat_id}}}
base:del(TD_ID..''..redis1..''..chat_id)
local list = base:smembers(TD_ID..''..redis2..'')
nms = 0
for k,v in pairs(list) do
if type == 'send' then
sendText(v,baner,0,'html')
elseif type == 'edit' then
local f = forwardMessage(v,chat_id,msg.message_id)
end
nms = nms + 1
end
editMessageText(chat_id,tonumber(snd),'◄ بنر شما با موفقیت به *'..nms..'* '..txt1..' #'..txt2..' شد',keyboard,'md')
end

function EditSends_(msg,chat_id,Msg,txt1,txt2,redis1,redis2,redis3,baner,type)
local snd = base:get(TD_ID..''..redis1..''..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'snd:Sends:'..chat_id}}}
base:del(TD_ID..''..redis1..''..chat_id)
local list = base:smembers(TD_ID..''..redis2..'')
nmss = 0
for k,v in pairs(list) do
local status = getChatAdministrators(v)
if status and status.result then
for m,n in ipairs(status.result) do
if n.status == ''..redis3..'' then
if n.user.id then
uid = n.user.id
end end end
if type == 'send' then
sendText(uid,baner,0,'html')
elseif type == 'edit' then
local fp =  forwardMessage(tonumber(uid),chat_id,tonumber(msg.message_id))
vardump(fp)
end
end
nmss = nmss + 1
end
editMessageText(chat_id,tonumber(snd),'◄ بنر شما با موفقیت به *'..nmss..'* '..txt1..' #'..txt2..' شد',keyboard,'md')
end
--<><>--<><>--<><><>--<><>--<><>--<><>--<><>--
function Runing(msg)
if msg.service then

if msg.new_chat_members then
if base:sismember(TD_ID..'Gp2:'..msg.chat.id,'Welcomeon') and not msg.left_chat_member then
if #msg.new_chat_members == 1 then
Res = getChatAdministrators(msg.chat.id)
local AdminNum = 0
for i=1, #Res.result do     
AdminNum = AdminNum+1
end
link = getChat(msg.chat.id).result.invite_link or base:get(TD_ID..'Link:'..msg.chat.id) or 'لینک گروه ثبت نشده است'

rules = base:get(TD_ID..'Rules:'..msg.chat.id) or 'قوانین گروه ثبت نشده است'
date = ''..jdates('#x')..'\n'..jdates('#D-#X-#Y')..''
local status = getChatAdministrators(msg.chat.id)
for m,n in ipairs(status.result) do
if n.status == 'creator' then
if n.user.id then
owner_id = n.user.id
owner =  n.user.first_name
end end end
full = ''..(msg.new_chat_members[1].first_name or '')..' '..(msg.new_chat_members[1].last_name or '')..''
if base:get(TD_ID..'Text:Welcome:'..msg.chat.id) then
txtt = base:get(TD_ID..'Text:Welcome:'..msg.chat.id) 
local txtt = txtt:gsub('FIRSTNAME',(msg.new_chat_members[1].first_name or ''))
local txtt = txtt:gsub('LASTNAME',(msg.new_chat_members[1].last_name or ''))
local txtt = txtt:gsub('RULES',rules or '')
local txtt = txtt:gsub('GROUP',(msg.chat.title or 'گپ ما'))
local txtt = txtt:gsub('LINK',link)
local txtt = txtt:gsub('ADMIN',(AdminNum or 'ربات ادمین نیست'))
local txtt = txtt:gsub('MEMBER',(getChatMembersCount(msg.chat.id).result or 'ربات ادمین نیست'))
local txtt = txtt:gsub('USERID',(msg.new_chat_members[1].id or ''))
local txtt = txtt:gsub('USERNAME',(msg.new_chat_members[1].username or ''))
local txtt = txtt:gsub('MENTION','<a href="tg://user?id='..msg.new_chat_members[1].id..'">'..msg.new_chat_members[1].first_name..'</a>')
local txtt = txtt:gsub('TIME',os.date("%H:%M:%S"))
local txtt = txtt:gsub('DATE',date)
local txtt = txtt:gsub('OWNER',(owner or ''))
local txtt = txtt:gsub('FULLNAME',full)
file = base:get(TD_ID..'gif'..msg.chat.id) or 0
pht = base:get(TD_ID..'photo'..msg.chat.id) or 0
msc = base:get(TD_ID..'audios'..msg.chat.id) or 0
if base:get(TD_ID.."wl_status:"..msg.chat.id) == "gif" then
sendDocumentId(msg.chat.id,file,0,txtt)
elseif base:get(TD_ID.."wl_status:"..msg.chat.id) == "text" then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'کانال راهنمای ربات',url = 'https://t.me/'..Channel}}}
sendText(msg.chat.id,txtt,0,'html',keyboard)
elseif base:get(TD_ID.."wl_status:"..msg.chat.id) == "photo" then
sendPhotoId(msg.chat.id,pht,0,txtt)
elseif base:get(TD_ID.."wl_status:"..msg.chat.id) == "music" then
sendMediaId(msg.chat.id,msc,'voice',0,txtt)
end
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'کانال راهنمای ربات',url = 'https://t.me/'..Channel}}}
sendText(msg.chat.id,'سلام <a href="tg://user?id='..msg.new_chat_members[1].id..'">'..msg.new_chat_members[1].first_name..'</a>️\nبه گروه '..msg.chat.title..' خوش اومدی 🌹\n─┅━━━━━━━┅─\nتعداد اعضاے گروه ما '..getChatMembersCount(msg.chat.id).result..' نفره\nکاری داشتی میتونی به یکی از '..AdminNum..' ادمین ما بگی\n─┅━━━━━━━┅─\nساعت عضویت :【'..os.date("%H:%M:%S")..'】\nتاریخ عضویت : 【'..jdates('#x')..' «» '..jdates('#D-#X-#Y')..'】',0,'html',keyboard)
end
end
end
end
end

if msg.left_chat_member and msg.left_chat_participant and tonumber(msg.left_chat_member.id) == tonumber(BotJoiner) and tonumber(msg.left_chat_participant.id) == tonumber(BotJoiner) then
x = getChatMember(msg.chat.id,BotJoiner)
if (x.ok == false and x.description == 'Forbidden: bot was kicked from the supergroup chat') then
local nm = '<a href="tg://user?id='..msg.from.id..'">'..msg.from.first_name..'</a>'
local ttfs = 
[[
#گزارش_حذف

►►►►اطلاعات فرد حذف کننده ◄◄◄◄

حذف کننده : 

► یوزرنیم : ]]..nm..[[

► شناسه : ]]..msg.from.id..[[

► نام گروه : ]]..msg.chat.title..[[

► تاریخ : ]]..jdates('#x')..' '..jdates('#Y/#M/#D')..[[

► ساعت : ]]..os.date('%X')..[[

#REM
]]
sendText(Config.Sudoid,ttfs,0,'html')
else
end end  

if msg.text then
if msg.text:match('@'..BotUsername) then
msg.text = msg.text:gsub('@'..BotUsername,'')
end
end
if msg.text then
local Black = msg.text
if Black then
Black = Black:lower()
--Start Bot
if msg.chat.type == 'private' then
if Black == '/start' or Black == 'start' then
STARTS(msg,msg.from.id,msg.message_id,1,'send')
--sendText(msg.chat.id,'txt',msg.message_id, 'md')
end end
--IS_SUDO
if (Black == 'reload' or Black == 'ریلود') and is_Sudo(msg) then
dofile(arg[0])
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'Our Channel',url = 'https://t.me/'..Channel}}}
sendText(msg.chat.id,'txt',msg.message_id, 'md',keyboard)
end

if (Black == 'rl') and is_Sudo(msg) then
dofile('Api.lua')
local keyboard = {}
keyboard.inline_keyboard = {{
{text = 'Our Channel',url = 'https://t.me/'..Channel}}}
sendText(msg.chat.id,'txt',msg.message_id, 'md',keyboard)
end


if Black == 'var' and is_Sudo(msg) then

local list = base:smembers(TD_ID..'SuperGp')
if #list > 0 then
nms = 0
for k,v in pairs(list) do
local status = getChatAdministrators(v)
if status and status.result then
for m,n in ipairs(status.result) do
if n.status == 'creator' then
if n.user.id then
owner_id = n.user.id
end end end
--local ff = forwardMessage(v,msg.chat.id,msg.message_id)
vardump(owner_id)
--sendText(msg.chat.id,serpent.block(getChat(msg.chat.id),{comment=false}),0,'html')
end end end end

if (Black == 'stats' or Black == 'امار' or Black == 'آمار') and is_Sudo(msg) then
sgps = base:scard(TD_ID..'SuperGp')
gp = base:scard(TD_ID..'Chat:Normal') or 0
users = base:scard(TD_ID..'ChatPrivite') or 0
user = io.popen('whoami'):read('*a')
totald = io.popen('du -h /var/lib/redis/dump.rdb'):read('*a')
txt = 'به پنل آمار ربات【['..botusername..'](tg://user?id='..BotJoiner..')】خوش امدید'
local keyboard = {}
keyboard.inline_keyboard = {{
{text=babi..'تعدادکل سوپرگروها : '..tostring(sgps)..'',callback_data='bd:ERROR:'..msg.chat.id}},{
{text=babi..'تعداد گروه ها : '..tostring(gp)..'',callback_data='bd:ERROR:'..msg.chat.id}},{
{text=babi..'تعداد کاربران پیوے : '..tostring(users)..'',callback_data='bd:ERROR:'..msg.chat.id}},{
{text = babi..'بستن پنل آمار',callback_data = 'bd:Exitss:-229445008'}}}
sendText(msg.chat.id,txt,msg.message_id, 'md',keyboard)
end

if base:get(TD_ID..'FWDPVS'..msg.from.id) then
List = base:smembers(TD_ID..'ChatPrivite')
for k,v in pairs(List) do
forwardMessage( v , msg.chat.id,msg.message_id)
base:del(TD_ID..'FWDPVS'..msg.from.id)
end 
base:del(TD_ID..'FWDPVS'..msg.from.id)
end
if base:get(TD_ID..'FWDGP'..msg.from.id) then
List = base:smembers(TD_ID..'SuperGp')
for k,v in pairs(List) do
forwardMessage( v , msg.chat.id,msg.message_id)
base:del(TD_ID..'FWDGP'..msg.from.id)
end 
base:del(TD_ID..'FWDGP'..msg.from.id)
end
if base:get(TD_ID..'SENDGP'..msg.from.id) then
List = base:smembers(TD_ID..'SuperGp')
for k,v in pairs(List) do
sendText(v,msg.text,0,'html')
base:del(TD_ID..'SENDGP'..msg.from.id)
end 
base:del(TD_ID..'SENDGP'..msg.from.id)
end
if base:get(TD_ID..'SENDPV'..msg.from.id) then
List = base:smembers(TD_ID..'ChatPrivite')
for k,v in pairs(List) do
sendText(v,msg.text,0,'html')
base:del(TD_ID..'SENDPV'..msg.from.id)
end 
base:del(TD_ID..'SENDPV'..msg.from.id)
end

if is_Sudo(msg) then
if (Black == "panelsend" or Black == "پنل ارسال") and not msg.reply_to_message then
Sends(msg,msg.chat.id,msg.message_id,'send')
end
end
end

lang = base:sismember(TD_ID..'Gp2:'..msg.chat.id,'diamondlang')
if msg.chat.type == 'supergroup' and is_Mod(msg.chat.id,msg.from.id) then
if base:sismember(TD_ID..'Gp2:'..msg.chat.id,'added') then

if (Black == 'menu' or Black == 'فهرست' or Black == 'پنل' or Black == 'panel') then
if base:sismember(TD_ID..'Gp2:'..msg.chat.id,'PanelPv') then
txtmmd= ''
else
txtmmd = '\n─┅━━━━━━━━━┅─\nفقط (['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')) اجازه کار با پنل را دارد !'
end
txt = '⛧ به پنل مدیریتی ربات خوش امدید ، لطفا برای استفاده بر روی #دکمه بخش مورد نظر خود کلیک کنید ▸'
sendText(msg.chat.id,txt..txtmmd,msg.message_id, 'md',menu(msg.chat.id,1))
end
if (Black == 'menupv' or Black == 'فهرست پیوی' or Black == 'پنل پیوی') then
txt = '⛧ به پنل مدیریتی ربات خوش امدید ، لطفا برای استفاده بر روی #دکمه بخش مورد نظر خود کلیک کنید ▸'
sendText(msg.chat.id,'فهرست به پیوی شما ارسال شد',msg.message_id,'md')
sendText(msg.from.id,txt,0, 'md',menu(msg.chat.id,1))
end
if (Black == 'helppv' or Black == 'راهنما پیوی' or Black == 'راهنما پیوی') then
txt = [[⛧ به پنل راهنما دستوری ربات خوش امدید ، لطفا برای استفاده بر روی #دکمه بخش مورد نظر خود کلیک کنید ▸]]
sendText(msg.chat.id,'راهنما به پیوی شما ارسال شد',msg.message_id,'md')
help(msg,msg.from.id,txt,msg.message_id,'send')
end
if (Black == 'help' or Black == 'راهنما') then
if base:sismember(TD_ID..'Gp2:'..msg.chat.id,'PanelPv') then
txtmmd= ''
else
txtmmd = '\nا─┅━━━━━━━━━┅─\nفقط (['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')) اجازه کار با پنل را دارد !'
end
name = '['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')'
time = ''..os.date('%X')..''
txt = [[
▸ راهنمای گروه :
*]]..msg.chat.title..[[*
▸ درخواست کننده :
]]..name..[[

▸ زمان درخواست :
*]]..time..[[*
]]
help(msg,msg.chat.id,txt..txtmmd,msg.message_id,'send')
end
if Black == 'config' or Black == 'پیکربندی' and is_JoinChannel(msg) then
local t = 'مالک گروه :\n'
local status = getChatAdministrators(msg.chat.id)
for m,n in ipairs(status.result) do
if n.status == 'creator' then
if n.user.id then
owner_id = n.user.id
owner_name = n.user.first_name
t = t..''..'['..owner_name:escape_hard()..'](tg://user?id='..owner_id..')'..'\nا─┅━━━━━━━━━┅─n' 
base:sadd(TD_ID..'OwnerList:'..msg.chat.id,owner_id)
end
end
end
t = t..'لیست مدیران گروه :\n'
list = getChatAdministrators(msg.chat.id)
for k,v in pairs(list.result) do
if v.status == 'administrator' then
if v.user.id then
admin_ids = v.user.id
admin_names = v.user.first_name
t = t..k..' - '..'['..admin_names:escape_hard()..'](tg://user?id='..admin_ids..')'..'\n' 
base:sadd(TD_ID..'ModList:'..msg.chat.id,admin_ids)
end
end
end
sendReply(msg,t,true)
end
if Black == 'setphoto' or Black == 'تنظیم عکس' then
if msg.reply_to_message then
if msg.reply_to_message.photo then
PhotoFile = msg.reply_to_message.photo[4] or msg.reply_to_message.photo[3] or msg.reply_to_message.photo[2] or msg.reply_to_message.photo[1]
PhotoFileId = PhotoFile.file_id
base:set(TD_ID..'photo'..msg.chat.id,PhotoFileId)
sendReply(msg,'عکس مورد نظر برای خوشامدگویی  تنظیم شد',true)
end
end 
end
if Black == 'setmusic' or Black == 'تنظیم موزیک' then
if msg.reply_to_message then
if msg.reply_to_message.voice then
PhotoFile = msg.reply_to_message.voice
PhotoFileId = PhotoFile.file_id
base:set(TD_ID..'audios'..msg.chat.id,PhotoFileId)
sendReply(msg,'موزیک موردنظر برای خوشامدگویی تنظیم شد',true)
end
end 
end
if Black == 'setgif' or Black == 'تنظیم گیف' then
if msg.reply_to_message then
if msg.reply_to_message.document then
PhotoFile = msg.reply_to_message.document
PhotoFileId = PhotoFile.file_id
base:set(TD_ID..'gif'..msg.chat.id,PhotoFileId)
sendReply(msg,'گیف موردنظر برای خوشامدگویی تنظیم شد',true)
end
end 
end
if (Black == 'tosticker' or Black == 'تبدیل به استیکر') then
if msg.reply_to_message then
if msg.reply_to_message.photo then
PhotoFile = msg.reply_to_message.photo[4] or msg.reply_to_message.photo[3] or msg.reply_to_message.photo[2] or msg.reply_to_message.photo[1]
PhotoFileId = PhotoFile.file_id
PhotoPathRes = getRes("getFile?file_id="..PhotoFileId)
if PhotoPathRes.ok then
FilePath = PhotoPathRes.result.file_path
FileUrl = "https://api.telegram.org/file/bot"..Config.JoinToken.."/"..FilePath
PathToSave = "./libs/data"
Photo = download(FileUrl,PathToSave,"BD.web")
sendSticker(msg.chat.id,Photo,msg.message_id)
end
else
sendReply(msg,'فقط #عکس ها قابل تبدیل میباشد',true)
end
end
end
if (Black == 'link' or Black == 'لینک') then
link = getChat(msg.chat.id).result.invite_link or base:get(TD_ID..'Link:'..msg.chat.id) or 'لینک گروه ثبت نشده است'
bio = getChat(msg.chat.id).result.description or 'گروه بیوگرافی ندارد'
PhotoFile = getChat(msg.chat.id).result.photo
if PhotoFile then
PhotoPathRes = getRes("getFile?file_id="..PhotoFile.big_file_id)
if PhotoPathRes.ok then
FilePath = PhotoPathRes.result.file_path
FileUrl = "https://api.telegram.org/file/bot"..Config.JoinToken.."/"..FilePath
PathToSave = "./libs/data"
Photo = download(FileUrl,PathToSave,"Gp.jpg")
sendPhoto(msg.chat.id,Photo,'▸ نام گروه : '..msg.chat.title..'\n▸ بیوگرافی گروه : '..bio..' \n▸ لینک گروه : '..link..'',msg.message_id)
end
else
txtt = '▸ نام گروه : '..msg.chat.title..'\n▸ بیوگرافی گروه : '..bio..' \n▸ لینک گروه : '..link..''
sendText(msg.chat.id,txtt,msg.message_id,'html')
end
end
if (Black == 'revoke link' or Black == 'باطل کردن لینک') then
local url = https.request(BASSE .. '/getChatMember?chat_id='..msg.chat.id..'&user_id='..Config.BotJoiner)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' then
if not exportChatInviteLink(msg.chat.id) then
sendReply(msg, '|↜ ربات به قسمت دعوت کاربران با لینک دسترسے ندارد...!\nلطفا ابتدا ربات را ادمین گروه کنید سپس این دستور را اراسال نمایید...!',true)
else
link = exportChatInviteLink(msg.chat.id).result
local keyboard = {}
keyboard.inline_keyboard = {{{text = '✦ براے عضویت در کانال کلیک کنید',url='https://telegram.me/'..Config.Channel}}}   
sendText(msg.chat.id,'لینک گروه باطل شد...!\n─┅━━━━━━━┅─\nلینک جدید :\n <a href="'..link..'">'..msg.chat.title..'</a>',msg.message_id,'html',keyboard)
base:set(TD_ID..'Link:'..msg.chat.id,link)
end
end
end
--GetFilter
mmd = base:get(TD_ID..'limit_filter'..msg.chat.id..msg.from.id)
if Black and mmd then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..msg.chat.id}}}
editMessageText(msg.chat.id,tonumber(mmd),'کلمه '..Black..'\nبه لیست فیلتر کلمات افزوده شد',keyboard,'md')
base:sadd(TD_ID..'filters:'..msg.chat.id,Black)
base:del(TD_ID..'limit_filter'..msg.chat.id..msg.from.id)
end
mmds = base:get(TD_ID..'limit_filters'..msg.chat.id..msg.from.id)
if Black and mmds then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..msg.chat.id}}}
editMessageText(msg.chat.id,tonumber(mmds),'کلمه '..Black..'\nاز لیست فیلتر کلمات حذف شد',keyboard,'md')
base:srem(TD_ID..'filters:'..msg.chat.id,Black)
base:del(TD_ID..'limit_filters'..msg.chat.id..msg.from.id)
end
--SetLink
links = base:get(TD_ID..'links'..msg.chat.id..msg.from.id)
if msg.text and (msg.text:match('/joinchat/')) and links then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:GroupLink:'..msg.chat.id}}}
editMessageText(msg.chat.id,tonumber(links),'لینک شما باموفقیت ثبت شد\n'..msg.text,keyboard)
base:set(TD_ID..'Link:'..msg.chat.id,msg.text)
base:del(TD_ID..'links'..msg.chat.id..msg.from.id)
end

---***Send & Fwd
local pvs = base:get(TD_ID..'SendPv'..msg.chat.id)
if Black and pvs then
EditSends(msg,msg.chat.id,msg.message_id,'پیوی','ارسال','SendPv','ChatPrivite',Black,'send')
end
local gps = base:get(TD_ID..'SendSuperGroups'..msg.chat.id)
if Black and gps then
EditSends(msg,msg.chat.id,msg.message_id,'سوپرگروه','ارسال','SendSuperGroups','SuperGp',Black,'send')
end
local pvf = base:get(TD_ID..'FwdPv'..msg.chat.id)
if Black and pvf then
EditSends(msg,msg.chat.id,msg.message_id,'پیوی','فوروارد','FwdPv','ChatPrivite',Black,'edit')
end
local gpf = base:get(TD_ID..'FwdSuperGroups'..msg.chat.id)
if Black and gpf then
EditSends(msg,msg.chat.id,msg.message_id,'سوپرگروه','فوروارد','FwdSuperGroups','SuperGp',Black,'edit')
end
-------
local sendowner = base:get(TD_ID..'SendOwner'..msg.chat.id)
if Black and sendowner then
EditSends_(msg,msg.chat.id,msg.message_id,'مالک','ارسال','SendOwner','SuperGp','creator',Black,'send')
end
local sendadmin = base:get(TD_ID..'SendAdmin'..msg.chat.id)
if Black and sendadmin then
EditSends_(msg,msg.chat.id,msg.message_id,'ادمین','ارسال','SendAdmin','SuperGp','administrator',Black,'send')
end
local fwdowner = base:get(TD_ID..'FwdOwner'..msg.chat.id)
if Black and fwdowner then
EditSends_(msg,msg.chat.id,msg.message_id,'مالک','فوروارد','FwdOwner','SuperGp','creator',Black,'edit')
end
local fwdadmin = base:get(TD_ID..'FwdAdmin'..msg.chat.id)
if Black and fwdadmin then
EditSends_(msg,msg.chat.id,msg.message_id,'ادمین','فوروارد','FwdAdmin','SuperGp','administrator',Black,'edit')
end

end
--end--BotAdded
end--supergroup
if msg.text then
print(clr.blue..'['..os.date('%X')..']'..clr.red..' '..msg.text..clr.reset..'['..msg.from.first_name..']--> ['..msg.chat.id..']')
end
end
if msg.chat.type == 'supergroup' then
if not is_Mod(msg.chat.id,msg.from.id) then
if base:get(TD_ID..'Lock:Stickers'..msg.chat.id) == 'del' then
if msg.sticker then 
if msg.sticker.is_animated == true then
print('msg.sticker')
deleteMessages(msg.chat.id,msg.message_id)
end
end
end
if base:get(TD_ID..'Lock:Vote'..msg.chat.id) == 'del' then
if msg.poll then
print('نظرسنجی')
deleteMessages(msg.chat.id,msg.message_id)
end
end
end
end
end

function calback(msg)
--if base:sismember(TD_ID..'Gp2:'..msg.message.chat.id,'added') then
--ChatId = msg.message.chat.id
--lang = base:sismember(TD_ID..'Gp2:'..msg.message.chat.id,'diamondlang')
--ChatName = msg.message.chat.title
--ChatType = msg.message.chat.type
--User_Id = msg.from.id
--User_Name = ec_name(msg.from.first_name)
--User_Username = msg.from.username

if msg.data:match('(cb):(.*):(%d+)') then
Split = msg.data:split(':')
chat_id = Split[3]
MenuName = Split[2]
if MenuName == 'STARTS' then
STARTS(msg,chat_id,'',1,'')
elseif MenuName == 'Emkanat' then
STARTS(msg,chat_id,msg.message.message_id,2,'')
elseif MenuName == 'amozesh' then
txt = [[
سلام دوست عزیز

*برای استفاده از ربات با کمک لینک زیر آن را به گروه خود اضافه کنید*

[لینک اضافه کردن ربات -
کلیک کنید](https://t.me/]]..botusername..[[/?startgroup=new)

◂ سپس ربات را داخل گروه ادمین کنید
◂ توصیه می‌شود ربات را ادمین کامل کنید 
◂ ربات بعد از ادمین شدن بطور خودکار نصب و فعال می‌شود 
◂ در صورت نبود یا دلیت بودن اکانت سازنده گروه
◂ ربات ادمینهای کامل را بعنوان مالک شناسایی و مقام می‌دهد


*از نظر امکانات و کیفیت همین بس که ربات ما با توجه به حجم خدمات و قدمت یکی از بزرگترین رباتها در نوع خود است. در صورت داشتن مشکل یا سوال گروه پشتیبانی در کنار شماست.*

[پشتیبانی](]]..Config.LinkSuppoRt..[[)
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'cb:STARTS:'..chat_id}}}
Edit(msg,txt,keyboard,true)
end end

function lock_add(name,mrr)
base:sadd(TD_ID..''..name..''..chat_id,mrr)
end
function lock_rem(arg,data)
base:srem(TD_ID..''..arg..''..chat_id,data)
end
function tdlock(chat_id,BD)
if BD == 'Document' then
text = 'فایل'
elseif BD == 'Inline' then
text = 'اینلاین'
elseif BD == 'Link' then
text = 'ارسال لینک'
elseif BD == 'Hashtag' then
text = 'ارسال تِگ'
elseif BD == 'Game' then
text = 'بازی'
elseif BD == 'Username' then
text = 'ارسال یوزرنیم'
elseif BD == 'Photo' then
text = 'عکس'
elseif BD == 'Gif' then
text = 'تصاویر متحرک'
elseif BD == 'Video' then
text = 'ویدئو'
elseif BD == 'Voice' then
text = 'وویس'
elseif BD == 'Audio' then
text = 'موزیک'
elseif BD == 'Text' then
text = 'متن'
elseif BD == 'Sticker' then
text = 'استیکر'
elseif BD == 'Stickers' then
text = 'استیکر متحرک'
elseif BD == 'Contact' then
text = 'مخاطبین'
elseif BD == 'Forward' then
text = 'فوروارد'
elseif BD == 'Persian' then
text = 'فارسی'
elseif BD == 'English' then
text = 'انگلیسی'
elseif BD == 'Bots' then
text = 'ربات'
elseif BD == 'Channelpost' then
text = 'پست کانال'
elseif BD == 'Selfi' then
text = 'فیلم سلفی'
elseif BD == 'Location' then
text = 'موقعیت مکانی'
elseif BD == 'Edit' then
text = 'ویرایش'
elseif BD == 'Fosh' then
text = 'فحش'
elseif BD == 'Hyper' then
text = 'هایپرلینک'
elseif BD == 'Flood' then
text = 'پیام مکرر'
elseif BD == 'Spam' then
text = 'هرزنامه'
elseif BD == 'Caption' then
text = 'کَپشِن'
elseif BD == 'Biolink' then
text = 'لینک بیو'
elseif BD == 'Vote' then
text = 'نظرسنجی'
else
text = BD
end
end
---*<><><>**Modern Locks--*<><><>**---
if msg.data:match('(-%d+):(Lock):(.*):(%d+)') then
Q = msg.data:split(':')
chat_id = Q[1]
BD = Q[3]
pg = tonumber(Q[4])
tdlock(chat_id,BD)
if not is_req(msg) then
Alert(msg.id,'Error !\nشما دسترسی کار کردن با این پنل را ندارید !',true)
else
if not is_Mod(chat_id,msg.from.id) then
Alert(msg.id,'شما از مدیران ربات نیستید',true)
return
end
if not base:get(TD_ID..'Lock:'..BD..chat_id) then
value = 'ban'
Status = 'مسدود'
base:set(TD_ID..'Lock:'..BD..chat_id,value)
Alert(msg.id,'قفل '..text..' حالت '..Status..' فعال شد ‣',true)
elseif base:get(TD_ID..'Lock:'..BD..chat_id) == 'ban' then
value = 'mute'
Status = 'محدود'
base:set(TD_ID..'Lock:'..BD..chat_id,value)
Alert(msg.id,'قفل '..text..' حالت '..Status..' فعال شد ‣',true)
elseif base:get(TD_ID..'Lock:'..BD..chat_id) == 'mute' then
value = 'warn'
Status = 'اخطار'
base:set(TD_ID..'Lock:'..BD..chat_id,value)
Alert(msg.id,'قفل '..text..' حالت '..Status..' فعال شد ‣',true)
elseif base:get(TD_ID..'Lock:'..BD..chat_id) == 'warn' then
value = 'silent'
Status = 'سکوت'
base:set(TD_ID..'Lock:'..BD..chat_id,value)
Alert(msg.id,'قفل '..text..' حالت '..Status..' فعال شد ‣',true)
elseif base:get(TD_ID..'Lock:'..BD..chat_id) == 'silent' then
value = 'del'
Status = 'حذف پیام'
base:set(TD_ID..'Lock:'..BD..chat_id,value)
Alert(msg.id,'قفل '..text..' حالت '..Status..' فعال شد ‣',true)
elseif base:get(TD_ID..'Lock:'..BD..chat_id) == 'del' then
base:del(TD_ID..'Lock:'..BD..chat_id)
Status = 'غیرفعال'
Alert(msg.id,'قفل '..text..' '..Status..' شد',true)
end
SETT(msg,chat_id,pg)
end
end
if msg.data:match('(Moaf):(%d+):(-%d+):(.*)') then
Split = msg.data:split(':')
user = Split[2]
chat_id = Split[3]
name = Split[4]
if not is_req(msg) then
Alert(msg.id,'Error !\nشما دسترسی کار کردن با این پنل را ندارید !',true)
else
if not is_Mod(chat_id,msg.from.id) then
Alert(msg.id,'شما از مدیران ربات نیستید',true)
return
end
t = 'کاربر 【['..name..'](tg://user?id='..user..')】از افزودن اجباری معاف شد'
base:sadd(TD_ID..'VipAdd:'..chat_id,user)
EdiT(msg,t,false,true)
end
end

--- <>tabchi<> ---
if msg.data:match('bd:IsTabchi(.*):(-%d+)') then
Split = msg.data:split(':')
chat_id = Split[3]
MenuName = Split[2]
Split2 = MenuName:split('>')
user = tonumber(Split2[2])
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
mmltxt = 'در گروه محدود خواهید شد !'
mmltxt2 = 'محدود'
mmltxt3 = 'محدود کردن'
else
mmltxt = 'از گروه اخراج خواهید شد !'
mmltxt2 = 'اخراج'
mmltxt3 = 'اخراج'
end
if tonumber(user) == tonumber(msg.from.id) then
name = msg.from.username or ec_name(msg.from.first_name)
if MenuName:match('IsTabchiFalse>') then
base:srem(TD_ID..'AntiTabchiUser'..chat_id,user)
deleteMessages(chat_id,msg.message.message_id)
UnRes(chat_id,user)
Alert(msg.id,'✔️تایید هویت شما انجام شد و ثابت شد شما ربات نیستید !\nتشکر بابت همکاری شما 🙏🏻',true)
end
if MenuName:match('IsTabchiTrue>') then
local keyboard = {}
TexT = '#احراز_هویت\n👤کاربر <a href="tg://user?id='..msg.from.id..'">'..name..'</a> پاسخ شما اشتباه بود !\n🔑در صورتی که ربات نیستید به سوال زیر پاسخ دهید !\n⚠️این تلاش اخر شما خواهد بود و در صورتی که به این سوال پاسخ ندهید و یا پاسخ اشتباه دهید '..mmltxt
Mohammad2 = {'MohammadrezaRosta','B_D'}
Mohammadr_r = {'BD','Mrr619','Babak','TeleDiamondCh'}
BDAntiTabchi = Mohammadr_r[math.random(#Mohammadr_r)]
if Mohammad2[math.random(#Mohammad2)] == 'MohammadrezaRosta' then
mrr619 = {0,1,2,3,4,5,6,7,8,9}
randnum = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum2 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum3 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
randnum4 = mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]..mrr619[math.random(#mrr619)]
if tonumber(randnum) == tonumber(randnum2) or tonumber(randnum) == tonumber(randnum3) or tonumber(randnum) == tonumber(randnum3) then
randnum = 1000
end
if BDAntiTabchi == 'Mrr619' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'BD' then
keyboard.inline_keyboard = {
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id},{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'TeleDiamondCh' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id}},
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'Babak' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id}},
{{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
local randnum = randnum:gsub("[0123456789]", {["0"] = "0️⃣", ["1"] = "1️⃣", ["2"] = "2️⃣", ["3"] = "3️⃣", ["4"] = "4️⃣", ["5"] = "5️⃣", ["6"] = "6️⃣", ["7"] = "7️⃣", ["8"] = "8️⃣", ["9"] = "9️⃣"})
EdiT(msg,TexT..'\n\n>معکوس عدد '..randnum..' را از میان دکمه های زیر پیدا کرده و بر روی آن کلیک کنید !',keyboard) 
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
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'BD' then
keyboard.inline_keyboard = {
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id},{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id}},
{{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'TeleDiamondCh' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id}},
{{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id},{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
if BDAntiTabchi == 'Babak' then
keyboard.inline_keyboard = {
{{text = randnum2,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>6:'..chat_id},{text = randnum3,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>5:'..chat_id}},
{{text = randnum4,callback_data='bd:IsTabchiTrue2>'..msg.from.id..'>4:'..chat_id},{text = randnum,callback_data='bd:IsTabchiFalse>'..msg.from.id..':'..chat_id}},
{{text = 'تایید هویت(مخصوص مدیران)',callback_data='bd:Is_Tabchino>'..msg.from.id..':'..chat_id},{text = 'عدم تاییدهویت(مخصوص مدیران)',callback_data='bd:Is_Tabchiyes>'..msg.from.id..':'..chat_id}},
}
end
local randnum = randnum:gsub(randnum,{["0️⃣"] = "شماره صفر", ["1️⃣"] = "شماره یک", ["2️⃣"] = "شماره دو", ["3️⃣"] = "شماره سه", ["4️⃣"] = "شماره چهار", ["5️⃣"] = "شماره پنج", ["6️⃣"] = "شماره شیش", ["7️⃣"] = "شماره هفت", ["8️⃣"] = "شماره هشت", ["9️⃣"] = "شماره نه", ["❤️"] = "قلب", ["😍"] = "چشم های قلبی", ["✅"] = "تیک سبز", ["😭"] = "گریه", ["🍦"] = "بستنی", ["🍌"] = "موز", ["🍉"] = "هندوانه", ["🍏"] = "سیب سبز", ["🍎"] = "سیب قرمز", ["🦆"] = "اردک", ["💰"] = "کیسه پول", ["🔑"] = "کلید", ["🐥"] = "جوجه", ["🎀"] = "پاپیون", ["🎈"] = "بادکنک قرمز", ["🔧"] = "اچهار فرانسه", ["🗡"] = "شمشیر", ["🤖"] = "ربات", ["💄"] = "رژ لب", ["💍"] = "انگشتر نگین دار", ["🐒"] = "میمون", ["⚽️"] = "توپ فوتبال", ["✔️"] = "تیک مشکی", ["⚫️"] = "دایره مشکی", ["🔴"] = "دایره قرمز", ["🔵"] = "دایره ابی", ["⚪️"] = "دایره سفید", ["🇮🇷"] = "پرچم ایران",["😡"] = "ادم عصبانی"})
EdiT(msg,TexT..'\n\n> اموجی '..randnum..' را از میان دکمه های زیر پیدا کرده و بر روی آن کلیک کنید !',keyboard) 
end
end
if MenuName:match('IsTabchiTrue2>') then
local url  = https.request(BASSE .. '/getChatMember?chat_id='..chat_id..'&user_id='..Config.BotJoiner)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' and statsurl.result.can_restrict_members == true then
EdiT(msg,'کاربر <a href="tg://user?id='..msg.from.id..'">'..name..'</a> به هر دو سوال احراز هویت پاسخ اشتباه داد و از گروه '..mmltxt2..' شد !\n#ربات_شناخته_شد',nil) 
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
MuteUser(chat_id,msg.from.id,1)
else
KickUser(chat_id,msg.from.id)
end
base:srem(TD_ID..'AntiTabchiUser'..chat_id,msg.from.id)
else
EdiT(msg,'کاربر <a href="tg://user?id='..msg.from.id..'">'..name..'</a> به هر دو سوال احراز هویت پاسخ اشتباه داد ولی ربات دسترسی برای '..mmltxt3..' کاربر را ندارد !\n#لطفا ربات را ادمین گروه کنید',nil) 
end
end
else
Alert(msg.id,'Error !\nاین احراز هویت برای شما نیست !',true)
end
end
--<><>Is Mod<><>--
if msg.data:match('(bd):(.*):(-%d+)') then
Split = msg.data:split(':')
chat_id = Split[3]
MenuName = Split[2]
if MenuName:match('>') then
Splitss = MenuName:split('>')
UseriD  = Splitss[2]
end
if msg.message.chat.type == 'supergroup' then
gpname = msg.message.chat.title
else
gpname = base:get(TD_ID..'StatsGpByName'..chat_id) or 'Not Found'
end
if not is_req(msg) then
Alert(msg.id,'Error !\nشما دسترسی کار کردن با این پنل را ندارید !',true)
return
end
if not is_Mod(chat_id,msg.from.id) then
Alert(msg.id,'شما از مدیران ربات نیستید',true)
return
end
if MenuName == 'menu' then
txt = '⛧ به پنل مدیریتی ربات خوش امدید ، لطفا برای استفاده بر روی #دکمه بخش مورد نظر خود کلیک کنید ▸'
Edit(msg,txt,menu(chat_id,1),true)
elseif MenuName == 'settings' then
SETT(msg,chat_id,1)
elseif MenuName == 'Next1' then
SETT(msg,chat_id,2)
elseif MenuName == 'Next2' then
SETT(msg,chat_id,3)
elseif MenuName == 'Next3' then
SETT(msg,chat_id,4)
elseif MenuName == 'Next4' then
SETT(msg,chat_id,5)
elseif MenuName == 'Next5' then
SETT(msg,chat_id,6)
elseif MenuName == 'groupinfo' then
SETT(msg,chat_id,7)
elseif MenuName == 'groupinfo2' then
SETT(msg,chat_id,8)
elseif MenuName == 'Nowsettings' then
SETT(msg,chat_id,9)
elseif MenuName == 'status1' then
SETT(msg,chat_id,10)
elseif MenuName == 'Amarr' then
StatusGp(msg,chat_id)
elseif MenuName == 'Falit' then
StatusGp2(msg,chat_id)
elseif MenuName == 'cleanamarr' then
allusers = base:smembers(TD_ID..'ModList:'..chat_id)
for k,v in pairs(allusers) do
base:del(TD_ID..'Total:messages:'..chat_id..':'..v)
base:del(TD_ID..'Total:messages:'..chat_id)
base:del(TD_ID..'Total:messages:'..chat_id..':'..os.date("%Y/%m/%d")..':'..v)
end
StatusGp2(msg,chat_id)

--[[elseif MenuName == 'tagmenu' then
Tags(msg,chat_id,1,'') 
elseif MenuName == 'tagmagham' then
Tags(msg,chat_id,2,'') 
elseif MenuName == 'tagbedonmagham' then
Tags(msg,chat_id,3,'') 
elseif MenuName == 'taghame' then
Tags(msg,chat_id,4,'') 
]]

elseif MenuName == 'activelock' then
lock_list = {
{'Link','لینک'},
{'Vote','نظرسنجی'},
{'Tag','هشتگ'},
{'Username','یوزرنیم'},
{'Persian','فارسی'},
{'English','انگلیسی'},
{'Join','ورود'},
{'Gif','گیف'},
{'Text','متن'}
,{'Photo','عکس'},
{'Video','فیلم'},
{'Videonote','سلفی'},
{'Audio','اهنگ'},
{'Voice','ویس'},
{'Sticker','استیکر'},
{'Contact','مخاطب'},
{'Forward','فوروارد'},
{'Location','موقعیت'},
{'Document','فایل'},
{'Inline','اینلاین,'},
{'Game','بازی'},
{'Edit','ویرایش'},
{'Bots','ربات'},
{'Fosh','فحش'}}
local text = 'وضعیت قفل های گروه :\n\n', false
for i,v in pairs(lock_list) do
if base:get(TD_ID..'Lock:'..v[1]..chat_id) == 'del' then
x = '✔️|↜ حذف پیام'
elseif base:get(TD_ID..'Lock:'..v[1]..chat_id) == 'ban' then
x = '✔️|↜ مسدود کاربر'
elseif base:get(TD_ID..'Lock:'..v[1]..chat_id) == 'warn' then
x = '✔️|↜ اخطار کاربر'
elseif base:get(TD_ID..'Lock:'..v[1]..chat_id) == 'mute' then
x = '✔️|↜ محدود کاربر'
elseif base:get(TD_ID..'Lock:'..v[1]..chat_id) == 'silent' then
x = '✔️|↜ سکوت کاربر'
else
x = 'غیرفعال'
end
if not text then
text = "• هیچ قفلی فعال نیست!"
break
else
text = text.."• قفل #"..v[2].." "..x.."\n"
end end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:Nowsettings:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif MenuName == 'Wlc' then
SETT(msg,chat_id,11)
elseif MenuName == 'WlcStatus' then
if not base:get(TD_ID..'wl_status:'..chat_id) then
value = 'music' 
Status = 'موزیک' --ban
base:set(TD_ID..'wl_status:'..chat_id,value)
Alert(msg.id,'حالت خوشامدگویی بر روی '..Status..' قرار گرفت')
elseif base:get(TD_ID..'wl_status:'..chat_id) == 'music' then
value = 'photo'
Status = 'عکس' --mute
base:set(TD_ID..'wl_status:'..chat_id,value)
Alert(msg.id,'حالت خوشامدگویی بر روی '..Status..' قرار گرفت')
elseif base:get(TD_ID..'wl_status:'..chat_id) == 'photo' then
value = 'gif'
Status = 'گیف'--warn
base:set(TD_ID..'wl_status:'..chat_id,value)
Alert(msg.id,'حالت خوشامدگویی بر روی '..Status..' قرار گرفت')
elseif base:get(TD_ID..'wl_status:'..chat_id) == 'gif' then
value = 'text'
Status = 'متن'--silent
base:set(TD_ID..'wl_status:'..chat_id,value)
Alert(msg.id,'حالت خوشامدگویی بر روی '..Status..' قرار گرفت')
elseif base:get(TD_ID..'wl_status:'..chat_id) == 'text' then
base:del(TD_ID..'wl_status:'..chat_id)
Status = 'غیرفعال'
Alert(msg.id,'حالت خوشامدگویی بر روی '..Status..' قرار گرفت')
end
SETT(msg,chat_id,11)
elseif MenuName == 'NotAdmin' then
Alert(msg.id,'لینک گروه ثبت نشده است',true)
elseif MenuName == 'ERROR' then
Alert(msg.id,'دارے اشتباه میزنی داداچ!',true)
elseif MenuName == 'Ghofl' then
Alert(msg.id,'Error !\nاز دکمه های پایین این قفل جهت تغییر قفلها استفاده کنید !',true)

elseif MenuName:match('Is_Tabchino>') then
base:srem(TD_ID..'AntiTabchiUser'..chat_id,UseriD)
deleteMessages(chat_id,msg.message.message_id)
UnRes(chat_id,UseriD)
Alert(msg.id,'✔️ شما تایید کردید که این کاربر ربات نیست !',true)
elseif MenuName:match('Is_Tabchiyes>') then
base:srem(TD_ID..'AntiTabchiUser'..chat_id,UseriD)
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
mmltxt2 = 'محدود'
MuteUser(chat_id,tonumber(UseriD),1)
else
mmltxt2 = 'اخراج'
KickUser(chat_id,tonumber(UseriD))
end
deleteMessages(chat_id,msg.message.message_id)
Alert(msg.id,'✔️ شما تایید کردید که این کاربر ربات است و کاربر از گروه '..mmltxt2..' شد !',true)
--Note Tabchi 
elseif MenuName:match('AntiTabchipnl') then
AntiTabchiPnl(msg,chat_id)

elseif MenuName == 'ehrazhovit' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'AntiTabchi') then
base:srem(TD_ID..'Gp2:'..chat_id,'AntiTabchi')
Alert(msg.id,babi..'احراز هویت براے کاربران جدید غیرفعال شد !',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'AntiTabchi')
Alert(msg.id,babi..'احراز هویت براے کاربران جدید فعال شد !',true)
end
AntiTabchiPnl(msg,chat_id)
elseif MenuName == 'nameanti' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'NameAntiTabchi') then
base:srem(TD_ID..'Gp2:'..chat_id,'NameAntiTabchi')
Alert(msg.id,babi..' حساسیت به اسم #غیرفعال شد!',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'NameAntiTabchi')
Alert(msg.id,babi..' حساسیت به اسم #فعال شد!',true)
end
AntiTabchiPnl(msg,chat_id)
elseif MenuName == 'fisrtmute' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'FirstTabchiMute') then
base:srem(TD_ID..'Gp2:'..chat_id,'FirstTabchiMute')
Alert(msg.id,babi..'محدودیت به محض ورود غیرفعال شد و کاربران تازه وارد میتوانند بدون پاسخ به احراز هویت تا اتمام زمان احراز هویت در گروه چت کنند !',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'FirstTabchiMute')
Alert(msg.id,babi..'محدودیت به محض ورود فعال شد و در صورت فعال بودن احراز هویت هر کاربری که وارد گروه میشود تا زمانی که به سوال احراز هویت پاسخ ندهد محدود خواهند ماند !',true)
end
AntiTabchiPnl(msg,chat_id)
elseif MenuName == 'Error404' then
Alert(msg.id,babi..'مجازات\nپاسخ نداد به سوال احراز هویت\nداشتن اسم غیرمجاز\nداشتن بیوگرافی غیرمجاز\nرا توسط دکمه های بالای مجازات تعیین کنید !',true)
elseif MenuName == 'bioanti' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'BioAntiTabchi') then
base:srem(TD_ID..'Gp2:'..chat_id,'BioAntiTabchi')
Alert(msg.id,babi..' حساسیت به بیوگرافی #غیرفعال شد!',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'BioAntiTabchi')
Alert(msg.id,babi..' حساسیت به بیوگرافی #فعال شد!',true)
end
AntiTabchiPnl(msg,chat_id)
elseif MenuName == 'modeehrazm' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
Alert(msg.id,babi..'حالت احراز هویت از قبل ب روی محدودیت از ارسال پیام قرار داشت !',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'MuteAntiTab')
AntiTabchiPnl(msg,chat_id)
Alert(msg.id,babi..'در صورت عدم پاسخ به سوال احراز هویت یا دادن پاسخ اشتباه به ان کاربر از ارسال پیام محدود میشود !',true)
end
AntiTabchiPnl(msg,chat_id)
elseif MenuName == 'modeehrazk' then
if base:sismember(TD_ID..'Gp2:'..chat_id,'MuteAntiTab') then
base:srem(TD_ID..'Gp2:'..chat_id,'MuteAntiTab')
AntiTabchiPnl(msg,chat_id)
Alert(msg.id,babi..'در صورت عدم پاسخ به سوال احراز هویت یا دادن پاسخ اشتباه به ان کاربر از گروه اخراج میشود !',true)
else
Alert(msg.id,babi..'حالت احراز هویت از قبل روی اخراج قرار داشت !',true)
end
elseif MenuName == 'helpmenu' then
name = '['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')'
time = ''..os.date('%X')..''
txt = [[
▸ راهنمای گروه :
*]]..msg.message.chat.title..[[*
▸ درخواست کننده :
]]..name..[[

▸ زمان درخواست :
*]]..time..[[*
]]
help(msg,chat_id,txt,'','')
elseif MenuName == 'helpkarbari' then
txt = [[
‼️به بخش راهنمای ارتقای مدیران خوشامدید :

⓵ مدیریت مدیران :
◄  با ارسال دستور اول تمام ادمین های گروه ارتقا مقام پیدا می‌کنند و با دستور دوم عزل می‌شوند :

⦁ پیکربندی
⦁ پاکسازی مدیران

⚏ برای ارتقا مقام و عزل یک کاربر از مدیریت ربات و  مشاهده لیست مدیران از دستورات زیر استفاده می‌کنیم :

⦁ ترفیع
⦁ عزل
⦁ مدیران
┈┅┅━❥━┅┅┈
⓶ مدیریت کاربران ویژه :
◄ کاربر ویژه به فردی گفته می‌شود که توانایی مدیریت ربات را ندارد اما درصورت ارسال لینک و... پست او حذف نخواهد شد
⚏ برای ویژه کردن و عزل یک کاربر ویژه از دو دستور زیر استفاده می‌کنیم :

⦁ تنظیم ویژه
⦁ حذف ویژه
┈┅┅━❥━┅┅┈
⓷ ادمین کردن :
◄ برای ادمین گروه کردن و حذف آن و مشاهده لیست از دستورات زیر استفاده می‌کنیم :

⦁ مدیر گروه 
⦁ حذف مدیر گروه
⦁ مدیران

توجه : ربات باید ادمین کامل باشد و تیک افزودن مدیر برایش فعال باشد
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpmodiriat' then
txt = [[
‼️ به بخش راهنمای دستورات کنترل کاربران خوشامدید:

➀ بن :
◄ با ریپلای و ارسال دستور اول کاربر بن و با دستور دوم حذف بن می‌شود. 

⦁ بن
⦁ حذف بن

⚏ برای مشاهده لیست بن و پاکسازی آن از دو دستور زیر استفاده می‌کنیم

⦁ مسدودی ها
⦁ پاکسازی مسدودی ها
┈┅┅━❥━┅┅┈
⓶ سکوت :
◄ با ریپلای و ارسال دستور اول کاربر سکوت و با دستور دوم حذف سکوت می‌شود. 

⦁ سکوت
⦁ حذف سکوت

⚏ برای مشاهده لیست سکوت و پاکسازی آن از دو دستور زیر استفاده می‌کنیم.
‎
⦁ سکوتی ها
⦁ پاکسازی سکوتی ها
┈┅┅━❥━┅┅┈
⓷ سکوت زماندار :
◄ با ریپلای و ارسال دستور کاربر به مدت مشخص شده سکوت می‌شود. 
برای مثال ( سکوت 6 ) با این دستور کاربر 26 ساعت سکوت می‌شود.

⦁ سکوت عدد
┈┅┅━❥━┅┅┈
⓸ اخطار :
◄ با ریپلای و ارسال دستور اول کاربر اخطار و با دستور دوم حذف اخطار می‌شود. 

⦁ اخطار
⦁ حذف اخطار
⚏ برای مشاهده لیست اخطار و پاکسازی آن از دو دستور زیر استفاده می‌کنیم :
‎
⦁ اخطاری ها
⦁ پاکسازی اخطاری ها
┈┅┅━❥━┅┅┈
⚏ برای تنظیم تعداد اخطار از دستور زیر استفاده می‌کنیم
برای مثال ( میزان اخطار 4 ) با این دستور کاربران بعد از دریافت 4 اخطار بن یا سکوت می‌شوند

⦁     تنظیم اخطار عدد
┈┅┅━❥━┅┅┈
💬 برای مجازات کاربران بجز ریپلای از آیدی و آیدی عددی و منشن نیز می‌توانیم استفاده کنیم
برای مثال ( بن @user ) با این دستور کاربران بن می‌شود
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpghofli' then
txt = [[
‼️به بخش راهنمای قفل ها خوشامدید :
⓵ تمامی قفل های اصلی ربات :
لینک . فوروارد . هشتگ . تبچی . خدمات . فارسی . انگلیسی . استیکر متحرک . ربات . سلفی . ورود . ایموجی متحرک . فراخانی . عکس . فیلم . وویس . گیف . اهنگ . فایل . متن . مخاطب . استیکر . بازی . دکمه شیشه ایی . پیام مکرر . 

◄ شما می‌توانید تمام موارد بالا را طبق مثال زیر قفل و باز کنید :

⦁ قفل لینک
⦁ بازکردن لینک
┈┅┅━❥━┅┅┈
⓶ قفل و بازکردن گروه :

⦁ قفل گروه
⦁ بازکردن گروه
┈┅┅━❥━┅┅┈
⓷ قفل خودکار گروه :
◄ با فعال سازی این قفل گروه در زمان تنظیم شده قفل و باز می‌شود

⦁ قفل خودکار
⦁ تنظیم زمان
⦁ بازکردن خودکار
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helppaksazi' then
txt = [[
‼️ به راهنمای دستورات پاکسازی خوشامدید :

⓵ پاکسازی کامل پیام های گروه :
◄ داخل گروه بنویسید :

⦁ پاکسازی همه پیام ها
┈┅┅━❥━┅┅┈
⓶ پاکسازی خودکار :
◄ با کمک پاکسازی خودکار می‌توانید هر روز در ساعت تنظیم شده تمامی پیام های گروه را پاکسازی کنید

⦁ پاکسازی فعال
⦁ پاکسازی غیرفعال
⦁ زمان پاکسازی گروه (زمان مورد نظر)

◄ برای مثال :
زمان پاکسازی گروه 17:00
┈┅┅━❥━┅┅┈
⓷ پاکسازی لیست‌ها :
⦁ پاکسازی مدیران
⦁ پاکسازی اخطاری ها
⦁ پاکسازی مسدودی ها
⦁ پاکسازی سکوتی ها
⦁ پاکسازی لیست فیلتر
⦁ پاکسازی دیلیت اکانتی ها
⦁ پاکسازی ربات ها

◄ توجه داشته باشید که دستور پاکسازی لیست مدیران ، ویژه و لقب مختص مالکین گروه می‌باشد
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpejbari' then
txt = [[
✂️ راهنمای دستورات اد اجباری گروه :

⓵ اد اجباری :
☚ با فعال سازی این قفل تمام کاربران مجبور می‌شوند 3 نفر به گروه  اد کنند تا اجازه چت کردن را بدست بیاورند :

⦁ افزودن اجباری فعال
⦁ افزودن اجباری غیرفعال
┈┅┅━❥━┅┅┈
⓶ تعداد اد اجباری :
☚ برای تعیین تعداد اد اجباری بطور مثال 5 نفر از دستور زیر استقاده می‌کنیم :

⦁ ادداجباری 5
┈┅┅━❥━┅┅┈
⓷ حالت اد اجباری :
☚ برای اینکه فقط اعضای جدید یا تمام اعضای گروه مجبور به اد شوند :

⦁ وضعیت افزودن اجباری جدید
⦁ وضعیت افزودن اجباری همه
┈┅┅━❥━┅┅┈
⓸ معاف کردن :
☚ برای معاف کردن کاربر از اد از دستور زیر استقاده می‌کنیم :

⦁ معاف
┈┅┅━❥━┅┅┈
✂️حضور اجباری کانال (ربات باید در کانال ادمین باشد) :

⓵ حضور اجباری :
☚ با فعال سازی این قفل تمام کاربران مجبور می‌شوند در کانال شما عضو شوند تا اجازه چت کردن را بدست بیاورند :

⦁ جوین اجباری فعال
⦁ جوین اجباری غیرفعال
┈┅┅━❥━┅┅┈
⓶ تنظیم کانال :
☚ برای تنظیم کانال ابتدا بنویسید عضویت اجباری و با یک فاصله آیدی کانال را قرار بدید(بدون @) :

⦁ عضویت اجباری (کانال موردنظر شما)  
 
💬 اکثر دستورات بالا به روش بسیار راحتی از طریق پنل ربات بخش تنظیمات پیشرفته قسمت ادد اجباری گروه و عضویت اجباری قابل تنظیم می‌باشد

┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpfun' then
txt = [[
‼️به بخش راهنمای سرگرمی خوشامدید :

🔶استیکر به عکس و بالعکس :
◄ برای استیکر به عکس کردن و بالعکس توجه داشته باشید رو عکس یا استیکر ریپلای کنید :

⦁ تبدیل به استیکر - tosticker
⦁ تبدیل به عکس - tophoto
┈┅┅━❥━┅┅┈
🔶فونت کلمات :
◄ با این دستور فونت های زیادی را مشاهده میکنید. 
برای مثال : زیباسازی مادر

⦁ زیباسازی - write
┈┅┅━❥━┅┅┈
🔶دستورات کاربردی :
◄ با ارسال هر یک از دستورات زیر گذینه مورد نظر را دریافت کنید . بطور مثال با نوشتن دستور (اوقات شرعی شیراز) اوقات شرعی شیراز ارسال میشود :

⦁ اوقات شرعی (نام شهر مورد نظر)
⦁ پ ن پ - pnp
⦁ فال - fal
⦁ قیمت ارز - currency
⦁ قیمت خودرو - price car
⦁ جوک - jok
⦁ دیالوگ - dialog
⦁ خاطره - memory
⦁ داستان - story
┈┅┅━❥━┅┅┈
🔶سخنگو کردن ربات توسط خودتان :
◄ شما میتوانید با این دستور هر کلمه ایی که بگید ربات میتواند کلمه شما را تکرار کند، به عنوان مثال اکو سلام ربات میگه سلام :

⦁ اکو سلام - echo hi
┈┅┅━❥━┅┅┈
🔶دریافت اطلاعات شما و گروه :
◄ این دستورات اطلاعات کاربری شمارا نشان میدهد شما میتوانید دستور اطلاعات را روی دیگران هم ریپلای بزنید :
⦁ آیدی - id
⦁ امار من - me
⦁ اطلاعات - info
┈┅┅━❥━┅┅┈
🔶تنظیم مقام برای کاربران :
◄ شما میتوانید برای کاربران خود مقام بزارید برای مثال :
تنظیم لقب شیطون و با هر بار صدا کردن ربات لقب شمارا میگوید :

⦁ تنظیم لقب (متن مورد نظر)

◄ برای حذف لقب یک کاربر ازین دستور استفاده میکنیم :

⦁ حذف لقب
┈┅┅━❥━┅┅┈
🔶تگ کردن مدیران :
◄ با این دستور میتوانید ادمین ها را تگ یا صدا کنید :

⦁ تگ مدیران
┈┅┅━❥━┅┅┈ 
🔶 آمار :
◄ برای بدست آوردن آمار فعالیت‌های گروه و فعال ترین اعضا از دستور زیر استفاده می‌کنیم :
⦁ آمار گروه
┈┅┅━❥━┅┅┈
🔶 ساخت گیف با اسم دلخواه شما :
◄ شما میتوانید با این دستور گیف با اسم دلخواهتون بسازید، مثال : مینویسید ساخت گیف ربات برای گیفی بنام ربات رو واستون میاره، بجای جمله، جمله خودتان را بگذارید :

⦁ ساخت گیف جمله - gif name
┈┅┅━❥━┅┅┈
🔶 دریافت روزنامه برای شما :
شما میتوانید روزنامه ایران رو با این دستور ببینید :

⦁ خبر 
 ┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpsetting' then
txt = [[
‼️ به راهنمای دستورات تنظیمی و مدیریتی خوشامدید :

⓵ پنل شیشه ای تنظیمات :

⦁ پنل
┈┅┅━❥━┅┅┈

⓶ تنظیمات گروه :
◄ برای مشاهده قفلهای فعال از دستور زیر استفاده می‌کنیم:

⦁ تنظیمات
┈┅┅━❥━┅┅┈

⓷ سنجاق کردن :

⦁ سنجاق
┈┅┅━❥━┅┅┈
⓸ تنظیم لینک :
◄ توجه کنید هر ادمین لینک مخصوص به خود دارد . ادمین ها نمی‌توانند لینک همدیگر را باطل کنند . اگر مدیر یا ادمین لفت بدهند یا از ادمینی برداشته شوند لینکش باطل می‌شود . برای مدیریت بهتر توصیه می‌کنم مدیر گروه بصورت دستی از تنظیمات گروه لینک را کپی کرده و با ارسال دستور ( تنظیم لینک ) آن را در ربات تنظیم کند :

⦁ تنظیم لینک (لینک خودتان) 
⦁ لینک
┈┅┅━❥━┅┅┈
⓹ باطل کردن لینک :
◄ با دستور باطل کردن لینک ، لینک قبلی ساخته شده در ربات باطل می‌شود و لینک جدید ساخته شده در ربات تنظیم می‌شود!
‎
⦁ باطل کردن لینک
┈┅┅━❥━┅┅┈
⓺ تنظیم قوانین :

⦁ تنظیم قوانین (متن مورد نظر) 
⦁ حذف قوانین 
⦁ قوانین
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpwel' then
txt = [[
‼️ به راهنمای خوشامدگویی خوشامدید :

⓵ فعال کردن و غیر فعال کردن خوشامدگویی :

⦁ خوشامدگویی فعال
⦁ خوشامدگویی غیرفعال
 ┈┅┅━❥━┅┅┈
⓶ تنظیم متن خوشامد :
◄ برای تنظیم ازین دستور استفاده میکنیم :
تنظیم خوشامدگویی سلام {FIRSTNAME} به {GROUP} خوشامدید
ساعت:{TIME} 
تاریخ:{DATE} 
┈┅┅━❥━┅┅┈
⓷ حذف خودکار پیام خوشامد :
◄ با دستور زیر پیام خوشامدگویی پس از زمان مشخص حذف می‌شود و با دستور دوم دیگر حذف نمی‌شود.
⦁ پاکسازی پیام ربات روشن
⦁ پاکسازی پیام ربات خاموش
⦁ زمان پاکسازی پیام ربات (عدد)
بطور مثال :

زمان پاکسازی پیام ربات 30
◄ با این دستور پیام خوشامد بعد از گذشت 30 ثانیه حذف می‌شود.
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpfilter' then
txt = [[
‼️ به راهنمای فیلتر کلمات خوشامدید :

⓵  فیلتر کردن کلمه مورد نظر:
◄ با ارسال دستور اول و سپس کلمه دلخواه کلمه‌ی مورد نظر فیلتر می‌شود و با دستور دوم کلمه‌ی مورد نظر رفع فیلتر می‌شود، مثال فیلترکردن بیشعور :

⦁ فیلتر کردن ... 
⦁ حذف فیلتر ... 

◄ برای مشاهده لیست فیلتر و پاکسازی آن از دو دستور زیر استفاده می‌کنیم
⦁ لیست فیلتر
⦁ پاکسازی لیست فیلتر
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)
elseif MenuName == 'helpragbari' then
txt = [[
‼️ به راهنمای دستورات کنترل رگباری خوشامدید :

⓵ قفل پیام رگباری (فلود) :
☚ با فعال سازی این قفل اگر کاربران بطور رگباری و مکرر پیام ارسال کنند از گروه محروم و یا پیامشان حذف می‌شوند. قفل کردن و بازکردن و تنظیم تعداد پیام رگباری 
⦁ قفل پیام مکرر
⦁ بازکردن پیام مکرر
⦁ تنظیم حداکثر رگبار 3-10
┈┅┅━❥━┅┅┈
⓶ قفل کاراکتر (اسپم) :
☚ با فعال سازی این قفل ربات به تعداد حروف (کاراکتر) تشکیل دهنده پست حساس می‌شود
بطور مثال با ارسال دستور ( حساسیت هرزنامه 150) اگر تعداد حروف داخل پست بیشتر از 150 باشد پست کاربر حذف می‌شود

⦁ قفل هرزنامه
⦁ بازکردن هرزنامه
⦁ حساسیت هرزنامه 1-4000
┈┅┅━❥━┅┅┈
📝کانال ما✌️👇
☛ @]]..Channel..[[
]]
local keyboard = {}
keyboard.inline_keyboard = {{
{text = ''..babi..' بازگشت',callback_data = 'bd:helpmenu:'..chat_id}}}
Edit(msg,txt,keyboard,true)

elseif MenuName == 'privpanel' then 
if base:sismember(TD_ID..'Gp2:'..chat_id,'PanelPv') then
base:srem(TD_ID..'Gp2:'..chat_id,'PanelPv')
Alert(msg.id,babi..'پنل بر روے خصوصی تنظیم شد و مدیر دیگر قادر به کار با پنلی که شما بازکردید نخواهد بود',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'PanelPv')
Alert(msg.id,babi..'پنل بر روے همگانی تنظیم شد و مدیر دیگر قادر به کار با پنلی که شما بازکردید خواهد بود',true)
end
SETT(msg,chat_id,4)

elseif MenuName == 'AddMAXup' then
if tonumber(Add_MAX) == 30 then
Alert(msg.id,'حداکثر مقدار 30',true)
else
Add_MAX = tonumber(base:get(TD_ID..'Force:Max:'..chat_id) or 10)
Add_MAX = tonumber(Add_MAX) + 1
Alert(msg.id,Add_MAX)
base:set(TD_ID..'Force:Max:'..chat_id,Add_MAX)
end
SETT(msg,chat_id,5)
elseif MenuName == 'AddMAXdown' then
if tonumber(Add_MAX) == 2 then
Alert(msg.id,'حداقل مقدار 2',true)
else
Add_MAX = tonumber(base:get(TD_ID..'Force:Max:'..chat_id) or 10)
Add_MAX = tonumber(Add_MAX) - 1
Alert(msg.id,Add_MAX)
base:set(TD_ID..'Force:Max:'..chat_id,Add_MAX)
end
SETT(msg,chat_id,5)
elseif MenuName == 'JoinWarnMAXup' then
if tonumber(JoinWarn) == 200 then
Alert(msg.id,'حداکثر مقدار 200',true)
else
JoinWarn = (base:get(TD_ID..'joinwarn:'..chat_id) or 5)
JoinWarn = tonumber(JoinWarn) + 1
Alert(msg.id,JoinWarn)
base:set(TD_ID..'joinwarn:'..chat_id,JoinWarn)
end
SETT(msg,chat_id,6)
elseif MenuName == 'JoinWarnMAXdown' then
if tonumber(JoinWarn) == 1 then
Alert(msg.id,'حداقل مقدار 1',true)
else
JoinWarn = (base:get(TD_ID..'joinwarn:'..chat_id) or 5)
JoinWarn = tonumber(JoinWarn) - 1
Alert(msg.id,JoinWarn)
base:set(TD_ID..'joinwarn:'..chat_id,JoinWarn)
end
SETT(msg,chat_id,6)
elseif MenuName == 'warnMAXup' then
if tonumber(warn) == 10 then
Alert(msg.id,'حداکثر مقدار 10',true)
else
warn = (base:get(TD_ID.."Warn:Max:"..chat_id) or 3)
warn = tonumber(warn) + 1
Alert(msg.id,warn)
base:set(TD_ID..'Warn:Max:'..chat_id,warn)
end
SETT(msg,chat_id,4)
elseif MenuName == 'warnMAXdown' then
if tonumber(warn) == 1 then
Alert(msg.id,'حداقل مقدار 1',true)
else
warn = (base:get(TD_ID..'Warn:Max:'..chat_id) or 3)
warn = tonumber(warn) - 1
Alert(msg.id,warn)
base:set(TD_ID..'Warn:Max:'..chat_id,warn)
end
SETT(msg,chat_id,4)
elseif MenuName == 'forcepmMAXup' then
if tonumber(forcepm) == 100 then
Alert(msg.id,'حداکثر مقدار 100',true)
else
forcepm = tonumber(base:get(TD_ID..'Force:Pm:'..chat_id) or 3)
forcepm = tonumber(forcepm) + 1
Alert(msg.id,forcepm)
base:set(TD_ID..'Force:Pm:'..chat_id,forcepm)
end
SETT(msg,chat_id,5)
elseif MenuName == 'forcepmMAXdown' then
if tonumber(forcepm) == 1 then
Alert(msg.id,'حداقل مقدار 1',true)
else
forcepm = tonumber(base:get(TD_ID..'Force:Pm:'..chat_id) or 5)
forcepm = tonumber(forcepm) - 1
Alert(msg.id,forcepm)
base:set(TD_ID..'Force:Pm:'..chat_id,forcepm)
end
SETT(msg,chat_id,5)
elseif MenuName == 'MSGMAXup' then
if tonumber(MSG_MAX) == 1000 then
Alert(msg.id,'حداکثر مقدار 1000',true)
else
MSG_MAX = (base:get(TD_ID..'Flood:Max:'..chat_id) or 10)
MSG_MAX = tonumber(MSG_MAX) + 1
Alert(msg.id,MSG_MAX)
base:set(TD_ID..'Flood:Max:'..chat_id,MSG_MAX)
end
SETT(msg,chat_id,4)
elseif MenuName == 'MSGMAXdown' then
if tonumber(MSG_MAX) == 3 then
Alert(msg.id,'حداقل مقدار 3',true)
else
MSG_MAX = (base:get(TD_ID..'Flood:Max:'..chat_id) or 10)
MSG_MAX = tonumber(MSG_MAX) - 1
Alert(msg.id,MSG_MAX)
base:set(TD_ID..'Flood:Max:'..chat_id,MSG_MAX)
end
SETT(msg,chat_id,4)
elseif MenuName == 'TIMEMAXup' then
if tonumber(TIME_CHECK) == 1 then
Alert(msg.id,'حداکثر مقدار 1',true)
else
TIME_CHECK = (base:get(TD_ID..'Flood:Time:'..chat_id) or 2)
TIME_CHECK = tonumber(TIME_CHECK) + 1
Alert(msg.id,TIME_CHECK)
base:set(TD_ID..'Flood:Time:'..chat_id,TIME_CHECK)
end
SETT(msg,chat_id,4)
elseif MenuName == 'TIMEMAXdown' then
if tonumber(TIME_CHECK) == 2 then
Alert(msg.id,'حداقل مقدار 2',true)
else
TIME_CHECK = (base:get(TD_ID..'Flood:Time:'..chat_id) or 2)
TIME_CHECK = tonumber(TIME_CHECK) - 1
Alert(msg.id,TIME_CHECK)
base:set(TD_ID..'Flood:Time:'..chat_id,TIME_CHECK)
end
SETT(msg,chat_id,4)
elseif MenuName == 'CHMAXup' then
if tonumber(CH_MAX) == 4016 then
Alert(msg.id,'حداکثر مقدار 4016',true)
else
CH_MAX = (base:get(TD_ID..'NUM_CH_MAX:'..chat_id) or 200)
CH_MAX= tonumber(CH_MAX) + 50
Alert(msg.id,CH_MAX)
base:set(TD_ID..'NUM_CH_MAX:'..chat_id,CH_MAX)
end
SETT(msg,chat_id,4)
elseif MenuName == 'CHMAXdown' then
if tonumber(CH_MAX) == 50 then
Alert(msg.id,'حداقل مقدار 50',true)
else
CH_MAX = (base:get(TD_ID..'NUM_CH_MAX:'..chat_id) or 200)
CH_MAX= tonumber(CH_MAX) - 50
Alert(msg.id,CH_MAX)
base:set(TD_ID..'NUM_CH_MAX:'..chat_id,CH_MAX)
end
SETT(msg,chat_id,4)
--FilterList
elseif MenuName == 'filterpanel' then
base:del(TD_ID..'limit_filter'..chat_id..msg.from.id,60,msg)
filters(msg,chat_id)
elseif MenuName == 'filterlist' then
Filters = base:smembers(TD_ID..'filters:'..chat_id)
text = 'لیست کلمات فیلتر گروه :\n'
for k,v in pairs(Filters) do
text = text..k..' - *'..v..'*\n' 
end
if #Filters == 0 then
text = babi..'لیست فیلتر خالی میباشد'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..chat_id}}}
Edit(msg,text,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanFilters:'..chat_id},
{text = '⌆ بازگشت', callback_data = 'bd:filterpanel:'..chat_id}}}
Edit(msg,text,keyboard,true)
end
elseif MenuName == 'cleanFilters' then
text = [[*لیست کلمات فیلتر شده پاکسازے شد*]]
base:del(TD_ID..'filters:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..chat_id}}}
Edit(msg,text,keyboard,true)
elseif MenuName == 'addfilter' then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..chat_id}}}
base:setex(TD_ID..'limit_filter'..chat_id..msg.from.id,60,tonumber(msg.message.message_id))
Edit(msg,'کلمه جدید را جهت فیلتر شدن ارسال کنید :',keyboard,true)
elseif MenuName == 'remfilter' then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:filterpanel:'..chat_id}}}
base:setex(TD_ID..'limit_filters'..chat_id..msg.from.id,60,tonumber(msg.message.message_id))
Edit(msg,'کلمه که میخواهید از لیست فیلتر حذف شود را ارسال کنید :',keyboard,true)
elseif MenuName == 'alluser+' and is_Owner(chat_id,msg.from.id) then
if base:sismember(TD_ID..'Gp2:'..chat_id,'force_NewUser') then
Alert(msg.id,babi..'وضعیت افزودن اجبارے از قبل روے #همه کاربران فعال بود!',true)
else
base:sadd(TD_ID..'Gp2:'..chat_id,'force_NewUser')
Alert(msg.id,'> وضعیت افزودن اجبارے روے #همه کاربران فعال شد#',true)
end
SETT(msg,chat_id,5)
elseif MenuName == 'newuser-' and is_Owner(chat_id,msg.from.id) then
if base:sismember(TD_ID..'Gp2:'..chat_id,'force_NewUser') then
base:srem(TD_ID..'Gp2:'..chat_id,'force_NewUser')
Alert(msg.id,'> وضعیت افزودن اجبارے روے کاربران #جدید فعال شد#',true)
else
Alert(msg.id,babi..'وضعیت افزودن اجبارے از قبل روے کاربران جدید #فعال بود!',true)
end
SETT(msg,chat_id,5)

elseif MenuName == 'stickerPK' then
list =  base:smembers("filterpack"..chat_id)
t = '`لیست پک های استیکر ربات در گروه :`\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【'..v..'】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = '`لیست پک استیکر گروه خالی میباشد`'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:PKlist:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
elseif MenuName == 'PKlist' then
t = [[`لیست پک` *پاکسازے شد*]]
base:del("filterpack"..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
--OwnerList
elseif MenuName == 'ownerlist' and is_Owner(chat_id,msg.from.id) then
list =  base:smembers(TD_ID..'OwnerList:'..chat_id)
t = '`لیست مالکان ربات در گروه :`\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = '`لیست مالکان گروه خالی میباشد`'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanownerlist:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
--VipList
elseif MenuName == 'viplist' then
list = base:smembers(TD_ID..'Vip:'..chat_id)
t = '`لیست افراد ویژه گروه :`\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = babi..'لیست افراد ویژه خالی میباشد'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanviplist:'..chat_id},
{text = 'بازگشت ',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
elseif MenuName == 'cleanviplist' then
t = [[`لیست ویژه` *پاکسازے شد*]]
base:del(TD_ID..'Vip:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
--ModList
elseif MenuName == 'modlist' then
list =  base:smembers(TD_ID..'ModList:'..chat_id)
t = '`لیست مدیران گروه :`\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = '`لیست مدیران گروه خالی میباشد.`'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanmodlist:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
--BanList
elseif MenuName == 'banlist' then
list = base:smembers(TD_ID..'BanUser:'..chat_id)
t = 'لیست کاربران مسدود :\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = babi..'لیست کاربران مسدود خالی میباشد'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanbanlist:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
--Mutelist
elseif MenuName == 'mutelist' then
list = base:smembers(TD_ID..'MuteList:'..chat_id)
t = 'لیست کاربران محدودشده :\n─┅━━━━━━━┅─\n'
for k,v in pairs(list) do
t = t..k..'-【['..v..'](tg://user?id='..v..')】\n─┅━━━━━━━┅─\n'
end
if #list == 0 then
t = babi..'لیست محدودشدگان خالی میباشد'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanmutelist:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
--Group Link
elseif MenuName == 'GroupLink' then
base:del(TD_ID..'links'..chat_id..msg.from.id)
if base:get(TD_ID..'Link:'..chat_id) then
link = getChat(chat_id).result.invite_link
else 
link = 'لینک گروه ثبت نشده است'
end
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'باطل کردن لینک',callback_data = 'bd:Deletelink:'..chat_id},
{text = babi..'ثبت لینک',callback_data = 'bd:Setlink:'..chat_id}},{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
editMessageText(chat_id,msg.message.message_id,link,keyboard,'html')
elseif MenuName == 'Setlink' then
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:GroupLink:'..chat_id}}}
base:setex(TD_ID..'links'..chat_id..msg.from.id,60,tonumber(msg.message.message_id))
Edit(msg,'جهت ثبت لینک گروه\nلینک گروه خود را ارسال کنید:',keyboard,true)
elseif MenuName == 'Deletelink' then
local url  = https.request(BASSE .. '/getChatMember?chat_id='..chat_id..'&user_id='..BotId)
if res ~= 200 then
end
statsurl = json:decode(url)
if statsurl.ok == true and statsurl.result.status == 'administrator' then
if exportChatInviteLink(chat_id) then
link = exportChatInviteLink(chat_id).result  
text = 'لینک جدید ساخته شد\n'..link
base:set(TD_ID..'Link:'..chat_id,link)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
EdiT(msg,text,keyboard,true)
end
else
Alert(msg.id,'ربات به قسمت باطل کردن لینک دسرسی ندارد',true)
end
--Rules
elseif MenuName == 'GroupRules' then
rules = base:get(TD_ID..'Rules:'..chat_id) or '*قوانین گروه ثبت نشده است*'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'حذف قوانین',callback_data = 'bd:Delrules:'..chat_id},
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,rules,keyboard,true)
elseif MenuName == 'Delrules' then
base:del(TD_ID..'Rules:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,'*قوانین گروه حذف شد*',keyboard,true)
--WarnList
elseif MenuName == 'warnlist' then
comn = base:hkeys(TD_ID..chat_id..':warn')
t = 'لیست اخطار ها :\n﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄﹃﹄\n'
for k,v in pairs (comn) do
cont = base:hget(TD_ID..chat_id..':warn',v)
t = t..k..'-【['..v..'](tg://user?id='..v..')】\nتعداد اخطار :【'..(cont - 1)..'】\n─┅━━━━━━━┅─\n'
end
if #comn == 0 then
t = '`لیست اخطار براے این گروه ثبت نشده است.`'
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
else
local keyboard = {}
keyboard.inline_keyboard = {{
{text = babi..'پاک کردن',callback_data = 'bd:cleanwarnlist:'..chat_id}},{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
end
--Welcome
elseif MenuName == 'Delwelcome' then
base:del(TD_ID..'welcome:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,babi..'*متن خوش امدگوی گروه حذف شد*',keyboard,true)
--Start Owner
elseif MenuName == 'cleanmodlist' and is_Owner(chat_id,msg.from.id) then
t = [[`لیست مدیران` *پاکسازے شد*]]
base:del(TD_ID..'ModList:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
elseif MenuName == 'cleanbanlist' and is_Owner(chat_id,msg.from.id) then
t = [[`لیست مسدود` *پاکسازے شد*]]
base:del(TD_ID..'BanUser:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
elseif MenuName == 'cleanwarnlist' and is_Owner(chat_id,msg.from.id) then
t = [[`لیست اخطار`  *پاکسازے شد*]]
base:del(TD_ID..chat_id..':warn')
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
elseif MenuName == 'cleanmutelist' and is_Owner(chat_id,msg.from.id) then
t = [[`لیست سکوت`  *پاکسازے شد*]]
base:del(TD_ID..'MuteList:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo2:'..chat_id}}}
Edit(msg,t,keyboard,true)
elseif MenuName == 'cleanownerlist' and is_sudo1(msg.from.id) then
t = [[`لیست مالکان` *پاکسازے شد*]]
base:del(TD_ID..'OwnerList:'..chat_id)
local keyboard = {}
keyboard.inline_keyboard = {{
{text = '⌆ بازگشت',callback_data = 'bd:groupinfo:'..chat_id}}}
Edit(msg,t,keyboard,true)
--EXIT
elseif MenuName == 'Exit' then
lang = base:sismember(TD_ID..'Gp2:'..chat_id,'diamondlang')
if lang then
txt= babi..'Glass list was successfully closed ヅ\nBy :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)
else
txt= babi..'فهرست شیشه اے با موفقیت بسته شد ヅ\nتوسط :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)
end
elseif MenuName == 'Exhlp' then
if lang then
txt= babi..'Help panel successfully closed ヅ\nBy :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)
else
txt = 'پنل شیشه اے راهنما با موفقیت بسته شد ヅ\nتوسط :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)
end
elseif MenuName == 'Exitss' then
txt= 'پنل شیشه اے آمار با موفقیت بسته شد ヅ\nتوسط :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)
elseif MenuName == 'Exitsr' then
txt= 'کنترل پنل با موفقیت بسته شد ヅ\nتوسط :【['..msg.from.first_name:escape_hard()..'](tg://user?id='..msg.from.id..')】'
EdiT(msg,txt,false,true)

elseif MenuName == 'Forceadd' then
SETT(msg,chat_id,5)
elseif MenuName == 'Forcejoin' then
SETT(msg,chat_id,6)


end
end

---Max Locks---
function tdlocks(chat_id,BD)
if BD == 'automuteall' then
txt = 'تعطیل کردن خودکار'
elseif BD == 'forceadd' then
txt = 'افزودن اجباری'
elseif BD == 'forcejoin' then
txt = 'عضویت اجباری'
elseif BD == 'Mute_All' then
txt = 'تعطیل کردن همه'
elseif BD == 'Welcomeon' then
txt = 'خوش امد گوے ربات'
elseif BD == 'Cmd' then
txt = 'قفل دستورات'
elseif BD == 'TGservice' then
txt = 'قفل خدمات'
elseif BD == 'Join' then
txt = 'قفل ورودی'

end end
if msg.data:match('(-%d+):(locks2):(.*):(%d+)') then
Q = msg.data:split(':')
chat_id = Q[1]
BD = Q[3]
page = tonumber(Q[4])
tdlocks(chat_id,BD)
if not is_req(msg) then
Alert(msg.id,'Error !\nشما دسترسی کار کردن با این پنل را ندارید !',true)
else
if not is_Mod(chat_id,msg.from.id) then
Alert(msg.id,'شما از مدیران ربات نیستید',true)
return
end
if base:sismember(TD_ID..'Gp2:'..chat_id,''..BD) then
lock_rem('Gp2:',''..BD)
txxt = ''..txt..' غیرفعال شد'
Alert(msg.id,txxt,true)
else 
lock_add('Gp2:',''..BD)
txxt = ''..txt..' فعال شد'
Alert(msg.id,txxt,true)
end
SETT(msg,chat_id,page)
end
end


if msg.data:match('(snd):(.*):(-%d+)') then
Split = msg.data:split(':')
chat_id = Split[3]
MenuName = Split[2]
if not is_req(msg) then
Alert(msg.id,'Error !\nشما دسترسی کار کردن با این پنل را ندارید !',true)
else
if not is_sudo1(msg.from.id) then
Alert(msg.id,'شما از مدیران ربات نیستید',true)
return
end
if MenuName == 'Sends' then
local plist = {'SendPv','SendSuperGroups','SendOwner','SendAdmin','FwdPv','FwdSuperGroups','FwdOwner','FwdAdmin'}
for k,v in pairs(plist) do
base:del(TD_ID..''..v..''..chat_id)
end
Sends(msg,chat_id,msg.message.message_id,'')
elseif MenuName == 'SendPv' then
xSends(msg,chat_id,msg.message.message_id,'ارسال','پیوی','SendPv')
elseif MenuName == 'SendSuperGroups' then
xSends(msg,chat_id,msg.message.message_id,'ارسال','سوپرگروه','SendSuperGroups')
elseif MenuName == 'SendOwner' then
xSends(msg,chat_id,msg.message.message_id,'ارسال','مالکان','SendOwner')
elseif MenuName == 'SendAdmin' then
xSends(msg,chat_id,msg.message.message_id,'ارسال','ادمین ها','SendAdmin')
elseif MenuName == 'FwdPv' then
xSends(msg,chat_id,msg.message.message_id,'فوروارد','پیوی','FwdPv')
elseif MenuName == 'FwdSuperGroups' then
xSends(msg,chat_id,msg.message.message_id,'فوروارد','سوپرگروه','FwdSuperGroups')
elseif MenuName == 'FwdOwner' then
xSends(msg,chat_id,msg.message.message_id,'فوروارد','مالکان','FwdOwner')
elseif MenuName == 'FwdAdmin' then
xSends(msg,chat_id,msg.message.message_id,'فوروارد','ادمین ها','FwdAdmin')
end
end
------------
end
end--calback

function EditProcess(msg)
if msg.text then
NewContent = msg.text
elseif msg.caption then
NewContent = msg.caption
else
return
end
end

firstUpdate()
while true do
local res = getUpdates((LastUpdate or 0)+1)
if res then
for i=1, #res.result do
--Najva
local msgm = res.result[i]
if msgm.callback_query then
local Diamond = msgm.callback_query
if Diamond.data:match('Najva::(.*)::(.*)') then
Split = Diamond.data:split('::')
user = Split[2]
if (Diamond.from.username and Diamond.from.username:lower() == user:lower()) or tonumber(Diamond.from.id) == tonumber(user) then
if Split[3]:match('^BDMrr(.*)') then
Mrr = Split[3]:gsub('BDMrr','')
text = base:get(Mrr)
if text then
Alert(Diamond.id,Mrr..text,true)
else
Alert(Diamond.id,'پیام مخفی شما منقضی شده است !',true)
end
else
Alert(Diamond.id,Split[3],true)
end
else
Alert(Diamond.id,'پیام مخفی برای شما ارسال نشده است ❗️\nشما قادر به دیدن این پیام نیستید ❌',true)
end
end
end 



BaBaK = res.result[i]
if BaBaK.message then
msg = BaBaK.message
if msg.new_chat_members or msg.left_chat_member or msg.new_chat_title
or msg.new_chat_photo or msg.delete_chat_photo or msg.group_chat_created
or msg.supergroup_chat_created or msg.channel_chat_created then
msg.service = true
end

if msg.text then
if msg.text:match('@'..BotUsername) then
msg.text = msg.text:gsub('@'..BotUsername, '')
end
end
Runing(msg)
--vardump(msg)
if msg.chat.type == 'supergroup' then
if msg.text then
--MSG_CHECKS(msg)
print(clr.blue..'['..os.date('%X')..']'..clr.red..' '..msg.text..clr.reset..'['..msg.from.first_name..']--> ['..msg.chat.id..']')
end
elseif msg.chat.type == 'private' then
if msg.text then
--PV(msg)
end
end


elseif BaBaK.callback_query then
msg = BaBaK.callback_query
calback(msg)
--[[if base:get(TD_ID..'Klick:'..msg.message.chat.id) then
Alert(msg.id,"سریع لمس میکنید 😐\nلطفا آهسته تر!",true)
else
base:setex(TD_ID..'Klick:'..msg.message.chat.id,tonumber(2),true)
end]]




--vardump(msg)
elseif BaBaK.edited_message then
msg = BaBaK.edited_message
EditProcess(msg)
end
end
for i,msg in pairs(res.result) do
LastUpdate = msg.update_id
end
else
print(clr.red..'=> Connection Error!'..clr.reset)
end
end
--FINISH BY : @sudo_hacker