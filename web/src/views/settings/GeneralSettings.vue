<template>
    <transition>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                      <ol class="breadcrumb m-0"> 
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Setting</a></li>
                          <li class="breadcrumb-item active">General settings </li> 
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
        <div class="card">
            <div class="card-body">
                <div class="wpr_table">
                    <div class="general_wrapper">
                        <div class="general_row_wrap  ">
                            <div class="wpr_general_sign_title">Invoice SMS Setting </div>
                            <div class="wpr_general_row  "> 
                                <div class="wpr_general_content raw ">
                                    <div class="form-check form-checkbox-success col-md-8"> 
                                        <div class="form-check form-check-inline form-group">
                                            <label class="form-check-label input-text" for="invoice_sms_status">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.invoice_sms_status" id="invoice_sms_status"> Enable Invoice Status</label> 
                                        </div> 
                                    </div> 
                                    <div class="form-check form-checkbox-success col-md-8"> 
                                        
                                        <div class="form-check form-check-inline form-group">
                                            <label class="form-check-label input-text" for="payment_status">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.payment_status" id="payment_status"> Enable Payment Status</label> 
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <label class="form-check-label input-text" for="date_status">
                                            <input type="checkbox" false-value="0" true-value="1" class="form-check-input" v-model="form.date_status" id="date_status" > Enable Date Status.</label>
                                        </div>  
                                        <div class="form-check form-check-inline form-group">
                                            <select v-if="form.date_status==1" class="input-group-text ml-2" v-model="form.date_format">  
                                                <option value="Y-m-d">Y-m-d</option>
                                                <option value="d-m-Y">d-m-Y</option>
                                                <option value="m-d-Y">m-d-Y</option>
                                                <option value="Y/m/d">Y/m/d</option>
                                                <option value="d/m/Y">d/m/Y</option>
                                                <option value="m/d/Y">m/d/Y</option>
                                            </select>  
                                        </div>  
                                    </div> 
                                    <div class="form-check form-checkbox-success col-md-10"> 
                                        <div class="form-check form-check-inline form-group">
                                            <label class="form-check-label input-text" for="sms_text">
                                            SMS Text</label> 
                                            <input type="text" class="form-control" v-model="form.sms_text" id="sms_text"> 
                                        </div>   
                                        <div class="form-check form-check-inline form-group">
                                            <label class="form-check-label input-text" for="sender_id">
                                            Sender ID</label> 
                                            <input type="text" class="form-control" v-model="form.sender_id" id="sender_id"> 
                                        </div>  
                                    </div>  
                                </div>
                            </div>
                            <div class="wpr_general_row col-md-12"> 
                                <div class="wpr_general_content">
                                    <div class="form-check form-checkbox-success "> 
                                        <div class="form-check form-check-inline">
                                            <lavel class="form-check-label input-text">API key</lavel>
                                            <input type="text" class="form-control" v-model="form.api_key" id="api_key">
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="wpr_general_row col-md-12"> 
                                <div class="wpr_general_content">
                                    <div class="form-check form-checkbox-success "> 
                                         Sms Preview 
                                    </div> 
                                    <div class="form-check form-checkbox-success "> 
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label input-text" for="sms_text">
                                            <span>Shop Name, Pro-Name, WEIGHT: 0.00 KG, RATE: TAKA 000/KG, TOTAL 00 TAKA, </span>
                                            <span v-if="form.payment_status ==1">RECEIVED: CASH/BKASH/NAGAD/CARD/CQ,</span> 
                                            <span v-if="form.date_status==1">DATE {{form.date_format}} &nbsp</span>
                                            <span> {{form.sms_text}}</span>  
                                            </label>   
                                        </div>   
                                          
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
                invoice_sms_status:'1',
                payment_status:'1',
                date_status:'1',
                date_format:'Y-m-d',
                sms_text:'',
                api_key:'',
                sender_id:'', 
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
        if (this.form.id > 0) { 
          var postEvent = axios.put(this.apiUrl + '/general_settings/' + this.form.id, this.form, this.headerjson);
        } else { 
          var postEvent = axios.post(this.apiUrl + '/general_settings', this.form, this.headerjson);
        } 
        postEvent.then((res) => {
          this.isSubmit = false;
          this.disabled = false;
          this.fatchData();
          this.$toast.success(res.data.message);
        })
        .catch((response) => {  
          this.loading = false; 
          this.isSubmit = false;
          this.disabled = false;
        })
      },
      async fatchData() {
          await axios.get(this.apiUrl+'/general_settings',this.headers)
          .then((res) => {
            if(res.data.data.length > 0){
              this.form.fill(res.data.data[0]); 
              this.form.id = res.data.data[0].id;  
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
    computed: {}
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
.modal-content.scrollbar-width-thin {
    border: none !important; 
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