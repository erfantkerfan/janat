<?php

namespace Database\Seeders;

use App\UserType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('user_types');

        UserType::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'Permanent member',
                'display_name' => 'عضو دائم',
                'description' => 'عضوی است که مبلغی بعنوان ماهانه پرداخت می کند و طبق نوبت براساس مبلغ ماهانه خود وام نوبتی دریافت می کند'
            ],
            [
                'name' => 'Temporary member',
                'display_name' => 'عضو موقت',
                'description' => 'عضوی است که فقط برای دریافت وام ضروری عضو می شود.'
            ],
            [
                'name' => 'Good man',
                'display_name' => 'عضو خیّر (مسدودی)',
                'description' => 'عضو خیّری است که مبلغی را در صندق قرار می دهد تا بتوانند به افرادی که خودشان معرفی می کنند وام ضروری پرداخت گردد. مبلغ و مدت باز پرداخت بعهده صاحب حساب بوده و به هر میزان که معرفی می نماید حسابش مسدود میگردد و به هر میزان بازپرداخت گردد مبلغ مسدودی آزاد می گردد.که باید وضعیت حساب این اعضا همشیه بروز باشد یعنی مبلغ آزاد ، مبلغ مسدودی و کل مبلغ آن مشخص باشد.'
            ]
        ];
    }

    public static function getRandomObject() {
        $values = (new UserTypeSeeder)->getValues();
        return UserType::where('id', Factory::create()->numberBetween(1, count($values)))->first();
    }
}
