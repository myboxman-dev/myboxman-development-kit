<?php

class MyboxmanJob
{
    protected $loadingAddress = "";
    protected $deliveryAddress = "";
    protected $loadingType = "ASAP";
    protected $deliveryType = "ASAP";
    protected $loadingDateTime1 = "";
    protected $loadingDateTime2 = "";
    protected $deliveryDateTime1 = "";
    protected $deliveryDateTime2 = "";
    protected $cardId = "";
    protected $publicDescription = "";
    protected $privateDescription = "";
    protected $categoryId = "1";
    protected $debug = true;
    protected $apiKey = "";
    protected $apiSecret = "";
    protected $jobId = "";
    protected $jobTitle = "";
    protected $jobDescription = "";
    protected $jobPrivateDescription = "";

    public function __construct(object $credentials, boolean $debug) {
        if($debug) {
            $credentials = (object) array(
                'api_key'=>'4bhiovju-19zab-ypaot-md0k-dz2gghf1',
                'api_secret'=>'y6bjgxjj-w6v9f-e9667-j68j-393q2p3s'
            );
        }

        $this->setDebug($debug);
        $this->apiKey = $credentials->api_key;
        $this->apiSecret = $credentials->api_secret;
    }

    public function setDebug(boolean $debug) {
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

    public function getLoadingAddress() {
        return $this->loadingAddress;
    }

    public function setDeliveryAddress($number,$street,$city,$country) {
        $this->deliveryAddress = (object) array(
            'number'=>$number,
            'street'=>$street,
            'city'=>$city,
            'country'=>$country
        );
    }

    public function getDeliveryAddress() {
        return $this->deliveryAddress;
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

    public function setDeliveryType($type) {
        $availableTypes = $this->getTypes();
        if (in_array($type, $availableTypes)) {
            $this->deliveryType = $type;
        } else {
            throw new Exception('This delivery type is not available');
        }
    }

    public function getDeliveryType() {
        return $this->deliveryType;
    }

    public function setLoadingDateTime($loadingDateTime1,$loadingDateTime2="") {

        if (gettype($loadingDateTime1)!="object" || get_class($loadingDateTime1)!="DateTime") {
            throw new Exception('LoadingDateTime1 is not in a valid format : DateTime object expected');
        }

        if($loadingDateTime2!="" && (gettype($loadingDateTime2)!="object") || get_class($loadingDateTime2)!="DateTime") {
            throw new Exception('LoadingDateTime2 is not in a valid format : DateTime object or empty string expected');
        }

        $this->loadingDateTime1 = $loadingDateTime1;
        $this->loadingDateTime2 = $loadingDateTime2;
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

    public function setPublicDescription($description) {
        $this->publicDescription = $description;
    }

    public function setPrivateDescription($description) {
        $this->privateDescription = $description;
    }

    public function setCategoryId($category) {
        $category_available = $this->getCategories();
        if (in_array($category, $category_available)) {
            $this->categoryId = $category;
        } else {
            throw new Exception('Category is not a valid category');
        }
    }

    public function setJobTitle($title) {
        $this->jobTitle = $title;
    }

    public function setJobDescription($description) {
        $this->jobDescription = $description;
    }

    public function setJobPrivateDescription($privateDescription) {
        $this->jobPrivateDescription = $privateDescription;
    }


    private function getHttpObject($uri,$method) {

        if ($this->debug) {
            $domain = "http://staging.myboxman.com";
        } else {
            $domain = "http://app.myboxman.com";
        }

        $req = new HttpRequest($domain.$uri,$method);

        $req->setHeaders(array(
            'App-Key'=>$this->apiKey,
            'App-Secret'=>$this->apiSecret,
        ));

        return $req;
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
            'gmt'=>'01000',
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

        if ($http->getMethod()==HttpRequest::METH_GET) {
            $http->addQueryData($params);
        }

        if ($http->getMethod() == HttpRequest::METH_POST) {
            $http->addPostFields($params);
        }

        return $http;
    }

    private function initCardId() {
        if($this->cardId=='') {
            $http = $this->getHttpObject('/api/getCreditCards', HttpRequest::METH_GET);

            try {
                $res = $http->send();
                if($res->getResponseCode()==200) {
                    $res = json_decode($res->getResponseBody());
                    $this->cardId = $res->response[0]->cardId;
                }
            } catch (HttpException $e) {
                echo $e;
            }

        }
    }

    public function getTypes() {
        $http = $this->getHttpObject('/api/getDateTypes', HttpRequest::METH_GET);

        try {
            $res = $http->send();
            if ($res->getResponseCode()==200) {
                return json_decode($res->getResponseBody(),true);
            }
        } catch(HttpException $e) {
            echo $e;
        }

    }

    public function getCategories() {
        $http = $this->getHttpObject('/api/getCategories', HttpRequest::METH_GET);

        try {

            $res = $http->send();
            if ($res->getResponseCode()==200) {
                return json_decode($res->getResponseBody(),true);
            }

        } catch(HttpException $e) {
            echo $e;
        }
    }

    public function getEstimation() {
        $http = $this->getHttpObject('/api/getEstimation', HttpRequest::METH_GET);

        $http = $this->prepareReqForMission($http);

        try {
            $res = $http->send();
            if($res->getResponseCode()==200) {
                return json_decode($res->getResponseBody());
            }
        } catch (HttpException $e) {
            echo $e;
        }
    }

    public function getJobId() {
        return $this->jobId;
    }

    public function postJob() {
        $http = $this->getHttpObject('/api/postJob', HttpRequest::METH_POST);

        $http = $this->prepareReqForMission($http);

        $http->addPostFields(array(
            'title'=>$this->jobTitle,
            'description'=>$this->jobDescription,
            'privateDescription'=>$this->jobPrivateDescription,
        ));

        try {
            $res = $http->send();
            if($res->getResponseCode()==200) {
                $res = json_decode($res->getResponseBody());
                $this->jobId = $res->response->jobId;
                return $res;
            }
        } catch (HttpException $e) {
            echo $e;
        }
    }



}
