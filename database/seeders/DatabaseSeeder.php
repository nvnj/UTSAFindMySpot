<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Garage;
use App\Models\Lot;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'UTSA Parking Admin',
            'email' => 'admin@utsa.edu',
        ]);

        $lots = [
            [
                'name' => 'Lot B1',
                'location' => 'Near Biotechnology Sciences and Engineering Building',
                'latitude' => 29.584600,
                'longitude' => -98.619300,
                'total_spots' => 150,
                'available_spots' => 45,
                'type' => 'open',
            ],
            [
                'name' => 'Lot B2',
                'location' => 'Near Business Building',
                'latitude' => 29.583900,
                'longitude' => -98.618800,
                'total_spots' => 200,
                'available_spots' => 120,
                'type' => 'open',
            ],
            [
                'name' => 'Lot H4',
                'location' => 'Near UTSA Boulevard',
                'latitude' => 29.582100,
                'longitude' => -98.620500,
                'total_spots' => 300,
                'available_spots' => 25,
                'type' => 'open',
            ],
            [
                'name' => 'Lot VP3',
                'location' => 'Near Valero Plaza',
                'latitude' => 29.585200,
                'longitude' => -98.617900,
                'total_spots' => 100,
                'available_spots' => 5,
                'type' => 'reserved',
            ],
            [
                'name' => 'Lot MS2',
                'location' => 'Near McKinney Science Building',
                'latitude' => 29.586100,
                'longitude' => -98.618600,
                'total_spots' => 175,
                'available_spots' => 80,
                'type' => 'open',
            ],
        ];

        $garages = [
            [
                'name' => 'Chaparral Parking Garage',
                'location' => '1604 Campus Drive',
                'latitude' => 29.584900,
                'longitude' => -98.620100,
                'levels' => 5,
                'total_spots' => 1200,
                'available_spots' => 450,
            ],
            [
                'name' => 'Bauerle Road Parking Garage',
                'location' => 'Near Recreation Center',
                'latitude' => 29.581400,
                'longitude' => -98.622300,
                'levels' => 4,
                'total_spots' => 800,
                'available_spots' => 120,
            ],
            [
                'name' => 'Circle Parking Garage',
                'location' => 'Near John Peace Library',
                'latitude' => 29.585800,
                'longitude' => -98.619800,
                'levels' => 6,
                'total_spots' => 1500,
                'available_spots' => 780,
            ],
        ];

        foreach ($lots as $lotData) {
            $lot = Lot::create($lotData);

            $spotsToCreate = min(10, $lotData['total_spots']);
            for ($i = 1; $i <= $spotsToCreate; $i++) {
                $occupied = $i <= ($spotsToCreate - ($lotData['available_spots'] * $spotsToCreate / $lotData['total_spots']));
                Spot::create([
                    'lot_id' => $lot->id,
                    'spot_number' => "L{$lot->id}-{$i}",
                    'occupied' => $occupied,
                    'last_updated_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }
        }

        foreach ($garages as $garageData) {
            $garage = Garage::create($garageData);

            $spotsPerLevel = 5;
            for ($level = 1; $level <= $garage->levels; $level++) {
                for ($i = 1; $i <= $spotsPerLevel; $i++) {
                    $occupied = rand(0, 100) > 60;
                    Spot::create([
                        'garage_id' => $garage->id,
                        'spot_number' => "G{$garage->id}-L{$level}-{$i}",
                        'level' => $level,
                        'occupied' => $occupied,
                        'last_updated_at' => now()->subMinutes(rand(1, 120)),
                    ]);
                }
            }
        }

        $firstLot = Lot::first();
        $firstGarage = Garage::first();

        Alert::create([
            'lot_id' => $firstLot->id,
            'alert_type' => 'event',
            'title' => 'Football Game Parking Restrictions',
            'details' => 'This lot is reserved for football game attendees on game days. Limited availability expected.',
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(6),
            'is_active' => true,
        ]);

        Alert::create([
            'lot_id' => Lot::skip(2)->first()->id,
            'alert_type' => 'construction',
            'title' => 'Lot Maintenance in Progress',
            'details' => 'Northern section of lot temporarily closed for repaving. Reduced capacity.',
            'start_time' => now()->subDays(1),
            'end_time' => now()->addDays(5),
            'is_active' => true,
        ]);

        Alert::create([
            'garage_id' => $firstGarage->id,
            'alert_type' => 'maintenance',
            'title' => 'Elevator Maintenance - Level 4',
            'details' => 'East elevator out of service for routine maintenance. Please use stairs or west elevator.',
            'start_time' => now(),
            'end_time' => now()->addHours(4),
            'is_active' => true,
        ]);
    }
}
