@extends('admin.layouts')

@section('main')

    <div class="columns top-bar">
        <div class="column">
            <h1 class="title">列表</h1>
        </div>
        <div class="column">
            <div class="field has-addons is-pulled-right search">
                <div class="control is-expanded">
                    <input id="search" class="input" placeholder="请输入要搜索的内容" value="{{ request('query', '') }}" />
                </div>
                <div class="control">
                    <a class="button" href="javascript:;" type="button">搜索</a>
                </div>
            </div>
        </div>
    </div>

    @if (count($list))
        <table class="table is-fullwidth">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="auto">名称</th>
                    <th>别名</th>
                    <th>描述</th>
                    <th width="200">最低版本</th>
                    <th width="62">星标</th>
                    <th width="62">其他</th>
                    <th width="300">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $key => $item)
                    <tr data-name="{{ $item['name'] }}" data-star="{{ $item['star'] }}" data-visible="{{ $item['visible'] }}">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['alias'] }}</td>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['minversion'] }}</td>
                        <td class="center">
                            <span class="icon is-small" data-type="star">
                                <i class="{{ $item['star'] ? 'fas' : 'far' }} fa-star"></i>
                            </span>
                        </td>
                        <td class="center">
                            @isset($item['homepage'])
                                <a href="{{ $item['homepage'] }}" target="_blank">
                                    <span class="icon is-small">
                                        <i class="mdi mdi-home mdi-dark"></i>
                                    </span>
                                </a>
                            @endisset
                            @isset($item['github'])
                                <a href="{{ $item['github'] }}" target="_blank">
                                    <span class="icon is-small">
                                        <i class="mdi mdi-github-circle mdi-dark"></i>
                                    </span>
                                </a>
                            @endisset
                        </td>
                        <td>
                            <div class="buttons has-addons is-centered">
                                <a class="button is-small" href="javascript:;" data-type="visible">{{ $item['visible'] ? '隐藏' : '显示' }}</a>
                                <a class="button is-small" href="{{ route('packages.edit', $item['name']) }}">修改</a>
                                <a class="button is-small" href="javascript:;" data-type="delete">删除</a>
                                <a class="button is-small" href="{{ route('packages.log', ['package' => $item['name']]) }}">日志</a>
                                <a class="button is-small" href="{{ route('packages.show', $item['name']) }}/">详细</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $list->appends(request()->query())->links('vendor.pagination.bulma') }}

        <script>
            $('.table .button[data-type="delete"]').on('click', event => {
                const name = $(event.currentTarget).parents('tr').attr('data-name');
                layer.confirm(`确定删除${name}吗？`, {
                    btn: ['删除','取消'],
                    skin: 'confirm-delete'
                }, () => {
                    $.ajax({
                        url: `{{ route('packages.destroy', '') }}/${name}`,
                        type: 'DELETE',
                        success (response, status) {
                            if(response.code === 1) {
                                return window.location.reload();
                            }
                            return layer.msg('删除失败');
                        }
                    });
                });
            });

            $('.table .icon[data-type="star"]').on('click', event => {
                const tr = $(event.currentTarget).parents('tr');
                const name = tr.attr('data-name');
                const star = parseInt(tr.attr('data-star')) ? 0 : 1;
                $.ajax({
                    url: `/admin/packages/${name}/star/${star}`,
                    type: 'PUT',
                    success (response)  {
                        if(response['code'] === 1) {
                            tr.attr('data-star', star);
                            $(event.currentTarget).find('.fa-star').removeClass(!star ? 'fas' : 'far').addClass(star ? 'fas' : 'far');
                        }
                    }
                });
            });

            $('.table .button[data-type="visible"]').on('click', event => {
                const tr = $(event.currentTarget).parents('tr');
                const name = tr.attr('data-name');
                const visible = parseInt(tr.attr('data-visible')) ? 0 : 1;
                $.ajax({
                    url: `/admin/packages/${name}/status/${visible}`,
                    type: 'PUT',
                    success (response)  {
                        if(response['code'] === 1) {
                            tr.attr('data-visible', visible);
                            $(event.currentTarget).text(!visible ? '显示' : '隐藏');
                            layer.msg((visible ? '显示' : '隐藏') + '成功');
                        }
                    }
                });
            });
        </script>
    @else

        <article class="message is-info">
            <div class="message-body">
                @if (request()->has('query'))
                    找不到与{{ request('query') }} 相关的Package
                @else
                    暂无数据，可<a href="{{ route('packages.search') }}">点击跳转</a>进行添加
                @endif
            </div>
        </article>
    @endif
@endsection
