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
                                <form @submit.prevent="submitVoucherForm()" enctype="multipart/form-data" id="ventry_form" class="ventry_form">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="customer_id">Customers *</label>
                                                    <select class="form-control border" v-model="vform.customer_id" @change="onkeyPress('customer_id'), getDueInvoices($event)">
                                                        <option value="">--- Select Customer ---</option>
                                                        <option v-for="(customer, et) in customers" :key="et" :value="customer.id" :data-id="customer.id">{{ customer.name }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.customer_id">
                                                        {{ errors.customer_id }}
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

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="vtype_value">Payment Type *</label>
                                                    <select class="form-control border" v-model="vform.payment_type" @change="onkeyPress('payment_type'), onChangeType($event)">
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-if="errors.payment_type">
                                                        {{ errors.payment_type }}
                                                    </div>
                                                </div> 
                                            </div>

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
                                                    <div class="invalid-feedback" v-if="errors.fiscal_year_id">
                                                        {{ errors.fiscal_year_id }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row"> 
                                            <div class="col-md-9">
                                                <div class="row" v-if="vform.payment_type == 'bank'">
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

                                    <div class="col-md-12">
                                        <div class="mb-3"> 
                                            <table class="table table-bordered table-centered table-nowrap w-100">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="3%">  # </th> 
                                                        <th width="25%">Order Info</th> 
                                                        <th width="22%">Amounts</th> 
                                                        <th width="40%">Payments</th> 
                                                        <th width="5%" class="text-center">
                                                            <div class="form-check text-center">
                                                                <input type="checkbox" v-model="selectAllInvoice" @change="checkAllCheckboxes" id="check-all-checkboxes" /> 
                                                                <label for="check-all-checkboxes" class="form-check-label mt-8"> </label>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item, index) in payments" :key="index" v-if="invoices.length > 0">
                                                        <td>{{ index+1 }}</td>
                                                        <td>
                                                            <strong>Inv Number</strong>: {{ item.invoice_number }}<br />
                                                            <strong>Grand total</strong>: {{ item.grand_total }}<br />
                                                            <strong>Total Amount</strong>: {{ item.total_amount }}
                                                        </td>
                                                        <td><strong>Received Amount</strong>: {{ item.total_payment }}</td>
                                                        <td>
                                                            <label :for="'pay-amounts-' + index"><strong>Pay Amount</strong></label>
                                                            <input
                                                                v-model="item.pay_amount"
                                                                type="number"
                                                                step="any"
                                                                :name="'pay_amount[' + item.id + ']'"
                                                                @keypress="isNumberKey"
                                                                @change="calculatePaymentAmount(item.id)" 
                                                                class="form-control text-right pay-amounts rounded"
                                                                />
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="payment-checkboxes" v-model="item.selected" /> 
                                                        </td>
                                                    </tr>
                                                    <tr v-else>
                                                        <td colspan="5">
                                                            <div class="tab-pane show active center" v-if="invoiceLoading">
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
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>                                       
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="global_note"> Note</label>
                                            <textarea class="form-control border" rows="3" v-model="vform.global_note" @keypress="onkeyPress('global_note')"></textarea>
                                            <div class="invalid-feedback" v-if="errors.global_note">
                                                {{errors.global_note[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn-sm btn btn-primary float-right" :disabled="disabled"> 
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>
                                            <i class="mdi mdi-content-save-all"></i> Save
                                        </button>
                                        <button type="button" class="btn-sm btn btn-danger float-right" style="margin-right: 5px;"> <i class="mdi mdi-close"></i> Cancel</button>
                                    </div>
                                </form>                        
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
                                            <p class="text-uppercase text-center mt-2"><strong> {{ $store.getters.userData.user.outlet_name }}</strong></p>
                                            <p class="text-uppercase text-center mt-2">{{ $store.getters.userData.user.outlet_address }}</p><br>
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
                                                        <tr>
                                                            <td style="width: 15%">Amount Code</td>
                                                            <td style="width: 50%">Amount Hand</td>
                                                            <td style="width: 15%">Cost Center</td>
                                                            <td style="width: 10%" class="text-center">Debit</td>
                                                            <td style="width: 10%" class="text-center">Credit</td>
                                                        </tr>   
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
                                                        <tr>
                                                            <td colspan="3" class="text-center fw-500">Total Transaction</td>
                                                            <td style="text-align: right">{{ Number(vitem.total_debit_amount).toFixed(2) }}</td>
                                                            <td style="text-align: right">{{ Number(vitem.total_credit_amount).toFixed(2) }}</td>
                                                        </tr>
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
            invoiceLoading: false,
            isSubmit: false,
            disabled: false,
            showModal: false,
            modalActive:false,
            voucherModalPrintActive:false,
            errors: {},
            vitem: '',
            customers: [],  
            fiscal_years: [],
            active_min_date: '',
            active_max_date: '',
            cost_centers: [],   
            renderOptionComponent: true,  
            vform: new Form({ 
                customer_id: '',
                cost_center_id: '',
                payment_type: 'cash', 
                vdate: new Date().toISOString().slice(0,10),
                fiscalyear_id: '',
                cheque_no: '',
                cheque_date: '',
                global_note: '',                 
            }),
            selectAllInvoice: false,
            selectedItems: [],
            invoices: [],
            payments:[]
        };
    },
    created() {
        this.fetchCustomers(); 
        this.fetchFiscalYear();
        this.fetchCostCenter(); 
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

        fetchCustomers() {
            axios.get(this.apiUrl+'/customers', this.headerjson)
            .then((resp) => {
                this.customers = resp.data.data;
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
        formValidation : (...arrValue) => {
            arrValue.forEach((item) => { 
                this.$toast.error(item); 
            });
        },

        submitVoucherForm: function(event) {
            // validation Check 
            if(!this.vform.customer_id){
                this.$toast.error('Please select customer');  
                return false;
            }
            if(!this.vform.cost_center_id){
                this.$toast.error('Please select cost center ');  
                return false;
            }
            if(!this.vform.fiscalyear_id){
                this.$toast.error('Please select fiscal year');  
                return false;
            }
            if(!this.vform.payment_type){
                this.$toast.error('Please select payment type');  
                return false;
            }else if(this.vform.payment_type =='bank'){
                if(!this.vform.cheque_no){
                    this.$toast.error('Please select cheque no');  
                    return false;
                }
                if(!this.vform.cheque_date){
                    this.$toast.error('Please select cheque date');  
                    return false;
                }
            }
            this.selectedItems = []; 
            this.payments.forEach((item) => {
                if(item.selected){
                    this.selectedItems.push(item);
                }
            }); 

            if(this.selectedItems.length <= 0 ){
                this.$toast.error('Please select invoice');  
                return false;
            }
            this.isSubmit = true; 
            this.disabled = true;  

            if((this.payments.length > 0) && (this.vform.payment_type)) {
                var formData = new FormData();
                formData.append("customer_id", this.vform.customer_id);
                formData.append("cost_center_id", this.vform.cost_center_id);
                formData.append("payment_type", this.vform.payment_type); 
                formData.append("vdate", this.vform.vdate);
                formData.append("fiscal_year_id", this.vform.fiscalyear_id);
                formData.append("cheque_no", this.vform.cheque_no);
                formData.append("cheque_date", this.vform.cheque_date);
                formData.append("global_note", this.vform.global_note); 
                formData.append("invoice_items", JSON.stringify(this.selectedItems));
                axios.post(this.apiUrl+'/payment_collections', formData, this.headers)
                .then((resp) => {
                    this.isSubmit = false;
                    this.disabled = false;
                    if(resp.status == 200){ 
                        this.vform.reset(); 
                        this.vtype_value = this.$route.query.type ?? '';
                        this.vitem  = resp.data.data;
                        this.forceRerender(); 
                        this.vform.fiscalyear_id = this.fiscal_years[0].id;
                        this.$toast.success(resp.data.message); 
                        this.payments=[];
                        // setTimeout(() => {
                        //     this.printContent("printArea");
                        // }, 3000);

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
                this.loading = true;
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

        checkAllCheckboxesback() {
            if (this.selectAllInvoice) {
                this.payments = '';
                this.selectedItems = this.invoices.map((item) => item.id);  
                // let paymentMapData = this.invoices.map(function(element){
                //     return {    pay_amount: (element.grand_total - element.paid_amount) , 
                //                 inv_id:element.id, 
                //                 invoice_number:element.invoice_number, 
                //                 paid_amount:element.paid_amount, 
                //                 collection_amount:element.collection_amount, 
                //                 collection_amount:element.collection_amount, 
                //                 total_amount:element.total_amount, 
                //                 selected:true, 
                //             };
                // }) 
                // this.payments = paymentMapData;
                let paymentMapData = this.invoices.map(function(element){
                    return {    pay_amount: (element.grand_total - element.paid_amount) , 
                                inv_id:element.id, 
                                invoice_number:element.invoice_number, 
                                paid_amount:element.paid_amount, 
                                collection_amount:element.collection_amount, 
                                collection_amount:element.collection_amount, 
                                total_amount:element.total_amount, 
                                selected:true, 
                            };
                }) 
                this.payments = paymentMapData;
                
            } else {
                this.selectedItems = [];
            }
        },
        checkAllCheckboxes() {
            console.log('selectAllInvoice', this.selectAllInvoice)
            if (this.selectAllInvoice) {
                this.payments.forEach((item) => {
                    item.selected = this.selectAllInvoice;
                });               
            } else {
                this.payments.forEach((item) => {
                    item.selected = this.selectAllInvoice;
                }); 
            }
        },
        isNumberKey(event) {
        // Your isNumberKey logic here
        },
        calculatePaymentAmount(itemId) {
        // Your calculatePaymentAmount logic here, using itemId
        }, 
        getDueInvoices(customer_id){
            this.invoiceLoading = true;
            const selectedCustomerId = this.vform.customer_id; 

            axios.get(this.apiUrl+'/due-invoices?customer_id='+selectedCustomerId, this.headerjson)
            .then((response) => {  
                this.invoices = response.data.data.orders;   
                if(this.invoices.length <= 0){
                    this.$toast.error('Invoice item not found');
                }else{
                    this.$toast.success(response.data.message);
                }

                let paymentMapData = this.invoices.map(function(element){
                    return {    pay_amount: (element.grand_total - element.total_payment) , 
                                inv_id:element.id, 
                                invoice_number:element.invoice_number, 
                                paid_amount:element.paid_amount, 
                                collection_amount:element.collection_amount, 
                                collection_amount:element.collection_amount, 
                                total_amount:element.total_amount, 
                                total_payment:element.total_payment, 
                                selected:false, 
                            };
                }) 
                this.payments = paymentMapData; 
                this.invoiceLoading = false;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            })
            .finally(() => {
                this.invoiceLoading = false;
            });
        }

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