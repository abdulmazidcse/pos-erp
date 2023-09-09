<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Sales </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Sales Posting</a></li>
                                
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
                            <h3 style="text-align: center;">Sales Posting List</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="">
                                        <label for="outlet_id"> Outlet *</label> <br>
                                        <Multiselect
                                            class="form-control border outlet_id"
                                            mode="single"
                                            v-model="search_terms.outlet_id"
                                            placeholder="Select Outlet"
                                            @change="onkeyPress('outlet_id')"
                                            :searchable="true"
                                            :filter-results="true"
                                            :options="outlet_options"
                                            :classes="multiclasses"
                                            :close-on-select="true"
                                            :min-chars="1"
                                            :resolve-on-load="false"
                                        />
                                        <div class="invalid-feedback" v-if="errors.outlet_id">
                                            {{errors.outlet_id[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="">
                                        <label for="sales_type"> Sales Type *</label>
                                        <select class="form-control" id="sales_type" v-model="search_terms.sales_type" @change="onkeyPress('sales_type')">
                                            <option value="all">All</option>
                                            <option value="cash">Cash</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="bkash">Bkash</option>
                                            <option value="nagad">Nagad</option>
                                            <option value="rocket">Rocket</option>
                                            <option value="redeem_point">Redeem Point</option>
                                            <option value="gift_card">Gift Card</option>
                                            <option value="credit_sale">Credit Sale</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.sales_type">
                                            {{errors.sales_type[0]}}
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
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="fetchSalesPostingList()">
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
                        <div class="card-header" v-if="Object.keys(items).length > 0">
                            <!-- <h4 style="text-align: center;">{{ sales_type_name }}</h4> -->
                            <h4 style="text-align: center;">From {{ from_date }} TO {{ to_date }}</h4>

                            <button type="button" class="btn btn-success float-right" :disabled="postDisabled" @click="salePostListSubmit()">
                                <span v-show="isPostSubmit">
                                    <i class="fas fa-spinner fa-spin" ></i>
                                </span>Post 
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="posting-list" class="table table-bordered table-centered w-100" v-if="!loading">
                                <thead class="table-light">
                                    <tr class="success item-head">
                                        <th width="5%" style="text-align: center">SL </th>
                                        <th width="25%" style="text-align: center">Name </th>
                                        <th width="10%" style="text-align: center">ActCodeDr</th>
                                        <th width="10%" style="text-align: center">ActCodeCr </th>
                                        <th width="10%" style="text-align: center">Amount</th>
                                        <th width="10%" style="text-align: center">Sale Date</th>
                                        <th width="10%" style="text-align: center">Com.%</th>
                                        <th width="10%" style="text-align: center">ActCodeCharge</th>
                                        <th width="10%" style="text-align: center">Cr.Crd.Com</th>
                                    </tr>
                                </thead>
                                <tbody v-if="Object.keys(items).length > 0">
                                    <tr class="" v-for="(item, keyIndex, index) in items" :key="index">
                                        <td style="text-align: center">{{ index + 1 }} </td>
                                        <td style="text-align: center">{{ item.name }} </td>
                                        <td style="text-align: center">{{ item.debit_ledger_code }} </td> 
                                        <td style="text-align: center">{{ item.credit_ledger_code }} </td> 
                                        <td style="text-align: center">{{ item.amount }}</td>
                                        <td style="text-align: center">{{ item.sale_date }}</td>
                                        <td style="text-align: center">{{ item.commission_percent }}</td>
                                        <td style="text-align: center">{{ item.charge_ledger_code }}</td>
                                        <td style="text-align: center">{{ item.commission_amount }}</td>
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

                        <div class="card-footer" v-if="Object.keys(items).length > 0">
                            <button type="button" class="btn btn-success float-right" :disabled="postDisabled" @click="salePostListSubmit()">
                                <span v-show="isPostSubmit">
                                    <i class="fas fa-spinner fa-spin" ></i>
                                </span>Post 
                            </button>
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

export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: false,
            isSubmit: false,
            isPostSubmit: false,
            disabled: true,
            postDisabled: false,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            items_asc: [],
            sales_ids: [],
            payment_ids: [],
            outlets: [],
            outlet_options: [],
            search_terms: new Form({
                from_date: '',
                to_date: '',
                sales_type: 'all',
                outlet_id: "",
            }),
            opening_balance: 0,
            sales_type_name: '',
            from_date: '',
            to_date: '',
            
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },
        };
    },
    created() {
        this.fetchOutlets();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
        },

        fetchOutlets() {
            axios.get(this.apiUrl+'/outlets', this.headerjson)
            .then((res) => { 
                this.outlets = res.data.data;
                this.outlet_options = res.data.data.map((item) => {
                    return {label: item.name, value: item.id};
                });
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            }); 
        },

        filterData() { 
            this.fetchSalesPostingList();
        },
        
        fetchSalesPostingList() {
            this.loading = true;
            this.isSubmit = true;
            this.disabled = true;
            let fromData = new FormData();
            fromData.append('outlet_id', this.search_terms.outlet_id);
            fromData.append('sales_type', this.search_terms.sales_type);
            fromData.append('from_date', this.search_terms.from_date);
            fromData.append('to_date', this.search_terms.to_date);

            axios.post(this.apiUrl+'/sales/sales-posting-list', fromData, this.headers)
            .then((res) => {
                this.loading = false;
                this.isSubmit = false;
                this.disabled = false;
                this.search_terms.reset();

                this.items = res.data.data.sales_posting_data_desc;
                this.items_asc = res.data.data.sales_posting_data_asc;
                this.sales_ids = res.data.data.sales_ids;
                this.payment_ids = res.data.data.payment_ids;
                this.from_date = res.data.data.from_date;
                this.to_date = res.data.data.to_date;
                // this.sales_type_name = res.data.data.sales_type_name;
            })
            .catch((err) => { 
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }

                this.items = [];
                this.items_asc = [];
                this.sales_ids = [];
                this.payment_ids = [];
                this.from_date = "";
                this.to_date = "";

            }).finally((ress) => {
                this.loading = false;
            });
        },
        checkRequiredPrimary()
        {
            if(this.search_terms.outlet_id && this.search_terms.sales_type && this.search_terms.from_date && this.search_terms.to_date) {
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


        salePostListSubmit: function() {
            this.isPostSubmit = true;
            this.postDisabled = true;

            var formData    = new FormData();
            formData.append("post_items", JSON.stringify(this.items_asc));
            formData.append("sales_ids", JSON.stringify(this.sales_ids));
            formData.append("payment_ids", JSON.stringify(this.payment_ids));

            axios.post(this.apiUrl+'/accounts/sales-posting', formData, this.headers)
            .then((res) => {
                this.isPostSubmit = false;
                this.postDisabled = false;

                console.log(res);
                
                this.$toast.success(res.data.message);

                this.items = [];
                this.items_asc = [];
                this.sales_ids = [];
                this.payment_ids = [];
                this.from_date = "";
                this.to_date = "";
            })
            .catch((err) => { 
                this.isPostSubmit = false;
                this.postDisabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            }).finally((ress) => {

            });
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