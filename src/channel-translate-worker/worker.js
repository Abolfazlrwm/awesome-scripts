// Telegram: @Abolfazl_rwm
// ============================================================================
// پروژه سیستم مدیریت پیشرفته Yaraav Bot (نسخه اصلاح‌شده API و ترجمه اختیاری)
// ============================================================================

const CONFIG = {
  TELEGRAM_TOKEN: "YOUR_TELEGRAM_BOT_TOKEN", // ⚠️ توکن ربات تلگرام خود را قرار دهید
  OWNER_ID: 0,                    // آیدی عددی مالک — حتماً ست کنید
};

addEventListener("fetch", (event) => {
  event.respondWith(handleRequest(event.request));
});

async function handleRequest(request) {
  if (request.method !== "POST") {
    return new Response("Yaraav Bot is running!", { status: 200 });
  }

  try {
    const update = await request.json();

    // ۱. مدیریت عضویت/حذف ربات در کانال‌ها
    if (update.my_chat_member) {
      await handleBotChatMemberUpdate(update.my_chat_member);
    }

    // ۲. مدیریت پیام‌های خصوصی پیوی
    if (update.message && update.message.chat.type === "private") {
      await handlePrivateMessage(update.message);
    }

    // ۳. مدیریت کلیک دکمه‌های شیشه‌ای
    if (update.callback_query) {
      await handleCallbackQuery(update.callback_query);
    }
  } catch (err) {
    console.error("Critical Webhook Error:", err);
  }

  return new Response("OK", { status: 200 });
}

// ============================================================================
// ۱. سیستم بررسی جوین اجباری (Force Join)
// ============================================================================

async function checkForceJoin(userId) {
  if (userId === CONFIG.OWNER_ID) return true;

  const fch = await getKV("force_channel");
  if (!fch || !fch.username) return true;

  try {
    const res = await sendTelegram("getChatMember", {
      chat_id: fch.username,
      user_id: userId
    });

    if (res.ok && ["creator", "administrator", "member"].includes(res.result.status)) {
      return true;
    }
  } catch (e) {
    console.error("Force Join Check Error:", e);
  }

  return false;
}

// ============================================================================
// ۲. مدیریت عضویت ربات در کانال‌ها
// ============================================================================

async function handleBotChatMemberUpdate(update) {
  const chat = update.chat;
  const fromUser = update.from;
  const newStatus = update.new_chat_member.status;

  if (chat.type !== "channel") return;

  const userId = fromUser.id;
  const channelId = chat.id;
  const channelTitle = chat.title || "کانال";

  if (newStatus === "administrator") {
    let userChannels = (await getKV(`channels_${userId}`)) || [];
    if (!userChannels.some((c) => c.id === channelId)) {
      userChannels.push({ id: channelId, title: channelTitle, api: null });
      await setKV(`channels_${userId}`, userChannels);
    }

    await sendTelegram("sendMessage", {
      chat_id: userId,
      text: `🎉 **ربات با موفقیت به کانال "${channelTitle}" اضافه شد!**\n\nبرای فعال‌سازی دکمه ترجمه روی پست‌های دلخواه، کافیست پست مورد نظر را به پیوی ربات فوروارد کنید.`,
      parse_mode: "Markdown"
    });
  }

  if (newStatus === "kicked" || newStatus === "left" || newStatus === "member") {
    let userChannels = (await getKV(`channels_${userId}`)) || [];
    userChannels = userChannels.filter((c) => c.id !== channelId);
    await setKV(`channels_${userId}`, userChannels);

    await sendTelegram("sendMessage", {
      chat_id: userId,
      text: `⚠️ **ربات از کانال "${channelTitle}" حذف شد.**\nاین کانال از لیست کانال‌های فعال شما خارج گردید.`,
      parse_mode: "Markdown"
    });
  }
}

// ============================================================================
// ۳. مدیریت پیام‌های پیوی و ترجمه اختیاری پست فوروارد شده
// ============================================================================

