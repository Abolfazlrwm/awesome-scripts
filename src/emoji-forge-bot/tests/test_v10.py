"""Tests for the v10 update: importer guard + validator signatures,
resilient Telegram retries, resumable publish checkpoint, gradient
registry, and the Persian progress helper. No live Telegram: the
API session is mocked exactly like the older suites."""
import os
import shutil
import sys
import tempfile
import unittest
from unittest.mock import MagicMock, patch

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

import requests

from emoji_forge import __version__
from emoji_forge.animation_engine import dump_tgs, load_tgs
from emoji_forge.importer_guard import fix_importer_risks, iter_groups
from emoji_forge.progress import ProgressReporter, fa_num, format_progress, progress_bar
from emoji_forge.validate_tgs import validate_tgs_bytes

TR = {"ty": "tr", "p": {"a": 0, "k": [0, 0]}, "a": {"a": 0, "k": [0, 0]},
      "s": {"a": 0, "k": [100, 100]}, "r": {"a": 0, "k": 0},
      "o": {"a": 0, "k": 100}, "sk": {"a": 0, "k": 0},
      "sa": {"a": 0, "k": 0}, "nm": "Transform"}
FL = {"ty": "fl", "c": {"a": 0, "k": [1, 1, 1, 1]}, "o": {"a": 0, "k": 100},
      "r": 1, "bm": 0, "nm": "Fill 1"}
ST = {"ty": "st", "c": {"a": 0, "k": [0.1, 0, 0.3, 1]}, "o": {"a": 0, "k": 100},
      "w": {"a": 0, "k": 2}, "lc": 2, "lj": 2, "bm": 0, "nm": "Stroke 1"}


def shape(n=3, tangents_zero=True):
    import math
    v = [[round(100 + 50 * math.cos(2 * math.pi * i / n), 2),
          round(100 + 50 * math.sin(2 * math.pi * i / n), 2)] for i in range(n)]
    tang = [[0, 0]] * n if tangents_zero else [[5, 5]] * n
    return {"ty": "sh", "ks": {"a": 0, "k": {"v": v, "i": tang, "o": tang, "c": True}},
            "nm": "Path 1"}


def subgroup(name="Sub"):
    return {"ty": "gr", "nm": name, "it": [shape(4), FL, dict(TR)]}


