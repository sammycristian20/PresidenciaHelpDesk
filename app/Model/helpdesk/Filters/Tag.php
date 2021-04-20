<?php

namespace App\Model\helpdesk\Filters;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['name', 'description'];

    public function filter()
    {
        $name = $this->attributes['name'];
        $filter = Filter::where('value', $name)->where('key', 'tag');
        return $filter;
    }

    public function update(array $attributes = array(), array $options = array())
    {
        if (checkArray('name', $attributes)) {
            $this->filter()->update([
                'value' => $attributes['name']
            ]);
        }
        parent::update($attributes, $options);
    }

    /**
     * Get the tags that are assigned to a ticket
     *
     * @date   2019-05-24T11:23:50+0530
     *
     * @param  integer $ticketid The id of the ticket
     *
     * @return array
     */
    public function assignedTags($ticketid)
    {
        $output = [];
        $filters = new Filter();
        $filter = $filters->getTagsByTicketId($ticketid);
        if (count($filter) > 0) {
            foreach ($filter as $fil) {
                if ($this->where('name', $fil)->first() != null) {
                    array_push($output, $this->where('name', $fil)->select('id', 'name')->first());
                }
            }
        }
        return $output;
    }

    public function delete()
    {
        $this->filter()->delete();
        parent::delete();
    }
}
