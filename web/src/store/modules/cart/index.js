import { notify } from "@kyvg/vue3-notification";
import { useToast } from 'vue-toastification'
const toast = useToast()

import axios from 'axios'; 
const state = {
  cartItems:[],
  cartInfo:{}, 
  countCartItems: 0, 
  hold_sale_info:{},
  edit_sale_info:'',
  sale_id:'',
}

const mutations = {
  UPDATE_CART_ITEMS (state, payload) {  
    const cartItemProduct = JSON.stringify(payload);  
    const itemProduct = JSON.parse(cartItemProduct);  
    const newCartProduct = {
      product_id: itemProduct.product_id,
      product_stock_id: itemProduct.product_stock_id,
      product_name: itemProduct.product_name, 
      mrp_price: itemProduct.mrp_price,
      product_code: itemProduct.product_code,
      discount: itemProduct.discount,
      item_discount: itemProduct.item_discount,
      tax: itemProduct.tax,
      expires_date: itemProduct.expires_date,
      quantity: itemProduct.min_order_qty ? itemProduct.min_order_qty : 1,
      stock_quantity: itemProduct.stock_quantity,
      weight: itemProduct.weight,
      measuring_unit: itemProduct.measuring_unit,
      min_order_qty:itemProduct.min_order_qty, 
      uom:itemProduct.measuring_unit,
      dis_array:itemProduct.dis_array, 
    }; 
    const cartProducts =  state.cartItems;
    let cartProductExists = false;
    cartProducts.map((cartProduct) => {
      if((cartProduct.product_id === newCartProduct.product_id) && (cartProduct.product_stock_id === newCartProduct.product_stock_id)){
        cartProduct.quantity++;
        cartProductExists = true;
      }
    });
    if (!cartProductExists) cartProducts.push(newCartProduct);     
  },
  UPDATE_HOLD_ITEMS (state, payload) {  
    const cartItemProduct = JSON.stringify(payload);  
    const itemProduct = JSON.parse(cartItemProduct); 
    const newCartProduct = {
      product_id: itemProduct.product_id,
      product_stock_id: itemProduct.product_stock_id,
      product_name: itemProduct.product_name, 
      mrp_price: itemProduct.mrp_price,
      product_code: itemProduct.product_code,
      discount: itemProduct.discount,
      item_discount: itemProduct.item_discount,
      discount_percent: itemProduct.discount_percent,
      tax: itemProduct.tax,
      expires_date: itemProduct.expires_date,
      quantity: Number(itemProduct.quantity)
    }; 
    const cartProducts =  state.cartItems;
    let cartProductExists = false;
    cartProducts.map((cartProduct) => {
      if((cartProduct.product_id === newCartProduct.product_id) && (cartProduct.product_stock_id === newCartProduct.product_stock_id)){
        cartProduct.quantity++;
        cartProductExists = true;
      }
    });
    if (!cartProductExists) cartProducts.push(newCartProduct);     
  },
  SELECT_CARTINFO (state, payload) { 
    Object.assign(state.cartInfo, payload);
  },
  removeAllFromcart: (state) =>{  
    state.cartItems.splice(0);
    state.hold_sale_info = {};
    state.countCartItems = 0;     
  }, 
  REMOVE_CART_INFO: (state, index) =>{ 
    //console.log(state.cartInfo)
    //state.cartInfo.splice(index, 1);  
    ///const cartItemProduct = JSON.stringify(state.cartInfo[0]);  
    //const itemProduct = JSON.parse(cartItemProduct);
    //var obj = state.cartInfo[0];

    //[index].forEach(index => delete obj[index]);

    //console.log(state.cartInfo[])

    // for (var k in state.cartInfo){     // Loop through the object       
    //   for(var j in state.cartInfo[k]){
    //     if(index == j){
          // console.log(j)
          // console.log(index)
          // console.log(state.cartInfo[k])
          // console.log(state.cartInfo[k])
          //delete state.cartInfo[k];
    //     }
    //   } 
    // }   
    //for(var i in index) {       
      // console.log('index',i)
      // console.log('cartItemProduct',cartItemProduct.i)
      // console.log('itemProduct',itemProduct.i)
      // console.log('cartInfo-0',state.cartInfo[0][i])
      // console.log('cartInfo',state.cartInfo.i)
      // delete itemProduct.i; 
    //}
    // console.log('REMOVE_CART_INFO',index)
    // console.log('REMOVE_CART_INFO===',itemProduct.order_discount)
   
    //delete itemProduct.index; 
    //delete itemProduct[index]; 
    //state.cartInfo[0].splice(index, 1); 
  },
  REMOVE_CART_ITEMS: (state, index) =>{
    state.cartItems.splice(index, 1);  
  }, 
  UPDATE_HOLD_SALE: (state, payload) => {
    const proxyToObject = Object.assign({}, payload);  
    if( Object.keys(proxyToObject).length > 0){
      state.hold_sale_info = proxyToObject;
    }else{
      state.hold_sale_info = {};
    } 
  }
}

const actions = { 
  addCartItem ({ commit }, cartItem) {   
    commit('UPDATE_CART_ITEMS', cartItem); 
    toast.success("Added to the cart!",{ 
          timeout: 800 
          });     
  },
  addHoldItem ({ commit }, cartItem) {   
    commit('UPDATE_HOLD_ITEMS', cartItem);     
  },
  addHoldSale({ commit }, cartItem) {   
    commit('UPDATE_HOLD_SALE', cartItem); 
    console.log('addHoldSale', cartItem)
    //toast.success("Successfully added product to the cart!");     
  },
  selectCartInfo ({ commit }, infoData) {   
    commit('SELECT_CARTINFO', infoData);      
  },
  removeCartItem ({ commit },  index) {    
    commit('REMOVE_CART_ITEMS', index) 
  },
  removeCartInfo ({ commit },  index) {    
    commit('REMOVE_CART_INFO', index) 
  },
  removeAllCartItems ({ commit }) {
    commit('removeAllFromcart', '')
  }
}

const getters = { 
  saleId: state => state.sale_id,
  cartItems: state => state.cartItems,
  cartInfo: state => state.cartInfo,
  holdSaleInfo: state => state.hold_sale_info,
  cartTotal: state => {
    return state.cartItems.reduce((acc, cartItem) => {
      return (cartItem.quantity * cartItem.price) + acc;
    }, 0).toFixed(2);
  },
  cartQuantity: state => {
    return state.cartItems.reduce((acc, cartItem) => {
      return cartItem.quantity + acc;
    }, 0);
  }
} 

const cartModule = {
  state,
  mutations,
  actions,
  getters
}
export default cartModule;