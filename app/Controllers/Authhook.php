<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

/**
 * This is a class that handles webhook payment for paystack and stripe
 */
class Authhook extends BaseController
{

	private function validateInterswitchHook()
	{
		if ((strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ) || !array_key_exists('HTTP_X_INTERSWITCH_SIGNATURE', $_SERVER) ){
			log_message('info','INTERSWITCH_ERROR: signature validation failed');
			echo "Oops, invalid operation";
		    exit();
		}
		$input = $this->request->getBody();
		define('INTERSWITCH_SECRET_KEY', getenv('interswitchSecretKey'));
		// validate event do all at once to avoid timing attack
		if($_SERVER['HTTP_X_INTERSWITCH_SIGNATURE'] !== hash_hmac('sha512', $input, INTERSWITCH_SECRET_KEY)){
			log_message('info','INTERSWITCH_ERROR: HmacSHA512 Alg validation failed');
		    echo "Oops, invalid operation";
		    exit();
		}
		return json_decode($input);
	}

	/**
	 * Validating bloc signature header
	 * @return [type] [description]
	 */
	private function validateBlocHook()
	{
		if ((strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ) || !array_key_exists('HTTP_X_BLOC_WEBHOOK', $_SERVER)){
			log_message('info','BLOC_ERROR: server post validation failed');
			echo "Oops, invalid operation";
		    exit();
		}
		$input = $this->request->getBody();
		define('BLOC_SECRET_KEY', getenv('blocWebHookSecret'));
		// validate event do all at once to avoid timing attack
		if($_SERVER['HTTP_X_BLOC_WEBHOOK'] !== hash_hmac('sha256', $input, BLOC_SECRET_KEY)){
			log_message('info','BLOC_ERROR: HmacSHA256 Alg validation failed');
		    echo "Oops, invalid operation";
		    exit();
		}
		return json_decode($input);
	}

	private function validateMonnifyHook(){
		if ((strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ) || !array_key_exists('HTTP_MONNIFY_SIGNATURE', $_SERVER)){
			log_message('info','MONNIFY_ERROR: server post validation failed');
			echo "Oops, invalid operation";
		    exit();
		}
		$input = $this->request->getBody();
		define('MONNIFY_SECRET_KEY', getenv('monnifySecretKey'));
		// validate event do all at once to avoid timing attack
		if($_SERVER['HTTP_MONNIFY_SIGNATURE'] !== hash_hmac('sha512', $input, MONNIFY_SECRET_KEY)){
			log_message('info','MONNIFY_ERROR: HmacSHA512 Alg validation failed');
		    echo "Oops, invalid operation";
		    exit();
		}
		return json_decode($input);
	}

	/**
	 * [logWebhook description]
	 * @test - OK
	 * @param  object $event [description]
	 * @return [type]        [description]
	 */
	private function logWebhook(object $event)
	{
		$webhook_logs = loadClass('webhook_logs');
		$webhook_logs->event = $event->event;
		$webhook_logs->data = json_encode($event);
		if(!$webhook_logs->insert()){
			log_message('info', "NAIRABOOM_WEBHOOK_LOGS: error logging the webhook");
		}
	}

	/**
	 * This is only invoke by the webhook from payment gateway
	 * @test - OK
	 * @param string $paymentType - Value to differentiate the payment gateway
	 * @return bool|string
	 */
	public function paymentTransaction(string $paymentType)
	{
		if($paymentType == 'bloc')
		{
			log_message('info', 'NAIRABOOM_BLOC_INIT: initiating payment hook');
			
			$event = $this->validateBlocHook();
			$this->logWebhook($event); // log the webhook
			
			$user_id = $this->walletBlocPayment($event);
			if($user_id){
				echo "Success on transaction";
				http_response_code(200);
				exit();
			}
			echo "Verification not successful";
			exit();
		}
		else if($paymentType == 'interswitch'){
			log_message('info', 'NAIRABOOM_INTERSWITCH_INIT: initiating payment hook');

			// $event = $this->testInterSwitchEvent('2Xdf35faAyX2Sk5Dalu405rUD');
			// $user_id = $this->verifyInterswitchPayment(json_decode($event));

			$event = $this->validateInterswitchHook();
			$this->logWebhook($event); // log the webhook
			
			$user_id = $this->verifyInterswitchPayment($event);
			if($user_id){
				echo "Success on transaction";
				http_response_code(200);
				exit();
			}
			echo "Verification not successful";
			exit();
		}
		else if($paymentType === 'monnify')
		{
			log_message('info', 'NAIRABOOM_MONNIFY_INIT: initiating payment hook');
			$event = $this->validateMonnifyHook();
			$this->logWebhook($event); // log the webhook

			$user_id = $this->walletMonnifyPayment($event);
			if($user_id){
				echo "Success on transaction";
				http_response_code(200);
				exit();
			}
			echo "Verification not successful";
			exit();
		}
	}

