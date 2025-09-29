<template>
    <transition>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                      <ol class="breadcrumb m-0"> 
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Sales</a></li>
                          <li class="breadcrumb-item active">Due Sale List </li> 
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
                                  <tr class="border" v-for="(item, i) in items" v-if="items.length > 0">
                                    <td>{{ item.id}} </td> 
                                    <td>{{ item.invoice_number }} </td>
                                    <td>{{ item.created_at}}</td>
                                    <td>{{ item.customer_name}} </td>
                                    <td>{{ item.total_amount }}</td> 
                                    <td>{{ item.grand_total }}</td> 
                                    <td>{{ item.collection_amount }}</td>
                                    <td>{{ item.sales_items_count }}</td>
                                    <td>
                                      <div class="dropdown float-end">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <a href="#" @click="findInvoice(item.invoice_number)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a> 
                                            <!-- <a href="#" @click="paymentCollection(item)" class="dropdown-item text-info"><i class="mdi mdi-currency-bdt"></i> Payment</a>    -->
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

        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div> 
                <div class="modal-body">
                    <div class="row">
                          
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary " :disabled="disabled">
                        <span v-show="isSubmit">
                            <i class="fas fa-spinner fa-spin" ></i>
                        </span>{{btn}} 
                    </button>
                </div> 
            </div>
        </Modal>  

        <Modal @close="invoiceModal()" :modalActive="invoiceModalActive">
          <div class="modal-content scrollbar-width-thin invoice-modal">
            <div class="modal-header">
              <h5>Invoice</h5>
              <button @click="invoiceModal()" type="button" class="btn btn-default">
                X
              </button>
            </div>
            <div class="modal-body" id="printMe">
              <div id="invoice-POS" v-if="invoice_info">
                <div id="top">
                  <div class="row">
                    <p class="text-uppercase text-center mt-2"><strong> {{ $store.getters.userData.user.outlet_name }}</strong></p>
                    <p class="text-uppercase text-center mt-2">{{ $store.getters.userData.user.outlet_address }}</p><br> 
                    <p>
                      <span class="float-left">{{
                        invoice_info.invoice_number
                      }}</span>
                      <span class="float-right">{{ invoice_info.created_at }}</span>
                    </p>
                  </div>
                </div>
                <!--End InvoiceTop-->
                <div id="mid">
                  <div class="info">
                    <p class="text-left" style="font-size: 14px">
                      Name: {{ invoice_info.customer.name }}<br />
                      Address: {{ invoice_info.customer.address }} <br />
                      Mobile: {{ invoice_info.customer.phone }}<br />
                      Srvd by: {{ invoice_info.created_by.name }}
                    </p>
                  </div>
                </div>
                <!--End Invoice Mid-->
                <div id="bot">
                  <div id="table">
                    <table>
                      <tr class="tabletitle borderTop borderBottom">
                        <td class="item"><h2>Item Name</h2></td>
                        <td class="hours"><h2>Qty</h2></td>
                        <td class="hours"><h2>Wt</h2></td>
                        <td class="rate"><h2>MRP</h2></td>
                        <td class="subtotal"><h2>Amount</h2></td>
                      </tr>

                      <tr
                        class="service borderBottom"
                        v-for="(item, i) in invoice_info.sales_items"
                        v-if="invoice_info.sales_items.length > 0"
                        :key="i"
                      >
                        <td class="tableitem">
                          <p class="itemtext">{{ item.products.product_name }}</p>
                        </td>
                        <td class="tableitem">
                          <p class="itemtext">{{ item.quantity }}</p>
                        </td>
                        <td class="tableitem">
                          <p class="itemtext">{{ item.weight }}</p>
                        </td>
                        <td class="tableitem">
                          <p class="itemtext">{{ item.mrp_price }}</p>
                        </td>
                        <td class="tableitem">
                          <p class="itemtext">
                            {{
                              item.uom == 5
                                ? parseFloat(
                                    item.mrp_price * item.weight - item.discount
                                  ).toFixed(2)
                                : parseFloat(
                                    item.mrp_price * item.quantity - item.discount
                                  ).toFixed(2)
                            }}
                          </p>
                        </td>
                      </tr>
                      <tr class="tabletitle" v-if="invoice_info.total_amount">
                        <td></td>
                        <td class="Rate" colspan="2"><h2>Total Amount</h2></td>
                        <td class="payment">
                          <h2>{{ invoice_info.total_amount }}</h2>
                        </td>
                      </tr>
                      <tr
                        class="tabletitle"
                        v-if="invoice_info.customer_group_discount"
                      >
                        <td></td>
                        <td class="Rate" colspan="2"><h2>Discount</h2></td>
                        <td class="payment">
                          <h2>{{ invoice_info.customer_group_discount }}</h2>
                        </td>
                      </tr>
                      <tr class="tabletitle" v-if="invoice_info.order_discount">
                        <td></td>
                        <td class="Rate" colspan="2"><h2>Special Discount</h2></td>
                        <td class="payment">
                          <h2>{{ invoice_info.order_discount }}</h2>
                        </td>
                      </tr>
                      <tr
                        class="tabletitle"
                        v-if="
                          invoice_info.order_vat || invoice_info.order_items_vat
                        "
                      >
                        <td></td>
                        <td class="Rate" colspan="2"><h2>VAT</h2></td>
                        <td class="payment">
                          <h2>
                            {{
                              invoice_info.order_vat + invoice_info.order_items_vat
                            }}
                          </h2>
                        </td>
                      </tr>
                      <tr class="tabletitle" v-if="invoice_info.grand_total">
                        <td></td>
                        <td class="Rate" colspan="2"><h2>Net Amount</h2></td>
                        <td class="payment">
                          <h2>{{ invoice_info.grand_total }}</h2>
                        </td>
                      </tr>
                      <tr class="tabletitle" v-if="invoice_info.paid_amount">
                        <td></td>
                        <td class="Rate" colspan="2"><h2>Paid Amount</h2></td>
                        <td class="payment">
                          <h2>{{ invoice_info.paid_amount }}</h2>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <!--End Table-->

                  <div id="legalcopy">
                    <h2 class="service borderBottom">Payment Info:</h2>
                    <table>
                      <tr class="service borderBottom">
                        <td>Description</td>
                        <td>Amount</td>
                      </tr>
                      <tr
                        class="service borderBottom"
                        v-for="(item, i) in invoice_info.payments"
                        v-if="invoice_info.payments.length > 0"
                        :key="item.id"
                      >
                        <td>{{ item.paying_by }}</td>
                        <td>{{ item.amount }}</td>
                      </tr>
                    </table>
                  </div>
                  <div id="legalcopy">
                    <BarcodeGenerator
                      :value="invoice_info.invoice_number"
                      :format="'CODE128'"
                      :lineColor="'black'"
                      :height="30"
                      :width="1"
                      :elementTag="'img'"
                      :textPosition="19"
                      :fontSize="15"
                      :text="invoice_info.invoice_number"
                    /> 
                  </div>
                  <div id="legalcopy">
                    <h2 class="text-center borderBottom">Note:</h2>
                    <p>Please Exchange Any Product Within 72 Hours</p>
                  </div>

                  <div id="legalcopy">
                    <p class="borderTop"><strong>System By: </strong>SSG-IT</p>
                  </div>
                </div>
                <!--End InvoiceBot-->
              </div>
              <!--End Invoice-->
            </div> 
          </div>
        </Modal>
        <Modal @close="paymentModal()" :modalActive="paymentModalActive">
          <div class="modal-content scrollbar-width-thin">
            <div class="modal-header">
              <h5>Payment option</h5>
              <button @click="paymentModal()" type="button" class="btn btn-default">
                X
              </button>
            </div>
            <div class="modal-body">
              <div class="row"> 
                <div class="col-lg-3">
                  <h3>Total Amount: {{Number(sales_item.total_amount).toFixed(2)}}</h3> 
                </div>
                <div class="col-lg-3"> 
                  <h3>Grand Total: {{Number(sales_item.grand_total).toFixed(2)}}</h3> 
                </div>
                <div class="col-lg-3"> 
                  <h3>Collection: {{Number(sales_item.collection_amount).toFixed(2)}}</h3> 
                </div> 
                <div class="col-lg-3"> 
                  <h3>Due: {{Number(sales_item.grand_total - sales_item.collection_amount).toFixed(2)}}</h3>
                </div>
                <!-- end col -->
                <!-- <div class="col-lg-6">
                  <div class="mb-3">
                    <textarea
                      data-toggle="maxlength"
                      v-model="pform.staff_note"
                      class="form-control"
                      maxlength="225"
                      rows="2"
                      placeholder="Staff Note"
                    ></textarea>
                  </div>
                </div> -->
                <!-- end col -->
              </div>
              <div class="payment well well-sm">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="amount_0">Amount</label>
                      <input
                        v-model="pform.payments[0].amount "
                        id="amount_0"
                        type="text"
                        class="form-control"
                        ref="amount_0"
                        @keyup="checkAboveAmount($event, 's')"
                        @keypress="validateNumber"
                      />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="paid_by">Paying by</label>
                      <select
                        class="form-control border"
                        id="payingby_0"
                        v-model="pform.payments[0].paid_by"
                        @change="payingBy($event, 0)"
                      >
                        <option
                          v-for="(item, key, index) in paying_by"
                          :key="key"
                          :value="item.value"
                          :disabled="checkItem(item.value)"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div
                      class="form-group"
                      v-if="pform.payments[0].paid_by == 'redeem_point'"
                    >
                      <label for="gift_card_no_1">Redeem point</label>
                      <input
                        v-model="pform.payments[0].redeem_point"
                        type="text"
                        class="form-control"
                        @keyup="redeemPointCheck($event, 0)"
                      />
                    </div>
                    <div
                      class="form-group"
                      v-if="pform.payments[0].paid_by == 'gift_card'"
                    >
                      <label for="gift_card_no_1">Gift Card No</label>
                      <input
                        v-model="pform.payments[0].gift_card"
                        type="text"
                        class="form-control"
                      />
                    </div>
                    <div class="row" v-if="pform.payments[0].paid_by == 'CC'">
                      <div class="form-group col-sm-6">
                        <label for="gift_card_no_1">Card Reference Code</label>
                        <input
                          v-model="pform.payments[0].card_reference_code"
                          type="text"
                          class="form-control"
                        />
                      </div>
                      <div class="form-group col-sm-6">
                        <label for="gift_card_no_1">Bank Name</label>
                        <select
                          class="form-control border"
                          v-model="pform.payments[0].bank_id"
                        >
                          <option
                            v-for="(bank, index) in state.banks"
                            :value="bank.id"
                            :key="index"
                          >
                            {{ bank.bank_name }}
                          </option>
                        </select>
                      </div>
                    </div>
                    <div
                      class="form-group"
                      v-if="pform.payments[0].paid_by == 'mfs'"
                    >
                      <label for="gift_card_no_1">Mobile wallet Name</label>
                      <select
                        class="form-control border"
                        v-model="pform.payments[0].wallet_id"
                      >
                        <option
                          v-for="(wallet, index) in state.wallets"
                          :value="wallet.id"
                          :key="index"
                        >
                          {{ wallet.mobile_wallet }}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="amount_1">Payment Note</label>
                      <textarea
                        data-toggle="maxlength"
                        v-model="pform.payments[0].payment_note"
                        class="form-control"
                        maxlength="225"
                        rows="2"
                        placeholder="Sale Note"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="payment well well-sm mt-2"
                v-for="(item, i) in more_payemnts"
                v-if="more_payemnts.length > 0"
                :key="item.id"
                >
                <div class="row">
                  <div class="float-right">
                    <button
                      type="button"
                      class="btn-primary float-right"
                      @click="morePayemntRemove(i)"
                    >
                      X
                    </button>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="amount_1">Amount</label>
                      <input
                        v-model="pform.payments[i + 1].amount"
                        :id="'amount_' + parseFloat(i + 1)"
                        type="text"
                        class="form-control"
                        ref="'amount_'+parseFloat(i+1)"
                        @keyup="checkAboveAmount($event, i)"
                        @keypress="validateNumber"
                      />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="paid_by">Paying by</label>
                      <select
                        class="form-control border"
                        :id="'payingby_' + parseFloat(i + 1)"
                        v-model="pform.payments[i + 1].paid_by"
                        @change="payingBy($event, i + 1)"
                      >
                        <option
                          v-for="(item, key, index) in paying_by"
                          :key="key"
                          :value="item.value"
                          :disabled="checkItem(item.value)"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div
                      class="form-group"
                      v-if="pform.payments[i + 1].paid_by == 'redeem_point'"
                    >
                      <label for="gift_card_no_1">Redeem point</label>
                      <input
                        v-model="pform.payments[i + 1].redeem_point"
                        type="text"
                        class="form-control"
                        @keyup="redeemPointCheck($event, i + 1)"
                      />
                    </div>
                    <div
                      class="form-group"
                      v-if="pform.payments[i + 1].paid_by == 'gift_card'"
                    >
                      <label for="gift_card_no_1">Gift Card No</label>
                      <input
                        v-model="pform.payments[i + 1].gift_card"
                        type="text"
                        class="form-control"
                      />
                    </div>
                    <div class="row" v-if="pform.payments[i + 1].paid_by == 'CC'">
                      <div class="form-group col-sm-6">
                        <label for="gift_card_no_1">Card Reference Code</label>
                        <input
                          v-model="pform.payments[i + 1].card_reference_code"
                          type="text"
                          class="form-control"
                        />
                      </div>
                      <div class="form-group col-sm-6">
                        <label for="gift_card_no_1">Bank Name</label>
                        <select
                          class="form-control border"
                          v-model="pform.payments[i + 1].bank_id"
                        >
                          <option
                            v-for="(bank, index) in state.banks"
                            :value="bank.id"
                            :key="index"
                          >
                            {{ bank.bank_name }}
                          </option>
                        </select>
                      </div>
                    </div>
                    <div
                      class="form-group"
                      v-if="pform.payments[i + 1].paid_by == 'mfs'"
                    >
                      <label for="gift_card_no_1">Mobile wallet Name</label>
                      <select
                        class="form-control border"
                        v-model="pform.payments[i + 1].wallet_id"
                      >
                        <option
                          v-for="(wallet, index) in state.wallets"
                          :value="wallet.id"
                          :key="index"
                        >
                          {{ wallet.mobile_wallet }}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="amount_1">Payment Note</label>
                      <textarea
                        data-toggle="maxlength"
                        v-model="pform.payments[i + 1].payment_note"
                        class="form-control"
                        maxlength="225"
                        rows="2"
                        placeholder="Sale Note"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div class="row pt-2">
                <div class="d-grid">
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="morePayemnt()"
                  >
                    <i class="fa fa-plus"></i> Add More Payments
                  </button>
                </div>
              </div> -->
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-primary"
                @click="handleCollSubmit"
                :disabled="disabled"
              >
                <span v-show="isSubmit" :disabled="disabled">
                  <i class="fas fa-spinner fa-spin"></i>
                </span>
                Submit
              </button>
            </div>
          </div>
        </Modal>
      </div> 
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { ref, reactive, onMounted, getCurrentInstance } from "vue";
import Modal from "./../helper/Modal";  
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
    setup() {
        const invoiceModalActive = ref(false);
        const paymentModalActive = ref(false);
        const app = getCurrentInstance();
        const app2 = app.appContext.config.globalProperties;
        const invoiceModal = () => { 
          invoiceModalActive.value = !invoiceModalActive.value; 
        };
        const paymentModal = () => { 
          paymentModalActive.value = !paymentModalActive.value; 
        };
        const state = reactive({
            companies: [],
            customer_groups: [],
            areas: [],
            units: [],
            districts: [],
            customers: [],
            customers_info: [],
            wallets: [],
            points_setting: "",
            banks: [],
        });
        onMounted(async () => { 
          await axios
            .get(app2.apiUrl + "/sale-helper", app2.headers)
            .then((res) => {
              state.customer_groups = res.data.data.customer_groups;
              state.districts = res.data.data.districts;
              state.wallets = res.data.data.wallets;
              state.banks = res.data.data.banks;
              state.units = res.data.data.units;
              state.points_setting = res.data.data.points_settings;
            }); 
        });
        return {state, invoiceModal, invoiceModalActive, paymentModalActive, paymentModal}
    },
    data() {  
        return {   
            items: [],
            loading:true, 
            sortKey: 'name',  
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
                    width: '15%'
                },   
                {
                    label: 'Invoice Number',
                    name: 'invoice_number',
                    width: '15%'
                },
                {
                    label: 'Date',
                    name: 'created_at',
                    width: '10%'
                },
                {
                    label: 'Cuastomer Name',
                    name: 'customer_name',
                    width: '15%'
                },
                {
                    label: 'Total Amount',
                    name: 'total_amount',
                    width: '15%'
                }, 
                {
                    label: 'Grand Total',
                    name: 'grand_total',
                    width: '15%'
                },  
                {
                    label: 'Paid',
                    name: 'collection_amount',
                    width: '10%'
                }, 
                {
                    label: 'Total Item',
                    name: 'sales_items_count',
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
                sortKey: 'product_name', 
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
            disabled:false,
            isLoading:true,
            componentKey: 0,
            invoice_info: "",
            hold_data: "",
            invoice_number: "",
            customer_points: 0,
            more_payemnts: [],
            sales_item: '',
            pform: new Form({ 
                id: 0,
                total_amount: 0,
                grand_total: "", 
                paid_amount: 0,
                return_amount: 0,  
                status: 0,  
                payments: [
                  {
                    amount: 0,
                    paid_by: "cash",
                    gift_card: "",
                    bank_id: "",
                    card_reference_code: "",
                    wallet_id: "",
                    payment_note: "",
                    redeem_point: "",
                  },
                ],
            }),
            paying_by: [
                { value: "cash", name: "Cash", disabled: false },  
                { value: "CC", name: "Credit Card", disabled: false },
                { value: "mfs", name: "Mobile wallet", disabled: false }, 
            ],
        };
    },  
    methods: { 
        // onSearch(search){
        //   alert(search);
        // }, 
        // onGettingEntries(){
        //   console.log('onGettingEntries');
        // },
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
        handleDeleteBtn(event){ 
          let trID = event.target.parentElement.parentElement.parentElement.parentElement.id
          this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/sales/'+trID, this.headers).then(res => {
                        if(res.status == 200){  
                          this.forceRerender();
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
        fetchItems(url = this.apiUrl+'/sale/duelist') {
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
        paymentCollection(item){ 
            this.sales_item = item;  
            this.pform.id = item.id;
            this.pform.total_amount = item.total_amount;
            this.pform.grand_total = item.grand_total; 
            this.pform.paid_amount = item.paid_amount; 
            this.pform.return_amount = item.return_amount;
            console.log('item', item)
            this.paymentModal();  
        },
        handleCollSubmit(){
            this.isSubmit = true; 
            this.disabled = true;
            axios.post(this.apiUrl+'/collection/',this.pform, this.headerjson).then(res => {
            if(res.status == 200){   
                this.isSubmit = false; 
                this.disabled = false;
                this.fetchItems();
                this.paymentModal(); 
                this.$toast.success(res.data.message); 
            }else{
                this.$toast.error(res.data.message);
            } 
            }).catch(err => {  
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message); 
            })  
        },
        checkAboveAmount: function (event, i) { 
          let keyCode = event.keyCode;
          var mask = document.getElementById(
            "payingby_" + event.target.id.split("_")[1]
          );
          if (mask.value != "cash") { 
            if((Number(this.pform.grand_total).toFixed(0) - Number(this.pform.paid_amount).toFixed(0)) < Number(event.target.value)) {
              event.target.value = this.netAmountCalculate;
              this.$toast.warning("Not allow max amount "); 
            }
          }
        },
        checkItem: function (item) {
          for (var i = 0; i < this.pform.payments.length; i++) {
            if (this.pform.payments[i].paid_by == item) {
              return true;
            }
          }
        },
        payingBy(event, e) {
          var mask = document.getElementById("amount_" + e);
          if (this.pform.payments[e].paid_by == "redeem_point") {
            mask.readOnly = true;
          } else {
            mask.readOnly = false;
          }
        },
        payingByAnother(event, e) {
          if (this.pform.payments[e].paid_by != "cash") {
            this.paymentModal();
          }
          this.pform.payments[0].paid_by = "cash";
          var mask = document.getElementById("amount_" + e);
          if (this.pform.payments[e].paid_by == "redeem_point") {
            mask.readOnly = true;
          } else {
            mask.readOnly = false;
          }
        },
        redeemPointCheck: function (event, e) {
          if (this.pform.payments[e].redeem_point > this.customer.points) {
            this.pform.payments[e].amount = this.convartRate * this.customer.points;
            this.pform.payments[e].redeem_point = this.customer.points;
          } else {
            this.pform.payments[e].amount = parseFloat(
              Number(
                this.convartRate * this.pform.payments[e].redeem_point
              ).toLocaleString()
            );
          }
        },
        findInvoice: function (invoice_number) { 
          this.invoiceModal();
          var postEvent = axios.get(
            this.apiUrl + "/saleInfo?invoice_number=" + invoice_number,
            this.headerjson
          );
          postEvent
            .then((res) => {
              this.invDisabled = false;
              if (res.status == 200) {
                if (res.data.data.length > 0) { 
                    this.popupError = "";  
                    this.invoice_info = res.data.data[0];                   
                } else {
                  this.popupError = "Please enter valid invoice number"; 
                }
              } else {
                this.popupError = res.data.message; 
              }
            })
            .catch((err) => {
              console.log("err", err);
              this.invDisabled = false;
              //this.$toast.error(err.response.data.message);
            });
        },
        show: function(item) {     
          axios.get(this.apiUrl+'/products/'+item.id, this.headers).then(res => {
              if(res.status == 200){  
                this.product = res.data.data
                this.modalShow = !this.modalShow;  
              }else{
                this.$toast.error(res.data.message);
              }
              console.log(res.data.data)
          }).catch(err => {  
              this.$toast.error(err.response.data.message);
              if(err.response.status == 422){
                  this.errors = err.response.data.errors 
              }
          }) 
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

.return_amount{
  font-size: 20px;
  font-weight: bold;
  color: #e86969; 
}
#return_amount{ 
  transition: 0.5s;
  animation: blinker 1s linear infinite;
}
@keyframes blinker {
  80% {
    opacity: 0;
  }
}
.form-control.border.coupon {
  height: 32px;
}
#outline-buttons-preview {
  margin: 5px 31px 10px -10px;
}
.w-full.rounded.bg-white.border.border-gray-300.px-4.py-2.space-y-1.absolute.z-10 {
  position: absolute;
  z-index: 9999;
}
.form-group {
  margin-bottom: 5px;
}
.border.success.item-head {
  width: 20px !important;
  color: #f4f4f4;
  background-color: #3e81ae;
}
.table-bordered th,
.table-bordered td {
  border: none;
}
.pos-footer {
  width: 96%;
  margin: 0px auto;
}
.pos-body {
  overflow-x: scroll;
}
.qty {
  width: 71px;
}
.input-group.input-group-merge {
  height: 38px;
}
.input-group-text {
  height: 40px;
}

.btn-square-md {
  width: 100px !important;
  max-width: 100% !important;
  max-height: 100% !important;
  height: 100px !important;
  text-align: center;
  padding: 0px;
  font-size: 12px;
}
.payment {
  width: 560px;
}
.well {
  border: 1px solid #ddd;
  background-color: #f6f6f6;
  box-shadow: none;
  border-radius: 0px;
}
.well-sm {
  padding: 9px;
  border-radius: 3px;
}

/** Autocomplete */
.autocomplete {
  position: relative;
}

ul.autocomplete-results {
  width: 100% !important;
  margin-top: 39px !important;
}
.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  overflow: auto;
  width: 100%;
}

.autocomplete-results li {
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
}

.autocomplete-results li.isActive,
.autocomplete-results li:hover {
  background-color: #4aae9b;
  color: white;
}
.autocomplete-results li:first-child {
  font-weight: 600;
  border-bottom: 1px solid #282828;
  margin-bottom: 3px;
}
.autocomplete-results li:first-child:hover {
  background-color: inherit;
  color: inherit;
}

.isActive {
  background-color: #dedede;
}

@media print {
  html,
  body {
    width: 80mm;
    height: 100%;
    position: absolute;
  }

  #invoice-POS {
    border: 2px solid #000;
    background: #3e81ae;
  }

  dl,
  ol,
  ul {
    margin-top: 0;
    margin-bottom: 1rem;
    list-style-type: none;
  }
}

