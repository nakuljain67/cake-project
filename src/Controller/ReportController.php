<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PHPMailer;
use Cake\Mailer\Email;

class ReportController extends AppController {

    public function initialize() {
        parent::initialize();
        //header('Access-Control-Allow-Origin: *');
    }

    /* default called method */

    public function index() {

        $this->autoRender = false;

        $this->json(1, array(
            "method" => "index",
            "messge" => "silence is golden"
        ));
    }

    /* semrush api units info */

    public function siteauditUnitsInfo() {

        $this->autoRender = false;

        $semrush_api_info = semrush_api_info();
        $key = $semrush_api_info['key'];
        $FinalURL = 'https://www.semrush.com/users/countapiunits.html?key=' . $key;

        $ch = curl_init($FinalURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result_on = curl_exec($ch);
        echo json_decode($result_on);
    }

    /* semrush operations */

    public function semrushTrigger() {

        $this->autoRender = false;

        $project_name = isset($_REQUEST['brand_name']) ? trim($_REQUEST['brand_name']) : "Lenfest Institute";
        $url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : "https://www.lenfestinstitute.org/";

        $semrush_api_info = semrush_api_info();
        $main_api_url = $semrush_api_info['main_api_url'];
        $key = $semrush_api_info['key'];
        // code to create project_id
        /* $curl_url = 'management/v1/projects?key=' . $key;
          $post_data['project_name'] = $project_name;
          $post_data['url'] = $this->fully_trim($url);
          $data_string = json_encode($post_data);
          $create_new_project = $this->pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);
          $create_new_project = json_decode($create_new_project);
          $project_id = $create_new_project->project_id; */
        // code to create snapshot id

        $project_id = 777293;
        $snapshot_id = "591c60b05f50e913a20fd98b";

        /* $curl_url = 'management/v1/projects/' . $project_id . '/siteaudit/enable?key=' . $key;
          $enable_site_audit_tool_post_data['domain'] = $this->fully_trim($url);
          $enable_site_audit_tool_post_data['scheduleDay'] = 0;
          $enable_site_audit_tool_post_data['notify'] = false;
          $enable_site_audit_tool_post_data['pageLimit'] = '1500';
          $enable_site_audit_tool_post_data['crawlSubdomains'] = false;

          $data_string = json_encode($enable_site_audit_tool_post_data);

          $enable_site_audit_tool = $this->pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);

          // Run Audit
          $curl_url = 'reports/v1/projects/' . $project_id . '/siteaudit/launch?key=' . $key;
          $data_string = json_encode(array());
          $run_audit = $this->pc_post($username = '', $password = '', $main_api_url, $curl_url, $data_string);
          $run_audit = json_decode($run_audit);
          $snapshot_id = $run_audit->snapshot_id;
          $this->pr($run_audit); */

        // code to get all information
        $curl_url = 'reports/v1/projects/' . $project_id . '/siteaudit/info?key=' . $key; // need to work here
        $all_info = $this->pc_get($username = '', $password = '', $main_api_url, $curl_url);
        $all_info = json_decode($all_info);

        // code to get all error list
        $curl_url = 'reports/v1/projects/' . $project_id . '/siteaudit/meta/issues?key=' . $key;
        $error_name = $this->pc_get($username = '', $password = '', $main_api_url, $curl_url);
        $error_name_list = serialize(json_decode($error_name));

        $all_info = serialize($all_info);
        
        $this->pr($error_name_list);
        $this->pr($all_info);
    }

}

?>