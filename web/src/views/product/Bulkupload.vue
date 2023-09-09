<template>
    <transition  >  
      <div class="row">
        <div class="col-md-12">
          <form @submit.prevent="uploadExcel()" role="form">
              <div class="form-group">
                  <p>Download format and product list by filtering in <a :href="url+'admin/stock-report'">stock list report </a> <br> you will find a button download sample product list</p> <br>
                  <span class="btn btn-primary btn-file"><span class="fileinput-new" id="excel-file"><i class="fa fa-file-excel-o"></i> Chose Excel</span>
                  <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/></span>
              </div>
              <button type="submit" class="btn btn-info"><i class="fa fa-upload" aria-hidden="true"></i> {{ button_name }}</button>
          </form>
      </div>
    </div>          
    </transition>
</template>
<script>  
import { ref } from "vue";
import Form from 'vform' 
  
import axios from 'axios';  
export default {
    name: 'Bulkupload',
    components: {  
    },
    data() {
        return { 
          file : '',
          attachment : '',
          upload_size : 0,
          url : base_url,
          button_name : "Upload",
          validation_error : null,
          form: new Form({ 
              allow_checkout_when_out_of_stock:0
          }),
        };
    }, 
    components: { 
    }, 
    methods: {      

    },
    created() {   
      
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
};
</script>
<style scoped>
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