<?php

namespace App\Http\Controllers\API;  

use App\Http\Controllers\AppBaseController; 

use App\Models\PerformaInvoice;
use Illuminate\Http\Request;

class PerformaInvoiceController extends AppBaseController
{
    public function index()
    {
        return PerformaInvoice::with(['letterOfCredits', 'pipelineStocks'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pi_number' => 'required|unique:performa_invoices',
            'vendor_id' => 'required|integer',
            // Add other fields as needed
        ]);

        $pi = PerformaInvoice::create($validated);

        return response()->json($pi, 201);
    }

    public function show($id)
    {
        return PerformaInvoice::with(['letterOfCredits', 'pipelineStocks'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pi = PerformaInvoice::findOrFail($id);
        $pi->update($request->all());

        return response()->json($pi);
    }

    public function destroy($id)
    {
        PerformaInvoice::destroy($id);
        return response()->json(null, 204);
    }
}
