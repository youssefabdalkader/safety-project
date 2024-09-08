<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = '_category';

    protected $primaryKey = 'categoryId';

    public $timestamps = true;

    protected $fillable = [
        'title',
    ];

    public function companyClients()
    {
        return $this->hasMany(CompanyClients::class, 'categoryId');
    }
}
