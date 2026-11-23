-- ==============================
-- 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
-- Use, edit and redistribute only with attribution to @Abolfazl_rwm.
-- ==============================
function jdate(str,unix)
local unix = unix or os.time()
local year,mont,day = 1349,1,1
local config = {
  mont = {
    'فروردین',
    'اردیبهشت',
    'خرداد',
    'تیر',
    'مرداد',
    'شهریور',
    'مهر',
    'آبان',
    'آذر',
    'دی',
    'بهمن',
    'اسفند'
  },
  days = {
    [0] = 'شنبه',
    'یکشنبه',
    'دوشنبه',
    'سه شنبه',
    'چهارشنبه',
    'پنجشنبه',
    'جمعه'
  },
  seasons = {
    'بهار',
    'تابستان',
    'پاییز',
    'زمستان'
  },
  zekr = {
  [0] = 'یا رَبَّ الْعالَمین',
    'یا ذَالْجَلالِ وَالْاِکْرام',
    'یا قاضِیَ الْحاجات',
    'یا اَرْحَمَ الرّاحِمین',
    'یا حَیُّ یا قَیّوم',
    'لا اِلهَ اِلّا اللهُ الْمَلِکُ الْحَقُّ الْمُبین',
    'اَللّهُمَّ صَلِّ عَلی مُحَمَّدٍ وَ آلِ مُحَمَّدٍ وَ عَجِّلْ فَرَجَهُمْ'
  },
  year_name = {
    [0] = 'مار',
    'اسب',
    'بز',
    'میمون',
    'خروس',
    'سگ',
    'خوک',
    'موش',
    'گاو',
    'ببر',
    'گربه',
    'اژدها',
  }
}
unix = unix - 6825600 + 12600
local days = math.floor(unix/86400)
unix = unix % 86400
local hours = math.floor(unix/3600)
unix = unix % 3600
local mins = math.floor(unix/60)
secs = math.floor(unix % 60)
local day_name = config.days[days%7]
local zekr = config.zekr[days%7]
function in_table(table,input)
  for k,v in pairs(table) do
    if v == input then
      return true
    end
  end
  return false
end
while 0 > days do
  days = days + 365
  year = year - 1
  if in_table({1,5,9,13,17,22,26,30},(year%33)) then
    days = days + 1
  end
end
while 365 < days do
  days = days - 365
  if in_table({1,5,9,13,17,22,26,30},(year%33)) then
    days = days - 1
  end
  year = year + 1
end
if not in_table({1,5,9,13,17,22,26,30},(year%33)) and days == 365 then
  year = year + 1
  days = 0
end
for k, v in pairs({ 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30}) do
  if days >= v then
    days = days - v
    mont = mont + 1
  else
    day = day + days
    days = 0
  end
end
if (mont < 7) then
  hours = hours + 1
end
if hours > 23 then
  hours = 0
  day = day + 1
end
if day > 31 then
  if month == 6 then
    hours = 23
    day = 31
  else
    day = 1
    mont = mont + 1
  end
end
if str then
  value = string.gsub(str,'%%','#')
  value = string.gsub(value,'#Y',year)
  value = string.gsub(value,'#M',mont)
  value = string.gsub(value,'#D',day)
  value = string.gsub(value,'#h',hours)
  value = string.gsub(value,'#m',mins)
  value = string.gsub(value,'#s',secs)
  value = string.gsub(value,'#X',config.mont[mont])
  value = string.gsub(value,'#x',day_name)
  value = string.gsub(value,'#F',config.seasons[math.floor((mont-1)/3)+1])
  value = string.gsub(value,'#y',config.year_name[year%12])
  value = string.gsub(value,'#z',zekr)
else
  value = [[
  -- #Y  --> سال
  -- #M  --> ماه
  -- #D  --> روز
  -- #h  --> ساعت
  -- #m  --> دقیقه
  -- #s  --> ثانیه
  -- #x  --> نام روز
  -- #X  --> نام ماه
  -- #F  --> نام فصل
  -- #y  --> حیوان سال
  -- #z  --> ذکر

  -- مثال :

    local jdate = require 'jdate'
    local parameters = {'Y','M','D','h','m','s','x','X','F','z'}
    for k,v in pairs(parameters) do
      print(jdate('خروجی تابع : #'..v)) -- or print(jdate(v,unixtime))
    end
  ]]
end
return value
end
return jdate