dl,
ol,
ul {
  list-style-type: none !important;
}

.modal-content.scrollbar-width-thin.customer-add-modal {
  width: 85%;
  display: block;
  margin: auto;
}
.modal-content.scrollbar-width-thin.confirm-window {
  width: 100%;
  display: block;
  margin: auto;
}
.modal-content.scrollbar-width-thin.invoice-modal {
  width: 100%;
  display: block;
  margin: auto;
}

.modal-content.scrollbar-width-thin.barcodeReaderModal {
  width: 100%;
  display: block;
  margin: auto;
}

.modal-content.scrollbar-width-thin.barcodeReaderModal #videoWindow.video {
    width: 100% !important;
}

.modal-content.scrollbar-width-thin.barcodeReaderModal #videoWindow canvas {
    /* width: 500px !important; */
    display: none !important;
}

canvas.drawingBuffer {
    display: none;
}

.modal-content.scrollbar-width-thin.return-replace-modal {
  width: 90%;
  display: block;
  margin: auto;
}
.btn-return {
  background-color: #fe7a00;
  color: #fff;
}
.return-replace-summary h2 {
  margin: 7px auto;
  padding: 0px;
  font-weight: bold;
}

#invoice-POS {
  box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  padding: 2mm;
  margin: 0 auto;
  width: 84mm;
  background: #fff;
}
#invoice-perview {
  box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  padding: 2mm;
  margin: 0 auto;
  width: 110mm;
  background: #fff;
}

