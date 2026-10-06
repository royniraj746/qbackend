<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name','sku','product_code','category_id','brand_id',
        'description','base_price','interest_rate','tax_rate',
        'selling_price','unit','stock_quantity','is_active','labour_cost'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->product_code = 'PRD-' . strtoupper(uniqid());
        });
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function projectCategories()
{
    return $this->belongsToMany(
        ProjectCategory::class,
        'project_category_products',
        'product_id',
        'project_category_id'
    );
}
}
