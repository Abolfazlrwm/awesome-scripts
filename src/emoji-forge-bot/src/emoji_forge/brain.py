"""Swappable AI brain for Emoji Forge AI.

HONEST ARCHITECTURE NOTE (read before claiming anything):
- There is NO public, documented API that lets this Python program call
  the Muse model that powers the Muse chat session, from inside the bot
  process, at Telegram-message time. The Muse session's model access
  belongs to the Muse runtime (its own internal sockets/tokens), not to
  user code, and this project deliberately does NOT touch those internal
  sockets, does NOT reuse any runtime token, and does NOT pretend a
  session-only capability is a bot API.
- Therefore the brain layer has three real, named providers:
  1. "rule" (DEFAULT, always available, no model, no cost, works after
     the Muse session ends, because it is plain code): deterministic
     heuristics for mode/colour suggestions and error diagnosis.
     It must NEVER be presented as AI.
  2. "muse-bridge" (ASYNC, file-queue, NOT a model API call): the bot
     writes a request JSON to data/brain/requests/<id>.json. A Muse
     session (or a Muse scheduled job the user explicitly enables)
     reads the queue, analyses it with its own intelligence, and writes
     data/brain/decisions/<id>.json. The bot only ever *reads* a
     decision file that already exists; it never calls a model.
     Latency = until the next Muse run. If the bot moves to a VPS,
     this bridge breaks, because the queue file is no longer on the
     same filesystem as Muse. That limit is documented, not hidden.
  3. "external" (OPTIONAL, disabled by default, NOT required): only if
     the user explicitly sets EMOJI_FORGE_EXTERNAL_BRAIN_URL and
     EMOJI_FORGE_EXTERNAL_BRAIN_KEY in .env, the bot POSTs to an
     OpenAI-compatible chat-completions endpoint. The key is never
     logged or stored anywhere except the user's own .env. Live
     external behaviour has NOT been tested (no key was ever supplied).
  4. "openai-compatible" (v10.1, OPTIONAL): the owner's own API key for
     ANY OpenAI-compatible endpoint, configured purely by environment:
     AI_API_KEY (required to activate), AI_BASE_URL (default
     https://api.openai.com/v1 — OpenAI, OpenRouter, a local server,
     any compatible gateway) and AI_MODEL (default gpt-4o-mini).
     Selection priority in get_brain(): when AI_API_KEY is present this
     provider wins over EMOJI_FORGE_BRAIN_PROVIDER; otherwise the
     existing behaviour is unchanged. On ANY error (network, HTTP,
     malformed reply) decide() falls back to the deterministic rule
     provider, so the bot never breaks because an endpoint is down.
     The key is only ever sent as the Authorization header to the
     configured base URL and is redacted from every message.

- The brain NEVER bypasses deterministic engines, size limits, the
  ownership gate, or validation. It only *suggests* mode/settings and
  *explains* errors. Nothing is auto-applied without user confirmation.
- Brain requests contain only text summaries (counts, formats, error
  text, brand settings). They never contain file bytes, Telegram tokens,
  or any other credential.
"""
import hashlib
import json
import os
import time
from dataclasses import dataclass, field

from .storage import read_json, write_json


def request_id_for(payload: dict) -> str:
    blob = json.dumps(payload, sort_keys=True, ensure_ascii=False).encode("utf-8")
    return hashlib.sha256(blob).hexdigest()[:16]


@dataclass
class BrainDecision:
    source: str  # rule | muse-bridge | external | openai-compatible | none
    mode: str = ""  # preserve | clean | redesign | branding | ""
    suggestion_fa: str = ""
    message_fa: str = ""
    warnings: list = field(default_factory=list)
    pending: bool = False
    confidence: str = "low"  # low | medium | high (rule/bridge are low/medium by design)


class BrainProvider:
    name = "base"

    def available(self):
        """Return (bool, Persian reason)."""
        return False, "موتور پردازش ربات در دسترس نیست."

    def decide(self, request: dict) -> BrainDecision:
        raise NotImplementedError


