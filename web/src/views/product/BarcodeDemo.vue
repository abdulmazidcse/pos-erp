<template>
    <transition  >  
      <div class="row">
        <div class="col-md-12">
          <BarcodeGenerator
            :value="'A010101000001'"
            :format="'CODE128'"
            :lineColor="'black'"
            :width="2" 
            :height="30"
            :elementTag="'img'"
          />  <br>


          <BarcodeGenerator
            :value="'A010101000011'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.9"
            :elementTag="'img'"
          /> <br>

          <BarcodeGenerator
            :value="'A010101000012'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.8"
            :elementTag="'img'"
          /> <br>

          <BarcodeGenerator
            :value="'A010101000013'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.7"
            :elementTag="'img'"
          /> <br>

          <BarcodeGenerator
            :value="'A010101000014'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.6"
            :elementTag="'img'"
          /> <br>

          <BarcodeGenerator
            :value="'A010101000015'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.5"
            :elementTag="'img'"
          /> <br>
          <BarcodeGenerator
            :value="'A010101000016'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.4"
            :elementTag="'img'"
          /> <br>

          <BarcodeGenerator
            :value="'A010101000017'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1.2"
            :elementTag="'img'"
          /> <br>  
          <BarcodeGenerator
            :value="'A010101000018'"
            :format="'CODE128'"
            :lineColor="'black'" 
            :height="30"
            :width="1"
            :elementTag="'img'"
          /> <br>

          <br>  
           <h5>3. Barcode with EAN13 format</h5>
          <BarcodeGenerator
            :value="'101010100001'"
            :format="'EAN13'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'" 
          /> <br>
          <h5>3. Barcode with UPC format</h5>
          <BarcodeGenerator
            :value="'123456789999'"
            :format="'UPC'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'" 
          /> <br>
          <h5>3. Barcode with CODE39 format</h5>
          <BarcodeGenerator
            :value="'A010101000001'"
            :format="'CODE39'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'" 
          /> <br>
          <h5>3. Barcode with MSI format</h5>
          <BarcodeGenerator
            :value="'12345678901231'"
            :format="'MSI'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'"
          /> <br>
          <h5>3. Barcode with codabar format</h5>
          <BarcodeGenerator
            :value="'12345678901231'"
            :format="'codabar'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'"
          /> <br> 
          <h5>3. Barcode with CODE128 format</h5>
          <BarcodeGenerator
            :value="'A010101000001'"
            :format="'CODE128'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'"
          /> <br> 
          <h5>3. Barcode with ITF14 format</h5>
          <BarcodeGenerator
            :value="'12345678901231'"
            :format="'ITF14'"
            :lineColor="'blue'" 
            :height="30"
            :elementTag="'img'"
          /> 
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