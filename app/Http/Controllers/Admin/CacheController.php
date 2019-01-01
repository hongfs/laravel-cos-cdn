<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CacheController extends Controller
{
    /**
     * 缓存
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->isMethod('get')) {
            return view('admin/cache');
        }

        $name = request()->query('name');

        if($name == 'all') {
            redis_clear('*');
        } elseif($name == 'web') {
            redis_clear(config('cache.prefix'));
        } else {
            return error();
        }

        return result();
    }
}
