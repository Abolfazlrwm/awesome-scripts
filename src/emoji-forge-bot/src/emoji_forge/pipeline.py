"""Pipeline: validate -> clean -> brand -> validate output limits.

Only the parts that need re-running are re-run: preview processes samples;
full processing reuses cleaned files when the region/mode did not change.
A per-file result is always recorded; one bad file never kills the pack.
"""
import os

from .animation_engine import (
    choose_font_for_tgs, dump_tgs, load_tgs, pack_common_signatures, process_webm,
    tgs_add_text_layer, tgs_duotone, tgs_has_keyframed_fills, tgs_hide_layers,
    tgs_hide_text_layers, tgs_logo_candidates, tgs_prune_unused_assets,
    tgs_recolor, tgs_remove_layers, tgs_remove_shapes_by_signatures,
    tgs_prune_empty_shape_layers,
    tgs_remove_watermark_shapes, text_to_lottie_shapes,
)
from .brand import BrandProfile
from .cleaning import (
    PRESETS, Region, clean_region, clean_region_smart, detect_uniform_blob_regions,
    limited_redesign, strip_edge_bars,
)
from .export import build_report, write_report_files, write_zip
from .formats import STICKER_LIMITS, describe
from .image_engine import (
    gradient_palette_hex, load_image, neutralize_duotone, neutralize_gradient,
    overlay_logo, overlay_name, overlay_watermark, recolor_non_skin,
    save_png, save_webp, to_square_100, to_sticker_512,
)
from .security import validate_upload
from .storage import ordered_name, project_dir, write_json


