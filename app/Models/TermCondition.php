<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermCondition extends Model
{
    use HasFactory;

    protected $table = 'term_conditions';

    protected $fillable = [
        'name',
        'title',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function quotations()
    {
        return $this->hasMany(
            Quotation::class,
            'term_conditions_id'
        );
    }
}
