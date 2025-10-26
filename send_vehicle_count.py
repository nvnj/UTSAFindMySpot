#!/usr/bin/env python3
"""
Example script to send vehicle count to ParkUTSA API
This demonstrates how to integrate YOLO vehicle detection with the parking system
"""

import requests
import json
from datetime import datetime

# Configuration
API_URL = "http://localhost:80/api/update_vehicle_count"  # Change to your actual URL
LOT_CODE = "BK1"  # Example: Brackenridge Avenue Lot 1
CAMERA_ID = "camera_001"  # Optional: Identifier for your camera


def send_vehicle_count(lot_code: str, vehicle_count: int, camera_id: str = None):
    """
    Send vehicle count to ParkUTSA API

    Args:
        lot_code: The parking lot code (e.g., 'BK1', 'BK2', 'TAG')
        vehicle_count: Number of vehicles detected
        camera_id: Optional camera identifier

    Returns:
        dict: API response
    """
    payload = {
        "lot_code": lot_code,
        "vehicle_count": vehicle_count,
        "timestamp": datetime.now().isoformat(),
    }

    if camera_id:
        payload["camera_id"] = camera_id

    try:
        response = requests.post(API_URL, json=payload, timeout=10)
        response.raise_for_status()

        result = response.json()
        print(f"✓ Successfully updated {lot_code}: {vehicle_count} vehicles")
        print(f"  Available spots: {result['lot']['available_spots']}/{result['lot']['total_spots']}")
        return result

    except requests.exceptions.RequestException as e:
        print(f"✗ Error sending data: {e}")
        return None


def example_yolo_integration():
    """
    Example of how to integrate with YOLO vehicle detection
    Replace this with your actual YOLO detection code
    """
    # Simulated vehicle count from YOLO detection
    # In your actual code, this would come from YOLO model inference
    detected_vehicles = 45

    # Send to API
    send_vehicle_count(
        lot_code=LOT_CODE,
        vehicle_count=detected_vehicles,
        camera_id=CAMERA_ID
    )


def yolo_detection_loop_example():
    """
    Example of continuous monitoring with YOLO
    This shows how you might structure a real-time detection system
    """
    from ultralytics import YOLO
    import cv2

    # Load YOLO model
    model = YOLO('yolov11n.pt')  # or yolov11s.pt, yolov11m.pt, etc.

    # Video source (camera feed, video file, or RTSP stream)
    video_source = 0  # 0 for webcam, or path to video file, or RTSP URL
    cap = cv2.VideoCapture(video_source)

    frame_count = 0
    update_interval = 30  # Send update every 30 frames

    while cap.isOpened():
        success, frame = cap.read()
        if not success:
            break

        # Run YOLO inference
        results = model(frame, classes=[2, 5, 7])  # 2=car, 5=bus, 7=truck

        # Count vehicles
        vehicle_count = len(results[0].boxes)

        # Send update periodically
        if frame_count % update_interval == 0:
            send_vehicle_count(
                lot_code=LOT_CODE,
                vehicle_count=vehicle_count,
                camera_id=CAMERA_ID
            )

        frame_count += 1

        # Display results (optional)
        annotated_frame = results[0].plot()
        cv2.imshow(f"YOLO - {LOT_CODE}", annotated_frame)

        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()


def test_connection():
    """Test the API connection with a simple request"""
    print("Testing API connection...")
    send_vehicle_count(
        lot_code="BK1",
        vehicle_count=10,
        camera_id="test_camera"
    )


if __name__ == "__main__":
    # Simple test
    print("=" * 60)
    print("ParkUTSA Vehicle Count Sender")
    print("=" * 60)

    # Test the connection
    test_connection()

    # Uncomment one of these to run:
    # example_yolo_integration()
    # yolo_detection_loop_example()
