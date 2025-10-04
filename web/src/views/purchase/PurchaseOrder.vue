
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Order</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="redirectRoute()">
                            Purchase Order List
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="tab-pane show active" v-if="loading">
                        <div class="row"> 
                            <div class="col-md-5">  
                            </div>
                            <div class=" col-md-2"> 
                                <img src="../../assets/image/loading.gif" alt="Loading..." style="width:130px">
                            </div>
                            <div class="col-md-5">  
                            </div>
                        </div>
                    </div>

                    <div class="card-body" v-if="!loading">                        
                        <!-- <form id="purchase_order_form" @submit.prevent="submitForm()"> -->

                            <div class="row">  
                     
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="spo" name="po_type" class="form-check-input" value="spo" checked @change="poGenerateType($event)">
                                        <label class="form-check-label" for="spo" style="font-size: 18px; font-weight: bold;">Single PO</label>
                                    </div>
                                    <!-- <div class="form-check">
                                        <input type="radio" id="mpo" name="po_type" class="form-check-input" value="mpo" @change="poGenerateType($event)"> 
                                        <label class="form-check-label" for="mpo">Multiple PO</label>
                                    </div> -->
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="mpo" name="po_type" class="form-check-input" value="mpo" @change="poGenerateType($event)"> 
                                        <label class="form-check-label" for="mpo" style="font-size: 18px; font-weight: bold;">Multiple PO</label>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <hr>
                            </div>

                            <div class="row mb-3">
                                <div class="form-group col-md-3">
                                    <label for="reference_no">Reference No *</label>
                                    <input type="text" class="form-control border" id="reference_no" @keypress="onkeyPress('reference_no')" v-model="obj.reference_no" readonly>
                                    <div class="invalid-feedback" v-if="errors.reference_no">
                                        {{errors.reference_no[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="order_date">Order Date *</label>
                                    <input type="text" class="form-control border" id="order_date" @change="onkeyPress('order_date')" v-model="obj.order_date" readonly>
                                    <div class="invalid-feedback" v-if="errors.order_date">
                                        {{errors.order_date[0]}}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="warehouse_id" style="width: 100%;">Delivery Location to Warehouse</label>
                                            <select class="form-control border" id="warehouse_id" v-model="obj.warehouse_id" @change="onkeyPress('warehouse_id'), onChangeWarehouse" :disabled="obj.outlet_id != '' ? true : false">
                                                <option value="">--- Select Warehouse ---</option>
                                                <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.id">{{ warehouse.name }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="warehouse_id" style="width: 100%;">Delivery Location to Outlet</label>
                                            <select class="form-control border" id="outlet_id" v-model="obj.outlet_id" @change="onkeyPress('outlet_id'), onChangeOutlet" :disabled="obj.warehouse_id != '' ? true : false">
                                                <option value="">--- Select Outlet ---</option>
                                                <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="delivery_date">Delivery Date *</label>
                                    <input type="date" class="form-control border" id="delivery_date" @change="onkeyPress('delivery_date')" v-model="obj.delivery_date">
                                    <div class="invalid-feedback" v-if="errors.delivery_date">
                                        {{errors.delivery_date[0]}}
                                    </div>
                                </div>

                                <!-- <div class="form-group col-md-3" v-if="is_single_po">
                                    <label for="supplier_id">Supplier *</label>
                                    <select class="form-control border" id="supplier_id" v-model="obj.supplier_id"  @change="onChangeSupplier($event), onkeyPress('supplier_id')">
                                        <option value="">--- Select Supplier ---</option>
                                        <option v-for="(supplier, index) in suppliers" :key="index" :refs="supplier" :value="supplier.id">{{ supplier.name }} [{{ supplier.phone }}]</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.supplier_id">
                                        {{errors.supplier_id[0]}}
                                    </div>
                                </div> -->

                                <div class="form-group col-md-3" v-if="is_single_po">
                                    
                                    <label for="supplier_id">Supplier *</label>    
                                    <div class="input-group input-group-merge">
                                        <Multiselect 
                                            class="form-control border supplier_id" 
                                            mode="single"
                                            v-model="obj.supplier_id"
                                            placeholder="Supplier"  
                                            @change="onChangeSupplier($event), onkeyPress('supplier_id')"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="suppliers"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                        />  
                                        <div class="input-group-text"><a @click="supplierAddModal">  <i class="fas fa-2x fa-plus-circle"  ></i> </a> </div> 
                                    </div> 
                                </div>

                                <div class="form-group col-md-3" v-if="is_single_po">
                                    <label for="supplier_payment_type">Supplier Payment Type*</label>
                                    <select class="form-control border" id="supplier_payment_type" v-model="obj.supplier_payment_type" @change="onkeyPress('supplier_payment_type')">
                                        <option value="">--- Select Payment Type ---</option>
                                        <option value="cash_purchase">Cash Purchase</option>
                                        <option value="credit">Credit</option>
                                        <option value="after_sale">After Sale</option>
                                        <option value="sale_after_commission">Sale After Commission</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.supplier_payment_type">
                                        {{errors.supplier_payment_type[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="remarks">Remarks </label>
                                    <textarea class="form-control border" id="remarks" @change="onkeyPress('remarks')" v-model="obj.remarks" rows="1"></textarea>
                                    <div class="invalid-feedback" v-if="errors.remarks">
                                        {{errors.remarks[0]}}
                                    </div>
                                </div>
                                <!-- <div :class="!is_multiple_po ? 'col-md-6' : 'col-md-12'">
                                    <div class="float-right">
                                        <button type="button" class="btn btn-primary" style="margin-top: 22px;" @click="onClickSupplierProduct()" v-if="productShowBtn">Show Product</button>
                                        <button type="button" class="btn btn-primary" style="margin-top: 22px;" v-else disabled>Show Product</button>
                                    </div>
                                </div> -->
                            </div>

                            <!-- Product Details -->
                            <div class="card">
                                <div class="card-header text-left">
                                    Product Details
                                    <input type="hidden" v-model="obj.total_quantity">
                                    <!-- <span class="total_quantity">Chalan Quantity: <b>{{ totalQuantity }}</b></span> -->
                                </div>
                                <div class="card-body">
                                    <!-- <div class="form-group mb-3">
                                        <input type="text" class="form-control border" id="search_product" placeholder="Search Product">
                                    </div> -->
                                    <div class="product_table">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <!-- <th class="text-center" style="width: 5%"><input type="checkbox" v-model="product_all_checked" @change="checkedAll"></th>  -->
                                                    <th class="text-center" style="width: 5%">SL </th> 
                                                    <th class="text-left" style="width: 30%">Name </th> 
                                                    <th class="text-center" style="width: 7%">UOM</th>
                                                    <!-- <th class="text-center">Crt. Size</th>v -->
                                                    <th class="text-center" style="width: 10%">Pur. Price</th>
                                                    <th class="text-center" style="width: 10%">MRP</th>
                                                    <th class="text-center" style="width: 10%">Ord. Qty</th>
                                                    <!-- <th>Disc (%)</th>
                                                    <th>Free Qty</th> -->
                                                    <th class="text-center" style="width: 10%">Amount</th>
                                                    <th class="text-center" v-if="is_multiple_po" style="width: 20%">Supplier</th>
                                                    <th class="text-center" style="width: 3%"></th>
                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <!-- <td class="text-center"><input type="checkbox" v-model="product_item.checked"></td> -->
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-left">
                                                        <Multiselect
                                                            class="form-control border product_id"
                                                            mode="single"
                                                            v-model="product_item.product_id"
                                                            placeholder="Product"
                                                            @change="addNewRow($event, i), onChangeProduct($event, i)"   
                                                            :searchable="true" 
                                                            :filter-results="true"
                                                            :options="products"
                                                            :classes="multiclasses"
                                                            :close-on-select="true" 
                                                            :min-chars="1"
                                                            :resolve-on-load="false"
                                                            :hide-selected="true" 
                                                        />
                                                    </td>
                                                    <td class="text-center">{{ product_item.unit_code }}</td>
                                                    <!-- <td class="text-center">{{ product_item.carton_size }}</td> -->
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" @focus="addNewRow($event, i)" v-model="product_item.tp">
                                                    </td>
                                                    <td class="text-center">{{ product_item.mrp }}</td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange(), quantityChanged(i, $event.target.value)" @focus="addNewRow($event, i)" v-model="product_item.order_qty"></td>
                                                    <!-- <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.disc_percent" style="width: 50px"></td> -->
                                                    <!-- <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.free_qty" style="width: 50px"></td> -->
                                                    <td class="text-center">{{ product_item.order_qty * product_item.tp }}</td>
                                                    <td class="text-center" style="width: 150px;" v-if="is_multiple_po">
                                                        <Multiselect
                                                            class="form-control border supplier_id"
                                                            mode="single"
                                                            v-model="product_item.supplier_id"
                                                            placeholder="Product"
                                                            @change="addNewRow($event, i, 'supplier'), itemSupplierChange($event, i)"   
                                                            :searchable="true" 
                                                            :filter-results="true"
                                                            :options="mpo_suppliers"
                                                            :classes="multiclasses"
                                                            :close-on-select="true" 
                                                            :min-chars="1"
                                                            :resolve-on-load="false" 
                                                        />
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="text-danger" style="font-size: 17px" @click="deleteRow(i)" v-if="i != 0"><i class="mdi mdi-close"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>

                                    <!-- <hr>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="total_value">Total Value</label>
                                            <input type="text" class="form-control border" id="total_value" v-model="obj.total_value" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="commission_value">Commission Value</label>
                                            <input type="text" class="form-control border" id="comission_value" v-model="obj.commission_value" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_vat">Total VAT</label>
                                            <input type="text" class="form-control border" id="total_vat" v-model="obj.total_vat" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="total_free_amount">Total Free Amount</label> 
                                            <input type="text" class="form-control border" id="total_free_amount" v-model="obj.total_free_amount" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_amount">Total Amount </label>
                                            <input type="text" class="form-control border" id="total_amount" v-model="obj.total_amount" readonly>
                                        </div>
                                    </div> -->

                                    <h5 class="text-right text-danger">Total Amount: <span>{{ obj.total_amount }}</span> </h5>
                                </div>
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary float-right" :disabled="disabled" v-if="is_single_po" @click.prevent="submitForm()">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> GENERATE ORDER 
                                </button>
                                <button type="button" class="btn btn-primary float-right" :disabled="disabled" v-else @click="beforeSubmitPreviewMPO()">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> GENERATE ORDER 
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <!-- <button type="button" class="btn btn-info" :disabled="disabled">HOLD</button>-->
                                <button type="button" class="btn btn-info float-right" :disabled="disabled" style="margin-right: 7px;" v-if="is_single_po" @click="beforeSubmitPreview()">PREVIEW</button>
                                <!-- <button type="button" class="btn btn-info float-right" :disabled="disabled" style="margin-right: 7px;" v-else @click="beforeSubmitPreviewMPO()">PREVIEW</button> -->
                            </div>
                        <!-- </form> -->
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Add Modal -->
        <Modal @close="supplierAddModal()" :modalActive="supplierAddModalActive">
            <div class="modal-content scrollbar-width-thin supplier-add-modal">
                <div class="modal-header"> 
                    <button @click="supplierAddModal()" type="button" class="btn btn-default">X</button>
                </div> 
                <div class="modal-body">  
                    <div class="row">
                        <div class="col-md-12">  
                            <div class="mb-3">
                                <label for="supplier_type_id" class="form-label">Supplier Type *</label> 
                                    <select class="form-control border" v-model="sform.supplier_type_id" @change="onkeyPress('supplier_type_id')" id="supplier_type_id">
                                        <option value="">Select Supplier Type</option>
                                        <option v-for="(supplier_type, index) in supplier_types" :value="supplier_type.id" :key="index"> {{supplier_type.name}} </option>                                 
                                    </select> 
                                    <div class="invalid-feedback" v-if="errors.supplier_type_id">
                                        {{errors.supplier_type_id[0]}}
                                    </div>
                            </div> 
                        </div> <!-- end col -->

                        <div class="col-md-12">  
                            <div class="mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" class="form-control " @keypress="onkeyPress('name')" v-model="sform.name" id="name" placeholder="Supplier Name" autocomplete="off"> 
                                <div class="invalid-feedback" v-if="errors.name">
                                    {{errors.name[0]}}
                                </div>
                            </div>   
                        </div> <!-- end col --> 
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email </label>
                                <input type="text" class="form-control " @keypress="onkeyPress('email')" v-model="sform.email" id="email" placeholder="Supplier Email" autocomplete="off"> 
                                <div class="invalid-feedback" v-if="errors.email">
                                    {{errors.email[0]}}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">  
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" @keypress="onkeyPress('phone')" v-model="sform.phone" id="phone" placeholder="Supplier mobile number"> 
                                <div class="invalid-feedback" v-if="errors.phone">
                                    {{errors.phone[0]}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">  
                        <!-- <div class="mb-3"> 
                            <label for="customer_code" class="form-label">Customer Code *</label> 
                            <div class="input-group">
                                <input type="text" class="form-control border" @keypress="onkeyPress('customer_code')" v-model="cform.customer_code" id="product_code" placeholder="Customer Code" autocomplete="off">  
                                <span class="input-group-text " id="random_num" @click="random_num()"  >
                                    <i class="fa fa-random"></i>
                                </span> 
                                <div class="invalid-feedback" v-if="errors.customer_code">
                                {{errors.customer_code[0]}}
                                </div>
                            </div>
                        </div>    -->
                        </div> <!-- end col --> 
                    </div> 
                </div>
                
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-primary " @click="createNewSupplier()" :disabled="disabledSupplierAdd">
                        <span v-show="isSupplierAddSubmit">
                            <i class="fas fa-spinner fa-spin" ></i>
                        </span>Submit
                    </button>                  
                </div> 
            </div>
        </Modal>

        <!-- Modal for SPO Preview Before Submit-->
        <Modal @close="togglePreviewSPOModal()" :modalActive="previewSPOModalActive">
            <div class="modal-content scrollbar-width-thin orderPreview">
                <div class="modal-header"> 
                    <button @click="togglePreviewSPOModal()" type="button" class="btn btn-default">X</button>
                    <div class="title" style="text-align:center; width: 100%">
                        <h3 style="width: 100%">Purchase Order Preview</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table po_invoice" style="border: 1px solid #000;">
                                        <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : '' }}</h5>
                                                <p>{{ (purchase_order.company) ? purchase_order.company.address : '' }} </p>
                                                <p>Dhaka, Bangladesh</p>
                                                <span class="invoice_logo">
                                                    <img src="assets/images/logo.png" alt="">
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Vendor Code: <span>{{ purchase_order.supplier_phone }}</span></p>
                                                <p>Vendor Name & Address: <span class="text-uppercase">{{ purchase_order.supplier_name }}</span></p>
                                                <p>
                                                    <span class="text-uppercase">{{ purchase_order.supplier_address }}</span><br>
                                                    <!-- <span class="text-uppercase">Dhaka - 1215</span> -->
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>TIN No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.supplier_contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.supplier_phone }}</span></p>
                                            </td>
                                            <td colspan="4" v-if="purchase_order.outlet">
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ purchase_order.outlet.name }}</span><br>
                                                    <span class="text-uppercase">{{ purchase_order.outlet_address }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.outlet.contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.outlet.outlet_number }}</span></p>
                                            </td>
                                            <td colspan="4" v-else>
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.warehouse) ? purchase_order.warehouse.name : '24/7 Warehouse' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.warehouse) ? purchase_order.warehouse.address : 'Plot -394, Road -29, Mohakhali DOHS' }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouse.contact_person_name : 'Khandakar Kudrat-e-Khuda (Pulack)' }}</span></p>
                                                <p>Contact No: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouse.warehouse_number : '01684727596' }}</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <h4 class="text-uppercase">Purchase Order</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Billing Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : '' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.address : '' }}</span>
                                                </p>
                                            </td>
                                            <td colspan="4">
                                                <p style="overflow: hidden;">
                                                    <span class="float-left">PO No: {{ obj.reference_no }}</span>
                                                    <span class="float-right">PO Date: {{ obj.order_date }}</span>
                                                </p>
                                                <p>RFQ No: <span></span></p>
                                                <p>PR No: <span></span></p>
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
                                                            <!-- <th class="text-center">Discount</th> -->
                                                            <th class="text-center">Net Amount</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr v-for="(product_item, i) in preview_product_items" :key="i">
                                                            <td class="text-center">{{ i + 1 }}</td>
                                                            <td class="text-center">{{ product_item.product_code }}</td>
                                                            <td class="text-center">{{ product_item.product_name }}</td>
                                                            <td class="text-center">{{ product_item.unit_code }}</td>
                                                            <td class="text-right">{{ product_item.order_qty }}</td>
                                                            <td class="text-center">{{ product_item.tp }}</td>
                                                            <!-- <td class="text-center">{{ product_item.disc_amount }}</td> -->
                                                            <td class="text-right">{{ ((product_item.order_qty * product_item.tp) - 0) }}</td>
                                                        </tr>

                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Total</td>
                                                            <td class="text-right text-bold">{{ obj.total_quantity }}</td>
                                                            <td></td>
                                                            <td class="text-right text-bold">{{ obj.total_value }}</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Other Charges</td>
                                                            <td colspan="3" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">VAT</td>
                                                            <td colspan="3" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Gross Amount</td>
                                                            <td colspan="3" class="text-right">{{ obj.total_amount }}</td>
                                                        </tr>
                                                        <!-- <tr>
                                                            <td colspan="8">
                                                                <p>Amount In Words: <span>{{ amount_in_words }}</span></p>
                                                            </td>
                                                        </tr> -->
                                                        
                                                    </tbody>

                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                    </table>

                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary float-right" :disabled="disabled" @click.prevent="submitForm()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span> CONFIRM 
                                        </button>
                                        <button type="button" class="btn btn-danger float-right" :disabled="disabled" style="margin-right: 7px;" @click="togglePreviewSPOModal()">CLOSE</button>
                                    </div>

                                </div>
                                
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </Modal>

        <!-- Modal for multiple PO List View -->
        <Modal @close="togglePreviewMPOModal()" :modalActive="previewMPOModalActive">
            <div class="modal-content scrollbar-width-thin mpOrderPreview">
                <div class="modal-header"> 
                    <button @click="togglePreviewMPOModal()" type="button" class="btn btn-default">X</button>
                    <div class="title" style="text-align:center; width: 100%">
                        <h3 style="width: 100%">Purchase Order Preview List</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped table-centered po_list">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">SL</th>
                                                <th style="width: 30%;">Supplier</th>
                                                <th style="width: 40%">Supplier Payment Type</th>
                                                <th class="text-center" style="width: 10%">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody v-if="purchase_orders.length > 0">
                                            <tr v-for="(purchase_order, index) in purchase_orders" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ purchase_order.supplier_name }}</td>
                                                <td>
                                                    <div class="form-group">
                                                        <select id="supplier_payment_type" class="form-control" v-model="purchase_order.supplier_payment_type">
                                                            <option value="">--- Select Payment Type ---</option>
                                                            <option value="cash_purchase">Cash Purchase</option>
                                                            <option value="credit">Credit</option>
                                                            <option value="after_sale">After Sale</option>
                                                            <option value="sale_after_commission">Sale After Commission</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" class="text-primary" @click="beforeSubmitSingleMPOPreview(purchase_order)"><i class="mdi mdi-eye-outline"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                                
                                <div class="card-footer">
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary float-right" :disabled="disabled" @click.prevent="submitMultiplePOForm()">
                                            <span v-show="isFinalMPOSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span> CONFIRM 
                                        </button>
                                        <button type="button" class="btn btn-danger float-right" :disabled="disabled" style="margin-right: 7px;" @click="togglePreviewMPOModal()">CLOSE</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </Modal>

        <!-- MOdal for Multiple PO List Single Preview -->
        <Modal @close="toggleMPOSinglePreviewModal()" :modalActive="previewMPOSingleModalActive">
            <div class="modal-content scrollbar-width-thin orderMPOSinglePreview">
                <div class="modal-header"> 
                    <button @click="toggleMPOSinglePreviewModal()" type="button" class="btn btn-default">X</button>
                    <div class="title" style="text-align:center; width: 100%">
                        <h3 style="width: 100%">Purchase Order Preview</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table po_invoice" style="border: 1px solid #000;">
                                        <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ (order_item.company) ? order_item.company.name : '' }}</h5>
                                                <p>{{ (order_item.company) ? order_item.company.address : '' }} </p>
                                                <p>Dhaka, Bangladesh</p>
                                                <span class="invoice_logo">
                                                    <img src="assets/images/logo.png" alt="">
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Vendor Code: <span>{{ order_item.supplier_phone }}</span></p>
                                                <p>Vendor Name & Address: <span class="text-uppercase">{{ order_item.supplier_name }}</span></p>
                                                <p>
                                                    <span class="text-uppercase">{{ order_item.supplier_address }}</span><br>
                                                    <!-- <span class="text-uppercase">Dhaka - 1215</span> -->
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>TIN No: <span></span></p>
                                                <p>Contact Person: <span>{{ order_item.supplier_contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ order_item.supplier_phone }}</span></p>
                                            </td>
                                            <td colspan="4" v-if="order_item.outlet">
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ order_item.outlet.name }}</span><br>
                                                    <span class="text-uppercase">{{ order_item.outlet_address }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ order_item.outlet.contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ order_item.outlet.outlet_number }}</span></p>
                                            </td>
                                            <td colspan="4" v-else>
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ (order_item.warehouse) ? order_item.warehouse.name : '24/7 Warehouse' }}</span><br>
                                                    <span class="text-uppercase">{{ (order_item.warehouse) ? order_item.warehouse.address : 'Plot -394, Road -29, Mohakhali DOHS' }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ (order_item.warehouses) ? order_item.warehouse.contact_person_name : 'Khandakar Kudrat-e-Khuda (Pulack)' }}</span></p>
                                                <p>Contact No: <span>{{ (order_item.warehouses) ? order_item.warehouse.warehouse_number : '01684727596' }}</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <h4 class="text-uppercase">Purchase Order</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Billing Address: 
                                                    <span class="text-uppercase">{{ (order_item.company) ? order_item.company.name : '' }}</span><br>
                                                    <span class="text-uppercase">{{ (order_item.company) ? order_item.company.address : '' }}</span>
                                                </p>
                                            </td>
                                            <td colspan="4">
                                                <p style="overflow: hidden;">
                                                    <span class="float-left">PO No: {{ order_item.reference_no }}</span>
                                                    <span class="float-right">PO Date: {{ order_item.order_date }}</span>
                                                </p>
                                                <p>RFQ No: <span></span></p>
                                                <p>PR No: <span></span></p>
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
                                                            <!-- <th class="text-center">Discount</th> -->
                                                            <th class="text-center">Net Amount</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr v-for="(product_item, i) in order_item.order_products" :key="i">
                                                            <td class="text-center">{{ i + 1 }}</td>
                                                            <td class="text-center">{{ product_item.product_code }}</td>
                                                            <td class="text-center">{{ product_item.product_name }}</td>
                                                            <td class="text-center">{{ product_item.unit_code }}</td>
                                                            <td class="text-right">{{ product_item.order_qty }}</td>
                                                            <td class="text-center">{{ product_item.tp }}</td>
                                                            <!-- <td class="text-center">{{ product_item.disc_amount }}</td> -->
                                                            <td class="text-right">{{ ((product_item.order_qty * product_item.tp) - 0) }}</td>
                                                        </tr>

                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Total</td>
                                                            <td class="text-right text-bold">{{ order_item.total_quantity }}</td>
                                                            <td></td>
                                                            <td class="text-right text-bold">{{ order_item.total_value }}</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Other Charges</td>
                                                            <td colspan="3" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">VAT</td>
                                                            <td colspan="3" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Gross Amount</td>
                                                            <td colspan="3" class="text-right">{{ order_item.total_amount }}</td>
                                                        </tr>
                                                        <!-- <tr>
                                                            <td colspan="8">
                                                                <p>Amount In Words: <span>{{ amount_in_words }}</span></p>
                                                            </td>
                                                        </tr> -->
                                                        
                                                    </tbody>

                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="buttons">
                                        <button type="button" class="btn btn-danger float-right" @click="toggleMPOSinglePreviewModal()">CLOSE</button>
                                    </div>

                                </div>
                                
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </Modal>

    </div>
    </transition>
