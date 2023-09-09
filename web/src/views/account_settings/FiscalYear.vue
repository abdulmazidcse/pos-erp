<template>
    <transition>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Fiscal Year </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Fiscal Year List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add Fiscal Year
                        </button> 
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
                                    <div class="form-group col-md-12">
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
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.company_id">
                                            {{errors.company_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="label">Label *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('label')" v-model="form.label" id="label" placeholder="Entry Type Label" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.label">
                                            {{errors.label[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="satrt_date">Fiscal Year Start *</label>
                                        <input type="date" class="form-control border " @keypress="onkeyPress('start_date')" v-model="form.start_date" id="start_date" placeholder="" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.start_date">
                                            {{errors.start_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="end_date">Fisacal Year End *</label>
                                        <input type="date" class="form-control border " @keypress="onkeyPress('end_date')" v-model="form.end_date" id="end_date" placeholder="" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.end_date">
                                            {{errors.end_date[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="ystatus">Status *</label>
                                        <select class="form-control border" @keypress="onkeyPress('status')" v-model="form.status" id="ystatus">
                                            <option value=""> Select.... </option>
                                            <option value="1">Active</option>
                                            <option value="0">InActive</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.status">
                                            {{errors.status[0]}}
                                        </div>
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
                                                    <select class="form-select" v-model="tableData.length" @change="getFiscalYear()"> 
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getFiscalYear()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.label }} </td>
                                        <td>{{ item.start_date+' to '+ item.end_date }} </td>
                                        <td>{{ item.companies.name }} </td>
                                        <td v-if="item.status == 1">
                                            <input type="checkbox" :id="'switch'+ (i + 1)" checked data-switch="bool" @change="fyActiveInactive(item)" />
                                            <label :for="'switch'+ (i + 1)" data-on-label="On" data-off-label="Off"></label>
                                        </td>
                                        <td v-else>
                                            <input type="checkbox" :id="'switch'+ (i + 1)" data-switch="bool" @change="fyActiveInactive(item)" />
                                            <label :for="'switch'+ (i + 1)" data-on-label="On" data-off-label="Off"></label>
                                        </td>
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
import {mapGetters, mapActions} from "vuex";
import Modal from "../helper/Modal.vue";
import Buttons from '@/components/Buttons.vue';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted, getCurrentInstance } from "vue";
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
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            form: new Form({
                id: '',
                company_id: '',
                label: '',
                start_date: '',
                end_date: '',
                status: '',
            }),
            companies: [],
            company_options: [],
            items: [],
            columns: [       
                {
                    label: 'Label',
                    name: 'label',           
                    width: '20%'
                },   
                {
                    label: 'Fiscal Year',
                    name: 'start_date',
                    width: '30%'
                }, 
                {
                    label: 'Company',
                    name: 'companies.name',
                    width: '30%'
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
                dir: 'desc',
                sortKey: 'label',
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
            },
            item_status: '',
            
        }
    },
    created() {
        this.getFiscalYear();
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
            .then((resp) => {
                this.companies = resp.data.data;
                this.company_options = [{label: "Select Company", value: ""}];
                resp.data.data.map((item) => {
                    this.company_options.push({label:item.name, value:item.id});
                });
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
            const formData = new FormData();  
            formData.append('company_id', this.form.company_id);
            formData.append('label', this.form.label);
            formData.append('start_date', this.form.start_date);
            formData.append('end_date', this.form.end_date);
            formData.append('status', this.form.status);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/fiscal_years/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/fiscal_years', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getFiscalYear(this.apiUrl+'/fiscal_years/list?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/fiscal_years/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getFiscalYear(this.apiUrl+'/fiscal_years/list?page='+this.pagination.currentPage);
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

        fyActiveInactive(item) {
            var item_id = item.id;
            var item_status = (item.status == 1) ? 0 : 1;
            var data = {item_id:item_id, status:item_status};

            this.$swal({
                title: 'Are you sure?',
                text: "You want to update this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => { 
                if (result.value) { 
                        axios.post(this.apiUrl+'/fiscal_years/status_update', data, this.headerjson)
                        .then((resp) => {
                            if(resp.status == 200) {
                                this.getFiscalYear(this.apiUrl+'/fiscal_years/list?page='+this.pagination.currentPage);
                                this.$toast.success(resp.data.message);
                            }else{
                                this.$toast.error(res.data.message);
                            }
                        })
                        .catch((err) => {
                            this.$toast.error(err.response.data.message);
                        });
                }else{
                    return false;
                }
            });
        },

        // For Pagination 
        getFiscalYear(url = this.apiUrl+'/fiscal_years/list') {
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
            this.getFiscalYear();
        },
        setPage(data){  
            this.getFiscalYear(data.url); 
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