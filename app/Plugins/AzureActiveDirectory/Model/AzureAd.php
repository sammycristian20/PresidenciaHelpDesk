<?php


namespace App\Plugins\AzureActiveDirectory\Model;


use App\BaseModel;

class AzureAd extends BaseModel
{
    protected $table = 'azure_ads';

    protected $fillable = [
        'app_name', 'tenant_id', 'app_id', 'app_secret', 'login_button_label'
    ];
}