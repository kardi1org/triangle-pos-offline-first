<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inventory Details</title>
    <link rel="stylesheet" href="{{ public_path('b3/bootstrap.min.css') }}">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div style="text-align: center;margin-bottom: 25px;">
                    <img width="180" src="{{ public_path('images/logo-dark.png') }}" alt="Logo">
                    <h4 style="margin-bottom: 20px;">
                        <span>Reference::</span> <strong>{{ $Inventory->reference }}</strong>
                    </h4>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-xs-4 mb-3 mb-md-0">
                                <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">
                                    Company Info:</h4>
                                <div><strong>{{ settings()->company_name }}</strong></div>
                                <div>{{ settings()->company_address }}</div>
                                <div>Email: {{ settings()->company_email }}</div>
                                <div>Phone: {{ settings()->company_phone }}</div>
                            </div>

                            <div class="col-xs-4 mb-3 mb-md-0">
                                <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">
                                    Supplier Info:</h4>
                                <div><strong>{{ $supplier->supplier_name }}</strong></div>
                                <div>{{ $supplier->address }}</div>
                                <div>Email: {{ $supplier->supplier_email }}</div>
                                <div>Phone: {{ $supplier->supplier_phone }}</div>
                            </div>

                            <div class="col-xs-4 mb-3 mb-md-0">
                                <h4 class="mb-2" style="border-bottom: 1px solid #dddddd;padding-bottom: 10px;">
                                    Invoice Info:</h4>
                                <div>Invoice: <strong>INV/{{ $Inventory->reference }}</strong></div>
                                <div>Date: {{ \Carbon\Carbon::parse($Inventory->date)->format('d M, Y') }}</div>
                                <div>
                                    Status: <strong>{{ $Inventory->status }}</strong>
                                </div>
                                <div>
                                    Payment Status: <strong>{{ $Inventory->payment_status }}</strong>
                                </div>
                            </div>

                        </div>

                        <div class="table-responsive-sm" style="margin-top: 30px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Product</th>
                                        <th class="align-middle">Net Unit Price</th>
                                        <th class="align-middle">Quantity</th>
                                        <th class="align-middle">Discount</th>
                                        <th class="align-middle">Tax</th>
                                        <th class="align-middle">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($Inventory->InventoryDetails as $item)
                                        <tr>
                                            <td class="align-middle">
                                                {{ $item->product_name }} <br>
                                                <span class="badge badge-success">
                                                    {{ $item->product_code }}
                                                </span>
                                            </td>

                                            <td class="align-middle">{{ format_currency($item->unit_price) }}</td>

                                            <td class="align-middle">
                                                {{ $item->quantity }}
                                            </td>

                                            <td class="align-middle">
                                                {{ format_currency($item->product_discount_amount) }}
                                            </td>

                                            <td class="align-middle">
                                                {{ format_currency($item->product_tax_amount) }}
                                            </td>

                                            <td class="align-middle">
                                                {{ format_currency($item->sub_total) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-xs-offset-8">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="left"><strong>Discount
                                                    ({{ $Inventory->discount_percentage }}%)</strong></td>
                                            <td class="right">{{ format_currency($Inventory->discount_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Tax ({{ $Inventory->tax_percentage }}%)</strong>
                                            </td>
                                            <td class="right">{{ format_currency($Inventory->tax_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Shipping)</strong></td>
                                            <td class="right">{{ format_currency($Inventory->shipping_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Grand Total</strong></td>
                                            <td class="right">
                                                <strong>{{ format_currency($Inventory->total_amount) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 25px;">
                            <div class="col-xs-12">
                                <p style="font-style: italic;text-align: center">{{ settings()->company_name }} &copy;
                                    {{ date('Y') }}.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
