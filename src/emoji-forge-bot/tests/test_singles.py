"""Tests for single-emoji editing (v10): pure keyboard/set-name
builders, process routing, and a mocked end-to-end bot flow.
No Telegram network; the API and the processing engine are mocked,
exactly like the pack-flow tests in test_emoji_forge.py.
"""
import json
import os
import shutil
import sys
import tempfile
import unittest
from unittest.mock import MagicMock, patch

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from emoji_forge.animation_engine import dump_tgs
from emoji_forge.brand import BrandProfile
from urllib.parse import parse_qs, urlparse

from emoji_forge.singles import (
    build_color_keyboard, build_font_keyboard, flatten_palette,
    load_fonts, load_palette, process_single, single_action_keyboard,
    single_set_candidates, studio_button_row, studio_url,
)
from emoji_forge.storage import load_jobs


def make_doc():
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
                    {"ty": "tr", "p": {"a": 0, "k": [0, 0]},
                     "a": {"a": 0, "k": [0, 0]}, "s": {"a": 0, "k": [100, 100]},
                     "r": {"a": 0, "k": 0}, "o": {"a": 0, "k": 100},
                     "sk": {"a": 0, "k": 0}, "sa": {"a": 0, "k": 0}}]}],
                "ip": 0, "op": 60}
    return {"v": "5.7.4", "fr": 30, "ip": 0, "op": 60, "w": 512, "h": 512,
            "layers": [layer(1, "Back"), layer(2, "LOGO PLACE", y=100, w=200, h=100)]}


def make_bot(tmp, user_id=777):
    """Bot with mocked API + an approved normal user."""
    from emoji_forge.bot import Bot
    from emoji_forge.config import Config
    config = Config(token="<redacted>",
                    data_dir=os.path.join(tmp, "data"), admin_ids=set())
    api = MagicMock()
    api.get_me.return_value = {"username": "tzemoji_ai", "id": 9071017807}
    sent = []
    api.send_message.side_effect = lambda chat_id, text, reply_markup=None, parse_mode=None: sent.append(
        (chat_id, text, reply_markup))
    bot = Bot(api, config)
    bot.registry.touch(user_id, "Tester", "tester")
    bot.registry.set_status(user_id, "approved")
    return bot, api, sent


def callback(bot, data, user_id=777):
    bot.handle_callback({"id": "q1", "data": data, "from": {"id": user_id},
                         "message": {"chat": {"id": user_id}}})


def single_jobs(bot):
    jobs = load_jobs(bot.config.data_dir).get("jobs", [])
    return [j for j in jobs if j.get("kind") == "single"]


