<template>
    <transition>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Cost Center </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Cost Center List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add Cost Center
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
                                <select class="form-control" v-model="tableData.company_id" @change="getCostCenters()">
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
                                <div class="form-group">
                                    <label for="company_id">Company *</label><br>
                                    <Multiselect 
                                        class="form-control border company_id" 
                                        mode="single"
                                        v-model="form.company_id"
                                        placeholder="Company Select"  
                                        :searchable="true" 
                                        :filter-results="true"
                                        :options="company_options"
                                        :classes="multiclasses"
                                        :close-on-select="true" 
                                        :min-chars="1"
                                        :resolve-on-load="false" 
                                        @change="onChangeCompany"
                                    /> 
                                    <div class="invalid-feedback" v-if="errors.company_id">
                                        {{errors.company_id[0]}}
                                    </div>
                                </div>

                                
                                <div class="form-group col-md-12">
                                    <label for="outlet_id">Outlet </label><br>
                                    <Multiselect 
                                        class="form-control border outlet_id" 
                                        mode="single"
                                        v-model="form.outlet_id"
                                        placeholder="Outlet Select"  
                                        :searchable="true" 
                                        :filter-results="true"
                                        :options="outlet_options"
                                        :classes="multiclasses"
                                        :close-on-select="true" 
                                        :min-chars="1"
                                        :resolve-on-load="false" 
                                    /> 
                                    <div class="invalid-feedback" v-if="errors.outlet_id">
                                        {{errors.outlet_id[0]}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="center_name">Cost Center Name *</label>
                                    <input type="text" class="form-control border " @keypress="onkeyPress('center_name')" v-model="form.center_name" id="center_name" placeholder="Cost Center Name" autocomplete="off"> 
                                    <div class="invalid-feedback" v-if="errors.center_name">
                                        {{errors.center_name[0]}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Note</label>
                                    <textarea class="form-control border" rows="3" @keypress="onkeyPress('note')" v-model="form.note" id="note" placeholder="Note details here!" autocomplete="off"></textarea> 
                                    <div class="invalid-feedback" v-if="errors.note">
                                        {{errors.note[0]}}
                                    </div>
                                </div>
                                    
                                <div class="form-group">
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
                                                    <select class="form-select" v-model="tableData.length" @change="getCostCenters()"> 
                                                        <option value="2" selected="selected">2</option>
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getCostCenters()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ i + 1 }} </td>
                                        <td>{{ item.center_name}} </td>
                                        <td>{{ item.companies.name }} </td>
                                        <td>{{ (item.outlets) ? item.outlets.name : 'N/A' }} </td>
                                        <td>{{ item.note }} </td>
                                        <td v-if="item.status == 1"><label class="badge bg-success">Active</label></td>
                                        <td v-else><label class="badge bg-danger">InActive</label></td>
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
// import {mapGetters, mapActions} from "vuex";
import Modal from "../helper/Modal.vue";
// import Buttons from '@/components/Buttons.vue';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
// import { ref, onMounted, getCurrentInstance } from "vue";
import Form from "vform";
import axios from "axios";

export default {

    name: "CostCenter",
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
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            companies: [],
            company_options: [],
            outlets: [],
            outlet_options: [{label: "Select Outlet", value: ""}],
            form: new Form({
                id: '',
                company_id: '',
                outlet_id: '',
                center_name: '',
                note: '',
                status: 1,
            }),
            items: [],
            columns: [       
                {
                    label: 'SL',
                    name: '',
                    isSearch: false,
                    isAction: true,           
                    width: '5%'
                },   
                {
                    label: 'Cost Center',
                    name: 'center_name',           
                    width: '15%'
                },   
                {
                    label: 'Company',
                    name: 'companies.name',
                    width: '15%'
                },
                {
                    label: 'Outlet',
                    name: 'outlets.name',
                    width: '15%'
                },
                {
                    label: 'Note',
                    name: 'note',
                    width: '15%'
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
                column: 0,
                dir: 'asc',
                sortKey: 'center_name',
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
        this.getCostCenters();
        this.fetchCompany();
        this.fetchOutlets();
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
                if(this.companies.length ==1){
                    this.tableData.company_id = this.companies[0].id
                }
                
                this.company_options = [{label: "Select Company", value: ""}];
                res.data.data.map((item) => {
                    this.company_options.push({label: item.name, value: item.id});
                });
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchOutlets() { 
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => {
                this.outlets = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }); 
        },

        onChangeCompany(value) {
            var company_id = value;
            if(company_id != '') {
                var outlets = this.outlets.filter((item) => {
                    if(item.company_id == company_id) {
                        return item;
                    }
                });

                outlets.map((item) => {
                    this.outlet_options.push({label: item.name, value: item.id})
                });

            }else {
                this.form.outlet_id = '';
                this.outlet_options = [{label: "Select Outlet", value: ""}];
            }
        },

        edit: function(item) {
            this.btn='Update';
            this.editMode = true;

            var outlets = this.outlets.filter((outlet) => {
                if(outlet.company_id == item.company_id) {
                    return outlet;
                }
            })

            outlets.map((item) => {
                this.outlet_options.push({label: item.name, value: item.id});
            })

            this.toggleModal();
            this.form.fill(item); 
        },
        submitForm: function(e) { 
            
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('company_id', this.form.company_id);
            formData.append('outlet_id', this.form.outlet_id);
            formData.append('center_name', this.form.center_name);
            formData.append('note', this.form.note);
            formData.append('status', this.form.status); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/cost_centers/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/cost_centers', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getCostCenters(this.apiUrl+'/cost_centers/list?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/cost_centers/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getCostCenters(this.apiUrl+'/cost_centers/list?page='+this.pagination.currentPage);
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
        getCostCenters(url = this.apiUrl+'/cost_centers/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw = data.draw) {
                    this.items = data.data.data;
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
            this.getCostCenters();
        },
        setPage(data){  
            this.getCostCenters(data.url); 
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
</style>