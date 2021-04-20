<?php


namespace App\FaveoReport\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class UserTodo extends Model
{
    protected $fillable = [

        /**
         * id of the user to whom this belongs
         */
        "user_id",

        /**
         * "todo" name
         */
        "name",

        /**
         * status of "todo". Possible values : "pending", "in-progress" and "completed"
         */
        "status",

        /**
         * Order of "todo"
         */
        "order"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}