async function handlePrivateMessage(msg) {
  const chatId = msg.chat.id;
  const userId = msg.from.id;
  const text = msg.text || "";

  // استارت ربات
  if (text === "/start") {
    await setKV(`state_${userId}`, null);

    if (userId === CONFIG.OWNER_ID) {
      return showAdminPanel(chatId);
    } else {
      const userLang = await getKV(`lang_${userId}`);
      if (!userLang) {
        return showLanguageSelection(chatId);
      }

      const isJoined = await checkForceJoin(userId);
      if (!isJoined) return sendForceJoinMessage(chatId);

      return showUserAuthMenu(chatId);
    }
  }

  // بررسی جوین اجباری برای سایر پیام‌ها
  if (userId !== CONFIG.OWNER_ID) {
    const isJoined = await checkForceJoin(userId);
    if (!isJoined) return sendForceJoinMessage(chatId);
  }

  // بررسی اینکه آیا کاربر پستی از کانال خودش فوروارد کرده تا دکمه ترجمه روی آن قرار گیرد
  if (msg.forward_from_chat && msg.forward_from_chat.type === "channel") {
    const channelId = msg.forward_from_chat.id;
    const forwardMsgId = msg.forward_from_message_id;

    const userChannels = (await getKV(`channels_${userId}`)) || [];
    const isOwnerOfChannel = userChannels.some((c) => c.id === channelId) || userId === CONFIG.OWNER_ID;

    if (!isOwnerOfChannel) {
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: "❌ این کانال در لیست کانال‌های مدیریت‌شده شما ثبت نشده است."
      });
    }

    // اضافه کردن دکمه‌های ترجمه به پست مشخص شده در کانال
    const keyboard = {
      inline_keyboard: [
        [
          { text: "🇮🇷 فارسی", callback_data: `tr_fa_${channelId}_${forwardMsgId}` },
          { text: "🇬🇧 English", callback_data: `tr_en_${channelId}_${forwardMsgId}` },
          { text: "🇷🇺 Русский", callback_data: `tr_ru_${channelId}_${forwardMsgId}` }
        ]
      ]
    };

    const res = await sendTelegram("editMessageReplyMarkup", {
      chat_id: channelId,
      message_id: forwardMsgId,
      reply_markup: keyboard
    });

    if (res.ok) {
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: `✅ **قابلیت ترجمه با موفقیت روی پست انتخابی فعال شد!**`
      });
    } else {
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: `❌ خطا در اضافه کردن دکمه به کانال. مطمئن شوید ربات در کانال دسترسی ویرایش پیام دارد.`
      });
    }
  }

  // استپ‌های کاربر و ادمین
  const userState = await getKV(`state_${userId}`);

  if (userId === CONFIG.OWNER_ID && userState) {
    if (userState.step === "SET_FORCE_CHANNEL") {
      let chInput = text.trim();
      if (!chInput.startsWith("@") && !chInput.startsWith("-100")) {
        chInput = "@" + chInput;
      }
      await setKV("force_channel", { username: chInput });
      await setKV(`state_${userId}`, null);

      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: `✅ **کانال جوین اجباری با موفقیت تنظیم شد:**\n${chInput}`,
        reply_markup: {
          inline_keyboard: [[{ text: "🔙 بازگشت به پنل", callback_data: "admin_main" }]]
        }
      });
    }

    if (userState.step === "ADD_USER_USERNAME") {
      await setKV(`state_${userId}`, { step: "ADD_USER_PASSWORD", targetUsername: text });
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: `🔑 نام کاربری **${text}** ثبت شد.\nحالا رمز عبور مورد نظر را وارد کنید:`
      });
    }

    if (userState.step === "ADD_USER_PASSWORD") {
      const username = userState.targetUsername;
      const password = text;

      const userObj = { username, password, approved: true };
      await setKV(`user_uname_${username}`, userObj);

      let allUsers = (await getKV("all_users_list")) || [];
      if (!allUsers.includes(username)) allUsers.push(username);
      await setKV("all_users_list", allUsers);

      await setKV(`state_${userId}`, null);

      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: `✅ کاربر **${username}** با موفقیت ایجاد شد!`,
        reply_markup: {
          inline_keyboard: [[{ text: "🔙 بازگشت به منو", callback_data: "admin_users_menu" }]]
        }
      });
    }
  }

  if (userState) {
    if (userState.step === "REQ_USERNAME") {
      await setKV(`state_${userId}`, { step: "REQ_PASSWORD", reqUsername: text });
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: "🔐 لطفاً یک رمز عبور برای حساب خود تعیین کنید:"
      });
    }

    if (userState.step === "REQ_PASSWORD") {
      const username = userState.reqUsername;
      const password = text;

      let requests = (await getKV("pending_requests")) || [];
      requests.push({ userId, username, password });
      await setKV("pending_requests", requests);

      await setKV(`state_${userId}`, null);

      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: "📥 درخواست عضویت شما ثبت شد و برای مالک ارسال گردید."
      });
    }

    if (userState.step === "LOGIN_USERNAME") {
      await setKV(`state_${userId}`, { step: "LOGIN_PASSWORD", loginUsername: text });
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: "🔐 لطفاً رمز عبور خود را وارد کنید:"
      });
    }

    if (userState.step === "LOGIN_PASSWORD") {
      const username = userState.loginUsername;
      const password = text;

      const userData = await getKV(`user_uname_${username}`);
      if (userData && userData.password === password && userData.approved) {
        await setKV(`session_${userId}`, username);
        await setKV(`state_${userId}`, null);
        return showUserDashboard(chatId, username);
      } else {
        await setKV(`state_${userId}`, null);
        return sendTelegram("sendMessage", {
          chat_id: chatId,
          text: "❌ نام کاربری یا رمز عبور اشتباه است."
        });
      }
    }

    if (userState.step === "SUBMIT_API") {
      let userApis = (await getKV(`apis_${userId}`)) || [];
      userApis.push(text.trim());
      await setKV(`apis_${userId}`, userApis);

      await setKV(`state_${userId}`, null);
      return sendTelegram("sendMessage", {
        chat_id: chatId,
        text: "✅ کلید API Gemini با موفقیت ثبت و فعال شد!",
        reply_markup: {
          inline_keyboard: [[{ text: "🔙 بازگشت به پنل", callback_data: "user_dashboard" }]]
        }
      });
    }
  }
}

