<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /**
     * 重置密码
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->isMethod('get')) {
            return view('admin/personal/password');
        }

        $user = auth()->user();

        $input = request()->all();

        $v = validator($input, [
            'oldPassword' => 'required|min:8|max:32',
            'newPassword' => 'required|min:8|max:32'
        ], [], [
            'oldPassword' => '旧密码',
            'newPassword' => '新密码'
        ]);

        $v->after(function($v) use($user, $input) {
            if(!Hash::check($input['oldPassword'], $user->password)) {
                $v->errors()->add('oldpassword', '旧密码错误');
            }
        });

        if($v->fails()) {
            return error($v->errors()->first());
        }

        $user->password = bcrypt($input['newPassword']);
        $user->save();
        
        return result();
    }
}
