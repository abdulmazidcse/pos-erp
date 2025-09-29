<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Balance Sheet</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right ">  
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="text-align: center;">Balance Sheet</h3>
                        </div>

                        <div class="card-body"> 
                            <div class="row searchBox"> 

                                <div class="col-md-3">
                                    <div class="">
                                        <label for="outlet_id"> Company </label>
                                        <select class="form-control"  v-model="search_terms.company_id" @change="this.filterComapany($event.target.value)">
                                            <option value="">--- Select Company ---</option>
                                            <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                        </select>
                                    </div>
                                </div> 

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="as_on_date"> As On Date *</label>
                                        <input type="date" class="form-control" id="as_on_date" v-model="search_terms.as_on_date" @change="onkeyPress('as_on_date')">
                                        <div class="invalid-feedback" v-if="errors.as_on_date">
                                            {{errors.as_on_date[0]}}
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
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="filterBalanceSheet()">
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

                            <div class="row" v-if="!loading">
                                <!-- Assets -->
                                <div class="col-md-12">
                                    <table class="table table-nowrap w-100">
                                        <tbody style="border-bottom: 0;">
                                            <tr>
                                                <td style="padding: 0; border-bottom: 0; width: 100%;">
                                                    <!-- Assets -->
                                                    <table class="table table-bordered w-100" style="margin-bottom: 0;">
                                                        <thead class="table-light">
                                                            <tr class="success item-head">
                                                                <th width="70%" style="text-align: center">Particular</th>
                                                                <th width="30%" style="text-align: center">Amount</th>
                                                            </tr>

                                                            <tr class="success item-head">
                                                                <th colspan="2" style="text-align: left; text-transform: uppercase;">Assets</th>
                                                            </tr>
                                                        </thead>
                                                        <AccountBalanceSheet v-for="(item, i) in asset_items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountBalanceSheet>
                                                        <tbody>
                                                            <tr>
                                                                <th width="70%" style="text-align: left; text-transform: uppercase;">Total Assets</th>
                                                                <th width="30%" style="text-align: right;">{{ asset_balance }}</th>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <!-- Liabilities -->
                                                    <table class="table table-bordered w-100" style="margin-bottom: 0;">
                                                        <thead class="table-light">
                                                            <tr class="success item-head">
                                                                <th colspan="2" style="text-align: left; text-transform: uppercase;">Liabilities & Equity</th>
                                                            </tr>
                                                        </thead>
                                                        <AccountBalanceSheet v-for="(item, i) in liability_items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountBalanceSheet>
                                                        <tbody>
                                                            <tr>
                                                                <th width="70%" style="text-align: left; text-transform: uppercase;">Total Liabilities & Equity</th>
                                                                <th width="30%" style="text-align: right;">{{ liability_equity_balance }}</th>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>

                                            </tr>
                                            
                                        </tbody>

                                    </table>
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

                    <Modal @close="toggleModal()"  >
                        <div class="modal-content scrollbar-width-thin orderPreview" >
                            <div class="modal-header"> 
                                <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                <h3 style="width: 100%">Balance Sheet</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Balance Sheet</h4> 
                                                <h4 style="text-align: center;" v-if="search_terms.as_on_date">As On Date {{ search_terms.as_on_date }} </h4>  
                                            </td>
                                        </tr>
                                    </table> 
                                    <table class="table table-nowrap w-100" v-if="!loading">  
                                        <tbody style="border-bottom: 0;">
                                            <tr>
                                                <td style="padding: 0; border-bottom: 0; width: 100%;">
                                                    <!-- Assets -->
                                                    <table class="table table-bordered w-100" style="margin-bottom: 0;">
                                                        <thead class="table-light">
                                                            <tr class="success item-head">
                                                                <th width="70%" style="text-align: center">Particular</th>
                                                                <th width="30%" style="text-align: center">Amount</th>
                                                            </tr>

                                                            <tr class="success item-head">
                                                                <th colspan="2" style="text-align: left; text-transform: uppercase;">Assets</th>
                                                            </tr>
                                                        </thead>
                                                        <AccountBalanceSheet v-for="(item, i) in asset_items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountBalanceSheet>
                                                        <tbody>
                                                            <tr>
                                                                <th width="70%" style="text-align: left; text-transform: uppercase;">Total Assets</th>
                                                                <th width="30%" style="text-align: right;">{{ asset_balance }}</th>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <!-- Liabilities -->
                                                    <table class="table table-bordered w-100" style="margin-bottom: 0;">
                                                        <thead class="table-light">
                                                            <tr class="success item-head">
                                                                <th colspan="2" style="text-align: left; text-transform: uppercase;">Liabilities & Equity</th>
                                                            </tr>
                                                        </thead>
                                                        <AccountBalanceSheet v-for="(item, i) in liability_items" :key="i" :account="item" :level="1" :spaces="5" :code_active="codeActive"></AccountBalanceSheet>
                                                        <tbody>
                                                            <tr>
                                                                <th width="70%" style="text-align: left; text-transform: uppercase;">Total Liabilities & Equity</th>
                                                                <th width="30%" style="text-align: right;">{{ liability_equity_balance }}</th>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>

                                            </tr>
                                            
                                        </tbody>

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

import AccountBalanceSheet from "@/components/AccountBalanceSheet";

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        AccountBalanceSheet
    },
    data() {
        return {
            loading: false,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            asset_items: [],
            companies: [],
            liability_items: [],
            asset_balance: 0,
            liability_equity_balance: 0,
            search_terms: new Form({
                as_on_date: '',
                supplier_id: '',
                company_id: ''
            }),
            opening_balance: 0,
            from_date: '',
            to_date: '',
            show_with_code: false,
            codeActive: false,
        };
    },
    created() {
        this.fetchCompanies(); 
        // this.fetchBalanceSheet();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        filterBalanceSheet()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.fetchBalanceSheet(this.search_terms.companyId, this.search_terms.as_on_date);
        },
        filterComapany(companyId)
        {
            this.isSubmit = true;
            this.disabled = true;
            this.search_terms.company_id = companyId;
            this.fetchBalanceSheet(companyId, this.search_terms.as_on_date);
        },

        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
                if (this.companies.length === 1) { 
                    this.fetchBalanceSheet(this.companies[0].id);
                    this.search_terms.company_id = this.companies[0].id;
                }
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        fetchBalanceSheet(companyId, asOnDate='') { 
            this.loading = true;
            if(asOnDate) {
                var getEvent = axios.get(this.apiUrl+'/reports/balance-sheet?company_id='+companyId+'as_on_date='+asOnDate, this.headerjson);
            }else{
                getEvent =axios.get(this.apiUrl+'/reports/balance-sheet?company_id='+companyId, this.headerjson);
            }
            
            getEvent.then((res) => {
                this.asset_items = res.data.data.assets_accounts;
                this.liability_items = res.data.data.liability_equity_accounts;
                this.asset_balance = res.data.data.asset_balance;
                this.liability_equity_balance = res.data.data.liability_equity_balance;
                this.to_date = res.data.data.as_on_date;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
                this.isSubmit = false;
                this.disabled = false;
            });
        },
        
        checkRequiredPrimary()
        {
            if(this.search_terms.as_on_date) {
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

@media print {
    div.searchBox {
        display: none;
    }

    table {
        font-size: 12px !important;
    }
}
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