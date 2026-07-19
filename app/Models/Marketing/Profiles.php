<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = [
        'id', 'user_id', 'title', 'firstname', 'lastname', 'email', 'telephone',
        'account_type', 'company_name', 'industry', 'employees_number',
        'is_email', 'is_phone', 'is_sms', 'is_post',
    ];
}
