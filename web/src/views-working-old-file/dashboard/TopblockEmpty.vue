<template>
  <div class="container-fluid"> 
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>
                <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
                </div>
            </h3>  
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
            <h3><div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
            </div></h3>
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
            <h3><div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
            </div></h3> 
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
            <h3><div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
            </div></h3> 
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
export default {
  name: "TopblockEmpty",
  components: { 
  }, 
  data() {
    return {   
    };
  }, 
};
</script>

<style>
.product_stock_report .table-responsive {
  padding: 0 !important;
  overflow-x: visible !important;
}
</style>
