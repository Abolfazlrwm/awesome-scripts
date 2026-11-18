"""Render-neutral repair of processed TGS finals so they pass the
pre-publish validator without changing what renders:

- delete hidden (hd=1) layers that nothing visible depends on
  (not a resolved parent of any visible layer, not a track-matte
  source, not the matte layer above a tt consumer);
- empty ty=4 shape layers: delete, or convert to a Null (ty=3) when
  other layers are parented to them (transform preserved);
- remaining protected hidden layers (parents of visible layers):
  convert to Null with the same ks/ip/op/parent and clear hd —
  they rendered nothing themselves, only their transform mattered;
- drop dangling parent pointers (target ind does not exist);
- clamp ip>op layers to op=ip (their visible range stays empty);
- renumber duplicate inds only when the ind is not referenced as a
  parent and the extra layers are not matte-related; otherwise flag;
- prune unreferenced assets (also shrinks oversize files).

Originals are never touched. Changed finals are backed up first.
A JSON report lands in the project root as fix_report.json.
"""
import json
import os
import shutil
import sys

sys.path.insert(0, os.path.join(os.path.dirname(__file__), "..", "src"))

from emoji_forge.animation_engine import load_tgs, dump_tgs, tgs_prune_unused_assets
from emoji_forge.validate_tgs import validate_tgs_bytes

BASE = "/home/hatch/workspace/emoji-forge-ai/data"
KEEP_KEYS = ("ind", "ty", "nm", "ks", "ip", "op", "st", "parent", "sr", "ao",
             "bm", "ddd", "ln", "cl", "tg")


def scopes_of(doc):
    out = [doc.get("layers") or []]
    for a in doc.get("assets") or []:
        if isinstance(a.get("layers"), list):
            out.append(a["layers"])
    return out


def fix_scope(layers, log):
    """Mutate one scope's layer list in place. Returns flags list."""
    flags = []

    def resolved_parent(ind):
        for l in layers:
            if l.get("ind") == ind:
                return l
        return None

    def matte_source_ids():
        ids = set()
        for i, l in enumerate(layers):
            if l.get("tt") and i > 0:
                ids.add(id(layers[i - 1]))
        return ids

    def protected_ids():
        prot = set()
        mattes = matte_source_ids()
        for l in layers:
            if l.get("td"):
                prot.add(id(l))
            if id(l) in mattes:
                prot.add(id(l))
        # ancestors (via resolved parents) of every non-hidden layer,
        # plus ancestors of already-protected layers, transitively.
        frontier = [l for l in layers if not l.get("hd")] + \
                   [l for l in layers if id(l) in prot]
        seen = set()
        stack = list(frontier)
        while stack:
            cur = stack.pop()
            if id(cur) in seen:
                continue
            seen.add(id(cur))
            p = cur.get("parent")
            if p is not None:
                tgt = resolved_parent(p)
                if tgt is not None and id(tgt) not in prot:
                    prot.add(id(tgt))
                    stack.append(tgt)
        return prot

    # 1) delete inert hidden layers
    prot = protected_ids()
    kept = []
    for l in layers:
        if l.get("hd") and id(l) not in prot:
            log["hidden_deleted"] += 1
            continue
        kept.append(l)
    layers[:] = kept

    # 2) empty ty=4 layers: delete or Null-convert when parented-to
    referenced = {l.get("parent") for l in layers if l.get("parent") is not None}
    mattes = matte_source_ids()
    kept = []
    for l in layers:
        if l.get("ty") == 4 and not (l.get("shapes") or []):
            if l.get("ind") in referenced or l.get("td") or id(l) in mattes:
                for k in list(l.keys()):
                    if k not in KEEP_KEYS:
                        del l[k]
                l["ty"] = 3
                l.pop("hd", None)
                log["empty_to_null"] += 1
                kept.append(l)
            else:
                log["empty_deleted"] += 1
            continue
        kept.append(l)
    layers[:] = kept

    # 3) remaining hidden layers: Null-convert (they are protected
    #    parents / mattes). td and matte sources are flagged, not touched.
    prot = protected_ids()
    mattes = matte_source_ids()
    for l in layers:
        if l.get("hd"):
            if l.get("td") or id(l) in mattes:
                flags.append(f"hidden matte/td kept: {l.get('nm')}")
                continue
            for k in list(l.keys()):
                if k not in KEEP_KEYS:
                    del l[k]
            l["ty"] = 3
            l.pop("hd", None)
            log["hidden_to_null"] += 1

    # 4) dangling parents
    inds = {l.get("ind") for l in layers}
    for l in layers:
        if l.get("parent") is not None and l.get("parent") not in inds:
            del l["parent"]
            log["dangling_dropped"] += 1

    # 5) ip>op clamp (range stays empty)
    for l in layers:
        if (l.get("ip") or 0) > (l.get("op") or 0):
            l["op"] = l.get("ip") or 0
            log["ipop_clamped"] += 1

    # 6) duplicate inds: renumber unreferenced extras only
    referenced = {l.get("parent") for l in layers if l.get("parent") is not None}
    mattes = matte_source_ids()
    seen = {}
    max_ind = max([l.get("ind") or 0 for l in layers] + [0])
    for l in layers:
        ind = l.get("ind")
        if ind in seen:
            if ind in referenced or l.get("td") or id(l) in mattes or id(seen[ind]) in mattes:
                flags.append(f"duplicate ind {ind} referenced/matte — left as-is")
                continue
            max_ind += 1
            l["ind"] = max_ind
            log["dup_renumbered"] += 1
        else:
            seen[ind] = l
    return flags


def main():
    project_id = sys.argv[1]
    root = os.path.join(BASE, "projects", project_id)
    final_dir = os.path.join(root, "final")
    backup_dir = os.path.join(root, "final_backup_before_fix")
    os.makedirs(backup_dir, exist_ok=True)
    report = {}
    for name in sorted(os.listdir(final_dir)):
        if not name.endswith(".tgs"):
            continue
        path = os.path.join(final_dir, name)
        data = open(path, "rb").read()
        issues, _ = validate_tgs_bytes(data, 0)
        if not issues:
            continue
        doc = load_tgs(data)
        log = {"hidden_deleted": 0, "hidden_to_null": 0, "empty_deleted": 0,
               "empty_to_null": 0, "dangling_dropped": 0, "ipop_clamped": 0,
               "dup_renumbered": 0, "assets_pruned": 0}
        flags = []
        for scope in scopes_of(doc):
            flags += fix_scope(scope, log)
        for _ in range(4):
            n = tgs_prune_unused_assets(doc)
            log["assets_pruned"] += n
            if not n:
                break
        out = dump_tgs(doc)
        new_issues, _ = validate_tgs_bytes(out, 0)
        shutil.copy2(path, os.path.join(backup_dir, name))
        with open(path, "wb") as fh:
            fh.write(out)
        report[name] = {"before_bytes": len(data), "after_bytes": len(out),
                        "log": log, "flags": flags,
                        "remaining_issues": len(new_issues),
                        "remaining_sample": new_issues[:2]}
        print(name, json.dumps(report[name], ensure_ascii=False), flush=True)
    with open(os.path.join(root, "fix_report.json"), "w") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=1)
    print(f"fixed {len(report)} files")


if __name__ == "__main__":
    main()
