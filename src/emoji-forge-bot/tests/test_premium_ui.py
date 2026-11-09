"""Premium UI (Bot API 9.4): coloured buttons + custom emojis.

Every shipped custom_emoji_id must exist verbatim in the owner's
harvested ID sources (imperium curated map / full pool) — an
invented id fails this suite.
"""
import json
import os
import re
import sys
import unittest

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from emoji_forge.premium import (
    DANGER, PREMIUM, PRIMARY, STYLES, SUCCESS, btn, icon, pe,
    strip_icons, strip_leading_emoji, strip_premium_tags,
)

IMPERIUM_CURATED = os.path.expanduser(
    "~/workspace/imperium/app/data/premium_emoji_ids.json")
IMPERIUM_POOL = os.path.expanduser(
    "~/workspace/imperium/data_harvest/pool.json")


def _all_source_ids() -> set:
    ids = set()
    with open(IMPERIUM_CURATED, encoding="utf-8") as fh:
        for entry in json.load(fh).values():
            ids.add(str(entry["id"]))
    with open(IMPERIUM_POOL, encoding="utf-8") as fh:
        for entries in json.load(fh).values():
            for entry in entries:
                ids.add(str(entry["id"]))
    return ids


def _flat(markup):
    return [b for row in markup["inline_keyboard"] for b in row]


class TestBtnBuilder(unittest.TestCase):
    def test_plain_button_has_no_visual_fields(self):
        b = btn("متن", callback_data="x:y")
        self.assertEqual(b, {"text": "متن", "callback_data": "x:y"})

    def test_style_and_icon_emitted_only_when_set(self):
        b = btn("متن", callback_data="x:y", style=PRIMARY, icon="check")
        self.assertEqual(b["style"], "primary")
        self.assertEqual(b["icon_custom_emoji_id"], PREMIUM["check"]["id"])
        b2 = btn("متن", url="https://example.com", style=SUCCESS)
        self.assertNotIn("icon_custom_emoji_id", b2)
        self.assertNotIn("callback_data", b2)

    def test_icon_accepts_raw_id(self):
        raw = PREMIUM["fire"]["id"]
        self.assertEqual(btn("x", icon=raw)["icon_custom_emoji_id"], raw)

    def test_invalid_style_rejected(self):
        with self.assertRaises(ValueError):
            btn("x", callback_data="y", style="purple")

    def test_web_app_passthrough(self):
        b = btn("x", web_app={"url": "https://example.com"}, style=PRIMARY,
                icon="studio")
        self.assertEqual(b["web_app"], {"url": "https://example.com"})
        self.assertEqual(b["style"], PRIMARY)


class TestPremiumIdsAreReal(unittest.TestCase):
    def test_every_shipped_id_exists_in_imperium_sources(self):
        if not (os.path.exists(IMPERIUM_CURATED) and os.path.exists(IMPERIUM_POOL)):
            self.skipTest("imperium ID sources not present on this machine")
        known = _all_source_ids()
        self.assertGreaterEqual(len(PREMIUM), 20)
        for key, entry in PREMIUM.items():
            self.assertIn(entry["id"], known,
                          f"premium id for {key!r} not in the verified sources")
            self.assertTrue(entry["alt"], f"empty alt for {key!r}")

    def test_icon_helper(self):
        self.assertEqual(icon("brand"), PREMIUM["brand"]["id"])
        self.assertIsNone(icon("no-such-key"))


class TestPeAndStrip(unittest.TestCase):
    def test_pe_output_shape(self):
        out = pe("brand")
        m = re.fullmatch(
            r'<tg-emoji emoji-id="(\d+)">(.+)</tg-emoji>', out)
        self.assertIsNotNone(m)
        self.assertEqual(m.group(1), PREMIUM["brand"]["id"])
        self.assertEqual(m.group(2), PREMIUM["brand"]["alt"])
        self.assertEqual(pe("no-such-key"), "")

    def test_strip_premium_tags_reduces_to_alt(self):
        text = f"سلام {pe('fire')} و {pe('check')}!"
        self.assertEqual(strip_premium_tags(text), "سلام 🔥 و ✅!")

    def test_strip_icons_removes_only_icons(self):
        markup = {"inline_keyboard": [[
            {"text": "a", "callback_data": "a", "style": "danger",
             "icon_custom_emoji_id": "123"},
            {"text": "b", "callback_data": "b", "style": "primary"},
        ]]}
        cleaned = strip_icons(markup)
        for b in _flat(cleaned):
            self.assertNotIn("icon_custom_emoji_id", b)
        self.assertEqual(_flat(cleaned)[0]["style"], "danger")
        # original untouched (deep copy)
        self.assertEqual(_flat(markup)[0]["icon_custom_emoji_id"], "123")
        self.assertIsNone(strip_icons(None))