class TestSingleKeyboards(unittest.TestCase):
    def test_flatten_palette_spec_shape(self):
        palette = [{"fa": "قرمزها", "colors": [
            {"name_fa": "قرمز", "hex": "#FF0000"},
            {"name_fa": "زرشکی", "hex": "#800000"}]},
            {"fa": "آبی‌ها", "colors": [{"name_fa": "آبی", "hex": "#0000FF"}]}]
        flat = flatten_palette(palette)
        self.assertEqual(len(flat), 3)
        self.assertEqual(flat[0]["hex"], "#FF0000")
        self.assertEqual(flat[0]["category_fa"], "قرمزها")
        self.assertEqual(flat[2]["category_fa"], "آبی‌ها")

    def test_color_keyboard_pagination(self):
        colors = [{"name_fa": f"رنگ {i}", "hex": f"#{i:06X}"} for i in range(30)]
        kb0 = build_color_keyboard(colors, page=0, per_page=12)
        rows0 = kb0["inline_keyboard"]
        picks0 = [b for row in rows0 for b in row
                  if b["callback_data"].startswith("single:color:")]
        self.assertEqual(len(picks0), 12)
        nav0 = [b["callback_data"] for row in rows0 for b in row]
        self.assertIn("single:colors:1", nav0)       # next page exists
        self.assertNotIn("single:colors:-1", nav0)
        kb2 = build_color_keyboard(colors, page=2, per_page=12)
        picks2 = [b for row in kb2["inline_keyboard"] for b in row
                  if b["callback_data"].startswith("single:color:")]
        self.assertEqual(len(picks2), 6)             # last page remainder
        nav2 = [b["callback_data"] for row in kb2["inline_keyboard"] for b in row]
        self.assertIn("single:colors:1", nav2)       # previous
        self.assertNotIn("single:colors:3", nav2)    # no next past the end
        # custom hex + back rows are always present
        self.assertIn("single:colorhex", nav2)
        self.assertIn("single:menu", nav2)

    def test_color_keyboard_clamps_page(self):
        colors = [{"name_fa": "x", "hex": "#000000"}]
        kb = build_color_keyboard(colors, page=99)
        picks = [b for row in kb["inline_keyboard"] for b in row
                 if b["callback_data"].startswith("single:color:")]
        self.assertEqual(len(picks), 1)

    def test_font_keyboard_pagination(self):
        fonts = [{"id": f"f{i}", "fa": f"فونت {i}", "persian": i % 2 == 0}
                 for i in range(25)]
        kb0 = build_font_keyboard(fonts, page=0, per_page=8)
        picks = [b for row in kb0["inline_keyboard"] for b in row
                 if b["callback_data"].startswith("single:font:")]
        self.assertEqual(len(picks), 8)
        self.assertEqual(picks[0]["callback_data"], "single:font:f0")
        self.assertIn("(فارسی)", picks[0]["text"])   # f0 is persian=True
        self.assertNotIn("(فارسی)", picks[1]["text"])
        nav = [b["callback_data"] for row in kb0["inline_keyboard"] for b in row]
        self.assertIn("single:fonts:1", nav)
        kb3 = build_font_keyboard(fonts, page=3, per_page=8)
        picks3 = [b for row in kb3["inline_keyboard"] for b in row
                  if b["callback_data"].startswith("single:font:")]
        self.assertEqual(len(picks3), 1)             # 25 = 3*8 + 1

    def test_real_registry_loads(self):
        colors = flatten_palette(load_palette())
        self.assertGreaterEqual(len(colors), 80)
        self.assertTrue(all(c["hex"].startswith("#") for c in colors))
        fonts = load_fonts()
        self.assertGreaterEqual(len(fonts), 20)
        self.assertTrue(all("id" in f and "fa" in f for f in fonts))
        kb = build_color_keyboard(colors, page=0)
        self.assertTrue(kb["inline_keyboard"])


class TestSingleSetCandidates(unittest.TestCase):
    def test_default_order(self):
        names = single_set_candidates(777, "tzemoji_ai")
        self.assertEqual(names, ["tzs_777_by_tzemoji_ai",
                                 "tzs_777_x2_by_tzemoji_ai",
                                 "tzs_777_x3_by_tzemoji_ai"])

    def test_stored_name_first_and_deduped(self):
        names = single_set_candidates(42, "tzemoji_ai", "tzs_42_x2_by_tzemoji_ai")
        self.assertEqual(names[0], "tzs_42_x2_by_tzemoji_ai")
        self.assertEqual(len(names), len(set(names)))
        self.assertIn("tzs_42_by_tzemoji_ai", names)


class TestFontOverrideHook(unittest.TestCase):
    def test_override_roundtrip(self):
        from emoji_forge.animation_engine import (
            FONT_STYLES, choose_font_for_tgs, clear_font_override,
            set_font_override,
        )
        if not os.path.exists(FONT_STYLES["mono"][0]):
            self.skipTest("mono font not installed")
        try:
            set_font_override("mono")
            style, path, _desc = choose_font_for_tgs({}, 0)
            self.assertEqual(style, "mono")
            self.assertEqual(path, FONT_STYLES["mono"][0])
        finally:
            clear_font_override()
        style, _path, _desc = choose_font_for_tgs({}, 0)
        self.assertNotEqual(style, "mono")


