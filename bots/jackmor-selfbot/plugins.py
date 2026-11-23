# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================

from requests import get,post
from os import name
from threading import Thread
from pytz import timezone
from datetime import date,datetime
import json 
from os import name
import os
from random import choice
from bs4 import BeautifulSoup
from re import findall
from PIL import Image, ImageDraw, ImageFont
from urllib.request import Request, urlopen
from urllib.parse import quote
from time import time
from rextester_Api import Rextester
import jdatetime
import pyPrivnote as pn
import shutil
import pytz
# @Sourrce_kade
org = [":","0","1","2","3","4","5","6","7","8","9"]
fonts = [[":","𝟶","𝟷","𝟸","𝟹","𝟺","𝟻","𝟼","𝟽","𝟾","𝟿"],
[":","⓪","①","②","③","④","⑤","⑥","⑦","⑧","⑨"],
[":","𝟬","𝟭","𝟮","𝟯","𝟰","𝟱","𝟲","𝟳","𝟴","𝟵"],
[":","０","１","２","３","４","５","６","７","８","９"],
[":","₀","₁","₂","₃","₄","₅","₆","₇","₈","₉"],
[":","𝟎","𝟏","𝟐","𝟑","𝟒","𝟓","𝟔","𝟕","𝟖","𝟗"]]
fonts2 = [[':','⁰','¹','²','³','⁴','⁵','⁶','⁷','⁸','⁹']]
org_eng = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
name_font = [["𝕬","𝕭","𝕮","𝕯","𝕰","𝕱","𝕲","𝕳","𝕴","𝕵","𝕶","𝕷","𝕸","𝕹","𝕺","𝕻","𝕼","𝕽","𝕾","𝕿","𝖀","𝖁","𝖂","𝖃","𝖄","𝖅"],
["𝓐","𝓑","𝓒","𝓓","𝓔","𝓕","𝓖","𝓗","𝓘","𝓙","𝓚","𝓛","𝓜","𝓝","𝓞","𝓟","𝓠","𝓡","𝓢","𝓣","𝓤","𝓥","𝓦","𝓧","𝓨","𝓩"],
["ꪖ","᥇","ᥴ","ᦔ","ꫀ","ᠻ","ᧁ","ꫝ","ⅈ","𝕛","𝕜","ꪶ","ꪑ","ꪀ","ꪮ","ρ","𝕢","𝕣","ડ","𝕥","ꪊ","ꪜ","᭙","᥊","ꪗ","𝕫"],
["𝔸","𝔹","ℂ","𝔻","𝔼","𝔽","𝔾","ℍ","𝕀","𝕁","𝕂","𝕃","𝕄","ℕ","𝕆","ℙ","ℚ","ℝ","𝕊","𝕋","𝕌","𝕍","𝕎","𝕏","𝕐","ℤ"],
["Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ","Ｎ","Ｏ","Ｐ","Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ"],
["🄰","🄱","🄲","🄳","🄴","🄵","🄶","🄷","🄸","🄹","🄺","🄻","🄼","🄽","🄾","🄿","🅀","🅁","🅂","🅃","🅄","🅅","🅆","🅇","🅈","🅉"],
["Ⓐ","Ⓑ","Ⓒ","Ⓓ","Ⓔ","Ⓕ","Ⓖ","Ⓗ","Ⓘ","Ⓙ","Ⓚ","Ⓛ","Ⓜ","Ⓝ","Ⓞ","Ⓟ","Ⓠ","Ⓡ","Ⓢ","Ⓣ","Ⓤ","Ⓥ","Ⓦ","Ⓧ","Ⓨ","Ⓩ"],
["ᴬ","ᴮ","ᶜ","ᴰ","ᴱ","ᶠ","ᴳ","ᴴ","ᴵ","ᴶ","ᴷ","ᴸ","ᴹ","ᴺ","ᴼ","ᴾ","Q","ᴿ","ˢ","ᵀ","ᵁ","ⱽ","ᵂ","ˣ","ʸ","ᶻ"],
['ᴀ','ʙ','ᴄ','ᴅ','ᴇ','ꜰ','ɢ','ʜ','ɪ','ᴊ','ᴋ','ʟ','ᴍ','ɴ','ᴏ','ᴘ','Q','ʀ','ꜱ','ᴛ','ᴜ','ᴠ','ᴡ','x','ʏ','ᴢ'],
["ₐ","B","C","D","ₑ","F","G","ₕ","ᵢ","ⱼ","ₖ","ₗ","ₘ","ₙ","ₒ","ₚ","Q","ᵣ","ₛ","ₜ","ᵤ","ᵥ","W","ₓ","Y","Z"],
["𝐀","𝐁","𝐂","𝐃","𝐄","𝐅","𝐆","𝐇","𝐈","𝐉","𝐊","𝐋","𝐌","𝐍","𝐎","𝐏","𝐐","𝐑","𝐒","𝐓","𝐔","𝐕","𝐖","𝐗","𝐘","𝐙"],
["𝗔","𝗕","𝗖","𝗗","𝗘","𝗙","𝗚","𝗛","𝗜","𝗝","𝗞","𝗟","𝗠","𝗡","𝗢","𝗣","𝗤","𝗥","𝗦","𝗧","𝗨","𝗩","𝗪","𝗫","𝗬","𝗭"],
["卂","乃","匚","ᗪ","乇","千","Ꮆ","卄","丨","ﾌ","Ҝ","ㄥ","爪","几","ㄖ","卩","Ɋ","尺","丂","ㄒ","ㄩ","ᐯ","山","乂","ㄚ","乙"],
["A҉","B҉","C҉","D҉","E҉","F҉","G҉","H҉","I҉","J҉","K҉","L҉","M҉","N҉","O҉","P҉","Q҉","R҉","S҉","T҉","U҉","V҉","W҉","X҉","Y҉","Z҉"]]
Orgtarikh = ["/","0","1","2","3","4","5","6","7","8","9"]
ftarikh = [['/','⁰','¹','²','³','⁴','⁵','⁶','⁷','⁸','⁹']]

headers = {
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
    'Accept-Encoding': 'gzip, deflate',
    'Accept-Language': 'en-US,en;q=0.9',
    'Cache-Control': 'no-cache',
    'Connection': 'keep-alive',
    'Content-Type': 'application/x-www-form-urlencoded',
    'Host': 'www.qqxnxx.com',
    'Origin': 'http://www.qqxnxx.com',
    'Pragma': 'no-cache',
    'Upgrade-Insecure-Requests': '1',
    'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36 OPR/82.0.4227.33'
}
url = 'http://www.qqxnxx.com/download.php'

