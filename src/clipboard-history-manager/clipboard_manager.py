#!/usr/bin/env python3
"""Clipboard History Manager — مدیریت تاریخچه‌ی کلیپ‌بورد.

A lightweight desktop app (tkinter, no heavy deps) that keeps a searchable
history of everything you copy — text snippets, links, commands — and lets
you re-copy or pin favorites with one click.

Features:
  * background clipboard polling (0.8 s), deduplicated, newest on top
  * instant search filter, pin favorites, delete, clear all
  * history persisted to JSON (last 500 entries) across restarts
  * dark UI, keyboard shortcuts: Ctrl+F search, Del delete, Enter re-copy

Env / paths:
    History file: ~/.clipboard_history.json (override with CLIPBOARD_HISTORY_FILE)
"""

from __future__ import annotations

import json
import os
import sys
import time
import tkinter as tk
from pathlib import Path
from tkinter import ttk

try:
    import pyperclip
except ImportError:  # pragma: no cover
    print("pyperclip is required:  pip install pyperclip", file=sys.stderr)
    raise SystemExit(1)

MAX_ENTRIES = 500
POLL_MS = 800

BG = "#0d1117"
PANEL = "#161b22"
PANEL_2 = "#1c2129"
BORDER = "#30363d"
TEXT = "#e6edf3"
MUTED = "#8b949e"
ACCENT = "#4493f8"
DANGER = "#f85149"
PIN = "#e3b341"


class HistoryStore:
    """JSON-backed clipboard history with pins and dedup."""

    def __init__(self, path: Path, limit: int = MAX_ENTRIES) -> None:
        self.path = path
        self.limit = limit
        self.entries: list[dict] = []  # {"text": str, "ts": int, "pinned": bool}
        self._load()

    def _load(self) -> None:
        try:
            data = json.loads(self.path.read_text(encoding="utf-8"))
            if isinstance(data, list):
                self.entries = [e for e in data if isinstance(e, dict) and "text" in e][: self.limit]
        except (OSError, ValueError):
            self.entries = []

    def save(self) -> None:
        try:
            self.path.write_text(
                json.dumps(self.entries[: self.limit], ensure_ascii=False, indent=1),
                encoding="utf-8",
            )
        except OSError as exc:
            print(f"could not save history: {exc}", file=sys.stderr)

    def add(self, text: str) -> bool:
        """Add a copied text; returns True if the list changed (new or moved)."""
        text = text.strip()
        if not text:
            return False
        for entry in self.entries:
            if entry["text"] == text:
                entry["ts"] = int(time.time())
                if not entry.get("pinned"):
                    self.entries.remove(entry)
                    self.entries.insert(0, entry)
                    return "moved"
                return False
        self.entries.insert(0, {"text": text, "ts": int(time.time()), "pinned": False})
        del self.entries[self.limit :]
        return True

    def toggle_pin(self, text: str) -> bool:
        for entry in self.entries:
            if entry["text"] == text:
                entry["pinned"] = not entry.get("pinned", False)
                return entry["pinned"]
        return False

    def delete(self, text: str) -> None:
        self.entries = [e for e in self.entries if e["text"] != text]

    def clear(self, keep_pins: bool = True) -> None:
        self.entries = [e for e in self.entries if keep_pins and e.get("pinned")]

    def filtered(self, query: str) -> list[dict]:
        q = query.strip().lower()
        items = [e for e in self.entries if q in e["text"].lower()] if q else list(self.entries)
        items.sort(key=lambda e: not e.get("pinned", False))  # pins first
        return items


