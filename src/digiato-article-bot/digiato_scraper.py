import re
import json
import time
import logging
from typing import Any, Dict, List, Optional, Tuple

import requests
from bs4 import BeautifulSoup, Tag
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry


LOGGER = logging.getLogger(__name__)


class HttpClient:
    """A lightweight HTTP client with retries, timeouts and connection pooling."""

    def __init__(self, timeout_seconds: float = 12.0) -> None:
        self.timeout_seconds = timeout_seconds
        self.session = requests.Session()
        retry = Retry(
            total=3,
            backoff_factor=0.5,
            status_forcelist=[429, 500, 502, 503, 504],
            allowed_methods=["HEAD", "GET", "OPTIONS"],
            raise_on_status=False,
        )
        adapter = HTTPAdapter(max_retries=retry, pool_connections=10, pool_maxsize=20)
        self.session.mount("http://", adapter)
        self.session.mount("https://", adapter)
        self.default_headers = {
            "User-Agent": (
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                "(KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
            ),
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language": "fa-IR,fa;q=0.9,en-US;q=0.8,en;q=0.7",
            "Connection": "keep-alive",
        }

    def get_html(self, url: str) -> str:
        try:
            response = self.session.get(url, headers=self.default_headers, timeout=self.timeout_seconds)
            if response.status_code != 200:
                LOGGER.warning("GET %s -> %s", url, response.status_code)
            response.raise_for_status()
            response.encoding = response.encoding or "utf-8"
            return response.text
        except requests.RequestException as exc:
            LOGGER.error("Failed to fetch %s: %s", url, exc)
            return ""


_http_client = HttpClient()


def _safe_json_loads(text: str) -> Optional[Any]:
    try:
        return json.loads(text)
    except Exception:
        # Attempt to fix common JSON-LD issues (e.g., stray script comments)
        try:
            cleaned = text.strip().split("</script>")[0]
            return json.loads(cleaned)
        except Exception:
            return None


def extract_jsonld_article(html: str) -> Optional[Dict[str, Any]]:
    soup = BeautifulSoup(html, "html.parser")
    for script in soup.find_all("script", attrs={"type": "application/ld+json"}):
        raw = script.string or script.get_text("", strip=True)
        if not raw:
            continue
        data = _safe_json_loads(raw)
        if not data:
            continue
        try:
            if isinstance(data, dict):
                # @graph array
                if isinstance(data.get("@graph"), list):
                    for item in data["@graph"]:
                        t = str(item.get("@type", ""))
                        if t and "article" in t.lower():
                            return item  # type: ignore[return-value]
                # Single object
                t = str(data.get("@type", ""))
                if t and "article" in t.lower():
                    return data
            elif isinstance(data, list):
                for item in data:
                    if not isinstance(item, dict):
                        continue
                    t = str(item.get("@type", ""))
                    if t and "article" in t.lower():
                        return item
        except Exception:
            continue
    return None


def _extract_meta(soup: BeautifulSoup, property_name: str) -> Optional[str]:
    # Try property then name
    tag = soup.find("meta", attrs={"property": property_name})
    if tag and tag.get("content"):
        return tag.get("content").strip()
    tag = soup.find("meta", attrs={"name": property_name})
    if tag and tag.get("content"):
        return tag.get("content").strip()
    return None


def extract_images(soup: BeautifulSoup) -> List[str]:
    """Extract unique image URLs from the article content container."""
    urls: Dict[str, bool] = {}
    for img in soup.select("div.articleNewsPosts__content img"):
        src = img.get("src")
        if src:
            urls[src] = True
    return list(urls.keys())


def parse_articles(html: str) -> List[Dict[str, Any]]:
    soup = BeautifulSoup(html, "html.parser")
    results: List[Dict[str, Any]] = []
    for container in soup.select("li div.rowCard"):
        title_a = container.select_one("a.rowCard__title")
        if not title_a:
            continue
        def get_text(selector: str) -> str:
            node = container.select_one(selector)
            return node.get_text(strip=True) if node else ""
        def get_image_attr(attr: str) -> str:
            img = container.select_one("div.rowCard__image img")
            return img.get(attr, "") if isinstance(img, Tag) else ""
        item = {
            "title": title_a.get_text(strip=True),
            "link": title_a.get("href", ""),
            "description": get_text("p.rowCard__description"),
            "category": get_text("a.rowCard__category"),
            "author": get_text("div.rowCard__author a"),
            "date": get_text("span.rowCard__date"),
            "image": get_image_attr("src"),
        }
        results.append(item)
    return results


BASE_URL = "https://digiato.com/"


def _detect_total_pages(soup: BeautifulSoup, max_pages: int) -> int:
    total_pages = 1
    node = soup.select_one("span.currentPage")
    if node:
        m = re.search(r"از\s*(\d+)", node.get_text(" ", strip=True))
        if m:
            try:
                total_pages = int(m.group(1))
            except ValueError:
                total_pages = 1
    if max_pages > 0 and max_pages < total_pages:
        total_pages = max_pages
    return total_pages


def search(query: str, max_pages: int = 0) -> Tuple[List[Dict[str, Any]], int]:
    """Search Digiato and return results and number of pages retrieved.

    Returns: (results, total_pages_retrieved)
    """
    search_url = f"{BASE_URL}?s={requests.utils.quote(query)}"
    html = _http_client.get_html(search_url)
    if not html:
        return [], 0
    soup = BeautifulSoup(html, "html.parser")
    total_pages = _detect_total_pages(soup, max_pages)
    results = parse_articles(html)
    # Pagination starts at page 2
    for page in range(2, total_pages + 1):
        page_url = f"{BASE_URL}page/{page}?s={requests.utils.quote(query)}"
        page_html = _http_client.get_html(page_url)
        if not page_html:
            break
        page_results = parse_articles(page_html)
        if not page_results:
            break
        results.extend(page_results)
    return results, total_pages


def fetch_article(url: str) -> Optional[Dict[str, Any]]:
    html = _http_client.get_html(url)
    if not html:
        return None
    soup = BeautifulSoup(html, "html.parser")
    meta = extract_jsonld_article(html)
    if not meta:
        meta = {
            "headline": _extract_meta(soup, "og:title") or _extract_meta(soup, "twitter:title"),
            "description": _extract_meta(soup, "og:description") or _extract_meta(soup, "description"),
            "datePublished": _extract_meta(soup, "article:published_time"),
            "dateModified": _extract_meta(soup, "article:modified_time"),
            "author": _extract_meta(soup, "article:author") or _extract_meta(soup, "author"),
            "image": _extract_meta(soup, "og:image") or _extract_meta(soup, "twitter:image"),
        }
    else:
        # Normalize as in PHP
        author = meta.get("author")
        image = meta.get("image")
        meta = {
            "headline": meta.get("headline"),
            "description": meta.get("description"),
            "datePublished": meta.get("datePublished"),
            "dateModified": meta.get("dateModified"),
            "author": (author.get("name") if isinstance(author, dict) else author),
            "image": (image.get("url") if isinstance(image, dict) else image),
        }
    content: List[str] = []
    for p in soup.select("div.articleNewsPosts__content p"):
        text = p.get_text(strip=True)
        if text:
            content.append(text)
    article = {
        **meta,
        "url": url,
        "content": content,
        "images": extract_images(soup),
    }
    return article 