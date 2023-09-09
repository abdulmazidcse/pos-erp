<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Permission </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Action List</a></li>
                            
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

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_route_action" name="route_action" value="route_action" v-model="permission_obj.is_route_action" :checked="permission_obj.is_route_action" @change="routeAndAction($event)">
                                            <label class="form-check-label" for="is_route_action">Route and Action</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_action" name="route_action" value="action" v-model="permission_obj.is_route_action" :checked="!permission_obj.is_route_action" @change="routeAndAction($event)">
                                            <label class="form-check-label" for="is_action">Only Action</label>
                                        </div>
                                    </div>

                                    <div class="invalid-feedback" v-if="errors.is_route_action">
                                        {{ errors.is_route_action[0] }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="module_id">Module/Menu *</label>
                                        <select class="form-control border " @keypress="onkeyPress('module_id')" v-model="permission_obj.module_id" id="module_id">
                                            <option value="0">--- Select Module/Menu ---</option>
                                            <option v-for="(modules, i) in permission_modules" :key="i" :value="modules.id">{{ modules.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.module_id">
                                            {{errors.module_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="name">Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="permission_obj.name" id="name" placeholder="Permission Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div>

                                    <div v-if="permission_obj.is_route_action">
                                        <div class="form-group col-md-12">
                                            <label for="url_path">Url Path *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('url_path')" v-model="permission_obj.url_path" id="url_path" placeholder="Url Path" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.url_path">
                                                {{errors.url_path[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="component_path">Component Path *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('component_path')" v-model="permission_obj.component_path" id="component_path" placeholder="Component Path" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.component_path">
                                                {{errors.component_path[0]}}
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-6" style="margin-top: 10px;">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_nav" v-model="permission_obj.is_nav" true-value="1" false-value="0" @change="onkeyPress('is_nav')">
                                                <label class="form-check-label" for="is_nav">Is Nav</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_nav">
                                                {{errors.is_nav[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="is_index" v-model="form.is_index" true-value="1" false-value="0" @change="onkeyPress('is_index')">
                                            <label class="form-check-label" for="is_index">Is Index/List</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_index">
                                            {{errors.is_index[0]}}
                                        </div>
                                    </div> -->

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
                                                  <select class="form-select" v-model="tableData.length" @change="fetchItems()">  
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
                                          <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchItems()">
                                      </div>
                                  </div>
                              </div>   
                          </template> 
                          <template #body >   
                              <tbody v-if="permissions.length > 0">
                                  <tr class="border" v-for="(item, i) in permissions" :key="i">
                                    <td>{{ i + 1 }} </td>
                                    <td>{{ item.name }} </td>
                                    <td>{{ item.slug }} </td>
                                    <td>{{ item.url_path }} </td>
                                    <td>{{ item.component_path }} </td>
                                    <!-- <td>{{ item.module_id }} </td> -->
                                    <td>{{ item.permission_modules.name }} </td>
                                    <td>{{ (item.is_nav == 1) ? "Yes" : "No" }} </td> 
                                    <td>
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end"> 
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
            permissions: [],
            permission_modules: [],
            permission_obj: new Form({
                id: '',
                name: '',
                url_path: '',
                component_path: '',
                module_id: 0,
                is_route_action: 1,
                is_nav: 0,
            }),
            columns: [       
                {
                    label: 'SL',
                    name: '',           
                    width: '5%',
                    isSearch: false, 
                    isAction: true,
                },   
                {
                    label: 'Name',
                    name: 'name',           
                    width: '10%'
                },   
                {
                    label: 'Slug',
                    name: 'slug',
                    width: '10%'
                },
                {
                    label: 'Url Path',
                    name: 'url_path',
                    width: '15%'
                },
                {
                    label: 'Component Path',
                    name: 'component_path',
                    width: '15%'
                },
                // {
                //     label: 'Module ID',
                //     name: 'module_id',
                //     width: '5%'
                // },
                {
                    label: 'Module/Menu',
                    name: 'permission_modules.name',
                    width: '10%'
                },
                {
                    label: 'Is Nav',            
                    name: 'is_nav',
                    width: '5%'
                },
                {
                    label: 'Actions',            
                    name: '',
                    width: '5%',
                    isSearch: false, 
                    isAction: true,
                }
            ],  
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc',
                sortKey: 'module_id', 
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
            isLoading:true,
            
        }
    },
    created() {
        // this.fetchDistrictsData();
        this.fetchItems();
        this.fetchPermissionModules();
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
            this.permission_obj.reset();
        },

        fetchPermissionModules() { 
            axios.get(this.apiUrl+'/permission_modules/getParentsWithoutHasChildren', this.headerjson)
            .then((res) => {
                this.permission_modules = res.data.data;
            }).catch((err) => {
                console.log(err.response.data);
            });
        },

        routeAndAction: function(event) {
            let action_value = event.target.value;
            if(action_value == "route_action") {
                this.permission_obj.is_route_action = 1;
            }
            else if(action_value == "action"){
                this.permission_obj.is_route_action = 0;
            }else{
                this.permission_obj.is_route_action = 1;
            }
        },

        edit: function(item) {
            this.btn='Update';
            this.editMode = true;
            this.toggleModal();
            this.permission_obj.fill(item); 
        },
        submitForm: function(e) { 
            
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('is_route_action', this.permission_obj.is_route_action);            
            formData.append('module_id', this.permission_obj.module_id);            
            formData.append('name', this.permission_obj.name);
            formData.append('url_path', this.permission_obj.url_path);
            formData.append('component_path', this.permission_obj.component_path);
            formData.append('is_nav', this.permission_obj.is_nav);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/permissions/'+this.permission_obj.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/permissions', formData, this.headers);
            }           

            postEvent.then((res) => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchPermissions(this.apiUrl+'/permissions/list?page='+this.pagination.currentPage);
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
                    axios.delete(this.apiUrl+'/permissions/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.fetchPermissions(this.apiUrl+'/permissions/list?page='+this.pagination.currentPage);
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
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
        fetchItems(url = this.apiUrl+'/permissions/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;  
                if(this.tableData.draw = data.draw) { 
                    this.permissions = data.data.data;
                    this.configPagination(data.data);
                }
            })
            .catch(errors => {
                console.log(errors);
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
            this.fetchItems();
        },
        setPage(data){  
            this.fetchItems(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        }


        // fetchPermissions(url = this.apiUrl+'/permissions/list') {
        //     this.tableData.draw++;
        //     axios.get(url, {params:this.tableData}, this.headerjson)
        //     .then((response) => {
        //         let data = response.data.data;
        //         console.log("permissions==", data);
        //         if(this.tableData.draw = data.draw) {
        //             this.permissions = data.data.data;
        //             this.configPagination(data.data);
        //         }
        //     })
        //     .catch(errors => {
        //         console.log(errors);
        //     })
        //     .finally((fres) => {
        //         this.loading = false;
        //     });
        // },

        // configPagination(data){
        //     this.pagination.lastPage = data.last_page;
        //     this.pagination.currentPage = data.current_page;
        //     this.pagination.total   = data.total;
        //     this.pagination.lastPageUrl = data.last_page_url;
        //     this.pagination.nextPageUrl = data.next_page_url;
        //     this.pagination.prevPageUrl = data.prev_page_url;
        //     this.pagination.from = data.from;
        //     this.pagination.to = data.to;

        // },

        // sortBy(key) {
        //     this.sortKey = key;
        //     this.sortOrders[key] = this.sortOrders[key] * -1;
        //     this.tableData.column = this.getIndex(this.columns, 'name', key);
        //     this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
        //     this.fetchPermissions();
        // },
        // getIndex(array, key, value) {
        //     return array.findIndex(i => i[key] == value)
        // },
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