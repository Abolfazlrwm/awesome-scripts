"""Download Telegram's own thumbnails for every sticker of the
live published set (ground-truth renders)."""
import json
import os
import sys

import requests

SET = "forge_20261029_072531_16e1fc_by_tzemoji_ai"
OUT = os.path.join(os.path.dirname(__file__), "..", "data", "thumbcheck", "live_set")
os.makedirs(OUT, exist_ok=True)


def main():
    tok = os.environ["EMOJI_FORGE_TOKEN"]
    api = f"https://api.telegram.org/bot{tok}"
    r = requests.post(api + "/getStickerSet", json={"name": SET}, timeout=60)
    d = r.json()
    assert d.get("ok"), d
    stickers = d["result"]["stickers"]
    print("stickers:", len(stickers))
    for i, st in enumerate(stickers, 1):
        th = st.get("thumbnail")
        if not th:
            print(i, "no thumbnail")
            continue
        fr = requests.post(api + "/getFile", json={"file_id": th["file_id"]}, timeout=30).json()
        path = fr["result"]["file_path"]
        img = requests.get(f"https://api.telegram.org/file/bot{tok}/{path}", timeout=30).content
        with open(os.path.join(OUT, f"{i:03d}.jpg"), "wb") as fh:
            fh.write(img)
    print("done ->", OUT)


if __name__ == "__main__":
    main()
