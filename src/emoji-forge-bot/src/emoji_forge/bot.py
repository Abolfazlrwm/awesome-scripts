"""Telegram bot: Persian RTL menus, polling, job queue for long work.

Flow (honest limits are stated to the user at /start):
- a single custom emoji gives custom_emoji_id -> getCustomEmojiStickers ->
  file_id + optional set_name. Without set_name, only that emoji can be
  fetched; the user is asked for a pack link or direct files instead.
- a bot can only publish a NEW set it creates; it cannot edit someone
  else's pack. /publish_new therefore creates a new set, never overwrites.
"""
import html
import io
import json
import os
import re
import sys
import threading
import time
import traceback

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from PIL import Image

from emoji_forge.access import (
    OWNER_ID, QUOTA_PRESETS, UserRegistry, access_status_message, tehran_today,
)
from emoji_forge.brand import BrandProfile, validate_hex
from emoji_forge.branding import (
    BOT_BRAND, apply_branding, help_html, help_text, pending_welcome_text,
    publish_title, report_caption, welcome_html, welcome_text, zip_caption,
)
from emoji_forge.premium import DANGER, PRIMARY, SUCCESS, btn, pe
from emoji_forge.brain import get_brain
from emoji_forge.cleaning import PRESETS, Region
from emoji_forge.config import load_config, load_dotenv
from emoji_forge.errors import AccessError, ExternalError
from emoji_forge.export import write_report_files
from emoji_forge.formats import describe
from emoji_forge.pack import (
    extract_custom_emoji_ids, manifest_from_sticker_set, parse_pack_link,
)
from emoji_forge.pipeline import export_project, process_project, sample_indices
from emoji_forge.queue import JobQueue
from emoji_forge.security import redact, safe_filename, validate_upload
from emoji_forge.selfcheck import readiness_line
from emoji_forge.settings import get_settings, set_setting
from emoji_forge.progress import ProgressReporter
from emoji_forge.singles import (
    SINGLES_SET_TITLE, SET_NAME_ERROR_MARKERS, build_color_keyboard,
    build_font_keyboard, build_gradient_keyboard, flatten_palette,
    font_label, load_fonts, load_gradients, load_palette,
    single_action_keyboard, single_set_candidates,
)
from emoji_forge.storage import (
    new_project_id, ordered_name, project_dir, read_json, write_json,
)
from emoji_forge.telegram_api import TelegramApi

# v9: the bot's own identity texts come from branding.py (Abolfazl /
# Abolfazl • @Abolfazl_rwm). User outputs keep the USER's brand — see branding.py.
START_TEXT = welcome_text()

HELP_TEXT = help_text()

# HTML variants (Bot API 9.4 premium emojis in the headers) — sent
# ONLY for /start and /help with parse_mode="HTML"; START_TEXT /
# HELP_TEXT stay the plain versions everywhere else.
START_HTML = welcome_html()

HELP_HTML = help_html()


PALETTE = [
    ("#0F2A1D", "سبز تیره"), ("#2D6A4F", "سبز"), ("#74C69D", "سبز روشن"),
    ("#D8F3DC", "کرمی روشن"), ("#000000", "مشکی"), ("#FFFFFF", "سفید"),
    ("#C9A227", "طلایی"), ("#1D4ED8", "آبی"), ("#B91C1C", "قرمز"),
]


