<?php

namespace App\Model\helpdesk\Ticket;

use Illuminate\Database\Eloquent\Model;

class TicketFilterMeta extends Model
{
    public $timestamps = false;

    protected $table = 'ticket_filter_meta';

    protected $fillable = ['ticket_filter_id', 'key', 'value', 'value_meta'];

    protected $hidden = ["ticket_filter_id"];

    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    public function getValueMetaAttribute($value)
    {
        return unserialize($value);
    }

    public function setValueMetaAttribute($value)
    {
        $this->attributes['value_meta'] = serialize($value);
    }

    public function ticketFilter()
    {
        $this->belongsTo(\App\Model\helpdesk\Ticket\TicketFilter::class);
    }
}
