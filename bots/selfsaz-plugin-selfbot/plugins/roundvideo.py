# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .base import Plugin
from telethon import events
from telethon.tl.types import (
    Channel, Chat, DocumentAttributeVideo, 
    DocumentAttributeAudio, DocumentAttributeFilename,
    InputMediaUploadedDocument
)
import os
import tempfile
import logging
import subprocess
import asyncio
import time
import shutil

logger = logging.getLogger(__name__)

HELP = """  
🔄 **تبدیل ویدیو به فرمت گرد** 🔄  

▬▬▬▬▬▬▬▬▬▬▬▬  
📌 **قابلیت‌های اصلی**:  
  • تبدیل ویدیوهای معمولی به **ویدیوهای گرد (دایره‌ای)**  
  • پشتیبانی از دو روش:  
      - ریپلای روی ویدیو  
      - آپلود ویدیو همراه با دستور  
  • پشتیبانی از فرمت‌های مختلف ویدیویی  
  • حفظ کیفیت اصلی ویدیو  

▬▬▬▬▬▬▬▬▬▬▬▬  
🎯 **دستورات**:  

  • **English:**  
       `/roundvideo` (با ریپلای) ➔ تبدیل ویدیوی ریپلای شده به فرمت گرد  
       `/roundvideo` (با آپلود) ➔ تبدیل ویدیوی آپلود شده به فرمت گرد  

  • **فارسی (بدون /):**  
       `ویدیو گرد` (با ریپلای) ➔ تبدیل ویدیوی ریپلای شده به فرمت گرد  
       `ویدیو گرد` (با آپلود) ➔ تبدیل ویدیوی آپلود شده به فرمت گرد  

▬▬▬▬▬▬▬▬▬▬▬▬  
✨ **نمونه استفاده**:  
1. تبدیل با ریپلای:  
      `/roundvideo` (ریپلای روی ویدیو) یا `ویدیو گرد` (ریپلای روی ویدیو)  
2. تبدیل با آپلود:  
      ارسال ویدیو همراه با کپشن `/roundvideo` یا `ویدیو گرد`  

⚠️ **نکات مهم**:  
  - پردازش ویدیوهای بزرگ ممکن است زمان‌بر باشد  
  - حداکثر سایز ویدیو: 50MB  
  - در صورت خطا، پیام مناسب نمایش داده می‌شود  
"""

