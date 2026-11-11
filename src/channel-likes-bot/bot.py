import asyncio
import sqlite3
import re

from telethon import TelegramClient, events, Button


API_ID = 0        # عددی — از my.telegram.org (بخش API development tools)
API_HASH = ""     # API HASH از my.telegram.org
BOT_TOKEN = ""    # توکن ربات از @BotFather

CHANNEL_ID = 0    # آیدی عددی کانال مقصد (مثل -1001234567890)

ADMIN_ID = 0      # آیدی عددی ادمین (برای محدودسازی دسترسی اختیاری)

DB_PATH = 'likes.db'


conn = sqlite3.connect(DB_PATH)
cur = conn.cursor()

cur.execute("""
CREATE TABLE IF NOT EXISTS posts (
    message_id INTEGER PRIMARY KEY,
    chat_id INTEGER NOT NULL,
    like_count INTEGER NOT NULL DEFAULT 0
)
""")

cur.execute("""
CREATE TABLE IF NOT EXISTS likes (
    message_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    UNIQUE(message_id, user_id)
)
""")

conn.commit()


client = TelegramClient('bot_session', API_ID, API_HASH).start(bot_token=BOT_TOKEN)



@client.on(events.NewMessage(incoming=True))
async def handler_new_message(event):
    
    # if event.sender_id != ADMIN_ID:
    #     return


    try:
        await event.reply("Your message has been received and posted to the channel.")
    except:
        pass
        
        
        
    msg_text = event.message.message or ""

    # ارسال به کانال
    sent = await client.send_message(
        CHANNEL_ID,
        msg_text if msg_text else None,
        file=event.message.media if event.message.media else None,
        buttons=[
            [Button.inline("❤ 0", data=f"like:{0}:{0}")]  
        ]
    )

    
    cur.execute(
        "INSERT OR IGNORE INTO posts (message_id, chat_id, like_count) VALUES (?, ?, ?)",
        (sent.id, CHANNEL_ID, 0)
    )
    conn.commit()

    
    await sent.edit(
        buttons=[
            [Button.inline(f"❤ 0", data=f"like:{sent.id}")]
        ]
    )



@client.on(events.CallbackQuery)
async def callback_handler(event):
    data = event.data.decode('utf-8')

    m = re.match(r"like:(\d+)", data)
    if not m:
        return

    message_id = int(m.group(1))
    user_id = event.sender_id

    
    cur.execute(
        "SELECT 1 FROM likes WHERE message_id = ? AND user_id = ?",
        (message_id, user_id)
    )
    already_liked = cur.fetchone()

    if already_liked:
        
        await event.answer("You have already liked this post.", alert=False)
        return

    
    cur.execute(
        "INSERT INTO likes (message_id, user_id) VALUES (?, ?)",
        (message_id, user_id)
    )

    
    cur.execute(
        "SELECT like_count FROM posts WHERE message_id = ?",
        (message_id,)
    )
    row = cur.fetchone()
    if row is None:
        
        like_count = 1
        cur.execute(
            "INSERT OR REPLACE INTO posts (message_id, chat_id, like_count) VALUES (?, ?, ?)",
            (message_id, CHANNEL_ID, like_count)
        )
    else:
        like_count = row[0] + 1
        cur.execute(
            "UPDATE posts SET like_count = ? WHERE message_id = ?",
            (like_count, message_id)
        )

    conn.commit()

    
    try:
        
        msg = await client.get_messages(CHANNEL_ID, ids=message_id)
        await msg.edit(
            buttons=[
                [Button.inline(f"❤ {like_count}", data=f"like:{message_id}")]
            ]
        )
    except Exception as e:
        print("Edit button error:", e)

    await event.answer("Liked!", alert=False)



def main():
    print("Bot is running...")
    client.run_until_disconnected()


if __name__ == "__main__":
    main()