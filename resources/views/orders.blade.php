@extends('layouts.app')

@push('page-css')
<!-- Select2 CSS -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">

<style>
    /* Hide receipt on screen */
    .receipt-container {
        display: none;
    }

    @media print {
        @page {
            size: auto portrait;
            margin: -120px;
        }

        body {
            width: 100%;
            height: auto;
            font-family: black;
            text-align: center;
            background: white;
        }

        /* Make the receipt occupy the whole page */
        #receipt {
            position: absolute;
            top: -120px;
            left: -120px;
            width: 100%;
            max-height: auto;
            background: white;
            font-size: 35px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            box-sizing: border-box;
        }

        .business_header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }


        /* Ensure elements inside are visible */
        body * {
            visibility: hidden;
        }

        #receipt,
        #receipt * {
            visibility: visible;
        }

        h1 {
            font-size: 80px;
            font-weight: bold;
        }

        p {
            font-size: 60px;
            margin: 5px 0;
        }

        .receipt-items {
            border-top: 2px solid #000;
            padding-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-items th,
        .receipt-items td {
            /* border: 2px dashed #4A4A4A; */
            font-size: 60px;
            text-align: center;
        }

        .receipt-total {
            margin-top: 20px;
            font-size: 60px;
            text-align: right;
        }

        .payment {
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        /* Hide receipt container on screen */
        .receipt-container {
            display: none;
        }
    }
</style>
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
    <h3 class="page-title">Orders</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active">Generate Order Receipt</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="orderForm">
                    @csrf
                    <div class="form-group">
                        <label for="search">Search Product</label>
                        <select name="product_id" id="product" class="form-control select2" required>
                            @foreach($products as $product)
                            @if (!empty($product->purchase) && !($product->purchase->quantity <= 0))
                        <option value="{{ $product->id }}" data-name="{{ $product->purchase->name }}" data-price="{{ $product->price }}">
                            {{ $product->purchase->name }} - Ksh {{ $product->price }}
                        </option>
                            @endif
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="total_price">Total Price</label>
                        <input type="text" name="total_price" id="total_price" class="form-control" readonly>
                    </div>

                    <!-- Order Table -->
                    <div class="form-group">
                        <table class="table table-bordered" id="order_table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="order_items">
                                <!-- Items will be added dynamically here -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="add_to_order" class="btn btn-secondary">Add to Order</button>
                    <button type="button" id="generate_receipt" class="btn btn-primary">Generate Receipt</button>
                </form>

                <!-- Receipt Display -->
                <div class="receipt-container" id="receipt">
                    <!-- Receipt Details (Hidden Initially) -->
                    <div class="receipt-details">
                        <div class="business_header">
                            <h1>Sehha Medical Chemist</h1>
                            <p><strong>Retail/Wholesale</strong></p>
                            <p>Location: Meru, Kenya <br></p>
                            <p>Tell: 0702972797 <br></p>
                            <p>PIN: P051719234N</p>
                            <h1>Receipt</h1>
                        </div>
                        <p>
                            Served by: <span id="served_by">{{auth()->user()->name}}</span><br>
                            Date/Time: <span id="order_time">{{ \Carbon\Carbon::now()->format('d/m/y H:i:s') }}</span><br>
                            Receipt No: <span id="receipt_number">{{ rand(100000, 999999) }}</span><br>
                            Prescription No: <span id="prescription_number"></span><br>

                        </p>

                    </div>

                    <table class="receipt-items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="receipt_items"></tbody>
                    </table>

                    <div class="receipt-total">
                        Discount(%): 00 <br>
                        Total: Ksh <span id="receipt_total"></span><br><br>
                    </div>

                    <div class="payment">
                        <h1>Mpesa Paybill No:</h1>
                        <p>400200</p>
                        <h1>Account No:</h1>
                        <p>885127</p>
                    </div>

                    <div id="qr_code_container" style="display: flex; justify-content: center; margin: 10px 0;">
                        <div id="qr_code"></div>
                    </div>

                    <div>
                        <p>Thankyou!</p>
                        <p>This system is made by: <br> JM Innovatech Solutions <br> 0791446968</p>
                    </div>


                </div>


            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@push('page-js')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#product').select2();

        $('#search').on('input', function () {
            let searchText = $(this).val().toLowerCase();
            $('#product option').each(function() {
                let productName = $(this).attr('data-name').toLowerCase();
                $(this).toggle(productName.includes(searchText));
            });
            $('#product').select2(); // Refresh Select2 UI
        });
    });
