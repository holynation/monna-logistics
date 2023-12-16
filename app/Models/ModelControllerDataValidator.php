<?php 

/**
* The controller that validate forms that should be inserted into a table based on the request url.
each method wil have the structure validate[modelname]Data
*/
namespace App\Models;

class ModelControllerDataValidator
{

	public function __construct(){
		helper('string');
	}

	public function validateSuper_agentData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			
		}
		return true;
	}

	public function validateInfluencerData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			
		}
		return true;
	}

	public function validateUserData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			$data['user_type'] = 'nlrc';
			$data['user_table_id'] = 0;
			$data['password'] = encode_password($data['password']);
		}

		return true;
	}

	public function validateCashbackData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			$validation = \Config\Services::validation();
			$customer = getCustomer();
			$wallet = loadClass('wallet');
			$bonusWallet = loadClass('bonus_wallet');
			$cashback = loadClass('cashback');
			$cashbackType = 'customer';
			if($customer->user_type == 'agent'){
				$validationData = $data;
				$validation->setRule('cust_fullname', 'customer fullname', 'required');
				$validation->setRule('cust_phone_number', 'customer phone number', 'required');
				$validation->setRule('bank_code', 'customer bank code', 'required');

				if (!$validation->run($validationData)) {
					$errors = $validation->getErrors();
					foreach($errors as $error){
						$message = $error;
						return false;
					}
				}

				$currrentBalance = $wallet->getWalletBalance($customer->user_id);
				if(!$agent->first_wallet_pay && $currrentBalance < 5000){
					$message = "Please fund your account with more than 5000 as your first wallet funding";
					return false;
				}

				unset($data['customer_id']);
				$cashbackType = 'agent';
			}

			$validationData = $data;
			// checks for first wallet funding minimum using cashback since you won't be able to play
			// without funding your wallet
			$cashbackFirst = $cashback->getWhere(['user_id' => $customer->user_id],$cashbackCount,0,null,false);
			if(!$cashbackFirst ){
				$currentWalletBalance = $wallet->getWalletBalance($customer->user_id);
				$validationData = array_merge($validationData, ['first_wallet_pay' => $currentWalletBalance]);
			}

			$customer->user_type == 'customer' ? $validation->setRule('game_type', 'game type', 'required|in_list[normal,check_in,cashout]') : null;
			$data['game_type'] != 'cashout' ? $validation->setRule('alert_type', 'alert type', 'required|in_list[credit,debit]') : null;
			$validation->setRule('amount', 'amount', 'required|is_natural_no_zero');
			(!$cashbackFirst) ? $validation->setRule('first_wallet_pay', 'first wallet funding', 'required|greater_than_equal_to[500]', [
				'greater_than_equal_to' => 'First wallet funding minimum is 500 Naira. Kindly top up your wallet.'
			]) : null;

			if (!$validation->run($validationData)) {
				$errors = $validation->getErrors();
				foreach($errors as $error){
					$message = $error;
					return false;
				}
			}

			$dateReceiveAlert = (isset($data['date_received_alert']) && $data['date_received_alert']) ? formatToUTC($data['date_received_alert']) : date('Y-m-d H:i:s');
			if($data['game_type'] == 'normal'){
				$data['amount'] = str_replace(',', '', $data['amount']);
				$currentWalletBalance = $wallet->getWalletBalance($customer->user_id);
				$deductAmount = $wallet->calculateStakeAmount($data['amount']);
				if($currentWalletBalance <= 0 || $deductAmount > $currentWalletBalance){
					$message = "Oops, you don't have enough amount in your wallet. Please fund your wallet";
					return false;
				}

				$data['stake_amount'] = str_replace(',', '', $data['stake_amount']);
				if($deductAmount != round($data['stake_amount'], 2)){
					$message = "Oops, stake amount is not accurate";
					return false;
				}
			}

			if($data['game_type'] == 'check_in'){
				$currentWalletBalance = $wallet->getWalletBalance($customer->user_id);
				if($currentWalletBalance > 200){
					$message = "You have more than enough to stake your game, kindly go back and play";
					return false;
				}
				$deductAmount = getBoomWalletAmount('checkin_deduct_amount');
			}

			if($data['game_type'] == 'cashout'){
				$currentBonusWalletBalance = $bonusWallet->getWalletBalance($customer->user_id);
				if($data['amount'] != $currentBonusWalletBalance){
					$message = "Oops, boom wallet in amount field is not accurate";
					return false;
				}
				$deductAmount = round((0.02 * $currentBonusWalletBalance), 2);
				$data['stake_amount'] = str_replace(',', '', $data['stake_amount']);
				if($deductAmount != round($data['stake_amount'], 2)){
					$message = "Oops, stake amount is not accurate";
					return false;
				}
				$data['alert_type'] = 'credit';
				$data['bank_lists_id'] = 00;
			}
			
			$data['date_received_alert'] = $dateReceiveAlert;
			$data['deducted_amount'] = $deductAmount;
			$data['cashback_type'] = $cashbackType;
			$data['date_created'] = formatToUTC();
			$data['reference_hash'] = generateNumericRef($db,'cashback','reference_hash','BA');
			
			// print_r($data);exit;
		}
		return true;
	}

	public function validateWithdrawal_requestData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			if($data){
				$customer = getCustomer();
				$wallet = loadClass('wallet');
				$data['amount'] = str_replace(',','',$data['amount']);
				// validate the amount in the user wallet
				$currentWalletBalance = $wallet->getWalletBalance($customer->user_id);
				$serviceCharge = 150;
				$deductAmount = $data['amount'] + $serviceCharge;

				if($currentWalletBalance <= 0 || $deductAmount > $currentWalletBalance){
					$message = "Oops, you don't have enough amount in your wallet";
					return false;
				}
				$data['reference'] = generateNumericRef($db,'withdrawal_request','reference','WAD');
				$data['amount'] = $deductAmount;
			}
		}
		return true;
	}

	public function validateSpinwheel_settingData(&$data,$type,&$db,&$message)
	{
		if($type == 'update'){
			$spinsetting = $db->query("SELECT * from spinwheel_setting where spin_type = 'jackpot' limit 1");
			$spinsetting = $spinsetting->getRow();

			if($spinsetting->ticket_cycle != $data['ticket_cycle']){
				$data['updateSetting'] = true;
			}
		}
		return true;
	}

	public function validateBoomcode_settingData(&$data,$type,&$db,&$message)
	{
		$validation = \Config\Services::validation();
		$validationData = $data;
		$validation->setRule('code', 'code', 'required|regex_match[/(\d\d)::(\d\d)::(\d\d)::(\d\d)/]', [
			'regex_match' => 'boom code is not in the correct format'
		]);

		if (!$validation->run($validationData)) {
			$errors = $validation->getErrors();
			foreach($errors as $error){
				$message = $error;
				return false;
			}
		}

		return true;
	}

}


?>