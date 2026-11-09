"""Restart the Emoji Forge bot, recovering its token IN MEMORY from
the still-running old process's environment (/proc/<pid>/environ).

The token is never printed, never written to disk, never logged: it
moves from the old process's environ straight into the new process's
environment. Usage: restart_with_env.py <old_pid> <logfile>
"""
import os
import subprocess
import sys
import time

BASE = "/home/hatch/workspace/emoji-forge-ai"


def main():
    old_pid = sys.argv[1]
    logfile = sys.argv[2]
    with open(f"/proc/{old_pid}/environ", "rb") as fh:
        env_pairs = fh.read().split(b"\0")
    token = None
    for pair in env_pairs:
        if pair.startswith(b"EMOJI_FORGE_TOKEN="):
            token = pair.split(b"=", 1)[1].decode()
            break
    if not token:
        print("TOKEN NOT FOUND in old process env")
        return 1
    subprocess.run(["kill", old_pid], check=False)
    time.sleep(2)
    env = dict(os.environ)
    env["EMOJI_FORGE_TOKEN"] = token
    token = None
    with open(logfile, "ab") as log:
        proc = subprocess.Popen(
            [os.path.join(BASE, ".venv/bin/python"), "run.py"],
            cwd=BASE, env=env, stdout=log, stderr=subprocess.STDOUT,
            start_new_session=True,
        )
    print("new bot pid:", proc.pid)
    return 0


if __name__ == "__main__":
    sys.exit(main())
