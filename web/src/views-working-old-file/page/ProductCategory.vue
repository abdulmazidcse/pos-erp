<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Product Category</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Product Category List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add New
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
                                <!-- @change="getSuppliers($event.target.value)" -->
                                <select class="form-control" v-model="tableData.company_id" @change="fetchItems()">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
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
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                      
                                        <td class="text-center">{{ i + 1 }} </td>
                                        <td class="text-center">{{ item.name}} </td>
                                        <td class="text-center">{{ item.parents ? item.parents.name : '' }} </td> 
                                        <td class="text-center"> <img :src="item.img_url+'/'+item.image" width="80" v-if="item.image"> </td>
                                        <td class="text-center">{{ item.discount }} </td> 
                                        <td class="text-center">{{ item.description }} </td> 
                                        <td class="text-center">
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline"></i> Edit</a>
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click.prevent="deleteItem(item)"><i class="mdi mdi-delete-outline"></i> Remove</a>
                                                    <!-- item-->
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
        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3>Product Category Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name">Category name *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Product Category name" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{errors.name[0]}}
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <label for="contact_person_name" style="float: none;">Parent Category </label>
                                        <!-- <select class="form-control border" v-model="form.parent_id" @change="onkeyPress('parent_id')" id="parent_id">
                                            <option value="0">Select Parent Category</option>
                                            <option v-for="(pcategory, index) in items" :value="pcategory.id" :key="index">{{ pcategory.name }}</option>
                                        </select>         -->
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="form.parent_id"
                                            placeholder="Select Parent Category"  
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="pcategories"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            :create-option="true" 
                                        />                                                   
                                        <div class="invalid-feedback" v-if="errors.parent_id">
                                            {{errors.parent_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="mb-3">
                                        <label for="description">Description </label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('description')" v-model="form.description" id="description" placeholder="Description Here!">
                                        <div class="invalid-feedback" v-if="errors.description">
                                            {{errors.description[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row"  >
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="discount">Discount (It's will be count percentage)</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('discount')" v-model="form.discount" id="discount" placeholder="Discount amount!">
                                            <div class="invalid-feedback" v-if="errors.discount">
                                                {{errors.discount[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mt-3 float-right">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_featured" v-model="form.is_featured" true-value="1" false-value="0" @change="onkeyPress('is_featured')">
                                                <label class="form-check-label" for="is_featured">Is Featured</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_featured">
                                                {{errors.is_featured[0]}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="form-group">
                                            <label>Category Icon (128X128) *</label> <br>
                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="position: relative">
                                            <span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Choose Icon</span>
                                            <span class="fileinput-exists" style="display:none">Change Icon</span><input type="file" name="..." @change="onImageChange"/></span>
                                            </div> 
                                            <div class="invalid-feedback" v-if="errors.image">
                                            {{errors.image[0]}}
                                        </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="pcstatus">Status</label>
                                        <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="pcstatus">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.status">
                                            {{errors.status[0]}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <p>Photo Preview</p>
                                <img :src="imagePreview" v-if="form.image" width="200" >
                                <div v-if="editMode & !imagePreview">
                                    <img :src="form.img_url+'/'+form.image" width="100" v-if="form.image">  
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
import Modal from "./../helper/Modal"; 
import Form from 'vform'
import axios from 'axios';

import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';

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
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            imagePreview:'',
            btn:'Create',
            items: [],
            pcategories: [],
            form: new Form({
                id: '',
                name: '',
                description: '',
                image: '',
                parent_id: 0,
                is_featured: 0,
                discount:0,
                status: 1,
                img_url: ''
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
                    label: 'Category Name',
                    name: 'name',           
                    width: '25%'
                },   
                {
                    label: 'Parent Category',
                    name: 'parent_cat_name',
                    width: '25%'
                },
                {
                    label: 'Image',
                    name: 'image',
                    width: '20%'
                },
                {
                    label: 'Discount',
                    name: 'discount',
                    width: '5%'
                },  
                {
                    label: 'Description',
                    name: 'description',
                    width: '15%'
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
                column: 1, 
                dir: 'asc',
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
            }, 
        };
    },
    created() {
        this.fetchItems();
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
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        // fetchIndexData() { 
        //     axios.get(this.apiUrl+'/product_categories', this.headerjson) 
        //     .then((res) => {
        //         this.items = res.data.data;
        //         this.pcategories = res.data.data.map((item) => {
        //             return { label: item.name, value: item.id }
        //         });

        //     })
        //     .catch((err) => { 
        //         this.$toast.error(err.response.data.message);
        //     }).finally((ress) => {
        //         this.loading = false;
        //     });
        // },

        // Funtion For Pagination 
        fetchItems(url = this.apiUrl+'/product_categories/list') { 
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers:this.headerparams})
            .then((response) => {
                let data = response.data.data;   
                if(this.tableData.draw = data.draw) { 
                  this.items = data.data.data;
                  this.pcategories = data.data.data.map(({ id, name }) => (
                  { label: name, value: id }
                  )); 
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
            //this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = sortable; ///this.sortOrders[key] === 1 ? 'asc' : 'desc'; 
            this.fetchItems();
        },
        setPage(data){  
            this.fetchItems(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },

        filterData(){
          this.fetchItems(); 
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
            formData.append('parent_id', this.form.parent_id);
            formData.append('description', this.form.description);
            formData.append('is_featured', this.form.is_featured);
            formData.append('status', this.form.status);  
            formData.append('discount', this.form.discount);  
            if(this.editMode){
                formData.append('_method', 'put');
                if(this.imagePreview){
                    this.form.image ? formData.append('image', this.form.image, this.form.image.name) : '';
                } 
                var postEvent = axios.post(this.apiUrl+'/product_categories/'+this.form.id, formData, this.headers);
            }else{ 
                this.form.image ? formData.append('image', this.form.image, this.form.image.name) : '';
                var postEvent = axios.post(this.apiUrl+'/product_categories', formData, this.headers);
            }  

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal(); 
                    this.fetchItems();
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
            });
        },

        onImageChange(e) {
            this.form.image = e.target.files[0]
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
                    axios.delete(this.apiUrl+'/product_categories/'+item.id,this.headers) 
                    .then(res => {
                        if(res.status == 200){   
                            this.fetchItems();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
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