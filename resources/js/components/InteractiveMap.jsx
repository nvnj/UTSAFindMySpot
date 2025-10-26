import { MapContainer, TileLayer, Marker, Popup, Tooltip, useMap } from 'react-leaflet';
import { icon } from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useEffect } from 'react';

// Fix for default marker icons in React-Leaflet
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

// Custom marker icons based on occupancy
const createCustomIcon = (color) => {
    return icon({
        iconUrl: `data:image/svg+xml;base64,${btoa(`
            <svg width="25" height="41" viewBox="0 0 25 41" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 9.4 12.5 28.5 12.5 28.5S25 21.9 25 12.5C25 5.6 19.4 0 12.5 0z" fill="${color}"/>
                <circle cx="12.5" cy="12.5" r="6" fill="white"/>
            </svg>
        `)}`,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
    });
};

const getMarkerColor = (occupancyPercentage, isActive = true) => {
    if (!isActive) return '#9ca3af'; // gray for closed/inactive
    if (occupancyPercentage >= 90) return '#ef4444'; // red
    if (occupancyPercentage >= 70) return '#f97316'; // orange
    if (occupancyPercentage >= 50) return '#eab308'; // yellow
    return '#22c55e'; // green
};

function MapUpdater({ center }) {
    const map = useMap();

    useEffect(() => {
        if (center) {
            map.setView(center, map.getZoom());
        }
    }, [center, map]);

    return null;
}

