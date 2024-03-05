<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabel extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function dinas(){
        return $this->belongsTo(Dinas::class, 'id_dinas', 'id');
    }
    public function subjects(){
        return $this->belongsTo(Subject::class, 'id_subjek', 'id');
    }
    
}
