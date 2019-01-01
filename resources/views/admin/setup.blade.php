@extends('admin.layouts')

@section('main')
    <h1 class="title">基本</h1>

    <form>
        <div class="field">
            <label class="label">站点名称</label>
            <p class="control">
                <input class="input" name="site-name" type="text" value="{{ option('site-name') }}" />
            </p>
        </div>

        <div class="field">
            <label class="label">站点关键词</label>
            <p class="control">
                <input class="input" name="site-keyword" type="text" value="{{ option('site-keyword') }}" />
            </p>
        </div>

        <div class="field">
            <label class="label">站点描述</label>
            <p class="control">
                <textarea class="textarea" name="site-describe" placeholder="多个关键词请用英文逗号隔开">{{ option('site-describe') }}</textarea>
            </p>
        </div>

        <div class="field">
            <label class="label">站点统计</label>
            <p class="control">
                <textarea class="textarea" name="site-analysis" placeholder="统计代码请带上标签(<script></script>)">{{ option('site-analysis') }}</textarea>
            </p>
        </div>

        <div class="field">
            <div class="control">
                <button class="button" type="submit">修改</button>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(() => {
            $("form").on('click', () => {
                var data = {};
                $("form").serializeArray().map(item => data[item.name] = item.value);

                $.ajax({
                    url: '',
                    type: 'PUT',
                    data: data,
                    success (response, status) {
                        if(status === 'success') {
                            if(response['code'] !== 1) return layer.msg(response['message'], {icon: 5});
                            layer.msg('保存成功');
                        }
                    }
                });
            });
        });
    </script>
@endsection