	/**
	 * @deprecated - Validation is occuring now on the webapimodel
	 * This validate the reference to know the user who made the transaction
	 * @param  string $ref [description]
	 * @return [type]      [description]
	 */
	private function validateInterswitchUser(string $ref)
	{
		$wallet_payment_history = loadClass('wallet_payment_history');
		$wallet_payment_history = $wallet_payment_history->getWhere(['reference_number'=>$ref],$count,0,1,false);
		if(!$wallet_payment_history){
			$this->logError1($ref,"wallet_payment_history_details_table - can't find the user wallet payment");
			return false;
		}
		return $wallet_payment_history[0];
	}

	/**
	 * This is to validate interswitch payment hook
	 * @param  object $event [description]
	 * @return [type]        [description]
	 */
	private function verifyInterswitchPayment(object $event)
	{
		if($event->event == "TRANSACTION.COMPLETED"){
			$data = $event->data;
			$payment = $this->validateInterswitchUser($data->merchantReference);
			if(!$payment){
				return false;
			}
			if($payment->payment_status == 'success'){
				return true;
			}
			$amount = $data->amount/100; // convert to naira
			if($payment->amount != $amount){
				logError1($data->merchantReference,'unable to validate the amount paid',$payment->user_id);
				return false;
			}
			$wallet = loadClass('wallet');
			$db = db_connect();
			$db->transBegin();
			$paymentDate = Time::createFromTimestamp($data->transactionDate);
			$paymentDate = $paymentDate->format('Y-m-d H:i:s');
			$paymentID = $payment->ID;
			$payment->ID = $paymentID;
			$payment->payment_status = 'success';
			$payment->payment_date = $paymentDate;
			if(!$payment->update()){
				$db->transRollback();
				logError1($data->merchantReference,"couldn't update wallet_payment_history field",$payment->user_id);
			}else{
				if(!$wallet->updateWallet($payment->user_id,$amount,'interswitch_fund','fund')){
					$this->logError1($data->merchantReference,"wallet_table - Unable to update the wallet table",$payment->user_id);
				}
			}
			$db->transCommit();
			return true;
		}
		else{
			return true;
		}
	}


	/**
	 * Verifying mono transaction using it webhook and insert data to db
	 * Hence, returning the user_id for further process
	 * @param object 	$event
	 * @return int|string
	 */
	private function walletBlocPayment(object $event)
	{
		if($event->event == 'transaction.new'){
			$data = $event->data;
			$virtualAccount = $data->account_id;
			if (!$virtualAccount) {
				log_message('info',"NAIRABOOM_BLOC_ERROR: an error occcured, cannot account ID");
				return false;
			}

			//start a database transaction here
			if ($data->drcr == 'CR') {
				$record = $this->updateWalletRecord($data);
				if (!$record) {
					log_message('info',"NAIRABOOM_BLOC_ERROR: couldn't update the wallet");
					return false;
				}
				return true;
			}

			// considering whether to use this function
			if ($data->drcr == 'DR') {
				$record = $this->updateWithdrawalRecord($data);
				if (!$record) {
					log_message('info',"NAIRABOOM_BLOC_ERROR: couldn't update the wallet");
					return false;
				}
				return true;
			}
			// else if ($data->type == 'credit' && $data->meta->transfer_type == 'virtual_account') {
			// 	$record = $this->transferToBank($data);
			// 	if (!$record) {
			// 		log_message('info',"NAIRABOOM_BLOC_ERROR: couldn't update the wallet");
			// 		return false;
			// 	}
			// 	return true;
			// }
		}
		return true;
	}

	/**
	 * This is to validate user account details
	 * @test - OK
	 * @param  [type] $account [description]
	 * @return [type]          [description]
	 */
	private function verifyUserAccount($account)
	{
		$user_kyc = loadClass('user_kyc_details');
		$user_kyc = $user_kyc->getWhere(['virtual_account'=>$account],$count,0,1,false);
		if(!$user_kyc){
			$this->logError($account,"user_kyc_details_table - can't find the user virtual_account");
			return false;
		}
		return $user_kyc[0];
	}

