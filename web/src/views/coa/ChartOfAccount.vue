<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Accounts</li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Chart Of Accounts </a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right"> 
                        <!-- <button type="button" class="btn btn-primary float-right" @click="createAccountModal()">
                            <i class="mdi mdi-plus-outline"></i> Create New Account
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12"> 
                <div class="col-md-10">
                    <div class="row">  
                        <div class="col-md-6">
                            <div class="">
                                <label for="outlet_id"> Company </label>
                                <!-- @change="fetchGroupData($event.target.value)" -->
                                <select class="form-control" @change="loadDataBasedOnCompany($event.target.value)">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>

        <!-- Modal New Account-->
        <Modal @close="createAccountModal()" :modalActive="modalAccActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="createAccountModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <div class="">
                                            <label for="outlet_id"> Company </label>
                                            <select v-model="form.company_id" class="form-control" @change="fetchGroupData($event.target.value)">
                                                <option value="">--- Select Company ---</option>
                                                <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group col-md-12">
                                        <label for="type_id">Account Type *</label>
                                        <select class="form-control border" v-model="form.type_id" @change="onChangeParentType($event.target.value), onkeyPress('type_id')" id="type_id" :disabled="!editMode ? true : false">
                                            <option value="">--- Select Type ---</option>
                                            <option v-for="(item, i) in account_types" :key="i" :value="item.id">{{ item.type_name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.type_id">
                                            {{errors.type_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="detail_type_id">Detail Type *</label>
                                        <treeselect 
                                            v-model="form.detail_type_id"
                                            :multiple="false" 
                                            :options="detail_type_options"
                                            :normalizer="normalizerDetailTypes"
                                            :value-consists-of="valueConsistsOf"
                                            :default-expand-level="Infinity"
                                            :search-nested="true"                                                
                                            placeholder='Select Detail Type' 
                                            @select="onChangeDetailType"
                                            v-if="renderOptionComponent && editMode"
                                        />
                                        <select class="form-control border" v-model="form.detail_type_id" @change="onChangeDetailType($event.target.value), onkeyPress('detail_type_id')" id="detail_type_id" :disabled="!editMode ? true : false" v-if="!editMode">
                                            <option value="">--- Select Detail Type ---</option>
                                            <option v-for="(item, i) in detail_types" :key="i" :value="item.id">{{ item.type_name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.detail_type_id">
                                            {{errors.detail_type_id[0]}}
                                        </div>
                                    </div>
                                    <!-- <div class="form-group col-md-12" v-show="parentFieldShow">
                                        <label for="group_id">Parent Account </label>
                                        <select class="form-control border" v-model="form.parent_id" @change="onChangeParentAccount($event.target.value), onkeyPress('parent_id')" id="parent_id">
                                            <option value="0">--- Select Parent Account ---</option>
                                            <option v-for="(item, i) in parent_items" :key="i" :value="item.id">[{{item.ledger_code}}] {{ item.ledger_name }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.group_id">
                                            {{errors.group_id[0]}}
                                        </div>
                                    </div> -->
                                    
                                    <!-- <div class="form-group col-md-12" v-show="parentFieldShow">
                                        <label for="group_id">Parent Account *</label>
                                        <treeselect 
                                            v-model="form.parent_id"
                                            :multiple="false" 
                                            :options="account_options"
                                            :value-consists-of="valueConsistsOf"
                                            :default-expand-level="Infinity"
                                            :search-nested="true"                                                
                                            placeholder='Select parent account' 
                                            @select="onChangeParentAccount"
                                            v-if="renderOptionComponent"
                                        />
                                        <div class="invalid-feedback" v-if="errors.parent_id">
                                            {{errors.parent_id[0]}}
                                        </div>
                                    </div> -->

                                    <div class="form-group col-md-6">
                                        <label for="ledger_code">Account Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_code')" v-model="form.ledger_code" id="ledger_code" placeholder="Enter Account Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.ledger_code">
                                            {{errors.ledger_code[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="ledger_name">Account Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_name')" v-model="form.ledger_name" id="ledger_name" placeholder="Enter Account Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.ledger_name">
                                            {{errors.ledger_name[0]}}
                                        </div>
                                    </div>

                                    
                                    <div class="form-group col-md-6">
                                        <label for="ledger_type">Opening Balance Type *</label>
                                        <select class="form-control border " v-model="form.ledger_type" id="ledger_type"> 
                                            <option value="dr">Debit</option>
                                            <option value="cr">Credit</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="errors.ledger_type">
                                            {{errors.ledger_type[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="opening_balance">Opening Balance *</label>
                                        <input type="number" step="any" class="form-control border" @keypress="onkeyPress('opening_balance')" v-model="form.opening_balance" id="opening_balance" placeholder="Enter Opening Balance" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.opening_balance">
                                            {{errors.opening_balance[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="balance_date">Balance Date*</label>
                                        <input type="date" class="form-control border" @keypress="onkeyPress('balance_date')" v-model="form.balance_date" id="balance_date" placeholder="Enter Balance Date" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.balance_date">
                                            {{errors.balance_date[0]}}
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-md-12">
                                        <div class="mb-3 mt-3">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_control_transaction" v-model="form.is_control_transaction" true-value="1" false-value="0" @change="onkeyPress('is_control_transaction')">
                                                <label class="form-check-label" for="is_control_transaction">Is Control Transaction</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_control_transaction">
                                                {{errors.is_control_transaction[0]}}
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledAccountSubmit">
                            <span v-show="isAccountSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>Submit 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal New Children Account-->
        <Modal @close="createChildrenModal()" :modalActive="modalChildrenActive">
            <div class="modal-content scrollbar-width-thin">
                <div class="modal-header"> 
                    <button @click="createChildrenModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitChildrenAccountForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" v-model="form_children.type_id">
                                    <input type="hidden" v-model="form_children.detail_type_id">
                                    <input type="hidden" v-model="form_children.parent_id">
                                    <div class="form-group col-md-12">
                                        <label for="type_name">Account Type *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_name')" v-model="form_children.type_name" id="type_name" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.type_id">
                                            {{errors.type_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="detail_type_name">Detail Type *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('detail_type_name')" v-model="form_children.detail_type_name" id="detail_type_name" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.detail_type_id">
                                            {{errors.detail_type_id[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12" v-show="parentFieldShow">
                                        <label for="parent_name">Parent Account *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('parent_name')" v-model="form_children.parent_name" id="parent_name" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.parent_id">
                                            {{errors.parent_id[0]}}
                                        </div>
                                    </div>

                                    

                                    <div class="form-group col-md-6">
                                        <label for="ledger_code">Account Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_code')" v-model="form_children.ledger_code" id="ledger_code" placeholder="Enter Account Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.ledger_code">
                                            {{errors.ledger_code[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="ledger_name">Account Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('ledger_name')" v-model="form_children.ledger_name" id="ledger_name" placeholder="Enter Account Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.ledger_name">
                                            {{errors.ledger_name[0]}}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <div class="mb-3 mt-3">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" id="is_control_transaction" v-model="form_children.is_control_transaction" true-value="1" false-value="0" @change="onkeyPress('is_control_transaction')">
                                                <label class="form-check-label" for="is_control_transaction">Is Control Transaction</label>
                                            </div>
                                            <div class="invalid-feedback" v-if="errors.is_control_transaction">
                                                {{errors.is_control_transaction[0]}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary " :disabled="disabledChildrenSubmit">
                            <span v-show="isChildrenSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span>Submit 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>


        <!-- Create Account Type -->
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
                                    <div class="add_data" v-if="!accountTypeEdit">
                                        <div class="form-group col-md-12">
                                            <label for="class_id">Account Class/Group *</label>
                                            <input type="hidden" class="form-control border " @keypress="onkeyPress('class_id')" v-model="form_type.class_id" id="class_id"> 
                                            <input type="text" class="form-control border " @keypress="onkeyPress('class_name')" v-model="form_type.class_name" id="class_name" readonly> 
                                            <div class="invalid-feedback" v-if="errors.class_id">
                                                {{errors.class_id[0]}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="edit_data" v-if="accountTypeEdit">
                                        <div class="form-group col-md-12">
                                            <label for="class_id">Account Group *</label>
                                            <select class="form-control border " @change="onChangeAccountGroup($event.target.value), onkeyPress('class_id')" v-model="form_type.class_id" id="class_id"> 
                                                <option value="">--- Select Account Group ---</option>
                                                <option v-for="(group, i) in groups" :key="i" :value="group.id">{{ '['+group.code +'] '+group.name }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.class_id">
                                                {{errors.class_id[0]}}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="parent_type_id">Parent Type </label>
                                            <select class="form-control border " @change="onChangeAccountType($event.target.value), onkeyPress('parent_type_id')" v-model="form_type.parent_type_id" id="parent_type_id"> 
                                                <option value="0">--- Select Parent Type ---</option>
                                                <option v-for="(type, i) in parent_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.parent_type_id">
                                                {{errors.parent_type_id[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="type_code">Type Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_code')" v-model="form_type.type_code" id="type_code" placeholder="Enter Type Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.type_code">
                                            {{errors.type_code[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_name">Type Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_name')" v-model="form_type.type_name" id="type_name" placeholder="Enter Type Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.type_name">
                                            {{errors.type_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_status">Status *</label>
                                        <select class="form-control border " @change="onkeyPress('status')" v-model="form_type.status" id="type_status">
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
                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit" v-if="accountTypeEdit">
                            <span v-show="isTypeSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> Update 
                        </button>

                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit" v-else>
                            <span v-show="isTypeSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> Create 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Create Account Sub Type -->
        <Modal @close="createAccountSubTypeModal()" :modalActive="modalAccSubTypeActive">
            <div class="modal-content scrollbar-width-thin account_groups">
                <div class="modal-header"> 
                    <button @click="createAccountSubTypeModal()" type="button" class="btn btn-default">X</button>
                </div>
                <form @submit.prevent="submitAccountTypeForm()" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="create_data" v-if="!accountTypeEdit">
                                        <div class="form-group col-md-12">
                                            <label for="class_id">Parent Type *</label>
                                            <input type="hidden" class="form-control border " @keypress="onkeyPress('class_id')" v-model="form_type.class_id" id="class_id"> 
                                            <input type="hidden" class="form-control border " @keypress="onkeyPress('parent_type_id')" v-model="form_type.parent_type_id" id="parent_type_id"> 
                                            <input type="text" class="form-control border " @keypress="onkeyPress('parent_type_name')" v-model="form_type.parent_type_name" id="parent_type_name" readonly> 
                                            <div class="invalid-feedback" v-if="errors.parent_type_id">
                                                {{errors.parent_type_id[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="edit_data" v-if="accountTypeEdit">
                                        <div class="form-group col-md-12">
                                            <label for="class_id">Account Group *</label>
                                            <select class="form-control border " @change="onChangeAccountGroup($event.target.value), onkeyPress('class_id')" v-model="form_type.class_id" id="class_id"> 
                                                <option value="">--- Select Account Group ---</option>
                                                <option v-for="(group, i) in groups" :key="i" :value="group.id">{{ '['+group.code +'] '+group.name }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.class_id">
                                                {{errors.class_id[0]}}
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="parent_type_id">Parent Type </label>
                                            <select class="form-control border " @change="onChangeAccountType($event.target.value), onkeyPress('parent_type_id')" v-model="form_type.parent_type_id" id="parent_type_id"> 
                                                <option value="0">--- Select Parent Type ---</option>
                                                <option v-for="(type, i) in parent_types" :key="i" :value="type.id">{{ '['+type.type_code +'] '+type.type_name }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-if="errors.parent_type_id">
                                                {{errors.parent_type_id[0]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="type_code">Type Code *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_code')" v-model="form_type.type_code" id="type_code" placeholder="Enter Type Code" autocomplete="off" readonly> 
                                        <div class="invalid-feedback" v-if="errors.type_code">
                                            {{errors.type_code[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_name">Type Name *</label>
                                        <input type="text" class="form-control border " @keypress="onkeyPress('type_name')" v-model="form_type.type_name" id="type_name" placeholder="Enter Type Name" autocomplete="off"> 
                                        <div class="invalid-feedback" v-if="errors.type_name">
                                            {{errors.type_name[0]}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="type_status">Status *</label>
                                        <select class="form-control border " @change="onkeyPress('status')" v-model="form_type.status" id="type_status">
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
                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit" v-if="accountTypeEdit">
                            <span v-show="isTypeSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> Update 
                        </button>

                        <button type="submit" class="btn btn-primary " :disabled="disabledTypeSubmit" v-else>
                            <span v-show="isTypeSubmit">
                                <i class="fas fa-spinner fa-spin" ></i>
                            </span> Create 
                        </button>
                        
                    </div>
                </form>
            </div>
        </Modal>



        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center"> Chart Of Accounts </h3>
                    </div>
                    <div class="card-body">
                        <ChartOfAccountList 
                            v-for="(item, i) in items" :key="i" 
                            :account="item" 
                            :level="1" 
                            @add-type="addTypeItem"
                            @add-sub-type="addSubTypeItem"
                            @add-ledger="addLedgerItem" 
                            @edit-item="edit" 
                            @delete-item="deleteItem"
                        ></ChartOfAccountList>

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
</template>


<script>
import { mapGetters, mapActions } from "vuex";
import Modal from "./../helper/Modal";
import { ref, onMounted } from "vue";
import Form from 'vform'
import axios from 'axios';

import ChartOfAccountList from '@/components/ChartOfAccountList';

export default {
    name: 'PosLeftbar',
    components: {
        Modal,
        ChartOfAccountList
    },
    data() {
        return {
            loading: true,
            showModal: false,
            editMode:false,
            disabled: false,
            disabledAccountSubmit: false,
            modalAccActive:false,
            modalAccTypeActive: false,
            modalAccSubTypeActive: false,
            isAccountSubmit:false,
            modalChildrenActive: false,
            disabledChildrenSubmit: false,
            isChildrenSubmit:false,
            parentSelect: false,
            errors: {},
            btn:'Create',
            items: [],
            companies: [],
            account_item: '',
            all_account_types: [],
            parent_types: [],
            account_types: [],
            account_detail_types: [],
            detail_type_options: [],
            normalizerDetailTypes(node) {
                return {
                    id: node.id,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
            // detail_types: [{id: "", type_name: "Select Detail Type"}],
            detail_types: [],
            renderOptionComponent: true,
            valueConsistsOf: 'BRANCH_PRIORITY',
            account_options: [],
            all_ledgers: [],

            groups: [],
            isTypeSubmit: false,
            disabledTypeSubmit: false,
            accountTypeEdit: false,

            isSubTypeSubmit: false,
            disabledSubTypeSubmit: false,

            form_type: new Form({
                id:'',
                class_id: '',
                class_name: '',
                parent_type_id: 0,
                parent_type_name: '',
                type_code: '',
                type_name: '',
                status: 1
            }),

            form: new Form({
                id: '',
                ledger_code: '',
                ledger_name: '',
                type_id: '',
                company_id:'',
                detail_type_id: '',
                parent_id: null,
                ledger_type: 'dr',
                opening_balance: 0,
                balance_date: '',
                is_control_transaction: 0,
            }),

            form_children: new Form({
                ledger_code: '',
                ledger_name: '',
                type_id: 0,
                detail_type_id: 0,
                parent_id: null,
                ledger_type: 'dr',
                opening_balance: 0,
                balance_date: '',
                type_name: '',
                detail_type_name: '',
                parent_name: '',
                is_control_transaction: 0,
            }),
            parentFieldShow: true,
        };
    },
    created() {
        this.fetchCompanies();
        this.fetchCOAData();
        this.fetchCOAOptionsData();
        this.fetchAccountTypeData();
        this.fetchGroupData();
        this.fetchAccountLedgerData();
    },
    methods: { 
        forceRerender() {
            // Remove my-component from the DOM
            this.renderOptionComponent = false;

            this.$nextTick(() => {
                // Add the component back in
                this.renderOptionComponent = true;
            });
        },
        loadDataBasedOnCompany: function(companyId){
            this.fetchCOAData(companyId);
            this.fetchCOAOptionsData(companyId);
            this.fetchAccountTypeData(companyId);
            this.fetchGroupData(companyId);
            this.fetchAccountLedgerData(companyId);
        },

        createAccountTypeModal: function() {
            this.modalAccTypeActive = !this.modalAccTypeActive; 
            if(!this.modalAccTypeActive){
                this.accountTypeEdit = false;
                this.forceRerender();
                this.form_type.reset();
            }else{
                this.forceRerender();
            }
            this.errors = '';
            this.isTypeSubmit = false;
        },

        createAccountSubTypeModal: function() {
            this.modalAccSubTypeActive = !this.modalAccSubTypeActive; 
            if(!this.modalAccSubTypeActive){
                this.forceRerender();
                this.form_type.reset();
            }else{
                this.forceRerender();
            }
            this.errors = '';
            this.isSubTypeSubmit = false;
        },

        createAccountModal: function() {
            this.modalAccActive = !this.modalAccActive; 
            if(!this.modalAccActive){
                this.editMode = false;
                this.btn='Create';
                this.forceRerender();
                this.form.reset();  
            }else{
                this.forceRerender();
            }
            this.errors = '';
            this.isAccountSubmit = false;
        },

        createChildrenModal: function() {
            this.modalChildrenActive = !this.modalChildrenActive;
            this.forceRerender();
            if(!this.modalChildrenActive){
                this.form_children.reset();
            }else{

            }
            this.errors = '';
            this.isChildrenSubmit = false;
            
        },
        
        fetchCOAData(selecteID) { 
            // axios.get(this.apiUrl+'/account_groups', this.headerjson)
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccounts?company_id='+selecteID, this.headerjson)
            .then((res) => {
                this.items = res.data.data.accounts;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        fetchCOAOptionsData(selecteID) {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOption?company_id='+selecteID, this.headerjson)
            .then((res) => {
                this.account_options = res.data.data.accounts;
                this.all_options = res.data.data.accounts;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                //
            });
        },
        
        fetchGroupData(selecteID) {
            axios.get(this.apiUrl+'/account_classes?company_id='+selecteID, this.headerjson)
            .then((res) => {
                this.groups = res.data.data.account_classes;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchAccountTypeData(selecteID) { 
            axios.get(this.apiUrl+'/account_types/getAccountTypeList?company_id='+selecteID, this.headerjson) 
            .then((res) => {
                this.all_account_types = res.data.data;
                this.account_types = res.data.data.filter((item) => {
                    if(item.parent_type_id == 0) {
                        return item;
                    }
                });

                this.account_detail_types = res.data.data.filter((item) => {
                    if(item.parent_type_id != 0) {
                        return item;
                    }
                });

            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        fetchAccountLedgerData(selecteID) {
            axios.get(this.apiUrl+'/account_ledgers?company_id='+selecteID, this.headerjson)
            .then((res) => {
                this.all_ledgers = res.data.data;
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            }); 
        },

        fetchCompanies() {   
            axios.get(this.apiUrl+'/companies', this.headerjson)
            .then((res) => {
                console.log('res', res.data.data)
                this.companies = res.data.data;
            }).catch((err) => { 
                this.$toast.error(err.response.data.message);
            }).finally((ress) => {
                this.loading = false;
            });
        },

        onChangeAccountGroup(value) {
            this.parent_types = [];
            var group_id = value;
            this.form_type.parent_type_id = 0;
            if(group_id != '' || group_id != 0) {
                this.all_account_types.filter((titem) => {
                    if (titem.id != this.form_type.id  && titem.class_id == group_id) {
                        this.parent_types.push(titem);
                    }
                });
                var type_data = this.all_account_types.find(({id}) => id == this.form_type.id); 
                if(type_data.class_id != group_id) {
                    this.fetchTypeCode(group_id, "group");
                }else{ 
                    if(this.modalAccSubTypeActive) { 
                        this.fetchTypeCode(group_id, "group");
                    }else {    
                        this.form_type.type_code = type_data.type_code; 
                    }
                }
                
            }
        },

        onChangeAccountType(value) {
            var type_id = value;
            if(type_id != '' && type_id != 0) {

                if(this.modalAccSubTypeActive) {
                    var type_data = this.all_account_types.find(({id}) => id == this.form_type.id);
                    if(type_data.parent_type_id == type_id) {
                        this.form_type.type_code = type_data.type_code;
                    }else{   
                        this.fetchTypeCode(type_id, "type");
                    }
                }else{   
                    this.fetchTypeCode(type_id, "type");
                }
            }else{   

                if(this.modalAccSubTypeActive) {
                    this.fetchTypeCode(this.form_type.class_id, "group");
                }else{
                    var type_data = this.all_account_types.find(({id}) => id == this.form_type.id);           
                    this.form_type.type_code = type_data.type_code; 
                }
            }
        },

        onChangeParentType(value) {            
            this.form.detail_type_id = null;
            this.form.ledger_code = '';
            // this.form.ledger_name = '';
            if(value != '') {
                var master_type = this.all_account_types.find(({id}) => id == value);
                this.fetchDetailTypes(JSON.stringify(master_type));
                this.forceRerender();
            }
        },

        onChangeDetailType(value) {
            this.onkeyPress('detail_type_id');
            this.form.ledger_code = '';
            // this.form.ledger_name = '';
            if(value != '') {            
                if(this.editMode) {
                    var ledger_data = this.all_ledgers.find(({id}) => id == this.form.id);
                    if(ledger_data.detail_type_id == value.id) {
                        this.form.ledger_code = ledger_data.ledger_code;
                    }else{
                        this.fetchAccountCode(value.id, "dtype");
                    }
                } else{
                    this.fetchAccountCode(value.id, "dtype");
                    var single_item = this.account_detail_types.find(({id}) => id == value.id);
                    this.form.ledger_name = single_item.type_name;
                }
                
            }else{
                this.form.ledger_code = '';
                this.form.ledger_name = '';
            }
        },

        fetchDetailTypes(types) {
            var formData = new FormData();
            formData.append("types", types);

            axios.post(this.apiUrl+'/account_types/getChartOfAccountsOnlyDetailTypeOptions', formData, this.headers)
            .then((res) => {
                this.detail_type_options = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            })
        },

        fetchAccountCode(reference_id = '', type='') {
            var formData = new FormData();
            formData.append("reference_id", reference_id);
            formData.append("reference_type", type);
            var actionEvent = axios.post(this.apiUrl+'/account_ledgers/getAccountCode', formData, this.headers);
            
            actionEvent.then((res) => {
                if(this.modalChildrenActive) {
                    this.form_children.ledger_code = res.data.data.account_code;
                }else{
                    this.form.ledger_code = res.data.data.account_code;
                }
            })
            .catch((err) => { 
                this.$toast.error(err.response.data.message);
            });
        },

        onChangeParentAccount: function(node, instanceId) {
            this.onkeyPress('parent_id');
            var parent_id = node.id;
            this.form.ledger_code = '';
            if(parent_id != '' || parent_id != 0) {
                this.fetchAccountCode(parent_id, 'paccount');
            }else{
                this.form.ledger_code = '';
                this.$toast.error("Please Select Parent Account");
            }
        },

        edit: function(item) { 

            this.parent_types = [];
            if(item.account_type == "type") {
                this.accountTypeEdit = true;
                var type_data = this.all_account_types.find(({id}) => id == item.id);

                this.all_account_types.filter((titem) => {
                    if (titem.id != item.id  && titem.class_id == type_data.class_id) {
                        this.parent_types.push(titem);
                    }
                });

                this.createAccountTypeModal();
                this.form_type.fill(type_data);
            }
            else if(item.account_type == "detail_type") {
                this.accountTypeEdit = true;
                var type_data = this.all_account_types.find(({id}) => id == item.id);
                
                this.all_account_types.filter((titem) => {
                    if (titem.id != item.id  && titem.class_id == type_data.class_id) {
                        this.parent_types.push(titem);
                    }
                });

                this.createAccountSubTypeModal();
                this.form_type.fill(type_data);
            }
            else if(item.account_type == 'ledger') {
                this.editMode = true;
                this.form.detail_type_id = null;
                var ledger_data = this.all_ledgers.find(({id}) => id == item.id);
                var master_type = this.all_account_types.find(({id}) => id == ledger_data.type_id);

                this.fetchDetailTypes(JSON.stringify(master_type));
                // (item.is_master_head) ? this.parentFieldShow = false : this.parentFieldShow = true;
                this.btn='Update';
                this.createAccountModal();
                setTimeout(() => {
                    this.form.fill(ledger_data); 
                    this.forceRerender();
                }, 5000) 
            }
        },

        submitAccountForm: function(e) { 

            this.isAccountSubmit = true;
            this.disabledAccountSubmit = true;
            var parent_id = (this.form.parent_id == null) ? '' : this.form.parent_id;

            const formData = new FormData();           
            formData.append('type_id', this.form.type_id);
            formData.append('detail_type_id', this.form.detail_type_id);
            // formData.append('parent_id', parent_id);
            formData.append('ledger_code', this.form.ledger_code);
            formData.append('ledger_name', this.form.ledger_name);
            formData.append('ledger_type', this.form.ledger_type);
            formData.append('opening_balance', this.form.opening_balance);
            formData.append('balance_date', this.form.balance_date);
            formData.append('is_control_transaction', this.form.is_control_transaction);
            if(this.editMode){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/account_ledgers/'+this.form.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/account_ledgers', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isAccountSubmit = false;
                this.disabledAccountSubmit = false;
                if(res.status == 200){
                    this.createAccountModal();

                    this.fetchCOAData();
                    this.fetchCOAOptionsData();
                    this.fetchAccountTypeData();
                    this.fetchGroupData();
                    this.fetchAccountLedgerData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isAccountSubmit = false; 
                this.disabledAccountSubmit = false;
                this.$toast.error(err.response.data.message);
                if(err.response.status == 422){
                    this.errors = err.response.data.errors 
                }
            });
        },

        fetchTypeCode(reference_id='', type='') {
            var formData = new FormData();
            formData.append("reference_id", reference_id);
            formData.append("reference_type", type);
            axios.post(this.apiUrl+'/account_types/getTypesCode', formData, this.headers)
            .then((res) => {
                this.form_type.type_code = res.data.data;
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message);
            });
        },

        // Add Type Data
        addTypeItem: function(item) {

            this.form_type.class_id = item.id;
            this.form_type.class_name = item.name;
            this.fetchTypeCode(item.id, item.account_type);

            this.createAccountTypeModal();

        },

        addSubTypeItem: function(item) {
            var type_data = this.all_account_types.find(({id}) => id == item.id);

            this.form_type.class_id = type_data.class_id;
            this.form_type.parent_type_id = item.id;
            this.form_type.parent_type_name = item.name;
            this.fetchTypeCode(item.id, item.account_type);

            this.createAccountSubTypeModal();
        },

        submitAccountTypeForm: function(e) { 

            this.isTypeSubmit = true;
            this.disabledTypeSubmit = true;
            const formData = new FormData();           
            formData.append('class_id', this.form_type.class_id);
            formData.append('parent_type_id', this.form_type.parent_type_id);
            formData.append('type_code', this.form_type.type_code);
            formData.append('type_name', this.form_type.type_name);
            formData.append('status', this.form_type.status);

            if(this.accountTypeEdit){
                formData.append('_method', 'put');
                var postEvent = axios.post(this.apiUrl+'/account_types/'+this.form_type.id, formData, this.headers);
            }else{ 
                var postEvent = axios.post(this.apiUrl+'/account_types', formData, this.headers);
            }         

            postEvent.then(res => {
                this.isTypeSubmit = false;
                this.disabledTypeSubmit = false;
                if(res.status == 200){
                    if(this.modalAccTypeActive) {
                        this.createAccountTypeModal();
                    }
                    if(this.modalAccSubTypeActive) {
                        this.createAccountSubTypeModal();
                    }

                    this.fetchCOAData();
                    this.fetchCOAOptionsData();
                    this.fetchAccountTypeData();
                    this.fetchGroupData();
                    this.fetchAccountLedgerData();
                    this.forceRerender();
                    
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


        addLedgerItem: function(item) {
            var request_data = {'account_id':item.id}
            if(item.account_type == 'detail_type') {
                this.fetchAccountCode(item.id, 'dtype');
                var detail_type = this.account_detail_types.find(({id}) => id == item.id);
                this.detail_types = this.account_detail_types.filter((item) => {
                    if(item.parent_type_id == detail_type.parent_type_id) {
                        return item;
                    }
                });

                var single_acctype = this.getMasterType(detail_type.parent_type_id);

                this.form.ledger_name = detail_type.type_name;
                this.form.type_id = single_acctype.id;
                this.form.detail_type_id = detail_type.id;
                this.createAccountModal();


            }else if(item.account_type == 'ledger') {
                this.fetchAccountCode(item.id, 'paccount');
                var detail_type     = this.account_detail_types.find(({id}) => id == item.detail_type_id);
                var single_acctype = this.getMasterType(item.detail_type_id);

                this.form_children.ledger_name = '';
                this.form_children.type_id = single_acctype.id;
                this.form_children.type_name = single_acctype.type_name;
                this.form_children.detail_type_id = detail_type.id;
                this.form_children.detail_type_name = detail_type.type_name;
                this.form_children.parent_id = item.id;
                this.form_children.parent_name = item.name;                
                this.createChildrenModal();

            }
            // var actionEvent = axios.post(this.apiUrl+'/account_ledgers/getAccountCode', request_data, this.headerjson);
            
            // actionEvent.then((res) => {
            //     this.form_children.ledger_code = res.data.data.account_code;
            //     this.form_children.parent_id = item.id;
            //     this.createChildrenModal();
            // })
            // .catch((err) => { 
            //     this.$toast.error(err.response.data.message);
            // });
        },

        getMasterType(parent_type_id) {
            
            var detail_type = this.all_account_types.find(({id}) => id == parent_type_id);
            if(detail_type.parent_type_id != 0) {
                detail_type = this.getMasterType(detail_type.parent_type_id);
            }

            return detail_type;
        },

        submitChildrenAccountForm: function(e) { 

            this.isChildrenSubmit = true;
            this.disabledChildrenSubmit = true;
            var parent_id = (this.form_children.parent_id == null) ? '' : this.form_children.parent_id;

            const formData = new FormData();           
            formData.append('type_id', this.form_children.type_id);
            formData.append('detail_type_id', this.form_children.detail_type_id);
            formData.append('parent_id', parent_id);
            formData.append('ledger_code', this.form_children.ledger_code);
            formData.append('ledger_name', this.form_children.ledger_name);
            formData.append('is_control_transaction', this.form_children.is_control_transaction);
            
            var postEvent = axios.post(this.apiUrl+'/account_ledgers', formData, this.headers);       

            postEvent.then(res => {
                this.isChildrenSubmit = false;
                this.disabledChildrenSubmit = false;
                if(res.status == 200){
                    this.createChildrenModal();
                    this.fetchCOAData();
                    this.fetchCOAOptionsData();
                    this.fetchAccountTypeData();
                    this.fetchGroupData();
                    this.fetchAccountLedgerData();
                    this.$toast.success(res.data.message); 
                }else{
                    this.$toast.error(res.data.message);
                }

            }).catch(err => { 
                this.isChildrenSubmit = false; 
                this.disabledChildrenSubmit = false;
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
            console.log(item);
            this.$swal({
                title: 'Are you sure?',
                text: "You want delete this item!", 
                showCancelButton: true,
                confirmButtonCategory: '#3085d6',
                cancelButtonCategory: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => { 
                if (result.value) { 
                    if("children" in item && item.children.length > 0) {
                        this.$toast.error("This item can't delete because is already used");
                    }else{
                        axios.delete(this.apiUrl+'/account_ledgers/'+item.id, this.headerjson) 
                        .then(res => {
                            if(res.status == 200){  
                                this.fetchCOAData();
                                this.fetchCOAOptionsData();
                                this.fetchAccountTypeData();
                                this.fetchGroupData();
                                this.fetchAccountLedgerData();
                                this.$toast.success(res.data.message); 
                            }else{
                                this.$toast.error(res.data.message);
                            }
                        }).catch(err => {  
                            this.$toast.error(err.response.data.message); 
                        });
                    } 
                }else{
                    // this.$toast.error(`Hey! I'm here`);
                    // this.$toast.warning(`Hey! I'm here`);
                    // this.$toast.info(`Hey! I'm here`)
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
.card-body {
    width: 60vw;
    margin: 0 auto;
}
.modal-content.scrollbar-width-thin { 
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

@media (max-width: 768px) {
    .text-item {
        font-size: 14px; /* Smaller font size for mobile */
    }
    .card-body{
        width: 100%;
    }
    .action {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end; /* Align actions to the right */
    }
}
</style>