@extends('index.layouts')

@section('header')
    <h2 class="subtitle">{{ option('site-describe') }}</h2>

    <div class="columns">
        <div class="column is-6 is-offset-3 is-12-mobile">
            <div class="control has-icons-left">
                <input id="search" class="input is-shadowless" style="border: none;" placeholder="请输入你要搜索的Package" autocomplete="off" />
                <span class="icon is-left">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </div>
@endsection

@section('main')
    @if (count($packages))

        <div id="card-list"></div>

        <script>
            const data = @json($packages);
            const isStar = {{ $isStar }};

            const notHtml = `<div class="message is-danger">
                                <div class="message-body">找不到您想要的</div>
                            </div>`;

            generateList();

            $('#search').on('input propertychange', () => generateList($('#search').val()));

            function generateList(name = null) {
                name = $.trim(name).toLocaleLowerCase();
                var html = '';

                if(!name) {
                    if(isStar) {
                        for(let i in data) {
                            if(!data[i].star) continue;
                            html += generateHtml(data[i]);
                        }
                    } else {
                        for(let i = 0; i < 10; i++) {
                            if(Object.keys(data).indexOf(i + '') == -1) break;
                            html += generateHtml(data[i]);
                        }
                    }
                } else {
                    for(let i in data) {
                        if(data[i].name.toLocaleLowerCase().indexOf(name) === -1) continue;
                        html += generateHtml(data[i]);
                    }
                }

                $('#card-list').html(html || notHtml);
            }

            function generateHtml(item) {
                return  `<div class="card mb3">
                            <header class="card-header">
                                <p class="card-header-title">
                                    <a class="has-text-dark" href="/${item.name}/">${item.name}</a>
                                </p>
                            </header>
                            ${item.description ? `
                                <div class="card-content">
                                    <div class="content">${item.description}</div>
                                </div>
                            ` : ``}
                        </div>`;
            }
        </script>
    @else

        <div class="message is-danger">
            <div class="message-body">
                暂无数据，前往<a href="{{ route('console') }}">后台</a>添加
            </div>
        </div>
    @endif
@endsection
