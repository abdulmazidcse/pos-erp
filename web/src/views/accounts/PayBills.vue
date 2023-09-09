<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Accounts </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Bill Payments</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <!-- <button type="button" class="btn btn-dark float-right" @click="redirectRoute('/accounting/voucher-list')">
                              <i class="mdi mdi-arrow-left-thin"></i> Back
                            </button>  -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="purchase_bill" name="form_type" class="form-check-input" value="pb" checked @change="onChangePaymentForm($event)">
                                        <label class="form-check-label" for="purchase_bill" style="font-size: 18px; font-weight: bold;">Purchase</label>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" id="others_bill" name="form_type" class="form-check-input" value="ob" @change="onChangePaymentForm($event)"> 
                                        <label class="form-check-label" for="others_bill" style="font-size: 18px; font-weight: bold;">Others</label>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <hr>
                            </div>

                            <div id="purchase_bill_form" v-if="is_purchase_form">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="supplier_id">Supplier *</label><br>
                                            <Multiselect
                                                class="form-control border supplier_id"
                                                mode="single"
                                                v-model="purchase_form.supplier_id"
                                                placeholder="Select Supplier"
                                                @change="onkeyPress('supplier_id'), onChangeSupplier($event)"   
                                                :searchable="true" 
                                                :filter-results="true"
                                                :options="supplier_options"
                                                :classes="multiclasses"
                                                :close-on-select="true" 
                                                :min-chars="1"
                                                :resolve-on-load="false"
                                                :hide-selected="true" 
                                            />
                                            <div class="invalid-feedback" v-if="errors.supplier_id">
                                                {{ errors.supplier_id }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">

                                    </div>

                                </div>

                                <!-- <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input type="radio" id="purchase_bill" name="purchase_bill" class="form-check-input" value="pb">
                                            <label class="form-check-label" for="purchase_bill" style="font-size: 18px; font-weight: bold;">New Payment</label>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input type="radio" id="others_bill" name="others_bill" class="form-check-input" value="ob"> 
                                            <label class="form-check-label" for="others_bill" style="font-size: 18px; font-weight: bold;">Advance Adjustment</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div> -->

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="ledger_id">Payment Accounts *</label>
                                            <!-- <select class="form-control border" v-model="purchase_form.ledger_id" @change="onkeyPress('ledger_id')">
                                                <option value="">--- Select Accounts ---</option>
                                                <option v-for="(account, a) in accounts" :key="a" :value="account.ledger_id" :data-code="account.ledger_code">[{{ account.ledger_code }}] {{ account.ledger_name }}</option>
                                            </select> -->
                                            <treeselect 
                                                v-model="purchase_form.ledger_id"
                                                :multiple="false" 
                                                :always-open="false"
                                                :options="accounts"
                                                :normalizer="normalizer"
                                                :value-consists-of="valueConsistsOf"
                                                :default-expand-level="Infinity"
                                                :search-nested="true"
                                                @select="onkeyPress('ledger_id')"                                                
                                                placeholder='Select Ledger account'
                                                v-if="renderOptionComponent"
                                            />
                                            <div class="invalid-feedback" v-if="errors.ledger_id">
                                                {{ errors.ledger_id }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="invoice_table">
                                        <table class="table table-bordered table-centered table-nowrap w-100">
                                            <thead class="table-light">
                                                <tr class="border success item-head">
                                                    <th class="text-center" style="width: 5%;"><input type="checkbox" v-model="check_all_item" @change="checkAllInvoice"></th>
                                                    <th class="text-center" style="width: 20%">Invoice </th> 
                                                    <th class="text-center" style="width: 15%">Payable Amount </th> 
                                                    <th class="text-center" style="width: 15%">Paid Amount</th>
                                                    <th class="text-center" style="width: 15%">Payment Amount</th>
                                                    <th class="text-center" style="width: 15%">Balance Amount</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(item, i) in purchase_form.items" :key="i" >
                                                    <td class="text-center">
                                                        <input type="checkbox" v-model="item.checked" @change="checkSingleItem">
                                                    </td>
                                                    <td>
                                                        {{ item.invoice_number }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ item.payable_amount }}
                                                    </td>

                                                    <td class="text-right">
                                                        {{ parseFloat(Number(item.total_paid_amount)).toFixed(2) }}
                                                    </td>
                                                    <!-- <td>                                                        
                                                       <input type="number" step="any" class="form-control border" v-model="item.adjust_amount">
                                                    </td> -->
                                                    <td class="text-right"> 
                                                        <input type="number" step="any" class="form-control border" :style="item.item_css" v-model="item.paid_amount" @input="pamountHandler($event, i)">
                                                    </td>
                                                    <td class="text-right">{{  parseFloat(Number(item.payable_amount)) - (parseFloat(Number(item.total_paid_amount)) + parseFloat(Number(item.paid_amount))) }}</td>
                                                    <!-- <td class="text-center">
                                                        <a href="javascript:void(0)" class="text-danger" style="font-size: 18px;" @click="removeAccountItem(i)"> <i class="mdi mdi-delete-outline"></i></a>
                                                    </td> -->
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <!-- <tr>
                                                    <td colspan="3"></td>
                                                    <td class="bg-success text-right">{{ Number(total_debit_amount).toFixed(2) }}</td>
                                                    <td class="bg-success text-right"> {{ Number(total_credit_amount).toFixed(2) }}</td>
                                                    <td colspan="3"> 
                                                        <a href="javascript:void(0)" class="text-primary float-right" style="font-size: 20px;" @click="addAccountItem()"> <i class="mdi mdi-plus-circle-outline"></i></a>
                                                    </td>
                                                </tr> -->
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="buttons" style="text-align: right;">
                                            <button type="button" class="btn btn-primary " @click.prevent="submitPayBill()" :disabled="disabled">
                                                <span v-show="isSubmit">
                                                    <i class="fas fa-spinner fa-spin" ></i>
                                                </span> Pay Bill 
                                            </button>
                                        </div>
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
        </div>
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';
import { json } from "body-parser";

export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: false,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            purchase_form: new Form({
                date: new Date().toISOString().slice(0,10),
                supplier_id: '',
                ledger_id: null,
                items: [],
                checked_items: [],
            }),
            purchase_form_validate: true,
            check_all_item: false,
            selected_item: [],
            suppliers: [],
            supplier_options: [],
            fiscal_years: [],
            active_min_date: '',
            active_max_date: '',
            accounts: [],
            renderOptionComponent: true,
            valueConsistsOf: 'BRANCH_PRIORITY',
            normalizer(node) {
                return {
                    id: node.id +'___'+node.code,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
            is_purchase_form: true,
            is_other_form: false,
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
        };
    },
    created() {
        this.fetchSuppliers();
        this.fetchAccountLedgers();
    },
    methods: {
        redirectRoute: function(route_link) {
            
        },

        forceRerender() {
            // Remove my-component from the DOM
            this.renderOptionComponent = false;

            this.$nextTick(() => {
                // Add the component back in
                this.renderOptionComponent = true;
            });
        },

        fetchSuppliers() {
            axios.get(this.apiUrl+'/suppliers', this.headerjson)
            .then((resp) => {
                this.suppliers = resp.data.data;
                this.supplier_options = [{label: "Select Supplier", value: ""}]
                resp.data.data.map((item) => {
                    this.supplier_options.push({label: item.name, value: item.id});
                });
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchAccountLedgers() {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOption', this.headerjson)
            .then((resp) => {
                this.accounts = resp.data.data.accounts;  
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            })
            .finally(() => {
                this.loading = false;
            });
        },

        onChangePaymentForm: function(event) {
            var formType = event.target.value;

            if(formType ==  "pb") {
                this.is_purchase_form = true;
                this.is_other_form = false;
            }else{
                this.is_purchase_form = false;
                this.is_other_form = true;
            }
        },

        onChangeSupplier: function(event) {
            var supplier_id = event;


            if(supplier_id != '') {
                var data = {supplier_id:supplier_id};
                axios.post(this.apiUrl+'/purchase_receives/getPurchaseReceiveData',data, this.headerjson)
                .then((res) => {
                    this.purchase_form.items = res.data.data;
                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                })
            }else{
                this.purchase_form.items = [];
            }
        },

        pamountHandler: function(event, index) {
            var newVal = event.target.value;
            var item_data   = this.purchase_form.items[index];
            if(item_data.payable_amount >= newVal) {
                item_data.item_css = 'text-align: right;';
                return true;
            }else{
                item_data.item_css = 'text-align: right; border: 1px solid red !important';
                this.$toast.error("Paid amount can't be getter than payable amount!")
            }
        },

        checkAllInvoice: function() {
            if(this.check_all_item) {
                this.purchase_form.items.map((item) => {
                    item.checked = true;
                    var index = this.selected_item.indexOf(item.receive_id);
                    if(index == -1) {
                        this.selected_item.push(item.receive_id);
                    }
                });

            }else{
                this.purchase_form.items.map((item) => {
                    item.checked = false;
                    var index = this.selected_item.indexOf(item.receive_id);
                    if(index > -1) {
                        this.selected_item.splice(index, 1);
                    }
                }); 
            }
            
        },

        checkSingleItem: function() {
            this.purchase_form.items.map((item) => {
                if(item.checked) {
                    var index = this.selected_item.indexOf(item.receive_id);
                    if(index == -1) {
                        this.selected_item.push(item.receive_id);
                    }
                }else{
                    var index = this.selected_item.indexOf(item.receive_id);
                    if(index > -1) {
                        this.selected_item.splice(index, 1);
                    }
                }
            });

            if(this.purchase_form.items.length == this.selected_item.length) {
                this.check_all_item = true;
            }else{
                this.check_all_item = false;
            }
        },

        //
        submitPayBill: function() {
            var selected_item = [];
            var match_item = [];
            this.purchase_form.items.map((item) => {
                if(item.checked) {
                    var index = selected_item.indexOf(item.receive_id);
                    if(index == -1) {
                        selected_item.push(item.receive_id)
                    }

                    if(item.payable_amount >= item.paid_amount) {
                        var mindex = match_item.indexOf(item.receive_id);
                        if(mindex == -1) {
                            match_item.push(item.receive_id);
                            this.purchase_form.checked_items.push(item);
                        }
                    }
                }
            });

            if(selected_item.length == match_item.length)  {
                this.isSubmit = true;
                this.disabled = true;

                var payment_ledger_id = (this.purchase_form.ledger_id != null) ? this.purchase_form.ledger_id : "";
                var formData    = new FormData();
                formData.append("date", this.purchase_form.date);
                formData.append("supplier_id", this.purchase_form.supplier_id);
                formData.append("ledger_id", payment_ledger_id);
                formData.append("invoice_items", JSON.stringify(this.purchase_form.checked_items));

                axios.post(this.apiUrl+'/accounts/payment-bill', formData, this.headers)
                .then(res => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(res.status == 200){
                        this.purchase_form.reset();
                        this.forceRerender();
                        this.$toast.success(res.data.message);
                    }else{
                        this.$toast.error(res.data.message);
                    }
                }).catch(err => { 
                    this.isSubmit = false; 
                    this.disabled = false;
                    console.log(err.response.data);
                    this.$toast.error(err.response.data.message);
                    if(err.response.status == 422){
                        this.errors = err.response.data.errors 
                    }
                });

            }else{
                this.$toast.error('Your selected item paid amount must be less than or equal payable amount!');
            }

        }, 

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        }, 

        inputChange: function() {

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
                formData.append("transaction_items", JSON.stringify(this.vform.account_items));
                axios.post(this.apiUrl+'/account_vouchers', formData, this.headers)
                .then((resp) => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(resp.status == 200){
                        this.vform.reset();
                        this.vtype_id = '';
                        this.vtype_value = this.$route.query.type ?? '';
                        this.total_debit_amount = '0.00';
                        this.total_credit_amount = '0.00';
                        this.forceRerender();
                        this.fetchVoucherCode();
                        this.vform.fiscalyear_id = this.fiscal_years[0].id;
                        this.$toast.success(resp.data.message); 
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
        }

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
    },
    watch: {
        'purchase_form.items': {
            handler: function(val, oldVal) {
                if(val.length > 0){
                    this.disabled = false;
                }else{
                    this.disabled = true;
                }
            },
            deep: true
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