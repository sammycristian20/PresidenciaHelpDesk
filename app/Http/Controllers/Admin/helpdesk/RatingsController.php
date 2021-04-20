<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ratings\Rating;
use App\Model\helpdesk\Ratings\RatingRef;
use App\Model\helpdesk\Ticket\Tickets;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Common\AlertAndNotification;
/**
 * 	Class which handles all functionalities related to Ratings settings.
 *
 * @category Controller
 * @package  App\Http\Controllers\Admin
 * @author   Manish Verma <manish.verma@ladybirdweb.com>
 * @since    v1.9.46
 * @todo     Move all settings related method such as create/edit or delete from 
 *           SettignsController to this class.
 */
class RatingsController extends Controller
{

	/**
	 * Generates rating links with icon to use in Rating feedback emails.
	 *
	 * @param  int     $ticketId  Id of the ticket
	 * @return string             string containging HTML hypelinks for ratings
	 */
	public function getRatingsIconWithLink(int $ticketId)
	{
		$encryptedId = \Crypt::encrypt($ticketId);
		$string = '';
		$rating = Rating::find(1);
		$icon = $this->getIcon($rating->rating_icon);
		for($i = 1; $i <=$rating->rating_scale; $i++) {
			$link = url('rating', [$encryptedId, \Crypt::encrypt($i)]);
			$title = trans('lang.click_here_to_rate', ['rating' => $i]);
			$string .= '<a href="'.$link.'" title="'.$title.'"style="text-decoration:none"><span style="font-size:30px;color:'.$this->getColor($i, $rating->rating_scale).'">'.$icon.'</span><sub>'.$i.'</sub></a>&nbsp;&nbsp;&nbsp;';
		}
		return $string;
	}

	/**
	 * Gives icon for sending in Ratings mail
	 * @param  string   $iconName  Name of the icon in database
	 * @return string              Icon (string)
	 */
	private function getIcon(string $iconName)
	{
		switch ($iconName) {
			case 'star':
				return '★';
			case 'star-o':
				return '☆';
			default:
				return '★';
		}
	}

	/**
	 * Generates color of icon on basis of total rating scales and currnt icon value
	 * @param  int     $value  icon weightage
	 * @param  int     $total  Total rating scale value
	 * @return string          Hexcode of color for given $value
	 */
	private function getColor(int $value,int $total) {
		$half = (int)($total/2);
		return checkArray((8/2)-($half-($value-1)),['#FEFCD1', '#FFF9A2', '#FFF98A', '#FFF65E', '#FFF42E', '#FEF100', '#FEF40F', '#FEDB02']);
	}


	/**
	 * Handles rating submission by clients from rating mails
	 * @param  string   $ticketId   Encrypted id of ticket
	 * @param  int      $rate       Rating given by users  
	 * @return View                 Ratings view with fails/success message
	 */
	public function saveRatingFeedBack(string $ticketId, string $rate)
	{
		try {
			$decrytedId = \Crypt::decrypt($ticketId);
			$rate = \Crypt::decrypt($rate);
			$ticket = Tickets::find($decrytedId);
			if ($ticket && $rate) {
				if (RatingRef::where('ticket_id', $decrytedId)->count() && !Rating::find(1)->allow_modification) return errorResponse(trans('lang.rating-modification-not-allowed'));
				RatingRef::updateOrCreate(['rating_id' => 1, 'ticket_id' => $decrytedId],[
					'thread_id' => '0',
            	    'rating_value' => $rate
				]);
            	$notificationStatus = (new AlertAndNotification)->checkAlertAndNotification("rating_confirmation");
            	$notificationStatus = ($notificationStatus == null) ? [] : $notificationStatus;
            	(new TicketController)->sendRatingNotify($notificationStatus, $ticket, $rate);
				
        return successResponse(trans('lang.thanks-for-rating'));
			}

			return errorResponse(trans('lang.sorry_you_are_not_allowed_token_expired_or_not_found'));
		} catch (\Exception $e) {

			return errorResponse(trans('lang.sorry_you_are_not_allowed_token_expired_or_not_found'));
		}
	}
}