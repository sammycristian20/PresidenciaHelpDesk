<?php

namespace App\Http\Controllers\Common\Mail;

use App\Http\Controllers\Controller;
use Exception;
use Config;
use App\Model\helpdesk\Email\Emails;
use Lang;
use Logger;

/**
 * this class handles all the basic functionality related to sending/fetching mail.
 * It can be extended by other child classes to use its functionality.
 *
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 *
 */
class BaseMailController extends Controller
{
    /**
     * @var Emails          email config object, with all the email configuration values.
     */
    protected $emailConfig;

    /**
     * @var object          stores exceptions|error-messages thrown by php-imap(in case any)
     */
    protected $error;


    protected function checkFetchConnection(Emails $emailConfig){
        try{
            $this->emailConfig = $emailConfig;

            $provider = $this->emailConfig->fetching_protocol == 'ews' ? new ExchangeWebServices($this->emailConfig) : new PhpImap($this->emailConfig);

            return $provider->checkIncomingConnection();
        }
        catch(Exception $e){
            $this->error = $e;
            return false;
        }
    }

    /**
     * checks send connection based on the mail driver
     * @param Emails $emailConfig emailConfig object
     * @return bool
     */
    protected function checkSendConnection(Emails $emailConfig)
    {
        try{
            if(!$emailConfig->sending_protocol){
                throw new \UnexpectedValueException("sending protocol must be provided");
            }

            $this->emailConfig = $emailConfig;

            //if sending protocol is mail, no connection check is required
            if($this->emailConfig->sending_protocol == 'mail'){
                return $this->checkMailConnection();
            }

            if($this->emailConfig->sending_protocol == 'ews'){
                return (new ExchangeWebServices($emailConfig))->checkOutgoingConnection();
            }

            //set outgoing mail configuation to the passed one
            setServiceConfig($this->emailConfig);

            if($this->emailConfig->sending_protocol == 'smtp'){
                return $this->checkSMTPConnection();
            }

            return $this->checkServices();
        } catch(Exception $e){
            $this->error = $e;
            return false;
        }

    }


    /**
     * checks if php's mail function is enabled on current server
     * @return boolean  true if enabled else false
     */
    private function checkMailConnection(){
        if(function_exists('mail')){
            return true;
        }
        $this->error = Lang::get('lang.mail_function_disabled');
        return false;
    }

    /**
     * Checks services status by raw sending mail and waiting for the response
     * @return boolean      true if success else false
     */
    private function checkServices(){
        try{

            $protocolName = $this->emailConfig->sending_protocol;

            //sending a text message and checking if respond comes. If yes, connection is considered to be successful
            \Mail::raw("This is a test mail for successful $protocolName connection", function ($message){
                 $message->to($this->emailConfig->email_address);
            });

            if(count(\Mail::failures()) > 0) {
                $this->error = Lang::get('lang.unknown_error_occured');
                return false;
            }

            return true;

        }catch(\Exception $e){

            $this->error = $e;
            return false;
        }
    }

    /**
     * Checks smtp connection stream. If exception is found, it writes the exception method to $this->error
     * TO DO: it is not required to set email configuration before checking the stream in above method,
     * because it is in this method too.
     * @return boolean      true if success else false
     */
    private function checkSMTPConnection(){
        try{

            $https = [];
            $https['ssl']['verify_peer']      = false;
            $https['ssl']['verify_peer_name'] = false;

            $transport = new \Swift_SmtpTransport(Config::get('mail.host'), Config::get('mail.port'), Config::get('mail.security'));
            $transport->setUsername(\Config::get('mail.username'));
            $transport->setPassword(\Config::get('mail.password'));
            $transport->setStreamOptions($https);
            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();

            return true;
        } catch(\Swift_TransportException $e){
            $this->error = $e;
            return false;
        } catch(\Exception $e){
            $this->error = $e;
            return false;
        }
    }
}
