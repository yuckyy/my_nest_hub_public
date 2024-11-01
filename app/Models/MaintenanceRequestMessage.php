<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequestMessage extends Model
{
    public function maintenanceRequest(){
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_user_id');
    }
}
