// ========== ربات کوتاه‌کننده‌ی لینک (URL Shortener Bot) ==========
// کاربر لینک می‌فرسته، ربات یه لینک کوتاه می‌سازه که از طریق یه سرور HTTP داخلی
// (که همراه ربات اجرا می‌شه) به آدرس اصلی ریدایرکت می‌شه و تعداد کلیک‌ها رو می‌شمره.

const TelegramBot = require('node-telegram-bot-api');
const http = require('http');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

// ---------- تنظیمات ----------
const BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';   // توکن ربات را از @BotFather بگیر
const HTTP_PORT = 3000;                     // پورت سرور ریدایرکت
const PUBLIC_BASE_URL = 'http://localhost:3000'; // بعد از دیپلوی، این را با دامنه‌ی واقعی‌ات جایگزین کن (مثلاً https://short.yourdomain.com)
const SHORT_CODE_LENGTH = 5;                // طول کد کوتاه تولیدشده

// ---------- پایگاه‌داده‌ی ساده (JSON) ----------
const DB_FILE = path.join(__dirname, 'shortener_data.json');

function loadDB() {
    if (!fs.existsSync(DB_FILE)) return { links: {} };
    try {
        return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
    } catch (e) {
        return { links: {} };
    }
}

function saveDB(db) {
    fs.writeFileSync(DB_FILE, JSON.stringify(db, null, 2), 'utf8');
}

let db = loadDB();

function generateCode() {
    let code;
    do {
        code = crypto.randomBytes(4).toString('base64url').slice(0, SHORT_CODE_LENGTH);
    } while (db.links[code]); // اطمینان از یکتا بودن کد
    return code;
}

function isValidUrl(text) {
    try {
        const u = new URL(text);
        return u.protocol === 'http:' || u.protocol === 'https:';
    } catch (e) {
        return false;
    }
}

// ---------- راه‌اندازی ربات (Long Polling) ----------
const bot = new TelegramBot(BOT_TOKEN, { polling: true });
console.log('🔗 ربات کوتاه‌کننده‌ی لینک روشن شد...');

bot.onText(/^\/start$/, (msg) => {
    bot.sendMessage(msg.chat.id,
        '🔗 سلام! هر لینکی برام بفرستی، برات کوتاهش می‌کنم و آمار کلیکش رو هم نگه می‌دارم.\n\n' +
        '/mylinks — لیست لینک‌های کوتاه‌شده‌ی تو\n' +
        '/stats [کد] — آمار کلیک یک لینک خاص');
});

// دریافت لینک از کاربر و ساخت نسخه‌ی کوتاه
bot.on('message', (msg) => {
    if (!msg.text || msg.text.startsWith('/')) return;
    const url = msg.text.trim();
    if (!isValidUrl(url)) return; // اگه لینک معتبر نبود، نادیده بگیر (ممکنه پیام معمولی باشه)

    const code = generateCode();
    db.links[code] = {
        url,
        ownerId: msg.from.id,
        clicks: 0,
        createdAt: Date.now()
    };
    saveDB(db);

    const shortUrl = `${PUBLIC_BASE_URL}/r/${code}`;
    bot.sendMessage(msg.chat.id,
        `✅ لینک کوتاه ساخته شد:\n${shortUrl}\n\n📊 برای دیدن آمار کلیک: /stats ${code}`);
});

bot.onText(/^\/mylinks$/, (msg) => {
    const myLinks = Object.entries(db.links).filter(([, v]) => v.ownerId === msg.from.id);
    if (myLinks.length === 0) return bot.sendMessage(msg.chat.id, 'هنوز هیچ لینکی نساختی.');

    const lines = myLinks.slice(-15).map(([code, data]) =>
        `🔗 ${PUBLIC_BASE_URL}/r/${code} — ${data.clicks} کلیک`);
    bot.sendMessage(msg.chat.id, `📋 <b>لینک‌های تو (۱۵ مورد آخر):</b>\n\n${lines.join('\n')}`, { parse_mode: 'HTML' });
});

bot.onText(/^\/stats\s+(\S+)$/, (msg, match) => {
    const code = match[1];
    const data = db.links[code];
    if (!data) return bot.sendMessage(msg.chat.id, '❌ همچین کدی پیدا نشد.');

    bot.sendMessage(msg.chat.id,
        `📊 <b>آمار لینک</b>\n\n` +
        `🔗 کد: <code>${code}</code>\n` +
        `🌍 مقصد: ${data.url}\n` +
        `👆 تعداد کلیک: ${data.clicks}\n` +
        `📅 ساخته‌شده: ${new Date(data.createdAt).toLocaleDateString('fa-IR')}`,
        { parse_mode: 'HTML' });
});

bot.on('polling_error', (err) => console.error('❌ خطای polling:', err.message));

// ---------- سرور HTTP داخلی برای ریدایرکت ----------
const server = http.createServer((req, res) => {
    const match = req.url.match(/^\/r\/([A-Za-z0-9_-]+)\/?$/);
    if (!match) {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        return res.end('لینک پیدا نشد.');
    }

    const code = match[1];
    const data = db.links[code];
    if (!data) {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        return res.end('این لینک منقضی شده یا وجود نداره.');
    }

    data.clicks += 1;
    saveDB(db);

    res.writeHead(302, { Location: data.url });
    res.end();
});

server.listen(HTTP_PORT, () => {
    console.log(`🌐 سرور ریدایرکت روی پورت ${HTTP_PORT} در حال اجراست`);
});
