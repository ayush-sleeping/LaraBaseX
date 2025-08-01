<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

/**
     * Global scope to restrict queries to the current user and their descendants.
     *
     * Excluded models can be configured via constructor or config.
 */
class HierarchyScope implements Scope
{
    /**
         * Models to exclude from this scope.
         *
         * @var array<int, class-string>
     */
    protected array $excludedModels;

    /**
         * Auth manager instance.
         *
         * @var \Illuminate\Contracts\Auth\Factory
     */
    protected AuthFactory $auth;

    /**
         * Create a new HierarchyScope instance.
         *
         * @param AuthFactory $auth
         * @param array<int, class-string> $excludedModels
     */
    public function __construct(AuthFactory $auth, array $excludedModels = [])
    {
        $this->auth = $auth;
        $this->excludedModels = $excludedModels ?: [
            'App\Models\Role',
            'App\Models\User',
            'App\Models\Enquiry',
        ];
    }

    /**
         * Apply the scope to a given Eloquent query builder.
         *
         * @param Builder $builder
         * @param Model $model
         * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (in_array(get_class($model), $this->excludedModels, true)) {
            return;
        }
        $user = $this->auth->user();
        if (!$user || !method_exists($user, 'getDescendantIds')) {
            return;
        }
        $builder->whereIn($model->getTable() . '.created_by', $user->getDescendantIds());
    }
}
