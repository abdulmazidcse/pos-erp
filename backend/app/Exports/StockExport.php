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

class StockExport implements FromCollection, WithHeadings, WithEvents
{
    use Exportable;
    protected $data;
    protected $columns;
    protected $customHeadings;
    protected $dateRange;
    public function __construct($data, $columns, $customHeadings)
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->customHeadings = $customHeadings;
    }

    public function collection()
    {
        $counter = 0; // Initialize the counter
        return collect($this->data)->map(function ($item) use (&$counter) {
            $row = [];
            $counter++;
            foreach ($this->columns as $column) {
                if ($column == 'Sl') {
                    $row[$column] = $counter;
                } elseif ($column == 'Item (Code||Expire Date)') {
                    $row[$column] = $item->product->product_name;
                } else {
                    $row[$column] = $item->$column ? $item->$column : '0';
                }
            }
            return $row;
        });
    }


    public function headings(): array
    {
        return [
            $this->customHeadings[0], // Customer heading row
            $this->customHeadings[1], // Customer heading row
            $this->getDefaultHeadings(), // Default heading row
        ];
    }

    protected function getDefaultHeadings(): array
    {
        return array_map(function ($column) {
            return ucfirst(str_replace('_', ' ', $column));
        }, $this->columns);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                // Merge cells for the custom header
                $event->sheet->mergeCells('A1:F1');
                $event->sheet->mergeCells('A2:F2'); 

                // Center align the merged cells
                $event->sheet->getStyle('A1:F2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A1:F2')->getAlignment()->setVertical('center'); 
            },
        ];
    }
}
