@extends('admin.layouts')

@section('main')
    <h1 class="title">{{ $name }}</h1>

    <form>
        <div class="field">
            <label class="label">名称</label>
            <p class="control">
                <input id="name" class="input" name="name" type="text" value="{{ $name }}" disabled />
            </p>
        </div>
    
        <div class="field">
            <label class="label">别名</label>
            <p class="control">
                <input id="alias" class="input" name="alias" type="text" value="@if($type === 'edit'){{ $data['alias'] }}@endif" />
            </p>
        </div>
    
        <div class="field">
            <label class="label">描述</label>
            <p class="control">
                <textarea id="description" name="description" class="textarea">@if($type === 'edit'){{ $data['description'] }}@endif</textarea>
            </p>
        </div>
    
        <div class="field">
            <label class="label">官网</label>
            <p class="control">
                <input id="homepage" class="input" name="homepage" type="url" value="@if($type === 'edit'){{ $data['homepage'] }}@endif" />
            </p>
        </div>
    
        <div class="field">
            <label class="label">GitHub</label>
            <p class="control">
                <input id="github" class="input" name="github" type="url" value="@if($type === 'edit'){{ $data['github'] }}@endif" />
            </p>
        </div>
    
        <div class="field">
            <label class="label">最低版本</label>
            <p class="control">
                <div class="select">
                    <select id="minversion" name="minversion" value="@if($type === 'edit'){{ $data['minversion'] }}@endif">
                    </select>
                </div>
            </p>
        </div>

        <div class="field">
            <div class="control">
                <button id="submit" class="button" type="button">修改</button>
            </div>
        </div>
    </form>

    <script>
        const type = '{{ $type }}';
        const githubPrefixList = [
            'git://git@github.com/',
            'git://github.com/',
            'git://github.com:',
            'git://github.',
            'git+ssh://git@github.com',
            'git+https://',
            'git@github.com:',
            'github.com',
            'http://'
        ];

        const packageAdd = "{{ route('packages.search') }}";
        const packageList = "{{ route('packages.list') }}";

        $.getJSON(`https://api.cdnjs.com/libraries/{{ $name }}`, (response, status) => {
            if(type !== 'edit') {
                if(!Object.keys(response).length) window.location.href = packageAdd;

                if(response['description']) $('textarea#description').val(response['description']);
                if(response['homepage']) $('input#homepage').val(response['homepage']);
                if(response['repository']['url']) {
                    var github = response['repository']['url'];
                    for(let i in githubPrefixList) {
                        if(github.indexOf(githubPrefixList[i]) === 0) {
                            github = github.replace(githubPrefixList[i], 'https://');
                            break;
                        }
                    }
                    const gitIndex = github.lastIndexOf('.git');
                    if(gitIndex !== -1) {
                        github = github.substr(0, gitIndex);
                    }
                    $('input#github').val(github);
                }
            }
            if(response['assets']) {
                const minversion = $('select#minversion').attr('value');
                const minversionHtml = response['assets'].map(item => `<option value="${item.version}" ${item.version === minversion ? 'selected' : ''}>${item.version}</option>`);
                $('select#minversion').html('<option value="">选择最低版本</option>' + minversionHtml);
            }
        });

        $("#submit").on('click', () => {
            var data = {};
            $("form").serializeArray().map(item => data[item.name] = item.value);

            $.ajax({
                url: '',
                type: type === 'edit' ? 'PUT' : 'POST',
                data,
                success (response, status) {
                    if(response['code'] !== 1) return layer.msg(response['message'], {icon: 5});
                    if(type === 'edit') {
                        layer.open({
                            content: '修改成功',
                            btn: ['返回列表'],
                            icon: 1,
                            yes () {
                                window.location.href = packageList;
                            },
                            cancel () {
                                window.location.href = packageList;
                            }
                        });

                        setTimeout(() => window.location.href = packageList, 3000);
                    } else {
                        layer.open({
                            content: '添加成功',
                            btn: ['继续添加','返回列表'],
                            icon: 1,
                            yes () {
                                window.location.href = packageAdd;
                            },
                            btn2 () {
                                window.location.href = packageList;
                            },
                            cancel () {
                                window.location.href = packageList;
                            }
                        });
                    }
                }
            })
        });
    </script>
@endsection