::selection {
  background: #f31544;
  color: #fff;
}
::moz-selection {
  background: #f31544;
  color: #fff;
}
h1 {
  font-size: 1.5em;
  color: #222;
}
h2 {
  font-size: 0.9em;
}
h3 {
  font-size: 1.2em;
  font-weight: 300;
  line-height: 2em;
}
p {
  font-size: 0.7em;
  color: #666;
  line-height: 1.2em;
}

#top,
#mid,
#bot {
  /* Targets all id with 'col-' */
  border-bottom: 1px solid #eee;
}

#top {
  min-height: 100px;
}
#mid {
  min-height: 80px;
}
#bot {
  min-height: 50px;
}

#top .logo {
  height: 60px;
  width: 60px;
  background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
  background-size: 60px 60px;
}
.clientlogo {
  float: left;
  height: 60px;
  width: 60px;
  background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
  background-size: 60px 60px;
  border-radius: 50px;
}
.info {
  display: block;
  margin-left: 0;
}
.title {
  float: right;
}
.title p {
  text-align: right;
}
table {
  width: 100%;
  border-collapse: collapse;
}
.tabletitle {
  font-size: 0.5em;
}
.service {
  border-bottom: 1px solid #eee;
}
.item {
  width: 30%;
}
.hours {
  width: 10%;
}
.rate {
  width: 10%;
}
.subtotal {
  width: 10%;
}
.action {
  width: 15%;
}
.itemtext {
  font-size: 0.5em;
}



@media only screen and (max-width: 464px) {
  /* For mobile phones: */
  #invoice-perview {
    width: 100%;
  }
}

@media only screen and (max-width: 768px) {
  /* For mobile phones: */
  .pos-body {
    min-height: 150px !important;
    height: auto !important;
    margin-bottom: 20px;
  }

  .pos-leftbar {
    margin-bottom: 20px;
  }
}

@media only screen and (max-width: 1330px) {
  /* For mobile phones: */
  .coupon_box {
    width: 100% !important;
  }
}

@media only screen and (min-width: 940px) and (max-width: 1330px) {
  /* For mobile phones: */
  .action_btn {
    width: 50% !important;
  }
}

@media only screen and (min-width: 769px) and (max-width: 939px) {
  /* For mobile phones: */
  .action_btn {
    width: 100% !important;
  }
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