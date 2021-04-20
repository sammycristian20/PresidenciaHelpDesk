<?php


namespace App\Traits;

use App\Model\helpdesk\Ticket\Tickets;
use Exception;
use Config;
use Auth;

trait ClickableTicketParam
{

    /**
     * If hyperlink is needed as an anchor tag.
     * If passes as true, result will be appended as <a href="link">Name</a>, else just link
     * @var bool
     */
    private $formattedHyperlink = true;

    /**
     * Handles non-user dependency hyperlink update
     * @param string $key key that will be given in inbox url for filtering
     * @param int|string $dependencyValue id|value of that dependency, which will be used to filter tickets for that dependencyId
     * @param Tickets $ticket
     * @param string $relationName
     * @param bool $isArray if dependency passed is an array
     * @param string $hyperLinkKey
     * @return void
     * @throws Exception
     */
    protected function updateDependencyHyperlink(string $key, $dependencyValue, Tickets &$ticket, string $relationName = '', $isArray = true, string $hyperLinkKey = 'name') : void
    {
        if (!method_exists($ticket, $relationName) && !isset($ticket->$relationName)) {
            throw new Exception('invalid relationName passed ');
        }

        if (isset($ticket->$relationName->name)) {
            // if its not an array in filter, we don't need to pass
            $key = $isArray ? $key.'[]' : $key;

            $ticketListUrl = $this->getInboxUrl().'&'.$key.'='.$dependencyValue;

            $ticket->$relationName->$hyperLinkKey = $this->formattedHyperlink ? $this->getHyperlink($ticketListUrl, $ticket->$relationName->name) : $ticketListUrl;
        }
    }

    /**
     * Handles user hyperlink update
     * @param string $key
     * @param int $dependencyValue id of that dependency, which will be used to filter tickets for that dependencyId
     * @param Tickets $ticket
     * @param string $relationName
     * @param string $hyperLinkKey
     * @return void
     * @throws Exception
     */
    protected function updateUserHyperlink(string $key, $dependencyValue, Tickets &$ticket, string $relationName = '', string $hyperLinkKey = 'name') : void
    {
        if (!method_exists($ticket, $relationName)) {
            throw new Exception('invalid relationName passed');
        }

        if (isset($ticket->$relationName->full_name)) {
            $ticketListUrl = $this->getInboxUrl().'&'.$key.'[]='.$dependencyValue;

            $ticket->$relationName->$hyperLinkKey = $this->formattedHyperlink ? $this->getHyperlink($ticketListUrl, $ticket->$relationName->full_name) : $ticketListUrl;
        }
    }

    /**
     * Makes ticket hyperlink
     * @param string $property
     * @param Tickets $ticket
     * @param string $text
     * @return void
     */
    protected function ticketHyperLink(string $property, Tickets &$ticket, string $text = null) : void
    {
        // if not logged in or user, should redirect to ticket conversation of client panel
        $ticketUrl = Config::get('app.url').'/ticket-conversation-guest/'.$ticket->encrypted_id;

        if (Auth::check() && Auth::user()->role != 'user') {
            $ticketUrl = Config::get('app.url').'/thread/'.$ticket->id;
        }

        $ticket->$property = $this->formattedHyperlink ? $this->getHyperlink($ticketUrl, $text): $ticketUrl;
    }

    /**
     * Updates Array hyperlinks. for eg. tags, labels, organisation
     * NOTE: relation must be given in an array format with each object having keys `id` and `name`
     *
     * @internal there are 2 scenarios:
     *
     *      1. when we want to see all values as comma separated with hyperlink
     *          $this->formattedHyperlink will be true in this case
     *
     *      2. when we want all values as array but each value has individual hyperlink
     *          $this->formattedHyperlink will be false in this case
     *
     * Currently it used for building labels and tags links
     *
     * @param string $key
     * @param Tickets $ticket
     * @param string $relationName
     * @param string $hyperLinkKey
     * @return void
     */
    protected function updateArrayHyperlink(string $key, Tickets &$ticket, string $relationName, string $hyperLinkKey = 'name')
    {
        $hyperLinkedElements = "";

        $formattedLabels = "";

        if ($ticket->$relationName) {
            $elementLinks = $this->getInboxUrl();

            // in case where formattedHyperlink is false, we need each element as separate with separate hyperlink
            foreach ($ticket->$relationName as $index => $element) {
                // appending hyperlink to each object in the array
                if(!$this->formattedHyperlink){

                   $element->$hyperLinkKey = $this->getInboxUrl() . "&$key".'[]='. $element->id;
                   continue;
                }

                $formattedLabels = $formattedLabels ? $formattedLabels.', ' .$element->name : $element->name;

                $elementLinks = $elementLinks . '&'.$key.'['.$index.']=' .$element->id;
            }

            if(!$this->formattedHyperlink){
                return;
            }

            $hyperLinkedElements = $this->getHyperlink($elementLinks, $formattedLabels);
        }
        unset($ticket->$relationName);

        $ticket->$relationName = $hyperLinkedElements;
    }

    /**
     * Gets inbox base url
     * @param string $category
     * @return string
     */
    protected function getInboxUrl($category = "all") : string
    {
        return Config::get('app.url')."/tickets?show%5B%5D=inbox&departments%5B%5D=All&filter-by-url=1&category=$category";
    }

    /**
     * Gets hyperlink
     * @param $redirectLink
     * @param $textToDisplay
     * @param string|null $class
     * @return string
     */
    protected function getHyperlink($redirectLink, $textToDisplay, string $class = null) : string
    {
        return $class ? "<a class='$class' href=$redirectLink>$textToDisplay</a>"
            : "<a href=$redirectLink>$textToDisplay</a>";
    }
}