
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Supplier </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Supplier List</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <button type="button" class="btn btn-primary float-right" @click="toggleModal()">
                            Add New
                        </button> 
                    </div>
                </div>
            </div>
        </div>

        <Modal @close="toggleModal()" :modalActive="modalActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <h3>Supplier Add Or Edit</h3>
                    <button @click="toggleModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form id="supplier-form" @submit.prevent="submitForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="name">Name *</label>
                                        <input type="text" class="form-control border " @keyup="ledgerAccountNameSetup($event.target.value)" @keypress="onkeyPress('name')" v-model="form.name" id="name" placeholder="Supplier Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.name">
                                            {{errors.name[0]}}
                                        </div>
                                    </div> 
                                </div>
                                <div class="row"> 
                                    <div class="form-group col-md-6">
                                        <label for="type_id">Type </label> 
                                        <select class="form-control border" v-model="form.type_id" @change="onkeyPress('type_id')" id="type_id">
                                            <option value="0">Select Supplier Type</option>
                                            <option v-for="(type, i) in supplier_types" :key="i" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>                                                         
                                        <div class="invalid-feedback" v-if="errors.type_id">
                                            {{errors.type_id[0]}}
                                        </div>                                        
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="mb-3">
                                            <label for="company_id">Company </label> 
                                            <select class="form-control border" v-model="form.company_id" @change="onkeyPress('company_id')" id="company_id">
                                                <option value="0">Select company</option>
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
                                    <div class="form-group col-md-6">
                                        <label for="address">Address *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('address')" v-model="form.address" id="address" placeholder="Enter Address"> 
                                        <div class="invalid-feedback" v-if="errors.address">
                                            {{errors.address[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="phone">Phone</label>
                                        <input type="tel" class="form-control border " @keypress="onkeyPress('phone')" v-model="form.phone" id="phone" placeholder="Contact Person Phone"> 
                                        <div class="invalid-feedback" v-if="errors.phone">
                                            {{errors.phone[0]}}
                                        </div>
                                    </div>
                                    
                                </div> 

                                <!-- <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="district_id">District </label>
                                        <select class="form-control border" v-model="form.district_id" @change="onkeyPress('district_id')" id="district_id">
                                            <option value="0">Select District</option>
                                            <option v-for="(district, index) in districts" :value="district.id" :key="index">
                                                {{district.name}}
                                            </option>
                                        </select> 
                                        <div class="invalid-feedback" v-if="errors.district_id">
                                            {{errors.district_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="area_id">Area </label>
                                        <select class="form-control border" v-model="form.area_id" @change="onkeyPress('area_id')" id="area_id">
                                            <option value="0">Select Area</option>
                                            <option v-for="(area, index) in areas" :value="area.id" :key="index">
                                                {{area.name}}
                                            </option>
                                        </select> 
                                        <div class="invalid-feedback" v-if="errors.area_id">
                                            {{errors.area_id[0]}}
                                        </div>
                                    </div>
                                </div>  -->

                                <div class="row">  
                                    <div class="form-group col-md-6">
                                        <label for="contact_person_name">Contact Person Name</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('contact_person_name')" v-model="form.contact_person_name" id="contact_person_name" placeholder="Contact Person Name"> 
                                        <div class="invalid-feedback" v-if="errors.contact_person_name">
                                            {{errors.contact_person_name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control border " @keypress="onkeyPress('email')" v-model="form.email" id="email" placeholder="Contact Person Email"> 
                                        <div class="invalid-feedback" v-if="errors.email">
                                            {{errors.email[0]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="form-group">
                                            <label style="width: 100%">Image (128X128)</label> <br>
                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="position: relative;">
                                            <span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Chose Image</span>
                                            <span class="fileinput-exists" style="display:none">Change Image</span><input type="file" name="..." @change="onImageChange"/></span>
                                            </div> 
                                            <div class="invalid-feedback" v-if="errors.logo_image">
                                            {{errors.logo_image[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="sstatus">Status</label>
                                        <select class="form-control border" v-model="form.status" @change="onkeyPress('status')" id="sstatus">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.status">
                                            {{errors.status[0]}}
                                        </div>
                                    </div>
                                        
                                </div>
                            </div> 

                            <div class="col-md-4">
                                <p>Photo Preview</p>
                                <img :src="imagePreview" v-if="form.logo_image" width="200" >
                                <div v-if="editMode & !imagePreview">
                                    <img :src="form.logo_image" v-if="form.logo_image" width="200">
                                </div>
                            </div>

                        </div>

                        <hr>

                        <!-- <div class="row">
                            <div class="form-group col-md-12" style="text-align:left">

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" style="margin-top: 0px" type="checkbox" v-model="form.outlet_receive" id="outlet_receive" true-value="1" false-value="0">
                                    <label class="form-check-label" for="outlet_receive"> Outlet/Store Can Receive</label>
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Payment Terms and Conditions *</label>
                                    </div>

                                    <div class="col-md-12" style="text-align:left; margin-bottom: 20px">

                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.payment_terms_conditions" id="payment_terms_conditions1" :value="1" @change="termConditionChange($event.target.value)">
                                            <label class="form-check-label" for="payment_terms_conditions1"> After Sale</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.payment_terms_conditions" id="payment_terms_conditions2" :value="2" @change="termConditionChange($event.target.value)">
                                            <label class="form-check-label" for="payment_terms_conditions2"> Sale After Commission</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.payment_terms_conditions" id="payment_terms_conditions3" :value="3" @change="termConditionChange($event.target.value)">
                                            <label class="form-check-label" for="payment_terms_conditions3">Credit</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.payment_terms_conditions" id="payment_terms_conditions4" :value="4" @change="termConditionChange($event.target.value)">
                                            <label class="form-check-label" for="payment_terms_conditions4">Cash Purchase</label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="payment_matured_days" v-if="!pmdDisabled">Payment Matured Day(s) *</label>
                                        <label for="payment_matured_days" v-else> Payment Matured Day(s)</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('payment_matured_days')" v-model="form.payment_matured_days" id="payment_matured_days" :disabled="pmdDisabled"> 
                                        <div class="invalid-feedback" v-if="errors.payment_matured_days">
                                            {{errors.payment_matured_days[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="commission_percent" v-if="!cpDisabled">Commission Percent (%) *</label>
                                        <label for="commission_percent" v-else>Commission Percent (%)</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('commission_percent')" v-model="form.commission_percent" id="commission_percent" :disabled="cpDisabled"> 
                                        <div class="invalid-feedback" v-if="errors.commission_percent">
                                            {{errors.commission_percent[0]}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mt-3 mb-3">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Supply Schedule *</label>
                                    </div>

                                    <div class="col-md-12" style="text-align:left; margin-bottom: 20px">

                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.supply_schedule" id="supply_schedule1" :value="1">
                                            <label class="form-check-label" for="supply_schedule1"> Daily</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.supply_schedule" id="supply_schedule2" :value="2">
                                            <label class="form-check-label" for="supply_schedule2"> Weekly</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.supply_schedule" id="supply_schedule3" :value="3">
                                            <label class="form-check-label" for="supply_schedule3"> Monthly</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.supply_schedule" id="supply_schedule4" :value="4">
                                            <label class="form-check-label" for="supply_schedule4"> As Per Requirement</label>
                                        </div>
                                    </div>

                                </div>

                                <!-- <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left; margin-bottom: 10px;">Adjust Specify </label>
                                    </div>

                                    <div class="col-md-6" style="text-align:left; margin-bottom: 20px">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Damage Product </label>
                                        <div class="form-check form-check-inline ">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.damage_product" id="damage_product1" :value="1">
                                            <label class="form-check-label" for="damage_product1"> Replace</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.damage_product" id="damage_product2" :value="2">
                                            <label class="form-check-label" for="damage_product2"> Return</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="text-align:left; margin-bottom: 20px">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Slow Moving </label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.slow_moving_product" id="slow_moving_product1" :value="1">
                                            <label class="form-check-label" for="slow_moving_product1"> Replace</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.slow_moving_product" id="slow_moving_product2" :value="2">
                                            <label class="form-check-label" for="slow_moving_product2"> Return</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="text-align:left; margin-bottom: 20px">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Short Dated </label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.short_dated_product" id="short_dated_product1" :value="1">
                                            <label class="form-check-label" for="short_dated_product1"> Replace</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.short_dated_product" id="short_dated_product2" :value="2">
                                            <label class="form-check-label" for="short_dated_product2"> Return</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="text-align:left; margin-bottom: 20px">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left;">Expire Product </label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.expire_product" id="expire_product1" :value="1">
                                            <label class="form-check-label" for="expire_product1"> Replace</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" style="margin-top: 0px" type="radio" v-model="form.expire_product" id="expire_product2" :value="2">
                                            <label class="form-check-label" for="expire_product2"> Return</label>
                                        </div>
                                    </div>

                                </div> -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left; margin-bottom: 20px; background-color: #313A46; padding: 5px 5px; color: #ffffff">Account Ledger Setup *</label>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="supplier_payable_account">Supplier Payable Account Ledger *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('supplier_payable_account')" v-model="form.supplier_payable_account" id="supplier_payable_account" placeholder="Supplier Payable Account"> 
                                        <div class="invalid-feedback" v-if="errors.supplier_payable_account">
                                            {{errors.supplier_payable_account[0]}}
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-md-6">
                                        <label for="supplier_discount_account">Supplier Discount Account Ledger *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('supplier_discount_account')" v-model="form.supplier_discount_account" id="supplier_discount_account" placeholder="Supplier Discount Account"> 
                                        <div class="invalid-feedback" v-if="errors.supplier_discount_account">
                                            {{errors.supplier_discount_account[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="supplier_advance_account">Supplier Advance Account Ledger *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('supplier_advance_account')" v-model="form.supplier_advance_account" id="supplier_advance_account" placeholder="Supplier Advance Account"> 
                                        <div class="invalid-feedback" v-if="errors.supplier_advance_account">
                                            {{errors.supplier_advance_account[0]}}
                                        </div>
                                    </div> -->

                                </div>

                                <!-- <div class="row">
                                    <div class="col-md-12">
                                        <label style="border-bottom: 1px solid rgba(0, 0, 0, 0.1); width: 100%; text-align:left; margin-bottom: 20px;">Bank Information </label>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('bank_name')" v-model="form.bank_name" id="bank_name" placeholder="Bank Name"> 
                                        <div class="invalid-feedback" v-if="errors.bank_name">
                                            {{errors.bank_name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="branch_name">Branch Name</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('branch_name')" v-model="form.branch_name" id="branch_name" placeholder="Branch Name"> 
                                        <div class="invalid-feedback" v-if="errors.branch_name">
                                            {{errors.branch_name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="routing_no">Routing No</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('routing_no')" v-model="form.routing_no" id="routing_no" placeholder="Routing No"> 
                                        <div class="invalid-feedback" v-if="errors.routing_no">
                                            {{errors.routing_no[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="account_name">Account Name</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('account_name')" v-model="form.account_name" id="account_name" placeholder="Account Name"> 
                                        <div class="invalid-feedback" v-if="errors.account_name">
                                            {{errors.account_name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="account_number">Account No</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('account_number')" v-model="form.account_number" id="account_number" placeholder="Account Number"> 
                                        <div class="invalid-feedback" v-if="errors.account_number">
                                            {{errors.account_number[0]}}
                                        </div>
                                    </div>

                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="button" class="btn btn-danger" @click="toggleModal()"> Close</button>
                        <button type="submit" class="btn btn-primary " :disabled="disabled">
                            <span v-show="isSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>{{btn}} 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body table-responsive">
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
                                                    <select class="form-select" v-model="tableData.length" @change="getSuppliers()"> 
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
                                            <input type="text" class="form-control" style="float: right;" v-model="tableData.search" placeholder="Search..." @input="getSuppliers()">
                                        </div>
                                    </div>
                                </div>   
                            </template> 
                            <template #body >                            
                                <tbody v-if="suppliers.length > 0">
                                    <tr class=" " v-for="(item, i) in suppliers" :key="i">
                                        <td>{{ item.name}} </td>
                                        <td>{{ item.phone }} </td>
                                        <td>{{ item.email }} </td>
                                        <td>{{ item.address }} </td>
                                        <td>{{ item.company_name }} </td>
                                        <td v-if="item.status"><label class="badge bg-success"> Active </label></td>
                                        <td v-else><label class="badge bg-danger"> Active </label></td>
                                        <td>
                                            <div class="dropdown float-end">
                                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <!-- item-->
                                                    <!-- <a href="#" @click="show(item)" class="dropdown-item text-info"><i class="fa-solid fa-eye"></i> View</a>  -->
                                                    <a href="javascript:void(0);" class="dropdown-item text-warning" @click="edit(item)">
                                                    <i class="mdi mdi-circle-edit-outline me-1"></i>Edit</a>
                                                    <!-- item-->
                                                    <a href="javascript:void(0);" class="dropdown-item text-danger" @click="deleteItem(item)"><i class="mdi mdi-delete-outline me-1"></i>Remove</a>
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
import Datatable from '@/components/Datatable.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios'; 

export default {
    name: 'Supplier',
    components: {
        Modal,
        Datatable,
        Pagination,
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
            loading: true,
            isSubmit: false,
            isRequired: true,
            pmdDisabled: true,
            cpDisabled: true,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            imagePreview:'',
            btn:'Create',
            items: [],
            suppliers: [],
            supplier_types: [],
            districts: [],
            areas: [],
            roles: [],
            companies:[],
            form: new Form({
                id: '',
                name: '',
                type_id: 0,
                company_id: 0,
                address: '',
                district_id: 0,
                area_id: 0,
                contact_person_name: '',
                phone: '',
                email: '',
                logo_image: '',
                status: 1,
                payment_terms_conditions: 4,
                payment_matured_days: '',
                commission_percent: '',
                supply_schedule: 4,
                damage_product: 0,
                slow_moving_product: 0,
                short_dated_product: 0,
                expire_product: 0,
                bank_name: '',
                branch_name: '',
                routing_no: '',
                account_name: '',
                account_number: '',
                outlet_receive: 0,
                supplier_payable_account: '',
                // supplier_discount_account: '',
                // supplier_advance_account: '',


            }),
            columns: [ 
                {
                    label: 'Name',
                    name: 'name',           
                    width: '20%'
                },   
                {
                    label: 'Phone',
                    name: 'phone',
                    width: '20%'
                },
                {
                    label: 'Email',
                    name: 'phone',
                    width: '20%'
                },
                {
                    label: 'Address',
                    name: 'address',
                    width: '15%'
                },
                {
                    label: 'Company Name',
                    name: 'company_name',
                    width: '15%'
                },
                {
                    label: 'Status',
                    name: 'status',
                    width: '15%'
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
                sortKey: 'name', 
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
            }
        };
    },
    created() {
        // this.fetchIndexData();
        this.getSuppliers();
        this.fetchSupplierType();
        this.fetchDistrict();
        this.fetchArea();
        this.fetchCompany();
    },
    methods: { 
        toggleModal: function() {
            this.modalActive = !this.modalActive;  
            if(!this.modalActive){
                this.editMode = false;
                this.isRequired = true;
                this.btn='Create';
            } 
            this.errors = '';
            this.isSubmit = false;
            this.form.reset(); 
        },

        termConditionChange(value)
        {
            this.pmdDisabled = (value == 1) ? false : true;
            this.cpDisabled = (value == 2) ? false : true;
        },
        fetchIndexData() { 
            axios.get(this.apiUrl+'/suppliers')
            .then((res) => {
                this.items = res.data.data;
                console.log("res.data.data", res.data.data);
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message); 
            }).finally((ress) => {
                this.loading = false;
            });
        }, 
        fetchCompany() { 
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => {
                this.companies = res.data.data; 
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },  
        fetchSupplierType() { 
            axios.get(this.apiUrl+'/supplier_types', this.headers)
            .then((res) => {
                this.supplier_types = res.data.data; 
            })
            .catch((response) => { 
                console.log('res',response) 
            }).finally((ress) => {
                console.log('finally',ress);
            });
        },
        fetchDistrict() { 
            axios.get(this.apiUrl+'/districts', this.headers)
            .then((res) => {
                this.districts = res.data.data; 
            })
            .catch((response) => { 
                console.log('Res',response) 
            }).finally((ress) => {
                console.log('finally',ress);
            });
        },
        fetchArea() { 
            axios.get(this.apiUrl+'/areas',this.headers)
            .then((res) => {
                this.areas = res.data.data; 
            })
            .catch((response) => { 
                console.log('',response) 
            }).finally((ress) => {
                console.log(' finally',ress);
            });
        },

        // Ledger Account Name Setup
        ledgerAccountNameSetup: function(value) {
            this.form.supplier_payable_account = value;
            // this.form.supplier_discount_account = value + ' Discount';
            // this.form.supplier_advance_account = value + ' Advance';
        },

        add: function(e) {
            console.log('preventDefault', e)
            //this.close()
        },
        edit: function(item) { 
            this.btn='Update';
            this.editMode = true;
            this.isRequired = false;
            this.toggleModal();
            this.form.fill(item);
            this.imagePreview = '';
            this.pmdDisabled = (this.form.payment_terms_conditions == 1) ? false : true;
            this.cpDisabled = (this.form.payment_terms_conditions == 2) ? false : true;

        },
        checkForm: function(e) {

        },

        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;
            const formData = new FormData();  
            formData.append('name', this.form.name);            
            formData.append('type_id', this.form.type_id);
            formData.append('company_id', this.form.company_id);
            formData.append('address', this.form.address ?? '');
            formData.append('district_id', this.form.district_id);
            formData.append('area_id', this.form.area_id);
            formData.append('contact_person_name', this.form.contact_person_name ?? '');
            formData.append('phone', this.form.phone ?? '');
            formData.append('email', this.form.email ?? '');
            formData.append('status', this.form.status);
            formData.append('payment_terms_conditions', this.form.payment_terms_conditions ?? '');
            formData.append('payment_matured_days', this.form.payment_matured_days ?? '');
            formData.append('commission_percent', this.form.commission_percent);
            formData.append('supply_schedule', this.form.supply_schedule);
            formData.append('damage_product', this.form.damage_product);
            formData.append('slow_moving_product', this.form.slow_moving_product);
            formData.append('short_dated_product', this.form.short_dated_product);
            formData.append('expire_product', this.form.expire_product);
            formData.append('bank_name', this.form.bank_name ?? '');
            formData.append('branch_name', this.form.branch_name ?? '');
            formData.append('routing_no', this.form.routing_no ?? '');
            formData.append('account_name', this.form.account_name ?? '');
            formData.append('account_number', this.form.account_number ?? '');
            formData.append('outlet_receive', this.form.outlet_receive);
            formData.append('supplier_payable_account', this.form.supplier_payable_account ?? '');
            // formData.append('supplier_discount_account', this.form.supplier_discount_account ?? '');
            // formData.append('supplier_advance_account', this.form.supplier_advance_account ?? '');
            if(this.editMode){
                formData.append('_method', 'put');
                if(this.imagePreview){
                    this.form.logo_image ? formData.append('logo_image', this.form.logo_image, this.form.logo_image.name) : '';
                } 
                var postEvent = axios.post(this.apiUrl+'/suppliers/'+this.form.id, formData, this.headers);
            }else{ 
                this.form.logo_image ? formData.append('logo_image', this.form.logo_image, this.form.logo_image.name) : '';
                var postEvent = axios.post(this.apiUrl+'/suppliers', formData, this.headers);
            }           

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.toggleModal();
                    this.getSuppliers();
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
                console.log('catch',this.isSubmit)
            });
        },

        onImageChange(e) {
            this.form.logo_image = e.target.files[0]
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
                    axios.delete(this.apiUrl+'/suppliers/'+item.id, this.headerjson)
                    .then(res => {
                        if(res.status == 200){  
                            this.getSuppliers();
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
        },

        // For Pagination 
        getSuppliers(url = this.apiUrl+'/suppliers/list') {
            this.tableData.draw++;
            axios.get(url, {params:this.tableData,  headers:this.headerparams})
            .then((response) => {
                let data = response.data.data; 
                if(this.tableData.draw = data.draw) {
                    this.suppliers = data.data.data;
                    this.configPagination(data.data);
                }
            })
            .catch(errors => {
                this.$toast.error(errors.response.data.message);
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
            this.getSuppliers();
        },
        setPage(data){  
            this.getSuppliers(data.url); 
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
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
    width: 1000px
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

#supplier-form input {
    margin-bottom: 10px;
}
</style>