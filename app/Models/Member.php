<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email',
        'qr_code', 'member_id_card', 'photo',
        'date_of_birth', 'gender', 'address', 'status',
        'department', 'tacms_number', 'otp', // ← add these
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'deleted_at'    => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        static::creating(function ($member) {
            $member->qr_code = (string) Str::uuid();
            $member->member_id_card = 'EL-' . str_pad(
                    Member::withTrashed()->max('id') + 1, 5, '0', STR_PAD_LEFT
                );
        });
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function absenteeFlag()
    {
        return $this->hasOne(AbsenteeFlag::class);
    }

    public function incomeRecords()
    {
        return $this->hasMany(IncomeRecord::class);
    }

    public function onlinePayments()
    {
        return $this->hasMany(OnlinePayment::class);
    }

    public function cellGroups()
    {
        return $this->belongsToMany(CellGroup::class, 'cell_group_members')
            ->withPivot('joined_date', 'is_leader')
            ->withTimestamps();
    }
}
