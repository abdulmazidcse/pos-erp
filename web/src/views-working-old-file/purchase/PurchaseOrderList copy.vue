<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-md-12 ">
                <div class="row ">
                    <div class="form-group col-md-6 float-left">
                        <div class="float-left ">
                            <ul class="breadcrumb">
                                <li><a href="/">Home</a></li>
                                <li>Purchase Order List</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-12 ">
                        <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add New
                        </button> -->
                        <Modal @close="toggleModal()" :modalActive="modalActive">
                            <div class="modal-content scrollbar-width-thin">
                                <div class="modal-header"> 
                                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                    <div class="title" style="text-align:center; width: 100%">
                                        <h2 style="width: 100%">Purchase Order </h2>
                                    </div>
                                </div>

                                <!-- <div class="title" style="text-align:center;">
                                    <h2 style="width: 100%">Purchase Order </h2>
                                </div> -->
                                    

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- <div class="card">
                                                <div class="card-body">
                                                    
                                                </div>
                                            </div> -->
                                            <!-- Product Details -->
                                            <div class="card">
                                                <!-- <div class="card-header text-left">
                                                    Product Details
                                                    <span class="total_quantity">Requisition Quantity: <b>{{ totalQuantity }}</b></span>
                                                    <span class="approve_total_quantity">Approve Quantity: <b>{{ approveTotalQuantity }}</b></span>
                                                </div> -->
                                                <div class="card-body">
                                                    <div class="requisition_details">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Reference No: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.reference_no }} </div>
                                                                </div>
                                                                
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Purchase Order Date: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.order_date }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Delivery Date: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.delivery_date }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Order Quantity: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_quantity }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Order Amount: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_value }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Free Quantity: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_free_quantity }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Free Amount: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_free_amount }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Discount Amount: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_commission_value }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Total Amount: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.total_amount }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Note: </label>
                                                                    <div class="col-md-7 text-left"> {{ purchase_order.remarks }} </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="row">
                                                                    <label class="col-md-4 text-left text-bold">Status: </label>
                                                                    <div class="col-md-7 text-left" v-html="purchase_order.approve_status_name"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <h4> Product Details</h4>
                                                    <div class="table-responsive product_table">
                                                        <table class="table table-bordered table-sm">
                                                            <thead class="tableFloatingHeaderOriginal">
                                                                <tr class="success item-head">
                                                                    <th>SL </th> 
                                                                    <th>Name </th> 
                                                                    <th>Barcode</th> 
                                                                    <th>UOM</th>
                                                                    <th>Purchase Price</th>
                                                                    <th>MRP Price</th>
                                                                    <th>Carton Size</th>
                                                                    <th>Order. Qty </th>
                                                                    <th>Order Amount</th>

                                                                </tr>
                                                            </thead>

                                                            <tbody v-if="product_items.length > 0">
                                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                                    <td>{{ i + 1 }}</td>
                                                                    <td>{{ product_item.product_name }}</td>
                                                                    <td>{{ product_item.product_code }}</td>
                                                                    <td>{{ product_item.purchase_unit }}</td>
                                                                    <td>{{ product_item.cost_price }}</td>
                                                                    <td>{{ product_item.mrp_price }}</td>
                                                                    <td>{{ product_item.carton_size }}</td>
                                                                    <td>{{ product_item.order_quantity }}</td>
                                                                    <td>{{ product_item.order_quantity * product_item.cost_price }}</td>
                                                                    
                                                                </tr>
                                                            </tbody>
                                                            
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
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped dt-responsive nowarp w-100" v-if="!loading">
                            <thead class="tableFloatingHeaderOriginal">
                                <tr class="border success item-head">
                                    <th width="20%">Supplier </th>
                                    <th width="15%">Challan No</th>
                                    <th width="15%">Challan Date</th>
                                    <th width="10%">Approve Status</th>
                                    <th width="20%">Remarks</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td>{{ item.supplier_name }} </td>
                                    <td>{{ item.reference_no }} </td> 
                                    <td>{{ item.order_date }} </td> 
                                    <td v-html="item.approve_status_name"></td> 
                                    <td>{{ item.remarks }}</td> 
                                    <td class="actions">                                        
                                        <a href="#" @click.prevent="viewDetails(item)"><i class="fas fa-eye"></i> </a>
                                        <a href="#" v-if="item.approve_status != 2 && item.approve_status != 1 && item.store_requisition_id == 0" @click="edit(item)"><i class="fas fa-edit"></i> </a>
                                        <a href="#" v-else-if="item.approve_status != 2 && item.approve_status != 1 && item.store_requisition_id != 0" @click="editRequisitionOrder(item)"><i class="fas fa-edit"></i> </a>
                                        <a href="#" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a>
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
            errors: {},
            btn:'Create',
            items: [],
            productShowBtn: false, 
            suppliers: [],
            outlets: [],
            purchase_order: '',
            product_items: [],
            obj: {
                supplier_id: '',
                supplier_payment_type: '',
                number_of_po: '',
                supply_schedule: '',
                order_date: '',
                delivery_date: '',
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
            },
        };
    },
    created() {
        this.fetchPurchaseOrdersData();
    },
    methods: { 

        checkedRequiredPrimary(){
            if(this.obj.supplier_id && this.obj.order_date &&  this.obj.delivery_date){
                this.productShowBtn = true;
            }else{
                 this.productShowBtn = false;
            }
        },

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },

        fetchPurchaseOrdersData() { 
            axios.get(this.apiUrl+'/purchase_orders')
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
            axios.get(this.apiUrl+'/purchase_orders/'+item.id+'/view_details', this.headers)
            .then((res) => {
                console.log(res);
                this.purchase_order = res.data.data.purchase_order;
                this.product_items = res.data.data.purchase_order_product;
                this.toggleModal();
            })
            .catch((err) => {
                // this.$toast.error(err.response.data.message);
            })
        },        
        edit: function(item) { 
            this.$router.push({name: 'PurchaseOrderEdit', params:{id:item.id}});
        },
        editRequisitionOrder: function(item) { 
            this.$router.push({name: 'StoreRequisitionPurchaseOrderEdit', params:{id:item.id}});
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
                    // axios.delete('http://127.0.0.1:8000/api/product_categories/'+item.id,{
                    //     headers:{
                    //       'Authorization' : '',
                    //       'Content-Type': 'multipart/form-data' 
                    //     }
                    // }).then(res => {
                    //     if(res.status == 200){  
                    //         this.fetchIndexData();
                    //         this.$toast.success(res.data.message); 
                    //     }else{
                    //         this.$toast.error(res.data.message);
                    //     }
                    //     console.log(res.data)
                    // }).catch(err => {  
                    //     this.$toast.error(err.response.data.message); 
                    // }) 
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
    computed: {}
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 90%;
    display: block;
    margin: 0 auto;
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