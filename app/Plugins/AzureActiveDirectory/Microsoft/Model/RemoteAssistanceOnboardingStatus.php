<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* RemoteAssistanceOnboardingStatus File
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
* RemoteAssistanceOnboardingStatus class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class RemoteAssistanceOnboardingStatus extends Enum
{
    /**
    * The Enum RemoteAssistanceOnboardingStatus
    */
    const NOT_ONBOARDED = "notOnboarded";
    const ONBOARDING = "onboarding";
    const ONBOARDED = "onboarded";
}