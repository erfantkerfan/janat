<?php

use App\LoanType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DBAssistant::resetTable('loan_types');

        LoanType::insert($this->getValues());
    }

    public function getValues () {
        return [
            [
                'name' => 'Turnover loan',
                'display_name' => 'وام نوبتی',
                'description' => 'وامی است که طبق نوبت فقط به اعضای دائم پرداخت می شود . هنگام دریافت وام نوبتی مجدد مبلغ مانده قبلی با وام جدید جمع شده و تقسیم برتعداد قسط وام جدید می شود تا مبلغ قسط تعیین گردد . پیش فرض تعداد اقساط وام نوبتی در حال حاضر 35 ماه می باشد که باید قابل ویرایش باشد. اقساط این وام می تواند دستی پرداخت شود و یا بصورت کسراز حقوق  در قسمت بروز رسانی وام عمل شود.درصورت یکسان نشدن اقساط باید قسط آخر متفاوت گردد.ضمنا مبلغ اقساط باید تا چهار رقم گرد شود. هر عضو با هر نوع حسابی می تواند یک وام نوبتی دریافت کند.'
            ],
            [
                'name' => 'Emergency loan',
                'display_name' => 'وام ضروری',
                'description' => 'عضوی است که فقط برای دریافت وام ضروری عضو می شود.'
            ],
            [
                'name' => 'Interim loan',
                'display_name' => 'وام علی الحساب',
                'description' => 'وامی است که بصورت کوتاه مدت پرداخت شده و باید یکجا عودت گردد و معمولا بابت آن چک دریافت می شود.'
            ]
        ];
    }

    public static function getRandomObject() {
        $values = (new LoanTypeSeeder)->getValues();
        return LoanType::where('id', Factory::create()->numberBetween(1, count($values)))->first();
    }
}
