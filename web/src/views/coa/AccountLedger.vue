<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Account Ledger </a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" @click="createAccountLedgerModal()">
                            <i class="mdi mdi-plus-outline"></i> Create New Ledger
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
                                <select class="form-control"  v-model="tableData.company_id" @change="loadDataBasedOnCompany($event.target.value)">
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
        <Modal @close="createAccountLedgerModal()" :modalActive="modalAccLedgerActive">
            <div class="modal-content scrollbar-width-thin account_groups">
                <div class="modal-header"> 
                    <h3>{{ btn }} Ledger</h3>
                    <button @click="createAccountLedgerModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountLedgerForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <!-- <div v-if="loadingModalData">
                            <span class="loader"></span>
                        </div> -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="">
                                            <label for="outlet_id"> Company </label>
                                            <select v-model="form.company_id" class="form-control" @change="fetchGroupData($event.target.value)">
                                                <option value="">--- Select Company ---</option>
                                                <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group col-md-12">
                                        <label for="class_id">Account Group *</label> <br>
                                        <!-- <select class="form-control border " @change="onChangeAccountGroup($event.target.value), onkeyPress('class_id')" v-model="form.class_id" id="class_id"> 
                                            <option value="">--- Select Account Group ---</option>
                                            <option v-for="(group, i) in groups" :key="i" :value="group.id">{{ '['+group.code +'] '+group.name }}</option>
                                        </select> -->
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="form.class_id"
                                            placeholder="--- Select Account Group ---"  
                                            @change="onChangeAccountGroup($event)" 
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="groups"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            :create-option="true" 
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.class_id">
                                            {{errors.class_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="type_id"> Type *</label> <br>
                                        <!-- <select class="form-control border " @change="onChangeAccountType($event.target.value), onkeyPress('type_id')" v-model="form.type_id" id="type_id"> 
                                            <option value="">--- Select Type ---</option>
                                            <option v-for="(type, i) in parent_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                        </select> -->
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="form.type_id"
                                            placeholder="--- Select Account Type ---"  
                                            @change="onChangeAccountType($event)" 
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="parent_type_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            :create-option="true" 
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.type_id">
                                            {{errors.type_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="detail_type_id"> Detail Type *</label>
                                        <!-- <select class="form-control border " @change="onChangeAccountDetailType($event.target.value), onkeyPress('detail_type_id')" v-model="form.detail_type_id" id="detail_type_id"> 
                                            <option value="">--- Select Detail Type ---</option>
                                            <option v-for="(type, i) in detail_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                        </select> -->
                                        
                                        <treeselect 
                                            v-model="form.detail_type_id"
                                            :multiple="false" 
                                            :always-open="false"
                                            :options="detail_types"
                                            :normalizer="normalizer"
                                            :value-consists-of="valueConsistsOf"
                                            :default-expand-level="Infinity"
                                            :search-nested="true"
                                            @select="onChangeAccountDetailType($event)"                                                
                                            placeholder='--- Select Detail Type ---'
                                            v-if="renderOptionComponent"
                                        />
                                        <div class="invalid-feedback" v-if="errors.detail_type_id">
                                            {{errors.detail_type_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="ledger_code">Ledger Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_code')" v-model="form.ledger_code" id="ledger_code" placeholder="Enter Ledger Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.ledger_code">
                                            {{errors.ledger_code[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="ledger_name">Ledger Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_name')" v-model="form.ledger_name" id="ledger_name" placeholder="Enter Ledger Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.ledger_name">
                                            {{errors.ledger_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="ledger_type">Opening Balance Type *</label>
                                        <select class="form-control border " v-model="form.ledger_type" id="ledger_type"> 
                                            <option value="dr">Debit</option>
                                            <option value="cr">Credit</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.ledger_type">
                                            {{errors.ledger_type[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="opening_balance">Opening Balance *</label>
                                        <input type="number" step="any" class="form-control border" @keypress="onkeyPress('opening_balance')" v-model="form.opening_balance" id="opening_balance" placeholder="Enter Opening Balance" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.opening_balance">
                                            {{errors.opening_balance[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="balance_date">Balance Date*</label>
                                        <input type="date" class="form-control border" @keypress="onkeyPress('balance_date')" v-model="form.balance_date" id="balance_date" placeholder="Enter Balance Date" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.balance_date">
                                            {{errors.balance_date[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="ledger_status">Status *</label>
                                        <select class="form-control border " @change="onkeyPress('ledger_status')" v-model="form.status" id="ledger_status">
                                            <option value="1">Active</option>
                                            <option value="0">InActive</option>
                                        </select> 
                                        <div class="invalid-feedback" v-if="errors.ledger_status">
                                            {{errors.ledger_status[0]}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit">
                            <span v-show="isTypeSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{ btn }} 
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
                                                    <select class="form-select" v-model="tableData.length" @change="getAccountsLedgers()">
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getAccountsLedgers()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="accounts.length > 0">
                                    <tr class="border" v-for="(item, i) in accounts" :key="i">
                                        <td class="text-center">{{ i + 1 }}</td>
                                        <td>{{ item.ledger_name }}</td>
                                        <td>{{ item.ledger_code }}</td>
                                        <td>{{ item.detail_type_name }}</td>
                                        <td>{{ item.type_name }}</td>
                                        <td class="text-center" v-if="item.status == '1'"><span class="badge bg-success">Active</span></td>
                                        <td class="text-center" v-else><span class="badge bg-danger">Inactive</span></td>
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
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                    <img src="../../assets/image/loading.gif" alt="Loading..." style="width: 130px;">
                                </div>
                                <div class="col-md-5"></div>
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'PosLeftbar',
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
            loadingModalData: true,
            showModal: false,
            editMode:false,
            disabledTypeSubmit: false,
            modalAccLedgerActive:false,
            isTypeSubmit:false,
            errors: {},
            btn:'Create',
            items: [],
            companies: [],
            accounts: [],
            groups: [],
            parent_types: [],
            parent_type_options: [],
            detail_types: [],
            temp_item: '',
            renderOptionComponent: true,
            valueConsistsOf: 'BRANCH_PRIORITY',
            normalizer(node) {
                return {
                    id: node.id,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
            form: new Form({
                id: '',
                class_id: '',
                type_id: '',
                company_id: '',
                detail_type_id: null,
                ledger_code: '',
                ledger_name: '',
                ledger_type: 'dr',
                opening_balance: 0,
                balance_date: '',
                status: 1
            }), 
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 

            columns: [       
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                },   
                {
                    label: 'Ledger Name',
                    name: 'ledger_name',
                    width: '15%'
                },
                {
                    label: 'Ledger Code',
                    name: 'ledger_code',
                    width: '10%'
                },
                {
                    label: 'Detail Type',
                    name: 'account_types.name',
                    width: '15%'
                },
                {
                    label: 'Type',
                    name: 'account_types.parent_types.name',
                    width: '10%'
                },
                {
                    label: 'Status',
                    name: 'status',
                    width: '10%'
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
                column: 1,
                dir: 'desc',
                sortKey: 'ledger_name',
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
        };
    },
    created() {
        // this.fetchAccountLedger();
        this.fetchCompanies();
        this.getAccountsLedgers();
        this.fetchGroupData();
    },
    methods: { 

        forceRerender() {
            // Remove my-component from the DOM
            this.renderOptionComponent = false;

            this.$nextTick(() => {
                // Add the component back in
                this.renderOptionComponent = true;
            });
        },
        loadDataBasedOnCompany: function(companyId){ 
            this.fetchGroupData(companyId); 
            this.getAccountsLedgers(companyId); 
        },

        createAccountLedgerModal: function() {
            this.forceRerender();
            this.modalAccLedgerActive = !this.modalAccLedgerActive;
    
            if(!this.modalAccLedgerActive) {
                this.editMode = false;
                this.btn='Create';
                this.form.reset(); 
                this.temp_item = '';
            }
            this.errors = '';
            this.isLedgerSubmit = false;
        },
        
        fetchAccountLedger() { 
            axios.get(this.apiUrl+'/account_ledgers', this.headerjson)
            .then((res) => {
                this.items = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        async fetchGroupData(companyId) {
          await  axios.get(this.apiUrl+'/account_classes?company_id='+companyId, this.headerjson)
            .then((res) => {
                this.groups = res.data.data.account_classes.map((item) => {
                    return {label: '['+item.code+'] '+item.name, value: item.id};
                });
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        async fetchParentTypes(group_id = '', type_id = '') {
            if(group_id != '') {
                var urlAction = axios.get(this.apiUrl+'/account_types/'+group_id+'/getParentTypes', this.headerjson);
            }else{
                urlAction = axios.get(this.apiUrl+'/account_types/getParentTypes', this.headerjson);
            }

            await urlAction.then((res) => {
                this.parent_types = res.data.data;
                this.parent_type_options = res.data.data.map((item) => {
                    return {label: '['+item.type_code+'] '+item.type_name, value: item.id};
                });
                this.fetchDetailTypes(type_id); 
            })
            .catch((err) => { 
                //console.log(err);
                this.$toast.error(err);
            }); 
        },

        async fetchDetailTypes(type_id) { 
            if(type_id){ 
                this.loadingModalData = true;
                this.forceRerender(); 
                let account_type = this.parent_types.find(item => item.id == type_id); 
                let urlAction = axios.post(this.apiUrl+'/account_types/getChartOfAccountsOnlyDetailTypeOptions', {types: JSON.stringify(account_type)}, this.headerjson);
            
                await urlAction.then((res) => {
                    this.detail_types = res.data.data;
                })
                .finally( () =>{
                    this.loadingModalData = false;
                })
            }
        },

        fetchLedgerCode(company_id, reference_id='', type='') {
            if(this.editMode == true && reference_id == this.temp_item.detail_type_id) {
                this.form.ledger_code = this.temp_item.ledger_code;
            }else{
                var formData = new FormData();
                formData.append("reference_id", reference_id);
                formData.append("reference_type", type);
                formData.append("company_id", company_id);
                axios.post(this.apiUrl+'/account_ledgers/getAccountCode', formData, this.headers)
                .then((res) => {
                    this.form.ledger_code = res.data.data.account_code;
                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                });
            }
        },

        onChangeAccountGroup(value) {
            var group_id = value;
            if(group_id != '' || group_id != 0) {
                this.fetchParentTypes(group_id);
            }
        },
        
        onChangeAccountType(value) {
            var type_id = value;
            if(type_id != '' || type_id != 0) {

                this.fetchDetailTypes(type_id);
            }
        },
        
        onChangeAccountDetailType(item) {
            var detail_type_id = item.id;
            let company_id = item.company_id; 
            if(detail_type_id != '' || detail_type_id != 0) {

                this.fetchLedgerCode(company_id, detail_type_id, "dtype");
            }
        },

        edit: function(item) { 
            this.forceRerender();
            this.btn='Update';
            this.editMode = true;
            this.createAccountLedgerModal();
            this.form.fill(item);  
                this.temp_item = item;
            this.fetchParentTypes(item.class_id, item.type_id); 
            // if(this.parent_types.length > 0){
                
                this.form.fill(item);  
                this.temp_item = item;
                // setTimeout(() => {
                   
                // }, 3000);
           //}
        },

        submitAccountLedgerForm: function(e) { 

            this.isTypeSubmit = true;
            this.disabledTypeSubmit = true;
            const formData = new FormData();           
            formData.append('class_id', this.form.class_id);
            formData.append('type_id', this.form.type_id);
            formData.append('detail_type_id', this.form.detail_type_id);
            formData.append('ledger_code', this.form.ledger_code);
            formData.append('ledger_name', this.form.ledger_name);
            formData.append('ledger_type', this.form.ledger_type);
            formData.append('opening_balance', this.form.opening_balance);
            formData.append('balance_date', this.form.balance_date);
            formData.append('status', this.form.status);
            formData.append('company_id', this.form.company_id);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/account_ledgers/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/account_ledgers', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isTypeSubmit = false;
                this.disabledTypeSubmit = false;
                if(res.status == 200){
                    this.createAccountLedgerModal();
                    this.getAccountsLedgers();
                    this.fetchGroupData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isTypeSubmit = false; 
                this.disabledTypeSubmit = false;
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
                    axios.delete(this.apiUrl+'/account_ledgers/'+item.id, this.headerjson) 
                    .then(res => {
                        if(res.status == 200){  
                            this.getAccountsLedgers();
                            this.fetchGroupData();
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
        getAccountsLedgers(url = this.apiUrl+'/account_ledgers/list') {
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
            this.getAccountsLedgers();
        },
        setPage(data){  
            this.getAccountsLedgers(data.url); 
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
    width: 600px;
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

.actions_btn a {
    margin-right: 7px;
}
</style>