# ---------------------------------------------------------------- rule
def _mode_from_text(text: str) -> str:
    t = (text or "").lower()
    if any(w in t for w in ["پاک", "حذف لوگو", "clean", "remove logo"]):
        return "clean"
    if any(w in t for w in ["بازطراحی", "redesign"]):
        return "redesign"
    if any(w in t for w in ["حفظ", "فقط تبدیل", "preserve"]):
        return "preserve"
    if any(w in t for w in ["برند", "لوگو", "brand", "logo"]):
        return "branding"
    return ""


def diagnose_error_text(error_text: str) -> str:
    e = (error_text or "").lower()
    if "401" in e or "unauthorized" in e:
        return ("خطای ۴۰۱ یعنی توکن ربات معتبر نیست. توکن BotFather را در فایل .env "
                "بررسی کنید؛ این خطا ربطی به پردازش تصویر ندارد.")
    if "set_name" in e or "نام پک" in e:
        return ("نام پک در پاسخ تلگرام نبود؛ پس پک کامل قابل‌شناسایی نیست. "
                "لینک t.me/addemoji/... را بفرستید یا فایل‌ها را مستقیم بدهید.")
    if "too big" in e or "512" in e or "256" in e or "64" in e or "حجم" in e:
        return ("فایل خروجی از سقف رسمی تلگرام بزرگ‌تر است (ثابت ۵۱۲KB، TGS ۶۴KB، "
                "WEBM ۲۵۶KB). کیفیت به‌صورت خودکار پایین می‌آید؛ اگر باز هم نشد، "
                "فایل ورودی را کوچک‌تر کنید.")
    if "timed out" in e or "timeout" in e:
        return ("اتصال به تلگرام زمان‌بر شد؛ با /retry دوباره تلاش کنید. "
                "این خطا به معنی خرابی فایل نیست.")
    return ("خطا از موتور یا تلگرام برگشت؛ متن کامل خطا در گزارش پروژه ذخیره شده است. "
            "با /retry یا تغییر حالت (/mode) دوباره امتحان کنید.")


class RuleBrain(BrainProvider):
    """Deterministic limited mode. NOT an AI model. Always available."""
    name = "rule"

    def available(self):
        return True, "موتور پردازش ربات فعال است."

    def decide(self, request: dict) -> BrainDecision:
        kind = request.get("kind", "analyze")
        if kind == "diagnose":
            return BrainDecision(
                source="rule",
                message_fa=diagnose_error_text(request.get("error_text", "")),
                confidence="low",
            )
        text = request.get("user_text", "") or ""
        mode = _mode_from_text(text)
        formats = request.get("formats") or {}
        suggestion = ""
        if mode:
            labels = {"preserve": "حفظ ظاهر اصلی", "clean": "فقط پاک‌سازی",
                      "redesign": "بازطراحی محدود", "branding": "برندینگ کامل"}
            suggestion = f"پیشنهاد ربات: حالت «{labels[mode]}»."
        elif formats.get("tgs"):
            suggestion = ("پیشنهاد ربات: چون فایل TGS دارید، اول /tgs_layers را "
                          "بزنید تا لایه‌های لوگو/متن را ببینید، بعد حالت branding را انتخاب کنید.")
        else:
            suggestion = ("پیشنهاد ربات: حالت پیش‌فرض branding است؛ اگر فقط تبدیل "
                          "فرمت می‌خواهید، /mode preserve را بزنید.")
        return BrainDecision(
            source="rule",
            mode=mode,
            suggestion_fa=suggestion,
            message_fa=(suggestion + "\nبرای اعمال این پیشنهاد، حالت را با /mode تغییر بدهید."),
            confidence="low",
        )


