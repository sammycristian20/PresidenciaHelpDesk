<?php

namespace App\Model\helpdesk\Filters;

use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;
use App\Traits\Observable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use Observable;

    protected $table    = 'filters';
    protected $fillable = ['ticket_id', 'key', 'value'];

    public function getLabelTitle($ticketid)
    {
        $filter = $this->where('ticket_id', $ticketid)->where('key', 'label')->first();
        $output = [];
        if ($filter && $filter->value) {
            $labelids = explode(',', $filter->value);
            $labels   = new Label();
            $label    = $labels->whereIn('title', $labelids)->get();
            if ($label->count() > 0) {
                foreach ($label as $key => $l) {
                    $output[$key] = $l->titleWithColor();
                }
            }
        }
        return $output;
    }
    public function getTagsByTicketId($ticketid)
    {
        $filter = $this->where('key', 'tag')->where('ticket_id', $ticketid)->pluck('value')->toArray();
        return $filter;
    }
    
    public function ticket()
    {
        return $this->belongsTo(\App\Model\helpdesk\Ticket\Tickets::class);
    }

    /**
     * Triggers at the end of model activity
     * @param Filter $filter
     * @throws \Exception
     */
    public function afterModelActivity(Filter $filter)
    {
        // calling SLA manually.
        // NOTE: just updating update_at column is avoided in ticketObserver because that column gets updated
        // multiple times. To avoid duplicate calculation, we are restricting it in TicketObserver
        if ($filter->ticket) {
            (new SlaEnforcer($filter->ticket))->handleSlaRelatedUpdates();
        }
    }
}
