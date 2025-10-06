<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Inventory </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Damage Product Management</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleAddNewModal">
                            <i class="fas fa-plus"></i> Add New
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!--Addt Modal -->
        <Modal @close="toggleAddNewModal" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleAddNewModal" type="button" class="btn btn-default">X</button>
                    <h5 style="text-align: right">Add New Damage Product</h5>
                </div>

                <div class="modal-body">  
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" @submit.prevent="submitDamageProduct()" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="outlet_id">Outlet *</label> <br>
                                        <Multiselect
                                            class="form-control border outlet_id"
                                            mode="single"
                                            v-model="obj.outlet_id"
                                            placeholder="Select Outlet"
                                            @change="onkeyPress('outlet_id')"
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

                                    <div class="mb-3 col-md-6">
                                        <label for="product_id">Product *</label> <br>
                                        <Multiselect
                                            class="form-control border product_id"
                                            mode="single"
                                            v-model="obj.product_id"
                                            placeholder="Select Product"
                                            @change="onkeyPress('product_id')"
                                            :searchable="true"
                                            :filter-results="true"
                                            :options="product_options"
                                            :classes="multiclasses"
                                            :close-on-select="true"
                                            :min-chars="1"
                                            :resolve-on-load="false"
                                        />
                                        <div class="invalid-feedback" v-if="errors.product_id">
                                            {{errors.product_id[0]}}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="damage_quantity">Damage Quantity </label>
                                        <input type="number" class="form-control border" id="damage_quantity" @keyup="onkeyPress('damage_quantity')" v-model="obj.damage_quantity">
                                        <div class="invalid-feedback" v-if="errors.damage_quantity">
                                            {{errors.damage_quantity[0]}}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 col-md-6">
                                        <label for="expires_date">Expires Date </label>
                                        <input type="date" class="form-control border" id="expires_date" @change="onkeyPress('expires_date')" v-model="obj.expires_date">
                                        <div class="invalid-feedback" v-if="errors.expires_date">
                                            {{errors.expires_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label for="note">Note</label>
                                        <textarea class="form-control border" id="note" rows="3" @keyup="onkeyPress('note')" v-model="obj.notes" placeholder="Remarks Here!"></textarea>
                                        <div class="invalid-feedback" v-if="errors.notes">
                                            {{errors.notes[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary float-right" :disabled="disabled">
                                        <span v-show="isSubmit">
                                            <i class="fas fa-spinner fa-spin" ></i>
                                        </span>Submit 
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <div class="row" >
                            <div class="col-md-2">
                                <div class="">
                                    <label for="outlet_id"> Outlet *</label> <br>
                                        <Multiselect
                                            class="form-control border outlet_id"
                                            mode="single"
                                            v-model="tableData.outlet_id"
                                            placeholder="Select Outlet"
                                            @change="onkeyPress('outlet_id')"
                                            :searchable="true"
                                            :filter-results="true"
                                            :options="outlet_options"
                                            :classes="multiclasses"
                                            :close-on-select="true"
                                            :min-chars="1"
                                            :resolve-on-load="false"
                                        />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="">
                                    <label for="product_id"> Product *</label> <br>
                                        <Multiselect
                                            class="form-control border product_id"
                                            mode="single"
                                            v-model="tableData.product_id"
                                            placeholder="Select Product"
                                            @change="onkeyPress('product_id')"
                                            :searchable="true"
                                            :filter-results="true"
                                            :options="product_options"
                                            :classes="multiclasses"
                                            :close-on-select="true"
                                            :min-chars="1"
                                            :resolve-on-load="false"
                                        />
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="">
                                    <label for="from_date"> From Date *</label>
                                    <input type="date" class="form-control" id="start_date" v-model="tableData.start_date">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="">
                                    <label for="to_date"> To Date *</label>
                                    <input type="date" class="form-control" id="end_date" v-model="tableData.end_date">
                                </div>
                            </div>

                            <div class="col-md-2" style="padding-right: 0 !important; text-align: right;">
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" :disabled="disabled" @click="fetchItems(), disabled=true, isSubmit=true">
                                        <span v-show="isSubmit">
                                            <i class="fas fa-spinner fa-spin" ></i>
                                        </span>Filter 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                        <td>{{ i + 1 }} </td>
                                        <td>{{ item.outlets.name }} </td>
                                        <td>{{ item.products.product_name }} </td> 
                                        <td>{{ item.products.product_code }} </td> 
                                        <td>{{ item.expires_date }} </td> 
                                        <td class="text-right">{{ Number(item.damage_quantity) }} </td> 
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['damage-product-edit']"><i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['damage-product-delete']"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-center"> Total </th>
                                        <th class="text-right">{{ totalQuantity('purchase') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
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
            importModal: false,
            errors: {},
            items: [],
            outlets: [],
            outlet_options: [],
            products: [],
            product_options: [],
            units: [],
            obj: new Form({
                outlet_id: '',
                product_id: '',
                expires_date: '',
                damage_quantity: '',
                notes: '',
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
                    label: 'Outlet',
                    name: 'outlets.name',
                    width: '10%'
                },
                {
                    label: 'Product Name',
                    name: 'products.product_name',
                    width: '15%'
                },
                {
                    label: 'Product Code',
                    name: 'products.product_code',
                    width: '10%'
                },  
                {
                    label: 'Expires Date',
                    name: 'expires_date',
                    width: '10%'
                },
                {
                    label: 'Damage Quantity',
                    name: 'damage_quantity',
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
                column: 2,
                dir: 'desc',
                sortKey: 'products.product_name', 
                outlet_id: '',
                product_id: '',
                start_date: '',
                end_date: '',

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
        // this.fetchStockData();
        this.fetchUnits();
        this.fetchItems();
        this.fetchOutlet();
        this.fetchProducts();
    },
    methods: { 
        toggleAddNewModal: function() {
            this.modalActive = !this.modalActive;
            if(!this.modalActive) {
                this.obj.reset();
            }
        },

        checkUnitCode(unit_id) {
            var unit_item = this.units.find(({id}) => id == unit_id);
            if(unit_item) {
                return unit_item.unit_code.toLowerCase();
            }else{
                return '';
            }
        },

        totalQuantity(type="") {
            var qsum = 0;
            var wsum = 0;
            if(this.items.length > 0) {
                this.items.filter((item) => {
                    qsum += parseFloat(item.damage_quantity);
                });
            }

            return qsum;
        },

        fetchUnits() {
            axios.get(this.apiUrl+"/units", this.headerjson)
            .then((resp) => {
                this.units = resp.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        fetchProducts() { 
            axios.get(this.apiUrl+'/products', this.headerjson)
            .then((res) => {
                this.products = res.data.data;
                this.product_options = res.data.data.map((item) => {
                    return {label: item.product_name+' -- '+item.product_code, value: item.id};
                });

            })
            .catch((err) => { 
                console.log("error", err.response);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        

        fetchOutlet() {
            axios.get(this.apiUrl+"/outlets", this.headerjson) 
            .then((resp) => {
                this.outlets = resp.data.data;
                this.outlet_options = resp.data.data.map((item) => {
                    return {label: item.name, value: item.id};
                });
            })
            .catch((err) => {
                console.log("error", err.response);
            })
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        
        submitDamageProduct: function(e) { 

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('outlet_id', this.obj.outlet_id); 
            formData.append('product_id', this.obj.product_id); 
            formData.append('damage_quantity', this.obj.damage_quantity); 
            formData.append('expires_date', this.obj.expires_date); 
            formData.append('notes', this.obj.notes); 
            
            var postEvent = axios.post(this.apiUrl+'/damage-products', formData, this.headers);         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.obj.reset();
                    this.toggleAddNewModal();
                    this.fetchItems();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
            });
        },

        // datatable For Pagination 
        fetchItems(url = this.apiUrl+'/damage-products') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData,  headers:this.headerparams})
            .then((response) => {
                this.isSubmit = false;
                this.disabled = false;
                let data = response.data.data; 
                // console.log("iewutoierugoid", data)
                // if(this.tableData.draw == data.draw) {
                  console.log("kfjsdkjgkdf", data.data);
                    this.items = data.data.data;
                    this.configPagination(data.data);
                // }
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
        }
        // datatable For Pagination 

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

.total_quantity {
    float: right;
    color: red;
}
.approve_total_quantity {
    float: right;
    color: red;
    margin-right: 10px;
}

.product_table {
    padding: 0;
    min-height: auto;
}

.product_table tbody td input {
    border-bottom: 1px solid #cecece;
}

.actions {
    
}

.actions a{
    margin-right: 5px;
}
</style>