one = ["کیرم تو خارت", "بصیک بچه کونی", "بای بده ننه پولی", "کیرم تو ننت اوبی", "نگامت کص ننه ", "کص ننه پرده ارتجاعیت", "ننتو شبی چند میدی؟", "خارتو با روغن جامد گاییدم", "کص آبجیت ", "زنا زادع ", "ننه خیابونی", "گی ننه", "آبم لا کص ننت چجوری میشه", "بالا باش ننه کیر دزد", "ننت مجلسی میزنه؟کصصصص ننت جووووون", "ننه جریده", "گی پدر زنا زادع ", "ننتو کرایه میدی؟", "شل ننه بالا باش", "خارکصده به ننت بگو رو کیرم خوش میگذره؟", "ننه توله کص ننتو جر میدم", "بیا ننتو ببر زخمش کردم", "کص ننتو بزارم یکم بخندیم", "به ننت بگو بیاد واسم پر توف بزنه خرجتونو بدم یتیم", "فلج تیز باش ننتو بیار", "ننت پر توف میزنی بابات شم؟", "اوب کونی بزن به چاک تا ننتو جلوت حامله نکردمننه کون طلا بیا بالا", "یتیم بیا بغلم ", "ننت گنگ بنگ دوس داره؟", "بیا بگامت شاد شی خار کصده", "کیرم تو کص ننت بگو باشه", "داداش دوس داری یا آبجی ننه پولی", "۵۰ میدم ننتو بدهکیرم کص آبجی کص طلاااات", "ننه پولی چند سانت دوس داری؟", "دست و پا نزن ننه کص گشاد", "ننه ساکر هویت میخوای؟", "کیر سگا تو کص آبجیت ", "از ننت بپرس آب کیر پرتقالی دوس داره؟", "پستون ننت چنده", "تخخخخ بیا بالا ادبی", "مادرت دستو پا میزنه زیرم", "ننه سکسی بیا یه ساک بزن بخندیم", "خمینی اومد جاده دهاتتونو آسفالت کرد اومدید شهر و گرنه ننت کجا کص میداد؟", "گص کش", "کس ننه", "کص ننت", "کس خواهر", "کس خوار", "کس خارت", "کس ابجیت", "کص لیس", "ساک بزن", "ساک مجلسی", "ننه الکسیس", "نن الکسیس", "ناموستو گاییدم", "ننه زنا", "کس خل", "کس مخ", "کس مغز", "کس مغذ", "خوارکس", "خوار کس", "خواهرکس", "خواهر کس", "حروم زاده", "حرومزاده", "خار کس", "تخم سگ", "پدر سگ", "پدرسگ", "پدر صگ", "پدرصگ", "ننه سگ", "نن سگ", "نن صگ", "ننه صگ", "ننه خراب", "تخخخخخخخخخ", "نن خراب", "مادر سگ", "مادر خراب", "مادرتو گاییدم", "تخم جن", "تخم سگ", "مادرتو گاییدم", "ننه حمومی", "نن حمومی", "نن گشاد", "ننه گشاد", "نن خایه خور", "تخخخخخخخخخ", "نن ممه", "کس عمت", "کس کش", "کس بیبیت", "کص عمت", "کص خالت", "کس بابا", "کس خر", "کس کون", "کس مامیت", "کس مادرن", "مادر کسده", "خوار کسده", "تخخخخخخخخخ", "ننه کس", "بیناموس", "بی ناموس", "شل ناموس", "سگ ناموس", "ننه جندتو گاییدم باو ", "چچچچ نگاییدم سیک کن پلیز D:", "ننه حمومی", "چچچچچچچ", "لز ننع", "ننه الکسیس", "کص ننت", "بالا باش", "ننت رو میگام", "کیرم از پهنا تو کص ننت", "مادر کیر دزد", "ننع حرومی", "تونل تو کص ننت", "کیر تک تک بکس تلع گلد تو کص ننت", "کص خوار بدخواه", "خوار کصده", "ننع باطل", "حروم لقمع", "ننه سگ ناموس", "منو ننت شما همه چچچچ", "ننه کیر قاپ زن", "ننع اوبی", "ننه کیر دزد", "ننه کیونی", "ننه کصپاره", "زنا زادع", "کیر سگ تو کص نتت پخخخ", "ولد زنا", "ننه خیابونی", "هیس بع کس حساسیت دارم", "کص نگو ننه سگ که میکنمتتاااا", "کص نن جندت", "ننه سگ", "ننه کونی", "ننه زیرابی", "بکن ننتم", "ننع فاسد", "ننه ساکر", "کس ننع بدخواه", "نگاییدم", "مادر سگ", "ننع شرطی", "گی ننع", "بابات شاشیدتت چچچچچچ", "ننه ماهر", "حرومزاده", "ننه کص", "کص ننت باو", "پدر سگ", "سیک کن کص ننت نبینمت", "کونده", "ننه ولو", "ننه سگ", "مادر جنده", "کص کپک زدع", "ننع لنگی", "ننه خیراتی", "سجده کن سگ ننع", "ننه خیابونی", "ننه کارتونی", "تکرار میکنم کص ننت", "تلگرام تو کس ننت", "کص خوارت", "خوار کیونی", "پا بزن چچچچچ", "مادرتو گاییدم", "گوز ننع", "کیرم تو دهن ننت", "ننع همگانی", "کیرم تو کص زیدت", "کیر تو ممهای ابجیت", "ابجی سگ", "کس دست ریدی با تایپ کردنت چچچ", "ابجی جنده", "ننع سگ سیبیل", "بده بکنیم چچچچ", "کص ناموس", "شل ناموس", "ریدم پس کلت چچچچچ", "ننه شل", "ننع قسطی", "ننه ول", "دست و پا نزن کس ننع", "ننه ولو", "خوارتو گاییدم", "محوی!؟", "ننت خوبع!؟", "کس زنت", "شاش ننع", "ننه حیاطی \\\\\\/:", "نن غسلی", "کیرم تو کس ننت بگو مرسی چچچچ", "ابم تو کص ننت :\\\\\\/", "فاک یور مادر خوار سگ پخخخ", "کیر سگ تو کص ننت", "کص زن", "ننه فراری", "بکن ننتم من باو جمع کن ننه جنده \\\\\\/:::", "ننه جنده بیا واسم ساک بزن", "حرف نزن که نکنمت هااا :|", "کیر تو کص ننت😐", "کص کص کص ننت", "کصصصص ننت جووون", "سگ ننع", "کص خوارت", "کیری فیس", "کلع کیری", "تیز باش سیک کن نبینمت", "فلج تیز باش چچچ", "بیا ننتو ببر", "بکن ننتم باو ", "کیرم تو بدخواه", "چچچچچچچ", "ننه جنده", "ننه کص طلا", "ننه کون طلا", "کس ننت بزارم بخندیم!؟", "کیرم دهنت", "مادر خراب", "ننه کونی", "هر چی گفتی تو کص ننت خخخخخخخ", "کص ناموست بای", "کص ننت بای :\\\\\\/\\\\\\/", "کص ناموست باعی تخخخخخ", "کون گلابی!", "ریدی آب قطع", "کص کن ننتم کع", "نن کونی", "نن خوشمزه", "ننه لوس", " نن یه چشم ", "ننه چاقال", "ننه جینده", "ننه حرصی ", "نن لشی", "ننه ساکر", "نن تخمی", "ننه بی هویت", "نن کس", "نن سکسی", "نن فراری", "لش ننه", "سگ ننه", "شل ننه", "ننه تخمی", "ننه تونلی", "ننه کوون", "نن خشگل", "نن جنده", "نن ول ", "نن سکسی", "نن لش", "کس نن ", "نن کون", "نن رایگان", "نن خاردار", "ننه کیر سوار", "نن پفیوز", "نن محوی", "ننه بگایی", "ننه بمبی", "ننه الکسیس", "نن خیابونی", "نن عنی", "نن ساپورتی", "نن لاشخور", "ننه طلا", "ننه عمومی", "ننه هر جایی", "نن دیوث", "تخخخخخخخخخ", "نن ریدنی", "نن بی وجود", "ننه سیکی", "ننه کییر", "نن گشاد", "نن پولی", "نن ول", "نن هرزه", "ننه لاشی کیری", "ننه ویندوزی", "نن تایپی", "نن برقی", "نن شاشی", "ننه درازی", "شل ننع", "یکن ننتم که", "کس خوار بدخواه", "آب چاقال", "ننه جریده", "ننه سگ سفید", "آب کون", "ننه 85", "ننه سوپری", "بخورش", "کس ن", "خوارتو گاییدم", "خارکسده", "گی پدر", "آب چاقال", "زنا زاده", "زن جنده", "سگ پدر", "مادر جنده", "ننع کیر خور", "چچچچچ", "تیز بالا", "ننه سگو با کسشر در میره", "کیر سگ تو کص ننت", "kos kesh", "kir", "kiri", "nane lashi", "kos", "kharet", "blis kirmo", "اوبی کونی هرزه", "کیرم لا کص خارت", "کیری", "ننه لاشی", "ممه", "کص", "کیر", "بی خایه", "ننه لش", "بی پدرمادر", "خارکصده", "مادر جنده", "کصکش", "کیرم کون مادرت", "بالا باش کیرم کص مادرت", "مادرتو میگام نوچه جون بالا??", "اب خارکصته تند تند تایپ کن ببینم", "مادرتو میگام بخای فرار کنی", "لال شو", "کیرم تو خارت", "بصیک بچه کونی", "بای بده ننه پولی", "کیرم تو ننت اوبی", "نگامت کص ننه ", "کص ننه پرده ارتجاعیت", "ننتو شبی چند میدی؟", "خارتو با روغن جامد گاییدم", "کص آبجیت ", "زنا زادع ", "ننه خیابونی", "گی ننه", "آبم لا کص ننت چجوری میشه", "بالا باش ننه کیر دزد", "ننت مجلسی میزنه؟", "کصصصص ننت جووووون", "ننه جریده", "گی پدر زنا زادع ", "ننتو کرایه میدی؟", "شل ننه بالا باش", "خارکصده به ننت بگو رو کیرم خوش میگذره؟", "ننه توله کص ننتو جر میدم", "بیا ننتو ببر زخمش کردم", "کص ننتو بزارم یکم بخندیم", "به ننت بگو بیاد واسم پر توف بزنه خرجتونو بدم یتیم", "ننه کون طلا بیا بالا", "یتیم بیا بغلم ", "ننت گنگ بنگ دوس داره؟", "بیا بگامت شاد شی خار کصده", "کیرم تو کص ننت بگو باشه", "داداش دوس داری یا آبجی ننه پولی", "۵۰ میدم ننتو بده", "فلج تیز باش ننتو بیار", "کیرم کص آبجی کص طلاااات", "ننه پولی چند سانت دوس داری؟", "دست و پا نزن ننه کص گشاد", "ننه ساکر هویت میخوای؟", "کیر سگا تو کص آبجیت ", "از ننت بپرس آب کیر پرتقالی دوس داره؟", "پستون ننت چنده", "تخخخخ بیا بالا ادبی", "مادرت دستو پا میزنه زیرم", "ننه سکسی بیا یه ساک بزن بخندیم", "خمینی اومد جاده دهاتتونو آسفالت کرد اومدید شهر و گرنه ننت کجا کص میداد؟", "کیرم تا ته و از پهنا تو کص مادرت", "کص ناموس مادرت", "مادر کص پاپیونی ", "مادر جنده حروم تخمی", "اوبی زاده حقیر", "بابات زیر کیرم بزرگ شد", "اسمم رو کون مادرت تتو شده", "خیخیخیخیخی", "چچچچچچچچ", "زجه بزن ناموس گلابی", "مادرت کیرمه ", "بابات منم ", "تخم سگ حروم زاده ", "کص ناموست ", "خواهرتو گاییدم", "ریدم بهت بیشعور", " بی شرف", " ریدم تو مغزت", " بی ارزش", " کصکش", " ریدم توی ناموست", " بی ناموس", " مادرجنده", " خواهر کصکش", " ریدم توی کل طایفت", " بی ناموس برو", " خوشم ازت نمیاد کصکش", " تو کصکشی", " برو خواهر جنده", "برو مادرجنده", " برو برادر کونی", " کونکش", "عوض بی ناموس", "ریدم تو قبر مادرت", "ریدم تو قبر پدرت", " ریدم تو قبرت", " ریدم تو زاتت", " ریدم تو خواهر جنده", " خواهر جندت خوبه", " مادر جندت خوبه", " پدر کونکشت خوبه", "برادر کونیت خوب", " پدرسگ", " مادر سگ", " برادر سگ", " خواهر سگ", " خواهر جندت چی", " مادر جندت چی", " پدر کونیت چی", " برادر کونیت چی", " اره جنده ها", " تو جنده ای", " تو کونی ای", " توی کصکشی", " خوشم از جنده ها نمیاد", " خواهرت جنده شده", " مادرت جنده شده", " جنده برو خودت رو جمع کن", " مامانت امشب روی کی هستش", " خواهرت پیش کیه", " برادرت داره کجا کون میده", " بابای قرمساقت کو", " خواهرت امشب روی کی هستش", " مادرت امشب روی کی خوابیده", "ننت پر توف میزنی بابات شم؟", "اوب کونی بزن به چاک تا ننتو جلوت حامله نکردم", " ریدم بهت", " بیشعور", " بی شرف", " ریدم تو مغزت", " بی ارزش", " کصکش", " ریدم توی ناموست", " بی ناموس", " مادرجنده", " خواهر کصکش", " ریدم توی کل طایفت", " بی ناموس برو", " خوشم ازت نمیاد کصکش", " تو کصکشی", " برو خواهر جنده", " برو مادرجنده", " برو برادر کونی", " کونکش", " عوض بی ناموس", " ریدم تو قبر مادرت", " ریدم تو قبر پدرت", " ریدم تو قبرت", " ریدم تو زاتت", " ریدم تو خواهر جنده", " خواهر جندت خوبه", " مادر جندت خوبه", " پدر کونکشت خوبه", " برادر کونیت خوب", " پدرسگ", " مادر سگ", " برادر سگ", " خواهر سگ", " خواهر جندت چی", " مادر جندت چی", " پدر کونیت چی", " برادر کونیت چی", " اره جنده ها", " تو جنده ای", " تو کونی ای", " توی کصکشی", " خوشم از جنده ها نمیاد", " خواهرت جنده شده", " مادرت جنده شده", " جنده برو خودت رو جمع کن", " مامانت امشب روی کی هستش", " خواهرت پیش کیه", " برادرت داره کجا کون میده", " بابای قرمساقت کو", " خواهرت امشب روی کی هستش", " مادرت امشب روی کی خوابیده", "کیرم کون مادرت", "بالا باش کیرم کص مادرت", "مادرتو میگام نوچه جون بالا", "اب خارکصته تند تند تایپ کن ببینم", "مادرتو میگام بخای فرار کنی", "لال شو دیگه نوچه", "مادرتو میگام اف بشی", "کیرم کون مادرت", "کیرم کص مص مادرت بالا", "کیرم تو چشو چال مادرت", "کون مادرتو میگام بالا", "بیناموس  خسته شدی؟", "نبینم خسته بشی بیناموس", "ننتو میکنم", "کیرم کون مادرت ", "صلف تو کصننت بالا", "بیناموس بالا باش بهت میگم", "کیر تو مادرت", "کص مص مادرتو بلیسم؟", "کص مادرتو چنگ بزنم؟", "به خدا کصننت بالا ", "مادرتو میگام ", "کیرم کون مادرت بیناموس", "مادرجنده بالا باش", "بیناموس تا کی میخای سطحت گح باشه", "اپدیت شو بیناموس خز بود", "کیرم از پهنا تو ننت", "و اما تو بیناموس چموش", "تو یکیو مادرتو میکنم", "کیرم تو ناموصت ", "کیر تو ننت", "ریش روحانی تو ننت", "کیر تو مادرت", "کص مادرتو مجر بدم", "صلف تو ننت", "بات تو ننت ", "مامانتو میکنم بالا", "کیر ترکا به ناموست", "سطحشو نگا", "تایپ کن بیناموس", "خشاب؟", "کیرم کون مادرت بالا", "بیناموس نبینم خسته بشی", "مادرتو بگام؟", "گح تو سطحت شرفت رف", "بیناموس شرفتو نابود کردم یه کاری کن", "وای کیرم تو سطحت", "بیناموس روانی شدی", "روانیت کردما", "مادرتو کردم کاری کن", "تایپ تو ننت", "بیپدر بالا باش", "و اما تو  خر", "ننتو میکنم بالا باش", "کیرم لب مادرت بالا", "چطوره بزنم نصلتو گح کنم", "داری تظاهر میکنی ارومی ولی مادرتو کوص کردم", "مادرتو کردم بیغیرت", "هرزه", "وای خدای من اینو نگا", "کیر تو کصننت", "ننتو بلیسم", "منو نگا بیناموس", "کیر تو ننت بسه دیگه", "خسته شدی؟", "ننتو میکنم خسته بشی", "وای دلم کون مادرت بگام", "اف شو احمق", "بیشرف اف شو بهت میگم", "مامان جنده اف شو", "کص مامانت اف شو", "کص لش وا ول کن اینجوری بگو؟", "ای بیناموس چموش", "خارکوصته ای ها", "مامانتو میکنم اف نشی", "گح تو ننت", "سطح یه گح صفتو", "گح کردم تو نصلتا", "چه رویی داری بیناموس", "ناموستو کردم", "رو کص مادرت کیر کنم؟", "نوچه بالا", "کیرم تو ناموصتاا", "یا مادرتو میگام یا اف میشی", "لالشو دیگه", "بیناموس", "مادرکصته", "ناموص کصده", "وای بدو ببینم میرسی", "کیرم کون مادرت چیکار میکنی اخه", "خارکصته بالا دیگه عه", "کیرم کصمادرت", "کیرم کون ناموصد", "بیناموس من خودم خسته شدم توچی؟", "ای شرف ندار", "مامانتو کردم بیغیرت", "و اما مادر جندت", "تو یکی زیر باش", "اف شو", "خارتو کوص میکنم", "کوصناموصد", "ناموص کونی", "خارکصته ی بۍ غیرت", "شرم کن بیناموس", "مامانتو کرد ", "ای مادرجنده", "بیغیرت", "کیرتو ناموصت", "بیناموس نمیخای اف بشی؟", "ای خارکوصته", "لالشو دیگه", "همه کس کونی", "حرامزاده", "مادرتو میکنم", "بیناموس", "کصشر", "اف شو مادرکوصته", "خارکصته کجایی", "ننتو کردم کاری نمیکنی؟", "کیرتو مادرت لال", "کیرتو ننت بسه", "کیرتو شرفت", "مادرتو میگام بالا", "کیر تو مادرت", "کونی ننه ی حقیر زاده", "وقتی تو کص ننت تلمبه های سرعتی میزدم تو کمرم بودی بعد الان برا بکنه ننت شاخ میشی هعی   ", "تو یه کص ننه ای ک ننتو به من هدیه کردی تا خایه مالیمو کنی مگ نه خخخخ", "انگشت فاکم تو کونه ناموست", "تخته سیاهه مدرسه با معادلات ریاضیه روش تو کص ننت اصلا خخخخخخخ ", "کیرم تا ته خشک خشک با کمی فلفل روش تو کص خارت ", "کص ننت به صورت ضربدری ", "کص خارت به صورت مستطیلی", "رشته کوه آلپ به صورت زنجیره ای تو کص نسلت خخخخ ", "10 دقیقه بیشتر ابم میریخت تو کس ننت این نمیشدی", "فکر کردی ننت یه بار بهمـ داده دیگه شاخی", "اگر ننتو خوب کرده بودم حالا تو اینجوری نمیشدی"]

