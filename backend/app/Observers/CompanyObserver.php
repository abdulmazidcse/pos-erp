<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\AccountClass;
use App\Models\AccountType;
use App\Models\AccountLedger;
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
        $accountSetting = AccountDefaultSetting::create(['company_id'=>$company->id]);  
        $fiscalYear = getFiscalYear();
        $start_date = $fiscalYear['start'];
        $end_date = $fiscalYear['end'];
        $label = $fiscalYear['label'];
        FiscalYear::create(['label'=>$label, 'start_date'=>$start_date, 'end_date'=>$end_date, 'company_id'=>$company->id, 'status'=>1]); 
        
        $start_date = $fiscalYear['start'];
        $end_date = $fiscalYear['end'];
        $label = $fiscalYear['label'];   
        CostCenter::create(['center_name'=>$company->name, 'company_id'=> $company->id, 'status'=>1]); 
        // Prepare account classes data
                 
        $accountClass = self::accountClass($company->id); 
        // Prepare account types for each class first step start 
        $accountTypesFirstStep = self::accountTypesFirstStep($company->id);
        // Prepare account types for each class first step ends
        // Prepare account types for each class second step start
        $accountTypesSecondStep = self::accountTypesSecondStep($company->id); 
         
        // Prepare account types for each class second step ends
        $defaultLedger = self::accountLedgers($company->id);
        // Prepare account types for each class third step start          
         
        $companyDefaultSetting = self::companyDefaultSetting($company->id);  
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
    
        $accountClass = self::accountClass($company->id); 
        // Prepare account types for each class first step start 
        $accountTypesFirstStep = self::accountTypesFirstStep($company->id);
        // Prepare account types for each class first step ends
        // Prepare account types for each class second step start
        $accountTypesSecondStep = self::accountTypesSecondStep($company->id); 
         
        // Prepare account types for each class second step ends
        $defaultLedger = self::accountLedgers($company->id);
        // Prepare account types for each class third step start          
         
        $companyDefaultSetting = self::companyDefaultSetting($company->id);  
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


    public function accountClass($company_id){ 
        $company = Company::find($company_id);
        $accountClasses = [
            ['name' => 'ASSETS', 'code' => '1', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
            ['name' => 'LIABILITIES', 'code' => '2', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EQUITIES', 'code' => '3', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'INCOME', 'code' => '4', 'is_debit_positive' => 0, 'is_credit_positive' => 1, 'company_id' => $company->id],
            ['name' => 'EXPENDITURE', 'code' => '5', 'is_debit_positive' => 1, 'is_credit_positive' => 0, 'company_id' => $company->id],
        ];

        foreach ($accountClasses as $accountClass) {
            AccountClass::updateOrCreate(
                ['code' => $accountClass['code'], 'company_id' => $accountClass['company_id']],
                $accountClass
            );
        }
        return AccountClass::where('company_id',$company_id)->get()->toArray();
    }

    public function accountTypesFirstStep($company_id){ 
        // Prepare account types for each class first step
        $createdClasses = AccountClass::where('company_id', $company_id)->get();       
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
            foreach ($flattenedArray as $accountTypeParentStep) {
                AccountType::updateOrCreate(
                    ['type_code' => $accountTypeParentStep['type_code'], 'company_id' => $accountTypeParentStep['company_id']],
                    $accountTypeParentStep
                );
            } 
        }
        return AccountType::where('company_id', $company_id)->where('parent_type_id',0)->get()->toArray();    
    }

    public function accountTypesSecondStep($company_id){  
        $accountTypeParents = AccountType::where('company_id', $company_id)->where('parent_type_id', 0)->get();  
        $accountTypesSecondStep = [];
        
        foreach ($accountTypeParents as $aTyPrnt) {             
            switch ($aTyPrnt->type_name) {
                // ASSETS
                case 'Non-Current Assets':
                    $accountTypesSecondStep[] = [
                        ['type_code' => '1101', 'type_name' => 'Property Plant & Equipment', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1102', 'type_name' => 'Intangibels', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1104', 'type_name' => 'Accumuleted Depriciation', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'Current Assets':
                    $accountTypesSecondStep[] = [ 
                        ['type_code' => '1201', 'type_name' => 'Inventories', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1202', 'type_name' => 'Accounts Receivables', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1203', 'type_name' => 'DIRECTORS CURRENT ACCOUNT', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1204', 'type_name' => 'ADVANCE DEPOSITS AND PREPAYMENTS', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1205', 'type_name' => 'Cash and Cash Equivalents', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '1206', 'type_name' => 'Other current Assets', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;

                // LIABILITIES
                case 'CURRENT LIABILITIES':
                    $accountTypesSecondStep[] = [
                        ['type_code' => '2101', 'type_name' => 'ACCOUNTS PAYABLE for services', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2102', 'type_name' => 'Liabilities Against Expenses', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2103', 'type_name' => 'Trade Payable', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2104', 'type_name' => 'Loan From Bank', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '2105', 'type_name' => 'Directors Current Account', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'NON CURRENT LIABILITIES':
                    $accountTypesSecondStep[] = [ 
                        ['type_code' => '2201', 'type_name' => 'LONG TERM LOAN', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'Accumulated Depreciation':
                    $accountTypesSecondStep[] = [ 
                        ['type_code' => '2301', 'type_name' => 'Accumulated Depreciation', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;

                // EQUITIES
                case 'PAID UP CAPITAL':
                    $accountTypesSecondStep[] = [
                        ['type_code' => '3101', 'type_name' => 'PAID UP CAPITAL', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'Other Reserves':
                    $accountTypesSecondStep[] = [ 
                        ['type_code' => '3201', 'type_name' => 'Other Reserves', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'PROFIT & LOSS ACCOUNT':
                    $accountTypesSecondStep[] = [
                        ['type_code' => '3301', 'type_name' => 'PROFIT & LOSS ACCOUNT', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;

                // INCOMES
                case 'OPERATING INCOME':
                    $accountTypesSecondStep[] = [
                        ['type_code' => '4101', 'type_name' => 'Sales', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                    ];
                    break;
                case 'NON-OPERATING INCOME':
                    $accountTypesSecondStep[] = [ 
                        ['type_code' => '4201', 'type_name' => 'NON-OPERATING INCOME', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '4202', 'type_name' => 'Income from Discount (Vendors)', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1'],
                        ['type_code' => '4203', 'type_name' => 'Interest Income - Bank', 'company_id' => $aTyPrnt->company_id, 'class_id' => $aTyPrnt->class_id, 'parent_type_id' => $aTyPrnt->id, 'status' => '1']
                    ];
                    break;

                // EXPENSES
                case 'Operating Expenses':
                    $accountTypesSecondStep[] = [
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
    
        $accountTypesSecondStepMarge = [];
        foreach ($accountTypesSecondStep as $subArray) {
            $accountTypesSecondStepMarge = array_merge($accountTypesSecondStepMarge, $subArray);
        }

        if (!empty($accountTypesSecondStepMarge)) {
            foreach ($accountTypesSecondStepMarge as $accountTypeSecondStep) {
                AccountType::updateOrCreate(
                    ['type_code' => $accountTypeSecondStep['type_code'], 'company_id' => $accountTypeSecondStep['company_id']],
                    $accountTypeSecondStep
                );
            } 
        }

        return AccountType::where('company_id', $company_id)->whereIn('parent_type_id',$accountTypeParents->pluck('id'))->get()->toArray(); 

    } 

    public function accountLedgers($company_id){

        $accountTypes = AccountType::where('company_id', $company_id)->get(); 
        $accountsLedgers = [];     
        foreach ($accountTypes as $accountType) {             
            switch ($accountType->type_code) { 
                // Current Assests Ledger Entry Start
                case '1201': // Inventories
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '120101', 'ledger_name' => 'Inventories', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                        ];
                    break;
                case '1202': // Accounts Receivables
                    $accountsLedgers[] = [  
                        ['ledger_code' => '120201', 'ledger_name' => 'Accounts Receivables', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;
                case '1203': // DIRECTORS CURRENT ACCOUNT
                    $accountsLedgers[] = [  
                        ['ledger_code' => '120301', 'ledger_name' => 'DIRECTORS CURRENT ACCOUNT', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;
                case '1204': // ADVANCE DEPOSITS AND PREPAYMENTS
                    $accountsLedgers[] = [  
                        ['ledger_code' => '120401', 'ledger_name' => 'ADVANCE DEPOSITS AND PREPAYMENTS', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;
                case '1205': // Cash and Cash Equivalents                   
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '120501', 'ledger_name' => 'Cash in Hand', 'type_id'=> $accountType->id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                        ['ledger_code' => '120502', 'ledger_name' => 'Default Bank', 'type_id'=> $accountType->id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '1206': //  Other current Assets
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '120601', 'ledger_name' => 'Other current Assets', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                // Current Assests Ledger Entry Start
                // CURRENT LIABILITIES Ledger Entry Start
                case '2101': //  ACCOUNTS PAYABLE for services
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '210101', 'ledger_name' => 'ACCOUNTS PAYABLES', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '2102': //  Liabilities Against Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '210201', 'ledger_name' => 'Liabilities Against Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '2103': //  Trade Payable
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '210301', 'ledger_name' => 'Trade Payable', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '2104': //  Loan From Bank
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '210401', 'ledger_name' => 'Loan From Bank', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '2105': //  Directors Current Account
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '210501', 'ledger_name' => 'Directors Current Account', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                // CURRENT LIABILITIES Ledger Entry END

                // NON CURRENT LIABILITIES Ledger Entry Start

                case '2201': //  LONG TERM LOAN
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '220101', 'ledger_name' => 'LONG TERM LOAN', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                case '2301': //  Accumulated Depreciation
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '230101', 'ledger_name' => 'Accumulated Depreciation', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  

                // NON CURRENT LIABILITIES Ledger Entry END

                

                // Equities Ledger Entry Start
        
                case '3101': //  PAID UP CAPITAL
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '310101', 'ledger_name' => 'PAID UP CAPITAL', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;   
                case '3201': //  Other Reserves
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '320101', 'ledger_name' => 'Other Reserves', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;   
                case '3301': //  PROFIT & LOSS ACCOUNT
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '330101', 'ledger_name' => 'PROFIT & LOSS ACCOUNT', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'dr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break; 
                
                // Equities Ledger Entry END


                // Operating Income Ledger Entry Start

                case '4101': //  Sales
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '410101', 'ledger_name' => 'Cash Sales', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                        ['ledger_code' => '410102', 'ledger_name' => 'Credit Sales', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                        ['ledger_code' => '410103', 'ledger_name' => 'Card Sales', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;    
                case '4201': //  NON-OPERATING INCOME
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '420101', 'ledger_name' => 'NON-OPERATING INCOME', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;    
                case '4202': // Income from Discount (Vendors)
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '420201', 'ledger_name' => 'Income from Discount (Vendors)', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;    
                case '4203': // Interest Income - Bank
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '420301', 'ledger_name' => 'Interest Income - Bank', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;     
                 // OperaOperating Expenses Start
                case '5101': // Remuneration
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510101', 'ledger_name' => 'Remuneration', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5102': // Payroll Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510201', 'ledger_name' => 'Payroll Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5103': // Utility Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510301', 'ledger_name' => 'Utility Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5104': // Employee Related Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510401', 'ledger_name' => 'Employee Related Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5105': // Rent/ Rate & Taxes
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510501', 'ledger_name' => 'Rent/ Rate & Taxes', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5106': // Office Maintenance
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510601', 'ledger_name' => 'Office Maintenance', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5107': // Printing & Stationary
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510701', 'ledger_name' => 'Printing & Stationary', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5108': // Registration & Renewal
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510801', 'ledger_name' => 'Registration & Renewal', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5109': // Vehicle Running Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '510901', 'ledger_name' => 'Vehicle Running Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5110': // Indirect Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511001', 'ledger_name' => 'Indirect Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5111': // Repair & Maintenance
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511101', 'ledger_name' => 'Repair & Maintenance', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5112': // Travel Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511201', 'ledger_name' => 'Travel Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5113': // Legal & Professional Fees
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511301', 'ledger_name' => 'Legal & Professional Fees', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5114': // Mobile banking and POS Machine charges
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511401', 'ledger_name' => 'Mobile banking and POS Machine charges', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5115': // Marketing Expense
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511501', 'ledger_name' => 'Marketing Expense', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5116': // Tax And Vat Expenses
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511601', 'ledger_name' => 'Tax And Vat Expenses', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5117': // Warranty, replacement & discount
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511701', 'ledger_name' => 'Warranty, replacement & discount', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5118': // Depreciation expense
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511801', 'ledger_name' => 'Depreciation expense', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5119': // Cost Of Goods Sold
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '511901', 'ledger_name' => 'Cost Of Goods Sold', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                        ['ledger_code' => '511902', 'ledger_name' => 'Stock Damage', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5120': // Bank Charge
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '512001', 'ledger_name' => 'Bank Charge', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                case '5121': // Bank Charge
                    $accountsLedgers[] = [ 
                        ['ledger_code' => '512101', 'ledger_name' => 'Interest - Bank', 'type_id'=> $accountType->parent_type_id, 'company_id' => $accountType->company_id, 'ledger_type' => 'cr', 'detail_type_id' => $accountType->id, 'status' => '1'],
                    ];
                    break;  
                 // OperaOperating Expenses END
 
            }
        } 

        $thirdStep = collect(); 
        $defaultLedgers = [];
        foreach ($accountsLedgers as $subArray) {
            $defaultLedgers = array_merge($defaultLedgers, $subArray);
        }
        if (!empty($defaultLedgers)) {
            foreach ($defaultLedgers as $defaultLedger) {
                $thirdStep = AccountLedger::updateOrCreate(
                    ['ledger_code' => $defaultLedger['ledger_code'], 'company_id' => $defaultLedger['company_id']],
                    $defaultLedger
                );
            } 
        }        
        return $thirdStep->toArray();
        
    }

    public function companyDefaultSetting($company_id){

        $supplier_payable_account_type = AccountType::where('company_id', $company_id)->where('type_code','2103')->first();  // (Trade Payable) (AccountType)  // ledger code 210301 
        $supplier_discount_account_type = AccountType::where('company_id', $company_id)->where('type_code','4202')->first(); // (Income from Discount (Vendors)) (AccountType) // ledger code 420201 
        $supplier_advance_payment_account_type = AccountType::where('company_id', $company_id)->where('type_code','1206')->first();  // (Other current Assets) (AccountType)  // ledger code 120601 
        $bank_account_asset_type = AccountType::where('company_id', $company_id)->where('type_code','1205')->first(); // (Cash and Cash Equivalents) (AccountType)  // ledger code  
        $bank_loan_account_liability_type = AccountType::where('company_id', $company_id)->where('type_code','2104')->first(); // Loan From Bank (AccountType)  // ledger code 210401 
        $bank_charge_account_expense_type = AccountType::where('company_id', $company_id)->where('type_code','5120')->first(); // Bank Charge (AccountType)  // ledger code 512001 
        $bank_loan_interest_expense_type = AccountType::where('company_id', $company_id)->where('type_code','5121')->first();  // Interest - Bank (AccountType)  // ledger code 512101 
        $bank_interest_income_type = AccountType::where('company_id', $company_id)->where('type_code','4203')->first();  // Interest Income - Bank (AccountType)  // ledger code 420301 
        $customer_receivable_account_type = AccountType::where('company_id', $company_id)->where('type_code','1202')->first();  // Accounts Receivables (AccountType)  // ledger code 120201 

        $inventory_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','120101')->first();           // (Inventories) Ledger
        $cogs_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','511901')->first();                // (Cost Of Goods Sold) Ledger
        $inventory_damage_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','511902')->first();    // (Stock Damage) ledger
        $inventory_adjustment_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','120101')->first();//(Inventories) ledger
        $petty_cash_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','120501')->first();          // (Cash in Hand) ledger
        $cash_in_hand_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','120501')->first();        // (Cash in Hand) ledger
        $bank_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','120502')->first();                // (Default Bank) ledger
        $bank_loan_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','210401')->first();           // (Loan From Bank) Ledger
        $bank_loan_interest_expense_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','512101')->first(); // (Interest - Bank) find expense
        $bank_charge_expense_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','512001')->first(); // (Bank charge) expense find expense
        $bank_interest_income_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','420301')->first();// (Interest Income - Bank) income find Income 

        $trade_payable_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','210301')->first();// (Trade Payable) Ledger   
        $supplier_discount_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','420201')->first(); // (Income from Discount (Vendors)) 
        $cash_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410101')->first();  // (Cash Sales) Ledger  
        $mfs_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410101')->first();   // (Cash Sales) Ledger 
        $bkash_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410101')->first(); // (Cash Sales) Ledger  
        $rocket_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410101')->first();// (Cash Sales) Ledger  
        $nagad_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410101')->first(); // (Cash Sales) Ledger 
        $card_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410103')->first();  // (Card Sales) Ledger 
        $credit_sales_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','410102')->first();// (credit sales) ledger 
        
        $account_receivable_ledger = AccountLedger::where('company_id', $company_id)->where('ledger_code','120201')->first();   // (Accounts Receivables) Ledger
        $customer_discount_account = AccountLedger::where('company_id', $company_id)->where('ledger_code','511701')->first();   // (Warranty, replacement & discount) ledger find expense
        $bank_reference_ledger_mfs = AccountLedger::where('company_id', $company_id)->where('ledger_code','120502')->first();   // (Default bank) ledger  find Asset
        $mfs_charge_ledger = AccountLedger::where('company_id', $company_id)->where('ledger_code','512001')->first();           // (Bank Charge) ledger find in Expense
        $bank_reference_ledger_bkash = AccountLedger::where('company_id', $company_id)->where('ledger_code','120502')->first(); // (Default bank) ledger  
        $bkash_charge_ledger = AccountLedger::where('company_id', $company_id)->where('ledger_code','512001')->first();         // (Bank Charge) ledger
        $bank_reference_ledger_rocket = AccountLedger::where('company_id', $company_id)->where('ledger_code','120502')->first();// (Default bank) ledger  
        $rocket_charge_ledger = AccountLedger::where('company_id', $company_id)->where('ledger_code','512001')->first();        // (Bank Charge) ledger 
        $bank_reference_ledger_nagad = AccountLedger::where('company_id', $company_id)->where('ledger_code','120502')->first(); // (Default bank) ledger 
        $nagad_charge_ledger = AccountLedger::where('company_id', $company_id)->where('ledger_code','512001')->first();         // (Bank Charge) ledger

        $defaultSetting = [
            'supplier_payable_account_type' => $supplier_payable_account_type->id,
            'supplier_discount_account_type' =>$supplier_discount_account_type->id,
            'supplier_advance_payment_account_type' => $supplier_advance_payment_account_type->id,
            'bank_account_asset_type' => $bank_account_asset_type->id,
            'bank_loan_account_liability_type' => $bank_loan_account_liability_type->id,
            'bank_charge_account_expense_type' => $bank_charge_account_expense_type->id,
            'bank_loan_interest_expense_type' => $bank_loan_interest_expense_type->id,
            'bank_interest_income_type' => $bank_interest_income_type->id,
            'customer_receivable_account_type' => $customer_receivable_account_type->id, 
            'inventory_account' => $inventory_account->id,
            'cogs_account' => $cogs_account->id,
            'inventory_damage_account' => $inventory_damage_account->id,
            'inventory_adjustment_account' => $inventory_adjustment_account->id,
            'petty_cash_account' => $petty_cash_account->id,
            'cash_in_hand_account' => $cash_in_hand_account->id,
            'bank_account' => $bank_account->id,
            'bank_loan_account' => $bank_loan_account->id,
            'bank_loan_interest_expense_account' => $bank_loan_interest_expense_account->id,
            'bank_charge_expense_account' => $bank_charge_expense_account->id,
            'bank_interest_income_account' => $bank_interest_income_account->id,
            'trade_payable_account' => $trade_payable_account->id,
            'supplier_discount_account' => $supplier_discount_account->id,
            'cash_sales_account' => $cash_sales_account->id,
            'mfs_sales_account' => $mfs_sales_account->id,
            'bkash_sales_account' => $bkash_sales_account->id,
            'rocket_sales_account' => $rocket_sales_account->id,
            'nagad_sales_account' => $nagad_sales_account->id,
            'card_sales_account' => $card_sales_account->id,
            'credit_sales_account' => $credit_sales_account->id,
            'account_receivable_ledger' => $account_receivable_ledger->id,
            'customer_discount_account' => $customer_discount_account->id,
            'bank_reference_ledger_mfs' => $bank_reference_ledger_mfs->id,
            'mfs_charge_ledger' => $mfs_charge_ledger->id,
            'bank_reference_ledger_bkash' => $bank_reference_ledger_bkash->id,
            'bkash_charge_ledger' => $bkash_charge_ledger->id,
            'bank_reference_ledger_rocket' => $bank_reference_ledger_rocket->id,
            'rocket_charge_ledger' => $rocket_charge_ledger->id,
            'bank_reference_ledger_nagad' => $bank_reference_ledger_nagad->id,
            'nagad_charge_ledger' => $nagad_charge_ledger->id,
        ];
        return AccountDefaultSetting::updateOrCreate(['company_id' => $company_id], $defaultSetting); 
    } 
}
