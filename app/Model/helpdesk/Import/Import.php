<?php


namespace App\Model\helpdesk\Import;


use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $table = 'imports';
    protected $fillable = ['path', 'columns'];
}