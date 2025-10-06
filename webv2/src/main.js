import { createApp } from 'vue';
import App from './App.vue';

import router from './router';
import store from './store';

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { fas } from '@fortawesome/free-solid-svg-icons';

library.add(fas);
dom.watch();

import notification from '@kyvg/vue3-notification';
import Toaster from '@meforma/vue-toaster';
// import Toast from "vue-toastification";
import VueSweetalert2 from 'vue-sweetalert2';
import Vue3TreeSelect from "vue3-treeselect";
import VueBarcode from '@chenfengyuan/vue-barcode';
import VueApexCharts from "vue3-apexcharts";
import Vue3Transitions from 'vue3-transitions';
import VueformMultiselect from '@vueform/multiselect';
import moment from 'moment';

import VueHtmlToPaper from './plugins/VueHtmlToPaper';

import "bootstrap/dist/js/bootstrap.min.js";
import 'sweetalert2/dist/sweetalert2.min.css';
// import 'vue-toastification/dist/index.css';
import 'vue3-treeselect/dist/vue3-treeselect.css';
// import 'material-icons/iconfont/material-icons.css';

require('@/assets/css/icons.min.css');
require('@/assets/css/app.min.css');
require('@/assets/css/style.css');
// require("unicons");

const app = createApp(App);

// Global properties
app.config.globalProperties.headers = {
  headers: {
    'Authorization': store.getters.token ? `Bearer ${store.getters.token}` : "",
    'Content-Type': 'multipart/form-data'
  }
};

app.config.globalProperties.headerjson = {
  headers: {
    'Authorization': store.getters.token ? `Bearer ${store.getters.token}` : "",
    'Content-Type': 'application/json'
  }
};

app.config.globalProperties.headerparams = {
  'Authorization': store.getters.token ? `Bearer ${store.getters.token}` : "",
  'Content-Type': 'application/json'
};

// API URL setup (your logic)
const isLocalhost = ['localhost:8081', '127.0.0.1:8081'].includes(location.host);
app.config.globalProperties.apiUrl = isLocalhost ? "http://127.0.0.1:8000/api" : "https://drmpos.24sevenbd.com/backend/api";
app.config.globalProperties.baseUrl = isLocalhost ? "http://127.0.0.1:8000" : "https://drmpos.24sevenbd.com/backend";
app.config.globalProperties.baseUrlPrintCSS = isLocalhost ? "http://127.0.0.1:8000" : "https://drmpos.24sevenbd.com/backend/public";
app.config.globalProperties.sampleFileUrl = isLocalhost ? "http://127.0.0.1:8000" : "https://drmpos.24sevenbd.com/backend/public";

// Attach moment globally
app.config.globalProperties.$moment = moment;

// Turn off warnings
app.config.warnHandler = () => null;

// Register components
app.component('font-awesome-icon', FontAwesomeIcon);
app.component('Multiselect', VueformMultiselect);
app.component('Treeselect', Vue3TreeSelect);
app.component(VueBarcode.name, VueBarcode);

// Use plugins
app.use(router);
app.use(store);
app.use(notification);
app.use(Vue3Transitions);
app.use(Toaster);
// app.use(Toast);
app.use(VueSweetalert2);
app.use(VueApexCharts);
app.use(VueHtmlToPaper);

// Mount app
app.mount('#app');

// Optional: assign app instance globally
window.Fire = app;
if (process.env.NODE_ENV === 'development') {
  window.logStoreState = () => {
    console.log('Store state:', JSON.parse(JSON.stringify(store.state)));
  };
  
  window.logStoreGetters = () => {
    console.log('Store getters:', Object.keys(store.getters));
  };
}
