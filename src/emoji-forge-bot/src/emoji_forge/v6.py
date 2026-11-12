"""v6 engine: full optimisation pass over v5 finals.

User order 2026-10-29 08:48: "bring everything to the most optimal
state". v6 does NOT change v5's placement rule (text at the old logo's
exact index/scope/parent, verbatim ks, on-screen size) — it normalises
and shrinks what v5 produced:

1. File 016 fix (root cause, see TEST-REPORT.md v6 section):
   Stripped of gradient colours and the comp_0 text layer, v5 file 016
   is byte-identical to v3 file 016, which Telegram accepted. The only
   remaining differences are in the brand text layer itself and the
   recoloured gradients. Each difference individually also appears in
   files Telegram accepted, so no single element is proven — but the
   brand text layer in file 016 combined three abnormal traits at
   once: op 9999 (far above doc op 180 and the referencing instance's
   op 180), ks with NO explicit opacity or rotation, and a group
   transform offset instead of baked path coordinates. v3's accepted
   text layer had op 180, explicit o=100 and r=0, and group transform
   p=[0,0]. v6 therefore normalises EVERY brand text layer to the
   accepted shape: op clamped to doc op, explicit o and r in ks, and
   the group offset baked into the path coordinates (p reset to
   [0,0]). Rendering is unchanged: the instance op already capped
   the text at doc op, o=100/r=0 are the defaults, and baking an
   offset into coordinates is an identity transform.
2. Size: prune assets that no layer references (tgs_prune_unused_assets
   already exists; v6 runs it again over v5 finals), and remove
   redundant keyframes: a middle keyframe whose value equals both
   neighbours' values (exact equality) cannot change interpolation,
   because interpolation between two equal values is constant.
3. Animation: brand text op clamping (above) also fixes layers whose
   op exceeds doc op; other layers' op values are original design and
   are left alone. Keyframe pops that exist in the original (e.g. the
   file 009 scale pop) are design, not defects, and are kept.

Returns (output_bytes, stats) where stats reports what was changed.
"""

from .animation_engine import (
    dump_tgs,
    load_tgs,
    tgs_prune_unused_assets,
)


def _bake_group_offset(layer: dict) -> int:
    """Bake each shape group's transform p into its path coordinates
    and reset p to [0,0]. Returns number of groups baked."""
    baked = 0
    for group in layer.get("shapes") or []:
        tr = None
        for item in group.get("it") or []:
            if item.get("ty") == "tr":
                tr = item
        if tr is None:
            continue
        p = (tr.get("p") or {}).get("k") or [0, 0]
        if len(p) < 2 or (p[0] == 0 and p[1] == 0):
            continue
        dx, dy = float(p[0]), float(p[1])
        for item in group.get("it") or []:
            if item.get("ty") != "sh":
                continue
            k = (item.get("ks") or {}).get("k") or {}
            for arr_key in ("v", "i", "o"):
                arr = k.get(arr_key) or []
                if arr_key == "v":
                    for pt in arr:
                        pt[0] += dx
                        pt[1] += dy
                # in/out tangents are relative vectors: unchanged.
        tr["p"]["k"] = [0, 0]
        baked += 1
    return baked


def _dedupe_keyframes(node) -> int:
    """Remove middle keyframes whose value equals both neighbours'.
    Walks the whole doc. Returns count removed."""
    removed = 0
    if isinstance(node, dict):
        for key, val in list(node.items()):
            if key in ("p", "a", "s", "r", "o") and isinstance(val, dict) \
                    and val.get("a") == 1 and isinstance(val.get("k"), list):
                frames = val["k"]
                if len(frames) >= 3 and all(
                        isinstance(f, dict) and "t" in f and "s" in f
                        for f in frames):
                    kept = [frames[0]]
                    for i in range(1, len(frames) - 1):
                        prev_s, cur_s, next_s = frames[i - 1]["s"], frames[i]["s"], frames[i + 1]["s"]
                        if cur_s == prev_s == next_s:
                            removed += 1
                            continue
                        kept.append(frames[i])
                    kept.append(frames[-1])
                    val["k"] = kept
            else:
                removed += _dedupe_keyframes(val)
    elif isinstance(node, list):
        for item in node:
            removed += _dedupe_keyframes(item)
    return removed


def _normalise_brand_text(doc: dict) -> dict:
    """Normalise every 'Abolfazl_rwm Brand' layer to the accepted v3 shape.
    Returns counts: clamped, o_added, r_added, baked."""
    doc_op = doc.get("op") or 0
    stats = {"clamped": 0, "o_added": 0, "r_added": 0, "baked": 0}
    scopes = [doc.get("layers") or []]
    scopes += [a.get("layers") or [] for a in doc.get("assets") or []]
    for layers in scopes:
        for layer in layers:
            if "Abolfazl_rwm" not in str(layer.get("nm", "")):
                continue
            if doc_op and (layer.get("op") or 0) > doc_op:
                layer["op"] = doc_op
                stats["clamped"] += 1
            ks = layer.setdefault("ks", {})
            if "o" not in ks:
                ks["o"] = {"a": 0, "k": 100}
                stats["o_added"] += 1
            if "r" not in ks:
                ks["r"] = {"a": 0, "k": 0}
                stats["r_added"] += 1
            stats["baked"] += _bake_group_offset(layer)
    return stats


def process_tgs_v6(data: bytes) -> tuple:
    """Optimise one v5 final. Returns (output_bytes, stats)."""
    doc = load_tgs(data)
    before_assets = len(doc.get("assets") or [])
    pruned = tgs_prune_unused_assets(doc)
    norm = _normalise_brand_text(doc)
    deduped = _dedupe_keyframes(doc)
    out = dump_tgs(doc)
    stats = {
        "assets_before": before_assets,
        "assets_after": len(doc.get("assets") or []),
        "assets_pruned": pruned,
        "brand_clamped": norm["clamped"],
        "brand_o_added": norm["o_added"],
        "brand_r_added": norm["r_added"],
        "brand_baked": norm["baked"],
        "keyframes_removed": deduped,
        "size_before": len(data),
        "size_after": len(out),
    }
    return out, stats
