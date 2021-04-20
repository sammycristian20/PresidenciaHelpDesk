<?php

namespace App\Http\Controllers\SLA;

use Carbon\Carbon;
use App\Model\helpdesk\Manage\Sla\BusinessHours as BusinessHour;
use Exception;
use Throwable;
use Logger;

// It will convert passed date into BH timezone and do the calculation and convert back to UTC
// REASON : converting business hour to UTC might span multiple days which will make calculations much more difficult
class BusinessHourCalculation
{

    const WEEK_ARRAY = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    /**
     * Enforced business hour
     * @var BusinessHour
     */
    private $businessHour;

    /**
     * Start time of business hour for the day
     * @var Carbon
     */
    private $startTimeOfBusinessHour;

    /**
     * End time of business hour for the day
     * @var Carbon
     */
    private $endTimeOfBusinessHour;


    public function __construct(BusinessHour $businessHour)
    {
        $this->businessHour = $businessHour;
    }

    /**
     * Calculates due-date based on start time
     * @param Carbon $startTime
     * @param int $timeDiffInSeconds
     * @return Carbon
     */
    public function getDueDate(Carbon $startTime, int $timeDiffInSeconds)
    {
        try{
            // timezone handling(converting ticket time to businessHour Timezone and converting back to UTC one done) will be handled in this method
            $startTime = $startTime->timezone($this->businessHour->timezone);

            // getting dueDate in Business Hour timezone
            $newDueDate = $this->getCarbonTimeByDiffInBusinessHour($startTime, $timeDiffInSeconds);

            // convert this dueDate back to UTC and return
            return $newDueDate->timezone('UTC');
        }catch (Throwable $e){
            Logger::exception($e);
        }
    }

    /**
     * Gives time difference in business hours
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return int time difference in minutes
     */
    public function getTimeDiffInBH(Carbon $startTime, Carbon $endTime)
    {
        try{
            $startTime = $startTime->timezone($this->businessHour->timezone);

            $endTime = $endTime->timezone($this->businessHour->timezone);

            // converting seconds into minutes
            return round($this->getTimeDifferenceInBusinessHour($startTime, $endTime)/60);

        } catch (Throwable $e){
            Logger::exception($e);
        }
    }

    /**
     * Gets time equivalent to startTime + timeDiff in business-hour
     * @param Carbon $initialTime
     * @param int $timeDiff
     * @param int $iterationCount
     * @return Carbon
     * @throws Throwable
     */
    private function getCarbonTimeByDiffInBusinessHour(Carbon $initialTime, int $timeDiff) : Carbon
    {
        $iterationCount = 0;

        while($iterationCount++ < 3000){

            // getting next working time based on initial time
            $initialTime = $this->getNextWorkingTime($initialTime);

            // if timeDiff is smaller than the remaining day's time, it should return initialTime + timeDiff
            if($this->endTimeOfBusinessHour->diffInSeconds($initialTime) > $timeDiff) {
                return $initialTime->copy()->addSeconds($timeDiff);
            }

            // if timeDiff is more than remaining time of the day,
            $timeDiff = $timeDiff - $this->endTimeOfBusinessHour->diffInSeconds($initialTime);

            $initialTime = $initialTime->copy()->addDay(1)->startOfDay();
        }

        throw  new Exception("Infinite Loop Encountered with business hour id ". $this->businessHour->id);
    }

    /*
            Logic for finding the difference b/w two times

            STBH - Start Time of Business Hour
            ETBH - End Time of Business Hour
            IT - Initial time
            FT - Final Time

                    =====================STBH===========================ETBH====================

            CASE 1: IT----------FT
            CASE 2: IT-----------------------------------FT
            CASE 3: IT--------------------------------------------------------------------FT
            CASE 4:                             IT------------FT
            CASE 5:                             IT----------------------------------------FT
            CASE 6:                                                              IT-------FT


            CASE 1: when both IT and FT is lessThanSTBH or in simple sense FT will be lessThanSTBH (take no action)

            CASE 2: when IT is lessThanSTBH and FT is betweenSTBHAndETBH (FT - STBH)

            CASE 3: when IT is lessThanSTBH and FT is moreThanETBH (ETBH - STBH)

            CASE 4: when IT is betweenSTBHAndETBH and FT is betweenSTBHAndETBH (FT - IT)

            CASE 5: when IT is betweenSTBHAndETBH and FT is moreThanETBH (ETBH - IT)

            CASE 6: when IT is moreThanETBH and FT is moreThanETBH or in simple sense, IT is moreThanETBH (no action)

            methods required : lessThanSTBH, moreThanETBH, betweenSTBHAndETBH
    */

