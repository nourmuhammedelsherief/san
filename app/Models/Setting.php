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
    ];
}
