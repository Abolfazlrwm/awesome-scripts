// ========== کلاس ارتباط با Telegram Bot API (بدون کتابخانه‌ی خارجی) ==========

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.util.List;
import java.util.Map;

public class TelegramApi {
    private final String token;
    private final HttpClient client;

    public TelegramApi(String token) {
        this.token = token;
        this.client = HttpClient.newHttpClient();
    }

    private String urlEncode(String s) {
        return URLEncoder.encode(s, StandardCharsets.UTF_8);
    }

    // ارسال درخواست GET ساده (برای getUpdates و getMe)
    public Map<String, Object> get(String method, String queryString) throws Exception {
        String url = "https://api.telegram.org/bot" + token + "/" + method +
                (queryString.isEmpty() ? "" : "?" + queryString);
        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(url))
                .timeout(java.time.Duration.ofSeconds(40))
                .GET()
                .build();
        HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));
        return JsonValue.asMap(JsonValue.parse(response.body()));
    }

    // ارسال درخواست POST با پارامترهای فرم (application/x-www-form-urlencoded)
    public Map<String, Object> post(String method, Map<String, String> params) throws Exception {
        StringBuilder body = new StringBuilder();
        boolean first = true;
        for (var entry : params.entrySet()) {
            if (!first) body.append('&');
            first = false;
            body.append(urlEncode(entry.getKey())).append('=').append(urlEncode(entry.getValue()));
        }

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create("https://api.telegram.org/bot" + token + "/" + method))
                .timeout(java.time.Duration.ofSeconds(30))
                .header("Content-Type", "application/x-www-form-urlencoded")
                .POST(HttpRequest.BodyPublishers.ofString(body.toString(), StandardCharsets.UTF_8))
                .build();
        HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));
        return JsonValue.asMap(JsonValue.parse(response.body()));
    }

    public void sendMessage(long chatId, String text) {
        try {
            post("sendMessage", Map.of("chat_id", String.valueOf(chatId), "text", text, "parse_mode", "HTML"));
        } catch (Exception e) {
            System.out.println("خطا در ارسال پیام: " + e.getMessage());
        }
    }

    public void sendMessageWithKeyboard(long chatId, String text, String replyMarkupJson) {
        try {
            post("sendMessage", Map.of(
                    "chat_id", String.valueOf(chatId),
                    "text", text,
                    "parse_mode", "HTML",
                    "reply_markup", replyMarkupJson));
        } catch (Exception e) {
            System.out.println("خطا در ارسال پیام: " + e.getMessage());
        }
    }

    public void editMessageText(long chatId, long messageId, String text) {
        try {
            post("editMessageText", Map.of(
                    "chat_id", String.valueOf(chatId),
                    "message_id", String.valueOf(messageId),
                    "text", text,
                    "parse_mode", "HTML"));
        } catch (Exception e) {
            System.out.println("خطا در ویرایش پیام: " + e.getMessage());
        }
    }

    public void answerCallbackQuery(String callbackQueryId, String text) {
        try {
            post("answerCallbackQuery", Map.of("callback_query_id", callbackQueryId, "text", text));
        } catch (Exception e) {
            System.out.println("خطا در پاسخ به callback: " + e.getMessage());
        }
    }
}
