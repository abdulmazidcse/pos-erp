<template>
  <div class="row">
    <div class="col-sm-12 col-md-8">
      <div class="pos-leftbar" id="pos-leftbar">
        <div class="pos-header">
          <div class="mb-1">
            <div class="input-group input-group-merge"> 
              <Multiselect
                class="form-control border customer_id"
                mode="single"
                v-model="form.customer_id"
                placeholder="Customer"
                @change="selectCustomer($event)"
                :searchable="true"
                :filter-results="true"
                :options="state.customers"
                :classes="multiclasses"
                :close-on-select="true"
                :min-chars="1"
                :resolve-on-load="false"
              />
              <div class="input-group-text">
                <a @click="customerModal">
                  <i class="fas fa-2x fa-plus-circle"></i>
                </a>
              </div>
            </div>
          </div> 
          <div class="mb-1">
            <div class="input-group input-group-merge autocomplete">
              <input
                type="text"
                v-model="searchIteam"
                class="form-control"
                placeholder="Scan/Search product by name/code"
                @keyup="inputChanged"
                @keyup.enter="addProductToCart()"
                @keydown.down="onArrowDown"
                @keydown.up="onArrowUp"
              />
              <ul
                v-if="searchProduct.length"
                class="
                  w-full
                  rounded
                  bg-white
                  border border-gray-300
                  px-4
                  py-2
                  space-y-1
                  absolute
                  z-10
                  autocomplete-results
                "
              >
                <li class="px-1 pt-1 pb-2 font-bold border-b border-gray-200">
                  <div style="width: 100%">
                    <div style="width: 80%; float: left; font-size: 10px">
                      Product name
                    </div>
                    <div
                      style="
                        width: 20%;
                        float: right;
                        font-size: 10px;
                        text-align: right;
                      "
                    >
                      Expire Date
                    </div>
                  </div>
                </li>
                <li
                  v-for="(item, i) in searchProduct.slice(0, 15)"
                  :key="item.product_name"
                  @change="selectProduct(item)"
                  @click="setResult(item)"
                  class="cursor-pointer hover:bg-gray-100 p-1"
                  :class="{ isActive: i === arrowCounter }"
                >
                  {{ item.product_name }} ({{ item.product_code }})

                  <span
                    v-if="item.expires_date"
                    style="
                      color: rgb(34, 5, 242);
                      float: right;
                      text-align: right;
                    "
                  >
                    {{ item.expires_date ? item.expires_date : "" }}
                  </span>
                </li>
              </ul>
              <div class="input-group-text">
                <a @click="barcodeReaderModal">
                  <i class="fas fa-2x fa-camera"></i>
                </a>
              </div>
            </div>
          </div>            
        </div>
        <div
          class="pos-body scrollbar-width-thin"
          v-bind:style="{ height: window.height + 'px' }"
        >
          <div class="">
            <table
              id="header-fixed"
              class="
                table-sm
                pos-table
                table
                items
                table-striped table-bordered table-condensed table-hover
                sortable_table
              "
            >
              <thead class="tableFloatingHeaderOriginal">
                <tr class="border item-head">
                  <th width="30%">Item Name</th>
                  <th width="10%" class="text-center">Stock Qty</th>
                  <th width="10%" class="text-center">UOM</th>
                  <th width="10%" class="text-center">Qty</th>
                  <th width="10%" class="text-center">Weight</th>
                  <th width="10%" class="text-center">Price</th> 
                  <th width="10%" class="text-center">Value</th> 
                  <th width="10%" class="text-center">Plat Disc. </th> 
                  <th width="10%" class="text-center">Disc %</th> 
                  <th width="10%" class="text-center">Disc Value</th> 
                  <th width="15%" class="text-center">Net Value</th>
                  <th width="5%">Action</th>
                </tr>
              </thead>
              <tr
                class="border"
                v-for="(item, i) in cartItems"
                v-if="cartItems.length > 0"
                :key="item.id"
              >
                <td class="text-left p-1">
                  {{ item.product_name }} ({{ item.product_code }}) ({{
                    item.expires_date
                  }}) <span v-if="item.product_name.length > 30">...</span>
                </td>
                <td class="text-center border">
                  {{ item.stock_quantity }}
                </td>
                <td class="tableitem">
                  <select class="form-control border" v-model="item.uom">
                    <option
                      :value="unit.id"
                      v-for="(unit, i) in state.units"
                      :key="i"
                    >
                      {{ unit.unit_code }}
                    </option>
                  </select>
                </td>
                <td class="text-center border">
                  <input
                    class="form-control qty "
                    type="number"
                    name=""
                    v-model="item.quantity"
                    @keyup="checkStockQty(item)"
                    style="text-align: center"
                  />
                </td>
                <td class="text-center border">
                  <input
                    class="form-control qty"
                    type="number"
                    name=""
                    v-model="item.weight"
                    style="text-align: center"
                  />
                </td>
                <td class="text-center border">{{ item.mrp_price }}</td>
                <td class="text-center border">{{
                    item.uom == 5
                      ? parseFloat(
                          item.mrp_price * item.weight 
                        ).toFixed(0)
                      : parseFloat(
                          item.mrp_price * item.quantity  
                        ).toFixed(0)
                  }}</td>
                
                <td class="text-center border">
                <input
                    class="form-control qty"
                    type="number"
                    name=""
                    v-model="item.discount"
                    @change="itemDiscountPlat(item, i)"
                    style="text-align: center"
                  /> </td> 
                <td class="text-center border">
                <input
                    class="form-control qty"
                    v-model="item.discount_percent"
                    type="number"
                    name="" 
                    @change="itemDiscountPercent(item, i)"
                    style="text-align: center"
                  /> </td> 
                <td class="text-center border">
                  {{
                    item.uom == 5
                      ? parseFloat(
                           (item.discount * item.weight)
                        ).toFixed(0)
                      : parseFloat(
                           (item.discount * item.quantity)
                        ).toFixed(0)
                  }} 
                </td>
                <td class="text-center border">
                  {{
                    item.uom == 5
                      ? parseFloat(
                          item.mrp_price * item.weight - (item.discount * item.weight)
                        ).toFixed(0)
                      : parseFloat(
                          item.mrp_price * item.quantity - (item.discount * item.quantity)
                        ).toFixed(0)
                  }}
                </td>
                
                <td @click="removeCartItem(i)" style="text-align: center">
                  <i
                    style="font-size: 20px; cursor: pointer; color: #ff6a00"
                    class="mdi mdi-trash-can-outline"
                  ></i>
                </td>
              </tr>

              <tr class="border">
                <td class="text-left p-1"> </td>
                <td class="tableitem"> </td>
                <td class="tableitem"> </td>
                <td class="text-center border">{{parseFloat(totalItemQty).toFixed(0)}} </td>
                <td class="text-center border">{{parseFloat(totalItemWeight).toFixed(3)}}</td>  
                <td class="text-center border">{{parseFloat(totalItemPrice).toFixed(0)}}</td>  
                <td class="text-center border">{{parseFloat(totalItemValue).toFixed(0)}}</td>
                <td class="text-center border"></td>
                <td class="text-center border"></td>
                <td class="text-center border">{{parseFloat(totalItemDiscountValue).toFixed(0)}}</td>
                <td class="text-center border">{{parseFloat(totalItemNetValue).toFixed(0)}}</td>
                <td  style="text-align: center"> 
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-12 col-md-4">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="amount_0">Given Amount</label>
            <input
              v-model="pform.payments[0].amount"
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
            <label for="paid_by">Paying byy</label>
            <select
              class="form-control border"
              id="payingby_0"
              v-model="pform.payments[0].paid_by"
              @change="payingByAnother($event, 0)"
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
        <div class="row pt-2"></div>
      </div>
      <div class="row">
        <div>
          <table
            class="table-sm table table-bordered border"
            style="margin-bottom: 0px"
          >
            <thead class="tableFloatingHeaderOriginal">
              <tr class="border item-head return_amount" >
                <th >Return Amount</th>
                <th :id="(Number(netAmountCalculate - totalCollections) < 0) ? 'return_amount': ''">
                <span v-if="(Number(netAmountCalculate - totalCollections) < 0)">
                  {{  Math.abs(Number(netAmountCalculate - totalCollections)).toLocaleString() }}
                </span> 
                <span v-else>
                  0
                </span> 
                </th> 
                <th
                  rowspan="7"
                  class="text-center align-middle border fc-darkblue"
                  bgcolor="green"
                >
                  <p style="font-size: 20px">
                    Net Amount <br />
                    {{ Number(netAmountCalculate).toLocaleString() }}
                  </p>
                </th>
              </tr>
              <tr class="border item-head">
                <th>Total</th>
                <th>{{ Number(Math.round(totalCartValue)).toLocaleString() }}</th> 
              </tr>
              <tr class="item-head">
                <th>Special Customer & Group Discount</th>
                <th>
                  {{
                    Math.round(Number(
                      customer_discount + customer_group_discount
                    )).toLocaleString()
                  }}
                </th>
              </tr>
              <!-- <tr class="item-head"> 
                  <th>Special customer  </th>
                  <th>{{Number(customer_group_discount).toLocaleString()}} </th> 
                </tr> -->
              <tr class="item-head">
                <th>Discount & Coupon</th>
                <th>
                  {{
                    Number(
                      special_discount + couponDiscoun 
                    ).toLocaleString()
                  }}
                </th>
              </tr>
              <tr class="item-head">
                <th>Item Discount</th>
                <th>{{ Number(cartItemDiscount).toLocaleString() }}</th>
              </tr>
              <tr class="item-head">
                <th>Invoice Vat & Item Vat</th>
                <th>
                  {{ Number(invoiceVat + totalCartTax).toLocaleString() }}
                </th>
              </tr>
              <!-- <tr class="  item-head">
                  <th></th>
                  <th>{{Number(totalCartTax).toLocaleString()}}</th> 
                </tr> -->
              <tr class="item-head">
                <th>Total Item</th>
                <th>{{ Number(cartItems.length).toLocaleString() }}</th>
              </tr>
            </thead>
          </table>
        </div>

        <div class="row button-list pt-2">
          <div class="col-md-8 coupon_box" style="padding: 0px 0px 0px 8px">
            <div class="d-grid">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control border coupon"
                  v-model="form.coupons_code"
                  placeholder="Enter Coupon Code"
                  autocomplete="off"
                  autofocus="off"
                />
                <button type="button" class="btn btn-sm btn-primary" @click="applyCoupons">
                  <i v-show="couponSubmit" class="fas fa-spinner fa-spin"></i>
                  Apply Coupon
                </button>
              </div>
            </div>
          </div>
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0 5px"
          >
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-sm btn-success"
                @click="addDiscountModal"
              >
                Add Discount
              </button>
            </div>
          </div>
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0"
          >
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-sm btn-primary"
                @click="handleSaleHoldSubmit"
                :disabled="disabled"
              >
                Hold
              </button>
            </div>
          </div>
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0 0px"
          >
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-sm btn-danger"
                id="reset"
                @click="clearAllCartItems"
              >
                Clear Item
              </button>
            </div>
          </div>
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0 5px"
          >
            <div class="d-grid">
              <button
                type="button" 
                class="btn btn-sm btn-success"
                @click="addConfirmModal"
                :disabled="submitDisable" 
              >
                Confirm
              </button>
            </div>
          </div>

          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0"
          >
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-sm btn-primary"
                @click="rePrintModal"
              >
                Reprint
              </button>
            </div>
          </div>
          <!-- <div class="col-md-4 " style="padding: 0;">
              <div class="d-grid">                 
                <button type="button" class="btn btn-sm btn-info" @click="saleReplaceModal" >
                Replace </button> 
              </div> 
            </div> -->
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0"
          >
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-sm btn-warning"
                @click="returnReplaceModal"
              >
                Return
              </button>
            </div>
          </div>
          <div
            class="col-xs-12 col-sm-6 col-md-4 action_btn"
            style="padding: 0"
          >
            <div class="d-grid">
              <!-- <button type="button" class="btn btn-sm btn-warning" @click="invoiceModal">Invoice preview</button>  -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal @close="paymentModal()" :modalActive="paymentModalActive">
      <div class="modal-content scrollbar-width-thin">
        <div class="modal-header">
          <h5>Multi payment option</h5>
          <button @click="paymentModal()" type="button" class="btn btn-default">
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <p v-if="customer && customer.customer_group_name != 'Walking Customer'">
                Available Points : {{ customer.points }} = ৳
                {{
                  parseFloat(
                    Number(convartRate * customer.points).toLocaleString()
                  ).toFixed(2)
                }}
              </p>
            </div>
            <div class="col-lg-6"></div>
            <div class="col-lg-6">
              <div class="mb-3">
                <textarea
                  data-toggle="maxlength"
                  v-model="pform.sale_note"
                  class="form-control"
                  maxlength="225"
                  rows="2"
                  placeholder="Sale Note"
                ></textarea>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-6">
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
            </div>
            <!-- end col -->
          </div>
          <div class="payment well well-sm">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="amount_0">Amount</label>
                  <input
                    v-model="pform.payments[0].amount"
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
                  <label for="paid_by">Paying by2</label>
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
                  class="row"
                  v-if="pform.payments[0].paid_by == 'mfs'"
                >
                  <div class="form-group col-sm-12">
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

                  <div class="form-group col-sm-6">
                    <label for="gift_card_no_1">Transaction No</label>
                    <input
                      v-model="pform.payments[0].transaction_no"
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
                  <label for="paid_by">Paying by3</label>
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
                  class="row"
                  v-if="pform.payments[i + 1].paid_by == 'mfs'"
                >
                  <div class="form-group col-sm-12">
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

                  <div class="form-group col-sm-6">
                    <label for="gift_card_no_1">Transaction No</label>
                    <input
                      v-model="pform.payments[i + 1].transaction_no"
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
          <div class="row pt-2">
            <div class="d-grid">
              <button
                type="button"
                class="btn btn-primary"
                @click="morePayemnt()"
              >
                <i class="fa fa-plus"></i> Add More Payments
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-primary"
            @click="handleSubmit"
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
    <Modal @close="customerModal()" :modalActive="customerModalActive">
      <div class="modal-content scrollbar-width-thin customer-add-modal">
        <div class="modal-header">
          <h5>Add New Customer</h5>
          <button
            @click="customerModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="customer_code" class="form-label" >Customer Code *</label>
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control border"
                    @keypress="onkeyPress('customer_code')"
                    v-model="cform.customer_code"
                    id="product_code"
                    placeholder="Customer Code"
                    autocomplete="off"
                  />
                  <span
                    class="input-group-text"
                    id="random_num"
                    @click="random_num()"
                  >
                    <i class="fa fa-random"></i>
                  </span>
                  <div class="invalid-feedback" v-if="errors.customer_code">
                    {{ errors.customer_code[0] }}
                  </div>
                </div>
              </div>
            </div> 
            <!-- end col -->
            <div class="col-lg-4" style="display: none">
              <div class="mb-3">
                <label for="company_id" class="form-label">Company </label>
                <select
                  class="form-control border"
                  v-model="cform.company_id"
                  @change="onkeyPress('company_id')"
                  id="company_id"
                >
                  <option value="0">Select company</option>
                  <option
                    v-for="(company, index) in state.companies"
                    :value="company.id"
                    :key="index"
                  >
                    {{ company.name }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="errors.company_id">
                  {{ errors.company_id[0] }}
                </div>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >Customer Group *</label
                >
                <select
                  class="form-control border"
                  v-model="cform.customer_group_id"
                  @change="onkeyPress('customer_group_id')"
                  id="customer_group_id"
                >
                  <option value="0">Select Customer Group</option>
                  <option
                    v-for="(c_group, index) in state.customer_groups"
                    :value="c_group.id"
                    :key="index"
                  >
                    {{ c_group.title }}
                  </option>
                </select>
                <div class="invalid-feedback" v-if="errors.customer_group_id">
                  {{ errors.customer_group_id[0] }}
                </div>
              </div>
            </div>
            <!-- end col -->
          </div>
          <div class="row">
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="name" class="form-label">Name *</label>
                <input
                  type="text"
                  class="form-control"
                  @keypress="onkeyPress('name')"
                  @keyup="ledgerAccountNameSetup($event.target.value)"
                  v-model="cform.name"
                  id="name"
                  placeholder="Customer Name"
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="errors.name">
                  {{ errors.name[0] }}
                </div>
              </div>
            </div>
            
            <div class="col-lg-4">
              <label for="address" class="form-label">Address *</label>
              <input
                type="text"
                class="form-control"
                @keypress="onkeyPress('address')"
                v-model="cform.address"
                id="address"
                placeholder="Customer Address"
              />
              <div class="invalid-feedback" v-if="errors.address">
                {{ errors.address[0] }}
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number *</label>
                <input
                  type="text"
                  class="form-control"
                  @keypress="onkeyPress('phone')"
                  v-model="cform.phone"
                  id="phone"
                  placeholder="Mobile number"
                />
                <div class="invalid-feedback" v-if="errors.phone">
                  {{ errors.phone[0] }}
                </div>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="email" class="form-label">Email </label>
                <input
                  type="text"
                  class="form-control"
                  @keypress="onkeyPress('email')"
                  v-model="cform.email"
                  id="email"
                  placeholder="Customer Email"
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="errors.email">
                  {{ errors.email[0] }}
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth </label>
                <input
                  type="date"
                  class="form-control"
                  @keypress="onkeyPress('dob')"
                  v-model="cform.dob"
                  id="dob"
                  placeholder=""
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="errors.dob">
                  {{ errors.dob[0] }}
                </div>
              </div>
            </div>
            <div class=" col-lg-4">
              <label for="discount_percent" class="form-label"
                >Discount Percent
              </label>
              <input
                type="number"
                class="form-control"
                v-model="cform.discount_percent"
                id="discount_percent"
                placeholder="Discount Percent"
                autocomplete="off"
              />
              <div class="invalid-feedback" v-if="errors.discount_percent">
                {{ errors.discount_percent[0] }}
              </div>
            </div>
          </div> 
          <div class="row">
            <div class="form-group col-md-3">
              <label for="district_id" class="form-label">District</label>
              <select
                class="form-control border"
                v-model="cform.district_id"
                id="district_id"
                @change="districtChange($event)"
              >
                <option value="0">Select district</option>
                <option
                  v-for="(district, index) in state.districts"
                  :value="district.id"
                  :key="index"
                >
                  {{ district.name }}
                </option>
              </select>
              <div class="invalid-feedback" v-if="errors.district_id">
                {{ errors.district_id[0] }}
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="area_id" class="form-label">Area</label>
              <select
                class="form-control border"
                v-model="cform.area_id"
                id="area_id"
              >
                <option value="0">Select area</option>
                <option
                  v-for="(area, index) in state.areas"
                  :value="area.id"
                  :key="index"
                >
                  {{ area.name }}
                </option>
              </select>
              <div class="invalid-feedback" v-if="errors.area_id">
                {{ errors.area_id[0] }}
              </div>
            </div>
            <div class="form-group col-md-3">
              <label for="postal_code" class="form-label">Postal Code</label>
              <input
                type="text"
                class="form-control border"
                v-model="cform.postal_code"
                id="postal_code"
                placeholder="Postal Code"
                autocomplete="off"
              />
              <div class="invalid-feedback" v-if="errors.postal_code">
                {{ errors.postal_code[0] }}
              </div>
            </div> 
            <div class="form-group col-md-3">
              <div class="mb-3">
                <label for="example-select" class="form-label">Status </label>
                <select class="form-select" v-model="cform.status">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
              <div class="invalid-feedback" v-if="errors.status">
                {{ errors.status[0] }}
              </div>
            </div>
          </div>

          <div class="row">
            <div class="form-group form-check col-md-3">
              <input
                type="checkbox"
                class="form-check-input"
                true-value="1"
                false-value="0"
                v-model="cform.wholesale_customer"
                id="wholesale_customer"
              />
              Wholesale Customer
              <div class="invalid-feedback" v-if="errors.wholesale_customer">
                {{ errors.wholesale_customer[0] }}
              </div>
            </div>
            <div class="form-group form-check col-md-3">
              <input
                type="checkbox"
                class="form-check-input"
                true-value="1"
                false-value="0"
                v-model="cform.sale_without_vat"
                id="sale_without_vat"
              />
              Sale Without VAT
              <div class="invalid-feedback" v-if="errors.sale_without_vat">
                {{ errors.sale_without_vat[0] }}
              </div>
            </div>
            <div class="form-group form-check col-md-3">
              <input
                type="checkbox"
                class="form-check-input"
                true-value="1"
                false-value="0"
                v-model="cform.credit_customer"
                id="credit_customer"
              />
              Credit Customer
              <div class="invalid-feedback" v-if="errors.credit_customer">
                {{ errors.credit_customer[0] }}
              </div>
            </div>
            <div class="form-group form-check col-md-3">
              <input
                type="checkbox"
                class="form-check-input"
                true-value="1"
                false-value="0"
                v-model="cform.store_customer"
                id="store_customer"
              />
              Store Customer
              <div class="invalid-feedback" v-if="errors.store_customer">
                {{ errors.store_customer[0] }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" @click="handleCustomer">
            <span v-show="isSubmit" :disabled="disabledCBtn">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Submit
          </button>
        </div>
      </div>
    </Modal>
    <Modal @close="addDiscountModal()" :modalActive="discountModalActive">
      <div class="modal-content scrollbar-width-thin">
        <div class="modal-header">
          <h5>Add Discount</h5>
          <button
            @click="addDiscountModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <!-- v-if="!enabledDiscount" -->
          <div class="row" style="display:none">
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >User ID *</label
                >
                <input
                  type="text"
                  class="form-control border"
                  v-model="user.email"
                  placeholder="User ID"
                  autocomplete="off"
                />
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >Password *</label
                >
                <input
                  type="password"
                  class="form-control border"
                  v-model="user.password"
                  placeholder="Password"
                  autocomplete="off"
                />
              </div>
            </div>
            <!-- end col -->
          </div>
          <div class="row" >
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="orderDiscount">Discount (5 or 5%) </label>
                <input
                  type="text"
                  class="form-control border"
                  v-model="form.order_discount"
                  placeholder="5 or 5%"
                  autocomplete="off"
                />
              </div>
            </div>
            <!-- end col -->
          </div>
        </div>
        <div class="modal-footer">
          <button style="display:none" 
            type="submit"
            class="btn btn-primary"
            @click="handleLogin"
            v-if="!enabledDiscount"
          >
            <span v-show="isSubmit" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i> </span
            >Submit
          </button>
          <button
            type="submit"
            class="btn btn-primary"
            @click="addDiscountModal" 
          >
            <span v-show="isSubmit">
              <i class="fas fa-spinner fa-spin"></i> </span
            >Apply
          </button>
        </div>
      </div>
    </Modal>
    <Modal @close="rePrintModal()" :modalActive="rePrintModalActive">
      <div class="modal-content scrollbar-width-thin">
        <div class="modal-header">
          <h5>Reprint Invoice</h5>
          <button @click="rePrintModal()" type="button" class="btn btn-default">
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >Invoice Number *</label
                >
                <input
                  type="text"
                  class="form-control border"
                  v-model="invoice_number"
                  placeholder="Invoice Number"
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="popupError != ''">
                  {{ popupError }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" @click="findInvoice">
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Submit
          </button>
        </div>
      </div>
    </Modal>

    <!-- Only Return -->
    <Modal
      @close="returnReplaceModal()"
      :modalActive="returnReplaceModalActive"
      >
      <div class="modal-content scrollbar-width-thin">
        <div class="modal-header">
          <h5>Return</h5>
          <button
            @click="returnReplaceModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >Invoice Number *</label
                >
                <input
                  type="text"
                  class="form-control border"
                  v-model="invoice_number"
                  placeholder="Invoice Number"
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="popupError != ''">
                  {{ popupError }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" @click="findInvoice('return')">
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Submit
          </button>
        </div>
      </div>
    </Modal>

    <Modal
      @close="returnReplacePageModal()"
      :modalActive="returnReplacePageModalActive"
      >
      <div class="modal-content scrollbar-width-thin return-replace-modal">
        <div class="modal-header">
          <h5>Return & Replace</h5>
          <button
            @click="returnReplacePageModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body" id="printMe">
          <div v-if="returnInfo">
            <div id="mid">
              <div class="info" v-if="returnInfo.customer">
                <p class="text-left" style="font-size: 14px">
                  Name: {{ returnInfo.customer.name }}<br />
                  Address: {{ returnInfo.customer.address }} <br />
                  Mobile: {{ returnInfo.customer.phone }}<br />
                  Srvd by: {{ returnInfo.created_by.name }}
                </p>
              </div>
            </div>
            <!--End Invoice Mid-->
            <div id="bot">
              <div id="table">
                <table>
                  <tr class="borderTop borderBottom">
                    <td class="hours">Sl</td>
                    <td class="item"><h2>Item Name</h2></td>
                    <td class="hours"><h2>Qty</h2></td>
                    <td class="hours"><h2>R.Qty</h2></td>
                    <td class="rate"><h2>MRP</h2></td>
                    <td class="rate"><h2>Discount</h2></td>
                    <td class="subtotal"><h2>Amount</h2></td>
                    <td class="action"><h2>Action</h2></td>
                  </tr>
                  <tr
                    class="borderBottom"
                    v-for="(item, i) in returnInfo.sales_items"
                    v-if="returnInfo.sales_items.length > 0"
                    :key="item.id"
                  >
                    <td class="">
                      <p class="">{{ i + 1 }}</p>
                    </td>
                    <td class="">
                      {{ item.products.product_name }}
                      <!-- {{$store.getters.productItems}} -->
                      <!-- <Multiselect v-if="return_replace.length > 0 && return_replace[i].return_or_replace == 1"
                            class="form-control border customer_id" 
                            mode="single"
                            v-model="return_replace[i].replace_pro_id"
                            placeholder="Select product"   
                            :searchable="true" 
                            :filter-results="true"
                            :options="state.products"
                            :classes="multiclasses"
                            :close-on-select="true" 
                            :min-chars="1"
                            :resolve-on-load="false"
                          />   -->
                    </td>
                    <td class="">
                      {{ item.quantity }}
                    </td>
                    <td class="">
                      <!-- <input v-if="return_replace.length > 0 && return_replace[i].return_or_replace == '0'" type="number" class="qty form-control" v-model="return_replace[i].return_qty">  -->
                      <input
                        v-if="
                          return_replace.length > 0 &&
                          (return_replace[i].return_or_replace == '0' ||
                            return_replace[i].return_or_replace == '1')
                        "
                        type="number"
                        class="qty form-control"
                        min="1"
                        @keyup="checkReturnQuantity($event.target.value, i, item.quantity)"
                        @change="checkReturnQuantity($event.target.value, i, item.quantity)"
                        v-model="return_replace[i].return_qty"
                      />
                    </td>
                    <td class="">
                      <p class="">{{ item.mrp_price }}</p>
                    </td>
                    <td class="">
                      <p class="">{{ item.discount * item.quantity }}</p>
                    </td>
                    <td class="">
                      <p class="">{{ (item.quantity * item.mrp_price) - (item.discount * item.quantity) }}</p>
                    </td>
                    <td class="action" v-if="return_replace.length > 0">
                      <label
                        ><input
                          type="radio"
                          v-model="return_replace[i].return_or_replace"
                          value="1"
                          @change="itemReturnOrReplace($event.target.value, i)"
                        />Return</label
                      >
                      <!-- <label><input type="radio" v-model="return_replace[i].return_or_replace" value="1" @change="itemReturnOrReplace($event.target.value, i)">Replace</label>  -->
                    </td>
                  </tr>

                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.total_amount"
                  >
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><h2>Total Amount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.total_amount }}</h2>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                  >
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><h2>Discount</h2></td>
                    <td class="payment">
                      <h2>{{ Number(parseFloat(returnInfo.sales_items_sum_discount) + parseFloat(returnInfo.customer_discount)+ parseFloat(returnInfo.customer_group_discount)+ parseFloat(returnInfo.order_discount) )  }}</h2>
                    </td>
                  </tr>
                  <!-- <tr
                    class="return-replace-summary"
                    v-if="returnInfo.order_discount"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Special Discount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.order_discount }}</h2>
                    </td>
                  </tr> -->
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.order_vat || returnInfo.order_items_vat"
                  >
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><h2>VAT</h2></td>
                    <td class="payment">
                      <h2>
                        {{ returnInfo.order_vat + returnInfo.order_items_vat }}
                      </h2>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.grand_total"
                  >
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><p>Net Amount</p></td>
                    <td class="payment">
                      <p>{{ returnInfo.grand_total }}</p>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.paid_amount"
                  >
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><p>Paid Amount</p></td>
                    <td class="payment">
                      <p>{{ returnInfo.paid_amount }}</p>
                    </td>
                  </tr>
                  <tr class="return-replace-summary">
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2"><p>Return Amount</p></td>
                    <td class="payment">
                      {{ salesReturnAmountSumVal }}
                      <input
                        type="hidden"
                        v-model="sale_return_info.return_amount"
                        style="width: 100%"
                      />
                    </td>
                  </tr>
                  <tr class="return-replace-summary">
                    <td colspan="5"></td>
                    <td class="Rate" colspan="2">
                      <h2>Reason of return (Optional):</h2>
                    </td>
                    <td class="payment">
                      <textarea rows="1" v-model="sale_return_info.return_reason"></textarea>
                    </td>
                    <!-- <td colspan="2"></td>
                    <td class="Rate" colspan="2">
                      <h2>Reason of return (Optional):</h2>
                    </td>
                    <td class="payment">
                      <input
                        type="text"
                        v-model="sale_return_info.return_reason"
                      />
                    </td> -->
                  </tr>
                </table>
              </div>
              <!--End Table-->
            </div>
            <!--End InvoiceBot-->
          </div>
          <!--End Invoice-->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-return" @click="voidInvoice">
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i> </span
            >Void Invoice
          </button>
          <button
            type="submit"
            class="btn btn-return"
            @click="returnAndReplace"
          >
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Return
          </button>
        </div>
      </div>
    </Modal>

    <!-- Only Replace -->
    <Modal @close="saleReplaceModal()" :modalActive="saleReplaceModalActive">
      <div class="modal-content scrollbar-width-thin">
        <div class="modal-header">
          <h5>Replace</h5>
          <button
            @click="saleReplaceModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label for="customer_group_id" class="form-label"
                  >Invoice Number *</label
                >
                <input
                  type="text"
                  class="form-control border"
                  v-model="invoice_number"
                  placeholder="Invoice Number"
                  autocomplete="off"
                />
                <div class="invalid-feedback" v-if="popupError != ''">
                  {{ popupError }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" @click="findInvoice">
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Submit
          </button>
        </div>
      </div>
    </Modal>

    <Modal
      @close="saleReplacePageModal()"
      :modalActive="saleReplacePageModalActive"
      >
      <div class="modal-content scrollbar-width-thin return-replace-modal">
        <div class="modal-header">
          <h5>Replace</h5>
          <button
            @click="saleReplacePageModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body" id="printMe">
          <div v-if="returnInfo">
            <div id="mid">
              <div class="info">
                <p class="text-left" style="font-size: 14px">
                  Name: {{ returnInfo.customer.name }}<br />
                  Address: {{ returnInfo.customer.address }} <br />
                  Mobile: {{ returnInfo.customer.phone }}<br />
                  Srvd by: {{ returnInfo.created_by.name }}
                </p>
              </div>
            </div>
            <!--End Invoice Mid-->
            <div id="bot">
              <div id="table">
                <table>
                  <tr class="borderTop borderBottom">
                    <td class="hours">Sl</td>
                    <td class="item"><h2>Item Name</h2></td>
                    <td class="hours"><h2>Qty</h2></td>
                    <td class="hours"><h2>R.Qty</h2></td>
                    <td class="rate"><h2>MRP</h2></td>
                    <td class="subtotal"><h2>Amount</h2></td>
                    <td class="action"><h2>Action</h2></td>
                  </tr>
                  <tr
                    class="borderBottom"
                    v-for="(item, i) in returnInfo.sales_items"
                    v-if="returnInfo.sales_items.length > 0"
                    :key="item.id"
                  >
                    <td class="">
                      <p class="">{{ i + 1 }}</p>
                    </td>
                    <td class="">
                      {{ item.products.product_name }}
                      <!-- {{$store.getters.productItems}} -->
                      <Multiselect
                        v-if="
                          return_replace.length > 0 &&
                          return_replace[i].return_or_replace == 1
                        "
                        class="form-control border customer_id"
                        mode="single"
                        v-model="return_replace[i].replace_pro_id"
                        placeholder="Select product"
                        :searchable="true"
                        :filter-results="true"
                        :options="state.products"
                        :classes="multiclasses"
                        :close-on-select="true"
                        :min-chars="1"
                        :resolve-on-load="false"
                        @change="onChangeReplaceProduct($event, i)"
                      />
                    </td>
                    <td class="">{{ item.quantity }}</td>
                    <td class="">
                      <!-- <input v-if="return_replace.length > 0 && return_replace[i].return_or_replace == '0'" type="number" class="qty form-control" v-model="return_replace[i].return_qty">  -->
                      <input
                        v-if="
                          return_replace.length > 0 &&
                          (return_replace[i].return_or_replace == '0' ||
                            return_replace[i].return_or_replace == '1')
                        "
                        type="number"
                        class="qty form-control"
                        v-model="return_replace[i].return_qty"
                      />
                    </td>
                    <td class="">
                      <p class="">{{ item.mrp_price }}</p>
                      <p
                        v-if="
                          return_replace.length > 0 &&
                          return_replace[i].replace_pro_id != ''
                        "
                        class=""
                      >
                        {{ return_replace[i].replace_mrp_price }}
                      </p>
                    </td>
                    <td class="">
                      <p class="">{{ item.quantity * item.mrp_price }}</p>
                      <p
                        v-if="
                          return_replace.length > 0 &&
                          return_replace[i].replace_pro_id != ''
                        "
                        class=""
                      >
                        {{
                          return_replace[i].return_qty *
                          return_replace[i].replace_mrp_price
                        }}
                      </p>
                    </td>
                    <td class="action" v-if="return_replace.length > 0">
                      <!-- <label><input type="radio" v-model="return_replace[i].return_or_replace" value="0" @change="itemReturnOrReplace($event.target.value, i)">Return</label> -->
                      <label
                        ><input
                          type="radio"
                          v-model="return_replace[i].return_or_replace"
                          value="1"
                          @change="itemReturnOrReplace($event.target.value, i)"
                        />Replace</label
                      >
                    </td>
                  </tr>

                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.total_amount"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Total Amount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.total_amount }}</h2>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.customer_group_discount"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Discount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.customer_group_discount }}</h2>
                    </td>
                  </tr>
                  <!-- <tr
                    class="return-replace-summary"
                    v-if="returnInfo.order_discount"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Special Discount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.order_discount }}</h2>
                    </td>
                  </tr> -->
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.order_vat || returnInfo.order_items_vat"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>VAT</h2></td>
                    <td class="payment">
                      <h2>
                        {{ returnInfo.order_vat + returnInfo.order_items_vat }}
                      </h2>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.grand_total"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Net Amount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.grand_total }}</h2>
                    </td>
                  </tr>
                  <tr
                    class="return-replace-summary"
                    v-if="returnInfo.paid_amount"
                  >
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2"><h2>Paid Amount</h2></td>
                    <td class="payment">
                      <h2>{{ returnInfo.paid_amount }}</h2>
                    </td>
                  </tr>
                  <!-- <tr class="return-replace-summary"> 
                        <td colspan="4"></td>
                        <td class="Rate" colspan="2"><h2>Return Amount</h2></td>
                        <td class="payment"><input type="number" v-model="sale_return_info.return_amount" ></td>
                      </tr> -->
                  <tr class="return-replace-summary">
                    <td colspan="4"></td>
                    <td class="Rate" colspan="2">
                      <h2>Reason of return (Optional):</h2>
                    </td>
                    <td class="payment">
                      <input
                        type="text"
                        v-model="sale_return_info.return_reason"
                      />
                    </td>
                  </tr>
                </table>
              </div>
              <!--End Table-->
            </div>
            <!--End InvoiceBot-->
          </div>
          <!--End Invoice-->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-return" @click="saleReplace">
            <span v-show="invDisabled" :disabled="disabled">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
            Submit
          </button>
        </div>
      </div>
    </Modal>

    <Modal  @close="addConfirmModal()" :modalActive="confirmModalActive">
      <div class="modal-content scrollbar-width-thin confirm-window">
        <div class="modal-header">
          <h5>Invoice Preview</h5> 
          <button @click="addConfirmModal()" type="button" class="btn btn-default">
            X
          </button>
        </div>
        <div class="modal-body inv-perview" v-if="cartItems.length > 0">
          <div id="invoice-perview">
            <div id="top">
              <div class="row">
                <h2 class="text-center">{{ $store.getters.userData.user.outlet_name }}</h2>
                <p class="text-center cmb-5">
                  {{ $store.getters.userData.user.outlet_address }} 

                  <span v-if="invHeadPhone">  <br>  Mobile: {{ this.invHeadPhone }} </span>
                </p>
                  <h2 class="text-center ">INVOICE</h2>
                
              </div>
            </div>
            <!--End InvoiceTop-->
            <div id="mid">
              <div class="info" v-if="customer">
                <p class="text-left" style="font-size: 14px">
                  Customer: {{ customer.label }}<br />
                  Address: {{ customer.address }} <br />
                  Mobile: {{ customer.phone }}<br />
                  Srvd by: {{ $store.getters.userData.user.name }}<br />
                  Sales Point: {{ $store.getters.userData.user.outlet_name }}
                </p>
              </div>
            </div>
            <!--End Invoice Mid-->
            <div id="bot">
              <div id="table">
                <table >
                  <tr class="borderTop borderBottom">
                    <td class="item"><h2>Item Name</h2></td> 
                    <td class="hours"><h2>Qty</h2></td>
                    <td class="hours"><h2>WT</h2></td>
                    <td class="rate text-center"><h2>Rate</h2></td> 
                    <td class="rate text-center"><h2>Value</h2></td> 
                    <td class="rate text-center"><h2>Disc</h2></td>
                    <td class="subtotal text-right"><h2>Amount</h2></td>  
                  </tr>

                  <tr
                    class="service "
                    v-for="(item, i) in cartItems"
                    v-if="cartItems.length > 0"
                    :key="i"
                  >
                  <td class="item"><h2>{{ item.product_name }}</h2></td>
                    <td class="hours"><h2>{{ item.quantity }}</h2></td>
                    <td class="hours"><h2>{{ item.weight }}</h2></td> 
                    <td class="rate text-center"><h2 >{{ Number(item.mrp_price).toFixed(0) }}</h2></td>
                    <td class="rate text-center"><h2 >{{
                          item.uom == 5
                            ? parseFloat(
                                item.mrp_price * item.weight  
                              ).toFixed(0)
                            : parseFloat(
                                item.mrp_price * item.quantity 
                              ).toFixed(0)
                        }}</h2></td>
                    <td class="rate text-right">
                        <h2 class="text-right">{{
                          item.uom == 5
                            ? parseFloat(
                                 (item.discount * item.weight)
                              ).toFixed(0)
                            : parseFloat(
                                (item.discount *  item.quantity)
                              ).toFixed(0)
                        }} </h2></td>
                    <td class="subtotal"><h2 class="text-right">{{
                          item.uom == 5
                            ? parseFloat(
                                item.mrp_price * item.weight - (item.discount * item.weight)
                              ).toFixed(0)
                            : parseFloat(
                                item.mrp_price * item.quantity - (item.discount *  item.quantity)
                              ).toFixed(0)
                        }}</h2></td>
 
                  </tr>
                  <tr class="service " >   
                    <td class="text-right">Total &nbsp</td> 
                    <td class=""><h2>{{parseFloat(totalItemQty).toFixed(0)}} </h2> </td>
                    <td class=""><h2>{{parseFloat(totalItemWeight).toFixed(3)}} </h2></td>  
                    <td class="text-center"> </td>   
                    <td class="text-center"><h2>{{parseFloat(totalItemValue).toFixed(0)}}</h2> </td>   
                    <!-- <td class="text-right"><h2>{{parseFloat(totalItemDiscountValue).toFixed(0)}} </h2></td> -->
                    <td class="text-right"><h2>{{parseFloat(totalItemDiscountValue).toFixed(0)}} </h2></td>
                    <td class="text-right"><h2>{{parseFloat(totalItemNetValue).toFixed(0)}} </h2></td> 
                  </tr>
                  <tr v-if="totalCartValue" class="service">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="Rate" colspan="2"><h2>Total Amount</h2></td>
                    <td class="payment">
                      <h2 class="text-right">{{ Number(totalCartValue).toFixed(0) }}</h2>
                    </td>
                  </tr>
                  <tr class="service">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="Rate" colspan="2"><h2>Discount</h2></td>
                    <td class="payment ">
                      <h2 class="text-right">
                        {{
                          Number(
                            Number(
                      customer_discount + customer_group_discount
                    ) + special_discount + couponDiscoun + all_discount
                          ).toFixed(0)
                        }}
                      </h2>
                    </td>
                  </tr> 
                  <tr v-if="totalCartTax" class="service">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="Rate" colspan="2"><h2>VAT</h2></td>
                    <td class="payment">
                      <h2 class="text-right">{{ totalCartTax.toFixed(0) }}</h2>
                    </td>
                  </tr>
                  <tr v-if="netAmountCalculate">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="Rate" colspan="2"><h2>Net Amount</h2></td>
                    <td class="payment">
                      <h2 class="text-right">{{ Number(netAmountCalculate).toFixed(0) }}</h2>
                    </td>
                  </tr>
                </table>
              </div>
              <!--End Table-->

              <div id=" ">
                <h2 class="service borderBottom">Payment Description:</h2>
                <table>
                  <tr class="service borderBottom">
                    <td>Description</td>
                    <td class="text-right">Amount</td>
                  </tr>
                  <tr
                    class="service borderBottom"
                    v-for="(item, i) in pform.payments"
                    :key="item.id"
                  >
                    <td>
                      {{
                        item.paid_by.charAt(0).toUpperCase() +
                        item.paid_by.slice(1)
                      }}
                    </td>
                    <td class="text-right">{{ Number(item.amount).toFixed(0) }}</td>
                  </tr>
                  <tr class="service borderBottom" v-if="(netAmountCalculate - totalCollections) > 0">
                    <td>
                      Due
                    </td>
                    <td class="text-right">{{ Number(netAmountCalculate - totalCollections).toFixed(0) }}</td>
                  </tr>
                  <tr class="service borderBottom" v-else>
                    <td>
                      Return
                    </td>
                    <td class="text-right">{{ Math.abs(Number(netAmountCalculate - totalCollections)).toFixed(0) }}</td>
                  </tr>
                </table>
              </div>
              <!-- <div id="legalcopy">
                <h2 class="text-center borderBottom">Note:</h2>
                <p>Please Exchange Any Product Within 72 Hours</p>
              </div> -->
            </div>
            <!--End InvoiceBot-->
          </div>
          <!--End Invoice-->
        </div>
        <div class="modal-footer">
          <div class="col-md-12">
            <div class="row button-list pt-2">
              <div class="col-md-4" style="padding: 0">
                <div class="d-grid">
                  <button type="button" class="btn btn-sm btn-primary" @click="handleSaleHoldSubmit" >
                    Save
                  </button>
                </div>
              </div>
              <div class="col-md-4" style="padding: 0 5px">
                <div class="d-grid">
                  <button
                    type="button"
                    class="btn btn-sm btn-info"
                    @click="addConfirmModal()"
                  >
                    Add New Item
                  </button>
                </div>
              </div>
              <div class="col-md-4" style="padding: 0 5px">
                <div class="d-grid">
                  <button
                    type="button"
                    class="btn btn-sm btn-success"
                    @click="handleSubmit"
                    :disabled="btn_disabled"
                  >
                    <span v-show="isSubmit">
                      <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    Submit
                  </button>
                </div>
              </div>
            </div>
          </div>
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
        <div class="modal-body inv-perview" id="printMe">
          <div id="invoice-POS" v-if="invoice_info">
            <div id="top">
              <div class="row">
                <h3 class="text-center">{{ invoice_info.outlets?.name}}</h3>
                <p class="text-center cmb-5">
                 {{ invoice_info.outlets?.address}}
                 <span v-if="invHeadPhone"> <br> Mobile: {{ this.invHeadPhone }} </span>
                </p> 
                <p class="text-center" ><strong> INVOICE </strong></p> 
              </div>
            </div>
            <!--End InvoiceTop-->
            <div id="mid" class="fnt10">
              <div class="info" v-if="invoice_info">
                <p class="text-left" style="font-size: 14px">
                  Customer : {{ invoice_info.customer.name }}<br />
                  Address: {{ invoice_info.customer.address }} <br />
                  Mobile: {{ invoice_info.customer.phone }}<br />
                  Srvd by: {{ invoice_info.created_by.name }} <br>  
                </p> 
              </div>
              <table  id="table" style="width: 100% !important;">
                <tr class="service borderBottom">
                  <td> #{{ invoice_info.invoice_number }}</td>
                  <td class="text-right" style="text-align: right" >{{ invoice_info.created_at }}</td>
                </tr> 
              </table>

            </div>
            <!--End Invoice Mid-->
            <div id="bot" class="fnt10">
              <div id="table">
                <table  style="width: 100% !important;">
                  <tr class="  borderBottom borderTop">
                    <td class="item">Item Name</td>
                    <td class="hours">Qty</td>
                    <td class="hours">Wt</td>
                    <td class="rate text-center">MRP</td>
                    <td class="rate text-center">Value</td>
                    <td class="subtotal text-center">Amount</td>
                  </tr>

                  <tr
                    class="  borderBottom"
                    v-for="(item, i) in invoice_info.sales_items"
                    v-if="invoice_info.sales_items.length > 0"
                    :key="i"
                  >
                    <td class="item">{{ item.products.product_name }}</td>
                     
                    <td class="tableitem"> {{ item.quantity }} 
                    </td>
                    <td class="tableitem"> {{ item.weight }} 
                    </td>
                    <td class="tableitem" style="text-align: right"> {{ Number(item.mrp_price).toFixed(2) }} 
                    </td>
                    <td class="rate text-center"> {{
                          item.uom == 5
                            ? parseFloat(
                                item.mrp_price * item.weight  
                              ).toFixed(2)
                            : parseFloat(
                                item.mrp_price * item.quantity 
                              ).toFixed(2)
                        }} </td>
                    <td class="tableitem text-right" style="text-align: right"> 
                        {{
                          item.uom == 5
                            ? parseFloat(
                                item.mrp_price * item.weight - (item.discount*item.weight)
                              ).toFixed(2)
                            : parseFloat(
                                item.mrp_price * item.quantity - (item.discount*item.quantity)
                              ).toFixed(2)
                        }} 
                    </td>
                  </tr>
                  <tr class=" " v-if="invoice_info.total_amount">
                    <td></td>
                    <td></td> 
                    <td class="Rate" colspan="3"> Total Amount </td>
                    <td class="payment text-right" style="text-align: right" > {{ Number(invoice_info.total_amount).toFixed(2) }} 
                    </td>
                  </tr> 
                  <tr class=" " v-if="invoice_info.customer_group_discount" >
                    <td></td>
                    <td></td> 
                    <td class="Rate" colspan="3"> Discount </td>
                    <td class="payment text-right" style="text-align: right" > {{ Number(invoice_info.customer_group_discount).toFixed(2) }} 
                    </td>
                  </tr>
                  <!-- <tr class=" " v-if="invoice_info.order_discount">
                    <td></td>
                    <td></td>
                    <td class="Rate" colspan="3">Special Discount</td>
                    <td class="payment"> {{ invoice_info.order_discount } </td>
                  </tr> -->
                  <tr
                    class=" "  v-if="invoice_info.order_vat || invoice_info.order_items_vat">
                    <td></td>
                    <td></td> 
                    <td  colspan="3"> VAT </td>
                    <td class="text-right" style="text-align: right"> {{
                          Number(invoice_info.order_vat + invoice_info.order_items_vat).toFixed(2)
                        }} 
                    </td>
                  </tr>
                  <tr class=" " v-if="invoice_info.grand_total">
                    <td></td>
                    <td></td> 
                    <td class="Rate" colspan="3">Net Amount</td>
                    <td class="payment text-right" style="text-align: right"> {{ Number(invoice_info.grand_total).toFixed(2) }} </td>
                  </tr>
                  <tr class=" " v-if="invoice_info.paid_amount">
                    <td></td>
                    <td></td> 
                    <td class="Rate" colspan="3">Paid Amount</td>
                    <td class="payment text-right" style="text-align: right"> <string> {{ Number(invoice_info.paid_amount).toFixed(2) }} </string></td>
                  </tr>
                </table>
              </div>
              <!--End Table-->

              <div class="col-md-12"  >
                <p style="margin-bottom: 0px; margin-top:5px"> <strong>Payment Description :</strong> </p>
                <table  id="table" style="width: 100% !important;">
                  <tr class="service borderBottom">
                    <td>Description</td>
                    <td class="text-right" style="text-align: right" >Amount</td>
                  </tr>
                  <tr
                    class="service borderBottom"
                    v-for="(item, i) in invoice_info.payments"
                    v-if="invoice_info.payments.length > 0"
                    :key="item.id"
                  >
                    <td>{{ item.paying_by }}</td>
                    <td class="text-right" style="text-align: right">{{ Number(item.amount).toFixed(2) }}</td>
                  </tr>
                </table>
              </div>
              <div id="legalcopy" class="text-center">
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
              <!-- <div id="legalcopy">
                <h2 class="text-center borderBottom">Note:</h2>
                <p>Please Exchange Any Product Within 72 Hours</p>
              </div> -->

              <div id="legalcopy">
                <p class="borderTop"><strong>System By: </strong>Brainstorm Thing</p>
              </div>
            </div>
            <!--End InvoiceBot-->
          </div>
          <!--End Invoice-->
        </div>
        <div class="modal-footer">
          <div class="col-md-12">
            <div class="row button-list pt-2">
              <div class="col-md-4" style="padding: 0 5px">
                <div class="d-grid">
                  <button
                    type="button"
                    class="btn btn-sm btn-success"
                    @click="print('invoice-POS')"
                  >
                    Print
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Modal>

    <Modal
      @close="barcodeReaderModal()"
      :modalActive="barcodeReaderModalActive"
      >
      <div class="modal-content scrollbar-width-thin barcodeReaderModal">
        <div class="modal-header">
          <h5>Barcode Reader</h5>
          <button
            @click="barcodeReaderModal()"
            type="button"
            class="btn btn-default"
          >
            X
          </button>
        </div>
        <div class="modal-body">
            <!-- <span style="font-size: x-large" v-if="!libLoaded">Loading Library...</span> -->
          
          <div id="videoWindow" class="video"></div>
          <!-- <QuaggaScannerTest v-on:found="found" v-if="renderBarcodeReader"> </QuaggaScannerTest> -->
        
        </div>

        <div class="modal-footer">
          <div class="col-md-12">
            <div class="row button-list pt-2">
              <div class="col-md-4" style="padding: 0 5px">
                <div class="d-grid">
                  <button
                    type="button"
                    class="btn btn-sm btn-danger"
                    @click="barcodeReaderModal()"
                  >
                    Close
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { ref } from "vue";
import Form from "vform";
import axios from "axios";
import {
  reactive, 
  computed,
  inject,
  onMounted,
  getCurrentInstance,
  onUpdated, 
} from "vue";
import { useStore } from "vuex";
import { useToast } from "vue-toastification";
import Modal from "./../helper/Modal";
import BarcodeGenerator from "@/components/BarcodeGenerator.vue";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { ImageBarcodeReader } from "vue-barcode-reader";
 
import Quagga from 'quagga'; 

export default {
  name: "PosLeftbar",
  setup() {
    const store = useStore();
    const toast = useToast();
    const swal = inject("$swal");
    const app = getCurrentInstance();
    const app2 = app.appContext.config.globalProperties;
    const count = ref(0);
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
      // banks: [
      //   { id: 1, name: "City Bank" },
      //   { id: 2, name: "DBBL Bank" },
      //   { id: 3, name: "Eastern Bank" },
      // ],
      products: store.getters.productItems.map(
        ({ product_id, product_stock_id, product_name, expires_date }) => ({
          label: product_name + "(" + expires_date + ")",
          value: product_id + "___" + product_stock_id,
        })
      ),
    });

    // let searchIteam = ref('')
    let disabled = ref("");
    let disabledCBtn = ref("");
    const paymentModalActive = ref(false);
    const customerModalActive = ref(false);
    const discountModalActive = ref(false);
    const confirmModalActive = ref(false);
    const rePrintModalActive = ref(false);
    const returnReplaceModalActive = ref(false);
    const returnReplacePageModalActive = ref(false);
    const saleReplaceModalActive = ref(false);
    const saleReplacePageModalActive = ref(false);
    const modalActive = ref(false);
    const taxModalActive = ref(false);
    const invoiceModalActive = ref(false);
    const barcodeReaderModalActive = ref(false);

    const paymentModal = () => {
      paymentModalActive.value = !paymentModalActive.value;
    };
    const taxModal = () => {
      taxModalActive.value = !taxModalActive.value;
    };
    const customerModal = () => {
      customerModalActive.value = !customerModalActive.value;
    };
    const addDiscountModal = () => {
      discountModalActive.value = !discountModalActive.value;
    };
    const addConfirmModal = () => {
      confirmModalActive.value = !confirmModalActive.value;
    };
    const rePrintModal = () => {
      rePrintModalActive.value = !rePrintModalActive.value;
    };
    const returnReplaceModal = () => {
      returnReplaceModalActive.value = !returnReplaceModalActive.value;
    };
    const returnReplacePageModal = () => {
      returnReplacePageModalActive.value = !returnReplacePageModalActive.value;
    };

    const saleReplaceModal = () => {
      saleReplaceModalActive.value = !saleReplaceModalActive.value;
    };
    const saleReplacePageModal = () => {
      saleReplacePageModalActive.value = !saleReplacePageModalActive.value;
    };

    const invoiceModal = () => {
      invoiceModalActive.value = !invoiceModalActive.value;
    };

    const barcodeReaderModal = () => {
      barcodeReaderModalActive.value = !barcodeReaderModalActive.value;
    };

    let customer_id = computed(() => {
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].customer_id : '';
    });
    let orderedDiscount = computed(() => {
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].order_discount : 0;
    });
    let orderedTax = computed(() => {
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].order_tax : 0;
    }); 
    const totalCartValue = computed(() => {
      return store.getters.cartItems.reduce(function (total, item) { 
        if (item.uom == 5) {
          return total + item.mrp_price * item.weight;
        } else {
          return total + item.mrp_price * item.quantity;
        }
      }, 0);
    });
    const cartItemDiscount = computed(() => {
      return store.getters.cartItems.reduce(function (total, item) {
        return total + item.item_discount * item.quantity;
      }, 0);
    }); 
    const totalCartTax = computed(() => {
      return store.getters.cartItems.reduce(function (total, item) {
        return total + item.tax * item.quantity;
      }, 0);
    });
    const pointToAmount = computed(() => {
      //return state.points_setting.cart_price_rate / state.points_setting.cart_points_rate * this.customer.points
      return 100;
    });
    const convartRate = computed(() => {
      return (
        state.points_setting.cart_price_rate /
        state.points_setting.cart_points_rate
      );

      // console.log('points_setting',state.points_setting)
      //return 100
    });
    const all_discount = computed(() => {
      return store.getters.cartItems.reduce(function (total, item) {
        return total + item.discount * item.quantity;
      }, 0);
    });
    // const selectProduct = (item) => {
    //   store.dispatch('addCartItem',item)
    //   selecteditem.value = item
    //   searchIteam.value = ''
    // }
    const clearAllCartItems = () => {
      swal({
        title: "Are you sure?",
        text: "You want clear cart list!",
        showCancelButton: true,
        confirmButtonCategory: "#3085d6",
        cancelButtonCategory: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          store.dispatch("removeAllCartItems");
          // toast("Cart list cleared!");
          // toast.info("Cart list cleared");
          // toast.success("Cart list cleared");
          // toast.error("Cart list cleared");
          toast.warning("Cart list cleared");
        } else {
        }
      });
    };
    let selecteditem = ref("");
    let multiclasses = {
      clear: "",
      clearIcon: "",
    };

    const fetchCustomers = () => {
      axios.get(app2.apiUrl + "/customers", app2.headers).then((res) => { 
        state.customers = res.data.data.map(
          ({
            id,
            name,
            discount_percent,
            customer_group_discount,
            customer_group_id,
            customer_group_name,
            available_point,
            address,
            phone,
          }) => ({
            label: name,
            value: id,
            discount: discount_percent,
            group_discount: customer_group_discount,
            customer_group_id: customer_group_id,
            customer_group_name: customer_group_name,
            points: available_point,
            address: address,
            phone: phone,
          })
        );
      });
    };

    const districtChange = (event) => {
      axios
        .get(
          app2.apiUrl + "/district-areas/" + event.target.value,
          app2.headers
        )
        .then((res) => {
          state.areas = res.data.data.areas;
        });
    };

    
    /** For Barcode Scan */
    const resultValue = ref(null);
    const libLoaded = ref(false);
    const bShowScanner = ref(false);
    /** Barcode Scan  */

    onMounted(async () => {
      await fetchCustomers();
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

      // try {
      //   //Load the library on page load to speed things up.
      //   await BarcodeScanner.loadWasm();
      //   libLoaded.value = true;
      //   showScanner();
      // } catch (ex) {
      //   alert(ex.message);
      //   throw ex;
      // }

    });

    const showScanner = () => {
      bShowScanner.value = true;
    };
    const appendMessage = (message) => {
      switch (message.type) {
        case "result":
          // resultValue.value = message.format + ": " + message.text;
          resultValue.value = message.text;
          // if(message.text) {
          //   bShowScanner.value = false;
          // }
          break;
        case "error":
          resultValue.value = message.msg;
          break;
        default:
          break;
      }
    };

    onUpdated(() => {
      //console.log('onUpdated')
      //console.log(document.getElementById('count').textContent)
    });
    // onUnmounted(() => {
    //   console.log('onUnmounted')
    // })
    // onBeforeMount(() => {
    //   console.log('onBeforeMount')
    // })
    // onBeforeUnmount(() => {
    //   console.log('onBeforeUnmount')
    // })
    // onBeforeUpdate(() => {
    //   if(totalCartValue.value > 0){
    //     disabled.value = false;
    //   }else{
    //     disabled.value = true;
    //   }
    // })
    return {
      state,
      paymentModalActive,
      paymentModal,
      customerModalActive,
      customerModal,
      taxModal,
      taxModalActive,
      fetchCustomers,
      cartItemDiscount,
      all_discount,
      addDiscountModal,
      discountModalActive,
      confirmModalActive,
      orderedDiscount,
      orderedTax,
      districtChange,
      addConfirmModal,
      rePrintModal,
      rePrintModalActive,
      returnReplaceModal,
      returnReplaceModalActive,
      returnReplacePageModalActive,
      returnReplacePageModal,
      saleReplaceModal,
      saleReplaceModalActive,
      saleReplacePageModalActive,
      saleReplacePageModal,
      invoiceModal,
      invoiceModalActive,
      barcodeReaderModal,
      barcodeReaderModalActive,
      // searchIteam,
      count,
      totalCartValue,
      pointToAmount,
      convartRate,
      totalCartTax,
      // searchProducts,
      // selectProduct,
      clearAllCartItems,
      disabled,
      disabledCBtn,
      selecteditem,
      multiclasses,
      // For Barcode Scan
      resultValue,
      libLoaded,
      bShowScanner,
      showScanner,
      appendMessage,
    };
  },
  components: {
    Modal,
    BarcodeGenerator,
    StreamBarcodeReader,
    ImageBarcodeReader,
    // BarcodeScannerLoad,
    // QuaggaScannerTest,
  },
  data() {
    return {
      getItems: "",
      customer: "",
      invoice_info: "",
      hold_data: "",
      invoice_number: "",
      customer_points: 0,
      isSubmit: false,
      couponSubmit: false,
      invDisabled: false,
      items: [],
      outlets: [],
      units: [],
      editMode: false,
      more_payemnts: [],
      window: {
        width: 0,
        height: 0,
      },
      discountOverwrite: 0,
      itemsDiscount: 0,
      enabledDiscount: false,
      submitDisable: true,
      special_discount: 0,
      errors: "",
      popupError: "",
      btn_disabled: false,
      paying_by: [
        { value: "cash", name: "Cash", disabled: false },
        { value: "gift_card", name: "Gift Card", disabled: false },
        { value: "redeem_point", name: "Redeem", disabled: false },
        { value: "CC", name: "Credit Card", disabled: false },
        { value: "mfs", name: "Mobile wallet", disabled: false },
        { value: "other", name: "Other", disabled: false },
      ],
      cform: new Form({
        id: "",
        customer_code: "C" + Math.floor(Math.random() * 100000), //'C1001',
        company_id: 0,
        customer_group_id: 1,
        name: "",
        email: "",
        phone: "",
        address: "",
        dob: "",
        district_id: "0",
        area_id: "0",
        postal_code: "",
        discount_percent: 0,
        wholesale_customer: 0,
        sale_without_vat: 0,
        credit_customer: 0,
        store_customer: 0,
        status: 1,
        customer_receivable_account: '',
      }),
      user: new Form({
        email: "",
        password: "",
      }),
      form: new Form({
        order_discount: 0,
        order_vat: 0,
        customer_id: 1,
        coupons_code: "",
      }),
      pform: new Form({
        order_discount: 0,
        order_vat: 0,
        order_items_vat: 0,
        sale_note: "",
        staff_note: "",
        customer_id: 1,
        items: "",
        total_amount: 0,
        grand_total: "",
        customer_discount: 0,
        customer_group_discount: 0,
        customer_group_id: '',
        customer_group_name: '',
        total_collect_amount: 0,
        paid_amount: 0,
        return_amount: 0,
        order_discount_value: 0,
        status: "",
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
      searchIteam: "",
      arrowCounter: -1,
      searchProduct: [],
      singleProductItem: "",
      couponDiscoun: 0,
      eventTimeForScanner: "",
      barcode: "",
      returnInfo: "",
      return_replace: [],
      sale_return_info: { return_amount: "", return_reason: "" },
      renderBarcodeReader: false,
      barcoderRrenderCounter: 0,
    };
  },
  props: {
    props: ["cartItem"],
  },
  methods: {
    replaceItem: function (item_id) {},

    // Barcode Reader 
    startRead() {
        if(this.barcodeReaderModalActive) {
          Quagga.init({
            inputStream : {
              name : "Live",
              type : "LiveStream",
              target: document.querySelector('#videoWindow')    // Or '#yourElement' (optional)
            },
            decoder : {
              // readers : ["code_128_reader"]
              readers : ["ean_reader"],
              // multiple: true,
            }
          }, function(err) {
              if (err) {
                  // console.log(err);
                  return
              }
              
              // console.log("Initialization finished. Ready to start");
              // console.log("Quagga.start()", Quagga.start());
              Quagga.start();
          });
        }else{
          Quagga.stop();
        }
        this.barcoderRrenderCounter++;
        this.renderBarcodeReader = true;

    },

    stopRead() {
        // console.log("this.barcoderRrenderCounter", this.barcoderRrenderCounter);
        for(var i = 0; i<=this.barcoderRrenderCounter; i++) {
          Quagga.stop(); 
          // console.log("close data");
        }
        this.barcoderRrenderCounter = 0;
        this.renderBarcodeReader = false;
    }, 

    foundBarcode(result) {
      if (result) { 
        this.searchIteam = result.code;
        this.barcodeReaderModalActive = false;
        this.renderBarcodeReader = false;
        this.stopRead();
        setTimeout(() => {
          var checkNumber = this.searchIteam.toLowerCase();
          var first2digit = String(checkNumber).slice(0, 2);
          var weightFigure = "";
          var product_weight = "";
          if (Number(first2digit) == 99) {
            this.searchIteam = String(checkNumber).slice(2, 7);
            weightFigure = String(checkNumber).slice(-6, -1);
            product_weight = parseFloat(Number(weightFigure) / 1000).toFixed(3);
          }

          var filtered = this.$store.getters.productItems.filter((item) => {
            let item_data = item.product_name + " (" + item.product_code + ")";
            return item_data.toLowerCase().match(this.searchIteam.toLowerCase());
          });
          if (filtered.length == 1) {
            filtered[0].weight = product_weight;
            this.setResult(filtered[0]);
            this.searchIteam = "";
            this.searchProduct.splice(0);
          } else {
            this.$toast.warning("Product not found!");
            this.searchIteam = "";
            this.searchProduct.splice(0);
          } 
        }, 500);
      }
    },

    findInvoice: function (type="") {
      this.popupError = "";
      this.invDisabled = true;
      this.return_replace = [];
      if(type == "return") {
        var postEvent = axios.get(
          this.apiUrl + "/saleInfo?invoice_number=" + this.invoice_number+ '&type='+type,
          this.headerjson
        );
      }else {
        postEvent = axios.get(
          this.apiUrl + "/saleInfo?invoice_number=" + this.invoice_number,
          this.headerjson
        );
      }
      postEvent
        .then((res) => {
          this.invDisabled = false;
          if (res.status == 200) {
            if (res.data.data.length > 0) {
              if (this.returnReplaceModalActive) {
                this.popupError = "";
                this.returnReplaceModalActive = false;
                this.returnReplacePageModalActive = true;
                //this.invoiceModal()
                //this.invoice_info = res.data.data[0];
                this.returnInfo = res.data.data[0]; 
                res.data.data[0].sales_items.forEach((item) => {
                  const newObj = {
                    item_id: item.id,
                    pro_id: item.product_id,
                    replace_pro_id: "",
                    new_pro_id: "",
                    replace_stock_id: "",
                    item_qty: item.quantity,
                    return_qty: "",
                    replace_mrp_price: item.mrp_price,
                    replace_cost_price: 0,
                    replace_discount: item.discount,
                    replace_vat: 0,
                    replace_vat_id: 0,
                    return_or_replace: "",
                  };
                  this.return_replace.push(newObj);
                });
              } else if (this.saleReplaceModalActive) {
                this.popupError = "";
                this.saleReplaceModalActive = false;
                this.saleReplacePageModalActive = true;
                //this.invoiceModal()
                //this.invoice_info = res.data.data[0];
                this.returnInfo = res.data.data[0]; 
                res.data.data[0].sales_items.forEach((item) => {
                  const newObj = {
                    item_id: item.id,
                    pro_id: item.product_id,
                    replace_pro_id: "",
                    new_pro_id: "",
                    replace_stock_id: "",
                    item_qty: item.quantity,
                    return_qty: "",
                    replace_mrp_price: item.mrp_price,
                    replace_cost_price: 0,
                    replace_discount: item.discount,
                    replace_vat: 0,
                    replace_vat_id: 0,
                    return_or_replace: "",
                  };
                  this.return_replace.push(newObj);
                });
              } else if (this.rePrintModalActive) {
                this.popupError = "";
                this.rePrintModalActive = false;
                this.invoiceModal();
                //this.returnInfo = res.data.data[0];
                this.invoice_info = res.data.data[0];
              }
            } else {
              this.popupError = "Please enter valid invoice number";
              //this.$toast.error('Please enter valid invoice number');
            }
          } else {
            this.popupError = res.data.message;
            // this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          // console.log("err", err);
          this.invDisabled = false;
          this.popupError = err.response.data.message;

          setTimeout(() => {
              this.popupError = "";
          }, 3000)
          // this.$toast.error(err.response.data.message);
        });
    },

    itemReturnOrReplace: function (value, index) {
      // 0 = Return
      // 1 = Replace
      //if(value ==  0) {
      this.return_replace[index].replace_pro_id = "";
      this.return_replace[index].return_qty = "";

      //}
    },

    checkReturnQuantity: function (value, index, sold_quantity) {
      if(value > sold_quantity) {
        this.return_replace[index].return_qty = "";
        this.$toast.error("Return quantity can't getter then sold quantity!");
      }


    },

    onChangeReplaceProduct: function (event, index) {
      var id_value = event;
      var separator = id_value.split("___");
      var sproduct_id = separator[0];
      var stock_id = separator[1];
      var product_item = this.$store.getters.productItems.find(
        ({ product_id, product_stock_id }) =>
          product_id == sproduct_id && product_stock_id == stock_id
      ); 

      // this.return_replace[index].replace_pro_id = product_item.product_id;
      this.return_replace[index].new_pro_id = product_item.product_id;
      this.return_replace[index].return_qty = "";
      // this.return_replace[index].replace_stock_id = product_item.product_stock_id;
      this.return_replace[index].replace_stock_id = product_item.product_stock_id;
      this.return_replace[index].replace_mrp_price = product_item.mrp_price;
      this.return_replace[index].replace_cost_price = product_item.cost_price;
      this.return_replace[index].replace_discount = product_item.item_discount;
      this.return_replace[index].replace_vat = product_item.tax;
      this.return_replace[index].replace_vat_id = product_item.product_tax;
    },

    // Void Invoice 
    voidInvoice: function () {
      var rrdata = {
        sale_return_info: this.sale_return_info,
        returnInfo: this.returnInfo,
        return_replace: this.return_replace,
      };

      this.$swal({
          title: 'Are you sure?',
          text: "You want void this invoice!", 
          showCancelButton: true,
          confirmButtonCategory: '#3085d6',
          cancelButtonCategory: '#d33',
          confirmButtonText: 'Confirm'
      }).then((result) => { 
          if(result.value) {

            var postEvent = axios.post(
              this.apiUrl + "/sale_returns_void",
              rrdata,
              this.headerjson
            );

            postEvent
            .then((res) => {
              this.returnReplacePageModal();
              this.sale_return_info = '';
              this.returnInfo = '';
              this.return_replace = [];
              this.$toast.success(res.data.message);
            })
            .catch((err) => {
              this.$toast.error(err);
              //console.log('err', err)
            });
          }
      });
    },

    returnAndReplace: function () {
      var rrdata = {
        sale_return_info: this.sale_return_info,
        returnInfo: this.returnInfo,
        return_replace: this.return_replace,
      };
      
      this.$swal({
          title: 'Are you sure?',
          text: "You want return sales item!", 
          showCancelButton: true,
          confirmButtonCategory: '#3085d6',
          cancelButtonCategory: '#d33',
          confirmButtonText: 'Confirm'
      }).then((result) => { 

          if(result.value) {
            var postEvent = axios.post(
              this.apiUrl + "/sale_returns",
              rrdata,
              this.headerjson
            );
            postEvent
              .then((res) => {
                this.returnReplacePageModal();
                //console.log("res.data.data", res.data.data);
                this.sale_return_info = '';
                this.returnInfo = '';
                this.return_replace = [];
                this.$toast.success(res.data.message);

              })
              .catch((err) => {
                // this.$toast.error(err);
                this.$toast.error(err.response.data.message);
              });
          }

      });

    },
    saleReplace: function () {


      var rrdata = {
        sale_return_info: this.sale_return_info,
        returnInfo: this.returnInfo,
        return_replace: this.return_replace,
      };
      var postEvent = axios.post(
        this.apiUrl + "/sale_replaces",
        rrdata,
        this.headerjson
      );
      postEvent
        .then((res) => {
          // this.saleReplacePageModal();

          //console.log("res.data.data", res.data.data);
          //this.returnInfo='';
          //this.$toast.success(res.data.message);
        })
        .catch((err) => {
          this.$toast.error(err);
        });
    },

    /** Check Stock Qty */
    checkStockQty: function(product_item) {
      if(product_item.quantity > product_item.stock_quantity) {
        this.$toast.error("Your item quantity can't getter than "+ product_item.stock_quantity, {position: "top-left"});
        product_item.quantity = 1;
      }
    },

    checkItem: function (item) {
      for (var i = 0; i < this.pform.payments.length; i++) {
        if (this.pform.payments[i].paid_by == item) {
          return true;
        }
      }
    },
    onkeyPress: function (field) {
      for (var k in this.errors) {
        // Loop through the object
        if (k == field) {
          // If the current key contains the string we're looking for
          delete this.errors[k]; // Delete obj[key];
        }
      }
    },
    validateNumber: (event) => {
      var RE = /^[0-9]*\.?[0-9]*$/;
      const toast = useToast();
      let keyCode = event.keyCode;
      const charCode = String.fromCharCode(event.keyCode);
      if (!RE.test(charCode)) {
        toast.warning("Please press valid amount");
        event.preventDefault();
      }
    },
    checkAboveAmount: function (event, i) {
      // console.log("jhdshf", i);
      // Check Cash Customer due 
      if(this.form.customer_id ==1){
        if(this.totalCollections > Number(this.netAmountCalculate)){
          this.submitDisable = false;   
        }else{
          this.submitDisable = true;
        } 
      }
      // Check Cash Customer due
      let keyCode = event.keyCode;
      var mask = document.getElementById(
        "payingby_" + event.target.id.split("_")[1]
      );
      if (mask.value != "cash") { 
        if (Number(this.netAmountCalculate) < Number(event.target.value)) {
          event.target.value = this.netAmountCalculate;
          if(i == 's') {
            this.pform.payments[0].amount = this.netAmountCalculate;
          }
          this.$toast.warning("Not allow max amount "); 
        }
      }
    },

    morePayemnt: function () {
      let paymentsPaid_by = [];
      for (var i = 0; i < this.pform.payments.length; i++) {
        paymentsPaid_by.push(this.pform.payments[i].paid_by);
      }
      let paid_by = "";
      for (var p = 0; p < this.paying_by.length; p++) {
        if (paymentsPaid_by.indexOf(this.paying_by[p].value) == -1) {
          paid_by = this.paying_by[p].value;
          break;
        }
      }
      paymentsPaid_by.splice(0);
      let r = (Math.random() + 1).toString(36).substring(7);
      const newPayment = {
        id: r,
      };
      // console.log('newPayment', newPayment)
      if (!paid_by) {
        this.$toast.error("Payment type not available");
      } else {
        const newObj = {
          amount: 0,
          paid_by: paid_by,
          gift_card: "",
          bank_id: "",
          card_reference_code: "",
          wallet_id: "",
          payment_note: "",
          redeem_point: "",
        };
        this.more_payemnts.push(newPayment);
        this.pform.payments.push(newObj);
      }
    },

    morePayemntRemove: function (index) {
      this.more_payemnts.splice(index, 1);
      this.pform.payments.splice(index + 1, 1);
    },

    handleLogin: function () {
      this.isSubmit = true;
      var postEvent = axios.post(
        this.apiUrl + "/auth/unlockDiscount",
        this.user,
        this.headerjson
      );
      postEvent
        .then((res) => {
          this.isSubmit = false;
          this.disabled = false;
          // console.log("enabledDiscount", res.data.data);
          if (res.data) {
            if (res.data.data.discount) {
              this.enabledDiscount = true;
            } else {
              this.$toast.error("Discount are not allowed for you");
            }
          }
        })
        .catch((err) => {
          this.isSubmit = false;
          this.disabled = false;
          this.$toast.error(err.response.data.message);
        });
    },
    
    handleSubmit: function () { 

      this.isSubmit = true;
      this.btn_disabled = true;
      this.pform.items = JSON.parse(
        JSON.stringify(this.$store.getters.cartItems)
      );
      this.pform.order_discount = this.form.order_discount;
      this.pform.customer_id = this.form.customer_id;
      this.pform.hold_sale_info = this.holdSaleInfo;

      // Return Amount  
      let extraMoney = Number(Math.round(this.pform.grand_total) - Math.round(this.totalCollections));
      if (extraMoney < 0) { 
        this.pform.paid_amount = this.pform.grand_total; 
        this.pform.status = "paid";
        if(!this.checkItem('cash')){
          // console.log(this.totalCollections)
          // console.log(Math.round(this.pform.grand_total))
           if(this.totalCollections <= Math.round(this.pform.grand_total)){
            // Continue
           }else{
            this.$toast.warning('Collection amount violate');
            this.isSubmit = false;
            this.btn_disabled = false;
            return false;
           }
        }else{
          let checkCollectionAmount = 0;
          for (var i = 0; i < this.pform.payments.length; i++) {
            if (this.pform.payments[i].paid_by != 'cash') {
               checkCollectionAmount += parseFloat(this.pform.payments[i].amount);
            }
          }
          // console.log(checkCollectionAmount)
          // console.log(this.pform.grand_total)
          if(checkCollectionAmount >= this.pform.grand_total){
            this.$toast.warning('Collection amount violate');
            this.isSubmit = false;
            this.btn_disabled = false;
            return false;
          } else{
            // Continue
          }
        }
      } else if(extraMoney == 0){
        this.pform.status = "paid"; 
        this.pform.paid_amount = this.totalCollections; 
        this.pform.total_collect_amount = this.totalCollections; 
      } else if ((Math.round(this.pform.grand_total) > Math.round(this.totalCollections)) &&  (Math.round(this.totalCollections) != 0)) {
        this.pform.status = "partial"; 
        this.pform.paid_amount = this.totalCollections;
        this.pform.total_collect_amount = this.totalCollections;
        extraMoney = 0;
      }  else {
        this.pform.status = "due"; 
        this.pform.paid_amount = this.totalCollections;
        this.pform.total_collect_amount = this.totalCollections;
        extraMoney = 0;
      }  
      this.pform.return_amount = Math.abs(extraMoney);  
      // console.log('this.pform', this.pform)
      //return false;
      //Return Amount
      if (this.editMode) {
        var postEvent = axios.put(
          this.apiUrl + "/sales/" + this.form.id,
          this.pform,
          this.headerjson
        );
      } else {
        var postEvent = axios.post(
          this.apiUrl + "/sales",
          this.pform,
          this.headerjson
        );
      }
      postEvent
        .then((res) => {
          this.isSubmit = false;
          this.btn_disabled = false;
          if (res.status == 200) {
            this.invoice_info = res.data.data[0];
            this.invoiceModal(); 
            if (this.invoiceModalActive) {
              setTimeout(() => {
                this.print("invoice-POS");
              }, 3000);
            } 
            if (this.paymentModalActive) {
              this.paymentModal();
            }
            if (this.confirmModalActive) {
              this.addConfirmModal();
            }
            if(Math.round(this.pform.return_amount) !=0){
              this.$swal({
                html:
                  "<h3 style='color:red'>" +
                  Math.round(this.pform.return_amount).toLocaleString() +
                  "</h3>",
                title: "Return Amount",
                confirmButtonCategory: "#3085d6",
                cancelButtonCategory: "#d33",
                confirmButtonText: "Ok!",
              });
            }
            this.more_payemnts = [];
            this.pform.reset();
            this.cform.reset();
            this.form.reset();
            this.$toast.success(res.data.message);
            this.$store.dispatch("removeAllCartItems");
            axios
              .get(this.apiUrl + "/posproducts", this.headers)
              .then((res) => {
                this.$store.commit("UPDATE_PRODUCT_ITEMS", res.data.data);
            });
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.$toast.error(err);
          this.isSubmit = false;
          this.btn_disabled = false;
        });
    },
    handleSaleHoldSubmit: function () {
      this.isSubmit = true;
      this.pform.items = JSON.parse(
        JSON.stringify(this.$store.getters.cartItems)
      );
      this.pform.order_discount = this.form.order_discount;
      this.pform.customer_id = this.form.customer_id;
      this.pform.hold_sale_info = this.holdSaleInfo;
      if (this.editMode) {
        var postEvent = axios.put(
          this.apiUrl + "/hold_sales/" + this.form.id,
          this.pform,
          this.headerjson
        );
      } else {
        var postEvent = axios.post(
          this.apiUrl + "/hold_sales",
          this.pform,
          this.headerjson
        );
      }
      postEvent
        .then((res) => {
          this.isSubmit = false;
          this.disabled = false;
          if (res.status == 200) {
            if (this.paymentModalActive) {
              this.paymentModal();
            }
            if (this.confirmModalActive) {
              this.addConfirmModal();
            }
            this.pform.reset();
            this.cform.reset();
            this.form.reset();
            this.$toast.success(res.data.message);
            this.$store.dispatch("removeAllCartItems");
            axios
              .get(this.apiUrl + "/posproducts", this.headers)
              .then((res) => {
                this.$store.commit("UPDATE_PRODUCT_ITEMS", res.data.data);
              });
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.isSubmit = false;
          this.disabled = false;
        });
    },
    onArrowDown: function (event) {
      if (this.searchProduct.length > 0) {
        this.arrowCounter =
          event.code == "ArrowDown" ? ++this.arrowCounter : --this.arrowCounter;
        if (this.arrowCounter >= this.searchProduct.length)
          this.arrowCounter = this.arrowCounter % this.searchProduct.length;
        else if (this.arrowCounter < 0)
          this.arrowCounter = this.searchProduct.length + this.arrowCounter;
        this.selectProduct(this.searchProduct[this.arrowCounter]);
        this.singleProductItem = this.searchProduct[this.arrowCounter];
      }
    },
    onArrowUp: function (event) {
      if (this.searchProduct.length > 0) {
        this.arrowCounter =
          event.code == "ArrowUp" ? --this.arrowCounter : ++this.arrowCounter;
        if (this.arrowCounter >= this.searchProduct.length)
          this.arrowCounter = this.arrowCounter % this.searchProduct.length;
        else if (this.arrowCounter < 0)
          this.arrowCounter = this.searchProduct.length + this.arrowCounter;
        this.selectProduct(this.searchProduct[this.arrowCounter]);
        this.singleProductItem = this.searchProduct[this.arrowCounter];
      }
    },

    selectProduct(item) {
      this.searchIteam = item.product_name + " (" + item.product_code + ")";
    },

    setResult(item) {
      // this.arrowCounter = -1;
      this.$store.dispatch("addCartItem", item);
      // this.searchIteam = item.product_name +' ('+ item.product_code + ')';
      this.searchIteam = "";
      this.searchProduct.splice(0);
    },

    addProductToCart() {
      let matches = 0;
      // let findProduct = this.$store.getters.productItems.filter(item => {
      //   if (item.product_name.toLowerCase().includes(this.searchIteam.toLowerCase()) && matches < 10) {
      //     matches++
      //     return item
      //   }else if (item.product_code.toLowerCase().includes(this.searchIteam.toLowerCase()) && matches < 10) {
      //     matches++
      //     return item
      //   }
      // });
      if (this.singleProductItem != "") {
        this.$store.dispatch("addCartItem", this.singleProductItem);
      } else {
        this.$toast.warning("Product not found!");
      }
      this.singleProductItem = "";
      this.searchIteam = "";
      this.searchProduct = [];
    },

    inputChanged(event) {
      setTimeout(() => {
        if (event.code == "ArrowUp" || event.code == "ArrowDown") return;
        this.searchProduct = [];
        if (event.code == "Enter") return;
        if (this.searchIteam == "") return;

        var checkNumber = this.searchIteam.toLowerCase();
        var first2digit = String(checkNumber).slice(0, 2);
        var weightFigure = "";
        var product_weight = "";
        if (Number(first2digit) == 99) {
          this.searchIteam = String(checkNumber).slice(2, 7);
          weightFigure = String(checkNumber).slice(-6, -1);
          product_weight = parseFloat(Number(weightFigure) / 1000).toFixed(3);
        }

        // console.log("this.searchIteam", this.searchIteam);
        // console.log("weightFigure", weightFigure);

        var filtered = this.$store.getters.productItems.filter((item) => {
          let item_data = item.product_name + " (" + item.product_code + ")";
          return item_data.toLowerCase().match(this.searchIteam.toLowerCase());
        });
        if (filtered.length == 1) {
          filtered[0].weight = product_weight;
          // console.log("filtered[0]", filtered[0]);
          this.setResult(filtered[0]);
          this.searchIteam = "";
          this.searchProduct.splice(0);
        }

        // console.log("filtered data", filtered);
        // console.log("filtered data push", ...filtered);
        this.searchProduct.push(...filtered);
        let match_data = this.searchProduct[this.arrowCounter]
          ? this.searchProduct[this.arrowCounter].product_name +
            " (" +
            this.searchProduct[this.arrowCounter].product_code +
            ")"
          : "";
        if (match_data != this.searchIteam) {
          this.arrowCounter = -1;
        }
      }, 500);
    },

    toggleReadonly(event) {
      event.preventDefault();
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");
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
      
      // console.log("this.event value",event.target.value);
      //console.log("this.pform.payments[e].paid_by", this.pform.payments[e].paid_by);

      if (this.pform.payments[e].paid_by != "cash") {
        ///  console.log("this.pform.payments[e].paid_by", this.pform.payments[e].paid_by);
        this.paymentModal();
      }
      // this.pform.payments[0].paid_by = "cash";
      this.pform.payments[0].paid_by = this.pform.payments[e].paid_by;
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

    applyCoupons() {
      var YmdFormat = new Date().toJSON().slice(0, 10).replace(/-/g, "-");
      if (!this.form.coupons_code) {
        this.$toast.error("Please enter your coupon code");
        return false;
      }
      this.couponSubmit = true;
      var postEvent = axios.get(
        this.apiUrl + "/coupons/?code=" + this.form.coupons_code,
        this.headerjson
      );
      postEvent
        .then((res) => {
          this.couponSubmit = false;
          this.form.coupons_code = "";
          this.couponDiscoun = 0;
          if (res.status == 200) {
            if (res.data.data.length > 0) {
              if (res.data.data[0].expires_at >= YmdFormat) {
                if (res.data.data[0].is_fixed) {
                  this.couponDiscoun = res.data.data[0].discount_amount;
                } else {
                  this.couponDiscoun =
                    (this.totalCartValue *
                      parseFloat(res.data.data[0].discount_amount)) /
                    100;
                }
                this.$toast.success("Coupon applied");
              } else {
                this.$toast.error("Expired coupon");
              }
            } else {
              this.$toast.error("Please enter valid coupon code");
            }
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.couponSubmit = false;
          this.$toast.error(err.response.data.message);
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
          }
        });
    },

    handleTax: function () {
      this.taxModal();
    },

    selectCustomer(event) {
      if(event){      
        this.customer = this.state.customers.find((e) => e.value == event);
        this.pform.customer_id = this.customer.value;
        this.pform.customer_group_id = this.customer.customer_group_id;
        this.pform.customer_group_name  = this.customer.customer_group_name;
        if(this.pform.customer_id ==1){
          this.totalCollections = this.pform.payments[0].amount;
        }  
      }
    },

    handleCustomer: function () {
      this.isSubmit = true;
      var postEvent = axios.post(
        this.apiUrl + "/customers",
        this.cform,
        this.headerjson
      );
      postEvent
        .then((res) => {
          if (res.status == 200) {
            this.isSubmit = false;
            this.cform.reset();
            this.form.customer_id = res.data.data.id;
            this.pform.customer_id = res.data.data.id;
            // this.selectCustomer(res.data.data.id)
            this.fetchCustomers();
            this.customerModal();
            this.$toast.success(res.data.message);
          } else {
            this.$toast.error(res.data.message);
          }
        })
        .catch((err) => {
          this.isSubmit = false;
          this.$toast.error(err.response.data.message);
          if (err.response.status == 422) {
            this.errors = err.response.data.errors;
          }
        });
    },

    handleResize() {
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight - 160;
      var body = document.body;
      var b = document.querySelector("body");
      var mask = document.getElementById("leftside-menu-container");
      if (b.hasAttribute("data-leftbar-compact-mode")) {
        mask.style.overflowY = "auto";
        b.removeAttribute("data-leftbar-compact-mode");
        document
          .getElementsByTagName("body")[0]
          .classList.add("sidebar-enable");
        document
          .getElementsByTagName("body")[0]
          .classList.remove("compact-enable");
      } else {
        mask.style.overflowY = "visible";
        b.setAttribute("data-leftbar-compact-mode", "condensed");
        document
          .getElementsByTagName("body")[0]
          .classList.remove("sidebar-enable");
        document
          .getElementsByTagName("body")[0]
          .classList.add("compact-enable");
      }
    },

    deleteItem: function (item) {
      this.items.splice(this.items.indexOf(item), 1);
    },

    random_num(min = 4, max = 6) {
      // const d1 = new Date();
      // const result = d1.getTime();
      const result = "C" + Math.floor(Math.random() * 100000);
      this.cform.customer_code = result;
    },

    // Ledger Account Name Setup
    ledgerAccountNameSetup: function(value) {
        this.cform.customer_receivable_account = value;
    },

    print(document_id) { 

      const options = {
                name: '_blank',
                specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
                styles: [ 
                    this.baseUrlPrintCSS+'/assets/css/bootstrap-print.min.css',
                    // this.baseUrlPrintCSS+'/assets/css/bootstrap.min.css',
                    this.baseUrlPrintCSS+'/assets/css/print.css'
                ],
            };
            
      // const options = {
      //   name: '_blank',
      //   specs: ['fullscreen=yes', 'titlebar=yes', 'scrollbars=yes'],
      //   styles: [ 
      //       this.baseUrlPrintCSS+'/assets/css/bootstrap.min.css',
      //       this.baseUrlPrintCSS+'/assets/css/print_invoice.css'
      //   ],
      // };
      this.$htmlToPaper(document_id, options);

    },

    itemDiscountPlat:function(item, index){   
      let percent = 0;   
      if(item.uom == 5){ 
        percent = (parseFloat(item.discount) / (item.weight * item.mrp_price)) * 100;  
      }else{ 
        percent = (parseFloat(item.discount) / (item.quantity * item.mrp_price)) * 100;  
      }  
      this.$store.getters.cartItems[index].discount_percent = percent.toFixed(2); 
    },

    itemDiscountPercent: function(item, index){ 
      let order_discount = 0 ; 
      if(item.uom == 5){
        order_discount = ((item.mrp_price) * (parseFloat(item.discount_percent)) / 100);  
      }else{ 
        order_discount = ((item.mrp_price) * (parseFloat(item.discount_percent)) / 100);  
      }
      this.$store.getters.cartItems[index].discount = order_discount;
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

    ...mapActions(["removeAllCartItems", "removeCartItem", "addCartItem"]),
  },
  created() {
    //console.log('cartInfo',this.$store.getters.cartInfo);
    //this.dicountCalculate();
    this.handleResize();
    //console.log('searchIteam',this.searchIteam);
    //this.fetchCustomers();
    // this.onLoaded();
    // this.onDecode();
  },
  destroyed() {
    //window.removeEventListener('resize', this.handleResize);
  },
  mounted() {
    window.scrollTo(0, 0);

    Quagga.onDetected((data) => {
      // console.log(data);
      if(data.codeResult) {
        this.foundBarcode(data.codeResult);
      }else{
        this.$toast.error("Something went wrong, please try again!");
      }

    });

    document.addEventListener("keydown", e => { 
        if(e.altKey && e.key == 'Enter') {
          if(!this.submitDisable) {    
            this.addConfirmModal(); 
          }else{
            this.$toast.warning("Collection amount can't be less then net amount!");
          }
        }

        if(e.shiftKey && e.key == 'Enter') {
            this.handleSubmit();
        }
          // console.log("event code", e.keyCode);
    }); 


  },

  beforeUpdate() { 
    if (this.totalCartValue > 0 && this.form.customer_id) {
      this.disabled = false;
    } else {
      this.disabled = true;
    }
    if (!this.discountModalActive) {
      this.enabledDiscount = false;
    }

    if (!this.rePrintModalActive) {
      this.invoice_number = "";
      this.popupError = "";
    }

    if (!this.returnReplaceModalActive) {
      this.popupError = "";
    }

    // ---------------------------- Akbar
    if (!this.returnReplacePageModalActive) {
      this.returnInfo = "";
      this.return_replace = [];
    }

    

    if (this.state.customers.length > 0) {
      this.customer = this.state.customers.find(
        (e) => e.value == this.form.customer_id
      );

      this.pform.customer_group_id  = this.customer ? this.customer.customer_group_id : '';
      this.pform.customer_group_name  = this.customer ?  this.customer.customer_group_name : '';

      // console.log('this.pformthis.pformthis.pformthis.pform ==== ', this.pform)
      // console.log("beforeUpdate-customers_info", this.state.customers_info);
      // console.log("beforeUpdate-customer", this.customer);
    } 

    if (!this.barcodeReaderModalActive) {
      if(this.renderBarcodeReader) {
        this.stopRead();
      }
    } else {
      this.startRead();
    }

    if(this.netAmountCalculate>0){
      this.submitDisable = false
      this.btn_disabled = false 
    }else{
      this.submitDisable = true
      this.btn_disabled = true 
    }


    if(this.pform.customer_id ==1){ 
      this.totalCollections = this.pform.payments[0].amount; 
      console.log(this.totalCollections ) // Plz don't remove this console
      console.log(this.netAmountCalculate ) // Plz don't remove this console
      if(Number(this.totalCollections) >= Number(this.netAmountCalculate)){ 
        this.submitDisable = false;      
      }else{
        this.submitDisable = true;
      }
    }else{
      this.submitDisable = false;
    }

    // Code Customized by Akbar
    if(this.paymentModalActive===false) {
      // setTimeout(()=> {
      //   console.log('this.paymentModalActive', this.paymentModalActive)
      //   if(this.pform.payments.length == 1){
      //     this.pform.payments[0].paid_by = "cash";
      //   } 
      // }, 500);
      
      // this.pform.payments[0].amount = this.netAmountCalculate;
      this.more_payemnts = [];
      this.pform.payments.splice(1);
    }
  },

  computed: {
    ...mapGetters([
      "productItems",
      "cartItems",
      "cartTotal",
      "cartQuantity",
      "holdSaleInfo",
    ]),

    totalItemQty:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) {
        return total + item.quantity;
      }, 0);
    }, 
    totalItemWeight:  function(){ 
      return this.$store.getters.cartItems.reduce(function (total, item) {
        // console.log('totalItemWeight', item)
        return total + item.weight;
      }, 0);
    },
    totalItemPrice:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) {
        return total + item.mrp_price;
      }, 0);
    },
    totalItemValue:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) {
        if(item.uom ==5){
          return total + item.weight * item.mrp_price;
        }else{
          return total + item.quantity * item.mrp_price;
        } 
      }, 0);
    },
    
    totalItemDiscount:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) { 
        return total + item.discount; 
      }, 0);
    },
    totalItemDiscountValue:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) { 
        let subtotal = 0;
         var item_weight = item.weight ? item.weight : 0;
         if(item.uom == 5){
          subtotal = parseFloat(item.discount) * parseFloat(item_weight).toFixed(0)
         }else{
          subtotal = parseFloat(item.discount) * parseFloat(item.quantity).toFixed(0)
         }  
        return total + subtotal; 
      }, 0);
    },
    totalItemNetValue:  function(){
      return this.$store.getters.cartItems.reduce(function (total, item) {
        let results;
        if(item.uom == 5){
          results = parseFloat( item.mrp_price * item.weight - (item.discount * item.weight)).toFixed(0)
        }else{
          results = parseFloat( item.mrp_price * item.quantity - (item.discount * item.quantity)).toFixed(0)
        } 
        return total + Number(results);
      }, 0);
    },
    specialDiscount: function () {
      let order_discount = 0;
      let checkNumber = !isNaN(this.form.order_discount);
      if (this.form.order_discount) {
        let percentage = this.form.order_discount.split("%");
        if (percentage.length > 1) {
          order_discount = (this.totalCartValue * parseFloat(percentage[0])) / 100;
        } else {
          order_discount = checkNumber ? parseFloat(this.form.order_discount) : 0;
        }
      }
      this.pform.order_discount_value = order_discount;
      return (this.special_discount = order_discount);
    },
    itemDiscount: function () {
      // const percentage = typeof item.discount === 'string' ? item.discount.toString.split("%") : ''; 
      // if (percentage.length > 1) {
      //   order_discount = (item.quantity * parseFloat(percentage[0])) / 100;
      // } else {
      //   order_discount = parseFloat(item.discount * item.quantity);
      // }

      return store.getters.cartItems.reduce(function (total, item) {
        return total + item.item_discount * item.quantity;
      }, 0);
 
    },

    invoiceVat: function () {
      let order_vat = 0;
      let checkNumber = !isNaN(this.form.order_vat);
      if (this.form.order_vat) {
        let percentage = this.form.order_vat.split("%");
        if (percentage.length > 1) {
          order_vat = (this.totalCartValue * parseFloat(percentage[0])) / 100;
        } else {
          order_vat = checkNumber
            ? (this.totalCartValue * parseFloat(this.form.order_vat)) / 100
            : 0;
        }
      }
      this.pform.order_vat = order_vat;
      return order_vat;
    },

    customer_discount: function () {
      if(this.customer){
        let customer_discount = (this.totalCartValue * parseFloat(this.customer.discount)) / 100;
        this.pform.customer_discount = customer_discount;
        return customer_discount ? customer_discount : 0;
      }else{
        return 0
      }
    },
    customer_group_discount: function () {
      // console.log("this.customers", this.customer);
      if(this.customer){
      let customer_group_discount = (this.totalCartValue * parseFloat(this.customer.group_discount)) / 100;
      this.pform.customer_group_discount = customer_group_discount;
      return customer_group_discount ? customer_group_discount : 0;
      }else{
        return 0;
      }
    },
    totalCollections: function () {
      return this.pform.payments.reduce(function (total, item) {
        return total + parseFloat(item.amount);
      }, 0);
    },
    netAmountCalculate: function () {
      let totalDiscount = this.all_discount + this.specialDiscount + this.customer_group_discount + this.customer_discount + this.couponDiscoun;

      let totalVatTax = this.invoiceVat + this.totalCartTax;
      let result = this.totalCartValue + totalVatTax - totalDiscount;
      this.pform.grand_total = result;        
      this.pform.order_items_vat = this.totalCartTax;
      this.pform.total_amount = this.totalCartValue;
      this.pform.payments[0].amount = result.toFixed(); 
      return result.toFixed();
    },
    totalQuantity: function () {
      return this.items.reduce(function (total, item) {
        return total + item.price;
      }, 0);
    },
    totalSumm: function () {
      return this.items.reduce(function (total, item) {
        return total + item.price * item.qty;
      }, 0);
    },
    salesReturnAmountSumVal: function () {
      let sumTotal = this.return_replace.reduce(function (total, ritem) {
        let item_discount = ritem.replace_discount * ritem.return_qty;
        return total + ((ritem.replace_mrp_price * ritem.return_qty) - item_discount); 
      }, 0);
      this.sale_return_info.return_amount = sumTotal;
      return sumTotal;
    }
  },
  watch: {
    'sale_return_info.return_amount': function (newVal, OldVal) {
      return this.return_replace.reduce(function (total, ritem) {
        return total + ritem.replace_mrp_price * ritem.return_qty;
        // this.sale_return_info.return_amount = total;
      }, 0)
    }
  }
};
</script>
<style scoped>
/* #UIElement {
  margin: 2vmin auto;
  text-align: center;
  font-size: medium;
  height: 40vh;
  width: 80vw;
} */

