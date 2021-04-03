<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kirschbaum\PowerJoins\PowerJoins;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use SoftDeletes, PowerJoins, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name',
        'l_name',
        'father_name',
        'SSN',
        'staff_code',
        'password',
        'salary',
        'address',
        'phone',
        'mobile',
        'email',
        'description',
        'user_pic',
        'company_id',
        'user_type_id',
        'status_id'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
//        'count_of_settled_allocated_loans',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'user_pic',
        'remember_token'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function status()
    {
        return $this->belongsTo(UserStatus::class);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function paidTransactions()
    {
        return $this->morphToMany(Transaction::class, 'transaction_payers');
    }

    public function getHasNotSettledLoanAttribute()
    {
        $hasNotSettledLoan = false;
        $userWithAccounts = $this->accounts()->get()->map(function (& $item) {
            return $item->setAppends(['hasNotSettledLoan']);
        });

        $userWithAccounts->each(function ($userWithAccount) use (& $hasNotSettledLoan) {
            if ($userWithAccount->hasNotSettledLoan) {
                $hasNotSettledLoan = true;
            }
        });

        return $hasNotSettledLoan;
    }

    public function getCountOfAllocatedLoansAttribute()
    {
        return $this->accounts->reduce(function ($carry, $item) {
            return $carry + $item->allocatedLoans()->count();
        });
    }

    public function getCountOfSettledAllocatedLoansAttribute()
    {
        return $this->accounts->reduce(function ($carry, $item) {
            $accountSettlesAllocatedLoans = $item->allocatedLoans()
                ->get()
                ->map(function ($allocatedLoan) {
                    return $allocatedLoan->append('is_settled');
                })
                ->where('is_settled', true)
                ->count();
            return $carry + $accountSettlesAllocatedLoans;
        });
    }

    public function scopeHasLoanPayrollDeduction($query) {
        $query->whereHas('accounts.allocatedLoans', function($q){
            $q->where('allocated_loans.payroll_deduction', '=', 1);
        });
    }

    public function scopeHasAccountPayrollDeduction($query) {
        $query->whereHas('accounts', function($q){
            $q->where('accounts.payroll_deduction', '=', 1);
        });
    }
}
