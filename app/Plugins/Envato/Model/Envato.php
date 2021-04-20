<?php namespace App\Plugins\Envato\Model;

use Illuminate\Database\Eloquent\Model;

class Envato extends Model {

	protected $table='envato';

	protected $fillable = ['token','mandatory','client_id','allow_expired','access_token','refresh_token','envato_account'];

}
