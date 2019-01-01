@extends('admin.layouts')

@section('main')
    <div class="card">
        <div class="card-content">
            <div class="content">
                <div class="control has-icons-left">
                    <input id="search" class="input is-medium" placeholder="请输入要搜索的Package 名称" value="{{ request('query', '') }}" autocomplete="off" />
                    <span class="icon is-left">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div id="list"></div>

    <script>
        const list = @json($list);

        getLibrarieData();
        $('#search').on('input propertychange', () => getLibrarieData());

        function getLibrarieData() {
            const name = $('#search').val();

            const notHtml = `<div class="message is-danger">
                                <div class="message-body">找不到与${name} 相关的Package</div>
                            </div>`;

            if(!name || !$.trim(name)) {
                $('#list').html('');
                return false;
            }

            $.getJSON(`https://api.cdnjs.com/libraries?search=${name}&fields=name,description,version`, (response, status) => {
                var html = '';
                for(let i in response.results) {
                    const item = response.results[i];
                    if(list.indexOf(item.name) !== -1) continue;
                    html += `<div class="card mt3">
                                <header class="card-header">
                                    <p class="card-header-title">
                                        <a href="{{ route('packages.create', '') }}/${item.name}/" class="title has-text-dark">
                                            ${item.name}
                                        </a>
                                    </p>
                                </header>
                                <div class="card-content">${item.description}</div>
                            </div>`;
                }

                $('#list').html(html || notHtml);
            });
        }
    </script>
@endsection
