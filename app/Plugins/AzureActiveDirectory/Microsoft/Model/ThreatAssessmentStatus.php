<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* ThreatAssessmentStatus File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace App\Plugins\AzureActiveDirectory\Microsoft\Model;

use App\Plugins\AzureActiveDirectory\Microsoft\Core\Enum;

/**
* ThreatAssessmentStatus class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class ThreatAssessmentStatus extends Enum
{
    /**
    * The Enum ThreatAssessmentStatus
    */
    const PENDING = "pending";
    const COMPLETED = "completed";
}