// ============================================================================
// ۴. مدیریت کلیک دکمه‌های شیشه‌ای و موتور ترجمه Gemini API
// ============================================================================

async function handleCallbackQuery(query) {
  const queryId = query.id;
  const data = query.data;
  const userId = query.from.id;
  const chatId = query.message ? query.message.chat.id : userId;
  const messageId = query.message ? query.message.message_id : null;

  if (data.startsWith("setlang_")) {
    const lang = data.split("_")[1];
    await setKV(`lang_${userId}`, lang);
    await answerCallback(queryId, "زبان ثبت شد.");

    await sendBotGuide(chatId);

    const isJoined = await checkForceJoin(userId);
    if (!isJoined) return sendForceJoinMessage(chatId);

    return showUserAuthMenu(chatId);
  }

  if (data === "check_force_join") {
    const isJoined = await checkForceJoin(userId);
    if (isJoined) {
      await answerCallback(queryId, "✅ عضویت شما تایید شد!", true);
      return showUserAuthMenu(chatId, messageId);
    } else {
      return answerCallback(queryId, "❌ شما هنوز در کانال عضو نشده‌اید!", true);
    }
  }

  if (userId !== CONFIG.OWNER_ID) {
    const isJoined = await checkForceJoin(userId);
    if (!isJoined) {
      await answerCallback(queryId, "⚠️ لطفاً ابتدا در کانال مربوطه عضو شوید.", true);
      return sendForceJoinMessage(chatId);
    }
  }

  // --- ترجمه هوشمند با Gemini API ---
  if (data.startsWith("tr_")) {
    const parts = data.split("_");
    const targetLangCode = parts[1];

    const originalText = query.message?.text || query.message?.caption || "";
    if (!originalText) {
      return answerCallback(queryId, "متنی برای ترجمه یافت نشد.", true);
    }

    // دریافت اولین API ثبت شده کاربر
    const userApis = (await getKV(`apis_${userId}`)) || [];
    const apiKey = userApis.length > 0 ? userApis[0] : null;

    try {
      let translatedText = "";
      if (apiKey) {
        translatedText = await translateWithGemini(originalText, targetLangCode, apiKey);
      } else {
        translatedText = await fallbackTranslate(originalText, targetLangCode);
      }

      const safeText = translatedText.length > 180 ? translatedText.substring(0, 177) + "..." : translatedText;
      return answerCallback(queryId, safeText, true);
    } catch (err) {
      console.error("Gemini Error, using fallback...", err);
      try {
        const fallbackText = await fallbackTranslate(originalText, targetLangCode);
        const safeText = fallbackText.length > 180 ? fallbackText.substring(0, 177) + "..." : fallbackText;
        return answerCallback(queryId, safeText, true);
      } catch (e) {
        return answerCallback(queryId, "خطا در برقراری ارتباط با سرور ترجمه.", true);
      }
    }
  }

  // منوی مالک و سایر دکمه‌ها
  if (data === "admin_main" && userId === CONFIG.OWNER_ID) {
    return showAdminPanel(chatId, messageId);
  }

  if (data === "admin_force_join_menu" && userId === CONFIG.OWNER_ID) {
    const fch = await getKV("force_channel");
    const statusText = fch && fch.username ? `📢 کانال فعال فعلی: ${fch.username}` : "❌ هیچ کانالی ثبت نشده است.";

    const keyboard = {
      inline_keyboard: [
        [{ text: "➕ ثبت / تغییر کانال", callback_data: "admin_set_force_ch" }],
        fch ? [{ text: "❌ حذف جوین اجباری", callback_data: "admin_del_force_ch" }] : [],
        [{ text: "🔙 بازگشت به پنل", callback_data: "admin_main" }]
      ].filter((row) => row.length > 0)
    };

    return editOrSend(chatId, messageId, `⚙️ **مدیریت جوین اجباری:**\n\n${statusText}`, keyboard);
  }

  if (data === "admin_set_force_ch" && userId === CONFIG.OWNER_ID) {
    await setKV(`state_${userId}`, { step: "SET_FORCE_CHANNEL" });
    await answerCallback(queryId, "آیدی کانال را ارسال کنید.");
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: "📢 لطفاً آیدی عمومی کانال یا گروه را همراه با `@` ارسال کنید (مثال: `@Abolfazl_rwm`):"
    });
  }

  if (data === "admin_del_force_ch" && userId === CONFIG.OWNER_ID) {
    await deleteKV("force_channel");
    await answerCallback(queryId, "جوین اجباری غیرفعال شد.", true);
    return showAdminPanel(chatId, messageId);
  }

  if (data === "admin_users_menu" && userId === CONFIG.OWNER_ID) {
    const keyboard = {
      inline_keyboard: [
        [{ text: "📥 درخواست‌ها", callback_data: "admin_requests" }],
        [{ text: "➕ افزودن کاربر", callback_data: "admin_add_user" }],
        [{ text: "👥 کاربران", callback_data: "admin_list_users" }],
        [{ text: "🔙 بازگشت", callback_data: "admin_main" }]
      ]
    };
    return editOrSend(chatId, messageId, "👥 **بخش مدیریت کاربران:**", keyboard);
  }

  if (data === "admin_add_user" && userId === CONFIG.OWNER_ID) {
    await setKV(`state_${userId}`, { step: "ADD_USER_USERNAME" });
    await answerCallback(queryId, "نام کاربری را وارد کنید.");
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: "👤 لطفاً **نام کاربری** جدید را ارسال کنید:"
    });
  }

  if (data === "admin_list_users" && userId === CONFIG.OWNER_ID) {
    const allUsers = (await getKV("all_users_list")) || [];
    if (allUsers.length === 0) return answerCallback(queryId, "هیچ کاربری ثبت نشده است.", true);

    const buttons = allUsers.map((u) => [{ text: `👤 ${u}`, callback_data: `view_u_${u}` }]);
    buttons.push([{ text: "🔙 بازگشت", callback_data: "admin_users_menu" }]);

    return editOrSend(chatId, messageId, "📜 **لیست کاربران:**", { inline_keyboard: buttons });
  }

  if (data.startsWith("view_u_") && userId === CONFIG.OWNER_ID) {
    const targetUser = data.replace("view_u_", "");
    const keyboard = {
      inline_keyboard: [
        [{ text: "👁 مشاهده نام کاربری و پسورد", callback_data: `show_pass_${targetUser}` }],
        [{ text: "❌ حذف کاربر", callback_data: `del_u_${targetUser}` }],
        [{ text: "🔙 برگشت به منوی قبل", callback_data: "admin_list_users" }]
      ]
    };
    return editOrSend(chatId, messageId, `👤 **اطلاعات کاربر:** ${targetUser}`, keyboard);
  }

  if (data.startsWith("show_pass_") && userId === CONFIG.OWNER_ID) {
    const targetUser = data.replace("show_pass_", "");
    const userData = await getKV(`user_uname_${targetUser}`);
    return answerCallback(queryId, `👤 User: ${userData.username}\n🔑 Pass: ${userData.password}`, true);
  }

  if (data.startsWith("del_u_") && userId === CONFIG.OWNER_ID) {
    const targetUser = data.replace("del_u_", "");
    await deleteKV(`user_uname_${targetUser}`);

    let allUsers = (await getKV("all_users_list")) || [];
    allUsers = allUsers.filter((u) => u !== targetUser);
    await setKV("all_users_list", allUsers);

    await answerCallback(queryId, "کاربر حذف شد.", true);
    return showAdminPanel(chatId, messageId);
  }

  if (data === "admin_requests" && userId === CONFIG.OWNER_ID) {
    const requests = (await getKV("pending_requests")) || [];
    if (requests.length === 0) return answerCallback(queryId, "درخواستی وجود ندارد.", true);

    const buttons = requests.map((r, idx) => [
      { text: `✅ قبول ${r.username}`, callback_data: `acc_req_${idx}` },
      { text: `❌ رد ${r.username}`, callback_data: `rej_req_${idx}` }
    ]);
    buttons.push([{ text: "🔙 بازگشت", callback_data: "admin_users_menu" }]);

    return editOrSend(chatId, messageId, "📥 **درخواست‌های عضویت:**", { inline_keyboard: buttons });
  }

  if (data.startsWith("acc_req_") && userId === CONFIG.OWNER_ID) {
    const idx = parseInt(data.replace("acc_req_", ""));
    let requests = (await getKV("pending_requests")) || [];
    const req = requests[idx];

    if (req) {
      const userObj = { username: req.username, password: req.password, approved: true };
      await setKV(`user_uname_${req.username}`, userObj);

      let allUsers = (await getKV("all_users_list")) || [];
      if (!allUsers.includes(req.username)) allUsers.push(req.username);
      await setKV("all_users_list", allUsers);

      requests.splice(idx, 1);
      await setKV("pending_requests", requests);

      try {
        await sendTelegram("sendMessage", {
          chat_id: req.userId,
          text: "✅ **درخواست عضویت شما توسط مالک تایید شد!** هم‌اکنون می‌توانید وارد پنل شوید."
        });
      } catch (e) {}

      await answerCallback(queryId, `درخواست ${req.username} تایید شد.`, true);
      return showAdminPanel(chatId, messageId);
    }
  }

  // --- بخش کاربران ---
  if (data === "user_auth_request") {
    await setKV(`state_${userId}`, { step: "REQ_USERNAME" });
    await answerCallback(queryId, "نام کاربری را ارسال کنید.");
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: "👤 لطفاً **نام کاربری** پیشنهادی خود را بفرستید:"
    });
  }

  if (data === "user_auth_login") {
    await setKV(`state_${userId}`, { step: "LOGIN_USERNAME" });
    await answerCallback(queryId, "نام کاربری را وارد کنید.");
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: "👤 لطفاً **نام کاربری** خود را وارد کنید:"
    });
  }

  if (data === "user_dashboard") {
    return showUserDashboard(chatId, "کاربر", messageId);
  }

  if (data === "user_channels_menu") {
    const userChannels = (await getKV(`channels_${userId}`)) || [];

    if (userChannels.length === 0) {
      const keyboard = {
        inline_keyboard: [
          [{ text: "➕ راهنمای افزودن کانال", callback_data: "how_add_channel" }],
          [{ text: "🔙 بازگشت", callback_data: "user_dashboard" }]
        ]
      };
      return editOrSend(
        chatId,
        messageId,
        "📢 **شما هنوز هیچ کانالی ثبت نکرده‌اید!**\n\nبرای اضافه کردن کانال، کافیست ربات را در کانال خود **ادمین (Admin)** کنید.",
        keyboard
      );
    }

    const buttons = userChannels.map((c) => [
      { text: `📢 ${c.title}`, callback_data: `manage_ch_${c.id}` }
    ]);
    buttons.push([{ text: "➕ راهنمای افزودن کانال جدید", callback_data: "how_add_channel" }]);
    buttons.push([{ text: "🔙 بازگشت", callback_data: "user_dashboard" }]);

    return editOrSend(chatId, messageId, "📢 **کانال‌های فعال شما:**", { inline_keyboard: buttons });
  }

  if (data === "how_add_channel") {
    const keyboard = {
      inline_keyboard: [[{ text: "🔙 بازگشت", callback_data: "user_channels_menu" }]]
    };
    return editOrSend(
      chatId,
      messageId,
      "📌 **راهنمای فعال‌سازی ترجمه برای پست‌های دلخواه:**\n\n۱. ربات را در کانال ادمین کنید.\n۲. هر پستی از کانال را که می‌خواهید دکمه ترجمه روی آن قرار بگیرد، به **پیوی ربات فوروارد کنید**.\n۳. ربات بلافاصله دکمه‌های ترجمه را زیر همان پست در کانال شما فعال می‌کند!",
      keyboard
    );
  }

  if (data.startsWith("manage_ch_")) {
    const channelId = data.replace("manage_ch_", "");
    const userChannels = (await getKV(`channels_${userId}`)) || [];
    const channel = userChannels.find((c) => c.id.toString() === channelId);

    if (!channel) return answerCallback(queryId, "کانال یافت نشد.", true);

    const keyboard = {
      inline_keyboard: [
        [{ text: "🔑 اتصال / تغییر API این کانال", callback_data: `set_ch_api_${channel.id}` }],
        [{ text: "🔙 بازگشت به لیست کانال‌ها", callback_data: "user_channels_menu" }]
      ]
    };

    const apiStatus = channel.api ? "✅ متصل شد" : "❌ بدون API (از سیستم پیش‌فرض استفاده می‌شود)";

    return editOrSend(
      chatId,
      messageId,
      `📢 **تنظیمات کانال:** ${channel.title}\n\nوضعیت API: ${apiStatus}`,
      keyboard
    );
  }

  if (data === "user_api_menu") {
    const userApis = (await getKV(`apis_${userId}`)) || [];
    const hasApi = userApis.length > 0;

    const keyboard = {
      inline_keyboard: [
        [{ text: "➕ ثبت API جدید", callback_data: "user_add_api" }],
        hasApi ? [{ text: "❌ حذف تمام APIها", callback_data: "user_del_api" }] : [],
        [{ text: "🔙 بازگشت", callback_data: "user_dashboard" }]
      ].filter((row) => row.length > 0)
    };

    const statusText = hasApi ? `✅ تعداد API فعال شما: ${userApis.length}` : "❌ هیچ API ثبت نکرده‌اید.";

    return editOrSend(chatId, messageId, `🔑 **مدیریت APIهای شما:**\n\n${statusText}`, keyboard);
  }

  if (data === "user_add_api") {
    await setKV(`state_${userId}`, { step: "SUBMIT_API" });
    await answerCallback(queryId, "کلید API را ارسال کنید.");
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: "🔑 لطفاً **کلید Gemini API** خود را بفرستید:"
    });
  }

  if (data === "user_del_api") {
    await deleteKV(`apis_${userId}`);
    await answerCallback(queryId, "تمام APIهای شما پاک شدند.", true);
    return showUserDashboard(chatId, "کاربر", messageId);
  }
}

