<?php

namespace App\Models;

use App\Traits\Hashidable;
use PHPUnit\Event\Code\Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * CoreModel: A modern base model for auditing and shared logic.
 * ---------------------------------------------------------- ::
 * - Auto-fills created_by and updated_by fields
 * - Provides creator/updator relationships and name helpers
 * - Uses HasFactory (and Hashidable if needed)
 * - PHP 8+ null-safe operators
 * - Easily extendable for more shared logic
 */


/**
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read User|null $createdBy
 * @property-read User|null $updatedBy
 */
class CoreModel extends Model
{
    use HasFactory;
    use Hashidable;

    /**
     * Set to false in child models to disable auditing.
     */
    public static bool $auditing = true;


    /**
     * Boot the model and automatically set created_by and updated_by fields on create/update.
     * Only sets fields if auditing is enabled and columns exist.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (static::$auditing && Auth::check()) {
                $userId = Auth::id();
                if ($model->hasColumn('created_by')) {
                    $model->created_by = $userId;
                }
                if ($model->hasColumn('updated_by')) {
                    $model->updated_by = $userId;
                }
            }
        });

        static::updating(function ($model) {
            if (static::$auditing && Auth::check()) {
                if ($model->hasColumn('updated_by')) {
                    $model->updated_by = Auth::id();
                }
            }
        });
    }

    /**
     * Check if the model's table has a given column.
     */
    protected function hasColumn(string $column): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }


    /**
     * Relationship: The user who created this model.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /**
     * Relationship: The user who last updated this model.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    /**
     * Get the full name of the creator (first + last name, trimmed).
     */
    public function creatorName(): string
    {
        $first = $this->createdBy?->first_name ?? '';
        $last = $this->createdBy?->last_name ?? '';
        return trim("$first $last");
    }


    /**
     * Get the full name of the last updater (first + last name, trimmed).
     */
    public function updatorName(): string
    {
        $first = $this->updatedBy?->first_name ?? '';
        $last = $this->updatedBy?->last_name ?? '';
        return trim("$first $last");
    }

    /**
     * Get a summary of audit info (creator, updater, timestamps).
     */
    public function fullAuditInfo(): array
    {
        return [
            'created_by' => $this->creatorName(),
            'created_at' => $this->created_at,
            'updated_by' => $this->updatorName(),
            'updated_at' => $this->updated_at,
        ];
    }

}
