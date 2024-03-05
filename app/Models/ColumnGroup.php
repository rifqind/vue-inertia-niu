<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColumnGroup extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // protected $table = 'column_groups';
    public $timestamps = false;
}
