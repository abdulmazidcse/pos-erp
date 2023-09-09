<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements
    ToModel,
    WithHeadingRow,
    WithValidation
{
    use Importable;

    public $customer_data;
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $this->customer_data = $row;


        $company = Company::where('name', $row['company'])->first();
        $customer_group = CustomerGroup::where('title', $row['customer_group'])->first();

        $date_of_birth = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dob']))->format("Y-m-d");
        return new Customer([
            'customer_code' => $row['customer_code'],
            'name'  => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'address'   => $row['address'],
            'dob'   => $date_of_birth,
            'customer_group_id' => $customer_group->id,
            'company_id'    => $company->id ?? 0,
            'discount_percent' => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.customer_code'   => 'required|unique:customers,customer_code',
            '*.name'   => 'required',
            '*.phone'   => 'required|unique:customers,phone',
            '*.address'   => 'required',
            '*.customer_group'   => 'required|unique:customers,customer_group_id',
        ];
    }
}
