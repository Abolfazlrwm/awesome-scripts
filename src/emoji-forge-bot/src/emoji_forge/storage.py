"""Persistent project storage under data/ (never /tmp).

Layout:
  data/projects/<project_id>/originals/<index>_<stem>.<ext>
  data/projects/<project_id>/cleaned/...
  data/projects/<project_id>/final/...
  data/projects/<project_id>/preview/...
  data/projects/<project_id>/manifest.json
  data/projects/<project_id>/brand.json
  data/projects/<project_id>/report.json
  data/jobs.json   (queue state, for resume after restart)
"""
import json
import os
import re
import secrets
from datetime import datetime, timezone


def new_project_id() -> str:
    stamp = datetime.now(timezone.utc).strftime("%Y%m%d-%H%M%S")
    return f"forge-{stamp}-{secrets.token_hex(3)}"


def project_dir(data_dir: str, project_id: str) -> str:
    if not re.fullmatch(r"[A-Za-z0-9-]+", project_id or ""):
        raise ValueError("شناسه پروژه نامعتبر است.")
    path = os.path.join(data_dir, "projects", project_id)
    os.makedirs(path, exist_ok=True)
    return path


def ordered_name(project_id: str, index: int, stem: str, ext: str) -> str:
    stem = re.sub(r"[^A-Za-z0-9_-]+", "_", stem or "item")[:40] or "item"
    return f"{project_id}_{index:03d}_{stem}{ext}"


def write_json(path: str, payload: dict) -> None:
    os.makedirs(os.path.dirname(path), exist_ok=True)
    tmp = path + ".tmp"
    with open(tmp, "w", encoding="utf-8") as fh:
        json.dump(payload, fh, ensure_ascii=False, indent=2)
    os.replace(tmp, path)


def read_json(path: str, default=None):
    try:
        with open(path, encoding="utf-8") as fh:
            return json.load(fh)
    except (OSError, ValueError):
        return default


def load_jobs(data_dir: str) -> dict:
    return read_json(os.path.join(data_dir, "jobs.json"), default={"jobs": []}) or {"jobs": []}


def save_jobs(data_dir: str, jobs: dict) -> None:
    write_json(os.path.join(data_dir, "jobs.json"), jobs)
