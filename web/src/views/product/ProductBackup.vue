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
                                <li>Company List</li>
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
                                    <button @click="close" type="button" class="btn btn-default">X</button>
                                </div>
                                <form @submit="submitForm" method="post" novalidate="true" class="needs-validation">
                                    <div class="modal-body">
                                        <div class="row"> 
                                            <div class="row">
                                                <div
                                                  class="col-md-12"
                                                  v-if="validation_error"
                                                  style="margin-top: 20px"
                                                >
                                                  <div class="form-group">
                                                    <div>
                                                      <ul>
                                                        <li
                                                          class="text-danger"
                                                          v-for="error in validation_error"
                                                          :key="error[0]"
                                                        >
                                                          {{ error[0] }}
                                                        </li>
                                                      </ul>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="row">
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Product Name*</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.product_name"
                                                      class="form-control"
                                                      placeholder="Product Name"
                                                    />
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Product Native Name</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.product_native_name"
                                                      class="form-control"
                                                      placeholder="Product Native Name"
                                                    />
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Category*</label>
                                                    <multiselect
                                                      v-model="product.category"
                                                      deselect-label
                                                      track-by="id"
                                                      label="category_name"
                                                      :searchable="true"
                                                      open-direction="bottom"
                                                      placeholder="Chose Category"
                                                      :options="categories"
                                                      @input="getSubCategory()"
                                                      :disabled="false"
                                                    ></multiselect>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Sub Category*</label>
                                                    <multiselect
                                                      v-model="product.sub_category"
                                                      deselect-label
                                                      track-by="id"
                                                      label="sub_category_name"
                                                      :loading="isCategoryLoading"
                                                      :searchable="true"
                                                      open-direction="bottom"
                                                      placeholder="Chose Sub Category"
                                                      :options="sub_categories"
                                                      @input="getSubSubCategories()"
                                                      :disabled="false"
                                                    ></multiselect>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Sub Sub Category</label>
                                                    <multiselect
                                                      v-model="product.sub_sub_category"
                                                      deselect-label
                                                      track-by="id"
                                                      label="sub_sub_category_name"
                                                      :loading="isSubCategoryLoading"
                                                      :searchable="true"
                                                      open-direction="bottom"
                                                      placeholder="Chose Sub Sub Category"
                                                      :options="sub_sub_categories"
                                                      @input="getBrand()"
                                                      :disabled="false"
                                                    ></multiselect>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Brand</label>
                                                    <multiselect
                                                      v-model="product.brand"
                                                      deselect-label
                                                      track-by="id"
                                                      label="brand_name"
                                                      :loading="isBrandLoading"
                                                      :searchable="true"
                                                      open-direction="bottom"
                                                      placeholder="Chose a Brand"
                                                      :options="brands"
                                                      :disabled="false"
                                                    ></multiselect>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Stock Quantity*</label>
                                                    <input
                                                      type="number"
                                                      v-model="product.quantity"
                                                      class="form-control"
                                                      placeholder="Product Quantity"
                                                    />
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Quantity Unit</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.quantity_unit"
                                                      class="form-control"
                                                      placeholder="Ex: 1 pieces,1/2 Kg,250gram,1kg per bag"
                                                    />
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Min Order Qty</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.min_order_qty"
                                                      class="form-control"
                                                      placeholder="Ex: 12pcs"
                                                    />
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Cost Price/Quantity*</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.buying_price"
                                                      class="form-control"
                                                      placeholder="Cost Price or Buying Price"
                                                    />
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Sale Price/Quantity*</label>
                                                    <input
                                                      type="text"
                                                      v-model="product.selling_price"
                                                      class="form-control"
                                                      placeholder="Sale Price or Saling Price"
                                                    />
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Product Tags*</label>
                                                    <multiselect
                                                      v-model="product.product_tag"
                                                      tag-placeholder="Add this as new tag"
                                                      placeholder="Search or add a tag"
                                                      label="keyword_name"
                                                      track-by="id"
                                                      :options="tags"
                                                      :multiple="true"
                                                      :taggable="true"
                                                      @tag="addTag"
                                                    ></multiselect>
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Chose Size</label>
                                                    <multiselect
                                                      v-model="product.size"
                                                      id="ajax"
                                                      label="name"
                                                      track-by="id"
                                                      placeholder="Type to search"
                                                      open-direction="bottom"
                                                      :options="sizes"
                                                      :multiple="true"
                                                      :searchable="true"
                                                      :internal-search="true"
                                                      :clear-on-select="false"
                                                      :close-on-select="false"
                                                      :options-limit="300"
                                                      :limit="6"
                                                      :max-height="600"
                                                      :show-no-results="false"
                                                      :hide-selected="true"
                                                    >
                                                      <template slot="tag" slot-scope="{ option, remove }"
                                                        ><span class="custom__tag"
                                                          ><span>{{ option.name }}</span
                                                          ><span class="custom__remove" @click="remove(option)">
                                                            X</span
                                                          ></span
                                                        ></template
                                                      >
                                                      <template slot="clear" slot-scope="props">
                                                        <div
                                                          class="multiselect__clear"
                                                          v-if="product.size.length"
                                                          @mousedown.prevent.stop="clearAll(props.search)"
                                                        ></div> </template
                                                      ><span slot="noResult"
                                                        >Oops! No elements found. Consider changing the search
                                                        query.</span
                                                      >
                                                    </multiselect>
                                                  </div>
                                                </div> 
                                                <!-- color  -->
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label>Chose Color</label>
                                                    <label>Chose Color</label> <span class="float-right" @click="colorModal()">Add Color</span>
                                                    <multiselect
                                                      v-model="product.color"
                                                      id="ajax"
                                                      label="name"
                                                      track-by="id"
                                                      placeholder="Type to search"
                                                      open-direction="bottom"
                                                      :options="colors"
                                                      :multiple="true"
                                                      :searchable="true"
                                                      :internal-search="true"
                                                      :clear-on-select="false"
                                                      :close-on-select="false"
                                                      :options-limit="300"
                                                      :limit="6"
                                                      :max-height="600"
                                                      :show-no-results="false"
                                                      :hide-selected="true"
                                                    >
                                                      <template slot="tag" slot-scope="{ option, remove }"
                                                        ><span
                                                          class="custom__tag"
                                                          :style="{
                                                            color: '#fff',
                                                            'background-color': option.color_code,
                                                          }"
                                                          ><span>{{ option.name }}</span
                                                          ><span class="custom__remove" @click="remove(option)">
                                                            ❌</span
                                                          ></span
                                                        ></template
                                                      >
                                                      <template slot="clear" slot-scope="props">
                                                        <div
                                                          class="multiselect__clear"
                                                          v-if="product.color.length"
                                                          @mousedown.prevent.stop="clearAll(props.search)"
                                                        ></div> </template
                                                      ><span slot="noResult"
                                                        >Oops! No elements found. Consider changing the search
                                                        query.</span
                                                      >
                                                    </multiselect>
                                                  </div>
                                                </div>

                                                <div class="col-md-3" v-if="trial_setting.status == 1">
                                                  <div class="form-group">
                                                    <label>Trialable</label>
                                                    <select class="form-control" v-model="product.trialable">
                                                      <option value="1">ON</option>
                                                      <option value="0">OFF</option>
                                                    </select>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label
                                                      >Main/Feature Image
                                                      <small
                                                        >(600X600 or 350X350 make sure every image is same
                                                        sizes)</small
                                                      >
                                                      *</label
                                                    >
                                                    <br />
                                                    <div
                                                      class="fileinput fileinput-new"
                                                      data-provides="fileinput"
                                                    >
                                                      <span class="btn btn-block btn-primary btn-file"
                                                        ><span class="fileinput-new"
                                                          ><i class="fa fa-camera"></i> Chose Image</span
                                                        >
                                                        <span class="fileinput-exists">Change Iimage</span
                                                        ><input type="file" name="..." @change="onImageChange"
                                                      /></span>
                                                      <img
                                                        style="height: 80px"
                                                        v-if="product.image"
                                                        :src="product.image"
                                                      />
                                                    </div>
                                                  </div>
                                                </div>

                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                    <label
                                                      >Multiple Image
                                                      <small
                                                        >(600X600 or 350X350 make sure every image is same
                                                        sizes)</small
                                                      ></label
                                                    >
                                                    <br />
                                                    <div
                                                      class="fileinput fileinput-new"
                                                      data-provides="fileinput"
                                                    >
                                                      <span class="btn btn-block btn-primary btn-file"
                                                        ><span class="fileinput-new"
                                                          ><i class="fa fa-camera"></i> Chose Image</span
                                                        >
                                                        <span class="fileinput-exists">Change Iimage</span
                                                        ><input
                                                          id="attachments"
                                                          type="file"
                                                          multiple="multiple"
                                                          name="attachments"
                                                          @change="uploadFieldChange"
                                                      /></span>
                                                    </div>

                                                    <div
                                                      class="attachment-holder animated fadeIn"
                                                      v-cloak
                                                      v-for="(attachment, index) in product.attachments"
                                                      :key="index"
                                                    >
                                                      <span class="label label-primary">{{
                                                        attachment.name +
                                                        " (" +
                                                        Number((attachment.size / 1024 / 1024).toFixed(1)) +
                                                        "MB)"
                                                      }}</span>
                                                      <span
                                                        class=""
                                                        style="background: red; cursor: pointer"
                                                        @click.prevent="removeAttachment(attachment)"
                                                        ><button class="btn btn-xs btn-danger">
                                                          Remove
                                                        </button></span
                                                      >
                                                    </div>
                                                  </div>
                                                </div>

                                                <div class="col-md-12">
                                                  <div class="form-group">
                                                    <label>Product Description</label>
                                                    <vue-editor v-model="product.description"></vue-editor>
                                                  </div>
                                                </div>

                                                <div class="col-md-12">
                                                  <button type="submit" id="btn-edit" class="btn btn-primary">
                                                    {{ button_name }}
                                                  </button>
                                                  <button
                                                    type="close"
                                                    class="btn btn-default"
                                                    data-dismiss="modal"
                                                  >
                                                    Close
                                                  </button>
                                                </div>
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