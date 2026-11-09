"""Generic pack audit (v9): logo-candidate classification for ANY
TGS pack — no pack-specific names baked in.

Why this exists: the v7 audit proved (on the sVnTeam pack) that
name-based deletion alone is not accurate enough — scene layers
were deleted and baked-in marks were missed. The owner's standing
rule is now: for an UNKNOWN pack, classify from evidence, and when
the evidence is weak, do NOT guess — route the pack to Mati's
personal review instead of silently deleting uncertain layers.

Evidence, in order of strength (pack-common geometry is PRIMARY):
1. pack_common: the element contains a shape whose normalised
   geometry signature appears across the pack (pack_common_signatures
   — identical geometry in many files is a watermark/logo by
   construction, for any pack, any names).
2. name: the layer/group name matches GENERIC brand words
   (logo/brand/watermark/badge/text/outlines/...) — generic English
   After-Effects vocabulary, NOT any one pack's brand names. The
   old sVnTeam strings (CDC/SVN/TON/FREE) survive only as EXAMPLE
   patterns in tests, never as deletion rules here.
3. solo_render: an optional caller-provided check (the render-based
   audit in scripts/audit_v7.py style) confirming the element looks
   like a standalone mark.

Classes: LOGO (candidate for removal/replacement), SCENE (keep),
THEME (blue-dominant decoration; recoloured, never deleted).

Confidence: "high" when logo evidence (pack-common or name) covers
at least half the files (or the single file, for a 1-file pack);
otherwise "low" — the bot then routes full processing to Mati's
review queue instead of the automatic engine.
"""
import re

from .animation_engine import _walk, pack_common_signatures, shape_signature
from .v4 import is_blue_dominant

GENERIC_BRAND_NAME_RE = re.compile(
    r"logo|brand|watermark|wmark|badge|label|sign(age)?|mark\b|"
    r"copyright|credit|trademark|™|©",
    re.IGNORECASE,
)
TEXT_LAYER_RE = re.compile(r"outlines?\b|text\b|title\b|caption\b", re.IGNORECASE)


def _fill_colours(node) -> list:
    cols = []
    for sub in _walk(node):
        if isinstance(sub, dict) and sub.get("ty") in ("fl", "gf"):
            k = (sub.get("c") or {}).get("k")
            if isinstance(k, list) and len(k) >= 3 and all(
                    isinstance(x, (int, float)) for x in k[:3]):
                cols.append(tuple(k[:3]))
    return cols


def _group_has_common_geometry(group, signatures) -> bool:
    if not signatures:
        return False
    for node in _walk(group):
        if isinstance(node, dict) and node.get("ty") == "sh":
            if shape_signature((node.get("ks") or {}).get("k") or {}) in signatures:
                return True
    return False


def _classify_group(group, signatures):
    evidence = []
    if _group_has_common_geometry(group, signatures):
        evidence.append("pack_common")
    name = str(group.get("nm", ""))
    if GENERIC_BRAND_NAME_RE.search(name):
        evidence.append("name")
    if evidence:
        return "LOGO", evidence
    cols = _fill_colours(group)
    if cols:
        blue = sum(1 for r, g, b in cols if is_blue_dominant(r, g, b))
        if blue >= max(1, len(cols) // 2):
            return "THEME", ["theme_colour"]
    return "SCENE", []


def _classify_layer(doc, scope, layer, signatures, solo_check=None, doc_index=0):
    name = str(layer.get("nm", ""))
    evidence = []
    if layer.get("ty") != 4:
        return "SCENE", evidence, []
    if GENERIC_BRAND_NAME_RE.search(name):
        evidence.append("name")
    if TEXT_LAYER_RE.search(name):
        evidence.append("text_name")
    groups = [g for g in layer.get("shapes") or []
              if isinstance(g, dict) and g.get("ty") == "gr"]
    group_rows = []
    for group in groups:
        gclass, gev = _classify_group(group, signatures)
        if "pack_common" in gev:
            evidence.append("pack_common")
        group_rows.append({"group": str(group.get("nm", "")),
                           "class": gclass, "evidence": gev})
    if solo_check is not None:
        try:
            if solo_check(doc_index, doc, scope, layer):
                evidence.append("solo_render")
        except Exception:
            pass
    classes = [r["class"] for r in group_rows]
    if "pack_common" in evidence or "solo_render" in evidence:
        layer_class = "LOGO"
    elif evidence and (not classes or "LOGO" in classes):
        layer_class = "LOGO"
    elif classes and all(c == "LOGO" for c in classes):
        layer_class = "LOGO"
    elif classes and all(c == "THEME" for c in classes):
        layer_class = "THEME"
    elif "LOGO" in classes:
        layer_class = "MIXED"  # brand baked into a scene/theme layer
    else:
        layer_class = "SCENE"
    return layer_class, sorted(set(evidence)), group_rows


def audit_pack(docs: list, solo_check=None) -> dict:
    """Audit a pack given as a list of parsed TGS docs (load_tgs).

    Returns a JSON-able report (see module docstring). The pipeline
    consumes `logo_layer_names` (only when confidence is high) and
    the bot consumes `confidence` for the review-routing rule."""
    docs = [d for d in docs if isinstance(d, dict)]
    signatures = pack_common_signatures(docs)
    elements = []
    logo_names = set()
    stats = {"LOGO": 0, "SCENE": 0, "THEME": 0, "MIXED": 0}
    docs_with_common = set()
    docs_with_name = set()
    for di, doc in enumerate(docs):
        scopes = [(None, doc.get("layers") or [])]
        scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
        for scope, layers in scopes:
            for layer in layers:
                lclass, evidence, group_rows = _classify_layer(
                    doc, scope, layer, signatures, solo_check, di)
                if lclass not in stats:
                    stats[lclass] = 0
                stats[lclass] += 1
                if "pack_common" in evidence:
                    docs_with_common.add(di)
                if "name" in evidence or "text_name" in evidence:
                    docs_with_name.add(di)
                if lclass in ("LOGO", "MIXED"):
                    logo_names.add(str(layer.get("nm", "")))
                elements.append({
                    "doc": di, "scope": scope or "top",
                    "layer": str(layer.get("nm", "")),
                    "class": lclass, "evidence": evidence,
                    "groups": group_rows,
                })
    total = max(1, len(docs))
    need = max(1, (total + 1) // 2)
    high = len(docs_with_common) >= need or len(docs_with_name) >= need
    reasons = []
    if docs_with_common:
        reasons.append(f"شکل مشترک پک در {len(docs_with_common)} از {total} فایل پیدا شد.")
    if docs_with_name:
        reasons.append(f"نامزد لوگویی از روی نام لایه در {len(docs_with_name)} از {total} فایل پیدا شد.")
    if not high:
        reasons.append("شواهد لوگو ضعیف است؛ حذف خودکار خطرناک است و پک باید دستی بررسی شود.")
    return {
        "version": 1,
        "docs": len(docs),
        "signatures": len(signatures),
        "elements": elements,
        "logo_layer_names": sorted(n for n in logo_names if n),
        "confidence": "high" if high else "low",
        "reasons": reasons,
        "stats": stats,
    }
