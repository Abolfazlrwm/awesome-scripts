<?php
/**
 * ============================================================
 *  AI Chat Web App — چت‌اپ هوش مصنوعی (OpenRouter)
 *  Developer: Abolfazl  |  Telegram: @Abolfazl_rwm
 *  Repo: https://github.com/Abolfazlrwm/awesome-scripts
 * ============================================================
 */

define('OPENROUTER_API_KEY', 'YOUR_OPENROUTER_API_KEY_HERE'); 
define('MODEL_NAME', 'openrouter/free');

if (isset($_GET['action']) && $_GET['action'] === 'chat') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['messages'])) {
        echo json_encode(['error' => 'داده‌های ارسالی نامعتبر هستند.']);
        exit;
    }

    if (OPENROUTER_API_KEY === 'YOUR_OPENROUTER_API_KEY_HERE' || empty(OPENROUTER_API_KEY)) {
        echo json_encode(['error' => 'لطفاً ابتدا کلید OPENROUTER_API_KEY را در ابتدای فایل index.php تنظیم کنید.']);
        exit;
    }

    $messages = $input['messages'];

    $payload = [
        'model' => MODEL_NAME,
        'messages' => $messages,
        'max_tokens' => 2048,
        'temperature' => 1.0,
        'top_p' => 0.95,
        'stream' => false,
        'reasoning' => [
            'enabled' => true
        ]
    ];

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $siteUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENROUTER_API_KEY,
        'HTTP-Referer: ' . $siteUrl,
        'X-Title: Abolfazl AI Chat'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo json_encode(['error' => 'خطای cURL: ' . curl_error($ch)]);
    } else {
        if ($httpCode !== 200) {
            $errDetails = json_decode($response, true);
            echo json_encode([
                'error' => 'سرور OpenRouter با کد وضعیت ' . $httpCode . ' پاسخ داد.',
                'details' => $errDetails ? $errDetails : $response
            ]);
        } else {
            echo $response;
        }
    }
    curl_close($ch);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گفتگو با هوش مصنوعی</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
        const savedTheme = localStorage.getItem('ai_theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        
        .markdown-body pre { background: #f1f5f9; color: #0f172a; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .dark .markdown-body pre { background: #1e293b; color: #f8fafc; }
        
        .markdown-body code { font-family: monospace; background: #e2e8f0; color: #0f172a; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-size: 0.9em; }
        .dark .markdown-body code { background: #334155; color: #f8fafc; }
        
        .markdown-body ul { list-style-type: disc; padding-right: 1.5rem; margin-block: 0.5rem; }
        .markdown-body ol { list-style-type: decimal; padding-right: 1.5rem; margin-block: 0.5rem; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 h-full flex flex-col relative overflow-hidden transition-colors duration-300">

    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/50 backdrop-blur-md px-6 py-4 flex items-center justify-between shrink-0 relative z-50 transition-colors duration-300">
        <button id="menu-btn" onclick="toggleMenu()" class="w-10 h-10 flex flex-col justify-center items-center gap-1.5 focus:outline-none relative z-50" aria-label="منوی گفتگوها">
            <span class="w-6 h-0.5 bg-slate-800 dark:bg-slate-200 transition-all duration-300 origin-center" id="line1" style="transform-origin: center;"></span>
            <span class="w-6 h-0.5 bg-slate-800 dark:bg-slate-200 transition-all duration-300" id="line2"></span>
            <span class="w-6 h-0.5 bg-slate-800 dark:bg-slate-200 transition-all duration-300 origin-center" id="line3" style="transform-origin: center;"></span>
        </button>

        <h1 class="font-bold text-slate-800 dark:text-slate-200 text-lg text-center flex-1">چت رایگان با هوش مصنوعی</h1>

        <div class="w-10"></div>
    </header>

    <div id="sidebar" class="fixed top-0 right-0 h-full w-80 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 z-40 transform translate-x-full transition-all duration-300 ease-out flex flex-col pt-20">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 shrink-0 transition-colors duration-300">
            <button onclick="startNewChat()" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white dark:text-slate-100 font-bold rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>گفتگوی جدید</span>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-2 no-scrollbar" id="conversation-list">
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800 shrink-0 transition-colors duration-300 mt-auto">
            <button onclick="openThemeModal()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                </svg>
                <span>انتخاب تم</span>
            </button>
        </div>
    </div>

    <div id="overlay" onclick="closeAllDrawersAndModals()" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm z-30 hidden transition-opacity duration-300"></div>

    <?php if (OPENROUTER_API_KEY === 'YOUR_OPENROUTER_API_KEY_HERE'): ?>
    <div class="bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-500/30 p-4 text-center text-amber-800 dark:text-amber-300 text-sm shrink-0 z-10 transition-colors duration-300">
        ⚠️ هشدار: کلید API اوپن‌روتر تنظیم نشده است. لطفاً فایل <code class="bg-amber-200 dark:bg-amber-950/80 px-2 py-0.5 rounded text-amber-900 dark:text-amber-400">index.php</code> را باز کرده و ثابت <code class="bg-amber-200 dark:bg-amber-950/80 px-2 py-0.5 rounded text-amber-900 dark:text-amber-400">OPENROUTER_API_KEY</code> را در خط ۸ تنظیم کنید.
    </div>
    <?php endif; ?>

    <main class="flex-1 overflow-y-auto p-6 space-y-6" id="chat-container">
    </main>

    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/30 p-4 shrink-0 transition-colors duration-300">
        <form onsubmit="sendMessage(event)" class="max-w-4xl mx-auto flex gap-3">
            <textarea id="user-input" rows="1" placeholder="پیام خود را بنویسید..." 
                class="flex-1 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:dark:border-blue-500/50 resize-none text-sm transition-colors"
                onkeydown="handleEnter(event)"></textarea>
            <button type="submit" id="send-btn" 
                class="bg-blue-600 hover:bg-blue-500 text-white dark:text-slate-950 font-bold px-6 py-3 rounded-xl flex items-center justify-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span>ارسال</span>
                <span id="loading-spinner" class="hidden w-4 h-4 border-2 border-slate-400 dark:border-slate-950 border-t-transparent rounded-full animate-spin"></span>
            </button>
        </form>
    </footer>

    <div id="theme-modal" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/70 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-6 space-y-6 shadow-2xl relative transition-colors duration-300">
            <button onclick="closeThemeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg transition-colors focus:outline-none" title="بستن">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="text-center pt-2">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg mb-1">انتخاب تم سایت</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">حالت نمایش مورد نظر خود را انتخاب کنید.</p>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <button onclick="setTheme('light')" id="theme-btn-light" class="flex-1 flex flex-col items-center gap-3 p-5 rounded-xl border-2 transition-all duration-200 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-blue-400 dark:hover:border-blue-500">
                    <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-slate-700 text-amber-500 dark:text-slate-400 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>
                    </div>
                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">تم روشن</span>
                </button>
                <button onclick="setTheme('dark')" id="theme-btn-dark" class="flex-1 flex flex-col items-center gap-3 p-5 rounded-xl border-2 transition-all duration-200 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-blue-400 dark:hover:border-blue-500">
                    <div class="w-14 h-14 rounded-full bg-slate-200 dark:bg-indigo-900/50 text-slate-500 dark:text-indigo-400 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                    </div>
                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">تم تیره</span>
                </button>
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/70 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-6 space-y-6 shadow-2xl transform transition-all duration-300">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-500 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.108 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg">حذف گفتگو</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">آیا از حذف این گفتگو مطمئن هستید؟</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button onclick="closeDeleteModal()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium text-sm transition-colors">
                    لغو
                </button>
                <button id="confirm-delete-btn" class="flex-1 py-2.5 bg-red-600 hover:bg-red-500 text-white dark:text-slate-950 font-bold rounded-xl text-sm transition-colors">
                    تایید
                </button>
            </div>
        </div>
    </div>

    <div id="rename-modal" class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/70 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-sm w-full p-6 space-y-5 shadow-2xl relative transition-all duration-300">
            <button onclick="closeRenameModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg transition-colors focus:outline-none" title="بستن">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="text-center pt-2">
                <h3 class="font-bold text-slate-900 dark:text-slate-100 text-lg mb-1">تغییر نام</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">عنوان دلخواه خود را برای این گفتگو وارد کنید.</p>
            </div>
            <div>
                <input type="text" id="rename-input" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:dark:border-blue-500/50 text-sm transition-colors text-center font-medium" placeholder="عنوان گفتگو..." onkeydown="if(event.key==='Enter') document.getElementById('confirm-rename-btn').click();" />
            </div>
            <div class="flex justify-center pt-2">
                <button id="confirm-rename-btn" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white dark:text-slate-950 font-bold rounded-xl text-sm transition-colors flex items-center justify-center">
                    تایید
                </button>
            </div>
        </div>
    </div>

    <script>
        let conversations = JSON.parse(localStorage.getItem('ai_conversations')) || [];
        let currentChatId = localStorage.getItem('ai_current_chat_id') || null;
        let chatCounter = parseInt(localStorage.getItem('ai_chat_counter')) || 0;

        let targetDeleteChatId = null;
        let targetRenameChatId = null;

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.chat-menu-container')) {
                document.querySelectorAll('.chat-dropdown-menu').forEach(menu => menu.classList.add('hidden'));
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            renderConversationsList();
            if (currentChatId) {
                const activeChat = conversations.find(c => c.id === currentChatId);
                if (activeChat) {
                    loadConversation(currentChatId);
                } else {
                    currentChatId = null;
                    saveConversations();
                    renderWelcomeMessage();
                }
            } else {
                renderWelcomeMessage();
            }
        });

        function closeAllDrawersAndModals() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('translate-x-0')) {
                toggleMenu();
            }
        }

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const line1 = document.getElementById('line1');
            const line2 = document.getElementById('line2');
            const line3 = document.getElementById('line3');
            
            const isOpen = sidebar.classList.contains('translate-x-0');
            
            if (isOpen) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                
                line1.style.transform = 'none';
                line2.style.opacity = '1';
                line3.style.transform = 'none';
            } else {
                sidebar.classList.remove('translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                
                line1.style.transform = 'translateY(8px) rotate(45deg)';
                line2.style.opacity = '0';
                line3.style.transform = 'translateY(-8px) rotate(-45deg)';
            }
        }

        function openThemeModal() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('translate-x-0')) {
                toggleMenu();
            }
            updateThemeModalUI();
            document.getElementById('theme-modal').classList.remove('hidden');
        }

        function closeThemeModal() {
            document.getElementById('theme-modal').classList.add('hidden');
        }

        function setTheme(theme) {
            localStorage.setItem('ai_theme', theme);
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateThemeModalUI();
        }

        function updateThemeModalUI() {
            const currentTheme = localStorage.getItem('ai_theme') || 'light';
            const btnLight = document.getElementById('theme-btn-light');
            const btnDark = document.getElementById('theme-btn-dark');
            
            if (currentTheme === 'dark') {
                btnDark.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btnDark.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800');
                
                btnLight.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btnLight.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800');
            } else {
                btnLight.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btnLight.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800');
                
                btnDark.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btnDark.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800');
            }
        }

        function renderWelcomeMessage() {
            const container = document.getElementById('chat-container');
            const welcomeText = 'سلام به سرویس چت با هوش مصنوعی خوش آمدید. جهت شروع کافیست پیام خود را بنویسید و ارسال کنید.';
            container.innerHTML = `
                <div class="flex gap-4 max-w-3xl">
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">🤖</div>
                    <div class="flex flex-col gap-1 items-start min-w-0 w-full max-w-[calc(100%-3rem)]">
                        <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl rounded-tr-none p-4 text-slate-700 dark:text-slate-300 text-sm leading-relaxed shadow-lg w-full">
                            ${welcomeText}
                        </div>
                        <button onclick="copyMessageText(this)" data-text="${escapeHtml(welcomeText)}" class="relative text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1 flex items-center justify-center mt-1" title="کپی پیام">
                            <svg class="copy-icon w-4 h-4 transition-all duration-300 transform scale-100 opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 16.5V19.5A2.25 2.25 0 0 1 13.5 21.75h-9a2.25 2.25 0 0 1-2.25-2.25v-9A2.25 2.25 0 0 1 4.5 8.25H7.5M15.75 16.5h-9A2.25 2.25 0 0 1 4.5 14.25v-9A2.25 2.25 0 0 1 6.75 3h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25Z" />
                            </svg>
                            <svg class="tick-icon w-4 h-4 transition-all duration-300 transform scale-50 opacity-0 absolute text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }

        function handleEnter(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(e);
            }
        }

        async function sendMessage(event) {
            event.preventDefault();
            const inputElement = document.getElementById('user-input');
            const messageText = inputElement.value.trim();
            if (!messageText) return;

            let activeChat = conversations.find(c => c.id === currentChatId);
            
            if (!activeChat) {
                chatCounter++;
                localStorage.setItem('ai_chat_counter', chatCounter);
                currentChatId = Date.now().toString();
                activeChat = {
                    id: currentChatId,
                    title: "گفتگوی " + chatCounter,
                    messages: []
                };
                conversations.unshift(activeChat); 
            }

            appendMessage('user', messageText);
            activeChat.messages.push({ role: 'user', content: messageText });
            saveConversations();
            renderConversationsList();
            inputElement.value = '';

            const sendBtn = document.getElementById('send-btn');
            const spinner = document.getElementById('loading-spinner');
            inputElement.disabled = true;
            sendBtn.disabled = true;
            spinner.classList.remove('hidden');

            try {
                const response = await fetch('?action=chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ messages: activeChat.messages })
                });

                const data = await response.json();

                if (data.error) {
                    appendMessage('system-error', data.error + (data.details ? ' \n' + JSON.stringify(data.details) : ''));
                } else if (data.choices && data.choices[0] && data.choices[0].message) {
                    const aiReply = data.choices[0].message.content;
                    appendMessage('ai', aiReply);
                    
                    activeChat.messages.push({ role: 'assistant', content: aiReply });
                    saveConversations();
                } else {
                    appendMessage('system-error', 'پاسخ نامشخصی از سرور دریافت شد.');
                }
            } catch (err) {
                appendMessage('system-error', 'خطا در ارتباط با سرور محلی: ' + err.message);
            } finally {
                inputFieldEnabled();
            }

            function inputFieldEnabled() {
                inputElement.disabled = false;
                sendBtn.disabled = false;
                spinner.classList.add('hidden');
                inputElement.focus();
            }
        }

        function toggleChatMenu(event, chatId) {
            event.stopPropagation();
            document.querySelectorAll('.chat-dropdown-menu').forEach(menu => {
                if (menu.id !== 'chat-menu-' + chatId) menu.classList.add('hidden');
            });
            const menu = document.getElementById('chat-menu-' + chatId);
            if (menu) menu.classList.toggle('hidden');
        }

        function openDeleteModal(event, chatId) {
            event.stopPropagation();
            const menu = document.getElementById('chat-menu-' + chatId);
            if (menu) menu.classList.add('hidden');
            
            targetDeleteChatId = chatId;
            document.getElementById('delete-modal').classList.remove('hidden');
            
            document.getElementById('confirm-delete-btn').onclick = () => {
                if (targetDeleteChatId) {
                    performDeleteConversation(targetDeleteChatId);
                }
                closeDeleteModal();
            };
        }

        function closeDeleteModal() {
            targetDeleteChatId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function performDeleteConversation(chatId) {
            conversations = conversations.filter(c => c.id !== chatId);
            if (currentChatId === chatId) {
                currentChatId = null;
                renderWelcomeMessage();
            }
            if (conversations.length === 0) {
                chatCounter = 0;
                localStorage.removeItem('ai_chat_counter');
            }
            saveConversations();
            renderConversationsList();
        }

        function openRenameModal(event, chatId) {
            event.stopPropagation();
            const menu = document.getElementById('chat-menu-' + chatId);
            if (menu) menu.classList.add('hidden');

            targetRenameChatId = chatId;
            const chat = conversations.find(c => c.id === chatId);
            const input = document.getElementById('rename-input');
            if (chat) {
                input.value = chat.title;
            }
            document.getElementById('rename-modal').classList.remove('hidden');
            input.focus();
            input.select();

            document.getElementById('confirm-rename-btn').onclick = () => {
                const newTitle = input.value.trim();
                if (newTitle && targetRenameChatId) {
                    performRenameConversation(targetRenameChatId, newTitle);
                }
                closeRenameModal();
            };
        }

        function closeRenameModal() {
            targetRenameChatId = null;
            document.getElementById('rename-modal').classList.add('hidden');
        }

        function performRenameConversation(chatId, newTitle) {
            const chat = conversations.find(c => c.id === chatId);
            if (chat) {
                chat.title = newTitle;
                saveConversations();
                renderConversationsList();
            }
        }

        function renderConversationsList() {
            const listContainer = document.getElementById('conversation-list');
            listContainer.innerHTML = '';

            if (conversations.length === 0) {
                listContainer.innerHTML = '<p class="text-center text-xs text-slate-500 py-4">هیچ گفتگویی یافت نشد.</p>';
                return;
            }

            conversations.forEach(chat => {
                const isActive = chat.id === currentChatId;
                const activeClasses = isActive ? 'bg-blue-50 dark:bg-blue-600/20 border-blue-300 dark:border-blue-500/50 text-blue-700 dark:text-blue-300' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300';
                
                const item = document.createElement('div');
                item.className = `flex items-center justify-between p-3 rounded-xl border text-sm transition-all duration-150 cursor-pointer ${activeClasses}`;
                item.setAttribute('onclick', `loadConversation('${chat.id}')`);
                
                const shortTitle = chat.title.length > 25 ? chat.title.substring(0, 25) + '...' : chat.title;

                item.innerHTML = `
                    <div class="flex items-center gap-2 overflow-hidden flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0 text-slate-500 dark:text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                        </svg>
                        <span class="truncate font-semibold">${escapeHtml(shortTitle)}</span>
                    </div>
                    <div class="relative shrink-0 chat-menu-container">
                        <button onclick="toggleChatMenu(event, '${chat.id}')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 p-1 rounded transition-colors text-xs focus:outline-none" title="گزینه‌ها">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                            </svg>
                        </button>
                        <div id="chat-menu-${chat.id}" class="hidden absolute left-0 mt-1 w-32 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl py-1.5 z-50 text-xs chat-dropdown-menu">
                            <button onclick="openDeleteModal(event, '${chat.id}')" class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.108 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>حذف</span>
                            </button>
                            <button onclick="openRenameModal(event, '${chat.id}')" class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span>تغییر نام</span>
                            </button>
                        </div>
                    </div>
                `;
                listContainer.appendChild(item);
            });
        }

        function loadConversation(chatId) {
            currentChatId = chatId;
            saveConversations();
            renderConversationsList();

            const activeChat = conversations.find(c => c.id === chatId);
            const container = document.getElementById('chat-container');
            container.innerHTML = '';

            if (activeChat && activeChat.messages.length > 0) {
                activeChat.messages.forEach(msg => {
                    appendMessage(msg.role === 'user' ? 'user' : 'ai', msg.content);
                });
            } else {
                renderWelcomeMessage();
            }

            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('translate-x-0')) {
                toggleMenu();
            }
        }

        function startNewChat() {
            currentChatId = null;
            saveConversations();
            renderWelcomeMessage();
            renderConversationsList();
            
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('translate-x-0')) {
                toggleMenu();
            }
        }

        function saveConversations() {
            localStorage.setItem('ai_conversations', JSON.stringify(conversations));
            localStorage.setItem('ai_current_chat_id', currentChatId);
        }

        function appendMessage(sender, text) {
            const container = document.getElementById('chat-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex gap-4 max-w-3xl animate-pulse';

            if (sender === 'user') {
                messageDiv.className += ' mr-auto flex-row-reverse';
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white dark:text-slate-950 flex items-center justify-center shrink-0 border border-blue-500 font-bold">شما</div>
                    <div class="flex flex-col gap-1 items-end min-w-0 max-w-[calc(100%-3rem)]">
                        <div class="bg-blue-50 dark:bg-slate-900 border border-blue-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-none p-4 text-sm leading-relaxed shadow-lg">
                            ${escapeHtml(text)}
                        </div>
                        <button onclick="copyMessageText(this)" data-text="${escapeHtml(text)}" class="relative text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1 flex items-center justify-center mt-1" title="کپی پیام">
                            <svg class="copy-icon w-4 h-4 transition-all duration-300 transform scale-100 opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 16.5V19.5A2.25 2.25 0 0 1 13.5 21.75h-9a2.25 2.25 0 0 1-2.25-2.25v-9A2.25 2.25 0 0 1 4.5 8.25H7.5M15.75 16.5h-9A2.25 2.25 0 0 1 4.5 14.25v-9A2.25 2.25 0 0 1 6.75 3h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25Z" />
                            </svg>
                            <svg class="tick-icon w-4 h-4 transition-all duration-300 transform scale-50 opacity-0 absolute text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>
                `;
            } else if (sender === 'ai') {
                const htmlContent = marked.parse(text);
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">🤖</div>
                    <div class="flex flex-col gap-1 items-start min-w-0 w-full max-w-[calc(100%-3rem)]">
                        <div class="markdown-body bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tr-none p-4 text-sm leading-relaxed shadow-lg overflow-x-auto w-full">
                            ${htmlContent}
                        </div>
                        <button onclick="copyMessageText(this)" data-text="${escapeHtml(text)}" class="relative text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1 flex items-center justify-center mt-1" title="کپی پیام">
                            <svg class="copy-icon w-4 h-4 transition-all duration-300 transform scale-100 opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 16.5V19.5A2.25 2.25 0 0 1 13.5 21.75h-9a2.25 2.25 0 0 1-2.25-2.25v-9A2.25 2.25 0 0 1 4.5 8.25H7.5M15.75 16.5h-9A2.25 2.25 0 0 1 4.5 14.25v-9A2.25 2.25 0 0 1 6.75 3h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25Z" />
                            </svg>
                            <svg class="tick-icon w-4 h-4 transition-all duration-300 transform scale-50 opacity-0 absolute text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>
                `;
            } else {
                messageDiv.className += ' mx-auto w-full';
                messageDiv.innerHTML = `
                    <div class="flex flex-col gap-1 items-center w-full">
                        <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-300 rounded-xl p-4 text-xs max-w-xl text-center w-full">
                            <strong>خطا:</strong> ${text}
                        </div>
                        <button onclick="copyMessageText(this)" data-text="${escapeHtml(text)}" class="relative text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1 flex items-center justify-center mt-1" title="کپی پیام">
                            <svg class="copy-icon w-4 h-4 transition-all duration-300 transform scale-100 opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 16.5V19.5A2.25 2.25 0 0 1 13.5 21.75h-9a2.25 2.25 0 0 1-2.25-2.25v-9A2.25 2.25 0 0 1 4.5 8.25H7.5M15.75 16.5h-9A2.25 2.25 0 0 1 4.5 14.25v-9A2.25 2.25 0 0 1 6.75 3h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25Z" />
                            </svg>
                            <svg class="tick-icon w-4 h-4 transition-all duration-300 transform scale-50 opacity-0 absolute text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>
                `;
            }

            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
            messageDiv.classList.remove('animate-pulse');
        }

        async function copyMessageText(btn) {
            const textToCopy = btn.getAttribute('data-text');
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(textToCopy);
                } else {
                    let textArea = document.createElement("textarea");
                    textArea.value = textToCopy;
                    textArea.style.position = "fixed";
                    textArea.style.left = "-999999px";
                    textArea.style.top = "-999999px";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                    } catch (err) {
                        console.error('Fallback copy failed', err);
                    }
                    textArea.remove();
                }
                
                const copyIcon = btn.querySelector('.copy-icon');
                const tickIcon = btn.querySelector('.tick-icon');
                
                copyIcon.classList.remove('scale-100', 'opacity-100');
                copyIcon.classList.add('scale-50', 'opacity-0');
                
                tickIcon.classList.remove('scale-50', 'opacity-0');
                tickIcon.classList.add('scale-100', 'opacity-100');
                
                setTimeout(() => {
                    tickIcon.classList.remove('scale-100', 'opacity-100');
                    tickIcon.classList.add('scale-50', 'opacity-0');
                    
                    copyIcon.classList.remove('scale-50', 'opacity-0');
                    copyIcon.classList.add('scale-100', 'opacity-100');
                }, 2000);
            } catch (err) {
                console.error('خطا در کپی:', err);
                alert('مرورگر شما از قابلیت کپی پشتیبانی نمی‌کند.');
            }
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>