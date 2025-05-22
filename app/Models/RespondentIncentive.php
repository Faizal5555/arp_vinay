<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondentIncentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'date','pn_no', 'respondent_name', 'email_id', 'contact_number',
        'speciality', 'incentive_amount', 'incentive_form',
        'start_date', 'end_date', 'payment_date',
        'payment_type'
    ];
}
