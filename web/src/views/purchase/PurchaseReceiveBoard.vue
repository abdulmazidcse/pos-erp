
<template>
    <transition name="fade" mode="out-in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Receive Board</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-success float-right" style="margin-left: 7px;" @click="toggleImportModal">
                            <i class="fas fa-plus"></i> Bulk Purchase Receive
                        </button>
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
                    <div class="card-body" v-if="!loading"> 
                            
                            
                            <div class="row">
                                

                               
 

                            </div>

                            <!-- Product Details -->
                            <div class="card" style="margin-top: 20px;">
                                <div class="card-header text-left">
                                    Product Details
                                </div>
                                <div class="card-body">
                                    <!-- <div class="form-group">
                                        <input type="text" class="form-control border" id="search_product" placeholder="Search Product">
                                    </div> -->
                                    <div class="product_table">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <th class="text-center" style="width: 5%">SL</th> 
                                                    <!-- <th class="text-center" style="width: 15%">Outlet</th>  -->
                                                    <th class="text-center" style="width: 15%">Supplier</th>
                                                    <th class="text-center" style="width: 17%">Product Name</th>
                                                    <th class="text-center" style="width: 10%">Expiry Date</th>
                                                    <th class="text-center" style="width: 5%">Ord. Qty</th>
                                                    <th class="text-center" style="width: 5%">Free Qty</th>
                                                    <th class="text-center" style="width: 5%">Rcv. Qty</th>
                                                    <th class="text-center" style="width: 7%">TP</th>
                                                    <th class="text-center" style="width: 7%">MRP</th>
                                                    <th class="text-center" style="width: 6%">Lead Time</th>
                                                    <th class="text-center" style="width: 3%"></th>
                                                </tr>
                                            </thead>

                                            <tbody v-for="(product_item, i) in product_items" :key="i">
                                                <tr> 
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                     
                                                    <td class="text-center">
                                                        <Multiselect
                                                            class="form-control border supplier_id"
                                                            mode="single"
                                                            v-model="product_item.supplier_id"
                                                            placeholder="Supplier"
                                                            @change="addNewRow($event, i)"   
                                                            :searchable="true" 
                                                            :filter-results="true"
                                                            :options="suppliers"
                                                            :classes="multiclasses"
                                                            :close-on-select="true" 
                                                            :min-chars="1"
                                                            :resolve-on-load="false" 
                                                        />
                                                         
                                                    </td>
                                                    <td class="text-center">
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
                                                        />
                                                         
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="date" class="form-control" v-model="product_item.expiry_date" @change="addNewRow($event.target.value, i)">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" v-model="product_item.order_qty" @focus="addNewRow($event.target.value, i)" placeholder="Order Qty">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" v-model="product_item.free_qty" @focus="addNewRow($event.target.value, i)" placeholder="Free Qty">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" v-model="product_item.rcv_qty" @focus="addNewRow($event.target.value, i)" placeholder="Receive Qty">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" v-model="product_item.tp" @focus="addNewRow($event.target.value, i)" placeholder="Trade Price">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control" v-model="product_item.mrp" placeholder="MRP Price">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" class="form-control" v-model="product_item.lead_time" placeholder="Lead Time">
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="text-danger" style="font-size: 17px" @click="deleteRow(i)" v-if="i != 0"><i class="mdi mdi-close"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary " :disabled="disabled" @click.prevent="submitForm()">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> SUBMIT 
                                </button>
                                <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                <!-- <button type="button" class="btn btn-info" :disabled="disabled"> HOLD</button>
                                <button type="button" class="btn btn-danger" :disabled="disabled"> PREVIEW</button> -->
                            </div>

                            
                        <!-- </form> -->
                        
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
        </div>

        <!--Bulk Import Purchase Receive Modal -->
        <Modal @close="toggleImportModal" :modalActive="importModal">
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
        </Modal>
    </div>
    </transition>
