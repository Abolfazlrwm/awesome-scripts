<?php
/* ==============================
* 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
* Use, edit and redistribute only with attribution to @Abolfazl_rwm.
* ============================== */

/*
Developer: t.me/Dev_Amiri 
Channel : t.me/Sourrce_kade 
اصکی با ذکر منبع مجاز است !
*/
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


if (isset($_GET['service_id'])) {
    $service_id = (int)$_GET['service_id'];
    $ip = $db->real_escape_string($_SERVER['REMOTE_ADDR']);
    $db->query("INSERT INTO visits (service_id, ip_address, visit_time) VALUES ($service_id, '$ip', NOW())");
}

$search = isset($_GET['search']) ? $db->real_escape_string($_GET['search']) : '';
$where = "WHERE is_active = 1";
if (!empty($search)) {
    $where .= " AND (name LIKE '%$search%' OR short_desc LIKE '%$search%' OR full_desc LIKE '%$search%')";
}

$services = $db->query("SELECT * FROM services $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وب سرویس | مرجع وب‌سرویس‌های کاربردی</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6a11cb;
            --secondary: #2575fc;
            --accent: #ff4d4d;
            --text: #333;
            --light: #f8f9fa;
            --dark: #343a40;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --text-bg: #f1f8ff;
            --code-bg: #2d2d2d;
            --radius: 15px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 40px 0;
            text-align: center;
            border-radius: 0 0 var(--radius) var(--radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
            animation: shine 8s infinite linear;
        }
        
        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo i {
            margin-left: 15px;
            color: #ffcc00;
        }
        
        .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
        }
        
        .search-container {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 25px 15px 50px;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(106,17,203,0.2);
        }
        
        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        
        .section-title {
            text-align: center;
            margin: 40px 0 30px;
            position: relative;
            font-size: 2rem;
            color: var(--primary);
        }
        
        .section-title::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            margin: 15px auto;
            border-radius: 2px;
        }
        
        .services-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 50px;
        }
        
        .service-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .service-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .service-header:hover {
            background: linear-gradient(to right, #5a0db5, #1a65e0);
        }
        
        .service-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .service-title i {
            font-size: 1.8rem;
        }
        
        .service-arrow {
            transition: transform 0.3s ease;
        }
        
        .service-card.active .service-arrow {
            transform: rotate(180deg);
        }
        
        .service-body {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        
        .service-card.active .service-body {
            padding: 20px;
            max-height: 2000px;
        }
        
        .short-desc {
            margin-bottom: 20px;
            color: #666;
        }
        
        .full-desc {
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .detail-section h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
            border-right: 4px solid var(--primary);
            padding-right: 10px;
        }
        
        .detail-text {
            background: var(--text-bg);
            padding: 15px;
            border-radius: var(--radius);
            line-height: 1.8;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border-radius: var(--radius);
            overflow: hidden;
        }
        
        .detail-table th, .detail-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: right;
        }
        
        .detail-table th {
            background-color: var(--light);
        }
        
        .detail-code {
            background: var(--code-bg);
            color: #f8f8f2;
            padding: 15px;
            border-radius: var(--radius);
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
        
        .footer {
            background: var(--dark);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: 50px;
            border-radius: var(--radius) var(--radius) 0 0;
        }
        
        .footer p {
            margin-bottom: 15px;
        }
        
        .footer a {
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer a:hover {
            color: var(--secondary);
        }
        
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 100;
            transition: all 0.3s;
        }
        
        .admin-link:hover {
            background: var(--secondary);
            transform: scale(1.1);
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
        }
        
        @media (max-width: 768px) {
            .logo {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .service-header {
                padding: 15px;
            }
            
            .service-title i {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    

    <header>
        <div class="container">
            <div class="logo">
                <i class="fas fa-crystal"></i>
                وب سرویس
            </div>
            <div class="tagline">مرجع وب‌سرویس‌های کاربردی با کیفیت و پایداری بالا</div>
            <div class="social-links">
                <a href="https://t.me/Sourrce_kade" target="_blank"><i class="fab fa-telegram"></i></a>
                <a href="https://t.me/Dev_Amiri" target="_blank"><i class="fas fa-question-circle"></i></a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="جستجوی وب سرویس..." 
                   value="<?= htmlspecialchars($search) ?>"
                   onkeyup="if(event.keyCode === 13) searchServices()">
        </div>
        
        <h2 class="section-title">💎 لیست وب‌سرویس‌های کریستال</h2>
        
        <div class="services-container" id="servicesContainer">
            <?php if ($services->num_rows > 0):
            /*
Developer: t.me/Dev_Amiri 
Channel : t.me/Sourrce_kade 
اصکی با ذکر منبع مجاز است !
*/
            ?>
                <?php while($service = $services->fetch_assoc()): 
                    $details = $db->query("SELECT * FROM service_details WHERE service_id = {$service['id']} ORDER BY sort_order ASC");
                ?>
                <div class="service-card" data-name="<?= strtolower(htmlspecialchars($service['name'])) ?>">
                    <div class="service-header" onclick="toggleService(this)">
                        <div class="service-title">
                            <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                        </div>
                        <i class="fas fa-chevron-down service-arrow"></i>
                    </div>
                    <div class="service-body">
                        <p class="short-desc"><?= htmlspecialchars($service['short_desc']) ?></p>
                        
                        <?php if (!empty($service['full_desc'])): ?>
                        <div class="full-desc"><?= nl2br(htmlspecialchars($service['full_desc'])) ?></div>
                        <?php endif; ?>
                        
                        <?php while($detail = $details->fetch_assoc()): ?>
                            <div class="detail-section">
                                <?php if (!empty($detail['title'])): ?>
                                <h4><?= htmlspecialchars($detail['title']) ?></h4>
                                <?php endif; ?>
                                
                                <?php if ($detail['detail_type'] === 'text'): ?>
                                    <div class="detail-text"><?= nl2br(htmlspecialchars($detail['content'])) ?></div>
                                
                                <?php elseif ($detail['detail_type'] === 'table'): 
                                    $rows = explode("\n", $detail['content']);
                                ?>
                                    <table class="detail-table">
                                        <?php foreach($rows as $i => $row): 
                                            $cols = explode("|", $row);
                                        ?>
                                        <tr>
                                            <?php foreach($cols as $col): ?>
                                            <td><?= trim(htmlspecialchars($col)) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                
                                <?php elseif ($detail['detail_type'] === 'code'): ?>
                                    <pre class="detail-code"><code><?= htmlspecialchars($detail['content']) ?></code></pre>
                                
                                <?php elseif ($detail['detail_type'] === 'button'): ?>
                                    <a href="<?= htmlspecialchars($detail['button_link']) ?>" class="detail-button" target="_blank">
                                        <?= htmlspecialchars($detail['button_text']) ?> <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; color: #6c757d; margin-bottom: 20px;"></i>
                    <h3>نتیجه‌ای یافت نشد</h3>
                    <p>هیچ وب سرویسی مطابق با جستجوی شما وجود ندارد</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <p>وب سرویس - مرجع وب‌سرویس‌های کاربردی</p>
            <p>✅ <a href="https://t.me/Sourrce_kade" target="_blank">@Sourrce_kade</a></p>
            <p>💻 <a href="https://t.me/Dev_Amiri" target="_blank">@Dev_Amiri</a></p>
            <p>🌐 تمامی حقوق برای وب سرویس محفوظ است.</p>
        </div>
    </footer>

    <script>
    	
    function toggleService(header) {
        const card = header.parentElement;
        card.classList.toggle('active');
        if (card.classList.contains('active')) {
            document.querySelectorAll('.service-card').forEach(item => {
                if (item !== card && item.classList.contains('active')) {
                    item.classList.remove('active');
                }
            });
        }
    }
    
    function searchServices() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const url = new URL(window.location.href);
        if (searchTerm.trim() === '') {
            url.searchParams.delete('search');
        } else {
            url.searchParams.set('search', searchTerm);
        }
        window.location.href = url.toString();
    }
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            document.getElementById('searchInput').value = searchParam;
            filterServices(searchParam);
        }
        const serviceId = urlParams.get('service_id');
        if (serviceId) {
            const serviceCard = document.querySelector(`.service-card[data-id="${serviceId}"]`);
            if (serviceCard) {
                serviceCard.classList.add('active');
                serviceCard.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
   
    function filterServices(searchTerm) {
        const services = document.querySelectorAll('.service-card');
        let hasResults = false;
        services.forEach(service => {
            const serviceName = service.getAttribute('data-name');
            if (serviceName.includes(searchTerm.toLowerCase())) {
                service.style.display = '';
                hasResults = true;
            } else {
                service.style.display = 'none';
            }
        });
        const noResults = document.querySelector('.no-results');
        if (noResults) {
            noResults.style.display = hasResults ? 'none' : 'block';
        }
    }
    
    </script>
</body>
</html>