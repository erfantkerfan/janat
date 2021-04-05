<?php


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserStatusSeeder::class,
            UserTypeSeeder::class,
            LoanTypeSeeder::class,
            TransactionTypeSeeder::class,
            UserTableSeeder::class,
            PermissionsSeeder::class,
            TransactionStatusSeeder::class,
            FundTableSeeder::class,
            CompanyTableSeeder::class,
            SettingTableSeeder::class,
            FakeDataSeeder::class
        ]);
    }
}