</template>
<script> 
import Modal from "./../helper/Modal";  
import axios from 'axios';   
export default {
    name: 'PurchaseReceiveBoard',
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
            importModal:false,
            errors: {},
            btn:'Create', 
            suppliers: [],
            outlets: [],
            products: [],
            products_data: [],
            product_items: [{
                // outlet_id: '',
                supplier_id: '',
                product_id: '',
                expiry_date: '',
                // expiry_date: new Date().toISOString().slice(0,10),
                order_qty: 0,
                free_qty: 0,
                rcv_qty: 0,
                tp: 0,
                mrp: 0,
                lead_time: '',
                disabled: false,
            }],
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },

            importFile: '',
            samplefile_url: this.baseUrl+'/import_excel_demo/purchase_receive.xlsx',
            
        };
    },
    created() {
        // this.fetchReferenceNo();        
        this.fetchOutlets();
        this.fetchSuppliers();
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

        // Reference Number 
        fetchReferenceNo() {
            axios.get(this.apiUrl+'/purchase_receives/getReferenceNo', this.headerjson)
            .then((res) => {
                this.obj.reference_no = res.data.data.reference_no; 
            })
            .catch((response) => { 
                console.log('companies => ',response.data) 
            }).finally((ress) => {
                console.log('companies finally',ress);
            });
        },

        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => {
                this.outlets = [{label:"Select Outlets", value:""}];
                res.data.data.map((item) => {
                    this.outlets.push({label:item.name, value:item.id});
                });
            })
            .catch((res) => {
                console.log('outlets => ',res)
            })
            .finally((res) => {
                console.log('outlets finally',res);
            });
        },

        // Supplier Data
        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headerjson)
            .then((res) => {
                this.suppliers = [{label: "Select Suppliers", value: ""}];
                res.data.data.map((item) => {
                    this.suppliers.push({label:item.name, value:item.id});
                }); 
            })
            .catch((response) => { 
                console.log('companies => ',response.data) 
            }).finally((ress) => {
                console.log('companies finally',ress);
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
                console.log("error", err.response)
            })
            .finally(() => {
                this.loading = false;
            });
        },

        // For Bulk Purchase Receive
        purchaseReceiveImportFile() {
            this.importFile = this.$refs.file.files[0];
            if(this.importFile != '') {
                this.errors = ''
            }
        },

        checkImportRequiredPrimary() {
            if(this.$refs.file.value != "") {
                this.disabled_upload = false;
            }else{
                this.disabled_upload = true;
            }
        },

        addNewRow(value, index){
            console.log("value, index", value, index);
            var item_length = this.product_items.length;  
            if(index == (item_length - 1)) {
                this.product_items.push(
                    {
                        // outlet_id: '',
                        supplier_id: '',
                        product_id: '',
                        expiry_date: '',
                        // expiry_date: new Date().toISOString().slice(0,10),
                        order_qty: 0,
                        free_qty: 0,
                        rcv_qty: 0,
                        tp: 0,
                        mrp: 0,
                        lead_time: '',
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

            this.product_items[index].tp = p_tp;
            this.product_items[index].mrp = p_mrp;
        },

        submitForm: function() {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("products", JSON.stringify(this.product_items));
                      
            var postEvent = axios.post(this.apiUrl+'/purchase_receives/purchaseReceiveWithBoard', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.product_items = [{
                                            // outlet_id: '',
                                            supplier_id: '',
                                            product_id: '',
                                            expiry_date: '',
                                            // expiry_date: new Date().toISOString().slice(0,10),
                                            order_qty: 0,
                                            free_qty: 0,
                                            rcv_qty: 0,
                                            tp: 0,
                                            mrp: 0,
                                            lead_time: '',
                                            disabled: false,
                                        }],
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
        },
        
        submitImportForm: function() {  
            this.isUploadSubmit = true;
            this.disabled_upload = true;
            const formData = new FormData();
            formData.append("excel_file", this.importFile);
                    
            var postEvent = axios.post(this.apiUrl+'/purchase_receives/purchaseReceiveBulkUpload', formData, this.headers);

            postEvent.then(res => {
                this.isUploadSubmit = false;
                this.disabled_upload = false;
                if(res.status == 200){
                    this.importFile = '';
                    this.disabled_upload = true;
                    this.toggleImportModal();
                    this.$toast.success(res.data.message); 
                    // window.location.reload();
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isUploadSubmit = false; 
                this.importFile = '';
                this.$refs.file.value=null;
                this.disabled_upload = true;
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
        },

        onkeyPress: function(field) { 
            if(this.importModal) {
                this.checkImportRequiredPrimary();
            }
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

    watch: {
        product_items: {
            handler: function(val) {

                // console.log("items ==== ", val);
                if(val.length > 0){
                    this.disabled = false;
                }else{
                    this.disabled = true;
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