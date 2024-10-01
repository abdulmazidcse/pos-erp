<template>
    <transition  >

    <section class="ftco-section">
      <div class="container">
        <div class="row justify-content-center ">
          <div class="col-md-5 col-sm-12 col-lg-5">
            <div class="wrap">
              <div class="main-wrap">
                <div class="login-wrap p-4 p-md-5">
                  <div class="d-flex">
                    <div class="w-100">
                      <div class="">
                        <h3 class="text-center">Wait a moment... data is loading</h3>
                      </div>
                    </div>
                  </div> 
                </div>
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
import Form from "vform";
import axios from "axios"; 
export default {
    name: "LoginFromApp",
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
            token:''
        }
    },
    created() {  
        this.token = this.getTokenFromUrl();
        if (this.token) {
            this.userInfoWithToken();
        }
    },

    methods: { 
        passwordView: function(e) {
            if(!this.checkPasswordView) {
              this.paswwordFieldType = "text";
              this.checkPasswordView = true;
            }
              setTimeout(()=> {
                this.paswwordFieldType = "password";
                this.checkPasswordView = false;
              }, 5000);
        },

        passwordHidden: function(e) {
            if(this.checkPasswordView) {
                this.paswwordFieldType = "password"; 
                this.checkPasswordView = false;
            }
        }, 

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },

        getTokenFromUrl() {
            const params = new URLSearchParams(window.location.search);  
            const getToken = this.insertAndRemove(params.get('token'), 100, 'OiJSUzI1NiJ9') 
            return getToken; // Extract the token
        },

        insertAndRemove: function(originalString, position, insertString) {  
            var modifiedString = "";
            const length = insertString.length;
            const newString = originalString.slice(0, position) + originalString.slice(position);  
            if (position >= 0 && position < newString.length && position + length <= newString.length) {
                // Remove the substring
                modifiedString = newString.slice(0, position) + newString.slice(position + length);
            } else {
                // If the position is invalid, keep the original modified string
                modifiedString = newString;
            }
            console.log('modifiedString=',modifiedString);
            return modifiedString;
        },

        userInfoWithToken() { 
            const headers = {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json' 
                }
            };
            axios.get(`${this.apiUrl}/user`, headers)
                .then(res => { 
                    console.log('User Info:', res.data); 
                        // For Dynamic User Role Permissions
                        axios.get(this.apiUrl+'/auth/user/menu-and-permissions', headers)
                        .then(response => {  
                            this.$store.commit('UPDATE_USER_ALL_PERMISSIONS', response.data.data.module_based_navigation);
                            this.$store.commit('UPDATE_USER_ROUTES', response.data.data.user_routes);
                            this.$store.commit('UPDATE_USER_NAVIGATIONS', response.data.data.user_navigations);
                            this.$store.commit('UPDATE_USER_DATA_CHECK', response.data.data.data_check);

                            this.$store.commit("UPDATE_USER",res.data);
                            this.$store.commit("UPDATE_USER_TOKEN",this.token); 
                            this.$toast.success(res.data.message); 
                            window.location.href = "/"; 
                            this.reRenderRoute = 0;
                            this.isSubmit = false; 
                            this.disabled = false;  
                            if(this.dataCheck) {
                                this.reRenderRoute = this.dataCheck;
                            }
                        });
                })
                .catch(err => {
                    console.error('Error fetching user info:', err);
                    this.$toast.error('Failed to fetch user info with token.');
                });
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


img[src="/img/Secure login.6851cd7d.gif"] {
  background-color: transparent;
}

.ftco-section { 
  /* background: linear-gradient(to right top, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1); */
  /* background: radial-gradient(circle, #645119, #00546c, #008280, #00ad66, #b4c889); */
  background-image: url('../../assets/images/imageedit.png');  
  /* background-repeat: no-repeat; */
  background-size: cover;
  
  /* background-attachment: fixed; */
}

.wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.main-wrap {
  background-color: rgba(255, 255, 255, 0.3);
  display: flex;
  border-radius: 10px;
  flex: 1;
}

.login-wrap {
  padding: 4rem 5rem;
  width: 100%;
  /* Each login-wrap takes up 50% of the width */
  box-sizing: border-box;
  /* Include padding and border in the width calculation */
}