.inv-perview h2, .inv-perview tr td{
  font-size:11px;
  margin: 0;
  padding: 3px 0;
}
#mid .info p{
  font-size:11px !important
}
.cmb-5{
  margin-bottom:5px !important;
  font-size:11px;
}
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
  
  body {
      margin: 0;
      padding: 0;
  }

  #invoice-POS {
    border: 2px solid #000;
    background: #3e81ae;
    font-size: 8px !important;
  }
  

  dl,
  ol,
  ul {
    margin-top: 0;
    margin-bottom: 1rem;
    list-style-type: none;
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
    font-size: 7px !important;
  }
  /* .fnt10{
    font-size: 10px !important;
  } */

  .borderTop {
    border-top: 1px solid black !important;
  }
  .borderBottom {
    border-bottom: 1px dashed #ccc !important;
  }
  
}

dl,
ol,
ul {
  list-style-type: none !important;
}

.modal-content.scrollbar-width-thin.customer-add-modal { 
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
  /* Targets all id with 'col-' 
  border-bottom: 1px solid #eee;*/
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

#legalcopy {
  margin-top: 5mm;
}

p {
  font-size: 0.7em;
  color: #000;
  line-height: 1.2em;
}
.borderTop {
  border-top: 1px solid black;
}
.borderBottom {
  border-bottom: 1px dashed #ccc;
}
.Rate h2 {
  margin: 0px;
  padding: 0px;
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
</style>

<style>
  .modal-content.scrollbar-width-thin.barcodeReaderModal #videoWindow video {
    width: 100% !important;
}

.modal-content.scrollbar-width-thin.barcodeReaderModal #videoWindow canvas {
    /* width: 500px !important; */
    display: none !important;
}

canvas.drawingBuffer {
    display: none;
}
</style>
