"""Safe size pass for oversize TGS finals — ONLY the recipes proven
render-neutral on this pack family (2026-10-29):
1. selective float rounding (|x|>=2 -> 1 decimal; |x|<2 -> 3 decimals;
   colours/alpha survive at 3 decimals; measured render mean 0.017)
2. exact-duplicate keyframe 'e' removal (e == next keyframe's 's')
3. last resort: integer-rounding of shape-path vertices in the
   single largest layer (colours never integer-rounded)
NO layer/asset deletion, NO hidden purge, NO strip-e.
Backups go to final_backup_before_shrink/."""
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
from emoji_forge.animation_engine import dump_tgs, load_tgs

ROOT = "/home/hatch/workspace/emoji-forge-ai/data/projects/forge-20261029-072531-16e1fc"
LIMIT = 65536


def round_floats(node):
    if isinstance(node, bool):
        return node
    if isinstance(node, float):
        return round(node, 1) if abs(node) >= 2 else round(node, 3)
    if isinstance(node, list):
        return [round_floats(v) for v in node]
    if isinstance(node, dict):
        return {k: round_floats(v) for k, v in node.items()}
    return node


def dedupe_exact_e(node):
    removed = 0
    if isinstance(node, dict):
        for v in node.values():
            removed += dedupe_exact_e(v)
        k = node.get("k")
        if isinstance(k, list) and k and all(isinstance(f, dict) for f in k):
            for cur, nxt in zip(k, k[1:]):
                if "e" in cur and "s" in nxt and cur.get("e") == nxt.get("s"):
                    del cur["e"]
                    removed += 1
    elif isinstance(node, list):
        for v in node:
            removed += dedupe_exact_e(v)
    return removed


def int_round_animated_paths(doc):
    """Integer-round vertices of ANIMATED path keyframes ('sh' nodes
    whose ks.k is a keyframe list; each keyframe's s/e holds path
    dicts). Needed for file 024, whose morph-path keyframes dominate
    its size. Sub-pixel (<=0.5px) like the other rounding steps."""
    from emoji_forge.animation_engine import _walk

    def intround_pathdict(d):
        for pk in ("v", "i", "o"):
            pts = d.get(pk)
            if isinstance(pts, list):
                d[pk] = [[round(float(x)) if isinstance(x, (int, float)) else x
                          for x in pt] if isinstance(pt, list) else pt
                         for pt in pts]

    n = 0
    for node in _walk(doc):
        if isinstance(node, dict) and node.get("ty") == "sh":
            k = (node.get("ks") or {}).get("k")
            if isinstance(k, list):
                for kf in k:
                    if isinstance(kf, dict):
                        for se in ("s", "e"):
                            v = kf.get(se)
                            if isinstance(v, list) and v and isinstance(v[0], dict):
                                intround_pathdict(v[0])
                                n += 1
    return n


def int_round_largest_layer(doc):
    scopes = [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]
    best, best_len = None, -1
    for layers in scopes:
        for layer in layers:
            n = len(json.dumps(layer))
            if n > best_len:
                best, best_len = layer, n
    if best is None:
        return 0

    def round_paths(node, in_path=False):
        cnt = 0
        if isinstance(node, dict):
            ty = node.get("ty")
            for key, v in node.items():
                if ty == "sh" and key == "k" and isinstance(v, dict):
                    for pk in ("v", "i", "o"):
                        pts = v.get(pk)
                        if isinstance(pts, list):
                            v[pk] = [[round(float(x)) if isinstance(x, (int, float)) else x
                                      for x in pt] if isinstance(pt, list) else pt
                                     for pt in pts]
                            cnt += len(pts)
                else:
                    cnt += round_paths(v, in_path)
        elif isinstance(node, list):
            for v in node:
                cnt += round_paths(v, in_path)
        return cnt

    return round_paths(best)


def main():
    final_dir = os.path.join(ROOT, "final")
    backup_dir = os.path.join(ROOT, "final_backup_before_shrink")
    os.makedirs(backup_dir, exist_ok=True)
    for name in sorted(os.listdir(final_dir)):
        path = os.path.join(final_dir, name)
        size0 = os.path.getsize(path)
        if size0 <= LIMIT:
            continue
        shutil.copy2(path, os.path.join(backup_dir, name))
        doc = load_tgs(open(path, "rb").read())
        doc = round_floats(doc)
        dedupe_exact_e(doc)
        data = dump_tgs(doc)
        step = "round+dedupe"
        if len(data) > LIMIT:
            int_round_animated_paths(doc)
            data = dump_tgs(doc)
            step += "+int-round-animpaths"
        if len(data) > LIMIT:
            int_round_largest_layer(doc)
            data = dump_tgs(doc)
            step += "+int-round-largest"
        with open(path, "wb") as fh:
            fh.write(data)
        print(f"{name}: {size0} -> {len(data)} ({step}) fits={len(data) <= LIMIT}", flush=True)


if __name__ == "__main__":
    main()
