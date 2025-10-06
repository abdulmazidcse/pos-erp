<template>
    <transition name="fade" mode="out-in">
    <div v-if="isVisible" class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Transfer </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Stock Transfer List</a></li>
                            
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

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>

                <div class="title" style="text-align:center;">
                    <h2 style="width: 100%">Stock Transfer </h2>
                    <h3 style="width: 100%">{{ transfer_data.title }}</h3>
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
                                    <div class="transfer_details">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Reference No: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.reference_no }} </div>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Transfer Date: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.purchase_date }} </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Transfer Quantity: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.total_quantity }} </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold"> Amount: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.total_cost }} </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Shipping Cost: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.shipping_cost }} </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Total Amount: </label>
                                                    <div class="col-md-7 text-left"> {{ transfer_data.grand_total }} </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h4> Product Details</h4>
                                    <div class="product_table">
                                        <table class="table table-bordered table-sm">
                                            <thead class="tableFloatingHeaderOriginal">
                                                <tr class="success item-head">
                                                    <th>SL </th> 
                                                    <th>Name </th> 
                                                    <th>Barcode</th> 
                                                    <th>Quantity</th>
                                                    <th>Amount</th>

                                                </tr>
                                            </thead>

                                            <tbody v-if="transfer_products.length > 0">
                                                <tr v-for="(product_item, i) in transfer_products" :key="i">
                                                    <td>{{ i + 1 }}</td>
                                                    <td>{{ product_item.product_name }}</td>
                                                    <td>{{ product_item.product_code }}</td>
                                                    <td>{{ product_item.quantity }}</td>
                                                    <td>{{ product_item.total_amount }}</td>
                                                    
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

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100" v-if="!loading">
                            <thead class="tableFloatingHeaderOriginal">
                                <tr class="border success item-head">
                                    <th width="5%">SL</th>
                                    <th width="15%">Reference No</th>
                                    <th width="15%">Transfer Date</th>
                                    <th width="15%">Transfer From</th>
                                    <th width="15%">Transfer TO</th>
                                    <th width="5%">Item</th>
                                    <th width="5%">Qty</th>
                                    <th width="10%">Amount</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td>{{ index + 1 }} </td> 
                                    <td>{{ item.reference_no }} </td> 
                                    <td>{{ item.transfer_date }} </td> 
                                    <td>{{ item.transfer_from }}</td> 
                                    <td>{{ item.transfer_to }}</td> 
                                    <td>{{ item.total_item }}</td> 
                                    <td>{{ item.total_quantity}}</td> 
                                    <td>{{ item.grand_total}}</td> 
                                    <td class="actions">
                                        <a href="#" @click.prevent="viewDetails(item)"><i class="fas fa-eye"></i> </a>
                                        <!-- <a href="#" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a> -->
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
// import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
// import { ref, onMounted } from "vue";
// import Form from 'vform'
import axios from 'axios';
export default {
    name: 'StockTransferList',
    components: {
        Modal
    },
    data() {
        return {
            isVisible: true, 
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            items: [],
            transfer_data: [],
            transfer_products: [],
        };
    },
    created() {
        this.fetchStockTransferData();
    },
    methods: { 
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },

        fetchStockTransferData() { 
            axios.get(this.apiUrl+'/stock_transfers',this.headerjson)
            .then((res) => {
                this.items = res.data.data;
            }).finally(( ) => {
                //console.log('companies finally',ress);
                this.loading = false;
            });
        },

        viewDetails: function(item) {

            // this.toggleModal();
            axios.get(this.apiUrl+'/stock_transfers/'+item.id, this.headerjson)
            .then((res) => {
                console.log(res);
                this.transfer_data = res.data.data.transfer_data;
                this.transfer_products = res.data.data.transfer_products;
                this.toggleModal();
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

    }, 
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
}
</script>
<style scoped>
 

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