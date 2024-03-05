<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurTahunGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'turtahun_groups';
    // protected $table = 'column_groups';
    public $timestamps = false;
}
