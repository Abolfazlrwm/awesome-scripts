<div align="center">
  <h1 dir="rtl">🤖 ربات ارز | ArzBot 🌐</h1>
  
  <div dir="rtl">
  <h3>ربات هوشمند تلگرام برای رصد لحظه‌ای نرخ ارز، ارزهای دیجیتال و طلا با قابلیت‌های مدیریتی پیشرفته</h3>
  </div>
  
  <p align="center">
    <img src="https://img.shields.io/badge/Version-1.0.0-brightgreen" alt="Version">
    <img src="https://img.shields.io/badge/Python-3.8%2B-blue" alt="Python">
    <img src="https://img.shields.io/badge/Telegram-Bot-2CA5E0" alt="Telegram Bot">
    <img src="https://img.shields.io/badge/License-MIT-yellow" alt="License">
  </p>

  <p align="center">
    <a href="#-ویژگیها">ویژگی‌ها</a> •
    <a href="#-نصب-و-راه‌اندازی">نصب</a> •
    <a href="#-اجرای-ربات">اجرا</a> •
    <a href="#-دستورات">دستورات</a> •
    <a href="#-مشارکت">مشارکت</a>
  </p>
  
  <p>A feature-rich, multi-language Telegram bot for tracking currency exchange rates, cryptocurrency prices, and gold rates with powerful admin capabilities.</p>
</div>

---

## ✨ ویژگی‌ها | Features

<div align="center">
  <img src="https://via.placeholder.com/800x400.png?text=ArzBot+Demo" alt="ArzBot Demo" width="80%"/>
</div>

<div dir="rtl" align="right">
  <h3>📊 ویژگی‌های اصلی</h3>
  <ul>
    <li><b>نرخ لحظه‌ای ارزها</b> - رصد نرخ ارزهای اصلی و فرعی به صورت زنده</li>
    <li><b>قیمت ارزهای دیجیتال</b> - پیگیری قیمت بیت‌کوین، اتریوم و سایر ارزها</li>
    <li><b>قیمت طلا و سکه</b> - نمایش به‌روز قیمت طلا و سکه به همراه نمودار</li>
    <li><b>پنل مدیریت پیشرفته</b> - مدیریت کامل تنظیمات و کاربران ربات</li>
    <li><b>جستجوی اینلاین</b> - دسترسی سریع به نرخ‌ها در هر چت تلگرام</li>
    <li><b>آمار و گزارش‌دهی</b> - رصد آمار استفاده و تحلیل عملکرد ربات</li>
  </ul>
</div>

### 🌟 Key Features

<div align="left">
  <ul>
    <li><b>Real-time Exchange Rates</b> - Track main and minor currency pairs live</li>
    <li><b>Crypto Prices</b> - Monitor BTC, ETH, and other major cryptocurrencies</li>
    <li><b>Gold & Coin Rates</b> - Updated precious metal prices with charts</li>
    <li><b>Admin Dashboard</b> - Comprehensive bot and user management</li>
    <li><b>Inline Queries</b> - Quick access to rates in any Telegram chat</li>
    <li><b>Analytics</b> - Detailed usage statistics and reporting</li>
  </ul>
</div>

<div dir="rtl">
- **نرخ لحظه‌ای ارزها**: رصد نرخ ارزهای اصلی و فرعی
- **قیمت ارزهای دیجیتال**: پیگیری قیمت بیت‌کوین، اتریوم و سایر ارزها
- **قیمت طلا**: نمایش به‌روز قیمت طلا
- **پنل مدیریت**: مدیریت تنظیمات و کاربران ربات
- **جستجوی اینلاین**: دسترسی سریع به نرخ‌ها در هر چت
- **آمار کاربران**: رصد آمار استفاده از ربات
</div>

- **Real-time Currency Rates**: Track main and minor currency exchange rates
- **Cryptocurrency Prices**: Monitor various cryptocurrencies including BTC, ETH, and more
- **Gold Rates**: Get updated gold prices
- **Admin Panel**: Manage bot settings and users
- **Inline Queries**: Quick access to rates directly in any chat
- **User Statistics**: Track bot usage and user engagement

