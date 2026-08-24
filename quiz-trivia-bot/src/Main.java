// ========== ربات کوییز و مسابقه (Quiz & Trivia Bot) ==========
// سوالات چهارگزینه‌ای در گروه می‌پرسه، اولین جواب درست امتیاز می‌گیره، لیدربورد نگه می‌داره.

import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.*;
import java.util.concurrent.ConcurrentHashMap;

public class Main {

    // ---------- تنظیمات ----------
    static final String BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"; // توکن ربات را از @BotFather بگیر
    static final Set<Long> ADMIN_IDS = Set.of(123456789L);  // آیدی عددی مدیران از @userinfobot
    static final int QUESTIONS_PER_ROUND = 5;

    static final Path QUESTIONS_FILE = Path.of("questions.txt");
    static final Path SCORES_FILE = Path.of("scores.json");

    static TelegramApi api;
    static List<Question> questionBank = new ArrayList<>();
    static Map<Long, Map<Long, Map<String, Object>>> scores = new LinkedHashMap<>(); // chatId -> userId -> {name, score}
    static Map<Long, QuizSession> activeSessions = new ConcurrentHashMap<>(); // chatId -> session

    record Question(String text, List<String> options, int correctIndex) {}

    static class QuizSession {
        List<Question> questions;
        int currentIndex = 0;
        boolean answered = false;
        QuizSession(List<Question> qs) { this.questions = qs; }
    }

    public static void main(String[] args) throws Exception {
        api = new TelegramApi(BOT_TOKEN);
        loadQuestions();
        loadScores();
        System.out.println("🎯 ربات کوییز روشن شد...");

        long offset = 0;
        while (true) {
            try {
                Map<String, Object> response = api.get("getUpdates",
                        "offset=" + (offset + 1) + "&timeout=30");
                if (!Boolean.TRUE.equals(response.get("ok"))) continue;

                for (Object updateObj : JsonValue.asList(response.get("result"))) {
                    Map<String, Object> update = JsonValue.asMap(updateObj);
                    offset = JsonValue.asLong(update.get("update_id"), offset);

                    if (update.containsKey("message")) {
                        handleMessage(JsonValue.asMap(update.get("message")));
                    } else if (update.containsKey("callback_query")) {
                        handleCallback(JsonValue.asMap(update.get("callback_query")));
                    }
                }
            } catch (Exception e) {
                System.out.println("خطا در حلقه‌ی اصلی: " + e.getMessage());
                Thread.sleep(3000);
            }
        }
    }

    // ================== بارگذاری سوالات از فایل متنی ==================
    // فرمت هر خط: سوال|گزینه۱|گزینه۲|گزینه۳|گزینه۴|ایندکس‌درست(۰ تا ۳)
    static void loadQuestions() throws IOException {
        if (!Files.exists(QUESTIONS_FILE)) {
            createDefaultQuestions();
        }
        List<String> lines = Files.readAllLines(QUESTIONS_FILE, StandardCharsets.UTF_8);
        questionBank.clear();
        for (String line : lines) {
            if (line.isBlank()) continue;
            String[] parts = line.split("\\|");
            if (parts.length != 6) continue;
            List<String> options = List.of(parts[1], parts[2], parts[3], parts[4]);
            int correctIndex = Integer.parseInt(parts[5].trim());
            questionBank.add(new Question(parts[0], options, correctIndex));
        }
    }

    static void createDefaultQuestions() throws IOException {
        String defaults = String.join("\n",
                "پایتخت ایران کجاست؟|تهران|اصفهان|شیراز|مشهد|0",
                "بزرگ‌ترین سیاره‌ی منظومه‌ی شمسی کدام است؟|زمین|مشتری|زحل|مریخ|1",
                "زبان برنامه‌نویسی پایتون در چه سالی منتشر شد؟|۱۹۸۹|۱۹۹۱|۲۰۰۰|۱۹۹۵|1",
                "نماد شیمیایی طلا چیست؟|Au|Ag|Fe|Pb|0",
                "کدام کشور بیشترین جمعیت جهان را دارد؟|هند|چین|آمریکا|برزیل|0",
                "سریع‌ترین حیوان زمینی کدام است؟|شیر|یوزپلنگ|اسب|گورخر|1",
                "تعداد استخوان‌های بدن انسان بالغ چند عدد است؟|۱۸۰|۲۰۶|۲۵۰|۳۰۰|1"
        );
        Files.writeString(QUESTIONS_FILE, defaults, StandardCharsets.UTF_8);
    }