class TestKeyboardsCarryTheLook(unittest.TestCase):
    def test_main_admin_and_singles_cover_all_three_styles(self):
        from emoji_forge.bot import admin_user_keyboard, main_menu
        from emoji_forge.brand import BrandProfile
        from emoji_forge.singles import (
            build_color_keyboard, flatten_palette, load_palette,
            single_action_keyboard,
        )
        markups = [
            main_menu(is_admin=True),
            admin_user_keyboard(42),
            build_color_keyboard(flatten_palette(load_palette())),
            single_action_keyboard(BrandProfile()),
        ]
        styles = {b.get("style") for m in markups for b in _flat(m) if b.get("style")}
        self.assertTrue({PRIMARY, SUCCESS, DANGER} <= styles,
                        f"styles present: {styles}")
        for style in styles:
            self.assertIn(style, STYLES)

    def test_main_ctas_have_icons(self):
        """Restrained look: semantic icons on the buttons whose
        meaning matches exactly, colour only on the ONE main CTA."""
        from emoji_forge.bot import main_menu
        flat = _flat(main_menu())
        by_callback = {b.get("callback_data"): b for b in flat}
        for cb in ("cmd:publish", "single:start", "cmd:preview",
                   "menu:brand", "packs:mine"):
            self.assertIn("icon_custom_emoji_id", by_callback[cb], cb)
            self.assertNotIn("style", by_callback[cb], cb)
        # پردازش کامل: the single PRIMARY, deliberately icon-free
        self.assertEqual(by_callback["cmd:process"].get("style"), PRIMARY)
        self.assertNotIn("icon_custom_emoji_id", by_callback["cmd:process"])
        self.assertEqual(by_callback["cmd:cancel"].get("style"), DANGER)
        # known mismatches from the screenshot must stay icon-free
        for cb in ("menu:region", "menu:tgs", "cmd:export"):
            self.assertNotIn("icon_custom_emoji_id", by_callback[cb], cb)

    def test_callback_data_unchanged_shape(self):
        """Visual fields only: every main-menu button still carries
        exactly the callback_data it had before the restyle."""
        from emoji_forge.bot import main_menu
        callbacks = {b.get("callback_data") for b in _flat(main_menu())
                     if b.get("callback_data")}
        self.assertTrue({
            "cmd:preview", "cmd:process", "menu:brand", "single:start",
            "menu:region", "menu:tgs", "cmd:export", "cmd:publish",
            "menu:advanced", "cmd:status", "packs:mine", "cmd:cancel",
        } <= callbacks)


# Any pictograph/symbol char that would count as "an emoji in the
# label" next to a premium icon (the double-visual from the owner's
# screenshot). Persian letters/digits are outside these ranges.
_EMOJI_CHAR_RE = re.compile(
    "[\U0001F000-\U0001FAFF\u2190-\u2BFF\u2300-\u23FF"
    "\u2600-\u27BF\u3030\u303D\u3297\u3299\uFE0F]"
)


def _all_keyboards():
    from emoji_forge.bot import (
        Session, admin_menu, admin_user_keyboard, advanced_menu,
        brand_menu, main_menu,
    )
    from emoji_forge.brand import BrandProfile
    from emoji_forge.singles import (
        build_color_keyboard, build_font_keyboard,
        build_gradient_keyboard, flatten_palette, load_gradients,
        load_palette, single_action_keyboard,
    )
    sess = Session("/tmp")
    return {
        "main": main_menu(),
        "main_admin": main_menu(is_admin=True),
        "admin": admin_menu(),
        "admin_user": admin_user_keyboard(42),
        "brand": brand_menu(),
        "advanced": advanced_menu(sess),
        "single_action": single_action_keyboard(BrandProfile()),
        "colors": build_color_keyboard(flatten_palette(load_palette())),
        "gradients": build_gradient_keyboard(load_gradients()),
        "fonts": build_font_keyboard(
            [{"id": "vazirmatn", "fa": "وزیرمتن", "persian": True}]),
    }


