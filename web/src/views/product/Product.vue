<template>
    <transition>
      <div class="container-fluid">
        <div class="row"> 
          <div class="col-12">
              <div class="page-title-box">
                  <div class="page-title-right float-left">
                    <ol class="breadcrumb m-0"> 
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Products </a></li>
                      <li class="breadcrumb-item active">Product List</li>  
                    </ol>
                  </div> 
                  <div class="page-title-right float-right"> 
                      <button type="button" class="btn btn-outline-primary float-right mr5" v-if="permission['product-barcode']" @click="toggleBarcode">
                        Barcode
                      </button>
                      <button  type="button" class="btn btn-outline-primary float-right mr5" v-if="permission['product-bulk-import']" @click="toggleImportModal">
                        Quick Products Upload
                      </button>
                      <button type="button" class="btn btn-outline-success float-right mr5" v-if="permission['product-bulk-import']" @click="masterDataUploadModal">
                        Master Data Upload
                      </button> 
                      <a href="javascript:void(0);" v-if="permission['product-create']" @click="toggleModal" class="btn btn-primary mb-2 mr5"><i class="mdi mdi-plus-circle me-2"></i> Add Products</a>
                       
                     <!--  <button type="button"  class="btn btn-primary float-right mr5" @click="toggleModal">
                          Add New
                      </button>  -->
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-12">
              <div class="card">
                <div class="card-body"> 
                    <div class="row" style="padding: 0 5px ; margin: 0;">
                      <div class=" col-md-3">  
                            <label class="form-label" for="product_native_name">Category</label> <br>
                            <Multiselect 
                              class="form-control border" 
                              mode="single"
                              v-model="filter.category_id"
                              placeholder="Category"  
                              @change="switchSelect($event)" 
                              :searchable="true" 
                              :filter-results="true"
                              :options="parents"
                              :classes="multiclasses"
                              :close-on-select="true" 
                              :min-chars="1"
                              :resolve-on-load="false" 
                              :create-option="true" 
                            />   
                      </div> 
                      <div class=" col-md-3"> 
                            <label class="form-label" for="product_native_name">Sub Category</label> <br>
                            <Multiselect 
                              class="form-control border" 
                              mode="single"
                              v-model="filter.sub_cat_id"
                              placeholder="Choose Sub Category"  
                              @change="filterData()" 
                              :searchable="true" 
                              :filter-results="true"
                              :options="childs"
                              :classes="multiclasses"
                              :close-on-select="true" 
                              :min-chars="1"
                              :resolve-on-load="false" 
                              :create-option="true" 
                            />    
                      </div> 
                      <div class=" col-md-3">  
                            <label class="form-label" for="product_native_name">Brand</label> <br>
                            <select class="form-control border" v-model="filter.brand_id" id="brand" @change="filterData()" > 
                                <option value=""> Choose Brand </option>
                                <option v-for="(brand, index) in brands" :value="brand.id" :key="index" 
                                  > {{brand.name}}
                                  </option>
                            </select> 
                      </div>
                    </div> 
                  
                </div>
              </div>

              <div class="card"> 
                  <div class="card-body">    
                        <Datatable 
                            :columns="columns" 
                            :sortKey="tableData.sortKey"  
                            @sort="sortBy" 
                            v-if="!loading">
                            <template #header > 
                                <div class="tableFilters" style="margin-bottom: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="control" style="float: left;">
                                                <span style="float: left; margin-right: 10px; padding: 7px 0px;">Show </span>
                                                <div class="select" style="float: left;">
                                                    <select class="form-select" v-model="tableData.length" @change="fetchItems()"> 
                                                        <option value="2" selected="selected">2</option>
                                                        <option value="5" selected="selected">5</option>
                                                        <option value="10" selected="selected">10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                </div>
                                                <span style="float: left; margin-left: 10px; padding: 7px 0px;"> Entries</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                             
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="fetchItems()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >  
                                <tbody v-if="items.length > 0">
                                    <tr class="border" v-for="(item, i) in items" v-if="items.length > 0">
                                      <td>{{ item.product_name}} </td>
                                      <td>{{ item.product_native_name}} </td>
                                      <td>{{ item.cost_price}}</td>
                                      <td>{{ item.mrp_price }} </td>
                                      <td>{{ item.product_code }}</td>
                                      <td>{{ item.category ? item.category.name : ''}} </td>
                                      <td>{{ item.alert_quantity }}</td>
                                      <td>
                                        <div class="dropdown float-end">
                                          <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="mdi mdi-dots-vertical"></i>
                                          </a>
                                          <div class="dropdown-menu dropdown-menu-end">
                                              <!-- item--> 
                                              <a href="#" v-if="permission['product-barcode']" @click="singleBarcode(item)" class="dropdown-item text-info"><i class="fa-solid fa-barcode"></i> Print Barcode</a>

                                              <a href="#" v-if="permission['product-show']" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a> 

                                              <a href="javascript:void(0);" class="dropdown-item text-warning" v-if="permission['product-edit']" @click="edit(item)">
                                              <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                              <!-- item-->
                                              <a href="javascript:void(0);" class="dropdown-item text-danger" v-if="permission['product-delete']" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
                                          </div>
                                        </div>   
                                      </td>
                                    </tr> 
                                </tbody> 
                                <tbody v-else>
                                    <tr>
                                        <td colspan="3"> No Data Available Here!</td>
                                    </tr>
                                </tbody>
                            </template> 
                            <template #footer>
                                <Pagination 
                                    :pagination="pagination"  
                                    :language="lang"
                                    @onChangePage="setPage" > 
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
                  </div> <!-- end card body-->
              </div> <!-- end card -->
          </div><!-- end col-->
      </div>  
        <!-- Product Add Form -->
        <Modal @close="toggleBarcode" :modalActive="modalActiveBarcode">
            <div class="modal-content scrollbar-width-thin product-barcode-modal">
                <div class="modal-header"> 
                    <h5>Product Barcode </h5>
                    <button @click="toggleBarcode" type="button" class="btn btn-default">X</button>
                </div> 
                <div class="modal-body " id="barcodePrint">  
                    <div class="row d-print-none">
                      <div class="col-md-3">    
                          <label class="form-label" for="product_native_name">Product </label> <br> 
                          <Multiselect 
                            class="form-control border customer_id" 
                            mode="single"
                            v-model="form.customer_id"
                            placeholder="Products"  
                            @change="selectProduct($event)"
                            :searchable="true" 
                            :filter-results="true"
                            :options="products"
                            :classes="multiclasses"
                            :close-on-select="true" 
                            :min-chars="1"
                            :resolve-on-load="false" 
                          />     
                      </div>
                      <div class="col-md-2">    
                          <label class="form-label" for="product_native_name">Model</label> <br>
                          <select class="form-control border" v-model="barcode_symbology" @change="barcodeSetting"> 
                              <option value=""> Choose barcode symbology</option>
                              <option v-for="(barcode, index) in barcode_symbology_list" :value="barcode.name" :key="index" 
                                > {{barcode.name}}
                                </option>
                          </select>  
                      </div>
                      <div class="col-md-2">    
                          <label class="form-label" for="product_native_name">Colors</label> <br>
                          <select class="form-control border" v-model="color"  @change="barcodeSetting"> 
                              <option value=""> Choose Color</option>
                              <option v-for="(color, index) in colors" :value="color.label" :key="index" 
                                > {{color.label}}
                                </option>
                          </select>  
                      </div> 
                      <div class="col-md-2">  
                          <label class="form-label" for="product_native_name">Barcode Width</label> <br>  
                          <input class="form-control border" type="number" v-model="barcodeWidth" @keypress="barcodeSetting">  
                      </div>
                      <div class="col-md-2"> 
                          <label class="form-label" for="product_native_name">Barcode Height</label> <br>   
                          <input class="form-control border" type="number" v-model="barcodeHeight" @keypress="barcodeSetting">  
                      </div> 
                      <div class="col-md-1"  > 
                          <label class="form-label" >Quentity</label> <br>   
                          <input class="form-control border" type="number" v-model="barcodeQty" @keypress="barcodeSetting">  
                      </div> 
                      <div class="col-md-1"  > 
                          <label class="form-label" >ABP Quentity</label> <br>   
                          <input class="form-control border" type="number" v-model="form.abp_qty" @keypress="barcodeSetting">  
                      </div> 
                    </div>
                    <div class="row" v-if="singleBarcodeEnable" >
                      <div class="col-md-6 col-xs-6" v-for="(item, i) in barcodeQty" > 
                        <div class="border mt-2" style="text-align: center;">
                          <span calss="barcodeName"> {{ product.product_name }}</span><br>
                          <BarcodeGenerator
                            v-if="barcodeReRender" 
                            :text="'Price '+product.mrp_price"
                            :fontSize="12"
                            :value="product.product_code"
                            :format="barcode_symbology"
                            :lineColor="color" 
                            :height="barcodeHeight"
                            :width="barcodeWidth"
                            :elementTag="'img'"
                          />  
                        </div> 
                      </div>  
                    </div>
                    <div class="row" v-if="!singleBarcodeEnable">
                      <div class="col-md-6 col-xs-6" v-for="(item, i) in items" v-if="items.length > 0"> 
                        <div class="border mt-2" style="text-align: center;">
                          <span calss="barcodeName"> {{ item.product_name }}</span><br>
                          <BarcodeGenerator
                            v-if="barcodeReRender"
                            :text="'Price '+item.mrp_price"
                            :fontSize="12" 
                            :value="item.product_code" 
                            :format="barcode_symbology"
                            :lineColor="color" 
                            :height="barcodeHeight"
                            :width="barcodeWidth"
                            :elementTag="'svg'"
                          />                            
                        </div> 
                      </div>  
                    </div>                     
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success float-right " @click="print">Print</button>
                </div> 
            </div>
        </Modal>

        <Modal @close="toggleModal" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin product-add-modal">
                <div class="modal-header"> 
                    <h5>Add New Product</h5>
                    <button @click="toggleModal" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                    <div class="modal-body"> 
                      <div class="col-md-12"> 
                        <div class="row">
                          <div class=" col-md-3">
                            <div class="mb-3"> 
                              <label class="form-label" for="product_type">Product Type</label> 
                              <select class="form-control border" v-model="form.product_type" id="brand">
                                  <option value="standard">Standard</option> 
                                  <option value="combo">Combo</option>  
                                  <option value="variable">Variable</option>  
                              </select>
                            </div>
                          </div> 
                          <div class=" col-md-3">
                            <div class="mb-3"> 
                              <label class="form-label" for="product_name">Product Name*</label>
                              <input type="text" class="form-control border " @keypress="onkeyPress('product_name')" v-model="form.product_name" id="product_name" placeholder="Product Name" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.product_name">
                                  {{errors.product_name[0]}}
                              </div>
                            </div>
                          </div> 
                          <div class=" col-md-3">
                            <div class="mb-3"> 
                              <label class="form-label" for="product_native_name">Product Native Name*</label>
                              <input type="text" class="form-control border " @keypress="onkeyPress('product_native_name')" v-model="form.product_native_name" id="product_native_name" placeholder="Product Native Name" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.product_native_name">
                                  {{errors.product_native_name[0]}}
                              </div>
                            </div>
                          </div> 
                          <div class=" col-md-3">
                            <div class="mb-3"> 
                              <label class="form-label" for="product_code">Product Code*</label> 
                              <div class="input-group">
                              <input type="text" class="form-control border" aria-label="Dollar amount (with dot and two decimal places)"  @keypress="onkeyPress('product_code')" v-model="form.product_code" id="product_code" placeholder="Product Code" autocomplete="off">  
                              <span class="input-group-text " id="random_num" @click="random_num()"  >
                                  <i class="fa fa-random"></i>
                              </span>
                              <div class="invalid-feedback" v-if="errors.product_code">
                                  {{errors.product_code[0]}}
                              </div>
                            </div>
                            </div>
                          </div>  
                        </div> 

                        <div class="row">
                            <div class=" col-md-3"> 
                                <div class="mb-3">
                                  <label class="form-label" for="product_native_name">Category*</label> <br>
                                  <Multiselect 
                                    class="form-control border" 
                                    mode="single"
                                    v-model="form.category_id"
                                    placeholder="Category"  
                                    @change="switchSelect($event)" 
                                    :searchable="true" 
                                    :filter-results="true"
                                    :options="parents"
                                    :classes="multiclasses"
                                    :close-on-select="true" 
                                    :min-chars="1"
                                    :resolve-on-load="false" 
                                    :create-option="true" 
                                  />  
                                  <div class="invalid-feedback" v-if="errors.category_id">
                                      {{errors.category_id[0]}}
                                  </div>
                                </div>
                            </div> 
                            <div class=" col-md-3">
                              <div class="mb-3"> 
                                <label class="form-label" for="product_native_name">Sub Category*</label> <br>
                                  <Multiselect 
                                    class="form-control border" 
                                    mode="single"
                                    v-model="form.sub_category_id"
                                    placeholder="choose Sub Category"  
                                    @change="onkeyPress('sub_category_id')" 
                                    :searchable="true" 
                                    :filter-results="true"
                                    :options="childs"
                                    :classes="multiclasses"
                                    :close-on-select="true" 
                                    :min-chars="1"
                                    :resolve-on-load="false" 
                                    :create-option="true" 
                                  />  
                                  <div class="invalid-feedback" v-if="errors.sub_category_id">
                                      {{errors.sub_category_id[0]}}
                                  </div> 
                                </div> 
                            </div> 
                            <div class=" col-md-3">
                              <div class="mb-3"> 
                              <label class="form-label" for="product_native_name">Brand</label> 
                              <select class="form-control border" v-model="form.brand_id" id="brand"> 
                                  <option value=""> Choose Brand </option>
                                  <option v-for="(brand, index) in brands" :value="brand.id" :key="index" 
                                  > {{brand.name}}
                                  </option>
                              </select>
                              </div> 
                            </div>
                            <div class=" col-md-3">
                              <div class="mb-2"> 
                                <label class="form-label" for="barcode">Supplier/Vendor</label> <br>
                                <Multiselect
                                  class="form-control  " 
                                  mode="tags"
                                  v-model="form.supplier_id"
                                  placeholder="Supplier/Vendor"
                                  :classes="multiclasses"
                                  :searchable="true" 
                                  :options="suppliers"  
                                  :close-on-select="true" 
                                  :create-option="true"  
                                /> 
                                </div> 
                            </div>  
                        </div>

                        <div class="row">  
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="min_order_qty">Min Order Qty*</label>
                              <input type="text" class="form-control border " @keypress="onkeyPress('min_order_qty')" v-model="form.min_order_qty" id="min_order_qty" placeholder="Ex: 12" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.min_order_qty">
                                  {{errors.min_order_qty[0]}}
                              </div>
                              </div> 
                          </div>
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="cost_price">Cost Price/Quantity*</label>
                              <input type="text" class="form-control border " @keypress="onkeyPress('cost_price')" v-model="form.cost_price" id="cost_price" placeholder="Cost Price or Buying Price" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.cost_price">
                                  {{errors.cost_price[0]}}
                              </div>
                              </div> 
                          </div>
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="depo_price">Depo Price</label> 
                              <input type="text" class="form-control border " @keypress="validateNumber($event, 'depo_price')" v-model="form.depo_price" id="depo_price" placeholder="Depot Price or Salling Price" autocomplete="off"> 
                              <div class="error" v-if="!isValid">Number is Invalid</div>
                              <div class="invalid-feedback" v-if="errors.depo_price">
                                  {{errors.depo_price[0]}}
                              </div>
                              </div> 
                          </div> 
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="mrp_price">MRP Price*</label>
                              <input type="number" class="form-control border " @keypress="onkeyPress('mrp_price')" v-model="form.mrp_price" id="mrp_price" placeholder="Cost Price or Buying Price" autocomplete="off" pattern="^\d+(?:\.\d{1,2})?$"> 
                              <div class="invalid-feedback" v-if="errors.mrp_price">
                                  {{errors.mrp_price[0]}}
                              </div>
                              </div> 
                          </div>  
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="abp_price">ABP Price</label> 
                              <input type="text" class="form-control border " @keypress="validateNumber($event, 'abp_price')" v-model="form.abp_price" id="abp_price" placeholder="ABP Price" autocomplete="off"> 
                              <div class="error" v-if="!isValid">Number is Invalid</div>
                              <div class="invalid-feedback" v-if="errors.abp_price">
                                  {{errors.abp_price[0]}}
                              </div>
                              </div> 
                          </div> 
                          <div class="col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="abp_qty">ABP Qty</label> 
                              <input type="text" class="form-control border " @keypress="validateNumber($event, 'abp_qty')" v-model="form.abp_qty" id="abp_qty" placeholder="ABP Price" autocomplete="off"> 
                              <div class="error" v-if="!isValid">Number is Invalid</div>
                              <div class="invalid-feedback" v-if="errors.abp_qty">
                                  {{errors.abp_qty[0]}}
                              </div>
                              </div> 
                          </div>                                               
                        </div>

                        <div class="row">
                          
                          <div class=" col-md-2">
                            <div class="mb-3">
                              <label class="form-label" for="discount">Discount (5 or 5%) </label>
                              <input type="text" class="form-control border" v-model="form.discount" id="discount" placeholder="(5 or 5%)" autocomplete="off">   
                              <div class="invalid-feedback" v-if="errors.discount">
                                  {{errors.discount[0]}}
                              </div>
                            </div>
                          </div>
                          <div class=" col-md-2">
                            <div class="mb-3">
                              <label class="form-label" for="tax_method">Tax Method*</label>
                              <select class="form-control border" v-model="form.tax_method" id="tax_method" @change="onkeyPress('tax_method')"> 
                                <option value="">Choose Tax Method</option>  
                                <option :value="tax.id" v-for="(tax, i) in producttaxs" :key="i" >{{tax.name}}</option>  
                              </select>  
                              <div class="invalid-feedback" v-if="errors.tax_method">
                                  {{errors.tax_method[0]}}
                              </div>
                            </div>
                          </div> 
                          <div class=" col-md-2">
                            <div class="mb-3">
                              <label class="form-label" for="product_tax">Product Tax*</label>
                              <select class="form-control border" v-model="form.product_tax" id="product_tax" @change="onkeyPress('product_tax')">
                                <option value="">Choose Tax</option>  
                                <option :value="tax.id" v-for="(tax, i) in producttax" :key="i" >{{tax.title}}</option>  
                              </select>  
                              <div class="invalid-feedback" v-if="errors.product_tax">
                                  {{errors.product_tax[0]}}
                              </div>
                            </div>
                          </div>
                          <div class=" col-md-3">
                            <div class="mb-3">
                              <label class="form-label" for="alert_quantity">Alert Quantity *</label>
                              <input type="text" class="form-control border " @keypress="onkeyPress('alert_quantity')" v-model="form.alert_quantity" id="alert_quantity" placeholder="Sale Price or Saling Price" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.alert_quantity">
                                  {{errors.alert_quantity[0]}}
                              </div>
                            </div> 
                          </div>
                          <div class=" col-md-3">
                            <div class="mb-3">
                              <label class="form-label" for="product_tags">Product Tags</label><br>
                              <Multiselect
                                  class="form-control border" 
                                  mode="tags"
                                  v-model="form.product_tags"
                                  placeholder="Search or add a tag"
                                  :searchable="true"
                                  :createTag="true"
                                  :options="product_tags"  
                                  :close-on-select="true" 
                                  :create-option="true"  
                                  :taggable="true"
                                /> 
                              <div class="invalid-feedback" v-if="errors.product_tags">
                                  {{errors.product_tags[0]}}
                              </div>
                            </div>
                          </div>    
                        </div> 

                        <div class="row">
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                              <label class="form-label" for="purchase_measuring_unit">Purchase Measuring Unit </label> 
                              <select class="form-control border" v-model="form.purchase_measuring_unit" id="purchase_measuring_unit"> 
                                <option value=""> Choose Unit </option>
                                <option :value="unit.id" v-for="(unit, i) in units" :key="i" >{{unit.unit_name}}</option>  
                                 
                              </select>
                              <div class="invalid-feedback" v-if="errors.purchase_measuring_unit">
                                  {{errors.purchase_measuring_unit[0]}}
                              </div>
                              </div>
                            </div> 
                            <div class="form-group col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="sales_measuring_unit">Sales Measuring Unit </label> 
                              <select class="form-control border" v-model="form.sales_measuring_unit" id="sales_measuring_unit">
                                <option value="">Choose Unit</option>
                                <option :value="unit.id" v-for="(unit, i) in units" :key="i" >{{unit.unit_name}}</option> 
                              </select>
                              <div class="invalid-feedback" v-if="errors.sales_measuring_unit">
                                  {{errors.sales_measuring_unit[0]}}
                              </div>
                              </div>
                            </div> 
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                              <label class="form-label" for="convertion_rate">1 Purchase Unit = ? Sales Unit </label> 
                              <input type="number" class="form-control border " @keypress="onkeyPress('convertion_rate')" v-model="form.convertion_rate" id="convertion_rate" placeholder="1 Purchase Unit = ? Sales Unit" autocomplete="off">  
                              <div class="invalid-feedback" v-if="errors.convertion_rate">
                                  {{errors.convertion_rate[0]}}
                              </div> 
                              </div>
                            </div>
                            <div class="form-group col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="carton_size">Carton Size </label> 
                              <input type="number" class="form-control border " @keypress="onkeyPress('carton_size')" v-model="form.carton_size" id="carton_size" placeholder="Carton Size" autocomplete="off">  
                              <div class="invalid-feedback" v-if="errors.carton_size">
                                  {{errors.carton_size[0]}}
                              </div>
                              </div>
                            </div>
                            <div class="form-group col-md-2">
                              <div class="mb-3">
                              <label class="form-label" for="carton_c_p_u"> Carton CPU </label> 
                              <input type="number" class="form-control border " @keypress="onkeyPress('carton_c_p_u')" v-model="form.carton_c_p_u" id="carton_c_p_u" placeholder="Carton CPU" autocomplete="off"> 
                              <div class="invalid-feedback" v-if="errors.carton_c_p_u">
                                  {{errors.carton_c_p_u[0]}}
                              </div>
                            </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="is_expirable">Expirable Product</label>
                                <select class="form-control border" v-model="form.is_expirable" id="is_expirable">
                                  <option :value="1">Yes</option>
                                  <option :value="0">No</option>  
                                </select>   
                                <div class="invalid-feedback" v-if="errors.is_expirable">
                                    {{errors.description[0]}}
                                </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="is_ecommerce">For Web/Ecommerce</label>
                                <select class="form-control border" v-model="form.is_ecommerce" id="is_ecommerce">
                                  <option :value="1">Yes</option>
                                  <option :value="0">No</option>  
                                </select>   
                                <div class="invalid-feedback" v-if="errors.is_ecommerce">
                                    {{errors.description[0]}}
                                </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="user_define_barcode">Vendor Define Barcode</label> <br>
                                <Multiselect
                                  class="form-control border" 
                                  mode="tags"
                                  v-model="form.user_define_barcode"
                                  placeholder="User Define Barcode"
                                  :searchable="true"
                                  :createTag="true"
                                  :options="user_define_barcode"  
                                  :close-on-select="true" 
                                  :create-option="true"  
                                />  
                                <div class="invalid-feedback" v-if="errors.user_define_barcode">  
                                   
                                  <span v-for="(barcode, i) in errors.user_define_barcode[0]" :key="i" > {{barcode}} <br>
                                  </span> 
                                </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="allow_checkout_when_out_of_stock">Allow checkout when out of stock</label>
                                <select class="form-control border" v-model="form.allow_checkout_when_out_of_stock" id="allow_checkout_when_out_of_stock">
                                  <option :value="0">No</option> 
                                  <option :value="1">Yes</option> 
                                </select>   
                                <div class="invalid-feedback" v-if="errors.allow_checkout_when_out_of_stock">
                                    {{errors.allow_checkout_when_out_of_stock[0]}}
                                </div>
                                </div>
                            </div>
                            
                            <div class="form-group col-md-3" style="display:none"> 
                                <Multiselect
                                    class="form-control border" 
                                    v-model="value"
                                    mode="tags"
                                    placeholder="Select employees"
                                    track-by="name"
                                    label="name"
                                    :close-on-select="true"
                                    :searchable="true"
                                    :options="[
                                      { value: 'judy', name: 'Judy', image: 'https://randomuser.me/api/portraits/med/women/1.jpg' },
                                      { value: 'jane', name: 'Jane', image: 'https://randomuser.me/api/portraits/med/women/2.jpg' },
                                      { value: 'john', name: 'John', image: 'https://randomuser.me/api/portraits/med/men/1.jpg' },
                                      { value: 'joe', name: 'Joe', image: 'https://randomuser.me/api/portraits/med/men/2.jpg' }
                                    ]"
                                  >
                                  <template v-slot:tag="{ option, handleTagRemove, disabled }">
                                    <div class="multiselect-tag is-user">
                                      <img :src="option.image">
                                      {{ option.name }}
                                      <span
                                        v-if="!disabled"
                                        class="multiselect-tag-remove"
                                        @mousedown.prevent="handleTagRemove(option, $event)"
                                      >
                                        <span class="multiselect-tag-remove-icon">X</span>
                                      </span>
                                    </div>
                                  </template>
                                </Multiselect>
                            </div>    
                        </div>

                        <div class="row" v-if="form.product_type=='variable'"> 
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="barcode">Colors</label> <br>
                                <Multiselect
                                  class="form-control border" 
                                  mode="tags"
                                  v-model="form.color"
                                  placeholder="Select Colors"
                                  :searchable="true"
                                  :createTag="false"
                                  :options="colors"  
                                  :close-on-select="true" 
                                  :create-option="false"  
                                /> 
                              </div> 
                            </div> 
                            <div class="form-group col-md-3">
                              <div class="mb-3">
                                <label class="form-label" for="barcode">Sizes</label> <br>
                                <Multiselect
                                  class="form-control border" 
                                  mode="tags"
                                  v-model="form.size"
                                  placeholder="Select Size"
                                  :searchable="true"
                                  :createTag="false"
                                  :options="sizes"  
                                  :close-on-select="true" 
                                  :create-option="false"  
                                />  
                              </div>     
                            </div>     
                        </div>
                        
                        <div class="row"> 
                            <div class="form-group col-md-6">
                              <div class="mb-3"> 
                                <label class="form-label" for="thumbnail">Main/Feature Image 
                                  <small>(350X350 make sure every image is same sizes)</small>
                                </label> <br>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                  <span class="btn btn-block btn-primary btn-file col-md-3"><span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                                  <span class="fileinput-exists" style="display:none">Change Image</span>
                                  <input type="file" name="..." @change="onImageChange"/></span>
                                  <img :src="thumbnailPreview" v-if="form.thumbnail" width="200"  >
                                  <div v-if="editMode & !thumbnailPreview"  >
                                      <img :src="form.thumbnail" v-if="form.thumbnail" width="200">
                                  </div>
                                </div> 
                              </div>                                
                            </div> 
                            <div class="form-group col-md-6" v-if="form.is_ecommerce"> 
                              <div class="mb-3">
                                <label class="form-label">Multiple Image
                                  <small>(make sure every image is same sizes)</small>
                                </label> <br>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                  <span class="btn btn-block btn-primary btn-file col-md-3"><span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                                  <span class="fileinput-exists" style="display:none">Change Image</span>
                                  <input id="attachments" type="file"  multiple="multiple" name="attachments" @change="uploadFieldChange"/></span>
                                  <div class="attachment-holder animated fadeIn" v-cloak v-for="(attachment, index) in form.attachments" :key="index"> 
                                    <span class="label label-primary">{{
                                      attachment.name +
                                        " (" +
                                        Number((attachment.size / 1024 / 1024).toFixed(1)) +
                                        "MB)"
                                      }}
                                    </span>
                                    <span class="" style="background: red; cursor: pointer"
                                      @click.prevent="removeAttachment(attachment)"><button class="btn btn-xs btn-danger">
                                        Remove
                                      </button>
                                    </span>
                                  </div>
                                </div>
                            </div>   
                            </div>   
                        </div>  
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="short_description">Short Description</label>
                                <textarea name="short_description" class="form-control border" placeholder="Enter text here..."></textarea>  
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="description">Description</label>
                                <textarea name="description" class="form-control border" placeholder="Enter text here..."></textarea>  
                            </div> 
                                
                        </div>  
                      </div> 
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="submit" class="btn btn-success float-right ">Submit</button> -->
                        <button type="submit" class="btn btn-success " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{btn}} 
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal @close="toggleModal2" :modalActive="modalShow">
          <div class="modal-content scrollbar-width-thin product-show-modal">
            <div class="modal-header"> 
                <h5 class="">Product Info</h5>
                <button @click="toggleModal2" type="button" class="btn btn-default">X</button>
            </div>
             <div class="modal-body" v-if="product">  
              <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-5">    
                                  <vue-barcode 
                                    :value="product.pro_code" 
                                    :options="{ 
                                      lineColor: '#000', 
                                      displayValue:true,
                                      text:product.product_name,  
                                      flat: true,
                                      format: EAN8,
                                      width: 1,
                                      height: 30 }" >
                                  </vue-barcode> 
                                </div> <!-- end col -->
                                <div class="col-lg-7"> 
                                        <!-- Product title -->
                                        
                                        <div class="col-md-12">
                                          <h3 class="mt-0">{{product.product_name}}  </h3>  
                                          <table class="table table-striped">
                                            <tr>
                                              <td scope="col">Cost Price</td>
                                              <td scope="col">Depo Price</td>
                                              <td scope="col">MRP Price</td>
                                              <td scope="col">ABP Price</td>
                                            </tr>
                                            <tr>
                                              <td>{{product.cost_price}}</td>
                                              <td>{{product.depo_price}}</td>
                                              <td>{{product.mrp_price}}</td>
                                              <td>{{product.abp_price}}</td>
                                            </tr>
                                          </table>
                                        </div>  

                                        <!-- Product information -->
                                        <div class="mt-4">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="font-14">Available Stock:</h6>
                                                    <p class="text-sm lh-150">{{ (product.stock_product) ? product.stock_product[0].stock_quantity : 0}}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="font-14">Sold Stock:</h6>
                                                    <p class="text-sm lh-150">{{ (product.stock_product) ? product.stock_product[0].out_stock_quantity : 0}}</p>
                                                </div>
                                                <!-- <div class="col-md-4">
                                                    <h6 class="font-14">Revenue:</h6>
                                                    <p class="text-sm lh-150">0</p>
                                                </div> -->
                                            </div>
                                        </div> 
                                </div> <!-- end col -->
                            </div> <!-- end row-->
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-centered mb-0"> 
                                    <tbody>
                                        <tr>
                                            <td>Product type</td>
                                            <td>{{product.product_type}}</td> 
                                        </tr> 
                                        <tr v-if="product.category_name">
                                            <td>category_name</td>
                                            <td>{{product.category_name.name}}</td> 
                                        </tr>
                                        <tr v-if="product.sub_category_name">
                                            <td>Sub Category</td>
                                            <td>{{product.sub_category_name.name}}</td> 
                                        </tr>
                                        <tr v-if="product.brand_name">
                                            <td>Brand</td>
                                            <td>{{product.brand_name.name}}</td> 
                                        </tr>
                                        <tr v-if="product.is_ecommerce">
                                            <td>is ecommerce</td>
                                            <td>
                                              <span class="badge bg-info" v-if="product.is_ecommerce">Yes</span> 
                                              <span class="badge bg-info" v-else>No</span> 
                                            </td>
                                        </tr> 
                                        <tr v-if="product.is_expirable">
                                            <td>is ecommerce</td>
                                            <td>
                                              <span class="badge bg-info" v-if="product.is_expirable">Yes</span> 
                                              <span class="badge bg-info" v-else>No</span> 
                                            </td>
                                        </tr> 
                                        <tr v-if="product.is_expirable">
                                            <td>is expirable</td>
                                            <td>
                                              <span class="badge bg-info" v-if="product.is_expirable">Yes</span> 
                                              <span class="badge bg-info" v-else>No</span> 
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td>purchase_measuring_unit</td>
                                            <td>
                                              <span class="badge bg-info" v-if="product.is_expirable">Yes</span> 
                                              <span class="badge bg-info" v-else>No</span> 
                                            </td>
                                        </tr> 
                                      </tbody>
                                    </table>
                                  </div> 
                            <div class="col-md-12"> 
                              <div class="form-row">
                                <div class="form-group col-md-3">
                                    
                                </div>
                              </div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->  
              </div>
            </div>
          </div>
        </Modal>

        <Modal @close="masterDataUploadModal" :modalActive="masterModal">
          <div class="modal-content scrollbar-width-thin product-upload-modal ">
            <div class="modal-header"> 
                <h3>Master Data Uploads</h3>
                <button @click="masterDataUploadModal" type="button" class="btn btn-default">X</button>
            </div>
             <div class="modal-body">  
               <div class="row">
                <div class="col-md-12">
                  <form @submit.prevent="uploadMasterDataExcel()" role="form" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group">   
                          <div class="mb-3">
                          <a :href="samplefile_master_data_url"  @click="downloadSampleMasterDataFile" class="btn btn-info" download><i class="fa fa-download" aria-hidden="true" ></i> Download Sample file</a>
                        </div>
                        </div> 
                      </div> 
                      <div class="row">
                        <div class="form-group">  
                          <span class="btn btn-primary btn-file">
                            <span class="fileinput-new" id="master-data-excel-file">
                              <i class="fas fa-file-excel"></i> Choose Excel
                            </span>
                            <input type="file" id="maste_file" ref="maste_file" class="form-control upload-file" @change="handleFileMasterDataUpload()"/>
                          </span>
                          <button type="submit" class="btn btn-info float-right"><i class="fa fa-upload" aria-hidden="true"></i> {{ button_name }}</button>
                        </div>
                      </div>
                  </form>
              </div>
            </div>
            </div>
          </div>
        </Modal>

        <Modal @close="toggleImportModal" :modalActive="importModal">
          <div class="modal-content scrollbar-width-thin product-upload-modal ">
            <div class="modal-header"> 
                <h3>Import Products</h3>
                <button @click="toggleImportModal" type="button" class="btn btn-default">X</button>
            </div>
             <div class="modal-body">  
               <div class="row">
                <div class="col-md-12">
                  <form @submit.prevent="uploadExcel()" role="form" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group">   
                          <div class="mb-3">
                          <a :href="samplefile_url"  @click="downloadSampleFile" class="btn btn-info" download><i class="fa fa-download" aria-hidden="true" ></i> Download Sample file</a>
                        </div>
                        </div> 
                      </div> 
                      <div class="row">
                        <div class="form-group">  
                          <span class="btn btn-primary btn-file">
                            <span class="fileinput-new" id="excel-file">
                              <i class="fas fa-file-excel"></i> Choose Excel
                            </span>
                            <input type="file" id="file" ref="file" class="form-control upload-file" @change="handleFileUpload()"/>
                          </span>
                          <button type="submit" class="btn btn-info float-right"><i class="fa fa-upload" aria-hidden="true"></i> {{ button_name }}</button>
                        </div>
                      </div>
                  </form>
              </div>
            </div>
            </div>
          </div>
        </Modal>
      </div>
    </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import VueEditor from "./../helper/Modal";
