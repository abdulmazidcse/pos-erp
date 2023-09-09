<template>
    <transition  >

    <section class="ftco-section">
        <div class="container">  
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="wrap">
                        <!-- <div class="img" style="background-image: url(images/bg-1.jpg);"></div> -->
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex">
                                <div class="w-100"> 
                                    <div class="d-flex">
                                         <img style="display:block; margin:0 auto;" src="../../assets/image/login-logo-pos.png" title="Sign In" width="250">
                                    </div>  
                                </div> 
                            </div>
                            <form @submit.prevent="submitForm()" enctype="multipart/form-data" class="signin-form">
                                <div class="form-group mt-3">
                                    <input type="text" class="form-control" required @keypress="onkeyPress('email')" v-model="form.email">
                                    <label class="form-control-placeholder" for="username">Username</label> 
                                    <div class="invalid-feedback" v-if="errors.email">
                                        {{errors.email[0]}}
                                    </div>
                                </div>
                                <div class="form-group">
                                  <input id="password-field" :type="paswwordFieldType" class="form-control" required  @keypress="onkeyPress('password')" v-model="form.password">
                                  <label class="form-control-placeholder" for="password">Password</label>
                                  <span @click="passwordView()" v-if="!checkPasswordView">
                                    <span toggle="#password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                                  </span>
                                  <span @click="passwordHidden()" v-else>
                                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                  </span>
                                  <div class="invalid-feedback" v-if="errors.password">
                                    {{errors.password[0]}}
                                </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-control btn btn-primary rounded submit px-3" :disabled="disabled">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span>
                                    Sign In</button>
                                </div>
                                <!-- <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                        <input type="checkbox" checked>
                                        <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <a href="#">Forgot Password</a>
                                    </div>
                                </div> -->
                            </form>
                            <!-- <p class="text-center">Not a member? <a data-toggle="tab" href="#signup">Sign Up</a></p> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </transition>
</template>

