<?php

namespace App\Model\helpdesk\Filters;

use Illuminate\Database\Eloquent\Model;
use Lang;

class Label extends Model
{
    protected $table = 'labels';
    protected $fillable = ['title', 'color', 'order', 'status', 'visible_to'];

    public function titleWithColor()
    {
        $title = $this->title;
        $color = $this->color;
        if ($title && $color) {
            return "<a href='".url('tickets?labels[]='.$title)."' title='".Lang::get('lang.timeline-links-title', ['filtername' => Lang::get('lang.label'), 'filtervalue' => $title])."' class='badge text-sm font-weight-normal' style='background-color:" . $color . "; color: #FFF;'>" . $title . "</a>";
        } else {
            return "--";
        }
    }

    public function status()
    {
        $status = $this->status;
        $output = Lang::get('lang.disabled');
        if ($status == 1) {
            $output = Lang::get('lang.enabled');
        }
        return $output;
    }

    public function isChecked($ticketid)
    {
        $title = $this->attributes['title'];
        $output = "";
        $filters = new Filter();
        $filter = $filters
            ->where('ticket_id', $ticketid)
            ->where('key', 'label')
            ->where('value', $title)
            ->first();
        if ($filter && $filter->value) {
            $output = "checked";
        }
        return $output;
    }
    
    /**
     * Get the labels that are assigned to a ticket and their colors
     *
     * @date   2019-05-16T11:23:50+0530
     *
     * @param  integer $ticketid The id of the ticket
     *
     * @return array
     */
    public function assignedLabels($ticketid)
    {
        $output = [];
        $user_role = \Auth::user()->role;
        $filters = new Filter();
        $filter = $filters->getLabelsByTicketId($ticketid);
        if (count($filter) > 0) {
            foreach ($filter as $fil) {
                if ($this->where('title', $fil)->first() != null) {
                    array_push($output, $this->where('title', $fil)->select('title', 'color')->first());
                }
            }
        }
        return $output;
    }
    
    public function deleteFilter()
    {
        $title = $this->attributes['title'];
        Filter::where('value', $title)->where('key', 'label')->delete();
    }
    
    public function delete()
    {
        $this->deleteFilter();
        parent::delete();
    }
}
