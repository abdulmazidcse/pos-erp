<template>
    <transition>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                      <ol class="breadcrumb m-0"> 
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Return</a></li>
                          <li class="breadcrumb-item active">Purchase Return List </li> 
                      </ol>
                  </div>
                  <div class="page-title-right float-right">
                        <button type="button" class="btn btn-primary float-right" @click="redirectRoute()">
                            Purchase Return
                        </button> 
                      <!--<button type="button" class="btn btn-primary float-right" @click="toggleModal">
                          Add New
                      </button>  -->
                  </div>
              </div>
          </div>
        </div>
        <div class="row">
            <div class="col-12">
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
                                                  <select class="form-select" v-model="tableData.length" @change="fetchItems()">  
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
                                          <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchItems()">
                                      </div>
                                  </div>
                              </div>   
                          </template> 
                          <template #body >   
                              <tbody v-if="items.length > 0">
                                  <tr class="border" v-for="(item, i) in items" :key="i">
                                    <td>{{ i + 1 }} </td> 
                                    <td>{{ item.reference_no }} </td>
                                    <td>{{ item.return_date }}</td>
                                    <td>{{ item.supplier_name }} </td>
                                    <td>{{ item.total_return_quantity }}</td> 
                                    <td>{{ item.total_return_amount }}</td>
                                    <td>
                                      <div class="dropdown float-end">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <a href="#" @click="purchaseReturnView(item)" class="dropdown-item text-info"><i class="mdi mdi-eye"></i> View</a> 
                                            <!-- <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)">
                                            <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> -->
                                            <!-- item-->
                                            <!-- <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a> -->
                                        </div>
                                    </div>   
                                    </td>
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
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> 

        <!-- Modal For View -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    <h2 style="width: 100%; padding-left: 30vw;">Purchase Return View</h2>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Product Details -->
                            <div class="card">
                                <div class="card-body">
                                    <div style="padding: 0 15px;">
                                        <div class="purchase_return_details">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Reference No: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.reference_no }} </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Date: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.return_date }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Supplier: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.supplier_name }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Note: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.note }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Return Quantity: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.total_return_quantity }} </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <label class="col-md-4 text-left text-bold">Return Amount: </label>
                                                        <div class="col-md-7 text-left"> {{ purchase_return.total_return_amount }} </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h4> Product Details</h4>
                                    </div>
                                    <div class="table-responsive product_table">
                                        <table class="table table-bordered table-centered table-nowrap">
                                            <thead class="table-light">
                                                <tr class="success item-head">
                                                    <th class="text-center">SL </th> 
                                                    <th class="text-center">Name </th> 
                                                    <th class="text-center">Barcode</th> 
                                                    <th class="text-center">UOM</th>
                                                    <th class="text-center">Exp. Date</th>
                                                    <th class="text-center">Purchase Price</th>
                                                    <th class="text-center">Re. Qty </th>
                                                    <th class="text-center">Amount</th>
                                                </tr>
                                            </thead>

                                            <tbody v-if="purchase_return_products.length > 0">
                                                <tr v-for="(product_item, i) in purchase_return_products" :key="i">
                                                    <td class="text-center">{{ i + 1 }}</td>
                                                    <td>{{ product_item.product_name }}</td>
                                                    <td class="text-center">{{ product_item.product_code }}</td>
                                                    <td class="text-center">{{ product_item.product_unit_code }}</td>
                                                    <td class="text-center">{{ product_item.product_expire_date }}</td>
                                                    <td class="text-center">{{ product_item.return_purchase_price }}</td>
                                                    <td class="text-center">{{ product_item.return_quantity }}</td>
                                                    <td class="text-center">{{ product_item.return_quantity * product_item.return_purchase_price }}</td>
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>

                                    <div class="summation_details">
                                        
                                        <!-- <span class="float-right text-danger">Total Amount: <strong>{{ totalAmount }}</strong></span> -->
                                        <!-- <span class="float-right text-danger" style="margin-right: 10px;">Approve Total Amount: <strong>{{ approveTotalAmount }}</strong></span> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                    <!-- <div class="modal-footer"> 
                        
                    </div> -->
            </div>
        </Modal>

      </div> 
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal"; 
import Form from 'vform'   
import axios from 'axios';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
export default {
    name: 'POS Sales',
    components: { 
      Modal,
      Datatable,
      Pagination
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
            modalActive: false,
            purchase_return: {},
            purchase_return_products: [],
            items: [],
            loading:true, 
            sortKey: 'reference_no',
            multiclasses:{ 
              clear: '',
              clearIcon: '', 
            }, 
            columns: [  
                {
                    label: 'SL',           
                    name: '',
                    isSearch: false, 
                    isAction: true,
                    width: '5%',
                },   
                {
                    label: 'Reference No',
                    name: 'reference_no',
                    width: '15%'
                },
                {
                    label: 'Date',
                    name: 'return_date',
                    width: '10%'
                },
                {
                    label: 'Supplier Name',
                    name: 'supplier_name',
                    width: '15%'
                },
                {
                    label: 'Total Return Qty',
                    name: 'total_return_quantity',
                    width: '15%'
                },  
                {
                    label: 'Total Return Amount',
                    name: 'total_return_amount',
                    width: '15%'
                },  
                {
                    label: 'Actions',            
                    name: '',
                    isSearch: false, 
                    isAction: true,
                    width: '5%',

                }
            ],  
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc',
                sortKey: 'reference_no', 
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
            isLoading:true,
            componentKey: 0,
        };
    },  
    methods: { 
        toggleModal()
        {
            this.modalActive = !this.modalActive;
            if(!this.modalActive){
                this.purchase_return = {};
                this.purchase_return_products = [];
            } 
        },

        redirectRoute() {
            this.$router.push('/purchase-return');
        },

        forceRerender() {
          this.componentKey += 1;  
          console.log("Force Update Done");
        },
       
        // For Purchase Return View 
        purchaseReturnView: function(item) {
            axios.get(this.apiUrl+'/return/purchase_return/'+item.id, this.headerjson)
            .then((res) => {
                this.purchase_return = res.data.data.prr_data;
                this.purchase_return_products = res.data.data.prr_products;
                this.toggleModal();
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        // datatable For Pagination 
        fetchItems(url = this.apiUrl+'/return/purchase_return_list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData,  headers:this.headerparams})
            .then((response) => {
                let data = response.data.data;  
                if(this.tableData.draw = data.draw) { 
                    this.items = data.data.data;
                    this.configPagination(data.data);
                }
            })
            .catch(errors => {
                console.log(errors);
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
            this.fetchItems();
        },
        setPage(data){  
            this.fetchItems(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        }
        // datatable For Pagination 
    },
    
    async created() {  
      this.fetchItems(); 
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
};
</script>


<style scoped>

.table.table-responsive {
    min-height: auto !important;
}
.btn-file {
  overflow: hidden;
  position: relative;
  vertical-align: middle;
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
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

/*.svg-inline--fa.fa-random {
  margin: -33px 0px 0px 103px !important;
  background-color: #ccc;
  padding: 9px;
  z-index: 100;
  display: inline-block;
  position: absolute;
  border-radius: 0px 3px 3px 0px;
}*/

  .user-image {
    margin: 0 6px 0 0;
    border-radius: 50%;
    height: 22px;
  }

</style>