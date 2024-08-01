<?php

namespace App\Exports;

use App\Models\RowGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RowGroupExport implements FromCollection, WithHeadings
{
    protected $label;

    public function __construct($label = null)
    {
        $this->label = $label;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = RowGroup::query();
        if ($this->label != 'null') $query->where('label', 'like', '%' . $this->label . '%');
        $rowGroup = $query->get();

        $data = $rowGroup->map(function ($item) {
            return [
                'label' => $item->label
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return ['Kelompok Baris'];
    }
}
