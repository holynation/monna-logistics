<?php

/**
* model for loading extra data needed by pages through ajax
*/
namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Entities\Superagent;
use CodeIgniter\Config\Factories;
use Exception;

class Ajaxdata extends BaseController
{
	private $webSessionManager = null;
	public function __construct()
	{
		$this->webSessionManager = new WebSessionManager;
		$exclude = array('changePassword','savePermission','approvePayment');
		$page = $this->getMethod($segments);
		if ($this->webSessionManager->getCurrentUserProp('user_type') == 'admin' && in_array($page, $exclude)) {
			$role = loadClass('role');
			$role->checkWritePermission();
		}
	}

	private function getMethod(&$allSegment)
	{
		$path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$base = base_url();
		$left = ltrim($path,$base);
		$result = explode('/', $left);
		$allSegment = $result;
		return $result[0];
	}

	private function returnJSONTransformArray($query,$data=array(),$valMessage='',$errMessage=''){
		$newResult=array();
		$result = $this->db->query($query,$data);
		if($result->getNumRows() > 0){
			$result = $result->getResultArray();
			if($valMessage != ''){
				$result[0]['value'] = $valMessage;
			}
			return json_encode($result[0]);
		}else{
			if($errMessage != ''){
				$dataParam = array('value' => $errMessage);
				return json_encode($dataParam);
			}
			return json_encode(array());
		}
	}

	private function returnJSONFromNonAssocArray($array){
		//process if into id and value then
		$result =array();
		for ($i=0; $i < count($array); $i++) {
			$current =$array[$i];
			$result[]=array('id'=>$current,'value'=>$current);
		}
		return json_encode($result);
	}

	protected function returnJsonFromQueryResult($query,$data=array(),$valMessage='',$errMessage=''){
		$result = $this->db->query($query,$data);
		if ($result->getNumRows() > 0) {
			$result = $result->getResultArray();
			if($valMessage != ''){
				$result[0]['value'] = $valMessage;
			}
			// print_r($result);exit;
			return json_encode($result);
		}
		else{
			if($errMessage != ''){
				$dataParam = array('value' => $errMessage);
				return json_encode($dataParam);
			}
			return "";
		}
	}

	public function savePermission()
	{	
		if (isset($_POST['role'])) {
			$role = $_POST['role'];
			if (!$role) {
				echo createJsonMessage('status',false,'message','Error occured while saving permission','flagAction',false);
			}
			$newRole = loadClass('role');
			try {
				$removeList = json_decode($_POST['remove'],true);
				$updateList = json_decode($_POST['update'],true);
				$newRole->ID = $role;
				$result=$newRole->processPermission($updateList,$removeList);
				echo createJsonMessage('status',$result,'message','Permission updated successfully','flagAction',true);
			} catch (Exception $e) {
				echo createJsonMessage('status',false,'message','Error occured while saving permission','flagAction',false);
			}
			
		}
	}

	public function approvePayment()
	{	
		if (isset($_POST['update'])) {
			$update = $_POST['update'];
			if (!$update) {
				echo createJsonMessage('status',false,'message','Error occured while approving payment','flagAction',false);
			}
			$wallet_payment_history = loadClass('wallet_payment_history');
			try {
				$updateList = json_decode($_POST['update'],true);
				$result = $wallet_payment_history->processVerification($updateList);
				$message = $result ? "Payment successfully approved" : "Error occured";
				echo createJsonMessage('status',$result,'message',$message,'flagAction',$result);
			} catch (Exception $e) {
				echo createJsonMessage('status',false,'message','Error occured while approving payment','flagAction',false);
			}
			
		}
	}

	private function createFundTransaction($user_id,$amount,$type,$channel,$status=1,$tranx='fund',$desc=null)
	{
		$builder = $this->db->table('transaction_history');
		$param = [
			'user_id' => $user_id,
			'amount' => $amount,
			'tranx_name' => $tranx,
			'tranx_type' => $type,
			'channel' => $channel,
			'status' => $status,
			'description' => $desc
		];
		$this->db->transBegin();
		$builder->set($param);
		if(!$builder->insert()){
			$this->db->transRollback();
			return false;
		}
		$this->db->transCommit();
		return true;
	}

