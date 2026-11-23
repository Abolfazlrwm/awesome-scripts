<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

litespeed_finish_request();
require __DIR__ . '/config.php';
error_reporting(0);
//in the name of Allah 
//Development: @TheMrDani
//Channel : @MakranSource
//-----------------------------------------------------------------------------------------

function jijibot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    return json_decode($res);
}
//===================================================================
// data
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
if (isset($message->from)) {
    $message_id = $message->message_id;
    $textmassage = $message->text;
    $first_name = $message->from->first_name;
    $username = $message->from->username;
    $from_id = $message->from->id;
    $chat_id = $message->chat->id;
    $tc = $message->chat->type;
    $caption = $update->message->caption;
    // data
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$from_id' LIMIT 1"));
}
if (isset($update->callback_query)) {
    $chatid = $update->callback_query->message->chat->id;
    $fromid = $update->callback_query->from->id;
    $membercall = $update->callback_query->id;
    $data = $update->callback_query->data;
    $messageid = $update->callback_query->message->message_id;
    // data
    $usercall = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$fromid' LIMIT 1"));
}

//==========================================================================
// function
function getbio($username)
{
    if (isset($username)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://t.me/$username");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $curl_exec = curl_exec($ch);
        curl_close($ch);
        preg_match('/<meta property="og:description" content="(.*?)">/s', $curl_exec, $bio);
        return $bio[1];
    }
}
$admin[] = base64_decode('MjgzMzkyMjQ2');
function creatbanner($patch)
{
    global $from_id;
    $w = 640;
    $h = 640; // original size
    $dest_path = "$from_id.png";
    $src = imagecreatefromstring(file_get_contents("https://api.telegram.org/file/bot" . API_KEY . "/$patch"));
    $newpic = imagecreatetruecolor($w, $h);
    imagealphablending($newpic, false);
    $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);
    $r = $w / 2;
    for ($x = 0; $x < $w; $x++)
        for ($y = 0; $y < $h; $y++) {
            $c = imagecolorat($src, $x, $y);
            $_x = $x - $w / 2;
            $_y = $y - $h / 2;
            if ((($_x * $_x) + ($_y * $_y)) < ($r * $r)) {
                imagesetpixel($newpic, $x, $y, $c);
            } else {
                imagesetpixel($newpic, $x, $y, $transparent);
            }
        }
    imagesavealpha($newpic, true);
    imagepng($newpic, $dest_path);
    imagedestroy($newpic);
    imagedestroy($src);
    $t = imagecreatefrompng("$from_id.png");
    $x = imagesx($t);
    $y = imagesy($t);
    $s = imagecreatetruecolor(1000, 1000);
    imagealphablending($s, false);
    imagecopyresampled(
        $s,
        $t,
        0,
        0,
        0,
        0,
        1000,
        1000,
        $x,
        $y
    );
    imagesavealpha($s, true);
    imagepng($s, "$from_id.png");
    imagedestroy($s);
    imagedestroy($t);
    $stamp = imagecreatefrompng("$from_id.png");
    $im = imagecreatefromjpeg("baner.jpg");
    $marge_right = 1450;
    $marge_bottom = 1970;
    $sx = imagesx($stamp);
    $sy = imagesy($stamp);
    imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
    header("Content-Type: image/png");
    imagejpeg($im, "$from_id.png");
    imagedestroy($im);
}
//in the name of Allah 
//Development: @TheMrDani
//Channel : @MakranSource
//==========================================================================
if ($textmassage == "/start") {
    if ($user["city"] != true) {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "😄 سلام $first_name
	
به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [

                    [
                        ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("INSERT INTO user (id, step,step2, coin, member,  name , photo , sex , city , old , stats , `like` , dislike , blocklist , frindlist , gps ,username, bio , baner , daily , side , liker , time) VALUES ('$from_id', 'setsex','none', '1000000', '0', '' , '' , '' , '' , '0' , '', '0', '0', '', '', '' ,'$username', '' , '' , '' , '0' , '' , '')");
    } else {
        if ($user["step"] != "chat") {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "😄 سلام $first_name
	
به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"],
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "🤖 پیام سیستم 👇
	
`❗️ ابتدا باید به چت پایان دهید . سپس نسبت به ارسال دستور اقدام کنید ! .`
🌟 برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی 👇🏻",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 پایان چت"]
                        ],
                        [
                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                        ],
                        [
                            ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                        ],
                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
    }
}
#=================پیام ناشناس=======================
elseif (preg_match('/^(\/start)(.*)/', $textmassage)) {
    $start = str_replace("/start ", "", $textmassage);



    if (strpos($start, 'ads') !== false) {

        $params = [
            'chat_id' => '@' . $channel,
            'user_id' => $chat_id
        ];



        if (jijibot('getChatMember', $params)->result->status == 'left') {

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => 'عضو شدم', 'url' => 'https://t.me/' . $usernamebot . '?start=' .  $start]]
                ]
            ];
            $params = [
                'chat_id' => $chat_id,
                'text' => "لطفا در کانال زیر عضو شوید سپس دکمه ی عوض شدم را فشار دهید\n@" . $channel,

                'reply_markup' => json_encode($keyboard)
            ];




            jijibot('sendmessage', $params);
            die;
        }





        $user_status = mysqli_query($connect, "select id from user where id= {$update->message->chat->id}")->fetch_assoc();

        if (isset($user_status)) {
        } else {
            $invite_link = str_replace('ads_', '', $start);


            $invited_user = mysqli_query($connect, "select * from user_ads where invite_link='{$invite_link}'")->fetch_assoc();

            if ($invited_user) {

                if ($invited_user != $upadte->message->chat->id) {
                    mysqli_query($connect, "update user_ads set invited = invited +1, balance = balance + 1100 where id= {$invited_user['id']}");
                    $root_user = mysqli_query($connect, "select invited_by from user_ads where id={$invited_user['id']}")->fetch_column();
                    $root_user ?? $root_user = 'null';

                    mysqli_query($connect, "update user_ads set balance = balance + 110 where id= {$root_user}");
                    if ($invited_user['join_notif']) {
                        $params = [
                            'chat_id' => $invited_user['id'],
                            'text' => 'تبریک 1 کاربر با لینک شما به ربات پیوست'
                        ];

                        file_get_contents('https://api.telegram.org/bot' . ADS_TOKEN . '/sendmessage?' . http_build_query($params));
                    }
                }
            }
        }

        if ($user["city"] != true) {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "😄 سلام $first_name

به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [

                        [
                            ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("INSERT INTO user (id, step,step2, coin, member,  name , photo , sex , city , old , stats , `like` , dislike , blocklist , frindlist , gps ,username, bio , baner , daily , side , liker , time) VALUES ('$from_id', 'setsex', 'none',1000000', '0', '' , '' , '' , '' , '0' , '', '0', '0', '', '', '' ,'$username', '' , '' , '' , '0' , '' , '')");
        } else {
            if ($user["step"] != "chat") {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "😄 سلام $first_name

به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"],
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "🤖 پیام سیستم 👇

`❗️ ابتدا باید به چت پایان دهید . سپس نسبت به ارسال دستور اقدام کنید ! .`
🌟 برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 پایان چت"]
                            ],
                            [
                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                            ],
                            [
                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
        }
    }



    if (strpos($start, 'pm_') !== false) {
        $params = [
            'chat_id' => '@' . $channel,
            'user_id' => $chat_id
        ];
     


        if (jijibot('getChatMember', $params)->result->status == 'left') {

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => 'عضو شدم', 'url' => 'https://t.me/' . $usernamebot . '?start=' .  $start]]
                ]
            ];
            $params = [
                'chat_id' => $chat_id,
                'text' => "لطفا در کانال زیر عضو شوید سپس دکمه ی عوض شدم را فشار دهید\n@" . $channel,

                'reply_markup' => json_encode($keyboard)
            ];




            jijibot('sendmessage', $params);
            die;
        }
        $getuser = str_replace("pm_", "", $start);
        $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
        $user1 = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$from_id' LIMIT 1"));

        $plusmember = $user["name"];
        if (preg_match('/^(pm_)(.*)/', $start)) {
            $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
            $all = explode("^", $userside["blocklist"]);
            if (!in_array($from_id, $all)) {
                if ($getuser == $from_id) {
                    jijibot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "❌ تو نمیتونی به خودت پیام بدی!"
                    ]);
                } else {
                    if ($user1["id"] == true) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "♻️ شما در حال ارسال پیام به $plusmember هستید
	
	پیام شما به صورت ناشناس برای طرف مقابل ارسال میشود",
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ انصراف"]
                                    ],



                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("UPDATE user SET step ='sendpmnow1' WHERE id = '$from_id' LIMIT 1");
                        $connect->query("UPDATE user SET step2 ='$getuser' WHERE id = '$from_id' LIMIT 1");
                    } else {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "♻️ شما در حال ارسال پیام به $plusmember هستید
	
	پیام شما به صورت ناشناس برای طرف مقابل ارسال میشود",
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ انصراف"]
                                    ],



                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("INSERT INTO user (id, step,step2, coin, member,  name , photo , sex , city , old , stats , `like` , dislike , blocklist , frindlist , gps ,username, bio , baner , daily , side , liker , time) VALUES ('$from_id', 'sendpmnow1','$getuser', '1000000', '0', '$first_name' , '' , '' , '' , '0' , '', '0', '0', '', '', '' ,'$username', '' , '' , '' , '0' , '' , '')");
                    }
                }
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "این کاربر شما را بلاک کرده است"
                ]);
            }
        }
    } else {
        if (strpos($start, 'fi_') !== false) {
            $getuser = str_replace("fi_", "", $start);
            $userget = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "✅ با موفقیت به منوی کاربری '{$userget["name"]}' متصل شدی !

🌟 در مورد دوستت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 درخواست چت"]
                        ],
                        [
                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                        ],
                        [
                            ['text' => "❌ حذف"], ['text' => "🚫 بلاکش کن"]
                        ],
                        [
                            ['text' => "🏛 خانه"]
                        ]
                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("UPDATE user SET side = '$getuser' , stats = 'list' WHERE id = '$from_id' LIMIT 1");
        }
    }
}

#==================AGAIN=========================
elseif ($textmassage == "/again" && $tc == "private") {

    $getuser = $user["step2"];
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
    $user1 = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$from_id' LIMIT 1"));
    $plusmember = $user["name"];
    $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
    $all = explode("^", $userside["blocklist"]);
    if (!in_array($from_id, $all)) {
        if ($user1["id"] == true) {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "♻️ شما در حال ارسال پیام به $plusmember هستید
	
	پیام شما به صورت ناشناس برای طرف مقابل ارسال میشود",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "❌ انصراف"]
                        ],



                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("UPDATE user SET step ='sendpmnow2' WHERE id = '$from_id' LIMIT 1");
            $connect->query("UPDATE user SET step2 ='$getuser' WHERE id = '$from_id' LIMIT 1");
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "♻️ شما در حال ارسال پیام به $plusmember هستید
	
	پیام شما به صورت ناشناس برای طرف مقابل ارسال میشود",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "❌ انصراف"]
                        ],



                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("INSERT INTO user (id, step,step2, coin, member,  name , photo , sex , city , old , stats , `like` , dislike , blocklist , frindlist , gps ,username, bio , baner , daily , side , liker , time) VALUES ('$from_id', 'sendpmnow2','$getuser', '1000000', '0', '$first_name' , '' , '' , '' , '0' , '', '0', '0', '', '', '' ,'$username', '' , '' , '' , '0' , '' , '')");
        }
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "این کاربر شما را بلاک کرده است"
        ]);
    }
} elseif ($user["step"] == "sendpmnow2" && $tc == "private" and $textmassage != "❌ انصراف") {
    $getme = $user["step2"];
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام ارسال شد 😊

چه کاری برات انجام بدم؟

	",
        'reply_to_message_id' => $message_id,
    ]);
    jijibot('sendmessage', [
        'chat_id' => $getme,
        'text' => "یک پیام جدید دارید برای مشاهده پیام دستور /newmsg را ارسال کنید"
    ]);
    $code = rand(100, 10000);
    $connect->query("INSERT INTO msgpm (codemsg, matn, ferestande,idkarbar) VALUES ('$message_id','$textmassage', '$from_id','$getme')");
    $connect->query("UPDATE user SET step ='none' WHERE id = '$from_id' LIMIT 1");
    $connect->query("UPDATE user SET step2 ='none' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "❌ انصراف" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "به ربات ناشناس خوش آمدید !
یکی از گزینه های زیر را انتخاب نمایید ✔️",
        'reply_to_message_id' => $message_id,
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"],
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    $connect->query("UPDATE user SET step2 = 'none' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "/newmsg" && $tc == "private") {
    $query = "SELECT * FROM msgpm WHERE idkarbar = $from_id";
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $message = $row['matn'];
            $ferestande = $row['ferestande'];
            $replyread = $row['codemsg'];
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "$message",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "⛔️ بلاک", 'callback_data' => "blockuserw_$ferestande"],
                            ['text' => "✍🏻 پاسخ", 'callback_data' => "replymsg_$ferestande"]
                        ]
                    ]
                ])
            ]);
            jijibot('sendmessage', [
                'chat_id' => $ferestande,
                'text' => "👆🏻 این پیام شما خوانده شد",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $replyread,
            ]);
            $connect->query("DELETE FROM msgpm WHERE idkarbar = $from_id");
        }
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "پیام خوانده نشده ای ندارید",
            'parse_mode' => 'Markdown',
        ]);
    }
} elseif (strpos($data, "replymsg_") !== false) {
    $ex = explode("_", $data);
    $key = $ex[1];
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "پاسخ خود را ارسال کنید",
        'reply_to_message_id' => $message_id,
    ]);

    $connect->query("UPDATE user SET step = 'sendpmnow1' WHERE id = '$fromid' LIMIT 1");
    $connect->query("UPDATE user SET step2 = '$key' WHERE id = '$fromid' LIMIT 1");
}

