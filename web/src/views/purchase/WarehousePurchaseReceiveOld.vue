
<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Warehouse Purchase Receive</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <a href="/purchase-receive-list"><button type="button" class="btn btn-primary float-right">
                            Purchase Receive List
                        </button></a> 
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <form id="purchase_order_form" @submit.prevent="submitForm()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Supplier *</label>
                                            <select class="form-control border" id="supplier_id" v-model="obj.supplier_id" @change="onChangeSupplier($event)">
                                                <option value="">--- Select Supplier ---</option>
                                                <option v-for="(supplier, index) in suppliers" :key="index" :value="supplier.id">{{ supplier.name }} [{{ supplier.phone }}]</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.supplier_id">
                                                {{errors.supplier_id[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="supplier_id">Purchase Order *</label>
                                            <select class="form-control border" id="purchase_order_id" v-model="obj.purchase_order_id" @change="onchangePurchaseOrder($event)">
                                                <option value="">--- Select ---</option>
                                                <option v-for="(supplier_order, index) in supplier_orders" :key="index" :value="supplier_order.id">{{ supplier_order.reference_no }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.supplier_id">
                                                {{errors.supplier_id[0]}}
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
                                    <label for="challan_no">Challan No *</label>
                                    <input type="text" class="form-control border" id="challan_no" @keypress="onkeyPress('challan_no')" v-model="obj.challan_no">
                                    <div class="invalid-feedback" v-if="errors.challan_no">
                                        {{errors.challan_no[0]}}
                                    </div>
                                </div>

                                <!-- <div class="form-group col-md-4">
                                    <label for="outlet_id">Delivery To</label>
                                    <select class="form-control border" id="outlet_id" v-model="obj.outlet_id">
                                        <option value="">--- Select Outlet ---</option>
                                        <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                    </select>
                                </div> -->

                                <!-- <div class="form-group col-md-3">
                                    <label for="supplier_id">Delivery To</label>
                                    <select class="form-control border" id="supplier_id">
                                        <option value="">--- Select Supplier ---</option>
                                        <option value="1">Halal & Brothers</option>
                                    </select>
                                </div> -->

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
                                    <div class="table-responsive product_table">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <th class="text-center" style="width: 5%"><input type="checkbox" v-model="product_all_checked" @change="checkedAll"></th> 
                                                    <th class="text-center">Name </th> 
                                                    <th class="text-center">Barcode</th> 
                                                    <!-- <th>WH STK</th>
                                                    <th>LAST PO Qty</th>
                                                    <th>LAST PUR. Qty</th>
                                                    <th>PO Qty</th> -->
                                                    <th class="text-center">UOM</th>
                                                    <!-- <th>Remain Qty</th> -->
                                                    <!-- <th>CPU</th> -->
                                                    <th class="text-center">Pur. Price</th>
                                                    <th class="text-center">MRP</th>
                                                    <th class="text-center">Rcv. Qty</th>
                                                    <th class="text-center">Free Qty</th>
                                                    <th class="text-center">Free Amount</th>
                                                    <th class="text-center">Disc (%)</th>
                                                    <th class="text-center">Disc Amt</th>
                                                    <th class="text-center">VAT</th>
                                                    <th class="text-center">Amount</th>
                                                    <!-- <th>Profit(%)CPU</th>
                                                    <th>Profit(%)MRP</th> -->
                                                    <th class="text-center"></th>
                                                </tr>
                                            </thead>

                                            <!-- <tbody v-if="product_items.length > 0"> -->
                                            <tbody v-for="(product_item, i) in product_items" :key="i">
                                                <tr v-if="is_expires != 1"> 
                                                    <td class="text-center"><input type="checkbox" v-model="product_item.checked"></td>
                                                    <td class="text-center">{{ product_item.name }}</td>
                                                    <td class="text-center">{{ product_item.code }}</td>
                                                    <!-- <td>{{ product_item.wh_stk }}</td>
                                                    <td>{{ product_item.last_po_qty }}</td>
                                                    <td>{{ product_item.last_purchase_qty }}</td>
                                                    <td>{{ product_item.po_qty }}</td> -->
                                                    <td class="text-center">{{ product_item.unit_code }}</td>
                                                    <!-- <td>{{ product_item.remain_qty }}</td> -->
                                                    <!-- <td>{{ product_item.cpu }}</td> -->
                                                    <td class="text-center">
                                                        <input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.purchase_price" style="width: 60px">
                                                    </td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.sale_price" style="width: 80px"></td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.rcv_qty" style="width: 50px"></td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.free_qty" style="width: 50px"></td>
                                                    <td class="text-center">{{ product_item.free_qty * product_item.purchase_price }}</td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange(product_item, 'percent')" v-model="product_item.disc_percent" style="width: 50px"></td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange(product_item, 'flat')" v-model="product_item.disc_amount" style="width: 80px"></td>
                                                    <!-- <td>{{ (((product_item.rcv_qty * product_item.purchase_price) * product_item.disc_percent) / 100) }}</td> -->
                                                    <td class="text-center">{{ product_item.vat }}</td>
                                                    <td class="text-center">{{ ((product_item.rcv_qty * product_item.purchase_price) - (((product_item.rcv_qty * product_item.purchase_price) * product_item.disc_percent) / 100)) + product_item.vat }}</td>

                                                    <!-- <td>{{ product_item.profit_percent_cpu }}</td> -->
                                                    <!-- <td>{{ product_item.profit_percent_mrp }}</td> -->

                                                    <td class="actions-btn">
                                                        <a href="#" v-if="!product_item.checked | !product_item.is_expirable" class="btn btn-primary btn-sm expireBtn btnDisabled"> Add Expire Date</a>
                                                        <a href="#" v-else-if="product_item.checked && !product_item.is_expirable" class="btn btn-primary btn-sm expireBtn btnDisabled"> Add Expire Date</a>
                                                        <a href="#" v-else class="btn btn-primary btn-sm expireBtn" @click.prevent="addExpireDate(product_item)"> Add Expire Date</a>
                                                        <a href="#" v-if="!product_item.checked" class="btn btn-info btn-sm giftBtn btnDisabled"> Add Gift</a>
                                                        <a href="#" v-else class="btn btn-info btn-sm giftBtn" @click.prevent="addGiftItem(product_item)"> Add Gift</a>
                                                    </td>
                                                </tr>

                                                <tr v-if="product_item.is_expires || product_item.is_gifts"> 
                                                    <td colspan="8">
                                                        <h4 v-if="product_item.is_expires">Expired Date Add</h4>
                                                        <table style="width: 100%">
                                                            <tr v-for="(expire_item, e) in product_item.expires_data" :key="e">
                                                                <td style="width: 50%"><input type="date" class="form-control" v-model="expire_item.expire_date"></td>
                                                                <td><input type="number" step="any" class="form-control" v-model="expire_item.expire_qty" @keyup="expireQtyValidation(product_item, e)"></td>
                                                                <td><button type="button" class="btn btn-danger btn-sm" @click.prevent="deleteExpires(product_item, e)">Delete Expires</button></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td colspan="6">
                                                        <h4 v-if="product_item.is_gifts">Gift Item Add</h4>
                                                        <table style="width: 100%">
                                                            <tr v-for="(gift_item, g) in product_item.gifts" :key="g">
                                                                <td style="width: 50%"><input type="text" class="form-control" v-model="gift_item.gift_name" placeholder="Gift Item Name"></td>
                                                                <td><input type="number" step="any" class="form-control" v-model="gift_item.gift_qty"></td>
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
                                    </span>SAVE 
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <button type="button" class="btn btn-info" :disabled="disabled">HOLD</button>
                                <button type="button" class="btn btn-danger" :disabled="disabled">PREVIEW</button>
                            </div>

                            
                        </form>    
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
import Form from 'vform'
import axios from 'axios'; 
import { ref, onUnmounted, getCurrentInstance, onMounted } from 'vue' 
 
export default {
    name: 'Purchase Receive',
    components: {
        Modal
    },
    // setup(){
    //     const { appContext } = getCurrentInstance()
    //     const container = ref()
    //     let counter = 1
    //     let destroyComp = null
    // },
    data() {
        return {
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            modalActive:false,
            errors: {},
            btn:'Create',
            productShowBtn: false, 
            suppliers: [],
            supplier_orders: [],
            outlets: [],
            supplier:'',
            is_expires:false,
            product_items: [],
            obj: {
                supplier_id: '',
                purchase_order_id: '',
                purchase_date: new Date().toISOString().slice(0,10),
                outlet_id: '',
                reference_no: '',
                challan_no: '',
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
            product_all_checked: false,
            
        };
    },
    created() {
        this.fetchReferenceNo();
        this.fetchSuppliers();
        this.fetchOutlets();

    },
    methods: {

        // checkedRequiredFinal(){
        //     //this.checkedRequiredPrimary();
        //     if(this.product_items.qty){
        //         this.disabled = false;
        //     }else{
        //          this.disabled = true;
        //     }
        // },
        checkedAll: function(){
            var checked = this.product_all_checked;
            if(checked == true) {
                return this.product_items.filter(item=>item.checked = true).length > 0
            }else{
                return this.product_items.filter(item=>item.checked =false).length > 0
            }
        },

        // Reference Number 
        fetchReferenceNo() {
            axios.get(this.apiUrl+'/warehouse_purchase_receives/getReferenceNo', this.headers)
            .then((res) => {
                this.obj.reference_no = res.data.data.reference_no; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
            });
        },
        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headers)
            .then((res) => {
                this.suppliers = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
            });
        },
        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headers)
            .then((res) => {
                this.outlets = res.data.data;
            })
            .catch((res) => {
                
            })
            .finally((res) => {

            });
        },

        onChangeSupplier(event){
            this.product_items = [];
            this.obj.purchase_order_id = '';
            this.obj.outlet_id = '';
            var supplier_id = event.target.value;
            if(supplier_id != ''){
                this.onkeyPress('supplier_id');

                axios.post(this.apiUrl+'/warehouse_purchase_receives/getPurchaseOrder', {'supplier_id': supplier_id}, this.headerjson )
                .then((res) => {

                    this.supplier_orders = res.data.data;
                    
                })
                .catch((err) => {

                })
                .finally();
            }else{
                this.supplier_orders = [];
                this.obj.purchase_order_id = '';
            }
            
            
        },

        onchangePurchaseOrder(event){

            this.product_items = [];
            this.obj.outlet_id = '';
            var purchase_order_id = event.target.value;
            if(purchase_order_id != '') {
                axios.post(this.apiUrl+'/purchase_receives/purchaseOrderProducts', {'purchase_order_id': purchase_order_id}, this.headerjson )
                .then((res) => {
                    console.log(res.data.data);
                    this.obj.outlet_id = res.data.data.requisition_outlet_id;
                    this.order_data.fill(res.data.data.purchase_order);
                    this.product_items  = res.data.data.purchase_products;
                    
                })
                .catch((err) => {

                })
                .finally();
            }else{
                this.order_data.reset();
                this.product_items  = [];
            }
        },

        inputChange(product_item='', type='')
        {
            this.order_data.total_quantity = this.totalQuantity;
            this.order_data.total_value = this.totalValue;
            this.order_data.commission_value = this.totalCommission;
            this.order_data.total_free_amount = this.totalFreeAmount;
            this.order_data.total_amount = this.totalAmount;
            this.order_data.net_amount = this.netAmount;

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

            console.log("counter", counter);
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

        
        add: function(e) {
            console.log('preventDefault', e)
            //this.close()
        },
        edit: function(item) { 
            this.btn='Update';
            this.editMode = true;

        },
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("supplier_id", this.obj.supplier_id);
            formData.append("purchase_order_id", this.obj.purchase_order_id);
            formData.append("purchase_date", this.obj.purchase_date);
            // formData.append("outlet_id", this.obj.outlet_id);
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
                      
            var postEvent = axios.post(this.apiUrl+'/warehouse_purchase_receives', formData);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.resetForm();
                    this.order_data.reset();
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
                console.log('catch',this.isSubmit)
            });
        },
        
        resetForm() {
            console.log('Reseting the form');
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
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        

        // ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        totalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                return total + parseFloat(item.rcv_qty); 
            },0);
        }, 
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.purchase_price * item.rcv_qty); 
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
                let item_value = (item.purchase_price * item.rcv_qty);
                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0); 
        },
        
        netAmount: function(){
            var net_amount = this.product_items.reduce(function(total, item){
                let item_value = (item.purchase_price * item.rcv_qty);
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

                // console.log("items ==== ", val);
                if(val.length > 0){
                    this.disabled = false;
                }else{
                    this.disabled = true;
                }
                console.log('checkQtyValue', this.checkQtyValue)
                // this.disabled = this.checkQtyValue;
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