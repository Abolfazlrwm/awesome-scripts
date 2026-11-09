<?php

// =====================================================
// تنظیمات
// =====================================================

$BOT_TOKEN = "PUT_BOT_TOKEN_HERE";       // توکن ربات از @BotFather

$ADMIN_ID = 0;                           // آیدی عددی ادمین — مثال: 123456789

$CHANNEL = "@your_channel";              // کانال انتشار پست‌ها (با @)

// کانال عضویت اجباری
$FORCE_CHANNEL = "@your_force_channel";  // کانال عضویت اجباری (با @)

$API = "https://api.telegram.org/bot" . $BOT_TOKEN;


// =====================================================
// فایل‌های ذخیره اطلاعات
// =====================================================

$dataFile   = __DIR__ . "/users_data.json";
$configFile = __DIR__ . "/config.json";


// =====================================================
// متن‌های پیش‌فرض
// =====================================================

$defaultConfig = [

    "start_text" =>
        "موضوعی که در کانال گفته شده رو تعریف کنید برامون🥰",

    "tell_text" =>
        "خاطرتو تعریف کن گوش کنم😍❤️",

    "received_text" =>
        "پیامت با موفقیت دریافت شد❤️\nمنتظر تأیید باش.",

    "published_text" =>
        "پیامت با موفقیت در کانال منتشر شد❤️",

    "cancel_text" =>
        "لغو شد✅️",

    "force_join_text" =>
        "برای استفاده از ربات ابتدا باید عضو کانال ما بشی❤️\n\nبعد از عضویت روی دکمه بررسی عضویت بزن."
];


// =====================================================
// ساخت فایل‌ها
// =====================================================

if (!file_exists($dataFile)) {

    file_put_contents(
        $dataFile,
        json_encode(
            [],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );
}

if (!file_exists($configFile)) {

    file_put_contents(
        $configFile,
        json_encode(
            $defaultConfig,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );
}


$users = json_decode(
    file_get_contents($dataFile),
    true
);

$config = json_decode(
    file_get_contents($configFile),
    true
);


if (!is_array($users)) {
    $users = [];
}

if (!is_array($config)) {
    $config = [];
}


// =====================================================
// اضافه کردن تنظیمات جدید به config قدیمی
// =====================================================

foreach ($defaultConfig as $key => $value) {

    if (!array_key_exists($key, $config)) {

        $config[$key] = $value;
    }
}

saveConfig();


// =====================================================
// توابع
// =====================================================

function bot($method, $data = [])
{
    global $API;

    $ch = curl_init($API . "/" . $method);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => $data,

        CURLOPT_TIMEOUT => 30
    ]);

    $result = curl_exec($ch);

    curl_close($ch);

    return json_decode($result, true);
}


function saveUsers()
{
    global $dataFile, $users;

    file_put_contents(
        $dataFile,
        json_encode(
            $users,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );
}


function saveConfig()
{
    global $configFile, $config;

    file_put_contents(
        $configFile,
        json_encode(
            $config,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        )
    );
}


function sendMessage(
    $chat_id,
    $text,
    $keyboard = null
) {

    $data = [

        "chat_id" => $chat_id,

        "text" => $text,

        "parse_mode" => "HTML"
    ];


    if ($keyboard !== null) {

        $data["reply_markup"] = json_encode(
            $keyboard,
            JSON_UNESCAPED_UNICODE
        );
    }


    return bot(
        "sendMessage",
        $data
    );
}


function editMessage(
    $chat_id,
    $message_id,
    $text,
    $keyboard = null
) {

    $data = [

        "chat_id" => $chat_id,

        "message_id" => $message_id,

        "text" => $text,

        "parse_mode" => "HTML"
    ];


    if ($keyboard !== null) {

        $data["reply_markup"] = json_encode(
            $keyboard,
            JSON_UNESCAPED_UNICODE
        );
    }


    return bot(
        "editMessageText",
        $data
    );
}


function answerCallback(
    $callback_id,
    $text = "",
    $show_alert = false
) {

    return bot(
        "answerCallbackQuery",
        [
            "callback_query_id" => $callback_id,
            "text" => $text,
            "show_alert" => $show_alert
        ]
    );
}


// =====================================================
// بررسی عضویت اجباری
// =====================================================

function isMember($user_id)
{
    global $FORCE_CHANNEL;

    $result = bot(
        "getChatMember",
        [
            "chat_id" => $FORCE_CHANNEL,
            "user_id" => $user_id
        ]
    );


    if (
        !$result ||
        !isset($result["ok"]) ||
        $result["ok"] !== true
    ) {

        return false;
    }


    $status =
        $result["result"]["status"] ?? "";


    if (
        in_array(
            $status,
            [
                "member",
                "administrator",
                "creator"
            ]
        )
    ) {

        return true;
    }


    if (
        $status === "restricted" &&
        !empty($result["result"]["is_member"])
    ) {

        return true;
    }


    return false;
}


// =====================================================
// نمایش پیام عضویت اجباری
// =====================================================

function forceJoinMessage($chat_id)
{
    global $config, $FORCE_CHANNEL;

    $keyboard = [

        "inline_keyboard" => [

            [

                [
                    "text" => "📢 عضویت در کانال",
                    "url" =>
                        "https://t.me/" .
                        ltrim($FORCE_CHANNEL, "@")
                ]

            ],

            [

                [
                    "text" => "🔄 بررسی عضویت",
                    "callback_data" =>
                        "check_membership"
                ]

            ]

        ]

    ];


    sendMessage(
        $chat_id,
        $config["force_join_text"],
        $keyboard
    );
}


// =====================================================
// دریافت آپدیت
// =====================================================

$update = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$update) {
    exit;
}


