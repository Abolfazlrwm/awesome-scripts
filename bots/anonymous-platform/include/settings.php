<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

/// @Sourrce_kade
// -------- Edit these --------
$settings = [
    'dbname'      => '',
    'username'    => '',
    'password'    => '',
    'token'       => '',
    'admin_id'    => '71983499',
    'merchant_id' => '',
    'bot_name'     => 'اسم ربات',
    'bot_username' => 'full_chat_bot'
];
// -------- Edit these --------

// -------- Dont Touch --------
date_default_timezone_set("Asia/Tehran");
$date = date("Y-m-d H:i:s");
$time = time();
$api_key = ''; // مرچنت‌کد درگاه پرداخت (زرین‌پال) خودت را اینجا بگذارید
$status_payment_gateway = "zarinpanl";
// $status_payment_gateway = "idpay";

$main = "🏛️ صفحه اصلی";
$back = "بازگشت 🔙";
$panel = "👨‍💻 پنل ادمین";
$end_chat = "پایان چت";
$show_profile = "👀مشاهده پروفایل این مخاطب👤";
$num_page = 1;
$step_page = 10;

$btns_bot = [
    '🔗 به یه ناشناس وصلم کن!️',
    '🔍 جستجوی کاربران 🔎',
    '📍افراد نزدیک',
    '💰سکه',
    '👤پروفایل',
    '🤔راهنما',
    '🚸 معرفی به دوستان (سکه رایگان)',
    $main,$back,$panel
];
$type_status_gender = [
    'unknown' => 'نامشخص',
    'boy' => 'پسر',
    'girl' => 'دختر',
    'all' => 'همه'
];
$status_pay = [
    'move_gateway' => "در انتظار پرداخت ⏳",
    'success' => "موفق ✅",
    'failed' => "ناموفق ❗️",
    'expired' => "منقضی شده ⛔️"
];
$status_gender = [
    'sage' => 'کاربران همسنی',
    'sstate' => 'کاربران هم استانی',
    'wchat' => 'کاربران بدون چت',
    'nuser' => 'کاربران جدید',
];
$status_gender_search = [
    'sage' => '👥 هم سنی ها',
    'sstate' => '🎌 هم استانی ها',
    'wchat' => '🚶‍♂️ بدون چت ها 🚶‍♀️',
    'nuser' => '🙋‍♂️ کاربران جدید 🙋‍♀️',
];
$title_gender_search = [
    'sage' => '👥 لیست افراد هم سن شما که در 3 روز اخیر آنلاین بوده اند',
    'sstate' => '🎌 لیست افراد هم استانی شما که در 3 روز اخیر آنلاین بوده اند',
    'wchat' => '🚶‍♀️لیست کاربران بدون چت آنلاین 🚶‍♂️',
    'nuser' => '🙋‍♀️ لیست کاربران جدید آنلاین🙋‍♂️',
];
$report_options = [
    'ads' => "تبلیغات سایت ها، ربات ها و کانال ها",
    'immoral' => "ارسال محتوای غیر اخلاقی",
    'disturb' => "ایجاد مزاحمت",
    'dissemination' => "پخش شماره موبایل یا اطلاعات شخصی دیگر",
    'immoralprofile' => "کلمات یا عکس غیر اخلاقی و یا توهین آمیز در پروفایل",
    'wronggender' => "جنسیت اشتباه در پروفایل",
    'other' => "دیگر موارد..",
];
$txts_array = [
    'onlinetime' => [
        'all' => 'همه',
        '3600' => 'تا یک ساعت قبل',
        '21600' => 'تا 6 ساعت قبل',
        '86400' => 'تا یک روز قبل',
        '172800' => 'تا دو روز قبل',
        '259200' => 'تا سه روز قبل',
        '604800' => 'تا یک هفته قبل',
    ],
    'sort' => [
        'last_activity' => 'تاریخ آنلاین',
        'near' => 'فاصله نزدیک',
        'min_age' => 'کمترین سن',
        'age' => 'سن نزدیک',
        'max_age' => 'بیشترین سن',
        'wchat' => 'فقط نمایش بدون چت های آنلاین',
    ],
    'type_text_user' => [
        'normal' => [
            'boy' => 'پسر',
            'girl' => 'دختر'
        ],
        'with_emoji' => [
            'boy' => '🙎‍♂️ پسر',
            'girl' => '🙎‍♀️ دختر'
        ]
    ]
];
$status_gender_emoji = [
    'boy' => '🙎‍♂️',
    'girl' => '🙎‍♀️'
];
?>