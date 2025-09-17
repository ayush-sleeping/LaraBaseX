<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends CoreModel
{
    protected $fillable = ['user_id', 'emp_id', 'personal_email', 'designation'];

    /* Get the hashid for the employee (for frontend use) :: */
    public function getHashidAttribute(): string
    {
        return $this->getRouteKey();
    }

    /**
     * @return BelongsTo<User, Employee>
     */
    public function user(): BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo(User::class);
    }
}