// =====================================================
// CALLBACK QUERY
// =====================================================

if (isset($update["callback_query"])) {

    $callback =
        $update["callback_query"];

    $callback_id =
        $callback["id"];

    $from_id =
        $callback["from"]["id"];

    $data =
        $callback["data"];

    $chat_id =
        $callback["message"]["chat"]["id"];

    $message_id =
        $callback["message"]["message_id"];


    // =================================================
    // بررسی عضویت
    // این قسمت قبل از قفل عضویت قرار دارد
    // =================================================

    if ($data === "check_membership") {

        if (isMember($from_id)) {

            answerCallback(
                $callback_id,
                "عضویت شما تأیید شد ✅️"
            );


            $keyboard = [

                "inline_keyboard" => [

                    [

                        [
                            "text" => "تعریف✨️",
                            "callback_data" => "tell_story"
                        ]

                    ]

                ]

            ];


            editMessage(
                $chat_id,
                $message_id,
                "عضویت شما تأیید شد ✅️\n\nحالا می‌تونی از ربات استفاده کنی.",
                $keyboard
            );

        } else {

            answerCallback(
                $callback_id,
                "هنوز عضو کانال نشدی ❌️",
                true
            );
        }


        exit;
    }


    // =================================================
    // قفل عضویت اجباری
    // ادمین مستثنی است
    // =================================================

    if ($from_id != $ADMIN_ID) {

        if (!isMember($from_id)) {

            answerCallback(
                $callback_id,
                "ابتدا باید عضو کانال بشی ❌️",
                true
            );


            forceJoinMessage($chat_id);

            exit;
        }
    }


    // =================================================
    // دکمه تعریف
    // =================================================

    if ($data === "tell_story") {

        answerCallback($callback_id);


        if (!isset($users[$from_id])) {

            $users[$from_id] = [

                "state" => "none",

                "story" => null
            ];
        }


        $users[$from_id]["state"] =
            "waiting_story";


        saveUsers();


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "❌️لغو",
                        "callback_data" => "cancel_story"
                    ]

                ]

            ]

        ];


        sendMessage(
            $chat_id,
            $config["tell_text"],
            $keyboard
        );


        exit;
    }


    // =================================================
    // لغو تعریف
    // =================================================

    if ($data === "cancel_story") {

        answerCallback(
            $callback_id,
            "لغو شد"
        );


        if (!isset($users[$from_id])) {

            $users[$from_id] = [

                "state" => "none",

                "story" => null
            ];
        }


        $users[$from_id]["state"] =
            "none";


        saveUsers();


        editMessage(
            $chat_id,
            $message_id,
            $config["cancel_text"]
        );


        exit;
    }


    // =================================================
    // فقط مالک از اینجا به بعد دسترسی دارد
    // =================================================

    if ($from_id != $ADMIN_ID) {

        answerCallback(
            $callback_id,
            "❌️ دسترسی نداری."
        );

        exit;
    }


    // =================================================
    // پنل ادمین
    // =================================================

    if ($data === "admin_panel") {

        answerCallback($callback_id);


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "📢 پیام همگانی",
                        "callback_data" => "broadcast"
                    ]

                ],

                [

                    [
                        "text" => "✏️ تغییر متن‌ها",
                        "callback_data" => "texts"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "⚙️ <b>پنل مدیریت</b>\n\nیک گزینه را انتخاب کن:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // منوی تغییر متن
    // =================================================

    if ($data === "texts") {

        answerCallback($callback_id);


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "متن Start",
                        "callback_data" => "edit_start"
                    ]

                ],

                [

                    [
                        "text" => "متن تعریف",
                        "callback_data" => "edit_tell"
                    ]

                ],

                [

                    [
                        "text" => "متن دریافت",
                        "callback_data" => "edit_received"
                    ]

                ],

                [

                    [
                        "text" => "متن انتشار",
                        "callback_data" => "edit_published"
                    ]

                ],

                [

                    [
                        "text" => "متن عضویت اجباری",
                        "callback_data" => "edit_force_join"
                    ]

                ],

                [

                    [
                        "text" => "🔙 بازگشت",
                        "callback_data" => "admin_panel"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "✏️ <b>تغییر متن‌ها</b>\n\nمتنی که می‌خواهی تغییر بدهی انتخاب کن:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // انتخاب متن برای ویرایش
    // =================================================

    $editTypes = [

        "edit_start" =>
            "start_text",

        "edit_tell" =>
            "tell_text",

        "edit_received" =>
            "received_text",

        "edit_published" =>
            "published_text",

        "edit_force_join" =>
            "force_join_text"
    ];


    if (isset($editTypes[$data])) {

        answerCallback($callback_id);


        $type =
            $editTypes[$data];


        $users[$ADMIN_ID]["state"] =
            "edit_text";

        $users[$ADMIN_ID]["edit_type"] =
            $type;


        saveUsers();


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "❌️ لغو",
                        "callback_data" => "cancel_edit_text"
                    ],

                    [
                        "text" => "🔙 بازگشت",
                        "callback_data" => "texts"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "✏️ متن جدید رو ارسال کن:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // لغو تغییر متن
    // =================================================

    if ($data === "cancel_edit_text") {

        answerCallback(
            $callback_id,
            "لغو شد"
        );


        $users[$ADMIN_ID]["state"] =
            "none";

        $users[$ADMIN_ID]["edit_type"] =
            null;


        saveUsers();


        editMessage(
            $ADMIN_ID,
            $message_id,
            "ویرایش متن لغو شد❌️"
        );


        exit;
    }


    // =================================================
    // پیام همگانی
    // =================================================

    if ($data === "broadcast") {

        answerCallback($callback_id);


        $users[$ADMIN_ID]["state"] =
            "broadcast";


        saveUsers();


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "❌️ لغو",
                        "callback_data" => "cancel_broadcast"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "📢 <b>پیام همگانی</b>\n\nپیامی که می‌خواهی برای کاربران ارسال شود را بفرست:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // لغو پیام همگانی
    // =================================================

    if ($data === "cancel_broadcast") {

        answerCallback(
            $callback_id,
            "لغو شد"
        );


        $users[$ADMIN_ID]["state"] =
            "none";


        saveUsers();


        editMessage(
            $ADMIN_ID,
            $message_id,
            "ارسال پیام همگانی لغو شد❌️"
        );


        exit;
    }


    // =================================================
    // رد داستان
    // =================================================

    if (strpos($data, "reject_") === 0) {

        $target_user =
            intval(
                str_replace(
                    "reject_",
                    "",
                    $data
                )
            );


        answerCallback(
            $callback_id,
            "پیام ارسال نشد ❌️"
        );


        editMessage(
            $chat_id,
            $message_id,
            "❌️ این پیام برای کانال ارسال نشد."
        );


        if (isset($users[$target_user])) {

            $users[$target_user]["story"] =
                null;
        }


        saveUsers();


        exit;
    }


    // =================================================
    // تأیید داستان و ارسال به کانال
    // =================================================

    if (strpos($data, "approve_") === 0) {

        $target_user =
            intval(
                str_replace(
                    "approve_",
                    "",
                    $data
                )
            );


        answerCallback(
            $callback_id,
            "در حال ارسال..."
        );


        if (
            !isset($users[$target_user]) ||
            empty($users[$target_user]["story"])
        ) {

            editMessage(
                $chat_id,
                $message_id,
                "❌️ متن پیدا نشد."
            );

            exit;
        }


        $story =
            $users[$target_user]["story"];


        $story =
            htmlspecialchars(
                $story,
                ENT_QUOTES | ENT_SUBSTITUTE,
                "UTF-8"
            );


        // قالب نهایی کانال
        $channelText =
            "#پیام_ناشناس\n\n\n" .
            $story .
            "\n\n\n" .
            "برای ارسال متن👇\n" .
            "@nashnassho_robot";


        $sent =
            sendMessage(
                $CHANNEL,
                $channelText
            );


        if (
            isset($sent["ok"]) &&
            $sent["ok"] === true
        ) {

            editMessage(
                $chat_id,
                $message_id,
                "✅️ پیام با موفقیت در کانال ارسال شد."
            );


            sendMessage(
                $target_user,
                $config["published_text"]
            );

        } else {

            editMessage(
                $chat_id,
                $message_id,
                "❌️ ارسال به کانال انجام نشد.\n\nربات را در کانال ادمین کن."
            );
        }


        $users[$target_user]["story"] =
            null;


        saveUsers();


        exit;
    }


    // =================================================
    // ارسال پیام به کاربر
    // =================================================

    if (strpos($data, "reply_") === 0) {

        $target_user =
            intval(
                str_replace(
                    "reply_",
                    "",
                    $data
                )
            );


        answerCallback($callback_id);


        $users[$ADMIN_ID]["state"] =
            "admin_message";

        $users[$ADMIN_ID]["reply_to_user"] =
            $target_user;


        saveUsers();


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "❌️ لغو",
                        "callback_data" =>
                            "cancel_admin_reply"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "📩 پیام خودت رو برای کاربر بفرست:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // لغو پیام به کاربر
    // =================================================

    if ($data === "cancel_admin_reply") {

        answerCallback(
            $callback_id,
            "لغو شد"
        );


        $users[$ADMIN_ID]["state"] =
            "none";

        $users[$ADMIN_ID]["reply_to_user"] =
            null;


        saveUsers();


        editMessage(
            $ADMIN_ID,
            $message_id,
            "ارسال پیام لغو شد❌️"
        );


        exit;
    }
}


// =====================================================
// MESSAGE
// =====================================================

if (isset($update["message"])) {

    $message =
        $update["message"];


    $chat_id =
        $message["chat"]["id"];


    $user_id =
        $message["from"]["id"];


    $text =
        $message["text"] ?? "";


    // =================================================
    // ثبت کاربر
    // =================================================

    if (!isset($users[$user_id])) {

        $users[$user_id] = [

            "state" => "none",

            "story" => null
        ];

        saveUsers();
    }


    // =================================================
    // /start
    // =================================================

    if ($text === "/start") {

        // ادمین بدون عضویت اجباری
        if ($user_id != $ADMIN_ID) {

            if (!isMember($user_id)) {

                $users[$user_id]["state"] =
                    "none";

                saveUsers();

                forceJoinMessage($chat_id);

                exit;
            }
        }


        $users[$user_id]["state"] =
            "none";


        saveUsers();


        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "تعریف✨️",
                        "callback_data" => "tell_story"
                    ]

                ]

            ]

        ];


        sendMessage(
            $chat_id,
            $config["start_text"],
            $keyboard
        );


        // =================================================
        // ❤️ ری‌اکشن روی خود پیام /start کاربر
        // =================================================

        bot(
            "setMessageReaction",
            [

                "chat_id" =>
                    $chat_id,

                "message_id" =>
                    $message["message_id"],

                "reaction" =>
                    json_encode(
                        [

                            [

                                "type" =>
                                    "emoji",

                                "emoji" =>
                                    "❤️"

                            ]

                        ],
                        JSON_UNESCAPED_UNICODE
                    )

            ]
        );


        exit;
    }


    // =================================================
    // /admin
    // =================================================

    if (
        $text === "/admin" &&
        $user_id == $ADMIN_ID
    ) {

        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "📢 پیام همگانی",
                        "callback_data" => "broadcast"
                    ]

                ],

                [

                    [
                        "text" => "✏️ تغییر متن‌ها",
                        "callback_data" => "texts"
                    ]

                ]

            ]

        ];


        sendMessage(
            $ADMIN_ID,
            "⚙️ <b>پنل مدیریت</b>\n\nاز گزینه‌های زیر استفاده کن:",
            $keyboard
        );


        exit;
    }


    // =================================================
    // قفل عضویت برای پیام‌های معمولی
    // =================================================

    if ($user_id != $ADMIN_ID) {

        if (!isMember($user_id)) {

            forceJoinMessage($chat_id);

            exit;
        }
    }


    // =================================================
    // تغییر متن توسط ادمین
    // =================================================

    if (
        $user_id == $ADMIN_ID &&
        isset($users[$ADMIN_ID]["state"]) &&
        $users[$ADMIN_ID]["state"] === "edit_text"
    ) {

        if ($text === "") {

            sendMessage(
                $ADMIN_ID,
                "❌️ لطفاً متن جدید را ارسال کن."
            );

            exit;
        }


        $type =
            $users[$ADMIN_ID]["edit_type"];


        if (
            $type &&
            array_key_exists(
                $type,
                $config
            )
        ) {

            $config[$type] =
                $text;


            saveConfig();


            $users[$ADMIN_ID]["state"] =
                "none";

            $users[$ADMIN_ID]["edit_type"] =
                null;


            saveUsers();


            $keyboard = [

                "inline_keyboard" => [

                    [

                        [
                            "text" => "✏️ تغییر متن‌ها",
                            "callback_data" => "texts"
                        ],

                        [
                            "text" => "🔙 پنل مدیریت",
                            "callback_data" => "admin_panel"
                        ]

                    ]

                ]

            ];


            sendMessage(
                $ADMIN_ID,
                "متن با موفقیت تغییر کرد✅️",
                $keyboard
            );
        }


        exit;
    }


    // =================================================
    // پیام همگانی
    // =================================================

    if (
        $user_id == $ADMIN_ID &&
        isset($users[$ADMIN_ID]["state"]) &&
        $users[$ADMIN_ID]["state"] === "broadcast"
    ) {

        if ($text === "") {

            sendMessage(
                $ADMIN_ID,
                "❌️ لطفاً متن پیام همگانی را ارسال کن."
            );

            exit;
        }


        $success = 0;

        $failed = 0;


        foreach ($users as $uid => $userData) {

            if ((int)$uid === $ADMIN_ID) {
                continue;
            }


            $result =
                sendMessage(
                    $uid,
                    $text
                );


            if (
                isset($result["ok"]) &&
                $result["ok"] === true
            ) {

                $success++;

            } else {

                $failed++;
            }
        }


        $users[$ADMIN_ID]["state"] =
            "none";


        saveUsers();


        sendMessage(
            $ADMIN_ID,

            "📢 <b>پیام همگانی تمام شد</b>\n\n" .
            "✅️ ارسال موفق: " .
            $success .
            "\n" .
            "❌️ ارسال ناموفق: " .
            $failed
        );


        exit;
    }


    // =================================================
    // ارسال پیام مالک به کاربر
    // =================================================

    if (
        $user_id == $ADMIN_ID &&
        isset($users[$ADMIN_ID]["state"]) &&
        $users[$ADMIN_ID]["state"] === "admin_message"
    ) {

        if ($text === "") {

            sendMessage(
                $ADMIN_ID,
                "❌️ لطفاً پیام را ارسال کن."
            );

            exit;
        }


        $target =
            $users[$ADMIN_ID]["reply_to_user"] ?? 0;


        if ($target) {

            $result =
                sendMessage(
                    $target,
                    $text
                );


            if (
                isset($result["ok"]) &&
                $result["ok"] === true
            ) {

                sendMessage(
                    $ADMIN_ID,
                    "پیام با موفقیت ارسال شد✅️"
                );

            } else {

                sendMessage(
                    $ADMIN_ID,
                    "❌️ ارسال پیام انجام نشد."
                );
            }
        }


        $users[$ADMIN_ID]["state"] =
            "none";

        $users[$ADMIN_ID]["reply_to_user"] =
            null;


        saveUsers();


        exit;
    }


    // =================================================
    // دریافت داستان کاربر
    // =================================================

    if (
        isset($users[$user_id]["state"]) &&
        $users[$user_id]["state"] === "waiting_story"
    ) {

        if ($text === "") {

            sendMessage(
                $chat_id,
                "لطفاً خاطرتو به صورت متن ارسال کن❤️"
            );

            exit;
        }


        $story =
            trim($text);


        $users[$user_id]["state"] =
            "none";

        $users[$user_id]["story"] =
            $story;


        saveUsers();


        // =================================================
        // دکمه‌های مالک
        // =================================================

        $keyboard = [

            "inline_keyboard" => [

                [

                    [
                        "text" => "❌️",
                        "callback_data" =>
                            "reject_" . $user_id
                    ],

                    [
                        "text" => "✅️",
                        "callback_data" =>
                            "approve_" . $user_id
                    ]

                ],

                [

                    [
                        "text" => "ارسال پیام به کاربر",
                        "callback_data" =>
                            "reply_" . $user_id
                    ]

                ]

            ]

        ];


        // =================================================
        // ارسال داستان برای مالک
        // =================================================

        $adminStory =
            htmlspecialchars(
                $story,
                ENT_QUOTES | ENT_SUBSTITUTE,
                "UTF-8"
            );


        sendMessage(
            $ADMIN_ID,

            "📩 <b>یک پیام ناشناس جدید</b>\n\n" .
            $adminStory,

            $keyboard
        );


        // =================================================
        // اطلاع به کاربر
        // =================================================

        sendMessage(
            $chat_id,
            $config["received_text"]
        );


        exit;
    }
}

?>