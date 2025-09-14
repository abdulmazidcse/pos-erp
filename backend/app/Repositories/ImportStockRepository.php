<?php

namespace App\Repositories;

use App\Models\ImportStock;
use App\Repositories\BaseRepository;

class ImportStockRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'vendor_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return ImportStock::class;
    }
}
