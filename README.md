Laravel Eloquent Model Splitted Dates Trait
===========================================

A Laravel Trait for Eloquent Models to handle date fields that are stored both 
in the date field itself and in separate field components such as year, month, 
day, hour, etc...

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

With Composer installed, you can then install the extension using the following commands:

```bash
$ php composer.phar require jlorente/laravel-eloquent-splitted-dates-trait
```

or add 

```json
...
    "require": {
        "jlorente/laravel-eloquent-splitted-dates-trait": "*"
    }
```

to the ```require``` section of your `composer.json` file.

## Usage

### Basic Configuration

To enable and configure splitted dates fields in an Eloquent Model use the 
Jlorente\Laravel\Eloquent\Concerns\SplittedDates\HasSplittedDates Trait on the model 
and define the splittedDates array with the date fields you want to store splitted.


```php
<?php

use Illuminate\Database\Eloquent\Model;
use Jlorente\Laravel\Eloquent\Concerns\SplittedDates\HasSplittedDates;

class Subscription extends Model
{
    use HasSplittedDates;

    /**
     * The splitted dates attributes.
     *
     * @var array
     */
    protected $splittedDates = [
        'begin_at'
    ];
}

```


In the example, the "begin_at" date will be stored splitted in the fields 
"begin_at_year", "begin_at_month" and "begin_at_day". Everytime the "begin_at" 
field is set, the other fields will be set also and vice versa.


```php

$subscription = new Subscription();
$subscription->begin_at = Carbon::create(2019, 10, 2);

echo $subscription->begin_at_year; // 2019
echo $subscription->begin_at_month; // 10
echo $subscription->begin_at_day; // 2

```


If one of the splitted components is null, the main attribute "begin_at" correspondent 
component will be set to to 1 in case of month and day properties, 0 in case of 
time properties or the current year in case of year property.


```php

$subscription = new Subscription();
$subscription->begin_at_year = 2025 // 2025

echo $subscription->begin_at->toDateTimeString(); // 2025-01-01 00:00:00

$subscription->begin_at_day = 26 // 2025

echo $subscription->begin_at->toDateTimeString(); // 2025-01-26 00:00:00

```

All the attributes defined in the splittedDates array will be treated as defined 
in the [date mutator](https://laravel.com/docs/6.x/eloquent-mutators#date-mutators) 
property so you can set its value to a UNIX timestamp, date string (Y-m-d), 
date-time string, or a DateTime / Carbon instance.


```php

$subscription = new Subscription();
$subscription->begin_at = Carbon::create(2019, 10, 2); // OK
$subscription->begin_at = '2019-10-02' // OK
$subscription->begin_at = '2019-10-02 22:11:45' // OK
$subscription->begin_at = strtotime() // OK

```


Remember that the database table must have the correspondent columns "_year", "_month" 
and "_day" to store the splitted date value.

### Advanced Configuration

The default configuration for splitted dates is to map the date value to a year, 
month and day field, but you can map the value to whatever carbon unit 
you want (year, month, day, hour, minute, second, milli, millisecond, micro, microsecond).

To configure the splitted date with other units than the default ones define 
which ones do you want to store on splittedDates property definition.


```php
<?php

use Illuminate\Database\Eloquent\Model;
use Jlorente\Laravel\Eloquent\Concerns\SplittedDates\HasSplittedDates;

class Subscription extends Model
{
    use HasSplittedDates;

    /**
     * The splitted dates attributes.
     *
     * @var array
     */
    protected $splittedDates = [
        'begin_at' => ['hour', 'minute']
    ];
}

```


Doing so, the properties "begin_at_hour" and "begin_at_minute" will be set everytime 
the "begin_at" field is set and vice versa.

```php

$subscription = new Subscription();
$subscription->begin_at = Carbon::create(2019, 10, 2, 15, 45, 12);

echo $subscription->begin_at_hour; // 15
echo $subscription->begin_at_minute; // 45

$subscription->begin_at_minute = 31;

echo $subscription->begin_at->toDateTimeString(); // 2019-10-02 15:31:12

```


## Further considerations

The trait does not validate dates when setting the splitted fields, so it is 
possible that date overflows ocurr if you set an invalid value for a date.


```php

$subscription = new Subscription();
$subscription->begin_at = Carbon::create(2019, 10, 2, 15, 45, 12);

$subscription->begin_at_hour = 27; // => Invalid value

echo $subscription->begin_at_hour; // 27
echo $subscription->begin_at->toDateTimeString(); // 2019-10-03 03:45:12

```


## License 

Copyright &copy; 2019 José Lorente Martín <jose.lorente.martin@gmail.com>.

Licensed under the BSD 3-Clause License. See LICENSE.txt for details.
