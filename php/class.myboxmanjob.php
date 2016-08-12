<?php

class MyboxmanJob
{
  public $loadingAddress = "";
  public $deliveryAddress = "";
  protected $loadingType = "ASAP";
  protected $deliveryType = "ASAP";
  protected $loadingDateTime1 = "";
  protected $loadingDateTime2 = "";
  protected $deliveryDateTime1 = "";
  protected $deliveryDateTime2 = "";
  protected $cardId = "";
  public $description = "";
  public $privateDescription = "";
  public $categoryId = "1";
  public $debug = false;
  protected $apiKey = "";
  protected $apiSecret = "";
  protected $id = "";
  public $title = "";
  public $price = "";
  private $_currentcURLMethod = "";
  private $_currentcURLRoute = "";
  private $_currentcURLParams = "";
  private $_lastAPIResponse = "";

  public function __construct($credentials, $jobId=null, $debug=null) {
    if($debug) {
      $credentials = (object) array(
        'api_key'=>'4bhiovju-19zab-ypaot-md0k-dz2gghf1',
        'api_secret'=>'y6bjgxjj-w6v9f-e9667-j68j-393q2p3s'
      );
    }

    $this->debug = $debug == null ? false : $debug;
    $this->apiKey = $credentials->api_key;
    $this->apiSecret = $credentials->api_secret;

    if($jobId!=null) {
      $this->loadJobFromJobId($jobId);
    }

  }

  private function loadJobFromJobId($jobId) {
    $http = $this->getHttpObject("/api/getJobDetails", "GET");

    curl_setopt($http, CURLOPT_URL, $this->_currentcURLRoute.'?'.http_build_query(array('jobId'=>$jobId)));

      $res=curl_exec($http);

        $res = json_decode($res);

        $job = $res->response;
        $this->id = $job->_id;
        $this->title = $job->title;
        $this->description = $job->description;
        $this->categoryId = $job->categoryId;

        preg_match('/^[0-9]{1,5}/', $job->origin->formattedAddress,$numberOrigin);
        preg_match('/^[0-9]{1,5}/', $job->destination->formattedAddress,$numberDestination);

        $this->loadingAddress = (object) array(
          'street'=>$job->origin->street,
          'city'=>$job->origin->city,
          'country'=>$job->origin->country,
          'number'=>$numberOrigin[0]
        );

        $this->deliveryAddress = (object) array(
          'street'=>$job->destination->street,
          'city'=>$job->destination->city,
          'country'=>$job->destination->country,
          'number'=>$numberDestination[0]
        );

        $this->loadingType = $job->loadingType;
        $this->deliveryType = $job->deliveryType;

        if ($this->loadingType=="FIX") {
          $this->loadingDateTime1 = new DateTime('@'.$job->loadingDateTime1->timeStamp);
        }

        if ($this->loadingType=="FLEXIBLE") {
          $this->loadingDateTime1 = new DateTime('@'.$job->loadingDateTime1->timeStamp);
          $this->loadingDateTime2 = new DateTime('@'.$job->loadingDateTime2->timeStamp);
        }
        if ($this->deliveryType=="FIX") {
          $this->deliveryDateTime1 = new DateTime('@'.$job->deliveryDateTime1->timeStamp);
        }

        if ($this->deliveryType=="FLEXIBLE") {
          $this->deliveryDateTime1 = new DateTime('@'.$job->deliveryDateTime1->timeStamp);
          $this->deliveryDateTime2 = new DateTime('@'.$job->deliveryDateTime2->timeStamp);
        }

        $this->price = $job->price;


        return true;

  }

  public function setDebug($debug) {
    $this->debug = $debug;
  }

  public function setLoadingAddress($number,$street,$city,$country) {
    $this->loadingAddress = (object) array(
      'number'=>$number,
      'street'=>$street,
      'city'=>$city,
      'country'=>$country
    );
  }

