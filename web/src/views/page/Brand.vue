<template>
    <transition>
        <div class="container-fluid card-body   ">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Brand </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Brand List</a></li>

                            </ol>
                        </div>
                        <div class="page-title-right float-right ">
                            <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal()" v-if="permission['brand-create']">
                                <i class="mdi mdi-camera-timer me-1"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
            </div>

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
                                            <td>{{ item.name}} </td>
                                            <td> <img :src="item.logo" width="50" v-if="item.logo"> </td>
                                            <td>{{ item.description }} </td>
                                            <td>{{ item.website }} </td>
                                            <td>{{ item.company_name }} </td>
                                            <td> <span v-if="item.status==1" class="badge bg-success">Active</span>
                                                 <span v-if="item.status==0" class="badge bg-warning">In-Active</span> </td>
                                            <td>
                                                <div class="dropdown float-end">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end"> 
                                                        <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['brand-edit']">
                                                        <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> 
                                                        <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['brand-delete']" ><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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

                <Modal @close="toggleModal()" :modalActive="modalActive">
                    <div class="modal-content scrollbar-width-thin">
                        <div class="modal-header">
                            <h3>Brand Add Or Edit</h3>
                            <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                        </div>
                        <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="name">Brand name *</label>
                                                <input type="text" class="form-control border "
                                                    @keypress="onkeyPress('name')" v-model="form.name" id="name"
                                                    placeholder="Brand name" autocomplete="off">
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.name[0]}}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="contact_person_name">Company </label>
                                                <select class="form-control border" v-model="form.company_id"
                                                    @change="onkeyPress('company_id')" id="company_id">
                                                    <option value="0">Select Company</option>
                                                    <option v-for="(company, index) in companies" :value="company.id"
                                                        :key="index">{{ company.name }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-if="errors.company_id">
                                                    {{errors.company_id[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description </label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('description')" v-model="form.description"
                                                id="description" placeholder="Description Here!">
                                            <div class="invalid-feedback" v-if="errors.description">
                                                {{errors.description[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Website </label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('website')" v-model="form.website" id="website"
                                                placeholder="Website Link Here">
                                            <div class="invalid-feedback" v-if="errors.website">
                                                {{errors.website[0]}}
                                            </div>
                                        </div>
                                        

                                        <div class="row">
                                            <div class="form-group col-md-12"
                                                style="text-align:left; margin-top: 10px;">

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" style="margin-top: 0px"
                                                        type="checkbox" v-model="form.is_featured" id="is_featured"
                                                        true-value="1" false-value="0"
                                                        @change="onkeyPress('is_featured')">
                                                    <label class="form-check-label" for="is_featured"> Is
                                                        Featured</label>
                                                </div>

                                                <div class="invalid-feedback" v-if="errors.is_featured">
                                                    {{errors.is_featured[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="bstatus">Status</label>
                                            <select class="form-control border" v-model="form.status"
                                                @change="onkeyPress('status')" id="bstatus">
                                                <option value="1">Active</option>
                                                <option value="0">In Active</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.status">
                                                {{errors.status[0]}}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <div class="form-group">
                                                    <label>Brand Logo (128X128) *</label> <br>
                                                    <div class="fileinput fileinput-new" data-provides="fileinput"
                                                        style="position: relative;">
                                                        <span class="btn btn-block btn-primary btn-file"><span
                                                                class="fileinput-new"><i class="fa fa-camera"></i> Chose
                                                                Icon</span>
                                                            <span class="fileinput-exists" style="display:none">Change
                                                                Icon</span><input type="file" name="..."
                                                                @change="onImageChange" /></span>
                                                    </div>
                                                    <div class="invalid-feedback" v-if="errors.logo">
                                                        {{errors.logo[0]}}
                                                    </div>
                                                </div>
                                            </div> 
                                            
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <p>Photo Preview</p>
                                        <img :src="imagePreview" v-if="form.logo" width="200">
                                        <div v-if="editMode & !imagePreview">
                                            <img :src="form.logo" v-if="form.logo" width="200">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary " :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>{{btn}}
                                </button>

                            </div>
                        </form>
                    </div>
                </Modal>

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
    name: 'Brand',
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
            editMode: false,
            disabled: false,
            modalActive: false,
            errors: {},
            imagePreview: '',
            btn: 'Create',
            items: [],
            companies: [],
            form: new Form({
                id: '',
                name: '',
                description: '',
                website: '',
                logo: '',
                company_id: 0,
                is_featured: 0,
                status: 0,
            }),
            columns: [ 
                {
                    label: 'Brand Name',
                    name: 'name',           
                    width: '15%'
                },   
                {
                    label: 'Image',
                    name: 'logo',
                    isSearch: false, 
                    width: '15%'
                },
                {
                    label: 'Description',
                    name: 'description',
                    width: '20%'
                },
                {
                    label: 'Website',
                    name: 'website',
                    width: '15%'
                },
                {
                    label: 'Company',
                    name: 'company_name',
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
        };
    },
    created() {
        this.getDataItems();
        this.fetchCompanyData();
    },
    methods: {
        toggleModal: function () {
            this.modalActive = !this.modalActive;
            if (!this.modalActive) {
                this.editMode = false;
                this.btn = 'Create';
            }
            this.errors = '';
            this.isSubmit = false;
            this.form.reset();
        },
        fetchCompanyData() {
            axios.get(this.apiUrl + '/companies/', this.headerjson)
                .then((res) => {
                    this.companies = res.data.data;
                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                });
        },

        edit: function (item) {
            this.btn = 'Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item);
        },
        submitForm: function (e) {

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('company_id', this.form.company_id);
            formData.append('description', this.form.description);
            formData.append('website', this.form.website);
            formData.append('is_featured', this.form.is_featured);
            formData.append('status', this.form.status);
            if (this.editMode) {
                formData.append('_method', 'put');
                if (this.imagePreview) {
                    this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                }
                var postEvent = axios.post(this.apiUrl + '/brands/' + this.form.id, formData, this.headers);
            } else {
                this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                var postEvent = axios.post(this.apiUrl + '/brands', formData, this.headers);
            }

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if (res.status == 200) {
                    this.toggleModal(); 
                    this.getDataItems(this.apiUrl+'/brands?page='+this.pagination.currentPage);
                    this.$toast.success(res.data.message);
                } else {
                    this.$toast.error(res.data.message);
                }
            }).catch(err => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if (err.response.status == 422) {
                    this.errors = err.response.data.errors
                }
            });
        },

        onImageChange(e) {
            this.form.logo = e.target.files[0]
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.createImage(files[0]);
        },
        createImage(file) {
            let reader = new FileReader();
            let vm = this;
            reader.onload = (e) => {
                vm.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        validation: function (...fiels) {
            var obj = new Object();
            var validate = false;
            for (var k in fiels) {     // Loop through the object  
                for (var j in this.form) {
                    if ((j == fiels[k]) && (!this.form[j])) {
                        obj[fiels[k]] = fiels[k].replace("_", " ") + ' field is required';  // Delete obj[key]; 
                        this.errors = { ...this.errors, ...obj };
                    } else {
                        validate = false;
                    }
                }
            }
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },
        onkeyPress: function (field) {
            for (var k in this.errors) {     // Loop through the object
                if (k == field) {      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }
        },
        deleteItem: function (item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!",
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    axios.delete(this.apiUrl + '/brands/' + item.id, this.headerjson)
                        .then(res => {
                            if (res.status == 200) {
                                this.getDataItems(this.apiUrl+'/brands?page='+this.pagination.currentPage);
                                this.$toast.success(res.data.message);
                            } else {
                                this.$toast.error(res.data.message);
                            }
                        }).catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                }
            });
        },
        // For Pagination 
        getDataItems(url = this.apiUrl+'/brands') {
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
            this.getDataItems();
        },
        setPage(data){  
            this.getDataItems(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },

        ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
    },
    destroyed() { },
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
     width: 900px;
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