<script>
import {mapGetters, mapActions} from "vuex"; 
import { ref, onMounted } from "vue";
import Form from "vform";
import axios from "axios"; 
export default {
    name: "Login",
    components: { 
    },
    data() {
        return { 
            errors: {},
            btn:'Create',
            items: [],
            isSubmit: false,
            loading:false,
            disabled: false,
            paswwordFieldType: 'password',
            checkPasswordView: false,
            form: new Form({ 
                email: '',
                password: '', 
            }),

        }
    },
    created() { 
      if(this.$store.getters.token){
        window.location.href = "/" 
      }
    },

    methods: {
        toggleClass: function(e){
            alert('kk')
        },
        passwordView: function(e) {
            if(!this.checkPasswordView) {
              this.paswwordFieldType = "text";
              // document.querySelector(".toggle-password").classList.remove("fa-eye-slash");
              // document.querySelector(".toggle-password").classList.add("fa-eye");
              this.checkPasswordView = true;
            }
              setTimeout(()=> {
                this.paswwordFieldType = "password";
                // document.querySelector(".toggle-password").classList.remove("fa-eye");
                // document.querySelector(".toggle-password").classList.add("fa-eye-slash");
                this.checkPasswordView = false;
              }, 5000);
        },

        passwordHidden: function(e) {
            if(this.checkPasswordView) {
                this.paswwordFieldType = "password";
                // document.querySelector(".toggle-password").classList.remove("fa-eye");
                // document.querySelector(".toggle-password").classList.add("fa-eye-slash");
                this.checkPasswordView = false;
            }
        },

        submitForm: function(e) { 
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('email', this.form.email);
            formData.append('password', this.form.password);             
            var postEvent = axios.post(this.apiUrl+'/auth/login', formData, this.headers); 
            postEvent.then(res => {   
              
              // console.log('res.data.data.user.outlet',res.data.data.user.outlet_id);
              
              // console.log('res.data.data', res.data.data.user.roles[0]);
              // console.log('res.data.data', res.data.data.user);

                if((res.data.data.user.roles[0] == 7) || (res.data.data.user.roles[0] == 3)) {
                    if((!res.data.data.user.outlet_id) || (res.data.data.user.outlet_id == 0)){
                        this.$toast.error(`Outlet not assigned, Please contact administrator!`);
                        this.isSubmit = false; 
                        this.disabled = false;
                        return false;
                    }
                } 

                if(res.status == 200){ 
                    // res.data.data.user.roles[0]
                    // this.$toast.success(res.data.message);  
                    // this.$store.commit("UPDATE_USER",res.data.data);
                    // this.$store.commit("UPDATE_USER_TOKEN",res.data.data.access_token); 
                    if(res.data.data.access_token){  
                        const api_url = this.apiUrl;
                        const auth_headers = {
                            headers: {
                              'Authorization' : res.data.data.access_token ? `Bearer ${res.data.data.access_token}` : "",
                              'Content-Type': 'application/json' 
                            }
                        };

                        // For Dynamic User Role Permissions
                        axios.get(this.apiUrl+'/auth/user/menu-and-permissions', auth_headers)
                        .then(response => { 
                            // this.$store.commit('UPDATE_USER_ALL_PERMISSIONS', response.data.data.user_all_permissions);
                            this.$store.commit('UPDATE_USER_ALL_PERMISSIONS', response.data.data.module_based_navigation);
                            this.$store.commit('UPDATE_USER_ROUTES', response.data.data.user_routes);
                            this.$store.commit('UPDATE_USER_NAVIGATIONS', response.data.data.user_navigations);
                            this.$store.commit('UPDATE_USER_DATA_CHECK', response.data.data.data_check);

                            this.$store.commit("UPDATE_USER",res.data.data);
                            this.$store.commit("UPDATE_USER_TOKEN",res.data.data.access_token); 
                            this.$toast.success(res.data.message);
                            if((res.data.data.user.roles[0] == 7) || (res.data.data.user.roles[0] == 3)) {
                            // if((res.data.data.user.roles[0] == 3)) {
                                window.location.href = "/pos";
                            }else{
                                window.location.href = "/";
                            }
                            
                            this.reRenderRoute = 0;
                            this.isSubmit = false; 
                            this.disabled = false; 
                            //this.$router.push("/");
                        });

                        
                      //this.makeQuerablePromise(dispatch);
                      //console.log('dispatch',dispatch)
                                        
                      //this.$router.push("/");
                      //window.location.href = "/"; 
                      if(this.dataCheck) {
                        this.reRenderRoute = this.dataCheck;
                      }

                        // this.$router.push({ name: 'Dashboard' })
                        // window.location.href = "/" 
                    } 
                }else{
                    this.$toast.error(res.data.message);
                }
                if(res.status ==401){ 
                    this.$toast.error(res.data.error); 
                }  

            }).catch(err => {   
                this.isSubmit = false; 
                this.disabled = false;
                
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }else{
                  this.$toast.error(err.response.data.error);
                }
            }) 
        },
        makeQuerablePromise : function(promise) {
            // Don't modify any promise that has been already modified.
            if (promise.isFulfilled) return promise;

            // Set initial state
            var isPending = true;
            var isRejected = false;
            var isFulfilled = false;

            // Observe the promise, saving the fulfillment in a closure scope.
            var result = promise.then(
                function(v) {
                    isFulfilled = true;
                    isPending = false;
                    return v; 
                }, 
                function(e) {
                    isRejected = true;
                    isPending = false;
                    throw e; 
                }
            );

            result.isFulfilled = function() { return isFulfilled; };
            result.isPending = function() { return isPending; };
            result.isRejected = function() { return isRejected; };
            return result;
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        ...mapActions(['logout']),
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed:{
        ...mapGetters([
          'userData',
          'token',
          'dataCheck'
        ]), 
    } 
}
</script>

<style scoped>
.content-page {
  margin-left: 0px !important; 
  padding: 70px 12px 65px;  
}
 .img {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center; }

.wrap {
  width: 100%;
  overflow: hidden;
  background: #fff;
  border-radius: 5px;
  -webkit-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);
  -moz-box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24);
  box-shadow: 0px 10px 34px -15px rgba(0, 0, 0, 0.24); }
  .wrap .img {
    height: 200px; }

.login-wrap {
  position: relative; }
  .login-wrap h3 {
    font-weight: 300; }

.form-group {
  position: relative;
  z-index: 0;
  margin-bottom: 20px !important; }
  .form-group a {
    color: gray; }