class RoundVideoPlugin(Plugin):
    def __init__(self, client, config, user_id):
        super().__init__(client, config)
        self.owner_id = str(user_id)
        self.temp_dir = tempfile.mkdtemp()
        # Ensure ffmpeg is available
        try:
            subprocess.run(['ffmpeg', '-version'], capture_output=True, check=True)
        except (subprocess.SubprocessError, FileNotFoundError):
            logger.error("FFmpeg is not installed or not in PATH")
        logger.info(f"RoundVideoPlugin initialized for owner: {self.owner_id}")
        
    async def handle_events(self):
        @self.client.on(events.NewMessage(pattern=r'^(?:/roundvideo|ویدیو\s+گرد)(?:@\w+)?$'))
        async def handler(event):
            # Silent owner check - do nothing for others
            if str(event.sender_id) != self.owner_id:
                return
                
            try:
                # Send initial processing message
                processing_msg = await event.reply("⏳ Processing video... This may take a moment.")
                
                # Get the video file
                video_message = None
                if event.is_reply:
                    # If command is a reply to a video
                    reply_msg = await event.get_reply_message()
                    if reply_msg.media and hasattr(reply_msg.media, 'document'):
                        for attribute in reply_msg.media.document.attributes:
                            if isinstance(attribute, DocumentAttributeVideo):
                                video_message = reply_msg
                                break
                else:
                    # If command comes with a video attachment
                    if event.message.media and hasattr(event.message.media, 'document'):
                        for attribute in event.message.media.document.attributes:
                            if isinstance(attribute, DocumentAttributeVideo):
                                video_message = event.message
                                break
                
                if not video_message:
                    await processing_msg.edit("❌ No video found! Please reply to a video or upload a video with the command.")
                    return
                
                # Check file size (limit to 50MB)
                if hasattr(video_message.media.document, 'size'):
                    file_size_mb = video_message.media.document.size / (1024 * 1024)
                    if file_size_mb > 50:
                        await processing_msg.edit("❌ Video file too large (>50MB). Please use a smaller video.")
                        return
                
                # Download the video
                await processing_msg.edit("⏳ Downloading video...")
                start_time = time.time()
                input_file = os.path.join(self.temp_dir, f"input_{int(start_time)}.mp4")
                output_file = os.path.join(self.temp_dir, f"round_{int(start_time)}.mp4")
                
                await video_message.download_media(file=input_file)
                
                # Process the video
                await processing_msg.edit("⏳ Converting to round format...")
                success, duration = await self.convert_to_round_video(input_file, output_file)
                
                if not success:
                    await processing_msg.edit("❌ Failed to convert video. Please try with a different video.")
                    return
                
                # Upload the processed video with correct attributes
                await processing_msg.edit("⏳ Uploading round video...")
                
                # Get video dimensions
                width, height = await self.get_video_dimensions(output_file)
                
                # Create attributes similar to the dump
                attributes = [
                    DocumentAttributeVideo(
                        duration=int(duration),
                        w=width,
                        h=height,
                        round_message=True,
                        supports_streaming=True,
                        nosound=False
                    ),
                    DocumentAttributeFilename(file_name=os.path.basename(output_file))
                ]
                
                # Check if original has audio
                has_audio = await self.has_audio(input_file)
                if has_audio:
                    audio_duration = await self.get_audio_duration(input_file)
                    attributes.append(DocumentAttributeAudio(
                        duration=int(audio_duration),
                        voice=False
                    ))
                
                # Upload with round video attributes
                await self.client.send_file(
                    event.chat_id,
                    output_file,
                    reply_to=event.message.id,
                    attributes=attributes,
                    supports_streaming=True,
                    video_note=True,
                    caption="🎥 Here's your round video!"
                )
                
                # Clean up
                await processing_msg.delete()
                
            except Exception as e:
                logger.exception("Round video conversion error:")
                await event.reply(f"❌ Error processing video: {str(e)}")
            finally:
                # Ensure temp files are cleaned up
                try:
                    if os.path.exists(input_file):
                        os.remove(input_file)
                    if os.path.exists(output_file):
                        os.remove(output_file)
                except:
                    pass
    
    async def convert_to_round_video(self, input_path, output_path):
        """Convert a regular video to round (circular) format"""
        # We use a thread pool to avoid blocking the event loop
        loop = asyncio.get_event_loop()
        return await loop.run_in_executor(None, self._convert_video, input_path, output_path)
    
    def _convert_video(self, input_path, output_path):
        """Alternative method using a simpler crop and circular overlay"""
        try:
            # Get video info
            probe_cmd = [
                'ffprobe', 
                '-v', 'error', 
                '-select_streams', 'v:0', 
                '-show_entries', 'stream=width,height,duration', 
                '-of', 'csv=p=0', 
                input_path
            ]
            
            result = subprocess.run(probe_cmd, capture_output=True, text=True, check=True)
            width, height, duration = map(float, result.stdout.strip().split(','))
            width, height = int(width), int(height)
            
            # Create square crop
            size = min(width, height)
            x_offset = (width - size) // 2
            y_offset = (height - size) // 2
            
            # Use crop and scale to make a square video (400x400 for Telegram)
            cmd = [
                'ffmpeg',
                '-i', input_path,
                '-vf', f'crop={size}:{size}:{x_offset}:{y_offset},scale=400:400',
                '-map', '0:v',
                '-map', '0:a?',
                '-c:v', 'libx264',
                '-preset', 'fast',
                '-pix_fmt', 'yuv420p',
                '-c:a', 'copy',
                '-y',
                output_path
            ]
            
            subprocess.run(cmd, capture_output=True, check=True)
            return True, duration
            
        except subprocess.CalledProcessError as e:
            logger.error(f"FFmpeg error: {e.stderr}")
            return False, 0
        except Exception as e:
            logger.exception(f"Error in video conversion: {str(e)}")
            return False, 0
    
    async def get_video_dimensions(self, video_path):
        """Get video dimensions using ffprobe"""
        try:
            cmd = [
                'ffprobe',
                '-v', 'error',
                '-select_streams', 'v:0',
                '-show_entries', 'stream=width,height',
                '-of', 'csv=p=0',
                video_path
            ]
            result = await asyncio.create_subprocess_exec(
                *cmd,
                stdout=asyncio.subprocess.PIPE,
                stderr=asyncio.subprocess.PIPE
            )
            stdout, _ = await result.communicate()
            width, height = map(int, stdout.decode().strip().split(','))
            return width, height
        except Exception as e:
            logger.error(f"Error getting video dimensions: {str(e)}")
            return 400, 400  # Default to Telegram's round video size
    
    async def has_audio(self, video_path):
        """Check if video has audio stream"""
        try:
            cmd = [
                'ffprobe',
                '-v', 'error',
                '-select_streams', 'a',
                '-show_entries', 'stream=codec_type',
                '-of', 'csv=p=0',
                video_path
            ]
            result = await asyncio.create_subprocess_exec(
                *cmd,
                stdout=asyncio.subprocess.PIPE,
                stderr=asyncio.subprocess.PIPE
            )
            stdout, _ = await result.communicate()
            return bool(stdout.strip())
        except Exception as e:
            logger.error(f"Error checking audio: {str(e)}")
            return False
    
    async def get_audio_duration(self, video_path):
        """Get audio duration using ffprobe"""
        try:
            cmd = [
                'ffprobe',
                '-v', 'error',
                '-select_streams', 'a:0',
                '-show_entries', 'stream=duration',
                '-of', 'csv=p=0',
                video_path
            ]
            result = await asyncio.create_subprocess_exec(
                *cmd,
                stdout=asyncio.subprocess.PIPE,
                stderr=asyncio.subprocess.PIPE
            )
            stdout, _ = await result.communicate()
            duration_str = stdout.decode().strip()
            return int(float(duration_str))  # Convert to float then to integer
        except Exception as e:
            logger.error(f"Error getting audio duration: {str(e)}")
            return 0
