<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
Developer: @Cristianoronal_d_o
Channel: @botbiso
*/
//در فایل بعدی که mm.php هست اگر یادتون باشه یه سورس اسکی کانفیگ نوشتیم توت ین مشکلی چه داشت اضافه میشد به ربات ها اول خطا می‌داد بعد کانفیگ رو میفرستاد که حل شده
header('Content-Type: application/json');

if (isset($_GET['names']) && isset($_GET['rounds'])) {
    $names = array_filter(array_map('trim', preg_split('/[,،]/u', $_GET['names'])));
    $rounds = (int)$_GET['rounds'];

    if (count($names) < 2) {
        echo json_encode(['error' => 'حداقل ۲ اسم لازم است!'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($rounds < 1 || $rounds > 5) {
        echo json_encode(['error' => 'تعداد چرخش باید بین ۱ تا ۵ باشد!'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // شبیه‌سازی چرخش با تأخیر
    usleep(2000000); // 2 ثانیه تأخیر
    $winner = $names[array_rand($names)];

    echo json_encode(['winner' => $winner ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'پارامترهای names و rounds را وارد کنید!'], JSON_UNESCAPED_UNICODE);
}
/*
Developer: @Cristianoronal_d_o
Channel: @botbiso
*/
?>
