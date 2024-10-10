
<template>
    <transition  >
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right float-left">
                        <ol class="breadcrumb m-0"> 
                            <li class="breadcrumb-item active">Account Settings </li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Account Default Settings</a></li>
                            
                        </ol>
                    </div>
                    <div class="page-title-right float-right "> 
                        <!-- <button type="button" class="btn btn-primary float-right" @click="">
                            Purchase Order List
                        </button>  -->
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
                                <select class="form-control" v-model="form.company_id" @change="fetchAccountDefaultSettings($event.target.value), fetchAccountLedgers($event.target.value)">
                                    <option value="">--- Select Company ---</option>
                                    <option v-for="(company, i) in companies" :key="i" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
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

                    <div class="card-body" v-if="!loading">                        
                        <!-- <form id="purchase_order_form" @submit.prevent="submitForm()"> -->

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Account Default Settings</h4>
                                </div>

                                <hr>
                            </div>

                            <div class="row">
                                
                                <!-- Ledger Create Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Ledger Create Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            
                                            <div class="row">
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Supplier Payable Account Type</label><br>
                                                        <treeselect 
                                                                v-model="form.supplier_payable_account_type"
                                                                :multiple="false" 
                                                                :always-open="false"
                                                                :options="ledgers"
                                                                :normalizer="normalizer"
                                                                :value-consists-of="valueConsistsOf"
                                                                :default-expand-level="Infinity"
                                                                :search-nested="true"                                                
                                                                placeholder='Select Ledger account'
                                                                v-if="renderOptionComponent"
                                                            />
                                                        <div class="invalid-feedback" v-if="errors.supplier_payable_account_type">
                                                            {{errors.supplier_payable_account_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Supplier Discount Account Type</label><br>
                                                        <treeselect 
                                                            v-model="form.supplier_discount_account_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.supplier_discount_account_type">
                                                            {{errors.supplier_discount_account_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Supplier Advance Payment Account Type</label><br>
                                                        <treeselect 
                                                            v-model="form.supplier_advance_payment_account_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.supplier_advance_payment_account_type">
                                                            {{errors.supplier_advance_payment_account_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Account Asset Type</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_account_asset_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_account_asset_type">
                                                            {{errors.bank_account_asset_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Loan Account Liability Type</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_loan_account_liability_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_loan_account_liability_type">
                                                            {{errors.bank_loan_account_liability_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Charge Account Expense Type</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_charge_account_expense_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_charge_account_expense_type">
                                                            {{errors.bank_charge_account_expense_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Loan Interest Expense Type</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_loan_interest_expense_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_loan_interest_expense_type">
                                                            {{errors.bank_loan_interest_expense_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Interest Income Type</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_interest_income_type"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :search-nested="true"                                                
                                                            placeholder='Select type or account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_interest_income_type">
                                                            {{errors.bank_interest_income_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Customer Receivable Account Type</label><br>
                                                        <treeselect 
                                                                v-model="form.customer_receivable_account_type"
                                                                :multiple="false" 
                                                                :always-open="false"
                                                                :options="ledgers"
                                                                :normalizer="normalizer"
                                                                :value-consists-of="valueConsistsOf"
                                                                :default-expand-level="Infinity"
                                                                :search-nested="true"                                                
                                                                placeholder='Select Account Type'
                                                                v-if="renderOptionComponent"
                                                            />
                                                        <div class="invalid-feedback" v-if="errors.customer_receivable_account_type">
                                                            {{errors.customer_receivable_account_type[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                    
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Transaction Ledger Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Inventory Transaction Ledger Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Inventories Account </label><br>
                                                        <treeselect 
                                                            v-model="form.inventory_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.inventory_ledger_id">
                                                            {{ errors.inventory_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">COGS Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.cogs_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.cogs_ledger_id">
                                                            {{ errors.cogs_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Inventory Adjustment Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.inv_adj_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.inv_adj_ledger_id">
                                                            {{ errors.inv_adj_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Inventory Damage Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.stock_damage_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.stock_damage_ledger_id">
                                                            {{ errors.stock_damage_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cash Transaction Ledger Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Cash Transaction Ledger Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">

                                                

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Petty Cash Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.petty_cash_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.petty_cash_ledger_id">
                                                            {{ errors.petty_cash_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Cash in hand Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.cash_hand_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.cash_hand_ledger_id">
                                                            {{errors.cash_hand_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transaction Ledger Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Bank Transaction Ledger Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">

                                                

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Default Bank Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_ledger_id">
                                                            {{ errors.bank_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Loan Account</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_loan_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_loan_ledger_id">
                                                            {{errors.bank_loan_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Loan Interest Expense</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_loan_interest_expense_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_loan_interest_expense_ledger_id">
                                                            {{errors.bank_loan_interest_expense_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Charge Account Expense</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_charge_expense_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_charge_expense_ledger_id">
                                                            {{errors.bank_charge_expense_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Interest Income </label><br>
                                                        <treeselect 
                                                            v-model="form.bank_interest_income_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_interest_income_ledger_id">
                                                            {{errors.bank_interest_income_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchase Transaction Ledger Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Purchase Transaction Ledger Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Trade Payable Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.trade_payable_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.trade_payable_ledger_id">
                                                            {{errors.trade_payable_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Supplier Discount Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.supplier_discount_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.supplier_discount_ledger_id">
                                                            {{errors.supplier_discount_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Transaction Ledger Setup -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Sales Transaction Ledger Setup</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Cash Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.cash_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.cash_sales_ledger_id">
                                                            {{errors.cash_sales_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Default MFS Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.mfs_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.mfs_sales_ledger_id">
                                                            {{ errors.mfs_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bkash Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.bkash_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bkash_sales_ledger_id">
                                                            {{ errors.bkash_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Rocket Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.rocket_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.rocket_sales_ledger_id">
                                                            {{ errors.rocket_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Nagad Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.nagad_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.nagad_sales_ledger_id">
                                                            {{ errors.nagad_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Card Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.card_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.card_sales_ledger_id">
                                                            {{ errors.card_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Credit Sales Ledger Account</label><br>
                                                        <treeselect 
                                                            v-model="form.credit_sales_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.credit_sales_ledger_id">
                                                            {{ errors.credit_sales_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Account Receiveable Ledger</label><br>
                                                        <treeselect 
                                                            v-model="form.account_receiveable_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.account_receiveable_ledger_id">
                                                            {{ errors.account_receiveable_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Customer Discount Accounts</label><br>
                                                        <treeselect 
                                                            v-model="form.customer_discount_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.customer_discount_ledger_id">
                                                            {{ errors.customer_discount_ledger_id[0] }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- MFS Transaction Reference Ledger -->
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>MFS Transaction reference Ledger</h3>
                                        </div>
                                        <div class="card-body">

                                            <div class="row">
                                                <h4>Default MFS Transaction Reference Ledger</h4>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Reference Ledger for MFS</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_reference_ledger_mfs"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_reference_ledger_bkash">
                                                            {{errors.bank_reference_ledger_bkash[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">MFS Charge Ledger</label><br>
                                                        <treeselect 
                                                            v-model="form.mfs_charge_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.mfs_charge_ledger_id">
                                                            {{errors.mfs_charge_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <h4>Bkash Transaction Reference Ledger</h4>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Reference Ledger for Bkash</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_reference_ledger_bkash"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_reference_ledger_bkash">
                                                            {{errors.bank_reference_ledger_bkash[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bkash Charge Ledger</label><br>
                                                        <treeselect 
                                                            v-model="form.bkash_charge_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bkash_charge_ledger_id">
                                                            {{errors.bkash_charge_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <h4>Rocket Transaction Reference Ledger</h4>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Reference Ledger for Rocket</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_reference_ledger_rocket"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_reference_ledger_rocket">
                                                            {{errors.bank_reference_ledger_rocket[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Rocket Charge Ledger</label><br>
                                                        <treeselect 
                                                            v-model="form.rocket_charge_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.rocket_charge_ledger_id">
                                                            {{errors.rocket_charge_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <h4>Nagad Transaction Reference Ledger</h4>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Bank Reference Ledger for Nagad</label><br>
                                                        <treeselect 
                                                            v-model="form.bank_reference_ledger_nagad"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.bank_reference_ledger_nagad">
                                                            {{errors.bank_reference_ledger_nagad[0]}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Nagad Charge Ledger</label><br>
                                                        <treeselect 
                                                            v-model="form.nagad_charge_ledger_id"
                                                            :multiple="false" 
                                                            :always-open="false"
                                                            :options="ledgers"
                                                            :normalizer="normalizer"
                                                            :value-consists-of="valueConsistsOf"
                                                            :default-expand-level="Infinity"
                                                            :disable-branch-nodes="true"
                                                            :search-nested="true"                                                
                                                            placeholder='Select ledger account'
                                                            v-if="renderOptionComponent"
                                                        />
                                                        <div class="invalid-feedback" v-if="errors.nagad_charge_ledger_id">
                                                            {{errors.nagad_charge_ledger_id[0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>  
                                </div>

                            </div>

                            <div class="buttons">
                                <button type="button" class="btn btn-primary float-right" :disabled="disabled" @click.prevent="submitForm()">
                                    <span v-show="isSubmit">
                                        <i class="fas fa-spinner fa-spin" ></i>
                                    </span> Save Default Settings
                                </button>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
    </transition>
</template>
<script> 
import Modal from "./../helper/Modal"; 
import Form from 'vform'
import axios from 'axios'; 

export default {
    name: 'Purchase Order',
    components: {
        Modal
    },
    data() {
        return {
            loading: true,
            isSubmit: false,
            showModal: false,
            editMode:false,
            disabled: false,
            modalActive:false,
            errors: {},
            btn:'Create',
            item: '',
            ledgers: [],
            companies:[],
            valueConsistsOf: 'BRANCH_PRIORITY',
            normalizer(node) {
                return {
                    id: node.id +'___'+node.code,
                    label: '['+ node.code +'] '+ node.name,
                    children: node.children,
                }
            },
            form: new Form({
                company_id: "",
                supplier_payable_account_type: null,
                supplier_discount_account_type: null,
                supplier_advance_payment_account_type: null,
                bank_account_asset_type: null,
                bank_loan_account_liability_type: null,
                bank_charge_account_expense_type: null,
                bank_loan_interest_expense_type: null,
                bank_interest_income_type: null,
                customer_receivable_account_type: null,

                inventory_ledger_id: null,
                cogs_ledger_id: null,
                stock_damage_ledger_id: null,
                inv_adj_ledger_id: null,
                petty_cash_ledger_id: null,
                cash_hand_ledger_id: null,
                bank_ledger_id: null,
                bank_loan_ledger_id: null,
                bank_loan_interest_expense_ledger_id: null,
                bank_charge_expense_ledger_id: null,
                bank_interest_income_ledger_id: null,
                trade_payable_ledger_id: null,
                supplier_discount_ledger_id: null,
                cash_sales_ledger_id: null,
                mfs_sales_ledger_id: null,
                bkash_sales_ledger_id: null,
                rocket_sales_ledger_id: null,
                nagad_sales_ledger_id: null,
                card_sales_ledger_id: null,
                credit_sales_ledger_id: null,
                account_receiveable_ledger_id: null,
                customer_discount_ledger_id: null,
                bank_reference_ledger_mfs: null,
                mfs_charge_ledger_id: null,
                bank_reference_ledger_bkash: null,
                bkash_charge_ledger_id: null,
                bank_reference_ledger_rocket: null,
                rocket_charge_ledger_id: null,
                bank_reference_ledger_nagad: null,
                nagad_charge_ledger_id: null,


            }),
            multiclasses: { 
                clear: '',
                clearIcon: '', 
            },            
            renderOptionComponent: true,
            
        };
    },
    created() {
        this.fetchCompanies()
        // this.fetchAccountLedgers();
        // this.fetchAccountDefaultSettings();
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

        fetchAccountLedgers(selectedId) {
            axios.get(this.apiUrl+'/account_ledgers/getChartOfAccountsOption?company_id='+selectedId, this.headerjson)
            .then((resp) => {
                this.ledgers = resp.data.data.accounts;  
            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            })
            .finally(() => {
                this.loading = false;
            });
        },


        fetchAccountDefaultSettings(selectedId) {
            // this.form.reset();
            axios.get(this.apiUrl+'/account-default-setting?company_id='+selectedId, this.headerjson)
            .then((resp) => {
                this.form.fill(resp.data.data);  
                this.forceRerender();

            })
            .catch((err) => {
                this.$toast.error(err.response.data.message)
            })
            .finally(() => {
                this.loading = false;
            });
        },

        submitForm: function(e) {  
            this.isSubmit = true;
            this.disabled = true;

            const formData = new FormData();
            formData.append("supplier_payable_account_type", this.form.supplier_payable_account_type);
            formData.append("supplier_discount_account_type", this.form.supplier_discount_account_type);
            formData.append("supplier_advance_payment_account_type", this.form.supplier_advance_payment_account_type);
            formData.append("bank_account_asset_type", this.form.bank_account_asset_type);
            formData.append("bank_loan_account_liability_type", this.form.bank_loan_account_liability_type);
            formData.append("bank_charge_account_expense_type", this.form.bank_charge_account_expense_type);
            formData.append("bank_loan_interest_expense_type", this.form.bank_loan_interest_expense_type);
            formData.append("bank_interest_income_type", this.form.bank_interest_income_type);
            formData.append("customer_receivable_account_type", this.form.customer_receivable_account_type);
            
            formData.append("inventory_account", this.form.inventory_ledger_id);
            formData.append("cogs_account", this.form.cogs_ledger_id);
            formData.append("inventory_damage_account", this.form.stock_damage_ledger_id);
            formData.append("inventory_adjustment_account", this.form.inv_adj_ledger_id);
            formData.append("petty_cash_account", this.form.petty_cash_ledger_id);
            formData.append("cash_in_hand_account", this.form.cash_hand_ledger_id);
            formData.append('bank_account', this.form.bank_ledger_id); // Bank Transaction
            formData.append('bank_loan_account', this.form.bank_loan_ledger_id);
            formData.append('bank_loan_interest_expense_account', this.form.bank_loan_interest_expense_ledger_id);
            formData.append('bank_charge_expense_account', this.form.bank_charge_expense_ledger_id);
            formData.append('bank_interest_income_account', this.form.bank_interest_income_ledger_id);
            formData.append('supplier_discount_account', this.form.supplier_discount_ledger_id);
            formData.append('trade_payable_account', this.form.trade_payable_ledger_id); // Purchase
            formData.append('cash_sales_account', this.form.cash_sales_ledger_id); // sales
            formData.append('mfs_sales_account', this.form.mfs_sales_ledger_id);
            formData.append('bkash_sales_account', this.form.bkash_sales_ledger_id);
            formData.append('rocket_sales_account', this.form.rocket_sales_ledger_id);
            formData.append('nagad_sales_account', this.form.nagad_sales_ledger_id);
            formData.append('card_sales_account', this.form.card_sales_ledger_id);
            formData.append('credit_sales_account', this.form.credit_sales_ledger_id);
            formData.append('account_receivable_ledger', this.form.account_receiveable_ledger_id);
            formData.append('customer_discount_account', this.form.customer_discount_ledger_id);
            formData.append('bank_reference_ledger_mfs', this.form.bank_reference_ledger_mfs);   // Reference Ledger
            formData.append('mfs_charge_ledger', this.form.mfs_charge_ledger_id);
            formData.append('bank_reference_ledger_bkash', this.form.bank_reference_ledger_bkash);
            formData.append('bkash_charge_ledger', this.form.bkash_charge_ledger_id);
            formData.append('bank_reference_ledger_rocket', this.form.bank_reference_ledger_rocket);
            formData.append('rocket_charge_ledger', this.form.rocket_charge_ledger_id);
            formData.append('bank_reference_ledger_nagad', this.form.bank_reference_ledger_nagad);
            formData.append('nagad_charge_ledger', this.form.nagad_charge_ledger_id);
            formData.append('company_id', this.form.company_id);
                
            var postEvent = axios.post(this.apiUrl+'/account-default-setting', formData, this.headers);

            postEvent.then(res => {
                this.isSubmit = false;
                this.disabled = false;
                if(res.status == 200){
                    this.$toast.success(res.data.message); 
                    window.location.reload();
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
        

    },
    destroyed() {},
    mounted() {
        window.scrollTo(0, 0);
    },
    computed: {
       

    }, 
    watch: {
        
    }, 
}
</script>
<style scoped>
.modal-content.scrollbar-width-thin { 
    width: 1000px;
}
.modal-content.scrollbar-width-thin.supplier-add-modal { 
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

#purchase_order_form {
    padding: 15px;
}

#purchase_order_form .form-control {

}

#reference_no {
    color: red;
}

.total_quantity {
    float: right;
    color: red;
}

.product_table {
    padding: 0;
    min-height: auto;
}

.product_table tbody td input {
    border-bottom: 1px solid #cecece;
}

div.buttons {
    margin-top: 30px;
}

div.buttons .btn-primary {
    margin-top: 0;
}

div.buttons .btn {
    margin-right: 5px;
}

div.buttons .btn:last-child {
    margin-right: 0;
}

.input-group-text {
    height: 40px;
}

/** PO Invoice Design */
.po_invoice {
    border: 1px solid #000 !important;
}

.po_invoice>:not(:first-child) {
    border: 0;
    border-top: 1px solid #000;
}

.po_invoice td {
    vertical-align: top !important;
}
.po_invoice td p {
    margin-bottom: 0px;
    padding: 2px 5px!important;
    color: #282828;
}

.po_invoice td h5, .po_invoice td h4, .po_invoice td h3, .po_invoice td h2 {
    margin: 0px;
    text-transform: uppercase;
    padding: 2px 5px!important;
    color: #282828;
}

.po_invoice td table {
    margin-bottom: 0;
}

span.invoice_logo {
    position: absolute;
    right: 15px;
    top: 0;
}
span.invoice_logo img {
    width: 140px;
    height: 100%;
}

.text-uppercase {
    text-transform: uppercase;
}
</style>