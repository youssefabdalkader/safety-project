<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyClients extends Model
{
    use HasFactory;

    protected $table = '_company_clients';

    protected $primaryKey = 'clientId';

    public $timestamps = true;

    protected $fillable = [
        'clientImageUrl',
        'clientname',
        'categoryId',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }
}
