@extends('admin.layouts')

@section('main')
    <h1 class="title">{{ $name }}</h1>

    <table class="table is-fullwidth">
        <thead>
            <tr>
                <td width="140"></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>名称</td>
                <td>{{ $name }}</td>
            </tr>
            <tr>
                <td>别名</td>
                <td>{{ $alias }}</td>
            </tr>
            <tr>
                <td>描述</td>
                <td>{{ $description }}</td>
            </tr>
            <tr>
                <td>GitHub</td>
                <td>{{ $github }}</td>
            </tr>
            <tr>
                <td>Home</td>
                <td>{{ $homepage }}</td>
            </tr>
            <tr>
                <td>版本列表</td>
                <td>
                    <span class="tags">
                        @foreach ($version as $item)
                            <span class="tag">{{ $item }}</span>
                        @endforeach
                    </span>
                </td>
            </tr>
            <tr>
                <td>最低版本</td>
                <td>{{ $minversion }}</td>
            </tr>
            <tr>
                <td>创建时间</td>
                <td>{{ $created_at }}</td>
            </tr>
        </tbody>
    </table>
@endsection
