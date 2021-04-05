<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('settings');

        Setting::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'loan_interest_per_month',
                'display_name' => 'کارمزد وام برای هر ماه قسط',
                'value' => '1000',
                'order' => 1
            ],
            [
                'name' => 'currency_unit',
                'display_name' => 'واحد ارز',
                'value' => 'ریال',
                'order' => 2
            ],
            [
                'name' => 'type_of_loan_interest_payment',
                'display_name' => 'نحوه پرداخت کارمزد وام',
                'value' => 'paid_at_first', //monthly_payment
                'order' => 3
            ]
        ];
    }
}
