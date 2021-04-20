<?php

namespace App\Traits;

use App\User;
use App\Http\Controllers\Common\PhpMailController;
use \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController;

/**
 * Provides common functionality for entity verification process. Such entities 
 * can be email, mobile or any other entity which we can add in future. This
 * This trait can be used by custom module and plug-ins to update verify status
 * send verification notifications to users.
 *
 * @package App\Traits
 * @since v4.0.0
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 *
 * @todo update and maintain this trait for adapting other entities like mobile
 * or any future field which needs to be verified and require to send notifications
 */
trait UserVerificationHelper
{
	/**
     * Function to sendEmail by the passed user
     * @param  User  $user
     * @return void
     */
    private function sendVerificationEmail(User $user):void
    {
    	$phpMailController = new PhpMailController();
        $notifications[]=[
                'email_verify_alert'=>[
                    'userid'=> $user->id,
                    'from'=>$phpMailController->mailfrom('1', '0'),
                    'message'=>['subject' => 'verification Required', 'scenario' => 'email_verify'],
                    'variable'=>['new_user_name' => $user->name(), 'new_user_email' => $user->email, 'account_activation_link' => faveoUrl('account/activation/' . $user->email_verify)],
                ]
            ];

        $alert = new NotificationController();
        $alert->setDetails($notifications);
    }

    /**
     * Function updates User and sets verified as true
     * @param  User   $user        user whose entities needs to be set as verified
     * @param  array  $columnNames entities/column names to set as verified
     * @return void
     */
    private function setEntitiesVerifiedByColumnNames(User $user, $columnNames = []):void
    {
    	foreach ($columnNames as $columns) {
    		$user->{$columns}=1;
    	}
    	$user->save();
    }

    /**
     * Function updates User and sets verified as token value
     * @param  User   $user        user whose entities needs to be set as verified
     * @param  array  $columnNames entities/column names to set as verified
     * @return void
     */
    private function setEntitiesUnverifiedByColumnNames(User $user, $columnNames = []):void
    {
        foreach ($columnNames as $columns) {
            $user->{$columns}="x".str_random(59);
        }
        $user->save();
    }

    /**
     * Function sets User mode entities based on values in model.
     * e.g: if User has value of email then email_verify will be set as true
     * 
     * @param   User $user
     * @return  void
     */
    private function setEntitiesVerifiedByModel(User $user):void
    {
    	$columnNames = [];
    	if($user->email)
    		array_push($columnNames, "email_verify");
    	if($user->mobile)
    		array_push($columnNames, "mobile_verify");
    	$this->setEntitiesVerifiedByColumnNames($user, $columnNames);
    }

    /**
     * Function sets User mode entities based on values in model.
     * e.g: if User has value of email then email_verify will be set as true
     * 
     * @param   User $user
     * @return  void
     */
    private function setEntitiesUnverifiedByModel(User $user):void
    {
        $columnNames = [];
        if(!$user->email)
            array_push($columnNames, "email_verify");
        if(!    $user->mobile)
            array_push($columnNames, "mobile_verify");
        $this->setEntitiesUnverifiedByColumnNames($user, $columnNames);
    }
}