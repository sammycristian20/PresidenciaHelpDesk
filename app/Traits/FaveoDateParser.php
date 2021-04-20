<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;

/**
 * Handler implementation of time format for filters
 * USAGE : set timezone first. Now, call `getTimeRangeObject` with `timeString`
 *        available `$timeString` formats are :
 *        - last::n~minute
 *        - last::n~hour
 *        - last::n~day
 *        - last::n~month
 *
 *        - next::n~minute
 *        - next::n~hour
 *        - next::n~day
 *        - next::n~month
 *
 *        - date::start_date~end_date
 */

trait FaveoDateParser
{

  /**
   * From which timezone conversion is happening
   * NOTE: We cannot define an abstract property, since this trait is date specific so it will make
   * sense to have its own timezone property. If we directly use parent class' $timezone property, every class
   * using this trait has to define that property. So, we are creating its own property for timezone
   * @var string
   */
    private $faveoTimezone;

    /**
     * Gets parsed date time string
     * @param string $timeString it will be in format "last::2~day" or "next::2~day" or "date::startDate~endDate"
     * @return object
     * @throws Exception
     */
    public function getTimeRangeObject(string $timeString = null, string $timezone = 'UTC') : object
    {
      // url decoding timeString since it contains special characters
        $timeString = urldecode($timeString);

        $this->faveoTimezone = $timezone;
    
        if (strpos($timeString, "last::") !== false) {
            return $this->getTimeObjectForPastTime($timeString);
        }

        if (strpos($timeString, "next::") !== false) {
            return $this->getTimeObjectForFutureTime($timeString);
        }

        if (strpos($timeString, "date::") !== false) {
            return $this->getTimeObjectForTimeRange($timeString);
        }

        throw new Exception('invalid parameter passed');
    }

    /**
     * Gets time object for a time range which belongs to past
     * @param string $timeString
     * @return Carbon
     * @throws Exception
     */
    private function getTimeObjectForPastTime(string $timeString) : object
    {
        $timeString = str_replace("last::", "", $timeString);

        $timeObject = $this->getTimeObject($timeString, true);

        $endTime = Carbon::now($this->faveoTimezone);

        $startTime = $this->getTimeFromTimeObject($endTime->copy(), $timeObject);

        return (object)["start" => $startTime->setTimezone('UTC'), "end" => $endTime->setTimezone('UTC')];
    }

    /**
     * Gets time object for a time range which belongs to future
     * @param string $timeString
     * @return Carbon
     * @throws Exception
     */
    private function getTimeObjectForFutureTime(string $timeString) : object
    {
        $timeString = str_replace("next::", "", $timeString);

        $timeObject = $this->getTimeObject($timeString);

        $startTime = Carbon::now($this->faveoTimezone);

        $endTime = $this->getTimeFromTimeObject($startTime->copy(), $timeObject);

        return (object)["start" => $startTime->setTimezone('UTC'), "end" => $endTime];
    }

  /**
   * Gets time object for a custom time range
   * @param  string $timeString
   * @return Carbon
   */
    private function getTimeObjectForTimeRange(string $timeString) : object
    {
        $timeString = str_replace("date::", "", $timeString);

        $timeString = explode("~", $timeString);

        $startTime = $this->parseDate($timeString[0]);

        $endTime = $this->parseDate($timeString[1]);

        return (object)["start" => $startTime, "end" => $endTime];
    }

    /**
     * Gets time from time string
     * @param Carbon $startTime
     * @param object $timeObject
     * @return Carbon
     * @throws Exception
     */
    private function getTimeFromTimeObject(Carbon $startTime, object $timeObject) : Carbon
    {
        switch ($timeObject->unit) {
            case 'minute':
                return $this->parseMinute($timeObject->quantity, $startTime);

            case 'hour':
                return $this->parseHour($timeObject->quantity, $startTime);

            case 'day':
                return $this->parseDay($timeObject->quantity, $startTime);

            case 'month':
                return $this->parseMonth($timeObject->quantity, $startTime);

            case 'year':
                return $this->parseYear($timeObject->quantity, $startTime);

            default:
                throw new Exception("Invalid unit in passed timeObject");
        }
    }

  /**
   * Gets time object from timeString
   * @param string $timeString should be in format quantity~unit
   * @return object
   */
    private function getTimeObject(string $timeString, bool $isPastDate = false)
    {
      // explode by tilde and extract quantity and unit
        $timeString = explode("~", $timeString);

      // minus one, so that `1` can be considered as the current minute, hour, day or month
        $quantity = $timeString[0] - 1;

      // for future time, we have to get SOD of next day, so +1. For past date, we need SOD of current day
        $quantity = $isPastDate ? -$quantity : $quantity + 1;

        $unit = $timeString[1];

        return (object)["quantity" => $quantity, "unit" => $unit];
    }

