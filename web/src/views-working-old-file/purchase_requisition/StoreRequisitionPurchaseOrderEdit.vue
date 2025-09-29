
<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Requisition </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Store Requistion Purchase Order Edit</a></li>
                            
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
                                                    
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="reference_no">Reference No *</label>
                                    <input type="text" class="form-control border" id="reference_no" @keypress="onkeyPress('reference_no')" v-model="obj.reference_no" readonly>
                                    <div class="invalid-feedback" v-if="errors.reference_no">
                                        {{errors.reference_no[0]}}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="order_date">Order Date *</label>
                                    <input type="date" class="form-control border" id="order_date" @change="onkeyPress('order_date')" v-model="obj.order_date" readonly>
                                    <div class="invalid-feedback" v-if="errors.order_date">
                                        {{errors.order_date[0]}}
                                    </div>
                                </div>
                                
                                <!-- <div class="form-group col-md-4">
                                    <label for="delivery_date">Delivery Date *</label>
                                    <input type="date" class="form-control border" id="delivery_data" @change="onkeyPress('delivery_date')" v-model="obj.delivery_date">
                            
                            
                                    <div class="invalid-feedback" v-if="errors.delivery_date">
                                        {{errors.delivery_date[0]}}
                                    </div>
                                </div> -->

                                <div class="form-group col-md-6">
                                    <label for="outlet_id" style="width: 100%;">Delivery Location</label>
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

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier *</label>
                                        <select class="form-control border" id="supplier_id" v-model="obj.supplier_id" @change="onChangeSupplier($event)">
                                            <option value="">--- Select Supplier ---</option>
                                            <option v-for="(supplier, index) in suppliers" :key="index" :refs="supplier" :value="supplier.id">{{ supplier.name }} [{{ supplier.phone }}]</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.supplier_id">
                                            {{errors.supplier_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-4">
                                    <button type="button" class="btn btn-primary float-left" style="margin-top: 30px;" @click="onClickSupplierProduct()" v-if="productShowBtn">Show</button>
                                    <button type="button" class="btn btn-primary float-left" style="margin-top: 30px;" @click="onClickSupplierProduct()" v-else disabled>Show</button>

                                
                                </div> -->
                            </div>

                            <!-- Product Details -->
                            <div class="card" style="margin-top: 20px;">
                                <div class="card-header text-left">
                                    <h4>Product Details</h4>
                                    <input type="hidden" v-model="obj.total_quantity">
                                    <!-- <span class="total_quantity">Chalan Quantity: <b>{{ totalQuantity }}</b></span> -->
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive product_table">
                                        <table class="table table-bordered table-centered table-nowrap">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <th class="text-center" style="width: 5%;">SL</th> 
                                                    <th class="text-center">Name </th>
                                                    <th class="text-center">UOM</th>
                                                    <th class="text-center">Crt. Size</th>                                              
                                                    <th class="text-center" style="width: 10%;">Pur. Price</th>
                                                    <th class="text-center" style="width: 10%;">MRP</th>
                                                    <th class="text-center">Req. Qty</th>
                                                    <th class="text-center" style="width: 10%;">Ord. Qty</th>
                                                    <!-- <th>Disc (%)</th> -->
                                                    <!-- <th>Free Qty</th> -->
                                                    <th class="text-center">Amount</th>
                                                    <!-- <th>Disc Amt</th> -->
                                                    <!-- <th>Free Amt</th> -->
                                                    <!-- <th>VAT</th> -->
                                                    <!-- <th>Amount</th> -->
                                                    <!-- <th>Line Notes</th> -->
                                                    <!-- <th class="text-center">Supplier</th> -->
                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1}}</td>
                                                    <td style="width: 150px;">{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                    <td class="text-center">{{ product_item.carton_size }}</td>
                                                    <td class="text-center">
                                                        <input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.cost_price">
                                                        <!-- {{ product_item.cost_price }} -->
                                                    </td>
                                                    <td class="text-center">{{ product_item.mrp_price }}</td>
                                                    <td class="text-center">{{ product_item.approve_qty }}</td>
                                                    <td class="text-center"><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.order_qty"></td>
                                                    <!-- <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.disc_percent" style="width: 50px"></td> -->
                                                    <!-- <td><input :readonly="!product_item.checked" type="text" class="form-control" @keyup="inputChange()" v-model="product_item.free_qty" style="width: 50px"></td> -->
                                                    <td class="text-center">{{ product_item.order_qty * product_item.cost_price }}</td>
                                                    <!-- <td>{{ (((product_item.order_qty * product_item.cost_price) * product_item.disc_percent) / 100) }}</td> -->
                                                    <!-- <td>{{ product_item.free_qty * product_item.cost_price }}</td> -->
                                                    <!-- <td>{{ product_item.vat }}</td> -->
                                                    <!-- <td>{{ ((product_item.order_qty * product_item.cost_price) - (((product_item.order_qty * product_item.cost_price) * product_item.disc_percent) / 100)) + product_item.vat }}</td> -->
                                                    <!-- <td><input  :readonly="!product_item.checked" type="text" class="form-control" v-model="product_item.line_notes" style="width: 150px"></td> -->
                                                    <!-- <td class="text-center" style="width: 150px;">
                                                        <select class="form-control border" v-model="product_item.supplier_id" style="width: 150px;" :disabled="!product_item.checked">
                                                            <option value="">--- Select ---</option>
                                                            <option v-for="(supplier, i) in suppliers" :value="supplier.id" :key="i">{{ supplier.name }}</option>
                                                        </select>
                                                    </td> -->
                                                </tr>
                                            </tbody>
                                            
                                        </table>

                                        <h5 class="text-right text-danger">Total Amount: <span>{{ obj.total_amount }}</span></h5>
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

                                </div>
                            </div>

                            <div class="buttons">
                                <button type="submit" class="btn btn-primary float-right" :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> Update Order 
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
    name: 'Store Requisition Purchase Order',
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
            suppliers: [],
            warehouses: [],
            outlets: [],
            product_items: [],
            obj: new Form({
                supplier_id: '',
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
            product_all_checked: false,
            
        };
    },
    created() {
        this.fetchSuppliers();
        this.fetchOutlets();
        this.fetchWarehouses();
        this.fetchRequisitionPurchaseOrder(this.purchaseOrderId);
    },
    methods: { 

        fetchRequisitionPurchaseOrder(purchase_order_id) {
            axios.get(this.apiUrl+'/purchase_orders/'+purchase_order_id+'/storeRequisitionPurchaseOrderEdit', this.headers)
            .then((res) => {
                this.obj.fill(res.data.data.purchase_order);
                this.product_items = res.data.data.purchase_order_products;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
            .finally((resp) => {
                this.loading = false;
            });
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

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headers)
            .then((res) => {
                this.suppliers = res.data.data; 
            })
            .catch((err) => { 
                console.log('err => ',err.response) 
            }).finally((ress) => {

            });
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

        onChangeWarehouse: function(event) {
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

        inputChange()
        {
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_value = this.totalValue;
            this.obj.commission_value = this.totalCommission;
            this.obj.total_free_amount = this.totalFreeAmount;
            this.obj.total_amount = this.totalAmount;
        },
        
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("supplier_id", this.obj.supplier_id);
            formData.append("order_date", this.obj.order_date);
            formData.append("delivery_date", this.obj.delivery_date);
            formData.append("warehouse_id", this.obj.warehouse_id);
            formData.append("outlet_id", this.obj.outlet_id);
            formData.append("reference_no", this.obj.reference_no);
            formData.append("start_date", this.obj.start_date);
            formData.append("end_date", this.obj.end_date);
            formData.append("total_quantity", this.obj.total_quantity);
            formData.append("total_value", this.obj.total_value);
            formData.append("commission_value", this.obj.commission_value);
            formData.append("total_vat", this.obj.total_vat);
            formData.append("total_free_amount", this.obj.total_free_amount);
            formData.append("total_amount", this.obj.total_amount);
            formData.append("products", JSON.stringify(this.product_items));
            formData.append("_method", "PUT");
                      
            var postEvent = axios.post(this.apiUrl+'/purchase_orders/'+this.purchaseOrderId+'/confirmRequisitionPurchaseOrderEdit', formData, this.headers);

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
                return total + (item.cost_price * item.order_qty); 
            },0); 
        },
        
        totalCommission: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.cost_price * item.order_qty);
                return total + ((item_value * item.disc_percent) / 100); 
            },0); 
        },

        totalFreeAmount: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.cost_price * item.free_qty); 
            },0); 
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                let item_value = (item.cost_price * item.order_qty);
                let item_discount = ((item_value * item.disc_percent) / 100);
                return total + (item_value - item_discount); 
            },0); 
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
        }
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