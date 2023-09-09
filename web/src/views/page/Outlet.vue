<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0"> 
                                <li class="breadcrumb-item active">Outlet </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Outlet List</a></li>
                            </ol>
                        </div>
                        <div class="page-title-right float-right "> 
                            <button type="button" class="btn btn-primary float-right" @click="toggleModal" v-if="permission['outlet-create']">
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
                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100"  v-if="!loading">
                                <thead> 
                                    <tr class="border success item-head">
                                        <th width="20%">Outlet Name </th> 
                                        <th width="25%">Contact Person Name</th>
                                        <th width="25%">Outlet Number</th>
                                        <th width="25%">Address </th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead> 
                                <tbody  v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" :key="i">
                                        <td>{{ item.name}} </td> 
                                        <td>{{ item.contact_person_name }} </td>
                                        <td>{{ item.outlet_number }}</td>
                                        <td>{{ item.address }}</td>
                                        <td>
                                            <a href="#" v-if="permission['outlet-edit']" @click="edit(item)"><i class="fas fa-edit"></i> </a>
                                            <a href="#" v-if="permission['outlet-delete']" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a>
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
                        <h3>Outlet Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data" >
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row  ">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Outlet Name *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Outlet Name" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.name">
                                                    {{errors.name[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_id">Company Name *</label> 
                                                <select class="form-control border" v-model="form.company_id" @change="onkeyPress('company_id')" id="company_id">
                                                    <option value="">Select company</option>
                                                    <option v-for="(company, index) in companies" :value="company.id" :key="index">
                                                        {{company.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.company_id">
                                                    {{errors.company_id[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="contact_person_name">Contact person name *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_name')" v-model="form.contact_person_name" id="contact_person_name" placeholder="Contact person name"> 
                                                <div class="invalid-feedback" v-if="errors.contact_person_name">
                                                    {{errors.contact_person_name[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="outlet_number">Outlet number *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('outlet_number')" v-model="form.outlet_number" id="outlet_number" placeholder="Outlet number"> 
                                                <div class="invalid-feedback" v-if="errors.outlet_number">
                                                    {{errors.outlet_number[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">  
                                        
                                        <div class="col-md-12">
                                            <div class="mb-3"> 
                                                <label for="address" style="margin-bottom: -11px;">Address *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3"> 
                                                <select class="form-control border" v-model="form.district_id" @change="onkeyPress('district')" id="district_id">
                                                    <option value="">Select district</option>
                                                    <option v-for="(district, index) in districts" :value="district.id" :key="index"> {{district.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.district_id">
                                                    {{errors.district_id[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3"> 
                                                <select class="form-control border" v-model="form.area_id" @change="onkeyPress('area_id')" id="district_id">
                                                    <option value="">Select area </option>
                                                    <option v-for="(area, index) in areas" :value="area.id" :key="index"> {{area.name}}
                                                    </option>
                                                </select> 
                                                <div class="invalid-feedback" v-if="errors.area_id">
                                                    {{errors.area_id[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3"> 
                                                <input type="text" class="form-control border " @keypress="onkeyPress('police_station')" v-model="form.police_station" id="police_station" placeholder="Police station" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.police_station">
                                                    {{errors.police_station[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-2">
                                            <div class="mb-3"> 
                                                <input type="text" class="form-control border " @keypress="onkeyPress('road_no')" v-model="form.road_no" id="road_no" placeholder="Road no" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.road_no">
                                                    {{errors.road_no[0]}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3"> 
                                                <input type="text" class="form-control border " @keypress="onkeyPress('plot_no')" v-model="form.plot_no" id="plot_no" placeholder="Plot no" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.plot_no">
                                                    {{errors.plot_no[0]}}
                                                </div>
                                            </div>
                                        </div>
                                         
                                    </div>
                                    <div class="row">  
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="latitude">Latitude *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('latitude')" v-model="form.latitude" id="latitude" placeholder="Latitude number" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.latitude">
                                                    {{errors.latitude[0]}}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="longitude">Longitude *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('longitude')" v-model="form.longitude" id="longitude" placeholder="Longitude number" autocomplete="off"> 
                                                <div class="invalid-feedback" v-if="errors.longitude">
                                                    {{errors.longitude[0]}}
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="Active">Status*</label>
                                                <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="Active">
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
//var base_url = 'http://127.0.0.1:8000/api/';
export default {
    name: 'Outlet',
    components: {},
    data() {
        return { 
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            logoPreview:'',
            loading: true,
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
                outlet_number: '',
                district_id: '',
                area_id: '',
                police_station: '',
                road_no: '',
                plot_no: '',
                latitude: '',
                longitude: '',
                status: 1,
            }),
        };
    },
    props: {
        props: ['cartItem'],
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
            axios.get(this.apiUrl+'/outlets',this.headers)
            .then((res) => {
                this.items = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                this.loading = false; 
                //console.log('companies finally',ress);
            });
        }, 
        fetchCompany() { 
            axios.get(this.apiUrl+'/companies', this.headers)
            .then((res) => {
                this.companies = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
            });
        }, 
        fetchDistrict() { 
            axios.get(this.apiUrl+'/districts', this.headers)
            .then((res) => {
                this.districts = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
            });
        },
        fetchArea() { 
            axios.get(this.apiUrl+'/areas',this.headers)
            .then((res) => {
                this.areas = res.data.data; 
            })
            .catch((response) => { 
                //console.log('companies => ',response.data) 
            }).finally((ress) => {
                //console.log('companies finally',ress);
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
            formData.append('outlet_number', this.form.outlet_number);
            formData.append('outlet_number', this.form.outlet_number);
            formData.append('district_id', this.form.district_id);
            formData.append('area_id', this.form.area_id);
            formData.append('status', this.form.status); 
            formData.append('police_station', this.form.police_station); 
            formData.append('road_no', this.form.road_no); 
            formData.append('plot_no', this.form.plot_no); 
            formData.append('latitude', this.form.latitude); 
            formData.append('latitude', this.form.latitude); 
            formData.append('longitude', this.form.longitude); 
            if(this.editMode){
                formData.append('_method', 'put');
                if(this.logoPreview){
                    this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                } 
                var postEvent = axios.post(this.apiUrl+'/outlets/'+this.form.id, formData, this.headers);
            }else{ 
                this.form.logo ? formData.append('logo', this.form.logo, this.form.logo.name) : '';
                var postEvent = axios.post(this.apiUrl+'/outlets', formData, this.headers);
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
                console.log(err.response.data.errors);
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
                    axios.delete(this.apiUrl+'/outlets/'+item.id,this.headers).then(res => {
                        if(res.status == 200){  
                            this.fetchIndexData();
                            this.$toast.success(res.data.message); 
                        }else{
                            this.$toast.error(res.data.message);
                        }
                        console.log(res.data)
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message); 
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        }
    },
    created() {
        this.fetchIndexData();
        this.fetchCompany()
        this.fetchDistrict()
        this.fetchArea();
        //this.$on('AfterCreated',() => {  
         
        //});
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
        }
    }
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin {
    border: none !important;
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