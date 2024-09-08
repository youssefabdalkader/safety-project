<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    use HasFactory;

    protected $table = '_service_item';
    protected $primaryKey = 'serviceItemId';

    protected $fillable = ['title'];

    public function companyServices()
    {
        return $this->belongsToMany(CompanyService::class, '_company_service_service_item', 'serviceItemId', 'companyServiceId');
    }
}
