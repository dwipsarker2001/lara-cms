import os
import requests

TARGET_URL = os.environ.get("TARGET_URL", "http://127.0.0.1:8000")

def test_home_page():
    r = requests.get(f"{TARGET_URL}/", timeout=10)
    assert r.status_code in [200, 404, 302], f"Unexpected status code {r.status_code}"

if __name__ == "__main__":
    test_home_page()
    print("PASS: Home page request completed")
