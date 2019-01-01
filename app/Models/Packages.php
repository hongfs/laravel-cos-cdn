<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packages extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'alias',
        'description',
        'homepage',
        'github',
        'minversion',
        'star'
    ];

    public function scopeShow($query, bool $isHide = true)
    {
        if($isHide) {
            return $query->whereIn('visible', [0, 1]);
        }

        return $query->where('visible', 1);
    }

    public function scopePackage($query, string $name = 'name', string $value, bool $isHide = true)
    {
        return  $query->where($name, $value)
                    ->show($isHide);
    }

    public function scopePackageShow($query, string $name = 'name', string $value)
    {
        return $query->package($name, $value, false);
    }

    public function belongsToLog()
    {
        return $this->belongsTo('App\Models\PackagesLog', 'pid', 'id');
    }

    public function hasManyLogs()
    {
        return $this->hasMany('App\Models\PackagesLog', 'pid', 'id');
    }

    public function logs()
    {
        return $this->hasManyLogs();
    }
}
