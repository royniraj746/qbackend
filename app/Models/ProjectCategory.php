<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by'
    ];

    // Creator Relationship
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function products()
{
    return $this->belongsToMany(
        Product::class,
        'project_category_products',
        'project_category_id',
        'product_id'
    );
}
}
