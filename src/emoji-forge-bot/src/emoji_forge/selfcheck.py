"""Startup self-check (v9): pure local validation, NO network.

Verifies the things the bot needs before it can honestly say it is
ready: data directories, the traced logo asset, fonts for raster
text, and optional tools (ffmpeg for WEBM, vtracer for logo
vectorisation). Optional-tool gaps are reported as warnings, not
failures — the engines that need them degrade honestly at runtime.
"""
import json
import os
import shutil


def run_self_check(data_dir: str) -> tuple[bool, list]:
    lines = []
    ok = True

    for sub in ("", "users", "projects", "brain/requests", "brain/decisions"):
        path = os.path.join(data_dir, sub) if sub else data_dir
        try:
            os.makedirs(path, exist_ok=True)
            if not os.path.isdir(path):
                raise OSError("not a directory")
        except OSError as exc:
            ok = False
            lines.append(f"data dir {sub or '.'}: FAILED ({exc})")

    shapes = os.path.join(os.path.dirname(os.path.abspath(__file__)),
                          "ehsan_logo_shapes.json")
    try:
        with open(shapes, encoding="utf-8") as fh:
            payload = json.load(fh)
        count = len(payload.get("subpaths") or [])
        if count < 1:
            raise ValueError("no subpaths")
        lines.append(f"logo asset: {count} subpaths")
    except Exception as exc:
        ok = False
        lines.append(f"logo asset: FAILED ({exc})")

    font_candidates = [
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
    ]
    if any(os.path.exists(p) for p in font_candidates):
        lines.append("fonts: system bold font found")
    else:
        lines.append("fonts: no system bold font (PIL default fallback)")

    lines.append("ffmpeg: " + ("found" if shutil.which("ffmpeg")
                                else "missing (WEBM processing unavailable)"))
    try:
        import vtracer  # noqa: F401
        lines.append("vtracer: found (user-logo vectorisation available)")
    except Exception:
        lines.append("vtracer: missing (user-logo vectorisation unavailable)")

    # v10: importer guard must import and repair a synthetic
    # tr-less group; the registry must carry the expanded counts;
    # the publish checkpoint directory must be writable.
    try:
        from .importer_guard import fix_importer_risks
        synthetic = {"v": "5.7.0", "fr": 30, "ip": 0, "op": 30,
                     "w": 512, "h": 512, "assets": [],
                     "layers": [{"nm": "L", "ty": 4, "shapes": [
                         {"ty": "gr", "nm": "G", "it": [
                             {"ty": "sh", "ks": {"a": 0, "k": {
                                 "v": [[0, 0], [10, 0], [10, 10]],
                                 "i": [[0, 0]] * 3, "o": [[0, 0]] * 3,
                                 "c": True}}}]}]}]}
        _fixed, _report = fix_importer_risks(synthetic)
        if _report.get("tr_added") != 1:
            raise ValueError(f"tr fix reported {_report}")
        lines.append("importer guard: OK (synthetic tr fix works)")
    except Exception as exc:
        ok = False
        lines.append(f"importer guard: FAILED ({exc})")
    try:
        from . import registry as _registry
        n_colours = len(_registry.palette_flat())
        n_gradients = len(_registry.gradients())
        if len(_registry.FONTS) < 25 or n_colours < 108 or n_gradients < 12:
            raise ValueError(
                f"fonts={len(_registry.FONTS)} colours={n_colours} "
                f"gradients={n_gradients}")
        lines.append(f"registry: {len(_registry.FONTS)} fonts, "
                     f"{n_colours} colours, {n_gradients} gradients")
    except Exception as exc:
        ok = False
        lines.append(f"registry: FAILED ({exc})")
    try:
        probe_dir = os.path.join(data_dir, "projects", ".selfcheck")
        os.makedirs(probe_dir, exist_ok=True)
        probe_file = os.path.join(probe_dir, "publish_progress.json")
        with open(probe_file, "w", encoding="utf-8") as fh:
            fh.write("{}")
        os.remove(probe_file)
        os.rmdir(probe_dir)
        lines.append("publish checkpoint dir: writable")
    except OSError as exc:
        ok = False
        lines.append(f"publish checkpoint dir: FAILED ({exc})")
    return ok, lines


def readiness_line(data_dir: str) -> str:
    """The single [forge] readiness line printed at startup."""
    from . import __version__
    ok, lines = run_self_check(data_dir)
    state = "OK" if ok else "PROBLEMS"
    return f"[forge] v{__version__} self-check {state}: " + "; ".join(lines)