def fosh_saz(text):
 return f"{choice(one)}{text}"

two = ["کنار تو بودن آرامش بخش ترین جای دنیاست" ,"بی شک … به دنیا آمده ام تا عاشق تو باشم!" ,"تا نَفَس دارَم قَلبـــ❤️ـــم اِقامتگاهِ توست …" ,"تو سرآغاز همه خنده های من هستی عشقم!" ,"همسرم! داستان های عاشقانه زیبا هستند و داستان عشق من و تو زیباترین آن ها" ,"عاشق تو بودن بزرگ ترین نقطه ضعف و نقطه قوت من است" ,"دوستت دارم و و این آغاز و پایان همه چیز است عشق من!" ,"هنوز … هر روز … عاشقت می شوم خانومم …" ,"تو پرنس من هستی و من پرنسس تو … و عشق در این میان پادشاهی می کند" ,"تو حَقیقِی تَرین دیالُوگِ عِشقی !" ,"آمدی به زندگی ام و روشن کردی چشم و دلم را بانو جان" ,"عشق تو رویای زیبایی بود که به حقیقت پیوست عزیزم!" ,"مهربانم! با تو خودم را گم می کنم و بی تو در آرزوی گم شدنم" ,"شدی قَلبُ و تَن و روحَم" ,"با تو زیر بارانم، چتر برای چه؟ خیال که خیس نمی شود !" ,"تُ جان مایِی‌ خودخَبر نداری !" ,"تو تَک مَنظُومِه کَهکِشّانِ قَلبَمی!" ,"می خواهمت و این شاید ابتدای خود خواهیم باشد!" ,"هوا خوبه چون تو هوامو داری" ,"قلب من همیشه برای تو خواهد تپید و با تمام وجودم به دوست داشتنت ادامه خواهم داد" ,"هر روز بیش از پیش به این راز پی می برم که تو دنیای من هستی" ,"دل را قرار نیست مگر در کنار تو …" ,"تو مرا جان و جهانی چه کنم جان و جهان را" ,"I LOVE YOU" ,"می خواهمت که خواستنی تر از هر کسی …" ,"گرمای دستات آرامش منه" ,"تو که هستی غرق در آرامشم" ,"خوش تر از دوران عشق ایام نیست" ,"You are my everything" ,"تو همه چیز منی" ,"فقط یار من و یار من و یار خودم باش" ,"گرمآی دستآت روی گونم بهتَرین حسه جان جآنآنم" ,"تو قلبم میمونی واسه همیشهـ" ,"صدای نفس کشیدن تو بهترین صدای زندگی من است" ,"نگاهت را قاب می گیرم در پس آن لبخند معصوم که به من شور و نشاط زندگی می‌ بخشد" ,"دو کلام حرف حساب باهات دارم : دوست دارم" ,"در نظرم چشمان تو ناب ترین شعر دنیا و موهایت غزل واره های زیبای جهان هستند" ,"چه خوبه که هستی" ,"در سر که نه، من در تمام وجودم عشق تو را می پرورانم" ,"کی فکرشو می کرد تو بشی دلیل حال خوب و انگیزه تموم زندگیم؟" ,"دُچارت شده‌ اَم بیا و چاره‌ ام باش" ,"من خیلی خوش شانسم که تو در زندگی منی" ,"مرسی که هستی" ,"جونم فدای یه تار موت" ,"تو برام خیلی مقدسی" ,"قند عسلم" ,"عسل من" ,"نفسم" ,"دورت بگردم" ,"الهی قوربون اون چشات بشم" ,"گفته بودم عاشقتم؟"]
def love_saz(text):
 return f"{choice(two)}{text}"
 
