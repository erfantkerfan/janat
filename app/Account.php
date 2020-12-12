<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fund_id',
        'acc_number',
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
}
