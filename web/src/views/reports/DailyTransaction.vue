<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Daily Transaction</a></li>
                                
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
                            <h3 style="text-align: center;">Daily Transaction</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <!-- <div class="">
                                        <label for="supplier_id"> Branch </label>
                                        <select class="form-control" id="supplier_id" v-model="search_terms.branch_id" @change="onkeyPress('branch_id')">
                                            <option value="">--- Select Branch ---</option>
                                            <option v-for="(branch, i) in branches" :key="i" :value="branch.id">{{ branch.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.branch_id">
                                            {{errors.branch_id[0]}}
                                        </div>
                                    </div> -->
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="from_date"> From Date *</label>
                                        <input type="date" class="form-control" id="from_date" v-model="search_terms.from_date" @change="onkeyPress('from_date')">
                                        <div class="invalid-feedback" v-if="errors.from_date">
                                            {{errors.from_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="">
                                        <label for="to_date"> To Date *</label>
                                        <input type="date" class="form-control" id="to_date" v-model="search_terms.to_date" @change="onkeyPress('to_date')">
                                        <div class="invalid-feedback" v-if="errors.to_date">
                                            {{errors.to_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="filterDailyTransaction()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                        <a href="javascript:void(0);" style="margin-left: 5px;" class=" btn btn-primary" @click.prevent="printItem(item)" ><i class="mdi mdi-printer-outline me-1"></i> </a>
                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <!-- <div class="card-header" v-if="items.length > 0">
                            <h4 style="text-align: center;">{{ supplier_name }}</h4>
                            <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>
                        </div> -->
                        <div class="card-body">
                            <h4 style="text-align: left;">Daily Transaction From {{ from_date }} TO {{ to_date }}</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered table-centered w-100" v-if="!loading">
                                    <thead class="table-light">
                                        <tr class="success item-head">
                                            <th width="10%" style="text-align: center">Voucher Date </th>
                                            <th width="15%" style="text-align: center">Voucher Code</th>
                                            <th width="10%" style="text-align: center">Voucher Type</th>
                                            <th width="20%" style="text-align: center">Account Head </th>
                                            <th width="25%" style="text-align: center">Notes</th>
                                            <th width="10%" style="text-align: center">Debit Amount</th>
                                            <th width="10%" style="text-align: center">Credit Amount</th>
                                        </tr>
                                    </thead>

                                    <tbody v-if="items.length > 0">
                                        <tr class="" v-for="(item, index) in items" :key="index">
                                            <td style="text-align: center"> {{ item.vdate }}</td>
                                            <td style="text-align: left">{{ item.vcode }} </td> 
                                            <td style="text-align: left">{{ item.vtype }} </td> 
                                            <td style="text-align: left">{{ item.ledger_head }}</td>
                                            <td style="text-align: left; max-width: 400px !important;">{{ item.notes }}</td>
                                            <td style="text-align: right">{{ item.debit_amount }}</td>
                                            <td style="text-align: right">{{ item.credit_amount }}</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-center"> Total </th>
                                            <th class="text-right">{{ total_debit_amount }}</th>
                                            <th class="text-right">{{ total_credit_amount }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
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

                        <Modal @close="toggleModal()"  >
                            <div class="modal-content scrollbar-width-thin orderPreview" >
                                <div class="modal-header"> 
                                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                    <h3 style="width: 100%">Product Sales Report</h3>
                                </div>
                                <div class="modal-body " id="printArea" >
                                    <div class="table-responsive product_table">
                                        <table class="table po_invoice no-border"  >
                                            <tr style="border: none">
                                                <td colspan="2" class="text-center" style="position: relative; border: none">
                                                    <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                    <p>{{ this.retailShopAddress }} </p>
                                                    <p>Dhaka, Bangladesh</p> 
                                                    <h4 style="text-align: center;">Daily Transaction From {{ from_date }} TO {{ to_date }}</h4>  
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                            <thead class="table-light">
                                                <tr class="success item-head">
                                                    <th width="10%" style="text-align: center">Voucher Date </th>
                                                    <th width="15%" style="text-align: center">Voucher Code</th>
                                                    <th width="10%" style="text-align: center">Voucher Type</th>
                                                    <th width="20%" style="text-align: center">Account Head </th>
                                                    <th width="25%" style="text-align: center">Notes</th>
                                                    <th width="10%" style="text-align: center">Debit Amount</th>
                                                    <th width="10%" style="text-align: center">Credit Amount</th>
                                                </tr>
                                            </thead>

                                            <tbody v-if="items.length > 0">
                                                <tr class="" v-for="(item, index) in items" :key="index">
                                                    <td style="text-align: center"> {{ item.vdate }}</td>
                                                    <td style="text-align: left">{{ item.vcode }} </td> 
                                                    <td style="text-align: left">{{ item.vtype }} </td> 
                                                    <td style="text-align: left">{{ item.ledger_head }}</td>
                                                    <td style="text-align: left; max-width: 400px !important;">{{ item.notes }}</td>
                                                    <td style="text-align: right">{{ item.debit_amount }}</td>
                                                    <td style="text-align: right">{{ item.credit_amount }}</td>
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th colspan="4">   </th>
                                                    <th style="text-align: right"> Total </th>
                                                    <th style="text-align: right">{{ total_debit_amount }}</th>
                                                    <th style="text-align: right">{{ total_credit_amount }}</th>
                                                </tr>
                                            </tfoot>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                        </Modal>
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

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            search_terms: new Form({
                from_date: '',
                to_date: '',
                branch_id: '',
            }),
            opening_balance: 0,
            from_date: '',
            to_date: '',
            total_debit_amount: 0,
            total_credit_amount: 0,
        };
    },
    created() {
        this.fetchDailyTransaction();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        filterDailyTransaction() {
            this.isSubmit = true;
            this.disabled = true;
            this.fetchDailyTransaction();
        },

        fetchDailyTransaction() { 
            var formData    = new FormData();
            formData.append('from_date', this.search_terms.from_date);
            formData.append('to_date', this.search_terms.to_date);
            axios.post(this.apiUrl+'/reports/daily-transaction', formData, this.headers)
            .then((res) => {
                // console.log("res.data.data", res.data.data);
                this.items = res.data.data.transactions;
                this.total_debit_amount = res.data.data.total_debit_amount,
                this.total_credit_amount = res.data.data.total_credit_amount,
                this.from_date  = res.data.data.from_date;
                this.to_date = res.data.data.to_date;
                this.disabled = false;
                this.isSubmit = false;  
                this.search_terms.reset();
            })
            .catch((err) => { 
                this.disabled = false;
                this.isSubmit = false;
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        
        checkRequiredPrimary()
        {
            if(this.search_terms.from_date && this.search_terms.to_date) {
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
        printItem: function () {   
            this.printContent('printArea');
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
        }


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
    border: none !important;
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
</style>