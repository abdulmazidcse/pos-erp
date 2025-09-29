<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Bank Book</a></li>
                                
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
                <div class="col-12"> 
                    <div class="col-md-10">
                        <div class="row">  
                            <div class="col-md-6">
                                <div class="">
                                    <label for="outlet_id"> Company </label> 
                                    <select class="form-control" @change="fetchAccountLedgers($event.target.value)">
                                        <option value="">--- Select Company ---</option>
                                        <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                    </select>
                                </div>
                            </div> 
                        </div>
                    </div> 
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="text-align: center;">Bank Book</h3>
                        </div>

                        <div class="card-body">
                            <div class="row"> 
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="supplier_id"> Ledger *</label><br>
                                        <treeselect 
                                            v-model="search_terms.ledger_id"
                                            :multiple="false" 
                                            :always-open="false"
                                            :options="ledgers"
                                            :normalizer="normalizer"
                                            :value-consists-of="valueConsistsOf"
                                            :default-expand-level="Infinity"
                                            :search-nested="true"                                                
                                            placeholder='Select Ledger account'
                                            v-if="renderOptionComponent"
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.supplier_id">
                                            {{errors.supplier_id[0]}}
                                        </div>
                                    </div>
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
                                        <button type="submit" class="btn btn-primary " :disabled="disabled" @click="filterBankBookReport()">
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
                            <h4 v-if="from_date != '' && to_date != ''">Cash Book From {{ from_date }} to {{ to_date }}</h4> 
                            <div class="table-responsive">
                                <table class="table table-bordered table-centered w-100" v-if="!loading">
                                    <thead class="table-light">
                                        <tr class="success item-head">
                                            <th width="5%" style="text-align: center">SL</th>
                                            <th width="10%" style="text-align: center">Date</th>
                                            <th width="15%" style="text-align: center">Voucher Code</th>
                                            <th width="30%" style="text-align: center">Account Head</th>
                                            <!-- <th width="15%" style="text-align: center">Narration/Cheque Details </th> -->
                                            <th width="10%" style="text-align: center">Debit Amount</th>
                                            <th width="10%" style="text-align: center">Credit Amount</th>
                                            <th width="10%" style="text-align: center">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        <tr v-if="search_terms.ledger_id != null">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>Opening Balance</td>
                                            <!-- <td></td> -->
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">{{ opening_balance.toFixed(4) }}</td>
                                        </tr> 
                                        <tr v-for="(ledger, index) in newItemAry" :key="index">
                                            <td class="text-center">{{ index + 1 }}</td>
                                            <td class="text-center">{{ ledger.vdate }}</td>
                                            <td class="text-center">{{ ledger.voucher_code }}</td>
                                            <td class="text-left" style="word-wrap: break-word; max-width: 400px;">{{ ledger.ledger_details }}</td>
                                            <td class="text-right" v-if="ledger.pvaccount_type == 'dr'">{{ ledger.credit }}</td>
                                            <td class="text-right" v-else> --- </td>
                                            <td class="text-right" v-if="ledger.pvaccount_type == 'cr'">{{ ledger.debit }}</td>
                                            <td class="text-right" v-else>----</td>
                                            <td class="text-right"> {{ ledger.balance_amount }}</td>
                                        </tr>                                   
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-center">Total </th>
                                            <th class="text-right">{{ balance('dr') }}</th>
                                            <th class="text-right">{{ balance('cr')  }}</th>
                                            <th class="text-right">{{ row_available_balance.toFixed(2) }}</th>
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
                    </div>

                    <Modal @close="toggleModal()"  >
                        <div class="modal-content scrollbar-width-thin orderPreview" >
                            <div class="modal-header"> 
                                <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                <h3 style="width: 100%">Bank Book</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Bank Book</h4> 
                                                <h4 style="text-align: center;">From {{ search_terms.from_date }} TO {{ search_terms.to_date }}</h4>  
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered table-centered w-100" v-if="!loading">
                                        <thead class="table-light">
                                            <tr class="success item-head">
                                                <th width="5%" style="text-align: center">SL</th>
                                                <th width="10%" style="text-align: center">Date</th>
                                                <th width="15%" style="text-align: center">Voucher Code</th>
                                                <th width="30%" style="text-align: center">Account Head</th>
                                                <!-- <th width="15%" style="text-align: center">Narration/Cheque Details </th> -->
                                                <th width="15%" style="text-align: center">Debit Amount</th>
                                                <th width="15%" style="text-align: center">Credit Amount</th>
                                                <th width="10%" style="text-align: center">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <tr v-if="search_terms.ledger_id != null">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Opening Balance</td>
                                                <!-- <td></td> -->
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">{{ opening_balance }}</td>
                                            </tr> 
                                            <tr v-for="(ledger, index) in newItemAry" :key="index">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td class="text-center">{{ ledger.vdate }}</td>
                                                <td class="text-center">{{ ledger.voucher_code }}</td>
                                                <td class="text-left" style="word-wrap: break-word; max-width: 400px;">{{ ledger.ledger_details }}</td>
                                                <!-- <td>{{ ledger.global_note }}</td>  -->
                                                <td class="text-right" v-if="ledger.pvaccount_type == 'dr'">{{ ledger.credit }}</td>
                                                <td class="text-right" v-else> --- </td>
                                                <td class="text-right" v-if="ledger.pvaccount_type == 'cr'">{{ ledger.debit }}</td>
                                                <td class="text-right" v-else>----</td>
                                                <td class="text-right"> {{ ledger.balance_amount }}</td>
                                            </tr>                                   
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-center">Total </th>
                                                <th class="text-right">{{ balance('dr') }}</th>
                                                <th class="text-right">{{ balance('cr')  }}</th>
                                                <th class="text-right">{{ row_available_balance }}</th>
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
            newItemAry: [],
            sl: 1,
            loading: true,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            companies: [],
            items: [],
            ledgers: [],
            ledger_options: [],
            ledger_id: null,
            renderOptionComponent: true,
            valueConsistsOf: 'BRANCH_PRIORITY',
            normalizer(node) {
                return {
                    id: node.id,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
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
            debit_amount_action: 1,
            credit_amount_action: -1,
            row_balance: 0,
            row_available_balance: 0,
            from_date: '',
            to_date: '',
            show_with_code: false,
            codeActive: false,
        };
    },
    created() {
        this.fetchCompanies();
    },
    methods: { 
        balance: function(type){
            let sum = 0;
            this.items.filter((item) => {
                item.recursion.filter((ledger) => {
                    if(type=='dr'){
                        sum += Number(ledger.credit);
                    }else{
                        sum += Number(ledger.debit);
                    }
                    
                });
            })
            return Number(sum).toFixed(4);
        },

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },
        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;

                if(this.companies.length == 1){
                    const companyId = this.companies[0].id;  
                    this.fetchAccountLedgers(companyId);
                }
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        }, 

        fetchAccountLedgers(companyId) {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOnlyLedgerOption?company_id='+companyId, this.headerjson)
            .then((resp) => {
                this.ledgers = resp.data.data.accounts;  
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            })
            .finally(() => {
                this.loading = false;
            });
        },

        filterBankBookReport() {
            this.isSubmit = true;
            this.disabled = true;

            this.fetchBankBookReport();
        },

        fetchBankBookReport() { 
            this.newItemAry = [];
            var data = {
                ledger_id: this.search_terms.ledger_id, 
                from_date: this.search_terms.from_date, 
                to_date: this.search_terms.to_date
            };
            axios.post(this.apiUrl+'/reports/bank-book-report', data, this.headerjson)
            .then((res) => {
                this.items = res.data.data.report_data;
                this.opening_balance = res.data.data.opening_balance;
                this.row_available_balance = res.data.data.opening_balance;
                this.debit_amount_action = res.data.data.debit_amount_action;
                this.credit_amount_action = res.data.data.credit_amount_action;

                
                var initial_balance = res.data.data.opening_balance;
                var balance_amount = 0; 

                // newItemAry
                this.items.filter((row, index) => { 
                    if(row.recursion.length> 0){
                        this.row_available_balance = res.data.data.opening_balance;
                        row.recursion.filter((child, cindex) => {
                            child.vdate = row.vdate;
                            child.voucher_code = row.voucher_code;
                            child.global_note = row.global_note;
                            child.pvaccount_type = row.vaccount_type;
                            if(row.vaccount_type == "dr") {
                                let damount = (parseFloat(child.credit) * (this.debit_amount_action));
                                // balance_amount = (this.row_available_balance + (damount));
                                balance_amount = (initial_balance + (damount));
                            }else{
                                let camount = (parseFloat(child.debit) * (this.credit_amount_action));
                                // balance_amount = (this.row_available_balance + (camount));
                                balance_amount = (initial_balance + (camount));
                            }

                            child.balance_amount = parseFloat(balance_amount).toFixed(4); 
                            initial_balance = balance_amount;
                            this.row_available_balance = balance_amount;
                            // Push new Item 
                            this.newItemAry.push(child);
                        });
                    }
                });
                this.isSubmit = false;
                // this.disabled = true;
            })
            .catch((err) => { 
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
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