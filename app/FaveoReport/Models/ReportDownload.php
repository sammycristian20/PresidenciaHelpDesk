<?php

namespace App\FaveoReport\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDownload extends Model
{
    protected $fillable = ['file', 'report_id', 'ext', 'type', 'hash', 'expired_at', 'user_id', 'is_completed'];

    /**
     * Relation with \App\User
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