// ============================================================================
// ۵. توابع ترجمه اختصاصی Gemini API
// ============================================================================

async function translateWithGemini(text, targetLang, apiKey) {
  const targetLanguages = {
    fa: "Persian",
    en: "English",
    ru: "Russian"
  };

  const langName = targetLanguages[targetLang] || "Persian";

  const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      contents: [
        {
          parts: [
            {
              text: `Translate the following text accurately to ${langName}. Return ONLY the translated text without any explanations or intro:\n\n${text}`
            }
          ]
        }
      ]
    })
  });

  if (!response.ok) {
    throw new Error(`Gemini API Error: ${response.status}`);
  }

  const data = await response.json();
  const translated = data.candidates?.[0]?.content?.parts?.[0]?.text;

  if (!translated) {
    throw new Error("Invalid response structure from Gemini API");
  }

  return translated.trim();
}

async function fallbackTranslate(text, targetLang) {
  const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(text)}`;
  const response = await fetch(url, { headers: { "User-Agent": "Mozilla/5.0" } });
  if (!response.ok) throw new Error("Translation failed");
  const result = await response.json();
  return result[0].map((item) => item[0]).join("");
}

// ============================================================================
// ۶. توابع کمکی ارسال پیام و UI
// ============================================================================

async function sendBotGuide(chatId) {
  const guideText = 
`✨ **به ربات هوشمند Yaraav خوش آمدید!**

