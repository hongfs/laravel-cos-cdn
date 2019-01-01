@extends('admin.layouts')

@section('css')
    <style>
        html {
            background-image: url('https://i.loli.net/2018/10/30/5bd7d099c4b62.jpg');
            background-size: cover;
            background-position: center center;
        }

        html, body, #login {
            height: 100%;
        }

        #login {
            display: flex;
            align-items: center;
        }

        #login .card {
            max-width: 400px;
        }

        #login .field:last-child {
            margin-bottom: 0 !important;
        }
    </style>
@endsection

@section('body')
    <div id="login" class="columns">
        <div class="column is-4 is-offset-4 is-10-mobile is-offset-1-mobile">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">登陆</p>
                </header>
                <div class="card-content">
                    <div class="content">
                        <form>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="username" autocomplete="off" />
                                    <span class="icon is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <div class="control has-icons-left">
                                    <input class="input" type="password" name="password" autocomplete="off" />
                                    <span class="icon is-left">
                                        <i class="fas fa-unlock-alt"></i>
                                    </span>
                                </div>
                            </div>
    
                            <div class="field is-grouped is-grouped-right">
                                <div class="control">
                                    <button class="button" type="submit">登陆</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <footer class="card-footer">
                    <p class="card-footer-item">&copy;{{ date('Y') }} {{ option('site-name') }}</p>
                </footer>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('form').validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 5,
                        maxlength: 32
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        maxlength: 32
                    }
                },
                messages: {
                    username: {
                        required: '账号不能为空',
                        minlength: '账号长度最少5位',
                        maxlength: '账号长度最长32位'
                    },
                    password: {
                        required: '密码不能为空',
                        minlength: '密码长度最少8位',
                        maxlength: '密码长度最长32位'
                    }
                },
                submitHandler: function(form) {
                    $.post("{{ route('auth') }}", $(form).serialize(), function(response, status) {
                        if(response.code !== 1) return layer.msg(response['message'], {icon: 5});
                        layer.msg('登陆成功');
                        window.location.href = "{{ route('console') }}";
                    });
                }
            });
        });
    </script>
@endsection
