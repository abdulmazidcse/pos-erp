import { createWebHistory, createRouter } from "vue-router";
import store from '../store/' 
import Login from "@/views/page/Login.vue";  
import AppLogin from "@/views/page/LoginFromApp.vue";   

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
  },
   {
    path: '/service',
    name: 'ServiceModule',
    component: () => import('../layouts/ServiceLayout.vue'),
    children: [
      {
        path: 'dashboard',
        name: 'ServiceDashboard',
        component: ServiceDashboard,
        meta: { title: 'সার্ভিস ড্যাশবোর্ড' }
      },
      {
        path: 'complaint',
        name: 'ComplaintRegister',
        component: ComplaintRegister,
        meta: { title: 'অভিযোগ রেজিস্টার' }
      },
      {
        path: 'warranty-check',
        name: 'WarrantyCheck',
        component: WarrantyCheck,
        meta: { title: 'ওয়ারেন্টি চেক' }
      },
      {
        path: 'assignment',
        name: 'TechnicianAssignment',
        component: TechnicianAssignment,
        meta: { title: 'টেকনিশিয়ান অ্যাসাইনমেন্ট' }
      },
      {
        path: 'execution',
        name: 'ServiceExecution',
        component: ServiceExecution,
        meta: { title: 'সার্ভিস এক্সিকিউশন' }
      },
      {
        path: 'billing',
        name: 'BillingSection',
        component: BillingSection,
        meta: { title: 'বিলিং' }
      },
      {
        path: 'delivery',
        name: 'DeliveryHandover',
        component: DeliveryHandover,
        meta: { title: 'ডেলিভারি ও হ্যান্ডওভার' }
      },
      {
        path: 'history',
        name: 'ServiceHistory',
        component: ServiceHistory,
        meta: { title: 'সার্ভিস হিস্ট্রি' }
      },
      {
        path: 'ticket/:id',
        name: 'TicketDetails',
        component: () => import('../components/ServiceModule/TicketDetails.vue'),
        meta: { title: 'টিকিট বিস্তারিত' }
      }
    ]
  }
  // {
  //   path: '/complaint',
  //   name: 'complaint-register',
  //   component: ComplaintRegister ,
  //   icon_name: 'fas fa-folder',
  // },
  // {
  //   path: '/warranty-check',
  //   name: 'warranty-check',
  //   component: WarrantyCheck ,
  //   icon_name: 'fas fa-folder',
  // },
  // { path: '/assignment', 
  //   name: 'assignment', 
  //   component: TechnicianAssignment,
  //   icon_name: 'fas fa-folder',
  // },
  // { path: '/execution', 
  //   name: 'execution', 
  //   component: ServiceExecution,
  //   icon_name: 'fas fa-folder',
  // },
  // { path: '/billing', 
  //   name: 'billing', 
  //   component: BillingSection,
  //   icon_name: 'fas fa-folder',
  // },
  // { path: '/delivery', 
  //   name: 'delivery', 
  //   component: DeliveryHandover,
  //   icon_name: 'fas fa-folder',
  // },
  // { path: '/history', 
  //   name: 'history', 
  //   component: ServiceHistory,
  //   icon_name: 'fas fa-folder',
  // },
     

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