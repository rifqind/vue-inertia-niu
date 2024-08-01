<?php

namespace App\Exports;

use App\Models\ColumnGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ColumnGroupExport implements FromCollection, WithHeadings
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
        $query = ColumnGroup::query();
        if ($this->label != 'null') $query->where('label', 'like', '%' . $this->label . '%');
        $columnGroup = $query->get();

        $data = $columnGroup->map(function ($item) {
            return [
                'label' => $item->label
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return ['Kelompok Kolom'];
    }
}
