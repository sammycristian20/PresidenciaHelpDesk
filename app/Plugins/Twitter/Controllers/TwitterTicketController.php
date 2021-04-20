<?php

namespace App\Plugins\Twitter\Controllers;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Manage\Tickettype;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterChannel;

class TwitterTicketController extends Controller
{
    /**
     * @var TicketController
     */
    private $ticketController;

    /**
     * @var array
     */
    private $entitiesToPersist = [];

    public function __construct()
    {
        $this->ticketController = new TicketController();
    }

    /**
     * Checks whether a tweet/message already exists as aticket
     * @param string $item
     * @param string $type
     * @return bool
     */
    private function entityExists(string $item, string $type)
    {
        return (bool) TwitterChannel::where(['via' => $type, 'message_id' => $item])->count();
    }

    /**
     * Creates ticket
     * @param $entities
     * @throws \Throwable
     */
    public function createTicket($entities)
    {
        usort($entities, function ($item1, $item2) {
            return $item1['posted_at'] <=> $item2['posted_at'];
        });

        foreach ($entities as $entity) {
            if (!$this->entityExists($entity['message_id'], $entity['via'])) {
                $ticketDetails = $this->getTicketDetailsIfThisEntityIsReplyToAnExistingTicket($entity);

                if ($ticketDetails) {
                    $replyDetails = $this->reply($entity['body'], $ticketDetails);
                    $this->persistEntityInDb($entity, $replyDetails->ticket_id);
                } else {
                    $newTicketDetails = $this->newTicket($entity);
                    $newTicket = $this->getTicketInfoByNewTicketDetails($newTicketDetails);
                    $this->persistEntityInDb($entity, $newTicket);
                }
            }
        }
    }

    /**
     * Get newly created ticket id
     * @param $newTicketDetails
     * @return mixed
     * @throws \Throwable
     */
    private function getTicketInfoByNewTicketDetails($newTicketDetails)
    {
        throw_if(!is_array($newTicketDetails), new \Exception(trans('Twitter::lang.twitter_new_ticket_creation_failed')));

        return Tickets::where('ticket_number', reset($newTicketDetails))->value('id');
    }

    /**
     * Persists Twitter Info In DB
     * @param $entity
     * @param $ticketId
     */
    private function persistEntityInDb($entity, $ticketId)
    {
        $modifiedEntity = array_diff_key($entity, array_flip(['parent_tweet_id','name','email']));

        $modifiedEntity['ticket_id'] = $ticketId;

        TwitterChannel::create($modifiedEntity);
    }

    /**
     * Checks whether the msg/tweet is a reply to the existing ticket
     * @param $entity
     * @return array
     */
    private function getTicketDetailsIfThisEntityIsReplyToAnExistingTicket($entity)
    {
        switch ($entity['via']) {
            case 'tweet':
                $ticketDetails = $this->checkReplyForTweets($entity);
                break;
            case 'message':
            case 'mention':
                $ticketDetails = $this->checkReplyForMessagesAndMentions($entity);
                break;
        }

        return (!empty($ticketDetails)) ? $ticketDetails : [];
    }

    /**
     * Check whether mention/dm is reply or not.
     * @param $entity
     * @return array
     */
    private function checkReplyForMessagesAndMentions($entity)
    {
        $existingTweet = TwitterChannel::where(
            ['user_id' => $entity['user_id'], 'via' => $entity['via'], 'hasExpired' => 0]
        )->first();

        if ($existingTweet) {
            return ($this->checkTimeConditionForReply($entity['posted_at'], $existingTweet->posted_at))
                ? ['ticket_id' => $existingTweet->ticket_id, 'user_id' => Tickets::where('id', $existingTweet->ticket_id)->value('user_id')]
                : [];
        }
    }

    /**
     * Checks the time constraint for reply
     * @param $entityPostedAt
     * @param $existingTweetPostedAt
     * @return bool
     */
    private function checkTimeConditionForReply($entityPostedAt, $existingTweetPostedAt)
    {
        $replyInterval = TwitterApp::value('reply_interval');
        return (strtotime($entityPostedAt) < strtotime("+$replyInterval day", strtotime($existingTweetPostedAt)));
    }

    /**
     * Check whether tweet is reply or not.
     * @param $entity
     * @return array
     */
    private function checkReplyForTweets($entity)
    {
        $hashtag = (!empty($entity['hashtag'])) ? $entity['hashtag'] : null;

        $parentTweet = (!empty($entity['parent_tweet_id'])) ? $entity['parent_tweet_id'] : null;

        if ($parentTweet) {
            $whereCondition = ['user_id' => $entity['user_id'], 'via' => $entity['via'], 'hasExpired' => 0, 'hashtag' => $hashtag, 'message_id' => $parentTweet];
        } else {
            $whereCondition = ['user_id' => $entity['user_id'], 'via' => $entity['via'], 'hasExpired' => 0, 'hashtag' => $hashtag];
        }

        $existingTweet = TwitterChannel::where($whereCondition)->first();

        if ($existingTweet) {
            return ($hashtag === $existingTweet->hashtag && $this->checkTimeConditionForReply($entity['posted_at'], $existingTweet->posted_at))
                ? ['ticket_id' => $existingTweet->ticket_id, 'user_id' => Tickets::where('id', $existingTweet->ticket_id)->value('user_id')]
                : [];
        }
    }

    /**
     * Add message/tweet as a reply to the existing ticket.
     * @param $body
     * @param $ticketDetails
     * @return mixed
     */
    private function reply($body, $ticketDetails)
    {
        return $this->ticketController->saveReply(
            $ticketDetails['ticket_id'],
            $body,
            $ticketDetails['user_id'],
            "",
            [],
            [],
            false,
            'client'
        );
    }

    /**
     * creates a new ticket from twitter
     * @param $entity
     * @return array
     */
    private function newTicket($entity)
    {
        return  $this->ticketController->create_user(
            $entity['username'],
            $entity['username'],
            ($entity['via'] === 'tweet') ? "Twitter ". ucfirst($entity['via']). " In #".$entity['hashtag'] :ucfirst($entity['via'])." from Twitter",
            $entity['body'],
            "",
            "",
            "",
            Ticket::value('help_topic') ?: '',
            '',
            '',
            $this->ticketController->getSourceByname("twitter")->id,
            [],
            System::value('department'),
            null,
            [],
            "",
            "",
            Tickettype::value('id') ?: ''
        );
    }
}
