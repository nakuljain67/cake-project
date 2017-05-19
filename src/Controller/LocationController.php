<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PHPMailer;
use Cake\Mailer\Email;

class LocationController extends AppController {

    public $locations_tbl;

    public function initialize() {
        parent::initialize();
        $this->locations_tbl = TableRegistry::get('tbl_locations');
        header('Access-Control-Allow-Origin: *');
    }

    /* default method called */

    public function index() {

        $this->autoRender = false;

        $this->json(1, array(
            "method" => "index",
            "messge" => "silence is golden"
        ));
    }

    /* create agency location */

    public function create() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            /* post variables */
            $agency_id = isset($_REQUEST['agency_id']) ? intval($_REQUEST['agency_id']) : "";
            $account_type = isset($_REQUEST['account_type']) ? trim($_REQUEST['account_type']) : "";
            $website = isset($_REQUEST['website']) ? trim($_REQUEST['website']) : "";
            $name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : "";
            $services = isset($_REQUEST['services']) ? trim($_REQUEST['services']) : "";
            $target = isset($_REQUEST['target']) ? trim($_REQUEST['target']) : "";
            $conv_verified = isset($_REQUEST['conv_verified']) ? intval($_REQUEST['conv_verified']) : "";
            $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : "";
            $created_by = isset($_REQUEST['created_by']) ? intval($_REQUEST['created_by']) : "";

            $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";
            $phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : "";
            $about = isset($_REQUEST['about']) ? trim($_REQUEST['about']) : "";
            $country_id = isset($_REQUEST['country_id']) ? intval($_REQUEST['country_id']) : "";
            $state_id = isset($_REQUEST['state_id']) ? intval($_REQUEST['state_id']) : "";
            $city_id = isset($_REQUEST['city_id']) ? intval($_REQUEST['city_id']) : "";
            $address = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : "";
            $zip_code = isset($_REQUEST['zip_code']) ? trim($_REQUEST['zip_code']) : "";
            //$created = isset($_REQUEST['created']) ? trim($_REQUEST['created']) : "";

            /* save details to tbl_locations table */

            $user_loc = $location_table->newEntity();
            $user_loc->agency_id = $agency_id;
            $user_loc->account_type = $account_type;
            $user_loc->website = $website;
            $user_loc->name = $name;
            $user_loc->services = $services;
            $user_loc->target = $target;
            $user_loc->email = $email;
            $user_loc->phone = $phone;
            $user_loc->about = $about;
            $user_loc->country_id = $country_id;
            $user_loc->state_id = $state_id;
            $user_loc->city_id = $city_id;
            $user_loc->address = $address;
            $user_loc->zip_code = $zip_code;
            $user_loc->conv_verified = $conv_verified;
            $user_loc->status = $status;
            $user_loc->created = date("Y-m-d H:i:s");
            $user_loc->created_by = $created_by;

            if ($this->locations_tbl->save($user_loc)) {

                $this->json(1, "Location created successfully");
            } else {

                $this->json(0, "Failed to create location");
            }
        } else {

            $this->json(0, "silence is golden");
        }
    }

    /* edit agency location */

    public function edit() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            /* post variables */

            // keeping it unique id to update data 
            $locationId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

            $agency_id = isset($_REQUEST['agency_id']) ? intval($_REQUEST['agency_id']) : "";
            $account_type = isset($_REQUEST['account_type']) ? trim($_REQUEST['account_type']) : "";
            $website = isset($_REQUEST['website']) ? trim($_REQUEST['website']) : "";
            $name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : "";
            $services = isset($_REQUEST['services']) ? trim($_REQUEST['services']) : "";
            $target = isset($_REQUEST['target']) ? trim($_REQUEST['target']) : "";
            $conv_verified = isset($_REQUEST['conv_verified']) ? intval($_REQUEST['conv_verified']) : "";
            $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : "";
            $created_by = isset($_REQUEST['created_by']) ? intval($_REQUEST['created_by']) : "";
            $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";
            $phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : "";
            $about = isset($_REQUEST['about']) ? trim($_REQUEST['about']) : "";
            $country_id = isset($_REQUEST['country_id']) ? intval($_REQUEST['country_id']) : "";
            $state_id = isset($_REQUEST['state_id']) ? intval($_REQUEST['state_id']) : "";
            $city_id = isset($_REQUEST['city_id']) ? intval($_REQUEST['city_id']) : "";
            $address = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : "";
            $zip_code = isset($_REQUEST['zip_code']) ? trim($_REQUEST['zip_code']) : "";

            $results = $this->locations_tbl->find("all", [
                        'conditions' => ['id' => $locationId]
                    ])->all()->toArray();

            $cities_array = array();

            if (count($results) > 0):

                $this->locations_tbl->updateAll(
                        ['agency_id' => $agency_id,
                    'account_type' => $account_type,
                    'website' => $website,
                    'name' => $name,
                    'services' => $services,
                    'target' => $target,
                    'email' => $email,
                    'phone' => $phone,
                    'about' => $about,
                    'country_id' => $country_id,
                    'state_id' => $state_id,
                    'city_id' => $city_id,
                    'address' => $address,
                    'zip_code' => $zip_code,
                    'conv_verified' => $conv_verified,
                    'status' => $status,
                    'created' => date("Y-m-d H:i:s"),
                    'created_by' => $created_by
                        ], ['id' => $locationId]);

                $this->json(1, "Location Updated successfully");

            else:

                $this->json(0, "Invalid Location Id");

            endif;
        } else {

            $this->json(0, "silence is golden");
        }
    }

    /* get locations by agency id */

    public function getUserLocations() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $agencyId = isset($_REQUEST['agency_id']) ? $_REQUEST['agency_id'] : 0;

            $results = $this->locations_tbl->find("all", [
                        'conditions' => ['agency_id' => $agencyId, 'status' => 1]
                    ])->all()->toArray();

            $locations_array = array();

            if (count($results) > 0):

                foreach ($results as $location):

                    array_push($locations_array, array(
                        "id" => $location->id,
                        "agency_id" => $location->agency_id,
                        "account_type" => $location->account_type,
                        "website" => $location->website,
                        "name" => $location->name,
                        "services" => $location->target,
                        "email" => $location->email,
                        "phone" => $location->phone,
                        "about" => $location->about,
                        "country_id" => $location->country_id,
                        "state_id" => $location->state_id,
                        "city_id" => $location->city_id,
                        "address" => $location->address,
                        "zip_code" => $location->zip_code,
                        "conv_verified" => $location->conv_verified,
                        "status" => $location->status,
                        "created" => $location->created,
                        "created_by" => $location->created_by
                    ));

                endforeach;

                $this->json(1, "Locations Found", $locations_array);

            else:

                $this->json(1, "No Locations Found", $locations_array);

            endif;
        } else {

            $this->json(0, "silence is golden");
        }
    }

    /* delete agency location */

    public function delete() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $location_id = isset($_REQUEST['location_id']) ? intval($_REQUEST['location_id']) : 0;

            $total_rows = $this->locations_tbl->find('all', [
                        'conditions' => ['id' => $location_id]
                    ])->count();

            if ($total_rows > 0) {
                $this->locations_tbl->deleteAll(['id' => $location_id]);

                $this->json(1, "Location Deleted Successfully");
            }
            $this->json(0, "No Location Found with Id: " . $location_id);
        } else {

            $this->json(0, "silence is golden");
        }
    }

}
?>

