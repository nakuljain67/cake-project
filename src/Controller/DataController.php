<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class DataController extends AppController {

    public function initialize() {
        parent::initialize();
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

    /* get list of countries */

    public function countries() {

        $this->autoRender = false;

        $countries_tbl = TableRegistry::get('tbl_countries');

        $results = $countries_tbl->find("all", [
                    'conditions' => ['status' => 1]
                ])->all()->toArray();

        $countries_array = array();

        foreach ($results as $country):
            array_push($countries_array, array(
                "id" => $country->id,
                "code" => $country->code,
                "name" => $country->country,
                "phonecode" => $country->phonecode
            ));
        endforeach;

        $this->json(1, 'countries found', $countries_array);
    }

    /* get list of states */

    public function states() {

        $this->autoRender = false;

        $states_tbl = TableRegistry::get('tbl_states');

        $results = $states_tbl->find("all")->all()->toArray();

        $states_array = array();

        foreach ($results as $states):
            array_push($states_array, array(
                "id" => $states->id,
                "country_id" => $states->country_id,
                "name" => $states->name
            ));
        endforeach;

        $this->json(1, 'states found', $states_array);
    }

    /* get list of cities  */

    public function cities() {

        $this->autoRender = false;

        $cities_tbl = TableRegistry::get('tbl_cities');

        $results = $cities_tbl->find("all")->all()->toArray();

        $cities_array = array();

        foreach ($results as $cities):
            array_push($cities_array, array(
                "id" => $cities->id,
                "state_id" => $cities->state_id,
                "name" => $cities->name
            ));
        endforeach;

        $this->json(1, 'cities found', $cities_array);
    }

    /* get states by country_id */

    public function statesByCountryId() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $countryId = isset($_REQUEST['country_id']) ? intval($_REQUEST['country_id']) : 0;

            $states_tbl = TableRegistry::get('tbl_states');

            $results = $states_tbl->find("all", [
                        'conditions' => ['country_id' => $countryId]
                    ])->all()->toArray();

            $states_array = array();

            if (count($results) > 0):

                foreach ($results as $states):

                    array_push($states_array, array(
                        "id" => $states->id,
                        "country_id" => $states->country_id,
                        "name" => $states->name
                    ));

                endforeach;

                $this->json(1, 'States found', $states_array);

            else:

                $this->json(1, 'No States found', $states_array);

            endif;
        }else {

            $this->json(0, "silence is golden");
        }
    }

    /* get cities by state_id */

    public function citiesByStateId() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $stateId = isset($_REQUEST['state_id']) ? intval($_REQUEST['state_id']) : 0;

            $cities_tbl = TableRegistry::get('tbl_cities');

            $results = $cities_tbl->find("all", [
                        'conditions' => ['state_id' => $stateId]
                    ])->all()->toArray();

            $cities_array = array();

            if (count($results) > 0):

                foreach ($results as $city):

                    array_push($cities_array, array(
                        "id" => $city->id,
                        "state_id" => $city->state_id,
                        "name" => $city->name
                    ));

                endforeach;

                $this->json(1, 'Cities found', $cities_array);

            else:

                $this->json(1, 'No City found', $cities_array);

            endif;
        }else {

            $this->json(0, "silence is golden");
        }
    }

}

?>