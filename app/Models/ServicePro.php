<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePro extends Model
{
    protected $table = 'services_pro';
    protected $servicePro = [
        'user_id', 'first_name', 'last_name',
        'middle_name', 'company_name', 'company_website', 'email','phone', 'fax',
        'tax_identity_type', 'tax_payer_id',
        'display_as_company', 'category', 'street_address', 'city', 'state_region', 'zip',
        'country'
    ];
    //
}
