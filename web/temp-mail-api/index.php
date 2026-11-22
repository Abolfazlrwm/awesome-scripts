<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

//========== Configurations ==========
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

//========== Classes ==========
class API {
    private $baseUrl = 'https://api.internal.temp-mail.io/api/v2';

    private function SendRequest($endpoint, $method = 'GET', $payload = null) {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST' && $payload) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function CreateMail($username, $domain) {
        return $this->SendRequest('/email/new', 'POST', [
            'name' => $username,
            'domain' => $domain
        ]);
    }

    public function fetch() {
        $response = $this->SendRequest('/domains');
        return $response['domains'] ?? [];
    }

    public function FetchMessages($email) {
        $response = $this->SendRequest('/email/' . $email . '/messages');
        return $response ?: 'Inbox is currently empty!';
    }
}

class FormatResponse {
    public static function format($status, $results) {
        return json_encode([
            'status' => $status,
            'channel' => '@Sourrce_kade',
            'owner' => '@DevMrErfi',
            'results' => $results
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

//========== Main Logic ==========
$api = new API();

if (!empty($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'createEmail':
            $domains = $api->fetch();
            if (!empty($domains)) {
                $email = $api->CreateMail('ErFan' . rand(100, 999), $domains[0]);
                echo FormatResponse::format(true, $email);
            } else {
                echo FormatResponse::format(false, 'No domains available!');
            }
            break;

        case 'getMessages':
            if (!empty($_GET['email'])) {
                $messages = $api->FetchMessages($_GET['email']);
                echo FormatResponse::format(true, $messages);
            } else {
                echo FormatResponse::format(false, 'Email parameter is missing!');
            }
            break;

        default:
            echo FormatResponse::format(false, 'Invalid action! Use createEmail or getMessages.');
            break;
    }
} else {
    echo FormatResponse::format(false, 'Action parameter is missing!');
}
?>
