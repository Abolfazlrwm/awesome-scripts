<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

if ($text == $key['bol']){
if ($user['coin'] >= 100) {

        $bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
                      $conn->query("UPDATE user SET step = 'udodoyody' WHERE id = '$from_id' LIMIT 1");

    } else {
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>", $_backe);
    }
}
if ($text == $key['dar']) {
    if ($user['coin'] >= 100) {

        $bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
                      $conn->query("UPDATE user SET step = 'hsjjansjaj' WHERE id = '$from_id' LIMIT 1");

    } else {
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>", $_back);
    }
}


if ($text == $key['bas']) {
    if ($user['coin'] >= 100) {
        $bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
        $conn->query("UPDATE user SET step = 'taschhjson' WHERE id = '$from_id' LIMIT 1");
    } else {
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>", $_back);
    }
}
if ($text == $key['tas']) {
    if ($user['coin'] >= 100) {
        $bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
        $conn->query("UPDATE user SET step = 'tascionha' WHERE id = '$from_id' LIMIT 1");
    } else {
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>",$_back);
    }
}



if($user['step'] == "udodoyody"and !in_array($text,$arry)) {
if($user['coin'] >= $text){
    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>🎳بازی بولینگ:

1.تعداد مانع هایی که میوفتن؟.(ضریب: {$dataa['bol']})</b>", 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'صفر مانع'], ['text' => 'یک مانع']],
                    [['text' => 'سه مانع'], ['text' => 'چهار مانع']],
                    [['text' => 'پنج مانع'], ['text' => 'شش مانع']],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'idpidyxyo' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}
}

if($user['step'] == "idpidyxyo"and !in_array($text,$arry)) {
if($text == "صفر مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1){
   $bot->sendMessage($from_id, "✅شما برنده شدید.",$_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.",$_back); 
   $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}
}
}