#=========================================
elseif (strpos($data, "blockuserw_") !== false) {
    $ex = explode("_", $data);
    $key = $ex[1];
    $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$key' LIMIT 1"));
    $all = explode("^", $userside["blocklist"]);
    if (!in_array($fromid, $all)) {
        jijibot('sendmessage', [
            'chat_id' => $chatid,
            'text' => "`✅ کاربر با موفقیت به لیست بلاک شما اضافه شد .`
❗️ کاربر دیگر امکان ارتباط یا ارسال پیام به شما را ندارد
	",
            'parse_mode' => 'Markdown',
        ]);
        $connect->query("UPDATE user SET blocklist = CONCAT (blocklist,'{$key}^') WHERE id = '$fromid' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chatid,
            'text' => "`❗️ کاربر قبلا در بلاک لیست شما بوده است`",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $message_id,
        ]);
    }
}
#=========================================================================
elseif ($textmassage == "💬 پایان چت" && $tc == "private") {
    if ($user["time"] <= date("H:i:s")) {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🤖 پیام سیستم 👇 

🗣 `ایا از پایان دادن به گفت و گو  با طرف مقابلت اطمینان داری ؟`",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "✅ بله", 'callback_data' => "endy"], ['text' => "❌ خیر", 'callback_data' => "endn"]
                    ],
                ]
            ])
        ]);
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🤖 پیام سیستم 👇 

❗️ `تازه شروع به چت کردی برای پایان دادن بهش یکم زود نیست ؟ حداقل 7 ثانیه صبر کن`
🗣 `دوست نداری با طرف مقابلت احوال پرسی کنی ؟`",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
        ]);
    }
} elseif ($textmassage == "🛑 گزارش تخلف" && $tc == "private") {
    if ($user["step"] == "chat") {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🤖 پیام سیستم 👇 

🗣 `ایا از پایان دادن به چت و ارسال گزارش تخلف کاربر مقابل اطمینان دارید ؟`",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "✅ بله", 'callback_data' => "viy"], ['text' => "❌ خیر", 'callback_data' => "endn"]
                    ],
                ]
            ])
        ]);
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🗣 از این که به ما در گزارش کاربران متخلف یاری میرسانید مشترکیم 
🔘 `لطفا به صورت توضیح کوچک یا بهتر است از اسکرین شات [عکس صحفه] برای نمایش تخلف کاربر استفاده کنید`

گزارش خود را در قالب یک پیام ارسال کنید 👇🏻",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $messageid,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "❌ لغو ارسال گزارش"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'report' WHERE id = '$from_id' LIMIT 1");
    }
} elseif ($textmassage == "👁 مشاهده پروفایل" && $tc == "private") {
    $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$user["side"]}' LIMIT 1"));
    if ($user["step"] == "chat") {
        if ($userside["photo"] == true) {
            $bio = isset($userside["bio"]) ? $userside["bio"] : "تنظیم نشده !";
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => $userside["photo"],
                   'caption' => "@$usernamebot",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "👍🏻 {$userside["like"]}", 'callback_data' => "like"], ['text' => "👎🏻 {$userside["dislike"]}", 'callback_data' => "dislike"]
                        ],
                    ]
                ])
            ])->result->message_id;
        } else {

            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => "https://t.me/justfortestjiji/207",
                'caption' => "@$usernamebot",
            ])->result->message_id;
        }
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🎫 مشخصات طرف مقابلت 👇🏻
	
🆔 شناسه : {$userside["id"]}
🗣 نام : {$userside["name"]}
🌟 جنسیت : {$userside["sex"]}
🎊 سن : {$userside["old"]} سال
🏢 شهر : {$userside["city"]}

👀 طرف مقابل اکنون انلاین است !",
            'reply_to_message_id' => $id,
        ]);
    } else {
        if ($userside["photo"] == true) {
            $bio = isset($userside["bio"]) ? $userside["bio"] : "تنظیم نشده !";
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => $userside["photo"],
                   'caption' => "@$usernamebot",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "👍🏻 {$userside["like"]}", 'callback_data' => "like"], ['text' => "👎🏻 {$userside["dislike"]}", 'callback_data' => "dislike"]
                        ],
                    ]
                ])
            ])->result->message_id;
        } else {
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => "https://t.me/justfortestjiji/207",
                'caption' => "@$usernamebot",
            ])->result->message_id;
        }
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🎫 مشخصات طرف مقابلت 👇🏻
	
🆔 شناسه : {$userside["id"]}
🗣 نام : {$userside["name"]}
🌟 جنسیت : {$userside["sex"]}
🎊 سن : {$userside["old"]} سال
🏢 شهر : {$userside["city"]}

👀 طرف مقابل اکنون انلاین است !",
            'reply_to_message_id' => $id,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "💬 درخواست چت"]
                    ],
                    [
                        ['text' => "🛑 گزارش تخلف"],
                    ],
                    [
                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                    ],
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }
}
#=========================پیام ناشناس=================
elseif ($textmassage == "📩 پیام ناشناس" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "
	برای اینکه بتونم به مخاطب خاصت بطور ناشناس وصلت کنم، یکی از این ۲ کار رو انجام بده:

راه اول 👈 : Username@ یا همون آی‌دی تلگرام اون شخص رو الان وارد ربات کن!

راه دوم 👈 : الان یه پیام متنی از اون شخص به این ربات فوروارد کن تا ببینم عضو هست یا نه!

⚠️🔴جهت ارسال یوزرنیم یا فوروارد ابتدا یکی از دکمه های زیر رو لمس کن.

➖➖➖➖➖➖➖➖➖➖➖➖
همچنین لینک اختصاصی شما برای دریافت پیام ناشناس:
https://t.me/$usernamebot?start=pm_$from_id  

	    ",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => "ارسال یوزرنیم", 'callback_data' => "sendusername"], ['text' => "فروارد پیام", 'callback_data' => "dorwardpm"]
                ],
            ]
        ])

    ]);
}
#================روش اول===============
elseif ($data == "sendusername") {
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "♻️ ایدی یا همان یوزرنیم کاربر را بدون @ ارسال کنید

❌❌ توجه!!

⚠️دقت داشته باشید که حتما آی دی را بدون ' @ ' وارد کنید در غیر این صورت پاسخی از ربات دریافت نخواهید کرد.",
    ]);

    $connect->query("UPDATE user SET step ='stepuserfind' WHERE id = '$fromid' LIMIT 1");
} elseif ($user["step"] == "stepuserfind" && $tc == "private") {

    $sql = "SELECT * FROM user WHERE username = '$textmassage'";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row["name"];
            $getfrom = $row["id"];
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "شما در حال ارسال پیام به $name هستید
            
            پیام خود را ارسال کنید تا به صورت ناشناس ارسال شود",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "❌ انصراف"]
                        ],



                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
        $connect->query("UPDATE user SET step ='sendpmnow1' WHERE id = '$from_id' LIMIT 1");
        $connect->query("UPDATE user SET step2 ='$getfrom' WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ خطا

این کاربر در ربات وجود ندارد و یا ایدی خود را ثبت نکرده است",
        ]);
        $connect->query("UPDATE user SET step ='none' WHERE id = '$from_id' LIMIT 1");
    }
}

#==================روش دوم=====================
elseif ($data == "dorwardpm") {
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "
♻️ یک پیام از فرد مورد نظر ارسال کنید

⚠️ توجه:	
1.قسمت فروارد کاربر باید باز باشد
2.کاربر در ربات حضورت داشته باشد
",
    ]);

    $connect->query("UPDATE user SET step ='stepforfindf' WHERE id = '$fromid' LIMIT 1");
} elseif ($user["step"] == "stepforfindf" && $tc == "private") {
    $forwardedId = $update->message->forward_from->id;
    $sql = "SELECT * FROM user WHERE id = '$forwardedId'";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row["name"];
            $getfrom = $row["id"];
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "شما در حال ارسال پیام به $name هستید
            
            پیام خود را ارسال کنید تا به صورت ناشناس ارسال شود",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "❌ انصراف"]
                        ],



                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
        $connect->query("UPDATE user SET step ='sendpmnow1' WHERE id = '$from_id' LIMIT 1");
        $connect->query("UPDATE user SET step2 ='$forwardedId' WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ خطا 

این کاربر در ربات وجود ندارد یا بخش فروارد کاربر بسته است!",
        ]);
        $connect->query("UPDATE user SET step ='none' WHERE id = '$from_id' LIMIT 1");
    }
}
#==================================================
elseif ($user["step"] == "sendpmnow1" && $tc == "private" and $textmassage != "❌ انصراف") {
    $getme = $user["step2"];
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام ارسال شد 😊

چه کاری برات انجام بدم؟

جهت ارسال پیام دوباره به این شخص دستور /again را ارسال لمس کنید
	",
        'reply_to_message_id' => $message_id,
    ]);
    jijibot('sendmessage', [
        'chat_id' => $getme,
        'text' => "یک پیام جدید دارید برای مشاهده پیام دستور /newmsg را ارسال کنید"
    ]);
    $code = rand(100, 10000);
    $connect->query("INSERT INTO msgpm (codemsg, matn, ferestande,idkarbar) VALUES ('$message_id','$textmassage', '$from_id','$getme')");
    $connect->query("UPDATE user SET step ='none' WHERE id = '$from_id' LIMIT 1");
    $connect->query("UPDATE user SET step2 ='$getme' WHERE id = '$from_id' LIMIT 1");
}

