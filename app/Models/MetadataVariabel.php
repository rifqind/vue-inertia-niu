<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetadataVariabel extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'metadata_variabel';
    public function tabel()
    {
        return $this->belongsTo(Tabel::class, 'id_tabel', 'id');
    }
    protected $fillable = [
        'id_tabel',
        'r101',
        'r102',
        'r103',
        'r104',
        'r105',
        'r106',
        'r107',
        'r108',
        'r109',
        'r110',
        'r111',
        'r112',
    ];
}
