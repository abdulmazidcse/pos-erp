<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory Summary Report</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                              Add New
                            </button>  -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h3 style="text-align: center;">Inventory Summary Report</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div>
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="tableData.outlet_id"
                                            placeholder="Select Outlet"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="outlet_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            @change="changeOutlet($event), onkeyPress('outlet_id')"
                                        /> 
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <Multiselect 
                                            class="form-control border category_id" 
                                            mode="single"
                                            v-model="tableData.category_id"
                                            placeholder="Select Category"
                                            @change="changeCategory($event), onkeyPress('category_id')"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="category_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false"
                                        /> 
                                        <!-- <input type="date" class="form-control" id="from_date" v-model="search_terms.from_date" @change="onkeyPress('from_date')"> -->
                                        <div class="invalid-feedback" v-if="errors.category_id">
                                            {{errors.category_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="">
                                        <button type="submit" class="btn btn-sm btn-primary" :disabled="disabled" @click="filterStockReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                        <a href="javascript:void(0);" style="margin-left: 5px;" class="btn-sm btn btn-primary" @click.prevent="printItem()" ><i class="mdi mdi-printer-outline me-1"></i> </a>                                             

                                        <a href="javascript:void(0);" style="margin-left: 5px;" class="btn-sm btn btn-primary"
                                        @click.prevent="downloading ? null : exportToExcel()">
                                            <span v-show="downloading"  >
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span> 
                                            <i class="mdi mdi-file-excel me-1"></i> 
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                                        <select class="form-select" v-model="tableData.length" @change="getInventorySummaryReport()">  
                                                            <option value="5" selected="selected">5</option>
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
                                                <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getInventorySummaryReport()">
                                            </div>
                                        </div>
                                    </div>   
                                </template> 
                                <template #body > 
                                    <tbody v-if="items.length > 0">
                                        <tr class="border" v-for="(item, i) in items" :key="i">
                                            <td>{{ i + 1 }} </td>
                                            <td>{{ item.name }} </td>
                                            <td class="text-right">{{ item.item_count ?? 0 }} </td>
                                            <td class="text-right">{{ item.stock_quantity }}</td>
                                            <td class="text-right">{{ item.stock_purchase_amount.toFixed(2) ?? 0 }} </td>
                                            <td class="text-right">{{ item.stock_sale_amount.toFixed(2) ?? 0 }} </td>
                                            <!-- <td>
                                                <div class="dropdown float-end">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        item-->
                                                        <!-- <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                        <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
                                                    </div>
                                                </div> 
                                            </td> -->
                                        </tr>

                                        <tr style="font-weight: bold;">
                                            <td colspan="2" class="text-center">Total</td>
                                            <td class="text-right">{{ totalBalance('item', 0) }}</td>
                                            <td class="text-right">{{ totalBalance('stock', 0) }}</td>
                                            <td class="text-right">{{ parseFloat(totalBalance('purchase') - discount_amount).toFixed(2) }}</td>
                                            <td class="text-right">{{ totalBalance('sold') }}</td>
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

                    <Modal @close="toggleModal()"  >
                        <div class="modal-content scrollbar-width-thin orderPreview" >
                            <div class="modal-header"> 
                                <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                <h3 style="width: 100%">Inventory Summary Report</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tbody>
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Inventory Summary Report</h4>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                        <thead class="tableFloatingHeaderOriginal">
                                            <tr class="success item-head">
                                                <th width="3%" style="text-align: center">SL </th>
                                                <th width="10%" style="text-align: center">Name </th>
                                                <th width="7%" style="text-align: center">Item</th>
                                                <th width="10%" style="text-align: center">Stock Qty </th>
                                                <th width="10%" style="text-align: center">Stock Purchase Amount</th>
                                                <th width="10%" style="text-align: center">Stock Sale Amount  </th>
                                            </tr>
                                        </thead>

                                        <tbody v-if="items.length > 0">
                                            <tr class="border" v-for="(item, i) in items" :key="i">
                                                <td>{{ i + 1 }} </td>
                                                <td>{{ item.name }} </td>
                                                <td style="text-align: right">{{ item.item_count }} </td>
                                                <td style="text-align: right">{{ item.stock_quantity }}</td>
                                                <td style="text-align: right">{{ item.stock_purchase_amount.toFixed(2) ?? 0 }} </td>
                                                <td style="text-align: right">{{ item.stock_sale_amount.toFixed(2) ?? 0 }} </td>
                                            </tr> 
                                            <tr style="font-weight: bold;">
                                                <td colspan="2" class="text-center">Total</td>
                                                <td style="text-align: right">{{ totalBalance('item', 0) }}</td>
                                                <td style="text-align: right">{{ totalBalance('stock', 0) }}</td>
                                                <td style="text-align: right">{{ parseFloat(totalBalance('purchase') - discount_amount).toFixed(2) }}</td>
                                                <td style="text-align: right">{{ totalBalance('sold') }}</td>
                                            </tr>
                                        </tbody> 
                                    </table> 
                                </div>
                            </div>
                        </div>
                    </Modal>

                </div>
            </div>        
        </div>
    </transition>
</template>
<script>
import {mapGetters, mapActions} from "vuex"; 
import Modal from "./../helper/Modal";
// import { ref, onMounted } from "vue";
import axios from 'axios';
// import Form from 'vform';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';

export default {
    name: 'PosLeftbar',
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
            downloading: false, 
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            outlets: [],
            outlet_options: [],
            categories: [],
            category_options: [],
            products: [],
            product_options: [],
            units: [],
            discount_amount: 0,
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
                    label: 'Category',
                    name: 'name',
                    width: '20%'
                },
                {
                    label: 'Item',
                    name: 'item_count',
                    width: '7%'
                },
                {
                    label: 'Stock Qty',
                    name: 'stock_quantity',
                    width: '7%'
                },
                {
                    label: 'Stock Purchase Amount',
                    name: 'stock_purchase_amount',
                    width: '10%'
                },
                {
                    label: 'Stock Sale Amount',
                    name: 'stock_sale_amount',
                    width: '10%'
                },
                // {
                //     label: 'Actions',            
                //     name: '',
                //     isSearch: false, 
                //     isAction: true,
                //     width: '5%'

                // }
            ],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'asc',
                sortKey: 'name', 
                outlet_id: '',
                category_id: '',
                product_id: '',
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
        this.fetchOutlets();
        this.fetchProductCategories();
        this.getInventorySummaryReport();
    },

    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        checkUnitCode(unit_id) {
            var unit_item = this.units.find(({id}) => id == unit_id);

            return unit_item.unit_code.toLowerCase();
        },

        totalBalance(type, decpoint=2) {
            var sum = 0;
            if(type == 'sold') {
                this.items.filter((item) => {
                    sum += parseFloat(item.stock_sale_amount);
                });
            }
            if(type == 'purchase') {
                this.items.filter((item) => {
                    sum += parseFloat(item.stock_purchase_amount);
                });
            }
            if(type == 'item') {
                this.items.filter((item) => {
                    sum += parseFloat(item.item_count);
                });
            }
            if(type == 'stock') {
                this.items.filter((item) => {
                    sum += parseFloat(item.stock_quantity);
                });
            }

            return sum.toFixed(decpoint);
        },

        fetchOutlets() {
            this.outlet_options = [];
            axios.get(this.apiUrl+"/outlets", this.headerjson)
            .then((resp) => {
                this.outlets = resp.data.data;
                resp.data.data.map((item) => {
                    this.outlet_options.push({value: item.id, label: item.name})
                })
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchProductCategories() {
            this.category_options = [];
            axios.get(this.apiUrl+"/product_categories", this.headerjson)
            .then((resp) => {
                this.categories = resp.data.data;
                resp.data.data.map((item) => {
                    if(item.parent_id !=0) {
                        this.category_options.push({value: item.id, label: item.name +' ('+ item.parent_cat_name +')'})
                    }
                })
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        filterStockReport()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.getInventorySummaryReport();
        },

        // For Pagination 
        getInventorySummaryReport(url = this.apiUrl+'/reports/inventory-summary-report') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw = data.draw) {
                    if(Array.isArray(data.data.data)) {
                        this.items = data.data.data;
                    }else {
                        this.items = Object.keys(data.data.data).map(key => {
                                        return data.data.data[key];
                                    });
                    }
                    this.configPagination(data.data);
                }
                this.isSubmit  = false;
                this.tableData.category_id  = '';
                this.tableData.product_id   = '';
                this.categories.map((item) => {
                    if(item.parent_id !=0) {
                        this.category_options.push({value: item.id, label: item.name})
                    }
                })
            })
            .catch((err) => {
                console.log(err);
                this.$toast.error(err.response);
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
            this.getInventorySummaryReport();
        },
        setPage(data){  
            this.getInventorySummaryReport(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        // End Pagination
        changeOutlet(outlet_id) {
            this.tableData.outlet_id    = outlet_id;
        },

        changeCategory(category_id) {
            this.tableData.category_id = category_id;
        },

        changeProduct(product_id) {
            this.tableData.product_id = product_id;
        },

        checkRequiredPrimary()
        {
            if(this.tableData.category_id != '') {
                this.disabled = false;
            }else if(this.tableData.product_id != '') {
                this.disabled = false;
            }else if(this.tableData.outlet_id != '') {
                this.disabled = false;
            }else{
                this.disabled = true;
            }
        },
        onkeyPress: function(field) { 
            this.checkRequiredPrimary();
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },

        printItem: function () {   
            this.printContent('printArea');
        }, 
        printContent(document_id) { 
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [ 
                    this.baseUrlPrintCSS+'/assets/css/bootstrap-print.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            this.$htmlToPaper(document_id, options);
        },
        async exportToExcel() {
            // if (!this.form.customer_id) {
            //     this.$toast.error('Please select a customer');
            //     return;
            // } 
            const queryString = this.objectToQueryString( this.tableData); 

            this.downloading = true; 

            const query = queryString;
            try {
                const response = await axios.get(`${this.apiUrl}/reports/inventory-summary-report-excel-export${query}`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'inventory-summary-report.xlsx');
                document.body.appendChild(link);
                link.click();
                this.downloading = false;
            } catch (error) { 
                this.downloading = false;
            }
        },
        
        objectToQueryString: function (obj) {   
            const queryString = Object.keys(obj)
                .filter((key) => obj[key] !== undefined) // Exclude undefined values
                .map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`)
                .join('&');
            return `?${queryString}`;
        }, 
        


    },

    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    }, 
    computed:{
        ...mapGetters([
          'userData',
          'token',
          'dataCheck'
        ]), 
    } 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin {
    border: none !important;
    width: 90%;
    display: block;
    margin: 0 auto;
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

.actions a{
    margin-right: 5px;
}

tr:nth-child(even) {
  background-color: #F4F4F4;
}
</style>