<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralSignup extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'ip_address',
        'user_agent',
        'bonus_awarded',
    ];

    protected function casts(): array
    {
        return [
            'bonus_awarded' => 'boolean',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
}
