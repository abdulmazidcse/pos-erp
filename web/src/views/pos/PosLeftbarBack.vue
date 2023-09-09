<template>
  <div class="row">
    <div class="col-md-7"> 
        <div class="pos-leftbar " id="pos-leftbar"> 
            <div class="pos-header "> 
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
                    <div class="input-group-text"><a  @click="customerModal">  <i class="fas fa-2x fa-plus-circle"  ></i> </a> </div> 
                </div> 
              </div>
              <div class="form-group col-md-12 autocomplete">   
                 <input type="text" v-model="searchIteam" class="form-control" placeholder="Scan/Search product by name/code"
                  @keyup="inputChanged" @keyup.enter="addProductToCart()" @keydown.down="onArrowDown" @keydown.up="onArrowUp" > 
                  <ul
                    v-if="searchProduct.length"
                    class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10 autocomplete-results"
                  >
                    <li class="px-1 pt-1 pb-2 font-bold border-b border-gray-200">
                      Showing {{ searchProduct.length }} of {{ productItems.length }} results
                    </li>
                    <li
                        v-for="(item, i) in searchProduct"
                        :key="item.product_name"
                        @change="selectProduct(item)"
                        @click="setResult(item)"
                        class="cursor-pointer hover:bg-gray-100 p-1"
                        :class="{ 'isActive': i === arrowCounter }"
                    >
                      {{ item.product_name }} ({{item.product_code}}) 
                      <span v-if="item.expires_date" style="color: rgb(34, 5, 242)"> 
                      {{item.expires_date ? ( item.expires_date  ) : '' }}
                      </span> 
                      
                    </li>
                  </ul>
              </div>   
            </div>  
            <div class="pos-body scrollbar-width-thin" v-bind:style="{height: window.height + 'px' }"> 
                <div class=" ">   
                  <table id="header-fixed" class="table-sm pos-table table items table-striped table-bordered table-condensed table-hover sortable_table">
                    <thead class="tableFloatingHeaderOriginal">
                      <tr class="border item-head">
                        <th width="50%">Item Name</th>
                        <th width="10%" class="text-center">Price</th>
                        <th width="10%" class="text-center">Qty</th>
                        <th width="10%" class="text-center">Discount</th>
                        <th width="10%" class="text-center">Vat</th>
                        <th width="15%" class="text-center">Subtotal</th>
                        <th width="5%">Del</th> 
                     </tr>
                    </thead>             
                      <tr class="border " v-for="(item, i) in cartItems" v-if="cartItems.length > 0" :key="item.id">
                       <td class="text-left p-1">{{ item.product_name }} ({{item.product_code}}) ({{item.expires_date}}) <span v-if="item.product_name.length > 30">...</span> </td>
                       <td class="text-center border">{{ item.mrp_price }}</td>
                       <td class="text-center border"><input class="qty" type="number" name="" v-model="item.quantity"> </td>
                       <td class="text-center border">{{item.discount}} </td>
                       <td class="text-center border">{{item.tax}} </td>
                       <td class="text-center border">{{ item.mrp_price * item.quantity -  item.discount}}</td> 
                       <td @click="removeCartItem(i)"><i class="fas fa-trash"></i></td> 
                      </tr> 
                  </table>
                </div>  
            </div>  
        </div>  
    </div>
    <div class="col-md-5">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="amount_0">Amount</label> 
            <input v-model="pform.payments[0].amount" id="amount_0" type="text" class="form-control" ref="amount_0" @keyup="checkAboveAmount($event,'s')" @keypress="validateNumber" >
          </div>
        </div>
        <div class="col-sm-6  ">
          <div class="form-group">
            <label for="paid_by">Paying by</label>
            <select class="form-control border" id="payingby_0" v-model="pform.payments[0].paid_by" @change="payingByAnother($event, 0)"> 
              <option  v-for="(item, key, index) in paying_by" :key="key" :value="item.value" :disabled="checkItem(item.value)">
              {{ item.name }}</option>  
            </select>
          </div>
        </div>  
      </div> 
      <div class="row">
        <div class="row pt-2">
                       
        </div>
      </div>
          <!-- <div class="col-md-6">
            <div class="mb-1">  
              <input type="text" class="form-control border" v-model="form.coupons_code" placeholder="Enter Coupon Code" autocomplete="off"> 
            </div>   
          </div>
          <div class="col-md-6"> 
            <div class="mb-1 mt-1">
              <button class="btn btn-sm btn-primary" @click="applyCoupons"> <span v-show="isSubmit">
                  <i class="fas fa-spinner fa-spin" ></i>
              </span> Apply Coupon</button>   
            </div>
          </div> -->
          <!-- <div class="col-md-6">
            <div class="form-group  ">
              <div class="mb-2"> 
                <label for="orderDiscount">Discount (5 or 5%) </label>
                <input type="text" class="form-control border" v-model="form.order_discount" placeholder="5 or 5%" autocomplete="off"> 
              </div>  
            </div>   
          </div>
          <div class="col-md-6">
            <div class="form-group  ">
                <div class="mb-2"> 
                  <label for="orderDiscount">Vat (5 or 5%) </label>
                  <input type="text" class="form-control border" v-model="form.order_vat" placeholder="5 or 5%" autocomplete="off">
                </div>  
              </div>   
          </div> -->            
      
      <div class="row "> 
          <div >
            <table class=" table-sm table  table-bordered  border " style="margin-bottom:0px">
              <thead class="tableFloatingHeaderOriginal"> 
                <tr class="border item-head">
                  <th>Total</th>
                  <th>{{Number(totalCartValue).toLocaleString()}}</th>
                  <th  rowspan="6" class="text-center align-middle border fc-darkblue" bgcolor="green">
                    <p style="font-size:25px">Net Amount <br> {{Number(netAmountCalculate).toLocaleString()}}</p></th> 
                </tr>
                <tr class="item-head"> 
                  <th>Special Customer & Group Discount  </th>
                  <th>{{Number(customer_discount + customer_group_discount).toLocaleString()}} </th> 
                </tr>
                <!-- <tr class="item-head"> 
                  <th>Special customer  </th>
                  <th>{{Number(customer_group_discount).toLocaleString()}} </th> 
                </tr> -->
                <tr class="item-head"> 
                  <th>Discount & Coupon </th>
                  <th>{{Number(special_discount+couponDiscoun+all_discount).toLocaleString()}} </th> 
                </tr>
                <tr class="item-head"> 
                  <th>Item Discount </th>
                  <th>{{Number(cartItemDiscount).toLocaleString()}} </th> 
                </tr>
                <tr class="  item-head">
                  <th>Invoice Vat & Item Vat</th>
                  <th>{{Number(invoiceVat + totalCartTax).toLocaleString()}}</th> 
                </tr>
                <!-- <tr class="  item-head">
                  <th></th>
                  <th>{{Number(totalCartTax).toLocaleString()}}</th> 
                </tr> -->
                <tr class="  item-head">
                  <th>Total Item</th>
                  <th>{{Number(cartItems.length).toLocaleString()}}</th> 
                </tr>
              </thead> 
            </table> 
          </div>
            
          <div class="row button-list pt-2">  
            <div class="col-md-4 " style="padding: 0 6px;">
              <div class="d-grid">
                <input type="text" class="form-control border" v-model="form.coupons_code" placeholder="Enter Coupon Code" autocomplete="off">  
              </div> 
            </div>
            <div class="col-md-4" style="padding: 0 5px;">
              <div class="d-grid">
                <button class="btn btn-sm btn-primary" @click="applyCoupons"> <span v-show="isSubmit">
                  <i class="fas fa-spinner fa-spin" ></i>
              </span> Apply Coupon</button>
              </div> 
            </div>
            <div class="col-md-4" style="padding: 0 5px;">
              <div class="d-grid">
                <button type="button" class="btn btn-sm btn-success" @click="addDiscountModal" >Add Discount</button> 
              </div> 
            </div>   
            <div class="col-md-4 " style="padding: 0;">
              <div class="d-grid">
                <button type="button" class="btn btn-sm btn-primary">Hold</button> 
              </div> 
            </div>
            <div class="col-md-4" style="padding: 0 5px;">
              <div class="d-grid"> 
                <button type="button" class="btn btn-sm btn-danger" id="reset" @click="clearAllCartItems">Cancel</button> 
               <!--  <button type="button" class="btn btn-sm btn-info" id="reset" @click="customerModal">Print Bill</button>  -->
              </div> 
            </div>
            <div class="col-md-4" style="padding: 0 5px;">
              <div class="d-grid">
                <button type="button"  class="btn btn-sm btn-success" @click="handleSubmit" :disabled="disabled">
                    <span v-show="isSubmit">
                        <i class="fas fa-spinner fa-spin" ></i>
                    </span> Print
                </button> 
                <!-- <button type="button" class="btn btn-sm btn-info" @click="paymentModal" :disabled="disabled" >Payment</button>  -->
              </div> 
            </div> 
          </div>
        </div> 
    </div> 

    <Modal @close="paymentModal()" :modalActive="paymentModalActive">
        <div class="modal-content scrollbar-width-thin">
            <div class="modal-header"> 
                <button @click="paymentModal()" type="button" class="btn btn-default">X</button>
            </div> 
            <div class="modal-body">
              <div class="row"> 
                <div class="col-lg-6">   
                    <p>Available Points : {{customer.points}} = ৳  
                    {{parseFloat(Number(convartRate * customer.points).toLocaleString()).toFixed(2)}}</p> 
                  </div> 
                  <div class="col-lg-6">   
                  </div> 
                  <div class="col-lg-6">  
                    <div class="mb-3">  
                        <textarea data-toggle="maxlength" v-model="pform.sale_note" class="form-control" maxlength="225" rows="2" placeholder="Sale Note"></textarea>
                    </div>   
                  </div> <!-- end col --> 
                  <div class="col-lg-6">  
                    <div class="mb-3"> 
                        <textarea data-toggle="maxlength" v-model="pform.staff_note" class="form-control" maxlength="225" rows="2" placeholder="Staff Note"></textarea>
                    </div> 
                  </div> <!-- end col -->   
              </div>  
              <div class="payment well well-sm">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="amount_0">Amount</label> 
                      <input v-model="pform.payments[0].amount" id="amount_0" type="text" class="form-control" ref="amount_0" @keyup="checkAboveAmount($event,'s')" @keypress="validateNumber" >
                    </div>
                  </div>
                  <div class="col-sm-6  ">
                    <div class="form-group">
                      <label for="paid_by">Paying by</label>
                      <select class="form-control border" id="payingby_0" v-model="pform.payments[0].paid_by" @change="payingBy($event, 0)"> 
                        <option  v-for="(item, key, index) in paying_by" :key="key" :value="item.value" :disabled="checkItem(item.value)">
                        {{ item.name }}</option>  
                      </select>
                    </div>
                  </div>  
                </div>  
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group" v-if="pform.payments[0].paid_by=='redeem_point'"> 
                        <label for="gift_card_no_1">Redeem point</label>                                               
                        <input v-model="pform.payments[0].redeem_point" type="text" class="form-control" @keyup="redeemPointCheck($event, 0)" > 
                    </div>
                    <div class="form-group" v-if="pform.payments[0].paid_by=='gift_card'"> 
                        <label for="gift_card_no_1">Gift Card No</label>                                               
                        <input v-model="pform.payments[0].gift_card" type="text" class="form-control"> 
                    </div>
                    <div class="row" v-if="pform.payments[0].paid_by=='CC'">
                      <div class="form-group col-sm-6" > 
                          <label for="gift_card_no_1">Card Reference Code</label>                                              
                          <input v-model="pform.payments[0].card_reference_code" type="text" class="form-control"> 
                      </div>
                      <div class="form-group col-sm-6">
                          <label for="gift_card_no_1">Bank Name</label> 
                          <select class="form-control border" v-model="pform.payments[0].bank_id" > 
                            <option v-for="(bank, index) in state.banks" :value="bank.id" :key="index"> 
                              {{bank.name}}
                            </option> 
                          </select> 
                      </div> 
                    </div> 

                    <div class="form-group " v-if="pform.payments[0].paid_by=='mfs'" >
                      <label for="gift_card_no_1">Mobile wallet Name</label> 
                      <select class="form-control border" v-model="pform.payments[0].wallet_id" > 
                          <option v-for="(wallet, index) in state.wallets" :value="wallet.id" :key="index"> 
                            {{wallet.mobile_wallet}}
                          </option> 
                      </select> 
                    </div>
                    <div class="form-group">
                      <label for="amount_1">Payment Note</label>
                      <textarea data-toggle="maxlength" v-model="pform.payments[0].payment_note" class="form-control" maxlength="225" rows="2" placeholder="Sale Note"></textarea>
                    </div>
                  </div>  
                </div>
              </div>
              <div class="payment well well-sm mt-2" v-for="(item, i) in more_payemnts" v-if="more_payemnts.length > 0" :key="item.id">
                <div class="row">
                  <div class="  float-right "> 
                    <button type="button" class="btn-primary float-right" @click="morePayemntRemove(i)">X</button> 
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="amount_1">Amount</label> 
                      <input v-model="pform.payments[i+1].amount" :id="'amount_'+parseFloat(i+1)" type="text" class="form-control" ref="'amount_'+parseFloat(i+1)" @keyup="checkAboveAmount($event,(i))" @keypress="validateNumber"  >
                    </div>
                  </div>
                  <div class="col-sm-6  ">
                    <div class="form-group">
                      <label for="paid_by">Paying by</label>
                      <select class="form-control border" :id="'payingby_'+parseFloat(i+1)" v-model="pform.payments[i+1].paid_by" @change="payingBy($event, i+1)" > 
                        <option  v-for="(item, key, index) in paying_by" :key="key" :value="item.value" :disabled="checkItem(item.value)">
                        {{ item.name }}</option> 
                      </select>
                    </div>
                  </div>  
                </div>  
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group" v-if="pform.payments[i+1].paid_by=='redeem_point'"> 
                        <label for="gift_card_no_1">Redeem point</label>                                               
                        <input v-model="pform.payments[i+1].redeem_point" type="text" class="form-control" @keyup="redeemPointCheck($event, i+1)" > 
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group" v-if="pform.payments[i+1].paid_by=='gift_card'"> 
                        <label for="gift_card_no_1">Gift Card No</label>                                               
                        <input v-model="pform.payments[i+1].gift_card" type="text" class="form-control"> 
                      </div>
                    </div>
                    <div class="row" v-if="pform.payments[i+1].paid_by=='CC'">
                      <div class="form-group col-sm-6" > 
                          <label for="gift_card_no_1">Card Reference Code</label>                                              
                          <input v-model="pform.payments[i+1].card_reference_code" type="text" class="form-control"> 
                      </div>
                      <div class="form-group col-sm-6">
                          <label for="gift_card_no_1">Bank Name</label> 
                          <select class="form-control border" v-model="pform.payments[i+1].bank_id" > 
                            <option v-for="(bank, index) in state.banks" :value="bank.id" :key="index"> 
                              {{bank.name}}
                            </option> 
                          </select> 
                      </div> 
                     
                    </div> 
                    <div class="form-group " v-if="pform.payments[i+1].paid_by=='mfs'" >
                      <label for="gift_card_no_1">Mobile wallet Name</label> 
                      <select class="form-control border" v-model="pform.payments[i+1].wallet_id" > 
                        <option v-for="(wallet, index) in state.wallets" :value="wallet.id" :key="index"> 
                          {{wallet.mobile_wallet}}
                        </option> 
                      </select> 
                    </div>
                    <div class="form-group">
                      <label for="amount_1">Payment Note</label>
                      <textarea data-toggle="maxlength" v-model="pform.payments[i+1].payment_note" class="form-control" maxlength="225" rows="2" placeholder="Sale Note"></textarea>
                    </div>
                  </div>  
                </div> 
              </div> 
              <div class="row pt-2">
                <div class="d-grid">
                  <button type="button" class="btn btn-primary" @click="morePayemnt()"> <i class="fa fa-plus"></i> Add More Payments</button> 
                </div> 
              </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary " @click="handleSubmit">
                    <span v-show="isSubmit">
                        <i class="fas fa-spinner fa-spin" ></i>
                    </span> Submit
                </button>
            </div> 
        </div>
    </Modal> 
    <Modal @close="customerModal()" :modalActive="customerModalActive">
        <div class="modal-content scrollbar-width-thin">
            <div class="modal-header"> 
                <button @click="customerModal()" type="button" class="btn btn-default">X</button>
            </div> 
            <div class="modal-body">  
                <div class="row">
                    <div class="col-lg-4">  
                      <div class="mb-3"> 
                          <label for="customer_code" class="form-label">Customer Code *</label>
                          <input type="text" class="form-control" @keypress="onkeyPress('customer_code')" v-model="cform.customer_code" id="customer_code" placeholder="Customer Code" autocomplete="off"> 
                          <div class="invalid-feedback" v-if="errors.customer_code">
                              {{errors.customer_code[0]}}
                          </div>
                          <div class="input-group">
                            <input type="text" class="form-control border" aria-label="Dollar amount (with dot and two decimal places)"  @keypress="onkeyPress('product_code')" v-model="form.product_code" id="product_code" placeholder="Product Code" autocomplete="off">  
                            <span class="input-group-text " id="random_num" @click="random_num()"  >
                                <i class="fa fa-random"></i>
                            </span> 
                          </div>
                      </div>   

                    </div> <!-- end col --> 
                    <div class="col-lg-4" style="display:none">  
                      <div class="mb-3">
                          <label for="company_id" class="form-label">Company </label> 
                          <select class="form-control border" v-model="cform.company_id" @change="onkeyPress('company_id')" id="company_id">
                              <option value="0">Select company</option>
                              <option v-for="(company, index) in state.companies" :value="company.id" :key="index"> {{company.name}}
                              </option>
                          </select> 
                          <div class="invalid-feedback" v-if="errors.company_id">
                              {{errors.company_id[0]}}
                          </div>
                      </div> 
                    </div> <!-- end col --> 
                    <div class="col-lg-4">  
                      <div class="mb-3">
                         <label for="customer_group_id" class="form-label">Customer Group *</label> 
                            <select class="form-control border" v-model="cform.customer_group_id" @change="onkeyPress('customer_group_id')" id="customer_group_id">
                                <option value="0">Select Customer Group</option>
                                <option v-for="(c_group, index) in state.customer_groups" :value="c_group.id" :key="index"> {{c_group.title}}
                                </option>                                 
                            </select> 
                            <div class="invalid-feedback" v-if="errors.customer_group_id">
                                {{errors.customer_group_id[0]}}
                            </div>
                      </div> 
                    </div> <!-- end col -->
                </div> 
                <div class="row">
                    <div class="col-lg-4">  
                      <div class="mb-3">
                          <label for="name" class="form-label">Name *</label>
                          <input type="text" class="form-control " @keypress="onkeyPress('name')" v-model="cform.name" id="name" placeholder="Customer Name" autocomplete="off"> 
                          <div class="invalid-feedback" v-if="errors.name">
                              {{errors.name[0]}}
                          </div>
                      </div>   
                    </div> <!-- end col --> 
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email </label>
                        <input type="text" class="form-control " @keypress="onkeyPress('email')" v-model="cform.email" id="email" placeholder="Customer Email" autocomplete="off"> 
                        <div class="invalid-feedback" v-if="errors.email">
                            {{errors.email[0]}}
                        </div>
                      </div>
                    </div> 
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth </label>
                        <input type="date" class="form-control " @keypress="onkeyPress('dob')" v-model="cform.dob" id="dob" placeholder="" autocomplete="off"> 
                        <div class="invalid-feedback" v-if="errors.dob">
                            {{errors.dob[0]}}
                        </div>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" @keypress="onkeyPress('phone')" v-model="cform.phone" id="phone" placeholder="Customer mobile number"> 
                        <div class="invalid-feedback" v-if="errors.phone">
                            {{errors.phone[0]}}
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="address" class="form-label">Address *</label>
                        <input type="text" class="form-control" @keypress="onkeyPress('address')" v-model="cform.address" id="address" placeholder="Customer Address"> 
                        <div class="invalid-feedback" v-if="errors.address">
                            {{errors.address[0]}}
                        </div>
                    </div>
                </div>  
                <div class="row">  
                  <div class="form-group col-md-4"> 
                      <label for="district_id" class="form-label">District</label>
                      <select class="form-control border" v-model="cform.district_id" id="district_id">
                          <option value="0">Select district</option>
                          <option v-for="(district, index) in state.districts" :value="district.id" :key="index"> {{district.name}}
                          </option>
                      </select> 
                      <div class="invalid-feedback" v-if="errors.district_id">
                          {{errors.district_id[0]}}
                      </div>
                  </div>
                  <div class="form-group col-md-4"> 
                      <label for="area_id" class="form-label">Area</label>
                      <select class="form-control border" v-model="cform.area_id" id="district_id">
                          <option value="0">Select area </option>
                          <option v-for="(area, index) in state.areas" :value="area.id" :key="index"> {{area.name}}
                          </option>
                      </select> 
                      <div class="invalid-feedback" v-if="errors.area_id">
                          {{errors.area_id[0]}}
                      </div>
                  </div>
                  <div class="form-group col-md-4"> 
                      <label for="postal_code" class="form-label">Postal Code</label>
                      <input type="text" class="form-control border " v-model="cform.postal_code" id="postal_code" placeholder="Postal Code" autocomplete="off"> 
                      <div class="invalid-feedback" v-if="errors.postal_code">
                          {{errors.postal_code[0]}}
                      </div>
                  </div> 
                </div>
                <div class="row">
                  <div class="form-group col-md-4"> 
                    <label for="discount_percent" class="form-label">Discount Percent *</label>
                    <input type="number" class="form-control   "  v-model="cform.discount_percent" id="discount_percent" placeholder="Discount Percent" autocomplete="off"> 
                    <div class="invalid-feedback" v-if="errors.discount_percent">
                        {{errors.discount_percent[0]}}
                    </div>
                  </div> 
                  <div class="form-group col-md-4">
                    <div class="mb-3">
                        <label for="example-select" class="form-label">Status *</label>
                        <select class="form-select" v-model="cform.status" >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div> 
                    <div class="invalid-feedback" v-if="errors.status">
                        {{errors.status[0]}}
                    </div>
                  </div> 
                </div>

                <div class="row">
                    <div class="form-group form-check col-md-3"> 
                        <input type="checkbox" class="form-check-input" true-value="1" false-value="0" v-model="cform.wholesale_customer" id="wholesale_customer"> Wholesale Customer
                        <div class="invalid-feedback" v-if="errors.wholesale_customer">
                            {{errors.wholesale_customer[0]}}
                        </div>
                    </div>  
                    <div class="form-group form-check col-md-3"> 
                        <input type="checkbox" class="form-check-input "  true-value="1" false-value="0" v-model="cform.sale_without_vat" id="sale_without_vat"> Sale Without VAT
                        <div class="invalid-feedback" v-if="errors.sale_without_vat">
                            {{errors.sale_without_vat[0]}}
                        </div>
                    </div>  
                    <div class="form-group form-check col-md-3"> 
                        <input type="checkbox" class="form-check-input"  true-value="1" false-value="0" v-model="cform.credit_customer" id="credit_customer"> Credit Customer
                        <div class="invalid-feedback" v-if="errors.credit_customer">
                            {{errors.credit_customer[0]}}
                        </div>
                    </div>  
                    <div class="form-group form-check col-md-3"> 
                        <input type="checkbox" class="form-check-input" true-value="1" false-value="0" v-model="cform.store_customer" id="store_customer"> Store Customer
                        <div class="invalid-feedback" v-if="errors.store_customer">
                            {{errors.store_customer[0]}}
                        </div>
                    </div>  
                </div>
            </div>
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary " @click="handleCustomer">
                    <span v-show="isSubmit">
                        <i class="fas fa-spinner fa-spin" ></i>
                    </span>Submit
                </button>                  
            </div> 
        </div>
    </Modal>
    <Modal @close="addDiscountModal()" :modalActive="discountModalActive">
        <div class="modal-content scrollbar-width-thin">
            <div class="modal-header"> 
              <button @click="addDiscountModal()" type="button" class="btn btn-default">X</button>
            </div> 
            <div class="modal-body">  
                <div class="row" v-if="!enabledDiscount">   
                    <div class="col-lg-12">  
                      <div class="mb-3">
                         <label for="customer_group_id" class="form-label">User ID *</label> 
                            <input type="text" class="form-control border" v-model="user.email" placeholder="User ID" autocomplete="off">
                      </div> 
                    </div> <!-- end col -->
                    <div class="col-lg-12">  
                      <div class="mb-3">
                         <label for="customer_group_id" class="form-label">Password *</label> 
                            <input type="password" class="form-control border" v-model="user.password" placeholder="Password" autocomplete="off">
                      </div> 
                    </div> <!-- end col -->
                </div>
                <div class="row" v-else>   
                    <div class="col-lg-12">  
                      <div class="mb-3">
                        <label for="orderDiscount">Discount (5 or 5%) </label>
                        <input type="text" class="form-control border" v-model="form.order_discount" placeholder="5 or 5%" autocomplete="off">
                      </div> 
                    </div> <!-- end col --> 
                </div>  
            </div>
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary " @click="handleLogin" v-if="!enabledDiscount">
                    <span v-show="isSubmit" :disabled="disabled">
                        <i class="fas fa-spinner fa-spin" ></i>
                    </span>Submit
                </button>  
                <button type="submit" class="btn btn-primary " @click="addDiscountModal" v-else>
                    <span v-show="isSubmit">
                        <i class="fas fa-spinner fa-spin" ></i>
                    </span>Apply
                </button>                  
            </div> 
        </div>
    </Modal>
  </div>    
