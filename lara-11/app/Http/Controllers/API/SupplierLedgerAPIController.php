<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\supplierLedgerExportResource;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use App\Exports\Export; 
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SupplierLedgerAPIController extends AppBaseController
{
    public function supplierLedger(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required',
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $supplier_id = $request->get('supplier_id');
        $from_date  = date("Y-m-d", strtotime($request->get('from_date')));
        $to_date  = date("Y-m-d", strtotime($request->get('to_date')));

        $supplier = Supplier::find($supplier_id);

        $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->whereDate('transaction_date', '<', $from_date)->orderBy('id', 'DESC')->first();

        $supplier_ledgers = SupplierLedger::where('supplier_id', $supplier_id)->whereBetween('transaction_date', [$from_date, $to_date])->get();

        $return_data    = [
            'opening_balance' => $supplier_ledger->closing_balance ?? 0,
            'supplier_ledgers' => $supplier_ledgers,
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'supplier_name' => $supplier->name ?? 'N/A'
        ];

        return $this->sendResponse($return_data, 'Ledger Retrieve Successfully');

    }

    public function supplierLedgersExportCollection(Request $request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->supplierLedger($request);  

        // Get the data array from the JSON response
        $data = $response->getData();  
        // dd( $data->data->supplier_ledgers);

        // Now $data contains the array of data 
        $return_data = supplierLedgerExportResource::collection($data->data->supplier_ledgers); 

        return $this->sendResponse($return_data,'');
    } 


    public function supplierLedgersExport(Request $request){
        // Call the index method to retrieve the JSON response 
        $response = $this->supplierLedgersExportCollection($request);  

        // Get the data array from the JSON response
        $data = $response->getData(); 

        $returnData = $data->data;  
        
        $supplier_id = $request->get('supplier_id');

        $supplier = Supplier::find($supplier_id);


        $from_date = request('from_date') ?? Carbon::now()->subMonths(1)->format("Y-m-d");
        $to_date = request('to_date') ?? Carbon::now()->format("Y-m-d");

        $customHeadings = [ ['Supplier Name: '. $supplier->name],['From '.$from_date.' TO '.$to_date]];
        $columns = ['Sl','date', 'opening_balance','purchases_amount','payment','return','discount','closing_balance']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:H1';
        $margeRangeTwo = 'A2:H2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'supplier-ledger.xlsx'); 
    }
}
