# Telegram: @Abolfazl_rwm
#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────
# Abolfazl Bot (Emoji Forge) — نصب‌کننده‌ی سرور | Server installer
# Idempotent: اجرای دوباره امن است؛ مقادیر قبلی .env حفظ می‌شوند.
#
# استفاده:
#   ./install.sh                 # نصب تعاملی + تلاش برای systemd
#   ./install.sh --non-interactive --no-start
#
# Override بدون پرسش (از محیط):
#   EMOJI_FORGE_TOKEN=... EMOJI_FORGE_ADMIN_IDS=123,456 \
#   AI_API_KEY=... AI_BASE_URL=https://api.openai.com/v1 AI_MODEL=gpt-4o-mini \
#   ./install.sh --non-interactive
# ─────────────────────────────────────────────────────────────────────
set -euo pipefail

cd "$(dirname "$(readlink -f "$0")")"
ROOT="$(pwd)"
VENV="$ROOT/.venv"
ENV_FILE="$ROOT/.env"
SERVICE_NAME="tz-emojis"
INTERACTIVE=1
DO_START=1
[ -t 0 ] || INTERACTIVE=0
for arg in "$@"; do
  case "$arg" in
    --non-interactive) INTERACTIVE=0 ;;
    --no-start) DO_START=0 ;;
    --help|-h)
      grep '^#' "$0" | head -20; exit 0 ;;
    *) echo "گزینه ناشناخته: $arg (راهنما: ./install.sh --help)"; exit 2 ;;
  esac
done

say()  { printf '\n\033[1;36m== %s\033[0m\n' "$*"; }
ok()   { printf '  \033[1;32m✓\033[0m %s\n' "$*"; }
warn() { printf '  \033[1;33m!\033[0m %s\n' "$*"; }
die()  { printf '\n\033[1;31m✗ خطا:\033[0m %s\n' "$*" >&2; exit 1; }

say "Abolfazl Bot — نصب روی سرور"
echo "  مسیر نصب: $ROOT"

# ── ۱) سیستم‌عامل و پیش‌نیازها ───────────────────────────────────────
say "بررسی سیستم و پیش‌نیازها"
OS_NAME="unknown"
if [ -f /etc/os-release ]; then
  # shellcheck disable=SC1091
  OS_NAME="$(. /etc/os-release; echo "${PRETTY_NAME:-$ID}")"
fi
echo "  سیستم: $OS_NAME ($(uname -s) $(uname -m))"

command -v python3 >/dev/null 2>&1 || die "python3 پیدا نشد. اول نصبش کنید:  sudo apt install -y python3 python3-venv  |  sudo dnf install -y python3"
PY_VER="$(python3 -c 'import sys; print("%d.%d" % sys.version_info[:2])')"
PY_OK="$(python3 -c 'import sys; print(1 if sys.version_info >= (3, 11) else 0)')"
[ "$PY_OK" = "1" ] || die "نسخه پایتون $PY_VER است؛ حداقل 3.11 لازم است."
ok "python3 $PY_VER"

if command -v ffmpeg >/dev/null 2>&1; then
  ok "ffmpeg موجود است"
else
  warn "ffmpeg پیدا نشد — پردازش WEBM غیرفعال می‌ماند. نصب: sudo apt install -y ffmpeg"
fi

# ── ۲) محیط مجازی و وابستگی‌ها ───────────────────────────────────────
say "ساخت محیط مجازی (venv) و نصب وابستگی‌ها"
if [ ! -x "$VENV/bin/python" ]; then
  python3 -m venv "$VENV" || die "ساخت venv ناموفق بود؛ بسته python3-venv را نصب کنید: sudo apt install -y python3-venv"
  ok ".venv ساخته شد"
else
  ok ".venv از قبل موجود است (استفاده مجدد)"
fi
"$VENV/bin/python" -m pip install --quiet --upgrade pip
"$VENV/bin/python" -m pip install --quiet -r "$ROOT/requirements.txt"
ok "وابستگی‌ها نصب شدند (requirements.txt)"

# ── ۳) پیکربندی (.env) ───────────────────────────────────────────────
say "پیکربندی — فایل .env"
# مقدار فعلی از .env خوانده می‌شود تا اجرای دوباره چیزی را پاک نکند.
env_current() {
  local key="$1"
  if [ -f "$ENV_FILE" ]; then
    grep -E "^${key}=" "$ENV_FILE" | tail -1 | cut -d= -f2- || true
  fi
}
ask() { # ask VAR "label" "default" secret?
  local var="$1" label="$2" def="${3:-}" secret="${4:-0}" cur val
  cur="${!var:-}"
  [ -n "$cur" ] && { printf -v "$var" '%s' "$cur"; return; }
  cur="$(env_current "$var")"
  if [ "$INTERACTIVE" = "1" ]; then
    if [ "$secret" = "1" ]; then
      read -r -s -p "  $label [$([ -n "$cur" ] && echo 'تنظیم شده' || echo "$def")]: " val || val=""
      echo
    else
      read -r -p "  $label [${cur:-$def}]: " val || val=""
    fi
    val="${val:-${cur:-$def}}"
  else
    val="${cur:-$def}"
  fi
  printf -v "$var" '%s' "$val"
}