def font(text, lang):
 request = get('https://api.codebazan.ir/font/?type={}&text={}'.format(lang, text))
 if request.json()['Ok' if lang else 'ok'] == True:
  results = "🌜 ᴛʜᴇʀᴇ's ʏᴏᴜʀ ʀᴇsᴜʟᴛ 🌛 \n\n"
  for key, value in request.json()['Result' if lang else 'result'].items():
   results += f"├ • {key} | `{value}`\n"
 return results

def create_time():
 a = datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")
 ran = choice(fonts)
 for char in a :
  a = a.replace(char , ran[int(org.index(str(char)))])
 return a
 
def fozolitime():
 a = datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")
 return a

def create_time2():
 a = datetime.now(timezone("Asia/Tehran")).strftime("%H:%M")
 ran = choice(fonts2)
 for char in a :
  a = a.replace(char , ran[int(org.index(str(char)))])
 return a

def create_tarikh():
 a = jdatetime.date.today().strftime("%Y/%m/%d")
 ran = choice(ftarikh)
 for char in a :
  a = a.replace(char , ran[int(Orgtarikh.index(str(char)))])
 return a

def fozolidate():
 a = jdatetime.date.today().strftime("%Y/%m/%d")
 return a

def fontinname(name):
 name = name.upper()
 rnd = choice(name_font)
 for char in name:
  try:
   name = name.replace(char , rnd[org_eng.index(char)])
  except:
   pass
 return name

