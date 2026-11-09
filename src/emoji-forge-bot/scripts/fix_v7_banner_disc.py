#!/usr/bin/env python3
"""Targeted v7 fix: white-disc ghost logos in banner files 017/018/019.

Problem (verified by solo/recolour renders of file 017):
  The banner precomp (comp_2) is instanced 4x in comp_0. Two logo
  elements render over the white disc badge:
  1. The disc's OWN logo slot: comp_2 layer "Logo in" -> comp_3, which
     in the original held the white "CDC Outlines" triangle (white fill,
     no stroke -> invisible on the white disc). v7 replaced it with the
     EHSAN logo WITH a dark stroke, so it shows as an outlined ghost.
  2. The main EHSAN logo in comp_2 is 358.4px wide (banner fix) and its
     left edge extends over the disc; its dark stroke paints outlined
     letters on the white disc. In the original the SVN text started at
     the banner, clear of the disc (verified by recolouring the
     original text layer red: zero pixels over the disc).

Fix (structure-preserving, main logo layer itself untouched):
  1. Disc slot (comp_3): remove the stroke from the EHSAN group ->
     white fill only, invisible on the white disc exactly like the
     original CDC mark.
  2. comp_2: lift the "Logo CIrcle" layer (the white disc silhouette)
     ABOVE the text layer, so the disc paints over the main logo's
     over-disc spill — reproducing the original's visual, where
     nothing of the logo is visible on the disc. The logo's size,
     fill, stroke, transform and animation are NOT modified; on the
     banner it renders identically (the disc does not reach it).
     Note: merely moving the layer in the array is NOT enough — it is
     parented to the "Guide Left" null, and parented layers render
     inside their parent's subtree (below the top-level text layer)
     in the restructure tree. So the layer is also UNPARENTED, baking
     in the (static, verified non-animated) composed transform
     Guide Left ∘ Circle. As an unparented layer at array index 0 it
     paints last in the local renderer, and index 0 is also topmost
     under standard Lottie semantics — the fix holds under both.

Only files 017/018/019 are touched. Local only — no Telegram calls.
Idempotent: re-running detects both changes and skips them.
"""
import copy
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import dump_tgs, load_tgs  # noqa: E402
from emoji_forge.validate_tgs import validate_tgs_bytes  # noqa: E402

FINAL_DIR = "data/projects/forge-v7-test/final"
REPORT = "data/projects/forge-v7-test/build-report.json"
TARGETS = (17, 18, 19)


def _assets(doc):
    return {a["id"]: a for a in doc.get("assets") or [] if "layers" in a}


def _strip_strokes(layer):
    """Remove every stroke item from a logo layer's shape groups."""
    removed = 0
    for g in layer.get("shapes") or []:
        if isinstance(g, dict) and g.get("ty") == "gr":
            before = len(g.get("it") or [])
            g["it"] = [it for it in g.get("it") or [] if it.get("ty") != "st"]
            removed += before - len(g["it"])
    return removed


def _has_stroke(layer):
    for g in layer.get("shapes") or []:
        if isinstance(g, dict) and g.get("ty") == "gr":
            if any(it.get("ty") == "st" for it in g.get("it") or []):
                return True
    return False


def _static(ks, prop):
    """Static (non-animated) value of a transform property."""
    v = (ks or {}).get(prop)
    if isinstance(v, dict):
        assert v.get("a", 0) == 0, f"animated transform {prop} — cannot bake"
        return v.get("k")
    return v


def _unparent_disc(layers, circle):
    """Bake the composed transform parent ∘ circle into the circle
    layer and clear its parent, so it becomes a top-level layer.
    Both transforms must be static and rotation-free (asserted).
    Returns True if the layer was unparented."""
    parent_ind = circle.get("parent")
    if parent_ind is None:
        return False
    parent = next(l for l in layers if l.get("ind") == parent_ind)
    assert not any(l.get("parent") == circle.get("ind") for l in layers), \
        "disc layer has children — cannot unparent safely"
    gks, cks = parent.get("ks") or {}, circle.get("ks") or {}
    for ks in (gks, cks):
        r = _static(ks, "r")
        assert r in (None, 0, 0.0), f"rotation present ({r}) — cannot bake"
    p_g, a_g, s_g = _static(gks, "p"), _static(gks, "a"), _static(gks, "s")
    p_l, s_l = _static(cks, "p"), _static(cks, "s")
    sg = [v / 100.0 for v in (s_g or [100, 100, 100])]
    sl = [v / 100.0 for v in (s_l or [100, 100, 100])]
    new_s = [round(sg[i] * sl[i] * 100, 3) for i in range(3)]
    new_p = [round(p_g[i] + sg[i] * (p_l[i] - a_g[i]), 3) for i in range(3)]
    cks["p"] = {"a": 0, "k": new_p}
    cks["s"] = {"a": 0, "k": new_s}
    del circle["parent"]
    return True


