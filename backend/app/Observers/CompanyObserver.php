<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\AccountClass;
use App\Models\AccountType;
use App\Models\AccountDefaultSetting;
use App\Models\FiscalYear;
use App\Models\CostCenter;
class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function creating(Company $company)
    {
        // You can perform any additional logic here before the company is created
    }

    public function created(Company $company) {
        AccountDefaultSetting::create(['company_id'=>$company->id]);  
        $fiscalYear = getFiscalYear();
        $start_date = $fiscalYear['start'];
        $end_date = $fiscalYear['end'];
        $label = $fiscalYear['label'];
        FiscalYear::create(['label'=>$label, 'start_date'=>$start_date, 'end_date'=>$end_date, 'company_id'=>$company->id, 'status'=>1]); 
        
        $start_date = $fiscalYear['start'];
        $end_date = $fiscalYear['end'];
        $label = $fiscalYear['label'];   
        CostCenter::create(['center_name'=>$company->name, 'company_id'=>$company->id, 'status'=>1]); 
        // Prepare account classes data
        $accountClasses = [
            ['name' => 'ASSETS', 'code' => '1', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
            ['name' => 'LIABILITIES', 'code' => '2', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EQUITIES', 'code' => '3', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'INCOME', 'code' => '4', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EXPENDITURE', 'code' => '5', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
        ]; 
        AccountClass::insert($accountClasses);         

        // Prepare account types for each class first step start
        $createdClasses = AccountClass::where('company_id', $company->id)->get();       
        $accountTypes = [];
        foreach ($createdClasses as $accountClass) {             
            switch ($accountClass->name) {
                case 'ASSETS':
                    $accountTypes[] = [
                        ['type_code' => '11', 'type_name' => 'Non-Current Assets', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '12', 'type_name' => 'Current Assets', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'LIABILITIES':
                    $accountTypes[] = [
                        ['type_code' => '21', 'type_name' => 'CURRENT LIABILITIES', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '22', 'type_name' => 'NON CURRENT LIABILITIES', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '23', 'type_name' => 'Accumulated Depreciation', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'EQUITIES':
                    $accountTypes[] = [
                        ['type_code' => '31', 'type_name' => 'PAID UP CAPITAL', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '32', 'type_name' => 'Other Reserves', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '33', 'type_name' => 'PROFIT & LOSS ACCOUNT', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'INCOME':
                    $accountTypes[] = [
                        ['type_code' => '41', 'type_name' => 'OPERATING INCOME', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '42', 'type_name' => 'NON-OPERATING INCOME', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'EXPENDITURE':
                    $accountTypes[] = [
                        ['type_code' => '51', 'type_name' => 'Operating Expenses', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
            }
        } 

        $flattenedArray = [];
        foreach ($accountTypes as $subArray) {
            $flattenedArray = array_merge($flattenedArray, $subArray);
        } 
        if (!empty($flattenedArray)) {
            AccountType::insert($flattenedArray);
        } 
        // Prepare account types for each class first step ends

        // Prepare account types for each class second step start
        $accountTypeParents = AccountType::where('company_id', $company->id)->where('parent_type_id', 0)->get();  
        $accountTypesParents = [];
        foreach ($accountTypeParents as $aTyPrnt) {             
            switch ($aTyPrnt->type_name) {
                case 'Non-Current Assets':
                    $accountTypesParents[] = [
                        ['type_code' => '1101', 'type_name' => 'Property Plant & Equipment', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1104', 'type_name' => 'Accumuleted Depriciation', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1102', 'type_name' => 'Intangibels', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'Current Assets':
                    $accountTypesParents[] = [ 
                        ['type_code' => '1205', 'type_name' => 'DIRECTORS CURRENT ACCOUNT', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1201', 'type_name' => 'Inventories', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1207', 'type_name' => 'Cash and Cash Equivalents', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1208', 'type_name' => 'Other current Assets', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1209', 'type_name' => 'Petty Cash Accounts', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1206', 'type_name' => 'ADVANCE DEPOSITS AND PREPAYMENTS', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1202', 'type_name' => 'Accounts Receivables', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'CURRENT LIABILITIES':
                    $accountTypesParents[] = [
                        ['type_code' => '2101', 'type_name' => 'ACCOUNTS PAYABLE for services', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2102', 'type_name' => 'Liabilities Against Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2103', 'type_name' => 'Trade Payable', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2104', 'type_name' => 'Loan From Bank', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2105', 'type_name' => 'Directors Current Account', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                   ];
                    break;
                case 'NON CURRENT LIABILITIES':
                    $accountTypesParents[] = [ 
                        ['type_code' => '2201', 'type_name' => 'LONG TERM LOAN', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'Accumulated Depreciation':
                    $accountTypesParents[] = [ 
                        ['type_code' => '2250', 'type_name' => 'Accumulated Depreciation', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'PAID UP CAPITAL':
                    $accountTypesParents[] = [
                        ['type_code' => '3101', 'type_name' => 'PAID UP CAPITAL', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'Other Reserves':
                    $accountTypesParents[] = [ 
                        ['type_code' => '3150', 'type_name' => 'Other Reserves', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'PROFIT & LOSS ACCOUNT':
                    $accountTypesParents[] = [
                        ['type_code' => '3301', 'type_name' => 'PROFIT & LOSS ACCOUNT', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'OPERATING INCOME':
                    $accountTypesParents[] = [
                        ['type_code' => '4101', 'type_name' => 'Sales', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'NON-OPERATING INCOME':
                    $accountTypesParents[] = [ 
                        ['type_code' => '4201', 'type_name' => 'NON-OPERATING INCOME', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '4202', 'type_name' => 'Income from Discount (Vendors)', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '4203', 'type_name' => 'Interest Income - Bank', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'Operating Expenses':
                    $accountTypesParents[] = [
                        ['type_code' => '5101', 'type_name' => 'Remuneration', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5102', 'type_name' => 'Payroll Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5103', 'type_name' => 'Utility Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5104', 'type_name' => 'Employee Related Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5105', 'type_name' => 'Rent/ Rate & Taxes', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5106', 'type_name' => 'Office Maintenance', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5107', 'type_name' => 'Printing & Stationary', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5108', 'type_name' => 'Registration & Renewal', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5109', 'type_name' => 'Vehicle Running Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5110', 'type_name' => 'Indirect Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5111', 'type_name' => 'Repair & Maintenance', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5112', 'type_name' => 'Travel Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5113', 'type_name' => 'Legal & Professional Fees', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5114', 'type_name' => 'Mobile banking and POS Machine charges', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5115', 'type_name' => 'Marketing Expense', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5116', 'type_name' => 'Tax And Vat Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5117', 'type_name' => 'Warranty, replacement & discount', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5118', 'type_name' => 'Depreciation expense', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5119', 'type_name' => 'Cost Of Goods Sold', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5120', 'type_name' => 'Bank Charge', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '5121', 'type_name' => 'Interest - Bank', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
            }
        } 

        $flattenedArraySub = [];
        foreach ($accountTypesParents as $subArray) {
            $flattenedArraySub = array_merge($flattenedArraySub, $subArray);
        } 
        if (!empty($flattenedArraySub)) {
            AccountType::insert($flattenedArraySub);
        }  
        // Prepare account types for each class second step ends

        // Prepare account types for each class third step start 
        if ($accountTypeParents->isNotEmpty()) { 
            $accountTypesSecondStep = AccountType::where('company_id', $company->id)
                                    ->whereIn('parent_type_id', $accountTypeParents->pluck('id'))
                                    ->get();
        } else { 
            $accountTypesSecondStep = collect(); // or return an empty collection
        }
        // $accountTypesThirdStep = AccountType::where('company_id', $company->id)
        //     ->whereIn('parent_type_id', $accountTypesSecondStep->pluck('id'))->get();  
        $accountTypesThirdData = [];
        foreach ($accountTypesSecondStep as $aThirdStep) {             
            switch ($aThirdStep->type_name) { 
                case 'Property Plant & Equipment':
                    // $accountTypesThirdData[] = [ 
                    //     ['type_code' => '2201', 'type_name' => 'IT Equipment', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1'],
                    //     ['type_code' => '2201', 'type_name' => 'Vehicles', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1'],
                    // ];
                    // break;
                case 'Cash and Cash Equivalents':
                    $accountTypesThirdData[] = [ 
                        ['type_code' => '2250', 'type_name' => 'Cash at Bank', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1'],
                        ['type_code' => '2251', 'type_name' => 'Cash In Hand', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1']
                    ];
                    break; 
            }
        } 

        $flattenedArrayThird = [];
        foreach ($accountTypesThirdData as $subArrayThird) {
            $flattenedArrayThird = array_merge($flattenedArrayThird, $subArrayThird);
        } 
        if (!empty($flattenedArrayThird)) {
            AccountType::insert($flattenedArrayThird);
        }  
        // Prepare account types for each class third step ends 
    } 
    
    /**
     * Handle the Company "updated" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function updated(Company $company) {
        // Check if default settings exist
        if (!AccountDefaultSetting::where('company_id', $company->id)->exists()) {
            AccountDefaultSetting::create(['company_id' => $company->id]);  
        } 
    
        // Update or create Fiscal Year
        $fiscalYear = getFiscalYear();
        $fiscalYearData = [
            'label' => $fiscalYear['label'], 
            'start_date' => $fiscalYear['start'], 
            'end_date' => $fiscalYear['end'], 
            'company_id' => $company->id, 
            'status' => 1
        ];
        
        FiscalYear::updateOrCreate(['company_id' => $company->id], $fiscalYearData);
    
        // Update or create Cost Center
        $costCenterData = ['center_name' => $company->name, 'company_id' => $company->id, 'status' => 1];
        CostCenter::updateOrCreate(['company_id' => $company->id], $costCenterData);
    
        // Prepare account classes data
        $accountClasses = [
            ['name' => 'ASSETS', 'code' => '1', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
            ['name' => 'LIABILITIES', 'code' => '2', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EQUITIES', 'code' => '3', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'INCOME', 'code' => '4', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EXPENDITURE', 'code' => '5', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
        ];
    
        AccountClass::insert($accountClasses);
    
        // Prepare account types for each class first step
        $createdClasses = AccountClass::where('company_id', $company->id)->get();       
        $accountTypes = [];
        
        foreach ($createdClasses as $accountClass) {             
            switch ($accountClass->name) {
                case 'ASSETS':
                    $accountTypes[] = [
                        ['type_code' => '11', 'type_name' => 'Non-Current Assets', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '12', 'type_name' => 'Current Assets', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'LIABILITIES':
                    $accountTypes[] = [
                        ['type_code' => '21', 'type_name' => 'CURRENT LIABILITIES', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '22', 'type_name' => 'NON CURRENT LIABILITIES', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '23', 'type_name' => 'Accumulated Depreciation', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'EQUITIES':
                    $accountTypes[] = [
                        ['type_code' => '31', 'type_name' => 'PAID UP CAPITAL', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '32', 'type_name' => 'Other Reserves', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '33', 'type_name' => 'PROFIT & LOSS ACCOUNT', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'INCOME':
                    $accountTypes[] = [
                        ['type_code' => '41', 'type_name' => 'OPERATING INCOME', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1'],
                        ['type_code' => '42', 'type_name' => 'NON-OPERATING INCOME', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
                case 'EXPENDITURE':
                    $accountTypes[] = [
                        ['type_code' => '51', 'type_name' => 'Operating Expenses', 'company_id' => $accountClass->company_id, 'class_id' => $accountClass->id, 'parent_type_id' => '0', 'status' => '1']
                    ];
                    break;
            }
        }
    
        $flattenedArray = [];
        foreach ($accountTypes as $subArray) {
            $flattenedArray = array_merge($flattenedArray, $subArray);
        }
        if (!empty($flattenedArray)) {
            AccountType::insert($flattenedArray);
        }
    
        // Prepare account types for each class second step
        $accountTypeParents = AccountType::where('company_id', $company->id)->where('parent_type_id', 0)->get();  
        $accountTypesParents = [];
        
        foreach ($accountTypeParents as $aTyPrnt) {             
            switch ($aTyPrnt->type_name) {
                case 'Non-Current Assets':
                    $accountTypesParents[] = [
                        ['type_code' => '1101', 'type_name' => 'Property Plant & Equipment', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1104', 'type_name' => 'Accumulated Depreciation', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1102', 'type_name' => 'Intangibles', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'Current Assets':
                    $accountTypesParents[] = [ 
                        ['type_code' => '1205', 'type_name' => 'DIRECTORS CURRENT ACCOUNT', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1201', 'type_name' => 'Inventories', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1207', 'type_name' => 'Cash and Cash Equivalents', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1208', 'type_name' => 'Other Current Assets', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1209', 'type_name' => 'Petty Cash Accounts', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'CURRENT LIABILITIES':
                    $accountTypesParents[] = [ 
                        ['type_code' => '2101', 'type_name' => 'Trade Payables', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2102', 'type_name' => 'Loans Payable', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'NON CURRENT LIABILITIES':
                    $accountTypesParents[] = [
                        ['type_code' => '2201', 'type_name' => 'Long Term Loans', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'PAID UP CAPITAL':
                    $accountTypesParents[] = [
                        ['type_code' => '3101', 'type_name' => 'Ordinary Shares', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'OPERATING INCOME':
                    $accountTypesParents[] = [
                        ['type_code' => '4101', 'type_name' => 'Sales Revenue', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
                case 'Operating Expenses':
                    $accountTypesParents[] = [
                        ['type_code' => '5101', 'type_name' => 'Salaries Expense', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;
            }
        }
    
        $flattenedArrayParents = [];
        foreach ($accountTypesParents as $subArray) {
            $flattenedArrayParents = array_merge($flattenedArrayParents, $subArray);
        }
        if (!empty($flattenedArrayParents)) {
            AccountType::insert($flattenedArrayParents);
        } 
            
        // Third Step for Account Types 
        // Prepare account types for each class third step start 
        if ($accountTypeParents->isNotEmpty()) { 
            $accountTypesSecondStep = AccountType::where('company_id', $company->id)
                                    ->whereIn('parent_type_id', $accountTypeParents->pluck('id'))
                                    ->get();
        } else { 
            $accountTypesSecondStep = collect(); // or return an empty collection
        }
        // $accountTypesThirdStep = AccountType::where('company_id', $company->id)
        //     ->whereIn('parent_type_id', $accountTypesSecondStep->pluck('id'))->get();  
        $accountTypesThirdData = [];
        foreach ($accountTypesSecondStep as $aThirdStep) {             
            switch ($aThirdStep->type_name) {  
                case 'Cash and Cash Equivalents':
                    $accountTypesThirdData[] = [ 
                        ['type_code' => '2250', 'type_name' => 'Cash at Bank', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1'],
                        ['type_code' => '2251', 'type_name' => 'Cash In Hand', 'company_id' => $aThirdStep->company_id, 'class_id' => $aThirdStep->class_id, 'parent_type_id' => $aThirdStep->id, 'status' => '1']
                    ];
                    break; 
            }
        } 

        $flattenedArrayThird = [];
        foreach ($accountTypesThirdData as $subArrayThird) {
            $flattenedArrayThird = array_merge($flattenedArrayThird, $subArrayThird);
        } 
        if (!empty($flattenedArrayThird)) {
            AccountType::insert($flattenedArrayThird);
        }  
        // Prepare account types for each class third step ends 
    } 

    /**
     * Handle the Company "deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function deleted(Company $company)
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}
