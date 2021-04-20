<?php

namespace App\Plugins\Twitter\Handler;

use App\Plugins\Twitter\Controllers\Twitter;
use App\Plugins\Twitter\Controllers\TwitterConversations;
use App\Plugins\Twitter\Controllers\TwitterTicketController;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Model\TwitterHashtags;
use App\Plugins\Twitter\Model\TwitterSystemUser;
use App\Plugins\Twitter\Traits\CommonTwitter;
use Illuminate\Support\Str;

class TwitterAPIHandler
{
    use CommonTwitter;

    private $entityFields = [];

    public function __construct()
    {
        $credentials = $this->getCredentials(false);

        $credentialsInRequiredFormat = [
            'access_token' => $credentials['access_token'],
            'access_secret' => $credentials['access_token_secret'],
            'consumer_key' => $credentials['consumer_api_key'],
            'consumer_secret' => $credentials['consumer_api_secret']
        ];

        $this->twitterInit($credentialsInRequiredFormat);
    }

    /**
     * Fetches data from twitter api.
     * Console command entrypoint
     * @throws \Throwable
     */
    public function fetchDataFromTwitter()
    {
        $this->getTweets()->getTweetReplies()->getMessages()->getMentionTweets()->createTickets();
    }

    /**
     * Creates tickets from twitter
     * @throws \Throwable
     */
    private function createTickets()
    {
        (new TwitterTicketController())->createTicket($this->entityFields);
    }

    /**
     * Gets the hastags which are being tracked in the system
     * @return mixed
     */
    private function getHashTagArray()
    {
        return TwitterHashtags::where('app_id', TwitterApp::value('id'))
            ->get()
            ->transform(function ($element) {
                return  (Str::startsWith($element->hashtag, '#')) ? substr($element->hashtag, 1) : $element->hashtag;
            })->toArray();
    }

    /**
     * Tracks the replies to twitter hashtags
     * @return $this
     * @throws \Exception
     */
    private function getTweetReplies()
    {
        $hashtags = $this->getHashTagArray();

        $tweets = TwitterChannel::whereIn('hashtag', $hashtags)
            ->where(['via' => 'tweet', 'system_twitter_user' => TwitterSystemUser::value('user_id')])
            ->get(['message_id'])->toArray();

        foreach (array_column($tweets, 'message_id') as $tweetId) {
            $replies = (new TwitterConversations())->fetchConversion($tweetId, $this->twitter, CONVERSATE_AFTER);
            $this->formatTweetReplies(array_column($replies['tweets'], 'tweet'));
        }

        return $this;
    }

    /**
     * Gets the body of the parent tweet to which the reply belongs
     * @param $tweetId
     * @return mixed
     */
    private function getParentTweetDetails($tweetId)
    {
        return TwitterChannel::where('message_id', $tweetId)->first(['body','message_id','hashtag']);
    }

    /**
     * Formats the tweet replies according to the DB structure for persisting.
     * @param $tweets
     * @throws \Exception
     */
    private function formatTweetReplies($tweets)
    {
        foreach ($tweets as $tweet) {
            if (property_exists($tweet, "errors")) {
                $errorDetails = reset($tweet->errors);
                throw new \Exception($errorDetails->message);
            }

            if ($tweet->user->id_str === TwitterSystemUser::value('user_id')) {
                continue;
            }
            $parentTweet = $this->getParentTweetDetails($tweet->in_reply_to_status_id_str);

            $this->entityFields[] = $this->returnEntityFieldForTweets($this->convertNestedObjectToArray($tweet), "reply", $parentTweet);
        }
    }

    /**
     * Fetches tickets from twitter tweets.
     * @throws \Exception
     */
    private function getTweets()
    {
        $sinceId = $this->getSinceIdForTwitter('tweet');

        $hashTagArray = array_column(
            TwitterHashtags::where('app_id', $this->getCredentials(false)['id'])->get(['hashtag'])->toArray(),
            'hashtag'
        );

        if ($hashTagArray) {
            $hashtags = implode(
                '+OR+',
                array_column(
                    TwitterHashtags::where('app_id', $this->getCredentials(false)['id'])->get(['hashtag'])->toArray(),
                    'hashtag'
                )
            );

            $tweets = $this->twitter->get('search/tweets', ['q' => $hashtags, 'since_id' => $sinceId]);

            $tweetsArray = $this->convertNestedObjectToArray($tweets);

            if (!empty($tweetsArray['statuses'])) {
                $this->formatTweets($tweetsArray['statuses'], $hashTagArray);
            }

            return $this;
        }
    }

    /**
     * Checks whether tweet can be skipped for ticket creation
     * @param $tweet
     * @param $hashTags
     * @return bool
     */
    private function isThisTweetEntitySkipAble($tweet, $hashTags)
    {
        $systemUser = TwitterSystemUser::value('user_id');

        $skipAble = false;

        //in database hashtag stored with preceding `#` symbol to make calls to twitter API easier; so adding `#` here
        $incomingTweetHashTag = "#".reset($tweet['entities']['hashtags'])['text'];

        if (empty($tweet['entities']['hashtags']) || (!in_array($incomingTweetHashTag, $hashTags))) {
            //ignoring all the tweets coming from hashtags that are not tracked in the system
            $skipAble = true;
        } elseif (isset($tweet['in_reply_to_user_id_str']) && $systemUser === $tweet['in_reply_to_user_id_str']) {
            //ignoring reply posted to twitter by system
            $skipAble = true;
        } elseif ($tweet['user']['id_str'] === $systemUser) {
            //ignoring tweet coming from the twitter account added to the system
            $skipAble = true;
        }

        return $skipAble;
    }

