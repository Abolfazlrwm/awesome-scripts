"""Pre-publish validator for TGS custom emoji.

Purpose: answer, BEFORE any Telegram upload, exactly which file
Telegram would reject and why — instead of the bot's old generic
"wrong file type" after an unknown file in the middle of a 20-file
upload. Every issue carries the file index and the layer/scope, so
the publish job can log "file #N: reason" instead of guessing.

Blocking checks (local, no Telegram call):
- gzip/JSON structure, layers present, canvas 512x512
- fr in (0, 60]; duration op/fr > 3.1s is a WARNING only (the original
  published pack itself contains a 4.02s file, so Telegram accepts it)
- size <= 64 KiB
- OUR brand layers' structural rules (the proven file-016 v5 cause:
  op clamped to doc op, explicit o and r in ks, group transform
  p = [0,0]) — applied to any layer whose name marks it as a brand
  layer ("Brand", or the historical Abolfazl_rwm/Abolfazl names).

Demoted to WARNINGS on direct Telegram evidence (2026-10-29): in the
EmojiStoreMehdi publish, files 001–011 were ACCEPTED by Telegram
while containing hidden (hd=1) layers, dangling parents, duplicate
inds, ip>op layers and empty ty=4 shape layers — those traits are
inherent to whole pack families (precomp-instance packs ship with
thousands of inert hidden layers) and are NOT what Telegram rejects.
The one rejection in that run (file 012) correlated with OUR OWN
mass purge of hidden layers/assets gutting the file's structure —
an edit-side failure, not a trait of the file. So: traits of the
original pack = warnings; structure of layers WE created = blocking.
Unreferenced assets are warnings too (Telegram tolerates them).
"""
import os

from .animation_engine import load_tgs
from .importer_guard import (
    VERTEX_WARN_LIMIT, group_vertex_count, is_paint_mixing, iter_groups,
)


def _is_brand_layer(name: str) -> bool:
    n = str(name or "")
    return "Brand" in n or "Abolfazl_rwm" in n or "Abolfazl" in n


