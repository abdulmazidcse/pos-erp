<template>
    <transition>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                      <ol class="breadcrumb m-0"> 
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Sales</a></li>
                          <li class="breadcrumb-item active">Sale Return List </li> 
                      </ol>
                  </div>
                  <div class="page-title-right float-right">
                      <!-- <button type="button" class="btn btn-primary float-right" @click="onFilter">
                        
                      </button>
                      <button type="button" class="btn btn-primary float-right" @click="toggleModal">
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
                                    <td>{{ item.id}} </td> 
                                    <td>{{ item.sale ? item.sale.invoice_number : ''}} </td> 
                                    <td>{{ item.return_item}}</td>
                                    <td>{{ item.return_item_qty}}</td>
                                    <td>{{ item.created_at}}</td>
                                    <td>{{ item.return_amount}} </td>
                                    <td>{{ item.return_reason }}</td>   
                                    <td><span class="badge bg-danger" v-if="item.return_type ==3">Void</span>
                                        <span class="badge bg-success" v-else-if="item.return_type ==2">Replace</span>
                                        <span class="badge bg-warning" v-else>Return</span></td>
                                    <td>
                                      <div class="dropdown float-end">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item--> 
                                            <a href="javascript:void(0);" class="dropdown-item text-info" @click="viewInfo(item)">
                                            <i class="mdi mdi-circle-edit-outline me-1"></i>Add to POS</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
      </div> 
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal"; 
import jldatatable from "./../../jl-datatable"; 
import Form from 'vform'   
import axios from 'axios'; 
import Buttons from '@/components/Buttons.vue'; 
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
export default {
    name: 'POS Sales',
    components: { 
      Modal,  
      Buttons,
      jldatatable,
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
            items: [],
            loading:true, 
            sortKey: 'name',  
            holdItems:[],
            form: new Form({
                id: '', 
                start_date:'',
                end_date:'',
                customer_id:'', 
            }), 
            multiclasses:{ 
              clear: '',
              clearIcon: '', 
            }, 
            columns: [  
                {
                    label: 'OrderID',
                    name: 'id',           
                    width: '5%'
                },   
                {
                    label: 'Invoice Number',
                    name: 'created_at',
                    width: '15%'
                },
                {
                    label: 'R.Items',
                    name: 'return_item',
                    width: '10%'
                },
                {
                    label: 'R.Qty',
                    name: 'return_item_qty',
                    width: '10%'
                },
                {
                    label: 'Date',
                    name: 'created_at',
                    width: '10%'
                },
                {
                    label: 'Return Amount',
                    name: 'return_amount',
                    width: '15%'
                },
                {
                    label: 'Return Reason',
                    name: 'return_reason',
                    width: '15%'
                },  
                {
                    label: 'Return Type',
                    name: 'return_type',
                    width: '10%'
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
                sortKey: 'sale_id', 
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
        viewInfo(item){

        },
        forceRerender() {
          this.componentKey += 1;  
          console.log("Force Update Done");
        },
        onEntriesFetched(){
          console.log('onEntriesFetched');
        }, 
        handleEditBtn(event){ 
          console.log('datatable', this.datatable)
          let trID = event.target.parentElement.parentElement.parentElement.parentElement.id 
        },
        deleteItem(item){ 
            console.log('item',item)
          //let trID = event.target.parentElement.parentElement.parentElement.parentElement.id
          this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/hold_sales/'+item.id, this.headers).then(res => {
                        if(res.status == 200){  
                          this.fetchItems();
                          this.$toast.success(res.data.message); 
                        }else{
                          this.$toast.error(res.data.message);
                        } 
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                } 
            }); 
        },
        filtering(){ 
          console.log('=======gettingEntries'); 
        },
        // datatable For Pagination 
        fetchItems(url = this.apiUrl+'/sale_return/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers:this.headerparams})
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
    computed:{
        ...mapGetters([
          'productItems',
          'cartItems', 
          'cartTotal',
          'cartQuantity'
        ]), 
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
  .multiselect-tag.is-user {
    padding: 5px 8px;
    border-radius: 22px;
    background: #35495e;
    margin: 3px 3px 8px;
  }

  .multiselect-tag.is-user img {
    width: 18px;
    border-radius: 50%;
    height: 18px;
    margin-right: 8px;
    border: 2px solid #ffffffbf;
  }

  .multiselect-tag.is-user i:before {
    color: #ffffff;
    border-radius: 50%;;
  }

  .user-image {
    margin: 0 6px 0 0;
    border-radius: 50%;
    height: 22px;
  }
  .multiselect-clear { 
    display: inline-block !important;
    float: right !important;;
  }
  .multiselect { 
    display: block;
    position: relative; 
  }
  .multiselect.is-active{
    z-index: 1;
  }
</style>
<style src="@vueform/multiselect/themes/default.css"></style>