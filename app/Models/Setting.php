<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    protected $fillable = [
        'bank_name',
        'account_number',
        'Iban_number',
        'online_token',
        'bearer_token',
        'sender_name',
        'school_subscribe_price',
        'teacher_subscribe_price',
        'invitation_code_discount',
        'invitation_code_commission',
        'logo',
        'contact_number',
        'payment_type',
        'site_url',
        'contact_email',
    ];
}
