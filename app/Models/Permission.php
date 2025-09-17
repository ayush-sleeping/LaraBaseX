<?php

namespace App\Models;

use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission as SpatiePermissionModel;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\RefreshesPermissionCache;

class Permission extends SpatiePermissionModel implements PermissionContract
{
    use Hashidable;
    use HasRoles;
    use RefreshesPermissionCache;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->guarded[] = $this->primaryKey;
    }

    protected $fillable = ['permissiongroup_id', 'name', 'methods'];

    protected $casts = [
        'methods' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Permissiongroup, Permission>
     */
    public function permissiongroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo('App\\Models\\Permissiongroup');
    }

    public function getTable()
    {
        return config('permission.table_names.permissions', parent::getTable());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function create(array $attributes = []): static
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);
        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * A permission can be applied to roles.
     */
    /**
     * @return BelongsToMany<Role, Permission>
     */
    public function roles(): BelongsToMany
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.permission_pivot_key', 'permission_id'),
            config('permission.column_names.role_pivot_key', 'role_id')
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<User, Permission>
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        /** @phpstan-ignore-next-line */
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.permission_pivot_key', 'permission_id'),
            config('permission.column_names.model_morph_key')
        );
    }

    public static function findByName(string $name, ?string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    public static function findById(string|int $id, ?string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['id' => $id, 'guard_name' => $guardName]);
        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    public static function findOrCreate(string $name, ?string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (! $permission) {
            /** @var PermissionContract */
            $created = static::query()->create(['name' => $name, 'guard_name' => $guardName]);

            return $created;
        }

        return $permission;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return Collection<int, Permission>
     */
    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected static function getPermission(array $params = []): ?PermissionContract
    {
        return static::getPermissions($params, true)->first();
    }
}
