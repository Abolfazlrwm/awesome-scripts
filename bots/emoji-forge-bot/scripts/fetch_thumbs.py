"""Fetch Telegram's OWN rendered thumbnails (rlottie) for the
original EmojiStoreMehdi #012 and for the test set's #012, so we can
see exactly what a user sees in Telegram — including any element the
python-lottie QA renderer cannot draw."""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.config import load_config
from emoji_forge.telegram_api import TelegramApi


def thumb_of(api, set_name, index, out):
    s = api.get_sticker_set(set_name)
    stickers = s.get("stickers") or []
    st = stickers[index]
    th = st.get("thumbnail") or st.get("thumb")
    if not th:
        print(set_name, "no thumbnail")
        return
    f = api.get_file(th["file_id"])
    data = api.download_file(f["file_path"])
    with open(out, "wb") as fh:
        fh.write(data)
    print("saved", out, len(data), "bytes; emoji:", st.get("emoji"))


def main():
    config = load_config()
    api = TelegramApi(config.token)
    thumb_of(api, "EmojiStoreMehdi", 11, "/home/hatch/workspace/your_files/thumb_orig_012.webp")
    thumb_of(api, "mehdi_test12_by_tzemoji_ai", 0, "/home/hatch/workspace/your_files/thumb_test_012.webp")


if __name__ == "__main__":
    main()