	private function verifyWithdrawalAccount(string $ref)
	{
		$withdrawal_request = loadClass('withdrawal_request');
		$withdrawal_request = $withdrawal_request->getWhere(['reference'=>$ref],$count,0,1,false);
		if(!$withdrawal_request){
			$this->logError($ref,"user_kyc_details_table - can't find the user virtual_account");
			return false;
		}
		return $withdrawal_request[0];
	}	

	/**
	 * [updateWithdrawFailed description]
	 * @param  object $data [description]
	 * @return [type]       [description]
	 */
	private function updateWithdrawFailed(object $data)
	{
		$wallet = loadClass('wallet');
		$user_kyc = $this->verifyUserAccount($data->account_id);
		if(!$user_kyc){
			return false;
		}
		$db = db_connect();
		$ref = $data->reference;
		$user_info = $this->verifyWithdrawalAccount($ref);
		if(!$user_info){
			return false;
		}
		// return wallet amount only if reversed
		if($data->reversal){
			$wallet->updateWallet($user_info->user_id,$user_info->amount,'bloc_reversal_fund','wallet');
		}
		$status = $data->status == 'pending' ? 'pending' : 'failed';
		$this->updateWithdrawalRequestStatus($db,$user_kyc->user_id,$ref,$status,$data,"issuing transfer(debit) failed");
		$this->updateTransactionDebitHistory($db,$user_kyc->user_id,$data->amount,0,'bloc_withdrawal');
		return true;
	}

	/**
	 * [updateWithdrawalRecord description]
	 * @test - OK
	 * @param  object $data [description]
	 * @return [type]       [description]
	 */
	private function updateWithdrawalRecord(object $data)
	{
		if($data->status == 'successful'){
			$user_kyc = $this->verifyWithdrawalAccount($data->reference);
			if(!$user_kyc){
				return false;
			}
			if($user_kyc->request_status == 'approved'){
				return true;
			}
			$db = db_connect();
			$amount = (abs($data->amount)/100); // convert to naira
			if($amount != $user_kyc->amount){
				log_message('info', "NAIRABOOM_FRAUD_ALERT: the amount is not equal");
				return false;
			}
			$ref = $data->reference;
			$this->updateWithdrawalRequestStatus($db,$user_kyc->user_id,$ref,'approved',$data,'successful');
			$this->updateTransactionDebitHistory($db,$user_kyc->user_id,$data->amount,1,'bloc_withdrawal');
			log_message('info',"NAIRABOOM_BLOC_SUCCESS: {id} {virtual_account} fund transfer",['id'=>$user_kyc->user_id,'virtual_account'=>$data->account_id]);
		}
		else{
			return $this->updateWithdrawFailed($data);
		}
		return true;
	}

	private function getTransactionChannel(string $type){
		$result = [
			'interswitch_fund' => 'interswitch',
			'bloc_fund' => 'bloc',
			'bloc_reversal_fund' => 'bloc',
			'bloc_withdrawal' => 'bloc',
		];
		if(array_key_exists($type, $result) !== false){
			return $result[$type];
		}
		return 'manual';
	}

	private function getTransactionDescription(string $type){
		$result = [
			'bloc_withdrawal' => 'Bloc withdrawal'
		];
		if(array_key_exists($type, $result) !== false){
			return $result[$type];
		}
		return null;
	}

	/**
	 * [updateTransactionDebitHistory description]
	 * @param  [type]  $db      [description]
	 * @param  [type]  $user_id [description]
	 * @param  [type]  $amount  [description]
	 * @param  integer $status  [description]
	 * @return [type]           [description]
	 */
	private function updateTransactionDebitHistory($db,$user_id,$amount,$status=1,$desc=null)
	{
		$builder = $db->table('transaction_history');
		$param = [
			'user_id' => $user_id,
			'amount' => (abs($amount)/100),
			'tranx_name' => 'withdrawal',
			'tranx_type' => 'debit',
			'status' => $status,
			'channel' => $this->getTransactionChannel($desc),
			'description' => $this->getTransactionDescription($desc)
		];
		$builder->set($param);
		$builder->insert();
	}

