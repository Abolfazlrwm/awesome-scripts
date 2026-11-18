# Telegram: @Abolfazl_rwm
import os
from http.server import HTTPServer, SimpleHTTPRequestHandler
import sys

class CORSRequestHandler(SimpleHTTPRequestHandler):
    def end_headers(self):
        # CORS is opt-in: export ALLOWED_ORIGIN to a single explicit origin
        # (e.g. https://lookup.example.com) to allow cross-origin reads.
        # Unset or invalid = no CORS header; the browser same-origin policy applies.
        allowed = os.environ.get("ALLOWED_ORIGIN", "").strip()
        if allowed.startswith(("http://", "https://")):
            self.send_header('Access-Control-Allow-Origin', allowed)
            self.send_header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        super().end_headers()

    def do_OPTIONS(self):
        self.send_response(200)
        self.end_headers()

def run_server(port=443):
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    server_address = ('', port)
    httpd = HTTPServer(server_address, CORSRequestHandler)
    
    print(f"Server running at http://localhost:{port}")
    print(f"Serving files from: {os.getcwd()}")
    print("Press Ctrl+C to stop the server")
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\nServer stopped")
        httpd.server_close()

if __name__ == "__main__":
    port = 85
    if len(sys.argv) > 1:
        try:
            port = int(sys.argv[1])
        except ValueError:
            print("Invalid port number. Using default port 443")
    
    run_server(port)