def sample_indices(count: int, limit: int = 3) -> list:
    """First, middle and last samples for preview (up to `limit`)."""
    if count <= 0:
        return []
    if count <= limit:
        return list(range(count))
    picks = {0, count - 1, count // 2}
    return sorted(picks)[:limit]


MODES = {
    # preserve: only resize/format conversion, nothing else changes.
    # clean: cleaning only. redesign: cleaning + limited colour shift.
    # branding (and old alias "faithful"): cleaning + recolour + logo + name + watermark.
    # strip: the user's philosophy — strip the pack raw (remove old logo
    # bars + fully neutralise colours), then rebuild it with the user's
    # colours, name and logo. Skin is NOT protected in strip mode.
    # v4: selective theme recolour (blue->red, subjects preserved) +
    # exact on-screen logo-box text replacement (see v4.py).
    # v8 (v9): the v7/v8 logo engine for ANY user — traced logo image
    # placed contain-fit inside the old logo slot; without a logo
    # image it falls back to the v5 text path (see process_tgs).
    "preserve", "clean", "redesign", "branding", "faithful", "strip", "v4", "v5", "v8",
}


def _strip_rebuild(image, brand: BrandProfile, region: Region | None, logo_data: bytes | None):
    """Strip + rebuild for one raster image. Returns (image, warnings)."""
    warnings = []
    if region is not None:
        image = clean_region_smart(image, region)
    image, stripped = strip_edge_bars(image)
    if stripped:
        warnings.append(f"نوار لوگوی لبه حذف شد: {', '.join(stripped)}.")
    else:
        warnings.append("نوار لوگوی یکدست در لبه پیدا نشد؛ اگر لوگو وسط تصویر است، با دکمه «ناحیه لوگوی قدیمی» آن را انتخاب کنید.")
    # Uniform blobs in edge bands (any colour, any emoji): auto-remove.
    try:
        for blob in detect_uniform_blob_regions(image):
            image = clean_region_smart(image, blob)
            warnings.append(f"حباب یکدست لوگو در لبه شناسایی و حذف شد: ({blob.x0},{blob.y0} تا {blob.x1},{blob.y1}).")
    except Exception:
        pass
    if brand.full_recolor:
        image = neutralize_duotone(image, brand.primary_hex, brand.secondary_hex) if not brand.gradient_enabled else neutralize_gradient(image, brand.primary_hex)
        if brand.gradient_enabled:
            warnings.append(f"طیف رنگی فعال است — از رنگ {brand.primary_hex} این درجه‌ها ساخته شد: {', '.join(gradient_palette_hex(brand.primary_hex))}.")
        warnings.append("بازسازی رنگی کامل فعال بود: رنگ‌ها کاملاً خنثی و با رنگ‌های شما بازسازی شدند (در این حالت رنگ مو و کاراکتر هم تغییر می‌کند).")
    else:
        warnings.append("کاراکتر حفظ شد: رنگ مو و رنگ‌های اصلی ایموجی تغییر نکردند؛ فقط لوگوی قدیمی حذف و نام/لوگوی شما اضافه شد.")
    if logo_data:
        logo = load_image(logo_data)
        glow = brand.glow_hex if brand.lighting_style == "neon" else None
        image = overlay_logo(image, logo, brand.logo_position, brand.logo_size_pct,
                             glow_hex=glow, glow_strength=0.35 if glow else 0.0)
    if brand.brand_name:
        image, warn = overlay_name(image, brand.brand_name, brand.primary_hex, font_style=brand.font_style)
        if warn:
            warnings.append(warn)
    if brand.watermark_text:
        wm_position = brand.watermark_position
        if logo_data and wm_position == brand.logo_position:
            for alt in ("bottom-left", "bottom-right", "top-left", "top-right", "center"):
                if alt != brand.logo_position:
                    wm_position = alt
                    break
            warnings.append(f"واترمارک از «{brand.watermark_position}» به «{wm_position}» منتقل شد تا روی لوگو نیفتد.")
        image, warn = overlay_watermark(image, brand.watermark_text,
                                        wm_position, brand.watermark_opacity,
                                        font_style=brand.font_style)
        if warn:
            warnings.append(warn)
    return image, warnings


def process_static(data: bytes, brand: BrandProfile, region: Region | None,
                   mode: str, output_kind: str, logo_data: bytes | None = None):
    """Returns (output_bytes, extension, warnings list)."""
    if mode not in MODES:
        raise ValueError(f"حالت پردازش نامعتبر است: {mode}")
    warnings = []
    image = load_image(data)
    if region is not None:
        area_fraction = ((region.x1 - region.x0) * (region.y1 - region.y0)) / 1_000_000
        if area_fraction > 0.25:
            warnings.append("ناحیه پاک‌سازی بزرگ است (بیش از ۲۵٪ تصویر)؛ پرکردن با میانگین حاشیه ممکن است بافت را تخت کند — پیش‌نمایش را حتماً بررسی کنید.")
    if mode == "clean":
        if region is not None:
            image = clean_region(image, region)
    elif mode == "strip":
        image, strip_warnings = _strip_rebuild(image, brand, region, logo_data)
        warnings.extend(strip_warnings)
    elif mode == "redesign":
        image = limited_redesign(image, region, brand.redesign_intensity)
    elif mode in ("branding", "faithful"):
        if region is not None:
            image = clean_region(image, region)
        if brand.fidelity < 100:
            strength = (100 - brand.fidelity) / 100.0 * 0.5
            image = recolor_non_skin(image, brand.primary_hex, strength)
        if logo_data:
            logo = load_image(logo_data)
            glow = brand.glow_hex if brand.lighting_style == "neon" else None
            image = overlay_logo(image, logo, brand.logo_position, brand.logo_size_pct,
                                 glow_hex=glow, glow_strength=0.35 if glow else 0.0)
        if brand.brand_name:
            image, warn = overlay_name(image, brand.brand_name, brand.primary_hex, font_style=brand.font_style)
            if warn:
                warnings.append(warn)
        if brand.watermark_text:
            wm_position = brand.watermark_position
            if logo_data and wm_position == brand.logo_position:
                for alt in ("bottom-left", "bottom-right", "top-left", "top-right", "center"):
                    if alt != brand.logo_position:
                        wm_position = alt
                        break
                warnings.append(f"واترمارک از «{brand.watermark_position}» به «{wm_position}» منتقل شد تا روی لوگو نیفتد.")
            image, warn = overlay_watermark(image, brand.watermark_text,
                                            wm_position, brand.watermark_opacity,
                                            font_style=brand.font_style)
            if warn:
                warnings.append(warn)
    # mode == "preserve": image untouched here.
    final = to_square_100(image) if output_kind == "emoji" else to_sticker_512(image)
    import io
    out_bytes = None
    for quality in (90, 70, 50, 30):
        buf = io.BytesIO()
        final.convert("RGBA").save(buf, format="WEBP", quality=quality, method=6)
        out_bytes = buf.getvalue()
        if len(out_bytes) <= STICKER_LIMITS["static"]["max_bytes"]:
            break
    if len(out_bytes) > STICKER_LIMITS["static"]["max_bytes"]:
        warnings.append(f"خروجی ثابت {len(out_bytes)} بایت است و از حد ۵۱۲KB تلگرام بیشتر است.")
    return out_bytes, ".webp", warnings


def detect_pack_theme_hue(docs: list) -> float | None:
    """Pack-level theme hue (degrees): the dominant saturated hue
    across all flat fill/stroke colours of the pack's TGS docs.
    Per-file detection would misread subject-heavy files (a yellow
    chick must not become the 'theme'); the pack aggregate is the
    store's brand colour. Returns None when too few samples."""
    import colorsys as _cs

    from .animation_engine import _flat_colour, _walk
    bins = [0.0] * 36
    total = 0
    for doc in docs:
        scopes = [doc.get("layers") or []] + [a.get("layers") or [] for a in doc.get("assets") or []]
        for layers in scopes:
            for layer in layers:
                for node in _walk(layer.get("shapes") or []):
                    if isinstance(node, dict) and node.get("ty") in ("fl", "st"):
                        k = _flat_colour(node)
                        if not k:
                            continue
                        r, g, b = float(k[0]), float(k[1]), float(k[2])
                        mx, mn = max(r, g, b), min(r, g, b)
                        if mx < 0.2 or (mx - mn) / (mx or 1.0) <= 0.3:
                            continue
                        h, _l, _s = _cs.rgb_to_hls(r, g, b)
                        bins[int(h * 36) % 36] += 1.0
                        total += 1
    if total < 20:
        return None
    best = max(range(36), key=lambda i: bins[i])
    # Circular mean over the winning bin and its neighbours.
    import math as _math
    sx = sy = 0.0
    for i in ((best - 1) % 36, best, (best + 1) % 36):
        ang = _math.radians((i + 0.5) * 10.0)
        sx += bins[i] * _math.cos(ang)
        sy += bins[i] * _math.sin(ang)
    if sx == 0 and sy == 0:
        return (best + 0.5) * 10.0
    return _math.degrees(_math.atan2(sy, sx)) % 360.0


def process_tgs(data: bytes, brand: BrandProfile, hide_names: set, mode: str,
                item_index: int = 0, pack_common_signatures: set | None = None,
                logo_data: bytes | None = None, theme_hue_deg: float | None = None):
    """Returns (output_bytes, warnings). Timing and layer order untouched."""
    if mode not in MODES:
        raise ValueError(f"حالت پردازش نامعتبر است: {mode}")
    if mode == "v4":
        # Delegated to v4.py (import here to avoid a circular import:
        # v4 imports helpers from animation_engine, not pipeline).
        from .v4 import process_tgs_v4
        return process_tgs_v4(data, brand, hide_names, item_index, pack_common_signatures)
    if mode == "v5":
        from .v5 import process_tgs_v5
        out_bytes, warnings, _placement = process_tgs_v5(
            data, brand, hide_names, item_index, pack_common_signatures,
            theme_hue_deg=theme_hue_deg)
        return out_bytes, warnings
    if mode == "v8":
        # v8 for ANY user (v9): the v7 audit/deletion/theme engine +
        # contain-fit logo placement, with the user's own assets:
        # - logo image uploaded  -> traced to vector shapes
        #   (logo_vector) and placed with v7, then re-solved by v8's
        #   contain-fit inside the OLD logo's on-screen box (the
        #   owner's controlling scale rule). logo_size_mode "fill"
        #   skips the fit step and keeps v7's sizing instead.
        # - no logo image, name set -> the v5 text path (same rule:
        #   name at the exact old slot), so placeholder text can
        #   never appear: the traced-logo run only happens when a
        #   real logo asset exists, and if it placed nothing the
        #   run falls back to v5 as well.
        from dataclasses import replace as _replace

        from . import v8 as _v8
        from .logo_vector import trace_logo_image
        from .v5 import process_tgs_v5
        from .v7 import process_tgs_v7

        signatures = pack_common_signatures or set()
        logo_asset = trace_logo_image(logo_data, name="Brand Logo") if logo_data else None
        if logo_asset is not None:
            place_brand = _replace(brand, brand_name=(brand.brand_name or "LOGO"))
            out, warnings, _info = process_tgs_v7(
                data, place_brand, hide_names, item_index=item_index,
                pack_common_signatures=signatures, logo_asset=logo_asset)
            doc_out = load_tgs(out)
            _all = list(doc_out.get("layers") or []) + [
                layer for asset in doc_out.get("assets") or []
                for layer in (asset.get("layers") or [])]
            found = any(
                str(layer.get("nm", "")) == "Brand Logo" or any(
                    isinstance(g, dict) and str(g.get("nm", "")) == "Brand Logo"
                    for g in (layer.get("shapes") or []))
                for layer in _all)
            if not found:
                out, warnings, _info = process_tgs_v5(
                    data, brand, hide_names, item_index, signatures,
                    theme_hue_deg=theme_hue_deg)
                warnings.append("v8: اسلات لوگو پیدا نشد؛ مسیر متن (v5) اجرا شد.")
                return out, warnings
            if brand.logo_size_mode == "fit":
                rows = _v8.rescale_doc_to_fit(
                    load_tgs(data), doc_out, signatures, hide_names,
                    logo_asset=logo_asset)
                warnings.append(
                    f"v8: لوگوی کاربر با قاعده فیت (کامل داخل قاب لوگوی قبلی) "
                    f"در {len(rows)} المان جای گرفت.")
                return dump_tgs(doc_out), warnings
            warnings.append("v8: لوگوی کاربر با حالت fill (سایز v7) جای گرفت.")
            return out, warnings
        out, warnings, _info = process_tgs_v5(
            data, brand, hide_names, item_index, signatures,
            theme_hue_deg=theme_hue_deg)
        if logo_data:
            warnings.append("v8: لوگوی تصویری قابل برداری‌سازی نبود؛ مسیر متن (v5) اجرا شد.")
        else:
            warnings.append("v8: بدون لوگوی تصویری، مسیر متن دقیق (v5/v6) اجرا شد.")
        return out, warnings
    if mode == "preserve":
        return data, []
    doc = load_tgs(data)
    warnings = []
    candidates = tgs_logo_candidates(doc)
    # Capture the old logo's transform BEFORE deleting it, so the new
    # brand text can sit exactly in its place with its animation.
    logo_info = None
    if mode == "strip":
        import copy as _copy
        logo_layers = [layer for layer in doc.get("layers") or []
                       if str(layer.get("nm", "")) in set(candidates) | set(hide_names or [])]
        # Prefer a visible shape layer whose parent is NOT also a logo
        # layer (otherwise the text's parent would be deleted with it
        # and the text would dangle off-canvas — file 009's bug).
        logo_inds = {layer.get("ind") for layer in logo_layers}
        logo_layers.sort(key=lambda l: (0 if l.get("ty") == 4 else 1,
                                        0 if l.get("parent") not in logo_inds else 1))
        if logo_layers:
            layer = logo_layers[0]
            logo_info = {"ks": _copy.deepcopy(layer.get("ks") or {}),
                         "ip": layer.get("ip", 0), "op": layer.get("op"),
                         "parent": layer.get("parent"), "name": layer.get("nm")}
            # Width from shape vertices, if this is a shape layer.
            xs = []
            def _collect(n):
                if isinstance(n, dict):
                    if n.get("ty") == "sh":
                        k = (n.get("ks") or {}).get("k") or {}
                        v = k.get("v") if isinstance(k, dict) else None
                        if isinstance(v, list):
                            xs.extend(float(p[0]) for p in v if isinstance(p, (list, tuple)) and len(p) >= 1)
                    for val in n.values():
                        _collect(val)
                elif isinstance(n, list):
                    for val in n:
                        _collect(val)
            _collect(layer.get("shapes") or [])
            if xs:
                logo_info["width"] = max(xs) - min(xs)
        if logo_info is None:
            # Logo lives in an asset (possibly nested: top precomp ->
            # comp_0 -> comp_2 in the banner files). Search ALL assets
            # for a logo layer and place the text INSIDE that asset,
            # in the logo's own coordinate space — that is the only
            # way the text lands in the banner's place, not at the
            # bottom of the canvas. This is generic: it works for any
            # pack whose logo lives in a precomp, not just this one.
            for asset in doc.get("assets") or []:
                asset_layers = [al for al in asset.get("layers") or []
                                if str(al.get("nm", "")) in set(candidates) | set(hide_names or [])]
                asset_layers.sort(key=lambda l: (0 if l.get("ty") == 4 else 1,))
                if asset_layers:
                    layer = asset_layers[0]
                    logo_info = {"ks": _copy.deepcopy(layer.get("ks") or {}),
                                 "ip": layer.get("ip", 0), "op": layer.get("op"),
                                 "parent": layer.get("parent"), "name": layer.get("nm"),
                                 "scope": asset.get("id")}
                    xs = []
                    def _collect2(n):
                        if isinstance(n, dict):
                            if n.get("ty") == "sh":
                                k = (n.get("ks") or {}).get("k") or {}
                                v = k.get("v") if isinstance(k, dict) else None
                                if isinstance(v, list):
                                    xs.extend(float(p[0]) for p in v if isinstance(p, (list, tuple)) and len(p) >= 1)
                            for val in n.values():
                                _collect2(val)
                        elif isinstance(n, list):
                            for val in n:
                                _collect2(val)
                    _collect2(layer.get("shapes") or [])
                    if xs:
                        logo_info["width"] = max(xs) - min(xs)
                    break
    hidden = tgs_hide_layers(doc, hide_names) if hide_names else 0
    if hide_names and hidden == 0:
        warnings.append(f"لایه‌ای با نام‌های انتخاب‌شده پیدا نشد؛ نامزدها: {', '.join(candidates) or 'هیچ‌کدام'}.")
    if mode == "strip":
        auto_hidden = tgs_hide_layers(doc, set(candidates))
        text_hidden = tgs_hide_text_layers(doc)
        if auto_hidden:
            warnings.append(f"در حالت خام‌سازی، لایه‌های لوگو/متن به‌صورت خودکار پنهان شدند: {', '.join(candidates)}.")
        if text_hidden:
            warnings.append(f"لایه‌های متنی TGS به‌صورت خودکار پنهان شدند: {text_hidden} لایه.")
        # Watermark shape by geometry (not just by name) — this is what
        # removes the logo from files where it hides under scene names.
        # Generic pack-common detection first (works for ANY pack);
        # the pack-specific geometry check is kept as a fallback.
        if pack_common_signatures:
            pc_removed = tgs_remove_shapes_by_signatures(doc, pack_common_signatures)
            if pc_removed:
                warnings.append(f"شکل مشترک پک (تشخیص عمومی) از فایل حذف شد: {pc_removed} گروه.")
        wm_removed = tgs_remove_watermark_shapes(doc)
        if wm_removed:
            warnings.append(f"شکل واترمارک مشترک پک از فایل حذف شد: {wm_removed} گروه.")
        # Layer-by-layer edit: actually DELETE logo layers (not just hide),
        # except parented ones which are hidden to keep the animation intact.
        removed, hidden2 = tgs_remove_layers(doc, set(candidates) | set(hide_names or []))
        if removed:
            warnings.append(f"لایه‌های لوگو به‌طور کامل از فایل حذف شدند: {removed} لایه.")
        if hidden2:
            warnings.append(f"لایه‌های لوگوی وابسته (پدر لایه‌های دیگر) فقط پنهان شدند: {hidden2} لایه.")
        pruned = tgs_prune_unused_assets(doc)
        if pruned:
            warnings.append(f"بخش‌های داخلی بدون ارجاع حذف شدند: {pruned} بخش.")
        # Empty shape layers left by watermark removal are rejected by
        # Telegram as "wrong file type" — delete them, not just hide.
        emptied = tgs_prune_empty_shape_layers(doc)
        if emptied:
            warnings.append(f"لایه‌های خالی‌شده پس از حذف واترمارک حذف شدند: {emptied} لایه.")
        gradient_base = brand.primary_hex if brand.gradient_enabled else None
        if brand.full_recolor:
            changed = tgs_duotone(doc, brand.primary_hex, brand.secondary_hex, gradient_base)
            if changed == 0:
                warnings.append("هیچ رنگی در TGS برای خنثی‌سازی و بازسازی پیدا نشد.")
            else:
                warnings.append(f"رنگ‌های TGS کاملاً خنثی و با رنگ‌های شما بازسازی شدند ({changed} رنگ/درجه، شامل لایه‌های داخل Assets، گرادینت و Stroke).")
                if brand.gradient_enabled:
                    warnings.append(f"طیف رنگی: {', '.join(gradient_palette_hex(brand.primary_hex))}.")
        else:
            warnings.append("کاراکتر TGS حفظ شد: رنگ‌های اصلی تغییر نکردند؛ فقط لایه‌های لوگو/متن پنهان شدند.")
        # Brand name as vector shapes, with a font chosen automatically
        # for this emoji (varied by subject, not one font for all).
        # If an old logo was captured, the text goes exactly in its
        # place with its animation, not at the bottom.
        if brand.brand_name:
            try:
                style, font_path, desc = choose_font_for_tgs(doc, item_index)
                # Text must be readable, never tiny: minimum 54, and when
                # a logo width is known, scale to fill it (up to 110).
                # If the old logo had a static scale below 100%, divide
                # it out so the text is not shrunk by the logo's scale.
                size = 54.0
                if logo_info is not None and logo_info.get("width"):
                    try:
                        _, w36 = text_to_lottie_shapes(brand.brand_name, font_path, 36.0)
                        if w36 > 0:
                            size = max(54.0, min(110.0, 36.0 * float(logo_info["width"]) / w36))
                    except Exception:
                        pass
                if logo_info is not None:
                    ks = logo_info.get("ks") or {}
                    s = ks.get("s")
                    if isinstance(s, dict) and s.get("a") == 0 and isinstance(s.get("k"), list) and len(s["k"]) >= 2:
                        sx = float(s["k"][0]) / 100.0
                        if 0 < sx < 1.0:
                            size = min(110.0, size / sx)
                    layer_name = tgs_add_text_layer(doc, brand.brand_name, font_path, size=size,
                                                    logo_ks=logo_info.get("ks"),
                                                    logo_ip=logo_info.get("ip"),
                                                    logo_op=logo_info.get("op"),
                                                    logo_parent=logo_info.get("parent"),
                                                    scope=logo_info.get("scope"), logo_name=logo_info.get("name"))
                    warnings.append(f"نام برند «{brand.brand_name}» دقیقاً جای لوگوی قبلی نشست، با همان انیمیشن (فونت: {desc} — {style}).")
                else:
                    layer_name = tgs_add_text_layer(doc, brand.brand_name, font_path, size=size)
                    warnings.append(f"نام برند «{brand.brand_name}» به‌صورت لایه برداری به فایل اضافه شد (فونت: {desc} — {style}).")
            except Exception as exc:
                warnings.append(f"افزودن نام برند به TGS ناموفق بود: {str(exc)[:200]}")
        if tgs_has_keyframed_fills(doc):
            warnings.append("برخی رنگ‌های TGS متحرک (Keyframe) هستند و بازسازی نشدند.")
    if mode in ("redesign", "branding", "faithful"):
        strength = (brand.redesign_intensity / 100.0 * 0.4) if mode == "redesign" else (100 - brand.fidelity) / 100.0 * 0.5
        if strength > 0:
            changed = tgs_recolor(doc, brand.primary_hex, strength)
            if changed == 0:
                warnings.append("هیچ رنگ Fill در TGS برای بازرنگی پیدا نشد.")
        if tgs_has_keyframed_fills(doc):
            warnings.append("برخی رنگ‌های TGS متحرک (Keyframe) هستند و بازرنگی نشدند؛ فقط رنگ‌های ثابت تغییر کردند.")
    out = dump_tgs(doc)
    if len(out) > STICKER_LIMITS["animated"]["max_bytes"]:
        warnings.append(f"خروجی TGS {len(out)} بایت است و از حد ۶۴KB تلگرام بیشتر است.")
    return out, warnings


def process_project(data_dir: str, project_id: str, manifest: dict, brand: BrandProfile,
                    region: Region | None, mode: str, output_kind: str,
                    only_indices: list | None = None, logo_data: bytes | None = None,
                    hide_layer_names: set | None = None, progress_callback=None) -> dict:
    """Process manifest items whose local_path exists. Returns a report dict."""
    if mode not in MODES:
        raise ValueError(f"حالت پردازش نامعتبر است: {mode}")
    root = project_dir(data_dir, project_id)
    originals = os.path.join(root, "originals")
    cleaned_dir = os.path.join(root, "cleaned")
    final_dir = os.path.join(root, "final")
    os.makedirs(cleaned_dir, exist_ok=True)
    os.makedirs(final_dir, exist_ok=True)
    # Incremental cache: if brand/region/mode did not change, an already
    # produced final file is reused instead of reprocessed.
    from .storage import read_json
    fingerprint = {
        "brand": brand.to_dict(), "region": region.__dict__ if region else None,
        "mode": mode, "output_kind": output_kind,
        "hide_layer_names": sorted(hide_layer_names or []),
        # Engine revision: bump when placement/cleaning logic changes
        # so cached finals from an older engine are never reused.
        "engine_rev": 5,  # 5 = scene guard + mirror counter-flip
    }
    fingerprint_path = os.path.join(root, "fingerprint.json")
    old_fingerprint = read_json(fingerprint_path, default=None)
    fingerprint_changed = old_fingerprint != fingerprint
    write_json(fingerprint_path, fingerprint)
    # Pack-level logo band (strip mode, no user region): if the same
    # band is identical across the pack's static items, it is the
    # fixed logo — for ANY colour, ANY emoji, ANY pack.
    pack_region = None
    pack_region_note = ""
    if mode in ("strip", "v8") and region is None:
        try:
            from .cleaning import detect_common_logo_region
            from .image_engine import load_image as _load_img
            pack_images = []
            for _item in manifest.get("items", []):
                _local = _item.get("local_path", "")
                if _local and os.path.exists(_local):
                    try:
                        with open(_local, "rb") as _fh:
                            _info = describe(_fh.read())
                        if _info.kind == "static":
                            with open(_local, "rb") as _fh:
                                pack_images.append(_load_img(_fh.read()))
                    except Exception:
                        pass
                if len(pack_images) >= 10:
                    break
            pack_region = detect_common_logo_region(pack_images)
            if pack_region is not None:
                pack_region_note = (f"ناحیه مشترک پک (لوگوی ثابت در همه ایموجی‌ها) شناسایی شد: "
                                    f"({pack_region.x0},{pack_region.y0} تا {pack_region.x1},{pack_region.y1}).")
        except Exception:
            pack_region = None
    # Generic pack-common shape detection for TGS: shapes with identical
    # normalised geometry in many pack items are a watermark/logo, for
    # ANY pack — no hardcoded vertices. Computed once per pack run.
    common_sigs = set()
    _tgs_docs = []
    if mode in ("strip", "v4", "v5", "v8"):
        try:
            for _item in manifest.get("items", []):
                _local = _item.get("local_path", "")
                if _local and os.path.exists(_local):
                    try:
                        with open(_local, "rb") as _fh:
                            if describe(_fh.read()).kind == "animated":
                                _fh.seek(0)
                                _tgs_docs.append(load_tgs(_fh.read()))
                    except Exception:
                        pass
            common_sigs = pack_common_signatures(_tgs_docs)
        except Exception:
            common_sigs = set()
            _tgs_docs = []
    # Pack theme hue for the general selective recolour (v5/v8 paths):
    # detected once from the whole pack, never per file.
    theme_hue_deg = None
    if mode in ("v5", "v8") and _tgs_docs:
        try:
            theme_hue_deg = detect_pack_theme_hue(_tgs_docs)
        except Exception:
            theme_hue_deg = None
    # v9 generic audit: for the destructive modes on TGS packs, run
    # the pack-agnostic audit once and keep its report in the project
    # (audit.json). Only HIGH-confidence logo layer names are unioned
    # into the hide set — low-confidence packs are supposed to be
    # routed to Mati's review by the bot before they ever get here,
    # and uncertain layers are never silently deleted.
    if mode in ("strip", "v8") and _tgs_docs:
        try:
            from .audit import audit_pack
            _audit = audit_pack(_tgs_docs)
            write_json(os.path.join(root, "audit.json"), _audit)
            if _audit.get("confidence") == "high":
                hide_layer_names = set(hide_layer_names or []) | set(
                    _audit.get("logo_layer_names") or [])
        except Exception:
            pass
    results = []
    for item in manifest.get("items", []):
        idx = item["index"]
        if only_indices is not None and (idx - 1) not in only_indices:
            continue
        if not fingerprint_changed:
            cached = [f for f in os.listdir(final_dir)
                      if f.startswith(f"{project_id}_{idx:03d}_")]
            if cached:
                results.append({"index": idx, "output_name": sorted(cached)[0],
                                "status": "ok", "detail": "از خروجی قبلی استفاده شد (تنظیمات تغییر نکرده بود)."})
                continue
        local = item.get("local_path", "")
        if not local or not os.path.exists(local):
            results.append({"index": idx, "output_name": "", "status": "failed",
                            "detail": "فایل اصلی دانلود نشده است."})
            continue
        try:
            with open(local, "rb") as fh:
                data = fh.read()
            validate_upload(data, os.path.basename(local), 20 * 1024 * 1024)
            info = describe(data)
            stem = os.path.splitext(os.path.basename(local))[0]
            if info.kind == "static":
                eff_region = region if region is not None else pack_region
                # v8 is a TGS mode; raster items use the strip engine.
                eff_mode = "strip" if mode == "v8" else mode
                out_bytes, ext, warnings = process_static(data, brand, eff_region, eff_mode, output_kind, logo_data)
                out_name = ordered_name(project_id, idx, stem, ext)
                with open(os.path.join(final_dir, out_name), "wb") as fh:
                    fh.write(out_bytes)
                detail = f"ثابت {info.width}×{info.height} -> {len(out_bytes)} بایت. " + "; ".join(warnings)
                if pack_region_note:
                    detail += " " + pack_region_note
                results.append({"index": idx, "output_name": out_name, "status": "ok",
                                "detail": detail})
            elif info.kind == "animated":
                out_bytes, warnings = process_tgs(data, brand, set(hide_layer_names or []), mode,
                                                  item_index=idx - 1,
                                                  pack_common_signatures=common_sigs,
                                                  logo_data=logo_data,
                                                  theme_hue_deg=theme_hue_deg)
                # Importer guard (v10): every processed TGS passes
                # fix_importer_risks + re-validation BEFORE it is
                # written. A file the importer would still reject
                # fails loudly here — it is never written as a final.
                from .animation_engine import dump_tgs as _dump, load_tgs as _load
                from .importer_guard import fix_importer_risks
                from .validate_tgs import validate_tgs_bytes
                _doc, _grep = fix_importer_risks(_load(out_bytes))
                if _grep["tr_added"] or _grep["paint_stripped"] or _grep["vertices_removed"]:
                    out_bytes = _dump(_doc)
                    warnings.append(
                        "گارد ایمپورتر: "
                        f"tr افزوده‌شده {_grep['tr_added']}، رنگ گروهی حذف‌شده "
                        f"{_grep['paint_stripped']}، رأس ساده‌شده "
                        f"{_grep['vertices_removed']}.")
                _issues, _vwarnings = validate_tgs_bytes(out_bytes, idx)
                if _issues:
                    results.append({"index": idx, "output_name": "", "status": "failed",
                                    "detail": "اعتبارسنجی پس از گارد ایمپورتر رد کرد: "
                                              + "; ".join(_issues)[:400]})
                    if progress_callback:
                        try:
                            progress_callback(idx, len(manifest.get("items", [])))
                        except Exception:
                            pass
                    continue
                out_name = ordered_name(project_id, idx, stem, ".tgs")
                with open(os.path.join(final_dir, out_name), "wb") as fh:
                    fh.write(out_bytes)
                results.append({"index": idx, "output_name": out_name, "status": "ok",
                                "detail": f"TGS -> {len(out_bytes)} بایت. " + "; ".join(warnings)})
            elif info.kind == "video":
                out_name = ordered_name(project_id, idx, stem, ".webm")
                out_path = os.path.join(final_dir, out_name)

                def frame_fn(frame, _i, _brand=brand, _region=region, _mode=mode, _logo=logo_data):
                    img = frame
                    if _mode == "clean":
                        if _region is not None:
                            img = clean_region(img, _region)
                    elif _mode in ("strip", "v8"):
                        # v8 is a TGS mode; video frames use strip.
                        img, _w = _strip_rebuild(img, _brand, _region, _logo)
                    elif _mode == "redesign":
                        img = limited_redesign(img, _region, _brand.redesign_intensity)
                    elif _mode in ("branding", "faithful"):
                        if _region is not None:
                            img = clean_region(img, _region)
                        if _brand.fidelity < 100:
                            img = recolor_non_skin(img, _brand.primary_hex,
                                                   (100 - _brand.fidelity) / 100.0 * 0.5)
                        if _logo:
                            from .image_engine import load_image as _load
                            img = overlay_logo(img, _load(_logo), _brand.logo_position,
                                               _brand.logo_size_pct)
                        if _brand.watermark_text:
                            img, _w = overlay_watermark(img, _brand.watermark_text,
                                                        _brand.watermark_position,
                                                        _brand.watermark_opacity,
                                                        font_style=_brand.font_style)
                    return img

                report = process_webm(local, out_path, frame_fn, output_kind=output_kind)
                results.append({"index": idx, "output_name": out_name, "status": "ok",
                                "detail": f"WEBM {report['frames']} فریم، {report['fps']}fps، {report['output_bytes']} بایت، در حد 256K: {report['fits_256k']}."})
            else:
                results.append({"index": idx, "output_name": "", "status": "failed",
                                "detail": "فرمت فایل شناخته نشد."})
        except Exception as exc:
            results.append({"index": idx, "output_name": "", "status": "failed",
                            "detail": f"{type(exc).__name__}: {exc}"})
        if progress_callback:
            try:
                progress_callback(idx, len(manifest.get("items", [])))
            except Exception:
                pass
    report = build_report(project_id, manifest.get("set_name", ""), results)
    write_report_files(root, report)
    return report


def export_project(data_dir: str, project_id: str) -> dict:
    root = project_dir(data_dir, project_id)
    final_dir = os.path.join(root, "final")
    zip_path = os.path.join(root, f"{project_id}.zip")
    names = write_zip(final_dir, zip_path)
    return {"zip_path": zip_path, "files": names, "zip_bytes": os.path.getsize(zip_path)}
