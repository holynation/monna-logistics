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
    private $attachment = null;

    const EMAIL_HOST = "mail.nairaboom.com.ng";
    const COMPANY_URL = 'https://monaaexpress.com/';
    const COMPANY_NAME = 'MonnaExpress';
    const COMPANY_SUPPORT = 'info@monaaexpress.com';
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

    public function setAttachment(array $content): void
    {
        $this->attachment = $content;
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
        $parser = \Config\Services::parser();
        $templateMsg = $parser->setData($data)->render('emails/'.$page.'.php');
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

        if($this->attachment != null){
            $this->mailer->attach($this->attachment['content'], 'attachment', $this->attachment['filename'], $this->attachment['content_type']);
        }
        
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
    private function mailSubject(string $type, string $title=null)
    {
        $result  = array(
            'payment_invoice' => "Shipping Invoice for Order #[$title] on MonnaExpress",
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
    public function sendNotificationMail(string $recipient,string $subject,array $info = [])
    {
        $orderNumber = isset($info['order_number']) ? $info['order_number'] : null;
        $subject = $this->mailSubject($subject, $orderNumber);

        if (!$this->mailerSend($recipient, $subject, $this->templateBody)) {
            return false;
        }
        return true;
    }

}
