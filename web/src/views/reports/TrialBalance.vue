<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Trial Balance</a></li>
                                
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
                            <h3 style="text-align: center;">Trial Balance</h3>
                        </div>

                        <div class="card-body">
                            <div class="row"> 
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
                                    <div class="mb-3 mt-3 float-right">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="show_code"  v-model="show_with_code" @change="showLedgerCode">
                                            <label class="form-check-label" for="show_code">Show with code </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="filterTrialBalance()">
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
                        <div class="card-body">
                            <h4 v-if="from_date != '' && to_date != ''">Trial Balance From {{ from_date }} to {{ to_date }}</h4> 

                            <table class="table table-bordered table-nowrap w-100" v-if="!loading">
                                <thead class="table-light">
                                    <tr class="success item-head">
                                        <th width="40%" style="text-align: center">Particulars</th>
                                        <th width="10%" style="text-align: center">Opening Balance</th>
                                        <th width="10%" style="text-align: center">Debit</th>
                                        <th width="10%" style="text-align: center">Credit </th>
                                        <th width="10%" style="text-align: center">Closing Balance</th>
                                    </tr>
                                </thead>
                                <AccountTrialBalanceVue v-for="(item, i) in items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountTrialBalanceVue>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">Total Balance</th>
                                        <th class="text-right">{{ total_opening_balance }}</th>
                                        <th class="text-right">{{ total_debit_balance }}</th>
                                        <th class="text-right">{{ total_credit_balance }}</th>
                                        <th class="text-right">{{ total_closing_balance }}</th>
                                    </tr>
                                </tfoot>
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

                    <Modal @close="toggleModal()"  >
                        <div class="modal-content scrollbar-width-thin orderPreview" >
                            <div class="modal-header"> 
                                <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                <h3 style="width: 100%">Trial Balance</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Trial Balance</h4> 
                                                <h4 style="text-align: center;">From {{ search_terms.from_date }} TO {{ search_terms.to_date }}</h4>  
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered table-nowrap w-100" v-if="!loading">
                                        <thead class="table-light">
                                            <tr class="success item-head">
                                                <th width="40%" style="text-align: center">Particulars</th>
                                                <th width="15%" style="text-align: center">Opening Balance</th>
                                                <th width="10%" style="text-align: center">Debit</th>
                                                <th width="10%" style="text-align: center">Credit </th>
                                                <th width="15%" style="text-align: center">Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <AccountTrialBalanceVue v-for="(item, i) in items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountTrialBalanceVue>
                                        <tfoot>
                                            <tr>
                                                <th class="text-center">Total Balance</th>
                                                <th class="text-right">{{ total_opening_balance }}</th>
                                                <th class="text-right">{{ total_debit_balance }}</th>
                                                <th class="text-right">{{ total_credit_balance }}</th>
                                                <th class="text-right">{{ total_closing_balance }}</th>
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
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';

import AccountTrialBalanceVue from "@/components/AccountTrialBalance";

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        AccountTrialBalanceVue
    },
    data() {
        return {
            loading: false,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            total_opening_balance: 0,
            total_debit_balance: 0,
            total_credit_balance: 0,
            total_closing_balance: 0,
            search_terms: new Form({
                from_date: '',
                to_date: '',
                supplier_id: '',
            }),
            opening_balance: 0,
            from_date: '',
            to_date: '',
            show_with_code: false,
            codeActive: false,
        };
    },
    created() {
        this.fetchTrialBalance();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        filterTrialBalance() {
            this.isSubmit = true;
            this.disabled = true;

            this.fetchTrialBalance();
        },

        fetchTrialBalance() { 
            this.loading = true;
            var url_params = ''
            if(this.search_terms.from_date != '' && this.search_terms.to_date != '') {
                if(url_params == '') {
                    url_params = '?from_date='+this.search_terms.from_date+'&to_date='+this.search_terms.to_date
                }else{
                   url_params = '&from_date='+this.search_terms.from_date+'&to_date='+this.search_terms.to_date 
                }
            }
            axios.get(this.apiUrl+'/reports/trial-balance'+url_params, this.headerjson)
            .then((res) => {
                this.items = res.data.data.accounts;
                this.total_opening_balance = res.data.data.total_opening_balance;
                this.total_debit_balance = res.data.data.total_debit_balance;
                this.total_credit_balance = res.data.data.total_credit_balance;
                this.total_closing_balance = res.data.data.total_closing_balance;
                this.from_date = res.data.data.from_date;
                this.to_date = res.data.data.to_date;
                this.search_terms.reset();
            })
            .catch((err) => { 
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
                this.isSubmit = false;
                this.disabled = false;
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

        showLedgerCode: function(event) {
            var checked_val = this.show_with_code;
            if(checked_val) {
                this.codeActive = true;
            }else{
                this.codeActive = false;
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