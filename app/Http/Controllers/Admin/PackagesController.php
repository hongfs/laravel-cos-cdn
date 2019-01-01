<?php

namespace App\Http\Controllers\Admin;

use App\Models\Packages;
use App\Models\PackagesLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class PackagesController extends Controller
{
    /**
     * 禁止名称列表
     *
     * @var array
     */
    protected $notName = [
        'index',
        'admin',
        'package',
        'log'
    ];

    /**
     * 验证
     * 
     * 验证正确返回true,否则返回错误信息
     * @param  array  $input  验证内容
     * @param  int  $id  排除ID
     * @return string|boolean
     */
    protected function validator(array $input, int $id = 0)
    {
        $v = validator($input, [
            'alias'         => [
                'required',
                Rule::unique('packages')->ignore($id)->where(function($query) {
                    return $query->whereIn('visible', [0, 1]);
                })
            ],
            'description'   => 'max:1000',
            'homepage'      => 'nullable|url|max:255',
            'github'        => 'nullable|url|max:255'
        ]);

        return $v->fails() ? $v->errors()->first() : true;
    }

    /**
     * 是否为禁止名称
     *
     * @param string  $alias  别名
     * @return void
     */
    public function is_not_name(string $name)
    {
        return in_array($name, $this->notName);
    }

    /**
     * 清除缓存
     *
     * @param string  $alias  别名
     * @return void
     */
    protected function cache_clear(string $alias)
    {
        cache()->forget('index');

        Redis::del(config('cache.prefix') . ':show:'. $alias);
        redis_clear(config('cache.prefix') . ':version:'. $alias);
    }

    /**
     * 列表
     *
     * @param  \App\Models\Packages  $packages
     * @return \Illuminate\Http\Response
     */
    public function index(Packages $packages)
    {
        $query = request()->query();

        if(isset($query['query'])) {
            $packages = $packages->where('name', 'like', '%' . $query['query'] . '%')
                            ->where('alias', 'like', '%' . $query['query'] . '%')
                            ->where('description', 'like', '%' . $query['query'] . '%');
        }

        $list = $packages->show()
                    ->latest('star')
                    ->latest('id')
                    ->paginate(config('cdn.page_size'));

        return view('admin/packages/list', [
            'list' => $list
        ]);
    }

    /**
     * 创建视图
     *
     * @param  \App\Models\Packages  $packages
     * @param string|null  $name  名称
     * @return \Illuminate\Http\Response
     */
    public function create(Packages $packages, $name = NULL)
    {
        if($name) {
            return view('admin/packages/edit', [
                'type' => 'add',
                'name' => $name
            ]);
        }

        $list = $packages->show()
                    ->pluck('name')
                    ->toArray();

        return view('admin/packages/search', [
            'list' => $list
        ]);
    }

    /**
     * 存储
     *
     * @param  \App\Models\Packages  $packages
     * @param string  $name  名称
     * @return \Illuminate\Http\Response
     */
    public function store(Packages $packages, string $name)
    {
        $exist =    $packages->package('name', $name)
                        ->exists();

        if($exist) {
            return error('请勿重复添加');
        }

        $input = request()->all();

        if(!isset($input['alias'])) $input['alias'] = $name;

        if($this->is_not_name($name)) {
            return error($name . '为保留字段');
        }

        if($this->is_not_name($input['alias'])) {
            return error($input['alias'] . '为保留字段');
        }

        $v = $this->validator($input);

        if($v !== true) {
            return error($v);
        }

        $result = DB::transaction(function() use($name, $input) {
            $package = new Packages;
            $package->name = $name;
            $package->alias = $input['alias'];
            if(isset($input['description'])) $package->description = $input['description'];
            if(isset($input['homepage'])) $package->homepage = $input['homepage'];
            if(isset($input['github'])) $package->github = $input['github'];
            if(isset($input['minversion'])) $package->minversion = $input['minversion'];
            return $package->save();
        });

        cache()->forget('index');

        return $result ? result() : error('添加失败');
    }

    public function show(Packages $packages, string $name)
    {
        $package =  $packages->package('alias', $name)
                        ->first();

        if(!$package) {
            return error();
        }

        $package->version = getVersionList($package->name, $package->minversion);

        return view('admin/packages/show', $package->toArray());
    }

    /**
     * 修改视图
     *
     * @param  \App\Models\Packages  $packages
     * @param  string  $name  名称
     * @return \Illuminate\Http\Response
     */
    public function edit(Packages $packages, string $name)
    {
        $package =  $packages->package('name', $name)
                        ->first();

        if(!$package) {
            return error();
        }
    
        return view('admin/packages/edit', [
            'type' => 'edit',
            'name' => $package->name,
            'data' => $package->toArray()
        ]);
    }

    /**
     * 更新
     *
     * @param  \App\Models\Packages  $packages
     * @param  string  $name  名称
     * @return \Illuminate\Http\Response
     */
    public function update(Packages $packages, string $name)
    {
        $package =  $packages->package('name', $name)
                        ->first();

        if(!$package) {
            return error();
        }

        $alias = $package->alias;

        $input = request()->all();

        if(!isset($input['alias'])) $input['alias'] = $name;

        if($this->is_not_name($name)) {
            return error($name . '为保留字段');
        }

        if($this->is_not_name($input['alias'])) {
            return error($input['alias'] . '为保留字段');
        }

        $v = $this->validator($input, $package->id);

        if($v !== true) {
            return error($v);
        }

        $storagePrefix = config('cdn.storage.path');

        if($package->alias != $input['alias']) {
            Storage::folderRename(
                $storagePrefix . $package->alias,
                $storagePrefix . $input['alias']
            );
        }

        $package->alias         = $input['alias'];
        $package->description   = $input['description'] ?? NULL;
        $package->homepage      = $input['homepage']    ?? NULL;
        $package->github        = $input['github']      ?? NULL;
        $package->minversion    = $input['minversion']  ?? NULL;

        $package->save();

        $this->cache_clear($alias);

        return result();
    }

    /**
     * 日志
     *
     * @param  \App\Models\PackagesLog  $logs
     * @return \Illuminate\Http\Response
     */
    public function log(PackagesLog $logs)
    {
        $query = request()->query();

        $list = $logs->whereHas('package', function($q) use($query) {
                    if(isset($query['package'])) {
                        $q->where('alias', $query['package']);
                    }

                    if(isset($query['query'])) {
                        $q->where('alias', 'like', '%' . $query['query'] . '%')
                            ->orWhere('description', 'like', '%' . $query['query'] . '%');
                    }

                    $q->select('id', 'alias')
                        ->show();
                })
                ->latest('id')
                ->paginate(config('cdn.page_size'));

        return view('admin/packages/log', [
            'list'  => $list
        ]);
    }

    /**
     * 星标
     *
     * @param  \App\Models\Packages  $packages
     * @param  string  $name  名称
     * @param  int  $star  星标
     * @return \Illuminate\Http\Response
     */
    public function star(Packages $packages, string $name, int $star)
    {
        $package =  $packages->package('name', $name)
                        ->whereNotIn('star', [(int) $star])
                        ->first();

        if(!$package) {
            return error();
        }

        $this->cache_clear($package->alias);

        $result =   $packages->where('id', $package->id)
                        ->update([
                            'star' => $star
                        ]);

        return $result ? result() : error('修改星标失败');
    }

    /**
     * 状态
     *
     * @param  \App\Models\Packages  $packages
     * @param  string  $name  名称
     * @param  int  $status  状态
     * @return \Illuminate\Http\Response
     */
    public function status(Packages $packages, string $name, int $status)
    {
        $package =  $packages->package('name', $name)
                        ->whereNotIn('visible', [(int) $status])
                        ->first();

        if(!$package) {
            return error();
        }

        $this->cache_clear($package->alias);

        $result =   $packages->where('id', $package->id)
                        ->update([
                            'visible' => $status
                        ]);

        return $result ? result() : error('修改状态失败');
    }

    /**
     * 删除
     *
     * @param  \App\Models\Packages  $packages
     * @param  string  $name  名称
     * @return \Illuminate\Http\Response
     */
    public function destroy(Packages $packages, string $name)
    {
        $package =  $packages->package('alias', $name)
                        ->first();

        if(!$package) {
            return error();
        }

        $this->cache_clear($package->alias);

        $result =   $packages->where('id', $package->id)
                        ->delete();

        if($result && config('cdn.delete_files', false)) {
            Storage::deleteDirectory(config('cdn.storage.path') . $package->alias);
        }

        return $result ? result() : error('删除失败');
    }
}
