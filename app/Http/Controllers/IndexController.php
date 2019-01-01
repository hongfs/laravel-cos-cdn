<?php

namespace App\Http\Controllers;

use App\Models\Packages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * 缓存时间
     *
     * @var
     */
    protected $cache_expire;

    /**
     * 获取Package
     *
     * @param  string  $alias 别名
     * @param  bool  $isVersionList 是否读取版本列表
     * @return array|bool
     */
    protected function getPackage($alias, $isVersionList = false)
    {
        $package =  Packages::where('alias', $alias)
                        ->where('visible', 1)
                        ->first();

        if(!$package) return false;

        if($isVersionList) {
            $package->version = getVersionList($package->name, $package->minversion);
    
            if(!$package->version) return false;
        }

        return $package->toArray();
    }

    /**
     * 后缀筛选
     *
     * @param  array  $data 数据
     * @param  array  $suffix 后缀列表
     * @return array
     */
    protected function filterSuffix($data, $suffix)
    {
        if(!count($suffix)) {
            return $data;
        }

        return collect($data)->filter(function($item) use($suffix) {
            $isMatched = preg_match_all('/\.(\w+)$/i', $item, $matches);

            if(!$isMatched || !in_array(substr($matches[0][0], 1), $suffix)) return false;

            return $item;
        })->values()->all();
    }

    public function __construct()
    {
        $this->cache_expire = now()->addMinutes(config('cdn.cache_expire'));
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return cache()->remember('index', $this->cache_expire, function() {
            $packages = Packages::select('name', 'alias', 'description', 'minversion', 'star')
                            ->where('visible', 1)
                            ->latest('star')
                            ->latest('id')
                            ->get();

            $packages =  $packages->filter(function($package) {
                return getVersionList($package->name, $package->minversion);
            })->map(function($package) {
                $package['name'] = $package['alias'];
                unset($package['alias']);
                return $package;
            });

            return  view('index/index')
                        ->with('packages', $packages->values()->toArray())
                        ->with('isStar', $packages->where('star', 1)->count())
                        ->render();
        });
    }

    /**
     * 详情
     *
     * @param  string  $alias  别名
     * @return \Illuminate\Http\Response
     */
    public function show(string $alias)
    {
        return cache()->remember(
            'show:' . $alias,
            $this->cache_expire,
            function() use($alias) {
                $package = $this->getPackage($alias, true);

                if(!$package) return abort(404);
        
                return  view('index/item', $package)
                            ->render();
            }
        );
    }

    /**
     * 版本
     *
     * @param  string  $alias  别名
     * @param  string  $version  版本
     * @return \Illuminate\Http\Response
     */
    public function version(string $alias, string $version)
    {
        return cache()->remember(
            sprintf('version:%s:%s', $alias, $version),
            $this->cache_expire,
            function() use($alias, $version) {
                $package =  $this->getPackage($alias);

                if(!$package) return error();

                if(version_compare($package['minversion'], $version) == 1) return error();

                $data = Redis::hget('libraries:' . $package['name'], $version);

                if(!$data) return error();

                $list = $this->filterSuffix(
                            explode(',', $data),
                            config('cdn.suffix_show', [])
                        );

                return $list === false ? error('获取列表失败') : result($list);
            }
        );
    }
}
