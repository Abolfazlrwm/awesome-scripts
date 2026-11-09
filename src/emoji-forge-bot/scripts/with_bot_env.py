"""Run a command with the running bot's EMOJI_FORGE_* environment
recovered IN MEMORY from /proc/<pid>/environ. Values are never
printed or written anywhere. Usage: with_bot_env.py <bot_pid> <cmd...>"""
import os
import sys


def main():
    pid = sys.argv[1]
    cmd = sys.argv[2:]
    with open(f"/proc/{pid}/environ", "rb") as fh:
        pairs = fh.read().split(b"\0")
    env = dict(os.environ)
    for pair in pairs:
        if pair.startswith(b"EMOJI_FORGE_") and b"=" in pair:
            key, value = pair.split(b"=", 1)
            env[key.decode()] = value.decode()
    if "EMOJI_FORGE_TOKEN" not in env:
        print("EMOJI_FORGE_TOKEN not found in bot env")
        return 1
    os.execvpe(cmd[0], cmd, env)
    return 0


if __name__ == "__main__":
    sys.exit(main())
