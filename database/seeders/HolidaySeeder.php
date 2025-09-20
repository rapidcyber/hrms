<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Holiday;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'New Year\'s Day',
                'date' => '2025-01-01',
                'description' => 'Celebration of the first day of the year.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Independence Day',
                'date' => '2025-06-12',
                'description' => 'Commemoration of the Declaration of Independence.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2025-12-25',
                'description' => 'Celebration of the birth of Jesus Christ.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Labor Day',
                'date' => '2025-05-01',
                'description' => 'A day to honor workers and their contributions.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Eid al-Fitr',
                'date' => '2025-04-10',
                'description' => 'Celebration marking the end of Ramadan.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Eid al-Adha',
                'date' => '2025-06-28',
                'description' => 'Commemoration of the willingness of Ibrahim to sacrifice his son.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'All Saints\' Day',
                'date' => '2025-11-01',
                'description' => 'A day to honor all saints, known and unknown.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'National Heroes Day',
                'date' => '2025-08-28',
                'description' => 'A day to honor the heroes of the nation.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Bonifacio Day',
                'date' => '2025-11-30',
                'description' => 'Commemoration of the birth of Andres Bonifacio, a national hero.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Rizal Day',
                'date' => '2025-12-30',
                'description' => 'Commemoration of the execution of Dr. Jose Rizal, a national hero.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];
        DB::table('holidays')->truncate();
        foreach ($data as $holiday) {
            Holiday::create($holiday);
        }
        $this->command->info('Holidays table seeded successfully!');
    }
}
