<?php

use App\Company;
use App\Fund;
use App\User;
use App\UserStatus;
use Faker\Factory;
use Faker\Provider\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserStatusSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(PermissionsSeeder::class);

        $this->call(FakeData::class);
    }
}

class UserStatusSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('user_statuses');

        UserStatus::create([
            'name' => 'active',
            'displayName' => 'فعال',
            'description' => 'در این وضعیت کاربر فعال است'
        ]);
        UserStatus::create([
            'name' => 'pending',
            'displayName' => 'در انتظار بررسی',
            'description' => 'در این حالت کاربر درخواست ثبت نام داده و منتظر تایید مدیر است'
        ]);
        UserStatus::create([
            'name' => 'inactive',
            'displayName' => 'غیر فعال',
            'description' => 'در این حالت کاربر از صندوق خارج شده'
        ]);
    }
}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('users');
        $userStatus = UserStatus::where('name', 'active')->first();
//        $company = Company::where('id', rand(1, 3))->first();
        DB::table('users')->insert([
            'SSN' => 'admin',
            'status_id' => $userStatus->id,
            'password' => Hash::make('janat'),
            'created_at' => '1991-11-27 00:00:00',
            'updated_at' => '1991-11-27 00:00:00'
        ]);
    }
}

class PermissionsSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('roles');
        DBAssistant::resetTable('permissions');
        DBAssistant::resetTable('role_has_permissions');

        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'access to everything']);
        $role->givePermissionTo($permission);

        $user = User::where('SSN', 'admin')->first();
        $user->assignRole([$role->name]);
    }
}

class FakeData extends Seeder {

    public function run()
    {
        $this->call(FakeFund::class);
        $this->call(FakeCompany::class);
        $this->call(FakeUser::class);
    }
}

class FakeFund extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('funds');
        $faker = Factory::create();
        for ($i = 1; $i < 4; $i++) {
            Fund::create([
                'name' => $faker->word,
                'monthly_payment' => 15000,
                'created_at' => $faker->date(),
                'updated_at' => $faker->date()
            ]);
        }
    }
}

class FakeCompany extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('companies');
        $faker = Factory::create();
        for ($i = 1; $i < 4; $i++) {
            $fund = Fund::where('id', $i)->first();
            Company::create([
                'name' => $faker->company,
                'fund_id' => $fund->id,
                'created_at' => $faker->date(),
                'updated_at' => $faker->date()
            ]);
        }
    }
}

class FakeUser extends Seeder {

    public function run()
    {
        $faces = [
            'sample-avatar-1.jpg',
            'sample-avatar-2.jpg',
            'sample-avatar-3.jpg',
            'sample-avatar-4.jpeg',
            'sample-avatar-5.jpg',
            'sample-avatar-6.jpeg',
            'sample-avatar-7.jpg',
            'sample-avatar-8.jpg',
            'sample-avatar-9.jpg',
            'sample-avatar-10.jpg'
        ];
        $faker = Factory::create();
        for ($i = 1; $i < 30; $i++) {
            $company = Company::find(rand(1, 3));
            $userStatus = UserStatus::find(rand(1, 3));
            User::create([
                'f_name' => $faker->name,
                'l_name' => $faker->lastName,
                'SSN' => '123456789'.$i,
                'phone' => '09'.rand(111111111, 999999999),
                'mobile' => '021'.rand(11111111, 99999999),
                'password' => Hash::make('1234'),
                'user_pic' => File::get(public_path('img/faces/'.$faces[rand(0, 9)])),
                'company_id' => $company->id,
                'status_id' => $userStatus->id,
                'created_at' => $faker->date(),
                'updated_at' => $faker->date()
            ]);
        }
    }
}

class DBAssistant {

    public static function resetTable($tableName)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($tableName)->delete();
        DB::statement("ALTER TABLE $tableName AUTO_INCREMENT =  1");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