def ytdl(text):
  id = get(f"https://one-api.ir/youtube/?token=x&action=search&q={text}&onlyvideos=").json()
  id  = id["result"][0]["id"]["videoId"]
  dl = get(f"https://one-api.ir/youtube/?token=x&action=fullvideo&id={id}&filter=audio").json()
  sts = dl
  dl = dl["result"]["formats"][6]["url"]
  dl = get(dl, stream = True)
  if sts["status"] == 200:
     
    with open("search.mp3",'wb') as f:
      shutil.copyfileobj(dl.raw, f) 
      return id
def ytinfo(text):
  id = get(f"https://one-api.ir/youtube/?token=x&action=search&q={text}&onlyvideos=").json()
  id  = id["result"][0]["snippet"]["title"]

  return id

import requests
def download_song(name):
    url = f"https://mrn.iran.liara.run?name={name}"
    response = requests.get(url)
    if response.status_code == 200:
        with open(f"{name}.mp3", 'wb') as file:
            file.write(response.content)
# مثال استفاده از تابع
song_name = "یاس"

download_song(song_name)
   
def DLX(Url):
    data = {'videoid': Url}
    soup = BeautifulSoup(post(url=url, headers=headers, data=data).text, 'html.parser')
    link = findall(r'"(https://video-hw.xnxx-cdn.com/videos/flv/.*)"', str(soup.find('script', {'type': 'application/ld+json'})))[0].split('"')[0]
    FileName = link.split('/')[-1]
    FileName = FileName[:FileName.find('?')]
    return link
    
from youtube_search import YoutubeSearch
from pytube import YouTube
import pytube

def song_YouTube(song_name):
    search_query = f"{song_name}"
    results = YoutubeSearch(search_query, max_results=1).to_dict()
    video_id = results[0]['id']
    video_url = f"https://www.youtube.com/watch?v={video_id}"
    video = pytube.YouTube(video_url)
    audio_streams = video.streams.filter(only_audio=True).all()
    name_file = video.title
    des = video.thumbnail_url
    caption = f"Name = {name_file}\n\n @Jack_self"
    audio_streams[0].download(filename=name_file)
    return name_file, caption , des
#____________________________________________________
def get_youtube_video(song_name):
    from pytube import YouTube
    from youtube_search import YoutubeSearch
    search_query = f"{song_name}"
    results = YoutubeSearch(search_query, max_results=1).to_dict()
    video_id = results[0]['id']
    video_url = f"https://www.youtube.com/watch?v={video_id}"
    try:
        video = pytube.YouTube(video_url)
        caption = video.title
        video_streams = video.streams.filter(only_video=False, resolution="360p").all()
        file_path = video_streams[0].download()
        des = video.thumbnail_url
        return file_path , des , caption
    except Exception as e:
        print(f"Error: {e}")
        return None

# در صورتی که فقط به عنوان برنامه اصلی اجرا شده است:
if __name__ == "__main__":
    video_file = get_youtube_video("song_name")
    if video_file:
        # انجام عملیات ارسال ویدیو
        pass
    else:
        print("Error: Failed to get the video.")
#________________________________________________________
from bs4 import BeautifulSoup
def torb(text):
    url = f"https://basalam.com/s?q={text}"
    rq = requests.get(url)
    soup = BeautifulSoup(rq.content , "html.parser")
    
    Description = ""
    for qpo in soup.find_all(id = "name-link" , limit=1):
        s = str(qpo.get_text()).replace("\n" , "").strip()
        Description += f"{s}جداکننده"
        
    price = ""
    for qpo in soup.find_all("span" , class_ = "product-card__price-original mt-1" , limit = 1):
        s = str(qpo.get_text()).replace("\n" , "").strip()
        price += s
        
    link = ""
    for test in soup.find_all("a" , id = "image-link", limit = 1):
        titel = test.get('title')
        for i in soup.find_all("img" , attrs = {"alt" : titel}, limit = 1):
            link = i.get("src")
            
    return Description , price , link
    
    
def snippet(params):
    url = 'https://api.crabon.io/v1/snippet'
    path = 'i.png'
    response = post('https://carbonara-42.herokuapp.com/api/cook', json=params)
    if response.status_code == 200:
        with open(path, 'wb') as f:
            for chunk in response:
                f.write(chunk)
    print(response.status_code)
    
from PIL import Image, ImageDraw, ImageFont
import os
import random
from datetime import datetime
import pytz

def generateimage(text):
    rand_img = ["image1.jpg", "image2.jpg", "image3.jpg", "image4.jpg", "image5.jpg", "image6.jpg"]

    font_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'font.ttf')
    font_obj = ImageFont.truetype(font=font_path, size=100)

    image_path = random.choice(rand_img)
    image = Image.open(image_path)
    W, H = image.size

    draw = ImageDraw.Draw(image)
    bbox = draw.textbbox((0, 0), text, font=font_obj)
    wt = bbox[2] - bbox[0]
    ht = bbox[3] - bbox[1]

    text_color = random.choice(["#00c7a4", "#0071c7", "#c7a200", "#728593", "#943633", "#6495ed", "#43f70a", "#e1b2ae", "#527130", "#629f5d", "#3d4e90", "#9a9ec4"])

    draw.text(((W - wt) / 2, (H - ht) / 2), text, font=font_obj, fill=text_color)

    image.save('time_image.jpg')

    return image

    tz = pytz.timezone('Asia/Tehran')
    text = datetime.now(tz).strftime("%H:%M:%S")
    image = generateimage(text)