  /**
   * Converts within last into carbon intance of corresponding date-time in given timezone
   * @param  int $quantity can be +n or -n
   * @return Carbon
   */
    private function parseMinute(int $quantity, Carbon $dateTime) : Carbon
    {
        return $dateTime->modify("$quantity minute")->second(0)->setTimezone('UTC');
    }

  /**
   * Converts within last into carbon intance of corresponding date-time in given timezone
   * @param  int  $quantity
   * @return Carbon
   */
    private function parseHour(int $quantity, Carbon $dateTime)
    {
        return $dateTime->modify("$quantity hour")->minute(0)->second(0)->setTimezone('UTC');
    }

  /**
   * Converts within last into carbon intance of corresponding date-time in given timezone
   * @param  int  $quantity
   * @return Carbon
   */
    private function parseDay(int $quantity, Carbon $dateTime)
    {
        return $dateTime->modify("$quantity day")->hour(0)->minute(0)->second(0)->setTimezone('UTC');
    }

  /**
   * Converts within last into carbon intance of corresponding date-time in given timezone
   * @param  int  $quantity
   * @return Carbon
   */
    private function parseMonth(int $quantity, Carbon $dateTime)
    {
        return $dateTime->modify("$quantity month")->day(1)->hour(0)->minute(0)->second(0)->setTimezone('UTC');
    }

    /**
    * Parses date string and give equivalent date in given timezone
    * @param  string $dateString
    * @return Carbon
    */
    function parseDate(string $dateString)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $dateString, $this->faveoTimezone)->setTimezone('UTC');
    }

    /**
     * Gives time interval in minutes in format {start: start_time_in_minutes, end: end_time_in_minutes}
     * Accepted format: interval::n~time_unit~m~time_unit
     * @param string $formattedTime
     * @return object
     * @throws Exception
     */
    private function getTimeIntervalInMinutes(string $formattedTime)
    {
        if($this->isValidTimeInterval($formattedTime)){
            $formattedTime = str_replace("interval::", "", $formattedTime);
            $timeArray = explode('~', $formattedTime);
            $firstQuantity = $timeArray[0];
            $firstUnit = $timeArray[1];
            $secondQuantity = $timeArray[2];
            $secondUnit = $timeArray[3];
            return (object)["start"=>$firstQuantity*$this->convertToMinute($firstUnit), "end"=>$secondQuantity*$this->convertToMinute($secondUnit)];
        }
    }

    /**
     * Validates time interval format
     * @param string $formattedTime
     * @return bool
     */
    private function isValidTimeInterval(string $formattedTime): bool
    {
        if (strpos($formattedTime, "interval::") === false || count(explode('~', $formattedTime)) != 4) {
            throw new InvalidArgumentException("wrong format of time is passed. Accepted format is interval::n~time_unit~m~time_unit. Value provided is $formattedTime");
        }
        return true;
    }

    /**
     * Gets time in minutes
     * @param string $formattedTime in format "diff::n~minute", "diff::n~hour", "diff::n~day"
     * @return int
     * @throws \Exception
     */
    public function getTimeDiffInMinutes(string $formattedTime) : int
    {
        if ($this->isValidTimeDiff($formattedTime)) {
            // remove diff and explode by '~'
            $formattedTime = str_replace("diff::", "", $formattedTime);

            $formattedTime = explode('~', $formattedTime);

            $quantity = $formattedTime[0];

            $unit = $formattedTime[1];

            return $quantity * $this->convertToMinute($unit);
        }
    }

    /**
     * if time diff format is valid
     * @param string $formattedTime
     * @return bool
     */
    public function isValidTimeDiff(string $formattedTime) : bool
    {
        if (strpos($formattedTime, "diff::") === false || strpos($formattedTime, "~") === false) {
            throw new InvalidArgumentException("wrong format of time is passed. Accepted format is diff::n~time_unit. Value provided is $formattedTime");
        }
        return true;
    }

    /**
     * Converts unit to minute
     * @param string $unit
     * @return int
     * @throws Exception
     */
    private function convertToMinute(string $unit) : int
    {
        switch ($unit) {
            case 'minute':
                return 1;

            case 'hour':
                return 60;

            case 'day':
                return 1440;

            default:
                throw new InvalidArgumentException("Invalid time unit given. Supported units are minute, hour and day");
        }
    }
}
