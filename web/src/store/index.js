import { createStore } from 'vuex'
import product from './modules/product';
import RequisitionProduct from './modules/product/RequisitionProducts';
import cart from './modules/cart';
import login from './modules/login';
import UserMenuAndPermissions from './modules/user_menu_and_permissions';
import createPersistedState from "vuex-persistedstate";
export default createStore({
  modules: {
    product,
    RequisitionProduct,
    cart,
    login,
    UserMenuAndPermissions,
  },
  plugins: [createPersistedState()],
})