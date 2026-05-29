<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chan extends Model
{
    protected $table = 'chan';
    public $timestamps = false;

    protected $fillable = [
        'nguoi_chan_id',
        'nguoi_bi_chan_id',
        'ngay_tao',
    ];

    protected $casts = [
        'ngay_tao' => 'datetime',
    ];

    public function blocker()
    {
        return $this->belongsTo(User::class, 'nguoi_chan_id');
    }

    public function blocked()
    {
        return $this->belongsTo(User::class, 'nguoi_bi_chan_id');
    }
}
