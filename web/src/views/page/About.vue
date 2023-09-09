<template>
    <transition  >
    <div class="container-fluid card-body   ">
        <div class="row">
            <div class="col-md-12 ">
                <div class="row ">
                    <div class="form-group col-md-6 float-left">
                        <div class="float-left ">
                            <ul class="breadcrumb">
                                <li><a href="/">Home</a></li>
                                <li>About List</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group col-md-6 ">
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal">
                            Add New
                        </button>
                        <Modal @close="toggleModal" :modalActive="modalActive">
                            <div class="modal-content scrollbar-width-thin">
                                <div class="modal-header"> 
                                    <h3>About Add Or Edit</h3>
                                    <button @click="close" type="button" class="btn btn-default">X</button>
                                </div>
                                <form @submit="submitForm" method="post" novalidate="true" class="needs-validation">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group  ">
                                                    <label for="name">Companuy name</label>
                                                    <input type="text" class="form-control border " @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Companuy name">
                                                    <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" class="invalid-feedback" />
                                                    <div class="invalid-feedback" v-if="errors.name">
                                                        {{errors.name}}
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="contact_person_name">Contact person name</label>
                                                        <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_name')" v-model="form.contact_person_name" id="contact_person_name" placeholder="Contact person name">
                                                        <div v-if="form.errors.has('contact_person_name')" v-html="form.errors.get('contact_person_name')" class="invalid-feedback" />
                                                        <div class="invalid-feedback" v-if="errors.contact_person_name">
                                                            {{errors.contact_person_name}}
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="contact_person_number">Contact person number</label>
                                                        <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_number')" v-model="form.contact_person_number" id="contact_person_number" placeholder="Contact person number">
                                                        <div v-if="form.errors.has('contact_person_name')" v-html="form.errors.get('contact_person_name')" class="invalid-feedback" />
                                                        <div class="invalid-feedback" v-if="errors.contact_person_number">
                                                            {{errors.contact_person_number}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="Address">Address</label>
                                                    <input type="text" class="form-control border " @keypress="onkeyPress('address')" v-model="form.address" id="Address" placeholder="1234 Main St">
                                                    <div class="invalid-feedback" v-if="errors.address">
                                                        {{errors.address}}
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <div class="form-group">
                                                          <label>Category Icon (128X128) *</label> <br>
                                                          <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Chose Icon</span>
                                                            <span class="fileinput-exists" style="display:none">Change Icon</span><input type="file" name="..." @change="onImageChange"/></span>
                                                          </div> 
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="status">Status</label>
                                                        <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="status">
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-if="errors.status">
                                                            {{errors.status}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                              <p>Photo Preview</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success float-right ">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <div class=" box box-success">
                    <div class="table-responsive ">
                        <table class="table table-bordered table-sm ">
                            <thead class="tableFloatingHeaderOriginal">
                                <tr class="border success item-head">
                                    <th width="20%">Company Name </th>
                                    <th width="20%">Logo</th>
                                    <th width="25%">Contact Person Name</th>
                                    <th width="25%">Contact Person Number</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border" v-for="(item, i) in items" v-if="items.length > 0">
                                    <td>{{ item.name}} </td>
                                    <td>{{ item.logo}}</td>
                                    <td>{{ item.contact_person_name }} </td>
                                    <td>{{ item.contact_person_number }}</td>
                                    <td>
                                        <a href="#" @click="edit(item)"><i class="fas fa-edit"></i> </a>
                                        <a href="#" @click="deleteItem(item)"><i class="fas fa-trash"></i> </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
import { ref } from "vue";
import Form from 'vform'
export default {
    name: 'PosLeftbar',
    components: {},
    data() {
        return {
            isShow: false,
            showModal: false,
            errors: {},
            items: [
                { id: 1, name: 'SSG Group', logo: '', status: '1', contact_person_name: 'Golam Rabbani', contact_person_number: '01821000000000' },
                { id: 2, name: 'SSG Group', logo: '', status: '0', contact_person_name: 'Golam Rabbani', contact_person_number: '01821000000000' },
            ],
            form: new Form({
                id: '',
                name: '',
                logo: '',
                contact_person_name: '',
                contact_person_number: '',
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
    setup() {
        const modalActive = ref(false);
        const toggleModal = () => {
            modalActive.value = !modalActive.value;
        };
        const close = () => {
            modalActive.value = !modalActive.value;
        };
        return { modalActive, toggleModal, close };
    },
    methods: {
        openModal: function(item) {
            document.getElementById("exampleModal").classList.add("modal");
        },
        closeModal: function(item) {
            alert('ddd')
        },
        add: function(e) {
            console.log('preventDefault', e)
            //this.close()
        },
        edit: function(item) {
            this.toggleModal();
            this.form.fill(item);
            //this.form.status='1';
        },
        checkForm: function(e) {

        },
        submitForm: function(e) {
            console.log('formData', this.form);
            //console.log('preventDefault',this.errors);
            if (this.form.name && this.form.address && this.form.contact_person_name && this.form.contact_person_number && this.form.status) {
                return true;
            }

            this.errors;

            if (!this.form.name) {
                this.errors.name = 'Name required.';
            } else {
                this.errors.name = '';
            }
            if (!this.form.contact_person_name) {
                this.errors.contact_person_name = 'Contact person name required.';
            } else {
                this.errors.contact_person_name = '';
            }
            if (!this.form.contact_person_number) {
                this.errors.contact_person_number = 'Contact person number required.';
            } else {
                this.errors.contact_person_number = '';
            }
            if (!this.form.address) {
                this.errors.address = 'Address required.';
            } else {
                this.errors.address = '';
            }
            if (!this.form.status) {
                this.errors.status = 'Status required.';
            } else {
                this.errors.status = '';
            }


            console.log('preventDefault', this.errors)

            e.preventDefault();

            // axios.post('/contact', this.form)
            //  .then((res) => {
            //      //Perform Success Action
            //  })
            //  .catch((error) => {
            //      // error.response.status Check status code
            //  }).finally(() => {
            //      //Perform action in always
            //  });
        },
        onkeyPress: function(field) {
            if (field == 'name') {
                this.errors.name = '';
            }
            if (field == 'contact_person_name') {
                this.errors.contact_person_name = '';
            }
            if (field == 'contact_person_number') {
                this.errors.contact_person_number = '';
            }
            if (field == 'address') {
                this.errors.address = '';
            }
            if (field == 'status') {
                this.errors.status = '';
            }
        },
        deleteItem: function(item) {
            alert('Ok')
        },

        ...mapActions(['removeAllCartItems', 'removeCartItem', 'addCartItem']),
    },
    created() {},
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {}
}
</script>
<style scoped> 

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