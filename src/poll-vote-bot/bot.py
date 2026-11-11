#!/usr/bin/env python3
"""Poll & Vote Bot — نظرسنجی و رأی‌گیری برای تلگرام.

A dependency-light Telegram bot for building polls inside groups and private chats:

  * /newpoll wizard — question, options, single/multi choice, anonymous/public
  * inline voting with live counters in the buttons
  * rendered result cards (RTL-aware bar chart, PNG) via Pillow
  * close / delete / CSV export, per-poll ownership

Only `requests` + `Pillow` are required; there is no bot framework, so the
whole flow is easy to audit and to extend. State lives in a single SQLite
file (pollbot.db by default).

Env vars (see .env.example):
    BOT_TOKEN      — token from @BotFather (required)
    POLL_DB_PATH   — sqlite path (default: pollbot.db next to this file)
    FONT_PATH      — TTF used for the result card (default: bundled Vazirmatn)
"""

from __future__ import annotations

import csv
import io
import os
import sqlite3
import sys
import time
from pathlib import Path

import requests
from PIL import Image, ImageDraw, ImageFont

BASE_DIR = Path(__file__).resolve().parent
BOT_TOKEN = os.getenv("BOT_TOKEN", "").strip()
DB_PATH = Path(os.getenv("POLL_DB_PATH", str(BASE_DIR / "pollbot.db")))
API = f"https://api.telegram.org/bot{BOT_TOKEN}"

ACCENT = (68, 147, 248)
ACCENT_2 = (188, 140, 255)
BG = (13, 17, 23)
PANEL = (22, 27, 34)
PANEL_2 = (28, 33, 41)
BORDER = (48, 54, 61)
TEXT = (230, 237, 243)
MUTED = (139, 148, 158)
GREEN = (63, 185, 80)

TEXT_EN = {
    "start": "🗳 <b>Poll &amp; Vote Bot</b>\n\nCreate polls with live results inside any group or chat.\n\n<b>Commands</b>\n/newpoll — build a poll (wizard)\n/mypolls — your polls\n/help — how it works",
    "help": "ℹ️ <b>How it works</b>\n\n1. Send /newpoll\n2. Send the <b>question</b>\n3. Send <b>2–10 options</b>, one per line\n4. Pick single/multi choice + anonymous/public\n5. Publish — votes update the buttons live\n\n📊 Tap “Results” any time for the rendered chart.\nOnly the poll owner can close, delete or export.",
    "ask_question": "❓ Send the <b>question</b> now (or /cancel):",
    "ask_options": "📝 Now send <b>2–10 options</b> — one per line:\n<i>like:</i>\n<code>Red\nGreen\nBlue</code>",
    "bad_options": "⚠️ Please send between 2 and 10 non-empty lines.",
    "settings": "⚙️ <b>{question}</b>\n\nChoose the poll settings, then publish:",
    "published": "✅ Poll published! Forward it anywhere — voting works in groups too.",
    "cancelled": "❌ Cancelled.",
    "no_polls": "You have no polls yet — send /newpoll.",
    "my_polls": "📋 <b>Your polls</b>",
    "closed": "🔒 Poll closed.",
    "deleted": "🗑 Poll deleted.",
    "csv_sent": "📄 CSV exported.",
    "not_owner": "⚠️ Only the poll owner can do that.",
    "poll_missing": "⚠️ This poll no longer exists.",
    "closed_votes": "🔒 Voting is closed.",
    "voted": "✅ Vote saved!",
    "card_caption": "📊 <b>{question}</b>\n👥 {total} votes · {mode} · {status}",
    "mode_multi": "multi-choice",
    "mode_single": "single-choice",
    "open": "open",
    "closed_st": "closed",
}

