#!/usr/bin/env python3
"""
YOLO Vehicle Detection to ParkUTSA API
Send detection results to the existing /api/update_camera endpoint

Usage:
1. Update API_URL with your friend's computer IP address
2. Set LOT_ID and CAMERA_ID
3. Run: python3 yolo_to_parkutsa.py
"""

import requests
import json
from datetime import datetime
from typing import List, Dict
import time

# ============================================================================
# CONFIGURATION - UPDATE THESE VALUES
# ============================================================================

# Your Laravel app IP address (from your computer)
# If on same network: use your computer's local IP (e.g., "http://172.21.116.15")
# If using ngrok/tunneling: use the tunnel URL (e.g., "https://abc123.ngrok.io")
API_URL = "http://172.21.116.15/api/update_camera"

# Parking lot ID from your database (run this query to find IDs):
# SELECT id, code, name FROM lots;
LOT_ID = 1  # Example: BK1 = 1, BK2 = 2, etc.

# Camera identifier
CAMERA_ID = "yolo_camera_001"

# Update interval (seconds) - how often to send data to API
UPDATE_INTERVAL = 5

# ============================================================================
# API FUNCTIONS
# ============================================================================

def send_camera_reports(reports: List[Dict]) -> bool:
    """
    Send camera detection reports to ParkUTSA API

    Args:
        reports: List of detection reports

    Returns:
        bool: True if successful, False otherwise
    """
    payload = {
        "reports": reports
    }

    try:
        response = requests.post(API_URL, json=payload, timeout=10)
        response.raise_for_status()

        result = response.json()
        if result.get('success'):
            print(f"✓ Successfully sent {len(reports)} reports")
            print(f"  Updated spots: {result.get('updated_spots', 0)}")
            return True
        else:
            print(f"✗ API returned error: {result.get('message')}")
            if result.get('errors'):
                for error in result['errors']:
                    print(f"    - {error}")
            return False

    except requests.exceptions.RequestException as e:
        print(f"✗ Connection error: {e}")
        print(f"  Make sure ParkUTSA is running at: {API_URL}")
        return False


def create_lot_level_report(lot_id: int, vehicle_count: int, total_spots: int, camera_id: str) -> List[Dict]:
    """
    Create reports for lot-level detection (when you don't have individual spot IDs)
    This sends one report indicating general occupancy

    Args:
        lot_id: Database ID of the parking lot
        vehicle_count: Number of vehicles detected
        total_spots: Total spots in the lot
        camera_id: Camera identifier

    Returns:
        List of reports to send
    """
    # Calculate if lot is more occupied than available
    occupied = vehicle_count >= (total_spots * 0.5)

    return [{
        "lot_id": lot_id,
        "spot_id": None,  # No specific spot
        "occupied": occupied,
        "camera_id": camera_id,
    }]


def create_spot_level_reports(lot_id: int, spot_detections: List[Dict], camera_id: str) -> List[Dict]:
    """
    Create reports for individual spot detection
    Use this if you can identify specific parking spots in the camera view

    Args:
        lot_id: Database ID of the parking lot
        spot_detections: List of {spot_id: int, occupied: bool}
        camera_id: Camera identifier

    Returns:
        List of reports to send
    """
    reports = []
    for detection in spot_detections:
        reports.append({
            "lot_id": lot_id,
            "spot_id": detection['spot_id'],
            "occupied": detection['occupied'],
            "camera_id": camera_id,
        })
    return reports


# ============================================================================
# YOLO DETECTION EXAMPLES
# ============================================================================

def simple_vehicle_count_example():
    """
    Simple example: Just count vehicles in the lot
    This uses lot-level detection (no individual spot tracking)
    """
    print("\n" + "="*60)
    print("Simple Vehicle Count Mode")
    print("="*60)

    # Simulated detection - replace with actual YOLO
    vehicle_count = 45
    total_spots = 120

    # Create and send report
    reports = create_lot_level_report(LOT_ID, vehicle_count, total_spots, CAMERA_ID)
    send_camera_reports(reports)


