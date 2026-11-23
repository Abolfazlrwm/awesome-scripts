<?php
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

session_start();
/*
Developer: t.me/Dev_Amiri 
Channel : t.me/Sourrce_kade 
اصکی با ذکر منبع مجاز است !
*/
$adminUser = ""; // یک یوزرنیم برای ورود ادمین ها بزارین
$password_text = ""; // رمز ورود ادمین رو بزارید

$db = new mysqli('localhost', 'DatabaseUsername', 'DatabasePass', 'DatabaseName');

$db->query("
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    short_desc TEXT NOT NULL,
    full_desc LONGTEXT NOT NULL,
    endpoint TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$db->query("
CREATE TABLE IF NOT EXISTS service_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    detail_type ENUM('text','table','code','button') NOT NULL,
    title VARCHAR(100),
    content LONGTEXT,
    button_text VARCHAR(50),
    button_link VARCHAR(255),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
)");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if (!isset($_SESSION['loggedin'])) {
    if (isset($_POST['login'])) {
        $hashed_password = password_hash($password_text, PASSWORD_DEFAULT); 
        if ($_POST['username'] === $adminUser && password_verify($_POST['password'], $hashed_password)) {
            $_SESSION['loggedin'] = true;
            session_regenerate_id();
            header('Location: admin.php');
            exit;
        } else {
            $error = "نام کاربری یا رمز عبور اشتباه است";
        }
    }
    ?>
    	
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>ورود به پنل مدیریت</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                padding: 0;
            }
            .login-container {
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                width: 100%;
                max-width: 400px;
            }
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .login-header h1 {
                color: #6a11cb;
                margin-bottom: 10px;
            }
            .login-header i {
                font-size: 3rem;
                color: #2575fc;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .form-group input {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 25px;
                outline: none;
                transition: all 0.3s;
            }
            .form-group input:focus {
                border-color: #6a11cb;
                box-shadow: 0 0 0 3px rgba(106,17,203,0.2);
            }
            .btn {
                width: 100%;
                padding: 12px;
                background: linear-gradient(to right, #6a11cb, #2575fc);
                color: white;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                font-weight: bold;
                font-size: 1rem;
                transition: all 0.3s;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .error {
                color: #dc3545;
                text-align: center;
                margin-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-crystal"></i>
                <h1>ورود به پنل مدیریت</h1>
            </div>
            <?php if (isset($error)) echo '<div class="error">'.$error.'</div>'; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">نام کاربری:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">رمز عبور:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn" name="login">ورود</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $stmt = $db->prepare("INSERT INTO services (name, short_desc, full_desc, endpoint, icon) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $_POST['name'], $_POST['short_desc'], $_POST['full_desc'], $_POST['endpoint'], $_POST['icon']);
        $stmt->execute();
        $service_id = $stmt->insert_id;
        $stmt->close();
        
        if (!empty($_POST['detail_title'])) {
            for ($i = 0; $i < count($_POST['detail_title']); $i++) {
                $detail_type = $_POST['detail_type'][$i];
                $content = ($detail_type === 'button') ? '' : $_POST['detail_content'][$i];
                $button_text = ($detail_type === 'button') ? $_POST['button_text'][$i] : '';
                $button_link = ($detail_type === 'button') ? $_POST['button_link'][$i] : '';
                
                $stmt = $db->prepare("INSERT INTO service_details (service_id, detail_type, title, content, button_text, button_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssi", $service_id, $detail_type, $_POST['detail_title'][$i], $content, $button_text, $button_link, $i);
                $stmt->execute();
                $stmt->close();
            }
        }
        header('Location: admin.php');
        exit;
    }
    
    if (isset($_POST['edit_service'])) {
        $stmt = $db->prepare("UPDATE services SET name=?, short_desc=?, full_desc=?, endpoint=?, icon=? WHERE id=?");
        $stmt->bind_param("sssssi", $_POST['name'], $_POST['short_desc'], $_POST['full_desc'], $_POST['endpoint'], $_POST['icon'], $_POST['id']);
        $stmt->execute();
        $stmt->close();
        
        $db->query("DELETE FROM service_details WHERE service_id=".(int)$_POST['id']);
        
        if (!empty($_POST['detail_title'])) {
            for ($i = 0; $i < count($_POST['detail_title']); $i++) {
                $detail_type = $_POST['detail_type'][$i];
                $content = ($detail_type === 'button') ? '' : $_POST['detail_content'][$i];
                $button_text = ($detail_type === 'button') ? $_POST['button_text'][$i] : '';
                $button_link = ($detail_type === 'button') ? $_POST['button_link'][$i] : '';
                
                $stmt = $db->prepare("INSERT INTO service_details (service_id, detail_type, title, content, button_text, button_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssssi", $_POST['id'], $detail_type, $_POST['detail_title'][$i], $content, $button_text, $button_link, $i);
                $stmt->execute();
                $stmt->close();
            }
        }
        header('Location: admin.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM services WHERE id=?");
    $stmt->bind_param("i", (int)$_GET['delete']);
    $stmt->execute();
    $stmt->close();
    header('Location: admin.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $stmt = $db->prepare("UPDATE services SET is_active = NOT is_active WHERE id=?");
    $stmt->bind_param("i", (int)$_GET['toggle']);
    $stmt->execute();
    $stmt->close();
    header('Location: admin.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$total_services = $db->query("SELECT COUNT(*) as total FROM services")->fetch_assoc()['total'];
$active_services = $db->query("SELECT COUNT(*) as total FROM services WHERE is_active = 1")->fetch_assoc()['total'];

$popular_query = $db->query("SELECT s.id, s.name, COUNT(v.id) as visits FROM services s LEFT JOIN visits v ON s.id = v.service_id GROUP BY s.id ORDER BY visits DESC LIMIT 5");
if ($popular_query) {
    $popular_services = $popular_query->fetch_all(MYSQLI_ASSOC);
} else {
    $popular_services = [];
    error_log("Popular services query failed: " . $db->error);
}

$services = $db->query("SELECT * FROM services ORDER BY created_at DESC");
$edit_service = isset($_GET['edit']) ? $db->query("SELECT * FROM services WHERE id=".(int)$_GET['edit'])->fetch_assoc() : null;
$edit_details = $edit_service ? $db->query("SELECT * FROM service_details WHERE service_id=".$edit_service['id']." ORDER BY sort_order") : null;
?>
	
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت وب سرویس</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6a11cb;
            --secondary: #2575fc;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
            --radius: 15px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .admin-nav {
            background: var(--dark);
            padding: 15px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            padding: 8px 15px;
            border-radius: var(--radius);
            transition: all 0.3s;
        }

        .admin-nav a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .stat-card h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .stat-card p {
            color: #6c757d;
        }

        .popular-services {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .popular-services h3 {
            margin-bottom: 20px;
            color: var(--primary);
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
        }

        .popular-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .service-form {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(106,17,203,0.1);
        }

        .form-group textarea {
            min-height: 100px;
            border-radius: var(--radius);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .services-list {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .service-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .service-item:hover {
            background: rgba(106,17,203,0.03);
        }

        .service-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .service-icon {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .service-actions {
            display: flex;
            gap: 10px;
        }

        .service-actions a {
            padding: 8px 15px;
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .service-actions a.edit {
            background: rgba(40,167,69,0.1);
            color: var(--success);
        }

        .service-actions a.edit:hover {
            background: var(--success);
            color: white;
        }

        .service-actions a.toggle {
            background: rgba(23,162,184,0.1);
            color: var(--info);
        }

        .service-actions a.toggle:hover {
            background: var(--info);
            color: white;
        }

        .service-actions a.delete {
            background: rgba(220,53,69,0.1);
            color: var(--danger);
        }

        .service-actions a.delete:hover {
            background: var(--danger);
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: var(--radius);
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-active {
            background: var(--success);
            color: white;
        }

        .status-inactive {
            background: var(--danger);
            color: white;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border-left: 5px solid var(--primary);
        }

        .detail-text {
            background: #f1f8ff;
            padding: 15px;
            border-radius: 10px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .detail-table th,
        .detail-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: right;
        }

        .detail-table th {
            background-color: var(--light);
        }

        .detail-code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 10px;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }

        .detail-button {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            text-decoration: none;
            border-radius: var(--radius);
            margin: 10px 0;
            transition: all 0.3s;
        }

        .detail-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .detail-actions {
            margin-top: 15px;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(106,17,203,0.1);
        }

        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .admin-nav {
                flex-direction: column;
                gap: 10px;
            }
            
            .service-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .service-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-crystal"></i> پنل مدیریت وب سرویس</h1>
        </div>
        
        <div class="admin-nav">
            <div>
                <a href="admin.php"><i class="fas fa-tachometer-alt"></i> پیشخوان</a>
                <a href="admin.php?action=add"><i class="fas fa-plus"></i> افزودن سرویس</a>
                <a href="index.php"><i class="fas fa-home"></i> بازگشت به سایت</a>
            </div>
            <div>
                <a href="admin.php?logout"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-cubes"></i>
                <h3><?= $total_services ?></h3>
                <p>تعداد کل سرویس‌ها</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?= $active_services ?></h3>
                <p>سرویس‌های فعال</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-eye"></i>
                <h3><?= ($popular_services[0]['visits'] ?? 0) ?></h3>
                <p>بازدید از پرطرفدارترین سرویس</p>
            </div>
        </div>
        
        <div class="popular-services">
            <h3><i class="fas fa-chart-line"></i> محبوب‌ترین سرویس‌ها</h3>
            <?php foreach ($popular_services as $service): ?>
                <div class="popular-item">
                    <span><?= htmlspecialchars($service['name']) ?></span>
                    <span><?= $service['visits'] ?> بازدید</span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php // @Dev_Amiri 
        if (isset($_GET['action']) || isset($_GET['edit'])): ?>
            <div class="service-form">
                <h2><?= isset($_GET['edit']) ? 'ویرایش سرویس' : 'افزودن سرویس جدید' ?></h2>
                <form method="POST">
                    <input type="hidden" name="<?= isset($_GET['edit']) ? 'edit_service' : 'add_service' ?>">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="id" value="<?= $edit_service['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>نام سرویس:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($edit_service['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>توضیح کوتاه:</label>
                        <textarea name="short_desc" rows="2" required><?= htmlspecialchars($edit_service['short_desc'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>توضیح کامل:</label>
                        <textarea name="full_desc" rows="4"><?= htmlspecialchars($edit_service['full_desc'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>آدرس API:</label>
                        <input type="text" name="endpoint" value="<?= htmlspecialchars($edit_service['endpoint'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>آیکون (Font Awesome):</label>
                        <input type="text" name="icon" value="<?= htmlspecialchars($edit_service['icon'] ?? '') ?>" required>
                        <small>مثال: fas fa-instagram</small>
                    </div>
                    
                    <h3 style="margin:20px 0 10px">جزئیات سرویس</h3>
                    <div id="details-container">
                        <?php // @Dev_Amiri 
                    if ($edit_details && $edit_details->num_rows > 0): ?>
                            <?php while($detail = $edit_details->fetch_assoc()): ?>
                                <div class="detail-item">
                                    <div class="form-group">
                                        <label>عنوان بخش:</label>
                                        <input type="text" name="detail_title[]" value="<?= htmlspecialchars($detail['title']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>نوع بخش:</label>
                                        <select name="detail_type[]" class="detail-type-select" onchange="changeDetailType(this)">
                                            <option value="text"<?= $detail['detail_type'] === 'text' ? ' selected' : '' ?>>متن</option>
                                            <option value="table"<?= $detail['detail_type'] === 'table' ? ' selected' : '' ?>>جدول</option>
                                            <option value="code"<?= $detail['detail_type'] === 'code' ? ' selected' : '' ?>>کد</option>
                                            <option value="button"<?= $detail['detail_type'] === 'button' ? ' selected' : '' ?>>دکمه</option>
                                        </select>
                                    </div>
                                    
                                    <?php if ($detail['detail_type'] === 'button'): ?>
                                        <div class="form-group button-fields">
                                            <label>متن دکمه:</label>
                                            <input type="text" name="button_text[]" value="<?= htmlspecialchars($detail['button_text']) ?>">
                                        </div>
                                        <div class="form-group button-fields">
                                            <label>لینک دکمه:</label>
                                            <input type="text" name="button_link[]" value="<?= htmlspecialchars($detail['button_link']) ?>">
                                        </div>
                                        <input type="hidden" name="detail_content[]" value="">
                                    <?php else: ?>
                                        <div class="form-group">
                                            <label>محتوا:<?= $detail['detail_type'] === 'table' ? ' (هر سطر در یک خط و ستون‌ها با | جدا شوند)' : '' ?></label>
                                            <textarea name="detail_content[]" rows="<?= $detail['detail_type'] === 'code' ? '8' : '4' ?>"><?= htmlspecialchars($detail['content']) ?></textarea>
                                        </div>
                                        <input type="hidden" name="button_text[]" value="">
                                        <input type="hidden" name="button_link[]" value="">
                                    <?php endif; ?>
                                    
                                    <button type="button" class="btn btn-danger remove-btn" onclick="removeDetail(this)">
                                        <i class="fas fa-trash"></i> حذف این بخش
                                    </button>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="detail-item">
                                <div class="form-group">
                                    <label>عنوان بخش:</label>
                                    <input type="text" name="detail_title[]">
                                </div>
                                <div class="form-group">
                                    <label>نوع بخش:</label>
                                    <select name="detail_type[]" class="detail-type-select" onchange="changeDetailType(this)">
                                        <option value="text">متن</option>
                                        <option value="table">جدول</option>
                                        <option value="code">کد</option>
                                        <option value="button">دکمه</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>محتوا:</label>
                                    <textarea name="detail_content[]" rows="4"></textarea>
                                </div>
                                <div class="form-group button-fields" style="display:none">
                                    <label>متن دکمه:</label>
                                    <input type="text" name="button_text[]">
                                </div>
                                <div class="form-group button-fields" style="display:none">
                                    <label>لینک دکمه:</label>
                                    <input type="text" name="button_link[]">
                                </div>
                                <button type="button" class="btn btn-danger remove-btn" onclick="removeDetail(this)">
                                    <i class="fas fa-trash"></i> حذف این بخش
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="btn btn-success" onclick="addDetail()">
                        <i class="fas fa-plus"></i> افزودن بخش جدید
                    </button>
                    
                    <button type="submit" class="btn btn-primary" style="margin-top:20px">
                        <?= isset($_GET['edit']) ? 'ویرایش سرویس' : 'افزودن سرویس' ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="services-list">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="جستجوی سرویس‌ها..." onkeyup="searchServices()">
            </div>
            
            <h2><i class="fas fa-list"></i> لیست سرویس‌ها</h2>
            
            <?php while($service = $services->fetch_assoc()): ?>
                <div class="service-item" data-name="<?= htmlspecialchars(strtolower($service['name'])) ?>">
                    <div class="service-info">
                        <i class="<?= htmlspecialchars($service['icon']) ?> service-icon"></i>
                        <div>
                            <h3><?= htmlspecialchars($service['name']) ?> <span class="status-badge status-<?= $service['is_active'] ? 'active' : 'inactive' ?>"><?= $service['is_active'] ? 'فعال' : 'غیرفعال' ?></span></h3>
                            <p><?= htmlspecialchars($service['short_desc']) ?></p>
                        </div>
                    </div>
                    <div class="service-actions">
                        <a href="admin.php?toggle=<?= $service['id'] ?>" class="toggle">
                            <i class="fas fa-power-off"></i> <?= $service['is_active'] ? 'غیرفعال' : 'فعال' ?>
                        </a>
                        <a href="admin.php?edit=<?= $service['id'] ?>" class="edit">
                            <i class="fas fa-edit"></i> ویرایش
                        </a>
                        <a href="admin.php?delete=<?= $service['id'] ?>" class="delete" onclick="return confirm('آیا مطمئن هستید؟')">
                            <i class="fas fa-trash"></i> حذف
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script>
    function addDetail() {
        const container = document.getElementById("details-container");
        const newDetail = document.createElement("div");
        newDetail.className = "detail-item";
        newDetail.innerHTML = `
            <div class="form-group">
                <label>عنوان بخش:</label>
                <input type="text" name="detail_title[]">
            </div>
            <div class="form-group">
                <label>نوع بخش:</label>
                <select name="detail_type[]" class="detail-type-select" onchange="changeDetailType(this)">
                    <option value="text">متن</option>
                    <option value="table">جدول</option>
                    <option value="code">کد</option>
                    <option value="button">دکمه</option>
                </select>
            </div>
            <div class="form-group">
                <label>محتوا:</label>
                <textarea name="detail_content[]" rows="4"></textarea>
            </div>
            <div class="form-group button-fields" style="display:none">
                <label>متن دکمه:</label>
                <input type="text" name="button_text[]">
            </div>
            <div class="form-group button-fields" style="display:none">
                <label>لینک دکمه:</label>
                <input type="text" name="button_link[]">
            </div>
            <button type="button" class="btn btn-danger remove-btn" onclick="removeDetail(this)">
                <i class="fas fa-trash"></i> حذف این بخش
            </button>
        `;
        container.appendChild(newDetail);
    }
    
    function removeDetail(btn) {
        btn.closest(".detail-item").remove();
    }
    
    function changeDetailType(select) {
        const item = select.closest(".detail-item");
        const contentField = item.querySelector("textarea[name='detail_content[]']");
        const buttonFields = item.querySelectorAll(".button-fields");
        
        if (select.value === "button") {
            contentField.style.display = "none";
            buttonFields.forEach(field => field.style.display = "block");
        } else {
            contentField.style.display = "block";
            buttonFields.forEach(field => field.style.display = "none");
            
            if (select.value === "code") {
                contentField.rows = 8;
            } else {
                contentField.rows = 4;
            }
        }
    }
    
    function searchServices() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toLowerCase();
        const services = document.querySelectorAll(".service-item");
        
        services.forEach(service => {
            const name = service.getAttribute("data-name");
            if (name.includes(filter)) {
                service.style.display = "flex";
            } else {
                service.style.display = "none";
            }
        });
    }
    
    document.querySelectorAll(".detail-type-select").forEach(select => {
        changeDetailType(select);
    });
    </script>
</body>
</html>