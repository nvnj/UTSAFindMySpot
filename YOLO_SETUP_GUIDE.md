# YOLO to ParkUTSA Integration Guide

This guide shows how to send vehicle detection data from a YOLO model running on **any computer** to your ParkUTSA Laravel application.

## Overview

Your friend can run YOLO vehicle detection on their computer and send results to your ParkUTSA API running locally on your computer.

---

## Setup Options

### Option 1: Same WiFi Network (Recommended for Testing)

If both computers are on the same WiFi network, your friend can connect directly to your local IP.

**Your Computer's Local IP**: `172.21.116.15`

**Steps:**

1. **Make sure ParkUTSA is running** on your computer:
   ```bash
   ./vendor/bin/sail up -d
   ```

2. **Share your local IP** with your friend: `172.21.116.15`

3. **Your friend updates the script** (`yolo_to_parkutsa.py`):
   ```python
   API_URL = "http://172.21.116.15/api/update_camera"
   ```

4. **Test the connection**:
   ```bash
   python3 yolo_to_parkutsa.py
   # Select option 1: Test API connection
   ```

**Limitations**: Only works when on same network (home WiFi, campus WiFi, etc.)

---

### Option 2: Using ngrok (Works from Anywhere)

If your friend is on a different network (different WiFi, different location), use ngrok to create a tunnel.

**Steps:**

1. **Install ngrok** on your computer:
   - Download from: https://ngrok.com/download
   - Or install via snap: `sudo snap install ngrok`

2. **Create an account** at https://ngrok.com and get your auth token

3. **Set up ngrok**:
   ```bash
   ngrok config add-authtoken YOUR_AUTH_TOKEN
   ```

4. **Start the tunnel** (keep this running):
   ```bash
   ngrok http 80
   ```

5. **Copy the forwarding URL** (looks like `https://abc123.ngrok.io`)

6. **Share this URL** with your friend

7. **Your friend updates the script**:
   ```python
   API_URL = "https://abc123.ngrok.io/api/update_camera"
   ```

**Benefits**: Works from anywhere in the world!

---

### Option 3: Using Cloudflare Tunnel (Alternative to ngrok)

Free alternative to ngrok with no time limits.

1. **Install cloudflared**:
   ```bash
   wget https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
   sudo dpkg -i cloudflared-linux-amd64.deb
   ```

2. **Start the tunnel**:
   ```bash
   cloudflared tunnel --url http://localhost:80
   ```

3. **Copy the URL** and share with your friend

---

## For Your Friend (YOLO Computer)

### 1. Install Dependencies

```bash
# Install Python packages
pip3 install ultralytics opencv-python requests

# Download YOLO model (first run will auto-download)
python3 -c "from ultralytics import YOLO; YOLO('yolov11n.pt')"
```

### 2. Get the Script

Copy `yolo_to_parkutsa.py` to their computer.

### 3. Configure the Script

Edit `yolo_to_parkutsa.py`:

```python
# Update with your IP or ngrok URL
API_URL = "http://172.21.116.15/api/update_camera"  # or ngrok URL

# Get LOT_ID from database (you'll provide this)
LOT_ID = 1  # Example: 1=BK1, 2=BK2, etc.

# Camera identifier
CAMERA_ID = "friends_camera_001"

# How often to send updates (seconds)
UPDATE_INTERVAL = 5
```

### 4. Find the Lot ID

On **your computer**, run:

```bash
./vendor/bin/sail artisan tinker
```

Then in tinker:
```php
\App\Models\Lot::select('id', 'code', 'name')->get()
```

This shows all lot IDs. Share the correct ID with your friend.

Example output:
```
id: 1, code: "BK1", name: "Brackenridge Avenue Lot 1"
id: 2, code: "BK2", name: "Brackenridge Avenue Lot 2"
```

### 5. Run the Detection

```bash
python3 yolo_to_parkutsa.py
```

Select option:
- **1**: Test API connection (do this first!)
- **3**: Real-time YOLO detection

---

## API Endpoints Reference

### Existing: `/api/update_camera` (Recommended)

**Purpose**: Submit camera-based occupancy detection results

**Request**:
```json
{
  "reports": [
    {
      "lot_id": 1,
      "spot_id": null,
      "occupied": true,
      "camera_id": "camera_001"
    }
  ]
}
```

**Response**:
```json
{
  "success": true,
  "message": "Camera reports processed",
  "updated_spots": 1,
  "spots": [...],
  "errors": []
}
```

### New: `/api/update_vehicle_count` (Alternative)

**Purpose**: Send total vehicle count for a lot

**Request**:
```json
{
  "lot_code": "BK1",
  "vehicle_count": 45,
  "camera_id": "camera_001",
  "timestamp": "2025-10-25T20:45:00"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Vehicle count updated successfully",
  "lot": {
    "code": "BK1",
    "available_spots": 75,
    "total_spots": 120
  }
}
```

---

## Troubleshooting

### Connection Refused

**Problem**: Cannot connect to API

**Solutions**:
1. Make sure ParkUTSA is running: `./vendor/bin/sail ps`
2. Check firewall isn't blocking port 80
3. Verify IP address is correct
4. Try ngrok if on different networks

### Lot ID Not Found

**Problem**: API returns "lot not found"

**Solution**: Get the correct lot ID from database (see step 4 above)

### CORS Errors

**Problem**: Browser shows CORS error (if testing from web)

**Solution**: Already configured! CORS is enabled for all origins.

### SSL Certificate Error (ngrok)

**Problem**: `SSL: CERTIFICATE_VERIFY_FAILED`

**Solution**: ngrok URLs use HTTPS with valid certificates, should work fine. If issues:
```python
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
```

---

## Testing

### 1. Test API from your computer

```bash
curl -X POST http://localhost/api/update_camera \
  -H "Content-Type: application/json" \
  -d '{
    "reports": [
      {
        "lot_id": 1,
        "spot_id": null,
        "occupied": true,
        "camera_id": "test"
      }
    ]
  }'
```

### 2. Test from friend's computer

```bash
curl -X POST http://172.21.116.15/api/update_camera \
  -H "Content-Type: application/json" \
  -d '{
    "reports": [
      {
        "lot_id": 1,
        "spot_id": null,
        "occupied": true,
        "camera_id": "test"
      }
    ]
  }'
```

---

## Video Sources

Your friend can use various video sources:

```python
# Webcam
video_source = 0

# Video file
video_source = "/path/to/video.mp4"

# RTSP camera stream
video_source = "rtsp://username:password@camera-ip:554/stream"

# HTTP camera stream
video_source = "http://camera-ip/video"
```

---

## Summary

1. **On your computer**: Run `./vendor/bin/sail up -d`
2. **Get your IP**: `172.21.116.15` (or use ngrok for remote access)
3. **On friend's computer**: Install ultralytics, configure script, run detection
4. **Watch it work**: Real-time vehicle counts update your ParkUTSA database!

The system automatically updates the `available_spots` in your database based on detected vehicles.