با استفاده از این ربات می‌توانید کانال‌های خود را هوشمند کرده و امکان ترجمه خودکار پست‌ها به چندین زبان را برای مخاطبان خود فراهم کنید.

---

📖 **خلاصه امکانات ربات:**
1️⃣ **مدیریت کانال‌ها:** تنها کافیست ربات را در کانال خود ادمین کنید تا به صورت خودکار شناسایی شود.
2️⃣ **ترجمه اختیاری:** برای اضافه کردن دکمه ترجمه روی یک پست، کافیست **آن پست را به پیوی ربات فوروارد کنید**.
3️⃣ **مدیریت API اختصاصی:** برای سرعت بیشتر، می‌توانید کلید API رایگان Gemini خودتان را ست کنید.

---

🔑 **آموزش سریع دریافت Gemini API رایگان:**
1. وارد سایت [Google AI Studio](https://aistudio.google.com) شوید.
2. با حساب گوگل وارد شوید.
3. روی دکمه **Get API key** کلیک کنید.
4. گزینه **Create API key** را بزنید و کلید کپی‌شده را در بخش **API** ربات ارسال کنید!`;

  await sendTelegram("sendMessage", {
    chat_id: chatId,
    text: guideText,
    parse_mode: "Markdown",
    disable_web_page_preview: true
  });
}

async function sendForceJoinMessage(chatId) {
  const fch = await getKV("force_channel");
  const channelLink = fch.username.startsWith("@") ? `https://t.me/${fch.username.replace("@", "")}` : fch.username;

  const keyboard = {
    inline_keyboard: [
      [{ text: "📢 عضویت در کانال", url: channelLink }],
      [{ text: "🔄 عضو شدم (بررسی)", callback_data: "check_force_join" }]
    ]
  };

  return sendTelegram("sendMessage", {
    chat_id: chatId,
    text: "⚠️ **برای استفاده از ربات، لطفاً ابتدا در کانال زیر عضو شوید:**\n\nپس از عضویت، روی دکمه **«عضو شدم»** کلیک کنید.",
    reply_markup: keyboard
  });
}

