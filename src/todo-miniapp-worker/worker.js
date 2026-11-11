/**
 * ============================================================
 *  ToDoList — Telegram Mini App
 *  یک فایل Worker واحد: Backend (API + Bot Webhook + Cron) + Frontend (Mini App)
 *  Stack: Cloudflare Workers, D1, KV, Telegram Bot API, Vanilla JS
 * ============================================================
 */

// ============================================================
// 1) CONSTANTS
// ============================================================
const DEFAULT_CATEGORIES = [
  { name: 'دانشگاه', icon: '🎓', color: '#6C5CE7' },
  { name: 'کار', icon: '💼', color: '#0984E3' },
  { name: 'شخصی', icon: '🏠', color: '#00B894' },
  { name: 'ایده‌ها', icon: '💡', color: '#FDCB6E' },
  { name: 'تفریح', icon: '🎮', color: '#E17055' },
];

const STREAK_MILESTONES = [3, 7, 30, 100];

const PRIORITY_LABELS = { low: '🔵 کم', normal: '🟢 عادی', high: '🟠 بالا', urgent: '🔴 فوری' };

// ============================================================
// 2) UTILITIES
// ============================================================
function json(data, status = 200, extraHeaders = {}) {
  return new Response(JSON.stringify(data), {
    status,
    headers: {
      'Content-Type': 'application/json; charset=utf-8',
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Content-Type, Authorization',
      'Access-Control-Allow-Methods': 'GET, POST, PATCH, DELETE, OPTIONS',
      ...extraHeaders,
    },
  });
}

function errorResponse(message, status = 400, details = null) {
  return json({ ok: false, error: message, details }, status);
}

function nowTs() {
  return Math.floor(Date.now() / 1000);
}

function isNonEmptyString(v) {
  return typeof v === 'string' && v.trim().length > 0;
}

function sanitizeText(v, maxLen = 2000) {
  if (typeof v !== 'string') return '';
  return v.trim().slice(0, maxLen);
}

// --- Timezone helpers (پشتیبانی از منطقهٔ زمانی اختصاصی هر کاربر) ---
// ریشهٔ باگ قبلی: تمام محاسبات زمان یادآور با آفست ثابت +۰۳:۳۰ تهران انجام می‌شد،
// درحالی‌که ساعتی که کاربر در دستگاه خودش وارد می‌کند در منطقهٔ زمانیِ خودِ او معنا دارد.
// این تابع آفستِ واقعیِ هر IANA timezone را (با احتساب DST در صورت وجود) محاسبه می‌کند.
function getTzOffsetMinutes(timeZone, dateUTC) {
  const dtf = new Intl.DateTimeFormat('en-US', {
    timeZone, hourCycle: 'h23',
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit', second: '2-digit',
  });
  const parts = dtf.formatToParts(dateUTC).reduce((acc, p) => { acc[p.type] = p.value; return acc; }, {});
  const asUtcOfLocalFields = Date.UTC(
    Number(parts.year), Number(parts.month) - 1, Number(parts.day),
    Number(parts.hour), Number(parts.minute), Number(parts.second)
  );
  return (asUtcOfLocalFields - dateUTC.getTime()) / 60000; // دقیقه، جلوتر از UTC
}

// تبدیل تاریخ/ساعت محلیِ یک منطقهٔ زمانی دلخواه (YYYY-MM-DD, HH:MM) به Unix timestamp UTC
function zonedTimeToUtcTimestamp(dateStr, timeStr = '00:00', timeZone = 'Asia/Tehran') {
  const [y, m, d] = dateStr.split('-').map(Number);
  const [hh, mm] = timeStr.split(':').map(Number);
  const naiveUtcMillis = Date.UTC(y, m - 1, d, hh, mm, 0);
  const offset1 = getTzOffsetMinutes(timeZone, new Date(naiveUtcMillis));
  let utcMillis = naiveUtcMillis - offset1 * 60000;
  const offset2 = getTzOffsetMinutes(timeZone, new Date(utcMillis));
  if (offset2 !== offset1) utcMillis = naiveUtcMillis - offset2 * 60000;
  return Math.floor(utcMillis / 1000);
}

// نگه‌داشته‌شده برای سازگاری: تاریخِ «امروزِ برنامه» و تقویم همیشه بر مبنای تهران هستند
function tehranToUtcTimestamp(dateStr, timeStr = '00:00') {
  return zonedTimeToUtcTimestamp(dateStr, timeStr, 'Asia/Tehran');
}

function todayInTehran() {
  const fmt = new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Tehran',
    year: 'numeric', month: '2-digit', day: '2-digit',
  });
  return fmt.format(new Date()); // YYYY-MM-DD
}

function nowTimeInTehran() {
  const fmt = new Intl.DateTimeFormat('en-GB', {
    timeZone: 'Asia/Tehran',
    hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
  });
  return fmt.format(new Date()); // HH:MM
}

function addDaysToDateStr(dateStr, days) {
  const [y, m, d] = dateStr.split('-').map(Number);
  const dt = new Date(Date.UTC(y, m - 1, d));
  dt.setUTCDate(dt.getUTCDate() + days);
  return dt.toISOString().slice(0, 10);
}

// --- تقویم شمسی (سمت سرور) — برای این‌که بازهٔ ماه/هفته با تقویم جلالی که در رابط
// کاربری نمایش داده می‌شود همخوان باشد (نیازمند پشتیبانی ICU از calendar=persian) ---
const JALALI_MONTH_NAMES_SERVER = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
const WEEKDAY_NAMES_FA_SERVER = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنج‌شنبه','جمعه','شنبه']; // ۰=یکشنبه...۶=شنبه
function jalaliOf(gDateStr) {
  const [y, m, d] = gDateStr.split('-').map(Number);
  const date = new Date(Date.UTC(y, m - 1, d, 12));
  const dtf = new Intl.DateTimeFormat('en-US-u-ca-persian', { year: 'numeric', month: 'numeric', day: 'numeric', timeZone: 'UTC' });
  const parts = dtf.formatToParts(date).reduce((a, p) => { a[p.type] = p.value; return a; }, {});
  return { jy: Number(parts.year), jm: Number(parts.month), jd: Number(parts.day) };
}

function findNowruzGregorian(jy) {
  const approxGy = jy + 621;
  for (let d = 15; d <= 25; d++) {
    const gStr = `${approxGy}-03-${String(d).padStart(2, '0')}`;
    const j = jalaliOf(gStr);
    if (j.jy === jy && j.jm === 1 && j.jd === 1) return gStr;
  }
  return `${approxGy}-03-21`;
}

function jalaliMonthLength(jy, jm) {
  if (jm <= 6) return 31;
  if (jm <= 11) return 30;
  const n1 = findNowruzGregorian(jy), n2 = findNowruzGregorian(jy + 1);
  const [y1, m1, d1] = n1.split('-').map(Number), [y2, m2, d2] = n2.split('-').map(Number);
  return Math.round((Date.UTC(y2, m2 - 1, d2) - Date.UTC(y1, m1 - 1, d1)) / 86400000) - 336;
}

function jalaliMonthFirstDayGregorian(jy, jm) {
  const nowruz = findNowruzGregorian(jy);
  let offset = 0;
  for (let mm = 1; mm < jm; mm++) offset += jalaliMonthLength(jy, mm);
  return addDaysToDateStr(nowruz, offset);
}

function persianDow(gDateStr) {
  const [y, m, d] = gDateStr.split('-').map(Number);
  const gDow = new Date(Date.UTC(y, m - 1, d)).getUTCDay(); // یکشنبه=۰
  return (gDow + 1) % 7; // شنبه=۰ ... جمعه=۶
}

// ============================================================
// 3) TELEGRAM initData VALIDATION (HMAC-SHA256)
// ============================================================
async function verifyTelegramInitData(initData, botToken) {
  if (!initData) return null;
  const params = new URLSearchParams(initData);
  const hash = params.get('hash');
  if (!hash) return null;
  params.delete('hash');

  const pairs = [];
  for (const [key, value] of params.entries()) pairs.push(`${key}=${value}`);
  pairs.sort();
  const dataCheckString = pairs.join('\n');

  const encoder = new TextEncoder();
  const secretKeyMaterial = await crypto.subtle.importKey(
    'raw', encoder.encode('WebAppData'), { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']
  );
  // مرحله ۱: secret_key = HMAC_SHA256("WebAppData", bot_token)
  const secretKeyBuffer = await crypto.subtle.sign('HMAC', secretKeyMaterial, encoder.encode(botToken));

  const hmacKey = await crypto.subtle.importKey(
    'raw', secretKeyBuffer, { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']
  );
  const signature = await crypto.subtle.sign('HMAC', hmacKey, encoder.encode(dataCheckString));
  const computedHash = [...new Uint8Array(signature)].map(b => b.toString(16).padStart(2, '0')).join('');

  if (computedHash !== hash) return null;

  // بررسی انقضا (۲۴ ساعت)
  const authDate = Number(params.get('auth_date') || 0);
  if (nowTs() - authDate > 86400) return null;

  const userStr = params.get('user');
  if (!userStr) return null;
  try {
    return JSON.parse(userStr);
  } catch {
    return null;
  }
}

// ============================================================
// 4) TELEGRAM BOT SERVICE
// ============================================================
function telegramApi(botToken, method) {
  return `https://api.telegram.org/bot${botToken}/${method}`;
}

const TELEGRAM_MAX_MESSAGE_LENGTH = 4096;

// تقسیم امن یک متن طولانی به چند پیام، مطابق محدودیت ۴۰۹۶ کاراکتریِ تلگرام.
// اولویت شکستن: پایان پاراگراف > پایان جمله > نزدیک‌ترین فاصلهٔ خالی — و هرگز وسط
// یک تگ HTML باز (که با parse_mode: 'HTML' استفاده می‌شود) بریده نمی‌شود.
function splitMessageForTelegram(text, maxLen = TELEGRAM_MAX_MESSAGE_LENGTH) {
  if (!text) return [''];
  if (text.length <= maxLen) return [text];

  const chunks = [];
  let remaining = text;
  while (remaining.length > maxLen) {
    let cut = maxLen;

    // ۱) ترجیح اول: پایان یک پاراگراف (خط خالی)
    const paragraphIdx = remaining.lastIndexOf('\n\n', maxLen);
    if (paragraphIdx > maxLen * 0.4) {
      cut = paragraphIdx + 2;
    } else {
      // ۲) ترجیح دوم: پایان یک جمله (نقطه/علامت سؤال/تعجب و بعدش فاصله یا خط جدید)
      const window = remaining.slice(0, maxLen);
      const sentenceEnds = [...window.matchAll(/[.!?؟۔](?=\s|\n|$)/g)];
      const lastSentence = sentenceEnds.length ? sentenceEnds[sentenceEnds.length - 1] : null;
      if (lastSentence && lastSentence.index > maxLen * 0.4) {
        cut = lastSentence.index + 1;
      } else {
        // ۳) آخرین راه‌حل: نزدیک‌ترین فاصلهٔ خالی، تا وسط یک کلمه بریده نشود
        const spaceIdx = remaining.lastIndexOf(' ', maxLen);
        cut = spaceIdx > maxLen * 0.4 ? spaceIdx + 1 : maxLen;
      }
    }

    // اطمینان از این‌که برش وسط یک تگ HTML باز (مثل <b> نصفه) نیفتد
    const openIdx = remaining.lastIndexOf('<', cut);
    const closeIdx = remaining.lastIndexOf('>', cut);
    if (openIdx > closeIdx) cut = openIdx;

    if (cut <= 0) cut = maxLen; // ایمنی در برابر حلقهٔ بی‌نهایت در ورودی‌های غیرعادی

    chunks.push(remaining.slice(0, cut).trim());
    remaining = remaining.slice(cut).trim();
  }
  if (remaining) chunks.push(remaining);
  return chunks;
}

// ارسال پیام به تلگرام — اگر متن از محدودیت طول تلگرام بیشتر باشد، خودکار به چند
// پیام متوالی (با حفظ ترتیب و بدون شکستن جمله/تگ) تقسیم می‌شود. reply_markup و سایر
// گزینه‌های extra فقط روی آخرین بخش اعمال می‌شوند تا دکمه‌ها تکراری نمایش داده نشوند.
async function tgSendMessage(env, chatId, text, extra = {}) {
  const parts = splitMessageForTelegram(text);
  let lastRes;
  for (let i = 0; i < parts.length; i++) {
    const isLast = i === parts.length - 1;
    lastRes = await fetch(telegramApi(env.BOT_TOKEN, 'sendMessage'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, text: parts[i], parse_mode: 'HTML', ...(isLast ? extra : {}) }),
    });
  }
  return lastRes;
}

function miniAppButton(env) {
  return {
    reply_markup: {
      inline_keyboard: [[{ text: '🚀 باز کردن ToDoList', web_app: { url: env.MINI_APP_URL } }]],
    },
  };
}

// برای بخش «حمایت از ربات» — نام کاربری ربات برای ساخت لینک اشتراک‌گذاری لازم است.
// اگر env.BOT_USERNAME تنظیم شده باشد از همان استفاده می‌شود (سریع‌تر، بدون تماس اضافه با API)،
// در غیر این صورت یک‌بار از خودِ Telegram (getMe) پرسیده می‌شود.
async function getBotUsername(env) {
  if (env.BOT_USERNAME) return env.BOT_USERNAME.replace(/^@/, '');
  const res = await fetch(telegramApi(env.BOT_TOKEN, 'getMe'));
  const data = await res.json();
  if (data.ok && data.result && data.result.username) return data.result.username;
  throw new Error('نام کاربری ربات قابل تشخیص نیست');
}

function base64ToUint8Array(base64) {
  const binaryStr = atob(base64);
  const bytes = new Uint8Array(binaryStr.length);
  for (let i = 0; i < binaryStr.length; i++) bytes[i] = binaryStr.charCodeAt(i);
  return bytes;
}

// تصویر بنر «حمایت از ربات» (لوگوی ToDoList، فشرده‌شده به JPEG ~۴۰ کیلوبایت) —
// به‌صورت base64 مستقیم در کد جاسازی شده تا نیازی به هیچ سرویس ذخیره‌سازی خارجی
// (مثل Cloudflare R2 که فعال‌سازی‌اش نیاز به کارت بانکی بین‌المللی دارد) نباشد.
// هر بار که کاربر بخواهد بنر را دریافت کند، فقط همین‌جا دیکود و به تلگرام آپلود می‌شود.
const SUPPORT_BANNER_IMAGE_BASE64 = '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';

const SUPPORT_BANNER_CAPTION = `<b>🎯 ToDoList — دستیار هوشمند مدیریت زندگی روزمره‌ات</b>

با ToDoList دیگه هیچ کاری از قلم نمی‌افته:

✅ مدیریت کارها و اولویت‌بندی هوشمند
📅 تقویم شمسی و میلادی یکپارچه
🔥 پیگیری عادت‌ها با شمارش Streak
📝 یادداشت‌ها همراه با فایل و تصویر
🔔 اعلان‌های دقیق و به‌موقع، حتی برای کارهای تکرارشونده
🤖 دستیار هوش مصنوعی برای برنامه‌ریزی روزانه
🎯 مدیریت اهداف و برنامه‌ریزی بلندمدت

رابط کاربری ساده و سریع، کاملاً رایگان — مستقیم داخل تلگرام، بدون نیاز به نصب هیچ اپ اضافه‌ای 🚀`;

// ارسال بنر تبلیغاتی (تصویر + کپشن + دکمهٔ شیشه‌ای) به چتِ خودِ کاربر با ربات.
// از دکمهٔ نوع url (نه web_app) استفاده می‌شود چون این نوع دکمه، برخلاف web_app،
// تضمین‌شده روی پیام‌های Forward‌شده هم برای هر کاربری کار می‌کند — و چون به لینک
// t.me/<bot>‎ اشاره دارد، با یک لمس مستقیم کاربر را وارد چتِ ربات می‌کند.
async function sendSupportBanner(env, user) {
  const botUsername = await getBotUsername(env);
  const imageBytes = base64ToUint8Array(SUPPORT_BANNER_IMAGE_BASE64);
  const form = new FormData();
  form.append('chat_id', String(user.telegram_id));
  form.append('photo', new Blob([imageBytes], { type: 'image/jpeg' }), 'todolist-banner.jpg');
  form.append('caption', SUPPORT_BANNER_CAPTION);
  form.append('parse_mode', 'HTML');
  form.append('reply_markup', JSON.stringify({
    inline_keyboard: [[{ text: '🚀 شروع استفاده از ربات', url: `https://t.me/${botUsername}?start=share` }]],
  }));
  const res = await fetch(telegramApi(env.BOT_TOKEN, 'sendPhoto'), { method: 'POST', body: form });
  const data = await res.json();
  if (!data.ok) throw new Error('ارسال بنر ناموفق بود: ' + (data.description || ''));
  return data.result;
}

// ============================================================
// 5) DATA ACCESS LAYER (Repository-style functions over D1)
// ============================================================

async function ensureUser(env, tgUser) {
  const existing = await env.DB.prepare('SELECT * FROM users WHERE telegram_id = ?')
    .bind(tgUser.id).first();
  if (existing) return existing;

  const ts = nowTs();
  await env.DB.prepare(
    `INSERT INTO users (telegram_id, first_name, username, language_code, theme, timezone, created_at, updated_at)
     VALUES (?, ?, ?, ?, 'light', 'Asia/Tehran', ?, ?)`
  ).bind(tgUser.id, tgUser.first_name || '', tgUser.username || '', tgUser.language_code || 'fa', ts, ts).run();

  const user = await env.DB.prepare('SELECT * FROM users WHERE telegram_id = ?').bind(tgUser.id).first();

  // ایجاد دسته‌بندی‌های پیش‌فرض برای کاربر جدید
  const stmts = DEFAULT_CATEGORIES.map(c =>
    env.DB.prepare(
      `INSERT INTO categories (user_id, name, icon, color, is_default, created_at) VALUES (?, ?, ?, ?, 1, ?)`
    ).bind(user.id, c.name, c.icon, c.color, ts)
  );
  await env.DB.batch(stmts);

  return user;
}

// ---- Categories ----
async function listCategories(env, userId) {
  const { results } = await env.DB.prepare(
    'SELECT * FROM categories WHERE user_id = ? ORDER BY is_default DESC, id ASC'
  ).bind(userId).all();
  return results;
}

async function createCategory(env, userId, { name, icon, color }) {
  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO categories (user_id, name, icon, color, is_default, created_at) VALUES (?, ?, ?, ?, 0, ?)`
  ).bind(userId, name, icon || '📌', color || '#6C5CE7', ts).run();
  return env.DB.prepare('SELECT * FROM categories WHERE id = ?').bind(res.meta.last_row_id).first();
}

// ---- Tasks ----
async function listTasks(env, userId, filters = {}) {
  let query = 'SELECT * FROM tasks WHERE user_id = ? AND deleted_at IS NULL';
  const binds = [userId];
  if (filters.date) { query += ' AND task_date = ?'; binds.push(filters.date); }
  if (filters.from && filters.to) { query += ' AND task_date BETWEEN ? AND ?'; binds.push(filters.from, filters.to); }
  if (filters.category) { query += ' AND category_id = ?'; binds.push(filters.category); }
  if (filters.status === 'done') query += ' AND is_completed = 1';
  if (filters.status === 'pending') query += ' AND is_completed = 0';
  query += ' ORDER BY task_date ASC, task_time ASC';
  const { results } = await env.DB.prepare(query).bind(...binds).all();
  return results;
}

async function getTask(env, userId, taskId) {
  return env.DB.prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ? AND deleted_at IS NULL')
    .bind(taskId, userId).first();
}

// روزِ هفته (۰=یکشنبه ... ۶=شنبه) برای یک تاریخ میلادیِ ساده — مستقل از منطقهٔ زمانی،
// چون خودِ رشتهٔ تاریخ (YYYY-MM-DD) قبلاً بر مبنای تایم‌زون کاربر محاسبه شده
function weekdayOfDateStr(dateStr) {
  const [y, m, d] = dateStr.split('-').map(Number);
  return new Date(Date.UTC(y, m - 1, d)).getUTCDay(); // ۰=یکشنبه ... ۶=شنبه
}

function todayInTimezone(timeZone) {
  const fmt = new Intl.DateTimeFormat('en-CA', { timeZone, year: 'numeric', month: '2-digit', day: '2-digit' });
  return fmt.format(new Date()); // YYYY-MM-DD
}

function parseRecurrenceDays(raw) {
  if (!raw) return [];
  try {
    const arr = JSON.parse(raw);
    return Array.isArray(arr) ? arr.filter((d) => Number.isInteger(d) && d >= 0 && d <= 6) : [];
  } catch { return []; }
}

// طول پنجرهٔ نمونه‌های آیندهٔ از پیش‌ساخته‌شده برای هر کار تکرارشونده — هم در لحظهٔ
// ساخت و هم هر روز در cron تمدید می‌شود، تا کاربر همیشه حداقل تا این‌قدر روز جلوتر
// نمونه‌های آینده را در تقویم/لیست کارها ببیند
const RECURRING_WINDOW_DAYS = 60;

// --- ریشهٔ باگ قبلی «تکرار هفتگی ذخیره/نمایش داده نمی‌شود» ---
// در نسخهٔ قبلی، وقتی کاربر تکرار را فعال می‌کرد، به‌جای یک کارِ معمولی، یک ردیفِ
// «قالب» مخفی (is_template=1) ساخته می‌شد که هرگز در لیست/تقویم نمایش داده نمی‌شد؛
// و تنها اگر «امروز» جزو روزهای انتخابی بود، یک نمونه برای همان روز ساخته می‌شد.
// نتیجه: اگر امروز جزو روزهای انتخابی نبود، پس از ذخیره هیچ‌چیزی در هیچ‌جا دیده
// نمی‌شد (به‌نظر می‌رسید ذخیره نشده)، و چون فرم ویرایش هرگز روزهای انتخابی را دوباره
// نشان نمی‌داد، با بازکردن مجدد کار هم اطلاعات تکرار انگار «گم شده» بود.
// راه‌حل: کارِ اصلی (master) از همان ابتدا یک ردیفِ کاملاً معمولی و همیشه-قابل‌مشاهده
// است؛ و بلافاصله هنگام ساخت، نمونه‌های ۶۰ روز آیندهٔ منطبق با الگو نیز از پیش ساخته
// می‌شوند (نه فقط امروز) تا نتیجه بلافاصله در تقویم دیده شود. هر نمونه هم فیلدهای
// recurrence_type/recurrence_days خودش را دارد تا با بازکردن هر نمونه (نه فقط ماستر)،
// روزهای انتخاب‌شده دوباره درست نمایش داده شوند.
async function createTask(env, user, data) {
  const ts = nowTs();
  const recurrenceDays = Array.isArray(data.recurrence_days)
    ? [...new Set(data.recurrence_days)].filter((d) => Number.isInteger(d) && d >= 0 && d <= 6).sort()
    : [];
  const isRecurring = recurrenceDays.length > 0;
  const reminderOffset = data.reminder_enabled ? Number(data.reminder_offset_minutes ?? 0) : null;

  const res = await env.DB.prepare(
    `INSERT INTO tasks (user_id, category_id, title, description, task_date, task_time, priority, color, icon,
                         recurrence_type, recurrence_days, reminder_offset_minutes, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).bind(
    user.id, data.category_id || null, data.title, data.description || '',
    data.task_date, data.task_time || null, data.priority || 'normal', data.color || '#6C5CE7', data.icon || '📝',
    isRecurring ? 'weekly' : null,
    isRecurring ? JSON.stringify(recurrenceDays) : null,
    reminderOffset, ts, ts
  ).run();

  const created = await getTask(env, user.id, res.meta.last_row_id);

  // یادآوری (چه برای کار معمولی، چه برای همین نمونهٔ اولِ سریِ تکرارشونده)
  if (reminderOffset !== null && created.task_date) {
    await createReminder(env, user, created, { offset_minutes: reminderOffset, repeat_type: 'none' }).catch(() => null);
  }

  if (isRecurring) {
    await generateFutureInstances(env, user, created, recurrenceDays, RECURRING_WINDOW_DAYS);
  }
  return created;
}

// ساخت نمونه‌های آیندهٔ یک سریِ تکرارشونده، از فردای تاریخ master تا windowDays روز
// جلوتر، برای هر روزی که در recurrenceDays باشد. idempotent است (createRecurringInstance
// خودش از تکراری‌شدن جلوگیری می‌کند).
async function generateFutureInstances(env, user, master, recurrenceDays, windowDays) {
  for (let offset = 1; offset <= windowDays; offset++) {
    const dateStr = addDaysToDateStr(master.task_date, offset);
    if (recurrenceDays.includes(weekdayOfDateStr(dateStr))) {
      await createRecurringInstance(env, user, master, dateStr).catch(() => null);
    }
  }
}

// ساخت نمونهٔ واقعیِ یک روزِ خاص از روی master یک سریِ تکرارشونده — idempotent
// (اگر نمونهٔ آن روز از قبل ساخته شده باشد، دوباره ساخته نمی‌شود؛ همان را برمی‌گرداند).
// template_id همیشه به ریشهٔ سری اشاره می‌کند (نه به یک نمونهٔ میانی) تا زنجیره تخت بماند.
async function createRecurringInstance(env, user, master, dateStr) {
  const rootId = master.template_id || master.id;
  const existing = await env.DB.prepare(
    'SELECT * FROM tasks WHERE (id = ? OR template_id = ?) AND task_date = ? AND deleted_at IS NULL'
  ).bind(rootId, rootId, dateStr).first();
  if (existing) return existing;

  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO tasks (user_id, category_id, title, description, task_date, task_time, priority, color, icon,
                         recurrence_type, recurrence_days, template_id, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).bind(
    user.id, master.category_id, master.title, master.description, dateStr, master.task_time,
    master.priority, master.color, master.icon, master.recurrence_type, master.recurrence_days,
    rootId, ts, ts
  ).run();

  const instance = await getTask(env, user.id, res.meta.last_row_id);
  if (master.reminder_offset_minutes !== null && master.reminder_offset_minutes !== undefined) {
    await createReminder(env, user, instance, { offset_minutes: master.reminder_offset_minutes, repeat_type: 'none' }).catch(() => null);
  }
  return instance;
}

// اجرای روزانه (از دلِ scheduled/cron)، برای همهٔ سری‌های تکرارشونده:
// ۱) شبکهٔ ایمنی: اگر به هر دلیلی نمونهٔ «امروز» ساخته نشده بود، همین الان بسازد.
// ۲) تمدید پنجره: نمونهٔ روزِ «امروز + RECURRING_WINDOW_DAYS» را (اگر با الگو منطبق
//    باشد) می‌سازد تا پنجرهٔ نمونه‌های آینده همیشه ~۶۰ روز جلوتر را پوشش دهد.
// چون idempotent است، اجرای چندبارهٔ آن در یک روز هرگز وظیفهٔ تکراری ایجاد نمی‌کند.
async function generateRecurringTaskInstances(env) {
  const { results: masters } = await env.DB.prepare(
    `SELECT t.*, u.timezone AS user_timezone, u.id AS owner_id
     FROM tasks t JOIN users u ON u.id = t.user_id
     WHERE t.recurrence_type = 'weekly' AND t.template_id IS NULL AND t.deleted_at IS NULL`
  ).all();

  for (const master of (masters || [])) {
    const days = parseRecurrenceDays(master.recurrence_days);
    if (days.length === 0) continue;
    const user = { id: master.owner_id, timezone: master.user_timezone };
    const todayStr = todayInTimezone(master.user_timezone || 'Asia/Tehran');

    if (days.includes(weekdayOfDateStr(todayStr))) {
      await createRecurringInstance(env, user, master, todayStr).catch(() => null);
    }
    const farDateStr = addDaysToDateStr(todayStr, RECURRING_WINDOW_DAYS);
    if (days.includes(weekdayOfDateStr(farDateStr))) {
      await createRecurringInstance(env, user, master, farDateStr).catch(() => null);
    }
  }
}

