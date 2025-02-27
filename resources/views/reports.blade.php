@extends('layouts.app')

@push('page-css')
<!-- Select2 CSS -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Reports</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Generate Reports</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="#generate_report" data-toggle="modal" class="btn btn-primary float-right mt-2">Generate Report</a>
</div>
@endpush


@section('content')

<div class="row">
	@isset($sales)
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon text-primary border-primary">
						<i class="fe fe-money"></i>
					</span>
					<div class="dash-count">
						<h3>{{$total_cash}}</h3>
					</div>
				</div>
				<div class="dash-widget-info">
					<h6 class="text-muted">Total Cash</h6>
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
						<i class="fe fe-activity"></i>
					</span>
					<div class="dash-count">
						<h3>{{$total_sales}}</h3>
					</div>
				</div>
				<div class="dash-widget-info">

					<h6 class="text-muted">Total Sales</h6>
					<div class="progress progress-sm">
						<div class="progress-bar bg-success w-50"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endisset
	<div class="col-md-12">

		@isset($sales)
		<!--  Sales -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="category-table" class="datatable table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>Medicine Name</th>
								<th>Quantity</th>
								<th>Total Price</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($sales as $sale)
							@if (!(empty($sale->product->purchase)))
							<tr>
								<td>{{$sale->product->purchase->name}}</td>
								<td>{{$sale->quantity}}</td>
								<td>{{($sale->total_price)}}</td>
								<td>{{date_format(date_create($sale->created_at),"d M, Y")}}</td>

							</tr>
							@endif
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- / sales -->
		@endisset

		@isset($products)
		<!-- Products -->
		<div class="card">
			<button onclick="printProducts()" class="btn btn-secondary float-right mt-2 mr-2">
				<i class="fa fa-print"></i> Print Report Products
			</button>
			<div class="card-body">
				<div class="table-responsive">
					<table id="category-table" class="datatable table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Product Name</th>
								<th>Category</th>
								<th>Price (KSh)</th>
								<th>Quantity</th>
								<th>Discount (%)</th>
								<th>Expiry Date</th>
							</tr>
						</thead>
						<tbody id="printable-products">
							@foreach ($products as $index => $product)
							@if($product->purchase()->exists())
							<tr>
								<td>{{ $index + 1 }}</td>
								<td>{{$product->purchase->name}}</td>
								<td>{{$product->purchase->category->name}}</td>
								<td>{{$product->price}}</td>
								<td>{{$product->purchase->quantity}}</td>
								<td>{{$product->discount}}%</td>
								<td>{{date_format(date_create($product->purchase->expiry_date),"d M, Y")}}</td>
							</tr>
							@endif
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /Products -->
		@endisset

		@isset($purchases)
		<!-- Purchases-->
		<div class="card">
			<button onclick="printPurchses()" class="btn btn-secondary float-right mt-2 mr-2">
				<i class="fa fa-print"></i> Print All Products
			</button>
			<div class="card-body">
				<div class="table-responsive">
					<table id="category-table" class="datatable table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Medicine Name</th>
								<th>Medicine Category</th>
								<th>Purchase Price</th>
								<th>Quantity</th>
								<th>Supplier</th>
								<th>Expire Date</th>
								<th class="action-btn">Action</th>
							</tr>
						</thead>
						<tbody id="printable-purchases">
							@php $index = 0; @endphp <!-- Initialize index counter -->
							@foreach ($purchases as $purchase)
							@if(!empty($purchase->supplier) && !empty($purchase->category))
							<tr>
								<td>{{ $index + 1 }}</td> <!-- Display the index starting from 1 -->
								<td>
									<h2 class="table-avatar">
										@if(!empty($purchase->image))
										<span class="avatar avatar-sm mr-2">
											<img class="avatar-img" src="{{asset('storage/purchases/'.$purchase->image)}}" alt="product image">
										</span>
										@endif
										{{$purchase->name}}
									</h2>
								</td>
								<td>{{$purchase->category->name}}</td>
								<td>{{$purchase->price}}</td>
								<td>{{$purchase->quantity}}</td>
								<td>{{$purchase->supplier->name}}</td>
								<td>{{date_format(date_create($purchase->expiry_date),"d M, Y")}}</td>
								<td></td>
							</tr>
							@php $index++; @endphp <!-- Increment the index for the next row -->
							@endif
							@endforeach
						</tbody>
					</table>

				</div>
			</div>
		</div>
		<!-- /Purchases -->
		@endisset
	</div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generate_report" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Generate Report</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('reports')}}">
					@csrf
					<div class="row form-row">
						<div class="col-12">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label>From</label>
										<input type="date" name="from_date" class="form-control">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label>To</label>
										<input type="date" name="to_date" class="form-control">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Resource</label>
								<select class="form-control select" name="resource">
									<option value="products">Products</option>
									<option value="purchases">Purchases</option>
									<option value="sales">Sales</option>
								</select>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary btn-block">Save Changes</button>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Generate Modal -->
@endsection


@push('page-js')
<!-- Select2 JS -->
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

