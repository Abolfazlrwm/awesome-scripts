"""Fix-3 batch 1: surgical removal of named old-brand groups
(ShadiFar / Mr / StoreMehdi*) from the CURRENT finals, staged into
final_fix3/. Every removal is render-verified with rlottie: if any
removal changes a scene-scale area at any sampled frame, the file
is aborted and reported for manual handling (the file-166 rule).
"""
import copy
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import ImageChops

from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.v5 import _remove_group_at_path, _render_frames, _scope_layers

PROJECT = "forge-20261029-072531-16e1fc"
BASE = os.path.join(os.path.dirname(__file__), "..", "data", "projects", PROJECT)
STAGE = os.path.join(BASE, "final_fix3")
os.makedirs(STAGE, exist_ok=True)

# idx -> list of (scope_substr or None, layer_list_index, path)
TARGETS = {
    3:   [(None, 17, (0, 3, 0, 0)), (None, 17, (0, 3, 1, 0))],
    19:  [(None, 1, (0,)), (None, 1, (0,)), (None, 1, (0,)),
          (None, 2, (0,)), (None, 4, (1,)), (None, 5, (0,))],
    22:  [("comp_1", 0, (0, 0)), ("comp_1_dup1", 0, (0, 0))],
    96:  [("comp_0", 2, (4, 0))],
    101: [("comp_0", 1, (1, 0))],
    161: [("comp_1", 0, (2, 0))],
    163: [(None, 20, (6, 0))],
    192: [(None, 0, (0, 2, 0))],
    195: [(None, 0, (0, 0))],
}


def resolve_scope(doc, substr):
    if substr is None:
        return None
    for a in doc.get("assets") or []:
        if str(a.get("id", "")).startswith(substr):
            return a.get("id")
    return "MISSING"


def diff_stats(base_imgs, var_imgs):
    worst_changed, worst_box = 0, (0, 0)
    for b, v in zip(base_imgs, var_imgs):
        d = ImageChops.difference(b, v).convert("L")
        mask = d.point(lambda x: 255 if x > 24 else 0)
        bbox = mask.getbbox()
        if bbox is None:
            continue
        changed = sum(1 for x in d.crop(bbox).getdata() if x > 24)
        worst_changed = max(worst_changed, changed)
        worst_box = (max(worst_box[0], bbox[2] - bbox[0]),
                     max(worst_box[1], bbox[3] - bbox[1]))
    return worst_changed, worst_box


def main():
    finals = {}
    for f in glob.glob(os.path.join(BASE, "final", "*.tgs")):
        finals[int(os.path.basename(f).split("_")[1])] = f
    for idx, targets in TARGETS.items():
        path = finals[idx]
        raw = open(path, "rb").read()
        doc = load_tgs(raw)
        op = doc.get("op", 180)
        frames = sorted(set(int(op * f) for f in (0, 0.2, 0.4, 0.6, 0.8)))
        base = _render_frames(raw, frames)
        work = copy.deepcopy(doc)
        ok = True
        notes = []
        for substr, layer_idx, gpath in targets:
            scope = resolve_scope(work, substr)
            if scope == "MISSING":
                notes.append(f"scope {substr} NOT FOUND")
                ok = False
                break
            layers = _scope_layers(work, scope)
            if layer_idx >= len(layers):
                notes.append(f"layer idx {layer_idx} out of range")
                ok = False
                break
            layer = layers[layer_idx]
            ind = layer.get("ind")
            trial = copy.deepcopy(work)
            _remove_group_at_path(trial, scope, ind, gpath)
            var = _render_frames(dump_tgs(trial), frames)
            changed, box = diff_stats(base, var)
            notes.append(f"rm scope={substr} L{layer_idx} path={gpath}: "
                         f"changed={changed} box={box}")
            if changed > 40000 or box[0] > 340 or box[1] > 340:
                notes.append("ABORT: scene-scale change")
                ok = False
                break
            if changed == 0:
                notes.append("WARN: removal changed nothing (already hidden?)")
            work = trial
            base = var
        if ok:
            out = os.path.join(STAGE, os.path.basename(path))
            with open(out, "wb") as fh:
                fh.write(dump_tgs(work))
            print(f"{idx}: STAGED -> {os.path.basename(out)}")
        else:
            print(f"{idx}: NOT staged")
        for n in notes:
            print("   ", n)


if __name__ == "__main__":
    main()
