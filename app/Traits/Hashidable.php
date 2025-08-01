<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait Hashidable
{
    /**
        * Get the hashed route key for the model.
     */
    public function getRouteKey()
    {
        return $this->encodeKey($this->getKey());
    }


    /**
        * Encode a given key using Hashids.
     */
    public function encodeKey($key)
    {
        return app('hashids')->encode($key);
    }


    /**
        * Decode a given hashid to the original key.
     */
    public function decodeKey($hash)
    {
        $decoded = app('hashids')->decode($hash);
        return $decoded[0] ?? null;
    }


    /**
        * Find a model by its hashid.
     */
    public static function findByHashid($hash)
    {
        $id = (new static)->decodeKey($hash);
        if ($id) {
            return static::find($id);
        }
        throw (new ModelNotFoundException)->setModel(static::class, [$hash]);
    }
}
