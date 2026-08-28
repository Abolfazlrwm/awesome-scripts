# 🤖 DigitIDBot - Telegram Numeric ID Finder Bot

Welcome to **DigitIDBot** – a Telegram bot that helps users find their **numeric Telegram ID** in seconds!  
This bot also allows you to extract the numeric ID of **any forwarded message** (if forwarding is allowed by the sender).

---

## ✨ Features

- 🆔 Displays the user's **numeric Telegram ID**.
- 📤 Detects and reveals the numeric ID of forwarded users or channels.
- 🧠 Educates users about what numeric IDs are and how they work.
- 🧼 Clean and simple interface.

---

## 📦 Requirements

Make sure you have Python 3.8+ installed.

Install dependencies with:

```bash
pip install -r requirements.txt
```

---

## 📁 File Structure

```
├── .env.example   # Put Your Token And ADMIN_USER_IDS here (remove .example)
├── .gitignore   # Just a GitIgnore!
├── README.md   # You Are Here!
├── config.py   # Configuration File For Bot
├── database.py   # Make Database And Queries Here
├── main.py   # Main File of Project
├── migrate_users.py   # A python file that exports every user from JSON to SQLite
├── handlers/
│   ├── __init__.py   # Initialize the handlers package
│   ├── messages.py   # Define message templates for bot responses
│   ├── commands.py   # Handle bot commands like /start, /help, and /alive
│   └── rate_limit.py   # Implement rate-limiting for user messages
```

---

## ⚙️ Usage

1. Replace the bot token in `.env`:

```python
TOKEN = "YOUR_BOT_TOKEN_HERE"
```

2. Run the bot:

```bash
python main.py
```

3. Start the bot in Telegram by typing `/start`.  
Forward a message from someone else to get their numeric ID (if visible).

---

## 🧪 Example Commands

- `/start` – Show welcome message and your numeric ID  
- `/help` – What is a numeric ID and how to use it  
- Forward a message – Get the original sender’s ID, name, and username

---

## 💡 What is a Telegram Numeric ID?

Every Telegram account has a unique, **unchangeable numeric ID**.  
Unlike usernames or display names, it cannot be modified or deleted, and it remains assigned to your account unless you delete your Telegram profile.

You can use this numeric ID in deep links like:

```
tg://openmessage?user_id=123456789
```

This opens a private chat directly (if permissions allow it).

---

## 🔐 Privacy & Security

- No logging of user data
- Using SQLite For storing user number IDs for sending Bulk message
- Forwarded messages are only used temporarily to extract metadata

---

## 📄 License

MIT License
---

## 🌐 Connect

- ✉️ Contact the author: [@ItsRezNum](https://t.me/ItsRezNum)

---

