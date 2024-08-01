<?php

namespace App\Exports;

use App\Models\Column;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ColumnExport implements FromCollection, WithHeadings
{
    protected $label;
    protected $columnGroupsLabel;

    public function __construct($label = null, $columnGroupsLabel = null)
    {
        $this->label = $label;
        $this->columnGroupsLabel = $columnGroupsLabel;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = Column::query();
        $query->join('column_groups as cg', 'cg.id', '=', 'columns.id_column_groups')
            ->select([
                'columns.*',
                'cg.label as columnGroupsLabel'
            ]);
        if ($this->label != 'null') $query->where('columns.label', 'like', '%' . $this->label . '%');
        if ($this->columnGroupsLabel != 'null') $query->where('cg.label', 'like', '%' . $this->columnGroupsLabel . '%');

        $columns = $query->get();

        $data = $columns->map(function ($item) {
            return [
                'label' => $item->label,
                'columnGroupsLabel' => $item->columnGroupsLabel
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return ['Kolom', 'Kelompok Kolom'];
    }
}
