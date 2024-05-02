<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BatchViewExport implements WithMultipleSheets
{
    use Exportable;

    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function sheets(): array
    {
        return [
            new TabelExport($this->id),
            new MetaDataExport($this->id)
        ];
    }
}