	/**
	 * @deprecated - ABANDONED
	 * This is to transfer from virtual account to user's provided account
	 * @param  object $data [description]
	 * @return [type]       [description]
	 */
	private function transferToBank(object $data){
		$user_info = $this->verifyWithdrawalAccount($data->reference);
		if(!$user_info){
			return false;
		}
		if($user_info && $user_info->request_status == 'approved'){ // check if already approved
			return true;
		}
		$db = db_connect();
		$user_kyc = loadClass('user_kyc_details');
		$wallet = loadClass('wallet');
		$amount = $data->amount; // already in kobo
		$ref = $user_info->reference;

		// this means that the money is in that person's virtual account from which we would
		// transfer out to the person's destination account
		$transfer = $user_kyc->transferFromMonoVirtualToAccount($amount,$user_info->bank_code,$user_info->account_number,$ref,$user_info->user_id);
		if(!$transfer){
			// return wallet amount
			if(!$wallet->updateWallet($user_info->user_id,$user_info->amount)){
				$this->logError($data->account_id,"wallet_table - Unable to fund the wallet table",$user_info->user_id);
			}
			$this->updateWithdrawalRequestStatus($db,$user_info->user_id,$ref,'failed',$data,"couldn't transfer to bank");
			$this->updateTransactionDebitHistory($db,$user_info->user_id,$amount,0);
			return false;
		}

		// this means that a request was sent to mono to credit a third party account
		$this->updateWithdrawalRequestStatus($db,$user_info->user_id,$ref,'processing',$data,'transfer in progress');
		return true;
	}

	/**
	 * [updateWithdrawalRequestStatus description]
	 * @test - OK
	 * @param  object $db      [description]
	 * @param  [type] $user_id [description]
	 * @param  [type] $ref     [description]
	 * @param  string $status  [description]
	 * @return [type]          [description]
	 */
	private function updateWithdrawalRequestStatus(object $db,$user_id,$ref,string $status='failed',?object $data=null,string $message='successful')
	{
		$data = json_encode($data);
		$builder = $db->table('withdrawal_request');
		$param = ['request_status' => $status,'request_log'=>$data,'message'=>$message];
		$builder->update($param,['user_id'=>$user_id,'reference'=>$ref]);
	}

	/**
	 * [updateWalletRecord description]
	 * @test - OK
	 * @param  object $data [description]
	 * @return [type]       [description]
	 */
	private function updateWalletRecord(object $data)
	{
		$user_kyc = $this->verifyUserAccount($data->account_id);
		if(!$user_kyc){
			return false;
		}
		$wallet_payment = loadClass('wallet_payment_history');
		$temp_wallet_payment = $wallet_payment->getWhere(['reference_number'=>$data->reference],$count,0,1,false);
		if($temp_wallet_payment && $temp_wallet_payment[0]->payment_status == 'success'){
			return true;
		}
		$amount = $data->amount/100; // convert to naira
		$paymentStatus = $data->status == 'successful' ? 'success' : 'pending';
		$db = db_connect();
		$insert = array(
			'transaction_number' => $data->id,
			'reference_number' => $data->reference,
			'reference_hash' => generateNumericRef($db,'wallet_payment_history','reference_hash','WAF'),
			'user_id' => $user_kyc->user_id,
			'payment_status' => $paymentStatus,
			'date_created' => formatToUTC(),
			'payment_date' => $data->created_at,
			'payment_channel' => 'bloc',
			'amount' => $amount,
			'payment_method' => $data->source,
			'payment_log' => json_encode($data)
		);
		$wallet = loadClass('wallet');
		$item = new $wallet_payment($insert);
		if (!$item->insert()) {
			$this->logError($data->account_id,"wallet_payment_history_table - there is a problem funding the wallet",$user_kyc->user_id);
			return false;
		}

		if($data->status == 'successful'){
			$db->transBegin();
			if(!$wallet->updateWallet($user_kyc->user_id,$amount,'bloc_fund','fund')){
				$this->logError($data->account_id,"wallet_table - Unable to fund the wallet table",$user_kyc->user_id);
				return false;
			}
			$db->transCommit();

			if(!$wallet->agentFirstWalletPayment($user_kyc->user_id,$amount)){
				$this->logError($data->account_id,"Unable to fund superagent commission",$user_kyc->user_id);
				// fail gracefully
			}
		}

		// $issuing_log = $db->table('issuing_wallet_logs');
		// $param = ['user_id' => $user_kyc->user_id, 'amount'=> $amount, 'status'=>'1'];
		// $transfer = $user_kyc->transferToIssuingWallet($amount*100,$user_kyc->user_id);
		// if(!$transfer){
		// 	$this->logError($data->account_id,"issuing_wallet - Unable to transfer fund to issuing wallet",$user_kyc->user_id);
		// 	$param['status'] = '0';
		// 	$issuing_log->insert($param);
		// 	return false;
		// }
		// $issuing_log->insert($param);
		log_message('info',"NAIRABOOM_BLOC_SUCCESS: {id} {virtual_account} wallet funded",['id'=>$user_kyc->user_id,'virtual_account'=>$data->account_id]);
		return true;
	}

