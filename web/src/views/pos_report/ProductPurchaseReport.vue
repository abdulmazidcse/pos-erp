<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Product Purchase Report</a></li>
                                
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
                            <h3 style="text-align: center;">Product Purchase Report</h3>
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
                                        <label for="supplier_id"> Supplier </label> <br>
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="tableData.supplier_id"
                                            placeholder="Select Product"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="supplier_options"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                            @change="changeSupplier($event), onkeyPress('supplier_id')"
                                        />                                         
                                        <div class="invalid-feedback" v-if="errors.supplier_id">
                                            {{errors.supplier_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mt-3">
                                        <button type="submit" class="btn-sm btn btn-primary" :disabled="disabled" @click="filterProductPurchaseReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                          
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mt-3" > 
                                        <a href="javascript:void(0);"   class="btn-sm btn btn-primary" @click.prevent="printItem(item)" ><i class="mdi mdi-printer-outline me-1"></i></a>
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
                            <h4 style="text-align: center;">Product Purchase Report</h4>
                            <h4 style="text-align: center;" v-if="from_date != '' && to_date != ''">From {{ from_date }} TO {{ to_date }}</h4>
                            <h3 v-if="supplier != ''">Supplier: {{ supplier.name }}</h3>
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
                                                        <select class="form-select" v-model="tableData.length" @change="getProductPurchaseReport()"> 
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
                                                <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getProductPurchaseReport()">
                                            </div>
                                        </div>
                                    </div>   
                                </template> 
                                <template #body > 
                                    <tbody v-if="items.length > 0">
                                        <tr class="border" v-for="(item, i) in items" :key="i">
                                            <td class="text-center">{{ i + 1 }} </td>
                                            <td class="text-left">{{ item.purchase_receive.purchase_date }} </td>
                                            <td class="text-left">{{ item.suppliers ? item.suppliers.name : 'N/A' }} </td>
                                            <td class="text-right">{{ item.products.product_name }} </td>
                                            <td class="text-right">{{ Number(item.receive_purchase_price).toFixed(2) }} </td>
                                            <td class="text-right">{{ item.receive_quantity - item.receive_free_quantity }} </td>
                                            <td class="text-right">{{ item.receive_weight - item.receive_free_quantity }} </td>
                                            <!-- <td class="text-right" v-if="checkUnitCode(item.receive_product_unit_id) == 'kg'">{{ item.receive_weight - item.receive_free_quantity }} </td>
                                            <td class="text-right" v-else>{{ item.receive_quantity - item.receive_free_quantity }} </td> -->
                                            <td class="text-right" v-if="checkUnitCode(item.receive_product_unit_id) == 'kg'">{{ (item.receive_weight - item.receive_free_quantity) * item.receive_purchase_price }} </td>
                                            <td class="text-right" v-else>{{ (item.receive_quantity - item.receive_free_quantity) * item.receive_purchase_price }} </td>
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
                                            <th class="text-right">{{ totalQuantity() }}</th>
                                            <th class="text-right">{{ totalWeight() }}</th>
                                            <th class="text-right">{{ totalBalance() }}</th>
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
                                <h3 style="width: 100%">Purchase Order View</h3>
                            </div>
                            <div class="modal-body " id="printArea" >
                                <div class="table-responsive product_table">
                                    <table class="table po_invoice">
                                        <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Product Purchase Report</h4>
                                                <h5 style="text-align: center;" v-if="from_date != '' && to_date != ''">From {{ from_date }} TO {{ to_date }}</h5>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>SL </th>
                                                <th>Date</th>
                                                <th>Supplier</th>
                                                <th>Product</th>
                                                <th>P.Price</th> 
                                                <th>Qty</th> 
                                                <th>Weight</th> 
                                                <th>Amount</th> 
                                            </tr>
                                        </thead>
                                        <tbody v-for="(item, i) in items" :key="i" v-if="items.length > 0">
                                            <tr class="border" v-for="(item, i) in items" :key="i">
                                                <td class="text-center">{{ i + 1 }} </td>
                                                <td class="text-left">{{ item.purchase_receive.purchase_date }} </td>
                                                <td class="text-left">{{ item.suppliers ? item.suppliers.name : 'N/A' }} </td>
                                                <td class="text-right">{{ item.products.product_name }} </td>
                                                <td class="text-right">{{ Number(item.receive_purchase_price).toFixed(2) }} </td>
                                                <td class="text-right">{{ item.receive_quantity - item.receive_free_quantity }} </td>
                                                <td class="text-right">{{ item.receive_weight - item.receive_free_quantity }} </td> 
                                                <td class="text-right" v-if="checkUnitCode(item.receive_product_unit_id) == 'kg'">{{ parseFloat(Number((item.receive_weight - item.receive_free_quantity) * item.receive_purchase_price )).toFixed(2)}} </td>
                                                <td class="text-right" v-else>{{ parseFloat(Number((item.receive_quantity - item.receive_free_quantity) * item.receive_purchase_price)).toFixed(2) }} </td>
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
            loading: true,
            downloading: false,
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            suppliers: [],
            supplier_options: [],
            products: [],
            product_options: [],
            units: [],
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 
            from_date: '',
            to_date: '',
            supplier: '',
            columns: [       
                {
                    label: 'SL',
                    name: '',           
                    width: '5%'
                },   
                {
                    label: 'Date',
                    name: 'purchase_date',
                    width: '10%'
                },
                {
                    label: 'Supplier',
                    name: 'supplier_name',
                    width: '20%'
                },
                {
                    label: 'Product',
                    name: 'product_name',
                    width: '20%'
                },
                {
                    label: 'P.Price',
                    name: 'receive_purchase_price',
                    width: '10%'
                },
                {
                    label: 'Qty',
                    name: 'receive_quantity',
                    width: '10%'
                },
                {
                    label: 'Weight',
                    name: 'receive_weight',
                    width: '10%'
                },
                {
                    label: 'Amount',
                    name: 'receive_purchase_price',
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
                supplier_id: '',
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
        this.fetchUnits();
        this.fetchProducts();
        this.fetchSuppliers();
        this.getProductPurchaseReport();
    },

    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
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
            var sum = 0;
            this.items.filter((item) => {
                if(this.checkUnitCode(item.receive_product_unit_id) != 'kg') {
                    sum += parseFloat(item.receive_quantity);
                }else{
                    sum += parseFloat(item.receive_quantity);
                }
            });

            return sum.toFixed(2);
        },

        totalWeight(type="") {
            var sum = 0;
            this.items.filter((item) => {
                if(this.checkUnitCode(item.receive_product_unit_id) == 'kg') {
                    sum += parseFloat(item.receive_weight);
                }else{
                    sum += 0;
                }
            });

            return sum.toFixed(2);
        },

        totalBalance(type="") {
            var sum = 0;
            this.items.filter((item) => {
                if(this.checkUnitCode(item.receive_product_unit_id) == 'kg') {
                    sum += parseFloat(item.receive_weight * item.receive_purchase_price);
                }else{
                    sum += parseFloat(item.receive_quantity * item.receive_purchase_price);
                }
            });

            return sum.toFixed(2);
        },

        fetchSuppliers() {
            this.supplier_options = [];
            axios.get(this.apiUrl+"/suppliers", this.headerjson)
            .then((resp) => {
                this.suppliers = resp.data.data;
                resp.data.data.map((item) => {
                    if(item.status == 1) {
                        this.supplier_options.push({value: item.id, label: item.name})
                    }
                })
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        changeSupplier: function(supplier_id) {
            this.tableData.supplier_id = supplier_id;
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

        fetchUnits() {
            axios.get(this.apiUrl+"/units", this.headerjson)
            .then((resp) => {
                this.units = resp.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        changeProduct: function(product_id) {
            this.tableData.product_id = product_id
        },

        filterProductPurchaseReport()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.getProductPurchaseReport();
        },

        // For Pagination 
        getProductPurchaseReport(url = this.apiUrl+'/reports/product-purchase-report') {
            this.tableData.draw++;
            let headers_data = {
                      'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                      'Content-Type': 'application/json' 
                    };
            axios.get(url, {params:this.tableData, headers:headers_data})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.items = data.data.data;
                    this.from_date  = data.from_date;
                    this.to_date  = data.to_date;
                    this.supplier  = data.supplier;
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
            this.getProductPurchaseReport();
        },
        setPage(data){  
            this.getProductPurchaseReport(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        // End Pagination

        checkRequiredPrimary()
        {
            if((this.tableData.from_date != '' && this.tableData.to_date != '') || this.tableData.supplier_id != "" || this.tableData.product_id != "") {
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
                const response = await axios.get(`${this.apiUrl}/reports/product-purchase-report-excel-export${query}`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'product-purchase-report.xlsx');
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

/* .filterMultiSelect .multiselect {
    overflow: hidden;
} */
</style>