<template> 
  <div class="leftside-menu " >    
      <!-- LOGO -->
      <a href="/" class="logo text-center logo-light">
          <span class="logo-lg">
              <img src="/assets/images/logo.png" alt="" height="80" width="230">
              <!-- <span>স্বাস্থ্যকর প্রতিদিন</span> -->
          </span>
          <span class="logo-sm">
              <img src="/assets/images/logo_sm.png" alt="" height="16">
          </span>
      </a>

      <!-- LOGO -->
      <!-- <a href="/" class="logo text-center logo-dark">
          <span class="logo-lg">
            <img src="assets/images/logo-dark.png" alt="" height="16">
          </span>
          <span class="logo-sm">
              <img src="assets/images/logo_sm_dark.png" alt="" height="16">
          </span>
      </a> -->

      <div class="h-100 scrollbar-width-thin" style="overflow-y: auto;" id="leftside-menu-container" data-simplebar> 
          <!--- Sidemenu --> 
          <ul class="side-nav crollbar-width-thin">  
              <li class="side-nav-item" v-for="route in routes" :key="route.name">
                
                  <a data-bs-toggle="collapse" :href="'#' + route.name" aria-expanded="false"  class="side-nav-link" v-if="route.children.length > 0" @click="checkParrentNav(route.name)">
                      <i v-if="route.icon_name" v-bind:class="route.icon_name"></i> 
                      <span>&nbsp;&nbsp;{{route.name}}</span>
                      <span class="menu-arrow"></span>
                  </a>
                  
                  <router-link :to="route.path" class="side-nav-link" v-else>
                    <i v-if="route.icon_name" v-bind:class="route.icon_name"></i>
                    <span> &nbsp;&nbsp;{{route.name}}  </span>
                  </router-link> 

                  <div class="collapse" :id="route.name">
                      <ul class="side-nav-second-level">
                          <li v-for="children in route.children" :key="children.name">
                            <router-link :to="children.path" >
                              <span class="fa fa-user mr-1"></span> &nbsp;{{children.name}}
                            </router-link> 
                          </li> 
                      </ul>
                  </div>                  
                  
              </li>  
          </ul> 

          <div class="clearfix"></div>

      </div>
      <!-- Sidebar -left -->
  </div>   
</template>
<script>
// import {mapGetters} from "vuex"
// import { defineComponent } from 'vue'
// import { FadeInOut } from 'vue3-transitions'
// import About from "@/views/page/RolePermission.vue";  
export default {
  name: 'NavigationTemplate',
  props: {
    msg: String
  },
  // components: { FadeInOut },
  data() {
    return {
      routes: [],
      activeClass:''
    };

  },
  methods:{  
    checkParrentNav(name){
      // console.log('checkParrentNav', name)
      // console.log('UserMenuAndPermissions', this.$store.getters.userAllPermissions[name][0].children)
    }
  },
  computed: {
    computedClass() {
      if(this.currentRouteName =='/pos'){
        return 'active';
      }else{
        return '';
      }  
    },
    currentRouteName() {
        return this.$route.path;
    }
  },
  mounted() { 
     
  },
  created(){   
    //this.$router.removeRoute('Outlet')
    //console.log(this.$router.options.navigations);
    this.routes = this.$router.options.navigations
  }
}

</script>
 
<style>
  .logo {
    line-heght: 1.5 !important;
  }
</style>