    /**
     * gets time difference in business hour
     * @param Carbon $startTime
     * @param Carbon $finalTime
     * @param int $timeDiff
     * @param int $iterationCount
     * @return int    time difference in seconds
     * @throws Throwable
     */
    private function getTimeDifferenceInBusinessHour(Carbon $startTime, Carbon $finalTime, $timeDiff = 0) : int
    {

        // LOGIC EXPLANATION :  startTime can be smaller than next working day or more. When Less, we have to make start time as
        // next working day, since time calculation should begin in working hours. When more, it should not change startTime, since
        // start time is already in working hours
        $iterationCount = 0;

        while($iterationCount++ < 3000){

            $nextWorkingDay = $this->getNextWorkingDay($startTime);

            $this->setBusinessHourBoundary($nextWorkingDay);

            if($nextWorkingDay->gt($startTime)){
                $startTime = $nextWorkingDay;
            }

            $timeDiff += $this->calculateTimeDifferenceInBusinessHour($startTime, $finalTime);

            if($finalTime->lte($this->startTimeOfBusinessHour)) {
                return $timeDiff;
            }

            $startTime = $startTime->copy()->addDay(1)->startOfDay();
        }

        throw new Exception("Infinite Loop Encountered with business hour id ". $this->businessHour->id);
    }

    /**
     * Sets business hour initial and final time, under which calculation has to take place.
     * NOTE : If given time is greater than start of business hour of the day, it will make STBH as given time
     * @param Carbon $startTime
     * @return void
     * @throws Throwable
     */
    private function setBusinessHourBoundary(Carbon $startTime)
    {
        $dayOfWeek = self::WEEK_ARRAY[$startTime->dayOfWeek];

        // query only for non-closed days
        $businessHourSchedule = $this->businessHour->schedule->where('days', $dayOfWeek)->first();
        // if business hour not found with that day
        if(!$businessHourSchedule){
            throw new Exception("Business hour for $dayOfWeek is not found for business hour id ".$this->businessHour->id);
        }


        // This method will never received 'Closed' status, since that part will be taken care in getNextWorkingDay
        if($businessHourSchedule->status == 'Open_fixed') {
            // start and end time will be SOD and EOD
            // startTime will always be greater than SOD
            $this->startTimeOfBusinessHour = $startTime->copy()->startOfDay();

            $this->endTimeOfBusinessHour = $startTime->copy()->endOfDay();

        } else {
            // get start and end time
            $this->startTimeOfBusinessHour = $this->getCarbonTimeByTimeString($startTime, $businessHourSchedule->custom->open_time);

            $this->endTimeOfBusinessHour = $this->getCarbonTimeByTimeString($startTime, $businessHourSchedule->custom->close_time);
        }
    }


    /**
     * Gets next working day's start of date
     * @param Carbon $startTime
     * @param integer $iterationCount
     * @return Carbon
     * @throws Throwable
     */
    private function getNextWorkingDay($startTime, $iterationCount = 0)
    {
        // to terminate the loop if it is infinite
        throw_if($iterationCount > 365, new Exception("Infinite Loop Encountered with business hour id ". $this->businessHour->id));

        // if closed or holiday, it should mark BH boundary as null
        // check if there is a holiday that day, increment to next day
        if($this->isHoliday($startTime)){
            // increment by one day and check again for holiday
            // increment initialTime by one day
            $startTime = $startTime->copy()->addDay(1);

            return $this->getNextWorkingDay($startTime, ++$iterationCount);
        }

        return $startTime->copy()->startOfDay();
    }

