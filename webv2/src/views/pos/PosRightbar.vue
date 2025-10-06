<template> 
  
  <section class="section-products pos-rightbar scrollbar-width-thin" v-bind:style="{width: window.width + 'px', height: window.height + 'px'}">   
  <div class="row  ">   
        <div class="col-md-4 col-lg-3 col-xl-3 col-xl-2" v-for="productItem in productItems" :key="productItem.id">
            <ProductListItem :productItem="productItem"/>
        </div>  
  </div> 
  </section>
  
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import Product_List_Item from './Product_List_Item'; 
export default {
  name: "PosRightbar",
  data(){
    return {
      window: {
        width: 0,
        height: 0
      }, 
    }
  },
  methods: {
    handleResize() {
      this.window.width = window.innerWidth-630;
      this.window.height = window.innerHeight-80; 
    },
  }, 
  destroyed() {
    window.removeEventListener('resize', this.handleResize);
  },
  mounted(){
    window.scrollTo(0,0);  
  },
  components: {
    ProductListItem:Product_List_Item, 
  },
  computed: {
    ...mapGetters([
      'productItems',
      'cartItems',
      'cartQuantity', 
    ])
  },
  created() {
    window.addEventListener('resize', this.handleResize);
    this.handleResize();   
  }
};
</script>
<style type="text/css" scoped="">
  .pos-rightbar.container {
    padding: 0px;
    margin: 0px;
  }
  .container {
    padding-left: 0px;
  }
  .card{
    margin: 5px;
  }
  .row {
    margin-right: 0px;
    margin-left: 0px;
  }
  .tile.is-parent.col-md-4 {
    padding: 0px;
  }   
  .pos-rightbar{
    margin-top: 1px;
    border: 1px solid #e3e3e3;
    padding: 6px;  
    overflow-x: scroll;
    width: 795px;
    overflow-y: scroll;
  }      
</style>