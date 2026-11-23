<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

error_reporting(E_ALL);
date_default_timezone_set('Asia/Tehran'); 
/*
نوشته شده توسط @Dev_Amiri و اوپن شده در کانال @CristalTeam 
 ذکر منبع اجباری!
 */
flush();
ob_start();
ob_implicit_flush(1);
ini_set("expose_php", "Off");
ini_set("Allow_url_fopen", "Off");
ini_set("disable_functions", "exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,eval,file,file_get_contents,file_put_contents,fclose,fopen,fwrite,mkdir,rmdir,unlink,glob,echo,die,exit,print,scandir");
ini_set("max_execution_time", "25");
include 'jdf.php';
include("Config.php");

function bot($method, $datas = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . API_KEY . '/' . $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $curl_exec = curl_exec($ch);
    return json_decode($curl_exec, true);
}

function getCurrencyRate($currency, $dstCurrency = 'irt') {
    $currency = strtoupper($currency);
    $dstCurrency = strtoupper($dstCurrency);
    $apiUrl = "https://api.nobitex.ir/market/stats?srcCurrency=" . urlencode($currency) . "&dstCurrency=" . urlencode($dstCurrency);
    $json = file_get_contents($apiUrl);
    $data = json_decode($json, true);
    if (isset($data['stats'][$currency . '-' . $dstCurrency])) {
        return $data['stats'][$currency . '-' . $dstCurrency]['latest'];
    } else {
        return null;
    }
}

function deleteMessage($chat_id, $message_id){
   bot('deletemessage',[
      'chat_id'=>$chat_id,
      'message_id'=>$message_id
   ]);
}

function persianToLatinNumber($str) {
    $persian_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $latin_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($persian_numbers, $latin_numbers, $str);
}


$update = json_decode(file_get_contents('php://input'));

if (isset($update->message)) {
    $message = $update->message;
    $text = $message->text;
    $tc = $message->chat->type;
    $chat_id = $message->chat->id;
    $from_id = $message->from->id;
    $message_id = $message->message_id;
    $first_name = @str_replace([">", "<"], ["&gt;", "&lt;"], $message->from->first_name);
} elseif (isset($update->callback_query)) {
    $callback_query = $update->callback_query;
    $data = $callback_query->data;
    $tc = $callback_query->message->chat->type;
    $chat_id = $callback_query->message->chat->id;
    $from_id = $callback_query->from->id;
    $message_id = $callback_query->message->message_id;
    $first_name = @str_replace([">", "<"], ["&gt;", "&lt;"], $callback_query->from->first_name);
    $text = $callback_query->message->text;

if (preg_match('/half_(.+)_(\d+(\.\d+)?)/', $data, $matches)) {
        $currency = strtolower($matches[1]);
        $amount = floatval($matches[2]) / 2;
        $action = 'half';
    } elseif (preg_match('/double_(.+)_(\d+(\.\d+)?)/', $data, $matches)) {
        $currency = strtolower($matches[1]);
        $amount = floatval($matches[2]) * 2;
        $action = 'double';
    } elseif (preg_match('/refresh_(.+)_(\d+(\.\d+)?)/', $data, $matches)) {
        $currency = strtolower($matches[1]);
        $amount = floatval($matches[2]);
        $action = 'refresh';
    } else {
        $action = 'unknown';
    }
} else {
    header("Location: index.html");
    exit();
}
// CristalTeam - @Dev_Amiri
if ($text == '/start') {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "😄 سلام به ربات قیمت رمز ارز خوش آمدید.\n\n🤖 من یک ربات کاملا رایگان هستم و میتوانم قیمت ارز دیجیتال را برای شما ارسال کنم\n\n☑️ برای استفاده از من فقط کافیه با استفاده از دکمه زیر منو به گروهت اضافه کنی یا منو به صورت غیرمستقیم به گروهت بیاری.\n\n🤗 منتظر چی هستی؟ همین الان منو به گروهت اضافه کن و از امکانات فوق العاده و رایگان من استفاده کن.",
        'parse_mode' => "HTML",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "➕ افزودن به گروه ➕", 'url' => "https://telegram.me/$botuser?startgroup=new"]],
                [['text' => "👨‍💻 مدیریت و گزارش مشکل 👨‍💻", 'url' => "https://t.me/Dev_Amiri"]],
            ]
        ])
    ]);
    $connect->query("INSERT INTO `user` (`id`) VALUES ('$chat_id')");
} 