def logo(text):
  logo = [f"https://dynamic.brandcrowd.com/asset/logo/1b18cb55-adbe-4239-ac3f-4e22d967d434/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1a2e3c8f-08db-4fad-b0f2-de3e58f24ce9/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7925e4fe-d125-4d7f-a3f6-12ecfb7fa641/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ad871f75-cf28-4e97-8580-f72f2844db67/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5f5dfa37-29e3-4a9f-ba5b-31f8214b8d40/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/bc419bf7-5723-4380-836e-26c55aa795c5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/086c0526-0be0-48b0-adee-f17844ac911c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/07d54ba4-4489-48cc-9a00-fe7e9cb52276/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c699f864-5fac-4cb7-b201-712259727a72/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d74c5889-e17a-44a1-852a-3bc1c0f64483/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/00359d52-ef6b-41bf-ae27-4339609fede3/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ed409e0a-9816-4b65-a3b9-e8f361798227/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7ea43112-2b71-4784-a6f1-9cb95f61e673/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/90880bf9-35ca-406d-aec2-af41e327b26a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8785de07-dc7b-4b47-86ff-270d14586345/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e49fa5be-1a3b-48f3-bc39-3109ce6c4bfa/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/26b1f627-ad53-408f-b023-3b0e77da78f7/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8a192263-eb69-48d0-a1bd-2599769e2787/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5313cf95-4ab7-4ff3-895e-ca21681e452d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/da767a21-6d72-4a2b-8a04-7b8c448e53b8/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0424daff-7df1-4bfb-aa07-ed52cfc99e1f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/eaa2cf5e-7df1-4224-b627-4a4094a2b44c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dcdaf4b4-2158-459b-a290-66d266fd3003/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/4030324b-894c-4ccf-906d-7a039b10d7c3/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/79450b06-4c42-4669-88c8-6a5f843f3b08/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8f52d556-af31-489b-90a2-5a1f9653f07c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/443aa5c4-6556-468c-9d44-cc31320aca59/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/739440b5-4846-438e-9e21-9a43e2099034/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d7076540-b78d-4092-bec3-84d0b5b6cf35/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/20333bac-5343-404d-83fe-49e54a591e5a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f78a6d4d-ca0b-4d59-92bd-5dde30dc5beb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ba3e427e-c7e2-45fd-8583-ae39792b520a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/bfda2f02-cf16-4a9a-8174-5a1c474fa8b4/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ebea98c1-507c-4cb6-8aea-332f330add3e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/88107639-8c59-48d7-aa72-b5ba622f2d2f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b2aa5492-009b-4b1a-85e5-e945c193361e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3b6db5a4-6408-43db-8155-7828258c7dfb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/06a2017e-24b4-4dc9-921a-4b93bd3aaa41/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/a7313939-d69e-4204-b0e8-1a6099c48b22/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d70cdc43-79ea-4bff-bd87-d8edaf4e691b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/930b5655-bde9-4f44-a31c-198367190eb8/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/2d1a8bbb-1c9e-4516-9be5-fa3d05693757/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/90c9913d-ade6-45af-8371-c91a9b07964c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/644391b8-e59d-422f-a81c-a7d5428c8efb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9182c620-b265-491e-bda1-6db153a5fb94/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/931f8416-aa36-4a01-af0d-35b731f898db/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dbf78f01-a741-4c92-a6e4-668129dca2bb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f4953040-e80b-49cf-a347-1cda77a97190/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d66113bf-3e06-4729-bbce-67fcf0d1848c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/a3f20deb-e126-48f4-a972-3877f69360fe/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ba6724d8-4138-4263-a434-fe7b7acd6b0b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5ea52fd4-10aa-4a70-9d25-3cbfb56c8bb4/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f5180411-054b-4b76-bb2b-6265981fbe11/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ec4faa35-d0f7-434e-8c25-c3a28b956049/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3a06896d-6a8e-4b61-a124-e0ab0453d07e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c5140ac3-0a5c-45f1-bf6b-203f02c3c4e4/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c7cf0c9e-3e48-40bb-81b5-4cc40df5a2a6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/752778e8-6197-4a13-8900-dcb666ca2bd1/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e0f5a980-b751-4b81-8425-ac2ecb77259a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ccf02e3a-6d03-44a8-9ec0-b5e33001bbce/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/21bed36c-cb81-407a-86b0-8333e357c59e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9d0bfaab-7506-41b9-8721-46555c7798df/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/794f593c-f03c-47ee-be57-a177409a1618/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/017d56c9-aaf5-4e1c-b0d5-e016b9f49e46/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0e981fc4-accf-4070-b8d0-9ac279f8e808/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d14e8ade-80d8-4e96-8d47-ed8a5cfbe180/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/cfaa5fac-c17d-4e75-9218-fe6673b3b40d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c00358da-24f7-451f-95f3-65f3f3d9bf14/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/97be57bb-13de-44c5-8000-9498feb3789b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8725b125-0434-421e-863e-9c94618943f6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/aa0eccb0-8dd5-48e5-940a-0157ad466072/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c5d0430c-6ecc-4278-a5a3-3b0e2cb6c6f5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/000e9616-8763-4add-acff-60754b711c0d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/a1966764-79c0-4adb-a7c7-5372dcbb63f1/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8e40623a-cb2b-406f-a91b-c47f6fb306f9/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/42c98814-fdda-46d1-a4e1-2e2011fb9b65/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0bf69dc7-3925-4825-b00f-8b66d7b30721/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/151adcab-dad2-41e6-883b-a579d726c5bb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9ac17003-596e-446d-b715-fbc245036803/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/2c0269cb-ad5f-464a-8cd0-227ecf8a77a6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7a2dca3f-e337-47fc-aba0-469c4fabeb63/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1a930669-1c02-47d8-bbe0-cf04975b8522/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1a248710-0d91-4aa7-8141-6da939c841e9/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1f83800a-0dbf-410b-954c-e19c2dab1ef8/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/17753682-84c3-4447-866c-ea170fc7b7d5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d71a7cf9-a684-4b34-a75e-ffb6bf641a7d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/eec764d5-ae8e-4ebf-affb-32082312f42e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/011a6521-23cf-40b6-88b3-990c6ec22a6e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/cf3f675f-e615-4f5e-a595-49332aacdb81/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3df1a69c-85ad-4dc8-9b00-3bd8e4db8383/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3df1a69c-85ad-4dc8-9b00-3bd8e4db8383/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/86c9985d-8949-44d8-9dc6-47a86f993993/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/c2e19663-ef1e-475f-8208-e22473849445/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e79b4266-bfa9-40da-aef7-d2eb90656d3b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0a8d749e-9df5-4476-9a10-dc1ac86a149c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/acaede2b-1c05-465f-9a33-1c11ac293f11/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/aa6390ec-4752-416b-9b77-034dcc34b17f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/37cc6ec8-b36e-41bd-bc72-4aa6363f0ebc/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5b9e7746-36eb-4c66-9bcd-1e252699d1f2/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/62de87f1-1257-46c7-9590-99a568115545/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/909ab155-c255-4d08-9918-69b0fcbef647/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ee799336-529d-4b36-9ebc-f2009d21e545/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d3a6e797-2c55-4b35-adf0-4ac763b95808/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d8bb2364-0350-4e2f-9095-0e093c504445/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/04cb4959-84cd-4beb-ae55-59884139603b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0e386f0d-907a-4a3e-9ce8-ae7b3f68d66a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/12531e0d-96ef-4b68-993e-cb4179a2ff29/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1c8935c3-e145-4890-ba64-91735c8dfe4f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/52f1623a-4af8-4065-bf8c-a746dff09fef/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5b2cb293-249e-46cd-901e-d190dc002e89/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/670e63fb-4dd9-4d17-9ba3-f2c944d45f28/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9013d098-93e2-4346-9656-6b63c24b440b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b2e761bd-82ea-4350-a752-fa556cef2dd0/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b5843fcf-37a3-44e7-9938-91addefa09fc/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dbd21a15-b0db-4ae9-a561-fd112aba6fcd/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/eb194df6-c069-4a33-82b6-4f4383877988/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f0223266-f576-40c7-a31d-d2c17c584a46/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/055241ff-dc4f-4743-90be-1c9caa0c900b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1fe7224c-8946-48e9-9d11-c978d0069fdb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3e0ee4c9-8165-42eb-801c-fb26aa2ecf0a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/4b4b9948-7c07-4f07-a1d1-d33b44084cc0/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/72241f70-7f3d-459d-8638-75b3cf6e12ee/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7b98994d-e50c-409c-ab2a-af1a568c16ad/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/888b0d00-f6a6-4c56-a744-9d5b3b6965f6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9467cb72-d11e-4462-804f-c7b34bf895d7/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b1c634dd-aacc-4190-986c-7ace14ed3ec6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/db41be37-350e-40f7-a3bf-7247e2a11948/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e31b1fb6-0f38-4c75-bc3f-3373aaaf3571/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f287cbe2-9389-4de0-9bd3-6b8eacf2768c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/01866580-0a27-4fae-8529-595b3d08c3c6/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/098a3e12-9643-417f-b14e-9c0929c21b1e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/449247de-6d8d-44a9-90e1-e54d4ee72137/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/65652ce5-16fd-45f1-b5bb-257b1b95be2c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/889a604d-aa1b-4486-b09c-7d0f9368becb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/89c21f53-1a93-41b4-b0e0-e7233ce40c27/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8c18fdd5-9007-4fb8-85bd-549e21c6ceea/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/97191afc-e552-42a7-a96f-5796e306ae1f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/a74b621b-fb9c-49d4-a7b9-48c702dee154/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ab948d82-e22b-4ec2-a4ae-eb93f55ddaf8/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/adcb5161-3b1e-4b2c-b658-42cdbef64c93/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b05d717d-a4a8-4350-a98e-4e6635271d2d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d5415cbf-418d-45ba-9e6c-05f9385457f0/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dcc17996-39bf-45d1-8b9d-f66e0b75d693/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e33108a3-9c4f-4ebe-a031-8304071f6888/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ea3439b4-3ae8-4789-9fb8-acc5745bde0d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f7e73e79-7ee6-42cf-9af2-7ac147c6c78f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/11e9e67b-723d-4320-9481-7df27efd143e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/09699c93-f687-4c58-b6dc-cb8010de7df9/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1cc2db6f-d3e7-425b-8b2a-d1349d3739d5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/37922c94-880a-4d6f-8070-914087acc09a/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/4a69a160-fe1d-4391-8af1-2d7ac9580953/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/5465ad8f-d9c4-4a4c-b587-23c98d231ae8/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/55c9faad-542c-4c56-b101-f3e21bbfb95f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/96b7e527-d141-442d-babb-fda190233a1e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ce545f6b-c441-4848-a02a-ca8779e52f29/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/e8fcd3b0-0ce8-41f1-abf4-a7283ee40ffc/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f18ae32f-ce31-4946-9704-72e193f5cad2/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/fc5aa3ab-e782-456e-b7e5-f93dfcd325ee/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1a5e85a2-ae4e-411d-ab13-43a3b918f478/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3c337f69-2066-4abe-b9ae-228ddf86bd4b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/56d42ddd-1c3d-4787-a7fe-cc6e9960c875/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7feb63c0-0210-4bb4-8a52-56849b495b8c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/8ee82bd4-4869-4fad-84c8-68f60f10959f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/95b5c8a5-d62d-4474-ba64-e726faa1bb97/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/a791985b-1b64-4f23-bd2d-be67bdc27577/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/bb8044ba-5367-47de-8c4b-9ca90bd67c4d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dbcdc939-e87b-45ce-8eb7-3e85d6a71bfa/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/dbfdb19c-5c38-43e2-a500-61426d4fd768/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/fcda8baf-e858-47ca-8e55-e945cebaf838/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/88aa303b-dbb1-40a3-ada7-c138d457df7d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/7b84c12f-6060-4f93-a0cb-6cfbfb0d649f/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d1510dc5-ac8d-497d-9ad9-c9fdec93796d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/484e6686-0062-4926-ba81-0b81353b4ed0/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b538b140-c1a4-4188-a160-b76e140b4ad5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0e73bf05-13a0-41aa-9b57-00d6670b4952/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/f0f53e57-7dda-469a-9513-273c8d2bb514/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/2d81292d-7c5a-41a2-9dfd-9d434a413c63/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3bf52b81-9940-4fd2-b326-ef90cc077272/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/864efb77-e149-4fd0-a058-976c7c5e492e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/07f5f5a5-ea09-4e94-88fa-d9ee9060b458/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/eaf58c74-5f43-48c3-9de5-2a0b94e1f8a2/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/3e1331ed-fc20-49d2-a55e-c3ced0e47c56/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/34372e0c-47ab-4f95-b136-2de09c21b8ed/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/fc5269e7-6172-4007-a47f-a183d8d7f3cd/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/cf1d7785-935c-4d28-a1f9-8d94321c6fba/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/9fcb5110-8b0e-4c6f-9764-b38dbd6e0112/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/00f0c0dc-7af4-441a-ab9e-cf5bb78fe220/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/6805ec29-0e17-4da2-ba12-1f170bc0ce45/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/d84859df-c614-4135-a55d-b9f95a19e2ff/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/ca2ff2db-806b-499f-b3b1-c0a5e1428a94/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/b0b0828d-dd3b-4c9f-a8c7-366f005590cb/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/696d69a2-8c49-4bd8-82c7-2cc6b14d3b28/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/770dbe6d-420f-4860-953a-69e763aafa00/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/00023174-20f6-4e58-9b10-65fe054bfbc4/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/02ffc18d-1bbe-4bd7-b177-3c79082a6a04/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0300c219-2ad6-47af-bb68-e3e0f241c34b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/04e8e3bd-0cff-4a68-98e1-b0f412c46611/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/059b8c80-052f-419b-9baa-26b62f7405cc/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/071ae338-60be-4a21-9437-cb15ec7ab4e9/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0748d91a-ac32-4b37-a27f-89ee68e8753b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0843ed95-3f00-4737-8f9c-af83b0fb92b3/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/08c3aa53-d862-41c9-adb1-fa10bd6a0fdd/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/08ffb721-d5fc-4675-9cd7-539893d17d8c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/09c8e48d-16c9-4fd6-aeec-0b87fdfee159/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0ad29a62-01cb-4f96-8643-a7eab0eb84f7/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0affd79b-f5df-4a61-a22f-2dc7cbab569d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0bba65a5-15b9-4da0-bf96-7ea879bf7081/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0c8acf74-1b27-4545-b46c-54327dc4f38e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0e88be07-4898-432f-b634-5a5df787416d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/0f0e7abb-5d45-4f31-9848-6b27f7fbf76d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1058614e-b9be-409b-962c-8f541cba0dd0/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/120ba62c-5a91-4c6a-a6c9-673d2baa35fe/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/13953056-ace8-4a1b-9b7d-949ed1798c0d/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/13c42cc5-eb6b-4587-8581-c55813bcf37e/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/13d16dbe-77f4-4a05-b0a0-ee6922941e0b/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/145f8d81-1f17-4cc4-b35c-44da350be2f5/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/15654083-1f64-4b60-bb53-3eb916141c3c/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/172fd7df-cb66-4aa9-a1ce-fbccf26d05f2/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/176993a8-22ac-44f1-a735-af004fd7d8dd/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/17bd5e20-9941-4177-b2a6-8ff0e932abda/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/17d56cfe-ca05-4de2-9648-ffbb3d27bb76/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1842af2e-44f8-4429-b840-5377904a7620/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/18cbcbad-b87b-4af7-9389-5c3cc19b6fc7/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/192be4b6-5a8a-42bd-8ec4-580c063d7f21/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1a487867-0157-4e8c-a568-aeeea80fce00/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1ada54d4-e64a-4e45-9d31-1706a6ada796/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1b65d0dc-43dd-4710-aa4b-e69aa3066982/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1b76e39d-7f17-4fb0-b12c-b68e1301a559/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1bd1306f-8b8f-4515-93b9-0438f6ff8130/logo?v=4&text={text}",f"https://dynamic.brandcrowd.com/asset/logo/1ca25ddf-40de-40fa-b93d-e29af3e46c05/logo?v=4&text={text}"]
  url = random.choice(logo)
  randomlogo = get(url, stream = True)
  if randomlogo.status_code == 200:
    with open("logo.png",'wb') as f:
        shutil.copyfileobj(randomlogo.raw, f) 
        return url