async function showAdminPanel(chatId, messageId = null) {
  const keyboard = {
    inline_keyboard: [
      [{ text: "👥 مدیریت کاربران", callback_data: "admin_users_menu" }],
      [{ text: "📢 تنظیم جوین اجباری", callback_data: "admin_force_join_menu" }],
      [{ text: "⚙️ پنل من (تنظیمات شخصی)", callback_data: "user_dashboard" }]
    ]
  };

  return editOrSend(chatId, messageId, "🔥 **خوش آمدید مالک عزیز! پنل مدیریت Yaraav:**", keyboard);
}

async function showLanguageSelection(chatId) {
  const keyboard = {
    inline_keyboard: [
      [
        { text: "🇮🇷 فارسی", callback_data: "setlang_fa" },
        { text: "🇬🇧 English", callback_data: "setlang_en" }
      ]
    ]
  };
  return sendTelegram("sendMessage", {
    chat_id: chatId,
    text: "🌐 لطفاً زبان خود را انتخاب کنید / Please select your language:",
    reply_markup: keyboard
  });
}

async function showUserAuthMenu(chatId, messageId = null) {
  const keyboard = {
    inline_keyboard: [
      [{ text: "🔑 ورود به پنل", callback_data: "user_auth_login" }],
      [{ text: "📝 درخواست عضویت", callback_data: "user_auth_request" }]
    ]
  };
  return editOrSend(chatId, messageId, "✨ به ربات **Yaraav** خوش آمدید. لطفاً گزینه مورد نظر را انتخاب کنید:", keyboard);
}