	private function createWalletHistory($user_id,$amount,$message,$ref=null){
		$wallet_payment = loadClass('wallet_payment_history');
		$this->db->transBegin();
		$wallet_pay = $wallet_payment->getWhere(['reference_number'=>$ref],$count,0,1,false);
		if($wallet_pay){
			$wallet_pay = $wallet_pay[0];
			if($wallet_pay == 'success'){ // already approved
				return false;
			}
			$wallet_pay->payment_status = 'success';
			$wallet_pay->transaction_message = 'Approved by admin';
			if(!$wallet_pay->update()){
				$this->db->transRollback();
				return false;
			}
			$this->db->transCommit();
			return true;
		}else{
			$payRef = generateHashRef('reference');
			$insert = array(
				'transaction_number' => $payRef,
				'reference_number' => $payRef,
				'reference_hash' => generateNumericRef($this->db,'wallet_payment_history','reference_hash','WAF'),
				'user_id' => $user_id,
				'payment_status' => 'success',
				'date_created' => formatToUTC(),
				'payment_date' => formatToUTC(),
				'payment_channel' => 'manual',
				'amount' => $amount,
				'gateway_reference' => 'admin',
				'payment_method' => 'manual',
				'transaction_message' => $message
			);
			$item = new $wallet_payment($insert);
			if (!$item->insert()) {
				$this->db->transRollback();
				return false;
			}
			$this->db->transCommit();
			return true;
		}
	}

	private function updateWithdrawStatus($ref,$fullname){
		$withdrawal_request = loadClass('withdrawal_request');
		$this->db->transBegin();

		$withdrawal = $withdrawal_request->getWhere(['reference'=>$ref],$count,0,1,false);
		if($withdrawal){
			$withdrawal = $withdrawal[0];
			$withdrawal->request_status = 'approved';
			$withdrawal->message = "Approved by admin {$fullname}";
			if(!$withdrawal->update()){
				$this->db->transRollback();
				return false;
			}
			$this->db->transCommit();
			return true;
		}
	}

	public function updateAccountWallet(string $type){
		$amount = trim($this->request->getPost('amount'));
		$userID = $this->request->getPost('user_id');
		$pageType = $this->request->getPost('pageType');
		$pageVal = $this->request->getPost('pageVal');

		if(!$amount){
			echo createJsonMessage('status',false,'message','Please supply wallet amount','flagAction',false);
			return;
		}
		$amount = str_replace(',', '', $amount);
		$wallet = loadClass('wallet');
		if($type === 'deduct'){
			$fullname = $this->webSessionManager->getCurrentUserProp('firstname').' '.$this->webSessionManager->getCurrentUserProp('lastname');

			$currentWalletBalance = $wallet->getWalletBalance($userID);
			if($currentWalletBalance <= 0 or $amount > $currentWalletBalance){
				$message = "Oops, there's no enough amount in the wallet";
				echo createJsonMessage('status',false,'message',$message,'flagAction',false);
				return false;
			}

			if($pageType == 'withdrawal'){
				// no need to deduct since it's removed at inception
				if($this->updateWithdrawStatus($pageVal,$fullname)){
					$this->createFundTransaction($userID,$amount,'debit','manual',1,'withdrawal','Admin payment withdrawal');
				}
			}
			else{
				$this->createWalletHistory($userID,$amount,"Deduct from wallet by {$fullname}",$pageVal);
				// $this->createFundTransaction($userID,$amount,'debit','manual',1);

				if(!$wallet->deductWallet($userID,$amount,'admin_withdrawal','withdrawal')){
					echo createJsonMessage('status',false,'message','Something went wrong','flagAction',false);
					return;
				}
			}
			echo createJsonMessage('status',true,'message','Wallet successfully deducted','flagAction',true);
			return;
		}
		else if($type === 'fund'){
			$fullname = $this->webSessionManager->getCurrentUserProp('firstname').' '.$this->webSessionManager->getCurrentUserProp('lastname');
			$history = $this->createWalletHistory($userID,$amount,"Funded wallet by {$fullname}",$pageVal);

			if($history){
				if(!$wallet->updateWallet($userID,$amount,'admin_fund','manual')){
					echo createJsonMessage('status',false,'message','Something went wrong','flagAction',false);
					return;
				}
				// $this->createFundTransaction($userID,$amount,'credit','manual',1);
			}
			echo createJsonMessage('status',true,'message','Wallet successfully funded','flagAction',true);
			return;
		}
	}

