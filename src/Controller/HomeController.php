<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PHPMailer;
use Cake\Mailer\Email;

class HomeController extends AppController {

    public $title;
    public $base_url;

    public function initialize() {
        parent::initialize();
        $this->viewBuilder()->layout('frontend');
        $this->base_url = "192.168.1.32/WorkingProjects/Enfusen/cake/smart-agency/";
    }

    public function index($apiType = '') {

        $apiUrl = '';

        if (empty($apiType)) {
            
        } else if ($apiType == "locationwidgets") {
            $this->title = "Location Widgets";
            $apiUrl = $this->base_url . "dashboard/locationwidgets";
        } else if ($apiType == "createwidget") {
            $this->title = "Create Widget";
            $apiUrl = $this->base_url . "dashboard/createwidget";
        } else if ($apiType == "login") {
            $this->title = "Login";
            $apiUrl = $this->base_url . "user/login";
        } else if ($apiType == "forgotpassword") {
            $this->title = "Forgot Password";
            $apiUrl = $this->base_url . "user/forgetpassword";
        } else if ($apiType == "changepassword") {
            $this->title = "Change Password";
            $apiUrl = $this->base_url . "user/changepassword";
        } else if ($apiType == "logout") {
            $this->title = "Logout";
        } else if ($apiType == "logoutallsessions") {
            $this->title = "Log out All Sessions";
            $apiUrl = $this->base_url . "user/logoutallsession";
        }

        $this->set("apiType", $this->title);
        $this->set("content", "<b>API Url: </b><br/>" . $apiUrl);
    }

}

?>
