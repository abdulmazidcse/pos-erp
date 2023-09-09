<template>
    <transition  >
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Coupon </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Coupon List</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-primary float-right" @click="toggleModal" v-if="permission['coupons-create']">
                              Add New
                            </button> 
                        </div>
                    </div>
                </div>
            </div> 
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">   
                            <table class="table table-bordered table-sm ">
                                <thead class="tableFloatingHeaderOriginal">
                                    <tr class="border success item-head">
                                        <th width="10%">Coupon Name </th>
                                        <th width="5%">Code</th>
                                        <th width="10%">Discount amount</th>
                                        <th width="10%">Description</th>
                                        <th width="5%">Number of Uses</th>
                                        <th width="10%">Max uses</th>
                                        <th width="10%">Fixed Or Percent</th>
                                        <th width="10%">Start at</th> 
                                        <th width="10%">Expires at</th> 
                                        <th width="5%">status</th> 
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.name}} </td>
                                        <td>{{ item.code}} </td> 
                                        <td>{{ item.discount_amount}} </td> 
                                        <td>{{ item.description}} </td> 
                                        <td>{{ item.uses}} </td> 
                                        <td>{{ item.max_uses}} </td> 
                                        <td> 
                                            <span class="badge btn bg-warning" v-if="item.is_fixed==1">Fixed</span>  
                                            <span class="badge btn bg-success"  v-else>Percent</span> </td> 
                                        <td>{{ item.start_at}} </td> 
                                        <td>{{ item.expires_at}} </td> 
                                        <td>
                                            <span class="badge btn bg-success" v-if="item.status==1">Active</span> 
                                            <span class="badge btn bg-info" v-else>Inactive</span> 
                                        </td>  
                                        <td>
                                            <a href="#" @click="edit(item)" v-if="permission['coupons-edit']"><i class="fas fa-edit"></i> </a>
                                            <a href="#" @click="deleteItem(item)" v-if="permission['coupons-delete']"><i class="fas fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                        </div>
                    </div>
                </div>
            </div> 
            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header"> 
                        <h3>Coupon Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                        <div class="modal-body">  
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6  ">
                                            <div class="mb-3">
                                                <label for="name">Coupon Name *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Name" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.name[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-6 ">
                                            <div class="mb-3">
                                                <label for="name"> Code *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('code')" v-model="form.code" id="code" placeholder="Code" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.code">
                                                    {{errors.code[0]}}
                                                </div>
                                            </div>
                                        </div>   
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6  ">
                                            <div class="mb-3">
                                                <label for="name"> Discount amount *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('discount_amount')" v-model="form.discount_amount" id="discount_amount" placeholder="Discount amount" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.discount_amount">
                                                    {{errors.discount_amount[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6  ">
                                            <div class="mb-3">
                                                <label for="name"> Description</label>
                                                <input type="text" class="form-control " @keypress="onkeyPress('description')" v-model="form.description" id="description" placeholder="Description" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.description">
                                                    {{errors.description[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="col-md-6  ">
                                            <div class="mb-3">
                                                <label for="name"> Start at</label>
                                                <input type="date" class="form-control " @keypress="onkeyPress('start_at')" v-model="form.start_at"> 
                                                <div class="invalid-feedback" v-if="errors.start_at">
                                                    {{errors.start_at[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-6  ">
                                            <div class="mb-3">
                                                <label for="name"> Expires at {{form.expires_at}}</label>
                                                <input type="date" class="form-control " @keypress="onkeyPress('expires_at')" v-model="form.expires_at"> 
                                                <div class="invalid-feedback" v-if="errors.expires_at">
                                                    {{errors.expires_at[0]}}
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4  ">
                                            <div class="mb-3">
                                                <input type="checkbox" id="switch3" data-switch="success" v-model="form.is_fixed" false-value="0" true-value="1"/>
                                                <label for="switch3" data-on-label="Fixed" data-off-label="%"></label>
                                                <label for="name"> Fixed Or Percent</label>  
                                                <div class="invalid-feedback" v-if="errors.is_fixed">
                                                    {{errors.is_fixed[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-4  ">
                                            <div class="mb-3">
                                                <input type="checkbox" id="switch5" data-switch="success" v-model="form.status" false-value="0" true-value="1"/>
                                                <label for="switch5" data-on-label="Yes" data-off-label="No"></label>
                                                <label for="name"> Status</label>  
                                                <div class="invalid-feedback" v-if="errors.status">
                                                    {{errors.status[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary " :disabled="disabled">
                                <span v-show="isSubmit">
                                    <i class="fas fa-spinner fa-spin" ></i>
                                </span>{{btn}} 
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </transition>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import Modal from "../helper/Modal.vue";
import { ref, onMounted } from "vue";
import Form from "vform";
import axios from "axios";
export default {
    name: "PosLeftbar",
    components: {
        Modal
    },
    data() {
        return {
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            loading: true, 
            btn:'Create',
            items: [],
            companies: [],
            form: new Form({
                id: '',
                name: '',
                code: '',
                type:'voucher',
                is_fixed:1,
                discount_amount:'',
                status:1,
                start_at:'',
                expires_at:'',
                description:''
            }),
        }
    },
    created() {
        this.fetchData();
    },

    methods: {
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            console.log('this.modalActive', this.modalActive)
            if(!this.modalActive){
                this.editMode = false;
                this.btn='Create';
            } 
            this.errors = '';
            this.isSubmit = false;
            this.form.reset(); 
            console.log('then',this.isSubmit)
        },
        fetchData() { 
            axios.get(this.apiUrl+'/coupons', this.headerjson)
            .then((res) => { 
                this.items = res.data.data;
            })
            .finally((ress) => {
              this.loading = false; 
            });;
        }, 
        edit: function(item) {
            this.btn='Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item); 
        },
        submitForm: function(e) { 
            this.isSubmit = true;
            this.disabled = true; 
            if(this.editMode){ 
                var postEvent = axios.put(this.apiUrl+'/coupons/'+this.form.id, this.form, this.headerjson);
            }else{  
                var postEvent = axios.post(this.apiUrl+'/coupons', this.form, this.headerjson);
            }
            postEvent.then(res => {                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
                console.log(res.data)
            }).catch(err => {  
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            })
        },

        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }  
        },
        deleteItem: function(item) {
            console.log('item delete=>',item.id);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/coupons/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){ 
                            this.fetchData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message);
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors 
                        }
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },
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
}
</script>

<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 600px;
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