import { useToast } from 'vue-toastification'

const toast = useToast()

const state = {
  cartItems: [],
  cartInfo: {}, 
  countCartItems: 0, 
  hold_sale_info: {},
  edit_sale_info: '',
  sale_id: '',
}

const mutations = {
  UPDATE_CART_ITEMS(state, payload) {  
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
      min_order_qty: itemProduct.min_order_qty, 
      uom: itemProduct.measuring_unit,
      dis_array: itemProduct.dis_array,
      // ✅ ADD MISSING PRICE FIELD - THIS WAS THE MAIN ISSUE!
      price: itemProduct.mrp_price // Add this line
    }; 
    
    const cartProducts = state.cartItems;
    let cartProductExists = false;
    
    cartProducts.forEach((cartProduct) => {
      if ((cartProduct.product_id === newCartProduct.product_id) && 
          (cartProduct.product_stock_id === newCartProduct.product_stock_id)) {
        cartProduct.quantity++;
        cartProductExists = true;
      }
    });
    
    if (!cartProductExists) {
      cartProducts.push(newCartProduct);
    }
    
    // ✅ UPDATE COUNT_CART_ITEMS TO MATCH ACTUAL ITEMS
    state.countCartItems = state.cartItems.length;
  },
  
  UPDATE_HOLD_ITEMS(state, payload) {  
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
      quantity: Number(itemProduct.quantity),
      // ✅ ADD MISSING PRICE FIELD
      price: itemProduct.mrp_price // Add this line
    }; 
    
    const cartProducts = state.cartItems;
    let cartProductExists = false;
    
    cartProducts.forEach((cartProduct) => {
      if ((cartProduct.product_id === newCartProduct.product_id) && 
          (cartProduct.product_stock_id === newCartProduct.product_stock_id)) {
        cartProduct.quantity++;
        cartProductExists = true;
      }
    });
    
    if (!cartProductExists) {
      cartProducts.push(newCartProduct);
    }
    
    // ✅ UPDATE COUNT_CART_ITEMS TO MATCH ACTUAL ITEMS
    state.countCartItems = state.cartItems.length;
  },
  
  SELECT_CARTINFO(state, payload) { 
    Object.assign(state.cartInfo, payload);
  },
  
  removeAllFromcart: (state) => {  
    state.cartItems.splice(0);
    state.hold_sale_info = {};
    state.countCartItems = 0; // ✅ CORRECTLY RESET COUNT
  }, 
  
  REMOVE_CART_ITEMS: (state, index) => {
    state.cartItems.splice(index, 1);
    // ✅ UPDATE COUNT AFTER REMOVAL
    state.countCartItems = state.cartItems.length;
  }, 
  
  UPDATE_CART_QUANTITY: (state, { index, quantity }) => {
    if (state.cartItems[index]) {
      state.cartItems[index].quantity = quantity;
    }
  },
  
  // ✅ NEW MUTATION TO SYNC COUNTS
  SYNC_CART_COUNT: (state) => {
    state.countCartItems = state.cartItems.length;
  },
  
  UPDATE_HOLD_SALE: (state, payload) => {
    const proxyToObject = Object.assign({}, payload);  
    if (Object.keys(proxyToObject).length > 0) {
      state.hold_sale_info = proxyToObject;
    } else {
      state.hold_sale_info = {};
    } 
  }
}

const actions = { 
  addCartItem({ commit }, cartItem) {   
    commit('UPDATE_CART_ITEMS', cartItem); 
    toast.success("Added to the cart!", { 
      timeout: 800 
    });     
  },
  
  addHoldItem({ commit }, cartItem) {   
    commit('UPDATE_HOLD_ITEMS', cartItem);     
  },
  
  addHoldSale({ commit }, cartItem) {   
    commit('UPDATE_HOLD_SALE', cartItem); 
    console.log('addHoldSale', cartItem);
  },
  
  selectCartInfo({ commit }, infoData) {   
    commit('SELECT_CARTINFO', infoData);      
  },
  
  removeCartItem({ commit }, index) {    
    commit('REMOVE_CART_ITEMS', index);
    // ✅ Show feedback when item is removed
    toast.success("Item removed from cart!", { 
      timeout: 800 
    });
  },
  
  removeCartInfo({ commit }, index) {    
    commit('REMOVE_CART_INFO', index);
  },
  
  removeAllCartItems({ commit }) {
    commit('removeAllFromcart', '');
    toast.success("Cart cleared!", { 
      timeout: 800 
    });
  },
  
  // ✅ NEW ACTION TO UPDATE QUANTITY
  updateCartQuantity({ commit }, { index, quantity }) {
    if (quantity <= 0) {
      commit('REMOVE_CART_ITEMS', index);
    } else {
      commit('UPDATE_CART_QUANTITY', { index, quantity });
    }
  },
  
  // ✅ NEW ACTION TO FIX INCONSISTENCIES
  syncCart({ commit, state }) {
    if (state.cartItems.length !== state.countCartItems) {
      commit('SYNC_CART_COUNT');
      console.warn('Cart count was out of sync. Fixed.');
    }
  }
}

const getters = { 
  saleId: state => state.sale_id,
  cartItems: state => state.cartItems,
  cartInfo: state => state.cartInfo,
  holdSaleInfo: state => state.hold_sale_info,
  
  // ✅ FIXED CART TOTAL - USE mrp_price INSTEAD OF price
  cartTotal: state => {
    return state.cartItems.reduce((acc, cartItem) => {
      // Use mrp_price since that's what you're storing
      const price = cartItem.mrp_price || cartItem.price || 0;
      const quantity = cartItem.quantity || 1;
      return (quantity * price) + acc;
    }, 0).toFixed(2);
  },
  
  // ✅ FIXED CART QUANTITY - COUNT ACTUAL ITEMS
  cartQuantity: state => {
    return state.cartItems.reduce((acc, cartItem) => {
      return (cartItem.quantity || 1) + acc;
    }, 0);
  },
  
  // ✅ NEW GETTER FOR ITEM COUNT (DIFFERENT FROM TOTAL QUANTITY)
  cartItemCount: state => state.countCartItems,
  
  // ✅ NEW GETTER TO CHECK CONSISTENCY
  isCartConsistent: state => {
    return state.cartItems.length === state.countCartItems;
  }
} 

const cartModule = {
  state,
  mutations,
  actions,
  getters
}

export default cartModule;