<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'client_name',
        'client_country',
        'client_email',
        'client_manager',
        'client_phoneno',
        'client_whatsapp',
        'user_id',
    ];

    // Define the user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