    /**
     * Checks if holiday is there in asked day
     * @param Carbon $date unix timestamp of the date
     * @return boolean
     */
    private function isHoliday(Carbon $date) : bool
    {
        // check for today's date in DB
        // convert time into m-d-Y format and check if it exists

        $formattedDate = $date->copy()->format('m-d-Y');
       

        $isHoliday = $this->businessHour->holiday->where('date', $formattedDate)->count();

        $weekDay = self::WEEK_ARRAY[$date->copy()->dayOfWeek];

        // writing Open_fixed and Open_custom, so that days where BH is not present in the DB will be considered as holiday
        $isWeekOffDay = !$this->businessHour->schedule->where('days', $weekDay)
            ->whereIn('status', ['Open_fixed', 'Open_custom'])
            ->count();

        return $isHoliday || $isWeekOffDay;
    }

    /**
     * Calculates time difference between intial and final time in milliseconds in business hours
     * @param Carbon $startTime
     * @param Carbon $finalTime
     * @return int
     */
    private function calculateTimeDifferenceInBusinessHour(Carbon $startTime, Carbon $finalTime)
    {
        $time = 0;

        if($this->lessThanSTBH($startTime) && $this->betweenSTBHAndETBH($finalTime)){
            $time += $finalTime->diffInSeconds($this->startTimeOfBusinessHour);
        }

        if($this->lessThanSTBH($startTime) && $this->moreThanETBH($finalTime)){
            $time += $this->endTimeOfBusinessHour->diffInSeconds($this->startTimeOfBusinessHour);
        }

        if($this->betweenSTBHAndETBH($startTime) && $this->betweenSTBHAndETBH($finalTime)){
            $time += $finalTime->diffInSeconds($startTime);
        }

        if($this->betweenSTBHAndETBH($startTime) && $this->moreThanETBH($finalTime)){
            $time += $this->endTimeOfBusinessHour->diffInSeconds($startTime);
        }
        return $time;
    }

    /**
     * check if given time is less than Start Time Business Hour
     * @param Carbon $time
     * @return Boolean
     */
    private function lessThanSTBH(Carbon $time)
    {
        return $time->lt($this->startTimeOfBusinessHour);
    }

    /**
     * check if given time is less than Start Time Business Hour
     * @param Carbon $time
     * @return Boolean
     */
    private function moreThanETBH(Carbon $time)
    {
        return $time->gt($this->endTimeOfBusinessHour);
    }

    /**
     * check if given time is greater than equal to Start Time Business Hour and
     * less than equal to End Time Business Hour
     * @param Carbon $time
     * @return Boolean
     */
    private function betweenSTBHAndETBH(Carbon $time)
    {
        return $time->gte($this->startTimeOfBusinessHour) && $time->lte($this->endTimeOfBusinessHour);
    }

    /**
     * @param Carbon $currentDate
     * @param string $timeString
     * @return Carbon
     * @throws Throwable
     */
    private function getCarbonTimeByTimeString(Carbon $currentDate, string $timeString) : Carbon
    {
        $timeArray = explode(":", $timeString);

        throw_if(count($timeArray) != 2, new \Exception('Invalid time format passed. Accepted value is hour:minute'));

        $hour = $timeArray[0];

        $minute = $timeArray[1];

        return $currentDate->copy()->hour($hour)->minute($minute)->second(0);
    }

    /**
     * Gets time is business hour. For eg. business hour is Monday 9 am - 5 pm and initial time passed is Sunday, it will give next working time
     *  i.e 9am monday. If passed time is Monday 5 am, it will give
     * NOTE: Business hour boundary get updated here too
     * @param Carbon $initialTime
     * @return Carbon
     * @throws Throwable
     */
    private function getNextWorkingTime(Carbon $initialTime) : Carbon
    {
        // setting business hour based on current time
        $this->setBusinessHourBoundary($this->getNextWorkingDay($initialTime));

        // Next working moment is STBH, so if initial time is less than STBH, we need make initial time as STBH
        if($this->lessThanSTBH($initialTime)){
            return $this->startTimeOfBusinessHour;
        }

        if($this->moreThanETBH($initialTime)){
            // now re-fetch next working day and setBusinessHourBoundary again
            // setting initial time as next working day startTimeOfBusinessHour
            $this->setBusinessHourBoundary($this->getNextWorkingDay($initialTime->copy()->addDay(1)->startOfDay()));
            return $this->startTimeOfBusinessHour;
        }

        // if it is between business hour
        return $initialTime;
    }
}
