# ==============================
# 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود
# Use, edit and redistribute only with attribution to @Abolfazl_rwm.
# ==============================
import hashlib
import requests
import re
import json
import time
import uuid
from urllib.parse import urlparse, urlunparse

class GameeScoreSubmitter:
    def __init__(self, game_url):
        # Clean the URL by removing fragments and parameters
        parsed = urlparse(game_url)
        self.clean_url = urlunparse(parsed._replace(query="", fragment=""))
        self.base_api_url = "https://api.gamee.com/"
        self.install_uuid = str(uuid.uuid4())
        self.headers = {
            "authority": "api.gamee.com",
            "accept": "*/*",
            "accept-language": "en-US,en;q=0.9",
            "content-type": "application/json",
            "origin": "https://prizes.gamee.com",
            "referer": "https://prizes.gamee.com/",
            "sec-ch-ua": '"Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-fetch-dest": "empty",
            "sec-fetch-mode": "cors",
            "sec-fetch-site": "same-site",
            "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
            "x-bot-header": "gamee",
            "x-install-uuid": self.install_uuid,
            "x-requested-with": "XMLHttpRequest",
            "client-language": "en"
        }
        
        if not self.validate_game_url():
            raise ValueError("Invalid Gamee game URL format")

    def validate_game_url(self):
        """Validate the game URL format after cleaning"""
        patterns = [
            r'https://prizes\.gamee\.com/game-bot/[a-zA-Z0-9_-]+',
            r'https://gamee\.com/game-bot/[a-zA-Z0-9_-]+',
            r'https://www\.gamee\.com/game-bot/[a-zA-Z0-9_-]+'
        ]
        return any(re.match(pattern, self.clean_url) for pattern in patterns)

    def extract_game_id(self):
        """Extract game ID from the cleaned URL"""
        try:
            # First try API method
            payload = {
                "jsonrpc": "2.0",
                "id": "game.getWebGameplayDetails",
                "method": "game.getWebGameplayDetails",
                "params": {"gameUrl": self.clean_url}
            }
            
            response = requests.post(self.base_api_url, headers=self.headers, json=payload)
            if response.status_code == 200:
                return response.json().get('result', {}).get('game', {}).get('id')
            
            # Fallback to regex extraction
            match = re.search(r'game-bot/([a-zA-Z0-9_-]+)', self.clean_url)
            if match:
                return match.group(1)
                
            return None
                
        except Exception as e:
            print(f"Error extracting game ID: {e}")
            return None

    def get_auth_token(self):
        """Get authentication token with improved error handling"""
        max_retries = 3
        for attempt in range(max_retries):
            try:
                payload = {
                    "jsonrpc": "2.0",
                    "id": "user.authentication.botLogin",
                    "method": "user.authentication.botLogin",
                    "params": {
                        "botName": "telegram",
                        "botGameUrl": self.clean_url,
                        "botUserIdentifier": str(uuid.uuid4()),
                        "clientInfo": {
                            "platform": "web",
                            "browser": "chrome",
                            "version": "122.0.0.0"
                        }
                    }
                }
                
                response = requests.post(
                    self.base_api_url,
                    headers=self.headers,
                    json=payload,
                    timeout=10
                )
                
                if response.status_code == 200:
                    result = response.json()
                    if 'result' in result and 'tokens' in result['result']:
                        return result['result']['tokens']['authenticate']
                    print("Unexpected response format:", result)
                    
                print(f"Auth request failed (attempt {attempt + 1}): Status {response.status_code}")
                print("Response:", response.text)
                
                if response.status_code == 429:
                    wait_time = (attempt + 1) * 2
                    time.sleep(wait_time)
                    continue
                    
            except Exception as e:
                print(f"Attempt {attempt + 1} failed: {e}")
                if attempt == max_retries - 1:
                    return None
                time.sleep(1)
                
        return None

    def generate_checksum(self, score, play_time):
        """Generate checksum using the clean URL"""
        secret_salt = "crmjbjm3lczhlgnek9uaxz2l9svlfjw14npauhen"
        str2hash = f"{score}:{play_time}:{self.clean_url}::{secret_salt}"
        return hashlib.md5(str2hash.encode()).hexdigest()

    def submit_score(self, score=100, play_time=30000, max_retries=3):
        """Submit score with comprehensive error handling"""
        if not isinstance(score, (int, float)) or score < 0:
            return {"error": "Score must be a positive number"}
            
        if not isinstance(play_time, int) or play_time <= 0:
            return {"error": "Play time must be a positive integer"}
            
        for attempt in range(max_retries):
            try:
                time.sleep(1)  # Basic rate limiting
                
                game_id = self.extract_game_id()
                if not game_id:
                    return {"error": "Could not determine game ID"}
                    
                auth_token = self.get_auth_token()
                if not auth_token:
                    return {"error": "Could not obtain authentication token"}
                
                submit_headers = self.headers.copy()
                submit_headers["authorization"] = f"Bearer {auth_token}"
                    
                checksum = self.generate_checksum(score, play_time)
                if not checksum:
                    return {"error": "Failed to generate checksum"}
                    
                payload = {
                    "jsonrpc": "2.0",
                    "id": "game.saveWebGameplay",
                    "method": "game.saveWebGameplay",
                    "params": {
                        "gameplayData": {
                            "gameId": game_id,
                            "score": int(score),
                            "playTime": int(play_time),
                            "gameUrl": self.clean_url,
                            "metadata": {"gameplayId": 30},
                            "releaseNumber": 8,
                            "gameStateData": None,
                            "createdTime": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
                            "checksum": checksum,
                            "replayVariant": None,
                            "replayData": None,
                            "replayDataChecksum": None,
                            "isSaveState": False,
                            "gameplayOrigin": "game",
                            "timestamp": int(time.time())
                        }
                    }
                }
                
                response = requests.post(
                    self.base_api_url,
                    headers=submit_headers,
                    json=payload,
                    timeout=10
                )
                
                if response.status_code == 200:
                    return response.json()
                elif response.status_code == 429:
                    wait_time = (attempt + 1) * 2
                    time.sleep(wait_time)
                    continue
                else:
                    return {"error": f"API returned status code {response.status_code}"}
                    
            except Exception as e:
                if attempt == max_retries - 1:
                    return {"error": str(e)}
                time.sleep(1)
                
        return {"error": "Max retries exceeded"}

# Example usage:
if __name__ == "__main__":
    main()