import { ref } from "vue";
import Form from 'vform'  
import Vue3Barcode from 'vue3-barcode'
import axios from 'axios';
import { reactive, toRefs } from 'vue' 
import Bulkupload from './Bulkupload'; 
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
 
import BarcodeGenerator from "@/components/BarcodeGenerator.vue"; 
import BarcodeDemo from "@/views/product/BarcodeDemo"; 

export default {
    name: 'Products',
    components: { 
      Modal,
      Vue3Barcode, 
      Bulkupload:Bulkupload,
      Datatable,
      Pagination, 
      BarcodeGenerator,
      BarcodeDemo
    },
    props:{
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
            editor: null,
            barcodeOprions:{ lineColor: '#0000FF' },
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false, 
            loading: true, 
            errors: {},
            logoPreview:'',
            btn:'Submit',
            isShow: false,
            showModal: false, 
            errors: {},
            items: [],
            product:'',
            parent_id:0,
            parents:[],
            childs:[],
            brands:[], 
            units:[], 
            sizes:[],
            colors:[],
            products:[],
            isValid: true,
            regex: '/[0-9]/',
            mynumber: "",
            suppliers:[], 
            thumbnailPreview:'',
            attachmentsPreview:false,             
            producttaxs:[{id: 1, name: 'Exclusive'}, {id: 2, name: 'Inclusive'}], 
            producttax:[{id: 1, name:'No tax'}], 
            value: null,
            user_define_barcode: [],
            product_tags: [],
            form: new Form({
                id:'',
                product_type:'standard',
                product_name:'',
                product_native_name:'',
                product_code:'',
                category_id:'',
                sub_category_id:'', 
                brand_id:'',
                barcode_symbology:'', 
                min_order_qty: "1",
                cost_price:'',
                depo_price:'',
                mrp_price:'', 
                abp_price:'', 
                abp_qty:'', 
                tax_method:'',
                discount:'',
                product_tax:'',
                alert_quantity:0,
                product_tags:[],
                thumbnail:'',    
                supplier_id:[],  
                size: [],
                color: [], 
                attachments: [],
                short_description: "", 
                description: "",
                status: 1,   
                is_expirable:0,
                is_ecommerce:0,
                user_define_barcode:[],
                purchase_measuring_unit:'',
                sales_measuring_unit:'',
                convertion_rate:'',
                carton_size:'',
                carton_c_p_u:'',
                is_outlet_management:'',
                allow_checkout_when_out_of_stock:0
            }),
            file : '',
            maste_file : '',
            attachment : '',
            upload_size : 0, 
            button_name : "Upload",
            validation_error : null,
            multiclasses: { 
              clear: '',
              clearIcon: '', 
            }, 
            columns: [ 
                {
                    label: 'Product Name',
                    name: 'product_name',           
                    width: '25%'
                },  
                {
                    label: 'Native Name',
                    name: 'product_native_name',           
                    width: '25%'
                },   
                {
                    label: 'Cost Price',
                    name: 'cost_price',
                    width: '10%'
                },
                {
                    label: 'MRP Price',
                    name: 'mrp_price',
                    width: '10%'
                },
                {
                    label: 'Product Code',
                    name: 'product_code',
                    width: '15%'
                },  
                {
                    label: 'Category Name',
                    name: 'category.name',
                    width: '10%'
                },
                {
                    label: 'Alert Qty',
                    name: 'alert_quantity',
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
                sortKey: 'product_name', 
                category_id:'',
                sub_cat_id:''
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
                links:[],
            },
            samplefile_url: this.sampleFileUrl+'/import_excel_demo/sample-products.xlsx',
            samplefile_master_data_url: this.sampleFileUrl+'/import_excel_demo/sample-master-data.xlsx',
            filter:{
              sub_cat_id:'',
              category_id:'',
              brand_id:'',
            },
            barcodeWidth:1.2,
            barcodeHeight:30,
            barcodeReRender:true,
            singleBarcodeEnable: false,
            barcode_symbology:'CODE128',
            color:'Black',
            barcodeQty:1,
            barcode_symbology_list:[{name:'CODE128'},{name:'EAN13'}, {name:'UPC'},{name:'CODE39'},{name:'MSI'}, {name:'codabar'},{name:'ITF14'} ]
        };
    },

    setup() {
        const modalActive = ref(false);
        const modalActiveBarcode = ref(false);
        const modalShow = ref(false);
        const importModal = ref(false); 
        const masterModal = ref(false); 
        return { modalActive, modalActiveBarcode, modalShow, importModal, masterModal };
    },
    beforeUnmount() {
      //this.editor.destroy()
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
        toggleModal2: function() {  
          this.modalShow = !this.modalShow;
        },
        toggleBarcode: function() {
          this.singleBarcodeEnable = false;
          this.modalActiveBarcode = !this.modalActiveBarcode;
        },
        toggleImportModal: function() {  
          this.importModal = !this.importModal;
        },
        masterDataUploadModal: function() {  
          this.masterModal = !this.masterModal;
        }, 
        singleBarcode : function(show){
          this.modalActiveBarcode = !this.modalActiveBarcode;
          this.singleBarcodeEnable = true;
          this.product = show;
          this.product.barcode_symbology = this.barcode_symbology;
          this.barcodeQty = this.product.quantity ? this.product.quantity : 1 ;
          console.log(show) 
        },
        forceRerender() {
          this.barcodeReRender = false;
          this.$nextTick(() => { 
              this.barcodeReRender = true;
          });
        },
        barcodeSetting: function(){ 
          this.forceRerender();
        },
        selectProduct(event) {   
          this.forceRerender();
          this.singleBarcodeEnable = true;
          this.product = this.items.find(e => e.id == event);    
          console.log('this.product', this.product)
        },
        print() { 
          this.$htmlToPaper("barcodePrint");
        }, 
        edit: function(item) {  
            axios.get(this.apiUrl+'/products/'+item.id, this.headerjson)
            .then((res) => {
              let getItem = res.data.data;  
              this.fetchChildsCat(item.category_id); 
              getItem.user_define_barcode.forEach(fields => {                
                if(getItem.product_code != fields){ 
                  this.user_define_barcode.push(fields);
                }
              }); 
              getItem.product_tags.forEach(fields => {   
                this.product_tags.push(fields);
              });
              this.form.fill(res.data.data);
            })
            .catch((response) => {  
            })  
            this.btn='Update';
            this.editMode = true;
            this.thumbnailPreview='';
            this.attachmentsPreview = false;
            this.toggleModal(); 
        },
        show: function(item) {     
          axios.get(this.apiUrl+'/products/'+item.id, this.headers).then(res => {
              if(res.status == 200){  
                this.product = res.data.data
                this.modalShow = !this.modalShow;  
              }else{
                this.$toast.error(res.data.message);
              }
              console.log(res.data.data)
          }).catch(err => {  
              this.$toast.error(err.response.data.message);
              if(err.response.status == 422){
                  this.errors = err.response.data.errors 
              }
          }) 
        }, 
        submitForm: function(e) { 
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('product_type', this.form.product_type);            
            formData.append('product_name', this.form.product_name);
            formData.append('product_native_name', this.form.product_native_name);
            formData.append('product_code', this.form.product_code);
            formData.append('category_id', this.form.category_id);
            formData.append('sub_category_id', this.form.sub_category_id);
            formData.append('brand_id', this.form.brand_id);
            formData.append('barcode_symbology', this.form.barcode_symbology); 
            formData.append('min_order_qty', this.form.min_order_qty); 
            formData.append('cost_price', this.form.cost_price); 
            formData.append('depo_price', this.form.depo_price); 
            formData.append('mrp_price', this.form.mrp_price); 
            formData.append('abp_price', this.form.abp_price); 
            formData.append('abp_qty', this.form.abp_qty); 
            formData.append('tax_method', this.form.tax_method); 
            formData.append('product_tax', this.form.product_tax); 
            formData.append('discount', this.form.discount); 
            formData.append('alert_quantity', this.form.alert_quantity); 
            formData.append('product_tags', this.form.product_tags);  
            formData.append('supplier_id', this.form.supplier_id); 
            formData.append('size', this.form.size); 
            formData.append('color', this.form.color);  
            formData.append('short_description', this.form.short_description); 
            formData.append('description', this.form.description); 
            formData.append('status', this.form.status); 
            formData.append('is_expirable', this.form.is_expirable); 
            formData.append('is_ecommerce', this.form.is_ecommerce);  
            formData.append('purchase_measuring_unit', this.form.purchase_measuring_unit); 
            formData.append('sales_measuring_unit', this.form.sales_measuring_unit); 
            formData.append('convertion_rate', this.form.convertion_rate); 
            formData.append('carton_size', this.form.carton_size); 
            formData.append('carton_c_p_u', this.form.carton_c_p_u); 
            formData.append('is_outlet_management', this.form.is_outlet_management);  
            formData.append('allow_checkout_when_out_of_stock', this.form.allow_checkout_when_out_of_stock);   
            if(this.form.user_define_barcode.length >0){
              for (var x = 0; x < this.form.user_define_barcode.length; x++) {
                if(this.form.product_code !=this.form.user_define_barcode[x]){
                  formData.append('user_define_barcode[]', this.form.user_define_barcode[x]);
                } 
              }
            } 
            if(this.editMode){
                formData.append('_method', 'put');
                if(this.thumbnailPreview){
                  this.form.thumbnail ? formData.append('thumbnail', this.form.thumbnail, this.form.thumbnail.name) : '';
                } 
                if(this.attachmentsPreview){
                  for (var x = 0; x < this.form.attachments.length; x++) { 
                    formData.append('attachments[]', this.form.attachments[x], this.form.attachments[x].name);
                  }
                }
                var postEvent = axios.post(this.apiUrl+'/products/'+this.form.id, formData, this.headers);
                
            }else{ 
                if(this.attachmentsPreview){
                  for (var x = 0; x < this.form.attachments.length; x++) { 
                    formData.append('attachments[]', this.form.attachments[x], this.form.attachments[x].name);
                  }
                }
                this.form.thumbnail ? formData.append('thumbnail', this.form.thumbnail, this.form.thumbnail.name) : '';
                var postEvent = axios.post(this.apiUrl+'/products', formData, this.headers);
            }
            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){ 
                  this.toggleModal();
                  this.$toast.success(res.data.message); 
                  //window.location.reload();
                }else{
                  this.$toast.error(res.data.message);
                } 
            }).catch(err => { 
                this.isSubmit = false; 
                this.disabled = false; 
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors ; 
                }
                
            }).finally(( res) => { 
              this.fetchItems(); 
            }); 
        },
        onkeyPress: function(field) { 
            for (var k in this.errors){     // Loop through the object
                if(k==field){      // If the current key contains the string we're looking for 
                  console.log('delete', this.errors[k])
                  delete this.errors[k];  // Delete obj[key];
                }
            }  
        }, 
        validateNumber:function(e,field){ 
          const number = e.target.value;
          if(this.isNumber(number)){
            this.form[field] = number;
            this.onkeyPress(this.form[field]);
          }else{
            this.form[field] = '';
          }
        }, 
        isNumber: function(value) {
            if ((undefined === value) || (null === value)) {
                return false;
            }
            if (typeof value == 'number') {
                return true;
            }
            return !isNaN(value - 0);
        },
        onImageChange(e) {
            this.form.thumbnail = e.target.files[0]
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return; 
            this.createImage(files[0]);
        },
        createImage(file) {
            let reader = new FileReader();
            let vm = this;
            reader.onload = (e) => { 
                vm.thumbnailPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        getAttachmentSize() {
          this.upload_size = 0; // Reset to beginningƒ
          this.form.attachments.map((item) => {
            this.upload_size += parseInt(item.size);
          }); 
          this.upload_size = Number(this.upload_size.toFixed(1)); 
        },
        removeAttachment(attachment) {
          this.form.attachments.splice(
            this.form.attachments.indexOf(attachment),
            1
          ); 
          this.getAttachmentSize();
        },
        uploadFieldChange(e) { 
          this.attachmentsPreview = true;
          var files = e.target.files || e.dataTransfer.files; 
          if (!files.length) return;
          for (var i = files.length - 1; i >= 0; i--) {
            this.form.attachments.push(files[i]);
          } 
          document.getElementById("attachments").value = [];
        },
        
        switchSelect(event){   
          this.fetchChildsCat(event)
        },
        random_num(min=12,max=15){
          const d1 = new Date();
          const result = d1.getTime(); 
          this.form.product_code = result; 
          this.onkeyPress('product_code')
        }, 
        async fetchHelpers() {
            await axios.get(this.apiUrl+'/products-helper',this.headers)
            .then((res) => {
                this.parents = res.data.data.categories.map(({ id, name }) => ({ label: name, value: id })); 
                this.brands = res.data.data.brands; 
                this.units = res.data.data.units; 
                this.suppliers = res.data.data.suppliers.map(({ id, name }) => ({ label: name, value: id })); 
                this.colors = res.data.data.colors.map(({ id, name }) => ({ label: name, value: id })); 
                this.sizes = res.data.data.sizes.map(({ id, name }) => ({ label: name, value: id }));  
                this.producttax = res.data.data.taxes; 
            })
            .catch((response) => {  
            }) 
        },
        fetchChildsCat(parent_id) {
            this.childs = [];
            axios.get(this.apiUrl+'/product_categories?parent_id='+parent_id, this.headerjson)
            .then((res) => { 
                this.childs = res.data.data.map(({ id, name }) => ({ label: name, value: id })); 
            })
            .catch((err) => {  
            }) 
        }, 
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            document.getElementById("excel-file").innerHTML = '<i class="fas fa-file-excel"></i> ' + this.file.name;
        }, 

        uploadExcel: function(e) {  
            this.button_name = "Uploading...";
            const formData = new FormData();
            formData.append("file", this.file);
                    
            var postEvent = axios.post(this.apiUrl+'/products-import', formData, this.headers);

            postEvent.then(res => {
                if(res.status == 200){
                    this.file = '';
                    this.toggleImportModal();
                    this.button_name = "Upload";
                    this.$toast.error(res.data.data.exists); 
                    this.$toast.error(res.data.data.code); 
                    this.$toast.success(res.data.data.inserted);  
                    // window.location.reload();

                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.file = '';
                this.$refs.file.value=null;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }

            });
        },

        handleFileMasterDataUpload(){
            this.file = this.$refs.maste_file.files[0];
            console.log('this.file', this.file)
            document.getElementById("master-data-excel-file").innerHTML = '<i class="fas fa-file-excel"></i> ' + this.file.name;
        }, 
        uploadMasterDataExcel(){
            this.button_name = "Uploading...";
            let formData = new FormData();
            formData.append('file', this.file); 
            axios.post(this.apiUrl+'/master-data-upload/',formData,this.headers)
            .then((res) => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){  
                  //this.toggleModal();
                  this.$toast.error(res.data.data.exists); 
                  this.$toast.error(res.data.data.code); 
                  this.$toast.success(res.data.data.inserted);  

                }else{
                  this.$toast.error(res.data.message);
                } 
            })
            .catch((err) => { 
              this.isSubmit = false; 
              this.disabled = false; 
              this.$toast.error(err.response.data.message);
              if(err.response.status == 422){
                  this.errors = err.response.data.errors ; 
              } 
            })
            .finally((ress) => {
              this.loading = false; 
            }); 
        },
        // datatable For Pagination 
        fetchItems(url = this.apiUrl+'/product/list') { 
            this.tableData.draw++;
            axios.get(url, {params:this.tableData, headers:this.headerparams})
            .then((response) => {
                let data = response.data.data;   
                if(this.tableData.draw = data.draw) { 
                  this.items = data.data.data;
                  this.products = data.data.data.map(({ id, product_name }) => (
                  { label: product_name, value: id }
                  )); 
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

        configPagination(data){
            this.pagination.lastPage = data.last_page;
            this.pagination.currentPage = data.current_page;
            this.pagination.total   = data.total ? data.total : 0;
            this.pagination.lastPageUrl = data.last_page_url;
            this.pagination.nextPageUrl = data.next_page_url;
            this.pagination.prevPageUrl = data.prev_page_url;
            this.pagination.from = data.from ? data.from : 0;
            this.pagination.to = data.to ? data.to : 0;  
            this.pagination.links = data.links;
        },

        sortBy(key,sortable) {
            this.tableData.sortKey = key;
            //this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = sortable; ///this.sortOrders[key] === 1 ? 'asc' : 'desc'; 
            this.fetchItems();
        },
        setPage(data){  
            this.fetchItems(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },

        filterData(){
          this.tableData.category_id = this.filter.category_id ? this.filter.category_id : '' ;
          this.tableData.sub_cat_id = this.filter.sub_cat_id ? this.filter.sub_cat_id : '';  
          this.tableData.brand_id = this.filter.brand_id ?  this.filter.brand_id : ''; 
          this.fetchItems(); 
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
                    axios.delete(this.apiUrl+'/products/'+item.id, this.headers).then(res => {
                        if(res.status == 200){ 
                          this.fetchItems();
                          this.$toast.success(res.data.message); 
                        }else{
                          this.$toast.error(res.data.message);
                        } 
                    }).catch(err => {  
                        this.$toast.error(err.response.data.message);
                        if(err.response.status == 422){
                            this.errors = err.response.data.errors 
                        }
                    }) 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
                }
            }); 
        },
        downloadSampleFile(){ 
          this.samplefile_url; 
        },
        downloadSampleMasterDataFile(){ 
          this.samplefile_master_data_url; 
        }
        // datatable For Pagination 
    },
    async created() {    
      await this.fetchItems(); 
      await this.fetchHelpers();        
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
};
</script>
<style>
@media print {
  .d-print-none {
    display: none!important;
  } 
  .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6,
  .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
    float: left;               
  }
  .col-sm-12 {
    width: 100%;
  }
  .col-sm-11 {
    width: 91.66666666666666%;
  }
  .col-sm-10 {
    width: 83.33333333333334%;
  }
  .col-sm-9 {
    width: 75%;
  }
  .col-sm-8 {
    width: 66.66666666666666%;
  }
 .col-sm-7 {
    width: 58.333333333333336%;
 }
 .col-sm-6 {
    width: 50%;
 }
 .col-sm-5 {
    width: 41.66666666666667%;
 }
 .col-sm-4 {
    width: 33.33333333333333%;
 }
 .col-sm-3 {
    width: 25%;
 }
 .col-sm-2 {
    width: 16.666666666666664%;
 }
 .col-sm-1 {
    width: 8.333333333333332%;
  }            
} 
</style>
<style scoped>

