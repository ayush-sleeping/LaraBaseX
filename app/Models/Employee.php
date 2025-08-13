<?php

namespace App\Models;

use App\Models\CoreModel;
use Illuminate\Database\Eloquent\Model;

class Employee extends CoreModel
{
    protected $fillable = ['user_id','emp_id','personal_email','designation'];

    /* Get the hashid for the employee (for frontend use) :: */
    public function getHashidAttribute(): string
    {
        return $this->getRouteKey();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
