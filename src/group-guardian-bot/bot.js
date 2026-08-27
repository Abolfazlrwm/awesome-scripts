// ========== ربات نگهبان گروه (Group Guardian) ==========
// ضدهرزنامه، خوش‌آمدگویی + کپچا برای اعضای جدید، فیلتر کلمات، سیستم اخطار و دستورات مدیریتی

const TelegramBot = require('node-telegram-bot-api');
const fs = require('fs');
const path = require('path');

// ---------- تنظیمات ----------
const BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';   // توکن ربات را از @BotFather بگیر
const OWNER_ID = 123456789;                 // آیدی عددی خودت را از @userinfobot بگیر

const CAPTCHA_TIMEOUT_SECONDS = 60;   // مهلت کپچا برای عضو جدید (ثانیه)
const FLOOD_MESSAGE_LIMIT = 6;        // تعداد پیام مجاز
const FLOOD_TIME_WINDOW_MS = 8000;    // در این بازه‌ی زمانی (میلی‌ثانیه)
const FLOOD_MUTE_MINUTES = 10;        // مدت میوت شدن در صورت اسپم (دقیقه)
const MAX_WARNINGS = 3;               // تعداد اخطار مجاز قبل از میوت خودکار
const AUTO_MUTE_MINUTES = 60;         // مدت میوت پس از رسیدن به سقف اخطار (دقیقه)

const DEFAULT_BAD_WORDS = ['کلمه1', 'کلمه2']; // کلمات ممنوعه‌ی پیش‌فرض (قابل تغییر با /addword)

// ---------- پایگاه‌داده‌ی ساده (JSON) ----------
const DB_FILE = path.join(__dirname, 'guardian_data.json');

function loadDB() {
    if (!fs.existsSync(DB_FILE)) {
        return { groups: {} };
    }
    try {
        return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
    } catch (e) {
        return { groups: {} };
    }
}

function saveDB(db) {
    fs.writeFileSync(DB_FILE, JSON.stringify(db, null, 2), 'utf8');
}

function getGroup(db, chatId) {
    const key = String(chatId);
    if (!db.groups[key]) {
        db.groups[key] = {
            welcomeMessage: '👋 به گروه خوش اومدی {name}! لطفاً قوانین رو رعایت کن.',
            rules: 'قوانینی هنوز تنظیم نشده. ادمین می‌تونه با /setrules تنظیم کنه.',
            captchaEnabled: true,
            floodEnabled: true,
            badWords: [...DEFAULT_BAD_WORDS],
            warnings: {},       // { userId: count }
            pendingCaptcha: {}  // { userId: { messageId, timeout } }
        };
    }
    return db.groups[key];
}

let db = loadDB();

// ---------- راه‌اندازی ربات ----------
const bot = new TelegramBot(BOT_TOKEN, { polling: true });
console.log('🛡️ ربات نگهبان گروه روشن شد...');

// ردیابی پیام‌های اخیر هر کاربر برای تشخیص اسپم (در حافظه، نیازی به ذخیره‌ی دائمی نیست)
const messageTimestamps = {}; // { "chatId_userId": [ts1, ts2, ...] }

function isAdmin(chatId, userId) {
    return bot.getChatMember(chatId, userId).then(member => {
        return ['administrator', 'creator'].includes(member.status) || userId === OWNER_ID;
    }).catch(() => false);
}