    /**
     * Single method to format tweets(reply,mention,tweet) in required format
     * @param $tweet
     * @param $tweetType
     * @param null $parentTweet
     * @return array
     * @throws \Exception
     */
    private function returnEntityFieldForTweets($tweet, $tweetType, $parentTweet = null)
    {
        $entityField = [
            'channel' => 'twitter',
            'name' => $tweet['user']['name'],
            'user_id' => $tweet['user']['id'],
            'username' => $tweet['user']['screen_name'],
            'posted_at' => $this->formatTimezone($tweet['created_at']),
            'message_id' => $tweet['id'],
            'via' => ($tweetType === 'mention') ? $tweetType : 'tweet',
            'system_twitter_user' => TwitterSystemUser::value('user_id'),
            'body' => trim(preg_replace("/([@]+[\w_-]+)/", '', $tweet['text']))
        ];

        $entityField['hashtag'] = (!empty($tweet['entities']['hashtags'])) ? reset($tweet['entities']['hashtags'])['text'] : null;

        if ($parentTweet) {
            $entityField['hashtag'] = $parentTweet->hashtag;
            $entityField['parent_tweet_id'] = $parentTweet->message_id;
        }

        return $entityField;
    }

    /**
     * Formats the tweet to savable format
     * @param $tweets
     * @param $hashTags
     * @throws \Exception
     */
    private function formatTweets($tweets, $hashTags)
    {
        foreach ($tweets as $tweet) {
            if (!empty($tweet['errors'])) {
                $errorDetails = reset($tweet['errors']);
                throw new \Exception($errorDetails['message']);
            }

            if ($this->isThisTweetEntitySkipAble($tweet, $hashTags)) {
                continue;
            }

            $this->entityFields[] = $this->returnEntityFieldForTweets($tweet, "tweet");
        }
    }

    /**
     * Fetches tickets from twitter direct messages(dm)
     * @throws \Exception
     */
    private function getMessages()
    {
        $sinceId = $this->getSinceIdForTwitter('message');

        $messages = $this->twitter->get('direct_messages/events/list', ['since_id' => $sinceId]);

        $messagesArray = $this->convertNestedObjectToArray($messages);

        if (isset($messagesArray['events']) && $messagesArray['events']) {
            $this->formatMessages($messagesArray['events']);
        }

        return $this;
    }

    /**
     * Gets the user information from twitter to save the user in DB
     * @param $userId
     * @return mixed
     */
    private function getUser($userId)
    {
        $user = $this->twitter->get('users/lookup', ["user_id" => $userId]);

        return reset($user);
    }

    /**
     * Formats the messages to savable format
     * @param $messages
     * @return void
     * @throws \Exception
     */
    private function formatMessages($messages)
    {
        foreach ($messages as $message) {
            if (!empty($message['errors'])) {
                $errorDetails = reset($message['errors']);
                throw new \Exception($errorDetails['message']);
            }

            if (isset($message['message_create']['source_app_id'])) {
                //skips fetching the reply made by us as tickets.
                continue;
            }

            $userInfo = $this->getUser($message['message_create']['sender_id']);

            $this->entityFields[] = [
                "channel" => "twitter",
                "name" => $userInfo->name,
                "user_id" => $message['message_create']['sender_id'],
                "username" => $userInfo->screen_name,
                "email" => '-',
                "body" => trim($message['message_create']['message_data']['text']),
                //twitter returns the message created time as epoch timestamp that is converted to Unix timestamp as below.
                "posted_at" => $this->formatTimezone(date("Y-m-d h:i:s", substr($message['created_timestamp'], 0, 10))),
                "message_id" => $message['id'],
                "via" => "message",
                'system_twitter_user' => TwitterSystemUser::value('user_id')
            ];
        }
    }

    /**
     * Formats the mention tweets
     * @param $tweets
     * @throws \Exception
     */
    private function formatMentions($tweets)
    {
        foreach ($tweets as $tweet) {
            if (!empty($tweet['errors'])) {
                $errorDetails = reset($tweet['errors']);
                throw new \Exception($errorDetails['message']);
            }

            if (!empty($tweet['in_reply_to_status_id_str'])) {
                continue;
            }

            $this->entityFields[] = $this->returnEntityFieldForTweets($tweet, "mention");
        }
    }

    /**
     * Gets the mention tweets
     * @return $this
     * @throws \Exception
     */
    private function getMentionTweets()
    {
        $sinceId = $this->getSinceIdForTwitter('mention');

        $tweets = $this->twitter->get('statuses/mentions_timeline', ($sinceId) ? ['since_id' => $sinceId] : []);

        $tweetsArray = $this->convertNestedObjectToArray($tweets);

        $this->formatMentions($tweetsArray);

        return $this;
    }
}

