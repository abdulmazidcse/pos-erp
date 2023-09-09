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
import AccountList from "@/views/page/Account.vue";


import PermissionModule from "@/views/menu_and_permissions/ModuleOrMenu.vue";
import PermissionAction from "@/views/menu_and_permissions/PermissionAction.vue";

const route_list = [
  { 'path': '/', 'name': 'Dashboard', 'component': 'Home', 'icon_name': 'fas fa-th', 'is_nav': true, 'children': [] },
  { 'path': '/pos', 'name': 'Pos', 'component': 'Pos', 'icon_name': 'fas fa-th', 'children': [] },
  {
    'path': '/sale', 'name': 'Sales', 'component': '', 'icon_name': 'fas fa-th',
    'children': [
      { 'path': '/pos-sales', 'name': 'Pos Sale', 'component': 'sales/PosSales', 'icon_name': 'fas fa-folder' },
      { 'path': '/al-sales', 'name': 'All Sale', 'component': 'sales/PosSales', 'icon_name': 'fas fa-folder' },
    ]
  },
  {
    'path': '/purchase', 'name': 'Purchase', 'component': '', 'icon_name': 'fas fa-th',
    'children': [
      { 'path': '/purchase-order', 'name': 'Order', 'component': 'purchase/PurchaseOrder', 'icon_name': 'fas fa-folder' },
      {
        'path': "/purchase-order/:id/edit",
        'name': "PurchaseOrderEdit",
        'component': 'purchase/PurchaseOrderEdit',
        'icon_name': 'fas fa-folder'
      },
      {
        'path': "/purchase-order-list",
        'name': "PurchaseOrderList",
        'component': 'purchase/PurchaseOrderList',
        'icon_name': 'fas fa-folder'
      },
    ]
  },
  {
    'path': '/requisition', 'name': 'Requisition', 'component': '', 'icon_name': 'fas fa-th',
    'children': [
      { 'path': '/store-purchase-requisition', 'name': 'Store Purchase', 'component': 'purchase_requisition/StorePurchaseRequisition', 'icon_name': 'fas fa-folder' },
    ]
  },
];

console.log("navigation_list", store.getters.userNavigations);
console.log("route_list", store.getters.userRoutes);
console.log("all_permission", store.getters.userAllPermissions);

var navigation_array = store.getters.userNavigations;

var route_list_array = [];
console.log('route_list', route_list);
for (let i = 0; i < route_list.length; i++) {

  var navigation_child_array = [];
  for (let j = 0; j < route_list[i].children.length; j++) {
    navigation_child_array.push({
      path: route_list[i].children[j].path,
      name: route_list[i].children[j].name,
      component: route_list[i].children[j].component,
      icon_name: route_list[i].children[j].icon_name,
    });
  }
  // navigation_array.push({
  //   path: route_list[i].path,
  //   name: route_list[i].name,
  //   component:route_list[i].component,
  //   icon_name: route_list[i].icon_name,
  //   children: navigation_child_array 
  // });

  if (route_list[i].children.length > 0) {
    for (let j = 0; j < route_list[i].children.length; j++) {
      route_list_array.push({
        path: route_list[i].children[j].path,
        name: route_list[i].children[j].name,
        component: setComponent(route_list[i].children[j].component),
        icon_name: route_list[i].children[j].icon_name,
      });
    }
  } else {
    if (route_list[i].component != '') {
      console.log("item==", route_list[i].component);
      route_list_array.push({
        path: route_list[i].path,
        name: route_list[i].name,
        component: setComponent(route_list[i].component),
        icon_name: route_list[i].icon_name,
      })
    }
  }

}

function setComponent(component_name) {
  // return () => import("@/views/"+component_name+".vue");
  return () => import(`@/views/${component_name}.vue`);
}

route_list_array.push(
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
    path: "/roles",
    name: "Role",
    component: Role,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/roles/permissions/:id",
    name: "RolePermission",
    component: RolePermission,
    icon_name: 'fas fa-folder',
  },
);

