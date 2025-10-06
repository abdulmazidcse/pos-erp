<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <h4 style="margin: 0; padding: 8px 0 1.5rem 0;">Inventory Board</h4>
                        </div>
                        <div class="page-title-right float-right">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Inventory </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory Board</a></li>
                                
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
                            <div class="row" >
                                <div class="col-md-4">
                                    <div class="">
                                        <label for="outlet_id"> Outlet *</label>
                                        <select class="form-control" id="outlet_id" v-model="search_terms.outlet_id" @change="onkeyPress('outlet_id')">
                                            <option value="">--- Select Outlet ---</option>
                                            <option v-for="(outlet, i) in outlets" :key="i" :value="outlet.id">{{ outlet.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.outlet_id">
                                            {{errors.outlet_id[0]}}
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

                                <div class="col-md-2" style="padding-right: 0 !important; text-align: right;">
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary" :disabled="disabled" @click="fetchInventoryBoardData()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span>Submit 
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-centered table-nowarp w-100" v-if="!loading">
                                <thead class="table-light">
                                    <tr class="border success item-head">
                                        <th width="5%" style="text-align: center">SL </th>
                                        <th width="15%" style="text-align: center">Outlet </th>
                                        <th width="20%" style="text-align: center">Product Name</th>
                                        <!-- <th width="15%" style="text-align: center">Available Quantity <br><span>(Automatic Update)</span></th> -->
                                        <th width="15%" style="text-align: center">Available Quantity </th>
                                        <th width="15%" style="text-align: center">Re-Order Quantity <br><span>(Average Daily Sale)</span></th>
                                        <th width="15%" style="text-align: center">Order Quantity <br><span>(Average Daily Sale)</span></th>
                                        <th width="10%" style="text-align: center">Order Date</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, index) in items" :key="index">
                                        <td style="text-align: center">{{ index + 1 }} </td>
                                        <td style="text-align: center">{{ item.outlet_name }} </td>
                                        <td style="text-align: center">{{ item.product_name }} </td> 
                                        <td style="text-align: center">{{ item.stock_quantity }} </td> 
                                        <td style="text-align: center">{{ (parseInt(item.average_sale_quantity) * parseInt(item.total_days)) }} </td> 
                                        <!-- <td style="text-align: center">{{ item.order_quantity }}</td> -->
                                        <td style="text-align: center">{{ ((parseInt(item.average_sale_quantity) * parseInt(item.total_days)) - item.stock_quantity) }}</td>
                                        <td style="text-align: center">{{ item.order_date }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr class="border" v-for="(item, index) in items" :key="index">
                                        <td colspan="7" style="text-align: center">Data Not Available </td>
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
            loading: true,
            isSubmit: false,
            disabled: false,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
            outlets: [],
            search_terms: new Form({
                outlet_id: '',
                from_date: '',
                to_date: ''
            })
        };
    },
    created() {
        this.fetchInventoryBoardData();
        this.fetchOutlet();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },
        
        fetchInventoryBoardData() {
            this.isSubmit = true;
            this.disabled = true;
            var formData = new FormData();
            formData.append('outlet_id', this.search_terms.outlet_id);
            formData.append('from_date', this.search_terms.from_date);
            formData.append('to_date', this.search_terms.to_date);
            axios.post(this.apiUrl+'/inventory-board', formData, this.headers)
            .then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                this.search_terms.reset();
                this.items = res.data.data;

            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
                this.loading = false;
            });
        },

        fetchOutlet() {
            axios.get(this.apiUrl+"/outlets", this.headerjson) 
            .then((resp) => {
                this.outlets = resp.data.data;
            })
            .catch((err) => {
                console.log("error", err.response);
            })
        },

        // checkRequiredPrimary()
        // {
        //     if(this.search_terms.supplier_id && this.search_terms.from_date && this.search_terms.to_date) {
        //         this.disabled = false;
        //     }else{
        //         this.disabled = true;
        //     }
        // },
        onkeyPress: function(field) { 
            // this.checkRequiredPrimary();
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
    computed: {}
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
</style>