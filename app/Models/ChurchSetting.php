<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChurchSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'logo_path', 'pastor_signature_path', 'finance_signature_path',
    ];

    public static function current(): self
    {
        return static::first() ?? new static;
    }
}
