<?php

namespace App\Models;

use App\Models\CoreModel;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends CoreModel
{
    protected $fillable = ['first_name','last_name','email','mobile','message','remark'];

}
