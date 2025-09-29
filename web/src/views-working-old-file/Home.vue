<template>
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box">
          <div class="page-title-right">
            <form class="d-flex">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control form-control-light"
                  id="dash-daterange"
                />
                <span
                  class="input-group-text bg-primary border-primary text-white"
                >
                  <i class="mdi mdi-calendar-range font-13"></i>
                </span>
              </div>
              <a href="javascript: void(0);" class="btn btn-primary ms-2">
                <i class="mdi mdi-autorenew"></i>
              </a>
              <a href="javascript: void(0);" class="btn btn-primary ms-1">
                <i class="mdi mdi-filter-variant"></i>
              </a>
            </form>
          </div>
          <h4 class="page-title">Dashboard </h4> 
        </div>
      </div>
    </div>
    <!-- end page title -->
    <!-- <AnalogWatch/> -->
    <Topblock v-if="topBlockLoad" :salesData="salesInfoData"></Topblock>
    <TopblockEmpty v-else /> 
    <ApexchartBlock  v-if="apexchartBlockLoad" 
        :salesVsPurchase="salesVsPurchasesData" 
        :salesData="salesInfoData" 
        :topProducts="topSalesProducts"  ></ApexchartBlock> 
     
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-3 col-md-3 col-6">
            <div class="card widget-flat">
              <div class="card-body">
                <div class="float-end">
                  <i class="mdi mdi-account-multiple widget-icon"></i>
                </div>
                <h5
                  class="text-muted fw-normal mt-0"
                  title="Number of Customers"
                >
                  Customers
                </h5>
                <h3 class="mt-3 mb-3">{{ counter.customer_count }}</h3> 
                <a href="/customers" class="btn btn-outline-dark float-right"
                  >Manage <i class="fas fa-arrow-circle-right"></i
                ></a>
              </div>
              <!-- end card-body-->
            </div>
            <!-- end card-->
          </div>
          <!-- end col-->

          <div class="col-lg-3 col-md-3 col-6">
            <div class="card widget-flat">
              <div class="card-body">
                <div class="float-end">
                  <i class="mdi mdi-cart-plus widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">
                  Orders
                </h5>
                <h3 class="mt-3 mb-3">{{ counter.order_count }}</h3>
                <a href="/pos-sales" class="btn btn-outline-dark float-right"
                  >Manage <i class="fas fa-arrow-circle-right"></i
                ></a>
              </div>
              <!-- end card-body-->
            </div>
            <!-- end card-->
          </div>
          <!-- end col-->
        
          <div class="col-lg-3 col-md-3 col-6">
            <div class="card widget-flat">
              <div class="card-body">
                <div class="float-end">
                  <i class="mdi mdi-account-multiple widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Average Revenue">
                  Suppliers
                </h5>
                <h3 class="mt-3 mb-3">{{ counter.supplier_count }}</h3>
                <a href="/suppliers" class="btn btn-outline-dark float-right"
                  >Manage <i class="fas fa-arrow-circle-right"></i
                ></a>
              </div>
              <!-- end card-body-->
            </div>
            <!-- end card-->
          </div>
          <!-- end col-->

          <div class="col-lg-3 col-md-3 col-6">
            <div class="card widget-flat">
              <div class="card-body">
                <div class="float-end">
                  <i class="mdi mdi-cart widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Growth">
                  Products
                </h5>
                <h3 class="mt-3 mb-3">{{ counter.product_count }}</h3>
                <a href="/product" class="btn btn-outline-dark float-right"
                  >Manage <i class="fas fa-arrow-circle-right"></i
                ></a>
              </div>
              <!-- end card-body-->
            </div>
            <!-- end card-->
          </div>
          <!-- end col-->
        </div>
        <!-- end row -->
      </div>
      <!-- end col -->  
    </div>
    <!-- end row -->  

    <div class="row">
      <div class="col-lg-6">
        <div class="card" v-if="permission['dashboard-quick-links-view']">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                <i class="fas fa-link"></i> Quick Links
              </h4>
            </div>

            <div class="row">
              <div class="col-lg-4 mb-3">
                <a href="/pos"
                  ><button
                    type="button"
                    class="btn btn-outline-info"
                    style="width: 100%"
                  >
                    <i class="fas fa-shopping-cart"></i> POS
                  </button></a
                >
              </div>
              <div class="col-lg-4 mb-3">
                <a href="/purchase-order"
                  ><button
                    type="button"
                    class="btn btn-outline-info"
                    style="width: 100%"
                  >
                    <i class="mdi mdi-cart-plus"></i> Purchase Order
                  </button></a
                >
              </div>
              <div class="col-lg-4 mb-3">
                <a href="/store-purchase-requisition"
                  ><button
                    type="button"
                    class="btn btn-outline-info"
                    style="width: 100%"
                  >
                    <i class="mdi mdi-cart-plus"></i> New Requisition
                  </button></a
                >
              </div>
              <div class="col-lg-4 mb-3">
                <a href="/stock-management"
                  ><button
                    type="button"
                    class="btn btn-outline-success"
                    style="width: 100%"
                  >
                    <i class="fas fa-shopping-cart"></i> Stock Management
                  </button></a
                >
              </div>
              <div class="col-lg-4 mb-3">
                <a href="/stock-in-out"
                  ><button
                    type="button"
                    class="btn btn-outline-success"
                    style="width: 100%"
                  >
                    <i class="fas fa-shopping-cart"></i> Stock In/Out
                  </button></a
                >
              </div>
              <div class="col-lg-4 mb-3">
                <a href="/stock-transfer"
                  ><button
                    type="button"
                    class="btn btn-outline-success"
                    style="width: 100%"
                  >
                    <i class="fas fa-shopping-cart"></i> Stock Transfer
                  </button></a
                >
              </div>
            </div>
          </div>
          <!-- end card-body-->
        </div>
        <!-- end card-->
      </div>
      <!-- end col-->
 
      <!-- end col-->
    </div>
    <!-- end row --> 
  </div>
