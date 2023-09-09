<?php
// app/Exports/Export.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; 
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping; 
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\Exportable;

class ChatOfAccountsExport implements FromCollection
{
    use Exportable;
    protected $data;
    protected $columns;
    protected $customHeadings;
    protected $margeRangeOne;
    protected $margeRangeTwo;
    public function __construct($data )
    {
        $this->data = $data; 
    }

    public function collection()
    {
        // Replace this with your actual data array
        // $data = [
        //     $this->data
        // ];

       // return collect($this->data);


        return collect($this->data)->map(function ($item) use (&$counter) {
            if ($item['children']) {
                // $nestedData = json_decode($item['children'], true);
                dd($item['children']);
                //return collect($nestedData)->map(function ($child) use (&$counter) {  
                    // if ($child['children']) {
                    //     $nestedDataJson = json_decode($child['children'], true);
                    //     return collect($nestedDataJson)->map(function ($subChild) use (&$counter) {
                    //         // Process the $subChild here
                    //         return $subChild;
                    //     });
                    // }
                    // Process the $child here
                    //return $child;
                //});
            }
            // Process the $item here
            return $item;
        });
        
    } 


    public function headings(): array
    {
        // Define your column headings here
        return [
            'ID',
            'Code',
            'Name',
            'Account Type',
            'Account Type Name',
            // Add more headings for nested data if needed
        ];
    }
}