if($user['step'] == "idpidyxyo"and !in_array($text,$arry)) {
if($text == "یک مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 2){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
  $bot->sendMessage($from_id, "⭕شما باختید.",$_back);   
    $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}
if($user['step'] == "idpidyxyo"and !in_array($text,$arry)) {
if($text == "سه مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 3){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
  
$bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}
if($user['step'] == "idpidyxyo" and !in_array($text,$arry)) {
if($text == "چهار مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 4){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{

 $bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}
if($user['step'] == "idpidyxyo" and !in_array($text,$arry)) {
if($text == "پنج مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 5){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}
if($user['step'] == "idpidyxyo" and !in_array($text,$arry)) {
if($text == "شش مانع"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'🎳');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bol'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}


if($user['step'] == "taschhjson" and !in_array($text,$arry)) {
if($user['coin'] >= $text){
    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>🏀بازی بسکتبال : 
        1. توپ شما وارد حلقه میشود و شرط را میبرید
        (ضریب: {$dataa['bas']})</b>", 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'گل میشود'], ['text' => 'گل نمیشود']],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'chatrjshe' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}

if($user['step'] == "hsjjansjaj" and !in_array($text,$arry)) {
if($user['coin'] >= $text){
    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>🎯بازی دارت:

1.دارت به وسط هدف بخورد..(ضریب: {$dataa['dar']})

2.دارت به هدف نخورد و به بیرون پرت شود..(ضریب: {$dataa['dar']})

3.دارت به رنگ قرمز برخورد کند(قرمز وسط حساب نمیشه)..(ضریب: {$dataa['dar']})

2.دارت به رنگ سفید برخورد کند..(ضریب: {$dataa['dar']})</b>",
 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'به هدف میخورد'],['text'=>"به هدف نمیخورد"]],
                    [['text' => 'قرمز'],['text'=>"سفید"]],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'chatrtge' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}





if ($text == $key['slt']) {
    if ($user['coin'] >= 100) {
        $bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);
        $conn->query("UPDATE user SET step = 'tascon' WHERE id = '$from_id' LIMIT 1");
    } else {
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>", $_back);
    }
}


if($user['step'] == "tascon" and !in_array($text,$arry)) {
if($user['coin'] >= $text){
    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>🎰ماشین اسلات:

1.همه شکل ها یکی باشند.(ضریب: {$dataa['slt']})</b>", 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'انجام شرط']],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'jsjjsjjab' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
if($user['step'] == "jsjjsjjab" and !in_array($text,$arry)) {
if($text == "انجام شرط"){
$dice1 = $bot->senddice($from_id,'🎰');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
   $zar = $user['step2'] * $dataa['slt'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
$bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}



if($text == $key['fot']){
if($user['coin'] >= 100){
$bot->sendMessage($from_id, "<b>👈سر چند سکه شرط میبندی؟\n\n⭕ موجودی شما : {$user['coin']}</b>",$_back);

$conn -> query("UPDATE `user` SET `step` = 'foto' WHERE `id` = '$from_id' LIMIT 1");
}else{
        $bot->sendMessage($from_id, "<b>⚠️حداقل موجودی 100 سکه میباشد.</b>", $_back);
}
}
if($user['step'] == "foto" and !in_array($text,$arry)) {
if($user['coin'] >= $text){

    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>⚽️بازی فوتبال:

1.توپ وارد دروازه میشود و اگر گل شد برنده اید.

2.توپ اگر وارد دروازه نشد برنده اید.

ضریب : 2</b>", 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'گل میشود'], ['text' => 'گل نمیشود']],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'chavtree' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
if($user['step'] == "chavtree" and !in_array($text,$arry)) {
if($text == "گل میشود"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'⚽');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1 or $value1 == 2){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
   $zar = $user['step2'] * $dataa['fot'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
$bot->sendMessage($from_id, "⭕شما باختید.",$_back);     $coin = $user['coin'] - $user['step2'];
      $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
}
if($user['step'] == "chavtree"  and !in_array($text,$arry)) {
if($text == "گل نمیشود"){
if($user['coin'] >= $user['step2']){
$dice1 = $bot->senddice($from_id,'⚽');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 3 or $value1 == 4 or $value1 == 5 or $value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
      $zar = $user['step2'] * $dataa['fot'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
   $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
   $conn -> query("UPDATE `user` SET `step` = 'none' WHERE `id` = '$from_id' LIMIT 1");
    $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}}
if($user['step'] == "chatrjshe" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "گل میشود"){
$dice1 = $bot->senddice($from_id,'🏀');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 4 or $value1 == 5 or $value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['bas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
   $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}
}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);


}}
if($user['step'] == "chatrjshe" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "گل نمیشود"){
$dice1 = $bot->senddice($from_id,'🏀');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1 or $value1 == 2 or $value1 == 3){

   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
      $zar = $user['step2'] * $dataa['bas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{

   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
   $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);


}
}




if($user['step'] == "chatrtge" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "به هدف نمیخورد"){
$dice1 = $bot->senddice($from_id,'🎯');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 != 1){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['dar'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}




if ($user['step'] == "chatrtge" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "به هدف میخورد") {
    $dice1 = $bot->senddice($from_id, '🎯');
    $value1 = $dice1->result->dice->value;
    sleep(4);

    if ($value1 == 6) {
$bot->sendMessage($from_id, "✅شما برنده شدید.", $_back);

    $zar = $user['step2'] * $dataa['dar'];
    $coin = $user['coin'] + $zar;

  } else {
    $bot->sendMessage($from_id, "⭕شما باختید.", $_back);

$coin = $user['coin'] - $user['step2'];

    $conn->query("UPDATE user SET coin = '$coin' WHERE id = '$from_id' LIMIT 1");

    }
}
}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}


if($user['step'] == "chatrtge" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "سفید"){
$dice1 = $bot->senddice($from_id,'🎯');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 2 or $value1 == 4 or $value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['dar'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}



if($user['step'] == "tascionha" and !in_array($text,$arry)) {
if($user['coin'] >= $text){
    if (is_numeric($text) && intval($text) > 0 && filter_var($text, FILTER_VALIDATE_INT) !== false) {
        $conn->query("UPDATE user SET step2 = '$text' WHERE id = '$from_id' LIMIT 1");
        $bot->bot('sendMessage', ['chat_id' => $chat_id, 'text' => "<b>🎲بازی تاس:

1.شما میتوانید روی زوج یا فرد آمادن شرط ببندید که در این صورت دوتاس انداخته میشود که باید هر دوتا با توجه به شرط یا زوج بیاید یا فرد.(ضریب: 2)

2.میتوانید روی اعداد شرط ببندید که یک تاس انداخته میشود اگر عدد شرط شما آمد شما برنده اید.(ضریب: 2)</b>", 'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => 'شرط روی زوج'],['text'=>"شرط روی فرد"]],
                    [['text' => '1'],['text'=>"2"],['text'=>"3"],['text' => '4'],['text'=>"5"],['text'=>"6"]],
                    [['text' => "🔙برگشت"]],
                ],
                'resize_keyboard' => true
            ])
   ]);
        $conn->query("UPDATE user SET step = 'chatrte' WHERE id = '$from_id' LIMIT 1");
} else {
        $bot->sendMessage($from_id, "❌ورودی صحیح نیست", $_back);
}
}else{
 $bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}
}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "شرط روی زوج"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 2 or $value1 == 4 or $value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "شرط روی فرد"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1 or $value1 == 3 or $value1 == 5){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}



if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "1"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
  
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "2"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 2){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "3"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 3){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "4"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 4){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
 $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "5"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 5){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
   $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
    $coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
if($user['step'] == "chatrte" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "6"){
$dice1 = $bot->senddice($from_id,'🎲');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 6){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['tas'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
$coin = $user['coin'] - $user['step2'];

   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);
}}
if($user['step'] == "chatrtge" and !in_array($text,$arry)) {
if($user['coin'] >= $user['step2']){
if($text == "قرمز"){
$dice1 = $bot->senddice($from_id,'🎯');
  $value1 = $dice1->result->dice->value;
  sleep(4);
  if($value1 == 1 && $value1 == 3 && $value1 == 5){
   $bot->sendMessage($from_id, "✅شما برنده شدید.", $_back); 
  $zar = $user['step2'] * $dataa['dar'];
   $coin = $user['coin'] + $zar;
      $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
      
}else{
   $bot->sendMessage($from_id, "⭕شما باختید.", $_back); 
    $coin = $user['coin'] - $user['step2'];
   $conn -> query("UPDATE `user` SET `coin` = '$coin' WHERE `id` = '$from_id' LIMIT 1");
}}}else{
$bot->sendMessage($from_id, $media->text(["coinnot"]),$_back);

}}
?>