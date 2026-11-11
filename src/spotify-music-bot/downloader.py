import os
import re
import uuid
import requests
import subprocess
from typing import Optional, Dict, Any
from PIL import Image
import mutagen
from mutagen.id3 import ID3, TIT2, TPE1, TALB, APIC, ID3NoHeaderError
from mutagen.mp3 import MP3

from search_engine import search_songs, get_ytmusic
from spotify_parser import get_spotify_track_info

DOWNLOADS_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), 'downloads'))
os.makedirs(DOWNLOADS_DIR, exist_ok=True)

def sanitize_filename(name: str) -> str:
    return re.sub(r'[\\/*?:"<>|]', "", name).strip()

def download_and_convert_thumb(thumb_url: str, output_path: str) -> bool:
    try:
        resp = requests.get(thumb_url, timeout=15)
        if resp.status_code == 200:
            temp_thumb = output_path + '.temp'
            with open(temp_thumb, 'wb') as f:
                f.write(resp.content)
            
            # Open with Pillow and convert to clean JPEG
            im = Image.open(temp_thumb)
            rgb_im = im.convert('RGB')
            # Resize square max 600x600 for optimal Telegram & ID3 size
            rgb_im.thumbnail((600, 600), Image.Resampling.LANCZOS)
            rgb_im.save(output_path, 'JPEG', quality=90)
            if os.path.exists(temp_thumb):
                os.remove(temp_thumb)
            return True
    except Exception as e:
        print(f"Error downloading cover {thumb_url}: {e}")
    return False

def apply_id3_tags(mp3_path: str, title: str, artist: str, album: str = '', cover_path: Optional[str] = None):
    try:
        try:
            tags = ID3(mp3_path)
        except ID3NoHeaderError:
            tags = ID3()

        # Add text tags
        tags.add(TIT2(encoding=3, text=title))
        tags.add(TPE1(encoding=3, text=artist))
        if album:
            tags.add(TALB(encoding=3, text=album))

        # Add Album Art
        if cover_path and os.path.exists(cover_path):
            with open(cover_path, 'rb') as f:
                cover_data = f.read()
            tags.add(APIC(
                encoding=3,
                mime='image/jpeg',
                type=3, # front cover
                desc='Cover',
                data=cover_data
            ))

        tags.save(mp3_path, v2_version=3)
    except Exception as e:
        print(f"Error applying ID3 tags: {e}")

def download_song_by_video_id(video_id: str, title: str, artist: str, album: str = '', thumb_url: Optional[str] = None) -> Optional[Dict[str, Any]]:
    """
    Downloads audio for given video_id, converts to MP3, embeds tags, returns paths.
    """
    unique_id = str(uuid.uuid4())[:8]
    clean_title = sanitize_filename(f"{artist} - {title}")[:60]
    base_filename = f"{clean_title}_{unique_id}"
    
    mp3_path = os.path.join(DOWNLOADS_DIR, f"{base_filename}.mp3")
    cover_path = os.path.join(DOWNLOADS_DIR, f"{base_filename}.jpg")
    
    # 1. Download cover
    if thumb_url:
        download_and_convert_thumb(thumb_url, cover_path)
    
    # 2. Download audio via yt-dlp (YouTube)
    yt_url = f"https://www.youtube.com/watch?v={video_id}"
    ytdlp_bin = "/root/spotify-downloader-bot/venv/bin/yt-dlp"
    if not os.path.exists(ytdlp_bin):
        ytdlp_bin = "/usr/local/lib/hermes-agent/venv/bin/yt-dlp"

    cmd = [
        ytdlp_bin,
        "--no-playlist",
        "--js-runtimes", "node:/usr/local/bin/node",
        "-x",
        "--audio-format", "mp3",
        "--audio-quality", "0", # Best quality 320kbps
        "-o", os.path.join(DOWNLOADS_DIR, f"{base_filename}.%(ext)s"),
    ]

    cookies_file = os.path.join(os.path.dirname(os.path.abspath(__file__)), "cookies.txt")
    if os.path.exists(cookies_file):
        cmd.extend(["--cookies", cookies_file])

    cmd.append(yt_url)

    try:
        res = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=90)
        if res.returncode != 0 or not os.path.exists(mp3_path):
            print(f"YouTube download failed: {res.stderr.decode('utf-8', errors='ignore')[:250]} -> Falling back to SoundCloud...")
            # Fallback to SoundCloud
            return download_from_soundcloud(title, artist, album, thumb_url)

        # 3. Apply ID3 tags
        apply_id3_tags(mp3_path, title, artist, album, cover_path if os.path.exists(cover_path) else None)

        # 4. Get audio duration
        duration = 0
        try:
            audio = MP3(mp3_path)
            duration = int(audio.info.length)
        except Exception:
            pass

        file_size = os.path.getsize(mp3_path)

        return {
            'mp3_path': mp3_path,
            'cover_path': cover_path if os.path.exists(cover_path) else None,
            'title': title,
            'artist': artist,
            'album': album,
            'duration': duration,
            'file_size': file_size,
        }
    except Exception as e:
        print(f"Download exception: {e} -> Falling back to SoundCloud...")
        return download_from_soundcloud(title, artist, album, thumb_url)