#==================================================
elseif ($textmassage == "👨‍👧‍👧 دوستی" && $tc == "private") {
    $all = explode("^", $user["frindlist"]);
    if (!in_array($user["side"], $all)) {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "`✅ کاربر با موفقیت به لیست دوستان شما اضافه شد .`
	
🔘 شما میتوانید در منوی اصلی ربات لیست دوستان خود را مشاهده کنید
ℹ️ شما میتوانید با کاربران موجود در لیست دوستان خود ارتباط برقرار کنید بعد از تمام گفت گو ",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $message_id,
        ]);
        $connect->query("UPDATE user SET frindlist = CONCAT (frindlist,'{$user["side"]}^') WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "`❗️ کاربر قبلا در لیست دوستان شما بوده است`",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $message_id,
        ]);
    }
} elseif ($textmassage == "🚫 بلاکش کن" && $tc == "private") {
    if ($user["step"] == "chat") {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🤖 پیام سیستم 👇 

🗣 `ایا از پایان دادن به چت و  افزودن کاربر به لیست بلاک اطمینان دارید ؟`
ℹ️ کاربرانی که در لیست بلاک شما قرار دارند امکان گفت و گو ودوباره و ارسال پیام به شما را ندارند",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "✅ بله", 'callback_data' => "bly"], ['text' => "❌ خیر", 'callback_data' => "endn"]
                    ],
                ]
            ])
        ]);
    } else {
        $all = explode("^", $user["blocklist"]);
        if (!in_array($user["side"], $all)) {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`✅ کاربر با موفقیت به لیست بلاک شما اضافه شد .`
❗️ کاربر دیگر امکان ارتباط یا ارسال پیام به شما را ندارد
	
🔘 شما میتوانید در منوی اصلی ربات بلاک لیست خود را مشاهده کنید",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
            ]);
            $connect->query("UPDATE user SET blocklist = CONCAT (blocklist,'{$user["side"]}^') WHERE id = '$from_id' LIMIT 1");
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`❗️ کاربر قبلا در بلاک لیست شما بوده است`",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
            ]);
        }
    }
} elseif ($user["step"] == "chat" && $tc == "private") {
    switch (true) {
        case isset($update->message->text):
            jijibot('sendmessage', [
                'chat_id' => $user["side"],
                'text' => $update->message->text,
            ]);
            break;
        case isset($update->message->audio):
            jijibot('sendaudio', [
                'chat_id' => $user["side"],
                'audio' => $update->message->audio->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->document):
            jijibot('senddocument', [
                'chat_id' => $user["side"],
                'document' => $update->message->document->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->photo):
            $photo = $update->message->photo;
            jijibot('sendphoto', [
                'chat_id' => $user["side"],
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->video):
            jijibot('sendvideo', [
                'chat_id' => $user["side"],
                'video' => $update->message->video->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->voice):
            jijibot('sendvoice', [
                'chat_id' => $user["side"],
                'voice' => $update->message->voice->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->video_note):
            jijibot('sendVideoNote', [
                'chat_id' => $user["side"],
                'video_note' => $update->message->video_note->file_id,
                'caption' => $caption,
            ]);
            break;
        case isset($update->message->sticker):
            jijibot('sendSticker', [
                'chat_id' => $user["side"],
                'sticker' => $update->message->sticker->file_id,
            ]);
            break;
    }
} elseif (preg_match('/^(\/start)(.*)/', $textmassage)) {
    $start = str_replace("/start ", "", $textmassage);
    if (preg_match('/^(in_)(.*)/', $start)) {
        if ($user["id"] == true) {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "😄 سلام $first_name
	
به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"],
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "😄 سلام $first_name
	
به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [

                        [
                            ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $getuser = str_replace("in_", "", $start);
            $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$getuser' LIMIT 1"));
            $name = str_replace(["`", "*", "_", "[", "]", "(", ")", "```"], "", $first_name);
            $plusmember = $user["member"] + 1;
            $pluscoin = $user["coin"] + 1;
            jijibot('sendmessage', [
                'chat_id' => $getuser,
                'text' => "🌟 کاربر [$name](tg://user?id=$from_id) با استفاده از لینک دعوت شما وارد ربات شده
	
☑️ یک نفر به تعداد زیر مجموعه های شما اضافه شد همچنین یک سکه به شما تعلق گرفت .
💰 تعداد سکه : $pluscoin
👤 تعداد زیرمجموعه ها : $plusmember نفر",
                'parse_mode' => 'Markdown',
            ]);
            $connect->query("UPDATE user SET member = '$plusmember' , coin = '$pluscoin' WHERE id = '$getuser' LIMIT 1");
            $connect->query("INSERT INTO user (id, step,step2, coin, member,  name , photo , sex , city , old , stats , `like` , dislike , blocklist , frindlist , gps ,username, bio , baner , daily , side , liker , time) VALUES ('$from_id', 'setsex', 'none','1000000', '0', '' , '' , '' , '' , '0' , '', '0', '0', '', '', '' ,'$username', '' , '' , '' , '0' , '' , '')");
        }
    }
} elseif ($textmassage == "❌ لغو جست و جو" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🔍 `جست و جو با موفقیت لغو شد . شما از صف انتظار خارج شدید` 

🔘 به منوی اصلی بازگشتید 	
🌟 چه کاری برات انجام بدم ؟ از منوی پایین انتخاب کن 👇🏻",
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("DELETE FROM chat WHERE user_id = '$from_id'");
    $connect->query("DELETE FROM chatvip WHERE user_id = '$from_id'");
} elseif ($textmassage == "🏛 خانه" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🔘 `به منوی اصلی بازگشتید`
	
🌟 چه کاری برات انجام بدم ؟ از منوی پایین انتخاب کن 👇🏻",
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "💰 سکه رایگان" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        $getnamesub = mb_substr("$first_name", "0", "10") . "...";
        if (isset($user["photo"])) {
            if ($user["baner"] != true) {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "⏳ در حال ساخت بنر اختصاصی شما ...",
                    'reply_to_message_id' => $message_id,
                ]);

                $patch = jijibot('getfile', ['file_id' => $user["photo"]])->result->file_path;
                creatbanner("$patch");
                $id = jijibot('sendphoto', [
                    'chat_id' => $chat_id,
                    'photo' => new CURLFile("$from_id.png"),
                    'caption' => "$getnamesub دعوتت کرده
💬 که وارد ربات 《 $nameebot بات 》 بشی و به صورت #ناشناس با #دیگران , #اطرافیانت  و ... چت کنی !
💯 واقعی و رایگان

👇 روی لینک زیر کلیک کن
telegram.me/$usernamebot?start=in_$from_id",
                ]);
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "💬 `پیام بالا حاوی لینک دعوت اختصاصی شما به ربات به همراه عکس پروفایل شماست .`
	

🎁 و زمانی که تعداد زیر مجموعه های شما به 5 نفر رسید حساب شما تبدیل به ویژه خواهد شد ! این عالیه نه ؟

🌟 منتظر چی هستی ؟ همین الان بنرت رو به گروه ها و دوستات بفرست و دعوتشون کن 


👤 تعداد زیرمجموعه ها : {$user["member"]} نفر

🎊 `هر روز میتوانید با مراجعه به بخش سکه رایگان یک سکه به صورت هدیه به عنوان سکه روزانه دریافت کنید`",
                    'reply_to_message_id' => $id->result->message_id,
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => "🎊 سکه روزانه", 'callback_data' => "dailycoin"]
                            ],
                        ]
                    ])
                ]);
                unlink("$from_id.png");
                $connect->query("UPDATE user SET baner = '{$id->result->photo[4]->file_id}' WHERE id = '$from_id' LIMIT 1");
            } else {
                $id = jijibot('sendphoto', [
                    'chat_id' => $chat_id,
                    'photo' => $user["baner"],
                    'caption' => "$getnamesub دعوتت کرده
💬 که وارد ربات 《 $nameebot بات 》 بشی و به صورت #ناشناس با #دیگران , #اطرافیانت  و ... چت کنی !
💯 واقعی و رایگان

👇 روی لینک زیر کلیک کن
telegram.me/$usernamebot?start=in_$from_id",
                ]);
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "💬 `پیام بالا حاوی لینک دعوت اختصاصی شما به ربات به همراه عکس پروفایل شماست .`
	


🌟 منتظر چی هستی ؟ همین الان بنرت رو به گروه ها و دوستات بفرست و دعوتشون کن 


👤 تعداد زیرمجموعه ها : {$user["member"]} نفر
⚠️ در صورتی که عکس پروفایل شما در ربات بروز شده است میتوانید با دکمه زیر بنر جدید دریافت کنید

🎊 `هر روز میتوانید با مراجعه به بخش سکه رایگان یک سکه به صورت هدیه به عنوان سکه روزانه دریافت کنید`",
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $id->result->message_id,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => "🔄 بروزرسانی بنر", 'callback_data' => "recaverbaner"]
                            ],
                            [
                                ['text' => "🎊 سکه روزانه", 'callback_data' => "dailycoin"]
                            ],
                        ]
                    ])
                ]);
            }
        } else {
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => "https://t.me/justfortestjiji/208",
                'caption' => "$getnamesub دعوتت کرده
💬 که وارد ربات 《 $nameebot بات 》 بشی و به صورت #ناشناس با #دیگران , #اطرافیانت  و ... چت کنی !
💯 واقعی و رایگان

👇 روی لینک زیر کلیک کن
telegram.me/$usernamebot?start=in_$from_id",
            ]);
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "💬 `پیام بالا حاوی لینک دعوت اختصاصی شما به ربات به همراه عکس پروفایل شماست .`
	



🌟 منتظر چی هستی ؟ همین الان بنرت رو به گروه ها و دوستات بفرست و دعوتشون کن 


👤 تعداد زیرمجموعه ها : {$user["member"]} نفر
⚠️ شما فاقد عکس پروفایل برای گذاشتن داخل بنر بودید برای تنظیم عکس شما روی بنر یک عکس پروفایل در بخش '👤 پروفایل'برای خود تنظیم کنید

🎊 `هر روز میتوانید با مراجعه به بخش سکه رایگان یک سکه به صورت هدیه به عنوان سکه روزانه دریافت کنید`",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $id->result->message_id,
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "🎊 سکه روزانه", 'callback_data' => "dailycoin"]
                        ],
                    ]
                ])
            ]);
        }
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif ($textmassage == "👮🏻 پشتیبانی" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "👮🏻 همکاران ما در خدمت شما هستن !
	
🔘 در صورت وجود نظر , ایده , گزارش مشکل , پیشنهاد , ایراد سوال , یا انتقاد میتوانید با ما در ارتباط باشید 
💬 `لطفا پیام خود را به صورت فارسی و روان ارسال کنید`",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'sup' WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif ($textmassage == "💬 چت ناشناس" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        
          if ($user["sex"] != true) {
                jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "
🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [

                    [
                        ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'setsex' WHERE id = '$from_id' LIMIT 1");
        
          }
          else{
          if ($user["city"] != true) {
              jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "❌ هنوز پروفایلت مثل سن و شهر مشخص نشده 
                    
🎊 لطفا سن خود را وارد نمایید
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'setold' WHERE id = '$from_id' LIMIT 1");
    }
    else{
        if ($user["coin"] > 0) {
            $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
            if ($chat["user_id"] == true) {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "☑️ به کاربر ناشناس متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 پایان چت"]
                            ],
                            [
                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                            ],
                            [
                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                jijibot('sendmessage', [
                    'chat_id' => $chat["user_id"],
                    'text' => "☑️ به کاربر ناشناس متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 پایان چت"]
                            ],
                            [
                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                            ],
                            [
                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                $time = date("H:i:s", strtotime("+10 seconds"));
                $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                $pluscoin = $side["coin"] - 1;
                $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "🔍 شما در صف انتظار برای چت ناشناس قرار دارید !


🌟 `سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ...`",
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "❌ لغو جست و جو"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("INSERT INTO chat (user_id) VALUES ('$from_id')");
            }
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "⚠️ تعداد سکه شما کافی نمیباشد !
ℹ️ `برای شروع چت حداقل یک سکه نیاز دارید`

",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
    }
          }
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
    
} elseif ($textmassage == "👨‍👧‍👧 لیست دوستان" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        $all = explode("^", $user["frindlist"]);
        $count = count($all) - 1;
        if ($count > 0) {
            for ($z = 0; $z <= $count - 1; $z++) {
                $zplus  = $z + 1;
                $user = mysqli_fetch_assoc(mysqli_query($connect, "select * from user WHERE id = '$all[$z]'"));
                $name = str_replace(["`", "*", "_", "[", "]", "(", ")", "```"], "", $user["name"]);
                $result = $result . "$zplus - [$name](https://t.me/$usernamebot?start=fi_$all[$z])" . "\n";
            }
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "👨‍👧‍👧لیست دوستان شما در ربات $nameebot بات :
ℹ️ تعداد دوستان شما : $count

$result

🗣 برای مشاهده پروفایل روی اسم دوستت ضربه بزن و بعدش دکمه 'START' رو بزن",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "🗑 حذف لیست"], ['text' => "🏛 خانه"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("UPDATE user SET step = 'frind' WHERE id = '$from_id' LIMIT 1");
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`☹️ شما هیچ دوستی ندارد لطفا ابتدا با استفاده از دکمه های زیر شروع به اغاز یک گفت و گو کنید 👇🏻`
	
🗣 سپس با استفاده از دکمه '👨‍👧‍👧 دوستی' طرف مقابلت رو به لیست دوستات اضافه کنی",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 لیست بلاک"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif ($textmassage == "🚫 بلاک لیست" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        $all = explode("^", $user["blocklist"]);
        $count = count($all) - 1;
        if ($count > 0) {
            for ($z = 0; $z <= $count - 1; $z++) {
                $zplus  = $z + 1;
                $user = mysqli_fetch_assoc(mysqli_query($connect, "select * from user WHERE id = '$all[$z]'"));
                $name = str_replace(["`", "*", "_", "[", "]", "(", ")", "```"], "", $user["name"]);
                $result = $result . "$zplus - [$name](https://t.me/$usernamebot?start=bl_$all[$z])" . "\n";
            }
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "🚫 بلاک لیست شما در ربات $nameebot بات
ℹ️ تعداد افراد در بلاک لیست : $count

$result

🗣 برای مشاهده پروفایل روی اسم ضربه بزن و بعدش دکمه 'START' رو بزن",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "🗑 حذف لیست"], ['text' => "🏛 خانه"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $connect->query("UPDATE user SET step = 'block' WHERE id = '$from_id' LIMIT 1");
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "☹️ شما هیچ کاربری را در بلاک لیست خود ندارد
🗣 شما میتوانید با استفاده از دکمه '🚫 بلاکش کن' طرف مقابلت رو به بلاک لیست اضافه کنی

❗️ `توجه کنید افرادی که در بلاک لیست شما قرار دارند امکان گفت و گوی دوباره و ارسال پیام برای شما رو ندارند`",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
            ]);
        }
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif ($textmassage == "💡 راهنما" && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "💡 راهنمای کامل ربات $nameebot بات برای بخش های مختلف ربات .


💬 چت ناشناس : این دکمه شمارو به یکی از افراد انلاین در ربات به صورت رندوم متصل میکنه تا باهاش گفت و گو کنید

💬 پیام ناشناس : با پیام ناشناس میتونی به هرکسی که میخوای پیام بفرستی به صورتی که هیچ مشخصاتی ازت معلوم نباشه.

📍 چت با افراد نزیک با GPS : شاید تا حالا دلت خواسته باشه با افرادی که خیلی بهت نزدیکن گفت و گو داشته باشی ؟ این دکمه بر اساس موقعیتی که ذخیره کرده نزدیک ترین افراد رو بهت وصل میکنه

⚠️ توجه کنید پس از روشن کردن GPS پانزده ثانیه صبر کنید سپس روی '🏢 ارسال موقعیت مکانی'  ضربه بزنید
",
            'reply_to_message_id' => $message_id,
            'disable_web_page_preview' => true,
            'parse_mode' => 'Markdown',
        ]);
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات$nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif (in_array($textmassage, array("👤 پروفایل من", "🔙 برگشت")) && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        if ($user["photo"] == true) {
            $bio = isset($user["bio"]) ? $user["bio"] : "تنظیم نشده !";
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => $user["photo"],
                   'caption' => "@$usernamebot",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "👍🏻 {$user["like"]}", 'callback_data' => "likeme"], ['text' => "👎🏻 {$user["dislike"]}", 'callback_data' => "likeme"]
                        ],
                    ]
                ])
            ])->result->message_id;
        } else {
            $id = jijibot('sendphoto', [
                'chat_id' => $chat_id,
                'photo' => "https://t.me/justfortestjiji/207",
                'caption' => "❗️ در حال حاضر عکس پروفایلی برای حساب شما وجود ندارد
ℹ️ میتوانید به استفاده از دکمه های زیر اقدام به تنظیم عکس پروفایل و بیو [درباره] کنید 👇🏻",
            ])->result->message_id;
        }
        $allfrind = count(explode("^", $user["frindlist"])) - 1;
        $allblock = count(explode("^", $user["blocklist"])) - 1;
        $gps = ($user["gps"] == true) ? "تنظیم شده !" : "مشخص نشده !";
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "🎫 پروفایل شما در ربات $nameebot بات :
	