TEXT_FA = {
    "start": "🗳 <b>ربات نظرسنجی و رأی‌گیری</b>\n\nنظرسنجی با نتایج زنده در هر گروه یا چتی بسازید.\n\n<b>دستورات</b>\n/newpoll — ساخت نظرسنجی (مرحله‌به‌مرحله)\n/mypolls — نظرسنجی‌های شما\n/help — راهنما",
    "help": "ℹ️ <b>راهنما</b>\n\n۱. دستور /newpoll را بفرستید\n۲. <b>سؤال</b> را بفرستید\n۳. <b>۲ تا ۱۰ گزینه</b> را هر کدام در یک خط بفرستید\n۴. تک‌انتخابی/چندانتخابی و ناشناس/عمومی را انتخاب کنید\n۵. منتشر کنید — شمارش رأی‌ها همان‌لحظه روی دکمه‌ها به‌روز می‌شود\n\n📊 هر وقت خواستید «نتایج» را بزنید تا کارت تصویری ارسال شود.\nفقط سازنده می‌تواند نظرسنجی را ببندد، حذف کند یا خروجی بگیرد.",
    "ask_question": "❓ حالا <b>سؤال</b> را بفرستید (یا /cancel):",
    "ask_options": "📝 حالا <b>۲ تا ۱۰ گزینه</b> را بفرستید — هر گزینه در یک خط:\n<i>مثلاً:</i>\n<code>قرمز\nسبز\nآبی</code>",
    "bad_options": "⚠️ لطفاً بین ۲ تا ۱۰ گزینه‌ی غیرخالی بفرستید.",
    "settings": "⚙️ <b>{question}</b>\n\nتنظیمات نظرسنجی را انتخاب و منتشر کنید:",
    "published": "✅ نظرسنجی منتشر شد! هرجا فورواردش کنید، رأی‌گیری همان‌جا هم کار می‌کند.",
    "cancelled": "❌ لغو شد.",
    "no_polls": "هنوز نظرسنجی‌ای نساخته‌اید — /newpoll را بفرستید.",
    "my_polls": "📋 <b>نظرسنجی‌های شما</b>",
    "closed": "🔒 نظرسنجی بسته شد.",
    "deleted": "🗑 نظرسنجی حذف شد.",
    "csv_sent": "📄 فایل CSV ارسال شد.",
    "not_owner": "⚠️ فقط سازنده‌ی نظرسنجی می‌تواند این کار را بکند.",
    "poll_missing": "⚠️ این نظرسنجی دیگر وجود ندارد.",
    "closed_votes": "🔒 رأی‌گیری بسته است.",
    "voted": "✅ رأی شما ثبت شد!",
    "vote_added": "✅ رأی اضافه شد", "vote_removed": "↩️ رأی حذف شد", "vote_moved": "✅ رأی شما به‌روز شد",
    "card_caption": "📊 <b>{question}</b>\n👥 {total} رأی · {mode} · {status}",
    "mode_multi": "چندانتخابی",
    "mode_single": "تک‌انتخابی",
    "open": "باز",
    "closed_st": "بسته",
    "ui_question": "سؤال نظرسنجی",
    "btn_single": "☑️ تک‌انتخابی", "btn_multi": "🔘 چندانتخابی",
    "btn_anon": "🕵️ ناشناس", "btn_public": "🙋 عمومی",
    "btn_publish": "✅ انتشار",
    "btn_results": "📊 نتایج", "btn_close": "🔒 بستن", "btn_delete": "🗑 حذف", "btn_csv": "📄 CSV",
}

# ---------- database ----------

def db() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db() -> None:
    with db() as conn:
        conn.executescript(
            """
            CREATE TABLE IF NOT EXISTS polls (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                owner_id   INTEGER NOT NULL,
                owner_name TEXT DEFAULT '',
                question   TEXT NOT NULL,
                multi      INTEGER NOT NULL DEFAULT 0,
                anonymous  INTEGER NOT NULL DEFAULT 1,
                is_open    INTEGER NOT NULL DEFAULT 1,
                created_at INTEGER NOT NULL
            );
            CREATE TABLE IF NOT EXISTS options (
                poll_id INTEGER NOT NULL,
                idx     INTEGER NOT NULL,
                text    TEXT NOT NULL,
                PRIMARY KEY (poll_id, idx)
            );
            CREATE TABLE IF NOT EXISTS votes (
                poll_id  INTEGER NOT NULL,
                idx      INTEGER NOT NULL,
                user_id  INTEGER NOT NULL,
                name     TEXT DEFAULT '',
                voted_at INTEGER NOT NULL,
                PRIMARY KEY (poll_id, idx, user_id)
            );
            """
        )


def create_poll(owner_id: int, owner_name: str, question: str, options: list[str], multi: bool, anonymous: bool) -> int:
    with db() as conn:
        cur = conn.execute(
            "INSERT INTO polls (owner_id, owner_name, question, multi, anonymous, created_at) VALUES (?,?,?,?,?,?)",
            (owner_id, owner_name, question, int(multi), int(anonymous), int(time.time())),
        )
        poll_id = cur.lastrowid
        conn.executemany(
            "INSERT INTO options (poll_id, idx, text) VALUES (?,?,?)",
            [(poll_id, i, text) for i, text in enumerate(options)],
        )
    return poll_id


