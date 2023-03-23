<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    use HasFactory;

    protected $fillable = ['document','task_id','employee_id'];

    public function tasks()
    {
        
    }
}
