<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Order Invoice</title>
    {{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
</head>

<body class="mt-5">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-7">
                        <h3>24X7 Super Shop</h3>
                        <h5>Central Store</h5>
                        <?php
                          //print_r($purchase_order); exit;
                        ?>
                        <div class="supplier_details">
                            <span>SUPPLIER: {{ $purchase_order->supplier_name }}</span><br>
                            <span>SUPPLIER ADDRESS: {{ $purchase_order->supplier_address }}</span><br>
                            <span>CONTACT NO: {{ $purchase_order->supplier_phone }}</span><br>
                            <span>CONATCT PERSON: {{ $purchase_order->supplier_contact_person_name }}</span> <br><br>

                            <span>DELIVERY ADDRESS: </span><br>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="order_info" style="text-align: right">
                            <h3>Purchase Order</h3> <br><br>
                            <span>ORDER# {{ $purchase_order->reference_no }}</span><br>
                            <span>DATE: {{ $purchase_order->order_date }}</span><br>
                            <span>DELIVERY DATE: {{ $purchase_order->delivery_date }}</span><br>
                            <span>DELIVERY TO: 24X7</span><br>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="border-top: 2px solid black; margin-top: 20px;">
                    <table class="table table-striped">
                        <thead class="tableFloatingHeaderOriginal">
                            <tr class="success item-head">
                                <th>SL </th>
                                <th>Description </th>
                                <th>Sale Price</th>
                                <th>Price</th>
                                <th>Order Qty </th>
                                <th>Free Qty </th>
                                <th>Value</th>
                                <th>Disc Amnt</th>
                                <th>Vat</th>
                                <th>Free Amt</th>
                                <th>Amount</th>

                            </tr>
                        </thead>

                        <tbody>
                        <?php $i = 0; ?>
                        @foreach($purchase_order_product as $product_item)
                            <?php $i++; ?>
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $product_item->product_code.'-'.$product_item->product_name }}</td>
                                <td>{{ $product_item->mrp_price }}</td>
                                <td>{{ $product_item->cost_price }}</td>
                                <td>{{ $product_item->order_quantity }}</td>
                                <td>{{ $product_item->order_free_quantity }}</td>
                                <td style="text-align: right">{{ $product_item->order_quantity * $product_item->cost_price }}</td>
                                <td style="text-align: right">{{ $product_item->order_discount_amount }}</td>
                                <td style="text-align: right">{{ $product_item->order_vat_amount }}</td>
                                <td style="text-align: right">{{ $product_item->order_free_amount }}</td>
                                <td style="text-align: right">{{ $product_item->order_amount }}</td>

                            </tr>
                        @endforeach
                        </tbody>

                        <tfoot>
                            <tr style="font-weight: bold; text-align: right;">
                                <td colspan="6" style="text-align: right">Total</td>
                                <td>{{ $purchase_order->total_value }}</td>
                                <td>{{ $purchase_order->total_commission_value }}</td>
                                <td>{{ $purchase_order->total_vat }}</td>
                                <td>{{ $purchase_order->total_free_amount }}</td>
                                <td>{{ $purchase_order->total_amount }}</td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" type="text/javascript"></script>--}}

</body>

</html>