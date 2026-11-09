"""v10.1 — custom OpenAI-compatible provider (AI_API_KEY / AI_BASE_URL /
AI_MODEL). All HTTP is mocked: these tests prove URL joining, the auth
header, model passthrough, selection priority and the graceful rule
fallback. They never prove a live endpoint works."""
import os
import sys
import unittest
from types import SimpleNamespace
from unittest.mock import MagicMock, patch

sys.path.insert(0, os.path.join(os.path.dirname(os.path.dirname(
    os.path.abspath(__file__))), "src"))

from emoji_forge.brain import OpenAICompatibleBrain, RuleBrain, get_brain
from emoji_forge.config import load_config

KEY = "TESTKEY-abc123"


def _ok_response(text="حالت branding پیشنهاد می‌شود."):
    fake = MagicMock()
    fake.raise_for_status.return_value = None
    fake.json.return_value = {"choices": [{"message": {"content": text}}]}
    return fake


class TestChatUrlJoining(unittest.TestCase):
    def test_base_without_trailing_slash(self):
        brain = OpenAICompatibleBrain(KEY, "https://api.example.com/v1", "m")
        self.assertEqual(brain.chat_url,
                         "https://api.example.com/v1/chat/completions")

    def test_base_with_trailing_slash(self):
        brain = OpenAICompatibleBrain(KEY, "https://api.example.com/v1/", "m")
        self.assertEqual(brain.chat_url,
                         "https://api.example.com/v1/chat/completions")

    def test_defaults_applied(self):
        brain = OpenAICompatibleBrain(KEY)
        self.assertEqual(brain.chat_url,
                         "https://api.openai.com/v1/chat/completions")
        self.assertEqual(brain.model, "gpt-4o-mini")


class TestDecideMockedHttp(unittest.TestCase):
    def test_auth_header_and_model_passthrough(self):
        brain = OpenAICompatibleBrain(KEY, "https://openrouter.ai/api/v1",
                                      "some/custom-model")
        with patch("requests.post", return_value=_ok_response()) as post:
            d = brain.decide({"kind": "analyze", "user_text": "لوگو بذار"})
        self.assertEqual(d.source, "openai-compatible")
        self.assertEqual(d.mode, "branding")
        args, kwargs = post.call_args
        self.assertEqual(args[0], "https://openrouter.ai/api/v1/chat/completions")
        self.assertEqual(kwargs["headers"]["Authorization"], f"Bearer {KEY}")
        self.assertEqual(kwargs["json"]["model"], "some/custom-model")
        self.assertNotIn(KEY, d.message_fa)

    def test_network_error_falls_back_to_rules(self):
        brain = OpenAICompatibleBrain(KEY, "https://example.invalid/v1", "m")
        with patch("requests.post", side_effect=ConnectionError(f"down {KEY}")):
            d = brain.decide({"kind": "analyze", "user_text": "لوگو را پاک کن"})
        self.assertEqual(d.source, "rule")
        self.assertEqual(d.mode, "clean")  # rule heuristic still applied
        self.assertNotIn(KEY, d.message_fa)
        self.assertNotIn(KEY, " ".join(d.warnings))

    def test_http_error_falls_back_to_rules(self):
        brain = OpenAICompatibleBrain(KEY, "https://example.invalid/v1", "m")
        fake = MagicMock()
        fake.raise_for_status.side_effect = RuntimeError("500 Server Error")
        with patch("requests.post", return_value=fake):
            d = brain.decide({"kind": "analyze", "user_text": "x"})
        self.assertEqual(d.source, "rule")

    def test_empty_completion_falls_back_to_rules(self):
        brain = OpenAICompatibleBrain(KEY, "https://example.invalid/v1", "m")
        with patch("requests.post", return_value=_ok_response("   ")):
            d = brain.decide({"kind": "analyze", "user_text": "x"})
        self.assertEqual(d.source, "rule")

    def test_no_key_never_calls_http(self):
        brain = OpenAICompatibleBrain("", "https://example.invalid/v1", "m")
        ok, _ = brain.available()
        self.assertFalse(ok)
        with patch("requests.post") as post:
            d = brain.decide({"kind": "analyze", "user_text": "x"})
        post.assert_not_called()
        self.assertEqual(d.source, "rule")


class TestSelectionPriority(unittest.TestCase):
    def _config(self, **kw):
        base = dict(brain_provider="rule", external_brain_url="",
                    external_brain_key="", ai_api_key="", ai_base_url="",
                    ai_model="", data_dir="/tmp")
        base.update(kw)
        return SimpleNamespace(**base)

    def test_key_wins_over_bridge_and_external(self):
        cfg = self._config(brain_provider="muse-bridge", ai_api_key=KEY,
                           ai_base_url="https://x.example/v1", ai_model="mm")
        brain = get_brain(cfg)
        self.assertIsInstance(brain, OpenAICompatibleBrain)
        self.assertEqual(brain.model, "mm")
        cfg2 = self._config(brain_provider="external",
                            external_brain_url="https://e.example/v1",
                            external_brain_key="k", ai_api_key=KEY)
        self.assertIsInstance(get_brain(cfg2), OpenAICompatibleBrain)

    def test_without_key_existing_behaviour_unchanged(self):
        self.assertIsInstance(get_brain(self._config()), RuleBrain)
        from emoji_forge.brain import ExternalBrain
        cfg = self._config(brain_provider="external",
                           external_brain_url="https://e.example/v1",
                           external_brain_key="k")
        self.assertIsInstance(get_brain(cfg), ExternalBrain)


class TestConfigEnv(unittest.TestCase):
    def test_load_config_reads_ai_env(self):
        cfg = load_config({"AI_API_KEY": "k1", "AI_BASE_URL": "https://or.example/v1",
                           "AI_MODEL": "m-9", "EMOJI_FORGE_TOKEN": "1:x"})
        self.assertEqual(cfg.ai_api_key, "k1")
        self.assertEqual(cfg.ai_base_url, "https://or.example/v1")
        self.assertEqual(cfg.ai_model, "m-9")

    def test_load_config_ai_defaults(self):
        cfg = load_config({"EMOJI_FORGE_TOKEN": "1:x"})
        self.assertEqual(cfg.ai_api_key, "")
        self.assertEqual(cfg.ai_base_url, "https://api.openai.com/v1")
        self.assertEqual(cfg.ai_model, "gpt-4o-mini")


if __name__ == "__main__":
    unittest.main()
