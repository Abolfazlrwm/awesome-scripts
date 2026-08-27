"""
Desktop Automation Demo (PyAutoGUI)
------------------------------------
A small, safe, configurable command-line tool that demonstrates basic
desktop automation: auto-typing text, taking a screenshot, and clicking
at a given position.

Usage examples:
    python automation_demo.py
    python automation_demo.py --message "Hello!" --delay 3 --no-click
    python automation_demo.py --click-x 500 --click-y 300 --screenshot out.png

Safety:
    PyAutoGUI's fail-safe stays ON: drag the mouse to any screen corner
    to immediately abort the script.
"""

import argparse
import logging
import sys
import time
from pathlib import Path
from typing import Optional

import pyautogui

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%H:%M:%S",
)
log = logging.getLogger("automation_demo")


def countdown(seconds: int) -> None:
    """Give the user time to switch to the target window before anything happens."""
    log.info("Starting in %d seconds - switch to the target window now.", seconds)
    for remaining in range(seconds, 0, -1):
        print(f"\r  {remaining}...", end="", flush=True)
        time.sleep(1)
    print("\r  go!      ")


def type_message(message: str, interval: float) -> None:
    log.info("Typing message (%d chars)...", len(message))
    pyautogui.write(message, interval=interval)


def take_screenshot(path: Path) -> Path:
    path.parent.mkdir(parents=True, exist_ok=True)
    screenshot = pyautogui.screenshot()
    screenshot.save(path)
    log.info("Screenshot saved to %s", path)
    return path


def click_at(x: Optional[int], y: Optional[int]) -> None:
    if x is None or y is None:
        width, height = pyautogui.size()
        x, y = width // 2, height // 2
        log.info("No coordinates given, using screen center (%d, %d)", x, y)
    pyautogui.click(x, y)
    log.info("Clicked at (%d, %d)", x, y)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Small desktop automation demo using PyAutoGUI."
    )
    parser.add_argument(
        "--message",
        default="Hello! This text was typed automatically with PyAutoGUI.",
        help="Text to type automatically.",
    )
    parser.add_argument(
        "--delay", type=int, default=5,
        help="Seconds to wait before starting (default: 5).",
    )
    parser.add_argument(
        "--type-interval", type=float, default=0.05,
        help="Delay between keystrokes in seconds (default: 0.05).",
    )
    parser.add_argument(
        "--screenshot", default="screenshot.png",
        help="Path to save the screenshot to (default: screenshot.png).",
    )
    parser.add_argument(
        "--no-screenshot", action="store_true",
        help="Skip taking a screenshot.",
    )
    parser.add_argument(
        "--no-click", action="store_true",
        help="Skip the click step entirely.",
    )
    parser.add_argument(
        "--click-x", type=int, default=None,
        help="X coordinate to click (defaults to screen center).",
    )
    parser.add_argument(
        "--click-y", type=int, default=None,
        help="Y coordinate to click (defaults to screen center).",
    )
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    pyautogui.FAILSAFE = True  # move mouse to a screen corner to abort

    countdown(args.delay)
    type_message(args.message, args.type_interval)

    if not args.no_screenshot:
        time.sleep(1)
        take_screenshot(Path(args.screenshot))

    if not args.no_click:
        time.sleep(1)
        click_at(args.click_x, args.click_y)

    log.info("Done.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