    // ================== بارگذاری و ذخیره‌ی امتیازها ==================
    @SuppressWarnings("unchecked")
    static void loadScores() throws IOException {
        if (!Files.exists(SCORES_FILE)) { scores = new LinkedHashMap<>(); return; }
        String json = Files.readString(SCORES_FILE, StandardCharsets.UTF_8);
        Map<String, Object> raw = JsonValue.asMap(JsonValue.parse(json));
        scores = new LinkedHashMap<>();
        for (var chatEntry : raw.entrySet()) {
            Map<Long, Map<String, Object>> chatScores = new LinkedHashMap<>();
            for (var userEntry : JsonValue.asMap(chatEntry.getValue()).entrySet()) {
                chatScores.put(Long.parseLong(userEntry.getKey()), JsonValue.asMap(userEntry.getValue()));
            }
            scores.put(Long.parseLong(chatEntry.getKey()), chatScores);
        }
    }

    static void saveScores() throws IOException {
        Map<String, Object> raw = new LinkedHashMap<>();
        for (var chatEntry : scores.entrySet()) {
            Map<String, Object> chatMap = new LinkedHashMap<>();
            for (var userEntry : chatEntry.getValue().entrySet()) {
                chatMap.put(String.valueOf(userEntry.getKey()), userEntry.getValue());
            }
            raw.put(String.valueOf(chatEntry.getKey()), chatMap);
        }
        Files.writeString(SCORES_FILE, JsonValue.stringify(raw), StandardCharsets.UTF_8);
    }

    static void addScore(long chatId, long userId, String name, int amount) throws IOException {
        scores.putIfAbsent(chatId, new LinkedHashMap<>());
        var chatScores = scores.get(chatId);
        chatScores.putIfAbsent(userId, new LinkedHashMap<>(Map.of("name", name, "score", 0.0)));
        var entry = chatScores.get(userId);
        double current = (double) entry.getOrDefault("score", 0.0);
        entry.put("score", current + amount);
        entry.put("name", name); // آپدیت آخرین اسم شناخته‌شده
        saveScores();
    }

    // ================== پردازش پیام‌های متنی ==================
    static void handleMessage(Map<String, Object> message) throws IOException {
        Map<String, Object> chat = JsonValue.asMap(message.get("chat"));
        long chatId = JsonValue.asLong(chat.get("id"), 0);
        String text = JsonValue.asString(message.get("text"), "");
        Map<String, Object> from = JsonValue.asMap(message.get("from"));
        long fromId = JsonValue.asLong(from.get("id"), 0);

        if (text.equals("/start")) {
            api.sendMessage(chatId,
                    "🎯 سلام! من ربات کوییز و مسابقه‌ام.\n\n" +
                    "/quiz — شروع یک دور مسابقه (" + QUESTIONS_PER_ROUND + " سوال)\n" +
                    "/leaderboard — لیدربورد این چت\n" +
                    "/help — راهنما");
            return;
        }

        if (text.equals("/help")) {
            api.sendMessage(chatId,
                    "📖 <b>راهنما</b>\n\n" +
                    "/quiz — شروع مسابقه\n" +
                    "/leaderboard — نمایش برترین‌های این چت\n" +
                    (ADMIN_IDS.contains(fromId) ?
                        "\n<b>ادمین:</b>\n/addquestion سوال|گزینه۱|گزینه۲|گزینه۳|گزینه۴|ایندکس‌درست" : ""));
            return;
        }

        if (text.equals("/quiz")) {
            startQuiz(chatId);
            return;
        }

        if (text.equals("/leaderboard")) {
            showLeaderboard(chatId);
            return;
        }

        if (text.startsWith("/addquestion ") && ADMIN_IDS.contains(fromId)) {
            String raw = text.substring("/addquestion ".length()).trim();
            String[] parts = raw.split("\\|");
            if (parts.length != 6) {
                api.sendMessage(chatId, "❗ فرمت درست:\nسوال|گزینه۱|گزینه۲|گزینه۳|گزینه۴|ایندکس‌درست(۰ تا ۳)");
                return;
            }
            Files.writeString(QUESTIONS_FILE, "\n" + raw, StandardCharsets.UTF_8,
                    java.nio.file.StandardOpenOption.APPEND);
            loadQuestions();
            api.sendMessage(chatId, "✅ سوال جدید اضافه شد. (تعداد کل سوالات: " + questionBank.size() + ")");
        }
    }

    // ================== شروع یک دور مسابقه ==================
    static void startQuiz(long chatId) {
        if (questionBank.size() < QUESTIONS_PER_ROUND) {
            api.sendMessage(chatId, "❌ تعداد سوالات موجود کافی نیست.");
            return;
        }
        List<Question> shuffled = new ArrayList<>(questionBank);
        Collections.shuffle(shuffled);
        QuizSession session = new QuizSession(shuffled.subList(0, QUESTIONS_PER_ROUND));
        activeSessions.put(chatId, session);
        sendQuestion(chatId, session);
    }

