<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Seed the countries table from a JSON file.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/countries.json');
        if (!File::exists($path)) {
            $this->command?->warn("countries.json not found at {$path}");
            return;
        }

        $json = File::get($path);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            $this->command?->warn('Invalid countries.json format');
            return;
        }

        foreach ($items as $item) {
            if (!isset($item['name'], $item['iso2'], $item['iso3'])) {
                continue;
            }

            Country::updateOrCreate(
                ['iso2' => strtoupper($item['iso2'])],
                [
                    'name' => $item['name'],
                    'iso3' => strtoupper($item['iso3']),
                ],
            );
        }
    }
}


