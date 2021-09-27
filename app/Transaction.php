<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Kirschbaum\PowerJoins\PowerJoins;

class Transaction extends Model
{
    use SoftDeletes, PowerJoins;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cost',
        'deadline_at',
        'paid_at',
        'manager_comment',
        'user_comment',
        'transaction_type_id',
        'transaction_status_id',
        'paid_as_payroll_deduction',
        'parent_transaction_id'
    ];

    public function scopePaid($query)
    {
        return $query->where('transaction_status_id', 1);
    }

    public function transactionStatus()
    {
        return $this->belongsTo(TransactionStatus::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function userPayers()
    {
        return $this->morphedByMany(User::class, 'transaction_payers');
    }

    public function userRecipients()
    {
        return $this->morphedByMany(User::class, 'transaction_recipients');
    }

    public function accountPayers()
    {
        return $this->morphedByMany(Account::class, 'transaction_payers');
    }

    public function accountRecipients()
    {
        return $this->morphedByMany(Account::class, 'transaction_recipients');
    }

    public function companyPayers()
    {
        return $this->morphedByMany(Company::class, 'transaction_payers');
    }

    public function fundPayers()
    {
        return $this->morphedByMany(Fund::class, 'transaction_payers');
    }

    public function fundRecipients()
    {
        return $this->morphedByMany(Fund::class, 'transaction_recipients');
    }

    public function allocatedLoanRecipients()
    {
        return $this->morphedByMany(AllocatedLoan::class, 'transaction_recipients');
    }

    public function allocatedLoanInstallmentRecipients()
    {
        return $this->morphedByMany(AllocatedLoanInstallment::class, 'transaction_recipients');
    }

    public function relatedPayers()
    {
        return $this->hasMany(TransactionPayer::class);
    }

    public function relatedRecipients()
    {
        return $this->hasMany(TransactionRecipient::class);
    }

    //each category might have one parent
    public function parent() {
        return $this->belongsTo(static::class, 'parent_transaction_id');
    }

    //each category might have multiple children
    public function children() {
        return $this->hasMany(static::class, 'id')->orderBy('created_at');
    }

    public function pictures()
    {
        return $this->morphToMany(Picture::class, 'attachable_case');
    }

    public function scopeHasCompanyAsPayer($query, $companyId) {
        $query->where(function (Builder $hasCompanyAsPayerOrRecipientQuery) use ($companyId) {
            $hasCompanyAsPayerOrRecipientQuery->whereHas('accountPayers.company', function (Builder $accountPayerQuery) use ($companyId) {
                $tableName = with($accountPayerQuery)->getModel()->getTable();
                $accountPayerQuery->where($tableName.'.id', $companyId);
            });

            $hasCompanyAsPayerOrRecipientQuery->orWhereHas('userPayers.accounts', function (Builder $userPayerQuery) use ($companyId) {
                $userPayerQuery->orWhereHas('company', function (Builder $companyQuery) use ($companyId) {
                    $tableName = with($companyQuery)->getModel()->getTable();
                    $companyQuery->where($tableName.'.id', $companyId);
                });
            });
        });
    }

    public function scopeHasCompanyAsRecipient($query, $companyId) {
        $query->where(function (Builder $hasCompanyAsPayerOrRecipientQuery) use ($companyId) {
            $hasCompanyAsPayerOrRecipientQuery->orWhereHas('accountRecipients.company', function (Builder $accountRecipientQuery) use ($companyId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $companyId);
            });
            $hasCompanyAsPayerOrRecipientQuery->orWhereHas('userRecipients.accounts', function (Builder $userRecipientQuery) use ($companyId) {
                $userRecipientQuery->orWhereHas('company', function (Builder $companyQuery) use ($companyId) {
                    $tableName = with($companyQuery)->getModel()->getTable();
                    $companyQuery->where($tableName.'.id', $companyId);
                });
            });
            $hasCompanyAsPayerOrRecipientQuery->orWhereHas('allocatedLoanRecipients.account.company', function (Builder $allocatedLoanRecipientQuery) use ($companyId) {
                $tableName = with($allocatedLoanRecipientQuery)->getModel()->getTable();
                $allocatedLoanRecipientQuery->where($tableName.'.id', $companyId);
            });
            $hasCompanyAsPayerOrRecipientQuery->orWhereHas('allocatedLoanInstallmentRecipients.allocatedLoan.account.company', function (Builder $allocatedLoanInstallmentRecipientQuery) use ($companyId) {
                $tableName = with($allocatedLoanInstallmentRecipientQuery)->getModel()->getTable();
                $allocatedLoanInstallmentRecipientQuery->where($tableName.'.id', $companyId);
            });
        });
    }

    public function scopeHasUserAsPayer($query, $userId) {
        $query->where(function (Builder $hasUserIdAsPayerOrRecipientQuery) use ($userId) {

            $hasUserIdAsPayerOrRecipientQuery->whereHas('accountPayers.user', function (Builder $accountPayerQuery) use ($userId) {
                $tableName = with($accountPayerQuery)->getModel()->getTable();
                $accountPayerQuery->where($tableName.'.id', $userId);
            });

            $hasUserIdAsPayerOrRecipientQuery->orWhereHas('userPayers', function (Builder $userPayerQuery) use ($userId) {
                $tableName = with($userPayerQuery)->getModel()->getTable();
                $userPayerQuery->where($tableName.'.id', $userId);
            });

        });
    }

    public function scopeHasUserAsRecipient($query, $userId) {
        $query->where(function (Builder $hasUserIdAsPayerOrRecipientQuery) use ($userId) {

            $hasUserIdAsPayerOrRecipientQuery->orWhereHas('accountRecipients.user', function (Builder $accountRecipientQuery) use ($userId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $userId);
            });
            $hasUserIdAsPayerOrRecipientQuery->orWhereHas('userRecipients', function (Builder $userRecipientQuery) use ($userId) {
                $tableName = with($userRecipientQuery)->getModel()->getTable();
                $userRecipientQuery->where($tableName.'.id', $userId);
            });

            $hasUserIdAsPayerOrRecipientQuery->orWhereHas('allocatedLoanRecipients.account.user', function (Builder $allocatedLoanRecipientQuery) use ($userId) {
                $tableName = with($allocatedLoanRecipientQuery)->getModel()->getTable();
                $allocatedLoanRecipientQuery->where($tableName.'.id', $userId);
            });
            $hasUserIdAsPayerOrRecipientQuery->orWhereHas('allocatedLoanInstallmentRecipients.allocatedLoan.account.user', function (Builder $allocatedLoanInstallmentRecipientQuery) use ($userId) {
                $tableName = with($allocatedLoanInstallmentRecipientQuery)->getModel()->getTable();
                $allocatedLoanInstallmentRecipientQuery->where($tableName.'.id', $userId);
            });

        });
    }

    public function scopeHasFundAsPayer($query, $fundId) {
        $query->where(function (Builder $hasFundAsPayerQuery) use ($fundId) {
            $hasFundAsPayerQuery->whereHas('fundPayers', function (Builder $accountPayerQuery) use ($fundId) {
                $tableName = with($accountPayerQuery)->getModel()->getTable();
                $accountPayerQuery->where($tableName.'.id', $fundId);
            });
        });
    }

    public function scopeHasFundAsRecipient($query, $fundId) {
        $query->where(function (Builder $hasFundAsRecipientQuery) use ($fundId) {
            $hasFundAsRecipientQuery->orWhereHas('fundRecipients', function (Builder $accountRecipientQuery) use ($fundId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $fundId);
            });
        });
    }

    public function scopeHasAccountAsPayer($query, $accountId) {
        $query->where(function (Builder $hasAccountAsPayerQuery) use ($accountId) {
            $hasAccountAsPayerQuery->whereHas('accountPayers', function (Builder $accountPayerQuery) use ($accountId) {
                $tableName = with($accountPayerQuery)->getModel()->getTable();
                $accountPayerQuery->where($tableName.'.id', $accountId);
            });
        });
    }

    public function scopeHasAccountAsRecipient($query, $accountId) {
        $query->where(function (Builder $hasAccountAsRecipientQuery) use ($accountId) {
            $hasAccountAsRecipientQuery->orWhereHas('accountRecipients', function (Builder $accountRecipientQuery) use ($accountId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $accountId);
            });
            $hasAccountAsRecipientQuery->orWhereHas('allocatedLoanRecipients.account', function (Builder $accountRecipientQuery) use ($accountId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $accountId);
            });
            $hasAccountAsRecipientQuery->orWhereHas('allocatedLoanInstallmentRecipients.allocatedLoan.account', function (Builder $accountRecipientQuery) use ($accountId) {
                $tableName = with($accountRecipientQuery)->getModel()->getTable();
                $accountRecipientQuery->where($tableName.'.id', $accountId);
            });
        });
    }
}
