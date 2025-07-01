<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Personnel;
use App\Models\City;
use App\Models\Sector;
use App\Models\Placement;
use App\Models\Payroll;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID'); // Use Indonesian locale for more relevant data

        // Seed Users (10 records)
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[] = User::create([
                'id' => (string) Str::uuid(),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'), // Default password for testing
                'role' => $faker->randomElement(['1', '2', '3']),
            ]);
        }

        // Seed Cities (5 records)
        $cityData = [
            ['code' => '001', 'name' => 'Jakarta', 'province' => 'DKI Jakarta'],
            ['code' => '002', 'name' => 'Bandung', 'province' => 'Jawa Barat'],
            ['code' => '003', 'name' => 'Surabaya', 'province' => 'Jawa Timur'],
            ['code' => '004', 'name' => 'Medan', 'province' => 'Sumatera Utara'],
            ['code' => '005', 'name' => 'Yogyakarta', 'province' => 'DI Yogyakarta'],
        ];
        foreach ($cityData as $data) {
            City::create($data);
        }

        // Seed Sectors (10 records)
        $sectors = [];
        // Define approximate coordinates for each city
        $cityCoordinates = [
            '001' => ['latitude' => '-6.2088', 'longitude' => '106.8456'], // Jakarta
            '002' => ['latitude' => '-6.9403', 'longitude' => '107.6590'], // Bandung (near PT. JAMKRIDA JABAR)
            '003' => ['latitude' => '-7.2575', 'longitude' => '112.7521'], // Surabaya
            '004' => ['latitude' => '3.5952', 'longitude' => '98.6722'], // Medan
            '005' => ['latitude' => '-7.7956', 'longitude' => '110.3695'], // Yogyakarta
        ];
        for ($i = 0; $i < 10; $i++) {
            $cityCode = $cityData[$faker->numberBetween(0, 4)]['code'];
            // Add small random offset to coordinates for variety
            $baseLat = floatval($cityCoordinates[$cityCode]['latitude']);
            $baseLon = floatval($cityCoordinates[$cityCode]['longitude']);
            $latOffset = $faker->randomFloat(4, -0.01, 0.01); // Small variation (±0.01 degrees)
            $lonOffset = $faker->randomFloat(4, -0.01, 0.01);
            $sectors[] = Sector::create([
                'code' => sprintf('SC%04d', $i + 1), // e.g., SC0001, SC0002, ..., SC0010
                'city_code' => $cityCode,
                'label' => $faker->word . ' Sector',
                'location' => $faker->streetAddress,
                'type' => $faker->randomElement(['Security', 'Retail', 'Industrial', 'Commercial']),
                'latitude' => (string) ($baseLat + $latOffset),
                'longitude' => (string) ($baseLon + $lonOffset),
                'description' => $faker->sentence,
            ]);
        }

        // Seed Personnel (10 records)
        $personnel = [];
        for ($i = 0; $i < 10; $i++) {
            $user = $i < 5 ? $users[$i] : null;
            // Generate family member data (not living in the same household)
            $hasFamilyData = $faker->boolean(70); // 70% chance of having family data
            $personnel[] = Personnel::create([
                'user_id' => $user ? $user->id : (string) Str::uuid(),
                'name' => $user ? $user->name : $faker->name,
                'nik' => $faker->unique()->nik(), // Generate unique Indonesian NIK
                'email' => $user ? $user->email : ($faker->boolean(80) ? $faker->unique()->safeEmail : null), // 80% chance of having email
                'address' => $faker->address,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'status' => $faker->randomElement(['1', '2', '3']),
                'phone_number' => $faker->phoneNumber,
                'height_cm' => $faker->numberBetween(150, 200),
                'weight_kg' => $faker->numberBetween(50, 100),
                'education' => $faker->randomElement(['High School', 'Bachelor\'s Degree', 'Master\'s Degree']),
                'work_experience' => $faker->sentence(10),
                'photo_url' => $faker->imageUrl(),
                'certifications' => implode(', ', $faker->words(3)),
                'fingerprint' => $faker->boolean(50) ? $faker->sha256 : null, // 50% chance of having fingerprint data
                'bank_name' => $faker->randomElement(['Bank ABC', 'Bank XYZ', 'Bank DEF']),
                'address' => $faker->address,
                'bank_account' => $faker->creditCardNumber,
                // Family member data (not living in the same household)
                'add_name' => $hasFamilyData ? $faker->name : null,
                'add_address' => $hasFamilyData ? $faker->address : null, // Different address from personnel
                'add_phone_number' => $hasFamilyData ? $faker->phoneNumber : null,
                'add_relationship' => $hasFamilyData ? $faker->randomElement(['Parent', 'Sibling', 'Spouse', 'Child']) : null,
            ]);
        }

        // Seed Placements (15 records)
        for ($i = 0; $i < 15; $i++) {
            Placement::create([
                'personnel_id' => $personnel[$faker->numberBetween(0, 9)]->id,
                'city_code' => $cityData[$faker->numberBetween(0, 4)]['code'], // e.g., 001, 002, etc.
                'sector_code' => $faker->boolean(80) ? $sectors[$faker->numberBetween(0, 9)]->code : null, // 80% chance of having a sector
                'notes' => $faker->sentence,
                'start_date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'end_date' => $faker->boolean(50) ? $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d') : null,
                'active' => $faker->boolean(70), // 70% chance of being active
            ]);
        }

        // Seed Payrolls (20 records, 2 per personnel)
        foreach ($personnel as $person) {
            for ($i = 0; $i < 2; $i++) {
                $base_salary = $faker->randomFloat(2, 5000000, 10000000); // 5M to 10M IDR
                $bonuses = $faker->randomFloat(2, 0, 1000000); // 0 to 1M IDR
                $deductions = $faker->randomFloat(2, 0, 500000); // 0 to 500K IDR
                $total_salary = $base_salary + $bonuses - $deductions;

                // Generate period_start and period_end (monthly periods)
                $month_offset = $faker->numberBetween(-6, -1); // Last 6 months
                $period_start = now()->addMonths($month_offset)->startOfMonth()->format('Y-m-d');
                $period_end = now()->addMonths($month_offset)->endOfMonth()->format('Y-m-d');

                Payroll::create([
                    'personnel_id' => $person->id,
                    'period_start' => $period_start,
                    'period_end' => $period_end,
                    'base_salary' => $base_salary,
                    'bonuses' => $bonuses,
                    'deductions' => $deductions,
                    'total_salary' => $total_salary,
                ]);
            }
        }
    }
}
