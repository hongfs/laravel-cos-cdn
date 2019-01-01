<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta content="telephone=no; email=no, adress=no" name="format-detection" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<title>{{ option('site-name') }}</title>
		<link rel="dns-prefetch" href="https://cdnjs.loli.net" />
		<link rel="dns-prefetch" href="https://use.fontawesome.net" />
		<link rel="dns-prefetch" href="https://gw.alipayobjects.com" />
		<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/bulma/0.7.2/css/bulma.min.css" />
		@isset($_theme)
			<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/bulmaswatch/0.7.2/{{ $_theme }}/bulmaswatch.min.css" />
		@endisset
		<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/basscss/8.0.0/css/basscss.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" />
		<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.loli.net/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
		<script src="https://cdnjs.loli.net/ajax/libs/jquery-validate/1.17.0/localization/messages_zh.min.js"></script>
		<script src="https://cdnjs.loli.net/ajax/libs/layer/2.3/layer.js"></script>
		<style>
			* {
				border-color: #dbdbdb;
			}

			.top-bar .field.search {
				width: 300px;
			}
		</style>
        @section('css')
		@show
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.validator.setDefaults({
                success: function(element) {
                    element.parent().find('.help').remove();
                },
                errorPlacement: function(error, element) {
                    error = error[0].innerText;
                    element = $(element).parent();
                    if(element.find('.help').length) {
                        element.find('p').text(error);
                    } else {
                        element.append(`<p class="help is-danger">${error}</p>`);
                    }
                }
			});
		</script>
	</head>
	<body class="m0">
		@section('body')
			<div class="columns" style="margin-bottom: 0 !important;">
				<div class="column is-2 pb0">
					<div class="section border-right has-background-white-ter" style="height: 100%;">
						<div class="menu">
							@foreach (config('cdn.admin_menu') as $item)
								@if (isset($item['children']) && is_array($item['children']))
									<p class="menu-label is-capitalized">{{ $item['name'] }}</p>
									<ul class="menu-list">
										@foreach ($item['children'] as $childrenItem)
											<li>
												<a href="{{ $childrenItem['to'] ?? 'javascript:;' }}">
													{{ $childrenItem['name'] }}
												</a>
											</li>
										@endforeach
									</ul>
								@endif
							@endforeach
						</div>
					</div>
				</div>
				<div class="column">
					<div class="section">
						@yield('main')
					</div>
				</div>
			</div>

			<script>
				$(document).ready(function() {
					$('.top-bar .search .button').on('click', function() {
						const query = $.trim($('#search').val());
						if(!query) return layer.msg('请输入搜索内容', {icon: 5});
						window.location.href = '?query=' + query;
					});
				});
			</script>
		@show
	</body>
</html>
