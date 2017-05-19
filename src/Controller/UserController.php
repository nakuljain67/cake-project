<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PHPMailer;
use Cake\Mailer\Email;

class UserController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->loadComponent('SmartEmail');
        header('Access-Control-Allow-Origin: *');
    }

    /* default called method */

    public function index() {

        $this->autoRender = false;

        $this->json(1, array(
            "method" => "index",
            "messge" => "silence is golden"
        ));
    }

    /* login */

    public function login() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";

            $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "";

            $users = TableRegistry::get('tbl_users');

            $conditions = array(
                'AND' => array(
                    array("email" => $email),
                    array("password" => md5($password))
            ));

            $results = $users->find()->where($conditions);

            if ($results->count() > 0) {

                $user_details = array();

                $userToken = sha1($this->generateRandomString());

                foreach ($results as $user):

                    /* delete live user session tokens */
                    //$this->deleteAllUserLiveSessions($user->id);
                    /* user details */
                    array_push($user_details, array(
                        "firstName" => $user->fname,
                        "lastName" => $user->lname,
                        "email" => $user->email,
                        "dob" => $user->dob,
                        "phone" => $user->phone,
                        "about" => $user->about,
                        "country" => $this->getCountryDetailsById($user->country_id)['name'],
                        "state" => $this->getStateDetailsById($user->state_id)['name'],
                        "city" => $this->getCityDetailsById($user->city_id)['name'],
                        "address" => $user->address,
                        "zipcode" => $user->zip_code,
                        "occupation" => $user->occupation,
                        "token" => $userToken,
                        "user_id" => $user->id,
                        "agency_id" => $user->agency_id
                    ));
                    /* make user entry to token table */
                    $usr_token_tbl = TableRegistry::get('tbl_userlive_tokens');
                    $user_tkn = $usr_token_tbl->newEntity();
                    $user_tkn->userid = $user->id;
                    $user_tkn->token = $userToken;
                    $user_tkn->token_created = date("Y-m-d H:i:s");
                    $user_tkn->token_expire = date("Y-m-d H:i:s");

                    if ($usr_token_tbl->save($user_tkn)) {
                        //record inserted now...:)
                    }

                endforeach;

                $this->json(1, "Login successful", $user_details);
            } else {
                /* invalid login */
                $this->json(0, "Invalid Login");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

    /* check valid email */

    public function checkEmailValidity() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";

            if ($this->is_email_valid($email)) {

                $this->json(1, "Valid email");
            } else {

                $this->json(0, "Invalid email");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

    /* forget password */

    public function forgetPassword() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $users_table = TableRegistry::get('tbl_users');

            $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "";
            $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : "";

            if (!empty($email) && !empty($url)) {
                if ($this->is_email_valid($email)) {
                    $to = $email;
                    $from = "notifications@enfusen.com";
                    $link = $url;
                    $type = "forgot_password";

                    $mailSentResponse = $this->SmartEmail->SendMail($to, $from, $link, $type);

                    if ($mailSentResponse['status'] == 1) {
                        $users_table->updateAll(['verify_code' => $mailSentResponse['token']], ['email' => $email]);
                        $this->json(1, "Mail sent");
                    } else {

                        $this->json(0, "Failed to send mail");
                    }
                } else {

                    $this->json(0, "Invalid email");
                }
            } else {
                echo $this->json(0, "Empty fields found");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

    /* Change Password */

    public function changePassword() {

        $this->autoRender = false;

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $users_table = TableRegistry::get('tbl_users');

            $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : "";
            $conf_password = isset($_REQUEST['confpassword']) ? trim($_REQUEST['confpassword']) : "";
            $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "";
            $url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : "";
            /* password comaparison */
            if ($conf_password == $password) {
                /* is token valid  */
                if ($this->isTokenExists($token)) {
                    /* updating user data */
                    $users_table->updateAll(['password' => md5($password)], ['verify_code' => $token]);
                    /* getting user email by token value */
                    $email = $this->getTableSingleDataByValue("tbl_users", "verify_code", $token, "email");
                    $email = $email[0]['email'];

                    $to = $email;
                    $from = "notifications@enfusen.com";
                    $link = $url;
                    $type = "reset_password";

                    $mailSentResponse = $this->SmartEmail->SendMail($to, $from, $link, $type);

                    if ($mailSentResponse['status'] == 1) {

                        $users_table->updateAll(['verify_code' => ''], ['email' => $email]);
                        $this->json(1, "Password Changed, Please check mail");
                    } else {

                        $this->json(0, "Failed to send mail");
                    }
                } else {
                    $this->json(0, "Invalid token");
                }
            } else {
                $this->json(0, "Password not matched");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

    /* Check token exists or not */

    public function isTokenExists($token) {

        $users_table = TableRegistry::get('tbl_users');

        $total_rows = $users_table->find('all', [
                    'conditions' => ['verify_code' => $token]
                ])->count();

        if ($total_rows > 0) {
            return true;
        }
        return false;
    }

    /* delete all live token of users */

    public function deleteAllUserLiveSessions($userId) {

        $usr_token_tbl = TableRegistry::get('tbl_userlive_tokens');

        $total_rows = $usr_token_tbl->find('all', [
                    'conditions' => ['userid' => $userId]
                ])->count();

        if ($total_rows > 0) {
            $usr_token_tbl->deleteAll(['userid' => $userId]);
            return true;
        }
        return false;
    }

    /* delete a particular user live token */

    public function deleteUserCurrentSession($token) {

        $usr_token_tbl = TableRegistry::get('tbl_userlive_tokens');

        $total_rows = $usr_token_tbl->find('all', [
                    'conditions' => ['token' => $token]
                ])->count();

        if ($total_rows > 0) {

            $usr_token_tbl->deleteAll(['token' => $token]);

            return true;
        }
        return false;
    }

    /* do user logout */

    public function logout() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : 0;

            if (!empty($token) && $token != 0) {

                if ($this->deleteUserCurrentSession($token)) {

                    $this->json(1, "User logged out successfully");
                } else {

                    $this->json(0, "Invalid Token");
                }
            } else {
                $this->json(0, "Invalid Token");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

    /* delete all live user sessions */

    public function logoutAllSession() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : 0;

            $userToken = $this->getUserIdByToken($token);

            if (!empty($userToken)) {

                $user_id = $userToken[0]['user_id'];

                if ($this->deleteAllUserLiveSessions($user_id)) {

                    $this->json(1, "User logged out successfully");
                } else {

                    $this->json(0, "Failed to do user logout");
                }
            } else {
                $this->json(0, "Invalid Token");
            }
        } else {
            $this->json(0, "silence is golden");
        }
    }

}

?>
