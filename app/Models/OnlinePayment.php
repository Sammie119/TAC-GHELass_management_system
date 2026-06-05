<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnlinePayment extends Model
{
    use HasFactory;

    protected $table = 'online_payments';

    protected $fillable = [
        'member_id', 'category', 'amount', 'currency',
        'phone', 'reference', 'provider',
        'status', 'notes', 'confirmed_at', 'confirmed_by',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
