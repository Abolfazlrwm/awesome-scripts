<?php
/* ==============================
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

$update = json_decode(file_get_contents('php://input'));

if (isset($update->message)) {
	$message = $update->message;
	$from_id = $update->message->from->id;
	$chat_id = $update->message->chat->id;
	$tc = $update->message->chat->type;
	$text = $update->message->text;
	$first_name = $message->from->first_name;
	$last_name = $message->from->last_name;
	$username = $message->from->username;
	$message_id = $message->message_id;
	$photo = $update->message->photo;
	$caption = $update->message->caption;
	$contact = $update->message->contact;
	$contactnum = $update->message->contact->phone_number;
	$reply_text = $update->message->reply_to_message->text;
	$reply_id = $update->message->reply_to_message->message_id;
	$fwd_from_id = $update->message->forward_from_chat->id;
	$fwd_from_title = $update->message->forward_from_chat->title;
	$fwd_from_tc = $update->message->forward_from_chat->type;
	$fwd_msg_id = $update->message->forward_from_message_id;
	
}
if (isset($update->callback_query)) {
	$data = $update->callback_query->data;
	$data_id = $update->callback_query->id;
	$chat_id = $update->callback_query->message->chat->id;
	$from_id = $update->callback_query->from->id;
	$type = $update->callback_query->chat->type;
	$message_id = $update->callback_query->message->message_id;
	$first_name = $update->callback_query->from->first_name;
	$last_name = $update->callback_query->from->last_name;
	$username = $update->callback_query->from->username;
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