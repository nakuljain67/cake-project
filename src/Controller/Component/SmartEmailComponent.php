<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use PHPMailer;
use SmartEmailTemplates;
use App\Controller\AppController;

class SmartEmailComponent extends Component {

    public $body;
    public $appController;

    public function __construct() {
        $this->appController = new AppController();
    }

    /* Send Mail according to its Type */

    public function SendMail($to, $from, $link, $type) {

        require_once($this->appController->getVendorPath("PHPMailer") . 'class.phpmailer.php');
        /* including forgot password template here */
        require_once($this->appController->getVendorPath("EmailTemplates") . 'class.templates.php');

        $email = new PHPMailer();
        $fromName = 'test';
        $email->From = $from;
        $email->FromName = $fromName;
        $email->IsHTML(true);
        $email->setFrom($from, $fromName);
        $email->addReplyTo($from, $fromName);
        $email->addAddress($to, '');

        $templates = new SmartEmailTemplates();

        if ($type == "forgot_password") {

            $token = $this->getGuid();
            $email->Subject = "Password Reset";
            $this->body = str_replace('~~EMAIL_HOLDER_NAME~~', $to, $templates->forgetPasswordTemplate());
            $this->body = str_replace('~~FORGOT_PASSWORD_LINK~~', $link . "/" . $token, $this->body);
        } else if ($type == "reset_password") {

            $content = '';
            $token = '';
            if (!empty($link) && $link != "") {
                $content = '<a href="' . $link . '" target="_blank">Click here to login</a>.';
            }

            $email->Subject = "Password Changed";

            $this->body = str_replace('~~EMAIL_HOLDER_NAME~~', $to, $templates->passwordChangedTemplate());
            $this->body = str_replace('~~CONTENT~~', $content, $this->body);
        }

        $email->MsgHTML($this->body);

        $email->AltBody = "test smart Agency";

        if ($email->Send()) {

            return array("status" => 1, "token" => $token);
        } else {

            return array("status" => 0, "token" => $token);
        }
    }

    /* generates 40 char random string  */

    public function getGuid() {

        return sha1($this->appController->generateRandomString());
    }

}

?>