<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
* Title: سورس ربات تلگرامی دانلود از پینترسته
* Developer: Mr Kigga
* Telegram: t.me/Mr_Kigga
* Channel: t.me/pulseXcode
*
* توجه: این سورس حق نشر دارد و بدون ذکر تگ سازنده غیرقانونی است.
*/

include 'config.php';
include 'functions.php';
include 'update.php';

$user = user($pdo,$chat_id);
$Channels = getAllChannels($pdo);
$admins = getAllAdmins($pdo);
$help_keyboard = json_encode([
				'inline_keyboard' => [
		[createInlineKeyboardButton('راهنما 🔗', 'help')]
		],
		]);
		
$admin_keyboard = json_encode(['keyboard' => [
	[['text' => "بخش اعلان | 📡"],['text' => "تنظیمات ربات | 💠"]],
	[['text' => "بخش ادمین ها | 🫀"],['text' => "بخش چنل ها | 🔗"]],
	[['text' => "برگشت به صفحه اصلی | 🔙"]],
], 'resize_keyboard' => true,
]);
$add_admin = json_encode(['keyboard' => [
	[['text' => "حذف ادمین |  ❌"],['text' => "افزودن ادمین |  ✅"]],
	[['text' => "لیست ادمین ها |  💠"]],
	[['text' => "برگشت به منوی اصلی | 🔙"]]
], 'resize_keyboard' => true,
]);

$add_channel = json_encode(['keyboard' => [
	[['text' => "حذف کانال | ❌"], ['text' => "افزودن کانال | ➕"]],
	[['text' => "لیست کانال‌ها | 📋"]],
	[['text' => "برگشت به منوی اصلی | 🔙"]]
], 'resize_keyboard' => true]);

$elan_keyboard = json_encode(['keyboard' => [
	[['text' => "ارسال به کاربران | 🔔"],['text' => 'فوروارد به کاربران | 🔔']],
	[['text' => "ارسال به گروه ها | 🔊"],['text' => "فوروارد به گروه ها | 🔊"]],
	[['text' => "برگشت به منوی اصلی | 🔙"]],
], 'resize_keyboard' => true,
]);

$setting_keyboard = json_encode(['keyboard' => [
	[['text' => "تنظیم محدودیت | 🚷"]],
	[['text' => "برگشت به منوی اصلی | 🔙"]]
], 'resize_keyboard' => true]);


$support = json_encode([
	'inline_keyboard' => [
		[['text' => "پشتیبانی ✅", 'url' => 'https://t.me/'.'Mr_Kigga']],
	],
]);
$team = json_encode([
	'inline_keyboard' => [
		[['text' => "👨‍💻Pulse Code", 'url' => 'https://t.me/'.'PulseXCode']],
	],
]);

$join_group = json_encode([
	'inline_keyboard' => [
		[['text' => "🆕 افزودن ربات به گروه", 'url' => 'https://t.me/PinterestScraperBot?startgroup=new']],
		[['text' => "پشتیبانی ✅", 'url' => 'https://t.me/'.'Mr_Kigga']]
	],
]);

