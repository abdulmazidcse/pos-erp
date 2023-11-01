<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Company </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Company List</a></li>
                            </ol>
                        </div>
                        <div class="page-title-right float-right ">
                            <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal" v-if="permission['company-create']">
                                <i class="mdi mdi-camera-timer me-1"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100"
                                v-if="!loading">
                                <thead>
                                    <tr class="border success item-head">
                                        <th width="20%">Company Name </th>
                                        <th width="20%">Logo</th>
                                        <th width="20%">Contact Person Name</th>
                                        <th width="20%">Contact Person Number</th>
                                        <th width="10%">Status </th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.name}} </td>
                                        <td> <img width="50" v-if="item.logo" :src="item.logo"> </td>
                                        <td>{{ item.contact_person_name }} </td>
                                        <td>{{ item.contact_person_number }}</td>
                                        <td><span v-if="item.status==1" class="badge bg-success">Active</span>
                                            <span v-if="item.status==0" class="badge bg-warning">In-Active</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end"> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)" v-if="permission['company-edit']">
                                                    <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a> 
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)" v-if="permission['company-delete']" ><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header">
                        <h3>Company Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group  ">
                                        <div class="mb-3">
                                            <label for="name">Company name *</label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('name')" v-model="form.name" id="name"
                                                placeholder="Company name" autocomplete="off">
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{errors.name[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_person_name">Contact person name *</label>
                                                <input type="text" class="form-control border "
                                                    @keypress="onkeyPress('contact_person_name')"
                                                    v-model="form.contact_person_name" id="contact_person_name"
                                                    placeholder="Contact person name">
                                                <div class="invalid-feedback" v-if="errors.contact_person_name">
                                                    {{errors.contact_person_name[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_person_number">Contact person number *</label>
                                                <input type="text" class="form-control border "
                                                    @keypress="onkeyPress('contact_person_number')"
                                                    v-model="form.contact_person_number" id="contact_person_number"
                                                    placeholder="Contact person number">
                                                <div class="invalid-feedback" v-if="errors.contact_person_number">
                                                    {{errors.contact_person_number[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="mb-3">
                                            <label for="Address">Address *</label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('address')" v-model="form.address" id="Address"
                                                placeholder="1234 Main St">
                                            <div class="invalid-feedback" v-if="errors.address">
                                                {{errors.address[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label>Company Logo (128X128) *</label> <br>
                                                <div class="fileinput fileinput-new" data-provides="fileinput"
                                                    style="position: relative">
                                                    <span class="btn btn-block btn-primary btn-file"><span
                                                            class="fileinput-new"><i class="fa fa-camera"></i> Choose
                                                            Icon</span>
                                                        <span class="fileinput-exists" style="display:none">Change
                                                            Icon</span>
                                                        <input type="file" name="..." @change="onImageChange" /></span>
                                                </div>
                                                <div class="invalid-feedback" v-if="errors.image">
                                                    {{errors.image[0]}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="active">Status</label>
                                                <select class="form-control border" v-model="form.status"
                                                    @change="onkeyPress('status')" id="active">
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
                                <div class="col-md-4">
                                    <p>Photo Preview</p>
                                    <img :src="logoPreview" v-if="form.logo" width="200">
                                    <div v-if="editMode & !logoPreview">
                                        <img :src="form.logo" v-if="form.logo" width="200">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary " :disabled="disabled">
                                <span v-show="isSubmit">
                                    <i class="fas fa-spinner fa-spin"></i>
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
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';
export default {
    name: 'Company',
    components: {},
    data() {
        return {
            isSubmit: false,
            showModal: false,
            editMode: false,
            disabled: false,
            modalActive: false,
            errors: {},
            logoPreview: '',
            btn: 'Create',
            loading: true,
            items: [],
            form: new Form({
                id: '',
                name: '',
                logo: '',
                address: '',
                contact_person_name: '',
                contact_person_number: '',
                status: 1,
            }),
        };
    },
    components: {
        Modal,
    },
    methods: {
        toggleModal: function () {
            this.modalActive = !this.modalActive;
            if (!this.modalActive) {
                this.editMode = false;
                this.btn = 'Create';
            }
            this.errors = '';
            this.isSubmit = false;
            this.form.reset();
            console.log('then', this.isSubmit)
        },
        fetchIndexData() {
            axios.get(this.apiUrl + '/companies', this.headers)
                .then((res) => {
                    this.items = res.data.data;
                    //console.log('companies res',res.data.data);
                })
                .catch((response) => {
                    //console.log('companies => ',response.data) 
                }).finally((ress) => {
                    this.loading = false;
                });
        },
        edit: function (item) {
            this.btn = 'Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item);
            this.logoPreview = '';
        },
        checkForm: function (e) {

        },
        submitForm: function (e) {
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('address', this.form.address);
            formData.append('contact_person_name', this.form.contact_person_name);
            formData.append('contact_person_number', this.form.contact_person_number);
            formData.append('status', this.form.status);
            if (this.editMode) {
                formData.append('_method', 'put');
                if (this.logoPreview) {
                    this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                }
                var postEvent = axios.post(this.apiUrl + '/companies/' + this.form.id, formData, this.headers);
            } else {
                this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                var postEvent = axios.post(this.apiUrl + '/companies', formData, this.headers);
            }
            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if (res.status == 200) {
                    this.toggleModal();
                    this.fetchIndexData();
                    this.$toast.success(res.data.message);
                } else {
                    this.$toast.error(res.data.message);
                }
                console.log('then', this.isSubmit)
            }).catch(err => {
                this.isSubmit = false;
                this.disabled = false;
                this.$toast.error(err.response.data.message);
                if (err.response.status == 422) {
                    this.errors = err.response.data.errors
                }
                console.log('catch', this.isSubmit)
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
        validation: function (...fiels) {
            var obj = new Object();
            var validate = false;
            for (var k in fiels) {     // Loop through the object  
                for (var j in this.form) {
                    if ((j == fiels[k]) && (!this.form[j])) {
                        obj[fiels[k]] = fiels[k].replace("_", " ") + ' field is required';  // Delete obj[key]; 
                        this.errors = { ...this.errors, ...obj };
                    } else {
                        validate = false;
                    }
                }
            }
            // var obj = new Object();
            // obj[fiels] = fiels.replace("_", " ")+' field is required';  
            // this.errors = {...this.errors, ...obj}; 
        },
        onkeyPress: function (field) {
            for (var k in this.errors) {     // Loop through the object
                if (k == field) {      // If the current key contains the string we're looking for 
                    delete this.errors[k];  // Delete obj[key];
                }
            }
        },
        deleteItem: function (item) {
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!",
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    axios.delete(this.apiUrl + '/companies/' + item.id, this.headers)
                        .then(res => {
                            if (res.status == 200) {
                                this.fetchIndexData();
                                this.$toast.success(res.data.message);
                            } else {
                                this.$toast.error(res.data.message);
                            }
                            console.log(res.data)
                        }).catch(err => {
                            this.$toast.error(err.response.data.message);
                        })
                } else {
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            });
        }
    },
    created() {
        this.fetchIndexData();
    },
    destroyed() { },
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
    width: 900px;
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