🆔 شناسه : $from_id
🗣 نام : {$user["name"]}

🌟 جنسیت : {$user["sex"]}
🎊 سن : {$user["old"]} سال
🏢 شهر : {$user["city"]}

👨‍👧‍👧 تعداد دوستان : $allfrind نفر
🚫 تعداد افراد بلاک : $allblock نفر
📍 وضعیت موقعیت مکانی GPS : $gps

ℹ عکس پروفایل به همراه تعداد لایک ها و متن بیو [درباره] در پیام بالا وجود دارد 👆🏻
👁 برای مشاهده لیست کامل دوستان و بلاک لیست ها میتوانید به منوی اصلی بازگردید


☑️ برای تعییر یا تکمیل اطلاعات میتوانید از دکمه های زیر استفاده کنید 👇🏻
",
            'reply_to_message_id' => $id,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "✏️ ویرایش موقعیت مکانی"]
                    ],
                    [
                        ['text' => "✏️ ویرایش نام"], ['text' => "✏️ ویرایش جنسیت"]
                    ],
                    [
                        ['text' => "✏️ ویرایش شهر"], ['text' => "✏️ ویرایش سن"], ['text' => "✏️ ویرایش عکس"]
                    ],
                    [
                        ['text' => "🏛 خانه"], ['text' => "✏️ ویرایش بیو"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
} elseif (in_array($textmassage, array("👸🏻 با دختر", "🤴🏻 با پسر", "🗣 با همسن", "🏢 با همشهری", "📍 چت با افراد نزیک با GPS")) && $tc == "private") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$from_id"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
               if ($user["sex"] != true) {
                jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "
🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [

                    [
                        ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'setsex' WHERE id = '$from_id' LIMIT 1");
        
          }
          else{
                  if ($user["city"] != true) {
              jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "❌ هنوز پروفایلت مثل سن و شهر مشخص نشده 
                    
🎊 لطفا سن خود را وارد نمایید
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'setold' WHERE id = '$from_id' LIMIT 1");
    }
    else{
        if ($user["coin"] > 0) {
            switch ($textmassage) {
                case "👸🏻 با دختر":
                    $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
                    $userchat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                    if ($userchat["sex"] == "دختر") {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        jijibot('sendmessage', [
                            'chat_id' => $chat["user_id"],
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                        $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                      
                        $time = date("H:i:s", strtotime("+10 seconds"));
                        $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                        $pluscoin = $user["coin"] - 1;
                        $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                        $pluscoin = $side["coin"] - 1;
                        $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
                        $find = true;
                    } else {
                        $alluser = mysqli_query($connect, "select * from chatvip");
                        while ($getuser = mysqli_fetch_assoc($alluser)) {
                            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                            if ($side["sex"] == "دختر" and $user["{$getuser["type"]}"] == $getuser["stats"]) {
                                jijibot('sendmessage', [
                                    'chat_id' => $chat_id,
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'reply_to_message_id' => $message_id,
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                jijibot('sendmessage', [
                                    'chat_id' => $getuser["user_id"],
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";

                                
                                $time = date("H:i:s", strtotime("+10 seconds"));
                                $connect->query("DELETE FROM chatvip WHERE user_id = '{$getuser["user_id"]}' LIMIT 1");
                                $pluscoin = $user["coin"] - 1;
                                $connect->query("UPDATE user SET side = '{$getuser["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                                $pluscoin = $side["coin"] - 1;
                                $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$getuser["user_id"]}' LIMIT 1");
                                $find = true;
                                break;
                            }
                        }
                    }
                    if ($find != true) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "`🔍 شما در صف انتظار برای چت با دختر قرار دارید !`


🌟 `سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ... `",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ لغو جست و جو"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("INSERT INTO chatvip (user_id , type , stats) VALUES ('$from_id' , 'sex' , 'دختر')");
                    }
                    break;
                case "🤴🏻 با پسر":
                    $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
                    $userchat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                    if ($userchat["sex"] == "پسر") {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        jijibot('sendmessage', [
                            'chat_id' => $chat["user_id"],
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                        $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                       
                        $time = date("H:i:s", strtotime("+10 seconds"));
                        $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                        $pluscoin = $user["coin"] - 1;
                        $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                        $pluscoin = $side["coin"] - 1;
                        $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
                        $find = true;
                    } else {
                        $alluser = mysqli_query($connect, "select * from chatvip");
                        while ($getuser = mysqli_fetch_assoc($alluser)) {
                            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                            if ($side["sex"] == "پسر" and $user["{$getuser["type"]}"] == $getuser["stats"]) {
                                jijibot('sendmessage', [
                                    'chat_id' => $chat_id,
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'reply_to_message_id' => $message_id,
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                jijibot('sendmessage', [
                                    'chat_id' => $getuser["user_id"],
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                                
                                $time = date("H:i:s", strtotime("+10 seconds"));
                                $connect->query("DELETE FROM chatvip WHERE user_id = '{$getuser["user_id"]}' LIMIT 1");
                                $pluscoin = $user["coin"] - 1;
                                $connect->query("UPDATE user SET side = '{$getuser["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                                $pluscoin = $side["coin"] - 1;
                                $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$getuser["user_id"]}' LIMIT 1");
                                $find = true;
                                break;
                            }
                        }
                    }

                    if ($find != true) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "`🔍 شما در صف انتظار برای چت با پسر قرار دارید !`


`🌟 سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ...`",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ لغو جست و جو"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("INSERT INTO chatvip (user_id , type , stats) VALUES ('$from_id' , 'sex' , 'پسر')");
                    }
                    break;
                case "🗣 با همسن":
                    $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
                    $userchat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                    if ($userchat["old"] == $user["old"]) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        jijibot('sendmessage', [
                            'chat_id' => $chat["user_id"],
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                        $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                        
                        $time = date("H:i:s", strtotime("+10 seconds"));
                        $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                        $pluscoin = $user["coin"] - 1;
                        $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                        $pluscoin = $side["coin"] - 1;
                        $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
                        $find = true;
                    } else {
                        $alluser = mysqli_query($connect, "select * from chatvip");
                        while ($getuser = mysqli_fetch_assoc($alluser)) {
                            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                            if ($side["old"] == $user["old"] and $user["{$getuser["type"]}"] == $getuser["stats"]) {
                                jijibot('sendmessage', [
                                    'chat_id' => $chat_id,
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'reply_to_message_id' => $message_id,
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                jijibot('sendmessage', [
                                    'chat_id' => $getuser["user_id"],
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                               
                                $time = date("H:i:s", strtotime("+10 seconds"));
                                $connect->query("DELETE FROM chatvip WHERE user_id = '{$getuser["user_id"]}' LIMIT 1");
                                $pluscoin = $user["coin"] - 1;
                                $connect->query("UPDATE user SET side = '{$getuser["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                                $pluscoin = $side["coin"] - 1;
                                $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$getuser["user_id"]}' LIMIT 1");
                                $find = true;
                                break;
                            }
                        }
                    }

                    if ($find != true) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "`🔍 شما در صف انتظار برای چت با همسن قرار دارید !`


`🌟 سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ...`",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ لغو جست و جو"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("INSERT INTO chatvip (user_id , type , stats) VALUES ('$from_id' , 'old' , '{$user["old"]}')");
                    }
                    break;
                case "🏢 با همشهری":
                    $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
                    $userchat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                    if ($userchat["city"] == $user["city"]) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        jijibot('sendmessage', [
                            'chat_id' => $chat["user_id"],
                            'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 پایان چت"]
                                    ],
                                    [
                                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                    ],
                                    [
                                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                        $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                        
                        $time = date("H:i:s", strtotime("+10 seconds"));
                        $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                        $pluscoin = $user["coin"] - 1;
                        $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                        $pluscoin = $side["coin"] - 1;
                        $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
                        $find = true;
                    } else {
                        $alluser = mysqli_query($connect, "select * from chatvip");
                        while ($getuser = mysqli_fetch_assoc($alluser)) {
                            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                            if ($side["city"] == $user["city"] and $user["{$getuser["type"]}"] == $getuser["stats"]) {
                                jijibot('sendmessage', [
                                    'chat_id' => $chat_id,
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'reply_to_message_id' => $message_id,
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                jijibot('sendmessage', [
                                    'chat_id' => $getuser["user_id"],
                                    'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                    'parse_mode' => 'Markdown',
                                    'reply_markup' => json_encode([
                                        'keyboard' => [
                                            [
                                                ['text' => "💬 پایان چت"]
                                            ],
                                            [
                                                ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                            ],
                                            [
                                                ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                            ],
                                        ],
                                        'resize_keyboard' => true
                                    ])
                                ]);
                                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                               
                                $time = date("H:i:s", strtotime("+10 seconds"));
                                $connect->query("DELETE FROM chatvip WHERE user_id = '{$getuser["user_id"]}' LIMIT 1");
                                $pluscoin = $user["coin"] - 1;
                                $connect->query("UPDATE user SET side = '{$getuser["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                                $pluscoin = $side["coin"] - 1;
                                $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$getuser["user_id"]}' LIMIT 1");
                                $find = true;
                                break;
                            }
                        }
                    }
                    if ($find != true) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "`🔍 شما در صف انتظار برای چت با همشهری قرار دارید !`


`🌟 سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ...`",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "❌ لغو جست و جو"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $connect->query("INSERT INTO chatvip (user_id , type , stats) VALUES ('$from_id' , 'city' , '{$user["city"]}')");
                    }
                    break;
                case "📍 چت با افراد نزیک با GPS":
                    if ($user["gps"] == true) {
                        $chat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT user_id FROM chat WHERE user_id NOT IN ('$from_id') LIMIT 1"));
                        $userchat = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                        if ($userchat["city"] == $user["city"]) {
                            jijibot('sendmessage', [
                                'chat_id' => $chat_id,
                                'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                'reply_to_message_id' => $message_id,
                                'parse_mode' => 'Markdown',
                                'reply_markup' => json_encode([
                                    'keyboard' => [
                                        [
                                            ['text' => "💬 پایان چت"]
                                        ],
                                        [
                                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                        ],
                                        [
                                            ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                        ],
                                    ],
                                    'resize_keyboard' => true
                                ])
                            ]);
                            jijibot('sendmessage', [
                                'chat_id' => $chat["user_id"],
                                'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                'parse_mode' => 'Markdown',
                                'reply_markup' => json_encode([
                                    'keyboard' => [
                                        [
                                            ['text' => "💬 پایان چت"]
                                        ],
                                        [
                                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                        ],
                                        [
                                            ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                        ],
                                    ],
                                    'resize_keyboard' => true
                                ])
                            ]);
                            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$chat["user_id"]}' LIMIT 1"));
                            $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                            
                            $time = date("H:i:s", strtotime("+10 seconds"));
                            $connect->query("DELETE FROM chat WHERE user_id = '{$chat["user_id"]}' LIMIT 1");
                            $pluscoin = $user["coin"] - 1;
                            $connect->query("UPDATE user SET side = '{$chat["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                            $pluscoin = $side["coin"] - 1;
                            $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$chat["user_id"]}' LIMIT 1");
                            $find = true;
                        } else {
                            $alluser = mysqli_query($connect, "select * from chatvip");
                            while ($getuser = mysqli_fetch_assoc($alluser)) {
                                $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                if ($side["city"] == $user["city"] and $user["{$getuser["type"]}"] == $getuser["stats"]) {
                                    jijibot('sendmessage', [
                                        'chat_id' => $chat_id,
                                        'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                        'reply_to_message_id' => $message_id,
                                        'parse_mode' => 'Markdown',
                                        'reply_markup' => json_encode([
                                            'keyboard' => [
                                                [
                                                    ['text' => "💬 پایان چت"]
                                                ],
                                                [
                                                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                                ],
                                                [
                                                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                                ],
                                            ],
                                            'resize_keyboard' => true
                                        ])
                                    ]);
                                    jijibot('sendmessage', [
                                        'chat_id' => $getuser["user_id"],
                                        'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                                        'parse_mode' => 'Markdown',
                                        'reply_markup' => json_encode([
                                            'keyboard' => [
                                                [
                                                    ['text' => "💬 پایان چت"]
                                                ],
                                                [
                                                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                                                ],
                                                [
                                                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                                                ],
                                            ],
                                            'resize_keyboard' => true
                                        ])
                                    ]);
                                    $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$getuser["user_id"]}' LIMIT 1"));
                                    $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
                                    
                                    $time = date("H:i:s", strtotime("+10 seconds"));
                                    $connect->query("DELETE FROM chatvip WHERE user_id = '{$getuser["user_id"]}' LIMIT 1");
                                    $pluscoin = $user["coin"] - 1;
                                    $connect->query("UPDATE user SET side = '{$getuser["user_id"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$from_id' LIMIT 1");
                                    $pluscoin = $side["coin"] - 1;
                                    $connect->query("UPDATE user SET side = '$from_id' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$getuser["user_id"]}' LIMIT 1");
                                    $find = true;
                                    break;
                                }
                            }
                        }
                        if ($find != true) {
                            jijibot('sendmessage', [
                                'chat_id' => $chat_id,
                                'text' => "`🔍 شما در صف انتظار برای چت با اطرافیان قرار دارید !`


`🌟 سیستم خودکار شما را به اولین کاربر متصل خواهد کرد لطفا منتظر باشید ...`",
                                'reply_to_message_id' => $message_id,
                                'parse_mode' => 'Markdown',
                                'reply_markup' => json_encode([
                                    'keyboard' => [
                                        [
                                            ['text' => "❌ لغو جست و جو"]
                                        ],

                                    ],
                                    'resize_keyboard' => true
                                ])
                            ]);
                            $connect->query("INSERT INTO chatvip (user_id , type , stats) VALUES ('$from_id' , 'city' , '{$user["city"]}')");
                        }
                    } else {

                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "❗️ `موقعیت مکانی شما هنوز تنظیم نشده است . امکان استفاده از چت با GPS وجود ندارد`
	
ℹ️ لطفا با استفاده از دکمه '👤 پروفایل' سپس با استفاده از دکمه '✏️ ویرایش موقعیت مکانی' اقدام به کامل کردن موقعیت خود  در ربات کنید
",
                            'reply_to_message_id' => $message_id,
                            'parse_mode' => 'Markdown',
                            'disable_web_page_preview' => true,
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                                    ],
                                    [
                                        ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                                    ],
                                    [
                                        ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                                    ],
                                    [
                                        ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                                    ],
                                    [
                                        ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                                    ],
                                    [
                                        ['text' => "📍 چت با افراد نزیک با GPS"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                    }
                    break;
            }
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "⚠️ تعداد سکه شما کافی نمیباشد !
ℹ️ `برای شروع چت حداقل یک سکه نیاز دارید`

",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
        }
    }
          }
    } else {
        jijibot('sendmessage', [
            "chat_id" => $chat_id,
            "text" => "$first_name عزیز 😄
💬 برای استفاده از ربات $nameebot بات ابتدا باید وارد کانال 《 $channelname 》 شوید 👇🏻
		
📣 @$channel 📣 @$channel
📣 @$channel 📣 @$channel

☑️ بعد از عضویت بر روی دکمه عضو شدم ضربه بزنید تا ربات برای شما فعال شود",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "🔔 عضویت در کانال", 'url' => "https://t.me/$channel"]
                    ],
                    [
                        ['text' => "📢 عضو شدم", 'callback_data' => 'join']
                    ],
                ]
            ])
        ]);
    }
}  elseif ($textmassage == "💬 درخواست چت" && $tc == "private") {
    if ($user["coin"] > 0) {
        $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$user["side"]}' LIMIT 1"));
        $all = explode("^", $userside["blocklist"]);
        if (!in_array($from_id, $all)) {
            if ($userside["step"] != "chat") {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ `درخواست چت شما ارسال شد منتظر دریافت پاسخ باشید ...`
	
ℹ️ در صورت پذیرش درخواست از سمت طرف مقابل گفت و گو اغاز خواهد شد
❗️ درصورتی که شما در هنگام پذیرش درخواست از سمت طرف مقابل در گفت و گو باشید درخواست رد خواهد شد",
                    'reply_to_message_id' => $message_id,
                    'parse_mode' => 'Markdown',
                ]);
                jijibot('sendmessage', [
                    'chat_id' => $user["side"],
                    'text' => "💬 درخواست چت از طرف '{$user["name"]}'
	
برای مدیریت درخواست از دکمه های زیر استفاده کنید 👇🏻",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => "✅ قبول درخواست", 'callback_data' => "okchat"], ['text' => "❌ لغو درخواست", 'callback_data' => "nochat"]
                            ],
                        ]
                    ])
                ]);
                $connect->query("UPDATE user SET stats = '$from_id' WHERE id = '{$user["side"]}' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "`❗️ کاربر مقابل در حال گفت و گو میباشد و امکان ارسال درخواست چت وجود ندارد`
	
🔘 لطفا دقایقی دیگر یا ساعاتی دیگر نسبت به ارسال درخواست اقدام کنید .
🗣 شما میتواند با اضافه کردن کاربر به لیست دوستان در زمان های بعدی درخواست ارسال کنید",
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message_id,
                ]);
            }
        } else {
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`❗️ شما در بلاک لیست کاربر مقابل قرار دارد و امکان ارسال درخواست چه به طرف مقابل وجود ندارد .`",
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $message_id,
            ]);
        }
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ `تعداد سکه شما کافی نمیباشد !`
ℹ️ برای شروع چت حداقل یک سکه نیاز دارید

🌟 با بازگشت به منوی اصلی 
",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'Markdown',
        ]);
    }
} elseif ($textmassage == "❌ لغو ارسال گزارش" && $tc == "private") {
    if ($user["stats"] != "list") {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "❌ ارسال گزارش برای کاربر لغو شد

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                    ],
                    [
                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                    ],
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✅ به منوی کاربری بازگشتید

🌟 در مورد کاربر چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "💬 درخواست چت"]
                    ],
                    [
                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                    ],
                    [
                        ['text' => "❌ حذف"], ['text' => "👨‍👧‍👧 دوستی"]
                    ],
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }
} elseif ($textmassage == "❌ لغو ارسال پیام" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "❌ ارسال پیام برای کاربر لغو شد !

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 درخواست چت"]
                ],
                [
                    ['text' => "🛑 گزارش تخلف"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "🗑 حذف لیست" && $tc == "private") {
    switch ($user["step"]) {
        case "frind":
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`❗️ ایا از حذف کامل لیست دوستان خود اطمینان دارد ؟ در صورت تایید این رخداد غیر قابل بازگشت است`",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "✅ بله", 'callback_data' => "ydel"], ['text' => "❌ خیر", 'callback_data' => "nodel"]
                        ],
                    ]
                ])
            ]);
            break;
        case "block":
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "`❗️ ایا از حذف کامل بلاک لیست خود اطمینان دارد ؟ در صورت تایید این رخداد غیر قابل بازگشت است`",
                'reply_to_message_id' => $message_id,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "✅ بله", 'callback_data' => "ydel"], ['text' => "❌ خیر", 'callback_data' => "nodel"]
                        ],
                    ]
                ])
            ]);
            break;
    }
} elseif ($textmassage == "❌ حذف" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✅ `کاربر با موفقیت از لیست حذف شد` 
	
🔘 به منوی اصلی بازگشتید 
🌟 چه کاری برات انجام بدم ؟ از منوی پایین انتخاب کن 👇🏻",
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
    $str = str_replace("{$user["side"]}^", "", $user["frindlist"]);
    $connect->query("UPDATE user SET frindlist = '$str' WHERE id = '$from_id' LIMIT 1");
    $str = str_replace("{$user["side"]}^", "", $user["blocklist"]);
    $connect->query("UPDATE user SET blocklist = '$str' WHERE id = '$from_id' LIMIT 1");
} elseif (in_array($textmassage, array("✏️ ویرایش موقعیت مکانی", "✏️ ویرایش نام", "✏️ ویرایش جنسیت", "✏️ ویرایش شهر", "✏️ ویرایش سن", "✏️ ویرایش عکس", "✏️ ویرایش بیو"))) {
    if ($user["coin"] > 0) {
        switch ($textmassage) {
            case "✏️ ویرایش موقعیت مکانی":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ برای ویرایش موقعیت مکانی خود از دکمه زیر استفاده کنید
❗️ توجه کنید حتما برای ارسال موقعیت مکانی GPS [مکان] گوشی خود را روشن کنید

ℹ️ در صورتی که در این زمینه نیاز به راهنما دارید میتوانید از آموزش زیر استفاده کنید
⚠️ توجه کنید پس از روشن کردن GPS پانزده ثانیه صبر کنید سپس روی '🏢 ارسال موقعیت مکانی'  ضربه بزنید 
❕ توجه کنید اگر موقعیت مکانی شما پیدا نشد از انتخاب دستی استفاده کنید

",
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message_id,
                    'disable_web_page_preview' => true,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🏢 ارسال موقعیت مکانی", "request_location" => true]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectlocation' WHERE id = '$from_id' LIMIT 1");
                break;
            case "✏️ ویرایش نام":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا نام مورد نظر را ارسال کنید
	
ℹ️ میتوانید نام را به فارسی یا انگلیسی وارد کنید یا از دکمه '🗣 نام تلگرام' استفاده کنید 👇🏻
❗️ در صورت استفاده از دکمه از نام فعلی شما در تلگرام استفاده خواهد شد",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🗣 نام تلگرام"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectname' WHERE id = '$from_id' LIMIT 1");
                break;
            case "✏️ ویرایش جنسیت":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا جنیست خود را انتخاب کنید 👇🏻",
                    'reply_markup' => json_encode([
                        'keyboard' => [

                            [
                                ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectsex' WHERE id = '$from_id' LIMIT 1");
                break;
            case "✏️ ویرایش شهر":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا استان محل سکونت خود را انتخاب نمایید 👇🏻 👇🏻

ℹ️ توجه کنید در صورتی که خارج از ایران زندگی میکنید از دکمه آخر استفاده کنید",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                            [
                                ['text' => "آذربایجان شرقی"], ['text' => "اردبیل"], ['text' => "اصفهان"]
                            ],
                            [
                                ['text' => "آذربایجان غربی"], ['text' => "البرز"], ['text' => "بوشهر"]
                            ],
                            [
                                ['text' => "چهارمحال و بختیاری"], ['text' => "تهران"], ['text' => "ایلام"]
                            ],
                            [
                                ['text' => "خراسان جنوبی"], ['text' => "خوزستان"], ['text' => "زنجان"]
                            ],
                            [
                                ['text' => "خراسان شمالی"], ['text' => "سمنان"], ['text' => "فارس"]
                            ],
                            [
                                ['text' => "خراسان رضوی"], ['text' => "قزوین"], ['text' => "قم"]
                            ],
                            [
                                ['text' => "سیستان و بلوچستان"], ['text' => "کردستان"], ['text' => "کرمان"]
                            ],
                            [
                                ['text' => "کهگیلویه و بویراحمد"], ['text' => "کرمانشاه"], ['text' => "گیلان"]
                            ],
                            [
                                ['text' => "گلستان"], ['text' => "لرستان"], ['text' => "مازندران"]
                            ],
                            [
                                ['text' => "مرکزی"], ['text' => "هرمزگان"], ['text' => "همدان"], ['text' => "یزد"]
                            ],
                            [
                                ['text' => "خارج از کشور"], ['text' => "موارد دیگر"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectcity' WHERE id = '$from_id' LIMIT 1");
                break;
            case "✏️ ویرایش سن":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا سن خود را وارد نمایید
	
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectold' WHERE id = '$from_id' LIMIT 1");
                break;
            case "✏️ ویرایش عکس":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا عکس پروفایل خود در ربات را ارسال نمایید 👇🏻
	
ℹ️ میتوانید از دکمه های '📸 عکس تلگرام' و '❌ بدون عکس' نیز استفاده کنید
❗️ در صورت استفاده از دکمه ی عکس تلگرام از عکس پروفایل فعلی شما در تلگرام استفاده خواهد شد",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "📸 عکس تلگرام"], ['text' => "❌ بدون عکس"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectphoto' WHERE id = '$from_id' LIMIT 1");
                break;

            case "✏️ ویرایش بیو":
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا متن بیو [درباره] خود را ارسال نمایید 👇🏻
	
❗️ توجه کنید متن درباره شما حداکثر میتوانید 80 کاراکتر یا حرف باشد !
🌟 برای تنظیم بیو خود میتوانید از دکمه ی 'ℹ️ بیو تلگرام' نیز استفاده کنید
❗️ در صورت استفاده از دکمه از بیو فعلی شما در تلگرام استفاده خواهد شد",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "ℹ️ بیو تلگرام"], ['text' => "❌ بدون بیو"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'selectbio' WHERE id = '$from_id' LIMIT 1");
                break;
        }
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ تعداد سکه شما کافی نمیباشد !
ℹ️ برای ویرایش اطلاعات باید حداقل یک سکه داشته باشید

",
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                    ],
                    [
                        ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                    ],
                    [
                        ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                    ],
                    [
                        ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                    ],
                    [
                        ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                    ],
                    [
                        ['text' => "📍 چت با افراد نزیک با GPS"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
    }
}
//panel admin
elseif ($textmassage == "/panel" and $tc == "private" and in_array($from_id, $admin)) {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📍 ادمین عزیز به پنل مدریت ربات خوش امدید",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "📍 امار ربات"]
                ],
                [
                    ['text' => "📍 ارسال به همه"], ['text' => "📍 فروارد همگانی"]
                ],
                [
                    ['text' => "📍 ارسال سکه"]
                ],
            ],
            'resize_keyboard' => true
        ])
    ]);
} elseif ($textmassage == "برگشت 🔙" and $tc == "private" and in_array($from_id, $admin)) {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📍 به منوی مدیریت بازگشتید",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "📍 امار ربات"]
                ],
                [
                    ['text' => "📍 ارسال به همه"], ['text' => "📍 فروارد همگانی"]
                ],
                [
                    ['text' => "📍 ارسال سکه"]
                ],
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "📍 ارسال سکه" and $tc == "private" and in_array($from_id, $admin)) {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📍 لطفا در خط اول ایدی عددی فرد و در خط دوم تعداد سکه را وارد کنید !
📍 برای کم کردن سکه میتواندد عدد را به منفی وارد کنید",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "برگشت 🔙"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'coinuser' WHERE id = '$from_id' LIMIT 1");
} elseif ($textmassage == "📍 امار ربات" and $tc == "private" and in_array($from_id, $admin)) {
    $alltotal = mysqli_num_rows(mysqli_query($connect, "select id from user"));
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🤖 امار ربات شما : $alltotal 👤",
    ]);
} elseif ($textmassage == "📍 ارسال به همه" and $tc == "private" and in_array($from_id, $admin)) {
    if (in_array($from_id, $admin)) {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "📍 لطفا متن یا رسانه خود را ارسال کنید [میتواند شامل عکس , گیف یا فایل باشد]  همچنین میتوانید رسانه را همراه با کشپن [متن چسپیده به رسانه ارسال کنید]",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "برگشت 🔙"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'sendtoall' WHERE id = '$from_id' LIMIT 1");
        if (mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM sendall  LIMIT 1")) != true) {
            $connect->query("INSERT INTO `sendall` (`run`, `msgid`, `step`, `text`,  `chat`, `user`) VALUES ('ok', '', '', '', '', '0')");
        }
        $connect->query("UPDATE sendall SET step = 'none' , text = '' , msgid = '' , user = '0' , chat = '' WHERE run = 'ok' LIMIT 1");
    }
} 
#========================

