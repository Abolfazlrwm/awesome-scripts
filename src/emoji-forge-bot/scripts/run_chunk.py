"""Resume helper: process a zero-based index range of a project with the
exact parameters of its applied Mati plan. Existing finals are reused by
process_project's fingerprint cache, so re-running is cheap and safe."""
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.brand import BrandProfile
from emoji_forge.pipeline import process_project

BASE = "/home/hatch/workspace/emoji-forge-ai/data"


def main():
    project_id, start, end = sys.argv[1], int(sys.argv[2]), int(sys.argv[3])
    root = os.path.join(BASE, "projects", project_id)
    plan = json.load(open(os.path.join(root, "mati_plan_applied.json")))
    manifest = json.load(open(os.path.join(root, "manifest.json")))
    brand = BrandProfile.from_dict(plan.get("brand") or {})
    mode = sys.argv[4] if len(sys.argv) > 4 else plan.get("mode", "strip")
    report = process_project(
        BASE, project_id, manifest, brand, None,
        mode, plan.get("output_kind", "emoji"),
        only_indices=list(range(start, end)),
        hide_layer_names=set(plan.get("hide_layer_names") or []),
    )
    ok = sum(1 for i in report["items"] if i["status"] == "ok")
    print(f"chunk {start}-{end}: ok {ok}/{len(report['items'])}", flush=True)


if __name__ == "__main__":
    main()
