<?php

namespace App;

use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDeduction extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'to',
        'from',
        'paid_at',
        'paid_for_loan',
        'paid_for_monthly_payment'
    ];


    public function scopePaidForLoan($query)
    {
        return $query->where('paid_for_loan', 1);
    }

    public function scopePaidForMonthlyPayment($query)
    {
        return $query->where('paid_for_monthly_payment', 1);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at');
    }


}
