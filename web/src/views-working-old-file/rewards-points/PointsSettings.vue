<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Points and Rewards </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Points and Rewards Settings</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="wpr_table">
                                <div class="general_wrapper">
                                    <div class="general_row_wrap">
                                        <div class="wpr_general_sign_title"> Enable </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label>Enable</label> 
                                            </div>
                                            <div class="wpr_general_content">
                                                <div class="form-check form-checkbox-success mb-2">
                                                    <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="enable_points_rewards" v-model="form.enable_points_rewards">
                                                    <label class="form-check-label" for="enable_points_rewards">Enable Points and Rewards</label>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="general_row_wrap">
                                        <div class="wpr_general_sign_title">
                                            Signup </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="enable_signup_points">Enable Signup Points</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <div class="form-check form-checkbox-success mb-2">
                                                    <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.enable_signup_points" id="enable_signup_points" checked>
                                                    <label class="form-check-label" for="enable_signup_points">Enable Signup Points for Rewards</label>
                                                </div> 
                                            </div> 
                                        </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="signup_points">Enter Signup Points</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <label for="wpr_thankyouorder_minimum">
                                                    <input type="text" class="form-control" id="signup_points" v-model="form.signup_points"  placeholder="Enter Signup Points"  >
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="general_row_wrap">
                                        <div class="wpr_general_sign_title">
                                            Referral </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="enable_referral_points">Enable Referral Points</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <div class="form-check form-checkbox-success mb-2">
                                                    <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.enable_referral_points" id="enable_referral_points" checked>
                                                    <label class="form-check-label" for="enable_referral_points">Enable Referral Points for Rewards</label>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="referral_points">Enter Referral Points</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <label for="referral_points">
                                                    <input type="text" class="form-control" id="referral_points" v-model="form.referral_points"  placeholder="Enter Referral Points" > 
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="general_row_wrap">
                                        <div class="wpr_general_sign_title">
                                            Social Sharing </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="enable_social_point">Enable Social Links</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <div class="form-check form-checkbox-success mb-2">
                                                    <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.enable_social_point" id="enable_social_point" checked>
                                                    <label class="form-check-label" for="enable_social_point">Enable Social Media Sharing.</label>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label>Select Social Links</label>
                                            </div>
                                            <div class="wpr_general_content"> 
                                                <div class="form-check form-checkbox-success "> 
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label input-text" for="social_share_facebook">
                                                        <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.social_share_facebook" id="social_share_facebook"> Facebook</label> 
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label input-text" for="social_share_twitter">
                                                        <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.social_share_twitter" id="social_share_twitter" > Twitter.</label>
                                                    </div> 
                                                </div>
                                            </div> 
                                            <div class="wpr_general_label">
                                                <label for="sharing_points">Enter Sharing  Points</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <label for="sharing_points">
                                                    <input type="text" class="form-control" id="sharing_points" v-model="form.social_share_points"  placeholder="Enter Sharing Points" > 
                                                </label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="general_row_wrap">
                                        <div class="wpr_general_sign_title">
                                            Redemption Settings </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="custom_points_on_cart">Redemption Over Cart Sub-Total</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <div class="form-check form-checkbox-success "> 
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label input-text" for="custom_points_on_cart">
                                                        <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.custom_points_on_cart" id="custom_points_on_cart"> Allow customers to apply points during Cart</label> 
                                                    </div> 
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="wpr_general_row">
                                            <div class="wpr_general_label">
                                                <label for="conversion_rate">Conversion rate for Cart Sub-Total Redemption</label>
                                            </div>
                                            <div class="wpr_general_content">
                                                <table class="form-table wp-list-table widefat fixed striped">
                                                    <tbody class="wpr_thankyouorder_tbody">                  
                                                        <tr valign="top">
                                                            <td >
                                                                <input type="number" v-model="form.cart_points_rate" id="cart_points_rate" class="form-control">  
                                                            </td>
                                                            <td >
                                                                <label for="wpr_thankyouorder_maximum">
                                                                    Points =
                                                                </label>
                                                            </td>
                                                            <td > 
                                                                <input type="text" v-model="form.cart_price_rate" id="cart_price_rate" class="form-control">  
                                                            </td>
                                                            <td > 
                                                                 ৳&nbsp; 
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table> 
                                            </div>
                                        </div> 
                                        <div class="general_row_wrap">
                                            <div class="wpr_general_sign_title">
                                                Enable the settings for the orders </div>
                                            <div class="wpr_general_row">
                                                <div class="wpr_general_label">
                                                    <label >Enter Points within Order Range</label>
                                                </div>
                                                <div class="wpr_general_content">                                                    
                                                    <table class="form-table wp-list-table widefat fixed striped">
                                                        <tbody class="wpr_thankyouorder_tbody">
                                                            <tr>
                                                                <td>
                                                                    <div class="enable_points_order_total">
                                                                        <div class="form-check form-checkbox-success "> 
                                                                            <div class="form-check form-check-inline">
                                                                                <label class="form-check-label input-text" for="enable_points_order_total">
                                                                                <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.enable_points_order_total" id="enable_points_order_total"> Enable Points on order total</label>  
                                                                            </div> 
                                                                        </div>  
                                                                    </div>  
                                                                </td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <th>Minimum</th>
                                                                <th>Maximum</th>
                                                                <th>Points</th>
                                                            </tr>  
                                                            <tr valign="top" v-for="(item, i) in form.points_within_order_range" v-if="form.points_within_order_range.length > 0" :key="item.id">
                                                                <td >
                                                                    <label for="wpr_thankyouorder_minimum">
                                                                        <input type="text" class="form-control" placeholder="No minimum"  
                                                                        v-model="item.minimum">
                                                                    </label>
                                                                </td>
                                                                <td >
                                                                    <label for="wpr_thankyouorder_maximum">
                                                                        <input type="text" class="form-control" placeholder="No maximum"  v-model="item.maximum">
                                                                    </label>
                                                                </td>
                                                                <td >
                                                                    <label for="wpr_thankyouorder_current_type">
                                                                        <input type="text" class="form-control" v-model="item.points"> 
                                                                    </label>
                                                                </td> 
                                                                <td @click="morePayemntRemove(i)"><i class="fas fa-trash"></i></td>  
                                                            </tr>

                                                        </tbody>
                                                    </table> 
                                                    <div class="row pt-2">
                                                        <div class="d-grid">
                                                          <button type="button" class="btn btn-primary" @click="morePayemnt()"> <i class="fa fa-plus"></i> Add More</button> 
                                                        </div> 
                                                      </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <p class="submit">
                                <button type="button" class="btn btn-sm btn-success" @click="submitForm" :disabled="disabled">Submit</button> 
                            </p>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </transition>
