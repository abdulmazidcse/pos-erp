<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Accounts </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Voucher Entry Edit</a></li>
                                
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
                        <div class="card-body">
                            <div class="row">
                                <form @submit.prevent="submitVoucherForm()" enctype="multipart/form-data" id="ventry_form" class="ventry_form" v-if="!loading">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vtype_value">Voucher Type *</label>
                                                    <select class="form-control border" v-model="vtype_value" @change="onkeyPress('vtype_id'), onChangeVtype($event)" disabled>
                                                        <option value="">--- Select Voucher Type ---</option>
                                                        <option v-for="(entry_type, et) in entry_types" :key="et" :value="entry_type.label" :data-id="entry_type.id">{{ entry_type.name }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.vtype_id">
                                                        {{ errors.vtype_id }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="vcode">Code *</label>
                                                    <input type="text" class="form-control border" v-model="vform.vcode" @input="onkeyPress('vcode')" placeholder="Enter Code" readonly />
                                                    <div class="invalid-feedback" v-if="errors.vcode">  
                                                        {{ errors.vcode }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vnumber">Number *</label>
                                                    <input type="text" class="form-control border" v-model="vform.vnumber" @keypress="onkeyPress('vnumber')" placeholder="Enter Number" />
                                                    <div class="invalid-feedback" v-if="errors.vnumber">
                                                        {{ errors.vnumber }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
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
                                    </div>

                                    <hr>

                                    <div class="entry_table">
                                        <button type="button" class="btn btn-info float-right" style="margin-bottom: 10px;" @click="addNewTransactionBlock()"><i class="mdi mdi-plus"></i> Add New Transaction</button>
                                        <table class="table table-bordered table-centered table-nowrap w-100" v-for="(transaction_block, t) in vform.transaction_blocks" :key="t">
                                            <thead class="table-light">
                                                <tr v-if="t != 0" style="background: #fff !important">
                                                    <th colspan="7" class="text-right" style="background: #fff !important"><button type="button" class="btn btn-light" @click="removeTransactionBlock(t)"><i class="mdi mdi-close"></i></button> </th> 
                                                </tr>
                                                <tr class="border success item-head">
                                                    <!-- <th class="text-center" style="width: 10%">Entry Type </th>  -->
                                                    <th class="text-center" style="width: 20%">Cost Center </th> 
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
                                                    <td>
                                                        <select class="form-control border" v-model="account_item.cost_center_id" @change="onkeyPress('cost_center_id')">
                                                            <option value="">--- Select Cost Center ---</option>
                                                            <option v-for="(cost_center, cc) in cost_centers" :key="cc" :value="cost_center.id">{{ cost_center.center_name }}</option>
                                                        </select>
                                                    </td>
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
                                                    <td colspan="2"></td>
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
                                            <textarea class="form-control border" rows="3" v-model="vform.global_note"></textarea>
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
                                            <i class="mdi mdi-content-save-all"></i> Update
                                        </button>
                                        <button type="button" class="btn btn-danger float-right" style="margin-right: 5px;" @click="redirectRoute('/accounting/voucher-list')"> <i class="mdi mdi-close"></i> Cancel</button>
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
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            disabled: false,
            showModal: false,
            modalActive:false,
            errors: {},
            voucher_id: this.$route.params.id,
            entry_types: [],
            vtype_value: '',
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
                return {
                    id: node.id +'___'+node.code,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
            vform: new Form({
                id: '',
                vcode: '',
                vnumber: '',
                vdate: '',
                fiscalyear_id: '',
                global_note: '',
                account_items: [],
                
            }),
            total_debit_amount: '0.00',
            total_credit_amount: '0.00',

            vform: new Form({
                vcode: '',
                vnumber: '',
                vdate: new Date().toISOString().slice(0,10),
                fiscalyear_id: '',
                global_note: '',
                transaction_blocks: [{
                    account_items: [],
                    total_debit_amount: '0.00',
                    total_credit_amount: '0.00',

                }],
                
            }),
        };
    },
    created() {        
        this.fetchEntryTypes();
        this.fetchFiscalYear();
        this.fetchCostCenter();
        this.fetchAccountLedgers();
        this.fetchVoucherData(this.voucher_id);
    },
    methods: {
        redirectRoute: function(route_link) {
            this.$router.push(route_link);
        },

        forceRerender() {
            // Remove my-component from the DOM
            this.renderOptionComponent = false;

            this.$nextTick(() => {
                // Add the component back in
                this.renderOptionComponent = true;
            });
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

        fetchVoucherData(voucher_id) {
            if(voucher_id != "") {
                axios.get(this.apiUrl+'/account_vouchers/'+voucher_id+'/edit', this.headerjson)
                .then((resp) => {
                    var voucher_data = resp.data.data;
                    this.vform.fill(voucher_data);
                    this.vtype_value = voucher_data.vtype_value;
                    this.vtype_id   = voucher_data.vtype_id;
                    this.total_debit_amount = voucher_data.total_debit_amount;
                    this.total_credit_amount = voucher_data.total_credit_amount;


                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                })
                .finally((fres) => {
                    this.loading = false;
                });
            }else{
                this.loading = true;
                this.$toast.error("Data couldn't found!");
            }
        },

        //
        onChangeVtype: function(event) {
            
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
                is_old: false,
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
        removeAccountItem: function(account_item, block_index, index='') {
            if(account_item.is_old) {
                this.$swal({
                    title: 'Are you sure?',
                    text: "You want delete this item!", 
                    showCancelButton: true,
                    confirmButtonCategory: '#3085d6',
                    cancelButtonCategory: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => { 
                    if (result.value) { 
                        axios.delete(this.apiUrl+'/account_voucher_transactions/'+account_item.id, this.headerjson)
                        .then(res => {
                            if(res.status == 200){ 
                                this.fetchVoucherData(this.voucher_id);
                                this.$toast.success(res.data.message); 
                            }else{
                                this.$toast.error(res.data.message);
                            } 
                        }).catch(err => {  
                            this.$toast.error(err.response.data.message);
                            if(err.response.status == 422){
                                this.errors = err.response.data.errors 
                            }
                        }) 
                    }
                });
            }else{
                
                this.forceRerender();
                this.vform.transaction_blocks[block_index].account_items.splice(index, 1);
            }
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
            if((this.total_debit_amount == this.total_credit_amount) && (this.total_debit_amount != '0.00' && this.total_credit_amount != '0.00')) {
                var formData = new FormData();
                formData.append("vcode", this.vform.vcode);
                formData.append("vnumber", this.vform.vnumber);
                formData.append("vtype_id", this.vtype_id);
                formData.append("vtype_value", this.vtype_value);
                formData.append("fiscal_year_id", this.vform.fiscalyear_id);
                formData.append("vdate", this.vform.vdate);
                formData.append("global_note", this.vform.global_note);
                formData.append("transaction_items", JSON.stringify(this.vform.transaction_blocks));
                formData.append("_method", "PUT");
                axios.post(this.apiUrl+'/account_vouchers/'+this.voucher_id, formData, this.headers)
                .then((resp) => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(resp.status == 200){
                        this.$toast.success(resp.data.message); 
                        this.$router.push('/accounting/voucher-list');
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

    },

    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {

        totalDebitAmount: function(){ 
            return this.vform.account_items.reduce(function(total, item){
                var debit_amount = (item.debit != '') ? item.debit : 0;
                return parseFloat(total + parseFloat(debit_amount)); 
            },0);
        }, 
        totalCreditAmount: function() {
            return this.vform.account_items.reduce(function(total, item) {
                var credit_amount = (item.credit != '') ? item.credit : 0;
                return parseFloat(total + parseFloat(credit_amount)); 
            },0);
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
</style>