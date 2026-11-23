# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
# referral_listener.py

import asyncio
import logging
from telethon import events, Button
from telethon.errors import UserNotParticipantError, ChannelPrivateError, FloodWaitError
from telethon.tl.functions.channels import GetParticipantRequest
from coins_db import coin_db

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("referal")  # Match your logger name from the logs

class ReferralListener:
    """
    Standalone listener: users start with /start=<code> or /start <code>.
    Enforces forced-join of two channels, records the referral,
    awards coins, then suppresses the normal /start handler.
    """
    def __init__(self, bot,
                 required_channels=None,
                 reward_referrer: int = 10,
                 reward_referee: int = 5,
                 debug_mode: bool = False):
        self.bot = bot
        self.debug_mode = debug_mode
        self.channels = required_channels or [
            -1002493252312,  # @telebotcraft
            -1002672760963   # @telebotcraftchat
        ]
        # Map channel IDs to usernames for better user feedback
        self.channel_names = {
            -1002493252312: "@telebotcraft",
            -1002672760963: "@telebotcraftback"
        }
        self.reward_referrer = reward_referrer
        self.reward_referee = reward_referee
        self.pending = {}  # user_id -> referrer_id
        
        # Add verification retry counter
        self.verification_attempts = {}  # user_id -> attempt count

        # Ensure our two tables exist
        asyncio.get_event_loop().create_task(self._ensure_tables())

        # 1) Catch referral deep‐links
        bot.add_event_handler(
            self._on_start_referral,
            events.NewMessage(pattern=r"^/start[ =]?([A-Za-z0-9_-]{6,32})$")
        )
        # 2) Catch the "Confirm Membership" button
        bot.add_event_handler(
            self._on_confirm,
            events.CallbackQuery(data=b"ref_confirm")
        )
        # 3) Add debug button handler if enabled
        if debug_mode:
            bot.add_event_handler(
                self._on_debug_info,
                events.CallbackQuery(data=b"ref_debug")
            )

    async def _ensure_tables(self):
        pool = coin_db.pool
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                # map each user to their unique code
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS referral_codes (
                        user_id BIGINT UNSIGNED PRIMARY KEY,
                        code    VARCHAR(32)     NOT NULL UNIQUE
                    ) ENGINE=InnoDB CHARSET=utf8mb4;
                """)
                # record actual referrals
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS referrals (
                        user_id     BIGINT UNSIGNED PRIMARY KEY,
                        referrer_id BIGINT UNSIGNED NOT NULL,
                        timestamp   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB CHARSET=utf8mb4;
                """)
                await conn.commit()
        logger.info("Database tables verified")

    async def _is_member(self, user_id: int, channel_id: int) -> tuple:
        """
        Fixed method that correctly checks channel membership using GetParticipantRequest
        instead of the non-existent get_participant method
        """
        try:
            # Get the channel entity first
            channel = await self.bot.get_entity(channel_id)
            
            # Use the proper method to check membership
            try:
                participant = await self.bot(GetParticipantRequest(
                    channel=channel,
                    participant=user_id
                ))
                return True, None
            except UserNotParticipantError:
                return False, "NotMember"
                
        except ChannelPrivateError:
            return False, "ChannelPrivate"
        except FloodWaitError as e:
            return False, f"FloodWait:{e.seconds}"
        except Exception as e:
            logger.error(f"Membership check error: {type(e).__name__}: {str(e)}")
            return False, f"Error:{type(e).__name__}"

    async def _on_start_referral(self, event):
        user_id = event.sender_id
        code = event.pattern_match.group(1)
        logger.info(f"Referral start: User {user_id} with code {code}")

        # 1) Lookup who owns this code
        pool = coin_db.pool
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT user_id FROM referral_codes WHERE code=%s",
                    (code,)
                )
                row = await cur.fetchone()
                if not row:
                    await event.reply("❌ کد معرفی نامعتبر است.")
                    return  # let the normal /start run
                referrer_id = row[0]

        # 2) Greet
        await event.reply("👋 در حال ثبت معرفی شما… لطفاً صبر کنید.")

        # 3) Prevent self‑referral
        if referrer_id == user_id:
            await event.reply("❌ نمی‌توانید خودتان را معرفی کنید.")
            return

        # 4) Already referred?
        async with pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT 1 FROM referrals WHERE user_id=%s",
                    (user_id,)
                )
                if await cur.fetchone():
                    await event.reply("🔔 شما قبلاً معرفی شده‌اید.")
                    return

        # 5) Force‑join check with more detailed feedback
        missing_channels = []
        error_details = {}
        
        for ch in self.channels:
            is_member, error = await self._is_member(user_id, ch)
            if not is_member:
                missing_channels.append(ch)
                error_details[ch] = error
        
        if missing_channels:
            # Reset verification attempts counter
            self.verification_attempts[user_id] = 0
            
            text = "برای ثبت معرفی ابتدا در کانال‌های رسمی ما عضو شوید:"
            buttons = [
                [Button.url("📢 کانال ما", "https://t.me/telebotcraft")],
                [Button.url("💬 گروه پشتیبانی", "https://t.me/telebotcraftchat")],
                [Button.inline("✅ تأیید عضویت", b"ref_confirm")]
            ]
            
            # Add debug button if debug mode is enabled
            if self.debug_mode:
                buttons.append([Button.inline("🔍 اطلاعات عیب‌یابی", b"ref_debug")])
            
            await event.reply(text, buttons=buttons)
            self.pending[user_id] = referrer_id
            logger.info(f"User {user_id} needs to join channels: {missing_channels}")
            # *** stop further /start handling here ***
            raise events.StopPropagation

        # 6) All set → record & award
        await self._record_and_award(user_id, referrer_id)
        # *** stop the default /start welcome ***
        raise events.StopPropagation

    async def _on_confirm(self, event):
        user_id = event.sender_id
        if user_id not in self.pending:
            await event.answer("❌ درخواست نامعتبر. لطفاً دوباره شروع کنید.", alert=True)
            return

        referrer_id = self.pending[user_id]
        
        # Update verification attempt counter
        if user_id in self.verification_attempts:
            self.verification_attempts[user_id] += 1
        else:
            self.verification_attempts[user_id] = 1
        
        attempt = self.verification_attempts[user_id]
        logger.info(f"Verification attempt #{attempt} for user {user_id}")

        # Re‑check forced‑join with improved feedback
        missing_channels = []
        error_details = {}
        
        for ch in self.channels:
            is_member, error = await self._is_member(user_id, ch)
            if not is_member:
                missing_channels.append(ch)
                error_details[ch] = error
        
        if missing_channels:
            # Prepare detailed feedback
            channel_names = [
                self.channel_names.get(ch, str(ch)) for ch in missing_channels
            ]
            
            # Add specific error information
            error_msg = f"❌ هنوز عضو کانال‌های {', '.join(channel_names)} نیستید."
            
            # Add more helpful tips on repeated attempts
            if attempt >= 2:
                error_msg += "\n\n💡 نکات عضویت:"
                error_msg += "\n• حتماً با همین اکانت در کانال‌ها عضو شوید"
                error_msg += "\n• بعد از عضویت کمی صبر کنید (۱۰-۲۰ ثانیه)"
                error_msg += "\n• اگر قبلاً عضو بودید، خارج شده و دوباره عضو شوید"
                error_msg += "\n• مطمئن شوید محدودیت یا بلاک از طرف تلگرام ندارید"
            
            await event.answer(error_msg, alert=True)
            return

        # Success! Record & award
        await self._record_and_award(user_id, referrer_id)
        self.pending.pop(user_id)
        self.verification_attempts.pop(user_id, None)
        await event.answer("✅ معرفی با موفقیت ثبت شد!", alert=True)
        await event.edit(
            "✅ عضویت شما تأیید شد و معرفی با موفقیت ثبت شد!",
            buttons=None  # Remove buttons
        )
        # *** prevent any other callback from firing ***
        raise events.StopPropagation

    async def _on_debug_info(self, event):
        """Debug handler to provide technical information"""
        user_id = event.sender_id
        if user_id not in self.pending:
            await event.answer("No pending referral found", alert=True)
            return
        
        debug_info = ["🔍 اطلاعات عیب‌یابی:"]
        
        # Check each channel and report status
        for ch in self.channels:
            is_member, error = await self._is_member(user_id, ch)
            channel_name = self.channel_names.get(ch, str(ch))
            status = "✅ عضو هستید" if is_member else f"❌ عضو نیستید ({error})"
            debug_info.append(f"{channel_name}: {status}")
        
        # Add attempt counter
        attempts = self.verification_attempts.get(user_id, 0)
        debug_info.append(f"دفعات تلاش: {attempts}")
        
        await event.answer("\n".join(debug_info), alert=True)

    async def _record_and_award(self, user_id: int, referrer_id: int):
        pool = coin_db.pool
        try:
            async with pool.acquire() as conn:
                async with conn.cursor() as cur:
                    # Check if already recorded to prevent duplicate entries
                    await cur.execute(
                        "SELECT 1 FROM referrals WHERE user_id=%s",
                        (user_id,)
                    )
                    if await cur.fetchone():
                        logger.warning(f"Prevented duplicate referral for user {user_id}")
                        return
                    
                    # Record the referral
                    await cur.execute(
                        "INSERT INTO referrals (user_id, referrer_id) VALUES (%s, %s)",
                        (user_id, referrer_id)
                    )
                    await conn.commit()
            
            logger.info(f"Recorded referral: user {user_id} by referrer {referrer_id}")

            # Give coins & notify
            try:
                await coin_db.add_coins(referrer_id, self.reward_referrer)
                await coin_db.add_coins(user_id, self.reward_referee)
                
                # Notify referrer
                await self.bot.send_message(
                    referrer_id,
                    f"🎉 کاربر `{user_id}` را معرفی کردید و "
                    f"{self.reward_referrer} سکه دریافت کردید!"
                )
                
                # Notify new user
                await self.bot.send_message(
                    user_id,
                    f"🎉 خوش آمدید! {self.reward_referee} سکه پاداش معرفی برای شما واریز شد."
                )
                logger.info(f"Coins awarded: {self.reward_referrer} to referrer and {self.reward_referee} to referee")
            except Exception as e:
                logger.error(f"Error in coin award: {type(e).__name__}: {str(e)}")
                
        except Exception as e:
            logger.error(f"Error recording referral: {type(e).__name__}: {str(e)}")