</script>
@endpush

<script>
    window.onload = function() {
        let qrDiv = document.getElementById("qr_code");
        let qrText = "Thank you for visiting Sehha Medical Chemist!";

        if (qrDiv) {
            new QRCode(qrDiv, {
                text: qrText,
                width: 400,
                height: 400
            });
        } else {
            console.error("QR Code container not found.");
        }
    };
</script>

<script>
    document.getElementById('search').addEventListener('input', function () {
        let searchText = this.value.toLowerCase();
        let productOptions = document.querySelectorAll('#product option');

        productOptions.forEach(option => {
            let productName = option.getAttribute('data-name').toLowerCase();
            if (productName.includes(searchText)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        // Refresh Select2 dropdown
        $('#product').select2();
    });
</script>


<script>
    let orderItems = [];

    document.getElementById('product').addEventListener('change', updateTotalPrice);
    document.getElementById('quantity').addEventListener('input', updateTotalPrice);

    function updateTotalPrice() {
        let price = document.getElementById('product').selectedOptions[0].getAttribute('data-price');
        let quantity = document.getElementById('quantity').value;
        document.getElementById('total_price').value = price * quantity;
    }

    document.getElementById('add_to_order').addEventListener('click', function() {
        let product = document.getElementById('product').selectedOptions[0];
        let productName = product.text;
        let productId = product.value;
        let quantity = parseInt(document.getElementById('quantity').value);
        let totalPrice = parseFloat(document.getElementById('total_price').value);

        if (quantity && totalPrice) {
            let existingProduct = orderItems.find(item => item.productId === productId);

            if (existingProduct) {
                existingProduct.quantity += quantity;
                existingProduct.totalPrice = (existingProduct.quantity * parseFloat(totalPrice / quantity)).toFixed(2);

                let tableRows = document.querySelectorAll('#order_items tr');
                tableRows.forEach(row => {
                    if (row.cells[0].textContent === productName) {
                        row.cells[1].textContent = existingProduct.quantity;
                        row.cells[2].textContent = existingProduct.totalPrice;
                    }
                });
            } else {
                orderItems.push({
                    productId,
                    productName,
                    quantity,
                    totalPrice
                });

                let tableBody = document.getElementById('order_items');
                let row = document.createElement('tr');
                row.innerHTML = `
            <td>${productName}</td>
            <td>${quantity}</td>
            <td>${totalPrice.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
        `;
                tableBody.appendChild(row);
            }

            document.getElementById('quantity').value = '';
            document.getElementById('total_price').value = '';
        } else {
            alert('Please enter a valid quantity and total price.');
        }
    });

    document.getElementById('order_table').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            let row = event.target.closest('tr');
            let productName = row.cells[0].textContent;
            orderItems = orderItems.filter(item => item.productName !== productName);
            row.remove();
        }
    });
    document.getElementById('generate_receipt').addEventListener('click', function() {
        if (orderItems.length === 0) {
            alert("No items added to order.");
            return;
        }

        let receiptItemsContainer = document.getElementById('receipt_items');
        receiptItemsContainer.innerHTML = "";
        let total = 0;

        orderItems.forEach(item => {
            let row = document.createElement('tr');
            row.innerHTML = `
        <td>${item.productName}</td>
        <td>${item.quantity}</td>
        <td>${item.totalPrice}</td>
    `;
            total += parseFloat(item.totalPrice);
            receiptItemsContainer.appendChild(row);
        });

        document.getElementById('order_time').textContent = new Date().toLocaleString();
        document.getElementById('receipt_number').textContent = Math.floor(Math.random() * 1000000);
        document.getElementById('receipt_total').textContent = total.toFixed(2);

        document.getElementById('receipt').style.display = 'block';
        window.print();
        document.getElementById('receipt').style.display = 'none';

        // Send the order data to the server
        fetch("{{route('sales.store')}}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    items: orderItems
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Order saved:", data);
                orderItems = [];
                document.getElementById('order_items').innerHTML = "";
            })
            .catch(error => {
                console.error("Error:", error);
            });
    });
</script>
@endsection