<?php

namespace App\Exports;

use App\Models\Row;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RowExport implements FromCollection, WithHeadings
{
    protected $label;
    protected $rowGroupsLabel;

    public function __construct($label = null, $rowGroupsLabel = null)
    {
        $this->label = $label;
        $this->rowGroupsLabel = $rowGroupsLabel;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = Row::query();
        $query->join('row_groups as rg', 'rg.id', '=', 'rows.id_row_groups')
            ->select([
                'rows.*', 'rg.label as rowGroupsLabel'
            ]);
        if ($this->label != 'null') $query->where('rows.label', 'like', '%' . $this->label . '%');
        if ($this->rowGroupsLabel != 'null') $query->where('rg.label', 'like', '%' . $this->rowGroupsLabel . '%');
        
        $rows = $query->get();

        $data = $rows->map(function ($item) {
            return [
                'label' => $item->label,
                'rowGroupsLabel' => $item->rowGroupsLabel
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return ['Baris', 'Kelompok Baris'];
    }
}
