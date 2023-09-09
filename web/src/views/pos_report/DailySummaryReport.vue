<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Daily Summary Report</a></li>
                                
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
                        <div class="card-header">
                            <h3 style="text-align: center;">Daily Summary Report</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <!-- <label for="src_date"> Date </label> -->
                                        <input type="date" class="form-control" id="src_date" v-model="tableData.src_date" @change="onkeyPress('src_date')">
                                        <div class="invalid-feedback" v-if="errors.src_date">
                                            {{errors.src_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-3">
                                    <div class="">
                                        <label for="to_date"> To </label>
                                        <input type="date" class="form-control" id="to_date" v-model="tableData.to_date" @change="onkeyPress('to_date')">
                                        <div class="invalid-feedback" v-if="errors.to_date">
                                            {{errors.to_date[0]}}
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-md-3">
                                    <div class="mt-0">
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="filterDailySummaryReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Daily Summary Report -->
                    <div class="report_area" v-if="!loading">

                        <!-- Daily Purchase Report -->
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-align: center;">Purchase Report</h4>
                                <h4 style="text-align: center;" v-if="src_date != ''">Date: {{ src_date }} </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-centered mb-0 w-100 dt-responsive nowrap no-footer dtr-inline collapsed">            
                                        <thead class="table-light">
                                            <tr class="border success item-head">
                                                <th>SN</th>
                                                <th>Reference No</th>
                                                <th>Supplier</th>
                                                <th>G. Total</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                            </tr>
                                        </thead>

                                        <tbody v-if="purchase_items.length > 0">
                                            <tr v-for="(pitem, i) in purchase_items" :key="i">
                                                <td class="text-center">{{ i + 1 }} </td>
                                                <td class="text-left">{{ pitem.reference_no }} </td>
                                                <td class="text-left">{{ pitem.suppliers ? pitem.suppliers.name : 'N/A' }} </td>
                                                <td class="text-right">{{ parseFloat(pitem.net_amount).toFixed(2) }} </td>
                                                <td class="text-right">{{ parseFloat(pitem.paid_amount).toFixed(2) }} </td>
                                                <td class="text-right">{{ parseFloat(pitem.net_amount - pitem.paid_amount).toFixed(2) }} </td>
                                            </tr>
                                        </tbody>

                                        <tbody v-else>
                                            <tr>
                                                <td colspan="6"> No data available here!</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr style="font-weight: bold;">
                                                <td colspan="3"> Total </td>
                                                <td class="text-right">{{ totalPurchaseNetAmount.toFixed(2) }}</td>
                                                <td class="text-right">{{ totalPurchasePaidAmount.toFixed(2) }}</td>
                                                <td class="text-right">{{ totalPurchaseDueAmount.toFixed(2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div> 
                        </div>

                        
                        <!-- Daily Sales Report -->
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-align: center;">Sales Report</h4>  
                                <h4 style="text-align: center;" v-if="src_date != ''">Date: {{ src_date }} </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-centered mb-0 w-100 dt-responsive nowrap no-footer dtr-inline collapsed">            
                                        <thead class="table-light">
                                            <tr class="border success item-head">
                                                <th>SN</th>
                                                <th>Reference No</th>
                                                <th>Customer</th>
                                                <th>Total Payable</th>
                                                <th>Discount</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                            </tr>
                                        </thead>

                                        <tbody v-if="sales_items.length > 0">
                                            <tr v-for="(sitem, i) in sales_items" :key="i">
                                                <td class="text-center">{{ i + 1 }}</td>
                                                <td class="text-left">{{ sitem.invoice_number }}</td>
                                                <td class="text-left">{{ sitem.customer_name }}</td>
                                                <td class="text-right">{{ parseFloat(sitem.grand_total).toFixed(2) }}</td>
                                                <td class="text-right">{{ parseFloat(sitem.order_discount).toFixed(2) }}</td>
                                                <td class="text-right">{{ parseFloat(sitem.paid_amount).toFixed(2) }}</td>
                                                <td class="text-right">{{ parseFloat(sitem.grand_total - sitem.paid_amount).toFixed(2) }}</td>
                                            </tr>
                                        </tbody>

                                        <tbody v-else>
                                            <tr>
                                                <td colspan="7"> No data available here!</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr style="font-weight: bold;">
                                                <td colspan="3"> Total </td>
                                                <td class="text-right">{{ totalSalesPayable.toFixed(2) }}</td>
                                                <td class="text-right">{{ totalSalesDiscount.toFixed(2) }}</td>
                                                <td class="text-right">{{ totalSalesPaid.toFixed(2) }}</td>
                                                <td class="text-right">{{ totalSalesDue.toFixed(2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>  

                            </div> 
                        </div>

                        <!-- Daily Collection Report-->
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-align: center;">Collection Report</h4>  
                                <h4 style="text-align: center;" v-if="src_date != ''">Date: {{ src_date }} </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-centered mb-0 w-100 dt-responsive nowrap no-footer dtr-inline collapsed">            
                                        <thead class="table-light">
                                            <tr class="border success item-head">
                                                <th>SN</th>
                                                <th>Reference No</th>
                                                <th>Customer</th>
                                                <th>Payment Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>

                                        <tbody v-if="collection_items.length > 0">
                                            <tr v-for="(citem, i) in collection_items" :key="i">
                                                <td class="text-center">{{ i + 1 }}</td>
                                                <td class="text-left">{{ citem.sales.invoice_number }}</td>
                                                <td class="text-left">{{ citem.sales.customer_name }}</td>
                                                <td class="text-left">{{ citem.paying_by }}</td>
                                                <td class="text-right">{{ parseFloat(citem.amount).toFixed(2) }}</td>
                                            </tr>
                                        </tbody>

                                        <tbody v-else>
                                            <tr>
                                                <td colspan="5"> No data available here!</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr style="font-weight: bold;">
                                                <td colspan="4"> Total </td>
                                                <td class="text-right">{{ totalCollectAmount.toFixed(2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>  

                            </div> 
                        </div>

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
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';
// import Datatable from '@/components/Datatable.vue';
// import Pagination from '@/components/Pagination.vue';

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        // Datatable,
        // Pagination,
    },
    
    data() {
        return {
            loading: true,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            sales_items: [],
            purchase_items: [],
            collection_items: [],
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 
            tableData: {
                src_date: '',
            }, 
            src_date: '',
        };
    },
    created() {
        this.getDailySummaryReport();

        console.log("location host", location.host);
    },

    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        filterDailySummaryReport()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.getDailySummaryReport();
        },

        // For Pagination 
        getDailySummaryReport(url = this.apiUrl+'/reports/daily-summary-report') {
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                console.log("response.data.data", response.data.data);
                let data = response.data.data; 
                this.purchase_items = data.data.purchase_items;
                this.sales_items = data.data.sales_items;
                this.collection_items = data.data.collection_items;
                this.src_date  = data.src_date;
                
                this.isSubmit  = false;
                this.tableData.src_date   = '';


            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
            .finally((fres) => {
                this.loading = false;
            });
        },

        checkRequiredPrimary()
        {
            if(this.tableData.src_date != '') {
                this.disabled = false;
            }else{
                this.disabled = true;
            }
        },
        onkeyPress: function(field) { 
            this.checkRequiredPrimary();
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
        totalPurchaseNetAmount() {
            return this.purchase_items.reduce(function(total, item) {
                return total + parseFloat(item.net_amount);
            }, 0);
        },
        totalPurchasePaidAmount() {
            return this.purchase_items.reduce(function(total, item) {
                return total + parseFloat(item.paid_amount);
            }, 0);
        },
        totalPurchaseDueAmount() {
            return this.purchase_items.reduce(function(total, item) {
                return total + parseFloat(parseFloat(item.net_amount) - parseFloat(item.paid_amount));
            }, 0);
        },
        totalSalesPayable() {
            return this.sales_items.reduce(function(total, item) {
                return total + parseFloat(item.grand_total);
            }, 0);
        },
        totalSalesDiscount() {
            return this.sales_items.reduce(function(total, item) {
                return total + parseFloat(item.order_discount);
            }, 0);
        },
        totalSalesPaid() {
            return this.sales_items.reduce(function(total, item) {
                return total + parseFloat(item.paid_amount);
            }, 0);
        },
        totalSalesDue() {
            return this.sales_items.reduce(function(total, item) {
                return total + parseFloat(parseFloat(item.grand_total) - parseFloat(item.paid_amount));
            }, 0);
        },

        totalCollectAmount() {
            return this.collection_items.reduce(function(total, item) {
                return total + parseFloat(item.amount);
            }, 0);
        },
    }
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

.actions a{
    margin-right: 5px;
}

.card-body .table-responsive {
    padding: 0;
}
</style>