# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import pymysql.cursors 

class Sql:
	def __init__(self) -> None :
		self.connection = pymysql.connect(
			host='localhost',
			user='tnvyalvl_gos', #masihhhh_gostarde
			password='F@G]=E+P!.=,', #feuEE6YM5m=3
			database='tnvyalvl_gos', #masihhhh_sanaz
			cursorclass=pymysql.cursors.DictCursor)
		
	def create(self) -> bool :
		self.cursor = self.connection.cursor()
		
		# CHANNELS - id , user_id , channel_id
		try:
			self.cursor.execute("CREATE TABLE `channels` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`channel_id` varchar(255) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass

        # users - id , user_id , step 
		try:
			self.cursor.execute("CREATE TABLE `users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`step` varchar(255) COLLATE utf8_bin NOT NULL,PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		# WITHRAW_REQ - id , user_id , name , username , amount
		try:
			self.cursor.execute("CREATE TABLE `withraw_req` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`name` varchar(255) COLLATE utf8_bin NOT NULL,`username` varchar(255) COLLATE utf8_bin NOT NULL,`amount` int(15) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
        # LOGIN_REQ - id , user_id , name , username , channels
		try:
			self.cursor.execute("CREATE TABLE `login_req` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`name` varchar(255) COLLATE utf8_bin NOT NULL,`username` varchar(255) COLLATE utf8_bin NOT NULL,`channels` varchar(255) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		
        # ACC MEMBERS - id , user_id , balance
		try:
			self.cursor.execute("CREATE TABLE `acc_members` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`balance` int(11) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		# AD - id , channel_id , price , code , tag , banner
		try:
			self.cursor.execute("CREATE TABLE `ad` ( `id` int(11) NOT NULL AUTO_INCREMENT, `channel_id` varchar(255) COLLATE utf8_bin NOT NULL,`price` int(11) COLLATE utf8_bin NOT NULL,`code` varchar(255) COLLATE utf8_bin NOT NULL,`tag` varchar(255) COLLATE utf8_bin NOT NULL,`banner` varchar(255) COLLATE utf8_bin NOT NULL,PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		# LINKS - id , user_id , link , code
		try:
			self.cursor.execute("CREATE TABLE `links` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`link` varchar(255) COLLATE utf8_bin NOT NULL,`code` varchar(255) COLLATE utf8_bin NOT NULL,PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		# TRON_REQ - id , user_id , name , username , amount , wallet
		try:
			self.cursor.execute("CREATE TABLE `tron_req` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`name` varchar(255) COLLATE utf8_bin NOT NULL,`username` varchar(255) COLLATE utf8_bin NOT NULL,`amount` int(15) COLLATE utf8_bin NOT NULL,`wallet` varchar(255) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass
		# TOMAN_REQ - id , user_id , name , username , amount , wallet
		try:
			self.cursor.execute("CREATE TABLE `toman_req` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` varchar(255) COLLATE utf8_bin NOT NULL,`name` varchar(255) COLLATE utf8_bin NOT NULL,`username` varchar(255) COLLATE utf8_bin NOT NULL,`amount` int(15) COLLATE utf8_bin NOT NULL,`wallet` varchar(255) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=1;")
			self.connection.commit()
		except:
			pass

		self.cursor.close()

	def remove_toman_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `toman_req` WHERE user_id = %s ",(user_id))
		self.connection.commit()

	def remove_tron_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `tron_req` WHERE user_id = %s ",(user_id))
		self.connection.commit()

	def get_wallet_toman_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `wallet` FROM `toman_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_amount_toman_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `amount` FROM `toman_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
	
	def get_username_toman_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `username` FROM `toman_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_all_toman_req(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `toman_req`")
		result = self.cursor.fetchall()
		self.cursor.close()
		return result

	def get_wallet_tron_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `wallet` FROM `tron_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_amount_tron_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `amount` FROM `tron_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
	
	def get_username_tron_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `username` FROM `tron_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_all_tron_req(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `tron_req`")
		result = self.cursor.fetchall()
		self.cursor.close()
		return result
	
	def add_toman_req(self,user_id,first_name,username,amount,wallet) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `toman_req` (`user_id`, `name` ,`username` , `amount` , `wallet`) VALUES (%s, %s , %s , %s, %s)",(user_id,'x',username,amount,wallet))
		self.connection.commit()
		self.cursor.close()

	def add_tron_req(self,user_id,first_name,username,amount,wallet) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `tron_req` (`user_id`, `name` ,`username` , `amount` , `wallet`) VALUES (%s, %s , %s , %s, %s)",(user_id,'x',username,amount,wallet))
		self.connection.commit()
		self.cursor.close()

	def update_channels_member(self,user_id,channel_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("UPDATE `channels` SET `channel_id` = %s WHERE `user_id` = %s",(channel_id,user_id))
		self.connection.commit()

	def add_channels_member(self,user_id,channel_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `channels` (`user_id`, `channel_id` ) VALUES (%s, %s)",(user_id,channel_id))
		self.connection.commit()

	def get_channels_member(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `channel_id` FROM `channels` WHERE `user_id` = %s",(user_id))
		result = self.cursor.fetchone()
		return result
	
	def get_hmg(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `users` ")
		result = self.cursor.fetchall()
		users = []
		for item in result:
			users.append(item['user_id'])
		return users
		
	def get_members(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `acc_members` WHERE `user_id` = %s ",(str(user_id)))
		result = self.cursor.fetchone()
		return result
		
	def get_acc_members(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `acc_members` WHERE `user_id` = %s",(user_id))
		result = self.cursor.fetchone()
		return result

	def get_user_for_hmg(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `acc_members` ")
		result = self.cursor.fetchall()
		users = []
		for item in result:
			users.append(item['user_id'])
		return users

	def delete_ad(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `ad` WHERE `code` = %s",(code))
		self.connection.commit()

	def delete_links(self,code,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `links` WHERE `code` = %s AND user_id = %s ",(code,user_id))
		self.connection.commit()

	def get_price(self,channel_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `price` FROM `ad` WHERE `channel_id` = %s",(channel_id))
		result = self.cursor.fetchone()
		return result['price']

	def remove_withraw_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `withraw_req` WHERE user_id = %s ",(user_id))
		self.connection.commit()

	def get_amount_withraw_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `amount` FROM `withraw_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_username_withraw_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `username` FROM `withraw_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def get_all_withraw_req(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `withraw_req`")
		result = self.cursor.fetchall()
		self.cursor.close()
		return result

	def add_withraw_req(self,user_id,first_name,username,amount) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `withraw_req` (`user_id`, `name` ,`username` , `amount`) VALUES (%s, %s , %s , %s)",(user_id,first_name,username,amount))
		self.connection.commit()
		self.cursor.close()
		
	def add_balance(self,money,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("UPDATE `acc_members` SET `balance` = %s WHERE `user_id` = %s",(money,user_id))
		self.connection.commit()

	def get_balance(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `balance` FROM `acc_members` WHERE `user_id` = %s",(user_id))
		result = self.cursor.fetchone()
		return result['balance']

	def get_all_ads(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `code` FROM `ad`")
		result = self.cursor.fetchall()
		links = []
		for i in result:
			links.append(i['code'])
		return links
		
	def get_user_id_by_link(self,link) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `links` WHERE `link` = %s",(link))
		result = self.cursor.fetchone()
		return result

	def get_all_links_by_code(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `link` FROM `links` WHERE `code` = %s",(code))
		links = []
		result = self.cursor.fetchall()
		for i in result:
			links.append(i['link'])
		return links

	def get_ad(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `channel_id` FROM `ad` WHERE `code` = %s ",(code))
		result = self.cursor.fetchone()
		return result

	def get_code_by_link(self,link) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `code` FROM `links` WHERE `link` = %s",(link))
		result = self.cursor.fetchone()
		return result

	def get_link(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `link` FROM `links` WHERE `user_id` = %s",(user_id))
		result = self.cursor.fetchall()
		links = []
		for i in result:
			links.append(i['link'])
		return links

	def check_geted_link(self,code,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `links` WHERE `code` = %s AND `user_id` = %s ",(code,user_id))
		result = self.cursor.fetchone()
		return result

	def add_link(self,user_id,link,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `links` (`user_id`, `link` , `code`) VALUES (%s, %s, %s)",(user_id,link,code))
		self.connection.commit()
		self.cursor.close()

	def get_channel_id_by_code(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `channel_id` FROM `ad` WHERE `code` = %s ",(code))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result

	def remove_login_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("DELETE FROM `login_req` WHERE user_id = %s ",(user_id))
		self.connection.commit()
		
	def get_banner_ad(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `banner` FROM `ad` WHERE `code` = %s ",(code))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
		
	def get_price_ad(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `price` FROM `ad` WHERE `code` = %s ",(code))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
		
	def get_tag_ad(self,code) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `tag` FROM `ad` WHERE `code` = %s ",(code))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
		
	def get_all_active_cods(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `code` FROM `ad`")
		result = self.cursor.fetchall()
		self.cursor.close()
		res = []
		for i in result:
			res.append(i)
		return res
		
	def add_ad(self,channel_id,price,code,tag,banner) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `ad` (`channel_id`, `price` , `code`, `tag` , `banner`) VALUES (%s, %s, %s, %s, %s)",(channel_id,int(price),code,tag,banner))
		self.connection.commit()
		self.cursor.close()

	def add_acc_member(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `acc_members` (`user_id`, `balance`) VALUES (%s, %s)",(user_id,0))
		self.connection.commit()
		self.cursor.close()
		
	def get_channel_login_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `channels` FROM `login_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
	
	def get_username_login_req(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `username` FROM `login_req` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
		
	def get_all_login_req(self) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `login_req`")
		result = self.cursor.fetchall()
		self.cursor.close()
		return result
		
	def get_step(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `step` FROM `users` WHERE `user_id` = %s ",(user_id))
		result = self.cursor.fetchone()
		return result

		
	def add_user(self,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `users` (`user_id`, `step` ) VALUES (%s, %s)",(user_id,'login'))
		self.connection.commit()
		self.cursor.close()
		
	def get_req_user(self , user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `login_req` WHERE `user_id` = %s ",(str(user_id)))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
		
	def get_acc_user(self , user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("SELECT `user_id` FROM `acc_members` WHERE `user_id` = %s ",(str(user_id)))
		result = self.cursor.fetchone()
		self.cursor.close()
		return result
	
	def update_step(self,step,user_id) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("UPDATE `users` SET `step` = %s WHERE `user_id` = %s",(step,user_id))
		self.connection.commit()
		self.cursor.close()
		
	def add_req_member(self,user_id,first_name,username,channels) -> bool :
		self.cursor = self.connection.cursor()
		self.cursor.execute("INSERT INTO `login_req` (`user_id`, `name` ,`username` , `channels`) VALUES (%s, %s , %s , %s)",(user_id,'x',username,channels))
		self.connection.commit()
		self.cursor.close()
		
