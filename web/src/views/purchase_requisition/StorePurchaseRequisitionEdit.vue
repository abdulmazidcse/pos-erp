
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Requisition </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Store Purchase Requisition Edit</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <!-- <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                            Add New
                        </button>  -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="tab-pane show active" v-if="loading">
                        <div class="row"> 
                            <div class="col-md-5">  
                            </div>
                            <div class=" col-md-2"> 
                                <img src="../../assets/image/loading.gif" alt="Loading..." style="width:130px">
                            </div>
                            <div class="col-md-5">  
                            </div>
                        </div>
                    </div>

                    <div class="card-body" v-if="!loading">
                        <!-- <form @submit.prevent="submitForm()" enctype="multipart/form-data" > -->

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="requisition_no">Requisition No *</label>
                                        <input type="text" class="form-control border" id="requisition_no" @change="onkeyPress('requisition_no')" v-model="obj.requisition_no" readonly>
                                        <div class="invalid-feedback" v-if="errors.requisition_no">
                                            {{errors.requisition_no[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="requisition_date">Date *</label>
                                        <input type="date" class="form-control border" id="requisition_date" @change="onkeyPress('requisition_date')" v-model="obj.requisition_date" readonly>
                                        <div class="invalid-feedback" v-if="errors.requisition_date">
                                            {{errors.requisition_date[0]}}
                                        </div>
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
                                        <button type="submit" class="btn btn-primary " :disabled="disabled" @click.prevent="submitForm()">
                                            <span v-show="isSubmit">
                                                <i class="fas fa-spinner fa-spin" ></i>
                                            </span> UPDATE 
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
    name: 'Store Purchase Requisition Edit',
    components: {
        Modal
    },
    data() {
        return {
            requisition_id: this.$route.params.id,
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
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
        this.$store.dispatch('getRequisitionProductItems',this.apiUrl, this.headerjson);
        this.fetchStoreRequisition();
        this.fetchAllCategory();
    },
    methods: { 

        fetchStoreRequisition() {
            axios.get(this.apiUrl+'/store_requisitions/'+this.requisition_id+'/edit', this.headerjson)
            .then((res) => {
                this.obj.fill(res.data.data.requisition_data);
                this.product_items = res.data.data.requisition_products;
            })
            .catch((err) => {

            })
            .finally((fres) => {
                this.loading = false;
            });
        },

        fetchAllCategory(){
            axios.get(this.apiUrl+'/getProductCategory', this.headerjson)
            .then((res) => {
                console.log("response_data===", res.data);
                this.parent_categories = res.data.data.parent_category;
                this.parent_sub_categories = res.data.data.sub_category;
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
            // alert(event.keyCode);
            axios.get(this.apiUrl+'/products', this.headerjson)
            .then((res) => {
                console.log(res.data.data);
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
            if(!this.checkExistsProduct(this.product_items, this.product_code)) {
                axios.post(this.apiUrl+'/store_requisitions/getProduct', data, this.headerjson)
                .then((res) => {
                    console.log(res);
                    this.product_code = '';
                    this.searchTerm = '';
                    this.arrowCounter = -1;
                    this.product_items.push(res.data.data);

                })
                .catch((err) => {
                    this.$toast.error(err.response.data.message);
                });
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
                //let item_data = item.product_name +' ('+ item.product_code + ')';
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
            console.log("test-arrow-down===", event)
            // let product_data = this.$store.getters.requisitionProductItems;
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
            console.log("test-arrow-up===", event)
            // let product_data = this.$store.getters.requisitionProductItems;
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
            formData.append("_method", "PUT");

            var postEvent = axios.post(this.apiUrl+'/store_requisitions/'+this.requisition_id, formData);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.resetForm();
                    this.$toast.success(res.data.message); 
                    this.$router.push('/store-requisition-list');
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
            console.log('Reseting the form');
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
        

        // ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
        
        ...mapGetters([
            'requisitionProductItems'
        ]),

        disabled: function(){
            if(this.product_items.length == 0) {
                return true;
            }else{
                return false;
            }
        },

        totalQuantity: function(){ 
            console.log("test quantity", this.product_items);
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
        
        totalAmount: function(){
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
</style>