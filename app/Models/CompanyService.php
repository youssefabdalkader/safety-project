<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyService extends Model
{
    use HasFactory;

    protected $table = '_company_service';

    protected $primaryKey = 'companyServiceId';

    public $timestamps = true;

    protected $fillable = [
        'companyServiceName',
        'companyServiceImageUrl',
        'companyServiceDescription',
    ];
}