export default function InteractiveMap({ lots, garages, onLocationClick, selectedLocation }) {
    // UTSA Main Campus center
    const defaultCenter = [29.5846, -98.6193];
    const center = selectedLocation
        ? [parseFloat(selectedLocation.latitude), parseFloat(selectedLocation.longitude)]
        : defaultCenter;

    return (
        <div className="relative h-[600px] w-full overflow-hidden rounded-lg shadow-lg">
            <MapContainer
                center={defaultCenter}
                zoom={15}
                className="h-full w-full"
                style={{ height: '100%', width: '100%' }}
            >
                <TileLayer
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />

                <MapUpdater center={selectedLocation ? center : null} />

                {/* Parking Lot Markers */}
                {lots.map((lot) => {
                    const position = [parseFloat(lot.latitude), parseFloat(lot.longitude)];
                    const isActive = lot.status !== 'closed' && lot.available_spots >= 0;
                    const markerColor = getMarkerColor(lot.occupancy_percentage, isActive);

                    return (
                        <Marker
                            key={`lot-${lot.id}`}
                            position={position}
                            icon={createCustomIcon(markerColor)}
                            eventHandlers={{
                                click: () => onLocationClick(lot, 'lot'),
                            }}
                        >
                            <Tooltip permanent direction="top" offset={[0, -35]} className="font-semibold">
                                {lot.lot_code || lot.name}
                            </Tooltip>
                            <Popup>
                                <div className="min-w-[200px] p-2">
                                    <h3 className="mb-2 text-lg font-bold">{lot.name}</h3>
                                    <p className="mb-1 text-sm text-gray-600">{lot.location}</p>
                                    {!isActive && (
                                        <div className="mb-2 rounded bg-red-100 px-2 py-1 text-sm font-semibold text-red-800">
                                            ⚠️ LOT CLOSED
                                        </div>
                                    )}
                                    <div className="my-2 flex items-center justify-between">
                                        <span className="text-2xl font-bold text-gray-900">
                                            {isActive ? lot.available_spots : 0}
                                        </span>
                                        <span className="text-sm text-gray-500">
                                            / {lot.total_spots} spots
                                        </span>
                                    </div>
                                    <div className="mb-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            className="h-full"
                                            style={{
                                                width: `${lot.occupancy_percentage}%`,
                                                backgroundColor: markerColor,
                                            }}
                                        ></div>
                                    </div>
                                    <p className="mb-3 text-xs text-gray-500">
                                        {lot.occupancy_percentage}% occupied
                                    </p>
                                    <div className="flex gap-2">
                                        <button
                                            onClick={() => onLocationClick(lot, 'lot')}
                                            className="flex-1 rounded bg-blue-500 px-3 py-1.5 text-sm text-white hover:bg-blue-600"
                                        >
                                            View Layout
                                        </button>
                                        <button
                                            onClick={() => window.open(`https://www.google.com/maps/dir/?api=1&destination=${lot.latitude},${lot.longitude}`, '_blank')}
                                            className="flex-1 rounded bg-green-500 px-3 py-1.5 text-sm text-white hover:bg-green-600"
                                        >
                                            Navigate
                                        </button>
                                    </div>
                                </div>
                            </Popup>
                        </Marker>
                    );
                })}

                {/* Parking Garage Markers */}
                {garages.map((garage) => {
                    const position = [parseFloat(garage.latitude), parseFloat(garage.longitude)];
                    const markerColor = getMarkerColor(garage.occupancy_percentage);

                    return (
                        <Marker
                            key={`garage-${garage.id}`}
                            position={position}
                            icon={createCustomIcon(markerColor)}
                            eventHandlers={{
                                click: () => onLocationClick(garage, 'garage'),
                            }}
                        >
                            <Tooltip permanent direction="top" offset={[0, -35]} className="font-semibold">
                                {garage.garage_code || garage.name}
                            </Tooltip>
                            <Popup>
                                <div className="min-w-[200px] p-2">
                                    <h3 className="mb-2 text-lg font-bold">{garage.name}</h3>
                                    <p className="mb-1 text-sm text-gray-600">{garage.location}</p>
                                    <p className="mb-2 text-xs text-gray-500">{garage.levels} levels</p>
                                    <div className="my-2 flex items-center justify-between">
                                        <span className="text-2xl font-bold text-gray-900">
                                            {garage.available_spots}
                                        </span>
                                        <span className="text-sm text-gray-500">
                                            / {garage.total_spots} spots
                                        </span>
                                    </div>
                                    <div className="mb-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div
                                            className="h-full"
                                            style={{
                                                width: `${garage.occupancy_percentage}%`,
                                                backgroundColor: markerColor,
                                            }}
                                        ></div>
                                    </div>
                                    <p className="mb-3 text-xs text-gray-500">
                                        {garage.occupancy_percentage}% occupied
                                    </p>
                                    <div className="flex gap-2">
                                        <button
                                            onClick={() => onLocationClick(garage, 'garage')}
                                            className="flex-1 rounded bg-blue-500 px-3 py-1.5 text-sm text-white hover:bg-blue-600"
                                        >
                                            View Layout
                                        </button>
                                        <button
                                            onClick={() => window.open(`https://www.google.com/maps/dir/?api=1&destination=${garage.latitude},${garage.longitude}`, '_blank')}
                                            className="flex-1 rounded bg-green-500 px-3 py-1.5 text-sm text-white hover:bg-green-600"
                                        >
                                            Navigate
                                        </button>
                                    </div>
                                </div>
                            </Popup>
                        </Marker>
                    );
                })}
            </MapContainer>

            {/* Map Legend */}
            <div className="absolute bottom-4 right-4 z-[1000] rounded-lg bg-white p-3 shadow-lg dark:bg-gray-800">
                <h4 className="mb-2 text-sm font-semibold text-gray-900 dark:text-white">
                    Availability
                </h4>
                <div className="space-y-1">
                    <div className="flex items-center gap-2">
                        <div className="size-3 rounded-full bg-green-500"></div>
                        <span className="text-xs text-gray-700 dark:text-gray-300">&lt; 50% full</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="size-3 rounded-full bg-yellow-500"></div>
                        <span className="text-xs text-gray-700 dark:text-gray-300">50-70% full</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="size-3 rounded-full bg-orange-500"></div>
                        <span className="text-xs text-gray-700 dark:text-gray-300">70-90% full</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="size-3 rounded-full bg-red-500"></div>
                        <span className="text-xs text-gray-700 dark:text-gray-300">&gt; 90% full</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
