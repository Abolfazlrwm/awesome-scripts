# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
from .id import IdPlugin
from .time import TimePlugin
from .ban import BanPlugin
from .unban import UnbanPlugin
from .markreadpv import MarkReadPVPlugin
from .markreadgroup import MarkReadGroupPlugin
from .markreadchannel import MarkReadChannelPlugin
from .banall import BanAllPlugin
from .delmessages import DelMessagesPlugin
from .ocr import OCRPlugin
from .resize import ResizePlugin
from .ephemeral_media import EphemeralMediaPlugin
from .mute import MutePlugin
from .dl import DownloadPlugin  # Import the DownloadPlugin
from .textformat import TextFormatPlugin
from .panel import PanelPlugin
from .typing import TypingPlugin
from .help import HelpPlugin
from .ping import PingPlugin
from .copier import CopierPlugin
from .filerename import FileRenamePlugin
from .translate import TranslationPlugin
from .finance import FinancePlugin
from .profile import ProfilePicCopier
from .online import OnlineModePlugin
from .timebio import TimeBioPlugin  # Import the FinancePlugin
from .timepfp import TimePfpPlugin
from .play import PlayPlugin
from .anti import AntiLoginPlugin
from .settings import SettingsPlugin  # Add to imports
from .plugin_manager import PluginManagerPlugin
from .answer import SetAnswerPlugin
from .welcome import WelcomePlugin
from .cast import BroadcastPlugin
from .screenshot import ScreenshotPlugin
from .zed import AntiBetrayalPlugin
from .enemy import EnemyManagerPlugin
from .bg import RemoveBgPlugin
from .autoaccept import AutoAcceptPlugin
from .manage import ContentLockPlugin
from .dump import DumpRawPlugin
from .comments import AutoCommentPlugin
from .tagall import TagAllPlugin
from .TelebotCraft import ForceJoinTelebotCraft
from .sign_mode import SignModePlugin
from .multiCleaner import CleanerFaEnPlugin
from .stream import StreamPlugin
from .rtmp import RTMPStreamPlugin
from .tts import PersianTTSPlugin
from .timename import TimeNamePlugin
from .tosticker import StickerImageConverterPlugin
from .Extractsticker import StickerExtractorPlugin  
from .pvlock import PVLockPlugin
from .tag import TagUsersPlugin  
from .leaveall import LeaveAllPlugin
from .warn import WarnSystemPlugin
from .spam import SpamPlugin
from .story import StoryDownloaderPlugin
from .roundvideo import RoundVideoPlugin
from .shazam import ShazamPlugin
from .instagram import InstagramDownloader
from .yt import YouTubeDLPlugin
from .upload import UploadKonPlugin
from .hack import GameeScorePlugin
from .deletelog import DeleteTrackerPlugin
from .captcha import CaptchaPlugin
from .dice import DicePlugin
from .save import UniversalChannelSaver
from .sticker import StickerManagerPlugin
from .gpt import GroqPlugin
from .dll import DownloadMediaPlugin
from .TelebotCraftChat import ForceJoinTelebotCraftChat
from .song import SongStreamPlugin
from .monshi import MonshiPlugin
from .aichat import AIChatPlugin
from .globalban import AccessManagerPlugin
from .post import PostDownloaderPlugin
from .autosender import AutoMsgPlugin
from .notbix import NobitexPlugin
__all__ = [
    'AntiBetrayalPlugin',
    'AntiLoginPlugin',
    'AutoAcceptPlugin',
    'AutoCommentPlugin',
    'BanAllPlugin',
    'BanPlugin',
    'BroadcastPlugin',
    'ContentLockPlugin',
    'CopierPlugin',
    'DelMessagesPlugin',
    'DownloadPlugin',
    'DumpRawPlugin',
    'EnemyManagerPlugin',
    'EphemeralMediaPlugin',
    'FileRenamePlugin',
    'FinancePlugin',
    'HelpPlugin',
    'IdPlugin',
    'MarkReadChannelPlugin',
    'MarkReadGroupPlugin',
    'MarkReadPVPlugin',
    'MutePlugin',
    'OCRPlugin',
    'OnlineModePlugin',
    'PanelPlugin',
    'PingPlugin',
    'PlayPlugin',
    'ProfilePicCopier',
    'RemoveBgPlugin',
    'ResizePlugin',
    'SetAnswerPlugin',
    'SettingsPlugin',
    'ScreenshotPlugin',
    'TagAllPlugin',
    'TextFormatPlugin',
    'TimeBioPlugin',
    'TimePfpPlugin',
    'TimePlugin',
    'TranslationPlugin',
    'TypingPlugin',
    'UnbanPlugin',
    'WelcomePlugin',
    # PluginManagerPlugin should be last as it manages plugins
    'PluginManagerPlugin',
    'ForceJoinTelebotCraft',
    'SignModePlugin',
    'CleanerFaEnPlugin',
    'StreamPlugin',
    'RTMPStreamPlugin',
    'PersianTTSPlugin',
    'TimeNamePlugin',
    'StickerImageConverterPlugin',
    'StickerExtractorPlugin',
    'PVLockPlugin',
    'TagUsersPlugin',
    'LeaveAllPlugin',
    'WarnSystemPlugin',
    'SpamPlugin',
    'ForceInvitePlugin',
    'StoryDownloaderPlugin',
    'RoundVideoPlugin',
    'ShazamPlugin',
    'InstagramDownloader',
    'YouTubeDLPlugin',
    'UploadKonPlugin',
    'GameeScorePlugin',
    'DeleteTrackerPlugin',
    'CaptchaPlugin',
    'UniversalChannelSaver',
    'DicePlugin',
    'StickerManagerPlugin',
    'GroqPlugin'
    'DownloadMediaPlugin',
    'ForceJoinTelebotCraftChat',
    'SongStreamPlugin',
    'MonshiPlugin',
    'AIChatPlugin',
    'AccessManagerPlugin',
    'PostDownloaderPlugin',
    'AutoMsgPlugin',
    'NobitexPlugin',
]

# Removed the redundant __all__.append('PingPlugin')