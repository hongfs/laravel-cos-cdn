@extends('admin.layouts')

@section('main')
    <div class="columns top-bar">
        <div class="column">
            <h1 class="title">日志 {{ request('package', '') }}</h1>
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
                    <th width="auto">Package</th>
                    <th>版本</th>
                    <th>文件数</th>
                    <th>运行时间</th>
                    <th>状态</th>
                    <th width="174">创建时间</th>
                    <th width="174">最后更新时间</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item['package']['alias'] }}</td>
                        <td>{{ $item['version'] }}</td>
                        <td>{{ $item['total_number'] }}</td>
                        <td>{{ $item['running_time'] }}s</td>
                        <td>{{ $item['status'] ? '成功' : '失败' }}</td>
                        <td>{{ $item['created_at'] }}</td>
                        <td>{{ $item['updated_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $list->appends(request()->query())->links('vendor.pagination.bulma') }}

    @else

        <article class="message is-info">
            <div class="message-body">
                @if (request()->has('package'))
                    找不到与{{ request('package') }} 相关的日志记录
                @elseif (request()->has('query'))
                    找不到与{{ request('query') }} 相关的日志记录
                @else
                    暂无日志记录
                @endif
            </div>
        </article>
    @endif
@endsection
