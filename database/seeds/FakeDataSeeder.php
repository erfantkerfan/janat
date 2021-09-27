<?php

use App\Account;
use App\AllocatedLoan;
use App\AllocatedLoanInstallment;
use App\Classes\LoanCalculator;
use App\Company;
use App\Fund;
use App\Loan;
use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Support\Facades\File;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FakeFund::class,
            FakeCompany::class,
            FakeUser::class,
            FakeAccount::class,
            FakeLoan::class,
            FakeAllocatedLoan::class,
            FakeAllocatedLoanInstallment::class,
            FakeTransaction::class,
        ]);
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
            $company = FakeCompany::getRandomObject();
            Account::create([
                'fund_id' => $fund->id,
                'user_id' => $user->id,
                'company_id' => $company->id,
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
            // LoanCalculator
            $loanCalculator = new LoanCalculator();
            [
                $installmentRate,
                $interestAmount,
                $interestRate
            ] = $loanCalculator->prepareLoanData($loan_amount, $number_of_installments);

            Loan::create([
                'name' => $faker->companyField(),
                'loan_amount' => $loan_amount,
                'interest_rate' => $interestRate,
                'interest_amount' => $interestAmount,
                'installment_rate' => $installmentRate,
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
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_ACCOUNT_CHARGE_FUND'))->first();
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
        $transactionType = TransactionType::where('name', config('constants.TRANSACTION_TYPE_ACCOUNT_PAY_INSTALLMENT'))->first();
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
