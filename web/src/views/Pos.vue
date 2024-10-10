<template> 
  <transition>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-12">
    			<PosLeftbar/> 
    		</div> 
    	</div>
    </div>  
  </transition>
</template>
<script>  
import axios from "axios";  
import PosLeftbar from './pos/PosLeftbar'
import PosRightbar from './pos/PosRightbar'
import DrawerCategory from './drawer/Category'
export default {
  name: 'Pos',
  props: {
    msg: String
  },
  components: { 
  PosLeftbar,
  PosRightbar,
  DrawerCategory
  },
  methods: { 
    openNav: (item) => {
      document.getElementById("mySidenav").style.width = "250px";
    } 
  },
  mounted() {
    //console.log('data-from-state ',this.$store.state.count)
  },
  computed: {
     
  },
  created(){
    axios.get(this.apiUrl+'/posproducts', this.headers).then(res => {
      this.$store.commit("UPDATE_PRODUCT_ITEMS",res.data.data);
    })
  }
}
</script>
 
<style scoped>
 
.drawer-button{
  font-size:30px;
  cursor:pointer; 
  right: 0;
  margin-right: -49px;
  z-index: 1;
  position: absolute; 
  color: #ffffff;
  background-color: #fe302f;
  padding: 1px 4px; 
  font-size: 1rem;
  border-radius: 13px 0px 0px 13px;
  margin-top:30%
}
 
</style>

<style src="@vueform/multiselect/themes/default.css"></style>