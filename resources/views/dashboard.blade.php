@extends('layouts.app')

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-12 d-flex justify-content-between align-items-center">
	<h3 class="page-title">Welcome {{auth()->user()->name}} To <Strong>Sehha Medical Chemist</Strong></h3>
	<div id="current-time" class="text-right">
		<!-- Time will appear here -->
	</div>
</div>
@endpush

@section('content')

<div class="row">
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon text-primary border-primary">
						<i class="fe fe-money"></i>
					</span>
					<div class="dash-count">
						<h3>KES {{$today_sales}}</h3>
					</div>
				</div>
				<div class="dash-widget-info">
					<h6 class="text-muted">Today Sales Cash</h6>
					<div class="progress progress-sm">
						<div class="progress-bar bg-primary w-50"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon text-success">
						<i class="fe fe-credit-card"></i>
					</span>
					<div class="dash-count">
						<h3>{{$total_categories}} Products</h3>
					</div>
				</div>
				<div class="dash-widget-info">

					<h6 class="text-muted">Product Categories</h6>
					<div class="progress progress-sm">
						<div class="progress-bar bg-success w-50"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon text-danger border-danger">
						<i class="fe fe-folder"></i>
					</span>
					<div class="dash-count">
						<h3>{{$total_expired_products}} Expired</h3>
					</div>
				</div>
				<div class="dash-widget-info">

					<h6 class="text-muted">Expired Products</h6>
					<div class="progress progress-sm">
						<div class="progress-bar bg-danger w-50"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon text-warning border-warning">
						<i class="fe fe-users"></i>
					</span>
					<div class="dash-count">
						<h3>{{\DB::table('users')->count()}} Users</h3>
					</div>
				</div>
				<div class="dash-widget-info">

					<h6 class="text-muted">System Users</h6>
					<div class="progress progress-sm">
						<div class="progress-bar bg-warning w-50"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-lg-6">

		<div class="card card-table">
			<div class="card-header">
				<h4 class="card-title ">Total Sales Done On {{ \Carbon\Carbon::now()->format('d M,Y  ') }}</h4>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-center mb-0">
						<thead>
							<tr>
								<th>Medicine</th>
								<th>Quantity</th>
								<th>Total Price</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($latest_sales as $sale)
							@if(!empty($sale->product->purchase))
							<tr>
								<td>{{$sale->product->purchase->name}}</td>
								<td>{{$sale->quantity}}</td>
								<td>
									{{($sale->total_price)}} Kshs
								</td>
								<td>{{date_format(date_create($sale->created_at),"d M, Y h:i:s a")}}</td>

							</tr>
							@endif
							@endforeach

						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>

	<div class="col-md-12 col-lg-6">

		<!-- Pie Chart -->
		<div class="card card-chart">
			<div class="card-header">
				<h4 class="card-title">Resources Sum</h4>
			</div>
			<div class="card-body">
				<canvas id="resourceChart"></canvas>
			</div>
		</div>
		<!-- /Pie Chart -->

	</div>


</div>
<div class="row">
	<div class="col-md-12">

		<!-- Latest Customers -->

		<!-- /Latest Customers -->

	</div>
</div>
@endsection

@push('page-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		let chartData = @json($chartData);

		let ctx = document.getElementById('resourceChart').getContext('2d');
		new Chart(ctx, {
			type: 'bar', // You can change this to 'pie', 'line', etc.
			data: {
				labels: chartData.labels,
				datasets: [{
					label: 'Resources Summary',
					data: chartData.values,
					backgroundColor: ['#FF6384', '#36A2EB', '#7bb13c'],
					borderWidth: 1
				}]
			},
			options: {
				responsive: true
			}
		});
	});

	function updateTime() {
		const now = new Date();
		let hours = now.getHours();
		const minutes = now.getMinutes().toString().padStart(2, '0');
		const seconds = now.getSeconds().toString().padStart(2, '0');

		// Determine AM or PM
		const amPm = hours >= 12 ? 'PM' : 'AM';

		// Convert to 12-hour format
		hours = hours % 12;
		hours = hours ? hours : 12; // '0' becomes '12'

		// Format time as HH:MM:SS AM/PM
		const timeString = `${hours}:${minutes}:${seconds} ${amPm}`;

		// Update the time in the DOM
		document.getElementById("current-time").textContent = timeString;
	}

	// Update time every second
	setInterval(updateTime, 1000);

	// Call updateTime initially to set the time immediately
	updateTime();
</script>

@endpush