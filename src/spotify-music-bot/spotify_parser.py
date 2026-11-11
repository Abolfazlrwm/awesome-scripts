import re
import json
import urllib.request
from typing import Optional, Dict, Any, List

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
}

def parse_spotify_url(url: str) -> Optional[Dict[str, str]]:
    if 'spotify.link/' in url:
        try:
            req = urllib.request.Request(url, headers=HEADERS)
            resp = urllib.request.urlopen(req, timeout=8)
            url = resp.geturl()
        except Exception:
            pass

    pattern = r'spotify\.com/(?:intl-[a-zA-Z\-]+/)?(?:user/[^/]+/)?(track|album|playlist)/([a-zA-Z0-9]+)'
    match = re.search(pattern, url)
    if not match:
        return None
    return {'type': match.group(1), 'id': match.group(2)}

def get_spotify_track_info(track_id: str) -> Optional[Dict[str, Any]]:
    embed_url = f'https://open.spotify.com/embed/track/{track_id}'
    try:
        req = urllib.request.Request(embed_url, headers=HEADERS)
        html = urllib.request.urlopen(req, timeout=12).read().decode('utf-8')
        match = re.search(r'<script id="__NEXT_DATA__" type="application/json">(.*?)</script>', html)
        if not match:
            return None
        data = json.loads(match.group(1))
        entity = data.get('props', {}).get('pageProps', {}).get('state', {}).get('data', {}).get('entity', {})
        
        title = entity.get('name') or entity.get('title') or 'Unknown Title'
        artists = [a.get('name') for a in entity.get('artists', []) if a.get('name')]
        artist_str = ', '.join(artists) if artists else 'Unknown Artist'
        
        # High-res cover image
        images = entity.get('visualIdentity', {}).get('image', [])
        cover_url = None
        if images:
            # Pick largest image
            sorted_imgs = sorted(images, key=lambda x: x.get('maxWidth', 0), reverse=True)
            cover_url = sorted_imgs[0].get('url')
        
        duration_sec = (entity.get('duration') or 0) // 1000
        preview_url = entity.get('audioPreview', {}).get('url')
        
        return {
            'id': track_id,
            'title': title,
            'artist': artist_str,
            'artists': artists,
            'cover_url': cover_url,
            'duration': duration_sec,
            'preview_url': preview_url,
            'url': f'https://open.spotify.com/track/{track_id}'
        }
    except Exception as e:
        print(f"Error fetching spotify track {track_id}: {e}")
        return None

def get_spotify_album_info(album_id: str, is_playlist: bool = False) -> Optional[Dict[str, Any]]:
    type_str = 'playlist' if is_playlist else 'album'
    embed_url = f'https://open.spotify.com/embed/{type_str}/{album_id}'
    try:
        req = urllib.request.Request(embed_url, headers=HEADERS)
        html = urllib.request.urlopen(req, timeout=12).read().decode('utf-8')
        match = re.search(r'<script id="__NEXT_DATA__" type="application/json">(.*?)</script>', html)
        if not match:
            return None
        data = json.loads(match.group(1))
        entity = data.get('props', {}).get('pageProps', {}).get('state', {}).get('data', {}).get('entity', {})
        
        title = entity.get('name') or entity.get('title') or 'Unknown'
        subtitle = entity.get('subtitle') or ''
        
        images = entity.get('visualIdentity', {}).get('image', [])
        cover_url = images[0].get('url') if images else None
        
        raw_tracks = entity.get('trackList', [])
        tracks = []
        for t in raw_tracks:
            t_uri = t.get('uri', '')
            t_id = t_uri.split(':')[-1] if ':' in t_uri else ''
            tracks.append({
                'id': t_id,
                'title': t.get('title', 'Unknown'),
                'artist': t.get('subtitle', ''),
                'duration': (t.get('duration') or 0) // 1000,
                'preview_url': t.get('audioPreview', {}).get('url')
            })
            
        return {
            'type': type_str,
            'id': album_id,
            'title': title,
            'subtitle': subtitle,
            'cover_url': cover_url,
            'tracks': tracks,
            'total_tracks': len(tracks),
            'url': f'https://open.spotify.com/{type_str}/{album_id}'
        }
    except Exception as e:
        print(f"Error fetching spotify {type_str} {album_id}: {e}")
        return None
