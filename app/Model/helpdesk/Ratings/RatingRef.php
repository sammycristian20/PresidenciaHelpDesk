<?php

namespace App\Model\helpdesk\Ratings;

use App\BaseModel;

class RatingRef extends BaseModel
{
    protected $table = 'rating_ref';
    protected $fillable = [

            'rating_id', 'ticket_id', 'thread_id', 'rating_value',
                            ];
    
    /**
     * @depreciated because naming convention is senseless
     */
    public function rating(){
        $related = 'App\Model\helpdesk\Ratings\Rating';
        return $this->belongsTo($related);
    }
    
    public function ratingType(){
        return $this->belongsTo('App\Model\helpdesk\Ratings\Rating','rating_id');
    }
}
