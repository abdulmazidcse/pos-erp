<template>
    <transition  >
        <div class="container-fluid  ">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Size </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Size List</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-primary float-right" @click="toggleModal" v-if="permission['sizes-create']">
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
                                        <th width="20%">Size Name </th>
                                        <th width="20%">Value</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.name}} </td>
                                        <td>{{ item.value}} </td> 
                                        <td>
                                            <a href="#" @click="edit(item)" v-if="permission['sizes-edit']"><i class="fas fa-edit"></i> </a>
                                            <a href="#" @click="deleteItem(item)" v-if="permission['sizes-delete']"><i class="fas fa-trash"></i> </a>
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
                        <h3>Size Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group  ">
                                        <div class="mb-3">
                                            <label for="name">Size Name *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="name" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{errors.name[0]}}
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="form-group  ">
                                        <div class="mb-3">
                                            <label for="value">Size Value *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('value')" v-model="form.value" id="value" placeholder="Value" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.value">
                                                {{errors.value[0]}}
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
            btn:'Create',
            loading: true,
            items: [],
            companies: [],
            form: new Form({
                id: '',
                name: '',
                value: 1,
            }),
        }
    },
    created() {
        this.fetchData();
    },

    methods: {
        toggleModal: function() {
            this.modalActive = !this.modalActive;
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
            axios.get(this.apiUrl+'/sizes', this.headerjson)
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
            const formData = new FormData();  
            formData.append('name', this.form.name);
            formData.append('value', this.form.value); 
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/sizes/'+this.form.id, formData,  this.headers);
            }else{  
                var postEvent = axios.post(this.apiUrl+'/sizes', formData, this.headers);
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
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/sizes/'+item.id, this.headers).then(res => {
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
    border: none !important;
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