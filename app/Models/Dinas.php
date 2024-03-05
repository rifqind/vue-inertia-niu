<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function wilayah(){
        return $this->belongsTo(MasterWilayah::class, 'wilayah_fullcode', 'wilayah_fullcode');
    }
}
