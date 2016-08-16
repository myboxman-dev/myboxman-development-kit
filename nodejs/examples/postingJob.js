var myboxmanjob = require('myboxmanjob');

var job = new myboxmanjob.job(null,true);

job.setLoadingAddress('31','rue de reuilly','paris','france');
job.setDeliveryAddress('38','rue simon lambacq','sinceny','france');

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