def fix_doc(doc):
    """Apply the disc-ghost fix to one parsed TGS doc. Returns a report
    dict; raises ValueError if the expected banner structure is absent."""
    assets = _assets(doc)
    # Locate the banner precomp: the asset that has both the white disc
    # layer ("Logo CIrcle") and a precomp layer pointing at the disc
    # logo slot asset.
    banner = None
    slot_asset = None
    for aid, asset in assets.items():
        names = [str(l.get("nm", "")) for l in asset["layers"]]
        if "Logo CIrcle" not in names:
            continue
        for layer in asset["layers"]:
            if layer.get("ty") == 0 and layer.get("refId") in assets:
                slot_layers = assets[layer["refId"]]["layers"]
                if any(str(l.get("nm", "")) == "EHSAN Logo" for l in slot_layers):
                    banner, slot_asset = asset, assets[layer["refId"]]
        if banner is not None:
            break
    if banner is None:
        raise ValueError("banner precomp with disc slot not found")

    layers = banner["layers"]
    main_logo = next(l for l in layers if str(l.get("nm", "")) == "EHSAN Logo")
    main_logo_before = copy.deepcopy(main_logo)
    circle = next(l for l in layers if str(l.get("nm", "")) == "Logo CIrcle")

    # 1) Disc slot: stroke-less (white fill only), like the original.
    slot_logo = next(l for l in slot_asset["layers"]
                     if str(l.get("nm", "")) == "EHSAN Logo")
    strokes_removed = _strip_strokes(slot_logo)

    # 2) White disc above the text: unparent it (baking the composed
    #    static transform), then move it to the top of the array.
    unparented = _unparent_disc(layers, circle)
    moved = False
    if layers[0] is not circle:
        layers.remove(circle)
        layers.insert(0, circle)
        moved = True

    # Self-checks: main logo untouched, still stroked; slot now clean.
    assert main_logo == main_logo_before, "main logo layer was modified!"
    assert _has_stroke(main_logo), "main logo lost its stroke!"
    assert not _has_stroke(slot_logo), "slot logo still has a stroke!"
    assert layers[0].get("nm") == "Logo CIrcle", "disc layer not on top"
    assert "parent" not in layers[0], "disc layer still parented"
    return {"strokes_removed": strokes_removed,
            "disc_unparented": unparented, "disc_moved_to_top": moved}


def main():
    report = {}
    if os.path.exists(REPORT):
        report = {int(k): v for k, v in json.load(open(REPORT)).items()}
    for idx in TARGETS:
        hits = [p for p in glob.glob(os.path.join(FINAL_DIR, "*.tgs"))
                if f"_{idx:03d}_" in os.path.basename(p)]
        assert len(hits) == 1, f"file {idx}: {hits}"
        path = hits[0]
        doc = load_tgs(open(path, "rb").read())
        info = fix_doc(doc)
        out = dump_tgs(doc)
        issues, vwarn = validate_tgs_bytes(out, idx)
        assert not issues, f"file {idx}: validator blocked: {issues}"
        with open(path, "wb") as fh:
            fh.write(out)
        if idx in report:
            report[idx]["size"] = len(out)
            report[idx]["issues"] = issues
            report[idx]["validator_warnings"] = vwarn
            report[idx].setdefault("info", {})["disc_ghost_fix"] = info
        print(f"[{idx:02d}] {os.path.basename(path)} {len(out)}B "
              f"fix={info} validator_issues={len(issues)}")
    with open(REPORT, "w") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=1, default=str)
    print("done")


if __name__ == "__main__":
    main()
