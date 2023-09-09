<template>
    <transition  >
    <div class="container-fluid card-body">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Purchase </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Purchase Order List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="redirectRoute()">
                            Add New Purchase Order
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin orderPreview">
                <div class="modal-header"> 
                    <h3 style="width: 100%">Purchase Order View</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    <!-- <div class="title" style="text-align:center; width: 100%">
                        
                    </div> -->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="8" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : '24/7 Retail Shop Limited' }}</h5>
                                                <p>{{ (purchase_order.company) ? purchase_order.company.address : 'Plot -394, Road -29, Mohakhali DOHS' }} </p>
                                                <p>Dhaka, Bangladesh</p>
                                                <span class="invoice_logo">
                                                    <img src="assets/images/logo.png" alt="">
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Vendor Code: <span>{{ purchase_order.supplier_phone }}</span></p>
                                                <p>Vendor Name & Address: <span class="text-uppercase">{{ purchase_order.supplier_name }}</span></p>
                                                <p>
                                                    <span class="text-uppercase">{{ purchase_order.supplier_address }}</span><br>
                                                    <!-- <span class="text-uppercase">Dhaka - 1215</span> -->
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>TIN No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.supplier_contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.supplier_phone }}</span></p>
                                            </td>
                                            <td colspan="4" v-if="purchase_order.outlets">
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ purchase_order.outlets.name }}</span><br>
                                                    <span class="text-uppercase">{{ purchase_order.outlet_address }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.outlets.contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.outlets.outlet_number }}</span></p>
                                            </td>
                                            <td colspan="4" v-else>
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.warehouses) ? purchase_order.warehouses.name : '24/7 Warehouse' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.warehouses) ? purchase_order.warehouses.address : 'Plot -394, Road -29, Mohakhali DOHS' }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouses.contact_person_name : 'Khandakar Kudrat-e-Khuda (Pulack)' }}</span></p>
                                                <p>Contact No: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouses.warehouse_number : '01684727596' }}</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <h4 class="text-uppercase">Purchase Order</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Billing Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : '' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.address : '' }}</span>
                                                </p>
                                            </td>
                                            <td colspan="4">
                                                <p style="overflow: hidden;">
                                                    <span class="float-left">PO No: {{ purchase_order.reference_no }}</span>
                                                    <span class="float-right">PO Date: {{ purchase_order.order_date }}</span>
                                                </p>
                                                <p>RFQ No: <span></span></p>
                                                <p>PR No: <span></span></p>
                                                <p>Currency: <span>BDT</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <table class="table table-bordered table-centered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">SL No.</th>
                                                            <th class="text-center">Code</th>
                                                            <th class="text-center">Description</th>
                                                            <th class="text-center">UOM</th>
                                                            <th class="text-center">Qty</th>
                                                            <th class="text-center">Unit Price</th>
                                                            <th class="text-center">Discount</th>
                                                            <th class="text-center">Net Amount</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr v-for="(product_item, i) in product_items" :key="i">
                                                            <td class="text-center">{{ i + 1 }}</td>
                                                            <td class="text-center">{{ product_item.product_code }}</td>
                                                            <td class="text-center">{{ product_item.product_name }}</td>
                                                            <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                            <td class="text-right">{{ product_item.order_quantity }}</td>
                                                            <td class="text-center">{{ product_item.cost_price }}</td>
                                                            <td class="text-center">{{ product_item.order_discount_amount }}</td>
                                                            <td class="text-right">{{ ((product_item.order_quantity * product_item.cost_price) - product_item.order_discount_amount) }}</td>
                                                        </tr>

                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Total</td>
                                                            <td class="text-right text-bold">{{ purchase_order.total_quantity }}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-right text-bold">{{ purchase_order.total_value }}</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Other Charges</td>
                                                            <td colspan="4" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">VAT</td>
                                                            <td colspan="4" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr style="font-weight: bold;">
                                                            <td colspan="4" class="text-right">Gross Amount</td>
                                                            <td colspan="4" class="text-right">{{ purchase_order.total_amount }}</td>
                                                        </tr>
                                                        <!-- <tr>
                                                            <td colspan="8">
                                                                <p>Amount In Words: <span>{{ amount_in_words }}</span></p>
                                                            </td>
                                                        </tr> -->
                                                        
                                                    </tbody>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
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
                        <table class="table table-bordered table-centered table-nowarp w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th class="text-center" width="5%">SL </th>
                                    <th class="text-center" width="15%">PO. Date</th>
                                    <th class="text-center" width="15%">PO. No</th>
                                    <th class="text-center" width="15%">Req. No</th>
                                    <th class="text-center" width="20%">Supplier </th>
                                    <th class="text-center" width="10%">App. Status</th>
                                    <th class="text-center" width="10%">PO Send</th>
                                    <th class="text-center" width="10%">Rcv. Status</th>
                                    <th class="text-center" width="15%">Amount</th>
                                    <th class="text-center" width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }} </td>
                                    <td class="text-center">{{ item.order_date }} </td> 
                                    <td class="text-center">{{ item.reference_no }} </td> 
                                    <td class="text-center">{{ item.store_requisition_no }} </td> 
                                    <td class="text-center">{{ item.supplier_name }} </td>
                                    <td class="text-center" v-html="item.approve_status_name"></td> 
                                    <td class="text-center" v-html="item.send_status_name"></td> 
                                    <td class="text-center" v-html="item.receive_status_name"></td> 
                                    <td class="text-center">{{ item.total_amount }}</td> 
                                    <td class="text-center actions"> 
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click.prevent="viewDetails(item)" ><i class="mdi mdi-eye-outline"></i> View</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click.prevent="printItem(item)" v-if="permission['order-view']"><i class="mdi mdi-printer-outline me-1"></i>Print</a> 
                                                <a href="javascript:void(0);" class="dropdown-item text-info" @click.prevent="sendPO(item)" v-if="permission['order-invoice-send'] && (item.approve_status == 1 && item.send_status == 0)"><i class="mdi mdi-check-circle-outline"></i> Send PO</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-info" @click.prevent="generatePDF(item)" v-if="permission['order-invoice-download'] && (item.approve_status == 1)"><i class="mdi mdi-download-outline"></i> PDF Download</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" v-if="permission['purchase-order-edit'] && (item.approve_status != 2 && item.approve_status != 1 && item.store_requisition_id == 0)" @click="edit(item)"><i class="mdi mdi-circle-edit-outline"></i> Edit</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" v-else-if="permission['order-purchase-requisition-edit'] && (item.approve_status != 2 && item.approve_status != 1 && item.store_requisition_id != 0)" @click="editRequisitionOrder(item)"><i class="mdi mdi-circle-edit-outline"></i> Edit</a>
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

        <div class="overlay" v-if="pageloading">
            <div class="overlay__inner">
                <div class="overlay__content">
                    <img src="../../assets/image/loading.gif" alt="Loading..." style="width:130px">
                </div>
            </div>
        </div>

        <Modal @close="toggleModal()" :modalActive="printableModalPrintActive">
            <div class="modal-content scrollbar-width-thin orderPreview" >
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    <h3 style="width: 100%">Purchase Order View</h3>
                </div>
                <div class="modal-body" id="printArea" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="font-size: 9px;">
                                <div class="card-body">
                                    <div class="title" style="text-align:center; width: 100%">
                                        <h3 style="width: 100%">Purchase Order View</h3>
                                    </div>
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="8" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : this.retailShopName }}</h5>
                                                <p>{{ (purchase_order.company) ? purchase_order.company.address : this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p>
                                                 
                                                <h4>Requisition Invoice</h4>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Vendor Code: <span>{{ purchase_order.supplier_phone }}</span></p>
                                                <p>Vendor Name & Address: <span class="text-uppercase">{{ purchase_order.supplier_name }}</span></p>
                                                <p>
                                                    <span class="text-uppercase">{{ purchase_order.supplier_address }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>TIN No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.supplier_contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.supplier_phone }}</span></p>
                                            </td>
                                            <td colspan="4" v-if="purchase_order.outlets">
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ purchase_order.outlets.name }}</span><br>
                                                    <span class="text-uppercase">{{ purchase_order.outlet_address }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ purchase_order.outlets.contact_person_name }}</span></p>
                                                <p>Contact No: <span>{{ purchase_order.outlets.outlet_number }}</span></p>
                                            </td>
                                            <td colspan="4" v-else>
                                                <p>Delivery Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.warehouses) ? purchase_order.warehouses.name : '24/7 Warehouse' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.warehouses) ? purchase_order.warehouses.address : 'Plot -394, Road -29, Mohakhali DOHS' }}</span><br>
                                                </p>
                                                <p>VAT Reg No: <span></span></p>
                                                <p>Contact Person: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouses.contact_person_name : 'Khandakar Kudrat-e-Khuda (Pulack)' }}</span></p>
                                                <p>Contact No: <span>{{ (purchase_order.warehouses) ? purchase_order.warehouses.warehouse_number : '01684727596' }}</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <h5 class="text-uppercase">Purchase Order</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="width: 50%">
                                                <p>Billing Address: 
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.name : '' }}</span><br>
                                                    <span class="text-uppercase">{{ (purchase_order.company) ? purchase_order.company.address : '' }}</span>
                                                </p>
                                            </td>
                                            <td colspan="4">
                                                <p style="overflow: hidden;">
                                                    <span class="float-left">PO No: {{ purchase_order.reference_no }}</span>
                                                    <span class="float-right">PO Date: {{ purchase_order.order_date }}</span>
                                                </p>
                                                <p>RFQ No: <span></span></p>
                                                <p>PR No: <span></span></p>
                                                <p>Currency: <span>BDT</span></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <table class="table table-bordered table-centered"> 
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center">SL No.</td>
                                                            <td class="text-center">Code</td>
                                                            <td class="text-center">Description</td>
                                                            <td class="text-center">UOM</td>
                                                            <td class="text-center">Qty</td>
                                                            <td class="text-center">Unit Price</td>
                                                            <td class="text-center">Discount</td>
                                                            <td class="text-center">Net Amount</td>
                                                        </tr>
                                                        <tr v-for="(product_item, i) in product_items" :key="i">
                                                            <td class="text-center">{{ i + 1 }}</td>
                                                            <td class="text-center">{{ product_item.product_code }}</td>
                                                            <td class="text-center">{{ product_item.product_name }}</td>
                                                            <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                            <td class="text-right">{{ product_item.order_quantity }}</td>
                                                            <td class="text-center">{{ product_item.cost_price }}</td>
                                                            <td class="text-center">{{ product_item.order_discount_amount }}</td>
                                                            <td class="text-right">{{ ((product_item.order_quantity * product_item.cost_price) - product_item.order_discount_amount) }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4" class="text-right">Total</td>
                                                            <td class="text-right text-bold">{{ purchase_order.total_quantity }}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-right text-bold">{{ purchase_order.total_value }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">Other Charges</td>
                                                            <td colspan="4" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">VAT</td>
                                                            <td colspan="4" class="text-right">0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">Gross Amount</td>
                                                            <td colspan="4" class="text-right">{{ purchase_order.total_amount }}</td>
                                                        </tr>                                                         
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
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
import html2canvas from 'html2canvas';
import jspdf from 'jspdf';
// let converter = require('number-to-words');

export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            pageloading: false,
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
            purchase_order: [],
            amount_in_words: 0,
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
            printableModalPrintActive:false,
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

        sendPO: function(item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You confirm send PO is done!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.value) { 
                    axios.post(this.apiUrl+'/purchase_orders/send_po/'+item.id, {confirm_value:result.value}, this.headerjson)
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchPurchaseOrdersData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        // console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    
                }
            })
        },


        generatePDF(item) {
            this.pageloading = true;
            axios.get(this.apiUrl+'/purchase_orders/'+item.id+'/generatePDF', this.headerjson)
            .then((res) => {
                console.log(res);
                this.pageloading = false;
                window.open(res.data.data.file_path, '_blank');

                // window.html2canvas = html2canvas;
                // const doc = new jspdf('l', 'px', 'a4');
                
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
                this.pageloading = false;
            });
        },

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        redirectRoute() {
            this.$router.push({name: "order-add"});
        },

        fetchPurchaseOrdersData() { 
            axios.get(this.apiUrl+'/purchase_orders', this.headerjson)
            .then((res) => {
                this.items = res.data.data;
                // console.log(res.data.data);
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
                // console.log(res.data.data);
                this.purchase_order = res.data.data.purchase_order;
                // this.amount_in_words = converter.toWords(res.data.data.purchase_order.total_amount);
                this.product_items = res.data.data.purchase_order_product;
                this.toggleModal();
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        edit: function(item) { 
            this.$router.push({name: 'purchase-order-edit', params:{id:item.id}});
        },
        editRequisitionOrder: function(item) { 
            this.$router.push({name: 'order-purchase-requisition-edit', params:{id:item.id}});
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

        togglePrintablePrintModal() {
            this.printableModalPrintActive = !this.printableModalPrintActive;
        },
        printItem: function (item) { 
            axios.get(this.apiUrl+'/purchase_orders/'+item.id+'/view_details', this.headerjson)
            .then((res) => {
                // console.log(res.data.data);
                this.purchase_order = res.data.data.purchase_order; 
                this.product_items = res.data.data.purchase_order_product;
                this.printContent('printArea');
            })
            .catch((err) => {
                // console.log('err.response.data.message', err)
                this.$toast.error(err);
            })
        },

        printContent(document_id) {
            // console.log('this.baseUrlPrintCSS', this.baseUrlPrintCSS+'/assets/css/print.css')
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
    border: none !important;
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

/* .overlay-loader {
    position: absolute;
    top:50%;
    left:50%;
    width:100%;
    height:100%;
    background-color:#eceaea;
    background-size: 50px;
    background-repeat:no-repeat;
    background-position:center;
    z-index:10000000;
    opacity: 0.4;
    filter: alpha(opacity=40);
} */

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

/** PO Invoice Design */
.po_invoice {
    border: 1px solid #000;
}

.po_invoice>:not(:first-child) {
    border: 0;
    border-top: 1px solid #000;
}

.po_invoice td {
    vertical-align: top !important;
}
.po_invoice td p {
    margin-bottom: 0px;
    padding: 2px 5px!important;
    color: #282828;
}

.po_invoice td h5, .po_invoice td h4, .po_invoice td h3, .po_invoice td h2 {
    margin: 0px;
    text-transform: uppercase;
    padding: 2px 5px!important;
    color: #282828;
}

.po_invoice td table {
    margin-bottom: 0;
}

span.invoice_logo {
    position: absolute;
    right: 15px;
    top: 0;
}
span.invoice_logo img {
    width: 140px;
    height: 100%;
}

.text-uppercase {
    text-transform: uppercase;
}

</style>