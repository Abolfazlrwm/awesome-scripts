import ast
import unittest
from pathlib import Path


PROJECT_DIR = Path(__file__).resolve().parents[1]
SOURCE_PATHS = sorted(PROJECT_DIR.glob("*.py"))
SOURCES = {path: path.read_text(encoding="utf-8") for path in SOURCE_PATHS}
SOURCE = "\n".join(SOURCES.values())
TREES = {path: ast.parse(source) for path, source in SOURCES.items()}


class VoiceCallSafetyTests(unittest.TestCase):
    def test_project_never_calls_create_group_call(self) -> None:
        self.assertNotIn("create_group_call", SOURCE)

    def test_group_call_config_disables_auto_start(self) -> None:
        safe_configs = [
            node
            for tree in TREES.values()
            for node in ast.walk(tree)
            if isinstance(node, ast.Call)
            and isinstance(node.func, ast.Name)
            and node.func.id == "GroupCallConfig"
            and any(
                keyword.arg == "auto_start"
                and isinstance(keyword.value, ast.Constant)
                and keyword.value.value is False
                for keyword in node.keywords
            )
        ]
        self.assertTrue(safe_configs, "GroupCallConfig(auto_start=False) is required")

    def test_every_play_call_has_explicit_config(self) -> None:
        play_calls = []
        for tree in TREES.values():
            play_calls.extend(
                node
                for node in ast.walk(tree)
                if isinstance(node, ast.Call)
                and isinstance(node.func, ast.Attribute)
                and node.func.attr == "play"
                and (
                    isinstance(node.func.value, ast.Name)
                    and node.func.value.id == "calls"
                    or isinstance(node.func.value, ast.Attribute)
                    and node.func.value.attr == "calls"
                )
            )
        self.assertTrue(play_calls)
        for call in play_calls:
            self.assertTrue(
                any(keyword.arg == "config" for keyword in call.keywords),
                f"calls.play on line {call.lineno} has no explicit safe config",
            )


if __name__ == "__main__":
    unittest.main()
