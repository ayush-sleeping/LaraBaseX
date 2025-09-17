<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Cacheable;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements OAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Cacheable, HasApiTokens, HasFactory, Hashidable, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'status',
        'device_id',
        'avatar',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cache configuration
     */
    /** @var int */
    protected $cacheTTL = 1800; // 30 minutes

    /** @var string[] */
    protected $cacheTags = ['users', 'auth'];

    /**
     * The attributes that should be appended to model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'name',
        'full_name',
        'initials',
        'avatar_url',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'ACTIVE',
    ];

    protected $casts = [
        'parent_id' => 'array',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's name (alias for full_name for frontend compatibility).
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');

        // Ensure we always return a string, never null
        $fullName = trim($firstName.' '.$lastName);

        return $fullName ?: 'Unknown User';
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute(): string
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        $firstInitial = $firstName ? strtoupper(substr($firstName, 0, 1)) : '';
        $lastInitial = $lastName ? strtoupper(substr($lastName, 0, 1)) : '';

        return $firstInitial.$lastInitial;
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        // Return a default avatar or generate one based on initials
        return 'https://ui-avatars.com/api/?name='.urlencode($this->full_name).'&background=random';
    }

    /**
     * Get the user who created this record.
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, User>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, static>
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, static>
     */
    public function updator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return array<int, string>
     */
    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCacheStats(): array
    {
        // ...existing code...
        return [];
    }

    public static function cachedFirst(): ?static
    {
        // ...existing code...
        return null;
    }

    public static function cachedLatest(): ?static
    {
        // ...existing code...
        return null;
    }

    public static function cachedOldest(): ?static
    {
        // ...existing code...
        return null;
    }

    public static function cachedFind(int|string $id): ?static
    {
        // ...existing code...
        return null;
    }

    public static function cachedWhere(string $column, string $operator, mixed $value): ?static
    {
        // ...existing code...
        return null;
    }

    /**
     * @return array<int, string>
     */
    public static function cachedPluck(string $column): array
    {
        // ...existing code...
        return [];
    }

    protected static function bootCacheable(): void
    {
        // ...existing code...
    }

    /**
     * Configure activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name',
                'last_name',
                'email',
                'mobile',
                'status',
                'avatar',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
