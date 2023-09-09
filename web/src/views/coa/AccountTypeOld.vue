<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Account Type </a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;" @click="createAccountTypeModal()">
                            <i class="mdi mdi-plus-outline"></i> Create New Type
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal @close="createAccountTypeModal()" :modalActive="modalAccTypeActive">
            <div class="modal-content scrollbar-width-thin account_groups">
                <div class="modal-header"> 
                    <button @click="createAccountTypeModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountTypeForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="class_id">Account Group *</label>
                                        <select class="form-control border " @change="onChangeAccountGroup($event.target.value), onkeyPress('class_id')" v-model="form.class_id" id="class_id"> 
                                            <option value="">--- Select Account Group ---</option>
                                            <option v-for="(group, i) in groups" :key="i" :value="group.id">{{ '['+group.code +'] '+group.name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.class_id">
                                            {{errors.class_id[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="parent_type_id">Parent Type </label>
                                        <select class="form-control border " @change="onChangeAccountType($event.target.value), onkeyPress('parent_type_id')" v-model="form.parent_type_id" id="parent_type_id"> 
                                            <option value="0">--- Select Parent Type ---</option>
                                            <option v-for="(type, i) in parent_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.parent_type_id">
                                            {{errors.parent_type_id[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_code">Type Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_code')" v-model="form.type_code" id="type_code" placeholder="Enter Type Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.type_code">
                                            {{errors.type_code[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_name">Type Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_name')" v-model="form.type_name" id="type_name" placeholder="Enter Type Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.type_name">
                                            {{errors.type_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_status">Status *</label>
                                        <select class="form-control border " @change="onkeyPress('status')" v-model="form.status" id="type_status">
                                            <option value="1">Active</option>
                                            <option value="0">InActive</option>
                                        </select> 
                                        <div class="invalid-feedback" v-if="errors.type_status">
                                            {{errors.type_status[0]}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit">
                            <span v-show="isTypeSubmit">
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
                                    <th width="15%" class="text-center">Type Name </th>
                                    <th width="10%" class="text-center">Type Code </th>
                                    <th width="15%" class="text-center">Parent Type </th>
                                    <th width="15%" class="text-center">Group/Class </th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="5%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody v-if="items.length > 0">
                                <tr class="border" v-for="(item, index) in items" :key="index">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>{{ item.type_name }}</td>
                                    <td>{{ item.type_code }}</td>
                                    <td>{{ (item.type_parents) ? item.type_parents.type_name : 'N/A' }}</td>
                                    <td>{{ (item.account_classes) ? item.account_classes.name : 'N/A' }}</td>
                                    <td class="text-center" v-if="item.status == '1'"><span class="badge bg-success">Active</span></td>
                                    <td class="text-center" v-else><span class="badge bg-danger">Inactive</span></td>
                                    <td class="actions_btn">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end"> 
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)"><i class="mdi mdi-circle-edit-outline me-1"></i> Edit</a>
                                                <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i> Remove</a>
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
            showModal: false,
            editMode:false,
            disabledTypeSubmit: false,
            modalAccTypeActive:false,
            isTypeSubmit:false,
            errors: {},
            btn:'Create',
            items: [],
            groups: [],
            parent_types: [],
            form: new Form({
                id: '',
                class_id: '',
                parent_type_id: '',
                type_code: '',
                type_name: '',
                status: 1
            }), 
        };
    },
    created() {
        this.fetchAccountType();
        this.fetchGroupData();
        this.fetchParentType();
    },
    methods: { 
        createAccountTypeModal: function() {
            this.modalAccTypeActive = !this.modalAccTypeActive;
            // setTimeout(()=> {
            //     this.modalAccGroupActive = !this.modalAccGroupActive; 
            // }, 2000);  
            if(!this.modalAccTypeActive) {
                this.editMode = false;
                this.btn='Create';
                this.form.reset(); 
            }
            this.errors = '';
            this.isTypeSubmit = false;
        },
        
        fetchAccountType() { 
            axios.get(this.apiUrl+'/account_types', this.headerjson)
            .then((res) => {
                this.items = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchGroupData() {
            axios.get(this.apiUrl+'/account_classes', this.headerjson)
            .then((res) => {
                this.groups = res.data.data.account_classes;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchParentType() {
            // axios.get(this.apiUrl+'/account_types/getParentTypes', this.headerjson)
            axios.get(this.apiUrl+'/account_types/getAccountTypeList', this.headerjson)
            .then((res) => {
                this.parent_types = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchTypeCode(reference_id='', type='') {
            var formData = new FormData();
            formData.append("reference_id", reference_id);
            formData.append("reference_type", type);
            axios.post(this.apiUrl+'/account_types/getTypesCode', formData, this.headers)
            .then((res) => {
                this.form.type_code = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        onChangeAccountGroup(value) {
            var group_id = value;
            if(group_id != '' || group_id != 0) {
                this.fetchTypeCode(group_id, "group");
            }
        },
        
        onChangeAccountType(value) {
            var type_id = value;
            if(type_id != '' || type_id != 0) {
                this.fetchTypeCode(type_id, "type");
            }
        },

        edit: function(item) { 
            this.btn='Update';
            this.editMode = true;
            this.createAccountTypeModal();
            this.form.fill(item);  
        },

        submitAccountTypeForm: function(e) { 

            this.isTypeSubmit = true;
            this.disabledTypeSubmit = true;
            const formData = new FormData();           
            formData.append('class_id', this.form.class_id);
            formData.append('parent_type_id', this.form.parent_type_id);
            formData.append('type_code', this.form.type_code);
            formData.append('type_name', this.form.type_name);
            formData.append('status', this.form.status);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/account_types/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/account_types', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isTypeSubmit = false;
                this.disabledTypeSubmit = false;
                if(res.status == 200){
                    this.createAccountTypeModal();
                    this.fetchAccountType();
                    this.fetchGroupData();
                    this.fetchParentType();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isTypeSubmit = false; 
                this.disabledTypeSubmit = false;
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
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    axios.delete(this.apiUrl+'/account_types/'+item.id, this.headerjson) 
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchAccountType();
                            this.fetchGroupData();
                            this.fetchParentType();
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

        addNewLedger(item) {
            this.createLedgerModal();
            this.ledger_form.group_id = item.id;
        },

        submitLedgerForm: function(e) { 

            this.isLedgerSubmit = true;
            this.disabledLedgerSubmit = true;

            var parent_id = this.ledger_form.parent_id != '' ? this.ledger_form.parent_id : 0;
            var op_balance = this.ledger_form.opening_balance != '' ? this.ledger_form.opening_balance : 0;

            console.log("opening_balance", op_balance);

            const formData = new FormData();             
            formData.append('ledger_code', this.ledger_form.ledger_code);            
            formData.append('group_id', this.ledger_form.group_id);            
            formData.append('parent_id', parent_id);
            formData.append('ledger_name', this.ledger_form.ledger_name);
            formData.append('ledger_type', this.ledger_form.ledger_type);
            formData.append('opening_balance', op_balance);
            
            var postEvent = axios.post(this.apiUrl+'/account_ledgers', formData, this.headers);
                     
            postEvent.then(res => {
                this.isLedgerSubmit = false;
                this.disabledLedgerSubmit = false;
                if(res.status == 200){
                    this.createLedgerModal();
                    this.fetchCOAData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isLedgerSubmit = false;
                this.disabledLedgerSubmit = false;
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