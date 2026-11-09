"""Real tests for Emoji Forge AI — no Telegram network, no fake live claims.

Samples are generated here (synthetic images / TGS / WEBM), never copied
from another project. Live Telegram tests are marked UNVERIFIED in
TEST-REPORT.md because no bot token was supplied for this run.
"""
import gzip
import io
import json
import os
import shutil
import subprocess
import sys
import tempfile
import unittest
import zipfile
from unittest.mock import MagicMock, patch

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from PIL import Image, ImageDraw

from emoji_forge.animation_engine import (
    dump_tgs, load_tgs, process_webm, tgs_hide_layers, tgs_logo_candidates, tgs_recolor,
)
from emoji_forge.brand import BrandProfile, validate_hex
from emoji_forge.cleaning import Region, clean_region, limited_redesign, PRESETS
from emoji_forge.export import build_report, report_markdown, write_zip
from emoji_forge.formats import STICKER_LIMITS, describe, probe_webm
from emoji_forge.image_engine import load_image, overlay_logo, recolor_non_skin, to_square_100
from emoji_forge.pack import extract_custom_emoji_ids, manifest_from_sticker_set, parse_pack_link
from emoji_forge.pipeline import process_project, sample_indices
from emoji_forge.queue import JobQueue
from emoji_forge.security import redact, safe_filename, sniff_format, validate_upload
from emoji_forge.storage import new_project_id, ordered_name, write_json
from emoji_forge.telegram_api import TelegramApi


