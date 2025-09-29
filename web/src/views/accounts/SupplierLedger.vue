<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Accounts </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Supplier Ledger</a></li>
                                
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
                <div class="col-12"> 
                    <div class="col-md-10">
                        <div class="row">  
                            <div class="col-md-6">
                                <div class="">
                                    <label for="outlet_id"> Company </label> 
                                    <select class="form-control" @change="fetchSuppliers($event.target.value)">
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
                        <div class="card-header">
                            <h3 style="text-align: center;">Supplier Ledger</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="supplier_id"> Supplier *</label>
                                        <select class="form-control" id="supplier_id" v-model="search_terms.supplier_id" @change="onkeyPress('supplier_id')">
                                            <option value="">--- Select Supplier ---</option>
                                            <option v-for="(supplier, i) in suppliers" :key="i" :value="supplier.id">{{ supplier.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.supplier_id">
                                            {{errors.supplier_id[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="from_date"> From Date *</label>
                                        <input type="date" class="form-control" id="from_date" v-model="search_terms.from_date" @change="onkeyPress('from_date')">
                                        <div class="invalid-feedback" v-if="errors.from_date">
                                            {{errors.from_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="">
                                        <label for="to_date"> To Date *</label>
                                        <input type="date" class="form-control" id="to_date" v-model="search_terms.to_date" @change="onkeyPress('to_date')">
                                        <div class="invalid-feedback" v-if="errors.to_date">
                                            {{errors.to_date[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mt-3">
                                        <button type="submit" class="btn-sm btn btn-primary" :disabled="disabled" @click="fetchSupplierLedgers()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
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
                        <div class="card-header" v-if="items.length > 0">
                            <h4 style="text-align: center;">{{ supplier_name }}</h4>
                            <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>
                        </div>
                        <div class="card-body">
                            <h4>Opening Balance: {{ opening_balance }}</h4>
                            <table id="basic-datatable" class="table table-striped dt-responsive nowarp w-100" v-if="!loading">
                                <thead class="tableFloatingHeaderOriginal">
                                    <tr class="success item-head">
                                        <th width="5%" style="text-align: center">SL </th>
                                        <th width="10%" style="text-align: center">Date </th>
                                        <th width="15%" style="text-align: center">Opening Balance</th>
                                        <th width="10%" style="text-align: center">PR. Amount</th>
                                        <th width="10%" style="text-align: center">Payment </th>
                                        <th width="10%" style="text-align: center">Return</th>
                                        <th width="10%" style="text-align: center">Discount</th>
                                        <th width="15%" style="text-align: center">Closing Balance</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="" v-for="(item, index) in items" :key="index">
                                        <td style="text-align: center">{{ index + 1 }} </td>
                                        <td style="text-align: center">{{ item.transaction_date }} </td>
                                        <td style="text-align: center">{{ item.opening_balance }} </td> 
                                        <td style="text-align: center">{{ item.purchase_receive_amount }} </td> 
                                        <td style="text-align: center">{{ item.payment_amount }} </td> 
                                        <td style="text-align: center">{{ item.return_amount }}</td>
                                        <td style="text-align: center">{{ item.discount_amount }}</td>
                                        <td style="text-align: center">{{ item.closing_balance }}</td>
                                    </tr>
                                </tbody>
                            </table>

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
                                        <tbody>
                                        <tr>
                                            <td colspan="2" class="text-center" style="position: relative;">
                                                <h5 class="text-uppercase">{{ this.retailShopName }}</h5>
                                                <p>{{ this.retailShopAddress }} </p>
                                                <p>Dhaka, Bangladesh</p> 
                                                <h4 style="text-align: center;">Supplier Ledger</h4>
                                                <h4 style="text-align: center;">{{ supplier_name }}</h4>
                                                <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>  
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                        <thead class="tableFloatingHeaderOriginal">
                                            <tr class="success item-head">
                                                <th width="5%" style="text-align: center">SL </th>
                                                <th width="10%" style="text-align: center">Date </th>
                                                <th width="15%" style="text-align: center">Opening Balance</th>
                                                <th width="10%" style="text-align: center">PR. Amount</th>
                                                <th width="10%" style="text-align: center">Payment </th>
                                                <th width="10%" style="text-align: center">Return</th>
                                                <th width="10%" style="text-align: center">Discount</th>
                                                <th width="15%" style="text-align: center">Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="items.length > 0">
                                            <tr class="" v-for="(item, index) in items" :key="index">
                                                <td style="text-align: center">{{ index + 1 }} </td>
                                                <td style="text-align: center">{{ item.transaction_date }} </td>
                                                <td style="text-align: center">{{ item.opening_balance }} </td> 
                                                <td style="text-align: center">{{ item.purchase_receive_amount }} </td> 
                                                <td style="text-align: center">{{ item.payment_amount }} </td> 
                                                <td style="text-align: center">{{ item.return_amount }}</td>
                                                <td style="text-align: center">{{ item.discount_amount }}</td>
                                                <td style="text-align: center">{{ item.closing_balance }}</td>
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import axios from 'axios';
import Form from 'vform';

export default {
    name: 'PosLeftbar',
    components: {
        Modal
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
            suppliers: [],
            companies:[],
            search_terms: new Form({
                from_date: '',
                to_date: '',
                supplier_id: '',
            }),
            opening_balance: 0,
            supplier_name: '',
            from_date: '',
            to_date: ''
        };
    },
    created() {
        // this.fetchProductExpiredReport();
        // this.fetchSupplierLedgers();
        this.fetchCompanies();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },
        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
                if(this.companies.length == 1){
                    this.fetchSuppliers(this.companies[0].id); 
                }
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        fetchSuppliers(companyId) {
            axios.get(this.apiUrl+'/suppliers?company_id='+companyId, this.headerjson)
            .then((resp) => {
                this.suppliers = resp.data.data;
            })
            .catch((err) => {
                console.log(err);
            })
        },

        filterData() { 
            this.fetchSupplierLedgers();
        },
        
        fetchSupplierLedgers() {
            this.loading = true;
            this.isSubmit = true;
            this.disabled = true;
            let fromData = new FormData();
            fromData.append('supplier_id', this.search_terms.supplier_id);
            fromData.append('from_date', this.search_terms.from_date);
            fromData.append('to_date', this.search_terms.to_date);

            axios.post(this.apiUrl+'/accounts/supplier-ledger', fromData, this.headers)
            .then((res) => {
                this.loading = false;
                this.isSubmit = false;
                this.disabled = true;
                // this.search_terms.reset();
                this.items = res.data.data.supplier_ledgers;
                this.opening_balance = res.data.data.opening_balance;
                this.from_date = res.data.data.from_date;
                this.to_date = res.data.data.to_date;
                this.supplier_name = res.data.data.supplier_name;
            })
            .catch((err) => { 
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            }).finally((ress) => {
                this.loading = false;
            });
        },
        checkRequiredPrimary()
        {
            if(this.search_terms.supplier_id && this.search_terms.from_date && this.search_terms.to_date) {
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
            if (!this.search_terms.supplier_id) {
                this.$toast.error('Please select supplier');
                return;
            }
            let fromData = new FormData();
            fromData.append('supplier_id', this.search_terms.supplier_id);
            fromData.append('from_date', this.search_terms.from_date);
            fromData.append('to_date', this.search_terms.to_date);

            this.downloading = true; 

            const query = `supplier_id=${this.search_terms.supplier_id}&start_date=${this.search_terms.from_date}&end_date=${this.search_terms.to_date}`;
            try {
                // axios.post(this.apiUrl+'/accounts/supplier-ledger', fromData, this.headers)
                const response = await axios.post(`${this.apiUrl}/supplierLedgersExport?`, fromData, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'supplier_ledgers.xlsx');
                document.body.appendChild(link);
                link.click();
                this.downloading = false;
            } catch (error) { 
                this.downloading = false;
            }
        }


    }, 
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