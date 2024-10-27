<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Receive List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right ">  
                        <a href="/purchase-receive"><button type="button" class="btn btn-primary float-right">
                            Store Purchase Receive
                        </button></a> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h2>Purchase Receive </h2>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>

                <div class="title" style="text-align:center;"> 
                </div>
                    

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12"> 
                            <div class="card"> 
                                <div class="card-body">
                                    <div class="requisition_details">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Purchase Receive Date: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.purchase_date }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Reference No: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.reference_no }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Vendor Name: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.supplier_name }} </div>
                                                </div>

                                                <div class="row" v-if="receive_data.receive_type == 'SR'">
                                                    <label class="col-md-4 text-left text-bold">Stock Location: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.outlet_name }} </div>
                                                </div>

                                                <div class="row" v-else>
                                                    <label class="col-md-4 text-left text-bold">Stock Location: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.warehouse_name }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Note: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.remarks }} </div>
                                                </div>
                                                
                                            </div>

                                            <div class="col-md-6">

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Total Amount: </label>
                                                    <div class="col-md-7 text-left"> {{ parseFloat(receive_data.total_amount).toFixed(2) }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Discount Amount: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.total_commission_value }} </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Additional Discount: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.additional_discount }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Addiotional Cost: </label>
                                                    <div class="col-md-7 text-left"> {{ receive_data.additional_cost }} </div>
                                                </div>

                                                <div class="row">
                                                    <label class="col-md-4 text-left text-bold">Net Amount: </label>
                                                    <div class="col-md-7 text-left"> {{ (parseFloat(receive_data.total_amount).toFixed(2) - parseFloat(receive_data.additional_discount)) + parseFloat(receive_data.additional_cost)}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <hr>
                                    <h4> Product Details</h4>
                                    <div class="table-responsive product_table">
                                        <table class="table table-bordered table-centered table-nowrap">
                                            <thead class="table-light">
                                                <tr class="success item-head">
                                                    <th class="text-center" style="width: 5%">SL </th> 
                                                    <th class="text-center">Name </th> 
                                                    <th class="text-center">Barcode</th> 
                                                    <th class="text-center">UOM</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-center">Weight </th>
                                                    <th class="text-center">Pur. Price</th>
                                                    <th class="text-center">Amount</th>

                                                </tr>
                                            </thead>

                                            <tbody v-if="product_items.length > 0">
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-center">{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.product_code }}</td>
                                                    <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                    <td class="text-center">{{ product_item.receive_quantity }}</td>
                                                    <td class="text-center">{{ product_item.receive_weight }} </td>
                                                    <td class="text-center">{{ parseFloat(product_item.cost_price).toFixed(2) }}</td>
                                                    <td class="text-center" v-if="product_item.receive_weight != 0"> 0 || {{ parseFloat(product_item.receive_weight * product_item.cost_price).toFixed(2) }}</td>
                                                    <td class="text-center" v-else>{{ parseFloat(product_item.receive_quantity * product_item.cost_price).toFixed(2) }} || 0</td>
                                                    
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <tr style="font-weight: bold;">
                                                    <td colspan="4" class="text-center"> Total</td>
                                                    <td class="text-center">{{ totalRcvQuantity }}</td>
                                                    <td class="text-center">{{ totalRcvWeight }}</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-center">{{ totalQtyAmount.toFixed(2) }} || {{ totalWeightAmount.toFixed(2) }}</td>
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

        <Modal @close="toggleVoucherPrintModal()" :modalActive="voucherModalPrintActive">
            <div class="modal-content scrollbar-width-thin orderPreview">
                <div class="modal-header" style="text-align:right; display: block;"> 
                    <button @click="toggleVoucherPrintModal()" type="button" class="btn btn-default">X</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12"> 
                            <div class="card" > 
                                <div class="container" id="printArea">
                                    <div class="text-center col-xs-12">
                                        <div class="row">
                                            <p class="text-uppercase text-center mt-2"><strong> {{ $store.getters.userData.user.outlet_name }}</strong></p>
                                            <p class="text-uppercase text-center mt-2">{{ $store.getters.userData.user.outlet_address }}</p><br>  
                                        </div>                                                                                         
                                    </div>
                                    <div class="d-flex justify-content-between "> 
                                        <div class=" col-xs-6">
                                            <div class="row"> 
                                                <div class="  text-left">Purchase Receive Date: {{ receive_data.purchase_date }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left">Reference No: {{ receive_data.reference_no }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left"> Vendor Name: {{ receive_data.supplier_name }} </div>
                                            </div>

                                            <div class="row" v-if="receive_data.receive_type == 'SR'"> 
                                                <div class="  text-left">Stock Location:  {{ receive_data.outlet_name }} </div>
                                            </div>

                                            <div class="row" v-else> 
                                                <div class="  text-left">Stock Location: {{ receive_data.warehouse_name }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left">Note: {{ receive_data.remarks }} </div>
                                            </div>
                                        </div> 
                                        <div class=" col-xs-6">
                                            <div class="row"> 
                                                <div class=" text-left">Total Amount:  {{ receive_data.total_amount }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left">Discount Amount:  {{ receive_data.total_commission_value }} </div>
                                            </div>
                                            
                                            <div class="row"> 
                                                <div class=" text-left">Additional Discount:  {{ receive_data.additional_discount }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left">Addiotional Cost:  {{ receive_data.additional_cost }} </div>
                                            </div>

                                            <div class="row"> 
                                                <div class=" text-left">Net Amount:  {{ receive_data.net_amount }}</div>
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>
                                    <h5> Product Details</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-centered table-nowrap"> 
                                            <tbody v-if="product_items.length > 0">
                                                <tr class="success  ">
                                                    <td class="text-center" style="width: 5%">SL </td> 
                                                    <td class="text-center">Name </td> 
                                                    <td class="text-center">Barcode</td> 
                                                    <td class="text-center">UOM</td>
                                                    <td class="text-center">Qty</td>
                                                    <td class="text-center">Weight </td>
                                                    <td class="text-center">Pur. Price</td>
                                                    <td class="text-center">Amount</td> 
                                                </tr>  
                                                <tr v-for="(product_item, i) in product_items" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td class="text-center">{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.product_code }}</td>
                                                    <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                    <td class="text-center">{{ product_item.receive_quantity }}</td>
                                                    <td class="text-center">{{ product_item.receive_weight }} </td>
                                                    <td class="text-center">{{ parseFloat(product_item.cost_price).toFixed(2) }}</td>
                                                    <td class="text-center" v-if="product_item.receive_weight != 0"> 0 || {{ parseFloat(product_item.receive_weight * product_item.cost_price).toFixed(2) }}</td>
                                                    <td class="text-center" v-else>{{ parseFloat(product_item.receive_quantity * product_item.cost_price).toFixed(2) }} || 0</td>
                                                    
                                                </tr>
                                                <tr  >
                                                    <td colspan="4" class="text-center"> Total</td>
                                                    <td class="text-center">{{ totalRcvQuantity }}</td>
                                                    <td class="text-center">{{ totalRcvWeight }}</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-center">{{ totalQtyAmount }} || {{ totalWeightAmount }}</td>
                                                </tr>
                                            </tbody>  
                                        </table>
                                    </div> 
                                    <!-- <div class="my-5 d-flex" style="display: none;">
                                        <p class="mr-fourty">Taka in word: </p>
                                        <number-to-word :number="Number(totalQtyAmount).toFixed(2)" /> -->
                                        <!-- <p><strong>Twelve Thousand One Hundred Seventy Only</strong></p> -->
                                    <!-- </div> -->
                                     
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
                                    <th class="text-center" width="5%">SL</th>
                                    <!-- <th class="text-center" width="5%">Order ID</th> -->
                                    <th class="text-center" width="15%">Receive Date</th>
                                    <th class="text-center" width="15%">Reference No</th>
                                    <th class="text-center" width="15%">Challan No</th>
                                    <th class="text-center" width="20%">Amount</th>
                                    <th class="text-center" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }} </td> 
                                    <!-- <td class="text-center">{{ item.purchase_order_id }} </td>  -->
                                    <td class="text-center">{{ item.purchase_date }} </td> 
                                    <td class="text-center">{{ item.reference_no }} </td> 
                                    <td class="text-center">{{ item.challan_no ?? 'N/A' }} </td> 
                                    <td class="text-center">{{ item.total_amount }}</td> 
                                    <td class="text-center actions">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click.prevent="viewDetails(item)"><i class="mdi mdi-eye-outline"></i> View</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click="printItem(item)"><i class="mdi mdi-printer-outline me-1"></i>Print</a>
                                                <!-- <a href="#" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a> -->
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
import NumberToWord from './../page/NumberToWord.vue';   
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        NumberToWord
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
            items: [],
            receive_data: [],
            product_items: [],
            voucherModalPrintActive:false,
            vitems: [],
            vitem: '',
        };
    },
    created() {
        this.fetchPurchaseReceivesData();
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
            console.log('then',this.isSubmit)
        },

        fetchPurchaseReceivesData() { 
            axios.get(this.apiUrl+'/purchase_receives', this.headerjson)
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

        toggleVoucherPrintModal() {
            this.voucherModalPrintActive = !this.voucherModalPrintActive;
        },

        viewDetails: function(item) {

            // this.toggleModal();
            axios.get(this.apiUrl+'/purchase_receives/'+item.id, this.headers)
            .then((res) => {
                // console.log(res);
                this.receive_data = res.data.data.receive_data;
                this.product_items = res.data.data.receive_product;
                this.toggleModal();
            })
            .catch((err) => {
                // this.$toast.error(err.response.data.message);
            })
        },

        deleteItem: function(item) {
            // console.log('item deleyt=>',item.id);
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
        
        printItem: function (item) {
            axios.get(this.apiUrl+'/purchase_receives/'+item.id, this.headerjson)
            .then((res) => {
                this.receive_data = res.data.data.receive_data;
                this.product_items = res.data.data.receive_product;
                this.printContent('printArea');
            })
            .catch((err) => {
                //this.$toast.error(err.response.data.message)
            });
        },

        printContent(document_id) {
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: ['/assets/css/app.min.css',
                    this.baseUrlPrintCSS+'/assets/css/bootstrap.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            this.$htmlToPaper(document_id, options);
        },
        printModal() {

            // var css_link = this.baseUrl+'/assets/css/print.css';

            // console.log("css link===", css_link);

            // // Create a reference to the print-friendly template
            // const printContent = document.getElementById("printArea");

            // // Create a new window to print the template
            // const printWindow = window.open("", "Print Window", "height=600,width=800");

            // // Write the template to the print window
            // printWindow.document.write('<html><head><title></title>');
            // printWindow.document.write('<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">');
            // // printWindow.document.write('<link rel="stylesheet" type="text/css" href="../assets/print.css">');
            // printWindow.document.write('<link rel="stylesheet" type="text/css" href="'+this.baseUrl+'/assets/css/print.css">');
            // printWindow.document.write('</head><body >');
            // printWindow.document.write(printContent.innerHTML);
            // printWindow.document.write('</body></html>');

            // // Print the window and close it
            // printWindow.print();
            // printWindow.close();
        },

        ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
    },

    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        totalRcvQuantity: function() {
            return this.product_items.reduce((total, item) => {
                return total + parseFloat(item.receive_quantity);
            }, 0)
        },

        totalRcvWeight: function() {
            return this.product_items.reduce((total, item) => {
                return total + parseFloat(item.receive_weight);
            }, 0)
        },

        totalQtyAmount: function() {
            return this.product_items.reduce((total, item) => {
                if(item.receive_weight == 0){
                    return total + parseFloat(item.receive_quantity * item.cost_price);
                }else{
                    return total + 0;
                }
            }, 0)
        },
        totalWeightAmount: function() {
            return this.product_items.reduce((total, item) => {
                if(item.receive_weight != 0){
                    return total + parseFloat(item.receive_weight * item.cost_price);
                }else{
                    return total + 0;
                }
            }, 0)
        }


    }
}
</script>
<style  >
.modal-content.scrollbar-width-thin { 
    width: 70vw;
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