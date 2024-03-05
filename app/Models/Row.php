<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    public $timestamps = false;
    public function id_rowlabel(){
        return $this->belongsTo(Rowlabel::class, 'id_rowlabels', 'id');
    }
}
