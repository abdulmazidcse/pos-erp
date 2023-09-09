<template>     
   <div class="navbar-custom">
      <ul class="list-unstyled topbar-menu float-end mb-0">
          <li class="dropdown notification-list d-lg-none">
              <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                  <i class="dripicons-search noti-icon"></i> 
              </a>
              <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                  <form class="p-3">
                      <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                  </form>
              </div>
          </li>
          <!-- <li class="dropdown notification-list topbar-dropdown">
              <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                  <img src="assets/images/flags/us.jpg" alt="user-image" class="me-0 me-sm-1" height="12"> 
                  <span class="align-middle d-none d-sm-inline-block">English</span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">
 
                  <a href="javascript:void(0);" class="dropdown-item notify-item">
                      <img src="assets/images/flags/germany.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle">English</span>
                  </a> 
              </div>
          </li> -->

          <!-- <li class="dropdown notification-list">
              <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                  <i class="dripicons-bell noti-icon"></i>
                  <span class="noti-icon-badge"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
 
                  <div class="dropdown-item noti-title px-3">
                      <h5 class="m-0">
                          <span class="float-end">
                              <a href="javascript: void(0);" class="text-dark">
                                  <small>Clear All</small>
                              </a>
                          </span>Notification
                      </h5>
                  </div>

                  <div class="px-3" style="max-height: 300px;" data-simplebar>

                      <h5 class="text-muted font-13 fw-normal mt-0">Today</h5> 
                      <a href="javascript:void(0);" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-2">
                          <div class="card-body">
                              <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>   
                              <div class="d-flex align-items-center">
                                  <div class="flex-shrink-0">
                                      <div class="notify-icon bg-primary">
                                          <i class="mdi mdi-comment-account-outline"></i>
                                      </div>
                                  </div>
                                  <div class="flex-grow-1 text-truncate ms-2">
                                      <h5 class="noti-item-title fw-semibold font-14">Datacorp <small class="fw-normal text-muted ms-1">1 min ago</small></h5>
                                      <small class="noti-item-subtitle text-muted">Caleb Flakelar commented on Admin</small>
                                  </div>
                                </div>
                          </div>
                      </a>     
                  </div> 
                  <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                      View All
                  </a>

              </div>
          </li> -->

          <!-- <li class="dropdown notification-list d-none d-sm-inline-block">
              <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                  <i class="dripicons-view-apps noti-icon"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">

                  <div class="p-2">
                      <div class="row g-0">
                          <div class="col">
                              <a class="dropdown-icon-item" href="#">
                                  <img src="assets/images/brands/slack.png" alt="slack">
                                  <span>Slack</span>
                              </a>
                          </div>
                          
                      </div>

                      <div class="row g-0">
                          <div class="col">
                              <a class="dropdown-icon-item" href="#">
                                  <img src="assets/images/brands/bitbucket.png" alt="bitbucket">
                                  <span>Bitbucket</span>
                              </a>
                          </div>
                          
                      </div>  
                  </div>

              </div>
          </li> -->

          <li class="dropdown notification-list">
              <a class="nav-link   nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                  aria-expanded="false">
                  <span class="account-user-avatar"> 
                      <img src="/assets/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle">
                  </span>
                  <span>
                      <span class="account-user-name">{{userData.user.name}}</span>
                      <span class="account-position"></span>
                  </span>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                  <!-- item-->
                  <div class=" dropdown-header noti-title">
                      <h6 class="text-overflow m-0">Welcome !</h6>
                  </div>

                  <!-- item-->
                  <a href="javascript:void(0);" class="dropdown-item notify-item">
                      <i class="mdi mdi-account-circle me-1"></i>
                      <span>My Account</span>
                  </a> 
                  <!-- item-->
                  <a href="javascript:void(0);"  @click="clearAllStorage()" class="dropdown-item notify-item">
                      <i class="mdi mdi-logout me-1"></i>
                      <span>Logout</span>
                  </a>
              </div>
          </li>
      </ul>
      <button class="button-menu-mobile open-left" id="sidebarCollapse" @click="handleResize">
          <i class="mdi mdi-menu"></i>
      </button>   
  </div>
</template>
<script>
import {mapGetters, mapActions} from "vuex"; 
export default {
  name: 'TopbarTemplate',
  props: {
    msg: String
  },
  data() {
    return {
      routes: this.$router.options.routes,
      activeClass:'', 
    };

  },
  methods:{  
    handleResize() {  
      var mask = document.getElementById('leftside-menu-container'); 
      var body = document.body; 
      var b = document.querySelector("body"); 
      if(b.hasAttribute('data-leftbar-compact-mode')){
        mask.style.overflowY = 'auto';
        b.removeAttribute('data-leftbar-compact-mode');
        document.getElementsByTagName('body')[0].classList.remove('compact-enable');
        document.getElementsByTagName('body')[0].classList.add('sidebar-enable'); 
      }else{ 
        mask.style.overflowY = 'visible';
        b.setAttribute("data-leftbar-compact-mode", "condensed");
        document.getElementsByTagName('body')[0].classList.add('compact-enable');
        document.getElementsByTagName('body')[0].classList.remove('sidebar-enable'); 
      } 
    },

    clearAllStorage: function(){       
      this.removeAllRoutes().then(
        this.removeAllProduct().then(
            this.removeAllRequitionProduct().then(
                this.logout()
            )
        )        
      ); 
    },
    ...mapActions(['logout','removeAllCartItems','removeAllProduct','removeAllRoutes', 'removeAllRequitionProduct']), 
  },
  computed: { 
    ...mapGetters([
      'userData', 
    ]), 
    computedClass() {
      if(this.currentRouteName =='/pos'){
        return 'active';
      }else{
        return '';
      }  
    },
    currentRouteName() {
        return this.$route.path;
    }, 
  },
  mounted() { 
    
  },
  created(){ 

  }
}
</script>
 