<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    const CREATED_AT = NULL;

    const UPDATED_AT = NULL;

    protected $table = 'option';

    protected $fillable = [
        'name',
        'value'
    ];

    public function scope_get($query, $names)
    {
        $names = is_array($names) ? $names : [$names];

        $data = $query->whereIn('name', $names)
                    ->pluck('value', 'name')
                    ->toArray();

        $result = [];

        foreach($names as $name) {
            $result[$name] = isset($data[$name]) ? $data[$name] : null;
        }

        return $result;
    }

    public function scope_set($query, $name, $value = '')
    {
        return $query->updateOrCreate([
            'name'  => $name
        ], [
            'value' => $value
        ]);
    }
}
