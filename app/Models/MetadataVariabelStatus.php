<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetadataVariabelStatus extends Model
{
    use HasFactory;
    protected $table = 'metadata_variabel_status';
    protected $fillable = ['id_tabel', 'status', 'edited_by'];
    // public $timestamps = false;
    public function tabel() {
        return $this->belongsTo(Tabel::class, 'id_tabel', 'id');
    }
    public function status() {
        return $this->belongsTo(StatusDesc::class, 'status', 'label');
    }

}
