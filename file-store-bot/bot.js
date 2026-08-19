// ========== ربات ذخیره فایل و لینک‌ساز (File Store Bot) ==========
// ادمین فایل رو به ربات می‌فرسته، ربات یه لینک اختصاصی می‌سازه؛
// هرکی روی لینک بزنه همون فایل رو از ربات دریافت می‌کنه.
// امکان دسته‌بندی چند فایل زیر یک لینک (Batch) و جوین اجباری هم داره.

const TelegramBot = require('node-telegram-bot-api');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

// ---------- تنظیمات ----------
const BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';    // توکن ربات را از @BotFather بگیر
const ADMIN_IDS = [123456789];               // لیست آیدی عددی مدیرانی که اجازه‌ی آپلود دارند
const FORCE_JOIN_CHANNEL = '';               // یوزرنیم کانال جوین اجباری، مثل "@mychannel" (خالی = غیرفعال)
const AUTO_DELETE_SECONDS = 0;               // حذف خودکار فایل ارسالی بعد از این‌قدر ثانیه (0 = غیرفعال)

// ---------- پایگاه‌داده‌ی ساده (JSON) ----------
const DB_FILE = path.join(__dirname, 'filestore_data.json');

function loadDB() {
    if (!fs.existsSync(DB_FILE)) {
        return { files: {}, batches: {}, stats: { totalDownloads: 0 } };
    }
    try {
        return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
    } catch (e) {
        return { files: {}, batches: {}, stats: { totalDownloads: 0 } };
    }
}

function saveDB(db) {
    fs.writeFileSync(DB_FILE, JSON.stringify(db, null, 2), 'utf8');
}

let db = loadDB();

function generateCode() {
    return crypto.randomBytes(6).toString('hex');
}

function isAdmin(userId) {
    return ADMIN_IDS.includes(userId);
}

// ---------- راه‌اندازی ربات ----------
const bot = new TelegramBot(BOT_TOKEN, { polling: true });
console.log('📁 ربات ذخیره فایل روشن شد...');

// حالت موقت هر ادمین در حافظه (برای batch mode)
const adminState = {}; // { adminId: { mode: 'batch', items: [...] } }

async function isMember(userId) {
    if (!FORCE_JOIN_CHANNEL) return true;
    try {
        const member = await bot.getChatMember(FORCE_JOIN_CHANNEL, userId);
        return ['member', 'administrator', 'creator'].includes(member.status);
    } catch (e) {
        return true; // اگه ربات دسترسی نداشت، مانع کاربر عادی نشیم
    }
}

function joinKeyboard() {
    return {
        inline_keyboard: [
            [{ text: '📢 عضویت در کانال', url: `https://t.me/${FORCE_JOIN_CHANNEL.replace('@', '')}` }],
            [{ text: '✅ عضو شدم، بررسی کن', callback_data: 'check_join' }]
        ]
    };
}

// ---------- استخراج اطلاعات فایل از پیام ----------
function extractFile(msg) {
    if (msg.document) return { type: 'document', file_id: msg.document.file_id, caption: msg.caption || '' };
    if (msg.photo) return { type: 'photo', file_id: msg.photo[msg.photo.length - 1].file_id, caption: msg.caption || '' };
    if (msg.video) return { type: 'video', file_id: msg.video.file_id, caption: msg.caption || '' };
    if (msg.audio) return { type: 'audio', file_id: msg.audio.file_id, caption: msg.caption || '' };
    if (msg.voice) return { type: 'voice', file_id: msg.voice.file_id, caption: msg.caption || '' };
    return null;
}

async function sendStoredFile(chatId, fileEntry) {
    const opts = fileEntry.caption ? { caption: fileEntry.caption } : {};
    let sent;
    switch (fileEntry.type) {
        case 'photo': sent = await bot.sendPhoto(chatId, fileEntry.file_id, opts); break;
        case 'video': sent = await bot.sendVideo(chatId, fileEntry.file_id, opts); break;
        case 'audio': sent = await bot.sendAudio(chatId, fileEntry.file_id, opts); break;
        case 'voice': sent = await bot.sendVoice(chatId, fileEntry.file_id, opts); break;
        default: sent = await bot.sendDocument(chatId, fileEntry.file_id, opts);
    }

    if (AUTO_DELETE_SECONDS > 0) {
        setTimeout(() => {
            bot.deleteMessage(chatId, sent.message_id).catch(() => {});
        }, AUTO_DELETE_SECONDS * 1000);
    }
}

// ---------- دستور /start (شامل دریافت فایل با دیپ‌لینک) ----------
bot.onText(/^\/start(?:\s+(\S+))?$/, async (msg, match) => {
    const chatId = msg.chat.id;
    const payload = match[1];

    if (!payload) {
        return bot.sendMessage(chatId,
            '📁 سلام! من ربات ذخیره‌سازی فایل هستم.\nهر لینک اختصاصی که برات فرستاده بشه رو اینجا باز کن تا فایلش رو برات بفرستم.');
    }

    if (!(await isMember(msg.from.id))) {
        return bot.sendMessage(chatId,
            '🔒 برای دریافت فایل، اول باید عضو کانال زیر بشی:',
            { reply_markup: joinKeyboard() });
    }

    await deliverPayload(chatId, payload);
});

