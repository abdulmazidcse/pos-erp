<template>
<form  @submit.prevent="submitForm()" enctype="multipart/form-data" novalidate="true" class="needs-validation">
  <div class="pos-leftbar " id="pos-leftbar"> 
      <div class="pos-header row"> 
        <div class="mb-3"> 
          <div class="input-group input-group-merge">
              <Multiselect
                class="form-control border" 
                mode="single"
                v-model="form.customer_id"
                placeholder="Customer"
                :searchable="true" 
                :options="customers"   
                autocomplete
              /> 
              <div class="input-group-text"><a href="">  <i class="fas fa-2x fa-plus-circle"  ></i> </a> </div> 
          </div>
        </div>
        <div class="form-group col-md-12 ">    
           <input type="text" v-model="searchTerm" class="form-control" placeholder="Scan/Search product by name/code"> 

            <ul
              v-if="searchCountries.length"
              class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10"
            >
              <li class="px-1 pt-1 pb-2 font-bold border-b border-gray-200">
                Showing {{ searchCountries.length }} of {{ countries.length }} results
              </li>
              <li
                  v-for="country in searchCountries"
                  :key="country.name"
                  @click="selectCountry(country.name)"
                  class="cursor-pointer hover:bg-gray-100 p-1"
              >
                {{ country.name }}
              </li>
            </ul>

        </div>   
      </div>  
      <div class="pos-body scrollbar-width-thin" v-bind:style="{height: window.height + 'px' }"> 
          <div class=" ">  
            <table class="table-sm pos-table table items table-striped table-bordered table-condensed table-hover sortable_table">
              <thead class="tableFloatingHeaderOriginal">
                <tr class="border   item-head">
                 <th width="50%">Item {{cartItems.length}} {{productItems.length}}</th>
                 <th width="20%">Price</th>
                 <th width="10%">Qty</th>
                 <th width="15%">Subtotal</th>
                 <th width="5%">Del</th>
                 <th width="5%">+</th>
               </tr>
              </thead>             
                <tr class="border" v-for="(item, i) in cartItems" v-if="cartItems.length > 0">
                 <td>{{ item.title.substring(0,30,'...') }} <span v-if="item.title.length > 30">...</span> </td>
                 <td>{{ item.price }} {{i}}</td>
                 <td><input class="qty" type="number" name="" v-model="item.quantity"> </td>
                 <td>{{ item.price * item.quantity }}</td> 
                 <td @click="removeCartItem(i)"><i class="fas fa-trash"></i></td>
                 <td @click="addCartItem(item)"><i class="fas fa-shopping-cart"></i></td>
                </tr> 
            </table>
          </div> 
      </div>
      <div class="pos-footer"> 
         <div class="row">
            <div class="col-md-4" style="padding: 0;">
              <div class="btn-group-vertical btn-block">
                <button type="button" class="btn btn-warning btn-block btn-flat" id="suspend">
                Hold </button>
                <button type="button" class="btn btn-danger btn-block btn-flat" id="reset" @click="removeAllCartItems">
                Cancel </button>
              </div>
            </div>
            <div class="col-md-4" style="padding: 0 5px;">
              <div class="btn-group-vertical btn-block">
                <button type="button" class="btn bg-purple btn-block btn-flat" id="print_order">
                Print Order </button>
                <button type="button" class="btn bg-navy btn-block btn-flat white" id="print_bill">
                Print Bill </button>
              </div>
            </div>
            <div class="col-md-4" style="padding: 0;">
              <button type="submit" class="btn btn-success float-right ">Submit</button>
              <button type="submit" class="btn btn-success bg-color-023844 btn-block btn-flat" id="payment" style="height:59px;">
              Payment </button>
            </div>
          </div>
      </div>
  </div> 
</form>
</template>
<script>   
import countries from '../../data/countries.json'
import { mapGetters, mapActions } from "vuex";   
import { ref } from "vue";
import Form from 'vform' 
import axios from 'axios';
import { reactive, toRefs, computed } from 'vue'

export default {
  setup() {
    let searchTerm = ref('')
    const searchCountries = computed(() => {
      if (searchTerm.value === '') {
        return []
      }
      let matches = 0
      return countries.filter(country => {
        if (country.name.toLowerCase().includes(searchTerm.value.toLowerCase()) && matches < 10) {
          matches++
          return country
        }
      })
    });
    const selectCountry = (country) => {
      selectedCountry.value = country
      searchTerm.value = ''
    }
    let selectedCountry = ref('')
    return { 
      searchTerm,
      searchCountries,
      selectCountry,
      selectedCountry
    }
  },
  name: 'PosLeftbar', 
  components: { 
  },
  data() {
    return { 
        getItems:'',
        items: [],
        customers:[{ label: 'name 1', value: 1 }, { label: 'name 2', value: 2 }],
        outlets:[],
        window: {
            width: 0,
            height: 0
        },
        form: new Form({
          id:'',
          product_type:'standard',
          customer_id:''
        })
    };
  },
  props: {
    props: ['cartItem'],
  },
  components: { 
  },
  methods: {
    submitForm(){ 
      console.log(this.cartItems);
    },
    fetchCustomers: function() {     
      axios.get(this.apiUrl+'/customers',this.headers)
      .then((res) => {                
        /// this.customers = res.data.data.map(({ id, name }) => ({ label: name, value: id })); 
      })
      .catch((response) => {  
      })
      .finally((ress) => { 
      });
    },
    fetchLanguages : function(e){
      let ffff = '?product_name='+e+'&product_code='+e
      axios.get(this.apiUrl+'/products',this.headers)
      .then((res) => {                
          this.items = res.data.data; 
      })
      .catch((response) => {  
      })
      .finally((ress) => {
        this.loading = false; 
      });
    },
    handleResize() { 
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight-160;
      var body = document.body; 
      var b = document.querySelector("body"); 
      document.getElementsByTagName('body')[0].classList.add('sidebar-enable'); 
      if(b.hasAttribute('data-leftbar-compact-mode')){
        b.removeAttribute('data-leftbar-compact-mode')
      }else{
        b.setAttribute("data-leftbar-compact-mode", "condensed");
      } 
    },
    deleteItem: function(item) {
      this.items.splice(this.items.indexOf(item), 1);
    }, 
    ...mapActions(['removeAllCartItems','removeCartItem','addCartItem']),
  },
  created(){ 
    window.addEventListener('resize', this.handleResize);
    this.handleResize();  
    this.fetchCustomers();
    this.$store.dispatch('getProductItems'); 
  },
  destroyed() {
    window.removeEventListener('resize', this.handleResize);
  },
  mounted(){
    window.scrollTo(0,0);  
  },
  computed:{
    ...mapGetters([
      'productItems',
      'cartItems',
      'cartTotal',
      'cartQuantity'
    ]), 
    totalQuantity: function(){ 
      return this.items.reduce(function(total, item){
        return total + item.price; 
      },0);
    }, 
    totalSumm: function(){
        return this.items.reduce(function(total, item){
          return total + (item.price * item.qty); 
        },0);
      }, 
    }
}

</script>
<style scoped>
.form-group {
  margin-bottom: 5px ;
}
.border.success.item-head {
  width: 20px !important;
  color: #f4f4f4;
  background-color: #3e81ae; 
} 
.table-bordered th, .table-bordered td {
  border: none;
}
.pos-footer {
  width: 96%;
  margin: 0px auto; 
}
.pos-body {
  min-height: 371px;
  overflow-x: scroll; 
} 
.qty {
  width: 71px;
}
</style>