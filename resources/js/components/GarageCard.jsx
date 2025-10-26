export default function GarageCard({ garage, statusColor }) {
    return (
        <div className="overflow-hidden rounded-lg bg-white shadow-lg transition hover:shadow-xl dark:bg-gray-800">
            <div className={`h-2 ${statusColor}`}></div>

            <div className="p-6">
                <div className="mb-4 flex items-start justify-between">
                    <div>
                        <h3 className="text-xl font-bold text-gray-900 dark:text-white">
                            {garage.name}
                        </h3>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {garage.location}
                        </p>
                    </div>
                    <span className="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800">
                        {garage.levels} levels
                    </span>
                </div>

                <div className="mb-4">
                    <div className="flex items-end justify-between">
                        <span className="text-3xl font-bold text-gray-900 dark:text-white">
                            {garage.available_spots}
                        </span>
                        <span className="text-sm text-gray-500 dark:text-gray-400">
                            / {garage.total_spots} spots
                        </span>
                    </div>
                    <div className="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                        <div
                            className={`h-full ${statusColor}`}
                            style={{ width: `${garage.occupancy_percentage}%` }}
                        ></div>
                    </div>
                    <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {garage.occupancy_percentage}% occupied
                    </p>
                </div>

                {garage.alerts && garage.alerts.length > 0 && (
                    <div className="mt-4 rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/20">
                        <p className="text-xs font-medium text-yellow-800 dark:text-yellow-400">
                            ⚠️ {garage.alerts[0].title}
                        </p>
                    </div>
                )}

                <div className="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span className={`flex items-center gap-1 ${
                        garage.status === 'available' ? 'text-green-600' : 'text-red-600'
                    }`}>
                        <span className={`inline-block size-2 rounded-full ${
                            garage.status === 'available' ? 'bg-green-500' : 'bg-red-500'
                        }`}></span>
                        {garage.status === 'available' ? 'Available' : 'Full'}
                    </span>
                </div>
            </div>
        </div>
    );
}
