<template>
    <transition>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Bank Accounts </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Bank Account List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add New
                        </button> 
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
                                <select class="form-control" v-model="tableData.company_id" @change="getBankAccounts()">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <!-- <button @click="toggleModal()" type="button" class="btn btn-default float-right">X</button> -->
                    <button type="button" class="btn-close" @click="toggleModal()" aria-label="Close"></button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Account No *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('account_no')" v-model="form.account_no" id="account_no" placeholder="Account Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.account_no">
                                            {{errors.account_no[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="bank_name">Bank Name *</label>
                                        <input type="text" class="form-control border " @keyup="ledgerAccountNameSetup($event.target.value)" @keypress="onkeyPress('bank_name')" v-model="form.bank_name" id="bank_name" placeholder="Bank Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.bank_name">
                                            {{errors.bank_name[0]}}
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-md-6">
                                        <label for="name">Initial Balance</label>
                                        <input type="number" class="form-control border " @keypress="onkeyPress('initial_balance')" v-model="form.initial_balance" id="initial_balance" placeholder="Account Initial Balance" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.account_name">
                                            {{errors.account_name[0]}}
                                        </div>
                                    </div> -->

                                    <!-- <div class="form-group col-md-6">
                                        <label for="name">Company *</label>
                                        <select class="form-control border" v-model="form.company_id" @change="onkeyPress('company_id')" id="company_id">
                                            <option value="0">Select Company</option>
                                            <option v-for="(company, index) in companies" :value="company.id" :key="index">{{ company.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.company_id">
                                            {{errors.company_id[0]}}
                                        </div>
                                    </div> -->

                                    <div class="form-group col-md-12">
                                        <label for="name">Note</label>
                                        <textarea class="form-control border" rows="3" @keypress="onkeyPress('note')" v-model="form.note" id="note" placeholder="Note details here!" autocomplete="off"></textarea> 
                                        <div class="invalid-feedback" v-if="errors.note">
                                            {{errors.note[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="mb-3 mt-3">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_default" v-model="form.is_default" true-value="1" false-value="0" @change="onkeyPress('is_default')">
                                                <label class="form-check-label" for="is_default">Is Default</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_default">
                                                {{errors.is_default[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label for="dstatus">Status</label>
                                        <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="dstatus">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.status">
                                            {{errors.status[0]}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left; margin-bottom: 20px; background-color: #313A46; padding: 5px 5px; color: #ffffff">Account Ledger Setup *</label>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="bank_asset_account">Bank Asset Account Ledger *</label>
                                <input type="text" class="form-control border " @keypress="onkeyPress('bank_asset_account')" v-model="form.bank_asset_account" id="bank_asset_account" placeholder="Bank Asset Account Ledger"> 
                                <div class="invalid-feedback" v-if="errors.bank_asset_account">
                                    {{errors.bank_asset_account[0]}}
                                </div>
                            </div>

                            <!-- <div class="form-group col-md-6">
                                <label for="bank_loan_account">Bank Loan Account Ledger *</label>
                                <input type="text" class="form-control border " @keypress="onkeyPress('bank_loan_account')" v-model="form.bank_loan_account" id="bank_loan_account" placeholder="Bank Liability Account Ledger"> 
                                <div class="invalid-feedback" v-if="errors.bank_loan_account">
                                    {{errors.bank_loan_account[0]}}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="bank_charge_account">Bank Charge Account Ledger *</label>
                                <input type="text" class="form-control border " @keypress="onkeyPress('bank_charge_account')" v-model="form.bank_charge_account" id="bank_charge_account" placeholder="Bank Charge Account Ledger"> 
                                <div class="invalid-feedback" v-if="errors.bank_charge_account">
                                    {{errors.bank_charge_account[0]}}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="bank_interest_expense_account">Bank Interest Expense Account Ledger *</label>
                                <input type="text" class="form-control border " @keypress="onkeyPress('bank_interest_expense_account')" v-model="form.bank_interest_expense_account" id="bank_interest_expense_account" placeholder="Bank Expense Account For Interest"> 
                                <div class="invalid-feedback" v-if="errors.bank_interest_expense_account">
                                    {{errors.bank_interest_expense_account[0]}}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="bank_interest_income_account">Bank Interest Income Account Ledger *</label>
                                <input type="text" class="form-control border " @keypress="onkeyPress('bank_interest_income_account')" v-model="form.bank_interest_income_account" id="bank_interest_income_account" placeholder="Bank Income Account For Interest"> 
                                <div class="invalid-feedback" v-if="errors.bank_interest_income_account">
                                    {{errors.bank_interest_income_account[0]}}
                                </div>
                            </div> -->

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" @click="toggleModal()">Close</button>
                        <button type="submit" class="btn btn-primary " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{btn}} 
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <Datatable 
                            :columns="columns" 
                            :sortKey="tableData.sortKey"  
                            @sort="sortBy" 
                            v-if="!loading">
                            <template #header > 
                                <div class="tableFilters" style="margin-bottom: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="control" style="float: left;">
                                                <span style="float: left; margin-right: 10px; padding: 7px 0px;">Show </span>
                                                <div class="select" style="float: left;">
                                                    <select class="form-select" v-model="tableData.length" @change="getBankAccounts()">
                                                        <option value="5" selected="selected">5</option>
                                                        <option value="10" selected="selected">10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                                <span style="float: left; margin-left: 10px; padding: 7px 0px;"> Entries</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                             
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getBankAccounts()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="accounts.length > 0">
                                    <tr class="border" v-for="(item, i) in accounts" :key="i">
                                        <td>{{ item.account_no}} </td>
                                        <td>{{ item.bank_name }} </td>
                                        <!-- <td>{{ item.initial_balance }} </td>
                                        <td>{{ item.current_balance }} </td> -->
                                        <td>{{ (item.is_default == 1) ? 'Default' : 'N/A' }}</td>
                                        <td>{{ item.note }} </td>
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
                                                </div>
                                            </div> 
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="3"> No Data Available Here!</td>
                                    </tr>
                                </tbody>
                            </template> 
                            <template #footer>
                                <Pagination 
                                    :pagination="pagination"  
                                    :language="lang"
                                    @onChangePage="setPage" > 
                                </Pagination> 
                            </template> 
                        </Datatable>

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
    </div>
    </transition>
</template>

<script> 
import Modal from "../helper/Modal.vue"; 
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue'; 
import Form from "vform";
import axios from "axios";

export default {

    name: "Accounts",
    components: {
        Modal,
        Datatable,
        Pagination,
    },

    props:{
        language: {
          type: Object,
          default: () => {
            return {
              lengthMenu: null,
              info: null,
              zeroRecords: null, 
              search: null
            }
          },
        },
    },  
    
    data() {

        return {
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            btn:'Create',
            companies: [],
            form: new Form({
                id: '',
                account_no: '',
                bank_name: '',
                initial_balance: 0,
                company_id: 0,
                note: '',
                is_default: 0,
                status: 1,
                bank_asset_account: '',
                // bank_loan_account: '',
                // bank_charge_account: '',
                // bank_interest_expense_account: '',
                // bank_interest_income_account: '',

            }),
            accounts: [],
            columns: [       
                {
                    label: 'Account No',
                    name: 'account_no',           
                    width: '15%'
                },   
                {
                    label: 'Bank Name',
                    name: 'bank_name',
                    width: '15%'
                },
                // {
                //     label: 'Initial Balance',
                //     name: 'initial_balance',
                //     width: '10%'
                // },
                // {
                //     label: 'Current Balance',
                //     name: 'current_balance',
                //     width: '10%'
                // },
                {
                    label: 'Default',
                    name: '',
                    width: '10%'
                },
                {
                    label: 'Note',
                    name: 'note',
                    width: '15%'
                },
                {
                    label: 'Actions',            
                    name: '',
                    isSearch: false,
                    isAction: true,
                    width: '5%'

                }
            ],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc',
                sortKey: 'bank_name',
                company_id:''
            },
            lang: {
                lengthMenu: this.$props.language.lengthMenu ? this.$props.language.lengthMenu : 'Show_MENU_entries',
                info: this.$props.language.info ? this.$props.language.info : 'Showing_FROM_to_TO_of_TOTAL_entries',
                zeroRecords: this.$props.language.zeroRecords ? this.$props.language.zeroRecords : 'No data available in table.', 
                search: this.$props.language.search ? this.$props.language.search : 'Search'
            },
            pagination: {
                lastPage: '',
                currentPage: '',
                total: '',
                lastPageUrl: '',
                nextPageUrl: '',
                prevPageUrl: '',
                from: '',
                to: '',
                links:[],
            }
            
        }
    },
    created() { 
        this.fetchCompany();
    },

    methods: {
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            if(!this.modalActive){
                this.editMode = false;
                this.btn='Create';
            } 
            this.errors = '';
            this.isSubmit = false;
            this.form.reset();
        },

        fetchCompany() { 
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => {
                this.companies = res.data.data;
                if(this.companies.length == 1){
                    this.tableData.company_id = this.companies[0].id;
                    this.getBankAccounts();
                }
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }).finally(() => {
                this.loading = false;
            });
        },

        // Ledger Account Name Setup
        ledgerAccountNameSetup: function(value) {
            this.form.bank_asset_account = value;
            // this.form.bank_loan_account = value,
            // this.form.bank_charge_account = value,
            // this.form.bank_interest_expense_account = value,
            // this.form.bank_interest_income_account = value
        },


        edit: function(item) {
            this.btn='Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item); 
        },
        submitForm: function(e) { 
            
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('account_no', this.form.account_no);
            formData.append('bank_name', this.form.bank_name);
            // formData.append('initial_balance', this.form.initial_balance);
            // formData.append('company_id', this.form.company_id);
            formData.append('note', this.form.note);
            formData.append('is_default', this.form.is_default);
            formData.append('status', this.form.status); 
            formData.append('bank_asset_account', this.form.bank_asset_account); 
            // formData.append('bank_loan_account', this.form.bank_loan_account); 
            // formData.append('bank_charge_account', this.form.bank_charge_account); 
            // formData.append('bank_interest_expense_account', this.form.bank_interest_expense_account); 
            // formData.append('bank_interest_income_account', this.form.bank_interest_income_account); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/bank_accounts/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/bank_accounts', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getBankAccounts(this.apiUrl+'/bank_accounts/list?page='+this.pagination.currentPage);
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            }).catch(err => {  
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            })
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        deleteItem: function(item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/bank_accounts/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getBankAccounts(this.apiUrl+'/bank_accounts/list?page='+this.pagination.currentPage);
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
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },

        // For Pagination 
        getBankAccounts(url = this.apiUrl+'/bank_accounts/list') {
            this.loading = true;
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw = data.draw) {
                    this.accounts = data.data.data;
                    this.configPagination(data.data);
                }
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
            .finally((fres) => {
                this.loading = false;
            });
        },

        configPagination(data){
            this.pagination.lastPage = data.last_page;
            this.pagination.currentPage = data.current_page;
            this.pagination.total   = data.total ? data.total : 0;
            this.pagination.lastPageUrl = data.last_page_url;
            this.pagination.nextPageUrl = data.next_page_url;
            this.pagination.prevPageUrl = data.prev_page_url;
            this.pagination.from = data.from ? data.from : 0;
            this.pagination.to = data.to ? data.to : 0;  
            this.pagination.links = data.links;
        },

        sortBy(key,sortable) {
            this.tableData.sortKey = key;
            //this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = sortable; ///this.sortOrders[key] === 1 ? 'asc' : 'desc'; 
            this.getBankAccounts();
        },
        setPage(data){  
            this.getBankAccounts(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
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
    width: 900px;
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
</style>