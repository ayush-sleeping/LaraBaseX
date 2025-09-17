<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * CODE STRUCTURE SUMMARY:
 * Hashidable
 * Get the hashed route key for the model.
 * Encode a given key using Hashids.
 * Decode a given hashid to the original key.
 * Find a model by its hashid.
 */
trait Hashidable
{
    /* Get the hashed route key for the model */
    public function getRouteKey()
    {
        return $this->encodeKey($this->getKey());
    }

    /* Encode a given key using Hashids */
    public function encodeKey(int|string $key): string
    {
        return app('hashids')->encode($key);
    }

    /* Decode a given hashid to the original key. */
    public function decodeKey(string $hash): int|string|null
    {
        $decoded = app('hashids')->decode($hash);

        return $decoded[0] ?? null;
    }

    /* Find a model by its hashid. */
    public static function findByHashid(string $hash): ?static
    {
        $id = (new self)->decodeKey($hash);
        /**
         * @return static|null
         */
        if ($id) {
            return static::find($id);
        }
        throw (new ModelNotFoundException)->setModel(static::class, [$hash]);
    }
}