if ($data == 'help') {
    $user_status = getChatMember($chat_id, $from_id)['result']['status'] ?? '';
    $admin_statuses = ['administrator', 'creator'];
    
    if (in_array($user_status, $admin_statuses)) {
        bot('editMessageText',[
		'chat_id' => $chat_id,
		'text' => "# راهنمای ربات دانلودر پینترست 📌✨

## 🔹 مرحله اول: ادمین کردن ربات
برای استفاده از ربات، **حتما باید آن را در گروه ادمین کنید**:
1. به تنظیمات گروه بروید
2. روی «مدیران» کلیک کنید
3. ربات را انتخاب و به عنوان ادمین اضافه کنید
4. دسترسی «ارسال پیام» را فعال کنید

✅ پس از ادمین شدن، ربات به صورت خودکار پیام تایید ارسال می‌کند!

---

## 🔹 نحوه استفاده از ربات
کافیست **هر یک از لینک‌های معتبر پینترست** را برای ربات ارسال کنید:

### نمونه لینک‌های قابل قبول:
1. پست معمولی:
   ```
   https://www.pinterest.com/pin/123456789012345678/
   ```

2. پین ویدیویی:
   ```
   https://pin.it/4XzY9qT
   ```

---

## 🔹 قابلیت‌های ربات
- دانلود **عکس‌ها** با کیفیت اصلی
- دانلود **ویدیوها** بدون افت کیفیت
- پشتیبانی از **لینک‌های کوتاه شده**
- پاسخگویی خودکار در گروه‌ها و چت خصوصی

---

## 🔹 نکات مهم
❌ ربات در گروه‌های غیرعمومی کار نمی‌کند  
❌ بدون ادمین بودن، ربات نمی‌تواند پاسخ دهد  
✅ برای استفاده در چت خصوصی نیاز به ادمین شدن نیست  

هر سوالی دارید می‌توانید از طریق پشتیبانی با ما در ارتباط باشید! 🤖💙

💠 | @PulseXCode",
		'message_id' => $message_id,
		'parse_mode' => 'html',
		'reply_markup' => $support
	]);
    } else {
        answerCallbackQuery($data_id, "❌ فقط ادمین‌های گروه می‌توانند از این بخش استفاده کنند");
    }
}
if ($data == 'join') {
	if (check_join($from_id) == true) {
		$message_join = "عضویت شما تایید شد ✅";
		answercallbackquery($data_id, $message_join);
		$message_welcome = "<b>کاربر</b> <a href=\"tg://user?id=$from_id\">$first_name</a> <b>به ربات پینترست دانلودر خوش آمدید🌹</b>\n\n<b>برای دانلود از پینترست فقط کافیه لینک مدیا رو ارسال کنید. با ربات ما می‌تونی گیف، عکس، فیلم رو از پینترست با کیفیت بالا دانلود کنی 😊</b>";
		send($from_id, $message_welcome, null, 'html', $join_group);
	} else {
		$message_join = "❌ هنوز داخل کانال ها عضو نیستی";
		answercallbackquery($data_id, $message_join);
	}
}
////privte////
if ($tc == 'private') {
	
	if ($text == "/start") {

	sendChatAction($from_id,'typing');
	
	if (check_join($from_id) == true) {
		$message_velcome = "<b>کاربر</b> <a href=\"tg://user?id=$from_id\">$first_name</a> <b>به ربات پینترست دانلودر خوش آمدید🌹</b>\n\n<b>برای دانلود از پینترست فقط کافیه لینک مدیا رو ارسال کنید. با ربات ما می‌تونی گیف، عکس، فیلم رو از پینترست با کیفیت بالا دانلود کنی 😊</b>";
		send($from_id,$message_velcome,$message_id,'html',$join_group);
	} else {
		is_join($from_id);
	}
	if (!userExists($pdo,$from_id)) {
		$data = [
			'id' => $from_id,
			'step' => 'hi',
			'username' => $username ?? null,
			'first_name' => $first_name,
			'last_name' => $last_name ?? null
		];
		saveUser($pdo, $data);
	}
	update($pdo,$from_id,'step','start');
}

if (preg_match('/https?:\/\/(www\.)?(pin\.it\/[^\s]+|pinterest\.com\/pin\/[^\s]+)/i', $text)) {
    list($allowed, $timeend) = checkTimeLimit($pdo, $from_id);
    sendChatAction($from_id, 'typing');
    
    if ($allowed == true) {
        sendReaction($from_id, $message_id, '💯');
        
        $message_find_link = "<b>درحال دریافت مدیا ... ✅ </b>";
        $send = send($from_id, $message_find_link, $message_id, 'HTML');
        
        $download_url = getPinterestMedia($text);
        $message_download_media = "<b>مدیا شما با موفیقت دانلود شد ✅</b>\n\n<a href=\"$download_url\">🔗 لینک مستقیم 🔗</a>";
        send($from_id, $message_download_media, $message_id, 'HTML', $team);
        
        deleteMessage($from_id, $send);
        updateLastRequest($pdo, $from_id);
    } else {
        // دریافت زمان کوئدون برای نمایش در پیام
        $stmt = $pdo->query("SELECT setting_value FROM bot_settings WHERE setting_name = 'request_cooldown'");
        $cooldown = (int)$stmt->fetchColumn();
        
        $message_not_allowed = "شما اجازه دارید هر $cooldown ثانیه درخواست بزنید ❗️\nتایم باقی مانده تا اتمام محدودیت <b>$timeend ثانیه</b> 💠";
        send($from_id, $message_not_allowed, $message_id, 'HTML');
    }
}


}
////privte////
////supergroup////
if ($tc == 'group' || $tc == 'supergroup') {
	if (preg_match('/https?:\/\/(www\.)?(pin\.it\/[^\s]+|pinterest\.com\/pin\/[^\s]+)/i', $text)) {
	 
	sendChatAction($chat_id,'typing');
	
	sendReaction($chat_id,$message_id,'💯');
	
	$message_find_link = "<b>درحال دریافت مدیا ... ✅ </b>";
	$send = send($chat_id,$message_find_link,$message_id,'HTML');
	
	$download_url = getPinterestMedia($text);
	$message_download_media = "<b>مدیا شما با موفیقت دانلود شد ✅</b>\n\n<a href=\"$download_url\">🔗 لینک مستقیم 🔗</a>";
	send($chat_id,$message_download_media,$message_id,'HTML',$team);
	
	deleteMessage($chat_id,$send);
	
}
	foreach ($message->new_chat_members as $momber)
	{
		if ($momber->is_bot && $momber->username == me()['result']['username']) {
			if (is_bot_admin($chat_id,me()['result']['id'])) {
			saveGroup($pdo,$message->chat->id,$message->chat->title,$message->chat->type);
			$message_velcome_group = "با موفقیت ادمین گروه شدم✅\n\nحالا فعالم و پاسخگو درخواست های شمام ، الان میتونم با سرعت موشک برات از پینترست دانلود کنم🙂❤️\n\nالان میتونی از خدمات من استفاده کنی 🥹 اگرم راهنمایی نیاز داشتی دکمه راهنما زیر رو بزن 👇🏻";
			send($chat_id,$message_velcome_group,null,'HTML',$help_keyboard);
			
			} else {
				
			$message_velcome_group = "<b>مرسی که به گروه خوشگلتون دعوتم کردی ولی هنوز نمیتونم کاری براتونم انجام بدم😭 \nباید حتما ادمین باشم ، منو ادمین گروه زیباتون بکنید تا بتونم جواب درخواست هاتون بدم ❤️🙂\nاگر هم راهنمایی نیاز داشتی میتونی بزنی روی دکمه زیر 👇🏻</b>";
			send($chat_id,$message_velcome_group,null,'HTML',$help_keyboard);
			
			}
		}
	}

}
if (isset($update->my_chat_member)) {
    $chat_id = $update->my_chat_member->chat->id;
    $title = $update->my_chat_member->chat->title;
    $type = $update->my_chat_member->chat->type;
    $new_status = $update->my_chat_member->new_chat_member->status;
    $old_status = $update->my_chat_member->old_chat_member->status;
    saveGroup($pdo,$chat_id,$title,$type);
    if ($old_status == "member" && $new_status == "administrator") {
    	$message_velcome_group = "با موفقیت ادمین گروه شدم✅\n\nحالا فعالم و پاسخگو درخواست های شمام ، الان میتونم با سرعت موشک برات از پینترست دانلود کنم🙂❤️\n\nالان میتونی از خدمات من استفاده کنی 🥹 اگرم راهنمایی نیاز داشتی دکمه راهنما زیر رو بزن 👇🏻";
		send($chat_id,$message_velcome_group,null,'HTML',$help_keyboard);
    }
}

