/**
 * ربات ارسال پست به کانال با پشتیبانی از ایموجی پرمیوم (Custom Emoji)
 * روی Cloudflare Worker + D1 Database اجرا می‌شود
 */

const EMOJI_REFERENCE_CHANNEL = "@YourEmojiReferenceChannel";
const DONATE_URL = "https://t.me/YourUsername";

const COLOR_CYCLE = ["✦", "✧", "✦"];
function colorLabel(index, label) {
  return `${COLOR_CYCLE[index % COLOR_CYCLE.length]} ${label}`;
}

function escapeHtml(str) {
  return String(str ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

const T = {
  welcome:
    "🔸 <b>خوش اومدی به ربات ایموجی پرمیوم</b>\n\nیکی از گزینه‌های زیر رو انتخاب کن 🔸",
  adOnce:
    "👋 خوش اومدی! برای شروع از دکمه‌های زیر استفاده کن.",
  registerInstructions:
    "🔸 <b>ثبت کانال جدید</b>\n\n1️⃣ ربات رو توی کانالت <b>ادمین</b> کن و دسترسی «ارسال و مدیریت پست‌ها» (Post Messages / Manage Messages) رو بهش بده\n2️⃣ یه پست از همون کانال رو برای ربات <b>فوروارد</b> کن",
  registerForceInstructions:
    "🔸 <b>افزودن کانال جوین اجباری</b>\n\n1️⃣ ربات رو توی کانال/گروه مورد نظر عضو یا ادمین کن\n2️⃣ یه پست از همون کانال/گروه رو برای ربات <b>فوروارد</b> کن",
  noPermission: (missing) =>
    `❗️ ربات توی این کانال دسترسی لازم رو نداره.\n\n<b>مشکل:</b> ${escapeHtml(missing)}\n\nلطفاً دسترسی «ارسال پیام» رو به ربات بده و دوباره یه پست فوروارد کن.`,
  channelRegistered: (title, username) =>
    `🏵 <b>اطلاعات کانال</b>\n\n• نام: ${escapeHtml(title)}\n• یوزرنیم: ${username ? "@" + escapeHtml(username) : "ندارد"}\n\nکانال با موفقیت ثبت شد 🔸`,
  forceChannelRegistered: (title) =>
    `🏵 کانال/گروه «${escapeHtml(title)}» به لیست جوین اجباری اضافه شد ✅`,
  forceChannelDeleted: "🗑 کانال از لیست جوین اجباری حذف شد.",
  noChannels: "❗️ هنوز هیچ کانالی ثبت نکردی. اول از «ثبت کانال» استفاده کن.",
  chooseChannel: "کانالی که می‌خوای پست توش ارسال بشه رو انتخاب کن:",
  choosePostType: "نوع پستت رو انتخاب کن:",
  askText:
    `📝 <b>متن پستت رو بنویس</b>\n\nمثال: سلام [5417984163095542602]\n\nبرای گذاشتن ایموجی پرمیوم، آیدی عددیش رو داخل [ ] بنویس.\nاگه آیدی رو نمی‌دونی، از کانال ${EMOJI_REFERENCE_CHANNEL} می‌تونی ببینی و برداری.`,
  askMedia: (kind) => `لطفاً <b>${escapeHtml(kind)}</b> مورد نظرت رو بفرست یا فوروارد کن.`,
  askCaption: "می‌خوای براش کپشن بذاری؟ متنش رو بفرست، یا بنویس «بدون کپشن».",
  previewHeader: "🔎 <b>پیش‌نمایش پستت</b> 👇 آیا همین ارسال بشه؟",
  sentOk: "✅ پست با موفقیت ارسال شد 🔸",
  cancelled: "❌ ارسال لغو شد.",
  badEntities:
    "❗️ یکی از آیدی‌های ایموجی که وارد کردی معتبر نیست، یا مشکلی در فرمت متن پیش اومد. لطفاً دوباره متن رو بفرست.",
  support:
    "🛟 <b>راهنما</b>\n\n1️⃣ برای ثبت کانال: ربات رو ادمین کانالت کن و یه پست ازش فوروارد کن.\n2️⃣ برای ارسال پست: از منوی اصلی «ارسال پست» رو بزن و مراحل رو طی کن.\n3️⃣ آیدی ایموجی‌های پرمیوم رو از کانال " +
    EMOJI_REFERENCE_CHANNEL +
    " بردار.\n\nبرای سوال بیشتر با پشتیبانی ربات در ارتباط باش.",
  donateIntro:
    "با دونیت و حمایت شما، اشتراک پرمیوم ربات تمدید می‌شه و همزمان روی آپدیت‌ها و امکانات ربات جدید هم کار می‌کنیم.\n\nاز همراهی و لطفت ممنونیم! 🙏",
  donateFooter:
    "با تشکر از حمایت شما 🙏",
  denied: "⛔️ ربات در حال حاضر خصوصیه و فقط سازنده بهش دسترسی داره.",
  genericError: "❗️ یه خطایی پیش اومد. دوباره امتحان کن.",
  settingsHeader: (mode) =>
    `⚙️ <b>تنظیمات ربات</b>\n\nحالت فعلی: ${mode === "public" ? "🟢 عمومی" : "🔴 خصوصی"}\n\nدر حالت عمومی همه کاربرها می‌تونن از ربات استفاده کنن.`,
  forceListHeader: (count) =>
    `📋 <b>کانال‌های جوین اجباری</b> (${count})\n\nکاربرها برای استفاده از ربات (در حالت عمومی) باید عضو همه این‌ها باشن.`,
  mustJoin:
    "⛔️ <b>برای استفاده از ربات باید عضو کانال/گروه‌های زیر بشی</b>\n\nبعد از عضویت، دکمه «بررسی مجدد» رو بزن.",
  stillNotMember: "❗️ هنوز عضو همه‌شون نشدی.",
  joinedOk: "✅ عضویت تایید شد، خوش اومدی!",
};

function timingSafeEqual(a, b) {
  const encoder = new TextEncoder();
  const bufA = encoder.encode(String(a ?? ""));
  const bufB = encoder.encode(String(b ?? ""));
  const len = Math.max(bufA.length, bufB.length, 1);
  let diff = bufA.length ^ bufB.length;
  for (let i = 0; i < len; i++) {
    const byteA = i < bufA.length ? bufA[i] : 0;
    const byteB = i < bufB.length ? bufB[i] : 0;
    diff |= byteA ^ byteB;
  }
  return diff === 0;
}

const EMOJI_FALLBACK_GLYPH = "🔸";

function applyEmojiPlaceholders(text, sourceEntities) {
  const entities = Array.isArray(sourceEntities)
    ? sourceEntities.map((e) => ({ ...e }))
    : [];

  const placeholderRe = /\[(\d{1,20})\]/g;
  const matches = [...text.matchAll(placeholderRe)];

  if (matches.length === 0) {
    return { text, entities };
  }

  const fbLen = EMOJI_FALLBACK_GLYPH.length;

  let resultText = "";
  let cursor = 0;
  const emojiEntities = [];
  const breakpoints = [];
  let runningDelta = 0;

  for (const m of matches) {
    const oldStart = m.index;
    const oldLen = m[0].length;
    const emojiId = m[1];

    resultText += text.slice(cursor, oldStart) + EMOJI_FALLBACK_GLYPH;
    const newStart = resultText.length - fbLen;

    emojiEntities.push({
      type: "custom_emoji",
      offset: newStart,
      length: fbLen,
      custom_emoji_id: emojiId,
    });

    runningDelta += fbLen - oldLen;
    breakpoints.push({ oldOffsetAfter: oldStart + oldLen, delta: runningDelta });
    cursor = oldStart + oldLen;
  }
  resultText += text.slice(cursor);

  function adjustOffset(offset) {
    let delta = 0;
    for (const bp of breakpoints) {
      if (offset >= bp.oldOffsetAfter) delta = bp.delta;
      else break;
    }
    return offset + delta;
  }

  const adjustedFormatting = entities.map((e) => ({
    ...e,
    offset: adjustOffset(e.offset),
  }));

  const finalEntities = [...adjustedFormatting, ...emojiEntities].sort(
    (a, b) => a.offset - b.offset
  );

  return { text: resultText, entities: finalEntities };
}

async function deliverPost(env, chatId, postType, text, entities, fileId) {
  if (postType === "text") {
    return tg(env, "sendMessage", {
      chat_id: chatId,
      text: text || "",
      entities: entities || [],
      disable_web_page_preview: true,
    });
  }

  const payload = { chat_id: chatId };
  if (text) {
    payload.caption = text;
    payload.caption_entities = entities || [];
  }

  if (postType === "photo") return tg(env, "sendPhoto", { ...payload, photo: fileId });
  if (postType === "video") return tg(env, "sendVideo", { ...payload, video: fileId });
  if (postType === "document")
    return tg(env, "sendDocument", { ...payload, document: fileId });
  return { ok: false, error: "unknown post type" };
}

async function forwardPost(env, toChatId, fromChatId, messageId) {
  return tg(env, "forwardMessage", {
    chat_id: toChatId,
    from_chat_id: fromChatId,
    message_id: messageId,
  });
}

function jsonResponse(obj, status = 200) {
  return new Response(JSON.stringify(obj), {
    status,
    headers: { "content-type": "application/json" },
  });
}

function textResponse(text, status = 200) {
  return new Response(text, { status });
}

function tgApi(env, method) {
  return `https://api.telegram.org/bot${env.BOT_TOKEN}/${method}`;
}

async function tg(env, method, payload) {
  try {
    const res = await fetch(tgApi(env, method), {
      method: "POST",
      headers: { "content-type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json().catch(() => null);
    if (!data || data.ok !== true) {
      return { ok: false, error: data, status: res.status };
    }
    return { ok: true, result: data.result };
  } catch (err) {
    return { ok: false, error: String(err) };
  }
}

async function sendMessage(env, chatId, text, extra = {}) {
  return tg(env, "sendMessage", {
    chat_id: chatId,
    text,
    parse_mode: extra.entities ? undefined : "HTML",
    disable_web_page_preview: true,
    ...extra,
  });
}

async function editOrSend(env, chatId, messageId, text, extra = {}) {
  if (messageId) {
    const editResult = await tg(env, "editMessageText", {
      chat_id: chatId,
      message_id: messageId,
      text,
      parse_mode: extra.entities ? undefined : "HTML",
      disable_web_page_preview: true,
      ...extra,
    });
    if (editResult.ok) return editResult;
  }
  return sendMessage(env, chatId, text, extra);
}

async function answerCallbackQuery(env, callbackQueryId, extra = {}) {
  try {
    await tg(env, "answerCallbackQuery", {
      callback_query_id: callbackQueryId,
      ...extra,
    });
  } catch (_) {
  }
}

function mainMenuKeyboard(isAdmin) {
  const rows = [
    [
      { text: colorLabel(0, "ارسال پست ✍️"), callback_data: "menu:send" },
      { text: colorLabel(1, "ثبت کانال ☸️"), callback_data: "menu:register" },
    ],
    [
      { text: colorLabel(2, "پشتیبانی 🛟"), callback_data: "menu:support" },
      { text: colorLabel(0, "حمایت از سازنده 💎"), callback_data: "menu:donate" },
    ],
  ];
  if (isAdmin) {
    rows.push([{ text: colorLabel(1, "تنظیمات ربات ⚙️"), callback_data: "menu:settings" }]);
  }
  return { inline_keyboard: rows };
}

function backButtonRow(callbackData = "nav:back_main") {
  return [{ text: colorLabel(2, "بازگشت 🔙"), callback_data: callbackData }];
}

function backOnlyKeyboard(callbackData = "nav:back_main") {
  return { inline_keyboard: [backButtonRow(callbackData)] };
}

function channelListKeyboard(channels) {
  const rows = channels.map((c, i) => [
    { text: colorLabel(i, c.title), callback_data: `pickchan:${c.id}` },
  ]);
  rows.push(backButtonRow());
  return { inline_keyboard: rows };
}

function postTypeKeyboard() {
  return {
    inline_keyboard: [
      [
        { text: colorLabel(0, "عکس 🖼"), callback_data: "posttype:photo" },
        { text: colorLabel(1, "فیلم 🎬"), callback_data: "posttype:video" },
      ],
      [
        { text: colorLabel(2, "فایل 📁"), callback_data: "posttype:document" },
        { text: colorLabel(0, "متن 📝"), callback_data: "posttype:text" },
      ],
      backButtonRow(),
    ],
  };
}

function confirmKeyboard() {
  return {
    inline_keyboard: [
      [
        { text: colorLabel(1, "بله، ارسال کن ✅"), callback_data: "confirm:yes" },
        { text: colorLabel(0, "لغو ❌"), callback_data: "confirm:no" },
      ],
    ],
  };
}

function donateKeyboard() {
  return {
    inline_keyboard: [
      [{ text: colorLabel(1, "دونیت / حمایت 💎"), url: DONATE_URL }],
      backButtonRow(),
    ],
  };
}

function settingsKeyboard(mode) {
  return {
    inline_keyboard: [
      [
        {
          text: colorLabel(mode === "public" ? 0 : 1, mode === "public" ? "سوییچ به خصوصی 🔴" : "سوییچ به عمومی 🟢"),
          callback_data: "settings:togglemode",
        },
      ],
      [{ text: colorLabel(2, "مدیریت جوین اجباری 📋"), callback_data: "menu:forcelist" }],
      backButtonRow(),
    ],
  };
}

function forceListKeyboard(channels) {
  const rows = channels.map((c, i) => [
    { text: colorLabel(i, c.title), url: c.username ? `https://t.me/${c.username}` : (c.invite_link || undefined) },
    { text: "🗑", callback_data: `fcdel:${c.id}` },
  ]);
  rows.push([{ text: colorLabel(1, "افزودن کانال ➕"), callback_data: "fcadd" }]);
  rows.push(backButtonRow("menu:settings"));
  return { inline_keyboard: rows };
}

function mustJoinKeyboard(channels) {
  const rows = channels.map((c, i) => [
    {
      text: colorLabel(i, c.title),
      url: c.username ? `https://t.me/${c.username}` : c.invite_link,
    },
  ]);
  rows.push([{ text: colorLabel(2, "بررسی مجدد 🔄"), callback_data: "fcheck" }]);
  return { inline_keyboard: rows };
}

const SCHEMA_STATEMENTS = [
  `CREATE TABLE IF NOT EXISTS channels (
     id INTEGER PRIMARY KEY AUTOINCREMENT,
     chat_id INTEGER NOT NULL UNIQUE,
     title TEXT NOT NULL,
     username TEXT,
     owner_id INTEGER,
     added_at INTEGER NOT NULL
   )`,
  `CREATE TABLE IF NOT EXISTS sessions (
     user_id INTEGER PRIMARY KEY,
     state TEXT NOT NULL DEFAULT 'idle',
     context TEXT,
     updated_at INTEGER NOT NULL
   )`,
  `CREATE TABLE IF NOT EXISTS posts_log (
     id INTEGER PRIMARY KEY AUTOINCREMENT,
     channel_id INTEGER NOT NULL,
     message_id INTEGER,
     post_type TEXT,
     sent_at INTEGER,
     FOREIGN KEY (channel_id) REFERENCES channels(id)
   )`,
  `CREATE TABLE IF NOT EXISTS setup_meta (
     key TEXT PRIMARY KEY,
     value TEXT
   )`,
  `CREATE TABLE IF NOT EXISTS bot_users (
     user_id INTEGER PRIMARY KEY,
     welcomed INTEGER NOT NULL DEFAULT 0,
     joined_at INTEGER NOT NULL
   )`,
  `CREATE TABLE IF NOT EXISTS force_channels (
     id INTEGER PRIMARY KEY AUTOINCREMENT,
     chat_id INTEGER NOT NULL UNIQUE,
     title TEXT NOT NULL,
     username TEXT,
     invite_link TEXT,
     added_at INTEGER NOT NULL
   )`,
];

async function runSchema(db) {
  for (const stmt of SCHEMA_STATEMENTS) {
    await db.prepare(stmt).run();
  }
}

async function getSetting(db, key, fallback = null) {
  const row = await db.prepare("SELECT value FROM setup_meta WHERE key = ?").bind(key).first();
  return row ? row.value : fallback;
}

async function setSetting(db, key, value) {
  await db
    .prepare(
      `INSERT INTO setup_meta (key, value) VALUES (?, ?)
       ON CONFLICT(key) DO UPDATE SET value = excluded.value`
    )
    .bind(key, value)
    .run();
}

async function getBotMode(db) {
  const mode = await getSetting(db, "bot_mode", "private");
  return mode === "public" ? "public" : "private";
}

async function ensureUser(db, userId) {
  const row = await db.prepare("SELECT user_id, welcomed FROM bot_users WHERE user_id = ?").bind(userId).first();
  if (row) return row;
  await db
    .prepare("INSERT INTO bot_users (user_id, welcomed, joined_at) VALUES (?, 0, ?)")
    .bind(userId, Date.now())
    .run();
  return { user_id: userId, welcomed: 0 };
}

async function markWelcomed(db, userId) {
  await db.prepare("UPDATE bot_users SET welcomed = 1 WHERE user_id = ?").bind(userId).run();
}

async function getSession(db, userId) {
  const row = await db
    .prepare("SELECT user_id, state, context, updated_at FROM sessions WHERE user_id = ?")
    .bind(userId)
    .first();
  if (!row) {
    return { user_id: userId, state: "idle", context: {} };
  }
  let context = {};
  try {
    context = row.context ? JSON.parse(row.context) : {};
  } catch (_) {
    context = {};
  }
  return { user_id: userId, state: row.state, context };
}

async function setSession(db, userId, state, context = {}) {
  await db
    .prepare(
      `INSERT INTO sessions (user_id, state, context, updated_at)
       VALUES (?, ?, ?, ?)
       ON CONFLICT(user_id) DO UPDATE SET
         state = excluded.state,
         context = excluded.context,
         updated_at = excluded.updated_at`
    )
    .bind(userId, state, JSON.stringify(context || {}), Date.now())
    .run();
}

async function clearSession(db, userId) {
  await setSession(db, userId, "idle", {});
}

async function listChannels(db, ownerId) {
  const { results } = await db
    .prepare("SELECT id, chat_id, title, username FROM channels WHERE owner_id = ? ORDER BY added_at ASC")
    .bind(ownerId)
    .all();
  return results || [];
}

async function getChannelById(db, id) {
  return db
    .prepare("SELECT id, chat_id, title, username, owner_id FROM channels WHERE id = ?")
    .bind(id)
    .first();
}

async function upsertChannel(db, chatId, title, username, ownerId) {
  await db
    .prepare(
      `INSERT INTO channels (chat_id, title, username, owner_id, added_at)
       VALUES (?, ?, ?, ?, ?)
       ON CONFLICT(chat_id) DO UPDATE SET
         title = excluded.title,
         username = excluded.username,
         owner_id = excluded.owner_id`
    )
    .bind(chatId, title, username || null, ownerId, Date.now())
    .run();
}

async function logPost(db, channelDbId, messageId, postType) {
  await db
    .prepare(
      `INSERT INTO posts_log (channel_id, message_id, post_type, sent_at)
       VALUES (?, ?, ?, ?)`
    )
    .bind(channelDbId, messageId || null, postType, Date.now())
    .run();
}

async function listForceChannels(db) {
  const { results } = await db
    .prepare("SELECT id, chat_id, title, username, invite_link FROM force_channels ORDER BY added_at ASC")
    .all();
  return results || [];
}

async function addForceChannel(db, chatId, title, username, inviteLink) {
  await db
    .prepare(
      `INSERT INTO force_channels (chat_id, title, username, invite_link, added_at)
       VALUES (?, ?, ?, ?, ?)
       ON CONFLICT(chat_id) DO UPDATE SET
         title = excluded.title,
         username = excluded.username,
         invite_link = excluded.invite_link`
    )
    .bind(chatId, title, username || null, inviteLink || null, Date.now())
    .run();
}

async function deleteForceChannel(db, id) {
  await db.prepare("DELETE FROM force_channels WHERE id = ?").bind(id).run();
}

async function checkForceSubscription(env, db, userId) {
  const channels = await listForceChannels(db);
  if (channels.length === 0) return { ok: true, missing: [] };

  const missing = [];
  for (const ch of channels) {
    const res = await tg(env, "getChatMember", { chat_id: ch.chat_id, user_id: userId });
    if (!res.ok) {
      missing.push(ch);
      continue;
    }
    const status = res.result.status;
    if (status === "left" || status === "kicked") {
      missing.push(ch);
    }
  }
  return { ok: missing.length === 0, missing, all: channels };
}

async function handleUpdate(update, env, ctx) {
  const db = env.DB;
  const adminId = Number(env.ADMIN_ID);

  try {
    if (update.callback_query) {
      await handleCallback(update.callback_query, env, db, adminId);
      return;
    }
    if (update.message) {
      await handleMessage(update.message, env, db, adminId);
      return;
    }
  } catch (err) {
    console.error("handleUpdate error:", err);
  }
}

function isFromAdmin(fromId, adminId) {
  return Number(fromId) === adminId;
}

async function resolveAccess(env, db, adminId, userId) {
  const mode = await getBotMode(db);
  const isAdmin = isFromAdmin(userId, adminId);
  await ensureUser(db, userId);

  if (mode === "private" && !isAdmin) {
    return { allowed: false, isAdmin, mode };
  }
  return { allowed: true, isAdmin, mode };
}

async function sendWelcomeAdIfNeeded(env, db, userId) {
  const row = await db.prepare("SELECT welcomed FROM bot_users WHERE user_id = ?").bind(userId).first();
  if (row && Number(row.welcomed) === 0) {
    await sendMessage(env, userId, T.adOnce);
    await markWelcomed(db, userId);
  }
}

async function handleMessage(msg, env, db, adminId) {
  const fromId = msg.from && msg.from.id;
  const chatId = msg.chat.id;

  const access = await resolveAccess(env, db, adminId, fromId);
  if (!access.allowed) {
    await sendMessage(env, chatId, T.denied);
    return;
  }

  const text = msg.text || "";

  if (text === "/start") {
    await clearSession(db, fromId);
    await sendWelcomeAdIfNeeded(env, db, fromId);

    if (access.mode === "public" && !access.isAdmin) {
      const sub = await checkForceSubscription(env, db, fromId);
      if (!sub.ok) {
        await sendMessage(env, chatId, T.mustJoin, { reply_markup: mustJoinKeyboard(sub.missing) });
        return;
      }
    }

    await sendMessage(env, chatId, T.welcome, { reply_markup: mainMenuKeyboard(access.isAdmin) });
    return;
  }

  if (access.mode === "public" && !access.isAdmin) {
    const sub = await checkForceSubscription(env, db, fromId);
    if (!sub.ok) {
      await sendMessage(env, chatId, T.mustJoin, { reply_markup: mustJoinKeyboard(sub.missing) });
      return;
    }
  }

  const session = await getSession(db, fromId);

  switch (session.state) {
    case "awaiting_channel_forward":
      await handleChannelForward(msg, env, db, fromId, chatId);
      return;

    case "awaiting_force_channel_forward":
      await handleForceChannelForward(msg, env, db, fromId, chatId);
      return;

    case "awaiting_text_content":
      await handleTextContent(msg, env, db, fromId, chatId, session);
      return;

    case "awaiting_media_content":
      await handleMediaContent(msg, env, db, fromId, chatId, session);
      return;

    case "awaiting_caption":
      await handleCaption(msg, env, db, fromId, chatId, session);
      return;

    default:
      await sendMessage(env, chatId, T.welcome, { reply_markup: mainMenuKeyboard(access.isAdmin) });
      return;
  }
}

async function handleChannelForward(msg, env, db, userId, chatId) {
  const originChat =
    (msg.forward_origin &&
      msg.forward_origin.type === "channel" &&
      msg.forward_origin.chat) ||
    msg.forward_from_chat ||
    null;

  if (!originChat) {
    await sendMessage(
      env,
      chatId,
      "❗️ این پیام از یه کانال فوروارد نشده. لطفاً یه پست از کانالت رو مستقیم فوروارد کن.",
      { reply_markup: backOnlyKeyboard() }
    );
    return;
  }

  const targetChatId = originChat.id;

  const meRes = await tg(env, "getMe", {});
  if (!meRes.ok) {
    await sendMessage(env, chatId, T.genericError, { reply_markup: backOnlyKeyboard() });
    return;
  }
  const botId = meRes.result.id;

  const memberRes = await tg(env, "getChatMember", {
    chat_id: targetChatId,
    user_id: botId,
  });

  if (!memberRes.ok) {
    await sendMessage(
      env,
      chatId,
      T.noPermission("ربات هنوز توی این کانال ادمین نیست یا بهش دسترسی داده نشده."),
      { reply_markup: backOnlyKeyboard() }
    );
    return;
  }

  const member = memberRes.result;
  const canPost =
    member.status === "administrator" &&
    (member.can_post_messages === true || member.is_anonymous === undefined
      ? member.can_post_messages === true
      : false);

  if (!canPost) {
    await sendMessage(
      env,
      chatId,
      T.noPermission("دسترسی «ارسال پیام» (Post Messages) به ربات داده نشده."),
      { reply_markup: backOnlyKeyboard() }
    );
    return;
  }

  const title = originChat.title || "بدون نام";
  const username = originChat.username || null;

  await upsertChannel(db, targetChatId, title, username, userId);
  await clearSession(db, userId);

  const access = await resolveAccess(env, db, Number(env.ADMIN_ID), userId);
  await sendMessage(env, chatId, T.channelRegistered(title, username), {
    reply_markup: mainMenuKeyboard(access.isAdmin),
  });
}

async function handleForceChannelForward(msg, env, db, userId, chatId) {
  const originChat =
    (msg.forward_origin &&
      (msg.forward_origin.type === "channel" || msg.forward_origin.type === "chat") &&
      msg.forward_origin.chat) ||
    msg.forward_from_chat ||
    null;

  if (!originChat) {
    await sendMessage(
      env,
      chatId,
      "❗️ این پیام فوروارد نشده. لطفاً یه پست از کانال/گروه مورد نظر رو مستقیم فوروارد کن.",
      { reply_markup: backOnlyKeyboard("menu:forcelist") }
    );
    return;
  }

  const targetChatId = originChat.id;
  const meRes = await tg(env, "getMe", {});
  if (!meRes.ok) {
    await sendMessage(env, chatId, T.genericError, { reply_markup: backOnlyKeyboard("menu:forcelist") });
    return;
  }
  const botId = meRes.result.id;

  const memberRes = await tg(env, "getChatMember", { chat_id: targetChatId, user_id: botId });
  if (!memberRes.ok) {
    await sendMessage(
      env,
      chatId,
      T.noPermission("ربات توی این کانال/گروه عضو نیست."),
      { reply_markup: backOnlyKeyboard("menu:forcelist") }
    );
    return;
  }

  const title = originChat.title || "بدون نام";
  const username = originChat.username || null;
  let inviteLink = null;

  if (!username) {
    const linkRes = await tg(env, "exportChatInviteLink", { chat_id: targetChatId });
    if (linkRes.ok) inviteLink = linkRes.result;
  }

  await addForceChannel(db, targetChatId, title, username, inviteLink);
  await clearSession(db, userId);

  await sendMessage(env, chatId, T.forceChannelRegistered(title), {
    reply_markup: backOnlyKeyboard("menu:forcelist"),
  });
}

async function handleTextContent(msg, env, db, userId, chatId, session) {
  if (!msg.text) {
    await sendMessage(env, chatId, "❗️ لطفاً یه پیام متنی بفرست.", {
      reply_markup: backOnlyKeyboard(),
    });
    return;
  }
  const ctx = { ...session.context, draftText: msg.text, draftEntities: msg.entities || [] };
  await goToPreview(env, db, userId, chatId, ctx);
}

async function handleMediaContent(msg, env, db, userId, chatId, session) {
  const kind = session.context.postType;
  let fileId = null;

  if (kind === "photo" && msg.photo && msg.photo.length > 0) {
    fileId = msg.photo[msg.photo.length - 1].file_id;
  } else if (kind === "video" && msg.video) {
    fileId = msg.video.file_id;
  } else if (kind === "document" && msg.document) {
    fileId = msg.document.file_id;
  }

  if (!fileId) {
    await sendMessage(
      env,
      chatId,
      `❗️ این پیام یه ${labelForType(kind)} معتبر نیست. لطفاً دوباره بفرست.`,
      { reply_markup: backOnlyKeyboard() }
    );
    return;
  }

  const ctx = { ...session.context, fileId };
  await setSession(db, userId, "awaiting_caption", ctx);
  await sendMessage(env, chatId, T.askCaption, { reply_markup: backOnlyKeyboard() });
}

async function handleCaption(msg, env, db, userId, chatId, session) {
  const text = msg.text || "";
  const ctx = { ...session.context };
  if (text.trim() !== "بدون کپشن") {
    ctx.draftCaption = text;
    ctx.draftCaptionEntities = msg.entities || [];
  }
  await goToPreview(env, db, userId, chatId, ctx);
}

function labelForType(kind) {
  return { photo: "عکس", video: "فیلم", document: "فایل", text: "متن" }[kind] || kind;
}

async function goToPreview(env, db, userId, chatId, ctx) {
  const postType = ctx.postType;
  const rawText = postType === "text" ? ctx.draftText : ctx.draftCaption;
  const rawEntities = postType === "text" ? ctx.draftEntities : ctx.draftCaptionEntities;

  let finalText = null;
  let finalEntities = [];
  if (rawText) {
    const applied = applyEmojiPlaceholders(rawText, rawEntities || []);
    finalText = applied.text;
    finalEntities = applied.entities;
  }

  ctx.finalText = finalText;
  ctx.finalEntities = finalEntities;

  await setSession(db, userId, "awaiting_confirmation", ctx);

  await sendMessage(env, chatId, T.previewHeader);

  const previewResult = await deliverPost(env, chatId, postType, finalText, finalEntities, ctx.fileId);

  if (!previewResult.ok) {
    await sendMessage(env, chatId, T.badEntities, { reply_markup: backOnlyKeyboard() });
    if (postType === "text") {
      await setSession(db, userId, "awaiting_text_content", ctx);
    } else {
      await setSession(db, userId, "awaiting_caption", ctx);
    }
    return;
  }

  ctx.previewChatId = chatId;
  ctx.previewMessageId = previewResult.result && previewResult.result.message_id;
  await setSession(db, userId, "awaiting_confirmation", ctx);

  await sendMessage(env, chatId, "برای تایید یا لغو یکی از دکمه‌های زیر رو بزن:", {
    reply_markup: confirmKeyboard(),
  });
}

async function handleCallback(cb, env, db, adminId) {
  const fromId = cb.from && cb.from.id;
  const chatId = cb.message && cb.message.chat && cb.message.chat.id;
  const messageId = cb.message && cb.message.message_id;

  const access = await resolveAccess(env, db, adminId, fromId);
  if (!access.allowed) {
    await answerCallbackQuery(env, cb.id, { text: T.denied, show_alert: true });
    return;
  }

  await answerCallbackQuery(env, cb.id);

  const data = cb.data || "";
  const [action, param] = data.split(":");

  if (action === "fcheck") {
    const sub = await checkForceSubscription(env, db, fromId);
    if (sub.ok) {
      await editOrSend(env, chatId, messageId, T.joinedOk + "\n\n" + T.welcome, {
        reply_markup: mainMenuKeyboard(access.isAdmin),
      });
    } else {
      await answerCallbackQuery(env, cb.id, { text: T.stillNotMember, show_alert: true });
    }
    return;
  }

  if (access.mode === "public" && !access.isAdmin) {
    const sub = await checkForceSubscription(env, db, fromId);
    if (!sub.ok) {
      await editOrSend(env, chatId, messageId, T.mustJoin, { reply_markup: mustJoinKeyboard(sub.missing) });
      return;
    }
  }

  switch (action) {
    case "menu":
      await handleMenuAction(param, env, db, adminId, fromId, chatId, messageId, access);
      return;
    case "nav":
      if (param === "back_main") {
        await clearSession(db, fromId);
        await editOrSend(env, chatId, messageId, T.welcome, {
          reply_markup: mainMenuKeyboard(access.isAdmin),
        });
      }
      return;
    case "pickchan":
      await handlePickChannel(param, env, db, fromId, chatId, messageId);
      return;
    case "posttype":
      await handlePostType(param, env, db, fromId, chatId, messageId);
      return;
    case "confirm":
      await handleConfirm(param, env, db, fromId, chatId, messageId, access);
      return;
    case "settings":
      await handleSettingsAction(param, env, db, adminId, fromId, chatId, messageId, access);
      return;
    case "fcdel":
      await handleForceDelete(param, env, db, adminId, fromId, chatId, messageId, access);
      return;
    case "fcadd":
      if (!access.isAdmin) return;
      await setSession(db, fromId, "awaiting_force_channel_forward", {});
      await editOrSend(env, chatId, messageId, T.registerForceInstructions, {
        reply_markup: backOnlyKeyboard("menu:forcelist"),
      });
      return;
    default:
      return;
  }
}

async function handleMenuAction(param, env, db, adminId, userId, chatId, messageId, access) {
  if (param === "register") {
    await setSession(db, userId, "awaiting_channel_forward", {});
    await editOrSend(env, chatId, messageId, T.registerInstructions, {
      reply_markup: backOnlyKeyboard(),
    });
    return;
  }

  if (param === "send") {
    const channels = await listChannels(db, userId);
    if (channels.length === 0) {
      await editOrSend(env, chatId, messageId, T.noChannels, {
        reply_markup: backOnlyKeyboard(),
      });
      return;
    }
    if (channels.length === 1) {
      const ctx = { channelDbId: channels[0].id, channelChatId: channels[0].chat_id };
      await setSession(db, userId, "awaiting_post_type", ctx);
      await editOrSend(env, chatId, messageId, T.choosePostType, {
        reply_markup: postTypeKeyboard(),
      });
      return;
    }
    await setSession(db, userId, "awaiting_channel_selection", {});
    await editOrSend(env, chatId, messageId, T.chooseChannel, {
      reply_markup: channelListKeyboard(channels),
    });
    return;
  }

  if (param === "support") {
    await editOrSend(env, chatId, messageId, T.support, {
      reply_markup: backOnlyKeyboard(),
    });
    return;
  }

  if (param === "donate") {
    await editOrSend(env, chatId, messageId, T.donateIntro, {
      reply_markup: donateKeyboard(),
    });
    await sendMessage(env, chatId, T.donateFooter);
    return;
  }

  if (param === "settings") {
    if (!access.isAdmin) return;
    const mode = await getBotMode(db);
    await editOrSend(env, chatId, messageId, T.settingsHeader(mode), {
      reply_markup: settingsKeyboard(mode),
    });
    return;
  }

  if (param === "forcelist") {
    if (!access.isAdmin) return;
    const channels = await listForceChannels(db);
    await editOrSend(env, chatId, messageId, T.forceListHeader(channels.length), {
      reply_markup: forceListKeyboard(channels),
    });
    return;
  }
}

async function handleSettingsAction(param, env, db, adminId, userId, chatId, messageId, access) {
  if (!access.isAdmin) return;

  if (param === "togglemode") {
    const current = await getBotMode(db);
    const next = current === "public" ? "private" : "public";
    await setSetting(db, "bot_mode", next);
    await editOrSend(env, chatId, messageId, T.settingsHeader(next), {
      reply_markup: settingsKeyboard(next),
    });
    return;
  }
}

async function handleForceDelete(idParam, env, db, adminId, userId, chatId, messageId, access) {
  if (!access.isAdmin) return;
  await deleteForceChannel(db, Number(idParam));
  const channels = await listForceChannels(db);
  await editOrSend(env, chatId, messageId, T.forceChannelDeleted + "\n\n" + T.forceListHeader(channels.length), {
    reply_markup: forceListKeyboard(channels),
  });
}

async function handlePickChannel(channelDbId, env, db, userId, chatId, messageId) {
  const channel = await getChannelById(db, Number(channelDbId));
  if (!channel || Number(channel.owner_id) !== Number(userId)) {
    await editOrSend(env, chatId, messageId, T.genericError, {
      reply_markup: backOnlyKeyboard(),
    });
    return;
  }
  const ctx = { channelDbId: channel.id, channelChatId: channel.chat_id };
  await setSession(db, userId, "awaiting_post_type", ctx);
  await editOrSend(env, chatId, messageId, T.choosePostType, {
    reply_markup: postTypeKeyboard(),
  });
}

async function handlePostType(postType, env, db, userId, chatId, messageId) {
  const session = await getSession(db, userId);
  const ctx = { ...session.context, postType };

  if (postType === "text") {
    await setSession(db, userId, "awaiting_text_content", ctx);
    await editOrSend(env, chatId, messageId, T.askText, {
      reply_markup: backOnlyKeyboard(),
    });
    return;
  }

  await setSession(db, userId, "awaiting_media_content", ctx);
  await editOrSend(env, chatId, messageId, T.askMedia(labelForType(postType)), {
    reply_markup: backOnlyKeyboard(),
  });
}

async function handleConfirm(param, env, db, userId, chatId, messageId, access) {
  const session = await getSession(db, userId);
  const ctx = session.context;

  if (param === "no") {
    await clearSession(db, userId);
    await editOrSend(env, chatId, messageId, T.cancelled, {
      reply_markup: mainMenuKeyboard(access.isAdmin),
    });
    return;
  }

  if (param !== "yes") return;

  const channel = await getChannelById(db, ctx.channelDbId);
  if (!channel || Number(channel.owner_id) !== Number(userId)) {
    await editOrSend(
      env,
      chatId,
      messageId,
      "❗️ این کانال دیگه ثبت نیست. دوباره ثبتش کن.",
      { reply_markup: mainMenuKeyboard(access.isAdmin) }
    );
    await clearSession(db, userId);
    return;
  }

  if (!ctx.previewMessageId || !ctx.previewChatId) {
    await editOrSend(env, chatId, messageId, T.genericError, {
      reply_markup: mainMenuKeyboard(access.isAdmin),
    });
    await clearSession(db, userId);
    return;
  }

  const result = await forwardPost(env, channel.chat_id, ctx.previewChatId, ctx.previewMessageId);

  if (!result.ok) {
    const errCode = result.error && result.error.error_code;
    let errText;
    if (errCode === 400) {
      errText = T.badEntities;
    } else if (errCode === 403) {
      errText = "❗️ ربات از این کانال حذف شده یا دسترسی نداره. لطفاً دوباره ثبتش کن.";
    } else if (errCode === 429) {
      errText = "❗️ محدودیت ارسال تلگرام. کمی صبر کن و دوباره امتحان کن.";
    } else {
      errText = T.genericError;
    }
    await editOrSend(env, chatId, messageId, errText, {
      reply_markup: mainMenuKeyboard(access.isAdmin),
    });
    return;
  }

  const sentMessageId = result.result && result.result.message_id;
  await logPost(db, channel.id, sentMessageId, ctx.postType);
  await clearSession(db, userId);

  await editOrSend(env, chatId, messageId, T.sentOk, {
    reply_markup: mainMenuKeyboard(access.isAdmin),
  });
}

async function handleSetup(request, env) {
  const url = new URL(request.url);
  const key = url.searchParams.get("key");

  if (!env.ADMIN_SECRET || !timingSafeEqual(key, env.ADMIN_SECRET)) {
    return textResponse("Forbidden", 403);
  }

  try {
    await runSchema(env.DB);
  } catch (err) {
    console.error("Schema setup failed:", err);
    return textResponse("Setup failed. Check Worker logs.", 500);
  }

  const origin = url.origin;
  const webhookUrl = `${origin}/webhook`;

  const setResult = await tg(env, "setWebhook", {
    url: webhookUrl,
    secret_token: env.ADMIN_SECRET,
    allowed_updates: ["message", "callback_query"],
  });

  const webhookOk = setResult.ok === true;

  const html = `<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>راه‌اندازی ربات</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; background:#0f1115; color:#f2f2f2; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; padding:20px; box-sizing:border-box; }
  .card { background:#181b22; border-radius:16px; padding:32px; max-width:480px; width:100%; box-shadow:0 8px 30px rgba(0,0,0,0.4); text-align:center; }
  h1 { font-size:22px; margin-bottom:16px; }
  .ok { color:#3ddc84; }
  .fail { color:#ff5c5c; }
  ul { text-align:right; list-style:none; padding:0; margin:20px 0; }
  li { padding:10px 0; border-bottom:1px solid #2a2e38; }
  .next { margin-top:20px; font-size:15px; color:#aab; }
</style>
</head>
<body>
  <div class="card">
    <h1>🔸 راه‌اندازی ربات</h1>
    <ul>
      <li>پایگاه داده: <span class="ok">آماده شد ✅</span></li>
      <li>وبهوک: <span class="${webhookOk ? "ok" : "fail"}">${
    webhookOk ? "ثبت شد ✅" : "ثبت نشد ❌"
  }</span></li>
    </ul>
    <div class="next">${
      webhookOk
        ? "حالا می‌تونی به ربات پیام /start بدی."
        : "مشکلی در ثبت وبهوک پیش اومد. تنظیمات BOT_TOKEN رو بررسی کن و دوباره این صفحه رو باز کن."
    }</div>
  </div>
</body>
</html>`;

  return new Response(html, {
    status: 200,
    headers: { "content-type": "text/html; charset=utf-8" },
  });
}

export default {
  async fetch(request, env, ctx) {
    try {
      const url = new URL(request.url);

      if (request.method === "GET" && url.pathname === "/setup") {
        return handleSetup(request, env);
      }

      if (request.method === "POST" && url.pathname === "/webhook") {
        const secretHeader = request.headers.get("X-Telegram-Bot-Api-Secret-Token");
        if (!env.ADMIN_SECRET || !timingSafeEqual(secretHeader, env.ADMIN_SECRET)) {
          return textResponse("Unauthorized", 401);
        }

        let update;
        try {
          update = await request.json();
        } catch (_) {
          return textResponse("OK", 200);
        }

        ctx.waitUntil(
          handleUpdate(update, env, ctx).catch((err) => {
            console.error("Unhandled update error:", err);
          })
        );

        return textResponse("OK", 200);
      }

      return textResponse("Not Found", 404);
    } catch (err) {
      console.error("Top-level fetch error:", err);
      return textResponse("Internal Error", 500);
    }
  },
};
