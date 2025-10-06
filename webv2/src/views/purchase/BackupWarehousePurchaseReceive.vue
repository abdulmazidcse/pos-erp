
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Receive</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <!-- <button type="button" class="btn btn-success float-right" style="margin-left: 7px;" @click="toggleImportModal">
                            <i class="fas fa-plus"></i> Bulk Purchase Receive
                        </button> -->
                        <a href="/purchase-receive-list"><button type="button" class="btn btn-primary float-right">
                            Purchase Receive List
                        </button></a> 
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card" v-if="!loading">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mt-2 mb-1">
                                    <div class="form-check">
                                        <input type="checkbox" id="pscan_receive" class="form-check-input" v-model="pscan_receive"> 
                                        <label class="form-check-label" for="pscan_receive" style="font-size: 18px; font-weight: bold;">Receive with scanner</label>
                                    </div>
                                </div>
                            </div>

                                        
                            <div class="col-md-8">
                                <div class="mb-1" v-if="pscan_receive">
                                    <div class="input-group input-group-merge autocomplete">
                                    <input
                                        type="text"
                                        v-model="searchIteam"
                                        class="form-control"
                                        placeholder="Scan/Search product by code"
                                        @keyup="inputChanged"
                                    />
                                    <div class="input-group-text">
                                        <a @click="barcodeReaderModal">
                                        <i class="fas fa-2x fa-camera"></i>
                                        </a>
                                    </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <form id="purchase_order_form" @submit.prevent="submitForm()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Supplier *</label>
                                            <select class="form-control border" id="supplier_id" v-model="obj.supplier_id" @change="onChangeSupplier($event)">
                                                <option value="">--- Select Supplier ---</option>
                                                <option v-for="(supplier, index) in suppliers" :key="index" :value="supplier.id">{{ supplier.name }} 
                                                    <span v-if="supplier.phone">[{{ supplier.phone }}]</span>
                                                </option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.supplier_id">
                                                {{errors.supplier_id[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Purchase Order *</label>
                                            <select class="form-control border" id="purchase_order_id" v-model="obj.purchase_order_id" @change="onchangePurchaseOrder($event)">
                                                <option value="">--- Select ---</option>
                                                <option value="direct">DIRECT</option>
                                                <option v-for="(supplier_order, index) in supplier_orders" :key="index" :value="supplier_order.id">{{ supplier_order.reference_no }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.purchase_order_id">
                                                {{errors.purchase_order_id[0]}}
                                            </div>
                                        </div>

                                        
                                        <div class="form-group col-md-4">
                                            <label for="supplier_payment_type">Receive Date *</label>
                                            <input type="text" class="form-control border" id="purchase_date" @change="onkeyPress('purchase_date')" v-model="obj.purchase_date" readonly>
                                            <div class="invalid-feedback" v-if="errors.purchase_date">
                                                {{errors.purchase_date[0]}}
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="reference_no">Reference No *</label>
                                    <input type="text" class="form-control border" id="reference_no" @keypress="onkeyPress('reference_no')" v-model="obj.reference_no">
                                    <div class="invalid-feedback" v-if="errors.reference_no">
                                        {{errors.reference_no[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="challan_no">Challan No </label>
                                    <input type="text" class="form-control border" id="challan_no" @keypress="onkeyPress('challan_no')" v-model="obj.challan_no">
                                    <div class="invalid-feedback" v-if="errors.challan_no">
                                        {{errors.challan_no[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="warehouse_id" style="width: 100%;">Receive Location *</label>
                                    <select class="form-control border" id="warehouse_id" v-model="obj.warehouse_id" @change="onkeyPress('warehouse_id')" :disabled="warehouse_disabled">
                                        <option value="0">--- Select Location ---</option>
                                        <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.id">{{ warehouse.name }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.warehouse_id">
                                        {{ errors.warehouse_id[0] }}
                                    </div>
                                </div>

                            </div>

                            

                            <div class="row">
                                
                            </div>

                            <!-- Product Details -->
                            <div class="card" style="margin-top: 20px;">
                                <div class="card-header text-left">
                                    Product Details
                                    <input type="hidden" v-model="obj.total_quantity">
                                    <!-- <span class="total_quantity">Challan Total: <b>{{ totalAmount }}</b></span>
                                    <span class="total_quantity" style="margin-right: 15px;">Challan Quantity: <b>{{ totalQuantity }}</b></span> -->
                                </div>
                                <div class="card-body">
                                    <!-- <div class="form-group">
                                        <input type="text" class="form-control border" id="search_product" placeholder="Search Product">
                                    </div> -->
                                    <div class="product_table">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <!-- <th class="text-center" style="width: 5%"><input type="checkbox" v-model="product_all_checked" @change="checkedAll"></th>  -->
                                                    <th class="text-center" style="width: 5%">SL</th> 
                                                    <th class="text-center" style="width: 25%">Name </th> 
                                                    <!-- <th class="text-center">Barcode</th>  -->
                                                    <!-- <th>WH STK</th>
                                                    <th>LAST PO Qty</th>
                                                    <th>LAST PUR. Qty</th>
                                                    <th>PO Qty</th> -->
                                                    <th class="text-center" style="width: 10%">UOM</th>
                                                    <!-- <th>Remain Qty</th> -->
                                                    <!-- <th>CPU</th> -->
                                                    <th class="text-center" style="width: 10%">MRP</th>
                                                    <th class="text-center" style="width: 7%" v-if="obj.purchase_order_id != 'direct'">Ord. Qty</th>
                                                    <!-- <th class="text-center" style="width: 7%">Free Qty</th> -->
                                                    <th class="text-center" style="width: 7%">Rcv. Qty</th>
                                                    <th class="text-center" style="width: 7%">Rcv. Weight</th>
                                                    <th class="text-center" style="width: 10%">CP</th>
                                                    <th class="text-center" style="width: 7%" v-if="obj.purchase_order_id != 'direct'">T.Rcv. Qty</th>
                                                    <!-- <th class="text-center">Free Amount</th> -->
                                                    <!-- <th class="text-center">Disc (%)</th> -->
                                                    <!-- <th class="text-center">Disc Amt</th> -->
                                                    <!-- <th class="text-center">VAT</th> -->
                                                    <th class="text-center" style="width: 10%">Amount</th>
                                                    <!-- <th>Profit(%)CPU</th>
                                                    <th>Profit(%)MRP</th> -->
                                                    <th class="text-center"></th>
                                                    <th class="text-center" style="width: 3%" v-if="obj.purchase_order_id == 'direct'"></th>
                                                </tr>
                                            </thead>

                                            <!-- <tbody v-if="product_items.length > 0"> -->
                                            <tbody v-for="(product_item, i) in product_items" :key="i">
                                                <tr v-if="is_expires != 1"> 
                                                    <!-- <td class="text-center"><input type="checkbox" v-model="product_item.checked"></td> -->
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-center" style="word-wrap: break-word;" v-if="obj.purchase_order_id != 'direct'">{{ product_item.name }}</td>
                                                    <td class="text-center" v-else>
                                                        <Multiselect
                                                            class="form-control border product_id"
                                                            mode="single"
                                                            v-model="product_item.id"
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
                                                    <!-- <td class="text-center">{{ product_item.code }}</td> -->
                                                    <!-- <td>{{ product_item.wh_stk }}</td>
                                                    <td>{{ product_item.last_po_qty }}</td>
                                                    <td>{{ product_item.last_purchase_qty }}</td>
                                                    <td>{{ product_item.po_qty }}</td> -->
                                                    <td class="text-center">
                                                        <select id="punit_id" class="form-control" v-model="product_item.product_unit_id" @change="productUnitChange($event.target.value, i)">
                                                            <option value="">--- Select Unit--</option>
                                                            <option v-for="(unit, i) in units" :key="i" :value="unit.id"> {{ unit.unit_code }}</option>

                                                        </select>
                                                    </td>
                                                    <!-- <td class="text-center">{{ product_item.unit_code }}</td> -->
                                                    <!-- <td>{{ product_item.remain_qty }}</td> -->
                                                    <!-- <td>{{ product_item.cpu }}</td> -->
                                                    
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-if="obj.purchase_order_id != 'direct'" v-model="product_item.sale_price">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-else @focus="addNewRow($event, i)" v-model="product_item.sale_price">
                                                    </td>
                                                    <td class="text-center" v-if="obj.purchase_order_id != 'direct'">{{ product_item.ord_qty }}</td>
                                                    <!-- <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-if="obj.purchase_order_id != 'direct'" v-model="product_item.free_qty">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-else @focus="addNewRow($event, i)" v-model="product_item.free_qty">
                                                    </td> -->
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-if="obj.purchase_order_id != 'direct'" v-model="product_item.rcv_qty">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-else @focus="addNewRow($event, i)" v-model="product_item.rcv_qty">
                                                    </td>                                                    
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-if="obj.purchase_order_id != 'direct'" v-model="product_item.rcv_weight" :readonly="product_item.unit_code != 'kg' ? true : false">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-else @focus="addNewRow($event, i)" v-model="product_item.rcv_weight" :readonly="product_item.unit_code != 'kg' ? true : false">
                                                    </td>             
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()"  v-if="obj.purchase_order_id != 'direct'" v-model="product_item.purchase_price">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-else @focus="addNewRow($event, i)" v-model="product_item.purchase_price">
                                                    </td>                                       
                                                    <td class="text-center" v-if="obj.purchase_order_id != 'direct'">{{ parseFloat(parseFloat(product_item.prcv_qty) + parseFloat(product_item.rcv_qty))>0?parseFloat(parseFloat(product_item.prcv_qty) + parseFloat(product_item.rcv_qty)):parseFloat(product_item.prcv_qty)  }}</td>
                                                    <!-- <td class="text-center">{{ product_item.free_qty * product_item.purchase_price }}</td> -->
                                                    <!-- <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange(product_item, 'percent')" v-model="product_item.disc_percent" style="width: 50px"></td> -->
                                                    <!-- <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange(product_item, 'flat')" v-model="product_item.disc_amount" style="width: 80px"></td> -->
                                                    <!-- <td>{{ (((product_item.rcv_qty * product_item.purchase_price) * product_item.disc_percent) / 100) }}</td> -->
                                                    <!-- <td class="text-center">{{ product_item.vat }}</td> -->
                                                    <!-- <td class="text-center">{{ ((product_item.rcv_qty * product_item.purchase_price) - (((product_item.rcv_qty * product_item.purchase_price) * product_item.disc_percent) / 100)) + product_item.vat }}</td> -->
                                                    <td class="text-center" v-if="product_item.unit_code == 'kg' ">{{ parseFloat((product_item.rcv_weight * product_item.purchase_price) - (((product_item.rcv_weight * product_item.purchase_price) * 0) / 100)).toFixed(2) }}</td>
                                                    <td class="text-center" v-else>{{ parseFloat((product_item.rcv_qty * product_item.purchase_price) - (((product_item.rcv_qty * product_item.purchase_price) * 0) / 100)).toFixed(2) }}</td>

                                                    <!-- <td>{{ product_item.profit_percent_cpu }}</td> -->
                                                    <!-- <td>{{ product_item.profit_percent_mrp }}</td> -->

                                                    <td class="actions-btn">
                                                        <a href="#" v-if="!product_item.is_expirable" class="btn btn-primary btn-sm expireBtn btnDisabled"> Add Expire Date</a>
                                                        <a href="#" v-else class="btn btn-primary btn-sm expireBtn" @click.prevent="addExpireDate(product_item)"> Add Expire Date</a>
                                                        <a href="#" class="btn btn-info btn-sm giftBtn" @click.prevent="addGiftItem(product_item)"> Add Gift</a>
                                                    </td>
                                                    <td class="text-center" v-if="obj.purchase_order_id == 'direct'">
                                                        <a href="javascript:void(0)" class="text-danger" style="font-size: 17px" @click="deleteRow(i)" v-if="i != 0"><i class="mdi mdi-close"></i></a>
                                                    </td>
                                                </tr>

                                                <tr v-if="product_item.is_expires || product_item.is_gifts"> 
                                                    <td :colspan="(obj.purchase_order_id != 'direct') ? 5 : 4">
                                                        <h4 v-if="product_item.is_expires">Expired Date Add</h4>
                                                        <table style="width: 100%">
                                                            <tr v-for="(expire_item, e) in product_item.expires_data" :key="e">
                                                                <td style="width: 50%"><input type="date" class="form-control" v-model="expire_item.expire_date"></td>
                                                                <td><input type="number" step="any" class="form-control" v-model="expire_item.expire_qty" @keyup="expireQtyValidation(product_item, e)"></td>
                                                                <td><button type="button" class="btn btn-danger btn-sm" @click.prevent="deleteExpires(product_item, e)">Delete Expires</button></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td :colspan="(obj.purchase_order_id != 'direct') ? 6 : 6">
                                                        <h4 v-if="product_item.is_gifts">Gift Item Add</h4>
                                                        <table style="width: 100%">
                                                            <tr v-for="(gift_item, g) in product_item.gifts" :key="g">
                                                                <td style="width: 70%"><input type="text" class="form-control" v-model="gift_item.gift_name" placeholder="Gift Item Name"></td>
                                                                <td style="width: 20%"><input type="number" step="any" class="form-control" v-model="gift_item.gift_qty"></td>
                                                                <td><button type="button" class="btn btn-danger btn-sm" @click.prevent="deleteGift(product_item, g)">Delete Gift</button></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                            </tbody>
                                            
                                        </table>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="total_value">Total Value</label>
                                            <input type="text" class="form-control border" id="total_value" v-model="order_data.total_value" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="commission_value">Commission Value</label>
                                            <input type="text" class="form-control border" id="comission_value" v-model="order_data.commission_value" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_free_amount">Total Free Amount</label> 
                                            <input type="text" class="form-control border" id="total_free_amount" v-model="order_data.total_free_amount" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="total_vat">Total VAT</label>
                                            <input type="text" class="form-control border" id="total_vat" v-model="order_data.total_vat" readonly>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_amount">Sub Total </label>
                                            <input type="text" class="form-control border" id="total_amount" v-model="order_data.total_amount" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="total_vat">Additional Discount *</label>
                                            <input type="text" class="form-control border" id="total_vat" v-model="order_data.additional_discount">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_amount">Additional Cost *</label>
                                            <input type="text" class="form-control border" id="total_amount" v-model="order_data.additional_cost">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="total_amount">Net Amount *</label>
                                            <input type="text" class="form-control border" id="total_amount" v-model="order_data.net_amount" readonly>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="buttons">
                                <button type="submit" class="btn btn-primary " :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> SUBMIT 
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <!-- <button type="button" class="btn btn-info" :disabled="disabled"> HOLD</button> -->
                                <!-- <button type="button" class="btn btn-danger" :disabled="disabled"> PREVIEW</button> -->
                            </div>

                            
                        </form>
                        
                    </div>



                </div>

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
            </div>
        </div>

        <!--Bulk Import Purchase Receive Modal -->
        <!-- <Modal @close="toggleImportModal" :modalActive="importModal">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleImportModal" type="button" class="btn btn-default">X</button>
                    <h5 style="text-align: right">Import Purchase Receive</h5>
                </div>

                <div class="modal-body">  
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" @submit.prevent="submitImportForm()" enctype="multipart/form-data">
                                <p style="font-size: 13px; font-style: italic;">The field labels marked with * are required input fields.</p>
                                <p style="font-size: 16px;">The correct column order is (outlet_name*, supplier_name*, product_name*, expiry_date, order_qty*, free_qty, receive_qty*, tp*, mrp*) and you must follow this.</p>
                                <p style="font-size: 16px;">Outlet, Supplier, and Product Must be enlisted Before Purchase Receive (if don't have)</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="excel_file"> Upload EXCEL File *</label>
                                            <input type="file" class="form-control" id="excel_file" ref="file" name="..." @change="purchaseReceiveImportFile(), onkeyPress('excel_file')">
                                            
                                            <div class="invalid-feedback" v-if="errors">
                                                <p v-for="(error, i) in errors" :key="i">{{ error[0] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label> Sample File</label>
                                            <a :href="samplefile_url" class="btn btn-info" style="display: block; width: 100%; clear:both;" download><i class="fas fa-download"></i> Download</a>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary" :disabled="disabled_upload">
                                        <span v-show="isUploadSubmit">
                                            <i class="fas fa-spinner fa-spin" ></i>
                                        </span>Submit 
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Modal> -->

        <Modal
        @close="barcodeReaderModal()"
        :modalActive="barcodeReaderModalActive"
        >
            <div class="modal-content scrollbar-width-thin barcodeReaderModal">
                <div class="modal-header">
                <h5>Barcode Reader</h5>
                <button
                    @click="barcodeReaderModal()"
                    type="button"
                    class="btn btn-default"
                >
                    X
                </button>
                </div>
                <div class="modal-body">
                    <!-- <span style="font-size: x-large" v-if="!libLoaded">Loading Library...</span> -->
                
                <div id="videoWindow" class="video"></div>
                <!-- <QuaggaScannerTest v-on:found="found" v-if="renderBarcodeReader"> </QuaggaScannerTest> -->
                
                </div>

                <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row button-list pt-2">
                    <div class="col-md-4" style="padding: 0 5px">
                        <div class="d-grid">
                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            @click="barcodeReaderModal()"
                        >
                            Close
                        </button>
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal"; 
import Form from 'vform'
import axios from 'axios'; 
import {
  ref,
  reactive,
  toRefs,
  computed,
  inject,
  onMounted,
  getCurrentInstance,
  onUpdated,
  onUnmounted,
  onBeforeMount,
  onBeforeUnmount,
  onBeforeUpdate,
} from "vue";

// import Quagga from 'quagga'; 
 
export default {
    name: 'Purchase Receive',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            isUploadSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            disabled_upload: false,
            modalActive:false,
            barcodeReaderModalActive:false,
            pscan_receive: false,
            importModal:false,
            errors: {},
            btn:'Create',
            productShowBtn: false, 
            auth_user: "",
            auth_user_roles: [],
            suppliers: [],
            supplier_orders: [],
            warehouses: [],
            warehouse_disabled: false,
            units: [],
            supplier:'',
            is_expires:false,
            products: [],
            products_data: [],
            product_items: [],
            obj: {
                supplier_id: '',
                purchase_order_id: '',
                purchase_date: new Date().toISOString().slice(0,10),
                warehouse_id: '',
                reference_no: '',
                challan_no: ''
            },
            order_data: new Form({
                total_quantity: '',
                total_value: '',
                commission_value: '',
                total_vat: '',
                total_free_amount: '',
                total_amount: '', 
                additional_discount: 0,
                additional_cost: 0,
                net_amount: ''
            }),
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            product_all_checked: false,
            importFile: '',
            samplefile_url: this.baseUrl+'/import_excel_demo/purchase_receive.xlsx',

            barcoderRrenderCounter: 0,
            renderBarcodeReader: false,
            searchIteam: '',
            
        };
    },
    created() {
        this.fetchAuthUser();
        this.fetchReferenceNo();
        this.fetchSuppliers();
        this.fetchUnits();
        this.fetchWarehouses();
        this.fetchProducts();

    },
    methods: {
        toggleImportModal: function() {
            this.importModal = !this.importModal;
            this.importFile = '';
            this.$refs.file.value=null;
            this.disabled_upload = true;
            this.errors = '';
        },

        barcodeReaderModal: function() {
            this.barcodeReaderModalActive = !this.barcodeReaderModalActive;
            if(this.barcodeReaderModalActive) {
                // this.startRead();
            }else{
                // this.stopRead();
            }
        },

        // Barcode Reader 
        startRead() {
            // if(this.barcodeReaderModalActive) {
            // Quagga.init({
            //     inputStream : {
            //     name : "Live",
            //     type : "LiveStream",
            //     target: document.querySelector('#videoWindow')    // Or '#yourElement' (optional)
            //     },
            //     decoder : {
            //     // readers : ["code_128_reader"]
            //     readers : ["ean_reader", "code_128_reader"],
            //     // multiple: true,
            //     }
            // }, function(err) {
            //     if (err) {
            //         console.log(err);
            //         return
            //     }
                
            //     console.log("Initialization finished. Ready to start");
            //     Quagga.start();
            // });
            // }else{
            //     Quagga.stop();
            // }
            // this.barcoderRrenderCounter++;
            // this.renderBarcodeReader = true;

        },

        stopRead() {
            console.log("this.barcoderRrenderCounter", this.barcoderRrenderCounter);
            for(var i = 0; i<=this.barcoderRrenderCounter; i++) {
                Quagga.stop(); 
            }
            this.barcoderRrenderCounter = 0;
            this.renderBarcodeReader = false;
        }, 

        foundBarcode(result) {
            if (result) {
                this.searchIteam = result.code;
                this.barcodeReaderModalActive = false;
                this.renderBarcodeReader = false;
                this.stopRead();
                this.inputChanged();
            }
        },

        inputChanged(event) {
            setTimeout(() => {

                if(this.searchIteam != "") {

                    var checkNumber = this.searchIteam.toLowerCase();
                    var first2digit = String(checkNumber).slice(0, 2);
                    var weightFigure = "";
                    var product_weight = "";
                    var item_code = "";
                    if (Number(first2digit) == Number(99)) {

                        item_code = String(checkNumber).slice(2, 7);
                        weightFigure = String(checkNumber).slice(-6, -1);
                        product_weight = parseFloat(Number(weightFigure) / 1000).toFixed(3);
                    }

                    
                    var filtered = this.products_data.find(({product_code}) => product_code == item_code);
                    
                    if(filtered) {

                        var p_tp = (filtered) ? filtered.cost_price : 0;
                        var p_mrp = (filtered) ? filtered.mrp_price : 0;
                        var p_code = (filtered) ? filtered.product_code : '';
                        var p_name = (filtered) ? filtered.product_name : '';
                        var unit_id = (filtered) ? filtered.purchase_measuring_unit : '';
                        var expireable = (filtered) ? filtered.is_expirable : 0;
                        var unit_data = this.units.find(({id}) => id == unit_id);

                        var single_item = {
                                id: filtered.id,
                                name: p_name,
                                code: p_code,
                                unit_code: unit_data.unit_code.toLowerCase(),
                                product_unit_id: unit_id,
                                purchase_price: p_tp,
                                sale_price: p_mrp,
                                ord_qty: 0,
                                rcv_qty: 1,
                                rcv_weight: product_weight,
                                free_qty: 0,
                                free_amount: 0,
                                disc_percent: 0,
                                disc_amount: 0,
                                amount: (product_weight * p_tp),
                                is_expires: false,
                                expires_data: [],
                                is_gifts: false,
                                gifts: [],
                                is_expirable: expireable,
                            }

                        var exists = this.product_items.find(({id}) => id == single_item.id);

                        if(exists) {
                            var index = this.product_items.findIndex((item) => item.id == single_item.id);
                            this.product_items[index].rcv_qty = this.product_items[index].rcv_qty + single_item.rcv_qty;
                            this.product_items[index].rcv_weight = parseFloat(parseFloat(this.product_items[index].rcv_weight) + parseFloat(single_item.rcv_weight)).toFixed(3);

                        }else{
                            this.product_items.push(single_item);
                        }

                        
                    }else{
                        this.$toast.error("This product not found!");
                    }
                    
                    this.order_data.total_quantity = this.totalQuantity.toFixed(2);
                    this.order_data.total_value = this.totalValue.toFixed(2);
                    this.order_data.commission_value = this.totalCommission.toFixed(2);
                    this.order_data.total_free_amount = this.totalFreeAmount.toFixed(2);
                    this.order_data.total_amount = this.totalAmount.toFixed(2);
                    this.order_data.net_amount = this.netAmount.toFixed(2);
                    this.searchIteam = '';
                }

            }, 500);
            
        },

        // Authentication User 
        fetchAuthUser() {
            axios.get(this.apiUrl+'/users/authUser', this.headerjson)
            .then((res) => {
                console.log("auth user", res.data.data);
                this.auth_user = res.data.data.user; 
                this.auth_user_roles = res.data.data.user_roles; 
                if(res.data.data.user_roles[0].slug != "warehouse-manager") {
                    this.warehouse_disabled = false;
                    this.obj.warehouse_id  = res.data.data.user.warehouse_id;
                }else{
                    this.warehouse_disabled = true;
                    this.obj.warehouse_id  = res.data.data.user.warehouse_id;
                }
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },
        // Reference Number 
        fetchReferenceNo() {
            axios.get(this.apiUrl+'/warehouse_purchase_receives/getReferenceNo', this.headerjson)
            .then((res) => {
                this.obj.reference_no = res.data.data.reference_no; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headerjson)
            .then((res) => {
                this.suppliers = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        // Units Data
        fetchUnits() {
            axios.get(this.apiUrl+'/units', this.headerjson)
            .then((res) => {
                this.units = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        onChangeSupplier(event){
            if(!this.pscan_receive) {

                this.product_items = [];
                var supplier_id = event.target.value;
                this.obj.purchase_order_id = '';
                if(supplier_id != '') {
                    axios.post(this.apiUrl+'/warehouse_purchase_receives/getPurchaseOrder', {'supplier_id': supplier_id}, this.headerjson )
                    .then((res) => {
                        this.supplier_orders = res.data.data;
                    })
                    .catch((err) => { 
                        this.$toast.error(err.response.data.message);
                    });
                }else{
                    this.supplier_orders = [];
                    this.obj.purchase_order_id = '';
                }

            }
            
            
        },

        // Outlets Data
        fetchWarehouses() {
            axios.get(this.apiUrl+'/warehouses', this.headerjson)
            .then((res) => {
                this.warehouses = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
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
                this.$toast.error(err.response.data.message);
            })
            .finally(() => {
                this.loading = false;
            });
        },

        onchangePurchaseOrder(event){
            this.product_items = [];
            var purchase_order_id = event.target.value;
            if(purchase_order_id != '') {
                axios.post(this.apiUrl+'/purchase_receives/purchaseOrderProducts', {'purchase_order_id': purchase_order_id}, this.headerjson )
                .then((res) => {
                    this.order_data.fill(res.data.data.purchase_order);
                    this.product_items  = res.data.data.purchase_products;   
                })
                .catch((err) => { 
                    this.$toast.error(err.response.data.message);
                })
            }else{
                this.order_data.reset();
                this.product_items  = [];
            }
        },
        
        // New Product Row Add
        addNewRow(value, index, change_field="") {
            var item_length = this.product_items.length;
            if(index == (item_length - 1)) {

                this.product_items.push(
                    {
                        // outlet_id: '',
                        id: '',
                        name: '',
                        code: '',
                        unit_code: '',
                        product_unit_id: '',
                        purchase_price: 0,
                        sale_price: 0,
                        ord_qty: 0,
                        rcv_qty: 0,
                        rcv_weight: 0,
                        free_qty: 0,
                        free_amount: 0,
                        disc_percent: 0,
                        disc_amount: 0,
                        amount: 0,
                        is_expires: false,
                        expires_data: [],
                        is_gifts: false,
                        gifts: [],
                        is_expirable: false,
                    }
                );
            }
        },

        deleteRow(index) {
            this.product_items.splice(index, 1);
        },

        onChangeProduct(product_id, index) {
            const product = this.products_data.find(({id}) => id == product_id);

            let p_tp = (product) ? product.cost_price : 0;
            let p_mrp = (product) ? product.mrp_price : 0;
            let p_code = (product) ? product.product_code : '';
            let p_name = (product) ? product.product_name : '';
            let unit_id = (product) ? product.purchase_measuring_unit : '';
            let sub_category_id = (product) ? product.sub_category_id : 0;
            let expireable = (product) ? product.is_expirable : 0;

            let unit_data = this.units.find(({id}) => id == unit_id);
            // let unit_code = 'pcs';
            // if(unit_id !='') {
            //     axios.get(this.apiUrl+'/units/'+unit_id, this.headerjson)
            //     .then((res) => {
            //         unit_code = res.data.data.unit_code;
            //     })
            //     .catch((err) => { 
            //         this.$toast.error(err.response.data.message);
            //     });
            // }

            this.product_items[index].code = p_code;
            this.product_items[index].name = p_name;
            this.product_items[index].product_unit_id = unit_id;
            this.product_items[index].sub_category_id = sub_category_id;
            this.product_items[index].unit_code = unit_data.unit_code.toLowerCase();
            this.product_items[index].purchase_price = p_tp;
            this.product_items[index].sale_price = p_mrp;
            this.product_items[index].is_expirable = (expireable == 1) ? true : false;


        },

        productUnitChange(unit_id, index) 
        {
            this.product_items[index].rcv_weight = 0;
            this.product_items[index].rcv_qty = 0;

            if(unit_id != '') {
                var unit_data = this.units.find(({id}) => id == unit_id);

                this.product_items[index].unit_code = unit_data.unit_code.toLowerCase();

            }else{
                var product_id = this.product_items[index].id;
                const product = this.products_data.find(({id}) => id == product_id);
                var punit_id = (product) ? product.purchase_measuring_unit : '';
                var unit_data = this.units.find(({id}) => id == punit_id);

                this.product_items[index].product_unit_id = punit_id;
                this.product_items[index].unit_code = unit_data.unit_code.toLowerCase();

            }


        },

        inputChange(product_item='', type='')
        {
            this.order_data.total_quantity = this.totalQuantity.toFixed(2);
            this.order_data.total_value = this.totalValue.toFixed(2);
            this.order_data.commission_value = this.totalCommission.toFixed(2);
            this.order_data.total_free_amount = this.totalFreeAmount.toFixed(2);
            this.order_data.total_amount = this.totalAmount.toFixed(2);
            this.order_data.net_amount = this.netAmount.toFixed(2);

            if(type == 'percent') {
                product_item.disc_amount = parseFloat(((product_item.rcv_qty * product_item.purchase_price) * product_item.disc_percent) / 100);
                // product_item.disc_percent = (((product_item.rcv_qty * product_item.purchase_price) - product_item.amount) / 1000);
            }
            else if(type == 'flat'){
                let orginal_price = (product_item.rcv_qty * product_item.purchase_price);
                let discount_price = (orginal_price - product_item.disc_amount);
                let percent = ((100 * (orginal_price - discount_price)) / orginal_price);
                product_item.disc_percent = percent;

            }
        },

        addExpireDate(product) 
        {
            if(!product.is_expires) {
                product.is_expires = true;
            }

            var expire_item = {expire_date:'', expire_qty: 0};
            product.expires_data.push(expire_item);
        },

        deleteExpires(product, index){
            if(index > -1) {
                product.expires_data.splice(index, 1);
            }

            if(product.expires_data.length == 0) {
                product.is_expires = false;
            }
        },
        expireQtyValidation: function(product, e) {
            var counter = 0;
            product.expires_data.filter(item => {
                if(item.expire_qty != '') {
                    counter += parseInt(item.expire_qty); 
                }
            });

            if(counter > product.rcv_qty) {
                this.$toast.error("Expired quantity can't greater than receive quantity!");
                if(product.expires_data[e].expire_qty != ''){
                    product.expires_data[e].expire_qty = 0;
                }
            }

        },

        addGiftItem(product) 
        {
            if(!product.is_gifts) {
                product.is_gifts = true;
            }
            var gift_item = {gift_name:'', gift_qty: 0};
            product.gifts.push(gift_item);
        },

        deleteGift(product, index){
            if(index > -1) {
                product.gifts.splice(index, 1);
            }

            if(product.gifts.length == 0) {
                product.is_gifts = false;
            }
        },

        // purchaseReceiveImportFile() {
        //     this.importFile = this.$refs.file.files[0];
        //     if(this.importFile != '') {
        //         this.errors = ''
        //     }
        // },

        // checkImportRequiredPrimary() {
        //     if(this.$refs.file.value != "") {
        //         this.disabled_upload = false;
        //     }else{
        //         this.disabled_upload = true;
        //     }
        // },

        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("supplier_id", this.obj.supplier_id);
            formData.append("purchase_order_id", this.obj.purchase_order_id);
            formData.append("purchase_date", this.obj.purchase_date);
            formData.append("warehouse_id", this.obj.warehouse_id);
            formData.append("reference_no", this.obj.reference_no);
            formData.append("challan_no", this.obj.challan_no);
            formData.append("total_quantity", this.order_data.total_quantity);
            formData.append("total_value", this.order_data.total_value);
            formData.append("commission_value", this.order_data.commission_value);
            formData.append("total_vat", this.order_data.total_vat);
            formData.append("total_free_amount", this.order_data.total_free_amount);
            formData.append("total_amount", this.order_data.total_amount);
            formData.append("additional_discount", this.order_data.additional_discount);
            formData.append("additional_cost", this.order_data.additional_cost);
            formData.append("net_amount", this.order_data.net_amount);
            formData.append("products", JSON.stringify(this.product_items));

            this.$swal({
              title: "Are you sure confirm this purchase!",
              confirmButtonCategory: "#3085d6",
              cancelButtonCategory: "#d33",
              showCancelButton: true,
              confirmButtonText: "Ok!",
            }).then((result) => {
                if(result.isConfirmed) {
                    var postEvent = axios.post(this.apiUrl+'/warehouse_purchase_receives', formData, this.headers);

                    postEvent.then(res => {
                        this.isSubmit = false;
                        this.disabled = false;
                        if(res.status == 200){
                            this.resetForm();
                            //this.order_data.reset();
                            this.$toast.success(res.data.message); 
                            //window.location.reload();
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
                    return false;

                }

            }).finally((res) => {
                this.isSubmit = false;
                this.disabled = false;
            })
                      
            
        },
        

        resetForm() {
            var self = this; //you need this because *this* will refer to Object.keys below`
            //Iterate through each object field, key is name of the object field`
            Object.keys(this.obj).forEach(function(key,index) {
                self.obj[key] = '';
            });
            this.product_items = [];
        },

        validation: function (...fiels){ 
            var obj = new Object(); 
            var validate = false;
            for (var k in fiels){     // Loop through the object  
                for (var j in this.form){  
                    if((j==fiels[k]) && (!this.form[j])) {  
                        obj[fiels[k]] = fiels[k].replace("_", " ")+' field is required';  // Delete obj[key]; 
                        this.errors = {...this.errors, ...obj};
                    }else{
                        validate = false;
                    }
                }              
            }  
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },

        onkeyPress: function(field) { 
            // this.checkImportRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        
    },
    destroyed() {},
    mounted() {
        console.log("before update");
        window.scrollTo(0, 0);
        Quagga.onDetected((data) => {
            // console.log(data);
            if(data.codeResult) {
                this.foundBarcode(data.codeResult);
            }else{
                this.$toast.error("Something went wrong, please try again!");
            }

        });

        // For Shortcut Key work
        document.addEventListener("keydown", e => { 
            
            if(e.altKey && e.code== 'Enter') {
                this.submitForm()
            }
        }); 
    },
    
    computed: {
        totalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                return total + parseFloat(item.rcv_qty); 
            },0);
        }, 

        totalWeight: function(){ 
            return this.product_items.reduce(function(total, item){
                return total + parseFloat(item.rcv_weight); 
            },0);
        }, 

        // totalRcvQuantity: function(number1, number2){ 
        //         var rcv_qty = (number2 != "") ? number2 : 0;
        //         var test = number1 + number2;
        //         return test;
        // }, 
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                if((item.rcv_qty != 0 && item.rcv_qty > 0) && (item.rcv_weight != 0 && item.rcv_weight > 0)) {
                    return total + (item.purchase_price * item.rcv_weight); 
                }
                else if(item.rcv_qty != 0 && item.rcv_qty > 0) {
                    return total + (item.purchase_price * item.rcv_qty); 
                }
                else{
                    return total + (item.purchase_price * item.rcv_weight); 
                }

            },0); 
        },
        
        totalCommission: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.purchase_price * item.rcv_qty);
                // return total + parseFloat((item_value * item.disc_percent) / 100); 
                return total + parseFloat(item.disc_amount); 
            },0); 
        },

        totalFreeAmount: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.purchase_price * item.free_qty); 
            },0); 
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                if((item.rcv_qty != 0 && item.rcv_qty > 0) && (item.rcv_weight != 0 && item.rcv_weight > 0)) {
                    var item_value = (item.purchase_price * item.rcv_weight);
                }
                else if(item.rcv_qty != 0 && item.rcv_qty > 0) {
                    item_value = (item.purchase_price * item.rcv_qty);
                }
                else {
                    item_value = (item.purchase_price * item.rcv_weight);
                }

                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0); 
        },
        
        netAmount: function(){
            var net_amount = this.product_items.reduce(function(total, item){
                if((item.rcv_qty != 0 && item.rcv_qty > 0) && (item.rcv_weight != 0 && item.rcv_weight > 0)) {
                    var item_value = (item.purchase_price * item.rcv_weight);
                }
                else if(item.rcv_qty != 0 && item.rcv_qty > 0) {
                    item_value = (item.purchase_price * item.rcv_qty);
                }
                else {
                    item_value = (item.purchase_price * item.rcv_weight);
                }

                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0); 

            var total_amount = (net_amount - this.order_data.additional_discount) + this.order_data.additional_cost;

            return total_amount;
        },
        
        checkQtyValue: function(){ 
            return this.product_items.reduce(function(total, item){
                console.log("item", item);
                if((item.checked)){
                    if((item.qty > 0)){
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
            handler: function(val, oldVal) {
                if(val.length > 0){
                    this.disabled = false;
                }else{
                    this.disabled = true;
                }
            },
            deep: true
        },

        pscan_receive:  {
            handler: function(val, oldVal) {
                if(val){
                    this.obj.purchase_order_id = 'direct';
                }else{
                    this.obj.purchase_order_id = '';
                }
            },
            deep: true
        },

        'order_data.additional_discount'(newVal, oldVal){
            var net_amount = this.product_items.reduce(function(total, item){
                let item_value = (item.purchase_price * item.rcv_qty);
                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0);
            this.order_data.net_amount = parseFloat((net_amount - parseFloat(newVal)) + parseFloat(this.order_data.additional_cost)) ;
        },

        'order_data.additional_cost'(newVal, oldVal){
            var net_amount = this.product_items.reduce(function(total, item){
                let item_value = (item.purchase_price * item.rcv_qty);
                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0);
            this.order_data.net_amount = parseFloat((net_amount - parseFloat(this.order_data.additional_discount)) + parseFloat(newVal));
        },

    }, 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 100%;
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

.actions-btn a{
    display: inline-block;
    margin-bottom: 5px;
    font-size: 10px;
    border-radius: 20px;
    width: 80px;
    padding: 3px 2px;
}

.actions-btn a:first-child {
    margin-right: 3px;
}

.btnDisabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>