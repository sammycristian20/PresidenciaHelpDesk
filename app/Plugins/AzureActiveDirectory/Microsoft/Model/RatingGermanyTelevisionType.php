<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* RatingGermanyTelevisionType File
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
* RatingGermanyTelevisionType class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright © Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class RatingGermanyTelevisionType extends Enum
{
    /**
    * The Enum RatingGermanyTelevisionType
    */
    const ALL_ALLOWED = "allAllowed";
    const ALL_BLOCKED = "allBlocked";
    const GENERAL = "general";
    const AGES_ABOVE6 = "agesAbove6";
    const AGES_ABOVE12 = "agesAbove12";
    const AGES_ABOVE16 = "agesAbove16";
    const ADULTS = "adults";
}