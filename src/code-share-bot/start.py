#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
🚀 CodeShareBot Startup Script
راه‌انداز ربات اشتراک‌گذاری کد

استفاده:
    python start.py
"""

import sys
import os
import logging
from pathlib import Path

# Add current directory to Python path
sys.path.insert(0, str(Path(__file__).parent))

def check_requirements():
    """بررسی وجود dependencies مورد نیاز"""
    required_packages = ['telegram', 'aiofiles', 'pygments']
    missing_packages = []
    
    for package in required_packages:
        try:
            __import__(package)
        except ImportError:
            missing_packages.append(package)
    
    if missing_packages:
        print("❌ پکیج‌های زیر نصب نشده‌اند:")
        for package in missing_packages:
            print(f"   - {package}")
        print("\n💡 برای نصب تمام dependencies از دستور زیر استفاده کنید:")
        print("   pip install -r requirements.txt")
        return False
    
    return True

def check_config():
    """بررسی تنظیمات ربات"""
    try:
        from config import BOT_TOKEN, ADMIN_IDS, validate_config
        
        if BOT_TOKEN == "YOUR_BOT_TOKEN_HERE":
            print("❌ توکن ربات تنظیم نشده است!")
            print("💡 لطفاً فایل config.py را ویرایش کنید و BOT_TOKEN را تنظیم کنید")
            return False
        
        if not ADMIN_IDS or ADMIN_IDS == [123456789]:
            print("⚠️ هشدار: آیدی ادمین تنظیم نشده است!")
            print("💡 لطفاً ADMIN_IDS را در فایل config.py تنظیم کنید")
        
        validate_config()
        return True
        
    except Exception as e:
        print(f"❌ خطا در بررسی تنظیمات: {e}")
        return False

def main():
    """تابع اصلی برای راه‌اندازی ربات"""
    print("🌟 CodeShareBot - ربات اشتراک‌گذاری کد")
    print("=" * 50)
    
    print("🔍 بررسی dependencies...")
    if not check_requirements():
        sys.exit(1)
    print("✅ تمام dependencies موجود است")
    
    print("🔧 بررسی تنظیمات...")
    if not check_config():
        sys.exit(1)
    print("✅ تنظیمات صحیح است")
    
    print("🚀 در حال راه‌اندازی ربات...")
    print("-" * 50)
    
    try:
        from bot import CodeShareBot
        bot = CodeShareBot()
        bot.run()
    except KeyboardInterrupt:
        print("\n⏹️ ربات متوقف شد")
    except Exception as e:
        print(f"\n❌ خطا در اجرای ربات: {e}")
        logging.exception("Bot startup error")
        sys.exit(1)

if __name__ == "__main__":
    main() 