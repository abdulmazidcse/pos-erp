<template>
    <transition>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Entry Types </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Entry Type List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add Entry Type
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
                                <select class="form-control" v-model="tableData.company_id" @change="getEntryTypes()">
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
                                        <label for="label">Label *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('label')" v-model="form.label" id="label" placeholder="Entry Type Label" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.label">
                                            {{errors.label[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="name">Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Entry Type Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('description')" v-model="form.description" id="description" placeholder="Description Here" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.description">
                                            {{errors.description[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="numbering">Numbering *</label><br>
                                        <Multiselect 
                                            class="form-control border numbering" 
                                            mode="single"
                                            v-model="form.numbering"
                                            placeholder="Numbering Select"  
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="[{label: 'Auto', value: 1},{label: 'Manual(required)', value: 2},{label: 'Manual(optional)', value: 3}]"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.numbering">
                                            {{errors.numbering[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="prefix">Prefix</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('prefix')" v-model="form.prefix" id="prefix" placeholder="Prefix" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.prefix">
                                            {{errors.prefix[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="suffix">Suffix</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('suffix')" v-model="form.suffix" id="suffix" placeholder="Suffix" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.suffix">
                                            {{errors.suffix[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="zero_padding">Zero Padding</label>
                                        <input type="number" step="any" pattern="[0-9]" class="form-control border" @keypress="onkeyPress('zero_padding')" v-model="form.zero_padding" id="zero_padding" placeholder="Zero Padding"> 
                                        <div class="invalid-feedback" v-if="errors.zero_padding">
                                            {{errors.zero_padding[0]}}
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label for="restrictions">Restrictions</label><br>
                                        <Multiselect 
                                            class="form-control border restrictions" 
                                            mode="single"
                                            v-model="form.restrictions"
                                            placeholder="Restriction Select"  
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="retrict_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                        /> 
                                        <div class="invalid-feedback" v-if="errors.restrictions">
                                            {{errors.restrictions[0]}}
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
                                                    <select class="form-select" v-model="tableData.length" @change="getEntryTypes()"> 
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getBankAccounts()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body > 
                                <tbody v-if="entry_types.length > 0">
                                    <tr class="border" v-for="(item, i) in entry_types" :key="i">
                                        <!-- <td>{{ item.label }} </td> -->
                                        <td>{{ item.name }} </td>
                                        <td>{{ item.description }} </td>
                                        <td>{{ item.number_string }} </td>
                                        <td>{{ item.prefix }}</td>
                                        <!-- <td>{{ item.suffix }} </td> -->
                                        <!-- <td>{{ item.zero_padding }} </td> -->
                                        <td>{{ item.restriction_string }} </td>
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

    name: "TransactionEntryTypes",
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
            companies:[],
            retrict_options: [
                
                {
                    value: 1,
                    label: 'Unrestricted',
                }, 
                {
                    value: 2,
                    label: 'Atleast one Bank or Cash account must be present on Debit side',
                }, 
                {
                    value: 3,
                    label: 'Atleast one Bank or Cash account must be present on Credit side',
                }, 
                {
                    value: 4,
                    label: 'Only Bank or Cash account can be present on both Debit and Credit side',
                }, 
                {
                    value: 5,
                    label: 'Only NON Bank or Cash account can be present on both Debit and Credit side',
                }
            ],
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            form: new Form({
                id: '',
                label: '',
                name: '',
                description: '',
                numbering: 1,
                prefix: '',
                suffix: '',
                zero_padding: '',
                restrictions: 1,
            }),
            entry_types: [],
            columns: [       
                // {
                //     label: 'Label',
                //     name: 'label',           
                //     width: '10%'
                // },   
                {
                    label: 'Name',
                    name: 'name',
                    width: '10%'
                },
                {
                    label: 'Description',
                    name: 'description',
                    width: '15%'
                },
                {
                    label: 'Numbering',
                    name: 'numbering',
                    width: '10%'
                },
                {
                    label: 'Prefix',
                    name: 'prefix',
                    width: '10%'
                },
                // {
                //     label: 'Suffix',
                //     name: 'suffix',
                //     width: '10%'
                // },
                // {
                //     label: 'Zero Padding',
                //     name: 'zero_padding',
                //     width: '10%'
                // },
                {
                    label: 'Restriction',
                    name: 'restriction',
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
                sortKey: 'name',
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
        this.fetchCompanies();
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

        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data; 
                if(this.companies.length ==1){
                    this.tableData.company_id = this.companies[0].id
                }
                this.getEntryTypes(); 
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
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
            formData.append('label', this.form.label);
            formData.append('name', this.form.name);
            if(this.form.description != '' && this.form.description != null) {
                formData.append('description', this.form.description);
            }
            formData.append('numbering', this.form.numbering);
            if(this.form.prefix != '' && this.form.prefix != null) {
                formData.append('prefix', this.form.prefix);
            }
            if(this.form.suffix != '' && this.form.suffix != null) {
                formData.append('suffix', this.form.suffix);
            }
            formData.append('zero_padding', this.form.zero_padding); 
            formData.append('restrictions', this.form.restrictions); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/entry_types/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/entry_types', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getEntryTypes(this.apiUrl+'/entry_types/list?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/entry_types/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getEntryTypes(this.apiUrl+'/entry_types/list?page='+this.pagination.currentPage);
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
        getEntryTypes(url = this.apiUrl+'/entry_types/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw = data.draw) {
                    this.entry_types = data.data.data;
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
            this.getEntryTypes();
        },
        setPage(data){  
            this.getEntryTypes(data.url); 
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