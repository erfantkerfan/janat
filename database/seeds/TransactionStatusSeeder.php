<?php

use Faker\Factory;
use App\TransactionStatus;
use Illuminate\Database\Seeder;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('transaction_statuses');

        TransactionStatus::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'paid',
                'display_name' => 'پرداخت شده',
                'description' => ''
            ],
            [
                'name' => 'pending',
                'display_name' => 'در انتظار پرداخت',
                'description' => ''
            ]
        ];
    }

    public static function getRandomObject() {
        $values = (new TransactionStatusSeeder)->getValues();
        return TransactionStatus::where('id', Factory::create()->numberBetween(1, count($values)))->first();
    }
}
