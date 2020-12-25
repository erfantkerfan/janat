<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Loan extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'loan_amount',
        'interest_rate',
        'interest_amount',
        'installment_rate',
        'number_of_installments',
        'fund_id',
        'loan_type_id'
    ];

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function allocatedLoans()
    {
        return $this->hasMany(AllocatedLoan::class);
    }
}
