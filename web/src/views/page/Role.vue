<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <h4 style="margin: 0; padding: 8px 0 1.5rem 0;">Role List</h4>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal()" v-if="permission['role-create']">
                            <i class="mdi mdi-camera-timer me-1"></i> Create New
                        </button>  
                    </div> 
                </div>
            </div>
        </div>
        
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3>Role Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Role Name *</label>
                                    <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Role Name" autocomplete="off"> 
                                    <div class="invalid-feedback" v-if="errors.name">
                                        {{errors.name[0]}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control border " @keypress="onkeyPress('description')" v-model="form.description" id="description" placeholder="Description Here!" rows="3"></textarea>
                                    <!-- <input type="text" class="form-control border " @keypress="onkeyPress('unit_name')" v-model="form.unit_name" id="unit_name" placeholder="Unit Name" autocomplete="off">  -->
                                    <div class="invalid-feedback" v-if="errors.description">
                                        {{errors.description[0]}}
                                    </div>
                                </div>

                                <!-- <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="errors.status">
                                        {{errors.status[0]}}
                                    </div>
                                </div> -->
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

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    
                    <div class="card-body table-responsive">   
                        <table class="table table-centered table-bordered table-nowrap w-100"  v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th width="5%" class="text-center">SL </th>
                                    <th width="40%">Role Name </th>
                                    <th width="40%">Description</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, i) in items" :key="i">
                                    <td class="text-center">{{ i + 1 }} </td>
                                    <td>{{ item.name }} </td>
                                    <td>{{ item.description }} </td>
                                    <td>
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end"> 
                                                <a href="#" @click="changePermission(item.id)" title="Change Permission" class="text-success dropdown-item changePermission" v-if="permission['role-permission']">
                                                    <i class="mdi mdi-lock-open me-1"></i> Permission</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['role-edit']">
                                                <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> 
                                                <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['role-delete']" ><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
                                            </div>
                                        </div>  
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
    </div>
    </transition>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import Modal from "./../helper/Modal.vue";
import { ref, onMounted, getCurrentInstance } from "vue";
import Form from "vform";
import axios from "axios";
import RolePermission from "@/views/page/RolePermission.vue";  
export default {
    name: "PosLeftbar",
    components: {
        Modal,
        RolePermission
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode: false,
            disabled: false,
            modalActive: false,
            errors: {},
            btn:'Create',
            items: [],
            form: new Form({
                id: '',
                name: '',
                description: '',
                // status: 1,
            }),
        }
    },
    created() {
        this.fetchRoleData();
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
        },
        fetchRoleData() { 
            axios.get(this.apiUrl+'/roles', this.headerjson)
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
            formData.append('description', this.form.description);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/roles/'+this.form.id, formData, this.headers);
            }else{
                var postEvent = axios.post(this.apiUrl+'/roles', formData, this.headers);
            }           

            postEvent.then(res => {
                
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchRoleData();
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
                    axios.delete(this.apiUrl+'/roles/'+item.id, this.headers)
                    .then(res => {
                        if(res.status == 200){ 
                            this.fetchRoleData();
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
                }
            }); 
        },

        changePermission(role_id){
            this.$router.push({name: 'role-permission', params:{id:role_id}});
        }
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

.changePermission {
    margin-right: 5px;
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