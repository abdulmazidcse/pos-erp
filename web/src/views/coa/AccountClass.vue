<template>
    <transition>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Account Group</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Group List </a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right">  
                        <button type="button" class="btn-sm btn btn-outline-success float-right" @click="createAccountGroupModal()"  >
                            <i class="mdi mdi-camera-timer me-1"></i> Create New Group
                        </button> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12"> 
                <div class="col-md-10">
                    <div class="row">  
                        <div class="col-md-6">
                            <div class="">
                                <label for="outlet_id"> Company </label>
                                <!--  -->
                                <select class="form-control" @change="fetchGroupData($event.target.value)">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="createAccountGroupModal()" :modalActive="modalAccGroupActive">
            <div class="modal-content scrollbar-width-thin account_groups"> 
                <div class="modal-header"> 
                    <h3>{{ btn }} Account Class</h3>
                    <button @click="createAccountGroupModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountGroupForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="">
                                            <label for="outlet_id"> Company </label>
                                            <select v-model="form.company_id"  class="form-control">
                                                <option value="">--- Select Company ---</option>
                                                <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                            </select>
                                        </div>
                                    </div> 
                                    
                                    <div class="form-group col-md-12">
                                        <label for="code">Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('code')" v-model="form.code" id="code" placeholder="Enter Group Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.code">
                                            {{errors.code[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="name">Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Enter Group Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledGroupSubmit">
                            <span v-show="isGroupSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{ btn }} 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-centered table-nowrap w-100" v-if="!loading">
                            <thead class="table-light">
                                <tr class="border success item-head">
                                    <th width="5%" class="text-center">SL </th>
                                    <th width="30%" class="text-center">Name </th>
                                    <th width="20%" class="text-center">Code </th>
                                    <th width="5%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td class="text-center">{{ item.name }}</td>
                                    <td class="text-center">{{ item.code }}</td>
                                    <td class="actions_btn">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                <!-- <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a> -->
                                            </div>
                                        </div> 
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
import Modal from "./../helper/Modal"; 
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
            showModal: false,
            editMode:false,
            disabled: false,
            disabledGroupSubmit: false,
            disabledLedgerSubmit: false,
            modalAccGroupActive:false,
            isGroupSubmit:false,
            modalAccLedgerActive:false,
            isLedgerSubmit:false,
            errors: {},
            btn:'Create',
            items: [],
            parent_items: [],
            companies: [],
            form: new Form({
                id: '',
                code: '',
                name: '',
                company_id:''

            }),
        };
    },
    created() {
        // this.fetchGroupData();
        this.fetchCompanies();
    },
    methods: { 
        createAccountGroupModal: function() {
            this.modalAccGroupActive = !this.modalAccGroupActive; 
            if(!this.modalAccGroupActive){
                this.editMode = false;
                this.btn='Create';
                this.form.reset(); 
            }else{
                if(!this.editMode) {
                    this.fetchGroupCode();
                }
            } 
            this.errors = '';
            this.isGroupSubmit = false;
        },
        
        fetchGroupData(selectedId) {  
            this.loading = true;
            axios.get(this.apiUrl+'/account_classes?company_id='+selectedId, this.headerjson)
            .then((res) => {
                this.items = res.data.data.account_classes;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },
        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => { 
                this.companies = res.data.data;
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchGroupCode(prefix = '') {
            var formData = new FormData();
            formData.append("prefix", prefix);
            axios.post(this.apiUrl+'/account_classes/getAccountClassCode', formData, this.headerjson) 
                .then((res) => {
                    this.form.code = res.data.data.group_code;
                })
                .catch((err) => { 
                    this.$toast.error(err.response.data.message);
                }).finally((ress) => {
            });
        },

        edit: function(item) { 
            this.btn='Update';
            this.editMode = true;
            this.createAccountGroupModal();
            this.form.fill(item);  
        },

        submitAccountGroupForm: function(e) { 

            this.isGroupSubmit = true;
            this.disabledGroupSubmit = true;
            const formData = new FormData();           
            formData.append('code', this.form.code);
            formData.append('name', this.form.name);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/account_classes/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/account_classes', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isGroupSubmit = false;
                this.disabledGroupSubmit = false;
                if(res.status == 200){
                    this.createAccountGroupModal();
                    this.fetchGroupData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isGroupSubmit = false; 
                this.disabledGroupSubmit = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
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
            console.log('item deleyt=>',item.id);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/account_classes/'+item.id, this.headerjson) 
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchGroupData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
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

.actions_btn a {
    margin-right: 7px;
}
</style>