</template>
<script>
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'Company',
    components: {},
    data() {
        return {
            isSubmit: false,
            showModal: false,
            editMode: false,
            disabled: false,
            modalActive: false,
            errors: {},
            logoPreview: '',
            btn: 'Create',
            loading: true,
            items: [],
            more_order_points:[], 
            form: new Form({
                id: 1, 
                enable_points_rewards:'',
                enable_signup_points:'',
                signup_points:'',
                enable_referral_points:'',
                referral_points:'',
                enable_social_point:'',
                social_share_facebook:'',
                social_share_twitter:'',
                custom_points_on_cart:'',
                cart_points_rate:'',
                cart_price_rate:'',
                enable_points_order_total:'',
                social_share_points:'',
                points_within_order_range:[], 
                //order_range:[{minimum:'100',maximum:'200',points:'20'}, {minimum:'100',maximum:'200',points:'20'}]
            }),
        };
    },
    components: {
        Modal,
    },
    methods: {
        toggleModal: function() {
            this.modalActive = !this.modalActive;
            if (!this.modalActive) {
                this.editMode = false;
                this.btn = 'Create';
            }
            this.errors = '';
            this.isSubmit = false;
            this.form.reset();
        },
        morePayemnt : function(){ 
          let r = (Math.random() + 1).toString(36).substring(7);
          const newPayment = { 
            id: r
          };  
          const newObj = {minimum:'',maximum:'',points:''}
          //this.more_order_points.push(newPayment);
          this.form.points_within_order_range.push(newObj);
        },
        morePayemntRemove : function(index){ 
          console.log(index);
          //this.more_order_points.splice(index, 1);
          this.form.points_within_order_range.splice((index), 1);
        },
        fetchIndexData() { 
            axios.get(this.apiUrl + '/points_settings', this.headerjson)
                .then((res) => {
                    this.items = res.data.data;
                    if(res.data.data.length > 0){
                        this.form.fill(res.data.data[0]); 
                        if(!res.data.data[0].points_within_order_range){
                            const newObj = {minimum:'',maximum:'',points:''}
                            this.form.points_within_order_range.push(newObj);
                        }  
                    }                   
                })
                .catch((err) => { 
                    this.$toast.error(err.response.data.message);
                }).finally((ress) => {
                    this.loading = false;
                });
        },
        edit: function(item) {
            this.btn = 'Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item);
            this.logoPreview = '';
        }, 
        submitForm: function(e) {
            this.isSubmit = true;
            this.disabled = true;
            if (this.items.length > 0) { 
                var postEvent = axios.put(this.apiUrl + '/points_settings/' + this.form.id, this.form, this.headerjson);
            } else { 
                var postEvent = axios.post(this.apiUrl + '/points_settings/', this.form, this.headerjson);
            }

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if (res.status == 200) {
                    this.toggleModal();
                    this.fetchIndexData();
                    this.$toast.success(res.data.message);
                } else {
                    this.$toast.error(res.data.message);
                }
            }).catch(err => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if (err.response.status == 422) {
                    this.errors = err.response.data.errors
                }
            });
        },
         
        validation: function(...fiels) {
            var obj = new Object();
            var validate = false;
            for (var k in fiels) { // Loop through the object  
                for (var j in this.form) {
                    if ((j == fiels[k]) && (!this.form[j])) {
                        obj[fiels[k]] = fiels[k].replace("_", " ") + ' field is required'; // Delete obj[key]; 
                        this.errors = { ...this.errors, ...obj };
                    } else {
                        validate = false;
                    }
                }
            }
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },
        onkeyPress: function(field) {
            for (var k in this.errors) { // Loop through the object
                if (k == field) { // If the current key contains the string we're looking for 
                    delete this.errors[k]; // Delete obj[key];
                }
            }
        }, 
    },
    created() {
        this.fetchIndexData();
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
}
</script>
<style scoped>
#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.mwb_rwpr_content_template {
    box-shadow: -2px 0 5px -1px rgba(0, 0, 0, .05);
    color: #555d66;
    font-size: 16px;
    flex: 0 0 80%;
    padding: 10px 30px 10px 40px;
    position: relative;
    line-height: 28px;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.general_row_wrap {
    box-shadow: 2px 3px 20px rgba(0, 0, 0, .2);
    margin: 20px 0 40px 0;
}

.mwb_rwpr_content_template {
    color: #555d66;
    font-size: 16px;
    line-height: 28px;
}

.wpr_general_sign_title {
    width: 100%;
    display: block;
    padding: 8px 20px;
    background-color: #4f3154;
    display: block;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}

.wpr_general_row {
    display: flex;
    flex-wrap: wrap;
    padding: 0 10px;
    align-items: center;
}

.wpr_general_sign_title {
    width: 100%;
    display: block;
    padding: 8px 20px;
    background-color: #4f3154;
    display: block;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.wpr_general_label label {
    width: 150px;
    display: inline-block;
}

.wpr_general_label {
    width: 210px;
    font-weight: 600;
}

.wpr_general_content {
    width: calc(100% - 240px);
    margin-left: 20px;
}

.wpr_general_content,
.wpr_general_label {
    padding: 13px 0;
}

.modal-content.scrollbar-width-thin { 
    width: 900px;
}

label {
    display: inline-block;
    margin: 0px 0px 4px 2px;
    float: left;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
    text-align: left;
}
</style>