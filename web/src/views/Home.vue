<template>  
    
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <form class="d-flex">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-light" id="dash-daterange">
                                <span class="input-group-text bg-primary border-primary text-white">
                                    <i class="mdi mdi-calendar-range font-13"></i>
                                </span>
                            </div>
                            <a href="javascript: void(0);" class="btn btn-primary ms-2">
                                <i class="mdi mdi-autorenew"></i>
                            </a>
                            <a href="javascript: void(0);" class="btn btn-primary ms-1">
                                <i class="mdi mdi-filter-variant"></i>
                            </a>
                        </form>
                    </div>
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-5 col-lg-6">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-multiple widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Customers</h5>
                                <h3 class="mt-3 mb-3">{{ counter.customer_count }}</h3>
                                <a href="/customers" class="btn btn-outline-dark float-right">Manage <i class="fas fa-arrow-circle-right"></i></a>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cart-plus widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Orders</h5>
                                <h3 class="mt-3 mb-3">{{ counter.order_count }}</h3>
                                <a href="/pos-sales" class="btn btn-outline-dark float-right">Manage <i class="fas fa-arrow-circle-right"></i></a>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row -->

                <div class="row">
                    <div class="col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-multiple widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Suppliers</h5>
                                <h3 class="mt-3 mb-3">{{ counter.supplier_count }}</h3>
                                <a href="/suppliers" class="btn btn-outline-dark float-right">Manage <i class="fas fa-arrow-circle-right"></i></a>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cart widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Growth">Products</h5>
                                <h3 class="mt-3 mb-3">{{ counter.product_count }}</h3>
                                <a href="/product" class="btn btn-outline-dark float-right">Manage <i class="fas fa-arrow-circle-right"></i></a>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row -->

            </div> <!-- end col -->

            <div class="col-xl-7 col-lg-6">
                <div class="card card-h-100" v-if="permission['dashboard-latest-sale-view']">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="header-title"><i class="fas fa-shopping-bag"></i> Last 7 Days Sale Comparison</h4>
                        </div>

                        <div dir="ltr">
                            <apexchart width="100%" height="280" type="bar" :options="options" :series="series"></apexchart>
                        </div>
                            
                    </div> <!-- end card-body-->
                </div> <!-- end card-->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body product_stock_report">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="header-title"><i class="fas fa-shopping-cart"></i> Product Stock Report</h4>
                        </div>
                        
                        <!-- <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4> -->
                        <Datatable 
                            :columns="stockProductColumns" 
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
                                                    <select class="form-select" v-model="tableData.length" @change="fetchProductStock()"> 
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchProductStock()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >  
                                <tbody v-if="stock_product_items.length > 0">
                                    <tr class="border" v-for="(item, i) in stock_product_items" :key="i">
                                        <td>{{ i + 1 }} </td>
                                        <td>{{ item.product_name }} </td> 
                                        <td>{{ item.product_code }} </td> 
                                        <td class="text-center">{{ item.in_stock_quantity }} </td> 
                                        <td class="text-center">{{ item.in_stock_weight }} </td> 
                                        <td class="text-center">{{ item.out_stock_quantity }} </td> 
                                        <td class="text-center">{{ item.out_stock_weight }} </td> 
                                        <td class="text-center">{{ item.stock_quantity }} </td> 
                                        <td class="text-center">{{ item.stock_weight }} </td> 
                                        <!-- <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end"> -->
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <!-- <a href="javascript:void(0);" class="dropdown-item text-primary" @click="adjustProductStock(item)" title="Adjust Stock">
                                                        <i class="mdi mdi-plus-outline me-1"></i>Adjustment
                                                    </a> -->
                                                    <!-- item-->
                                                <!-- </div>
                                            </div>   
                                        </td> -->
                                    </tr> 
                                </tbody> 
                                <tbody v-else>
                                    <tr>
                                        <td colspan="5"> No Data Available Here!</td>
                                    </tr>
                                </tbody>
                                <!-- <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-center"> Total </th>
                                        <th class="text-right">{{ totalQuantity('purchase') }}</th>
                                        <th class="text-right">{{ totalQuantity('sold') }}</th>
                                        <th class="text-right">{{ totalQuantity('available') }}</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot> -->
                            </template> 
                            <template #footer>
                                <Pagination 
                                    :pagination="pagination"  
                                    :language="lang"
                                    @onChangePage="setPage" > 
                                </Pagination> 
                            </template> 
                        </Datatable> 


                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title"><i class="fas fa-users"></i> Daily Sales Report</h4>
                        </div>
                        
                        <!-- <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4> -->
                        <Datatable 
                            :columns="productDailySalesColumns" 
                            :sortKey="tableData.sortKey"  
                            @sort="dailySalesSortBy" 
                            v-if="!loading">
                            <template #header > 
                                <div class="tableFilters" style="margin-bottom: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="control" style="float: left;">
                                                <span style="float: left; margin-right: 10px; padding: 7px 0px;">Show </span>
                                                <div class="select" style="float: left;">
                                                    <select class="form-select" v-model="tableData.length" @change="fetchDailySales()"> 
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchDailySales()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >  
                                <tbody v-if="product_daily_sales_items.length > 0">
                                    <tr class="border" v-for="(item, i) in product_daily_sales_items" :key="i">
                                        <td>{{ i + 1 }} </td>
                                        <td>{{ item.created_at }} </td> 
                                        <td>{{ item.products.product_name }} </td> 
                                        <td class="text-center">{{ item.item_quantity }} </td> 
                                        <td class="text-center">{{ item.item_weight ?? '---'}} </td> 
                                        <td class="text-center">{{ item.mrp_price }} </td> 
                                        <td class="text-center">
                                            {{
                                                item.uom == 5
                                                ? parseFloat(
                                                    item.mrp_price * item.item_weight
                                                    ).toFixed(2)
                                                : parseFloat(
                                                    item.mrp_price * item.item_quantity
                                                    ).toFixed(2)
                                            }}
                                        </td> 
                                        <!-- <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end"> -->
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <!-- <a href="javascript:void(0);" class="dropdown-item text-primary" @click="adjustProductStock(item)" title="Adjust Stock">
                                                        <i class="mdi mdi-plus-outline me-1"></i>Adjustment
                                                    </a> -->
                                                    <!-- item-->
                                                <!-- </div>
                                            </div>   
                                        </td> -->
                                    </tr> 
                                </tbody> 
                                <tbody v-else>
                                    <tr>
                                        <td colspan="5"> No Data Available Here!</td>
                                    </tr>
                                </tbody>
                                <!-- <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-center"> Total </th>
                                        <th class="text-right">{{ totalQuantity('purchase') }}</th>
                                        <th class="text-right">{{ totalQuantity('sold') }}</th>
                                        <th class="text-right">{{ totalQuantity('available') }}</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot> -->
                            </template> 
                            <template #footer>
                                <Pagination 
                                    :pagination="daily_sales_pagination"  
                                    :language="lang"
                                    @onChangePage="dailySalesSetPage" > 
                                </Pagination> 
                            </template> 
                        </Datatable>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-lg-7">
                <div class="card" v-if="permission['dashboard-quick-links-view']">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="header-title"><i class="fas fa-link"></i> Quick Links</h4>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <a href="/pos"><button type="button" class="btn btn-outline-info" style="width: 100%;"><i class="fas fa-shopping-cart"></i> POS </button></a>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <a href="/purchase-order"><button type="button" class="btn btn-outline-info" style="width: 100%;"><i class="mdi mdi-cart-plus"></i> Purchase Order</button></a>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <a href="/store-purchase-requisition"><button type="button" class="btn btn-outline-info" style="width: 100%;"><i class="mdi mdi-cart-plus"></i> New Requisition</button></a>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <a href="/stock-management"><button type="button" class="btn btn-outline-success" style="width: 100%;"><i class="fas fa-shopping-cart"></i> Stock Management </button></a>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <a href="/stock-in-out"><button type="button" class="btn btn-outline-success" style="width: 100%;"><i class="fas fa-shopping-cart"></i> Stock In/Out</button></a>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <a href="/stock-transfer"><button type="button" class="btn btn-outline-success" style="width: 100%;"><i class="fas fa-shopping-cart"></i> Stock Transfer</button></a>
                            </div>
                        </div>
                        
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title"><i class="fas fa-shopping-cart"></i> Item Low/Low Stock (0)</h4>
                        </div>
                        
                        <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="header-title"><i class="fa fa-money"></i> Customer Receivables</h4>
                        </div>
                        
                        <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title"><i class="fa fa-money"></i> Supplier Payables</h4>
                        </div>
                        
                        <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="header-title"><i class="fa fa-money"></i> Account</h4>
                        </div>
                        
                        <h4 class="text-center" style="margin-top: 70px;"> Under Construction....</h4>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-5">
                
            </div> <!-- end col-->
        </div>
        <!-- end row -->


    </div>  