  public function setDeliveryAddress($number,$street,$city,$country) {
    $this->deliveryAddress = (object) array(
      'number'=>$number,
      'street'=>$street,
      'city'=>$city,
      'country'=>$country
    );
  }

  public function setLoadingType($type) {
    $availableTypes = $this->getTypes();
    if (in_array($type, $availableTypes)) {
      $this->loadingType = $type;
    } else {
      throw new Exception('This loading type is not available');
    }
  }

  public function getLoadingType() {
    return $this->loadingType;
  }
  public function getDeliveryType() {
    return $this->deliveryType;
  }

  public function setDeliveryType($type) {
    $availableTypes = $this->getTypes();
    if (in_array($type, $availableTypes)) {
      $this->deliveryType = $type;
    } else {
      throw new Exception('This delivery type is not available');
    }
  }

  public function setLoadingDateTime($loadingDateTime1,$loadingDateTime2="") {

    if (gettype($loadingDateTime1)!="object" || get_class($loadingDateTime1)!="DateTime") {
      throw new Exception('LoadingDateTime1 is not in a valid format : DateTime object expected');
    }

    if($loadingDateTime2!="" && (gettype($loadingDateTime2)!="object" || get_class($loadingDateTime2)!="DateTime")) {
      throw new Exception('LoadingDateTime2 is not in a valid format : DateTime object or empty string expected');
    }

    $this->loadingDateTime1 = $loadingDateTime1;
    $this->loadingDateTime2 = $loadingDateTime2;
  }

  public function getLoadingTimes() {
    return (object) array(
      'loadingDateTime1'=>$this->loadingDateTime1,
      'loadingDateTime2'=>$this->loadingDateTime2,
    );
  }

  public function getDeliveryTimes() {
    return (object) array(
      'deliveryDateTime1'=>$this->deliveryDateTime1,
      'deliveryDateTime2'=>$this->deliveryDateTime2,
    );
  }

  public function setDeliveryDateTime($deliveryDateTime1,$deliveryDateTime2="") {

    if (gettype($deliveryDateTime1)!="object" || get_class($deliveryDateTime1)!="DateTime") {
      throw new Exception('deliveryDateTime1 is not in a valid format : DateTime object expected');
    }

    if ($deliveryDateTime2!="" && (gettype($deliveryDateTime2)!="object") || get_class($deliveryDateTime2)!="DateTime") {
      throw new Exception('LoadingDateTime2 is not in a valid format : DateTime object or empty string expected');
    }

    $this->deliveryDateTime1 = $deliveryDateTime1;
    $this->deliveryDateTime2 = $deliveryDateTime2;
  }

