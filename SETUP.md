# ParkUTSA Quick Setup Guide

## Prerequisites Checklist

- [ ] Docker Desktop installed and running
- [ ] Node.js 18+ installed
- [ ] npm installed
- [ ] Python 3.10+ installed (optional, for camera script)
- [ ] Git installed

## Quick Start (5 Minutes)

1. **Start Docker Services**
   ```bash
   ./vendor/bin/sail up -d
   ```

2. **Run Database Setup**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Build Frontend**
   ```bash
   npm run build
   ```

5. **Visit Application**
   - Open http://localhost in your browser
   - You should see the ParkUTSA dashboard with sample data

## Development Mode

For frontend development with hot reload:

```bash
npm run dev
```

Then visit http://localhost in your browser.

## Testing the System

### Test the API

```bash
# Get all lots
curl http://localhost/api/lots

# Get all garages
curl http://localhost/api/garages

# Get active alerts
curl http://localhost/api/alerts/active
```

### Test Camera Detection

```bash
cd scripts/python
pip install requests
python camera_detector.py
```

The script will simulate camera detections and POST to the API.

### Test GPS Reporter

Open `scripts/client/gps_reporter.html` in a web browser and allow location access.

## Troubleshooting

### Port Already in Use
If port 80 is already in use:

1. Edit `.env` and change `APP_PORT=8000`
2. Restart Sail: `./vendor/bin/sail down && ./vendor/bin/sail up -d`
3. Access at http://localhost:8000

### Database Connection Issues
```bash
# Reset everything
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```

### Frontend Not Loading
```bash
# Rebuild assets
npm run build

# Or clear cache
./vendor/bin/sail artisan optimize:clear
```

## Next Steps

1. Explore the [API Documentation](API_DOCUMENTATION.md)
2. Check the [main README](README.md) for architecture details
3. Modify seeders in `database/seeders/DatabaseSeeder.php` for your campus
4. Customize the frontend in `resources/js/components/`

## Stopping the Application

```bash
./vendor/bin/sail down
```

To stop and remove all data:
```bash
./vendor/bin/sail down -v
```
