"""Configuration only from environment variables. No secret is ever hard-coded."""
import os
from dataclasses import dataclass, field


@dataclass
class Config:
    token: str = ""
    admin_ids: set = field(default_factory=set)
    data_dir: str = ""
    max_concurrent: int = 2
    max_file_bytes: int = 20 * 1024 * 1024  # Bot API getFile practical limit
    poll_timeout: int = 30
    brain_provider: str = "rule"  # rule | muse-bridge | external (optional)
    external_brain_url: str = ""
    external_brain_key: str = ""
    # Custom OpenAI-compatible provider (v10.1): when ai_api_key is set it
    # takes priority over brain_provider. Owner/infra config only, from
    # environment variables AI_API_KEY / AI_BASE_URL / AI_MODEL.
    ai_api_key: str = ""
    ai_base_url: str = "https://api.openai.com/v1"
    ai_model: str = "gpt-4o-mini"

    @property
    def has_token(self) -> bool:
        return bool(self.token and ":" in self.token)


def load_config(env=None) -> Config:
    env = os.environ if env is None else env
    admins = set()
    for part in (env.get("EMOJI_FORGE_ADMIN_IDS") or "").split(","):
        part = part.strip()
        if part.lstrip("-").isdigit():
            admins.add(int(part))
    base = env.get("EMOJI_FORGE_DATA_DIR") or os.path.join(
        os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))),
        "data",
    )
    try:
        max_conc = int(env.get("EMOJI_FORGE_MAX_CONCURRENT") or 2)
    except ValueError:
        max_conc = 2
    try:
        max_bytes = int(env.get("EMOJI_FORGE_MAX_FILE_BYTES") or 20 * 1024 * 1024)
    except ValueError:
        max_bytes = 20 * 1024 * 1024
    return Config(
        token=(env.get("EMOJI_FORGE_TOKEN") or "").strip(),
        admin_ids=admins,
        data_dir=base,
        max_concurrent=max(1, min(4, max_conc)),
        max_file_bytes=max_bytes,
        brain_provider=(env.get("EMOJI_FORGE_BRAIN_PROVIDER") or "rule").strip().lower(),
        external_brain_url=(env.get("EMOJI_FORGE_EXTERNAL_BRAIN_URL") or "").strip(),
        external_brain_key=(env.get("EMOJI_FORGE_EXTERNAL_BRAIN_KEY") or "").strip(),
        ai_api_key=(env.get("AI_API_KEY") or "").strip(),
        ai_base_url=(env.get("AI_BASE_URL") or "https://api.openai.com/v1").strip(),
        ai_model=(env.get("AI_MODEL") or "gpt-4o-mini").strip(),
    )


def load_dotenv(path: str) -> None:
    """Load a local .env file (never committed) into os.environ if present."""
    try:
        with open(path, encoding="utf-8") as fh:
            for line in fh:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                key, value = line.split("=", 1)
                os.environ.setdefault(key.strip(), value.strip())
    except OSError:
        pass
