# ParkUTSA API Documentation

Version: 1.0
Base URL: `http://localhost/api`

## Overview

The ParkUTSA API provides real-time access to campus parking information including lot/garage availability, spot-level data, and campus alerts.

## Authentication

Currently, the API does not require authentication. For production deployment, implement API tokens or OAuth.

## Endpoints

### Parking Lots

#### GET /api/lots
Get all active parking lots with current availability.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Lot B1",
      "location": "Near Biotechnology Sciences Building",
      "latitude": 29.584600,
      "longitude": -98.619300,
      "type": "open",
      "total_spots": 150,
      "available_spots": 45,
      "occupancy_percentage": 70.00,
      "status": "available",
      "alerts": []
    }
  ]
}
```

#### GET /api/lots/{id}
Get detailed information about a specific lot including spots.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Lot B1",
    "location": "Near Biotechnology Sciences Building",
    "latitude": 29.584600,
    "longitude": -98.619300,
    "type": "open",
    "total_spots": 150,
    "available_spots": 45,
    "occupancy_percentage": 70.00,
    "status": "available",
    "spots": [...],
    "alerts": [...]
  }
}
```

### Parking Garages

#### GET /api/garages
Get all active parking garages with current availability.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Chaparral Parking Garage",
      "location": "1604 Campus Drive",
      "latitude": 29.584900,
      "longitude": -98.620100,
      "levels": 5,
      "total_spots": 1200,
      "available_spots": 450,
      "occupancy_percentage": 62.50,
      "status": "available",
      "alerts": []
    }
  ]
}
```

#### GET /api/garages/{id}
Get detailed information about a specific garage including spots.

---

### Data Ingestion

#### POST /api/update_camera
Submit camera-based occupancy detection results.

**Request Body:**
```json
{
  "reports": [
    {
      "lot_id": 1,
      "spot_id": 5,
      "occupied": true,
      "camera_id": "CAM001"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Camera reports processed",
  "updated_spots": 1,
  "spots": [
    {
      "spot_id": 5,
      "lot_id": 1,
      "occupied": true
    }
  ],
  "errors": []
}
```

#### POST /api/update_gps
Submit GPS-based parking event (user parked/unparked).

**Request Body:**
```json
{
  "lot_id": 1,
  "spot_id": 5,
  "parked": true,
  "latitude": 29.584600,
  "longitude": -98.619300
}
```

**Response:**
```json
{
  "success": true,
  "message": "GPS report processed",
  "data": {
    "spot_updated": true,
    "spot_id": 5,
    "occupied": true
  }
}
```

#### POST /api/update_entry_exit
Submit garage entry/exit event.

**Request Body:**
```json
{
  "garage_id": 1,
  "action": "entry"
}
```

**Valid actions:** `entry`, `exit`

**Response:**
```json
{
  "success": true,
  "message": "Entry/exit event processed",
  "data": {
    "garage_id": 1,
    "action": "entry",
    "available_spots": 449,
    "total_spots": 1200,
    "occupancy_percentage": 62.58
  }
}
```

---

### Alerts

#### GET /api/alerts
Get all alerts (active and inactive).

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "lot_id": 1,
      "garage_id": null,
      "alert_type": "event",
      "title": "Football Game Parking Restrictions",
      "details": "This lot is reserved for football game attendees.",
      "start_time": "2025-10-26T19:02:00.000000Z",
      "end_time": "2025-10-27T01:02:00.000000Z",
      "is_active": true,
      "lot": {...},
      "garage": null
    }
  ]
}
```

#### GET /api/alerts/active
Get only currently active alerts.

#### POST /api/alerts
Create a new alert.

**Request Body:**
```json
{
  "lot_id": 1,
  "garage_id": null,
  "alert_type": "construction",
  "title": "Lot Maintenance",
  "details": "Northern section temporarily closed",
  "start_time": "2025-10-24T08:00:00Z",
  "end_time": "2025-10-26T17:00:00Z",
  "is_active": true
}
```

**Valid alert types:** `closure`, `construction`, `event`, `maintenance`, `full`

**Response:**
```json
{
  "success": true,
  "message": "Alert created successfully",
  "data": {...}
}
```

#### PUT /api/alerts/{id}
Update an existing alert.

#### DELETE /api/alerts/{id}
Delete an alert.

---

## Error Responses

All endpoints return standard error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

**Common HTTP Status Codes:**
- 200: Success
- 201: Created
- 400: Bad Request
- 404: Not Found
- 422: Validation Error
- 500: Server Error

---

## Rate Limiting

Currently no rate limiting is implemented. For production, consider implementing rate limiting.

## Data Models

### Lot
- id: integer
- name: string
- location: string
- latitude: decimal(10,7)
- longitude: decimal(10,7)
- total_spots: integer
- available_spots: integer
- type: enum('open', 'covered', 'reserved')
- is_active: boolean

### Garage
- id: integer
- name: string
- location: string
- latitude: decimal(10,7)
- longitude: decimal(10,7)
- levels: integer
- total_spots: integer
- available_spots: integer
- is_active: boolean

### Spot
- id: integer
- lot_id: integer (nullable)
- garage_id: integer (nullable)
- spot_number: string
- level: integer (nullable)
- occupied: boolean
- last_updated_at: timestamp

### Alert
- id: integer
- lot_id: integer (nullable)
- garage_id: integer (nullable)
- alert_type: enum
- title: string
- details: text
- start_time: timestamp
- end_time: timestamp (nullable)
- is_active: boolean