def download_from_soundcloud(title: str, artist: str, album: str = '', thumb_url: Optional[str] = None) -> Optional[Dict[str, Any]]:
    """
    Fallback downloader using SoundCloud search which never requires bot verification or cookies.
    Uses --max-downloads 1 --ignore-errors to skip DRM items cleanly.
    """
    unique_id = str(uuid.uuid4())[:8]
    clean_title = sanitize_filename(f"{artist} - {title}")[:60]
    base_filename = f"{clean_title}_{unique_id}"

    mp3_path = os.path.join(DOWNLOADS_DIR, f"{base_filename}.mp3")
    cover_path = os.path.join(DOWNLOADS_DIR, f"{base_filename}.jpg")

    if thumb_url:
        download_and_convert_thumb(thumb_url, cover_path)

    ytdlp_bin = "/root/spotify-downloader-bot/venv/bin/yt-dlp"
    if not os.path.exists(ytdlp_bin):
        ytdlp_bin = "/usr/local/lib/hermes-agent/venv/bin/yt-dlp"

    query = f"{artist} {title}".strip()
    cmd = [
        ytdlp_bin,
        "--no-playlist",
        "--max-downloads", "1",
        "--ignore-errors",
        "--concurrent-fragments", "4",
        "--buffer-size", "64K",
        f"scsearch5:{query}",
        "-x",
        "--audio-format", "mp3",
        "--audio-quality", "0",
        "-o", os.path.join(DOWNLOADS_DIR, f"{base_filename}.%(ext)s")
    ]

    try:
        subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=90)
        if not os.path.exists(mp3_path):
            # Try searching by title only
            cmd[6] = f"scsearch5:{title}"
            subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=90)

        if not os.path.exists(mp3_path):
            print(f"SoundCloud fallback also failed for: {artist} - {title}")
            return None

        apply_id3_tags(mp3_path, title, artist, album, cover_path if os.path.exists(cover_path) else None)

        duration = 0
        try:
            audio = MP3(mp3_path)
            duration = int(audio.info.length)
        except Exception:
            pass

        return {
            'mp3_path': mp3_path,
            'cover_path': cover_path if os.path.exists(cover_path) else None,
            'title': title,
            'artist': artist,
            'album': album,
            'duration': duration,
            'file_size': os.path.getsize(mp3_path),
        }
    except Exception as e:
        print(f"SoundCloud download exception: {e}")
        return None

def download_track_by_metadata(title: str, artist: str, album: str = '', thumb_url: Optional[str] = None) -> Optional[Dict[str, Any]]:
    """
    Downloads track by title and artist directly.
    Prioritizes SoundCloud first for instant, bot-check-free high-speed download,
    then falls back to YouTube Music.
    """
    # 1. High-speed direct download (SoundCloud engine)
    res = download_from_soundcloud(title=title, artist=artist, album=album or title, thumb_url=thumb_url)
    if res and os.path.exists(res.get('mp3_path', '')):
        return res

    # 2. Fallback to YouTube Music
    query = f"{artist} {title}".strip()
    results = search_songs(query, limit=3)
    if not results:
        results = search_songs(title, limit=3)

    if results:
        best_match = results[0]
        cover_to_use = thumb_url or best_match.get('thumb_url')
        res = download_song_by_video_id(
            video_id=best_match['video_id'],
            title=title,
            artist=artist,
            album=album or title,
            thumb_url=cover_to_use
        )
        if res and os.path.exists(res.get('mp3_path', '')):
            return res

    return None

def download_spotify_track(track_id: str) -> Optional[Dict[str, Any]]:
    """
    Given Spotify track_id:
    1. Fetches metadata (title, artist, high-res cover)
    2. Searches best audio match on YouTube Music
    3. Downloads and embeds tags with official Spotify cover
    """
    track = get_spotify_track_info(track_id)
    if not track:
        return None

    return download_track_by_metadata(
        title=track['title'],
        artist=track['artist'],
        album=track.get('title', ''),
        thumb_url=track.get('cover_url')
    )
