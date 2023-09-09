
<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Order Edit</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                            Add New
                        </button>  -->
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
                        <form id="purchase_order_form" @submit.prevent="submitForm()">
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
                                    <label for="warehouse_id" style="width: 100%;">Delivery Location</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select class="form-control border" id="warehouse_id" v-model="obj.warehouse_id" @change="onChangeWarehouse" :disabled="obj.outlet_id != '' ? true : false">
                                                <option value="">--- Select Warehouse ---</option>
                                                <option v-for="(warehouse, index) in warehouses" :key="index" :value="warehouse.id">{{ warehouse.name }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control border" id="outlet_id" v-model="obj.outlet_id" @change="onChangeOutlet" :disabled="obj.warehouse_id != '' ? true : false">
                                                <option value="">--- Select Outlet ---</option>
                                                <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="supplier_id">Supplier *</label>
                                    <select class="form-control border" id="supplier_id" v-model="obj.supplier_id" @change="onChangeSupplier($event)">
                                        <option value="">--- Select Supplier ---</option>
                                        <option v-for="(supplier, index) in suppliers" :key="index" :refs="supplier" :value="supplier.id">{{ supplier.name }} [{{ supplier.phone }}]</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.supplier_id">
                                        {{errors.supplier_id[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="supplier_payment_type">Supplier Payment Type *</label>
                                    <select id="supplier_payment_type" class="form-control" v-model="obj.supplier_payment_type">
                                        <option value="">--- Select Payment Type ---</option>
                                        <option value="cash_purchase">Cash Purchase</option>
                                        <option value="credit">Credit</option>
                                        <option value="after_sale">After Sale</option>
                                        <option value="sale_after_commission">Sale After Commission</option>
                                    </select>
                                </div>

                                <!-- <div class="col-md-9">
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
                                    <!-- <span class="total_quantity">Total Order Quantity: <b>{{ totalQuantity }}</b></span> -->
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

                                    <h5 class="text-right text-danger">Total Amount: <span>{{ totalAmount }}</span> </h5>

                                </div>
                            </div>

                            <div class="buttons">
                                <button type="submit" class="btn btn-primary float-right" :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> UPDATE ORDER
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <!-- <button type="button" class="btn btn-info">HOLD</button>
                                <button type="button" class="btn btn-danger">PREVIEW</button> -->
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
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 

export default {
    name: 'Purchase Order Edit',
    components: {
        Modal
    },
    data() {
        return {
            purchaseOrderId: this.$route.params.id,
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            btn:'Create',
            productShowBtn: false,  
            suppliers: [],
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
            obj: new Form({
                id: '',
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
                total_quantity: '',
                total_value: '',
                commission_value: '',
                total_vat: '',
                total_free_amount: '',
                total_amount: '', 
            }),
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            product_all_checked: false,
            
        };
    },
    created() {
        this.fetchSuppliers();
        this.fetchOutlets();
        this.fetchWarehouses();
        this.fetchProducts();
        this.fetchPurchaseOrder(this.purchaseOrderId);
    },
    methods: { 

        fetchPurchaseOrder(order_id) {
            axios.get(this.apiUrl+'/purchase_orders/'+order_id, this.headers)
            .then((res) => {
                this.obj.fill(res.data.data.purchase_order);
                this.product_items = res.data.data.purchase_products;
                this.productShowBtn = true;
            })
            .catch((err) => {
                console.log("error", err.response);
            })
            .finally((resp) => {
                this.loading = false;
            });
        },

        checkedRequiredPrimary() {
            // if(this.obj.supplier_id && this.obj.order_date &&  this.obj.delivery_date && this.obj.reference_no && this.obj.start_date && this.obj.end_date){
            if(this.obj.supplier_id && this.obj.order_date && this.obj.reference_no){
                this.productShowBtn = true;
            }else{
                 this.productShowBtn = false;
            }
        },

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headerjson)
            .then((res) => {
                console.log("suppliers === ", res.data.data)
                this.suppliers = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
            });
        },

        onChangeSupplier(event){
            var supplier_id = event.target.value;
            if(supplier_id != '') {

            }
            
        },

        // Warehouses Data
        fetchWarehouses() {
            axios.get(this.apiUrl+'/warehouses', this.headerjson)
            .then((res) => {
                this.warehouses = res.data.data;
            })
            .catch((err) => {
                console.log("error => ", err.response);
            })
            .finally((res) => {

            });
        },

        onChangeWarehouse: function(event) {
            var w_id = event.target.value;
            if(w_id != "") {
                this.obj.outlet_id = '';
            } 
        },
        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => {
                console.log("outlets === ", res.data.data);
                this.outlets = res.data.data;
            })
            .catch((res) => {
                
            })
            .finally((res) => {

            });
        },
        
        onChangeOutlet: function(event) {
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
        
        // For New Product Row Add
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

        quantityChanged(index, value)
        {            

        },
        
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;

            const data_item = this.product_items.filter(function(pitem) {
                if((pitem.product_id != "") && (pitem.tp != 0 && pitem.tp != "") && (pitem.order_qty != 0 && pitem.order_qty != "")) {
                    return pitem;
                }
            });

            if(data_item.length > 0) {

                const formData = new FormData();
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
                formData.append("products", JSON.stringify(this.product_items));
                formData.append('_method', 'put');
                    
                var postEvent = axios.post(this.apiUrl+'/purchase_orders/'+this.purchaseOrderId, formData);

                postEvent.then(res => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(res.status == 200){
                        this.resetForm();                    
                        this.$toast.success(res.data.message); 
                        this.$router.push('/purchase-order-list');
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
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.tp * item.order_qty); 
            },0); 
        },
        
        totalCommission: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.tp * item.order_qty);
                return total + ((item_value * 0) / 100); 
            },0); 
        },

        totalFreeAmount: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.tp * 0); 
            },0); 
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.tp * item.order_qty);
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
        handler: function(val, oldVal) {
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
    border: none !important;
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

#reference_no {
    color: red;
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
</style>