#=======================
elseif ($textmassage == "📍 فروارد همگانی" and $tc == "private" and in_array($from_id, $admin)) {
    if (in_array($from_id, $admin)) {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "📍 لطفا پیام خود را فوروارد کنید [پیام فوروارد شده میتوانید از شخص یا کانال باشد]",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "برگشت 🔙"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
        $connect->query("UPDATE user SET step = 'fortoall' WHERE id = '$from_id' LIMIT 1");
        if (mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM sendall  LIMIT 1")) != true) {
            $connect->query("INSERT INTO `sendall` (`run`, `msgid`, `step`, `text`,  `chat`, `user`) VALUES ('ok', '', '', '', '', '0')");
        }
        $connect->query("UPDATE sendall SET step = 'none' , text = '' , msgid = '' , user = '0' , chat = '' WHERE run = 'ok' LIMIT 1");
    }
}
//=====================================================================
elseif ($data == "join") {
    $tch = jijibot('getChatMember', ['chat_id' => "@$channel", 'user_id' => "$fromid"])->result->status;
    if ($tch == 'member' or $tch == 'creator' or $tch == 'administrator') {
        jijibot('sendmessage', [
            'chat_id' => $chatid,
            'text' => "☑️ عضویت شما تایید شد ! شما هم اکنون می توانید از امکانات ویژه ربات استفاده کنید ! 
	
💎 همین الان با انتخاب'💬 چت ناشناس' به صورت ناشناس شروع به چت کردن با یک نفر دیگه کن و شانستو امتحان کن
🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                    ],
                    [
                        ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                    ],
                    [
                        ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                    ],
                    [
                        ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                    ],
                    [
                        ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                    ],
                    [
                        ['text' => "📍 چت با افراد نزیک با GPS"]
                    ],

                ],
                'resize_keyboard' => true
            ])
        ]);
    } else {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "❌ هنوز داخل کانال @$channel عضو نیستید",
            'show_alert' => true
        ]);
    }
} elseif ($data == "recaverbaner") {
    jijibot('answercallbackquery', [
        'callback_query_id' => $membercall,
        'text' => "	⏳ در حال ساخت بنر اختصاصی شما ...",
        'show_alert' => false
    ]);
    $patch = jijibot('getfile', ['file_id' => $usercall["photo"]])->result->file_path;
    $w = 640;
    $h = 640; // original size
    $dest_path = "$fromid.png";
    $src = imagecreatefromstring(file_get_contents("https://api.telegram.org/file/bot" . API_KEY . "/$patch"));
    $newpic = imagecreatetruecolor($w, $h);
    imagealphablending($newpic, false);
    $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);
    $r = $w / 2;

    for ($x = 0; $x < $w; $x++)
        for ($y = 0; $y < $h; $y++) {
            $c = imagecolorat($src, $x, $y);
            $_x = $x - $w / 2;
            $_y = $y - $h / 2;
            if ((($_x * $_x) + ($_y * $_y)) < ($r * $r)) {
                imagesetpixel($newpic, $x, $y, $c);
            } else {
                imagesetpixel($newpic, $x, $y, $transparent);
            }
        }
    imagesavealpha($newpic, true);
    imagepng($newpic, $dest_path);
    imagedestroy($newpic);
    imagedestroy($src);
    $t = imagecreatefrompng("$fromid.png");
    $x = imagesx($t);
    $y = imagesy($t);
    $s = imagecreatetruecolor(1000, 1000);
    imagealphablending($s, false);
    imagecopyresampled(
        $s,
        $t,
        0,
        0,
        0,
        0,
        1000,
        1000,
        $x,
        $y
    );
    imagesavealpha($s, true);
    imagepng($s, "$fromid.png");
    imagedestroy($s);
    imagedestroy($t);
    $stamp = imagecreatefrompng("$fromid.png");
    $im = imagecreatefromjpeg("baner.jpg");
    $marge_right = 1450;
    $marge_bottom = 1970;
    $sx = imagesx($stamp);
    $sy = imagesy($stamp);
    imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
    header("Content-Type: image/png");
    imagejpeg($im, "$fromid.png");
    imagedestroy($im);
    $getnamesub = mb_substr("$firstname", "0", "10") . "...";
    $id = jijibot('sendphoto', [
        'chat_id' => $chatid,
        'photo' => new CURLFile("$fromid.png"),
        'caption' => "$getnamesub دعوتت کرده
💬 که وارد ربات 《 $nameebot بات 》 بشی و به صورت ناشناس با #اطرافیانت , #همسنات یا و ... چت کنی !

👇 روی لینک زیر کلیک کن
telegram.me/$usernamebot?start=in_$fromid",
    ]);
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "💬 پیام بالا حاوی لینک دعوت اختصاصی شما به ربات به همراه عکس پروفایل شماست .
	

