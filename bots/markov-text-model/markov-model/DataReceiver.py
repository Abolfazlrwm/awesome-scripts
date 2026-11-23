# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import ast,os,time,wikipedia
from bs4 import BeautifulSoup
os.chdir("Data")

#تابع استخراج متن با موضوع در ویکیپدیا 
def get_wiki(subject):
	try:
		wikipedia.set_lang('en')
		page = wikipedia.page(subject)
		return page.content
	except Exception as e:
		print("we have problem\n",e)

#فایل یا متن برای تبدیل به داده عددی
myfile = ""

digitsdata = "Digitsdata.txt"

if myfile.endswith(".html"):
	#استخراج متن خالص از فایل html
	with open(myfile,"r") as file:
		html_content = file.read()
		soup = BeautifulSoup(html_content,"html.parser")
		raw_text = soup.get_text()
		with open('t.txt',"w") as file:
			file.write(raw_text)

digits_word = ast.literal_eval(open("DigitsWord.txt","r").read())

#خوندن فایل متنی
def text(path:str):
	if path.startswith("/"):
		with open(f"{path}","r") as file:
			file_text = file.read()
			print(file_text,"\n\n")
			return file_text
	else:
		return path

#متغیر های دیتا دار
digit_data = []
reading_data = {}
rev_word = {v: k for k, v in digits_word.items()}

#tex = ""

#خواندن اطلاعات و تبدیل به ارقام
try:
	for words in get_wiki("Jesus"):
		try:
			converted_word = digits_word[words]
			digit_data.append(int(converted_word))
			reading_data[converted_word] = words
		#	tex += words
		except Exception as ee:
			print("Error",ee)
			continue
	digit_data.append(0)
except Exception as e:
	print("Information change Failed !",e)

#ذخیره لیست ارقام به داده متنی
try:
	with open(digitsdata,"r") as file:
		file_list = ast.literal_eval(file.read())
		with open(digitsdata,"w") as fl:
			file_list.extend(digit_data);fl.seek(0)
			fl.write(str(file_list))
except FileNotFoundError:
	print("expect running..")
	with open(digitsdata,"w") as file:
		file.write(str(digit_data))
		
