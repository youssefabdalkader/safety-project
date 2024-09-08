<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyService extends Model
{
    use HasFactory;

    protected $table = '_company_service';
    protected $primaryKey = 'companyServiceId';

    protected $fillable = [
        'companyServiceName',
        'companyServiceImageUrl',
    ];

    public function serviceItems()
    {
        return $this->belongsToMany(ServiceItem::class, '_company_service_service_item', 'companyServiceId', 'serviceItemId');
    }
}

?>