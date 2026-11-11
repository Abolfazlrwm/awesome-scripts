from __future__ import annotations

import asyncio
import ipaddress
import json
import re
import socket
from collections import defaultdict, deque
from dataclasses import asdict, dataclass
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import Any, Callable
from urllib.parse import urlparse

from telethon import Button, TelegramClient, events
from pytgcalls import PyTgCalls
from pytgcalls import filters as call_filters
from pytgcalls.exceptions import NoActiveGroupCall
from pytgcalls.types import GroupCallConfig, MediaStream, StreamEnded, VideoQuality


MAX_QUEUE_SIZE = 50
MAX_PLAYLIST_SIZE = 100
MAX_STREAMS = 50


@dataclass
class Track:
    source: str
    title: str
    kind: str = "audio"
    requester_id: int = 0


class MediaFeatures:
    def __init__(
        self,
        assistant: TelegramClient,
        panel: TelegramClient,
        calls: PyTgCalls,
        owner_id: int,
        admin_ids: set[int],
        state: dict[str, Any],
        save_state: Callable[[], None],
        download_dir: Path,
        main_menu_buttons: Callable[[], list[list[Button]]],
        joined_chats: set[int],
    ) -> None:
        self.assistant = assistant
        self.panel = panel
        self.calls = calls
        self.owner_id = owner_id
        self.admin_ids = admin_ids
        self.state = state
        self.save_state = save_state
        self.download_dir = download_dir
        self.main_menu_buttons = main_menu_buttons
        self.joined_chats = joined_chats
        self.queues: dict[int, deque[Track]] = defaultdict(deque)
        self.current: dict[int, Track] = {}
        self.play_locks: dict[int, asyncio.Lock] = {}
        self.panel_pending: dict[int, str] = {}
        self._ensure_state()

    def _ensure_state(self) -> None:
        self.state.setdefault("playback_admins", {})
        self.state.setdefault("playlists", {})
        self.state.setdefault("live_streams", {})
        self.state.setdefault("group_subscriptions", {})
        self.state.setdefault("subscriptions_required", False)
        self.state.setdefault("video_quality", {})

    @staticmethod
    def safe_config() -> GroupCallConfig:
        return GroupCallConfig(auto_start=False)

    def _lock(self, chat_id: int) -> asyncio.Lock:
        return self.play_locks.setdefault(chat_id, asyncio.Lock())

    def _group_key(self, chat_id: int) -> str:
        return str(chat_id)

    async def _is_operator(self, event: events.NewMessage.Event) -> bool:
        sender_id = int(event.sender_id or 0)
        if sender_id in self.admin_ids:
            return True
        stored = self.state["playback_admins"].get(self._group_key(int(event.chat_id)), [])
        if sender_id in {int(item) for item in stored}:
            return True
        try:
            permissions = await self.assistant.get_permissions(event.chat_id, sender_id)
            return bool(getattr(permissions, "is_admin", False) or getattr(permissions, "is_creator", False))
        except Exception:
            return False

    def _subscription_end(self, chat_id: int) -> datetime | None:
        raw = self.state["group_subscriptions"].get(self._group_key(chat_id))
        if not raw:
            return None
        try:
            parsed = datetime.fromisoformat(raw)
            return parsed if parsed.tzinfo else parsed.replace(tzinfo=timezone.utc)
        except ValueError:
            return None

    def _subscription_active(self, chat_id: int) -> bool:
        if not self.state.get("subscriptions_required"):
            return True
        end = self._subscription_end(chat_id)
        return bool(end and end > datetime.now(timezone.utc))

    async def _authorize(self, event: events.NewMessage.Event) -> bool:
        if not await self._is_operator(event):
            await event.reply("⛔ این دستور فقط برای مدیر گروه یا مدیر پخش است.")
            return False
        if int(event.sender_id or 0) not in self.admin_ids and not self._subscription_active(int(event.chat_id)):
            await event.reply("⌛ اعتبار پخش این گروه تمام شده است. با مدیر ربات تماس بگیرید.")
            return False
        return True

    @staticmethod
    async def _validate_public_url(value: str) -> str:
        parsed = urlparse(value)
        if parsed.scheme not in {"http", "https"} or not parsed.hostname:
            raise ValueError("لینک باید با http یا https شروع شود.")
        if parsed.username or parsed.password:
            raise ValueError("لینک دارای اطلاعات ورود پذیرفته نمی‌شود.")
        try:
            default_port = 443 if parsed.scheme == "https" else 80
            addresses = await asyncio.to_thread(socket.getaddrinfo, parsed.hostname, parsed.port or default_port)
        except socket.gaierror as exc:
            raise ValueError("دامنه لینک قابل شناسایی نیست.") from exc
        for address in addresses:
            ip = ipaddress.ip_address(address[4][0])
            if not ip.is_global:
                raise ValueError("لینک خصوصی یا داخلی پذیرفته نمی‌شود.")
        return value

    async def _youtube_search(self, query: str) -> tuple[str, str]:
        process = await asyncio.create_subprocess_exec(
            "yt-dlp",
            "--dump-single-json",
            "--no-playlist",
            f"ytsearch1:{query}",
            stdout=asyncio.subprocess.PIPE,
            stderr=asyncio.subprocess.PIPE,
        )
        try:
            stdout, stderr = await asyncio.wait_for(process.communicate(), timeout=30)
        except asyncio.TimeoutError:
            process.kill()
            raise ValueError("جست‌وجوی یوتیوب بیش از حد طول کشید.")
        if process.returncode or not stdout:
            detail = stderr.decode(errors="ignore").strip().splitlines()
            raise ValueError(detail[-1][:200] if detail else "نتیجه‌ای از یوتیوب پیدا نشد.")
        data = json.loads(stdout.decode())
        entries = data.get("entries") or [data]
        item = entries[0]
        link = item.get("webpage_url") or item.get("original_url")
        if not link:
            raise ValueError("لینک نتیجه یوتیوب پیدا نشد.")
        return str(link), str(item.get("title") or query)

    async def _track_from_event(self, event: events.NewMessage.Event, argument: str, kind: str) -> Track:
        reply = await event.get_reply_message() if event.is_reply else None
        if reply and reply.media:
            self.download_dir.mkdir(parents=True, exist_ok=True)
            source = await self.assistant.download_media(reply, file=str(self.download_dir))
            if not source:
                raise ValueError("دانلود فایل تلگرام انجام نشد.")
            file_name = getattr(getattr(reply, "file", None), "name", None)
            return Track(str(source), file_name or "فایل تلگرام", kind, int(event.sender_id or 0))
        argument = argument.strip()
        if not argument:
            raise ValueError("روی فایل ریپلای کنید یا لینک/نام آهنگ را بعد از دستور بنویسید.")
        if argument.startswith(("http://", "https://")):
            source = await self._validate_public_url(argument)
            return Track(source, argument[:100], kind, int(event.sender_id or 0))
        source, title = await self._youtube_search(argument)
        return Track(source, title, kind, int(event.sender_id or 0))

    def _stream_for(self, chat_id: int, track: Track) -> MediaStream:
        if track.kind == "audio":
            return MediaStream(track.source, video_flags=MediaStream.Flags.IGNORE)
        quality_name = self.state["video_quality"].get(self._group_key(chat_id), "720")
        qualities = {
            "360": VideoQuality.SD_360p,
            "480": VideoQuality.SD_480p,
            "720": VideoQuality.HD_720p,
            "1080": VideoQuality.FHD_1080p,
        }
        return MediaStream(track.source, video_parameters=qualities.get(quality_name, VideoQuality.HD_720p))

    async def _start_track(self, chat_id: int, track: Track) -> None:
        await self.calls.play(chat_id, self._stream_for(chat_id, track), config=self.safe_config())
        self.joined_chats.add(chat_id)
        self.current[chat_id] = track

    def clear_runtime(self, chat_id: int) -> None:
        self.current.pop(chat_id, None)
        self.queues.pop(chat_id, None)
        self.play_locks.pop(chat_id, None)

    async def _enqueue_or_play(self, chat_id: int, track: Track) -> tuple[str, int]:
        async with self._lock(chat_id):
            if chat_id in self.current:
                if len(self.queues[chat_id]) >= MAX_QUEUE_SIZE:
                    raise ValueError("صف پخش پر است.")
                self.queues[chat_id].append(track)
                return "queued", len(self.queues[chat_id])
            await self._start_track(chat_id, track)
            return "playing", 0

    async def _advance(self, chat_id: int) -> Track | None:
        async with self._lock(chat_id):
            self.current.pop(chat_id, None)
            if not self.queues[chat_id]:
                await self.calls.play(chat_id, None, config=self.safe_config())
                return None
            track = self.queues[chat_id].popleft()
            await self._start_track(chat_id, track)
            return track

    def _playlist(self, chat_id: int) -> list[dict[str, Any]]:
        return self.state["playlists"].setdefault(self._group_key(chat_id), [])

    async def _show_help(self, event: events.NewMessage.Event) -> None:
        await event.reply(
            "🎵 راهنمای پخش\n\n"
            "`پخش نام آهنگ` یا `/play name` — جست‌وجو و پخش صوت\n"
            "`پخش ویدئو لینک` یا `/vplay link` — پخش ویدئو\n"
            "روی فایل ریپلای کنید و `پخش` بفرستید\n"
            "`مکث`، `ادامه`، `توقف`، `بعدی`\n"
            "`بیصدا`، `باصدا`، `صدا 100`\n"
            "`کیفیت 360/480/720/1080`\n"
            "`صف`، `پاکسازی صف`\n"
            "`افزودن لیست`، `لیست پخش`، `اجرای لیست`، `حذف لیست 2`\n"
            "`شبکه‌ها`، `شبکه نام`\n"
            "`افزودن مدیر پخش` و `حذف مدیر پخش` روی پیام کاربر\n"
            "`مدیران پخش`، `اعتبار`"
        )

    async def _group_command(self, event: events.NewMessage.Event) -> None:
        if not event.is_group or not event.raw_text:
            return
        raw = event.raw_text.strip()
        first, _, argument = raw.partition(" ")
        command = first.lower().lstrip("/").split("@", 1)[0]
        aliases = {
            "راهنما": "help", "help": "help", "player": "help",
            "پخش": "play", "play": "play",
            "ویدئو": "vplay", "ویدیو": "vplay", "vplay": "vplay",
            "مکث": "pause", "pause": "pause",
            "ادامه": "resume", "ازسرگیری": "resume", "resume": "resume",
            "توقف": "stop", "stop": "stop",
            "بعدی": "skip", "skip": "skip",
            "بیصدا": "mute", "mute": "mute",
            "باصدا": "unmute", "unmute": "unmute",
            "صدا": "volume", "volume": "volume",
            "کیفیت": "quality", "quality": "quality",
            "صف": "queue", "queue": "queue",
            "پاکسازی_صف": "clearqueue", "clearqueue": "clearqueue",
            "افزودن_لیست": "playlistadd", "playlistadd": "playlistadd",
            "حذف_لیست": "playlistdel", "playlistdel": "playlistdel",
            "لیست_پخش": "playlist", "playlist": "playlist",
            "اجرای_لیست": "playlistplay", "playlistplay": "playlistplay",
            "شبکه": "streamplay", "stream": "streamplay",
            "شبکه‌ها": "streams", "streams": "streams",
            "افزودن_مدیر_پخش": "playeradminadd", "playeradminadd": "playeradminadd",
            "حذف_مدیر_پخش": "playeradmindel", "playeradmindel": "playeradmindel",
            "مدیران_پخش": "playeradmins", "playeradmins": "playeradmins",
            "اعتبار": "credit", "credit": "credit",
        }
        normalized = raw.replace("پخش ویدئو", "ویدئو", 1).replace("پخش ویدیو", "ویدیو", 1)
        normalized = normalized.replace("پاکسازی صف", "پاکسازی_صف", 1).replace("افزودن لیست", "افزودن_لیست", 1)
        normalized = normalized.replace("حذف لیست", "حذف_لیست", 1).replace("لیست پخش", "لیست_پخش", 1)
        normalized = normalized.replace("اجرای لیست", "اجرای_لیست", 1).replace("افزودن مدیر پخش", "افزودن_مدیر_پخش", 1)
        normalized = normalized.replace("حذف مدیر پخش", "حذف_مدیر_پخش", 1).replace("مدیران پخش", "مدیران_پخش", 1)
        first, _, argument = normalized.partition(" ")
        command = aliases.get(first.lower().lstrip("/").split("@", 1)[0], "")
        if not command:
            return
        if command == "help":
            await self._show_help(event)
            return
        if command == "credit":
            end = self._subscription_end(int(event.chat_id))
            if not self.state.get("subscriptions_required"):
                text = "نامحدود (سیستم اشتراک غیرفعال است)"
            elif not end:
                text = "فاقد اعتبار"
            else:
                remaining = (end - datetime.now(timezone.utc)).days
                text = "منقضی شده" if remaining < 0 else f"{remaining + 1} روز تا {end.date()}"
            await event.reply(f"⌛ اعتبار گروه: {text}")
            return
        if not await self._authorize(event):
            return
        chat_id = int(event.chat_id)
        try:
            if command in {"play", "vplay"}:
                track = await self._track_from_event(event, argument, "video" if command == "vplay" else "audio")
                status, position = await self._enqueue_or_play(chat_id, track)
                if status == "playing":
                    await event.reply(f"▶️ در حال پخش: {track.title}")
                else:
                    await event.reply(f"➕ به صف اضافه شد: {track.title}\nجایگاه: {position}")
            elif command == "pause":
                await self.calls.pause(chat_id); await event.reply("⏸ پخش متوقف شد.")
            elif command == "resume":
                await self.calls.resume(chat_id); await event.reply("▶️ پخش ادامه یافت.")
            elif command == "mute":
                await self.calls.mute(chat_id); await event.reply("🔇 صدا قطع شد.")
            elif command == "unmute":
                await self.calls.unmute(chat_id); await event.reply("🔊 صدا وصل شد.")
            elif command == "volume":
                volume = int(argument)
                if not 1 <= volume <= 200:
                    raise ValueError("مقدار صدا باید بین ۱ تا ۲۰۰ باشد.")
                await self.calls.change_volume_call(chat_id, volume)
                await event.reply(f"🔊 صدای پخش روی {volume} تنظیم شد.")
            elif command == "quality":
                quality = argument.strip()
                if quality not in {"360", "480", "720", "1080"}:
                    raise ValueError("کیفیت باید یکی از 360، 480، 720 یا 1080 باشد.")
                self.state["video_quality"][self._group_key(chat_id)] = quality
                self.save_state()
                await event.reply(f"🎞 کیفیت ویدئو روی {quality}p تنظیم شد.")
            elif command == "stop":
                async with self._lock(chat_id):
                    self.queues[chat_id].clear(); self.current.pop(chat_id, None)
                    await self.calls.play(chat_id, None, config=self.safe_config())
                await event.reply("⏹ پخش و صف متوقف شد؛ اکانت داخل ویس ماند.")
            elif command == "skip":
                track = await self._advance(chat_id)
                await event.reply(f"⏭ بعدی: {track.title}" if track else "⏹ صف تمام شد.")
            elif command == "queue":
                current = self.current.get(chat_id)
                lines = [f"▶️ {current.title}" if current else "چیزی در حال پخش نیست."]
                lines += [f"{index}. {item.title}" for index, item in enumerate(self.queues[chat_id], 1)]
                await event.reply("🎶 صف پخش\n\n" + "\n".join(lines[:30]))
            elif command == "clearqueue":
                self.queues[chat_id].clear(); await event.reply("🗑 صف انتظار پاک شد.")
            elif command == "playlistadd":
                track = await self._track_from_event(event, argument, "audio")
                playlist = self._playlist(chat_id)
                if len(playlist) >= MAX_PLAYLIST_SIZE:
                    raise ValueError("لیست پخش پر است.")
                playlist.append(asdict(track)); self.save_state()
                await event.reply(f"✅ به لیست پخش اضافه شد: {track.title}")
            elif command == "playlistdel":
                index = int(argument) - 1; playlist = self._playlist(chat_id)
                if index < 0 or index >= len(playlist):
                    raise ValueError("شماره آهنگ معتبر نیست.")
                removed = playlist.pop(index); self.save_state()
                await event.reply(f"🗑 حذف شد: {removed['title']}")
            elif command == "playlist":
                playlist = self._playlist(chat_id)
                lines = [f"{i}. {item['title']}" for i, item in enumerate(playlist, 1)]
                await event.reply("📃 لیست پخش\n\n" + ("\n".join(lines[:50]) or "لیست خالی است."))
            elif command == "playlistplay":
                playlist = [Track(**item) for item in self._playlist(chat_id)]
                if not playlist:
                    raise ValueError("لیست پخش خالی است.")
                first_track = playlist.pop(0)
                async with self._lock(chat_id):
                    if chat_id in self.current:
                        for item in [first_track] + playlist:
                            if len(self.queues[chat_id]) < MAX_QUEUE_SIZE:
                                self.queues[chat_id].append(item)
                        message = "✅ لیست به صف اضافه شد."
                    else:
                        self.queues[chat_id].extend(playlist[:MAX_QUEUE_SIZE])
                        await self._start_track(chat_id, first_track)
                        message = f"▶️ اجرای لیست شروع شد: {first_track.title}"
                await event.reply(message)
            elif command == "streams":
                streams = self.state["live_streams"]
                lines = [f"• {name} ({item['kind']})" for name, item in streams.items()]
                await event.reply("📡 شبکه‌های تعریف‌شده\n\n" + ("\n".join(lines) or "شبکه‌ای تعریف نشده است."))
            elif command == "streamplay":
                item = self.state["live_streams"].get(argument.strip())
                if not item: raise ValueError("این شبکه تعریف نشده است؛ دستور «شبکه‌ها» را بفرستید.")
                track = Track(item["url"], argument.strip(), item["kind"], int(event.sender_id or 0))
                status, position = await self._enqueue_or_play(chat_id, track)
                if status == "playing":
                    await event.reply(f"📡 پخش شبکه «{track.title}» شروع شد.")
                else:
                    await event.reply(f"➕ شبکه «{track.title}» به صف اضافه شد. جایگاه: {position}")
            elif command in {"playeradminadd", "playeradmindel"}:
                reply = await event.get_reply_message() if event.is_reply else None
                target_id = int(reply.sender_id) if reply and reply.sender_id else int(argument)
                admins = {int(item) for item in self.state["playback_admins"].get(self._group_key(chat_id), [])}
                if command == "playeradminadd": admins.add(target_id)
                else: admins.discard(target_id)
                self.state["playback_admins"][self._group_key(chat_id)] = sorted(admins); self.save_state()
                await event.reply("✅ فهرست مدیران پخش به‌روزرسانی شد.")
            elif command == "playeradmins":
                admins = self.state["playback_admins"].get(self._group_key(chat_id), [])
                await event.reply("👥 مدیران پخش\n\n" + ("\n".join(f"• `{x}`" for x in admins) or "فهرست خالی است."))
        except NoActiveGroupCall:
            await event.reply("⚠️ ویس‌چت فعالی وجود ندارد؛ تماس جدیدی ساخته نشد.")
        except (ValueError, TypeError) as exc:
            await event.reply(f"⚠️ {exc}")
        except Exception as exc:
            await event.reply(f"❌ عملیات ناموفق بود: `{type(exc).__name__}`")

    async def _stream_ended(self, _: PyTgCalls, update: StreamEnded) -> None:
        if update.chat_id not in self.current:
            return
        try:
            await self._advance(int(update.chat_id))
        except Exception:
            self.current.pop(int(update.chat_id), None)

    def panel_buttons(self) -> list[list[Button]]:
        required = "روشن ✅" if self.state.get("subscriptions_required") else "خاموش ❌"
        return [
            [Button.inline("➕ ثبت/تمدید اشتراک", b"media_sub_add")],
            [Button.inline("➖ لغو اشتراک", b"media_sub_del"), Button.inline("📋 لیست اشتراک‌ها", b"media_sub_list")],
            [Button.inline(f"🔐 الزام اشتراک: {required}", b"media_sub_toggle")],
            [Button.inline("📺 افزودن شبکه", b"media_stream_add"), Button.inline("🗑 حذف شبکه", b"media_stream_del")],
            [Button.inline("📡 لیست شبکه‌ها", b"media_stream_list")],
            [Button.inline("📖 راهنمای فرمان‌ها", b"media_help")],
            [Button.inline("🔙 بازگشت", b"main")],
        ]

    async def _panel_callback(self, event: events.CallbackQuery.Event) -> None:
        if int(event.sender_id or 0) not in self.admin_ids:
            await event.answer("دسترسی ندارید.", alert=True); return
        action = event.data.decode()
        await event.answer()
        if action == "media_menu":
            await event.edit("🎵 مدیریت پخش، شبکه‌ها و اشتراک گروه‌ها", buttons=self.panel_buttons()); return
        if action == "media_help":
            await event.edit(
                "📖 فرمان‌های اصلی گروه\n\n"
                "`پخش نام آهنگ`، `ویدئو لینک`، `مکث`، `ادامه`، `توقف`، `بعدی`، `صف`، `صدا 100`، "
                "`لیست پخش`، `شبکه‌ها`، `شبکه نام` و `اعتبار`\n\nبرای راهنمای کامل در گروه «راهنما» بفرستید.",
                buttons=[[Button.inline("🔙 بازگشت", b"media_menu")]],
            ); return
        if action == "media_sub_toggle":
            if int(event.sender_id) != self.owner_id:
                await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=self.panel_buttons()); return
            self.state["subscriptions_required"] = not bool(self.state.get("subscriptions_required")); self.save_state()
            await event.edit("✅ وضعیت الزام اشتراک تغییر کرد.", buttons=self.panel_buttons()); return
        prompts = {
            "media_sub_add": "آیدی گروه و تعداد روز را بفرستید. مثال:\n`-1001234567890 30`",
            "media_sub_del": "آیدی عددی گروه را بفرستید.",
            "media_stream_add": "اطلاعات شبکه را این‌طور بفرستید:\n`نام | audio یا video | لینک مستقیم`",
            "media_stream_del": "نام دقیق شبکه را بفرستید.",
        }
        if action in prompts:
            if int(event.sender_id) != self.owner_id:
                await event.edit("⛔ فقط مالک اصلی دسترسی دارد.", buttons=self.panel_buttons()); return
            self.panel_pending[int(event.sender_id)] = action
            await event.edit(prompts[action], buttons=[[Button.inline("❌ انصراف", b"media_menu")]]); return
        if action == "media_sub_list":
            lines = [f"• `{chat}` تا {end[:10]}" for chat, end in self.state["group_subscriptions"].items()]
            await event.edit("📋 اشتراک گروه‌ها\n\n" + ("\n".join(lines) or "اشتراکی ثبت نشده است."), buttons=self.panel_buttons()); return
        if action == "media_stream_list":
            lines = [f"• {name} ({item['kind']})" for name, item in self.state["live_streams"].items()]
            await event.edit("📡 شبکه‌ها\n\n" + ("\n".join(lines) or "شبکه‌ای ثبت نشده است."), buttons=self.panel_buttons())

    async def _panel_input(self, event: events.NewMessage.Event) -> None:
        if not event.is_private or int(event.sender_id or 0) != self.owner_id:
            return
        action = self.panel_pending.pop(int(event.sender_id), None)
        if not action:
            return
        try:
            raw = event.raw_text.strip()
            if action == "media_sub_add":
                chat_raw, days_raw = raw.split(maxsplit=1); chat_id, days = int(chat_raw), int(days_raw)
                if chat_id >= 0 or not 1 <= days <= 3660: raise ValueError("آیدی یا تعداد روز معتبر نیست.")
                current = self._subscription_end(chat_id); base = max(current or datetime.now(timezone.utc), datetime.now(timezone.utc))
                end = base + timedelta(days=days)
                self.state["group_subscriptions"][str(chat_id)] = end.isoformat(); self.save_state()
                message = f"✅ اشتراک گروه `{chat_id}` تا {end.date()} فعال شد."
            elif action == "media_sub_del":
                chat_id = int(raw); self.state["group_subscriptions"].pop(str(chat_id), None); self.save_state()
                message = f"✅ اشتراک گروه `{chat_id}` لغو شد."
            elif action == "media_stream_add":
                name, kind, url = [part.strip() for part in raw.split("|", 2)]
                if not name or len(name) > 40 or kind not in {"audio", "video"}: raise ValueError("فرمت نام یا نوع شبکه درست نیست.")
                if len(self.state["live_streams"]) >= MAX_STREAMS and name not in self.state["live_streams"]: raise ValueError("فهرست شبکه‌ها پر است.")
                await self._validate_public_url(url)
                self.state["live_streams"][name] = {"kind": kind, "url": url}; self.save_state()
                message = f"✅ شبکه «{name}» ثبت شد."
            else:
                name = raw; self.state["live_streams"].pop(name, None); self.save_state(); message = f"✅ شبکه «{name}» حذف شد."
            await event.respond(message, buttons=self.panel_buttons())
        except (ValueError, TypeError) as exc:
            await event.respond(f"⚠️ {exc}", buttons=self.panel_buttons())

    def register(self) -> None:
        self.assistant.add_event_handler(self._group_command, events.NewMessage())
        self.panel.add_event_handler(self._panel_callback, events.CallbackQuery(pattern=rb"^media_"))
        self.panel.add_event_handler(self._panel_input, events.NewMessage(incoming=True))
        self.calls.add_handler(self._stream_ended, call_filters.stream_end(StreamEnded.Type.AUDIO))
