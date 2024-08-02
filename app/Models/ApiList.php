<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiList extends Model
{
    use HasFactory;
    protected $table = 'api_list';
    protected $fillable = ['key', 'wilayah_fullcode'];
    public $timestamps = false;
}
