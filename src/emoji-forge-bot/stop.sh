#!/usr/bin/env bash
# توقف رباتی که با start.sh اجرا شده است — Stop the nohup-started bot.
set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
if [ -f logs/bot.pid ]; then
  PID="$(cat logs/bot.pid)"
  if kill -0 "$PID" 2>/dev/null; then
    kill "$PID" && echo "ربات متوقف شد (PID $PID)."
  else
    echo "پروسه‌ای با PID $PID در حال اجرا نیست."
  fi
  rm -f logs/bot.pid
else
  echo "فایل logs/bot.pid نیست؛ ربات با start.sh اجرا نشده یا قبلاً متوقف شده است."
  echo "اگر با systemd نصب کرده‌اید: sudo systemctl stop tz-emojis"
fi
