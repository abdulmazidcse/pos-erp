<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Warehouse </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Warehouse List</a></li>
                                
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-primary float-right" @click="toggleModal" v-if="permission['warehouse-create']">
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
                                        <th width="20%">Warehouse Name </th> 
                                        <th width="25%">Contact Person Name</th>
                                        <th width="25%">Warehouse Number</th>
                                        <th width="25%">Address </th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.name}} </td> 
                                        <td>{{ item.contact_person_name }} </td>
                                        <td>{{ item.warehouse_number }}</td>
                                        <td>{{ item.address }}</td>
                                        <td>
                                            <a href="#" @click="edit(item)" v-if="permission['warehouse-edit']"><i class="fas fa-edit"></i> </a>
                                            <a href="#" @click="deleteItem(item)" v-if="permission['warehouse-delete']"><i class="fas fa-trash"></i> </a>
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
                        <h3>Warehouse Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row  ">
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Warehouse Name *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Warehouse Name" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.name[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Company Name *</label> 
                                                <select class="form-control border" v-model="form.company_id" @change="onkeyPress('status')">
                                                    <option value="">Select company</option>
                                                    <option v-for="(company, index) in companies" :value="company.id" :key="index">
                                                        {{company.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.company_id[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_person_name">Contact person name *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_name')" v-model="form.contact_person_name" id="contact_person_name" placeholder="Contact person name"> 
                                                <div class="invalid-feedback" v-if="errors.contact_person_name">
                                                    {{errors.contact_person_name[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="warehouse_number">Warehouse number *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('warehouse_number')" v-model="form.warehouse_number" id="warehouse_number" placeholder="Warehouse number"> 
                                                <div class="invalid-feedback" v-if="errors.warehouse_number">
                                                    {{errors.warehouse_number[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="row">  
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="address">Address *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('address')" v-model="form.address" id="address" placeholder="Address" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.address">
                                                    {{errors.address[0]}}
                                                </div> 
                                            </div> 
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="status">Status*</label>
                                                <select class="form-control border" v-model="form.status" @change="onkeyPress('status')">
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
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 
export default {
    name: 'Warehouse',
    components: {},
    data() {
        return {
            base_url: this.apiUrl,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            loading: true,
            errors: {},
            logoPreview:'',
            btn:'Create',
            items: [],
            outlets: [],
            districts: [],
            areas: [],
            companies: [],
            form: new Form({
                id: '',
                name: '',
                company_id:'', 
                contact_person_name: '',
                warehouse_number: '', 
                address: '',
                status: 1,
            }),
        };
    }, 
    components: {
        Modal,
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
        fetchIndexData() {  
            axios.get(this.base_url+'/warehouses',this.headerjson)
            .then((res) => {
                this.items = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message); 
            }).finally((ress) => {
                this.loading = false; 
            });
        }, 
        fetchCompany() { 
            axios.get(this.base_url+'/companies', this.headerjson)
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
        checkForm: function(e) {

        },
        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('name', this.form.name);            
            formData.append('company_id', this.form.company_id);
            formData.append('contact_person_name', this.form.contact_person_name);
            formData.append('warehouse_number', this.form.warehouse_number);
            formData.append('address', this.form.address); 
            formData.append('status', this.form.status);   
            if(this.editMode){
                formData.append('_method', 'put'); 
                var postEvent = axios.post(this.base_url+'/warehouses/'+this.form.id, formData, this.headers);
            }else{ 
                this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                var postEvent = axios.post(this.base_url+'/warehouses', formData, this.headers);
            }           

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.fetchIndexData();
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
            });
        },

        onImageChange(e) {
            this.form.logo = e.target.files[0]
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;  
            
            this.createImage(files[0]);

        },
        createImage(file) {
            let reader = new FileReader();
            let vm = this;
            reader.onload = (e) => { 
                vm.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
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
                    axios.delete(this.base_url+'/warehouses/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){  
                            this.fetchIndexData();
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
    created() {
        this.fetchIndexData(); 
        this.fetchCompany();
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed:{
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
    width: 900px
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