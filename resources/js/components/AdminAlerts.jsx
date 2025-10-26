import { useEffect, useState } from 'react';
import axios from 'axios';

export default function AdminAlerts() {
    const [alerts, setAlerts] = useState([]);
    const [lots, setLots] = useState([]);
    const [garages, setGarages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingAlert, setEditingAlert] = useState(null);
    const [formData, setFormData] = useState({
        lot_id: '',
        garage_id: '',
        alert_type: 'event',
        title: '',
        details: '',
        start_time: '',
        end_time: '',
        is_active: true,
    });

    useEffect(() => {
        fetchAlerts();
        fetchLotsAndGarages();
    }, []);

    const fetchAlerts = async () => {
        try {
            const response = await axios.get('/api/alerts');
            setAlerts(response.data.data);
            setLoading(false);
        } catch (error) {
            console.error('Error fetching alerts:', error);
            setLoading(false);
        }
    };

    const fetchLotsAndGarages = async () => {
        try {
            const [lotsRes, garagesRes] = await Promise.all([
                axios.get('/api/lots'),
                axios.get('/api/garages'),
            ]);
            setLots(lotsRes.data.data);
            setGarages(garagesRes.data.data);
        } catch (error) {
            console.error('Error fetching lots and garages:', error);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            if (editingAlert) {
                await axios.put(`/api/alerts/${editingAlert.id}`, formData);
            } else {
                await axios.post('/api/alerts', formData);
            }
            fetchAlerts();
            resetForm();
        } catch (error) {
            console.error('Error saving alert:', error);
            alert('Error saving alert: ' + (error.response?.data?.message || 'Unknown error'));
        }
    };

    const handleEdit = (alert) => {
        setEditingAlert(alert);
        setFormData({
            lot_id: alert.lot_id || '',
            garage_id: alert.garage_id || '',
            alert_type: alert.alert_type,
            title: alert.title,
            details: alert.details || '',
            start_time: alert.start_time ? new Date(alert.start_time).toISOString().slice(0, 16) : '',
            end_time: alert.end_time ? new Date(alert.end_time).toISOString().slice(0, 16) : '',
            is_active: alert.is_active,
        });
        setShowModal(true);
    };

    const handleDelete = async (id) => {
        if (!confirm('Are you sure you want to delete this alert?')) return;

        try {
            await axios.delete(`/api/alerts/${id}`);
            fetchAlerts();
        } catch (error) {
            console.error('Error deleting alert:', error);
            alert('Error deleting alert');
        }
    };

    const resetForm = () => {
        setFormData({
            lot_id: '',
            garage_id: '',
            alert_type: 'event',
            title: '',
            details: '',
            start_time: '',
            end_time: '',
            is_active: true,
        });
        setEditingAlert(null);
        setShowModal(false);
    };

    const alertTypeColors = {
        closure: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        construction: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        event: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        maintenance: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        full: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    };

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center">
                <div className="text-lg">Loading alerts...</div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-50 p-4 dark:bg-gray-900 sm:p-6 lg:p-8">
            <div className="mx-auto max-w-7xl">
                <div className="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900 dark:text-white">Alert Management</h1>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Create and manage parking alerts for lots and garages
                        </p>
                    </div>
                    <button
                        onClick={() => setShowModal(true)}
                        className="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Create New Alert
                    </button>
                </div>

                <div className="rounded-lg bg-white shadow dark:bg-gray-800">
                    <div className="overflow-x-auto">
                        <table className="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead className="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Type
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Title
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Location
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Start Time
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        End Time
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                {alerts.length === 0 ? (
                                    <tr>
                                        <td colSpan="7" className="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No alerts found. Create one to get started.
                                        </td>
                                    </tr>
                                ) : (
                                    alerts.map((alert) => (
                                        <tr key={alert.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td className="whitespace-nowrap px-6 py-4">
                                                <span
                                                    className={`rounded-full px-2 py-1 text-xs font-semibold ${alertTypeColors[alert.alert_type]}`}
                                                >
                                                    {alert.alert_type}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                {alert.title}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {alert.lot ? alert.lot.name : alert.garage ? alert.garage.name : 'N/A'}
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {new Date(alert.start_time).toLocaleString()}
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {alert.end_time ? new Date(alert.end_time).toLocaleString() : 'N/A'}
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4">
                                                <span
                                                    className={`rounded-full px-2 py-1 text-xs font-semibold ${
                                                        alert.is_active
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
                                                    }`}
                                                >
                                                    {alert.is_active ? 'Active' : 'Inactive'}
                                                </span>
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                <button
                                                    onClick={() => handleEdit(alert)}
                                                    className="mr-2 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(alert.id)}
                                                    className="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
                    <div className="w-full max-w-2xl rounded-lg bg-white p-6 dark:bg-gray-800">
                        <h2 className="mb-4 text-2xl font-bold text-gray-900 dark:text-white">
                            {editingAlert ? 'Edit Alert' : 'Create New Alert'}
                        </h2>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Alert Type
                                    </label>
                                    <select
                                        value={formData.alert_type}
                                        onChange={(e) => setFormData({ ...formData, alert_type: e.target.value })}
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        required
                                    >
                                        <option value="closure">Closure</option>
                                        <option value="construction">Construction</option>
                                        <option value="event">Event</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="full">Full</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status
                                    </label>
                                    <select
                                        value={formData.is_active ? 'active' : 'inactive'}
                                        onChange={(e) =>
                                            setFormData({ ...formData, is_active: e.target.value === 'active' })
                                        }
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Title
                                </label>
                                <input
                                    type="text"
                                    value={formData.title}
                                    onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                    className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Details
                                </label>
                                <textarea
                                    value={formData.details}
                                    onChange={(e) => setFormData({ ...formData, details: e.target.value })}
                                    rows="3"
                                    className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Parking Lot
                                    </label>
                                    <select
                                        value={formData.lot_id}
                                        onChange={(e) =>
                                            setFormData({ ...formData, lot_id: e.target.value, garage_id: '' })
                                        }
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">Select a lot (optional)</option>
                                        {lots.map((lot) => (
                                            <option key={lot.id} value={lot.id}>
                                                {lot.lot_code} - {lot.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Parking Garage
                                    </label>
                                    <select
                                        value={formData.garage_id}
                                        onChange={(e) =>
                                            setFormData({ ...formData, garage_id: e.target.value, lot_id: '' })
                                        }
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">Select a garage (optional)</option>
                                        {garages.map((garage) => (
                                            <option key={garage.id} value={garage.id}>
                                                {garage.garage_code} - {garage.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Start Time
                                    </label>
                                    <input
                                        type="datetime-local"
                                        value={formData.start_time}
                                        onChange={(e) => setFormData({ ...formData, start_time: e.target.value })}
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        required
                                    />
                                </div>

                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        End Time (Optional)
                                    </label>
                                    <input
                                        type="datetime-local"
                                        value={formData.end_time}
                                        onChange={(e) => setFormData({ ...formData, end_time: e.target.value })}
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={resetForm}
                                    className="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    {editingAlert ? 'Update Alert' : 'Create Alert'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