class TestSingleProcessRouting(unittest.TestCase):
    def test_name_action_disables_recolor(self):
        brand = BrandProfile(brand_name="MYBRAND", primary_hex="#FF0000")
        with patch("emoji_forge.v5.process_tgs_v5",
                   return_value=(b"out", [], None)) as mock_v5:
            out, _w = process_single(b"data", brand, "name")
        self.assertEqual(out, b"out")
        args, kwargs = mock_v5.call_args
        self.assertEqual(args[1].brand_name, "MYBRAND")
        self.assertFalse(kwargs["recolor"])

    def test_color_action_clears_name_and_recolors(self):
        brand = BrandProfile(brand_name="MYBRAND", primary_hex="#FF0000")
        with patch("emoji_forge.v5.process_tgs_v5",
                   return_value=(b"out", [], None)) as mock_v5:
            process_single(b"data", brand, "color")
        args, kwargs = mock_v5.call_args
        self.assertEqual(args[1].brand_name, "")
        self.assertTrue(kwargs["recolor"])

    def test_font_override_applied_and_cleared(self):
        brand = BrandProfile(brand_name="B")
        with patch("emoji_forge.v5.process_tgs_v5",
                   return_value=(b"out", [], None)), \
             patch("emoji_forge.animation_engine.set_font_override") as mock_set, \
             patch("emoji_forge.animation_engine.clear_font_override") as mock_clear:
            process_single(b"data", brand, "name", font_id="mono")
        mock_set.assert_called_once_with("mono")
        mock_clear.assert_called_once_with()

    def test_logo_action_requires_logo(self):
        with self.assertRaises(ValueError):
            process_single(b"data", BrandProfile(), "logo")

    def test_bad_action_rejected(self):
        with self.assertRaises(ValueError):
            process_single(b"data", BrandProfile(), "explode")


