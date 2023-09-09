<template>
	<div id="mySidenav" class="sidenav">
      <a href="javascript:void(0)" class="closebtn" @click="closeNav()">&times;</a>
      <div class="category-list">
        <ul style="list-style-type:none;line-height: 11px; padding:0px">
          <li><a href="#" @click="openNav2()" >Cooking Essentials</a></li>
          <li><a href="#" @click="openNav2()" >Groceries</a></li>
          <li><a href="#" @click="openNav2()" >Sauces & Pickles</a></li>
          <li><a href="#" @click="openNav2()" >Snacks</a></li>
          <li><a href="#" @click="openNav2()" >Frozen Food</a></li>
          <li><a href="#" @click="openNav2()" >Spreads</a></li>
          <li><a href="#" @click="openNav2()" >Beverages</a></li>
          <li><a href="#" @click="openNav2()" >Baby Food & Care</a></li>
          <li><a href="#" @click="openNav2()" >Home Care & Cleaning</a></li>
          <li><a href="#" @click="openNav2()" >Personal Care</a></li>
          <li><a href="#" @click="openNav2()" >Stationery</a></li>
          <li><a href="#" @click="openNav2()" >Pet Care</a></li>
        </ul>
      </div> 
    </div>
</template>

<script>    
export default {
  name: 'Pos',
  props: {
    msg: String
  }, 
  methods: { 
    openNav: (item) => {
      document.getElementById("mySidenav").style.width = "250px";
    },
    closeNav: (item) => {
      document.getElementById("mySidenav").style.width = "0";
    },
    openNav2: (item) => {
      document.getElementById("mySidenav2").style.width = "300px";
    },
    closeNav2: (item) => {
      document.getElementById("mySidenav2").style.width = "0";
    }, 
    getCategory: () =>{
      axios.get(`/api/products`).then((response) => {
        commit('UPDATE_PRODUCT_ITEMS', response.data)
      });
    }
  },
  mounted() {
    // console.log('data-from-state ',this.$store.state.count)
  },
  computed: {
     
  },
  created(){
  //this.toggleBodyClass('active')
  //console.log('dddddddddddd', APISettings.baseURL );
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.category-list ul li a {
    display: ruby-text-container;   
    color: #fff;
    text-align: center;
    box-shadow: 0 2px 20px rgb(50 50 50 / 10%);
    transition: color 0.2s;
    font-size: 15px;
} 
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 9;
  top: 0;
  right: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.8s;
  padding-top: 60px;
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>