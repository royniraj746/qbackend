<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryCustomer extends Model
{
    //

    protected $fillable = [
        'customer_name','mobile','email',
        'business_name','business_category',
        'address','city','state','pincode','gst_number','pan_number','adhar_number','created_by','country'
      ];

      public function enquiries() {
        return $this->hasMany(Enquiry::class);
      }
      function createdBy(){
        return $this->belongsTo(User::class,"created_by");
      }
}

