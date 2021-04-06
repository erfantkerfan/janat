<?php

use App\Company;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('companies');
        Company::create([
            'name' => 'دانشگاه شهید عباسپور',
            'undertaker' => 'مدیر سیستم'
        ]);
    }
}