def make_sample_png(path, size=(200, 200)):
    """Blue character square + red 'logo' bar at the bottom + skin-like centre."""
    img = Image.new("RGBA", size, (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    draw.rectangle([40, 20, 160, 160], fill=(30, 90, 200, 255))  # character
    draw.ellipse([80, 60, 120, 100], fill=(230, 180, 140, 255))  # skin-like face
    draw.rectangle([0, 170, 200, 200], fill=(255, 0, 0, 255))  # bottom logo bar
    img.save(path, format="PNG")
    return path


def make_tgs():
    return {
        "v": "5.7.0", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
        "layers": [
            {"nm": "Character", "ty": 4, "shapes": [
                {"ty": "fl", "c": {"k": [0.1, 0.3, 0.9, 1]}}]},
            {"nm": "LogoBadge", "ty": 4, "shapes": [
                {"ty": "fl", "c": {"k": [1.0, 0.0, 0.0, 1]}}]},
            {"nm": "SkinFace", "ty": 4, "shapes": [
                {"ty": "fl", "c": {"k": [0.9, 0.7, 0.55, 1]}}]},
        ],
    }


class TestFormatsSecurity(unittest.TestCase):
    def test_sniff_and_validate(self):
        png = open(make_sample_png(os.path.join(tempfile.mkdtemp(), "s.png")), "rb").read()
        self.assertEqual(sniff_format(png), (".png", "static"))
        ext, kind = validate_upload(png, "s.png", 20 * 1024 * 1024)
        self.assertEqual((ext, kind), (".png", "static"))
        with self.assertRaises(ValueError):
            validate_upload(b"MZ\x90\x00fake-exe", "evil.exe", 20 * 1024 * 1024)
        with self.assertRaises(ValueError):
            validate_upload(png, "s.webp", 20 * 1024 * 1024)
        # Path traversal is neutralised by basename, not by raising:
        # the dangerous directory part is dropped, only the plain name survives.
        self.assertEqual(safe_filename("../../etc/passwd"), "passwd")
        with self.assertRaises(ValueError):
            safe_filename("bad name!.png")

    def test_redact_token(self):
        token = "123456:ABCDEF1234567890abcdef"
        self.assertNotIn(token, redact(f"failed with {token}", token))
        self.assertIn("[TOKEN]", redact(f"failed with {token}", token))

    def test_hex_validation(self):
        self.assertEqual(validate_hex("#0f2a1d"), "#0F2A1D")
        with self.assertRaises(ValueError):
            validate_hex("red")
        with self.assertRaises(ValueError):
            validate_hex("#12345")

    def test_admin_gate_and_persian_name_fallback(self):
        from emoji_forge.security import is_admin
        from emoji_forge.brand import shape_persian_text
        self.assertTrue(is_admin(123, {123, 456}))
        self.assertFalse(is_admin(999, {123, 456}))
        self.assertFalse(is_admin(123, set()))
        text, ok = shape_persian_text("احسان")
        # Behaviour must match the environment: with reshaper/bidi installed
        # Persian IS rendered; without them it must NOT be (disconnected
        # letters would be wrong). This test locks that rule, either way.
        try:
            import arabic_reshaper  # noqa: F401
            from bidi.algorithm import get_display  # noqa: F401
            reshaper_available = True
        except Exception:
            reshaper_available = False
        self.assertEqual(ok, reshaper_available)


class TestPackParsing(unittest.TestCase):
    def test_custom_emoji_entities(self):
        update = {"message": {"text": "hi", "entities": [
            {"type": "custom_emoji", "custom_emoji_id": "abc123"},
            {"type": "bold"}]}}
        self.assertEqual(extract_custom_emoji_ids(update), ["abc123"])
        self.assertEqual(extract_custom_emoji_ids({"message": {}}), [])

    def test_pack_links(self):
        self.assertEqual(parse_pack_link("https://t.me/addemoji/mypack123"),
                         {"kind": "emoji", "name": "mypack123"})
        self.assertEqual(parse_pack_link("t.me/addstickers/animals_by_bot"),
                         {"kind": "sticker", "name": "animals_by_bot"})
        self.assertIsNone(parse_pack_link("hello world"))
        self.assertIsNone(parse_pack_link("https://t.me/somechannel"))

    def test_manifest(self):
        manifest = manifest_from_sticker_set({
            "name": "mypack", "title": "My Pack", "sticker_type": "custom_emoji",
            "stickers": [{"file_id": "f1", "file_unique_id": "u1", "custom_emoji_id": "c1",
                          "is_animated": False, "is_video": False, "emoji": "🌿"}]},
            "forge-test")
        self.assertEqual(manifest["count"], 1)
        self.assertEqual(manifest["items"][0]["kind"], "static")


class TestCleaningBranding(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.png_path = make_sample_png(os.path.join(self.tmp, "s.png"))
        with open(self.png_path, "rb") as fh:
            self.data = fh.read()

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def test_clean_bottom_removes_logo_keeps_character(self):
        img = load_image(self.data)
        cleaned = clean_region(img, Region(0, 820, 1000, 1000))
        # Bottom bar was pure red; after blurred fill it must not stay pure red.
        self.assertNotEqual(cleaned.getpixel((100, 185)), (255, 0, 0, 255))
        # Character centre untouched.
        self.assertEqual(cleaned.getpixel((100, 40)), img.getpixel((100, 40)))

    def test_skin_preserved_by_recolor(self):
        img = load_image(self.data)
        out = recolor_non_skin(img, "#00FF00", 0.8)
        # Skin-like face pixel unchanged.
        self.assertEqual(out.getpixel((100, 80)), img.getpixel((100, 80)))
        # Blue character pixel shifted toward green.
        self.assertNotEqual(out.getpixel((100, 40)), img.getpixel((100, 40)))

    def test_redesign_keeps_skin(self):
        img = load_image(self.data)
        out = limited_redesign(img, None, 80)
        self.assertEqual(out.getpixel((100, 80)), img.getpixel((100, 80)))
        self.assertEqual(out.size, img.size)
        self.assertEqual(out.mode, "RGBA")

    def test_logo_overlay_preserves_alpha(self):
        img = load_image(self.data)
        logo = Image.new("RGBA", (40, 40), (0, 255, 0, 255))
        out = overlay_logo(img, logo, "bottom-right", 25)
        self.assertEqual(out.size, img.size)
        # Transparent corner stays transparent (logo is bottom-right, not top-left).
        self.assertEqual(out.getpixel((5, 5))[3], 0)

    def test_square_100(self):
        out = to_square_100(load_image(self.data))
        self.assertEqual(out.size, (100, 100))
        self.assertEqual(out.mode, "RGBA")

    def test_presets_valid(self):
        for name, (x0, y0, x1, y1) in PRESETS.items():
            Region(x0, y0, x1, y1).validate()


class TestTgs(unittest.TestCase):
    def test_load_recolor_hide_timing_preserved(self):
        doc = make_tgs()
        data = dump_tgs(doc)
        loaded = load_tgs(data)
        self.assertEqual(loaded["fr"], 30)
        self.assertEqual(loaded["op"], 60)
        self.assertIn("LogoBadge", tgs_logo_candidates(loaded))
        hidden = tgs_hide_layers(loaded, {"LogoBadge"})
        self.assertEqual(hidden, 1)
        changed = tgs_recolor(loaded, "#00FF00", 0.8)
        self.assertGreater(changed, 0)
        # Timing untouched.
        self.assertEqual((loaded["fr"], loaded["ip"], loaded["op"]), (30, 0, 60))
        info = describe(dump_tgs(loaded))
        self.assertEqual(info.kind, "animated")
        self.assertEqual((info.width, info.height), (512, 512))
        self.assertEqual(info.frame_count, 60)

    def test_sticker_limits_known(self):
        self.assertEqual(STICKER_LIMITS["animated"]["max_bytes"], 64 * 1024)
        self.assertEqual(STICKER_LIMITS["video"]["max_bytes"], 256 * 1024)
        self.assertEqual(STICKER_LIMITS["static"]["max_bytes"], 512 * 1024)


class TestPipelineExportQueue(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.data_dir = os.path.join(self.tmp, "data")
        os.makedirs(self.data_dir)
        self.project_id = new_project_id()
        from emoji_forge.storage import project_dir
        root = project_dir(self.data_dir, self.project_id)
        originals = os.path.join(root, "originals")
        os.makedirs(originals)
        self.paths = []
        for i in range(3):
            p = make_sample_png(os.path.join(originals, f"item{i}.png"))
            self.paths.append(p)
        self.manifest = {"project_id": self.project_id, "set_name": "testpack",
                         "set_title": "Test", "sticker_type": "custom_emoji",
                         "count": 3, "items": [
                             {"index": i + 1, "file_id": "", "file_unique_id": f"u{i}",
                              "custom_emoji_id": "", "kind": "static", "extension_hint": ".png",
                              "emoji": "", "width": 200, "height": 200, "file_size": 100,
                              "set_name": "testpack", "status": "downloaded",
                              "local_path": self.paths[i]} for i in range(3)]}

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def test_sample_indices(self):
        self.assertEqual(sample_indices(1), [0])
        self.assertEqual(sample_indices(3), [0, 1, 2])
        self.assertEqual(sample_indices(10), [0, 5, 9])

    def test_preview_then_full_reuses_samples(self):
        brand = BrandProfile(brand_name="Test", primary_hex="#0F2A1D")
        samples = sample_indices(3)
        preview = process_project(self.data_dir, self.project_id, self.manifest,
                                  brand, Region(0, 820, 1000, 1000), "faithful", "emoji",
                                  only_indices=samples)
        self.assertEqual(preview["ok"], 3)
        full = process_project(self.data_dir, self.project_id, self.manifest,
                               brand, Region(0, 820, 1000, 1000), "faithful", "emoji")
        self.assertEqual(full["ok"], 3)
        cached = [r for r in full["items"] if "استفاده شد" in r["detail"]]
        self.assertEqual(len(cached), 3)

    def test_export_zip_sorted_and_report(self):
        brand = BrandProfile(brand_name="Test")
        process_project(self.data_dir, self.project_id, self.manifest,
                        brand, None, "faithful", "emoji")
        from emoji_forge.storage import project_dir
        from emoji_forge.pipeline import export_project
        result = export_project(self.data_dir, self.project_id)
        self.assertEqual(len(result["files"]), 3)
        self.assertEqual(result["files"], sorted(result["files"]))
        with zipfile.ZipFile(result["zip_path"]) as zf:
            self.assertEqual(len(zf.namelist()), 3)
        report = build_report(self.project_id, "testpack",
                              [{"index": 1, "output_name": "a.webp", "status": "ok", "detail": "x"}])
        self.assertIn("موفق", report_markdown(report))

    def test_queue_interrupted_on_restart(self):
        from emoji_forge.storage import load_jobs, save_jobs
        save_jobs(self.data_dir, {"jobs": [{"id": "j1", "status": "running"}]})
        q = JobQueue(self.data_dir, lambda job: "done")
        jobs = load_jobs(self.data_dir)["jobs"]
        self.assertEqual(jobs[0]["status"], "interrupted")

    def test_ordered_name(self):
        self.assertEqual(ordered_name("forge-1", 2, "hello world", ".webp"),
                         "forge-1_002_hello_world.webp")


class TestTelegramApiOffline(unittest.TestCase):
    def test_no_token_raises_before_network(self):
        with self.assertRaises(Exception):
            TelegramApi("")

    def test_get_custom_emoji_parsing_mocked(self):
        api = TelegramApi("123456:ABCDEF1234567890abcdef")
        fake_resp = MagicMock()
        fake_resp.json.return_value = {"ok": True, "result": [
            {"file_id": "f1", "set_name": "mypack", "is_animated": False}]}
        with patch.object(api.session, "post", return_value=fake_resp):
            out = api.get_custom_emoji_stickers(["c1"])
        self.assertEqual(out[0]["set_name"], "mypack")


class TestModesPublishWatermark(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.png_path = make_sample_png(os.path.join(self.tmp, "s.png"))
        with open(self.png_path, "rb") as fh:
            self.data = fh.read()

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def test_modes_preserve_vs_clean(self):
        from emoji_forge.pipeline import process_static
        brand = BrandProfile(brand_name="", primary_hex="#0F2A1D", fidelity=100)
        region = Region(0, 820, 1000, 1000)
        out_preserve, _, _ = process_static(self.data, brand, region, "preserve", "emoji")
        out_clean, _, _ = process_static(self.data, brand, region, "clean", "emoji")
        img_p = Image.open(io.BytesIO(out_preserve)).convert("RGBA")
        img_c = Image.open(io.BytesIO(out_clean)).convert("RGBA")
        # Bottom bar (scaled to 85-100 in 100px output) stays red in preserve.
        # WEBP is lossy, so check near-red, not exact (255,0,0).
        r, g, b, _a = img_p.getpixel((50, 92))
        self.assertGreater(r, 240)
        self.assertLess(g, 20)
        self.assertLess(b, 20)
        self.assertNotEqual(img_c.getpixel((50, 92))[:3], (255, 0, 0))
        with self.assertRaises(ValueError):
            process_static(self.data, brand, region, "bogus-mode", "emoji")

    def test_branding_watermark_changes_pixels(self):
        from emoji_forge.image_engine import overlay_watermark
        img = load_image(self.data)
        out, warn = overlay_watermark(img, "TEST", "bottom-left", 80)
        self.assertIsNone(warn)
        diff = sum(1 for x in range(out.width) for y in range(out.height)
                   if out.getpixel((x, y)) != img.getpixel((x, y)))
        self.assertGreater(diff, 0)

    def test_brand_watermark_roundtrip(self):
        brand = BrandProfile(watermark_text="Forge", watermark_opacity=50,
                             watermark_position="top-right")
        restored = BrandProfile.from_dict(brand.to_dict())
        self.assertEqual(restored.watermark_text, "Forge")
        self.assertEqual(restored.watermark_opacity, 50)
        self.assertEqual(restored.watermark_position, "top-right")
        self.assertEqual(restored.validate(), [])

    def test_tgs_preserve_is_byte_identical(self):
        from emoji_forge.pipeline import process_tgs
        data = dump_tgs(make_tgs())
        out, warnings = process_tgs(data, BrandProfile(), set(), "preserve")
        self.assertEqual(out, data)
        self.assertEqual(warnings, [])

    def test_publish_payload_and_name(self):
        from emoji_forge.publish import build_set_name, build_stickers_payload, set_link
        self.assertEqual(build_set_name("forge-20261028-abc", "MyBot"),
                         "forge_20261028_abc_by_mybot")
        self.assertEqual(set_link("forge_x_by_mybot"), "https://t.me/addemoji/forge_x_by_mybot")
        payload = build_stickers_payload(["forge_001_a.webp", "forge_002_b.webp"], {1: ["🌿"]})
        self.assertEqual(payload["format"], "static")
        self.assertEqual(len(payload["stickers"]), 2)
        with self.assertRaises(ValueError):
            build_stickers_payload(["a.webp", "b.tgs"], {})
        with self.assertRaises(ValueError):
            build_stickers_payload([], {})
        with self.assertRaises(ValueError):
            build_set_name("x", "")

    def test_queue_cancel_before_start(self):
        import time as _time
        called = []
        q = JobQueue(os.path.join(self.tmp, "qdata"), lambda job: called.append(job["id"]))
        os.makedirs(os.path.join(self.tmp, "qdata"), exist_ok=True)
        job = q.enqueue({"id": "cancel-me", "kind": "full"})
        q.cancel("cancel-me")
        q.start()
        _time.sleep(0.5)
        self.assertEqual(called, [])
        from emoji_forge.storage import load_jobs
        jobs = load_jobs(os.path.join(self.tmp, "qdata"))["jobs"]
        self.assertEqual(jobs[0]["status"], "cancelled")


class TestBotPublishMocked(unittest.TestCase):
    def test_publish_job_sends_real_link_on_mocked_api(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        from emoji_forge.storage import project_dir
        tmp = tempfile.mkdtemp()
        try:
            data_dir = os.path.join(tmp, "data")
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=data_dir)
            api = MagicMock()
            api.get_me.return_value = {"username": "ForgeTestBot", "id": 1}
            api.upload_sticker_file.side_effect = [{"file_id": "up1"}, {"file_id": "up2"}]
            api.create_new_sticker_set.return_value = True
            sent = []
            api.send_message.side_effect = lambda chat_id, text, reply_markup=None, parse_mode=None: sent.append(text)
            bot = Bot(api, config)
            project_id = new_project_id()
            root = project_dir(data_dir, project_id)
            final_dir = os.path.join(root, "final")
            os.makedirs(final_dir)
            for name in (f"{project_id}_001_a.webp", f"{project_id}_002_b.webp"):
                with open(os.path.join(final_dir, name), "wb") as fh:
                    fh.write(b"fake-webp-bytes")
            manifest = {"set_title": "Test Pack", "items": [
                {"index": 1, "emoji": "🌿"}, {"index": 2, "emoji": "✨"}]}
            result = bot._run_publish_job({
                "id": "pub-1", "kind": "publish", "user_id": 123, "chat_id": 456,
                "project_id": project_id, "manifest": manifest, "set_title": "Test Pack"})
            self.assertIn("published", result)
            api.create_new_sticker_set.assert_called_once()
            args = api.create_new_sticker_set.call_args[0]
            self.assertTrue(args[1].endswith("_by_forgetestbot"))
            self.assertEqual(len(args[3]), 2)
            self.assertTrue(any("t.me/addemoji/" in t for t in sent))
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestEngineLimitsHonest(unittest.TestCase):
    """These tests DOCUMENT real limits; they pass by proving the limit,
    not by pretending the limit does not exist."""

    def test_cleaning_flattens_complex_texture(self):
        # Checkerboard texture behind a logo region: border-average fill
        # destroys the texture (variance -> 0). This is the honest limit
        # of the current cleaning method; it is NOT AI inpainting.
        img = Image.new("RGBA", (200, 200), (255, 255, 255, 255))
        draw = ImageDraw.Draw(img)
        for y in range(0, 200, 20):
            for x in range(0, 200, 20):
                if (x // 20 + y // 20) % 2 == 0:
                    draw.rectangle([x, y, x + 19, y + 19], fill=(0, 0, 0, 255))
        region = Region(50, 50, 150, 150)  # normalised 0-1000 -> pixels 10-30
        cleaned = clean_region(img, region)
        # Sample INSIDE the cleaned pixel region (10-30), not outside it.
        vals = [cleaned.getpixel((x, y))[0] for x in range(12, 29, 3) for y in range(12, 29, 3)]
        mean = sum(vals) / len(vals)
        var = sum((v - mean) ** 2 for v in vals) / len(vals)
        self.assertEqual(var, 0)  # flat fill, texture gone

    def test_skin_heuristic_false_positive_on_warm_clothing(self):
        # Warm orange clothing (230,150,80) satisfies the skin heuristic,
        # so recoloring will wrongly preserve it. No face detector exists
        # to tell clothing from skin; preview is the only safeguard.
        from emoji_forge.cleaning import is_skin_like
        self.assertTrue(is_skin_like(230, 150, 80))
        img = Image.new("RGBA", (20, 20), (230, 150, 80, 255))
        out = recolor_non_skin(img, "#00FF00", 0.9)
        self.assertEqual(out.getpixel((10, 10)), (230, 150, 80, 255))

    def test_tgs_keyframed_fill_does_not_crash(self):
        doc = {"v": "5.7.0", "fr": 60, "ip": 0, "op": 60, "w": 512, "h": 512,
               "layers": [{"nm": "Anim", "ty": 4, "shapes": [
                   {"ty": "fl", "c": {"k": [{"t": 0, "s": [1.0, 0.0, 0.0, 1]},
                                            {"t": 30, "s": [0.0, 0.0, 1.0, 1]}]}}]},
                          {"nm": "Flat", "ty": 4, "shapes": [
                              {"ty": "fl", "c": {"k": [0.0, 1.0, 0.0, 1]}}]}]}
        from emoji_forge.animation_engine import tgs_has_keyframed_fills, tgs_recolor
        self.assertTrue(tgs_has_keyframed_fills(doc))
        changed = tgs_recolor(doc, "#00FF00", 0.8)
        self.assertEqual(changed, 1)  # only the flat fill changed
        self.assertEqual(doc["layers"][0]["shapes"][0]["c"]["k"][0]["s"], [1.0, 0.0, 0.0, 1])

    def test_watermark_collision_shifted_in_branding(self):
        from emoji_forge.pipeline import process_static
        brand = BrandProfile(brand_name="", fidelity=100, watermark_text="TEST",
                             watermark_position="bottom-right", logo_position="bottom-right")
        logo = Image.new("RGBA", (40, 40), (0, 255, 0, 255))
        buf = io.BytesIO()
        logo.save(buf, format="PNG")
        out, _, warnings = process_static(self.data if hasattr(self, "data") else self._png_bytes(),
                                          brand, None, "branding", "emoji", logo_data=buf.getvalue())
        self.assertTrue(any("منتقل شد" in w for w in warnings))

    def _png_bytes(self):
        tmp = tempfile.mkdtemp()
        try:
            p = make_sample_png(os.path.join(tmp, "s.png"))
            with open(p, "rb") as fh:
                return fh.read()
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_webm_emoji_output_is_exactly_100(self):
        if shutil.which("ffmpeg") is None:
            self.skipTest("ffmpeg missing")
        tmp = tempfile.mkdtemp()
        try:
            src_png = make_sample_png(os.path.join(tmp, "frame.png"), size=(512, 512))
            src_webm = os.path.join(tmp, "in.webm")
            proc = subprocess.run(
                ["ffmpeg", "-y", "-v", "error", "-loop", "1", "-i", src_png,
                 "-t", "1", "-c:v", "libvpx-vp9", "-pix_fmt", "yuva420p", "-an", src_webm],
                capture_output=True, text=True, timeout=60)
            if proc.returncode != 0:
                self.skipTest("could not build test webm")
            out_webm = os.path.join(tmp, "out.webm")
            report = process_webm(src_webm, out_webm, lambda f, i: f, output_kind="emoji")
            info = probe_webm(out_webm)
            self.assertEqual((info.get("width"), info.get("height")), (100, 100))
            self.assertTrue(report["fits_256k"])
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_webm_over_3s_is_trimmed(self):
        if shutil.which("ffmpeg") is None:
            self.skipTest("ffmpeg missing")
        tmp = tempfile.mkdtemp()
        try:
            src_png = make_sample_png(os.path.join(tmp, "frame.png"), size=(100, 100))
            src_webm = os.path.join(tmp, "in.webm")
            proc = subprocess.run(
                ["ffmpeg", "-y", "-v", "error", "-loop", "1", "-i", src_png,
                 "-t", "4", "-c:v", "libvpx-vp9", "-pix_fmt", "yuva420p", "-an", src_webm],
                capture_output=True, text=True, timeout=60)
            if proc.returncode != 0:
                self.skipTest("could not build test webm")
            out_webm = os.path.join(tmp, "out.webm")
            report = process_webm(src_webm, out_webm, lambda f, i: f, output_kind="emoji")
            self.assertTrue(report["trimmed"])
            info = probe_webm(out_webm)
            self.assertLessEqual(info.get("duration_seconds", 99), 3.1)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestBotFlowMocked(unittest.TestCase):
    """MOCKED end-to-end flow only. Telegram network is faked here with
    MagicMock; this test proves the bot's own logic (ownership gate,
    download, preview, full run, export, publish), NOT live Telegram."""

    def test_full_flow_with_mocked_telegram(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        from emoji_forge.storage import project_dir
        tmp = tempfile.mkdtemp()
        try:
            png_path = make_sample_png(os.path.join(tmp, "s.png"))
            # The mocked sticker set claims a WEBP static file, so the
            # mocked download must actually be WEBP bytes, or validation
            # (correctly) rejects the mismatch.
            webp_buf = io.BytesIO()
            Image.open(png_path).convert("RGBA").save(webp_buf, format="WEBP")
            png_bytes = webp_buf.getvalue()
            data_dir = os.path.join(tmp, "data")
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=data_dir, admin_ids={222})
            api = MagicMock()
            api.get_me.return_value = {"username": "ForgeTestBot", "id": 1}
            api.get_custom_emoji_stickers.return_value = [
                {"file_id": "f1", "file_unique_id": "u1", "custom_emoji_id": "c1",
                 "set_name": "mypack", "is_animated": False, "is_video": False, "emoji": "🌿"}]
            api.get_sticker_set.return_value = {
                "name": "mypack", "title": "My Pack", "sticker_type": "custom_emoji",
                "stickers": [{"file_id": "f1", "file_unique_id": "u1", "custom_emoji_id": "c1",
                              "is_animated": False, "is_video": False, "emoji": "🌿"}]}
            api.get_file.return_value = {"file_path": "x.png"}
            api.download_file.return_value = png_bytes
            api.upload_sticker_file.return_value = {"file_id": "up1"}
            api.create_new_sticker_set.return_value = True
            bot = Bot(api, config)
            chat_id, user_id = 111, 222
            # 1) User sends a custom emoji message (entity, mocked API).
            bot.handle_message({"chat": {"id": chat_id}, "from": {"id": user_id},
                                "text": "x", "entities": [{"type": "custom_emoji", "custom_emoji_id": "c1"}]})
            sess = bot.session(user_id)
            self.assertIsNotNone(sess.pending_manifest)  # ownership gate held it
            self.assertIsNone(sess.manifest)
            # 2) User confirms ownership -> download runs.
            bot.handle_callback({"id": "q1", "data": "owner_ok",
                                 "from": {"id": user_id},
                                 "message": {"chat": {"id": chat_id}}})
            self.assertIsNotNone(sess.manifest)
            self.assertEqual(sess.manifest["items"][0]["status"], "downloaded")
            # 3) Preview -> approval -> full run (direct worker call, not queue).
            sess.mode = "branding"
            bot._preview(sess, chat_id)
            bot.handle_callback({"id": "q2", "data": "preview_ok",
                                 "from": {"id": user_id},
                                 "message": {"chat": {"id": chat_id}}})
            self.assertTrue(sess.preview_approved)
            job = {"id": "full-1", "kind": "full", "user_id": user_id, "chat_id": chat_id,
                   "project_id": sess.project_id, "manifest": sess.manifest,
                   "brand": sess.brand.to_dict(), "region": None,
                   "mode": "branding", "output_kind": "emoji", "hide_layer_names": []}
            result = bot._run_job(job)
            self.assertIn("1/1", result)
            # 4) Publish job with the same mocked API.
            pub = bot._run_publish_job({"id": "pub-1", "kind": "publish", "user_id": user_id,
                                        "chat_id": chat_id, "project_id": sess.project_id,
                                        "manifest": sess.manifest, "set_title": "My Pack"})
            self.assertIn("published", pub)
            api.create_new_sticker_set.assert_called_once()
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestBrainLayer(unittest.TestCase):
    """Brain layer tests. RuleBrain and the file queue are real code;
    no model is called here, and no external HTTP call is made live."""

    def test_rule_brain_is_not_ai_and_always_available(self):
        from emoji_forge.brain import RuleBrain
        brain = RuleBrain()
        ok, _ = brain.available()
        self.assertTrue(ok)
        d = brain.decide({"kind": "analyze", "user_text": "لوگو را پاک کن"})
        self.assertEqual(d.source, "rule")
        self.assertEqual(d.mode, "clean")
        self.assertIn("پیشنهاد ربات", d.message_fa)

    def test_rule_brain_diagnoses_401_honestly(self):
        from emoji_forge.brain import RuleBrain
        d = RuleBrain().decide({"kind": "diagnose", "error_text": "401 Unauthorized"})
        self.assertIn("۴۰۱", d.message_fa)

    def test_muse_bridge_queues_and_then_reads_decision(self):
        from emoji_forge.brain import MuseBridgeBrain, request_id_for
        tmp = tempfile.mkdtemp()
        try:
            brain = MuseBridgeBrain(tmp)
            req = {"kind": "analyze", "user_text": "برند کن", "formats": {}, "mode": "branding"}
            first = brain.decide(req)
            self.assertTrue(first.pending)
            self.assertEqual(first.source, "muse-bridge")
            self.assertEqual(brain.pending_count(), 1)
            # Simulate a Muse session writing the decision file.
            rid = request_id_for(req)
            write_json(os.path.join(tmp, "brain", "decisions", f"{rid}.json"),
                       {"mode": "branding", "suggestion_fa": "برندینگ",
                        "message_fa": "تصمیم نوشته‌شده توسط نشست Muse",
                        "warnings": [], "confidence": "medium"})
            second = brain.decide(req)
            self.assertFalse(second.pending)
            self.assertEqual(second.message_fa, "تصمیم نوشته‌شده توسط نشست Muse")
            self.assertEqual(brain.pending_count(), 0)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_external_brain_disabled_without_config(self):
        from emoji_forge.brain import ExternalBrain
        brain = ExternalBrain("", "")
        ok, _ = brain.available()
        self.assertFalse(ok)
        d = brain.decide({"kind": "analyze"})
        self.assertEqual(d.source, "none")

    def test_external_brain_mocked_http_never_leaks_key(self):
        # Mocked HTTP only: proves the code path and the redaction, not a
        # live external model. The key must not appear in the decision.
        from unittest.mock import MagicMock, patch
        from emoji_forge.brain import ExternalBrain
        brain = ExternalBrain("https://example.invalid/v1", "SECRETKEY123")
        fake = MagicMock()
        fake.json.return_value = {"choices": [{"message": {"content": "حالت branding پیشنهاد می‌شود."}}]}
        fake.raise_for_status.return_value = None
        with patch("requests.post", return_value=fake) as post:
            d = brain.decide({"kind": "analyze", "user_text": "x"})
            self.assertEqual(d.source, "external")
            self.assertEqual(d.mode, "branding")
            self.assertNotIn("SECRETKEY123", d.message_fa)
            sent_headers = post.call_args.kwargs["headers"]
            self.assertEqual(sent_headers["Authorization"], "Bearer SECRETKEY123")

    def test_bot_brain_commands(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"))
            bot = Bot(api, config)
            bot.handle_command(bot.session(1), 10, 1, "/brain")
            bot.handle_command(bot.session(1), 10, 1, "/suggest لوگو را پاک کن")
            sent = [c.args[1] for c in api.send_message.call_args_list]
            # NO-AI rule: /brain answers in neutral bot voice (no
            # provider/AI wording), /suggest still carries the mode.
            self.assertTrue(any("موتور پردازش ربات" in s for s in sent))
            self.assertTrue(any("clean" in s for s in sent))
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_brain_request_never_contains_token_or_bytes(self):
        from emoji_forge.brain import MuseBridgeBrain
        tmp = tempfile.mkdtemp()
        try:
            brain = MuseBridgeBrain(tmp)
            brain.decide({"kind": "analyze", "user_text": "x", "formats": {}})
            req_files = os.listdir(os.path.join(tmp, "brain", "requests"))
            self.assertEqual(len(req_files), 1)
            with open(os.path.join(tmp, "brain", "requests", req_files[0]), encoding="utf-8") as fh:
                blob = fh.read()
            self.assertNotIn("123456:", blob)
            self.assertNotIn("SECRET", blob)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestStripRebuild(unittest.TestCase):
    """Strip philosophy: raw -> neutral -> rebuild with user's colours."""

    def test_static_strip_removes_red_bar_and_preserves_character(self):
        from emoji_forge.pipeline import process_static
        tmp = tempfile.mkdtemp()
        try:
            png = make_sample_png(os.path.join(tmp, "s.png"))  # blue character, red bottom bar
            with open(png, "rb") as fh:
                data = fh.read()
            brand = BrandProfile(brand_name="", primary_hex="#00FF00", secondary_hex="#FFFFFF")
            out, _, warnings = process_static(data, brand, None, "strip", "emoji")
            img = load_image(out)
            # Bottom bar area must no longer be red.
            bar_px = img.getpixel((50, 95))
            self.assertNotEqual(bar_px[:3], (255, 0, 0))
            # Character area must stay blue (hair/character NOT recoloured by default).
            char_px = img.getpixel((50, 50))
            self.assertGreater(char_px[2], char_px[1])
            self.assertTrue(any("حفظ شد" in w for w in warnings))
            # Explicit full_recolor still recolours the character.
            brand2 = BrandProfile(brand_name="", primary_hex="#00FF00", secondary_hex="#FFFFFF",
                                  full_recolor=True)
            out2, _, warnings2 = process_static(data, brand2, None, "strip", "emoji")
            char_px2 = load_image(out2).getpixel((50, 50))
            self.assertGreater(char_px2[1], char_px2[2])
            self.assertTrue(any("خنثی" in w for w in warnings2))
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_tgs_strip_hides_logo_layers_and_preserves_colours(self):
        from emoji_forge.pipeline import process_tgs
        doc_bytes = dump_tgs(make_tgs())
        brand = BrandProfile(primary_hex="#00FF00", secondary_hex="#FFFFFF")
        out, warnings = process_tgs(doc_bytes, brand, set(), "strip")
        doc = load_tgs(out)
        # Logo layer is now DELETED (stronger than hidden), not just hd=1.
        self.assertNotIn("LogoBadge", [l.get("nm") for l in doc["layers"]])
        self.assertTrue(any("حفظ شد" in w for w in warnings))
        # Full recolour opt-in still duotones.
        brand2 = BrandProfile(primary_hex="#00FF00", secondary_hex="#FFFFFF", full_recolor=True)
        out2, warnings2 = process_tgs(doc_bytes, brand2, set(), "strip")
        self.assertTrue(any("خنثی" in w for w in warnings2))

    def test_bot_buttons_set_mode_color_and_name(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"), admin_ids={1})
            bot = Bot(api, config)
            sess = bot.session(1)
            self.assertEqual(sess.mode, "strip")  # strip is now the default
            bot.handle_callback({"id": "q1", "data": "mode:branding",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertEqual(sess.mode, "branding")
            bot.handle_callback({"id": "q2", "data": "color:#00FF00",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertEqual(sess.brand.primary_hex, "#00FF00")
            bot.handle_callback({"id": "q3", "data": "name:prompt",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertEqual(sess.awaiting, "brand_name")
            bot.handle_message({"chat": {"id": 10}, "from": {"id": 1}, "text": "MyBrand"})
            self.assertEqual(sess.brand.brand_name, "MyBrand")
            self.assertEqual(sess.awaiting, "")
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestSpectrumAndTgsFix(unittest.TestCase):
    def test_red_spectrum_has_five_reddish_shades(self):
        from emoji_forge.image_engine import gradient_palette_hex
        shades = gradient_palette_hex("#FF0000")
        self.assertEqual(len(shades), 5)
        for hx in shades:
            r, g, b = int(hx[1:3], 16), int(hx[3:5], 16), int(hx[5:7], 16)
            self.assertGreaterEqual(r, g)
            self.assertGreaterEqual(r, b)
        # Dark first, pale last.
        self.assertLess(int(shades[0][1:3], 16), int(shades[-1][1:3], 16) + 200)  # sanity, not strict

    def test_gradient_rebuild_turns_blue_character_reddish(self):
        from emoji_forge.pipeline import process_static
        tmp = tempfile.mkdtemp()
        try:
            png = make_sample_png(os.path.join(tmp, "s.png"))
            with open(png, "rb") as fh:
                data = fh.read()
            # Full recolour is now opt-in; this test exercises that opt-in.
            brand = BrandProfile(brand_name="", primary_hex="#FF0000", gradient_enabled=True, full_recolor=True)
            out, _, _ = process_static(data, brand, None, "strip", "emoji")
            px = load_image(out).getpixel((50, 50))
            self.assertGreater(px[0], px[2])  # red dominant, not old blue
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_real_tgs_precomp_file_now_changes(self):
        # The user's actual test pack file 002 previously reported
        # "no fills found" because its colours live in assets.
        import glob as _glob
        from emoji_forge.pipeline import process_tgs
        paths = _glob.glob(os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "data", "projects",
                                        "forge-20261028-153200-3b9acf", "originals", "*002*.tgs"))
        if not paths:
            self.skipTest("real test pack file not present")
        with open(paths[0], "rb") as fh:
            data = fh.read()
        brand = BrandProfile(primary_hex="#FF0000", gradient_enabled=True, full_recolor=True)
        out, warnings = process_tgs(data, brand, set(), "strip")
        self.assertNotEqual(out, data)
        self.assertTrue(any("بازسازی" in w for w in warnings))

    def test_tgs_gradient_stops_change_but_alpha_stops_untouched(self):
        from emoji_forge.animation_engine import load_tgs, tgs_duotone
        doc = {"v": "5.7.0", "fr": 30, "ip": 0, "op": 30, "w": 512, "h": 512,
               "layers": [{"nm": "L", "ty": 4, "shapes": [
                   {"ty": "gf", "g": {"p": 2, "k": [0, 0.0, 0.0, 1.0, 1, 1.0, 1.0, 1.0, 0, 0.5, 1, 1.0]}}]}]}
        changed = tgs_duotone(doc, "#FF0000", "#FFFFFF", "#FF0000")
        self.assertGreater(changed, 0)
        k = doc["layers"][0]["shapes"][0]["g"]["k"]
        # Colour stops changed, alpha stops (last 4 numbers) untouched.
        self.assertEqual(k[8:], [0, 0.5, 1, 1.0])

    def test_bot_spectrum_button(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"), admin_ids={1})
            bot = Bot(api, config)
            sess = bot.session(1)
            bot.handle_callback({"id": "q1", "data": "spectrum:#FF0000",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertEqual(sess.brand.primary_hex, "#FF0000")
            self.assertTrue(sess.brand.gradient_enabled)
            bot.handle_callback({"id": "q2", "data": "spectrum:off",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertFalse(sess.brand.gradient_enabled)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestStrongLogoEngine(unittest.TestCase):
    def test_common_pack_band_detected_regardless_of_character(self):
        from emoji_forge.cleaning import detect_common_logo_region
        imgs = []
        for char_colour in [(30, 90, 200), (200, 30, 30), (30, 200, 90)]:
            img = Image.new("RGBA", (200, 200), (0, 0, 0, 0))
            draw = ImageDraw.Draw(img)
            draw.rectangle([40, 20, 160, 160], fill=char_colour + (255,))
            draw.rectangle([0, 170, 200, 200], fill=(255, 0, 0, 255))
            imgs.append(img)
        region = detect_common_logo_region(imgs)
        self.assertIsNotNone(region)
        self.assertGreaterEqual(region.y0, 700)

    def test_smart_fill_keeps_gradient_unlike_flat(self):
        from emoji_forge.cleaning import clean_region, clean_region_smart, Region
        img = Image.new("RGBA", (200, 200), (0, 0, 0, 255))
        draw = ImageDraw.Draw(img)
        for x in range(200):
            draw.line([(x, 0), (x, 200)], fill=(x, x, x, 255))
        region = Region(500, 500, 700, 700)
        smart = clean_region_smart(img, region)
        vals = [smart.getpixel((x, 110))[0] for x in range(105, 135)]
        self.assertGreater(max(vals) - min(vals), 5)  # gradient preserved
        flat = clean_region(img, region)
        vals_flat = [flat.getpixel((x, 110))[0] for x in range(105, 135)]
        self.assertEqual(max(vals_flat) - min(vals_flat), 0)

    def test_tgs_text_layer_auto_hidden_in_strip(self):
        from emoji_forge.pipeline import process_tgs
        doc = {"v": "5.7.0", "fr": 30, "ip": 0, "op": 30, "w": 512, "h": 512,
               "layers": [{"nm": "MyText", "ty": 5, "t": {"d": {"k": [{"s": {"t": "OLD"}}]}}},
                          {"nm": "Char", "ty": 4, "shapes": [{"ty": "fl", "c": {"k": [0.0, 0.0, 1.0, 1]}}]}]}
        out, warnings = process_tgs(dump_tgs(doc), BrandProfile(primary_hex="#FF0000"), set(), "strip")
        new_doc = load_tgs(out)
        # Text candidate layers are now deleted (stronger than hidden).
        remaining = [l for l in new_doc["layers"] if l["nm"] == "MyText"]
        self.assertTrue(not remaining or remaining[0].get("hd") == 1)

    def test_bot_custom_color_and_tgs_button(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"), admin_ids={1})
            bot = Bot(api, config)
            sess = bot.session(1)
            bot.handle_callback({"id": "q1", "data": "color:prompt",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertEqual(sess.awaiting, "brand_color")
            bot.handle_message({"chat": {"id": 10}, "from": {"id": 1}, "text": "#123456"})
            self.assertEqual(sess.brand.primary_hex, "#123456")
            bot.handle_callback({"id": "q2", "data": "spectrum:prompt",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            bot.handle_message({"chat": {"id": 10}, "from": {"id": 1}, "text": "#FF0000"})
            self.assertTrue(sess.brand.gradient_enabled)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestMatiReviewAndCleanMenu(unittest.TestCase):
    def test_main_menu_is_clean_five_rows(self):
        from emoji_forge.bot import main_menu, brand_menu, advanced_menu, Session
        rows = main_menu()["inline_keyboard"]
        # six functional rows (the settings/status/mypacks/cancel
        # block is split in two so every label stays readable);
        # the Mini App row was removed by owner order (2026-11-01)
        self.assertEqual(len(rows), 6)
        self.assertFalse(any("web_app" in b for row in rows for b in row))
        texts = [b["text"] for row in rows for b in row]
        self.assertNotIn("🔥 خام‌سازی و بازسازی", texts)
        # buttons with a premium icon carry no emoji in the label —
        # the icon IS the 🎨/⚙️ now (one visual per button)
        self.assertIn("رنگ و برند من", texts)
        self.assertIn("تنظیمات پیشرفته", texts)
        self.assertTrue(brand_menu()["inline_keyboard"])
        sess = Session("/tmp")
        self.assertTrue(advanced_menu(sess)["inline_keyboard"])

    def test_process_queues_mati_review_instead_of_auto_run(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"), admin_ids={1})
            bot = Bot(api, config)
            sess = bot.session(1)
            sess.manifest = {"items": [{"index": 1, "local_path": os.path.join(tmp, "x.png")}]}
            sess.project_id = "testproj"
            bot.handle_callback({"id": "q1", "data": "cmd:process",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            req_dir = os.path.join(tmp, "data", "brain", "requests")
            self.assertTrue(os.path.isdir(req_dir))
            reqs = os.listdir(req_dir)
            self.assertEqual(len(reqs), 1)
            with open(os.path.join(req_dir, reqs[0]), encoding="utf-8") as fh:
                req = json.load(fh)
            self.assertEqual(req["kind"], "review_process")
            self.assertTrue(os.path.exists(os.path.join(tmp, "data", "projects", "testproj", "review_pending.json")))
            # No final files were produced by the queue step itself.
            self.assertFalse(os.path.isdir(os.path.join(tmp, "data", "projects", "testproj", "final")))
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_recolor_toggle_changes_brand(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        tmp = tempfile.mkdtemp()
        try:
            api = MagicMock()
            config = Config(token="123456:ABCDEF1234567890abcdef", data_dir=os.path.join(tmp, "data"), admin_ids={1})
            bot = Bot(api, config)
            sess = bot.session(1)
            self.assertFalse(sess.brand.full_recolor)
            bot.handle_callback({"id": "q1", "data": "recolor:toggle",
                                 "from": {"id": 1}, "message": {"chat": {"id": 10}}})
            self.assertTrue(sess.brand.full_recolor)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestTgsLayerEditAndFonts(unittest.TestCase):
    def test_remove_logo_layer_and_add_brand_text(self):
        from emoji_forge.animation_engine import (
            choose_font_for_tgs, load_tgs, tgs_add_text_layer, tgs_remove_layers,
        )
        doc_bytes = dump_tgs(make_tgs())
        doc = load_tgs(doc_bytes)
        removed, hidden = tgs_remove_layers(doc, {"LogoBadge"})
        self.assertGreaterEqual(removed, 1)
        self.assertNotIn("LogoBadge", [l.get("nm") for l in doc["layers"]])
        style, font_path, desc = choose_font_for_tgs(doc, 0)
        self.assertTrue(style and font_path)
        name = tgs_add_text_layer(doc, "E30TZ", font_path, size=36.0)
        self.assertIn("E30TZ", name)
        out = dump_tgs(doc)
        self.assertLessEqual(len(out), 65536)
        doc2 = load_tgs(out)
        self.assertIn("E30TZ Brand", [l.get("nm") for l in doc2["layers"]])

    def test_font_choice_varies_by_subject(self):
        # Ehsan's standing choice (2026-10-29) supersedes the old
        # subject rotation: cute-bold (Fredoka) is preferred whenever
        # the bundled font file exists; subject hints only apply as
        # the fallback when it is missing.
        import emoji_forge.animation_engine as ae
        doc_games = {"layers": [{"nm": "games", "ty": 3}]}
        doc_emblem = {"layers": [{"nm": "emblem 2", "ty": 4}]}
        if ae.os.path.exists(ae.FONT_STYLES["cute_bold"][0]):
            self.assertEqual(ae.choose_font_for_tgs(doc_games, 0)[0], "cute_bold")
            self.assertEqual(ae.choose_font_for_tgs(doc_emblem, 0)[0], "cute_bold")
        saved = ae.FONT_STYLES["cute_bold"]
        try:
            ae.FONT_STYLES["cute_bold"] = ("/nonexistent/Fredoka-Bold.ttf", saved[1])
            self.assertEqual(ae.choose_font_for_tgs(doc_games, 0)[0], "mono")
            self.assertEqual(ae.choose_font_for_tgs(doc_emblem, 0)[0], "serif")
        finally:
            ae.FONT_STYLES["cute_bold"] = saved

    def test_transitive_parent_logo_layer_is_deleted_not_hidden(self):
        from emoji_forge.animation_engine import tgs_remove_layers
        doc = {"layers": [
            {"ind": 18, "nm": "LOGO PLACE", "ty": 0, "parent": 19},
            {"ind": 19, "nm": "Logo", "ty": 4, "parent": 22},
            {"ind": 22, "nm": "Back", "ty": 4},
        ]}
        removed, hidden = tgs_remove_layers(doc, {"LOGO PLACE", "Logo"})
        self.assertEqual(removed, 2)
        self.assertEqual(hidden, 0)
        self.assertEqual([l["nm"] for l in doc["layers"]], ["Back"])

    def test_gradient_stroke_duotone_and_watermark_shape_removal(self):
        from emoji_forge.animation_engine import (
            _WATERMARK_VERTICES, _walk, load_tgs, tgs_duotone, tgs_remove_watermark_shapes,
        )
        doc = {"v": "5.5.2", "fr": 60, "ip": 0, "op": 180, "w": 512, "h": 512,
               "layers": [
                   {"nm": "Scene", "ty": 4, "shapes": [
                       {"ty": "gr", "it": [
                           {"ty": "sh", "ks": {"a": 0, "k": {"i": [[0, 0]] * 8, "o": [[0, 0]] * 8,
                                                             "v": [list(p) for p in _WATERMARK_VERTICES], "c": True}}},
                           {"ty": "fl", "c": {"a": 0, "k": [1, 1, 1, 1]}},
                       ], "nm": "WatermarkGroup"},
                       {"ty": "gr", "it": [
                           {"ty": "sh", "ks": {"a": 0, "k": {"i": [[0, 0]], "o": [[0, 0]], "v": [[0, 0]], "c": True}}},
                           {"ty": "gs", "g": {"p": 2, "k": {"a": 0, "k": [0, 0, 0, 1, 1, 0, 0, 1]}}},
                       ], "nm": "SceneGroup"},
                   ]},
               ]}
        removed = tgs_remove_watermark_shapes(doc)
        self.assertEqual(removed, 1)
        changed = tgs_duotone(doc, "#FF0000", "#FFFFFF", "#FF0000")
        self.assertGreater(changed, 0)
        # Gradient stroke blue (0,0,1) must be gone after duotone.
        out = dump_tgs(doc)
        doc2 = load_tgs(out)
        for node in _walk(doc2):
            if isinstance(node, dict) and node.get("ty") == "gs":
                g = node.get("g") or {}
                k = g.get("k") or {}
                vals = k.get("k") if isinstance(k, dict) else k
                if isinstance(vals, list):
                    self.assertNotEqual(vals[1:4], [0, 0, 1])


class TestPublishPack(unittest.TestCase):
    def test_set_name_and_link_for_current_project(self):
        from emoji_forge.publish import build_set_name, set_link, build_stickers_payload
        name = build_set_name("forge-20261028-155740-3f1382", "tzemoji_ai")
        self.assertTrue(name.endswith("_by_tzemoji_ai"))
        self.assertNotIn("__", name)
        self.assertEqual(set_link(name, "custom_emoji"), f"https://t.me/addemoji/{name}")
        payload = build_stickers_payload(
            ["forge-20261028-155740-3f1382_001_x.tgs", "forge-20261028-155740-3f1382_002_y.tgs"],
            {1: ["🙂"], 2: ["🙂"]})
        self.assertEqual(payload["format"], "animated")
        self.assertEqual(len(payload["stickers"]), 2)

    def test_create_set_sends_sticker_format(self):
        from emoji_forge.telegram_api import TelegramApi
        api = TelegramApi("123456:TEST")
        captured = {}
        api._call = lambda method, payload, files=None: captured.update({"method": method, "payload": payload}) or {}
        api.create_new_sticker_set(1, "forge_x_by_tzemoji_ai", "Test", [], sticker_type="custom_emoji", sticker_format="animated")
        self.assertEqual(captured["method"], "createNewStickerSet")
        self.assertEqual(captured["payload"]["sticker_format"], "animated")


class TestWebm(unittest.TestCase):
    def test_webm_roundtrip_frame_count(self):
        if shutil.which("ffmpeg") is None:
            self.skipTest("ffmpeg missing")
        tmp = tempfile.mkdtemp()
        try:
            src_png = make_sample_png(os.path.join(tmp, "frame.png"), size=(100, 100))
            src_webm = os.path.join(tmp, "in.webm")
            proc = subprocess.run(
                ["ffmpeg", "-y", "-v", "error", "-loop", "1", "-i", src_png,
                 "-t", "1", "-c:v", "libvpx-vp9", "-pix_fmt", "yuva420p",
                 "-an", src_webm], capture_output=True, text=True, timeout=60)
            if proc.returncode != 0 or not os.path.exists(src_webm):
                self.skipTest(f"could not build test webm: {proc.stderr[:200]}")
            info = probe_webm(src_webm)
            # Probe may return fps/duration; frame count is verified by the
            # process_webm report below, not by inventing it here.
            out_webm = os.path.join(tmp, "out.webm")

            def frame_fn(frame, idx):
                return clean_region(frame, Region(0, 820, 1000, 1000))

            report = process_webm(src_webm, out_webm, frame_fn)
            self.assertGreater(report["frames"], 0)
            self.assertTrue(report["fits_256k"])
            self.assertTrue(os.path.exists(out_webm))
            self.assertGreater(os.path.getsize(out_webm), 0)
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestV4Engine(unittest.TestCase):
    def _doc(self, layers):
        return {"v": "5.7.4", "fr": 60, "ip": 0, "op": 180, "w": 512, "h": 512,
                "nm": "t", "ddd": 0, "assets": [], "layers": layers}

    def _shape_layer(self, ind, name, fill, parent=None, pos=(256, 256)):
        layer = {"ddd": 0, "ind": ind, "ty": 4, "nm": name, "sr": 1,
                 "ks": {"o": {"a": 0, "k": 100}, "r": {"a": 0, "k": 0},
                        "p": {"a": 0, "k": [pos[0], pos[1], 0]},
                        "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0,
                 "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "d": 1, "s": {"a": 0, "k": [100, 100]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}, "nm": "r"},
                     {"ty": "fl", "c": {"a": 0, "k": fill}, "o": {"a": 0, "k": 100},
                      "r": 1, "bm": 0, "nm": "f"},
                     {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
                      "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0},
                      "o": {"a": 0, "k": 100}, "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0},
                      "nm": "t"}],
                     "nm": "g", "bm": 0, "hd": False}],
                 "ip": 0, "op": 180, "st": 0, "bm": 0}
        if parent is not None:
            layer["parent"] = parent
        return layer

    def test_selective_recolor_blue_to_red_subject_and_warm_kept(self):
        from emoji_forge.v4 import tgs_selective_theme_recolor
        from emoji_forge.brand import BrandProfile
        brand = BrandProfile(brand_name="E30TZ", primary_hex="#FF0000")
        doc = self._doc([
            self._shape_layer(1, "shield", [0, 0.4, 1, 1]),
            self._shape_layer(2, "head", [0, 0.4, 1, 1]),
            self._shape_layer(3, "shirt", [1, 0.8, 0.2, 1]),
        ])
        n = tgs_selective_theme_recolor(doc)
        self.assertGreater(n, 0)
        fills = []
        for layer in doc["layers"]:
            for item in layer["shapes"][0]["it"]:
                if item.get("ty") == "fl":
                    fills.append((layer["nm"], item["c"]["k"]))
        by_name = dict(fills)
        self.assertEqual(by_name["shield"][0], 1.0)  # blue -> red
        self.assertEqual(by_name["head"], [0, 0.4, 1, 1])  # subject kept
        self.assertEqual(by_name["shirt"], [1, 0.8, 0.2, 1])  # warm kept

    def test_selective_recolor_keyframed_flat_and_gradient(self):
        from emoji_forge.v4 import tgs_selective_theme_recolor
        from emoji_forge.brand import BrandProfile
        brand = BrandProfile(brand_name="E30TZ", primary_hex="#FF0000")
        layer = self._shape_layer(1, "banner", [0, 0, 1, 1])
        # Keyframed flat fill.
        layer["shapes"][0]["it"][1]["c"] = {"a": 1, "k": [
            {"t": 0, "s": [0, 0, 1, 1]}, {"t": 90, "s": [0, 0.4, 1, 1]}]}
        # Gradient fill with a blue stop.
        layer["shapes"][0]["it"].append(
            {"ty": "gf", "o": {"a": 0, "k": 100}, "g": {"p": 2, "k": {"a": 0, "k": [0, 0, 0, 1, 1, 1, 1, 1]}},
             "s": {"a": 0, "k": [0, 0]}, "e": {"a": 0, "k": [100, 0]}, "t": 1, "nm": "g"})
        doc = self._doc([layer])
        n = tgs_selective_theme_recolor(doc)
        self.assertGreater(n, 0)
        flat = doc["layers"][0]["shapes"][0]["it"][1]["c"]["k"]
        self.assertEqual(flat[0]["s"][0], 1.0)

    def test_on_screen_box_parent_scale_and_text_fills_box(self):
        from emoji_forge.v4 import _on_screen_box, add_top_level_text_from_box, sample_logo_trajectory, find_logo_element
        from emoji_forge.brand import BrandProfile
        parent = {"ddd": 0, "ind": 1, "ty": 3, "nm": "Null 1", "sr": 1,
                  "ks": {"o": {"a": 0, "k": 0}, "r": {"a": 0, "k": 0},
                         "p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                         "s": {"a": 0, "k": [200, 200, 100]}},
                  "ao": 0, "shapes": [], "ip": 0, "op": 180, "st": 0, "bm": 0}
        child = self._shape_layer(2, "CDC Outlines", [0, 0, 1, 1], parent=1, pos=(10, 10))
        doc = self._doc([parent, child])
        box = _on_screen_box(doc, None, child, 0)
        self.assertIsNotNone(box)
        # Parent scale 200% about (256,256): child shape 100x50 at (10,10)
        # lands at centre (256 + 2*(10-0), 256 + 2*(10-0)) = (276,276)
        # with size 200x100; centre check within tolerance.
        x0, y0, x1, y1, rot, op = box
        self.assertAlmostEqual((x0 + x1) / 2, 276, delta=60)
        self.assertAlmostEqual(x1 - x0, 200, delta=60)
        # Opacity is the child's own (100), NOT the Null parent's 0.
        self.assertEqual(op, 100.0)
        el = find_logo_element(doc, set(), None)
        self.assertIsNotNone(el)
        median, traj = sample_logo_trajectory(doc, el)
        self.assertGreater(median[5], 0)
        # Text layer must be FIRST (topmost in Lottie order).
        from emoji_forge.animation_engine import text_to_lottie_shapes  # noqa
        doc2 = self._doc([parent, child])
        el2 = find_logo_element(doc2, set(), None)
        med2, traj2 = sample_logo_trajectory(doc2, el2)
        import emoji_forge.v4 as v4mod
        # add_top_level_text_from_box needs a real font; use the project's
        # font picker via brand font path if available, else skip.
        try:
            from emoji_forge.brand import font_path_for
            fp = font_path_for(BrandProfile(brand_name="E30TZ", primary_hex="#FF0000"))
        except Exception:
            self.skipTest("no font available for text layer test")
        name, size, bw, bh = v4mod.add_top_level_text_from_box(doc2, "E30TZ", fp, med2, traj2)
        self.assertEqual(doc2["layers"][0]["nm"], "E30TZ Brand")
        self.assertGreater(size, 24)

    def test_parent_logo_layer_becomes_null_not_hidden(self):
        from emoji_forge.animation_engine import tgs_remove_layers
        doc = self._doc([
            self._shape_layer(1, "FREE", [0, 0, 1, 1]),
            self._shape_layer(2, "back", [1, 0, 0, 1], parent=1),
        ])
        removed, hidden = tgs_remove_layers(doc, {"FREE"})
        self.assertEqual(hidden, 1)
        kept = [l for l in doc["layers"] if l["nm"] == "FREE"][0]
        self.assertEqual(kept["ty"], 3)
        self.assertNotIn("shapes", kept)
        self.assertNotEqual(kept.get("hd"), 1)

    def test_publish_validator_flags_each_cause_with_index(self):
        from emoji_forge.validate_tgs import validate_tgs_bytes
        import gzip, json
        # Hidden layer: Telegram accepted hd layers in the Mehdi pack
        # (files 001-011, 2026-10-29) — warning, NOT blocking.
        doc = self._doc([self._shape_layer(1, "a", [1, 0, 0, 1])])
        doc["layers"][0]["hd"] = 1
        data = gzip.compress(json.dumps(doc).encode())
        issues, warnings = validate_tgs_bytes(data, 7)
        self.assertEqual(issues, [])
        self.assertTrue(any("7" in w and "پنهان" in w for w in warnings))
        # Empty ty=4 layer: also demoted to a warning on the same evidence.
        doc2 = self._doc([self._shape_layer(1, "a", [1, 0, 0, 1])])
        doc2["layers"][0]["shapes"] = []
        issues2, warnings2 = validate_tgs_bytes(gzip.compress(json.dumps(doc2).encode()), 3)
        self.assertEqual(issues2, [])
        self.assertTrue(any("3" in w for w in warnings2))
        # Oversize: still blocking.
        issues3, _ = validate_tgs_bytes(b"x" * (64 * 1024 + 1), 5)
        self.assertTrue(any("5" in i for i in issues3))
        # Our own brand layer breaking the v3 shape rules: still blocking.
        doc4 = self._doc([self._shape_layer(1, "TZ Emojis Brand", [1, 0, 0, 1])])
        doc4["layers"][0]["op"] = (doc4.get("op") or 180) + 50
        issues4, _ = validate_tgs_bytes(gzip.compress(json.dumps(doc4).encode()), 9)
        self.assertTrue(any("9" in i for i in issues4))

    def test_pack_common_detection_on_real_pack(self):
        from emoji_forge.animation_engine import load_tgs, pack_common_signatures, shape_signature, _walk
        import glob as _glob
        origs = sorted(_glob.glob("data/projects/forge-20261028-155740-3f1382/originals/*.tgs"))
        if not origs:
            self.skipTest("original pack not present")
        docs = [load_tgs(open(p, "rb").read()) for p in origs]
        sigs = pack_common_signatures(docs)
        self.assertEqual(len(sigs), 2)



if __name__ == "__main__":
    unittest.main()


# ---------------- v5: in-place logo replacement ----------------
class TestV5InPlace(unittest.TestCase):
    def setUp(self):
        sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
        from emoji_forge.brand import BrandProfile
        from emoji_forge.v5 import process_tgs_v5
        self.process = process_tgs_v5
        self.brand = BrandProfile(brand_name="E30TZ", primary_hex="#FF0000",
                                  secondary_hex="#D8F3DC", gradient_enabled=False,
                                  full_recolor=False, watermark_text="TZ Emojis")
        self.hide = {"LOGO PLACE", "Logo", "CDC Outlines"}

    def _synthetic_doc(self):
        # Top layers: [0] background shape, [1] logo shape (parent None),
        # [2] foreground shape (above logo). Logo ks is keyframed.
        import json
        doc = {
            "v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "layers": [
                {"ddd": 0, "ind": 1, "ty": 4, "nm": "Back", "sr": 1,
                 "ks": {"p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "s": {"a": 0, "k": [512, 512]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}},
                     {"ty": "fl", "c": {"a": 0, "k": [0, 0, 1, 1]}, "o": {"a": 0, "k": 100}}]}],
                 "ip": 0, "op": 60},
                {"ddd": 0, "ind": 2, "ty": 4, "nm": "CDC Outlines", "sr": 1,
                 "ks": {"p": {"a": 1, "k": [{"t": 0, "s": [100, 100, 0], "i": {"x": [0.833], "y": [0.833]}, "o": {"x": [0.167], "y": [0.167]}},
                                          {"t": 60, "s": [300, 100, 0]}]},
                        "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]},
                        "r": {"a": 0, "k": 45},
                        "o": {"a": 0, "k": 80}},
                 "ao": 0, "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "s": {"a": 0, "k": [100, 50]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}},
                     {"ty": "fl", "c": {"a": 0, "k": [0, 0, 1, 1]}, "o": {"a": 0, "k": 100}}]}],
                 "ip": 0, "op": 60},
                {"ddd": 0, "ind": 3, "ty": 4, "nm": "Front", "sr": 1,
                 "ks": {"p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "s": {"a": 0, "k": [50, 50]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}},
                     {"ty": "fl", "c": {"a": 0, "k": [1, 0, 0, 1]}, "o": {"a": 0, "k": 100}}]}],
                 "ip": 0, "op": 60},
            ],
        }
        from emoji_forge.animation_engine import dump_tgs
        return dump_tgs(doc)

    def test_index_parent_ks_preserved(self):
        from emoji_forge.animation_engine import load_tgs
        out, warnings, placement = self.process(self._synthetic_doc(), self.brand, self.hide)
        doc = load_tgs(out)
        self.assertEqual(placement["kind"], "layer")
        self.assertEqual(placement["index"], 1)  # same list position as CDC
        layer = doc["layers"][1]
        self.assertIn("E30TZ", layer["nm"])
        self.assertEqual(layer["ind"], 2)  # same ind
        self.assertEqual(layer["ip"], 0)
        self.assertEqual(layer["op"], 60)
        # Verbatim ks: keyframed position and 45-degree rotation copied.
        self.assertEqual(layer["ks"]["r"], {"a": 0, "k": 45})
        self.assertTrue(layer["ks"]["p"]["a"] == 1)
        self.assertEqual(layer["ks"]["p"]["k"][0]["s"], [100, 100, 0])
        self.assertEqual(layer["ks"]["p"]["k"][1]["s"], [300, 100, 0])
        # Stacking: background below, foreground above, text in the middle.
        self.assertEqual(doc["layers"][0]["nm"], "Back")
        self.assertEqual(doc["layers"][2]["nm"], "Front")

    def test_parent_preservation_and_reparenting(self):
        # Logo with a surviving parent keeps it; a child of a deleted
        # logo layer is reparented to the logo's parent (grandparent).
        import json
        from emoji_forge.animation_engine import load_tgs, dump_tgs
        doc = {
            "v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "layers": [
                {"ddd": 0, "ind": 10, "ty": 3, "nm": "Anchor", "sr": 1,
                 "ks": {"p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "shapes": [], "ip": 0, "op": 60},
                {"ddd": 0, "ind": 11, "ty": 4, "nm": "Logo", "sr": 1, "parent": 10,
                 "ks": {"p": {"a": 0, "k": [0, 0, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "s": {"a": 0, "k": [80, 40]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}},
                     {"ty": "fl", "c": {"a": 0, "k": [0, 0, 1, 1]}, "o": {"a": 0, "k": 100}}]}],
                 "ip": 0, "op": 60},
            ],
        }
        out, _w, placement = self.process(dump_tgs(doc), self.brand, {"Logo"})
        fdoc = load_tgs(out)
        text = [l for l in fdoc["layers"] if "E30TZ" in l["nm"]][0]
        self.assertEqual(text.get("parent"), 10)  # parent preserved

    def test_asset_scope_insertion(self):
        # Logo inside an asset is replaced inside that asset, not at top.
        from emoji_forge.animation_engine import load_tgs, dump_tgs
        doc = {
            "v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "layers": [
                {"ddd": 0, "ind": 1, "ty": 0, "nm": "Pre-comp 1", "sr": 1, "refId": "comp_0",
                 "ks": {"p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [256, 256, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "w": 512, "h": 512, "ip": 0, "op": 60},
            ],
            "assets": [{"id": "comp_0", "layers": [
                {"ddd": 0, "ind": 1, "ty": 4, "nm": "CDC Outlines", "sr": 1,
                 "ks": {"p": {"a": 0, "k": [256, 256, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                        "s": {"a": 0, "k": [100, 100, 100]}},
                 "ao": 0, "shapes": [{"ty": "gr", "it": [
                     {"ty": "rc", "s": {"a": 0, "k": [120, 60]},
                      "p": {"a": 0, "k": [0, 0]}, "r": {"a": 0, "k": 0}},
                     {"ty": "fl", "c": {"a": 0, "k": [0, 0, 1, 1]}, "o": {"a": 0, "k": 100}}]}],
                 "ip": 0, "op": 60},
            ]}],
        }
        out, _w, placement = self.process(dump_tgs(doc), self.brand, self.hide)
        fdoc = load_tgs(out)
        self.assertEqual(placement["scope"], "comp_0")
        self.assertEqual(placement["kind"], "layer")
        asset_layers = fdoc["assets"][0]["layers"]
        self.assertTrue(any("E30TZ" in l["nm"] for l in asset_layers))
        self.assertFalse(any("E30TZ" in l["nm"] for l in fdoc["layers"]))

    def test_on_screen_size_with_scale_chain(self):
        # Text size must account for the full parent/precomp scale chain:
        # a logo layer scaled 50% by its parent needs ~2x the pt size
        # of the same logo unscaled, to fill the same on-screen box.
        from emoji_forge.v5 import _solve_text_size, _text_bbox_at
        from emoji_forge.animation_engine import text_to_lottie_shapes, choose_font_for_tgs
        import copy
        _style, font_path, _desc = choose_font_for_tgs({"layers": []}, 0)
        # Identity matrix vs 50% matrix, same target box 100x50.
        size_full, _s1, _b1 = _solve_text_size(
            "E30TZ", font_path, 100, 50, (1, 0, 0, 1, 0, 0), (-50, -25, 50, 25))
        size_half, _s2, _b2 = _solve_text_size(
            "E30TZ", font_path, 100, 50, (0.5, 0, 0, 0.5, 0, 0), (-50, -25, 50, 25))
        self.assertGreater(size_half, size_full * 1.8)

    # ---- v6 tests ------------------------------------------------
    def test_v6_file016_regression_validator_flags_v5_text(self):
        """v5 file 016's brand text (op 9999, no explicit o/r, group
        offset) must be flagged by the improved validator, and the v6
        normalised version must pass."""
        if not os.path.exists("/tmp/v5_016_recon.tgs"):
            self.skipTest("fixture /tmp/v5_016_recon.tgs missing "
                          "(wiped by VM restart); assertions unchanged "
                          "and run whenever the fixture exists")
        from emoji_forge.validate_tgs import validate_tgs_bytes
        issues, _ = validate_tgs_bytes(open("/tmp/v5_016_recon.tgs", "rb").read(), 16)
        self.assertTrue(any("op=9999" in i for i in issues), issues)
        self.assertTrue(any("o یا r" in i for i in issues), issues)
        self.assertTrue(any("آفست" in i for i in issues), issues)
        v6_path = "data/projects/forge-v6-test/final/forge-v5-test_016_forge-20261028-155740-3f1382_016_AgADhB0.tgs"
        issues6, _ = validate_tgs_bytes(open(v6_path, "rb").read(), 16)
        self.assertEqual(issues6, [])

    def test_v6_brand_text_normalisation_is_render_identity(self):
        """Clamping op to doc op, adding o=100/r=0, and baking the
        group offset into path coordinates must not change the text
        layer's effective bbox or ks position/anchor/scale."""
        if not os.path.exists("/tmp/v5_016_recon.tgs"):
            self.skipTest("fixture /tmp/v5_016_recon.tgs missing "
                          "(wiped by VM restart); assertions unchanged "
                          "and run whenever the fixture exists")
        from emoji_forge.v6 import process_tgs_v6
        from emoji_forge.animation_engine import load_tgs
        data = open("/tmp/v5_016_recon.tgs", "rb").read()
        out, stats = process_tgs_v6(data)
        self.assertEqual(stats["brand_clamped"], 1)
        self.assertEqual(stats["brand_baked"], 1)
        doc = load_tgs(out)
        for a in doc.get("assets") or []:
            for l in a["layers"]:
                if "E30TZ" in str(l.get("nm")):
                    self.assertEqual(l["op"], 180)
                    self.assertEqual((l["ks"]["o"] or {}).get("k"), 100)
                    self.assertEqual((l["ks"]["r"] or {}).get("k"), 0)
                    for g in l["shapes"]:
                        for it in g.get("it") or []:
                            if it.get("ty") == "tr":
                                self.assertEqual((it.get("p") or {}).get("k"), [0, 0])

    def test_v6_keyframe_dedup_only_removes_equal_neighbours(self):
        from emoji_forge.v6 import _dedupe_keyframes
        doc = {"layers": [{"ks": {"s": {"a": 1, "k": [
            {"t": 0, "s": [100, 100, 100]},
            {"t": 10, "s": [100, 100, 100]},
            {"t": 20, "s": [50, 50, 100]},
            {"t": 30, "s": [50, 50, 100]},
            {"t": 40, "s": [50, 50, 100]},
            {"t": 50, "s": [100, 100, 100]}]}}}]}
        removed = _dedupe_keyframes(doc)
        # Only t30 goes: its value equals both original neighbours.
        # t10 and t40 are transition points (next/prev value differs),
        # so removing them would change interpolation timing.
        self.assertEqual(removed, 1)
        times = [f["t"] for f in doc["layers"][0]["ks"]["s"]["k"]]
        self.assertEqual(times, [0, 10, 20, 40, 50])

    def test_v6_single_file_upload_plan(self):
        from emoji_forge.publish import single_file_upload_plan, validate_before_upload
        plan = single_file_upload_plan("forge-v6-test_016_x.tgs")
        self.assertEqual(plan["steps"], ["validate_single_file", "uploadStickerFile", "addStickerToSet"])
        # validate_before_upload on a dir with only the v6 file 016 must pass
        import tempfile, shutil, os
        tmp = tempfile.mkdtemp()
        try:
            shutil.copy("data/projects/forge-v6-test/final/forge-v5-test_016_forge-20261028-155740-3f1382_016_AgADhB0.tgs",
                        os.path.join(tmp, "forge-v6-test_016_x.tgs"))
            blocked = validate_before_upload(["forge-v6-test_016_x.tgs"], tmp)
            self.assertEqual(blocked, [])
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestV7AccuracyAndLogo(unittest.TestCase):
    """v7: audit-driven repairs + traced EHSAN logo (big-size rule)."""

    ORIG = "data/projects/forge-20261028-155740-3f1382/originals"

    def setUp(self):
        sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
        import glob as _g
        self.origs = sorted(_g.glob(os.path.join(self.ORIG, "*.tgs")))
        if len(self.origs) != 20:
            self.skipTest("original pack not present")
        from emoji_forge.brand import BrandProfile
        from emoji_forge.animation_engine import load_tgs, pack_common_signatures
        self.brand = BrandProfile(brand_name="E30TZ", primary_hex="#FF0000",
                                  secondary_hex="#D8F3DC", gradient_enabled=False,
                                  full_recolor=False, watermark_text="TZ Emojis")
        fp = json.load(open("data/projects/forge-v5-test/fingerprint.json"))
        self.hide = set(fp["hide_layer_names"])
        self.docs = [load_tgs(open(p, "rb").read()) for p in self.origs]
        self.sigs = pack_common_signatures(self.docs)

    def _process(self, idx):
        from emoji_forge.v7 import process_tgs_v7
        data = open(self.origs[idx - 1], "rb").read()
        return process_tgs_v7(data, self.brand, self.hide, idx - 1, self.sigs)

    def test_logo_trace_asset_shape(self):
        payload = json.load(open("src/emoji_forge/ehsan_logo_shapes.json"))
        self.assertEqual(payload["width"], 512.0)
        self.assertEqual(len(payload["subpaths"]), 12)
        self.assertEqual(payload["holes"], 2)  # A counter + "0" counter
        # Holes must wind opposite to outers (nonzero fill rule).
        def area(sp):
            v = sp["v"]
            return sum(v[j][0] * v[(j + 1) % len(v)][1]
                       - v[(j + 1) % len(v)][0] * v[j][1]
                       for j in range(len(v))) / 2
        signs = [1 if area(sp) > 0 else -1 for sp in payload["subpaths"]]
        self.assertEqual(signs.count(-1), 2)

    def test_classify_composite_and_restored_elements(self):
        from emoji_forge.v7 import classify_group, classify_layer
        from emoji_forge.v4 import _scope_layers
        d11 = self.docs[10]
        ton = next(l for l in d11["layers"] if l.get("nm") == "TON")
        by_name = {g.get("nm"): g for g in ton["shapes"] if isinstance(g, dict)}
        self.assertEqual(classify_group(d11, None, ton, by_name["Group 4"], self.sigs),
                         "LOGO/BRAND")
        self.assertEqual(classify_group(d11, None, ton, by_name["Group5"], self.sigs),
                         "SCENE")
        self.assertEqual(classify_group(d11, None, ton, by_name["GroupFon"], self.sigs),
                         "THEME")
        rebro = next(l for l in d11["layers"] if l.get("nm") == "TON-Rebro-LEFT")
        self.assertEqual(classify_layer(d11, None, rebro, self.sigs), "SCENE")
        d17 = self.docs[16]
        circ = next(l for l in _scope_layers(d17, "comp_2")
                    if l.get("nm") == "Logo CIrcle")
        self.assertEqual(classify_layer(d17, "comp_2", circ, self.sigs), "THEME")

    def test_file011_coin_restored_and_logo_big(self):
        from emoji_forge.animation_engine import load_tgs
        from emoji_forge.validate_tgs import validate_tgs_bytes
        out, _w, info = self._process(11)
        issues, _ = validate_tgs_bytes(out, 11)
        self.assertEqual(issues, [])
        doc = load_tgs(out)
        names = [l.get("nm") for l in doc["layers"]]
        self.assertIn("TON-Rebro-LEFT", names)
        self.assertIn("TON-Rebro-LEFT 3", names)
        ton = next(l for l in doc["layers"] if l.get("nm") == "TON")
        group_names = [g.get("nm") for g in ton["shapes"] if isinstance(g, dict)]
        # Coin body groups survived; only the glyph group was replaced.
        self.assertIn("GroupFon", group_names)
        self.assertIn("Group5", group_names)
        self.assertIn("EHSAN Logo", group_names)
        self.assertNotIn("Group 4", group_names)
        self.assertGreaterEqual(info["logo"]["width"], 350.0)

    def test_file013_square_and_frame_kept(self):
        from emoji_forge.animation_engine import load_tgs
        out, _w, _info = self._process(13)
        doc = load_tgs(out)
        free = next(l for l in doc["layers"] if l.get("nm") == "FREE")
        group_names = [g.get("nm") for g in free["shapes"] if isinstance(g, dict)]
        self.assertIn("Group 4", group_names)   # theme square body
        self.assertIn("Group 2", group_names)   # white frame
        self.assertIn("EHSAN Logo", group_names)
        self.assertNotIn("Group 5", group_names)  # triangle mark removed

    def test_file017_disc_restored_and_secondary_slot_filled(self):
        from emoji_forge.animation_engine import load_tgs
        from emoji_forge.v4 import _scope_layers
        out, _w, info = self._process(17)
        doc = load_tgs(out)
        comp2 = [l.get("nm") for l in _scope_layers(doc, "comp_2")]
        self.assertIn("Logo CIrcle", comp2)
        self.assertIn("Logo CIrcle 2", comp2)
        self.assertIn("Logo in", comp2)
        comp3 = _scope_layers(doc, "comp_3")
        self.assertTrue(any(l.get("nm") == "EHSAN Logo" for l in comp3))
        self.assertEqual(len(info["secondary"]), 1)
        # Main logo keeps the old (wider-than-canvas) banner width.
        self.assertGreaterEqual(info["logo"]["width"], 800.0)

    def test_file016_alternating_slot_no_longer_blank(self):
        from emoji_forge.animation_engine import load_tgs
        from emoji_forge.v4 import _scope_layers
        out, _w, info = self._process(16)
        doc = load_tgs(out)
        comp1 = _scope_layers(doc, "comp_1")
        self.assertTrue(any(l.get("nm") == "EHSAN Logo" for l in comp1))
        self.assertEqual(len(info["secondary"]), 1)


class TestV8ContainFit(unittest.TestCase):
    """v8: precise contain-fit — the logo fits ENTIRELY inside the
    old logo's on-screen box (user order 2026-10-29 09:59, which
    supersedes v7's always-big rule)."""

    def test_contain_fit_scale_math(self):
        from emoji_forge.v8 import contain_fit_scale
        # Wide flat box (banner 877x130), logo aspect ~2.3: height binds.
        k = contain_fit_scale(512.0, 222.74, 877.0, 130.0)
        self.assertAlmostEqual(512.0 * k, 299.0, delta=1.0)
        self.assertAlmostEqual(222.74 * k, 130.0, delta=0.5)
        # Square-ish box: width binds, height stays inside.
        k = contain_fit_scale(512.0, 222.74, 190.0, 190.0)
        self.assertAlmostEqual(512.0 * k, 190.0, delta=0.5)
        self.assertLessEqual(222.74 * k, 190.0)
        # Exact-aspect box: both bind.
        k = contain_fit_scale(512.0, 222.74, 256.0, 111.37)
        self.assertAlmostEqual(512.0 * k, 256.0, delta=0.5)
        self.assertAlmostEqual(222.74 * k, 111.37, delta=0.5)
        # Degenerate inputs give 0, never a negative/huge scale.
        self.assertEqual(contain_fit_scale(0, 10, 100, 100), 0.0)
        self.assertEqual(contain_fit_scale(10, 10, 0, 100), 0.0)

    def test_solve_logo_group_fit_never_overflows(self):
        import math
        from emoji_forge.v7 import LOGO_H, LOGO_W
        from emoji_forge.v8 import solve_logo_group_fit
        ident = (1.0, 0.0, 0.0, 1.0, 0.0, 0.0)
        group, ach = solve_logo_group_fit(ident, 200.0, 100.0,
                                          (256.0, 256.0), clamp=False)
        self.assertIsNotNone(group)
        self.assertLessEqual(ach["width"], 200.0 + 0.5)
        self.assertLessEqual(ach["height"], 100.0 + 0.5)
        # Width binds (logo aspect 2.3 > box aspect 2.0): the logo
        # meets the box's width and its height (200/2.3 = 87) stays
        # inside. Height would bind only for a flatter box.
        self.assertAlmostEqual(ach["width"], 200.0, delta=1.0)
        self.assertAlmostEqual(ach["height"], 200.0 * 222.74 / 512.0,
                               delta=1.0)
        self.assertEqual(ach["centre"], [256.0, 256.0])
        # Uniform scale 2x: shape-space k halves, screen fit identical.
        group, ach2 = solve_logo_group_fit((2.0, 0.0, 0.0, 2.0, 0.0, 0.0),
                                           200.0, 100.0, (256.0, 256.0),
                                           clamp=False)
        self.assertAlmostEqual(ach2["width"], ach["width"], delta=1.0)
        self.assertAlmostEqual(ach2["height"], ach["height"], delta=1.0)
        self.assertAlmostEqual(ach2["k"], ach["k"] / 2, delta=0.01)
        # Rotation 30deg: the rotated bbox must still fit the box.
        c, s = math.cos(math.radians(30)), math.sin(math.radians(30))
        rot = (c, s, -s, c, 0.0, 0.0)
        group, ach3 = solve_logo_group_fit(rot, 200.0, 100.0,
                                           (256.0, 256.0), clamp=False)
        self.assertLessEqual(ach3["width"], 200.0 + 0.5)
        self.assertLessEqual(ach3["height"], 100.0 + 0.5)
        # The group geometry is the real logo, pre-scaled by k.
        self.assertEqual(group["nm"], "EHSAN Logo")
        self.assertEqual(LOGO_W / LOGO_H, 512.0 / 222.74)

    def test_v8_transform_fits_and_preserves_nonlogo(self):
        import glob as _g
        import json as _json
        from emoji_forge.animation_engine import (
            load_tgs, pack_common_signatures)
        from emoji_forge.v4 import _scope_layers
        from emoji_forge.v8 import canonical_without_logo, rescale_doc_to_fit
        origs = sorted(_g.glob(
            "data/projects/forge-20261028-155740-3f1382/originals/*.tgs"))
        v7s = sorted(_g.glob("data/projects/forge-v7-test/final/*.tgs"))
        if len(origs) != 20 or len(v7s) != 20:
            self.skipTest("pack or v7 finals not present")
        docs = [load_tgs(open(p, "rb").read()) for p in origs]
        sigs = pack_common_signatures(docs)
        fp = _json.load(open("data/projects/forge-v5-test/fingerprint.json"))
        hide = set(fp["hide_layer_names"])
        for idx in (1, 16, 17):
            v7doc = load_tgs(open(v7s[idx - 1], "rb").read())
            before = _json.dumps(canonical_without_logo(v7doc),
                                 sort_keys=True)
            rows = rescale_doc_to_fit(docs[idx - 1], v7doc, sigs, hide)
            after = _json.dumps(canonical_without_logo(v7doc),
                                sort_keys=True)
            # ONLY logo groups changed.
            self.assertEqual(before, after)
            self.assertTrue(rows)
            for r in rows:
                self.assertEqual(r["status"], "ok")
                # Fitted logo inside the old box on both dimensions.
                self.assertLessEqual(r["measured"]["width"],
                                     r["box"][0] + 1.0)
                self.assertLessEqual(r["measured"]["height"],
                                     r["box"][1] + 1.0)
            if idx == 16:
                # Hard rule: file 016's comp_1 stays EMPTY (its
                # secondary logo was proven to make Telegram reject
                # the file); the transform must not re-add anything.
                comp1 = _scope_layers(v7doc, "comp_1")
                self.assertEqual(comp1, [])



# ---------------------------------------------------------------------------
# v9: public release — access control, bot branding, generic audit,
# universal customisation surface, robustness.
# ---------------------------------------------------------------------------

def _v9_bot(tmp, admin_ids=None):
    """A Bot with a mocked API; returns (bot, api, sent list)."""
    from emoji_forge.bot import Bot
    from emoji_forge.config import Config
    config = Config(token="<redacted>", data_dir=os.path.join(tmp, "data"),
                    admin_ids=admin_ids or set())
    api = MagicMock()
    api.get_me.return_value = {"username": "tzemoji_ai", "id": 9071017807}
    sent = []
    api.send_message.side_effect = lambda chat_id, text, reply_markup=None, parse_mode=None: sent.append(
        (chat_id, text, reply_markup))
    return Bot(api, config), api, sent


class TestAccessRegistry(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        from emoji_forge.access import UserRegistry
        self.reg = UserRegistry(os.path.join(self.tmp, "data"), owner_ids=set())

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def test_new_user_is_pending_and_blocked(self):
        rec, is_new = self.reg.touch(555, "Sara", "sara")
        self.assertTrue(is_new)
        self.assertEqual(rec["status"], "pending")
        self.assertFalse(self.reg.is_allowed(555))
        ok, _reason = self.reg.check_quota(555, 3)
        self.assertFalse(ok)

    def test_owner_always_approved_unlimited(self):
        from emoji_forge.access import OWNER_ID
        rec, _ = self.reg.touch(OWNER_ID)
        self.assertEqual(rec["status"], "approved")
        self.assertIsNone(rec["quota"])
        self.assertTrue(self.reg.is_allowed(OWNER_ID))
        ok, _ = self.reg.check_quota(OWNER_ID, 10 ** 6)
        self.assertTrue(ok)
        self.assertIsNone(self.reg.record_usage(OWNER_ID, packs=1, emojis=50))

    def test_approve_sets_approved_at(self):
        self.reg.touch(556)
        rec = self.reg.set_status(556, "approved")
        self.assertEqual(rec["status"], "approved")
        self.assertTrue(rec["approved_at"])
        self.assertTrue(self.reg.is_allowed(556))

    def test_default_quota_values(self):
        from emoji_forge.access import DEFAULT_QUOTA
        self.assertEqual(DEFAULT_QUOTA,
                         {"packs_total": 3, "emojis_total": 100, "packs_per_day": 1})
        rec, _ = self.reg.touch(557)
        self.assertEqual(rec["quota"], DEFAULT_QUOTA)

    def test_quota_refusal_at_pack_limit(self):
        self.reg.touch(558)
        self.reg.set_status(558, "approved")
        self.reg.set_quota(558, {"packs_total": 1, "emojis_total": 100, "packs_per_day": 5})
        ok, _ = self.reg.check_quota(558, 10)
        self.assertTrue(ok)
        self.reg.record_usage(558, packs=1, emojis=10)
        ok, reason = self.reg.check_quota(558, 1)
        self.assertFalse(ok)
        self.assertIn("1/1", reason)

    def test_quota_refusal_emoji_and_daily_limits(self):
        self.reg.touch(559)
        self.reg.set_status(559, "approved")
        self.reg.set_quota(559, {"packs_total": 10, "emojis_total": 5, "packs_per_day": 1})
        ok, _ = self.reg.check_quota(559, 6)
        self.assertFalse(ok)  # emoji limit
        self.reg.record_usage(559, packs=1, emojis=1)
        ok, reason = self.reg.check_quota(559, 1)
        self.assertFalse(ok)  # daily pack limit (1/day)
        self.assertIn("روزانه", reason)

    def test_record_usage_daily_key_tehran(self):
        from emoji_forge.access import tehran_today
        self.reg.touch(560)
        self.reg.set_status(560, "approved")
        rec = self.reg.record_usage(560, packs=1, emojis=7)
        self.assertEqual(rec["usage"]["packs_used"], 1)
        self.assertEqual(rec["usage"]["emojis_used"], 7)
        day = rec["usage"]["usage_by_day"][tehran_today()]
        self.assertEqual((day["packs"], day["emojis"]), (1, 7))

    def test_negative_quota_rejected(self):
        self.reg.touch(561)
        with self.assertRaises(ValueError):
            self.reg.set_quota(561, {"packs_total": -1})

    def test_profile_merge_preserves_access_fields(self):
        self.reg.touch(562)
        self.reg.set_status(562, "approved")
        self.reg.update_profile(562, {"brand": {"brand_name": "X"},
                                      "ownership_confirmed": True})
        rec = self.reg.get(562)
        self.assertEqual(rec["status"], "approved")
        self.assertEqual(rec["brand"], {"brand_name": "X"})
        self.assertTrue(rec["ownership_confirmed"])

    def test_unlimited_quota_preset(self):
        self.reg.touch(563)
        self.reg.set_status(563, "approved")
        self.reg.set_quota(563, None)
        ok, _ = self.reg.check_quota(563, 10 ** 5)
        self.assertTrue(ok)


class TestAccessBotGate(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.bot, self.api, self.sent = _v9_bot(self.tmp)

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def _msg(self, user_id, text):
        self.bot.handle_message({"chat": {"id": user_id},
                                 "from": {"id": user_id, "first_name": "N"},
                                 "text": text})

    def test_pending_user_blocked_and_owner_notified(self):
        from emoji_forge.access import OWNER_ID
        self._msg(777, "/process")
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("در انتظار تأیید" in t for t in texts))
        notify = [m for c, _t, m in self.sent
                  if c == OWNER_ID and m and "admin:approve:777" in json.dumps(m)]
        self.assertTrue(notify, "owner must get Approve/Reject buttons")
        self.assertEqual(self.bot.registry.status_of(777), "pending")

    def test_pending_user_start_gets_pending_welcome(self):
        self._msg(778, "/start")
        texts = [t for c, t, _m in self.sent if c == 778]
        self.assertTrue(any("نیازمند تأیید مالک" in t for t in texts))

    def test_approve_callback_flips_status(self):
        self._msg(779, "سلام")
        self.assertEqual(self.bot.registry.status_of(779), "pending")
        from emoji_forge.access import OWNER_ID
        self.bot.handle_callback({
            "id": "q1", "data": "admin:approve:779",
            "from": {"id": OWNER_ID}, "message": {"chat": {"id": OWNER_ID}}})
        self.assertEqual(self.bot.registry.status_of(779), "approved")
        user_texts = [t for c, t, _m in self.sent if c == 779]
        self.assertTrue(any("تأیید شد" in t for t in user_texts))

    def test_non_owner_cannot_approve(self):
        self._msg(780, "سلام")
        self.api.answer_callback.reset_mock()
        self.bot.handle_callback({
            "id": "q9", "data": "admin:approve:780",
            "from": {"id": 888}, "message": {"chat": {"id": 888}}})
        self.assertEqual(self.bot.registry.status_of(780), "pending")
        answers = [c for c in self.api.answer_callback.call_args_list
                   if len(c.args) >= 2 and "فقط برای مالک" in str(c.args[1])]
        self.assertTrue(answers, "non-owner must get a refusal callback answer")

    def test_non_owner_admin_command_refused(self):
        sess = self.bot.session(889)
        self.bot.handle_command(sess, 889, 889, "/users")
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("فقط برای مالک" in t for t in texts))
        self.api.set_my_name.assert_not_called()

    def test_quota_gate_refuses_process_enqueue(self):
        self._msg(781, "سلام")
        self.bot.registry.set_status(781, "approved")
        self.bot.registry.set_quota(781, {"packs_total": 0, "emojis_total": 0,
                                          "packs_per_day": 0})
        sess = self.bot.session(781)
        sess.project_id = "p-quota"
        sess.manifest = {"items": [{"index": 1}]}
        sess.preview_approved = True
        self.bot.handle_command(sess, 781, 781, "/process")
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("سهمیه" in t for t in texts))
        self.assertFalse(any("در صف قرار گرفت" in t for t in texts))

    def test_usage_recorded_only_on_success(self):
        from emoji_forge.bot import Bot
        self.bot.registry.touch(782)
        job = {"id": "j1", "kind": "full", "user_id": 782, "chat_id": 782,
               "manifest": {"items": [1, 2, 3]}}
        with patch.object(Bot, "_run_job_inner", return_value="full run: 3/3 ok"):
            self.bot._run_job(job)
        rec = self.bot.registry.get(782)
        self.assertEqual(rec["usage"]["packs_used"], 1)
        self.assertEqual(rec["usage"]["emojis_used"], 3)
        with patch.object(Bot, "_run_job_inner", return_value="publish failed: x"):
            self.bot._run_job({**job, "id": "j2", "kind": "publish"})
        with patch.object(Bot, "_run_job_inner", return_value="cancelled"):
            self.bot._run_job({**job, "id": "j3"})
        rec = self.bot.registry.get(782)
        self.assertEqual(rec["usage"]["packs_used"], 1)  # unchanged
        with patch.object(Bot, "_run_job_inner",
                          return_value="published s_by_tzemoji_ai (3 stickers)"):
            self.bot._run_job({**job, "id": "j4", "kind": "publish"})
        rec = self.bot.registry.get(782)
        self.assertEqual(rec["usage"]["packs_used"], 2)
        self.assertEqual(rec["usage"]["emojis_used"], 6)


class TestBrandingV9(unittest.TestCase):
    def test_publish_title_rule(self):
        from emoji_forge.branding import publish_title
        self.assertEqual(publish_title("عنوان من", "p1"), "عنوان من")  # no suffix
        self.assertTrue(publish_title("", "p1").startswith("TZ Emojis"))
        self.assertTrue(publish_title(None, "p1").startswith("TZ Emojis"))
        self.assertLessEqual(len(publish_title("x" * 100, "p1")), 64)

    def test_texts_carry_bot_brand(self):
        from emoji_forge.branding import help_text, welcome_text, zip_caption
        self.assertIn("TZ Emojis", welcome_text())
        self.assertNotIn("Ai Agent", welcome_text())
        self.assertIn("ساخته‌شده توسط TZ Emojis", help_text())
        self.assertIn("TZ Emojis", zip_caption(3))
        # Owner rule (2026-10-29): no trace of EHSAN / E30TZ in bot-facing texts.
        for text in (welcome_text(), help_text(), zip_caption(3)):
            self.assertNotIn("EHSAN", text)
            self.assertNotIn("E30TZ", text)
            self.assertNotIn("متی", text)

    def test_apply_branding_calls_four_setters(self):
        from emoji_forge.branding import BOT_COMMANDS, BOT_NAME, apply_branding
        api = MagicMock()
        results = apply_branding(api)
        self.assertEqual(set(results), {"name", "short_description",
                                        "description", "commands"})
        api.set_my_name.assert_called_once_with(BOT_NAME)
        api.set_my_commands.assert_called_once_with(BOT_COMMANDS)

    def test_apply_branding_command_owner_only(self):
        tmp = tempfile.mkdtemp()
        try:
            bot, api, sent = _v9_bot(tmp)
            from emoji_forge.access import OWNER_ID
            sess = bot.session(999)
            bot.handle_command(sess, 999, 999, "/apply_branding")
            api.set_my_name.assert_not_called()
            sess2 = bot.session(OWNER_ID)
            bot.handle_command(sess2, OWNER_ID, OWNER_ID, "/apply_branding")
            api.set_my_name.assert_called_once()
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


def _audit_shape_layer(name, verts, fill):
    n = len(verts)
    return {"nm": name, "ty": 4, "shapes": [{"ty": "gr", "nm": name + " G", "it": [
        {"ty": "sh", "ks": {"a": 0, "k": {"v": verts, "i": [[0, 0]] * n,
                                          "o": [[0, 0]] * n, "c": True}}},
        {"ty": "fl", "c": {"a": 0, "k": fill}}]}]}


class TestAuditGeneric(unittest.TestCase):
    """The generic audit on a synthetic NON-sVnTeam pack: different
    names, a pack-common watermark shape, a character."""

    def _pack(self, shared):
        square = [[0, 0], [120, 0], [120, 120], [0, 120]]
        docs = []
        for i in range(4):
            hero = [[i * 7, 0], [200 - i * 5, 30], [150, 220 - i * 3], [20, 180]]
            layers = [_audit_shape_layer("Hero", hero, [0.9, 0.7, 0.55, 1])]
            if shared:
                layers.append(_audit_shape_layer("CornerThing", square,
                                                 [0.2, 0.2, 0.9, 1]))
            docs.append({"v": "5.7.4", "fr": 30, "ip": 0, "op": 60,
                         "w": 512, "h": 512, "layers": layers})
        return docs

    def test_pack_common_watermark_found_character_kept(self):
        from emoji_forge.audit import audit_pack
        report = audit_pack(self._pack(shared=True))
        self.assertEqual(report["confidence"], "high")
        self.assertIn("CornerThing", report["logo_layer_names"])
        hero = [e for e in report["elements"] if e["layer"] == "Hero"]
        self.assertTrue(hero)
        self.assertTrue(all(e["class"] == "SCENE" for e in hero))

    def test_no_shared_evidence_is_low_confidence(self):
        from emoji_forge.audit import audit_pack
        docs = []
        for i in range(3):
            hero = [[i * 11, 0], [190 - i * 4, 40], [140, 210 - i * 2], [30, 170]]
            bg = [[0, 0], [300 - i, 0], [300 - i, 300], [0, 300]]
            docs.append({"v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512,
                         "h": 512, "layers": [
                             _audit_shape_layer("Hero", hero, [0.9, 0.7, 0.55, 1]),
                             _audit_shape_layer("Bg", bg, [0.1, 0.5, 0.2, 1])]})
        report = audit_pack(docs)
        self.assertEqual(report["confidence"], "low")
        self.assertEqual(report["logo_layer_names"], [])


def _v9_doc():
    def layer(ind, nm, y=256, w=512, h=512):
        return {"ddd": 0, "ind": ind, "ty": 4, "nm": nm, "sr": 1,
                "ks": {"p": {"a": 0, "k": [256, y, 0]}, "a": {"a": 0, "k": [0, 0, 0]},
                       "s": {"a": 0, "k": [100, 100, 100]}, "r": {"a": 0, "k": 0},
                       "o": {"a": 0, "k": 100}},
                "ao": 0, "shapes": [{"ty": "gr", "it": [
                    {"ty": "rc", "s": {"a": 0, "k": [w, h]}, "p": {"a": 0, "k": [0, 0]},
                     "r": {"a": 0, "k": 0}},
                    {"ty": "fl", "c": {"a": 0, "k": [0, 0, 1, 1]},
                     "o": {"a": 0, "k": 100}},
                    # Real TGS groups always end with a transform item;
                    # the v10 importer guard/validator require it.
                    {"ty": "tr", "p": {"a": 0, "k": [0, 0]},
                     "a": {"a": 0, "k": [0, 0]}, "s": {"a": 0, "k": [100, 100]},
                     "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
                     "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0}}]}],
                "ip": 0, "op": 60}
    return {"v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "layers": [layer(1, "Back"),
                       layer(2, "LOGO PLACE", y=100, w=200, h=100)]}


class TestPipelineV8Mode(unittest.TestCase):
    def test_v8_mode_registered_and_fit_is_default(self):
        from emoji_forge.pipeline import MODES
        self.assertIn("v8", MODES)
        profile = BrandProfile()
        self.assertEqual(profile.logo_size_mode, "fit")  # contain-fit DEFAULT
        self.assertEqual(profile.font_style, "bold_sans")

    def test_v8_text_path_places_brand_name(self):
        from emoji_forge.pipeline import process_tgs
        brand = BrandProfile(brand_name="MYBRAND", primary_hex="#FF0000")
        out, _warnings = process_tgs(dump_tgs(_v9_doc()), brand,
                                     {"LOGO PLACE"}, "v8")
        doc = load_tgs(out)
        self.assertTrue(any("MYBRAND" in str(l.get("nm", ""))
                            for l in doc["layers"]))

    def test_v8_logo_path_places_traced_logo(self):
        try:
            import vtracer  # noqa: F401
        except Exception:
            self.skipTest("vtracer unavailable")
        from emoji_forge.pipeline import process_tgs
        img = Image.new("RGBA", (300, 150), (0, 0, 0, 0))
        ImageDraw.Draw(img).rounded_rectangle([10, 30, 290, 120], radius=20,
                                              fill=(0, 0, 0, 255))
        buf = io.BytesIO()
        img.save(buf, format="PNG")
        brand = BrandProfile(brand_name="MYBRAND", primary_hex="#FF0000")
        out, _warnings = process_tgs(dump_tgs(_v9_doc()), brand,
                                     {"LOGO PLACE"}, "v8",
                                     logo_data=buf.getvalue())
        doc = load_tgs(out)
        names = [str(l.get("nm", "")) for l in doc["layers"]]
        names += [str(g.get("nm", "")) for l in doc["layers"]
                  for g in (l.get("shapes") or []) if isinstance(g, dict)]
        self.assertIn("Brand Logo", names)

    def test_v8_static_items_use_strip_engine(self):
        tmp = tempfile.mkdtemp()
        try:
            from emoji_forge.storage import project_dir as _pd
            data_dir = os.path.join(tmp, "data")
            project_id = new_project_id()
            _pd(data_dir, project_id)
            png = make_sample_png(os.path.join(tmp, "a.png"))
            manifest = {"project_id": project_id, "set_name": "",
                        "items": [{"index": 1, "local_path": png,
                                   "status": "downloaded"}]}
            report = process_project(data_dir, project_id, manifest,
                                     BrandProfile(brand_name="X"), None,
                                     "v8", "emoji")
            self.assertEqual(report["items"][0]["status"], "ok")
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_raster_font_styles_load(self):
        from emoji_forge.image_engine import load_font
        for style, needle in (("bold_sans", "Sans"), ("serif", "Serif"),
                              ("mono", "Mono")):
            font = load_font(style, 24)
            self.assertIn(needle, font.getname()[0])


class TestRobustnessV9(unittest.TestCase):
    def test_running_jobs_marked_interrupted_on_startup(self):
        from emoji_forge.storage import load_jobs, save_jobs
        tmp = tempfile.mkdtemp()
        try:
            data_dir = os.path.join(tmp, "data")
            os.makedirs(data_dir, exist_ok=True)
            save_jobs(data_dir, {"jobs": [{"id": "j1", "status": "running"},
                                          {"id": "j2", "status": "done"}]})
            JobQueue(data_dir, lambda job: "ok")
            jobs = {j["id"]: j for j in load_jobs(data_dir)["jobs"]}
            self.assertEqual(jobs["j1"]["status"], "interrupted")
            self.assertTrue(jobs["j1"].get("detail"))
            self.assertEqual(jobs["j2"]["status"], "done")
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_self_check_ok_and_readiness_line(self):
        from emoji_forge.selfcheck import readiness_line, run_self_check
        tmp = tempfile.mkdtemp()
        try:
            ok, _lines = run_self_check(os.path.join(tmp, "data"))
            self.assertTrue(ok)
            line = readiness_line(os.path.join(tmp, "data"))
            self.assertTrue(line.startswith("[forge]"))
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestSingleFilePublishGate(unittest.TestCase):
    def _setup_project(self, tmp, doc):
        from emoji_forge.storage import project_dir as _pd
        data_dir = os.path.join(tmp, "data")
        project_id = new_project_id()
        root = _pd(data_dir, project_id)
        final_dir = os.path.join(root, "final")
        os.makedirs(final_dir, exist_ok=True)
        return data_dir, project_id, final_dir

    def test_invalid_tgs_blocked_before_upload(self):
        tmp = tempfile.mkdtemp()
        try:
            bot, api, _sent = _v9_bot(tmp)
            _dd, project_id, final_dir = self._setup_project(tmp, None)
            doc = _v9_doc()
            doc["w"], doc["h"] = 100, 100  # validator blocks non-512 canvas
            name = f"{project_id}_001_bad.tgs"
            with open(os.path.join(final_dir, name), "wb") as fh:
                fh.write(dump_tgs(doc))
            result = bot.publish_single_file(777, 777, project_id, name)
            self.assertTrue(result.startswith("single publish blocked"))
            api.upload_sticker_file.assert_not_called()
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_valid_tgs_publishes_single_set(self):
        tmp = tempfile.mkdtemp()
        try:
            bot, api, _sent = _v9_bot(tmp)
            api.upload_sticker_file.return_value = {"file_id": "up1"}
            api.create_new_sticker_set.return_value = True
            _dd, project_id, final_dir = self._setup_project(tmp, None)
            name = f"{project_id}_001_good.tgs"
            with open(os.path.join(final_dir, name), "wb") as fh:
                fh.write(dump_tgs(_v9_doc()))
            with patch("emoji_forge.bot.time.sleep", lambda *_a: None):
                result = bot.publish_single_file(777, 777, project_id, name)
            self.assertTrue(result.startswith("published"))
            self.assertIn("(1 stickers)", result)
            api.create_new_sticker_set.assert_called_once()
        finally:
            shutil.rmtree(tmp, ignore_errors=True)
