<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = ['name','package','company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
