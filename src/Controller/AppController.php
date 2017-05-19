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
    public function initialize() {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
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
    }

    /* get state details by id */

    function getStateDetailsById($id) {

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
    }

    /* get city details by id */

    function getCityDetailsById($id) {

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
    }

    /* email existance */

    public function is_email_valid($emailAddr) {

        $email = $emailAddr;

        $users = TableRegistry::get('tbl_users');

        $results = $users->find()->where(["email" => $email]);

        if ($results->count() > 0) {

            return true;
        } else {

            return false;
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
        return ROOT . DS . 'vendor' . DS . $vendorFolderName . DS;
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
    }

    /* post request via cURL */

    public function pc_post($username, $password, $main_api_url, $curl_url, $data_string) {

        $FinalURL = $main_api_url . $curl_url;
        $ch = curl_init($FinalURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
            "Content-Length:" . strlen($data_string)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if ($username != "" && $password != "") {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }
        $Val = curl_exec($ch);
        return $Val;
    }

    public function fully_trim($str) {
        return rtrim(str_replace(array('http://', 'https://', 'www.'), "", $str), '/\\');
    }

    /* get request via cURL */

    public function pc_get($username, $password, $main_api_url, $curl_url) {
        $FinalURL = $main_api_url . $curl_url;
        $ch = curl_init($FinalURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if ($username != "" && $password != "") {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }
        return $result_on = curl_exec($ch);
    }

}
