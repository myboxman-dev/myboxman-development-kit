# MyBoxMan Job Class to help interact with MyBoxMan API

## Features

* MyBoxManJob Class helps not bothering about the API calls behind the scene
* Estimate the price for a delivery
* Post quickly a mission
* Track the mission status and confirm Pickup / Delivery etc
* Retrieve easily a posted Job and its information

## Dependencies

* PHP 5
* cURL Library

## Licence

This software is distributed under the LGPL 2.1 license. Please read LICENSE for information on the software availability and distribution.

## Installation

Use class.myboxmanjob.php in your project and just easily include it. That's it, you can now use the class within your code

## Examples

### Create a job, then post it

```php

<?php


include('lib/myboxman/class.myboxmanjob.php');

$credentials = (object) array( //Api credentials can be obtained on app.myboxman.com
  'App-Key'=>'My app Key',
  'App-Secret'=>'My app Secret',
);

$debug = true; // Debug true means posting fake mission with a test account, no deliverer will come, only for testing and integration

$jobId = null; // Used to retrieve a job if necessary

$job = new MyBoxmanJob($credentials,$jobId,$debug);

$job->setTitle('Test MBK');
$job->setDescription('Job créé avec le mbk');

$job->setLoadingAddress('31','rue de reuilly','paris','france');
$job->setDeliveryAddress('38','rue condorcet','paris','france');

$res = $job->getEstimation();
echo $job->price;

if ($job->price < 20) {
  $job->postJob();
}


```

### Retrieve a job from MyBoxMan, read information

```php

<?php

include('class.myboxmanjob.php');

$credentials = (object) array( //Api credentials can be obtained on app.myboxman.com
  'App-Key'=>'My app Key',
  'App-Secret'=>'My app Secret',
);

$debug = true; // Debug true means posting fake mission with a test account, no deliverer will come, only for testing and integration

$jobId = "Xym3KSwXFDN2BgDYx"; // Used to retrieve a job if necessary

$job = new MyBoxmanJob($credentials,$jobId,$debug);

echo $job->title;
echo $job->description;

$codeConfirmation = '17095';

  if($job->confirmPickup($codeConfirmation)) {
    // Update local database, tell the sender, etc
  } else {
    $job->getLastResponse(); //Debugging purpose
  }

  if($job->confirmDelivery()) {
    // Update local database, tell the sender, etc
  } else {
    $job->getLastResponse(); //Debugging purpose
  }


```

## References

### MyBoxManJob constructor

Name        |  Type  | Description
----------- | ------ | -----------
Credentials | Object | Contains the credentials to login on the API
jobId       | String | Used for retrieving a job that has been previously posted
debug       | bool   | Used to indicate if the mission will be fake or not, debug means fake mission, no real delivery planned

###Example
```php

$credentials = (object) array( //Api credentials can be obtained on app.myboxman.com
  'App-Key'=>'My app Key',
  'App-Secret'=>'My app Secret',
);

$debug = true; // Debug true means posting fake mission with a test account, no deliverer will come, only for testing and integration

$jobId = "Xym3KSwXFDN2BgDYx"; // Used to retrieve a job if necessary

$job = new MyBoxmanJob($credentials,$jobId,$debug);
```

### Public attributes

Name               |  Type  | Description
-------------------|--------|--------------------------------------------------------------------------------------
loadingAddress     | Object | Contains the address decomposed through number,street,city,country
deliveryAddress    | Object | Same as above
title              | String | Title of the mission, publicly available eg 'Phone delivery'
description        | String | Contains the publicly available description for the mission eg '4 phones to deliver'
privateDescription | String | Contains more information about the mission for the boxman who will take it
debug              |  Bool  | Debug mode activated or not, see constructor
price              | String | Contains the price of the mission gotten through getEstimation

### Protected attributes, accessibles through getters (eg: MyboxmanJob::getLoadingTimes())

Name               |       Type        | Description
-------------------|-------------------|--------------------------------------------------------------------------------------
loadingDateTime1   | Object (DateTime) | Time of loading if loadingType equals FIX, start of time slot if FLEXIBLE, empty if ASAP
loadingDateTime2   | Object (DateTime) | Contains end of loading time slot if loadingType is FLEXIBLE
loadingType        |      String       | Describes when the loading must occur, possible values : ASAP, FLEXIBLE, FIX
deliveryType       |      String       | Same as above
deliveryDateTime1  | Object (DateTime) | Time of delivery if deliveryType equals FIX, start of time slot if FLEXIBLE, empty if ASAP
deliveryDateTime2  | Object (DateTime) | Contains end of delivery time slot if deliveryType is FLEXIBLE
id                 |      String       | Contains the id (reference) of the mission in MyBoxMan system
categoryId         |       String      | Category of the item sent, values: 1=BAG; 2=SUITCASE; 3=Car; 4=Van


### Public methods (Setters)

#### SetLoadingAddress(number,street,city,country)
* Number: Street number eg: '38'
* Street: Street Name eg: 'Rue de reuilly'
* City: Name of the city eg: 'Paris'
* Country: Name of the country eg: 'France'

#### setDeliveryAddress(number,street,city,country)
Same as above

#### setLoadingDateTime(loadingDateTime1, loadingDateTime2="")
* loadingDateTime1 must be a DateTime object
* loadingDateTime2 must be a DateTime object, can be missing if type is FIX

#### setDeliveryDateTime(deliveryDateTime1, deliveryDateTime1="")
Same as above

#### setLoadingType(type)
* type must be a string containing ASAP, FIX or FLEXIBLE

#### setDeliveryType(type)
* type must be a string containing ASAP, FIX or FLEXIBLE

#### setCategoryId(category)
* category must be a string containg 1,2,3 or 4

### Public methods (Getters)

#### getLoadingTimes()
Returns the loadingTimes in an array

#### getDeliveryTimes()
Returns the deliveryTimes in an array

#### getLoadingType()
Returns the loading type

#### getDeliveryType()
Returns the delivery type

#### getTypes()
Returns the available types in an array

#### getCategories()
Returns the possible categories in an array

#### getId()
Returns job Id

#### getLastResponse()
Returns the last API response, used mostly for debugging purpose

### Mission control methods

#### getEstimation()
Returns the estimated price for the current settings

#### postJob()
Post the job on MyBoxMan (on debug platform with fake account if debug equals true)

#### confirmPickup(codeConfirmation)
Confirm the pickup for the mission
* codeConfirmation must be a string

#### confirmDelivery()
Confirm the delivery of the items and conclude the mission