</template>
<script>    
import { mapGetters, mapActions } from "vuex";   
import { ref } from "vue";
import Form from 'vform' 
import axios from 'axios';
import { reactive, toRefs, computed, inject, onMounted, 
  getCurrentInstance, onUpdated, onUnmounted, onBeforeMount, onBeforeUnmount, onBeforeUpdate  } from 'vue'
import { useStore } from "vuex";
import { useToast } from 'vue-toastification'
import Modal from "./../helper/Modal";
export default { 
  name: 'PosLeftbar', 
  setup() {    
    const store = useStore();
    const toast = useToast(); 
    const swal = inject('$swal') 
    const app = getCurrentInstance()
    const app2 = app.appContext.config.globalProperties ;
    const count = ref(0) 
    const state = reactive({
      companies : [],
      customer_groups: [],
      areas : [],
      districts : [],
      customers : [],
      customers_info : [], 
      wallets : [],
      points_setting:'', 
      banks:[{id:1, name:'City Bank'},{id:2, name:'DBBL Bank'}, {id:3, name:'Eastern Bank'}]
    })

    // let searchIteam = ref('')
    let disabled = ref('');  
    const paymentModalActive = ref(false);
    const customerModalActive = ref(false);
    const discountModalActive = ref(false);
    const modalActive = ref(false); 
    const taxModalActive = ref(false); 
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
    let customer_id = computed(() => { 
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].customer_id : '';
    });
    let orderedDiscount = computed(() => { 
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].order_discount : 0;
    });
    let orderedTax = computed(() => { 
      //return (store.getters.cartInfo.length > 0 ) ? JSON.parse(JSON.stringify(store.getters.cartInfo))[0].order_tax : 0;
    });

    // const searchProducts = computed(() => {
    //   if (searchIteam.value === '') {
    //     return []
    //   }
    //   let matches = 0 
    //   return store.getters.productItems.filter(item => {
    //     if (item.product_name.toLowerCase().includes(searchIteam.value.toLowerCase()) && matches < 10) {
    //       matches++
    //       return item
    //     }else if (item.product_code.toLowerCase().includes(searchIteam.value.toLowerCase()) && matches < 10) {
    //       matches++
    //       return item
    //     }
    //   })
    // });

    const totalCartValue = computed(() =>{ 
      return store.getters.cartItems.reduce(function(total, item){ 
        return total + (item.mrp_price * item.quantity); 
      },0);  
    });
    const cartItemDiscount = computed(() =>{  
      return store.getters.cartItems.reduce(function(total, item){ 
        return total + (item.item_discount * item.quantity); 
      },0);  
    }); 
    const totalCartTax = computed(() =>{ 
      return store.getters.cartItems.reduce(function(total, item){  
        return total + (item.tax * item.quantity); 
      },0);  
    });  
    const pointToAmount = computed(() =>{ 
      //return state.points_setting.cart_price_rate / state.points_setting.cart_points_rate * this.customer.points
      return 100  
    });  
    const convartRate = computed(() =>{ 
      return state.points_setting.cart_price_rate / state.points_setting.cart_points_rate;
      //return 100  
    }); 
    const all_discount = computed(() =>{  
      return store.getters.cartItems.reduce(function(total, item){ 
        return total + (item.discount * item.quantity); 
      },0);  
    });  
    // const selectProduct = (item) => { 
    //   store.dispatch('addCartItem',item) 
    //   selecteditem.value = item
    //   searchIteam.value = ''
    // } 
    const clearAllCartItems = () =>{  
      swal({
          title: 'Are you sure?',
          text: "You want clear cart list!", 
          showCancelButton: true,
          confirmButtonCategory: '#3085d6',
          cancelButtonCategory: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => { 
          if (result.value) { 
            store.dispatch('removeAllCartItems')  
            // toast("Cart list cleared!");
            // toast.info("Cart list cleared");
            // toast.success("Cart list cleared");
            // toast.error("Cart list cleared");
            toast.warning("Cart list cleared");                 
          }else{ 
          }
      });  
    };   
    let selecteditem = ref('')
    let multiclasses ={ 
      clear: '',
      clearIcon: '', 
    } 

    const fetchCustomers = () => {
      axios.get(app2.apiUrl+'/customers',app2.headers)
      .then((res) => { 
        state.customers = res.data.data.map(({ id, name, discount_percent, customer_group_discount, available_point }) => (
          { label: name, value: id, discount: discount_percent, group_discount: customer_group_discount, points: available_point }
          )); 
      });
    }
    onMounted(async () => {   
      await fetchCustomers()
      await axios.get(app2.apiUrl+'/companies', app2.headers)
      .then((res) => {
        state.companies = res.data.data;  
      });
      await axios.get(app2.apiUrl+'/customer_groups', app2.headers)
      .then((res) => {
        state.customer_groups = res.data.data;  
      });
      await axios.get(app2.apiUrl+'/districts', app2.headers)
      .then((res) => { 
        state.districts = res.data.data;   
      });
      await axios.get(app2.apiUrl+'/areas',app2.headers)
      .then((res) => {
        state.areas = res.data.data; 
      });
      await axios.get(app2.apiUrl+'/mobile_wallets',app2.headers)
      .then((res) => {
        state.wallets = res.data.data; 
      });  
      await axios.get(app2.apiUrl+'/points_settings',app2.headers)
      .then((res) => {
        state.points_setting = res.data.data[0]; 
      });      
    }) 
    onUpdated(() => {  
      //console.log('onUpdated') 
      //console.log(document.getElementById('count').textContent)
    })
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
      paymentModalActive, paymentModal, customerModalActive, customerModal, taxModal, taxModalActive,
      fetchCustomers,cartItemDiscount, all_discount, addDiscountModal, discountModalActive,
      orderedDiscount, orderedTax,
      // searchIteam, 
      count,
      totalCartValue, pointToAmount, convartRate, 
      totalCartTax, 
      // searchProducts,
      // selectProduct,
      clearAllCartItems, 
      disabled, 
      selecteditem,
      multiclasses, 
    }
  }, 
  components: {
    Modal,
  },
  data() {
    return {  
        getItems:'',
        customer:'',
        customer_points:0,
        isSubmit: false,
        items: [], 
        outlets:[],
        editMode:false,
        more_payemnts:[],
        window: {
          width: 0,
          height: 0
        },
        discountOverwrite:0,
        itemsDiscount:0,
        enabledDiscount:false,
        special_discount:0,
        errors:'', 
        paying_by :[
            {'value':'cash','name':'Cash','disabled':false},
            {'value':'gift_card','name':'Gift Card','disabled':false},
            {'value':'redeem_point','name':'Redeem','disabled':false},
            {'value':'CC','name':'Card','disabled':false},
            {'value':'mfs','name':'Mobile wallet','disabled':false},
            {'value':'other','name':'Other','disabled':false}],
        cform: new Form( { 
          id: '',
          customer_code: '1001', 
          company_id: 0, 
          customer_group_id: 1, 
          name: '',
          email: '',
          phone: '',
          address: '',
          dob: '',
          district_id: '0',
          area_id: '0',
          postal_code: '',
          discount_percent: 0,
          wholesale_customer: 0,
          sale_without_vat: 0,
          credit_customer: 0,
          store_customer: 0,
          status: 1,
        }),
        user: new Form({
          email:'',
          password:''
        }),
        form: new Form( {
          order_discount: 0,
          order_vat: 0,
          customer_id:1, 
          coupons_code:'',
        }),
        pform: new Form( {
          order_discount: 0,
          order_vat: 0,
          order_items_vat: 0,
          sale_note:'',
          staff_note:'',
          customer_id: '', 
          items:'',
          total_amount:0,
          grand_total:'',
          customer_discount: 0,
          customer_group_discount: 0,
          total_collect_amount:0, 
          paid_amount:0,
          return_amount:0,
          order_discount_value:0,
          status:'', 
          payments:[{amount:0, paid_by:'cash', gift_card:'', bank_id:'', card_reference_code:'', wallet_id:'',payment_note:'',redeem_point:''}]
        }),
        searchIteam: '',
        arrowCounter: -1,
        searchProduct: [],
        singleProductItem: '', 
        couponDiscoun:0,
    };
  },
  props: {
    props: ['cartItem'],
  }, 
  methods: {  
    checkItem(item){ 
     for (var i = 0; i < this.pform.payments.length; i++) { 
       if(this.pform.payments[i].paid_by == item) {
        return true; 
       } 
     } 
    },
    onkeyPress: function(field) { 
      for (var k in this.errors){     // Loop through the object
        if(k==field){      // If the current key contains the string we're looking for 
          delete this.errors[k];  // Delete obj[key];
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
    checkAboveAmount:  function(event,i){  
      let keyCode = event.keyCode;      
      var mask = document.getElementById('payingby_'+event.target.id.split("_")[1]);  
      if(mask.value !='cash'){
        if(this.netAmountCalculate < event.target.value){
          event.target.value =this.netAmountCalculate;
          this.$toast.warning("Not allow max amount ");
          event.preventDefault();
        }         
      } 
    },
    morePayemnt : function(){  
      let paymentsPaid_by = [];
      for (var i = 0; i < this.pform.payments.length; i++) { 
        paymentsPaid_by.push(this.pform.payments[i].paid_by); 
      }
      let paid_by = '';
      for (var p = 0; p < this.paying_by.length; p++) { 
        if(paymentsPaid_by.indexOf(this.paying_by[p].value) == -1){     
          paid_by = this.paying_by[p].value;
          break;  
        }  
      }
      paymentsPaid_by.splice(0);
      let r = (Math.random() + 1).toString(36).substring(7);
      const newPayment = { 
        id: r
      };  
      if(!paid_by){
        this.$toast.error('Payment type not available');
      }else{
      const newObj = {amount:0, paid_by:paid_by, bank_id:'', card_reference_code:'', gift_card:'', wallet_id:'',payment_note:'',redeem_point:''}
      this.more_payemnts.push(newPayment);
      this.pform.payments.push(newObj);
      }
    },
    morePayemntRemove : function(index){  
      this.more_payemnts.splice(index, 1);
      this.pform.payments.splice((index+1), 1);
    },
    handleLogin: function(){     
      this.isSubmit = true;  
      var postEvent = axios.post(this.apiUrl+'/auth/unlockDiscount', this.user, this.headerjson);
      postEvent.then(res => { 
        this.isSubmit = false;
        this.disabled = false;  
        console.log('enabledDiscount', res.data.data) 
        if(res.data){
          if(res.data.data.discount){
            this.enabledDiscount = true;
          }else{
            this.$toast.error('Discount are not allowed for you');
          }
          
        } 
      }).catch(err => {  
          this.isSubmit = false;
          this.disabled = false;
          this.$toast.error(err.response.data.message); 
      }) 
    },
    handleSubmit: function(){  
      this.isSubmit = true;
      this.pform.items = JSON.parse(JSON.stringify(this.$store.getters.cartItems)) 
      this.pform.order_discount = this.form.order_discount;
      this.pform.customer_id = this.form.customer_id;
      if(this.editMode){ 
          var postEvent = axios.put(this.apiUrl+'/sales/'+this.form.id, this.pform, this.headerjson);
      }else{
          var postEvent = axios.post(this.apiUrl+'/sales', this.pform, this.headerjson);
      }  
      postEvent.then(res => { 
        this.isSubmit = false;
        this.disabled = false;
        if (res.status == 200) {
          this.paymentModal(); 
          this.$swal({
            html: "<h3 style='color:red'>"+this.pform.return_amount.toLocaleString()+"</h3>",
            title: 'Return Amount',   
            confirmButtonCategory: '#3085d6',
            cancelButtonCategory: '#d33',
            confirmButtonText: 'Ok!'
          });
          this.pform.reset();
          this.cform.reset();
          this.form.reset();
          this.$toast.success(res.data.message);
          this.$store.dispatch('removeAllCartItems')  
          axios.get(this.apiUrl+'/posproducts', this.headers).then(res => {
            this.$store.commit("UPDATE_PRODUCT_ITEMS",res.data.data);
          })
        } else {
          this.$toast.error(res.data.message);
        } 
      }).catch(err => {
        this.isSubmit = false;
        this.disabled = false; 
      }); 
    },
    onArrowDown: function(event) { 
      if (this.searchProduct.length > 0) {
        this.arrowCounter = event.code == "ArrowDown" ? ++this.arrowCounter : --this.arrowCounter;
        if (this.arrowCounter >= this.searchProduct.length)
        this.arrowCounter = (this.arrowCounter) % this.searchProduct.length;
        else if (this.arrowCounter < 0)
        this.arrowCounter = this.searchProduct.length + this.arrowCounter;
        this.selectProduct(this.searchProduct[this.arrowCounter]);            
        this.singleProductItem = this.searchProduct[this.arrowCounter];
      }
    },
    
    onArrowUp: function(event) { 
      if (this.searchProduct.length > 0) {
        this.arrowCounter = event.code == "ArrowUp" ? --this.arrowCounter : ++this.arrowCounter;
        if (this.arrowCounter >= this.searchProduct.length)
        this.arrowCounter = (this.arrowCounter) % this.searchProduct.length;
        else if (this.arrowCounter < 0)
        this.arrowCounter = this.searchProduct.length + this.arrowCounter;
        this.selectProduct(this.searchProduct[this.arrowCounter]);
        this.singleProductItem = this.searchProduct[this.arrowCounter];
      }
    },

    selectProduct(item) {   
        this.searchIteam = item.product_name +' ('+ item.product_code + ')'; 
    }, 

    setResult(item) { 
        // this.arrowCounter = -1;
        this.$store.dispatch('addCartItem',item); 
        // this.searchIteam = item.product_name +' ('+ item.product_code + ')'; 
        this.searchIteam = ''; 
        this.searchProduct = [];
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
      if(this.singleProductItem !=''){
        this.$store.dispatch('addCartItem', this.singleProductItem);
      }else{
        this.$toast.warning('Product not found!');
      } 
      this.singleProductItem = ''; 
      this.searchIteam = ''; 
      this.searchProduct = [];
    },

    inputChanged(event) {            
        if (event.code == "ArrowUp" || event.code == "ArrowDown")
            return;            
        this.searchProduct = [];
        if (event.code == "Enter")
            return;
        if(this.searchIteam == '') 
            return;        
        var filtered = this.$store.getters.productItems.filter(item => {
            let item_data = item.product_name +' ('+ item.product_code + ')';
            return item_data.toLowerCase().match(this.searchIteam.toLowerCase());
        });
        // this.isOpen = true
        this.searchProduct.push(...filtered);
         let match_data = this.searchProduct[this.arrowCounter] ? this.searchProduct[this.arrowCounter].product_name +' ('+ this.searchProduct[this.arrowCounter].product_code +')' : '';
        if(match_data != this.searchIteam) {
            this.arrowCounter = -1;
        }

    },

    toggleReadonly(event){
      event.preventDefault()
      if(event.target.getAttribute('readonly') == 'readonly'){
        event.target.removeAttribute('readonly')
      }else{
        event.target.setAttribute('readonly', 'readonly')
      }
    },
    payingBy(event,e){ 
      var mask = document.getElementById('amount_'+e);
      if(this.pform.payments[e].paid_by =='redeem_point'){  
        mask.readOnly = true;   
      }else{
        mask.readOnly = false;
      }
    },
    payingByAnother(event,e){ 
      if(this.pform.payments[e].paid_by !='cash'){
        this.paymentModal(); 
      }
      this.pform.payments[0].paid_by = 'cash';
      var mask = document.getElementById('amount_'+e);
      if(this.pform.payments[e].paid_by =='redeem_point'){  
        mask.readOnly = true;   
      }else{
        mask.readOnly = false;
      }
    },
    redeemPointCheck: function(event, e){ 
      if(this.pform.payments[e].redeem_point > this.customer.points){
        this.pform.payments[e].amount = (this.convartRate * this.customer.points);
        this.pform.payments[e].redeem_point = this.customer.points;
      }else{
        this.pform.payments[e].amount = parseFloat(Number(this.convartRate * this.pform.payments[e].redeem_point).toLocaleString()) 
      }
    },
    applyCoupons(){ 
      var YmdFormat = new Date().toJSON().slice(0,10).replace(/-/g,'-'); 
      if(!this.form.coupons_code){
        this.$toast.error('Please enter your coupon code');
        return false
      } 
      this.isSubmit=true; 
      var postEvent = axios.get(this.apiUrl+'/coupons/?code='+this.form.coupons_code, this.headerjson);  
      postEvent.then(res => {
        this.isSubmit=false; 
        this.form.coupons_code='';
        this.couponDiscoun=0;
        if(res.status == 200){   
          if(res.data.data.length>0){
            if(res.data.data[0].expires_at>=YmdFormat){ 
              if(res.data.data[0].is_fixed){
                this.couponDiscoun = res.data.data[0].discount_amount
              }else{ 
                this.couponDiscoun = this.totalCartValue*parseFloat(res.data.data[0].discount_amount)/100 
              } 
              this.$toast.success('Coupon applied');
            }else{
              this.$toast.error('Expired coupon');
            }   
          }else{
            this.$toast.error('Please enter valid coupon code');
          }          
        }else{
          this.$toast.error(res.data.message);
        } 
      }).catch(err => { 
        this.isSubmit = false;  
        this.$toast.error(err.response.data.message);
        if(err.response.status == 422){ 
          this.errors = err.response.data.errors  
        } 
      });
    },
    resetAll(){

    },
    handleTax: function(){
      this.taxModal();
    }, 
    selectCustomer(event) {  
      this.customer = this.state.customers.find(e => e.value == event);   
    },
    handleCustomer: function() {  
      var postEvent = axios.post(this.apiUrl+'/customers/', this.cform, this.headerjson);  
      postEvent.then(res => {
        if(res.status == 200){ 
          this.cform.reset();
          this.form.customer_id = res.data.data.id
          this.selectCustomer(res.data.data.id)
          this.fetchCustomers();
          this.customerModal();
          this.$toast.success(res.data.message); 
          this.$toast.success(res.data.data); 
        }else{
          this.$toast.error(res.data.message);
        } 
      }).catch(err => {   
          this.$toast.error(err.response.data.message);
          if(err.response.status == 422){ 
            this.errors = err.response.data.errors  
          } 
      }); 
    },
    handleResize() { 
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight-160;
      var body = document.body; 
      var b = document.querySelector("body"); 
      var mask = document.getElementById('leftside-menu-container'); 
      document.getElementsByTagName('body')[0].classList.add('sidebar-enable'); 
      if(b.hasAttribute('data-leftbar-compact-mode')){
        mask.style.overflowY = 'auto';
        b.removeAttribute('data-leftbar-compact-mode')
      }else{ 
        mask.style.overflowY = 'visible';
        b.setAttribute("data-leftbar-compact-mode", "condensed");
      }
    },
    deleteItem: function(item) {
      this.items.splice(this.items.indexOf(item), 1);
    }, 
    random_num(min=12,max=15){
      const d1 = new Date();
      const result = d1.getTime(); 
      this.form.product_code = result;  
    },

    ...mapActions(['removeAllCartItems','removeCartItem','addCartItem']),
  },
  created(){    
    //console.log('cartInfo',this.$store.getters.cartInfo);
    //this.dicountCalculate();  
    this.handleResize();  
    //this.fetchCustomers(); 
  },
  destroyed() {
    //window.removeEventListener('resize', this.handleResize);
  },
  mounted(){
    window.scrollTo(0,0);  
  },
  beforeUpdate(){
    if((this.totalCartValue > 0) && (this.form.customer_id)){
      console.log(this.convartRate)
      //state.points_setting.cart_price_rate / state.points_setting.cart_points_rate * customer.points
      this.disabled = false;
    }else{
      this.disabled = true;
    } 
    if(!this.discountModalActive){
      this.enabledDiscount = false; 
    } 
  },
  computed:{
    ...mapGetters([
      'productItems',
      'cartItems', 
      'cartTotal',
      'cartQuantity'
    ]), 
    specialDiscount: function(){
      let order_discount = 0; 
      let checkNumber = !isNaN(this.form.order_discount)  
      if(this.form.order_discount){
        let percentage = this.form.order_discount.split('%');       
        if(percentage.length > 1){ 
          order_discount = (this.totalCartValue*parseFloat(percentage[0]))/100; 
        }else{
          order_discount = checkNumber ? parseFloat(this.form.order_discount) : 0;
        } 
      } 
      this.pform.order_discount_value = order_discount;
      return this.special_discount = order_discount;
    },
    invoiceVat: function(){
      let order_vat = 0; 
      let checkNumber = !isNaN(this.form.order_vat)  
      if(this.form.order_vat){
        let percentage = this.form.order_vat.split('%');       
        if(percentage.length > 1){ 
          order_vat = (this.totalCartValue*parseFloat(percentage[0]))/100; 
        }else{
          order_vat = checkNumber ? (this.totalCartValue*parseFloat(this.form.order_vat))/100 : 0;
        } 
      }  
      this.pform.order_vat = order_vat
      return order_vat;
    },
    customer_discount: function(){
      let customer_discount = this.totalCartValue*parseFloat(this.customer.discount)/100
      this.pform.customer_discount = customer_discount;
      return  customer_discount ? customer_discount : 0 
    },
    customer_group_discount: function(){
      let customer_group_discount = this.totalCartValue*parseFloat(this.customer.group_discount)/100
      this.pform.customer_group_discount = customer_group_discount;
      return customer_group_discount ? customer_group_discount : 0
    },
    netAmountCalculate: function(){ 
      let totalDiscount = this.all_discount + this.cartItemDiscount + this.specialDiscount + this.customer_group_discount+this.customer_discount;
      let totalVatTax = this.invoiceVat + this.totalCartTax;
      let result = this.totalCartValue + totalVatTax - totalDiscount;
      this.pform.grand_total = result;

      let collection = this.pform.payments.map(item => parseFloat(item.amount)).reduce((prev, curr) => prev + curr, 0); 
      this.pform.total_collect_amount = collection;

      let extraMoney = this.pform.grand_total - this.pform.total_collect_amount;      
      if(extraMoney <= 0){
        extraMoney = this.pform.total_collect_amount - this.pform.grand_total;
        this.pform.status = 'paid'
      }else {
        this.pform.status = 'partial'
        extraMoney = 0;
      }
      this.pform.return_amount = extraMoney
      this.pform.order_items_vat = this.totalCartTax;
      this.pform.total_amount = this.totalCartValue;
      this.pform.payments[0].amount = result.toFixed();
      return result.toFixed();

    }, 
    totalQuantity: function(){ 
      return this.items.reduce(function(total, item){
        return total + item.price; 
      },0);
    }, 
    totalSumm: function(){
      return this.items.reduce(function(total, item){
        return total + (item.price * item.qty); 
      },0);
    }, 
  }
}

</script>
<style scoped> 
#outline-buttons-preview {
  margin: 5px 31px 10px -10px;
}
.w-full.rounded.bg-white.border.border-gray-300.px-4.py-2.space-y-1.absolute.z-10 {
  position: absolute;
  z-index: 9999;
}
.form-group {
  margin-bottom: 5px ;
}
.border.success.item-head {
  width: 20px !important;
  color: #f4f4f4;
  background-color: #3e81ae; 
} 
.table-bordered th, .table-bordered td {
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

.modal-content.scrollbar-width-thin {
  min-width: 90% !important;
}

.btn-square-md {
  width: 100px !important;
  max-width: 100% !important;
  max-height: 100% !important;
  height: 100px !important;
  text-align: center;
  padding: 0px;
  font-size:12px;
} 
.payment{
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
  margin-top: 1px !important;
}
.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  /* height: 120px; */
  overflow: auto;
  width: 100%;
}

.autocomplete-results li{
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
  /* background: red; */
}

.autocomplete-results li.isActive,
.autocomplete-results li:hover {
  background-color: #4AAE9B;
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
</style>