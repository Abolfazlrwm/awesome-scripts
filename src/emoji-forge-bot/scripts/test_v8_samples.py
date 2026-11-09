"""Sample test: process a few items of a project with mode v8 into a
scratch project (forge-test-v8), using the source project's full
manifest (so pack signatures and theme hue match the real run) and
its applied plan's brand/hide settings."""
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.brand import BrandProfile
from emoji_forge.pipeline import process_project

BASE = "/home/hatch/workspace/emoji-forge-ai/data"
SRC = "forge-20261029-072531-16e1fc"
DST = "forge-test-v8"


def main():
    indices = [int(x) for x in sys.argv[1:]] or [0, 11, 23, 33, 99]
    src_root = os.path.join(BASE, "projects", SRC)
    dst_root = os.path.join(BASE, "projects", DST)
    os.makedirs(dst_root, exist_ok=True)
    plan = json.load(open(os.path.join(src_root, "mati_plan_applied.json")))
    manifest = json.load(open(os.path.join(src_root, "manifest.json")))
    with open(os.path.join(dst_root, "manifest.json"), "w") as fh:
        json.dump(manifest, fh, ensure_ascii=False)
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    report = process_project(
        BASE, DST, manifest, brand, None, "v8", plan.get("output_kind", "emoji"),
        only_indices=indices,
        hide_layer_names=set(plan.get("hide_layer_names") or []),
    )
    for item in report["items"]:
        print(item["index"], item["status"], item.get("output_name", ""),
              "|", item.get("detail", "")[:400], flush=True)


if __name__ == "__main__":
    main()