class TestSingleBotFlow(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.bot, self.api, self.sent = make_bot(self.tmp)
        self.api.get_file.return_value = {"file_path": "files/x.tgs"}
        self.api.download_file.return_value = dump_tgs(make_doc())

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def _intake_sticker(self):
        callback(self.bot, "single:start")
        sess = self.bot.session(777)
        self.assertEqual(sess.awaiting, "single_emoji")
        self.bot.handle_message({
            "chat": {"id": 777}, "from": {"id": 777, "first_name": "T"},
            "sticker": {"file_id": "f1", "is_animated": True,
                        "is_video": False, "emoji": "😀", "set_name": ""}})
        return sess

    def test_main_menu_has_single_button(self):
        from emoji_forge.bot import main_menu
        rows = main_menu()["inline_keyboard"]
        # 6 functional rows; the Mini App row was removed (2026-11-01)
        self.assertEqual(len(rows), 6)
        callbacks = [b.get("callback_data") for row in rows for b in row]
        self.assertIn("single:start", callbacks)
        self.assertFalse(any("web_app" in b for row in rows for b in row))

    def test_sticker_intake_shows_action_menu(self):
        sess = self._intake_sticker()
        self.assertIsNotNone(sess.single)
        self.assertEqual(sess.single["emoji"], "😀")
        markups = [m for _c, _t, m in self.sent if m]
        callbacks = [b.get("callback_data") for m in markups
                     for row in m["inline_keyboard"] for b in row]
        self.assertIn("single:act:all", callbacks)
        self.assertIn("single:act:color", callbacks)

    def test_custom_emoji_intake(self):
        callback(self.bot, "single:start")
        self.api.get_custom_emoji_stickers.return_value = [
            {"file_id": "cf1", "is_animated": True, "is_video": False,
             "emoji": "😀"}]
        self.bot.handle_message({
            "chat": {"id": 777}, "from": {"id": 777, "first_name": "T"},
            "text": "😀",
            "entities": [{"type": "custom_emoji", "custom_emoji_id": "55",
                          "offset": 0, "length": 2}]})
        sess = self.bot.session(777)
        self.assertIsNotNone(sess.single)

    def test_video_sticker_rejected_tgs_only(self):
        callback(self.bot, "single:start")
        self.bot.handle_message({
            "chat": {"id": 777}, "from": {"id": 777, "first_name": "T"},
            "sticker": {"file_id": "v1", "is_animated": False,
                        "is_video": True, "emoji": "😀", "set_name": ""}})
        sess = self.bot.session(777)
        self.assertIsNone(sess.single)
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("TGS" in t for t in texts))

    def test_end_to_end_single_edit_creates_personal_set(self):
        sess = self._intake_sticker()
        sess.brand.brand_name = "TEST"
        callback(self.bot, "single:act:color")
        jobs = single_jobs(self.bot)
        self.assertEqual(len(jobs), 1)
        self.api.upload_sticker_file.return_value = {"file_id": "up1"}
        self.api.get_sticker_set.side_effect = Exception(
            "Bad Request: STICKERSET_INVALID")
        self.api.create_new_sticker_set.return_value = True
        with patch("emoji_forge.singles.process_single",
                   return_value=(dump_tgs(make_doc()), [])):
            result = self.bot._run_job(jobs[0])
        self.assertEqual(result, "single published")
        self.api.create_new_sticker_set.assert_called_once()
        args, kwargs = self.api.create_new_sticker_set.call_args
        self.assertEqual(args[1], "tzs_777_by_tzemoji_ai")
        self.assertEqual(args[2], "ایموجی‌های من | TZ Emojis")
        self.assertEqual(kwargs.get("sticker_type"), "custom_emoji")
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("https://t.me/addemoji/tzs_777_by_tzemoji_ai" in t
                            for t in texts))
        self.api.send_sticker.assert_called_once_with(777, "up1")
        rec = self.bot.registry.get(777)
        self.assertEqual(rec["usage"]["emojis_used"], 1)
        self.assertEqual(rec["usage"]["packs_used"], 0)  # no pack slot burned
        self.assertEqual(rec.get("single_set_name"), "tzs_777_by_tzemoji_ai")

    def test_second_edit_adds_to_existing_set(self):
        self.api.upload_sticker_file.return_value = {"file_id": "up2"}
        self.api.get_sticker_set.return_value = {"name": "tzs_777_by_tzemoji_ai"}
        tmp_path = os.path.join(self.tmp, "edited.tgs")
        with open(tmp_path, "wb") as fh:
            fh.write(dump_tgs(make_doc()))
        result = self.bot.deliver_single_to_set(777, 777, tmp_path, "😀")
        self.assertEqual(result["set_name"], "tzs_777_by_tzemoji_ai")
        self.assertEqual(result["link"],
                         "https://t.me/addemoji/tzs_777_by_tzemoji_ai")
        self.api.add_sticker_to_set.assert_called_once()
        self.api.create_new_sticker_set.assert_not_called()

    def test_occupied_name_falls_back_and_is_stored(self):
        self.api.upload_sticker_file.return_value = {"file_id": "up3"}
        self.api.get_sticker_set.side_effect = Exception(
            "Bad Request: STICKERSET_INVALID")
        self.api.create_new_sticker_set.side_effect = [
            Exception("Bad Request: sticker set name is already occupied"),
            True]
        tmp_path = os.path.join(self.tmp, "edited.tgs")
        with open(tmp_path, "wb") as fh:
            fh.write(dump_tgs(make_doc()))
        result = self.bot.deliver_single_to_set(777, 777, tmp_path, "😀")
        self.assertEqual(result["set_name"], "tzs_777_x2_by_tzemoji_ai")
        rec = self.bot.registry.get(777)
        self.assertEqual(rec.get("single_set_name"), "tzs_777_x2_by_tzemoji_ai")

    def test_invalid_output_blocked_before_delivery(self):
        sess = self._intake_sticker()
        callback(self.bot, "single:act:color")
        jobs = single_jobs(self.bot)
        bad = make_doc()
        bad["w"], bad["h"] = 100, 100  # validator blocks non-512 canvas
        with patch("emoji_forge.singles.process_single",
                   return_value=(dump_tgs(bad), [])):
            result = self.bot._run_job(jobs[0])
        self.assertTrue(result.startswith("single validation failed"))
        self.api.upload_sticker_file.assert_not_called()
        self.api.create_new_sticker_set.assert_not_called()
        rec = self.bot.registry.get(777)
        self.assertEqual(rec["usage"]["emojis_used"], 0)

    def test_quota_blocks_single_when_emojis_exhausted(self):
        self.bot.registry.set_quota(
            777, {"packs_total": 3, "emojis_total": 0, "packs_per_day": 1})
        sess = self._intake_sticker()
        callback(self.bot, "single:act:color")
        self.assertEqual(single_jobs(self.bot), [])
        texts = [t for _c, t, _m in self.sent]
        self.assertTrue(any("سهمیه" in t for t in texts))

    def test_font_pick_stores_font_id_on_profile(self):
        sess = self._intake_sticker()
        callback(self.bot, "single:font:mono")
        self.assertEqual(sess.font_id, "mono")
        rec = self.bot.registry.get(777)
        self.assertEqual(rec.get("font_id"), "mono")

    def test_colour_pick_updates_brand(self):
        sess = self._intake_sticker()
        callback(self.bot, "single:color:#FF0000")
        self.assertEqual(sess.brand.primary_hex, "#FF0000")

    def test_web_app_data_single_mode_applies_profile(self):
        payload = {"mode": "single", "name": "برند من", "color": "#00FF00",
                   "font_id": "cute_round", "logo_rect": {"x": 10, "y": 20}}
        self.bot.handle_message({
            "chat": {"id": 777}, "from": {"id": 777, "first_name": "T"},
            "web_app_data": {"data": json.dumps(payload), "button_text": "ذخیره"}})
        sess = self.bot.session(777)
        self.assertEqual(sess.brand.brand_name, "برند من")
        self.assertEqual(sess.brand.primary_hex, "#00FF00")
        self.assertEqual(sess.font_id, "cute_round")
        self.assertEqual(sess.logo_rect, {"x": 10, "y": 20})
        self.assertEqual(sess.awaiting, "single_emoji")
        rec = self.bot.registry.get(777)
        self.assertEqual(rec.get("font_id"), "cute_round")