</template>
<script type="text/javascript">
import { mapGetters, mapActions } from "vuex";
import { ref, onMounted, getCurrentInstance } from "vue";
import Form from "vform";
import axios from "axios";
import Datatable from "@/components/Datatable.vue";
import Pagination from "@/components/Pagination.vue";
import Topblock from "@/views/dashboard/Topblock.vue";
import TopblockEmpty from "@/views/dashboard/TopblockEmpty.vue";
import ApexchartBlock from "@/views/dashboard/ApexchartBlock.vue";
import AnalogWatch from '@/views/page/AnalogWatch.vue';
export default {
  name: "Dashboard",
  components: {
    Datatable,
    Pagination,
    Topblock,
    TopblockEmpty,
    ApexchartBlock,
    AnalogWatch
  },
  props: {
    language: {
      type: Object,
      default: () => {
        return {
          lengthMenu: null,
          info: null,
          zeroRecords: null,
          search: null,
        };
      },
    },
  },
  data() {
    return {
      errors: {},
      btn: "Create",
      items: [],
      counter: {
        customer_count: 0,
        order_count: 0,
        supplier_count: 0,
        product_count: 0,
      },  
      topBlockLoad: false,
      apexchartBlockLoad: false, 
      salesInfoData:{}, 
      salesVsPurchasesData:{}, 
      topSalesProducts:{}, 
    };
  },

  created() {
    // this.checkLogin();
    this.fetchDashboardData(); 
  },
  methods: {
    checkLogin: function (e) {
      const app = getCurrentInstance();
      app.appContext.config.globalProperties.headers = {
        headers: {
          Authorization: this.$store.getters.token
            ? `Bearer ${this.$store.getters.token}`
            : "",
          "Content-Type": "multipart/form-data",
        },
      };

      app.appContext.config.globalProperties.headerjson = {
        headers: {
          Authorization: this.$store.getters.token
            ? `Bearer ${this.$store.getters.token}`
            : "",
          "Content-Type": "application/json",
        },
      };

      if (!this.$store.getters.token) {
        window.location.href = "/login";
      }
    },

    fetchDashboardData() {
      this.topBlockLoad = false;
      this.apexchartBlockLoad = false; 
      axios
        .get(this.apiUrl + "/dashboard", this.headerjson)
        .then((resp) => {
          this.counter = resp.data.data;
          this.salesInfoData = resp.data.data.annual_sales_report;
          this.topSalesProducts = resp.data.data.top_sales_products;
          this.salesVsPurchasesData = resp.data.data.sales_vs_purchases;
          this.topBlockLoad = true;
          this.apexchartBlockLoad = true; 
          //console.log('counters', resp.data.data);
        })
        .catch((err) => {
          this.$toast.error(err.response.data.message);
        });
    },   
    // datatable For Pagination
  },
  computed: {
    permission() {
      let pname = this.$route.meta.parent_module;
      let module_name = this.$route.meta.module_name;
      let path_name = this.$route.path;
      let data = "";
      if (this.$route.meta.parent_module) {
        data =
          this.$store.getters.userAllPermissions[pname][0].children[path_name];
      } else {
        data =
          this.$store.getters.userAllPermissions[module_name][0].other_actions;
      }
      return data;
    },
    ...mapGetters(["userData", "token"]),
  },
};
</script>

<style>
.product_stock_report .table-responsive {
  padding: 0 !important;
  overflow-x: visible !important;
}
</style>
