<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Gst extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gst_percentage',
        'slug',
        'is_active'
    ];
}
