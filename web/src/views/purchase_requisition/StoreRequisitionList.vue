<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Requisition </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Store Requisition List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <a href="/store-purchase-requisition"><button type="button" class="btn btn-primary float-right">
                            New Store Requisition
                        </button></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3> Requisition ({{ requisition.outlet_name }})</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>                    
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Product Details -->
                            <div class="card">
                                <!-- <div class="card-header text-left">
                                    Product Details
                                    <span class="total_quantity">Requisition Quantity: <b>{{ totalQuantity }}</b></span>
                                    <span class="approve_total_quantity">Approve Quantity: <b>{{ approveTotalQuantity }}</b></span>
                                </div> -->
                                <div class="card-body">
                                    <div style="padding: 0 15px;">
                                        <div class="requisition_details">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Requisition No: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.requisition_no }} </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Date: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.requisition_date }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Requisition Quantity: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_quantity }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Requisition Amount: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_amount }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Approve Quantity: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_approve_quantity }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Approve Amount: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_approve_amount }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Status: </label>
                                                        <div class="col-md-7 text-left" v-html="requisition.approve_status_name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Note: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.remarks }} </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h4> Product Details</h4>
                                    </div>
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
                                                    <th class="text-center">Crt Size</th>
                                                    <th class="text-center">Req. Qty </th>
                                                    <th class="text-center">App. Qty </th>
                                                    <th class="text-center">Amount</th>
                                                    <th class="text-center">App. Amount</th>

                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td>{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.product_code }}</td>
                                                    <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                    <td class="text-center">{{ product_item.cost_price }}</td>
                                                    <td class="text-center">{{ product_item.mrp_price }}</td>
                                                    <td class="text-center">{{ product_item.carton_size }}</td>
                                                    <td class="text-center">{{ product_item.req_qty }}</td>
                                                    <td class="text-center">{{ product_item.approve_qty }}</td>
                                                    <td class="text-center">{{ parseFloat(product_item.req_qty * product_item.cost_price).toFixed(2) }}</td>
                                                    <td class="text-center">{{ parseFloat(product_item.approve_qty * product_item.cost_price).toFixed(2) }}</td>
                                                    
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
                    <!-- <div class="modal-footer"> 
                        
                    </div> -->
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
                                    <th class="text-center" width="15%">Req. Date</th>
                                    <th class="text-center" width="15%">Req. No</th>
                                    <th class="text-center" width="20%">Outlet </th>
                                    <th class="text-center" width="10%">App. Status</th>
                                    <th class="text-center" width="10%">Ord. Status</th>
                                    <th class="text-center" width="20%">Remarks</th>
                                    <th class="text-center" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }} </td>
                                    <td class="text-center">{{ item.requisition_date }} </td> 
                                    <td class="text-center">{{ item.requisition_no }} </td> 
                                    <td class="text-center">{{ item.outlet_name }} </td>
                                    <td class="text-center" v-html="item.approve_status_name"></td> 
                                    <td class="text-center" v-html="item.order_status_name"></td> 
                                    <td class="text-center">{{ item.remarks }}</td> 
                                    <td class="text-center actions">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click="requisitionView(item)" v-if="permission['list-view']"><i class="fas fa-eye"></i> View</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click.prevent="printItem(item)"  ><i class="mdi mdi-printer-outline me-1"></i>Print</a> 
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" v-if="permission['list-purchase-requisition-edit'] && (item.approve_status != 2 && item.approve_status != 1)" @click="edit(item)"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-info" v-if="permission['list-requisition-purchase-order'] && (item.approve_status == 1 && item.order_status != 2)" @click="requisitionPurchaseOrder(item)"><i class="mdi mdi-arrow-right-circle-outline"></i> Purchase Order</a>
                                                <!-- <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline"></i> Remove</a> -->
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

        <Modal @close="toggleModal()"  >
            <div class="modal-content scrollbar-width-thin orderPreview" >
                <div class="modal-header"> 
                    <h3> Requisition ({{ requisition.outlet_name }})</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>                    
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Product Details -->
                            <div class="card"> 
                                <div class="card-body" id="printArea">
                                    <div style="padding: 0 15px;">
                                        <div class="requisition_details">
                                            <table class="table po_invoice">
                                                <tr>
                                                    <td colspan="4" class="text-center" style="position: relative;">                                                 
                                                        <h5 class="text-uppercase">{{ (requisition.company) ? requisition.company.name : this.retailShopName}}</h5>
                                                        <p>{{ (requisition.company) ? requisition.company.address : this.retailShopAddress }} </p>
                                                        <p>Dhaka, Bangladesh</p>
                                                        <h4>Requisition Invoice</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><label class=" text-left text-bold">Requisition No: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.requisition_no }} </div></td>
                                                    <td><label class=" text-left text-bold">Date: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.requisition_date }} </div></td> 
                                                </tr>
                                                <tr>
                                                    <td><label class="col-md-5 text-left text-bold">Requisition Quantity: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.total_quantity }} </div></td>
                                                    <td><label class="col-md-5 text-left text-bold">Requisition Amount: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.total_amount }} </div></td> 
                                                </tr>
                                                <tr>
                                                    <td><label class="col-md-5 text-left text-bold">Approve Quantity: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.total_approve_quantity }} </div></td>
                                                    <td><label class="col-md-5 text-left text-bold">Approve Amount: </label></td>
                                                    <td><div class=" text-left"> {{ requisition.total_approve_amount }} </div></td> 
                                                </tr>
                                                <tr>
                                                    <td><label class="col-md-5 text-left text-bold">Status: </label></td>
                                                    <td><div class="col-md-7 text-left" v-html="requisition.approve_status_name"></div></td>
                                                    <td><label class="col-md-5 text-left text-bold">Note: </label></td>
                                                    <td><div class="col-md-7 text-left"> {{ requisition.remarks }} </div></td> 
                                                </tr>
                                            </table>   
                                            <!-- <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Requisition No: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.requisition_no }} </div>
                                                    </div> 
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Date: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.requisition_date }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Requisition Quantity: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_quantity }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Requisition Amount: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_amount }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Approve Quantity: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_approve_quantity }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Approve Amount: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.total_approve_amount }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Status: </label>
                                                        <div class="col-md-7 text-left" v-html="requisition.approve_status_name"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-5 text-left text-bold">Note: </label>
                                                        <div class="col-md-7 text-left"> {{ requisition.remarks }} </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div> 
                                        <h5> Product Details</h5>
                                    </div>
                                    <div class="table-responsive product_table">
                                        <table class="table table-bordered table-centered table-nowrap">  
                                            <tbody v-if="product_items.length > 0">
                                                <tr class="success item-head">
                                                    <td class="text-center">SL </td> 
                                                    <td class="text-center">Name </td> 
                                                    <td class="text-center">Barcode</td> 
                                                    <td class="text-center">UOM</td>
                                                    <td class="text-center">Purchase Price</td>
                                                    <td class="text-center">MRP Price</td>
                                                    <td class="text-center">Crt Size</td>
                                                    <td class="text-center">Req. Qty </td>
                                                    <td class="text-center">App. Qty </td>
                                                    <td class="text-center">Amount</td>
                                                    <td class="text-center">App. Amount</td> 
                                                </tr> 
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td>{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.product_code }}</td>
                                                    <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                    <td class="text-center">{{ product_item.cost_price }}</td>
                                                    <td class="text-center">{{ product_item.mrp_price }}</td>
                                                    <td class="text-center">{{ product_item.carton_size }}</td>
                                                    <td class="text-center">{{ product_item.req_qty }}</td>
                                                    <td class="text-center">{{ product_item.approve_qty }}</td>
                                                    <td class="text-center">{{ parseFloat(product_item.req_qty * product_item.cost_price ).toFixed(2)}}</td>
                                                    <td class="text-center">{{ parseFloat(product_item.approve_qty * product_item.cost_price ).toFixed(2)}}</td>
                                                    
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>

                                    <div class="summation_details"> 
                                    </div>
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
            product_items: [],
            requisition: '',
            printableModalPrintActive:false,
            
        };
    },
    created() {
        this.fetchStoreRequisitionData();
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
            if(!this.modalActive){
                this.editMode = false;
                this.btn='Create';
            } 
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },

        fetchStoreRequisitionData() { 
            axios.get(this.apiUrl+'/store_requisitions', this.headers)
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
        
        add: function(e) {
            console.log('preventDefault', e)
            //this.close()
        },
        edit: function(item) { 
            this.$router.push({name: 'list-purchase-requisition-edit', params:{id:item.id}});
        },

        requisitionView: function(item) {
            // this.requisition = item;
            axios.get(this.apiUrl+'/store_requisitions/'+item.id, this.headers)
            .then((res) => {
                console.log(res);
                this.requisition = res.data.data.requisition_data;
                this.product_items = res.data.data.requisition_products;
                this.toggleModal();
            })
            .catch((err) => {
                // this.$toast.error(err.response.data.message);
            })
        },

        requisitionPurchaseOrder: function(item)
        {
            this.$router.push({name: 'list-requisition-purchase-order', params:{id: item.id}});
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

        // togglePrintablePrintModal() {
        //     console.log('printableModalPrintActive', this.printableModalPrintActive)
        //    // this.printableModalPrintActive = !this.printableModalPrintActive;
        // },
        printItem: function (item) { 
            axios.get(this.apiUrl+'/store_requisitions/'+item.id, this.headers)
            .then((res) => {
                console.log(res);
                this.requisition = res.data.data.requisition_data;
                this.product_items = res.data.data.requisition_products;
                this.printContent('printArea');
            }) 
            .catch((err) => { 
                this.$toast.error(err);
            })
        },

        printContent(document_id) { 
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [ 
                    this.baseUrlPrintCSS+'/assets/css/bootstrap-print.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            this.$htmlToPaper(document_id, options);
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

.actions {
    
}

.actions a{
    margin-right: 5px;
}
</style>