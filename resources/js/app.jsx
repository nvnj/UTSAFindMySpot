import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import ParkingMap from './components/ParkingMap';
import AdminAlerts from './components/AdminAlerts';

function App() {
    return (
        <Router>
            <nav className="bg-blue-600 p-4 text-white shadow-md">
                <div className="container mx-auto flex items-center justify-between">
                    <Link to="/" className="flex items-center gap-3">
                        <img
                            src="/findmyspot.png"
                            alt="UTSAFindMySpot Logo"
                            className="h-10 w-10 rounded-lg"
                        />
                        <span className="text-xl font-bold">UTSAFindMySpot</span>
                    </Link>
                    <div className="flex gap-4">
                        <Link
                            to="/"
                            className="rounded px-3 py-2 transition-colors hover:bg-blue-700"
                        >
                            Parking Map
                        </Link>
                        <Link
                            to="/admin/alerts"
                            className="rounded px-3 py-2 transition-colors hover:bg-blue-700"
                        >
                            Admin Alerts
                        </Link>
                    </div>
                </div>
            </nav>
            <Routes>
                <Route path="/" element={<ParkingMap />} />
                <Route path="/admin/alerts" element={<AdminAlerts />} />
            </Routes>
        </Router>
    );
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
