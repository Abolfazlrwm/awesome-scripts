// ========== ربات نرخ لحظه‌ای رمزارز (Price Ticker Bot) ==========
// نمایش قیمت لحظه‌ای رمزارزها (از CoinGecko - رایگان و بدون نیاز به کلید API)
// + هشدار قیمت شخصی + ارسال خودکار آپدیت قیمت به کانال/گروه در بازه‌ی زمانی مشخص

const TelegramBot = require('node-telegram-bot-api');
const fs = require('fs');
const path = require('path');

// ---------- تنظیمات ----------
const BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';   // توکن ربات را از @BotFather بگیر
const ADMIN_IDS = [123456789];              // آیدی عددی مدیران (برای دستورات مدیریتی)

const CHECK_INTERVAL_MINUTES = 5;           // هر چند دقیقه هشدارهای قیمت چک بشه
const SCHEDULED_UPDATE_HOURS = 6;           // هر چند ساعت آپدیت خودکار به مشترکین ارسال بشه

// لیست پیش‌فرض کوین‌های قابل استعلام (شناسه‌ی CoinGecko : نماد نمایشی)
const DEFAULT_COINS = {
    bitcoin: 'BTC',
    ethereum: 'ETH',
    tether: 'USDT',
    'binancecoin': 'BNB',
    ripple: 'XRP',
    dogecoin: 'DOGE',
    'the-open-network': 'TON'
};

// ---------- پایگاه‌داده‌ی ساده (JSON) ----------
const DB_FILE = path.join(__dirname, 'price_data.json');

function loadDB() {
    if (!fs.existsSync(DB_FILE)) {
        return { alerts: [], subscribers: [], trackedCoins: { ...DEFAULT_COINS } };
    }
    try {
        const data = JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
        if (!data.trackedCoins) data.trackedCoins = { ...DEFAULT_COINS };
        return data;
    } catch (e) {
        return { alerts: [], subscribers: [], trackedCoins: { ...DEFAULT_COINS } };
    }
}

function saveDB(db) {
    fs.writeFileSync(DB_FILE, JSON.stringify(db, null, 2), 'utf8');
}

let db = loadDB();

function isAdmin(userId) {
    return ADMIN_IDS.includes(userId);
}

// ---------- راه‌اندازی ربات ----------
const bot = new TelegramBot(BOT_TOKEN, { polling: true });
console.log('💰 ربات نرخ رمزارز روشن شد...');

// ---------- دریافت قیمت از CoinGecko ----------
async function fetchPrices(coinIds) {
    const idsParam = coinIds.join(',');
    const url = `https://api.coingecko.com/api/v3/simple/price?ids=${idsParam}&vs_currencies=usd&include_24hr_change=true`;
    const res = await fetch(url);
    if (!res.ok) throw new Error(`خطای API: ${res.status}`);
    return res.json();
}

function formatPriceLine(symbol, priceData) {
    if (!priceData) return `${symbol}: اطلاعاتی یافت نشد`;
    const price = priceData.usd;
    const change = priceData.usd_24h_change;
    const changeIcon = change >= 0 ? '🟢' : '🔴';
    const changeText = change !== undefined ? `${changeIcon} ${change.toFixed(2)}%` : '';
    return `💠 <b>${symbol}</b>: $${price.toLocaleString('en-US', { maximumFractionDigits: 6 })} ${changeText}`;
}

// ---------- دستور /price : نمایش قیمت همه‌ی کوین‌های ردیابی‌شده ----------
bot.onText(/^\/price$/, async (msg) => {
    try {
        const coinIds = Object.keys(db.trackedCoins);
        const prices = await fetchPrices(coinIds);
        const lines = coinIds.map(id => formatPriceLine(db.trackedCoins[id], prices[id]));
        bot.sendMessage(msg.chat.id, `📊 <b>نرخ لحظه‌ای رمزارزها</b>\n\n${lines.join('\n')}`, { parse_mode: 'HTML' });
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در دریافت قیمت. کمی بعد دوباره امتحان کن.');
    }
});