def validate_tgs_bytes(data: bytes, index: int = 0):
    """Return (issues, warnings). issues are blocking; warnings are not."""
    issues = []
    warnings = []
    if len(data) > 64 * 1024:
        issues.append(f"فایل #{index}: حجم {len(data)} بایت از حد ۶۴KB تلگرام بیشتر است.")
    try:
        doc = load_tgs(data)
    except Exception as exc:
        return [f"فایل #{index}: ساختار TGS نامعتبر است: {str(exc)[:200]}"], warnings
    w, h = doc.get("w"), doc.get("h")
    if w != 512 or h != 512:
        issues.append(f"فایل #{index}: ابعاد {w}×{h} است، باید ۵۱۲×۵۱۲ باشد.")
    fr = doc.get("fr") or 0
    if not (0 < fr <= 60):
        issues.append(f"فایل #{index}: نرخ فریم {fr} خارج از حد (۱ تا ۶۰) است.")
    elif fr and doc.get("op"):
        dur = doc["op"] / fr
        if dur > 3.1:
            warnings.append(f"فایل #{index}: مدت {dur:.2f} ثانیه از حد ۳ ثانیه بیشتر است (هشدار؛ پک اصلی هم فایل ۴ ثانیه‌ای دارد).")
    if not doc.get("layers"):
        issues.append(f"فایل #{index}: هیچ لایه‌ای ندارد.")
    scopes = [("top", doc.get("layers") or [])]
    scopes += [(a.get("id"), a.get("layers") or []) for a in doc.get("assets") or []]
    doc_op = doc.get("op") or 0
    for scope_name, layers in scopes:
        inds = {}
        for layer in layers:
            ind = layer.get("ind")
            if ind in inds:
                warnings.append(f"فایل #{index}: شاخص لایه تکراری {ind} در {scope_name} (هشدار؛ تلگرام در پک مهدی پذیرفت).")
            inds[ind] = layer
            # Brand-text rules (file 016 regression): brand text
            # layers created by us must match the accepted v3 shape —
            # op clamped to doc op, explicit o and r in ks, group
            # transform p baked to [0,0]. v5 file 016 broke all three
            # at once and was the only file Telegram rejected, while
            # the rest of the file was byte-identical (modulo colours)
            # to accepted v3 file 016. Blocking for OUR brand layers
            # only; original layers are exempt (they shipped with
            # op 9999 etc. and Telegram accepted the original pack).
            if _is_brand_layer(layer.get("nm", "")):
                if doc_op and (layer.get("op") or 0) > doc_op:
                    issues.append(f"فایل #{index}: لایه برند «{layer.get('nm')}» در {scope_name} با op={layer.get('op')} بیشتر از op سند ({doc_op}) — علت مشکوک رد فایل ۰۱۶ در v5.")
                ks = layer.get("ks") or {}
                if "o" not in ks or "r" not in ks:
                    issues.append(f"فایل #{index}: لایه برند «{layer.get('nm')}» در {scope_name} فاقد o یا r صریح در ks است (v3 پذیرفته‌شده هر دو را داشت).")
                for group in layer.get("shapes") or []:
                    for item in group.get("it") or []:
                        if item.get("ty") == "tr":
                            p = (item.get("p") or {}).get("k") or [0, 0]
                            if len(p) >= 2 and (p[0] != 0 or p[1] != 0):
                                issues.append(f"فایل #{index}: لایه برند «{layer.get('nm')}» در {scope_name} آفست تبدیل گروه دارد (p={p})؛ باید در مختصات مسیر پخته شود.")
                                break
            if layer.get("ty") == 4 and not (layer.get("shapes") or []):
                warnings.append(f"فایل #{index}: لایه شکلی خالی «{layer.get('nm')}» در {scope_name} (هشدار؛ تلگرام در پک مهدی پذیرفت).")
            if layer.get("hd"):
                warnings.append(f"فایل #{index}: لایه پنهان «{layer.get('nm')}» در {scope_name} (هشدار؛ تلگرام در پک مهدی پذیرفت).")
            if layer.get("ip", 0) > (layer.get("op") or 0):
                warnings.append(f"فایل #{index}: لایه «{layer.get('nm')}» در {scope_name} با ip>op (هشدار؛ تلگرام در پک مهدی پذیرفت).")
        for layer in layers:
            if layer.get("parent") is not None and layer.get("parent") not in inds:
                warnings.append(f"فایل #{index}: پدر معلق {layer.get('parent')} برای لایه «{layer.get('nm')}» در {scope_name} (هشدار؛ تلگرام در پک مهدی پذیرفت).")
    # Importer-guard checks (v10, proven by live uploads 2026-10-30):
    # groups without a trailing 'tr' and the paint-mixing brand-group
    # signature are REJECTED by Telegram's importer even though
    # rlottie renders them fine — blocking. Oversized groups are a
    # warning only (the ceiling is context-dependent, file 097).
    for scope_name, layers in scopes:
        for layer in layers:
            for group in iter_groups(layer.get("shapes") or []):
                items = [it for it in group.get("it") or []
                         if isinstance(it, dict)]
                gname = group.get("nm") or "بدون نام"
                if not items or items[-1].get("ty") != "tr":
                    issues.append(
                        f"فایل #{index}: گروه «{gname}» در لایه «{layer.get('nm')}» "
                        f"({scope_name}) آیتم tr پایانی ندارد؛ ایمپورتر تلگرام "
                        "این فایل را رد می‌کند (گارد ایمپورتر آن را اصلاح می‌کند).")
                if is_paint_mixing(group):
                    issues.append(
                        f"فایل #{index}: گروه «{gname}» در لایه «{layer.get('nm')}» "
                        f"({scope_name}) امضای رنگ‌آمیزی گروهی ردشونده دارد "
                        "(زیرگروه + fl و st گروهی بدون شکل مستقیم)؛ ایمپورتر "
                        "تلگرام این فایل را رد می‌کند.")
                vcount = group_vertex_count(group)
                if vcount > VERTEX_WARN_LIMIT:
                    warnings.append(
                        f"فایل #{index}: گروه «{gname}» در لایه «{layer.get('nm')}» "
                        f"({scope_name}) {vcount} رأس دارد؛ سقف ایمپورتر "
                        "وابسته به بافت است (هشدار، فایل ۰۹۷).")
    refs = set()
    for _, layers in scopes:
        for layer in layers:
            if layer.get("refId"):
                refs.add(layer.get("refId"))
    for asset in doc.get("assets") or []:
        if asset.get("id") not in refs:
            warnings.append(f"فایل #{index}: بخش بدون ارجاع {asset.get('id')} (هشدار، نه خطا).")
    return issues, warnings


def validate_final_dir(final_dir: str):
    """Validate every .tgs in a final dir. Returns list of (index, issues, warnings)."""
    out = []
    for name in sorted(f for f in os.listdir(final_dir) if f.endswith(".tgs")):
        # Index from the ordered filename: <project>_<NNN>_...
        idx = 0
        try:
            parts = name.split("_")
            for part in parts:
                if part.isdigit() and len(part) == 3:
                    idx = int(part)
                    break
        except Exception:
            pass
        with open(os.path.join(final_dir, name), "rb") as fh:
            data = fh.read()
        issues, warnings = validate_tgs_bytes(data, idx)
        out.append((idx, name, issues, warnings))
    return out