.form-control {
  height: 48px;
  background: #fff;
  color: #000;
  font-size: 16px;
  border-radius: 5px;
  -webkit-box-shadow: none;
  box-shadow: none;
  border: 1px solid rgba(0, 0, 0, 0.1); }
  .form-control::-webkit-input-placeholder {
    /* Chrome/Opera/Safari */
    color: rgba(0, 0, 0, 0.2) !important; }
  .form-control::-moz-placeholder {
    /* Firefox 19+ */
    color: rgba(0, 0, 0, 0.2) !important; }
  .form-control:-ms-input-placeholder {
    /* IE 10+ */
    color: rgba(0, 0, 0, 0.2) !important; }
  .form-control:-moz-placeholder {
    /* Firefox 18- */
    color: rgba(0, 0, 0, 0.2) !important; }
  .form-control:focus, .form-control:active {
    outline: none !important;
    -webkit-box-shadow: none;
    box-shadow: none;
    border: 1px solid #01d28e; }

.field-icon {
  position: absolute;
  top: 50%;
  right: 15px;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  color: rgba(0, 0, 0, 0.3); }

.form-control-placeholder {
  position: absolute;
  top: 3px;
  padding: 7px 0 0 15px;
  -webkit-transition: all 400ms;
  -o-transition: all 400ms;
  transition: all 400ms;
  opacity: .6; 
  left: 7px;}

.form-control:focus + .form-control-placeholder,
.form-control:valid + .form-control-placeholder {
  -webkit-transform: translate3d(0, -120%, 0);
  transform: translate3d(0, -120%, 0);
  padding: 7px 0 0 0;
  opacity: 1;
  text-transform: uppercase;
  font-size: 12px;
  letter-spacing: 1px;
  color: #01d28e;
  font-weight: 700; }

.social-media {
  position: relative;
  width: 100%; }
  .social-media .social-icon {
    display: block;
    width: 40px;
    height: 40px;
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.05);
    font-size: 16px;
    margin-right: 5px;
    border-radius: 50%; }
    .social-media .social-icon span {
      color: #999999; }
    .social-media .social-icon:hover, .social-media .social-icon:focus {
      background: #01d28e; }
      .social-media .social-icon:hover span, .social-media .social-icon:focus span {
        color: #fff; }

.checkbox-wrap {
  display: block;
  position: relative;
  padding-left: 30px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none; } 
.checkbox-wrap input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0; }
 
.checkmark {
  position: absolute;
  top: 0;
  left: 0; }
 
.checkmark:after {
  content: "\f0c8";
  font-family: "FontAwesome";
  position: absolute;
  color: rgba(0, 0, 0, 0.1);
  font-size: 20px;
  margin-top: -4px;
  -webkit-transition: 0.3s;
  -o-transition: 0.3s;
  transition: 0.3s; }
  @media (prefers-reduced-motion: reduce) {
    .checkmark:after {
      -webkit-transition: none;
      -o-transition: none;
      transition: none; } }
 
.checkbox-wrap input:checked ~ .checkmark:after {
  display: block;
  content: "\f14a";
  font-family: "FontAwesome";
  color: rgba(0, 0, 0, 0.2); }
 
.checkbox-primary {
  color: #01d28e; }
  .checkbox-primary input:checked ~ .checkmark:after {
    color: #01d28e; }
.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
    text-align: left;
}
.btn {
  cursor: pointer;
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
  font-size: 15px;
  padding: 10px 20px; }
  .btn:hover, .btn:active, .btn:focus {
    outline: none; }
  .btn.btn-primary {
    background: #01d28e !important;
    border: 1px solid #01d28e !important;
    color: #fff !important; 
  }
.btn.btn-primary:hover {
  border: 1px solid #01d28e;
  background: transparent;
  color: #01d28e; 
}
.btn.btn-primary.btn-outline-primary {
  border: 1px solid #01d28e;
  background: transparent;
  color: #01d28e; 
}
.btn.btn-primary.btn-outline-primary:hover {
  border: 1px solid transparent;
  background: #01d28e;
  color: #fff; 
}

.toggle-password {
  cursor: pointer;
}
</style>