</template>
<script type="text/javascript">
import {mapGetters, mapActions} from "vuex"; 
import {ref, onMounted, getCurrentInstance } from "vue";
import Form from "vform";
import axios from "axios";  
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
export default {
    name: "Dashboard",
    components: { 
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
            errors: {},
            btn:'Create',
            items: [],
            counter: {
                customer_count: 0,
                order_count: 0,
                supplier_count: 0,
                product_count: 0,
            }, 
            options: {
                chart: {
                id: 'vuechart-example'
                },
                xaxis: {
                categories: ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
                }
            },
            series: [{
                name: 'series-1',
                data: [30, 40, 45, 50, 49, 60, 70]
            }],

            stock_product_items: [],
            // Stock Product Pagination
            stockProductColumns: [ 
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                }, 
                {
                    label: 'P. Name',
                    name: 'products.product_name',
                    width: '15%'
                },
                {
                    label: 'P. Code',
                    name: 'products.product_code',
                    width: '10%'
                },  
                {
                    label: 'In S.Qty',
                    name: 'in_stock_quantity',
                    width: '5%'
                },
                {
                    label: 'In S.WT/kg',
                    name: 'in_stock_weight',
                    width: '5%'
                },
                {
                    label: 'S. Qty',
                    name: 'out_stock_quantity',
                    width: '5%'
                },
                {
                    label: 'S. WT/kg',
                    name: 'out_stock_weight',
                    width: '5%'
                },
                {
                    label: 'A. Qty',
                    name: 'stock_quantity',
                    width: '5%'
                },
                {
                    label: 'A. WT/kg',
                    name: 'stock_weight',
                    width: '5%'
                },
            ],  
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 1,
                dir: 'desc',
                sortKey: 'product_name', 
                // outlet_id: '',
                // category_id: '',
                // sub_category_id: '',
                // start_date: '',
                // end_date: '',

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

            daily_sales_pagination: {
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

            product_daily_sales_items: [],
            productDailySalesColumns: [ 
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                }, 
                {
                    label: 'Date',
                    name: 'created_at',
                    width: '15%'
                },
                {
                    label: 'P. Name',
                    name: 'products.product_name',
                    width: '15%'
                },
                // {
                //     label: 'P. Code',
                //     name: 'products.product_code',
                //     width: '10%'
                // },  
                {
                    label: 'Qty',
                    name: 'item_quantity',
                    width: '10%'
                },
                {
                    label: 'WT/kg',
                    name: 'item_weight',
                    width: '10%'
                },
                {
                    label: 'MRP',
                    name: 'mrp_price',
                    width: '10%'
                },
                {
                    label: 'Total',
                    name: 'mrp_price',
                    isSearch: false, 
                    isAction: true,
                    width: '10%'
                },
            ],
        }
    }, 
    
    created() {   
        this.checkLogin();
        this.fetchDashboardData();
        this.fetchProductStock();
        this.fetchDailySales();
    },
    methods: {

	    checkLogin: function(e){ 
            const app = getCurrentInstance();
            app.appContext.config.globalProperties.headers = {headers: {
              'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
              'Content-Type': 'multipart/form-data' 
            }};

            app.appContext.config.globalProperties.headerjson = {headers: {
              'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
              'Content-Type': 'application/json' 
            }};
            
	    	if(!this.$store.getters.token){
	      		window.location.href = "/login"   
		    } 

		},

        fetchDashboardData() {
            axios.get(this.apiUrl+'/dashboard', this.headerjson)
            .then((resp) => {
                this.counter = resp.data.data;
                //console.log('counters', resp.data.data);
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        fetchDailySales(url = this.apiUrl+'/dashboard/product_daily_sales') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                this.isSubmit = false;
                this.disabled = false;
                let data = response.data.data.data;
                // if(this.tableData.draw == data.draw) {
                    this.product_daily_sales_items = data.data;
                    this.dailySalesConfigPagination(data);
                // }
            })
            .catch(errors => {
                this.$toast.error(errors.response.data.message);
            })
        },

        dailySalesConfigPagination(data){
            this.daily_sales_pagination.lastPage = data.last_page;
            this.daily_sales_pagination.currentPage = data.current_page;
            this.daily_sales_pagination.total   = data.total ? data.total : 0;
            this.daily_sales_pagination.lastPageUrl = data.last_page_url;
            this.daily_sales_pagination.nextPageUrl = data.next_page_url;
            this.daily_sales_pagination.prevPageUrl = data.prev_page_url;
            this.daily_sales_pagination.from = data.from ? data.from : 0;
            this.daily_sales_pagination.to = data.to ? data.to : 0;  
            this.daily_sales_pagination.links = data.links;
        },

        dailySalesSortBy(key,sortable) {
            this.tableData.sortKey = key;
            //this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.productDailySalesColumns, 'name', key);
            this.tableData.dir = sortable; ///this.sortOrders[key] === 1 ? 'asc' : 'desc'; 
            this.fetchDailySales();
        },
        dailySalesSetPage(data){  
            this.fetchDailySales(data.url); 
        },

        // datatable For Pagination 
        fetchProductStock(url = this.apiUrl+'/dashboard/product_stock') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                this.isSubmit = false;
                this.disabled = false;
                let data = response.data.data.stock_data;
                // if(this.tableData.draw == data.draw) {
                    this.stock_product_items = data.data;
                    this.configPagination(data);
                // }
            })
            .catch(errors => {
                this.$toast.error(errors.response.data.message);
            })
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
            this.tableData.column = this.getIndex(this.stockProductColumns, 'name', key);
            this.tableData.dir = sortable; ///this.sortOrders[key] === 1 ? 'asc' : 'desc'; 
            this.fetchProductStock();
        },
        setPage(data){  
            this.fetchProductStock(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        }
        // datatable For Pagination 
	},
    computed:{
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
        ...mapGetters([
          'userData',
          'token'
        ]), 
    } 
};
	
</script>

<style>
    .product_stock_report .table-responsive {
        padding: 0 !important;
        overflow-x: visible !important;
    }
</style>