// ---------- خوش‌آمدگویی + کپچا برای عضو جدید ----------
bot.on('new_chat_members', async (msg) => {
    const chatId = msg.chat.id;
    const group = getGroup(db, chatId);

    for (const newMember of msg.new_chat_members) {
        if (newMember.is_bot) continue;

        const name = newMember.first_name || 'کاربر';
        const welcomeText = group.welcomeMessage.replace('{name}', name);

        if (group.captchaEnabled) {
            // تا زمانی که کپچا رو نزنه، حق ارسال پیام نداره
            try {
                await bot.restrictChatMember(chatId, newMember.id, {
                    permissions: { can_send_messages: false }
                });
            } catch (e) { /* اگر ربات ادمین نباشه این خط خطا میده، بی‌خیالش میشیم */ }

            const sent = await bot.sendMessage(chatId,
                `${welcomeText}\n\n🔐 برای فعال شدن حساب، دکمه‌ی زیر رو تا ${CAPTCHA_TIMEOUT_SECONDS} ثانیه بزن:`,
                {
                    reply_markup: {
                        inline_keyboard: [[
                            { text: '✅ من ربات نیستم', callback_data: `captcha_${newMember.id}` }
                        ]]
                    }
                });

            group.pendingCaptcha[newMember.id] = { messageId: sent.message_id };
            saveDB(db);

            // اگه به‌موقع کپچا رو نزنه، از گروه حذفش کن
            setTimeout(async () => {
                if (group.pendingCaptcha[newMember.id]) {
                    try {
                        await bot.banChatMember(chatId, newMember.id);
                        await bot.unbanChatMember(chatId, newMember.id); // بن موقت = فقط اخراج
                        await bot.deleteMessage(chatId, sent.message_id).catch(() => {});
                        await bot.sendMessage(chatId, `⏰ ${name} به‌موقع کپچا رو تأیید نکرد و از گروه حذف شد.`);
                    } catch (e) { /* اگه ربات دسترسی نداشت، رد میشیم */ }
                    delete group.pendingCaptcha[newMember.id];
                    saveDB(db);
                }
            }, CAPTCHA_TIMEOUT_SECONDS * 1000);

        } else {
            await bot.sendMessage(chatId, welcomeText);
        }
    }
});

// ---------- تأیید کپچا ----------
bot.on('callback_query', async (query) => {
    const data = query.data;
    if (!data.startsWith('captcha_')) return;

    const targetUserId = parseInt(data.split('_')[1], 10);
    const chatId = query.message.chat.id;
    const group = getGroup(db, chatId);

    if (query.from.id !== targetUserId) {
        return bot.answerCallbackQuery(query.id, { text: '❌ این دکمه برای شما نیست!', show_alert: true });
    }

    try {
        await bot.restrictChatMember(chatId, targetUserId, {
            permissions: {
                can_send_messages: true,
                can_send_media_messages: true,
                can_send_other_messages: true,
                can_add_web_page_previews: true
            }
        });
    } catch (e) { /* اگه ربات دسترسی نداشت رد میشیم */ }

    delete group.pendingCaptcha[targetUserId];
    saveDB(db);

    await bot.answerCallbackQuery(query.id, { text: '✅ خوش اومدی!' });
    await bot.editMessageText('✅ کاربر تأیید شد و می‌تونه پیام بده.', {
        chat_id: chatId,
        message_id: query.message.message_id
    }).catch(() => {});
});

// ---------- بررسی هر پیام: ضدهرزنامه + فیلتر کلمات ----------
bot.on('message', async (msg) => {
    if (!msg.text || msg.chat.type === 'private') return;
    const chatId = msg.chat.id;
    const userId = msg.from.id;
    if (userId === OWNER_ID) return;

    const group = getGroup(db, chatId);
    const admin = await isAdmin(chatId, userId);
    if (admin) return; // روی ادمین‌ها اعمال نمی‌کنیم

    // --- فیلتر کلمات ممنوعه ---
    const lowerText = msg.text.toLowerCase();
    const hasBadWord = group.badWords.some(w => w && lowerText.includes(w.toLowerCase()));
    if (hasBadWord) {
        await bot.deleteMessage(chatId, msg.message_id).catch(() => {});
        return warnUser(chatId, userId, msg.from.first_name, 'استفاده از کلمات ممنوعه');
    }

    // --- ضدهرزنامه (Anti-flood) ---
    if (group.floodEnabled) {
        const key = `${chatId}_${userId}`;
        const now = Date.now();
        if (!messageTimestamps[key]) messageTimestamps[key] = [];
        messageTimestamps[key] = messageTimestamps[key].filter(ts => now - ts < FLOOD_TIME_WINDOW_MS);
        messageTimestamps[key].push(now);

        if (messageTimestamps[key].length > FLOOD_MESSAGE_LIMIT) {
            messageTimestamps[key] = [];
            const untilDate = Math.floor(Date.now() / 1000) + FLOOD_MUTE_MINUTES * 60;
            try {
                await bot.restrictChatMember(chatId, userId, {
                    permissions: { can_send_messages: false },
                    until_date: untilDate
                });
                await bot.sendMessage(chatId,
                    `🚫 ${msg.from.first_name} به دلیل ارسال پیام‌های پی‌درپی (اسپم) به مدت ${FLOOD_MUTE_MINUTES} دقیقه میوت شد.`);
            } catch (e) { /* ربات دسترسی نداشت */ }
        }
    }
});

