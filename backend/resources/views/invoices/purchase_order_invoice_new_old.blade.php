<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Order Invoice</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">

</head>

<body>
    <table class="table invoice_header">
        <tr>
            <td>
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
            </td>

            <td>
                <div class="order_info" style="text-align: right">
                    <h3>Purchase Order</h3> <br><br>
                    <span>ORDER# {{ $purchase_order->reference_no }}</span><br>
                    <span>DATE: {{ $purchase_order->order_date }}</span><br>
                    {{--<span>DELIVERY DATE: {{ $purchase_order->delivery_date }}</span><br>--}}
                    <span>DELIVERY TO: 24X7</span><br>
                </div>
            </td>
        </tr>
    </table>

    <div class="table-responsive" style="border-top: 2px solid black; margin-top: 20px;">
        <table class="table table-bordered table-centered table-nowrap w-100">
            <thead class="table-dark">
            <tr class="success item-head">
                <th class="text-center" style="width: 5%">SL </th>
                <th class="text-center" style="width: 40%">Product Desc. </th>
                <th class="text-center">Sale Price</th>
                <th class="text-center">Pur. Price</th>
                <th class="text-center">Order Qty </th>
                <th class="text-center">Amount</th>

            </tr>
            </thead>

            <tbody>
            <?php $i = 0; ?>
            @foreach($purchase_order_product as $product_item)
                <?php $i++; ?>
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td class="text-center">{{ $product_item->product_name }}</td>
                    <td class="text-center">{{ $product_item->mrp_price }}</td>
                    <td class="text-center">{{ $product_item->cost_price }}</td>
                    <td class="text-center">{{ $product_item->order_quantity }}</td>
                    <td class="text-center">{{ $product_item->order_quantity * $product_item->cost_price }}</td>

                </tr>
            @endforeach
            </tbody>

            <tfoot>
            <tr style="font-weight: bold; text-align: right;">
                <td colspan="5" style="text-align: right">Total</td>
                <td class="text-center">{{ $purchase_order->total_amount }}</td>
            </tr>
            </tfoot>

        </table>
    </div>

    {{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" type="text/javascript"></script>--}}

</body>

</html>