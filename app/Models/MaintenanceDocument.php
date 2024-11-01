<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceDocument extends Model
{
    public function icon()
    {
        switch($this->extension){
            case 'pdf' :
                $icon = '<i class="fal fa-file-pdf"></i>';
                break;
            case 'doc' :
            case 'docx' :
                $icon = '<i class="fal fa-file-word"></i>';
                break;
            case 'xls' :
            case 'xlsx' :
                $icon = '<i class="fal fa-file-excel"></i>';
                break;
            case 'csv' :
                $icon = '<i class="fal fa-file-csv"></i>';
                break;
            case 'png' :
            case 'jpeg' :
            case 'jpg' :
            case 'gif' :
                $icon = '<i class="fal fa-file-image"></i>';
                break;
            default :
                $icon = '<i class="fal fa-file-alt"></i>';
                break;
        }
        return $icon;
    }
}
