<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;
    protected $table = 'notifikasi';
    protected $guarded = ['id'];
    public function statustabel(){
        return $this->belongsTo(Statustables::class, 'id_statustabel', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

}
