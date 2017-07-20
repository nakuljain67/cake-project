<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public $start = 0;
    public $limit = 10;
    private $connection;

    public function initialize() {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        ini_set('max_execution_time', 10000);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers:token,Content-Type, Content-Range, Content-Disposition, Content-Description');

        $this->connection = ConnectionManager::get('default');
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event) {
        if (!array_key_exists('_serialize', $this->viewVars) &&
                in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /* method to make data to json encoded */

    public function json($sts, $msg, $arr = array()) {

        $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
        header("Content-Type:application/json; charset=UTF-8");
        print_r(json_encode($ar));
        die;
    }

    /* method to make data into format view */

    function pr($data, $prefix = '') {
        echo '<pre>' . ($prefix ? '<br/><br/>' . $prefix . '<br/>' : '');

        print_r($data);

        echo '</pre>';
    }

    /* method to give details of country by id */

    function getCountryDetailsById($id) {

        try {

            $countryTable = TableRegistry::get('tbl_countries');
            $query = $countryTable->find()->where(["id" => $id]);
            $code = '';
            $country = '';
            $phonecode = '';
            $status = '';
            if ($query->count() > 0) {

                foreach ($query as $data):
                    $code = $data->code;
                    $country = $data->country;
                    $phonecode = $data->phonecode;
                    $status = $data->status;
                endforeach;

                return array(
                    "code" => $code,
                    "name" => $country,
                    "phonecode" => $phonecode,
                    "status" => $status
                );
            }
            return array();
        } catch (Exception $ex) {
            
        }
    }

    /* get state details by id */

    function getStateDetailsById($id) {

        try {

            $countryTable = TableRegistry::get('tbl_states');
            $query = $countryTable->find()->where(["id" => $id]);
            $country = '';
            $name = '';
            if ($query->count() > 0) {

                foreach ($query as $data):
                    $name = $data->name;
                    $country = $this->getCountryDetailsById($data->country_id)['name'];
                endforeach;

                return array(
                    "name" => $name,
                    "country" => $country
                );
            }
            return array();
        } catch (Exception $ex) {
            
        }
    }

    /* get city details by id */

    function getCityDetailsById($id) {

        try {

            $countryTable = TableRegistry::get('tbl_cities');
            $query = $countryTable->find()->where(["id" => $id]);
            $state = '';
            $name = '';
            $country = '';
            if ($query->count() > 0) {

                foreach ($query as $data):
                    $name = $data->name;
                    $state = $this->getStateDetailsById($data->state_id)['name'];
                    $country = $this->getStateDetailsById($data->state_id)['country'];
                endforeach;

                return array(
                    "name" => $name,
                    "state" => $state,
                    "country" => $country
                );
            }
            return array();
        } catch (Exception $ex) {
            
        }
    }

    /* email existance */

    public function is_email_valid($emailAddr) {

        try {

            $email = $emailAddr;

            $users = TableRegistry::get('tbl_users');

            $results = $users->find()->where(["email" => $email]);

            if ($results->count() > 0) {

                return true;
            } else {

                return false;
            }
        } catch (Exception $ex) {
            
        }
    }

    /* generate random string */

    function generateRandomString($length = 5) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /* get path of vendor folder */

    public function getVendorPath($vendorFolderName) {
        try {

            return ROOT . DS . 'vendor' . DS . $vendorFolderName . DS;
        } catch (Exception $ex) {
            
        }
    }

    /* get user detail */

    public function getTableSingleDataByValue($tableName, $colName, $colValue, $fetchWhat) {

        $tableFound = TableRegistry::get("$tableName");

        try {
            $results = $tableFound->find()->where(["$colName" => $colValue]);

            $user_details = array();

            if ($results->count() > 0) {

                foreach ($results as $data):
                    array_push($user_details, array("$fetchWhat" => $data->$fetchWhat));
                endforeach;
            }

            return $user_details;
        } catch (Exception $ex) {

            return $user_details;
        }
    }

    /* get user token by user_id */

    public function getUserIdByToken($token) {

        try {

            $usr_token_tbl = TableRegistry::get('tbl_userlive_tokens');

            $total_rows = $usr_token_tbl->find('all', [
                'conditions' => ['token' => $token]
            ]);

            $user_details = array();

            if ($total_rows->count() > 0) {
                foreach ($total_rows as $row) {
                    array_push($user_details, array(
                        "token" => $token,
                        "user_id" => $row->userid,
                        "id" => $row->id,
                        "token_created" => $row->token_created
                    ));
                }
            }
            return $user_details;
        } catch (Exception $ex) {
            
        }
    }

    /* post request via cURL */

    public function pc_post($username, $password, $main_api_url, $curl_url, $data_string, $additional = array()) {
        try {

            $FinalURL = $main_api_url . $curl_url;
            $ch = curl_init($FinalURL);
            $location_id = isset($additional['location_id']) ? intval($additional['location_id']) : 0;
            if ($location_id > 0) {
                $headers = array("Content-Length: " . strlen($data_string), "location_id: " . $location_id);
            } else {
                $headers = array("Content-Length: " . strlen($data_string));
            }

            $contentType = isset($additional['contentType']) ? trim($additional['contentType']) : "";
            if ($contentType != '') {
                array_push($headers, $contentType);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            if ($username != "" && $password != "") {
                curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            }
            $Val = curl_exec($ch);

            return $Val;
        } catch (Exception $ex) {
            
        }
    }

    public function pc_put($username, $password, $main_api_url, $curl_url, $data_string, $additional = array()) {
        $url = $main_api_url . $curl_url;
        $ch = curl_init();
        $location_id = isset($additional['location_id']) ? intval($additional['location_id']) : 0;

        if ($location_id > 0) {
            $headers = array("Content-Length: " . strlen($data_string), "location_id: " . $location_id);
        } else {
            $headers = array("Content-Length: " . strlen($data_string));
        }

        $contentType = isset($additional['contentType']) ? trim($additional['contentType']) : "";
        if ($contentType != '') {
            array_push($headers, $contentType);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function pc_delete($username, $password, $main_api_url, $curl_url, $additional = array()) {
        $FinalURL = $main_api_url . $curl_url;

        $location_id = isset($additional['location_id']) ? intval($additional['location_id']) : 0;
        if ($location_id > 0) {
            $headers = array("location_id: " . $location_id);
        } else {
            $headers = array();
        }
        $contentType = isset($additional['contentType']) ? trim($additional['contentType']) : "";
        if ($contentType != '') {
            array_push($headers, $contentType);
        }

        $ch = curl_init($FinalURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if ($username != "" && $password != "") {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }
        return $result_on = curl_exec($ch);
    }

    function pc_get($username, $password, $main_api_url, $curl_url, $additional = array()) {
        $FinalURL = $main_api_url . $curl_url;
        $location_id = isset($additional['location_id']) ? intval($additional['location_id']) : 0;
        if ($location_id > 0) {
            $headers = array("location_id: " . $location_id);
        } else {
            $headers = array();
        }

        $contentType = isset($additional['contentType']) ? trim($additional['contentType']) : "";
        if ($contentType != '') {
            array_push($headers, $contentType);
        }
        $ch = curl_init($FinalURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if ($username != "" && $password != "") {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }
        return $result_on = curl_exec($ch);
    }

    public function fully_trim($str) {
        return rtrim(str_replace(array('http://', 'https://', 'www.'), "", $str), '/\\');
    }

    /* get Agency Account Type */

    public function getAgencyAccountType($agency_id) {

        try {

            $usr_token_tbl = TableRegistry::get('tbl_agency_types');

            $results = $usr_token_tbl->find('all', [
                        'conditions' => ['id' => $agency_id]
                            ], ['fields' => 'type'])->toArray();


            if (count($results) > 0) {

                return $results[0]->type;
            } else {

                return '';
            }
        } catch (Exception $ex) {
            
        }
    }

    public function smartcURL_get($url) {

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            return $result_on = curl_exec($ch);
        } catch (Exception $ex) {
            
        }
    }

    /* cURL Post */

    public function smartcURL_post($url, $data) {

        try {
            /*
             * url: Hit URL,
             * data: $data = json_encode($post_data); data should be json encoded
             */

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
                "Content-Length:" . strlen($data)));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $Val = curl_exec($ch);

            return $Val;
        } catch (Exception $ex) {
            
        }
    }

    function topercent($Num, $NDecimals = 2, $DivBy = 100.0) {

        return number_format($this->tofloatnum($Num) / $DivBy, $NDecimals) . '%';
    }

    function tofloatnum($Num) {

        return !empty($Num) ? floatval($Num) : 0.0;
    }

    function moneyfrmmicros($Amount) {

        $microval = 1000000.0;
        return $Amount / $microval;
    }

    function formattomney($Num, $NDecimals = 2) {

        return ($Num >= 0 ? '' : '-') . '$' . number_format(abs($Num), $NDecimals);
    }

    public function getUserDetails($user_id) {

        $usr_location = TableRegistry::get('tbl_users');

        $total_rows = $usr_location->find('all', [
                    'conditions' => ['id' => $user_id]
                ])->toArray();

        return $total_rows;
    }

    public function ToFloat($Num) {
        return !empty($Num) ? floatval($Num) : 0.0;
    }

    public function FormatFloat($Num, $NDecimals = 2) {
        return number_format($Num, $NDecimals);
    }

    public function PerSentFormat($Num, $NDecimals = 2, $DivBy = 100.0) {
        return number_format($this->ToFloat($Num) / $DivBy, $NDecimals);
    }

    public function SecondsToMinSec($Seconds) {
        return strval(intval($Seconds / 60)) . ':' . strval($Seconds % 60);
    }

    public function FormatMoney($Num, $NDecimals = 2) {
        return ($Num >= 0 ? '' : '-') . '$' . number_format(abs($Num), $NDecimals);
    }

    public function getLocationByUserId($user_id) {

        try {

            $usr_location = TableRegistry::get('tbl_locations');

            $row = $usr_location->find('all', [
                        'conditions' => ['created_by' => $user_id],
                        'order' => ['created' => 'ASC'],
                        'limit' => 1
                    ])->first();
            $location_details = array();

            if (count($row) > 0) {
                array_push($location_details, array(
                    "id" => $row->id,
                    "agency_id" => $row->agency_id,
                    "account_type" => $row->account_type,
                    "website" => $row->website,
                    "name" => $row->name,
                    "services" => $row->services,
                    "target" => $row->target,
                    "email" => $row->email,
                    "phone" => $row->phone,
                    "about" => $row->about,
                    "country_id" => $row->country_id,
                    "address" => $row->address,
                    "zip_code" => $row->zip_code,
                    "conv_verified" => $row->conv_verified
                ));
            }

            return $location_details;
        } catch (Exception $ex) {
            
        }
    }

    /* function runs process in background */

    public function silent_post($params, $remote_url) {

        $url = trim($remote_url);

        $post_string = $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);

        $is_https = 0;
        $httpscnt = explode("https", strtolower(trim($url)));
        if (count($httpscnt) > 1) {
            $is_https = 1;
        }

        if ($is_https == 1 || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);

        $res = curl_exec($ch);
        curl_close($ch);
    }

    /* function to make error log in db table entry */

    public function logSmartError($params = array()) {

        $usr_log_tbl = TableRegistry::get('tbl_error_log');

        $user_log = $usr_log_tbl->newEntity();
        $user_log->location_id = isset($params['location_id']) ? intval($params['location_id']) : 0;
        $user_log->error_type = isset($params['error_type']) ? trim($params['error_type']) : "";
        $user_log->file = isset($params['file']) ? trim($params['file']) : "";
        $user_log->url = isset($params['url']) ? trim($params['url']) : "";
        $user_log->function = isset($params['function']) ? trim($params['function']) : "";
        $user_log->message = isset($params['message']) ? trim($params['message']) : "";
        $user_log->full_exception = isset($params['full_exception']) ? trim($params['full_exception']) : "";
        $user_log->browser = isset($params['browser']) ? trim($params['browser']) : "";
        $user_log->os = isset($params['os']) ? trim($params['os']) : "";
        $user_log->created = date("Y-m-d H:i:s");

        if ($usr_log_tbl->save($user_log)) {
            return true;
        }
        return false;
    }

    /* function to check is token is valid or not */

    public function is_token_valid($token = '') {
        if ($token == '') {
            $token = $this->request->header('token');
        }

        if (empty($token)) {
            return false;
        }
        $usr_token_tbl = TableRegistry::get('tbl_userlive_tokens');
        $res = $usr_token_tbl->findByToken($token)->first();

        if (!empty($res)) {

            $exp = date("Y-m-d H:i:s", strtotime($res->token_expire));
            $now = date("Y-m-d H:i:s");
            if ($now > $exp) {
                return false;
            }

            // refresh token, if usre logged in
            $usrtoken = $usr_token_tbl->newEntity();
            $usrtoken->token_expire = date("Y-m-d H:i:s", strtotime("+" . token_expire_days . " days"));
            $usrtoken->id = $res->id;
            $usr_token_tbl->save($usrtoken);

            return true;
        }
        return false;
    }

    public function provideSortedDataPoint($ispecent, $data = array()) {

        $low = $data['low'];
        $avg = $data['avg'];
        $high = $data['high'];
        $you = $data['you'] != NULL ? $data['you'] : 0;
        $eqlTo = '';
        if ($you == $low) {
            $eqlTo = "low";
            //$eqlTo = $low;
            //$you = $low * (0.1) + $low;
        } else if ($you == $avg) {
            $eqlTo = "avg";
            //$eqlTo = $avg;
            //$you = $low * (0.1) + $low;
        } else if ($you == $high) {
            $eqlTo = "high";
            //$eqlTo = $high;
            //$you = $low - $low * (0.1);
        }

        $largenum = max(array(intval($low), intval($avg), intval($you), intval($high)));

        $lowpercent = round((intval($low) / $largenum) * 100);
        $avgpercent = round((intval($avg) / $largenum) * 100);
        $highpercent = round((intval($high) / $largenum) * 100);

        $youcent = round((intval($you) / $largenum) * 100);
        //        if($lowpercent == $youcent){
//            $youcent = $lowpercent + 2;
//        }
//        if($avgpercent == $youcent){
//            $youcent = $avgpercent + 2;
//        }
//        if($highpercent == $youcent){
//            $youcent = $highpercent - 2;
//        }


        $dataPointArray = array(
            array("key" => "low", "value" => $low, "percent" => $lowpercent, 'class' => 'red'),
            array("key" => "avg", "value" => $avg, "percent" => $avgpercent, 'class' => 'orange'),
            array("key" => "you", "value" => $you, "percent" => $youcent, 'class' => 'green', "equalTo" => $eqlTo),
            array("key" => "high", "value" => $high, "percent" => $highpercent, 'class' => 'blue')
        );

        // sorting array by value
        usort($dataPointArray, function($a, $b) {
            return $a['value'] - $b['value'];
        });

        $newar = array();
        $firstval = 0;
        foreach ($dataPointArray as $dataPoint) {
            $val = $dataPoint['value'];
            unset($dataPoint['value']);
            $newval = '';
            if ($ispecent == 1) {
                $newval = $val . '%';
            } else if ($ispecent == 2) {
                $newval = $this->convertMinutuesSecondsFormat($val);
            } else {
                $newval = $val;
            }
            $dataPoint['value'] = $newval;
            if ($firstval == 0) {
                $val = $dataPoint['percent'];
                unset($dataPoint['percent']);
                $dataPoint['percent'] = 0;
            }
            $newar[] = $dataPoint;
            $firstval++;
        }
        return $newar;
    }

    /* function to find the page id by page url from cre table */

    public function findPageURLByPageIdCRE($pageId, $location_id) {
        try {

            $cre_urls = TableRegistry::get('tbl_cre_urls');

            $query = $cre_urls->query();
            $page_id = 0;

            $total_rows = $query->find('all', [
                        'conditions' => ['location_id' => $location_id, "id" => $pageId]
                    ])->toArray();

            // $trimurl = trim(trim(str_replace(array('http://', 'https://', 'www.'), array('', '', ''), $url)), "/");

            $location_details = array();

            if (count($total_rows) > 0) {

                // finding page url  by id
                foreach ($total_rows as $index => $urls) {

                    array_push($location_details, array(
                        "page_url" => $urls->url
                    ));
                }
            }

            if ($pageId > 0) {
                // updating page status by page_id 
                $query->update()
                        ->set(
                                [
                                    'is_running' => 1,
                                    'modified' => date("Y-m-d H:i:s")
                                ]
                        )
                        ->where(['location_id' => $location_id, 'id' => $pageId])
                        ->execute();
            }

            return $location_details;
        } catch (Exception $ex) {
            
        }
    }

    public function countURLsRunningPages($location_id = '') {

        $cre_urls = TableRegistry::get('tbl_cre_urls');

        $query = $cre_urls->query();
        $page_id = 0;

        if (!empty($location_id)) {
            $total_rows = $query->find('all', [
                        'conditions' => ['location_id' => $location_id, "is_running" => 1]
                    ])->toArray();
        } else {
            $total_rows = $query->find('all', [
                        'conditions' => ["is_running" => 1]
                    ])->toArray();
        }

        return $total_rows;
    }

    /* function to get location url by location id */

    public function getLocationUrlById($location_id) {

        try {

            $usr_location = TableRegistry::get('tbl_locations');

            $total_rows = $usr_location->find('all', [
                'conditions' => ['id' => $location_id]
                    ], ["fields" => "website"]);


            if ($total_rows->count() > 0) {

                foreach ($total_rows as $data) {
                    return $data->website;
                }
            }
            return '';
        } catch (Exception $ex) {
            
        }
    }

    public function getlastIdOfCREUrls($location_id) {

        $cre_urls = TableRegistry::get('tbl_cre_urls');

        $result = $cre_urls->find('all', array('conditions' => array('location_id' => $location_id), 'order' => array('id' => 'DESC')))->first();

        return $result->id;
    }

    /* get cre page urls */

    public function getCREPageUrls($location_id, $offset) {

        try {

            $limit = BATCH_CRE_SCAN;
            
            $usr_location = TableRegistry::get('tbl_cre_urls');
            $page_last_index = TableRegistry::get('tbl_cre_queue');
            $query_last_index = $page_last_index->query();

            $total_rows = $usr_location->find('all', [
                'conditions' => ['location_id' => $location_id, 'is_running' => 1],
                'limit' => $limit,
                'offset' => $offset
            ]);

            $page_ids = array();

            $urls = array();
            if ($total_rows->count() > 0) {

                foreach ($total_rows as $index => $data) {
                    array_push($urls, array("page_url" => $data->url, "page_id" => $data->id));
                    array_push($page_ids, $data->id);
                }
                $query_last_index->update()
                        ->set(
                                [
                                    'current_last_id' => $page_ids[count($page_ids) - 1]
                                ]
                        )
                        ->where(['location_id' => $location_id])
                        ->execute();
                // array_push($urls, array("pageIds" => $page_ids));
            }
            return array("urls" => $urls, "pageIds" => $page_ids);
        } catch (Exception $ex) {
            
        }
    }

    public function getSilentPostPath() {

        return BASE_PATH_URL;
    }

    /* give you the details of token like user_id, location_id */

    public function fetchTokenDetails($token = "") {
        if ($token == "") {
            $token = $this->request->header('token');
        }
        if ($token == "") {
            return $this->json(0, "Token is empty", array("token" => "required"));
        }

        $query = "SELECT t.id, t.token, u.id as user_id, u.fname, u.lname, u.email, ut.type, ut.code, l.id as location_id, l.name, l.website, "
                . "l.agency_id FROM tbl_userlive_tokens t INNER JOIN tbl_users u ON t.userid = "
                . "u.id INNER JOIN tbl_utypes ut ON u.utype_id = ut.id LEFT JOIN tbl_locations l "
                . "ON t.location_id = l.id WHERE t.token = :token";
        $data = $this->connection->execute($query, ['token' => $token])->fetch('assoc');

        if (!empty($data)) {

            return array(
                "fname" => $data['fname'],
                "lname" => $data['lname'],
                "email" => $data['email'],
                "usertype" => $data['type'],
                "code" => $data['code'],
                "user_id" => $data['user_id'],
                "agency_id" => $data['agency_id'],
                "location_id" => $data['location_id'] != '' ? $data['location_id'] : 0,
                "website" => $data['website'],
                "name" => $data['name'],
                "token_id" => $data['id'],
                "token" => $data['token']
            );
        }
        return $this->json(0, "invalid token");
    }

    function appendhttp($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = trim($url, "/");
            $url = "http://" . $url;
        }
        return $url;
    }

    public function checkgaconnect($location_id) {
        try {
            $gaconnected = 0;
            $response = $this->pc_post("", "", API_ENGINE_URI, "/getgatoken", "", array('location_id' => $location_id));
            $response = json_decode($response);
            if (isset($response->sts) && $response->sts == 1) {
                if (isset($response->arr->google_token) && $response->arr->google_token != '') {
                    $gaconnected = 1;
                }
            } return $gaconnected;
        } catch (Exception $ex) {
            
        }
    }

    public function notifyamin($subject, $message) {

        $mail = @mail(ADMIN_EMAIL, $subject, $message);
    }

    // Function to get the client IP address
    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function website_format($website) {

        $website = rtrim($website, '/\\');

        $website = str_replace(array('http://', 'https://'), "", $website);

        return $website;
    }

    public function get_user_meta($location_id, $option_key) {

        $tbl_options = TableRegistry::get('tbl_options');
        $tbl_options_query = $tbl_options->query();

        $total_rows = $tbl_options_query->find('all', [
                    'conditions' => ['location_id' => $location_id, "option_key" => $option_key]
                ])->toArray();

        $option_value = '';

        if (count($total_rows) > 0) {

            $option_value = $total_rows[0]['option_value'];
        }

        return $option_value;
    }

    public function add_user_meta($location_id, $option_key, $option_value) {

        $tbl_options = TableRegistry::get('tbl_options');
        $tbl_options_query = $tbl_options->query();

        if (empty($this->get_user_meta($location_id, $option_key))) {
            if ($tbl_options_query->insert(['location_id', 'option_key', 'option_value', 'created'])
                            ->values(
                                    [
                                        'location_id' => $location_id,
                                        'option_key' => $option_key,
                                        'option_value' => $option_value,
                                        'created' => date("Y-m-d H:i:s")
                                    ]
                            )
                            ->execute()) {
                return true;
            }
            return false;
        }
        return false;
    }

    function convertMinutuesSecondsFormat($seconds) {
        $remainder = $seconds % 60;
        $quotient = ($seconds - $remainder) / 60;

        return round($quotient, 2) . "m" . $remainder . "s";
    }

    public function update_user_meta($location_id, $option_key, $option_value) {

        $tbl_options = TableRegistry::get('tbl_options');
        $tbl_options_query = $tbl_options->query();

        if (!empty($this->get_user_meta($location_id, $option_key))) {

            if ($tbl_options_query->update()
                            ->set(
                                    [
                                        'option_value' => $option_value,
                                        'updated' => date("Y-m-d H:i:s")
                                    ]
                            )
                            ->where(['location_id' => $location_id, "option_key" => $option_key])
                            ->execute()) {
                return true;
            } else {

                return false;
            }
        }
        return false;
    }

    function urlexist($url) {
        try {
            $exists = false;

            if (!$exists && in_array('curl', get_loaded_extensions())) {

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_exec($ch);

                $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($response != 404) {
                    $exists = true;
                }

                curl_close($ch);
            }

            return $exists;
        } catch (Exception $e) {
            $message = $e->getMessage();
            //mail('parambir@rudrainnovatives.com','URL Check Error. Function urlexist',$message);
            return true;
        }
    }

    // delete from placesscout
    public function delete_rank_report($ranking_id) {
        $placesscout_api_info = placesscout_api_info();
        $username = $placesscout_api_info['username'];
        $password = $placesscout_api_info['password'];
        $main_api_url = $placesscout_api_info['main_api_url'];
        $curl_url = 'rankingreports/' . $ranking_id;
        $this->pc_delete($username, $password, $main_api_url, $curl_url);
        return 1;
    }

    public function has_duplicate_values($array) {
        $dupe_array = array();
        foreach ($array as $val) {
            if (++$dupe_array[$val] > 1) {
                return true;
            }
        }
        return false;
    }

    public function ChangeDateFormat($date) {
        return date_format($date, 'jS F Y g:ia');
    }

    public function bucket_name($CurrentRank) {
        $current_bucket = '';
        if ($CurrentRank == '0') {
            $current_bucket = '50+';
        } else if ($CurrentRank > 10 && $CurrentRank <= 50) {
            $current_bucket = '11-50';
        } else if ($CurrentRank >= 4 && $CurrentRank <= 10) {
            $current_bucket = '4-10';
        } else if ($CurrentRank >= 1 && $CurrentRank <= 3) {
            $current_bucket = 'Top 3';
        }
        return $current_bucket;
    }

    public function trimUrl($url) {

        return $trimurl = trim(trim(str_replace(array('http://', 'https://', 'www.'), array('', '', ''), $url)), "/");
    }

    public function doPercentage($numerator, $denominator) {
        return sprintf("%.2f", ($numerator / $denominator) * 100);
    }

    function semrush_api_info() {
        $sm_api['key'] = "562e2601cd42d050497232b4b6510a31";
        $sm_api['main_api_url'] = "http://api.semrush.com/";
        $sm_api['semrush_project_url'] = "http://api.semrush.com/reports/v1/projects/";
        return $sm_api;
    }

    /* PLACESCOUT PARAMETERS */

    function placesscout_api_info() {
        $pc_api['username'] = "rbryan";
        $pc_api['password'] = "TG7RN5XvKaJvt9UiAKYpAy8";
        $pc_api['main_api_url'] = "https://apihost1.placesscout.com/";
        return $pc_api;
    }

    function semrush_limit() {
        $limit = semrush_limit;
        return $limit;
    }

    public function is_valid_url($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return true;
        } else {
            return false;
        }
    }

    public function market_values() {
        // need to be dynamic later
        $jsonvalues = json_encode(["kv" => "15", "cre" => "40", "sa" => "35", "ca" => "10"]); // get from DB
        $arr = json_decode($jsonvalues);
        return $arr;
    }

    public function get_option($agency_id, $option_key) {

        $tbl_options = TableRegistry::get('tbl_options');
        $tbl_options_query = $tbl_options->query();

        $total_rows = $tbl_options_query->find('all', [
                    'conditions' => ['agency_id' => $agency_id, "option_key" => $option_key]
                ])->toArray();

        $option_value = '';

        if (count($total_rows) > 0) {

            $option_value = $total_rows[0]['option_value'];
        }

        return $option_value;
    }

    public function set_option($agency_id, $option_key, $option_value) {

        $tbl_options = TableRegistry::get('tbl_options');
        $tbl_options_query = $tbl_options->query();

        if (empty($this->get_user_meta($location_id, $option_key))) {
            if ($tbl_options_query->insert(['agency_id', 'option_key', 'option_value', 'created'])
                            ->values(
                                    [
                                        'agency_id' => $agency_id,
                                        'option_key' => $option_key,
                                        'option_value' => $option_value,
                                        'created' => date("Y-m-d H:i:s")
                                    ]
                            )
                            ->execute()) {
                return true;
            }
            return false;
        }
        return false;
    }
    
    public function is_dbtable_exists($tableName) {

        $results = $this->connection
                ->execute("SHOW TABLES LIKE '" . $tableName . "'")
                ->fetchAll('assoc');

        if (count($results) > 0) {

            return true;
        } else {
            return false;
        }
    }

}
