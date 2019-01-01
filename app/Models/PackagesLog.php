<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagesLog extends Model
{
    protected $table = 'packages_logs';

    public function hasOnePackage()
    {
        return $this->hasOne('App\Models\Packages', 'id', 'pid');
    }

    public function belongsToLogs()
    {
        return $this->belongsTo('App\Models\PackagesLog', 'pid', 'id');
    }

    public function package()
    {
        return $this->hasOnePackage();
    }

    public function logs()
    {
        return $this->belongsToLogs();
    }
}
