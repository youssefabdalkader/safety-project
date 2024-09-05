<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyNews extends Model
{
    use HasFactory;

    protected $table = '_company_news';

    protected $primaryKey = 'companyNewId';

    public $timestamps = true;

    protected $fillable = [
        'companyNewTitle',
        'companyNewUrl',
        'companyNewImageUrl',
    ];
}
