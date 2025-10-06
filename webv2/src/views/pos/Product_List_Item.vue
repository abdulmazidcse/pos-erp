<template> 
      <div id="product-2" class="single-product">
        <div class="part-1">
            <span class="discount">15% off </span>
            <ul>
                <li><a @click="addCartItem(productItem)"><i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="#"><i class="fas fa-heart"></i></a></li>
                <li><a href="#"><i class="fas fa-plus"></i></a></li>
                <li><a href="#"><i class="fas fa-expand"></i></a></li>
            </ul>
        </div>
        <div class="part-2">
            <h3 class="product-title">{{ productItem.product_name.substring(0,20,'...') }} <span v-if="productItem.product_name.length > 30">...</span> </h3>
            <h4 class="product-price">$ {{ productItem.mrp_price }}</h4>
        </div>
      </div> 
</template>
<script>
import {mapActions} from 'vuex'
export default {
  name: "ProductListItem",
  props: ["productItem"],
  methods: {
    ...mapActions(["addCartItem"]),
  },
  created(){ 
  }
};
</script>
<style type="text/css" scoped="">  
.section-products .single-product h3 {
  line-height: 1; 
}
.section-products .single-product h4 {
    margin-bottom: 0.2rem;
    font-size: .8rem;
}
.section-products .single-product {
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;
    margin: 1px -17px 10px 0px;
}
.section-products .single-product .part-1 {
    position: relative;
    height: 150px;
    max-height: 150px;
    margin-bottom: 5px;
    overflow: hidden;  
}

.section-products .single-product .part-1::before {
    position: absolute;
    content: "";
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    transition: all 0.3s;
}

.section-products .single-product:hover .part-1::before {
    transform: scale(1.2,1.2) rotate(5deg);
} 

.section-products #product-2 .part-1::before {
    background: url("../../assets/image/no-image.jpg") no-repeat center;
    background-size: cover;
} 
.section-products .single-product .part-1 .discount,
.section-products .single-product .part-1 .new {
    position: absolute;
    top: 7px;
    right: 0px;
    color: #ffffff;
    background-color: #fe302f;
    padding: 1px 4px;
    text-transform: uppercase;
    font-size: 0.85rem;
    border-radius: 13px 0px 0px 13px;
}

.section-products .single-product .part-1 .new {
    left: 0;
    background-color: #444444;
}

.section-products .single-product .part-1 ul {
    position: absolute;
    bottom: 1px;
    left: 34px;
    margin: 0;
    padding: 0;
    list-style: none;
    opacity: 0;
    transition: bottom 0.5s, opacity 0.5s;
}

.section-products .single-product:hover .part-1 ul {
    bottom: 10px;
    opacity: 1;
}

.section-products .single-product .part-1 ul li {
    display: inline-block;
    margin-right: 4px;
}

.section-products .single-product .part-1 ul li a {
    display: inline-block;
    width: 26px;
    height: 29px;
    line-height: 33px;
    background-color: #ffffff;
    color: #444444;
    text-align: center;
    box-shadow: 0 2px 20px rgb(50 50 50 / 10%);
    transition: color 0.2s;
}

.section-products .single-product .part-1 ul li a:hover {
    color: #fe302f;
}

.section-products .single-product .part-2 .product-title {
    font-size: .9rem;
}
 
</style>
