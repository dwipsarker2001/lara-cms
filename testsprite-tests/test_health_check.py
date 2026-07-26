import os
import requests

TARGET_URL = os.environ.get("TARGET_URL", "https://e22732bfea6a98b8-123-253-135-132.serveousercontent.com")

def test_health_check():
    r = requests.get(f"{TARGET_URL}/up", timeout=15)
    assert r.status_code == 200, f"Expected 200, got {r.status_code}"

if __name__ == "__main__":
    test_health_check()
    print("PASS: Health check succeeded")