async function updateTask(env, userId, taskId, data) {
  const task = await getTask(env, userId, taskId);
  if (!task) return null;
  const fields = ['category_id', 'title', 'description', 'task_date', 'task_time', 'priority', 'color', 'icon'];
  const sets = [];
  const binds = [];
  for (const f of fields) {
    if (data[f] !== undefined) { sets.push(`${f} = ?`); binds.push(data[f]); }
  }
  if (sets.length > 0) {
    sets.push('updated_at = ?'); binds.push(nowTs());
    binds.push(taskId, userId);
    await env.DB.prepare(`UPDATE tasks SET ${sets.join(', ')} WHERE id = ? AND user_id = ?`).bind(...binds).run();
  }

  // تغییر الگوی تکرار از فرم ویرایش (اگر فیلد ارسال شده باشد) — جداگانه مدیریت می‌شود
  // چون نیاز به هماهنگ‌سازی با کل سری (نمونه‌های آینده) دارد. اگر الگوی جدید دقیقاً با
  // الگوی فعلی یکسان باشد، هیچ کاری انجام نمی‌شود (از پردازش/نوشتن بی‌مورد در هر ویرایش
  // ساده جلوگیری می‌کند، چون این فیلد حالا همیشه از فرم ارسال می‌شود).
  if (data.recurrence_days !== undefined) {
    const newDays = [...new Set(Array.isArray(data.recurrence_days) ? data.recurrence_days : [])]
      .filter((d) => Number.isInteger(d) && d >= 0 && d <= 6).sort();
    const currentDays = parseRecurrenceDays(task.recurrence_days);
    const unchanged = newDays.length === currentDays.length && newDays.every((d, i) => d === currentDays[i]);
    if (!unchanged) {
      await updateRecurrencePattern(env, userId, taskId, newDays);
    }
  }

  return getTask(env, userId, taskId);
}

// تغییر الگوی تکرار یک سری از طریق ویرایشِ هر یک از نمونه‌های آن (یا خودِ ماستر):
// ریشهٔ سری و همهٔ نمونه‌های آینده را با الگوی جدید هماهنگ می‌کند — نمونه‌های آینده‌ای
// که دیگر با الگوی جدید نمی‌خوانند حذف (soft-delete) می‌شوند، و نمونه‌های تازه برای
// روزهای تازه‌انتخاب‌شده ساخته می‌شوند. نمونه‌های گذشته (تاریخ‌گذشته) هرگز دست نمی‌خورند.
async function updateRecurrencePattern(env, userId, taskId, recurrenceDaysRaw) {
  const task = await getTask(env, userId, taskId);
  if (!task) return;
  const rootId = task.template_id || task.id;
  const root = task.template_id ? await getTask(env, userId, rootId) : task;
  if (!root) return;

  const recurrenceDays = [...new Set(recurrenceDaysRaw)].filter((d) => Number.isInteger(d) && d >= 0 && d <= 6).sort();
  const isRecurring = recurrenceDays.length > 0;
  const newType = isRecurring ? 'weekly' : null;
  const newDaysJson = isRecurring ? JSON.stringify(recurrenceDays) : null;

  await env.DB.prepare(
    `UPDATE tasks SET recurrence_type = ?, recurrence_days = ?, updated_at = ?
     WHERE user_id = ? AND (id = ? OR template_id = ?) AND deleted_at IS NULL`
  ).bind(newType, newDaysJson, nowTs(), userId, rootId, rootId).run();

  if (!isRecurring) return; // کاربر تکرار را کاملاً خاموش کرد؛ نمونه‌های آیندهٔ ازقبل‌ساخته‌شده دست‌نخورده باقی می‌مانند اما دیگر تمدید نمی‌شوند

  const user = await env.DB.prepare('SELECT id, timezone FROM users WHERE id = ?').bind(userId).first();
  if (!user) return;
  const todayStr = todayInTimezone(user.timezone || 'Asia/Tehran');

  const { results: futureInstances } = await env.DB.prepare(
    'SELECT * FROM tasks WHERE template_id = ? AND task_date > ? AND deleted_at IS NULL'
  ).bind(rootId, todayStr).all();
  for (const inst of (futureInstances || [])) {
    if (!recurrenceDays.includes(weekdayOfDateStr(inst.task_date))) {
      await env.DB.prepare('UPDATE tasks SET deleted_at = ? WHERE id = ?').bind(nowTs(), inst.id).run();
    }
  }

  const updatedRoot = await getTask(env, userId, rootId);
  await generateFutureInstances(env, user, updatedRoot, recurrenceDays, RECURRING_WINDOW_DAYS);
}

async function toggleTask(env, userId, taskId) {
  const task = await getTask(env, userId, taskId);
  if (!task) return null;
  const newState = task.is_completed ? 0 : 1;
  const completedAt = newState ? nowTs() : null;
  await env.DB.prepare('UPDATE tasks SET is_completed = ?, completed_at = ?, updated_at = ? WHERE id = ? AND user_id = ?')
    .bind(newState, completedAt, nowTs(), taskId, userId).run();
  return getTask(env, userId, taskId);
}

async function deleteTask(env, userId, taskId) {
  await env.DB.prepare('UPDATE tasks SET deleted_at = ? WHERE id = ? AND user_id = ?')
    .bind(nowTs(), taskId, userId).run();
  return true;
}

// ---- Reminders ----
function computeTriggerAt(task, offsetMinutes, timeZone = 'Asia/Tehran') {
  const base = zonedTimeToUtcTimestamp(task.task_date, task.task_time || '09:00', timeZone);
  return base + offsetMinutes * 60;
}

async function createReminder(env, user, task, { offset_minutes = 0, repeat_type = 'none' }) {
  const ts = nowTs();
  const timeZone = user.timezone || 'Asia/Tehran';
  const triggerAt = computeTriggerAt(task, offset_minutes, timeZone);
  const res = await env.DB.prepare(
    `INSERT INTO reminders (task_id, user_id, offset_minutes, repeat_type, is_active, trigger_at, created_at)
     VALUES (?, ?, ?, ?, 1, ?, ?)`
  ).bind(task.id, user.id, offset_minutes, repeat_type, triggerAt, ts).run();
  return env.DB.prepare('SELECT * FROM reminders WHERE id = ?').bind(res.meta.last_row_id).first();
}

async function listRemindersForTask(env, userId, taskId) {
  const { results } = await env.DB.prepare(
    'SELECT * FROM reminders WHERE task_id = ? AND user_id = ? ORDER BY trigger_at ASC'
  ).bind(taskId, userId).all();
  return results;
}

async function toggleReminder(env, userId, reminderId) {
  const rem = await env.DB.prepare('SELECT * FROM reminders WHERE id = ? AND user_id = ?').bind(reminderId, userId).first();
  if (!rem) return null;
  const newState = rem.is_active ? 0 : 1;
  await env.DB.prepare('UPDATE reminders SET is_active = ? WHERE id = ? AND user_id = ?')
    .bind(newState, reminderId, userId).run();
  return env.DB.prepare('SELECT * FROM reminders WHERE id = ?').bind(reminderId).first();
}

async function deleteReminder(env, userId, reminderId) {
  await env.DB.prepare('DELETE FROM reminders WHERE id = ? AND user_id = ?').bind(reminderId, userId).run();
  return true;
}

// ---- Habits ----
async function listHabits(env, userId) {
  const { results } = await env.DB.prepare(
    'SELECT * FROM habits WHERE user_id = ? AND is_active = 1 AND deleted_at IS NULL ORDER BY id DESC'
  ).bind(userId).all();

  const habits = [];
  for (const h of results) {
    habits.push(await attachHabitStats(env, h));
  }
  return habits;
}

async function attachHabitStats(env, habit) {
  const { results: logs } = await env.DB.prepare(
    'SELECT log_date FROM habit_logs WHERE habit_id = ? ORDER BY log_date DESC'
  ).bind(habit.id).all();

  const logDates = new Set(logs.map(l => l.log_date));
  const today = todayInTehran();

  // محاسبه streak فعلی (رو به عقب از امروز یا دیروز)
  let streak = 0;
  let cursor = logDates.has(today) ? today : addDaysToDateStr(today, -1);
  while (logDates.has(cursor)) {
    streak++;
    cursor = addDaysToDateStr(cursor, -1);
  }

  // درصد موفقیت از تاریخ شروع
  const startDate = habit.start_date;
  const daysSinceStart = Math.max(1, Math.floor(
    (tehranToUtcTimestamp(today) - tehranToUtcTimestamp(startDate)) / 86400
  ) + 1);
  const successRate = Math.min(100, Math.round((logDates.size / daysSinceStart) * 100));

  let badge = null;
  for (const m of STREAK_MILESTONES) if (streak >= m) badge = m;

  return {
    ...habit,
    streak,
    success_rate: successRate,
    done_today: logDates.has(today),
    total_logs: logDates.size,
    badge,
    recent_dates: [...logDates].slice(0, 35),
  };
}

// آمار کامل‌تر برای نمایشِ پیشرفتِ یک عادت (فراخوانی‌شده فقط هنگام باز کردن جزئیات
// یک عادت خاص، نه در لیست — تا لیست سبک و سریع بماند). به‌جای فقط یک نمودار خطی
// (که برای دادهٔ دوتایی «انجام‌شد/نشد» جذابیت بصری کمی دارد)، ترکیبی از:
// Streak فعلی، بهترین رکورد تاریخی، درصد موفقیت، Heatmap ۹۱ روزهٔ اخیر (شبیهِ
// GitHub contributions) و نشان‌های افتخار قفل/بازشده برگردانده می‌شود.
async function getHabitProgress(env, userId, habitId) {
  const habit = await env.DB.prepare('SELECT * FROM habits WHERE id = ? AND user_id = ? AND deleted_at IS NULL')
    .bind(habitId, userId).first();
  if (!habit) return null;

  const { results: logs } = await env.DB.prepare(
    'SELECT log_date FROM habit_logs WHERE habit_id = ? ORDER BY log_date ASC'
  ).bind(habitId).all();
  const logDates = new Set(logs.map((l) => l.log_date));
  const today = todayInTehran();

  // Streak فعلی (رو به عقب از امروز یا دیروز)
  let streak = 0;
  let cursor = logDates.has(today) ? today : addDaysToDateStr(today, -1);
  while (logDates.has(cursor)) { streak++; cursor = addDaysToDateStr(cursor, -1); }

  // بهترین رکورد تاریخی: با یک عبور روی تاریخ‌های مرتب‌شده، طولانی‌ترین دنبالهٔ پیوسته
  let bestStreak = 0, runLength = 0, prevDate = null;
  const sortedDates = [...logDates].sort();
  for (const d of sortedDates) {
    runLength = (prevDate && addDaysToDateStr(prevDate, 1) === d) ? runLength + 1 : 1;
    bestStreak = Math.max(bestStreak, runLength);
    prevDate = d;
  }
  bestStreak = Math.max(bestStreak, streak);

  const daysSinceStart = Math.max(1, Math.floor(
    (tehranToUtcTimestamp(today) - tehranToUtcTimestamp(habit.start_date)) / 86400
  ) + 1);
  const successRate = Math.min(100, Math.round((logDates.size / daysSinceStart) * 100));

  // Heatmap ۹۱ روزهٔ اخیر (۱۳ هفته × ۷ روز)
  const HEATMAP_DAYS = 91;
  const heatmap = [];
  for (let i = HEATMAP_DAYS - 1; i >= 0; i--) {
    const d = addDaysToDateStr(today, -i);
    heatmap.push({ date: d, done: logDates.has(d) });
  }

  const achievements = STREAK_MILESTONES.map((m) => ({ milestone: m, unlocked: bestStreak >= m }));

  return {
    ...habit,
    streak,
    best_streak: bestStreak,
    success_rate: successRate,
    total_logs: logDates.size,
    done_today: logDates.has(today),
    heatmap,
    achievements,
  };
}

async function createHabit(env, userId, data) {
  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO habits (user_id, title, description, icon, color, daily_goal, start_date, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
  ).bind(
    userId, data.title, data.description || '', data.icon || '🔥', data.color || '#00B894',
    data.daily_goal || 1, data.start_date || todayInTehran(), ts
  ).run();
  const habit = await env.DB.prepare('SELECT * FROM habits WHERE id = ?').bind(res.meta.last_row_id).first();
  return attachHabitStats(env, habit);
}

async function updateHabit(env, userId, habitId, data) {
  const habit = await env.DB.prepare('SELECT * FROM habits WHERE id = ? AND user_id = ? AND deleted_at IS NULL')
    .bind(habitId, userId).first();
  if (!habit) return null;
  const fields = ['title', 'description', 'icon', 'color', 'daily_goal'];
  const sets = []; const binds = [];
  for (const f of fields) if (data[f] !== undefined) { sets.push(`${f} = ?`); binds.push(data[f]); }
  if (sets.length > 0) {
    binds.push(habitId, userId);
    await env.DB.prepare(`UPDATE habits SET ${sets.join(', ')} WHERE id = ? AND user_id = ?`).bind(...binds).run();
  }
  const updated = await env.DB.prepare('SELECT * FROM habits WHERE id = ?').bind(habitId).first();
  return attachHabitStats(env, updated);
}

async function logHabitToday(env, userId, habitId) {
  const habit = await env.DB.prepare('SELECT * FROM habits WHERE id = ? AND user_id = ?').bind(habitId, userId).first();
  if (!habit) return null;
  const today = todayInTehran();
  const ts = nowTs();
  await env.DB.prepare(
    `INSERT INTO habit_logs (habit_id, log_date, count, created_at) VALUES (?, ?, 1, ?)
     ON CONFLICT(habit_id, log_date) DO UPDATE SET count = count + 1`
  ).bind(habitId, today, ts).run();

  const updated = await attachHabitStats(env, habit);

  // بررسی Badge جدید
  if (updated.badge && STREAK_MILESTONES.includes(updated.streak)) {
    const already = await env.DB.prepare(
      'SELECT id FROM badges WHERE user_id = ? AND habit_id = ? AND badge_type = ?'
    ).bind(userId, habitId, `streak_${updated.streak}`).first();
    if (!already) {
      await env.DB.prepare(
        'INSERT INTO badges (user_id, habit_id, badge_type, earned_at) VALUES (?, ?, ?, ?)'
      ).bind(userId, habitId, `streak_${updated.streak}`, ts).run();
    }
  }
  return updated;
}

async function deleteHabit(env, userId, habitId) {
  await env.DB.prepare('UPDATE habits SET deleted_at = ?, is_active = 0 WHERE id = ? AND user_id = ?')
    .bind(nowTs(), habitId, userId).run();
  return true;
}

// ---- Notes ----
async function listNotes(env, userId, { search, pinned } = {}) {
  let query = `SELECT n.*, (SELECT COUNT(*) FROM note_attachments a WHERE a.note_id = n.id) AS attachment_count
               FROM notes n WHERE n.user_id = ? AND n.deleted_at IS NULL`;
  const binds = [userId];
  if (search) { query += ' AND (n.title LIKE ? OR n.content LIKE ?)'; binds.push(`%${search}%`, `%${search}%`); }
  if (pinned === 'true') query += ' AND n.is_pinned = 1';
  query += ' ORDER BY n.is_pinned DESC, n.updated_at DESC';
  const { results } = await env.DB.prepare(query).bind(...binds).all();
  return results;
}

async function getNote(env, userId, noteId) {
  const note = await env.DB.prepare('SELECT * FROM notes WHERE id = ? AND user_id = ? AND deleted_at IS NULL')
    .bind(noteId, userId).first();
  if (!note) return null;
  note.attachments = await listAttachments(env, userId, noteId);
  return note;
}

async function createNote(env, userId, data) {
  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO notes (user_id, category_id, title, content, color, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`
  ).bind(userId, data.category_id || null, data.title || '', data.content, data.color || '#FFEAA7', ts, ts).run();
  return env.DB.prepare('SELECT * FROM notes WHERE id = ?').bind(res.meta.last_row_id).first();
}

async function updateNote(env, userId, noteId, data) {
  const fields = ['title', 'content', 'color', 'category_id', 'is_pinned'];
  const sets = []; const binds = [];
  for (const f of fields) if (data[f] !== undefined) { sets.push(`${f} = ?`); binds.push(data[f]); }
  if (sets.length === 0) return env.DB.prepare('SELECT * FROM notes WHERE id = ? AND user_id = ?').bind(noteId, userId).first();
  sets.push('updated_at = ?'); binds.push(nowTs());
  binds.push(noteId, userId);
  await env.DB.prepare(`UPDATE notes SET ${sets.join(', ')} WHERE id = ? AND user_id = ?`).bind(...binds).run();
  return env.DB.prepare('SELECT * FROM notes WHERE id = ? AND user_id = ?').bind(noteId, userId).first();
}

async function deleteNote(env, userId, noteId) {
  // پاک‌سازیِ کامل فایل‌های پیوست (هم پیام تلگرام هم رکورد دیتابیس) هنگام حذف یادداشت
  const attachmentRows = await env.DB.prepare('SELECT id FROM note_attachments WHERE note_id = ? AND user_id = ?')
    .bind(noteId, userId).all();
  const attachmentIds = (attachmentRows.results || []).map((a) => a.id);
  if (attachmentIds.length > 0) {
    await Promise.all(attachmentIds.map((attId) => deleteAttachment(env, userId, attId).catch(() => null)));
  }
  await env.DB.prepare('UPDATE notes SET deleted_at = ? WHERE id = ? AND user_id = ?').bind(nowTs(), noteId, userId).run();
  return true;
}

// ============================================================
// 5.6) پیوست‌های یادداشت (فایل/تصویر) — ذخیره‌سازی روی خودِ Telegram
// به‌جای Cloudflare R2 (که فعال‌سازی‌اش نیاز به کارت بانکی بین‌المللی دارد و برای
// کاربران داخل ایران معمولاً در دسترس نیست)، فایل‌ها با همان Bot API موجود به چتِ
// خودِ کاربر با ربات ارسال می‌شوند و فقط شناسهٔ فایل (file_id) در D1 نگه‌داری می‌شود؛
// دانلود هم با getFile و دریافت مستقیم بایت‌ها از سرورهای تلگرام انجام می‌شود.
// این روش هیچ هزینه یا نیاز به کارت بانکی ندارد.
// ============================================================
const ATTACHMENT_MAX_BYTES = 20 * 1024 * 1024; // محدودیت آپلود مستقیم Bot API برای اسناد ~۵۰ مگابایت است؛ برای اطمینان محافظه‌کارانه‌تر ۲۰ مگابایت گذاشته شده

function sanitizeFileName(name) {
  return (name || 'file').replace(/[^a-zA-Z0-9آ-ی._-]/g, '_').slice(0, 100);
}

async function listAttachments(env, userId, noteId) {
  const { results } = await env.DB.prepare(
    `SELECT id, filename, mime_type, size, created_at FROM note_attachments
     WHERE note_id = ? AND user_id = ? ORDER BY created_at ASC`
  ).bind(noteId, userId).all();
  return results || [];
}

async function createAttachment(env, user, noteId, file) {
  const note = await env.DB.prepare('SELECT id FROM notes WHERE id = ? AND user_id = ? AND deleted_at IS NULL')
    .bind(noteId, user.id).first();
  if (!note) throw new Error('یادداشت یافت نشد');
  if (file.size > ATTACHMENT_MAX_BYTES) throw new Error('حجم فایل نباید بیشتر از ۲۰ مگابایت باشد');

  const safeName = sanitizeFileName(file.name);
  const isImage = (file.type || '').startsWith('image/');
  const form = new FormData();
  form.append('chat_id', String(user.telegram_id));
  form.append(isImage ? 'photo' : 'document', file, safeName);
  form.append('caption', '📎 پیوست یادداشت — ' + safeName);

  const tgRes = await fetch(telegramApi(env.BOT_TOKEN, isImage ? 'sendPhoto' : 'sendDocument'), {
    method: 'POST',
    body: form,
  });
  const tgData = await tgRes.json();
  if (!tgData.ok) throw new Error('ارسال فایل به تلگرام ناموفق بود: ' + (tgData.description || ''));

  const result = tgData.result;
  let fileId, actualSize = file.size;
  if (isImage && result.photo && result.photo.length) {
    // تلگرام برای عکس چند سایز مختلف برمی‌گرداند؛ بزرگ‌ترین (باکیفیت‌ترین) را انتخاب می‌کنیم
    const largest = result.photo[result.photo.length - 1];
    fileId = largest.file_id;
    actualSize = largest.file_size || actualSize;
  } else if (result.document) {
    fileId = result.document.file_id;
    actualSize = result.document.file_size || actualSize;
  }
  if (!fileId) throw new Error('دریافت شناسهٔ فایل از تلگرام ناموفق بود');

  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO note_attachments (note_id, user_id, telegram_file_id, telegram_message_id, filename, mime_type, size, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
  ).bind(noteId, user.id, fileId, result.message_id, safeName, file.type || 'application/octet-stream', actualSize, ts).run();

  return env.DB.prepare('SELECT id, filename, mime_type, size, created_at FROM note_attachments WHERE id = ?')
    .bind(res.meta.last_row_id).first();
}

async function getAttachmentForDownload(env, userId, attachmentId) {
  const att = await env.DB.prepare('SELECT * FROM note_attachments WHERE id = ? AND user_id = ?')
    .bind(attachmentId, userId).first();
  if (!att) return null;
  // مسیر دانلود تلگرام کوتاه‌مدت است، پس هر بار قبل از دانلود دوباره از getFile گرفته می‌شود
  const fileInfoRes = await fetch(telegramApi(env.BOT_TOKEN, 'getFile') + '?file_id=' + encodeURIComponent(att.telegram_file_id));
  const fileInfo = await fileInfoRes.json();
  if (!fileInfo.ok) throw new Error('دریافت فایل از تلگرام ناموفق بود');
  const fileUrl = `https://api.telegram.org/file/bot${env.BOT_TOKEN}/${fileInfo.result.file_path}`;
  const fileRes = await fetch(fileUrl);
  if (!fileRes.ok || !fileRes.body) throw new Error('دانلود فایل از تلگرام ناموفق بود');
  return { body: fileRes.body, meta: att };
}

async function deleteAttachment(env, userId, attachmentId) {
  const att = await env.DB.prepare('SELECT * FROM note_attachments WHERE id = ? AND user_id = ?')
    .bind(attachmentId, userId).first();
  if (!att) return false;
  // تلاش برای حذف پیامِ فایل از چت تلگرام هم (اختیاری و best-effort؛ اگر کاربر پیام را
  // خودش دستی حذف کرده باشد یا خطایی رخ دهد، صرفاً نادیده گرفته می‌شود)
  if (att.telegram_message_id) {
    const owner = await env.DB.prepare('SELECT telegram_id FROM users WHERE id = ?').bind(userId).first();
    if (owner) {
      await fetch(telegramApi(env.BOT_TOKEN, 'deleteMessage'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ chat_id: owner.telegram_id, message_id: att.telegram_message_id }),
      }).catch(() => null);
    }
  }
  await env.DB.prepare('DELETE FROM note_attachments WHERE id = ? AND user_id = ?').bind(attachmentId, userId).run();
  return true;
}

// ---- Dashboard / Calendar / Analytics / Search ----
async function getDashboard(env, userId) {
  const today = todayInTehran();
  const tasks = await listTasks(env, userId, { date: today });
  const done = tasks.filter(t => t.is_completed).length;
  const total = tasks.length;
  const progress = total === 0 ? 0 : Math.round((done / total) * 100);
  const nextTask = tasks.filter(t => !t.is_completed).sort((a, b) => (a.task_time || '99:99').localeCompare(b.task_time || '99:99'))[0] || null;

  const habits = await listHabits(env, userId);
  const notes = await listNotes(env, userId, { pinned: 'true' });

  return {
    date: today,
    total_tasks: total,
    completed_tasks: done,
    progress_percent: progress,
    next_task: nextTask,
    tasks,
    habits_today: habits,
    pinned_notes: notes.slice(0, 3),
  };
}

