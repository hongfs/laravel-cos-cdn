<?php

use Illuminate\Support\Facades\Redis;

if(!function_exists('result')) {
    /**
     * 返回数据
     *
     * @param string|array  $data  数据
     * @param int  $status  状态码
     * @return \Illuminate\Http\Response
     */
    function result($data = null, int $status = 200) {
        return response()->json(
            is_null($data) ? ['code' => 1] : ['code' => 1, 'data' => $data],
            $status
        );
    }
}

if(!function_exists('error')) {
    /**
     * 返回错误
     *
     * @param string  $msg  错误信息
     * @param int  $status  状态码
     * @return \Illuminate\Http\Response
     */
    function error($msg = '参数错误', int $status = 200) {
        return response()->json(
            ['code' => 0, 'message' => $msg],
            $status
        );
    }
}

if(!function_exists('opiton')) {
    /**
     * 获取设置
     *
     * @param string  $key  键值
     * @param string  $default  默认值
     * @return string|null
     */
    function option(string $key, $default = NULL) {
        $options = cache()->rememberForever('option', function() {
            return  \App\Models\Option::pluck('value', 'name')
                        ->toArray();
        });

        return $options[$key] ?? $default;
    }
}

// if(!function_exists('toStorageUnit')) {
//     /**
//      * 转存储单位
//      *
//      * @param int  $size  大小
//      * @return array
//      */
//     function toStorageUnit(int $size) {
//         $unitList = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
//         $i = 0;
    
//         while($size >= 1024) {
//             $size /= 1024;
//             $i++;
//         }
        
//         return [
//             round($size, 2),
//             $unitList[$i],
//             $i
//         ];
//     }
// }

if(!function_exists('num2metric')) {
    /**
     * 转度量单位
     *
     * @param int $num 数字
     * @return array
     */
    function num2metric(int $num) {
        $unitList = [
            'P' => 15,
            'T' => 12,
            'G' => 9,
            'M' => 6,
            'W' => 4,
            'K' => 3
        ];
    
        foreach($unitList as $name => $pow) {
            $size = pow(10, $pow);
            if($num >= $size) {
                return [
                    _round($num / $size),
                    $name
                ];
            }
        }
    
        return [
            $num,
            '',
            0
        ];
    }
}

if(!function_exists('num2storage')) {
    /**
     * 数字转存储
     *
     * @param int $num 数字
     * @param string $unit 默认单位
     * @return string|int
     */
    function num2storage(int $num, string $unit = 'Bytes') {
        $unitList = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    
        $i = array_search($unit, $unitList);
    
        while($num >= 1024) {
            $num /= 1024;
            $i++;
        }
    
        return [
            _round($num),
            $unitList[$i],
            $i
        ];
    }
}

if(!function_exists('num2mbps')) {
    /**
     * 转带宽单位
     *
     * @param int $num 数字
     * @param string $unit 默认单位
     * @return array
     */
    function num2mbps(int $num, string $unit = 'bps') {
        $unitList = ['bps', 'Kbps', 'Mbps'];
        $i = array_search($unit, $unitList);
    
        while($num >= 1000) {
            $num /= 1000;
            $i++;
        }
    
        return [
            _round($num),
            $unitList[$i],
            $i
        ];
    }
}

if(!function_exists('getVersionList')) {
    /**
     * 获取版本列表
     *
     * @param int $num 数字
     * @param string $minVersion 默认单位
     * @return array
     */
    function getVersionList($name, $minVersion = NULL) {
        $list = Redis::hkeys('libraries:' . $name);
        if(!$list) return [];

        $list = collect($list);

        if($minVersion) {
            $list = $list->filter(function($version) use($minVersion) {
                return version_compare($minVersion, $version) != 1;
            });
        }

        $list = $list->sort(function($version1, $version2) {
            return version_compare($version1, $version2) != 1;
        });

        return $list ? $list->toArray() : [];
    }
}

if(!function_exists('_round')) {
    /**
     * Redis 清除
     *
     * @param int|float $val 值
     * @param int $precision 小数点位数
     * @return bool
     */
    function _round($val, $precision = 2) {
        return sprintf('%.' . $precision . 'f', $val);
    }
}

if(!function_exists('redis_clear')) {
    /**
     * Redis 清除
     *
     * @param string $prefix 前缀
     * @return bool
     */
    function redis_clear($prefix) {
        $keys = Redis::keys($prefix . ':*');
        foreach($keys as $key) {
            Redis::del($key);
        }
        return true;
    }
}