## 🚀 نصب و راه‌اندازی | Installation

<div align="center">
  <img src="https://via.placeholder.com/600x300.png?text=Installation+Guide" alt="Installation" width="70%"/>
</div>

<div dir="rtl">
### پیش‌نیازها | Prerequisites
- پایتون 3.8 یا بالاتر
- نصب pip (مدیریت بسته‌های پایتون)
- توکن ربات تلگرام از [@BotFather](https://t.me/botfather)
- اطلاعات API تلگرام از [my.telegram.org](https://my.telegram.org/)

### مراحل نصب | Setup

1. **دانلود سورس کد**
   ```bash
   git clone <repository-url>
   cd arz
   ```

2. **نصب پیش‌نیازها**
   ```bash
   pip install -r requirements.txt
   ```

3. **تنظیم متغیرهای محیطی**
   فایل `main.py` را باز کرده و خطوط زیر را پیدا و ویرایش کنید (خطوط 41-45):
   ```python
   # API credentials
   API_ID = '0'  # خط 42: شناسه API تلگرام خود را وارد کنید
   API_HASH = 'your_api_hash_here'  # خط 43: هش API خود را وارد کنید
   BOT_TOKEN = 'PUT-YOUR-KEY-HERE'  # خط 44: توکن ربات خود را وارد کنید
   ADMIN_IDS = [7150795159]  # خط 47: شناسه عددی تلگرام خود را وارد کنید
   ```

   یا فایل `.env` را در پوشه اصلی پروژه با محتوای زیر ایجاد کنید:
   ```env
   API_ID=your_telegram_api_id
   API_HASH=your_telegram_api_hash
   BOT_TOKEN=your_telegram_bot_token
   ADMIN_IDS=123456789,987654321  # شناسه‌های کاربران مدیریت (با کاما جدا شوند)
   ```

4. **راه‌اندازی پایگاه داده**
   ```bash
   python -m migrations.001_add_metadata_to_user_stats
   python -m migrations.002_add_force_join_channels
   ```
</div>

### Prerequisites
- Python 3.8 or higher
- pip (Python package manager)
- Telegram Bot Token from [@BotFather](https://t.me/botfather)
- Telegram API credentials from [my.telegram.org](https://my.telegram.org/)

### Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd arz
   ```

2. **Install dependencies**
   ```bash
   pip install -r requirements.txt
   ```

3. **Configure environment variables**
   Open `main.py` and edit the following lines (lines 41-45):
   ```python
   # API credentials
   API_ID = '0'  # Line 42: Enter your Telegram API ID
   API_HASH = 'your_api_hash_here'  # Line 43: Enter your API Hash
   BOT_TOKEN = 'PUT-YOUR-KEY-HERE'  # Line 44: Enter your Bot Token
   ADMIN_IDS = [7150795159]  # Line 47: Enter your Telegram user ID
   ```
   
   OR create a `.env` file in the project root with the following variables:
   ```
   API_ID=your_telegram_api_id
   API_HASH=your_telegram_api_hash
   BOT_TOKEN=your_telegram_bot_token
   ADMIN_IDS=123456789,987654321  # Comma-separated list of admin user IDs
   ```

4. **Initialize the database**
   ```bash
   python -m migrations.001_add_metadata_to_user_stats
   python -m migrations.002_add_force_join_channels
   ```

## 🏃‍♂️ اجرای ربات | Running the Bot

<div align="center">
  <table>
    <tr>
      <th>عملیات</th>
      <th>دستور</th>
    </tr>
    <tr>
      <td>اجرای عادی</td>
      <td><code>python main.py</code></td>
    </tr>
    <tr>
      <td>اجرای با PM2</td>
      <td><code>pm2 start main.py --name arzbot --interpreter python3</code></td>
    </tr>
    <tr>
      <td>مشاهده لاگ‌ها</td>
      <td><code>pm2 logs arzbot</code></td>
    </tr>
  </table>
</div>

<div dir="rtl">
برای اجرای ربات، دستور زیر را در ترمینال وارد کنید:

```bash
python main.py
```

برای محیط تولید، توصیه می‌شود از یک مدیریت‌گر فرآیند مانند `pm2` یا `systemd` برای اجرای مداوم ربات استفاده کنید.

مثال استفاده از PM2:
```bash
npm install -g pm2
pm2 start main.py --name arzbot --interpreter python3
pm2 save
pm2 startup
```
</div>

To run the bot, use the following command in your terminal:

```bash
python main.py
```

For production, consider using a process manager like `pm2` or `systemd` to keep the bot running.

Example using PM2:
```bash
npm install -g pm2
pm2 start main.py --name arzbot --interpreter python3
pm2 save
pm2 startup
```

## 🎛 دستورات مدیریتی | Admin Commands

<div align="center">
  <table>
    <tr>
      <th>دستور</th>
      <th>توضیحات</th>
      <th>Command</th>
      <th>Description</th>
    </tr>
    <tr>
      <td><code>/admin</code></td>
      <td>دسترسی به پنل مدیریت</td>
      <td><code>/admin</code></td>
      <td>Access admin panel</td>
    </tr>
    <tr>
      <td><code>/stats</code></td>
      <td>مشاهده آمار ربات</td>
      <td><code>/stats</code></td>
      <td>View bot statistics</td>
    </tr>
    <tr>
      <td><code>/broadcast</code></td>
      <td>ارسال پیام به تمام کاربران</td>
      <td><code>/broadcast</code></td>
      <td>Send message to all users</td>
    </tr>
  </table>
</div>

<div dir="rtl">
- `/admin` - دسترسی به پنل مدیریت
- `/stats` - مشاهده آمار ربات
- `/broadcast` - ارسال پیام به تمام کاربران
- `/add_admin [آیدی کاربر]` - افزودن مدیر جدید
- `/remove_admin [آیدی کاربر]` - حذف مدیر
</div>

- `/admin` - Access admin panel
- `/stats` - View bot statistics
- `/broadcast` - Send message to all users
- `/add_admin [user_id]` - Add new admin
- `/remove_admin [user_id]` - Remove admin

## 🤖 دستورات کاربری | User Commands

<div align="center">
  <table>
    <tr>
      <th>دستور</th>
      <th>توضیحات</th>
      <th>Command</th>
      <th>Description</th>
    </tr>
    <tr>
      <td><code>/start</code></td>
      <td>شروع کار با ربات</td>
      <td><code>/start</code></td>
      <td>Start the bot</td>
    </tr>
    <tr>
      <td><code>/menu</code></td>
      <td>نمایش منوی اصلی</td>
      <td><code>/menu</code></td>
      <td>Show main menu</td>
    </tr>
    <tr>
      <td><code>/crypto</code></td>
      <td>نمایش قیمت ارزهای دیجیتال</td>
      <td><code>/crypto</code></td>
      <td>Show cryptocurrency prices</td>
    </tr>
  </table>
</div>

<div dir="rtl">
- `/start` - شروع کار با ربات
- `/menu` - نمایش منوی اصلی
- `/crypto` - نمایش قیمت ارزهای دیجیتال
- `/gold` - نمایش قیمت طلا
- `/convert` - تبدیل ارزها به یکدیگر
</div>

- `/start` - Start the bot
- `/menu` - Show main menu
- `/crypto` - Show cryptocurrency prices
- `/gold` - Show gold rates
- `/convert` - Convert between currencies

## 📊 Supported Currencies

<div align="center">
  <table>
    <tr>
      <th>💵 ارزهای اصلی</th>
      <th>💰 ارزهای دیجیتال</th>
      <th>🏦 سایر ارزها</th>
    </tr>
    <tr>
      <td>دلار آمریکا (USD)</td>
      <td>بیت‌کوین (BTC)</td>
      <td>یورو (EUR)</td>
    </tr>
    <tr>
      <td>یورو (EUR)</td>
      <td>اتریوم (ETH)</td>
      <td>پوند (GBP)</td>
    </tr>
    <tr>
      <td>ین ژاپن (JPY)</td>
      <td>تتر (USDT)</td>
      <td>فرانک سوئیس (CHF)</td>
    </tr>
  </table>
</div>

### Main Currencies
- USD (US Dollar)
- EUR (Euro)
- GBP (British Pound)
- More...

### Cryptocurrencies
- BTC (Bitcoin)
- ETH (Ethereum)
- USDT (Tether)
- And many more...

## 🤝 مشارکت | Contributing

<div dir="rtl" align="right">
  <p>ما از مشارکت‌های شما استقبال می‌کنیم! برای مشارکت در توسعه ربات:</p>
  <ol>
    <li>ریپوزیتوری را فورک کنید</li>
    <li>شاخه جدیدی ایجاد کنید: <code>git checkout -b feature/feature-name</code></li>
    <li>تغییرات خود را کامیت کنید: <code>git commit -m 'Add some feature'</code></li>
    <li>به شاخه خود پوش کنید: <code>git push origin feature/feature-name</code></li>
    <li>یک درخواست کشش (Pull Request) باز کنید</li>
  </ol>
</div>

<p>For contributing to the project, please read <a href="CONTRIBUTING.md">CONTRIBUTING.md</a> for details on our code of conduct and the process for submitting pull requests.</p>

## 📜 مجوز | License

<div dir="rtl" align="center">
  <p>این پروژه تحت مجوز MIT است - برای جزئیات به فایل <a href="LICENSE">LICENSE</a> مراجعه کنید.</p>
</div>

<p>This project is licensed under the MIT License - see the <a href="LICENSE">LICENSE</a> file for details.</p>

## 🙏 تشکر | Acknowledgments

<div dir="rtl" align="center">
  <p>از تمامی عزیزانی که در توسعه این پروژه همکاری کردند صمیمانه تشکر می‌کنیم.</p>
  <p>توسعه‌دهندگان اصلی:</p>
  <ul>
    <li><a href="#">نام شما</a></li>
  </ul>
</div>

<p>Special thanks to all contributors who have helped make this project better.</p>

---

<div align="center">
  <p>ساخته شده با ❤️ و ☕ توسط تیم توسعه‌دهندگان</p>
  <p>Made with ❤️ and ☕ by the development team</p>
</div>

Edit `config.py` to customize:
- Default currencies
- Update intervals
- API endpoints
- Bot behavior

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 مجوز | License

<div dir="rtl">
این پروژه تحت مجوز MIT منتشر شده است. برای جزئیات بیشتر فایل [LICENSE](LICENSE) را مشاهده کنید.
</div>

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 تشکر و قدردانی | Acknowledgments

<div dir="rtl">
- [Telethon](https://docs.telethon.dev/) - کتابخانه کلاینت تلگرام
- [Binance API](https://github.com/binance/binance-spot-api-docs) - اطلاعات ارزهای دیجیتال
- تمامی عزیزانی که در توسعه این پروژه همکاری کردند

### کانال و گروه تلگرام | Telegram Channel & Group

- کانال تلگرام: [Telebotcraft](https://t.me/Telebotcraft) - آخرین به‌روزرسانی‌ها و آموزش‌ها
- گروه پشتیبانی: [Telebotcraft Group](https://t.me/TelebotcraftGroup) - پرسش و پاسخ و پشتیبانی
</div>

- [Telethon](https://docs.telethon.dev/) - Telegram client library
- [Binance API](https://github.com/binance/binance-spot-api-docs) - Cryptocurrency data
- All contributors who helped in development

### Telegram Channel & Group

- Channel: [Telebotcraft](https://t.me/Telebotcraft) - Latest updates and tutorials
- Support Group: [Telebotcraft Group](https://t.me/TelebotcraftGroup) - Q&A and support

---

<div align="center">
  ساخته شده با ❤️ برای جامعه کریپتو و فارکس | Made with ❤️ for the crypto and forex community
</div>
