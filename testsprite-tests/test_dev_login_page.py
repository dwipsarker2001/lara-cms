import os
import requests

TARGET_URL = os.environ.get("TARGET_URL", "https://e22732bfea6a98b8-123-253-135-132.serveousercontent.com")

def test_dev_login_page():
    r = requests.get(f"{TARGET_URL}/dev-login", timeout=15)
    assert r.status_code == 200, f"Expected 200, got {r.status_code}"
    assert "login" in r.text.lower() or "csrf" in r.text.lower() or "password" in r.text.lower(), "Expected login form elements"

if __name__ == "__main__":
    test_dev_login_page()
    print("PASS: Dev login page accessible")
