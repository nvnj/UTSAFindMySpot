<?php

namespace Database\Seeders;

use App\Models\Garage;
use App\Models\Lot;
use App\Models\Spot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UTSAParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('spots')->truncate();
        DB::table('lots')->truncate();
        DB::table('garages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Commuter Lots (C permit) - space_type: 'commuter'
        $this->createLot('BK1', 'Brackenridge Avenue Lot 1', 'Near Recreation Wellness Center', 29.581394618982845, -98.62079982090208, 120, 'open', 'commuter');
        $this->createLot('BK2', 'Brackenridge Avenue Lot 2', 'Near Recreation Wellness Center', 29.580429282840427, -98.62194811362839, 100, 'open', 'commuter');
        $this->createLot('BK4', 'Brackenridge Avenue Lot 4', 'Near Recreation Wellness Center', 29.579297497675142, -98.62612024386732, 85, 'open', 'commuter');
        $this->createLot('BK5', 'Brackenridge Avenue Lot 5', 'Near Recreation Wellness Center', 29.578665007826892, -98.6288176362348, 75, 'open', 'commuter');
        $this->createLot('XL', 'Ximenes Avenue Lot', 'Near Ximenes Avenue', 29.58122493370704, -98.61891166662676, 100, 'open', 'commuter');
        $this->createLot('BR1', 'Bauerle Road Lot 1', 'Near Bauerle Road', 29.581143875787443, -98.61514013582294, 150, 'open', 'commuter');
        $this->createLot('BS', 'Barshop Boulevard Lot', 'Near Barshop Boulevard', 29.581695040060843, -98.62755870972853, 95, 'open', 'commuter');
        $this->createLot('EC1', 'East Campus Lot 1', 'East Campus Drive', 29.583300507405557, -98.61027460867108, 140, 'open', 'commuter');
        $this->createLot('EC2', 'East Campus Lot 2', 'East Campus Drive', 29.582493280563956, -98.60933355921593, 160, 'open', 'commuter');
        $this->createLot('EC3', 'East Campus Lot 3', 'East Campus Drive', 29.5839127034908, -98.60870890457494, 130, 'open', 'commuter');

        // Resident H Lots - space_type: 'resident'
        // Note: BS is both commuter AND resident H, already created above
        $this->createLot('R1', 'Resident Lot 1', 'Near Chisholm Hall', 29.584612554911082, -98.62252146429067, 120, 'reserved', 'resident');
        $this->createLot('R2', 'Resident Lot 2', 'Near Alvarez Hall', 29.585229407210875, -98.62417909984849, 110, 'reserved', 'resident');
        $this->createLot('R4', 'Resident Lot 4', 'Near Laurel Village', 29.586385230282993, -98.62446628085078, 100, 'reserved', 'resident');
        $this->createLot('R5', 'Resident Lot 5', 'Near Guadalupe Hall', 29.58509855966302, -98.62899975537727, 95, 'reserved', 'resident');

        // Reserved Lots - space_type: 'reserved'
        $this->createLot('BR2', 'Bauerle Road Lot 2', 'Near Bauerle Road', 29.58529680865299, -98.61622926838848, 180, 'reserved', 'reserved');
        $this->createLot('BOS', 'Bosque Street Lot', 'Near Bosque Street Building', 29.58528997825413, -98.62156071556862, 60, 'reserved', 'reserved');
        $this->createLot('KCL', 'Key Circle Lot', 'Near Key Circle', 29.582614545334085, -98.61683575052254, 85, 'reserved', 'reserved');

        // Employee A Lots - space_type: 'employee_a'
        $this->createLot('DL', 'Devine Avenue Lot', 'Near Devine Avenue', 29.58574330787882, -98.61825687488172, 70, 'reserved', 'employee_a');

        // Employee B Lots - space_type: 'employee_b'
        $this->createLot('BR3', 'Bauerle Road Lot 3', 'Near Bauerle Road', 29.586871439349927, -98.61760254504904, 200, 'reserved', 'employee_b');
        $this->createLot('FL', 'Ford Lot', 'Near Ford Avenue', 29.580840053783746, -98.61686803397, 110, 'reserved', 'employee_b');
        $this->createLot('BK3', 'Brackenridge Avenue Lot 3', 'Near Recreation Wellness Center', 29.579686374323956, -98.62494953729878, 90, 'reserved', 'employee_b', false); // CLOSED

        // Garages - space_type matches garage code in lowercase
        $this->createGarage('BRG', 'Bauerle Road Garage', '1604 Campus Drive', 29.58619385647707, -98.61671423910182, 5, 1200, 'garage_brg');
        $this->createGarage('TAG', 'Tobin Avenue Garage', 'Near Tobin Avenue', 29.586980929389846, -98.6210045538868, 6, 1500, 'garage_tag');
        $this->createGarage('XAG', 'Ximenes Avenue Garage', 'Near Ximenes Avenue', 29.582482197865165, -98.61918546259412, 4, 800, 'garage_xag');

        $this->command->info('UTSA parking lots and garages seeded with correct coordinates!');
    }

    private function createLot(string $code, string $name, string $location, float $lat, float $lng, int $totalSpots, string $type, string $spaceType, bool $isActive = true): void
    {
        $availableSpots = $isActive ? rand((int) ($totalSpots * 0.2), (int) ($totalSpots * 0.7)) : 0;

        $lot = Lot::create([
            'lot_code' => $code,
            'name' => $name,
            'location' => $location,
            'latitude' => $lat,
            'longitude' => $lng,
            'total_spots' => $totalSpots,
            'available_spots' => $availableSpots,
            'type' => $type,
            'space_type' => $spaceType,
            'is_active' => $isActive,
        ]);

        // Create 20 sample spots per lot for layout visualization
        for ($i = 1; $i <= 20; $i++) {
            Spot::create([
                'lot_id' => $lot->id,
                'spot_number' => "{$code}-{$i}",
                'occupied' => rand(0, 100) > 40,
                'last_updated_at' => now()->subMinutes(rand(1, 60)),
            ]);
        }
    }

    private function createGarage(string $code, string $name, string $location, float $lat, float $lng, int $levels, int $totalSpots, string $spaceType): void
    {
        $availableSpots = rand((int) ($totalSpots * 0.3), (int) ($totalSpots * 0.6));

        $garage = Garage::create([
            'garage_code' => $code,
            'name' => $name,
            'location' => $location,
            'latitude' => $lat,
            'longitude' => $lng,
            'levels' => $levels,
            'total_spots' => $totalSpots,
            'available_spots' => $availableSpots,
            'space_type' => $spaceType,
            'is_active' => true,
        ]);

        // Create spots for each level (30 spots per level for visualization)
        for ($level = 1; $level <= $levels; $level++) {
            for ($i = 1; $i <= 30; $i++) {
                Spot::create([
                    'garage_id' => $garage->id,
                    'spot_number' => "{$code}-L{$level}-{$i}",
                    'level' => $level,
                    'occupied' => rand(0, 100) > 50,
                    'last_updated_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }
        }
    }
}