</template>
<script>
// import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
// import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 

export default {
    name: 'Purchase Order',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            isSupplierAddSubmit: false,
            disabledSupplierAdd: false,
            isFinalMPOSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            modalActive:false,
            supplierAddModalActive:false,
            previewSPOModalActive:false,
            previewMPOModalActive:false,
            previewMPOSingleModalActive:false,
            errors: {},
            btn:'Create',
            productShowBtn: false, 
            supplier_types: [],
            suppliers: [],
            mpo_suppliers: [{label: "Select Supplier", value: ""}],
            warehouses: [],
            outlets: [],
            products: [],
            products_data: [],
            product_items: [{
                // outlet_id: '',
                supplier_id: '',
                product_id: '',
                product_code: '',
                product_name: '',
                unit_code: 'pcs',
                product_unit_id: 0,
                order_qty: 0,
                tp: 0,
                mrp: 0,
                amnt: 0,
                disabled: false,
            }],
            obj: {
                supplier_id: '',
                supplier_payment_type: '',
                number_of_po: '',
                supply_schedule: '',
                order_date: new Date().toISOString().slice(0,10),
                delivery_date: new Date().toISOString().slice(0,10),
                warehouse_id: '',
                outlet_id: '',
                reference_no: '',
                start_date: '',
                end_date: '',
                total_quantity: 0,
                total_value: 0,
                commission_value: 0,
                total_vat: 0,
                total_free_amount: 0,
                total_amount: 0, 
                remarks: ''
            },
            sform: new Form({
                supplier_type_id: '',
                name: '',
                email: '',
                phone: '',
            }),
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            product_all_checked: false,
            is_single_po: true,
            is_multiple_po: false,
            item_curr_supplier_id: '',
            // For SPO Preview
            purchase_order: {},
            preview_product_items: [],

            // For MPO Preview
            purchase_orders: [],
            order_item: '',
            
        };
    },
    created() {
        this.fetchOrderReferenceNo();
        this.fetchSupplierType();
        this.fetchSuppliers();
        this.fetchOutlets();
        this.fetchWarehouses(); 
        this.fetchProducts();     
    },
    methods: { 
        supplierAddModal() {
            this.supplierAddModalActive = !this.supplierAddModalActive;
            if(!this.supplierAddModalActive) {

            }

        },

        togglePreviewSPOModal() {
            this.previewSPOModalActive = !this.previewSPOModalActive;
            if(!this.previewSPOModalActive) {
                this.preview_product_items = [];
                this.purchase_order = {};
            }

        },

        togglePreviewMPOModal() {
            this.previewMPOModalActive = !this.previewMPOModalActive;
            if(!this.previewMPOModalActive) {

            }

        },

        toggleMPOSinglePreviewModal() {
            this.previewMPOSingleModalActive = !this.previewMPOSingleModalActive;

        },

        beforeSubmitPreview() {
            this.preview_product_items = this.product_items.filter(function(pitem) {
                if((pitem.product_id != "" && pitem.product_id != null) && (pitem.tp != 0 && pitem.tp != "") && (pitem.order_qty != 0 && pitem.order_qty != "")) {
                    return pitem;
                }
            });

            console.log("sgfsdhj", this.preview_product_items);

            if(this.preview_product_items.length > 0) {
                var data = new FormData();
                data.append('supplier_id', this.obj.supplier_id);
                data.append('warehouse_id', this.obj.warehouse_id);
                data.append('outlet_id', this.obj.outlet_id);
                axios.post(this.apiUrl+'/purchase_orders/getBeforeSubmitPreviewSPO', data, this.headers)
                .then((resp) => {
                    this.purchase_order = resp.data.data;
                })
                .catch((err) => {
                    console.log("error", err.response);
                });
                setTimeout(this.togglePreviewSPOModal(), 2000);

            }else{
                this.$toast.error("Please at least one or more product item order qty greater then 0 or more!");
            }
        },

        beforeSubmitPreviewMPO() {
            this.isSubmit = true;
            this.disabled = true;
            var data = new FormData();
            data.append("order_date", this.obj.order_date);
            data.append("delivery_date", this.obj.delivery_date);
            data.append("warehouse_id", this.obj.warehouse_id);
            data.append("outlet_id", this.obj.outlet_id);
            data.append("reference_no", this.obj.reference_no);
            data.append("products", JSON.stringify(this.product_items));

            axios.post(this.apiUrl+'/purchase_orders/getBeforeSubmitPreviewMPO', data, this.headers)
            .then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                this.purchase_orders = res.data.data;
                setTimeout(this.togglePreviewMPOModal(), 2000);
            })
            .catch((err) => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                console.log("err", err.response.data);
            })

            
        },

        beforeSubmitSingleMPOPreview(order_data)
        {
            this.order_item = order_data;
            setTimeout(this.toggleMPOSinglePreviewModal(), 2000);
        },

        checkedRequiredPrimary(){
            // if(this.obj.supplier_id && this.obj.order_date &&  this.obj.delivery_date && this.obj.reference_no && this.obj.start_date && this.obj.end_date){
            var condition = false;
            if(this.is_single_po){
                if(this.obj.supplier_id && this.obj.order_date && this.obj.reference_no && (this.obj.warehouse_id || this.obj.outlet_id)){
                    condition = true;
                }else{
                    condition = false;
                }
            }else{
                if(this.obj.order_date && this.obj.reference_no && (this.obj.warehouse_id || this.obj.outlet_id)){
                    condition = true;
                }else{
                    condition = false;
                }
            }

            if(condition){
                this.productShowBtn = true;
            }else{
                 this.productShowBtn = false;
            }
        },

        checkedAll: function(){
            var checked = this.product_all_checked;
            if(checked == true) {
                return this.product_items.filter(item=>item.checked = true).length > 0
            }else{
                return this.product_items.filter(item=>item.checked =false).length > 0
            }
        },
        // checkedRequiredFinal(){
        //     //this.checkedRequiredPrimary();
        //     if(this.product_items.qty){
        //         this.disabled = false;
        //     }else{
        //          this.disabled = true;
        //     }
        // },

        poGenerateType: function(event){
            var curr_val = event.target.value;
            if(curr_val == "spo") {
                this.is_multiple_po = false;
                this.is_single_po = true;
                this.mpo_suppliers = [{label:"Select Supplier", value:""}];
            }else{
                this.is_multiple_po = true;
                this.is_single_po = false;

                // this.suppliers.map((item) => {
                //     this.mpo_suppliers.push({label:item.name, value:item.id});
                // });

                this.mpo_suppliers = this.suppliers;
            }     
            this.product_items = [{
                // outlet_id: '',
                supplier_id: '',
                product_id: '',
                product_code: '',
                product_name: '',
                unit_code: 'pcs',
                product_unit_id: 0,
                order_qty: 0,
                tp: 0,
                mrp: 0,
                amnt: 0,
                disabled: false,
            }];       
            this.obj.supplier_id = '';
            this.item_curr_supplier_id = '';
            this.obj.supplier_payment_type = '';
            this.obj.warehouse_id = '';
            this.obj.outlet_id = '';
            this.checkedRequiredPrimary();
        },

        redirectRoute() {
            this.$router.push({name: "order-list"});
        },

        // Get Reference no 
        fetchOrderReferenceNo() {
            axios.get(this.apiUrl+'/purchase_orders/getOrderReferenceNo', this.headers)
            .then((res) => {
                this.obj.reference_no = res.data.data.reference_no;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }) 
        },

        // Supplier Type
        fetchSupplierType() { 
            axios.get(this.apiUrl+'/supplier_types', this.headerjson)
            .then((res) => {
                this.supplier_types = res.data.data; 
            }) ;
        },

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headerjson)
            .then((res) => {
                var suppliers = [{label: 'Select Supplier', value: ""}]; 
                res.data.data.map((item) => {
                    suppliers.push({label: item.name +' ['+ item.phone +']', value: item.id});
                });
                this.suppliers = suppliers;
            })
            .catch((err) => { 
                console.log('err => ',err.response) 
            }) ;
        },

        onChangeSupplier(event){
            // var supplier_id = event.target.value;
            var supplier_id = event;
            if(supplier_id != ''){
                
            }
        },

        createNewSupplier: function() {
            this.isSupplierAddSubmit = true;
            this.disabledSupplierAdd = true;
            const formData = new FormData();
            formData.append("supplier_type_id", this.sform.supplier_type_id);
            formData.append("name", this.sform.name);
            formData.append("email", this.sform.email);
            formData.append("phone", this.sform.phone);
            
            var postEvent = axios.post(this.apiUrl+'/suppliers/add', formData, this.headers);

            postEvent.then((res) => {
                this.isSupplierAddSubmit = false;
                this.disabledSupplierAdd = false;
                if(res.status == 200){
                    // this.fetchSuppliers();
                    var supplier = res.data.data;
                    this.suppliers.push({label: supplier.name +' ['+ supplier.phone +']', value: supplier.id});
                    this.obj.supplier_id = supplier.id;
                    this.obj.supplier_payment_type = 'cash_purchase';
                    this.$toast.success(res.data.message); 
                    this.sform.reset();
                    this.supplierAddModal();
                    
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isSupplierAddSubmit = false;
                this.disabledSupplierAdd = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }

            })

        },

        // Warehouses Data
        fetchWarehouses() {
            axios.get(this.apiUrl+'/warehouses', this.headers)
            .then((res) => {
                this.warehouses = res.data.data;
            })
            .catch((err) => {
                console.log("error => ", err.response);
            }) ;
        },

        onChangeWarehouse: function(event) {
            this.item_curr_supplier_id = '';
            var w_id = event.target.value;
            if(w_id != "") {
                this.obj.outlet_id = '';
            } 
        },
        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headers)
            .then((res) => {
                this.outlets = res.data.data;
            })
            .catch((err) => {
                console.log("error", err.response);
            });
        },
        
        onChangeOutlet: function(event) {
            this.item_curr_supplier_id = '';
            var ol_id = event.target.value;
            if(ol_id != "") {
                this.obj.warehouse_id = '';
            } 
        },

        // Products Data
        fetchProducts() {
            axios.get(this.apiUrl+"/products", this.headerjson)
            .then((resp) => {
                this.products_data  = resp.data.data;
                this.products = [{label: "Select Products", value: ""}];
                resp.data.data.map((item) => {
                    this.products.push({label:item.product_name, value:item.id});
                });
            })
            .catch((err) => {
                console.log("error", err.response)
            })
            .finally(() => {
                this.loading = false;
            });
        },  
        // New Product Row Add
        addNewRow(value, index, change_field="") {
            var item_length = this.product_items.length;
            if(index == (item_length - 1)) {
                var curr_supplier_id = "";
                if(change_field == "supplier") {
                    curr_supplier_id = value;
                }else{
                    curr_supplier_id = this.product_items[index].supplier_id;
                }

                var next_supplier_id = '';
                if(curr_supplier_id != "") {
                    next_supplier_id = curr_supplier_id;
                }else{
                    next_supplier_id = this.item_curr_supplier_id;
                } 

                this.product_items.push(
                    {
                        // outlet_id: '',
                        supplier_id: next_supplier_id,
                        product_id: '',
                        product_code: '',
                        product_name: '',
                        unit_code: 'pcs',
                        product_unit_id: 0,
                        order_qty: 0,
                        tp: 0,
                        mrp: 0,
                        amnt: 0,
                        disabled: false,
                    }
                );
            }
        },

        deleteRow(index) {
            this.product_items.splice(index, 1);
        },

        onChangeProduct(product_id, index) {
            const product = this.products_data.find(({id}) => id == product_id);

            var p_tp = (product) ? product.cost_price : 0;
            var p_mrp = (product) ? product.mrp_price : 0;
            var p_code = (product) ? product.product_code : '';
            var p_name = (product) ? product.product_name : '';
            var unit_id = (product) ? product.purchase_measuring_unit : 0;

            var unit_code = 'pcs';
            if(unit_id != 0) {
                axios.get(this.apiUrl+'/units/'+unit_id, this.headerjson)
                .then((res) => {
                    unit_code = res.data.data.unit_code;
                })
                .catch()
            }

            this.product_items[index].product_code = p_code;
            this.product_items[index].product_name = p_name;
            this.product_items[index].product_unit_id = unit_id;
            this.product_items[index].unit_code = unit_code;
            this.product_items[index].tp = p_tp;
            this.product_items[index].mrp = p_mrp;

        },

        inputChange()
        {            
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_value = this.totalValue;
            this.obj.commission_value = this.totalCommission;
            this.obj.total_free_amount = this.totalFreeAmount;
            this.obj.total_amount = this.totalAmount;
        },

        quantityChanged( )
        {            

        },

        itemSupplierChange(supplier_id)
        {
            this.item_curr_supplier_id = '';
            if(supplier_id != "") {
                this.item_curr_supplier_id = supplier_id;
                this.product_items.filter((item) => {
                    if(item.supplier_id == ""){
                        item.supplier_id = supplier_id;
                    }
                });
            }
        },
        
        submitForm: function( ) {  
            this.isSubmit = true;
            this.disabled = true;

            const data_item = this.product_items.filter(function(pitem) {
                if((pitem.product_id != "") && (pitem.tp != 0 && pitem.tp != "") && (pitem.order_qty != 0 && pitem.order_qty != "")) {
                    return pitem;
                }
            });

            if(data_item.length > 0) {

                const formData = new FormData();
                formData.append("is_single_po", this.is_single_po);
                formData.append("is_multiple_po", this.is_multiple_po);
                formData.append("supplier_id", this.obj.supplier_id);
                formData.append("supplier_payment_type", this.obj.supplier_payment_type);
                formData.append("number_of_po", this.obj.number_of_po);
                formData.append("supply_schedule", this.obj.supply_schedule);
                formData.append("order_date", this.obj.order_date);
                formData.append("delivery_date", this.obj.delivery_date);
                formData.append("warehouse_id", this.obj.warehouse_id);
                formData.append("outlet_id", this.obj.outlet_id);
                formData.append("reference_no", this.obj.reference_no);
                // formData.append("start_date", this.obj.start_date);
                // formData.append("end_date", this.obj.end_date);
                formData.append("total_quantity", this.obj.total_quantity);
                formData.append("total_value", this.obj.total_value);
                formData.append("commission_value", this.obj.commission_value);
                formData.append("total_vat", this.obj.total_vat);
                formData.append("total_free_amount", this.obj.total_free_amount);
                formData.append("total_amount", this.obj.total_amount);
                formData.append("remarks", this.obj.remarks);
                formData.append("products", JSON.stringify(this.product_items));
                    
                var postEvent = axios.post(this.apiUrl+'/purchase_orders', formData, this.headers);

                postEvent.then(res => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(res.status == 200){
                        this.resetForm();
                        this.productShowBtn = false;
                        this.$toast.success(res.data.message); 
                        window.location.reload();
                    }else{
                        this.$toast.error(res.data.message);
                    }

                }).catch(err => { 
                    this.isSubmit = false; 
                    this.disabled = false;
                    this.$toast.error(err.response.data.message);
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors 
                    }

                });

            }else{
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error("Please fill up product item required data!");
            }
        },

        submitMultiplePOForm: function( ) {  
            this.isFinalMPOSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("is_single_po", this.is_single_po);
            formData.append("is_multiple_po", this.is_multiple_po);
            formData.append("purchase_orders", JSON.stringify(this.purchase_orders));
                  
            var postEvent = axios.post(this.apiUrl+'/purchase_orders', formData, this.headers);

            postEvent.then(res => {
                this.isFinalMPOSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.resetForm();
                    this.productShowBtn = false;
                    this.$toast.success(res.data.message); 
                    window.location.reload();
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isFinalMPOSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }

            });
        },
        
        resetForm() {
            var self = this; //you need this because *this* will refer to Object.keys below`
            //Iterate through each object field, key is name of the object field`
            Object.keys(this.obj).forEach(function(key) {
                self.obj[key] = '';
            });

            this.product_items = [];
        },

        validation: function (...fiels){ 
            var obj = new Object();  
            for (var k in fiels){     // Loop through the object  
                for (var j in this.form){  
                    if((j==fiels[k]) && (!this.form[j])) {  
                        obj[fiels[k]] = fiels[k].replace("_", " ")+' field is required';  // Delete obj[key]; 
                        this.errors = {...this.errors, ...obj};
                    } 
                }              
            }  
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },

        onkeyPress: function(field) { 
            this.checkedRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        

    }, 
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        permission() {
            let pname = this.$route.meta.parent_module;
            let module_name = this.$route.meta.module_name;
            let path_name = this.$route.path; 
            let data = '';
            if(this.$route.meta.parent_module){
                data = this.$store.getters.userAllPermissions[pname][0].children[path_name]
            }else{
                data = this.$store.getters.userAllPermissions[module_name][0].other_actions; 
            } 
            return data;
        },
        totalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                return total + parseFloat(item.order_qty); 
            },0);
        }, 
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.tp * item.order_qty); 
            },0); 
        },
        
        totalCommission: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.tp * item.order_qty);
                // return total + ((item_value * item.disc_percent) / 100); 
                return total + ((item_value * 0) / 100);
            },0); 
        },

        totalFreeAmount: function(){
            return this.product_items.reduce(function(total, item){
                // return total + (item.tp * item.free_qty); 
                return total + (item.tp * 0); 
            },0); 
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.tp * item.order_qty);
                // let item_discount = ((item_value * item.disc_percent) / 100);
                let item_discount = ((item_value * 0) / 100);
                return total + (item_value - item_discount); 
            },0); 
        },
        
        checkQtyValue: function(){ 
            return this.product_items.reduce(function(total, item){
                if((item.checked)){
                    if((item.order_qty > 0)){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return true;
                }
            },true);
        }, 

    }, 
    watch: {
        product_items: {
            handler: function(val) {
                if(val.length > 1){
                    this.disabled = false;
                }else{
                    if(val[0].product_id != "" && (val[0].tp != 0 && val[0].tp != "") && (val[0].order_qty != 0 && val[0].order_qty != "")) {
                        this.disabled = false;
                    }else{
                        this.disabled = true;
                    }
                }
            },
            deep: true
        }
    }, 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 1000px;
}
.modal-content.scrollbar-width-thin.supplier-add-modal {
    border: none !important;
    width: 600px;
}

label {
    display: inline-block;
    margin: 0px 0px 4px 2px;
    float: left;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
    text-align: left;
}

#purchase_order_form {
    padding: 15px;
}

#purchase_order_form .form-control {

}

#reference_no {
    color: red;
}

.total_quantity {
    float: right;
    color: red;
}

.product_table {
    padding: 0;
    min-height: auto;
}

.product_table tbody td input {
    border-bottom: 1px solid #cecece;
}

div.buttons {
    margin-top: 30px;
}

div.buttons .btn-primary {
    margin-top: 0;
}

div.buttons .btn {
    margin-right: 5px;
}

div.buttons .btn:last-child {
    margin-right: 0;
}

.input-group-text {
    height: 40px;
}

/** PO Invoice Design */
.po_invoice {
    border: 1px solid #000 !important;
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