////supergroup////
////panel admin////

if ($text == "برگشت به صفحه اصلی | 🔙" && isAdmin()) {
	$message_back_asli = "مدیر عزیز شما به صفحه اصلی ربات برگشتید ✅\n\n👇🏻";
	send($from_id,$message_back_asli,null,'HTML',json_encode(['remove_keyboard' => true]));
	
	$message_velcome = "<b>کاربر</b> <a href=\"tg://user?id=$from_id\">$first_name</a> <b>به ربات پینترست دانلودر خوش آمدید🌹</b>\n\n<b>برای دانلود از پینترست فقط کافیه لینک مدیا رو ارسال کنید. با ربات ما می‌تونی گیف، عکس، فیلم رو از پینترست با کیفیت بالا دانلود کنی 😊</b>";
		send($from_id,$message_velcome,$message_id,'html',$join_group);
		update($pdo,$from_id,'step','backed_main');
}

if ($text == "برگشت به منوی اصلی | 🔙" && isAdmin()) {
	$message_back_asli = "مدیر عزیز شما به منوی اصلی برگشتید ✅\n\nبرای ادامه روی یکی از دکمه های زیر کلیک کنید 👇🏻";
	send($from_id,$message_back_asli,null,'HTML',$admin_keyboard);
	update($pdo,$from_id,'step','backed');
}

