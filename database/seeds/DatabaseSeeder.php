<?php

use App\TransactionType;
use App\User;
use App\Loan;
use App\Fund;
use App\Setting;
use App\Account;
use App\Company;
use App\UserType;
use App\LoanType;
use Faker\Factory;
use App\UserStatus;
use App\Transaction;
use App\AllocatedLoan;
use App\TransactionStatus;
use Illuminate\Database\Seeder;
use App\AllocatedLoanInstallment;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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
        $this->call(UserTypeSeeder::class);
        $this->call(LoanTypeSeeder::class);
        $this->call(TransactionTypeSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(TransactionStatusSeeder::class);
        $this->call(FundTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(SettingTableSeeder::class);

        $this->call(FakeData::class);
    }
}

class UserStatusSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('user_statuses');

        UserStatus::create([
            'name' => 'active',
            'display_name' => 'فعال',
            'description' => 'در این وضعیت کاربر فعال است'
        ]);
        UserStatus::create([
            'name' => 'pending',
            'display_name' => 'در انتظار بررسی',
            'description' => 'در این حالت کاربر درخواست ثبت نام داده و منتظر تایید مدیر است'
        ]);
        UserStatus::create([
            'name' => 'inactive',
            'display_name' => 'غیر فعال',
            'description' => 'در این حالت کاربر از صندوق خارج شده'
        ]);
    }

    public static function getRandomObject() {
        return UserStatus::where('id', Factory::create()->numberBetween(1, 3))->first();
    }
}

class UserTypeSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('user_types');

        UserType::create([
            'name' => 'Permanent member',
            'display_name' => 'عضو دائم',
            'description' => 'عضوی است که مبلغی بعنوان ماهانه پرداخت می کند و طبق نوبت براساس مبلغ ماهانه خود وام نوبتی دریافت می کند'
        ]);
        UserType::create([
            'name' => 'Temporary member',
            'display_name' => 'عضو موقت',
            'description' => 'عضوی است که فقط برای دریافت وام ضروری عضو می شود.'
        ]);
        UserType::create([
            'name' => 'Good man',
            'display_name' => 'عضو خیّر (مسدودی)',
            'description' => 'عضو خیّری است که مبلغی را در صندق قرار می دهد تا بتوانند به افرادی که خودشان معرفی می کنند وام ضروری پرداخت گردد. مبلغ و مدت باز پرداخت بعهده صاحب حساب بوده و به هر میزان که معرفی می نماید حسابش مسدود میگردد و به هر میزان بازپرداخت گردد مبلغ مسدودی آزاد می گردد.که باید وضعیت حساب این اعضا همشیه بروز باشد یعنی مبلغ آزاد ، مبلغ مسدودی و کل مبلغ آن مشخص باشد.'
        ]);
    }

    public static function getRandomObject() {
        return UserType::where('id', Factory::create()->numberBetween(1, 3))->first();
    }
}

class TransactionTypeSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('transaction_types');

        TransactionType::create([
            'name' => 'user charge fund',
            'display_name' => 'کمک مالی کاربر به صندوق',
            'description' => 'کاربر خیر به صندوق کمک مالی می کند'
        ]);
        TransactionType::create([
            'name' => 'company charge fund',
            'display_name' => 'کمک مالی شرکت به صندوق',
            'description' => 'شرکت خیر به صندوق کمک مالی می کند'
        ]);
        TransactionType::create([
            'name' => 'user pay installment',
            'display_name' => 'پرداخت قسط کاربر',
            'description' => 'کاربر قسط وام خود را به حساب صندوق واریز می کند.'
        ]);
        TransactionType::create([
            'name' => 'fund pay loan',
            'display_name' => 'پرداخت وام از صندوق به حساب کاربر',
            'description' => 'صندوق مبلغ وام را به حساب کاربر واربز می کند.'
        ]);
        TransactionType::create([
            'name' => 'user withdraws from account',
            'display_name' => 'برداشت کاربر از حساب صندوق',
            'description' => 'کاربر از موجودی خود در صندوق برداشت می کند.'
        ]);
        TransactionType::create([
            'name' => 'include sub-transactions',
            'display_name' => 'شامل ریز تراکنش ها',
            'description' => 'این تراکنش شامل ریز تراکنش هایی از انواع تراکنش های دیگر است.'
        ]);
    }

    public static function getRandomObject() {
        return UserType::where('id', Factory::create()->numberBetween(1, 3))->first();
    }
}

class TransactionStatusSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('transaction_statuses');

        TransactionStatus::create([
            'name' => 'paid',
            'display_name' => 'پرداخت شده',
            'description' => ''
        ]);
        TransactionStatus::create([
            'name' => 'pending',
            'display_name' => 'در انتظار پرداخت',
            'description' => ''
        ]);
    }

    public static function getRandomObject() {
        return TransactionStatus::where('id', Factory::create()->numberBetween(1, 2))->first();
    }
}

class LoanTypeSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('loan_types');

        LoanType::create([
            'name' => 'Turnover loan',
            'display_name' => 'وام نوبتی',
            'description' => 'وامی است که طبق نوبت فقط به اعضای دائم پرداخت می شود . هنگام دریافت وام نوبتی مجدد مبلغ مانده قبلی با وام جدید جمع شده و تقسیم برتعداد قسط وام جدید می شود تا مبلغ قسط تعیین گردد . پیش فرض تعداد اقساط وام نوبتی در حال حاضر 35 ماه می باشد که باید قابل ویرایش باشد. اقساط این وام می تواند دستی پرداخت شود و یا بصورت کسراز حقوق  در قسمت بروز رسانی وام عمل شود.درصورت یکسان نشدن اقساط باید قسط آخر متفاوت گردد.ضمنا مبلغ اقساط باید تا چهار رقم گرد شود. هر عضو با هر نوع حسابی می تواند یک وام نوبتی دریافت کند.'
        ]);
        LoanType::create([
            'name' => 'Emergency loan',
            'display_name' => 'وام ضروری',
            'description' => 'عضوی است که فقط برای دریافت وام ضروری عضو می شود.'
        ]);
        LoanType::create([
            'name' => 'Interim loan',
            'display_name' => 'وام علی الحساب',
            'description' => 'وامی است که بصورت کوتاه مدت پرداخت شده و باید یکجا عودت گردد و معمولا بابت آن چک دریافت می شود.'
        ]);
    }

    public static function getRandomObject() {
        return LoanType::where('id', Factory::create()->numberBetween(1, 3))->first();
    }
}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('users');
        $userStatus = UserStatus::where('name', 'active')->first();
        DB::table('users')->insert([
            'SSN' => 'admin',
            'status_id' => $userStatus->id,
            'password' => Hash::make('janat'),
            'description' => 'کاربر ادمین',
            'created_at' => '1991-11-27 00:00:00'
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

class FundTableSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('funds');
        Fund::create([
            'name' => 'صندوق قرض الحسنه دانشگاه شهید عباسپور'
        ]);
    }
}

class CompanyTableSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('companies');
        $fund = Fund::where('id', 1)->first();
        Company::create([
            'name' => 'دانشگاه شهید عباسپور',
            'undertaker' => 'مدیر سیستم'
        ]);
    }
}

class SettingTableSeeder extends Seeder {

    public function run()
    {
        DBAssistant::resetTable('settings');
        $values = [
            [
                'name' => 'loan_interest_per_month',
                'display_name' => 'کارمزد وام برای هر ماه قسط',
                'value' => '0',
                'order' => 1
            ],
            [
                'name' => 'currency_unit',
                'display_name' => 'واحد ارز',
                'value' => 'ریال',
                'order' => 2
            ]
        ];
        foreach ($values as $value) {
            Setting::create($value);
        }
    }
}

class FakeData extends Seeder {

    public function run()
    {
        $this->call(FakeFund::class);
        $this->call(FakeCompany::class);
        $this->call(FakeUser::class);
        $this->call(FakeAccount::class);
        $this->call(FakeLoan::class);
        $this->call(FakeAllocatedLoan::class);
        $this->call(FakeAllocatedLoanInstallment::class);
        $this->call(FakeTransaction::class);
    }
}

class FakeFund extends Seeder {

    public static $countOfObject = 5;

