<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Account Type </a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" @click="createAccountTypeModal()">
                            <i class="mdi mdi-plus-outline"></i> Create New Type
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
                                <!-- @change="getAccountTypes($event.target.value)"  v-model="tableData.search"-->
                                <select class="form-control" v-model="tableData.company_id" @change="changeCompany($event.target.value)">
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
        <Modal @close="createAccountTypeModal()" :modalActive="modalAccTypeActive">
            <div class="modal-content scrollbar-width-thin account_groups">
                <div class="modal-header"> 
                    <h3>{{ btn }} Account Type</h3>
                    <button @click="createAccountTypeModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountTypeForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="class_id">Company *</label>
                                        <select class="form-control border " @change="fetchGroupData($event.target.value), onkeyPress('class_id')" v-model="form.company_id" id="class_id"> 
                                            <option value="">--- Select Company ---</option>
                                            <option v-for="(company, i) in companies" :key="i" :value="company.id">{{  company.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.company_id">
                                            {{errors.class_id[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="class_id">Account Group *</label>
                                        <select class="form-control border " @change="onChangeAccountGroup($event.target.value), onkeyPress('class_id')" v-model="form.class_id" id="class_id"> 
                                            <option value="">--- Select Account Group ---</option>
                                            <option v-for="(group, i) in groups" :key="i" :value="group.id">{{ '['+group.code +'] '+group.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.class_id">
                                            {{errors.class_id[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="parent_type_id">Parent Type </label>
                                        <!-- <select class="form-control border " @change="onChangeAccountType($event.target.value), onkeyPress('parent_type_id')" v-model="form.parent_type_id" id="parent_type_id"> 
                                            <option value="0">--- Select Parent Type ---</option>
                                            <option v-for="(type, i) in parent_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                        </select> -->
                                        <treeselect 
                                            v-model="form.parent_type_id"
                                            :multiple="false" 
                                            :always-open="false"
                                            :options="parent_types"
                                            :normalizer="normalizer"
                                            :value-consists-of="valueConsistsOf"
                                            :default-expand-level="Infinity"
                                            :search-nested="true"
                                            @select="onChangeAccountType($event)"                                                
                                            placeholder='--- Select Parent Type ---'
                                            v-if="renderOptionComponent"
                                        />
                                        <div class="invalid-feedback" v-if="errors.parent_type_id">
                                            {{errors.parent_type_id[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_code">Type Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_code')" v-model="form.type_code" id="type_code" placeholder="Enter Type Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.type_code">
                                            {{errors.type_code[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_name">Type Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_name')" v-model="form.type_name" id="type_name" placeholder="Enter Type Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.type_name">
                                            {{errors.type_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_status">Status *</label>
                                        <select class="form-control border " @change="onkeyPress('status')" v-model="form.status" id="type_status">
                                            <option value="1">Active</option>
                                            <option value="0">InActive</option>
                                        </select> 
                                        <div class="invalid-feedback" v-if="errors.type_status">
                                            {{errors.type_status[0]}}
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
                                                    <select class="form-select" v-model="tableData.length" @change="getAccountTypes()">
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getAccountTypes()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="account_types.length > 0">
                                    <tr class="border" v-for="(item, i) in account_types" :key="i">
                                        <td class="text-center">{{ i + 1 }}</td>
                                        <td>{{ item.type_name }}</td>
                                        <td>{{ item.type_code }}</td>
                                        <td>{{ (item.type_parents) ? item.type_parents.type_name : 'N/A' }}</td>
                                        <td>{{ (item.account_classes) ? item.account_classes.name : 'N/A' }}</td>
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
// import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
// import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        Datatable,
        Pagination
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
            showModal: false,
            editMode:false,
            disabledTypeSubmit: false,
            modalAccTypeActive:false,
            isTypeSubmit:false,
            errors: {},
            btn:'Create',
            items: [],
            account_types: [],
            groups: [],
            parent_types: [],
            companies: [],
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
                parent_type_id: null,
                type_code: '',
                type_name: '',
                status: 1,
                company_id: ''
            }), 

            columns: [       
                {
                    label: 'SL',
                    name: '',  
                    isSearch: false,  
                    isAction: true,       
                    width: '5%'
                },   
                {
                    label: 'Type Name',
                    name: 'type_name',
                    width: '15%'
                },
                {
                    label: 'Type Code',
                    name: 'type_code',
                    width: '10%'
                },
                {
                    label: 'Parent Type',
                    name: 'parent_type',
                    width: '15%'
                },
                {
                    label: 'Group/Class',
                    name: 'group_name',
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
                column: 4,
                dir: 'asc',
                sortKey: 'group_name',
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
        // this.fetchAccountType();
        // this.getAccountTypes();
        // this.fetchGroupData();
        this.fetchCompanies();
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

        createAccountTypeModal: function() {
            this.forceRerender();
            this.modalAccTypeActive = !this.modalAccTypeActive;
            // setTimeout(()=> {
            //     this.modalAccGroupActive = !this.modalAccGroupActive; 
            // }, 2000);  
            if(!this.modalAccTypeActive) {
                this.editMode = false;
                this.btn='Create';
                this.form.reset(); 
                this.parent_types = [];
            }
            this.errors = '';
            this.isTypeSubmit = false;
        },
        
        fetchAccountType() { 
            axios.get(this.apiUrl+'/account_types', this.headerjson)
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchGroupData(selectedId) {
            this.tableData.company_id = selectedId;
            axios.get(this.apiUrl+'/account_classes?company_id='+selectedId, this.headerjson)
            .then((res) => {
                this.groups = res.data.data.account_classes;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
                if (this.companies.length === 1) { 
                    // this.fetchGroupData(this.companies[0].id);
                    this.tableData.company_id = this.companies[0].id;
                    this.getAccountTypes();
                } 
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        changeCompany(selectedId){
            this.tableData.company_id = selectedId;
            this.getAccountTypes();
        },

        fetchParentTypes(group_id) {
            this.forceRerender();
            axios.post(this.apiUrl+'/account_types/getChartOfAccountsTypeOptions', {class_id: group_id}, this.headerjson)
            .then((res) => {
                this.parent_types = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchTypeCode(reference_id='', type='') {
            var formData = new FormData();
            console.log('this.tableData.company_id', this.tableData.company_id);
            formData.append("reference_id", reference_id);
            formData.append("reference_type", type);
            formData.append("company_id", this.tableData.company_id);
            axios.post(this.apiUrl+'/account_types/getTypesCode', formData, this.headers)
            .then((res) => {
                this.form.type_code = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        onChangeAccountGroup(value) {
            var group_id = value;
            if(group_id != '' || group_id != 0) {
                this.fetchParentTypes(group_id);
                this.fetchTypeCode(group_id, "group");
            }
        },
        
        onChangeAccountType(item) {
            var type_id = item.id;
            if(type_id != '' || type_id != 0) {
                this.fetchTypeCode(type_id, "type");
            }
        },

        edit: function(item) { 
            this.forceRerender();
            this.btn='Update';
            this.editMode = true;
            this.createAccountTypeModal();
            console.log(item.class_id);
            this.fetchParentTypes(item.class_id);
            this.form.fill(item);  
        },

        submitAccountTypeForm: function(e) { 

            this.isTypeSubmit = true;
            this.disabledTypeSubmit = true;
            const formData = new FormData();           
            formData.append('class_id', this.form.class_id);
            formData.append('parent_type_id', this.form.parent_type_id);
            formData.append('type_code', this.form.type_code);
            formData.append('type_name', this.form.type_name);
            formData.append('status', this.form.status);
            formData.append('company_id', this.form.company_id);
            let postEvent;
            if(this.editMode){
                formData.append('_method', 'put');
                postEvent = axios.post(this.apiUrl+'/account_types/'+this.form.id, formData, this.headers);
            }else{ 
                postEvent = axios.post(this.apiUrl+'/account_types', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isTypeSubmit = false;
                this.disabledTypeSubmit = false;
                if(res.status == 200){
                    this.createAccountTypeModal();
                    this.getAccountTypes();
                    this.fetchGroupData(this.form.company_id);
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
                    axios.delete(this.apiUrl+'/account_types/'+item.id, this.headerjson) 
                    .then(res => {
                        if(res.status == 200){  
                            this.getAccountTypes();
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
        getAccountTypes(url = this.apiUrl+'/account_types/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw == data.draw) {
                    this.account_types = data.data.data;
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
            this.getAccountTypes();
        },
        setPage(data){  
            this.getAccountTypes(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },

    }, 
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