def _palette_keyboard(kind: str):
    rows = []
    row = []
    for hex_code, label in PALETTE:
        row.append(btn(f"{label} {hex_code}", callback_data=f"{kind}:{hex_code}"))
        if len(row) == 3:
            rows.append(row)
            row = []
    if row:
        rows.append(row)
    rows.append([btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")])
    return {"inline_keyboard": rows}


def main_menu(is_admin: bool = False):
    """Clean main menu: only the daily actions. Brand and advanced
    settings live behind one button each, so the menu is not cluttered.
    The owner additionally gets the 👥 کاربران admin row (v9).
    Visual layer (Bot API 9.4, restrained): exactly ONE PRIMARY
    button (پردازش کامل — the single main CTA) and one DANGER
    (لغو); everything else is the default style. A button carries
    ONE visual only: a premium icon where the semantics match
    exactly (btn() strips the label's own emoji then), otherwise
    the label's plain emoji and no icon."""
    rows = [
        [btn("👁 پیش‌نمایش", callback_data="cmd:preview", icon="preview"),
         btn("✅ پردازش کامل", callback_data="cmd:process", style=PRIMARY)],
        [btn("🎨 رنگ و برند من", callback_data="menu:brand", icon="palette"),
         btn("✏️ ویرایش ایموجی تکی", callback_data="single:start", icon="edit")],
        [btn("📍 ناحیه لوگوی قدیمی", callback_data="menu:region"),
         btn("🔤 لایه‌های TGS", callback_data="menu:tgs")],
        [btn("📦 خروجی ZIP", callback_data="cmd:export"),
         btn("🚀 انتشار پک جدید", callback_data="cmd:publish", icon="publish")],
        [btn("⚙️ تنظیمات پیشرفته", callback_data="menu:advanced", icon="settings"),
         btn("📊 وضعیت", callback_data="cmd:status", icon="stats")],
        [btn("📦 پک‌های من", callback_data="packs:mine", icon="pack"),
         btn("❌ لغو", callback_data="cmd:cancel", style=DANGER, icon="cross")],
    ]
    if is_admin:
        rows.append([btn("👥 کاربران (مدیریت دسترسی)", callback_data="admin:users", icon="users"),
                     btn("📊 آمار ربات", callback_data="admin:stats", icon="stats")])
    return {"inline_keyboard": rows}


def admin_menu():
    """Owner-only admin home (v9 access control)."""
    return {"inline_keyboard": [
        [btn("👥 فهرست کاربران", callback_data="admin:users", icon="users")],
        [btn("🏷 اعتبار ربات در خروجی کاربران", callback_data="admin:credit")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


def admin_user_keyboard(uid: int):
    """Per-user admin actions: status changes + quota presets (v9)."""
    return {"inline_keyboard": [
        [btn("✅ تأیید", callback_data=f"admin:approve:{uid}", style=SUCCESS, icon="check"),
         btn("❌ رد", callback_data=f"admin:reject:{uid}", style=DANGER, icon="cross")],
        [btn("⛔ مسدود", callback_data=f"admin:block:{uid}", style=DANGER),
         btn("🔓 رفع مسدودیت", callback_data=f"admin:unblock:{uid}", style=SUCCESS)],
        [btn("سهمیه پیش‌فرض (۳ پک)", callback_data=f"admin:qpreset:{uid}:default"),
         btn("سهمیه پلاس (۱۰ پک)", callback_data=f"admin:qpreset:{uid}:plus")],
        [btn("سهمیه پرو (۵۰ پک)", callback_data=f"admin:qpreset:{uid}:pro"),
         btn("سهمیه نامحدود", callback_data=f"admin:qpreset:{uid}:unlimited")],
        [btn("✏️ سهمیه دلخواه (عدد)", callback_data=f"admin:qcustom:{uid}", icon="edit")],
        [btn("👥 بازگشت به فهرست", callback_data="admin:users", icon="users")],
    ]}


def brand_menu():
    return {"inline_keyboard": [
        [btn("🎨 رنگ من", callback_data="menu:color", icon="palette"),
         btn("🌈 رنگ دوم", callback_data="menu:color2", icon="palette")],
        [btn("🎨 رنگ دلخواه", callback_data="color:prompt"),
         btn("🌈 طیف دلخواه", callback_data="spectrum:prompt")],
        [btn("🌈 طیف رنگی", callback_data="menu:spectrum"),
         btn("🌈 گرادیان آماده", callback_data="menu:gradients")],
        [btn("✏️ نام برند", callback_data="name:prompt"),
         btn("🖼 لوگو", callback_data="logo:prompt")],
        [btn("💧 واترمارک", callback_data="watermark:prompt")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


def advanced_menu(sess):
    recolor = "روشن" if sess.brand.full_recolor else "خاموش"
    review = "روشن" if sess.review_by_mati else "خاموش"
    font_label = {"bold_sans": "ضخیم بدون‌سریف", "serif": "سریف",
                  "mono": "تک‌عرض"}.get(sess.brand.font_style, sess.brand.font_style)
    size_label = "فیت دقیق داخل قاب" if sess.brand.logo_size_mode == "fit" else "پرکردن قاب (قدیمی)"
    return {"inline_keyboard": [
        [btn("🔥 خام‌سازی و بازسازی", callback_data="mode:strip"),
         btn("🚀 v8 — لوگوی برداری دقیق", callback_data="mode:v8", style=PRIMARY)],
        [btn("🎨 برندینگ", callback_data="mode:branding", icon="palette"),
         btn("🧹 فقط پاک‌سازی", callback_data="mode:clean")],
        [btn("🖼 حفظ ظاهر اصلی", callback_data="mode:preserve")],
        [btn(f"🎨 بازسازی رنگی کامل: {recolor}", callback_data="recolor:toggle", icon="palette")],
        [btn(f"🔎 پردازش پیشرفته توسط ربات: {review}", callback_data="review:toggle")],
        [btn(f"🔤 فونت متن رستری: {font_label}", callback_data="font:cycle"),
         btn(f"📐 اندازه لوگو (TGS): {size_label}", callback_data="logosize:toggle")],
        [btn("📌 محل لوگو", callback_data="menu:pos"),
         btn("🔍 اندازه لوگو", callback_data="menu:size")],
        [btn("📖 راهنما", callback_data="cmd:help")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


def _region_keyboard():
    return {"inline_keyboard": [
        [btn("⬆️ بالا", callback_data="region:top"),
         btn("⬇️ پایین", callback_data="region:bottom"),
         btn("⏺ وسط", callback_data="region:center")],
        [btn("↖️ بالا چپ", callback_data="region:top-left"),
         btn("↗️ بالا راست", callback_data="region:top-right")],
        [btn("↙️ پایین چپ", callback_data="region:bottom-left"),
         btn("↘️ پایین راست", callback_data="region:bottom-right")],
        [btn("🚫 بدون ناحیه (فقط لبه خودکار)", callback_data="region:none")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


def _pos_keyboard():
    return {"inline_keyboard": [
        [btn("⬆️ بالا", callback_data="pos:top"),
         btn("⬇️ پایین", callback_data="pos:bottom"),
         btn("⏺ وسط", callback_data="pos:center")],
        [btn("↖️ بالا چپ", callback_data="pos:top-left"),
         btn("↗️ بالا راست", callback_data="pos:top-right")],
        [btn("↙️ پایین چپ", callback_data="pos:bottom-left"),
         btn("↘️ پایین راست", callback_data="pos:bottom-right")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


def _size_keyboard():
    return {"inline_keyboard": [
        [btn("۱۵٪", callback_data="size:15"),
         btn("۲۵٪", callback_data="size:25"),
         btn("۳۵٪", callback_data="size:35"),
         btn("۵۰٪", callback_data="size:50")],
        [btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")],
    ]}


class Session:
    def __init__(self, data_dir: str):
        self.data_dir = data_dir
        self.user_id = None
        self.project_id = ""
        self.manifest = None
        self.brand = BrandProfile()
        self.region = None
        self.mode = "strip"  # preserve | clean | redesign | branding | strip (default: strip & rebuild)
        self.output_kind = "emoji"
        self.preview_approved = False
        self.awaiting = ""  # brand_logo | ...
        self.logo_data = None
        self.ownership_confirmed = False
        self.pending_manifest = None
        self.tgs_hide = set()
        self.last_job_id = ""
        self.review_by_mati = True  # default: advanced bot processing on full runs
        self.review_pending = False
        # Single-emoji editing (v10): the pending source emoji, the
        # chosen action, a logo sent just for this flow, and the
        # persisted per-user singles preferences (font + set name +
        # Mini App logo rect; loaded/saved with the profile).
        self.single = None  # {"data": bytes, "emoji": str, "file_name": str}
        self.single_action = ""
        self.single_logo_data = None
        self.font_id = ""
        self.single_set_name = ""
        self.logo_rect = None

    def ensure_project(self):
        if not self.project_id:
            self.project_id = new_project_id()
            project_dir(self.data_dir, self.project_id)
        return self.project_id


class Bot:
    def __init__(self, api: TelegramApi, config):
        self.api = api
        self.config = config
        self.brain = get_brain(config)
        self.sessions: dict = {}
        # v9 access control: owner = OWNER_ID + configured admin ids.
        self.registry = UserRegistry(
            config.data_dir, set(getattr(config, "admin_ids", set()) or set()))
        self.queue = JobQueue(config.data_dir, self._run_job, max_workers=1)

    def session(self, user_id: int) -> Session:
        if user_id not in self.sessions:
            sess = Session(self.config.data_dir)
            sess.user_id = user_id
            self.sessions[user_id] = sess
            self._load_user_state(sess)
        return self.sessions[user_id]

    def _user_file(self, user_id: int) -> str:
        path = os.path.join(self.config.data_dir, "users")
        os.makedirs(path, exist_ok=True)
        return os.path.join(path, f"{user_id}.json")

    def _load_user_state(self, sess: Session):
        # v9: profile and access live in ONE record (data/users/<id>.json)
        # managed by UserRegistry; loading goes through its cache.
        data = self.registry.get(sess.user_id)
        if not data:
            return
        try:
            if data.get("brand"):
                sess.brand = BrandProfile.from_dict(data["brand"])
        except Exception:
            pass
        sess.ownership_confirmed = bool(data.get("ownership_confirmed"))
        sess.review_by_mati = bool(data.get("review_by_mati", True))
        sess.font_id = data.get("font_id", "") or ""
        sess.single_set_name = data.get("single_set_name", "") or ""
        sess.logo_rect = data.get("logo_rect")

    def _save_user_state(self, sess: Session):
        if sess.user_id is None:
            return
        # update_profile merges ONLY the profile fields — the access
        # fields (status/quota/usage) in the same file are preserved.
        self.registry.update_profile(sess.user_id, {
            "brand": sess.brand.to_dict(),
            "ownership_confirmed": sess.ownership_confirmed,
            "review_by_mati": sess.review_by_mati,
            "font_id": sess.font_id,
            "single_set_name": sess.single_set_name,
            "logo_rect": sess.logo_rect,
        })

    def _save_named_brand(self, user_id: int, name: str, brand: BrandProfile):
        path = os.path.join(self.config.data_dir, "brands")
        os.makedirs(path, exist_ok=True)
        file_path = os.path.join(path, f"{user_id}.json")
        profiles = read_json(file_path, default={}) or {}
        profiles[name] = brand.to_dict()
        write_json(file_path, profiles)

    def _load_named_brand(self, user_id: int, name: str):
        file_path = os.path.join(self.config.data_dir, "brands", f"{user_id}.json")
        profiles = read_json(file_path, default={}) or {}
        return profiles.get(name)

    # -- helpers -------------------------------------------------------
    def send(self, chat_id, text: str, markup=None, parse_mode: str | None = None):
        try:
            if parse_mode:
                self.api.send_message(chat_id, text, reply_markup=markup,
                                      parse_mode=parse_mode)
            else:
                self.api.send_message(chat_id, text, reply_markup=markup)
        except Exception as exc:
            print(f"[send-error] {redact(str(exc), self.config.token)}", flush=True)

    def _send_with_id(self, chat_id, text: str):
        """Send a message and return its message_id (None when the
        API did not give one — mocked or failed sends)."""
        try:
            result = self.api.send_message(chat_id, text)
            if isinstance(result, dict):
                mid = result.get("message_id")
                return mid if isinstance(mid, int) else None
        except Exception as exc:
            print(f"[send-error] {redact(str(exc), self.config.token)}", flush=True)
        return None

    def _make_reporter(self, chat_id, label: str, total_hint: int = 0):
        """A ProgressReporter editing ONE status message (v10).
        Returns (reporter, fallback_milestones_set): when no message
        id is available, the reporter is None and callers fall back
        to the old milestone sends."""
        mid = self._send_with_id(chat_id, f"⏳ {label}: شروع شد…")
        if mid is None:
            return None, {25, 50, 75, 100}

        def _edit(text, _chat=chat_id, _mid=mid):
            self.api.edit_message_text(_chat, _mid, text)

        return ProgressReporter(_edit, label=label), set()

    # -- my packs + owner stats (v10) ----------------------------------
    def _user_projects(self, user_id: int) -> list:
        """Projects attributable to this user: the session's current
        project, projects in the job log for this user, and projects
        whose review_pending.json names the user. Returns dicts with
        project_id/title/count/set_name/link/final_count."""
        from emoji_forge.publish import set_link as _set_link
        from emoji_forge.storage import load_jobs as _load_jobs
        ids = []
        sess = self.sessions.get(user_id)
        if sess and sess.project_id:
            ids.append(sess.project_id)
        try:
            for job in _load_jobs(self.config.data_dir).get("jobs", []):
                if job.get("user_id") == user_id and job.get("project_id"):
                    ids.append(job["project_id"])
        except Exception:
            pass
        projects_root = os.path.join(self.config.data_dir, "projects")
        try:
            candidates = os.listdir(projects_root)
        except OSError:
            candidates = []
        for pid in candidates:
            pending = read_json(os.path.join(projects_root, pid, "review_pending.json"),
                                default={}) or {}
            if pending.get("user_id") == user_id:
                ids.append(pid)
        out = []
        seen = set()
        for pid in ids:
            if pid in seen or not re.fullmatch(r"[A-Za-z0-9-]+", pid or ""):
                continue
            seen.add(pid)
            root = os.path.join(projects_root, pid)
            manifest = read_json(os.path.join(root, "manifest.json"),
                                 default={}) or {}
            final_dir = os.path.join(root, "final")
            final_count = 0
            try:
                final_count = len([f for f in os.listdir(final_dir)
                                   if os.path.isfile(os.path.join(final_dir, f))])
            except OSError:
                pass
            # A published set name survives in publish jobs' details
            # ("published <set_name> (N stickers)") and in the v10
            # publish checkpoint.
            set_name = ""
            try:
                for job in _load_jobs(self.config.data_dir).get("jobs", []):
                    detail = str(job.get("detail") or "")
                    if (job.get("project_id") == pid
                            and detail.startswith("published ")):
                        set_name = detail.split()[1]
            except Exception:
                pass
            if not set_name:
                cp = read_json(os.path.join(root, "publish_progress.json"),
                               default={}) or {}
                if cp.get("set_created") and cp.get("set_name"):
                    set_name = cp["set_name"]
            out.append({
                "project_id": pid,
                "title": manifest.get("set_title") or manifest.get("set_name") or pid,
                "count": manifest.get("count") or len(manifest.get("items") or []),
                "final_count": final_count,
                "set_name": set_name,
                "link": _set_link(set_name, "custom_emoji") if set_name else "",
            })
        return out

    def _send_my_packs(self, chat_id: int, user_id: int):
        packs = self._user_projects(user_id)
        single_name = (self.registry.get(user_id) or {}).get("single_set_name") or ""
        if not packs and not single_name:
            self.send(chat_id, "هنوز پکی ندارید؛ یک ایموجی یا پک بفرستید تا شروع کنید.",
                      main_menu(is_admin=self._is_admin(user_id)))
            return
        lines = ["📦 پک‌های من:"]
        for pack in packs:
            line = (f"• {pack['title']} — {pack['count']} ایموجی"
                    f" (نهایی آماده: {pack['final_count']})")
            if pack["link"]:
                line += f"\n  لینک ست: {pack['link']}"
            lines.append(line)
        if single_name:
            from emoji_forge.publish import set_link as _set_link
            lines.append(f"• ست ایموجی‌های تکی من:\n  {_set_link(single_name, 'custom_emoji')}")
        self.send(chat_id, "\n".join(lines),
                  main_menu(is_admin=self._is_admin(user_id)))

    def _send_bot_stats(self, chat_id: int):
        from emoji_forge.storage import load_jobs as _load_jobs
        users = self.registry.all_users()
        by_status = {}
        emojis_total = 0
        for rec in users:
            by_status[rec.get("status") or "pending"] = \
                by_status.get(rec.get("status") or "pending", 0) + 1
            emojis_total += int((rec.get("usage") or {}).get("emojis_used") or 0)
        projects_root = os.path.join(self.config.data_dir, "projects")
        try:
            project_ids = [d for d in os.listdir(projects_root)
                           if os.path.isdir(os.path.join(projects_root, d))]
        except OSError:
            project_ids = []
        stickers_final = 0
        for pid in project_ids:
            try:
                final_dir = os.path.join(projects_root, pid, "final")
                stickers_final += len([f for f in os.listdir(final_dir)
                                       if f.endswith(".tgs")])
            except OSError:
                pass
        jobs = _load_jobs(self.config.data_dir).get("jobs", [])
        by_job_status = {}
        for job in jobs:
            by_job_status[job.get("status") or "?"] = \
                by_job_status.get(job.get("status") or "?", 0) + 1
        status_fa = {"pending": "در انتظار تأیید", "approved": "تأییدشده",
                     "rejected": "ردشده", "blocked": "مسدود"}
        lines = ["📊 آمار ربات (نسخه ۱۰):",
                 f"کاربران: {len(users)} — "
                 + "، ".join(f"{status_fa.get(k, k)}: {v}"
                             for k, v in sorted(by_status.items())),
                 f"پروژه‌ها: {len(project_ids)}",
                 f"استیکر پردازش‌شده (فایل نهایی TGS): {stickers_final}",
                 f"ایموجی مصرف‌شده از سهمیه کاربران: {emojis_total}",
                 "صف کارها: " + ("، ".join(f"{k}: {v}"
                                            for k, v in sorted(by_job_status.items()))
                                  or "خالی")]
        self.send(chat_id, "\n".join(lines), admin_menu())

    # -- access control (v9) -------------------------------------------
    def _is_admin(self, user_id) -> bool:
        return self.registry.is_owner(user_id)

    def _register_user(self, user_id: int, user: dict):
        """Touch the registry for an interacting user; a brand-new
        non-owner user triggers the owner notification exactly once."""
        rec, is_new = self.registry.touch(
            user_id, user.get("first_name", "") or "", user.get("username", "") or "")
        if is_new and not self.registry.is_owner(user_id):
            self._notify_owner_new_user(rec)
        return rec, is_new

    def _notify_owner_new_user(self, rec: dict):
        uid = rec.get("user_id")
        text = ("👤 کاربر جدید در انتظار تأیید است:\n"
                + self.registry.describe_user(rec)
                + "\n\nبا دکمه‌ها تأیید یا رد کنید؛ مدیریت کامل: /users")
        markup = {"inline_keyboard": [[
            btn("✅ تأیید", callback_data=f"admin:approve:{uid}", style=SUCCESS, icon="check"),
            btn("❌ رد", callback_data=f"admin:reject:{uid}", style=DANGER, icon="cross"),
        ]]}
        for owner_id in sorted(self.registry.owner_ids):
            self.send(owner_id, text, markup)

    def _quota_gate(self, user_id: int, chat_id: int, item_count: int) -> bool:
        """Quota check BEFORE enqueueing a process/publish job."""
        ok, reason = self.registry.check_quota(user_id, item_count)
        if not ok:
            self.send(chat_id, f"⛔ {reason}")
        return ok

    def _audit_gate(self, sess: Session, chat_id: int, user_id: int) -> bool:
        """New-pack safety (v9): before an AUTOMATIC full run in a
        destructive mode, audit the pack's TGS files with the generic
        audit. Low logo-classification confidence routes the pack to
        the bot's advanced-processing queue instead of guessing —
        uncertain layers are never silently deleted. Returns True
        when the caller may proceed with the automatic run."""
        if sess.mode not in ("strip", "v8") or not sess.manifest:
            return True
        if sess.review_by_mati:
            return True  # already routed to personal review
        docs = []
        for item in sess.manifest.get("items", []):
            path = item.get("local_path", "")
            if path and path.endswith(".tgs") and os.path.exists(path):
                try:
                    from emoji_forge.animation_engine import load_tgs
                    with open(path, "rb") as fh:
                        docs.append(load_tgs(fh.read()))
                except Exception:
                    pass
        if len(docs) < 2:
            return True
        try:
            from emoji_forge.audit import audit_pack
            report = audit_pack(docs)
        except Exception:
            return True
        if report.get("confidence") == "high":
            return True
        self.send(chat_id,
                  "🔎 اطمینان تشخیص لوگوی این پک پایین است؛ برای اینکه لایه‌ای "
                  "اشتباهی حذف نشود، ربات این پک را با پردازش پیشرفته بررسی می‌کند "
                  "و نتیجه را همین‌جا می‌فرستد؛ لازم نیست دوباره دکمه‌ای بزنید.")
        self._queue_mati_review(sess, chat_id, user_id)
        return False

    # -- admin (owner-only, v9) -----------------------------------------
    def _send_users_list(self, chat_id: int):
        users = self.registry.all_users()
        if not users:
            self.send(chat_id, "هنوز کاربری ثبت نشده است.")
            return
        lines = ["👥 کاربران ربات:"]
        rows = []
        for rec in users:
            lines.append(self.registry.describe_user(rec))
            uid = rec.get("user_id")
            if not self.registry.is_owner(uid):
                rows.append([btn(f"⚙️ مدیریت {uid}", callback_data=f"admin:user:{uid}", icon="settings")])
        rows.append([btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")])
        self.send(chat_id, "\n".join(lines), {"inline_keyboard": rows})

    def _send_user_detail(self, chat_id: int, uid: int):
        rec = self.registry.get(uid)
        if rec is None:
            self.send(chat_id, f"کاربری با شناسه {uid} پیدا نشد.")
            return
        usage = rec.get("usage") or {}
        by_day = usage.get("usage_by_day") or {}
        today = by_day.get(tehran_today()) or {}
        text = ("👤 " + self.registry.describe_user(rec)
                + f"\nمصرف امروز: {today.get('packs', 0)} پک، {today.get('emojis', 0)} ایموجی"
                + "\n\nوضعیت یا سهمیه را از دکمه‌ها تغییر بدهید:")
        self.send(chat_id, text, admin_user_keyboard(uid))

    def _admin_set_status(self, chat_id: int, uid: int, status: str, verb_fa: str):
        rec = self.registry.set_status(uid, status)
        self.send(chat_id, f"{verb_fa}\n" + self.registry.describe_user(rec),
                  admin_user_keyboard(uid))
        if status == "approved":
            self.send(uid, "✅ دسترسی شما به Abolfazl تأیید شد! /start را بزنید و شروع کنید.",
                      main_menu())
        elif status == "rejected":
            self.send(uid, "❌ درخواست دسترسی شما به Abolfazl رد شد.")
        elif status == "blocked":
            self.send(uid, "⛔ دسترسی شما به Abolfazl مسدود شد.")

    def _handle_admin_callback(self, query: dict, data: str, chat_id: int, user_id: int) -> bool:
        """Owner-only admin callbacks. Returns True when handled.
        A non-owner pressing an admin button only gets a refusal
        callback answer — nothing is changed."""
        if not data.startswith("admin:"):
            return False
        if not self.registry.is_owner(user_id):
            try:
                self.api.answer_callback(query.get("id", ""), "⛔ این بخش فقط برای مالک ربات است.")
            except Exception:
                pass
            return True
        parts = data.split(":")
        action = parts[1] if len(parts) > 1 else ""
        try:
            self.api.answer_callback(query.get("id", ""))
        except Exception:
            pass
        sess = self.session(user_id)
        if action == "users":
            self._send_users_list(chat_id)
        elif action == "stats":
            self._send_bot_stats(chat_id)
        elif action == "user" and len(parts) == 3:
            self._send_user_detail(chat_id, int(parts[2]))
        elif action in ("approve", "reject", "block", "unblock") and len(parts) == 3:
            uid = int(parts[2])
            status = {"approve": "approved", "reject": "rejected",
                      "block": "blocked", "unblock": "approved"}[action]
            verb = {"approve": "✅ کاربر تأیید شد.", "reject": "❌ کاربر رد شد.",
                    "block": "⛔ کاربر مسدود شد.", "unblock": "🔓 مسدودیت کاربر رفع شد."}[action]
            self._admin_set_status(chat_id, uid, status, verb)
        elif action == "qpreset" and len(parts) == 4:
            uid = int(parts[2])
            preset = QUOTA_PRESETS.get(parts[3])
            self.registry.set_quota(uid, preset)
            self._send_user_detail(chat_id, uid)
        elif action == "qcustom" and len(parts) == 3:
            uid = int(parts[2])
            sess.awaiting = f"admin_quota:{uid}"
            self.send(chat_id,
                      f"سهمیه دلخواه برای کاربر {uid}: سه عدد با فاصله بفرستید — "
                      "«پک کل ایموجی کل پک روزانه»، مثل: 5 200 2")
        elif action == "credit":
            current = bool(get_settings(self.config.data_dir).get("credit_in_outputs"))
            new_value = not current
            set_setting(self.config.data_dir, "credit_in_outputs", new_value)
            state = "روشن" if new_value else "خاموش"
            self.send(chat_id,
                      f"🏷 اعتبار ربات در خروجی کاربران {state} شد.\n"
                      "وقتی روشن است، فقط برای کاربرانی که واترمارک خودشان را تنظیم نکرده‌اند، "
                      "واترمارک «Abolfazl» با سازوکار استاندارد واترمارک اضافه می‌شود. "
                      "برند خود کاربر هرگز بازنویسی نمی‌شود.",
                      admin_menu())
        else:
            self.send(chat_id, "بخش مدیریت:", admin_menu())
        return True

    def _handle_admin_command(self, sess: Session, chat_id: int, user_id: int,
                              cmd: str, arg: str) -> bool:
        """Owner-only admin commands. Returns True when handled."""
        if cmd not in ("/users", "/approve", "/reject", "/block", "/unblock",
                       "/quota", "/apply_branding", "/admin", "/stats"):
            return False
        if not self.registry.is_owner(user_id):
            self.send(chat_id, "⛔ این دستور فقط برای مالک ربات است.")
            return True
        if cmd == "/admin":
            self.send(chat_id, "بخش مدیریت ربات (فقط مالک):", admin_menu())
        elif cmd == "/stats":
            self._send_bot_stats(chat_id)
        elif cmd == "/users":
            self._send_users_list(chat_id)
        elif cmd in ("/approve", "/reject", "/block", "/unblock"):
            try:
                uid = int(arg.strip())
            except ValueError:
                self.send(chat_id, f"شناسه عددی کاربر را بدهید، مثل: {cmd} 123456789")
                return True
            status = {"/approve": "approved", "/reject": "rejected",
                      "/block": "blocked", "/unblock": "approved"}[cmd]
            verb = {"/approve": "✅ کاربر تأیید شد.", "/reject": "❌ کاربر رد شد.",
                    "/block": "⛔ کاربر مسدود شد.", "/unblock": "🔓 مسدودیت کاربر رفع شد."}[cmd]
            self._admin_set_status(chat_id, uid, status, verb)
        elif cmd == "/quota":
            fields = arg.split()
            try:
                uid = int(fields[0])
                packs, emojis, per_day = int(fields[1]), int(fields[2]), int(fields[3])
            except (ValueError, IndexError):
                self.send(chat_id, "فرمت: /quota <شناسه> <پک کل> <ایموجی کل> <پک روزانه> — مثل: /quota 123456789 5 200 2")
                return True
            try:
                self.registry.set_quota(uid, {"packs_total": packs,
                                              "emojis_total": emojis,
                                              "packs_per_day": per_day})
            except ValueError as exc:
                self.send(chat_id, str(exc))
                return True
            self._send_user_detail(chat_id, uid)
        elif cmd == "/apply_branding":
            try:
                results = apply_branding(self.api)
                done = [k for k, v in results.items() if v]
                self.send(chat_id,
                          "🏷 برند ربات اعمال شد (نام، توضیح کوتاه، توضیح کامل، فهرست دستورها).\n"
                          f"بخش‌های موفق: {', '.join(done) or '—'}")
            except Exception as exc:
                self.send(chat_id, f"اعمال برند ناموفق بود: {redact(str(exc), self.config.token)[:300]}")
        return True

    def _save_upload(self, sess: Session, data: bytes, declared: str, kind_hint: str):
        """Normalise (JPEG photos -> PNG), validate, save as an original."""
        if data[:3] == b"\xff\xd8\xff":
            img = Image.open(io.BytesIO(data)).convert("RGBA")
            buf = io.BytesIO()
            img.save(buf, format="PNG")
            data = buf.getvalue()
            declared = os.path.splitext(declared)[0] + ".png"
        ext, kind = validate_upload(data, declared, self.config.max_file_bytes)
        pid = sess.ensure_project()
        root = project_dir(self.config.data_dir, pid)
        originals = os.path.join(root, "originals")
        os.makedirs(originals, exist_ok=True)
        index = len(os.listdir(originals)) + 1
        name = ordered_name(pid, index, os.path.splitext(safe_filename(declared))[0], ext)
        path = os.path.join(originals, name)
        with open(path, "wb") as fh:
            fh.write(data)
        return {"index": index, "file_id": "", "file_unique_id": "", "custom_emoji_id": "",
                "kind": kind, "extension_hint": ext, "emoji": "", "width": None,
                "height": None, "file_size": len(data), "set_name": "",
                "status": "downloaded", "local_path": path}

    def _ask_ownership(self, sess: Session, chat_id: int, manifest: dict):
        """Gate for full-pack downloads: the user must confirm they own
        the pack or have permission to modify/use it commercially."""
        if sess.ownership_confirmed:
            sess.manifest = manifest
            self.send(chat_id, f"پک پیدا شد: {manifest.get('set_title')} — {manifest['count']} ایموجی. در حال دانلود…")
            self._download_manifest_items(sess, chat_id)
            return
        sess.pending_manifest = manifest
        self.send(chat_id,
                  f"پک پیدا شد: {manifest.get('set_title')} — {manifest['count']} ایموجی.\n\n"
                  "⚖️ تأیید کنید: مالک این پک هستید یا اجازه تغییر و استفاده تجاری آن را دارید؟ "
                  "بدون این تأیید، دانلود پک کامل انجام نمی‌شود.",
                  {"inline_keyboard": [[btn("✅ مالک هستم / اجازه دارم", callback_data="owner_ok", style=SUCCESS, icon="check"),
                                        btn("❌ نیستم", callback_data="owner_no", style=DANGER, icon="cross")]]})

    def _download_manifest_items(self, sess: Session, chat_id: int):
        manifest = sess.manifest
        root = project_dir(self.config.data_dir, sess.project_id)
        originals = os.path.join(root, "originals")
        os.makedirs(originals, exist_ok=True)
        counts = {}
        total = len(manifest["items"])
        milestones = {25, 50, 75, 100}
        for pos, item in enumerate(manifest["items"], start=1):
            try:
                info = self.api.get_file(item["file_id"])
                data = self.api.download_file(info["file_path"])
                ext, kind = validate_upload(data, item.get("extension_hint", ""), self.config.max_file_bytes)
                name = ordered_name(sess.project_id, item["index"],
                                    item.get("file_unique_id", "item")[:20], ext)
                path = os.path.join(originals, name)
                with open(path, "wb") as fh:
                    fh.write(data)
                item["local_path"] = path
                item["status"] = "downloaded"
                item["kind"] = kind
                counts[kind] = counts.get(kind, 0) + 1
            except Exception as exc:
                item["status"] = "failed"
                item["detail"] = redact(str(exc), self.config.token)[:200]
            pct = int(pos * 100 / max(1, total))
            if pct in milestones:
                milestones.discard(pct)
                self.send(chat_id, f"⏳ دانلود: {pct}٪ ({pos} از {total})")
        write_json(os.path.join(root, "manifest.json"), manifest)
        ok = sum(1 for i in manifest["items"] if i["status"] == "downloaded")
        failed = total - ok
        text = (f"دریافت پک تمام شد: {ok} از {total} فایل دانلود شد.\n"
                f"نوع‌ها: {counts or '—'}\n"
                f"پک: {manifest.get('set_name') or 'نامشخص'} — {manifest.get('set_title', '')}\n")
        if failed:
            text += "برخی فایل‌ها ناموفق بودند؛ فایل‌ها را دوباره بفرستید.\n"
        text += "حالا با دکمه‌ها رنگ، نام و لوگو را تنظیم کنید و «پیش‌نمایش» را بزنید."
        self.send(chat_id, text, main_menu(is_admin=self._is_admin(sess.user_id)))

    # -- commands ------------------------------------------------------
    def handle_command(self, sess: Session, chat_id: int, user_id: int, text: str):
        parts = text.strip().split(None, 1)
        cmd = parts[0].lower()
        arg = parts[1].strip() if len(parts) > 1 else ""
        if self._handle_admin_command(sess, chat_id, user_id, cmd, arg):
            return
        if cmd == "/start":
            self.send(chat_id, START_HTML, main_menu(is_admin=self._is_admin(user_id)),
                      parse_mode="HTML")
        elif cmd == "/help":
            self.send(chat_id, HELP_HTML, parse_mode="HTML")
        elif cmd == "/mypacks":
            self._send_my_packs(chat_id, user_id)
        elif cmd == "/brain":
            self.send(chat_id,
                      "🤖 موتور پردازش ربات فعال است؛ همه‌ی پردازش‌ها را خود ربات انجام می‌دهد.")
        elif cmd == "/suggest":
            formats = {}
            if sess.manifest:
                for item in sess.manifest.get("items", []):
                    kind = item.get("kind", "static")
                    formats[kind] = formats.get(kind, 0) + 1
            request = {"kind": "analyze", "user_text": arg,
                       "formats": formats,
                       "brand_name": sess.brand.brand_name,
                       "mode": sess.mode,
                       "count": len(formats) and sum(formats.values()) or 0}
            decision = self.brain.decide(request)
            text = decision.message_fa or decision.suggestion_fa or "پیشنهادی ندارم."
            if decision.mode:
                text += f"\nحالت پیشنهادی (اعمال نشده): {decision.mode} — برای اعمال، /mode {decision.mode} را بزنید."
            self.send(chat_id, text)
            b = sess.brand
            self.send(chat_id,
                      f"پروفایل برند:\nنام: {b.brand_name or '—'}\nرنگ اصلی: {b.primary_hex}\n"
                      f"رنگ ثانویه: {b.secondary_hex}\nدرخشش: {b.glow_hex}\n"
                      f"محل لوگو: {b.logo_position} — اندازه: {b.logo_size_pct}٪\n"
                      f"وفاداری: {b.fidelity} — بازطراحی: {b.redesign_intensity}\n"
                      f"واترمارک: {b.watermark_text or '—'} ({b.watermark_opacity}٪، {b.watermark_position})\n"
                      f"حالت: {sess.mode} — لایه‌های TGS پنهان: {', '.join(sorted(sess.tgs_hide)) or '—'}\n"
                      f"لوگو: {'دارد' if sess.logo_data else 'ندارد'}\n"
                      f"با /brand_save [نام] این پروفایل را برای استفاده بعدی ذخیره کنید.")
        elif cmd == "/brand_name":
            sess.brand.brand_name = arg
            self.send(chat_id, f"نام برند ثبت شد: {arg or '—'}")
        elif cmd == "/brand_color":
            try:
                sess.brand.primary_hex = validate_hex(arg)
                self.send(chat_id, f"رنگ اصلی: {sess.brand.primary_hex}")
            except ValueError as exc:
                self.send(chat_id, str(exc))
        elif cmd == "/brand_color2":
            try:
                sess.brand.secondary_hex = validate_hex(arg)
                self.send(chat_id, f"رنگ ثانویه: {sess.brand.secondary_hex}")
            except ValueError as exc:
                self.send(chat_id, str(exc))
        elif cmd == "/brand_glow":
            try:
                sess.brand.glow_hex = validate_hex(arg)
                self.send(chat_id, f"رنگ درخشش: {sess.brand.glow_hex}")
            except ValueError as exc:
                self.send(chat_id, str(exc))
        elif cmd == "/brand_position":
            if arg in ("center", "top", "bottom", "top-left", "top-right", "bottom-left", "bottom-right"):
                sess.brand.logo_position = arg
                self.send(chat_id, f"محل لوگو: {arg}")
            else:
                self.send(chat_id, "محل نامعتبر است. گزینه‌ها: center, top, bottom, top-left, top-right, bottom-left, bottom-right")
        elif cmd == "/brand_size":
            try:
                value = int(arg)
                if 10 <= value <= 60:
                    sess.brand.logo_size_pct = value
                    self.send(chat_id, f"اندازه لوگو: {value}٪")
                else:
                    self.send(chat_id, "اندازه باید بین ۱۰ تا ۶۰ باشد.")
            except ValueError:
                self.send(chat_id, "عدد معتبر بفرستید، مثل: /brand_size 25")
        elif cmd == "/brand_fidelity":
            try:
                value = int(arg)
                if 0 <= value <= 100:
                    sess.brand.fidelity = value
                    self.send(chat_id, f"وفاداری: {value}")
                else:
                    self.send(chat_id, "وفاداری باید بین ۰ تا ۱۰۰ باشد.")
            except ValueError:
                self.send(chat_id, "عدد معتبر بفرستید.")
        elif cmd == "/brand_redesign":
            try:
                value = int(arg)
                if 0 <= value <= 100:
                    sess.brand.redesign_intensity = value
                    self.send(chat_id, f"شدت بازطراحی: {value}")
                else:
                    self.send(chat_id, "شدت باید بین ۰ تا ۱۰۰ باشد.")
            except ValueError:
                self.send(chat_id, "عدد معتبر بفرستید.")
        elif cmd == "/brand_save":
            name = arg or "default"
            self._save_named_brand(user_id, name, sess.brand)
            self._save_user_state(sess)
            self.send(chat_id, f"پروفایل برند با نام «{name}» ذخیره شد. با /brand_load {name} دوباره بارگذاری می‌شود.")
        elif cmd == "/brand_load":
            if not arg:
                self.send(chat_id, "نام پروفایل را بدهید، مثل: /brand_load default")
            else:
                data = self._load_named_brand(user_id, arg)
                if data is None:
                    self.send(chat_id, f"پروفایلی با نام «{arg}» پیدا نشد.")
                else:
                    sess.brand = BrandProfile.from_dict(data)
                    self._save_user_state(sess)
                    self.send(chat_id, f"پروفایل «{arg}» بارگذاری شد.")
        elif cmd == "/watermark":
            sess.brand.watermark_text = arg
            self.send(chat_id, f"واترمارک: {arg or '—'}")
        elif cmd == "/watermark_opacity":
            try:
                value = int(arg)
                if 0 <= value <= 100:
                    sess.brand.watermark_opacity = value
                    self.send(chat_id, f"شفافیت واترمارک: {value}٪")
                else:
                    self.send(chat_id, "شفافیت باید بین ۰ تا ۱۰۰ باشد.")
            except ValueError:
                self.send(chat_id, "عدد معتبر بفرستید، مثل: /watermark_opacity 35")
            sess.awaiting = "brand_logo"
            self.send(chat_id, "لوگو را به‌صورت فایل (PNG شفاف) بفرستید.")
        elif cmd == "/clean":
            if arg in PRESETS:
                x0, y0, x1, y1 = PRESETS[arg]
                sess.region = Region(x0, y0, x1, y1)
                self.send(chat_id, f"ناحیه پاک‌سازی: {arg} ({x0},{y0} تا {x1},{y1})")
            elif arg == "none":
                sess.region = None
                self.send(chat_id, "پاک‌سازی غیرفعال شد.")
            else:
                self.send(chat_id, "ناحیه نامعتبر است. گزینه‌ها: top, bottom, center, top-left, top-right, bottom-left, bottom-right, none")
        elif cmd == "/mode":
            if arg in ("preserve", "clean", "redesign", "branding", "strip", "v8"):
                sess.mode = arg
                labels = {"preserve": "حفظ ظاهر اصلی (فقط تبدیل فرمت)",
                          "clean": "فقط پاک‌سازی",
                          "redesign": "بازطراحی محدود",
                          "branding": "برندینگ کامل",
                          "v8": "v8 — لوگوی برداری شما با فیت دقیق داخل قاب لوگوی قبلی (بدون لوگو: متن دقیق v5)",
                          "strip": "خام‌سازی و بازسازی (حذف لوگوی قبلی + خنثی‌سازی رنگ + بازسازی با رنگ/اسم/لوگوی شما)"}
                self.send(chat_id, f"حالت: {labels[arg]}", main_menu())
            elif arg == "faithful":
                sess.mode = "branding"
                self.send(chat_id, "حالت: برندینگ کامل (نام قدیمی faithful)")
            else:
                self.send(chat_id, "حالت نامعتبر است: /mode preserve یا clean یا redesign یا branding یا strip یا v8")
        elif cmd == "/tgs_layers":
            if not sess.manifest:
                self.send(chat_id, "هنوز پروژه‌ای ندارید.")
            else:
                found = []
                for item in sess.manifest["items"]:
                    path = item.get("local_path", "")
                    if path and path.endswith(".tgs") and os.path.exists(path):
                        try:
                            from emoji_forge.animation_engine import load_tgs, tgs_logo_candidates
                            with open(path, "rb") as fh:
                                doc = load_tgs(fh.read())
                            found = tgs_logo_candidates(doc)
                            break
                        except Exception as exc:
                            self.send(chat_id, f"خواندن TGS ناموفق بود: {redact(str(exc), self.config.token)[:200]}")
                            return
                if found:
                    self.send(chat_id, "لایه‌های نامزد لوگو/متن در اولین TGS:\n" + "\n".join(f"• {n}" for n in found) +
                              "\nبا /tgs_hide <نام> آن را در پردازش پنهان کنید.")
                else:
                    self.send(chat_id, "در اولین TGS، لایه‌ای با نام لوگو/متن پیدا نشد (یا فایل TGS ندارید).")
        elif cmd == "/tgs_hide":
            if not arg:
                self.send(chat_id, "نام لایه را بدهید، مثل: /tgs_hide LogoBadge")
            else:
                sess.tgs_hide.add(arg)
                self.send(chat_id, f"لایه «{arg}» در پردازش TGS پنهان می‌شود.")
        elif cmd == "/tgs_clear":
            sess.tgs_hide = set()
            self.send(chat_id, "فهرست لایه‌های پنهان TGS پاک شد.")
        elif cmd == "/retry":
            if not sess.manifest:
                self.send(chat_id, "پروژه‌ای در جریان نیست.")
            else:
                failed = [i for i in sess.manifest["items"] if i.get("status") == "failed"]
                if not failed:
                    self.send(chat_id, "مورد ناموفقی برای تلاش مجدد نیست؛ اگر پردازش ناموفق بود، «پیش‌نمایش» یا «پردازش کامل» را دوباره بزنید.")
                else:
                    self.send(chat_id, f"تلاش مجدد برای {len(failed)} فایل ناموفق…")
                    # Re-download only the failed items, reusing the same path.
                    saved = sess.manifest["items"]
                    sess.manifest["items"] = failed
                    try:
                        self._download_manifest_items(sess, chat_id)
                    finally:
                        # _download replaced counts text; restore full manifest.
                        sess.manifest["items"] = saved
                        for item in failed:
                            for full in saved:
                                if full["index"] == item["index"]:
                                    full.update(item)
                        try:
                            write_json(os.path.join(project_dir(self.config.data_dir, sess.project_id), "manifest.json"), sess.manifest)
                        except Exception:
                            pass
        elif cmd == "/preview":
            self._preview(sess, chat_id)
        elif cmd == "/process":
            if not sess.preview_approved:
                self.send(chat_id, "اول «پیش‌نمایش» را بزنید و پیش‌نمایش را تأیید کنید؛ پردازش کامل فقط پس از تأیید انجام می‌شود.")
                return
            item_count = len((sess.manifest or {}).get("items") or [])
            if not self._quota_gate(user_id, chat_id, item_count):
                return
            if not self._audit_gate(sess, chat_id, user_id):
                return
            job_id = f"{sess.project_id}-full-{int(time.time())}"
            sess.last_job_id = job_id
            self.queue.enqueue({"id": job_id,
                                "kind": "full", "user_id": user_id, "chat_id": chat_id,
                                "project_id": sess.project_id, "manifest": sess.manifest,
                                "brand": sess.brand.to_dict(),
                                "region": sess.region.__dict__ if sess.region else None,
                                "mode": sess.mode, "output_kind": sess.output_kind,
                                "hide_layer_names": sorted(sess.tgs_hide)})
            self.send(chat_id, "پردازش کامل در صف قرار گرفت؛ پیشرفت را گزارش می‌کنم و تمام که شد ZIP را می‌فرستم. برای لغو، دکمه «لغو» را بزنید.")
        elif cmd == "/export":
            if not sess.project_id:
                self.send(chat_id, "هنوز پروژه‌ای ندارید؛ اول یک ایموجی یا فایل بفرستید.")
                return
            try:
                result = export_project(self.config.data_dir, sess.project_id)
                self.api.send_document(chat_id, result["zip_path"], caption=zip_caption(len(result['files'])))
                root = project_dir(self.config.data_dir, sess.project_id)
                report_path = os.path.join(root, "report.md")
                if os.path.exists(report_path):
                    self.api.send_document(chat_id, report_path, caption=report_caption())
            except Exception as exc:
                self.send(chat_id, f"خروجی ناموفق بود: {redact(str(exc), self.config.token)[:300]}")
        elif cmd == "/status":
            if not sess.manifest:
                self.send(chat_id, "پروژه‌ای در جریان نیست.")
            else:
                ok = sum(1 for i in sess.manifest["items"] if i["status"] == "downloaded")
                self.send(chat_id, f"پروژه {sess.project_id}\nپک: {sess.manifest.get('set_name') or '—'}\n"
                                  f"دانلودشده: {ok} از {len(sess.manifest['items'])}\n"
                                  f"تأیید پیش‌نمایش: {'بله' if sess.preview_approved else 'خیر'}")
        elif cmd == "/cancel":
            if sess.last_job_id:
                self.queue.cancel(sess.last_job_id)
            sess.manifest = None
            sess.pending_manifest = None
            sess.project_id = ""
            sess.preview_approved = False
            sess.last_job_id = ""
            self.send(chat_id, "پروژه جاری و کار در صف (اگر در صف بود) لغو شد.")
        elif cmd == "/publish_new":
            if not sess.project_id:
                self.send(chat_id, "هنوز پروژه‌ای ندارید؛ اول یک ایموجی یا فایل بفرستید و «پردازش کامل» را کامل کنید.")
                return
            root = project_dir(self.config.data_dir, sess.project_id)
            final_dir = os.path.join(root, "final")
            if not os.path.isdir(final_dir) or not os.listdir(final_dir):
                self.send(chat_id, "فایل نهایی برای انتشار وجود ندارد؛ اول «پردازش کامل» را کامل کنید.")
                return
            self.send(chat_id,
                      "برای ساخت پک جدید، «ساخت پک جدید» را بزنید. این کار یک ست جدید می‌سازد که ربات مالک آن است؛ "
                      "پک اصلی شما دست نمی‌خورد. اگر انتشار با API ناموفق شود، خطای واقعی تلگرام را می‌بینید و مسیر دستی ZIP هم هست.",
                      {"inline_keyboard": [[btn("✅ ساخت پک جدید", callback_data="publish_new", style=PRIMARY, icon="pack")]]})
        else:
            self.send(chat_id, "دستور شناخته نشد. /help را بزنید.")

    def _queue_mati_review(self, sess: Session, chat_id: int, user_id: int):
        """Queue a full run for the bot's advanced processing instead
        of running the automatic engine immediately. The advanced
        processor inspects the originals, decides the logo region and
        what to protect (hair/character colours), writes mati_plan.json,
        and the bot's watcher then runs that plan and delivers the
        result automatically — the user never has to tap anything."""
        if not sess.manifest or not sess.manifest.get("items"):
            self.send(chat_id, "هنوز فایلی ندارید؛ اول یک ایموجی، استیکر، لینک پک یا فایل بفرستید.")
            return
        if not self._quota_gate(user_id, chat_id, len(sess.manifest["items"])):
            return
        pid = sess.ensure_project()
        root = project_dir(self.config.data_dir, pid)
        write_json(os.path.join(root, "manifest.json"), sess.manifest)
        req_id = f"review-{pid}-{int(time.time())}"
        brain_dir = os.path.join(self.config.data_dir, "brain", "requests")
        os.makedirs(brain_dir, exist_ok=True)
        write_json(os.path.join(brain_dir, f"{req_id}.json"), {
            "id": req_id, "kind": "review_process", "project_id": pid,
            "user_id": user_id, "chat_id": chat_id, "mode": sess.mode,
            "output_kind": sess.output_kind, "brand": sess.brand.to_dict(),
            "region": sess.region.__dict__ if sess.region else None,
            "hide_layer_names": sorted(sess.tgs_hide),
            "item_count": len(sess.manifest["items"]),
            "originals_dir": os.path.join(root, "originals"),
            "instruction": ("Advanced processing for this pack: look at the originals, "
                            "find the old logo, protect hair/character colours, write "
                            "mati_plan.json in the project dir and a decision file."),
        })
        write_json(os.path.join(root, "review_pending.json"), {
            "request_id": req_id, "user_id": user_id, "chat_id": chat_id,
            "project_id": pid, "created_at": int(time.time()),
        })
        sess.review_pending = True
        self.send(chat_id,
                  "⏳ در حال پردازش پیشرفته توسط ربات…\n"
                  "ربات لوگو را پیدا می‌کند، رنگ مو و کاراکتر را حفظ می‌کند و بعد نتیجه را همین‌جا می‌فرستد.\n"
                  "معمولاً تا چند دقیقه دیگر آماده است؛ لازم نیست دوباره دکمه‌ای بزنید.")

    def _review_watcher(self):
        """Background watcher: when an advanced-processing plan
        (mati_plan.json) lands for a project, run that plan as a
        normal job and deliver the ZIP automatically. Runs inside
        the bot process, so delivery is the bot's own job."""
        import glob as _glob
        while True:
            try:
                pattern = os.path.join(self.config.data_dir, "projects", "*", "mati_plan.json")
                for plan_path in _glob.glob(pattern):
                    try:
                        plan = read_json(plan_path, default=None)
                        if not plan or not plan.get("project_id"):
                            continue
                        root = os.path.dirname(plan_path)
                        manifest_path = os.path.join(root, "manifest.json")
                        manifest = plan.get("manifest") or read_json(manifest_path, default=None)
                        if not manifest:
                            continue
                        brand = BrandProfile.from_dict(plan.get("brand") or {})
                        region = Region(**plan["region"]) if plan.get("region") else None
                        job_id = f"{plan['project_id']}-mati-{int(time.time())}"
                        self.queue.enqueue({"id": job_id, "kind": "process",
                                            "user_id": plan.get("user_id", 0),
                                            "chat_id": plan.get("chat_id", 0),
                                            "project_id": plan["project_id"],
                                            "manifest": manifest, "brand": brand.to_dict(),
                                            "region": region.__dict__ if region else None,
                                            "mode": plan.get("mode", "strip"),
                                            "output_kind": plan.get("output_kind", "emoji"),
                                            "hide_layer_names": plan.get("hide_layer_names") or []})
                        os.rename(plan_path, os.path.join(root, "mati_plan_applied.json"))
                        if plan.get("mati_notes"):
                            self.send(plan.get("chat_id", 0),
                                      f"🔎 بررسی پیشرفته ربات تمام شد: {plan['mati_notes']}\nپردازش پک شروع شد.")
                    except Exception:
                        print("[review-watcher-error]\n" + traceback.format_exc(), flush=True)
                        try:
                            os.rename(plan_path, os.path.join(os.path.dirname(plan_path), "mati_plan_error.json"))
                        except Exception:
                            pass
            except Exception:
                print("[review-watcher-error]\n" + traceback.format_exc(), flush=True)
            # Publish requests: when a plan writes publish_request.json
            # for a project, the bot itself builds
            # the new set and sends the link — the user never needs files.
            try:
                pub_pattern = os.path.join(self.config.data_dir, "projects", "*", "publish_request.json")
                for pub_path in _glob.glob(pub_pattern):
                    try:
                        req = read_json(pub_path, default=None)
                        if not req or not req.get("project_id"):
                            continue
                        root = os.path.dirname(pub_path)
                        manifest = req.get("manifest") or read_json(os.path.join(root, "manifest.json"), default=None)
                        if not manifest:
                            continue
                        job_id = f"{req['project_id']}-publish-{int(time.time())}"
                        self.queue.enqueue({"id": job_id, "kind": "publish",
                                            "user_id": req.get("user_id", 0),
                                            "chat_id": req.get("chat_id", 0),
                                            "project_id": req["project_id"],
                                            "manifest": manifest,
                                            "set_title": req.get("set_title") or manifest.get("set_title", "")})
                        os.rename(pub_path, os.path.join(root, "publish_request_applied.json"))
                        self.send(req.get("chat_id", 0), "⏳ پک در حال ساخته شدن است؛ لینکش را همین‌جا می‌فرستم.")
                    except Exception:
                        print("[publish-watcher-error]\n" + traceback.format_exc(), flush=True)
                        try:
                            os.rename(pub_path, os.path.join(os.path.dirname(pub_path), "publish_request_error.json"))
                        except Exception:
                            pass
            except Exception:
                print("[publish-watcher-error]\n" + traceback.format_exc(), flush=True)
            # Single-emoji advanced-processing results: when a
            # single_plan.json lands in a project dir (the fixed .tgs
            # produced for a queued single_review request), deliver it
            # into the user's personal set automatically — the user
            # never has to send or tap anything.
            try:
                single_pattern = os.path.join(self.config.data_dir, "projects", "*", "single_plan.json")
                for sp_path in _glob.glob(single_pattern):
                    try:
                        plan = read_json(sp_path, default=None)
                        if not plan or not plan.get("file_path"):
                            continue
                        root = os.path.dirname(sp_path)
                        fpath = plan["file_path"]
                        if not os.path.isabs(fpath):
                            fpath = os.path.join(root, fpath)
                        if not os.path.exists(fpath):
                            continue
                        result = self.deliver_single_to_set(
                            plan.get("user_id", 0), plan.get("chat_id", 0),
                            fpath, plan.get("emoji") or "🙂")
                        self.send(plan.get("chat_id", 0),
                                  "✅ ویرایش ایموجی شما آماده شد و به ست شخصی‌تان اضافه شد!\n"
                                  f"لینک ست: {result['link']}")
                        try:
                            self.api.send_sticker(plan.get("chat_id", 0), result["file_id"])
                        except Exception:
                            pass  # preview copy is best-effort
                        try:
                            self.registry.record_usage(plan.get("user_id", 0), packs=0, emojis=1)
                        except Exception:
                            pass
                        os.rename(sp_path, os.path.join(root, "single_plan_applied.json"))
                    except Exception:
                        print("[single-watcher-error]\n" + traceback.format_exc(), flush=True)
                        try:
                            os.rename(sp_path, os.path.join(os.path.dirname(sp_path), "single_plan_error.json"))
                        except Exception:
                            pass
            except Exception:
                print("[single-watcher-error]\n" + traceback.format_exc(), flush=True)
            time.sleep(30)

    def _preview(self, sess: Session, chat_id: int):
        if not sess.manifest or not sess.manifest.get("items"):
            self.send(chat_id, "هنوز فایلی ندارید؛ اول یک ایموجی، استیکر، لینک پک یا فایل بفرستید.")
            return
        samples = sample_indices(len(sess.manifest["items"]))
        try:
            report = process_project(self.config.data_dir, sess.project_id, sess.manifest,
                                     sess.brand, sess.region, sess.mode, sess.output_kind,
                                     only_indices=samples, logo_data=sess.logo_data,
                                     hide_layer_names=set(sess.tgs_hide))
        except Exception as exc:
            self.send(chat_id, f"پیش‌نمایش ناموفق بود: {redact(str(exc), self.config.token)[:300]}")
            return
        lines = ["پیش‌نمایش واقعی نمونه‌ها — فایل‌های اصلی و نهایی هر نمونه را جداگانه می‌فرستم تا مقایسه کنید (فایل‌های نهایی، انیمیشن کامل‌اند و در تلگرام پخش می‌شوند، نه فقط یک فریم):"]
        for item in report["items"]:
            lines.append(f"#{item['index']}: {item['status']} — {item.get('output_name', '')} — {item.get('detail', '')}")
        # v4: validator summary for the preview samples, so a file that
        # Telegram would reject is named here, not after a full publish.
        try:
            from emoji_forge.validate_tgs import validate_final_dir
            _root = project_dir(self.config.data_dir, sess.project_id)
            _blocked = [(idx, iss) for idx, _n, iss, _w in validate_final_dir(os.path.join(_root, "final")) if iss]
            if _blocked:
                lines.append("⚠️ اعتبارسنجی پیش از انتشار این نمونه‌ها را رد کرد: " + "; ".join(f"#{idx}: {iss[0][:120]}" for idx, iss in _blocked))
        except Exception:
            pass
        lines.append("اگر راضی هستید، «تأیید» را بزنید تا پردازش کامل شروع شود.")
        self.send(chat_id, "\n".join(lines),
                  {"inline_keyboard": [[btn("✅ تأیید و پردازش کامل", callback_data="preview_ok", style=SUCCESS, icon="check"),
                                        btn("❌ لغو", callback_data="preview_no", style=DANGER, icon="cross")]]})
        self._send_preview_files(sess, chat_id, report)

    def _render_preview_thumb(self, path: str):
        """One preview thumbnail (PIL Image) for a file, or None.
        TGS renders go through rlottie (Telegram's own renderer)
        when importable; raster files open directly. Any failure
        returns None — the contact sheet is best-effort."""
        try:
            if path.lower().endswith(".tgs"):
                try:
                    from rlottie_python import LottieAnimation
                    anim = LottieAnimation.from_tgs(path)
                    frame = anim.render_pillow_frame(frame_num=0)
                    return frame.convert("RGBA")
                except Exception:
                    return None
            img = Image.open(path).convert("RGBA")
            img.thumbnail((256, 256))
            return img
        except Exception:
            return None

    def _send_preview_contact_sheet(self, sess: Session, chat_id: int, report: dict):
        """v10 preview improvement: a before/after contact sheet —
        original vs processed renders side by side, one row per
        sample — sent as ONE photo before the individual files.
        Silently skipped when rendering is unavailable."""
        try:
            root = project_dir(self.config.data_dir, sess.project_id)
            final_dir = os.path.join(root, "final")
            by_index = {item["index"]: item for item in sess.manifest["items"]}
            rows = []
            cell = 256
            for item in report["items"][:3]:
                if item.get("status") != "ok" or not item.get("output_name"):
                    continue
                original = by_index.get(item["index"], {}).get("local_path", "")
                final_path = os.path.join(final_dir, item["output_name"])
                before = self._render_preview_thumb(original) if original else None
                after = self._render_preview_thumb(final_path)
                if before is None and after is None:
                    continue
                row_img = Image.new("RGBA", (cell * 2 + 12, cell), (24, 24, 24, 255))
                for x_off, thumb in ((0, before), (cell + 12, after)):
                    if thumb is not None:
                        thumb = thumb.copy()
                        thumb.thumbnail((cell, cell))
                        row_img.paste(thumb, (x_off, 0), thumb)
                rows.append(row_img)
            if not rows:
                return
            sheet = Image.new("RGBA", (cell * 2 + 12, cell * len(rows) + 8 * (len(rows) - 1)),
                              (24, 24, 24, 255))
            for i, row_img in enumerate(rows):
                sheet.paste(row_img, (0, i * (cell + 8)), row_img)
            preview_dir = os.path.join(root, "preview")
            os.makedirs(preview_dir, exist_ok=True)
            sheet_path = os.path.join(preview_dir, "contact_sheet.png")
            sheet.save(sheet_path, format="PNG")
            self.api.send_photo(chat_id, sheet_path,
                                caption="پیش‌نمایش کنارهم: چپ = اصلی، راست = نهایی (نسخه ۱۰)")
        except Exception:
            pass  # never crash the preview flow over a cosmetic sheet

    def _send_preview_files(self, sess: Session, chat_id: int, report: dict):
        root = project_dir(self.config.data_dir, sess.project_id)
        final_dir = os.path.join(root, "final")
        self._send_preview_contact_sheet(sess, chat_id, report)
        by_index = {item["index"]: item for item in sess.manifest["items"]}
        for item in report["items"]:
            if item.get("status") != "ok" or not item.get("output_name"):
                continue
            idx = item["index"]
            original = by_index.get(idx, {}).get("local_path", "")
            if original and os.path.exists(original):
                try:
                    self.api.send_document(chat_id, original, caption=f"اصلی #{idx}")
                except Exception as exc:
                    self.send(chat_id, f"ارسال فایل اصلی #{idx} ناموفق بود: {redact(str(exc), self.config.token)[:200]}")
            final_path = os.path.join(final_dir, item["output_name"])
            if os.path.exists(final_path):
                try:
                    self.api.send_document(chat_id, final_path, caption=f"نهایی (پیش‌نمایش) #{idx}")
                except Exception as exc:
                    self.send(chat_id, f"ارسال فایل نهایی #{idx} ناموفق بود: {redact(str(exc), self.config.token)[:200]}")

    def _run_job(self, job: dict):
        """Queue worker entry (v9): runs the job, then records quota
        usage on SUCCESSFUL completion only — a failed or cancelled
        job never consumes quota (documented accounting rule; the
        quota itself is checked before enqueueing)."""
        result = self._run_job_inner(job)
        try:
            if result != "cancelled":
                kind = job.get("kind")
                user_id = job.get("user_id") or 0
                items = len((job.get("manifest") or {}).get("items") or [])
                if kind == "publish":
                    if isinstance(result, str) and result.startswith("published"):
                        self.registry.record_usage(user_id, packs=1, emojis=items)
                elif kind == "single":
                    # One edited emoji = 1 emoji of quota, no pack slot.
                    if isinstance(result, str) and result.startswith("single published"):
                        self.registry.record_usage(user_id, packs=0, emojis=1)
                elif kind in ("full", "process"):
                    self.registry.record_usage(user_id, packs=1, emojis=items)
        except Exception:
            pass
        return result

    def _run_job_inner(self, job: dict):
        """Worker: full run (process + export + send) or real publish."""
        if self.queue.is_cancelled(job["id"]):
            return "cancelled"
        if job.get("kind") == "publish":
            return self._run_publish_job(job)
        if job.get("kind") == "single":
            return self._run_single_job(job)
        from emoji_forge.brand import BrandProfile as _BP
        project_id = job["project_id"]
        brand = _BP.from_dict(job["brand"])
        # v9 admin credit toggle (default OFF): users who have NOT set
        # their own watermark get the standard watermark mechanism
        # with the bot's brand text. A user's own watermark/brand is
        # never overwritten.
        if not brand.watermark_text and get_settings(self.config.data_dir).get("credit_in_outputs"):
            brand.watermark_text = BOT_BRAND
        region = Region(**job["region"]) if job.get("region") else None
        logo_data = None
        logo_path = os.path.join(project_dir(self.config.data_dir, project_id), "logo.png")
        if os.path.exists(logo_path):
            with open(logo_path, "rb") as fh:
                logo_data = fh.read()
        chat_id = job["chat_id"]
        total = len(job["manifest"].get("items", []))
        # v10: ONE status message edited with a live progress bar +
        # ETA (throttled); milestone sends remain the fallback when
        # no editable message id is available.
        reporter, milestones = self._make_reporter(chat_id, "پردازش", total)

        def progress(done: int, _total: int):
            if not total:
                return
            if reporter is not None:
                reporter.update(done, total)
                return
            pct = int(done * 100 / total)
            if pct in milestones:
                milestones.discard(pct)
                self.send(chat_id, f"⏳ پردازش: {pct}٪ ({done} از {total})")

        report = process_project(self.config.data_dir, project_id, job["manifest"],
                                 brand, region, job["mode"], job["output_kind"],
                                 logo_data=logo_data,
                                 hide_layer_names=set(job.get("hide_layer_names") or []),
                                 progress_callback=progress)
        result = export_project(self.config.data_dir, project_id)
        self.send(chat_id, f"پردازش کامل تمام شد: موفق {report['ok']} از {report['total']} — ناموفق {report['failed']}.")
        try:
            self.api.send_document(chat_id, result["zip_path"], caption=zip_caption(len(result['files'])))
            root = project_dir(self.config.data_dir, project_id)
            report_path = os.path.join(root, "report.md")
            if os.path.exists(report_path):
                self.api.send_document(chat_id, report_path, caption=report_caption())
        except Exception as exc:
            self.send(chat_id, f"ارسال ZIP ناموفق بود: {redact(str(exc), self.config.token)[:300]}")
        return f"full run: {report['ok']}/{report['total']} ok"

    def _run_publish_job(self, job: dict):
        """Real publish via Bot API: upload -> createNewStickerSet -> add rest.

        Any Telegram error is reported as-is (redacted) and the manual ZIP
        route is offered. Nothing is faked as published.

        v10: resumable — a publish_progress.json checkpoint inside the
        project records every uploaded file_id and every published
        sticker; re-running the job skips finished stickers instead
        of starting over. Per-sticker emojis come from the project's
        manifest.json when it exists (never a hardcoded default)."""
        from emoji_forge.publish import (
            build_set_name, build_stickers_payload, checkpoint_done_indices,
            checkpoint_published, checkpoint_published_indices,
            checkpoint_uploaded, load_checkpoint, manifest_emojis,
            save_checkpoint, set_link,
        )
        chat_id = job["chat_id"]
        user_id = job["user_id"]
        project_id = job["project_id"]
        root = project_dir(self.config.data_dir, project_id)
        final_dir = os.path.join(root, "final")
        try:
            files = sorted(f for f in os.listdir(final_dir)
                           if os.path.isfile(os.path.join(final_dir, f)))
            emoji_by_index = dict(manifest_emojis(root))
            for item in job["manifest"].get("items", []):
                if item.get("emoji"):
                    emoji_by_index[item["index"]] = [item["emoji"]]
            payload = build_stickers_payload(files, emoji_by_index)
            # v4 pre-publish validation: report EXACTLY which file would
            # fail and why, before any Telegram upload is attempted.
            # Previously a mid-upload "wrong file type" gave no file
            # index at all, so the same pack was re-uploaded blindly.
            try:
                from emoji_forge.validate_tgs import validate_final_dir
                blocked = [(idx, name, iss) for idx, name, iss, _w in validate_final_dir(final_dir) if iss]
            except Exception:
                blocked = []
            if blocked:
                detail_lines = "\n".join(f"فایل #{idx} ({name}): {'; '.join(iss)[:250]}" for idx, name, iss in blocked)
                self.send(chat_id,
                          f"❌ انتشار متوقف شد — اعتبارسنجی پیش از انتشار این فایل‌ها را رد کرد:\n{detail_lines}\n\n"
                          "هیچ فایلی به تلگرام فرستاده نشد؛ اول فایل‌های بالا را اصلاح کنید.")
                return f"publish blocked by validator: {len(blocked)} files: " + "; ".join(f"#{idx} {iss[0][:120]}" for idx, _, iss in blocked)
            me = self.api.get_me()
            set_name = build_set_name(project_id, me.get("username", ""))
            # v9 title rule: a title the user/plan set is kept exactly;
            # only the bot-generated default gets the " • Abolfazl"
            # suffix (see branding.publish_title).
            title = publish_title(job.get("set_title"), project_id)
            # v10 checkpoint: resume where a previous run stopped.
            checkpoint = load_checkpoint(root)
            resumed = bool(checkpoint.get("set_created")
                           and checkpoint.get("set_name") == set_name)
            done_uploads = checkpoint_done_indices(checkpoint)
            self.send(chat_id, f"⏳ انتشار شروع شد: {len(files)} فایل، فرمت {payload['format']}، نام ست {set_name}"
                      + (f" — ادامه از چک‌پوینت ({len(done_uploads)} فایل قبلاً آپلود شده)" if done_uploads else ""))
            reporter, _ms = self._make_reporter(chat_id, "انتشار", len(payload["stickers"]))
            def _index_of(file_name: str) -> int:
                m = re.search(r"_(\d{3})_", file_name)
                return int(m.group(1)) if m else 0
            uploaded = []
            for pos, entry in enumerate(payload["stickers"], start=1):
                if self.queue.is_cancelled(job["id"]):
                    self.send(chat_id, "انتشار لغو شد.")
                    return "publish cancelled"
                idx = _index_of(entry["file_name"])
                prior = (checkpoint.get("stickers") or {}).get(str(idx)) or {}
                if idx in done_uploads and prior.get("file_id"):
                    uploaded.append({"sticker": prior["file_id"], "format": entry["format"],
                                     "emoji_list": entry["emoji_list"], "_index": idx})
                    if reporter is not None:
                        reporter.update(pos, len(payload["stickers"]))
                    continue
                path = os.path.join(final_dir, entry["file_name"])
                # Telegram rate-limits rapid uploads (we hit 429 after a
                # burst); space uploads out and respect retry_after.
                result = None
                for attempt in range(3):
                    try:
                        result = self.api.upload_sticker_file(user_id, path, entry["format"])
                        break
                    except Exception as exc:
                        import re as _re
                        m = _re.search(r"retry after (\d+)", str(exc))
                        if m and attempt < 2:
                            time.sleep(int(m.group(1)) + 5)
                            continue
                        # v4: name the failing file instead of a generic error.
                        raise RuntimeError(f"آپلود فایل {entry['file_name']} ناموفق بود: {exc}") from exc
                time.sleep(3)
                file_id = result.get("file_id") if isinstance(result, dict) else None
                if not file_id:
                    raise RuntimeError("uploadStickerFile شناسه فایل برنگرداند.")
                checkpoint_uploaded(checkpoint, idx, entry["file_name"], file_id)
                save_checkpoint(root, checkpoint)
                uploaded.append({"sticker": file_id, "format": entry["format"],
                                 "emoji_list": entry["emoji_list"], "_index": idx})
                if reporter is not None:
                    reporter.update(pos, len(payload["stickers"]))
            initial = uploaded[:50]
            if not resumed:
                self.api.create_new_sticker_set(
                    user_id, set_name, title,
                    [{k: v for k, v in s.items() if not k.startswith("_")}
                     for s in initial],
                    sticker_type="custom_emoji",
                    sticker_format=payload["format"])
                checkpoint["set_name"] = set_name
                checkpoint["set_created"] = True
                checkpoint_published(checkpoint, [s["_index"] for s in initial])
                save_checkpoint(root, checkpoint)
            already_published = checkpoint_published_indices(checkpoint)
            for sticker in uploaded[50:]:
                if sticker["_index"] in already_published:
                    continue
                self.api.add_sticker_to_set(
                    user_id, set_name,
                    {k: v for k, v in sticker.items() if not k.startswith("_")})
                checkpoint_published(checkpoint, [sticker["_index"]])
                save_checkpoint(root, checkpoint)
            link = set_link(set_name, "custom_emoji")
            self.send(chat_id,
                      f"{pe('publish')} پک جدید منتشر شد!\n"
                      f"لینک: {html.escape(link)}\n"
                      "پک اصلی شما دست نخورده است.\n"
                      f"ساخته‌شده با {html.escape(BOT_BRAND)}",
                      parse_mode="HTML")
            return f"published {set_name} ({len(uploaded)} stickers)"
        except Exception as exc:
            msg = redact(str(exc), self.config.token)
            self.send(chat_id,
                      f"❌ انتشار با API تلگرام ناموفق بود: {msg[:400]}\n\n"
                      "مسیر جایگزین: با دکمه «خروجی ZIP» فایل ZIP را بگیرید و در ادیتور داخل اپ تلگرام "
                      "یک پک جدید بسازید و فایل‌ها را دستی اضافه کنید. پک اصلی دست نخورده است.")
            return f"publish failed: {msg[:200]}"

    def publish_single_file(self, user_id: int, chat_id: int, project_id: str,
                            file_name: str, emoji: str = "🙂", set_slug: str = ""):
        """Repair route (v6 plan, wired in v9): publish ONE fixed file
        as its own new set — used when a full publish failed on a
        single file. The v6 validator gate runs BEFORE any upload:
        a blocked TGS never reaches Telegram. Upload spacing and
        retry_after are honoured exactly like the full publish."""
        from emoji_forge.publish import (
            FORMAT_BY_EXT, build_set_name, set_link, validate_before_upload,
        )
        root = project_dir(self.config.data_dir, project_id)
        final_dir = os.path.join(root, "final")
        path = os.path.join(final_dir, file_name)
        if not os.path.isfile(path):
            self.send(chat_id, f"فایل {file_name} در خروجی‌های نهایی پیدا نشد.")
            return f"single publish missing file: {file_name}"
        if file_name.lower().endswith(".tgs"):
            blocked = validate_before_upload([file_name], final_dir)
            if blocked:
                idx, name, issues = blocked[0]
                self.send(chat_id,
                          f"❌ انتشار تکی متوقف شد — اعتبارسنجی فایل {name} را رد کرد:\n"
                          + "; ".join(issues)[:300])
                return f"single publish blocked by validator: {name}: {issues[0][:120]}"
        try:
            fmt = FORMAT_BY_EXT[os.path.splitext(file_name)[1].lower()]
            me = self.api.get_me()
            slug = set_slug or f"{project_id}-fix-{os.path.splitext(file_name)[0][-12:]}"
            set_name = build_set_name(slug, me.get("username", ""))
            title = publish_title("", project_id)
            result = None
            for attempt in range(3):
                try:
                    result = self.api.upload_sticker_file(user_id, path, fmt)
                    break
                except Exception as exc:
                    import re as _re
                    m = _re.search(r"retry after (\d+)", str(exc))
                    if m and attempt < 2:
                        time.sleep(int(m.group(1)) + 5)
                        continue
                    raise
            time.sleep(3)
            file_id = result.get("file_id") if isinstance(result, dict) else None
            if not file_id:
                raise RuntimeError("uploadStickerFile شناسه فایل برنگرداند.")
            self.api.create_new_sticker_set(
                user_id, set_name, title,
                [{"sticker": file_id, "format": fmt, "emoji_list": [emoji]}],
                sticker_type="custom_emoji", sticker_format=fmt)
            link = set_link(set_name, "custom_emoji")
            self.send(chat_id, f"✅ فایل {file_name} به‌صورت ست جدا منتشر شد!\nلینک: {link}\nساخته‌شده با {BOT_BRAND}")
            return f"published {set_name} (1 stickers)"
        except Exception as exc:
            msg = redact(str(exc), self.config.token)
            self.send(chat_id, f"❌ انتشار تکی ناموفق بود: {msg[:400]}")
            return f"single publish failed: {msg[:200]}"

    # -- single-emoji editing (v10) -------------------------------------
    def _send_single_menu(self, sess: Session, chat_id: int):
        """The action menu for the pending single emoji, showing the
        current profile values (colour/font/logo) inline."""
        has_logo = bool(sess.single_logo_data or sess.logo_data)
        if not has_logo and sess.project_id:
            has_logo = os.path.exists(
                os.path.join(project_dir(self.config.data_dir, sess.project_id), "logo.png"))
        studio_extra = {"mode": "single",
                        "name": sess.brand.brand_name,
                        "color": sess.brand.primary_hex,
                        "font": sess.font_id}
        self.send(chat_id,
                  f"{pe('edit')} ایموجی دریافت شد. چه کاری رویش انجام شود؟\n"
                  f"نام برند فعلی: {html.escape(sess.brand.brand_name or '—')} — "
                  f"رنگ: {html.escape(sess.brand.primary_hex)}\n"
                  "رنگ، فونت و لوگو از پروفایل برند شما خوانده می‌شود؛ "
                  "از دکمه‌های پایین می‌توانید همین‌جا عوضشان کنید.",
                  single_action_keyboard(
                      sess.brand, font_label(sess.font_id, load_fonts()), has_logo,
                      studio_extra=studio_extra),
                  parse_mode="HTML")

    def _single_intake(self, sess: Session, chat_id: int, user_id: int, msg: dict) -> bool:
        """While awaiting a single emoji, consume a TGS document, an
        animated sticker or a custom-emoji message as THE single
        source. Returns True when the message was consumed. Video/
        static inputs get the honest TGS-only reply; anything else
        falls through to the normal pack flow."""
        if sess.awaiting != "single_emoji":
            return False
        st = msg.get("sticker")
        doc = msg.get("document")
        ids = extract_custom_emoji_ids({"message": msg})
        if not (st or doc or ids):
            if msg.get("text"):
                self.send(chat_id, "در حالت ویرایش تکی هستید؛ یک ایموجی سفارشی، "
                                   "استیکر متحرک یا فایل TGS بفرستید — یا «لغو» را بزنید.",
                          {"inline_keyboard": [[btn("❌ لغو", callback_data="single:cancel", style=DANGER, icon="cross")]]})
                return True
            return False
        try:
            if ids:
                stickers = self.api.get_custom_emoji_stickers(ids[:1])
                if not stickers:
                    self.send(chat_id, "ایموجی سفارشی شناخته نشد؛ دوباره بفرستید.")
                    return True
                st0 = stickers[0]
                if st0.get("is_video"):
                    self.send(chat_id, "ویرایش تکی فعلاً فقط برای ایموجی متحرک (TGS) است؛ "
                                       "ایموجی ویدیویی (WEBM) را نمی‌شود تکی ویرایش کرد.")
                    return True
                if not st0.get("is_animated"):
                    self.send(chat_id, "ویرایش تکی فقط برای ایموجی متحرک (TGS) است؛ "
                                       "این ایموجی ثابت است.")
                    return True
                info = self.api.get_file(st0.get("file_id", ""))
                data = self.api.download_file(info["file_path"])
                emoji = st0.get("emoji", "") or "🙂"
            elif st is not None:
                if st.get("is_video"):
                    self.send(chat_id, "ویرایش تکی فعلاً فقط برای ایموجی متحرک (TGS) است؛ "
                                       "استیکر ویدیویی (WEBM) را نمی‌شود تکی ویرایش کرد.")
                    return True
                if not st.get("is_animated"):
                    self.send(chat_id, "ویرایش تکی فقط برای ایموجی متحرک (TGS) است؛ "
                                       "این استیکر ثابت است.")
                    return True
                info = self.api.get_file(st.get("file_id", ""))
                data = self.api.download_file(info["file_path"])
                emoji = st.get("emoji", "") or "🙂"
            else:
                name = (doc.get("file_name") or "").lower()
                if name.endswith(".webm"):
                    self.send(chat_id, "ویرایش تکی فعلاً فقط برای فایل TGS است؛ "
                                       "فایل WEBM را نمی‌شود تکی ویرایش کرد.")
                    return True
                if not name.endswith(".tgs"):
                    return False  # not an emoji file: normal flow decides
                info = self.api.get_file(doc.get("file_id", ""))
                data = self.api.download_file(info["file_path"])
                _ext, kind = validate_upload(data, doc.get("file_name") or "single.tgs",
                                             self.config.max_file_bytes)
                if kind != "animated":
                    self.send(chat_id, "این فایل TGS معتبر نیست.")
                    return True
                emoji = "🙂"
        except Exception as exc:
            self.send(chat_id, f"دریافت ایموجی ناموفق بود: {redact(str(exc), self.config.token)[:300]}")
            return True
        sess.awaiting = ""
        sess.single = {"data": data, "emoji": emoji, "file_name": "single.tgs"}
        self._send_single_menu(sess, chat_id)
        return True

    def _start_single_action(self, sess: Session, chat_id: int, user_id: int, action: str):
        """Fire one single edit: missing name/logo are requested first
        (the flow resumes here afterwards), then the quota gate, then
        the job is queued on the SAME worker queue as pack jobs, so
        polling is never blocked and the font override cannot race."""
        if not sess.single:
            sess.awaiting = "single_emoji"
            self.send(chat_id, "اول یک ایموجی بفرستید: ایموجی سفارشی، استیکر متحرک یا فایل TGS.")
            return
        sess.single_action = action
        if action in ("name", "all") and not sess.brand.brand_name:
            sess.awaiting = "single_name"
            self.send(chat_id, "نام برند شما هنوز ثبت نشده است؛ نام برند را به‌صورت متن بفرستید.")
            return
        logo_bytes = sess.single_logo_data or sess.logo_data
        if action in ("logo", "all") and not logo_bytes:
            pid0 = sess.project_id
            has_file = bool(pid0) and os.path.exists(
                os.path.join(project_dir(self.config.data_dir, pid0), "logo.png"))
            if not has_file:
                sess.awaiting = "single_logo"
                self.send(chat_id, "برای این عملیات لوگو لازم است؛ لوگو را به‌صورت فایل (PNG شفاف) بفرستید.")
                return
        if not self._quota_gate(user_id, chat_id, 1):
            return
        pid = sess.ensure_project()
        root = project_dir(self.config.data_dir, pid)
        single_dir = os.path.join(root, "single")
        os.makedirs(single_dir, exist_ok=True)
        src_path = os.path.join(single_dir, f"single_{int(time.time())}.tgs")
        with open(src_path, "wb") as fh:
            fh.write(sess.single["data"])
        if action in ("logo", "all") and logo_bytes:
            with open(os.path.join(root, "logo.png"), "wb") as fh:
                fh.write(logo_bytes)
        job_id = f"{pid}-single-{int(time.time())}"
        sess.last_job_id = job_id
        self.queue.enqueue({"id": job_id, "kind": "single",
                            "user_id": user_id, "chat_id": chat_id,
                            "project_id": pid, "src_path": src_path,
                            "action": action, "brand": sess.brand.to_dict(),
                            "font_id": sess.font_id,
                            "emoji": sess.single.get("emoji") or "🙂"})
        sess.single = None
        sess.single_action = ""
        self.send(chat_id, "⏳ ویرایش ایموجی تکی در صف قرار گرفت؛ "
                           "تمام که شد، لینک ست شخصی و پیش‌نمایش را همین‌جا می‌فرستم.")

    def deliver_single_to_set(self, user_id: int, chat_id: int, path: str,
                              emoji: str = "🙂") -> dict:
        """Add one processed TGS to the user's ONE personal singles
        set (tzs_<user_id>_by_<bot>), creating it on first use. When
        Telegram rejects a candidate name (STICKERSET_INVALID /
        occupied), the next candidate is tried and the winning name
        is stored in the user profile. Returns
        {"link", "file_id", "set_name"}."""
        from emoji_forge.publish import set_link
        me = self.api.get_me()
        username = me.get("username", "")
        stored = (self.registry.get(user_id) or {}).get("single_set_name") or ""
        candidates = single_set_candidates(user_id, username, stored)
        uploaded = self.api.upload_sticker_file(user_id, path, "animated")
        file_id = uploaded.get("file_id") if isinstance(uploaded, dict) else None
        if not file_id:
            raise RuntimeError("uploadStickerFile شناسه فایل برنگرداند.")
        sticker = {"sticker": file_id, "format": "animated", "emoji_list": [emoji]}
        last_error = None
        for name in candidates:
            try:
                self.api.get_sticker_set(name)
                exists = True
            except Exception:
                exists = False
            try:
                if exists:
                    self.api.add_sticker_to_set(user_id, name, sticker)
                else:
                    self.api.create_new_sticker_set(
                        user_id, name, SINGLES_SET_TITLE, [sticker],
                        sticker_type="custom_emoji", sticker_format="animated")
            except Exception as exc:
                if any(marker in str(exc) for marker in SET_NAME_ERROR_MARKERS):
                    last_error = exc
                    continue
                raise
            if name != stored:
                self.registry.update_profile(user_id, {"single_set_name": name})
                if user_id in self.sessions:
                    self.sessions[user_id].single_set_name = name
            return {"link": set_link(name, "custom_emoji"),
                    "file_id": file_id, "set_name": name}
        raise RuntimeError(f"ساخت ست شخصی با هیچ نامی ممکن نشد: {last_error}")

    def _run_single_job(self, job: dict) -> str:
        """Worker for one single edit: process -> validate -> deliver
        into the personal set -> link + sticker preview copy."""
        from emoji_forge.singles import process_single
        from emoji_forge.validate_tgs import validate_tgs_bytes
        chat_id = job["chat_id"]
        user_id = job["user_id"]
        try:
            with open(job["src_path"], "rb") as fh:
                data = fh.read()
            brand = BrandProfile.from_dict(job["brand"])
            logo_data = None
            if job["action"] in ("logo", "all"):
                logo_path = os.path.join(
                    project_dir(self.config.data_dir, job["project_id"]), "logo.png")
                if os.path.exists(logo_path):
                    with open(logo_path, "rb") as fh:
                        logo_data = fh.read()
            out, _warnings = process_single(
                data, brand, job["action"],
                font_id=job.get("font_id") or None, logo_data=logo_data)
            # v10 delivery gate: the importer guard runs over EVERY
            # output here too (even an engine that bypassed its own
            # guard), then validation decides delivery.
            try:
                from emoji_forge.animation_engine import dump_tgs as _dg, load_tgs as _lg
                from emoji_forge.importer_guard import fix_importer_risks as _fix
                _doc, _rep = _fix(_lg(out))
                if _rep["tr_added"] or _rep["paint_stripped"] or _rep["vertices_removed"]:
                    out = _dg(_doc)
            except Exception:
                pass  # unparseable output: validation below rejects it
            issues, _vw = validate_tgs_bytes(out, 1)
            if issues:
                self.send(chat_id, "❌ خروجی ویرایش تکی اعتبارسنجی نشد:\n"
                                   + "; ".join(issues)[:300])
                return f"single validation failed: {issues[0][:120]}"
            out_path = os.path.join(os.path.dirname(job["src_path"]),
                                    f"edited_{int(time.time())}.tgs")
            with open(out_path, "wb") as fh:
                fh.write(out)
            result = self.deliver_single_to_set(
                user_id, chat_id, out_path, job.get("emoji") or "🙂")
            self.send(chat_id, "✅ ایموجی ویرایش و به ست شخصی شما اضافه شد!\n"
                               f"لینک ست: {result['link']}")
            try:
                self.api.send_sticker(chat_id, result["file_id"])
            except Exception:
                pass  # preview copy is best-effort; the set link is the delivery
            return "single published"
        except Exception as exc:
            from emoji_forge.singles import LogoSlotNotFound
            if isinstance(exc, LogoSlotNotFound):
                # Never deliver a single whose logo did not actually
                # replace the old mark. Queue it for the bot's
                # advanced processing (internal queue, same as pack
                # advanced processing) and say so in bot voice.
                req_id = f"single-review-{job['project_id']}-{int(time.time())}"
                brain_dir = os.path.join(
                    self.config.data_dir, "brain", "requests")
                os.makedirs(brain_dir, exist_ok=True)
                write_json(os.path.join(brain_dir, f"{req_id}.json"), {
                    "id": req_id, "kind": "single_review",
                    "project_id": job["project_id"],
                    "user_id": user_id, "chat_id": chat_id,
                    "action": job["action"],
                    "src_path": job["src_path"],
                    "brand": job["brand"],
                    "instruction": (
                        "Single-emoji logo swap found no slot. Advanced "
                        "processing: locate the old mark, replace it with "
                        "the user's logo.png in the exact slot, then write "
                        "single_plan.json into the project dir with "
                        "{\"project_id\", \"user_id\", \"chat_id\", "
                        "\"file_path\" (the fixed .tgs), \"emoji\"} — the "
                        "bot delivers it into the user's personal set "
                        "automatically from that file."),
                })
                self.send(chat_id,
                          "⚠️ لوگوی قبلیِ این ایموجی دقیق پیدا نشد، برای همین "
                          "نتیجه‌ی ناقص تحویل ندادم.\n"
                          "⏳ ربات آن را با پردازش پیشرفته دوباره بررسی می‌کند؛ "
                          "بعد از اصلاح، نتیجه همین‌جا فرستاده می‌شود.")
                for owner_id in sorted(self.registry.owner_ids):
                    self.send(owner_id,
                              f"🛠 یک ویرایش تکی وارد پردازش پیشرفته ربات شد:\n"
                              f"پروژه: {job['project_id']}\nکاربر: {user_id}")
                return "single routed to advanced processing (no logo slot)"
            msg = redact(str(exc), self.config.token)
            self.send(chat_id, f"❌ ویرایش تکی ناموفق بود: {msg[:400]}")
            return f"single failed: {msg[:150]}"

    def _handle_single_callback(self, sess: Session, chat_id: int, user_id: int, data: str) -> bool:
        """All single:* callbacks. Returns True when handled."""
        if not data.startswith("single:"):
            return False
        parts = data.split(":")
        action = parts[1] if len(parts) > 1 else ""
        arg = parts[2] if len(parts) > 2 else ""
        if action == "noop":
            return True
        if action == "start":
            sess.single = None
            sess.single_action = ""
            sess.awaiting = "single_emoji"
            self.send(chat_id,
                      "✏️ ویرایش ایموجی تکی\n"
                      "یک ایموجی بفرستید: خودِ ایموجی سفارشی در پیام، یک استیکر متحرک، "
                      "یا فایل TGS. (فعلاً فقط TGS پشتیبانی می‌شود، نه WEBM.)",
                      {"inline_keyboard": [[btn("❌ لغو", callback_data="single:cancel", style=DANGER, icon="cross")]]})
        elif action == "cancel":
            sess.single = None
            sess.single_action = ""
            if sess.awaiting.startswith("single"):
                sess.awaiting = ""
            self.send(chat_id, "ویرایش تکی لغو شد.", main_menu(is_admin=self._is_admin(user_id)))
        elif action == "menu":
            if sess.single:
                self._send_single_menu(sess, chat_id)
            else:
                self.send(chat_id, "ایموجی‌ای در انتظار ویرایش نیست؛ از «✏️ ویرایش ایموجی تکی» شروع کنید.",
                          main_menu(is_admin=self._is_admin(user_id)))
        elif action == "colors":
            page = int(arg) if arg.isdigit() else 0
            colors = flatten_palette(load_palette())
            self.send(chat_id, "رنگ اصلی خود را انتخاب کنید (برای ایموجی تکی و پروفایل برندتان):",
                      build_color_keyboard(colors, page))
        elif action == "color":
            try:
                sess.brand.primary_hex = validate_hex(arg)
                self._save_user_state(sess)
                self.send(chat_id, f"رنگ اصلی: {sess.brand.primary_hex}")
            except ValueError as exc:
                self.send(chat_id, str(exc))
            if sess.single:
                self._send_single_menu(sess, chat_id)
        elif action == "colorhex":
            sess.awaiting = "single_color_hex"
            self.send(chat_id, "کد رنگ دلخواه را به‌صورت متن بفرستید، مثل #FF0000.")
        elif action == "gradients":
            self.send(chat_id, "یک گرادیان آماده انتخاب کنید (رنگ اصلی و دوم برندتان از آن ست می‌شود):",
                      build_gradient_keyboard(load_gradients(), 0))
        elif action == "grads":
            page = int(arg) if arg.isdigit() else 0
            self.send(chat_id, "یک گرادیان آماده انتخاب کنید:",
                      build_gradient_keyboard(load_gradients(), page))
        elif action == "grad":
            from emoji_forge.registry import gradient_by_id
            grad = gradient_by_id(arg)
            if grad:
                sess.brand.primary_hex = grad["color1"]
                sess.brand.secondary_hex = grad["color2"]
                sess.brand.gradient_enabled = True
                self._save_user_state(sess)
                self.send(chat_id, f"گرادیان «{grad['fa']}» فعال شد — رنگ اصلی: {grad['color1']}، رنگ دوم: {grad['color2']}")
            if sess.single:
                self._send_single_menu(sess, chat_id)
        elif action == "fonts":
            page = int(arg) if arg.isdigit() else 0
            self.send(chat_id, "فونت نام برند را انتخاب کنید:",
                      build_font_keyboard(load_fonts(), page))
        elif action == "font":
            sess.font_id = arg
            self._save_user_state(sess)
            self.send(chat_id, f"فونت انتخاب شد: {font_label(arg, load_fonts())}")
            if sess.single:
                self._send_single_menu(sess, chat_id)
        elif action == "logo":
            sess.awaiting = "single_logo"
            self.send(chat_id, "لوگو را به‌صورت فایل (PNG شفاف) بفرستید.")
        elif action == "act" and arg in ("color", "name", "logo", "all"):
            self._start_single_action(sess, chat_id, user_id, arg)
        return True

    def _handle_web_app_data(self, sess: Session, chat_id: int, user_id: int, wad: dict):
        """Web-app sendData payloads (dormant: no button in the bot
        currently opens a web app, so this cannot be triggered from
        the UI). Fields name/color/font_id/logo_rect are applied to
        the user's profile (and thereby to the session); mode
        "single" then continues the single-emoji flow. A payload
        naming project_id/pid is also routed back onto that project
        when it is the user's own (see _apply_studio_project).
        Payloads without a mode keep the pack-flow behaviour:
        apply + confirm."""
        try:
            payload = json.loads(wad.get("data") or "{}")
        except Exception:
            payload = {}
        if not isinstance(payload, dict):
            payload = {}
        name = payload.get("name") or payload.get("brand_name")
        if name:
            sess.brand.brand_name = str(name)[:64]
        color = payload.get("color") or payload.get("primary_hex") or payload.get("primary")
        if color:
            try:
                sess.brand.primary_hex = validate_hex(str(color))
            except ValueError:
                pass
        if payload.get("gradient_id"):
            try:
                from emoji_forge.registry import gradient_by_id as _gbid
                _grad = _gbid(str(payload["gradient_id"]))
                if _grad:
                    sess.brand.primary_hex = _grad["color1"]
                    sess.brand.secondary_hex = _grad["color2"]
                    sess.brand.gradient_enabled = True
            except Exception:
                pass
        color2 = payload.get("color2") or payload.get("secondary_hex") or payload.get("secondary")
        if color2:
            try:
                sess.brand.secondary_hex = validate_hex(str(color2))
            except ValueError:
                pass
        if payload.get("font_id"):
            sess.font_id = str(payload["font_id"])
        if payload.get("logo_rect"):
            sess.logo_rect = payload["logo_rect"]
        self._save_user_state(sess)
        # Continuity: a payload that names a project (project_id/pid)
        # lands on that project too — the session brand/font above
        # are already the session-side application of it.
        project_state = self._apply_studio_project(sess, user_id, payload)
        if project_state == "applied":
            self.send(chat_id, "🎨 تنظیمات روی پروژه‌ی فعلی‌تان اعمال شد.",
                      main_menu(is_admin=self._is_admin(user_id)))
        elif payload.get("mode") == "single":
            if sess.single:
                self._send_single_menu(sess, chat_id)
            else:
                sess.awaiting = "single_emoji"
                self.send(chat_id, "تنظیمات دریافت شد. حالا یک ایموجی بفرستید "
                                   "تا ویرایش تکی رویش انجام شود.")
        else:
            self.send(chat_id, "✅ تنظیمات روی پروفایل برند شما اعمال شد.",
                      main_menu(is_admin=self._is_admin(user_id)))
        if project_state == "mismatch":
            self.send(chat_id, "⚠️ پروژه‌ی انتخاب‌شده با پروژه‌ی فعلی شما یکی نیست؛ "
                               "تنظیمات فقط روی پروفایل برند اعمال شد و پروژه دست نخورد.")

    def _apply_studio_project(self, sess: Session, user_id: int, payload: dict) -> str:
        """Route a web-app payload back onto the bot project it came
        from. Returns "applied", "mismatch", or "" when the payload
        names no project. The named project counts as the user's own
        when it is the session's current project, or when the
        project's review_pending.json names this user. On a match
        the FULL payload is written to <project>/studio_settings.json
        so the advanced-processing flow can honour logo_rect etc.;
        the session brand/font are updated by the caller."""
        project_id = payload.get("project_id") or payload.get("pid")
        if not project_id:
            return ""
        project_id = str(project_id)
        if not re.fullmatch(r"[A-Za-z0-9-]+", project_id):
            return "mismatch"
        matched = bool(sess.project_id) and sess.project_id == project_id
        if not matched:
            try:
                root = os.path.join(self.config.data_dir, "projects", project_id)
                pending = read_json(os.path.join(root, "review_pending.json"),
                                    default={}) or {}
                matched = pending.get("user_id") == user_id
            except Exception:
                matched = False
        if not matched:
            return "mismatch"
        try:
            root = project_dir(self.config.data_dir, project_id)
            write_json(os.path.join(root, "studio_settings.json"), payload)
        except Exception:
            return "mismatch"
        return "applied"

    # -- message handling ------------------------------------------------
    def handle_message(self, msg: dict):
        chat = msg.get("chat") or {}
        chat_id = chat.get("id")
        user = msg.get("from") or {}
        user_id = user.get("id")
        if chat_id is None or user_id is None:
            return
        sess = self.session(user_id)
        # v9 access gate: register the user (new users become pending
        # and the owner is notified once); non-approved users get only
        # the short status message — no feature runs for them.
        _rec, _is_new = self._register_user(user_id, user)
        if not self.registry.is_allowed(user_id):
            text0 = (msg.get("text") or "").strip().lower()
            if text0.startswith("/start") and _is_new:
                self.send(chat_id, pending_welcome_text())
            else:
                self.send(chat_id, access_status_message(self.registry.status_of(user_id)))
            return
        if msg.get("web_app_data"):
            self._handle_web_app_data(sess, chat_id, user_id, msg["web_app_data"])
            return
        text = msg.get("text") or ""
        if text and not text.startswith("/"):
            if sess.awaiting.startswith("admin_quota:"):
                target = int(sess.awaiting.split(":", 1)[1])
                fields = text.split()
                try:
                    packs, emojis, per_day = int(fields[0]), int(fields[1]), int(fields[2])
                    self.registry.set_quota(target, {"packs_total": packs,
                                                     "emojis_total": emojis,
                                                     "packs_per_day": per_day})
                    sess.awaiting = ""
                    self.send(chat_id, f"✅ سهمیه کاربر {target} ثبت شد.")
                    self._send_user_detail(chat_id, target)
                except (ValueError, IndexError):
                    self.send(chat_id, "سه عدد با فاصله بفرستید: «پک کل ایموجی کل پک روزانه» — مثل: 5 200 2")
                return
            if sess.awaiting == "brand_name":
                sess.awaiting = ""
                sess.brand.brand_name = text.strip()
                self._save_user_state(sess)
                self.send(chat_id, f"نام برند ثبت شد: {sess.brand.brand_name}", main_menu())
                return
            if sess.awaiting == "watermark":
                sess.awaiting = ""
                sess.brand.watermark_text = text.strip()
                self._save_user_state(sess)
                self.send(chat_id, f"واترمارک ثبت شد: {sess.brand.watermark_text or '—'}", main_menu())
                return
            if sess.awaiting == "single_color_hex":
                try:
                    sess.brand.primary_hex = validate_hex(text.strip())
                    sess.awaiting = ""
                    self._save_user_state(sess)
                    self.send(chat_id, f"رنگ اصلی: {sess.brand.primary_hex}")
                    if sess.single:
                        self._send_single_menu(sess, chat_id)
                    else:
                        self.send(chat_id, "رنگ ثبت شد.", main_menu(is_admin=self._is_admin(user_id)))
                except ValueError as exc:
                    self.send(chat_id, str(exc) + " — دوباره با فرمت #RRGGBB بفرستید.")
                return
            if sess.awaiting == "single_name":
                sess.awaiting = ""
                sess.brand.brand_name = text.strip()
                self._save_user_state(sess)
                self.send(chat_id, f"نام برند ثبت شد: {sess.brand.brand_name}")
                if sess.single_action:
                    self._start_single_action(sess, chat_id, user_id, sess.single_action)
                elif sess.single:
                    self._send_single_menu(sess, chat_id)
                return
            if sess.awaiting == "brand_color":
                try:
                    sess.brand.primary_hex = validate_hex(text.strip())
                    sess.awaiting = ""
                    self._save_user_state(sess)
                    self.send(chat_id, f"رنگ اصلی: {sess.brand.primary_hex}", main_menu())
                except ValueError as exc:
                    self.send(chat_id, str(exc) + " — دوباره با فرمت #RRGGBB بفرستید.")
                return
            if sess.awaiting == "spectrum_color":
                try:
                    sess.brand.primary_hex = validate_hex(text.strip())
                    sess.brand.gradient_enabled = True
                    sess.awaiting = ""
                    self._save_user_state(sess)
                    from emoji_forge.image_engine import gradient_palette_hex
                    shades = ", ".join(gradient_palette_hex(sess.brand.primary_hex))
                    self.send(chat_id, f"طیف فعال شد — رنگ پایه: {sess.brand.primary_hex}\nدرجه‌ها: {shades}", main_menu())
                except ValueError as exc:
                    self.send(chat_id, str(exc) + " — دوباره با فرمت #RRGGBB بفرستید.")
                return
        if text.startswith("/"):
            self.handle_command(sess, chat_id, user_id, text)
            return
        if self._single_intake(sess, chat_id, user_id, msg):
            return
        link = parse_pack_link(text) if text else None
        if link:
            sess.ensure_project()
            try:
                sticker_set = self.api.get_sticker_set(link["name"])
                manifest = manifest_from_sticker_set(sticker_set, sess.project_id)
                write_json(os.path.join(project_dir(self.config.data_dir, sess.project_id), "manifest.json"), manifest)
                self._ask_ownership(sess, chat_id, manifest)
            except Exception as exc:
                self.send(chat_id, f"پک پیدا یا دانلود نشد: {redact(str(exc), self.config.token)[:300]}\n"
                                  "مسیر جایگزین: فایل‌های پک را مستقیم به‌صورت فایل بفرستید.")
            return
        ids = extract_custom_emoji_ids({"message": msg})
        if ids:
            sess.ensure_project()
            try:
                stickers = self.api.get_custom_emoji_stickers(ids[:200])
            except Exception as exc:
                self.send(chat_id, f"اطلاعات ایموجی گرفته نشد: {redact(str(exc), self.config.token)[:300]}")
                return
            if not stickers:
                self.send(chat_id, "ایموجی سفارشی شناخته نشد.")
                return
            set_name = stickers[0].get("set_name", "")
            if set_name:
                try:
                    sticker_set = self.api.get_sticker_set(set_name)
                    manifest = manifest_from_sticker_set(sticker_set, sess.project_id)
                    write_json(os.path.join(project_dir(self.config.data_dir, sess.project_id), "manifest.json"), manifest)
                    self._ask_ownership(sess, chat_id, manifest)
                    return
                except Exception as exc:
                    self.send(chat_id, f"پک کامل دانلود نشد ({redact(str(exc), self.config.token)[:200]}). فقط همان ایموجی گرفته می‌شود.")
            # Fallback: only the sent emoji itself.
            sess.manifest = {"project_id": sess.project_id, "set_name": set_name,
                             "set_title": "", "sticker_type": "custom_emoji",
                             "count": len(stickers), "items": []}
            for idx, st in enumerate(stickers, start=1):
                item = {"index": idx, "file_id": st.get("file_id", ""), "file_unique_id": st.get("file_unique_id", ""),
                        "custom_emoji_id": st.get("custom_emoji_id", ""), "kind": "animated" if st.get("is_animated") else ("video" if st.get("is_video") else "static"),
                        "extension_hint": ".tgs" if st.get("is_animated") else (".webm" if st.get("is_video") else ".webp"),
                        "emoji": st.get("emoji", ""), "width": st.get("width"), "height": st.get("height"),
                        "file_size": st.get("file_size"), "set_name": set_name, "status": "listed", "local_path": ""}
                sess.manifest["items"].append(item)
            self.send(chat_id, "نام پک در پاسخ تلگرام نبود؛ فقط همین ایموجی(ها) گرفته می‌شود. برای پک کامل، لینک t.me/addemoji/... را بفرستید.")
            self._download_manifest_items(sess, chat_id)
            return
        if msg.get("sticker"):
            st = msg["sticker"]
            sess.ensure_project()
            set_name = st.get("set_name", "")
            if set_name:
                try:
                    sticker_set = self.api.get_sticker_set(set_name)
                    manifest = manifest_from_sticker_set(sticker_set, sess.project_id)
                    self._ask_ownership(sess, chat_id, manifest)
                    return
                except Exception as exc:
                    self.send(chat_id, f"پک کامل دانلود نشد: {redact(str(exc), self.config.token)[:200]}")
            sess.manifest = {"project_id": sess.project_id, "set_name": set_name, "set_title": "",
                             "sticker_type": "", "count": 1, "items": [{
                                 "index": 1, "file_id": st.get("file_id", ""), "file_unique_id": st.get("file_unique_id", ""),
                                 "custom_emoji_id": "", "kind": "animated" if st.get("is_animated") else ("video" if st.get("is_video") else "static"),
                                 "extension_hint": ".tgs" if st.get("is_animated") else (".webm" if st.get("is_video") else ".webp"),
                                 "emoji": st.get("emoji", ""), "width": st.get("width"), "height": st.get("height"),
                                 "file_size": st.get("file_size"), "set_name": set_name, "status": "listed", "local_path": ""}]}
            self._download_manifest_items(sess, chat_id)
            return
        # Direct file / photo uploads.
        file_id = None
        file_name = "upload"
        if msg.get("document"):
            file_id = msg["document"].get("file_id")
            file_name = msg["document"].get("file_name") or "upload"
        elif msg.get("photo"):
            photos = msg["photo"]
            file_id = photos[-1].get("file_id")
            file_name = "photo.png"
        if file_id:
            try:
                info = self.api.get_file(file_id)
                data = self.api.download_file(info["file_path"])
                if sess.awaiting in ("brand_logo", "single_logo"):
                    was_single = sess.awaiting == "single_logo"
                    sess.awaiting = ""
                    sess.logo_data = data
                    if was_single:
                        sess.single_logo_data = data
                    sess.brand.logo_path = "uploaded-logo"
                    # Persist the logo inside the project dir so a queued
                    # job can use it even if the session object is gone.
                    try:
                        root = project_dir(self.config.data_dir, sess.ensure_project())
                        with open(os.path.join(root, "logo.png"), "wb") as fh:
                            fh.write(data)
                    except Exception:
                        pass
                    if was_single:
                        self.send(chat_id, "لوگوی ایموجی تکی دریافت شد.")
                        if sess.single_action:
                            self._start_single_action(sess, chat_id, user_id, sess.single_action)
                        elif sess.single:
                            self._send_single_menu(sess, chat_id)
                        return
                    self.send(chat_id, "لوگو دریافت شد و در پروفایل برند ذخیره شد.")
                    return
                item = self._save_upload(sess, data, file_name, "upload")
                if sess.manifest is None:
                    sess.manifest = {"project_id": sess.project_id, "set_name": "", "set_title": "",
                                     "sticker_type": "", "count": 0, "items": []}
                sess.manifest["items"].append(item)
                sess.manifest["count"] = len(sess.manifest["items"])
                self.send(chat_id, f"فایل دریافت و ذخیره شد: {item['local_path'].split('/')[-1]} — حالا از دکمه‌ها «پیش‌نمایش» را بزنید.", main_menu())
            except Exception as exc:
                self.send(chat_id, f"فایل پذیرفته نشد: {redact(str(exc), self.config.token)[:300]}")
            return
        if text:
            self.send(chat_id, "پیام دریافت شد. یک ایموجی، استیکر، لینک پک یا فایل بفرستید، یا از دکمه‌ها استفاده کنید.", main_menu())

    def handle_callback(self, query: dict):
        data = query.get("data", "")
        chat_id = (query.get("message") or {}).get("chat", {}).get("id")
        user = query.get("from") or {}
        user_id = user.get("id")
        if chat_id is None or user_id is None:
            return
        # Premium-UI demo buttons (Bot API 9.4 visual proof message):
        # harmless no-op callbacks that only acknowledge the tap.
        if data.startswith("ui_demo:"):
            try:
                self.api.answer_callback(query.get("id", ""), "نمایش ظاهر تازه ✅")
            except Exception:
                pass
            return
        # v9: admin callbacks are owner-only and are routed BEFORE the
        # generic gate/answer (a non-owner gets a refusal answer).
        if data.startswith("admin:"):
            self._handle_admin_callback(query, data, chat_id, user_id)
            return
        try:
            self.api.answer_callback(query.get("id", ""))
        except Exception:
            pass
        # v9 access gate: same rule as for messages.
        self._register_user(user_id, user)
        if not self.registry.is_allowed(user_id):
            self.send(chat_id, access_status_message(self.registry.status_of(user_id)))
            return
        sess = self.session(user_id)
        if data.startswith("single:"):
            self._handle_single_callback(sess, chat_id, user_id, data)
            return
        if data == "packs:mine":
            self._send_my_packs(chat_id, user_id)
            return
        if data == "menu:gradients":
            from emoji_forge.registry import gradients as _gradients
            rows = [[{"text": f"{g['fa']} {g['color1']} ← {g['color2']}",
                      "callback_data": f"gradient:{g['id']}"}]
                    for g in _gradients()]
            rows.append([btn("⬅️ بازگشت", callback_data="menu:brand", icon="back")])
            self.send(chat_id,
                      "یک گرادیان آماده انتخاب کنید؛ رنگ اصلی و دوم برندتان از آن ست می‌شود و طیف رنگی فعال می‌شود:",
                      {"inline_keyboard": rows})
            return
        if data.startswith("gradient:"):
            from emoji_forge.registry import gradient_by_id as _gradient_by_id
            grad = _gradient_by_id(data.split(":", 1)[1])
            if grad:
                sess.brand.primary_hex = grad["color1"]
                sess.brand.secondary_hex = grad["color2"]
                sess.brand.gradient_enabled = True
                self._save_user_state(sess)
                self.send(chat_id,
                          f"گرادیان «{grad['fa']}» فعال شد — رنگ اصلی: {grad['color1']}، رنگ دوم: {grad['color2']}",
                          main_menu(is_admin=self._is_admin(user_id)))
            return
        if data == "menu:main":
            self.send(chat_id,
                      f"{pe('home')} منوی اصلی — حالت فعلی: "
                      + html.escape(sess.mode),
                      main_menu(is_admin=self._is_admin(user_id)),
                      parse_mode="HTML")
        elif data == "menu:spectrum":
            rows = []
            row = []
            for hex_code, label in [("#FF0000", "قرمز"), ("#00FF00", "سبز"), ("#0000FF", "آبی"),
                                    ("#FF8000", "نارنجی"), ("#FF00FF", "صورتی"), ("#8000FF", "بنفش"),
                                    ("#00FFFF", "فیروزه‌ای"), ("#FFFF00", "زرد")]:
                row.append({"text": label, "callback_data": f"spectrum:{hex_code}"})
                if len(row) == 4:
                    rows.append(row)
                    row = []
            if row:
                rows.append(row)
            rows.append([btn("🚫 خاموش کردن طیف (بازگشت به دو رنگ)", callback_data="spectrum:off", style=DANGER, icon="cross")])
            rows.append([btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")])
            self.send(chat_id, "یک رنگ پایه برای طیف انتخاب کنید؛ ربات از همان رنگ، درجه‌های تیره تا روشن می‌سازد و در بازسازی استفاده می‌کند:", {"inline_keyboard": rows})
        elif data.startswith("spectrum:"):
            name = data.split(":", 1)[1]
            if name == "prompt":
                sess.awaiting = "spectrum_color"
                self.send(chat_id, "کد رنگ پایه طیف دلخواه خود را به‌صورت متن بفرستید، مثل #FF0000؛ ربات از همان رنگ درجه‌های تیره تا روشن می‌سازد.")
            elif name == "off":
                sess.brand.gradient_enabled = False
                self._save_user_state(sess)
                self.send(chat_id, "طیف رنگی خاموش شد.", main_menu())
            else:
                try:
                    sess.brand.primary_hex = validate_hex(name)
                    sess.brand.gradient_enabled = True
                    self._save_user_state(sess)
                    from emoji_forge.image_engine import gradient_palette_hex
                    shades = ", ".join(gradient_palette_hex(sess.brand.primary_hex))
                    self.send(chat_id, f"طیف فعال شد — رنگ پایه: {sess.brand.primary_hex}\nدرجه‌ها: {shades}", main_menu())
                except ValueError as exc:
                    self.send(chat_id, str(exc), main_menu())
        elif data == "color:prompt":
            sess.awaiting = "brand_color"
            self.send(chat_id, "کد رنگ دلخواه خود را به‌صورت متن بفرستید، مثل #FF0000 (هر رنگی، نه فقط رنگ‌های آماده).")
        elif data == "spectrum:prompt":
            sess.awaiting = "spectrum_color"
            self.send(chat_id, "کد رنگ پایه طیف دلخواه خود را به‌صورت متن بفرستید، مثل #FF0000؛ ربات از همان رنگ درجه‌های تیره تا روشن می‌سازد.")
        elif data == "menu:tgs":
            if not sess.manifest:
                self.send(chat_id, "هنوز پروژه‌ای ندارید.", main_menu())
            else:
                found = []
                for item in sess.manifest["items"]:
                    path = item.get("local_path", "")
                    if path and path.endswith(".tgs") and os.path.exists(path):
                        try:
                            from emoji_forge.animation_engine import load_tgs, tgs_logo_candidates
                            with open(path, "rb") as fh:
                                doc = load_tgs(fh.read())
                            found = tgs_logo_candidates(doc)
                            break
                        except Exception as exc:
                            self.send(chat_id, f"خواندن TGS ناموفق بود: {redact(str(exc), self.config.token)[:200]}", main_menu())
                            return
                if not found:
                    self.send(chat_id, "در اولین TGS، لایه‌ای با نام لوگو/متن پیدا نشد. در حالت خام‌سازی، لایه‌های متنی به‌صورت خودکار پنهان می‌شوند.", main_menu())
                else:
                    rows = []
                    for name in found[:10]:
                        if len(f"tgs_hide:{name}".encode("utf-8")) <= 64:
                            rows.append([{"text": f"🙈 پنهان کردن «{name}»", "callback_data": f"tgs_hide:{name}"}])
                    rows.append([btn("⬅️ بازگشت به منوی اصلی", callback_data="menu:main", icon="back")])
                    self.send(chat_id, "لایه‌های نامزد لوگو/متن در اولین TGS — برای پنهان کردن هرکدام، دکمه‌اش را بزنید:", {"inline_keyboard": rows})
        elif data.startswith("tgs_hide:"):
            name = data.split(":", 1)[1]
            sess.tgs_hide.add(name)
            self.send(chat_id, f"لایه «{name}» در پردازش TGS پنهان می‌شود.", main_menu())
        elif data == "menu:brand":
            self.send(chat_id, "رنگ و برند شما:", brand_menu())
        elif data == "menu:advanced":
            self.send(chat_id, "تنظیمات پیشرفته:", advanced_menu(sess))
        elif data == "recolor:toggle":
            sess.brand.full_recolor = not sess.brand.full_recolor
            self._save_user_state(sess)
            state = "روشن شد — هشدار: رنگ مو و کاراکتر هم تغییر می‌کند." if sess.brand.full_recolor else "خاموش شد — کاراکتر حفظ می‌شود."
            self.send(chat_id, f"بازسازی رنگی کامل {state}", advanced_menu(sess))
        elif data == "review:toggle":
            sess.review_by_mati = not sess.review_by_mati
            self._save_user_state(sess)
            state = "روشن شد — پردازش کامل اول توسط ربات با پردازش پیشرفته بررسی می‌شود." if sess.review_by_mati else "خاموش شد — پردازش کامل مستقیم و خودکار اجرا می‌شود."
            self.send(chat_id, f"پردازش پیشرفته توسط ربات {state}", advanced_menu(sess))
        elif data == "font:cycle":
            order = ["bold_sans", "serif", "mono"]
            cur = order.index(sess.brand.font_style) if sess.brand.font_style in order else 0
            sess.brand.font_style = order[(cur + 1) % len(order)]
            self._save_user_state(sess)
            labels = {"bold_sans": "ضخیم بدون‌سریف", "serif": "سریف", "mono": "تک‌عرض"}
            self.send(chat_id, f"فونت متن رستری: {labels[sess.brand.font_style]}", advanced_menu(sess))
        elif data == "logosize:toggle":
            sess.brand.logo_size_mode = "fill" if sess.brand.logo_size_mode == "fit" else "fit"
            self._save_user_state(sess)
            label = "فیت دقیق داخل قاب لوگوی قبلی (پیش‌فرض)" if sess.brand.logo_size_mode == "fit" else "پرکردن قاب (رفتار قدیمی v7)"
            self.send(chat_id, f"اندازه لوگوی TGS در حالت v8: {label}", advanced_menu(sess))
        elif data == "menu:color":
            self.send(chat_id, "رنگ اصلی خود را انتخاب کنید:", _palette_keyboard("color"))
        elif data == "menu:color2":
            self.send(chat_id, "رنگ دوم (روشن) خود را انتخاب کنید:", _palette_keyboard("color2"))
        elif data == "menu:region":
            self.send(chat_id, "ناحیه لوگوی قدیمی را انتخاب کنید (نوارهای لبه در حالت خام‌سازی خودکار حذف می‌شوند):", _region_keyboard())
        elif data == "menu:pos":
            self.send(chat_id, "محل لوگوی خود را انتخاب کنید:", _pos_keyboard())
        elif data == "menu:size":
            self.send(chat_id, "اندازه لوگوی خود را انتخاب کنید:", _size_keyboard())
        elif data.startswith("mode:"):
            mode = data.split(":", 1)[1]
            if mode in ("preserve", "clean", "redesign", "branding", "strip", "v8"):
                sess.mode = mode
                labels = {"preserve": "حفظ ظاهر اصلی", "clean": "فقط پاک‌سازی",
                          "redesign": "بازطراحی محدود", "branding": "برندینگ کامل",
                          "v8": "v8 — لوگوی برداری دقیق (فیت داخل قاب)",
                          "strip": "خام‌سازی و بازسازی"}
                self.send(chat_id, f"حالت انتخاب شد: {labels[mode]}", main_menu())
        elif data.startswith("color:"):
            try:
                sess.brand.primary_hex = validate_hex(data.split(":", 1)[1])
                self._save_user_state(sess)
                self.send(chat_id, f"رنگ اصلی: {sess.brand.primary_hex}", main_menu())
            except ValueError as exc:
                self.send(chat_id, str(exc), main_menu())
        elif data.startswith("color2:"):
            try:
                sess.brand.secondary_hex = validate_hex(data.split(":", 1)[1])
                self._save_user_state(sess)
                self.send(chat_id, f"رنگ دوم: {sess.brand.secondary_hex}", main_menu())
            except ValueError as exc:
                self.send(chat_id, str(exc), main_menu())
        elif data.startswith("region:"):
            name = data.split(":", 1)[1]
            if name == "none":
                sess.region = None
                self.send(chat_id, "ناحیه پاک‌سازی غیرفعال شد (فقط حذف خودکار لبه در حالت خام‌سازی).", main_menu())
            elif name in PRESETS:
                x0, y0, x1, y1 = PRESETS[name]
                sess.region = Region(x0, y0, x1, y1)
                self.send(chat_id, f"ناحیه لوگوی قدیمی: {name}", main_menu())
        elif data.startswith("pos:"):
            name = data.split(":", 1)[1]
            if name in ("center", "top", "bottom", "top-left", "top-right", "bottom-left", "bottom-right"):
                sess.brand.logo_position = name
                self._save_user_state(sess)
                self.send(chat_id, f"محل لوگو: {name}", main_menu())
        elif data.startswith("size:"):
            try:
                value = int(data.split(":", 1)[1])
                sess.brand.logo_size_pct = value
                self._save_user_state(sess)
                self.send(chat_id, f"اندازه لوگو: {value}٪", main_menu())
            except ValueError:
                pass
        elif data == "name:prompt":
            sess.awaiting = "brand_name"
            self.send(chat_id, "نام برند خود را به‌صورت متن بفرستید (نه دستور، فقط خودِ نام).")
        elif data == "logo:prompt":
            sess.awaiting = "brand_logo"
            self.send(chat_id, "لوگو را به‌صورت فایل (PNG شفاف) بفرستید.")
        elif data == "watermark:prompt":
            sess.awaiting = "watermark"
            self.send(chat_id, "متن واترمارک را به‌صورت متن بفرستید.")
        elif data == "cmd:brand":
            self.handle_command(sess, chat_id, user_id, "/brand")
        elif data == "cmd:preview":
            self._preview(sess, chat_id)
        elif data == "cmd:process":
            if sess.review_by_mati:
                self._queue_mati_review(sess, chat_id, user_id)
            else:
                self.handle_command(sess, chat_id, user_id, "/process")
        elif data == "cmd:export":
            self.handle_command(sess, chat_id, user_id, "/export")
        elif data == "cmd:publish":
            self.handle_command(sess, chat_id, user_id, "/publish_new")
        elif data == "cmd:status":
            self.handle_command(sess, chat_id, user_id, "/status")
        elif data == "cmd:cancel":
            self.handle_command(sess, chat_id, user_id, "/cancel")
        elif data == "cmd:help":
            self.send(chat_id, HELP_TEXT)
        elif data == "preview_ok":
            sess.preview_approved = True
            if sess.review_by_mati:
                self._queue_mati_review(sess, chat_id, user_id)
            else:
                self.handle_command(sess, chat_id, user_id, "/process")
        elif data == "preview_no":
            sess.preview_approved = False
            self.send(chat_id, "تأیید نشد. تنظیمات را تغییر بدهید و دوباره «پیش‌نمایش» را بزنید.")
        elif data == "owner_ok":
            if sess.pending_manifest is None:
                self.send(chat_id, "پکی در انتظار تأیید نیست.")
            else:
                sess.ownership_confirmed = True
                sess.manifest = sess.pending_manifest
                sess.pending_manifest = None
                self._save_user_state(sess)
                write_json(os.path.join(project_dir(self.config.data_dir, sess.project_id), "manifest.json"), sess.manifest)
                self.send(chat_id, "تأیید مالکیت ثبت شد. در حال دانلود پک…")
                self._download_manifest_items(sess, chat_id)
        elif data == "owner_no":
            sess.pending_manifest = None
            self.send(chat_id, "دانلود پک لغو شد. فقط فایل‌هایی را پردازش کنید که مالک آن‌ها هستید یا اجازه دارید.")
        elif data == "publish_new":
            if not sess.project_id:
                self.send(chat_id, "هنوز پروژه‌ای ندارید.")
                return
            root = project_dir(self.config.data_dir, sess.project_id)
            final_dir = os.path.join(root, "final")
            if not os.path.isdir(final_dir) or not os.listdir(final_dir):
                self.send(chat_id, "فایل نهایی برای انتشار وجود ندارد؛ اول «پردازش کامل» را کامل کنید.")
                return
            if not self._quota_gate(user_id, chat_id, len((sess.manifest or {}).get("items") or [])):
                return
            job_id = f"{sess.project_id}-publish-{int(time.time())}"
            sess.last_job_id = job_id
            self.queue.enqueue({"id": job_id, "kind": "publish", "user_id": user_id,
                                "chat_id": chat_id, "project_id": sess.project_id,
                                "manifest": sess.manifest or {"items": []},
                                "set_title": (sess.manifest or {}).get("set_title", "")})
            self.send(chat_id, "انتشار پک جدید در صف قرار گرفت؛ نتیجه واقعی (لینک یا خطای تلگرام) را همین‌جا می‌فرستم.")

    def run(self):
        # v9 startup self-check (no network): data dirs, fonts, logo
        # shapes — one readiness line before polling starts.
        print(readiness_line(self.config.data_dir), flush=True)
        me = self.api.get_me()
        print(f"[forge] bot running as @{me.get('username', '?')} (id {me.get('id')})", flush=True)
        self.queue.start()
        threading.Thread(target=self._review_watcher, daemon=True).start()
        offset = None
        while True:
            try:
                updates = self.api.get_updates(offset=offset, timeout=30)
            except Exception as exc:
                print(f"[poll-error] {redact(str(exc), self.config.token)[:200]}", flush=True)
                time.sleep(2)
                continue
            for update in updates:
                offset = update.get("update_id", 0) + 1
                try:
                    if "message" in update:
                        self.handle_message(update["message"])
                    elif "callback_query" in update:
                        self.handle_callback(update["callback_query"])
                except Exception:
                    print("[handler-error]\n" + traceback.format_exc(), flush=True)


def main() -> int:
    load_dotenv(os.path.join(os.path.dirname(os.path.abspath(__file__)), ".env"))
    config = load_config()
    if not config.has_token:
        print(
            "EMOJI_FORGE_TOKEN تنظیم نشده است.\n"
            "۱) در @BotFather یک ربات بسازید و توکن را بگیرید.\n"
            "۲) آن را فقط در فایل .env بنویسید (نمونه: .env.example).\n"
            "۳) دوباره python3 run.py را اجرا کنید.\n"
            "ربات بدون توکن اجرا نشد — هیچ موفقیتی اعلام نمی‌شود.",
            flush=True,
        )
        return 2
    try:
        api = TelegramApi(config.token)
        bot = Bot(api, config)
        bot.run()
    except AccessError as exc:
        print(f"خطای دسترسی: {exc}", flush=True)
        return 2
    except KeyboardInterrupt:
        print("متوقف شد.", flush=True)
        return 0
    return 0