// ---------- دستور /coin [نماد] : قیمت یک کوین خاص ----------
bot.onText(/^\/coin\s+(\S+)$/, async (msg, match) => {
    const query = match[1].toLowerCase();
    const coinId = Object.keys(db.trackedCoins).find(
        id => id === query || db.trackedCoins[id].toLowerCase() === query
    );
    if (!coinId) {
        return bot.sendMessage(msg.chat.id,
            `❌ این کوین توی لیست ردیابی نیست.\nبرای دیدن لیست کامل /coinlist رو بزن.`);
    }
    try {
        const prices = await fetchPrices([coinId]);
        bot.sendMessage(msg.chat.id, formatPriceLine(db.trackedCoins[coinId], prices[coinId]), { parse_mode: 'HTML' });
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در دریافت قیمت.');
    }
});

bot.onText(/^\/coinlist$/, (msg) => {
    const lines = Object.entries(db.trackedCoins).map(([id, symbol]) => `• ${symbol} (${id})`);
    bot.sendMessage(msg.chat.id, `📋 <b>کوین‌های قابل استعلام:</b>\n\n${lines.join('\n')}`, { parse_mode: 'HTML' });
});

// ---------- هشدار قیمت شخصی ----------
// فرمت: /alert btc above 70000  یا  /alert btc below 60000
bot.onText(/^\/alert\s+(\S+)\s+(above|below)\s+([\d.]+)$/i, (msg, match) => {
    const symbolQuery = match[1].toLowerCase();
    const direction = match[2].toLowerCase();
    const targetPrice = parseFloat(match[3]);

    const coinId = Object.keys(db.trackedCoins).find(
        id => id === symbolQuery || db.trackedCoins[id].toLowerCase() === symbolQuery
    );
    if (!coinId) {
        return bot.sendMessage(msg.chat.id, '❌ این کوین توی لیست ردیابی نیست. با /coinlist لیست رو ببین.');
    }

    db.alerts.push({
        userId: msg.from.id,
        chatId: msg.chat.id,
        coinId,
        symbol: db.trackedCoins[coinId],
        direction,
        targetPrice,
        createdAt: Date.now()
    });
    saveDB(db);

    bot.sendMessage(msg.chat.id,
        `🔔 هشدار ثبت شد: وقتی ${db.trackedCoins[coinId]} ${direction === 'above' ? 'بالاتر از' : 'پایین‌تر از'} $${targetPrice} بشه بهت خبر می‌دم.`);
});

bot.onText(/^\/myalerts$/, (msg) => {
    const userAlerts = db.alerts.filter(a => a.userId === msg.from.id);
    if (userAlerts.length === 0) return bot.sendMessage(msg.chat.id, 'هیچ هشدار فعالی نداری.');
    const lines = userAlerts.map((a, i) =>
        `${i + 1}. ${a.symbol} ${a.direction === 'above' ? '>' : '<'} $${a.targetPrice}`);
    bot.sendMessage(msg.chat.id, `🔔 هشدارهای فعال تو:\n\n${lines.join('\n')}`);
});

bot.onText(/^\/clearalerts$/, (msg) => {
    db.alerts = db.alerts.filter(a => a.userId !== msg.from.id);
    saveDB(db);
    bot.sendMessage(msg.chat.id, '🗑 همه‌ی هشدارهای تو پاک شد.');
});

// ---------- اشتراک آپدیت خودکار قیمت ----------
bot.onText(/^\/subscribe$/, (msg) => {
    const chatId = msg.chat.id;
    if (!db.subscribers.includes(chatId)) {
        db.subscribers.push(chatId);
        saveDB(db);
    }
    bot.sendMessage(chatId, `✅ این چت مشترک آپدیت خودکار قیمت شد (هر ${SCHEDULED_UPDATE_HOURS} ساعت).`);
});

bot.onText(/^\/unsubscribe$/, (msg) => {
    db.subscribers = db.subscribers.filter(id => id !== msg.chat.id);
    saveDB(db);
    bot.sendMessage(msg.chat.id, '✅ اشتراک آپدیت خودکار لغو شد.');
});

