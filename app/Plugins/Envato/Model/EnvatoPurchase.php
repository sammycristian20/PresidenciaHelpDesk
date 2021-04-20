<?php namespace App\Plugins\Envato\Model;

use Illuminate\Database\Eloquent\Model;

class EnvatoPurchase extends Model {

	protected $table='envato_purchases';

	protected $fillable = ['username','sold_at','supported_until','licence','purchase_code','ticket_id'];
       
        protected $dates = ['sold_at','supported_until'];

}