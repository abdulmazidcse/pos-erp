<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Reports </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Collection Report</a></li>
                                
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
                            <h3 style="text-align: center;">Collection Report</h3>
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

                                <div class="col-md-3 ">
                                    <div class="filterMultiSelect">
                                        <label for="customer_id"> Customer </label> <br>
                                        <Multiselect 
                                            class="form-control border" 
                                            mode="single"
                                            v-model="tableData.customer_id"
                                            placeholder="Select Customer"
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
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="filterSalesReport()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <!-- <div class="card-header" v-if="items.length > 0">
                        </div> -->
                        <div class="card-body">
                            <h4 style="text-align: center;">Collection Report</h4>
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
                                                        <select class="form-select" v-model="tableData.length" @change="getSalesReport()"> 
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
                                            <td class="text-left">{{ item.created_at }} </td>
                                            <td class="text-left">{{ item.sales.invoice_number }} </td>
                                            <td class="text-left">{{ item.sales.customer_name ?? 'N/A' }} </td>
                                            <td class="text-left">{{ item.paying_by ?? 'N/A' }} </td>
                                            <td class="text-right">{{ parseFloat(item.amount).toFixed(2) }} </td> 
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="6"> No Data Available Here!</td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-center"> Total</th>
                                            <th class="text-right">{{ totalCollectionAmount.toFixed(2) }}</th> 
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
            isSubmit: false,
            disabled: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            customers: [],
            customer_options: [],
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 
            from_date: '',
            to_date: '',
            columns: [       
                {
                    label: 'SL',
                    name: 'id',           
                    width: '5%'
                },   
                {
                    label: 'Date',
                    name: 'created_at',
                    width: '10%'
                },
                {
                    label: 'Reference No',
                    name: 'invoice_number',
                    width: '20%'
                },
                {
                    label: 'Customer',
                    name: 'customer_name',
                    width: '20%'
                },  
                {
                    label: 'Paying by',
                    name: 'paying_by',
                    width: '20%'
                },  
                {
                    label: 'Amount',
                    name: 'amount',
                    width: '10%'
                }, 
            ],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc',
                sortKey: 'reference_no',
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
        this.fetchCustomers();
        this.getSalesReport();
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

        // fetchProducts() {
        //     this.product_options = [];
        //     axios.get(this.apiUrl+"/products", this.headerjson)
        //     .then((resp) => {
        //         this.products = resp.data.data;
        //         resp.data.data.map((item) => {
        //             this.product_options.push({value: item.id, label: item.product_name})
        //         });
        //     })
        //     .catch((err) => {
        //         this.$toast.error(err.response.data.message);
        //     })
        // },

        // changeProduct: function(product_id) {
        //     this.tableData.product_id = product_id
        // },

        filterSalesReport()
        {
            this.isSubmit = true;
            this.disabled = true;
            this.getSalesReport();
        },

        // For Pagination 
        getSalesReport(url = this.apiUrl+'/reports/collection-report') {
            //this.loading =true;
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
            this.getSalesReport();
        },
        setPage(data){  
            this.getSalesReport(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        // End Pagination

        checkRequiredPrimary()
        {
            if((this.tableData.from_date != '' && this.tableData.to_date != '') || this.tableData.customer_id != "" ) {
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


    },

    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        totalCollectionAmount() {
            return this.items.reduce(function(total, item) {
                return total + parseFloat(item.amount);
            }, 0);
        } 
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