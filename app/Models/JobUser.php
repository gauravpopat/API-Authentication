<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','job_id','is_confirm','resume'];

    public function jobs()
    {
        return $this->belongsTo(Job::class,'job_id');
    }
}
