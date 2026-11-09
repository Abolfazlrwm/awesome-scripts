"""Access control (v9): owner approval gate + per-user quotas.

Public-release model (owner's order, 2026-10-29):
- Every NEW user starts as "pending". They cannot use any feature
  until the OWNER approves them from the owner's own chat (inline
  buttons admin:approve:<id> / admin:reject:<id>, owner-only).
- The owner sets how much each approved user may use the bot:
  packs_total, emojis_total and packs_per_day quotas. Defaults for a
  newly approved user live in DEFAULT_QUOTA below (owner-editable).
- The owner (OWNER_ID, plus any EMOJI_FORGE_ADMIN_IDS) is always
  approved and unlimited.

Persistence: one JSON per user in data/users/<id>.json — the SAME
file the profile store (brand / ownership / review flags) uses.
Every write is atomic (tmp + rename, via storage.write_json) and the
registry keeps an in-memory cache: the users directory is scanned at
most once per process (all_users()); single-user reads/writes go
through the cache, so no per-message directory scans happen.

Usage accounting rule (documented choice): counters are incremented
when a process/publish job COMPLETES successfully (the bot calls
record_usage from its job worker), not at enqueue time — a job that
fails or is cancelled does not consume quota. Quota is CHECKED
before enqueueing. Daily counters are keyed by Asia/Tehran date.
"""
import os
import time
from datetime import datetime

from .storage import read_json, write_json

def _owner_id_from_env() -> int:
    """Owner id for THIS deployment (distributable build): read from
    EMOJI_FORGE_OWNER_ID, else the first EMOJI_FORGE_ADMIN_IDS entry.
    0 means unset — no real Telegram user has id 0."""
    for raw in (os.environ.get("EMOJI_FORGE_OWNER_ID"),
                (os.environ.get("EMOJI_FORGE_ADMIN_IDS") or "").split(",")[0]):
        try:
            return int(str(raw).strip())
        except (TypeError, ValueError):
            continue
    return 0


OWNER_ID = _owner_id_from_env()

# Defaults granted to a newly approved user (owner can change per
# user at any time from the admin menu / /quota command).
DEFAULT_QUOTA = {"packs_total": 3, "emojis_total": 100, "packs_per_day": 1}

STATUSES = ("pending", "approved", "rejected", "blocked")

# Quota presets offered as buttons in the admin menu.
QUOTA_PRESETS = {
    "default": {"packs_total": 3, "emojis_total": 100, "packs_per_day": 1},
    "plus": {"packs_total": 10, "emojis_total": 300, "packs_per_day": 3},
    "pro": {"packs_total": 50, "emojis_total": 2000, "packs_per_day": 10},
    "unlimited": None,  # None quota = no limits
}

STATUS_FA = {
    "pending": "در انتظار تأیید مالک",
    "approved": "تأییدشده",
    "rejected": "ردشده",
    "blocked": "مسدود",
}


def tehran_today(now: float | None = None) -> str:
    """Today's date (YYYY-MM-DD) in Asia/Tehran — the quota day key."""
    try:
        from zoneinfo import ZoneInfo
        tz = ZoneInfo("Asia/Tehran")
    except Exception:
        tz = None
    dt = datetime.fromtimestamp(now if now is not None else time.time(), tz)
    return dt.strftime("%Y-%m-%d")


def _blank_usage() -> dict:
    return {"packs_used": 0, "emojis_used": 0, "usage_by_day": {}}


def _normalise(raw: dict | None, user_id: int, owner_ids: set) -> dict:
    """Fill the access fields of a user record with safe defaults.

    Old profile files (pre-v9) have no access fields: a normal user
    with an old file starts as PENDING (they must be approved once);
    the owner is always approved/unlimited."""
    rec = dict(raw or {})
    rec["user_id"] = int(rec.get("user_id") or user_id)
    is_owner = rec["user_id"] in owner_ids
    rec["role"] = "owner" if is_owner else (rec.get("role") or "user")
    if is_owner:
        rec["status"] = "approved"
        rec["quota"] = None
    else:
        if rec.get("status") not in STATUSES:
            rec["status"] = "pending"
        if "quota" not in rec:
            rec["quota"] = dict(DEFAULT_QUOTA)
        elif isinstance(rec.get("quota"), dict):
            merged = dict(DEFAULT_QUOTA)
            merged.update({k: rec["quota"][k] for k in DEFAULT_QUOTA
                           if k in rec["quota"]})
            rec["quota"] = merged
    usage = rec.get("usage") or {}
    base = _blank_usage()
    base.update({k: usage[k] for k in base if k in usage})
    if not isinstance(base.get("usage_by_day"), dict):
        base["usage_by_day"] = {}
    rec["usage"] = base
    rec.setdefault("first_name", "")
    rec.setdefault("username", "")
    rec.setdefault("created_at", None)
    rec.setdefault("approved_at", None)
    return rec


