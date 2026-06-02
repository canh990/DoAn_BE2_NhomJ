<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TroGiup extends Model
{
    protected $table = 'tro_giup';

    protected $fillable = [
        'loai',
        'khoa',
        'cau_hoi',
        'tra_loi',
        'ngon_ngu',
    ];
}
