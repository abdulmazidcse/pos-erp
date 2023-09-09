<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Department </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Department List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()" v-if="permission['department-create']">
                            Add New
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">   
                        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100"  v-if="!loading">
                            <thead class="tableFloatingHeaderOriginal">
                                <tr class="border success item-head">
                                    <th width="20%">Department Name </th>
                                    <th width="20%">Company Name</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, i) in items" :key="i">
                                    <td>{{ item.name}} </td>
                                    <td>{{ item.company_name }} </td>
                                    <td>
                                        <a href="#" @click="edit(item)" v-if="['department-edit']"><i class="fas fa-edit"></i> </a>
                                        <a href="#" @click="deleteItem(item)" v-if="['department-edit']"><i class="fas fa-trash"></i> </a>
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
                    <h3>Department Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group  ">
                                    <label for="name">Department Name *</label>
                                    <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Department name" autocomplete="off"> 
                                    <div class="invalid-feedback" v-if="errors.name">
                                        {{errors.name[0]}}
                                    </div>
                                </div>

                                <div class="form-group  ">
                                    <label for="name">Company </label>
                                    <select class="form-control border" v-model="form.company_id" @change="onkeyPress('company_id')" id="company_id">
                                        <option value="0">Select Company</option>
                                        <option v-for="(company, index) in companies" :value="company.id" :key="index">{{ company.name }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.company_id">
                                        {{errors.company_id[0]}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="dstatus">Status</label>
                                    <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="dstatus">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.status">
                                        {{errors.status[0]}}
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
import Modal from "./../helper/Modal.vue";
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
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            btn:'Create',
            items: [],
            companies: [],
            form: new Form({
                id: '',
                name: '',
                company_id: 0,
                status: 1,
            }),
        }
    },
    created() {
        this.fetchDepartmentData();
        this.fetchCompanyData();
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
        fetchDepartmentData() { 
            axios.get(this.apiUrl+'/departments', this.headerjson) 
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
            .finally((fres) => {
                this.loading = false;
            }); 
        },

        fetchCompanyData() { 
            axios.get(this.apiUrl+'/companies', this.headerjson)  
            .then((res) => { 
                this.companies = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
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
            formData.append('company_id', this.form.company_id);
            formData.append('status', this.form.status); 
            this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/departments/'+this.form.id, formData, this.headers);
            }else{  
                var postEvent = axios.post(this.apiUrl+'/departments', formData, this.headers);
            }          

            postEvent.then(res => {                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchDepartmentData();
                    this.$toast.success(res.data.message); 
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
                    axios.delete(this.apiUrl+'/departments/'+item.id, this.headerjson)
                     .then(res => {
                        if(res.status == 200){ 
                            this.fetchDepartmentData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        } 
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