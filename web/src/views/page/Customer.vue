<template>
    <transition  >
    <div class="container-fluid ">
        <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Customer </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Customer List</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-success float-right" style="margin-left: 7px;" @click="toggleImportModal">
                                <i class="fas fa-plus"></i> Bulk Add Customer
                            </button>
                            <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                              Add New
                            </button>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="row">
                <div class="col-12">
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
                                                        <select class="form-select" v-model="tableData.length" @change="getCustomerList()"> 
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
                                                <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getCustomerList()">
                                            </div>
                                        </div>
                                    </div>   
                                </template> 
                                <template #body >                            
                                    <tbody v-if="customers.length > 0">
                                        <tr class=" " v-for="(item, i) in customers" :key="i">
                                            <td>{{ i + 1 }} </td> 
                                            <td>{{ item.customer_code }} </td> 
                                            <td>{{ item.name}} </td> 
                                            <td>{{ item.phone }} </td>
                                            <td>{{ item.email }}</td>
                                            <td>{{ item.address }}</td>
                                            <td>{{ item.customer_group_name }}</td> 
                                            <td>
                                                <div class="dropdown float-end">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <!-- item-->
                                                        <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                        <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)">
                                                        <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                        <!-- item-->
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

            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header"> 
                        <h3>Customer Create</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row  ">
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="customer_code">Customer Code *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('customer_code')" v-model="form.customer_code" id="customer_code" placeholder="Customer Code" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.customer_code">
                                                    {{errors.customer_code[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group col-md-2">
                                            <div class="mb-3">
                                                <label for="emp_code">EMP ID *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('emp_code')" v-model="form.emp_code" id="emp_code" placeholder="EMP ID" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.emp_code">
                                                    {{errors.emp_code[0]}}
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="company_id">Company </label> 
                                                <select class="form-control border" v-model="form.company_id" @change="onkeyPress('company_id')" id="company_id">
                                                    <option value="0">Select company</option>
                                                    <option v-for="(company, index) in companies" :value="company.id" :key="index">
                                                        {{company.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.company_id">
                                                    {{errors.company_id[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="company_id">Customer Group *</label> 
                                                <select class="form-control border" v-model="form.customer_group_id" @change="onkeyPress('customer_group_id')" id="customer_group_id">
                                                    <option value="0" selected>Select Customer Group</option>
                                                    <option v-for="(c_group, index) in customer_groups" :value="c_group.id" :key="index"> {{c_group.title}}
                                                    </option>                                                    
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.customer_group_id">
                                                    {{errors.customer_group_id[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row  ">
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="name">Name *</label>
                                                <input type="text" class="form-control border" @keyup="ledgerAccountNameSetup($event.target.value)" @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Customer Name" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.name[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="email">Email </label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('email')" v-model="form.email" id="email" placeholder="Customer Email" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.email">
                                                    {{errors.email[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="dob">Date of Birth </label>
                                                <input type="date" class="form-control border " @keypress="onkeyPress('dob')" v-model="form.dob" id="dob" placeholder="" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.dob">
                                                    {{errors.dob[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="phone">Customer Number *</label>
                                                <input type="text" class="form-control border" @keypress="onkeyPress('phone')" v-model="form.phone" id="phone" placeholder="Customer mobile number"> 
                                                <div class="invalid-feedback" v-if="errors.phone">
                                                    {{errors.phone[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="address">Address *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('address')" v-model="form.address" id="address" placeholder="Customer Address"> 
                                                <div class="invalid-feedback" v-if="errors.address">
                                                    {{errors.address[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">  
                                        <div class="form-group col-md-4">
                                            <div class="mb-3"> 
                                                <label for="district_id">District</label>
                                                <select class="form-control border" v-model="form.district_id" @change="onkeyPress('district_id')" id="district_id">
                                                    <option value="0">Select district</option>
                                                    <option v-for="(district, index) in districts" :value="district.id" :key="index"> {{district.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.district_id">
                                                    {{errors.district_id[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="mb-3"> 
                                                <label for="area_id">Area</label>
                                                <select class="form-control border" v-model="form.area_id" @change="onkeyPress('area_id')" id="district_id">
                                                    <option value="0">Select area </option>
                                                    <option v-for="(area, index) in areas" :value="area.id" :key="index"> {{area.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.area_id">
                                                    {{errors.area_id[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="mb-3"> 
                                                <label for="postal_code">Postal Code</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('postal_code')" v-model="form.postal_code" id="postal_code" placeholder="Postal Code" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.postal_code">
                                                    {{errors.postal_code[0]}}
                                                </div>
                                            </div>
                                        </div>
                                         
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <div class="mb-3"> 
                                                <label for="discount_percent">Discount Percent *</label>
                                                <input type="number" class="form-control border " @keypress="onkeyPress('discount_percent')" v-model="form.discount_percent" id="discount_percent" placeholder="Discount Percent" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.discount_percent">
                                                    {{errors.discount_percent[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group col-md-4">
                                            <div class="mb-3">
                                                <label for="status">Status *</label>
                                                <select class="form-control border" v-model="form.status" @change="onkeyPress('status')">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.status">
                                                    {{errors.status[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-3"> 
                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input border " @change="onkeyPress('wholesale_customer')" true-value="1" false-value="0" v-model="form.wholesale_customer" id="wholesale_customer"> Wholesale Customer
                                                <div class="invalid-feedback" v-if="errors.wholesale_customer">
                                                    {{errors.wholesale_customer[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="form-group col-md-3"> 
                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input border " @change="onkeyPress('sale_without_vat')" true-value="1" false-value="0" v-model="form.sale_without_vat" id="sale_without_vat"> Sale Without VAT
                                                <div class="invalid-feedback" v-if="errors.sale_without_vat">
                                                    {{errors.sale_without_vat[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="form-group col-md-3"> 
                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input border " @change="onkeyPress('credit_customer')" true-value="1" false-value="0" v-model="form.credit_customer" id="credit_customer"> Credit Customer
                                                <div class="invalid-feedback" v-if="errors.credit_customer">
                                                    {{errors.credit_customer[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="form-group col-md-3"> 
                                            <div class="mb-3">
                                                <input type="checkbox" class="form-check-input border " @change="onkeyPress('store_customer')" true-value="1" false-value="0" v-model="form.store_customer" id="store_customer"> Store Customer
                                                <div class="invalid-feedback" v-if="errors.store_customer">
                                                    {{errors.store_customer[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                    </div>

                                    

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left; margin-bottom: 20px; background-color: #313A46; padding: 5px 5px; color: #ffffff">Account Ledger Setup *</label>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="customer_receivable_account">Customer Receivable Account Ledger *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('customer_receivable_account')" v-model="form.customer_receivable_account" id="customer_receivable_account" placeholder="Customer Receivable Account"> 
                                            <div class="invalid-feedback" v-if="errors.customer_receivable_account">
                                                {{errors.customer_receivable_account[0]}}
                                            </div>
                                        </div>
                                    </div>

                                </div> 
                            </div>
                        </div>
                        <div class="modal-footer"> 
                            <button type="submit" class="btn btn-primary " :disabled="disabled">
                                <span v-show="isSubmit">
                                    <i class="fas fa-spinner fa-spin" ></i>
                                </span>{{btn}} 
                            </button> 
                        </div>
                    </form>
                </div>
            </Modal>

            <!--Bulk Add Customer Modal -->
            <Modal @close="toggleImportModal" :modalActive="importModal">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header"> 
                        <button @click="toggleImportModal" type="button" class="btn btn-default">X</button>
                        <h5 style="text-align: right">Import Customers</h5>
                    </div>

                    <div class="modal-body">  
                        <div class="row">
                            <div class="col-md-12">
                                <form role="form" @submit.prevent="submitImportForm()" enctype="multipart/form-data">
                                    <p style="font-size: 13px; font-style: italic;">The field labels marked with * are required input fields.</p>
                                    <p style="font-size: 16px;">The correct column order is (customer_code*, name*, email, phone*, address*, dob, customer_group*, company) and you must follow this.</p>
                                    <p style="font-size: 16px;">Customer Code and Phone both field data must be unique.</p>
                                    <p style="font-size: 16px;">Customer Group and Company Must be enlisted Before Customer Add (if don't have)</p>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="excel_file"> Upload EXCEL File *</label>
                                                <input type="file" class="form-control" id="excel_file" ref="file" name="..." @change="customerImportFile(), onkeyPress('excel_file')">
                                                
                                                <div class="invalid-feedback" v-if="errors">
                                                    <p v-for="(error, i) in errors" :key="i">{{ error[0] }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label> Sample File</label>
                                                <a :href="samplefile_url" class="btn btn-info" style="display: block; width: 100%; clear:both;" download><i class="fas fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <button type="submit" class="btn btn-primary" :disabled="disabled_upload">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </Modal>

        </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'Customer',
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
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            disabled_upload: false,
            modalActive:false,
            importModal: false,
            errors: {},
            btn:'Create',
            items: [],
            customers: [],
            customer_groups: [],
            districts: [],
            areas: [],
            loading: true,
            companies: [],
            form: new Form({
                id: '',
                customer_code: '', 
                emp_code: '', 
                company_id: 0, 
                customer_group_id: 1, 
                name: '',
                email: '',
                phone: '',
                address: '',
                dob: '',
                district_id: '0',
                area_id: '0',
                postal_code: '',
                discount_percent: 0,
                wholesale_customer: 0,
                sale_without_vat: 0,
                credit_customer: 0,
                store_customer: 0,
                status: 1,

                customer_receivable_account: '',
            }),
            importFile: '',
            samplefile_url: this.baseUrl+'/import_excel_demo/customers.xlsx',
            
            columns: [ 
                {
                    label: 'SL',
                    name: '',  
                    isSearch: false, 
                    isAction: true,         
                    width: '5%'
                },   
                {
                    label: 'Code',
                    name: 'customer_code',           
                    width: '10%'
                },   
                {
                    label: 'Name',
                    name: 'name',
                    width: '15%'
                },
                {
                    label: 'Phone',
                    name: 'phone',
                    width: '10%'
                },
                {
                    label: 'Email',
                    name: 'email',
                    width: '10%'
                },
                {
                    label: 'Address',
                    name: 'address',
                    width: '10%'
                },
                {
                    label: 'Group Name',
                    name: 'customer_group_name',
                    width: '15%'
                },
                {
                    label: 'Actions',            
                    name: '',
                    isSearch: false, 
                    isAction: true,
                    width: '5%',

                }
            ],  
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 2,
                dir: 'asc',
                sortKey: 'name', 
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

        };
    },
    created() { 
        this.getCustomerList();
        this.fetchIndexData();
        this.fetchCompany()
        this.fetchDistrict()
        this.fetchArea();
        this.fetchCustomerGroup(); 
    },
    methods: { 
        toggleImportModal: function() {
            this.importModal = !this.importModal;
            this.importFile = '';
            this.$refs.file.value=null;
            this.disabled_upload = true;
            this.errors = '';
        },
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
        fetchIndexData() { 
            axios.get(this.apiUrl+'/customers', this.headerjson)
            .then((res) => {
                this.items = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        }, 
        fetchCompany() { 
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => {
                this.companies = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        }, 
        fetchCustomerGroup() { 
            axios.get(this.apiUrl+'/customer_groups', this.headerjson)
            .then((res) => {
                this.customer_groups = res.data.data; 
            }) 
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            })
        }, 
        fetchDistrict() { 
            axios.get(this.apiUrl+'/districts', this.headerjson)
            .then((res) => {
                this.districts = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },
        fetchArea() { 
            axios.get(this.apiUrl+'/areas',this.headerjson)
            .then((res) => {
                this.areas = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        }, 
        
        customerImportFile() {
            this.importFile = this.$refs.file.files[0];
            if(this.importFile != '') {
                this.errors = ''
            }
        },

        checkImportRequiredPrimary() {
            if(this.$refs.file.value != "") {
                this.disabled_upload = false;
            }else{
                this.disabled_upload = true;
            }
        },

        // Ledger Account Name Setup
        ledgerAccountNameSetup: function(value) {
            this.form.customer_receivable_account = value;
        },

        edit: function(item) { 
            this.btn='Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item);  
        },
        checkForm: function(e) {

        },
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();              
            formData.append('customer_code', this.form.customer_code);
            // formData.append('emp_code', this.form.emp_code);
            formData.append('company_id', this.form.company_id);
            formData.append('customer_group_id', this.form.customer_group_id);
            formData.append('name', this.form.name);
            formData.append('email', this.form.email);
            formData.append('phone', this.form.phone);
            formData.append('address', this.form.address);
            formData.append('dob', this.form.dob);
            formData.append('district_id', this.form.district_id);
            formData.append('area_id', this.form.area_id);
            formData.append('postal_code', this.form.postal_code);
            formData.append('discount_percent', this.form.discount_percent);
            formData.append('wholesale_customer', this.form.wholesale_customer);
            formData.append('sale_without_vat', this.form.sale_without_vat);
            formData.append('credit_customer', this.form.credit_customer);
            formData.append('store_customer', this.form.store_customer);
            formData.append('status', this.form.status);

            formData.append('customer_receivable_account', this.form.customer_receivable_account);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/customers/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/customers', formData, this.headers);
            }           

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchIndexData();
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
            });
        }, 

        submitImportForm: function(e) {  
            this.isSubmit = true;
            this.disabled_upload = true;
            const formData = new FormData();
            formData.append("excel_file", this.importFile);
                    
            var postEvent = axios.post(this.apiUrl+'/customers/customerBulkUpload', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled_upload = false;
                if(res.status == 200){
                    this.importFile = '';
                    this.disabled_upload = true;
                    this.toggleImportModal();
                    this.fetchIndexData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
            }).catch(err => { 
                this.isSubmit = false; 
                this.importFile = '';
                this.$refs.file.value=null;
                this.disabled_upload = true;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },

        validation: function (...fiels){ 
            var obj = new Object(); 
            var validate = false;
            for (var k in fiels){     // Loop through the object  
                for (var j in this.form){  
                    if((j==fiels[k]) && (!this.form[j])) {  
                        obj[fiels[k]] = fiels[k].replace("_", " ")+' field is required';  // Delete obj[key]; 
                        this.errors = {...this.errors, ...obj};
                    }else{
                        validate = false;
                    }
                }              
            }  
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },
        onkeyPress: function(field) { 
            this.checkImportRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        deleteItem: function(item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this customer!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/customers/'+item.id,this.headerjson)
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchIndexData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },

        // For Pagination 
        getCustomerList(url = this.apiUrl+'/customers/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData,  headers:this.headerparams})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.customers = data.data.data;
                    this.configPagination(data.data);
                }
            })
            .catch(errors => {
                this.$toast.error(errors.response.data.message);
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
            this.getCustomerList();
        },
        setPage(data){  
            this.getCustomerList(data.url); 
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
    border: none !important;
    width: 900px
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