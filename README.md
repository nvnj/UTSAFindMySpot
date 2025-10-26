# ParkUTSA - Smart Campus Parking System

A fullstack real-time parking management system for UTSA campus, featuring computer vision detection, GPS reporting, and live availability tracking.

## Features

- **Real-time Parking Availability**: Live updates for all campus lots and parking garages
- **Computer Vision Detection**: YOLO/OpenCV-based camera detection for spot occupancy
- **GPS-based Reporting**: Mobile app integration for user-reported parking events
- **Garage Entry/Exit Tracking**: Real-time counting system for multi-level garages
- **Campus Alerts**: Event and maintenance alerts with map overlays
- **Interactive React Frontend**: Modern UI with live updates and filtering
- **RESTful API**: Comprehensive API for all parking data

## Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **MySQL** - Database (via Laravel Sail)
- **PHP 8.4** - Server-side language

### Frontend
- **React 18** - UI framework
- **Tailwind CSS 4** - Styling
- **Vite 7** - Build tool
- **Axios** - HTTP client

### Data Collection
- **Python 3.10+** - Camera detection script
- **HTML5/JavaScript** - GPS reporter client

## Installation

### Prerequisites
- Docker Desktop
- Node.js 18+ and npm
- Python 3.10+ (for camera detection script)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ParkUTSA
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Start Laravel Sail (Docker)**
   ```bash
   ./vendor/bin/sail up -d
   ```

6. **Run migrations and seed database**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   # Or for development with hot reload:
   npm run dev
   ```

8. **Access the application**
   - Frontend: http://localhost
   - API: http://localhost/api

## API Endpoints

### Parking Data (GET)
- `GET /api/lots` - List all parking lots
- `GET /api/lots/{id}` - Get specific lot details
- `GET /api/garages` - List all garages
- `GET /api/garages/{id}` - Get specific garage details

### Data Ingestion (POST)
- `POST /api/update_camera` - Submit camera detection results
- `POST /api/update_gps` - Report parking event via GPS
- `POST /api/update_entry_exit` - Report garage entry/exit

### Alerts (GET/POST/PUT/DELETE)
- `GET /api/alerts` - List all alerts
- `GET /api/alerts/active` - List active alerts
- `POST /api/alerts` - Create new alert
- `PUT /api/alerts/{id}` - Update alert
- `DELETE /api/alerts/{id}` - Delete alert

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed API documentation.

## Usage

### Running the Camera Detection Script

```bash
cd scripts/python
pip install -r requirements.txt
python camera_detector.py
```

### Using the GPS Reporter

Open `scripts/client/gps_reporter.html` in a web browser. The GPS reporter will request location permission and allow you to report parking events.

### Frontend Development

```bash
npm run dev  # Development with hot reload
npm run build  # Production build
```

## Database Schema

### Key Entities

- **Lots** - Open parking lots with capacity tracking
- **Garages** - Multi-level parking structures
- **Spots** - Individual parking spaces
- **CameraReports** - Camera detection history
- **GpsReports** - GPS event history
- **EntryEvents** - Garage entry/exit traffic
- **Alerts** - Campus notifications

## Seeded Sample Data

Includes realistic UTSA parking data:
- 5 Parking Lots (B1, B2, H4, VP3, MS2)
- 3 Parking Garages (Chaparral, Bauerle Road, Circle)
- Sample alerts for events, construction, and maintenance

## Testing

```bash
./vendor/bin/sail artisan test
```

## Code Formatting

```bash
./vendor/bin/sail pint
```

## Architecture

```
Camera (YOLO/OpenCV) ──> Python Script ──> Laravel API ──> MySQL
Mobile App GPS ──────────────────────────> Laravel API ──> MySQL
Garage Sensors ──────────────────────────> Laravel API ──> MySQL
                                               │
                                               ├──> React Frontend
                                               └──> External Integrations
```

## Future Enhancements

- Real YOLO/OpenCV integration
- Mobile app (React Native)
- Historical analytics dashboard
- Parking reservation system
- Push notifications
- Predictive ML models

## License

Open-sourced for educational purposes.