async function deliverPayload(chatId, payload) {
    // batch: چند فایل زیر یک کد
    if (payload.startsWith('batch_')) {
        const code = payload.replace('batch_', '');
        const batch = db.batches[code];
        if (!batch) return bot.sendMessage(chatId, '❌ این لینک منقضی شده یا نامعتبره.');

        await bot.sendMessage(chatId, `📦 در حال ارسال ${batch.items.length} فایل...`);
        for (const fileEntry of batch.items) {
            await sendStoredFile(chatId, fileEntry);
        }
        db.stats.totalDownloads += 1;
        saveDB(db);
        return;
    }

    // فایل تکی
    const fileEntry = db.files[payload];
    if (!fileEntry) return bot.sendMessage(chatId, '❌ این لینک منقضی شده یا نامعتبره.');

    await sendStoredFile(chatId, fileEntry);
    db.stats.totalDownloads += 1;
    saveDB(db);
}

bot.on('callback_query', async (query) => {
    if (query.data !== 'check_join') return;
    const ok = await isMember(query.from.id);
    if (!ok) return bot.answerCallbackQuery(query.id, { text: '❌ هنوز عضو نشدی!', show_alert: true });

    await bot.answerCallbackQuery(query.id, { text: '✅ عضویت تأیید شد!' });
    // پیام حاوی دکمه معمولاً بدون payload اصلیه؛ کاربر رو راهنمایی می‌کنیم دوباره لینک اصلی رو بزنه
    await bot.editMessageText('✅ عضویت تأیید شد! لطفاً دوباره روی لینک دانلود اصلی بزن.', {
        chat_id: query.message.chat.id,
        message_id: query.message.message_id
    }).catch(() => {});
});

// ---------- دریافت فایل از ادمین (حالت تکی) ----------
bot.on('message', async (msg) => {
    if (msg.chat.type !== 'private') return;
    if (!isAdmin(msg.from.id)) return;

    const fileEntry = extractFile(msg);
    if (!fileEntry) return; // پیام متنی معمولی یا دستور دیگه، نادیده بگیر

    const state = adminState[msg.from.id];

    if (state && state.mode === 'batch') {
        state.items.push(fileEntry);
        return bot.sendMessage(msg.chat.id,
            `➕ فایل به دسته اضافه شد. (تعداد فعلی: ${state.items.length})\nبرای پایان و ساخت لینک، دستور /endbatch رو بزن.`);
    }

    // حالت تکی: بلافاصله لینک بساز
    const code = generateCode();
    db.files[code] = fileEntry;
    saveDB(db);

    const botInfo = await bot.getMe();
    const link = `https://t.me/${botInfo.username}?start=${code}`;
    bot.sendMessage(msg.chat.id, `✅ لینک اختصاصی فایل ساخته شد:\n${link}`);
});

// ---------- دستورات ادمین ----------
bot.onText(/^\/start_batch$|^\/startbatch$/, (msg) => {
    if (!isAdmin(msg.from.id) || msg.chat.type !== 'private') return;
    adminState[msg.from.id] = { mode: 'batch', items: [] };
    bot.sendMessage(msg.chat.id, '📦 حالت دسته‌ای فعال شد. حالا فایل‌ها رو یکی‌یکی بفرست، در آخر /endbatch رو بزن.');
});

bot.onText(/^\/end_batch$|^\/endbatch$/, async (msg) => {
    if (!isAdmin(msg.from.id) || msg.chat.type !== 'private') return;
    const state = adminState[msg.from.id];
    if (!state || state.mode !== 'batch' || state.items.length === 0) {
        return bot.sendMessage(msg.chat.id, '❌ هیچ دسته‌ی فعالی وجود نداره یا فایلی اضافه نکردی.');
    }

    const code = generateCode();
    db.batches[code] = { items: state.items, createdAt: Date.now() };
    saveDB(db);
    delete adminState[msg.from.id];

    const botInfo = await bot.getMe();
    const link = `https://t.me/${botInfo.username}?start=batch_${code}`;
    bot.sendMessage(msg.chat.id, `✅ لینک اختصاصی دسته (${db.batches[code].items.length} فایل) ساخته شد:\n${link}`);
});

bot.onText(/^\/stats$/, (msg) => {
    if (!isAdmin(msg.from.id)) return;
    const fileCount = Object.keys(db.files).length;
    const batchCount = Object.keys(db.batches).length;
    bot.sendMessage(msg.chat.id,
        `📊 <b>آمار ربات</b>\n\n` +
        `📄 فایل‌های تکی ذخیره‌شده: ${fileCount}\n` +
        `📦 دسته‌های ذخیره‌شده: ${batchCount}\n` +
        `⬇️ کل دانلودها: ${db.stats.totalDownloads}`,
        { parse_mode: 'HTML' });
});

bot.onText(/^\/help$/, (msg) => {
    if (!isAdmin(msg.from.id)) {
        return bot.sendMessage(msg.chat.id, 'برای دریافت فایل، فقط کافیه روی لینک اختصاصی که بهت داده شده بزنی.');
    }
    bot.sendMessage(msg.chat.id,
        `📖 <b>راهنمای ادمین</b>\n\n` +
        `— یک فایل (عکس/ویدیو/فایل/صدا) مستقیم برام بفرست تا لینکش رو بسازم.\n` +
        `/startbatch — شروع حالت دسته‌ای (چند فایل زیر یک لینک)\n` +
        `/endbatch — پایان حالت دسته‌ای و ساخت لینک\n` +
        `/stats — آمار کلی ربات`,
        { parse_mode: 'HTML' });
});

// ---------- مدیریت خطاها ----------
bot.on('polling_error', (err) => console.error('❌ خطای polling:', err.message));
