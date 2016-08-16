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
