from __future__ import annotations

import os
import runpy
from pathlib import Path

from dotenv import load_dotenv


os.chdir(Path(__file__).resolve().parent)
load_dotenv()

target = "main.py" if os.getenv("SESSION_STRING", "").strip() else "bootstrap.py"
runpy.run_path(target, run_name="__main__")