// ---------- دستورات مدیریتی: افزودن/حذف کوین از لیست ردیابی ----------
bot.onText(/^\/addcoin\s+(\S+)\s+(\S+)$/, (msg, match) => {
    if (!isAdmin(msg.from.id)) return;
    const [, coinId, symbol] = match;
    db.trackedCoins[coinId.toLowerCase()] = symbol.toUpperCase();
    saveDB(db);
    bot.sendMessage(msg.chat.id, `✅ ${symbol.toUpperCase()} (شناسه‌ی CoinGecko: ${coinId}) به لیست اضافه شد.`);
});

bot.onText(/^\/removecoin\s+(\S+)$/, (msg, match) => {
    if (!isAdmin(msg.from.id)) return;
    const coinId = match[1].toLowerCase();
    delete db.trackedCoins[coinId];
    saveDB(db);
    bot.sendMessage(msg.chat.id, `✅ کوین با شناسه‌ی ${coinId} حذف شد.`);
});

bot.onText(/^\/start$/, (msg) => {
    bot.sendMessage(msg.chat.id,
        '💰 سلام! من ربات نرخ لحظه‌ای رمزارزم.\n\n' +
        '/price — نرخ همه‌ی کوین‌های ردیابی‌شده\n' +
        '/coin [نماد] — نرخ یک کوین خاص (مثلاً /coin btc)\n' +
        '/coinlist — لیست کوین‌های قابل استعلام\n' +
        '/alert [نماد] [above|below] [قیمت] — تنظیم هشدار قیمت\n' +
        '/myalerts — لیست هشدارهای من\n' +
        '/subscribe — اشتراک آپدیت خودکار در این چت');
});

// ---------- حلقه‌ی بررسی هشدارها (هر چند دقیقه یک‌بار) ----------
async function checkAlerts() {
    if (db.alerts.length === 0) return;
    const coinIds = [...new Set(db.alerts.map(a => a.coinId))];
    let prices;
    try {
        prices = await fetchPrices(coinIds);
    } catch (e) {
        console.error('خطا در بررسی هشدارها:', e.message);
        return;
    }

    const remainingAlerts = [];
    for (const alert of db.alerts) {
        const currentPrice = prices[alert.coinId] ? prices[alert.coinId].usd : null;
        if (currentPrice === null) { remainingAlerts.push(alert); continue; }

        const triggered = alert.direction === 'above'
            ? currentPrice >= alert.targetPrice
            : currentPrice <= alert.targetPrice;

        if (triggered) {
            bot.sendMessage(alert.chatId,
                `🚨 هشدار قیمت! ${alert.symbol} به $${currentPrice.toLocaleString('en-US')} رسید ` +
                `(هدف: ${alert.direction === 'above' ? 'بالای' : 'پایین'} $${alert.targetPrice})`
            ).catch(() => {});
        } else {
            remainingAlerts.push(alert);
        }
    }
    db.alerts = remainingAlerts;
    saveDB(db);
}

setInterval(checkAlerts, CHECK_INTERVAL_MINUTES * 60 * 1000);

// ---------- ارسال دوره‌ای آپدیت قیمت به مشترکین ----------
async function sendScheduledUpdate() {
    if (db.subscribers.length === 0) return;
    const coinIds = Object.keys(db.trackedCoins);
    let prices;
    try {
        prices = await fetchPrices(coinIds);
    } catch (e) {
        console.error('خطا در ارسال آپدیت زمان‌بندی‌شده:', e.message);
        return;
    }
    const lines = coinIds.map(id => formatPriceLine(db.trackedCoins[id], prices[id]));
    const text = `📊 <b>آپدیت دوره‌ای قیمت رمزارزها</b>\n\n${lines.join('\n')}`;

    for (const chatId of db.subscribers) {
        bot.sendMessage(chatId, text, { parse_mode: 'HTML' }).catch(() => {});
    }
}

setInterval(sendScheduledUpdate, SCHEDULED_UPDATE_HOURS * 60 * 60 * 1000);

// ---------- مدیریت خطاها ----------
bot.on('polling_error', (err) => console.error('❌ خطای polling:', err.message));
