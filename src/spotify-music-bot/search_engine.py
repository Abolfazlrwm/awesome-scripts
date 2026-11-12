# Telegram: @Abolfazl_rwm
import re
from typing import List, Dict, Any, Optional
from ytmusicapi import YTMusic

# Lazy-loaded singleton
_ytmusic_instance: Optional[YTMusic] = None

def get_ytmusic() -> YTMusic:
    global _ytmusic_instance
    if _ytmusic_instance is None:
        _ytmusic_instance = YTMusic()
    return _ytmusic_instance

def search_songs(query: str, limit: int = 7) -> List[Dict[str, Any]]:
    """
    Searches for songs via YouTube Music API.
    Returns structured list with title, artist, album, duration, videoId, thumbnail.
    """
    try:
        ytm = get_ytmusic()
        raw_results = ytm.search(query, filter='songs', limit=limit)
        
        songs = []
        for item in raw_results:
            if item.get('resultType') != 'song' and 'videoId' not in item:
                continue
                
            video_id = item.get('videoId')
            if not video_id:
                continue
                
            title = item.get('title') or 'Unknown Title'
            artists = [a.get('name') for a in item.get('artists', []) if a.get('name')]
            artist_str = ', '.join(artists) if artists else 'Unknown Artist'
            
            album = item.get('album', {})
            album_name = album.get('name') if album else ''
            
            duration = item.get('duration') or ''
            
            # Duration in seconds if parseable
            duration_sec = 0
            if duration:
                parts = duration.split(':')
                try:
                    if len(parts) == 2:
                        duration_sec = int(parts[0]) * 60 + int(parts[1])
                    elif len(parts) == 3:
                        duration_sec = int(parts[0]) * 3600 + int(parts[1]) * 60 + int(parts[2])
                except ValueError:
                    duration_sec = 0
            
            # Thumbnail
            thumbnails = item.get('thumbnails', [])
            thumb_url = thumbnails[-1].get('url') if thumbnails else None
            # High-res fix if possible
            if thumb_url and '=w' in thumb_url:
                thumb_url = re.sub(r'=w\d+-h\d+', '=w600-h600', thumb_url)
                
            songs.append({
                'video_id': video_id,
                'title': title,
                'artist': artist_str,
                'album': album_name,
                'duration': duration,
                'duration_sec': duration_sec,
                'thumb_url': thumb_url,
                'url': f'https://music.youtube.com/watch?v={video_id}'
            })
            
            if len(songs) >= limit:
                break
                
        return songs
    except Exception as e:
        print(f"Error searching songs for query '{query}': {e}")
        return []
