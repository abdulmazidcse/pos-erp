
<template>
    <transition>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right float-left">
                            <h4 style="margin: 0; padding: 8px 0 1.5rem 0;">User List</h4>
                        </div> 
                        <div class="page-title-right float-right ">
                            <button type="button" class="btn-sm btn btn-outline-success float-right" @click="toggleModal()" v-if="permission['user-create']">
                                <i class="mdi mdi-plus-outline"></i> Add New
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="card"> 
                        <div class="card-body">
                            <Datatable :columns="columns" :sortKey="tableData.sortKey" @sort="sortBy" v-if="!loading">
                                <template #header>
                                    <div class="tableFilters" style="margin-bottom: 10px;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="control" style="float: left;">
                                                    <span
                                                        style="float: left; margin-right: 10px; padding: 7px 0px;">Show
                                                    </span>
                                                    <div class="select" style="float: left;">
                                                        <select class="form-select" v-model="tableData.length"
                                                            @change="fetchItems()">
                                                            <option value="10" selected="selected">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                            <option :value="pagination.total">All</option>
                                                        </select>
                                                    </div>
                                                    <span style="float: left; margin-left: 10px; padding: 7px 0px;">
                                                        Entries</span>
                                                </div>
                                            </div>

                                            <div class="col-md-2">

                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" style="float: right;"
                                                    v-model="tableData.search" placeholder="Search..."
                                                    @input="fetchItems()">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template #body>
                                    <tbody v-if="items.length > 0">
                                        <tr class="border" v-for="(item, i) in items" :key="i"
                                            style="height: 35px;">
                                            <td>{{ item.id}} </td>
                                            <td>
                                                <img style="border-radius:50%; border:1px solid #ededed"
                                                    :src="item.profile_image" width="40" height="40"
                                                    v-if="item.profile_image">
                                            </td>
                                            <td>{{ item.name}} </td>
                                            <td>
                                                <div style="  word-wrap: break-word;">{{ item.email }} </div>
                                            </td>
                                            <td>{{ item.user_code }} </td>
                                            <td>{{ item.phone }}</td>
                                            <td>{{ item.company_name }}</td>
                                            <td>{{ item.roles[0].name }}</td>
                                            <td><span v-if="item.status==1" class="badge bg-success">Active</span>
                                                <span v-if="item.status==0" class="badge bg-warning">In-Active</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown float-end">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <!-- item-->
                                                        <a href="javascript:void(0);" class="dropdown-item text-info"
                                                            @click="outletAssign(item)"
                                                            v-if="permission['user-outlet-assign'] || (item.roles.some(data => data.name == 'Sales Man') || item.roles.some(data => data.name == 'Shop Manager'))"
                                                            >
                                                            <i class="mdi mdi-store-outline me-1"></i> Outlet Assign
                                                        </a>
                                                        <a href="javascript:void(0);" class="dropdown-item text-warning"
                                                            @click="edit(item)" v-if="permission['user-edit']"><i
                                                                class="mdi mdi-circle-edit-outline me-1"></i> Edit</a>
                                                        <a href="javascript:void(0);" class="dropdown-item text-danger"
                                                            @click="deleteItem(item)" v-if="permission['user-delete']"><i
                                                                class="mdi mdi-delete-outline me-1"></i> Remove</a>
                                                        <!-- item-->
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr style="height: 35px;">
                                            <td colspan="9"> No Data Available Here!</td>
                                        </tr>
                                    </tbody>
                                </template>
                                <template #footer>
                                    <Pagination :pagination="pagination" :language="lang" @onChangePage="setPage">
                                    </Pagination>
                                </template>
                            </Datatable>
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

            <!-- Modal User Add -->
            <Modal @close="toggleModal()" :modalActive="modalActive">
                <div class="modal-content scrollbar-width-thin">
                    <div class="modal-header">
                        <h3>User Add Or Edit</h3>
                        <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-row">
                                        <div class="form-group mb-2">
                                            <label for="name">Name *</label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('name')" v-model="form.name" id="name"
                                                placeholder="User Name" autocomplete="off">
                                            <div class="invalid-feedback" v-if="errors.name">
                                                {{errors.name[0]}}
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Email *</label>
                                            <input type="email" class="form-control border "
                                                @keypress="onkeyPress('email')" v-model="form.email" id="email"
                                                placeholder="User Email" autocomplete="off">
                                            <div class="invalid-feedback" v-if="errors.email">
                                                {{errors.email[0]}}
                                            </div>

                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="user_code">User Code *</label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('user_code')" v-model="form.user_code"
                                                id="user_code" placeholder="User Code" autocomplete="off">
                                            <div class="invalid-feedback" v-if="errors.user_code">
                                                {{errors.user_code[0]}}
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group mb-2">
                                            <label for="phone">Phone *</label>
                                            <input type="text" class="form-control border "
                                                @keypress="onkeyPress('phone')" v-model="form.phone" id="phone"
                                                placeholder="Contact Number">
                                            <div class="invalid-feedback" v-if="errors.phone">
                                                {{errors.phone[0]}}
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="company_id">Comapny *</label>
                                            <select class="form-control border" v-model="form.company_id"
                                                @change="onkeyPress('company_id')" id="company_id">
                                                <option value="">Select company</option>
                                                <option v-for="(company, index) in companies" :value="company.id"
                                                    :key="index">
                                                    {{company.name}}
                                                </option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.company_id">
                                                {{errors.company_id[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group mb-2">
                                            <label for="password" v-if="isRequired">Password *</label>
                                            <label for="password" v-else>Password</label>
                                            <input type="password" class="form-control border "
                                                @keypress="onkeyPress('password')" v-model="form.password" id="password"
                                                placeholder="Password">
                                            <div class="invalid-feedback" v-if="errors.password">
                                                {{errors.password[0]}}
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="confirm_password" v-if="isRequired">Confirm Password *</label>
                                            <label for="confirm_password" v-else>Confirm Password</label>
                                            <input type="password" class="form-control border "
                                                @keypress="onkeyPress('password_confirmation')"
                                                v-model="form.password_confirmation" id="confirm_password"
                                                placeholder="Confirm Password">
                                            <div class="invalid-feedback" v-if="errors.password_confirmation">password
                                                {{errors.password_confirmation[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="roles">Role </label>
                                        <!-- <v-select :options="['Canada','United States']" /> -->
                                        <select class="form-control border" v-model="form.roles"
                                            @change="onkeyPress('roles')" id="roles">
                                            <option value="">Select Role</option>
                                            <option v-for="(role, index) in roles" :value="index" :key="index"> {{role}}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.role_id">
                                            {{errors.role_id[0]}}
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
                                    <div class="form-row">

                                        <div class="form-group mb-2">
                                            <div class="form-group">
                                                <label>Image (128X128)</label> <br>
                                                <div class="fileinput fileinput-new" data-provides="fileinput"
                                                    style="position: relative">
                                                    <span class="btn btn-block btn-primary btn-file"><span
                                                            class="fileinput-new"><i class="fa fa-camera"></i> Chosse
                                                            Icon</span>
                                                        <span class="fileinput-exists" style="display:none">Change
                                                            Icon</span><input type="file" name="..."
                                                            @change="onImageChange" /></span>
                                                </div>
                                                <div class="invalid-feedback" v-if="errors.profile_image">
                                                    {{errors.profile_image[0]}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p>Photo Preview</p>
                                    <img :src="imagePreview" v-if="form.profile_image" width="200">
                                    <div v-if="editMode & !imagePreview">
                                        <img :src="form.profile_image" v-if="form.profile_image" width="200">
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

            <!-- Modal User Outlet Assign -->
            <Modal @close="toggleAssignModal()" :modalActive="assignModalActive">
                <div class="modal-content scrollbar-width-thin outlet-assign">
                    <div class="modal-header">
                        <button @click="toggleAssignModal()" type="button" class="btn btn-default">X</button>
                    </div>
                    <form @submit.prevent="submitAssignForm()" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" v-model="assignform.user_id">
                                    <div class="form-group">
                                        <label for="roles">Outlet </label>
                                        <Multiselect class="form-control  " mode="tags" v-model="assignform.outlets"
                                            placeholder="Select Outlet" :classes="multiclasses" :searchable="true"
                                            :options="outlets" :close-on-select="true" :create-option="true"
                                            @change="onkeyPress('outlets')" />

                                        <div class="invalid-feedback" v-if="errors.outlets">
                                            {{errors.outlets[0]}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary " :disabled="assignDisabled">
                                <span v-show="isAssignSubmit">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span> Assign
                            </button>

                        </div>
                    </form>
                </div>
            </Modal>
        </div>
    </transition>
</template>
<script>
import {
   mapGetters,
   mapActions
} from "vuex";
import Modal from "./../helper/Modal";
import {
   ref,
   onMounted
} from "vue";
import Form from 'vform'
import axios from 'axios';

import Buttons from '@/components/Buttons.vue';
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';

export default {
   name: 'User',
   components: {
      Modal,
      Buttons,
      Datatable,
      Pagination
   },
   props: {
      language: {
         type: Object,
         default: () => {
            return {
               lengthMenu: null,
               info: null,
               zeroRecords: null,
               search: null
            }
         },
      },
   },
   data() {
      return {
         base_url: this.apiUrl,
         loading: true,
         isSubmit: false,
         isRequired: true,
         showModal: false,
         editMode: false,
         disabled: false,
         assignDisabled: false,
         modalActive: false,
         assignModalActive: false,
         isAssignSubmit: false,
         errors: {},
         imagePreview: '',
         btn: 'Create',
         items: [],
         companies: [],
         roles: [],
         outlet_data: [],
         outlets: [],
         form: new Form({
            id: '',
            name: '',
            email: '',
            user_code: '',
            phone: '',
            password: '',
            password_confirmation: '',
            profile_image: '',
            company_id: '',
            roles: [],
            status: 1
         }),
         assignform: new Form({
            user_id: '',
            outlets: [],
         }),
         multiclasses: {
            clear: '',
            clearIcon: '',
         },
         columns: [{
               label: 'ID',
               name: 'id',
               width: '5%'
            },
            {
               label: 'Image',
               name: 'profile_image',
               width: '10%'
            },
            {
               label: 'Name',
               name: 'name',
               width: '10%'
            },
            {
               label: 'Email',
               name: 'email',
               width: '10%'
            },
            {
               label: 'User Code',
               name: 'user_code',
               width: '10%'
            },
            {
               label: 'Phone',
               name: 'phone',
               width: '10%'
            },
            {
               label: 'Company',
               name: 'company_name',
               width: '10%'
            },
            {
               label: 'Role',
               name: 'role.0.name',
               width: '10%'
            },
            {
               label: 'Status',
               name: 'status',
               width: '10%'
            },
            {
               label: 'Actions',
               name: '',
               isSearch: false,
               isAction: true,
               width: '5%',

            }
         ],
         tableData: {
            draw: 0,
            length: 10,
            search: '',
            column: 0,
            dir: 'desc',
            sortKey: 'id',
         },
         lang: {
            lengthMenu: this.$props.language.lengthMenu ? this.$props.language.lengthMenu : 'Show_MENU_entries',
            info: this.$props.language.info ? this.$props.language.info : 'Showing_FROM_to_TO_of_TOTAL_entries',
            zeroRecords: this.$props.language.zeroRecords ? this.$props.language.zeroRecords : 'No data available in table.',
            search: this.$props.language.search ? this.$props.language.search : 'Search'
         },
         pagination: {
            lastPage: '',
            currentPage: '',
            total: '',
            lastPageUrl: '',
            nextPageUrl: '',
            prevPageUrl: '',
            from: '',
            to: '',
            links: [],
         },
         isLoading: true,
      };
   },
   methods: {
      toggleModal: function () {
         this.modalActive = !this.modalActive;
         if (!this.modalActive) {
            this.editMode = false;
            this.isRequired = true;
            this.btn = 'Create';
         }
         this.errors = '';
         this.isSubmit = false;
         this.form.reset();
      },
      toggleAssignModal: function () {
         this.assignModalActive = !this.assignModalActive;
         if (!this.assignModalActive) {
            this.btn = 'Assign';
            this.assignform.reset();
         }
         this.errors = '';
         this.isAssignSubmit = false;
         // this.assignform.reset();  
      },
      add: function (e) {},
      edit: function (item) {
         this.btn = 'Update';
         this.editMode = true;
         this.isRequired = false;
         this.toggleModal();
         this.form.fill(item);
         this.form.roles = item.roles[0].id;
         this.imagePreview = '';
      },

      submitForm: function (e) {
         this.isSubmit = true;
         this.disabled = true;
         const formData = new FormData();
         formData.append('name', this.form.name);
         formData.append('company_id', this.form.company_id);
         formData.append('email', this.form.email);
         formData.append('user_code', this.form.user_code);
         formData.append('phone', this.form.phone);
         formData.append('password', this.form.password);
         formData.append('password_confirmation', this.form.password_confirmation);
         formData.append('roles', this.form.roles);
         formData.append('status', this.form.status);
         if (this.editMode) {
            formData.append('_method', 'put');
            if (this.imagePreview) {
               this.form.profile_image ? formData.append('profile_image', this.form.profile_image, this.form.profile_image.name) : '';
            }
            var postEvent = axios.post(this.base_url + '/users/' + this.form.id, formData, this.headers);
         } else {
            this.form.profile_image ? formData.append('profile_image', this.form.profile_image, this.form.profile_image.name) : '';
            var postEvent = axios.post(this.base_url + '/users', formData, this.headers);
         }
         postEvent.then(res => {
            this.isSubmit = false;
            this.disabled = false;
            if (res.status == 200) {
               this.toggleModal();
               this.fetchItems();
               this.$toast.success(res.data.message);
            } else {
               this.$toast.error(res.data.message);
            }
         }).catch(err => {
            this.isSubmit = false;
            this.disabled = false;
            this.$toast.error(err.response.data.message);
            if (err.response.status == 422) {
               this.errors = err.response.data.errors
            }
         });
      },

      outletAssign: function (item) { 
         this.assignform.user_id = item.id;
         this.outlets = this.outlet_data.map(({
            id,
            name
         }) => ({
            label: name,
            value: id
         }));

         this.toggleAssignModal();
      },

      submitAssignForm: function (e) {
         this.isAssignSubmit = true;
         this.assignDisabled = true;
         const formData = new FormData();
         formData.append('user_id', this.assignform.user_id);
         formData.append('outlets', this.assignform.outlets);

         var postEvent = axios.post(this.base_url + '/users/outlet_assign', formData, this.headers);

         postEvent.then(res => {
            this.isAssignSubmit = false;
            this.assignDisabled = false;
            if (res.status == 200) {
               this.toggleAssignModal();
               this.fetchItems();
               this.$toast.success(res.data.message);
            } else {
               this.$toast.error(res.data.message);
            }
         }).catch(err => {
            this.isAssignSubmit = false;
            this.assignDisabled = false;
            this.$toast.error(err.response.data.message);
            if (err.response.status == 422) {
               this.errors = err.response.data.errors
            }
         });
      },

      onImageChange(e) {
         this.form.profile_image = e.target.files[0]
         let files = e.target.files || e.dataTransfer.files;
         if (!files.length)
            return;

         this.createImage(files[0]);

      },
      createImage(file) {
         let reader = new FileReader();
         let vm = this;
         reader.onload = (e) => {
            vm.imagePreview = e.target.result;
         };
         reader.readAsDataURL(file);
      },
      validation: function (...fiels) {
         var obj = new Object();
         var validate = false;
         for (var k in fiels) { // Loop through the object  
            for (var j in this.form) {
               if ((j == fiels[k]) && (!this.form[j])) {
                  obj[fiels[k]] = fiels[k].replace("_", " ") + ' field is required'; // Delete obj[key]; 
                  this.errors = {
                     ...this.errors,
                     ...obj
                  };
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
         for (var k in this.errors) { // Loop through the object
            if (k == field) { // If the current key contains the string we're looking for 
               delete this.errors[k]; // Delete obj[key];
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
               axios.delete(this.base_url + '/users/' + item.id, this.headers).then(res => {
                  if (res.status == 200) {
                     this.fetchItems();
                     this.$toast.success(res.data.message);
                  } else {
                     this.$toast.error(res.data.message);
                  }
               }).catch(err => {
                  this.$toast.error(err.response.data.message);
               })
            }
         });
      },

      fetchHelperData() {
         axios.get(this.apiUrl + '/user-helper-data', this.headerjson)
            .then((response) => {
               let data = response.data.data;
               this.roles = response.data.data.roles;
               this.outlet_data = response.data.data.outlet;
               this.companies = response.data.data.companies;
            })
            .catch(errors => {
               console.log(errors);
            })
            .finally((fres) => {
               this.loading = false;
            });
      },
      fetchItems(url = this.apiUrl + '/user-list') {
         this.tableData.draw++;
         axios.get(url, {
               params: this.tableData,
               headers: this.headerparams
            })
            .then((response) => {
               let data = response.data.data;
               if (this.tableData.draw = data.draw) {
                  this.items = data.data.data;
                  this.configPagination(data.data);
               }
            })
            .catch(errors => {
               console.log(errors);
            })
            .finally((fres) => {
               this.loading = false;
            });
      },

      configPagination(data) {
         this.pagination.lastPage = data.last_page;
         this.pagination.currentPage = data.current_page;
         this.pagination.total = data.total ? data.total : 0;
         this.pagination.lastPageUrl = data.last_page_url;
         this.pagination.nextPageUrl = data.next_page_url;
         this.pagination.prevPageUrl = data.prev_page_url;
         this.pagination.from = data.from ? data.from : 0;
         this.pagination.to = data.to ? data.to : 0;
         this.pagination.links = data.links;
      },

      sortBy(key, sortable) {
         this.tableData.sortKey = key;
         this.tableData.column = this.getIndex(this.columns, 'name', key);
         this.tableData.dir = sortable;
         this.fetchItems();
      },
      setPage(data) {
         this.fetchItems(data.url);
      },
      getIndex(array, key, value) {
         return array.findIndex(i => i[key] == value)
      }

   },
   created() {
      this.fetchHelperData();
      this.fetchItems();
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
    width: 900px
}

.outlet-assign.modal-content.scrollbar-width-thin { 
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