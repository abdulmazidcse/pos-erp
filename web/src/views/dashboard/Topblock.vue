<template>
  <div class="container-fluid"> 
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ salesData?.annual_sale_item?.total_price ? formatNumber(parseFloat(salesData.annual_sale_item.total_price).toFixed(0)) : 0 }}</h3>  
            <p>Annual Total Sales</p>  
          </div>
          <div class="icon">
            <i class="material-icons">attach_money </i>
          </div>
          <a href="#" class="small-box-footer"
            >Sales info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{ salesData?.annual_sales?.total_grand_total ? formatNumber(parseFloat(salesData.annual_sales.total_grand_total).toFixed(0)) : 0 }}</h3>  
            <p>Annual Grand Total sales</p>
          </div>
          <div class="icon">
            <i class="material-icons">refresh</i>
          </div>
          <a href="#" class="small-box-footer"
            >Sales info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>{{ salesData?.annual_sale_item?.total_cost_price ? formatNumber(parseFloat(salesData.annual_sale_item.total_cost_price).toFixed(0)) : 0 }}</h3>  
            <p>Annual Total cost price</p>
          </div>
          <div class="icon">
            <i class="material-icons">monetization_on</i>
          </div>
          <a href="#" class="small-box-footer"
            >Redeem info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>{{ salesData?.annual_sale_item?.total_profit && salesData?.annual_sales?.total_order_discount ? formatNumber( (parseFloat(salesData.annual_sale_item.total_profit) - parseFloat(salesData.annual_sales.total_order_discount)).toFixed(0) ): 0 }}</h3>

            <p>Annual Total Profit</p>
          </div>
          <div class="icon">
            <i class="material-icons">attach_money</i>
          </div>
          <a href="#" class="small-box-footer"
            >Paid Amount info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div> 
  </div>
</template>
<script type="text/javascript">
import { mapGetters, mapActions } from "vuex";
import { ref, onMounted, getCurrentInstance } from "vue"; 
import axios from "axios"; 
export default {
  name: "Topblock",
  components: { 
  },
  props: {
    salesData: {
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
    };
  },

  created() { 
  },
  methods: {  
    formatNumber(number) {
      // Convert number to string
      const numStr = number.toString();
      
      // Match and format according to Indian numbering system
      const [integer, decimal] = numStr.split('.');
      const lastThreeDigits = integer.slice(-3);
      const otherDigits = integer.slice(0, -3);
      
      // Add commas to the remaining digits
      const formattedInteger = otherDigits.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThreeDigits;
      
      return decimal ? `${formattedInteger}.${decimal}` : formattedInteger;
    },
    indianNumbering(number) {
      // Convert number to string
      const numStr = number.toFixed(); // Keep two decimal places
      
      // Split the integer and decimal parts
      const [integer, decimal] = numStr.split('.');
      
      // Format the integer part according to the Indian numbering system
      const lastThreeDigits = integer.slice(-3);
      const otherDigits = integer.slice(0, -3);
      
      // Add commas to the remaining digits
      const formattedInteger = otherDigits.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThreeDigits;
      
      // Return the formatted number with the currency symbol
      return `₹${formattedInteger}.${decimal}`;
    }
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
