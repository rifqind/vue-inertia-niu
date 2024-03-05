<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDesc extends Model
{
    use HasFactory;
    protected $table = 'status_desc';
    public $timestamps = false;
    protected $guarded = ['id'];
}