def get_poll(poll_id: int):
    with db() as conn:
        poll = conn.execute("SELECT * FROM polls WHERE id=?", (poll_id,)).fetchone()
        options = conn.execute("SELECT idx, text FROM options WHERE poll_id=? ORDER BY idx", (poll_id,)).fetchall()
    return poll, options


def tallies(poll_id: int, n_options: int) -> list[int]:
    with db() as conn:
        rows = conn.execute("SELECT idx, COUNT(*) c FROM votes WHERE poll_id=? GROUP BY idx", (poll_id,)).fetchall()
    counts = [0] * n_options
    for row in rows:
        counts[row["idx"]] = row["c"]
    return counts


def unique_voters(poll_id: int) -> int:
    with db() as conn:
        row = conn.execute("SELECT COUNT(DISTINCT user_id) c FROM votes WHERE poll_id=?", (poll_id,)).fetchone()
    return row["c"]


def cast_vote(poll_id: int, idx: int, user_id: int, name: str, multi: bool, anonymous: bool) -> str:
    """Apply a vote click. Multi-choice clicks toggle; single-choice clicks move.
    Returns 'added' | 'removed' | 'moved'."""
    now = int(time.time())
    with db() as conn:
        if multi:
            existing = conn.execute(
                "SELECT 1 FROM votes WHERE poll_id=? AND idx=? AND user_id=?", (poll_id, idx, user_id)
            ).fetchone()
            if existing:
                conn.execute("DELETE FROM votes WHERE poll_id=? AND idx=? AND user_id=?", (poll_id, idx, user_id))
                return "removed"
            conn.execute(
                "INSERT INTO votes (poll_id, idx, user_id, name, voted_at) VALUES (?,?,?,?,?)",
                (poll_id, idx, user_id, "" if anonymous else name, now),
            )
            return "added"
        conn.execute("DELETE FROM votes WHERE poll_id=? AND user_id=?", (poll_id, user_id))
        conn.execute(
            "INSERT INTO votes (poll_id, idx, user_id, name, voted_at) VALUES (?,?,?,?,?)",
            (poll_id, idx, user_id, "" if anonymous else name, now),
        )
        return "moved"


def poll_keyboard(poll_id: int, options, counts: list[int], multi: bool) -> dict:
    rows = []
    for i, opt in enumerate(options):
        mark = "●" if counts[i] else "○"
        rows.append([{"text": f"{opt['text']}  {mark} {counts[i]}", "callback_data": f"vote:{poll_id}:{i}"}])
    rows.append([{"text": "📊 نتایج", "callback_data": f"results:{poll_id}"}])
    return {"inline_keyboard": rows}


def owner_keyboard(poll_id: int) -> dict:
    return {
        "inline_keyboard": [[
            {"text": "📊 نتایج", "callback_data": f"results:{poll_id}"},
            {"text": "🔒 بستن", "callback_data": f"close:{poll_id}"},
            {"text": "📄 CSV", "callback_data": f"csv:{poll_id}"},
            {"text": "🗑 حذف", "callback_data": f"delete:{poll_id}"},
        ]]
    }


# ---------- result card (RTL-aware, Pillow + Raqm) ----------

def load_font(size: int) -> ImageFont.FreeTypeFont:
    candidates = [
        os.getenv("FONT_PATH", ""),
        str(BASE_DIR / "Vazirmatn-Bold.ttf"),
        "/usr/share/fonts/truetype/vazirmatn/Vazirmatn-Bold.ttf",
    ]
    for path in candidates:
        if path and Path(path).exists():
            return ImageFont.truetype(path, size)
    return ImageFont.load_default()


FA_DIGITS = str.maketrans("0123456789", "۰۱۲۳۴۵۶۷۸۹")


def fa_num(value) -> str:
    return str(value).translate(FA_DIGITS)


