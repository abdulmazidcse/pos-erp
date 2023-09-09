<template>
    <transition>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                      <ol class="breadcrumb m-0"> 
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Sales</a></li>
                          <li class="breadcrumb-item active">Offer settings List </li> 
                      </ol>
                  </div>
                  <div class="page-title-right float-right">  
                      <!-- <button type="button" class="btn btn-primary float-right" @click="onFilter">
                        
                      </button>
                      <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                          Add New
                      </button>  -->
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
                                <div class="wpr_general_sign_title"> Customer wise offer </div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                        <div class="form-check form-checkbox-success mb-2">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="customer_wise" v-model="form.customer_wise">
                                            <label class="form-check-label" for="customer_wise">Enable</label>
                                        </div> 
                                    </div>
                                    <div class="wpr_general_content">
                                      <div class="form-check form-checkbox-success "> 
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label input-text" for="individual_customer">
                                                <input :disabled="form.customer_wise==0" type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.individual_customer" id="individual_customer"> Enable individual customer offer</label> 
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label input-text" for="customer_group_wise">
                                                <input :disabled="form.customer_wise==0" type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.customer_group_wise" id="customer_group_wise" > Enable group customer offer.</label>
                                            </div> 
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.customer_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.customer_offer_within_range[0].end_date"> 
                                                        </td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title"> Product wise offer </div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                        <div class="form-check form-checkbox-success mb-2">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="product_wise" v-model="form.product_wise">
                                            <label class="form-check-label" for="product_wise">Enable</label>
                                        </div> 
                                    </div>
                                    <div class="wpr_general_content">
                                      <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.product_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.product_offer_within_range[0].end_date"> 
                                                        </td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> 
                                    </div> 
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title"> Category wise offer </div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                      <div class="form-check form-checkbox-success mb-2">
                                        <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="category_wise" v-model="form.category_wise">
                                        <label class="form-check-label" for="category_wise">Enable</label>
                                      </div> 
                                    </div> 
                                    <div class="wpr_general_content">
                                      <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.category_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.category_offer_within_range[0].end_date"> 
                                                        </td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">Sub-Category wise offer </div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                        <div class="form-check form-checkbox-success mb-2">
                                          <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="sub_category_wise" v-model="form.sub_category_wise">
                                          <label class="form-check-label" for="sub_category_wise">Enable</label>
                                        </div> 
                                    </div>
                                    <div class="wpr_general_content">
                                        <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.sub_cat_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.sub_cat_offer_within_range[0].end_date"> 
                                                        </td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">Vendor wise offer</div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                        <div class="form-check form-checkbox-success mb-2">
                                          <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="vendor_wise" v-model="form.vendor_wise">
                                          <label class="form-check-label" for="vendor_wise">Enable</label>
                                        </div> 
                                    </div> 
                                    <div class="wpr_general_content">
                                        <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.vendor_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.vendor_offer_within_range[0].end_date"> 
                                                        </td> 
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">Slow moving product offer</div>
                                <div class="wpr_general_row">
                                  <div class="wpr_general_label">
                                    <div class="form-check form-checkbox-success mb-2">
                                      <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="slow_moving_product" v-model="form.slow_moving_product">
                                      <label class="form-check-label" for="slow_moving_product">Enable</label>
                                    </div> 
                                  </div>  
                                  <div class="wpr_general_content">   
                                        <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                      <th width="30%" title="It's will work total cart value ">Discount (It's will calculate % wise)</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.slow_moving_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.slow_moving_offer_within_range[0].end_date"> 
                                                        </td> 
                                                        <td > 
                                                            <input type="text" v-model="form.slow_moving_product_discount" class="form-control border " id="slow_moving_product_discount" placeholder="Ex-5" autocomplete="off"> 
                                                           
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                  </div>  
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">Fast moving product offer</div>
                                <div class="wpr_general_row">
                                  <div class="wpr_general_label">
                                    <div class="form-check form-checkbox-success mb-2">
                                      <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="fast_moving_product" v-model="form.fast_moving_product">
                                      <label class="form-check-label" for="fast_moving_product">Enable</label>
                                    </div> 
                                  </div>
                                  <div class="wpr_general_content">   
                                        <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th> 
                                                      <th width="30%" title="It's will work total cart value ">Discount (It's will calculate % wise)</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.fast_moving_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.fast_moving_offer_within_range[0].end_date"> 
                                                        </td> 
                                                        <td > 
                                                            <input type="text" v-model="form.fast_moving_product_discount" class="form-control border " id="fast_moving_product_discount" placeholder="Ex-5" autocomplete="off"> 
                                                           
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                  </div>  
                                </div>  
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">Sales platform wise offer</div>
                                <div class="wpr_general_row">
                                  <div class="wpr_general_label">
                                    <div class="form-check form-checkbox-success mb-2">
                                      <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="sales_platform" v-model="form.sales_platform">
                                      <label class="form-check-label" for="sales_platform">Enable</label>
                                    </div> 
                                  </div> 
                                  <div class="wpr_general_content"> 
                                    <div class="row">
                                      <div class="form-check form-checkbox-success col-md-6"> 
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label input-text" for="sales_platform_pos">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.sales_platform_pos" id="sales_platform_pos">POS</label> 
                                        </div>
                                        <div class="mb-2 col-md-10"> 
                                            <label class="form-label" for="sales_platform_pos_discount">Discount (It's will calculate % wise)</label>
                                            <input type="text" v-model="form.sales_platform_pos_discount" class="form-control mt-2" id="sales_platform_pos_discount" placeholder="Ex-5" autocomplete="off"> 
                                        </div>
                                      </div>
                                      <div class="form-check form-checkbox-success col-md-6">                                       
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label input-text" for="sales_platform_ecom">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.sales_platform_ecom" id="sales_platform_ecom" > E-Commerce.</label> 
                                        </div> 
                                        <div class="mb-2 col-md-10"> 
                                            <label class="form-label" for="sales_platform_ecom_discount">Discount (It's will calculate % wise)</label>
                                            <input type="text" v-model="form.sales_platform_ecom_discount" class="form-control ml-2" id="sales_platform_ecom_discount" placeholder="Ex-5" autocomplete="off"> 
                                        </div>
                                      </div>
                                      </div>
                                  </div> 
                                </div>
                            </div>
                            <div class="general_row_wrap">
                                <div class="wpr_general_sign_title">GP wise discount</div>
                                <div class="wpr_general_row">
                                    <div class="wpr_general_label">
                                        <div class="form-check form-checkbox-success mb-2">
                                          <input type="checkbox" false-value="0" true-value="1" class="form-check-input" id="gp_wise" v-model="form.gp_wise">
                                          <label class="form-check-label" for="gp_wise">Enable</label>
                                        </div> 
                                    </div> 
                                    <div class="wpr_general_content">   
                                        <div class="form-check form-checkbox-success ">  
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                        <th width="35%">Start Date</th>
                                                        <th width="35%">End Date</th> 
                                                        <th width="30%">Discount (It's will calculate % wise)</th> 
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.gp_offer_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.gp_offer_within_range[0].end_date"> 
                                                        </td> 
                                                        <td > 
                                                            <input type="text" v-model="form.gp_wise_discount" class="form-control" id="gp_wise_discount" placeholder="Ex-5" autocomplete="off">  
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>  
                                </div>
                            </div>   
                            <div class="general_row_wrap">   
                                <div class="general_row_wrap">
                                    <div class="wpr_general_sign_title">
                                      Conditional offer </div>
                                    <div class="wpr_general_row">
                                        <div class="wpr_general_label">
                                            <div class="enable_points_order_total col-md-12">
                                                <div class="form-check form-checkbox-success "> 
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label input-text" for="enable_conditional_discount">
                                                        <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.enable_conditional_discount" id="enable_conditional_discount"> Enable Conditional offer</label>
                                                    </div> 
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="wpr_general_content">                                                    
                                            <table class="form-table wp-list-table widefat fixed striped" style="width:100%">
                                                <tbody class="wpr_thankyouorder_tbody"> 
                                                    <tr valign="top">
                                                      <th width="35%">Start Date</th>
                                                      <th width="35%">End Date</th>
                                                      <th width="30%" title="It's will work total cart value ">Discount</th>
                                                    </tr>  
                                                    <tr valign="top">
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="Start Date"  
                                                              v-model="form.discount_within_range[0].start_date">
                                                        </td>
                                                        <td > 
                                                            <input type="date" class="form-control" placeholder="End Date"  v-model="form.discount_within_range[0].end_date"> 
                                                        </td>
                                                        <td >
                                                          <label for="wpr_thankyouorder_current_type">
                                                            <input type="text" class="form-control" v-model="form.discount_within_range[0].discount"> 
                                                          </label>
                                                        </td>   
                                                    </tr>
                                                </tbody>
                                            </table> 
                                            <!-- <div class="row pt-2">
                                              <div class="d-grid">
                                                <button type="button" class="btn btn-primary" @click="morePayemnt()"> <i class="fa fa-plus"></i> Add More</button> 
                                              </div> 
                                            </div>  -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <p class="submit float-right">
                        <button type="button" class="btn btn-sm btn-success" @click="submitForm" :disabled="disabled">
                        <span v-show="isSubmit">
                            <i class="fas fa-spinner fa-spin" ></i>
                        </span> Submit</button> 
                    </p>
                </div>
            </div>
        </div>
      </div>    
      </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal"; 
import Form from 'vform'   
import axios from 'axios';  
export default {
    name: 'POS Sales',
    components: { 
      Modal,   
    },
    data() {  
        return {   
            isSubmit: false,
            discountHead: [],
            discountData:'',
            loading:true,  
            disabled: false,  
            form: new Form({
                id: '1', 
                customer_wise:'',
                customer_group_wise:'',
                individual_customer :'',
                product_wise:'',
                category_wise:'',
                sub_category_wise:'',
                vendor_wise:'',
                slow_moving_product:'',
                slow_moving_product_discount:'',
                fast_moving_product:'',
                fast_moving_product_discount:'',
                sales_platform:'',
                sales_platform_pos:'',
                sales_platform_pos_discount:'',
                sales_platform_ecom:'',
                sales_platform_ecom_discount:'',
                gp_wise:'',
                gp_wise_discount:'',
                enable_conditional_discount:'', 
                discount_within_range:[{start_date:'',end_date:'',discount:''}], 
                customer_offer_within_range:[{start_date:'',end_date:''}], 
                product_offer_within_range:[{start_date:'',end_date:''}], 
                category_offer_within_range: [{start_date:'',end_date: ''}],
                sub_cat_offer_within_range: [{start_date:'',end_date: ''}],
                vendor_offer_within_range: [{start_date:'',end_date: ''}],
                slow_moving_offer_within_range: [{start_date:'',end_date: ''}],
                fast_moving_offer_within_range: [{start_date:'',end_date: ''}],
                sales_platform_pos_offer_within_range: [{start_date:'',end_date: ''}],
                sales_platform_ecom_offer_within_range: [{start_date:'',end_date: ''}],
                gp_offer_within_range: [{start_date:'',end_date: ''}],
            }), 
            multiclasses:{ 
              clear: '',
              clearIcon: '', 
            }, 
        };
    },  
    methods: {  
      submitForm: function(){
        this.isSubmit = true;
        this.disabled = true; 
        console.log('submitForm', this.form);
        if (this.discountData.length > 0) { 
          var postEvent = axios.put(this.apiUrl + '/discount_settings/' + this.form.id, this.form, this.headerjson);
        } else { 
          var postEvent = axios.post(this.apiUrl + '/discount_settings/', this.form, this.headerjson);
        } 
        postEvent.then((res) => {
          this.isSubmit = false;
          this.disabled = false;
          this.fatchData();
          this.$toast.success(res.data.message);
        })
        .catch((response) => {  
          this.loading = false; 
        })
      },
      async fatchData() {
          await axios.get(this.apiUrl+'/discount_settings',this.headers)
          .then((res) => {
            if(res.data.data.length > 0){
              this.form.fill(res.data.data[0]); 
              // if(!res.data.data[0].discount_within_range){
              //     const newObj = {start_date:'',end_date:'',discount:''}
              //     this.form.discount_within_range.push(newObj);
              // }
            }
            this.discountData = res.data.data;  
          }) 
      }
    },
    async created() {  
      this.fatchData(); 
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        permission() {
            let pname = this.$route.meta.parent_module;
            let module_name = this.$route.meta.module_name;
            let path_name = this.$route.path; 
            let data = '';
            if(this.$route.meta.parent_module){
                data = this.$store.getters.userAllPermissions[pname][0].children[path_name]
            }else{
                data = this.$store.getters.userAllPermissions[module_name][0].other_actions; 
            } 
            return data;
        },
        
    }
};
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
    background-color: #034f84;
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
    background-color: #034f84;
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
    border: none !important;
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
<style scoped>
.btn-file {
  overflow: hidden;
  position: relative;
  vertical-align: middle;
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
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

/*.svg-inline--fa.fa-random {
  margin: -33px 0px 0px 103px !important;
  background-color: #ccc;
  padding: 9px;
  z-index: 100;
  display: inline-block;
  position: absolute;
  border-radius: 0px 3px 3px 0px;
}*/ 
  .multiselect-tag.is-user {
    padding: 5px 8px;
    border-radius: 22px;
    background: #35495e;
    margin: 3px 3px 8px;
  }

  .multiselect-tag.is-user img {
    width: 18px;
    border-radius: 50%;
    height: 18px;
    margin-right: 8px;
    border: 2px solid #ffffffbf;
  }

  .multiselect-tag.is-user i:before {
    color: #ffffff;
    border-radius: 50%;;
  }

  .user-image {
    margin: 0 6px 0 0;
    border-radius: 50%;
    height: 22px;
  }
  .multiselect-clear { 
    display: inline-block !important;
    float: right !important;;
  }
  .multiselect { 
    display: block;
    position: relative; 
  }
  .multiselect.is-active{
    z-index: 1;
  }
</style>
<style src="@vueform/multiselect/themes/default.css"></style>