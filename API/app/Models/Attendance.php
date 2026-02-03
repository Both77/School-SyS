<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['date','student_id','class_id','section_id','status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