<script>
	function printProducts() {
		let printWindow = window.open('', '', 'width=900,height=650');

		let header = `
            <div style="text-align: center; font-family: Arial, sans-serif;">
                <h2 style="margin: 0; font-weight: bold;">Sehha Medical Chemist</h2>
                <p style="margin: 0; font-size: 14px;">Location: Meru, Kenya</p>
                <p style="margin: 0; font-size: 14px;">Tel: +254 702972797 | PIN: P051719234N</p>
                <hr style="border: 1px solid black; margin-top: 10px;">
                <h3 style="margin-top: 10px; text-decoration: underline;">PRODUCTS LIST</h3>
                <p style="margin: 5px 0; font-size: 14px;">
                    Product Printed On Date: ${new Date().getDate()}/${new Date().getMonth() + 1}/${new Date().getFullYear()} 
                    ${new Date().getHours()}:${new Date().getMinutes()}:${new Date().getSeconds()}
                </p>
            </div>
        `;

		let footer = `
            <div style="position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 12px; font-style: italic;">
                <hr style="border: 1px solid black; margin-bottom: 5px;">
                <p>This system is designed by JM Innovatech Solutions - 0791446968</p>
            </div>
        `;

		let approvals = `
            <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                <div>
                    <p><strong>Prepared by:</strong> ________________________</p>
                    <p>Date: ________________________</p>
                </div>
                <div>
                    <p><strong>Approved by:</strong> ________________________</p>
                    <p>Date: ________________________</p>
                </div>
            </div>
        `;

		let productTable = document.getElementById('printable-products').innerHTML;

		printWindow.document.write(`
            <html>
            <head>
                <title>Print Products</title>
                <style>
                    body { font-family: Arial, sans-serif; display: flex; flex-direction: column; min-height: 100vh; margin: 0; padding: 0; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
                    th, td { border: 1px solid black; padding: 10px; text-align: left; }
                    th { background-color: #000; color: white; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f2f2f2; }
                    .content { flex: 1; }
                    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 12px; }
                    img { max-width: 120px; display: block; margin: 0 auto; }
                </style>
            </head>
            <body>
                ${header}
                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price (KSh)</th>
                                <th>Quantity</th>
                                <th>Discount (%)</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${productTable}
                        </tbody>
                    </table>
                    ${approvals}
                </div>
                ${footer}
                <script>
                    window.onafterprint = function () {
                        window.close();
                    };
                <\/script>
            </body>
            </html>
        `);

		printWindow.document.close();
		printWindow.print();
	}
</script>

<script>
	function printPurchses() {
		let printWindow = window.open('', '', 'width=900,height=650');

		let header = `
            <div style="text-align: center; font-family: 'Times New Roman', serif; padding: 20px;">
                <h2 style="margin: 0; font-weight: bold; font-size: 20px;">Sehha Medical Chemist</h2>
                <p style="margin: 5px 0; font-size: 12px;">Location: Meru, Kenya</p>
                <p style="margin: 5px 0; font-size: 12px;">Tel: +254 702972797 | PIN: P051719234N</p>
                <hr style="border: 1px solid black; margin-top: 20px;">
                <h3 style="margin-top: 10px; text-decoration: underline; font-size: 16px;">PURCHASES LIST</h3>
                <p style="margin: 5px 0; font-size: 12px;">
                    Products Printed On Date: ${new Date().getDate()}/${new Date().getMonth() + 1}/${new Date().getFullYear()} 
                    ${new Date().getHours()}:${new Date().getMinutes()}:${new Date().getSeconds()}
                </p>
            </div>
        `;



		let approvals = `
            <div style="display: flex; justify-content: space-between; margin-top: 40px; font-size: 12px;">
                <div>
                    <p><strong>Prepared by:</strong> ________________________</p>
                    <p>Date: ________________________</p>
                </div>
                <div>
                    <p><strong>Approved by:</strong> ________________________</p>
                    <p>Date: ________________________</p>
                </div>
            </div>
        `;

		let purchaseTable = document.getElementById('printable-purchases').innerHTML;

		printWindow.document.write(`
            <html>
            <head>
                <title>Print Purchases</title>
                <style>
                    body { font-family: 'Times New Roman', serif; margin: 0; padding: 0; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 10px; } /* All cells will have the same smaller font size */
                    th { background-color: #000; color: white; font-weight: bold;}
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .content { flex: 1;  }
                    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; page-break-before: always; }
                    @media print {
                        /* Ensures the footer is placed on the last page */
                        .footer {
                            page-break-before: always;
                        }
                        .footer:last-child {
                            page-break-before: auto;
                        }
                    }
                </style>
            </head>
            <body>
                ${header}
                <div class="content">
                    <table>
                        <thead>
                            <tr>
							    <th>#</th>
                                <th>Medicine Name</th>
                                <th>Medicine Category</th>
                                <th>Purchase Price</th>
                                <th>Quantity</th>
                                <th>Supplier</th>
                                <th>Expire Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${purchaseTable}
                        </tbody>
                    </table>
                    ${approvals}
                </div>
               
                <script>
                    window.onafterprint = function () {
                        window.close();
                    };
                <\/script>
            </body>
            </html>
        `);

		printWindow.document.close();
		printWindow.print();
	}
</script>








@endpush