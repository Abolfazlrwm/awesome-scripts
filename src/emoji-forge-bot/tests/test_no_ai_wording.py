"""NO-AI rule (owner order, 2026-11-01): users must never see any
mention of AI in the bot. These tests scan the STRING LITERALS of
the user-facing modules (via AST, so code identifiers and internal
plumbing names do not false-positive) for the banned tokens, and
check the live text builders + brain messages too."""
import ast
import os
import re
import unittest

SRC = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
                   "src", "emoji_forge")

BANNED_SUBSTRINGS = [
    "Ai Agent", "هوش مصنوعی", "متی", "جعفر", "مغز",
    "بررسی دستی", "بررسی شخصی", "بازبینی دستی", "manual review",
    "Muse", "Muse",
]
# Standalone Latin "AI"/"Ai" as a word (not inside a longer word).
AI_WORD = re.compile(r"(?<![A-Za-z])AI(?![A-Za-z])|(?<![A-Za-z])Ai(?![A-Za-z])")


def _string_literals(path):
    tree = ast.parse(open(path, encoding="utf-8").read())
    out = []
    for node in ast.walk(tree):
        if isinstance(node, ast.Constant) and isinstance(node.value, str):
            out.append(node.value)
    return out


class TestNoAiWording(unittest.TestCase):
    def _scan(self, module):
        bad = []
        for lit in _string_literals(os.path.join(SRC, module)):
            for token in BANNED_SUBSTRINGS:
                if token in lit:
                    bad.append((token, lit[:80]))
            if AI_WORD.search(lit):
                bad.append(("AI-word", lit[:80]))
        return bad

    def test_bot_literals_clean(self):
        self.assertEqual(self._scan("bot.py"), [])

    def test_branding_literals_clean(self):
        self.assertEqual(self._scan("branding.py"), [])

    def test_singles_literals_clean(self):
        self.assertEqual(self._scan("singles.py"), [])

    def test_brain_user_messages_clean(self):
        from emoji_forge.brain import ExternalBrain, MuseBridgeBrain, RuleBrain
        texts = []
        ok, reason = RuleBrain().available()
        texts.append(reason)
        d = RuleBrain().decide({"kind": "analyze", "user_text": "لوگو را پاک کن"})
        texts += [d.message_fa, d.suggestion_fa]
        ok, reason = ExternalBrain().available()
        texts.append(reason)
        import tempfile
        with tempfile.TemporaryDirectory() as tmp:
            bridge = MuseBridgeBrain(tmp)
            ok, reason = bridge.available()
            texts.append(reason)
            d = bridge.decide({"kind": "analyze", "user_text": "سلام"})
            texts += [d.message_fa, d.suggestion_fa] + list(d.warnings)
        for text in texts:
            for token in BANNED_SUBSTRINGS:
                self.assertNotIn(token, text)
            self.assertIsNone(AI_WORD.search(text), text)

    def test_bot_literals_have_no_miniapp_reference(self):
        # Mini App removed by owner order (2026-11-01): no bot text
        # may mention or offer it anywhere.
        tokens = ["استودیو", "مینی‌اپ", "مینی اپ", "Mini App", "Studio"]
        bad = []
        for lit in _string_literals(os.path.join(SRC, "bot.py")):
            for token in tokens:
                if token in lit:
                    bad.append((token, lit[:80]))
        self.assertEqual(bad, [])

    def test_menus_have_no_web_app_buttons(self):
        from emoji_forge.bot import main_menu
        from emoji_forge.brand import BrandProfile
        from emoji_forge.singles import single_action_keyboard
        for kb in (main_menu(), main_menu(is_admin=True),
                   single_action_keyboard(BrandProfile())):
            for row in kb["inline_keyboard"]:
                for b in row:
                    self.assertNotIn("web_app", b)
                    self.assertNotIn("استودیو", b.get("text", ""))

    def test_profile_and_help_texts_clean(self):
        from emoji_forge import branding
        texts = [branding.BOT_NAME, branding.BOT_SHORT_DESCRIPTION,
                 branding.BOT_DESCRIPTION, branding.welcome_text(),
                 branding.help_text(), branding.pending_welcome_text(),
                 branding.OWNER_CREDIT_FA]
        texts += [c["description"] for c in branding.BOT_COMMANDS]
        for text in texts:
            for token in BANNED_SUBSTRINGS:
                self.assertNotIn(token, text)
            self.assertIsNone(AI_WORD.search(text), text)


if __name__ == "__main__":
    unittest.main()