def doc_with_group(group):
    return {"v": "5.7.0", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "assets": [],
            "layers": [{"ddd": 0, "ind": 1, "ty": 4, "nm": "L", "sr": 1,
                        "ks": {"p": {"a": 0, "k": [256, 256, 0]},
                               "a": {"a": 0, "k": [0, 0, 0]},
                               "s": {"a": 0, "k": [100, 100, 100]},
                               "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100}},
                        "ao": 0, "shapes": [group], "ip": 0, "op": 60}]}


class TestImporterGuard(unittest.TestCase):
    def test_adds_missing_tr(self):
        group = {"ty": "gr", "nm": "G", "it": [shape(3), FL]}  # no tr
        doc = doc_with_group(group)
        fixed, report = fix_importer_risks(doc)
        self.assertEqual(report["tr_added"], 1)
        groups = list(iter_groups(fixed["layers"][0]["shapes"]))
        self.assertEqual(groups[0]["it"][-1]["ty"], "tr")
        # input not mutated
        self.assertEqual(doc["layers"][0]["shapes"][0]["it"][-1]["ty"], "fl")

    def test_strips_paint_from_paint_mixing_group(self):
        group = {"ty": "gr", "nm": "Abolfazl Brand",
                 "it": [subgroup("A"), subgroup("B"), subgroup("C"),
                        dict(FL), dict(ST), dict(TR)]}
        fixed, report = fix_importer_risks(doc_with_group(group))
        self.assertEqual(report["paint_stripped"], 2)
        tys = [it["ty"] for it in fixed["layers"][0]["shapes"][0]["it"]]
        self.assertEqual(tys, ["gr", "gr", "gr", "tr"])

    def test_direct_shape_group_keeps_its_paint(self):
        # A group WITH a direct shape + fl + st is scene art, not the
        # rejection signature — its paints must survive.
        group = {"ty": "gr", "nm": "Scene",
                 "it": [shape(5), subgroup("A"), subgroup("B"),
                        dict(FL), dict(ST), dict(TR)]}
        fixed, report = fix_importer_risks(doc_with_group(group))
        self.assertEqual(report["paint_stripped"], 0)
        tys = [it["ty"] for it in fixed["layers"][0]["shapes"][0]["it"]]
        self.assertIn("fl", tys)
        self.assertIn("st", tys)

    def test_single_paint_group_untouched(self):
        # Accepted scene art in the real packs: subgroups + ONE paint.
        group = {"ty": "gr", "nm": "Star",
                 "it": [subgroup("A"), subgroup("B"), dict(ST), dict(TR)]}
        fixed, report = fix_importer_risks(doc_with_group(group))
        self.assertEqual(report["paint_stripped"], 0)

    def test_idempotent(self):
        group = {"ty": "gr", "nm": "Abolfazl Brand",
                 "it": [subgroup("A"), subgroup("B"), dict(FL), dict(ST)]}
        once, _r1 = fix_importer_risks(doc_with_group(group))
        _twice, report2 = fix_importer_risks(once)
        self.assertEqual(report2["tr_added"], 0)
        self.assertEqual(report2["paint_stripped"], 0)
        self.assertEqual(report2["vertices_removed"], 0)

    def test_simplifies_oversized_zero_tangent_group(self):
        big = {"ty": "gr", "nm": "Big",
               "it": [shape(1200, tangents_zero=True), dict(TR)]}
        fixed, report = fix_importer_risks(doc_with_group(big))
        self.assertGreater(report["vertices_removed"], 0)
        v = fixed["layers"][0]["shapes"][0]["it"][0]["ks"]["k"]["v"]
        self.assertLess(len(v), 600)

    def test_curved_oversized_group_is_skipped_not_mangled(self):
        big = {"ty": "gr", "nm": "Curved",
               "it": [shape(1200, tangents_zero=False), dict(TR)]}
        fixed, report = fix_importer_risks(doc_with_group(big))
        self.assertEqual(report["vertices_removed"], 0)
        self.assertGreaterEqual(report["simplify_skipped"], 1)
        v = fixed["layers"][0]["shapes"][0]["it"][0]["ks"]["k"]["v"]
        self.assertEqual(len(v), 1200)


class TestValidatorV10Signatures(unittest.TestCase):
    def test_flags_missing_tr_as_blocking(self):
        group = {"ty": "gr", "nm": "G", "it": [shape(3), FL]}
        issues, _w = validate_tgs_bytes(dump_tgs(doc_with_group(group)), 7)
        self.assertTrue(any("tr" in i for i in issues), issues)

    def test_flags_paint_mixing_as_blocking(self):
        group = {"ty": "gr", "nm": "Brand",
                 "it": [subgroup("A"), subgroup("B"), subgroup("C"),
                        dict(FL), dict(ST), dict(TR)]}
        issues, _w = validate_tgs_bytes(dump_tgs(doc_with_group(group)), 7)
        self.assertTrue(any("رنگ‌آمیزی گروهی" in i for i in issues), issues)

    def test_oversized_group_is_warning_not_blocking(self):
        group = {"ty": "gr", "nm": "Big",
                 "it": [shape(950, tangents_zero=True), dict(TR)]}
        issues, warnings = validate_tgs_bytes(dump_tgs(doc_with_group(group)), 7)
        self.assertEqual(issues, [])
        self.assertTrue(any("رأس" in w for w in warnings), warnings)

    def test_clean_group_passes(self):
        group = {"ty": "gr", "nm": "Ok", "it": [shape(4), FL, dict(TR)]}
        issues, _w = validate_tgs_bytes(dump_tgs(doc_with_group(group)), 7)
        self.assertEqual(issues, [])


def _resp(payload, status=200):
    resp = MagicMock()
    resp.json.return_value = payload
    resp.status_code = status
    return resp


class TestResilientTelegramCall(unittest.TestCase):
    def setUp(self):
        from emoji_forge.telegram_api import TelegramApi
        self.api = TelegramApi("123456:ABCDEF1234567890abcdef")
        sleep_patcher = patch("emoji_forge.telegram_api.time.sleep",
                              lambda *_a, **_k: None)
        sleep_patcher.start()
        self.addCleanup(sleep_patcher.stop)

    def test_transient_400_retried_then_succeeds(self):
        bad = _resp({"ok": False, "error_code": 400,
                     "description": "Bad Request: wrong file type"}, 400)
        good = _resp({"ok": True, "result": {"file_id": "x"}})
        with patch.object(self.api.session, "post",
                          side_effect=[bad, good]) as post:
            out = self.api._call("uploadStickerFile", {})
        self.assertEqual(out, {"file_id": "x"})
        self.assertEqual(post.call_count, 2)

    def test_non_empty_400_retried(self):
        bad = _resp({"ok": False, "error_code": 400,
                     "description": "Bad Request: file must be non-empty"}, 400)
        good = _resp({"ok": True, "result": True})
        with patch.object(self.api.session, "post",
                          side_effect=[bad, good]) as post:
            self.assertTrue(self.api._call("addStickerToSet", {}))
        self.assertEqual(post.call_count, 2)

    def test_permanent_400_not_retried(self):
        from emoji_forge.errors import ExternalError
        bad = _resp({"ok": False, "error_code": 400,
                     "description": "Bad Request: chat not found"}, 400)
        with patch.object(self.api.session, "post",
                          side_effect=[bad]) as post:
            with self.assertRaises(ExternalError):
                self.api._call("sendMessage", {})
        self.assertEqual(post.call_count, 1)

    def test_429_honours_retry_after(self):
        limited = _resp({"ok": False, "error_code": 429,
                         "description": "Too Many Requests: retry after 5",
                         "parameters": {"retry_after": 5}}, 429)
        good = _resp({"ok": True, "result": True})
        sleeps = []
        with patch("emoji_forge.telegram_api.time.sleep", sleeps.append), \
             patch.object(self.api.session, "post",
                          side_effect=[limited, good]) as post:
            self.assertTrue(self.api._call("sendMessage", {}))
        self.assertEqual(post.call_count, 2)
        self.assertIn(5, sleeps)  # max(backoff=2, retry_after=5)

    def test_network_error_retried(self):
        good = _resp({"ok": True, "result": {"id": 1}})
        with patch.object(self.api.session, "post",
                          side_effect=[requests.ConnectionError("boom"),
                                       good]) as post:
            out = self.api._call("getMe", {})
        self.assertEqual(out, {"id": 1})
        self.assertEqual(post.call_count, 2)


class TestPublishCheckpoint(unittest.TestCase):
    def test_checkpoint_roundtrip_and_manifest_emojis(self):
        from emoji_forge.publish import (
            checkpoint_done_indices, checkpoint_published,
            checkpoint_published_indices, checkpoint_uploaded,
            load_checkpoint, manifest_emojis, save_checkpoint,
        )
        from emoji_forge.storage import write_json
        tmp = tempfile.mkdtemp()
        try:
            cp = load_checkpoint(tmp)
            self.assertEqual(cp["stickers"], {})
            checkpoint_uploaded(cp, 1, "a.tgs", "file-1")
            checkpoint_published(cp, [1])
            save_checkpoint(tmp, cp)
            again = load_checkpoint(tmp)
            self.assertEqual(checkpoint_done_indices(again), {1})
            self.assertEqual(checkpoint_published_indices(again), {1})
            self.assertEqual(again["stickers"]["1"]["file_id"], "file-1")
            write_json(os.path.join(tmp, "manifest.json"),
                       {"items": [{"index": 1, "emoji": "🌿"},
                                  {"index": 2, "emoji": ""}]})
            self.assertEqual(manifest_emojis(tmp), {1: ["🌿"]})
        finally:
            shutil.rmtree(tmp, ignore_errors=True)

    def test_publish_job_resume_skips_uploaded_stickers(self):
        from emoji_forge.bot import Bot
        from emoji_forge.config import Config
        from emoji_forge.publish import (
            checkpoint_uploaded, load_checkpoint, save_checkpoint,
        )
        from emoji_forge.storage import new_project_id, project_dir
        tmp = tempfile.mkdtemp()
        try:
            data_dir = os.path.join(tmp, "data")
            config = Config(token="123456:ABCDEF1234567890abcdef",
                            data_dir=data_dir)
            api = MagicMock()
            api.get_me.return_value = {"username": "ForgeTestBot", "id": 1}
            api.upload_sticker_file.side_effect = [{"file_id": "up2"}]
            api.create_new_sticker_set.return_value = True
            api.send_message.side_effect = lambda *a, **k: None
            bot = Bot(api, config)
            project_id = new_project_id()
            root = project_dir(data_dir, project_id)
            final_dir = os.path.join(root, "final")
            os.makedirs(final_dir)
            for name in (f"{project_id}_001_a.webp", f"{project_id}_002_b.webp"):
                with open(os.path.join(final_dir, name), "wb") as fh:
                    fh.write(b"fake-webp-bytes")
            # Sticker 1 was already uploaded by a previous, crashed run.
            cp = load_checkpoint(root)
            checkpoint_uploaded(cp, 1, f"{project_id}_001_a.webp", "up1")
            save_checkpoint(root, cp)
            manifest = {"set_title": "T", "items": [
                {"index": 1, "emoji": "🌿"}, {"index": 2, "emoji": "✨"}]}
            with patch("emoji_forge.bot.time.sleep", lambda *_a: None):
                result = bot._run_publish_job({
                    "id": "pub-resume", "kind": "publish", "user_id": 5,
                    "chat_id": 6, "project_id": project_id,
                    "manifest": manifest, "set_title": "T"})
            self.assertIn("published", result)
            # Only sticker 2 was uploaded in this run.
            api.upload_sticker_file.assert_called_once()
            args = api.create_new_sticker_set.call_args[0]
            self.assertEqual(len(args[3]), 2)
            self.assertEqual(args[3][0]["sticker"], "up1")  # reused id
            self.assertEqual(args[3][1]["sticker"], "up2")
            self.assertEqual(args[3][0]["emoji_list"], ["🌿"])  # manifest emoji
            done = load_checkpoint(root)
            self.assertTrue(done["set_created"])
        finally:
            shutil.rmtree(tmp, ignore_errors=True)


class TestRegistryV10(unittest.TestCase):
    def test_counts_expanded(self):
        from emoji_forge import registry
        self.assertGreaterEqual(len(registry.palette_flat()), 108)
        self.assertGreaterEqual(len(registry.gradients()), 12)
        self.assertGreaterEqual(len(registry.FONTS), 25)

    def test_gradient_lookup(self):
        from emoji_forge import registry
        sunset = registry.gradient_by_id("sunset")
        self.assertEqual(sunset["color1"], "#FF8000")
        self.assertEqual(sunset["color2"], "#FF00FF")
        self.assertIsNone(registry.gradient_by_id("no_such_gradient"))
        self.assertIsNone(registry.gradient_by_id(None))
        for grad in registry.gradients():
            self.assertTrue(grad["id"] and grad["fa"])
            self.assertRegex(grad["color1"], r"^#[0-9A-F]{6}$")
            self.assertRegex(grad["color2"], r"^#[0-9A-F]{6}$")


class TestProgressHelper(unittest.TestCase):
    def test_format_progress_fa_digits_and_bar(self):
        text = format_progress(120, 200, elapsed=40.0)
        self.assertIn("██████░░░░", text)
        self.assertIn("۶۰٪", text)
        self.assertIn("۱۲۰ از ۲۰۰", text)
        self.assertIn("ثانیه مانده", text)  # ETA present mid-run

    def test_format_progress_bounds(self):
        self.assertIn("۰٪", format_progress(0, 10))
        done = format_progress(10, 10, elapsed=5.0)
        self.assertIn("۱۰۰٪", done)
        self.assertNotIn("مانده", done)  # no ETA at completion
        self.assertEqual(progress_bar(0), "░" * 10)
        self.assertEqual(progress_bar(100), "█" * 10)
        self.assertEqual(fa_num(123), "۱۲۳")

    def test_reporter_throttles(self):
        now = [1000.0]
        sent = []
        reporter = ProgressReporter(sent.append, min_interval=3.0,
                                    step_pct=5.0, clock=lambda: now[0])
        self.assertTrue(reporter.update(1, 100))      # first always emits
        self.assertFalse(reporter.update(2, 100))     # +1% < step, < interval
        now[0] += 3.0
        self.assertTrue(reporter.update(3, 100))      # interval reached
        self.assertTrue(reporter.update(100, 100))    # completion emits
        self.assertEqual(len(sent), 3)


class TestVersionV10(unittest.TestCase):
    def test_version_is_10(self):
        self.assertEqual(__version__, "10.1.0")
        from emoji_forge.branding import V10_WHATS_NEW, help_text, welcome_text
        self.assertIn("نسخه ۱۰", welcome_text())
        self.assertIn("نسخه ۱۰", help_text())
        self.assertIn("گارد ایمپورتر", V10_WHATS_NEW)


if __name__ == "__main__":
    unittest.main()
