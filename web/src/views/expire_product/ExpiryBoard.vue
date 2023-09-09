<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <h4 style="margin: 0; padding: 8px 0 1.5rem 0;">Expiry Board</h4>
                        </div>
                        <div class="page-title-right float-right">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Product </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Product Expiry Board</a></li>
                                
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
                        <!-- <div class="card-header">
                            <h4 style="text-align: center;">Expiry Board</h4>
                        </div> -->
                        <div class="card-body">
                            <table class="table table-bordered table-centerd table-nowarp w-100" v-if="!loading">
                                <thead class="table-light">
                                    <tr class="border success item-head">
                                        <th width="5%" style="text-align: center">SL </th>
                                        <th width="15%" style="text-align: center">Outlet </th>
                                        <th width="20%" style="text-align: center">Product Name</th>
                                        <th width="10%" style="text-align: center">Qty</th>
                                        <th width="15%" style="text-align: center">Available days till Expiry</th>
                                        <th width="10%" style="text-align: center">Vendor Policy</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, index) in items" :key="index">
                                        <td style="text-align: center">{{ index + 1 }} </td>
                                        <td style="text-align: center">{{ item.outlet_name }} </td>
                                        <td style="text-align: center">{{ item.product_name }} </td> 
                                        <td style="text-align: center">{{ item.stock_quantity }} </td> 
                                        <td style="text-align: center">{{ item.available_days }} </td> 
                                        <td style="text-align: center">{{ item.return_policy }}</td>
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

export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            showModal: false,
            modalActive:false,
            errors: {},
            items: [],
        };
    },
    created() {
        this.fetchProductExpiredReport();
    },
    methods: { 

        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            this.errors = '';
            this.isSubmit = false;
            console.log('then',this.isSubmit)
        },

        fetchProductExpiredReport() { 
            axios.get(this.apiUrl+'/product-expired-board', this.headerjson)
            .then((res) => {
                this.items = res.data.data;
                //console.log('companies res',res.data.data);
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
                this.loading = false;
            });
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