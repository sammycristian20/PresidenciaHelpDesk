<?php

namespace App\Http\Controllers\Common\Dependency;

use App\Exceptions\DependencyNotFoundException;
use Illuminate\Http\Request;

/**
 * handles dependency details related operation based on keys
 * for eg. if key is status-id or status_id or status-ids and value is 1,
 * it is going to give the details of status whose id is one
 */

class DependencyDetails extends DependencyController
{
    /**
     * key of the dependency
     * @var string
     */
    private $key;

    /**
     * Value of the dependency. can be id, username, email depending on the dependency
     * @var string|array|int
     */
    private $value;

    /**
     * By default this class gives output as array/collection but if needed only single output, this variable can be set as true (for eg. in workflow/listener)
     * NOTE: currently not being used anywhere but will be used to refactor workflow/listener code
     * @var bool
     */
    private $outputAsObject = false;

    /**
     * Sets key after formatting and validating
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $this->getFormattedKey($key);
    }

    public function setOutputAsObject($value = true)
    {
        $this->outputAsObject = $value;
    }

    /**
     * Sets value after formatting and validating
     * @param string|array|int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Gets dependency details
     * TODO: make it statically available
     * @param  string  $key
     * @param  string|array|int  $value  ids or values of the dependencies
     * @param  boolean $meta
     * @param  boolean $config
     * @return array
     * @throws DependencyNotFoundException
     */
    public function getDependencyDetails(string $key, $value, bool $meta = false, bool $config = false, array $options = [])
    {

        if(!$value){
            return [];
        }
        if(!is_array($value)){
            // converting value into array
            $value = [$value];
        }

        $this->key = $this->getFormattedKey($key);

        $request = new Request;

        $parameters = array_merge(['meta' => $meta, 'limit' => 'all', 'config'=> $config, 'ids' => $value], $options);

        $request->replace($parameters);

        $this->initializeParameterValues($request);

        // if value is array, it should send result as array else object
        $dependencies = $this->handleDependencies($this->key);

        // if value is an array, it should return output as array, else object
        // flatten the array and make 2nd layer visible
        return $this->getFlattenedDependencyArray($dependencies);
    }

    /**
     * This method takes dependencies and modify it into plain array of dependencies
     * NOTE: this method can later be modified to make it return object based on parameters
     * @param array $dependencies
     * @param bool $isValueAnArray
     * @return array
     */
    private function getFlattenedDependencyArray(array $dependencies)
    {

        if($this->outputAsObject){
            return isset(array_values($dependencies)[0][0]) ? array_values($dependencies)[0][0] : null;
        } else {
            return array_values($dependencies)[0];
        }
    }

    /**
     * gets key in a formatted way in which DependencyController accepts
     * @param  string $key
     * @return string
     */
    private function getFormattedKey(string $key)
    {
        switch ($key) {
            case in_array($key, ["helptopic-ids", "help_topic_id"]) : return "help-topics";

            case in_array($key, ["dept-ids", "dept_id", "department_id"]) : return "departments";

            case in_array($key, ["priority-ids", "priority_id"]) : return "priorities";

            case in_array($key, ["owner-ids", "owner_id", "user_id", "assignee-ids", "assigned_id", "creator-ids", "creator_id", "collaborator-ids", "cc_ids", "cc", "requester"]) : return "users";

            case in_array($key, ["sla-plan-ids", "sla"]) : return "sla-plans";

            case in_array($key, ["team-ids", "team_id"]) : return "teams";

            case in_array($key, ["status-ids", "status_id"]) : return "statuses";

            case in_array($key, ["type-ids", "type_id"]) : return "types";

            case in_array($key, ["source-ids", "source_id"]) : return "sources";

            case in_array($key, ["tag-ids", "tag_ids"]) : return "tags";

            case in_array($key, ["label-ids", "label_ids"]) : return "labels";

            case in_array($key, ["location-ids", "location_id"]) : return "locations";

            case in_array($key, ["ticket-ids"]) : return "tickets";

            case in_array($key, ["organization-ids", "organisation", "organization"]) : return "organizations";

            case in_array($key, ["approval_workflow_id"]) : return "approval-workflows";

            default: return $key;
        }
    }
}
