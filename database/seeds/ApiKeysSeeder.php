<?php

use App\ApiKey;
use App\Classes\Key;
use App\User;
use Illuminate\Database\Seeder;

class ApiKeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_key = ApiKey::firstOrCreate([
            'user_id' => User::where('email', 'averbanck1992@gmail.com')->first()->id,
        ]);
    }
}
