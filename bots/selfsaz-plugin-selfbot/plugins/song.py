# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from youtubesearchpython import VideosSearch
import aiohttp
from telethon.tl import types
import logging

logger = logging.getLogger(__name__)

class SongStreamPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.api_url = "https://api.mr-amiri.ir/api/youtube-dl.php"
        self.api_key = "PUT-YOUR-KEY-HERE"
        self.headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36",
            "Referer": "https://www.youtube.com/"
        }

    async def search_best_song(self, query):
        """Search YouTube and return the best matching song"""
        try:
            videos_search = VideosSearch(query, limit=1)
            result = videos_search.result()['result'][0]
            return {
                'id': result.get('id'),
                'title': result.get('title'),
                'url': f"https://youtu.be/{result.get('id')}",
                'duration': self.parse_duration(result.get('duration', '0:00')),
                'uploader': result.get('channel', {}).get('name', 'Unknown')
            }
        except Exception as e:
            logger.error(f"Search error: {str(e)}")
            return None

    async def get_stream_url(self, youtube_url):
        """Get stream URL from the API with retry logic"""
        async with aiohttp.ClientSession() as session:
            for attempt in range(3):
                try:
                    params = {'url': youtube_url, 'key': self.api_key}
                    async with session.get(
                        self.api_url, 
                        params=params,
                        timeout=aiohttp.ClientTimeout(total=10)
                    ) as response:
                        data = await response.json()
                        if data.get('ok'):
                            return next((item['url'] for item in data['result'] if item.get('ext') == 'm4a')), None
                    await asyncio.sleep(2 ** attempt)
                except Exception as e:
                    logger.warning(f"API attempt {attempt+1} failed: {str(e)}")
            return None

    async def send_audio(self, event, song, stream_url):
        """Try streaming first, fallback to download if needed"""
        try:
            # Attempt to send as streamable audio
            await event.client.send_file(
                event.chat_id,
                file=stream_url,
                caption=f"🎵 {song['title']}\n👤 {song['uploader']}",
                attributes=[
                    types.DocumentAttributeAudio(
                        duration=song['duration'],
                        title=song['title'],
                        performer=song['uploader']
                    )
                ],
                supports_streaming=True,
                headers=self.headers
            )
            return True
        except Exception as e:
            logger.error(f"Streaming failed: {str(e)}. Attempting download...")
            try:
                async with aiohttp.ClientSession(headers=self.headers) as session:
                    async with session.get(stream_url) as resp:
                        if resp.status == 200:
                            content = await resp.read()
                            await event.client.send_file(
                                event.chat_id,
                                content,
                                caption=f"🎵 {song['title']}\n👤 {song['uploader']}",
                                attributes=[
                                    types.DocumentAttributeAudio(
                                        duration=song['duration'],
                                        title=song['title'],
                                        performer=song['uploader']
                                    )
                                ]
                            )
                            return True
            except Exception as download_error:
                logger.error(f"Download failed: {str(download_error)}")
                return False

    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^/song\s+(.+)$'))
        async def song_handler(event):
            query = event.pattern_match.group(1).strip()
            if not query:
                return await event.reply("Please provide a song name")
            
            msg = await event.respond("🔍 Searching...")
            try:
                # Search YouTube
                song = await self.search_best_song(query)
                if not song:
                    return await msg.edit("❌ Song not found")
                
                # Get stream URL
                await msg.edit("📡 Getting audio...")
                stream_url = await self.get_stream_url(song['url'])
                if not stream_url:
                    return await msg.edit("❌ Audio source unavailable")
                
                # Attempt to send audio
                await msg.edit("🎵 Sending...")
                success = await self.send_audio(event, song, stream_url)
                
                if not success:
                    await msg.edit("❌ Failed to process audio")
                else:
                    await msg.delete()
                    
            except Exception as e:
                logger.error(f"Handler error: {str(e)}")
                await msg.edit("❌ An error occurred")

        @self.client.on(events.NewMessage(pattern=r'^/songhelp$'))
        async def help_handler(event):
            await event.reply("Use /song <query> to download music")

    def parse_duration(self, duration_str):
        """Convert duration string to seconds"""
        try:
            return sum(int(x) * 60 ** i for i, x in enumerate(reversed(duration_str.split(':'))))
        except:
            return 0

    async def start(self):
        logger.info("Song plugin started")

    async def stop(self):
        logger.info("Song plugin stopped")