def logo2(text):
  logo = [f"https://api2.haji-api.ir/ephoto360/?type=text&id=1&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=2&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=3&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=4&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=5&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=6&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=7&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=8&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=9&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=10&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=11&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=12&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=13&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=14&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=15&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=16&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=17&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=18&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=19&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=20&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=21&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=22&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=23&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=24&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=25&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=26&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=27&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=28&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=29&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=30&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=31&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=32&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=33&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=34&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=35&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=36&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=37&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=38&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=39&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=40&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=41&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=42&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=43&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=44&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=45&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=46&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=47&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=48&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=49&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=50&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=51&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=52&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=53&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=54&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=55&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=56&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=57&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=58&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=59&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=60&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=61&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=62&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=63&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=64&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=65&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=66&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=67&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=68&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=69&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=70&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=71&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=72&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=73&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=74&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=75&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=76&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=77&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=78&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=79&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=80&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=81&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=82&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=83&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=84&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=85&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=86&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=87&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=88&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=89&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=90&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=91&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=92&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=93&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=94&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=95&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=96&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=97&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=98&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=99&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=100&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=101&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=102&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=104&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=103&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=105&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=106&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=107&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=108&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=109&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=110&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=111&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=112&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=113&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=114&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=115&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=116&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=117&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=118&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=119&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=120&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=121&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=122&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=123&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=124&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=125&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=126&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=127&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=128&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=129&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=130&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=131&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=132&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=133&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=134&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=135&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=136&text={text}",f"https://api2.haji-api.ir/ephoto360/?type=text&id=137&text={text}"]
  url = random.choice(logo)
  randomlogo = get(url, stream = True)
  if randomlogo.status_code == 200:
    with open("logo.png",'wb') as f:
        shutil.copyfileobj(randomlogo.raw, f) 
        return url
