<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Accounts </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Voucher Entry</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-dark float-right" @click="redirectRoute('/accounting/voucher-list')">
                              <i class="mdi mdi-arrow-left-thin"></i> Back
                            </button> 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <form @submit.prevent="submitVoucherForm()" enctype="multipart/form-data" id="ventry_form" class="ventry_form" v-if="!loading">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vtype_value">Voucher Type *</label>
                                                    <select class="form-control border" v-model="vtype_value" @change="onkeyPress('vtype_id'), onChangeVtype($event)">
                                                        <option value="">--- Select Voucher Type ---</option>
                                                        <option v-for="(entry_type, et) in entry_types" :key="et" :value="entry_type.label" :data-id="entry_type.id">{{ entry_type.name }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.vtype_id">
                                                        {{ errors.vtype_id }}
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vcode">Voucher Code *</label>
                                                    <input type="text" class="form-control border" v-model="vform.vcode" @input="onkeyPress('vcode')" placeholder="Enter Code" readonly />
                                                    <div class="invalid-feedback" v-if="errors.vcode">  
                                                        {{ errors.vcode }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="cost_center_id">Cost Center *</label>
                                                    <select class="form-control border" id="cost_center_id" v-model="vform.cost_center_id" @change="onkeyPress('cost_center_id')">
                                                            <option value="">--- Select Cost Center ---</option>
                                                            <option v-for="(cost_center, cc) in cost_centers" :key="cc" :value="cost_center.id">{{ cost_center.center_name }}</option>
                                                        </select>
                                                    <div class="invalid-feedback" v-if="errors.cost_center_id">
                                                        {{ errors.cost_center_id }}
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vnumber">Number </label>
                                                    <input type="text" class="form-control border" v-model="vform.vnumber" @keypress="onkeyPress('vnumber')" placeholder="Enter Number" />
                                                    <div class="invalid-feedback" v-if="errors.vnumber">
                                                        {{ errors.vnumber }}
                                                    </div>
                                                </div>
                                            </div> -->

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="vnumber">Date *</label>
                                                    <input type="date" :min="active_min_date" :max="active_max_date" class="form-control border" v-model="vform.vdate" @keypress="onkeyPress('vdate')" />
                                                    <div class="invalid-feedback" v-if="errors.vdate">
                                                        {{ errors.vdate }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="fiscalyear_id">Fiscal Year *</label>
                                                    <select class="form-control border" v-model="vform.fiscalyear_id" @change="onkeyPress('fiscalyear_id')">
                                                        <option v-for="(fiscal_year, f) in fiscal_years" :key="f" :value="fiscal_year.id">{{ fiscal_year.start_date+' to '+fiscal_year.end_date }}</option>
                                                    </select>
                                                    <!-- <treeselect 
                                                        v-model="ledger_id"
                                                        :multiple="false" 
                                                        :options="ledgers"
                                                        :normalizer="normalizer"
                                                        :value-consists-of="valueConsistsOf"
                                                        :default-expand-level="Infinity"
                                                        :search-nested="true"                                                
                                                        placeholder='Select Ledger account'
                                                        v-if="renderOptionComponent"
                                                    /> -->
                                                    <div class="invalid-feedback" v-if="errors.fiscal_year_id">
                                                        {{ errors.fiscal_year_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3" v-if="vtype_value == 'payment'">
                                                    <label for="vtype_value">Payment Type *</label>
                                                    <select class="form-control border" v-model="vform.vpayment_type" @change="onkeyPress('vpayment_type'), onChangeType($event)">
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.vpayment_type">
                                                        {{ errors.vpayment_type }}
                                                    </div>
                                                </div>

                                                <div class="mb-3" v-else-if="vtype_value == 'receipt'">
                                                    <label for="vtype_value">Receipt Type *</label>
                                                    <select class="form-control border" v-model="vform.vreceipt_type" @change="onkeyPress('vreceipt_type'), onChangeType($event)">
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.vreceipt_type">
                                                        {{ errors.vreceipt_type }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-9">
                                                <div class="row" v-if="vform.vpayment_type == 'bank' || vform.vreceipt_type == 'bank' || vtype_value == 'contra'">
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label for="cheque_no">Cheque Number *</label>
                                                            <input type="text" class="form-control border" v-model="vform.cheque_no" @input="onkeyPress('cheque_no')" placeholder="Enter Cheque Number" />
                                                            <div class="invalid-feedback" v-if="errors.cheque_no">  
                                                                {{ errors.cheque_no }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label for="cheque_date">Cheque Date *</label>
                                                            <input type="date" class="form-control border" v-model="vform.cheque_date" @keypress="onkeyPress('cheque_date')" />
                                                            <div class="invalid-feedback" v-if="errors.cheque_date">
                                                                {{ errors.cheque_date }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                        
                                    <hr>

                                    <div class="entry_table" style="position: relative;">
                                        <button type="button" class="btn btn-info float-right" style="margin-bottom: 10px;" @click="addNewTransactionBlock()"><i class="mdi mdi-plus"></i> Add New Transaction</button>
                                        <table class="table table-bordered table-centered table-nowrap w-100" v-for="(transaction_block, t) in vform.transaction_blocks" :key="t">
                                            <thead class="table-light">
                                                <tr v-if="t != 0" style="background: #fff !important">
                                                    <th colspan="7" class="text-right" style="background: #fff !important"><button type="button" class="btn btn-light" @click="removeTransactionBlock(t)"><i class="mdi mdi-close"></i></button> </th> 
                                                </tr>
                                                <tr class="border success item-head">
                                                    <!-- <th class="text-center" style="width: 10%">Entry Type </th>  -->
                                                    <!-- <th class="text-center" style="width: 20%">Cost Center </th>  -->
                                                    <!-- <th class="text-left" style="width: 10%">Dr/Cr </th>  -->
                                                    <th class="text-center" style="width: 25%">Ledger</th>
                                                    <th class="text-center" style="width: 10%">Debit</th>
                                                    <th class="text-center" style="width: 10%">Credit</th>
                                                    <th class="text-center" style="width: 15%">Naration</th>
                                                    <!-- <th class="text-center" style="width: 15%">Balance</th> -->
                                                    <th class="text-center" style="width: 5%">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(account_item, i) in transaction_block.account_items" :key="i" >
                                                    <!-- <td>
                                                        <select class="form-control border" v-model="account_item.ventry_type" @change="onkeyPress('ventry_type')">
                                                            <option value="">Select Type</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="bank">Bank</option>
                                                        </select>
                                                    </td> -->
                                                    <!-- <td>
                                                        <select class="form-control border" v-model="account_item.cost_center_id" @change="onkeyPress('cost_center_id')">
                                                            <option value="">--- Select Cost Center ---</option>
                                                            <option v-for="(cost_center, cc) in cost_centers" :key="cc" :value="cost_center.id">{{ cost_center.center_name }}</option>
                                                        </select>
                                                    </td> -->
                                                    <!-- <td>
                                                        <select class="form-control border" v-model="account_item.ledger_type" @change="onkeyPress('ledger_type')">
                                                            <option value="">--- Select Type ---</option>
                                                            <option value="dr">Dr</option>
                                                            <option value="cr">Cr</option>
                                                        </select>
                                                    </td> -->
                                                    <td>                                                        
                                                        <treeselect 
                                                            v-model="account_item.ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :disable-branch-nodes="true"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select Ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <!-- <select class="form-control border" v-model="account_item.ledger_id" @change="onkeyPress('ledger_id')">
                                                            <option value="">--- Select Ledger ---</option>
                                                        </select> --> 
                                                    </td>
                                                    <td> 
                                                        <!-- <input type="number" step="any" class="form-control border" v-model="account_item.debit" @keyup="inputChange()" @keypress="onkeyPress('debit')" :readonly="(account_item.ledger_type == 'cr') ? true : false"> -->
                                                        <input v-if="i==0" type="number" step="any" class="form-control border" v-model="account_item.debit" @keyup="inputChange(t, i)" @keypress="onkeyPress('debit')" :readonly="account_item.credit_active" @click="debitActive(t, i)">
                                                        <input v-else type="number" step="any" class="form-control border" v-model="account_item.debit" @keyup="inputChange(t, i)" @keypress="onkeyPress('debit')" :readonly="account_item.credit_active">
                                                    </td>
                                                    <td>
                                                        <input v-if="i==0" type="number" step="any" class="form-control border" v-model="account_item.credit" @keyup="inputChange(t, i)" @keypress="onkeyPress('credit')" :readonly="account_item.debit_active" @click="creditActive(t, i)">
                                                        <input v-else type="number" step="any" class="form-control border" v-model="account_item.credit" @keyup="inputChange(t, i)" @keypress="onkeyPress('credit')" :readonly="account_item.debit_active">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control border" v-model="account_item.note" @keypress="onkeyPress('note')">
                                                    </td>
                                                    <!-- <td class="text-right">
                                                        {{ account_item.balance }}
                                                    </td> -->
                                                    <td class="text-center">
                                                        <a v-if="i != 0 && i != 1" href="javascript:void(0)" class="text-danger" style="font-size: 18px;" @click="removeAccountItem(t, i)"> <i class="mdi mdi-delete-outline"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td class="bg-success text-right">{{ Number(transaction_block.total_debit_amount).toFixed(2) }}</td>
                                                    <td class="bg-success text-right"> {{ Number(transaction_block.total_credit_amount).toFixed(2) }}</td>
                                                    <td colspan="2"> 
                                                        <a href="javascript:void(0)" class="text-primary float-right" style="font-size: 20px;" @click="addAccountItem(t)"> <i class="mdi mdi-plus-circle-outline"></i></a>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <button v-if="vform.transaction_blocks.length > 1" type="button" class="btn btn-info float-right" style="overflow:hidden; clear: both;" @click="addNewTransactionBlock()"><i class="mdi mdi-plus"></i> Add New Transaction</button>
                                    </div>
                                    

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="global_note"> Note *</label>
                                            <textarea class="form-control border" rows="3" v-model="vform.global_note" @keypress="onkeyPress('global_note')"></textarea>
                                            <div class="invalid-feedback" v-if="errors.global_note">
                                                {{errors.global_note[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary float-right" :disabled="disabled"> 
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>
                                            <i class="mdi mdi-content-save-all"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-danger float-right" style="margin-right: 5px;"> <i class="mdi mdi-close"></i> Cancel</button>
                                    </div>
                                </form>                        
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
            
            
            <!-- Modal For Print-->
            <Modal @close="toggleVoucherPrintModal()" :modalActive="voucherModalPrintActive">
                <div class="modal-content scrollbar-width-thin orderPreview">
                    <div class="modal-header" style="text-align:right; display: block;"> 
                        <button @click="toggleVoucherPrintModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="container" id="printArea">
                                            <p class="text-uppercase text-center mt-2 fw-500">Twenty Four 7</p>
                                            <p class="text-uppercase text-center mt-2 fw-500">3rd floor, UCEP Cheyne Tower, 25 Segun Bagicha Rd, Dhaka 1000</p><br>
                                            <hr class="m-zero">
                                            <!-- <p class="text-center m-0 font-italic"><strong>Cash Received Voucher (Approval)</strong></p> -->
                                            <p class="text-center m-0 font-italic fw-500">{{ vitem.vtype_name }} Voucher (Approval)</p>
                                            <hr class="m-zero">
                                            <div class="d-flex justify-content-between mt-fourty">
                                                <div>
                                                    <div class="d-flex">
                                                        <p class="mr-fourty fw-500">Voucher#:</p>
                                                    <p>{{ vitem.vcode }}</p>
                                                    </div>
                                                    <div class="d-flex">
                                                        <p class="mr-fourty fw-500">Description:</p>
                                                        <p>{{ vitem.global_note }}</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="mr-fourty fw-500">Date:</p>
                                                    <p>{{ vitem.vdate }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <table class="table table-hover mt-3 fw-500">
                                                    <thead>
                                                        <td style="width: 15%">Amount Code</td>
                                                        <td style="width: 50%">Amount Hand</td>
                                                        <td style="width: 15%">Cost Center</td>
                                                        <td style="width: 10%" class="text-center">Debit</td>
                                                        <td style="width: 10%" class="text-center">Credit</td>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(ledger_item, i) in vitem.ledger_items" :key="i">
                                                            <td>{{ ledger_item.ledger_code }}</td>
                                                            <td>{{ ledger_item.ledger_head }}</td>
                                                            <td>{{ ledger_item.cost_center_name }}</td>
                                                            <td style="text-align: right">{{ Number(ledger_item.debit_amount).toFixed(2) }}</td>
                                                            <td style="text-align: right">{{ Number(ledger_item.credit_amount).toFixed(2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                            <td colspan="3" class="text-center fw-500">Total Transaction</td>
                                                            <td style="text-align: right">{{ Number(vitem.total_debit_amount).toFixed(2) }}</td>
                                                            <td style="text-align: right">{{ Number(vitem.total_credit_amount).toFixed(2) }}</td>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="my-5 d-flex">
                                                <p class="mr-fourty">Taka in word:</p>
                                                <number-to-word :number="Number(vitem.total_debit_amount).toFixed(2)" />
                                                <!-- <p><strong>Twelve Thousand One Hundred Seventy Only</strong></p> -->
                                            </div>
                                            <div class="d-flex justify-content-between align-item-center">
                                                <div class="text-center col-xs-4">
                                                    <p class="m-zero">Khandakar Kudrat E Khuda</p>
                                                    <hr class="m-zero">
                                                    <p class="fw-500">Prepare By</p>
                                                </div>
                                                <div class="text-center col-xs-4">
                                                    <p class="m-zero">&nbsp;</p>
                                                    <hr class="m-zero">
                                                    <p class="fw-500">Checked By</p>
                                                </div>
                                                <div class="text-center col-xs-4">
                                                    <p class="m-zero">Khandakar Kudrat E Khuda</p>
                                                    <hr class="m-zero">
                                                    <p class="fw-500">Approved By</p>
                                                </div>
                                            </div>
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
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';
import NumberToWord from './../page/NumberToWord.vue';   

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
            disabled: false,
            showModal: false,
            modalActive:false,
            voucherModalPrintActive:false,
            errors: {},
            vitem: '',
            entry_types: [],
            vtype_value: this.$route.query.type ?? '',
            vtype_id: '',
            fiscal_years: [],
            active_min_date: '',
            active_max_date: '',
            cost_centers: [],
            ledgers: [],
            ledger_options: [],
            ledger_id: null,
            renderOptionComponent: true,
            valueConsistsOf: 'BRANCH_PRIORITY',
            normalizer(node) {
                // if(node.account_type == "ledger") {
                //     var disable_item = true;
                // } else{
                //     disable_item = false;
                // }

                // if(node.children.length > 0) {
                //     var children_data = {children: node.children};
                // } else{
                //     children_data = '';
                // }
                return {
                    id: node.id +'___'+node.code,
                    label: '['+ node.code +'] '+ node.name,
                    // isDisabled: disable_item,
                    children: node.children,
                    // children_data
                }
            },
            vform: new Form({
                vcode: '',
                vnumber: '',
                cost_center_id: '',
                vdate: new Date().toISOString().slice(0,10),
                fiscalyear_id: '',
                vpayment_type: 'cash',
                vreceipt_type: 'cash',
                cheque_no: '',
                cheque_date: '',
                global_note: '',
                transaction_blocks: [{
                    account_items: [
                        {
                            ventry_type: '',
                            cost_center_id: '',
                            ledger_type: 'dr',
                            ledger_id: null,
                            debit: '',
                            credit: '',
                            debit_active: true,
                            credit_active: true,
                            note: '',
                        },
                        {
                            ventry_type: '',
                            cost_center_id: '',
                            ledger_type: 'cr',
                            ledger_id: null,
                            debit: '',
                            credit: '',
                            debit_active: true,
                            credit_active: true,
                            note: '',

                        }
                    ],
                    total_debit_amount: '0.00',
                    total_credit_amount: '0.00',

                }],
                
            }),
        };
    },
    created() {
        this.fetchEntryTypes();
        this.fetchVoucherCode();
        this.fetchFiscalYear();
        this.fetchCostCenter();
        this.fetchAccountLedgers();
    },
    methods: {
        redirectRoute: function(route_link) {
            this.$router.push(route_link);
        },

        toggleVoucherPrintModal() {

            this.voucherModalPrintActive = !this.voucherModalPrintActive;

        },

        forceRerender() {
            // Remove my-component from the DOM
            this.renderOptionComponent = false;

            this.$nextTick(() => {
                // Add the component back in
                this.renderOptionComponent = true;
            });
        },

        onChangeType(event) {
            this.vform.cheque_no = '';
            this.vform.cheque_date = '';
        },

        fetchEntryTypes() {
            axios.get(this.apiUrl+'/entry_types', this.headerjson)
            .then((resp) => {
                this.entry_types = resp.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchVoucherCode() {
            var data = {vtype: this.vtype_value}
            axios.post(this.apiUrl+'/accounts/getVoucherCode', data, this.headerjson) 
            .then((resp) => {
                this.vform.vcode = resp.data.data.voucher_code;
                this.vtype_id = resp.data.data.voucher_type_id;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchFiscalYear() {
            axios.get(this.apiUrl+'/fiscal_years/getActiveFiscalYear', this.headerjson) 
            .then((resp) => {
                this.fiscal_years = resp.data.data;
                this.vform.fiscalyear_id = resp.data.data[0].id;
                this.active_min_date = resp.data.data[0].start_date;
                this.active_max_date = resp.data.data[0].end_date;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchCostCenter() {
            axios.get(this.apiUrl+'/cost_centers', this.headerjson)
            .then((resp) => {
                this.cost_centers = resp.data.data;
            })
            .catch((err) => {
                console.log(err);
            })
        },

        fetchAccountLedgers() {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOption', this.headerjson)
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

        //
        onChangeVtype: function(event) {

            this.vform.vpayment_type = 'cash';
            this.vform.vreceipt_type = 'cash';
            this.vform.cheque_no = '';
            this.vform.cheque_date = '';

            if(this.vtype_value != "") {

                if(this.$route.query.type) {
                    var current_location = window.location.href;
                    var divide_url = current_location.split("?");
                    var base_url    = divide_url[0];
                    var replace_url = base_url+'?type='+event.target.value;
                    window.history.replaceState({}, 'type='+event.target.value, replace_url);

                    this.$route.query.type = event.target.value;

                }
                var type_id = event.target.options[event.target.options.selectedIndex].dataset.id;
                this.vtype_id = type_id;
                this.fetchVoucherCode();
            }else{
                this.vtype_id = '';
                this.vform.vcode = '';
            }
        },

        // Add New Block Item 
        addNewTransactionBlock: function() {
            const newBlock = {
                account_items: [
                    {
                        ventry_type: '',
                        cost_center_id: '',
                        ledger_type: 'dr',
                        ledger_id: null,
                        debit: '',
                        credit: '',
                        debit_active: true,
                        credit_active: true,
                        note: '',
                        balance: '0.00'
                    },
                    {
                        ventry_type: '',
                        cost_center_id: '',
                        ledger_type: 'cr',
                        ledger_id: null,
                        debit: '',
                        credit: '',
                        debit_active: true,
                        credit_active: true,
                        note: '',
                        balance: '0.00'
                    }
                ],
                total_debit_amount: '0.00',
                total_credit_amount: '0.00',
            };


            this.vform.transaction_blocks.push(newBlock);
        },

        // Remove Transaction Block
        removeTransactionBlock: function(block_index) {
            this.vform.transaction_blocks.splice(block_index, 1);
        },

        // Add Account Item
        addAccountItem: function(block_index) {
            if(this.vform.transaction_blocks[block_index].account_items[0].debit_active && this.vform.transaction_blocks[block_index].account_items[0].credit_active == false) {
                var newDebitActive = false;
                var newCreditActive = true;
            }else if(this.vform.transaction_blocks[block_index].account_items[0].credit_active && this.vform.transaction_blocks[block_index].account_items[0].debit_active == false) {
                newDebitActive = true;
                newCreditActive = false;
            }else{
                newDebitActive = true;
                newCreditActive = true;
            }
            const  newItem = {
                ventry_type: '',
                cost_center_id: '',
                ledger_type: '',
                ledger_id: null,
                debit: '',
                credit: '',
                debit_active: newDebitActive,
                credit_active: newCreditActive,
                note: '',
                balance: '0.00'
            };

            this.vform.transaction_blocks[block_index].account_items.push(newItem);
        },

        // Remove Account Item
        removeAccountItem: function(block_index, index) {
            this.forceRerender();
            this.vform.transaction_blocks[block_index].account_items.splice(index, 1);
        },   

        //debitActive
        debitActive: function(block_index, index) {
            var credit_active = this.vform.transaction_blocks[block_index].account_items[index].credit_active;
            
            this.vform.transaction_blocks[block_index].account_items[index].debit = '';
            this.vform.transaction_blocks[block_index].account_items[index].credit = '';

            if(credit_active) {
                this.vform.transaction_blocks[block_index].account_items[index].credit_active = false;
                this.vform.transaction_blocks[block_index].account_items[index].debit_active = true;


                for(var i = 0; i<this.vform.transaction_blocks[block_index].account_items.length; i++) {
                    // this.vform.transaction_blocks[block_index].account_items[i].ledger_id = null;
                    // this.forceRerender();
                    if (i === 0) { continue; }
                    this.vform.transaction_blocks[block_index].account_items[i].credit_active = true;
                    this.vform.transaction_blocks[block_index].account_items[i].debit_active = false;
                    this.vform.transaction_blocks[block_index].account_items[i].debit = '';
                    this.vform.transaction_blocks[block_index].account_items[i].credit = '';
                }


            }else{
                this.vform.transaction_blocks[block_index].account_items[index].debit_active = true;
            }
        },

        //creditActive
        creditActive: function(block_index, index) {
            var debit_active = this.vform.transaction_blocks[block_index].account_items[index].debit_active;

            this.vform.transaction_blocks[block_index].account_items[index].debit = '';
            this.vform.transaction_blocks[block_index].account_items[index].credit = '';

            if(debit_active) {
                this.vform.transaction_blocks[block_index].account_items[index].debit_active = false;
                this.vform.transaction_blocks[block_index].account_items[index].credit_active = true;

                for(var i = 0; i<this.vform.transaction_blocks[block_index].account_items.length; i++) {
                    
                    // this.vform.transaction_blocks[block_index].account_items[i].ledger_id = null;
                    // this.forceRerender();
                    if (i === 0) { continue; }
                    this.vform.transaction_blocks[block_index].account_items[i].debit_active = true;
                    this.vform.transaction_blocks[block_index].account_items[i].credit_active = false;
                    this.vform.transaction_blocks[block_index].account_items[i].debit= '';
                    this.vform.transaction_blocks[block_index].account_items[i].credit = '';
                }
            }else{
                this.vform.transaction_blocks[block_index].account_items[index].credit_active = true;
            }
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        }, 

        inputChange: function(block_index, index) {
            var debit_amount = this.vform.transaction_blocks[block_index].account_items.reduce(function(total, item){
                var debit_amount = (item.debit != '') ? item.debit : 0;
                return parseFloat(total + parseFloat(debit_amount)); 
            },0);

            var credit_amount = this.vform.transaction_blocks[block_index].account_items.reduce(function(total, item){
                var credit_amount = (item.credit != '') ? item.credit : 0;
                return parseFloat(total + parseFloat(credit_amount)); 
            },0);

            this.vform.transaction_blocks[block_index].total_debit_amount = debit_amount;
            this.vform.transaction_blocks[block_index].total_credit_amount = credit_amount;
        },

        submitVoucherForm: function(event) {
            this.isSubmit = true; 
            this.disabled = true;
            var payment_type = "";
            if(this.vtype_value == "payment") {
                payment_type = this.vform.vpayment_type;
            }
            else if(this.vtype_value == "receipt") {
                payment_type = this.vform.vreceipt_type;
            }

            if((this.total_debit_amount == this.total_credit_amount) && (this.total_debit_amount != '0.00' && this.total_credit_amount != '0.00')) {
                var formData = new FormData();
                formData.append("vcode", this.vform.vcode);
                formData.append("vnumber", this.vform.vnumber);
                formData.append("cost_center_id", this.vform.cost_center_id);
                formData.append("vtype_id", this.vtype_id);
                formData.append("vtype_value", this.vtype_value);
                formData.append("payment_type", payment_type);
                formData.append("cheque_no", this.vform.cheque_no);
                formData.append("cheque_date", this.vform.cheque_date);
                formData.append("fiscal_year_id", this.vform.fiscalyear_id);
                formData.append("vdate", this.vform.vdate);
                formData.append("global_note", this.vform.global_note);
                formData.append("transaction_items", JSON.stringify(this.vform.transaction_blocks));
                axios.post(this.apiUrl+'/account_vouchers', formData, this.headers)
                .then((resp) => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(resp.status == 200){

                        console.log("datatdatata==", resp.data.data );
                        this.vform.reset();
                        this.vtype_id = '';
                        this.vtype_value = this.$route.query.type ?? '';
                        this.vitem  = resp.data.data;
                        this.forceRerender();
                        this.fetchVoucherCode();
                        this.vform.fiscalyear_id = this.fiscal_years[0].id;
                        this.$toast.success(resp.data.message); 

                        setTimeout(() => {
                            this.printContent("printArea");
                        }, 3000);

                    }else{
                        this.$toast.error(resp.data.message);
                    }
                })
                .catch((err) => {
                    this.isSubmit = false; 
                    this.disabled = false;
                    this.$toast.error(err.response.data.message);
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors 
                    }
                })
            }else{
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error("Debit and Credit account must be equal");
            }
        },

        // For Print
        printContent(document_id) {
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [
                    // 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                    '/assets/css/app.min.css',
                    this.baseUrlPrintCSS+'/assets/css/bootstrap.min.css',
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

        totalDebitAmount: function(){ 
            return this.vform.transaction_blocks.account_items.reduce(function(total, item){
                var debit_amount = (item.debit != '') ? item.debit : 0;
                return parseFloat(total + parseFloat(debit_amount)); 
            },0);
        }, 
        totalCreditAmount: function() {
            return this.vform.transaction_blocks.account_items.reduce(function(total, item) {
                var credit_amount = (item.credit != '') ? item.credit : 0;
                return parseFloat(total + parseFloat(credit_amount)); 
            },0);
        },
    }
}
</script>
<style scoped>

.vue-treeselect__menu {
    z-index: 9999;
    display: none;
}
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