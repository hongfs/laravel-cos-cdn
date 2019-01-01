@extends('admin.layouts')

@section('main')
    <h1 class="title">修改密码</h1>

    <form id="reset">
        <div class="field">
            <label class="label">旧密码</label>
            <p class="control">
                <input class="input" name="oldPassword" id="oldPassword" type="password" />
            </p>
        </div>
        <div class="field">
            <label class="label">新密码</label>
            <p class="control">
                <input class="input" name="newPassword" id="newPassword" type="password" />
            </p>
        </div>
        <div class="field">
            <label class="label">确认密码</label>
            <p class="control">
                <input class="input" name="confirmPassword" id="confirmPassword" type="password" />
            </p>
        </div>
        <div class="field">
            <div class="control">
                <button class="button" type="submit">修改</button>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $('form').validate({
                rules: {
                    oldPassword: {
                        required: true,
                        minlength: 5,
                        maxlength: 32
                    },
                    newPassword: {
                        required: true,
                        minlength: 8,
                        maxlength: 32
                    },
                    confirmPassword: {
                        required: true,
                        minlength: 8,
                        maxlength: 32,
                        equalTo: "#newPassword"
                    }
                },
                messages: {
                    oldPassword: {
                        required: '旧密码不能为空',
                        minlength: '旧密码长度最少8位',
                        maxlength: '旧密码长度最长32位'
                    },
                    newPassword: {
                        required: '新密码不能为空',
                        minlength: '新密码长度最少8位',
                        maxlength: '新密码长度最长32位'
                    },
                    confirmPassword: {
                        required: '确认密码不能为空',
                        minlength: '确认密码长度最少8位',
                        maxlength: '确认密码长度最长32位',
                        equalTo: '新密码与确认密码不一致'
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        type: 'PUT',
                        data: $(form).serialize(),
                        success (response, status) {
                            if(response.code !== 1) return layer.msg(response['message'], {icon: 5});
                            return layer.msg('修改成功');
                        }
                    });
                }
            });
        });
    </script>
@endsection