def yolo_realtime_detection():
    """
    Real-time YOLO detection example
    Continuously monitors camera feed and sends updates
    """
    try:
        from ultralytics import YOLO
        import cv2
    except ImportError:
        print("Error: ultralytics or opencv-python not installed")
        print("Install with: pip3 install ultralytics opencv-python")
        return

    print("\n" + "="*60)
    print("YOLO Real-time Detection Mode")
    print("="*60)
    print(f"API URL: {API_URL}")
    print(f"Lot ID: {LOT_ID}")
    print(f"Camera ID: {CAMERA_ID}")
    print("Press 'q' to quit")
    print("="*60 + "\n")

    # Load YOLO model
    print("Loading YOLOv11 model...")
    model = YOLO('yolov11n.pt')  # Use yolov11s.pt or yolov11m.pt for better accuracy

    # Video source (0 = webcam, or path to video file, or RTSP URL)
    video_source = 0
    cap = cv2.VideoCapture(video_source)

    if not cap.isOpened():
        print(f"Error: Cannot open video source: {video_source}")
        return

    frame_count = 0
    last_update_time = time.time()
    total_spots = 120  # Update this with actual lot capacity

    while cap.isOpened():
        success, frame = cap.read()
        if not success:
            print("End of video or camera disconnected")
            break

        # Run YOLO inference
        # Class IDs: 2=car, 5=bus, 7=truck (COCO dataset)
        results = model(frame, classes=[2, 5, 7], verbose=False)

        # Count detected vehicles
        vehicle_count = len(results[0].boxes)

        # Send update to API periodically
        current_time = time.time()
        if current_time - last_update_time >= UPDATE_INTERVAL:
            reports = create_lot_level_report(LOT_ID, vehicle_count, total_spots, CAMERA_ID)
            send_camera_reports(reports)
            last_update_time = current_time

        # Display results
        annotated_frame = results[0].plot()

        # Add info overlay
        cv2.putText(annotated_frame, f"Vehicles: {vehicle_count}", (10, 30),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
        cv2.putText(annotated_frame, f"Lot ID: {LOT_ID}", (10, 70),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.7, (255, 255, 255), 2)

        cv2.imshow(f"YOLO Detection - {CAMERA_ID}", annotated_frame)

        frame_count += 1

        # Press 'q' to quit
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()
    print("\nDetection stopped.")


def test_api_connection():
    """
    Test the API connection before running detection
    """
    print("\n" + "="*60)
    print("Testing API Connection")
    print("="*60)
    print(f"API URL: {API_URL}")
    print(f"Lot ID: {LOT_ID}")
    print(f"Camera ID: {CAMERA_ID}")
    print("="*60 + "\n")

    # Send a test report
    test_reports = [{
        "lot_id": LOT_ID,
        "spot_id": None,
        "occupied": True,
        "camera_id": f"{CAMERA_ID}_test",
    }]

    success = send_camera_reports(test_reports)

    if success:
        print("\n✓ API connection successful!")
        print("You can now run the detection system.")
    else:
        print("\n✗ API connection failed!")
        print("\nTroubleshooting:")
        print("1. Make sure ParkUTSA Laravel app is running")
        print("2. Check the API_URL is correct")
        print("3. Verify LOT_ID exists in database")
        print("4. If on different networks, use ngrok or similar tunneling")


# ============================================================================
# MAIN
# ============================================================================

if __name__ == "__main__":
    print("="*60)
    print("YOLO to ParkUTSA - Vehicle Detection System")
    print("="*60)

    # Show menu
    print("\nSelect mode:")
    print("1. Test API connection")
    print("2. Simple vehicle count example")
    print("3. Real-time YOLO detection")
    print("4. Exit")

    choice = input("\nEnter choice (1-4): ").strip()

    if choice == "1":
        test_api_connection()
    elif choice == "2":
        simple_vehicle_count_example()
    elif choice == "3":
        yolo_realtime_detection()
    elif choice == "4":
        print("Exiting...")
    else:
        print("Invalid choice")
