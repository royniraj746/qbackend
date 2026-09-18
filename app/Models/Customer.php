<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //


    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'business_name',
        'user_type',
        'gst_number',
        'pan_number',
        'adhar_number',
        'address',
        'state',
        'city',
        'pincode',
        'country',
        'currency',
        'default_tax',
        'quotation_validity_days',
        'profile_photo',
        'website',
        'is_active',
        'created_by'
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}

}
