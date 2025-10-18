<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Station;
use App\Models\Channel;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $station = Station::create([
            'name' => 'Todo Radio',
            'slug' => 'todo-radio',
            'logo_url' => 'https://tudominio.com/logo.png',
            'is_active' => true,
        ]);

        Channel::create([
            'station_id' => $station->id,
            'name' => 'Radio 1',
            'slug' => 'radio-1',
            'stream_url' => 'http://192.168.1.10:8000/stream',
            'is_active' => true,
        ]);

        Channel::create([
            'station_id' => $station->id,
            'name' => 'Radio 2',
            'slug' => 'radio-2',
            'stream_url' => 'http://192.168.1.15:8000/stream',
            'is_active' => true,
        ]);
    }
}
