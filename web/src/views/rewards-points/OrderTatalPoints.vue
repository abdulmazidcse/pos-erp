<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Points and Rewards </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Points and Rewards Settings</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mwb_wpr_wrap_table">
                                <table class="form-table mwb_wpr_general_setting mwp_wpr_settings">
                                    <tbody>
                                        <tr valign="top">
                                            <th scope="row" class="mwb-wpr-titledesc">
                                                <label for="mwb_wpr_thankyouorder_enable">Enable the settings for the orders</label>
                                            </th>
                                            <td class="forminp forminp-text">
                                                <span class="woocommerce-help-tip"></span> <label for="mwb_wpr_thankyouorder_enable">
                                                    <input type="checkbox" name="mwb_wpr_thankyouorder_enable" checked="checked" id="mwb_wpr_thankyouorder_enable" class="input-text"> Enable Points on order total. </label>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row" class="mwb-wpr-titledesc">
                                                <label for="">Enter Points within Order Range</label>
                                            </th>
                                            <td class="forminp forminp-text">
                                                <table class="form-table wp-list-table widefat fixed striped">
                                                    <tbody class="mwb_wpr_thankyouorder_tbody">
                                                        <tr valign="top">
                                                            <th>Minimum</th>
                                                            <th>Maximum</th>
                                                            <th>Points</th>
                                                        </tr>
                                                        <tr valign="top">
                                                            <td class="forminp forminp-text">
                                                                <label for="mwb_wpr_thankyouorder_minimum">
                                                                    <input type="text" name="mwb_wpr_thankyouorder_minimum[]" class="mwb_wpr_thankyouorder_minimum input-text wc_input_price" required="" placeholder="No minimum" value="">
                                                                </label>
                                                            </td>
                                                            <td class="forminp forminp-text">
                                                                <label for="mwb_wpr_thankyouorder_maximum">
                                                                    <input type="text" name="mwb_wpr_thankyouorder_maximum[]" class="mwb_wpr_thankyouorder_maximum" placeholder="No maximum" value="">
                                                                </label>
                                                            </td>
                                                            <td class="forminp forminp-text">
                                                                <label for="mwb_wpr_thankyouorder_current_type">
                                                                    <input type="text" name="mwb_wpr_thankyouorder_current_type[]" class="mwb_wpr_thankyouorder_current_type input-text wc_input_price" required="" value="">
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="mwb_wpr_object_purchase">
                                                    <p>Please purchase the pro plugin to add multiple membership. <a target="_blanck" href="https://makewebbetter.com/product/woocommerce-points-and-rewards?utm_source=MWB-PAR-org&amp;utm_medium=MWB-org-plugin&amp;utm_campaign=MWB-PAR-org">Click here</a></p>
                                                </div>
                                                <input type="button" value="Add More" class="mwb_wpr_add_more button" id="mwb_wpr_add_more">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header">
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group  ">
                                        <div class="mb-3">
                                            <label for="name">Company name *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Company name" autocomplete="off">
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{errors.name[0]}}
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
                                                <label for="contact_person_number">Contact person number *</label>
                                                <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_number')" v-model="form.contact_person_number" id="contact_person_number" placeholder="Contact person number">
                                                <div class="invalid-feedback" v-if="errors.contact_person_number">
                                                    {{errors.contact_person_number[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="mb-3">
                                            <label for="Address">Address *</label>
                                            <input type="text" class="form-control border " @keypress="onkeyPress('address')" v-model="form.address" id="Address" placeholder="1234 Main St">
                                            <div class="invalid-feedback" v-if="errors.address">
                                                {{errors.address[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <div class="mb-3">
                                                    <label>Category Icon (128X128) *</label> <br>
                                                    <div class="   -new">
                                                        <span class="btn btn-block btn-primary btn-file">
                                                            <span class=" -new"><i class="fa fa-camera"></i> Choose Icon</span>
                                                            <span class=" -exists" style="display:none">Change Icon</span>
                                                            <input type="file" name="..." @change="onImageChange" /></span>
                                                    </div>
                                                    <div class="invalid-feedback" v-if="errors.logo">
                                                        {{errors.logo[0]}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="active">Status</label>
                                                <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="active">
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
        toggleModal: function() {
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
        edit: function(item) {
            this.btn = 'Update';
            this.editMode = true;
            this.toggleModal();
            this.form.fill(item);
            this.logoPreview = '';
        },
        checkForm: function(e) {

        },
        submitForm: function(e) {
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
                var postEvent = axios.post(this.apiUrl + '/companies/', formData, this.headers);
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
        validation: function(...fiels) {
            var obj = new Object();
            var validate = false;
            for (var k in fiels) { // Loop through the object  
                for (var j in this.form) {
                    if ((j == fiels[k]) && (!this.form[j])) {
                        obj[fiels[k]] = fiels[k].replace("_", " ") + ' field is required'; // Delete obj[key]; 
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
        onkeyPress: function(field) {
            for (var k in this.errors) { // Loop through the object
                if (k == field) { // If the current key contains the string we're looking for 
                    delete this.errors[k]; // Delete obj[key];
                }
            }
        },
        deleteItem: function(item) {
            console.log('item deleyt=>', item.id);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!",
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    axios.delete(this.apiUrl + '/outlets/' + item.id, this.headers)
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
        //console.log('headersheaders',this.$store.getters)
        console.log('headersheaders', this.headers)
        //this.$on('AfterCreated',() => {  

        //});
    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
}
</script>
<style scoped>
#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.mwb_rwpr_content_template {
    box-shadow: -2px 0 5px -1px rgba(0, 0, 0, .05);
    color: #555d66;
    font-size: 16px;
    flex: 0 0 80%;
    padding: 10px 30px 10px 40px;
    position: relative;
    line-height: 28px;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.mwb_wpr_general_row_wrap {
    box-shadow: 2px 3px 20px rgba(0, 0, 0, .2);
    margin: 20px 0 40px 0;
}

.mwb_rwpr_content_template {
    color: #555d66;
    font-size: 16px;
    line-height: 28px;
}

.mwb_wpr_general_sign_title {
    width: 100%;
    display: block;
    padding: 8px 20px;
    background-color: #4f3154;
    display: block;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}

.mwb_wpr_general_row {
    display: flex;
    flex-wrap: wrap;
    padding: 0 10px;
    align-items: center;
}

.mwb_wpr_general_sign_title {
    width: 100%;
    display: block;
    padding: 8px 20px;
    background-color: #4f3154;
    display: block;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}

#mwb_rwpr_setting_wrapper * {
    box-sizing: border-box;
}

.mwb_wpr_general_label label {
    width: 150px;
    display: inline-block;
}

.mwb_wpr_general_label {
    width: 210px;
    font-weight: 600;
}

.mwb_wpr_general_content {
    width: calc(100% - 240px);
    margin-left: 20px;
}

.mwb_wpr_general_content,
.mwb_wpr_general_label {
    padding: 13px 0;
}

.modal-content.scrollbar-width-thin {
    border: none !important;
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