<?php

namespace App\Models;

use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRoleModel;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\RefreshesPermissionCache;

/**
 * Role Model
 *
 * Modern role model extending CoreModel with Spatie Permission integration.
 * Provides role management with permissions, teams support, and auditing.
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int|null $team_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Role extends SpatieRoleModel implements RoleContract
{
    use Hashidable;
    use HasPermissions;
    use RefreshesPermissionCache;

    /** @var array<string> */
    protected $guarded = [];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'guard_name',
        'created_by',
        'updated_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Create a new role instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->guarded[] = $this->primaryKey;
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    /**
     * Create a new role with validation.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws RoleAlreadyExists
     */
    public static function create(array $attributes = []): static
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];

        // Handle teams if enabled
        if (config('permission.teams', false)) {
            $teamsKey = config('permission.column_names.team_foreign_key', 'team_id');
            if (array_key_exists($teamsKey, $attributes)) {
                $params[$teamsKey] = $attributes[$teamsKey];
            }
        }

        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * A role may be given various permissions.
     */
    /**
     * @return BelongsToMany<Permission, Role>
     */
    public function permissions(): BelongsToMany
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.role_pivot_key', 'role_id'),
            config('permission.column_names.permission_pivot_key', 'permission_id')
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    /**
     * @return MorphToMany<User, Role>
     */
    public function users(): MorphToMany
    {
        /** @phpstan-ignore-next-line */
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.role_pivot_key', 'role_id'),
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param  string|null  $guardName
     *
     * @throws RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }

    /**
     * Find a role by its ID and guard name.
     *
     * @param  string|null  $guardName
     *
     * @throws RoleDoesNotExist
     */
    public static function findById(int|string $id, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['id' => $id, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::withId($id, $guardName);
        }

        return $role;
    }

    /**
     * Find or create role by its name (and optionally guardName).
     *
     * @param  string|null  $guardName
     */
    public static function findOrCreate(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            $attributes = ['name' => $name, 'guard_name' => $guardName];
            if (config('permission.teams', false)) {
                $teamsKey = config('permission.column_names.team_foreign_key', 'team_id');
                $attributes[$teamsKey] = null; // Default team value
            }
            /** @var RoleContract $newRole */
            $newRole = static::query()->create($attributes);

            return $newRole;
        }

        return $role;
    }

    /**
     * Find role by parameters with team support.
     *
     * @param  array<string, mixed>  $params
     */
    protected static function findByParam(array $params = []): ?static
    {
        $query = static::query();

        // Handle teams if enabled
        if (config('permission.teams', false)) {
            $teamsKey = config('permission.column_names.team_foreign_key', 'team_id');
            $teamValue = $params[$teamsKey] ?? null;
            $query->where(function ($q) use ($teamsKey, $teamValue) {
                $q->whereNull($teamsKey)
                    ->orWhere($teamsKey, $teamValue);
            });
            unset($params[$teamsKey]);
        }

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Determine if the role has the given permission.
     *
     * @param  string|int|\Spatie\Permission\Contracts\Permission  $permission
     *
     * @throws GuardDoesNotMatch
     */
    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if (config('permission.enable_wildcard_permission', false)) {
            return $this->hasWildcardPermission($permission, $guardName ?: $this->getDefaultGuardName());
        }

        $permissionClass = app(config('permission.models.permission'));

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $guardName ?: $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $guardName ?: $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Get the role display name with creator info.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name.($this->creatorName() ? ' (by '.$this->creatorName().')' : '');
    }

    /**
     * Scope to get roles for a specific guard.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Role>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Role>
     */
    public function scopeForGuard($query, string $guardName): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('guard_name', $guardName);
    }

    /**
     * Scope to get roles with permissions count.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Role>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Role>
     */
    public function scopeWithPermissionsCount($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->withCount('permissions');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the creator's name for display purposes.
     */
    public function creatorName(): ?string
    {
        return $this->createdBy?->name;
    }
}
