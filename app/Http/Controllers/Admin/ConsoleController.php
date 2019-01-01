<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\MonitorController as Monitor;

class ConsoleController extends Controller
{
    /**
     * Basic
     *
     * @return array
     */
    protected $basic;

    /**
     * 开始时间
     *
     * @return string
     */
    protected $startTime = '-1 day';

    /**
     * 获取最大带宽
     *
     * @return void
     */
    protected function getMaxBandWidth()
    {
        $name = 'bandwidth';

        if(isset($this->basic[$name])) {
            $value = $this->getData($name);

            [ $this->basic[$name]['value'], $this->basic[$name]['unit'] ] = num2mbps($value);
        }
    }

    /**
     * 获取总流量
     *
     * @return void
     */
    protected function getFlux()
    {
        $name = 'flux';

        if(isset($this->basic[$name])) {
            $value = $this->getData($name);

            [ $this->basic[$name]['value'], $this->basic[$name]['unit'] ] = num2storage($value);
        }
    }

    /**
     * 获取总请求数
     *
     * @return void
     */
    public function getRequest()
    {
        $name = 'request';

        if(isset($this->basic[$name])) {
            $data = $this->getData($name);

            [ $this->basic[$name]['value'], $this->basic[$name]['unit'] ] = num2metric($data);
        }
    }

    /**
     * 获取总命中率
     *
     * @return void
     */
    protected function getFluxHitRate()
    {
        $name = 'fluxHitRate';

        if(isset($this->basic[$name])) {
            $value = $this->getData($name);

            $this->basic[$name]['value'] = _round($value);
            $this->basic[$name]['unit'] = '%';
        }
    }

    /**
     * 获取数据
     *
     * @param  string  $name  名称
     * @return array|bool
     */
    protected function getData($name)
    {
        $result = (new Monitor)->getData($name, $this->startTime, 'day')[0]['DetailData'];

        return end($result)['Value'];
    }

    /**
     * 视图
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/console', [
            'basic'     => config('cdn.basic'),
            'monitor'   => config('cdn.monitor')
        ]);
    }

    /**
     * 基本数据
     *
     * @return \Illuminate\Http\Response
     */
    public function basic()
    {
        $this->basic = config('cdn.basic');
        
        $this->getMaxBandWidth();
        $this->getFlux();
        $this->getRequest();
        $this->getFluxHitRate();
        
        return result($this->basic);
    }
}