if ($text == "/panel" && isAdmin()) {
	$stmt = $pdo->query("SELECT * FROM `users`");
	$count_users = $stmt->rowCount();
	$stmt = $pdo->query("SELECT * FROM `groups`"); // با backtick احاطه شود
	$count_groups = $stmt->rowCount();
	$message_panel_admin = "سلام مدیر عزیز به پنل مدیریت من خوش اومدی ✅\n\n📡 - آمار ربات تا کنون :
💠 | تعداد عضو : %s
🔗 | تعداد گروه : %s \n\n⏳تاریخ و ساعت - %s | %s";
$data = [
	$count_users,
	$count_groups,
	get_Date(),
	get_Time()
];
$message_panel_admin = vsprintf($message_panel_admin, $data);

	send($from_id,$message_panel_admin,null,'HTML',$admin_keyboard);
}

if ($text == "بخش اعلان | 📡" || $text == "برگشت به منوی اعلان | 🔙" && isAdmin()) {
	$message_elan = "به بخش اعلان خوش آمدید مدیر گرامی ✅\n\nبرای ادامه روی یکی از دکمه های زیر کلیک کنید 👇🏻";
	send($from_id,$message_elan,null,'HTML',$elan_keyboard);
	update($pdo,$from_id,'step','none');
}


if ($text == "بخش ادمین ها | 🫀" || $text == "برگشت به ادمین ها | 🔙" && isAdmin()) {
	$message_admin_panel = "به بخش ادمین ها خوش آمدید مدیر گرامی ✅\n\nبرای ادامه روی یکی از دکمه های زیر کلیک کنید 👇🏻";
	send($from_id,$message_admin_panel,null,'HTML',$add_admin);
	update($pdo,$from_id,'step','none');
}

if ($text == "بخش چنل ها | 🔗" || $text == "برگشت به چنل ها | 🔙" && isAdmin()) {
	$message_admin_panel = "به بخش چنل ها خوش آمدید مدیر گرامی ✅\n\nبرای ادامه روی یکی از دکمه های زیر کلیک کنید 👇🏻";
	send($from_id,$message_admin_panel,null,'HTML',$add_channel);
	update($pdo,$from_id,'step','none');
}

if ($text == "تنظیمات ربات | 💠" || $text == "برگشت به منوی تنظیمات | 🔙" && isAdmin()) {
	$message_elan = "به بخش تنظیمات خوش آمدید مدیر گرامی ✅\n\nبرای ادامه روی یکی از دکمه های زیر کلیک کنید 👇🏻";
	send($from_id,$message_elan,null,'HTML',$setting_keyboard);
	update($pdo,$from_id,'step','none');
}


