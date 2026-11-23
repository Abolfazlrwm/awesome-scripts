# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import re
import os
import hashlib
import time
from functools import lru_cache
from config import BANNED_WORDS, LINK_PATTERNS
class MessageFilters:
    def __init__(self, db):
        self.db = db
        self.link_patterns = [re.compile(pattern) for pattern in LINK_PATTERNS]
        self.banned_words = set(BANNED_WORDS)
        self.profanity_cache = {}
        self.profanity_cache_ttl = 3600
        self.profanity_cache_max_size = 5000
        self._preprocess_banned_words()
        self.nsfw_patterns = [
            r'porn', r'xxx', r'sexy', r'hot', r'adult', r'18\+', r'sex',
            r'پورن', r'سکس', r'سکسی', r'لخت', r'برهنه', r'سوپر', r'داغ',
            r'nude', r'naked', r'boob', r'breast', r'pussy', r'dick', r'cock',
            r'ass', r'anal', r'cum', r'orgasm', r'masturbat',
            r'سینه', r'ممه', r'کس', r'کیر', r'کون', r'جنده', r'فاحشه',
            r'hentai', r'bdsm', r'fetish', r'blowjob', r'handjob', r'gangbang',
            r'orgy', r'threesome', r'foursome', r'creampie', r'bukkake',
            r'جلق', r'لز', r'لزبین', r'گی', r'همجنس', r'لواط', r'دوجنسه',
            r'کیرکلفت', r'ساک', r'فیلم سوپر', r'عکس سکسی', r'فیلم سکسی'
        ]
        self.nsfw_regex = re.compile('|'.join(self.nsfw_patterns), re.IGNORECASE)
        self.suspicious_extensions = ['.mp4', '.avi', '.mov', '.wmv', '.gif', '.jpg', '.jpeg', '.png', '.webm', '.webp']
        self.known_nsfw_hashes = set()
        self.suspicious_file_id_prefixes = ['CgAD', 'CAACAgEA']
        self.suspicious_mime_types = ['video/mp4', 'video/quicktime', 'image/gif']
        self.cheating_patterns = [
            r'join', r'channel', r'group', r'invite', r'contest', r'giveaway', r'prize', r'win',
            r'عضو شو', r'عضو شوید', r'جایزه', r'کانال', r'گروه', r'دعوت', r'مسابقه', r'برنده',
            r'جوین', r'بپیوندید', r'بپیوند', r'پیوستن', r'قرعه کشی', r'برنده شو', r'هدیه',
            r'رایگان', r'free', r'vip'
        ]
        self.cheating_regex = re.compile('|'.join(self.cheating_patterns), re.IGNORECASE)
        self.external_mention_patterns = [
            r'@\w+', r't\.me/\w+', r'telegram\.me/\w+', r'https?://t\.me/\w+'
        ]
        self.external_mention_regex = re.compile('|'.join(self.external_mention_patterns))
    def _clean_text_for_profanity(self, text):
        """پاک‌سازی متن برای بررسی کلمات فحش"""
        if not text:
            return ""
        text_lower = text.lower()
        cleaned_text = re.sub(r'[^\w\s]', '', text_lower)
        cleaned_text = re.sub(r'\s+', '', cleaned_text)
        return cleaned_text
    def _preprocess_banned_words(self):
        """پیش‌پردازش کلمات فحش برای جستجوی سریع‌تر"""
        self.cleaned_banned_words = set()
        for word in self.banned_words:
            cleaned = self._clean_text_for_profanity(word)
            if cleaned:
                self.cleaned_banned_words.add(cleaned)
        if self.cleaned_banned_words:
            pattern = '|'.join(re.escape(word) for word in self.cleaned_banned_words)
            self.profanity_regex = re.compile(pattern)
        else:
            self.profanity_regex = None
    def _generate_cache_key(self, text):
        """تولید کلید کش برای متن"""
        return hashlib.md5(text.encode('utf-8')).hexdigest()
    def _clean_cache(self):
        """پاک‌سازی کش قدیمی"""
        current_time = time.time()
        expired_keys = [k for k, v in self.profanity_cache.items() if current_time - v[1] > self.profanity_cache_ttl]
        for key in expired_keys:
            del self.profanity_cache[key]
        if len(self.profanity_cache) > self.profanity_cache_max_size:
            sorted_items = sorted(self.profanity_cache.items(), key=lambda x: x[1][1])
            for key, _ in sorted_items[:len(self.profanity_cache) - self.profanity_cache_max_size]:
                del self.profanity_cache[key]
    @lru_cache(maxsize=1000)
    def _clean_text_for_profanity_cached(self, text):
        """نسخه کش شده از تابع تمیز کردن متن"""
        return self._clean_text_for_profanity(text)
    def contains_profanity(self, text):
        """Check if the text contains any banned words"""
        if not text:
            return False
        cleaned_text = self._clean_text_for_profanity_cached(text)
        if not cleaned_text:
            return False
        cache_key = self._generate_cache_key(cleaned_text)
        if cache_key in self.profanity_cache:
            result, _ = self.profanity_cache[cache_key]
            return result
        self._clean_cache()
        result = False
        if self.profanity_regex and self.profanity_regex.search(cleaned_text):
            result = True
        current_time = time.time()
        self.profanity_cache[cache_key] = (result, current_time)
        return result
    def is_forward(self, message):
        """Check if the message is forwarded"""
        return message.forward_date is not None
    def check_file_id_for_nsfw(self, file_id):
        """Check if the file ID suggests NSFW content"""
        if not file_id:
            return False
        for prefix in self.suspicious_file_id_prefixes:
            if file_id.startswith(prefix):
                return True
        return False
    def might_contain_nsfw(self, message):
        """Check if the message might contain NSFW content"""
        has_media = (
            message.photo or 
            message.video or 
            message.animation or
            message.document or
            message.sticker
        )
        if not has_media:
            return False
        if message.caption:
            if self.nsfw_regex.search(message.caption):
                return True
        if message.document:
            if self.check_file_id_for_nsfw(message.document.file_id):
                return True
            filename = message.document.file_name
            if filename:
                _, ext = os.path.splitext(filename.lower())
                if ext in self.suspicious_extensions:
                    if self.nsfw_regex.search(filename):
                        return True
                    if message.document.file_size > 5 * 1024 * 1024:
                        return True
            if hasattr(message.document, 'mime_type') and message.document.mime_type:
                if message.document.mime_type in self.suspicious_mime_types:
                    return True
        if message.video:
            if self.check_file_id_for_nsfw(message.video.file_id):
                return True
            if message.video.duration > 20:
                return True
            if message.video.width and message.video.height:
                aspect_ratio = message.video.width / message.video.height
                if aspect_ratio > 1.8 or aspect_ratio < 0.5:
                    return True
            if message.video.file_size and message.video.file_size > 8 * 1024 * 1024:
                return True
            if hasattr(message.video, 'mime_type') and message.video.mime_type:
                if message.video.mime_type in self.suspicious_mime_types:
                    return True
        if message.animation:
            if self.check_file_id_for_nsfw(message.animation.file_id):
                return True
            if message.animation.file_name and self.nsfw_regex.search(message.animation.file_name):
                return True
            if message.animation.width and message.animation.height:
                aspect_ratio = message.animation.width / message.animation.height
                if aspect_ratio > 1.8 or aspect_ratio < 0.5:
                    return True
                if (message.animation.width >= 300 and message.animation.height >= 300) and \
                   (message.animation.width <= 800 and message.animation.height <= 800):
                    if message.animation.duration and message.animation.duration > 5:
                        return True
                    if message.animation.file_size and message.animation.file_size > 2 * 1024 * 1024:
                        return True
            if hasattr(message.animation, 'mime_type') and message.animation.mime_type:
                if 'video' in message.animation.mime_type or message.animation.mime_type in self.suspicious_mime_types:
                    return True
            if message.animation.file_size:
                if 500 * 1024 <= message.animation.file_size <= 4 * 1024 * 1024:
                    if message.animation.duration and message.animation.duration >= 3:
                        return True
            if message.animation.duration:
                if 3 <= message.animation.duration <= 15:
                    return True
        if message.photo and len(message.photo) > 0:
            photo = message.photo[-1]
            if self.check_file_id_for_nsfw(photo.file_id):
                return True
            if photo.width and photo.height:
                aspect_ratio = photo.width / photo.height
                if aspect_ratio > 1.8 or aspect_ratio < 0.5:
                    return True
            if photo.file_size and photo.file_size > 1 * 1024 * 1024:
                return True
        if message.sticker:
            if self.check_file_id_for_nsfw(message.sticker.file_id):
                return True
            if message.sticker.emoji:
                nsfw_emojis = ['🍑', '🍆', '👅', '💦', '🔞', '😈', '😏', '🥵', '🤤', '👙', '🩲', '🩱']
                if message.sticker.emoji in nsfw_emojis:
                    return True
            if message.sticker.set_name and self.nsfw_regex.search(message.sticker.set_name):
                return True
        return False
    def is_spam(self, message, chat_id, user_id):
        """Check if a message is spam"""
        return self.db.track_message_for_spam(chat_id, user_id)
    def might_be_cheating(self, message, chat_id, user_id):
        """Check if a message might be cheating (advertising external groups)"""
        if not message.text and not message.caption:
            return False
        text = message.text or message.caption
        has_suspicious_link = False
        has_external_mention = False
        external_mentions = self.external_mention_regex.findall(text)
        if external_mentions:
            has_external_mention = True
            if self.cheating_regex.search(text):
                self.db.track_cheating_activity(chat_id, user_id, False, True)
                return True
        if self.contains_link(text) and self.cheating_regex.search(text):
            has_suspicious_link = True
            self.db.track_cheating_activity(chat_id, user_id, True, False)
            return True
        if has_suspicious_link or has_external_mention:
            is_cheating = self.db.track_cheating_activity(chat_id, user_id, 
                                                         has_suspicious_link, 
                                                         has_external_mention)
            return is_cheating
        return False
    def should_delete_message(self, message, chat_id):
        """Check if a message should be deleted based on group settings"""
        settings = self.db.get_group_settings(chat_id)
        if not settings:
            return False
        text = message.text or message.caption or ""
        try:
            user_id = message.from_user.id
            if self.db.is_user_admin(user_id):
                return False
        except:
            pass
        try:
            member_count_key = f"member_count:{chat_id}"
            current_time = time.time()
            if member_count_key not in self.profanity_cache or \
               (current_time - self.profanity_cache[member_count_key][1]) > 3600:
                try:
                    from telegram.error import BadRequest
                    try:
                        member_count = message.bot.get_chat_member_count(chat_id)
                    except BadRequest:
                        member_count = 500
                    except AttributeError:
                        try:
                            member_count = message.chat.get_member_count()
                        except:
                            member_count = 500
                    self.profanity_cache[member_count_key] = (member_count, current_time)
                except:
                    self.profanity_cache[member_count_key] = (500, current_time)
            member_count, _ = self.profanity_cache[member_count_key]
            is_large_group = member_count > 200
        except:
            is_large_group = False
        if settings["antilink_enabled"] and self.contains_link(text):
            return True
        if settings["antiprofanity_enabled"] and text:
            if is_large_group:
                profanity_key = f"profanity:{self._generate_cache_key(text)}"
                if profanity_key in self.profanity_cache:
                    result, _ = self.profanity_cache[profanity_key]
                    if result:
                        return True
                else:
                    has_profanity = self.contains_profanity(text)
                    self.profanity_cache[profanity_key] = (has_profanity, current_time)
                    if has_profanity:
                        return True
            else:
                if self.contains_profanity(text):
                    return True
        if settings["antiforward_enabled"] and self.is_forward(message):
            return True
        if settings["antiporn_enabled"] and self.might_contain_nsfw(message):
            return True
        if settings["antispam_enabled"] and self.is_spam(message, chat_id, user_id):
            return True
        if settings["anticheating_enabled"] and self.might_be_cheating(message, chat_id, user_id):
            return True
        return False
    def get_violation_reason(self, message, chat_id):
        """Get the reason why a message violates the rules"""
        settings = self.db.get_group_settings(chat_id)
        if not settings:
            return None
        user_id = message.from_user.id
        if self.db.is_user_admin(user_id):
            return None
        if settings.get("antispam_enabled", False) and self.is_spam(message, chat_id, user_id):
            return "ارسال پیام‌های مکرر (اسپم) در گروه ممنوع است"
        if settings.get("anticheating_enabled", False) and self.might_be_cheating(message, chat_id, user_id):
            return "تبلیغ کانال‌ها و گروه‌های دیگر در گروه ممنوع است"
        if message.text or message.caption:
            text = message.text or message.caption
            if settings["antilink_enabled"] and self.contains_link(text):
                return "ارسال لینک در گروه ممنوع است"
            if settings["antiprofanity_enabled"] and self.contains_profanity(text):
                return "استفاده از کلمات نامناسب در گروه ممنوع است"
        if settings["antiforward_enabled"] and self.is_forward(message):
            return "فوروارد پیام در گروه ممنوع است"
        if settings.get("antiporn_enabled", False) and self.might_contain_nsfw(message):
            content_type = "محتوای نامناسب"
            if message.photo:
                content_type = "تصویر نامناسب"
            elif message.video:
                content_type = "ویدیو نامناسب"
            elif message.animation:
                content_type = "گیف نامناسب"
            elif message.document:
                content_type = "فایل نامناسب"
            elif message.sticker:
                content_type = "استیکر نامناسب"
            return f"ارسال {content_type} (پورن) در گروه ممنوع است"
        return None
    @lru_cache(maxsize=500)
    def contains_link_cached(self, text):
        """نسخه کش شده از بررسی لینک"""
        if not text:
            return False
        for pattern in self.link_patterns:
            if pattern.search(text):
                return True
        return False
    def contains_link(self, text):
        """Check if the text contains any links"""
        return self.contains_link_cached(text) 