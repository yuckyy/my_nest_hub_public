<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIdentity extends Model
{

    protected $table = 'user_identities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'account_type', // ['personal', 'soleProprietorship', 'corporation', 'llc', 'partnership']
        'first_name',
        'last_name',
        'email',
        'address',
        'address_2',
        'city',
        'state',
        'zip',
        'dob',
        'ssn',
        'business_name',
        'business_classification',
        'ein',
        'website',
        'phone',
        'log',
        'customer_url',
        'controller_first_name',
        'controller_last_name',
        'controller_title',
        'controller_address',
        'controller_address_2',
        'controller_city',
        'controller_state',
        'controller_zip',
        'status'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function userIdentityDocuments() {
        return $this->hasMany(
            'App\Models\UserIdentityDocument'
        );
    }

    public static function calculateSubmittedHash($dataArray){
        $hash = '';
        switch ($dataArray['account_type']){
            case 'personal':
                $ssn = substr($dataArray['ssn'], -4);
                $fields = ['email', 'first_name', 'last_name', 'address', 'address_2', 'city', 'state', 'zip'];
                foreach($fields as $f){
                    $hash .= ( $dataArray[$f] ?? '' );
                }
                $hash .= $ssn;
                break;
            case 'soleProprietorship':
                $ssn = substr($dataArray['ssn'], -4);
                $fields = ['email', 'first_name', 'last_name', 'address', 'address_2', 'city', 'state', 'zip', 'account_type', 'business_name', 'business_classification', 'ein'];
                foreach($fields as $f){
                    $hash .= ( $dataArray[$f] ?? '' );
                }
                $hash .= $ssn;
                break;
            case 'corporation':
            case 'llc':
            case 'partnership':
                $ssn = substr($dataArray['ssn'], -4);
                $fields = ['email', 'first_name', 'last_name', 'address', 'address_2', 'city', 'state', 'zip', 'account_type', 'business_name', 'business_classification', 'ein', 'controller_first_name', 'controller_last_name', 'controller_title', 'controller_address', 'controller_address_2', 'controller_city', 'controller_state', 'controller_zip'];
                foreach($fields as $f){
                    $hash .= ( $dataArray[$f] ?? '' );
                }
                $hash .= $ssn;
                break;
        }
        return $hash;
    }

    public static function getAccountTypes(){
        return [
            'personal' => 'Personal',
            'soleProprietorship' => 'Sole proprietorships, Unincorporated association, Trust',
            'corporation' => 'Corporation, Publicly traded corporation',
            'llc' => 'LLC, Non-profit',
            'partnership' => 'Partnerships, LP, LLP',
        ];
    }

}