////panel admin////
if($text == 'ارسال به کاربران | 🔔' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس باشد]  همچنین میتوانید رسانه را همراه با کشپن [متن چسپیده به رسانه ارسال کنید]",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به منوی اعلان | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','send_simple');
	} 
	
	if($text == 'فوروارد به کاربران | 🔔' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به منوی اعلان | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','send_forward');
	}
	
	if($text == 'ارسال به گروه ها | 🔊' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس باشد]  همچنین میتوانید رسانه را همراه با کشپن [متن چسپیده به رسانه ارسال کنید]",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به منوی اعلان | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','send_simple_group');
	} 
	
	if($text == 'فوروارد به گروه ها | 🔊' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به منوی اعلان | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','send_forward_group');
	}
	
	if($text == 'افزودن ادمین |  ✅' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا ایدی عددی ادمین جدید ارسال کنید . ",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به ادمین ها | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','add_admin');
	} 
	
	if($text == 'حذف ادمین |  ❌' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفا ایدی عددی ادمین برای حذف ارسال کنید . ",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به ادمین ها | 🔙"]]
				],
				'resize_keyboard' => true
			])
		]);
		update($pdo,$from_id,'step','delete_admin');
	} 
	
	if($text == 'لیست ادمین ها |  💠' and $tc == 'private' && isAdmin()) {
		$adminList = getAllAdmins($pdo);
		$adminList = array_filter($adminList, function($id) use ($dev) {
			return $id != $dev;
			
		});
		if (empty($adminList)) {
			bot('sendMessage', [
				'chat_id' => $from_id,
				'text' => "❌ لیست ادمین‌ها خالیه!"
			]);
} else {
    $text = "📋 لیست ادمین‌های فعلی:\n\n";
    $i = 1;
    foreach ($adminList as $adminId) {
        $number = str_pad($i++, 2, '0', STR_PAD_LEFT); // شماره‌گذاری 01, 02, ...
        $text .= "🔹 $number ➤ <code>$adminId</code>\n";
    }

    bot('sendMessage', [
        'chat_id' => $from_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ]);
}
		update($pdo,$from_id,'step','none');
	} 
	
	//////
	
	if($text == 'افزودن کانال | ➕' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفاً تگ چنلی که میخاین روی جوین اجباری تنظیم شه رو ارسال کنید حتما بدون @ باشه ! \n\n❗️مثال : pulseXCode",
			'reply_markup' => json_encode([
				'keyboard' => [
				[['text' => "برگشت به چنل ها | 🔙"]]
			],
			'resize_keyboard' => true
			])
		]);
		update($pdo, $from_id, 'step', 'add_channel');
	}
	
	if($text == 'حذف کانال | ❌' and $tc == 'private' && isAdmin()) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "📍 لطفاً تگ چنلی که میخاین از روی جوین اجباری حذف شه رو ارسال کنید حتما بدون @ باشه ! \n\n❗️مثال : pulseXCode",
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به چنل ها | 🔙"]]
					],
			'resize_keyboard' => true
		])
	]);
	update($pdo, $from_id, 'step', 'delete_channel');
		
	}
	
	if($text == 'لیست کانال‌ها | 📋' and $tc == 'private' && isAdmin()) {
	$channels = getAllChannels($pdo);

	if (empty($channels)) {
		bot('sendMessage', [
			'chat_id' => $from_id,
			'text' => "❌ لیست کانال‌ها خالیه!"
		]);
	} else {
		$message = "📋 لیست کانال‌های ثبت‌شده:\n\n";
		$i = 1;
		foreach ($channels as $link) {
			$num = str_pad($i++, 2, '0', STR_PAD_LEFT);
			$message .= "🔹 $num ➤ <code>$link</code>\n";
		}
		bot('sendMessage', [
			'chat_id' => $from_id,
			'text' => $message,
			'parse_mode' => 'HTML'
		]);
	}
	update($pdo, $from_id, 'step', 'none');
}
	////
	////////////////
	if ($user['step'] == 'send_simple' && $text != "برگشت به منوی اعلان | 🔙") {
		$photo = $message->photo[count($message->photo)-1]->file_id;
		$caption = $update->message->caption;
		if (isset($message->photo)) {
			$pdo->exec("UPDATE `send` SET `target` = 'users' , `type_send` = 'simple' , `message` = '$caption' , `media` = '$photo' LIMIT 1");
		} else {
			$pdo->exec("UPDATE `send` SET target = 'users' , `type_send` = 'simple' , `message` = '$text' LIMIT 1");
		}
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✔️ پیام شما با موفقیت برای ارسال همگانی تنظیم شد",
		]);
		update($pdo,$from_id,'step','none');
	} 
	
	if($user['step'] == 'send_forward' && $text != "برگشت به منوی اعلان | 🔙") {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✔️ پیام شما با موفقیت به عنوان فوروارد همگانی تنظیم شد",
		]);
		$pdo->exec("UPDATE `send` SET `target` = 'users' , `type_send` = 'forward' , `chat_id` = '$from_id' , `message` = '$message_id' LIMIT 1");
		update($pdo,$from_id,'step','none');
		
	} 
	
	if ($user['step'] == 'send_simple_group' && $text != "برگشت به منوی اعلان | 🔙") {
		$photo = $message->photo[count($message->photo)-1]->file_id;
		$caption = $update->message->caption;
		if (isset($message->photo)) {
			$pdo->exec("UPDATE `send` SET `target` = 'groups' , `type_send` = 'simple' , `message` = '$caption' , `media` = '$photo' LIMIT 1");
		} else {
			$pdo->exec("UPDATE `send` SET target = 'groups' , `type_send` = 'simple' , `message` = '$text' LIMIT 1");
		}
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✔️ پیام شما با موفقیت برای ارسال همگانی تنظیم شد",
		]);
		update($pdo,$from_id,'step','none');
	} 
	
	if($user['step'] == 'send_forward_group' && $text != "برگشت به منوی اعلان | 🔙") {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✔️ پیام شما با موفقیت به عنوان فوروارد همگانی تنظیم شد",
		]);
		$pdo->exec("UPDATE `send` SET `target` = 'groups' , `type_send` = 'forward' , `chat_id` = '$from_id' , `message` = '$message_id' LIMIT 1");
		update($pdo,$from_id,'step','none');
		
	} 
	
	if($user['step'] == 'add_admin' && $text != "برگشت به ادمین ها | 🔙") {
		if (isAdmins($pdo,$text)) {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "❌ این کاربر از قبل ادمینه ."
		]);
		} else {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✅️ ادمین جدید با موفیقت اضافه شد ."
		]);
		addAdmin($pdo,$text);
		}
		update($pdo,$from_id,'step','none');
		
	} 
	
	if($user['step'] == 'delete_admin' && $text != "برگشت به ادمین ها | 🔙") {
		if (isAdmins($pdo,$text)) {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✅️ با موفیقت از لیست ادمین ها حذف شد ."
		]);
		deleteAdmin($pdo,$text);
		} else {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "❌ کاربر داخل لیست ادمین ها وجود نداره ."
		]);
		}
		update($pdo,$from_id,'step','none');
		
	} 
	if($user['step'] == 'add_channel' && $text != "برگشت به چنل ها | 🔙") {
	if (isChannels($pdo, $text)) {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "❌ این چنل از قبل سیو شده ."
		]);
	} else {
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✅️ کانال با موفقیت ثبت شد."
		]);
		addChannel($pdo, $text);
	}
	update($pdo, $from_id, 'step', 'none');
	}
	
	if($user['step'] == 'delete_channel' && $text != "برگشت به چنل ها | 🔙") {
		if (isChannels($pdo,$text)) {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "✅️ ما موفیقت چنل از جوین اجباری ربات حذف شد ."
			]);
		deleteChannel($pdo,$text);
		} else {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => "❌ چنل داخل لیست جوین اجباری ها نبود ."
			]);
		}
		update($pdo,$from_id,'step','none');
		
	}
	
	if($text == 'تنظیم محدودیت | 🚷' and $tc == 'private' && isAdmin()) {
	$getCooldownTime = getCooldownTime($pdo);
	$mesaage_getCooldownTime = "❗️تایم فعلی : $getCooldownTime\n\n💠 - تایم مورد نظر ارسال کنید ، توجه داشته باشید که باید به ثانیه باشه بیشتر از 5 ثانیه کمتر از 3600 ثانیه .";
	
	bot('sendMessage', [
			'chat_id' => $from_id,
			'text' => $mesaage_getCooldownTime,
			'parse_mode' => 'HTML',
			'reply_markup' => json_encode([
				'keyboard' => [
					[['text' => "برگشت به منوی تنظیمات | 🔙"]]
					],
			'resize_keyboard' => true
		])
		]);
	update($pdo, $from_id, 'step', 'set_getCooldownTime');
}

if($user['step'] == 'set_getCooldownTime' && $text != "برگشت به منوی تنظیمات | 🔙") {
	
	if (setCooldownTime($pdo,$text)) {
		$getCooldownTime = getCooldownTime($pdo);
	
		$mesaage_getCooldownTime = "تایم با موفیقت تنظیم شد ✅\n\n💠 -‌ تایم ثبت شده جدید : $getCooldownTime";
		
		bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => $mesaage_getCooldownTime
		]);
	} else {
			bot('sendmessage', [
			'chat_id' => $from_id,
			'text' => 'مشکلی پیش اومده پیشنهاد میشه به دیتابیس سر بزنی !'
		]);
	}
	update($pdo,$from_id,'step','none');
	}
	
	
/*
* پایان سورس ربات تلگرامی دانلود از پینترسته
* توسعه‌دهنده: Mr Kigga
* تلگرام: t.me/Mr_Kigga
* چنل: t.me/pulseXcode
*
* هشدار: هرگونه کپی‌برداری یا انتشار بدون ذکر منبع پیگرد قانونی دارد.
* این سورس متعلق به سازنده اصلی است و هیچ‌کس حق ندارد آن را به نام خود منتشر کند.
*/
?>