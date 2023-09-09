<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Cash and Bank Book</a></li>
                                
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
                            <h3 style="text-align: center;">Cash and Bank Book</h3>
                        </div>

                        <div class="card-body">
                            <div class="row"> 
                                <div class="col-md-3"> 
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
                                        <button type="submit" class="btn btn-primary " :disabled="disabled" @click="filterCashAndBankBookReport()">
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

                            <table class="table table-bordered table-nowrap w-100" v-if="!loading">
                                <thead class="table-light">
                                    <tr class="success item-head">
                                        <th width="5%" style="text-align: center" rowspan="2">SL</th>
                                        <th width="10%" style="text-align: center" rowspan="2">Date</th>
                                        <th width="15%" style="text-align: center" rowspan="2">Voucher Code</th>
                                        <th width="15%" style="text-align: center" rowspan="2">Account Head</th>
                                        <th width="15%" style="text-align: center" colspan="2">Receipt (Debit)</th>
                                        <th width="15%" style="text-align: center" colspan="2">Payment (Credit)</th>
                                        <th width="15%" style="text-align: center" colspan="2">Balance</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center">Cash</th>
                                        <th style="text-align: center">Bank</th>
                                        <th style="text-align: center">Cash</th>
                                        <th style="text-align: center">Bank</th>
                                        <th style="text-align: center">Cash</th>
                                        <th style="text-align: center">Bank</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    <tr v-if="search_terms.ledger_id != null">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Opening Balance</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">{{ opening_balance }}</td>
                                    </tr> 
                                    <tr v-for="(ledger, index) in newItemAry" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ledger.vdate }}</td>
                                        <td>{{ ledger.voucher_code }}</td>
                                        <td>{{ ledger.ledger_code }}</td>
                                        <td>{{ ledger.global_note }}</td> 
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
                                        <th class="text-right">{{ balance('dr') }}</th>
                                        <th class="text-right">{{ balance('cr')  }}</th>
                                        <th class="text-right">{{ balance('dr') }}</th>
                                        <th class="text-right">{{ balance('cr')  }}</th>
                                        <!-- <th class="text-right">{{ row_available_balance }}</th>
                                        <th class="text-right">{{ row_available_balance }}</th> -->
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
                                <h3 style="width: 100%">Cash and Bank Book</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Cash and Bank Book</h4> 
                                                <h4 style="text-align: center;">From {{ search_terms.from_date }} TO {{ search_terms.to_date }}</h4>  
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered table-nowrap w-100" v-if="!loading">
                                        <thead class="table-light">
                                            <tr class="success item-head">
                                                <th width="5%" style="text-align: center" rowspan="2">SL</th>
                                                <th width="10%" style="text-align: center" rowspan="2">Date</th>
                                                <th width="15%" style="text-align: center" rowspan="2">Voucher Code</th>
                                                <th width="15%" style="text-align: center" rowspan="2">Account Head</th>
                                                <th width="15%" style="text-align: center" colspan="2">Receipt (Debit)</th>
                                                <th width="15%" style="text-align: center" colspan="2">Payment (Credit)</th>
                                                <th width="10%" style="text-align: center" colspan="2">Balance</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align: center">Cash</th>
                                                <th style="text-align: center">Bank</th>
                                                <th style="text-align: center">Cash</th>
                                                <th style="text-align: center">Bank</th>
                                                <th style="text-align: center">Cash</th>
                                                <th style="text-align: center">Bank</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            <tr v-if="search_terms.ledger_id != null">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Opening Balance</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">{{ opening_balance }}</td>
                                            </tr> 
                                            <tr v-for="(ledger, index) in newItemAry" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ ledger.vdate }}</td>
                                                <td>{{ ledger.voucher_code }}</td>
                                                <td>{{ ledger.ledger_code }}</td>
                                                <td>{{ ledger.global_note }}</td> 
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
                                                <th class="text-right">{{ balance('dr') }}</th>
                                                <th class="text-right">{{ balance('cr')  }}</th>
                                                <th class="text-right">{{ balance('dr') }}</th>
                                                <th class="text-right">{{ balance('cr')  }}</th>
                                                <!-- <th class="text-right">{{ row_available_balance }}</th>
                                                <th class="text-right">{{ row_available_balance }}</th> -->
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

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
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
            from_date: '',
            to_date: '',
            show_with_code: false,
            codeActive: false,
        };
    },
    created() {
        this.fetchAccountLedgers();
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

        fetchAccountLedgers() {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOnlyLedgerOption', this.headerjson)
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

        filterCashAndBankBookReport() {
            this.isSubmit = true;
            this.disabled = true;

            this.fetchCashAndBankBookReport();
        },

        fetchCashAndBankBookReport() {  
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