async function getCalendar(env, userId, view, dateStr, calendarType = 'jalali') {
  const base = dateStr || todayInTehran();
  let from, to;
  if (view === 'day') {
    from = base; to = base;
  } else if (view === 'week') {
    if (calendarType === 'gregorian') {
      // هفتهٔ میلادی: دوشنبه تا یکشنبه (استاندارد ISO)
      const [y, m, d] = base.split('-').map(Number);
      const gDow = new Date(Date.UTC(y, m - 1, d)).getUTCDay(); // یکشنبه=۰
      const isoDow = (gDow + 6) % 7; // دوشنبه=۰ ... یکشنبه=۶
      from = addDaysToDateStr(base, -isoDow);
    } else {
      // هفتهٔ ایرانی: شنبه تا جمعه
      from = addDaysToDateStr(base, -persianDow(base));
    }
    to = addDaysToDateStr(from, 6);
  } else { // month
    if (calendarType === 'gregorian') {
      const [y, m] = base.split('-').map(Number);
      from = `${y}-${String(m).padStart(2, '0')}-01`;
      const lastDay = new Date(Date.UTC(y, m, 0)).getUTCDate();
      to = `${y}-${String(m).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
    } else {
      const j = jalaliOf(base);
      from = jalaliMonthFirstDayGregorian(j.jy, j.jm);
      to = addDaysToDateStr(from, jalaliMonthLength(j.jy, j.jm) - 1);
    }
  }

  const [tasks, notes, habitLogs] = await Promise.all([
    listTasks(env, userId, { from, to }),
    env.DB.prepare(
      `SELECT id, title, created_at FROM notes WHERE user_id = ? AND deleted_at IS NULL
       AND created_at >= ? AND created_at <= ? ORDER BY created_at ASC`
    ).bind(userId, tehranToUtcTimestamp(from, '00:00'), tehranToUtcTimestamp(to, '23:59')).all(),
    env.DB.prepare(
      `SELECT hl.log_date, h.title, h.icon FROM habit_logs hl
       JOIN habits h ON h.id = hl.habit_id AND h.deleted_at IS NULL
       WHERE h.user_id = ? AND hl.log_date >= ? AND hl.log_date <= ? ORDER BY hl.log_date ASC`
    ).bind(userId, from, to).all(),
  ]);

  const grouped = {};
  const ensureDay = (d) => grouped[d] || (grouped[d] = { tasks: [], notes: [], habits: [] });
  for (const t of tasks) ensureDay(t.task_date).tasks.push(t);
  for (const n of (notes.results || [])) {
    const d = tehranDateOf(n.created_at);
    ensureDay(d).notes.push({ id: n.id, title: n.title });
  }
  for (const h of (habitLogs.results || [])) ensureDay(h.log_date).habits.push({ title: h.title, icon: h.icon });

  return { view, from, to, calendar_type: calendarType, days: grouped };
}

// تاریخِ (به‌وقتِ تهران) متناظر با یک Unix timestamp — برای گروه‌بندی یادداشت‌ها بر اساس روزِ ثبت
function tehranDateOf(ts) {
  const fmt = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Tehran', year: 'numeric', month: '2-digit', day: '2-digit' });
  return fmt.format(new Date(ts * 1000)); // en-CA gives YYYY-MM-DD
}

async function getAnalytics(env, userId, range) {
  const today = todayInTehran();
  const from = range === 'month' ? addDaysToDateStr(today, -29) : addDaysToDateStr(today, -6);
  const tasks = await listTasks(env, userId, { from, to: today });
  const total = tasks.length;
  const completed = tasks.filter(t => t.is_completed).length;
  const successRate = total === 0 ? 0 : Math.round((completed / total) * 100);

  const byDay = {};
  for (const t of tasks) {
    byDay[t.task_date] = byDay[t.task_date] || { total: 0, done: 0 };
    byDay[t.task_date].total++;
    if (t.is_completed) byDay[t.task_date].done++;
  }

  const categories = await listCategories(env, userId);
  const byCategory = {};
  for (const t of tasks) {
    const cat = categories.find(c => c.id === t.category_id);
    const key = cat ? cat.name : 'بدون‌دسته';
    byCategory[key] = (byCategory[key] || 0) + 1;
  }
  const topCategory = Object.entries(byCategory).sort((a, b) => b[1] - a[1])[0]?.[0] || null;

  const habits = await listHabits(env, userId);
  const longestStreak = habits.reduce((max, h) => Math.max(max, h.streak), 0);

  return {
    range, from, to: today, total, completed, success_rate: successRate,
    by_day: byDay, by_category: byCategory, top_category: topCategory, longest_streak: longestStreak,
  };
}

async function search(env, userId, q) {
  if (!q || q.trim().length === 0) return { tasks: [], notes: [], habits: [] };
  const like = `%${q.trim()}%`;
  const [tasksR, notesR, habitsR] = await Promise.all([
    env.DB.prepare('SELECT * FROM tasks WHERE user_id = ? AND deleted_at IS NULL AND (title LIKE ? OR description LIKE ?) LIMIT 20')
      .bind(userId, like, like).all(),
    env.DB.prepare('SELECT * FROM notes WHERE user_id = ? AND deleted_at IS NULL AND (title LIKE ? OR content LIKE ?) LIMIT 20')
      .bind(userId, like, like).all(),
    env.DB.prepare('SELECT * FROM habits WHERE user_id = ? AND is_active = 1 AND deleted_at IS NULL AND title LIKE ? LIMIT 20')
      .bind(userId, like).all(),
  ]);
  return { tasks: tasksR.results, notes: notesR.results, habits: habitsR.results };
}

async function updateSettings(env, userId, data) {
  const fields = ['theme', 'language_code', 'timezone', 'calendar_type'];
  const sets = []; const binds = [];
  for (const f of fields) if (data[f] !== undefined) { sets.push(`${f} = ?`); binds.push(data[f]); }
  if (sets.length === 0) return env.DB.prepare('SELECT * FROM users WHERE id = ?').bind(userId).first();
  sets.push('updated_at = ?'); binds.push(nowTs());
  binds.push(userId);
  await env.DB.prepare(`UPDATE users SET ${sets.join(', ')} WHERE id = ?`).bind(...binds).run();
  return env.DB.prepare('SELECT * FROM users WHERE id = ?').bind(userId).first();
}

// هرگز pin_hash/pin_salt را مستقیم به کلاینت برنگردان — فقط وضعیتِ فعال بودن قفل کافی است
function sanitizeUser(user) {
  if (!user) return user;
  const { pin_hash, pin_salt, ...safe } = user;
  return { ...safe, has_pin: !!pin_hash };
}

// ============================================================
// 5.4) قفل امنیتی PIN (اختیاری)
// ذخیرهٔ امن: هیچ‌وقت PIN را به‌صورت متن ساده ذخیره نمی‌کنیم. از PBKDF2 (Web Crypto،
// کاملاً سازگار با Cloudflare Workers) با salt تصادفی و ۱۰۰,۰۰۰ تکرار استفاده می‌کنیم.
// همراه با محدودسازی تلاش‌های ناموفق (Lockout تصاعدی) برای جلوگیری از Brute-force.
// ============================================================
const PIN_MAX_ATTEMPTS = 5;
const PIN_LOCKOUT_BASE_SECONDS = 30;

function bufToHex(buf) {
  return [...new Uint8Array(buf)].map((b) => b.toString(16).padStart(2, '0')).join('');
}
function hexToBuf(hex) {
  const bytes = new Uint8Array(hex.length / 2);
  for (let i = 0; i < bytes.length; i++) bytes[i] = parseInt(hex.substr(i * 2, 2), 16);
  return bytes;
}

async function hashPin(pin, saltHex) {
  const encoder = new TextEncoder();
  const salt = saltHex ? hexToBuf(saltHex) : crypto.getRandomValues(new Uint8Array(16));
  const keyMaterial = await crypto.subtle.importKey('raw', encoder.encode(pin), { name: 'PBKDF2' }, false, ['deriveBits']);
  const bits = await crypto.subtle.deriveBits(
    { name: 'PBKDF2', salt, iterations: 100000, hash: 'SHA-256' },
    keyMaterial,
    256
  );
  return { hash: bufToHex(bits), salt: saltHex || bufToHex(salt) };
}

function isValidPinFormat(pin) {
  return typeof pin === 'string' && /^[0-9]{4,8}$/.test(pin);
}

async function getPinStatus(env, userId) {
  const user = await env.DB.prepare('SELECT pin_hash, pin_locked_until FROM users WHERE id = ?').bind(userId).first();
  const lockedUntil = (user && user.pin_locked_until) || 0;
  const now = nowTs();
  return {
    enabled: !!(user && user.pin_hash),
    locked: lockedUntil > now,
    locked_seconds_left: lockedUntil > now ? lockedUntil - now : 0,
  };
}

async function setPin(env, userId, pin) {
  if (!isValidPinFormat(pin)) throw new Error('رمز باید بین ۴ تا ۸ رقم باشد');
  const { hash, salt } = await hashPin(pin);
  await env.DB.prepare(
    `UPDATE users SET pin_hash = ?, pin_salt = ?, pin_fail_count = 0, pin_locked_until = 0, updated_at = ? WHERE id = ?`
  ).bind(hash, salt, nowTs(), userId).run();
  return { enabled: true };
}

async function changePin(env, userId, currentPin, newPin) {
  const user = await env.DB.prepare('SELECT pin_hash, pin_salt FROM users WHERE id = ?').bind(userId).first();
  if (!user || !user.pin_hash) throw new Error('هیچ رمزی فعلاً تنظیم نشده است');
  const { hash } = await hashPin(currentPin, user.pin_salt);
  if (hash !== user.pin_hash) throw new Error('رمز فعلی اشتباه است');
  return setPin(env, userId, newPin);
}

async function removePin(env, userId, currentPin) {
  const user = await env.DB.prepare('SELECT pin_hash, pin_salt FROM users WHERE id = ?').bind(userId).first();
  if (!user || !user.pin_hash) return { enabled: false }; // از قبل غیرفعال بوده
  const { hash } = await hashPin(currentPin, user.pin_salt);
  if (hash !== user.pin_hash) throw new Error('رمز اشتباه است');
  await env.DB.prepare(
    `UPDATE users SET pin_hash = NULL, pin_salt = NULL, pin_fail_count = 0, pin_locked_until = 0, updated_at = ? WHERE id = ?`
  ).bind(nowTs(), userId).run();
  return { enabled: false };
}

async function verifyPinAttempt(env, userId, pin) {
  const user = await env.DB.prepare('SELECT pin_hash, pin_salt, pin_fail_count, pin_locked_until FROM users WHERE id = ?')
    .bind(userId).first();
  if (!user || !user.pin_hash) return { ok: true }; // قفلی فعال نیست
  const now = nowTs();
  if (user.pin_locked_until && user.pin_locked_until > now) {
    return { ok: false, locked: true, locked_seconds_left: user.pin_locked_until - now };
  }
  const { hash } = await hashPin(pin, user.pin_salt);
  if (hash === user.pin_hash) {
    await env.DB.prepare('UPDATE users SET pin_fail_count = 0, pin_locked_until = 0 WHERE id = ?').bind(userId).run();
    return { ok: true };
  }
  const failCount = (user.pin_fail_count || 0) + 1;
  let lockedUntil = 0;
  let lockedSeconds = 0;
  if (failCount >= PIN_MAX_ATTEMPTS) {
    // قفل موقتِ تصاعدی: هر بار که مجدداً به آستانه برسد، مدت قفل بیشتر می‌شود
    const lockMultiplier = Math.pow(2, Math.floor(failCount / PIN_MAX_ATTEMPTS) - 1);
    lockedSeconds = PIN_LOCKOUT_BASE_SECONDS * lockMultiplier;
    lockedUntil = now + lockedSeconds;
  }
  await env.DB.prepare('UPDATE users SET pin_fail_count = ?, pin_locked_until = ? WHERE id = ?')
    .bind(failCount, lockedUntil, userId).run();
  return {
    ok: false,
    locked: lockedUntil > now,
    locked_seconds_left: lockedSeconds,
    attempts_left: Math.max(0, PIN_MAX_ATTEMPTS - (failCount % PIN_MAX_ATTEMPTS || PIN_MAX_ATTEMPTS)),
  };
}

// ============================================================
// 5.5) دستیار هوش مصنوعی (AI Assistant)
// طراحی ماژولار: تعویض مدل/سرویس فقط یعنی افزودن یک تابع callXxxProvider
// جدید و یک شاخهٔ دیگر در callAiProvider — هیچ بخش دیگری از پروژه لازم
// نیست تغییر کند.
// ============================================================
const AI_SYSTEM_PROMPT = `تو «دستیار ToDoList» هستی؛ یک دستیار متخصص در برنامه‌ریزی و بهره‌وری، با لحنی دوستانه،
حرفه‌ای و انگیزشی. فقط و فقط دربارهٔ موضوعات زیر به کاربر کمک می‌کنی:
- برنامه‌ریزی روزانه و مدیریت زمان
- اولویت‌بندی کارها و وظایف
- مدیریت و پیگیری عادت‌ها
- بهره‌وری و تمرکز
- یادداشت‌برداری و سازمان‌دهی اطلاعات
- تعیین و پیگیری اهداف
- برنامهٔ مطالعه و برنامهٔ کاری
- افزایش انگیزه
- راهنمایی دربارهٔ نحوهٔ استفادهٔ بهتر از امکانات اپلیکیشن ToDoList (تقویم، کارها، عادت‌ها، یادداشت‌ها، تنظیمات)

قوانین سختگیرانه دربارهٔ نحوهٔ پاسخ‌دهی:
۱. هرگز فرآیند فکری، استدلال داخلی، تحلیل گام‌به‌گامِ قبل از پاسخ، یا هر نوع "Chain of Thought" را در خروجی ننویس.
   هیچ‌وقت از تگ‌هایی مثل <think>، <thinking>، <reasoning> یا عباراتی مثل «بذار فکر کنم»، «تحلیل من:» استفاده نکن.
   فقط و فقط پاسخ نهایی، تمیز و آماده برای نمایش به کاربر را بنویس.
۲. اگر کاربر سؤالی کاملاً خارج از حوزه‌های بالا پرسید (سؤال عمومی، سیاسی، فنی-برنامه‌نویسی، پزشکی، سرگرمی و...)،
   با یک جملهٔ کوتاه و مؤدبانه بگو که فقط دربارهٔ برنامه‌ریزی، بهره‌وری و مدیریت کارها/عادت‌ها/یادداشت‌ها می‌توانی کمک کنی،
   و به آن سؤال پاسخ نده.
۳. هرگز ادعا نکن کاری را داخل اپ برای کاربر انجام داده‌ای (مثلاً «کار را برایت ثبت کردم») — تو فقط مشاوره می‌دهی؛
   خودِ کاربر باید از طریق رابط کاربری اپ، کار/یادداشت/عادت را ثبت کند.
۴. پاسخ‌ها را طبیعی، روان و به فارسی محاوره‌ای بنویس؛ از تکرار بی‌مورد جملات یا کلمات پرهیز کن.
۵. برای برنامه‌های چندمرحله‌ای یا اولویت‌بندی، از لیست بولت یا مراحل شماره‌دار استفاده کن؛ برای مقایسه‌ها در صورت نیاز از جدول
   ساده استفاده کن. پاسخ کوتاه و کاربردی بهتر از پاسخ طولانی و کلی است، مگر کاربر توضیح مفصل بخواهد.
۶. اگر اطلاعاتی از وضعیت فعلی کاربر (تعداد کارهای امروز، عادت‌ها، Streakها) در پیام سیستم داده شده، حتماً پیشنهادهایت را
   بر اساس همان اطلاعات واقعی شخصی‌سازی کن، نه پیشنهاد کلی و عمومی.
۷. همیشه پاسخ را کامل و تمام‌شده بنویس؛ هرگز وسط یک جمله، لیست یا جدول متوقف نشو. اگر موضوع واقعاً نیاز به توضیح جامع دارد،
   کامل و با جزئیات کافی بنویس؛ ناقص رها کردن پاسخ برای کوتاه‌ترکردن آن هرگز قابل قبول نیست.`;

const AI_MAX_HISTORY = 12; // تعداد پیام اخیر که به‌عنوان زمینه (context) به مدل داده می‌شود

async function getAiHistory(env, userId, limit = AI_MAX_HISTORY) {
  const res = await env.DB.prepare(
    `SELECT id, role, content, created_at FROM ai_messages WHERE user_id = ? ORDER BY id DESC LIMIT ?`
  ).bind(userId, limit).all();
  return (res.results || []).reverse();
}

async function saveAiMessage(env, userId, role, content) {
  const ts = nowTs();
  const res = await env.DB.prepare(
    `INSERT INTO ai_messages (user_id, role, content, created_at) VALUES (?, ?, ?, ?)`
  ).bind(userId, role, content, ts).run();
  return { id: res.meta.last_row_id, role, content, created_at: ts };
}

async function clearAiHistory(env, userId) {
  await env.DB.prepare(`DELETE FROM ai_messages WHERE user_id = ?`).bind(userId).run();
}

// خلاصهٔ سبک از وضعیت فعلی کاربر برای شخصی‌سازی پاسخ‌ها (بدون بارِ اضافه روی دیتابیس)
async function getAiUserContext(env, userId) {
  const today = todayInTehran();
  const [taskRow, habitRow] = await Promise.all([
    env.DB.prepare(
      `SELECT COUNT(*) AS total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS done
       FROM tasks WHERE user_id = ? AND task_date = ? AND deleted_at IS NULL`
    ).bind(userId, today).first(),
    env.DB.prepare(
      `SELECT title FROM habits WHERE user_id = ? AND deleted_at IS NULL LIMIT 5`
    ).bind(userId).all(),
  ]);
  const total = (taskRow && taskRow.total) || 0;
  const done = (taskRow && taskRow.done) || 0;
  const habitTitles = (habitRow.results || []).map((h) => h.title);
  if (total === 0 && habitTitles.length === 0) return null;
  let ctx = `وضعیت فعلی کاربر (فقط برای شخصی‌سازی پاسخ استفاده کن، آن را عیناً تکرار نکن): `;
  ctx += `امروز ${total} کار ثبت کرده که ${done} مورد را انجام داده. `;
  if (habitTitles.length) ctx += `عادت‌های فعال او: ${habitTitles.join('، ')}.`;
  return ctx;
}

// حذف کامل هرگونه متن استدلال/فکر داخلی (Chain-of-Thought) که برخی مدل‌های
// reasoning-محور قبل یا همراه پاسخ نهایی تولید می‌کنند. کاربر فقط باید پاسخ
// نهایی و تمیز را ببیند.
function stripAiReasoningArtifacts(text) {
  if (!text) return text;
  let out = text;
  // تگ‌های رایج مدل‌های reasoning (باز و بسته)
  out = out.replace(/<think>[\s\S]*?<\/think>/gi, '');
  out = out.replace(/<thinking>[\s\S]*?<\/thinking>/gi, '');
  out = out.replace(/<reasoning>[\s\S]*?<\/reasoning>/gi, '');
  out = out.replace(/<\|begin_of_thought\|>[\s\S]*?<\|end_of_thought\|>/gi, '');
  out = out.replace(/<\|begin_of_solution\|>|<\|end_of_solution\|>/gi, '');
  // اگر تگ باز بدون بسته باقی مانده باشد (پاسخ نصفه قطع‌شده)، هرچه بعدش آمده حذف شود
  out = out.replace(/<think>[\s\S]*$/gi, '');
  out = out.replace(/<thinking>[\s\S]*$/gi, '');
  // بلوک‌های مارک‌داونیِ رایج برای بیان تفکر داخلی
  out = out.replace(/^\s*\*{0,2}(Thought|Thinking|Reasoning|تحلیل داخلی|فکر می‌کنم که باید)\s*:\*{0,2}[\s\S]*?(?=\n\n|$)/gim, '');
  return out.trim();
}

// --- لایهٔ انتزاعی Providerهای هوش مصنوعی ---
// انتخاب Provider از env.AI_PROVIDER خوانده می‌شود (پیش‌فرض: workers-ai که نیازی
// به هیچ API Key خارجی ندارد و مستقیم داخل Cloudflare Workers اجرا می‌شود).
// هر تابع callXxxProvider شکل یکسان { text, truncated } برمی‌گرداند تا askAiAssistant
// بتواند مستقل از Provider، ادامهٔ پاسخ‌های بریده‌شده را مدیریت کند.
const AI_DEFAULT_MAX_TOKENS = 1536; // ریشهٔ اصلی باگ «پاسخ‌های ناقص»: قبلاً ۵۱۲ بود که برای پاسخ‌های ساختاریافته خیلی کم است

async function callAiProvider(env, messages) {
  const provider = (env.AI_PROVIDER || 'workers-ai').toLowerCase();
  if (provider === 'openai' && env.OPENAI_API_KEY) return callOpenAiProvider(env, messages);
  if (provider === 'gemini' && env.GEMINI_API_KEY) return callGeminiProvider(env, messages);
  if (env.AI) return callWorkersAiProvider(env, messages);
  throw new Error('هیچ Provider هوش مصنوعی روی این Worker پیکربندی نشده است (باید binding به نام AI یا کلید OPENAI_API_KEY/GEMINI_API_KEY تنظیم شود)');
}

// تشخیصِ حدسی بریده‌شدن پاسخ، برای Providerهایی که سیگنال رسمی finish_reason نمی‌دهند
// (مثل Workers AI). اگر متن با علامت پایان جمله یا بستن ساختار (کد/پرانتز/گیومه) تمام
// نشده باشد، به‌احتمال زیاد وسط جمله قطع شده است.
function looksTruncated(text) {
  if (!text) return false;
  const trimmed = text.trim();
  if (!trimmed) return false;
  const tail = trimmed.slice(-3);
  return !/[.!?؟۔:"'”)\]}»]\s*$/.test(tail) && !/```\s*$/.test(trimmed);
}

async function callWorkersAiProvider(env, messages) {
  const model = env.AI_MODEL || '@cf/meta/llama-3.1-8b-instruct';
  const maxTokens = Number(env.AI_MAX_TOKENS) || AI_DEFAULT_MAX_TOKENS;
  // برخی مدل‌های reasoning (مثل deepseek-r1-distill) پارامتر جداگانه برای غیرفعال‌کردن
  // حالت تفکر پشتیبانی می‌کنند؛ به‌عنوان خط دفاع اصلی همچنان روی stripAiReasoningArtifacts
  // تکیه می‌کنیم چون همهٔ مدل‌ها این پارامتر را رعایت نمی‌کنند.
  const result = await env.AI.run(model, { messages, max_tokens: maxTokens });
  const text = (result && (result.response || (result.result && result.result.response))) || '';
  return { text, truncated: looksTruncated(text) };
}

async function callOpenAiProvider(env, messages) {
  const maxTokens = Number(env.AI_MAX_TOKENS) || AI_DEFAULT_MAX_TOKENS;
  const res = await fetch('https://api.openai.com/v1/chat/completions', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${env.OPENAI_API_KEY}` },
    body: JSON.stringify({ model: env.AI_MODEL || 'gpt-4o-mini', messages, max_tokens: maxTokens }),
  });
  if (!res.ok) throw new Error('خطا در ارتباط با OpenAI');
  const data = await res.json();
  const choice = data.choices && data.choices[0];
  const text = (choice && choice.message && choice.message.content) || '';
  // OpenAI سیگنال رسمی می‌دهد: finish_reason === 'length' یعنی پاسخ به‌خاطر محدودیت توکن بریده شده
  const truncated = choice && choice.finish_reason === 'length';
  return { text, truncated: !!truncated };
}

async function callGeminiProvider(env, messages) {
  const maxTokens = Number(env.AI_MAX_TOKENS) || AI_DEFAULT_MAX_TOKENS;
  const sys = messages.find((m) => m.role === 'system');
  const rest = messages.filter((m) => m.role !== 'system');
  const contents = rest.map((m) => ({ role: m.role === 'assistant' ? 'model' : 'user', parts: [{ text: m.content }] }));
  const model = env.AI_MODEL || 'gemini-1.5-flash';
  const res = await fetch(
    `https://generativelanguage.googleapis.com/v1beta/models/${model}:generateContent?key=${env.GEMINI_API_KEY}`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        contents,
        systemInstruction: sys ? { parts: [{ text: sys.content }] } : undefined,
        generationConfig: { maxOutputTokens: maxTokens },
      }),
    }
  );
  if (!res.ok) throw new Error('خطا در ارتباط با Gemini');
  const data = await res.json();
  const candidate = data.candidates && data.candidates[0];
  const text = (candidate && candidate.content && candidate.content.parts && candidate.content.parts[0] && candidate.content.parts[0].text) || '';
  // Gemini سیگنال رسمی می‌دهد: finishReason === 'MAX_TOKENS' یعنی به سقف توکن خورده
  const truncated = candidate && candidate.finishReason === 'MAX_TOKENS';
  return { text, truncated: !!truncated };
}

const AI_MAX_CONTINUATION_ROUNDS = 3; // حداکثر تعداد دفعاتی که در صورت بریده‌شدن، از مدل ادامه خواسته می‌شود

// این تابع علت اصلی «پاسخ‌های ناقص» را حل می‌کند: به‌جای فقط بزرگ‌کردن max_tokens
// (که همچنان ممکن است کافی نباشد)، وقتی Provider سیگنال بریده‌شدن بدهد (یا حدس زده شود)،
// خودکار و شفاف از مدل می‌خواهد دقیقاً از همان‌جا ادامه دهد، و بخش‌ها را بدون درز به هم می‌چسباند.
async function generateCompleteAiReply(env, messages) {
  let combinedText = '';
  let currentMessages = messages;
  for (let round = 0; round < AI_MAX_CONTINUATION_ROUNDS; round++) {
    const { text, truncated } = await callAiProvider(env, currentMessages);
    const cleaned = stripAiReasoningArtifacts(text);
    combinedText += (combinedText && cleaned ? (combinedText.endsWith('\n') ? '' : ' ') : '') + cleaned;
    if (!truncated || !cleaned) break;
    currentMessages = [
      ...currentMessages,
      { role: 'assistant', content: cleaned },
      { role: 'user', content: 'ادامه بده، دقیقاً از همان جایی که متوقف شدی، بدون تکرار مطالب قبلی و بدون معرفی مجدد موضوع.' },
    ];
  }
  return combinedText;
}

async function askAiAssistant(env, userId, userMessage) {
  const [history, userCtx] = await Promise.all([
    getAiHistory(env, userId),
    getAiUserContext(env, userId).catch(() => null),
  ]);
  const messages = [
    { role: 'system', content: AI_SYSTEM_PROMPT },
    ...(userCtx ? [{ role: 'system', content: userCtx }] : []),
    ...history.map((h) => ({ role: h.role, content: h.content })),
    { role: 'user', content: userMessage },
  ];
  await saveAiMessage(env, userId, 'user', userMessage);
  let replyText;
  try {
    replyText = await generateCompleteAiReply(env, messages);
    if (!replyText || !replyText.trim()) {
      replyText = 'متأسفم، در حال حاضر نتوانستم پاسخ مناسبی تولید کنم. لطفاً دوباره تلاش کن.';
    }
  } catch (err) {
    replyText = 'خطا در ارتباط با دستیار هوش مصنوعی. لطفاً کمی بعد دوباره تلاش کن. (' + err.message + ')';
  }
  return saveAiMessage(env, userId, 'assistant', replyText);
}

// ============================================================
// 6) AUTH MIDDLEWARE
// ============================================================
async function authenticate(request, env) {
  const authHeader = request.headers.get('Authorization') || '';
  const initData = authHeader.startsWith('tma ') ? authHeader.slice(4) : authHeader;
  const tgUser = await verifyTelegramInitData(initData, env.BOT_TOKEN);
  if (!tgUser) return null;
  const user = await ensureUser(env, tgUser);
  return user;
}

