<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCertificate extends Model
{
    use HasFactory;

    protected $table = '_company_certificate';

    protected $primaryKey = 'certificateId';

    public $timestamps = true;

    protected $fillable = [
        'certificateCode',
        'certificatePhotoUrl',
    ];

  

    protected $hidden = ['certificateId'];

    
}