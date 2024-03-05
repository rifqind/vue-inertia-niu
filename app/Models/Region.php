<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function getMyRegion()
    {
        // $user = auth()->user();
        if (auth()->user()->dinas->regions->kode == 7100){
            $region = Region::all();
        } else {
            $region = Region::where('kode', auth()->user()->dinas->regions->kode)->get();
        }
        return $region;
    }

    public static function getRegionId()
    {
        // $user = auth()->user();
        if (auth()->user()->dinas->regions->kode == 7100){
            $region = Region::all()->pluck("id");
        } else {
            $region = Region::where('kode', auth()->user()->dinas->regions->kode)->pluck("id");
        }
        return $region;
    }
}