// ============================================================
// 7) API ROUTER
// ============================================================
async function handleApi(request, env, ctx, url) {
  if (request.method === 'OPTIONS') return json({ ok: true });

  const user = await authenticate(request, env);
  if (!user) return errorResponse('احراز هویت نامعتبر است', 401);

  const parts = url.pathname.replace('/api/v1/', '').split('/').filter(Boolean);
  const [resource, id, sub] = parts;
  const method = request.method;

  let body = {};
  const isMultipartUpload = resource === 'notes' && id && sub === 'attachments' && method === 'POST';
  if ((method === 'POST' || method === 'PATCH') && !isMultipartUpload) {
    try { body = await request.json(); } catch { body = {}; }
  }

  try {
    // ---- Dashboard ----
    if (resource === 'dashboard' && method === 'GET') {
      return json({ ok: true, data: await getDashboard(env, user.id) });
    }

    // ---- Categories ----
    if (resource === 'categories' && method === 'GET') {
      return json({ ok: true, data: await listCategories(env, user.id) });
    }
    if (resource === 'categories' && method === 'POST') {
      if (!isNonEmptyString(body.name)) return errorResponse('نام دسته‌بندی الزامی است');
      return json({ ok: true, data: await createCategory(env, user.id, body) }, 201);
    }

    // ---- Tasks ----
    if (resource === 'tasks' && !id && method === 'GET') {
      const filters = {
        date: url.searchParams.get('date') || undefined,
        category: url.searchParams.get('category') || undefined,
        status: url.searchParams.get('status') || undefined,
      };
      return json({ ok: true, data: await listTasks(env, user.id, filters) });
    }
    if (resource === 'tasks' && !id && method === 'POST') {
      if (!isNonEmptyString(body.title)) return errorResponse('عنوان کار الزامی است');
      if (!isNonEmptyString(body.task_date)) return errorResponse('تاریخ کار الزامی است');
      const task = await createTask(env, user, {
        ...body, title: sanitizeText(body.title, 200), description: sanitizeText(body.description, 2000),
      });
      return json({ ok: true, data: task }, 201);
    }
    if (resource === 'tasks' && id && !sub && method === 'GET') {
      const task = await getTask(env, user.id, id);
      if (!task) return errorResponse('کار یافت نشد', 404);
      return json({ ok: true, data: task });
    }
    if (resource === 'tasks' && id && !sub && method === 'PATCH') {
      const task = await updateTask(env, user.id, id, body);
      if (!task) return errorResponse('کار یافت نشد', 404);
      return json({ ok: true, data: task });
    }
    if (resource === 'tasks' && id && !sub && method === 'DELETE') {
      await deleteTask(env, user.id, id);
      return json({ ok: true });
    }
    if (resource === 'tasks' && id && sub === 'toggle' && method === 'PATCH') {
      const task = await toggleTask(env, user.id, id);
      if (!task) return errorResponse('کار یافت نشد', 404);
      return json({ ok: true, data: task });
    }
    if (resource === 'tasks' && id && sub === 'reminders' && method === 'GET') {
      return json({ ok: true, data: await listRemindersForTask(env, user.id, id) });
    }
    if (resource === 'tasks' && id && sub === 'reminders' && method === 'POST') {
      const task = await getTask(env, user.id, id);
      if (!task) return errorResponse('کار یافت نشد', 404);
      const reminder = await createReminder(env, user, task, body);
      return json({ ok: true, data: reminder }, 201);
    }

    // ---- Reminders (standalone) ----
    if (resource === 'reminders' && id && method === 'PATCH') {
      const reminder = await toggleReminder(env, user.id, id);
      if (!reminder) return errorResponse('یادآور یافت نشد', 404);
      return json({ ok: true, data: reminder });
    }
    if (resource === 'reminders' && id && method === 'DELETE') {
      await deleteReminder(env, user.id, id);
      return json({ ok: true });
    }

    // ---- Habits ----
    if (resource === 'habits' && !id && method === 'GET') {
      return json({ ok: true, data: await listHabits(env, user.id) });
    }
    if (resource === 'habits' && !id && method === 'POST') {
      if (!isNonEmptyString(body.title)) return errorResponse('عنوان عادت الزامی است');
      return json({ ok: true, data: await createHabit(env, user.id, body) }, 201);
    }
    if (resource === 'habits' && id && sub === 'log' && method === 'POST') {
      const habit = await logHabitToday(env, user.id, id);
      if (!habit) return errorResponse('عادت یافت نشد', 404);
      return json({ ok: true, data: habit });
    }
    if (resource === 'habits' && id && sub === 'progress' && method === 'GET') {
      const progress = await getHabitProgress(env, user.id, id);
      if (!progress) return errorResponse('عادت یافت نشد', 404);
      return json({ ok: true, data: progress });
    }
    if (resource === 'habits' && id && !sub && method === 'PATCH') {
      const habit = await updateHabit(env, user.id, id, body);
      if (!habit) return errorResponse('عادت یافت نشد', 404);
      return json({ ok: true, data: habit });
    }
    if (resource === 'habits' && id && !sub && method === 'DELETE') {
      await deleteHabit(env, user.id, id);
      return json({ ok: true });
    }

    // ---- Notes ----
    if (resource === 'notes' && !id && method === 'GET') {
      return json({ ok: true, data: await listNotes(env, user.id, {
        search: url.searchParams.get('search') || undefined,
        pinned: url.searchParams.get('pinned') || undefined,
      }) });
    }
    if (resource === 'notes' && !id && method === 'POST') {
      if (!isNonEmptyString(body.content)) return errorResponse('متن یادداشت الزامی است');
      return json({ ok: true, data: await createNote(env, user.id, body) }, 201);
    }
    if (resource === 'notes' && id && !sub && method === 'GET') {
      const note = await getNote(env, user.id, id);
      if (!note) return errorResponse('یادداشت یافت نشد', 404);
      return json({ ok: true, data: note });
    }
    if (resource === 'notes' && id && method === 'PATCH') {
      const note = await updateNote(env, user.id, id, body);
      return json({ ok: true, data: note });
    }
    if (resource === 'notes' && id && method === 'DELETE') {
      await deleteNote(env, user.id, id);
      return json({ ok: true });
    }
    if (resource === 'notes' && id && sub === 'attachments' && method === 'POST') {
      let form;
      try { form = await request.formData(); } catch { return errorResponse('فرمت آپلود نامعتبر است'); }
      const file = form.get('file');
      if (!file || typeof file.arrayBuffer !== 'function') return errorResponse('فایلی ارسال نشده است');
      try {
        return json({ ok: true, data: await createAttachment(env, user, id, file) }, 201);
      } catch (err) {
        return errorResponse(err.message || 'خطا در آپلود فایل');
      }
    }
    if (resource === 'attachments' && id && sub === 'download' && method === 'GET') {
      let result;
      try { result = await getAttachmentForDownload(env, user.id, id); }
      catch (err) { return errorResponse(err.message, 500); }
      if (!result) return errorResponse('فایل یافت نشد', 404);
      return new Response(result.body, {
        headers: {
          'Content-Type': result.meta.mime_type || 'application/octet-stream',
          'Content-Disposition': `inline; filename="${encodeURIComponent(result.meta.filename)}"`,
          'Cache-Control': 'private, max-age=3600',
        },
      });
    }
    if (resource === 'attachments' && id && !sub && method === 'DELETE') {
      await deleteAttachment(env, user.id, id);
      return json({ ok: true });
    }

    // ---- Calendar ----
    if (resource === 'calendar' && method === 'GET') {
      const view = url.searchParams.get('view') || 'month';
      const date = url.searchParams.get('date') || undefined;
      const calType = user.calendar_type || 'jalali';
      return json({ ok: true, data: await getCalendar(env, user.id, view, date, calType) });
    }

    // ---- Search ----
    if (resource === 'search' && method === 'GET') {
      return json({ ok: true, data: await search(env, user.id, url.searchParams.get('q') || '') });
    }

    // ---- AI Assistant ----
    if (resource === 'ai' && id === 'messages' && method === 'GET') {
      return json({ ok: true, data: await getAiHistory(env, user.id, 50) });
    }
    if (resource === 'ai' && id === 'chat' && method === 'POST') {
      if (!isNonEmptyString(body.message)) return errorResponse('پیام نمی‌تواند خالی باشد');
      if (body.message.length > 2000) return errorResponse('پیام بیش‌ازحد طولانی است');
      const reply = await askAiAssistant(env, user.id, body.message.trim());
      return json({ ok: true, data: reply });
    }
    if (resource === 'ai' && id === 'clear' && method === 'POST') {
      await clearAiHistory(env, user.id);
      return json({ ok: true });
    }

    // ---- Analytics ----
    if (resource === 'analytics' && method === 'GET') {
      return json({ ok: true, data: await getAnalytics(env, user.id, url.searchParams.get('range') || 'week') });
    }

    // ---- Settings ----
    if (resource === 'settings' && method === 'GET') {
      return json({ ok: true, data: sanitizeUser(user) });
    }
    if (resource === 'settings' && method === 'PATCH') {
      return json({ ok: true, data: sanitizeUser(await updateSettings(env, user.id, body)) });
    }

    // ---- Support / Share ----
    if (resource === 'support' && id === 'share-info' && method === 'GET') {
      try {
        const username = await getBotUsername(env);
        return json({ ok: true, data: { bot_link: `https://t.me/${username}`, bot_username: username, caption: SUPPORT_BANNER_CAPTION } });
      } catch (err) {
        return errorResponse(err.message, 500);
      }
    }
    if (resource === 'support' && id === 'banner-image' && method === 'GET') {
      const imageBytes = base64ToUint8Array(SUPPORT_BANNER_IMAGE_BASE64);
      return new Response(imageBytes, { headers: { 'Content-Type': 'image/jpeg', 'Cache-Control': 'private, max-age=86400' } });
    }
    if (resource === 'support' && id === 'send-banner' && method === 'POST') {
      try {
        await sendSupportBanner(env, user);
        return json({ ok: true });
      } catch (err) {
        return errorResponse(err.message, 500);
      }
    }

    // ---- Security / PIN Lock ----
    if (resource === 'security' && id === 'pin' && sub === 'status' && method === 'GET') {
      return json({ ok: true, data: await getPinStatus(env, user.id) });
    }
    if (resource === 'security' && id === 'pin' && sub === 'set' && method === 'POST') {
      if (!isValidPinFormat(body.pin)) return errorResponse('رمز باید بین ۴ تا ۸ رقم باشد');
      return json({ ok: true, data: await setPin(env, user.id, body.pin) });
    }
    if (resource === 'security' && id === 'pin' && sub === 'change' && method === 'POST') {
      if (!isValidPinFormat(body.newPin)) return errorResponse('رمز جدید باید بین ۴ تا ۸ رقم باشد');
      if (!isNonEmptyString(body.currentPin)) return errorResponse('رمز فعلی الزامی است');
      return json({ ok: true, data: await changePin(env, user.id, body.currentPin, body.newPin) });
    }
    if (resource === 'security' && id === 'pin' && sub === 'remove' && method === 'POST') {
      if (!isNonEmptyString(body.currentPin)) return errorResponse('رمز فعلی الزامی است');
      return json({ ok: true, data: await removePin(env, user.id, body.currentPin) });
    }
    if (resource === 'security' && id === 'pin' && sub === 'verify' && method === 'POST') {
      if (!isNonEmptyString(body.pin)) return errorResponse('رمز الزامی است');
      const result = await verifyPinAttempt(env, user.id, body.pin);
      if (!result.ok) return json({ ok: false, error: result.locked ? 'حساب موقتاً قفل شده است' : 'رمز اشتباه است', data: result }, result.locked ? 429 : 401);
      return json({ ok: true, data: result });
    }

    return errorResponse('مسیر یافت نشد', 404);
  } catch (err) {
    return errorResponse('خطای داخلی سرور', 500, err.message);
  }
}

// ============================================================
// 8) BOT WEBHOOK HANDLER
// ============================================================
async function handleWebhook(request, env) {
  const secret = request.headers.get('X-Telegram-Bot-Api-Secret-Token');
  if (env.WEBHOOK_SECRET && secret !== env.WEBHOOK_SECRET) {
    return new Response('Forbidden', { status: 403 });
  }

  let update;
  try { update = await request.json(); } catch { return json({ ok: true }); }

  const message = update.message;
  if (message && message.text) {
    const chatId = message.chat.id;
    const text = message.text.trim();

    if (text === '/start') {
      await ensureUser(env, message.from);
      await tgSendMessage(env, chatId,
        `👋 سلام ${message.from.first_name || ''}!\n\nبه <b>ToDoList</b> خوش آمدید 🎯\nمدیریت کارها، عادت‌ها، یادداشت‌ها و یادآورهای روزانه‌ی شما، همه در یک اپلیکیشن.`,
        miniAppButton(env)
      );
    } else if (text === '/stats') {
      const user = await ensureUser(env, message.from);
      const dashboard = await getDashboard(env, user.id);
      await tgSendMessage(env, chatId,
        `📊 <b>آمار امروز</b>\n\n✅ انجام‌شده: ${dashboard.completed_tasks}/${dashboard.total_tasks}\n📈 پیشرفت: ${dashboard.progress_percent}%`,
        miniAppButton(env)
      );
    } else {
      await tgSendMessage(env, chatId, 'برای مدیریت کارها، عادت‌ها و یادداشت‌های خود، Mini App را باز کنید 👇', miniAppButton(env));
    }
  }

  // دکمه‌های عملیاتیِ اعلان‌ها (✅ انجام شد / ⏰ یادآوری دوباره)
  const callback = update.callback_query;
  if (callback && callback.data) {
    const [action, idStr] = callback.data.split(':');
    const targetId = Number(idStr);
    try {
      const user = await ensureUser(env, callback.from);
      if (action === 'done' && targetId) {
        const task = await markTaskDone(env, user.id, targetId);
        await answerCallbackQuery(env, callback.id, task ? '✅ عالی بود! کار انجام‌شده ثبت شد' : '❌ کار یافت نشد');
        if (task) await clearReminderMessageButtons(env, callback.message);
      } else if (action === 'snooze' && targetId) {
        await snoozeReminder(env, targetId, 10);
        await answerCallbackQuery(env, callback.id, '⏰ باشه، ۱۰ دقیقهٔ دیگر دوباره یادآوری می‌کنم');
      } else {
        await answerCallbackQuery(env, callback.id, '');
      }
    } catch (err) {
      await answerCallbackQuery(env, callback.id, '❌ خطایی رخ داد، دوباره تلاش کنید');
    }
  }

  return json({ ok: true });
}

// ============================================================
// 9) SCHEDULED HANDLER — Reminder Dispatcher (Cron: هر ۱ دقیقه)
// ============================================================
function formatDateForMessage(dateStr, calendarType) {
  const dow = WEEKDAY_NAMES_FA_SERVER[weekdayOfDateStr(dateStr)];
  if (calendarType === 'gregorian') {
    const [y, m, d] = dateStr.split('-').map(Number);
    return `${dow}، ${d}/${m}/${y}`;
  }
  const j = jalaliOf(dateStr);
  return `${dow}، ${j.jd} ${JALALI_MONTH_NAMES_SERVER[j.jm - 1]} ${j.jy}`;
}

async function markTaskDone(env, userId, taskId) {
  const task = await getTask(env, userId, taskId);
  if (!task) return null;
  if (!task.is_completed) {
    await env.DB.prepare('UPDATE tasks SET is_completed = 1, completed_at = ?, updated_at = ? WHERE id = ? AND user_id = ?')
      .bind(nowTs(), nowTs(), taskId, userId).run();
  }
  return getTask(env, userId, taskId);
}

async function snoozeReminder(env, reminderId, minutes = 10) {
  const newTrigger = nowTs() + minutes * 60;
  await env.DB.prepare('UPDATE reminders SET trigger_at = ?, is_active = 1, last_sent_at = NULL WHERE id = ?')
    .bind(newTrigger, reminderId).run();
}

async function answerCallbackQuery(env, callbackQueryId, text) {
  return fetch(telegramApi(env.BOT_TOKEN, 'answerCallbackQuery'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ callback_query_id: callbackQueryId, text, show_alert: false }),
  }).catch(() => null);
}

async function clearReminderMessageButtons(env, message) {
  if (!message || !message.chat) return;
  await fetch(telegramApi(env.BOT_TOKEN, 'editMessageReplyMarkup'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ chat_id: message.chat.id, message_id: message.message_id, reply_markup: { inline_keyboard: [] } }),
  }).catch(() => null);
}

const PRIORITY_EMOJI_FA = { low: '🔵', normal: '🟢', high: '🟠', urgent: '🔴' };
const PRIORITY_LABEL_FA = { low: 'کم', normal: 'عادی', high: 'بالا', urgent: 'فوری' };
const REMINDER_MOTIVATION_LINES = [
  'یه قدم کوچیک، یه روز بهتر 💪',
  'همین الان شروع کن، بعداً بهت افتخار می‌کنی ✨',
  'با انجام همین یکی، امروزت کامل می‌شه 🌟',
  'پیوستگی مهم‌تر از کمال‌گراییه — انجامش بده 🔥',
  'وقتشه! فقط چند دقیقه زمان می‌بره ⏱️',
];

// پیام یادآوریِ کامل و ساخت‌یافته: عنوان، توضیحات (در صورت وجود)، تاریخ (مطابق تقویم
// انتخابیِ کاربر)، ساعت، وضعیت تکرار، اولویت، دسته‌بندی، و یک جملهٔ انگیزشی کوتاه در پایان.
function buildReminderMessage(rem) {
  const lines = [];
  lines.push(`🔔 <b>یادآوری کار</b>`);
  lines.push('');
  lines.push(`📝 <b>${escapeHtmlServer(rem.task_title)}</b>`);
  if (rem.task_description && rem.task_description.trim()) {
    lines.push(`💬 ${escapeHtmlServer(rem.task_description.trim())}`);
  }
  lines.push('');
  lines.push(`📅 ${formatDateForMessage(rem.task_date, rem.user_calendar_type)}`);
  if (rem.task_time) lines.push(`🕐 ساعت ${rem.task_time}`);
  if (rem.category_name) lines.push(`🏷️ دسته‌بندی: ${escapeHtmlServer(rem.category_name)}`);
  const priorityEmoji = PRIORITY_EMOJI_FA[rem.priority] || '🟢';
  const priorityLabel = PRIORITY_LABEL_FA[rem.priority] || 'عادی';
  lines.push(`${priorityEmoji} اولویت: ${priorityLabel}`);
  if (rem.recurrence_type === 'weekly') {
    const days = parseRecurrenceDays(rem.recurrence_days).map((d) => WEEKDAY_NAMES_FA_SERVER[d]);
    if (days.length) lines.push(`🔁 تکرار هفتگی: ${days.join('، ')}`);
  }
  lines.push('');
  lines.push(`💡 ${REMINDER_MOTIVATION_LINES[rem.id % REMINDER_MOTIVATION_LINES.length]}`);
  return lines.join('\n');
}