def logo3(text ,tem):
  url = f"https://api2.haji-api.ir/ephoto360/?type=text&id={tem}&text={text}"
  logo = get(url, stream = True)
  if logo.status_code == 200:
    with open("logo.png",'wb') as f:
        shutil.copyfileobj(logo.raw, f) 
        return url

def logo4(text ,tem):
  url = f"https://api.fasttube.ir/image/?text={text}&limit={tem}"
  logo = get(url, stream = True)
  if logo.status_code == 200:
    with open("logo.jpg",'wb') as f:
        shutil.copyfileobj(logo.raw, f) 
        return url 
        
def gif(text):
  gif = [f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=alien-glow-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=flash-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=shake-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=highlight-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=blue-fire&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=burn-in-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=inner-fire-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=glitter-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=flaming-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120",f"https://www.flamingtext.com/net-fu/proxy_form.cgi?imageoutput=true&script=memories-anim-logo&text={text}&doScale=true&scaleWidth=240&scaleHeight=120"]
  url = random.choice(gif)
  randomgif = get(url, stream = True)
  if randomgif.status_code == 200:
    with open("proxy_form.gif", 'wb') as f:
      shutil.copyfileobj(randomgif.raw, f)
      return url
def get_size(bytes, suffix="B"):
    factor = 1024
    for unit in ["", "K", "M", "G", "T", "P"]:
        if bytes < factor:
            return f"{bytes:.2f}{unit}{suffix}"
        bytes /= factor

def if_not_exist_creat(filename):
 if not os.path.isfile(filename):
  with open(filename , "w") as f:
   f.write("")
   f.close() 
def write(filename , text):
 with open(filename , "w", encoding="utf-8") as f:
   f.write(text)
   f.close() 
def write_a(filename , text):
 with open(filename , "a", encoding="utf-8") as f:
   f.write(text)
   f.close() 
def read(filename):
 with open(filename , "r", encoding="utf-8") as f:
   return f.read()
def json_read(filename):
 with open(filename , "r", encoding="utf-8") as f:
   return json.load(f)
   
def run_codi(lang , code):
    a = Rextester(lang , code)
    k = a.stats;k = k.replace(",","")
    run_time = k.index("running time:")
    cpu_time = k.index("cpu time:")
    used_memory = k.index("memory peak:")
    kossher = k.index("absolute service time")
    mamad = f"**Result**: \n`{a.result if a.result else '--'}`{f'**ERROR**: `{a.errors}`' if a.errors else ''}\n**State**:\n__{k[run_time:cpu_time]}\n{k[cpu_time:used_memory]}\n{k[used_memory:kossher]}__"
    return mamad

def moon_or_sun():
  a = datetime.now(timezone("Asia/Tehran")).strftime("%H");a = int(a)
  if a in[20,21,22,23,00,1,2,3,4,5]:
    b = "🌑"
  elif a in[6,7]:
    b = "🌒"
  elif a in[8,9,10,11]:
    b = "🌔"
  elif a in[12,13,14,15,16,17]:
    b = "🌕"
  elif a in[18,19]:
    b = "🌒"
  return b

def love_emoji():
  l = ["❤️","🧡","💛","💚","💙","💜","🖤","🤍"]
  lo = choice(l)
  return lo
  

def dast_del(text):
  if text.privileges:
     if text.privileges.can_delete_messages == True:
        return True

def have_sec(t):
  if len(t.split(":")) == 3:
    return str(t)
  else:
    return str(t) +":00"
    
def read_note(url):
  b = str()   
  for j in url.split("\n"):
    try: 
      n = pn.read_note(j) 
      b += f"\n({j}) --> ({n})"
    except Exception as er:
      b += f"\n({j}) --> ({er})"
  return b


