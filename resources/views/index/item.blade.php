@extends('index.layouts')

@section('css')
    <style>
        .table .tag {
            cursor: pointer;
        }
    </style>
@endsection

@section('title', $alias)

@section('header')
    <h1 class="title">{{ $alias }}</h1>

    @isset($description)
        <h2 class="subtitle">{{ $description }}</h2>
    @endisset

    @if (isset($github) || isset($homepage))
        <div class="buttons is-centered">
            @isset($homepage)
                <a class="button is-{{ $_color }} is-inverted is-outlined is-capitalized" href="{{ $homepage }}" target="_blank">
                    <span class="icon">
                        <i class="fas fa-home"></i>
                    </span>
                    <span>官网</span>
                </a>
            @endisset

            @isset($github)
                <a class="button is-{{ $_color }} is-inverted is-outlined is-capitalized" href="{{ $github }}" target="_blank">
                    <span class="icon">
                        <i class="fab fa-github"></i>
                    </span>
                    <span>GitHub</span>
                </a>
            @endisset
        </div>
    @endif
@endsection

@section('main')
    <div class="field" style="margin-bottom: 2rem;">
        <div class="control">
            <div class="select is-fullwidth is-{{ $_color }} version">
                <select id="version" @if (count($version) == 1)disabled @endif>
                    @foreach ($version as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <table class="table is-fullwidth" style="table-layout: fixed;">
        <tbody></tbody>
    </table>

    <script>
        var currentVersion;
        const versionSelect = $('.version');
        const urlPrefix = `https://{{ config('cdn.storage.domain') }}{{ config('cdn.storage.path') }}{{ $alias }}`;

        $(document).ready($ => {
            getVersionData();
            $('#version').on('change', () => getVersionData());
        });

        new ClipboardJS('.tag[data-copy]', {
            text (trigger) {
                const url = $(trigger).parents('tr').find('td').eq(0).text();
                if($(trigger).attr('data-copy') == 'tag') {
                    if(/\.css$/.test(url)) return `<link rel="stylesheet" href="${url}" />`;
                    if(/\.js$/.test(url)) return `<script src="${url}" /><\/script>`;
                }
                return url;
            }
        });
        
        function getVersionData() {
            const version = currentVersion = $('#version').val();

            if(!versionSelect.hasClass('is-loading')) {
                versionSelect.addClass('is-loading');
            }

            $.ajax({
                url: `{{ route('version', [$alias, '']) }}/${version}/`,
                dataType: 'json',
                success (response) {
                    if(version !== currentVersion) return false;
                    versionSelect.removeClass('is-loading');
                    if(response['code'] !== 1) return layer.msg(response['message'], {icon: 5});
                    generateTable(response['data'], version);
                },
                error (response) {
                    if(response['responseJSON'] && response['responseJSON']['message']) {
                        return layer.msg(response['responseJSON']['message'], {icon: 5});
                    }
                    return layer.msg(`[${response.status}]${response.statusText}`, {icon: 5});
                }
            });
        }

        function generateTable(data, version) {
            const html = data.map(url => {
                return  `<tr>
                            <td class="break-word">${urlPrefix}/${version}/${url}</td>
                            <td class="is-hidden-mobile" width="118">
                                <div class="tags">
                                    <div class="tag" data-copy="url">链接</div>
                                    <div class="tag ${!/\.(css|js)$/.test(url) ? 'is-hidden' : ''}" data-copy="tag">标签</div>
                                </div>
                            </td>
                        </tr>`;
            });

            $('.table tbody').html(html);
        }
    </script>
@endsection
