// ========== ربات اعلان خودکار فید RSS (RSS News Bot) ==========
// فیدهای RSS را دوره‌ای چک می‌کند و پست‌های جدید را به کانال/گروه تلگرام می‌فرستد.

#include <iostream>
#include <fstream>
#include <sstream>
#include <string>
#include <vector>
#include <thread>
#include <chrono>
#include <regex>
#include <curl/curl.h>
#include <nlohmann/json.hpp>

using json = nlohmann::json;

// ---------- تنظیمات ----------
const std::string BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"; // توکن ربات را از @BotFather بگیر
const long long ADMIN_ID = 123456789;                   // آیدی عددی ادمین از @userinfobot
const int CHECK_INTERVAL_SECONDS = 300;                 // هر چند ثانیه فیدها چک شوند (پیش‌فرض ۵ دقیقه)
const std::string DATA_FILE = "rss_data.json";

// ================== کمکی: دریافت محتوای یک URL با libcurl ==================
static size_t writeCallback(void* contents, size_t size, size_t nmemb, std::string* out) {
    out->append((char*)contents, size * nmemb);
    return size * nmemb;
}

std::string httpGet(const std::string& url) {
    CURL* curl = curl_easy_init();
    std::string response;
    if (curl) {
        curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writeCallback);
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response);
        curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1L);
        curl_easy_setopt(curl, CURLOPT_TIMEOUT, 30L);
        curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 1L);
        CURLcode res = curl_easy_perform(curl);
        if (res != CURLE_OK) {
            std::cerr << "خطای curl: " << curl_easy_strerror(res) << std::endl;
        }
        curl_easy_cleanup(curl);
    }
    return response;
}

std::string urlEncode(const std::string& s) {
    CURL* curl = curl_easy_init();
    char* out = curl_easy_escape(curl, s.c_str(), (int)s.length());
    std::string result(out);
    curl_free(out);
    curl_easy_cleanup(curl);
    return result;
}

void sendTelegramMessage(long long chatId, const std::string& text) {
    std::string url = "https://api.telegram.org/bot" + BOT_TOKEN + "/sendMessage";
    CURL* curl = curl_easy_init();
    if (!curl) return;
    std::string postFields = "chat_id=" + std::to_string(chatId) +
                              "&text=" + urlEncode(text) +
                              "&parse_mode=HTML&disable_web_page_preview=false";
    std::string response;
    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, postFields.c_str());
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writeCallback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response);
    curl_easy_setopt(curl, CURLOPT_TIMEOUT, 20L);
    curl_easy_perform(curl);
    curl_easy_cleanup(curl);
}

// ================== استخراج ساده‌ی آیتم‌های RSS با regex ==================
struct RssItem {
    std::string title;
    std::string link;
    std::string guid;
};

std::string extractTag(const std::string& block, const std::string& tag) {
    std::regex re("<" + tag + "[^>]*>(?:<!\\[CDATA\\[)?([\\s\\S]*?)(?:\\]\\]>)?</" + tag + ">");
    std::smatch m;
    if (std::regex_search(block, m, re)) return m[1].str();
    return "";
}

std::vector<RssItem> parseRssItems(const std::string& xml) {
    std::vector<RssItem> items;
    std::regex itemRe("<item[\\s\\S]*?</item>");
    auto begin = std::sregex_iterator(xml.begin(), xml.end(), itemRe);
    auto end = std::sregex_iterator();
    for (auto it = begin; it != end; ++it) {
        std::string block = it->str();
        RssItem item;
        item.title = extractTag(block, "title");
        item.link = extractTag(block, "link");
        item.guid = extractTag(block, "guid");
        if (item.guid.empty()) item.guid = item.link;
        if (!item.title.empty()) items.push_back(item);
    }
    return items;
}

// ================== ذخیره‌سازی JSON (فیدها + آیتم‌های دیده‌شده) ==================
json loadData() {
    std::ifstream f(DATA_FILE);
    if (!f.good()) return json{{"feeds", json::array()}, {"seen", json::array()}};
    json data;
    f >> data;
    return data;
}

void saveData(const json& data) {
    std::ofstream f(DATA_FILE);
    f << data.dump(2);
}

bool isSeen(const json& data, const std::string& guid) {
    for (auto& g : data["seen"]) {
        if (g.get<std::string>() == guid) return true;
    }
    return false;
}

void markSeen(json& data, const std::string& guid) {
    data["seen"].push_back(guid);
    // فقط ۵۰۰ مورد آخر رو نگه داریم که فایل بی‌نهایت بزرگ نشه
    if (data["seen"].size() > 500) {
        json trimmed = json::array();
        size_t start = data["seen"].size() - 500;
        for (size_t i = start; i < data["seen"].size(); i++) trimmed.push_back(data["seen"][i]);
        data["seen"] = trimmed;
    }
}