navigation_array.push(
  {
    path: '/permission-setting',
    name: 'PermissionSettings',
    component: Home,
    icon_name: 'fas fa-th',
    children: [
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
      }
    ]
  },
  {
    path: "/settings",
    name: "Settings",
    component: Home,
    icon_name: 'fas fa-th',
    children: [
      // {
      //   path: "/districts",
      //   name: "District",
      //   component: Districts,
      //   icon_name: 'fas fa-tachometer-alt' 
      // },
      // {
      //   path: "/outlet",
      //   name: "Outlet",
      //   component: Outlet,
      //   icon_name: 'fas fa-tachometer-alt' 
      // },
      // {
      //   path: "/company",
      //   name: "Company",
      //   component: Company,
      //   icon_name: 'fas fa-tachometer-alt' 
      // },      
      // {
      //   path: "/department",
      //   name: "Department",
      //   component: Department,
      //   icon_name: 'fas fa-tachometer-alt' 
      // },
      // {
      //   path: "/brand",
      //   name: "Brand",
      //   component: Brand,
      //   icon_name: 'fas fa-folder' 
      // }, 
      // {
      //   path: "/unit",
      //   name: "Unit",
      //   component: Unit,
      //   icon_name: 'fas fa-folder' 
      // },
      {
        path: "/roles",
        name: "Role",
        component: Role,
        icon_name: 'fas fa-folder',
      },
      // {
      //   path: "/user",
      //   name: "User",
      //   component: User,
      //   icon_name: 'fas fa-folder',
      // },
      // {
      //   path: "/warehouse",
      //   name: "Warehouse",
      //   component: Warehouse,
      //   icon_name: 'fas fa-folder'
      // }, 
      // {
      //   path: "/color",
      //   name: "Colors",
      //   component: Colors,
      //   icon_name: 'fas fa-folder'
      // },
      // {
      //   path: "/size",
      //   name: "Sizes",
      //   component: Sizes,
      //   icon_name: 'fas fa-folder'
      // },
      // {
      //   path: "/mobile-wallet",
      //   name: "Mobile-wallet",
      //   component: MobileWallet,
      //   icon_name: 'fas fa-folder'
      // }, 
      // {
      //   path: "/coupons",
      //   name: "Coupons",
      //   component: Coupons,
      //   icon_name: 'fas fa-coupons'
      // }, 
    ]
  },
);