def render_results(question: str, option_texts: list[str], counts: list[int], *, anonymous: bool, is_open: bool) -> bytes:
    """Draw the result card: title bar, RTL bars with counts and percentages."""
    total = max(sum(counts), 1)
    W, pad = 900, 36
    row_h, bar_h = 84, 34
    head_h = 148
    H = head_h + len(option_texts) * row_h + 96
    img = Image.new("RGB", (W, H), BG)
    draw = ImageDraw.Draw(img)

    f_title = load_font(40)
    f_opt = load_font(30)
    f_num = load_font(28)
    f_small = load_font(22)

    # header panel
    draw.rounded_rectangle([pad, pad, W - pad, head_h - 16], radius=18, fill=PANEL, outline=BORDER, width=2)
    draw.text((W - pad - 24, pad + 18), "🗳 نتایج نظرسنجی", font=f_title, fill=TEXT, anchor="ra")
    status = "باز" if is_open else "بسته"
    dot = GREEN if is_open else (248, 81, 73)
    total_votes = sum(counts)
    meta = f"{fa_num(total_votes)} رأی · {'ناشناس' if anonymous else 'عمومی'} · {status}"
    draw.text((W - pad - 24, pad + 78), meta, font=f_small, fill=MUTED, anchor="ra")
    meta_w = draw.textlength(meta, font=f_small)
    draw.ellipse([W - pad - 24 - meta_w - 22, pad + 84, W - pad - 24 - meta_w - 8, pad + 98], fill=dot)

    # question
    q = question if len(question) <= 60 else question[:57] + "…"
    draw.text((W / 2, head_h + 6), q, font=f_opt, fill=TEXT, anchor="ma")

    colors = [ACCENT, ACCENT_2, GREEN, (248, 189, 72), (248, 81, 73), (94, 205, 228), (219, 109, 216), (121, 192, 255), (255, 166, 128), (152, 216, 140)]
    max_count = max(counts) if counts else 0
    bar_area = W - pad * 2 - 150  # leave room for the count label on the right

    y = head_h + 52
    for i, text in enumerate(option_texts):
        label = text if len(text) <= 34 else text[:31] + "…"
        draw.text((W - pad - 8, y + 2), label, font=f_opt, fill=TEXT, anchor="ra")

        w = int(bar_area * (counts[i] / max_count)) if max_count else 0
        draw.rounded_rectangle(
            [pad + 8, y + 44, pad + 8 + bar_area, y + 44 + bar_h],
            radius=10, fill=PANEL_2, outline=BORDER, width=1,
        )
        if w > 18:
            draw.rounded_rectangle(
                [pad + 8, y + 44, pad + 8 + w, y + 44 + bar_h],
                radius=10, fill=colors[i % len(colors)],
            )
        pct = int(round(counts[i] * 100 / total))
        draw.text((W - pad - 8, y + 46), f"{fa_num(counts[i])} ({fa_num(pct)}٪)", font=f_num, fill=MUTED, anchor="ra")
        y += row_h

    draw.text((W / 2, H - 34), "@Abolfazl_rwm", font=f_small, fill=(94, 104, 122), anchor="mm")

    buf = io.BytesIO()
    img.save(buf, "PNG", optimize=True)
    return buf.getvalue()


def build_csv(poll_id: int) -> bytes:
    poll, options = get_poll(poll_id)
    with db() as conn:
        rows = conn.execute(
            "SELECT idx, user_id, name, voted_at FROM votes WHERE poll_id=? ORDER BY voted_at", (poll_id,)
        ).fetchall()
    buf = io.StringIO()
    writer = csv.writer(buf)
    writer.writerow(["poll_id", "question", "option", "user_id", "name", "voted_at_iso"])
    for row in rows:
        writer.writerow([
            poll_id, poll["question"], options[row["idx"]]["text"],
            row["user_id"], row["name"],
            time.strftime("%Y-%m-%d %H:%M:%S", time.gmtime(row["voted_at"])),
        ])
    return buf.getvalue().encode("utf-8-sig")


# ---------- telegram api ----------

def call(method: str, payload: dict | None = None, files: dict | None = None, retries: int = 3) -> dict:
    for attempt in range(retries):
        try:
            resp = requests.post(f"{API}/{method}", json=payload, files=files, timeout=45)
            data = resp.json()
            if data.get("ok"):
                return data
            desc = str(data.get("description", ""))
            if "message is not modified" in desc:
                return data
            print(f"[tg] {method} failed: {desc}", flush=True)
            return data
        except (requests.RequestException, ValueError) as exc:
            if attempt == retries - 1:
                print(f"[tg] {method} error: {exc}", flush=True)
                return {"ok": False, "description": str(exc)}
            time.sleep(2 * (attempt + 1))
    return {"ok": False}


def send(chat_id: int, text: str, reply_markup: dict | None = None) -> None:
    call("sendMessage", {"chat_id": chat_id, "text": text, "parse_mode": "HTML",
                         "reply_markup": reply_markup, "disable_web_page_preview": True})


