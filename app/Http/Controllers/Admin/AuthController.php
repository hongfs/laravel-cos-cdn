<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * 视图
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if(auth()->check()) {
            auth()->logout();
        }

        return view('admin/login');
    }

    /**
     * 验证
     *
     * @return \Illuminate\Http\Response
     */
    public function auth()
    {
        if(auth()->check()) {
            auth()->logout();
        }

        $input = request()->only('username', 'password');

        $v = validator($input, [
            'username'  => 'required|min:5|max:32',
            'password'  => 'required|min:8|max:32'
        ], [], [
            'username'  => '账号',
            'password'  => '密码'
        ]);

        if($v->fails()) {
            return error($v->errors()->first());
        }

        if(auth()->attempt($input)) {
            return result();
        }

        return error('账号或密码错误');
    }
}