	public function validateUserAccount($userID){
		$user = loadClass('user');
		$user->ID = $userID;
		$superagent = null;
		if(!$user->load()){
			return false;
		}
		$userType = $user->user_type;
		if(!$userType = $user->$userType){
			return false;
		}
		return $userType;
	}

	public function createAccountHolder(){
		$user_kyc = loadClass('user_kyc_details');
		$user = loadClass('user');
		$userID = $this->request->getPost('user_id');
		$userType = $this->request->getPost('user_type');
		$user_kyc = $user_kyc->getWhere(['user_id'=>$userID],$count,0,1,false);
		if(!$user_kyc){
			echo createJsonMessage('status',false,'message',"Oops, your account not validated yet.");
			return;
		}
		$user_kyc = $user_kyc[0];
		$fullname = null;
		$phone = null;
		$email = null;
		$bvnNumber = $user_kyc->bvn_number;
		if($userType == 'customer'){
			$fullname = $user_kyc->user->customer->fullname;
			$phone = $user_kyc->user->customer->phone_number;
			$email = $user_kyc->user->customer->email;
		}
		else if($userType == 'agent'){
			$fullname = $user_kyc->user->agent->fullname;
			$phone = $user_kyc->user->agent->phone_number;
			$email = $user_kyc->user->agent->email;
		}

		if(!$accounts = $user_kyc->createMonoAccount($fullname,$phone,$bvnNumber,$userID,$email,$paymentResponse)){
			echo createJsonMessage('status',false,'message',"Error: {$paymentResponse}");
			return;
		}
		if($accounts[0] == '' && $accounts[1] == ''){
			echo createJsonMessage('status',false,'message',"Something went wrong while validating account");
			return;
		}
		$this->db->transBegin();
		$user_kyc->ID = $accounts[2];
		$user_kyc->status = 1;

		if(!$user_kyc->update()){
			$this->db->transRollback();
			echo createJsonMessage('status',false,'message',"Something went wrong while validating BVN");
			return;
		}
		$this->db->transCommit();
		echo createJsonMessage('status',true,'message',"Account has been successfully created");
		return;
	}