  public function setCategoryId($category) {
    $category_available = $this->getCategories();
    if (in_array($category, $category_available)) {
      $this->categoryId = $category;
    } else {
      throw new Exception('Category is not a valid category');
    }
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function setDescription($description) {
    $this->description = $description;
  }

  public function setPrivateDescription($privateDescription) {
    $this->privateDescription = $privateDescription;
  }


  private function getHttpObject($uri,$method) {

    if ($this->debug) {
      $domain = "http://staging.myboxman.com";
    } else {
      $domain = "http://app.myboxman.com";
    }

    $this->_currentcURLMethod = $method;
    $this->_currentcURLRoute = $domain.$uri;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $domain.$uri);
    if($method=="POST") {
      curl_setopt($ch, CURLOPT_POST, true);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'App-Key: '.$this->apiKey,
      'App-Secret: '.$this->apiSecret,
    ));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return $ch;
  }

  private function prepareReqForMission($http) {

    $params = array(
      'categoryId'=>$this->categoryId,
      'loadingType'=>$this->loadingType,
      'deliveryType'=>$this->deliveryType,
      'originCity'=>$this->loadingAddress->city,
      'originCountry'=>$this->loadingAddress->country,
      'originSreetNumber'=>$this->loadingAddress->number,
      'originStreet'=>$this->loadingAddress->street,
      'destinationCity'=>$this->deliveryAddress->city,
      'destinationCountry'=>$this->deliveryAddress->country,
      'destinationStreetNumber'=>$this->deliveryAddress->number,
      'destinationStreet'=>$this->deliveryAddress->street,
      'gmt'=>'0100',
    );

    if ($this->loadingType=="FIX") {
      $params['loadingDateTime1'] = $this->loadingDateTime1->format('Y-m-d H:i:s');
    }

    if($this->loadingType=="FLEXIBLE") {
      $params['loadingDateTime1'] = $this->loadingDateTime1->format('Y-m-d H:i:s');
      $params['loadingDateTime2'] = $this->loadingDateTime2->format('Y-m-d H:i:s');
    }

    if ($this->deliveryType=="FIX") {
      $params['deliveryDateTime1'] = $this->deliveryDateTime1->format('Y-m-d H:i:s');
    }
    if($this->deliveryType=="FLEXIBLE") {
      $params['deliveryDateTime1'] = $this->deliveryDateTime1->format('Y-m-d H:i:s');
      $params['deliveryDateTime2'] = $this->deliveryDateTime2->format('Y-m-d H:i:s');
    }

    $this->_currentcURLParams = $params;

    if ($this->_currentcURLMethod=="GET") {
      curl_setopt($http, CURLOPT_URL, $this->_currentcURLRoute.'?'.http_build_query($params));
    }

    if ($this->_currentcURLMethod=="POST") {
      curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    return $http;
  }

  public function getLastResponse() {
    return $this->_lastAPIResponse;
  }

  private function initCardId() {
    if($this->cardId=='') {
      $http = $this->getHttpObject('/api/getCreditCards', "GET");

      $res = curl_exec($http);
      $res = json_decode($res);
      $this->_lastAPIResponse = $res;
      $this->cardId = $res->response[0]->cardId;

    }
  }

  public function getTypes() {
    $http = $this->getHttpObject('/api/getDateTypes', "GET");

    $res = curl_exec($http);
    $res = json_decode($res,true);

    $this->_lastAPIResponse = $res;

    return $res;

  }

  public function getCategories() {
    $http = $this->getHttpObject('/api/getCategories', "GET");

    $res = curl_exec($http);
    $res = json_decode($res,true);

    $this->_lastAPIResponse = $res;

    return $res;

  }

  public function getEstimation() {
    $http = $this->getHttpObject('/api/getEstimation', "GET");

    $http = $this->prepareReqForMission($http);

    $res = curl_exec($http);

    $res = json_decode($res);
    $this->price = $res->response->price;

    $this->_lastAPIResponse = $res;

    return $res;
  }

  public function getId() {
    return $this->id;
  }

  public function postJob() {

    $this->initCardId();

    $http = $this->getHttpObject('/api/postJob', "POST");

    $http = $this->prepareReqForMission($http);

    $params = $this->_currentcURLParams;

    $params['title']=$this->title;
    $params['description']=$this->description;
    $params['privateDescription']=$this->privateDescription;
    $params['cardId']=$this->cardId;

    $this->_currentcURLParams = $params;

    curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($params));

      $res = curl_exec($http);
      $res = json_decode($res);

      $this->_lastAPIResponse = $res;

      $this->jobId = $res->response->jobId;
      return $res;
  }

  public function confirmPickup($codeConfirm) {

    $http = $this->getHttpObject('/api/confirmPickup', 'POST');

    $params = array(
      'jobId'=>$this->id,
      'confirmationCode'=>$codeConfirm,
    );

    curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($params));

    $res = curl_exec($http);
    $res = json_decode($res);

    $this->_lastAPIResponse = $res;


    return $res->status==200;

  }

  public function confirmDelivery() {

    $http = $this->getHttpObject('/api/confirmDelivery', 'POST');

    $params = array(
      'jobId'=>$this->id,
    );

    curl_setopt($http, CURLOPT_POSTFIELDS, http_build_query($params));
    $res = curl_exec($http);
    $res = json_decode($res);

    return $res->status==200;
  }



}
