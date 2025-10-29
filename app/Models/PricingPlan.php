<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name','type','price','duration_days'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'plan_course')->withPivot('override_price');
    }
}
