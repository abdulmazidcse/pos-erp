<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Order Approval List</a></li>
                            
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

        <!-- Modal For Reject -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{btn}} 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal For View-->
        <Modal @close="toggleModalView()" :modalActive="viewModalActive">
            <div class="modal-content scrollbar-width-thin orderPreview">
                <div class="modal-header"> 
                    <button @click="toggleModalView()" type="button" class="btn btn-default">X</button>
                    <div class="title" style="text-align:center; width: 100%">
                        <h2 style="width: 100%">Purchase Order </h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row" style="padding: 0 15px;">
                                        <div class="col-md-7">
                                            <h3>24X7 Super Shop</h3>
                                            <h5>Central Store</h5>

                                            <div class="supplier_details">
                                                <span>SUPPLIER: {{ purchase_order.supplier_name }}</span><br>
                                                <span>SUPPLIER ADDRESS: {{ purchase_order.supplier_address }}</span><br>
                                                <span>CONTACT NO: {{ purchase_order.supplier_phone }}</span><br>
                                                <span>CONATCT PERSON: {{ purchase_order.supplier_contact_person_name }}</span> <br><br>

                                                <span>DELIVERY ADDRESS: </span><br>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="order_info" style="text-align: right">                                                                
                                                <h3>Purchase Order</h3> <br><br>
                                                <span>ORDER# {{ purchase_order.reference_no }}</span><br>
                                                <span>DATE: {{ purchase_order.order_date }}</span><br>
                                                <!-- <span>DELIVERY DATE: {{ purchase_order.delivery_date }}</span><br> -->
                                                <span>DELIVERY LOCATION: 24X7</span><br>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive product-table" style="border-top: 2px solid black; margin-top: 20px;">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="success item-head">
                                                    <th class="text-center" style="width: 5%">SL </th> 
                                                    <th class="text-center">Product Desc. </th> 
                                                    <th class="text-center">Sale Price</th> 
                                                    <th class="text-center">Pur. Price</th>
                                                    <th class="text-center">Ord. Qty </th>
                                                    <th class="text-center">AMOUNT</th>

                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-center">{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.mrp_price }}</td>
                                                    <td class="text-center">{{ product_item.cost_price }}</td>
                                                    <td class="text-center">{{ product_item.order_quantity }}</td>
                                                    <td class="text-center">{{ product_item.order_quantity * product_item.cost_price }}</td>
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <tr style="font-weight: bold; text-align: right;">
                                                    <td colspan="5" class="text-right">Total</td>
                                                    <td class="text-center">{{ purchase_order.total_amount }}</td>
                                                </tr>
                                            </tfoot>
                                            
                                        </table>
                                    </div>

                                    <div class="summation_details">
                                        
                                        <!-- <span class="float-right text-danger">Total Amount: <strong>{{ totalAmount }}</strong></span> -->
                                        <!-- <span class="float-right text-danger" style="margin-right: 10px;">Approve Total Amount: <strong>{{ approveTotalAmount }}</strong></span> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </Modal>


        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-centered table-nowrap w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th class="text-center" width="5%">SL </th>
                                    <th class="text-center" width="15%">PO Date</th>
                                    <th class="text-center" width="25%">PO. No</th>
                                    <th class="text-center" width="25%">Supplier </th>
                                    <th class="text-center" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }} </td>
                                    <td class="text-center">{{ item.order_date }} </td> 
                                    <td class="text-center">{{ item.reference_no }} </td> 
                                    <td class="text-center">{{ item.supplier_name }} </td>
                                    <td class="text-center actions">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click.prevent="viewDetails(item)" v-if="permission['order-approval-view']"><i class="mdi mdi-eye-outline"></i> View</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-success" @click="approveItem(item)" title="Approve Item" v-if="permission['order-approval-approve']"><i class="mdi mdi-check-outline"></i> Approve</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-danger" @click="rejectOrder(item)" title="Reject Item" v-if="permission['order-approval-reject']"><i class="mdi mdi-close-outline"></i> Reject</a>
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
            disabled: false,
            modalActive:false,
            viewModalActive:false,
            errors: {},
            btn:'SAVE',
            purchase_order: '',
            product_items: [],
            items: [],
            obj: {
                id: '',
                remarks: '',
            }
        };
    },
    created() {
        this.fetchPurchaseOrdersData();
    },
    methods: { 
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            if(!this.modalActive){
                this.editMode = false;
            } 
            this.errors = '';
            this.isSubmit = false;
            this.obj.id = ''; 
            this.obj.remarks = ''; 
        },

        toggleModalView: function() {
            this.viewModalActive = !this.viewModalActive;
        },

        fetchPurchaseOrdersData() { 
            axios.get(this.apiUrl+'/purchase_orders/getData', this.headerjson)
            .then((res) => {
                this.items = res.data.data;
                //console.log('companies res',res.data.data);
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
                this.loading = false;
            });
        },
        
        viewDetails: function(item) {
            axios.get(this.apiUrl+'/purchase_orders/'+item.id+'/view_details', this.headerjson)
            .then((res) => {
                this.purchase_order = res.data.data.purchase_order;
                this.product_items = res.data.data.purchase_order_product;
                this.toggleModalView();
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },
        
        rejectOrder: function(item) { 
            this.editMode = true;
            this.toggleModal();
            this.obj.id = item.id;  
            this.obj.remarks = item.remarks;  
        },
        
        submitForm: function(e) { 

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('remarks', this.obj.remarks); 
            
            var postEvent = axios.post(this.apiUrl+'/purchase_orders/reject/'+this.obj.id, formData, this.headers);         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchPurchaseOrdersData();
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
                console.log('catch',this.isSubmit)
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
        
        approveItem: function(item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want approve this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, Approve it!'
            }).then((result) => {
                if (result.value) { 
                    axios.post(this.apiUrl+'/purchase_orders/approve/'+item.id, {confirm_value:result.value}, this.headerjson)
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchPurchaseOrdersData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    
                }
            })
        },
        
        rejectItem: function(item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want reject this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, Reject it!'
            }).then((result) => {
                if (result.value) { 
                    axios.post(this.apiUrl+'/product_orders/reject/'+item.id,this.headers)
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchPurchaseOrdersData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    
                }
            })
        },

        deleteItem: function(item) {
            console.log('item deleyt=>',item.id);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete('http://127.0.0.1:8000/api/product_categories/'+item.id,{
                        headers:{
                          'Authorization' : '',
                          'Content-Type': 'multipart/form-data' 
                        }
                    }).then(res => {
                        if(res.status == 200){  
                            this.fetchIndexData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },

        ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
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

    }
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin.orderPreview { 
    width: 900px;
}
.modal-content.scrollbar-width-thin { 
    width: 900px;
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

.actions {
    
}

.actions a{
    margin-right: 5px;
}
</style>