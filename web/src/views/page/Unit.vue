<template>
    <transition  >
    <div class="container-fluid  ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Unit </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Unit List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal()" v-if="permission['unit-create']">
                            <i class="mdi mdi-camera-timer me-1"></i> Create New
                        </button> 
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3>Unit Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="unit_code">Unit Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('unit_code')" v-model="form.unit_code" id="unit_code" placeholder="Unit Code" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.unit_code">
                                            {{errors.unit_code[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="unit_name">Unit Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('unit_name')" v-model="form.unit_name" id="unit_name" placeholder="Unit Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.unit_name">
                                            {{errors.unit_name[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="base_unit">Base Unit </label>
                                        <select class="form-control border" v-model="form.base_unit" @change="onkeyPress('base_unit')" id="base_unit">
                                            <option value="0">Select Base Unit</option>
                                            <option v-for="(unit, index) in items" :value="unit.id" :key="index">{{ unit.unit_name }} ({{ unit.unit_code }})</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.base_unit">
                                            {{errors.base_unit[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="operator">Operator </label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('operator')" v-model="form.operator" id="operator" placeholder="Operator Value" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.operator">
                                            {{errors.operator[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="operation_value">Operation Value </label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('operation_value')" v-model="form.operation_value" id="operation_value" placeholder="operation_value Value" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.operation_value">
                                            {{errors.operation_value[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
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
                                                    <select class="form-select" v-model="tableData.length" @change="getDataItems()">  
                                                        <option value="10" selected="selected">10</option> 
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option :value="pagination.total">All</option>
                                                    </select>
                                                </div>
                                                <span style="float: left; margin-left: 10px; padding: 7px 0px;"> Entries</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getDataItems()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >                            
                                <tbody v-if="items.length > 0">
                                    <tr class=" " v-for="(item, i) in items" :key="i">                                            
                                        <td>{{ item.unit_code}} </td> 
                                        <td>{{ item.unit_name }} </td>
                                        <td>{{ item.base_unit }} </td>
                                        <td>{{ item.operator }} </td>
                                        <td>{{ item.operation_value }} </td>
                                        <td> <span v-if="item.status==1" class="badge bg-success">Active</span>
                                             <span v-if="item.status==0" class="badge bg-warning">In-Active</span> </td>
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end"> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['unit-edit']">
                                                    <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['unit-delete']" ><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
import Modal from "./../helper/Modal.vue";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted } from "vue";
import Form from "vform";
import axios from "axios";
export default {
    name: "Unit",
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
            items: [],
            form: new Form({
                id: '',
                unit_code: '',
                unit_name: '',
                base_unit: 0,
                operator: '*',
                operation_value: 1,
                status: 1,
            }), 
            columns: [ 
                {
                    label: 'Unit Code',
                    name: 'unit_code',           
                    width: '15%'
                }, 
                {
                    label: 'Unit Name',
                    name: 'unit_name',           
                    width: '15%'
                }, 
                {
                    label: 'Base Unit',
                    name: 'base_unit',
                    width: '20%'
                },
                {
                    label: 'Operator',
                    name: 'operator',
                    width: '15%'
                },
                {
                    label: 'Operation Value',
                    name: 'operation_value',
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
                    width: '5%',

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
        this.getDataItems();
    },

    methods: {
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            if(!this.modalActive){
                this.editMode = false;
                this.btn='Create';
            } 
            this.errors = '';
            this.isSubmit = false;
            this.form.reset(); 
        },
        fetchUnitData() { 
            axios.get(this.apiUrl+'/units', this.headers)
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
            .finally((fres) => {
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
            formData.append('unit_code', this.form.unit_code);
            formData.append('unit_name', this.form.unit_name);
            formData.append('base_unit', this.form.base_unit);
            formData.append('operator', this.form.operator);
            formData.append('operation_value', this.form.operation_value);
            formData.append('status', this.form.status); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/units/'+this.form.id, formData, this.headers);
            }else{
                var postEvent = axios.post(this.apiUrl+'/units', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getDataItems(this.apiUrl+'/units?page='+this.pagination.currentPage);
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
            console.log('item delete=>',item.id);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/units/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getDataItems(this.apiUrl+'/units?page='+this.pagination.currentPage);
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
        getDataItems(url = this.apiUrl+'/units') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                console.log('response', response.data.data);
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
            this.getDataItems();
        },
        setPage(data){  
            this.getDataItems(data.url); 
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
    width: 800px;
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