function warnUser(chatId, userId, firstName, reason) {
    const group = getGroup(db, chatId);
    group.warnings[userId] = (group.warnings[userId] || 0) + 1;
    const count = group.warnings[userId];
    saveDB(db);

    if (count >= MAX_WARNINGS) {
        const untilDate = Math.floor(Date.now() / 1000) + AUTO_MUTE_MINUTES * 60;
        bot.restrictChatMember(chatId, userId, {
            permissions: { can_send_messages: false },
            until_date: untilDate
        }).catch(() => {});
        group.warnings[userId] = 0;
        saveDB(db);
        return bot.sendMessage(chatId,
            `⚠️ ${firstName} به سقف اخطار (${MAX_WARNINGS}) رسید و به مدت ${AUTO_MUTE_MINUTES} دقیقه میوت شد.\nدلیل آخرین اخطار: ${reason}`);
    }

    return bot.sendMessage(chatId,
        `⚠️ اخطار ${count}/${MAX_WARNINGS} برای ${firstName}\nدلیل: ${reason}`);
}

// ---------- دستورات عمومی ----------
bot.onText(/^\/start$/, (msg) => {
    bot.sendMessage(msg.chat.id,
        '🛡️ سلام! من ربات نگهبان گروهم.\nمنو به گروهت اضافه کن و ادمینم کن تا شروع کنم به محافظت از گروه.\n\nدستور /help رو بزن برای لیست کامل قابلیت‌ها.');
});

bot.onText(/^\/help$/, (msg) => {
    bot.sendMessage(msg.chat.id,
        `📖 <b>راهنمای ربات نگهبان</b>\n\n` +
        `<b>دستورات عمومی:</b>\n` +
        `/rules — نمایش قوانین گروه\n\n` +
        `<b>دستورات ادمین (فقط داخل گروه):</b>\n` +
        `/warn (ریپلای) — اخطار به کاربر\n` +
        `/unwarn (ریپلای) — کم کردن یک اخطار\n` +
        `/mute (ریپلای) [دقیقه] — میوت کاربر\n` +
        `/unmute (ریپلای) — رفع میوت\n` +
        `/kick (ریپلای) — اخراج کاربر\n` +
        `/ban (ریپلای) — بن دائم کاربر\n` +
        `/unban [آیدی عددی] — رفع بن\n` +
        `/setrules [متن] — تنظیم قوانین گروه\n` +
        `/setwelcome [متن] — تنظیم پیام خوش‌آمدگویی (از {name} برای اسم استفاده کن)\n` +
        `/addword [کلمه] — افزودن کلمه به فیلتر\n` +
        `/removeword [کلمه] — حذف کلمه از فیلتر\n` +
        `/togglecaptcha — روشن/خاموش کردن کپچای ورود\n` +
        `/toggleflood — روشن/خاموش کردن ضدهرزنامه`,
        { parse_mode: 'HTML' });
});

bot.onText(/^\/rules$/, (msg) => {
    const group = getGroup(db, msg.chat.id);
    bot.sendMessage(msg.chat.id, `📜 <b>قوانین گروه:</b>\n\n${group.rules}`, { parse_mode: 'HTML' });
});

// ---------- دستورات مدیریتی (فقط ادمین) ----------
async function requireAdmin(msg) {
    if (msg.chat.type === 'private') {
        await bot.sendMessage(msg.chat.id, '❌ این دستور فقط داخل گروه کار می‌کنه.');
        return false;
    }
    const admin = await isAdmin(msg.chat.id, msg.from.id);
    if (!admin) {
        await bot.sendMessage(msg.chat.id, '⛔ این دستور فقط برای ادمین‌هاست.');
        return false;
    }
    return true;
}

function getReplyTargetId(msg) {
    return msg.reply_to_message ? msg.reply_to_message.from.id : null;
}

bot.onText(/^\/warn$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    warnUser(msg.chat.id, targetId, msg.reply_to_message.from.first_name, 'اخطار دستی توسط ادمین');
});

bot.onText(/^\/unwarn$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    const group = getGroup(db, msg.chat.id);
    group.warnings[targetId] = Math.max(0, (group.warnings[targetId] || 0) - 1);
    saveDB(db);
    bot.sendMessage(msg.chat.id, `✅ یک اخطار کم شد. اخطار فعلی: ${group.warnings[targetId]}`);
});

