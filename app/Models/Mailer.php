<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class Mailer extends Model
{

    private $mailer;
    private $view;
    private $ccMailAddress = '';
    private $templateBody = '';

    const EMAIL_HOST = "mail.nairaboom.com.ng";
    const COMPANY_URL = 'nairaboom.com.ng';
    const COMPANY_NAME = 'Nairaboom';
    const COMPANY_SUPPORT = 'support@nairaboom.com.ng';
    const COMPANY_EMAIL = "noreply@nairaboom.com.ng";

    public function __construct()
    {
        $this->mailer = Services::email();
        $this->view = Services::renderer();

        $senderMail = self::COMPANY_EMAIL;
        $config = $this->privateMailConfig($senderMail);
        $this->mailer->initialize($config);
    }

    /**
     * @param string $senderMail
     * @return array
     */
    private function privateMailConfig(string $senderMail=null){
        $config['protocol'] = 'smtp';
        $config['mailPath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'utf-8';
        $config['wordWrap'] = false;
        $config['SMTPHost'] = self::EMAIL_HOST;
        $config['SMTPPort'] = 465;
        $config['SMTPCrypto'] = 'ssl';
        $config['SMTPUser'] = $senderMail;
        $config['SMTPPass'] = getenv('mailKey');
        $config['mailType'] = 'html';
        $config['CRLF'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['wordWrap'] = true;

        return $config;
    }

    public function setCcMail(string $name): void
    {
        $this->ccMailAddress = $name;
    }

    public function getCcMail(): string
    {
        return $this->ccMailAddress;
    }

    public function setTemplateBody(string $name): void
    {
        $this->templateBody = $name;
    }

    public function getTemplateBody(): string
    {
        return $this->templateBody;
    }

    /**
     * @param array     $data
     * @param string    $page
     * @return string \App\Views\{page}
     */
    public function mailTemplateRender(array $data,string $page)
    {
        $this->view->setData($data);
        $page = $page.'.php';
        $templateMsg = $this->view->render('emails/'.$page);
        $this->setTemplateBody($templateMsg);
        return $templateMsg;
    }

    /**
     * This is the main func that send the mail to the client
     * @param  string|null $recipient [description]
     * @param  string|null $subject   [description]
     * @param  string|null $message   [description]
     * @return [type]                 [description]
     */
    private function mailerSend(string $recipient = null, string $subject = null, string $message = null)
    {
        $this->mailer->setFrom(self::COMPANY_EMAIL,self::COMPANY_NAME);
        $this->mailer->setTo($recipient);
        if ($this->ccMailAddress != '') {
            $this->mailer->setCC($this->ccMailAddress);
        }
        $this->mailer->setSubject($subject);
        $this->mailer->setMessage($message);
        if ($this->mailer->send()) {
            unset($this->ccMailAddress);
            return true;
        } else {
            echo 'Mailer Error: ' . $this->mailer->printDebugger();
            return false;
        }
    }

    public function sendAdminMail($message,$fullname = null)
    {
        $recipient = 'info@nairaboom.ng';
        $subject = "Contact Message From A User - {$fullname}";
        if (!$this->mailerSend($recipient, $subject, $message)) {
            return false;
        }
        return true;
    }

    /**
     * This is to get the subject mail
     * @param  string $type [description]
     * @return [type]       [description]
     */
    private function mailSubject(string $type)
    {
        $result  = array(
            'verify_account' => 'Verification of account from Nairaboom',
            'welcome' => 'Welcome On Board To Nairaboom Platform',
            'payment_invoice' => 'Notice On Your Payment Invoice on Nairaboom',
            'password_reset' => 'Request to Reset your Password!',
            'password_app_token' => 'Nairaboom password Recovery OTP',
            'password_reset_success' => 'Nairaboom Password Recovery Success',
        );
        return $result[$type];
    }

    /**
     * This is function to send mail out to client
     * @param  string       $recipient [description]
     * @param  string       $subject   [description]
     * @param  int|string   $type      [description]
     * @param  string       $customer  [description]
     * @param  array|string $info      [description]
     * @return [type]                  [description]
     */
    public function sendCustomerMail(string $recipient,string $subject,int $type=null,string $customer=null,array $info = null)
    {
        // it property templateBody take precedence over the mailBody method
        if ($this->templateBody != '')
        {
            $message = $this->templateBody;
        }
        else
        {
            $message = $this->formatMsg($recipient, $type, $customer, $info);
        }
        $recipient = trim($recipient);
        $subject = $this->mailSubject($subject);

        if (!$this->mailerSend($recipient, $subject, $message)) {
            return false;
        }
        return true;
    }

    private function formatMsg($recipient = '', $type = null, $customer=null, $info=null)
    {
        if ($recipient) {
            $msg = '';
            return $msg;
        }
    }
}
