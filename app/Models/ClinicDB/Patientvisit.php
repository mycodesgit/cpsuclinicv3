<?php

namespace App\Models\ClinicDB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patientvisit extends Model
{
    use HasFactory;
    protected $table ='patientvisits';

    protected $fillable=[
        'stid',
        'date',
        'time',
        'chief_complaint',
        'treatment',
        'medicine',
        'qty',
        'certificate',
    ];
   
    public function patients()
    {
        return $this->belongstomany(Patients::class, 'id');
    } 

    public function medicines()
    {
        return $this->belongstomany(Medicine::class, 'id');
    }
}

