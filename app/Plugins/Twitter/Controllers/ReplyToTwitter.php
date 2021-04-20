<?php

namespace App\Plugins\Twitter\Controllers;

use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Traits\CommonTwitter;
use App\Plugins\Twitter\Model\TwitterSystemUser;

class ReplyToTwitter
{
    use CommonTwitter;

    /**
     * Returns the ticket id to which  a tweet/message belongs to if it already exists
     * @param $ticketId
     * @param $searchString
     * @return TwitterChannel
     */
    private function getDetailsForReplying($ticketId, $searchString)
    {
        $replayDetails =  ($searchString) ? TwitterChannel::where([
          'ticket_id' => $ticketId,
          'body'  => trim($searchString)
        ]): TwitterChannel::where('ticket_id', $ticketId);

        return $replayDetails->first(['user_id','message_id','username','via']);
    }
    
    /**
     * Replies to ticket raised from twitter, on twitter
     * @param array $data
     * @return void
     */
    public function replyTwitter($data)
    {
        $ticketId = $data['ticket_id'];

        if ($this->isTicketSourceTwitter($ticketId)) {
            $credentials = $this->getCredentials(false);

            if ($credentials) {
                $credentialsInRequiredFormat = [
                    'access_token' => $credentials['access_token'],
                    'access_secret' => $credentials['access_token_secret'],
                    'consumer_key' => $credentials['consumer_api_key'],
                    'consumer_secret' => $credentials['consumer_api_secret']
                ];

                $this->twitterInit($credentialsInRequiredFormat);

                $replyBody = strip_tags(str_replace("&nbsp;", "", $data['body']));

                $searchStringPosition = strrpos($replyBody, 'wrote :');

                $searchString = ($searchStringPosition) ? ltrim(substr($replyBody, $searchStringPosition), "wrote :") : '';

                $detailsForReplying = $this->getDetailsForReplying($ticketId, $searchString);

                if ($detailsForReplying) {
                    $usernameRequiredByTwitter = "@".$detailsForReplying->username;

                    (in_array($detailsForReplying->via, ['tweet','mention','reply']))
                        ? $this->replyTweet($detailsForReplying->message_id, "$usernameRequiredByTwitter $replyBody", $ticketId, $detailsForReplying->hashtag)
                        : $this->replyMessage($detailsForReplying->user_id, $replyBody);
                } else {
                    throw new \Exception(trans("Twitter::lang.twitter_cannot_reply"));
                }
            } else {
                throw new \Exception(trans("Twitter::lang.twitter_cannot_reply_no_credentials"));
            }

        }
    }

    /**
     * Checks whether ticket source is twitter
     * @param mixed $ticketId
     * @return boolean
     */
    private function isTicketSourceTwitter($ticketId)
    {
        $isSourceTwitter = false;
        $ticketSource = Tickets::where('id', $ticketId)->value('source');
        if ($ticketSource) {
            $isSourceTwitter = Ticket_source::where('id', $ticketSource)->value('name') == "Twitter";
        }
        return $isSourceTwitter;
    }

    /**
     * Replies to twitter tweet
     * @param mixed $messageId
     * @param mixed $content
     * @param $ticketId
     * @param $hashtag
     * @return void
     */
    private function replyTweet($messageId, $content, $ticketId, $hashtag)
    {
        $postData = [
            'status' => str_replace("On", "\r\nOn", $content),
            'in_reply_to_status_id' => $messageId,
        ];

        $result = $this->twitter->post("statuses/update", $postData);

        $this->saveTweetsInDB($result, $ticketId, $hashtag);
    }

    private function saveTweetsInDB($tweet, $ticketId, $hashtag)
    {
        $userDetails = reset($tweet->entities->user_mentions);

        $dataToPersist = [
            'channel' => 'twitter',
            'name' => $userDetails->name,
            'user_id' => $userDetails->id_str,
            'username' => $userDetails->screen_name,
            'posted_at' => $this->formatTimezone($tweet->created_at),
            'message_id' => $tweet->id_str,
            'via' => 'tweet',
            'system_twitter_user' => TwitterSystemUser::value('user_id'),
            'body' => $tweet->text,
            'ticket_id' => $ticketId,
            'hashtag' => $hashtag
        ];

        TwitterChannel::create($dataToPersist);
    }

    /**
     * Replies to twitter message
     * @param mixed $userId
     * @param mixed $content
     * @return void
     */
    private function replyMessage($userId, $content)
    {
        $data = [
            'event' => [
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $userId
                    ],
                    'message_data' => [
                        'text' => str_replace("On", "\r\nOn", $content)
                    ]
                ]
            ]
        ];

        $this->twitter->post('direct_messages/events/new', $data, true);
    }
}