bot.onText(/^\/mute(?:\s+(\d+))?$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    const minutes = match[1] ? parseInt(match[1], 10) : 60;
    const untilDate = Math.floor(Date.now() / 1000) + minutes * 60;
    try {
        await bot.restrictChatMember(msg.chat.id, targetId, {
            permissions: { can_send_messages: false },
            until_date: untilDate
        });
        bot.sendMessage(msg.chat.id, `🔇 کاربر به مدت ${minutes} دقیقه میوت شد.`);
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا: مطمئن شو ربات ادمین گروه با دسترسی محدودسازی کاربران هست.');
    }
});

bot.onText(/^\/unmute$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    try {
        await bot.restrictChatMember(msg.chat.id, targetId, {
            permissions: {
                can_send_messages: true,
                can_send_media_messages: true,
                can_send_other_messages: true,
                can_add_web_page_previews: true
            }
        });
        bot.sendMessage(msg.chat.id, '🔊 میوت کاربر برداشته شد.');
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در رفع میوت.');
    }
});

bot.onText(/^\/kick$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    try {
        await bot.banChatMember(msg.chat.id, targetId);
        await bot.unbanChatMember(msg.chat.id, targetId); // بن+آنبن = فقط اخراج، نه بن دائم
        bot.sendMessage(msg.chat.id, '👢 کاربر از گروه اخراج شد.');
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در اخراج کاربر.');
    }
});

bot.onText(/^\/ban$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = getReplyTargetId(msg);
    if (!targetId) return bot.sendMessage(msg.chat.id, '❗ روی پیام کاربر ریپلای کن.');
    try {
        await bot.banChatMember(msg.chat.id, targetId);
        bot.sendMessage(msg.chat.id, '🚫 کاربر برای همیشه بن شد.');
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در بن کردن کاربر.');
    }
});

bot.onText(/^\/unban\s+(\d+)$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const targetId = parseInt(match[1], 10);
    try {
        await bot.unbanChatMember(msg.chat.id, targetId);
        bot.sendMessage(msg.chat.id, '✅ کاربر آنبن شد.');
    } catch (e) {
        bot.sendMessage(msg.chat.id, '❌ خطا در آنبن کردن کاربر.');
    }
});

bot.onText(/^\/setrules\s+([\s\S]+)$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    group.rules = match[1];
    saveDB(db);
    bot.sendMessage(msg.chat.id, '✅ قوانین گروه به‌روزرسانی شد.');
});

bot.onText(/^\/setwelcome\s+([\s\S]+)$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    group.welcomeMessage = match[1];
    saveDB(db);
    bot.sendMessage(msg.chat.id, '✅ پیام خوش‌آمدگویی به‌روزرسانی شد.');
});

bot.onText(/^\/addword\s+(\S+)$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    if (!group.badWords.includes(match[1])) {
        group.badWords.push(match[1]);
        saveDB(db);
    }
    bot.sendMessage(msg.chat.id, `✅ کلمه‌ی «${match[1]}» به فیلتر اضافه شد.`);
});

bot.onText(/^\/removeword\s+(\S+)$/, async (msg, match) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    group.badWords = group.badWords.filter(w => w !== match[1]);
    saveDB(db);
    bot.sendMessage(msg.chat.id, `✅ کلمه‌ی «${match[1]}» از فیلتر حذف شد.`);
});

bot.onText(/^\/togglecaptcha$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    group.captchaEnabled = !group.captchaEnabled;
    saveDB(db);
    bot.sendMessage(msg.chat.id, `🔐 کپچای ورود ${group.captchaEnabled ? 'فعال' : 'غیرفعال'} شد.`);
});

bot.onText(/^\/toggleflood$/, async (msg) => {
    if (!(await requireAdmin(msg))) return;
    const group = getGroup(db, msg.chat.id);
    group.floodEnabled = !group.floodEnabled;
    saveDB(db);
    bot.sendMessage(msg.chat.id, `🌊 ضدهرزنامه ${group.floodEnabled ? 'فعال' : 'غیرفعال'} شد.`);
});

// ---------- مدیریت خطاها ----------
bot.on('polling_error', (err) => console.error('❌ خطای polling:', err.message));
