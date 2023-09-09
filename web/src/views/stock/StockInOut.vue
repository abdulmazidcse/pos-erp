
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Stock </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Stock In/Out</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleImportModal">
                            <i class="fas fa-plus"></i> Bulk Stock In/Out
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!--Bulk Product Stock In/Out Modal -->
        <Modal @close="toggleImportModal" :modalActive="importModal">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleImportModal" type="button" class="btn btn-default">X</button>
                    <h5 style="text-align: right">Import Stock Product</h5>
                </div>

                <div class="modal-body">  
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" @submit.prevent="submitImportForm()" enctype="multipart/form-data">
                                <p style="font-size: 13px; font-style: italic;">The field labels marked with * are required input fields.</p>
                                <p style="font-size: 16px;">The correct column order is (product_name*, product_code*, outlet_name*, expires_date, in_stock, out_stock) and you must follow this.</p>
                                <p style="font-size: 16px;">In Stock for increase stock product and out_stock for decrease stock_product. Both default value is 0.</p>
                                <p style="font-size: 16px;">(If you provide expires date this product will be stock based on expires date)</p>
                                <p style="font-size: 16px;">Product Name Must be enlisted product Before Stock (if don't have)</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="excel_file"> Upload EXCEL File *</label>
                                            <input type="file" class="form-control" id="excel_file" ref="file" name="..." @change="stockInOutImportFile(), onkeyPress('excel_file')">
                                            
                                            <div class="invalid-feedback" v-if="errors.excel_file">
                                                {{errors.excel_file[0]}}
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
                                        <span v-show="isSubmit">
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

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        
                        <form id="stock_in_out_form" @submit.prevent="submitForm()">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="outlet_id">Outlet *</label>
                                    <select class="form-control border" id="outlet_id" v-model="obj.outlet_id" @change="onkeyPress('outlet_id')">
                                        <option value="">--- Select Outlet ---</option>
                                        <option v-for="(outlet, index) in outlets" :key="index" :value="outlet.id">{{ outlet.name }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.outlet_id">
                                        {{errors.outlet_id[0]}}
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="product_id">Product *</label>
                                    <select class="form-control border" id="product_id" v-model="obj.product_id" @change="onkeyPress('product_id')">
                                        <option value="">--- Select Product ---</option>
                                        <option v-for="(product, index) in products" :key="index" :value="product.id">{{ product.product_name }} - [{{ product.product_code }}]</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.product_id">
                                        {{errors.product_id[0]}}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="in_stock_quantity">In Stock Quantity </label>
                                    <input type="number" class="form-control border" id="in_stock_quantity" @keyup="onkeyPress('in_stock_quantity')" v-model="obj.in_stock_quantity">
                                    <div class="invalid-feedback" v-if="errors.in_stock_quantity">
                                        {{errors.in_stock_quantity[0]}}
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="out_stock_quantity">Out Stock Quantity </label>
                                    <input type="number" class="form-control border" id="out_stock_quantity" @keyup="onkeyPress('out_stock_quantity')" v-model="obj.out_stock_quantity">
                                    <div class="invalid-feedback" v-if="errors.out_stock_quantity">
                                        {{errors.out_stock_quantity[0]}}
                                    </div>
                                </div>
                                
                                <div class="mb-3 col-md-4">
                                    <label for="expires_date">Expires Date </label>
                                    <input type="date" class="form-control border" id="expires_date" @change="onkeyPress('expires_date')" v-model="obj.expires_date">
                                    <div class="invalid-feedback" v-if="errors.expires_date">
                                        {{errors.expires_date[0]}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <label for="note">Note</label>
                                    <textarea class="form-control border" id="note" rows="3" @keyup="onkeyPress('note')" v-model="obj.note" placeholder="Remarks Here!"></textarea>
                                    <div class="invalid-feedback" v-if="errors.note">
                                        {{errors.note[0]}}
                                    </div>
                                </div>
                            </div>

                            <div class="buttons">
                                <button type="submit" class="btn btn-primary " style="width: 10%;" :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span>SAVE 
                                </button>
                            </div>

                            
                        </form>
                        
                    </div>
                </div>

                <!-- Product Stock Details -->
                <div class="card">
                    <div class="card-header text-left">
                        Product Latest Stock List
                    </div>
                    <div class="card-body">
                        <!-- <div class="mb-3">
                            <input type="text" class="form-control border" id="search_product" placeholder="Search Product">
                        </div> -->
                        <div class="product_table table-responsive">
                            <table class="table table-centered table-bordered table-nowrap w-100"  v-if="!loading">
                                <thead class="table-light">
                                    <tr class="border success item-head">
                                        <th>SL</th> 
                                        <th>Outlet </th> 
                                        <th>Product Name</th> 
                                        <th>Product Code</th>
                                        <th>Expires Date</th>
                                        <th>In Stock Qty </th>
                                        <th>In Stock Weight</th>
                                        <th>Out Stock Qty </th>
                                        <th>Out Stock Weight</th>
                                        <th>Stock Qty </th>
                                        <th>Stock Weight</th>
                                    </tr>
                                </thead>

                                <tbody v-if="items.length > 0">
                                    <tr v-for="(product_item, i) in items" :key="i">

                                        <td>{{ i + 1 }}</td>
                                        <td>{{ product_item.outlet_name }}</td>
                                        <td>{{ product_item.product_name }}</td>
                                        <td>{{ product_item.product_code }}</td>
                                        <td>{{ product_item.expires_date }}</td>
                                        <td class="text-right">{{ product_item.in_stock_quantity }}</td>

                                        <td class="text-right">{{ product_item.in_stock_weight }}</td>
                                        <td class="text-right">{{ product_item.out_stock_quantity}}</td>
                                        <td class="text-right">{{ product_item.out_stock_weight }}</td>
                                        <td class="text-right">{{ product_item.stock_quantity }}</td>
                                        <td class="text-right">{{ product_item.stock_weight }}</td>                                   
                                        
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="overlay" v-if="pageloading">
            <div class="overlay__inner">
                <div class="overlay__content">
                    <img src="../../assets/image/loading.gif" alt="Loading..." style="width:130px">
                    <!-- <span class="spinner"></span> -->
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
    name: 'Stock In/Out',
    components: {
        Modal
    },
    data() {
        return {
            pageloading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            disabled_upload: true,
            modalActive:false,
            importModal:false,
            errors: {},
            outlets: [],
            products: [],
            items: [],
            importFile: '',
            samplefile_url: this.sampleFileUrl+'/import_excel_demo/stock_products_in_out.xlsx',
            obj: new Form({
                outlet_id: '',
                product_id: '',
                in_stock_quantity: '',
                out_stock_quantity: '',
                expires_date: '',
                note: '',
            }),

            
        };
    },
    created() {
        this.fetchLatestStockData();
        this.fetchProducts();
        this.fetchOutlets();
    },
    methods: { 

        toggleImportModal() {
            this.importModal = !this.importModal;
            this.importFile = '';
            this.$refs.file.value=null;
            this.disabled_upload = true;
        },
        // Stock Data
        fetchLatestStockData() {
            axios.get(this.apiUrl+'/stocks/latestStockData', this.headerjson)
            .then((res) => {
                this.items = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.pageloading = false;
            });
        },

        // Product Data
        fetchProducts() {
            axios.get(this.apiUrl+'/products', this.headerjson)
            .then((res) => {
                this.products = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },
        // Outlets Data
        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => {
                this.outlets = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },
        
        checkRequiredPrimary() {
            if((this.obj.outlet_id != '' && this.obj.product_id != '') && (this.obj.in_stock_quantity != '' || this.obj.out_stock_quantity != '')) {
                this.disabled = false;
            }else{
                this.disabled = true;
            }
        }, 
        
        stockInOutImportFile() {
            this.importFile = this.$refs.file.files[0];
        },

        checkImportRequiredPrimary() {
            if(this.$refs.file.value != "") {
                this.disabled_upload = false;
            }else{
                this.disabled_upload = true;
            }
        }, 

        resetForm() {
            var self = this; //you need this because *this* will refer to Object.keys below`
            //Iterate through each object field, key is name of the object field`
            Object.keys(this.obj).forEach(function(key,index) {
                self.obj[key] = '';
            });
            this.items = [];
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
            this.checkRequiredPrimary();
            this.checkImportRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("outlet_id", this.obj.outlet_id);
            formData.append("product_id", this.obj.product_id);
            formData.append("in_stock_quantity", this.obj.in_stock_quantity);
            formData.append("out_stock_quantity", this.obj.out_stock_quantity);
            formData.append("expires_date", this.obj.expires_date);
            formData.append("note", this.obj.note);
                    
            var postEvent = axios.post(this.apiUrl+'/stocks/stockInOut', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.obj.reset();
                    this.disabled = true;
                    this.fetchLatestStockData();
                    this.$toast.success(res.data.message); 
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
        
        submitImportForm: function(e) {  
            this.isSubmit = true;
            this.disabled_upload = true;
            const formData = new FormData();
            formData.append("excel_file", this.importFile);
                    
            var postEvent = axios.post(this.apiUrl+'/stocks/stockBulkInOut', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled_upload = false;
                if(res.status == 200){
                    this.importFile = '';
                    this.disabled_upload = true;
                    this.toggleImportModal();
                    this.fetchLatestStockData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled_upload = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },        

    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}, 
    watch: {}, 
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
}

/* Loader */
.overlay {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: fixed;
    background: #fff;
    opacity: 0.7;
}

.overlay__inner {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
}

.overlay__content {
    left: 58%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
}

.spinner {
    width: 75px;
    height: 75px;
    display: inline-block;
    border-width: 2px;
    border-color: rgba(255, 255, 255, 0.05);
    border-top-color: #fff;
    animation: spin 1s infinite linear;
    border-radius: 100%;
    border-style: solid;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>