async function showUserDashboard(chatId, username, messageId = null) {
  const keyboard = {
    inline_keyboard: [
      [{ text: "📢 مدیریت کانال‌ها", callback_data: "user_channels_menu" }],
      [{ text: "🔑 API", callback_data: "user_api_menu" }],
      [{ text: "🔙 بازگشت به منوی اصلی", callback_data: "admin_main" }]
    ]
  };
  return editOrSend(chatId, messageId, `🎉 **پنل کاربری شما:**`, keyboard);
}

async function getKV(key) {
  try {
    const val = await YARAAV_DB.get(key);
    return val ? JSON.parse(val) : null;
  } catch {
    return null;
  }
}

async function setKV(key, value) {
  return await YARAAV_DB.put(key, JSON.stringify(value));
}

async function deleteKV(key) {
  return await YARAAV_DB.delete(key);
}

async function editOrSend(chatId, messageId, text, keyboard) {
  if (messageId) {
    return sendTelegram("editMessageText", {
      chat_id: chatId,
      message_id: messageId,
      text: text,
      parse_mode: "Markdown",
      reply_markup: keyboard
    });
  } else {
    return sendTelegram("sendMessage", {
      chat_id: chatId,
      text: text,
      parse_mode: "Markdown",
      reply_markup: keyboard
    });
  }
}

async function answerCallback(queryId, text, showAlert = false) {
  return sendTelegram("answerCallbackQuery", {
    callback_query_id: queryId,
    text: text,
    show_alert: showAlert
  });
}

async function sendTelegram(method, body) {
  const url = `https://api.telegram.org/bot${CONFIG.TELEGRAM_TOKEN}/${method}`;
  const response = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  });
  return response.json();
}
