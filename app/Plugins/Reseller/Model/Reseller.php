<?php namespace App\Plugins\Reseller\Model;

use Illuminate\Database\Eloquent\Model;

class Reseller extends Model {

	protected $table='reseller';

	protected $fillable = ['userid','apikey','enforce','mode'];
        
        

}