.btn-file {
  overflow: hidden;
  position: relative;
  vertical-align: middle;
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
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

/*.svg-inline--fa.fa-random {
  margin: -33px 0px 0px 103px !important;
  background-color: #ccc;
  padding: 9px;
  z-index: 100;
  display: inline-block;
  position: absolute;
  border-radius: 0px 3px 3px 0px;
}*/ 
  .multiselect-tag.is-user {
    padding: 5px 8px;
    border-radius: 22px;
    background: #35495e;
    margin: 3px 3px 8px;
  }

  .multiselect-tag.is-user img {
    width: 18px;
    border-radius: 50%;
    height: 18px;
    margin-right: 8px;
    border: 2px solid #ffffffbf;
  }

  .multiselect-tag.is-user i:before {
    color: #ffffff;
    border-radius: 50%;;
  }

  .user-image {
    margin: 0 6px 0 0;
    border-radius: 50%;
    height: 22px;
  }
  .multiselect-clear { 
    display: inline-block !important;
    float: right !important;;
  }
  .multiselect { 
    display: block;
    position: relative; 
  }
  .multiselect.is-active{
    z-index: 1;
  }

  .modal-content.scrollbar-width-thin.product-show-modal {
    width:  700px;
    margin: auto;
  }
  .modal-content.scrollbar-width-thin.product-upload-modal {
    width: 400px;
  }
  .modal-content.scrollbar-width-thin.product-add-modal {
    width: 95% !important;
    margin: 0px auto;
  }
  .modal-content.scrollbar-width-thin.product-barcode-modal {
     
    max-width:900px;
    min-width:800px;
    margin: 0px auto;
  }
</style>
<style src="@vueform/multiselect/themes/default.css"></style>