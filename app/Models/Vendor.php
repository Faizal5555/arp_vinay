<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'vendor_name', 'vendor_country', 'vendor_email',
        'vendor_manager', 'vendor_phoneno', 'vendor_whatsapp', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}