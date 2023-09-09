<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Order Invoice</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">

    <style type="text/css">
        /** PO Invoice Design */
        .po_invoice {
            border: 1px solid #000;
        }

        .po_invoice>:not(:first-child) {
            border: 0;
            border-top: 1px solid #000;
        }

        .po_invoice td {
            vertical-align: top !important;
        }
        .po_invoice td p {
            margin-bottom: 0px;
            padding: 2px 5px!important;
            color: #282828;
        }

        .po_invoice td h5, .po_invoice td h4, .po_invoice td h3, .po_invoice td h2 {
            margin: 0px;
            text-transform: uppercase;
            padding: 2px 5px!important;
            color: #282828;
        }

        .po_invoice td table {
            margin-bottom: 0;
        }

        span.invoice_logo {
            position: absolute;
            right: 15px;
            top: 0;
        }
        span.invoice_logo img {
            width: 140px;
            height: 100%;
        }

        .text-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
<table class="table po_invoice">
    <tr>
        <td colspan="8" class="text-center" style="position: relative;">
            <h5 class="text-uppercase">{{ ($purchase_order->company) ? $purchase_order->company->name : '24/7 Retail Shop Limited' }}</h5>
            <p>{{ ($purchase_order->company) ? $purchase_order->company->address : 'Plot -394, Road -29, Mohakhali DOHS' }} </p>
            <p>Dhaka, Bangladesh</p>
            <span class="invoice_logo">
                                                        {{--<img src="{{ $purchase_order->company_logo }}" alt="" style="width: 100px; height: 100px;">--}}
                                                    </span>
        </td>
    </tr>

    <tr>
        <td colspan="4" style="width: 50%">
            <p>Vendor Code: <span>{{ $purchase_order->supplier_phone }}</span></p>
            <p>Vendor Name & Address: <span class="text-uppercase">{{ $purchase_order->supplier_name }}</span></p>
            <p>
                <span class="text-uppercase">{{ $purchase_order->supplier_address }}</span><br>
                <!-- <span class="text-uppercase">Dhaka - 1215</span> -->
            </p>
            <p>VAT Reg No: <span></span></p>
            <p>TIN No: <span></span></p>
            <p>Contact Person: <span>{{ $purchase_order->supplier_contact_person_name }}</span></p>
            <p>Contact No: <span>{{ $purchase_order->supplier_phone }}</span></p>
        </td>
        @if($purchase_order->outlets)
            <td colspan="4">
                <p>Delivery Address:
                    <span class="text-uppercase">{{ $purchase_order->outlets->name }}</span><br>
                    <span class="text-uppercase">{{ $purchase_order->outlet_address }}</span><br>
                </p>
                <p>VAT Reg No: <span></span></p>
                <p>Contact Person: <span>{{ $purchase_order->outlets->contact_person_name }}</span></p>
                <p>Contact No: <span>{{ $purchase_order->outlets->outlet_number }}</span></p>
            </td>
        @else
            <td colspan="4">
                <p>Delivery Address:
                    <span class="text-uppercase">{{ ($purchase_order->warehouses) ? $purchase_order->warehouses->name : '24/7 Warehouse' }}</span><br>
                    <span class="text-uppercase">{{ ($purchase_order->warehouses) ? $purchase_order->warehouses->address : 'Plot -394, Road -29, Mohakhali DOHS' }}</span><br>
                </p>
                <p>VAT Reg No: <span></span></p>
                <p>Contact Person: <span>{{ ($purchase_order->warehouses) ? $purchase_order->warehouses->contact_person_name : 'Khandakar Kudrat-e-Khuda (Pulack)' }}</span></p>
                <p>Contact No: <span>{{ ($purchase_order->warehouses) ? $purchase_order->warehouses->warehouse_number : '01684727596' }}</span></p>
            </td>
        @endif
    </tr>
    <tr>
        <td colspan="8" class="text-center">
            <h4 class="text-uppercase">Purchase Order</h4>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="width: 50%">
            <p>Billing Address:
                <span class="text-uppercase">{{ ($purchase_order->company) ? $purchase_order->company->name : '' }}</span><br>
                <span class="text-uppercase">{{ ($purchase_order->company) ? $purchase_order->company->address : '' }}</span>
            </p>
        </td>
        <td colspan="5">
            <p style="clear: both; display: block; width: 100%;">
                <span style="width: 50%; text-align: left;">PO No: {{ $purchase_order->reference_no }}</span>
                <span style="width: 50%; text-align: right;">PO Date: {{ $purchase_order->order_date }}</span>
            </p>
            <p>RFQ No: <span></span></p>
            <p>PR No: <span>3902382329</span></p>
            <p>Currency: <span>BDT</span></p>
        </td>
    </tr>
    <tr>
        <td colspan="8">
            <table class="table table-bordered table-centered">
                <thead>
                <tr>
                    <th class="text-center">SL No.</th>
                    <th class="text-center">Code</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">UOM</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Unit Price</th>
                    <th class="text-center">Discount</th>
                    <th class="text-center">Net Amount</th>
                </tr>
                </thead>

                <tbody>
                <?php $i = 0; ?>
                @foreach($purchase_order_product as $product_item)
                    <?php $i++; ?>
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $product_item->product_code }}</td>
                        <td class="text-center">{{ $product_item->product_name }}</td>
                        <td class="text-center">{{ $product_item->purchase_unit }}</td>
                        <td class="text-right">{{ $product_item->order_quantity }}</td>
                        <td class="text-center">{{ $product_item->cost_price }}</td>
                        <td class="text-center">{{ $product_item->order_discount_amount }}</td>
                        <td class="text-right">{{ (($product_item->order_quantity * $product_item->cost_price) - $product_item->order_discount_amount) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right text-bold">{{ $purchase_order->total_quantity }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-right text-bold">{{ $purchase_order->total_value }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="4" class="text-right">Other Charges</td>
                    <td colspan="4" class="text-right">0.00</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="4" class="text-right">VAT</td>
                    <td colspan="4" class="text-right">0.00</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="4" class="text-right">Gross Amount</td>
                    <td colspan="4" class="text-right">{{ $purchase_order->total_amount }}</td>
                </tr>
                <!-- <tr>
                                                            <td colspan="8">
                                                                <p>Amount In Words: <span> </span></p>
                                                            </td>
                                                        </tr> -->

                </tbody>

            </table>
        </td>
    </tr>
</table>

    {{--<table class="table invoice_header">--}}
        {{--<tr>--}}
            {{--<td>--}}
                {{--<h3>24X7 Super Shop</h3>--}}
                {{--<h5>Central Store</h5>--}}
                {{--<?php--}}
                {{--//print_r($purchase_order); exit;--}}
                {{--?>--}}
                {{--<div class="supplier_details">--}}
                    {{--<span>SUPPLIER: {{ $purchase_order->supplier_name }}</span><br>--}}
                    {{--<span>SUPPLIER ADDRESS: {{ $purchase_order->supplier_address }}</span><br>--}}
                    {{--<span>CONTACT NO: {{ $purchase_order->supplier_phone }}</span><br>--}}
                    {{--<span>CONATCT PERSON: {{ $purchase_order->supplier_contact_person_name }}</span> <br><br>--}}

                    {{--<span>DELIVERY ADDRESS: </span><br>--}}
                {{--</div>--}}
            {{--</td>--}}

            {{--<td>--}}
                {{--<div class="order_info" style="text-align: right">--}}
                    {{--<h3>Purchase Order</h3> <br><br>--}}
                    {{--<span>ORDER# {{ $purchase_order->reference_no }}</span><br>--}}
                    {{--<span>DATE: {{ $purchase_order->order_date }}</span><br>--}}
                    {{--<span>DELIVERY DATE: {{ $purchase_order->delivery_date }}</span><br>--}}
                    {{--<span>DELIVERY TO: 24X7</span><br>--}}
                {{--</div>--}}
            {{--</td>--}}
        {{--</tr>--}}
    {{--</table>--}}

    {{--<div class="table-responsive" style="border-top: 2px solid black; margin-top: 20px;">--}}
        {{--<table class="table table-bordered table-centered table-nowrap w-100">--}}
            {{--<thead class="table-dark">--}}
            {{--<tr class="success item-head">--}}
                {{--<th class="text-center" style="width: 5%">SL </th>--}}
                {{--<th class="text-center" style="width: 40%">Product Desc. </th>--}}
                {{--<th class="text-center">Sale Price</th>--}}
                {{--<th class="text-center">Pur. Price</th>--}}
                {{--<th class="text-center">Order Qty </th>--}}
                {{--<th class="text-center">Amount</th>--}}

            {{--</tr>--}}
            {{--</thead>--}}

            {{--<tbody>--}}
            {{--<?php $i = 0; ?>--}}
            {{--@foreach($purchase_order_product as $product_item)--}}
                {{--<?php $i++; ?>--}}
                {{--<tr>--}}
                    {{--<td class="text-center">{{ $i }}</td>--}}
                    {{--<td class="text-center">{{ $product_item->product_name }}</td>--}}
                    {{--<td class="text-center">{{ $product_item->mrp_price }}</td>--}}
                    {{--<td class="text-center">{{ $product_item->cost_price }}</td>--}}
                    {{--<td class="text-center">{{ $product_item->order_quantity }}</td>--}}
                    {{--<td class="text-center">{{ $product_item->order_quantity * $product_item->cost_price }}</td>--}}

                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}

            {{--<tfoot>--}}
            {{--<tr style="font-weight: bold; text-align: right;">--}}
                {{--<td colspan="5" style="text-align: right">Total</td>--}}
                {{--<td class="text-center">{{ $purchase_order->total_amount }}</td>--}}
            {{--</tr>--}}
            {{--</tfoot>--}}

        {{--</table>--}}
    {{--</div>--}}

    {{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" type="text/javascript"></script>--}}

</body>

</html>