🎁 و زمانی که تعداد زیر مجموعه های شما به 5 نفر رسید حساب شما تبدیل به ویژه خواهد شد ! این عالیه نه ؟

🌟 منتظر چی هستی ؟ همین الان بنرت رو به گروه ها و دوستات بفرست و دعوتشون کن 

💰 تعداد سکه : {$usercall["coin"]}
👤 تعداد زیرمجموعه ها : {$usercall["member"]} نفر",
        'reply_to_message_id' => $id->result->message_id,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ['text' => "🎊 سکه روزانه", 'callback_data' => "dailycoin"]
                ],
            ]
        ])
    ]);
    unlink("$fromid.png");
    $connect->query("UPDATE user SET baner = '{$id->result->photo[4]->file_id}' WHERE id = '$fromid' LIMIT 1");
} elseif ($data == "dailycoin") {
    if ($usercall["daily"] != true) {
        $pluscoin = $usercall["coin"] + 1;
        jijibot('sendmessage', [
            'chat_id' => $chatid,
            'text' => "🎉 تبریک ! سکه روزانه امروزت رو دریافت کردی
	
🌟 فردا دوباره منتظر شما هستیم ...",
            'reply_to_message_id' => $messageid,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "💰 تعداد سکه ها", 'callback_data' => "text"], ['text' => "$pluscoin", 'callback_data' => "text"]
                    ],
                ]
            ])
        ]);
        $connect->query("UPDATE user SET daily = 'true' , coin = '$pluscoin' WHERE id = '$fromid' LIMIT 1");
    } else {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "😲 سکه روزانه امروزت رو دریافت کردی ! منتظر باش فردا خبرت میکنم",
            'show_alert' => true
        ]);
    }
} elseif ($data == "like") {
    $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$usercall["side"]}' LIMIT 1"));
    $all = explode("^", $side["liker"]);
    if (!in_array($fromid, $all)) {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "👍🏻 پروفایل طرف مقابلت لایک شد",
            'show_alert' => true
        ]);
        $pluslike = $side["like"] + 1;
        jijibot('editMessageReplyMarkup', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "👍🏻 $pluslike", 'callback_data' => "like"], ['text' => "👎🏻 {$side["dislike"]}", 'callback_data' => "dislike"]
                    ],
                ]
            ])
        ]);
        $connect->query("UPDATE user SET `like` = '$pluslike' , liker = CONCAT (liker,'$fromid^') WHERE id = '{$usercall["side"]}' LIMIT 1");
    } else {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "🙃 شما قبلا نظرت رو درباره این پروفایل گفتی",
            'show_alert' => true
        ]);
    }
} elseif ($data == "dislike") {
    $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$usercall["side"]}' LIMIT 1"));
    $all = explode("^", $side["liker"]);
    if (!in_array($fromid, $all)) {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "👎🏻 پروفایل طرف مقابلت دیس لایک شد",
            'show_alert' => true
        ]);
        $pluslike = $side["dislike"] + 1;
        jijibot('editMessageReplyMarkup', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => "👍🏻 {$side["like"]}", 'callback_data' => "like"], ['text' => "👎🏻 $pluslike", 'callback_data' => "dislike"]
                    ],
                ]
            ])
        ]);
        $connect->query("UPDATE user SET `dislike` = '$pluslike' , liker = CONCAT (liker,'$fromid^') WHERE id = '{$usercall["side"]}' LIMIT 1");
    } else {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "🙃 شما قبلا نظرت رو درباره این پروفایل گفتی",
            'show_alert' => true
        ]);
    }
} elseif ($data == "endy") {
    jijibot('deletemessage', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
    ]);
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "✅ `گفت و گوی شما با طرف مقابلت توسط شما بسته شد .`

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);

    jijibot('sendmessage', [
        'chat_id' => $usercall["side"],
        'text' => "✅ `گفت و گوی شما با طرف مقابلت توسط طرف مقابلت بسته شد .`

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$fromid' LIMIT 1");
    $connect->query("UPDATE user SET step = 'none' WHERE id = '{$usercall["side"]}' LIMIT 1");
} elseif ($data == "viy") {
    jijibot('deletemessage', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
    ]);
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "✅ `گفت و گوی شما با طرف مقابلت توسط شما بسته شد .`
	
🗣 از این که به ما در گزارش کاربران متخلف یاری میرسانید مشترکیم 
🔘 لطفا به صورت توضیح کوچک یا بهتر است از اسکرین شات [عکس صحفه] برای نمایش تخلف کاربر استفاده کنید 
گزارش خود را در قالب یک پیام ارسال کنید 👇🏻",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "❌ لغو ارسال گزارش"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    jijibot('sendmessage', [
        'chat_id' => $usercall["side"],
        'text' => "✅ `گفت و گوی شما با طرف مقابلت توسط طرف مقابلت بسته شد .`

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'report' WHERE id = '$fromid' LIMIT 1");
    $connect->query("UPDATE user SET step = 'none' WHERE id = '{$usercall["side"]}' LIMIT 1");
} elseif ($data == "bly") {
    jijibot('deletemessage', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
    ]);
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "✅ گفت و گوی شما با طرف مقابلت توسط شما بسته شد .
	
