#!/usr/bin/env python3
"""Apply v6 optimisation to forge-v6-test/final/ in place (v5 finals
are the input; forge-v5-test/ is untouched). Reports per-file sizes
before/after and v6 stats, then runs the improved validator."""
import glob
import json
import os
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))
from emoji_forge.v6 import process_tgs_v6  # noqa: E402
from emoji_forge.validate_tgs import validate_tgs_bytes  # noqa: E402

FINAL = os.path.join(os.path.dirname(__file__), "..", "data", "projects", "forge-v6-test", "final")
report = []
for path in sorted(glob.glob(os.path.join(FINAL, "*.tgs"))):
    data = open(path, "rb").read()
    out, stats = process_tgs_v6(data)
    open(path, "wb").write(out)
    issues, warnings = validate_tgs_bytes(out, stats and 0)
    # index from filename
    idx = 0
    for part in os.path.basename(path).split("_"):
        if part.isdigit() and len(part) == 3:
            idx = int(part)
            break
    issues, warnings = validate_tgs_bytes(out, idx)
    report.append({"file": os.path.basename(path), "index": idx, **stats,
                   "validator_issues": issues, "validator_warnings": warnings})
    print(f"#{idx:02d} {stats['size_before']} -> {stats['size_after']} B "
          f"(assets {stats['assets_before']}->{stats['assets_after']}, "
          f"clamped {stats['brand_clamped']}, baked {stats['brand_baked']}, "
          f"kf_removed {stats['keyframes_removed']}) "
          f"validator_issues={len(issues)}")
total_before = sum(r["size_before"] for r in report)
total_after = sum(r["size_after"] for r in report)
print(f"TOTAL {total_before} -> {total_after} B "
      f"({total_before - total_after} B saved, "
      f"{100.0 * (total_before - total_after) / total_before:.1f}%)")
print(f"BLOCKED: {sum(1 for r in report if r['validator_issues'])} files")
json.dump(report, open("/tmp/v6_qa/size_report.json", "w"), ensure_ascii=False, indent=2)
