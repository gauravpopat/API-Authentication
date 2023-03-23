<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['first_name','last_name','company','email','phone','joining_date'];

    public function tasks()
    {
        return $this->hasMany(Task::class,'employee_id','id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company','id');
    }
}
