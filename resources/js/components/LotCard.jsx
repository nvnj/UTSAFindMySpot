export default function LotCard({ lot, statusColor }) {
    return (
        <div className="overflow-hidden rounded-lg bg-white shadow-lg transition hover:shadow-xl dark:bg-gray-800">
            <div className={`h-2 ${statusColor}`}></div>

            <div className="p-6">
                <div className="mb-4 flex items-start justify-between">
                    <div>
                        <h3 className="text-xl font-bold text-gray-900 dark:text-white">
                            {lot.name}
                        </h3>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {lot.location}
                        </p>
                    </div>
                    <span className={`rounded-full px-3 py-1 text-xs font-semibold ${
                        lot.type === 'reserved'
                            ? 'bg-purple-100 text-purple-800'
                            : lot.type === 'covered'
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-gray-100 text-gray-800'
                    }`}>
                        {lot.type}
                    </span>
                </div>

                <div className="mb-4">
                    <div className="flex items-end justify-between">
                        <span className="text-3xl font-bold text-gray-900 dark:text-white">
                            {lot.available_spots}
                        </span>
                        <span className="text-sm text-gray-500 dark:text-gray-400">
                            / {lot.total_spots} spots
                        </span>
                    </div>
                    <div className="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                        <div
                            className={`h-full ${statusColor}`}
                            style={{ width: `${lot.occupancy_percentage}%` }}
                        ></div>
                    </div>
                    <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {lot.occupancy_percentage}% occupied
                    </p>
                </div>

                {lot.alerts && lot.alerts.length > 0 && (
                    <div className="mt-4 rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/20">
                        <p className="text-xs font-medium text-yellow-800 dark:text-yellow-400">
                            ⚠️ {lot.alerts[0].title}
                        </p>
                    </div>
                )}

                <div className="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span className={`flex items-center gap-1 ${
                        lot.status === 'available' ? 'text-green-600' : 'text-red-600'
                    }`}>
                        <span className={`inline-block size-2 rounded-full ${
                            lot.status === 'available' ? 'bg-green-500' : 'bg-red-500'
                        }`}></span>
                        {lot.status === 'available' ? 'Available' : 'Full'}
                    </span>
                </div>
            </div>
        </div>
    );
}
