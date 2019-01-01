<?php

namespace App\Http\Controllers\Admin;

use App\Models\Option;
use App\Http\Controllers\Controller;

class SetupController extends Controller
{
    /**
     * 设置
     *
     * @param  \App\Models\Option  $options
     * @return \Illuminate\Http\Response
     */
    public function index(Option $options)
    {
        if(request()->isMethod('get')) {
            return view('admin/setup');
        }

        $names = [
            'site-name'     => 'required|max:100',
            'site-keyword'  => 'nullable|max:100',
            'site-describe' => 'nullable|max:100',
            'site-analysis' => 'nullable|max:500'
        ];

        $input = request()->all();

        $validator = validator($input, $names);

        if($validator->fails()) {
            return error($validator->errors()->first());
        }
        
        foreach($names as $key => $item) {
            if(isset($input[$key])) {
                $options->_set($key, $input[$key]);
            }
        }

        cache()->forget('option');

        return result();
    }
}
