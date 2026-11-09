#!/usr/bin/env python3
"""Build v8 finals: v7 finals with every EHSAN logo re-solved for
PRECISE CONTAIN-FIT inside the old logo's on-screen box.

User order 2026-10-29 09:59 (supersedes v7's "always big" rule):
the logo must fit the frame, measured precisely, logical in shape,
appearance and subject. The transform touches ONLY logo groups;
everything else (audit repairs, restored coin/disc layers, theme
recolour, banner disc fix, file 016's proven-empty comp_1) is
proven identical to the v7 finals by a canonical diff that erases
logo content on both sides.

Writes data/projects/forge-v8-test/{originals,final}/ and
fit-report.json. Local only — no Telegram calls.
"""
import glob
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import dump_tgs, load_tgs, pack_common_signatures  # noqa: E402
from emoji_forge.v8 import canonical_without_logo, rescale_doc_to_fit  # noqa: E402
from emoji_forge.validate_tgs import validate_tgs_bytes  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
V7_DIR = "data/projects/forge-v7-test/final"
OUT_DIR = "data/projects/forge-v8-test"
FINGERPRINT = "data/projects/forge-v5-test/fingerprint.json"


def final_path(ddir, idx):
    hits = [p for p in glob.glob(os.path.join(ddir, "*.tgs"))
            if f"_{idx:03d}_" in os.path.basename(p)]
    assert len(hits) == 1, (ddir, idx, hits)
    return hits[0]


def main():
    fp = json.load(open(FINGERPRINT))
    hide = set(fp.get("hide_layer_names") or [])
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)
    os.makedirs(os.path.join(OUT_DIR, "originals"), exist_ok=True)
    os.makedirs(os.path.join(OUT_DIR, "final"), exist_ok=True)
    for p in origs:
        shutil.copy(p, os.path.join(OUT_DIR, "originals", os.path.basename(p)))
    report = {}
    print(f"{'#':>2} {'role':>9} {'scope':>7} {'old box':>13} {'fitted logo':>13} "
          f"{'centre dev':>10} {'shift':>5} {'canon':>5} {'valid':>5}")
    all_ok = True
    for idx, path in enumerate(origs, 1):
        name = os.path.basename(path)
        v7_doc = load_tgs(open(final_path(V7_DIR, idx), "rb").read())
        v7_canon = json.dumps(canonical_without_logo(v7_doc), sort_keys=True)
        rows = rescale_doc_to_fit(docs[idx - 1], v7_doc, sigs, hide)
        out = dump_tgs(v7_doc)
        issues, vwarn = validate_tgs_bytes(out, idx)
        v8_doc = load_tgs(out)
        v8_canon = json.dumps(canonical_without_logo(v8_doc), sort_keys=True)
        canon_ok = v7_canon == v8_canon
        # File 016 hard rule: comp_1 stays EMPTY (proven publishable).
        comp1_empty = None
        if idx == 16:
            comp1 = next((a for a in v8_doc.get("assets") or []
                          if a.get("id") == "comp_1"), None)
            comp1_empty = comp1 is not None and not (comp1.get("layers") or [])
        with open(os.path.join(OUT_DIR, "final", name), "wb") as fh:
            fh.write(out)
        ok = canon_ok and not issues and all(
            r.get("status") == "ok" for r in rows)
        if idx == 16:
            ok = ok and bool(comp1_empty)
        all_ok = all_ok and ok
        report[idx] = {"file": name, "size": len(out), "elements": rows,
                       "canonical_identical_nonlogo": canon_ok,
                       "validator_issues": issues,
                       "validator_warnings": vwarn,
                       "comp1_empty": comp1_empty}
        for r in rows:
            if r.get("status") == "ok":
                box = "x".join(str(v) for v in r["box"])
                fit = "x".join(str(v) for v in
                               (r["measured"]["width"], r["measured"]["height"]))
                dev = ""
                if r["measured"]:
                    dev = f"{max(abs(r['measured']['centre'][0] - r['box_centre'][0]), abs(r['measured']['centre'][1] - r['box_centre'][1])):.1f}"
                print(f"{idx:>2} {r['role']:>9} {r['scope']:>7} {box:>13} "
                      f"{fit:>13} {dev:>10} {r['solved']['shift']:>5} "
                      f"{str(canon_ok):>5} {str(len(issues)):>5}")
            else:
                print(f"{idx:>2} {r['role']:>9} {r['scope']:>7}  {r.get('status')}")
    with open(os.path.join(OUT_DIR, "fit-report.json"), "w") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=1, default=str)
    blocked = sum(len(r["validator_issues"]) for r in report.values())
    print(f"done: {len(report)} files, validator blocked: {blocked}, "
          f"canonical non-logo identity + all fits ok: {all_ok}")


if __name__ == "__main__":
    main()
