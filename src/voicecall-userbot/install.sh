#!/usr/bin/env bash
set -Eeuo pipefail

cd "$(dirname "$0")"

green='\033[0;32m'
yellow='\033[1;33m'
red='\033[0;31m'
clear='\033[0m'

ok() { printf "%b%s%b\n" "$green" "$1" "$clear"; }
warn() { printf "%b%s%b\n" "$yellow" "$1" "$clear"; }
fail() { printf "%b%s%b\n" "$red" "$1" "$clear" >&2; exit 1; }

printf "\nNasb-e asan-e Telegram Voice Call Panel\n\n"

PYTHON_CMD=""
for candidate in \
  python3.13 python3.12 python3.11 python3.10 \
  /opt/alt/python313/bin/python3 /opt/alt/python312/bin/python3 \
  /opt/alt/python311/bin/python3 /opt/alt/python310/bin/python3 \
  python3 python; do
  if command -v "$candidate" >/dev/null 2>&1 \
    && "$candidate" -c 'import sys; raise SystemExit(sys.version_info < (3, 10))' 2>/dev/null; then
    PYTHON_CMD="$candidate"
    break
  fi
done
[[ -n "$PYTHON_CMD" ]] || fail "Python 3.10 ya balatar peyda nashod. Dar cPanel az Setup Python App an ra faal konid."
ok "Python shenasaei shod: $($PYTHON_CMD --version 2>&1)"

if command -v ffmpeg >/dev/null 2>&1; then
  ok "FFmpeg shenasaei shod."
else
  warn "System FFmpeg nasb nist; installer yek FFmpeg local nasb mikonad."
fi

printf "Dar hale sakht-e venv va nasb-e package-ha...\n"
"$PYTHON_CMD" -m venv venv || fail "Sakht-e venv namovafagh bod. Momken ast venv rooye host faal nabashad."
VENV_PYTHON="$PWD/venv/bin/python"
"$VENV_PYTHON" -m pip install --upgrade pip
"$VENV_PYTHON" -m pip install -r requirements.txt
ok "Package-ha nasb shodand."

mkdir -p .runtime-bin
LOCAL_FFMPEG="$($VENV_PYTHON -c 'import imageio_ffmpeg; print(imageio_ffmpeg.get_ffmpeg_exe())')"
ln -sfn "$LOCAL_FFMPEG" .runtime-bin/ffmpeg
chmod +x "$LOCAL_FFMPEG" .runtime-bin/ffmpeg
if .runtime-bin/ffmpeg -version >/dev/null 2>&1; then
  ok "FFmpeg local amade ast."
else
  fail "FFmpeg local rooye in host ghabel-e ejra nist. Ba support-e host tamas begirid."
fi

read -r -p "API_ID: " API_ID_VALUE
read -r -s -p "API_HASH: " API_HASH_VALUE; printf "\n"
read -r -s -p "BotFather token: " BOT_TOKEN_VALUE; printf "\n"
read -r -p "Owner numeric Telegram ID: " OWNER_ID_VALUE

[[ "$API_ID_VALUE" =~ ^[0-9]+$ ]] || fail "API_ID bayad adadi bashad."
[[ "$OWNER_ID_VALUE" =~ ^[0-9]+$ ]] || fail "OWNER_ID bayad adadi bashad."
[[ -n "$API_HASH_VALUE" ]] || fail "API_HASH khali ast."
[[ -n "$BOT_TOKEN_VALUE" ]] || fail "Bot token khali ast."

export SETUP_API_ID="$API_ID_VALUE"
export SETUP_API_HASH="$API_HASH_VALUE"
export SETUP_BOT_TOKEN="$BOT_TOKEN_VALUE"
export SETUP_OWNER_ID="$OWNER_ID_VALUE"
"$VENV_PYTHON" - <<'PY'
import os
from pathlib import Path

values = {
    "API_ID": os.environ["SETUP_API_ID"],
    "API_HASH": os.environ["SETUP_API_HASH"],
    "SESSION_STRING": "",
    "BOT_TOKEN": os.environ["SETUP_BOT_TOKEN"],
    "OWNER_ID": os.environ["SETUP_OWNER_ID"],
    "ADMIN_IDS": "",
    "ALLOWED_CHAT_IDS": "",
    "AUTO_JOIN": "true",
    "SCAN_INTERVAL": "20",
    "DOWNLOAD_DIR": "downloads",
    "DATA_DIR": "data",
    "LOG_LEVEL": "INFO",
    "SUBSCRIPTION_END": "",
    "SUPPORT_URL": "",
    "CHANNEL_URL": "",
}
Path(".env").write_text(
    "\n".join(f"{key}={value}" for key, value in values.items()) + "\n",
    encoding="utf-8",
)
PY
unset SETUP_API_ID SETUP_API_HASH SETUP_BOT_TOKEN SETUP_OWNER_ID
chmod 600 .env
ok "Tanzimat-e avalie zakhire shod."

chmod +x install.sh botctl
"$VENV_PYTHON" -m unittest discover -s tests -v
ok "Safety test anjam shod."

read -r -p "Bot alan dar background ejra shavad? [Y/n] " START_NOW
if [[ ! "$START_NOW" =~ ^[Nn]$ ]]; then
  ./botctl start
fi

printf "\nNasb tamam shod. Modiriat-e bot:\n"
printf "  ./botctl start    ejra\n"
printf "  ./botctl stop     tavaqof\n"
printf "  ./botctl restart  restart\n"
printf "  ./botctl status   vaziyat\n"
printf "  ./botctl logs     live logs\n\n"
printf "Dar Telegram bot-e khod ra baz konid va /start befrestid; vorood-e account ba QR anjam mishavad.\n\n"
