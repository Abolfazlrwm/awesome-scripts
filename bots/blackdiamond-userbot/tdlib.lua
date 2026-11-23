local TDLib = require 'tdlua'
local client = TDLib()
local data_timer , tdlib_functions = {},{}
local data_cache = {
colors_key = {
  reset =      0,
  bright     = 1,
  dim        = 2,
  underline  = 4,
  blink      = 5,
  reverse    = 7,
  hidden     = 8,
  black     = 30,
  red       = 31,
  green     = 32,
  yellow    = 33,
  blue      = 34,
  magenta   = 35,
  cyan      = 36,
  white     = 37,
  blackbg   = 40,
  redbg     = 41,
  greenbg   = 42,
  yellowbg  = 43,
  bluebg    = 44,
  magentabg = 45,
  cyanbg    = 46,
  whitebg   = 47
},
  config = {
  }
}
function print_error(err)
  print(tdlib_functions.colors('%{red}\27[5m bug in your script ! %{reset}\n\n%{red}'.. err))
end
function client_send(input)
  local result = client:execute(input)
  if type(result) ~= 'table' then
    return nil
  else
    return result
  end
end
client:send({
  ["@type"] = 'getAuthorizationState'
})
TDLib.setLogLevel(0)
TDLib.setLogPath('.tdlua.log')
function tdlib_functions.len(input)
  if type(input) == 'table' then
    size = 0
    for key,value in pairs(input) do
      size = size + 1
    end
    return size
  else
    size = tostring(input)
    return #size
  end
end
function tdlib_functions.match(...)
  local val = {}
  for no,v in ipairs({...}) do
    val[v] = true
  end
  return val
end
function tdlib_functions.secToClock(seconds)
  local seconds = tonumber(seconds)
  if seconds <= 0 then
    return {hours=00,mins=00,secs=00}
  else
    local hours = string.format("%02.f", math.floor(seconds / 3600));
    local mins = string.format("%02.f", math.floor(seconds / 60 - ( hours*60 ) ));
    local secs = string.format("%02.f", math.floor(seconds - hours * 3600 - mins * 60));
    return {hours=hours,mins=mins,secs=secs}
  end
end
function tdlib_functions.number_format(num)
  local out = tonumber(num)
  while true do
    out,i= string.gsub(out,'^(-?%d+)(%d%d%d)', '%1,%2')
    if (i==0) then
      break
    end
  end
  return out
