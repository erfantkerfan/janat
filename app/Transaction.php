<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;

class Transaction extends Model
{
    use SoftDeletes, PowerJoins;

    public function scopePaid($query)
    {
        return $query->where('transaction_status_id', 1);
    }

    public function transactionStatus()
    {
        return $this->belongsTo(TransactionStatus::class);
    }

    public function userPayers()
    {
        return $this->morphedByMany(User::class, 'transaction_payers');
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
}
