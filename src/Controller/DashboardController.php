<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class DashboardController extends AppController {

    public $dashboard_widget_tbl;

    public function initialize() {
        parent::initialize();
        header('Access-Control-Allow-Origin: *');
        $this->dashboard_widget_tbl = TableRegistry::get('tbl_dashboard_widgets');
    }

    /* default called method */

    public function index() {

        $this->autoRender = false;

        $this->json(1, array(
            "method" => "index",
            "messge" => "silence is golden"
        ));
    }

    /* showing dashboard widgets */

    public function widgets() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $arr = array();
            $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : "";

            $widgets_array = array("col3div", "col4div", "col6div");

            if ($type == "col3div") {

                $arr = array(
                    array('id' => 1, 'name' => 'Efficency Score', 'image' => ''),
                    array('id' => 2, 'name' => 'Site Audit', 'image' => ''),
                    array('id' => 3, 'name' => 'CRE Score', 'image' => ''),
                    array('id' => 4, 'name' => 'Citation Score', 'image' => ''),
                    array('id' => 5, 'name' => 'Visibilty Score', 'image' => ''),
                    array('id' => 6, 'name' => 'Total Keywords', 'image' => ''),
                    array('id' => 7, 'name' => 'Average Rank', 'image' => ''),
                    array('id' => 8, 'name' => 'Average Rank', 'image' => ''),
                    array('id' => 9, 'name' => 'Top 3 Ranked Keywords', 'image' => ''),
                    array('id' => 10, 'name' => 'Top 10 Ranked Keywords', 'image' => '')
                );
            } else if ($type == "col4div") {

                $arr = array(
                    array('id' => 101, 'name' => 'Conversion Values', 'image' => ''),
                    array('id' => 102, 'name' => 'Traffic Values', 'image' => ''),
                    array('id' => 103, 'name' => 'Keyword Positions', 'image' => ''),
                    array('id' => 104, 'name' => 'Time On Site', 'image' => ''),
                    array('id' => 105, 'name' => 'Conversion Metrics', 'image' => ''),
                    array('id' => 106, 'name' => 'Traffic Metrics', 'image' => ''),
                    array('id' => 107, 'name' => 'Rank Metrics', 'image' => '')
                );
            } else if ($type == "col6div") {

                $arr = array(
                    array('id' => 201, 'name' => 'Campaign Insights', 'image' => ''),
                    array('id' => 202, 'name' => 'Keyword Insights', 'image' => ''),
                    array('id' => 203, 'name' => 'Content Insights', 'image' => ''),
                    array('id' => 204, 'name' => 'Site Insights', 'image' => ''),
                    array('id' => 205, 'name' => 'Citation Insights', 'image' => ''),
                    array('id' => 206, 'name' => 'Top Converting Landing Pages', 'image' => ''),
                    array('id' => 207, 'name' => 'Top Organics Traffic Pages', 'image' => ''),
                    array('id' => 208, 'name' => 'Top Keyword (By change in rank)', 'image' => ''),
                );
            }

            if (!empty($type) && in_array($type, $widgets_array))
                $this->json(1, 'Widgets Found', $arr);
            else
                $this->json(1, 'No Widgets Found', $arr);
        } else {

            $this->json(0, "silence is golden");
        }
    }

    /* showing dashboard widgets by location_id */

    public function locationWidgets() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $location_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
            $agency_id = isset($_REQUEST['agency_id']) ? intval($_REQUEST['agency_id']) : 0;

            $results = $this->dashboard_widget_tbl->find('all', [
                'conditions' => ['agency_id' => $agency_id, 'location_id' => $location_id]
            ]);

            $userWidgetsArray = array();

            if ($results->count() > 0) {

                foreach ($results as $userWidgets):

                    array_push($userWidgetsArray, array(
                        'agency_id' => $userWidgets->agency_id,
                        'location_id' => $userWidgets->location_id,
                        'widgets' => json_decode($userWidgets->widgets_found)
                    ));
                endforeach;

                $this->json(1, "Dashboard Widgets Found", $userWidgetsArray);
            }else {

                $this->json(0, "Location ID not found in Database with Agency ID: " . $agency_id);
            }
        } else {

            $this->json(0, "silence is golden");
        }
    }

    /* used to create dashboard widget */

    public function userWidget() {

        $this->autoRender = false;

        $AgencyId = isset($_REQUEST['agency_id']) ? $_REQUEST['agency_id'] : 0;
        $LocationId = isset($_REQUEST['location_id']) ? $_REQUEST['location_id'] : 0;
        $WidgetsFound = isset($_REQUEST['user_widgets']) ? $_REQUEST['user_widgets'] : "";

        $results = $this->dashboard_widget_tbl->find('all', [
            'conditions' => ['agency_id' => $agency_id, 'location_id' => $location_id]
        ]);

        if ($results->count() > 0) {

            $this->dashboard_widget_tbl->updateAll(['widgets_found' => json_encode($WidgetsFound), 'updated_dt' => date('Y-m-d H:i:s')], ['agency_id' => $AgencyId, 'location_id' => $LocationId]);
        } else {

            $usr_dashboard_tbl = $this->dashboard_widget_tbl;
            $user_widget = $usr_dashboard_tbl->newEntity();
            $user_widget->agency_id = $AgencyId;
            $user_widget->location_id = $LocationId;
            $user_widget->widgets_found = json_encode($WidgetsFound);
            $user_widget->created_dt = date("Y-m-d H:i:s");
            $user_widget->updated_dt = date("Y-m-d H:i:s");

            if ($usr_dashboard_tbl->save($user_widget)) {
                //record inserted now...:)
            }
        }
    }

}

?>