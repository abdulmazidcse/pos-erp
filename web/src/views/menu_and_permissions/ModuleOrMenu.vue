<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Permission</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Module/Menu List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add New
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Module name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Module Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="parent_id">Parent Module</label>
                                        <select class="form-control border" v-model="form.parent_id" @change="onkeyPress('parent_id')" id="parent_id">
                                            <option value="0">--- Select Module ---</option>
                                            <option v-for="(item, i) in parent_items" :key="i" :value="item.id">{{ item.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.parent_id">
                                            {{errors.parent_id[0]}}
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="icon_name">Icon Name </label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('icon_name')" v-model="form.icon_name" id="icon_name" placeholder="Menu Icon Name Here!">
                                        <div class="invalid-feedback" v-if="errors.icon_name">
                                            {{errors.icon_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="menu_order">Menu Order </label>
                                        <input type="number" class="form-control border " @keypress="onkeyPress('menu_order')" v-model="form.menu_order" id="menu_order" placeholder="Menu Order Here!">
                                        <div class="invalid-feedback" v-if="errors.menu_order">
                                            {{errors.menu_order[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_children" name="children_action" value="children" v-model="children_action" :checked="form.is_children" @change="onkeyPress('children_action'), childOrAction($event)">
                                            <label class="form-check-label" for="is_children">Has Children</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_children">
                                            {{errors.is_children[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_action_menu" name="children_action" value="action" v-model="children_action" :checked="form.is_action_menu" @change="onkeyPress('children_action'), childOrAction($event)">
                                            <label class="form-check-label" for="is_action_menu">Has Menu Action</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_action_menu">
                                            {{errors.is_action_menu[0]}}
                                        </div>
                                    </div>

                                    <div class="invalid-feedback" v-if="errors.children_action">
                                        {{ errors.children_action[0] }}
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- <div class="form-group col-md-4" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="is_children" v-model="form.is_children" true-value="1" false-value="0" @change="onkeyPress('is_children')">
                                            <label class="form-check-label" for="is_children">Has Children</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_children">
                                            {{errors.is_children[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="is_action_menu" v-model="form.is_action_menu" true-value="1" false-value="0" @change="onkeyPress('is_action_menu')">
                                            <label class="form-check-label" for="is_action_menu">Has Menu Action</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_action_menu">
                                            {{errors.is_action_menu[0]}}
                                        </div>
                                    </div> -->

                                    <div class="form-group col-md-12" style="margin-top: 10px;" v-if="form.is_action_menu">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="is_multiple_action" v-model="form.is_multiple_action" true-value="1" false-value="0" @change="onkeyPress('is_multiple_action')">
                                            <label class="form-check-label" for="is_multiple_action">Has Multiple Action</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_multiple_action">
                                            {{errors.is_multiple_action[0]}}
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

        <!-- Add Permissions Modal-->
        <!-- Modal -->
        <Modal @close="togglePermissionModal()" :modalActive="permissionModal">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="togglePermissionModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitPermissionForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_route_action" name="route_action" value="route_action" v-model="permission_obj.is_route_action" :checked="permission_obj.is_route_action" @change="routeAndAction($event)">
                                            <label class="form-check-label" for="is_route_action">Route and Action</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="is_action" name="route_action" value="action" v-model="permission_obj.is_route_action" :checked="!permission_obj.is_route_action" @change="routeAndAction($event)">
                                            <label class="form-check-label" for="is_action">Only Action</label>
                                        </div>
                                    </div>

                                    <div class="invalid-feedback" v-if="errors.is_route_action">
                                        {{ errors.is_route_action[0] }}
                                    </div>
                                </div>

                                <div class="row">
                                    <input type="hidden" v-model="permission_obj.module_id">
                                    <div class="form-group col-md-12">
                                        <label for="name">Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="permission_obj.name" id="name" placeholder="Permission Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div>

                                    <div v-if="permission_obj.is_route_action">
                                        <div class="form-group col-md-12">
                                            <label for="url_path">Url Path *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('url_path')" v-model="permission_obj.url_path" id="url_path" placeholder="Url Path" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.url_path">
                                                {{errors.url_path[0]}}
                                            </div>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="component_path">Component Path *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('component_path')" v-model="permission_obj.component_path" id="component_path" placeholder="Component Path" autocomplete="off"> 
                                            <div class="invalid-feedback" v-if="errors.component_path">
                                                {{errors.component_path[0]}}
                                            </div>
                                        </div>

                                    
                                        <div class="form-group col-md-6" style="margin-top: 10px;">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_nav" v-model="permission_obj.is_nav" true-value="1" false-value="0" @change="onkeyPress('is_nav')">
                                                <label class="form-check-label" for="is_nav">Is Nav</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_nav">
                                                {{errors.is_nav[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-md-6" style="margin-top: 10px;">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="is_index" v-model="form.is_index" true-value="1" false-value="0" @change="onkeyPress('is_index')">
                                            <label class="form-check-label" for="is_index">Is Index/List</label>
                                        </div>
                                        <div class="invalid-feedback" v-if="errors.is_index">
                                            {{errors.is_index[0]}}
                                        </div>
                                    </div> -->

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledPermission">
                            <span v-show="isPermissionSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>Submit 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>


        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100" v-if="!loading"> 
                            <thead class="tableFloatingHeaderOriginal">
                                <tr class="border success item-head">
                                    <th width="5%">SL </th>
                                    <th width="15%">Name </th>
                                    <th width="10%">Slug</th>
                                    <th width="15%">Parent Module</th>
                                    <th width="10%">Icon Name</th>
                                    <th width="5%">Order</th>
                                    <th width="5%">Has Children</th>
                                    <th width="5%">Has Menu Action</th>
                                    <th width="5%">Has Multiple Action</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.slug }}</td>
                                    <td>{{ item.parent_module_name }}</td> 
                                    <td>{{ item.icon_name }}</td> 
                                    <td>{{ item.menu_order }} </td> 
                                    <td>{{ (item.is_children == 1) ? 'Yes' : 'No'}} </td> 
                                    <td>{{ (item.is_action_menu == 1) ? 'Yes' : 'No' }} </td> 
                                    <td>{{ (item.is_multiple_action == 1) ? 'Yes' : 'No' }} </td> 
                                    <td class="actions_btn">
                                        <a href="#" v-if="(item.is_action_menu == 1 && item.is_multiple_action == 0 && item.total_actions == 0) || item.is_multiple_action == 1" @click="addPermissions(item)" title="Add Permissions"><i class="fas fa-plus"></i> </a>
                                        <a href="#" @click="edit(item)"><i class="fas fa-edit"></i> </a>
                                        <a href="#" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="tab-pane show active" v-if="loading">
                            <div class="row">
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                    <img src="../../assets/image/loading.gif" alt="Loading..." style="width: 130px;">
                                </div>
                                <div class="col-md-5"></div>
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'PosLeftbar',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            isPermissionSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            disabledPermission: false,
            modalActive:false,
            permissionModal:false,
            errors: {},
            btn:'Create',
            items: [],
            parent_items: [],
            children_action: '',
            form: new Form({
                id: '',
                name: '',
                icon_name: '',
                parent_id: 0,
                is_children: 0,
                is_action_menu: 0,
                is_multiple_action: 0,
                menu_order: 1,

            }),
            permission_obj: new Form({
                is_route_action: 1,
                module_id: '',
                name: '',
                url_path: '',
                component_path: '',
                is_nav: 0,
                // is_index: 0,
            })
        };
    },
    created() {
        this.fetchModuleData();
        this.fetchParentModuleData();
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

        togglePermissionModal: function() {
            this.permissionModal = !this.permissionModal;   
        
            this.errors = '';
            this.isPermissionSubmit = false;
            this.permission_obj.reset();
        },
        
        fetchModuleData() { 
            axios.get(this.apiUrl+'/permission_modules', this.headers)
            .then((res) => {
                this.items = res.data.data;
                //console.log('companies res',res.data.data);
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
                this.loading = false;
            });
        },
        
        fetchParentModuleData() { 
            axios.get(this.apiUrl+'/permission_modules/getParentsModule', this.headerjson) 
            .then((res) => {
                this.parent_items = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
            });
        },

        childOrAction: function(event) {
            let action_value = event.target.value;
            if(action_value == "children") {
                this.form.is_children = 1;
                this.form.is_action_menu = 0;
                this.form.is_multiple_action = 0;
            }
            else if(action_value == "action"){
                this.form.is_children = 0;
                this.form.is_action_menu = 1;
                this.form.is_multiple_action = 0;
            }else{
                this.form.is_children = 0;
                this.form.is_action_menu = 0;
                this.form.is_multiple_action = 0;
            }
        },

        routeAndAction: function(event) {
            let action_value = event.target.value;
            if(action_value == "route_action") {
                this.permission_obj.is_route_action = 1;
            }
            else if(action_value == "action"){
                this.permission_obj.is_route_action = 0;
            }else{
                this.permission_obj.is_route_action = 1;
            }
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
            formData.append('parent_id', this.form.parent_id);
            formData.append('icon_name', this.form.icon_name);
            formData.append('menu_order', this.form.menu_order);
            formData.append('is_children', this.form.is_children);
            formData.append('is_action_menu', this.form.is_action_menu);  
            formData.append('is_multiple_action', this.form.is_multiple_action);  
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/permission_modules/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/permission_modules', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchModuleData();
                    this.fetchParentModuleData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }
                console.log('then',this.isSubmit)
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
                console.log('catch',this.isSubmit)
            });
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
                    axios.delete(this.apiUrl+'/permission_modules/'+item.id, this.headerjson) 
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchModuleData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log("sfjsdhfjdk===", res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },

        addPermissions(item) {
            this.togglePermissionModal();
            this.permission_obj.module_id = item.id;
        },

        submitPermissionForm: function(e) { 

            this.isPermissionSubmit = true;
            this.disabledPermission = true;
            var module_id = this.permission_obj.module_id;
            const formData = new FormData();  
            formData.append('is_route_action', this.permission_obj.is_route_action);            
            formData.append('module_id', this.permission_obj.module_id);            
            formData.append('name', this.permission_obj.name);
            formData.append('url_path', this.permission_obj.url_path);
            formData.append('component_path', this.permission_obj.component_path);
            formData.append('is_nav', this.permission_obj.is_nav);
            // formData.append('is_index', this.permission_obj.is_index);
            
            var postEvent = axios.post(this.apiUrl+'/permissions', formData, this.headers);
                     
            postEvent.then(res => {
                this.isPermissionSubmit = false;
                this.disabledPermission = false;
                if(res.status == 200){
                    this.togglePermissionModal();
                    this.fetchModuleData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isPermissionSubmit = false; 
                this.disabledPermission = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },

    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
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

.actions_btn a {
    margin-right: 7px;
}
</style>