import { useEffect, useState } from 'react';
import axios from 'axios';

const SpotCell = ({ spot, onClick }) => {
    const getSpotColor = () => {
        if (spot.occupied) return 'bg-red-500 hover:bg-red-600';
        return 'bg-green-500 hover:bg-green-600';
    };

    const spotId = spot.spot_number.split('-')[1];

    return (
        <div
            className={`group relative flex aspect-square cursor-pointer items-center justify-center rounded transition-all ${getSpotColor()}`}
            onClick={() => !spot.occupied && onClick(spot)}
            title={`Spot ${spot.spot_number} - ${spot.occupied ? 'Occupied' : 'Available'}`}
        >
            <span className="text-sm font-bold text-white drop-shadow-lg">
                {spotId}
            </span>
            {!spot.occupied && (
                <div className="absolute -top-1 -right-1 size-2 animate-pulse rounded-full bg-white"></div>
            )}
        </div>
    );
};

export default function LotLayoutModal({ location, type, isOpen, onClose }) {
    const [spots, setSpots] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectedSpot, setSelectedSpot] = useState(null);

    useEffect(() => {
        if (isOpen && location) {
            fetchSpots();
            const interval = setInterval(fetchSpots, 5000); // Refresh every 5 seconds
            return () => clearInterval(interval);
        }
    }, [isOpen, location]);

    const fetchSpots = async () => {
        try {
            const endpoint = type === 'lot' ? `/api/lots/${location.id}` : `/api/garages/${location.id}`;
            const response = await axios.get(endpoint);
            setSpots(response.data.data.spots || []);
        } catch (error) {
            console.error('Failed to fetch spots:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleSpotClick = (spot) => {
        setSelectedSpot(spot);
    };

    const handleNavigate = () => {
        if (selectedSpot && location) {
            window.open(
                `https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}`,
                '_blank'
            );
        }
    };

    if (!isOpen) return null;

    // Calculate grid layout - aim for roughly square grid
    const totalSpots = spots.length;
    const cols = Math.ceil(Math.sqrt(totalSpots * 1.5)); // 1.5 ratio for parking lot shape
    const rows = Math.ceil(totalSpots / cols);

    const availableSpots = spots.filter(s => !s.occupied).length;
    const occupiedSpots = spots.filter(s => s.occupied).length;

    return (
        <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
            <div className="max-h-[90vh] w-full max-w-6xl overflow-auto rounded-lg bg-white shadow-2xl dark:bg-gray-800">
                {/* Header */}
                <div className="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4 dark:bg-gray-800">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                            {location?.name} - Layout
                        </h2>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            {location?.location}
                        </p>
                    </div>
                    <button
                        onClick={onClose}
                        className="rounded-full p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg className="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-3 gap-4 border-b p-6">
                    <div className="text-center">
                        <div className="text-3xl font-bold text-green-600">{availableSpots}</div>
                        <div className="text-sm text-gray-600 dark:text-gray-400">Available</div>
                    </div>
                    <div className="text-center">
                        <div className="text-3xl font-bold text-red-600">{occupiedSpots}</div>
                        <div className="text-sm text-gray-600 dark:text-gray-400">Occupied</div>
                    </div>
                    <div className="text-center">
                        <div className="text-3xl font-bold text-gray-900 dark:text-white">{totalSpots}</div>
                        <div className="text-sm text-gray-600 dark:text-gray-400">Total</div>
                    </div>
                </div>

                {/* Legend */}
                <div className="flex items-center justify-center gap-6 border-b bg-gray-50 px-6 py-3 dark:bg-gray-900">
                    <div className="flex items-center gap-2">
                        <div className="size-4 rounded bg-green-500"></div>
                        <span className="text-sm text-gray-700 dark:text-gray-300">Available</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="size-4 rounded bg-red-500"></div>
                        <span className="text-sm text-gray-700 dark:text-gray-300">Occupied</span>
                    </div>
                    <div className="text-sm text-gray-500">Click an available spot to navigate</div>
                </div>

                {/* Parking Grid */}
                <div className="p-6">
                    {loading ? (
                        <div className="flex h-64 items-center justify-center">
                            <div className="size-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                        </div>
                    ) : (
                        <>
                            {/* Entrance Marker */}
                            <div className="mb-4 flex items-center justify-center">
                                <div className="rounded-full bg-blue-500 px-4 py-2 text-sm font-semibold text-white">
                                    ↓ Entrance
                                </div>
                            </div>

                            {/* Spot Grid */}
                            <div
                                className="mx-auto grid gap-2"
                                style={{
                                    gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))`,
                                    maxWidth: `${cols * 60}px`,
                                }}
                            >
                                {spots.map((spot) => (
                                    <SpotCell key={spot.id} spot={spot} onClick={handleSpotClick} />
                                ))}
                            </div>

                            {/* Exit Marker */}
                            <div className="mt-4 flex items-center justify-center">
                                <div className="rounded-full bg-gray-500 px-4 py-2 text-sm font-semibold text-white">
                                    ↑ Exit
                                </div>
                            </div>
                        </>
                    )}
                </div>

                {/* Selected Spot Actions */}
                {selectedSpot && (
                    <div className="sticky bottom-0 border-t bg-blue-50 p-4 dark:bg-gray-900">
                        <div className="flex items-center justify-between">
                            <div>
                                <h4 className="font-semibold text-gray-900 dark:text-white">
                                    Selected: Spot {selectedSpot.spot_number}
                                </h4>
                                <p className="text-sm text-gray-600 dark:text-gray-400">
                                    This spot is currently available
                                </p>
                            </div>
                            <div className="flex gap-3">
                                <button
                                    onClick={() => setSelectedSpot(null)}
                                    className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold hover:bg-gray-100"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={handleNavigate}
                                    className="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600"
                                >
                                    <svg
                                        className="size-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
                                        />
                                    </svg>
                                    Navigate to {location.name}
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
