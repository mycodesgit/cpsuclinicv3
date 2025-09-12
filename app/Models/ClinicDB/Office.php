<?php

namespace App\Models\ClinicDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name',
        'office_abbr',
        'orders',
        'group_by',
    ];

}
