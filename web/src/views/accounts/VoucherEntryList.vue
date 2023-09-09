<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Accounts </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Voucher Entry List</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" 
                                v-for="(entry_type, et) in entry_types" 
                                :key="et" 
                                :class="(entry_type.label == 'receipt') ? 'float-right btn btn-primary' : 
                                (entry_type.label == 'payment') ? 'float-right btn btn-success' : 
                                (entry_type.label == 'journal') ? 'float-right btn btn-info' : 
                                (entry_type.label == 'contra') ? 'float-right btn btn-dark' : 'float-right btn btn-light'"
                                style="margin-left: 5px;"
                                @click="redirectRoute(entry_type.label)"    
                            >
                              <i class="mdi mdi-arrow-right-thin"></i> {{ entry_type.name }}
                            </button> 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="voucher_list_table">

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
                                                                <select class="form-select" v-model="tableData.length" @change="getVoucherList()">  
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
                                                        <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getVoucherList()">
                                                    </div>
                                                </div>
                                            </div>   
                                        </template> 
                                        <template #body > 
                                            <tbody v-if="vitems.length > 0">
                                                <tr class="border" v-for="(item, i) in vitems" :key="i">
                                                    <td>{{ i + 1 }} </td>
                                                    <td>{{ item.vdate }} </td>
                                                    <td>{{ item.vcode }} </td>
                                                    <td>{{ item.vtype_value }}</td>
                                                    <td>{{ item.global_note }} </td>
                                                    <td>
                                                        <div class="dropdown float-end">
                                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="mdi mdi-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <!-- item-->
                                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click="viewItem(item)"><i class="mdi mdi-eye-outline me-1"></i>View</a>
                                                                <a href="javascript:void(0);" class="dropdown-item text-primary" @click="printItem(item)"><i class="mdi mdi-printer-outline me-1"></i>Print</a>
                                                                <a v-if="item.modified_item == 2" href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                                <a v-if="item.modified_item != 0" href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
                                                            </div>
                                                        </div> 
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tbody v-else>
                                                <tr>
                                                    <td colspan="6"> No Data Available Here!</td>
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

                                </div>                      
                            </div>
                        </div>

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

                <!-- Modal For View-->
                <Modal @close="toggleVoucherViewModal()" :modalActive="voucherModalActive">
                    <div class="modal-content scrollbar-width-thin orderPreview">
                        <div class="modal-header" style="text-align:right; display: block;"> 
                            <button @click="toggleVoucherViewModal()" type="button" class="btn btn-default">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="company_info">
                                                <h4 style="text-transform: uppercase; text-align: center;">Twenty Four 7</h4>
                                                <h5 style="text-transform: uppercase; text-align: center;">UNCEP CHEYNE TOwer, Segunbagicha, Dhaka</h5>

                                                <h3 style="text-align: center;">{{ vitem.vtype_name }} Voucher</h3>
                                            </div>
                                            <table class="table voucher_view">
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <table style="width: 100%">
                                                                    <tr>
                                                                        <td><b>Voucher Code</b></td>
                                                                        <td>:</td>
                                                                        <td>{{ vitem.vcode }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><b>Voucher Type</b></td>
                                                                        <td>:</td>
                                                                        <td>{{ vitem.vtype_name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-4">
                                                                <table style="width: 100%">
                                                                    <tr style="text-align: right !important">
                                                                        <td><b>Voucher Date</b></td>
                                                                        <td>:</td>
                                                                        <td>{{ vitem.vdate }}</td>
                                                                    </tr>
                                                                    <tr style="text-align: right !important">
                                                                        <td><b>Print Date</b></td>
                                                                        <td>:</td>
                                                                        <td>{{ vitem.print_date }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">
                                                        <table class="table table-bordered table-centered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Ledger Head</th>
                                                                    <th class="text-center">Code</th>
                                                                    <th class="text-center">Debit Amount</th>
                                                                    <th class="text-center">Credit Amount</th>
                                                                    <th class="text-center">Line Note</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <tr v-for="(ledger_item, i) in vitem.ledger_items" :key="i">
                                                                    <td class="text-left">{{ ledger_item.ledger_head }}</td>
                                                                    <td class="text-left">{{ ledger_item.ledger_code }}</td>
                                                                    <td class="text-right">{{ ledger_item.debit_amount }}</td>
                                                                    <td class="text-right">{{ ledger_item.credit_amount }}</td>
                                                                    <td class="text-center">
                                                                        <textarea class="form-control" v-model="ledger_item.line_note" readonly></textarea>
                                                                    </td>
                                                                </tr>
                                                                <tr style="font-weight: bold;">
                                                                    <td colspan="2" class="text-right">Total</td>
                                                                    <td class="text-right text-bold">{{ Number(vitem.total_debit_amount).toFixed(4) }}</td>
                                                                    <td class="text-right text-bold">{{ Number(vitem.total_credit_amount).toFixed(4) }}</td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td colspan="8">
                                                        <p><b>Narration/Cheque Details: &nbsp;</b> {{ vitem.global_note }}</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </Modal>

                <!-- Modal For Print-->
                <Modal @close="toggleVoucherPrintModal()" :modalActive="voucherModalPrintActive">
                    <div class="modal-content scrollbar-width-thin orderPreview">
                        <div class="modal-header" style="text-align:right; display: block;"> 
                            <button @click="toggleVoucherPrintModal()" type="button" class="btn btn-default">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="container" id="printArea">
                                                <p class="text-uppercase text-center mt-2"><strong>Twenty Four 7</strong></p>
                                                <p class="text-uppercase text-center mt-2">3rd floor, UCEP Cheyne Tower, 25 Segun Bagicha Rd, Dhaka 1000</p><br>
                                                <hr class="m-zero">
                                                <!-- <p class="text-center m-0 font-italic"><strong>Cash Received Voucher (Approval)</strong></p> -->
                                                <p class="text-center m-0 font-italic"><strong>{{ vitem.vtype_name }} Voucher (Approval)</strong></p>
                                                <hr class="m-zero">
                                                <div class="d-flex justify-content-between mt-fourty">
                                                    <div>
                                                        <div class="d-flex">
                                                            <p class="mr-fourty"><strong>Voucher#:</strong></p>
                                                        <p>{{ vitem.vcode }}</p>
                                                        </div>
                                                        <div class="d-flex">
                                                            <p class="mr-fourty"><strong>Description:</strong></p>
                                                            <p>{{ vitem.global_note }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <p class="mr-fourty"><strong>Date:</strong></p>
                                                        <p>{{ vitem.vdate }}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <table class="table table-hover mt-3">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <td style="width: 15%">Amount Code</td>
                                                                <td style="width: 50%">Amount Hand</td>
                                                                <td style="width: 15%">Cost Center</td>
                                                                <td style="width: 10%" class="text-center">Debit</td>
                                                                <td style="width: 10%" class="text-center">Credit</td>
                                                            </tr>
                                                            <tr v-for="(ledger_item, i) in vitem.ledger_items" :key="i">
                                                                <td>{{ ledger_item.ledger_code }}</td>
                                                                <td>{{ ledger_item.ledger_head }}</td>
                                                                <td>{{ ledger_item.cost_center_name }}</td>
                                                                <td style="text-align: right">{{ Number(ledger_item.debit_amount).toFixed(2) }}</td>
                                                                <td style="text-align: right">{{ Number(ledger_item.credit_amount).toFixed(2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-center">Total Transaction</td>
                                                                <td style="text-align: right">{{ Number(vitem.total_debit_amount).toFixed(2) }}</td>
                                                                <td style="text-align: right">{{ Number(vitem.total_credit_amount).toFixed(2) }}</td>
                                                            </tr>
                                                        </tbody>
                                                       
                                                    </table>
                                                </div>
                                                <!-- <div class="my-5 d-flex">
                                                    <p class="mr-fourty">Taka in word:</p>
                                                    <number-to-word :number="Number(vitem.total_debit_amount).toFixed(2)" /> -->
                                                    <!-- <p><strong>Twelve Thousand One Hundred Seventy Only</strong></p> -->
                                                <!-- </div> -->
                                                <!-- <div class="d-flex justify-content-between align-item-center">
                                                    <div class="text-center col-xs-4">
                                                        <p class="m-zero">Khandakar Kudrat E Khuda</p>
                                                        <hr class="m-zero">
                                                        <p><strong>Prepare By</strong></p>
                                                    </div>
                                                    <div class="text-center col-xs-4">
                                                        <p class="m-zero">&nbsp;</p>
                                                        <hr class="m-zero">
                                                        <p><strong>Checked By</strong></p>
                                                    </div>
                                                    <div class="text-center col-xs-4">
                                                        <p class="m-zero">Khandakar Kudrat E Khuda</p>
                                                        <hr class="m-zero">
                                                        <p><strong>Approved By</strong></p>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </Modal> 
            </div>     
            
            
            
        </div>

        
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import axios from 'axios';
import Form from 'vform';
import NumberToWord from './../page/NumberToWord.vue';           
import '@/assets/css/print.css';       
export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        Datatable,
        Pagination,
        NumberToWord
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
            voucherModalActive:false,
            voucherModalPrintActive:false,
            errors: {},
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
            entry_types: [],
            vitems: [],
            vitem: '',
            columns: [       
                {
                    label: 'SL',
                    name: '',
                    isSearch: false,
                    isAction: true,           
                    width: '5%'
                },   
                {
                    label: 'Date',
                    name: 'vdate',           
                    width: '10%'
                },   
                {
                    label: 'V. Code',
                    name: 'vcode',
                    width: '15%'
                },
                {
                    label: 'V. Type',
                    name: 'vtype_value',
                    width: '15%'
                },
                {
                    label: 'Note',
                    name: 'global_note',
                    width: '15%'
                },
                {
                    label: 'Actions',            
                    name: '',
                    isSearch: false,
                    isAction: true,
                    width: '5%'

                }
            ],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 1,
                dir: 'desc',
                sortKey: 'vdate',
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
        this.fetchEntryTypes();
        this.getVoucherList();
    },
    methods: { 

        redirectRoute: function(type) {
            if(type == 'receipt') {
                this.$router.push('/accounting/voucher-entry?type=receipt');
            }else if(type == 'payment') {
                this.$router.push('/accounting/voucher-entry?type=payment');
            }else if(type == 'journal') {
                this.$router.push('/accounting/voucher-entry?type=journal');
            }else if(type == 'contra') {
                this.$router.push('/accounting/voucher-entry?type=contra');
            }else if(type == 'bank') {
                this.$router.push('/accounting/voucher-entry?type=bank');
            }else{
                this.$router.push('/accounting/voucher-entry');
            }
        },

        toggleVoucherViewModal() {

            this.voucherModalActive = !this.voucherModalActive;

        },

        toggleVoucherPrintModal() {

            this.voucherModalPrintActive = !this.voucherModalPrintActive;

        },

        fetchEntryTypes() {
            axios.get(this.apiUrl+'/entry_types', this.headerjson)
            .then((resp) => {
                this.entry_types = resp.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        fetchVoucherData() {
            axios.get(this.apiUrl+'/account_vouchers', this.headerjson)
            .then((resp) => {
                //
            })
            .catch((err) => {
                console.log(err);
            })
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },

        viewItem: function(item) {

            axios.get(this.apiUrl+'/account_vouchers/'+item.id, this.headerjson)
            .then((resp) => {
                this.vitem = resp.data.data;
                this.toggleVoucherViewModal();
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            });
        },

        printItem: function (item) {
            axios.get(this.apiUrl+'/account_vouchers/'+item.id, this.headerjson)
            .then((resp) => {
                this.vitem = resp.data.data;
                // this.toggleVoucherPrintModal();
                this.printContent('printArea');
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            });
        },

        printContent(document_id) {
            const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [
                    // 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                    '/assets/css/app.min.css',
                    this.baseUrlPrintCSS+'/assets/css/bootstrap.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            this.$htmlToPaper(document_id, options);
        },

        printModal() {

            var css_link = this.baseUrl+'/assets/css/print.css';

            console.log("css link===", css_link);

            // Create a reference to the print-friendly template
            const printContent = document.getElementById("printArea");

            // Create a new window to print the template
            const printWindow = window.open("", "Print Window", "height=600,width=800");

            // Write the template to the print window
            printWindow.document.write('<html><head><title></title>');
            printWindow.document.write('<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">');
            // printWindow.document.write('<link rel="stylesheet" type="text/css" href="../assets/print.css">');
            printWindow.document.write('<link rel="stylesheet" type="text/css" href="'+this.baseUrl+'/assets/css/print.css">');
            printWindow.document.write('</head><body >');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.write('</body></html>');

            // Print the window and close it
            printWindow.print();
            printWindow.close();
        },
        

        edit: function(item) {
            this.$router.push('/accounting/voucher-edit/'+item.id);
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
                    // console.log("dsfkjskd", this.pagination.currentPage);
                    axios.delete(this.apiUrl+'/account_vouchers/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.getVoucherList();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        } 
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message);
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors 
                        }
                    }) 
                }
            }); 
        },

        // For Pagination 
        getVoucherList(url = this.apiUrl+'/account_vouchers/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers: this.headerparams})
            .then((response) => {
                let data = response.data.data;
                if(this.tableData.draw = data.draw) {
                    this.vitems = data.data.data;
                    this.configPagination(data.data);
                }
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
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = sortable;
            this.getVoucherList();
        },
        setPage(data){  
            this.getVoucherList(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
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
    border: none !important;
    width: 70vw;
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


/** Print css */


.m-zero{
    margin: 0px;
}

.mr-fourty{
    margin-right: 40px;
}

.mt-fourty{
    margin-top: 40px;
}

.font-italic{
    font-weight: 400;
    font-style: italic;
}

@media print {
    .m-zero{
        margin: 0px;
    }

    .mr-fourty{
        margin-right: 40px;
    }

    .mt-fourty{
        margin-top: 40px;
    }

    .font-italic{
        font-weight: 400;
        font-style: italic;
    }
}

</style>