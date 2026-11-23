<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <title>تنظیمات ربات | مکران کد</title>
    <meta charset="utf-8">
    <style>
        body { 
    font-family: Tahoma, Arial;
    background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

.header {
    background: rgba(255, 255, 255, 0.95);
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
}

.brand {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
}

.brand img {
    width: 120px;
    height: auto;
    margin-left: 20px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
}

.brand img:hover {
    transform: scale(1.05);
}

.brand h1 {
    color: #2c3e50;
    font-size: 38px;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    background: linear-gradient(45deg, #1a5f7a, #2980b9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: bold;
}

.container { 
    width: 85%;
    max-width: 1100px;
    margin: 40px auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}

.form-group { 
    margin: 25px 0;
}

label { 
    display: block;
    margin-bottom: 12px;
    color: #34495e;
    font-weight: bold;
    font-size: 15px;
    letter-spacing: 0.5px;
}

input { 
    width: 100%;
    padding: 14px;
    border: 2px solid #e3e3e3;
    border-radius: 12px;
    box-sizing: border-box;
    transition: all 0.3s ease;
    font-family: Tahoma;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.9);
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 10px rgba(52,152,219,0.2);
    transform: translateY(-2px);
}

button { 
    padding: 16px 32px;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-family: Tahoma;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 30px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.success {
    background: linear-gradient(135deg, #43cea2, #185a9d);
    color: white;
    padding: 25px;
    margin: 25px 0;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(67,206,162,0.3);
}

.error {
    background: linear-gradient(135deg, #ff6b6b, #ee0979);
    color: white;
    padding: 25px;
    margin: 25px 0;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(238,9,121,0.3);
}

h2 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 35px;
    font-size: 28px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

::placeholder {
    color: #95a5a6;
    opacity: 0.8;
}

/* انیمیشن ورود عناصر */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.container {
    animation: fadeIn 0.8s ease-out;
}

    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <img src="logo.jpg" alt="مکران کد">
            <h1>مکران کد</h1>
        </div>
    </div>

    <!-- Rest of your existing HTML code remains the same -->

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $config = "<?php\n\n";
            $config .= "//config\n";
            $config .= "\$admin = array(\"{$_POST['admin1']}\",\"{$_POST['admin2']}\"); // آیدی ادمین ها\n";
            $config .= "\$usernamebot = \"{$_POST['usernamebot']}\"; // یوزرنیم ربات\n";
            $config .= "\$channel = \"{$_POST['channel']}\"; // یوزرنیم کانال\n";
            $config .= "\$channelby = \"{$_POST['channelby']}\"; // یوزرنیم کانال گزارش\n";
            $config .= "\$web = \"{$_POST['web']}\"; // آدرس ربات\n";
            $config .= "\$sup = \"{$_POST['sup']}\"; // یوزرنیم پشتیبانی\n";
            $config .= "\$far = \"{$_POST['far']}\"; // نام فارسی ربات\n\n";
            $config .= "//database\n";
            $config .= "\$servername = \"localhost\";\n";
            $config .= "\$username = \"{$_POST['dbusername']}\";\n";
            $config .= "\$password = 'PUT-YOUR-KEY-HERE'dbpassword']}';\n";
            $config .= "\$dbname = \"{$_POST['dbname']}\";\n\n";
            $config .= "\$tokenbot = '{$_POST['tokenbot']}';\n\n";
            $config .= "//مرچنت\n";
            $config .= "\$MerchantID = \"{$_POST['MerchantID']}\";\n";
            $config .= "?>";

            // تنظیم وب‌هوک
            $token = $_POST['tokenbot'];
            $domain = rtrim($_POST['domain'], '/');
            $webhookUrl = $domain . "/bot.php";
            
            $setWebhook = file_get_contents("https://api.telegram.org/bot{$token}/setWebhook?url={$webhookUrl}");
            $webhookResult = json_decode($setWebhook, true);

            if (file_put_contents('config.php', $config)) {
                echo '<div class="success">
                    <h3>اطلاعات با موفقیت ذخیره شد</h3>';
                
                if ($webhookResult['ok']) {
                    echo '<p>وب‌هوک با موفقیت تنظیم شد</p>';
                } else {
                    echo '<p style="color: #a94442;">خطا در تنظیم وب‌هوک: ' . $webhookResult['description'] . '</p>';
                }
                
                echo '<button onclick="window.location.href=\'table.php\'">اتصال به دیتابیس</button>
                </div>';
            } else {
                echo '<div class="error">خطا در ذخیره اطلاعات</div>';
            }
        } else {
        ?>
        <h2>تنظیمات ربات</h2>
        <form method="POST">
            <div class="form-group">
           <label>مسیر ربات (مثال: https://yourdomain.com/foldrl):</label>
                <input type="text" name="domain" required placeholder="https://example.com">
            </div>
            <div class="form-group">
                <label>آیدی ادمین اول:</label>
                <input type="text" name="admin1" required>
            </div>
            <div class="form-group">
                <label>آیدی ادمین دوم:</label>
                <input type="text" name="admin2" required>
            </div>
            <div class="form-group">
                <label>یوزرنیم ربات:</label>
                <input type="text" name="usernamebot" required>
            </div>
            <div class="form-group">
                <label>یوزرنیم کانال:</label>
                <input type="text" name="channel" required>
            </div>
            <div class="form-group">
                <label>یوزرنیم کانال گزارش:</label>
                <input type="text" name="channelby" required>
            </div>
            <div class="form-group">
                <label>آدرس ربات شماره مجازی:</label>
                <input type="text" name="web" required>
            </div>
            <div class="form-group">
                <label>یوزرنیم پشتیبانی:</label>
                <input type="text" name="sup" required>
            </div>
            <div class="form-group">
                <label>نام فارسی ربات:</label>
                <input type="text" name="far" required>
            </div>
            <div class="form-group">
                <label>نام کاربری دیتابیس:</label>
                <input type="text" name="dbusername" required>
            </div>
            <div class="form-group">
                <label>رمز عبور دیتابیس:</label>
                <input type="password" name="dbpassword" required>
            </div>
            <div class="form-group">
                <label>نام دیتابیس:</label>
                <input type="text" name="dbname" required>
            </div>
            <div class="form-group">
                <label>توکن ربات:</label>
                <input type="text" name="tokenbot" required>
            </div>
            <div class="form-group">
                <label>مرچنت آیدی:</label>
                <input type="text" name="MerchantID" required>
            </div>
            <button type="submit">ثبت اطلاعات</button>
        </form>
        <?php } ?>
    </div>
</body>
</html>
