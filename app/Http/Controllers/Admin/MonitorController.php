<?php

namespace App\Http\Controllers\Admin;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Cdn\V20180606\CdnClient;
use TencentCloud\Cdn\V20180606\Models\DescribeCdnDataRequest;
use TencentCloud\Cdn\V20180606\Models\DescribeIpVisitRequest;
use App\Http\Controllers\Controller;

class MonitorController extends Controller
{
    /**
     * Cred
     *
     * @return \Credential
     */
    protected $cred;

    /**
     * Client
     *
     * @return \CdnClient
     */
    protected $client;

    /**
     * 获取数据
     *
     * @param  string  $name  名称
     * @param  string  $startTime  开始时间
     * @param  string  $name  时间粒度
     * @return array|bool
     */
    public function getData($name, $startTime = '-1 day', $interval = '5min')
    {
        try {
            $params = [
                'StartTime' => date("Y-m-d H:i:s", strtotime($startTime)),
                'EndTime'   => date("Y-m-d H:i:s"),
                'Interval'  => $interval,
                'Domains'   => [
                    config('cdn.storage.domain')
                ]
            ];

            if($name == 'visit') {
                $req = new DescribeIpVisitRequest();
    
                $req->fromJsonString(json_encode($params));
    
                $resp = $this->client->DescribeIpVisit($req);
            } else {
                $req = new DescribeCdnDataRequest();

                $params['Metric'] = $name;
    
                $req->fromJsonString(json_encode($params));
    
                $resp = $this->client->DescribeCdnData($req);
            }

            return json_decode($resp->toJsonString(), true)['Data'][0]['CdnData'];
        } catch (TencentCloudSDKException $e) {
            logger($e);
        }
        return false;
    }

    /**
     * 过滤
     *
     * @param  string  $name  名称
     * @param  array  $data  数据
     * @param  array  $list  详细列表
     * @return array
     */
    protected function filter(string $name, array $data, array $config)
    {
        $filter = $config['filter'] ?? '';

        $config['lable'] = $name;

        switch ($filter) {
            case 1:
                    $data = $data[0]['DetailData'];
    
                    $avgValue = round(collect($data)->avg('Value'), 2);
    
                    $unitArr  = num2storage($avgValue);
    
                    $except = pow(1024, $unitArr[2]);
    
                    $config['unit'] = $unitArr[1];
    
                    $config['data'] = collect($data)->map(function($item) use($except) {
                        $value = $item['Value'] ? number_format($item['Value'] / $except, 2) : 0;
    
                        return [
                            'time'  => $item['Time'],
                            'value' => $value == 0 ? 0 : $value
                        ];
                    })->toArray();
                    break;
            
            case 2:
                $data = $data[0]['DetailData'];

                $avgValue = round(collect($data)->avg('Value'), 2);

                $unitArr  = num2mbps($avgValue);

                $except = pow(1000, $unitArr[2]);

                $config['unit'] = $unitArr[1];

                $config['data'] = collect($data)->map(function($item) use($except) {
                    $value = $item['Value'] ? number_format($item['Value'] / $except, 2) : 0;

                    return [
                        'time'  => $item['Time'],
                        'value' => $value == 0 ? 0 : $value
                    ];
                })->toArray();
                break;
            
            case 3:
                $config['data'] = [];

                collect($data)->map(function($item) use(&$config) {
                    foreach($item['DetailData'] as $statusItem) {
                        $config['data'][] = [
                            'name'  => $item['Metric'],
                            'time'  => $statusItem['Time'],
                            'value' => $statusItem['Value']
                        ];
                    }
                });
                break;

            default:
                foreach($data[0]['DetailData'] as $item) {
                    $config['data'][] = [
                        'time'  => $item['Time'],
                        'value' => (float) number_format($item['Value'], 2)
                    ];
                }
                break;
        }

        return $config;
    }

    public function __construct()
    {
        $this->cred = new Credential(config('cdn.storage.secret_id'), config('cdn.storage.secret_key'));
        $this->client = new CdnClient($this->cred, config('cdn.storage.region'));
    }

    /**
     * 监控
     *
     * @param  string  $name  名称
     * @return \Illuminate\Http\Response
     */
	public function index(string $name)
	{
        $monitorList = config('cdn.monitor');

        if(!isset($monitorList[$name])) {
            return error();
        }

        $data = $this->getData($name);

        if(is_array($data)) {
            $result = $this->filter($name, $data, $monitorList[$name]);

            return result($result);
        }

        logger($data);
        return error();
    }
}
