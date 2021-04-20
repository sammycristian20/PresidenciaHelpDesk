<?php

namespace App\FaveoReport\Tests\Backend\Controllers;

use Tests\DBTestCase;
use App\Model\helpdesk\Ticket\Tickets;
use App\FaveoReport\Controllers\BaseReportController;
use Config;
use Exception;
use App\FaveoReport\Exceptions\VariableNotFoundException;

class BaseReportControllerTest extends DBTestCase
{
    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedWhichIsNotPresentInTheRow_shouldGiveAnEmptyStringAsOutput()
    {
        $classObject = new BaseReportController;

        // updated_at is not present
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            [':created_at - :updated_at*:created_at', (object)['created_at' => 100]]);

        $this->assertEquals('', $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedWhichIsPresentInTheRow_shouldGiveDesiredCalculationResult()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            [':updated_at*:created_at - :created_at', (object)['created_at' => 100, 'updated_at'=>100]]);

        $this->assertEquals(9900, $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedInInvalidFormat_shouldThrowAnExceptionSayingInvalidEquation()
    {
        $classObject = new BaseReportController;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Equation');

        // sending created_at in invalid format (without ":")
        $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['created_at - :created_at', (object)['created_at' => 100, 'updated_at'=>100]]);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedInValidFormatButThereIsAZeroInDenominator_shouldReturnInfinity()
    {
        $classObject = new BaseReportController;

        // sending created_at in invalid format (without ":")
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['1/:created_at', (object)['created_at' => 0]]);

        $this->assertEquals('infinity', $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedasTime_shouldReturnInfinity()
    {
        $classObject = new BaseReportController;

        // sending created_at in invalid format (without ":")
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['1/:created_at', (object)['created_at' => 0]]);

        $this->assertEquals('infinity', $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedWhoseValueIsAnEmptyString_shouldReturnEmptyStringWithoutBreaking()
    {
        $classObject = new BaseReportController;

        // sending created_at in invalid format (without ":")
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            [':created_at/90', (object)['created_at' => ""]]);

        $this->assertEquals('', $methodResponse);
    }

    /** @group validateEquation */
    public function test_validateEquation_whenAVariableIsPassedWhichIsNotPresentInTheAllowedVariables_shouldThrowAnException()
    {
        $this->expectException(VariableNotFoundException::class);
        $this->expectExceptionMessage('Variable updated_at is not present in given object');

        $classObject = new BaseReportController;

        // updated_at is not present
        $this->getPrivateMethod($classObject, 'validateEquation',
            [':created_at - :updated_at*:created_at', [':created_at']]);
    }

    /** @group validateEquation */
    public function test_validateEquation_whenAVariableIsPassedWhichIsPresentInAllowedVariables_shouldNotThrowAnyException()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'validateEquation',
            [':updated_at*:created_at - :created_at', ['created_at', 'updated_at']]);

        // if any exception was thrown, this test would have been shown as errored
        $this->assertTrue(true);
    }

    /** @group validateEquation */
    public function test_validateEquation_whenAVariableIsPassedInInvalidFormat_shouldThrowAnExceptionSayingInvalidEquation()
    {
        $classObject = new BaseReportController;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Equation');

        // sending created_at in invalid format (without ":")
        $this->getPrivateMethod($classObject, 'validateEquation',
            ['created_at - :created_at', ['created_at']]);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAFunctionIsPassedWhichDoesntExistsInClass_shouldReturnThrowAnException()
    {
        $classObject = new BaseReportController;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Equation');

        $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['@invalid_method()', (object)['created_at' => 0]]);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAFunctionIsPassedWhichExistInClass_shouldCallThatMethodWithProperArguments()
    {
        $classObject = new BaseReportController;

        $ticket = factory(Tickets::class)->create(['dept_id'=>1]);

        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['@last_status_change(open)', (object)['id' => $ticket->id]]);

        $this->assertEquals(null, $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAFunctionIsPassedWhichExistInClassButItsValueIsNull_shouldReturnEmptyStringAsOutput()
    {
        $classObject = new BaseReportController;

        $ticket = factory(Tickets::class)->create(['dept_id'=>1]);

        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['@last_status_change(invalid_status)', (object)['id' => $ticket->id]]);

        $this->assertEquals('', $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedWhoseValueIsNull_shouldReturnEmptyString()
    {
        $classObject = new BaseReportController;

        // sending created_at in invalid format (without ":")
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            ['1/:created_at', (object)['created_at' => null]]);

        $this->assertEquals('', $methodResponse);
    }

    /** @group getParsedEquationValue */
    public function test_getParsedEquationValue_whenAVariableIsPassedWhoseValueIsHyperlink_shouldReturnValueAfterStrippingTags()
    {
        $classObject = new BaseReportController;

        // sending created_at in invalid format (without ":")
        $methodResponse = $this->getPrivateMethod($classObject, 'getParsedEquationValue',
            [':created_at', (object)['created_at' => "<a>10</a>"]]);

        $this->assertEquals(10, $methodResponse);
    }

    public function test_getDateRangeByFormat_whenFormatIsPassedAsDay_shouldGiveStartAndEndOfDayRange()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'getDateRangeByFormat',['day', '12 Dec 2019']);

        $this->assertEquals('2019-12-12 00:00:00~2019-12-12 23:59:59', $methodResponse);
    }

    public function test_getDateRangeByFormat_whenFormatIsPassedAsWeek_shouldGiveStartAndEndOfWeekRange()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'getDateRangeByFormat',['week', '12 Dec 2019']);

        $this->assertEquals('2019-12-08 00:00:00~2019-12-14 23:59:59', $methodResponse);
    }

    public function test_getDateRangeByFormat_whenFormatIsPassedAsMonth_shouldGiveStartAndEndOfMonthRange()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'getDateRangeByFormat',['month', 'Dec 2019']);

        $this->assertEquals('2019-12-01 00:00:00~2019-12-31 23:59:59', $methodResponse);
    }

    public function test_getDateRangeByFormat_whenFormatIsPassedAsYear_shouldGiveStartAndEndOfYearRange()
    {
        $classObject = new BaseReportController;

        $methodResponse = $this->getPrivateMethod($classObject, 'getDateRangeByFormat',['year', '2019']);

        $this->assertEquals('2019-01-01 00:00:00~2019-12-31 23:59:59', $methodResponse);
    }
}
