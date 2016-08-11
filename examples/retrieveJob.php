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
