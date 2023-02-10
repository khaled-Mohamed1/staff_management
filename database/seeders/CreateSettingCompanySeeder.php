<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateSettingCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Setting::create([
            'company_name' => 'Stuff Management',
            'company_email' => 'Stuff@gmail.com',
            'company_logo' => null,
            'company_phone_NO' => 1234123412,
        ]);
    }
}
