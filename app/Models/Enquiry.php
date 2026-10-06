<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    //
    protected $fillable = [
        'enquiry_code','enquiry_type',
        'lead_source','status',
        'remarks','enquiry_customer_id','created_by','worksite','ref'
      ];

      public function enquirycustomer() {
        return $this->belongsTo(EnquiryCustomer::class,'enquiry_customer_id');
      }
      public function customer()
      {
          return $this->belongsTo(Customer::class);
      }


      function createdBy(){
        return $this->belongsTo(User::class,"created_by");
      }

//       public function customer()
// {
//     return $this->belongsTo(EnquiryCustomer::class, 'enquiry_customer_id');
// }
}
