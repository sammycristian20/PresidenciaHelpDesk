<?php

namespace App\Plugins\SMS\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\SMS\Controllers\SMSTemplateController;
use App\Http\Controllers\Common\TemplateVariablesController;
use DB;
use Lang;
use Finder;
use Exception;

/**
 *@category class MsgNotificationController
 *This class handles the evnets to send nonew TemplateVariablesController;
 dd(tification messag*This class handles ->getVariableValues();es to the clients
 *This class uses Msg91Controller
 *@author manish.verma@ladybirdweb.com
 */
class MsgNotificationController extends Controller
{
    /**
     * @category function to handle SMS sending
     * @param array $user_array, $variables_array, $message_array, $ticket_array
     * @return
     */
    public function notifyBySMS($user_array, $variables_array, $message_array, $ticket_array)
    {
        $user = $this->formatRecieverDetails($user_array);
        $temp_var_controller = new TemplateVariablesController;
        if ($user['email'] == $temp_var_controller->checkElement('agent_email', $variables_array)) {
            $assigned_agent = true;
        } else {
            $assigned_agent = false;
        }
        $Controller = new SMSTemplateController;
        $content = $Controller->getTemplateBody($message_array['scenario'], $user['role'], $assigned_agent, $user['user_language']);
        $variables = $temp_var_controller->getVariableValues(array_merge($user, $variables_array));
        $content = $this->replaceTemplateVariablesWithValues($variables, $content);
        $message = $this->formatMessageBody($content);
        $this->sendSMS($user['number'], $message);
    }

    /**
     *@category function to format message body
     *@param string $comment
     *@return string $comment
     *
     */
    public function formatMessageBody($comment)
    {
        $comment = str_replace('<br>', "\r\n", $comment);
        $comment = str_replace('<br/>', "\r\n", $comment);
        $comment = str_replace('</div>', "\r\n", $comment);
        $comment = str_replace('</p>', "\r\n", $comment);
        $comment = str_replace('".."', '', $comment);
        $comment = preg_replace('/<[^>]*>/', '', $comment);
        return $comment;
    }

    /**
     *@category function to send SMS using sms() function of Msg91Controller
     *@param string $number, string $message (mobile number of a user and messge to be sent)
     *@return null
     */
    public function sendSMS($number, $message)
    {
        $msg_Controller = new Msg91Controller();//creatign object of msg91Controller
        $response = $msg_Controller->sms($number, $message);
    }

    /**
     * @category funtction to format user details name and number
     * @param array $array
     * @return array $user
     */
    public function formatRecieverDetails($array)
    {
        $user = [];
        if ($array['first_name'] != '' || $array['first_name'] != null) {
            $user['receiver_name'] = $array['first_name'].' '.$array['last_name'];
        } else {
            $user['receiver_name'] = $array['user_name'];
        }
        $user['number'] = $array['country_code'].''.$array['mobile'];
        $user['role'] = $array['role'];
        $user['user_language'] = $array['user_language'];
        $user['email'] = $array['email'];
        return $user;
    }

    /**
     *
     *
     *
     */
    public function replaceTemplateVariablesWithValues($variables, $content)
    {
        foreach ($variables as $k => $v) {
            $messagebody = str_replace($k, $v, $content);
            $content = $messagebody;
        }
        return $content; 
    }
}
