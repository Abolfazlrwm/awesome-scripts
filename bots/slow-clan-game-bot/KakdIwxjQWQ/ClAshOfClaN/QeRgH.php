<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
if(!$ok)
{
	exit(header("location: https://snowyPage.xyz/DcJsjxjdbd"));
	}
$admin = array('[*[ADMIN]*]','[*[ADMIN]*]');//آیدی عددی خود را وارد کنید
function QuBnM($method,$datas=[]){
    $url = "https://api.telegram.org/bot".QUYSVB."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
 
function download($file,$urll){
  $url  = $urll;
  $path = $file;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  curl_close($ch);
  file_put_contents($path, $data);
}
function ph($method,$datas=[]){
    $url = "https://api.telegram.org/bot".QUYSVB."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function api($method,$datas=[]){
    $url = "https://api.telegram.org/bot".QUYSVB."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
 function SM($chatID)
{
	$tab = json_decode(file_get_contents("../../lib/Jsons/tab.json"),true);
	if($tab['type'] == 'photo')
	{
		bot('sendphoto',['chat_id'=>$chatID,'photo'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'file')
	{
		bot('sendDocument',['chat_id'=>$chatID,'document'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'video')
	{
		bot('SendVideo',['chat_id'=>$chatID,'video'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'music')
	{
		bot('SendAudio',['chat_id'=>$chatID,'audio'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'sticker')
	{
		bot('SendSticker',['chat_id'=>$chatID,'sticker'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else if($tab['type'] == 'voice')
	{
		bot('SendVoice',['chat_id'=>$chatID,'voice'=>$tab["msgid"],'caption'=>$tab["text"],'reply_markup'=>$tab['reply_markup']]);
	}
	else
	{
		if($tab['reply_markup'] != null)
		{
			bot('SendMessage',['chat_id'=>$chatID,'text'=>$tab['text'],'reply_markup'=>$tab['reply_markup']]);
		}
		else
		{
			bot('SendMessage',['chat_id'=>$chatID,'text'=>$tab['text']]);
		}
	}
}
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".QUYSVB."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function getRanks($file){
   $users = scandir('data/');
   $users = array_diff($users,[".",".."]);
   $coins =[];
   foreach($users as $user){
    $coin = json_decode(file_get_contents('data/'.$user.'/'.$user.'.json'),true)["$file"];
    $coins[$user] = $coin;
}
   arsort($coins);
   foreach($coins as $key => $user){
   $list[] = array('user'=>$key,'coins'=>$coins[$key]);
   } 
   return $list;
}
function ufile($f){
$content = (int)  file_get_contents($f);
if($content > 1) file_put_contents($f,($content+1));
}
$content = file_get_contents("php://input");
$u = json_decode($content, true);
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$es = $message->sticker->emoji;
$first_name = $message->chat->first_name;
$LASTNAME = $message->chat->last_name;
$USERNAME = $message->chat->username;
$chat_id = isset($update->callback_query->message->chat->id)?$update->callback_query->message->chat->id:$update->message->chat->id;
$fadmin = $message->from->id;
$MyCoIn1 = isset($message->text)?$message->text:$update->callback_query->data;
$url_bot='https://telegram.me/ID';
$user_bot='ID';
$inname = $update->inline_query->from->first_name;
$intext = $update->inline_query->query;
$MyCoInmessage = isset($update->message->text)?$update->message->text:'';
$idzormajmoe = str_replace("/start ","",$MyCoInmessage);
$message_id2 = isset($update->callback_query->message->message_id)?$update->callback_query->message->message_id:null;
$tc = $update->message->chat->type;
 