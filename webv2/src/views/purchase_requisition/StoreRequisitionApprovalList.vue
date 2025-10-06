<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Requisition </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Requisition Approval List</a></li>
                            
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


        <!-- Approve Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin approveModal">
                <div class="modal-header"> 
                    <h3>Requisition Details</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitApproveForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="requisition_date">Date *</label>
                                    <input type="date" class="form-control border" id="requisition_date" v-model="obj.requisition_date" readonly>
                                
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="requisition_no">Requisition No *</label>
                                    <input type="text" class="form-control border" id="requisition_no" v-model="obj.requisition_no" readonly>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control border " v-model="obj.remarks" id="remarks" rows="3" placeholder="Remarks Here!"></textarea>
                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <!-- Product Details -->
                                <div class="card">
                                    <div class="card-header text-left">
                                        Product Details
                                        <input type="hidden" v-model="obj.total_quantity">
                                        <!-- <span class="total_quantity">Requisition Quantity: <b>{{ totalQuantity }}</b></span>
                                        <span class="approve_total_quantity">Approve Quantity: <b>{{ approveTotalQuantity }}</b></span> -->
                                    </div>
                                    <div class="card-body">
                                        <!-- <div class="form-group">
                                            <input type="text" class="form-control border" id="search_product" placeholder="Search Product">
                                        </div> -->
                                        <div class="table-responsive product_table">
                                            <table class="table table-bordered table-centered table-nowrap">
                                                <thead class="table-light">
                                                    <tr class="success item-head">
                                                        <th class="text-center">SL </th> 
                                                        <th class="text-center">Name </th> 
                                                        <th class="text-center">Barcode</th> 
                                                        <th class="text-center">UOM</th>
                                                        <th class="text-center">Purchase Price</th>
                                                        <th class="text-center">MRP Price</th>
                                                        <th class="text-center">Crt. Size</th>
                                                        <th class="text-center">Req. Qty </th>
                                                        <th class="text-center" width="10%">App. Qty </th>
                                                        <th class="text-center">Amount</th>
                                                        <th class="text-center">App. Amount</th>
                                                    </tr>
                                                </thead>

                                                <tbody v-if="product_items.length > 0">
                                                    <tr v-for="(product_item, i) in product_items" :key="i">
                                                        <td class="text-center">{{ i + 1 }}</td>
                                                        <td class="text-center">{{ product_item.product_name }}</td>
                                                        <td class="text-center">{{ product_item.product_code }}</td>
                                                        <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                        <td class="text-center">{{ product_item.cost_price }}</td>
                                                        <td class="text-center">{{ product_item.mrp_price }}</td>
                                                        <td class="text-center">{{ product_item.carton_size }}</td>
                                                        <td class="text-center">{{ product_item.req_qty }}</td>
                                                        <td class="text-center"><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.approve_qty"></td>
                                                        <td class="text-center">{{ parseFloat(product_item.req_qty * product_item.cost_price).toFixed(2)  }}</td>
                                                        <td class="text-center">{{ parseFloat(product_item.approve_qty * product_item.cost_price).toFixed(2)  }}</td>
                                                        
                                                    </tr>
                                                </tbody>
                                                
                                            </table>
                                        </div>

                                        <div class="summation_details">
                                            
                                            <!-- <span class="float-right text-danger">Total Amount: <strong>{{ totalAmount }}</strong></span>
                                            <span class="float-right text-danger" style="margin-right: 10px;">Approve Total Amount: <strong>{{ approveTotalAmount }}</strong></span> -->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabled" v-if="isShow">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> {{btn}} 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Reject Modal -->
        <Modal @close="toggleModalTwo()" :modalActive="rejectModalActive">
            <div class="modal-content scrollbar-width-thin rejectModal">
                <div class="modal-header"> 
                    <button @click="toggleModalTwo()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitRejectForm()" enctype="multipart/form-data" >
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control border " v-model="rejectObj.remarks" id="remarks" rows="3" placeholder="Remarks Here!"></textarea>
                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> {{btn}} 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="table-responsive card-body">
                        <table class="table table-bordered table-centered table-nowrap w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th class="text-center" width="5%">SL</th>
                                    <th class="text-center" width="20%">Requisition Date</th>
                                    <th class="text-center" width="20%">Requisition No</th>
                                    <th class="text-center" width="20%">Outlet </th>
                                    <th class="text-center" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }} </td>
                                    <td class="text-center">{{ item.requisition_date }} </td> 
                                    <td class="text-center">{{ item.requisition_no }} </td> 
                                    <td class="text-center">{{ item.outlet_name }} </td>
                                    <td class="text-center actions">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-success" @click="showItem(item)" title="Approve Item" v-if="permission['approval-approve']"><i class="fas fa-eye"></i> View</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-success" @click="approveItem(item)" title="Approve Item" v-if="permission['approval-approve']"><i class="mdi mdi-check-outline"></i> Approve</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-danger" @click="rejectRequisition(item)" title="Reject Item" v-if="permission['approval-reject']"><i class="mdi mdi-close-outline"></i> Reject</a>
                                                <!-- item-->
                                          </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

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
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            isShow:false,
            disabled: false,
            modalActive:false,
            rejectModalActive:false,
            errors: {},
            btn:'SAVE',
            items: [],
            product_items: [],
            obj: new Form({
                id: '',
                outlet_id: '',
                requisition_date: '',
                requisition_no: '',
                total_quantity: 0,
                total_value: '',
                total_amount: 0, 
                remarks: ''
            }),

            rejectObj: new Form({
                id: '',
                remarks: '',
            })
        };
    },
    created() {
        this.fetchStoreRequisitionData();
    },
    methods: { 
        // Approve Modal
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            if(!this.modalActive){
                this.editMode = false;
            } 
            this.errors = '';
            this.isSubmit = false;
            this.obj.reset(); 
            this.product_items = [];
        },

        // Reject Modal
        toggleModalTwo: function() {
            this.rejectModalActive = !this.rejectModalActive;  
            if(!this.rejectModalActive){
                this.editMode = false;
            } 
            this.errors = '';
            this.isSubmit = false;
            this.rejectObj.reset();
        },

        fetchStoreRequisitionData() { 
            axios.get(this.apiUrl+'/store_requisitions/getRequisitionData', this.headers)
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => { 
                console.log('errr => ', err.response) 
            }).finally((ress) => {
                this.loading = false;
            });
        },
        
        rejectRequisition: function(item) { 
            this.editMode = true;
            this.btn = "SUBMIT";
            this.toggleModalTwo();
            this.rejectObj.id = item.id;  
            this.rejectObj.remarks = item.remarks;  
        },
        
        submitRejectForm: function(e) { 

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('remarks', this.rejectObj.remarks); 
            
            var postEvent = axios.post(this.apiUrl+'/store_requisitions/'+this.rejectObj.id+'/rejectStoreRequisition', formData, this.headers);         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModalTwo();
                    this.fetchStoreRequisitionData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
                console.log('then',this.isSubmit)
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
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
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },

        inputChange()
        {
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_value = this.totalValue;
            this.obj.total_amount = this.totalAmount;
        },
        
        

        // For Requisition Approval
        approveItem: function(item) {
            this.btn='APPROVE';
            this.editMode = true;
            this.isShow = true;
            axios.get(this.apiUrl+'/store_requisitions/'+item.id+'/getRequisitionProduct', this.headers)
            .then((res) => {
                if(res.status == 200) {
                    this.toggleModal();
                    this.obj.fill(item); 
                    this.product_items = res.data.data.requisition_products; 
                }else{
                    this.$toast.error(res.data.message);
                }
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }); 
        },
        showItem: function(item) {
            this.btn='APPROVE';
            this.editMode = true;
            this.isShow = false;
            axios.get(this.apiUrl+'/store_requisitions/'+item.id+'/getRequisitionProduct', this.headers)
            .then((res) => {
                if(res.status == 200) {
                    this.toggleModal();
                    this.obj.fill(item); 
                    this.product_items = res.data.data.requisition_products; 
                }else{
                    this.$toast.error(res.data.message);
                }
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }); 
        },

        submitApproveForm: function(e) { 

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('remarks', this.obj.remarks); 
            formData.append('products', JSON.stringify(this.product_items)); 
            
            var postEvent = axios.post(this.apiUrl+'/store_requisitions/'+this.obj.id+'/approveStoreRequisition', formData, this.headers);         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchStoreRequisitionData();
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

    },

    destroyed() {},
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
                
                let qty = item.req_qty;
                if(item.req_qty == '') {
                   qty = 0;
                }
                return total + parseFloat(qty); 
            },0);
        },

        approveTotalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                
                let qty = item.approve_qty;
                if(item.approve_qty == '') {
                   qty = 0;
                }
                return total + parseFloat(qty); 
            },0);
        }, 
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.cost_price * item.qty); 
            },0); 
        },
        
        totalAmount: function(){
            return this.product_items.reduce(function(total, item){
                // let item_value = (item.cost_price * item.req_qty);
                //let item_discount = ((item_value * item.disc_percent) / 100);
                // return total + (item_value - item_discount); 
                return total + (item.cost_price * item.req_qty); 
            },0); 
        },
        
        approveTotalAmount: function(){
            return this.product_items.reduce(function(total, item){
                // let item_value = (item.cost_price * item.req_qty);
                //let item_discount = ((item_value * item.disc_percent) / 100);
                // return total + (item_value - item_discount); 
                return total + (item.cost_price * item.approve_qty); 
            },0); 
        },
    }
}
</script>
<style scoped>
 
.modal-content.scrollbar-width-thin.rejectModal { 
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

.total_quantity {
    float: right;
    color: red;
}
.approve_total_quantity {
    float: right;
    color: red;
    margin-right: 10px;
}

.product_table {
    padding: 0;
    min-height: auto;
}

.product_table tbody td input {
    border-bottom: 1px solid #cecece;
}

.actions {
    
}

.actions a{
    margin-right: 5px;
}
</style>