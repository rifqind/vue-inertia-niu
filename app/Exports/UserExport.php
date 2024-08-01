<?php

namespace App\Exports;

use App\Models\MasterWilayah;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    protected $username;
    protected $name;
    protected $nama_dinas;
    protected $wilayah_label;
    protected $noHp;
    protected $role;

    public function __construct(
        $username = null,
        $name = null,
        $nama_dinas = null,
        $wilayah_label = null,
        $noHp = null,
        $role = null,
    ) {
        $this->username = $username;
        $this->name = $name;
        $this->nama_dinas = $nama_dinas;
        $this->wilayah_label = $wilayah_label;
        $this->noHp = $noHp;
        $this->role = $role;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = User::query();
        $query->join('dinas', 'users.id_dinas', '=', 'dinas.id')
            ->join('master_wilayah as w', 'w.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')
            ->orderBy('dinas.wilayah_fullcode', 'asc')
            ->whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah())->with('dinas')
            ->select(['users.*', 'dinas.nama as nama_dinas', 'w.label as wilayah_label']);

        if ($this->username != 'null') {
            $query->where('username', 'like', '%' . $this->username . '%');
        }
        if ($this->name != 'null') {
            $query->where('name', 'like', '%' .  $this->name . '%');
        }
        if ($this->nama_dinas != 'null') {
            $query->where('dinas.nama', 'like', '%' .  $this->nama_dinas . '%');
        }
        if ($this->wilayah_label != 'null') {
            $query->where('w.label', 'like', '%' . $this->wilayah_label . '%');
        }
        if ($this->noHp != 'null') {
            $query->where('noHp', 'like', '%' . $this->noHp . '%');
        }
        if ($this->role != 'null') {
            $query->where('role', 'like', '%' . $this->role . '%');
        }

        $user = $query->get();

        $data = $user->map(function ($item) {
            return [
                'username' => $item->username,
                'name' => $item->name,
                'nama_dinas' => $item->nama_dinas,
                'wilayah_label' => $item->wilayah_label,
                'noHp' => $item->noHp,
                'role' => $item->role,
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            'Username', 'Nama', 'Produsen Data', 'Wilayah Kerja', 'Nomor HP', 'Peran'
        ];
    }
}
