<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'group', 'key', 'label', 'meta', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