class UserRegistry:
    """Cached, atomic per-user record store (profile + access)."""

    def __init__(self, data_dir: str, owner_ids=None):
        self.data_dir = data_dir
        self.users_dir = os.path.join(data_dir, "users")
        os.makedirs(self.users_dir, exist_ok=True)
        self.owner_ids = {int(x) for x in (owner_ids or set())} | {OWNER_ID}
        self._cache: dict[int, dict] = {}
        self._scanned = False

    # -- low level --------------------------------------------------
    def _path(self, user_id: int) -> str:
        return os.path.join(self.users_dir, f"{int(user_id)}.json")

    def is_owner(self, user_id) -> bool:
        try:
            return int(user_id) in self.owner_ids
        except (TypeError, ValueError):
            return False

    def get(self, user_id: int) -> dict | None:
        """Record or None. Cached after the first read; the cache is
        updated on every save, so it never goes stale in-process."""
        user_id = int(user_id)
        if user_id in self._cache:
            return self._cache[user_id]
        raw = read_json(self._path(user_id), default=None)
        if raw is None:
            return None
        rec = _normalise(raw, user_id, self.owner_ids)
        self._cache[user_id] = rec
        return rec

    def save(self, record: dict) -> dict:
        user_id = int(record["user_id"])
        write_json(self._path(user_id), record)
        self._cache[user_id] = record
        return record

    def all_users(self) -> list:
        """Every known record. The directory is scanned ONCE per
        process; afterwards the cache (kept fresh by save()) is used."""
        if not self._scanned:
            self._scanned = True
            try:
                names = os.listdir(self.users_dir)
            except OSError:
                names = []
            for name in names:
                stem = name[:-5] if name.endswith(".json") else ""
                if stem.isdigit():
                    self.get(int(stem))
        return [self._cache[uid] for uid in sorted(self._cache)]

    # -- lifecycle --------------------------------------------------
    def touch(self, user_id: int, first_name: str = "", username: str = ""):
        """Get-or-create the record for an interacting user.

        Returns (record, is_new). A brand-new normal user is created
        as PENDING with the default quota (quota only bites after
        approval). Display name/username are refreshed when provided."""
        user_id = int(user_id)
        rec = self.get(user_id)
        if rec is not None:
            changed = False
            if first_name and rec.get("first_name") != first_name:
                rec["first_name"] = first_name
                changed = True
            if username and rec.get("username") != username:
                rec["username"] = username
                changed = True
            if changed:
                self.save(rec)
            return rec, False
        rec = _normalise(None, user_id, self.owner_ids)
        rec["first_name"] = first_name or ""
        rec["username"] = username or ""
        rec["created_at"] = int(time.time())
        if self.is_owner(user_id):
            rec["status"] = "approved"
            rec["approved_at"] = rec["created_at"]
        self.save(rec)
        return rec, True

    def update_profile(self, user_id: int, fields: dict) -> dict:
        """Merge profile fields (brand, ownership, review flags) into
        the record WITHOUT touching access fields. Creates a minimal
        pending record when none exists yet."""
        user_id = int(user_id)
        rec = self.get(user_id)
        if rec is None:
            rec = _normalise(None, user_id, self.owner_ids)
            rec["created_at"] = int(time.time())
        rec.update(fields)
        return self.save(rec)

    # -- admin actions ------------------------------------------------
    def set_status(self, user_id: int, status: str) -> dict:
        if status not in STATUSES:
            raise ValueError(f"وضعیت نامعتبر است: {status}")
        user_id = int(user_id)
        rec = self.get(user_id)
        if rec is None:
            rec, _new = self.touch(user_id)
        if self.is_owner(user_id):
            status = "approved"  # the owner can never lock himself out
        rec["status"] = status
        if status == "approved" and not rec.get("approved_at"):
            rec["approved_at"] = int(time.time())
        return self.save(rec)

    def set_quota(self, user_id: int, quota) -> dict:
        """quota: None (unlimited) or a dict with any of the
        DEFAULT_QUOTA keys; missing keys fall back to the defaults."""
        user_id = int(user_id)
        rec = self.get(user_id)
        if rec is None:
            rec, _new = self.touch(user_id)
        if quota is None:
            rec["quota"] = None
        else:
            merged = dict(DEFAULT_QUOTA)
            for key in DEFAULT_QUOTA:
                if key in quota:
                    value = int(quota[key])
                    if value < 0:
                        raise ValueError(f"سهمیه {key} نمی‌تواند منفی باشد.")
                    merged[key] = value
            rec["quota"] = merged
        return self.save(rec)

    # -- quota --------------------------------------------------------
    def is_allowed(self, user_id: int) -> bool:
        if self.is_owner(user_id):
            return True
        rec = self.get(user_id)
        return bool(rec and rec.get("status") == "approved")

    def status_of(self, user_id: int) -> str:
        if self.is_owner(user_id):
            return "approved"
        rec = self.get(user_id)
        return (rec or {}).get("status") or "pending"

    def check_quota(self, user_id: int, item_count: int, now: float | None = None):
        """(allowed, reason_fa). Checked BEFORE enqueueing a job that
        would process/publish one pack of `item_count` emojis."""
        if self.is_owner(user_id):
            return True, ""
        rec = self.get(int(user_id))
        if rec is None or rec.get("status") != "approved":
            return False, "دسترسی شما هنوز تأیید نشده است."
        quota = rec.get("quota")
        if quota is None:
            return True, ""
        usage = rec.get("usage") or _blank_usage()
        today = tehran_today(now)
        day = (usage.get("usage_by_day") or {}).get(today) or {}
        if usage.get("packs_used", 0) + 1 > quota["packs_total"]:
            return False, (f"سهمیه پک شما تمام شده است "
                           f"({usage.get('packs_used', 0)}/{quota['packs_total']}). "
                           "برای افزایش سهمیه با مالک ربات در تماس باشید.")
        if usage.get("emojis_used", 0) + max(0, item_count) > quota["emojis_total"]:
            return False, (f"سهمیه ایموجی شما کافی نیست "
                           f"({usage.get('emojis_used', 0)}/{quota['emojis_total']}). "
                           "برای افزایش سهمیه با مالک ربات در تماس باشید.")
        if day.get("packs", 0) + 1 > quota["packs_per_day"]:
            return False, (f"سهمیه روزانه پک شما تمام شده است "
                           f"({day.get('packs', 0)}/{quota['packs_per_day']}). "
                           "فردا دوباره تلاش کنید یا با مالک ربات در تماس باشید.")
        return True, ""

    def record_usage(self, user_id: int, packs: int = 1, emojis: int = 0,
                     now: float | None = None) -> dict | None:
        """Increment counters after a job completed successfully.
        Owner/unlimited users are skipped (returns None)."""
        if self.is_owner(user_id):
            return None
        rec = self.get(int(user_id))
        if rec is None:
            return None
        usage = rec.setdefault("usage", _blank_usage())
        usage["packs_used"] = usage.get("packs_used", 0) + packs
        usage["emojis_used"] = usage.get("emojis_used", 0) + max(0, emojis)
        today = tehran_today(now)
        by_day = usage.setdefault("usage_by_day", {})
        day = by_day.setdefault(today, {"packs": 0, "emojis": 0})
        day["packs"] = day.get("packs", 0) + packs
        day["emojis"] = day.get("emojis", 0) + max(0, emojis)
        self.save(rec)
        return rec

    # -- presentation helpers ------------------------------------------
    def describe_user(self, rec: dict) -> str:
        uid = rec.get("user_id")
        name = rec.get("first_name") or "—"
        uname = f"@{rec['username']}" if rec.get("username") else "—"
        status = STATUS_FA.get(rec.get("status"), rec.get("status") or "—")
        usage = rec.get("usage") or _blank_usage()
        quota = rec.get("quota")
        if rec.get("role") == "owner" or quota is None:
            quota_txt = "نامحدود"
        else:
            quota_txt = (f"پک {usage.get('packs_used', 0)}/{quota['packs_total']} — "
                         f"ایموجی {usage.get('emojis_used', 0)}/{quota['emojis_total']} — "
                         f"روزانه {quota['packs_per_day']}")
        return f"• {uid} — {name} ({uname}) — {status} — {quota_txt}"


def access_status_message(status: str) -> str:
    """The short Persian refusal shown to non-approved users."""
    if status == "pending":
        return ("⏳ درخواست دسترسی شما ثبت شده و در انتظار تأیید مالک ربات است.\n"
                "به‌محض تأیید، می‌توانید از ربات استفاده کنید.")
    if status == "rejected":
        return "❌ درخواست دسترسی شما توسط مالک ربات رد شده است."
    if status == "blocked":
        return "⛔ دسترسی شما به این ربات مسدود شده است."
    return "⏳ دسترسی شما هنوز تأیید نشده است."
