"""Prepare Ehsan's E30TZ logo for TGS use:
1) remove the baked-in checkerboard background -> real alpha,
2) trace the black letterforms and the cyan glow accents as two
   aligned Lottie shape sets (shared 512-wide frame),
3) save a reusable logo-group JSON + preview renders.
Run once; fix4_logo.py consumes data/logo_e30tz.json.
"""
import io
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from PIL import Image, ImageFilter

SRC = ("/home/hatch/workspace/user/files/"
       "9B24E971-8318-49D7-A149-1E4FE51BD455-photo-"
       "389F4A33-4242-4059-85B3-3E2C9E845248.jpg")
OUT_DIR = os.path.join(os.path.dirname(__file__), "..", "data")
PREVIEW = "/home/hatch/workspace/your_files/logo_e30tz_preview.png"


def main():
    from emoji_forge.logo_vector import trace_logo_image

    im = Image.open(SRC).convert("RGB")
    w, h = im.size
    px = im.load()
    alpha = Image.new("L", (w, h), 0)
    ap = alpha.load()
    for y in range(h):
        for x in range(w):
            r, g, b = px[x, y]
            mx, mn = max(r, g, b), min(r, g, b)
            # checkerboard tones: near-grey and light -> background,
            # unless the pixel carries the cyan glow (b >> r)
            if mx - mn < 20 and mn > 140 and not (b - r > 25):
                ap[x, y] = 0
            else:
                ap[x, y] = 255
    alpha = alpha.filter(ImageFilter.GaussianBlur(0.7))
    bbox = alpha.getbbox()
    print("artwork bbox:", bbox)
    logo = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    logo.paste(im, (0, 0))
    logo.putalpha(alpha)
    logo = logo.crop(bbox)
    logo.save(os.path.join(OUT_DIR, "logo_e30tz.png"))
    lw, lh = logo.size
    print("logo cropped:", logo.size)

    arr = logo.load()
    black_mask = Image.new("RGBA", (lw, lh), (0, 0, 0, 0))
    cyan_mask = Image.new("RGBA", (lw, lh), (0, 0, 0, 0))
    bp, cp = black_mask.load(), cyan_mask.load()
    cyan_pts, dark_pts = [], []
    for y in range(lh):
        for x in range(lw):
            r, g, b, a = arr[x, y]
            if a < 110:
                continue
            if b - r > 45 and b > 90:  # neon cyan accent
                cp[x, y] = (0, 176, 255, 255)
                cyan_pts.append((r, g, b))
            elif (r + g + b) / 3 < 110:  # near-black body
                bp[x, y] = (0, 0, 0, 255)
                dark_pts.append((r, g, b))
    def median(pts, i, default):
        if not pts:
            return default
        vals = sorted(p[i] for p in pts)
        return vals[len(vals) // 2]
    black_rgb = [median(dark_pts, i, 8) / 255 for i in range(3)]
    cyan_rgb = [median(cyan_pts, i, 255) / 255 for i in range(3)]
    print("black sample:", black_rgb, "| cyan sample:", cyan_rgb,
          "| pts:", len(dark_pts), len(cyan_pts))

    def traced(mask_im):
        buf = io.BytesIO()
        mask_im.save(buf, format="PNG")
        return trace_logo_image(buf.getvalue())

    def remap(trace, mask_im):
        """trace subpaths are in the mask's own cropped 512-frame;
        move them into the full logo frame."""
        if trace is None:
            return []
        mb = mask_im.getchannel("A").getbbox()
        if mb is None:
            return []
        cw = mb[2] - mb[0]
        out = []
        for sp in trace["subpaths"]:
            nsp = {"c": sp["c"]}
            for key in ("v", "i", "o"):
                nsp[key] = [[round((mb[0] + p[0] * cw / 512.0) * 512.0 / lw, 2),
                             round((mb[1] + p[1] * cw / 512.0) * 512.0 / lw, 2)]
                            for p in sp[key]]
            out.append(nsp)
        return out

    black_subs = remap(traced(black_mask), black_mask)
    cyan_subs = remap(traced(cyan_mask), cyan_mask)
    print("subpaths: black", len(black_subs), "| cyan", len(cyan_subs))
    payload = {
        "frame_w": 512.0,
        "frame_h": round(lh * 512.0 / lw, 2),
        "black": {"rgb": black_rgb, "subpaths": black_subs},
        "cyan": {"rgb": cyan_rgb, "subpaths": cyan_subs},
    }
    with open(os.path.join(OUT_DIR, "logo_e30tz.json"), "w") as fh:
        json.dump(payload, fh)
    print("saved data/logo_e30tz.json; frame_h:", payload["frame_h"])

    # preview: logo on dark and light panels
    prev = Image.new("RGB", (1024, 300), (24, 24, 28))
    prev.paste((235, 235, 235), (512, 0, 1024, 300))
    small = logo.resize((480, int(480 * lh / lw)))
    for x0 in (16, 528):
        prev.paste(small, (x0, (300 - small.height) // 2), small)
    prev.save(PREVIEW)
    print("preview saved")


if __name__ == "__main__":
    main()
