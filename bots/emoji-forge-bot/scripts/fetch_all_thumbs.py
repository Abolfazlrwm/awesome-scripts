"""Download Telegram's own thumbnails for ALL stickers of the
original EmojiStoreMehdi set (ground-truth renders) into
data/thumbcheck/orig_XXX.webp, then build contact sheets of the
fallback-class files."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.config import load_config
from emoji_forge.telegram_api import TelegramApi


def main():
    config = load_config()
    api = TelegramApi(config.token)
    s = api.get_sticker_set("EmojiStoreMehdi")
    stickers = s.get("stickers") or []
    outdir = os.path.join(os.path.dirname(__file__), "..", "data", "thumbcheck")
    os.makedirs(outdir, exist_ok=True)
    for i, st in enumerate(stickers, start=1):
        th = st.get("thumbnail") or st.get("thumb")
        if not th:
            continue
        out = os.path.join(outdir, f"orig_{i:03d}.webp")
        if os.path.exists(out):
            continue
        f = api.get_file(th["file_id"])
        data = api.download_file(f["file_path"])
        with open(out, "wb") as fh:
            fh.write(data)
    print("thumbs:", len(os.listdir(outdir)))


if __name__ == "__main__":
    main()
