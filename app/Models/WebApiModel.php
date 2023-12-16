<?php 

/**
 * This is the Model that manages Api specific request
 */
namespace App\Models;

use App\Enums\CashbackEnum;
use App\Models\EntityCreator;
use App\Models\WebSessionManager;
use App\Models\Mailer;
use App\Models\Sms;
use CodeIgniter\Model;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Config\Factories;

class WebApiModel extends Model
{
	protected $request;
	protected $response;
	private $mailer;
	private $webSessionManager;
	protected $db;
	private $entitiesNameSpace = 'App\Entities';

	public function __construct(RequestInterface $request=null, ResponseInterface $response=null)
	{
		$this->db = db_connect();
		$this->request = $request;
		$this->response = $response;
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
	}

	/**
	 * This is both for mobile and web version
	 * @param $this->request->getPost(email) The user email
	 * @param $this->request->getPost(password) The user password
	 * @return JSON return a json having the user details & token
	 */
	public function login(){
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me');
        $userType = $this->request->getPost('user_type') ?? 'customer';

        if (!($username || $password)) {
            displayJson(false,"invalid entry data");
            return;
        }
        $user = loadClass('user');
        $user_token = loadClass('user_tokens');
        if (!$user->findBoth($username, $userType)) {
            displayJson(false,'Invalid username or password');
            return;
        }
        $user = $user->data();
        if($user->status == '0'){
        	displayJson(false,'Kindly check your email and activate your account');
            return;
        }
        if (!decode_password($password, $user->password)) {
            displayJson(false, 'Invalid username or password');
            return;
        }
        $payload = $this->userPayloadData($user);
        if($rememberMe){
        	$rememberToken = $user_token->rememberMe($user->ID);
        	$payload['remember_me'] = [
        		'token' => $rememberToken[0],
        		'expired_seconds'=>$rememberToken[1]
        	];
        }
		displayJson(true, "You're successfully authenticated",$payload);
        return;
	}

    /**
     * This return the user payload data
     * @param  object $user [description]
     * @return [type]       [description]
     */
    private function userPayloadData(object $user){
    	$wallet = loadClass('wallet');
    	$userType = $this->webSessionManager->saveCurrentUser($user,true);
        if(!$userType){
        	displayJson(false, "Oops, sorry you can't login at the moment");
            return; 
        }
        unset($userType['password']);
		$token = $this->generateToken($userType); // the combined data of user_type(agent|customer) and user table
		$user_id = $userType['ID'];
		$userType['ID'] = $userType['user_table_id'];
		unset($userType['user_table_id']);

		$builder = $this->db->table('user');
		$builder->update(['last_login'=>$this->formatToUTC()], ['id'=>$user_id]);

		$userType['kyc_updated'] = false;
		$userType['kyc_approved'] = false;
		if($kycDetails = $this->getKycDetails($user_id)){
			$userType['kyc_updated'] = true;
			$userType['kyc_approved'] = $kycDetails['bvn_status'] == 0 ? false : true;
			$userType['wallet_balance'] = number_format($kycDetails['amount'],2) ?? 0;
			$userType['bonus_wallet_balance'] = number_format($kycDetails['bonus_wallet'],2) ?? 0;
			$userType['account_name'] = $kycDetails['account_name'];
			$userType['account_number'] = $kycDetails['account_number'];
			$userType['bank_name'] = $kycDetails['bank_name'];
		}
		if($userType['user_type'] == 'customer'){
			$userType['first_wallet_pay'] = $wallet->customerFirstWalletPayment($user_id) ? 1 : 0;
		}
		$payload['token'] = $token;
		$payload['details'] = $userType;
		return $payload;
    }

    /**
     * This is for remember me login function
     * @return [type] [description]
     */
    public function login_me(){
    	$user_token = loadClass('user_tokens');
    	$token = $this->request->getPost('token');
    	if ($token) {
    		[$selector, $validator] = token_me_valid($token); // token from the client cookie
    		$tokens = $user_token->findUserTokenBySelector($selector);
    		if (!$tokens) {
    			displayJson(false, 'Token no longer valid, login afresh');
    		    return;
    		}

    		if(!password_verify($validator, $tokens['hashed_validator'])){
    			displayJson(false, 'Token no longer valid, login afresh');
    			return;
    		}
	        $user = $user_token->findUserByToken($token);
	        if ($user) {
	        	// return the payload
	        	$payload = $this->userPayloadData($user);
	        	displayJson(true, "You're successfully sign-in",$payload);
            	return;
	        }
	    }
	    return false;
    }

    /**
     * [generateToken description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function generateToken(array $data){
    	helper('security');
		$token = generateJwtToken($data);
		return $token;
    }

    /**
     * This is both for mobile and web version
	 * @param fullname		the user fullname
	 * @param email 		the user email
	 * @param phone_number	the user phone number
	 * @param password 		the user password
	 * @return JSON if sucessfully created
	 */
    public function register()
	{
		//get all the information validate and create the necessary account
		$fullname = $this->request->getPost('fullname');
		if (!$fullname) {
			displayJson(false,"Name can't be empty");
			return;
		}
		$email = null;
		if($email = $this->request->getPost('email') ){
			if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
				displayJson(false,"Invalid email address");
				return;
			}
		}
		$phone = null;
		$userType = $this->request->getPost('user_type');
		if(!in_array($userType, ['agent','customer'])){
			displayJson(false, 'kindly used the approved user_type value (agent|customer)');
			return;
		}
		if($phone = $this->request->getPost('phone_number')){
			if (!isValidPhone($phone)) {
				displayJson(false,"Invalid phone number");
				return;
			}
			if (!isUniquePhone($this->db,$phone,$userType)) {
				displayJson(false,"Phone number already exists");
				return;
			}
			$formatPhone = formatToNgPhone($phone);
		}

		$referralCode = refEncode(13);
		$user = loadClass('user');

		$userTemp = $userType;
		$superagentCode = null;
		if($userType == 'agent'){
			if(!$phone){
				displayJson(false, 'Agent must provide phone number');
				return;
			}
			$superagentCode = $this->request->getPost('superagent_code');
			if(!$superagentCode){
				displayJson(false, 'Please supply your superagent code');
				return;
			}
			$superagent = loadClass('superagent');
			$superagent = $superagent->getWhere(['super_code'=>$superagentCode],$count,0,1,false);
			if(!$superagent){
				displayJson(false, 'Invalid superagent code supplied');
				return;
			}
			$superagentCode = $superagent[0]->ID;
		}
		// hash the password
		$password = trim($this->request->getPost('password'));
		if (!$password) {
			displayJson(false,'No password provided');
			return;
		}

		$this->db->transBegin();
		$password = encode_password($password);
		$toInsert = array(
			'fullname' => $fullname,'email' => $email,
			'phone_number' => $formatPhone, 'status' => $email ? 0 : 1
		);

		if($superagentCode){
			$toInsert['agent_code'] = $this->generateAgentCode();
			$toInsert['superagent_id'] = $superagentCode;
		}

		$userType = loadClass($userType);
		$userType = new $userType($toInsert);
		if (!$userType->insert($this->db,$message)) {
			$this->db->transRollback();
			displayJson(false,"Error occured: {$message}");
			return;
		}
		$lastInsertId = getLastInsertId($this->db);
		$data = array(
			'username' => $email ?? $phone,
			'username_2' => $email ? $phone : null,
			'password' => $password,
			'user_type' => $userTemp,
			'user_table_id' => $lastInsertId,
			'status' => $email ? 0 : 1,
			'referral_code' => $referralCode
		);
		
		$userID = null;
		if(!$userID = $this->createUsers($this->db,$user,$data)){
			return;
		}
		$referral = $this->request->getPost('referral_code');
		$referralType = $this->request->getPost('referral_type') ?: 'normal';
		if(!$this->handleReferral($this->db,$user,$referral,$userID,$userTemp,$referralType)){
			displayJson(false, 'The referral code is invalid');
			return;
		}

