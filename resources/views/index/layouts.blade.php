<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta content="telephone=no; email=no, adress=no" name="format-detection" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
        <meta name="keywords" content="{{ option('site-keyword') }}" />
		<meta name="description" content="{{ option('site-describe') }}" />
		<title>
			@hasSection ('title')
				@yield('title') - 
			@endif
			{{ option('site-name') }}
		</title>
		<link rel="dns-prefetch" href="https://cdnjs.loli.net" />
		<link rel="dns-prefetch" href="https://use.fontawesome.net" />
		<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/bulma/0.7.2/css/bulma.min.css" />
		@isset($_theme)
			<link rel="stylesheet" href="/static/bulmaswatch/0.7.2/{{ $_theme }}/bulmaswatch.min.css" />
		@endisset
		<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/basscss/8.0.0/css/basscss.min.css" />
		<link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/font-awesome/5.6.3/css/all.min.css" />
		<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://cdnjs.loli.net/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>
		<script src="https://cdnjs.loli.net/ajax/libs/layer/2.3/layer.js"></script>
        @section('css')
		@show

		{!! option('site-analysis') !!}

		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
		</script>
	</head>
	<body style="display: flex; flex-direction: column;">
		<section class="hero is-{{ $_color }}">
			<div class="hero-head">
				<nav class="main-nav navbar has-background-{{ $_color }}">
					<div class="container">
						<div class="navbar-brand">
							<a class="navbar-item" href="{{ route('home') }}">{{ option('site-name') }}</a>
						</div>
					</div>
				</nav>
			</div>
			@hasSection ('header')
				<div class="hero-body">
					<div class="container has-text-centered">
						@yield('header')
					</div>
				</div>
			@endif
		</section>

		<main style="flex: 1;">
			<div class="container">
				<div class="section">
					@yield('main')
				</div>
			</div>
		</main>
		<footer class="footer has-text-centered">
			<div class="container">
				<p class="is-size-7">
					&copy; {{ date('Y') }} {{ option('site-name') }} By <a href="https://github.com/hongfs/laravel-cdn" target="_blank">laravel-cdn</a>
				</p>
			</div>
		</footer>
	</body>
</html>
