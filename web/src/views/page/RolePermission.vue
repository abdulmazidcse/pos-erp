<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Role </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Role Permissions</a></li>
                            
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
                    <div class="card-header">
                        <h1><i class="fas fa-folder"></i> Role Permission ({{ this.role.name }})</h1>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submitForm()">
                            <table class="table table-bordered table-sm " v-if="!loading">
                                <thead class="tableFloatingHeaderOriginal">
                                    <tr class="border success item-head">
                                        <th width="15%" rowspan="2" class="moduleHead">Module Name </th>
                                        <th width="15%" rowspan="2" class="moduleHead">Sub Module Name </th>
                                        <th width="85%" colspan="6">Permissions <br><input type="checkbox" class="all_permission" :checked="isCheckAll" @change="checkedAll($event.target.checked)"> All</th>
                                    </tr>
                                    <tr class="border success item-head"> 
                                    </tr>
                                </thead>
                                <tbody v-for="(parent_module, i) in menu_and_permissions" :key="i">
                                    <!-- Main with Sub-Module Permissions -->
                                    <tr class="border" 
                                        v-for="(sub_module, iSub) in parent_module.sub_modules" 
                                        :key="iSub"
                                        v-if="parent_module.sub_modules.length > 0">

                                        <td style="text-align: left"
                                            v-if="iSub === 0"  
                                            :rowspan="(parent_module.sub_modules.length > 0) ? (parent_module.sub_modules.length) : ''"
                                        >   
                                            <input type="checkbox" :id="'module_id_'+ i" :value="parent_module.id" v-model="form.module_permissions" @change="checkModule($event)">
                                            {{ parent_module.name }} 
                                        </td>

                                        <td>
                                            <input type="checkbox" :id="'sub_module_id_'+ iSub" :value="sub_module.id" :refs="parent_module.id" v-model="form.sub_module_permissions" @change="checkSubModule($event)">
                                            {{ sub_module.name }}
                                        </td> 
                                        <td>
                                            <ul class="permission_list" v-if="sub_module.actions.length > 0">
                                                <li v-for="(permission, j) in sub_module.actions" :key="j">
                                                    <input type="checkbox" :id="'permission_id'+ j" class="checkbox" :value="permission.id" :refs="sub_module.id" :parent_id="parent_module.id" child_type="sub_module"  v-model="form.role_permissions" @change="checkSinglePermission($event)"> 
                                                    {{ permission.name }}
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>  
                                    <!-- Main Module Permissions -->
                                    <tr class="border" v-else>
                                        <td style="text-align: left" >   
                                            <input type="checkbox" :id="'module_id_'+ i" :value="parent_module.id" v-model="form.module_permissions" @change="checkModule($event)">
                                            {{ parent_module.name }}  
                                        </td> 
                                        <td> N/A </td> 
                                        <td>
                                            <ul class="permission_list" v-if="parent_module.actions.length > 0">
                                                <li v-for="(permission, j) in parent_module.actions" :key="j">
                                                    <input type="checkbox" :id="'permission_id'+ j" class="checkbox" :value="permission.id" :refs="parent_module.id" :parent_id="parent_module.id" child_type="module" v-model="form.role_permissions" @change="checkSinglePermission($event)"> 
                                                    {{ permission.name }}
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>  
                                </tbody>


                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align: center">
                                            <button type="submit" class="btn btn-primary " :disabled="disabled">
                                                <span v-show="isSubmit">
                                                    <i class="fas fa-spinner fa-spin" ></i>
                                                </span>{{btn}} 
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
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

                        </form>
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
import { ref, onMounted, getCurrentInstance} from "vue";
import Form from "vform";
import axios from "axios";
export default {
    name: "PosLeftbar",
    components: {
        
    },
    data() {
        return {
            loading: true,
            role_id: this.$route.params.id,
            isSubmit: false,
            disabled: false,
            errors: {},
            btn:'Update',
            role: '',
            sub_modules: [],
            permissions: [],
            menu_and_permissions: [],
            form:{
                role_permissions:[],
                module_permissions: [],
                sub_module_permissions: [],
            },
            isCheckAll: false,
        }
    },

    created() {
        this.fetchRoleAndPermissionData();
    },
    

    methods: {

        fetchRoleAndPermissionData() {
            axios.get(this.apiUrl+'/roles/'+this.role_id+'/permission', this.headerjson)
            .then((res) => {
                this.role = res.data.data.role;
                this.menu_and_permissions = res.data.data.menu_and_permissions;
                this.form.role_permissions = res.data.data.role.role_permissions;
                this.form.module_permissions = res.data.data.module_permissions;
                this.form.sub_module_permissions = res.data.data.sub_module_permissions;
                this.sub_modules = res.data.data.sub_modules;
                this.permissions    = res.data.data.permissions;

                if(this.form.module_permissions.length == this.menu_and_permissions.length) {
                    this.isCheckAll = true;
                }else{
                    this.isCheckAll = false;
                }
            })
            .catch()
            .finally((fres) => {
                this.loading = false;
            });
        },

        checkedAll(checked){
            if(checked){ 
                this.isCheckAll = true;
                for (let i = 0; i < this.menu_and_permissions.length; i++) { 
                    var module_permission_id = parseInt(this.menu_and_permissions[i].id);
                    const m_index = this.form.module_permissions.indexOf(module_permission_id);
                    if(m_index == -1) {
                        this.form.module_permissions.push(module_permission_id);
                    }

                    for(let s = 0; s < this.menu_and_permissions[i].sub_modules.length; s++){
                        var sub_module_id = parseInt(this.menu_and_permissions[i].sub_modules[s].id);
                        const index = this.form.sub_module_permissions.indexOf(sub_module_id)
                        if(index == -1) {
                            this.form.sub_module_permissions.push(sub_module_id);
                        }

                        for(let sa = 0; sa < this.menu_and_permissions[i].sub_modules[s].actions.length; sa++){
                            var permission_id = parseInt(this.menu_and_permissions[i].sub_modules[s].actions[sa].id);
                            const index = this.form.role_permissions.indexOf(permission_id)
                            if(index == -1) {
                                this.form.role_permissions.push(permission_id)
                            }
                        }
                    }

                    for(let j = 0; j < this.menu_and_permissions[i].actions.length; j++){
                        var mpermission_id = parseInt(this.menu_and_permissions[i].actions[j].id);
                        const index = this.form.role_permissions.indexOf(mpermission_id);
                        if(index == -1) {
                            this.form.role_permissions.push(mpermission_id);
                        }
                    }
                }   

            }else{
                this.isCheckAll = false;
                this.form.role_permissions = [];
                this.form.module_permissions = [];                    
                this.form.sub_module_permissions = [];                    
            }
            
        },

        checkModule(event) {
            if(event.target._modelValue.length == this.menu_and_permissions.length) {
                this.isCheckAll = true;
            }else{
                this.isCheckAll = false;
            }
            let checked = event.target.checked;
            let j;
            var s;
            var sa;
            if(checked) {
                if(event.target.value in this.sub_modules) {
                    

                    for(s=0; s<this.sub_modules[event.target.value].length; s++) {
                        var csub_module_id = parseInt(this.sub_modules[event.target.value][s]);
                        const index = this.form.sub_module_permissions.indexOf(csub_module_id);
                        if(index == -1) {
                            this.form.sub_module_permissions.push(csub_module_id);
                        }

                        if(this.sub_modules[event.target.value][s] in this.permissions) {
                            for(sa = 0; sa < this.permissions[this.sub_modules[event.target.value][s]].length; sa++) {
                                var smpermission_id = parseInt(this.permissions[this.sub_modules[event.target.value][s]][sa].id);
                                const index = this.form.role_permissions.indexOf(smpermission_id);
                                if(index == -1) {
                                    this.form.role_permissions.push(smpermission_id);
                                }
                            }
                        }
                    }

                }

                if(event.target.value in this.permissions) {
                    for(j = 0; j < this.permissions[event.target.value].length; j++) {
                        var mpermission_id = parseInt(this.permissions[event.target.value][j].id);
                        const index = this.form.role_permissions.indexOf(mpermission_id);
                        if(index == -1) {
                            this.form.role_permissions.push(mpermission_id);
                        }
                    }
                }
            }else{
                if(event.target.value in this.sub_modules) {

                    for(s=0; s<this.sub_modules[event.target.value].length; s++) {
                        var usub_module_id = parseInt(this.sub_modules[event.target.value][s]);
                        const index = this.form.sub_module_permissions.indexOf(usub_module_id);
                        if(index > -1) {
                            this.form.sub_module_permissions.splice(index, 1);
                        }

                        if(this.sub_modules[event.target.value][s] in this.permissions) {
                            for(sa = 0; sa < this.permissions[this.sub_modules[event.target.value][s]].length; sa++) {
                                var sm_permission_id = parseInt(this.permissions[this.sub_modules[event.target.value][s]][sa].id); 
                                const index = this.form.role_permissions.indexOf(sm_permission_id);
                                if(index > -1) {
                                    this.form.role_permissions.splice(index, 1);
                                }
                            }
                        }
                    }


                }

                if(event.target.value in this.permissions) {
                    for(j = 0; j < this.permissions[event.target.value].length; j++) {
                        var umpermission_id  = parseInt(this.permissions[event.target.value][j].id);
                        const index = this.form.role_permissions.indexOf(umpermission_id);
                        if(index > -1) {
                            this.form.role_permissions.splice(index, 1);
                        }
                    }
                }
            }
        },

        checkSubModule(event) {
            let checked = event.target.checked;
            let module_id = parseInt(event.target.getAttribute('refs'));
            let j;
            let s;
            if(checked) {
                var sub_module_count = 0;
                for(s=0; s<this.sub_modules[module_id].length; s++) {
                    var sub_module_id = parseInt(this.sub_modules[module_id][s]);
                    const index = this.form.sub_module_permissions.indexOf(sub_module_id);
                    if(index == -1) {
                        sub_module_count++;
                    }
                }
                if(sub_module_count == 0) {
                    this.form.module_permissions.push(module_id);
                }

                for(j = 0; j < this.permissions[event.target.value].length; j++) {
                    var smpermission_id = parseInt(this.permissions[event.target.value][j].id);
                    const index = this.form.role_permissions.indexOf(smpermission_id);
                    if(index == -1) {
                        this.form.role_permissions.push(smpermission_id);
                    }
                }
            }else{
                for(j = 0; j < this.permissions[event.target.value].length; j++) {
                    var usmpermission_id = parseInt(this.permissions[event.target.value][j].id);
                    const index = this.form.role_permissions.indexOf(usmpermission_id);
                    if(index > -1) {
                        this.form.role_permissions.splice(index, 1);
                    }
                }

                const sindex = this.form.sub_module_permissions.indexOf(parseInt(event.target.value));
                if(sindex > -1) {
                    this.form.sub_module_permissions.splice(sindex, 1);
                }

                const mindex = this.form.module_permissions.indexOf(module_id);
                if(mindex > -1) {
                    this.form.module_permissions.splice(mindex, 1);
                }
                
            }


            if(this.form.module_permissions.length == this.menu_and_permissions.length) {
                this.isCheckAll = true;
            }else{
                this.isCheckAll = false;
            }

        },

        checkSinglePermission(event) {

            let checked = event.target.checked;
            let module_id   = parseInt(event.target.getAttribute("refs"));
            let type = event.target.getAttribute("child_type");
            let parent_module_id = parseInt(event.target.getAttribute("parent_id"));

            let mod_permissions = this.permissions[module_id];
            let checked_array = [];
            for(let i = 0; i < mod_permissions.length; i++) {

                const index = this.form.role_permissions.indexOf(mod_permissions[i].id);
                if(index > -1) {
                    checked_array.push(mod_permissions[i].id);
                }
            }

            if(checked) {
                if(type =="sub_module") {
                    if(mod_permissions.length == checked_array.length) {
                        const m_index = this.form.sub_module_permissions.indexOf(module_id);
                        if(m_index == -1) {
                            this.form.sub_module_permissions.push(module_id);
                        }
                    }

                    var sub_module_count = 0;
                    for(let s=0; s<this.sub_modules[parent_module_id].length; s++) {
                        var sub_module_id = parseInt(this.sub_modules[parent_module_id][s]);
                        const index = this.form.sub_module_permissions.indexOf(sub_module_id);
                        if(index == -1) {
                            sub_module_count++;
                        }
                    }

                    if(sub_module_count == 0) {
                        this.form.module_permissions.push(parent_module_id);
                    }

                }else{
                    if(mod_permissions.length == checked_array.length) {
                        const m_index = this.form.module_permissions.indexOf(module_id);
                        if(m_index == -1) {
                            this.form.module_permissions.push(parent_module_id);
                        }
                    }
                }

            }else{
                if(type == "sub_module") {
                    const m_index = this.form.module_permissions.indexOf(parent_module_id);
                    if(m_index > -1) {
                        this.form.module_permissions.splice(m_index, 1);
                    }

                    const s_index = this.form.sub_module_permissions.indexOf(module_id);
                    if(s_index > -1) {
                        this.form.sub_module_permissions.splice(s_index, 1);
                    }
                }else{
                    const m_index = this.form.module_permissions.indexOf(module_id);
                    if(m_index > -1) {
                        this.form.module_permissions.splice(m_index, 1);
                    }
                }
            }
            

            if(this.form.module_permissions.length == this.menu_and_permissions.length) {
                this.isCheckAll = true;
            }else{
                this.isCheckAll = false;
            }

        },


        // edit: function(item) {
        //     this.btn='Update';
        //     this.editMode = true;
        //     this.toggleModal();
        //     this.form.fill(item); 
        // },
        submitForm: function(e) {            
            this.isSubmit = true;
            this.disabled = true;
            var api_url = this.apiUrl;
            var auth_headers = this.headerjson;
            
            var postEvent = axios.post(this.apiUrl+'/roles/'+this.role_id+'/permission', this.form, this.headerjson);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.$toast.success(res.data.message); 
                    this.$store.dispatch('getUserMenuAndPermissions', {api_url, auth_headers});
                    // this.$router.push('/role');
                    // this.$router.go(-1);
                    //window.location.href = '/role';
                }else{
                    this.$toast.error(res.data.message);
                }
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false;
                console.log(err.response.data.message);
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
    computed: {},
    ...mapGetters[
        'dataCheck'
    ],
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

.moduleHead {
    vertical-align: middle;
}

.permission_list {
    list-style: none;
}

.permission_list li {
    display: inline-block;
    margin-right: 10px; 
}
</style>