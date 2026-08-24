// ========== ربات مترجم متن (Translator Bot) ==========
// از API رایگان MyMemory برای ترجمه استفاده می‌کند (بدون نیاز به کلید API)

const TelegramBot = require('node-telegram-bot-api');

// ---------- تنظیمات ----------
const BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE'; // توکن ربات را از @BotFather بگیر

// زبان پیش‌فرض مقصد برای هر کاربر (در حافظه نگه‌داری می‌شود)
const userDefaultLang = {}; // { userId: 'en' }

const LANG_NAMES = {
    fa: 'فارسی', en: 'انگلیسی', ar: 'عربی', tr: 'ترکی',
    fr: 'فرانسوی', de: 'آلمانی', es: 'اسپانیایی', ru: 'روسی', zh: 'چینی'
};

const bot = new TelegramBot(BOT_TOKEN, { polling: true });
console.log('🌍 ربات مترجم روشن شد...');

// ---------- تابع ترجمه با MyMemory API ----------
async function translateText(text, targetLang, sourceLang = 'auto') {
    const langPair = `${sourceLang}|${targetLang}`;
    const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=${langPair}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`خطای API: ${res.status}`);
    const data = await res.json();
    if (!data.responseData || !data.responseData.translatedText) {
        throw new Error('ترجمه یافت نشد');
    }
    return data.responseData.translatedText;
}

// ---------- دستورات ----------
bot.onText(/^\/start$/, (msg) => {
    bot.sendMessage(msg.chat.id,
        '🌍 سلام! من ربات مترجمم.\n\n' +
        'فقط یه متن برام بفرست، خودکار به فارسی/انگلیسی ترجمه‌ش می‌کنم.\n\n' +
        '/lang [کد زبان] — تنظیم زبان مقصد پیش‌فرض (مثلاً /lang en)\n' +
        '/tr [کد زبان] [متن] — ترجمه به یک زبان خاص (مثلاً /tr fr سلام دنیا)\n' +
        '/langs — لیست کدهای زبان پشتیبانی‌شده');
});

bot.onText(/^\/langs$/, (msg) => {
    const lines = Object.entries(LANG_NAMES).map(([code, name]) => `${code} — ${name}`);
    bot.sendMessage(msg.chat.id, `🌐 <b>کدهای زبان:</b>\n\n${lines.join('\n')}`, { parse_mode: 'HTML' });
});

bot.onText(/^\/lang\s+(\S+)$/, (msg, match) => {
    const code = match[1].toLowerCase();
    userDefaultLang[msg.from.id] = code;
    bot.sendMessage(msg.chat.id, `✅ زبان مقصد پیش‌فرض تو روی «${LANG_NAMES[code] || code}» تنظیم شد.`);
});

bot.onText(/^\/tr\s+(\S+)\s+([\s\S]+)$/, async (msg, match) => {
    const targetLang = match[1].toLowerCase();
    const text = match[2];
    try {
        const translated = await translateText(text, targetLang);
        bot.sendMessage(msg.chat.id, `🌍 <b>ترجمه (${LANG_NAMES[targetLang] || targetLang}):</b>\n\n${translated}`, { parse_mode: 'HTML' });
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در ترجمه. زبان یا متن را بررسی کن.');
    }
});

// ---------- ترجمه‌ی خودکار پیام‌های عادی ----------
bot.on('message', async (msg) => {
    if (!msg.text || msg.text.startsWith('/')) return;

    const targetLang = userDefaultLang[msg.from.id] ||
        (/[\u0600-\u06FF]/.test(msg.text) ? 'en' : 'fa'); // اگه فارسی/عربی بود به انگلیسی، وگرنه به فارسی

    try {
        const translated = await translateText(msg.text, targetLang);
        bot.sendMessage(msg.chat.id, `🌍 ${translated}`, { reply_to_message_id: msg.message_id });
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در ترجمه. کمی بعد دوباره امتحان کن.');
    }
});

bot.on('polling_error', (err) => console.error('❌ خطای polling:', err.message));