def answer_cb(cb_id: str, text: str = "") -> None:
    call("answerCallbackQuery", {"callback_query_id": cb_id, "text": text})


def tr(key: str, **kw) -> str:
    return TEXT_FA[key].format(**kw)


# ---------- wizard state ----------

WIZARD: dict[int, dict] = {}


def settings_keyboard(state: dict) -> dict:
    return {"inline_keyboard": [
        [{"text": "☑️ تک‌انتخابی" if not state["multi"] else "🔘 چندانتخابی",
          "callback_data": "wiz:multi"},
         {"text": "🕵️ ناشناس" if state["anonymous"] else "🙋 عمومی",
          "callback_data": "wiz:anon"}],
        [{"text": "✅ انتشار", "callback_data": "wiz:publish"}],
    ]}


def publish_poll(owner_id: int, owner_name: str, chat_id: int, message_id: int) -> None:
    state = WIZARD[owner_id]
    poll_id = create_poll(owner_id, owner_name, state["question"], state["options"], state["multi"], state["anonymous"])
    poll, options = get_poll(poll_id)
    counts = [0] * len(options)
    call("editMessageText", {
        "chat_id": chat_id, "message_id": message_id,
        "text": f"🗳 <b>{state['question']}</b>",
        "parse_mode": "HTML",
        "reply_markup": poll_keyboard(poll_id, options, counts, state["multi"]),
    })
    send(chat_id, tr("published"))
    WIZARD.pop(owner_id, None)


def refresh_poll_message(chat_id: int, message_id: int, poll_id: int) -> None:
    poll, options = get_poll(poll_id)
    if not poll:
        return
    counts = tallies(poll_id, len(options))
    call("editMessageReplyMarkup", {
        "chat_id": chat_id, "message_id": message_id,
        "reply_markup": poll_keyboard(poll_id, options, counts, bool(poll["multi"])),
    })


def send_results(chat_id: int, poll_id: int, viewer_id: int) -> None:
    poll, options = get_poll(poll_id)
    if not poll:
        send(chat_id, tr("poll_missing"))
        return
    counts = tallies(poll_id, len(options))
    png = render_results(
        poll["question"], [o["text"] for o in options], counts,
        anonymous=bool(poll["anonymous"]), is_open=bool(poll["is_open"]),
    )
    caption = tr(
        "card_caption", question=poll["question"], total=sum(counts),
        mode=tr("mode_multi" if poll["multi"] else "mode_single"),
        status=tr("open" if poll["is_open"] else "closed_st"),
    )
    markup = owner_keyboard(poll_id) if poll["owner_id"] == viewer_id else None
    call("sendPhoto", {"chat_id": chat_id, "caption": caption, "parse_mode": "HTML",
                       "reply_markup": markup},
         files={"photo": ("results.png", png, "image/png")})


# ---------- handlers ----------

def handle_message(msg: dict) -> None:
    chat_id = msg["chat"]["id"]
    user = msg.get("from", {})
    user_id = user.get("id", chat_id)
    name = user.get("first_name", "")
    text = (msg.get("text") or "").strip()
    if not text:
        return

    if text.startswith("/start"):
        send(chat_id, TEXT_FA["start"])
        return
    if text.startswith("/help"):
        send(chat_id, TEXT_FA["help"])
        return
    if text.startswith("/cancel"):
        WIZARD.pop(user_id, None)
        send(chat_id, tr("cancelled"))
        return

    if text.startswith("/newpoll"):
        WIZARD[user_id] = {"step": "question", "chat_id": chat_id, "multi": False, "anonymous": True}
        send(chat_id, tr("ask_question"))
        return

    if text.startswith("/mypolls"):
        with db() as conn:
            rows = conn.execute(
                "SELECT id, question, is_open FROM polls WHERE owner_id=? ORDER BY id DESC LIMIT 10", (user_id,)
            ).fetchall()
        if not rows:
            send(chat_id, tr("no_polls"))
            return
        lines = [tr("my_polls")]
        for row in rows:
            icon = "🟢" if row["is_open"] else "🔒"
            lines.append(f"{icon} #{row['id']} — {row['question'][:40]}")
        send(chat_id, "\n".join(lines))
        return

    state = WIZARD.get(user_id)
    if not state:
        send(chat_id, TEXT_FA["start"])
        return

    if state["step"] == "question":
        state["question"] = text[:200]
        state["step"] = "options"
        send(chat_id, tr("ask_options"))
        return

    if state["step"] == "options":
        options = [line.strip() for line in text.splitlines() if line.strip()]
        if not (2 <= len(options) <= 10):
            send(chat_id, tr("bad_options"))
            return
        state["options"] = options
        state["step"] = "settings"
        send(chat_id, tr("settings", question=state["question"]), settings_keyboard(state))
        return


