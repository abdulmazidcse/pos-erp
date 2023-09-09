<template>
    <transition  >
    <div class="container-fluid ">
        <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Customer </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Customer ledger</a></li>
                            </ol>
                        </div> 
                    </div>
                </div>
        </div>  
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 style="text-align: center;">Customer Ledger</h3>
                    </div>
                    <div class="card-body">                             
                        <div class="tab-pane show active">
                            <div class="row"> 
                                <div class="col-md-3">
                                    <div class="form-group  ">
                                        <div class="mb-2"> 
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control border" id="start_date" v-model="form.start_date"> 
                                        </div>  
                                    </div>   
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group  ">
                                        <div class="mb-2"> 
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control border" id="end_date" v-model="form.end_date">
                                        </div>  
                                        </div>   
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group  ">
                                        <div class="mb-2"> 
                                            <label for="customer_id" class="customer_id">Customers</label><br>
                                            <Multiselect 
                                            class="form-control border customer_id" 
                                            mode="single"
                                            v-model="form.customer_id"
                                            placeholder="Customer"  
                                            @change="selectCustomer($event)"
                                            :searchable="true" 
                                            :filter-results="true"
                                            :options="customers"
                                            :classes="multiclasses"
                                            :close-on-select="true" 
                                            :min-chars="1"
                                            :resolve-on-load="false" 
                                        /> 
                                        </div>  
                                        </div>   
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group  ">
                                        <div class="mb-1 mt-3">
                                            <button class="btn btn-sm btn-primary" @click="handleSubmit"> <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit</button>   
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
                    </div>
                </div>


                <div class="card"> 
                    <div class="card-header" v-if="items.length > 0">
                        <h4 style="text-align: center;">{{ customer_name }}</h4>
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
                                    <th width="10%" style="text-align: center">Sales Amount</th>
                                    <th width="10%" style="text-align: center">Receive Amount </th>
                                    <th width="15%" style="text-align: center">Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="" v-for="(item, index) in items" :key="index">
                                    <td style="text-align: center">{{ index + 1 }} </td>
                                    <td style="text-align: center">{{ item.transaction_date }} </td>
                                    <td style="text-align: center">{{ item.opening_balance }} </td> 
                                    <td style="text-align: center">{{ item.sales_amount }} </td> 
                                    <td style="text-align: center">{{ item.payment_receive_amount }} </td> 
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
                    <!-- print area-->
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
                                                <h4 style="text-align: center;">Customer Ledger</h4>
                                                <h4 style="text-align: center;">{{ customer_name }}</h4>
                                                <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>  
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;" class="table-bordered table-centered table-nowrap">
                                        <thead class="tableFloatingHeaderOriginal">
                                            <tr class="success item-head">
                                                <th width="5%" style="text-align: center">SL </th>
                                                <th width="10%" style="text-align: center">Date </th>
                                                <th width="15%" style="text-align: center">Opening Balance</th>
                                                <th width="10%" style="text-align: center">Debit </th>
                                                <th width="10%" style="text-align: center">Credit </th>
                                                <th width="15%" style="text-align: center">Closing Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="items.length > 0">
                                            <tr class="" v-for="(item, index) in items" :key="index">
                                                <td style="text-align: center">{{ index + 1 }} </td>
                                                <td style="text-align: center">{{ item.transaction_date }} </td>
                                                <td style="text-align: center">{{ item.opening_balance }} </td> 
                                                <td style="text-align: center">{{ item.sales_amount }} </td> 
                                                <td style="text-align: center">{{ item.payment_receive_amount }} </td> 
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
    </div>
    </transition>
</template>
<script>
// const today = new Date();
// const yyyy = today.getFullYear();
// let mm = today.getMonth() + 1; // Months start at 0!
// let dd = today.getDate();

// const formattedToday = dd + '/' + mm + '/' + yyyy;
// var strdate = document.getElementById('start_date').value = formattedToday;
// console.log('strdate', formattedToday)

import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'Customer',
    components: {
        Modal
    },
    data() {
        return { 
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            disabled_upload: false,
            modalActive:false,
            importModal: false,
            errors: {},
            btn:'Create',
            items: [],
            customers: [], 
            customer: '', 
            loading: false, 
            downloading: false, 
            form: new Form({
                id: '', 
                start_date:'',
                end_date:'',
                customer_id:'', 
            }), 
            opening_balance: 0,
            customer_name: '',
            from_date: '',
            to_date: '',

            multiclasses:{ 
              clear: '',
              clearIcon: '', 
            } 
        };
    },
    created() {  
        this.fetchCustomers(); 
    },
    methods: {  
        selectCustomer(event) {  
          this.customer = this.customers.find(e => e.value == event);   
        },
        fetchCustomers(){
          axios.get(this.apiUrl+'/customers',this.headerjson)
          .then((res) => { 
            this.customers = res.data.data.map(({ id, name, discount_percent, customer_group_discount, available_point }) => (
              { label: name, value: id, discount: discount_percent, group_discount: customer_group_discount, points: available_point }
              )); 
          });
        },  
        handleSubmit: function(e){ 
            this.loading = true; 
            this.item='';
            if(!this.form.customer_id){
                this.$toast.error('Please select customer');
                this.loading = false; 
                return false;
            }
            let query = 'customer_id='+this.form.customer_id+'&start_date='+this.form.start_date+'&end_date='+this.form.end_date;
            axios.get(this.apiUrl+'/customer_ledgers?'+query,this.headers)
              .then((res) => { 
                this.loading = false; 
                // this.form.reset();
                this.items = res.data.data.customer_ledgers;
                this.opening_balance = res.data.data.opening_balance;
                this.from_date = res.data.data.from_date;
                this.to_date = res.data.data.to_date;
                this.customer_name = res.data.data.customer_name;
            });            
        }, 


        onkeyPress: function(field) { 
            this.checkImportRequiredPrimary();
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
            if (!this.form.customer_id) {
                this.$toast.error('Please select a customer');
                return;
            }

            this.downloading = true; 

            const query = `customer_id=${this.form.customer_id}&start_date=${this.form.start_date}&end_date=${this.form.end_date}`;
            try {
                const response = await axios.get(`${this.apiUrl}/customerLedgersExport?${query}`, {
                responseType: 'blob', // Important: set the response type to 'blob'
                headers: {
                    'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
                    Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // Specify the expected content type
                }
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'customer_ledgers.xlsx');
                document.body.appendChild(link);
                link.click();
                this.downloading = false;
            } catch (error) { 
                this.downloading = false;
            }
        }
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
    width: 900px
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
</style>