end
function tdlib_functions.base64_encode(str)
	local Base ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
	return ((str:gsub('.', function(x)
			local r,Base='',x:byte()
			for i=8,1,-1 do r=r..(Base%2^i-Base%2^(i-1)>0 and '1' or '0') end
			return r;
	end)..'0000'):gsub('%d%d%d?%d?%d?%d?', function(x)
			if (#x < 6) then return '' end
			local c=0
			for i=1,6 do c=c+(x:sub(i,i)=='1' and 2^(6-i) or 0) end
			return Base:sub(c+1,c+1)
	end)..({ '', '==', '=' })[#str%3+1])
end
function tdlib_functions.base64_decode(str)
	local Base ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
  str = string.gsub(str, '[^'..Base..'=]', '')
  return (str:gsub('.', function(x)
    if (x == '=') then
      return ''
    end
    local r,f='',(Base:find(x)-1)
    for i=6,1,-1 do r=r..(f%2^i-f%2^(i-1)>0 and '1' or '0') end
    return r;
  end):gsub('%d%d%d?%d?%d?%d?%d?%d?', function(x)
    if (#x ~= 8) then
      return ''
    end
    local c=0
    for i=1,8 do c=c+(x:sub(i,i)=='1' and 2^(8-i) or 0) end
    return string.char(c)
  end))
end
function tdlib_functions.exists(file)
   local ok, err, code = os.rename(file, file)
   if not ok then
      if code == 13 then
         return true
      end
   end
   return ok, err
end
function tdlib_functions.in_array(table, value)
  for k,v in pairs(table) do
    if value == v then
      return true
    end
  end
  return false
end
function tdlib_functions.colors(buffer)
  for keys in buffer:gmatch('%%{(.-)}') do
    buffer = string.gsub(buffer,'%%{'..keys..'}', '\27['..data_cache.colors_key[keys]..'m')
  end
  return buffer .. '\27[0m'
end
function tdlib_functions.vardump(input)
  local function vardump(value)
     if type(value) == 'table' then
        local dump = '{\n'
        for key,v in pairs(value) do
           if type(key) == 'number' then
             key = '['..key..']'
           elseif type(key) == 'string' and key:match('@') then
             key = '["'..key..'"]'
           end
           if type(v) == 'string' then
             v = "'" .. v .. "'"
           end
           dump = dump .. key .. ' = ' .. vardump(v) .. ',\n'
        end
        return dump .. '}'
     else
        return tostring(value)
     end
   end
  print(tdlib_functions.colors('%{yellow} vardump : %{reset}\n\n%{green}'..vardump(input)))
  return vardump(input)
end
function tdlib_functions.set_timer(seconds, def, argv)
  if type(seconds) ~= 'number' then
    return {
      ["@type"] = false,
      message = 'set_timer(int seconds, funtion def, table)'
    }
  elseif type(def) ~= 'function' then
    return {
      ["@type"] = false,
      message = 'set_timer(int seconds, funtion def, table)'
    }
  end
  data_timer[#data_timer + 1] = {
    def = def,
    argv = argv,
    run_in = os.time() + seconds
  }
  return {
    ["@type"] = true,
    run_in = os.time() + seconds,
    timer_id = #data_timer
  }
end
function tdlib_functions.get_timer(timer_id)
  local timer_data = data_timer[timer_id]
  if timer_data then
    return {
      ["@type"] = true,
      run_in = timer_data.run_in,
      argv = timer_data.argv
    }
  else
    return {
      ["@type"] = false,
    }
  end
end
function tdlib_functions.cancel_timer(timer_id)
  if data_timer[timer_id] then
    table.remove(data_timer,timer_id)
    return {
      ["@type"] = true
    }
  else
    return {
      ["@type"] = false
    }
  end
end
function tdlib_functions.chat_type(chat_id)
local value = 'cache'
  local result = tdlib_functions.getChat(chat_id)
  if result.type then
    if result.type["@type"] == 'chatTypeSupergroup' then
      if result.type.is_channel then
        value = 'is_channel'
      else
        value = 'is_supergroup'
      end
    elseif result.type["@type"] == 'chatTypeBasicGroup' then
      value = 'is_group'
    elseif result.type["@type"] == 'chatTypePrivate' then
      value = 'is_private'
  elseif result.type["@type"] == 'chatTypeSecret' then
    value = 'is_secret'
    end
  end
  return value, result
end
function tdlib_functions.getChats(chat_list, offset_order, offset_chat_id, limit)
  local limit = limit or 500
  local offset_order = offset_order or '9223372036854775807'
  local offset_chat_id = offset_chat_id or 0
  local filter = (string.lower(tostring(chat_list)) == 'archive') and 'chatListArchive' or 'chatListMain'
  return client_send{
    ["@type"] = 'getChats',
    offset_order = offset_order,
    offset_chat_id = offset_chat_id,
    limit = tdlib_functions.setLimit(500),
    chat_list = {
      ["@type"] = filter
    }
  }
end
 function tdlib_functions.all_chats(chat_list)
  local result = {
    group = {
    },
    channel = {
    },
    private = {
    },
    supergroup = {
    },
    secret = {
    },
    other = {
    },
    offset_order = 9223372036854775807,
    off_set_chat = 0
  }
  repeat
    local update = tdlib_functions.getChats(chat_list, result.offset_order , result.off_set_chat, 500)
    for key, value in pairs(update.chat_ids) do
      local chat_type, get_chat = tdlib_functions.chat_type(value)
      result.offset_order = get_chat.positions and get_chat.positions[1] and get_chat.positions[1].order 
      result.off_set_chat = value
      if chat_type == 'is_channel' then
        result.channel[#result.channel + 1] = value
      elseif chat_type == 'is_supergroup' then
        result.supergroup[#result.supergroup + 1] = value
      elseif chat_type == 'is_group' then
        result.group[#result.group + 1] = value
      elseif chat_type == 'is_private' then
        result.private[#result.private + 1] = value
      elseif chat_type == 'is_secret' then
        result.secret[#result.secret + 1] = value
      else
        result.other[#result.other + 1] = value
      end
    end
  until #update.chat_ids ~= 0
  result.offset_order = nil
  result.off_set_chat = nil
  return result
end
function tdlib_functions.getChats_(limit)
  return client_send{
    ["@type"] = 'getChats',
    offset_order="9223372036854775807",       offset_chat_id=0, limit=limit}
end		
function tdlib_functions.replyMarkup(input)
  if type(input.type) ~= 'string' then
    return nil
  end
  local _type = string.lower(input.type)
  if _type == 'inline' then
    local result = {
      ["@type"] = 'replyMarkupInlineKeyboard',
      rows = {
      }
    }
    for _, rows in pairs(input.data) do
      local new_id = #result.rows + 1
      result.rows[new_id] = {}
      for key, value in pairs(rows) do
        local rows_new_id = #result.rows[new_id] + 1
        if value.url and value.text then
          result.rows[new_id][rows_new_id] = {
            ["@type"] = 'inlineKeyboardButton',
            text = value.text,
            type = {
              ["@type"] = 'inlineKeyboardButtonTypeUrl',
              url = value.url
            }
          }
        elseif value.data and value.text then
            result.rows[new_id][rows_new_id] = {
              ["@type"] = 'inlineKeyboardButton',
              text = value.text,
              type = {
                data = tdlib_functions.base64_encode(value.data), -- Base64 only
                ["@type"] = 'inlineKeyboardButtonTypeCallback',
              }
            }
          elseif value.forward_text and value.id and value.url and value.text then
            result.rows[new_id][rows_new_id] = {
              ["@type"] = 'inlineKeyboardButton',
              text = value.text,
              type = {
                id = value.id,
                url = value.url,
                forward_text = value.forward_text,
                ["@type"] = 'inlineKeyboardButtonTypeLoginUrl',
              }
            }
          elseif value.query and value.text then
            result.rows[new_id][rows_new_id] = {
              ["@type"] = 'inlineKeyboardButton',
              text = value.text,
              type = {
                query = value.query,
                ["@type"] = 'inlineKeyboardButtonTypeSwitchInline',
              }
            }
        end
      end
    end
    return result
  elseif _type == 'keyboard' then
    local result = {
      ["@type"] = 'replyMarkupShowKeyboard',
      resize_keyboard = input.resize,
      one_time = input.one_time,
      is_personal = input.is_personal,
      rows = {
      }
    }
    for _, rows in pairs(input.data) do
      local new_id = #result.rows + 1
      result.rows[new_id] = {}
      for key, value in pairs(rows) do
        local rows_new_id = #result.rows[new_id] + 1
        if type(value.type) == 'string' then
          value.type = string.lower(value.type)
          if value.type == 'requestlocation' and value.text then
            result.rows[new_id][rows_new_id] = {
              type = {
                ["@type"] = 'keyboardButtonTypeRequestLocation'
              },
              ["@type"] = 'keyboardButton',
              text = value.text
            }
          elseif value.type == 'requestphone' and value.text then
            result.rows[new_id][rows_new_id] = {
              type = {
                ["@type"] = 'keyboardButtonTypeRequestPhoneNumber'
              },
              ["@type"] = 'keyboardButton',
              text = value.text
            }
          elseif value.type == 'requestpoll' and value.text then
            result.rows[new_id][rows_new_id] = {
              type = {
                ["@type"] = 'keyboardButtonTypeRequestPoll',
                force_regular = value.force_regular,
                force_quiz = value.force_quiz
              },
              ["@type"] = 'keyboardButton',
              text = value.text
            }
          elseif value.type == 'text' and value.text then
            result.rows[new_id][rows_new_id] = {
              type = {
                ["@type"] = 'keyboardButtonTypeText'
              },
              ["@type"] = 'keyboardButton',
              text = value.text
            }
          end
        end
      end
    end
    return result
  elseif _type == 'forcereply' then
    return {
      ["@type"] = 'replyMarkupForceReply',
      is_personal = input.is_personal
    }
  elseif _type == 'remove' then
    return {
      ["@type"] = 'replyMarkupRemoveKeyboard',
      is_personal = input.is_personal
    }
  end
end
function tdlib_functions.addProxy(proxy, server, port)
  return client_send{
    ["@type"] = 'addProxy',
    server = server,
    port = port,
    type = proxy
  }
end
function tdlib_functions.addProxy_(proxy_type, server, port, username, password_secret, http_only)
if type(proxy_type) ~= 'string' then
return client_send{
["@type"] = false
}
end
local proxy_type = string.lower(proxy_type)
if proxy_type == 'mtproto' then
_type_ = {
["@type"] = 'proxyTypeMtproto',
secret = password_secret
}
elseif proxy_Type == 'socks5' then
_type_ = {
["@type"] = 'proxyTypeSocks5',
username = username,
password = password_secret
}
elseif proxy_Type == 'http' then
_type_ = {
["@type"] = 'proxyTypeHttp',
username = username,
password = password_secret,
http_only = http_only
}
  else
return client_send{
["@type"] = false
}
end
return client_send{
["@type"] = 'addProxy',
server = server,
port = port,
type = _type_
}
end
function tdlib_functions.enableProxy(proxy_id)
  return client_send{
    ["@type"] = 'enableProxy',
    proxy_id = proxy_id
  }
end
function tdlib_functions.removeProxy(proxy_id)
  return client_send{
    ["@type"] = 'removeProxy',
    proxy_id = proxy_id
  }
end
function tdlib_functions.pingProxy(proxy_id)
  return client_send{
    ["@type"] = 'pingProxy',
    proxy_id = proxy_id
  }
end
function tdlib_functions.disableProxy(proxy_id)
  return client_send{
    ["@type"] = 'disableProxy',
    proxy_id = proxy_id
  }
end
function tdlib_functions.getProxies()
  return client_send{
    ["@type"] = 'getProxies'
  }
end
function tdlib_functions.delProxies()
  return client_send{
    ["@type"] = 'delProxies'
  }
end
function tdlib_functions.getChatId(chat_id)
  local chat_id = tostring(chat_id)
  if chat_id:match('^-100') then
    return {
      id = string.sub(chat_id, 5),
      type = 'supergroup'
    }
  else
    local basicgroup_id = string.sub(chat_id, 2)
    return {
      id = string.sub(chat_id, 2),
      type = 'basicgroup'
    }
  end
end
function tdlib_functions.getInputFile(file, conversion_str, expected_size)
  local str = tostring(file)
  if (conversion_str and expectedsize) then
    return {
      ["@type"] = 'inputFileGenerated',
      original_path = tostring(file),
      conversion = tostring(conversion_str),
      expected_size = expected_size
    }
  else
    if str:match('/') then
      return {
        ["@type"] = 'inputFileLocal',
        path = file
      }
    elseif str:match('^%d+$') then
      return {
        ["@type"] = 'inputFileId',
        id = file
      }
    else
      return {
        ["@type"] = 'inputFileRemote',
        id = file
      }
    end
  end
end
function tdlib_functions.getInputChatPhoto(file, type, main_frame_timestamp)
     if type == 'ChatPhotoAnimation' then
        return {
          ["@type"] = 'inputChatPhotoAnimation',
          animation = tdlib_functions.getInputFile(file),
          main_frame_timestamp = main_frame_timestamp
          }
     elseif type == 'ChatPhotoStatic' then
       return {
          ["@type"] = 'inputChatPhotoStatic',
          photo = tdlib_functions.getInputFile(file)
          }
     elseif type == 'ChatPhotoPrevious' then
       return {
          ["@type"] = 'inputChatPhotoPrevious',
          chat_photo_id = file
          }
    end
end
function tdlib_functions.getParseMode(parse_mode)
  if parse_mode then
    local mode = parse_mode:lower()
    if mode == 'markdown' or mode == 'md' then
      return {
        ["@type"] = 'textParseModeMarkdown'
      }
    elseif mode == 'html' or mode == 'lg' then
      return {
        ["@type"] = 'textParseModeHTML'
      }
    end
  end
end
function tdlib_functions.parseTextEntities(text, parse_mode)
  if type(parse_mode) == 'string' and string.lower(parse_mode) == 'lg' then
    for txt in text:gmatch('%%{(.-)}') do
      local _text, text_type = txt:match('(.*),(.*)')
      local txt = string.gsub(txt,'+','++')
      local text_type = string.gsub(text_type,' ','')
      if type(_text) == 'string' and type(text_type) == 'string' then
        for key, value in pairs({['<'] = '&lt;',['>'] = '&gt;'}) do
          _text = string.gsub(_text, key, value)
        end
        if (string.lower(text_type) == 'b' or string.lower(text_type) == 'i' or string.lower(text_type) == 'c') then
          local text_type = string.lower(text_type)
          local text_type = text_type == 'c' and 'code' or text_type
          text = string.gsub(text,'%%{'..txt..'}','<'..text_type..'>'.._text..'</'..text_type..'>')
        else
          if type(tonumber(text_type)) == 'number' then
            link = 'tg://user?id='..text_type
          else
            link = text_type
          end
          text = string.gsub(text, '%%{'..txt..'}', '<a href="'..link..'">'.._text..'</a>')
        end
      end
    end
  end
  return client_send{
    ["@type"] = 'parseTextEntities',
    text = tostring(text),
    parse_mode = tdlib_functions.getParseMode(parse_mode)
  }
end
function tdlib_functions.vectorize(table)
  if type(table) == 'table' then
    return table
  else
    return {
      table
    }
  end
end
function tdlib_functions.setLimit(limit, num)
  local limit = tonumber(limit)
  local number = tonumber(num or limit)
  return (number >= limit) and limit or number
end
function tdlib_functions.sendChatAction(chat_id, action, progress)
  return client_send{
    ["@type"] = 'sendChatAction',
    chat_id = chat_id,
    action = {
    ["@type"] = 'chatAction' .. action,
      progress = progress or 100
    }
  }
  end

function tdlib_functions.sendMessage(chat_id,message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'Typing',100)
  local tdlib_body, message = {
        ["@type"] = 'sendMessage',
        chat_id = chat_id,
        reply_to = {
          ["@type"] = 'inputMessageReplyToMessage',
          chat_id = chat_id,
          message_id = message_id or 0
        },
        options = {
          ["@type"] = 'messageSendOptions',
          disable_notification = disable_notification or 0 ,
          from_background = from_background or 1 
          },
        reply_markup = reply_markup,
        input_message_content = input_message_content
      }, {}
  if input_message_content.text then
    text = input_message_content.text.text
  elseif input_message_content.caption then
    text = input_message_content.caption.text
  end
  if text then
    if parse_mode then
      local result = tdlib_functions.parseTextEntities(text, parse_mode)
      if tdlib_body.input_message_content.text then
        tdlib_body.input_message_content.text = result
      else
        tdlib_body.input_message_content.caption = result
      end
      return client_send(tdlib_body)
    else
      while #text > 4096 do
        text = string.sub(text, 4096, #text)
        message[#message + 1] = text
      end
      message[#message + 1] = text
      for i = 1, #message do
        if input_message_content.text and input_message_content.text.text then
          tdlib_body.input_message_content.text.text = message[i]
        elseif input_message_content.caption and input_message_content.caption.text then
          tdlib_body.input_message_content.caption.text = message[i]
        end
        return client_send(tdlib_body)
      end
    end
  else
    return client_send(tdlib_body)
  end
end
function tdlib_functions.logOut()
  return client_send{
    ["@type"] = 'logOut'
  }
end
function tdlib_functions.getPasswordState()
  return client_send{
    ["@type"] = 'getPasswordState'
  }
end
function tdlib_functions.setPassword(old_password, new_password, new_hint, set_recovery_email_address, new_recovery_email_address)
  return client_send{
    ["@type"] = 'setPassword',
    old_password = tostring(old_password),
    new_password = tostring(new_password),
    new_hint = tostring(new_hint),
    set_recovery_email_address = set_recovery_email_address,
    new_recovery_email_address = tostring(new_recovery_email_address)
  }
end
function tdlib_functions.getRecoveryEmailAddress(password)
  return client_send{
    ["@type"] = 'getRecoveryEmailAddress',
    password = tostring(password)
  }
end
function tdlib_functions.setRecoveryEmailAddress(password, new_recovery_email_address)
  return client_send{
    ["@type"] = 'setRecoveryEmailAddress',
    password = tostring(password),
    new_recovery_email_address = tostring(new_recovery_email_address)
  }
end
function tdlib_functions.requestPasswordRecovery()
  return client_send{
    ["@type"] = 'requestPasswordRecovery'
  }
end
function tdlib_functions.recoverPassword(recovery_code)
  return client_send{
    ["@type"] = 'recoverPassword',
    recovery_code = tostring(recovery_code)
  }
end
function tdlib_functions.createTemporaryPassword(password, valid_for)
  local valid_for = valid_for > 86400 and 86400 or valid_for
  return client_send{
    ["@type"] = 'createTemporaryPassword',
    password = tostring(password),
    valid_for = valid_for
  }
end
function tdlib_functions.getTemporaryPasswordState()
  return client_send{
    ["@type"] = 'getTemporaryPasswordState'
  }
end
function tdlib_functions.getMe()
  return client_send({
    ["@type"] = 'getMe'
  })
end
function tdlib_functions.getUser(user_id)
  return client_send{
    ["@type"] = 'getUser',
    user_id = user_id
  }
end
function tdlib_functions.getUserFullInfo(user_id)
  return client_send{
    ["@type"] = 'getUserFullInfo',
    user_id = user_id
  }
end
function tdlib_functions.getBasicGroup(basic_group_id)
  return client_send{
    ["@type"] = 'getBasicGroup',
    basic_group_id = tdlib_functions.getChatId(basic_group_id).id
  }
end
function tdlib_functions.getBasicGroupFullInfo(basic_group_id)
  return client_send{
    ["@type"] = 'getBasicGroupFullInfo',
    basic_group_id = tdlib_functions.getChatId(basic_group_id).id
  }
end
function tdlib_functions.getSupergroup(supergroup_id)
  return client_send{
    ["@type"] = 'getSupergroup',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id
  }
end
function tdlib_functions.getSupergroupFullInfo(supergroup_id)
  return client_send{
    ["@type"] = 'getSupergroupFullInfo',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id
  }
end
function tdlib_functions.getSecretChat(secret_chat_id)
  return client_send{
    ["@type"] = 'getSecretChat',
    secret_chat_id = secret_chat_id
  }
end
function tdlib_functions.getChat(chat_id)
  return client_send{
    ["@type"] = 'getChat',
    chat_id = chat_id
  }
end
function tdlib_functions.getMessage(chat_id, message_id)
  return client_send{
    ["@type"] = 'getMessage',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.getMessageThread(chat_id, message_id)
  return client_send{
    ["@type"] = 'getMessageThread',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.getMessageThreadHistory(chat_id, message_id,from_message_id,offset,limit)
  return client_send{
    ["@type"] = 'getMessageThreadHistory',
    chat_id = chat_id,
    message_id = message_id,
    from_message_id = from_message_id,
    offset = offset,
    limit = limit
  }
end
function tdlib_functions.getRepliedMessage(chat_id, message_id)
  return client_send{
    ["@type"] = 'getRepliedMessage',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.getChatPinnedMessage(chat_id)
  return client_send{
    ["@type"] = 'getChatPinnedMessage',
    chat_id = chat_id
  }
end
function tdlib_functions.getMessages(chat_id, message_ids)
  return client_send{
    ["@type"] = 'getMessages',
    chat_id = chat_id,
    message_ids = tdlib_functions.vectorize(message_ids)
  }
end
function tdlib_functions.getFile(file_id)
  return client_send{
    ["@type"] = 'getFile',
    file_id = file_id
  }
end
function tdlib_functions.getRemoteFile(remote_file_id, file_type)
  return client_send{
    ["@type"] = 'getRemoteFile',
    remote_file_id = tostring(remote_file_id),
    file_type = {
      ["@type"] = 'fileType' .. file_type or 'Unknown'
    }
  }
end
function tdlib_functions.searchPublicChat(username)
  return client_send{
    ["@type"] = 'searchPublicChat',
    username = tostring(username)
  }
end
function tdlib_functions.searchPublicChats(query)
  return client_send{
    ["@type"] = 'searchPublicChats',
    query = tostring(query)
  }
end
function tdlib_functions.searchChats(query, limit)
  return client_send{
    ["@type"] = 'searchChats',
    query = tostring(query),
    limit = limit
  }
end
function tdlib_functions.checkChatUsername(chat_id, username)
  return client_send{
    ["@type"] = 'checkChatUsername',
    chat_id = chat_id,
    username = tostring(username)
  }
end
function tdlib_functions.searchChatsOnServer(query, limit)
  return client_send{
    ["@type"] = 'searchChatsOnServer',
    query = tostring(query),
    limit = limit
  }
end
function tdlib_functions.clearRecentlyFoundChats()
  return client_send{
    ["@type"] = 'clearRecentlyFoundChats'
  }
end
function tdlib_functions.getTopChats(category, limit)
  return client_send{
    ["@type"] = 'getTopChats',
    category = {
      ["@type"] = 'topChatCategory' .. category
    },
    limit = tdlib_functions.setLimit(30, limit)
  }
end
function tdlib_functions.removeTopChat(category, chat_id)
  return client_send{
    ["@type"] = 'removeTopChat',
    category = {
      ["@type"] = 'topChatCategory' .. category
    },
    chat_id = chat_id
  }
end
function tdlib_functions.addRecentlyFoundChat(chat_id)
  return client_send{
    ["@type"] = 'addRecentlyFoundChat',
    chat_id = chat_id
  }
end
function tdlib_functions.getCreatedPublicChats()
  return client_send{
    ["@type"] = 'getCreatedPublicChats'
  }
end
function tdlib_functions.removeRecentlyFoundChat(chat_id)
  return client_send{
    ["@type"] = 'removeRecentlyFoundChat',
    chat_id = chat_id
  }
end
function tdlib_functions.getChatHistory(chat_id, from_message_id, offset, limit, only_local)
  return client_send{
    ["@type"] = 'getChatHistory',
    chat_id = chat_id,
    from_message_id = from_message_id,
    offset = offset,
    limit = tdlib_functions.setLimit(500, limit),
    only_local = only_local
  }
end
function tdlib_functions.getGroupsInCommon(user_id, offset_chat_id, limit)
  return client_send{
    ["@type"] = 'getGroupsInCommon',
    user_id = user_id,
    offset_chat_id = offset_chat_id or 0,
    limit = tdlib_functions.setLimit(100, limit)
  }
end
function tdlib_functions.searchMessages(query, offset_date, offset_chat_id, offset_message_id, limit)
  return client_send{
    ["@type"] = 'searchMessages',
    query = tostring(query),
    offset_date = offset_date or 0,
    offset_chat_id = offset_chat_id or 0,
    offset_message_id = offset_message_id or 0,
    limit = tdlib_functions.setLimit(100, limit)
  }
end
function tdlib_functions.searchChatMessages(chat_id, query, filter, sender_user_id, from_message_id, offset, limit)
  return client_send{
    ["@type"] = 'searchChatMessages',
    chat_id = chat_id,
    query = tostring(query),
    sender_user_id = sender_user_id or 0,
    from_message_id = from_message_id or 0,
    offset = offset or 0,
    limit = tdlib_functions.setLimit(100, limit),
    filter = {
      ["@type"] = 'searchMessagesFilter' .. filter
    }
  }
end
function tdlib_functions.searchSecretMessages(chat_id, query, from_search_id, limit, filter)
  local filter = filter or 'Empty'
  return client_send{
    ["@type"] = 'searchSecretMessages',
    chat_id = chat_id or 0,
    query = tostring(query),
    from_search_id = from_search_id or 0,
    limit = tdlib_functions.setLimit(100, limit),
    filter = {
      ["@type"] = 'searchMessagesFilter' .. filter
    }
  }
end
function tdlib_functions.deleteChatHistory(chat_id, remove_from_chat_list)
  return client_send{
    ["@type"] = 'deleteChatHistory',
    chat_id = chat_id,
    remove_from_chat_list = remove_from_chat_list
  }
end
function tdlib_functions.searchCallMessages(from_message_id, limit, only_missed)
  return client_send{
    ["@type"] = 'searchCallMessages',
    from_message_id = from_message_id or 0,
    limit = tdlib_functions.setLimit(100, limit),
    only_missed = only_missed
  }
end
function tdlib_functions.getChatMessageByDate(chat_id, date)
  return client_send{
    ["@type"] = 'getChatMessageByDate',
    chat_id = chat_id,
    date = date
  }
end
function tdlib_functions.getPublicMessageLink(chat_id, message_id, for_album)
  return client_send{
    ["@type"] = 'getPublicMessageLink',
    chat_id = chat_id,
    message_id = message_id,
    for_album = for_album
  }
end
function tdlib_functions.sendMessageAlbum(chat_id, reply_to_message_id, input_message_contents, disable_notification, from_background)
  return client_send{
    ["@type"] = 'sendMessageAlbum',
    chat_id = chat_id,
    reply_to_message_id = reply_to_message_id or 0,
    disable_notification = disable_notification,
    from_background = from_background,
    input_message_contents = tdlib_functions.vectorize(input_message_contents)
  }
end
function tdlib_functions.sendBotStartMessage(bot_user_id, chat_id, parameter)
  return client_send{
    ["@type"] = 'sendBotStartMessage',
    bot_user_id = bot_user_id,
    chat_id = chat_id,
    parameter = tostring(parameter)
  }
end
function tdlib_functions.sendInlineQueryResultMessage(chat_id, reply_to_message_id, disable_notification, from_background, query_id, result_id)
  return client_send{
    ["@type"] = 'sendInlineQueryResultMessage',
    chat_id = chat_id,
    reply_to_message_id = reply_to_message_id,
    disable_notification = disable_notification,
    from_background = from_background,
    query_id = query_id,
    result_id = tostring(result_id)
  }
end
function tdlib_functions.forwardMessages(chat_id, from_chat_id, message_ids, disable_notification, from_background, as_album, send_copy, remove_caption)
  return client_send{
    ["@type"] = 'forwardMessages',
    chat_id = chat_id,
    from_chat_id = from_chat_id,
    message_ids = tdlib_functions.vectorize(message_ids),
    disable_notification = disable_notification,
    from_background = from_background,
    as_album = as_album,
    send_copy = send_copy,
    remove_caption = remove_caption
  }
end
function tdlib_functions.fwd(chat_id, from_chat_id, message_ids, disable_notification, from_background, as_album)
  return client_send{
    ["@type"] = 'forwardMessages',
    chat_id = chat_id,
    from_chat_id = from_chat_id,
    message_ids = tdlib_functions.vectorize(message_ids),
    disable_notification = disable_notification,
    from_background = from_background,
    as_album = as_album,
    send_copy = true,
    remove_caption = false
  }
end
function tdlib_functions.sendReaction(chat_id,message_id,reaction)
  return client_send{
    ["@type"] = 'setMessageReaction',
    chat_id = chat_id,
    message_id = message_id,
    reaction = reaction,
    is_big = true
  }
end
function tdlib_functions.getMessageAvailableReactions(chat_id,message_id)
  return client_send{
    ["@type"] = 'getMessageAvailableReactions',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.getMessageAddedReactions(chat_id,message_id,reaction,offset,limit)
  return client_send{
     ["@type"] = 'getMessageAddedReactions',
     chat_id = chat_id,
     message_id = message_id,
     reaction = reaction,
     offset = offset,
     limit = limit
  }
end
function tdlib_functions.getChatListsToAddChat(chat_id)
  return client_send{
    ["@type"] = 'getChatListsToAddChat',
    chat_id = chat_id
  }
end
function tdlib_functions.addChatToList(chat_id)
  return client_send{
    ["@type"] = 'addChatToList',
    chat_id = chat_id,
    chat_list = tdlib_functions.getChatListsToAddChat(chat_id).chat_lists[1]
  }
end
function tdlib_functions.sendChatSetTtlMessage(chat_id, ttl)
  return client_send{
    ["@type"] = 'sendChatSetTtlMessage',
    chat_id = chat_id,
    ttl = ttl
  }
end
function tdlib_functions.sendChatScreenshotTakenNotification(chat_id)
  return client_send{
    ["@type"] = 'sendChatScreenshotTakenNotification',
    chat_id = chat_id
  }
end
function tdlib_functions.deleteMessages(chat_id, message_ids, revoke)
  return client_send{
    ["@type"] = 'deleteMessages',
    chat_id = chat_id,
    message_ids = tdlib_functions.vectorize(message_ids),
    revoke = revoke
  }
end
function tdlib_functions.getMessageSender(user_id)
 if tostring(user_id):match('^-100') then
  return {
  _ = 'messageSenderChat',
  chat_id = user_id
  }
 else
  return {
  _ = 'messageSenderUser',
  user_id = tonumber(user_id)
  }
 end
end
function tdlib_functions.deleteChatMessagesBySender(chat_id, user_id)
 return client_send{
  ["@type"] = 'deleteChatMessagesBySender',
  chat_id = chat_id,
  sender_id = tdlib_functions.getMessageSender(user_id)
 }
end
function tdlib_functions.deleteChatMessagesFromUser(chat_id, user_id)
  return client_send{
    ["@type"] = 'deleteChatMessagesFromUser',
    chat_id = chat_id,
    user_id = user_id
  }
end
function tdlib_functions.editMessageText(chat_id, message_id, text, parse_mode,reply_markup)
  local tdlib_body = {
    ["@type"] = 'editMessageText',
    chat_id = chat_id,
    message_id = message_id,
    reply_markup = reply_markup,
    input_message_content = {
      ["@type"] = 'inputMessageText',
      disable_web_page_preview = true,
      text = {
        text = text
      },
      clear_draft = 0
    }
  }
  if parse_mode then
    tdlib_body.input_message_content.text = tdlib_functions.parseTextEntities(text, parse_mode)
  end
  return client_send(tdlib_body)
end
function tdlib_functions.editMessageCaption(chat_id, message_id, caption, parse_mode, reply_markup)
  local tdlib_body = {
    ["@type"] = 'editMessageCaption',
    chat_id = chat_id,
    message_id = message_id,
    reply_markup = reply_markup,
    caption = caption
  }
  if parse_mode then
      tdlib_body.caption = tdlib_functions.parseTextEntities(text,parse_mode)
  end
  return client_send(tdlib_body)
end
function tdlib_functions.getTextEntities(text)
  return client_send{
    ["@type"] = 'getTextEntities',
    text = tostring(text)
  }
end
function tdlib_functions.getFileMimeType(file_name)
  return client_send{
    ["@type"] = 'getFileMimeType',
    file_name = tostring(file_name)
  }
end
function tdlib_functions.getFileExtension(mime_type)
  return client_send{
    ["@type"] = 'getFileExtension',
    mime_type = tostring(mime_type)
  }
end
function tdlib_functions.getInlineQueryResults(bot_user_id, chat_id, latitude, longitude, query, offset)
  return client_send{
    ["@type"] = 'getInlineQueryResults',
    bot_user_id = bot_user_id,
    chat_id = chat_id,
    user_location = {
      ["@type"] = 'location',
      latitude = latitude,
      longitude = longitude
    },
    query = tostring(query),
    offset = tostring(offset)
  }
end
function tdlib_functions.answerCallbackQuery(callback_query_id, text, show_alert, url, cache_time)
  return client_send{
        ["@type"] = 'answerCallbackQuery',
        callback_query_id = callback_query_id,
        show_alert = show_alert,
        cache_time = cache_time,
        text = text,
        url = url,
  }
end
function tdlib_functions.getCallbackQueryAnswer(chat_id, message_id, payload, data, game_short_name)
  return client_send{
    ["@type"] = 'getCallbackQueryAnswer',
    chat_id = chat_id,
    message_id = message_id,
    payload = {
      ["@type"] = 'callbackQueryPayload' .. payload,
      data = data,
      game_short_name = game_short_name
    }
  }
end
function tdlib_functions.deleteChatReplyMarkup(chat_id, message_id)
  return client_send{
    ["@type"] = 'deleteChatReplyMarkup',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.openChat(chat_id)
  return client_send{
    ["@type"] = 'openChat',
    chat_id = chat_id
  }
end
function tdlib_functions.closeChat(chat_id)
  return client_send{
    ["@type"] = 'closeChat',
    chat_id = chat_id
  }
end
function tdlib_functions.viewMessages(chat_id, message_ids, force_read)
  return client_send{
    ["@type"] = 'viewMessages',
    chat_id = chat_id,
    message_ids = tdlib_functions.vectorize(message_ids),
    force_read = force_read
  }
end
function tdlib_functions.openMessageContent(chat_id, message_id)
  return client_send{
    ["@type"] = 'openMessageContent',
    chat_id = chat_id,
    message_id = message_id
  }
end
function tdlib_functions.readAllChatMentions(chat_id)
  return client_send{
    ["@type"] = 'readAllChatMentions',
    chat_id = chat_id
  }
end
function tdlib_functions.createPrivateChat(user_id, force)
  return client_send{
    ["@type"] = 'createPrivateChat',
    user_id = user_id,
    force = force
  }
end
function tdlib_functions.createBasicGroupChat(basic_group_id, force)
  return client_send{
    ["@type"] = 'createBasicGroupChat',
    basic_group_id = tdlib_functions.getChatId(basic_group_id).id,
    force = force
  }
end
function tdlib_functions.createSupergroupChat(supergroup_id, force)
  return client_send{
    ["@type"] = 'createSupergroupChat',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    force = force
  }
end
function tdlib_functions.createSecretChat(secret_chat_id)
  return client_send{
    ["@type"] = 'createSecretChat',
    secret_chat_id = secret_chat_id
  }
end
function tdlib_functions.createNewBasicGroupChat(user_ids, title)
  return client_send{
    ["@type"] = 'createNewBasicGroupChat',
    user_ids = tdlib_functions.vectorize(user_ids),
    title = tostring(title)
  }
end
function tdlib_functions.createNewSupergroupChat(title, is_channel, description)
  return client_send{
    ["@type"] = 'createNewSupergroupChat',
    title = tostring(title),
    is_channel = is_channel,
    description = tostring(description)
  }
end
function tdlib_functions.createNewSecretChat(user_id)
  return client_send{
    ["@type"] = 'createNewSecretChat',
    user_id = tonumber(user_id)
  }
end
function tdlib_functions.upgradeBasicGroupChatToSupergroupChat(chat_id)
  return client_send{
    ["@type"] = 'upgradeBasicGroupChatToSupergroupChat',
    chat_id = chat_id
  }
end
function tdlib_functions.setChatPermissions(chat_id, can_send_messages, can_send_media_messages, can_send_polls, can_send_other_messages, can_add_web_page_previews, can_change_info, can_invite_users, can_pin_messages)
  return client_send{
    ["@type"] = 'setChatPermissions',
    chat_id = chat_id,
     permissions = {
      can_send_messages = can_send_messages,
      can_send_media_messages = can_send_media_messages,
      can_send_polls = can_send_polls,
      can_send_other_messages = can_send_other_messages,
      can_add_web_page_previews = can_add_web_page_previews,
      can_change_info = can_change_info,
      can_invite_users = can_invite_users,
      can_pin_messages = can_pin_messages,
    }
  }
end
function tdlib_functions.setChatTitle(chat_id, title)
  return client_send{
    ["@type"] = 'setChatTitle',
    chat_id = chat_id,
    title = tostring(title)
  }
end
function tdlib_functions.setChatPhoto(chat_id, photo)
  return client_send{
    ["@type"] = 'setChatPhoto',
    chat_id = chat_id,
    photo = getInputFile(photo)
  }
end
function tdlib_functions.setChatDraftMessage(chat_id, reply_to_message_id, text, parse_mode, disable_web_page_preview, clear_draft)
  local tdlib_body = {
    ["@type"] = 'setChatDraftMessage',
    chat_id = chat_id,
    draft_message = {
      ["@type"] = 'draftMessage',
      reply_to_message_id = reply_to_message_id,
      input_message_text = {
        ["@type"] = 'inputMessageText',
        disable_web_page_preview = disable_web_page_preview,
        text = {text = text},
        clear_draft = clear_draft
      }
    }
  }
  if parse_mode then
      tdlib_body.draft_message.input_message_text.text = tdlib_functions.parseTextEntities(text, parse_mode)
  end
  return client_send(tdlib_body)
end
function tdlib_functions.toggleChatIsPinned(chat_id, is_pinned)
  return client_send{
    ["@type"] = 'toggleChatIsPinned',
    chat_id = chat_id,
    is_pinned = is_pinned
  }
end
function tdlib_functions.setChatClientData(chat_id, client_data)
  return client_send{
    ["@type"] = 'setChatClientData',
    chat_id = chat_id,
    client_data = tostring(client_data)
  }
end
function tdlib_functions.addChatMember(chat_id, user_id, forward_limit)
  return client_send{
    ["@type"] = 'addChatMember',
    chat_id = chat_id,
    user_id = user_id,
    forward_limit = tdlib_functions.setLimit(300, forward_limit)
  }
end
function tdlib_functions.addChatMembers(chat_id, user_ids)
  return client_send{
    ["@type"] = 'addChatMembers',
    chat_id = chat_id,
    user_ids = tdlib_functions.vectorize(user_ids)
  }
end
function tdlib_functions.setChatMemberStatus(chat_id, user_id, status, right)
  local right = right and tdlib_functions.vectorize(right) or {}
  local status = string.lower(status)
  if status == 'creator' then
    chat_member_status = {
    ["@type"] = 'chatMemberStatusCreator',
    is_member = right[1] or 1
    }
  elseif status == 'administrator' then
    chat_member_status = {
    ["@type"] = 'chatMemberStatusAdministrator',
    can_be_edited = right[1] or 1,
    can_change_info = right[2] or 1,
    can_post_messages = right[3] or 1,
    can_edit_messages = right[4] or 1,
    can_delete_messages = right[5] or 1,
    can_invite_users = right[6] or 1,
    can_restrict_members = right[7] or 1,
    can_pin_messages = right[8] or 1,
    can_promote_members = right[9] or 0
    }
  elseif status == 'restricted' then
    chat_member_status = {
    permissions = {
    ["@type"] = 'chatPermissions',
    can_send_polls = right[2] or 0,
    can_add_web_page_previews = right[3] or 1,
    can_change_info = right[4] or 0,
    can_invite_users = right[5] or 1,
    can_pin_messages = right[6] or 0,
    can_send_media_messages = right[7] or 1,
    can_send_basic_messages = right[8] or 1,
    can_send_other_messages = right[9] or 1
    },
    is_member = right[1] or 1,
    restricted_until_date = right[10] or 0,
    ["@type"] = 'chatMemberStatusRestricted'
    }
  elseif status == 'ban' then
    chat_member_status = {
    ["@type"] = 'chatMemberStatusBanned',
    banned_until_date = right[1] or 0
    }
  end
  return client_send{
  ["@type"] = 'setChatMemberStatus',
  chat_id = chat_id,
  member_id = {
  ["@type"] = 'messageSenderUser',
  user_id = user_id
  },
  status = chat_member_status or {}
  }
end
function tdlib_functions.getChatMember(chat_id, user_id)
  return client_send{
    ["@type"] = 'getChatMember',
    chat_id = chat_id,
    member_id = {
    ["@type"] = 'messageSenderUser',
    user_id = user_id}
  }
end
function tdlib_functions.searchChatMembers(chat_id, query, limit)
  return client_send{
    ["@type"] = 'searchChatMembers',
    chat_id = chat_id,
    query = tostring(query),
    limit = tdlib_functions.setLimit(200, limit)
  }
end
function tdlib_functions.getChatAdministrators(chat_id)
  return client_send{
    ["@type"] = 'getChatAdministrators',
    chat_id = chat_id
  }
end
function tdlib_functions.setPinnedChats(chat_ids)
  return client_send{
    ["@type"] = 'setPinnedChats',
    chat_ids = tdlib_functions.vectorize(chat_ids)
  }
end
function tdlib_functions.downloadFile(file_id, priority)
  return client_send{
    ["@type"] = 'downloadFile',
    file_id = file_id,
    priority = priority or 32
  }
end
function tdlib_functions.cancelDownloadFile(file_id, only_if_pending)
  return client_send{
    ["@type"] = 'cancelDownloadFile',
    file_id = file_id,
    only_if_pending = only_if_pending
  }
end
function tdlib_functions.uploadFile(file, file_type, priority)
  local ftype = file_type or 'Unknown'
  return client_send{
    ["@type"] = 'uploadFile',
    file = tdlib_functions.getInputFile(file),
    file_type = {
      ["@type"] = 'fileType' .. ftype
    },
    priority = priority or 32
  }
end
function tdlib_functions.cancelUploadFile(file_id)
  return client_send{
    ["@type"] = 'cancelUploadFile',
    file_id = file_id
  }
end
function tdlib_functions.deleteFile(file_id)
  return client_send{
    ["@type"] = 'deleteFile',
    file_id = file_id
  }
end
function tdlib_functions.generateChatInviteLink(chat_id)
  return client_send{
    ["@type"] = 'generateChatInviteLink',
    chat_id = chat_id
  }
end
function tdlib_functions.joinChatByUsername(username)
  if type(username) == 'string' and 5 <= #username then
    local result = tdlib_functions.searchPublicChat(username)
    if result.type and result.type["@type"] == 'chatTypeSupergroup' then
      return client_send{
        ["@type"] = 'joinChat',
        chat_id = result.id
      }
    else
      return result
    end
  end
end
function tdlib_functions.joinChatByUsername_(username,chat_id)
  if type(username) == 'string' and 5 <= #username then
  return client_send{
    ["@type"] = 'joinChat',
    chat_id = chat_id
  }
end
end
function tdlib_functions.joinChat(chat_id)
  return client_send{
    ["@type"] = 'joinChat',
    chat_id = chat_id
  }
end
function tdlib_functions.checkChatInviteLink(invite_link)
  return client_send{
    ["@type"] = 'checkChatInviteLink',
    invite_link = tostring(invite_link)
  }
end
function tdlib_functions.joinChatByInviteLink(invite_link)
  return client_send{
    ["@type"] = 'joinChatByInviteLink',
    invite_link = tostring(invite_link)
  }
end
function tdlib_functions.leaveChat(chat_id)
  return  client_send{
    ["@type"] = 'leaveChat',
    chat_id = chat_id
  }
end
function tdlib_functions.createCall(user_id, udp_p2p, udp_reflector, min_layer, max_layer)
  return client_send{
    ["@type"] = 'createCall',
    user_id = user_id,
    protocol = {
      ["@type"] = 'callProtocol',
      udp_p2p = udp_p2p,
      udp_reflector = udp_reflector,
      min_layer = min_layer or 65,
      max_layer = max_layer or 65
    }
  }
end
function tdlib_functions.acceptCall(call_id, udp_p2p, udp_reflector, min_layer, max_layer)
  return client_send{
    ["@type"] = 'acceptCall',
    call_id = call_id,
    protocol = {
      ["@type"] = 'callProtocol',
      udp_p2p = udp_p2p,
      udp_reflector = udp_reflector,
      min_layer = min_layer or 65,
      max_layer = max_layer or 65
    }
  }
end
function tdlib_functions.endcall(id)
  return client_send{
    ["@type"] = 'endGroupCall',
    call_id = id}
end
function tdlib_functions.createcallgp(id)
  return client_send{
    ["@type"] = 'createVideoChat',
    chat_id = id,
    protocol = {
    ["@type"] = 'callProtocol',
    udp_p2p = true,
    udp_reflector = true,
    min_layer = 65,
    max_layer = 65
    }
  }
end
function tdlib_functions.setspeak(id,audioSource,isSpeaking)
  return client_send{
    ["@type"] = 'setGroupCallParticipantIsSpeaking',
    group_call_id = id,
    audio_source = audioSource,
    is_speaking = isSpeaking
  }
end
function tdlib_functions.setspeak_()
  return client_send{
    ["@type"] = 'setGroupCallParticipantIsSpeaking'
  }
end
function tdlib_functions.loadGroupCallParticipants(id)
  return client_send{
    ["@type"] = 'loadGroupCallParticipants',
    group_call_id = id}
end
function tdlib_functions.getGroupCall(id)
  return client_send{
    ["@type"] = 'getGroupCall',
    group_call_id = id
  }
end
function tdlib_functions.groupCall(id)
  return client_send{
    ["@type"] = 'creategroupCall',
    chat_id = id
  }
end
function tdlib_functions.joinGroupCall(id)
  return client_send{
    ["@type"] = 'joinGroupCall',
    group_call_id = id,
  }
end
function tdlib_functions.blockUser(user_id)
  return client_send{
    ["@type"] = 'blockUser',
    user_id = user_id
  }
end
function tdlib_functions.unblockUser(user_id)
  return client_send{
    ["@type"] = 'unblockUser',
    user_id = user_id
  }
end
function tdlib_functions.getBlockedUsers(offset, limit)
  return client_send{
    ["@type"] = 'getBlockedUsers',
    offset = offset or 0,
    limit = limit
 }   
end

function tdlib_functions.deleteChat(chat_id)
  return client_send{
    ["@type"] = 'deleteChat',
    chat_id = chat_id
 }
end

function tdlib_functions.getContacts()
  return client_send{
    ["@type"] = 'getContacts'
  }
end
function tdlib_functions.addContact(value, share_phone_number)
  local result = {
      ["@type"] = 'contact',
      phone_number = tostring(value.phone_number),
      first_name = tostring(value.first_name),
      last_name = tostring(value.last_name),
      user_id = value.user_id or 0
    }
  return client_send{
    ["@type"] = 'addContact',
    contact = result,
    share_phone_number = share_phone_number
  }
end
function tdlib_functions.importContacts(contacts)
  local result = {}
  for key, value in pairs(contacts) do
    result[#result + 1] = {
      ["@type"] = 'contact',
      phone_number = tostring(value.phone_number),
      first_name = tostring(value.first_name),
      last_name = tostring(value.last_name),
      user_id = value.user_id or 0
    }
  end
  return client_send{
    ["@type"] = 'importContacts',
    contacts = result
  }
end
function tdlib_functions.importContacts_(phone_number,first_name,last_name,user_id)
return client_send{
    ["@type"] = "importContacts",
contacts = {[1] = {
phone_number = tostring(phone_number),
first_name = tostring(first_name),
last_name = tostring(last_name),
user_id = user_id
    }
  }
  }
end
function tdlib_functions.addContact_(phone_number,first_name,last_name,user_id)
  return client_send{
    ["@type"] = "addContact",
contact = {
phone_number = tostring(phone_number),
first_name = tostring(first_name),
last_name = tostring(last_name),
user_id = user_id
    }
  }
end
function tdlib_functions.searchContacts(query, limit)
  return client_send{
    ["@type"] = 'searchContacts',
    query = tostring(query),
    limit = limit
  }
end
function tdlib_functions.removeContacts(user_ids)
  return client_send{
    ["@type"] = 'removeContacts',
    user_ids = tdlib_functions.vectorize(user_ids)
  }
end
function tdlib_functions.getImportedContactCount()
  return client_send{
    ["@type"] = 'getImportedContactCount'
  }
end
function tdlib_functions.changeImportedContacts(phone_number, first_name, last_name, user_id)
  return client_send{
    ["@type"] = 'changeImportedContacts',
    contacts = {
      ["@type"] = 'contact',
      phone_number = tostring(phone_number),
      first_name = tostring(first_name),
      last_name = tostring(last_name),
      user_id = user_id or 0
    }
  }
end
function tdlib_functions.clearImportedContacts()
  return client_send{
    ["@type"] = 'clearImportedContacts'
  }
end
function tdlib_functions.getUserProfilePhotos(user_id, offset, limit)
  return client_send{
    ["@type"] = 'getUserProfilePhotos',
    user_id = user_id,
    offset = offset or 0,
    limit = tdlib_functions.setLimit(100, limit)
  }
end
function tdlib_functions.getBlockedMessageSenders(offset, limit)
  return client_send{
    ["@type"] = 'getBlockedMessageSenders',
    offset = offset or 0,
    limit = tdlib_functions.setLimit(100, limit)
  }
end
function tdlib_functions.getStickers(emoji, limit)
  return client_send{
    ["@type"] = 'getStickers',
    emoji = tostring(emoji),
    limit = tdlib_functions.setLimit(100, limit)
  }
end
function tdlib_functions.searchStickers(emoji, limit)
  return client_send{
    ["@type"] = 'searchStickers',
    emoji = tostring(emoji),
    limit = limit
  }
end
function tdlib_functions.getArchivedStickerSets(is_masks, offset_sticker_set_id, limit)
  return client_send{
    ["@type"] = 'getArchivedStickerSets',
    is_masks = is_masks,
    offset_sticker_set_id = offset_sticker_set_id,
    limit = limit
  }
end
function tdlib_functions.getTrendingStickerSets()
  return client_send{
    ["@type"] = 'getTrendingStickerSets'
  }
end
function tdlib_functions.getAttachedStickerSets(file_id)
  return client_send{
    ["@type"] = 'getAttachedStickerSets',
    file_id = file_id
  }
end
function tdlib_functions.getStickerSet(set_id)
  return client_send{
    ["@type"] = 'getStickerSet',
    set_id = set_id
  }
end
function tdlib_functions.searchStickerSet(name)
  return client_send{
    ["@type"] = 'searchStickerSet',
    name = tostring(name)
  }
end
function tdlib_functions.searchInstalledStickerSets(is_masks, query, limit)
  return client_send{
    ["@type"] = 'searchInstalledStickerSets',
    is_masks = is_masks,
    query = tostring(query),
    limit = limit
  }
end
function tdlib_functions.searchStickerSets(query)
  return client_send{
    ["@type"] = 'searchStickerSets',
    query = tostring(query)
  }
end
function tdlib_functions.changeStickerSet(set_id, is_installed, is_archived)
  return client_send{
    ["@type"] = 'changeStickerSet',
    set_id = set_id,
    is_installed = is_installed,
    is_archived = is_archived
  }
end
function tdlib_functions.viewTrendingStickerSets(sticker_set_ids)
  return client_send{
    ["@type"] = 'viewTrendingStickerSets',
    sticker_set_ids = tdlib_functions.vectorize(sticker_set_ids)
  }
end
function tdlib_functions.reorderInstalledStickerSets(is_masks, sticker_set_ids)
  return client_send{
    ["@type"] = 'reorderInstalledStickerSets',
    is_masks = is_masks,
    sticker_set_ids = tdlib_functions.vectorize(sticker_set_ids)
  }
end
function tdlib_functions.getRecentStickers(is_attached)
  return client_send{
    ["@type"] = 'getRecentStickers',
    is_attached = is_attached
  }
end
function tdlib_functions.addRecentSticker(is_attached, sticker)
  return client_send{
    ["@type"] = 'addRecentSticker',
    is_attached = is_attached,
    sticker = tdlib_functions.getInputFile(sticker)
  }
end
function tdlib_functions.clearRecentStickers(is_attached)
  return client_send{
    ["@type"] = 'clearRecentStickers',
    is_attached = is_attached
  }
end
function tdlib_functions.getFavoriteStickers()
  return client_send{
    ["@type"] = 'getFavoriteStickers'
  }
end
function tdlib_functions.addFavoriteSticker(sticker)
  return client_send{
    ["@type"] = 'addFavoriteSticker',
    sticker = tdlib_functions.getInputFile(sticker)
  }
end
function tdlib_functions.removeFavoriteSticker(sticker)
  return client_send{
    ["@type"] = 'removeFavoriteSticker',
    sticker = tdlib_functions.getInputFile(sticker)
  }
end
function tdlib_functions.getStickerEmojis(sticker)
  return client_send{
    ["@type"] = 'getStickerEmojis',
    sticker = tdlib_functions.getInputFile(sticker)
  }
end
function tdlib_functions.getSavedAnimations()
  return client_send{
    ["@type"] = 'getSavedAnimations'
  }
end
function tdlib_functions.addSavedAnimation(animation)
  return client_send{
    ["@type"] = 'addSavedAnimation',
    animation = tdlib_functions.getInputFile(animation)
  }
end
function tdlib_functions.removeSavedAnimation(animation)
  return client_send{
    ["@type"] = 'removeSavedAnimation',
    animation = tdlib_functions.getInputFile(animation)
  }
end
function tdlib_functions.getRecentInlineBots()
  return client_send{
    ["@type"] = 'getRecentInlineBots'
  }
end
function tdlib_functions.searchHashtags(prefix, limit)
  return client_send{
    ["@type"] = 'searchHashtags',
    prefix = tostring(prefix),
    limit = limit
  }
end
function tdlib_functions.removeRecentHashtag(hashtag)
  return client_send{
    ["@type"] = 'removeRecentHashtag',
    hashtag = tostring(hashtag)
  }
end
function tdlib_functions.getWebPagePreview(text)
  return client_send{
    ["@type"] = 'getWebPagePreview',
    text = {
      text = text
    }
  }
end
function tdlib_functions.getWebPageInstantView(url, force_full)
  return client_send{
    ["@type"] = 'getWebPageInstantView',
    url = tostring(url),
    force_full = force_full
  }
end
function tdlib_functions.getNotificationSettings(scope, chat_id)
  return client_send{
    ["@type"] = 'getNotificationSettings',
    scope = {
      ["@type"] = 'notificationSettingsScope' .. scope,
      chat_id = chat_id
    }
  }
end
function tdlib_functions.setNotificationSettings(scope, chat_id, mute_for, sound, show_preview)
  return client_send{
    ["@type"] = 'setNotificationSettings',
    scope = {
      ["@type"] = 'notificationSettingsScope' .. scope,
      chat_id = chat_id
    },
    notification_settings = {
      ["@type"] = 'notificationSettings',
      mute_for = mute_for,
      sound = tostring(sound),
      show_preview = show_preview
    }
  }
end
function tdlib_functions.resetAllNotificationSettings()
  return client_send{
    ["@type"] = 'resetAllNotificationSettings'
  }
end
function tdlib_functions.setProfilePhoto(photo, type, main_frame_timestamp)
  return client_send{
    ["@type"] = 'setProfilePhoto',
    photo = tdlib_functions.getInputChatPhoto(photo, type, main_frame_timestamp)
  }
end
function tdlib_functions.deleteProfilePhoto(profile_photo_id)
  return client_send{
    ["@type"] = 'deleteProfilePhoto',
    profile_photo_id = profile_photo_id
  }
end
function tdlib_functions.setName(first_name, last_name)
  return client_send{
    ["@type"] = 'setName',
    first_name = tostring(first_name),
    last_name = tostring(last_name)
  }
end
function tdlib_functions.setBio(bio)
  return client_send{
    ["@type"] = 'setBio',
    bio = tostring(bio)
  }
end
function tdlib_functions.setUsername(username)
  return client_send{
    ["@type"] = 'setUsername',
    username = tostring(username)
  }
end
function tdlib_functions.getActiveSessions()
  return client_send{
    ["@type"] = 'getActiveSessions'
  }
end
function tdlib_functions.terminateAllOtherSessions()
  return client_send{
    ["@type"] = 'terminateAllOtherSessions'
  }
end
function tdlib_functions.terminateSession(session_id)
  return client_send{
    ["@type"] = 'terminateSession',
    session_id = session_id
  }
end
function tdlib_functions.toggleBasicGroupAdministrators(basic_group_id, everyone_is_administrator)
  return client_send{
    ["@type"] = 'toggleBasicGroupAdministrators',
    basic_group_id = tdlib_functions.getChatId(basic_group_id).id,
    everyone_is_administrator = everyone_is_administrator
  }
end
function tdlib_functions.setSupergroupUsername(supergroup_id, username)
  return client_send{
    ["@type"] = 'setSupergroupUsername',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    username = tostring(username)
  }
end
function tdlib_functions.setSupergroupStickerSet(supergroup_id, sticker_set_id)
  return client_send{
    ["@type"] = 'setSupergroupStickerSet',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    sticker_set_id = sticker_set_id
  }
end
function tdlib_functions.toggleSupergroupInvites(supergroup_id, anyone_can_invite)
  return client_send{
    ["@type"] = 'toggleSupergroupInvites',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    anyone_can_invite = anyone_can_invite
  }
end
function tdlib_functions.toggleSupergroupSignMessages(supergroup_id, sign_messages)
  return client_send{
    ["@type"] = 'toggleSupergroupSignMessages',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    sign_messages = sign_messages
  }
end
function tdlib_functions.toggleSupergroupIsAllHistoryAvailable(supergroup_id, is_all_history_available)
  return client_send{
    ["@type"] = 'toggleSupergroupIsAllHistoryAvailable',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    is_all_history_available = is_all_history_available
  }
end
function tdlib_functions.setChatDescription(chat_id, description)
  return client_send{
    ["@type"] = 'setChatDescription',
    chat_id = chat_id,
    description = tostring(description)
  }
end
function tdlib_functions.pinChatMessage(chat_id, message_id, disable_notification)
  return client_send{
    ["@type"] = 'pinChatMessage',
    chat_id = chat_id,
    message_id = message_id,
    disable_notification = disable_notification
  }
end
function tdlib_functions.unpinChatMessage(chat_id, message_id)
  return client_send{
    ["@type"] = 'unpinChatMessage',
    chat_id = chat_id,
    message_id = message_id,
  }
end
function tdlib_functions.unpinAllChatMessages(chat_id)
  return client_send{
    ["@type"] = 'unpinAllChatMessages',
    chat_id = chat_id
  }
end
function tdlib_functions.reportSupergroupSpam(supergroup_id, user_id, message_ids)
  return client_send{
    ["@type"] = 'reportSupergroupSpam',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    user_id = user_id,
    message_ids = tdlib_functions.vectorize(message_ids)
  }
end
function tdlib_functions.getSupergroupMembers(supergroup_id, filter, query, offset, limit)
  local filter = filter or 'Recent'
  return client_send{
    ["@type"] = 'getSupergroupMembers',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id,
    filter = {
      ["@type"] = 'supergroupMembersFilter' .. filter,
      query = query
    },
    offset = offset or 0,
    limit = tdlib_functions.setLimit(200, limit)
  }
end
function tdlib_functions.deleteSupergroup(supergroup_id)
  return client_send{
    ["@type"] = 'deleteSupergroup',
    supergroup_id = tdlib_functions.getChatId(supergroup_id).id
  }
end
function tdlib_functions.closeSecretChat(secret_chat_id)
  return client_send{
    ["@type"] = 'closeSecretChat',
    secret_chat_id = secret_chat_id
  }
end
function tdlib_functions.getChatEventLog(chat_id, query, from_event_id, limit, filters, user_ids)
  local filters = filters or {1,1,1,1,1,1,1,1,1,1}
  return client_send{
    ["@type"] = 'getChatEventLog',
    chat_id = chat_id,
    query = tostring(query) or '',
    from_event_id = from_event_id or 0,
    limit = tdlib_functions.setLimit(100, limit),
    filters = {
      ["@type"] = 'chatEventLogFilters',
      message_edits = filters[0],
      message_deletions = filters[1],
      message_pins = filters[2],
      member_joins = filters[3],
      member_leaves = filters[4],
      member_invites = filters[5],
      member_promotions = filters[6],
      member_restrictions = filters[7],
      info_changes = filters[8],
      setting_changes = filters[9]
    },
    user_ids = tdlib_functions.vectorize(user_ids)
  }
end
function tdlib_functions.getSavedOrderInfo()
  return client_send{
    ["@type"] = 'getSavedOrderInfo'
  }
end
function tdlib_functions.deleteSavedOrderInfo()
  return client_send{
    ["@type"] = 'deleteSavedOrderInfo'
  }
end
function tdlib_functions.deleteSavedCredentials()
  return client_send{
    ["@type"] = 'deleteSavedCredentials'
  }
end
function tdlib_functions.getSupportUser()
  return client_send{
    ["@type"] = 'getSupportUser'
  }
end
function tdlib_functions.getWallpapers()
  return client_send{
    ["@type"] = 'getWallpapers'
  }
end
function tdlib_functions.setUserPrivacySettingRules(setting, rules, chat_ids)
  if not chat_ids and (rules == 'RestrictAll' or rules == 'AllowAll') then
    setting_rules = {
       {
        ["@type"] = 'userPrivacySettingRule' .. rules
       }
     }
  elseif chat_ids and (rules == 'RestrictChatMembers' or rules == 'AllowChatMembers') then
    setting_rules = {
      {
        ["@type"] = 'userPrivacySettingRule' .. rules,
        user_ids = tdlib_functions.vectorize(chat_ids)
      }
    }
  elseif not chat_ids and (rules == 'RestrictContacts' or rules == 'AllowContacts') then
    setting_rules = {
        {
         ["@type"] = 'userPrivacySettingRule' .. rules,
        }
      }
  elseif chat_ids and (rules == 'RestrictUsers' or rules == 'AllowUsers') then
    setting_rules = {
      {
        ["@type"] = 'userPrivacySettingRule' .. rules,
        user_ids = tdlib_functions.vectorize(chat_ids)
      }
    }
  end
  return client_send{
    ["@type"] = 'setUserPrivacySettingRules',
    setting = {
      ["@type"] = 'userPrivacySetting' .. setting,
    },
    rules = {
        ["@type"] = 'userPrivacySettingRules',
          rules = setting_rules
     }
  }
end
function tdlib_functions.getUserPrivacySettingRules(setting)
  return client_send{
    ["@type"] = 'getUserPrivacySettingRules',
    setting = {
      ["@type"] = 'userPrivacySetting' .. setting
    }
  }
end
function tdlib_functions.getOption(name)
  return client_send{
    ["@type"] = 'getOption',
    name = tostring(name)
  }
end
function tdlib_functions.setAlarm(seconds)
return client_send{
    ["@type"] = 'setAlarm',
    seconds = seconds
}
end
function tdlib_functions.setOption(name, option_value, value)
  return client_send{
    ["@type"] = 'setOption',
    name = tostring(name),
    value = {
      ["@type"] = 'optionValue' .. option_value,
      value = value
    }
  }
end
function tdlib_functions.setAccountTtl(ttl)
  return client_send{
    ["@type"] = 'setAccountTtl',
    ttl = {
      ["@type"] = 'accountTtl',
      days = ttl
    }
  }
end
function tdlib_functions.getAccountTtl()
  return client_send{
    ["@type"] = 'getAccountTtl'
  }
end
function tdlib_functions.deleteAccount(reason)
  return client_send{
    ["@type"] = 'deleteAccount',
    reason = tostring(reason)
  }
end
function tdlib_functions.getChatReportSpamState(chat_id)
  return client_send{
    ["@type"] = 'getChatReportSpamState',
    chat_id = chat_id
  }
end
function tdlib_functions.reportChat(chat_id, reason, text, message_ids)
  return client_send{
    ["@type"] = 'reportChat',
    chat_id = chat_id,
    reason = {
      ["@type"] = 'chatReportReason' .. reason,
      text = text
    },
    message_ids = tdlib_functions.vectorize(message_ids)
  }
end
function tdlib_functions.getStorageStatistics(chat_limit)
  return client_send{
    ["@type"] = 'getStorageStatistics',
    chat_limit = chat_limit
  }
end
function tdlib_functions.getStorageStatisticsFast()
  return client_send{
    ["@type"] = 'getStorageStatisticsFast'
  }
end
function tdlib_functions.optimizeStorage(size, ttl, count, immunity_delay, file_type, chat_ids, exclude_chat_ids, chat_limit)
  local file_type = file_type or ''
  return client_send{
    ["@type"] = 'optimizeStorage',
    size = size or -1,
    ttl = ttl or -1,
    count = count or -1,
    immunity_delay = immunity_delay or -1,
    file_type = {
      ["@type"] = 'fileType' .. file_type
    },
    chat_ids = tdlib_functions.vectorize(chat_ids),
    exclude_chat_ids = tdlib_functions.vectorize(exclude_chat_ids),
    chat_limit = chat_limit
  }
end
function tdlib_functions.setNetworkType(type)
  return client_send{
    ["@type"] = 'setNetworkType',
    type = {
      ["@type"] = 'networkType' .. type
    },
  }
end
function tdlib_functions.getNetworkStatistics(only_current)
  return client_send{
    ["@type"] = 'getNetworkStatistics',
    only_current = only_current
  }
end
function tdlib_functions.addNetworkStatistics(entry, file_type, network_type, sent_bytes, received_bytes, duration)
  local file_type = file_type or 'None'
  return client_send{
    ["@type"] = 'addNetworkStatistics',
    entry = {
      ["@type"] = 'networkStatisticsEntry' .. entry,
      file_type = {
        ["@type"] = 'fileType' .. file_type
      },
      network_type = {
        ["@type"] = 'networkType' .. network_type
      },
      sent_bytes = sent_bytes,
      received_bytes = received_bytes,
      duration = duration
    }
  }
end
function tdlib_functions.resetNetworkStatistics()
  return client_send{
    ["@type"] = 'resetNetworkStatistics'
  }
end
function tdlib_functions.getCountryCode()
  return client_send{
    ["@type"] = 'getCountryCode'
  }
end
function tdlib_functions.getInviteText()
  return client_send{
    ["@type"] = 'getInviteText'
  }
end
function tdlib_functions.getTermsOfService()
  return client_send{
    ["@type"] = 'getTermsOfService'
  }
end
function tdlib_functions.sendText(chat_id, reply_to_message_id, text, parse_mode, disable_web_page_preview, clear_draft, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'Typing',100)
  local input_message_content = {
    ["@type"] = 'inputMessageText',
    disable_web_page_preview = disable_web_page_preview,
    text = {text = text},
    clear_draft = clear_draft
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendAnimation(chat_id, reply_to_message_id, animation, caption, parse_mode, duration, width, height, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageAnimation',
    animation = tdlib_functions.getInputFile(animation),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    caption = {text = caption},
    duration = duration,
    width = width,
    height = height
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendAudio(chat_id, reply_to_message_id, audio, caption, parse_mode, duration, title, performer, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'RecordingVoiceNote',100)
tdlib_functions.sendChatAction(chat_id,'UploadingVoiceNote',100)
  local input_message_content = {
    ["@type"] = 'inputMessageAudio',
    audio = tdlib_functions.getInputFile(audio),
    album_cover_thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    caption = {text = caption},
    duration = duration,
    title = tostring(title),
    performer = tostring(performer)
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendDocument(chat_id, reply_to_message_id, document, caption, parse_mode, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'UploadingDocument',100)
  local input_message_content = {
    ["@type"] = 'inputMessageDocument',
    document = tdlib_functions.getInputFile(document),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    caption = {text = caption}
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendPhoto(chat_id, reply_to_message_id, photo, caption, parse_mode, added_sticker_file_ids, width, height, ttl, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessagePhoto',
    photo = tdlib_functions.getInputFile(photo),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    caption = {text = caption},
    added_sticker_file_ids = tdlib_functions.vectorize(added_sticker_file_ids),
    width = width,
    height = height,
    ttl = ttl or 0
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendSticker(chat_id, reply_to_message_id, sticker, width, height, disable_notification, thumbnail, thumb_width, thumb_height, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageSticker',
    sticker = tdlib_functions.getInputFile(sticker),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    width = width,
    height = height
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendVideo(chat_id, reply_to_message_id, video, caption, parse_mode, added_sticker_file_ids, duration, width, height, ttl, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'UploadingVideo',100)
  local input_message_content = {
    ["@type"] = 'inputMessageVideo',
    video = tdlib_functions.getInputFile(video),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    caption = {text = caption},
    added_sticker_file_ids = tdlib_functions.vectorize(added_sticker_file_ids),
    duration = duration,
    width = width,
    height = height,
    ttl = ttl
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendVideoNote(chat_id, reply_to_message_id, video_note, duration, length, thumbnail, thumb_width, thumb_height, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'RecordingVideoNote',100)
tdlib_functions.sendChatAction(chat_id,'UploadingVideoNote',100)
  local input_message_content = {
    ["@type"] = 'inputMessageVideoNote',
    video_note = tdlib_functions.getInputFile(video_note),
    thumbnail = {
      ["@type"] = 'inputThumbnail',
      thumbnail = tdlib_functions.getInputFile(thumbnail),
      width = thumb_width,
      height = thumb_height
    },
    duration = duration,
    length = length
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendVoiceNote(chat_id, reply_to_message_id, voice_note, caption, parse_mode, duration, waveform, disable_notification, from_background, reply_markup)
tdlib_functions.sendChatAction(chat_id,'RecordingVoiceNote',100)
tdlib_functions.sendChatAction(chat_id,'UploadingVoiceNote',100)
  local input_message_content = {
    ["@type"] = 'inputMessageVoiceNote',
    voice_note = tdlib_functions.getInputFile(voice_note),
    caption = {text = caption},
    duration = duration,
    waveform = waveform
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendLocation(chat_id, reply_to_message_id, latitude, longitude, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageLocation',
    location = {
      ["@type"] = 'location',
      latitude = latitude,
      longitude = longitude
    },
    live_period = liveperiod
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendVenue(chat_id, reply_to_message_id, latitude, longitude, title, address, provider, id, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageVenue',
    venue = {
      ["@type"] = 'venue',
      location = {
        ["@type"] = 'location',
        latitude = latitude,
        longitude = longitude
      },
      title = tostring(title),
      address = tostring(address),
      provider = tostring(provider),
      id = tostring(id)
    }
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendContact(chat_id, reply_to_message_id, phone_number, first_name, last_name, user_id, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageContact',
    contact = {
      ["@type"] = 'contact',
      phone_number = tostring(phone_number),
      first_name = tostring(first_name),
      last_name = tostring(last_name),
      user_id = user_id
    }
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendInvoice(chat_id, reply_to_message_id, invoice, title, description, photo_url, photo_size, photo_width, photo_height, payload, provider_token, provider_data, start_parameter, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageInvoice',
    invoice = invoice,
    title = tostring(title),
    description = tostring(description),
    photo_url = tostring(photo_url),
    photo_size = photo_size,
    photo_width = photo_width,
    photo_height = photo_height,
    payload = payload,
    provider_token = tostring(provider_token),
    provider_data = tostring(provider_data),
    start_parameter = tostring(start_parameter)
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendForwarded(chat_id, reply_to_message_id, from_chat_id, message_id, in_game_share, disable_notification, from_background, reply_markup)
  local input_message_content = {
    ["@type"] = 'inputMessageForwarded',
    from_chat_id = from_chat_id,
    message_id = message_id,
    in_game_share = in_game_share
  }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, nil, disable_notification, from_background, reply_markup)
end
function tdlib_functions.sendPoll(chat_id, reply_to_message_id, question, options, pollType, is_anonymous, allow_multiple_answers)
  local input_message_content = {
      ["@type"] = 'inputMessagePoll',
      is_anonymous = is_anonymous,
      question = question,
      type = {
        ["@type"] = 'pollType'..pollType,
        allow_multiple_answers = allow_multiple_answers
      },
      options = options
    }
  return tdlib_functions.sendMessage(chat_id, reply_to_message_id, input_message_content, parse_mode, disable_notification, from_background, reply_markup)
end
function tdlib_functions.getPollVoters(chat_id, message_id, option_id, offset, limit)
  return client_send{
    ["@type"] = 'getPollVoters',
    chat_id = chat_id,
    message_id = message_id,
    option_id = option_id,
    limit = tdlib_functions.setLimit(50 , limit),
    offset = offset
  }
end
function tdlib_functions.setPollAnswer(chat_id, message_id, option_ids)
  return client_send{
    ["@type"] = 'setPollAnswer',
    chat_id = chat_id,
    message_id = message_id,
    option_ids = option_ids
  }
end
function tdlib_functions.destroy()
  return client_send{
    ["@type"] = 'destroy'
  }
end
function tdlib_functions.stopPoll(chat_id, message_id, reply_markup)
  return client_send{
    ["@type"] = 'stopPoll',
    chat_id = chat_id,
    message_id = message_id,
    reply_markup = reply_markup
  }
end
function tdlib_functions.getInputMessage(value)
  if type(value) ~= 'table' then
    return value
  end
  if type(value.type) == 'string' then
    if value.parse_mode and value.caption then
      caption = tdlib_functions.parseTextEntities(value.caption, value.parse_mode)
    elseif value.caption and not value.parse_mode then
      caption = {
        text = value.caption
      }
    elseif value.parse_mode and value.text then
      text = tdlib_functions.parseTextEntities(value.text, value.parse_mode)
    elseif not value.parse_mode and value.text then
      text = {
        text = value.text
      }
    end
    value.type = string.lower(value.type)
    if value.type == 'text' then
      return {
        ["@type"] = 'inputMessageText',
        disable_web_page_preview = value.disable_web_page_preview,
        text = text,
        clear_draft = value.clear_draft
      }
    elseif value.type == 'animation' then
      return {
        ["@type"] = 'inputMessageAnimation',
        animation = tdlib_functions.getInputFile(value.animation),
        thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        caption = caption,
        duration = value.duration,
        width = value.width,
        height = value.height
      }
    elseif value.type == 'audio' then
      return {
        ["@type"] = 'inputMessageAudio',
        audio = tdlib_functions.getInputFile(value.audio),
        album_cover_thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        caption = caption,
        duration = value.duration,
        title = tostring(value.title),
        performer = tostring(value.performer)
      }
    elseif value.type == 'document' then
      return {
        ["@type"] = 'inputMessageDocument',
        document = tdlib_functions.getInputFile(value.document),
        thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        caption = caption
      }
    elseif value.type == 'photo' then
      return {
        ["@type"] = 'inputMessagePhoto',
        photo = tdlib_functions.getInputFile(value.photo),
        thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        caption = caption,
        added_sticker_file_ids = tdlib_functions.vectorize(value.added_sticker_file_ids),
        width = value.width,
        height = value.height,
        ttl = value.ttl or 0
      }
    elseif value.text == 'video' then
      return {
        ["@type"] = 'inputMessageVideo',
        video = tdlib_functions.getInputFile(value.video),
        thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        caption = caption,
        added_sticker_file_ids = tdlib_functions.vectorize(value.added_sticker_file_ids),
        duration = value.duration,
        width = value.width,
        height = value.height,
        ttl = value.ttl or 0
      }
    elseif value.text == 'videonote' then
      return {
        ["@type"] = 'inputMessageVideoNote',
        video_note = tdlib_functions.getInputFile(value.video_note),
        thumbnail = {
          ["@type"] = 'inputThumbnail',
          thumbnail = tdlib_functions.getInputFile(value.thumbnail),
          width = value.thumb_width,
          height = value.thumb_height
        },
        duration = value.duration,
        length = value.length
      }
    elseif value.text == 'voice' then
      return {
        ["@type"] = 'inputMessageVoiceNote',
        voice_note = tdlib_functions.getInputFile(value.voice_note),
        caption = caption,
        duration = value.duration,
        waveform = value.waveform
      }
    elseif value.text == 'location' then
      return {
        ["@type"] = 'inputMessageLocation',
        location = {
          ["@type"] = 'location',
          latitude = value.latitude,
          longitude = value.longitude
        },
        live_period = value.liveperiod
      }
    elseif value.text == 'contact' then
      return {
        ["@type"] = 'inputMessageContact',
        contact = {
          ["@type"] = 'contact',
          phone_number = tostring(value.phone_number),
          first_name = tostring(value.first_name),
          last_name = tostring(value.last_name),
          user_id = value.user_id
        }
      }
    elseif value.text == 'contact' then
      return {
        ["@type"] = 'inputMessageContact',
        contact = {
          ["@type"] = 'contact',
          phone_number = tostring(value.phone_number),
          first_name = tostring(value.first_name),
          last_name = tostring(value.last_name),
          user_id = value.user_id
        }
      }
    end
  end
end
function tdlib_functions.editInlineMessageText(inline_message_id, input_message_content, reply_markup)
  return client_send{
    ["@type"] = 'editInlineMessageText',
    inline_message_id = inline_message_id,
    input_message_content = tdlib_functions.getInputMessage(input_message_content),
    reply_markup = reply_markup
  }
end
function tdlib_functions.answerInlineQuery(inline_query_id, results, next_offset, switch_pm_text, switch_pm_parameter, is_personal, cache_time)
  local answerInlineQueryResults = {}
  for key, value in pairs(results) do
    local newAnswerInlineQueryResults_id = #answerInlineQueryResults + 1
    if type(value) == 'table' and type(value.type) == 'string' then
      value.type = string.lower(value.type)
      if value.type == 'gif' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultAnimatedGif',
          id = value.id,
          title = value.title,
          thumbnail_url = value.thumbnail_url,
          gif_url = value.gif_url,
          gif_duration = value.gif_duration,
          gif_width = value.gif_width,
          gif_height = value.gif_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'mpeg4' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultAnimatedMpeg4',
          id = value.id,
          title = value.title,
          thumbnail_url = value.thumbnail_url,
          mpeg4_url = value.mpeg4_url,
          mpeg4_duration = value.mpeg4_duration,
          mpeg4_width = value.mpeg4_width,
          mpeg4_height = value.mpeg4_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'article' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultArticle',
          id = value.id,
          url = value.url,
          hide_url = value.hide_url,
          title = value.title,
          description = value.description,
          thumbnail_url = value.thumbnail_url,
          thumbnail_width = value.thumbnail_width,
          thumbnail_height = value.thumbnail_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'audio' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultAudio',
          id = value.id,
          title = value.title,
          performer = value.performer,
          audio_url = value.audio_url,
          audio_duration = value.audio_duration,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'contact' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultContact',
          id = value.id,
          contact = value.contact,
          thumbnail_url = value.thumbnail_url,
          thumbnail_width = value.thumbnail_width,
          thumbnail_height = thumbnail_height.description,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'document' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultDocument',
          id = value.id,
          title = value.title,
          description = value.description,
          document_url = value.document_url,
          mime_type = value.mime_type,
          thumbnail_url = value.thumbnail_url,
          thumbnail_width = value.thumbnail_width,
          thumbnail_height = value.thumbnail_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'game' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultGame',
          id = value.id,
          game_short_name = value.game_short_name,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'location' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultLocation',
          id = value.id,
          location = value.location,
          live_period = value.live_period,
          title = value.title,
          thumbnail_url = value.thumbnail_url,
          thumbnail_width = value.thumbnail_width,
          thumbnail_height = value.thumbnail_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'photo' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultPhoto',
          id = value.id,
          title = value.title,
          description = value.description,
          thumbnail_url = value.thumbnail_url,
          photo_url = value.photo_url,
          photo_width = value.photo_width,
          photo_height = value.photo_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'sticker' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultSticker',
          id = value.id,
          thumbnail_url = value.thumbnail_url,
          sticker_url = value.sticker_url,
          sticker_width = value.sticker_width,
          sticker_height = value.sticker_height,
          photo_width = value.photo_width,
          photo_height = value.photo_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'sticker' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultSticker',
          id = value.id,
          thumbnail_url = value.thumbnail_url,
          sticker_url = value.sticker_url,
          sticker_width = value.sticker_width,
          sticker_height = value.sticker_height,
          photo_width = value.photo_width,
          photo_height = value.photo_height,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'video' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultVideo',
          id = value.id,
          title = value.title,
          description = value.description,
          thumbnail_url = value.thumbnail_url,
          video_url = value.video_url,
          mime_type = value.mime_type,
          video_width = value.video_width,
          video_height = value.video_height,
          video_duration = value.video_duration,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      elseif value.type == 'videonote' then
        answerInlineQueryResults[newAnswerInlineQueryResults_id] = {
          ["@type"] = 'inputInlineQueryResultVoiceNote',
          id = value.id,
          title = value.title,
          voice_note_url = value.voice_note_url,
          voice_note_duration = value.voice_note_duration,
          reply_markup = tdlib_functions.replyMarkup{
            type = 'inline',
            data = value.reply_markup
          },
          input_message_content = tdlib_functions.getInputMessage(value.input)
        }
      end
    end
  end
  return client_send{
    ["@type"] = 'answerInlineQuery',
    inline_query_id = inline_query_id,
    next_offset = next_offset,
    switch_pm_text = switch_pm_text,
    switch_pm_parameter = switch_pm_parameter,
    is_personal = is_personal,
    cache_time = cache_time,
    results = answerInlineQueryResults,
  }
end

function set_config(data)
  if not data.api_hash then
    print(tdlib_functions.colors('%{red}please use api_hash in your script !'))
    os.exit()
  end
  if not data.api_id then
    print(tdlib_functions.colors('%{red}please use api_id in your script !'))
    os.exit()
  end
  if not data.session_name then
    print(tdlib_functions.colors('%{red}please use session_name in your script !'))
    os.exit()
  end
  if not data.token and not data.phone and not tdlib_functions.exists('./CallBack-Bot/'..data.session_name) then
    io.write(tdlib_functions.colors('\n%{green}please enter token or phone number :'))
    local phone_token = io.read()
    if phone_token:match('%d+:') then
      data_cache.config.is_bot = true
      data_cache.config.token = phone_token
    else
      data_cache.config.is_bot = false
      data_cache.config.phone = phone_token
    end
  elseif data.token and not tdlib_functions.exists('./CallBack-Bot/'..data.session_name) then
    data_cache.config.is_bot = true
    data_cache.config.token = data.token
   elseif data.phone and not tdlib_functions.exists('./CallBack-Bot/'..data.session_name) then
    data_cache.config.is_bot = false
    data_cache.config.phone = data.phone
  end
  if not tdlib_functions.exists('./CallBack-Bot') then
    os.execute('mkdir ./CallBack-Bot')
  end
  data_cache.config.encryption_key = data.encryption_key or ''
  data_cache.config.api_id = data.api_id
  data_cache.config.api_hash = data.api_hash
  data_cache.config.use_message_database = data.use_message_database or true
  data_cache.config.use_secret_chats = use_secret_chats or true
  data_cache.config.system_language_code = data.language_code or 'en'
  data_cache.config.device_model = data.device_model or 'tdlua'
  data_cache.config.system_version = data.system_version or 'linux'
  data_cache.config.application_version = data.app_version or '1.0'
  data_cache.config.enable_storage_optimizer = data.enable_storage_optimizer or true
  data_cache.config.use_pfs = data.use_pfs or true
  data_cache.config.database_directory = './CallBack-Bot/'..data.session_name
  data_cache.config.database_encryption_key = database_encryption_key or ''
return tdlib_functions
end

function login(state)
  if state.name == 'version' and state.value and state.value.value then
    print(tdlib_functions.colors('%{magenta}TDLIB VERSION > '..state.value.value))
  elseif state["@type"] == 'error' and (state.message == 'PHONE_NUMBER_INVALID' or state.message == 'ACCESS_TOKEN_INVALID') then
    if state.message == 'PHONE_NUMBER_INVALID' then
      print(tdlib_functions.colors('%{red} phone number invalid !'))
    else
      print(colors('%{red} access token invalid !'))
    end
    io.write(tdlib_functions.colors('\n%{green} please enter your token or phone number > '))
    local phone_token = io.read()
    if phone_token:match('%d+:') then
      client:send({
        ["@type"] = 'checkAuthenticationBotToken',
        token = phone_token
      })
    else
      client:send({
        ["@type"] = 'setAuthenticationPhoneNumber',
        phone_number = phone_token
      })
    end
elseif state["@type"] == 'error' and state.code == 8 then
    io.write(tdlib_functions.colors('\n%{green} please enter your token or phone number : '))
    local phone_token = io.read()
    if phone_token:match('%d+:') then
      client:send({
        ["@type"] = 'checkAuthenticationBotToken',
        token = phone_token
      })
    else
      client:send({
        ["@type"] = 'setAuthenticationPhoneNumber',
        phone_number = phone_token
      })
    end
  elseif state["@type"] == 'error' and state.message == 'PHONE_CODE_INVALID' then
    io.write(tdlib_functions.colors('\n%{green} the code is incorrect, please re-enter code : '))
    local code = io.read()
    client:send({
      ["@type"] = 'checkAuthenticationCode',
      code = code
    })
  elseif state["@type"] == 'error' and state.message == 'PASSWORD_HASH_INVALID' then
    io.write(tdlib_functions.colors('\n%{green} the password is incorrect, please re-enter password : : '))
    local password = io.read()
    client:send({
      ["@type"] = 'checkAuthenticationPassword',
      password = password
    })
  elseif state["@type"] == 'authorizationStateWaitTdlibParameters' or (state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitTdlibParameters') then
    client:send({
      ["@type"] = 'setTdlibParameters',
      api_id = data_cache.config.api_id,
      api_hash = data_cache.config.api_hash,
      use_message_database = data_cache.config.use_message_database,
      use_secret_chats = data_cache.config.use_secret_chats,
      system_language_code = data_cache.config.system_language_code,
      device_model = data_cache.config.device_model,
      system_version = data_cache.config.system_version,
      application_version = data_cache.config.application_version,
      enable_storage_optimizer = data_cache.config.enable_storage_optimizer,
      use_pfs = data_cache.config.use_pfs,
      database_directory = data_cache.config.database_directory,
      database_encryption_key = data_cache.config.database_encryption_key
    })       
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitEncryptionKey' then
    client:send({
      ["@type"] = 'checkDatabaseEncryptionKey',
      encryption_key = data_cache.config.encryption_key
    })
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitPhoneNumber' then
    if data_cache.config.is_bot then
      client:send({
        ["@type"] = 'checkAuthenticationBotToken',
        token = data_cache.config.token
      })
    else
      client:send({
        ["@type"] = 'setAuthenticationPhoneNumber',
        phone_number = data_cache.config.phone
      })
    end
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitCode' then
    io.write(tdlib_functions.colors('\n%{green} code : '))
    local code = io.read()
    client:send({
      ["@type"] = 'checkAuthenticationCode',
      code = code
    })
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitPassword' then
      io.write(tdlib_functions.colors('\n%{green} password [ '..state.authorization_state.password_hint..' ] : '))
      local password = io.read()
      client:send({
        ["@type"] = 'checkAuthenticationPassword',
        password = password
      })
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateWaitRegistration' then
    io.write(tdlib_functions.colors('\n%{green} first name : '))
    local first_name = io.read()
    io.write(tdlib_functions.colors('\n%{green} last name : '))
    local last_name = io.read()
    client:send({
      ["@type"] = 'registerUser',
      first_name = first_name,
      last_name = last_name
    })
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateReady' then
    print(tdlib_functions.colors("%{green}>> login successfully <<"))
    print(tdlib_functions.colors('%{green}>> Account Info :\n\n• Name: '..tdlib_functions.getMe().first_name..'\n• User iD: '..tdlib_functions.getMe().id..'\n• Phone Number: +'..tdlib_functions.getMe().phone_number))
  elseif state.authorization_state and state.authorization_state["@type"] == 'authorizationStateClosed' then
    print(tdlib_functions.colors('%{red}>> authorization state closed '))
    os.execute('rm -rf '..data_cache.config.parameters.database_directory..'')
    os.exit()
  elseif state["@type"] == 'error' and state.message == "Phone number can't be empty" then
	os.execute('rm -rf '..data_cache.config.parameters.database_directory..'')
	os.exit()
  elseif state["@type"] == 'error' and state.code == 429 then
    print(tdlib_functions.colors("%{green}\n\n"..state.message))
    os.execute('rm -rf '..data_cache.config.parameters.database_directory..'')
	os.exit()
  elseif not (state.authorization_state and tdlib_functions.in_array({'updateConnectionState', 'updateSelectedBackground', 'updateConnectionState', 'updateOption', 'ok'}, state.authorization_state)) then
    return true
  end
end
local function get_functions()
  return tdlib_functions
end
local function run(main_function)
local get_update = {}
	while true do
		get_update[1] = client:receive(2) or nil
		if type(get_update[1]) == 'table' then
			if login(get_update[1]) then
				if main_function[get_update[1]['@type']] then
					xpcall(main_function[get_update[1]['@type']], print_error, get_update[1])
				end
			end
		end
		for timer_id, timer_data in pairs(data_timer) do
			if os.time() >= timer_data.run_in then
				xpcall(timer_data.def, print_error,timer_data.argv)
				table.remove(data_timer,timer_id)
			end
		end
	end
end
return {
run = run,
set_config = set_config,
get_functions = get_functions
}