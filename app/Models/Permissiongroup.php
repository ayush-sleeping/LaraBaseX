<?php

namespace App\Models;

use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permissiongroup extends CoreModel
{
    use Hashidable;

    protected $fillable = ['name', 'controller'];

    public function permissions(): HasMany
    {
        return $this->hasMany('App\Models\Permission');
    }
}
