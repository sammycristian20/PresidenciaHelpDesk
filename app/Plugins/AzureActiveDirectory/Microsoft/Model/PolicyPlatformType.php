<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* PolicyPlatformType File
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
* PolicyPlatformType class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class PolicyPlatformType extends Enum
{
    /**
    * The Enum PolicyPlatformType
    */
    const ANDROID = "android";
    const I_OS = "iOS";
    const MAC_OS = "macOS";
    const WINDOWS_PHONE81 = "windowsPhone81";
    const WINDOWS81_AND_LATER = "windows81AndLater";
    const WINDOWS10_AND_LATER = "windows10AndLater";
    const ANDROID_WORK_PROFILE = "androidWorkProfile";
    const ALL = "all";
}