<?php


namespace App\Plugins\Twitter\Traits;

use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\TwitterOAuth\TwitterOAuth;
use DateTime;
use DateTimeZone;
use Logger;

trait CommonTwitter
{
    private $twitter;

    private $credentials;

    public function __construct()
    {
        $this->credentials = TwitterApp::first();
    }

    /**
     * Sets the Twitter OAUTH Instance
     * @param array $settings
     * @return false
     */
    private function TwitterInit(array $settings)
    {
        $twitterInitialized = false;

        try {
            $this->twitter = new TwitterOAuth(
                $settings['consumer_key'],
                $settings['consumer_secret'],
                $settings['access_token'],
                $settings['access_secret']
            );
            $this->twitter->setTimeouts(10,15);
            $twitterInitialized =  (bool) $this->twitter;
        } catch (\Exception $e) {
            Logger::exception($e);
        }

        return $twitterInitialized;
    }

    /**
     * Retrieves the twitter credentials if it is registered or null otherwise.
     * @param bool $json
     * @return \Illuminate\Http\JsonResponse|array|null
     */
    public function getCredentials($json=true)
    {
        $credentials = ($this->credentials) ?: TwitterApp::first();

        if ($credentials) {
            return ($json) ? successResponse($credentials->toArray()) : $credentials->toArray();
        }
    }

    /**
     * Converts deeply nested objects to array
     * This is required because twitter returns deeply nested objects
     * @param $nestedObject
     * @return mixed
     */
    private function convertNestedObjectToArray($nestedObject)
    {
        return json_decode(json_encode($nestedObject), true);
    }

    /**
     * Formats the tweet/message created time to UTC timezone
     * Required since twitter returns time in epoch for direct messages
     * @param string $dateTime
     * @return string
     * @throws \Exception
     */
    private function formatTimezone(string $dateTime)
    {
        $date = new DateTime($dateTime, new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Gets the latest message id fetched by twitter
     * @param string $type
     * @return string
     */
    private function getSinceIdForTwitter(string $type)
    {
        $channel = TwitterChannel::where('channel', 'twitter')->where('via', $type)->orderBy('posted_at', 'desc')
            ->first(['message_id']);

        return ($channel) ? $channel->message_id : 0;
    }
}
