# MyBoxMan Job Class to help interact with MyBoxMan API

## Features

* MyBoxManJob Class helps not bothering about the API calls behind the scene
* Estimate the price for a delivery
* Post quickly a mission
* Track the mission status and confirm Pickup / Delivery etc
* Retrieve easily a posted Job and its information

## Dependencies

* NodeJS
* Request Module (https://github.com/request/request)

## Licence

This software is distributed under the LGPL 2.1 license. Please read LICENSE for information on the software availability and distribution.

## Installation

Use myboxman module in your project and just easily include it by requiring it. Just install the folder in your node_module folder. That's it, you can now use the module within your code

## Examples

### Create a job, then post it

```javascript

var myboxmanjob = require('myboxmanjob');

var job = new myboxmanjob.job(null,true);

job.setLoadingAddress('31','rue de reuilly','paris','france');
job.setDeliveryAddress('38','rue condorcet','paris','france');

job.title = "Job crée avec le MDK NodeJS";
job.description = "Mission fictive";

job.setLoadingType("FLEXIBLE");
job.setDeliveryType("FLEXIBLE");

job.categoryId = 3;

job.setLoadingType('ASAP');

job.setLoadingDateTime(new Date('2016-08-16 16:00:00'),new Date('2016-08-16 17:00:00'));
job.setDeliveryDateTime(new Date('2016-08-16 18:00:00'),new Date('2016-08-16 19:00:00'));

job.getEstimation(onJobEstimated)

function onJobEstimated(res,body) {
  if (res) {
    var price = body.price;
    console.log(price)
    job.postJob(onJobPosted)
  } else {
    console.log(body)
  }
}

function onJobPosted(res,body) {
  if (res) {
    console.log('Job posted '+job.getJobId());
  } else {
    console.log(err);
  }
}

;

```

### Retrieve a job from MyBoxMan, read information

```javascript

var myboxmanjob = require('myboxmanjob');

var job = new myboxmanjob.job(null,true);

job.loadJobFromJobId('ph99vzvMEwpd4mAsB',onJobLoaded);

function onJobLoaded(res,body) {
  if(res) {
    console.log('Job ready to use');
    console.log(job.title);
    console.log(job.description);
    job.confirmPickup('sjdh',onJobPickupConfirmed);
  } else {
    console.log("Error happened while getting the Job");
    console.log(body);
  }
}


function onJobPickupConfirmed(res,body) {
  if (res) {
    console.log('Pickup confirmed');
    job.confirmDelivery(onJobDeliveryConfirmed);
  } else {
    console.log('Error, pickup confirmation failed');
    console.log(body);
  }
}

function onJobDeliveryConfirmed(res,body) {
  if(res) {
    console.log('Delivery confirmed');
  } else {
    console.log('Error, delivery confirmation failed');
    console.log(body);
  }
}

```

## References

### MyBoxManJob constructor

Name        |  Type  | Description
----------- | ------ | -----------
Credentials | Object | Contains the credentials to login on the API
debug       | bool   | Used to indicate if the mission will be fake or not, debug means fake mission, no real delivery planned

###Example
```javascript

var credentials =  { //Api credentials can be obtained on app.myboxman.com
  'App-Key'=>'My app Key',
  'App-Secret'=>'My app Secret',
};

debug = true; // Debug true means posting fake mission with a test account, no deliverer will come, only for testing and integration

$job = new MyBoxmanJob(credentials,debug);
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
categoryId         | String | Category of the item sent, values: 1=BAG; 2=SUITCASE; 3=Car; 4=Van

### Protected attributes, accessibles through getters (eg: myboxmanjob.getLoadingTimes())

Name               |       Type        | Description
-------------------|-------------------|--------------------------------------------------------------------------------------
loadingDateTime1   | Object (Date) | Time of loading if loadingType equals FIX, start of time slot if FLEXIBLE, empty if ASAP
loadingDateTime2   | Object (Date) | Contains end of loading time slot if loadingType is FLEXIBLE
deliveryDateTime1  | Object (Date) | Time of delivery if deliveryType equals FIX, start of time slot if FLEXIBLE, empty if ASAP
deliveryDateTime2  | Object (Date) | Contains end of delivery time slot if deliveryType is FLEXIBLE
loadingType        |      String       | Describes when the loading must occur, possible values : ASAP, FLEXIBLE, FIX
deliveryType       |      String       | Same as above
id                 |      String       | Contains the id (reference) of the mission in MyBoxMan system


### Public methods (Setters)

#### setLoadingAddress(number,street,city,country)
* Number: Street number eg: '38'
* Street: Street Name eg: 'Rue de reuilly'
* City: Name of the city eg: 'Paris'
* Country: Name of the country eg: 'France'

#### setDeliveryAddress(number,street,city,country)
Same as above

#### setLoadingDateTime(loadingDateTime1, loadingDateTime2="")
* loadingDateTime1 must be a Date object
* loadingDateTime2 must be a Date object, can be missing if type is FIX

#### setDeliveryDateTime(deliveryDateTime1, deliveryDateTime1="")
Same as above

#### setLoadingType(type)
* type must be a string containing ASAP, FIX or FLEXIBLE

#### setDeliveryType(type)
* type must be a string containing ASAP, FIX or FLEXIBLE

#### setCategoryId(category)
* category must be a string containing 1,2,3 or 4

### Public methods (Getters)

#### getLoadingTimes()
Returns the loadingTimes in an object

#### getDeliveryTimes()
Returns the deliveryTimes in an object

#### getLoadingType()
Returns the loading type

#### getDeliveryType()
Returns the delivery type

#### getJobId()
Returns job Id

### Mission control methods

#### getEstimation(onJobEstimated(res,body))
Returns the estimated price for the current settings

#### postJob(onJobPosted(res,body))
Post the job on MyBoxMan (on debug platform with fake account if debug equals true)

#### confirmPickup(codeConfirmation,onJobPickupConfirmed(res,body))
Confirm the pickup for the mission
* codeConfirmation must be a string

#### confirmDelivery(onJobDeliveryConfirmed(res,body))
Confirm the delivery of the items and conclude the mission