// ================== بررسی همه‌ی فیدها و ارسال آیتم‌های جدید ==================
void checkFeeds() {
    json data = loadData();
    bool changed = false;

    for (auto& feed : data["feeds"]) {
        std::string url = feed["url"].get<std::string>();
        long long targetChat = feed["chat_id"].get<long long>();

        std::string xml = httpGet(url);
        if (xml.empty()) continue;

        auto items = parseRssItems(xml);
        // آیتم‌ها معمولاً از جدید به قدیم مرتبن؛ برعکس می‌کنیم تا ارسال به ترتیب زمانی درست باشه
        for (auto it = items.rbegin(); it != items.rend(); ++it) {
            if (isSeen(data, it->guid)) continue;
            std::string message = "📰 <b>" + it->title + "</b>\n" + it->link;
            sendTelegramMessage(targetChat, message);
            markSeen(data, it->guid);
            changed = true;
        }
    }

    if (changed) saveData(data);
}

// ================== دستورات مدیریتی از طریق تلگرام (Long Polling) ==================
long long lastUpdateId = 0;

void handleMessage(const json& message) {
    long long chatId = message["chat"]["id"].get<long long>();
    std::string text = message.value("text", "");
    long long fromId = message["from"]["id"].get<long long>();

    if (text == "/start") {
        sendTelegramMessage(chatId,
            "📰 سلام! من ربات اعلان خودکار RSS هستم.\n\n"
            "/addfeed [آدرس فید] — افزودن فید جدید به همین چت (فقط ادمین)\n"
            "/listfeeds — نمایش فیدهای ثبت‌شده در این چت\n"
            "/removefeed [شماره] — حذف یک فید");
        return;
    }

    if (text.rfind("/addfeed ", 0) == 0) {
        if (fromId != ADMIN_ID) { sendTelegramMessage(chatId, "⛔ فقط ادمین می‌تونه فید اضافه کنه."); return; }
        std::string feedUrl = text.substr(9);
        json data = loadData();
        json newFeed = {{"url", feedUrl}, {"chat_id", chatId}};
        data["feeds"].push_back(newFeed);
        saveData(data);
        sendTelegramMessage(chatId, "✅ فید اضافه شد:\n" + feedUrl);
        return;
    }

    if (text == "/listfeeds") {
        json data = loadData();
        std::string result = "📋 فیدهای این چت:\n\n";
        int idx = 1;
        for (auto& feed : data["feeds"]) {
            if (feed["chat_id"].get<long long>() == chatId) {
                result += std::to_string(idx) + ". " + feed["url"].get<std::string>() + "\n";
            }
            idx++;
        }
        sendTelegramMessage(chatId, result);
        return;
    }

    if (text.rfind("/removefeed ", 0) == 0) {
        if (fromId != ADMIN_ID) { sendTelegramMessage(chatId, "⛔ فقط ادمین می‌تونه فید حذف کنه."); return; }
        int removeIndex = std::stoi(text.substr(12)) - 1;
        json data = loadData();
        if (removeIndex >= 0 && removeIndex < (int)data["feeds"].size()) {
            data["feeds"].erase(data["feeds"].begin() + removeIndex);
            saveData(data);
            sendTelegramMessage(chatId, "✅ فید حذف شد.");
        } else {
            sendTelegramMessage(chatId, "❌ شماره‌ی نامعتبر.");
        }
    }
}

void pollUpdates() {
    std::string url = "https://api.telegram.org/bot" + BOT_TOKEN +
                       "/getUpdates?offset=" + std::to_string(lastUpdateId + 1) + "&timeout=25";
    std::string response = httpGet(url);
    if (response.empty()) return;

    try {
        json parsed = json::parse(response);
        if (!parsed.value("ok", false)) return;
        for (auto& update : parsed["result"]) {
            lastUpdateId = update["update_id"].get<long long>();
            if (update.contains("message")) handleMessage(update["message"]);
        }
    } catch (const std::exception& e) {
        std::cerr << "خطای پارس JSON: " << e.what() << std::endl;
    }
}

// ================== نقطه‌ی ورود برنامه ==================
int main() {
    curl_global_init(CURL_GLOBAL_ALL);
    std::cout << "📰 ربات اعلان RSS روشن شد..." << std::endl;

    auto lastCheck = std::chrono::steady_clock::now() - std::chrono::seconds(CHECK_INTERVAL_SECONDS);

    while (true) {
        pollUpdates();

        auto now = std::chrono::steady_clock::now();
        if (std::chrono::duration_cast<std::chrono::seconds>(now - lastCheck).count() >= CHECK_INTERVAL_SECONDS) {
            checkFeeds();
            lastCheck = now;
        }

        std::this_thread::sleep_for(std::chrono::seconds(2));
    }

    curl_global_cleanup();
    return 0;
}
