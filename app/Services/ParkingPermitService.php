<?php

namespace App\Services;

class ParkingPermitService
{
    /**
     * Determine what space types a permit can access.
     * Returns an array of space types this permit is allowed to use.
     */
    public function getAllowedSpaceTypes(string $permitType, bool $isAfterHours = false): array
    {
        $allowed = [];

        // Student Surface Permits
        if (in_array($permitType, ['C'])) {
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['H'])) {
            $allowed[] = 'resident';
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['SD'])) {
            $allowed[] = 'dolorosa_reserved';
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['U'])) {
            $allowed[] = 'university_oaks';
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['Z'])) {
            // Twilight only works after hours
            if ($isAfterHours) {
                $allowed[] = 'commuter';
            }
        }

        if (in_array($permitType, ['M'])) {
            $allowed[] = 'motorcycle';
        }

        // Student Garage Permits - They can ALSO park in surface lots
        if (in_array($permitType, ['RT', 'SRW', 'ST', 'SX', 'SB'])) {
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType));
            $allowed[] = 'commuter'; // Key: Garage permits CAN park in commuter surface lots!
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['STR', 'SXR', 'SBR'])) {
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType)).'_reserved';
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType));
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_b';
            }
        }

        if (in_array($permitType, ['XN', 'TN', 'BN'])) {
            // Night-only garage permits only work after hours
            if ($isAfterHours) {
                $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType));
            }
        }

        // Employee Surface Permits
        if (in_array($permitType, ['A'])) {
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['B'])) {
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
            if ($isAfterHours) {
                $allowed[] = 'employee_a'; // Park Up!
            }
        }

        if (in_array($permitType, ['ED'])) {
            $allowed[] = 'dolorosa_reserved';
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['R'])) {
            $allowed[] = 'reserved';
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
        }

        // Employee Garage Permits - They can ALSO park in surface lots
        if (in_array($permitType, ['ERW', 'ET', 'EX', 'EB'])) {
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType));
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
            $allowed[] = 'certain_reserved'; // R2, R5, BSA, CAR, CRW, etc.
        }

        if (in_array($permitType, ['ETR', 'EXR', 'EBR'])) {
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType)).'_reserved';
            $allowed[] = 'garage_'.strtolower($this->getGarageForPermit($permitType));
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
            $allowed[] = 'certain_reserved';
        }

        // Single-Use/Visitor Permits
        if (in_array($permitType, ['J'])) {
            $allowed[] = 'university_oaks';
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['K'])) {
            $allowed[] = 'resident';
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['S'])) {
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['Y'])) {
            $allowed[] = 'employee_a';
            $allowed[] = 'employee_b';
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['CV'])) {
            $allowed[] = 'employee_b';
        }

        if (in_array($permitType, ['W'])) {
            $allowed[] = 'commuter'; // Specific to BK2 lot
        }

        if (in_array($permitType, ['N'])) {
            $allowed[] = 'commuter';
        }

        if (in_array($permitType, ['FastPass', 'Telecommuter'])) {
            $allowed[] = 'garage_brg';
            $allowed[] = 'garage_tag';
            $allowed[] = 'garage_xag';
        }

        // Special evening/weekend rule: Everyone can use A, B, and Commuter spaces
        if ($isAfterHours) {
            if (! in_array('employee_a', $allowed)) {
                $allowed[] = 'employee_a';
            }
            if (! in_array('employee_b', $allowed)) {
                $allowed[] = 'employee_b';
            }
            if (! in_array('commuter', $allowed)) {
                $allowed[] = 'commuter';
            }
        }

        return array_unique($allowed);
    }

    /**
     * Get the garage code for a permit.
     */
    private function getGarageForPermit(string $permitType): string
    {
        if (str_contains($permitType, 'T') || $permitType === 'RT') {
            return 'TAG';
        }
        if (str_contains($permitType, 'X') || $permitType === 'SRW' || $permitType === 'ERW') {
            return 'XAG';
        }
        if (str_contains($permitType, 'B')) {
            return 'BRG';
        }

        return '';
    }

    /**
     * Check if it's currently after hours (evening/weekend).
     */
    public function isAfterHours(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $hour = $now->hour;
        $minute = $now->minute;
        $time = $hour * 60 + $minute; // Minutes since midnight

        // Friday 4:30 PM (16:30) until Monday 7:00 AM
        if ($dayOfWeek === 5 && $time >= 16 * 60 + 30) {
            return true; // Friday after 4:30 PM
        }
        if ($dayOfWeek === 6 || $dayOfWeek === 0) {
            return true; // Saturday or Sunday
        }
        if ($dayOfWeek === 1 && $time < 7 * 60) {
            return true; // Monday before 7:00 AM
        }

        // Monday-Thursday: 10:00 PM (22:00) - 7:00 AM
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            if ($time >= 22 * 60 || $time < 7 * 60) {
                return true;
            }
        }

        return false;
    }

    /**
     * Simplified check: Can this permit park at this lot/garage?
     * This is a high-level check based on permit type matching.
     */
    public function canParkAt(string $permitType, array $allowedPermits): bool
    {
        // If no restrictions, anyone can park
        if (empty($allowedPermits)) {
            return true;
        }

        // Direct match
        if (in_array($permitType, $allowedPermits)) {
            return true;
        }

        // Check if any allowed permit grants access to this location
        // For example, if location allows 'commuter' and permit has commuter access
        $isAfterHours = $this->isAfterHours();
        $allowedSpaceTypes = $this->getAllowedSpaceTypes($permitType, $isAfterHours);

        // This is a simplified check - we'll enhance this when we properly tag lots with space types
        return false;
    }
}
