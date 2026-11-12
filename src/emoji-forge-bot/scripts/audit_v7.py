#!/usr/bin/env python3
"""v7 per-layer audit of the original pack vs v6.

Enumerates EVERY layer and EVERY shape group (recursively, all
scopes) of the 20 originals, classifies each as LOGO/BRAND, SCENE or
THEME with the shared rule set in emoji_forge.v7 (geometry signatures
+ solo-render-verified overrides — not names alone), and records
whether v6 deleted it and whether it should have been deleted.

Also renders solo images of the key suspect elements (the evidence
the classification rests on) to /tmp/v7_qa/solo/ and original-vs-v6
difference frames to /tmp/v7_qa/diff/.

Writes data/projects/forge-v7-test/audit.md and audit.json.
"""
import copy
import glob
import hashlib
import io
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
sys.path.insert(0, os.path.dirname(__file__))

from PIL import Image  # noqa: E402

from emoji_forge.animation_engine import _walk, load_tgs, pack_common_signatures  # noqa: E402
from emoji_forge.v4 import _scope_layers  # noqa: E402
from emoji_forge.v7 import classify_group, classify_layer, group_has_brand_geometry  # noqa: E402
from qa_v5 import render_frame  # noqa: E402

ORIG_DIR = "data/projects/forge-20261028-155740-3f1382/originals"
V6_DIR = "data/projects/forge-v6-test/final"
OUT_MD = "data/projects/forge-v7-test/audit.md"
OUT_JSON = "data/projects/forge-v7-test/audit.json"
SOLO_DIR = "/tmp/v7_qa/solo"
DIFF_DIR = "/tmp/v7_qa/diff"


def path_hash(node):
    k = (node.get("ks") or {}).get("k") or {}

    def rec(x):
        if isinstance(x, dict):
            if "v" in x:
                return ("P", repr((x.get("v"), x.get("i"), x.get("o"))))
            return ("D", tuple((kk, rec(vv)) for kk, vv in sorted(x.items())))
        if isinstance(x, list):
            return ("L", tuple(rec(v) for v in x))
        return ("A", x)

    return hashlib.sha256(repr(rec(k)).encode()).hexdigest()[:10]


def geom_hash(node):
    """Structure+path-geometry hash of a group (fills ignored, so a
    theme recolour does not masquerade as a deletion)."""
    if not isinstance(node, dict):
        return "?"
    if node.get("ty") == "sh":
        return path_hash(node)
    parts = [node.get("ty")]
    for item in node.get("it") or []:
        parts.append(geom_hash(item))
    return hashlib.sha256(repr(parts).encode()).hexdigest()[:10]


def layer_group_hashes(layer):
    out = {}

    def visit(item, path):
        if isinstance(item, dict) and item.get("ty") == "gr":
            out[path] = geom_hash(item)
            for j, sub in enumerate(item.get("it") or []):
                visit(sub, f"{path}>{j}:{sub.get('nm') if isinstance(sub, dict) else '?'}")

    for gi, item in enumerate(layer.get("shapes") or []):
        if isinstance(item, dict):
            visit(item, f"{gi}:{item.get('nm')}")
    return out


