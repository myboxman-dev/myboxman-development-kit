<?php


include('class.myboxmanjob.php');

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

if($job->price<20) {
  $job->postJob();
}
