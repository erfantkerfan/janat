<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Kirschbaum\PowerJoins\PowerJoins;

class Account extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fund_id',
        'monthly_payment',
        'payroll_deduction',
        'joined_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function allocatedLoans()
    {
        return $this->hasMany(AllocatedLoan::class);
    }

    public function salaries()
    {
        return $this->morphToMany(Transaction::class, 'transaction_payers');
    }

    public function paidSalaries()
    {
        return $this->salaries()->where('transaction_status_id', 1);
    }

    public function totalPaidSalaries()
    {
        return $this->paidSalaries()->get()->sum('cost');
    }

    public function scopeHasPayrollDeduction($query) {
        $query->where('payroll_deduction', 1);
    }

    public function scopeLastPayrollDeductionForChargeFundNotPaidAt($query, $operator, $date, $operator2 = null, $date2 = null) {
        $whereClause1 = "AND `transactions`.`paid_at` $operator '$date'";
        $whereClause2 = (isset($operator2) && isset($date2)) ? "AND `transactions`.`paid_at` $operator2 '$date2'" : '';
        $rawQuery = "
            SELECT `id`
            FROM (
                SELECT `accounts`.`id`, `transactions`.`paid_at`
                FROM `transactions`
                INNER JOIN `transaction_payers` ON `transactions`.`id` = `transaction_payers`.`transaction_id`
                INNER JOIN `transaction_recipients` ON `transactions`.`id` = `transaction_recipients`.`transaction_id`
                AND `transaction_recipients`.`transaction_recipients_type` = 'App\\\Fund'
                INNER JOIN `accounts` ON `transaction_payers`.`transaction_payers_id` = `accounts`.`id`
                AND `transaction_payers`.`transaction_payers_type` = 'App\\\Account'
                AND `accounts`.`deleted_at` IS NULL
                WHERE `transactions`.`paid_as_payroll_deduction` = 1
                AND `transactions`.`deleted_at` IS NULL
                $whereClause1
                $whereClause2
                ORDER BY `transactions`.`paid_at` DESC
            ) AS tbl";

        $result = DB::select($rawQuery);
        $query->whereNotIn('id', collect($result)->map(function($x){ return (array) $x; })->toArray());
    }
}