if ($text == "راهنما" || $text == "help" || $text == "Help") {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "<b>راهنمای ربات Currency Rate 💡 :</b>\n\n✅ این ربات قابلیت استعلام گیری با نام فارسی و انگلیسی را دارد.\n\n📝 <b>دستورالعمل برای قیمت‌گیری :</b>\n\n<code>اسم ارز -> مثال : نات ، نات کوین</code>\n<code>P اسم ارز -> P not</code>\n\n\n🔸<u>همچنین قابلیت استعلام با تعداد ارز نیز فراهم است!</u>\n\nمثال : \n\n<code>Calc not 100</code>\n<code>نات 100 ، نات کوین 100</code>\n\n\n⚙ <b>قابلیت‌ ماشین حساب :</b>\nمثال استفاده : 10+10 \n\n\n👨‍💻<i><b>درصورت هرگونه مشکل میتوانید با پشتیبانی در ارتباط باشيد. </b></i>\n@Dev_Amiri \n\n<a href=\"https://telegram.me/CurrencyRateRobot?startgroup=new\">👈<b>جهت افزودن ربات به گروه کلیک کنید👉</b></a>",
        'parse_mode' => "HTML",
        'reply_to_message_id' => $message_id,
    ]);
}
// CristalTeam - @Dev_Amiri
elseif (preg_match('/^calc\s+([a-zA-Z]+)\s+([0-9.]+)/i', $text, $matches)) {
    $currency = strtolower($matches[1]);
    $amount = floatval($matches[2]);
    if (preg_match('/^[a-zA-Z]+$/', $currency) && $amount > 0) {
        $rateIrt = getCurrencyRate($currency, 'rls');
        $rateUsdt = getCurrencyRate($currency, 'usdt');
        if ($rateIrt !== null && $rateUsdt !== null) {
            $rateToman = $rateIrt / 10; 
            $totalToman = $amount * $rateToman;
            $totalUsdt = $amount * $rateUsdt;
            $message = "<b>🪙 • ".strtoupper($currency).": ".number_format($amount, 2)."</b>\n".
                       "<b>💰 • PriceToman: ".number_format($rateToman)."</b>\n".
                       "<b>💰 • PriceUsdt: ".$rateUsdt."</b>\n".
                       "<b>💸 • Current Toman: ".number_format($totalToman)."</b>\n".
                       "<b>💸 • Current Usdt: ".number_format($totalUsdt, 2)."</b>\n".
                       "<b>⏰ • Time: ".date("Y-m-d | H:i:s")."</b>";
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => 'Half ✂', 'callback_data' => 'half_'.$currency.'_'.$amount], ['text' => 'Double 💰', 'callback_data' => 'double_'.$currency.'_'.$amount]],
                    [['text' => '•Refresh•', 'callback_data' => 'refresh_'.$currency.'_'.$amount]],
                    [['text' => 'Developer', 'url' => 'https://t.me/SirAmiri']]
                ]
            ];
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode($keyboard)
            ]);
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "ارز موجود نمی‌باشد.",
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id
            ]);
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "مقدار نامعتبر.",
            'parse_mode' => "HTML",
            'reply_to_message_id' => $message_id
        ]);
    }
}
// CristalTeam - @Dev_Amiri
if (isset($action)) {
    if ($action === 'double') {
        $amount *= 2;
    } elseif ($action === 'half') {
        $amount /= 2;
    }
    $rateIrt = getCurrencyRate($currency, 'rls');
    $rateUsdt = getCurrencyRate($currency, 'usdt');
    if ($rateIrt && $rateUsdt) {
        $rateToman = $rateIrt / 10;
        $totalToman = $amount * $rateToman;
        $totalUsdt = $amount * $rateUsdt;
        $message = "<b>🪙 • ".strtoupper($currency).": ".number_format($amount, 2)."</b>\n".
                   "<b>💰 • PriceToman: ".number_format($rateToman)."</b>\n".
                   "<b>💰 • PriceUsdt: ".$rateUsdt."</b>\n".
                   "<b>💸 • Current Toman: ".number_format($totalToman)."</b>\n".
                   "<b>💸 • Current Usdt: ".number_format($totalUsdt, 2)."</b>\n".
                   "<b>⏰ • Time: ".date("Y-m-d | H:i:s")."</b>";
        $keyboard = [
            'inline_keyboard' => [
                [['text' => 'Half ✂', 'callback_data' => 'half_' . $currency . '_' . $amount], ['text' => 'Double 💰', 'callback_data' => 'double_' . $currency . '_' . $amount]],
                [['text' => '•Refresh•', 'callback_data' => 'refresh_' . $currency . '_' . $amount]],
                [['text' => 'Developer', 'url' => 'https://t.me/SirAmiri']]
            ]
        ];
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $message,
            'parse_mode' => "HTML",
            'reply_markup' => json_encode($keyboard)
        ]);
    } 
}
// CristalTeam - @Dev_Amiri
elseif (preg_match('/^P\s+([a-zA-Z]+)/i', $text, $matches)) {
    $currency = strtolower($matches[1]);
    if (preg_match('/^[a-zA-Z]+$/', $currency)) {
        $rateIrt = getCurrencyRate($currency, 'rls');
        $rateUsdt = getCurrencyRate($currency, 'usdt');
        if ($rateIrt !== null && $rateUsdt !== null) {
            $rateToman = $rateIrt / 10;
            $message = "<b>┌ •".strtoupper($currency)."</b>\n".
                       "<b>├ Price</b>\n".
                       "<b>┊ ├ Usdt : ".$rateUsdt."</b>\n".
                       "<b>┊ ├ Toman : ".number_format($rateToman)."</b>\n".
                       "<b>└ Time  ".date("Y-m-d | H:i:s")."</b>";
         $keyboard = [
            'inline_keyboard' => [
                [['text' => '•Refresh•', 'callback_data' => 'refresh_' . $currency]],
                [['text' => 'Developer', 'url' => 'https://t.me/SirAmiri']]
            ]
        ];
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'reply_to_message_id'=>$message_id,
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode($keyboard)
            ]);
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "ارز موجود نمی‌باشد.",
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id
            ]);
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "مقدار نامعتبر.",
            'parse_mode' => "HTML",
            'reply_to_message_id' => $message_id
        ]);
    }
}
// CristalTeam - @Dev_Amiri
elseif (preg_match('/^refresh\s+([a-zA-Z]+)/i', $text, $matches)) {
    $currency = strtolower($matches[1]);
    if (preg_match('/^[a-zA-Z]+$/', $currency)) {
        $rateIrt = getCurrencyRate($currency, 'rls');
        $rateUsdt = getCurrencyRate($currency, 'usdt');
        if ($rateIrt !== null && $rateUsdt !== null) {
            $rateToman = $rateIrt / 10;
            $message = "<b>┌ •".strtoupper($currency)."</b>\n".
                       "<b>├ Price</b>\n".
                       "<b>┊ ├ Usdt : ".$rateUsdt."</b>\n".
                       "<b>┊ ├ Toman : ".number_format($rateToman)."</b>\n".
                       "<b>└ Time  ".date("Y-m-d | H:i:s")."</b>";
         $keyboard = [
            'inline_keyboard' => [
                [['text' => '•Refresh•', 'callback_data' => 'refresh_' . $currency]],
                [['text' => 'Developer', 'url' => 'https://t.me/SirAmiri']]
            ]
        ];
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id
            ]);
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "ارز موجود نمی‌باشد.",
                'parse_mode' => "HTML",
                'reply_to_message_id' => $message_id
            ]);
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "مقدار نامعتبر.",
            'parse_mode' => "HTML",
            'reply_to_message_id' => $message_id
        ]);
    }
}
// CristalTeam - @Dev_Amiri
if (preg_match('/^(\d+)\s*([\+\-\*\/])\s*(\d+)$/', $text, $matches)) {
    $num1 = (float)$matches[1];
    $operator = $matches[2];
    $num2 = (float)$matches[3];
    switch ($operator) {
        case '+':
            $result = number_format($num1 + $num2);
            break;
        case '-':
            $result = number_format($num1 - $num2);
            break;
        case '*':
            $result = number_format($num1 * $num2);
            break;
        case '/':
            if ($num2 != 0) {
                $result = number_format($num1 / $num2);
            } else {
                $result = 'تقسیم بر صفر غیرمجاز است';
            }
            break;
        default:
            $result = 'عملیات معتبر نیست';
            break;
    }
    if (isset($result) && $result !== 'عملیات معتبر نیست') {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "$num1 $operator $num2 = $result",
            'parse_mode' => "HTML",
            'reply_to_message_id' => $message_id,
        ]);
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $result,
            'parse_mode' => "HTML",
            'reply_to_message_id' => $message_id,
        ]);
    }
}
// CristalTeam - @Dev_Amiri
if ($text == '/panel'){
    $user = mysqli_num_rows(mysqli_query($connect,"select `id` from `user`"));
    bot('sendMessage',[
       'chat_id'=>$chat_id,
       'text'=>"👤 آمار کل کاربران : $alluser",
       'parse_mode'=>"HTML",
    ]);
}
/*
نوشته شده توسط @Dev_Amiri و اوپن شده در کانال @CristalTeam 
 ذکر منبع اجباری!
 */
?>