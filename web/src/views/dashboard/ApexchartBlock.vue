<template>
  <div class="container-fluid"> 
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="small-box  ">
          <div class="inner">
            <apexchart
              type="polarArea"
              height="380"
              :options="chartOptionsMono"
              :series="seriesMono"
            ></apexchart>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6"> 
        <div class="small-box bg-maron">
          <div class="inner">
            <apexchart type="radialBar" height="380" :options="chartOptionsRadia" :series="seriesRadia"></apexchart>
            </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="small-box  ">
          <div class="inner"> 
            <apexchart
              type="line"
              height="380"
              :options="lineChartOptions"
              :series="lineChartSeries"
            ></apexchart>
          </div>
        </div>
      </div> 
    </div>
  </div>
</template>
<script type="text/javascript"> 
export default {
  name: "ApexchartBlock",
  components: { 
  },
  props: {
    salesData: {
      type: Object,
      required: true,
      default: () => [],
    }, 
    salesVsPurchase: {
      type: Object,
      required: true,
      default: () =>[]
    },
    topProducts: {
      type: Array,
      required: true,
      default: () => [],
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
      seriesMono: [42, 47, 52, 58, 65],
      chartOptionsMono: {
          chart: {
            width: 380,
            type: "polarArea",
          },
          labels: [],
          fill: { 
            colors: ["#28a745","#D3A009", "#17a2b8", "#5A1F25", "#1A6767"], // Set your desired background colors
            opacity: 1,
          },
          stroke: {
          width: 0, 
          },
          yaxis: {
          show: false,
          },
          legend: {
          position: "bottom",
          },
          plotOptions: {
          polarArea: {
              rings: {
              strokeWidth: 0,
              },
              spokes: {
              strokeWidth: 0,
              },
          },
          },
          theme: {
          monochrome: {
              enabled: true,
              shadeTo: "light",
              shadeIntensity: 0.6,
          },
          },
      },

      seriesRadia: [65, 67, 61, 90,30],
      chartOptionsRadia: { 
          chart: {
              height: 390,
              type: 'radialBar',
          },
          plotOptions: {
              radialBar: {
              offsetY: 0,
              startAngle: 0,
              endAngle: 270,
              hollow: {
                  margin: 5,
                  size: '30%',
                  background: 'transparent',
                  image: undefined,
              },
              dataLabels: {
                  name: {
                  show: false,
                  },
                  value: {
                  show: false,
                  }
              }
              }
          },
          colors: ['#0084ff', '#39539E', '#0077B5','#2a2a2a'],
          labels: ['Sales', 'Discount', 'Collection', 'Dues'],
          legend: {
              show: true,
              floating: true,
              fontSize: '16px',
              position: 'right',
              offsetX: '90%',
              offsetY: 15,
              labels: {
                  useSeriesColors: true,
              },
              markers: {
                  size: 0
              },
              formatter: function(seriesName, opts) {
                  return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
              },
              itemMargin: {
              vertical: 3
              }
          },
          responsive: [{
              breakpoint: 480,
              options: {
              legend: {
                  show: false
              }
              }
          }]
      }, 

      lineChartOptions: {
        chart: {
          type: 'line',
        },
        xaxis: {
          categories: this.salesVsPurchase.months, //JSON.parse(this.salesData).months,
        },
        title: {
          text: 'Month-wise Sales Report',
        },
      },
      lineChartSeries: [
        {
          name: 'Sales Data',
          data: this.salesVsPurchase.sales, // JSON.parse(this.salesData).sales,
        },
        {
          name: 'Purchases Data',
          data: this.salesVsPurchase.purchase, // JSON.parse(this.salesData).sales,
        },
      ],
      topProductsStringified: "",
    };
  },
  watch: {
    topProducts: {
      handler() {
        this.mapDataForApex();
        this.stringifyTopProducts();
      },
      deep: true,
    },
    salesData: {
      handler(nv) { 
        this.seriesRadia = [
            (nv?.annual_sale_item?.total_cost_price ? parseFloat(nv.annual_sale_item.total_cost_price).toFixed(0) : 0),
            (nv?.annual_sale_item?.total_discount && nv?.annual_sales?.total_order_discount
                ? (parseFloat(nv.annual_sale_item.total_discount) + parseFloat(nv.annual_sales.total_order_discount)).toFixed(0)
                : 0),
            (nv?.annual_collection?.total_amount ? parseFloat(nv.annual_collection.total_amount).toFixed(0) : 0),
            (nv?.annual_sale_item?.total_cost_price ? parseFloat(nv.annual_sale_item.total_cost_price).toFixed(0) : 0),
        ] || [];
      },
      immediate: true, // To trigger the watcher on component creation
    },
  }, 

  created() { 
  },
  methods: {  
    mapDataForApex() {
      const productsArray = this.topProducts.topProducts || [];
      const series = productsArray.map((product) => product.sales_items_count);
      const labels = productsArray.map((product) => product.product_name);

      this.seriesMono = series;
      this.chartOptionsMono.labels = labels;
    },
    stringifyTopProducts() {
      this.topProductsStringified = JSON.stringify(this.topProducts, null, 2);
    },
  },
  mounted() {
    this.mapDataForApex();
    this.stringifyTopProducts();
  },
  
};
</script>

<style>
.product_stock_report .table-responsive {
  padding: 0 !important;
  overflow-x: visible !important;
}
</style>
