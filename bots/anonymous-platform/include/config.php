<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

$conn = new PDO('mysql:host=localhost;dbname='.$settings['dbname'],$settings['username'],$settings['password']);
$conn->exec('SET NAMES utf8mb4');

#database tables
define('admin','love_admin_1Zskz3TibDm6a1No9Bxekjqak9zQwQ');
define('amounts','love_amounts_qxP94Hcjdo37510cjemudnsCnd283Hpa84');
define('blocked','love_blocked_pan84JCo3Hck38Chuw739Ca0u84hc92Map3');
define('chatmsgs','love_chatmsgs_8xXnyAk7Bfuwbo4z5QK6hGWJOVi9eI');
define('chats','love_chats_KVXH2MNOq3gLRloGmvBpeuDkfECwQ0');
define('cron','love_cron_zSWcr2gkmpQs3Lhyqn9iBAOt5fdvbJ');
define('friends','love_friends_Zls47Cpa081LAri34095Cnsdlfp237');
define('likes','love_likes_la38Cnao38Pqmc601nncChaor83Cjie3');
define('notif','love_notif_UHbXF6kqPNVvm7yGOWEczJ5Y1oI8sg');
define('states','love_states_hoz34HRbJDuUZeAKmYNaEgvsnBCqGX');
define('payments','love_payments_MtsGw1HgsfFeujTF6pAyjRkbSmGJdB');
define('search','love_search_Mqo37Cn02Zpd749dnK947Aldheu39Cnei25');
define('users','love_users_cUWglHdnD27gXs2LqxeY5oZislGlqw');

?>