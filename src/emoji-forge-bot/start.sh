#!/usr/bin/env bash
# شروع ربات در پس‌زمینه (بدون systemd) — Start the bot with nohup.
set -euo pipefail
cd "$(dirname "$(readlink -f "$0")")"
mkdir -p logs data
if [ -f logs/bot.pid ] && kill -0 "$(cat logs/bot.pid)" 2>/dev/null; then
  echo "ربات از قبل در حال اجراست (PID $(cat logs/bot.pid))."
  exit 0
fi
if [ -f .env ]; then set -a; . ./.env; set +a; fi
if [ -z "${EMOJI_FORGE_TOKEN:-}" ]; then
  echo "خطا: EMOJI_FORGE_TOKEN در .env تنظیم نشده است." >&2
  exit 2
fi
nohup .venv/bin/python run.py >> logs/bot.log 2>&1 &
echo $! > logs/bot.pid
echo "ربات شروع شد (PID $!). لاگ: logs/bot.log"
