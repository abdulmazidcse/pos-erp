<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Requisition </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Store ddd Purchase Requisition</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <a href="/store-requisition-list"><button type="button" class="btn btn-primary float-right">
                            Requisition List
                        </button></a>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <!-- <form @submit.prevent="submitForm()" enctype="multipart/form-data" > -->

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="requisition_no">Requisition No *</label>
                                        <input type="text" class="form-control border" id="requisition_no" @change="onkeyPress('requisition_no')" v-model="obj.requisition_no" readonly>
                                        <div class="invalid-feedback" v-if="errors.requisition_no">
                                            {{errors.requisition_no[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="requisition_date">Date *</label>
                                        <input type="date" class="form-control border" id="requisition_date" @change="onkeyPress('requisition_date')" v-model="obj.requisition_date" readonly>
                                        <div class="invalid-feedback" v-if="errors.requisition_date">
                                            {{errors.requisition_date[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="requisition_date">Outlet List</label>
                                        <select class="form-control border" v-model="obj.outlet_id" id="outlet_id"> 
                                            <option v-for="(outlet, index) in outlets" :value="outlet.id" :key="index">{{ outlet.name }}</option>
                                        </select>  
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <!-- Product Details -->
                                    <div class="card">
                                        <div class="card-header text-left">
                                            Product Details
                                            <input type="hidden" v-model="obj.total_quantity">
                                            <!-- <span class="total_quantity">Requisition Quantity: <b>{{ totalQuantity }}</b></span> -->
                                        </div>
                                        <div class="card-body">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control border" v-model="category_id" @change="onkeyPress('category_id'), onChangeParentCategory($event.target.value)" id="category_id">
                                                            <option value="0">Select Category</option>
                                                            <option v-for="(pcategory, index) in parent_categories" :value="pcategory.id" :key="index">{{ pcategory.name }}</option>
                                                        </select>                                                        
                                                        <div class="invalid-feedback" v-if="errors.category_id">
                                                            {{errors.category_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control border" v-model="sub_category_id" @change="onkeyPress('sub_category_id'), onChangeSubCategory($event.target.value)" id="sub_category_id">
                                                            <option value="0">Select Sub Category</option>
                                                            <option v-for="(scategory, index) in sub_categories" :value="scategory.id" :key="index">{{ scategory.name }}</option>
                                                        </select>                                                        
                                                        <div class="invalid-feedback" v-if="errors.sub_category_id">
                                                            {{errors.sub_category_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="autocomplete form-group">
                                                        <input type="hidden" class="form-control border" id="product_code" v-model="product_code">
                                                        <input type="text" class="form-control border" id="searchTerm" @change="onkeyPress('searchTerm')" @keyup="inputChanged" @keyup.enter="getProductByCode()"  @keydown.down="onArrowDown" @keydown.up="onArrowUp" v-model="searchTerm" placeholder="Search By Product Code or Name">
                                                        <div class="invalid-feedback" v-if="errors.searchTerm">
                                                            {{errors.searchTerm[0]}}
                                                        </div>
                                                        <ul
                                                            v-if="searchProducts.length"
                                                            class="w-full rounded bg-white border border-gray-300 px-4 py-2 space-y-1 absolute z-10 autocomplete-results"
                                                        >
                                                            <li>
                                                            Showing {{ searchProducts.length }} of {{ requisitionProductItems.length }} results
                                                            </li>
                                                            <li
                                                                v-for="(item, i) in searchProducts"
                                                                :key="i"
                                                                @change="selectProduct(item)"
                                                                @click="setResult(item)"
                                                                :class="{ 'is-active': i === arrowCounter }"
                                                            >
                                                            {{ item.product_name }} ({{item.product_code}})
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>   

                                            </div>

                                            <div class="table-responsive product_table">
                                                <table class="table table-bordered table-centered table-nowrap">
                                                    <thead class="table-light">
                                                        <tr class="success item-head">
                                                            <th class="text-center">SL </th> 
                                                            <th class="text-center">Name </th> 
                                                            <!-- <th class="text-center">Barcode</th>  -->
                                                            <th class="text-center">UOM</th>
                                                            <th class="text-center">Purchase. Price</th>
                                                            <th class="text-center">MRP</th>
                                                            <!-- <th class="text-center">Crt. Size</th> -->
                                                            <!-- <th width="10%">Req. Carton Qty </th> -->
                                                            <th  class="text-center" width="10%">Req. Qty </th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Actions</th>

                                                        </tr>
                                                    </thead>

                                                    <tbody v-if="product_items.length > 0">
                                                        <tr v-for="(product_item, i) in product_items" :key="i">
                                                            <td class="text-center">{{ i + 1 }}</td>
                                                            <td class="text-center">{{ product_item.product_name }}</td>
                                                            <!-- <td class="text-center">{{ product_item.product_code }}</td> -->
                                                            <td class="text-center">{{ product_item.purchase_unit }}</td>
                                                            <td class="text-center">{{ product_item.cost_price }}</td>
                                                            <td class="text-center">{{ product_item.mrp_price }}</td>
                                                            <!-- <td class="text-center">{{ product_item.carton_size }}</td> -->
                                                            <!-- <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.req_carton_qty"></td> -->
                                                            <td><input type="text" class="form-control" @keyup="inputChange()" v-model="product_item.req_qty"></td>
                                                            <td class="text-center">{{ product_item.req_qty * product_item.cost_price }}</td>
                                                            <td class="text-center">
                                                                <a href="#" class="remove_item" @click.prevent="removeProductListItem(i)" title="Remove"><i class="fas fa-times"></i></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    
                                                </table>
                                            </div>

                                            <div class="summation_details">
                                                
                                                <!-- <span class="float-right text-danger">Total Amount: <strong>{{ totalAmount }}</strong></span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary float-right" :disabled="disabled" @click.prevent="submitForm()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span> Submit 
                                        </button>
                                        <!-- <button type="submit" class="btn btn-primary">SAVE</button> -->
                                        <!-- <button type="button" class="btn btn-info" :disabled="disabled">HOLD</button>
                                        <button type="button" class="btn btn-danger" :disabled="disabled">PREVIEW</button> -->
                                    </div>
                                </div>
                            </div>
                        <!-- </form> -->
                    </div>
                </div>
            </div>

            <div
                class="right-drawer"
                :style="{
                    width: drawerVisible? '30vw' : '0',
                    paddingLeft: drawerVisible? '10px' : '0',
                    paddingRight: drawerVisible? '10px' : '0',
                }"
            >
                <div style="text-align:right; margin:5px; padding-right: 10px;" v-if="drawerVisible">
                    <!-- <button class="btn btn-danger btn-sm" @click="drawerVisible = false"> -->
                        <i class="mdi mdi-close-outline" style="font-size:24px; color: red;" @click="drawerVisible = false"></i>
                    <!-- </button> -->
                </div>

                <div class="drawer-body" style="position: relative;">
                    
                    <div class="card">
                        <div class="card-header">
                            <h1>Product List</h1>
                        </div>
                        <div class="product-content card-body">
                            <h4>Home</h4>
                            <h4>About</h4>
                            <h4>Stories</h4>
                            <h4>Testimonials</h4>
                            <h4>Contact</h4>
                        </div>
                    </div>

                    <button 
                        class="btn btn-dark drawer-btn"  
                        @click="drawerVisible = true" 
                        v-if="!drawerVisible"
                        style="left: -23px;"
                    >
                        <i class="mdi mdi-arrow-left"></i>
                    </button>
                    <button 
                        class="btn btn-dark drawer-btn"  
                        @click="drawerVisible = false" 
                        v-else
                    >
                        <i class="mdi mdi-arrow-right"></i>
                    </button>
                </div>
            </div>
            <div
                class="drawer-mask"
                :style="{
                    width: drawerVisible ? '100vw' : '0',
                    opacity: drawerVisible ? '0.6' : '0',
                }"
                @click="drawerVisible = false"
                >
            </div>

        </div>
    </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 

export default {
    name: 'Store Purchase Requisition',
    components: {
        Modal
    },
    data() {
        return {
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: true,
            modalActive:false,
            errors: {},
            btn:'Create',
            productShowBtn: false,
            outlets: [],
            product_items: [],
            product_list: [],
            product_code: '',
            searchTerm: '',
            arrowCounter: -1,
            obj: new Form({
                outlet_id: '',
                requisition_date: new Date().toISOString().slice(0,10),
                requisition_no: '',
                total_quantity: 0,
                total_value: '',
                total_amount: 0, 
            }),
            searchProducts: [],
            parent_categories: [],
            parent_sub_categories: [],
            sub_categories: [],
            category_id: 0,
            sub_category_id: 0,
            drawerVisible: false,
            
        };
    },
    created() { 
        this.$store.dispatch('getRequisitionProductItems', {url: this.apiUrl, header: this.headerjson});
        this.fetchStoreRequisitionNo();
        this.fetchOutlets();
        this.fetchAllCategory();
    },
    methods: { 
        // currentDate() {
        //     const current = new Date();
        //     const date = `${current.getDate()}/${current.getMonth()+1}/${current.getFullYear()}`;     
        //     return date;
        // },
        toggleModal: function() {
            this.modalActive = !this.modalActive; 
            // if(!this.modalActive){
            //     this.editMode = false;
            //     this.btn='Create';
            // } 
            // this.errors = '';
            // this.isSubmit = false;
            // console.log('then',this.isSubmit)
        },

        fetchStoreRequisitionNo() {
            axios.get(this.apiUrl+'/store_requisitions/getRequisitionNo', this.headers)
            .then((res) => {
                this.obj.requisition_no = res.data.data.requisition_no;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        fetchAllCategory(){
            axios.get(this.apiUrl+'/getProductCategory', this.headerjson)
            .then((res) => {
                this.parent_categories = res.data.data.parent_category;
                this.parent_sub_categories = res.data.data.sub_category;
            })
            .catch((err) => {
                console.log("error==", err.response);
            })
        },
        fetchOutlets(){
            axios.get(this.apiUrl+'/outlets-list', this.headerjson)
            .then((res) => {
                this.outlets = res.data.data; 
            })
            .catch((err) => {
                console.log("error==", err.response);
            })
        },

        onChangeParentCategory: function(cat_id) {
            this.sub_category_id = 0;
            this.searchProducts = [];
            this.searchTerm = '';
            if(cat_id != 0) {
                this.sub_categories = this.parent_sub_categories[cat_id];
            }else{
                this.sub_categories = [];
            }

        },

        onChangeSubCategory: function(subcat_id) {
            this.searchProducts = [];
            this.searchTerm = '';
            if(subcat_id != 0) {
                var filtered = this.$store.getters.requisitionProductItems.filter(item => {

                    if(item.sub_category_id == subcat_id) {
                        let item_data = item.product_name +' ('+ item.product_code + ')';
                        return item_data;
                    }

                });

                this.searchProducts.push(...filtered);
            }
        },

        getProductList() {
            axios.get(this.apiUrl+'/products', this.headerjson)
            .then((res) => {
                this.product_list = res.data.data;
                this.toggleModal();
            })
            .catch()
            .finally();
        },

        getProductByCode(){

            if(this.product_code == '') {
                this.product_code = this.searchTerm;
            }
            let data = {code: this.product_code};

            this.searchTerm = '';
            if(!this.checkExistsProduct(this.product_items, this.product_code)) {
                if(this.product_code != '') {
                    axios.post(this.apiUrl+'/store_requisitions/getProduct', data, this.headerjson)
                    .then((res) => {
                        this.product_code = '';
                        this.searchTerm = '';
                        this.arrowCounter = -1;
                        this.product_items.push(res.data.data);

                    })
                    .catch((err) => {
                        this.$toast.error(err.response.data.message);
                    });
                }
            }else{
                this.product_code = '';
                this.searchTerm = '';
                this.arrowCounter = -1;
                this.$toast.error("This Product Already Added!");
            }

        },

        inputChanged(event) {
            
            if (event.code == "ArrowUp" || event.code == "ArrowDown")
                return;
                
            this.searchProducts = [];

            if (event.code == "Enter")
                return;
            if(this.searchTerm == '') 
                return;
            

            var filtered = this.$store.getters.requisitionProductItems.filter(item => {
                let item_data = '';
                if(this.sub_category_id != 0) {
                    if(item.sub_category_id == this.sub_category_id) {
                        item_data = item.product_name +' ('+ item.product_code + ')';
                    }
                }else{
                    item_data = item.product_name +' ('+ item.product_code + ')';
                }

                return item_data.toLowerCase().match(this.searchTerm.toLowerCase());

            });

            // this.isOpen = true
            this.searchProducts.push(...filtered);
            if(this.arrowCounter == -1) {
                return;
            }
            let match_data = this.searchProducts[this.arrowCounter].product_name +' ('+ this.searchProducts[this.arrowCounter].product_code +')';
            if(match_data != this.searchTerm) {
                this.arrowCounter = -1;
            }

        },

        selectProduct(item) { 
            // this.arrowCounter = -1;
            this.product_code = item.product_code;
            this.searchTerm = item.product_name +' ('+ item.product_code + ')';
            // this.getProductByCode();
        }, 

        setResult(item) { 
            // this.arrowCounter = -1;
            this.product_code = item.product_code;
            this.searchTerm = item.product_name +' ('+ item.product_code + ')';
            this.getProductByCode();
            this.searchProducts = [];
            this.category_id = 0;
            this.sub_category_id = 0;
            this.sub_categories = [];
        }, 

        // For Check Product List
        checkExistsProduct(array_data, product_code){
            let productExists = false;
            array_data.map((item) => {
                if(item.product_code == product_code) {
                    productExists = true;
                }
            });

            return productExists;
        },

        inputChange()
        {
            this.obj.total_quantity = this.totalQuantity;
            this.obj.total_value = this.totalValue;
            this.obj.total_amount = this.totalAmount;
        },

        removeProductListItem(row_id) 
        {
            
            if(this.product_items[row_id]) {
                this.product_items.splice(row_id, 1);
            }else{
                this.$toast.error("Oop's, something went wrong please try again!");
            }

        },
        
        onArrowDown: function(event) {
            if (this.searchProducts.length > 0) {
                this.arrowCounter = event.code == "ArrowDown" ? ++this.arrowCounter : --this.arrowCounter;
                if (this.arrowCounter >= this.searchProducts.length)
                this.arrowCounter = (this.arrowCounter) % this.searchProducts.length;
                else if (this.arrowCounter < 0)
                this.arrowCounter = this.searchProducts.length + this.arrowCounter;
                this.selectProduct(this.searchProducts[this.arrowCounter]);
            }
        },
        
        onArrowUp: function(event) {
            if (this.searchProducts.length > 0) {
                this.arrowCounter = event.code == "ArrowUp" ? --this.arrowCounter : ++this.arrowCounter;
                if (this.arrowCounter >= this.searchProducts.length)
                this.arrowCounter = (this.arrowCounter) % this.searchProducts.length;
                else if (this.arrowCounter < 0)
                this.arrowCounter = this.searchProducts.length + this.arrowCounter;
                this.selectProduct(this.searchProducts[this.arrowCounter]);
            }
        },

        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append("outlet_id", this.obj.outlet_id);
            formData.append("requisition_date", this.obj.requisition_date);
            formData.append("requisition_no", this.obj.requisition_no);
            formData.append("total_quantity", this.obj.total_quantity);
            formData.append("total_value", this.obj.total_value);
            formData.append("total_amount", this.obj.total_amount);
            formData.append("products", JSON.stringify(this.product_items));
                    
            var postEvent = axios.post(this.apiUrl+'/store_requisitions', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.resetForm();
                    this.$toast.success(res.data.message);
                    this.obj.requisition_date = new Date().toISOString().slice(0,10);
                    this.fetchStoreRequisitionNo();
                    // window.location.reload(); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },
        
        resetForm() {
            var self = this; //you need this because *this* will refer to Object.keys below`

            //Iterate through each object field, key is name of the object field`
            Object.keys(this.obj).forEach(function(key,index) {
                self.obj[key] = '';
            });

            this.product_items = [];
        },

        validation: function (...fiels){ 
            var obj = new Object(); 
            var validate = false;
            for (var k in fiels){     // Loop through the object  
                for (var j in this.form){  
                    if((j==fiels[k]) && (!this.form[j])) {  
                        obj[fiels[k]] = fiels[k].replace("_", " ")+' field is required';  // Delete obj[key]; 
                        this.errors = {...this.errors, ...obj};
                    }else{
                        validate = false;
                    }
                }              
            }  
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },
        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },

    beforeUpdate(){
        if(this.product_items.length == 0) {
            this.disabled = false;
        }else{
            this.disabled = true;
        }
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
        ...mapGetters([
            'requisitionProductItems'
        ]),  

        disabled: function(){
            if(this.product_items.length == 0) {
                return true;
                // this.disabled = true;
            }else{
                return false;
                // this.disabled = false;
            }
        },

        totalQuantity: function(){ 
            return this.product_items.reduce(function(total, item){
                
                let qty = item.req_qty;
                if(item.req_qty == '') {
                   qty = 0;
                }
                return total + parseFloat(qty); 
            },0);
        }, 
        
        totalValue: function(){
            return this.product_items.reduce(function(total, item){
                return total + (item.cost_price * item.qty); 
            },0); 
        },
        
        totalAmount: function() {
            return this.product_items.reduce(function(total, item){
                // let item_value = (item.cost_price * item.req_qty);
                //let item_discount = ((item_value * item.disc_percent) / 100);
                // return total + (item_value - item_discount); 
                return total + (item.cost_price * item.req_qty); 
            },0); 
        },

    }, 
    watch: {
    
    }, 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 1000px
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


.total_quantity {
    float: right;
    color: red;
}

.product_table {
    padding: 0;
    min-height: auto;
}

.product_table tbody td input {
    border-bottom: 1px solid #cecece;
}

#requisition_no {
    color: red;
}
div.buttons {
    margin-top: 30px;
}

div.buttons .btn-primary {
    margin-top: 0;
}

div.buttons .btn {
    margin-right: 5px;
}

div.buttons .btn:last-child {
    margin-right: 0;
}

/** Autocomplete */
.autocomplete {
  position: relative;
}

ul.autocomplete-results {
  width: 100% !important;
  margin-top: 1px !important;
  position: absolute;
  left: 0;
  z-index: 999;
}
.autocomplete-results {
  padding: 0;
  margin: 0;
  border: 1px solid #eeeeee;
  /* height: 120px; */
  overflow: auto;
  width: 100%;
}

.autocomplete-results li{
  list-style: none;
  text-align: left;
  padding: 4px 2px;
  cursor: pointer;
  /* background: red; */
}

.autocomplete-results li.is-active,
.autocomplete-results li:hover {
  background-color: #4AAE9B;
  color: white;
}
.autocomplete-results li:first-child {
  font-weight: 600;
  border-bottom: 1px solid #282828;
  margin-bottom: 3px;
}
.autocomplete-results li:first-child:hover {
  background-color: inherit;
  color: inherit;
}

.is-active {
  background-color: #dedede;
}

/** For Drawer */
.right-drawer {
  position: absolute;
  top: 53px;
  right: 0;
  width: 0; /* initially */
  height: 100vh;
  padding-left: 0; /*initially */
  padding-right: 0; /*initially */
  border-left: 1px solid whitesmoke;
  background: white;
  z-index: 200;
  transition: all 0.2s; /* for the animation */
}
.drawer-body {
    position: relative;
    padding: 0 5px 0 5px;
}
.product-content {
    height: 100vh;
    overflow: hidden;
}

.drawer-btn {
    position: absolute;
    left: -34px;
    top: 30vh;
    height: 15vh;
    width: 1.5vw;
    padding: 0;
    border-radius: 20px 0 0 20px;
}

.drawer-mask {
  position: absolute;
  left: 0;
  top: 0;
  width: 0; /* initially */
  height: 100vh;
  background: #000;
  opacity: 0.3;
  z-index: 199;
}
</style>