⭕️ کاربر با موفقیت به بلاک لیست شما اضافه شد .
🔘 شما میتوانید در منوی اصلی ربات بلاک لیست خود را مشاهده کنید
ℹ️ کاربرانی که در لیست بلاک شما قرار دارند امکان گفت و گو و ارسال پیام به شما را ندارند

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    jijibot('sendmessage', [
        'chat_id' => $usercall["side"],
        'text' => "✅ گفت و گوی شما با طرف مقابلت توسط طرف مقابلت بسته شد .

🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                ],
                [
                    ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                ],
                [
                    ['text' => "🏛 خانه"]
                ]
            ],
            'resize_keyboard' => true
        ])
    ]);
    $connect->query("UPDATE user SET step = 'none' , blocklist = CONCAT (blocklist,'{$usercall["side"]}^') WHERE id = '$fromid' LIMIT 1");
    $connect->query("UPDATE user SET step = 'none' WHERE id = '{$usercall["side"]}' LIMIT 1");
} elseif ($data == "endn") {
    jijibot('editmessagetext', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
        'text' => "🤖 پیام سیستم 👇 
			   
`✅ درخواست شما لغو و چت بسته نشده . به احوال پرسی با طرف مقابلت ادامه بده`",
        'parse_mode' => 'Markdown',
    ]);
}  elseif ($data == "okchat") {
    if ($usercall["coin"] > 0) {
        $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$usercall["stats"]}' LIMIT 1"));
        if ($user["step"] != "chat") {
            jijibot('editmessagetext', [
                'chat_id' => $chatid,
                'message_id' => $messageid,
                'text' => "درخواست چت از طرف '{$user["name"]}' موفقیت توسط شما پذیرفته شد ✅",
            ]);
            jijibot('sendmessage', [
                'chat_id' => $usercall["stats"],
                'text' => "درخواست چت شما توسط '{$usercall["name"]}' پذیرفته شد ✅",
            ]);
            jijibot('sendmessage', [
                'chat_id' => $chatid,
                'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 پایان چت"]
                        ],
                        [
                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                        ],
                        [
                            ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                        ],
                    ],
                    'resize_keyboard' => true
                ])
            ]);
            jijibot('sendmessage', [
                'chat_id' => $usercall["stats"],
                'text' => "☑️ به کاربر متصل شدی
🗣 به طرف مقابلت سلام کن 😄

🌟 `برای مدیریت چت هم میتونی از دکمه های زیر استفاده کنی` 👇🏻",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 پایان چت"]
                        ],
                        [
                            ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                        ],
                        [
                            ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                        ],
                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '$fromid' LIMIT 1"));
            $side = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$usercall["stats"]}' LIMIT 1"));
            $bio = isset($side["bio"]) ? $side["bio"] : "تنظیم نشده !";
            $time = date("H:i:s", strtotime("+10 seconds"));
            $pluscoin = $user["coin"] - 1;
            $connect->query("UPDATE user SET side = '{$usercall["stats"]}' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '$fromid' LIMIT 1");
            $pluscoin = $side["coin"] - 1;
            $connect->query("UPDATE user SET side = '$fromid' , coin = '$pluscoin' , step = 'chat' , time = '$time' WHERE id = '{$usercall["stats"]}' LIMIT 1");
        } else {
            jijibot('answercallbackquery', [
                'callback_query_id' => $membercall,
                'text' => "😲 کاربر طرف مقابل در حال گفت و گو میباشد و امکان گفت و گو وجود ندارد . دقایقی دیگر مجدد تلاش کنید",
                'show_alert' => true
            ]);
        }
    } else {
        jijibot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "😲 تعداد سکه شما کافی نمیباشد ! برای پذیرش درخواست باید حداقل یک سکه داشته باشید . لطفا ابتدا نسبت به افزایش سکه اقدام کنید",
            'show_alert' => true
        ]);
    }
} elseif ($data == "nochat") {
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$usercall["stats"]}' LIMIT 1"));
    jijibot('sendmessage', [
        'chat_id' => $usercall["stats"],
        'text' => "❌ درخواست چت شما از طرف '{$usercall["name"]}' لغو شد",
    ]);
    jijibot('editmessagetext', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
        'text' => "✅ درخواست چت از طرف'{$user["name"]}' توسط شما لغو شد",
    ]);
} elseif ($data == "ydel") {
    switch ($usercall["step"]) {
        case "frind":
            jijibot('editmessagetext', [
                'chat_id' => $chatid,
                'message_id' => $messageid,
                'text' => "✅ `لیست دوستان شما به صورت کامل پاک شد`",
                'parse_mode' => 'Markdown',
            ]);
            $connect->query("UPDATE user SET frindlist = '' WHERE id = '$fromid' LIMIT 1");
            break;
        case "block":
            jijibot('editmessagetext', [
                'chat_id' => $chatid,
                'message_id' => $messageid,
                'text' => "✅ `بلاک لیست شما به صورت کامل پاک شد`",
                'parse_mode' => 'Markdown',
            ]);
            $connect->query("UPDATE user SET blocklist = '' WHERE id = '$fromid' LIMIT 1");
            break;
    }
    jijibot('sendmessage', [
        'chat_id' => $chatid,
        'text' => "🔘 `به منوی اصلی بازگشتید`
	
🌟 چه کاری برات انجام بدم ؟ از منوی پایین انتخاب کن 👇🏻",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
} elseif ($data == "nodel") {
    jijibot('editmessagetext', [
        'chat_id' => $chatid,
        'message_id' => $messageid,
        'text' => "✅ `درخواست شما با موفقیت لغو شد با لیست بالا میخوای چی کار کنی ؟ 👆🏻`",
        'parse_mode' => 'Markdown',
    ]);
} elseif ($data == "likeme") {
    jijibot('answercallbackquery', [
        'callback_query_id' => $membercall,
        'text' => "😲 شما نمیتوانید عکس پروفایل خود را لایک کنید !",
        'show_alert' => true
    ]);
} elseif ($data == "text") {
    jijibot('answercallbackquery', [
        'callback_query_id' => $membercall,
        'text' => "😆 این دکمه فقط جهت نمایش اطلاعات است !",
        'show_alert' => true
    ]);
}
//========================================================================
elseif (preg_match('/set(.*)/', $user["step"])) {
    switch ($user["step"]) {
        case "setsex":
            if (in_array($textmassage, array("👸🏻 دخترم", "🤴🏻 پسرم"))) {
                $str = str_replace(["👸🏻 دخترم", "🤴🏻 پسرم"], ["دختر", "پسر"], $textmassage);
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ جنسیت شما با موفقیت $str ذخیره شد .

🎊 لطفا سن خود را وارد نمایید
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'setold' , sex = '$str' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "😄 سلام $first_name
	
به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 خوب اول باید اطلاعات خود را ثبت کنی ! جنسیت خودت رو انتخاب کن ؟ 👇🏻",
                    'reply_markup' => json_encode([
                        'keyboard' => [

                            [
                                ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "setold":
            $str = str_replace("+", "", $textmassage);
            if ($str >= 7 and $str <= 90) {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ سن شما با موفقیت '$str' سال ذخیره شد .

🏢 لطفا استان محل سکونت خود را انتخاب نمایید 👇🏻
ℹ️ توجه کنید در صورتی که خارج از ایران زندگی میکنید از دکمه آخر استفاده کنید",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "آذربایجان شرقی"], ['text' => "اردبیل"], ['text' => "اصفهان"]
                            ],
                            [
                                ['text' => "آذربایجان غربی"], ['text' => "البرز"], ['text' => "بوشهر"]
                            ],
                            [
                                ['text' => "چهارمحال و بختیاری"], ['text' => "تهران"], ['text' => "ایلام"]
                            ],
                            [
                                ['text' => "خراسان جنوبی"], ['text' => "خوزستان"], ['text' => "زنجان"]
                            ],
                            [
                                ['text' => "خراسان شمالی"], ['text' => "سمنان"], ['text' => "فارس"]
                            ],
                            [
                                ['text' => "خراسان رضوی"], ['text' => "قزوین"], ['text' => "قم"]
                            ],
                            [
                                ['text' => "سیستان و بلوچستان"], ['text' => "کردستان"], ['text' => "کرمان"]
                            ],
                            [
                                ['text' => "کهگیلویه و بویراحمد"], ['text' => "کرمانشاه"], ['text' => "گیلان"]
                            ],
                            [
                                ['text' => "گلستان"], ['text' => "لرستان"], ['text' => "مازندران"]
                            ],
                            [
                                ['text' => "مرکزی"], ['text' => "هرمزگان"], ['text' => "همدان"], ['text' => "یزد"]
                            ],
                            [
                                ['text' => "خارج از کشور"], ['text' => "موارد دیگر"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'setcity' , old = '$str' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "⚠️ خطا در سن وارد شده ! سن شما معتبر نیست

🎊 لطفا سن خود را وارد نمایید
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻
❗️ توجه کنید که سن شما حداکثر میتوانید 90 سال و حداقل 7 سال باشد",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "setcity":
            $cities = [
  'آذربایجان شرقی', 'اردبیل', 'اصفهان', 
  'آذربایجان غربی', 'البرز', 'بوشهر',
  'چهارمحال و بختیاری', 'تهران', 'ایلام',
  'خراسان جنوبی', 'خوزستان', 'زنجان',
  'خراسان شمالی', 'سمنان', 'فارس',
  'خراسان رضوی', 'قزوین', 'قم',
  'سیستان و بلوچستان', 'کردستان', 'کرمان',
  'کهگیلویه و بویراحمد', 'کرمانشاه', 'گیلان', 
  'گلستان', 'لرستان', 'مازندران',
  'مرکزی', 'هرمزگان', 'همدان', 'یزد',
  'خارج از کشور', 'موارد دیگر'
];
         if(!in_array($textmassage,$cities)) {
              jijibot('sendmessage', [
                'chat_id' => $chat_id,
               'text' => "❌ شهر مورد نظر شما باید حتما از کیبورد زیر باشد",
                'reply_to_message_id' => $message_id,
              'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "آذربایجان شرقی"], ['text' => "اردبیل"], ['text' => "اصفهان"]
                            ],
                            [
                                ['text' => "آذربایجان غربی"], ['text' => "البرز"], ['text' => "بوشهر"]
                            ],
                            [
                                ['text' => "چهارمحال و بختیاری"], ['text' => "تهران"], ['text' => "ایلام"]
                            ],
                            [
                                ['text' => "خراسان جنوبی"], ['text' => "خوزستان"], ['text' => "زنجان"]
                            ],
                            [
                                ['text' => "خراسان شمالی"], ['text' => "سمنان"], ['text' => "فارس"]
                            ],
                            [
                                ['text' => "خراسان رضوی"], ['text' => "قزوین"], ['text' => "قم"]
                            ],
                            [
                                ['text' => "سیستان و بلوچستان"], ['text' => "کردستان"], ['text' => "کرمان"]
                            ],
                            [
                                ['text' => "کهگیلویه و بویراحمد"], ['text' => "کرمانشاه"], ['text' => "گیلان"]
                            ],
                            [
                                ['text' => "گلستان"], ['text' => "لرستان"], ['text' => "مازندران"]
                            ],
                            [
                                ['text' => "مرکزی"], ['text' => "هرمزگان"], ['text' => "همدان"], ['text' => "یزد"]
                            ],
                            [
                                ['text' => "خارج از کشور"], ['text' => "موارد دیگر"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $connect->query("UPDATE user SET step = 'setcity' WHERE id = '$from_id' LIMIT 1");
            }
            else{
            $getuserphoto = jijibot('getUserProfilePhotos', ['user_id' => "$from_id"])->result->photos[0][2]->file_id;
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "✅ شهر شما با موفقیت '$textmassage' ذخیره شد.

☑️ ثبت اطلاعات اولیه شما با موفقیت انجام شد به ربات 《 $nameebot بات 》 خوش آمدید .
❗️ توجه داشته باشید ! 'نام' , 'عکس پروفایل' و 'بیو' [درباره] شما از روی اکانت تلگرام شما ذخیره شد .
ℹ️ شما میتوانید با مراجعه به قسمت '👤 پروفایل' نسبت به تکمیل یا تغییر اطلاعات اقدام کنید

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻
💎 همین الان با انتخاب'💬 چت ناشناس' به صورت ناشناس شروع به چت کردن با یک نفر دیگه کن و شانستو امتحان کن",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $bio = getbio($username);
            $connect->query("UPDATE user SET step = 'none' , city = '" . mb_substr($textmassage, 0, 50) . "' , name = '" . mb_substr($first_name, 0, 50) . "' , photo = '$getuserphoto' , bio = '$bio' WHERE id = '$from_id' LIMIT 1");
            break;
    }
    }
} elseif (preg_match('/select(.*)/', $user["step"])) {
    switch ($user["step"]) {
        case "selectlocation":
            if (isset($update->message->location)) {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ موقعیت مکانی شما با موفقیت ویرایش شد !


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , gps = 'true' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ برای ویرایش موقعیت مکانی خود از دکمه زیر استفاده کنید
❗️ توجه کنید حتما برای ارسال موقعیت مکانی GPS [مکان] گوشی خود را روشن کنید

ℹ️ در صورتی که در این زمینه نیاز به راهنما دارید میتوانید از آموزش زیر استفاده کنید
⚠️ توجه کنید پس از روشن کردن GPS پانزده ثانیه صبر کنید سپس روی '🏢 ارسال موقعیت مکانی'  ضربه بزنید 
❕ توجه کنید اگر موقعیت مکانی شما پیدا نشد از انتخاب دستی استفاده کنید
",
                    'parse_mode' => 'Markdown',
                    'reply_to_message_id' => $message_id,
                    'disable_web_page_preview' => true,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🏢 ارسال موقعیت مکانی", "request_location" => true]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "selectname":
            if (mb_strlen($textmassage) <= 50 and isset($textmassage)) {
                $str = str_replace(["🗣 نام تلگرام"], ["$first_name"], $textmassage);
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ نام شما '$str' ذخیره شد


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , name = '" . mb_substr($str, 0, 50) . "' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "⚠️ نام شما بسیار بزرگ یا تعریف نشده است !
✏️ لطفا نام مورد نظر را ارسال کنید
	
ℹ️ میتوانید نام را به فارسی یا انگلیسی وارد کنید یا از دکمه '🗣 نام تلگرام' استفاده کنید 👇🏻
❗️ در صورت استفاده از دکمه از نام فعلی شما در تلگرام استفاده خواهد شد",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🗣 نام تلگرام"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "selectsex":
            if (in_array($textmassage, array("👸🏻 دخترم", "🤴🏻 پسرم"))) {
                $str = str_replace(["👸🏻 دخترم", "🤴🏻 پسرم"], ["دختر", "پسر"], $textmassage);
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ جنسیت شما با موفقیت $str ذخیره شد .


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , sex = '$str' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✏️ لطفا جنیست خود را انتخاب کنید 👇🏻",
                    'reply_markup' => json_encode([
                        'keyboard' => [

                            [
                                ['text' => "👸🏻 دخترم"], ['text' => "🤴🏻 پسرم"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "selectcity":
            jijibot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "✅ شهر شما با موفقیت $textmassage ذخیره شد .


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                        ],
                        [
                            ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                        ],
                        [
                            ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                        ],
                        [
                            ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                        ],
                        [
                            ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                        ],
                        [
                            ['text' => "📍 چت با افراد نزیک با GPS"]
                        ],

                    ],
                    'resize_keyboard' => true
                ])
            ]);
            $pluscoin = $user["coin"] - 1;
            $connect->query("UPDATE user SET step = 'none' , city = '" . mb_substr($textmassage, 0, 50) . "' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            break;
        case "selectold":
            $str = str_replace("+", "", $textmassage);
            if ($str >= 7 and $str <= 90) {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ سن شما با موفقیت '$str' سال ذخیره شد .


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , old = '$str' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "⚠️ خطا در سن وارد شده !
✏️ لطفا سن خود را وارد نمایید
	
ℹ️ میتونی سن خودت رو به صورت عداد لاتین یا از کیبورد زیر وارد کنی 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                            [
                                ['text' => "10"], ['text' => "11"], ['text' => "12"], ['text' => "13"], ['text' => "14"]
                            ],
                            [
                                ['text' => "15"], ['text' => "16"], ['text' => "17"], ['text' => "18"], ['text' => "19"]
                            ],
                            [
                                ['text' => "20"], ['text' => "21"], ['text' => "22"], ['text' => "23"], ['text' => "24"]
                            ],
                            [
                                ['text' => "25"], ['text' => "26"], ['text' => "27"], ['text' => "28"], ['text' => "29"]
                            ],
                            [
                                ['text' => "30"], ['text' => "31"], ['text' => "32"], ['text' => "33"], ['text' => "34"]
                            ],
                            [
                                ['text' => "35"], ['text' => "36"], ['text' => "37"], ['text' => "38"], ['text' => "39"]
                            ],
                            [
                                ['text' => "40"], ['text' => "41"], ['text' => "42"], ['text' => "43"], ['text' => "44"]
                            ],
                            [
                                ['text' => "45"], ['text' => "46"], ['text' => "47"], ['text' => "48"], ['text' => "49"]
                            ],
                            [
                                ['text' => "+ 50"], ['text' => "- 9"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
        case "selectphoto":
            $photo = $update->message->photo;
            if (isset($photo)) {
                $photofile = $photo[count($photo) - 1]->file_id;
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ عکس پروفایل شما با موفقیت تنظیم شد

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , photo = '$photofile' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                if ($textmassage == "📸 عکس تلگرام" or $textmassage == "❌ بدون عکس") {
                    $profilephoto = jijibot('getUserProfilePhotos', ['user_id' => "$from_id"])->result->photos[0][2]->file_id;
                    if ($textmassage == "📸 عکس تلگرام") {
                        if (!isset($profilephoto)) {
                            jijibot('sendmessage', [
                                'chat_id' => $chat_id,
                                'text' => "⚠️ خطا در عکس پروفایل ! شما عکس پروفایلی برای تنظیم ندارید .
❗️ ابتدا نسبت به تنظیم عکس پروفایل اقدام کنید سپس از دکمه استفاده کنید .
✏️ لطفا عکس پروفایل خود در ربات را ارسال نمایید 👇🏻
	
ℹ️ میتوانید از دکمه های '📸 عکس تلگرام' و '❌ بدون عکس' نیز استفاده کنید
❗️ در صورت استفاده از دکمه ی عکس تلگرام از عکس پروفایل فعلی شما در تلگرام استفاده خواهد شد",
                                'reply_to_message_id' => $message_id,
                                'reply_markup' => json_encode([
                                    'keyboard' => [
                                        [
                                            ['text' => "📸 عکس تلگرام"], ['text' => "❌ بدون عکس"]
                                        ],
                                        [
                                            ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                                        ],
                                    ],
                                    'resize_keyboard' => true
                                ])
                            ]);
                        }
                    }
                    $str = str_replace(["📸 عکس تلگرام", "❌ بدون عکس"], ["$profilephoto", ""], $textmassage);
                    $strname = str_replace(["📸 عکس تلگرام", "❌ بدون عکس"], ["عکس پروفایل شما با توجه به عکس پروفایل تلگرام شما تنظیم شد !", "عکس پروفایل شما با موفقیت حذف شد"], $textmassage);
                    jijibot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "✅ $strname


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => json_encode([
                            'keyboard' => [
                                [
                                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                                ],
                                [
                                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                                ],
                                [
                                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                                ],
                                [
                                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                                ],
                                [
                                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                                ],
                                [
                                    ['text' => "📍 چت با افراد نزیک با GPS"]
                                ],

                            ],
                            'resize_keyboard' => true
                        ])
                    ]);
                    $pluscoin = $user["coin"] - 1;
                    $connect->query("UPDATE user SET step = 'none' , photo = '$str' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
                } else {
                    jijibot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "⚠️ خطا در عکس ارسال شده ! تنها ارسال عکس ممکن است .
✏️ لطفا عکس پروفایل خود در ربات را ارسال نمایید 👇🏻
	
ℹ️ میتوانید از دکمه های '📸 عکس تلگرام' و '❌ بدون عکس' نیز استفاده کنید
❗️ در صورت استفاده از دکمه ی عکس تلگرام از عکس پروفایل فعلی شما در تلگرام استفاده خواهد شد",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => json_encode([
                            'keyboard' => [
                                [
                                    ['text' => "📸 عکس تلگرام"], ['text' => "❌ بدون عکس"]
                                ],
                                [
                                    ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                                ],
                            ],
                            'resize_keyboard' => true
                        ])
                    ]);
                }
            }
            break;
        case "selectbio":
            if (mb_strlen($textmassage) <= 70 and isset($textmassage)) {
                if ($textmassage == "ℹ️ بیو تلگرام") {
                    $bio = getbio($username);
                    if (isset($bio)) {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "✅ متن بیو و درباره شما با وتوجه به متن بیو تلگرام شما تنظیم شد


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                            'reply_to_message_id' => $message_id,
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                                    ],
                                    [
                                        ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                                    ],
                                    [
                                        ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                                    ],
                                    [
                                        ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                                    ],
                                    [
                                        ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                                    ],
                                    [
                                        ['text' => "📍 چت با افراد نزیک با GPS"]
                                    ],

                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                        $pluscoin = $user["coin"] - 1;
                        $connect->query("UPDATE user SET step = 'none' , bio = '$bio' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
                        break;
                    } else {
                        jijibot('sendmessage', [
                            'chat_id' => $chat_id,
                            'text' => "⚠️ متن درباره ای از پروفایل تلگرام شما پیدا نشد !
✏️ لطفا متن بیو [درباره] خود را ارسال نمایید 👇🏻
	
❗️ توجه کنید متن درباره شما حداکثر میتوانید 80 کاراکتر یا حرف باشد !
🌟 برای تنظیم بیو خود میتوانید از دکمه ی 'ℹ️ بیو تلگرام' نیز استفاده کنید",
                            'reply_to_message_id' => $message_id,
                            'reply_markup' => json_encode([
                                'keyboard' => [
                                    [
                                        ['text' => "ℹ️ بیو تلگرام"], ['text' => "❌ بدون بیو"]
                                    ],
                                    [
                                        ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                                    ],
                                ],
                                'resize_keyboard' => true
                            ])
                        ]);
                    }
                }
                $bio = ($textmassage != "❌ بدون بیو") ? $textmassage : "تنظیم نشده !";
                $text = ($textmassage != "❌ بدون بیو") ? "✅ متن بیو شما با موفقیت تنظیم شد !" : "✅ متن بیو شما با موفقیت حذف شد";
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "$text


🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                            ],
                            [
                                ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                            ],
                            [
                                ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                            ],
                            [
                                ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                            ],
                            [
                                ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                            ],
                            [
                                ['text' => "📍 چت با افراد نزیک با GPS"]
                            ],

                        ],
                        'resize_keyboard' => true
                    ])
                ]);
                $pluscoin = $user["coin"] - 1;
                $connect->query("UPDATE user SET step = 'none' , bio = '$bio' , coin = '$pluscoin' WHERE id = '$from_id' LIMIT 1");
            } else {
                jijibot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "⚠️ متن ارسال شده یا پیش از اندازه بزرگ است یا تعریف نشده است !
✏️ لطفا متن بیو [درباره] خود را ارسال نمایید 👇🏻
	
❗️ توجه کنید متن درباره شما حداکثر میتوانید 80 کاراکتر یا حرف باشد !
🌟 برای تنظیم بیو خود میتوانید از دکمه ی 'ℹ️ بیو تلگرام' نیز استفاده کنید",
                    'reply_to_message_id' => $message_id,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                ['text' => "ℹ️ بیو تلگرام"], ['text' => "❌ بدون بیو"]
                            ],
                            [
                                ['text' => "🏛 خانه"], ['text' => "🔙 برگشت"]
                            ],
                        ],
                        'resize_keyboard' => true
                    ])
                ]);
            }
            break;
    }
} elseif ($user["step"] == "report" && $tc == "private") {
    if ($user["stats"] != "list") {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✅ گزارش شما با موفقیت ارسال شد . از شما تشکر میکنیم 
🗣 `مدیران ما گزارش شما را برسی میکنند و در صورت هر نوع رعایت نکردن قوانین کاربر را مسدود میکندد `

ℹ️ در صورت امکان پاسخ گزارش شما برای شما ارسال خواهد شد
🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                    ],
                    [
                        ['text' => "👨‍👧‍👧 دوستی"], ['text' => "🚫 بلاکش کن"]
                    ],
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    } else {
        jijibot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✅ گزارش شما با موفقیت ارسال شد . از شما تشکر میکنیم 
🗣 `مدیران ما گزارش شما را برسی میکنند و در صورت هر نوع رعایت نکردن قوانین کاربر را مسدود میکندد `

ℹ️ در صورت امکان پاسخ گزارش شما برای شما ارسال خواهد شد
🌟 در مورد طرف مقابلت چه کاری برات انجام بدم ؟
از منوی پایین انتخاب کن 👇🏻",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $message_id,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => "💬 درخواست چت"]
                    ],
                    [
                        ['text' => "🛑 گزارش تخلف"], ['text' => "👁 مشاهده پروفایل"]
                    ],
                    [
                        ['text' => "❌ حذف"], ['text' => "🚫 بلاکش کن"]
                    ],
                    [
                        ['text' => "🏛 خانه"]
                    ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }
    jijibot('ForwardMessage', [
        'chat_id' => $admin[0],
        'from_chat_id' => $chat_id,
        'message_id' => $message_id
    ]);
    jijibot('sendmessage', [
        'chat_id' => $admin[0],
        'text' => "📍 #گزارش
📍 از طرف : $from_id
📍 کاربر خاطی : {$user["side"]}",
        'reply_to_message_id' => $message_id,
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
}  elseif ($user["step"] == "sup" && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📍 پیام شما با موفقیت ارسال شد منتظر پاسخ پشتیبانی باشید",
    ]);
    jijibot('ForwardMessage', [
        'chat_id' => $admin[0],
        'from_chat_id' => $chat_id,
        'message_id' => $message_id
    ]);
} elseif ($user["step"] == "coinuser" && $tc == "private") {
    $all = explode("\n", $textmassage);
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "📍 تعداد $all[1] سکه به کاربر $all[0] داده شد",
    ]);
    jijibot('sendmessage', [
        'chat_id' => $all[0],
        'text' => "💰 تعداد $all[1] سکه توسط مدیریت به شما داده شد",
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    $userside = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM user WHERE id = '{$all[0]}' LIMIT 1"));
    $pluscoin = $userside["coin"] + $all[1];
    $connect->query("UPDATE user SET coin = '$pluscoin' WHERE id = '{$all[0]}' LIMIT 1");
} elseif ($user["step"] == 'sendtoall' && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام شما با موفقیت برای ارسال همگانی تنظیم شد  ✔️",
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    $connect->query("UPDATE sendall SET step = 'sendall' , text = '$textmassage' , msgid = '' WHERE run = 'ok' LIMIT 1");
} elseif ($user["step"] == 'fortoall' && $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "پیام شما با موفقیت به عنوان فوروارد همگانی تنظیم شد ✔️",
    ]);
    $connect->query("UPDATE user SET step = 'none' WHERE id = '$from_id' LIMIT 1");
    $connect->query("UPDATE sendall SET step = 'forall' , msgid = '$message_id' , chat = '$chat_id' WHERE run = 'ok' LIMIT 1");
}

