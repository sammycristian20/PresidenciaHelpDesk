<?php

namespace App\Model\satellite;

use App\BaseModel;

class SatelliteHelpdesk extends BaseModel
{
    /* define the table name */

    protected $table = 'satellite_helpdesk_user';

    /* Define the fillable fields */
    protected $fillable = ['id', 'user_id', 'helpdesk_id','created_at', 'updated_at'];

    
}
