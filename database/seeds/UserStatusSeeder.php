<?php

use Faker\Factory;
use App\UserStatus;
use Illuminate\Database\Seeder;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('user_statuses');

        UserStatus::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'active',
                'display_name' => 'فعال',
                'description' => 'در این وضعیت کاربر فعال است'
            ],
            [
                'name' => 'pending',
                'display_name' => 'در انتظار بررسی',
                'description' => 'در این حالت کاربر درخواست ثبت نام داده و منتظر تایید مدیر است'
            ],
            [
                'name' => 'inactive',
                'display_name' => 'غیر فعال',
                'description' => 'در این حالت کاربر از صندوق خارج شده'
            ]
        ];
    }

    public static function getRandomObject() {
        $values = (new UserStatusSeeder)->getValues();
        return UserStatus::where('id', Factory::create()->numberBetween(1, count($values)))->first();
    }
}
