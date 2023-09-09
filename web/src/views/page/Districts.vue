<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Districts </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">District List</a></li>
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()" v-if="permission['districts-create']">
                            Add New
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3>District Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group  ">
                                    <label for="name">District Name *</label>
                                    <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="District name" autocomplete="off"> 
                                    <div class="invalid-feedback" v-if="errors.name">
                                        {{errors.name[0]}}
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="name">Bangla Name *</label>
                                    <input type="text" class="form-control border " @keypress="onkeyPress('bn_name')" v-model="form.bn_name" id="name" placeholder="District Bangla Name" autocomplete="off"> 
                                    <div class="invalid-feedback" v-if="errors.bn_name">
                                        {{errors.bn_name[0]}}
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="name">Division *</label>
                                    <select class="form-control border" v-model="form.division_id" @change="onkeyPress('division_id')" id="division_id">
                                        <option value="0">Select Division</option>
                                        <option v-for="(division, index) in divisions" :value="division.id" :key="index">{{ division.name }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.division_id">
                                        {{errors.division_id[0]}}
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
                                                    <select class="form-select" v-model="tableData.length" @change="getDistricts()">  
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getDistricts()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >                            
                                <tbody v-if="districts.length > 0">
                                    <tr class=" " v-for="(item, i) in districts" :key="i">
                                        <td>{{ item.name}} </td>
                                        <td>{{ item.bn_name }} </td>
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['districts-edit']">
                                                    <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['districts-delete']"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
import Buttons from '@/components/Buttons.vue';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted, getCurrentInstance } from "vue";
import Form from "vform";
import axios from "axios";

export default {

    name: "Districts",
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
            divisions: [],
            form: new Form({
                id: '',
                name: '',
                bn_name: '',
                division_id: 0,
                status: 1,
            }),
            districts: [],
            columns: [ 
                {
                    label: 'Name',
                    name: 'name',           
                    width: '20%'
                },   
                {
                    label: 'Bangla Name',
                    name: 'bn_name',
                    width: '20%'
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
        // this.fetchDistrictsData();
        this.getDistricts();
        this.fetchDivisionData();
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

        fetchDistrictsData() { 
            axios.get(this.apiUrl+'/districts', this.headerjson)
            .then((res) => { 
                this.items = res.data.data.data;
                this.data = data.data; 
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message); 
            })
            .finally((fres) => {
                this.loading = false;
            }); 
        },

        fetchDivisionData() { 
            axios.get(this.apiUrl+'/divisions', this.headerjson)
            .then((res) => {
                this.divisions = res.data.data;
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
            formData.append('name', this.form.name);
            formData.append('bn_name', this.form.bn_name);
            formData.append('division_id', this.form.division_id);
            formData.append('status', this.form.status); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/districts/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/districts', formData, this.headers);
            }           

            postEvent.then(res => {                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getDistricts(this.apiUrl+'/districts/list?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/districts/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getDistricts(this.apiUrl+'/districts/list?page='+this.pagination.currentPage);
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
                } 
            }); 
        },

        // For Pagination 
        getDistricts(url = this.apiUrl+'/districts/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.districts = data.data.data;
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
            this.getDistricts();
        },
        setPage(data){  
            this.getDistricts(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
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