def scopes_of(doc):
    return [(None, doc.get("layers") or [])] + [
        (a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]


def final_path(ddir, idx):
    hits = [p for p in glob.glob(os.path.join(ddir, "*.tgs"))
            if f"_{idx:03d}_" in os.path.basename(p)]
    return hits[0] if hits else None


# ---------------- solo renders ----------------

def _render_doc(doc, tag):
    path = f"/tmp/v7_qa/solo_{tag}.json"
    with open(path, "w") as fh:
        json.dump(doc, fh)
    op = doc.get("op", 180)
    best = None
    for fr in (0, int(op * .25), int(op * .5), int(op * .75), op - 1):
        im = render_frame(path, fr, 256)
        score = sum(1 for px in im.getdata() if sum(px) > 36)
        if best is None or score > best[0]:
            best = (score, im)
    return best[1]


def solo_layer(doc, scope, layer, tag):
    if scope is None:
        d = {"v": "5.7.4", "fr": doc.get("fr", 60), "ip": 0,
             "op": doc.get("op", 180), "w": 512, "h": 512, "nm": "solo",
             "layers": [], "assets": copy.deepcopy(doc.get("assets") or [])}
        lay = copy.deepcopy(layer)
        lay.pop("parent", None)
        d["layers"] = [lay]
        im = _render_doc(d, tag)
        im.save(os.path.join(SOLO_DIR, f"{tag}.png"))
        return
    # Asset-scope layer: a bare promotion loses the instance transform
    # that places it on screen (files 017-019 badge layers rendered
    # black that way). Context solo instead: keep the whole doc, but
    # in the target asset keep ONLY this layer, and remove every
    # top-level layer that does not (transitively) instance the chain
    # leading to it — the element appears at its true on-screen place.
    from emoji_forge.v4 import _referencing_layers
    d = copy.deepcopy(doc)
    keep_top = set()
    frontier = [scope]
    seen = set()
    while frontier:
        aid = frontier.pop()
        if aid in seen:
            continue
        seen.add(aid)
        for ref_scope, ref_layer in _referencing_layers(doc, aid):
            if ref_scope is None:
                keep_top.add(ref_layer.get("ind"))
            else:
                frontier.append(ref_scope)
    d["layers"] = [l for l in d.get("layers") or []
                   if l.get("ind") in keep_top]
    for asset in d.get("assets") or []:
        if asset.get("id") == scope:
            # Keep the target layer AND its ancestor chain (guide
            # nulls) — dropping a parent breaks the renderer and the
            # placement.
            by_ind = {l.get("ind"): l for l in asset.get("layers") or []}
            keep = set()
            cur = layer
            while cur is not None:
                keep.add(cur.get("ind"))
                cur = by_ind.get(cur.get("parent"))
            asset["layers"] = [l for l in asset.get("layers") or []
                               if l.get("ind") in keep]
    im = _render_doc(d, tag)
    im.save(os.path.join(SOLO_DIR, f"{tag}.png"))


def solo_group(doc, scope, layer, group, tag):
    d = {"v": "5.7.4", "fr": doc.get("fr", 60), "ip": 0,
         "op": doc.get("op", 180), "w": 512, "h": 512, "nm": "solo",
         "layers": [], "assets": copy.deepcopy(doc.get("assets") or [])}
    lay = copy.deepcopy(layer)
    lay.pop("parent", None)
    lay["shapes"] = [copy.deepcopy(group)]
    d["layers"] = [lay]
    im = _render_doc(d, tag)
    im.save(os.path.join(SOLO_DIR, f"{tag}.png"))


def render_solos(docs):
    os.makedirs(SOLO_DIR, exist_ok=True)

    def find(doc, scope, name):
        for l in _scope_layers(doc, scope):
            if l.get("nm") == name:
                return l
        return None

    d11 = docs[10]
    ton = find(d11, None, "TON")
    for gi, g in enumerate(ton.get("shapes") or []):
        if isinstance(g, dict) and g.get("ty") == "gr":
            solo_group(d11, None, ton, g, f"f11_TON_group{gi}_{g.get('nm')}")
    solo_layer(d11, None, ton, "f11_TON_layer")
    for nm in ("TON-Rebro-LEFT", "TON-Rebro-LEFT 3"):
        lay = find(d11, None, nm)
        if lay:
            solo_layer(d11, None, lay, f"f11_{nm}")
    d13 = docs[12]
    free = find(d13, None, "FREE")
    for gi, g in enumerate(free.get("shapes") or []):
        if isinstance(g, dict) and g.get("ty") == "gr":
            solo_group(d13, None, free, g, f"f13_FREE_group{gi}_{g.get('nm')}")
    solo_layer(d13, None, free, "f13_FREE_layer")
    d17 = docs[16]
    for nm in ("Logo CIrcle", "Logo CIrcle 2", "Logo in"):
        lay = find(d17, "comp_2", nm)
        if lay:
            solo_layer(d17, "comp_2", lay, f"f17_{nm}")
    d9 = docs[8]
    solo_layer(d9, None, find(d9, None, "Logo"), "f09_Logo_layer")
    d1 = docs[0]
    solo_layer(d1, None, find(d1, None, "emblem 2"), "f01_emblem2_layer")
    d16 = docs[15]
    solo_layer(d16, "comp_1", find(d16, "comp_1", "CDC Outlines"),
               "f16_comp1_CDC_layer")
    d15 = docs[14]
    solo_layer(d15, "comp_3", find(d15, "comp_3", "$SVN Outlines"),
               "f15_comp3_SVN_layer")
    solo_layer(d15, "comp_2", find(d15, "comp_2", "CDC Outlines"),
               "f15_comp2_CDC_layer")
    d4 = docs[3]
    solo_layer(d4, "comp_0", find(d4, "comp_0", "Shape Layer 7"),
               "f04_orbit_triangle_layer")


def render_diffs(origs):
    os.makedirs(DIFF_DIR, exist_ok=True)
    import numpy as np
    for idx, path in enumerate(origs, 1):
        doc = load_tgs(open(path, "rb").read())
        mid = int((doc.get("op") or 180) * 0.5)
        a = np.asarray(render_frame(path, mid, 256), dtype=int)
        b = np.asarray(render_frame(final_path(V6_DIR, idx), mid, 256), dtype=int)
        diff = np.clip(np.abs(a - b) * 2, 0, 255).astype("uint8")
        Image.fromarray(diff).save(os.path.join(DIFF_DIR, f"diff_{idx:02d}.png"))


def main():
    origs = sorted(glob.glob(os.path.join(ORIG_DIR, "*.tgs")))
    docs = [load_tgs(open(p, "rb").read()) for p in origs]
    sigs = pack_common_signatures(docs)
    render_solos(docs)
    render_diffs(origs)

    totals = {"layers": 0, "groups": 0}
    class_counts = {"LOGO/BRAND": 0, "SCENE": 0, "THEME": 0}
    false_deleted = []
    missed_logos = []
    per_file = {}

    for idx, doc in enumerate(docs, 1):
        v6 = load_tgs(open(final_path(V6_DIR, idx), "rb").read())
        v6_scopes = {sc: {l.get("ind"): l for l in ls}
                     for sc, ls in scopes_of(v6)}
        rows = []
        for scope, layers in scopes_of(doc):
            v6_layers = v6_scopes.get(scope, {})
            for layer in layers:
                totals["layers"] += 1
                lname = str(layer.get("nm", ""))
                lcls = classify_layer(doc, scope, layer, sigs)
                class_counts[lcls] = class_counts.get(lcls, 0) + 1
                v6_layer = v6_layers.get(layer.get("ind"))
                if v6_layer is None:
                    ldel = "yes"
                elif str(v6_layer.get("nm", "")) in ("Abolfazl_rwm Brand",):
                    ldel = "replaced"
                else:
                    ldel = "no"
                lshould = "yes" if lcls == "LOGO/BRAND" else "no"
                rows.append({"element": f"layer [{scope or 'top'}] ind={layer.get('ind')} "
                                        f"ty={layer.get('ty')} «{lname}»",
                             "class": lcls, "deleted_v6": ldel,
                             "should_delete": lshould})
                if lcls == "LOGO/BRAND" and ldel == "no" and v6_layer is not None:
                    # A missed logo must be provable geometrically: at
                    # least one LOGO-class group's geometry still
                    # present in the v6 layer. Carriers (ty=0, no
                    # shapes) and group-replaced containers fail this
                    # test correctly.
                    v6_hashes_l = set(layer_group_hashes(v6_layer).values())
                    for g in layer.get("shapes") or []:
                        if isinstance(g, dict) and g.get("ty") == "gr" and \
                                classify_group(doc, scope, layer, g, sigs) == "LOGO/BRAND" and \
                                geom_hash(g) in v6_hashes_l:
                            missed_logos.append(
                                f"file {idx:02d} layer {scope or 'top'}:{lname}")
                            break
                # groups (recursive)
                v6_hashes = set()
                if v6_layer is not None and ldel != "replaced":
                    v6_hashes = set(layer_group_hashes(v6_layer).values())

                def visit(item, path, top_cls):
                    if not isinstance(item, dict) or item.get("ty") != "gr":
                        return
                    totals["groups"] += 1
                    if group_has_brand_geometry(item, sigs):
                        gcls = "LOGO/BRAND"
                    elif ">" not in path:
                        gcls = classify_group(doc, scope, layer, item, sigs)
                    else:
                        gcls = top_cls
                    class_counts[gcls] = class_counts.get(gcls, 0) + 1
                    if ldel == "replaced":
                        gdel = "replaced"
                    elif ldel == "yes":
                        gdel = "yes"
                    else:
                        gdel = "no" if geom_hash(item) in v6_hashes else "yes"
                    gshould = "yes" if gcls == "LOGO/BRAND" else "no"
                    rows.append({"element": f"  group [{scope or 'top'}] "
                                            f"{lname} :: {path}",
                                 "class": gcls, "deleted_v6": gdel,
                                 "should_delete": gshould})
                    if gcls == "LOGO/BRAND" and gdel == "no":
                        missed_logos.append(
                            f"file {idx:02d} group {scope or 'top'}:{lname}::{path}")
                    if gshould == "no" and gdel in ("yes", "replaced"):
                        # Only count VISUAL false deletions: the group
                        # must contain at least one path.
                        if any(isinstance(n, dict) and n.get("ty") == "sh"
                               for n in _walk(item)):
                            false_deleted.append(
                                f"file {idx:02d} group {scope or 'top'}:{lname}::{path} ({gcls})")
                    for j, sub in enumerate(item.get("it") or []):
                        visit(sub, f"{path}>{j}:{sub.get('nm') if isinstance(sub, dict) else '?'}", gcls)

                for gi, item in enumerate(layer.get("shapes") or []):
                    if isinstance(item, dict) and item.get("ty") == "gr":
                        visit(item, f"{gi}:{item.get('nm')}", None)
                if lshould == "no" and ldel in ("yes", "replaced"):
                    has_geom = any(isinstance(n, dict) and n.get("ty") == "sh"
                                   for n in _walk(layer.get("shapes") or []))
                    if has_geom and layer.get("ty") == 4:
                        # Layer-level false deletion only when the layer
                        # itself was removed (not group-replaced).
                        if ldel == "yes":
                            false_deleted.append(
                                f"file {idx:02d} layer {scope or 'top'}:{lname} ({lcls})")
        per_file[idx] = rows

    with open(OUT_JSON, "w") as fh:
        json.dump({"totals": totals, "class_counts": class_counts,
                   "false_deleted": false_deleted,
                   "missed_logos": missed_logos, "per_file": per_file},
                  fh, ensure_ascii=False, indent=1)

    lines = ["# v7 Per-Layer Audit — original pack vs v6", "",
             "Classification uses the shared rule set in `src/emoji_forge/v7.py`:",
             "pack-common geometry signatures and brand-geometry tests first,",
             "solo-render-verified overrides for composite/baked elements",
             "(renders in `/tmp/v7_qa/solo/`), names only as a last resort.",
             "Original-vs-v6 difference frames: `/tmp/v7_qa/diff/`.", "",
             "## Summary", "",
             f"- Total layers audited: **{totals['layers']}**",
             f"- Total shape groups audited (recursive): **{totals['groups']}**",
             f"- Total elements: **{totals['layers'] + totals['groups']}**",
             f"- By class: " + ", ".join(
                 f"{k} = {v}" for k, v in sorted(class_counts.items())),
             f"- Falsely deleted in v6 (scene/theme elements removed): "
             f"**{len(false_deleted)}**",
             *[f"  - {x}" for x in false_deleted],
             f"- Logos missed in v6 (brand elements still present): "
             f"**{len(missed_logos)}**",
             *[f"  - {x}" for x in missed_logos],
             ""]
    for idx in sorted(per_file):
        lines.append(f"## File {idx:02d}")
        lines.append("")
        lines.append("| element | classification | deleted in v6? | should delete? |")
        lines.append("|---|---|---|---|")
        for r in per_file[idx]:
            lines.append(f"| {r['element']} | {r['class']} | "
                         f"{r['deleted_v6']} | {r['should_delete']} |")
        lines.append("")
    with open(OUT_MD, "w") as fh:
        fh.write("\n".join(lines))
    print(f"layers={totals['layers']} groups={totals['groups']} "
          f"classes={class_counts}")
    print(f"false_deleted={len(false_deleted)} missed_logos={len(missed_logos)}")
    for x in false_deleted:
        print("  FD:", x)
    for x in missed_logos:
        print("  ML:", x)


if __name__ == "__main__":
    main()
