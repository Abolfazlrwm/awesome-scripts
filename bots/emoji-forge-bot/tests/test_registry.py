"""Tests for the shared FONTS + COLOURS registry (registry.py) and
its wiring into animation_engine: every registered font file must
exist and really load, the palette must stay wide and unique, and
the module-level font override must win over — then restore — the
default cute_bold choice.
"""
import os
import re
import sys
import unittest

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "src"))

from fontTools.ttLib import TTFont

from emoji_forge import registry
from emoji_forge.animation_engine import (
    FONT_STYLES, choose_font_for_tgs, clear_font_override, set_font_override,
)

LATIN = [ord(c) for c in
         "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"]
HEX_RE = re.compile(r"^#[0-9A-Fa-f]{6}$")


class TestRegistryFonts(unittest.TestCase):
    def test_first_two_are_the_cute_pair(self):
        self.assertEqual(registry.FONTS[0]["id"], "cute_bold")
        self.assertEqual(registry.FONTS[1]["id"], "cute_round")

    def test_ids_unique(self):
        ids = [entry["id"] for entry in registry.FONTS]
        self.assertEqual(len(ids), len(set(ids)))

    def test_every_font_file_exists_and_loads(self):
        for entry in registry.FONTS:
            with self.subTest(font=entry["id"]):
                path = registry.font_by_id(entry["id"])
                self.assertIsNotNone(path, f"{entry['id']}: file missing")
                font = TTFont(path)
                try:
                    cmap = font.getBestCmap()
                finally:
                    font.close()
                missing = [chr(c) for c in LATIN if c not in cmap]
                self.assertEqual(missing, [],
                                 f"{entry['id']}: missing glyphs {missing}")

    def test_persian_fonts_cover_arabic_block(self):
        persian = [e for e in registry.FONTS if e["persian"]]
        self.assertGreaterEqual(len(persian), 7)
        for entry in persian:
            with self.subTest(font=entry["id"]):
                font = TTFont(registry.font_by_id(entry["id"]))
                try:
                    cmap = font.getBestCmap()
                finally:
                    font.close()
                covered = sum(1 for cp in range(0x0600, 0x0700)
                              if cp in cmap)
                self.assertGreaterEqual(
                    covered, 40,
                    f"{entry['id']}: only {covered} codepoints in "
                    "U+0600-U+06FF")

    def test_font_by_id_unknown_returns_none(self):
        self.assertIsNone(registry.font_by_id("no_such_font"))
        self.assertIsNone(registry.font_by_id(None))
        self.assertIsNone(registry.font_by_id(""))


class TestRegistryPalette(unittest.TestCase):
    def test_palette_at_least_72_unique_hexes(self):
        flat = registry.palette_flat()
        self.assertGreaterEqual(len(flat), 72)
        hexes = [hex_ for _name, hex_ in flat]
        self.assertEqual(len(hexes), len(set(hexes)),
                         "palette hexes must be unique")

    def test_palette_entries_well_formed(self):
        for name_fa, hex_ in registry.palette_flat():
            self.assertTrue(name_fa)
            self.assertRegex(hex_, HEX_RE)


class TestFontOverride(unittest.TestCase):
    def tearDown(self):
        clear_font_override()

    def test_registry_fonts_extended_font_styles(self):
        for entry in registry.FONTS:
            self.assertIn(entry["id"], FONT_STYLES)
        # the six system fallbacks are still there
        for style in ("bold_sans", "display", "mono", "serif",
                      "liberation_sans", "noto_serif"):
            self.assertIn(style, FONT_STYLES)

    def test_default_choice_is_cute_bold(self):
        clear_font_override()
        style, path, _desc = choose_font_for_tgs({})
        self.assertEqual(style, "cute_bold")
        self.assertEqual(os.path.normpath(path),
                         registry.font_by_id("cute_bold"))

    def test_override_wins_then_clear_restores(self):
        set_font_override("cute_round")
        style, path, _desc = choose_font_for_tgs({})
        self.assertEqual(style, "cute_round")
        self.assertEqual(os.path.normpath(path),
                         registry.font_by_id("cute_round"))
        set_font_override("vazirmatn")
        style, path, _desc = choose_font_for_tgs({})
        self.assertEqual(style, "vazirmatn")
        self.assertEqual(os.path.normpath(path),
                         registry.font_by_id("vazirmatn"))
        clear_font_override()
        style, _path, _desc = choose_font_for_tgs({})
        self.assertEqual(style, "cute_bold")

    def test_unknown_override_falls_back_to_default(self):
        set_font_override("no_such_font")
        style, _path, _desc = choose_font_for_tgs({})
        self.assertEqual(style, "cute_bold")


if __name__ == "__main__":
    unittest.main()
