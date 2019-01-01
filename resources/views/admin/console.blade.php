@extends('admin.layouts')

@section('main')
	@isset($basic)
		<div class="card is-shadowless basic">
			<header class="card-header">
				<p class="card-header-title">控制台 <small>(24小时)</small></p>
			</header>
			<div class="card-content">
				<div class="columns">
					@foreach ($basic as $key => $item)
						<div class="column has-text-centered skeleton" data-name="{{ $key }}">
							<div>
								<p class="heading">{{ $item['name'] }}</p>
								<p class="title">&nbsp;</p>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>

		<script>
			$(document).ready($ => {
				$.getJSON(`{{ route('console.basic') }}`, (response) => {
					$.map(response.data, (item, key) => {
						$(`.basic .skeleton[data-name="${key}"] .title`).text(`${item.value} ${item.unit || ''}`).parents('.skeleton').removeClass('skeleton');
					});
				});
			});
		</script>
	@endisset

	@isset ($monitor)
		<div id="monitor">
			@foreach ($monitor as $key => $item)
				<div class="card mt3" data-name="{{ $key }}">
					<header class="card-header">
						<p class="card-header-title">{{ $item['name'] }}</p>
					</header>
					<div class="card-content">
						<div id="chart-{{ $key }}"></div>
					</div>
				</div>
			@endforeach
		</div>

		<script src="https://gw.alipayobjects.com/os/antv/pkg/_antv.g2-3.4.7/dist/g2.min.js"></script>

		<script>
			$(document).ready($ => {
				$('#monitor .card').map(function(item) {
					const name = $(this).attr('data-name');
					$.getJSON(`{{ route('console.monitor', '') }}/${name}`, function(response) {
						const data = response.data;
						data['unit'] = data['unit'] || '';
						
						const chart = new G2.Chart({
							container: 'chart-' + data.lable,
							forceFit: true,
							height: 220,
							padding: 'auto'
						});
						chart.clear();
						chart.scale('value', {
							min: 0,
							tickCount: 5,
							type: 'pow',
							alias: data['name']
						});
						chart.scale('time', {
							type: 'time',
							mask: 'HH:mm'
						});
						chart.tooltip({
							crosshairs: {
								type: 'line'
							},
							itemTpl: '<li>{name}: {value}' + data['unit'] + '</li>'
						})
						if(data['filter'] === 3) {
							chart.line().color('name').position('time*value');
						} else {
							chart.line().position('time*value');
						}
						chart.source(data.data);
						chart.axis('value', {
							label: {
								formatter: val => {
									return val + data['unit'];
								}
							}
						});
						chart.render();
					});
				});
			});
		</script>
	@endisset
@endsection
