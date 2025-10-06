import axios from 'axios';
const state = {
  productItems: [],
  requsitionItems: []
}
const mutations = {
  UPDATE_PRODUCT_ITEMS (state, payload) {
    state.productItems = payload;
  },
  UPDATE_REQI_ITEMS (state, payload) {
    state.requsitionItems = payload;
  },
  removeAllProducts: (state) =>{  
    state.productItems.splice(0);
    state.requsitionItems.splice(0);   
  }, 
}

const actions = {
  getProductItems ({ commit },apiUrl, headerjson) {
    axios.get(apiUrl+'/posproducts', headerjson).then((response) => {
      console.log( 'UPDATE_PRODUCT_ITEMS',headerjson)
      commit('UPDATE_PRODUCT_ITEMS', response.data.data)
    });
  },
  getRequiItems ({ commit },apiUrl) {
    axios.get(apiUrl+'/posproducts').then((response) => { 
      commit('UPDATE_REQI_ITEMS', response.data.data)
    });
  },
  removeAllProduct ({ commit }) {
    commit('removeAllProducts', '')
  }
}

const getters = {
  productItems: state => state.productItems,
  productItemById: (state) => (id) => {
    return state.productItems.find(productItem => productItem.id === id)
  }
}

const productModule = {
  state,
  mutations,
  actions,
  getters
}

export default productModule;