"""Corrective repairs for files 034 and 024, from their pre-fix backups.

034: the hidden layer converted to Null in fix_finals was the ROOT of
a hidden subtree — clearing hd made its children (the old StoreMehdi
chest text) visible. Correct fix: delete every layer that is not
EFFECTIVELY visible (itself or any ancestor hidden), except track
mattes / matte sources. Nothing effectively visible is touched.

024: doc-wide 1-decimal rounding also rounded 0..1 colour/alpha
floats and shifted colours. Correct fix: brand font swap + strip-e +
selective rounding (coordinates |x|>=2 -> 1 decimal; small values
-> 3 decimals, colour error <= 0.0005, invisible).
"""
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, dump_tgs, text_to_lottie_shapes
from emoji_forge.validate_tgs import validate_tgs_bytes

ROOT = "/home/hatch/workspace/emoji-forge-ai/data/projects/forge-20261029-072531-16e1fc"
FINAL = os.path.join(ROOT, "final")
BACKUP = os.path.join(ROOT, "final_backup_before_fix")


def path_in(directory, idx):
    name = [f for f in os.listdir(directory)
            if f.startswith(f"forge-20261029-072531-16e1fc_{idx:03d}_")][0]
    return os.path.join(directory, name)


def scopes_of(doc):
    out = [doc.get("layers") or []]
    for a in doc.get("assets") or []:
        if isinstance(a.get("layers"), list):
            out.append(a["layers"])
    return out


def delete_hidden_subtrees(doc):
    deleted = 0
    for layers in scopes_of(doc):
        def resolved(ind):
            for l in layers:
                if l.get("ind") == ind:
                    return l
            return None

        def effectively_visible(layer, seen=None):
            seen = seen or set()
            cur = layer
            while cur is not None:
                if id(cur) in seen:
                    break
                seen.add(id(cur))
                if cur.get("hd"):
                    return False
                p = cur.get("parent")
                cur = resolved(p) if p is not None else None
            return True

        matte_sources = set()
        for i, l in enumerate(layers):
            if l.get("tt") and i > 0 and effectively_visible(l):
                matte_sources.add(id(layers[i - 1]))

        def protected(layer):
            # td layers and matte sources of visible consumers, plus
            # their ancestor chains, are never deleted.
            cur = layer
            seen = set()
            while cur is not None and id(cur) not in seen:
                seen.add(id(cur))
                if cur.get("td") or id(cur) in matte_sources:
                    return True
                p = cur.get("parent")
                cur = resolved(p) if p is not None else None
            return id(layer) in matte_sources or bool(layer.get("td"))

        kept = []
        for l in layers:
            if not effectively_visible(l) and not protected(l):
                deleted += 1
                continue
            kept.append(l)
        layers[:] = kept
    return deleted


def fix_034():
    src = path_in(BACKUP, 34)
    doc = load_tgs(open(src, "rb").read())
    n = delete_hidden_subtrees(doc)
    out = dump_tgs(doc)
    issues = validate_tgs_bytes(out, 34)[0]
    print(f"034: deleted {n} hidden-subtree layers, size {len(out)}, issues: {issues}")
    assert not issues
    open(path_in(FINAL, 34), "wb").write(out)


def strip_e(o):
    if isinstance(o, dict):
        if "t" in o and "e" in o and "s" in o:
            del o["e"]
        for v in o.values():
            strip_e(v)
    elif isinstance(o, list):
        for v in o:
            strip_e(v)


def selective_round(o):
    if isinstance(o, float):
        return round(o, 1) if abs(o) >= 2 else round(o, 3)
    if isinstance(o, list):
        return [selective_round(x) for x in o]
    if isinstance(o, dict):
        return {k: selective_round(v) for k, v in o.items()}
    return o


def fix_024():
    src = path_in(BACKUP, 24)
    doc = load_tgs(open(src, "rb").read())
    for layer in doc.get("layers") or []:
        if "Brand" in str(layer.get("nm")):
            shapes, _w = text_to_lottie_shapes(
                "TZ Emojis",
                "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
                54.0)
            layer["shapes"] = shapes
    strip_e(doc)
    out = dump_tgs(selective_round(doc))
    issues = validate_tgs_bytes(out, 24)[0]
    print(f"024: size {len(out)}, issues: {issues}")
    assert len(out) <= 64 * 1024 and not issues
    open(path_in(FINAL, 24), "wb").write(out)


if __name__ == "__main__":
    fix_034()
    fix_024()
