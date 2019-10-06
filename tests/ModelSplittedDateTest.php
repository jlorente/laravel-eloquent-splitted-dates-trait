<?php

namespace Jlorente\Laravel\Eloquent\Concerns\SplittedDates\Tests;

use Custom\Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase;
use Jlorente\Laravel\Eloquent\Concerns\SplittedDates\HasSplittedDates;

/**
 * Class ModelSplittedDatesTest.
 * 
 * @author Jose Lorente <jose.lorente.martin@gmail.com>
 */
class ModelSplittedDatesTest extends TestCase
{

    /**
     * 
     * @param array $splittedDates
     * @return Model
     */
    protected function createAnonymousModel(array $attributes, array $splittedDates): Model
    {
        $model = new class() extends Model
        {

            use HasSplittedDates;

            /**
             * The dates attributes.
             *
             * @var array
             */
            protected $splittedDates = [
            ];

            /**
             * 
             * @param array $attributes
             */
            public function setTestAttributes(array $attributes)
            {
                foreach ($attributes as $key => $value) {
                    if (is_string($key)) {
                        $this->attributes[$key] = $value;
                    } else {
                        $this->attributes[$value] = null;
                    }
                }
            }

            /**
             * 
             * @param array $attributes
             */
            public function setTestSplittedDates(array $splittedDates)
            {
                $this->splittedDates = $splittedDates;
            }
        };

        $model->setTestAttributes($attributes);
        $model->setTestSplittedDates($splittedDates);

        return $model;
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearWithDefaultConfiguration()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day'
                ], [
            'begin_at'
        ]);

        $model->begin_at_year = 2020;
        $this->assertEquals(Carbon::create(2020), $model->begin_at);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearAndMonthWithDefaultConfiguration()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day'
                ], [
            'begin_at'
        ]);

        $model->begin_at_year = 2020;
        $model->begin_at_month = 11;
        $this->assertEquals(Carbon::create(2020, 11), $model->begin_at);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearMonthAndDayWithDefaultConfiguration()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day'
                ], [
            'begin_at'
        ]);

        $model->begin_at_year = 2005;
        $model->begin_at_month = 5;
        $model->begin_at_day = 30;
        $this->assertEquals(Carbon::create(2005, 5, 30), $model->begin_at);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetDateWithDefaultConfiguration()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day'
                ], [
            'begin_at'
        ]);

        $model->begin_at = '2006-08-12';
        $this->assertEquals(2006, $model->begin_at_year);
        $this->assertEquals(8, $model->begin_at_month);
        $this->assertEquals(12, $model->begin_at_day);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearAndMonthWithDefaultConfigurationDoesNotModifyOtherPropertyWhenOtherPropertyIsNull()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day'
                ], [
            'begin_at'
        ]);

        $model->begin_at_year = 2020;
        $model->begin_at_month = 11;
        $this->assertNull($model->begin_at_day);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearAndMonthWithDefaultConfigurationDoesNotModifyOtherPropertyWhenOtherPropertyIsNotNull()
    {
        $model = $this->createAnonymousModel([
            'begin_at'
            , 'begin_at_year'
            , 'begin_at_month'
            , 'begin_at_day' => 18
                ], [
            'begin_at'
        ]);

        $model->begin_at_year = 2020;
        $model->begin_at_month = 11;
        $this->assertEquals(18, $model->begin_at_day);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearConfiguringYearAndMonth()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_year'
            , 'end_at_month'
                ], [
            'end_at' => ['year', 'month']
        ]);

        $model->end_at_year = 2025;
        $this->assertEquals(Carbon::create(2025), $model->end_at);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetYearAndMinuteConfiguringYearAndMinute()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_year'
            , 'end_at_minute'
                ], [
            'end_at' => ['year', 'minute']
        ]);

        $model->end_at_year = 2001;
        $model->end_at_minute = 54;
        $this->assertEquals(Carbon::create(2001, 1, 1, 0, 54), $model->end_at);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetDateAsDateTimeObjectConfiguringYearAndSecond()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_year'
            , 'end_at_second'
                ], [
            'end_at' => ['year', 'second']
        ]);

        $date = now();
        $model->end_at = $date;
        $this->assertEquals($date->year, $model->end_at_year);
        $this->assertEquals($date->second, $model->end_at_second);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testSetDateAsStringConfiguringHourAndMinute()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_hour'
            , 'end_at_minute'
                ], [
            'end_at' => ['hour', 'minute']
        ]);

        $model->end_at = '2027-07-15 19:56:01';
        $this->assertEquals(19, $model->end_at_hour);
        $this->assertEquals(56, $model->end_at_minute);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testHourOverflow()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_hour'
            , 'end_at_minute'
                ], [
            'end_at' => ['hour', 'minute']
        ]);

        $model->end_at = '2027-07-15 19:56:01';
        $model->end_at_hour = 27;
        $this->assertEquals(16, $model->end_at->day);
        $this->assertEquals(3, $model->end_at->hour);
        $this->assertEquals(27, $model->end_at_hour);
    }

    /**
     * @group CommonTests
     * @group CommonUnitTests
     * @group ModelSplittedDatesTest
     */
    public function testDateSetToNullSetsAllSplittedValuesToNull()
    {
        $model = $this->createAnonymousModel([
            'end_at'
            , 'end_at_year'
            , 'end_at_month'
            , 'end_at_day'
                ], [
            'end_at'
        ]);

        $model->end_at = '2027-07-15 19:56:01';
        $this->assertEquals(15, $model->end_at_day);

        $model->end_at = null;
        $this->assertNull($model->end_at_day);
        $this->assertNull($model->end_at_month);
        $this->assertNull($model->end_at_year);
    }

}
