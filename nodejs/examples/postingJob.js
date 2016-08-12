var myboxmanjob = require('myboxmanjob');

var job = new myboxmanjob.job(null,null,true);

job.setLoadingAddress('31','rue de reuilly','paris','france');
job.setDeliveryAddress('38','rue simon lambacq','sinceny','france');

job.title = "Job crée avec le MDK NodeJS";
job.description = "Mission fictive";

job.setLoadingType("FLEXIBLE");
job.setDeliveryType("FLEXIBLE");

job.categoryId = 3;

job.setLoadingType('ASAP');

job.setLoadingDateTime(new Date('2016-08-13 16:00:00'),new Date('2016-08-13 17:00:00'));
job.setDeliveryDateTime(new Date('2016-08-13 18:00:00'),new Date('2016-08-13 19:00:00'));

job.getEstimation(function(res) {
  var price = res.price;
  console.log(price)
  if(price<200) {
    job.postJob(onJobPosted);
  }
});



function onJobPosted(res) {
  if (res) {
    console.log('Job posted '+job.getJobId());
  }
}

;
