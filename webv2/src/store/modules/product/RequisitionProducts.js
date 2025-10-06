import axios from 'axios';
const state = {
  requisitionProductItems: []
}
const mutations = {
  UPDATE_REQUISITION_PRODUCT_ITEMS (state, payload) {
    state.requisitionProductItems = payload;
  },

  REMOVE_ALL_REQUISITION_PRODUCT (state) {
    state.requisitionProductItems = [];
  }
}

const actions = {
  getRequisitionProductItems ({ commit }, paramData) { 
    axios.get(paramData.url+'/requisition_products', paramData.header).then((response) => {
      commit('UPDATE_REQUISITION_PRODUCT_ITEMS', response.data.data)
    });
  },

  removeAllRequitionProduct({commit}) {
    commit("REMOVE_ALL_REQUISITION_PRODUCT", '')
  }
}

const getters = {
  requisitionProductItems: state => state.requisitionProductItems,
  // productItemById: (state) => (id) => {
  //   return state.productItems.find(productItem => productItem.id === id)
  // }
}

const requisitionProductModule = {
  state,
  mutations,
  actions,
  getters
}

export default requisitionProductModule;