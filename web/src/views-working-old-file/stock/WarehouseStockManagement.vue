<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Stock </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Warehouse Stock Management</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <!-- <button type="button" class="btn btn-primary float-right" @click="toggleImportModal">
                            <i class="fas fa-plus"></i> Bulk Adjustment
                        </button>  -->
                    </div>
                </div>
            </div>
        </div>

        <!--Bulk Adjustment Modal -->
        <!-- <Modal @close="toggleImportModal" :modalActive="importModal">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleImportModal" type="button" class="btn btn-default">X</button>
                    <h5 style="text-align: right">Import Adjustment Product</h5>
                </div>

                <div class="modal-body">  
                    <div class="row">
                        <div class="col-md-12">
                            <form role="form" @submit.prevent="submitAdjustBulkStock()" enctype="multipart/form-data">
                                <p style="font-size: 13px; font-style: italic;">The field labels marked with * are required input fields.</p>
                                <p style="font-size: 16px;">The correct column order is (product_name*, product_code*, outlet_name*, expires_date, in_stock, out_stock) and you must follow this.</p>
                                <p style="font-size: 16px;">In Stock for increase stock product and out_stock for decrease stock_product. Both default value is 0.</p>
                                <p style="font-size: 16px;">(If you provide expires date this product will be stock based on expires date)</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="excel_file"> Upload EXCEL File *</label>
                                            <input type="file" class="form-control" id="excel_file" ref="file" name="..." @change="adjustProductImportFile(), onkeyPress('excel_file')">
                                            
                                            <div class="invalid-feedback" v-if="errors.excel_file">
                                                {{errors.excel_file[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label> Sample File</label>
                                            <a :href="samplefile_url" class="btn btn-info" style="display: block; width: 100%; clear:both;" download><i class="fas fa-download"></i> Download</a>
                                        </div>
                                    </div>
                                </div> -->

                                <!-- <div class="form-group">
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new" id="excel-file">
                                            <i class="fas fa-file-excel"></i> Choose Excel
                                        </span>
                                        <input type="file" id="file" ref="file" @change="handleFileUpload()"/>
                                    </span>
                                </div> -->

                               <!-- <div>
                                    <button type="submit" class="btn btn-primary" :disabled="disabled">
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
        </Modal> -->


        <!-- Adjustment Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    <h5 style="text-align: right;">Stock Adjustment</h5>
                </div>
                <form @submit.prevent="submitAdjustStock()" enctype="multipart/form-data" >
                    <div class="modal-body">

                        <div class="row">
                            <input type="hidden" v-model="adjustObj.stock_id">
                            <input type="hidden" v-model="adjustObj.unit_code">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="in_stock_quantity">In Stock Quantity/Weight</label>
                                    <input type="number" class="form-control border " v-model="adjustObj.in_stock_quantity" id="in_stock_quantity" placeholder="Increase Quantity" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="out_stock_quantity">Out Stock Quantity/Weight</label>
                                    <input type="number" class="form-control border " v-model="adjustObj.out_stock_quantity" id="out_stock_quantity" placeholder="Decrease Quantity" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control border " v-model="adjustObj.remarks" id="remarks" rows="3" placeholder="Remarks Here!"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> Submit 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <div class="row" >
                            <div class="col-md-2">
                                <div class="">
                                    <label for="warehouse_id"> Warehouse *</label>
                                    <select class="form-control" id="warehouse_id" v-model="tableData.warehouse_id">
                                        <option value="">--- Select Warehouse ---</option>
                                        <option v-for="(warehouse, i) in warehouses" :key="i" :value="warehouse.id">{{ warehouse.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label for="category_id"> Category *</label>
                                    <select class="form-control" id="category_id" v-model="tableData.category_id" @change="onChangeCategory($event.target.value)">
                                        <option value="">--- Select Category ---</option>
                                        <option v-for="(category, i) in categories" :key="i" :value="category.id">{{ category.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label for="sub_category_id"> Sub Category *</label>
                                    <select class="form-control" id="sub_category_id" v-model="tableData.sub_category_id">
                                        <option value="">--- Select Sub Category ---</option>
                                        <option v-for="(sub_category, i) in sub_categories" :key="i" :value="sub_category.id">{{ sub_category.name }}</option>
                                    </select>
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
                                    <button type="submit" class="btn btn-primary" :disabled="disabled" @click="fetchItems(), disabled=true, isSubmit=true">
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
                                        <td>{{ item.warehouse_name }} </td>
                                        <td>{{ item.product_name }} </td> 
                                        <td>{{ item.product_code }} </td> 
                                        <td class="text-center">{{ item.punit.toUpperCase() }} </td> 
                                        <td>{{ item.expires_date }} </td> 
                                        <td class="text-right">{{ item.in_stock_quantity + ' || '+ item.in_stock_weight }} </td> 
                                        <td class="text-right">{{ item.out_stock_quantity + ' || '+ item.out_stock_weight }} </td> 
                                        <td class="text-right">{{ item.stock_quantity + ' || '+ item.stock_weight }} </td> 
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <a href="javascript:void(0);" class="dropdown-item text-primary" @click="adjustProductStock(item)" title="Adjust Stock">
                                                        <i class="mdi mdi-plus-outline me-1"></i>Adjustment
                                                    </a>
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
                                        <th colspan="6" class="text-center"> Total </th>
                                        <th class="text-right">{{ totalQuantity('purchase') }}</th>
                                        <th class="text-right">{{ totalQuantity('sold') }}</th>
                                        <th class="text-right">{{ totalQuantity('available') }}</th>
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
            warehouses: [],
            categories: [],
            parent_sub_categories: [],
            sub_categories: [],
            units: [],
            adjustObj: new Form({
                stock_id: '',
                unit_code: '',
                in_stock_quantity: '',
                out_stock_quantity: '',
                remarks: '',
            }),
            // importFile: '',
            // samplefile_url: this.baseUrl+'/import_excel_demo/stock_products_adjustment.xlsx',
            columns: [ 
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                },   
                {
                    label: 'Warehouse',
                    name:  'warehouses.name',
                    width: '10%'
                },
                {
                    label: 'Product Name',
                    name:  'products.product_name',
                    width: '15%'
                },
                {
                    label: 'Product Code',
                    name:  'products.product_code',
                    width: '10%'
                },  
                {
                    label: 'Unit',
                    name:  'punit',
                    width: '5%'
                },
                {
                    label: 'Expires Date',
                    name:  'expires_date',
                    width: '10%'
                },
                {
                    label: 'In-Stock Qty || Weight',
                    name:  'in_stock_quantity',
                    width: '10%'
                },
                {
                    label: 'Sold Qty || Weight',
                    name:  'out_stock_quantity',
                    width: '10%'
                },
                {
                    label: 'Stock Qty || Weight',
                    name:  'stock_quantity',
                    width: '10%'
                },
                {
                    label: 'Actions',            
                    name:  '',
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
                sortKey: 'product_name', 
                warehouse_id: '',
                category_id: '',
                sub_category_id: '',
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
        this.fetchWarehouse();
        this.fetchCategoryData();
    },
    methods: { 
        toggleModal: function() {
            this.modalActive = !this.modalActive;
            if(!this.modalActive) {
                this.adjustObj.reset();
            }
        },

        toggleImportModal: function() {
            this.importModal = !this.importModal;
            this.importFile = '';
            this.$refs.file.value=null;
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
            this.items.filter((item) => {
                if(type=="purchase") {
                    qsum += parseFloat(item.in_stock_quantity);
                    wsum += parseFloat(item.in_stock_weight);
                }else if(type=="sold") {
                    qsum += parseFloat(item.out_stock_quantity);
                    wsum += parseFloat(item.out_stock_weight);
                }else{
                    qsum += parseFloat(item.stock_quantity);
                    wsum += parseFloat(item.stock_weight);
                }
            });

            return qsum +' || '+ wsum;
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

        fetchStockData() { 
            axios.get(this.apiUrl+'/stocks/warehouse-stock-management')
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => { 
                console.log("error", err.response);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        

        fetchWarehouse() {
            axios.get(this.apiUrl+"/warehouses", this.headerjson) 
            .then((resp) => {
                this.warehouses = resp.data.data;
            })
            .catch((err) => {
                console.log("error", err.response);
            })
        },

        fetchCategoryData() {
            axios.get(this.apiUrl+"/getProductCategory", this.headerjson) 
            .then((resp) => {
                this.categories = resp.data.data.parent_category;
                this.parent_sub_categories = resp.data.data.sub_category;
            })
            .catch((err) => {
                console.log("error", err.response);
            })
        },

        onChangeCategory: function(cat_id) {
            this.sub_categories = [];
            this.tableData.sub_category_id = '';
            if(cat_id != "") {
                this.sub_categories = this.parent_sub_categories[cat_id];
            } 
        },
        

        adjustProductStock: function(item) {
            this.adjustObj.stock_id = item.id;
            this.adjustObj.unit_code = this.checkUnitCode(item.product_purchase_unit_id)
            this.toggleModal();
        },

        adjustProductImportFile()
        {
            this.importFile = this.$refs.file.files[0];
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        
        submitAdjustStock: function(e) { 

            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('stock_id', this.adjustObj.stock_id); 
            formData.append('unit_code', this.adjustObj.unit_code); 
            formData.append('in_stock_quantity', this.adjustObj.in_stock_quantity); 
            formData.append('out_stock_quantity', this.adjustObj.out_stock_quantity); 
            formData.append('note', this.adjustObj.remarks); 
            
            var postEvent = axios.post(this.apiUrl+'/stocks/warehouse-stock-adjustment', formData, this.headers);         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.adjustObj.reset();
                    this.toggleModal();
                    this.fetchItems();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
                console.log('then',this.isSubmit)
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
            });
        },

        // submitAdjustBulkStock: function(e) { 

        //     this.isSubmit = true;
        //     this.disabled = true;
        //     const formData = new FormData();  
        //     formData.append('excel_file', this.importFile);
            
        //     var postEvent = axios.post(this.apiUrl+'/stocks/stockBulkAdjustment', formData, this.headers);         

        //     postEvent.then(res => {
        //         this.isSubmit = false;
        //         this.disabled = false;
        //         console.log("response===", res);
        //         if(res.status == 200){
        //             this.importFile = '';
        //             this.toggleImportModal();
        //             this.fetchItems();
        //             this.$toast.success(res.data.message); 
        //         }else{
        //             this.$toast.error(res.data.message);
        //         }
        //         console.log('then',this.isSubmit)
        //     }).catch(err => { 
        //         this.isSubmit = false; 
        //         this.disabled = false;
        //         this.$toast.error(err.response.data.message);
        //         if(err.response.status == 422){
        //             this.errors = err.response.data.errors 
        //         }
        //         console.log('catch',this.isSubmit)
        //     });
        // },

        // datatable For Pagination 
        fetchItems(url = this.apiUrl+'/stocks/warehouse-stock-management') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData,  headers:this.headerparams})
            .then((response) => {
                this.isSubmit = false;
                this.disabled = false;
                let data = response.data.data; 
                // console.log("iewutoierugoid", data)
                // if(this.tableData.draw == data.draw) {
                  console.log("kfjsdkjgkdf", data.data);
                    this.items = data.data;
                    this.configPagination(data);
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