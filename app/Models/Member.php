<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'member_code',
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}