# ------------------------------------------------------- muse bridge
class MuseBridgeBrain(BrainProvider):
    """Async file-queue bridge to a Muse session / scheduled Muse job.

    decide() NEVER calls a model. It (1) returns a previously written
    decision file if one matches, (2) otherwise writes the request to
    the queue and returns a pending decision with the rule fallback.
    """
    name = "muse-bridge"

    def __init__(self, data_dir: str):
        self.base = os.path.join(data_dir, "brain")
        self.req_dir = os.path.join(self.base, "requests")
        self.dec_dir = os.path.join(self.base, "decisions")
        os.makedirs(self.req_dir, exist_ok=True)
        os.makedirs(self.dec_dir, exist_ok=True)

    def available(self):
        return True, "موتور پردازش پیشرفته ربات فعال است."

    def _decision_path(self, rid: str) -> str:
        return os.path.join(self.dec_dir, f"{rid}.json")

    def decide(self, request: dict) -> BrainDecision:
        rid = request_id_for(request)
        existing = read_json(self._decision_path(rid), default=None)
        if existing:
            return BrainDecision(
                source="muse-bridge",
                mode=existing.get("mode", ""),
                suggestion_fa=existing.get("suggestion_fa", ""),
                message_fa=existing.get("message_fa", ""),
                warnings=list(existing.get("warnings") or []),
                confidence=existing.get("confidence", "medium"),
            )
        payload = dict(request)
        payload["request_id"] = rid
        payload["created_at"] = int(time.time())
        write_json(os.path.join(self.req_dir, f"{rid}.json"), payload)
        fallback = RuleBrain().decide(request)
        return BrainDecision(
            source="muse-bridge",
            mode=fallback.mode,
            suggestion_fa=fallback.suggestion_fa,
            message_fa=("⏳ در حال پردازش پیشرفته توسط ربات…\n"
                        "فعلاً پیشنهاد ربات را ببینید:\n"
                        + fallback.suggestion_fa),
            warnings=["پردازش پیشرفته در جریان است."],
            pending=True,
            confidence="low",
        )

    def pending_count(self) -> int:
        try:
            reqs = {f[:-5] for f in os.listdir(self.req_dir) if f.endswith(".json")}
            decs = {f[:-5] for f in os.listdir(self.dec_dir) if f.endswith(".json")}
            return len(reqs - decs)
        except OSError:
            return 0


# ----------------------------------------------------------- external
class ExternalBrain(BrainProvider):
    """Optional OpenAI-compatible endpoint. Disabled unless the user
    explicitly configures URL + key in .env. Never used by default."""
    name = "external"

    def __init__(self, base_url: str = "", api_key: str = ""):
        self.base_url = (base_url or "").rstrip("/")
        self.api_key = api_key or ""

    def available(self):
        if not self.base_url or not self.api_key:
            return False, "این قابلیت فعال نیست."
        return True, "این قابلیت فعال است."

    def decide(self, request: dict) -> BrainDecision:
        ok, reason = self.available()
        if not ok:
            return BrainDecision(source="none", message_fa=reason, pending=False)
        import requests  # only imported when explicitly configured
        prompt = ("You are a helper for a Telegram custom-emoji personalisation bot. "
                  "Reply in Persian, in one short paragraph, suggesting only: mode "
                  "(preserve/clean/redesign/branding) and one practical next step. "
                  f"Request: {json.dumps(request, ensure_ascii=False)[:2000]}")
        try:
            resp = requests.post(
                f"{self.base_url}/chat/completions",
                headers={"Authorization": f"Bearer {self.api_key}"},
                json={"model": "gpt-4o-mini",
                      "messages": [{"role": "user", "content": prompt}]},
                timeout=30,
            )
            resp.raise_for_status()
            data = resp.json()
            text = (data.get("choices") or [{}])[0].get("message", {}).get("content", "") or ""
            mode = _mode_from_text(text)
            return BrainDecision(source="external", mode=mode,
                                 suggestion_fa=text[:1000], message_fa=text[:1000],
                                 confidence="medium")
        except Exception as exc:
            # Never include the key or the full response in the message.
            safe = str(exc).replace(self.api_key, "***")[:200]
            return BrainDecision(source="none",
                                 message_fa=f"خطا در پردازش ربات: {safe}",
                                 warnings=["پردازش ناموفق بود؛ دوباره تلاش کنید."])


# ------------------------------------------- openai-compatible (v10.1)
DEFAULT_AI_BASE_URL = "https://api.openai.com/v1"
DEFAULT_AI_MODEL = "gpt-4o-mini"


