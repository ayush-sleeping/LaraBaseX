<?php

namespace App\Models;

class Enquiry extends CoreModel
{
    protected $fillable = ['first_name', 'last_name', 'email', 'mobile', 'message', 'remark'];
}
