<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'admission_no','name','dob','gender','class_id','section_id','guardian_phone','address'
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
