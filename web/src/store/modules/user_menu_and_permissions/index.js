import axios from 'axios';
const state = {
  userAllPermissions: [],
  userRoutes: [],
  userNavigations: [],
  dataCheck: 0,
}
const mutations = {
  // UPDATE_USER_ALL_PERMISSIONS (state, payload) {
  //   state.userAllPermissions = payload;
  // },

  UPDATE_USER_ALL_PERMISSIONS (state, payload) {
    state.userAllPermissions = payload;
  },

  UPDATE_USER_ROUTES (state, payload) {
    state.userRoutes = payload;
  },
  UPDATE_USER_NAVIGATIONS (state, payload) {
    state.userNavigations = payload;
  },
  UPDATE_USER_DATA_CHECK (state, payload) {
    state.dataCheck = payload;
  },
  RemoveAllRouteMenue: (state) =>{  
    state.userAllPermissions = {};
    state.userRoutes.splice(0);   
    state.userNavigations.splice(0);   
  }
}

const actions = {
  async getUserMenuAndPermissions ({ commit }, params) {
    await axios.get(params.api_url+'/auth/user/menu-and-permissions', params.auth_headers)
    .then((response) => {
      // commit('UPDATE_USER_ALL_PERMISSIONS', response.data.data.user_all_permissions);
      commit('UPDATE_USER_ALL_PERMISSIONS', response.data.data.module_based_navigation);
      commit('UPDATE_USER_ROUTES', response.data.data.user_routes);
      commit('UPDATE_USER_NAVIGATIONS', response.data.data.user_navigations);
      commit('UPDATE_USER_DATA_CHECK', response.data.data.data_check);
    });
  },
  removeAllRoutes ({ commit }) {
    commit('RemoveAllRouteMenue', '')
  }
}

const getters = {
  userAllPermissions: state => state.userAllPermissions,
  userRoutes: state => state.userRoutes,
  userNavigations: state => state.userNavigations,
  dataCheck: state => state.dataCheck,

  // productItemById: (state) => (id) => {
  //   return state.productItems.find(productItem => productItem.id === id)
  // }
}

const userMenuAndPermissionModule = {
  state,
  mutations,
  actions,
  getters
}

export default userMenuAndPermissionModule;