    static void sendQuestion(long chatId, QuizSession session) {
        Question q = session.questions.get(session.currentIndex);
        session.answered = false;

        StringBuilder buttonsJson = new StringBuilder("{\"inline_keyboard\":[");
        for (int i = 0; i < q.options().size(); i++) {
            buttonsJson.append("[{\"text\":\"")
                    .append(escapeJson(q.options().get(i)))
                    .append("\",\"callback_data\":\"ans_").append(session.currentIndex).append('_').append(i)
                    .append("\"}]");
            if (i < q.options().size() - 1) buttonsJson.append(',');
        }
        buttonsJson.append("]}");

        String text = String.format("❓ <b>سوال %d از %d</b>\n\n%s",
                session.currentIndex + 1, session.questions.size(), q.text());
        api.sendMessageWithKeyboard(chatId, text, buttonsJson.toString());
    }

    static String escapeJson(String s) {
        return s.replace("\\", "\\\\").replace("\"", "\\\"");
    }

    // ================== پردازش پاسخ به دکمه‌ها ==================
    static void handleCallback(Map<String, Object> query) throws IOException {
        String data = JsonValue.asString(query.get("data"), "");
        String callbackId = JsonValue.asString(query.get("id"), "");
        Map<String, Object> from = JsonValue.asMap(query.get("from"));
        long userId = JsonValue.asLong(from.get("id"), 0);
        String userName = JsonValue.asString(from.get("first_name"), "کاربر");
        Map<String, Object> messageObj = JsonValue.asMap(query.get("message"));
        Map<String, Object> chat = JsonValue.asMap(messageObj.get("chat"));
        long chatId = JsonValue.asLong(chat.get("id"), 0);
        long messageId = JsonValue.asLong(messageObj.get("message_id"), 0);

        if (!data.startsWith("ans_")) return;
        String[] parts = data.substring(4).split("_");
        int questionIndex = Integer.parseInt(parts[0]);
        int chosenOption = Integer.parseInt(parts[1]);

        QuizSession session = activeSessions.get(chatId);
        if (session == null || session.currentIndex != questionIndex) {
            api.answerCallbackQuery(callbackId, "⌛ این سوال دیگه فعال نیست.");
            return;
        }
        if (session.answered) {
            api.answerCallbackQuery(callbackId, "این سوال قبلاً جواب داده شده.");
            return;
        }

        Question q = session.questions.get(questionIndex);
        if (chosenOption == q.correctIndex()) {
            session.answered = true;
            addScore(chatId, userId, userName, 1);
            api.answerCallbackQuery(callbackId, "✅ آفرین! جواب درست بود.");
            api.editMessageText(chatId, messageId,
                    "✅ <b>" + userName + "</b> جواب درست داد!\n\nسوال: " + q.text() +
                    "\nجواب درست: " + q.options().get(q.correctIndex()));

            session.currentIndex++;
            if (session.currentIndex < session.questions.size()) {
                sendQuestion(chatId, session);
            } else {
                activeSessions.remove(chatId);
                api.sendMessage(chatId, "🏁 مسابقه تموم شد! برای دیدن نتایج /leaderboard رو بزن.");
            }
        } else {
            api.answerCallbackQuery(callbackId, "❌ اشتباه بود، دوباره امتحان کن!");
        }
    }

    // ================== نمایش لیدربورد ==================
    static void showLeaderboard(long chatId) {
        var chatScores = scores.get(chatId);
        if (chatScores == null || chatScores.isEmpty()) {
            api.sendMessage(chatId, "هنوز هیچ امتیازی توی این چت ثبت نشده. با /quiz شروع کن!");
            return;
        }

        List<Map.Entry<Long, Map<String, Object>>> sorted = new ArrayList<>(chatScores.entrySet());
        sorted.sort((a, b) -> Double.compare(
                (double) b.getValue().getOrDefault("score", 0.0),
                (double) a.getValue().getOrDefault("score", 0.0)));

        StringBuilder sb = new StringBuilder("🏆 <b>لیدربورد این چت</b>\n\n");
        String[] medals = {"🥇", "🥈", "🥉"};
        for (int i = 0; i < Math.min(10, sorted.size()); i++) {
            var entry = sorted.get(i);
            String medal = i < 3 ? medals[i] : (i + 1) + ".";
            sb.append(medal).append(" ").append(entry.getValue().get("name"))
                    .append(" — ").append(((Double) entry.getValue().get("score")).intValue()).append(" امتیاز\n");
        }
        api.sendMessage(chatId, sb.toString());
    }
}
