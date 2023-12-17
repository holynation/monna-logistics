<?php

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\Mailer;

/**
 * This is the authentication class handler for the web
 */
class Auth extends BaseController
{
	private $webSessionManager;
	private $mailer;
	private $loggedIn = false;
	private $appBaseUrl = 'https://';

	public function __construct()
	{
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
	}

	public function index()
	{
		$this->login();
	}

	public function login($data = '')
	{
		return view('nairaboom/login', [$data]);
	}

	public function web()
	{
		if(!$this->validate([
			'email' => 'required|valid_email',
			'password' => 'required|string'
		])){
			$errors = $this->validator->getErrors();
			foreach($errors as $error){
				displayJson(false, $error);return;
			}
		}
		$validData = $this->validator->getValidated();
		$username = $validData['email'];
		$password = $validData['password'];
		$isAjax =  ($this->request->getPost('isajax') == "true") ? true : false;

		$user = loadClass('user');
		if (!$user->findBoth($username)) {
			if ($isAjax) {
				displayJson(false, "Invalid emaild or password");return;
			} else {
				$this->webSessionManager->setFlashMessage('error', 'invalid email or password');
				redirect(base_url('auth/login'));
			}
		}
		$user = $user->data();
		if($user->status == '0'){
			displayJson(false,'Your account is not activated');
		    return;
		}
		$checkPass = decode_password(trim($password), $user->password);
		if (!$checkPass) {
			if ($isAjax) {
				displayJson(false, "Invalid emaild or password");return;
			} else {
				$this->webSessionManager->setFlashMessage('error', 'invalid email or password');
				redirect(base_url('auth/login'));
			}
		}

		if($user->user_type != 'admin'){
			displayJson(false, "Oops, invalid username or password");return;
		}

		$baseurl = base_url();
		$this->webSessionManager->saveCurrentUser($user);
		$baseurl .= $this->getUserPage($user);
		$user->last_login = formatToUTC();
		$user->update();
		
		if ($isAjax) {
			displayJson(true, $baseurl);return;
		}else {
			redirect($baseurl);
			exit;
		}
	}

	/**
	 * This is to return the user based dashboard
	 * 
	 * @param  string $user
	 * @return string
	 */
	private function getUserPage($user)
	{
		$link = array(
			'admin' => 'vc/admin/dashboard',
		);
		$roleName = $user->user_type;
		return $link[$roleName];
	}

	/**
	 * This is invoke when user click the verification link in their email account
	 * @param string $email
	 * @param string $hash
	 * @param string type
	 * @return array
	 */
	public function verify($email,$hash,$type){
		if(isset($email,$hash,$type)){
			$email = trim(urldecode($email));
			$email = str_replace(array('~az~','~09~'),array('@','.com'),$email);
			$hash = trim(urldecode($hash));
			$email_hash = sha1($email . $hash);
			$expireTime = rndDecode(@$_GET['tk']);
			$task = rndDecode(@$_GET['task']);
			$currentTime = time();
			if($task != 'verify'){
				$data['error'] = 'It seems like the link had broken, kindly re-click or copied the right link.';
				return view('verify',$data);
			}

			$check = md5(appConfig('salt') . $email) == $hash;
			if(!$check){
				$data['error'] = 'there seems to be an error in validating your email account,try again later.';
				return view('verify',$data);return;
			}

			if(isTimePassed($currentTime,$expireTime)){
				$data['error'] = 'Oops an invalid or expired link was provided.Kindly reached out to the administrator';
				return view('verify',$data);
			}

			$user = loadClass('user');
			$tempUser = $user->find($email);
			$data = array();
			if(!$tempUser){
				$data['error'] = 'sorry we don\'t seems to have that email account on our platform.';
				return view('verify',$data);
			}

			if($tempUser && $check){
				$mailType = appConfig('type');
				if($mailType[$type] == 'verify_account'){
					$id = $user->data()[0]['ID'];
					$userType = $user->data()[0]['user_type'];
					$result = $user->updateStatus($id,$userType);
					$data['type'] = $mailType[$type];
					if($result){
						// send welcome mail to user
						$param = ['customerName' => $email];
						$template = $this->mailer->mailTemplateRender($param,'account_created');
						$this->mailer->sendCustomerMail($email,'welcome');
						if(true){
							$data['success'] = "Your Account has been successfully verified";
						}
					}
					else{
						$data['error'] = 'There seems to be an error in performing the operation...';
					}
				}
				else if($mailType[$type] == 'forget'){
					$data['type'] = $mailType[$type];
					$data['email_hash'] = $email_hash;
					$data['email_code'] = $hash;
					$data['email'] = $email;
				}
				return view('verify',$data);
			}
			
		}
	}

	public function logout()
	{
		$link = '';
		$base = base_url();
		$this->webSessionManager->logout();
		$path = $base . $link;
		header("location:$path");
		exit;
	}
}
