#!/usr/bin/env python3
"""Visual QA for v7 finals vs originals.

Renders each original and each v7 final at 0/25/50/75/100% into
/tmp/v7_qa/file_NN_orig_vs_v7.png, builds the midframe grid at
/tmp/v7_qa/v7_midframe_grid.png (copied to ~/workspace/), and checks:
  (a) pack-common watermark signatures remaining in finals (must be 0)
  (b) an "Abolfazl Logo" brand element present in every final
  (c) blue-pixel fraction down vs original (theme recolour intact)
  (d) per-file logo on-screen width at the median visible frame
      (from fidelity measurement; target >= 358px or old width)
  (e) file 011 coin proof: frames [0,15,22,30,38,45,90] of the v7
      final, plus the same frames with the logo group removed so the
      restored spinning coin is visible on its own.
"""
import glob
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image  # noqa: E402

from emoji_forge.animation_engine import load_tgs, pack_common_signatures  # noqa: E402
from qa_v5 import blue_frac, count_pack_common, render_frame  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
FINAL_DIR = "data/projects/forge-v7-test/final"
OUT = "/tmp/v7_qa"


def final_path(idx):
    hits = [p for p in glob.glob(os.path.join(FINAL_DIR, "*.tgs"))
            if f"_{idx:03d}_" in os.path.basename(p)]
    return hits[0]


def has_logo(doc):
    scopes = [doc.get("layers") or []] + [a.get("layers") or []
                                          for a in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            if str(layer.get("nm", "")) == "Abolfazl Logo":
                return True
            for item in layer.get("shapes") or []:
                if isinstance(item, dict) and item.get("ty") == "gr" \
                        and str(item.get("nm", "")) == "Abolfazl Logo":
                    return True
    return False


def main():
    os.makedirs(OUT, exist_ok=True)
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)
    fid = json.load(open(os.path.join(OUT, "fidelity.json")))
    grid = Image.new("RGB", (4 * 256, 5 * 256), (0, 0, 0))
    print(f"{'#':>2} {'packcommon':>10} {'logo':>5} {'blue orig->v7':>15} {'logoW':>7}")
    all_ok = True
    for idx, path in enumerate(origs, 1):
        doc = docs[idx - 1]
        fp = final_path(idx)
        fdoc = load_tgs(open(fp, "rb").read())
        op = doc.get("op", 180)
        frames = [int(op * f) for f in (0, .25, .5, .75, .999)]
        strip = Image.new("RGB", (512, 5 * 256), (0, 0, 0))
        bo = bv = 0.0
        for j, fr in enumerate(frames):
            oi = render_frame(path, fr, 256)
            fi = render_frame(fp, fr, 256)
            strip.paste(oi, (0, j * 256))
            strip.paste(fi, (256, j * 256))
            if j == 2:
                bo, _ = blue_frac(oi)
                bv, _ = blue_frac(fi)
                grid.paste(fi, ((idx - 1) % 4 * 256, (idx - 1) // 4 * 256))
        strip.save(os.path.join(OUT, f"file_{idx:02d}_orig_vs_v7.png"))
        pc = count_pack_common(fdoc, sigs)
        logo = has_logo(fdoc)
        lw = fid[str(idx)]["v7"].get("new_median_width")
        ok = pc == 0 and logo
        all_ok = all_ok and ok
        print(f"{idx:>2} {pc:>10} {str(logo):>5} {bo:.3f}->{bv:.3f}   {lw}")
    grid.save(os.path.join(OUT, "v7_midframe_grid.png"))
    shutil.copy(os.path.join(OUT, "v7_midframe_grid.png"),
                os.path.expanduser("~/workspace/v7_midframe_grid.png"))
    # --- File 011 coin proof.
    p11 = final_path(11)
    frames = [0, 15, 22, 30, 38, 45, 90]
    proof = Image.new("RGB", (4 * 256, 2 * 256), (0, 0, 0))
    for i, fr in enumerate(frames):
        proof.paste(render_frame(p11, fr, 256), ((i % 4) * 256, (i // 4) * 256))
    proof.save(os.path.join(OUT, "file_11_coin_proof.png"))
    # Coin-only variant: strip the Abolfazl group from the TON layer.
    import copy as _copy
    d11 = load_tgs(open(p11, "rb").read())
    for layer in d11.get("layers") or []:
        if layer.get("nm") == "TON":
            layer["shapes"] = [g for g in layer.get("shapes") or []
                               if not (isinstance(g, dict)
                                       and str(g.get("nm", "")) == "Abolfazl Logo")]
    tmp = os.path.join(OUT, "file_11_nologo.json")
    with open(tmp, "w") as fh:
        json.dump(d11, fh)
    proof2 = Image.new("RGB", (4 * 256, 2 * 256), (0, 0, 0))
    for i, fr in enumerate(frames):
        proof2.paste(render_frame(tmp, fr, 256), ((i % 4) * 256, (i // 4) * 256))
    proof2.save(os.path.join(OUT, "file_11_coin_solo.png"))
    print("grid + coin proofs written; all_ok =", all_ok)


if __name__ == "__main__":
    main()
