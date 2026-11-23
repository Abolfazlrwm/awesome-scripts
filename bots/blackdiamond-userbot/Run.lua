#!/usr/bin/env lua5.3
-- ==============================
-- 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
-- Use, edit and redistribute only with attribution to @Abolfazl_rwm.
-- ==============================
local redis = dofile('./libs/redis.lua')
--local redis = redis.connect('127.0.0.1', 6379)
local Matches = arg[1]
if not Matches then
	return print('\27[31m➥ Script Can Not Be Executed Without Value !\27[0m')
end
function getConfig(bot_name)
local List = {(redis:get(bot_name .. "api_id") or ''), (redis:get(bot_name .. "api_hash") or ''), (redis:get(bot_name .. "sudo_id") or ''), (redis:get(bot_name .. "Info:channel_username") or '')}
	for k,v in pairs(List) do
		if tostring(v) == '' then
			return false
		end
	end
return true
end
function CheckStatus(input, count)
local result = io.popen('ls $PWD/.TD'):read('*all')
	if result == '' then
		return false
	else
		for v in string.gmatch(result, input..'-(%d+)') do
			if tonumber(v) == tonumber(count) then
				return true
			end
		end
	end
return false
end
function getBots()
local result = io.popen('ls $PWD/.TD'):read('*all')
	local Listed = {}
		if result == '' then
			return Listed
		else
			for v in string.gmatch(result, '-(%d+)') do
				table.insert(Listed, tonumber(v))
			end
		end
	table.sort(Listed)
return Listed
end

function getStatus(count)
local result = io.popen('ls $PWD/.TD'):read('*all')
  local input, Listed = false, {}
    if result == '' then
      return input,Listed
    else
      for v in string.gmatch(result, '(%d+)') do
        if tonumber(v) == tonumber(count) then
          input = true
        end
        table.insert(Listed, v)
      end
    end
  return input,Listed
end
--================ Start arg ================--
if arg[1] == '+' then
local result,List = getStatus(0)
if #List == 0 then
    print('\27[35m >> No Bot Have Been Created ! \27[0m')
	else
    for k,v in pairs(List) do
      if not getStatus(v) then
        print('\27[31m >> The Bot ('..v..') is Not Logged in Yet ! \27[0m')
      else
      os.execute('./launch ONALL '..v)
    end
  end
end
end


--[[
if Matches == '+' then
local ListBot = getBots()
	if #ListBot == 0 then
		print('\27[35m➥ No Bots Have Been Login !\27[0m')
	else
		for k,v in pairs(ListBot) do
			if not CheckStatus('Bot', v) then
				print('\27[31m➥ The Bot ('..v..') is Not Logged in Yet !\27[0m')
			elseif not CheckStatus('Cli', v) then
				print('\27[31m➥ The Cleaner ('..v..') is Not Logged in Yet !\27[0m')
			elseif not getConfig(v) then
				print('\27[31m➥ The Bot ('..v..') Config is invalid !\27[0m')
			else
				os.execute('./launch ONALL '..v)
			end
		end
	end
	]]

if Matches == '-' then
local ListBot = getBots()
	if #ListBot == 0 then
		print('\27[35m➥ No Bots Have Been Login !\27[0m')
	else
		for k,v in pairs(ListBot) do
			os.execute('./launch OFFALL '..v)
		end
	end
end