console.log("route_list===", route_list_array);
// const routes = route_list_array;
const routes = [
  {
    path: "/",
    name: "Dashboard",
    component: Home,
    icon_name: 'fas fa-th'
  },
  {
    path: "/pos",
    name: "Pos",
    component: Pos,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/districts",
    name: "District",
    component: Districts,
    icon_name: 'fas fa-tachometer-alt'
  },
  {
    path: "/outlet",
    name: "Outlet",
    component: Outlet,
    icon_name: 'fas fa-tachometer-alt'
  },
  {
    path: "/company",
    name: "Company",
    component: Company,
    icon_name: 'fas fa-tachometer-alt',
  },
  {
    path: "/department",
    name: "Department",
    component: Department,
    icon_name: 'fas fa-tachometer-alt'
  },
  {
    path: "/brand",
    name: "Brand",
    component: Brand,
    icon_name: 'fas fa-folder',
  },
  {
    path: "/unit",
    name: "Unit",
    component: Unit,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/product",
    name: "Product",
    component: Product,
    icon_name: 'fas fa-barcode'
  },
  {
    path: "/product-category",
    name: "ProductCategory",
    component: ProductCategory,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/role",
    name: "Role",
    component: Role,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/role-permission/:id",
    name: "RolePermission",
    component: RolePermission,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/user",
    name: "User",
    component: User,
    icon_name: 'fas fa-folder',
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/warehouse",
    name: "Warehouse",
    component: Warehouse,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/customers",
    name: "Customer",
    component: Customer,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/customer-ledger",
    name: "Customer Ledger",
    component: CustomerLedger,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/customer-group",
    name: "Customer group",
    component: CustomerGroup,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/supplier-types",
    name: "SupplierType",
    component: SupplierType,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/suppliers",
    name: "Supplier",
    component: Supplier,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-order",
    name: "PurchaseOrder",
    component: PurchaseOrder,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-order/:id/edit",
    name: "PurchaseOrderEdit",
    component: PurchaseOrderEdit,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-order/:id/requisition-purchase-order-edit",
    name: "StoreRequisitionPurchaseOrderEdit",
    component: StoreRequisitionPurchaseOrderEdit,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-order-list",
    name: "PurchaseOrderList",
    component: PurchaseOrderList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-order-approval-list",
    name: "PurchaseOrderApprovalList",
    component: PurchaseOrderApprovalList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-receive",
    name: "Purchase Receive",
    component: PurchaseReceive,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-receive-warehouse",
    name: "Purchase Receive Warehouse",
    component: PurchaseReceiveWarehouse,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/purchase-receive-list",
    name: "Purchase Receive List",
    component: PurchaseReceiveList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/store-purchase-requisition",
    name: "StorePurchaseRequisition",
    component: StorePurchaseRequisition,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/store-purchase-requisition/:id/edit",
    name: "StoreRequisitionEdit",
    component: StorePurchaseRequisitionEdit,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/store-requisition-list",
    name: "StoreRequisitionList",
    component: StoreRequisitionList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/store-requisition-approval-list",
    name: "StoreRequisitionApprovalList",
    component: StoreRequisitionApprovalList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/store-requisition/:id/purchase-order",
    name: "StoreRequisitionPurchaseOrder",
    component: StoreRequisitionPurchaseOrder,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/color",
    name: "Colors",
    component: Colors,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/size",
    name: "Sizes",
    component: Sizes,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/Taxes",
    name: "Taxes",
    component: Taxes,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/Taxes",
    name: "Taxes",
    component: Taxes,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/mobile-wallet",
    name: "mobile-wallet",
    component: MobileWallet,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/attributes",
    name: "attributes",
    component: Attributes,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/attribute-value",
    name: "attribute-value",
    component: AttributeValue,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/product-expirey-board",
    name: "Expiry Board",
    component: ExpiryBoard,
    icon_name: "fas fa-folder"
  },
  {
    path: "/stock-transfer",
    name: "Stock Transfer",
    component: StockTransfer,
    icon_name: "fas fa-folder"
  },
  {
    path: "/stock-transfer-list",
    name: "Stock Transfer List",
    component: StockTransferList,
    icon_name: 'fas fa-folder'
  },
  {
    path: "/stock-management",
    name: "Stock Management",
    component: StockManagementList,
    icon_name: "fas fa-folder"
  },
  {
    path: "/stock-in-out",
    name: "Stock In/Out",
    component: StockInOut,
    icon_name: "fas fa-folder"
  },
  {
    path: "/coupons",
    name: "Coupons",
    component: Coupons,
    icon_name: "fas fa-folder"
  },
  {
    path: "/points-settings",
    name: "points-settings",
    component: PointsSettings,
    icon_name: "fas fa-tropy"
  },
  {
    path: "/Order-tatal-points",
    name: "Order-tatal-points",
    component: OrderTatalPoints,
    icon_name: "fas fa-tropy"
  },
  {
    path: "/inventory-board",
    name: "Inventory Board",
    component: InventoryBoard,
    icon_name: "fas fa-folder"
  },
  {
    path: "/supplier-ledger",
    name: "Supplier Ledger",
    component: SupplierLedger,
    icon_name: "fas fa-folder"
  },
  {
    path: "/pos-sales",
    name: "POS Sale",
    component: Sales,
    icon_name: "fas fa-folder"
  },
  {
    path: "/all-sales",
    name: "All Sale",
    component: Sales,
    icon_name: "fas fa-folder"
  },
  {
    path: "/account-list",
    name: "AccountList",
    component: AccountList,
    icon_name: "fas fa-folder"
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

];


// const navigations = [
//   {
//     path: "/",
//     name: "Dashboard",
//     component: Home,
//     icon_name: 'fas fa-home',
//     children: []
//   },
//   {
//     path: "/pos",
//     name: "Pos",
//     component: Pos,
//     icon_name: 'fas fa-folder' ,
//     children: []
//   },
//   {
//     path: "/sale",
//     name: "Sales",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/pos-sales",
//         name: "POS Sale",
//         component: Sales,
//         icon_name: 'fas fa-barcode' 
//       }, 
//       {
//         path: "/all-sales",
//         name: "All Sales",
//         component: Sales,
//         icon_name: 'fas fa-barcode' 
//       },  
//     ]
//   },
//   {
//     path: "/purchase",
//     name: "Purchase",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/purchase-order",
//         name: "Order",
//         component: PurchaseOrder,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/purchase-order-list",
//         name: "Order List",
//         component: PurchaseOrderList,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/purchase-order-approval-list",
//         name: "Order Approval List",
//         component: PurchaseOrderApprovalList,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/purchase-receive",
//         name: "Receive",
//         component: PurchaseReceive,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/purchase-receive-warehouse",
//         name: "Receive Warehouse",
//         component: PurchaseReceiveWarehouse,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/purchase-receive-list",
//         name: "Receive List",
//         component: PurchaseReceiveList,
//         icon_name: 'fas fa-folder'
//       }
//     ]
//   },
//   {
//     path: "/requisition",
//     name: "Requisition",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/store-purchase-requisition",
//         name: "Add New",
//         component: StorePurchaseRequisition,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/store-requisition-list",
//         name: "List",
//         component: StoreRequisitionList,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/store-requisition-approval-list",
//         name: "Approval List",
//         component: StoreRequisitionApprovalList,
//         icon_name: 'fas fa-folder'
//       },
//
//     ]
//   },
//   {
//     path: "/transfer",
//     name: "Transfer",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/stock-transfer",
//         name: "Stock Transfer",
//         component: StockTransfer,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/stock-transfer-list",
//         name: "Stock Transfer List",
//         component: StockTransferList,
//         icon_name: 'fas fa-folder'
//       },
//     ]
//   },
//   {
//     path: "/product-settings",
//     name: "Product-Settings",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/product",
//         name: "Product",
//         component: Product,
//         icon_name: 'fas fa-barcode' 
//       }, 
//       {
//         path: "/product-category",
//         name: "Product Category",
//         component: ProductCategory,
//         icon_name: 'fas fa-folder' 
//       },
//       {
//         path: "/attributes",
//         name: "Attributes",
//         component: Attributes,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/attribute-value",
//         name: "Attribute-value",
//         component: AttributeValue,
//         icon_name: 'fas fa-folder'
//       },
//     ]
//   },
//   {
//     path: "stock",
//     name: "Stock",
//     component: Home,
//     icon_name: "fas fa-th",
//     children: [
//       {
//         path: "/stock-in-out",
//         name: "Stock In/Out",
//         component: StockInOut,
//         icon_name: "fas fa-folder"
//       },
//       {
//         path: "/stock-management",
//         name: "Stock Management",
//         component: StockManagementList,
//         icon_name: "fas fa-folder"
//       }
//     ]
//   },
//   {
//     path: "/product-expirey-board",
//     name: "Expiry Board",
//     component: ExpiryBoard,
//     icon_name: "fas fa-folder",
//     children: []
//   },
//   {
//     path: "/inventory-board",
//     name: "Inventory Board",
//     component: InventoryBoard,
//     icon_name: "fas fa-folder",
//     children: []
//   },
//   {
//     path: "accounts",
//     name: "Accounts",
//     component: Home,
//     icon_name: "fas fa-folder",
//     children: [
//       {
//         path: "/account-list",
//         name: "Account List",
//         component: AccountList,
//         icon_name: "fas fa-folder"
//       }
//     ]
//   },
//   {
//     path: "reports",
//     name: "Reports",
//     component: Home,
//     icon_name: "fas fa-folder",
//     children: [
//       {
//         path: "/supplier-ledger",
//         name: "Supplier Ledger",
//         component: SupplierLedger,
//         icon_name: "fas fa-folder"
//       },
//     ]
//   },
//   {
//     path: "/suppliers-setting",
//     name: "Suppliers",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/supplier-types",
//         name: "SupplierType",
//         component: SupplierType,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/suppliers",
//         name: "Supplier",
//         component: Supplier,
//         icon_name: 'fas fa-folder'
//       },
//     ]
//   },
//   {
//     path: "/customer-setting",
//     name: "Customer",
//     component: Home,
//     icon_name: 'fas fa-th',
//     children: [
//       {
//         path: "/customers",
//         name: "Customer",
//         component: Customer,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/customer-group",
//         name: "Customer group",
//         component: CustomerGroup,
//         icon_name: 'fas fa-folder'
//       },
//       {
//         path: "/customer-ledger",
//         name: "Customer ledger",
//         component: CustomerLedger,
//         icon_name: 'fas fa-folder'
//       }
//     ]
//   },
// {
//   path: "/settings",
//   name: "Settings",
//   component: Home,
//   icon_name: 'fas fa-th',
//   children: [
//     {
//       path: "/districts",
//       name: "District",
//       component: Districts,
//       icon_name: 'fas fa-tachometer-alt' 
//     },
//     {
//       path: "/outlet",
//       name: "Outlet",
//       component: Outlet,
//       icon_name: 'fas fa-tachometer-alt' 
//     },
//     {
//       path: "/company",
//       name: "Company",
//       component: Company,
//       icon_name: 'fas fa-tachometer-alt' 
//     },      
//     {
//       path: "/department",
//       name: "Department",
//       component: Department,
//       icon_name: 'fas fa-tachometer-alt' 
//     },
//     {
//       path: "/brand",
//       name: "Brand",
//       component: Brand,
//       icon_name: 'fas fa-folder' 
//     }, 
//     {
//       path: "/unit",
//       name: "Unit",
//       component: Unit,
//       icon_name: 'fas fa-folder' 
//     },
//     {
//       path: "/role",
//       name: "Role",
//       component: Role,
//       icon_name: 'fas fa-folder',
//     },
//     {
//       path: "/user",
//       name: "User",
//       component: User,
//       icon_name: 'fas fa-folder',
//     },
//     {
//       path: "/warehouse",
//       name: "Warehouse",
//       component: Warehouse,
//       icon_name: 'fas fa-folder'
//     }, 
//     {
//       path: "/color",
//       name: "Colors",
//       component: Colors,
//       icon_name: 'fas fa-folder'
//     },
//     {
//       path: "/size",
//       name: "Sizes",
//       component: Sizes,
//       icon_name: 'fas fa-folder'
//     },
//     {
//       path: "/mobile-wallet",
//       name: "Mobile-wallet",
//       component: MobileWallet,
//       icon_name: 'fas fa-folder'
//     }, 
//     {
//       path: "/coupons",
//       name: "Coupons",
//       component: Coupons,
//       icon_name: 'fas fa-coupons'
//     }, 
//   ]
// },  
//   {
//     path: "/rewards-points",
//     name: "Rewards-Points",
//     component: Home,
//     icon_name: 'fas fa-trophy',
//     children: [ 
//       {
//         path: "/points-settings",
//         name: "Points settings",
//         component: PointsSettings,
//         icon_name: 'fas fa-folder' 
//       },  
//     ]
//   }, 
// ];


//const navigations2 = store.getters.userNavigations;
const navigations = store.getters.userNavigations;
const router = createRouter({
  history: createWebHistory(),
  routes,
  navigations
});
router.beforeEach((to, from, next) => {
  if (to.name !== 'Login' && !store.getters.token) next({ name: 'Login' })
  else next()
})
export default router;