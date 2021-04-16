<?php

use App\User;
use App\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('users');
        $userStatus = UserStatus::where('name', 'active')->first();
        $user = User::create([
            'SSN' => 'admin',
            'status_id' => $userStatus->id,
            'password' => Hash::make('janat'),
            'description' => 'کاربر ادمین',
            'created_at' => '1991-11-27 00:00:00'
        ]);
    }
}
