import { createWebHistory, createRouter } from "vue-router";
import store from '../store/'

import Home from "@/views/Home.vue"; 
import Pos from "@/views/Pos.vue";  
import Brand from "@/views/page/Brand.vue"; 
import ProductCategory from "@/views/page/ProductCategory.vue"; 
import Product from "@/views/product/Product.vue"; 
import Notification from "@/views/Notifications.vue"; 
import Districts from "@/views/page/Districts.vue";  
import Company from "@/views/page/Company.vue";  
import Department from "@/views/page/Department.vue";  
import Outlet from "@/views/page/Outlet.vue";   
import Unit from "@/views/page/Unit.vue";  
import Role from "@/views/page/Role.vue";  
import RolePermission from "@/views/page/RolePermission.vue";  
import User from "@/views/page/User.vue";  
import Login from "@/views/page/Login.vue";  
import AppLogin from "@/views/page/LoginFromApp.vue";  
import Warehouse from "@/views/page/Warehouse.vue";  
import Customer from "@/views/page/Customer.vue";  
import CustomerLedger from "@/views/page/CustomerLedger.vue";  
import SupplierType from "@/views/page/SupplierType.vue";   
import CustomerGroup from "@/views/page/CustomerGroup.vue";   
import Supplier from "@/views/page/Supplier.vue";  
import PurchaseOrder from "@/views/purchase/PurchaseOrder.vue";  
import PurchaseOrderEdit from "@/views/purchase/PurchaseOrderEdit.vue";  
import PurchaseOrderList from "@/views/purchase/PurchaseOrderList.vue";  
import PurchaseReceive from "@/views/purchase/PurchaseReceive.vue";  
import PurchaseReceiveWarehouse from "@/views/purchase/WarehousePurchaseReceive.vue";  
import PurchaseReceiveList from "@/views/purchase/PurchaseReceiveList.vue";  
import PurchaseOrderApprovalList from "@/views/purchase/PurchaseOrderApprovalList.vue";  
import StorePurchaseRequisition from "@/views/purchase_requisition/StorePurchaseRequisition.vue";  
import StorePurchaseRequisitionEdit from "@/views/purchase_requisition/StorePurchaseRequisitionEdit.vue";  
import StoreRequisitionList from "@/views/purchase_requisition/StoreRequisitionList.vue";  
import StoreRequisitionApprovalList from "@/views/purchase_requisition/StoreRequisitionApprovalList.vue";  
import StoreRequisitionPurchaseOrder from "@/views/purchase_requisition/StoreRequisitionPurchaseOrder.vue";  
import StoreRequisitionPurchaseOrderEdit from "@/views/purchase_requisition/StoreRequisitionPurchaseOrderEdit.vue"; 
import Sizes from "@/views/page/Sizes.vue";  
import Colors from "@/views/page/Colors.vue";  
import Taxes from "@/views/page/Taxes.vue";  
import Attributes from "@/views/page/Attributes.vue";  
import AttributeValue from "@/views/page/AttributeValue.vue";  
import MobileWallet from "@/views/page/MobileWallet.vue";  
import ExpiryBoard from "@/views/expire_product/ExpiryBoard.vue";
import StockTransfer from "@/views/stock_transfer/StockTransfer.vue";
import StockTransferList from "@/views/stock_transfer/StockTransferList.vue";
import StockManagementList from "@/views/stock/StockManagement.vue";
import StockInOut from "@/views/stock/StockInOut.vue";
import Coupons from "@/views/page/Conpons.vue";  
import RewardsPoints from "@/views/rewards-points/RewardsPoints.vue";  
import PointsSettings from "@/views/rewards-points/PointsSettings.vue";  
import OrderTatalPoints from "@/views/rewards-points/OrderTatalPoints.vue";  
import InventoryBoard from "@/views/inventory/InventoryBoard.vue";
import SupplierLedger from "@/views/accounts/SupplierLedger.vue";
import Sales from "@/views/sales/PosSales.vue";
import BankAccountList from "@/views/account_settings/BankAccount.vue";
import DiscountSettings from "@/views/sales/DiscountSettings.vue";


import PurchaseReceiveBoard from "@/views/purchase/PurchaseReceiveBoard.vue";
import PermissionModule from "@/views/menu_and_permissions/ModuleOrMenu.vue";
import PermissionAction from "@/views/menu_and_permissions/PermissionAction.vue";

// for test
import VoucherEdit from "@/views/accounts/VoucherEntryEdit.vue"; 



const route_list = store.getters.userRoutes;
var route_list_array = [];
for(let i=0; i<route_list.length; i++) {
  route_list_array.push({
    path: route_list[i].path,
    name: route_list[i].name,
    component: setComponent(route_list[i].component),
    icon_name: route_list[i].icon_name,
    meta: {
      module_name: route_list[i].module_name,
      parent_module: route_list[i].parent_module
    }
  });
}
// console.log('route_list', route_list);


function setComponent(component_name) {
  // return () => import("@/views/"+component_name+".vue");
  return () => import(`@/views/${component_name}.vue`);
}

//custom route added 
route_list_array.push(
  {
    path: '/purchase/receive-board',
    name: 'purchase-receive-board',
    component: PurchaseReceiveBoard,
    icon_name: 'fas fa-folder',
  },
  {
    path: '/permissions/module-or-menu',
    name: 'Module/Menu',
    component: PermissionModule,
    icon_name: 'fas fa-folder',
  },
  {
    path: '/permissions/actions',
    name: 'Actions',
    component: PermissionAction,
    icon_name: 'fas fa-folder',
  },
  {
    path: '/Login',
    name: 'Login',
    component: Login,
    icon_name: 'fas fa-folder',
  },
  {
    path: '/app-login',
    name: 'AppLogin',
    component: AppLogin,
    icon_name: 'fas fa-folder',
  },
  {
    path: '/accounting/voucher-edit/:id',
    name: 'VoucherEdit',
    component: VoucherEdit,
    icon_name: 'fas fa-folder',
  } 

)

const routes = route_list_array;
const navigations = store.getters.userNavigations;

// navigations.push();

const router = createRouter({
  history: createWebHistory(),
  routes,
  navigations
});
// router.beforeEach((to, from, next) => {
//   if (to.name !== 'Login' && !store.getters.token) next({ name: 'Login' })
//   else next()
// }) 

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters.token; // Replace with your method to check auth 
  if ((to.name !== 'Login' && to.name !== 'AppLogin') && !isAuthenticated) {
    next({ name: 'Login' }); // Redirect to Login if not authenticated
  } else {
    next(); // Proceed to the requested route
  }
});
export default router;