// خروجی این تابع همیشه در HTML parse_mode ارسال می‌شود؛ پس هر متن آزاد (عنوان/توضیحات
// کاربر) باید از کاراکترهای خاص HTML خالی شود، وگرنه پیام یا خطا می‌دهد یا خراب نمایش داده می‌شود
function escapeHtmlServer(text) {
  return String(text)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

async function dispatchReminders(env) {
  const now = nowTs();
  const { results: dueReminders } = await env.DB.prepare(
    `SELECT r.*, t.title AS task_title, t.description AS task_description, t.task_time AS task_time,
            t.task_date AS task_date, t.priority AS priority, t.recurrence_type AS recurrence_type,
            t.recurrence_days AS recurrence_days, c.name AS category_name,
            u.telegram_id AS chat_id, u.timezone AS user_timezone, u.calendar_type AS user_calendar_type
     FROM reminders r
     JOIN tasks t ON t.id = r.task_id AND t.deleted_at IS NULL
     JOIN users u ON u.id = r.user_id
     LEFT JOIN categories c ON c.id = t.category_id
     WHERE r.is_active = 1 AND r.trigger_at <= ? AND (r.last_sent_at IS NULL OR r.last_sent_at < r.trigger_at)
     LIMIT 50`
  ).bind(now).all();

  for (const rem of dueReminders) {
    const text = buildReminderMessage(rem);
    const buttons = {
      inline_keyboard: [
        [
          { text: '✅ انجام شد', callback_data: `done:${rem.task_id}` },
          { text: '⏰ ۱۰ دقیقه دیگر', callback_data: `snooze:${rem.id}` },
        ],
        [{ text: '📋 مشاهده در اپ', web_app: { url: env.MINI_APP_URL } }],
      ],
    };
    await tgSendMessage(env, rem.chat_id, text, { reply_markup: buttons });

    let nextTrigger = null;
    if (rem.repeat_type === 'daily') nextTrigger = rem.trigger_at + 86400;
    else if (rem.repeat_type === 'weekly') nextTrigger = rem.trigger_at + 86400 * 7;

    if (nextTrigger) {
      await env.DB.prepare('UPDATE reminders SET last_sent_at = ?, trigger_at = ? WHERE id = ?')
        .bind(now, nextTrigger, rem.id).run();
    } else {
      await env.DB.prepare('UPDATE reminders SET last_sent_at = ?, is_active = 0 WHERE id = ?')
        .bind(now, rem.id).run();
    }
  }
}

// ============================================================
// 10) FRONTEND — Mini App (HTML + CSS + JS در یک رشته Embedded)
// ============================================================
const HTML_PAGE = `<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
<title>ToDoList</title>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
<style>
${'/*__CSS_PLACEHOLDER__*/'}
</style>
</head>
<body>
<div id="app">
  <header class="topbar">
    <button id="settingsBtn" class="icon-btn" aria-label="تنظیمات">⚙️</button>
    <div class="topbar-title" id="pageTitle" data-i18n="nav.dashboard">🏠 خانه</div>
    <div class="topbar-right-group">
      <button id="assistantBtn" class="icon-btn" aria-label="دستیار هوش مصنوعی">🤖</button>
      <button id="searchBtn" class="icon-btn" aria-label="جستجو">🔍</button>
    </div>
  </header>

  <main id="mainContent" class="main-content"></main>

  <button id="fab" class="fab" aria-label="افزودن">＋</button>

  <nav class="bottom-nav">
    <button class="nav-item active" data-page="dashboard"><span>🏠</span><small data-i18n="nav.dashboard.short">خانه</small></button>
    <button class="nav-item" data-page="tasks"><span>📌</span><small data-i18n="nav.tasks.short">کارها</small></button>
    <button class="nav-item" data-page="habits"><span>🔥</span><small data-i18n="nav.habits.short">عادت‌ها</small></button>
    <button class="nav-item" data-page="calendar"><span>📅</span><small data-i18n="nav.calendar.short">تقویم</small></button>
    <button class="nav-item" data-page="notes"><span>📝</span><small data-i18n="nav.notes.short">یادداشت</small></button>
  </nav>
</div>

<div id="toastContainer" class="toast-container"></div>
<div id="modalRoot"></div>

<script>
${'/*__JS_PLACEHOLDER__*/'}
</script>
</body>
</html>`;

// ---- CSS ----
const CSS_TEXT = `
* { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
html, body { height: 100%; }
body {
  font-family: 'Vazirmatn', sans-serif;
  background: var(--bg-primary);
  color: var(--text-primary);
  overflow-x: hidden;
  transition: background .3s ease, color .3s ease;
}

/* ===== Theme Tokens ===== */
:root, .theme-light {
  --bg-primary: #F7F8FC; --bg-card: #FFFFFF; --bg-elevated: #FFFFFF;
  --text-primary: #1A1D29; --text-secondary: #6B7280; --border-color: #EEF0F5;
  --accent: #6C5CE7; --accent-light: #EDE9FF;
  --success: #00B894; --danger: #FF6B6B; --warning: #FDCB6E;
  --shadow: 0 4px 20px rgba(0,0,0,.06);
}
.theme-dark {
  --bg-primary: #14151F; --bg-card: #1E2030; --bg-elevated: #262A3E;
  --text-primary: #F1F2F6; --text-secondary: #9298B0; --border-color: #2C2F45;
  --accent: #8C7CFF; --accent-light: #2A2650;
  --success: #26D9A0; --danger: #FF7A7A; --warning: #FFD873;
  --shadow: 0 4px 20px rgba(0,0,0,.35);
}
.theme-blue {
  --bg-primary: #F0F6FF; --bg-card: #FFFFFF; --bg-elevated: #FFFFFF;
  --text-primary: #12233F; --text-secondary: #5C7093; --border-color: #E1ECFB;
  --accent: #0984E3; --accent-light: #DCEEFF;
  --success: #00B894; --danger: #FF6B6B; --warning: #FDCB6E;
  --shadow: 0 4px 20px rgba(9,132,227,.10);
}
.theme-green {
  --bg-primary: #F1FBF6; --bg-card: #FFFFFF; --bg-elevated: #FFFFFF;
  --text-primary: #103824; --text-secondary: #567A69; --border-color: #DCF3E7;
  --accent: #00B894; --accent-light: #DAF7EA;
  --success: #00B894; --danger: #FF6B6B; --warning: #FDCB6E;
  --shadow: 0 4px 20px rgba(0,184,148,.10);
}
.theme-purple {
  --bg-primary: #F8F5FF; --bg-card: #FFFFFF; --bg-elevated: #FFFFFF;
  --text-primary: #2B1B4D; --text-secondary: #7965A3; --border-color: #EBE1FB;
  --accent: #A55EEA; --accent-light: #F0E4FE;
  --success: #00B894; --danger: #FF6B6B; --warning: #FDCB6E;
  --shadow: 0 4px 20px rgba(165,94,234,.12);
}
.theme-orange {
  --bg-primary: #FFF7F0; --bg-card: #FFFFFF; --bg-elevated: #FFFFFF;
  --text-primary: #402410; --text-secondary: #8A6A50; --border-color: #FCE7D6;
  --accent: #E17055; --accent-light: #FBE3DA;
  --success: #00B894; --danger: #FF6B6B; --warning: #FDCB6E;
  --shadow: 0 4px 20px rgba(225,112,85,.12);
}

/* ===== Layout ===== */
#app { display: flex; flex-direction: column; min-height: 100vh; padding-bottom: 78px; }
.topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px; position: sticky; top: 0; z-index: 20;
  background: var(--bg-primary); backdrop-filter: blur(8px);
}
.topbar-title { font-weight: 700; font-size: 17px; }
.topbar-right-group { display: flex; align-items: center; gap: 8px; }
.icon-btn {
  width: 40px; height: 40px; border: none; border-radius: 12px; background: var(--bg-card);
  box-shadow: var(--shadow); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: transform .15s ease;
}
.icon-btn:active { transform: scale(.92); }
.main-content { flex: 1; padding: 4px 16px 24px; animation: fadeIn .35s ease; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(8px);} to { opacity: 1; transform: translateY(0);} }

/* ===== Bottom Nav ===== */
.bottom-nav {
  position: fixed; bottom: 0; left: 0; right: 0; height: 68px;
  background: var(--bg-card); border-top: 1px solid var(--border-color);
  display: flex; z-index: 30; padding-bottom: env(safe-area-inset-bottom);
}
.nav-item {
  flex: 1; border: none; background: transparent; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 2px; font-size: 20px; color: var(--text-secondary);
  cursor: pointer; transition: color .2s ease, transform .2s ease;
}
.nav-item small { font-size: 11px; font-weight: 500; }
.nav-item.active { color: var(--accent); transform: translateY(-2px); }

.fab {
  position: fixed; bottom: 86px; left: 20px; width: 56px; height: 56px; border-radius: 50%;
  background: var(--accent); color: #fff; font-size: 28px; border: none; box-shadow: var(--shadow);
  cursor: pointer; z-index: 25; display: flex; align-items: center; justify-content: center;
  transition: transform .2s cubic-bezier(.34,1.56,.64,1);
}
.fab:active { transform: scale(.88) rotate(90deg); }

/* ===== Cards ===== */
.card {
  background: var(--bg-card); border-radius: 18px; padding: 16px; margin-bottom: 12px;
  box-shadow: var(--shadow); border: 1px solid var(--border-color);
}
.card-title { font-weight: 700; font-size: 15px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

.progress-bar { height: 10px; background: var(--accent-light); border-radius: 6px; overflow: hidden; }
.progress-fill { height: 100%; background: var(--accent); border-radius: 6px; transition: width .5s ease; }

.greeting { font-size: 20px; font-weight: 800; margin-bottom: 2px; }
.greeting-date { color: var(--text-secondary); font-size: 13px; margin-bottom: 16px; }

/* ===== Task Item ===== */
.task-item {
  display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 14px;
  background: var(--bg-elevated); margin-bottom: 8px; border: 1px solid var(--border-color);
  border-inline-start: 4px solid var(--task-color, var(--accent));
  transition: opacity .2s ease;
}
.task-item.done { opacity: .55; }
.task-item.done .task-title { text-decoration: line-through; }
.task-check {
  width: 26px; height: 26px; border-radius: 50%; border: 2px solid var(--task-color, var(--accent)); flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; color: transparent;
  transition: all .2s ease;
}
.task-check.checked { background: var(--task-color, var(--accent)); color: #fff; }
.task-body { flex: 1; min-width: 0; }
.task-title { font-weight: 600; font-size: 14px; }
.task-meta { font-size: 12px; color: var(--text-secondary); display: flex; gap: 8px; margin-top: 2px; flex-wrap: wrap; }
.task-icon {
  font-size: 18px; width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.chip {
  display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px;
  font-size: 11px; background: var(--accent-light); color: var(--accent); font-weight: 600;
}
.chip-recurring { background: #E8F5E9; color: #2E7D32; }

/* ===== Empty State ===== */
.empty-state { text-align: center; padding: 40px 20px; color: var(--text-secondary); }
.empty-state .emoji { font-size: 42px; margin-bottom: 10px; }

/* ===== Habit Card ===== */
.habit-card { display: flex; align-items: center; gap: 12px; }
.habit-circle {
  width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 24px; cursor: pointer; border: 3px solid var(--accent-light); transition: all .25s ease; flex-shrink: 0;
}
.habit-circle.done { background: var(--success); border-color: var(--success); color: #fff; transform: scale(1.05); }
.habit-info { flex: 1; }
.habit-title { font-weight: 700; font-size: 14px; }
.habit-sub { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
.streak-badge { font-size: 12px; font-weight: 700; color: var(--warning); }

/* ===== Habit Progress: Heatmap & Achievements ===== */
.habit-heatmap { display: flex; gap: 3px; overflow-x: auto; padding: 4px 2px 8px; direction: rtl; }
.habit-heatmap-col { display: flex; flex-direction: column; gap: 3px; flex-shrink: 0; }
.habit-heatmap-cell {
  width: 12px; height: 12px; border-radius: 3px; background: var(--bg-elevated); border: 1px solid var(--border-color);
}
.habit-heatmap-cell.done { background: var(--habit-color, var(--success)); border-color: var(--habit-color, var(--success)); }
.habit-achievements { display: flex; gap: 10px; flex-wrap: wrap; }
.habit-achievement {
  flex: 1; min-width: 70px; text-align: center; padding: 12px 6px; border-radius: 14px;
  background: var(--bg-elevated); border: 1px solid var(--border-color); opacity: .5;
}
.habit-achievement.unlocked { opacity: 1; background: var(--accent-light); border-color: var(--accent); }
.habit-achievement-icon { font-size: 22px; margin-bottom: 4px; }
.habit-achievement-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); }
.habit-achievement.unlocked .habit-achievement-label { color: var(--accent); }

/* ===== Note Card ===== */
.note-card { border-radius: 16px; padding: 14px; margin-bottom: 10px; position: relative; }
.note-title { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
.note-content { font-size: 13px; color: var(--text-primary); opacity: .85; white-space: pre-wrap; word-break: break-word; }
.note-pin { position: absolute; top: 10px, left: 10px; font-size: 14px; }
.note-attachment-badge {
  display: inline-flex; align-items: center; gap: 3px; margin-top: 8px; font-size: 11px;
  font-weight: 700; color: var(--accent); background: var(--accent-light); border-radius: 20px; padding: 3px 9px;
}

/* ===== Attachments ===== */
.attachment-item {
  display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 12px;
  background: var(--bg-elevated); border: 1px solid var(--border-color); margin-bottom: 8px;
}
.attachment-thumb-wrap { width: 40px; height: 40px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: var(--bg-card); }
.attachment-thumb { width: 100%; height: 100%; object-fit: cover; }
.attachment-icon {
  width: 40px; height: 40px; border-radius: 8px; background: var(--bg-card); flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.attachment-info { flex: 1; min-width: 0; }
.attachment-name { font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.attachment-size { font-size: 11px; color: var(--text-secondary); }
.attachment-actions { display: flex; gap: 4px; flex-shrink: 0; }
.lightbox-overlay {
  position: fixed; inset: 0; z-index: 600; background: rgba(0,0,0,.85);
  display: flex; align-items: center; justify-content: center; padding: 20px;
}
.lightbox-img { max-width: 100%; max-height: 100%; border-radius: 8px; }

/* ===== Forms ===== */
.form-group { margin-bottom: 14px; }
.form-label { font-size: 13px; font-weight: 600; margin-bottom: 6px; display: block; color: var(--text-secondary); }
.form-hint { font-size: 12px; color: var(--text-secondary); margin: -4px 0 12px; }
.form-input, .form-textarea, .form-select {
  width: 100%; padding: 12px 14px; border-radius: 12px; border: 1.5px solid var(--border-color);
  background: var(--bg-elevated); color: var(--text-primary); font-family: inherit; font-size: 14px;
  transition: border-color .2s ease;
}
.form-input:focus, .form-textarea:focus, .form-select:focus { outline: none; border-color: var(--accent); }
.form-textarea { min-height: 80px; resize: vertical; }
.form-row { display: flex; gap: 10px; }
.form-row > * { flex: 1; }

.btn {
  padding: 13px 20px; border-radius: 14px; border: none; font-family: inherit; font-weight: 700;
  font-size: 14px; cursor: pointer; transition: transform .15s ease, opacity .15s ease; width: 100%;
}
.btn:active { transform: scale(.97); }
.btn-primary { background: var(--accent); color: #fff; }
.btn-secondary { background: var(--accent-light); color: var(--accent); }
.btn-danger { background: #FFEAEA; color: var(--danger); }

.priority-selector { display: flex; gap: 6px; }
.priority-opt {
  flex: 1; padding: 8px; border-radius: 10px; text-align: center; font-size: 12px; cursor: pointer;
  border: 1.5px solid var(--border-color); background: var(--bg-elevated); transition: all .15s ease;
}
.priority-opt.selected { border-color: var(--accent); background: var(--accent-light); font-weight: 700; }

.icon-picker, .color-picker { display: flex; gap: 8px; flex-wrap: wrap; }
.icon-opt {
  width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
  font-size: 20px; background: var(--bg-elevated); border: 1.5px solid var(--border-color); cursor: pointer;
}
.icon-opt.selected { border-color: var(--accent); background: var(--accent-light); }
.color-opt { width: 32px; height: 32px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; }
.color-opt.selected { border-color: var(--text-primary); }

.weekday-picker { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 4px; }
.weekday-opt {
  width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700; background: var(--bg-elevated); border: 1.5px solid var(--border-color); cursor: pointer;
  transition: all .15s ease;
}
.weekday-opt.selected { border-color: var(--accent); background: var(--accent); color: #fff; }

/* ===== Modal / Sheet ===== */
.sheet-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 100;
  display: flex; align-items: flex-end; animation: fadeIn .2s ease;
}
.sheet {
  background: var(--bg-primary); width: 100%; border-radius: 22px 22px 0 0; padding: 20px 18px calc(20px + env(safe-area-inset-bottom));
  max-height: 88vh; overflow-y: auto; animation: slideUp .3s cubic-bezier(.32,.72,0,1);
}
@keyframes slideUp { from { transform: translateY(100%);} to { transform: translateY(0);} }
.sheet-handle { width: 40px; height: 5px; background: var(--border-color); border-radius: 3px; margin: 0 auto 16px; }
.sheet-title { font-size: 17px; font-weight: 800; margin-bottom: 16px; }

.confirm-box {
  position: fixed; inset: 0; z-index: 200; display: flex; align-items: center; justify-content: center;
  background: rgba(0,0,0,.45); padding: 20px; animation: fadeIn .15s ease;
}
.confirm-card { background: var(--bg-card); border-radius: 18px; padding: 20px; width: 100%; max-width: 320px; text-align: center; }
.confirm-actions { display: flex; gap: 10px; margin-top: 16px; }

/* ===== Toast ===== */
.toast-container { position: fixed; top: 14px; left: 0; right: 0; z-index: 300; display: flex; flex-direction: column; align-items: center; gap: 8px; pointer-events: none; }
.toast {
  background: var(--bg-card); color: var(--text-primary); padding: 12px 18px; border-radius: 14px;
  box-shadow: var(--shadow); font-size: 13px; font-weight: 600; animation: toastIn .25s ease; max-width: 90vw;
}
@keyframes toastIn { from { opacity: 0; transform: translateY(-14px);} to { opacity: 1; transform: translateY(0);} }

/* ===== Skeleton ===== */
.skeleton { background: linear-gradient(90deg, var(--border-color) 25%, var(--accent-light) 50%, var(--border-color) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 12px; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
.skeleton-line { height: 14px; margin-bottom: 8px; }
.skeleton-card { height: 70px; margin-bottom: 10px; }

/* ===== Calendar ===== */
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
.cal-dow { text-align: center; font-size: 11px; color: var(--text-secondary); font-weight: 700; padding: 4px 0; }
.cal-day {
  aspect-ratio: 1; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center;
  font-size: 12px; background: var(--bg-elevated); cursor: pointer; position: relative; border: 1px solid var(--border-color);
  gap: 2px; padding: 2px;
}
.cal-day.today { border-color: var(--accent); font-weight: 800; }
.cal-day.selected { background: var(--accent); color: #fff; }
.cal-day-num { line-height: 1; }
.cal-day-dots { display: flex; align-items: center; justify-content: center; gap: 2px; flex-wrap: nowrap; min-height: 6px; }
.cal-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.cal-dot-more { font-size: 8px; font-weight: 700; line-height: 1; color: inherit; opacity: .8; }
.cal-day.holiday { color: #E74C3C; }
.cal-day.selected.holiday { color: #fff; }
.cal-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.cal-nav-title { font-weight: 800; font-size: 14px; }
.cal-day-single { text-align: center; padding: 24px 0; }
.cal-day-single-num { font-size: 40px; font-weight: 800; }
.cal-day-single-label { font-size: 13px; color: var(--text-secondary); margin-top: 4px; }
.cal-day-single.holiday .cal-day-single-num { color: #E74C3C; }
.cal-day-single-event { margin-top: 8px; font-size: 13px; color: var(--accent); font-weight: 700; }
.cal-mini-item { font-size: 13px; padding: 6px 10px; background: var(--bg-elevated); border-radius: 10px; margin-bottom: 6px; border: 1px solid var(--border-color); }

/* ===== AI Assistant ===== */
.ai-chat-wrap { display: flex; flex-direction: column; height: calc(100vh - 210px); min-height: 360px; }
.ai-toolbar { display: flex; justify-content: flex-end; margin-bottom: 8px; }
.ai-messages { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 4px; padding: 4px 2px; }
.ai-bubble-row { display: flex; width: 100%; }
.ai-bubble-row.user { justify-content: flex-start; }
.ai-bubble-row.assistant { justify-content: flex-end; }
.ai-bubble {
  max-width: 78%; padding: 10px 14px; border-radius: 16px; font-size: 14px; line-height: 1.7;
  margin-bottom: 8px; word-break: break-word; white-space: pre-wrap;
}
.ai-bubble.user { background: var(--accent); color: #fff; border-bottom-left-radius: 4px; }
.ai-bubble.assistant { background: var(--bg-elevated); border: 1px solid var(--border-color); border-bottom-right-radius: 4px; }
.ai-bubble.ai-thinking { opacity: .7; font-style: italic; }
.ai-input-bar { display: flex; gap: 8px; padding-top: 8px; border-top: 1px solid var(--border-color); }
.ai-input-bar .form-input { flex: 1; margin: 0; }

/* ===== PIN Lock Screen ===== */
.pin-lock-overlay {
  position: fixed; inset: 0; z-index: 500; background: var(--bg-primary);
  display: flex; align-items: center; justify-content: center; padding: 24px;
}
.pin-lock-box { width: 100%; max-width: 320px; text-align: center; }
.pin-lock-icon { font-size: 48px; margin-bottom: 12px; }
.pin-lock-title { font-size: 15px; font-weight: 700; margin-bottom: 18px; }
.pin-lock-input {
  text-align: center; letter-spacing: 8px; font-size: 22px; margin-bottom: 10px;
}
.pin-lock-error { color: var(--danger); font-size: 13px; min-height: 18px; margin-bottom: 12px; }

/* ===== Support / Share Banner — پیش‌نمایش واقعیِ پیامی که ارسال می‌شود ===== */
.tg-banner-preview {
  border-radius: 16px; overflow: hidden; background: var(--bg-elevated); border: 1px solid var(--border-color);
}
.tg-banner-img-wrap { width: 100%; aspect-ratio: 1; background: var(--bg-card); }
.tg-banner-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.tg-banner-caption { padding: 14px 16px; font-size: 13px; line-height: 1.9; white-space: pre-wrap; }
.tg-banner-caption b { font-size: 14.5px; }
.tg-banner-button {
  margin: 0 12px 14px; padding: 12px; text-align: center; border-radius: 10px;
  background: var(--accent-light); color: var(--accent); font-weight: 700; font-size: 13px; cursor: default;
}
.support-share-btn {
  background: var(--accent); color: #fff; font-weight: 800; font-size: 15px;
  padding: 14px; border-radius: 14px; box-shadow: 0 6px 16px rgba(0,0,0,.15);
}

.tabs { display: flex; gap: 6px; background: var(--bg-elevated); padding: 4px; border-radius: 14px; margin-bottom: 14px; }
.tab-btn { flex: 1; padding: 9px; border: none; background: transparent; border-radius: 10px; font-size: 12px; font-weight: 700; color: var(--text-secondary); cursor: pointer; }
.tab-btn.active { background: var(--accent); color: #fff; }

.search-input-wrap { position: relative; margin-bottom: 14px; }
.stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
.stat-box { background: var(--bg-card); border-radius: 16px; padding: 14px; box-shadow: var(--shadow); }
.stat-num { font-size: 22px; font-weight: 800; color: var(--accent); }
.stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

.theme-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.theme-swatch { border-radius: 14px; padding: 14px 8px; text-align: center; cursor: pointer; border: 2px solid var(--border-color); font-size: 12px; font-weight: 700; }
.theme-swatch.active { border-color: var(--accent); }
`;

// ---- JS ----
const JS_TEXT = `
(function () {
'use strict';

const tg = window.Telegram?.WebApp;
if (tg) { tg.ready(); tg.expand(); }

const state = {
  page: 'dashboard',
  theme: localStorage.getItem('lm_theme') || 'light',
  lang: localStorage.getItem('lm_lang') || 'fa',
  calendarType: localStorage.getItem('lm_caltype') || 'jalali',
  data: {},
  categories: [],
  calendarView: 'month',
  calendarDate: todayStr(),
};

// ===== سیستم چندزبانه (i18n) =====
// همهٔ متن‌های ثابت رابط کاربری باید از این دیکشنری با t(key) خوانده شوند تا:
// ۱) اضافه‌کردن زبان جدید فقط یعنی اضافه‌کردن یک بلوک دیگر به I18N
// ۲) تغییر زبان بدون نیاز به ری‌استارت، فقط با رندر دوبارهٔ صفحهٔ جاری انجام شود
const I18N = {
  fa: {
    'nav.dashboard': '🏠 خانه', 'nav.tasks': '📌 کارها', 'nav.habits': '🔥 عادت‌ها',
    'nav.calendar': '📅 تقویم', 'nav.notes': '📝 یادداشت‌ها', 'nav.search': '🔍 جستجو',
    'nav.analytics': '📈 آمار', 'nav.settings': '⚙️ تنظیمات', 'nav.assistant': '🤖 دستیار هوش مصنوعی',
    'assistant.placeholder': 'پیامت را بنویس... (مثلاً: برای فردا چطور برنامه‌ریزی کنم؟)',
    'assistant.thinking': '🤖 در حال فکر کردن...',
    'assistant.emptyState': 'سلام! من دستیار ToDoList هستم 🤖\\nدربارهٔ برنامه‌ریزی، مدیریت کارها، عادت‌ها و بهره‌وری هرچی بخوای می‌تونم کمکت کنم.',
    'assistant.clearConfirm': 'تاریخچهٔ گفتگو پاک شود؟',
    'assistant.send': 'ارسال', 'assistant.clear': '🗑️ پاک کردن گفتگو',
    'assistant.error': 'مشکلی در ارتباط با دستیار پیش آمد. دوباره تلاش کن.',
    'nav.tasks.short': 'کارها', 'nav.habits.short': 'عادت‌ها', 'nav.calendar.short': 'تقویم', 'nav.notes.short': 'یادداشت', 'nav.dashboard.short': 'خانه',
    'common.save': '💾 ذخیره', 'common.cancel': 'انصراف', 'common.delete': 'حذف', 'common.edit': 'ویرایش',
    'common.error': 'خطا', 'common.unknownError': 'خطای نامشخص', 'common.parseError': 'خطای پارس پاسخ',
    'common.saved': '✅ ذخیره شد', 'common.deleted': '🗑️ حذف شد', 'common.updated': '✅ به‌روزرسانی شد', 'common.added': '✅ اضافه شد',
    'confirm.deleteTask': 'این کار حذف شود؟', 'confirm.deleteNote': 'این یادداشت حذف شود؟', 'confirm.deleteHabit': 'این عادت حذف شود؟',
    'greeting.hello': 'سلام 👋',
    'dashboard.todayProgress': '📊 پیشرفت امروز',
    'dashboard.doneOf': 'کار انجام شد',
    'dashboard.habitsToday': '🔥 عادت‌های امروز',
    'dashboard.pinnedNotes': '📝 یادداشت سریع',
    'dashboard.todayTasks': '📌 کارهای امروز',
    'dashboard.noHabitsYet': 'هنوز عادتی ثبت نکرده‌اید',
    'dashboard.noPinnedNotes': 'یادداشت پین‌شده‌ای ندارید',
    'dashboard.noTasksToday': 'کاری برای امروز ثبت نشده',
    'notes.untitled': 'بدون‌عنوان',
    'notes.attachments': '📎 پیوست‌ها', 'notes.addAttachment': '📎 افزودن فایل/تصویر',
    'notes.attachmentLoadError': 'بارگذاری فایل با خطا مواجه شد',
    'notes.attachmentUploadError': 'آپلود فایل با خطا مواجه شد', 'notes.attachmentUploading': '⏳ در حال آپلود...',
    'notes.attachmentDeleteConfirm': 'این فایل حذف شود؟', 'notes.attachmentTooLarge': 'حجم فایل نباید بیشتر از ۲۰ مگابایت باشد',
    'notes.download': '⬇️ دانلود', 'notes.view': '👁️ مشاهده',
    'empty.tasks': 'کاری برای امروز ندارید', 'empty.habits': 'هنوز عادتی اضافه نکرده‌اید',
    'empty.notes': 'یادداشتی ندارید', 'empty.calendarDay': 'برنامه‌ای برای این روز نیست',
    'empty.search': 'نتیجه‌ای یافت نشد', 'empty.chartData': 'داده‌ای موجود نیست',
    'tasks.title': 'عنوان کار الزامی است', 'notes.contentRequired': '⚠️ متن یادداشت الزامی است',
    'tasks.repeatWeekly': '🔁 تکرار هفتگی (اختیاری)',
    'tasks.repeatWeeklyHint': 'روزهایی که این کار باید خودکار تکرار شود را انتخاب کن',
    'tasks.repeatWeeklyEditHint': 'تغییر این روزها روی کل سریِ تکرار (نمونه‌های آینده) اعمال می‌شود؛ روزهای گذشته دست‌نخورده می‌مانند',
    'tasks.recurringBadge': '🔁 تکرارشونده',
    'tasks.enableReminder': 'یادآوری فعال باشد',
    'habits.titleRequired': '⚠️ عنوان الزامی است',
    'habits.currentStreak': 'روز متوالی', 'habits.bestStreak': 'بهترین رکورد', 'habits.successRate': 'درصد موفقیت',
    'habits.last90days': '📅 ۹۱ روز اخیر', 'habits.achievements': '🏅 نشان‌های افتخار',
    'habits.days': 'روز', 'habits.logToday': 'ثبت عادت امروز',
    'calendar.month': 'ماهانه', 'calendar.week': 'هفتگی', 'calendar.day': 'روزانه',
    'calendar.eventsThisMonth': '🎉 مناسبت‌های این ماه', 'calendar.holiday': 'تعطیل',
    'calendar.schedule': '📌 برنامه‌ها',
    'search.placeholder': '🔍 جستجو در کارها، یادداشت‌ها و عادت‌ها...',
    'search.tasks': '📌 کارها', 'search.notes': '📝 یادداشت‌ها', 'search.habits': '🔥 عادت‌ها',
    'analytics.total': '📌 کل کارها', 'analytics.completed': '✅ انجام‌شده',
    'analytics.successRate': '📈 درصد موفقیت', 'analytics.longestStreak': '🔥 طولانی‌ترین Streak',
    'analytics.weeklyChart': '📊 نمودار هفتگی', 'analytics.topCategory': '🏆 پرکارترین دسته',
    'settings.theme': '🎨 تم', 'settings.language': '🌐 زبان', 'settings.stats': '📈 آمار',
    'settings.calendarType': '📅 نوع تقویم', 'settings.calendarJalali': '📆 شمسی', 'settings.calendarGregorian': '📅 میلادی',
    'support.title': '💛 حمایت از ربات',
    'support.settingsDesc': 'اگه از ToDoList راضی بودی، معرفیش به دوستات بزرگ‌ترین کمکیه که می‌تونی بکنی.',
    'support.openButton': '📤 معرفی به دوستان',
    'support.shareButton': '📤 ارسال بنر برای اشتراک‌گذاری',
    'support.shareHint': 'این بنر در چتِ شما با ربات ارسال می‌شود؛ کافیه آن را برای دوستان یا گروه‌هایتان Forward کنید.',
    'support.loadingPreview': 'در حال بارگذاری پیش‌نمایش...',
    'support.previewError': 'پیش‌نمایش بارگذاری نشد',
    'support.inlineButtonLabel': 'شروع استفاده از ربات',
    'support.sending': '⏳ در حال ارسال...',
    'support.sentSuccess': 'بنر در چت شما ارسال شد! برو Forwardش کن 🚀',
    'security.pinLock': '🔒 قفل امنیتی (PIN)',
    'security.pinLockDesc': 'با فعال کردن رمز، هربار قبل از ورود به اپ، رمز عبورت پرسیده می‌شود.',
    'security.setPin': '🔒 تنظیم رمز', 'security.changePin': '🔄 تغییر رمز', 'security.removePin': '🗑️ حذف رمز',
    'security.newPin': 'رمز جدید', 'security.confirmPin': 'تکرار رمز جدید', 'security.currentPin': 'رمز فعلی',
    'security.pinFormatHint': 'رمز باید بین ۴ تا ۸ رقم عددی باشد',
    'security.pinMismatch': 'رمزهای وارد‌شده یکسان نیستند',
    'security.pinSetSuccess': 'رمز با موفقیت تنظیم شد', 'security.pinChangeSuccess': 'رمز با موفقیت تغییر کرد',
    'security.pinRemoveSuccess': 'رمز حذف شد', 'security.removeConfirmHint': 'با حذف رمز، قفل امنیتی اپ کاملاً غیرفعال می‌شود. مطمئنی؟',
    'security.enterPin': '🔒 برای ورود، رمز خود را وارد کن', 'security.unlock': 'ورود',
    'security.wrongPin': 'رمز اشتباه است', 'security.wrongPinAttempts': 'رمز اشتباه است — {n} تلاش باقی‌مانده',
    'security.locked': 'به‌دلیل تلاش‌های ناموفق زیاد، {sec} ثانیه دیگر صبر کن',
    'settings.viewFullStats': 'مشاهده آمار کامل', 'settings.about': 'ℹ️ درباره',
    'settings.aboutText': 'ToDoList — مدیریت هوشمند کارها و زندگی روزمره شما 🎯',
    'auth.invalid': 'احراز هویت نامعتبر است',
  },
  en: {
    'nav.dashboard': '🏠 Home', 'nav.tasks': '📌 Tasks', 'nav.habits': '🔥 Habits',
    'nav.calendar': '📅 Calendar', 'nav.notes': '📝 Notes', 'nav.search': '🔍 Search',
    'nav.analytics': '📈 Analytics', 'nav.settings': '⚙️ Settings', 'nav.assistant': '🤖 AI Assistant',
    'assistant.placeholder': 'Type your message... (e.g. how should I plan tomorrow?)',
    'assistant.thinking': '🤖 Thinking...',
    'assistant.emptyState': 'Hi! I\\'m the ToDoList assistant 🤖\\nAsk me anything about planning, tasks, habits and productivity.',
    'assistant.clearConfirm': 'Clear the whole conversation?',
    'assistant.send': 'Send', 'assistant.clear': '🗑️ Clear conversation',
    'assistant.error': 'Something went wrong talking to the assistant. Please try again.',
    'nav.tasks.short': 'Tasks', 'nav.habits.short': 'Habits', 'nav.calendar.short': 'Calendar', 'nav.notes.short': 'Notes', 'nav.dashboard.short': 'Home',
    'common.save': '💾 Save', 'common.cancel': 'Cancel', 'common.delete': 'Delete', 'common.edit': 'Edit',
    'common.error': 'Error', 'common.unknownError': 'Unknown error', 'common.parseError': 'Response parse error',
    'common.saved': '✅ Saved', 'common.deleted': '🗑️ Deleted', 'common.updated': '✅ Updated', 'common.added': '✅ Added',
    'confirm.deleteTask': 'Delete this task?', 'confirm.deleteNote': 'Delete this note?', 'confirm.deleteHabit': 'Delete this habit?',
    'greeting.hello': 'Hello 👋',
    'dashboard.todayProgress': '📊 Today\\'s progress',
    'dashboard.doneOf': 'tasks done',
    'dashboard.habitsToday': '🔥 Today\\'s habits',
    'dashboard.pinnedNotes': '📝 Quick note',
    'dashboard.todayTasks': '📌 Today\\'s tasks',
    'dashboard.noHabitsYet': 'No habits logged yet',
    'dashboard.noPinnedNotes': 'No pinned notes',
    'dashboard.noTasksToday': 'No tasks for today',
    'notes.untitled': 'Untitled',
    'notes.attachments': '📎 Attachments', 'notes.addAttachment': '📎 Add file/image',
    'notes.attachmentLoadError': 'Failed to load the file',
    'notes.attachmentUploadError': 'Failed to upload the file', 'notes.attachmentUploading': '⏳ Uploading...',
    'notes.attachmentDeleteConfirm': 'Delete this file?', 'notes.attachmentTooLarge': 'File size must not exceed 20 MB',
    'notes.download': '⬇️ Download', 'notes.view': '👁️ View',
    'empty.tasks': 'No tasks for today', 'empty.habits': 'No habits added yet',
    'empty.notes': 'No notes yet', 'empty.calendarDay': 'Nothing scheduled for this day',
    'empty.search': 'No results found', 'empty.chartData': 'No data available',
    'tasks.title': 'Task title is required', 'notes.contentRequired': '⚠️ Note content is required',
    'tasks.repeatWeekly': '🔁 Weekly repeat (optional)',
    'tasks.repeatWeeklyHint': 'Pick the days this task should automatically repeat on',
    'tasks.repeatWeeklyEditHint': "Changing these days applies to the whole recurring series (future occurrences); past days are left untouched",
    'tasks.recurringBadge': '🔁 Recurring',
    'tasks.enableReminder': 'Enable reminder',
    'habits.titleRequired': '⚠️ Title is required',
    'habits.currentStreak': 'Current streak', 'habits.bestStreak': 'Best streak', 'habits.successRate': 'Success rate',
    'habits.last90days': '📅 Last 91 days', 'habits.achievements': '🏅 Achievements',
    'habits.days': 'days', 'habits.logToday': 'Log today',
    'calendar.month': 'Month', 'calendar.week': 'Week', 'calendar.day': 'Day',
    'calendar.eventsThisMonth': '🎉 Events this month', 'calendar.holiday': 'Holiday',
    'calendar.schedule': '📌 Schedule',
    'search.placeholder': '🔍 Search tasks, notes and habits...',
    'search.tasks': '📌 Tasks', 'search.notes': '📝 Notes', 'search.habits': '🔥 Habits',
    'analytics.total': '📌 Total tasks', 'analytics.completed': '✅ Completed',
    'analytics.successRate': '📈 Success rate', 'analytics.longestStreak': '🔥 Longest streak',
    'analytics.weeklyChart': '📊 Weekly chart', 'analytics.topCategory': '🏆 Top category',
    'settings.theme': '🎨 Theme', 'settings.language': '🌐 Language', 'settings.stats': '📈 Stats',
    'settings.calendarType': '📅 Calendar type', 'settings.calendarJalali': '📆 Jalali (Persian)', 'settings.calendarGregorian': '📅 Gregorian',
    'support.title': '💛 Support the bot',
    'support.settingsDesc': "If you like ToDoList, sharing it with friends is the biggest help you can give.",
    'support.openButton': '📤 Tell a friend',
    'support.shareButton': '📤 Send banner to share',
    'support.shareHint': "This banner will be sent to your chat with the bot — just forward it to friends or groups.",
    'support.loadingPreview': 'Loading preview...',
    'support.previewError': 'Preview failed to load',
    'support.inlineButtonLabel': 'Start using the bot',
    'support.sending': '⏳ Sending...',
    'support.sentSuccess': 'Banner sent to your chat! Go forward it 🚀',
    'security.pinLock': '🔒 Security PIN lock',
    'security.pinLockDesc': 'When enabled, you\\'ll be asked for your PIN every time before entering the app.',
    'security.setPin': '🔒 Set PIN', 'security.changePin': '🔄 Change PIN', 'security.removePin': '🗑️ Remove PIN',
    'security.newPin': 'New PIN', 'security.confirmPin': 'Confirm new PIN', 'security.currentPin': 'Current PIN',
    'security.pinFormatHint': 'PIN must be 4 to 8 digits',
    'security.pinMismatch': 'The PINs entered do not match',
    'security.pinSetSuccess': 'PIN set successfully', 'security.pinChangeSuccess': 'PIN changed successfully',
    'security.pinRemoveSuccess': 'PIN removed', 'security.removeConfirmHint': 'Removing the PIN will fully disable the app lock. Are you sure?',
    'security.enterPin': '🔒 Enter your PIN to continue', 'security.unlock': 'Unlock',
    'security.wrongPin': 'Incorrect PIN', 'security.wrongPinAttempts': 'Incorrect PIN — {n} attempts left',
    'security.locked': 'Too many failed attempts. Try again in {sec}s',
    'settings.viewFullStats': 'View full stats', 'settings.about': 'ℹ️ About',
    'settings.aboutText': 'ToDoList — smart management of your tasks and daily life 🎯',
    'auth.invalid': 'Invalid authentication',
  },
};
function t(key) { return (I18N[state.lang] && I18N[state.lang][key]) || I18N.fa[key] || key; }
function setLang(lang) {
  state.lang = lang;
  localStorage.setItem('lm_lang', lang);
  document.documentElement.setAttribute('lang', lang === 'en' ? 'en' : 'fa');
  document.documentElement.setAttribute('dir', lang === 'en' ? 'ltr' : 'rtl');
  document.body.dir = lang === 'en' ? 'ltr' : 'rtl';
  applyStaticI18n();
}
function applyStaticI18n() {
  document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.dataset.i18n); });
  document.querySelectorAll('[data-i18n-ph]').forEach(el => { el.placeholder = t(el.dataset.i18nPh); });
  const titleEl = document.getElementById('pageTitle');
  if (titleEl) titleEl.textContent = t('nav.' + state.page);
}

const THEMES = [
  { id: 'light', label: '☀️ روشن' }, { id: 'dark', label: '🌙 تیره' },
  { id: 'blue', label: '🔵 آبی' }, { id: 'green', label: '🟢 سبز' },
  { id: 'purple', label: '🟣 بنفش' }, { id: 'orange', label: '🟠 نارنجی' },
];

function todayStr() {
  const d = new Date();
  return d.toISOString().slice(0, 10);
}

// این تابع در نسخهٔ قبلی فقط سمت سرور تعریف شده بود؛ چون توابع تقویمِ سمت کلاینت
// (renderCalendar/renderCalGrid/jalaliMonthFirstDayGregorian و...) به آن نیاز داشتند
// ولی در scope مرورگر تعریف نشده بود، هر بار که کاربر تب «تقویم» را باز می‌کرد
// یک ReferenceError رخ می‌داد و کل صفحه با پیام خطا جایگزین می‌شد — یعنی از دید
// کاربر «تقویم اصلاً باز نمی‌شد». همین تابع اکنون اینجا (سمت کلاینت) هم تعریف شده است.
function addDaysToDateStr(dateStr, days) {
  const [y, m, d] = dateStr.split('-').map(Number);
  const dt = new Date(Date.UTC(y, m - 1, d));
  dt.setUTCDate(dt.getUTCDate() + days);
  return dt.toISOString().slice(0, 10);
}

function haptic(type) {
  try { tg?.HapticFeedback?.impactOccurred(type || 'light'); } catch (e) {}
}

// ===== API Wrapper =====
async function api(path, options = {}) {
  const initData = tg?.initData || '';
  const res = await fetch('/api/v1' + path, {
    method: options.method || 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'tma ' + initData,
    },
    body: options.body ? JSON.stringify(options.body) : undefined,
  });
  const json = await res.json().catch(() => ({ ok: false, error: t('common.parseError') }));
  if (!res.ok || !json.ok) {
    const err = new Error(json.error || t('common.unknownError'));
    err.data = json.data; err.status = res.status;
    throw err;
  }
  return json.data;
}

// آپلود فایل به‌صورت multipart/form-data — عمداً از api() جدا شده چون آن تابع
// همیشه Content-Type را روی application/json می‌گذارد که برای آپلود فایل نامناسب است.
async function apiUpload(path, file) {
  const initData = tg?.initData || '';
  const form = new FormData();
  form.append('file', file, file.name);
  const res = await fetch('/api/v1' + path, {
    method: 'POST',
    headers: { 'Authorization': 'tma ' + initData }, // Content-Type را مرورگر خودش با boundary درست تنظیم می‌کند
    body: form,
  });
  const json = await res.json().catch(() => ({ ok: false, error: t('common.parseError') }));
  if (!res.ok || !json.ok) throw new Error(json.error || t('common.unknownError'));
  return json.data;
}

// دریافت فایل باینری (تصویر/سند) با هدر Authorization — چون این فایل‌ها private
// هستند و نمی‌شود مستقیم در <img src> بدون auth بارگذاری‌شان کرد.
async function apiDownloadBlob(path) {
  const initData = tg?.initData || '';
  const res = await fetch('/api/v1' + path, { headers: { 'Authorization': 'tma ' + initData } });
  if (!res.ok) throw new Error(t('notes.attachmentLoadError'));
  return res.blob();
}

// ===== Toast =====
function toast(msg) {
  const c = document.getElementById('toastContainer');
  const el = document.createElement('div');
  el.className = 'toast';
  el.textContent = msg;
  c.appendChild(el);
  setTimeout(() => el.remove(), 2600);
}

// ===== Confirm Dialog =====
function confirmDialog(message) {
  return new Promise((resolve) => {
    const root = document.getElementById('modalRoot');
    root.innerHTML = \`
      <div class="confirm-box" id="confirmBox">
        <div class="confirm-card">
          <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
          <div style="font-weight:700;">\${message}</div>
          <div class="confirm-actions">
            <button class="btn btn-secondary" id="cnCancel">\${t('common.cancel')}</button>
            <button class="btn btn-danger" id="cnOk">\${t('common.delete')}</button>
          </div>
        </div>
      </div>\`;
    document.getElementById('cnCancel').onclick = () => { root.innerHTML = ''; resolve(false); };
    document.getElementById('cnOk').onclick = () => { root.innerHTML = ''; resolve(true); };
  });
}

// ===== Bottom Sheet =====
function openSheet(title, bodyHtml) {
  const root = document.getElementById('modalRoot');
  root.innerHTML = \`
    <div class="sheet-overlay" id="sheetOverlay">
      <div class="sheet">
        <div class="sheet-handle"></div>
        <div class="sheet-title">\${title}</div>
        <div id="sheetBody">\${bodyHtml}</div>
      </div>
    </div>\`;
  document.getElementById('sheetOverlay').addEventListener('click', (e) => {
    if (e.target.id === 'sheetOverlay') closeSheet();
  });
}
function closeSheet() { document.getElementById('modalRoot').innerHTML = ''; }

// ===== Skeleton =====
function skeletonCards(n) {
  return Array.from({ length: n }).map(() => '<div class="skeleton skeleton-card"></div>').join('');
}

// ===== Theme =====
function applyTheme(themeId) {
  document.body.className = 'theme-' + themeId;
  state.theme = themeId;
  localStorage.setItem('lm_theme', themeId);
  if (tg) {
    const bg = getComputedStyle(document.body).getPropertyValue('--bg-primary').trim();
    try { tg.setHeaderColor(bg); tg.setBackgroundColor(bg); } catch (e) {}
  }
}

// ===== Router =====
async function navigate(page) {
  state.page = page;
  document.getElementById('pageTitle').textContent = t('nav.' + page) || '';
  document.querySelectorAll('.nav-item').forEach(el => el.classList.toggle('active', el.dataset.page === page));
  document.getElementById('fab').style.display = ['dashboard','tasks','habits','notes'].includes(page) ? 'flex' : 'none';
  haptic('light');
  const main = document.getElementById('mainContent');
  main.innerHTML = skeletonCards(3);
  try {
    if (page === 'dashboard') await renderDashboard();
    else if (page === 'tasks') await renderTasks();
    else if (page === 'habits') await renderHabits();
    else if (page === 'calendar') await renderCalendar();
    else if (page === 'notes') await renderNotes();
    else if (page === 'search') await renderSearch();
    else if (page === 'analytics') await renderAnalytics();
    else if (page === 'settings') await renderSettings();
    else if (page === 'assistant') await renderAssistant();
  } catch (err) {
    main.innerHTML = \`<div class="empty-state"><div class="emoji">😕</div>\${t('common.error')}: \${err.message}</div>\`;
  }
}

// ===== Dashboard =====
async function renderDashboard() {
  const [d, cats] = await Promise.all([api('/dashboard'), loadCategories()]);
  state.data.dashboard = d;
  const main = document.getElementById('mainContent');
  const dateLocale = new Intl.DateTimeFormat(state.lang === 'en' ? 'en-US' : 'fa-IR', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date());

  main.innerHTML = \`
    <div class="greeting">\${t('greeting.hello')}</div>
    <div class="greeting-date">\${dateLocale}</div>

    <div class="card">
      <div class="card-title">\${t('dashboard.todayProgress')}</div>
      <div class="progress-bar"><div class="progress-fill" style="width:\${d.progress_percent}%"></div></div>
      <div style="margin-top:8px;font-size:13px;color:var(--text-secondary);">\${d.completed_tasks} \${state.lang==='en'?'of':'از'} \${d.total_tasks} \${t('dashboard.doneOf')} (\${d.progress_percent}%)</div>
    </div>

    \${d.next_task ? \`
    <div class="card">
      <div class="card-title">⏭️ کار بعدی</div>
      <div class="task-item" style="border:none;background:var(--accent-light);">
        <div class="task-icon">\${d.next_task.icon}</div>
        <div class="task-body">
          <div class="task-title">\${escapeHtml(d.next_task.title)}</div>
          <div class="task-meta">\${d.next_task.task_time ? '⏰ ' + d.next_task.task_time : ''}</div>
        </div>
      </div>
    </div>\` : ''}

    <div class="card">
      <div class="card-title">\${t('dashboard.habitsToday')}</div>
      \${d.habits_today.length === 0 ? '<div class="empty-state" style="padding:16px;">هنوز عادتی ثبت نکرده‌اید</div>' :
        d.habits_today.map(h => habitRowHtml(h)).join('')}
    </div>

    <div class="card">
      <div class="card-title">\${t('dashboard.pinnedNotes')}</div>
      \${d.pinned_notes.length === 0 ? '<div class="empty-state" style="padding:16px;">' + t('dashboard.noPinnedNotes') + '</div>' :
        d.pinned_notes.map(n => \`<div class="note-card" style="background:\${n.color}22;border:1px solid \${n.color}55;">
          <div class="note-title">📌 \${escapeHtml(n.title || t('notes.untitled'))}</div>
          <div class="note-content">\${escapeHtml(n.content).slice(0,80)}</div>
        </div>\`).join('')}
    </div>

    <div class="card">
      <div class="card-title">\${t('dashboard.todayTasks')}</div>
      \${d.tasks.length === 0 ? emptyState('📭', t('dashboard.noTasksToday')) : d.tasks.map(t => taskItemHtml(t)).join('')}
    </div>
  \`;
  bindTaskEvents();
  bindHabitEvents();
}

function habitRowHtml(h) {
  return \`
    <div class="habit-card" style="margin-bottom:10px;">
      <div class="habit-circle \${h.done_today ? 'done' : ''}" data-habit-log="\${h.id}">\${h.icon}</div>
      <div class="habit-info">
        <div class="habit-title">\${escapeHtml(h.title)}</div>
        <div class="habit-sub">🔥 \${h.streak} روز متوالی · \${h.success_rate}% موفقیت</div>
      </div>
      \${h.streak >= 3 ? '<div class="streak-badge">🔥' + h.streak + '</div>' : ''}
    </div>\`;
}

function taskItemHtml(task) {
  const c = task.color || '#6C5CE7';
  return \`
    <div class="task-item \${task.is_completed ? 'done' : ''}" data-task-id="\${task.id}" style="--task-color:\${c};">
      <div class="task-check \${task.is_completed ? 'checked' : ''}" data-toggle="\${task.id}">✓</div>
      <div class="task-icon" style="background:\${c}22;">\${task.icon}</div>
      <div class="task-body">
        <div class="task-title">\${escapeHtml(task.title)}</div>
        <div class="task-meta">
          \${task.task_time ? '<span>⏰ ' + task.task_time + '</span>' : ''}
          <span class="chip">\${priorityLabel(task.priority)}</span>
          \${task.recurrence_type ? '<span class="chip chip-recurring">' + t('tasks.recurringBadge') + '</span>' : ''}
        </div>
      </div>
      <button class="icon-btn" data-edit="\${task.id}" style="width:32px;height:32px;font-size:14px;">✏️</button>
    </div>\`;
}

function priorityLabel(p) {
  return { low: '🔵 کم', normal: '🟢 عادی', high: '🟠 بالا', urgent: '🔴 فوری' }[p] || p;
}

function emptyState(emoji, text) {
  return \`<div class="empty-state"><div class="emoji">\${emoji}</div>\${text}</div>\`;
}

function escapeHtml(s) {
  if (!s) return '';
  return s.replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
}

function bindTaskEvents() {
  document.querySelectorAll('[data-toggle]').forEach(el => {
    el.onclick = async (e) => {
      e.stopPropagation();
      haptic('medium');
      const id = el.dataset.toggle;
      try {
        await api('/tasks/' + id + '/toggle', { method: 'PATCH' });
        toast('✅ وضعیت کار به‌روزرسانی شد');
        navigate(state.page);
      } catch (err) { toast('❌ ' + err.message); }
    };
  });
  document.querySelectorAll('[data-edit]').forEach(el => {
    el.onclick = async (e) => { e.stopPropagation(); openTaskForm(el.dataset.edit); };
  });
}

const STREAK_MILESTONES = [3, 7, 30, 100];
function encouragementFor(streak) {
  if (streak >= 100) return '🏆 صد روز متوالی! افسانه‌ای هستی!';
  if (streak >= 30) return '🌟 یک ماه کامل! فوق‌العاده‌ای!';
  if (streak >= 7) return '💪 یک هفته متوالی! همینطور ادامه بده!';
  if (streak >= 3) return '🔥 سه روز متوالی! خوب شروع کردی!';
  return '✅ عادت امروز ثبت شد!';
}

function bindHabitEvents() {
  document.querySelectorAll('[data-habit-log]').forEach(el => {
    el.onclick = async () => {
      if (el.classList.contains('done')) { toast('✅ عادت امروز قبلاً ثبت شده'); return; } // فقط یک بار در روز
      haptic('medium');
      try {
        const h = await api('/habits/' + el.dataset.habitLog + '/log', { method: 'POST' });
        toast(STREAK_MILESTONES.includes(h.streak) ? encouragementFor(h.streak) : ('🔥 ثبت شد! ' + h.streak + ' روز متوالی'));
        navigate(state.page);
      } catch (err) { toast('❌ ' + err.message); }
    };
  });
  document.querySelectorAll('[data-habit-progress]').forEach(el => {
    el.onclick = () => openHabitProgress(el.dataset.habitProgress);
  });
  document.querySelectorAll('[data-habit-edit]').forEach(el => {
    el.onclick = (e) => { e.stopPropagation(); openHabitForm(el.dataset.habitEdit); };
  });
}

async function loadCategories() {
  if (state.categories.length === 0) state.categories = await api('/categories');
  return state.categories;
}
`;

// ---- JS part 2: Tasks, Task Form ----
const JS_TEXT_2 = `
let taskFilter = { status: 'all' };

async function renderTasks() {
  const [tasks, cats] = await Promise.all([api('/tasks?date=' + todayStr()), loadCategories()]);
  state.data.tasks = tasks;
  const main = document.getElementById('mainContent');
  const filtered = taskFilter.status === 'all' ? tasks : tasks.filter(t => taskFilter.status === 'done' ? t.is_completed : !t.is_completed);
  main.innerHTML = \`
    <div class="tabs">
      <button class="tab-btn \${taskFilter.status==='all'?'active':''}" data-filter="all">همه</button>
      <button class="tab-btn \${taskFilter.status==='pending'?'active':''}" data-filter="pending">در انتظار</button>
      <button class="tab-btn \${taskFilter.status==='done'?'active':''}" data-filter="done">انجام‌شده</button>
    </div>
    <div class="card-title" style="margin:4px 4px 10px;">📌 کارهای امروز</div>
    \${filtered.length === 0 ? emptyState('📭','کاری یافت نشد') : filtered.map(t => taskItemHtml(t)).join('')}
  \`;
  bindTaskEvents();
  document.querySelectorAll('[data-filter]').forEach(el => {
    el.onclick = () => { taskFilter.status = el.dataset.filter; renderTasks(); };
  });
}

const ICONS = ['📝','📚','💻','🏋️','🛒','🍎','💊','✈️','🎯','📞','💰','🎨'];
const COLORS = ['#6C5CE7','#0984E3','#00B894','#FDCB6E','#E17055','#FF6B6B','#A55EEA','#2D3436'];

// نام کوتاه روزهای هفته برای انتخاب‌گر تکرار — ایندکس مطابق قرارداد ۰=یکشنبه...۶=شنبه
// (همان قراردادی که سمت سرور هم برای recurrence_days استفاده می‌کند)
const WEEKDAY_SHORT = {
  fa: ['ی', 'د', 'س', 'چ', 'پ', 'ج', 'ش'],
  en: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
};
// ترتیب نمایش: برای فارسی از شنبه شروع می‌شود (هفتهٔ ایرانی)، برای انگلیسی از یکشنبه
function weekdayDisplayOrder() {
  return state.lang === 'fa' ? [6, 0, 1, 2, 3, 4, 5] : [0, 1, 2, 3, 4, 5, 6];
}
const REMINDER_OFFSET_OPTIONS_FA = [
  [0, 'دقیقاً سر وقت'], [-10, '۱۰ دقیقه قبل'], [-30, '۳۰ دقیقه قبل'], [-60, '۱ ساعت قبل'], [-1440, '۱ روز قبل'],
];
const REMINDER_OFFSET_OPTIONS_EN = [
  [0, 'Exactly on time'], [-10, '10 minutes before'], [-30, '30 minutes before'], [-60, '1 hour before'], [-1440, '1 day before'],
];
function reminderOffsetOptions() {
  return state.lang === 'fa' ? REMINDER_OFFSET_OPTIONS_FA : REMINDER_OFFSET_OPTIONS_EN;
}

async function openTaskForm(taskId) {
  const cats = await loadCategories();
  let task = { title:'', description:'', task_date: todayStr(), task_time:'', priority:'normal', icon:'📝', color:'#6C5CE7', category_id: cats[0]?.id, recurrence_days: null };
  if (taskId) {
    try { task = await api('/tasks/' + taskId); }
    catch (err) { toast('❌ ' + err.message); return; }
  }
  const existingRecurrenceDays = parseRecurrenceDaysClient(task.recurrence_days);
  const catOptions = cats.map(c => \`<option value="\${c.id}" \${task.category_id===c.id?'selected':''}>\${c.icon} \${c.name}</option>\`).join('');
  const priorities = [['low','🔵 کم'],['normal','🟢 عادی'],['high','🟠 بالا'],['urgent','🔴 فوری']];

  openSheet(taskId ? '✏️ ویرایش کار' : '➕ کار جدید', \`
    <div class="form-group">
      <label class="form-label">عنوان</label>
      <input class="form-input" id="f_title" value="\${escapeHtml(task.title)}" placeholder="مثلاً: جلسه تیم">
    </div>
    <div class="form-group">
      <label class="form-label">توضیحات</label>
      <textarea class="form-textarea" id="f_desc" placeholder="اختیاری">\${escapeHtml(task.description||'')}</textarea>
    </div>
    <div class="form-row form-group">
      <div><label class="form-label">تاریخ</label><input type="date" class="form-input" id="f_date" value="\${task.task_date}"></div>
      <div><label class="form-label">ساعت</label><input type="time" class="form-input" id="f_time" value="\${task.task_time||''}"></div>
    </div>
    <div class="form-group">
      <label class="form-label">دسته‌بندی</label>
      <select class="form-select" id="f_cat">\${catOptions}</select>
    </div>
    <div class="form-group">
      <label class="form-label">اولویت</label>
      <div class="priority-selector" id="f_priority">
        \${priorities.map(([v,l]) => \`<div class="priority-opt \${task.priority===v?'selected':''}" data-val="\${v}">\${l}</div>\`).join('')}
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">آیکون</label>
      <div class="icon-picker" id="f_icon">
        \${ICONS.map(i => \`<div class="icon-opt \${task.icon===i?'selected':''}" data-val="\${i}">\${i}</div>\`).join('')}
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">رنگ</label>
      <div class="color-picker" id="f_color">
        \${COLORS.map(c => \`<div class="color-opt \${task.color===c?'selected':''}" data-val="\${c}" style="background:\${c}"></div>\`).join('')}
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">\${t('tasks.repeatWeekly')}</label>
      <div class="weekday-picker" id="f_recur_days">
        \${weekdayDisplayOrder().map(dow => \`<div class="weekday-opt \${existingRecurrenceDays.includes(dow)?'selected':''}" data-dow="\${dow}">\${WEEKDAY_SHORT[state.lang][dow]}</div>\`).join('')}
      </div>
      <div class="form-hint">\${taskId ? t('tasks.repeatWeeklyEditHint') : t('tasks.repeatWeeklyHint')}</div>
    </div>
    \${!taskId ? \`
    <div class="form-group">
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;margin-bottom:8px;"><input type="checkbox" id="f_reminder_enabled"> 🔔 \${t('tasks.enableReminder')}</label>
      <select class="form-select" id="f_reminder_offset" style="display:none;">
        \${reminderOffsetOptions().map(([v,l]) => \`<option value="\${v}">\${l}</option>\`).join('')}
      </select>
    </div>
    \` : ''}
    \${taskId ? '<div class="form-group"><label class="form-label">⏰ یادآورها</label><div id="remindersList"></div><button class="btn btn-secondary" id="addReminderBtn" type="button">➕ افزودن یادآور</button></div>' : ''}
    <div class="form-row" style="margin-top:10px;">
      \${taskId ? '<button class="btn btn-danger" id="deleteTaskBtn" type="button">🗑️ حذف</button>' : ''}
      <button class="btn btn-primary" id="saveTaskBtn" type="button">💾 ذخیره</button>
    </div>
  \`);

  bindSelector('f_priority', 'priority-opt');
  bindSelector('f_icon', 'icon-opt');
  bindSelector('f_color', 'color-opt');

  document.querySelectorAll('#f_recur_days .weekday-opt').forEach(el => {
    el.onclick = () => el.classList.toggle('selected');
  });
  if (!taskId) {
    const reminderCheck = document.getElementById('f_reminder_enabled');
    const reminderOffsetSel = document.getElementById('f_reminder_offset');
    reminderCheck.onchange = () => { reminderOffsetSel.style.display = reminderCheck.checked ? 'block' : 'none'; };
  }

  if (taskId) {
    loadReminders(taskId);
    document.getElementById('addReminderBtn').onclick = () => openReminderForm(taskId);
    document.getElementById('deleteTaskBtn').onclick = async () => {
      const ok = await confirmDialog(t('confirm.deleteTask'));
      if (!ok) return;
      try { await api('/tasks/' + taskId, { method: 'DELETE' }); toast('🗑️ کار حذف شد'); closeSheet(); navigate(state.page); }
      catch (err) { toast('❌ ' + err.message); }
    };
  }

  document.getElementById('saveTaskBtn').onclick = async () => {
    const payload = {
      title: document.getElementById('f_title').value.trim(),
      description: document.getElementById('f_desc').value.trim(),
      task_date: document.getElementById('f_date').value,
      task_time: document.getElementById('f_time').value || null,
      category_id: Number(document.getElementById('f_cat').value),
      priority: document.querySelector('#f_priority .selected')?.dataset.val || 'normal',
      icon: document.querySelector('#f_icon .selected')?.dataset.val || '📝',
      color: document.querySelector('#f_color .selected')?.dataset.val || '#6C5CE7',
    };
    // همیشه (چه ساخت، چه ویرایش) روزهای انتخاب‌شدهٔ تکرار ارسال می‌شود — آرایهٔ خالی
    // یعنی «تکرار غیرفعال شود»، که سرور آن را به‌درستی مدیریت می‌کند
    payload.recurrence_days = [...document.querySelectorAll('#f_recur_days .weekday-opt.selected')].map(el => Number(el.dataset.dow));
    if (!taskId) {
      const reminderEnabled = document.getElementById('f_reminder_enabled').checked;
      payload.reminder_enabled = reminderEnabled;
      if (reminderEnabled) payload.reminder_offset_minutes = Number(document.getElementById('f_reminder_offset').value);
    }
    if (!payload.title) { toast(t('habits.titleRequired')); return; }
    if (!payload.task_date) { toast('⚠️ تاریخ الزامی است'); return; }
    try {
      if (taskId) await api('/tasks/' + taskId, { method: 'PATCH', body: payload });
      else await api('/tasks', { method: 'POST', body: payload });
      toast('✅ ذخیره شد');
      closeSheet();
      navigate(state.page);
    } catch (err) { toast('❌ ' + err.message); }
  };
}

// خواندن آرایهٔ روزهای تکرار از رشتهٔ JSON ذخیره‌شده روی یک کار (سمت کلاینت،
// معادل parseRecurrenceDays سمت سرور) — برای پرکردنِ صحیح چک‌باکس‌ها هنگام ویرایش
function parseRecurrenceDaysClient(raw) {
  if (!raw) return [];
  try {
    const arr = typeof raw === 'string' ? JSON.parse(raw) : raw;
    return Array.isArray(arr) ? arr.filter((d) => Number.isInteger(d) && d >= 0 && d <= 6) : [];
  } catch { return []; }
}

function bindSelector(containerId, cls) {
  const container = document.getElementById(containerId);
  container.querySelectorAll('.' + cls).forEach(el => {
    el.onclick = () => {
      container.querySelectorAll('.' + cls).forEach(o => o.classList.remove('selected'));
      el.classList.add('selected');
    };
  });
}

async function loadReminders(taskId) {
  const list = document.getElementById('remindersList');
  if (!list) return;
  const reminders = await api('/tasks/' + taskId + '/reminders');
  if (reminders.length === 0) { list.innerHTML = '<div style="font-size:12px;color:var(--text-secondary);">یادآوری تنظیم نشده</div>'; return; }
  list.innerHTML = reminders.map(r => \`
    <div class="task-item" style="padding:8px 10px;">
      <div class="task-icon">\${r.is_active ? '🔔' : '🔕'}</div>
      <div class="task-body"><div class="task-title" style="font-size:13px;">\${reminderLabel(r)}</div></div>
      <button class="icon-btn" style="width:30px;height:30px;font-size:12px;" data-rem-toggle="\${r.id}">\${r.is_active?'⏸️':'▶️'}</button>
      <button class="icon-btn" style="width:30px;height:30px;font-size:12px;" data-rem-del="\${r.id}">🗑️</button>
    </div>\`).join('');
  list.querySelectorAll('[data-rem-toggle]').forEach(el => el.onclick = async () => { await api('/reminders/'+el.dataset.remToggle, {method:'PATCH'}); loadReminders(taskId); });
  list.querySelectorAll('[data-rem-del]').forEach(el => el.onclick = async () => { await api('/reminders/'+el.dataset.remDel, {method:'DELETE'}); loadReminders(taskId); });
}

function reminderLabel(r) {
  if (r.offset_minutes === 0) return 'دقیقاً در زمان کار' + repeatSuffix(r.repeat_type);
  if (r.offset_minutes < 0) return Math.abs(r.offset_minutes) + ' دقیقه قبل' + repeatSuffix(r.repeat_type);
  return r.offset_minutes + ' دقیقه بعد' + repeatSuffix(r.repeat_type);
}
function repeatSuffix(rt) { return rt === 'daily' ? ' (روزانه)' : rt === 'weekly' ? ' (هفتگی)' : ''; }

function openReminderForm(taskId) {
  const wrap = document.createElement('div');
  wrap.innerHTML = \`
    <div class="form-group">
      <label class="form-label">زمان یادآوری</label>
      <select class="form-select" id="rm_offset">
        <option value="-30">۳۰ دقیقه قبل</option>
        <option value="-15">۱۵ دقیقه قبل</option>
        <option value="-5">۵ دقیقه قبل</option>
        <option value="0">دقیقاً سر زمان</option>
        <option value="10">۱۰ دقیقه بعد</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">تکرار</label>
      <select class="form-select" id="rm_repeat">
        <option value="none">بدون تکرار</option>
        <option value="daily">روزانه</option>
        <option value="weekly">هفتگی</option>
      </select>
    </div>
    <button class="btn btn-primary" id="rm_save">➕ افزودن</button>
  \`;
  document.getElementById('remindersList').after(wrap);
  wrap.querySelector('#rm_save').onclick = async () => {
    try {
      await api('/tasks/' + taskId + '/reminders', { method:'POST', body: { offset_minutes: Number(wrap.querySelector('#rm_offset').value), repeat_type: wrap.querySelector('#rm_repeat').value } });
      toast('⏰ یادآور اضافه شد');
      wrap.remove();
      loadReminders(taskId);
    } catch (err) { toast('❌ ' + err.message); }
  };
}
`;

// ---- JS part 3: Habits, Calendar, Notes ----
const JS_TEXT_3 = `
async function renderHabits() {
  const habits = await api('/habits');
  state.data.habits = habits;
  const main = document.getElementById('mainContent');
  main.innerHTML = habits.length === 0 ? emptyState('🔥', t('empty.habits')) :
    habits.map(h => \`
      <div class="card habit-card">
        <div class="habit-circle \${h.done_today?'done':''}" data-habit-log="\${h.id}">\${h.icon}</div>
        <div class="habit-info" data-habit-progress="\${h.id}">
          <div class="habit-title">\${escapeHtml(h.title)}</div>
          <div class="habit-sub">🔥 \${h.streak} روز · 🎯 هدف روزانه: \${h.daily_goal||1} · ✅ \${h.total_logs} بار ثبت · 📈 \${h.success_rate}%</div>
          <div class="progress-bar" style="margin-top:6px;"><div class="progress-fill" style="width:\${h.success_rate}%"></div></div>
        </div>
        <button class="icon-btn" style="width:32px;height:32px;font-size:14px;" data-habit-edit="\${h.id}">✏️</button>
        <button class="icon-btn" style="width:32px;height:32px;font-size:14px;" data-habit-del="\${h.id}">🗑️</button>
      </div>\`).join('');
  bindHabitEvents();
  document.querySelectorAll('[data-habit-del]').forEach(el => {
    el.onclick = async (e) => {
      e.stopPropagation();
      const ok = await confirmDialog(t('confirm.deleteHabit'));
      if (!ok) return;
      await api('/habits/' + el.dataset.habitDel, { method:'DELETE' });
      toast('🗑️ حذف شد'); renderHabits();
    };
  });
}

async function openHabitProgress(habitId) {
  let p;
  try { p = await api('/habits/' + habitId + '/progress'); }
  catch (err) { toast('❌ ' + err.message); return; }

  const WEEKS = 13;
  const cols = [];
  for (let w = 0; w < WEEKS; w++) cols.push(p.heatmap.slice(w * 7, w * 7 + 7));
  const habitColor = p.color || '#00B894';

  const heatmapHtml = '<div class="habit-heatmap">' + cols.map((week) =>
    '<div class="habit-heatmap-col">' + week.map((day) =>
      '<div class="habit-heatmap-cell ' + (day.done ? 'done' : '') + '" style="' + (day.done ? '--habit-color:' + habitColor + ';' : '') + '" title="' + day.date + (day.done ? ' ✅' : '') + '"></div>'
    ).join('') + '</div>'
  ).join('') + '</div>';

  const achievementsHtml = '<div class="habit-achievements">' + p.achievements.map((a) =>
    '<div class="habit-achievement ' + (a.unlocked ? 'unlocked' : '') + '">' +
      '<div class="habit-achievement-icon">' + (a.unlocked ? '🏆' : '🔒') + '</div>' +
      '<div class="habit-achievement-label">' + a.milestone + ' ' + t('habits.days') + '</div>' +
    '</div>'
  ).join('') + '</div>';

  openSheet('📊 ' + escapeHtml(p.title), \`
    <div class="stat-grid">
      <div class="stat-box"><div class="stat-num">🔥\${p.streak}</div><div class="stat-label">\${t('habits.currentStreak')}</div></div>
      <div class="stat-box"><div class="stat-num">🏆\${p.best_streak}</div><div class="stat-label">\${t('habits.bestStreak')}</div></div>
      <div class="stat-box"><div class="stat-num">\${p.success_rate}%</div><div class="stat-label">\${t('habits.successRate')}</div></div>
    </div>
    <div class="card-title" style="margin-top:18px;">\${t('habits.last90days')}</div>
    \${heatmapHtml}
    <div class="card-title" style="margin-top:18px;">\${t('habits.achievements')}</div>
    \${achievementsHtml}
    \${!p.done_today ? '<button class="btn btn-primary" id="progressLogBtn" style="margin-top:18px;">🔥 ' + t('habits.logToday') + '</button>' : ''}
  \`);

  if (!p.done_today) {
    document.getElementById('progressLogBtn').onclick = async () => {
      try {
        const h = await api('/habits/' + habitId + '/log', { method: 'POST' });
        toast(STREAK_MILESTONES.includes(h.streak) ? encouragementFor(h.streak) : ('🔥 ثبت شد! ' + h.streak + ' روز متوالی'));
        closeSheet();
        navigate(state.page);
      } catch (err) { toast('❌ ' + err.message); }
    };
  }
}

async function openHabitForm(habitId) {
  let habit = { title:'', description:'', icon:'🔥', daily_goal: 1 };
  if (habitId) {
    const list = state.data.habits || await api('/habits');
    habit = list.find(h => String(h.id) === String(habitId)) || habit;
  }
  const icons = ['🔥','📚','🏋️','💧','🧘','🎯','🚭','🌅'];
  openSheet(habitId ? '✏️ ویرایش عادت' : '🔥 عادت جدید', \`
    <div class="form-group"><label class="form-label">عنوان</label><input class="form-input" id="h_title" placeholder="مثلاً: مطالعه روزانه" value="\${escapeHtml(habit.title||'')}"></div>
    <div class="form-group"><label class="form-label">توضیح</label><textarea class="form-textarea" id="h_desc">\${escapeHtml(habit.description||'')}</textarea></div>
    <div class="form-group"><label class="form-label">هدف روزانه (تعداد تکرار در روز)</label><input class="form-input" type="number" min="1" id="h_goal" value="\${habit.daily_goal||1}"></div>
    <div class="form-group">
      <label class="form-label">آیکون</label>
      <div class="icon-picker" id="h_icon">
        \${icons.map((i) => \`<div class="icon-opt \${i===habit.icon?'selected':''}" data-val="\${i}">\${i}</div>\`).join('')}
      </div>
    </div>
    <button class="btn btn-primary" id="h_save">💾 ذخیره</button>
  \`);
  if (!document.querySelector('#h_icon .selected')) document.querySelector('#h_icon .icon-opt')?.classList.add('selected');
  bindSelector('h_icon', 'icon-opt');
  const saveBtn = document.getElementById('h_save');
  saveBtn.onclick = async () => {
    const title = document.getElementById('h_title').value.trim();
    if (!title) { toast(t('habits.titleRequired')); return; }
    const goal = Math.max(1, Number(document.getElementById('h_goal').value) || 1);
    if (saveBtn.disabled) return;
    saveBtn.disabled = true;
    const payload = {
      title, description: document.getElementById('h_desc').value.trim(),
      icon: document.querySelector('#h_icon .selected')?.dataset.val || '🔥',
      daily_goal: goal,
    };
    try {
      if (habitId) await api('/habits/' + habitId, { method:'PATCH', body: payload });
      else await api('/habits', { method:'POST', body: payload });
      toast(habitId ? '✅ عادت به‌روزرسانی شد' : '✅ عادت اضافه شد'); closeSheet(); navigate(state.page);
    } catch (err) { toast('❌ ' + err.message); saveBtn.disabled = false; }
  };
}

// ===== تقویم شمسی (جلالی) =====
// از تقویم داخلی ICU (Intl با calendar=persian) برای تبدیل میلادی↔شمسی استفاده می‌کنیم
// تا نیازی به پیاده‌سازی دستیِ فرمول تبدیل نباشد و دقت آن تضمین‌شده باشد.
const JALALI_MONTH_NAMES = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
const JALALI_DOW_LETTERS = ['ش','ی','د','س','چ','پ','ج']; // شنبه تا جمعه (شروع هفته از شنبه)
// مناسبت‌های رسمی با تاریخ ثابت شمسی (تعطیلات قمری مانند عید فطر/عاشورا هر سال جابه‌جا می‌شوند
// و نیازمند تقویم هجری قمری‌اند؛ این لیست را می‌توان هرساله تکمیل کرد)
const JALALI_SOLAR_EVENTS = {
  '1-1': { label: 'نوروز', holiday: true }, '1-2': { label: 'عید نوروز', holiday: true },
  '1-3': { label: 'عید نوروز', holiday: true }, '1-4': { label: 'عید نوروز', holiday: true },
  '1-12': { label: 'روز جمهوری اسلامی ایران', holiday: true },
  '1-13': { label: 'سیزده‌به‌در', holiday: true },
  '3-14': { label: 'رحلت امام خمینی (ره)', holiday: true },
  '3-15': { label: 'قیام ۱۵ خرداد', holiday: true },
  '11-22': { label: 'پیروزی انقلاب اسلامی', holiday: true },
  '12-29': { label: 'ملی‌شدن صنعت نفت', holiday: true },
};

function jalaliOf(gDateStr) {
  const [y, m, d] = gDateStr.split('-').map(Number);
  const date = new Date(Date.UTC(y, m - 1, d, 12));
  const dtf = new Intl.DateTimeFormat('en-US-u-ca-persian', { year:'numeric', month:'numeric', day:'numeric', timeZone:'UTC' });
  const parts = dtf.formatToParts(date).reduce((a,p) => { a[p.type] = p.value; return a; }, {});
  return { jy: Number(parts.year), jm: Number(parts.month), jd: Number(parts.day) };
}

const _nowruzCache = {};
function findNowruzGregorian(jy) {
  if (_nowruzCache[jy]) return _nowruzCache[jy];
  const approxGy = jy + 621;
  for (let d = 15; d <= 25; d++) {
    const gStr = approxGy + '-03-' + String(d).padStart(2,'0');
    const j = jalaliOf(gStr);
    if (j.jy === jy && j.jm === 1 && j.jd === 1) { _nowruzCache[jy] = gStr; return gStr; }
  }
  return approxGy + '-03-21'; // نادر: مقدار پیش‌فرض ایمن
}

function jalaliMonthLength(jy, jm) {
  if (jm <= 6) return 31;
  if (jm <= 11) return 30;
  const n1 = findNowruzGregorian(jy), n2 = findNowruzGregorian(jy + 1);
  const [y1,m1,d1] = n1.split('-').map(Number), [y2,m2,d2] = n2.split('-').map(Number);
  const days = Math.round((Date.UTC(y2,m2-1,d2) - Date.UTC(y1,m1-1,d1)) / 86400000);
  return days - 336; // ۳۱×۶ + ۳۰×۵ = ۳۳۶ روزِ ماه‌های اول تا یازدهم
}

function jalaliMonthFirstDayGregorian(jy, jm) {
  const nowruz = findNowruzGregorian(jy);
  let offset = 0;
  for (let mm = 1; mm < jm; mm++) offset += jalaliMonthLength(jy, mm);
  return addDaysToDateStr(nowruz, offset);
}

function jalaliToGregorian(jy, jm, jd) {
  return addDaysToDateStr(jalaliMonthFirstDayGregorian(jy, jm), jd - 1);
}

function persianDow(gDateStr) {
  const [y,m,d] = gDateStr.split('-').map(Number);
  const gDow = new Date(Date.UTC(y, m-1, d)).getUTCDay(); // یکشنبه=۰
  return (gDow + 1) % 7; // شنبه=۰ ... جمعه=۶
}

function shiftJalaliMonth(jy, jm, delta) {
  let total = jy * 12 + (jm - 1) + delta;
  const ny = Math.floor(total / 12), nm = (total % 12) + 1;
  return { jy: ny, jm: nm };
}

// نام ماه‌ها و روزهای هفتهٔ میلادی (بسته به زبان انتخابی نمایش داده می‌شوند)
const GREGORIAN_MONTH_NAMES = {
  fa: ['ژانویه','فوریه','مارس','آوریل','مه','ژوئن','ژوئیه','اوت','سپتامبر','اکتبر','نوامبر','دسامبر'],
  en: ['January','February','March','April','May','June','July','August','September','October','November','December'],
};
const GREGORIAN_DOW_LETTERS = { fa: ['د','س','چ','پ','ج','ش','ی'], en: ['M','T','W','T','F','S','S'] }; // دوشنبه تا یکشنبه
const GREGORIAN_EVENTS = {
  '1-1': { label: 'سال نو میلادی', holiday: false },
  '2-14': { label: 'ولنتاین', holiday: false },
  '3-8': { label: 'روز جهانی زن', holiday: false },
  '3-20': { label: 'روز جهانی زمین', holiday: false },
  '5-1': { label: 'روز جهانی کارگر', holiday: false },
  '10-31': { label: 'هالووین', holiday: false },
  '12-25': { label: 'کریسمس', holiday: false },
  '12-31': { label: 'شب سال نو', holiday: false },
};

function gregorianDow(dateStr) {
  const [y, m, d] = dateStr.split('-').map(Number);
  const gDow = new Date(Date.UTC(y, m - 1, d)).getUTCDay(); // یکشنبه=۰
  return (gDow + 6) % 7; // دوشنبه=۰ ... یکشنبه=۶ (استاندارد ISO)
}
function gregorianMonthLength(y, m) { return new Date(Date.UTC(y, m, 0)).getUTCDate(); }
function isWeekendGregorian(dow) { return dow === 5 || dow === 6; } // شنبه/یکشنبه

// ===== تقویم =====
async function renderCalendar() {
  const data = await api('/calendar?view=' + state.calendarView + '&date=' + state.calendarDate);
  const main = document.getElementById('mainContent');
  const isJalali = state.calendarType === 'jalali';
  const j = isJalali ? jalaliOf(state.calendarDate) : null;
  const [gy, gm, gd] = state.calendarDate.split('-').map(Number);
  let titleText = '';
  if (isJalali) {
    if (state.calendarView === 'month') titleText = JALALI_MONTH_NAMES[j.jm-1] + ' ' + j.jy;
    else if (state.calendarView === 'week') titleText = 'هفتهٔ ' + JALALI_MONTH_NAMES[j.jm-1] + ' ' + j.jy;
    else titleText = j.jd + ' ' + JALALI_MONTH_NAMES[j.jm-1] + ' ' + j.jy;
  } else {
    const names = GREGORIAN_MONTH_NAMES[state.lang] || GREGORIAN_MONTH_NAMES.fa;
    if (state.calendarView === 'month') titleText = names[gm-1] + ' ' + gy;
    else if (state.calendarView === 'week') titleText = names[gm-1] + ' ' + gy;
    else titleText = gd + ' ' + names[gm-1] + ' ' + gy;
  }

  main.innerHTML = \`
    <div class="tabs">
      <button class="tab-btn \${state.calendarView==='month'?'active':''}" data-view="month">\${t('calendar.month')}</button>
      <button class="tab-btn \${state.calendarView==='week'?'active':''}" data-view="week">\${t('calendar.week')}</button>
      <button class="tab-btn \${state.calendarView==='day'?'active':''}" data-view="day">\${t('calendar.day')}</button>
    </div>
    <div class="card">
      <div class="cal-nav">
        <button class="icon-btn" id="calPrev">›</button>
        <div class="cal-nav-title">\${titleText}</div>
        <button class="icon-btn" id="calNext">‹</button>
      </div>
      <div id="calGrid"></div>
    </div>
    <div id="calEvents"></div>
    <div class="card-title" style="margin:4px 4px 10px;">\${t('calendar.schedule')}</div>
    <div id="calTasks"></div>
  \`;
  document.querySelectorAll('[data-view]').forEach(el => el.onclick = () => { state.calendarView = el.dataset.view; renderCalendar(); });
  document.getElementById('calPrev').onclick = () => { navigateCalendar(1); };
  document.getElementById('calNext').onclick = () => { navigateCalendar(-1); }; // راست‌به‌چپ: ‹ به معنای جلو رفتن
  renderCalGrid(data, j, gy, gm);
  renderCalEvents(j, gy, gm);
  renderCalTasks(data);
}

function navigateCalendar(dir) {
  if (state.calendarView === 'day') { state.calendarDate = addDaysToDateStr(state.calendarDate, dir); }
  else if (state.calendarView === 'week') { state.calendarDate = addDaysToDateStr(state.calendarDate, dir * 7); }
  else if (state.calendarType === 'jalali') {
    const j = jalaliOf(state.calendarDate);
    const { jy, jm } = shiftJalaliMonth(j.jy, j.jm, dir);
    state.calendarDate = jalaliToGregorian(jy, jm, 1);
  } else {
    const [y, m] = state.calendarDate.split('-').map(Number);
    let total = y * 12 + (m - 1) + dir;
    const ny = Math.floor(total / 12), nm = (total % 12) + 1;
    state.calendarDate = \`\${ny}-\${String(nm).padStart(2,'0')}-01\`;
  }
  renderCalendar();
}

// خلاصهٔ مینیمال فعالیت‌های یک روز برای نمایش داخل خانهٔ تقویم (حداکثر ۳ نشانه + «+N»)
function dayActivitySummaryHtml(dayData) {
  if (!dayData) return '';
  const items = [
    ...dayData.tasks.map(x => ({ kind:'task', color: x.color || '#6C5CE7' })),
    ...dayData.notes.map(() => ({ kind:'note', color:'#F5A623' })),
    ...dayData.habits.map(() => ({ kind:'habit', color:'#E74C3C' })),
  ];
  if (items.length === 0) return '';
  const shown = items.slice(0, 3);
  const rest = items.length - shown.length;
  let html = '<div class="cal-day-dots">' + shown.map(it => '<span class="cal-dot" style="background:'+it.color+'"></span>').join('');
  if (rest > 0) html += '<span class="cal-dot-more">+' + rest + '</span>';
  html += '</div>';
  return html;
}

function dayCellHtml(dateStr, data, jd) {
  const isToday = dateStr === todayStr();
  const isSelected = dateStr === state.calendarDate;
  const dayData = data.days[dateStr];
  let isHoliday = false, evLabel = '';
  if (state.calendarType === 'jalali') {
    const jal = jalaliOf(dateStr);
    const ev = JALALI_SOLAR_EVENTS[jal.jm + '-' + jal.jd];
    isHoliday = persianDow(dateStr) === 6 || (ev && ev.holiday);
    evLabel = ev ? ev.label : '';
  } else {
    const [y,m,d] = dateStr.split('-').map(Number);
    const ev = GREGORIAN_EVENTS[m + '-' + d];
    isHoliday = isWeekendGregorian(gregorianDow(dateStr));
    evLabel = ev ? ev.label : '';
  }
  const dayNum = jd !== undefined ? jd : (state.calendarType === 'jalali' ? jalaliOf(dateStr).jd : Number(dateStr.split('-')[2]));
  return '<div class="cal-day '+(isToday?'today':'')+' '+(isSelected?'selected':'')+' '+(isHoliday?'holiday':'')+'" data-date="'+dateStr+'" title="'+evLabel+'">'
    + '<span class="cal-day-num">' + dayNum + '</span>' + dayActivitySummaryHtml(dayData) + '</div>';
}

function renderCalGrid(data, j, gy, gm) {
  const grid = document.getElementById('calGrid');
  const isJalali = state.calendarType === 'jalali';
  let html = '';
  if (state.calendarView === 'month') {
    if (isJalali) {
      const firstGDate = jalaliMonthFirstDayGregorian(j.jy, j.jm);
      const monthLen = jalaliMonthLength(j.jy, j.jm);
      const firstDow = persianDow(firstGDate);
      html += '<div class="cal-grid">' + JALALI_DOW_LETTERS.map(d => '<div class="cal-dow">'+d+'</div>').join('');
      for (let i=0; i<firstDow; i++) html += '<div></div>';
      for (let jd=1; jd<=monthLen; jd++) html += dayCellHtml(addDaysToDateStr(firstGDate, jd-1), data, jd);
    } else {
      const firstGDate = \`\${gy}-\${String(gm).padStart(2,'0')}-01\`;
      const monthLen = gregorianMonthLength(gy, gm);
      const firstDow = gregorianDow(firstGDate);
      const dows = GREGORIAN_DOW_LETTERS[state.lang] || GREGORIAN_DOW_LETTERS.fa;
      html += '<div class="cal-grid">' + dows.map(d => '<div class="cal-dow">'+d+'</div>').join('');
      for (let i=0; i<firstDow; i++) html += '<div></div>';
      for (let d=1; d<=monthLen; d++) html += dayCellHtml(\`\${gy}-\${String(gm).padStart(2,'0')}-\${String(d).padStart(2,'0')}\`, data, d);
    }
    html += '</div>';
  } else if (state.calendarView === 'week') {
    const dows = isJalali ? JALALI_DOW_LETTERS : (GREGORIAN_DOW_LETTERS[state.lang] || GREGORIAN_DOW_LETTERS.fa);
    html += '<div class="cal-grid">' + dows.map(d => '<div class="cal-dow">'+d+'</div>').join('');
    const weekStart = isJalali
      ? addDaysToDateStr(state.calendarDate, -persianDow(state.calendarDate))
      : addDaysToDateStr(state.calendarDate, -gregorianDow(state.calendarDate));
    for (let i=0; i<7; i++) html += dayCellHtml(addDaysToDateStr(weekStart, i), data);
    html += '</div>';
  } else { // day
    let isHoliday, label, evLabel;
    if (isJalali) {
      const ev = JALALI_SOLAR_EVENTS[j.jm + '-' + j.jd];
      isHoliday = persianDow(state.calendarDate) === 6 || (ev && ev.holiday);
      label = j.jd + ' ' + JALALI_MONTH_NAMES[j.jm-1] + ' ' + j.jy;
      evLabel = ev ? ev.label : '';
    } else {
      const dNum = Number(state.calendarDate.split('-')[2]);
      const evReal = GREGORIAN_EVENTS[gm + '-' + dNum];
      isHoliday = isWeekendGregorian(gregorianDow(state.calendarDate));
      label = dNum + ' ' + (GREGORIAN_MONTH_NAMES[state.lang]||GREGORIAN_MONTH_NAMES.fa)[gm-1] + ' ' + gy;
      evLabel = evReal ? evReal.label : '';
    }
    html += '<div class="cal-day-single '+(isHoliday?'holiday':'')+'">'
      + '<div class="cal-day-single-num">' + (isJalali ? j.jd : Number(state.calendarDate.split('-')[2])) + '</div>'
      + '<div class="cal-day-single-label">' + label + (isHoliday?' · ' + t('calendar.holiday'):'') + '</div>'
      + (evLabel ? '<div class="cal-day-single-event">🎉 ' + evLabel + '</div>' : '')
      + '</div>';
  }
  grid.innerHTML = html;
  grid.querySelectorAll('[data-date]').forEach(el => el.onclick = () => { state.calendarDate = el.dataset.date; renderCalendar(); });
}

function renderCalEvents(j, gy, gm) {
  const el = document.getElementById('calEvents');
  if (state.calendarView !== 'month') { el.innerHTML = ''; return; }
  const isJalali = state.calendarType === 'jalali';
  const events = isJalali ? JALALI_SOLAR_EVENTS : GREGORIAN_EVENTS;
  const monthIdx = isJalali ? j.jm : gm;
  const monthName = isJalali ? JALALI_MONTH_NAMES[j.jm-1] : (GREGORIAN_MONTH_NAMES[state.lang]||GREGORIAN_MONTH_NAMES.fa)[gm-1];
  const monthEvents = Object.entries(events).filter(([k]) => Number(k.split('-')[0]) === monthIdx);
  if (monthEvents.length === 0) { el.innerHTML = ''; return; }
  el.innerHTML = '<div class="card"><div class="card-title">' + t('calendar.eventsThisMonth') + '</div>' +
    monthEvents.map(([k, v]) => '<div style="font-size:13px;padding:4px 0;">📅 ' + k.split('-')[1] + ' ' + monthName + ' — ' + v.label + '</div>').join('') +
    '</div>';
}

function renderCalTasks(data) {
  const el = document.getElementById('calTasks');
  const dayData = data.days[state.calendarDate] || { tasks: [], notes: [], habits: [] };
  const total = dayData.tasks.length + dayData.notes.length + dayData.habits.length;
  if (total === 0) { el.innerHTML = emptyState('📭', t('empty.calendarDay')); return; }
  let html = dayData.tasks.map(tsk => taskItemHtml(tsk)).join('');
  if (dayData.notes.length) {
    html += '<div class="card-title" style="margin-top:10px;font-size:13px;">📝 ' + t('nav.notes') + '</div>';
    html += dayData.notes.map(n => '<div class="cal-mini-item">📝 ' + escapeHtml(n.title || t('notes.untitled')) + '</div>').join('');
  }
  if (dayData.habits.length) {
    html += '<div class="card-title" style="margin-top:10px;font-size:13px;">🔥 ' + t('nav.habits') + '</div>';
    html += dayData.habits.map(h => '<div class="cal-mini-item">' + h.icon + ' ' + escapeHtml(h.title) + '</div>').join('');
  }
  el.innerHTML = html;
  bindTaskEvents();
}

// ===== Notes =====
let noteSearchQuery = '';
async function renderNotes() {
  const notes = await api('/notes' + (noteSearchQuery ? '?search='+encodeURIComponent(noteSearchQuery) : ''));
  const main = document.getElementById('mainContent');
  main.innerHTML = \`
    <div class="search-input-wrap"><input class="form-input" id="noteSearch" placeholder="🔍 جستجوی یادداشت..." value="\${escapeHtml(noteSearchQuery)}"></div>
    <div id="notesList"></div>
  \`;
  document.getElementById('noteSearch').oninput = debounce((e) => { noteSearchQuery = e.target.value; renderNotes(); }, 350);
  const list = document.getElementById('notesList');
  list.innerHTML = notes.length === 0 ? emptyState('📝', t('empty.notes')) : notes.map(n => \`
    <div class="note-card" style="background:\${n.color}22;border:1px solid \${n.color}55;" data-note-id="\${n.id}">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div class="note-title">\${n.is_pinned?'📌 ':''}\${escapeHtml(n.title||t('notes.untitled'))}</div>
        <button class="icon-btn" style="width:28px;height:28px;font-size:12px;" data-note-del="\${n.id}">🗑️</button>
      </div>
      <div class="note-content">\${escapeHtml(n.content)}</div>
      \${n.attachment_count > 0 ? '<div class="note-attachment-badge">📎 ' + n.attachment_count + '</div>' : ''}
    </div>\`).join('');
  list.querySelectorAll('[data-note-id]').forEach(el => {
    el.onclick = (e) => { if (e.target.closest('[data-note-del]')) return; openNoteForm(el.dataset.noteId); };
  });
  list.querySelectorAll('[data-note-del]').forEach(el => {
    el.onclick = async (e) => {
      e.stopPropagation();
      const ok = await confirmDialog(t('confirm.deleteNote'));
      if (!ok) return;
      await api('/notes/' + el.dataset.noteDel, { method:'DELETE' });
      toast('🗑️ حذف شد'); renderNotes();
    };
  });
}

function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; }

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / 1024 / 1024).toFixed(1) + ' MB';
}

function attachmentItemHtml(a) {
  const isImage = (a.mime_type || '').startsWith('image/');
  return \`<div class="attachment-item" data-att-id="\${a.id}">
    \${isImage ? \`<div class="attachment-thumb-wrap"><img class="attachment-thumb" id="att-img-\${a.id}" alt=""></div>\` : '<div class="attachment-icon">📄</div>'}
    <div class="attachment-info">
      <div class="attachment-name">\${escapeHtml(a.filename)}</div>
      <div class="attachment-size">\${formatFileSize(a.size)}</div>
    </div>
    <div class="attachment-actions">
      <button class="icon-btn" data-att-view="\${a.id}" data-att-mime="\${a.mime_type}" data-att-name="\${escapeHtml(a.filename)}" style="width:30px;height:30px;font-size:13px;">\${isImage ? '👁️' : '⬇️'}</button>
      <button class="icon-btn" data-att-del="\${a.id}" style="width:30px;height:30px;font-size:13px;">🗑️</button>
    </div>
  </div>\`;
}

function showImageLightbox(url) {
  const overlay = document.createElement('div');
  overlay.className = 'lightbox-overlay';
  overlay.innerHTML = \`<img src="\${url}" class="lightbox-img">\`;
  overlay.onclick = () => { overlay.remove(); URL.revokeObjectURL(url); };
  document.body.appendChild(overlay);
}

async function handleAttachmentView(id, mime, name) {
  try {
    const blob = await apiDownloadBlob('/attachments/' + id + '/download');
    const url = URL.createObjectURL(blob);
    if ((mime || '').startsWith('image/')) {
      showImageLightbox(url);
    } else {
      const a = document.createElement('a');
      a.href = url; a.download = name;
      document.body.appendChild(a); a.click(); a.remove();
      setTimeout(() => URL.revokeObjectURL(url), 5000);
    }
  } catch (err) { toast('❌ ' + err.message); }
}

function renderAttachmentItems(noteId, attachments) {
  const container = document.getElementById('n_attachments_list');
  if (!container) return;
  container.innerHTML = attachments.length === 0 ? '' : attachments.map(attachmentItemHtml).join('');
  attachments.filter(a => (a.mime_type || '').startsWith('image/')).forEach(async (a) => {
    try {
      const blob = await apiDownloadBlob('/attachments/' + a.id + '/download');
      const img = document.getElementById('att-img-' + a.id);
      if (img) img.src = URL.createObjectURL(blob);
    } catch (e) { /* پیش‌نمایش اختیاری است؛ اگر لود نشد نادیده گرفته می‌شود */ }
  });
  container.querySelectorAll('[data-att-view]').forEach(el => {
    el.onclick = () => handleAttachmentView(el.dataset.attView, el.dataset.attMime, el.dataset.attName);
  });
  container.querySelectorAll('[data-att-del]').forEach(el => {
    el.onclick = async () => {
      const ok = await confirmDialog(t('notes.attachmentDeleteConfirm'));
      if (!ok) return;
      try {
        await api('/attachments/' + el.dataset.attDel, { method: 'DELETE' });
        refreshAttachmentsSection(noteId);
      } catch (err) { toast('❌ ' + err.message); }
    };
  });
}

async function refreshAttachmentsSection(noteId) {
  const container = document.getElementById('n_attachments_list');
  if (!container) return;
  try {
    const updated = await api('/notes/' + noteId);
    renderAttachmentItems(noteId, updated.attachments || []);
  } catch (err) { /* اگر رفرش شکست خورد، لیست فعلی روی صفحه می‌ماند */ }
}

async function openNoteForm(noteId) {
  let note = { title:'', content:'', color:'#FFEAA7', is_pinned:0, attachments: [] };
  if (noteId) {
    try { note = await api('/notes/' + noteId); }
    catch (err) { toast('❌ ' + err.message); return; }
  }
  const noteColors = ['#FFEAA7','#A8E6CF','#FFAAA5','#D4A5FF','#A5D8FF','#FFD3A5'];
  const main = document.getElementById('mainContent');
  openSheet(noteId ? '✏️ ویرایش یادداشت' : '📝 یادداشت جدید', \`
    <div class="form-group"><label class="form-label">عنوان</label><input class="form-input" id="n_title" placeholder="اختیاری" value="\${escapeHtml(note.title||'')}"></div>
    <div class="form-group"><label class="form-label">متن</label><textarea class="form-textarea" id="n_content" placeholder="یادداشت خود را بنویسید...">\${escapeHtml(note.content||'')}</textarea></div>
    <div class="form-group">
      <label class="form-label">رنگ</label>
      <div class="color-picker" id="n_color">
        \${noteColors.map((c)=>\`<div class="color-opt \${c===note.color?'selected':''}" data-val="\${c}" style="background:\${c}"></div>\`).join('')}
      </div>
    </div>
    <div class="form-group">
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;"><input type="checkbox" id="n_pin" \${note.is_pinned?'checked':''}> 📌 پین کردن</label>
    </div>
    <div class="form-group">
      <label class="form-label">\${t('notes.attachments')}</label>
      \${noteId ? \`
        <div id="n_attachments_list">\${(note.attachments||[]).map(attachmentItemHtml).join('')}</div>
        <input type="file" id="n_attach_input" style="display:none;">
        <button type="button" class="btn btn-secondary" id="n_attach_btn" style="margin-top:6px;">\${t('notes.addAttachment')}</button>
      \` : \`<div class="form-hint">💾 برای افزودن فایل، ابتدا یادداشت را ذخیره کن.</div>\`}
    </div>
    <button class="btn btn-primary" id="n_save">💾 ذخیره</button>
  \`);
  // اگر رنگِ ذخیره‌شده در لیست رنگ‌های پیش‌فرض نبود (مثلاً داده قدیمی)، اولین گزینه انتخاب شود
  if (!document.querySelector('#n_color .selected')) {
    document.querySelector('#n_color .color-opt')?.classList.add('selected');
  }
  bindSelector('n_color', 'color-opt');
  if (noteId && note.attachments && note.attachments.length) renderAttachmentItems(noteId, note.attachments);
  if (noteId) {
    const attachBtn = document.getElementById('n_attach_btn');
    const attachInput = document.getElementById('n_attach_input');
    attachBtn.onclick = () => attachInput.click();
    attachInput.onchange = async (e) => {
      const file = e.target.files[0];
      e.target.value = '';
      if (!file) return;
      if (file.size > 20 * 1024 * 1024) { toast('⚠️ ' + t('notes.attachmentTooLarge')); return; }
      attachBtn.disabled = true;
      const originalLabel = attachBtn.textContent;
      attachBtn.textContent = t('notes.attachmentUploading');
      try {
        await apiUpload('/notes/' + noteId + '/attachments', file);
        await refreshAttachmentsSection(noteId);
      } catch (err) { toast('❌ ' + (err.message || t('notes.attachmentUploadError'))); }
      finally { attachBtn.disabled = false; attachBtn.textContent = originalLabel; }
    };
  }
  const saveBtn = document.getElementById('n_save');
  saveBtn.onclick = async () => {
    const content = document.getElementById('n_content').value.trim();
    if (!content) { toast(t('notes.contentRequired')); return; }
    if (saveBtn.disabled) return; // جلوگیری از ثبت تکراری با چند بار کلیک سریع
    saveBtn.disabled = true;
    const payload = {
      title: document.getElementById('n_title').value.trim(),
      content, color: document.querySelector('#n_color .selected')?.dataset.val || '#FFEAA7',
      is_pinned: document.getElementById('n_pin').checked ? 1 : 0,
    };
    try {
      if (noteId) await api('/notes/' + noteId, { method:'PATCH', body: payload });
      else await api('/notes', { method:'POST', body: payload });
      toast('✅ ذخیره شد'); closeSheet(); renderNotes();
    } catch (err) { toast('❌ ' + err.message); saveBtn.disabled = false; }
  };
}
`;

// ---- JS part 4: Search, Analytics, Settings, FAB/Init ----
const JS_TEXT_4 = `
// ===== Search =====
async function renderSearch() {
  const main = document.getElementById('mainContent');
  main.innerHTML = \`
    <div class="search-input-wrap"><input class="form-input" id="globalSearch" placeholder="\${t('search.placeholder')}" autofocus></div>
    <div id="searchResults"></div>
  \`;
  document.getElementById('globalSearch').oninput = debounce(async (e) => {
    const q = e.target.value.trim();
    const results = document.getElementById('searchResults');
    if (!q) { results.innerHTML = ''; return; }
    const data = await api('/search?q=' + encodeURIComponent(q));
    let html = '';
    if (data.tasks.length) html += '<div class="card-title">' + t('search.tasks') + '</div>' + data.tasks.map(t => taskItemHtml(t)).join('');
    if (data.notes.length) html += '<div class="card-title" style="margin-top:12px;">' + t('search.notes') + '</div>' + data.notes.map(n => \`<div class="note-card" style="background:\${n.color}22;"><div class="note-title">\${escapeHtml(n.title||t('notes.untitled'))}</div><div class="note-content">\${escapeHtml(n.content).slice(0,80)}</div></div>\`).join('');
    if (data.habits.length) html += '<div class="card-title" style="margin-top:12px;">' + t('search.habits') + '</div>' + data.habits.map(h => \`<div class="task-item"><div class="task-icon">\${h.icon}</div><div class="task-body"><div class="task-title">\${escapeHtml(h.title)}</div></div></div>\`).join('');
    results.innerHTML = html || emptyState('🔍', t('empty.search'));
    bindTaskEvents();
  }, 350);
}

// ===== Analytics =====
async function renderAnalytics() {
  const data = await api('/analytics?range=week');
  const main = document.getElementById('mainContent');
  main.innerHTML = \`
    <div class="stat-grid">
      <div class="stat-box"><div class="stat-num">\${data.total}</div><div class="stat-label">\${t('analytics.total')}</div></div>
      <div class="stat-box"><div class="stat-num">\${data.completed}</div><div class="stat-label">\${t('analytics.completed')}</div></div>
      <div class="stat-box"><div class="stat-num">\${data.success_rate}%</div><div class="stat-label">\${t('analytics.successRate')}</div></div>
      <div class="stat-box"><div class="stat-num">\${data.longest_streak}</div><div class="stat-label">\${t('analytics.longestStreak')}</div></div>
    </div>
    <div class="card">
      <div class="card-title">\${t('analytics.weeklyChart')}</div>
      \${renderBarChart(data.by_day)}
    </div>
    \${data.top_category ? '<div class="card"><div class="card-title">'+t('analytics.topCategory')+'</div><div style="font-size:14px;">'+data.top_category+'</div></div>' : ''}
  \`;
}

function renderBarChart(byDay) {
  const days = Object.keys(byDay).sort();
  if (days.length === 0) return emptyState('📊', t('empty.chartData'));
  const max = Math.max(...days.map(d => byDay[d].total), 1);
  return '<div style="display:flex;align-items:flex-end;gap:6px;height:120px;">' + days.map(d => {
    const h = Math.round((byDay[d].total / max) * 100);
    const hd = Math.round((byDay[d].done / max) * 100);
    const label = d.slice(8,10);
    return '<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;"><div style="width:100%;height:100px;background:var(--accent-light);border-radius:6px;position:relative;display:flex;align-items:flex-end;"><div style="width:100%;height:'+h+'%;background:var(--accent-light);border-radius:6px;position:absolute;bottom:0;"></div><div style="width:100%;height:'+hd+'%;background:var(--accent);border-radius:6px;position:absolute;bottom:0;"></div></div><small style="font-size:10px;color:var(--text-secondary);">'+label+'</small></div>';
  }).join('') + '</div>';
}

// ===== دستیار هوش مصنوعی (AI Assistant) =====
function aiBubbleHtml(msg) {
  const isUser = msg.role === 'user';
  return \`<div class="ai-bubble-row \${isUser ? 'user' : 'assistant'}">
    <div class="ai-bubble \${isUser ? 'user' : 'assistant'}">\${escapeHtml(msg.content).replace(/\\n/g, '<br>')}</div>
  </div>\`;
}

async function renderAssistant() {
  const main = document.getElementById('mainContent');
  main.innerHTML = \`
    <div class="ai-chat-wrap">
      <div class="ai-toolbar">
        <button class="btn btn-secondary" id="aiClearBtn" style="font-size:12px;padding:8px 12px;">\${t('assistant.clear')}</button>
      </div>
      <div id="aiMessages" class="ai-messages"></div>
      <div id="aiThinking" class="ai-bubble-row assistant" style="display:none;">
        <div class="ai-bubble assistant ai-thinking">\${t('assistant.thinking')}</div>
      </div>
      <div class="ai-input-bar">
        <input class="form-input" id="aiInput" placeholder="\${t('assistant.placeholder')}" autocomplete="off">
        <button class="btn btn-primary" id="aiSendBtn" style="width:auto;padding:0 18px;">\${t('assistant.send')}</button>
      </div>
    </div>
  \`;

  const messagesEl = document.getElementById('aiMessages');
  let history = [];
  try { history = await api('/ai/messages'); } catch (e) { /* شروع تازه در صورت خطا */ }
  messagesEl.innerHTML = history.length === 0
    ? '<div class="ai-bubble-row assistant"><div class="ai-bubble assistant">' + t('assistant.emptyState').replace(/\\n/g,'<br>') + '</div></div>'
    : history.map(aiBubbleHtml).join('');
  messagesEl.scrollTop = messagesEl.scrollHeight;

  const input = document.getElementById('aiInput');
  const sendBtn = document.getElementById('aiSendBtn');
  const thinkingEl = document.getElementById('aiThinking');

  async function sendMessage() {
    const text = input.value.trim();
    if (!text || sendBtn.disabled) return;
    input.value = '';
    sendBtn.disabled = true;
    if (messagesEl.querySelector('.ai-bubble-row') && history.length === 0) messagesEl.innerHTML = '';
    messagesEl.insertAdjacentHTML('beforeend', aiBubbleHtml({ role: 'user', content: text }));
    messagesEl.scrollTop = messagesEl.scrollHeight;
    thinkingEl.style.display = 'flex';
    messagesEl.parentElement.appendChild(thinkingEl); // نگه‌داشتنِ نشانگر «در حال فکر کردن» در انتهای گفتگو
    try {
      const reply = await api('/ai/chat', { method: 'POST', body: { message: text } });
      history.push({ role: 'user', content: text }, reply);
      thinkingEl.style.display = 'none';
      messagesEl.insertAdjacentHTML('beforeend', aiBubbleHtml(reply));
    } catch (err) {
      thinkingEl.style.display = 'none';
      toast('❌ ' + (err.message || t('assistant.error')));
    } finally {
      sendBtn.disabled = false;
      messagesEl.scrollTop = messagesEl.scrollHeight;
      input.focus();
    }
  }

  sendBtn.onclick = sendMessage;
  input.onkeydown = (e) => { if (e.key === 'Enter') sendMessage(); };

  document.getElementById('aiClearBtn').onclick = async () => {
    const ok = await confirmDialog(t('assistant.clearConfirm'));
    if (!ok) return;
    try {
      await api('/ai/clear', { method: 'POST' });
      history = [];
      messagesEl.innerHTML = '<div class="ai-bubble-row assistant"><div class="ai-bubble assistant">' + t('assistant.emptyState').replace(/\\n/g,'<br>') + '</div></div>';
    } catch (err) { toast('❌ ' + err.message); }
  };
}

// ===== Settings =====
async function renderSettings() {
  const main = document.getElementById('mainContent');
  let pinStatus = { enabled: false };
  try { pinStatus = await api('/security/pin/status'); } catch (e) { /* پیش‌فرض: غیرفعال */ }
  main.innerHTML = \`
    <div class="card">
      <div class="card-title">\${t('settings.language')}</div>
      <div class="theme-grid">
        <div class="theme-swatch \${state.lang==='fa'?'active':''}" data-lang="fa">🇮🇷 فارسی</div>
        <div class="theme-swatch \${state.lang==='en'?'active':''}" data-lang="en">🇬🇧 English</div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">\${t('settings.calendarType')}</div>
      <div class="theme-grid">
        <div class="theme-swatch \${state.calendarType==='jalali'?'active':''}" data-caltype="jalali">\${t('settings.calendarJalali')}</div>
        <div class="theme-swatch \${state.calendarType==='gregorian'?'active':''}" data-caltype="gregorian">\${t('settings.calendarGregorian')}</div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">\${t('settings.theme')}</div>
      <div class="theme-grid">
        \${THEMES.map(th => '<div class="theme-swatch '+(state.theme===th.id?'active':'')+'" data-theme="'+th.id+'">'+th.label+'</div>').join('')}
      </div>
    </div>
    <div class="card">
      <div class="card-title">\${t('security.pinLock')}</div>
      <div style="font-size:13px;color:var(--text-secondary);margin-bottom:10px;">\${t('security.pinLockDesc')}</div>
      \${pinStatus.enabled ? \`
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <button class="btn btn-secondary" id="pinChangeBtn" style="flex:1;">\${t('security.changePin')}</button>
          <button class="btn btn-danger" id="pinRemoveBtn" style="flex:1;">\${t('security.removePin')}</button>
        </div>
      \` : \`
        <button class="btn btn-primary" id="pinSetBtn">\${t('security.setPin')}</button>
      \`}
    </div>
    <div class="card">
      <div class="card-title">\${t('support.title')}</div>
      <div style="font-size:13px;color:var(--text-secondary);margin-bottom:10px;">\${t('support.settingsDesc')}</div>
      <button class="btn btn-secondary" id="openSupportBtn">\${t('support.openButton')}</button>
    </div>
    <div class="card">
      <div class="card-title">\${t('settings.stats')}</div>
      <button class="btn btn-secondary" id="goAnalytics">\${t('settings.viewFullStats')}</button>
    </div>
    <div class="card">
      <div class="card-title">\${t('settings.about')}</div>
      <div style="font-size:13px;color:var(--text-secondary);line-height:1.8;">\${t('settings.aboutText')}<br>Built with Cloudflare Workers</div>
    </div>
  \`;
  document.querySelectorAll('[data-lang]').forEach(el => {
    el.onclick = async () => {
      setLang(el.dataset.lang);
      haptic('light');
      try { await api('/settings', { method:'PATCH', body: { language_code: el.dataset.lang } }); } catch(e) {}
      navigate('settings'); // بازرندر کامل صفحه با زبان جدید، بدون نیاز به ری‌استارت اپ
    };
  });
  document.querySelectorAll('[data-caltype]').forEach(el => {
    el.onclick = async () => {
      state.calendarType = el.dataset.caltype;
      localStorage.setItem('lm_caltype', state.calendarType);
      haptic('light');
      try { await api('/settings', { method:'PATCH', body: { calendar_type: state.calendarType } }); } catch(e) {}
      navigate('settings'); // بازرندر تنظیمات برای فعال‌شدن گزینهٔ انتخابی
    };
  });
  document.querySelectorAll('[data-theme]').forEach(el => {
    el.onclick = async () => {
      applyTheme(el.dataset.theme);
      document.querySelectorAll('[data-theme]').forEach(o => o.classList.remove('active'));
      el.classList.add('active');
      haptic('light');
      try { await api('/settings', { method:'PATCH', body: { theme: el.dataset.theme } }); } catch(e) {}
    };
  });
  document.getElementById('goAnalytics').onclick = () => navigate('analytics');
  const supportBtn = document.getElementById('openSupportBtn');
  if (supportBtn) supportBtn.onclick = openSupportSheet;
  const setBtn = document.getElementById('pinSetBtn');
  if (setBtn) setBtn.onclick = openPinSetForm;
  const changeBtn = document.getElementById('pinChangeBtn');
  if (changeBtn) changeBtn.onclick = openPinChangeForm;
  const removeBtn = document.getElementById('pinRemoveBtn');
  if (removeBtn) removeBtn.onclick = openPinRemoveForm;
}

async function openSupportSheet() {
  openSheet(t('support.title'), \`
    <div class="tg-banner-preview">
      <div class="tg-banner-img-wrap"><img id="supportBannerImg" class="tg-banner-img" alt=""></div>
      <div class="tg-banner-caption" id="supportBannerCaption">\${t('support.loadingPreview')}</div>
      <div class="tg-banner-button">🚀 \${t('support.inlineButtonLabel')}</div>
    </div>
    <button class="btn btn-primary support-share-btn" id="doShareBtn" style="margin-top:14px;">\${t('support.shareButton')}</button>
    <div class="form-hint" style="text-align:center;margin-top:8px;">\${t('support.shareHint')}</div>
  \`);

  // بارگذاری پیش‌نمایش واقعی بنر (همان تصویر و متنی که واقعاً ارسال می‌شود)
  (async () => {
    try {
      const [info, blob] = await Promise.all([
        api('/support/share-info'),
        apiDownloadBlob('/support/banner-image'),
      ]);
      document.getElementById('supportBannerImg').src = URL.createObjectURL(blob);
      document.getElementById('supportBannerCaption').innerHTML = escapeHtmlKeepBasicTags(info.caption).replace(/\\n/g, '<br>');
    } catch (err) {
      document.getElementById('supportBannerCaption').textContent = t('support.previewError');
    }
  })();

  document.getElementById('doShareBtn').onclick = async () => {
    const shareBtn = document.getElementById('doShareBtn');
    if (shareBtn.disabled) return;
    shareBtn.disabled = true;
    const originalLabel = shareBtn.textContent;
    shareBtn.textContent = t('support.sending');
    try {
      await api('/support/send-banner', { method: 'POST' });
      haptic('medium');
      toast('✅ ' + t('support.sentSuccess'));
      closeSheet();
    } catch (err) {
      toast('❌ ' + err.message);
    } finally {
      shareBtn.disabled = false;
      shareBtn.textContent = originalLabel;
    }
  };
}

// برای نمایش امنِ کپشنِ بنر (که شامل چند تگ HTML ساده مثل <b> است) بدون باز گذاشتن
// دست برای تزریق تگ‌های دلخواه — فقط <b>/<i>/<u> مجاز نگه داشته می‌شوند
function escapeHtmlKeepBasicTags(text) {
  const escaped = escapeHtml(text || '');
  return escaped
    .replace(/&lt;(\\/?)(b|i|u)&gt;/gi, '<$1$2>');
}

function pinInputHtml(id, label) {
  return \`<div class="form-group"><label class="form-label">\${label}</label>
    <input class="form-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="8" id="\${id}"></div>\`;
}

function openPinSetForm() {
  openSheet(t('security.setPin'), \`
    \${pinInputHtml('pinNew1', t('security.newPin'))}
    \${pinInputHtml('pinNew2', t('security.confirmPin'))}
    <div class="form-hint">\${t('security.pinFormatHint')}</div>
    <button class="btn btn-primary" id="pinSetSave">\${t('common.save')}</button>
  \`);
  document.getElementById('pinSetSave').onclick = async () => {
    const p1 = document.getElementById('pinNew1').value.trim();
    const p2 = document.getElementById('pinNew2').value.trim();
    if (!/^[0-9]{4,8}$/.test(p1)) { toast('⚠️ ' + t('security.pinFormatHint')); return; }
    if (p1 !== p2) { toast('⚠️ ' + t('security.pinMismatch')); return; }
    try {
      await api('/security/pin/set', { method: 'POST', body: { pin: p1 } });
      toast('✅ ' + t('security.pinSetSuccess'));
      closeSheet(); navigate('settings');
    } catch (err) { toast('❌ ' + err.message); }
  };
}

function openPinChangeForm() {
  openSheet(t('security.changePin'), \`
    \${pinInputHtml('pinCur', t('security.currentPin'))}
    \${pinInputHtml('pinNew1', t('security.newPin'))}
    \${pinInputHtml('pinNew2', t('security.confirmPin'))}
    <button class="btn btn-primary" id="pinChangeSave">\${t('common.save')}</button>
  \`);
  document.getElementById('pinChangeSave').onclick = async () => {
    const cur = document.getElementById('pinCur').value.trim();
    const p1 = document.getElementById('pinNew1').value.trim();
    const p2 = document.getElementById('pinNew2').value.trim();
    if (!/^[0-9]{4,8}$/.test(p1)) { toast('⚠️ ' + t('security.pinFormatHint')); return; }
    if (p1 !== p2) { toast('⚠️ ' + t('security.pinMismatch')); return; }
    try {
      await api('/security/pin/change', { method: 'POST', body: { currentPin: cur, newPin: p1 } });
      toast('✅ ' + t('security.pinChangeSuccess'));
      closeSheet(); navigate('settings');
    } catch (err) { toast('❌ ' + err.message); }
  };
}

function openPinRemoveForm() {
  openSheet(t('security.removePin'), \`
    <div class="form-hint" style="margin-bottom:10px;">\${t('security.removeConfirmHint')}</div>
    \${pinInputHtml('pinCur', t('security.currentPin'))}
    <button class="btn btn-danger" id="pinRemoveSave">\${t('security.removePin')}</button>
  \`);
  document.getElementById('pinRemoveSave').onclick = async () => {
    const cur = document.getElementById('pinCur').value.trim();
    const ok = await confirmDialog(t('security.removeConfirmHint'));
    if (!ok) return;
    try {
      await api('/security/pin/remove', { method: 'POST', body: { currentPin: cur } });
      toast('✅ ' + t('security.pinRemoveSuccess'));
      closeSheet(); navigate('settings');
    } catch (err) { toast('❌ ' + err.message); }
  };
}

// ===== FAB =====
function handleFabClick() {
  haptic('medium');
  if (state.page === 'tasks' || state.page === 'dashboard') openTaskForm(null);
  else if (state.page === 'habits') openHabitForm();
  else if (state.page === 'notes') openNoteForm(null);
  else openTaskForm(null);
}

// ===== قفل امنیتی (PIN Lock Screen) =====
// این Overlay قبل از هرگونه navigate/renderDashboard نمایش داده می‌شود، بنابراین
// تا وقتی رمز درست وارد نشود هیچ اطلاعات شخصی کاربر fetch یا render نمی‌شود.
function showPinLockScreen(onUnlocked, initialStatus) {
  const overlay = document.createElement('div');
  overlay.id = 'pinLockOverlay';
  overlay.className = 'pin-lock-overlay';
  overlay.innerHTML = \`
    <div class="pin-lock-box">
      <div class="pin-lock-icon">🔒</div>
      <div class="pin-lock-title">\${t('security.enterPin')}</div>
      <input type="password" inputmode="numeric" pattern="[0-9]*" maxlength="8" class="form-input pin-lock-input" id="pinLockInput">
      <div class="pin-lock-error" id="pinLockError"></div>
      <button class="btn btn-primary" id="pinLockSubmit">\${t('security.unlock')}</button>
    </div>
  \`;
  document.body.appendChild(overlay);
  const input = document.getElementById('pinLockInput');
  const errEl = document.getElementById('pinLockError');
  const submitBtn = document.getElementById('pinLockSubmit');
  let lockTimer = null;

  function setLockedMessage(sec) {
    let remaining = Math.max(1, Math.ceil(sec));
    submitBtn.disabled = true;
    input.disabled = true;
    errEl.textContent = t('security.locked').replace('{sec}', remaining);
    if (lockTimer) clearInterval(lockTimer);
    lockTimer = setInterval(() => {
      remaining--;
      if (remaining <= 0) {
        clearInterval(lockTimer);
        errEl.textContent = '';
        submitBtn.disabled = false;
        input.disabled = false;
        input.focus();
      } else {
        errEl.textContent = t('security.locked').replace('{sec}', remaining);
      }
    }, 1000);
  }

  if (initialStatus && initialStatus.locked) setLockedMessage(initialStatus.locked_seconds_left);

  async function attempt() {
    const pin = input.value.trim();
    if (!pin || submitBtn.disabled) return;
    submitBtn.disabled = true;
    try {
      await api('/security/pin/verify', { method: 'POST', body: { pin } });
      overlay.remove();
      onUnlocked();
    } catch (err) {
      input.value = '';
      submitBtn.disabled = false;
      const data = err.data || {};
      if (data.locked) setLockedMessage(data.locked_seconds_left);
      else if (typeof data.attempts_left === 'number') errEl.textContent = t('security.wrongPinAttempts').replace('{n}', data.attempts_left);
      else errEl.textContent = t('security.wrongPin');
      input.focus();
    }
  }
  submitBtn.onclick = attempt;
  input.onkeydown = (e) => { if (e.key === 'Enter') attempt(); };
  setTimeout(() => input.focus(), 50);
}

// ===== Init =====
async function init() {
  applyTheme(state.theme);
  document.documentElement.setAttribute('lang', state.lang === 'en' ? 'en' : 'fa');
  document.documentElement.setAttribute('dir', state.lang === 'en' ? 'ltr' : 'rtl');
  applyStaticI18n();
  document.querySelectorAll('.nav-item').forEach(el => el.onclick = () => navigate(el.dataset.page));
  document.getElementById('fab').onclick = handleFabClick;
  document.getElementById('searchBtn').onclick = () => navigate('search');
  document.getElementById('assistantBtn').onclick = () => navigate('assistant');
  document.getElementById('settingsBtn').onclick = () => navigate('settings');

  // بررسی قفل امنیتی PIN — قبل از هرگونه نمایش داده‌های شخصی کاربر
  let pinStatus = { enabled: false };
  try { pinStatus = await api('/security/pin/status'); } catch (e) { /* در صورت خطا، برای جلوگیری از قفل‌شدن کاربر، ادامه می‌دهیم */ }
  if (pinStatus.enabled) {
    showPinLockScreen(bootApp, pinStatus);
  } else {
    bootApp();
  }
}

async function bootApp() {
  navigate('dashboard');
  // همگام‌سازی زبان ذخیره‌شدهٔ کاربر در سرور + ثبت منطقهٔ زمانی واقعی دستگاه
  // (برای رفع باگ یادآورها: بدون این مقدار، سرور همیشه تهران را فرض می‌کرد)
  try {
    const settings = await api('/settings');
    if (settings.language_code && settings.language_code !== state.lang && ['fa','en'].includes(settings.language_code)) {
      setLang(settings.language_code);
      applyStaticI18n();
    }
    if (settings.calendar_type && ['jalali','gregorian'].includes(settings.calendar_type) && settings.calendar_type !== state.calendarType) {
      state.calendarType = settings.calendar_type;
      localStorage.setItem('lm_caltype', state.calendarType);
    }
    const detectedTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (detectedTz && detectedTz !== settings.timezone) {
      await api('/settings', { method: 'PATCH', body: { timezone: detectedTz } });
    }
  } catch (e) { /* بی‌اهمیت: تنظیمات پیش‌فرض کار می‌کنند */ }
}

document.addEventListener('DOMContentLoaded', init);
})();
`;

const FULL_JS = JS_TEXT + JS_TEXT_2 + JS_TEXT_3 + JS_TEXT_4;

// ============================================================
// 11) ASSEMBLE FINAL HTML PAGE
// ============================================================
function renderMiniAppPage() {
  return HTML_PAGE
    .replace('/*__CSS_PLACEHOLDER__*/', CSS_TEXT)
    .replace('/*__JS_PLACEHOLDER__*/', FULL_JS);
}

// ============================================================
// 12) MAIN WORKER EXPORT
// ============================================================
export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);

    try {
      // --- Bot Webhook ---
      if (url.pathname === '/webhook/bot' && request.method === 'POST') {
        return handleWebhook(request, env);
      }

      // --- REST API ---
      if (url.pathname.startsWith('/api/v1/')) {
        return handleApi(request, env, ctx, url);
      }

      // --- Mini App (Static SPA) ---
      if (url.pathname === '/' || url.pathname === '/app' || url.pathname === '/index.html') {
        return new Response(renderMiniAppPage(), {
          headers: { 'Content-Type': 'text/html; charset=utf-8' },
        });
      }

      // --- Setup helper: ثبت خودکار Webhook در تلگرام (فقط برای دیپلوی اولیه) ---
      if (url.pathname === '/setup' && request.method === 'GET') {
        const webhookUrl = `${url.origin}/webhook/bot`;
        const res = await fetch(telegramApi(env.BOT_TOKEN, 'setWebhook'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            url: webhookUrl,
            secret_token: env.WEBHOOK_SECRET || undefined,
            allowed_updates: ['message'],
          }),
        });
        const data = await res.json();
        return json({ ok: true, webhook_url: webhookUrl, telegram_response: data });
      }

      return errorResponse('یافت نشد', 404);
    } catch (err) {
      return errorResponse('خطای پیش‌بینی‌نشده سرور', 500, err.message);
    }
  },

  async scheduled(event, env, ctx) {
    ctx.waitUntil(dispatchReminders(env));
    ctx.waitUntil(generateRecurringTaskInstances(env));
  },
};