<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Certificate extends Model
{
    protected $table = '_company_certificate';

    protected $primaryKey = 'certificateCode';
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = [
        'certificateCode',
        'certificatePhotoUrl',
        'startat',
        'endat',
        'invalid',
    ];

    // Automatically update 'invalid' status
   
    public function getInvalidAttribute()
    {
        $now = Carbon::now('Africa/Cairo')->startOfDay(); // تعيين الوقت إلى بداية اليوم
        $startat = Carbon::parse($this->startat)->startOfDay();
        $endat = Carbon::parse($this->endat)->endOfDay();

        return $now->between($startat, $endat);
    }
}