class App(tk.Tk):
    def __init__(self) -> None:
        super().__init__()
        self.title("Clipboard History — تاریخچه کلیپ‌بورد")
        self.geometry("680x520")
        self.minsize(520, 380)
        self.configure(bg=BG)

        history_file = Path(os.getenv("CLIPBOARD_HISTORY_FILE", str(Path.home() / ".clipboard_history.json")))
        self.store = HistoryStore(history_file)
        self.last_clip = ""

        self._build_ui()
        self._refresh()
        self.after(POLL_MS, self._poll_clipboard)

    # ---------- UI ----------
    def _build_ui(self) -> None:
        style = ttk.Style(self)
        style.theme_use("clam")
        style.configure("TFrame", background=BG)
        style.configure("TButton", background=PANEL_2, foreground=TEXT, bordercolor=BORDER,
                        focuscolor=ACCENT, padding=6, relief="flat")
        style.map("TButton", background=[("active", BORDER)])
        style.configure("Accent.TButton", background=ACCENT, foreground="#ffffff")
        style.map("Accent.TButton", background=[("active", "#5aa2f8")])

        top = ttk.Frame(self)
        top.pack(fill="x", padx=12, pady=(12, 6))

        self.search_var = tk.StringVar()
        self.search_var.trace_add("write", lambda *_: self._refresh())
        self.search = tk.Entry(
            top, textvariable=self.search_var, bg=PANEL_2, fg=TEXT, insertbackground=TEXT,
            relief="flat", font=("Segoe UI", 12), width=34,
        )
        self.search.pack(side="left", fill="x", expand=True, ipady=6, padx=(0, 8))
        self.search.insert(0, "  🔎  search… (Ctrl+F)")
        self.search.bind("<FocusIn>", self._clear_search_placeholder)
        self.search.bind("<FocusOut>", self._set_search_placeholder)
        self.search.bind("<Control-a>", lambda e: self.search.select_range(0, "end") or "break")

        ttk.Button(top, text="📋 Copy", style="Accent.TButton", command=self._copy_selected).pack(side="left", padx=3)
        ttk.Button(top, text="📌 Pin", command=self._pin_selected).pack(side="left", padx=3)
        ttk.Button(top, text="🗑 Del", command=self._delete_selected).pack(side="left", padx=3)
        ttk.Button(top, text="🧹 Clear (keep pins)", command=self._clear_all).pack(side="left", padx=3)

        self.count_var = tk.StringVar(value="")
        count_label = tk.Label(self, textvariable=self.count_var, bg=BG, fg=MUTED, font=("Segoe UI", 10), anchor="e")
        count_label.pack(fill="x", padx=14)

        frame = tk.Frame(self, bg=BG)
        frame.pack(fill="both", expand=True, padx=12, pady=(4, 0))

        self.listbox = tk.Listbox(
            frame, bg=PANEL, fg=TEXT, relief="flat", highlightthickness=1,
            highlightbackground=BORDER, selectbackground=ACCENT, selectforeground="#ffffff",
            font=("Consolas", 11), activestyle="none",
        )
        scrollbar = ttk.Scrollbar(frame, orient="vertical", command=self.listbox.yview)
        self.listbox.configure(yscrollcommand=scrollbar.set)
        self.listbox.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        self.listbox.bind("<Double-Button-1>", lambda _e: self._copy_selected())
        self.listbox.bind("<Return>", lambda _e: self._copy_selected())
        self.listbox.bind("<Delete>", lambda _e: self._delete_selected())

        self.status_var = tk.StringVar(value="watching clipboard…")
        status = tk.Label(self, textvariable=self.status_var, bg=BG, fg=MUTED,
                          font=("Segoe UI", 10), anchor="w")
        status.pack(fill="x", padx=14, pady=(4, 10))

        self.bind("<Control-f>", lambda _e: self.search.focus_set())
        self.protocol("WM_DELETE_WINDOW", self._on_close)

    def _clear_search_placeholder(self, _e=None) -> None:
        if self.search.get().startswith("  🔎"):
            self.search.delete(0, "end")

    def _set_search_placeholder(self, _e=None) -> None:
        if not self.search.get():
            self.search.insert(0, "  🔎  search… (Ctrl+F)")

    # ---------- clipboard ----------
    def _poll_clipboard(self) -> None:
        try:
            clip = pyperclip.paste() or ""
        except pyperclip.PyperclipException:
            clip = ""
        if clip and clip != self.last_clip:
            self.last_clip = clip
            if self.store.add(clip):
                self.store.save()
                self._refresh(flash="copied to history ✓")
        self.after(POLL_MS, self._poll_clipboard)

    # ---------- list ----------
    def _refresh(self, flash: str | None = None) -> None:
        selected_text = self._selected_text()
        self.listbox.delete(0, "end")
        items = self.store.filtered(self.search_var.get())
        for entry in items:
            preview = entry["text"].replace("\n", " ⏎ ")
            if len(preview) > 76:
                preview = preview[:73] + "…"
            prefix = "📌 " if entry.get("pinned") else "    "
            self.listbox.insert("end", f"{prefix}{preview}")
        if selected_text:
            for i, entry in enumerate(items):
                if entry["text"] == selected_text:
                    self.listbox.selection_set(i)
                    self.listbox.see(i)
                    break
        total = len(self.store.entries)
        shown = len(items)
        self.count_var.set(f"{shown} shown · {total} stored (max {MAX_ENTRIES})")
        if flash:
            self.status_var.set(flash)
        elif items:
            self.status_var.set("watching clipboard… · double-click or Enter to re-copy")
        else:
            self.status_var.set("nothing here yet — copy something anywhere")

    def _selected_text(self) -> str | None:
        selection = self.listbox.curselection()
        if not selection:
            return None
        items = self.store.filtered(self.search_var.get())
        if selection[0] < len(items):
            return items[selection[0]]["text"]
        return None

    # ---------- actions ----------
    def _copy_selected(self) -> None:
        text = self._selected_text()
        if text:
            pyperclip.copy(text)
            self.last_clip = text
            self.store.add(text)
            self.store.save()
            self._refresh(flash="copied ✓  ({} chars)".format(len(text)))

    def _pin_selected(self) -> None:
        text = self._selected_text()
        if text:
            pinned = self.store.toggle_pin(text)
            self.store.save()
            self._refresh(flash=("pinned 📌" if pinned else "unpinned"))

    def _delete_selected(self) -> None:
        text = self._selected_text()
        if text:
            self.store.delete(text)
            self.store.save()
            self._refresh(flash="deleted")

    def _clear_all(self) -> None:
        self.store.clear(keep_pins=True)
        self.store.save()
        self._refresh(flash="history cleared — pins kept 📌")

    def _on_close(self) -> None:
        self.store.save()
        self.destroy()


def main() -> None:
    App().mainloop()


if __name__ == "__main__":
    main()