	/**
	 * This logs error on mono hook
	 * @param  string   $id      [description]
	 * @param  string   $message [description]
	 * @param  int|null $user_id [description]
	 * @return [type]            [description]
	 */
	private function logError(string $id,string $message,?int $user_id=null)
	{
		log_message('info',"NAIRABOOM_BLOC_ERROR: {id} {virtual_account} $message",['id'=>$user_id,'virtual_account'=>$id]);
	}

	/**
	 * This logs error on interswitch hook
	 * @param  string   $ref     [description]
	 * @param  string   $message [description]
	 * @param  int|null $user_id [description]
	 * @return [type]            [description]
	 */
	private function logError1(string $ref,string $message,?int $user_id=null)
	{
		log_message('info',"NAIRABOOM_INTERSWITCH_ERROR: {id}-{ref} $message",['id'=>$user_id,'ref'=>$ref]);
	}

	private function walletMonnifyPayment(object $event)
	{
		if($event->eventType == 'SUCCESSFUL_TRANSACTION'){
			$data = $event->eventData;
			$record = $this->updateMonnifyWalletRecord($data);
			if (!$record) {
				log_message('info',"NAIRABOOM_MONNIFY_ERROR: couldn't update the wallet");
				return false;
			}
			return true;
		}
		return true;
	}

	private function updateMonnifyWalletRecord(object $data){
		$user_kyc = $this->verifyUserAccount($data->product->reference);
		if(!$user_kyc){
			return false;
		}
		$wallet_payment = loadClass('wallet_payment_history');
		$bonusWallet = loadClass('bonus_wallet');
		$temp_wallet_payment = $wallet_payment->getWhere(['reference_number'=>$data->paymentReference],$count,0,1,false);
		if($temp_wallet_payment && $temp_wallet_payment[0]->payment_status == 'success'){
			return true;
		}
		$amount = $data->amountPaid; // convert to naira
		$paymentStatus = 'success';
		$db = db_connect();
		$insert = array(
			'transaction_number' => $data->transactionReference,
			'reference_number' => $data->paymentReference,
			'reference_hash' => generateNumericRef($db,'wallet_payment_history','reference_hash','WAF'),
			'user_id' => $user_kyc->user_id,
			'payment_status' => $paymentStatus,
			'date_created' => formatToUTC(),
			'payment_date' => $data->paidOn,
			'payment_channel' => 'bloc',
			'amount' => $amount,
			'payment_method' => $data->paymentMethod,
			'payment_log' => json_encode($data)
		);
		$wallet = loadClass('wallet');
		$item = new $wallet_payment($insert);
		if (!$item->insert()) {
			$this->logError($data->product->reference,"wallet_payment_history_table - there is a problem funding the wallet",$user_kyc->user_id);
			return false;
		}

		$db->transBegin();
		if(!$wallet->updateWallet($user_kyc->user_id,$amount,'monnify_fund','fund')){
			$this->logError($data->product->reference,"wallet_table - Unable to fund the wallet table",$user_kyc->user_id);
			return false;
		}
		$db->transCommit();

		if(!$wallet->customerFirstWalletPayment($user_kyc->user_id)){
			$customerFirstFund = getBoomWalletAmount('first_wallet_funding');
			$bonusWallet->updateWallet($user_kyc->user_id, $customerFirstFund, 'customer_first_wallet_pay');
		}

		if(!$wallet->agentFirstWalletPayment($user_kyc->user_id,$amount)){
			$this->logError($data->product->reference,"Unable to fund superagent commission",$user_kyc->user_id);
			// fail gracefully
		}

		log_message('info',"NAIRABOOM_MONNIFY_SUCCESS: {id} {virtual_account} wallet funded",['id'=>$user_kyc->user_id,'virtual_account'=>$data->product->reference]);
		return true;
	}

}
