<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box">
          <div class="page-title-right float-left">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item active">Accounts </li>
              <li class="breadcrumb-item">
                <a href="javascript: void(0);">Supplier Ledger</a>
              </li>
            </ol>
          </div>
          <div class="page-title-right float-right">
            <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal">
              Add New
            </button> -->
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-6">
              <div>
                <label for="outlet_id"> Company </label>
                <select class="form-control" @change="fetchSuppliers($event.target.value)">
                  <option value="">--- Select Company ---</option>
                  <option v-for="(company, i) in companies" :key="i" :value="company.id">
                    {{ company.name }}
                  </option>
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
            <h3 style="text-align: center;">Supplier Ledger</h3>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <div>
                  <label for="supplier_id"> Supplier *</label>
                  <select
                    class="form-control"
                    id="supplier_id"
                    v-model="search_terms.supplier_id"
                    @change="onkeyPress('supplier_id')"
                  >
                    <option value="">--- Select Supplier ---</option>
                    <option v-for="(supplier, i) in suppliers" :key="i" :value="supplier.id">
                      {{ supplier.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="errors.supplier_id">
                    {{ errors.supplier_id[0] }}
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div>
                  <label for="from_date"> From Date *</label>
                  <input
                    type="date"
                    class="form-control"
                    id="from_date"
                    v-model="search_terms.from_date"
                    @change="onkeyPress('from_date')"
                  />
                  <div class="invalid-feedback" v-if="errors.from_date">
                    {{ errors.from_date[0] }}
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div>
                  <label for="to_date"> To Date *</label>
                  <input
                    type="date"
                    class="form-control"
                    id="to_date"
                    v-model="search_terms.to_date"
                    @change="onkeyPress('to_date')"
                  />
                  <div class="invalid-feedback" v-if="errors.to_date">
                    {{ errors.to_date[0] }}
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="mt-3">
                  <button
                    type="submit"
                    class="btn-sm btn btn-primary"
                    :disabled="disabled"
                    @click="fetchSupplierLedgers()"
                  >
                    <span v-show="isSubmit">
                      <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    Submit
                  </button>
                  <a
                    href="javascript:void(0);"
                    style="margin-left: 5px;"
                    class="btn-sm btn btn-primary"
                    @click.prevent="printItem(item)"
                  >
                    <i class="mdi mdi-printer-outline me-1"></i>
                  </a>
                  <a
                    href="javascript:void(0);"
                    style="margin-left: 5px;"
                    class="btn-sm btn btn-primary"
                    @click.prevent="downloading ? null : exportToExcel()"
                  >
                    <span v-show="downloading">
                      <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <i class="mdi mdi-file-excel me-1"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" v-if="items.length > 0">
            <h4 style="text-align: center;">{{ supplier_name }}</h4>
            <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>
          </div>

          <div class="card-body">
            <h4>Opening Balance: {{ opening_balance }}</h4>

            <table
              id="basic-datatable"
              class="table table-striped dt-responsive nowarp w-100"
              v-if="!loading"
            >
              <thead class="tableFloatingHeaderOriginal">
                <tr class="success item-head">
                  <th width="5%" style="text-align: center">SL </th>
                  <th width="10%" style="text-align: center">Date </th>
                  <th width="15%" style="text-align: center">Opening Balance</th>
                  <th width="10%" style="text-align: center">PR. Amount</th>
                  <th width="10%" style="text-align: center">Payment </th>
                  <th width="10%" style="text-align: center">Return</th>
                  <th width="10%" style="text-align: center">Discount</th>
                  <th width="15%" style="text-align: center">Closing Balance</th>
                </tr>
              </thead>
              <tbody v-if="items.length > 0">
                <tr v-for="(item, index) in items" :key="index">
                  <td style="text-align: center">{{ index + 1 }} </td>
                  <td style="text-align: center">{{ item.transaction_date }} </td>
                  <td style="text-align: center">{{ item.opening_balance }} </td>
                  <td style="text-align: center">{{ item.purchase_receive_amount }} </td>
                  <td style="text-align: center">{{ item.payment_amount }} </td>
                  <td style="text-align: center">{{ item.return_amount }}</td>
                  <td style="text-align: center">{{ item.discount_amount }}</td>
                  <td style="text-align: center">{{ item.closing_balance }}</td>
                </tr>
              </tbody>
              <!-- Example footer (if you want to add totals, make sure to wrap with <tr>) -->
              <tfoot>
                <tr>
                  <td colspan="2" class="text-center fw-500">Totals</td>
                  <td style="text-align: center">{{ totalOpeningBalance }}</td>
                  <td style="text-align: center">{{ totalPurchaseReceiveAmount }}</td>
                  <td style="text-align: center">{{ totalPaymentAmount }}</td>
                  <td style="text-align: center">{{ totalReturnAmount }}</td>
                  <td style="text-align: center">{{ totalDiscountAmount }}</td>
                  <td style="text-align: center">{{ totalClosingBalance }}</td>
                </tr>
              </tfoot>
            </table>

            <div class="tab-pane show active" v-if="loading">
              <div class="row">
                <div class="col-md-5"></div>
                <div class="col-md-2">
                  <img
                    src="../../assets/image/loading.gif"
                    alt="Loading..."
                    style="width: 130px"
                  />
                </div>
                <div class="col-md-5"></div>
              </div>
            </div>
          </div>
        </div>

        <Modal @close="toggleModal()">
          <div class="modal-content scrollbar-width-thin orderPreview">
            <div class="modal-header">
              <button @click="toggleModal()" type="button" class="btn btn-default">
                X
              </button>
              <h3 style="width: 100%">Product Sales Report</h3>
            </div>
            <div class="modal-body" id="printArea">
              <div class="table-responsive product_table">
                <table class="table po_invoice">
                    <tbody>
                  <tr>
                    <td colspan="2" class="text-center" style="position: relative;">
                      <h5 class="text-uppercase">{{ retailShopName }}</h5>
                      <p>{{ retailShopAddress }} </p>
                      <p>Dhaka, Bangladesh</p>
                      <h4 style="text-align: center;">Supplier Ledger</h4>
                      <h4 style="text-align: center;">{{ supplier_name }}</h4>
                      <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>
                    </td>
                  </tr>
                  </tbody>
                </table>

                <table
                  style="width: 100%;"
                  class="table-bordered table-centered table-nowrap"
                >
                  <thead class="tableFloatingHeaderOriginal">
                    <tr class="success item-head">
                      <th width="5%" style="text-align: center">SL </th>
                      <th width="10%" style="text-align: center">Date </th>
                      <th width="15%" style="text-align: center">Opening Balance</th>
                      <th width="10%" style="text-align: center">PR. Amount</th>
                      <th width="10%" style="text-align: center">Payment </th>
                      <th width="10%" style="text-align: center">Return</th>
                      <th width="10%" style="text-align: center">Discount</th>
                      <th width="15%" style="text-align: center">Closing Balance</th>
                    </tr>
                  </thead>
                  <tbody v-if="items.length > 0">
                    <tr v-for="(item, index) in items" :key="index">
                      <td style="text-align: center">{{ index + 1 }} </td>
                      <td style="text-align: center">{{ item.transaction_date }} </td>
                      <td style="text-align: center">{{ item.opening_balance }} </td>
                      <td style="text-align: center">{{ item.purchase_receive_amount }} </td>
                      <td style="text-align: center">{{ item.payment_amount }} </td>
                      <td style="text-align: center">{{ item.return_amount }}</td>
                      <td style="text-align: center">{{ item.discount_amount }}</td>
                      <td style="text-align: center">{{ item.closing_balance }}</td>
                    </tr>
                  </tbody>
                  <!-- Optional footer here, same rule: wrap with <tr> -->
                </table>
              </div>
            </div>
          </div>
        </Modal>
      </div>
    </div>
  </div>
</template>

<script>
import Modal from "./../helper/Modal";
// import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';
import NumberToWord from './../page/NumberToWord.vue';   

export default {
    name: 'VoucherEntry',
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
            company_id : '',
            companies: [],
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
        this.fetchCompanies()
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

        onChangeConpany(companyId){
            this.company_id = companyId;
            this.fetchEntryTypes(companyId);
            this.fetchVoucherCode(companyId);
            this.fetchFiscalYear(companyId);
            this.fetchCostCenter(companyId);
            this.fetchAccountLedgers(companyId);
        },
        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;

                if(this.companies.length == 1){
                    const companyId = this.companies[0].id; 
                    this.company_id = companyId;
                    this.fetchEntryTypes(companyId);
                    this.fetchVoucherCode(companyId);
                    this.fetchFiscalYear(companyId);
                    this.fetchCostCenter(companyId);
                    this.fetchAccountLedgers(companyId);
                }
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        }, 
        
        fetchEntryTypes(company_id) {
            axios.get(this.apiUrl+'/entry_types?company_id='+company_id, this.headerjson)
            .then((resp) => {
                this.entry_types = resp.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchVoucherCode(company_id) {
            var data = {vtype: this.vtype_value}
            axios.post(this.apiUrl+'/accounts/getVoucherCode?company_id='+company_id, data, this.headerjson) 
            .then((resp) => {
                this.vform.vcode = resp.data.data.voucher_code;
                this.vtype_id = resp.data.data.voucher_type_id;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchFiscalYear(company_id) {
            axios.get(this.apiUrl+'/fiscal_years/getActiveFiscalYear?company_id='+company_id, this.headerjson) 
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

        fetchCostCenter(company_id) {
            axios.get(this.apiUrl+'/cost_centers?company_id='+company_id, this.headerjson)
            .then((resp) => {
                this.cost_centers = resp.data.data;
            })
            .catch((err) => {
                console.log(err);
            })
        },

        fetchAccountLedgers(company_id) {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOption?company_id='+company_id, this.headerjson)
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
            const companyId = this.company_id;

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
                this.fetchVoucherCode(companyId);
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
                formData.append("company_id", this.vform.company_id);
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
                        this.vform.reset();
                        this.vtype_id = '';
                        this.vtype_value = this.$route.query.type ?? '';
                        this.vitem  = resp.data.data;
                        this.forceRerender();
                        this.fetchVoucherCode(this.vform.company_id);
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