ask EMOJI_FORGE_TOKEN "توکن ربات (از @BotFather)" "" 1
ask EMOJI_FORGE_ADMIN_IDS "آیدی عددی ادمین‌ها (با کاما جدا کنید)" ""
ask AI_API_KEY "کلید API اختصاصی (اختیاری — خالی = غیرفعال)" "" 1
ask AI_BASE_URL "آدرس پایه API (Base URL)" "https://api.openai.com/v1"
ask AI_MODEL "نام مدل" "gpt-4o-mini"
ask PANEL_KEY "کلید ورود پنل وب cPanel (اختیاری)" "" 1

if [ -f "$ENV_FILE" ] && [ ! -f "$ENV_FILE.bak" ]; then
  cp "$ENV_FILE" "$ENV_FILE.bak"
fi
umask 077
{
  echo "EMOJI_FORGE_TOKEN=${EMOJI_FORGE_TOKEN:-}"
  echo "EMOJI_FORGE_ADMIN_IDS=${EMOJI_FORGE_ADMIN_IDS:-}"
  echo "EMOJI_FORGE_DATA_DIR="
  echo "EMOJI_FORGE_MAX_CONCURRENT=2"
  echo "EMOJI_FORGE_MAX_FILE_BYTES=20971520"
  echo "EMOJI_FORGE_BRAIN_PROVIDER=rule"
  echo "EMOJI_FORGE_EXTERNAL_BRAIN_URL="
  echo "EMOJI_FORGE_EXTERNAL_BRAIN_KEY="
  echo "AI_API_KEY=${AI_API_KEY:-}"
  echo "AI_BASE_URL=${AI_BASE_URL:-https://api.openai.com/v1}"
  echo "AI_MODEL=${AI_MODEL:-gpt-4o-mini}"
  echo "PANEL_KEY=${PANEL_KEY:-}"
} > "$ENV_FILE"
chmod 600 "$ENV_FILE"
ok ".env نوشته شد (دسترسی 600)"
[ -n "${EMOJI_FORGE_TOKEN:-}" ] || warn "توکن خالی است — ربات تا وقتی توکن در .env ننویسید اجرا نمی‌شود."

# ── ۴) خودآزمایی ─────────────────────────────────────────────────────
say "خودآزمایی (Self-check)"
set -a; [ -f "$ENV_FILE" ] && . "$ENV_FILE"; set +a
"$VENV/bin/python" -c "
import sys; sys.path.insert(0, 'src')
from emoji_forge.config import load_config
from emoji_forge.selfcheck import readiness_line
print(readiness_line(load_config().data_dir))
" || warn "خودآزمایی هشدار داد؛ جزئیات در خط بالا."

# ── ۵) اجرا: systemd یا fallback ──────────────────────────────────────
say "راه‌اندازی سرویس"
mkdir -p "$ROOT/data" "$ROOT/logs"
STARTED=0
if command -v systemctl >/dev/null 2>&1 && [ -d /run/systemd/system ] && [ "$(id -u)" = "0" ]; then
  RUN_USER="${SUDO_USER:-root}"
  cat > "/etc/systemd/system/${SERVICE_NAME}.service" <<EOF
[Unit]
Description=Abolfazl Bot (Emoji Forge)
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=${RUN_USER}
WorkingDirectory=${ROOT}
EnvironmentFile=${ENV_FILE}
ExecStart=${VENV}/bin/python ${ROOT}/run.py
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF
  systemctl daemon-reload
  systemctl enable "${SERVICE_NAME}.service" >/dev/null 2>&1 || true
  ok "سرویس systemd نصب شد: ${SERVICE_NAME}.service (کاربر: ${RUN_USER})"
  if [ "$DO_START" = "1" ] && [ -n "${EMOJI_FORGE_TOKEN:-}" ]; then
    systemctl restart "${SERVICE_NAME}.service"
    sleep 3
    if systemctl is-active --quiet "${SERVICE_NAME}.service"; then
      ok "سرویس فعال است"
      STARTED=1
    else
      warn "سرویس بالا نیامد؛ لاگ: journalctl -u ${SERVICE_NAME} -n 50"
    fi
  fi
  echo "  مدیریت:  systemctl {start|stop|restart|status} ${SERVICE_NAME}"
  echo "  لاگ:     journalctl -u ${SERVICE_NAME} -f"
else
  warn "systemd/روت در دسترس نیست — حالت دستی (nohup):"
  echo "    ./start.sh    # شروع"
  echo "    ./stop.sh     # توقف"
  echo "    لاگ: $ROOT/logs/bot.log"
  if [ "$DO_START" = "1" ] && [ -n "${EMOJI_FORGE_TOKEN:-}" ]; then
    "$ROOT/start.sh" || warn "شروع دستی ناموفق بود؛ لاگ را ببینید."
    STARTED=1
  fi
fi

say "تمام شد"
if [ "$STARTED" = "1" ]; then
  echo "  ربات در حال اجراست. در تلگرام /start را بزنید."
elif [ "$DO_START" = "0" ]; then
  echo "  نصب کامل شد؛ به درخواست شما (--no-start) اجرا نشد."
  echo "  شروع: sudo systemctl start ${SERVICE_NAME}  یا  ./start.sh"
elif [ -z "${EMOJI_FORGE_TOKEN:-}" ]; then
  echo "  برای شروع: توکن را در .env کامل کنید و دوباره ./install.sh را بزنید،"
  echo "  یا دستی: ./start.sh"
else
  echo "  نصب کامل شد. شروع: sudo systemctl start ${SERVICE_NAME}  یا  ./start.sh"
fi
echo "  راهنمای کامل: README-INSTALL-FA.md"