class TestStudioUrl(unittest.TestCase):
    BASE = "https://studio.example/app"

    def test_query_building_drops_empty_and_encodes(self):
        with patch("emoji_forge.singles._studio_base_url",
                   return_value=self.BASE):
            url = studio_url({"mode": "pack", "pid": "forge-1",
                              "name": "برند من", "color": "#FF0000",
                              "font": ""})
        self.assertTrue(url.startswith(self.BASE + "?"))
        query = parse_qs(urlparse(url).query)
        self.assertEqual(query["mode"], ["pack"])
        self.assertEqual(query["pid"], ["forge-1"])
        self.assertEqual(query["name"], ["برند من"])
        self.assertEqual(query["color"], ["#FF0000"])
        self.assertNotIn("font", query)          # empty value dropped
        self.assertNotIn("برند", url)             # Persian is %-encoded

    def test_no_extra_or_all_empty_returns_base(self):
        with patch("emoji_forge.singles._studio_base_url",
                   return_value=self.BASE):
            self.assertEqual(studio_url(), self.BASE)
            self.assertEqual(studio_url({}), self.BASE)
            self.assertEqual(studio_url({"mode": "", "pid": None}),
                             self.BASE)

    def test_missing_url_file_means_none(self):
        with patch("emoji_forge.singles._studio_base_url",
                   return_value=None):
            self.assertIsNone(studio_url())
            self.assertIsNone(studio_url({"mode": "pack", "pid": "p1"}))
            self.assertIsNone(studio_button_row({"mode": "pack"}))

    def test_button_row_disabled_by_owner_order(self):
        # Mini App removed (2026-11-01): even with a URL configured,
        # no web_app row is ever built.
        with patch("emoji_forge.singles._studio_base_url",
                   return_value=self.BASE):
            self.assertIsNone(studio_button_row({"mode": "pack", "pid": "p9"}))

    def test_single_action_keyboard_has_no_web_app_row(self):
        with patch("emoji_forge.singles._studio_base_url",
                   return_value=self.BASE):
            kb = single_action_keyboard(
                BrandProfile(),
                studio_extra={"mode": "single", "font": "mono"})
        urls = [b["web_app"]["url"] for row in kb["inline_keyboard"]
                for b in row if "web_app" in b]
        self.assertEqual(urls, [])


