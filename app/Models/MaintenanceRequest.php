<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $appends = [
        'truncated_description'
    ];

    public function status() {
        return $this->hasOne('App\Models\MaintenanceRequestStatus', 'id', 'status_id');
    }

    public function priority() {
        return $this->hasOne('App\Models\MaintenanceRequestPriority', 'id', 'priority_id');
    }

    public function creator() {
        return $this->hasOne('App\Models\User', 'id', 'creator_user_id');
    }

    public function unit() {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public function messages() {
        return $this->hasMany(
            'App\Models\MaintenanceRequestMessage'
        );
    }

    public function maintenanceDocuments() {
        return $this->hasMany(
            'App\Models\MaintenanceDocument'
        );
    }

    public function color() {
        if($this->archived){
            return "secondary";
        }
        switch($this->priority_id){
            case 1 :
                $color = "info";
                break;
            case 2 :
                $color = "warning";
                break;
            case 3 :
                $color = "danger";
                break;
            default :
                $color = "secondary";
                break;
        }
        return $color;
    }

    public function getTruncatedDescriptionAttribute() {
        return strlen($this->description) > 300 ?
            substr($this->description, 0 , 300) . '...'
            : $this->description;
    }
}
