// ========== ربات مانیتور آپ‌تایم سایت/سرور (Uptime Monitor Bot) ==========
// به‌صورت دوره‌ای سایت‌ها/سرورهای مشخص‌شده رو پینگ می‌کنه و در صورت قطعی یا برگشت،
// به ادمین در تلگرام اطلاع می‌ده. همچنین یک صفحه‌ی وضعیت HTML/CSS ساده هم تولید می‌کنه.

using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Text.Json;
using System.Threading;
using System.Threading.Tasks;

class Program
{
    // ---------- تنظیمات ----------
    const string BOT_TOKEN = "YOUR_BOT_TOKEN_HERE";   // توکن ربات را از @BotFather بگیر
    const long ADMIN_ID = 123456789;                    // آیدی عددی ادمین از @userinfobot
    const int CHECK_INTERVAL_SECONDS = 60;              // هر چند ثانیه سایت‌ها چک بشن
    const int HTTP_TIMEOUT_SECONDS = 10;                // حداکثر زمان انتظار برای پاسخ هر سایت
    static readonly string DataFile = Path.Combine(AppContext.BaseDirectory, "sites.json");
    static readonly string StatusHtmlFile = Path.Combine(AppContext.BaseDirectory, "status.html");

    static readonly HttpClient ApiClient = new HttpClient();
    static readonly HttpClient CheckClient = new HttpClient { Timeout = TimeSpan.FromSeconds(HTTP_TIMEOUT_SECONDS) };
    static List<SiteEntry> sites = new();
    static long lastUpdateId = 0;

    class SiteEntry
    {
        public string Name { get; set; } = "";
        public string Url { get; set; } = "";
        public bool IsUp { get; set; } = true;
        public int ResponseTimeMs { get; set; } = 0;
        public string LastChecked { get; set; } = "";
    }