def handle_callback(cb: dict) -> None:
    data = cb.get("data", "")
    cb_id = cb["id"]
    msg = cb.get("message") or {}
    chat_id = msg.get("chat", {}).get("id")
    message_id = msg.get("message_id")
    user = cb.get("from", {})
    user_id = user.get("id")

    action, _, rest = data.partition(":")

    if action == "wiz":
        state = WIZARD.get(user_id)
        if not state:
            answer_cb(cb_id)
            return
        if rest == "multi":
            state["multi"] = not state["multi"]
        elif rest == "anon":
            state["anonymous"] = not state["anonymous"]
        elif rest == "publish":
            answer_cb(cb_id)
            publish_poll(user_id, user.get("first_name", ""), chat_id, message_id)
            return
        call("editMessageText", {
            "chat_id": chat_id, "message_id": message_id,
            "text": tr("settings", question=state["question"]), "parse_mode": "HTML",
            "reply_markup": settings_keyboard(state),
        })
        answer_cb(cb_id)
        return

    if action == "vote":
        poll_id, _, idx = rest.partition(":")
        poll_id, idx = int(poll_id), int(idx)
        poll, options = get_poll(poll_id)
        if not poll:
            answer_cb(cb_id, tr("poll_missing"))
            return
        if not poll["is_open"]:
            answer_cb(cb_id, tr("closed_votes"))
            return
        result = cast_vote(poll_id, idx, user_id, user.get("first_name", ""), bool(poll["multi"]), bool(poll["anonymous"]))
        counts = tallies(poll_id, len(options))
        call("editMessageReplyMarkup", {
            "chat_id": chat_id, "message_id": message_id,
            "reply_markup": poll_keyboard(poll_id, options, counts, bool(poll["multi"])),
        })
        answer_cb(cb_id, tr("vote_" + result))
        return

    if action == "results":
        answer_cb(cb_id)
        send_results(chat_id, int(rest), user_id)
        return

    poll_id = int(rest)
    poll, _ = get_poll(poll_id)
    if not poll:
        answer_cb(cb_id, tr("poll_missing"))
        return
    if poll["owner_id"] != user_id:
        answer_cb(cb_id, tr("not_owner"))
        return

    if action == "close":
        with db() as conn:
            conn.execute("UPDATE polls SET is_open=0 WHERE id=?", (poll_id,))
        answer_cb(cb_id, tr("closed"))
        refresh_poll_message(chat_id, message_id, poll_id)
    elif action == "delete":
        with db() as conn:
            conn.execute("DELETE FROM votes WHERE poll_id=?", (poll_id,))
            conn.execute("DELETE FROM options WHERE poll_id=?", (poll_id,))
            conn.execute("DELETE FROM polls WHERE id=?", (poll_id,))
        answer_cb(cb_id, tr("deleted"))
        call("deleteMessage", {"chat_id": chat_id, "message_id": message_id})
    elif action == "csv":
        answer_cb(cb_id, tr("csv_sent"))
        call("sendDocument", {"chat_id": chat_id, "caption": tr("csv_sent")},
             files={"document": (f"poll-{poll_id}.csv", build_csv(poll_id), "text/csv")})


def process_update(update: dict) -> None:
    try:
        if "message" in update:
            handle_message(update["message"])
        elif "callback_query" in update:
            handle_callback(update["callback_query"])
    except Exception as exc:  # keep the bot alive on any single-update failure
        print(f"[error] {exc}", flush=True)


def main() -> None:
    if not BOT_TOKEN:
        print("BOT_TOKEN is missing — copy .env.example to .env and fill it in.", file=sys.stderr)
        raise SystemExit(1)
    init_db()
    offset = 0
    print("Poll & Vote Bot is running… (long polling)", flush=True)
    while True:
        data = call("getUpdates", {"offset": offset, "timeout": 30}, retries=1)
        for update in data.get("result", []):
            offset = update["update_id"] + 1
            process_update(update)


if __name__ == "__main__":
    main()
