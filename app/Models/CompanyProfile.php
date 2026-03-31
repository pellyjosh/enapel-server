<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';

    protected $fillable = [
        'name',
        'email',
        'logo',
        'modules',
    ];

    protected $casts = [
        'modules' => 'array',
    ];
}
