<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PDO;

class MigrationController extends AppController {

    private $app;
    private $smartdata;
    private $conn;
    private $smart;

    public function initialize() {
        parent::initialize();
        ini_set('max_execution_time', 90000000000);
        $this->app = new AppController();
        $this->loadModel('Apilocations');
        $this->smartdata = "smart";
        $this->conn = ConnectionManager::get('default');
        $this->smart = ConnectionManager::get($this->smartdata);
    }

    /* default called method */

    public function index() {
        $this->autoRender = false;
        $this->json(1, array(
            "method" => "index",
            "messge" => "silence is golden"
        ));
    }
    public function migrateAgency() {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
       $smart = TableRegistry::get('tbl_agencies');
       $db = new PDO('mysql:dbname=enfusen_mcc_new;host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92');
       $i = 1;
       foreach ($database as $new) { 
           $result =$db->query("SELECT * from wp_setup_table WHERE db_name = '$new'");
           $data = $result->fetchAll(); 
          $logo = '';
          $pdf_logo = ''; 
    $path =  $data[0]['white_lbl'].'/wp-content/plugins/settings/uploads/pdf_logo.jpg';
        if($this->checkRemoteFile($path)) {
         file_put_contents(WWW_ROOT . 'uploads/'.$i.'.jpg' , file_get_contents($path));
         $pdf_logo =  $i.'.jpg';
            } 
        $logo_path = $data[0]['white_lbl'].'/wp-content/plugins/settings/uploads/logo.png';
        if($this->checkRemoteFile($logo_path)) {
       file_put_contents(WWW_ROOT . 'uploads/'.$i.'.png' , file_get_contents($logo_path));
        $logo = $i.'.png';
            }
            $query = $smart->query();
            $result = $query->insert(['name', 'email', 'prefix', 'url', 'white_lbl', 'logo','pdf_logo','status', 'created', 'created_by', 'modified', 'modified_by'])
                    ->values(
                            [
                                'name' => $data[0]['name'],
                                'email' => $data[0]['email'],
                                'prefix' => $data[0]['prefix'],
                                'url' => $data[0]['url'],
                                'white_lbl' => $data[0]['white_lbl'],
                                'logo' => $logo,
                                'pdf_logo' => $pdf_logo,
                                'status' => $data[0]['status'],
                                'created' => $data[0]['created_dt'],
                                'created_by' => $data[0]['created_by'],
                                'modified' => $data[0]['updated_dt'],
                                'modified_by' => $data[0]['updated_by'],
                            ]
                    )
                    ->execute();
                    $i++;
        }
        echo "success";
        die;
    }
     public function checkRemoteFile($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            // don't download content
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if(curl_exec($ch)!==FALSE)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
  public function migrateLocation() {
          try {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
        $smart = TableRegistry::get('tbl_locations');
       foreach ($database as $new) {
        $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT MCCUserId FROM `wp_client_location`");
        if($new = 'mcc_mcc'){
           $res = $result->fetchAll(); 
           $con1 = array("MCCUserId" =>"652");
           $con2 = array("MCCUserId" =>"1473");
           $con3 = array("MCCUserId" =>"1529");
           $new = array("24" => $con1, "25" => $con2, "26" => $con3);
           $data = array_merge($res, $new);
        }else{
        $data = $result->fetchAll(); 
        }
        foreach ($data as $key){
          $user_id = $key['MCCUserId'];
          $result = $db->query("SELECT * FROM `wp_usermeta` WHERE user_id = '$user_id'");
           $new = $result->fetchAll();
             $website = '';
             $name = '';
             $phone = '';
             $country = '';
             $state = '';
             $city = '';
             $address = '';
             $zip_code = '';
           foreach ($new as $ab){
            if ($ab['meta_key'] == 'website'){
                $website = $ab['meta_value'];
            }
            if ($ab['meta_key'] == 'BRAND_NAME'){
                $name = $ab['meta_value'];
            }
            if ($ab['meta_key'] == 'phonenumber'){
                $phone = $ab['meta_value'];
             } 
            if ($ab['meta_key'] == 'country'){
                $country = $ab['meta_value'];
              }
            if ($ab['meta_key'] == 'state'){
                $state = $ab['meta_value'];
               }
            if ($ab['meta_key'] == 'city'){
                $city = $ab['meta_value'];
                }
            if ($ab['meta_key'] == 'streetaddress'){
                $address = $ab['meta_value'];
            }
            if ($ab['meta_key'] == 'zip'){
                $zip_code = $ab['meta_value']; 
            }
           }
           $info = $db->query("SELECT * FROM `wp_client_location` WHERE MCCUserId = '$user_id'");
           $qa = $info->fetchAll();
           $status = $qa[0]['status'];
           $created_by = $qa[0]['created_by'];
           $created = $qa[0]['created_dt'];
           $modified = $qa[0]['updated_dt'];
            $query = $smart->query();
            $result = $query->insert(['website', 'name', 'phone', 'country_id', 'state_id', 'city_id', 'address', 'zip_code', 'status', 'created', 'created_by', 'modified'])
                    ->values(
                            [
                                'website' => $website,
                                'name' => $name,
                                'phone' => $phone,
                                'country_id' => $country,
                                 'state_id' => $state,
                                 'city_id' => $city,
                                 'address' => $address,
                                 'zip_code' => $zip_code,
                                'status' => $status,
                                'created' => $created,
                                'created_by' => $created_by,
                                'modified' => $modified,
                            ]
                    )
                    ->execute();
        }  
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }
    public function migrateLocationMapping() {
         try {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
        foreach ($database as $new){
        $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT * FROM `wp_location_mapping` ");
        $data = $result->fetchAll(); 
        $smart = TableRegistry::get('tbl_location_mapping');
        foreach ($data as $new) {
             $query = $smart->query();
             $result = $query->insert(['location_id', 'user_id', 'created', 'modified'])
                    ->values(
                            [ 
                                'location_id' => $new['location_id'],
                                'user_id' => $new['user_id'],
                                'created' => $new['created_dt'],
                                'modified' => $new['updated_dt'],
                            ]
                    )
                    ->execute();
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }

   public function migrateUser() {
         try {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
        foreach ($database as $new){
        $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $name = $db->query("SELECT MCCUserId FROM `wp_client_location`");
        if($new = 'mcc_mcc'){
           $res = $name->fetchAll(); 
           $con1 = array("MCCUserId" =>"652");
           $con2 = array("MCCUserId" =>"1473");
           $con3 = array("MCCUserId" =>"1529");
           $new = array("24" => $con1, "25" => $con2, "26" => $con3);
           $user_id = array_merge($res, $new);
        } else{
        $user_id = $name->fetchAll(); 
        }
        $result = $db->query("SELECT * FROM `wp_users` WHERE user_status = 1");
        $data = $result->fetchAll(); 
        $smart = TableRegistry::get('tbl_users');
        foreach ($data as $new) {
        $id = $new['id'];
          foreach($user_id as $key){
             $user_id = $key['MCCUserId'];
             if($user_id == $id){
              continue;
             }
          }
            $query = $smart->query();
            $result = $query->insert(['utype_id', 'email', 'password', 'fname', 'verify_code', 'status', 'created'])
                    ->values(
                            [ 
                                'utype_id' => 1,
                                'email' =>  $new['user_login'],
                                'password' =>  $new['user_pass'],
                                'fname' =>  $new['display_name'],
                                'verify_code' =>  $new['user_activation_key'],
                                'status' =>  $new['user_status'],
                                'created' =>  $new['user_registered'],
                            ]
                    )
                    ->execute();
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }

  // public function migrateSiteAudit() {
  //        try {
  //       $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
  //       foreach ($database as $new){
  //       $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
  //       $result = $db->query("SELECT * FROM `wp_site_audit` ");
  //       $data = $result->fetchAll(); 
  //       $smart = TableRegistry::get('tbl_site_audit');
  //       foreach ($data as $new) {
  //           $user_id = $new['user_id'];
  //           $query = $smart->query();
  //           $result = $query->insert(['location_id', 'campaign_id', 'snapshot_id', 'audit_status', 'all_info', 'error_name_list', 'rerun', 'last_audit'])
  //                   ->values(
  //                           [
  //                               'location_id' =>  $new['user_id'],
  //                               'campaign_id' =>  $new['campaign_id'],
  //                               'snapshot_id' =>  $new['snapshot_id'],
  //                               'audit_status' =>  $new['audit_status'],
  //                               'all_info' =>  $new['all_info'],
  //                               'error_name_list' =>  $new['error_name_list'],
  //                               'rerun' =>  $new['rerun'],
  //                               'last_audit' =>  $new['last_audit'],
  //                           ]
  //                   )
  //                   ->execute();
  //           $this->conn->query("CREATE TABLE db1.tbl_site_audit_error_page_list_$location_id AS (SELECT * FROM db2.table)");
  //       }
  //       }    
  //       echo "success";
  //       die;
  //       } catch (PDOException $ex) {
  //         echo 'Connection failed: ' . $ex->getMessage();
  //       }
  //   }  
  
   public function migrateSiteAudit() {
         try {
          $agencies = TableRegistry::get('tbl_agencies');            
          $database = $agencies->find('all')->select(['id','old_db'])->all();
          $temptable = TableRegistry::get('tbl_temp');    
          $smart = TableRegistry::get('tbl_site_audit');
        foreach ($database as $dbdata){
        $db_name = $dbdata->old_db;
        $db = new PDO('mysql:dbname='.$db_name.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT * FROM `wp_site_audit` ");
        $data = $result->fetchAll(); 
        foreach ($data as $new) {
            $user_id = $new['user_id'];
            $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' =>  $db_name])->first(); 
            if(!empty($loc_data)) {
            $location_id = $loc_data->loc_id;
            $query = $smart->query();
            $result = $query->insert(['location_id', 'campaign_id', 'snapshot_id', 'audit_status', 'all_info', 'error_name_list', 'rerun', 'last_audit'])
                    ->values(
                            [
                                'location_id' =>  $location_id,
                                'campaign_id' =>  $new['campaign_id'],
                                'snapshot_id' =>  $new['snapshot_id'],
                                'audit_status' =>  $new['audit_status'],
                                'all_info' =>  $new['all_info'],
                                'error_name_list' =>  $new['error_name_list'],
                                'rerun' =>  $new['rerun'],
                                'last_audit' =>  $new['last_audit'],
                            ]
                    )
                    ->execute();  
             $this->conn->query("CREATE TABLE smart_agency_new.tbl_site_audit_error_page_list_$location_id SELECT * FROM smart_agency.tbl_site_audit_error_page_list_3");
            die;
            }
          
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }  
  
  public function migrateKeywordResearch(){
         try {
          $agencies = TableRegistry::get('tbl_agencies');            
          $database = $agencies->find('all')->select(['id','old_db'])->all();
          $temptable = TableRegistry::get('tbl_temp');    
          $smart = TableRegistry::get('tbl_keyword_research');
        foreach ($database as $dbdata){
        $db_name = $dbdata->old_db;
        $db = new PDO('mysql:dbname='.$db_name.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT * FROM `keyword_opportunity` ");
        $data = $result->fetchAll(); 
        foreach ($data as $new) {
            $user_id = $new['user_id'];
            $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' =>  $db_name])->first(); 
            if(!empty($loc_data)) {
            $location_id = $loc_data->loc_id;
            $query = $smart->query();
            $result = $query->insert(['location_id', 'campaign_id', 'snapshot_id', 'audit_status', 'all_info', 'error_name_list', 'rerun', 'last_audit'])
                    ->values(
                            [
                                'location_id' =>  $location_id,
                                'keyword' =>  $new['keyword'],
                                'position' =>  $new['position'],
                                'prev_position' =>  $new['prev_position'],
                                'search_volume' =>  $new['search_volume'],
                                'cpc' =>  $new['CPC'],
                                ' url' =>  $new['url'],
                                 'traffic_percent' =>  $new['traffic'],
                                 'traffic_cost' =>  $new['traffic_cost'],
                                  'results' =>  $new['results'],
                                  'trends' =>  $new['trends'],
                                  'status' =>  $new['status'],
                            ]
                    )
                    ->execute();  
            die;
            }
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
  }



 public function migrateCompetitor() {
        try {
          $agencies = TableRegistry::get('tbl_agencies');            
          $database = $agencies->find('all')->select(['id','old_db'])->all();
          $temptable = TableRegistry::get('tbl_temp');    
          $smart = TableRegistry::get('tbl_compititors');
        foreach ($database as $dbdata){
        $db_name = $dbdata->old_db;
        $location_data = $temptable->find("all")->where(['old_agency_db' => $db_name])->toArray();
        $db = new PDO('mysql:dbname='.$db_name.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        foreach ($location_data as $loc_data) {
          $location_id = $loc_data->old_loc_id;
          $new_location_id = $loc_data->loc_id;
          $result = $db->query("SELECT meta_value FROM `wp_usermeta` WHERE user_id = $location_id and meta_key = 'competitor_url'");
           $new = $result->fetchAll();
           if(!empty($new)){
             $url = unserialize($new[0]['meta_value']);
           foreach($url as  $data) {
            if(!empty($data)) {
            $query = $smart->query();
            $result = $query->insert(['website', 'location_id'])
                    ->values(
                            [
                                'website' => $data,
                                'location_id' => $new_location_id,
                            ]
                      )
                    ->execute();
            }
           }
           }
        }  
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }

  // public function migrateCitation() {
  //        try {
  //         $agencies = TableRegistry::get('tbl_agencies');            
  //         $database = $agencies->find('all')->select(['id','old_db'])->all();
  //         $temptable = TableRegistry::get('tbl_temp');    
  //         $smart = TableRegistry::get('tbl_citations');
  //         $list = TableRegistry::get('tbl_citationlist');
  //       foreach ($database as $dbdata){
  //       $db_name = $dbdata->old_db;
  //       $agency_id = $dbdata->id;
  //       $db = new PDO('mysql:dbname='.$db_name.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
  //       $result = $db->query("SELECT * FROM `wp_citation_list` limit 1");
  //       $new = $result->fetchAll(); 
  //       $tracker = $db->query("SELECT * FROM `wp_citation_tracker` ");
  //       $tracker_data = $tracker->fetchAll(); 
  //           $user_id = $new[0]['userid'];
  //           $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' =>  $db_name])->first(); 
  //           if(!empty($loc_data)) {
  //           $location_id = $loc_data->loc_id;
  //           $query = $smart->query();
  //           $result = $query->insert(['agency_id', 'location_id', 'name', 'url', 'phone', 'address', 'zip_code', 'city_id', 'state_id', 'status'])
  //                   ->values(
  //                           [
  //                               'agency_id' => $agency_id,
  //                               'location_id' => $location_id,
  //                               'name' =>  $new[0]['name'],
  //                               'url' =>  $new[0]['yext_url'],
  //                               'phone' =>  $new[0]['phone'],
  //                               'address' =>  $new[0]['address'],
  //                               'zip_code' =>  $new[0]['zip'],
  //                               'city_id' =>  $new[0]['city'],
  //                               'state_id' =>  $new[0]['state'],
  //                               'status' =>  $new[0]['status'],
  //                           ]
  //                   )
  //                   ->execute();
  //           $lastrecord = $smart->find("all")->select(['id'])->order(['id' => 'desc'])->first();  
  //           $new_citation_id = $lastrecord->id; 
  //           foreach ($tracker_data as $mobile) {
  //             echo 'hi' ; pr($mobile['status']); die;
  //                   $query = $list->query();
  //                   $result = $query->insert(['location_id', 'citation_id', 'reportId', '   status', 'rerun', ' basic_data', 'citations_data', '    competitive_citation', 'listing_reportId', 'listings_data', '   calculate_info', 'last_run'])
  //                           ->values(
  //                                   [
  //                                       'location_id' => $location_id,
  //                                       'citation_id' =>  $new_citation_id,
  //                                       'reportId' =>$mobile['ReportId'],
  //                                       'status' =>$mobile['status'],
  //                                       'rerun' =>$mobile['rerun'],
  //                                       'basic_data' =>$mobile['basic_data'],
  //                                       'citations_data' =>$mobile['citations_data'],
  //                                       'competitive_citation' =>$mobile['competitive_citation'],
  //                                       'listing_reportId' =>$mobile['listing_reportId'],
  //                                       'listings_data' =>$mobile['listings_data'],
  //                                       'calculate_info' =>$mobile['calculate_info'],
  //                                       'last_run' => $mobile['last_run'],
  //                                   ]
  //                           )
  //                           ->execute();
  //           }
  //           die;
  //       }
  //       }    
  //       echo "success";
  //       die;
  //       } catch (PDOException $ex) {
  //         echo 'Connection failed: ' . $ex->getMessage();
  //       }
  //   }  

public function migrateCitation() {
         try {
          $agencies = TableRegistry::get('tbl_agencies');            
          $database = $agencies->find('all')->select(['id','old_db'])->all();
          $temptable = TableRegistry::get('tbl_temp');    
          $smart = TableRegistry::get('tbl_citations');
          $list = TableRegistry::get('tbl_citationlist');
          $competitor_table = TableRegistry::get('tbl_citation_competitor');

            $countrytable = TableRegistry::get('tbl_countries');
            $statetable = TableRegistry::get('tbl_states');
            $citytable = TableRegistry::get('tbl_cities');
        foreach ($database as $dbdata){
        $db_name = $dbdata->old_db;
        $agency_id = $dbdata->id;
     $location_data = $temptable->find("all")->where(['old_agency_db' =>  $db_name])->toArray();
     $db = new PDO('mysql:dbname='.$db_name.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
     foreach($location_data as $loc_data) {
        $location_id = $loc_data->old_loc_id;
        $new_location_id = $loc_data->loc_id;
        $result = $db->query("SELECT * FROM `wp_usermeta` WHERE user_id = $location_id");

        $new = $result->fetchAll();
             $website = '';
             $name = '';
             $phone = '';
             $country = '';
             $state = '';
             $city = '';
             $address = '';
             $zip_code = '';
           foreach ($new as $ab){
            if ($ab['meta_key'] == 'website'){
                $website = $ab['meta_value'];
            }
            if ($ab['meta_key'] == 'phonenumber'){
                $phone = $ab['meta_value'];
             } 
              if ($ab['meta_key'] == 'country') {
                            $country = 231;
                            $countrytxt = $ab['meta_value'];
                            if($countrytxt != ""){
                                $res = $countrytable->find("all")->select(['id'])->where(['LOWER(country)' => strtolower($countrytxt)])->first();
                                if(!empty($res)){
                                    $country = $res->id;
                                }
                            }
                        }
                        if ($ab['meta_key'] == 'state') {
                            $state = $ab['meta_value'];
                            if($state != ""){
                                $res = $statetable->find("all")->select(['id'])->where(['LOWER(name)' => strtolower($state)])->first();
                                if(!empty($res)){
                                    $state = $res->id;
                                }
                            }
                        }
                        if ($ab['meta_key'] == 'city') {
                            $city = $ab['meta_value'];                            
                            if($city != ""){
                                $res = $citytable->find("all")->select(['id'])->where(['LOWER(name)' => strtolower($city)])->first();
                                if(!empty($res)){
                                    $city = $res->id;
                                }
                            }
                        }
            if ($ab['meta_key'] == 'streetaddress'){
                $address = $ab['meta_value'];
            }
            if ($ab['meta_key'] == 'zip'){
                $zip_code = $ab['meta_value']; 
            }
             if ($ab['meta_key'] == 'BRAND_NAME') {
                $name = $ab['meta_value'];
              }
           }
         $insert = $smart->newEntity();
         $insert->agency_id = $agency_id;
         $insert->location_id = $new_location_id;
         $insert->name = $name;
         $insert->url = $website;
         $insert->phone = $phone;
         $insert->address = $address;
         $insert->zip_code = $zip_code;
         $insert->city_id = $city;
         $insert->state_id = $state;
         $insert->country_id = $country;
         $smart->save($insert); 
         $lastrecord = $smart->find("all")->select(['id'])->order(['id' => 'desc'])->first();  
        $new_citation_id = $lastrecord->id; 
        $tracker = $db->query("SELECT * FROM `wp_citation_tracker` WHERE user_id = $location_id");
        $tracker_data = $tracker->fetchAll();
        if(!empty($tracker_data)){
      foreach ($tracker_data as $mobile) {
               $insert = $list->newEntity();
               $insert->location_id = $new_location_id;
               $insert->citation_id  = $new_citation_id;
               $insert->reportId = $mobile['ReportId'];
               $insert->status = $mobile['status'];
               $insert->rerun = $mobile['rerun'];
               $insert->basic_data = $mobile['basic_data'];
               $insert->citations_data = $mobile['citations_data'];
               $insert->competitive_citation = $mobile['competitive_citation'];
               $insert->listing_reportId = $mobile['listing_reportId'];
               $insert->listings_data = $mobile['listings_data'];
               $insert->calculate_info = $mobile['calculate_info'];
               $insert->last_run = $mobile['last_run'];
               $list->save($insert);
       $citation_tracker_id = $mobile['citation_tracker_id'];
        $record = $list->find("all")->select(['id'])->order(['id' => 'desc'])->first();  
        $new_competitor_id = $record->id;
         $competitor = $db->query("SELECT * FROM `wp_citation_competitor` WHERE citation_tracker_id = $citation_tracker_id");
        $competitor_data = $competitor->fetchAll(); 
        foreach ($competitor_data as $abc) {
                $insert = $competitor_table->newEntity();
                $insert->citation_tracker_id =  $new_competitor_id;
                $insert->citationReportCitationsId = $abc['CitationReportCitationsId'];
                $insert->citations = $abc['citations'];
                $competitor_table->save($insert);
                }
         }
       }
     }  
        }   
        echo "success";
        die;
        } catch (\PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }  







  public function migrateKeygroup() {
         try {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
        foreach ($database as $new){
        $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT * FROM `wp_keygroup` ");
        $data = $result->fetchAll(); 
        $smart = TableRegistry::get('tbl_keygroup');
        foreach ($data as $new) {
            $query = $smart->query();
            $result = $query->insert(['campaign_id', 'location_id', 'google_location', '    landing_page', 'live_date', 'home_page', 'resource_page', 'is_target','notes', 'user_id', 'status', 'created', 'modified'])
                    ->values(
                            [
                                'campaign_id' =>  $new['campaign_id'],
                                'location_id' =>  $new['location_id'],
                                'google_location' =>  $new['google_location'],
                                'landing_page' =>  $new['landing_page'],
                                'live_date' =>  $new['live_date'],
                                'home_page' =>  $new['home_page'],
                                'resource_page' =>  $new['resource_page'],
                                'is_target' =>  $new['is_target'],
                                'notes' =>  $new['notes'],
                                'user_id' =>  $new['user_id'],
                                'status' =>  $new['status'],
                                'created' =>  $new['created_dt'],
                                'modified' =>  $new['updated_dt'],
                            ]
                    )
                    ->execute();
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }  
 public function migrateCampaign() {
          try {
            $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
            foreach ($database as $new) {
                $db = new PDO('mysql:dbname=' . $new . ';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92');
                $result = $db->query("SELECT * FROM `wp_campaigns` ");
                $data = $result->fetchAll();    
                $smart = TableRegistry::get('tbl_campaigns');
                $country = TableRegistry::get('tbl_countries');
                foreach ($data as $new) {
                    $tc = $new['target_country'];
                $id = $country->find('all')->where(["placesscout_name" => $tc])->first();
                  $target_country = $id->id;
                    $query = $smart->query();
                    $result = $query->insert(['name', 'target_country', 'local_location', 'location_id', 'user_id', 'status', 'is_running', 'rundate', '  rankdate', 'ranking_id', 'created', 'modified'])
                            ->values(
                                    [
                                        'name' => $new['name'],
                                        'target_country' => $target_country,
                                        'local_location' => $new['local_location'],
                                        'location_id' => $new['location_id'],
                                        'user_id' => $new['user_id'],
                                        'status' => $new['status'],
                                        'is_running' => $new['is_running'],
                                        'rundate' => $new['rundate'],
                                        'rankdate' => $new['rankdate'],
                                        'ranking_id' => $new['ranking_id'],
                                        'created' => $new['created_dt'],
                                        'modified' => $new['updated_dt'],
                                    ]
                            )
                            ->execute();
                }
            }
            echo "success";
            die;
        } catch (PDOException $ex) {
            echo 'Connection failed: ' . $ex->getMessage();
        }
    }



  public function migrateSchedule(){
       try {
          $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
          foreach ($database as $new){
          $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
          $result = $db->query("SELECT * FROM `wp_mcc_sch_settings` ");
          $data = $result->fetchAll(); 
          $smart = TableRegistry::get('tbl_sch_settings');
          foreach ($data as $new) {
              $query = $smart->query();
              pr($new['sch_reportVolume']); die;
              $result = $query->insert(['location_id', 'campaign_id', 'sch_frequency', '    sch_reportVolume', 'sch_outTime', ' sch_type', 'report_type', 'sch_otherConfig','sch_status', 'modified'])
                      ->values(
                              [
                                  'location_id' =>  $new['sch_uId'],
                                  'campaign_id' =>  $new['campaign_id'],
                                  'sch_frequency' =>  $new['sch_frequency'],
                                  'sch_reportVolume' =>  $new['sch_reportVolume'],
                                  'sch_outTime' =>  $new['sch_outTime'],
                                  'sch_type' =>  $new['sch_type'],
                                  'report_type' =>  $new['report_type'],
                                  'sch_otherConfig' =>  $new['sch_otherConfig'],
                                  'sch_status' =>  $new['sch_status'],
                                  'modified' =>  $new['sch_lastUpdated'],
                              ]
                      )
                      ->execute();
          }
          }    
          echo "success";
          die;
          } catch (PDOException $ex) {
            echo 'Connection failed: ' . $ex->getMessage();
          }
  }
  public function migrateScheduleEmail() {
       try {
          $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
          foreach ($database as $new){
          $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
          $result = $db->query("SELECT * FROM `wp_mcc_sch_emails` ");
          $data = $result->fetchAll(); 
          $smart = TableRegistry::get('tbl_sch_emails');
          foreach ($data as $new) {
              $query = $smart->query();
              $result = $query->insert(['sch_id', 'em_emailTo', 'em_status', 'em_lastUpdate'])
                      ->values(
                              [
                                  'sch_id' =>  $new['em_sch_id'],
                                  'em_emailTo' =>  $new['em_emailTo'],
                                  'em_status' =>  $new['em_status'],
                                  'em_lastUpdate' =>  $new['em_lastUpdate'],
                              ]
                      )
                      ->execute();
                      die;
          }
          }    
          echo "success";
          } catch (PDOException $ex) {
            echo 'Connection failed: ' . $ex->getMessage();
          }
  }




 public function migrateKeyword() {
         try {
        $database = array('mcc_medstar', 'mcc_clixsy', 'mcc_reports', 'mcc_garynealon', 'mcc_ciriusmarket', 'mcc_joseponcejr', 'mcc_attwooddigit', 'mcc_njlocalseoex', 'mcc_fifthavenueb', 'mcc_mcc', 'mcc_pmishra');
        foreach ($database as $new){
        $db = new PDO('mysql:dbname='.$new.';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92'); 
        $result = $db->query("SELECT * FROM `wp_keywords` WHERE group_id = 1");
        $data = $result->fetchAll(); 
        $smart = TableRegistry::get('tbl_keywords');
        foreach ($data as $new) {
        $rankdata = json_decode($new['rankdetail']); 
        $results = $rank_report->keywordRankingResults;
            $query = $smart->query();
            $result = $query->insert(['campaign_id', 'location_id', 'google_location', '    landing_page', 'live_date', 'home_page', 'resource_page', 'is_target','notes', 'user_id', 'status', 'created', 'modified'])
                    ->values(
                            [
                                'campaign_id' =>  $new['campaign_id'],
                                'location_id' =>  $new['location_id'],
                                'google_location' =>  $new['google_location'],
                                'landing_page' =>  $new['landing_page'],
                                'live_date' =>  $new['live_date'],
                                'home_page' =>  $new['home_page'],
                                'resource_page' =>  $new['resource_page'],
                                'is_target' =>  $new['is_target'],
                                'notes' =>  $new['notes'],
                                'user_id' =>  $new['user_id'],
                                'status' =>  $new['status'],
                                'created' =>  $new['created_dt'],
                                'modified' =>  $new['updated_dt'],
                            ]
                    )
                    ->execute();
        }
        }    
        echo "success";
        die;
        } catch (PDOException $ex) {
          echo 'Connection failed: ' . $ex->getMessage();
        }
    }  
     public function migrateCre(){
       try {
            $agencies = TableRegistry::get('tbl_agencies');
            $database = $agencies->find('all')->select(['id', 'old_db'])->all();
            $temptable = TableRegistry::get('tbl_temp');
            $smart = TableRegistry::get('tbl_cre');
            $history = TableRegistry::get('tbl_cre_history');
            $urls = TableRegistry::get('tbl_cre_urls');
            foreach ($database as $dbdata) {
                $db_name = $dbdata->old_db;
                $db = new PDO('mysql:dbname=' . $db_name . ';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92');
                // $result = $db->query("SELECT * FROM `wp_content_recommend` ");
                // $data = $result->fetchAll();
                // foreach ($data as $new) {
                //     $user_id = $new['user_id'];
                //     $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' => $db_name])->first();
                //     if (!empty($loc_data)) {
                //     $location_id = $loc_data->loc_id;
                //     $insert = $smart->newEntity();
                //     $insert->type = $new['type'];
                //     $insert->location_id = $location_id;
                //     $insert->crawl_result = $new['crawl_result'];
                //     $insert->trigger_report = $new['trigger_report'];
                //     $insert->rundate = $new['rundate'];
                //     $insert->created = $new['created_dt'];
                //     $insert->modified = $new['updated_dt'];
                //     $smart->save($insert);
                //     die;
                //     }
                // }
                $history = $db->query("SELECT * FROM `cre_history` ");
                $cre_history = $history->fetchAll();
                foreach ($cre_history as $new) {
                    $user_id = $new['user_id'];
                    $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' => $db_name])->first();
                    if (!empty($loc_data)) {
                    $location_id = $loc_data->loc_id;
                    $insert = $history->newEntity();
                    $insert->type = $new['type'];
                    $insert->location_id = $location_id;
                    $insert->totalurls = $new['totalurls'];
                    $insert->totalissues = $new['totalissues'];
                    $insert->issues_detail = $new['issues_detail'];
                    $insert->avg_score = $new['avg_score'];
                    $insert->rundate = $new['rundate'];
                    $insert->created = $new['created_dt'];
                    $history->save($insert);
                    }
                }
                $urls = $db->query("SELECT * FROM `cre_urls` ");
                $cre_urls = $urls->fetchAll();
               foreach ($cre_urls as $new) {
                    $user_id = $new['user_id'];
                    $loc_data = $temptable->find("all")->where(['old_loc_id' => $user_id, 'old_agency_db' => $db_name])->first();
                    if (!empty($loc_data)) {
                    $location_id = $loc_data->loc_id;
                    $insert = $urls->newEntity();
                    $insert->location_id = $location_id;
                    $insert->url = $new['url'];
                    $insert->keyword = $new['keyword'];
                    $insert->is_running = $new['is_running'];
                    $insert->result = $new['result'];
                    $insert->total_issues = $new['total_issues'];
                    $insert->rundate = $new['rundate'];
                    $insert->created = $new['created_dt'];
                    $urls->save($insert);
                    }
                }
            }
              } catch (PDOException $ex) {
            echo 'Connection failed: ' . $ex->getMessage();
        }
   }
   public function migrateAnalytics(){
       try {
            $agencies = TableRegistry::get('tbl_agencies');
            $database = $agencies->find('all')->select(['id', 'old_db'])->all();
            $temptable = TableRegistry::get('tbl_temp');
            $con_main = new PDO('mysql:dbname=apiengine;host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92');
            foreach ($database as $dbdata) {
                $db_name = $dbdata->old_db;
                $ana_db = str_replace("mcc","analytic", $db_name);
                $db = new PDO('mysql:dbname=' . $ana_db . ';host=enfusen.c22tracmla9w.us-west-2.rds.amazonaws.com', 'enfusen_master', '25beerisgood4u!92');
                  $location_data = $temptable->find("all")->where(['old_agency_db' => $db_name])->toArray();
                  foreach ($location_data as $loc_data) {
                        $location_id = $loc_data->old_loc_id;
                        $new_location_id = $loc_data->loc_id;
                        $table = 'short_analytics_'.$location_id;
                        $main_table = 'main_analytics_'.$location_id;
                  if ($result =  $db->query("SHOW TABLES LIKE '".$table."'")) {
                  if($result->rowCount() >0) {
                  $this->conn->query("CREATE TABLE IF NOT EXISTS smartAgency.api_short_analytics_$new_location_id SELECT * FROM  $ana_db.short_analytics_$location_id"); 
                  $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE DateOfVisit dateOfVisit varchar(20)");
                  $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE PageURL pageURL varchar(300)");  
                  $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE Keyword keyword varchar(20)");  
                   $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE CurrentRank currentRank int(5)");  
                   $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE (none) none int(5)");  
                   $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE Total total int(10)");   
                   $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE TimeOnSite timeOnSite varchar(20)");   
                   $this->conn->query("ALTER TABLE smartAgency.api_short_analytics_$new_location_id CHANGE BounceRate bounceRate varchar(20)");        
                            }
                        }
               if ($result =  $db->query("SHOW TABLES LIKE '".$main_table."'")) {
                  if($result->rowCount() >0) {
                $con_main->exec("CREATE TABLE IF NOT EXISTS `api_main_analytics_$new_location_id` (
                `id` int(15) NOT NULL AUTO_INCREMENT,
                `location_id` int(11) NOT NULL,
                `tableId` varchar(50) NOT NULL,
                `pageURL` varchar(700) NOT NULL,
                `visits` varchar(100) NOT NULL,
                `timeOnSite` varchar(50) NOT NULL,
                `bounceRate` varchar(50) NOT NULL,
                `dateOfVisit` varchar(700) NOT NULL,
                `timeOfVisit` varchar(700) NOT NULL,
                `source` varchar(100) NOT NULL,
                `medium` varchar(100) NOT NULL,
                `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");  
                  }
              }
                }
            }
            echo "success";
            die;
        } catch (PDOException $ex) {
            echo 'Connection failed: ' . $ex->getMessage();
        }
  }


}
