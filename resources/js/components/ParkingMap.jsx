import { useEffect, useState } from 'react';
import axios from 'axios';
import LotCard from './LotCard';
import GarageCard from './GarageCard';
import AlertBanner from './AlertBanner';
import InteractiveMap from './InteractiveMap';
import LotLayoutModal from './LotLayoutModal';
import PermitSelector from './PermitSelector';
import BuildingSelector from './BuildingSelector';

const API_URL = '/api';

export default function ParkingMap() {
    const [lots, setLots] = useState([]);
    const [garages, setGarages] = useState([]);
    const [alerts, setAlerts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [lastUpdate, setLastUpdate] = useState(new Date());
    const [filter, setFilter] = useState('all'); // all, lots, garages
    const [viewMode, setViewMode] = useState('cards'); // cards, map
    const [selectedLocation, setSelectedLocation] = useState(null);
    const [isLayoutModalOpen, setIsLayoutModalOpen] = useState(false);
    const [modalLocation, setModalLocation] = useState(null);
    const [modalType, setModalType] = useState('lot');
    const [selectedPermit, setSelectedPermit] = useState(() => {
        return localStorage.getItem('userPermit') || '';
    });
    const [selectedBuilding, setSelectedBuilding] = useState(() => {
        const saved = localStorage.getItem('selectedBuilding');
        return saved ? parseInt(saved) : null;
    });

    useEffect(() => {
        fetchData();
        const interval = setInterval(fetchData, 30000); // Refresh every 30 seconds

        return () => clearInterval(interval);
    }, [selectedPermit]);

    const fetchData = async () => {
        try {
            const permitParam = selectedPermit ? `?permit=${selectedPermit}` : '';
            const [lotsRes, garagesRes, alertsRes] = await Promise.all([
                axios.get(`${API_URL}/lots${permitParam}`),
                axios.get(`${API_URL}/garages${permitParam}`),
                axios.get(`${API_URL}/alerts/active`)
            ]);

            setLots(lotsRes.data.data);
            setGarages(garagesRes.data.data);
            setAlerts(alertsRes.data.data);
            setLastUpdate(new Date());
        } catch (error) {
            console.error('Failed to fetch parking data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handlePermitChange = (permit) => {
        setSelectedPermit(permit);
        localStorage.setItem('userPermit', permit);
    };

    const handleBuildingChange = (buildingId) => {
        setSelectedBuilding(buildingId);
        if (buildingId) {
            localStorage.setItem('selectedBuilding', buildingId);
        } else {
            localStorage.removeItem('selectedBuilding');
        }
    };

    const getStatusColor = (occupancyPercentage) => {
        if (occupancyPercentage >= 90) return 'bg-red-500';
        if (occupancyPercentage >= 70) return 'bg-orange-500';
        if (occupancyPercentage >= 50) return 'bg-yellow-500';
        return 'bg-green-500';
    };

    const handleLocationClick = (location, type) => {
        setModalLocation(location);
        setModalType(type);
        setIsLayoutModalOpen(true);
        setSelectedLocation(location);
    };

    const handleCardClick = (location, type) => {
        handleLocationClick(location, type);
    };

    const filteredLots = filter === 'garages' ? [] : lots;
    const filteredGarages = filter === 'lots' ? [] : garages;

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-gray-100 dark:bg-gray-900">
                <div className="text-center">
                    <div className="mx-auto mb-4 size-16 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                    <p className="text-gray-600 dark:text-gray-400">Loading parking data...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            {/* Header */}
            <header className="bg-white shadow dark:bg-gray-800">
                <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                                Dashboard
                            </h1>
    
                        </div>
                        <div className="text-right">
                            <button
                                onClick={fetchData}
                                className="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                            >
                                Refresh
                            </button>
                            <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Last updated: {lastUpdate.toLocaleTimeString()}
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            {/* Active Alerts */}
            {alerts.length > 0 && (
                <div className="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <div className="space-y-2">
                        {alerts.map((alert) => (
                            <AlertBanner key={alert.id} alert={alert} />
                        ))}
                    </div>
                </div>
            )}

            {/* Filter Tabs & View Toggle */}
            <div className="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div className="space-y-4">
                    {/* Permit & Building Selectors */}
                    <div className="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                        <div className="space-y-4">
                            <PermitSelector selectedPermit={selectedPermit} onChange={handlePermitChange} />
                            <BuildingSelector
                                selectedBuilding={selectedBuilding}
                                onChange={handleBuildingChange}
                                selectedPermit={selectedPermit}
                            />
                        </div>
                    </div>

                    {/* View Mode Toggle */}
                    <div className="flex justify-center gap-2 rounded-lg bg-white p-2 shadow dark:bg-gray-800">
                        <button
                            onClick={() => setViewMode('cards')}
                            className={`flex items-center gap-2 rounded-md px-6 py-2 font-medium transition ${
                                viewMode === 'cards'
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            <svg className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Card View
                        </button>
                        <button
                            onClick={() => setViewMode('map')}
                            className={`flex items-center gap-2 rounded-md px-6 py-2 font-medium transition ${
                                viewMode === 'map'
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            <svg className="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Map View
                        </button>
                    </div>

                    {/* Filter Tabs */}
                    <div className="flex gap-4 rounded-lg bg-white p-2 shadow dark:bg-gray-800">
                        <button
                            onClick={() => setFilter('all')}
                            className={`flex-1 rounded-md px-4 py-2 font-medium transition ${
                                filter === 'all'
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            All Parking
                        </button>
                        <button
                            onClick={() => setFilter('lots')}
                            className={`flex-1 rounded-md px-4 py-2 font-medium transition ${
                                filter === 'lots'
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            Open Lots ({lots.length})
                        </button>
                        <button
                            onClick={() => setFilter('garages')}
                            className={`flex-1 rounded-md px-4 py-2 font-medium transition ${
                                filter === 'garages'
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                            }`}
                        >
                            Parking Garages ({garages.length})
                        </button>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {viewMode === 'map' ? (
                    /* Map View */
                    <InteractiveMap
                        lots={filteredLots}
                        garages={filteredGarages}
                        onLocationClick={handleLocationClick}
                        selectedLocation={selectedLocation}
                    />
                ) : (
                    /* Card View */
                    <>
                        {/* Parking Lots */}
                        {filteredLots.length > 0 && (
                            <section className="mb-8">
                                <h2 className="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
                                    Open Parking Lots
                                </h2>
                                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                    {filteredLots.map((lot) => (
                                        <div
                                            key={lot.id}
                                            onClick={() => handleCardClick(lot, 'lot')}
                                            className="cursor-pointer transition-transform hover:scale-105"
                                        >
                                            <LotCard
                                                lot={lot}
                                                statusColor={getStatusColor(lot.occupancy_percentage)}
                                            />
                                        </div>
                                    ))}
                                </div>
                            </section>
                        )}

                        {/* Parking Garages */}
                        {filteredGarages.length > 0 && (
                            <section>
                                <h2 className="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
                                    Parking Garages
                                </h2>
                                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                    {filteredGarages.map((garage) => (
                                        <div
                                            key={garage.id}
                                            onClick={() => handleCardClick(garage, 'garage')}
                                            className="cursor-pointer transition-transform hover:scale-105"
                                        >
                                            <GarageCard
                                                garage={garage}
                                                statusColor={getStatusColor(garage.occupancy_percentage)}
                                            />
                                        </div>
                                    ))}
                                </div>
                            </section>
                        )}
                    </>
                )}
            </main>

            {/* Lot Layout Modal */}
            <LotLayoutModal
                location={modalLocation}
                type={modalType}
                isOpen={isLayoutModalOpen}
                onClose={() => {
                    setIsLayoutModalOpen(false);
                    setModalLocation(null);
                    setSelectedLocation(null);
                }}
            />

            {/* Footer */}
            <footer className="mt-12 bg-white py-6 shadow dark:bg-gray-800">
                <div className="mx-auto max-w-7xl px-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>UTSAFindMySpot - Smart Campus Parking System</p>
                    <p className="mt-1">Real-time data from cameras, GPS, and entry/exit sensors</p>
                </div>
            </footer>
        </div>
    );
}
