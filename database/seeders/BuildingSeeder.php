<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            // Academic Buildings
            ['code' => 'ART', 'name' => 'Arts Building', 'category' => 'Academic', 'lat' => 29.58337158730394, 'lng' => -98.61772236771091],
            ['code' => 'BSE', 'name' => 'Biotechnology Sciences & Engineering', 'category' => 'Academic', 'lat' => 29.581447968648348, 'lng' => -98.61717021353962],
            ['code' => 'BB', 'name' => 'Business Building', 'category' => 'Academic', 'lat' => 29.58549350036243, 'lng' => -98.61766936935179],
            ['code' => 'EB', 'name' => 'Engineering Building', 'category' => 'Academic', 'lat' => 29.58247207564603, 'lng' => -98.61786427120396],
            ['code' => 'FLN', 'name' => 'Flawn Sciences Building', 'category' => 'Academic', 'lat' => 29.5828853749089, 'lng' => -98.61848867121677],
            ['code' => 'JPL', 'name' => 'John Peace Library', 'category' => 'Academic', 'lat' => 29.58465380191836, 'lng' => -98.61758885587868],
            ['code' => 'MB', 'name' => 'Main Building', 'category' => 'Academic', 'lat' => 29.58481185208534, 'lng' => -98.61670202702217],
            ['code' => 'MH', 'name' => 'McKinney Humanities Building', 'category' => 'Academic', 'lat' => 29.584604252558105, 'lng' => -98.61914157119976],
            ['code' => 'MS', 'name' => 'Multidisciplinary Studies Building', 'category' => 'Academic', 'lat' => 29.583731254583135, 'lng' => -98.61917641539144],
            ['code' => 'SEB', 'name' => 'Science & Engineering Building', 'category' => 'Academic', 'lat' => 29.581727396724173, 'lng' => -98.61663342249408],

            // Athletics
            ['code' => 'CC', 'name' => 'Convocation Center', 'category' => 'Athletics', 'lat' => 29.58248942503135, 'lng' => -98.62149553453544],
            ['code' => 'RACE', 'name' => 'Roadrunner Athletic Center of Excellence', 'category' => 'Athletics', 'lat' => 29.580636209349336, 'lng' => -98.62501491354965],

            // Housing
            ['code' => 'AH', 'name' => 'Alvarez Hall', 'category' => 'Housing', 'lat' => 29.58431598337879, 'lng' => -98.62334152888714],
            ['code' => 'CV', 'name' => 'Chaparral Village', 'category' => 'Housing', 'lat' => 29.58518515122956, 'lng' => -98.6246413270255],
            ['code' => 'CH', 'name' => 'Chisholm Hall', 'category' => 'Housing', 'lat' => 29.584973036240907, 'lng' => -98.62666348193689],
            ['code' => 'GH', 'name' => 'Guadalupe Hall', 'category' => 'Housing', 'lat' => 29.58585312969048, 'lng' => -98.62377773072846],
            ['code' => 'LV', 'name' => 'Laurel Village', 'category' => 'Housing', 'lat' => 29.58613651570331, 'lng' => -98.62269541001358],

            // Student Services
            ['code' => 'AC', 'name' => 'Activity Center', 'category' => 'Student Services', 'lat' => 29.585523899223848, 'lng' => -98.62562657882526],
            ['code' => 'HSU', 'name' => 'H-E-B Student Union', 'category' => 'Student Services', 'lat' => 29.584055472863618, 'lng' => -98.62045854422783],
            ['code' => 'RWC', 'name' => 'Recreation Wellness Center', 'category' => 'Student Services', 'lat' => 29.58187219580369, 'lng' => -98.62304419371969],
            ['code' => 'SSC', 'name' => 'Student Success Center', 'category' => 'Student Services', 'lat' => 29.585416924463644, 'lng' => -98.61955757097405],
        ];

        foreach ($buildings as $building) {
            Building::create([
                'code' => $building['code'],
                'name' => $building['name'],
                'category' => $building['category'],
                'latitude' => $building['lat'],
                'longitude' => $building['lng'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Seeded '.count($buildings).' UTSA buildings');
    }
}