		$message = "Your have successfully registered";
		if($email){
			$accountLink = $this->activationLink($email);
			$param = [
				'customerName'=>$fullname,
				'urlLink' => $accountLink
			];
			$template = $this->mailer->mailTemplateRender($param,'account_activate');
			$mailResponse = ($this->mailer->sendCustomerMail($email,'verify_account')) ? true : false;
			if(!$mailResponse){
				log_message("info", "WEB_REGISTRATION mail not sent to user {$lastInsertId}");
			}
			$message = "Your have successfully register.Kindly Check your email for verification";
		}
		// $this->sendSmsToUser($userTemp,$lastInsertId,$formatPhone,$otp);
		$payload['ID'] = $lastInsertId;
		$payload['user_type'] = $userTemp;
		$payload['referral_code'] = $referralCode;
		// $payload['sms_otp'] = $otp;
		$payload = array_merge($payload, $toInsert);
		$this->db->transCommit();
		displayJson(true,$message,$payload);
		return;
	}

	private function createBonus($db,$userId,$refId,$userType){
		$signupBonus = getBoomWalletAmount('signup');
		$refBonus = getBoomWalletAmount('sharing_ads');
		$bonusWallet = loadClass('bonus_wallet');
		if($userId){
			$bonusWallet->updateWallet($userId,$signupBonus,'signup');
		}

		if($refId){
			$bonusWallet->updateWallet($refId,$refBonus,'referral_ads');
		}
	}

	private function handleReferral(object $db,object $user,$referral,$userId,$userType,$referralType){
		$refId = null;
		if($referral){
			$referral = urldecode(trim($referral));
			$userInsertID = $userId; // users table id
			$confirmRef = $user->getWhere(array('referral_code'=>$referral),$count,0,1,false);
			if(!$confirmRef){
				$db->transRollback();
				return false;
			}

			$confirmRef = $confirmRef[0];
			// $refId = $confirmRef->ID; // deprecated this since it's now handle from cashback
			
			if($referralType == 'normal'){
				$refParam = array(
					'referrer' => $confirmRef->ID,
					'referee' => $userInsertID
				);
				$builder = $db->table('referral');
				$builder->insert($refParam);
			}

			if($referralType == 'share_ads'){
				$refId = $confirmRef->ID;
			}
		}
		if($userType == 'customer'){
			$this->createBonus($db,$userId,$refId,$userType);
		}
		return true;
	}

	private function generateAgentCode(){
		$orderStart = '100000011';
		$query = "SELECT agent_code as code from agent order by ID desc limit 1";
		$result = $this->db->query($query);
		$result = $result->getResultArray();
		if(!empty($result) && $result[0]['code']){
			[$label,$temp] = explode('AG',$result[0]['code']);
			$orderStart = ($temp) ? $temp+1 : $orderStart;
		}
		return 'AG'.$orderStart;
	}

	/**
	 * This is the function to send sms otp to user
	 * @deprecated - Not used again
	 * @param  string $userType      [description]
	 * @param  [type] $user_table_id [description]
	 * @param  [type] $phone_number  [description]
	 * @return [type]                [description]
	 */
	private function sendSmsToUser(string $userType,$user_table_id,$phone,&$otp=null){
		$user = loadClass('user');
		$user->disableAllPasswordOTPs($userType, $user_table_id);
		// save the OTP and send the mail
		$password_otp = loadClass('password_otp');
		$otp = $password_otp->createPasswordOtp($userType,$user_table_id);
		// send otp to user using third party api
		$message = "[#][Nairaboom] {$otp} is your OTP code for verification after sign-up";
		$sms = Sms::sendCodeSms($phone,$message);
		return true;
	}

	/**
	 * This is to resend sms otp to user
	 * @deprecated 
	 * @return [type] [description]
	 */
	public function resend_sms_otp(){
		$userType = $this->request->getPost('user_type');
		$userID = $this->request->getPost('ID');
		$phone_number = $this->request->getPost('phone_number');

		if($this->sendSmsToUser($userType,$userID,$phone_number)){
			displayJson(true, 'Otp has been resent to your mobile number,valid for 15 minutes');
			return;
		}
		displayJson(false, 'Otp failed to send');
		return;
	}

	/**
	 * This is to validate user sms
	 * @deprecated
	 * @return [type] [description]
	 */
	public function validate_sms(){
		$userType = $this->request->getPost('user_type');
		$userID = $this->request->getPost('ID');
		$otp = $this->request->getPost('otp');
		$user = loadClass('user');

		if (!$this->verifyPasswordOTP($userID,$otp,$userType)) {
			displayJson(false,'Oops invalid code');
			return false;
		}
		$this->db->transBegin();
		$user->disableAllPasswordOTPs($userType,$userID);
		$userTemp = $user->findByUserTypeID($userID,$userType);
		if($userTemp){
			$id = $user->data()[0]['ID'];
			$userType = $user->data()[0]['user_type'];
			$result = $user->updateStatus($id,$userType);
			$this->db->transCommit();

			displayJson(true,"Otp successfully validated");
			return;
		}
		$this->db->transRollback();
	}

	/**
	 * @param string $email
	 * @return string
	 */
	private function activationLink($email){
		$mailSalt = appConfig('salt');
        $encodeEmail = str_replace(array('@', '.com'), array('~az~', '~09~'), $email);
        $temp = md5($mailSalt . $email);
        $expire = rndEncode(time());
        $verifyTask = rndEncode('verify');
        $accountLink = base_url("account/verify/$encodeEmail/$temp/1?task=$verifyTask&tk=$expire");
        return $accountLink;
	}

	/**
	 * This is to create user
	 * @param object  $db
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	private function createUsers(object $db, object $user, array $data){
		try{
			$user = new $user($data);
			if(!$user->insert($db,$message)){
				$db->transRollback();
				displayJson(false,"Error occured: {$message}");
				return false;	
			}
			return getLastInsertId($db);
		}catch(Exception $e){
			$db->transRollback();
			displayJson(false,"Something went wrong, try again later");
			return false;	
		}
	}

	/**
	 * @param email
	 * @return otp and expire time
	 */
	public function requestForgetPassword()
	{
		$email = trim($this->request->getPost('identity'));
		if (!$email) {
			displayJson(false,'Please provide email/phone address');
			return;
		}
		// this will just generate the token and send to the email address
		$user = loadClass('user');
		$user = $user->getWhere(array('username'=>$email),$count,0,1,false);
		if (!$user) {
			displayJson(false,'Account with that email/phone address does not exist');
			return;
		}
		$user = $user[0];
		if(strpos($email, '@') !== false){
			$this->resetWithEmail($user, $email);
		}
		else{
			$this->resetWithPhone($user, $email);
		}
	}

	private function resetWithEmail(object $user, $email){
		if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
			displayJson(false,"Invalid email address");
			return;
		}
		// disable all previous OTP by the user
		$userType = $user->user_type;
		// this is to disable all prev otp on diff users
		$user->disableAllPasswordOTPs($userType, $user->user_table_id);
		// save the OTP and send the mail
		$password_otp = loadClass('password_otp');
		$otp = $password_otp->createPasswordOtp($userType,$user->user_table_id);

		$mailer = $this->mailer;
		$param = ['customerName'=>$email,'otp'=>$otp];
		$template = $mailer->mailTemplateRender($param,'password_reset');

		$payload = array('code'=> null, 'expired_in'=>'30 minutes');
		displayJson(true,'Kindly check your email for further instructions',$payload);
		return;
	}

	private function resetWithPhone(object $user, $phone){
		if (!isValidPhone($phone)) {
			displayJson(false,"Invalid phone number supplied");
			return;
		}

		// disable all previous OTP by the user
		$userType = $user->user_type;
		$user->disableAllPasswordOTPs($userType, $user->user_table_id);
		// save the OTP and send the mail
		$password_otp = loadClass('password_otp');
		$otp = $password_otp->createPasswordOtp($userType,$user->user_table_id);
		$phone = formatToNgPhone($phone);
		$message = "[#][Nairaboom] {$otp} is your new OTP code";
		$sms = Sms::sendCodeSms($phone,$message);
		if(!$sms){
			displayJson(false, 'Unable to send new password at the moment.Please try again later');return;
		}
		
		$payload = array('code'=> null, 'expired_in'=>'30 minutes');
		displayJson(true,'Kindly check your phone for further instructions',$payload);
		return;
	}

	private function resetWithPhone_old(object $user, $phone){
		if (!isValidPhone($phone)) {
			displayJson(false,"Invalid phone number supplied");
			return;
		}
		$newPassword = randStrGen(11);
		$randomPassword = encode_password($newPassword);
		$phone = formatToNgPhone($phone);
		$message = "[#][Nairaboom] {$newPassword} is your new OTP code";
		$sms = Sms::sendCodeSms($phone,$message);
		if(!$sms){
			displayJson(false, 'Unable to send new password at the moment.Please try again later');return;
		}
		
		// $user->password = $randomPassword;
		// $user->update();
		
		$payload = array('code'=> null, 'expired_in'=>'30 minutes');
		displayJson(true,'Kindly check your phone for further instructions',$payload);
		return;
	}

	/**
	 * @param otp 		the otp sent to user
	 * @param email 	the user email
	 * @param password 	the new password
	 * @return true 	true on sucess
	 */
	public function changePassword()
	{
		$email = trim($this->request->getPost('email'));
		$otp = trim($this->request->getPost('otp'));
		$password = trim($this->request->getPost('password'));
		if (!$email) {
			displayJson(false,'Please provide email address');
			return;
		}
		if (!$otp) {
			displayJson(false,'No otp provided');
			return;
		}
		if (!$password) {
			displayJson(false,'No password provided');
			return;
		}

		$user = loadClass('user');
		$user = $user->getWhere(array('username'=>$email),$count,0,1,false);
		if (!$user){
			displayJson(false,'Sorry, an invalid operation...');
			return;
		}
		$user = $user[0];
		if(!$this->validate_otp($otp,$email,true,$user)){
			return false;
		}

		$password = encode_password($password);
		$userType = $user->user_type;
		$user_table_id = $user->user_table_id;
		$userID = $user->ID;
		$this->db->transBegin();

		$user->ID = $userID;
		$user->password = $password;

		if (!$user->update()) {
			$this->db->transRollback();
			displayJson(false,"Error occured while resetting password");
			return;		
		}

		$user->disableAllPasswordOTPs($userType,$user_table_id);

		if(strpos($email, '@') !== false){
			$mailer = new Mailer;
			$param = ['customerName'=>$email];
			$template = $mailer->mailTemplateRender($param,'password_reset_success');
			$mailer->sendCustomerMail($email,'password_reset_success');
		}

		$this->db->transCommit();
		displayJson(true,"Password has been reset successfully");
		return;	
	}

	/**
	 * @param current_password 	$this->request->getPost('current_password');
	 * @param password 			$this->request->getPost('password')
	 * @param confirm_password 	$this->request->getPost('confirm_password')
	 * @return JSON 	
	 */
	public function update_password()
	{
	    $curr_password = $this->request->getPost('current_password');
	    $new = $this->request->getPost('password');
	    $confirm = $this->request->getPost('confirm_password');

	    if (!isNotEmpty($curr_password,$new,$confirm)){
	        displayJson(false,'Empty field detected.please fill all required field and try again');
	        return;
	    }

	    if ($new !== $confirm) {
	        displayJson(false,'New password does not match with the confirmation password');
	        return;
	    }

	    $customer = getCustomer();
	    if(!$customer){
	    	displayJson(false,'Invalid users');return;
	    }
	      
	    $id = $customer->user_id;
	    $user = loadClass('user');

	    if($user->findUserProp($id)){
	        $check = decode_password(trim($curr_password), $user->data()[0]['password']);
	        if(!$check){
	        	displayJson(false,'Please type-in your password correctly');
	          	return;
	        }
	    }
		
	    $new = encode_password($new);
        $query = "update user set password = '$new' where ID=?";
        if ($this->db->query($query,array($id))) {
          	displayJson(true,'You have successfully change password');
          	return;
        }
        else{
          	displayJson(false, 'Error occured during operation');
          	return;
        }
	}

	/**
	 * This is to validate the otp for reset password
	 * @param int 		$otp This is the otp
	 * @param string 	$email This is the email
	 * @param bool 		$return
	 * @param object 	$user
	 * @return JSON - Returning JSON based on the validation
	 */
	public function validate_otp($otp=null,string $email=null,
		bool $return=false,object $user=null)
	{
		$otp = $otp;
		if($this->request->getPost('otp')){
			$otp = $this->request->getPost('otp');
		}
		$email = $email ?? trim($this->request->getPost('email'));

		if($user == null){
			$user = loadClass('user');
			$user = $user->getWhere(array('username'=>$email),$count,0,1,false);
			if (!$user) {
				if(!$return){
					displayJson(false,'invalid operation');
					return false;
				}
				return false;
			}
			$user = $user[0];
		}
		if (!$this->verifyPasswordOTP($user->user_table_id,$otp,$user->user_type)) {
			if(!$return){
				displayJson(false,'Oops invalid code');
				return false;
			}
			return false;
		}
		if(!$return){
			displayJson(true,'Otp successfully validated');
			return;
		}else{
			return true;
		}
	}

	/**
	 * @param 	int 		$user_table_id
	 * @param 	int 		$otp
	 * @param 	string 	$userType
	 * @return 	array 	return array
	 */
	private function verifyPasswordOTP(int $user_table_id,$otp,$userType)
	{
		$query = "select * from password_otp where user_table_id=? and otp=? and user_type = ? and status=0 and timestampdiff(MINUTE,date_created,current_timestamp) <= 30 order by ID desc limit 1";
		$result = $this->db->query($query,[$user_table_id,$otp,$userType]);
		if($result->getNumRows() <= 0){
			return false;
		}
		$result = $result->getResultArray();
		return $result;
	}

	/**
	 * @return void
	 */
	public function logout(){
		$customer = getCustomer();
		$user_token = loadClass('user_tokens');
		$user_token->deleteUserToken($customer->user_id);
		$this->webSessionManager->logout();
		$_SERVER['current_user'] = [];
		displayJson(true,"You've successfully logout");
    }

    /**
     * This is to get the kyc details of users
     * @param  int    $user_id [description]
     * @return array            [description]
     */
    private function getKycDetails(int $user_id){
    	$query = "SELECT a.*,name as bank_name,c.amount,d.amount as bonus_wallet from user_kyc_details a left join bank_lists b on b.bank_code = a.bank_code left join wallet c on c.user_id = a.user_id left join bonus_wallet d on d.user_id = a.user_id where a.user_id = ? and a.status='1'";
    	$result = $this->db->query($query, [$user_id]);
    	$result = $result->getResultArray();
    	if(empty($result)){
    		return false;
    	}
    	return $result[0];
    }

    /**
     * THis is to set the kyc document object based on the $currentUserObject
     * @param object $currentUserObj [description]
     * @param object $customer       [description]
     */
    private function setKycDetails(object $currentUserObj,object $customer){
    	$kycDetails = $this->getKycDetails($customer->user_id);
    	$currentUserObj->kyc_updated = false;
    	$currentUserObj->kyc_approved = false;
    	if($kycDetails){
    		$currentUserObj->kyc_updated = true;
    		$currentUserObj->kyc_approved = $kycDetails['bvn_status'] == 0 ? false : true;
    		$currentUserObj->account_name = $kycDetails['account_name'];
    		$currentUserObj->account_number = $kycDetails['account_number'];
    		$currentUserObj->bank_name = $kycDetails['bank_name'];
    	}
    	return $currentUserObj;
    }

	/**
	 * [profile description]
	 * @return [type]               [description]
	 */
	public function profile()
	{
		// check for get and post to the able to perform the necessary update as required
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$customer = getCustomer();
			$temp = loadClass('customer');
			$wallet = loadClass('wallet');
			$temp->ID = $customer->ID;
			if($temp->load()){
				$data = [
					'user_id' => $customer->user_id,
					'referral_code' => $customer->referral_code,
					'user_type' => $customer->user_type,
				];
				$data1 = array_merge($temp->toArray(), $data);
				$data1['first_wallet_pay'] = $wallet->customerFirstWalletPayment($data1['user_id']) ? 1 : 0;
			}

			$customer = new $temp($data1);

			if($customer){
				$this->setKycDetails($customer, $customer);
			}
			unset($customer->user_id);
			displayJson(true,"success",$customer->toArray());
			return;
		}

		// this would mean to update the profile
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$customer = getCustomer();
			$userType = $customer->user_type;
			$userTemp = $userType;
			$userType = loadClass($userType);
			$entityCreator = new EntityCreator($this->request);
			// remove email,status,date_created,password,they should not be editable
			$nonEditable = array('date_created','status');
			$param  = $this->request->getPost(null);
			foreach ($nonEditable as $value) {
				if(array_key_exists($value, $param)){
					unset($param[$value]);
				}
			}
			$entityCreator->outputResult = false;
			$result = $entityCreator->update($userTemp,$customer->ID,true,$param);
			$entityCreator->outputResult = true;
			// TODO: I WANT TO FIX THE CODE THAT DISPLAY ERROR AFOREHAND BEFORE GETTING TO THIS POINT IN THE CODE SUCH THAT THE ERROR IS DISPLAYED RIGHT AT THIS POINT
			if (!$result) {
				// displayJson(false,"error occured");
				return;
			}
			$newCustomer = new $userType(array('ID'=>$customer->ID));
			$newCustomer->load();
			$message = "You've successfully updated your profile";
			$newCustomer->user_type = $customer->user_type;
			$newCustomer->user_id = $customer->user_id;
			if($newCustomer){
				$this->setKycDetails($newCustomer, $customer);
			}
			$myResult = (object)$newCustomer;
			$_SERVER['current_user'] = $myResult;
			$myResult = $myResult->toArray();
			displayJson(true,$message,$myResult);
			return;
		}
	}

	/**
	 *
	 * This would validate the users and create virtual account for them if validated
	 * @return [type] [description]
	 */
	public function validate_account(){
		$bvnNumber = $this->request->getPost('bvn_number');
		$email = $this->request->getPost('email');
		$validation = \Config\Services::validation();

		$validation->setRules([
			'bvn_number' => 'required|numeric',
			'email' => 'required|valid_email',
			'account_number' => 'required|numeric',
			'bank_code' => 'required',
		]);
		if(!$validation->withRequest($this->request)->run()){
			$errors = $validation->getErrors();
			foreach($errors as $error){
				displayJson(false, $error);
				return;
			}
		}
		$monnify = Factories::libraries('Monnify');
		$customer = getCustomer();
		$bankLists = loadClass('bank_lists');
		$userBanks = loadClass('user_banks');
		$user_kyc = loadClass('user_kyc_details');

		$validation = $validation->getValidated();
		$bvnNumber = $validation['bvn_number'];
		$email = $validation['email'];
		$accountNumber = $validation['account_number'];
		$bankCode = $validation['bank_code'];

		$userID = $customer->user_id;
		if($user_kyc->getWhere(['user_id'=>$userID,'bvn_status'=>'1'],$c,0,null,false)){
			displayJson(true, "Your account has been validated already",$customer);
			return;
		}

		// count bvn trial times
		if(!$this->bvnLookUp($customer, $bvnNumber)){
			displayJson(false,"Oops, you've suspended on the platform due to multiple bvn trial validation, kindly reach out to admin");
			return;
		}
		$param = [
			'bvn' => $bvnNumber,
			'bankCode' => $bankCode,
			'accountNumber' => $accountNumber
		];
		$bvnResponse = $monnify->bvnMatchValidation($param);
		if(!$bvnResponse){
			$error = $monnify->getError() ?? 'Error validating your BVN';
			displayJson(false, $error);return;
		}
		$this->db->transBegin();
		$bankName = $bankLists->getWhere(['bank_code' => $bankCode],$c,0,null,false);
		$bankName = ($bankName) ? $bankName[0]->name : $bankCode;
		$accountName = $bvnResponse->responseBody->accountName;
		$bvnNumber = $bvnResponse->responseBody->bvn;
		$accountNumber = $bvnResponse->responseBody->accountNumber;

		$bankData = [
		    'user_id' => $userID,
		    'account_name' => $accountName,
		    'account_number' => $accountNumber,
		    'bank_name' => $bankName
		];
		$userBanks->setArray($bankData);
		if(!$userBanks->insert($this->db, $message)){
			$this->db->transRollback();
			displayJson(false, $message);return;
		}

		$param = [
			'bvn' => $bvnNumber,
			'email' => $email,
			'fullname' => $customer->fullname
		];
		$virtualAccount = $monnify->createVirtualAccount($param);
		if(!$virtualAccount){
			$this->db->transRollback();
			$error = $monnify->getError() ?? 'Error creating virtual account, please try again later';
			displayJson(false, $error);return;
		}

		$param = [
			'user_id' => $userID,
			'account_holder' => $virtualAccount->reservationReference,
			'virtual_account' => $virtualAccount->accountReference,
			'account_name' => $virtualAccount->accounts[0]['accountName'],
			'account_number' => $virtualAccount->accounts[0]['accountNumber'],
			'bank_code' => $virtualAccount->accounts[0]['bankName'],
			'bvn_number' => $bvnNumber,
			'bvn_status' => 1,
			'status' => 1,
		];
		if(!$user_kyc->insert($this->db, $message)){
			$this->db->transRollback();
			displayJson(false, $message);return;
		}
		$this->db->transCommit();
		$this->createBvnLookUp($userID,$bvnNumber);

		$newCustomer->user_type = $customer->user_type;
		$newCustomer->user_id = $customer->user_id;
		if($newCustomer){
			$this->setKycDetails($newCustomer, $customer);
		}
		$payload = $newCustomer;
		displayJson(true, "Your BVN has been successfully validated and virtual account created",$payload);
		return;	
	}

	private function bvnLookUp(object $customer, $bvnNumber){
		$bvn_lookup = loadClass('bvn_lookup');
		return $bvn_lookup->bvnLookUp($customer, $bvnNumber);
	}

	private function createBvnLookUp($userID, $bvnNumber){
		$bvn_lookup = loadClass('bvn_lookup');
		return $bvn_lookup->bvnLookUp($userID, $bvnNumber);
	}

	/**
	 * This is to get the virtual account details of a user
	 * @return [type] [description]
	 */
	public function fetch_virtual_account(){
		$customer = getCustomer();
		$monnify = Factories::libraries('Monnify');
		$account = $monnify->getVirtualAccountDetails($customer);

		$payload = [];
		if(!is_object($account) && $account == 4){
			displayJson(false, "You need to first validate your account");
			return;
		}
		if(!is_object($account) && $account == 2){
			displayJson(false, 'Something went wrong, kindly reach out to the admin');
			return;
		}
		if(!is_object($account) && $account == 3){
			displayJson(false, 'Your BVN must be validated before you get your account details');
			return;
		}
		if(!$account){
			displayJson(false, 'Unable to locate your virtual account');
			return;
		}
		if($account->accounts){
			$payload = [
				'account_name' => $account->accountName,
				'account_number' => $account->accounts[0]['accountNumber'],
				'bank_name' => $account->accounts[0]['bankName'],
			];
			displayJson(true,'Operation successful',$payload);
			return;
		}
		displayJson(true,'Virtual account details is not yet ready');
		return;
	}

	public function fetch_account_name(){
		$code = $this->request->getPost('bank_code');
		$accountNumber = $this->request->getPost('account_number');
		if(!$code || !$accountNumber){
			displayJson(false,'Please supplied the required parameters');
			return;
		}
		$monnify = Factories::libraries('Monnify');
		$customer = getCustomer();
		$accountName = $monnify->getAccountName($code,$accountNumber,$customer->user_id);
		if(!$accountName){
			displayJson(false,"Sorry couldn't fetch account name, try again");
			return;
		}
		displayJson(true,'Operation successful',['account_name'=>$accountName]);
		return;
	}

	/**
	 * @ignore - Stricly for development purpose
	 * This is an endpoint to delete customer account using the api
	 * and it is for development purpose
	 * @return [type] [description]
	 */
	public function delete_mono_account(){
		$user_kyc = loadClass('user_kyc_details');
		$accountHolder = $this->request->getPost('account_holder');
		$account = $user_kyc->deleteMonoAccountHolder($accountHolder);
		if(!$account){
			displayJson(true,"account could have been deleted already");
			return;
		}
		displayJson(true,'account successfully deleted');
		return;
	}

	/**
	 * This is for interswitch payment after successful payment
	 * @return [type] [description]
	 */
	public function interswitch_wallet_payment()
	{
		$customer = getCustomer();
		$ref = $this->request->getPost('ref');
		$pay_ref = $this->request->getPost('pay_ref');
		$amount = $this->request->getPost('amount'); // in kobo
		$amount = $amount/100; // convert to naira
		$card = $this->request->getPost('payment_method');
		if($customer->user_type == 'agent' && !$customer->first_wallet_pay){
			if($amount < 5000){
				displayJson(false, 'The first amount to fund should be at least 5,000');
				return;
			}
		}
		$insert = array(
			'transaction_number' => $pay_ref,
			'reference_number' => $ref,
			'reference_hash' => generateNumericRef($this->db,'wallet_payment_history','reference_hash','WAF'),
			'user_id' => $customer->user_id,
			'payment_status' => 'pending',
			'date_created' => formatToUTC(),
			'payment_channel' => 'interswitch',
			'amount' => $amount,
			'payment_method' => $card ?? 'transfer'
		);
		$this->db->transBegin();
		$wallet_payment = loadClass('wallet_payment_history');
		$payment = $this->validateInterswitchUser($ref);

		if($payment && $payment->payment_status == 'success'){
			displayJson(true,'You wallet had been funded already');
			return true;
		}
		$item = new $wallet_payment($insert);
		if (!$item->insert()) {
			$this->db->transRollback();
			displayJson(false,'There is a problem initiating payment');
			return;
		}
		$this->db->transCommit();
		displayJson(true,'Payment has been initialized');
		return;
	}

	public function interswitch_validation(){
		$ref = $this->request->getPost('ref');
		$amount = $this->request->getPost('amount');
		$merchant_code = getenv('interswitchMerchantCode');
		$initializationBaseURL = (ENVIRONMENT == 'development') ? 'https://qa.interswitchng.com' : 'https://webpay.interswitchng.com';
		$url = $initializationBaseURL."/collections/api/v1/gettransaction.json?merchantcode={$merchant_code}&transactionreference={$ref}&amount={$amount}";

		$payment = $this->validateInterswitchUser($ref);
		if(!$payment){
			displayJson(false, "Unable to validate payment reference");
			return false;
		}
		if($payment && $payment->payment_status == 'success'){
			displayJson(true,'Your wallet had been funded already');
			return true;
		}

		$headers = array('Content-Type'=>'application/json');
		$requestReport = curlRequest($url,'get',$headers,null,true,$output);
		if (!$requestReport) {
			$response = 'Unable to connect with payment server';
			displayJson(false,$response);
			return false;
		}
		$output = json_decode($output);
		if($output->ResponseCode == '00'){
			if(!$this->updateTransactionRecord($output,$amount,$payment)){
				return false;
			}
			return true;
		}
		$amount = $amount/100;
		$ref = $output->MerchantReference ?? $ref;
		// $this->createFundTransaction($this->db,$payment->user_id,$amount,$ref,0);
		displayJson(false,'Unable to validate payment');
		return;
	}

	/**
	 * This validate the reference to know the user who made the transaction
	 * @param  string $ref [description]
	 * @return [type]      [description]
	 */
	private function validateInterswitchUser(string $ref)
	{
		$wallet_payment_history = loadClass('wallet_payment_history');
		$wallet_payment_history = $wallet_payment_history->getWhere(['reference_number'=>$ref],$count,0,1,false);
		if(!$wallet_payment_history){
			return false;
		}
		return $wallet_payment_history[0];
	}

	private function updateTransactionRecord(object $data,$paymentAmount,$payment){
		$amount = $data->Amount; // in kobo
		if($paymentAmount != $amount){
			displayJson(false, "Unable to validate payment");
			return false;
		}
		$amount = $amount/100; // convert to naira
		$wallet = loadClass('wallet');
		$bonusWallet = loadClass('bonus_wallet');
		$db = $this->db;
		$db->transBegin();
		$paymentDate = formatToUTC($data->TransactionDate);
		$payment->ID = $payment->ID;
		$payment->payment_status = 'success';
		$payment->payment_date = $paymentDate;
		$payment->transaction_number = $data->PaymentReference;
		$payment->transaction_message = $data->ResponseDescription;
		$payment->payment_log = json_encode($data);
		if(!$payment->update()){
			$db->transRollback();
			displayJson(false,'Unable to update payment record');
			return false;
		}

		if(!$wallet->updateWallet($payment->user_id,$amount,'interswitch_fund','fund')){
			log_message('info',"NAIRABOOM_INTERSWITCH_ERROR: Unable to update the wallet {id}-{ref}",['id'=>$payment->user_id,'ref'=>$data->MerchantReference]);
			// fail gracefully
		}

		if(!$wallet->customerFirstWalletPayment($payment->user_id)){
			$customerFirstFund = getBoomWalletAmount('first_wallet_funding');
			$bonusWallet->updateWallet($payment->user_id, $customerFirstFund, 'customer_first_wallet_pay');
		}

		if(!$wallet->agentFirstWalletPayment($payment->user_id,$amount)){
			log_message('info',"NAIRABOOM_INTERSWITCH_ERROR: Unable to update the wallet {id}-{ref}",['id'=>$payment->user_id,'ref'=>$data->MerchantReference]);
			// fail gracefully
		}
		// $this->createFundTransaction($db,$payment->user_id,$amount,$data->MerchantReference,1);
		$db->transCommit();
		log_message('info','NAIRABOOM_INTERSWITCH_SUCCESS: payment successfully made');
		displayJson(true,'Your wallet has been succesfully funded');
		return true;
	}

	/**
	 * This would create the transaction history
	 * @param  object $db      [description]
	 * @param  [type] $user_id [description]
	 * @param  [type] $amount  [description]
	 * @param  [type] $ref     [description]
	 * @return [type]          [description]
	 */
	private function createFundTransaction(object $db,$user_id,$amount,$ref,$status=1)
	{
		$builder = $db->table('transaction_history');
		$param = [
			'user_id' => $user_id,
			'amount' => $amount,
			'tranx_name' => 'fund',
			'tranx_type' => 'credit',
			'channel' => 'interswitch',
			'status' => $status
		];
		$builder->set($param);
		if(!$builder->insert()){
			$db->transRollback();
			log_message('info',"NAIRABOOM_INTERSWITCH_ERROR: problem inserting into transaction_history table {id}-{ref}",['id'=>$user_id,'ref'=>$ref]);
		}
	}

	public function winning_boxes(){
		$customer = getCustomer();
		$daily_winner = loadClass('daily_winner');
		$winningBoxes = $daily_winner->getWinningBoxes($customer->user_id);
		$progressBoxes = $daily_winner->getWinningBoxesProgress($customer->user_id);
		$payload = [
			'final_progress' => $winningBoxes,
			'cumulative_progress' => $progressBoxes,
		];
		displayJson(true,'success',$payload);
		return;
	}

	public function wheel_tracker(){
		$cashback = loadClass('cashback');
		$settings = loadClass('settings');
		$spin = loadClass('spinwheel_setting');
		$lastWheelCounter = $settings->getWhere(['settings_name' => 'last_wheel_counter'],$c,0,1,false);
		$lastCashbackCounter = $settings->getWhere(['settings_name' => 'last_cashback_counter'],$c,0,1,false);
		$lastCounter = 0;
		if($lastWheelCounter){
			$lastCounter = $lastWheelCounter[0]->settings_value;
		}
		$cycleCounter = null;
		if($lastCounter == 0){
			// This would work when they are just starting out the game
			// however, i need to capture another case if the game already started the counter was resetted
			$lastCashbackCounter = $lastCashbackCounter[0]->settings_value;
			if($lastCashbackCounter > 0){
				$cycleCounter = $cashback::totalCount(" where id > '$lastCashbackCounter'");
			}else{
				$cycleCounter = $cashback::totalCount();
			}
		}else{
			$cycleCounter = $cashback::totalCount(" where id > '$lastCounter'");
		}
		$spin = $spin->allNonObject($count,true,0,null);
		$payload = [
			'spin_cycle_counter' => $cycleCounter,
			'spin_cycle_settings' => $spin
		];
		displayJson(true,'success',$payload);
		return;
	}

	public function validate_game(){
		$ref = $this->request->getPost('game_ref');
		$cashbackLog = loadClass('cashback_log');
		$cashbackLog = $cashbackLog->getWhere(['game_ref'=>$ref],$c,0,null,false);
		if(!$cashbackLog){
			displayJson(false, 'Please provide a valid game ticket');return;
		}
		$cashbackLog = $cashbackLog[0];
		if($cashbackLog->game_ref_status){
			displayJson(false, "Your game ticket is already expired, kindly go back and play again");return;
		}

		displayJson(true,"Your game has been succesfully validated");return;
	}

	public function update_play_stake(){
		$colourScheme = $this->request->getPost('colour_scheme');
		$cashbackNumber = $this->request->getPost('cashback_number');
		$colours = explode(":", $colourScheme);
		if(count($colours) < 4){
			$message = "Please ensure you are supplying the right box count";
			return false;
		}
		$boxNumber = $this->request->getPost('boom_box_number');
		$ref = $this->request->getPost('game_ref');
		$cashbackLog = loadClass('cashback_log');
		$cashbackLog = $cashbackLog->getWhere(['game_ref'=>$ref, 'game_ref_status'=>0],$c,0,null,false);
		if(!$cashbackLog){
			displayJson(false, 'Please provide a valid game ticket');return;
		}
		$cashbackLog = $cashbackLog[0];
		if($cashbackLog->game_ref_status){
			displayJson(false, "Your game ticket is already expired, kindly go back and play again");return;
		}
		$data = [];
		$payload = $cashbackLog->cashback->toArray() ?? [];
		$origCashback = $cashbackLog->cashback;
		$customer = getCustomer();
		$builder = $this->db->table('cashback');
		$builder->update(['cashback_time' => $cashbackNumber], ['id' => $cashbackLog->cashback_id]);

		if($boxNumber == 1){
			$spin = loadClass('spinwheel_setting');
			$result = $spin->getWhere(['spin_type'=>'alert_boom'], $c,0,null,false);
			$winningColour = strtolower($result[0]->spin_colour);
			if($customer->user_type == 'agent'){
				$bonusWalletMeta = loadClass('bonus_wallet_meta');
				$currentBonusWalletBalance = $bonusWalletMeta->getWalletBalance($origCashback->user_id, $cashbackLog->phone_number);
			}else if($customer->user_type == 'customer'){
				$bonusWallet = loadClass('bonus_wallet');
				$currentBonusWalletBalance = $bonusWallet->getWalletBalance($origCashback->user_id);
			}
			
			if($this->matchAllThreeUnitUnsequence($winningColour,$colourScheme)){
				$data['winning_amount_status'] = true;
				$data['winning_amount_1'] = $currentBonusWalletBalance;
				$data['winning_amount'] = 0; // since all bonus in giveaway wallet is usually moved to main wallet
				$data['match_sequence'] = 'three_unseq';
				$data['winning_type'] = 'alert_boom';
			}
			else{
				$logMessage = "TicketIdentity:: ".CashbackEnum::ALERT_BOOM->value." Cashback:: {$origCashback->ID} Name:: {$cashbackLog->fullname}[user_id:: {$origCashback->user_id}] is trying to win ALERT BOOM with an invalid combination";
				log_message('info', $logMessage);
				displayJson(false, "It appears you don't have a valid winning combo");return;
			}
		}
		else if($boxNumber == 2){
			$spin = loadClass('spinwheel_setting');
			$result = $spin->getWhere(['spin_type'=>'jackpot'], $c,0,null,false);
			$winningColour = strtolower($result[0]->spin_colour);
			$wiinningAmt = str_replace(',', '', $result[0]->amount);
			if($this->matchAllFourUnitSequence($winningColour,$colourScheme)){
				$data['winning_amount_status'] = true;
				$data['winning_amount_1'] = $wiinningAmt;
				$data['winning_amount'] = $wiinningAmt;
				$data['match_sequence'] = 'four_consec';
				$data['winning_type'] = 'jackpot';
			}
			else{
				$logMessage = "TicketIdentity:: ".CashbackEnum::JACKPOT->value." Cashback:: {$origCashback->ID} Name:: {$cashbackLog->fullname}[user_id:: {$origCashback->user_id}] is trying to win JACKPOT with an invalid combination";
				log_message('info', $logMessage);
				displayJson(false, "It appears you don't have a valid winning combo");return;
			}
		}
		
		// since this are diff from the real winning modalities
		if($boxNumber == 3){
			$data['winning_bonus_amount'] = true;
			$data['winning_type'] = 'one_box';
			$data['winning_bonus_amount_value'] = getBoomWalletAmount('one_green_box');
		}

		if($boxNumber == 4){
			$data['winning_bonus_amount'] = true;
			$data['winning_type'] = 'two_box';
			$data['winning_bonus_amount_value'] = $origCashback->amount * getBoomWalletAmount('two_green_box');
		}

		if($customer->user_type == 'customer'){
			// if($boxNumber == 5){
			// 	$data['winning_bonus_amount'] = true;
			// 	$data['winning_type'] = 'two_match_number_box';
			// 	$data['winning_bonus_amount_value'] = ($origCashback->deducted_amount * 2.5);
			// }

			if($boxNumber == 7){
				$boomcode = loadClass('boomcode_setting');
				$cashbackCode = str_replace(':', '::', $cashbackNumber);
				$boomcode = $boomcode->getWhere(['code' => $cashbackCode, 'status' => 1],$c,0,null,false);
				if($boomcode){
					$data['winning_bonus_amount'] = true;
					$data['winning_type'] = 'boom_code';
					$data['winning_bonus_amount_value'] = 1;
				}
			}
		}
		
		if($customer->user_type == 'agent'){
			if($boxNumber == 6){
				$data['winning_amount_status'] = true;
				$data['winning_amount'] = ($origCashback->deducted_amount * 2.5);
				$data['match_sequence'] = 'two_match_unseq';
				$data['winning_type'] = 'agent_two_box';
			}
		}

		$payload['bank_lists_id'] = $origCashback->bank_lists->name;
		$payload['fullname'] = $cashbackLog->fullname;
		$payload['phone_number'] = $cashbackLog->phone_number;
		$payload['account_number'] = $cashbackLog->account_number;
		$payload['bank_code'] = $cashbackLog->bank_code;
		$payload['game_ref'] = $ref;
		$this->db->transBegin();
		$winningCode = null;

		if(isset($data['winning_bonus_amount']) && $data['winning_bonus_amount']){
			$bonusWalletParam = [
				'winning_type' => $data['winning_type'],
				'winning_amount' => $data['winning_bonus_amount_value'],
			];
			$this->handleGiveAwayWallet($customer,$bonusWalletParam,$cashbackLog);
		}

		// validate that winning data is passed from modelvalidator
		if(isset($data['winning_amount_status']) && $data['winning_amount_status']){
			$winningCode = generateHashRef('reference');
			$redeemStatus = $customer->user_type == 'customer' ? '1' : '0'; // used for agent redeeming code
			$winnerParam = [
				'cashback_id' => $cashbackLog->cashback_id,
				'lucky_time' => $colourScheme,
				'amount_won' => $data['winning_amount_1'],
				'date_won' => $cashbackLog->date_created,
				'match_sequence' => $data['match_sequence'],
				'winning_code' => $winningCode,
				'redeem_status' => $redeemStatus,
			];
			$param = [
				'fullname' => $cashbackLog->fullname,
				'phone_number' => $cashbackLog->phone_number,
				'winning_type' => $data['winning_type'],
				'winning_amount' => $data['winning_amount'],
			];
			if(!$this->handleWinner($customer->user_id,$winnerParam,$customer->user_type,$param)){
				$this->db->transRollback();
			}
		}

		$payload['winning_code'] = $winningCode;
		$cashbackLog->game_ref_status = 1;
		$cashbackLog->update();
		$this->db->transCommit();
		displayJson(true,"You have successfully updated the game",$payload);return;
	}

	private function handleGiveAwayWallet($customer,$param, $cashbackLog){
		$bonusWallet = loadClass('bonus_wallet');
		$wallet = loadClass('wallet');
		$bonusWalletMeta = loadClass('bonus_wallet_meta');
		$userId = $customer->user_id;
		$userType = $customer->user_type;

		if($param['winning_type'] == 'one_box' || $param['winning_type'] == 'two_box'){
			$amountWon = $param['winning_amount'];
			$bonusWallet->updateWallet($userId,$amountWon,$param['winning_type']);
			if($userType == 'agent'){
				$bonusWalletMeta->updateWallet($userId,$amountWon, $cashbackLog->phone_number);
			}
		}
		else if($param['winning_type'] == 'boom_code'){
			$amountWon = $param['winning_amount'];
			$builder = $this->db->table('boom_points');
			$crossAmount = 0;
			$giveawaySetting = $bonusWallet->getGiveSetting();
			if($giveawaySetting){
				$crossAmount = $giveawaySetting->threshold;
			}
			$builder->insert(['user_id' => $userId, 'point' => $amountWon, 'amount' => $crossAmount]);
		}
		else if($param['winning_type'] == 'two_match_number_box'){
			$currrentBalance = $bonusWallet->getWalletBalance($userId);
			if($currentWalletBalance >= 10000){
				// transfer 2.5% of deducted_amount(stake amount) out of this bonus to his real wallet
				$amountWon = $param['winning_amount'];
				$bonusWallet->deductWallet($userId,$amountWon,$param['winning_type']);
				$wallet->updateWallet($userId,$amountWon,'won_two_green');
			}
		}
	}

	private function handleWinner($userId,$param,$userType,$userInfo=null){
		$builder = $this->db->table('daily_winner');
		$builder->insert($param);

		if($userType == 'customer'){
			$amountWon = $userInfo['winning_amount'];
			$wallet = loadClass('wallet');
			$bonusWallet = loadClass('bonus_wallet');
			$bonusWallet = $bonusWallet->getWhere(['user_id'=>$userId],$c,0,null,false);
			if($bonusWallet){
				$bonusWallet = $bonusWallet[0];
				$bonusAmount = $bonusWallet->amount;
				if($bonusAmount && $bonusAmount > 0){
					$amountWon = $amountWon + $bonusAmount;
					$bonusWallet->deductWallet($userId,$bonusAmount,'debit_game');
				}
			}
			$wallet->updateWallet($userId,$amountWon,$userInfo['winning_type']);

			if($userInfo['winning_type'] == 'alert_boom' || $userInfo['winning_type'] == 'jackpot'){
				$type = $userInfo['winning_type'] == 'alert_boom' ? 'influencer_commission_alert' : 'influencer_commission_jackpot';
				$this->processInfluncerEarning($amountWon, $type);
			}

			return true;
		}
		else if($userType == 'agent'){
			if($param['amount_won'] > 0){
				// send sms to the winner that played under the agent
				$formatPhone = formatToNgPhone($userInfo['phone_number']);
				$code = $param['winning_code'];
				$message = "[#][Nairaboom] Hello {$userInfo['fullname']}, your winning code is {$code} ";
				$sms = Sms::sendCodeSms($formatPhone,$message);
			}
			
			return true;
		}
		return false;
	}

	private function processInfluncerEarning($amount,$type){
		$cashback = loadClass('cashback');
		$wallet = loadClass('wallet');
		$users = $cashback->getAllUserInfluencer();
		if(!empty($users)){
			foreach($users as $user){
				$userID = $user['ID'];
				$amount = 0.05 * $amount;
				$wallet->updateWallet($userID,$amount,$type,'wallet');
			}
		}
	}

	private function matchAllThreeUnitUnsequence(string $search,string $against){
		$time1 = [$search,$search,$search];
		$time2 = explode(":",$against);
		sort($time1,SORT_NUMERIC);
		sort($time2,SORT_NUMERIC);
		$result = array_intersect($time2,$time1);
		if(!empty($result) && count($result) == 3){
			return true;
		}
		return false;
	}

	private function matchAllFourUnitSequence(string $search,string $against){
		$time1 = [$search,$search,$search,$search];
		$time2 = explode(":",$against);
		sort($time1,SORT_NUMERIC);
		sort($time2,SORT_NUMERIC);
		$result = array_intersect($time2,$time1);
		if(!empty($result) && count($result) == 4){
			return true;
		}
		return false;
	}

	public function redeem_winning(){
		$code = $this->request->getGet('code');
		if(!$code){
			displayJson(false,'Please provide a code');return;
		}
		$dailyWinner = loadClass('daily_winner');
		$winner = $dailyWinner->getWhere(['winning_code'=>$code],$c,0,null,false);
		if(!$winner){
			displayJson(false,'Please provide a valid code');return;
		}
		$winner = $winner[0];
		if($winner->redeem_status){
			displayJson(true,"You have already redeemed your winning using the code '{$code}'");return;
		}
		$cashback = loadClass('cashback');
		$wallet = loadClass('wallet');
		$bonusWallet = loadClass('bonus_wallet');
		$bonusWalletMeta = loadClass('bonus_wallet_meta');
		$agent = loadClass('agent');
		$user = loadClass('user');

		$cashback = $cashback->getWhere(['id'=>$winner->cashback_id],$c,0,null,false);
		$cashback = $cashback[0];
		$cashbackLog = $cashback->cashback_log;

		$user->ID = $cashback->user_id;
		if(!$user->load()){
			displayJson(false, "Unable to locate user agent record");return;
		}
		$agent = $user->agent;
		$superagentUserId = @$agent->superagent?->user?->ID;
		if(!$superagentUserId){
			$message = "The superagent code is missing. Kindly reach out to the administrator.";
			displayJson(false, $message);return;
		}

		$currentWalletBalance = $bonusWalletMeta->getWalletBalance($cashback->user_id, $cashbackLog['phone_number']);
		$deductBonusAmount = $currentWalletBalance;
		$bonusAmount = ($winner->match_sequence == 'three_unseq') ? 0 : $currentWalletBalance;
		$amountWon = $winner->amount_won + $bonusAmount;

		if($amountWon <= 0){
			$winner->redeem_status = 1;
			$winner->update();
			displayJson(false, "Your winning amount is not sufficient enough for payout");return;
		}

		$agentCommission = ($amountWon * 0.09);
		$superagentCommission = ($amountWon * 0.01);
		$payout = ($amountWon - $agentCommission - $superagentCommission);
		$payload = [
			'alert_boom' => $cashback->amount,
			'amount_won' => $amountWon,
			'agent_commission' => $agentCommission,
			'giveaway_bonus' => $bonusAmount,
			'payout' => $payout
		];

		$this->db->transBegin();

		$winnerType = $winner->match_sequence;
		$bonusWalletMeta->deductWallet($cashback->user_id, $deductBonusAmount, $cashbackLog['phone_number']);
		$bonusWallet->deductWallet($cashback->user_id, $deductBonusAmount, 'giveaway_bonus_transfer');

		$wallet->updateWallet($cashback->user_id, $payout, $winnerType); // agent customer wallet
		$wallet->updateWallet($cashback->user_id, $agentCommission, 'game_commission','wallet'); // agent commission
		$wallet->updateWallet($superagentUserId, $superagentCommission, 'game_commission','wallet'); // superagent commission
		$winner->redeem_status = 1;
		$winner->update();

		$this->db->transCommit();

		displayJson(true,'Successful',$payload);return;
	}

	public function boom_code(){
		$code = loadClass('boomcode_setting');
		$codeData = $code->getWhereNonObject(['status' => 1],$c,0,1,false);
		if($codeData){
			$payload = [
				'ID' => $codeData[0]['ID'],
				'code' => $codeData[0]['code'],
			];
			displayJson(true, 'Successful', $payload);return;
		}
		displayJson(true, 'Successful', null);return;
	}

	public function app_settings(){
		$payload = [];
		$code = loadClass('boomcode_setting');
		$giveaway = loadClass('giveaway_setting');
		$spin = loadClass('spinwheel_setting');
		$codeData = $code->getWhereNonObject(['status' => 1],$c,0,1,false);
		$giveData = $giveaway->getWhereNonObject(['status' => 1],$c,0,1,false);
		$spinData = $spin->getWhereNonObject(['spin_type' => 'jackpot'],$c,0,1,false);

		$payload['boom_code'] = $codeData ? $codeData[0]['code'] : null;
		$payload['crossover'] = $giveData ? $giveData[0]['threshold'] : 0;
		$payload['jackpot'] = $spinData ? $spinData[0]['amount'] : 0;

		displayJson(true, 'Successful', $payload);return;
	}

	public function notification_reward(){
		$customer = getCustomer();
		$bonusWallet = loadClass('bonus_wallet');
		$user = loadClass('user');
		$user->ID = $customer->user_id;
		if(!$user->load()){
			// fail gracefully
			log_message('info', "NAIRABOOM NOTIFICATION REWARD: {$customer->user_id} can't receive bonus");
			return;
		}

		$message = "Unable to receive your notification reward at the moment";
		$status = false;
		if(!$user->turn_notification){
			$amountWon = getBoomWalletAmount('turn_notification');
			$userID = $customer->user_id;
			$bonusWallet->updateWallet($userID, $amountWon, 'notification');

			$user->turn_notification = 1;
			$user->update();
			$message = "Your have received your notification reward";
			$status = true;
		}else{
			$message = "You have already received your notification reward";
		}
		displayJson($status, $message);
	}

	public function boom_code_balance(){
		$customer = getCustomer();
		$points = loadClass('boom_points');
		$userID = $customer->user_id;
		$balance = $points::totalSum('amount', " where user_id = '$userID' and status = '1'");
		$payload = [
			'user_id' => $userID,
			'amount' => $balance ?? 0
		];

		displayJson(true, 'success', $payload);
	}

	/**
	 |
	 | This endpoints are for the website
	 |
	 * @return [type] [description]
	 */
	public function fetch_clock(){
		$cashback = loadClass('cashback');

		$time_id = 1;
		// incrementing the timer if it exist already for continuous sake
		if($this->webSessionManager->getCurrentUserProp('timer_id')){
			$newTimeID = $this->webSessionManager->getCurrentUserProp('timer_id') + 1;
			$this->webSessionManager->setContent('timer_id', $newTimeID);
			$time_id = $newTimeID;
		}

		$data = $cashback->getDailyTimestamp($time_id);
		if(!$data){
			// this should not occur, and if it does, it means no data timestamp available.Hence, restart from the beginning again
			$time_id = 1;
			$data = $cashback->getDailyTimestamp($time_id);
		}
		// log the timestamp to database if it's 11:59pm UTC+1
		$cashback->logTimeStamp($data['tp_timer'],$data['percentage']);
		// initiate the timer and update it for any coming new timer
		$this->webSessionManager->setContent('timer_id', $data['time_order']);
		displayJson(true,'time clock fetched successfully',$data);return;
	}

	public function lastest_cashback_time(){
		$cashback = loadClass('cashback');
		$data = $cashback->getLastestCashbackTime();
		displayJson(true,'operation successful', $data);
	}

	public function dashboard_metrics(){
		$customer = getCustomer();
		$transaction_history = loadClass('transaction_history');
		$cashback = loadClass('cashback');
		$tranxCount = $transaction_history::totalCount("where user_id = {$customer->user_id}");
		$tranxPaid = $transaction_history->totalAmountPaid($customer->user_id);
		$cashbackHistory = $cashback->cashbackHistory($customer->user_id,$customer->user_type);

		$payload = [
			'total_transactions' => $tranxCount ?? 0,
			'total_amount_paid' => $tranxPaid ?? 0,
			'cashback_history' => $cashbackHistory
		];
		displayJson(true,'Dashboard metrics successfully fectched', $payload);
	}

	public function winnings(){
		$customer = getCustomer();
		$daily_winner = loadClass('daily_winner');
		$winnerCount = $daily_winner->totalWinCount($customer->user_id,$customer->user_type);
		$winnerHistory = $daily_winner->winnersHistory($customer->user_id,$customer->user_type);

		$payload = [
			'total_winning' => $winnerCount ?? 0,
			'winning_history' => $winnerHistory
		];
		displayJson(true,'Winnings successfully fectched', $payload);
	}

	public function contact_us(){
		$fullname = $this->request->getPost('fullname');
		$phone = $this->request->getPost('phone_number');
		$email = $this->request->getPost('email');
		$message = $this->request->getPost('message');

		if(!($fullname || $phone || $message)){
			displayJson(false, 'kindly filled the required fields');
			return;
		}
		$body = "<p>
			Dear Administrator, <br/> You have a message from <b>$fullname</b>,<br/><br/>
			<b>Email address: $email</b> <br />
			<b>Phone Number: $phone</b><br /><br/>
			<b>Message:</b> $message
		</p>";
		$response = $this->mailer->sendAdminMail($body,$fullname);
		if($response){
			displayJson(true,'Your information has been succesfully sent. We would respond shortly as soon as possible. Thank you!');
			return;
		}else{
			displayJson(false,'Oops, something went wrong when sending your information. Please try again later. Thank you.');
			return;
		}
	}

	public function auto_generated_numbers(){
		$generatedNumbers = loadClass('generated_numbers');
		$number = $generatedNumbers->getAutoRandomNumbers();
		$numbers = $number['timestamp_numbers'];
		$numbers = explode(":", $numbers);
		[$field1, $field2, $field3] = $numbers;
		$data = [
			'num_1' => $field1,
			'num_2' => $field2,
			'num_3' => $field3,
		];
		displayJson(true,'Operation successful', $data);
	}

	public function share_ads_image(){
		$ads = loadClass('share_ads');
		$ads = $ads->allNonObject();
		$payload = $ads ? $ads[0] : [];

		displayJson(true, 'Share ads fectched successfully', $payload);
	}

	/**
	 |
	 | End website endpoint
	 |
	 */

	/**
	 * [formatToUTC description]
	 * @param  string|null $date [description]
	 * @return [type]            [description]
	 */
	private function formatToUTC(string $date=null){
		return formatToUTC($date);
	}

}

?>