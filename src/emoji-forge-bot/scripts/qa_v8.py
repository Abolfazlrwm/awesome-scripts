#!/usr/bin/env python3
"""Visual QA for v8 finals vs originals.

Renders each original and each v8 final at 0/25/50/75/100% into
/tmp/v8_qa/file_NN_orig_vs_v8.png, builds the midframe grid at
/tmp/v8_qa/v8_midframe_grid.png (copied to ~/workspace/), and checks:
  (a) pack-common watermark signatures remaining in finals (must be 0)
  (b) an "EHSAN Logo" brand element present in every final
  (c) blue-pixel fraction down vs original (theme recolour intact)
  (d) fitted logo median width (from the v8 fidelity measurement)
  (e) banner white-disc cleanliness (017-019): dark pixels inside
      the disc interior at every 5th frame — the disc mask is the
      union of filled roundish near-white blobs (fill >= 0.7,
      area >= 400) in the ORIGINAL's render at the same frame,
      eroded by one pixel; dark = max channel < 200. (An earlier
      largest-blob mask misidentified a text fragment as the disc
      in file 018 and reported phantom counts; the blob-shape
      criterion fixes that — the original itself reads 0 under
      this mask, as it must.)
  (f) file 011 coin proof (restored spinning coin, untouched by the
      v8 rescale — canonical diff already proves it; kept as the
      visual record).
"""
import glob
import json
import os
import shutil
import sys
from collections import deque

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

import numpy as np  # noqa: E402
from PIL import Image  # noqa: E402

from emoji_forge.animation_engine import load_tgs, pack_common_signatures  # noqa: E402
from qa_v5 import blue_frac, count_pack_common, render_frame  # noqa: E402
from qa_v7 import has_logo  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
FINAL_DIR = "data/projects/forge-v8-test/final"
V7_DIR = "data/projects/forge-v7-test/final"
OUT = "/tmp/v8_qa"
SIZE = 256


def final_path(ddir, idx):
    hits = [p for p in glob.glob(os.path.join(ddir, "*.tgs"))
            if f"_{idx:03d}_" in os.path.basename(p)]
    return hits[0]


def _disc_mask(orig_im):
    """Interior mask of the white disc in an original banner render:
    the union of filled roundish near-white blobs (fill ratio >=
    0.7 of their bbox, area >= 400 — the disc and its in-frame caps
    qualify, text strokes and the cursor do not), eroded once.
    Returns a boolean array (possibly all-False)."""
    a = np.asarray(orig_im).astype(int)
    white = (a.min(axis=2) > 240)
    h, w = white.shape
    seen = np.zeros_like(white)
    mask = np.zeros_like(white)
    for y in range(h):
        for x in range(w):
            if not white[y, x] or seen[y, x]:
                continue
            q = deque([(y, x)])
            seen[y, x] = True
            cells = []
            while q:
                cy, cx = q.popleft()
                cells.append((cy, cx))
                for dy, dx in ((1, 0), (-1, 0), (0, 1), (0, -1)):
                    ny, nx = cy + dy, cx + dx
                    if 0 <= ny < h and 0 <= nx < w and white[ny, nx] \
                            and not seen[ny, nx]:
                        seen[ny, nx] = True
                        q.append((ny, nx))
            if len(cells) < 400:
                continue
            ys = [c[0] for c in cells]
            xs = [c[1] for c in cells]
            bw = max(xs) - min(xs) + 1
            bh = max(ys) - min(ys) + 1
            if len(cells) / (bw * bh) >= 0.7:
                for cy, cx in cells:
                    mask[cy, cx] = True
    # Erode once: interior only (a pixel stays if all 4 neighbours in).
    interior = mask.copy()
    interior[1:-1, 1:-1] = (mask[1:-1, 1:-1] & mask[:-2, 1:-1]
                            & mask[2:, 1:-1] & mask[1:-1, :-2]
                            & mask[1:-1, 2:])
    interior[:1, :] = interior[-1:, :] = interior[:, :1] = interior[:, -1:] = False
    return interior


def disc_dark_count(orig_im, final_im, mask):
    """Dark pixels of a final render inside the disc interior mask
    derived from the original's render at the same frame."""
    if not mask.any():
        return None
    a = np.asarray(final_im).astype(int)
    dark = a.max(axis=2) < 200
    return int((dark & mask).sum())


def main():
    os.makedirs(OUT, exist_ok=True)
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)
    fid = json.load(open(os.path.join(OUT, "fidelity.json")))
    grid = Image.new("RGB", (4 * 256, 5 * 256), (0, 0, 0))
    print(f"{'#':>2} {'packcommon':>10} {'logo':>5} {'blue orig->v8':>15} {'logoW':>7}")
    all_ok = True
    for idx, path in enumerate(origs, 1):
        doc = docs[idx - 1]
        fp = final_path(FINAL_DIR, idx)
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
        strip.save(os.path.join(OUT, f"file_{idx:02d}_orig_vs_v8.png"))
        pc = count_pack_common(fdoc, sigs)
        logo = has_logo(fdoc)
        lw = fid[str(idx)]["v8"].get("new_median_width")
        ok = pc == 0 and logo
        all_ok = all_ok and ok
        print(f"{idx:>2} {pc:>10} {str(logo):>5} {bo:.3f}->{bv:.3f}   {lw}")
    grid.save(os.path.join(OUT, "v8_midframe_grid.png"))
    shutil.copy(os.path.join(OUT, "v8_midframe_grid.png"),
                os.path.expanduser("~/workspace/v8_midframe_grid.png"))
    # --- Banner disc cleanliness (original must read 0 as control).
    print("disc dark-pixel counts (max over every 5th frame):")
    for idx in (17, 18, 19):
        op = docs[idx - 1].get("op", 90)
        frames = list(range(0, op + 1, 5))
        row = {}
        for tag, path in (("orig", origs[idx - 1]),
                          ("v7", final_path(V7_DIR, idx)),
                          ("v8", final_path(FINAL_DIR, idx))):
            counts = []
            for fr in frames:
                oi = render_frame(origs[idx - 1], fr, SIZE)
                mask = _disc_mask(oi)
                c = disc_dark_count(oi, render_frame(path, fr, SIZE), mask)
                if c is not None:
                    counts.append(c)
            row[tag] = max(counts) if counts else None
        print(f"  file {idx}: orig max={row['orig']}  v7 max={row['v7']}  "
              f"v8 max={row['v8']} ({len(frames)} frames)")
        all_ok = all_ok and row["v8"] == 0 and row["orig"] == 0
    # --- File 011 coin proof.
    p11 = final_path(FINAL_DIR, 11)
    frames = [0, 15, 22, 30, 38, 45, 90]
    proof = Image.new("RGB", (4 * 256, 2 * 256), (0, 0, 0))
    for i, fr in enumerate(frames):
        proof.paste(render_frame(p11, fr, 256), ((i % 4) * 256, (i // 4) * 256))
    proof.save(os.path.join(OUT, "file_11_coin_proof.png"))
    print("grid + coin proof written; all_ok =", all_ok)


if __name__ == "__main__":
    main()
