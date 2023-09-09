
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Return </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Return</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="redirectRoute()">
                            Purchase Return List
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

                            <!-- <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="spo" name="po_type" class="form-check-input" value="spo" checked @change="poGenerateType($event)">
                                        <label class="form-check-label" for="spo" style="font-size: 18px; font-weight: bold;">Single PO</label>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="mpo" name="po_type" class="form-check-input" value="mpo" @change="poGenerateType($event)"> 
                                        <label class="form-check-label" for="mpo" style="font-size: 18px; font-weight: bold;">Multiple PO</label>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <hr>
                            </div> -->

                            <div class="row mb-3">
                                <div class="form-group col-md-3">
                                    <label for="return_date">Date *</label>
                                    <input type="text" class="form-control border" id="return_date" @change="onkeyPress('return_date')" v-model="obj.return_date" readonly>
                                    <div class="invalid-feedback" v-if="errors.return_date">
                                        {{errors.return_date[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="outlet_id" style="margin-bottom: 5px;">Outlet *</label> <br>
                                    <Multiselect
                                        class="form-control border outlet_id"
                                        mode="single"
                                        v-model="obj.outlet_id"
                                        placeholder="Select Outlet"
                                        @change="onChangeOutlet($event), onkeyPress('outlet_id')"
                                        :searchable="true"
                                        :filter-results="true"
                                        :options="outlet_options"
                                        :classes="multiclasses"
                                        :close-on-select="true"
                                        :min-chars="1"
                                        :resolve-on-load="false"
                                        :disabled="outletDisabled"
                                    />
                                    <div class="invalid-feedback" v-if="errors.outlet_id">
                                        {{errors.outlet_id[0]}}
                                    </div>
                                </div>

                                <!-- <div class="form-group col-md-3">
                                    <label for="invoice_no">Purchase Invoice No *</label>
                                    <select class="form-control border" id="invoice_no" v-model="obj.invoice_no"  @change="onChangeInvoice($event), onkeyPress('invoice_no')"> -->
                                    <!-- <select class="form-control border" id="supplier_id" v-model="obj.supplier_id"  @change="onkeyPress('supplier_id')"> -->
                                        <!-- <option value="">--- Select Purchase Invoice ---</option>
                                        <option v-for="(pr_receive, index) in purchase_receives" :key="index" :value="pr_receive.id">{{ pr_receive.reference_no }} [{{ 'PR Date: ' + pr_receive.purchase_date }}]</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.supplier_id">
                                        {{errors.supplier_id[0]}}
                                    </div>
                                </div> -->

                                <div class="form-group col-md-6">
                                    <label for="item_id">Item *</label> <br>
                                    <!-- <select class="form-control border" id="item_id" v-model="obj.item_id" @change="onChangeItem($event), onkeyPress('item_id')">
                                        <option value="">--- Select Item ---</option>
                                        <option v-for="(purchase_item, i) in purchase_receive_items" :key="i" :value="purchase_item.receive_id">{{ purchase_item.product_name }}</option>
                                    </select> -->
                                    <Multiselect
                                        class="form-control border item_id"
                                        mode="single"
                                        v-model="obj.item_id"
                                        placeholder="Select Item"
                                        @change="onChangeItem($event), onkeyPress('item_id')"
                                        :searchable="true"
                                        :filter-results="true"
                                        :options="purchase_receive_items"
                                        :classes="multiclasses"
                                        :close-on-select="true"
                                        :min-chars="1"
                                        :resolve-on-load="false"
                                    />
                                    <div class="invalid-feedback" v-if="errors.item_id">
                                        {{errors.item_id[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="supplier_id" style="margin-bottom: 5px;">Supplier *</label> <br>
                                    <!-- <select class="form-control border" id="supplier_id" v-model="obj.supplier_id"  @change="onChangeSupplier($event), onkeyPress('supplier_id')"> -->
                                    <!-- <select class="form-control border" id="supplier_id" v-model="obj.supplier_id"  @change="onkeyPress('supplier_id')"> -->
                                        <!-- <option value="">--- Select Supplier ---</option>
                                        <option v-for="(supplier, index) in suppliers" :key="index" :value="supplier.id">{{ supplier.name }} [{{ supplier.phone }}]</option>
                                    </select> -->
                                    <Multiselect
                                        class="form-control border supplier_id"
                                        mode="single"
                                        v-model="obj.supplier_id"
                                        placeholder="Select Supplier"
                                        @change="onChangeSupplier($event), onkeyPress('supplier_id')"
                                        :searchable="true"
                                        :filter-results="true"
                                        :options="supplier_options"
                                        :classes="multiclasses"
                                        :close-on-select="true"
                                        :min-chars="1"
                                        :resolve-on-load="false"
                                    />
                                    <div class="invalid-feedback" v-if="errors.supplier_id">
                                        {{errors.supplier_id[0]}}
                                    </div>
                                </div>

                                <!-- <div class="form-group col-md-3">
                                    <label for="purchase_qty">Last Purchase Quantity</label>
                                    <input type="text" class="form-control border" id="purchase_qty" @change="onkeyPress('purchase_qty')" v-model="obj.purchase_qty" readonly>
                                    <div class="invalid-feedback" v-if="errors.purchase_qty">
                                        {{errors.purchase_qty[0]}}
                                    </div>
                                </div> -->

                                <div class="form-group col-md-3">
                                    <label for="stock_quantity">Stock Quantity</label>
                                    <input type="text" class="form-control border" id="stock_quantity" @change="onkeyPress('stock_quantity')" v-model="obj.stock_quantity" readonly>
                                    <div class="invalid-feedback" v-if="errors.stock_quantity">
                                        {{errors.stock_quantity[0]}}
                                    </div>
                                </div>


                                <div class="form-group col-md-3">
                                    <label for="expire_date">Expire Date</label>
                                    <input type="text" class="form-control border" id="expire_date" @keyup="onkeyPress('expire_date')" v-model="obj.expire_date" readonly>
                                    <div class="invalid-feedback" v-if="errors.expire_date">
                                        {{errors.expire_date[0]}}
                                    </div>
                                </div>

                                <!-- <div class="col-md-6" v-if="(single_item != '' && single_item.is_expirable == true)">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="expire_id">Expired Date *</label>
                                            <select class="form-control border" id="expire_id" v-model="obj.expire_id" @change="onChangeExpireDate($event), onkeyPress('expire_id')">
                                                <option value="">--- Select Date ---</option>
                                                <option v-for="(ex_data, i) in itemExpiresData" :key="i" :value="ex_data.id">{{ ex_data.expire_date }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.expire_id">
                                                {{errors.expire_id[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="return_qty">Expire Date Qty *</label>
                                            <input type="text" class="form-control border" id="expire_qty" @keyup="onkeyPress('expire_qty')" v-model="obj.expire_qty" readonly>
                                            <div class="invalid-feedback" v-if="errors.expire_qty">
                                                {{errors.expire_qty[0]}}
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="form-group col-md-3">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="text" class="form-control border" id="unit_price" @keyup="onkeyPress('unit_price')" v-model="obj.unit_price" readonly>
                                    <div class="invalid-feedback" v-if="errors.unit_price">
                                        {{errors.unit_price[0]}}
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="return_unit_price">Return Unit Price</label>
                                    <input type="text" class="form-control border" id="return_unit_price" @keyup="totalReturnAmount(), onkeyPress('return_unit_price')" v-model="obj.return_unit_price">
                                    <div class="invalid-feedback" v-if="errors.return_unit_price">
                                        {{errors.return_unit_price[0]}}
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-3">
                                    <label for="return_qty">Return Quantiy *</label>
                                    <input type="text" class="form-control border" id="return_qty" @keyup="totalReturnAmount(), onkeyPress('return_qty')" v-model="obj.return_qty">
                                    <div class="invalid-feedback" v-if="errors.return_qty">
                                        {{errors.return_qty[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="total_return_amount">Total Return Amount</label>
                                    <input type="text" class="form-control border" id="total_return_amount" @keyup="onkeyPress('total_return_amount')" v-model="obj.total_return_amount" readonly>
                                    <div class="invalid-feedback" v-if="errors.total_return_amount">
                                        {{errors.total_return_amount[0]}}
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="note">Reason</label>
                                    <select id="note" class="form-control border" @change="onkeyPress('note')" v-model="obj.note">
                                        <option value="">--- Select Reason ---</option>
                                        <option>Damage Product</option>
                                        <option>Expired Product</option>
                                        <option>Slow Moving Product</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.note">
                                        {{errors.note[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <button class="btn btn-info addBtn" :disabled="itemAddEnabled" style="margin-top: 20px;" @click="addReturnItem()"><i class="mdi mdi-plus-outline"></i> ADD</button>
                                </div>

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
                                                    <th class="text-center" style="width: 5%">SL </th> 
                                                    <th class="text-left" style="width: 30%">Name </th> 
                                                    <!-- <th class="text-center" style="width: 7%">UOM</th> -->
                                                    <th class="text-center" style="width: 10%">Expire Date</th>
                                                    <!-- <th class="text-center" style="width: 10%">Pur. Qty</th> -->
                                                    <th class="text-center" style="width: 10%">Rtn. Qty</th>
                                                    <th class="text-center" style="width: 10%">CPU</th>
                                                    <th class="text-center" style="width: 10%">Amount</th>
                                                    <th class="text-center" style="width: 10%">Reason</th>
                                                    <th class="text-center" style="width: 3%"></th>
                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <!-- <td class="text-center"><input type="checkbox" v-model="product_item.checked"></td> -->
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-left">
                                                        {{ product_item.product_name }}
                                                    </td>
                                                    <!-- <td class="text-center">{{ product_item.unit_code }}</td> -->
                                                    <!-- <td class="text-center">{{ product_item.carton_size }}</td> -->
                                                    <td class="text-center">{{ product_item.expire_date }}</td>
                                                    <!-- <td class="text-center">{{ product_item.purchase_qty }}</td> -->
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange(), quantityChanged(i, $event.target.value)" v-model="product_item.return_qty">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.tp">
                                                    </td>
                                                    <td class="text-center">{{ product_item.return_qty * product_item.tp }}</td>
                                                    <td class="text-center" style="width: 150px;">
                                                        <textarea cols="30" rows="2" v-model="product_item.note"></textarea>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="text-danger" style="font-size: 17px" @click="deleteRow(i)"><i class="mdi mdi-close"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                    <h5 class="text-right text-danger">Total Amount: <span>{{ totalAmount }}</span> </h5>
                                </div>
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary float-right" :disabled="disabled" @click.prevent="submitForm()">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> SUBMIT
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <!-- <button type="button" class="btn btn-info" :disabled="disabled">HOLD</button>-->
                            </div>
                        <!-- </form> -->
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 
import { emptyStatement, tSInferType } from "@babel/types";

export default {
    name: 'Purchase Return',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            outletDisabled: false,
            modalActive:false,
            errors: {},
            btn:'Submit',
            itemAddEnabled: true, 
            // suppliers: [{label: "Select Supplier", value: ""}],
            suppliers: [],
            supplier_options: [],
            warehouses: [],
            outlets: [],
            outlet_options: [],
            purchase_receives: [],
            purchase_receive_all_items: [],
            purchase_receive_items: [],
            products_data: [],
            product_items: [],
            obj: {
                return_date: new Date().toISOString().slice(0,10),
                warehouse_id: '',
                outlet_id: '',
                supplier_id: '',
                item_id: '',
                stock_quantity: '',
                expire_date: '',
                return_qty: '',
                unit_price: '',
                return_unit_price: '',
                total_return_amount: '',
                note: '', 
            },
            single_item: '',
            expireStatus: false,
            itemExpiresData: [],
            total_quantity: 0,
            total_amount: 0,
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            
        };
    },
    created() {
        this.fetchOutlets();
        this.fetchSuppliers();    
        this.fetchStockProductData();    
    },
    methods: {

        checkedRequiredPrimary(){
            if(this.obj.supplier_id != "" && this.obj.item_id != "" && (this.obj.return_qty != "" && this.obj.return_qty != 0)) {
                this.itemAddEnabled = false;
            }else{ 
                this.itemAddEnabled = true;
            }
        },

        redirectRoute() {
            this.$router.push('/purchase-return-list');
        },

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headers)
            .then((res) => {
                this.suppliers = res.data.data;
                this.supplier_options = res.data.data.map((item) => {
                    return {label: item.name, value: item.id};
                }); 
            })
            .catch((err) => { 
                console.log('err => ',err.response) 
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchStockProductData() {
            axios.get(this.apiUrl + "/purchase-return-products", this.headers)
                .then((res) => {
                    this.products_data   = res.data.data;
                    console.log('this.products_data =', res.data.data);
                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                })
        },

        onChangeOutlet(event){
            var curr_outlet_id = event;
            this.purchase_receive_items = [];
            this.obj.item_id = '';
            // this.outletDisabled = false;
            if(curr_outlet_id != '') {
                // this.outletDisabled = true;
                var outlet_stock_items  = this.products_data.filter(({outlet_id}) => outlet_id == curr_outlet_id);
                this.purchase_receive_items = outlet_stock_items.map((item) => {
                    if(item.expires_date != '') {
                        var expire_data  = ' ('+item.expires_date+')';
                    }else{
                        expire_data = ''
                    }
                    return {label: item.product_name + expire_data, value: item.product_stock_id+'__'+item.product_id};
                }); 
            }
        },

        onChangeSupplier(event){
            var supplier_id = event;
            if(supplier_id != ''){
                this.obj.supplier_id = supplier_id;
                // axios.post(this.apiUrl+'/return/getPurchaseReceiveInvoice', {supplier_id:supplier_id}, this.headerjson)
                // .then((resp) => {
                //     this.purchase_receives = resp.data.data.purchase_receives;
                //     // this.purchase_receive_all_items = resp.data.data.purchase_receive_items;
                // })
                // .catch((err) => {
                //     this.$toast.error(err.response.data.message);
                // })
            }
        },


        // onChangeInvoice(event) {
        //     this.obj.item_id = '';
        //     this.purchase_receive_items = [];
        //     var invoice_id  = event.target.value;
        //     if(invoice_id != "") {
        //         axios.post(this.apiUrl+'/return/getPurchaseReceiveItems', {purchase_receive_id:invoice_id}, this.headerjson)
        //         .then((resp) => {
        //             this.purchase_receive_items = resp.data.data;
        //         })
        //         .catch((err) => {
        //             this.$toast.error(err.response.data.message);
        //         })
        //     }
        // },

        onChangeItem(event) {
            this.single_item = '';
            this.obj.stock_quantity = '';
            this.obj.return_qty = '';
            this.obj.total_return_amount = '';
            var item_id = event;
            var separate_value = item_id.split('__');
            var stock_id    = separate_value[0];
            var stock_product_id  = separate_value[1];
            
            if(item_id != "") {
                this.single_item = this.products_data.find(({product_stock_id, product_id}) => (product_stock_id == stock_id && product_id == stock_product_id));
                var item_data = this.products_data.find(({product_stock_id, product_id}) => (product_stock_id == stock_id && product_id == stock_product_id));

                this.obj.stock_quantity = (item_data) ? parseFloat(item_data.stock_quantity) : '';
                this.obj.expire_date = (item_data) ? item_data.expires_date : '';
                this.obj.unit_price = (item_data) ? parseFloat(item_data.cost_price) : '';
                this.obj.return_unit_price = (item_data) ? parseFloat(item_data.cost_price) : '';


            }
        },
        
        
        // onChangeItem(event) {
        //     this.single_item = '';
        //     this.obj.expire_id = '';
        //     this.obj.expire_qty = '';
        //     this.obj.return_qty = '';
        //     this.obj.total_return_amount = '';
        //     var item_id = event.target.value;
        //     if(item_id != "") {
        //         this.single_item = this.purchase_receive_items.find(({receive_id}) => receive_id == item_id);
        //         var item_data = this.purchase_receive_items.find(({receive_id}) => receive_id == item_id);

        //         if(item_data.is_expirable == true) {
        //             axios.post(this.apiUrl+'/return/getProductExpireData', {item_id:item_id}, this.headerjson)
        //             .then((resp) => {
        //                 this.itemExpiresData = resp.data.data;
        //             })
        //             .catch((err) => {
        //                 this.$toast.error(err.response.data.message);
        //             })
        //         }
        //         this.obj.purchase_qty = (item_data) ? parseFloat(item_data.purchase_qty) : '';
        //         this.obj.unit_price = (item_data) ? parseFloat(item_data.tp) : '';
        //         this.obj.return_unit_price = (item_data) ? parseFloat(item_data.tp) : '';


        //     }
        // },

        // onChangeExpireDate(event) {
        //     this.obj.expire_date = '';
        //     this.obj.expire_qty = '';

        //     var expire_id = event.target.value;
        //     if(expire_id != '') {
        //         var single_expire_data = this.itemExpiresData.find(({id}) => id == expire_id);

        //         this.obj.expire_date = single_expire_data.expire_date;
        //         this.obj.expire_qty = single_expire_data.expire_quantity;
        //     }

        // },

        totalReturnAmount: function(){
            if(this.obj.return_qty > 0 && this.obj.return_qty > this.obj.stock_quantity){
                this.$toast.error("Return quantity can't getter then stock quantity!");
                this.obj.return_qty = '';
            }

            if(this.obj.return_qty != "" && this.obj.return_unit_price != "") {
                var qty = this.obj.return_qty;
                var unit_price = this.obj.return_unit_price;
                var tamount = parseFloat(parseFloat(qty) * parseFloat(unit_price));
                this.obj.total_return_amount =  tamount;
            }else{
                this.obj.total_return_amount = 0;   
            }
        },

        addReturnItem() {            
            var item_id = this.obj.item_id;
            var separate_value = item_id.split('__');
            var stock_id    = separate_value[0];
            var stock_product_id  = separate_value[1];
            var sup_id = this.obj.supplier_id;
            var exp_date = this.obj.expire_date;

            var checkExists = "";
            if(this.product_items.length > 0) {
                checkExists = this.product_items.find(({product_stock_id, product_id, supplier_id, expire_date}) => ((product_stock_id == stock_id) && (product_id == stock_product_id) && (supplier_id == sup_id) && (expire_date == exp_date)));
            }
            
            var single_items = {};
            if(checkExists == "" || checkExists == undefined) {
                single_items = this.products_data.find(({product_stock_id, product_id}) => (product_stock_id == stock_id && product_id == stock_product_id));
                single_items = JSON.parse(JSON.stringify(single_items));
                
                single_items.supplier_id = sup_id;
                single_items.expire_date = exp_date;
                single_items.return_qty = this.obj.return_qty;
                single_items.note = this.obj.note;
                if(this.obj.return_unit_price != "" && this.obj.return_unit_price != 0) {
                    single_items.tp = this.obj.return_unit_price;
                }
                this.product_items.push(single_items);
        
            }else{
                this.$toast.error("Item Already Added for Return!");
            }
            this.itemAddEnabled = true;            
            this.expireStatus = false,
            this.single_item = '',
            this.itemExpiresData = [],
            this.obj.item_id = '';
            this.obj.expire_date = '';
            this.obj.stock_quantity = '';
            this.obj.return_qty = '';
            this.obj.unit_price = '';
            this.obj.return_unit_price = '';
            this.obj.total_return_amount = '';
            this.obj.note = '';
            single_items = {};
            // Reset Form
            // this.resetForm();

            // Current Date Add
            this.obj.return_date = new Date().toISOString().slice(0,10);
        },

        // Warehouses Data
        fetchWarehouses() {
            axios.get(this.apiUrl+'/warehouses', this.headers)
            .then((res) => {
                this.warehouses = res.data.data;
            })
            .catch((err) => {
                console.log("error => ", err.response);
            })
            .finally((res) => {

            });
        },

        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headers)
            .then((res) => {
                this.outlets = res.data.data;
                this.outlet_options = res.data.data.map((item) => {
                    return {label: item.name, value: item.id};
                });
            })
            .catch((err) => {
                console.log("error", err.response);
            })
            .finally((res) => {

            });
        },

        deleteRow(index) {
            this.product_items.splice(index, 1);
        },

        inputChange()
        {            
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_amount = this.totalAmount;
        },

        quantityChanged(index, value)
        {            

        },
        
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;

            const data_item = this.product_items.filter(function(pitem) {
                if((pitem.product_id != "") && (pitem.tp != 0 && pitem.tp != "") && (pitem.return_qty != 0 && pitem.return_qty != "")) {
                    return pitem;
                }
            });

            if(data_item.length > 0) {

                const formData = new FormData();
                formData.append("return_date", this.obj.return_date);
                formData.append("outlet_id", this.obj.outlet_id);
                formData.append("return_items", JSON.stringify(this.product_items));
                    
                var postEvent = axios.post(this.apiUrl+'/return/purchase_return', formData, this.headers);

                postEvent.then(res => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(res.status == 200){
                        this.obj.warehouse_id = '';
                        this.obj.outlet_id = '';
                        this.obj.supplier_id = '';
                        this.obj.item_id = '';
                        this.obj.purchase_qty = '';
                        this.obj.expire_date = '';
                        this.obj.stock_qty = '';
                        this.obj.return_qty = '';
                        this.obj.unit_price = '';
                        this.obj.return_unit_price = '';
                        this.obj.total_return_amount = '';
                        this.obj.note = '';
                        this.product_items = [];
                        // this.resetForm();
                        this.$toast.success(res.data.message); 
                        // window.location.reload();
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
        
        resetForm() {
            var self = this; //you need this because *this* will refer to Object.keys below`
            //Iterate through each object field, key is name of the object field`
            Object.keys(this.obj).forEach(function(key,index) {
                self.obj[key] = '';
            });
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
            this.checkedRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        

    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        totalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                return total + parseFloat(item.order_qty); 
            },0);
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.tp * item.return_qty);
                // let item_discount = ((item_value * item.disc_percent) / 100);
                let item_discount = ((item_value * 0) / 100);
                return total + (item_value - item_discount); 
            },0); 
        },

    }, 
    watch: {
        product_items: {
            handler: function(val, oldVal) {
                if(val.length > 0){
                    this.disabled = false;
                    this.outletDisabled = true;
                }else{
                    this.disabled = true;
                    this.outletDisabled = false;
                    // if(val.length > 0 && ((val[0].tp != 0 && val[0].tp != "") && (val[0].return_qty != 0 && val[0].return_qty != ""))) {
                    //     this.disabled = false;
                    // }else{
                    //     this.disabled = true;
                    // }
                }
            },
            deep: true
        },

    }, 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 1000px
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
}t

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