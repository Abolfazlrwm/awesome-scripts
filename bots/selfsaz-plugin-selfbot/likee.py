# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import asyncio
import logging
from telethon import events
from telethon.tl.types import (
    UpdateMessageReactions,
    PeerChannel,
    PeerUser,
    MessagePeerReaction,
    ReactionEmoji
)
from telethon.tl.functions.messages import GetMessageReactionsListRequest
from coins_db import coin_db

logger = logging.getLogger(__name__)

class LikeTracker:
    """
    Tracks user reactions (likes) on messages within a specific channel.
    Awards 1 coin per unique like per message, no repeats.
    Uses both `recent_reactions` raw data and fallback to API fetch.
    """
    def __init__(self, bot, channel_id: int = -1002493252312, reward: int = 1):
        self.bot = bot
        self.channel_id = abs(channel_id)
        self.reward = reward
        self.db_pool = coin_db.pool

        # Ensure the like_log table exists
        asyncio.get_event_loop().create_task(self._ensure_table())

        # Listen for all raw updates
        bot.add_event_handler(self._on_raw_update, events.Raw)

    async def _ensure_table(self):
        async with self.db_pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute("""
                    CREATE TABLE IF NOT EXISTS like_logs (
                        msg_id BIGINT NOT NULL,
                        user_id BIGINT NOT NULL,
                        PRIMARY KEY (msg_id, user_id)
                    ) ENGINE=InnoDB CHARSET=utf8mb4;
                """)
                await conn.commit()

    async def _on_raw_update(self, update):
        # Only care about reaction updates
        if not isinstance(update, UpdateMessageReactions):
            return

        peer = update.peer
        # Only track our target channel
        if not (isinstance(peer, PeerChannel) and peer.channel_id == self.channel_id):
            return

        msg_id = update.msg_id
        reactors = set()

        # 1) Try raw recent_reactions if present
        rr = getattr(update.reactions, 'recent_reactions', None)
        if rr:
            logger.debug(f"[LikeTracker] raw recent_reactions for msg {msg_id}: {rr}")
            for rec in rr:
                user_peer = getattr(rec, 'peer_id', None)
                if isinstance(user_peer, PeerUser):
                    reactors.add(user_peer.user_id)

        # 2) Fallback: for each emoji, fetch up to 100 reactors
        if not reactors:
            for rc in update.reactions.results:
                reaction = rc.reaction
                try:
                    resp = await self.bot(GetMessageReactionsListRequest(
                        peer=peer,
                        msg_id=msg_id,
                        reaction=reaction,
                        offset=0,
                        limit=100,
                        hash=0
                    ))
                except Exception as e:
                    logger.error(f"[LikeTracker] error fetching reactors for msg {msg_id}, emoji {reaction}: {e}")
                    continue

                for pr in getattr(resp, 'reactions', []):
                    # pr is a MessagePeerReaction
                    user_peer = getattr(pr, 'peer_id', None)
                    if isinstance(user_peer, PeerUser):
                        reactors.add(user_peer.user_id)

        # Award coins for each new reactor
        for user_id in reactors:
            if await self._log_and_award(msg_id, user_id):
                await self._notify_user(user_id, msg_id)

    async def _log_and_award(self, msg_id: int, user_id: int) -> bool:
        """
        Returns True if this (msg_id, user_id) was first-time,
        logs it and awards a coin. False otherwise.
        """
        async with self.db_pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(
                    "SELECT 1 FROM like_logs WHERE msg_id=%s AND user_id=%s",
                    (msg_id, user_id)
                )
                if await cur.fetchone():
                    return False

                # Log the like
                await cur.execute(
                    "INSERT INTO like_logs (msg_id, user_id) VALUES (%s, %s)",
                    (msg_id, user_id)
                )
                await conn.commit()

        try:
            await coin_db.add_coins(user_id, self.reward)
            logger.info(f"[LikeTracker] awarded {self.reward} coin to {user_id} for msg {msg_id}")
            return True
        except Exception as e:
            logger.error(f"[LikeTracker] failed to award coin to {user_id}: {e}")
            return False

    async def _notify_user(self, user_id: int, msg_id: int):
        """Send DM notifying the user about their earned coin."""
        try:
            await self.bot.send_message(
                user_id,
                f"✅ شما پیام #{msg_id} را لایک کردید و {self.reward} سکه دریافت کردید!"
            )
        except Exception as e:
            logger.error(f"[LikeTracker] failed to notify user {user_id}: {e}")
