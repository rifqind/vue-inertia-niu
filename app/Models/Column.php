<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function columnGroups(){
        return $this->belongsTo(ColumnGroup::class, 'id_column_groups', 'id');
    }
}
