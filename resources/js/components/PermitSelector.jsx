import React from 'react';

const PERMIT_OPTIONS = [
    { value: '', label: 'All Parking (No Filter)' },
    { value: 'C', label: 'Commuter (C)' },
    { value: 'H', label: 'Housing Resident (H)' },
    { value: 'U', label: 'University Oaks (U)' },
    { value: 'SD', label: 'Student Dolorosa (SD)' },
    { value: 'Z', label: 'Twilight (Z)' },
    { value: 'M', label: 'Motorcycle (M)' },
    { value: 'RT', label: 'Resident Tobin Garage (RT)' },
    { value: 'ST', label: 'Student Tobin Garage (ST)' },
    { value: 'STR', label: 'Student Tobin Reserved (STR)' },
    { value: 'SX', label: 'Student Ximenes Garage (SX)' },
    { value: 'SXR', label: 'Student Ximenes Reserved (SXR)' },
    { value: 'SB', label: 'Student Bauerle Garage (SB)' },
    { value: 'SBR', label: 'Student Bauerle Reserved (SBR)' },
    { value: 'XN', label: 'Ximenes Night Only (XN)' },
    { value: 'TN', label: 'Tobin Night Only (TN)' },
    { value: 'BN', label: 'Bauerle Night Only (BN)' },
    { value: 'A', label: 'Employee A' },
    { value: 'B', label: 'Employee B' },
    { value: 'ET', label: 'Employee Tobin Garage (ET)' },
    { value: 'EX', label: 'Employee Ximenes Garage (EX)' },
    { value: 'EB', label: 'Employee Bauerle Garage (EB)' },
    { value: 'FastPass', label: 'FastPass' },
    { value: 'Telecommuter', label: 'Telecommuter Pass' },
];

export default function PermitSelector({ selectedPermit, onChange }) {
    return (
        <div className="flex items-center gap-3">
            <label htmlFor="permit-select" className="text-sm font-medium text-gray-700 dark:text-gray-300">
                My Permit:
            </label>
            <select
                id="permit-select"
                value={selectedPermit}
                onChange={(e) => onChange(e.target.value)}
                className="block rounded-md border-gray-300 py-2 px-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
                {PERMIT_OPTIONS.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
}