class TestCleanLook(unittest.TestCase):
    """The restrained-design rules from the owner's screenshot
    review («خیلی زشته»): one visual per button, scarce colour,
    no icon-only buttons."""

    def test_no_button_has_both_icon_and_emoji_in_text(self):
        for name, markup in _all_keyboards().items():
            for b in _flat(markup):
                if b.get("icon_custom_emoji_id"):
                    self.assertIsNone(
                        _EMOJI_CHAR_RE.search(b.get("text", "")),
                        f"{name}: double visual on {b.get('text')!r}")

    def test_btn_auto_strips_leading_emoji_when_icon_set(self):
        b = btn("✅ تأیید", callback_data="x", icon="check")
        self.assertEqual(b["text"], "تأیید")
        self.assertIn("icon_custom_emoji_id", b)
        # no icon -> label untouched (single emoji in text is fine)
        b2 = btn("✅ پردازش کامل", callback_data="x", style=PRIMARY)
        self.assertEqual(b2["text"], "✅ پردازش کامل")
        self.assertEqual(strip_leading_emoji("⬅️ بازگشت"), "بازگشت")
        self.assertEqual(strip_leading_emoji("🧑‍💼 بررسی"), "بررسی")
        self.assertEqual(strip_leading_emoji("۱۵٪"), "۱۵٪")
        self.assertEqual(strip_leading_emoji("متن ساده"), "متن ساده")
        self.assertEqual(strip_leading_emoji(""), "")

    def test_main_menu_colour_restraint(self):
        flat = _flat(_all_keyboards()["main_admin"])
        primaries = [b for b in flat if b.get("style") == PRIMARY]
        styled = [b for b in flat if b.get("style")]
        self.assertLessEqual(len(primaries), 1, primaries)
        self.assertLessEqual(len(styled), 2, styled)

    def test_main_menu_no_icon_only_buttons(self):
        for name in ("main", "main_admin"):
            for b in _flat(_all_keyboards()[name]):
                text = b.get("text", "")
                self.assertTrue(
                    any(ch.isalpha() for ch in text),
                    f"{name}: icon-only/label-less button {b!r}")

    def test_welcome_help_single_clean_premium_emoji(self):
        """Exactly one premium emoji in /start and /help HTML, placed
        BEFORE «تازه‌های نسخه ۱۰»; bullets carry none."""
        from emoji_forge.branding import help_html, welcome_html
        for text in (welcome_html(), help_html()):
            self.assertEqual(text.count("<tg-emoji"), 1)
            self.assertLess(text.index("<tg-emoji"),
                            text.index("تازه‌های نسخه ۱۰"))
            self.assertNotIn("📦", text)


class TestCosmeticFallback(unittest.TestCase):
    """telegram_api: a custom-emoji/icon rejection retries once,
    stripped — a cosmetic field never breaks a functional send."""

    def _api_with_fake_call(self, failures_before_success=1):
        from emoji_forge.telegram_api import TelegramApi
        api = TelegramApi.__new__(TelegramApi)
        api._token = "123:fake"
        api.calls = []

        def fake_call(method, payload=None, files=None, retries=4):
            api.calls.append((method, dict(payload or {})))
            if len(api.calls) <= failures_before_success:
                from emoji_forge.errors import ExternalError
                raise ExternalError(
                    "تلگرام خطا داد: Bad Request: CUSTOM_EMOJI_INVALID "
                    "or icon_custom_emoji_id invalid")
            return {"message_id": 7}

        api._call = fake_call
        return api

    def test_send_message_retries_stripped_once(self):
        api = self._api_with_fake_call()
        markup = {"inline_keyboard": [[
            {"text": "x", "callback_data": "y",
             "icon_custom_emoji_id": PREMIUM["check"]["id"]}]]}
        result = api.send_message(
            1, f"سلام {pe('fire')}", reply_markup=markup, parse_mode="HTML")
        self.assertEqual(result, {"message_id": 7})
        self.assertEqual(len(api.calls), 2)
        second = api.calls[1][1]
        self.assertNotIn("parse_mode", second)
        self.assertIn("🔥", second["text"])
        self.assertNotIn("tg-emoji", second["text"])
        sent_markup = json.loads(second["reply_markup"])
        self.assertNotIn("icon_custom_emoji_id",
                         sent_markup["inline_keyboard"][0][0])

    def test_non_cosmetic_error_is_not_retried(self):
        from emoji_forge.errors import ExternalError
        from emoji_forge.telegram_api import TelegramApi
        api = TelegramApi.__new__(TelegramApi)
        api._token = "123:fake"
        api.calls = []

        def fake_call(method, payload=None, files=None, retries=4):
            api.calls.append(method)
            raise ExternalError("تلگرام خطا داد: chat not found")

        api._call = fake_call
        with self.assertRaises(ExternalError):
            api.send_message(1, "سلام")
        self.assertEqual(len(api.calls), 1)


if __name__ == "__main__":
    unittest.main()
