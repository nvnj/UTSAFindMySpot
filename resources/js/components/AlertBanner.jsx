export default function AlertBanner({ alert }) {
    const getAlertStyle = (type) => {
        switch (type) {
            case 'closure':
                return 'bg-red-100 border-red-500 text-red-900 dark:bg-red-900/20 dark:text-red-400';
            case 'construction':
                return 'bg-orange-100 border-orange-500 text-orange-900 dark:bg-orange-900/20 dark:text-orange-400';
            case 'maintenance':
                return 'bg-blue-100 border-blue-500 text-blue-900 dark:bg-blue-900/20 dark:text-blue-400';
            case 'event':
                return 'bg-purple-100 border-purple-500 text-purple-900 dark:bg-purple-900/20 dark:text-purple-400';
            default:
                return 'bg-yellow-100 border-yellow-500 text-yellow-900 dark:bg-yellow-900/20 dark:text-yellow-400';
        }
    };

    const getAlertIcon = (type) => {
        switch (type) {
            case 'closure':
                return '🚫';
            case 'construction':
                return '🚧';
            case 'maintenance':
                return '🔧';
            case 'event':
                return '📅';
            default:
                return '⚠️';
        }
    };

    const locationName = alert.lot?.name || alert.garage?.name || 'General';

    return (
        <div className={`rounded-lg border-l-4 p-4 ${getAlertStyle(alert.alert_type)}`}>
            <div className="flex items-start">
                <span className="mr-3 text-2xl">{getAlertIcon(alert.alert_type)}</span>
                <div className="flex-1">
                    <h4 className="font-semibold">
                        {alert.title} - {locationName}
                    </h4>
                    {alert.details && (
                        <p className="mt-1 text-sm opacity-90">{alert.details}</p>
                    )}
                    <p className="mt-2 text-xs opacity-75">
                        {new Date(alert.start_time).toLocaleString()}
                        {alert.end_time && ` - ${new Date(alert.end_time).toLocaleString()}`}
                    </p>
                </div>
            </div>
        </div>
    );
}