    public function run()
    {
        $faker = Factory::create('fa_IR');
        for ($i = 1; $i < self::$countOfObject; $i++) {
            Fund::create([
                'name' => $faker->companyField(),
                'undertaker' => $faker->firstName.' '.$faker->lastName,
                'balance' => $faker->numberBetween(10000000, 50000000),
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return Fund::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeCompany extends Seeder {

    public static $countOfObject = 10;

    public function run()
    {
        $faker = Factory::create('fa_IR');
        for ($i = 1; $i < self::$countOfObject; $i++) {
            Company::create([
                'name' => $faker->company,
                'undertaker' => $faker->firstName.' '.$faker->lastName,
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return Company::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeUser extends Seeder {

    public static $countOfObject = 30;

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
        $faker = Factory::create('fa_IR');
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $company = FakeCompany::getRandomObject();
            $userStatus = UserStatusSeeder::getRandomObject();
            $userType = UserTypeSeeder::getRandomObject();
            User::create([
                'f_name' => $faker->firstName,
                'l_name' => $faker->lastName,
                'father_name' => $faker->firstName,
                'SSN' => $faker->nationalCode,
                'staff_code' => $faker->nationalCode,
                'phone' => '021'.$faker->numberBetween(11111111, 99999999),
                'mobile' => $faker->mobileNumber,
                'address' => $faker->address,
                'password' => Hash::make('1234'),
                'user_pic' => File::get(public_path('img/faces/'.$faces[Factory::create()->numberBetween(0, 9)])),
                'description' => $faker->paragraph,
                'company_id' => $company->id,
                'status_id' => $userStatus->id,
                'user_type_id' => $userType->id,
                'created_at' => $faker->dateTime
            ]);
        }
    }

    public static function getRandomObject() {
        return User::select('id', 'f_name','l_name')->where('id', Factory::create()->numberBetween(2, self::$countOfObject))->first();
    }
}

class FakeAccount extends Seeder {

    public static $countOfObject = 40;

    public function run()
    {
        $faker = Factory::create();
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $user = FakeUser::getRandomObject();
            $fund = FakeFund::getRandomObject();
            Account::create([
                'fund_id' => $fund->id,
                'user_id' => $user->id,
                'monthly_payment' => $faker->numberBetween(15000, 20000),
                'payroll_deduction' => rand(0, 1),
                'joined_at' => $faker->dateTime(),
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return Account::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeLoan extends Seeder {

    public static $countOfObject = 20;

    public function run()
    {
        $faker = Factory::create('fa_IR');
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $fund = FakeFund::getRandomObject();
            $loanType = LoanTypeSeeder::getRandomObject();
            $loan_amount = $faker->numberBetween(1000, 1000000);
            $number_of_installments = $faker->numberBetween(10, 20);
            $interest_rate = $faker->numberBetween(0, 5);
            $interest_amount = ($interest_rate/100) * $loan_amount;
            $installment_rate = ($interest_amount + $loan_amount) / $number_of_installments;
            Loan::create([
                'name' => $faker->companyField(),
                'loan_amount' => $loan_amount,
                'interest_rate' => $interest_rate,
                'interest_amount' => $interest_amount,
                'installment_rate' => $installment_rate,
                'number_of_installments' => $number_of_installments,
                'fund_id' => $fund->id,
                'loan_type_id' => $loanType->id,
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return Loan::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeAllocatedLoan extends Seeder {

    public static $countOfObject = 20;

    public function run()
    {
        $faker = Factory::create();
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $account = FakeAccount::getRandomObject();
            $loan = FakeLoan::getRandomObject();
            AllocatedLoan::create([
                'account_id' => $account->id,
                'loan_id' => $loan->id,
                'loan_amount' => $loan->loan_amount,
                'interest_rate' => $loan->interest_rate,
                'interest_amount' => $loan->interest_amount,
                'installment_rate' => $loan->installment_rate,
                'number_of_installments' => $loan->number_of_installments,
                'payroll_deduction' => rand(0, 1),
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return AllocatedLoan::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeAllocatedLoanInstallment extends Seeder {

    public static $countOfObject = 100;

    public function run()
    {
        $faker = Factory::create();
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $allocatedLoan = FakeAllocatedLoan::getRandomObject();
            AllocatedLoanInstallment::create([
                'allocated_loan_id' => $allocatedLoan->id,
                'rate' => $allocatedLoan->installment_rate,
                'created_at' => $faker->dateTime()
            ]);
        }
    }

    public static function getRandomObject() {
        return AllocatedLoanInstallment::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }
}

class FakeTransaction extends Seeder {

    public static $countOfObject = 100;

    public function run()
    {
        $faker = Factory::create('fa_IR');
        $statuses = [
            'userChargeFund',
            'companyChargeFund',
            'fundPayLoan',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment',
            'userPayInstallment'
        ];
        for ($i = 1; $i <= self::$countOfObject; $i++) {
            $status = $statuses[rand(0, count($statuses)-1)];
            $transactionStatus = TransactionStatusSeeder::getRandomObject();

            if ($status === 'userChargeFund') {
                $this->userChargeFund($faker, $transactionStatus);
            } elseif ($status === 'companyChargeFund') {
                $this->companyChargeFund($faker, $transactionStatus);
            } elseif ($status === 'fundPayLoan') {
                $this->fundPayLoan($faker, $transactionStatus);
            } elseif ($status === 'userPayInstallment') {
                $this->userPayInstallment($faker, $transactionStatus);
            }
        }
    }

    public static function getRandomObject() {
        return Transaction::where('id', Factory::create()->numberBetween(1, self::$countOfObject))->first();
    }

    private function userChargeFund($faker, $transactionStatus) {
        $account = FakeAccount::getRandomObject();
        $fund = FakeFund::getRandomObject();
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_USER_CHARGE_FUND'))->first();
        $cost = $account->monthly_payment;
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $faker->paragraph,
            'user_comment' => $faker->paragraph,
            'transaction_status_id' => $transactionStatus->id,
            'transaction_type_id' => $transactionType->id,
            'deadline_at' => $faker->dateTime(),
            'paid_at' => $faker->dateTime(),
            'created_at' => $faker->dateTime()
        ]);
        $transaction->accountPayers()->attach($account, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
    }

    private function companyChargeFund($faker, $transactionStatus) {
        $company = FakeCompany::getRandomObject();
        $fund = FakeFund::getRandomObject();
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_COMPANY_CHARGE_FUND'))->first();
        $cost = rand(1000, 10000);
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $faker->paragraph,
            'user_comment' => $faker->paragraph,
            'transaction_status_id' => $transactionStatus->id,
            'transaction_type_id' => $transactionType->id,
            'deadline_at' => $faker->dateTime(),
            'paid_at' => $faker->dateTime(),
            'created_at' => $faker->dateTime()
        ]);
        $transaction->companyPayers()->attach($company, ['cost'=> $cost]);
        $transaction->fundRecipients()->attach($fund, ['cost'=> $cost]);
    }

    private function fundPayLoan($faker, $transactionStatus) {
        $fund = FakeFund::getRandomObject();
        $allocatedLoan = FakeAllocatedLoan::getRandomObject();
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_FUND_PAY_LOAN'))->first();
        $cost = $allocatedLoan->loan_amount;
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $faker->paragraph,
            'user_comment' => $faker->paragraph,
            'transaction_status_id' => $transactionStatus->id,
            'transaction_type_id' => $transactionType->id,
            'deadline_at' => $faker->dateTime(),
            'paid_at' => $faker->dateTime(),
            'created_at' => $faker->dateTime()
        ]);
        $transaction->fundPayers()->attach($fund, ['cost'=> $cost]);
        $transaction->allocatedLoanRecipients()->attach($allocatedLoan, ['cost'=> $cost]);
    }

    private function userPayInstallment($faker, $transactionStatus) {
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_USER_PAY_INSTALLMENT'))->first();
        $allocatedLoanInstallment = FakeAllocatedLoanInstallment::getRandomObject();
        $user = $allocatedLoanInstallment->allocatedLoan->account->user()->first();

        if ($allocatedLoanInstallment->is_settled) {
            return;
        }
        if ($allocatedLoanInstallment->allocatedLoan->is_settled) {
            return;
        }
        $cost = $allocatedLoanInstallment->allocatedLoan->installment_rate;
        $transaction = Transaction::create([
            'cost' => $cost,
            'manager_comment' => $faker->paragraph,
            'user_comment' => $faker->paragraph,
            'transaction_status_id' => $transactionStatus->id,
            'transaction_type_id' => $transactionType->id,
            'deadline_at' => $faker->dateTime(),
            'paid_at' => $faker->dateTime(),
            'created_at' => $faker->dateTime()
        ]);
        $transaction->userPayers()->attach($user, ['cost'=> $cost]);
        $transaction->allocatedLoanInstallmentRecipients()->attach($allocatedLoanInstallment, ['cost'=> $cost]);
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
