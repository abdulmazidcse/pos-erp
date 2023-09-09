<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Product Sales Report</a></li>
                                
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
                            <h3 style="text-align: center;">Product Sales Report</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="">
                                        <label for="from_date"> From </label>
                                        <input type="date" class="form-control" id="from_date" v-model="tableData.from_date" @change="onkeyPress('from_date')">
                                        <div class="invalid-feedback" v-if="errors.from_date">
                                            {{errors.from_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="">
                                        <label for="to_date"> To </label>
                                        <input type="date" class="form-control" id="to_date" v-model="tableData.to_date" @change="onkeyPress('to_date')">
                                        <div class="invalid-feedback" v-if="errors.to_date">
                                            {{errors.to_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="filterMultiSelect">
                                        <label for="to_date"> Product </label> <br>
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="tableData.product_id"
                                            placeholder="Select Product"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="product_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            @change="changeProduct($event), onkeyPress('product_id')"
                                        />                                          
                                        <div class="invalid-feedback" v-if="errors.product_id">
                                            {{errors.product_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 ">
                                    <div class="filterMultiSelect">
                                        <label for="customer_id"> Customer </label> <br>
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="tableData.customer_id"
                                            placeholder="Select Product"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="customer_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            @change="changeCustomer($event), onkeyPress('customer_id')"
                                        />                                         
                                        <div class="invalid-feedback" v-if="errors.customer_id">
                                            {{errors.customer_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-3">
                                        <button type="submit" class="btn-sm btn btn-primary" :disabled="disabled" @click="filterProductSaleReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button> 
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mt-3"> 
                                        <a href="javascript:void(0);" style="margin-left: 5px;" class="btn-sm btn btn-primary" @click.prevent="printItem(item)" ><i class="mdi mdi-printer-outline me-1"></i> </a>
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
                        <!-- <div class="card-header" v-if="items.length > 0">
                        </div> -->
                        <div class="card-body">
                            <h4 style="text-align: center;">Product Sale Report</h4>
                            <h4 style="text-align: center;" v-if="from_date != '' && to_date != ''">From {{ from_date }} TO {{ to_date }}</h4>
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
                                                        <select class="form-select" v-model="tableData.length" @change="getProductSaleReport()"> 
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
                                                <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getProductSaleReport()">
                                            </div>
                                        </div>
                                    </div>   
                                </template> 
                                <template #body > 
                                    <tbody v-if="items.length > 0">
                                        <tr class="border" v-for="(item, i) in items" :key="i">
                                            <td class="text-center">{{ i + 1 }} </td>
                                            <td class="text-left">{{ item.sales.created_at }} </td>
                                            <td class="text-left">{{ item.sales ? item.sales.customer_name : 'N/A' }} </td>
                                            <td class="text-left">{{ item.products.product_name }} </td>
                                            <td class="text-right">{{ parseFloat(item.mrp_price).toFixed(2) }} </td>
                                            <td class="text-right">{{ parseFloat(item.quantity).toFixed(2) }} || {{ (item.weight) ? parseFloat(item.weight).toFixed(3) : '0.000' }} </td>
                                            <td class="text-right" v-if="(item.weight)" > {{ '0.00' }} || {{ parseFloat(parseFloat(item.weight) * parseFloat(item.mrp_price)).toFixed(2) }} </td>
                                            <td class="text-right" v-else>{{ parseFloat(parseFloat(item.quantity) * parseFloat(item.mrp_price)).toFixed(2) }} || {{ '0.00' }}</td>
                                            <!-- <td class="text-right">{{ item.users }} </td> -->
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
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="3"> No Data Available Here!</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-center"> Total</th>
                                            <th class="text-right">{{ totalSalesQty.toFixed(2) }} || {{ totalSalesWt.toFixed(3) }}</th>
                                            <th class="text-right">{{ totalSalesQtyAmount.toFixed(2) }} || {{ totalSalesWtAmount.toFixed(2) }}</th>
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

                    <Modal @close="toggleModal()"  >
                        <div class="modal-content scrollbar-width-thin orderPreview" >
                            <div class="modal-header"> 
                                <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                                <h3 style="width: 100%">Product Sales Report</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Product Sales Report</h4>
                                                <h5 style="text-align: center;" v-if="from_date != '' && to_date != ''">From {{ from_date }} TO {{ to_date }}</h5>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>SL </th>
                                                <th>Date</th> 
                                                <th>Customer Name</th> 
                                                <th>Product Name</th> 
                                                <th>S.Price</th> 
                                                <th>Qty || Wt </th> 
                                                <th>Amount </th> 
                                            </tr>
                                        </thead>
                                        <tbody v-if="items.length > 0">

                                            <tr class="border" v-for="(item, i) in items" :key="i">
                                                <td class="text-center">{{ i + 1 }} </td>
                                                <td class="text-left">{{ item.sales.created_at }} </td>
                                                <td class="text-left">{{ item.sales ? item.sales.customer_name : 'N/A' }} </td>
                                                <td class="text-left">{{ item.products.product_name }} </td>
                                                <td class="text-right">{{ parseFloat(item.mrp_price).toFixed(2) }} </td>
                                                <td class="text-right">{{ parseFloat(item.quantity).toFixed(2) }} || {{ (item.weight) ? parseFloat(item.weight).toFixed(3) : '0.000' }} </td>
                                                <td class="text-right" v-if="(item.weight)" > {{ '0.00' }} || {{ parseFloat(parseFloat(item.weight) * parseFloat(item.mrp_price)).toFixed(2) }} </td>
                                                <td class="text-right" v-else>{{ parseFloat(parseFloat(item.quantity) * parseFloat(item.mrp_price)).toFixed(2) }} || {{ '0.00' }}</td>
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
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';
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
            loading: false,
            downloading: false,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            customers: [],
            customer_options: [],
            products: [],
            product_options: [],
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 
            from_date: '',
            to_date: '',
            columns: [       
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                },   
                {
                    label: 'Date',
                    name: 'sales_date',
                    width: '10%'
                },
                {
                    label: 'Customer',
                    name: 'customer_name',
                    width: '20%'
                },
                {
                    label: 'Product',
                    name: 'product_name',
                    width: '20%'
                },
                {
                    label: 'S.Price',
                    name: 'mrp_price',
                    width: '10%'
                },
                {
                    label: 'Qty || Wt',
                    name: 'quantity',
                    width: '10%'
                },
                {
                    label: 'Amount',
                    name: 'amount',
                    width: '10%'
                },
                // {
                //     label: 'Purchased By',
                //     name: 'purchase_by',
                //     width: '10%'
                // },
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
                dir: 'desc',
                sortKey: 'reference_no', 
                product_id: '',
                customer_id: '',
                from_date: '',
                to_date: '',
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
        this.fetchProducts();
        this.fetchCustomers();
        this.getProductSaleReport();
    },

    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        fetchCustomers() {
            this.customer_options = [];
            axios.get(this.apiUrl+"/customers", this.headerjson)
            .then((resp) => {
                this.customers = resp.data.data;
                resp.data.data.map((item) => {
                    if(item.status == 1) {
                        this.customer_options.push({value: item.id, label: item.name})
                    }
                })
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        changeCustomer: function(customer_id) {
            this.tableData.customer_id = customer_id;
        },

        fetchProducts() {
            this.product_options = [];
            axios.get(this.apiUrl+"/products", this.headerjson)
            .then((resp) => {
                this.products = resp.data.data;
                resp.data.data.map((item) => {
                    this.product_options.push({value: item.id, label: item.product_name})
                });
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        changeProduct: function(product_id) {
            this.tableData.product_id = product_id
        },

        filterProductSaleReport()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.getProductSaleReport();
        },

        // For Pagination 
        getProductSaleReport(url = this.apiUrl+'/reports/product-sales-report') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.items = data.data.data;
                    this.from_date  = data.from_date;
                    this.to_date  = data.to_date;
                    this.configPagination(data.data);
                }
                this.isSubmit  = false;
                this.disabled = false;
                // this.tableData.from_date  = '';
                // this.tableData.to_date   = '';

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
            this.getProductSaleReport();
        },
        setPage(data){  
            this.getProductSaleReport(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        // End Pagination

        checkRequiredPrimary()
        {
            if((this.tableData.from_date != '' && this.tableData.to_date != '') || this.tableData.customer_id != "" || this.tableData.product_id != "") {
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
            const queryString = this.objectToQueryString( this.tableData); 
            this.downloading = true; 

            const query = queryString;
            try {
                const response = await axios.get(`${this.apiUrl}/reports/product-sales-report-excel-export${query}`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Product-Sales-Report.xlsx');
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
    computed: {
        totalSalesQty() {
            return this.items.reduce(function(total, item) {
                return total + parseFloat(item.quantity);
            }, 0);
        },
        totalSalesWt() {
            return this.items.reduce(function(total, item) {
                if(item.weight) {
                    return total + parseFloat(item.weight);
                }else{
                    return total + 0;
                }
                
            }, 0);
        },
        totalSalesQtyAmount() {
            return this.items.reduce(function(total, item) {
                if(!item.weight){
                    return total + parseFloat(parseFloat(item.quantity) * parseFloat(item.mrp_price));
                }else{
                    return total + 0;
                }
            }, 0);
        },
        totalSalesWtAmount() {
            return this.items.reduce(function(total, item) {
                if(item.weight){
                    return total + parseFloat(parseFloat(item.weight) * parseFloat(item.mrp_price));
                }else{
                    return total + 0;
                }
            }, 0);
        },
    }
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
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

/* .filterMultiSelect .multiselect {
    overflow: hidden;
} */
</style>