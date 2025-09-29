import* as Vue from 'vue';
// import { createApp } from 'vue/dist/vue.runtime.esm-bundler'
import { createApp } from 'vue/dist/vue.esm-bundler';

import { createStore } from 'vuex' 
import recipeHelpers from "./helper.js";
import App from './App.vue'
require('@/assets/css/icons.min.css')  
require('@/assets/css/app.min.css')   
require('@/assets/css/style.css')   
 
import router from './router' 
import store   from './store';  
import { mapGetters, mapActions } from "vuex";
import jQuery from 'jquery'
// start for menu icon 
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from '@fortawesome/free-solid-svg-icons'
library.add(fas); 
import { dom } from "@fortawesome/fontawesome-svg-core";
dom.watch(); 
// End for menu icon 


require("unicons");
  
import notification from '@kyvg/vue3-notification' 
import "bootstrap/dist/js/bootstrap.min.js"; 
import Vue3Transitions from 'vue3-transitions'

import Toaster from '@meforma/vue-toaster';


import Toast, { PluginOptions } from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css'; 
 
 
import Vue3TreeSelect from "vue3-treeselect";
// import the styles
import 'vue3-treeselect/dist/vue3-treeselect.css';

// import Autocomplete from "vue3-autocomplete";
// import 'vue3-autocomplete/dist/vue3-autocomplete.css';

import VueBarcode from '@chenfengyuan/vue-barcode';

// For ApexChart
import VueApexCharts from "vue3-apexcharts";
import moment from 'moment'

//import JlDatatable from 'jl-datatable'

import  VueHtmlToPaper from './plugins/VueHtmlToPaper'

import 'material-icons/iconfont/material-icons.css';
 
window.Fire = createApp(App); 
const app = createApp(App); 
import VueformMultiselect from '@vueform/multiselect';  

app.config.globalProperties.headers = {headers: {
                      'Authorization' : store.getters.token ? `Bearer ${store.getters.token}` : "",
                      'Content-Type': 'multipart/form-data' 
                    }};
app.config.globalProperties.headerjson = {headers: {
                      'Authorization' : store.getters.token ? `Bearer ${store.getters.token}` : "",
                      'Content-Type': 'application/json' 
                    }};
app.config.globalProperties.headerparams = {
                      'Authorization' : store.getters.token ? `Bearer ${store.getters.token}` : "",
                      'Content-Type': 'application/json' 
                    }; 

if((location.host == 'localhost:8081') || (location.host == '127.0.0.1:8081')){ 
  // let baseUrl =  "https://drmpos.24sevenbd.com/backend"; 
  let baseUrl =  "http://127.0.0.1:8000"; 
	app.config.globalProperties.apiUrl = baseUrl+"/api";
	app.config.globalProperties.baseUrl = baseUrl; 
	app.config.globalProperties.baseUrlPrintCSS = baseUrl; 
	app.config.globalProperties.sampleFileUrl = baseUrl; 
} else {
	app.config.globalProperties.apiUrl = "https://drmpos.24sevenbd.com/backend/api";
	app.config.globalProperties.baseUrl = "https://drmpos.24sevenbd.com/backend";
	app.config.globalProperties.baseUrlPrintCSS ="https://drmpos.24sevenbd.com/backend/public";
	app.config.globalProperties.sampleFileUrl = "https://drmpos.24sevenbd.com/backend/public";
}  

app.config.globalProperties.$createElement = '';
app.config.globalProperties.reRenderRoute = 0;
app.config.globalProperties.address =  "North Bashaboo, Dhaka 1000";  
app.config.globalProperties.invHeadAddress =  "North Bashaboo, Dhaka 1000";  
app.config.globalProperties.invHeadPhone =  "";// 01739897087  
app.config.globalProperties.prepareBy =  "Khandakar Kudrat E Khuda";  
app.config.globalProperties.checkedBy =  "Khandakar Kudrat E Khuda";  
app.config.globalProperties.approvedBy =  "Khandakar Kudrat E Khuda";  
  
app.config.globalProperties.retailShopName =  "Retail Shop 1.";  
app.config.globalProperties.retailShopAddress =  "North Bashaboo, Dhaka 1000";  

// for turn off warning
app.config.warnHandler = function (msg, vm, trace) {
  return null
}

app.use(router)  
app.use(FontAwesomeIcon)  
app.use(notification) 
app.use(jQuery)   
app.use(store)   
app.use(Vue3Transitions)
app.use(Toaster) 
app.use(Toast); 
app.use(VueSweetalert2)  
app.component('Multiselect', VueformMultiselect)   
app.component("Treeselect", Vue3TreeSelect); 
app.component(VueBarcode.name, VueBarcode);
app.use(VueApexCharts); 
app.use(moment); 
app.use(VueHtmlToPaper)
app.mount('#app')