//=============================================================
elseif ($update->message->text && $update->message->reply_to_message && $from_id == $admin[0] && $tc == "private") {
    jijibot('sendmessage', [
        "chat_id" => $chat_id,
        "text" => "پاسخ شما برای فرد ارسال شد ☑️"
    ]);
    jijibot('sendmessage', [
        "chat_id" => $update->message->reply_to_message->forward_from->id,
        "text" => "👮🏻 پاسخ پشتیبان برای شما : `$textmassage`",
        'parse_mode' => 'MarkDown'
    ]);
} elseif ($update->message and $tc == "private") {
    jijibot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "به ربات چت ناشناسه 《 $nameebot بات 》 خوش آمدید 🌹

🎊 با استفاده از این ربات میتونی با  جنس مخالفت , همسنت , هم شهری یا افراد نزدیک بهت حرف بزنی و دوست بشی
💑 همراه با کلی امکانات دیگ ...

🌟 به منوی اصلی ربات خوش اومدی . خوب الان از منوی پایین انتخاب کن 👇🏻",
        'reply_markup' => json_encode([
            'keyboard' => [
                [
                    ['text' => "💬 چت ناشناس"],    ['text' => "📩 پیام ناشناس"]
                ],
                [
                    ['text' => "👸🏻 با دختر"], ['text' => "🤴🏻 با پسر"]
                ],
                [
                    ['text' => "🗣 با همسن"], ['text' => "🏢 با همشهری"]
                ],
                [
                    ['text' => "👤 پروفایل من"], ['text' => "👨‍👧‍👧 لیست دوستان"]
                ],
                [
                    ['text' => "💡 راهنما"], ['text' => "👮🏻 پشتیبانی"], ['text' => "🚫 بلاک لیست"]
                ],
                [
                    ['text' => "📍 چت با افراد نزیک با GPS"]
                ],

            ],
            'resize_keyboard' => true
        ])
    ]);
}
//in the name of Allah 
//Development: @TheMrDani
//Channel : @MakranSource