class OpenAICompatibleBrain(BrainProvider):
    """Owner-configured OpenAI-compatible chat endpoint (v10.1).

    Activated purely by environment: AI_API_KEY set (AI_BASE_URL and
    AI_MODEL optional). Works with OpenAI, OpenRouter, local servers —
    anything that answers POST {base}/chat/completions. Every failure
    path falls back to the deterministic rule provider, so a dead or
    misconfigured endpoint degrades the bot to rule suggestions instead
    of breaking it. The key is never logged and is redacted from any
    error text before it can reach a decision.
    """
    name = "openai-compatible"

    def __init__(self, api_key: str = "", base_url: str = "",
                 model: str = "", timeout: int = 30):
        self.api_key = (api_key or "").strip()
        self.base_url = (base_url or "").strip() or DEFAULT_AI_BASE_URL
        self.model = (model or "").strip() or DEFAULT_AI_MODEL
        self.timeout = timeout

    @property
    def chat_url(self) -> str:
        return self.base_url.rstrip("/") + "/chat/completions"

    def available(self):
        if not self.api_key:
            return False, "این قابلیت فعال نیست."
        return True, "این قابلیت فعال است."

    def decide(self, request: dict) -> BrainDecision:
        ok, _reason = self.available()
        if not ok:
            return RuleBrain().decide(request)
        import requests  # only imported when explicitly configured
        prompt = ("You are a helper for a Telegram custom-emoji personalisation bot. "
                  "Reply in Persian, in one short paragraph, suggesting only: mode "
                  "(preserve/clean/redesign/branding) and one practical next step. "
                  f"Request: {json.dumps(request, ensure_ascii=False)[:2000]}")
        try:
            resp = requests.post(
                self.chat_url,
                headers={"Authorization": f"Bearer {self.api_key}",
                         "Content-Type": "application/json"},
                json={"model": self.model,
                      "messages": [{"role": "user", "content": prompt}]},
                timeout=self.timeout,
            )
            resp.raise_for_status()
            data = resp.json()
            text = (data.get("choices") or [{}])[0].get("message", {}).get("content", "") or ""
            text = text.strip()
            if not text:
                raise ValueError("empty completion")
            fallback = RuleBrain().decide(request)
            return BrainDecision(
                source="openai-compatible",
                mode=_mode_from_text(text) or fallback.mode,
                suggestion_fa=text[:1000],
                message_fa=text[:1000],
                confidence="medium",
            )
        except Exception as exc:
            # Graceful degradation: rule suggestions keep working, and
            # the key can never leak through the error text.
            safe = str(exc).replace(self.api_key, "***")[:200]
            decision = RuleBrain().decide(request)
            decision.warnings.append(f"سرویس پردازش تکمیلی پاسخ نداد ({safe})؛ پیشنهاد ربات اعمال شد.")
            return decision


def get_brain(config) -> BrainProvider:
    # v10.1 priority: an explicitly configured custom API key wins over
    # the EMOJI_FORGE_BRAIN_PROVIDER selection; without a key the
    # historical behaviour below is byte-for-byte unchanged.
    ai_key = (getattr(config, "ai_api_key", "") or "").strip()
    if ai_key:
        return OpenAICompatibleBrain(
            ai_key,
            getattr(config, "ai_base_url", "") or DEFAULT_AI_BASE_URL,
            getattr(config, "ai_model", "") or DEFAULT_AI_MODEL,
        )
    provider = (getattr(config, "brain_provider", "") or "rule").strip().lower()
    if provider == "muse-bridge":
        return MuseBridgeBrain(config.data_dir)
    if provider == "external":
        return ExternalBrain(getattr(config, "external_brain_url", ""),
                             getattr(config, "external_brain_key", ""))
    return RuleBrain()


def validate_bridge_decision(data: dict) -> list:
    """Validate a decision JSON written by a Muse session. Returns errors."""
    errors = []
    if not isinstance(data, dict):
        return ["تصمیم باید یک آبجکت JSON باشد."]
    if data.get("mode") not in ("", "preserve", "clean", "redesign", "branding"):
        errors.append("mode نامعتبر است؛ فقط preserve/clean/redesign/branding یا خالی.")
    if not isinstance(data.get("suggestion_fa", ""), str):
        errors.append("suggestion_fa باید متن باشد.")
    if not isinstance(data.get("message_fa", ""), str):
        errors.append("message_fa باید متن باشد.")
    if data.get("confidence") not in ("low", "medium", "high"):
        errors.append("confidence باید low/medium/high باشد.")
    return errors
