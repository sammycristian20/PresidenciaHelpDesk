<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* SafeSearchFilterType File
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
* SafeSearchFilterType class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class SafeSearchFilterType extends Enum
{
    /**
    * The Enum SafeSearchFilterType
    */
    const USER_DEFINED = "userDefined";
    const STRICT = "strict";
    const MODERATE = "moderate";
}