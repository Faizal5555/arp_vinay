<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;


class PendingProject extends Model
{
    use HasFactory;
    protected $fillable = [
        'entry_date',
        'fy',
        'quarter',
        'client_id',
        'company_name',
        'pn_no',
        'email_subject',
        'commission_date',
        'currency_amount',
        'original_revenue',
        'margin',
        'final_invoice_amount',
        'comments',
        'supplier_name',
        'supplier_payment_details',
        'total_incentives_paid',
        'incentive_paid_date',
        'invoice_number',
        'invoice_status',
        'partial_comment',
        'original_revenue_total',
        'invoice_amount_total',
        'incentives_paid_total',
        'user_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}
