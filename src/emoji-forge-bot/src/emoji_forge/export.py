"""Export: sorted ZIP, per-file report, technical info, publish guide.

Publishing is NOT simulated: the guide explains the two real routes
(1) bot-created new set via Bot API, (2) manual in-app editor with the ZIP.
"""
import json
import os
import zipfile


def build_report(project_id: str, set_name: str, results: list) -> dict:
    ok = sum(1 for r in results if r.get("status") == "ok")
    return {
        "project_id": project_id,
        "set_name": set_name,
        "total": len(results),
        "ok": ok,
        "failed": len(results) - ok,
        "items": results,
    }


def report_markdown(report: dict) -> str:
    lines = [
        f"# گزارش خروجی TZ Emojis — پروژه {report['project_id']}",
        "",
        f"پک: {report.get('set_name') or 'نامشخص'} — موفق: {report['ok']} / کل: {report['total']} / ناموفق: {report['failed']}",
        "",
        "| # | فایل | وضعیت | جزئیات |",
        "|---|---|---|---|",
    ]
    for item in report["items"]:
        lines.append(
            f"| {item.get('index', '')} | {item.get('output_name', '')} | "
            f"{item.get('status', '')} | {item.get('detail', '')} |"
        )
    lines += [
        "",
        "## انتشار رسمی",
        "",
        "1. مسیر ربات: دستور /publish_new در ربات — فقط یک ست جدید می‌سازد که",
        "   ربات مالک آن است؛ پک اصلی شخص دیگر ویرایش نمی‌شود.",
        "2. مسیر دستی: فایل‌های ZIP را باز کنید و در ادیتور داخل اپ تلگرام",
        "   (یا @Stickers) یک پک جدید بسازید و فایل‌ها را اضافه کنید.",
        "   لینک پک جدید به شکل t.me/addemoji/<name>_by_<bot> خواهد بود.",
    ]
    return "\n".join(lines) + "\n"


def write_zip(final_dir: str, zip_path: str) -> list:
    """Zip sorted final files. Returns the sorted names actually zipped."""
    os.makedirs(os.path.dirname(zip_path), exist_ok=True)
    names = sorted(
        f for f in os.listdir(final_dir)
        if os.path.isfile(os.path.join(final_dir, f))
    )
    with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED) as zf:
        for name in names:
            zf.write(os.path.join(final_dir, name), arcname=name)
    return names


def write_report_files(project_dir: str, report: dict) -> dict:
    json_path = os.path.join(project_dir, "report.json")
    md_path = os.path.join(project_dir, "report.md")
    with open(json_path, "w", encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    with open(md_path, "w", encoding="utf-8") as fh:
        fh.write(report_markdown(report))
    return {"json": json_path, "markdown": md_path}
