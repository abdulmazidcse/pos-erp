<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Low Stock Report</a></li>
                                
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
                            <h3 style="text-align: center;">Low Stock Report</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="category_id"> Category </label>
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
                                        <label for="product_id"> Item </label>
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
                                        <!-- <input type="date" class="form-control" id="to_date" v-model="search_terms.to_date" @change="onkeyPress('to_date')"> -->
                                        <div class="invalid-feedback" v-if="errors.to_date">
                                            {{errors.to_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="">
                                        <button type="submit" class="btn-sm btn btn-primary" :disabled="disabled" @click="filterStockReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                        <a href="javascript:void(0);" style="margin-left: 15px;" class="btn-sm btn btn-primary   " @click.prevent="printItem(item)"  ><i class="mdi mdi-printer-outline me-1"></i>Print</a> 
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
                                                        <select class="form-select" v-model="tableData.length" @change="getStockReport()"> 
                                                            <option value="2" selected="selected">2</option>
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
                                                <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getStockReport()">
                                            </div>
                                        </div>
                                    </div>   
                                </template> 
                                <template #body > 
                                    <tbody  v-for="(item, i) in items" :key="i" v-if="items.length > 0">
                                        <!-- <tr class="border" v-for="(item, i) in items" :key="i"> -->
                                        <tr class="border" v-if="item.stock_quantity <= item.product.alert_quantity" style="color: red;">
                                            <td>{{ i + 1 }} </td>
                                            <td v-if="item.expires_date">{{ item.product.product_name +' ('+ item.product.product_code +' || '+item.expires_date+')' }} </td>
                                            <td v-else>{{ item.product.product_name +' ('+ item.product.product_code +')' }} </td>
                                            <td>{{ item.product.sub_category.name }} </td>
                                            <td class="text-right">{{ item.product.alert_quantity }} </td>
                                            <td class="text-right">{{ item.stock_quantity }} </td>
                                        </tr>

                                        <tr class="border" v-else>
                                            <td>{{ i + 1 }} </td>
                                            <td v-if="item.expires_date">{{ item.product.product_name +' ('+ item.product.product_code +' || '+item.expires_date+')' }} </td>
                                            <td v-else>{{ item.product.product_name +' ('+ item.product.product_code +')' }} </td>
                                            <td>{{ item.product.sub_category.name }} </td>
                                            <td class="text-right">{{ item.product.alert_quantity }} </td>
                                            <td class="text-right">{{ item.stock_quantity }} </td>
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
            
            <Modal @close="toggleModal()"  >
                <div class="modal-content scrollbar-width-thin orderPreview" >
                    <div class="modal-header"> 
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button> 
                    </div>
                    <div class="modal-body " id="printArea" >
                        <div class="table-responsive product_table">
                            <table class="table po_invoice">
                                <tr>
                                    <td colspan="4" class="text-center" style="position: relative;">
                                        <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                        <p>{{ this.retailShopAddress }} </p>
                                        <p>Dhaka, Bangladesh</p> 
                                        <h5 style="width: 100%">Low Stock Report</h5>
                                    </td>
                                    
                                </tr>
                            </table>
                            <table class="  table-bordered table-centered table-nowrap">
                                <thead>
                                    <tr>
                                        <th>SL 	</th>
                                        <th>Item (Code||Expire Date)</th>
                                        <th>Category</th>
                                        <th>Low Stock Qty</th>
                                        <th>Stock Qty</th>
                                    </tr>
                                </thead>
                                <tbody v-for="(item, i) in items" :key="i" v-if="items.length > 0">
                                    <tr v-if="item.stock_quantity <= item.product.alert_quantity" style="background-color: darkgray;">
                                        <td>{{ i + 1 }} </td>
                                        <td v-if="item.expires_date">{{ item.product.product_name +' ('+ item.product.product_code +' || '+item.expires_date+')' }} </td>
                                        <td v-else>{{ item.product.product_name +' ('+ item.product.product_code +')' }} </td>
                                        <td>{{ item.product.sub_category.name }} </td>
                                        <td class="text-right">{{ item.product.alert_quantity }} </td>
                                        <td class="text-right">{{ item.stock_quantity }} </td>
                                    </tr> 
                                    <tr  v-else>
                                        <td>{{ i + 1 }} </td>
                                        <td v-if="item.expires_date">{{ item.product.product_name +' ('+ item.product.product_code +' || '+item.expires_date+')' }} </td>
                                        <td v-else>{{ item.product.product_name +' ('+ item.product.product_code +')' }} </td>
                                        <td>{{ item.product.sub_category.name }} </td>
                                        <td class="text-right">{{ item.product.alert_quantity }} </td>
                                        <td class="text-right">{{ item.stock_quantity }} </td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
            </Modal>   
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
            categories: [],
            category_options: [],
            products: [],
            product_options: [],
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
                    label: 'Item (Code||Expire Date)',
                    name: 'product.product_name',
                    width: '50%'
                },
                {
                    label: 'Category',
                    name: 'product_category.name',
                    width: '20%'
                },
                {
                    label: 'Low Stock Qty',
                    name: 'stock_quantity',
                    width: '15%'
                },
                {
                    label: 'Stock Qty',
                    name: 'stock_quantity',
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
                sortKey: 'product_name', 
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
            },
            printableModalPrintActive:false,
        };
    },
    created() {
        this.fetchProductCategories();
        this.fetchProducts();
        this.getStockReport();
    },

    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        fetchProductCategories() {
            this.category_options = [];
            axios.get(this.apiUrl+"/product_categories", this.headerjson)
            .then((resp) => {
                this.categories = resp.data.data;
                resp.data.data.map((item) => {
                    if(item.parent_id !=0) {
                        this.category_options.push({value: item.id, label: item.name})
                    }
                })
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        fetchProducts() {
            this.product_options = [];
            axios.get(this.apiUrl+"/products", this.headerjson)
            .then((resp) => {
                this.products = resp.data.data;
                resp.data.data.map((item) => {
                    this.product_options.push({value: item.id, label: item.product_name})
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
            this.getStockReport();
        },

        // For Pagination 
        getStockReport(url = this.apiUrl+'/reports/low-stock-report') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers:this.headerparams})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.items = data.data.data;
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
                this.products.map((item) => {
                    this.product_options.push({value: item.id, label: item.product_name})
                })
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
            this.getStockReport();
        },
        setPage(data){  
            this.getStockReport(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        // End Pagination

        changeCategory(category_id) {
            this.tableData.category_id = category_id;
            this.product_options = [];
            this.products.map((item) => {
                if(item.sub_category_id == category_id) {
                    this.product_options.push({value: item.id, label: item.product_name});
                }
            })
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

        togglePrintablePrintModal() {
            this.printableModalPrintActive = !this.printableModalPrintActive;
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
                const response = await axios.get(`${this.apiUrl}/reports/low-stock-report-excel-export${query}`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'low-stock-report.xlsx');
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
    computed: {}
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
</style>