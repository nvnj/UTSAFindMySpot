import React, { useEffect, useState } from 'react';
import axios from 'axios';

export default function BuildingSelector({ selectedBuilding, onChange, selectedPermit }) {
    const [buildings, setBuildings] = useState([]);
    const [nearestParking, setNearestParking] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        fetchBuildings();
    }, []);

    useEffect(() => {
        if (selectedBuilding) {
            fetchNearestParking();
        } else {
            setNearestParking([]);
        }
    }, [selectedBuilding, selectedPermit]);

    const fetchBuildings = async () => {
        try {
            const response = await axios.get('/api/buildings');
            setBuildings(response.data.data);
        } catch (error) {
            console.error('Failed to fetch buildings:', error);
        }
    };

    const fetchNearestParking = async () => {
        if (!selectedBuilding) return;

        setLoading(true);
        try {
            const permitParam = selectedPermit ? `?permit=${selectedPermit}` : '';
            const response = await axios.get(`/api/buildings/${selectedBuilding}/nearest-parking${permitParam}`);
            setNearestParking(response.data.data);
        } catch (error) {
            console.error('Failed to fetch nearest parking:', error);
        } finally {
            setLoading(false);
        }
    };

    const groupedBuildings = buildings.reduce((acc, building) => {
        if (!acc[building.category]) {
            acc[building.category] = [];
        }
        acc[building.category].push(building);
        return acc;
    }, {});

    return (
        <div className="space-y-4">
            {/* Building Selector */}
            <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                <label htmlFor="building-select" className="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Going to:
                </label>
                <select
                    id="building-select"
                    value={selectedBuilding || ''}
                    onChange={(e) => onChange(e.target.value ? parseInt(e.target.value) : null)}
                    className="block w-full rounded-md border-gray-300 py-2 px-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:w-auto"
                >
                    <option value="">Select a building...</option>
                    {Object.entries(groupedBuildings).map(([category, categoryBuildings]) => (
                        <optgroup key={category} label={category}>
                            {categoryBuildings.map((building) => (
                                <option key={building.id} value={building.id}>
                                    {building.name} ({building.code})
                                </option>
                            ))}
                        </optgroup>
                    ))}
                </select>
            </div>

            {/* Nearest Parking Results */}
            {selectedBuilding && (
                <div className="w-full rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <h3 className="mb-3 text-lg font-semibold text-blue-900 dark:text-blue-100">
                        🎯 Nearest Available Parking
                    </h3>

                    {loading ? (
                        <div className="flex items-center justify-center py-4">
                            <div className="size-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                        </div>
                    ) : nearestParking.length > 0 ? (
                        <div className="grid gap-2 sm:grid-cols-1">
                            {nearestParking.map((parking, index) => (
                                <div
                                    key={`${parking.type}-${parking.id}`}
                                    className="flex flex-col gap-2 rounded-md bg-white p-3 shadow-sm dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div className="flex-1">
                                        <div className="flex items-center gap-2">
                                            {index === 0 && (
                                                <span className="rounded-full bg-green-500 px-2 py-0.5 text-xs font-bold text-white">
                                                    CLOSEST
                                                </span>
                                            )}
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {parking.name}
                                            </span>
                                        </div>
                                        <div className="mt-1 flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                                            <span>📍 {parking.distance_feet}ft away</span>
                                            <span>🚶 {parking.walk_time_minutes} min walk</span>
                                            <span className="font-medium text-green-600 dark:text-green-400">
                                                {parking.available_spots} spots available
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => {
                                            const url = `https://www.google.com/maps/dir/?api=1&destination=${parking.latitude},${parking.longitude}`;
                                            window.open(url, '_blank');
                                        }}
                                        className="ml-3 rounded-md bg-blue-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-600"
                                    >
                                        Navigate
                                    </button>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            No available parking found for your permit type.
                        </p>
                    )}
                </div>
            )}
        </div>
    );
}