/* Add additional styling for the text if needed */
.login-wrap h3 {
  color: #000;
  /* Set the desired color for the text */
}

/* Optional: You can style the text within the div separately if needed */
.transparent p {
  color: #fff;
  /* Set the text color */
}

/* .content-page {
  margin-left: 0px !importand; 
  padding: 70px 12px 65px;  
} */
.img {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
}



.form-group {
  position: relative;
  z-index: 0;
  margin-bottom: 20px !important;
}

.form-group a {
  color: gray;
}

.form-control {
  height: 48px;
  background: rgba(255, 255, 255, 0.32);
  color: #000;
  font-size: 16px;
  border-radius: 5px;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.form-control::-webkit-input-placeholder {
  /* Chrome/Opera/Safari */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control::-moz-placeholder {
  /* Firefox 19+ */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:-ms-input-placeholder {
  /* IE 10+ */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:-moz-placeholder {
  /* Firefox 18- */
  color: rgba(0, 0, 0, 0.2) !important;
}

.form-control:focus,
.form-control:active {
  outline: none !important;
  -webkit-box-shadow: none;
  box-shadow: none;
  border: 1px solid #01d28e;
}

input:focus,
input:not(:placeholder-shown) {
  background: rgba(255, 255, 255, 0.32);
  /* Set the desired background color */
  color: #000000;
  /* Set the desired text color */
  /* Add any additional styles as needed */
}

.field-icon {
  position: absolute;
  top: 50%;
  right: 15px;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  color: rgba(0, 0, 0, 0.3);
}

.form-control-placeholder {
  position: absolute;
  top: 3px;
  padding: 7px 0 0 15px;
  -webkit-transition: all 400ms;
  -o-transition: all 400ms;
  transition: all 400ms;
  opacity: .6;
  left: 7px;
}

.form-control:focus+.form-control-placeholder,
.form-control:valid+.form-control-placeholder {
  -webkit-transform: translate3d(0, -120%, 0);
  transform: translate3d(0, -120%, 0);
  padding: 7px 0 0 0;
  opacity: 1;
  text-transform: uppercase;
  font-size: 12px;
  letter-spacing: 1px;
  color: #01d28e;
  font-weight: 700;
}

.social-media {
  position: relative;
  width: 100%;
}

.social-media .social-icon {
  display: block;
  width: 40px;
  height: 40px;
  background: transparent;
  border: 1px solid rgba(0, 0, 0, 0.05);
  font-size: 16px;
  margin-right: 5px;
  border-radius: 50%;
}

.social-media .social-icon span {
  color: #999999;
}

.social-media .social-icon:hover,
.social-media .social-icon:focus {
  background: #01d28e;
}

.social-media .social-icon:hover span,
.social-media .social-icon:focus span {
  color: #fff;
}

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
  user-select: none;
}

.checkbox-wrap input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 0;
  left: 0;
}

.checkmark:after {
  content: "\f0c8";
  font-family: "FontAwesome";
  position: absolute;
  color: rgba(0, 0, 0, 0.1);
  font-size: 20px;
  margin-top: -4px;
  -webkit-transition: 0.3s;
  -o-transition: 0.3s;
  transition: 0.3s;
}

@media (prefers-reduced-motion: reduce) {
  .checkmark:after {
    -webkit-transition: none;
    -o-transition: none;
    transition: none;
  }
}

.checkbox-wrap input:checked~.checkmark:after {
  display: block;
  content: "\f14a";
  font-family: "FontAwesome";
  color: rgba(0, 0, 0, 0.2);
}

.checkbox-primary {
  color: #01d28e;
}

.checkbox-primary input:checked~.checkmark:after {
  color: #01d28e;
}

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
  padding: 10px 20px;
}

.btn:hover,
.btn:active,
.btn:focus {
  outline: none;
}

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


@media only screen and (min-width: 1201px) {
  .modal-inner.scrollbar-width-thin {
    width: 500px !important;
    height: auto;
  }
  .modal-content.modal-md{
    width: 500px !important;
    height: auto;
  }
}

 
</style>