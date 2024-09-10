<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCategory extends Model
{
    use HasFactory;
    public $table = 'data_category';
    public $timestamps = false;
    protected $fillable = ['label'];
}
