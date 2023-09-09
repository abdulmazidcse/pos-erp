<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Mobile Wallets  </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Mobile Wallet List</a></li>
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal" v-if="permission['mobile-create']">
                            <i class="mdi mdi-camera-timer me-1"></i> Create New
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
                                                    <select class="form-select" v-model="tableData.length" @change="fetchData()">  
                                                        <option value="10" selected="selected">10</option> 
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="500">500</option>
                                                    </select>
                                                </div>
                                                <span style="float: left; margin-left: 10px; padding: 7px 0px;"> Entries</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchData()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >                            
                                <tbody v-if="items.length > 0">
                                    <tr class=" " v-for="(item, i) in items" :key="i">                                            
                                        <td>{{ item.mobile_wallet}} </td>
                                        <td>{{ item.agent_name}} </td> 
                                        <td>{{ item.mobile_number}} </td> 
                                        <td>{{ item.charge_percent}} </td> 
                                        <td> <span v-if="item.status==1" class="badge bg-success">Active</span>
                                            <span v-if="item.status==0" class="badge bg-warning">In-Active</span> </td>
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end"> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['mobile-wallet-edit']">
                                                    <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['mobile-wallet-delete']" ><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
                    <h3>Mobile Wallets Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <div class="mb-3">
                                        <label for="company_id">Company *</label> 
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
                                <div class="form-group  ">
                                    <div class="mb-3">
                                        <label for="mobile_wallet">Mobile wallet type *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('mobile_wallet')" v-model="form.mobile_wallet" id="mobile_wallet" placeholder="Mobile wallet type" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.mobile_wallet">
                                            {{errors.mobile_wallet[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group  ">
                                    <div class="mb-3">
                                        <label for="agent_name">Agent name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('agent_name')" v-model="form.agent_name" id="agent_name" placeholder="Agent name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.agent_name">
                                            {{errors.agent_name[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group  ">
                                    <div class="mb-3">
                                        <label for="mobile_number">Mobile number *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('mobile_number')" v-model="form.mobile_number" id="mobile_number" placeholder="Mobile number" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.mobile_number">
                                            {{errors.mobile_number[0]}}
                                        </div>
                                    </div>
                                </div>  
                                <div class="form-group  ">
                                    <div class="mb-3">
                                        <label for="charge_percent">Charge Percent *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('charge_percent')" v-model="form.charge_percent" id="charge_percent" placeholder="Charge Percent" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.charge_percent">
                                            {{errors.charge_percent[0]}}
                                        </div>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label for="ustatus">Status</label>
                                    <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="ustatus">
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
    </div>
    </transition>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import Modal from "../helper/Modal.vue";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import Form from "vform";
import axios from "axios";
export default {
    name: "MobileWallets",
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
            modalActive:false,
            errors: {},
            btn:'Create',
            items: [],
            companies: [],
            loading: true,
            form: new Form({
                id: '',
                mobile_wallet: '',
                agent_name: '',
                mobile_number:'',
                charge_percent:'',
                company_id:'',
                status: 1,
            }), 
            columns: [ 
                {
                    label: 'Mobile wallet type ',
                    name: 'name',           
                    width: '25%'
                }, 
                {
                    label: 'Agent name',
                    name: 'value',           
                    width: '20%'
                }, 
                {
                    label: 'Mobile number',
                    name: 'value',           
                    width: '20%'
                }, 
                {
                    label: 'Charge %',
                    name: 'value',           
                    width: '15%'
                }, 
                {
                    label: 'Status',
                    name: 'status',
                    isSearch: false, 
                    width: '10%'
                },
                {
                    label: 'Actions',            
                    name: '',
                    isSearch: false, 
                    isAction: true,
                    width: '10%',

                }
            ],  
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc',
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
        }
    },
    created() {
        this.fetchData();
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
            axios.get(this.apiUrl+'/companies',this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message); 
            });
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
            if(this.editMode){ 
                var postEvent = axios.put(this.apiUrl+'/mobile_wallets/'+this.form.id, this.form, this.headerjson );
            }else{  
                var postEvent = axios.post(this.apiUrl+'/mobile_wallets', this.form,  this.headerjson);
            }
            postEvent.then(res => { 
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchData(this.apiUrl+'/mobile_wallets?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/mobile_wallets/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.fetchData(this.apiUrl+'/mobile_wallets?page='+this.pagination.currentPage);
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
        fetchData(url = this.apiUrl+'/mobile_wallets') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => { 
                this.items = response.data.data;   
                this.configPagination(response.data.meta); 
            })
            .catch(errors => {
                this.$toast.error(errors);
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
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = sortable;  
            this.fetchData();
        },
        setPage(data){  
            this.fetchData(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        }
          
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        permission() {
            let pname = this.$route.meta.parent_module;
            let module_name = this.$route.meta.module_name;
            let path_name = this.$route.path; 
            let data = '';
            if(this.$route.meta.parent_module){
                data = this.$store.getters.userAllPermissions[pname][0].children[path_name]
            }else{
                data = this.$store.getters.userAllPermissions[module_name][0].other_actions; 
            } 
            return data;
        },

    }
}
</script>

<style scoped>
.modal-content.scrollbar-width-thin {
    border: none !important;
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
</style>