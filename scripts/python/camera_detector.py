#!/usr/bin/env python3
"""
ParkUTSA Camera-based Parking Spot Detection Script
Uses YOLO/OpenCV for real-time parking spot occupancy detection
POSTs results to Laravel API endpoint
"""

import time
import requests
import json
from typing import List, Dict
import random

# Configuration
API_BASE_URL = "http://localhost/api"
CAMERA_ID = "CAM001"
LOT_ID = 1
UPDATE_INTERVAL = 10  # seconds

class ParkingSpotDetector:
    """
    Simulates YOLO/OpenCV-based parking spot detection.
    In production, this would process actual camera feeds.
    """

    def __init__(self, api_url: str, camera_id: str, lot_id: int):
        self.api_url = api_url
        self.camera_id = camera_id
        self.lot_id = lot_id
        self.session = requests.Session()

    def detect_spots(self, num_spots: int = 10) -> List[Dict]:
        """
        Simulates parking spot detection from camera feed.
        In production, this would use YOLO/OpenCV to detect vehicles.

        Returns:
            List of spot detection results
        """
        detections = []

        for spot_id in range(1, num_spots + 1):
            # Simulate detection with random occupancy (80% chance of change)
            occupied = random.random() < 0.3

            detections.append({
                'lot_id': self.lot_id,
                'spot_id': spot_id,
                'occupied': occupied,
                'camera_id': self.camera_id
            })

        return detections

    def send_to_api(self, detections: List[Dict]) -> bool:
        """
        Sends detection results to Laravel API.

        Args:
            detections: List of spot detection results

        Returns:
            True if successful, False otherwise
        """
        try:
            payload = {'reports': detections}

            response = self.session.post(
                f"{self.api_url}/update_camera",
                json=payload,
                headers={'Content-Type': 'application/json'},
                timeout=10
            )

            response.raise_for_status()
            result = response.json()

            print(f"✓ Successfully updated {result.get('updated_spots', 0)} spots")

            if result.get('errors'):
                print(f"⚠ Errors: {len(result['errors'])}")
                for error in result['errors'][:3]:  # Show first 3 errors
                    print(f"  - {error.get('error', 'Unknown error')}")

            return result.get('success', False)

        except requests.exceptions.RequestException as e:
            print(f"✗ API request failed: {e}")
            return False
        except json.JSONDecodeError as e:
            print(f"✗ Invalid JSON response: {e}")
            return False

    def run(self, interval: int = 10):
        """
        Main detection loop.

        Args:
            interval: Seconds between detection cycles
        """
        print(f"Starting ParkUTSA Camera Detector")
        print(f"Camera ID: {self.camera_id}")
        print(f"Lot ID: {self.lot_id}")
        print(f"API URL: {self.api_url}")
        print(f"Update interval: {interval}s")
        print("-" * 50)

        cycle = 0

        try:
            while True:
                cycle += 1
                print(f"\n[Cycle {cycle}] Detecting parking spots...")

                # Detect spots (in production, this would process camera feed)
                detections = self.detect_spots(num_spots=10)

                occupied_count = sum(1 for d in detections if d['occupied'])
                print(f"Detected: {occupied_count}/{len(detections)} spots occupied")

                # Send to API
                success = self.send_to_api(detections)

                if not success:
                    print("⚠ Failed to update API, will retry next cycle")

                # Wait for next cycle
                time.sleep(interval)

        except KeyboardInterrupt:
            print("\n\nStopping detector...")
            print(f"Total cycles completed: {cycle}")


def main():
    """Entry point for the camera detector script."""
    detector = ParkingSpotDetector(
        api_url=API_BASE_URL,
        camera_id=CAMERA_ID,
        lot_id=LOT_ID
    )

    detector.run(interval=UPDATE_INTERVAL)


if __name__ == "__main__":
    main()
