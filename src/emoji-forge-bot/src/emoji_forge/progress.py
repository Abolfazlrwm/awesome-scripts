"""Persian progress reporting (v10): ONE status message edited in
place with a block progress bar, Persian digits and an ETA.

format_progress() is a pure function (unit-tested). ProgressReporter
wraps a caller-supplied edit callback and throttles: an update is
emitted at most once per `min_interval` seconds OR when the percent
advanced by at least `step_pct` since the last emit — whichever
comes first — plus always on completion (done >= total) and when
force=True. Edit failures are swallowed by design: progress is
best-effort cosmetics and must never kill a job.
"""
import time

_FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")


def fa_num(value) -> str:
    """ASCII digits -> Persian digits (ints, floats, percents)."""
    return str(value).translate(_FA_DIGITS)


def progress_bar(pct: float, width: int = 10) -> str:
    pct = max(0.0, min(100.0, float(pct)))
    filled = int(round(pct / 100.0 * width))
    return "█" * filled + "░" * (width - filled)


def format_progress(done: int, total: int, elapsed: float | None = None,
                    label: str = "پردازش") -> str:
    """e.g. «⏳ پردازش: ██████░░░░ ۶۰٪ — ۱۲۰ از ۲۰۰ — حدود ۴۰ ثانیه مانده».

    The ETA is a linear estimate from elapsed time; it is only shown
    mid-run (never at 0% or 100%) and rounded to whole seconds."""
    total = max(1, int(total))
    done = max(0, min(int(done), total))
    pct = done * 100.0 / total
    text = (f"⏳ {label}: {progress_bar(pct)} {fa_num(int(round(pct)))}٪"
            f" — {fa_num(done)} از {fa_num(total)}")
    if elapsed is not None and 0 < done < total and elapsed > 0:
        eta = elapsed * (total - done) / done
        if eta >= 1:
            text += f" — حدود {fa_num(int(round(eta)))} ثانیه مانده"
    return text


class ProgressReporter:
    """Throttled editor for one status message.

    edit_fn(text) performs the actual Telegram edit (the bot wraps
    api.edit_message_text with the chat/message ids). The first
    update always emits (so the user sees motion immediately)."""

    def __init__(self, edit_fn, min_interval: float = 3.0,
                 step_pct: float = 5.0, label: str = "پردازش",
                 clock=time.monotonic):
        self._edit_fn = edit_fn
        self._min_interval = float(min_interval)
        self._step_pct = float(step_pct)
        self._label = label
        self._clock = clock
        self._started = clock()
        self._last_emit_at = None
        self._last_pct = -1.0

    def update(self, done: int, total: int, force: bool = False) -> bool:
        """Maybe emit an edit. Returns True when one was emitted."""
        total = max(1, int(total))
        pct = max(0, min(int(done), total)) * 100.0 / total
        now = self._clock()
        if not force and self._last_emit_at is not None:
            complete = done >= total
            step_ok = pct - self._last_pct >= self._step_pct
            time_ok = now - self._last_emit_at >= self._min_interval
            if not complete and not (step_ok or time_ok):
                return False
        text = format_progress(done, total,
                               elapsed=now - self._started,
                               label=self._label)
        try:
            self._edit_fn(text)
        except Exception:
            return False
        self._last_emit_at = now
        self._last_pct = pct
        return True