	public function validateBvnByMono(){
		$user_kyc = loadClass('user_kyc_details');
		$validation = \Config\Services::validation();

		$bvnNumber = $this->request->getPost('bvn_number');
		$userID = $this->request->getPost('userID');
		$email = $this->request->getPost('email');
		$accountNumber = $this->request->getPost('account_number');
		$bankCode = $this->request->getPost('bank_code');

		$validation->setRules([
			'bvn_number' => 'required|numeric',
			'email' => 'required|valid_email',
			'account_number' => 'required|numeric',
			'bank_code' => 'required',
		]);
		if(!$validation->run($this->request->getPost(null))){
			$errors = $validation->getErrors();
			foreach($errors as $error){
				displayJson(false, $error);
				return;
			}
		}
		$monnify = Factories::libraries('Monnify');
		$bankLists = loadClass('bank_lists');
		$userBanks = loadClass('user_banks');
		$user_kyc = loadClass('user_kyc_details');

		$superagent = null;
		if(!$superagent = $this->validateUserAccount($userID)){
			echo createJsonMessage('status',false,'message',"Sorry, account can't be validated");
			return;
		}
		$superagent->user_type = 'superagent';
		$superagent->user_id = $userID;
		$_SERVER['current_user'] = $superagent;
		$customer = getCustomer();

		if($user_kyc->getWhere(['user_id'=>$userID,'bvn_status'=>'1'],$count,0,null,false)){
			displayJson(true, "Your account has been validated already");
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

	public function withdrawalRequest(){
		$wallet = loadClass('wallet');
		$user_kyc = loadClass('user_kyc_details');
		$user = loadClass('user');
		$monnify = Factories::libraries('Monnify');

		$accountNumber = $this->request->getPost('account_number');
		$bankCode = $this->request->getPost('bank_name');
		$userID = $this->request->getPost('userID');
		$amount = str_replace(',','',$this->request->getPost('amount'));
		$accountName = null;

		$userType = null;
		if(!$userType = $this->validateUserAccount($userID)){
			echo createJsonMessage('status',false,'message',"Sorry, account can't be validated");
			return;
		}

		// validate the amount in the user wallet
		$currentWalletBalance = $wallet->getWalletBalance($userID);
		if($currentWalletBalance <= 0 or $amount > $currentWalletBalance){
			$message = "Oops, you don't have enough amount in your wallet";
			echo createJsonMessage('status',false,'message',$message);
			return;
		}

		$reference = generateNumericRef($this->db,'withdrawal_request','reference','WAD');;
		// validating account number
		if(!$accountName = $monnify->getAccountName($bankCode,$accountNumber,$userID)){
			$message = "Account number can't be validated";
			echo createJsonMessage('status',false,'message',$message);
			return;
		}
		$builder = $this->db->table('withdrawal_request');
		$param = [
			'user_id' => $userID,
			'reference' => $reference,
			'amount' => $amount,
			'account_number' => $accountNumber,
			'account_name' => $accountName,
			'bank_code' => $bankCode,
			'request_status' => 'pending',
		];
		$builder->insert($param);

		// deduct the amount from the user wallet
		$wallet->deductWallet($monnify,$userID,$amount,'monnify_withdrawal','withdrawal','0');

		if(!$this->userWithdrawal($amount,$reference,$userID,$bankCode,$accountNumber)){
			$message = "Unable to withdrawal fund at the moment";
			echo createJsonMessage('status',false,'message',$message);
			return;
		}
		$message = "Your transfer is being processed";
		echo createJsonMessage('status',true,'message',$message);
		return;
	}

	private function userWithdrawal(object $monnify,$amount,$ref,$userID,$bankCode,$accountNumber){
		$amount = $amount * 100; // in kobo
		$transfer = null;
		$transfer = $monnify->withdrawalPayment($amount,$ref,$userID,$bankCode,$accountNumber,$response);
		if(!$transfer){
			// fail gracefully
			$builder = $this->db->table('withdrawal_request');
			$builder->update(['message' => $response],['reference'=>$ref]);
			return false;
		}
		$builder = $this->db->table('withdrawal_request');
		$param = [
			'transfer_ref' => $transfer // the return id of mono endpoint
		];
		$builder->update($param,['reference'=>$ref]);
		return true;
	}

	private function userWithdrawalOld(object $user_kyc,$amount,$ref,$userID,$bankCode,$accountNumber){
		$amount = $amount * 100; // in kobo
		$transfer = null;
		$transfer = $user_kyc->transferFromMonoIssueToVirtual($amount,$ref,$userID,$bankCode,$accountNumber,$response);
		if(!$transfer){
			// fail gracefully
			$builder = $this->db->table('withdrawal_request');
			$builder->update(['message' => $response],['reference'=>$ref]);
			return false;
		}
		$builder = $this->db->table('withdrawal_request');
		$param = [
			'transfer_ref' => $transfer // the return id of mono endpoint
		];
		$builder->update($param,['reference'=>$ref]);
		return true;
	}

	public function appSettings(){
		$validation = \Config\Services::validation();
		$settings = loadClass('settings');
	  	$min_withdrawal = $this->request->getPost('min_withdrawal');

	  	if(!$this->validate([
	  		'min_withdrawal' => [
	  			'label' => 'minimum withdrawal',
	  			'rules' => 'required|numeric'
	  		],
	  		'auto_withdrawal' => [
	  			'label' => 'automatic withdrawal',
	  			'rules' => 'permit_empty',
	  		],
	  	])){
	  		$errors = $this->validator->getErrors();
	  		foreach($errors as $error){
	  			echo createJsonMessage('status',false,'message', $error);return;
	  		}
	  	}
	  	$validData = $this->validator->getValidated();
	  	$settings_data = array( 
			'min_withdrawal'	=> $validData['min_withdrawal'],
			'auto_withdrawal'	=> @$validData['auto_withdrawal'] ?: 0,
		);
		
		// check if create method was successful
		$settings->registerSettings($settings_data);
		echo createJsonMessage('status',true,'message', 'Settings saved successfully');return;
	}
	
}