    static async Task Main()
    {
        Console.WriteLine("📡 ربات مانیتور آپ‌تایم روشن شد...");
        LoadSites();

        // یک تسک جدا برای بررسی دوره‌ای سایت‌ها
        _ = Task.Run(MonitorLoop);

        // حلقه‌ی اصلی: دریافت پیام‌های تلگرام (Long Polling)
        while (true)
        {
            try
            {
                await PollUpdatesAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"❌ خطا در دریافت آپدیت: {ex.Message}");
                await Task.Delay(5000);
            }
        }
    }

    // ================== ذخیره و بارگذاری لیست سایت‌ها ==================
    static void LoadSites()
    {
        if (!File.Exists(DataFile)) { sites = new List<SiteEntry>(); return; }
        try
        {
            var json = File.ReadAllText(DataFile);
            sites = JsonSerializer.Deserialize<List<SiteEntry>>(json) ?? new List<SiteEntry>();
        }
        catch { sites = new List<SiteEntry>(); }
    }

    static void SaveSites()
    {
        var json = JsonSerializer.Serialize(sites, new JsonSerializerOptions { WriteIndented = true });
        File.WriteAllText(DataFile, json, Encoding.UTF8);
    }

    // ================== حلقه‌ی بررسی دوره‌ای سایت‌ها ==================
    static async Task MonitorLoop()
    {
        while (true)
        {
            foreach (var site in sites.ToList())
            {
                bool wasUp = site.IsUp;
                var (isUp, responseMs) = await CheckSiteAsync(site.Url);

                site.IsUp = isUp;
                site.ResponseTimeMs = responseMs;
                site.LastChecked = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss");

                if (wasUp && !isUp)
                {
                    await SendMessageAsync(ADMIN_ID, $"🔴 <b>{site.Name}</b> از دسترس خارج شد!\n🔗 {site.Url}");
                }
                else if (!wasUp && isUp)
                {
                    await SendMessageAsync(ADMIN_ID, $"🟢 <b>{site.Name}</b> دوباره برگشت آنلاین شد.\n🔗 {site.Url}\n⏱ زمان پاسخ: {responseMs}ms");
                }
            }

            SaveSites();
            GenerateStatusHtml();
            await Task.Delay(CHECK_INTERVAL_SECONDS * 1000);
        }
    }

    static async Task<(bool isUp, int responseMs)> CheckSiteAsync(string url)
    {
        var start = DateTime.Now;
        try
        {
            var response = await CheckClient.GetAsync(url);
            var elapsed = (int)(DateTime.Now - start).TotalMilliseconds;
            return (response.IsSuccessStatusCode, elapsed);
        }
        catch
        {
            return (false, 0);
        }
    }

    // ================== ساخت صفحه‌ی وضعیت HTML/CSS ==================
    static void GenerateStatusHtml()
    {
        var sb = new StringBuilder();
        sb.AppendLine("<!DOCTYPE html>");
        sb.AppendLine("<html lang=\"fa\" dir=\"rtl\"><head><meta charset=\"UTF-8\">");
        sb.AppendLine("<title>وضعیت سرویس‌ها</title><style>");
        sb.AppendLine(@"
            body { font-family: Tahoma, sans-serif; background:#0f1115; color:#eee; padding:30px; }
            h1 { text-align:center; margin-bottom:30px; }
            .card { background:#181b22; border-radius:10px; padding:16px 20px; margin:10px auto; max-width:600px;
                    display:flex; justify-content:space-between; align-items:center; border:1px solid #2a2e38; }
            .status-up { color:#3ddc84; font-weight:bold; }
            .status-down { color:#ff5c5c; font-weight:bold; }
            .meta { font-size:13px; color:#999; }
        ");
        sb.AppendLine("</style></head><body>");
        sb.AppendLine("<h1>📡 وضعیت سرویس‌ها</h1>");

        foreach (var site in sites)
        {
            string statusClass = site.IsUp ? "status-up" : "status-down";
            string statusText = site.IsUp ? "✅ آنلاین" : "❌ آفلاین";
            sb.AppendLine("<div class=\"card\"><div>");
            sb.AppendLine($"<div><b>{System.Net.WebUtility.HtmlEncode(site.Name)}</b></div>");
            sb.AppendLine($"<div class=\"meta\">{System.Net.WebUtility.HtmlEncode(site.Url)} — آخرین بررسی: {site.LastChecked}</div>");
            sb.AppendLine("</div>");
            sb.AppendLine($"<div class=\"{statusClass}\">{statusText}</div>");
            sb.AppendLine("</div>");
        }

        sb.AppendLine("</body></html>");
        File.WriteAllText(StatusHtmlFile, sb.ToString(), Encoding.UTF8);
    }

    // ================== ارتباط با تلگرام (Long Polling) ==================
    static async Task PollUpdatesAsync()
    {
        var url = $"https://api.telegram.org/bot{BOT_TOKEN}/getUpdates?offset={lastUpdateId + 1}&timeout=30";
        var response = await ApiClient.GetStringAsync(url);
        using var doc = JsonDocument.Parse(response);

        if (!doc.RootElement.GetProperty("ok").GetBoolean()) return;

        foreach (var update in doc.RootElement.GetProperty("result").EnumerateArray())
        {
            lastUpdateId = update.GetProperty("update_id").GetInt64();
            if (update.TryGetProperty("message", out var message))
            {
                await HandleMessageAsync(message);
            }
        }
    }

    static async Task HandleMessageAsync(JsonElement message)
    {
        long chatId = message.GetProperty("chat").GetProperty("id").GetInt64();
        string text = message.TryGetProperty("text", out var t) ? t.GetString() ?? "" : "";
        long fromId = message.GetProperty("from").GetProperty("id").GetInt64();

        if (text == "/start")
        {
            await SendMessageAsync(chatId,
                "📡 سلام! من ربات مانیتور آپ‌تایم هستم.\n\n" +
                "/status — وضعیت فعلی همه‌ی سایت‌ها\n" +
                (fromId == ADMIN_ID ?
                    "/addsite [نام] [آدرس] — افزودن سایت جدید\n/removesite [نام] — حذف سایت" : ""));
            return;
        }

        if (text == "/status")
        {
            if (sites.Count == 0) { await SendMessageAsync(chatId, "هنوز هیچ سایتی ثبت نشده."); return; }
            var lines = sites.Select(s => $"{(s.IsUp ? "🟢" : "🔴")} <b>{s.Name}</b> — {(s.IsUp ? "آنلاین" : "آفلاین")} ({s.ResponseTimeMs}ms)");
            await SendMessageAsync(chatId, "📊 <b>وضعیت سایت‌ها:</b>\n\n" + string.Join("\n", lines));
            return;
        }

        if (fromId != ADMIN_ID) return; // بقیه‌ی دستورات فقط برای ادمین

        if (text.StartsWith("/addsite "))
        {
            var parts = text.Substring(9).Trim().Split(' ', 2);
            if (parts.Length != 2) { await SendMessageAsync(chatId, "❗ فرمت درست: /addsite نام آدرس"); return; }

            sites.Add(new SiteEntry { Name = parts[0], Url = parts[1], IsUp = true, LastChecked = "هنوز چک نشده" });
            SaveSites();
            await SendMessageAsync(chatId, $"✅ سایت «{parts[0]}» اضافه شد.");
            return;
        }

        if (text.StartsWith("/removesite "))
        {
            var name = text.Substring(12).Trim();
            var removed = sites.RemoveAll(s => s.Name == name);
            SaveSites();
            await SendMessageAsync(chatId, removed > 0 ? $"✅ سایت «{name}» حذف شد." : "❌ سایتی با این نام پیدا نشد.");
            return;
        }
    }

    static async Task SendMessageAsync(long chatId, string text)
    {
        var url = $"https://api.telegram.org/bot{BOT_TOKEN}/sendMessage";
        var payload = new Dictionary<string, string>
        {
            ["chat_id"] = chatId.ToString(),
            ["text"] = text,
            ["parse_mode"] = "HTML"
        };
        try
        {
            await ApiClient.PostAsync(url, new FormUrlEncodedContent(payload));
        }
        catch (Exception ex)
        {
            Console.WriteLine($"❌ خطا در ارسال پیام: {ex.Message}");
        }
    }
}