class TestStudioProjectContinuity(unittest.TestCase):
    """Bot <-> Studio continuity: settings chosen in the Mini App
    land back on the user's own bot project."""

    def setUp(self):
        self.tmp = tempfile.mkdtemp()
        self.bot, self.api, self.sent = make_bot(self.tmp)

    def tearDown(self):
        shutil.rmtree(self.tmp, ignore_errors=True)

    def _texts(self):
        return [t for _c, t, _m in self.sent]

    def _send_wad(self, payload):
        self.bot.handle_message({
            "chat": {"id": 777}, "from": {"id": 777, "first_name": "T"},
            "web_app_data": {"data": json.dumps(payload),
                             "button_text": "ذخیره"}})

    def _project_file(self, pid, name):
        return os.path.join(self.tmp, "data", "projects", pid, name)

    def test_matching_session_project_gets_settings_file(self):
        sess = self.bot.session(777)
        pid = sess.ensure_project()
        payload = {"v": 2, "mode": "pack", "project_id": pid,
                   "name": "برند پروژه", "color": "#123456",
                   "font_id": "mono",
                   "logo_rect": {"x": 1, "y": 2, "w": 3, "h": 4}}
        self._send_wad(payload)
        path = self._project_file(pid, "studio_settings.json")
        self.assertTrue(os.path.exists(path))
        with open(path, encoding="utf-8") as fh:
            self.assertEqual(json.load(fh), payload)  # FULL payload
        # session application: brand + font + rect updated
        self.assertEqual(sess.brand.brand_name, "برند پروژه")
        self.assertEqual(sess.brand.primary_hex, "#123456")
        self.assertEqual(sess.font_id, "mono")
        self.assertEqual(sess.logo_rect, {"x": 1, "y": 2, "w": 3, "h": 4})
        self.assertTrue(any("روی پروژه‌ی فعلی‌تان اعمال شد" in t
                            for t in self._texts()))

    def test_review_pending_names_user_matches(self):
        from emoji_forge.storage import project_dir, write_json
        pid = "forge-20990101-000000-abcdef"
        root = project_dir(self.bot.config.data_dir, pid)
        write_json(os.path.join(root, "review_pending.json"),
                   {"request_id": "r1", "user_id": 777, "project_id": pid})
        sess = self.bot.session(777)
        self.assertNotEqual(sess.project_id, pid)
        self._send_wad({"mode": "pack", "pid": pid,
                        "name": "برند", "color": "#00FF00"})
        self.assertTrue(os.path.exists(
            self._project_file(pid, "studio_settings.json")))
        self.assertTrue(any("روی پروژه‌ی فعلی‌تان اعمال شد" in t
                            for t in self._texts()))

    def test_non_matching_project_untouched(self):
        sess = self.bot.session(777)
        own = sess.ensure_project()
        foreign = "forge-20990101-000000-ffffff"
        self._send_wad({"mode": "pack", "project_id": foreign,
                        "name": "برند دیگر", "color": "#654321"})
        # profile/session application still happened…
        self.assertEqual(sess.brand.brand_name, "برند دیگر")
        # …but no project got a settings file
        self.assertFalse(os.path.exists(
            self._project_file(foreign, "studio_settings.json")))
        self.assertFalse(os.path.exists(
            self._project_file(own, "studio_settings.json")))
        # …and the user is told plainly
        self.assertTrue(any("یکی نیست" in t for t in self._texts()))
        self.assertFalse(any("روی پروژه‌ی فعلی‌تان اعمال شد" in t
                             for t in self._texts()))
        self.assertEqual(sess.project_id, own)

    def test_pack_completion_has_no_miniapp_offer(self):
        # Mini App removed by owner order (2026-11-01): pack
        # completion must NOT offer or mention any web app.
        sess = self.bot.session(777)
        pid = sess.ensure_project()
        sess.manifest = {"project_id": pid, "set_name": "mypack",
                         "set_title": "My Pack", "count": 0, "items": []}
        self.bot._download_manifest_items(sess, 777)
        self.assertTrue(any("دریافت پک تمام شد" in t for t in self._texts()))
        self.assertFalse(any("استودیو" in t or "مینی‌اپ" in t
                             for t in self._texts()))
        markups = [m for _c, _t, m in self.sent if m]
        urls = [b["web_app"]["url"] for m in markups
                for row in m["inline_keyboard"] for b in row
                if "web_app" in b]
        self.assertEqual(urls, [])


if __name__ == "__main__":
    unittest.main()
