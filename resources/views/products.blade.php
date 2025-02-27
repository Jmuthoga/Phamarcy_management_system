@extends('layouts.app')

@push('page-css')
<!-- Select2 CSS -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Products</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Products</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="{{route('add-product')}}" class="btn btn-primary float-right mt-2">Add New</a>
	<button onclick="printProducts()" class="btn btn-secondary float-right mt-2 mr-2">
		<i class="fa fa-print"></i> Print All Products
	</button>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<!-- Products -->
		<div class="card">
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
	</div>
</div>

<!-- Delete Modal -->
<x-modals.delete :route="'products'" :title="'Product'" />
<!-- /Delete Modal -->
@endsection

@push('page-js')
<!-- Select2 JS -->
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

<!-- Print Function (Improved KEMSA-Style Layout) -->
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