<?php

use Faker\Factory;
use App\TransactionType;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('transaction_types');

        TransactionType::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'user charge fund',
                'display_name' => 'کمک مالی کاربر به صندوق',
                'description' => 'کاربر خیر به صندوق کمک مالی می کند'
            ],
            [
                'name' => 'user pay the fund tuition',
                'display_name' => 'پرداخت ماهانه صندوق',
                'description' => 'گاربر ماهانه(شهریه) صندوق را پرداخت می کند.'
            ],
            [
                'name' => 'user withdraw from account',
                'display_name' => 'برداشت وجه کاربر از صندوق',
                'description' => 'کاربر از موجودی حساب خود در صندوق برداشت می کند'
            ],
            [
                'name' => 'company charge fund',
                'display_name' => 'کمک مالی شرکت به صندوق',
                'description' => 'شرکت خیر به صندوق کمک مالی می کند'
            ],
            [
                'name' => 'user pay installment',
                'display_name' => 'پرداخت قسط کاربر',
                'description' => 'کاربر قسط وام خود را به حساب صندوق واریز می کند.'
            ],
            [
                'name' => 'fund pay loan',
                'display_name' => 'پرداخت وام از صندوق به حساب کاربر',
                'description' => 'صندوق مبلغ وام را به حساب کاربر واربز می کند.'
            ],
            [
                'name' => 'pay fund expenses',
                'display_name' => 'پرداخت هزینه های صندوق',
                'description' => 'پرداخت هزینه های مربوط به امور صندوق از جانب در آمد های صندوق مانند ماهانه ها و یا کارمزد وام ها.'
            ],
            [
                'name' => 'user withdraws from account',
                'display_name' => 'برداشت کاربر از حساب صندوق',
                'description' => 'کاربر از موجودی خود در صندوق برداشت می کند.'
            ],
            [
                'name' => 'include sub-transactions',
                'display_name' => 'شامل ریز تراکنش ها',
                'description' => 'این تراکنش شامل ریز تراکنش هایی از انواع تراکنش های دیگر است.'
            ],
            //            [
            //                'name' => 'monthly payment of the user to the fund',
            //                'display_name' => 'پرداخت ماهانه کاربر به صندوق',
            //                'description' => 'ماهانه ای که کاربر می بایست هر ماه به صندوق واریز کند'
            //            ]
        ];
    }

    public static function getRandomObject() {
        $values = (new TransactionTypeSeeder)->getValues();
        return TransactionType::where('id', Factory::create()->numberBetween(1, count($values)))->first();
    }
}
