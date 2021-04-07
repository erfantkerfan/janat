<?php

use App\Fund;
use Illuminate\Database\Seeder;

class FundTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('funds');
        Fund::create([
            'name' => 'صندوق قرض الحسنه دانشگاه شهید عباسپور'
        ]);
    }
}
