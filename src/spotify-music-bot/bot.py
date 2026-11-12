# Telegram: @Abolfazl_rwm
import os
import re
import sys
import time
import json
import sqlite3
import logging
from typing import Dict, Any, Optional

import telebot
from telebot import types

import spotify_parser
import search_engine
import downloader

# Configure logging
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    level=logging.INFO
)
logger = logging.getLogger('SpotifyDownloaderBot')

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CONFIG_FILE = os.path.join(BASE_DIR, 'config.json')
DB_FILE = os.path.join(BASE_DIR, 'bot_data.db')

def load_config() -> Dict[str, Any]:
    if os.path.exists(CONFIG_FILE):
        try:
            with open(CONFIG_FILE, 'r', encoding='utf-8') as f:
                return json.load(f)
        except Exception:
            pass
    return {
        "bot_token": "",
        "admin_ids": [6750615582],
        "channel_username": "",
        "download_quality": "320kbps"
    }

def save_config(cfg: Dict[str, Any]):
    with open(CONFIG_FILE, 'w', encoding='utf-8') as f:
        json.dump(cfg, f, ensure_ascii=False, indent=2)

config = load_config()
BOT_TOKEN = config.get("bot_token", "").strip()

# Initialize DB
def init_db():
    conn = sqlite3.connect(DB_FILE)
    c = conn.cursor()
    c.execute('''
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY,
            username TEXT,
            first_name TEXT,
            downloads_count INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    c.execute('''
        CREATE TABLE IF NOT EXISTS downloads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            title TEXT,
            artist TEXT,
            source_type TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    conn.commit()
    conn.close()

init_db()

def record_user(user_id: int, username: str, first_name: str):
    try:
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute('''
            INSERT INTO users (user_id, username, first_name, downloads_count)
            VALUES (?, ?, ?, 0)
            ON CONFLICT(user_id) DO UPDATE SET
            username = excluded.username,
            first_name = excluded.first_name
        ''', (user_id, username, first_name))
        conn.commit()
        conn.close()
    except Exception as e:
        logger.error(f"DB Error record_user: {e}")

def increment_download(user_id: int, title: str, artist: str, source_type: str):
    try:
        conn = sqlite3.connect(DB_FILE)
        c = conn.cursor()
        c.execute('UPDATE users SET downloads_count = downloads_count + 1 WHERE user_id = ?', (user_id,))
        c.execute('INSERT INTO downloads (user_id, title, artist, source_type) VALUES (?, ?, ?, ?)',
                  (user_id, title, artist, source_type))
        conn.commit()
        conn.close()
    except Exception as e:
        logger.error(f"DB Error increment_download: {e}")

# In-memory search cache to map short callback data
# callback_data max 64 bytes in Telegram API!
SEARCH_CACHE: Dict[str, Dict[str, Any]] = {}

def create_bot(token: str) -> telebot.TeleBot:
    from telebot import apihelper
    try:
        apihelper.API_URL = "http://127.0.0.1:8081/bot{0}/{1}"
        apihelper.FILE_URL = "http://127.0.0.1:8081/file/bot{0}/{1}"
    except Exception:
        pass

    bot = telebot.TeleBot(token, parse_mode='HTML')

    @bot.message_handler(content_types=['new_chat_members'])
    def on_new_chat_members(message: types.Message):
        if message.chat.type in ('group', 'supergroup'):
            try:
                bot.leave_chat(message.chat.id)
            except Exception:
                pass

    @bot.message_handler(commands=['start', 'help'], chat_types=['private'])
    def send_welcome(message: types.Message):
        if message.chat.type != 'private':
            return
        try:
            record_user(message.from_user.id, message.from_user.username, message.from_user.first_name)
            text = (
                "🎧 <b>به ربات حرفه‌ای اسپاتیفای و موزیک دانلودر خوش آمدید!</b>\n\n"
                "با این ربات به راحتی می‌تونید هر آهنگی رو با <b>کیفیت اصلی ۳۲۰</b>، کاور اورجینال و مشخصات کامل دانلود کنید:\n\n"
                "🔹 <b>روش اول (ارسال لینک اسپاتیفای):</b>\n"
                "کافیه لینک ترک، آلبوم یا پلی‌لیست اسپاتیفای رو کپی کنی و برام بفرستی.\n\n"
                "🔹 <b>روش دوم (جستجوی متنی):</b>\n"
                "فقط اسم آهنگ، نام خواننده یا بخشی از متن شعر رو بنویس و بفرست (مثلاً: <code>شادمهر تقدیر</code> یا <code>Billie Eilish</code>).\n\n"
                "🚀 <i>آماده‌ام! همین حالا لینک یا اسم موزیک دلخواهت رو برام بفرست:</i>"
            )
            markup = types.InlineKeyboardMarkup(row_width=1)
            markup.add(
                types.InlineKeyboardButton("💡 راهنمای کار با ربات", callback_data="btn_help"),
                types.InlineKeyboardButton("📊 آمار دانلود من", callback_data="btn_stats")
            )
            bot.reply_to(message, text, reply_markup=markup)
        except Exception as e:
            logger.error(f"Error in send_welcome: {e}")

    @bot.message_handler(func=lambda m: True, chat_types=['private'])
    def handle_text(message: types.Message):
        if message.chat.type != 'private':
            return
        text = (message.text or '').strip()
        if not text:
            return

        record_user(message.from_user.id, message.from_user.username, message.from_user.first_name)

        # Check if text contains Spotify URL
        if 'spotify.com' in text or 'spotify.link' in text:
            parsed = spotify_parser.parse_spotify_url(text)
            if not parsed:
                bot.reply_to(message, "❌ لینک اسپاتیفای نامعتبر است. لطفاً لینک معتبر ترک، آلبوم یا پلی‌لیست ارسال کنید.")
                return

            req_type = parsed['type']
            req_id = parsed['id']

            if req_type == 'track':
                process_spotify_track(bot, message, req_id)
            elif req_type in ('album', 'playlist'):
                process_spotify_album(bot, message, req_id, is_playlist=(req_type == 'playlist'))
            return

        # Otherwise, treat as search query
        process_search_query(bot, message, text)

    def process_spotify_track(bot: telebot.TeleBot, message: types.Message, track_id: str):
        status_msg = bot.reply_to(message, "🔍 <i>در حال استعلام مشخصات آهنگ از اسپاتیفای...</i>")
        try:
            track_info = spotify_parser.get_spotify_track_info(track_id)
            if not track_info:
                bot.edit_message_text("❌ متأسفانه اطلاعات این ترک از اسپاتیفای دریافت نشد.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            bot.edit_message_text(
                f"📥 <b>در حال دانلود با بالاترین کیفیت:</b>\n"
                f"🎵 <b>{track_info['title']}</b>\n"
                f"👤 <b>{track_info['artist']}</b>\n\n"
                f"⏳ <i>لطفاً چند لحظه شکیبا باشید...</i>",
                chat_id=status_msg.chat.id,
                message_id=status_msg.message_id
            )

            res = downloader.download_spotify_track(track_id)
            if not res or not os.path.exists(res['mp3_path']):
                bot.edit_message_text("❌ خطا در دانلود یا تبدیل فایل صوتی. لطفاً مجدداً تلاش کنید.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            bot.edit_message_text("📤 <i>در حال ارسال فایل صوتی به تلگرام...</i>",
                                  chat_id=status_msg.chat.id, message_id=status_msg.message_id)

            caption = (
                f"🎵 <b>{res['title']}</b>\n"
                f"👤 خواننده: <b>{res['artist']}</b>\n"
                f"💽 کیفیت: <b>320kbps (Original Audio)</b>\n"
                f"🔗 <a href=\"{track_info['url']}\">مشاهده در اسپاتیفای</a>\n\n"
                f"🎧 @Abolfazl_rwm"
            )

            thumb_file = None
            if res.get('cover_path') and os.path.exists(res['cover_path']):
                thumb_file = open(res['cover_path'], 'rb')

            with open(res['mp3_path'], 'rb') as audio_file:
                bot.send_audio(
                    chat_id=message.chat.id,
                    audio=audio_file,
                    caption=caption,
                    title=res['title'],
                    performer=res['artist'],
                    duration=res['duration'],
                    thumb=thumb_file,
                    reply_to_message_id=message.message_id
                )

            if thumb_file:
                thumb_file.close()

            # Increment count
            increment_download(message.from_user.id, res['title'], res['artist'], 'spotify_track')

            # Delete progress status message
            try:
                bot.delete_message(chat_id=status_msg.chat.id, message_id=status_msg.message_id)
            except Exception:
                pass

            # Cleanup temp files
            cleanup_files(res['mp3_path'], res.get('cover_path'))

        except Exception as e:
            logger.error(f"Error process_spotify_track: {e}")
            bot.edit_message_text(f"❌ خطا در پردازش درخواست: {e}",
                                  chat_id=status_msg.chat.id, message_id=status_msg.message_id)

    def process_spotify_album(bot: telebot.TeleBot, message: types.Message, album_id: str, is_playlist: bool):
        type_name = "پلی‌لیست" if is_playlist else "آلبوم"
        status_msg = bot.reply_to(message, f"🔍 <i>در حال دریافت مشخصات {type_name} از اسپاتیفای...</i>")
        try:
            album_info = spotify_parser.get_spotify_album_info(album_id, is_playlist=is_playlist)
            if not album_info or not album_info.get('tracks'):
                bot.edit_message_text(f"❌ اطلاعات این {type_name} دریافت نشد.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            tracks = album_info['tracks']
            total = len(tracks)
            if total == 0:
                bot.edit_message_text(f"❌ هیچ ترکی در این {type_name} یافت نشد.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            album_title = album_info.get('title', 'Unknown')
            album_subtitle = album_info.get('subtitle', '')
            album_cover = album_info.get('cover_url')

            header_text = (
                f"💽 <b>{type_name}:</b> {album_title}\n"
                f"👤 <b>هنرمند / سازنده:</b> {album_subtitle}\n"
                f"🔢 <b>تعداد ترک‌ها:</b> {total} قطعه\n\n"
                f"🚀 <i>دانلود خودکار تمامی آهنگ‌ها آغاز شد و پشت سر هم ارسال می‌شوند...</i>"
            )

            # Send album cover and header without any inline buttons
            if album_cover:
                try:
                    bot.send_photo(message.chat.id, photo=album_cover, caption=header_text, reply_to_message_id=message.message_id)
                except Exception:
                    bot.send_message(message.chat.id, header_text, reply_to_message_id=message.message_id)
            else:
                bot.send_message(message.chat.id, header_text, reply_to_message_id=message.message_id)

            try:
                bot.delete_message(chat_id=status_msg.chat.id, message_id=status_msg.message_id)
            except Exception:
                pass

            import threading
            threading.Thread(
                target=_download_and_send_all_tracks,
                args=(bot, message, tracks, album_title, album_subtitle, album_cover, type_name),
                daemon=True
            ).start()

        except Exception as e:
            logger.error(f"Error process_spotify_album: {e}")
            try:
                bot.edit_message_text(f"❌ خطا در پردازش {type_name}: {e}",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
            except Exception:
                pass

    def _download_and_send_all_tracks(bot: telebot.TeleBot, message: types.Message, tracks: list, album_title: str, album_subtitle: str, album_cover: Optional[str], type_name: str):
        total = len(tracks)
        prog_msg = None
        try:
            prog_msg = bot.send_message(
                message.chat.id,
                f"⚡️ <b>در حال دانلود پرسرعت {total} آهنگ...</b>\n<i>دانلود چندنخی همزمان فعال شد. آهنگ‌ها به ترتیب ارسال می‌شوند.</i>"
            )
        except Exception:
            pass

        successful = 0
        failed = 0

        from concurrent.futures import ThreadPoolExecutor

        def _worker(tr_item):
            t_title = tr_item.get('title', 'Unknown')
            t_artist = tr_item.get('artist', '') or album_subtitle
            try:
                return downloader.download_track_by_metadata(
                    title=t_title,
                    artist=t_artist,
                    album=album_title,
                    thumb_url=album_cover
                )
            except Exception as err:
                logger.error(f"Error in parallel download for {t_title}: {err}")
                return None

        # Pre-fetch downloads concurrently (3 tracks in parallel)
        with ThreadPoolExecutor(max_workers=3) as executor:
            futures = [
                (idx, tr, executor.submit(_worker, tr))
                for idx, tr in enumerate(tracks, start=1)
            ]

            for idx, tr, future in futures:
                tr_title = tr.get('title', 'Unknown')
                tr_artist = tr.get('artist', '') or album_subtitle

                if prog_msg and (idx == 1 or idx % 3 == 0 or idx == total):
                    try:
                        bot.edit_message_text(
                            f"⚡️ <b>در حال ارسال ({idx}/{total}):</b>\n"
                            f"🎵 <b>{tr_title}</b>\n"
                            f"👤 <b>{tr_artist}</b>\n\n"
                            f"🚀 <i>دانلود موازی در پس‌زمینه فعال است...</i>",
                            chat_id=prog_msg.chat.id,
                            message_id=prog_msg.message_id
                        )
                    except Exception:
                        pass

                res = None
                try:
                    res = future.result(timeout=150)
                except Exception as ex:
                    logger.error(f"Error awaiting future for {tr_title}: {ex}")
                    res = None

                if not res or not os.path.exists(res.get('mp3_path', '')):
                    failed += 1
                    logger.warning(f"Failed to download track {idx}/{total}: {tr_title} - {tr_artist}")
                    continue

                caption = (
                    f"🎵 <b>{res['title']}</b>\n"
                    f"👤 خواننده: <b>{res['artist']}</b>\n"
                    f"💽 {type_name}: <b>{album_title}</b>\n"
                    f"🔢 قطعه <b>{idx}</b> از <b>{total}</b>\n\n"
                    f"🎧 @Abolfazl_rwm"
                )

                thumb_file = None
                if res.get('cover_path') and os.path.exists(res['cover_path']):
                    try:
                        thumb_file = open(res['cover_path'], 'rb')
                    except Exception:
                        thumb_file = None

                try:
                    with open(res['mp3_path'], 'rb') as audio_file:
                        bot.send_audio(
                            chat_id=message.chat.id,
                            audio=audio_file,
                            caption=caption,
                            title=res['title'],
                            performer=res['artist'],
                            duration=res.get('duration', 0),
                            thumb=thumb_file
                        )
                    successful += 1
                    increment_download(message.from_user.id, res['title'], res['artist'], 'spotify_album')
                except Exception as send_err:
                    logger.error(f"Error sending audio {tr_title}: {send_err}")
                    failed += 1
                finally:
                    if thumb_file:
                        thumb_file.close()
                    cleanup_files(res['mp3_path'], res.get('cover_path'))

                time.sleep(0.5)

        if prog_msg:
            try:
                summary = (
                    f"✅ <b>پایان دانلود تمام آهنگ‌های {type_name}!</b>\n\n"
                    f"🎉 <b>{successful}</b> آهنگ با سرعت بالا دانلود و ارسال شد."
                )
                if failed > 0:
                    summary += f"\n⚠️ <i>{failed} آهنگ به دلیل عدم دسترسی دانلود نشد.</i>"
                bot.edit_message_text(summary, chat_id=prog_msg.chat.id, message_id=prog_msg.message_id)
            except Exception:
                pass

    def process_search_query(bot: telebot.TeleBot, message: types.Message, query: str):
        status_msg = bot.reply_to(message, f"🔎 <i>در حال جستجوی موزیک برای «{query}»...</i>")
        try:
            results = search_engine.search_songs(query, limit=6)
            if not results:
                bot.edit_message_text(f"❌ آهنگی با عبارت «{query}» پیدا نشد. لطفاً اسم خواننده یا آهنگ را به شکل دیگری بنویسید.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            text = f"🎯 <b>نتایج جستجو برای:</b> <i>{query}</i>\n\n"
            markup = types.InlineKeyboardMarkup(row_width=1)

            for idx, s in enumerate(results, start=1):
                dur_str = f" [{s['duration']}]" if s.get('duration') else ""
                text += f"<b>{idx}.</b> 🎵 <b>{s['title']}</b>\n"
                text += f"     👤 {s['artist']}{dur_str}\n\n"

                cache_key = f"yt_{s['video_id']}"
                SEARCH_CACHE[cache_key] = s

                btn_text = f"📥 دانلود {idx}: {s['title'][:24]} - {s['artist'][:16]}"
                markup.add(types.InlineKeyboardButton(btn_text, callback_data=f"dl_yt:{s['video_id']}"))

            bot.edit_message_text(text, chat_id=status_msg.chat.id, message_id=status_msg.message_id, reply_markup=markup)

        except Exception as e:
            logger.error(f"Error process_search_query: {e}")
            bot.edit_message_text(f"❌ خطا در جستجو: {e}",
                                  chat_id=status_msg.chat.id, message_id=status_msg.message_id)

    @bot.callback_query_handler(func=lambda call: call.data in ('btn_help', 'btn_stats'))
    def handle_static_callbacks(call: types.CallbackQuery):
        if call.message and getattr(call.message.chat, 'type', 'private') != 'private':
            try:
                bot.answer_callback_query(call.id, "این ربات فقط در چت خصوصی (پی‌وی) فعالیت می‌کند.", show_alert=True)
            except Exception:
                pass
            return
        if call.data == 'btn_help':
            help_text = (
                "📖 <b>راهنمای استفاده از ربات:</b>\n\n"
                "۱. <b>دانلود با لینک اسپاتیفای:</b>\n"
                "وارد اپلیکیشن اسپاتیفای شوید، روی گزینه اشتراک‌گذاری (Share) موزیک بزنید و لینک را کپی کنید. سپس لینک را داخل همین ربات بفرستید.\n\n"
                "۲. <b>جستجوی هوشمند متنی:</b>\n"
                "فقط کافیست نام آهنگ و خواننده را تایپ کنید:\n"
                "مثال: <code>شادمهر عقیلی تقدیر</code> یا <code>Adele Someone Like You</code>\n\n"
                "✨ <b>تمامی فایل‌ها با بالاترین کیفیت ۳۲۰ و کاور رسمی ارسال می‌شوند.</b>"
            )
            bot.answer_callback_query(call.id)
            bot.send_message(call.message.chat.id, help_text)
        elif call.data == 'btn_stats':
            conn = sqlite3.connect(DB_FILE)
            c = conn.cursor()
            c.execute('SELECT downloads_count FROM users WHERE user_id = ?', (call.from_user.id,))
            row = c.fetchone()
            count = row[0] if row else 0
            conn.close()
            bot.answer_callback_query(call.id)
            bot.send_message(call.message.chat.id, f"📊 <b>آمار دانلود شما:</b>\nشما تا کنون <b>{count}</b> آهنگ با این ربات دانلود کرده‌اید! 🎵")

    @bot.callback_query_handler(func=lambda call: call.data.startswith(('dl_yt:', 'dl_sp:')))
    def handle_download_callback(call: types.CallbackQuery):
        if call.message and getattr(call.message.chat, 'type', 'private') != 'private':
            try:
                bot.answer_callback_query(call.id, "برای دانلود لطفاً به چت خصوصی (پی‌وی) ربات مراجعه کنید.", show_alert=True)
            except Exception:
                pass
            return
        bot.answer_callback_query(call.id, "درخواست دریافت شد، در حال آماده‌سازی دانلود...")

        parts = call.data.split(':', 1)
        prefix = parts[0]
        item_id = parts[1]

        status_msg = bot.send_message(call.message.chat.id, "📥 <i>در حال دانلود با بهترین کیفیت ۳۲۰...</i>")

        try:
            if prefix == 'dl_sp':
                # Spotify track ID
                res = downloader.download_spotify_track(item_id)
            else:
                # YouTube video ID
                cache_key = f"yt_{item_id}"
                cached = SEARCH_CACHE.get(cache_key)
                if cached:
                    res = downloader.download_song_by_video_id(
                        video_id=item_id,
                        title=cached['title'],
                        artist=cached['artist'],
                        album=cached.get('album', ''),
                        thumb_url=cached.get('thumb_url')
                    )
                else:
                    # Fetch from ytmusic directly
                    res = downloader.download_song_by_video_id(
                        video_id=item_id,
                        title="Unknown Title",
                        artist="Unknown Artist",
                        album=""
                    )

            if not res or not os.path.exists(res['mp3_path']):
                bot.edit_message_text("❌ متأسفانه دریافت فایل آهنگ ناموفق بود.",
                                      chat_id=status_msg.chat.id, message_id=status_msg.message_id)
                return

            bot.edit_message_text("📤 <i>در حال آپلود آهنگ در تلگرام...</i>",
                                  chat_id=status_msg.chat.id, message_id=status_msg.message_id)

            caption = (
                f"🎵 <b>{res['title']}</b>\n"
                f"👤 <b>{res['artist']}</b>\n"
                f"💽 کیفیت: <b>320kbps (Original Audio)</b>\n\n"
                f"🎧 @Abolfazl_rwm"
            )

            thumb_file = None
            if res.get('cover_path') and os.path.exists(res['cover_path']):
                thumb_file = open(res['cover_path'], 'rb')

            with open(res['mp3_path'], 'rb') as audio_file:
                bot.send_audio(
                    chat_id=call.message.chat.id,
                    audio=audio_file,
                    caption=caption,
                    title=res['title'],
                    performer=res['artist'],
                    duration=res['duration'],
                    thumb=thumb_file
                )

            if thumb_file:
                thumb_file.close()

            increment_download(call.from_user.id, res['title'], res['artist'], prefix)

            try:
                bot.delete_message(chat_id=status_msg.chat.id, message_id=status_msg.message_id)
            except Exception:
                pass

            cleanup_files(res['mp3_path'], res.get('cover_path'))

        except Exception as e:
            logger.error(f"Error handle_download_callback: {e}")
            bot.edit_message_text(f"❌ خطا: {e}",
                                  chat_id=status_msg.chat.id, message_id=status_msg.message_id)

    # Inline query handler (search inside any chat!)
    @bot.inline_handler(func=lambda query: len(query.query.strip()) > 1)
    def handle_inline_search(inline_query: types.InlineQuery):
        q = inline_query.query.strip()
        try:
            results = search_engine.search_songs(q, limit=10)
            inline_results = []
            for s in results:
                cache_key = f"yt_{s['video_id']}"
                SEARCH_CACHE[cache_key] = s

                dur_str = f" • {s['duration']}" if s.get('duration') else ""
                r = types.InlineQueryResultArticle(
                    id=s['video_id'],
                    title=s['title'],
                    description=f"{s['artist']}{dur_str}",
                    thumbnail_url=s.get('thumb_url'),
                    input_message_content=types.InputTextMessageContent(
                        f"🎵 <b>{s['title']}</b>\n👤 خواننده: <b>{s['artist']}</b>\n\n<i>برای دانلود دکمه زیر را لمس کنید:</i>",
                        parse_mode='HTML'
                    ),
                    reply_markup=types.InlineKeyboardMarkup().add(
                        types.InlineKeyboardButton("📥 دانلود این آهنگ (۳۲۰)", callback_data=f"dl_yt:{s['video_id']}")
                    )
                )
                inline_results.append(r)

            bot.answer_inline_query(inline_query.id, inline_results, cache_time=300)
        except Exception as e:
            logger.error(f"Error inline_query: {e}")

    return bot

def cleanup_files(mp3_path: str, cover_path: Optional[str]):
    try:
        if mp3_path and os.path.exists(mp3_path):
            os.remove(mp3_path)
        if cover_path and os.path.exists(cover_path):
            os.remove(cover_path)
    except Exception as e:
        logger.error(f"Error cleaning files: {e}")

if __name__ == '__main__':
    if not BOT_TOKEN:
        print("BOT_TOKEN is not set in config.json! Please provide a bot token.")
        sys.exit(1)
    
    print(f"Starting Spotify Downloader Bot...")
    bot = create_bot(BOT_TOKEN)
    bot.infinity_polling(timeout=30, long_polling_timeout=20)
