<template>
  <div>
    <div class="wrapper"> 
      <NavigationTemplate v-if="token" :key="reRenderRoute" />  
      <div v-bind:class="[token ? 'content-page': '' ]">
        <div class="content">  
          <notifications
            group="foo-css" 
            :speed="500"
            :width="280"
            :height="30"
            animation-name="v-fade-left"
            position="top right"
          /> 
          <notifications
            group="foo-info" 
            :speed="500"
            :width="280"
            :height="30"
            animation-name="v-fade-left"
            position="top left"
          /> 
          <!-- Custom style example -->
          <notifications
            group="custom-style"
            position="top center"
            classes="n-light"
            :max="3"
            :width="400"
          /> 
          <!-- Custom template example -->
          <notifications
            group="custom-template"
            :duration="5000"
            :width="500"
            animation-name="v-fade-left"
            position="top left"
          > 
          </notifications> 
          <!-- Full width example -->
          <notifications
            group="full-width"
            width="100%"
          /> 
          <router-view v-slot="{ Component }" :key="$route.path">
            <topbarTemplate v-if="token" />
            <FadeInOut entry="left" exit="right" :duration="300" mode="out-in">
              <component :is="Component" />
            </FadeInOut>
          </router-view>
        </div>
      </div>
    </div>
    <!-- Right Sidebar -->
    <div class="end-bar" style="display: none;">

        <div class="rightbar-title">
            <a href="javascript:void(0);" class="end-bar-toggle float-end">
                <i class="dripicons-cross noti-icon"></i>
            </a>
            <h5 class="m-0">Settings</h5>
        </div>

        <div class="rightbar-content h-100" data-simplebar>

            <div class="p-3">
                <div class="alert alert-warning" role="alert">
                    <strong>Customize </strong> the overall color scheme, sidebar menu, etc.
                </div>

                <!-- Settings -->
                <h5 class="mt-3">Color Scheme</h5>
                <hr class="mt-1" />

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="color-scheme-mode" value="light" id="light-mode-check" checked>
                    <label class="form-check-label" for="light-mode-check">Light Mode</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="color-scheme-mode" value="dark" id="dark-mode-check">
                    <label class="form-check-label" for="dark-mode-check">Dark Mode</label>
                </div>
   

                <!-- Width -->
                <h5 class="mt-4">Width</h5>
                <hr class="mt-1" />
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="width" value="fluid" id="fluid-check" checked>
                    <label class="form-check-label" for="fluid-check">Fluid</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="width" value="boxed" id="boxed-check">
                    <label class="form-check-label" for="boxed-check">Boxed</label>
                </div>
    

                <!-- Left Sidebar-->
                <h5 class="mt-4">Left Sidebar</h5>
                <hr class="mt-1" />
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="theme" value="default" id="default-check">
                    <label class="form-check-label" for="default-check">Default</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="theme" value="light" id="light-check" checked>
                    <label class="form-check-label" for="light-check">Light</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="theme" value="dark" id="dark-check">
                    <label class="form-check-label" for="dark-check">Dark</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="compact" value="fixed" id="fixed-check" checked>
                    <label class="form-check-label" for="fixed-check">Fixed</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="compact" value="condensed" id="condensed-check">
                    <label class="form-check-label" for="condensed-check">Condensed</label>
                </div>

                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="compact" value="scrollable" id="scrollable-check">
                    <label class="form-check-label" for="scrollable-check">Scrollable</label>
                </div>

                <div class="d-grid mt-4">
                    <button class="btn btn-primary" id="resetBtn">Reset to Default</button>
        
                    <a href="https://themes.getbootstrap.com/product/hyper-responsive-admin-dashboard-template/"
                        class="btn btn-danger mt-3" target="_blank"><i class="mdi mdi-basket me-1"></i> Purchase Now</a>
                </div>
            </div> <!-- end padding-->

        </div>
    </div>  
  </div>
</template>

<script> 
import NavigationTemplate from './NavigationTemplate.vue'
import topbarTemplate from './TopbarTemplate.vue'
import {mapGetters, mapActions} from "vuex"; 
import {ref, onMounted, getCurrentInstance } from "vue";
export default {
  name: 'MasterTemplate',
  props: {
    msg: String
  },
  data() {
    return {
      isLogin:false,
      user:[]
    }
  },
  components: {
    NavigationTemplate,
    topbarTemplate
  },
  computed:{
    currentRouteName() {
      return this.$route.name;
    },
    // currentRoute() {
    //   if(this.$route.meta.parent_module){
    //     console.log('has-parrent',this.$route.meta.parent_module)
    //   }else{
    //     console.log('no-parrent',this.$route.meta.module_name)
    //   }
    //   return this.$route;
    // },
      ...mapGetters([
        'userData',
        'token',
      ]), 
  },
  created() {
    console.log("re-render-route", this.reRenderRoute);
    const api_url = this.apiUrl;
    const auth_headers = {
      headers: {
        'Authorization' : this.token ? `Bearer ${this.token}` : "",
        'Content-Type': 'application/json' 
      }
    };
    if(this.token) {
      const app = getCurrentInstance();
      app.appContext.config.globalProperties.headers = {headers: {
        'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
        'Content-Type': 'multipart/form-data' 
      }};

      app.appContext.config.globalProperties.headerjson = {headers: {
        'Authorization' : this.$store.getters.token ? `Bearer ${this.$store.getters.token}` : "",
        'Content-Type': 'application/json' 
      }};
      this.$store.dispatch('getUserMenuAndPermissions', {api_url, auth_headers});
    }
  },
  
}
</script>