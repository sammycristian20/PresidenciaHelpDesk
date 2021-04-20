<?php

namespace App\Model\helpdesk\Ticket;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\helpdesk\Agent\Department;
use Auth;
use Illuminate\Support\Arr;
use Exception;

class TicketFilter extends Model
{
	use Observable;

    protected $fillable = ['name', 'status', 'user_id', 'display_on_dashboard', 'icon_class', 'icon_color'];

	protected $hidden = ["parent_id", "parent_type"];

    /**
     * Relation with user
     */
    public function users()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * Relation with ticket filter meta
     */
    public function filterMeta()
    {
        return $this->hasMany(\App\Model\helpdesk\Ticket\TicketFilterMeta::class);
    }

    public function sharedUsers()
    {
    	return $this->morphedByMany(User::class, 'ticket_filter_shareable');
    }

    public function sharedDepartments()
    {
        return $this->morphedByMany(Department::class, 'ticket_filter_shareable');
    }

    /**
     * If a given filter is accessible to logged in user
     * @param  int  $filterId Id of the filter
     * @return boolean
     */
    public static function isGivenFilterAccessible($filterId) : bool
    {
			$accessibleFilters = self::getAccessibleFilterIds(true);

			return in_array($filterId, $accessibleFilters);
    }

		/**
		 * Gets accessible filter ids
		 * @param bool $collapse  if passed as false, result will be ['own'=>[3,4], 'shared'=>[1,2]],
		 * 												if passed true it will be [1,2,3,4]
		 * @return array
		 */
		public static function getAccessibleFilterIds($collapse = false) : array
		{
			$accessibleFilters = ['own_ids'=>[], 'shared_ids'=>[]];

			if(Auth::user()){
				// own filters
				$accessibleFilters['own_ids'] = Auth::user()->ticketFilters()->pluck('id')->toArray();

				// individual share
				$accessibleFilters['shared_ids'] = Auth::user()->ticketFilterShares()
					->pluck('ticket_filter_id')->toArray();

				// department share
				foreach (Auth::user()->departments as $department) {
					$deptFilterIds = $department->ticketFilterShares()
						->pluck('ticket_filter_id')->toArray();
					$accessibleFilters['shared_ids'] = array_merge($deptFilterIds, $accessibleFilters['shared_ids']);
				}
			}

			return $collapse ? Arr::collapse($accessibleFilters) : $accessibleFilters;
		}

    /**
     * Gets filter data in key value pair by filter Id
     * @param  int $filterId
     * @return array
     */
    public static function getFilterParametersByFilterId(int $filterId) : array
    {
      return TicketFilterMeta::where('ticket_filter_id', $filterId)->get(['key','value'])
        ->map(function($element){
          return [ $element->key => $element->value ];
        })->collapse()->toArray();
    }

    public function parent()
    {
        return $this->morphTo();
    }

    public function beforeDelete($model)
    {
        foreach ($model->filterMeta as $meta) {
            $meta->delete();
        }
    }

    public function getIconClassAttribute($value)
    {
        if(!$value){
            return 'fa fa-circle-o';
        }
        return $value;
    }
}
