#!/usr/bin/env python3
"""Visual QA for v4 finals vs originals.

Renders each original and each v4 final at 0/25/50/75/100% of its
animation with lottie+cairosvg, builds contact grids in /tmp/v4_qa/,
and reports structural + pixel checks:
  (a) pack-common watermark signatures remaining in finals (must be 0)
  (b) an "Abolfazl_rwm Brand" top-level text layer present in every final
  (c) blue-pixel fraction in rendered frames: theme blue down vs
      original, warm/yellow pixels preserved within tolerance
Pixel checks are evidence, not proof: the grids are for human
visual inspection (old logo visible? text readable? natural colours
intact?). Nothing here claims Telegram-side rendering.

Usage: .venv/bin/python scripts/qa_v4.py [--orig DIR] [--final DIR] [--out DIR]
"""
import argparse
import glob
import io
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from lottie import parsers  # noqa: E402
from lottie.exporters import svg  # noqa: E402
import cairosvg  # noqa: E402
from PIL import Image  # noqa: E402

from emoji_forge.animation_engine import load_tgs, pack_common_signatures, shape_signature, _walk  # noqa: E402


def render_frame(path, frame, size=256):
    an = parsers.tgs.parse_tgs(path)
    frame = max(an.in_point, min(int(frame), an.out_point - 1))
    buf = io.StringIO()
    svg.export_svg(an, buf, frame=frame)
    png = io.BytesIO()
    cairosvg.svg2png(bytestring=buf.getvalue().encode(), write_to=png,
                     output_width=size, output_height=size)
    png.seek(0)
    return Image.open(png).convert("RGB")


def blue_frac(im):
    px = list(im.getdata())
    blue = sum(1 for r, g, b in px if b > 120 and b > r + 30 and b > g + 10)
    warm = sum(1 for r, g, b in px if r > 120 and r > b + 30 and g > 60)
    return blue / max(1, len(px)), warm / max(1, len(px))


def count_pack_common(doc, sigs):
    n = 0
    for scope in [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]:
        for layer in scope:
            for item in layer.get("shapes") or []:
                for node in _walk(item):
                    if isinstance(node, dict) and node.get("ty") == "sh":
                        if shape_signature((node.get("ks") or {}).get("k") or {}) in sigs:
                            n += 1
    return n


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--orig", default="data/projects/forge-20261028-155740-3f1382/originals")
    ap.add_argument("--final", default="data/projects/forge-v4-test/final")
    ap.add_argument("--out", default="/tmp/v4_qa")
    args = ap.parse_args()
    os.makedirs(args.out, exist_ok=True)

    origs = sorted(glob.glob(os.path.join(args.orig, "*.tgs")))
    finals = sorted(glob.glob(os.path.join(args.final, "*.tgs")))
    assert len(origs) == len(finals), f"orig {len(origs)} != final {len(finals)}"
    sigs = pack_common_signatures([load_tgs(open(p, "rb").read()) for p in origs])

    fracs = (0.0, 0.25, 0.5, 0.75, 1.0)
    rows = []
    for oi, (op, fp) in enumerate(zip(origs, finals), 1):
        fdoc = load_tgs(open(fp, "rb").read())
        pc = count_pack_common(fdoc, sigs)
        has_text = any("Brand" in str(l.get("nm", "")) for l in fdoc.get("layers") or [])
        o_an = parsers.tgs.parse_tgs(op)
        f_an = parsers.tgs.parse_tgs(fp)
        cells = []
        blue_o = blue_f = warm_o = warm_f = 0.0
        for frac in fracs:
            o_im = render_frame(op, o_an.in_point + frac * (o_an.out_point - o_an.in_point))
            f_im = render_frame(fp, f_an.in_point + frac * (f_an.out_point - f_an.in_point))
            bo, wo = blue_frac(o_im)
            bf, wf = blue_frac(f_im)
            blue_o += bo; blue_f += bf; warm_o += wo; warm_f += wf
            strip = Image.new("RGB", (512, 256), (255, 255, 255))
            strip.paste(o_im, (0, 0))
            strip.paste(f_im, (256, 0))
            cells.append(strip)
        grid = Image.new("RGB", (512, 256 * len(fracs)), (255, 255, 255))
        for i, c in enumerate(cells):
            grid.paste(c, (0, i * 256))
        grid.save(os.path.join(args.out, f"file_{oi:02d}_orig_vs_v4.png"))
        rows.append((oi, pc, has_text, blue_o / 5, blue_f / 5, warm_o / 5, warm_f / 5))
        print(f"file {oi:02d}: pack_common={pc} text_layer={has_text} "
              f"blue orig={blue_o/5:.3f} v4={blue_f/5:.3f} warm orig={warm_o/5:.3f} v4={warm_f/5:.3f}")

    # Mid-frame contact grid, v4 only.
    imgs = []
    for fp in finals:
        an = parsers.tgs.parse_tgs(fp)
        imgs.append(render_frame(fp, (an.in_point + an.out_point) / 2))
    grid = Image.new("RGB", (5 * 256, 4 * 256), (255, 255, 255))
    for i, im in enumerate(imgs):
        grid.paste(im, ((i % 5) * 256, (i // 5) * 256))
    grid.save(os.path.join(args.out, "v4_midframe_grid.png"))
    print(f"\nGrids in {args.out}")
    print(f"pack_common remaining total: {sum(r[1] for r in rows)} (target 0)")
    print(f"text layer missing in: {[r[0] for r in rows if not r[2]] or 'none'}")


if __name__ == "__main__":
    main()
