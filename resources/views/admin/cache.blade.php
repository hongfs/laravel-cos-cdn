@extends('admin.layouts')

@section('main')
    <h1 class="title">缓存</h1>

    <div class="buttons">
        <button class="button" onclick="cache('web')">清空网站缓存</button>
        <button class="button" onclick="cache('all')">清空全部缓存</button>
    </div>
    
    <script>
        function cache(name) {
            $.ajax({
                url: `?name=${name}`,
                type: 'PUT',
                timeout: 0,
                success (response) {
                    if(response.code !== 1) return layer.msg(response['message'], {icon: 5});
                    return layer.msg('成功');
                }
            })
        }
    </script>
@endsection