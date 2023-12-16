<?php 
	/**
	* This is the class that contain the method that will be called whenever any data is inserted for a particular table.
	* the url path should be linked to this page so that the correct operation is performed ultimately. T
	*/
namespace App\Models;

use App\Models\WebSessionManager;
use App\Models\Sms;

class ModelControllerCallback
{
	private $webSessionManager;

	function __construct()
	{
		helper(['string','url','array']);
		$this->webSessionManager = new WebSessionManager;
	}

	public function onAdminInserted($data,$type,&$db,&$message)
	{
		//remember to remove the file if an error occured here
		//the user type should be admin
		$user = loadClass('user');
		if ($type == 'insert') {
			// login details as follow: username = email, password = firstname(in lowercase)
			$password = encode_password(strtolower($data['firstname']));
			$param = array('user_type'=>'admin','username'=>$data['email'],'username_2'=>'08109994485','password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID']);
			$std = new $user($param);
			if ($std->insert($db,$message)) {
				return true;
			}
			return false;
		}
		return true;
	}

	public function onSuperagentInserted($data,$type,&$db,&$message)
	{
		//remember to remove the file if an error occured here
		//the user type should be admin
		$user = loadClass('user');
		if ($type == 'insert') {
			// login details as follow: username = email, password = firstname(in lowercase)
			[$firstname, $lastname] = explode(' ',strtolower(trim($data['fullname'])));
			$password = encode_password($firstname);
			$username2 = @$data['phone_number'] ?? null;
			$param = array('user_type'=>'superagent','username'=>$data['email'],'username_2'=>$username2,'password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID']);
			$std = new $user($param);
			if ($std->insert($db,$message)) {
				return true;
			}
			return false;
		}
		return true;
	}

	public function onInfluencerInserted($data,$type,&$db,&$message)
	{
		//remember to remove the file if an error occured here
		//the user type should be admin
		$user = loadClass('user');
		if ($type == 'insert') {
			// login details as follow: username = email, password = firstname(in lowercase)
			[$firstname, $lastname] = explode(' ',strtolower(trim($data['fullname'])));
			$password = encode_password($firstname);
			$username2 = @$data['phone_number'] ?? null;
			$param = array('user_type'=>'influencer','username'=>$data['email'],'username_2'=>$username2,'password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID']);
			$std = new $user($param);
			if ($std->insert($db,$message)) {
				return true;
			}
			return false;
		}
		return true;
	}

	public function onCashbackInserted($data,$type,&$db,&$message,&$extra)
	{
		if($type == 'insert'){
			$customer = getCustomer();
			$wallet = loadClass('wallet');
			$bonusWallet = loadClass('bonus_wallet');
			$user_kyc = loadClass('user_kyc_details');
			$lastInsertId = $data['LAST_INSERT_ID'];
			$deductAmount = $data['deducted_amount'];
			$user_id = $customer->user_id;
			$checkInAmount = 0;
			// deduct amount from wallet
			if($data['game_type'] == 'cashout'){
				if($deductAmount = $bonusWallet->deductWallet($user_id,$deductAmount,'cashout')){
					if($deductAmount <= 0){
						// update the table to zero has a precaution measure
					}
				}
			}
			else{
				if($deductAmount = $wallet->deductWallet($user_id,$deductAmount,'game_withdrawal','wallet')){
					if($deductAmount <= 0){
						// update the table to zero has a precaution measure
					}
				}
			}

			if($customer->user_type == 'customer'){
				$gameRefStatus = 0;
				if($data['game_type'] == 'check_in'){
					$gameRefStatus = 1;
				}
				$param = [
					'cashback_id' => $lastInsertId,
					'fullname' => $customer->fullname,
					'phone_number' => $customer->phone_number,
					'email' => isset($customer->email) ? $customer->email : null,
					'date_created' => formatToUTC(),
					'game_ref' => generateHashRef('reference', 31),
					'game_ref_status' => $gameRefStatus,
					'game_type' => $data['game_type'],
					'checkin_amount' => $checkInAmount
				];

				if($data['game_type'] == 'check_in'){
					$checkInAmount = $this->handleCheckInBonus($data['amount']);
					$param['checkin_amount'] = $checkInAmount;
					$bonusWallet->updateWallet($user_id,$checkInAmount,'check_in');
				}
				$this->handleCashbackBonuses($db, $user_id, $data);
			}
			else if($customer->user_type == 'agent'){
				$param = [
					'cashback_id' => $lastInsertId,
					'fullname' => $data['cust_fullname'],
					'phone_number' => $data['cust_phone_number'],
					'email' => isset($data['cust_email']) ? $data['cust_email'] : null,
					'date_created' => formatToUTC(),
					'account_number' => @$data['account_number'] ?? null,
					'bank_code' => $data['bank_code'],
					'game_ref' => generateHashRef('reference', 31),
					'game_ref_status' => 0,
				];
				if(!$this->createCommission($db,$customer->ID,$customer->user_id,$data['deducted_amount'],$message))
				{
					log_message('info',"NAIRABOOM_COMMISSION_ERROR: cashback-$lastInsertId commission not inserted");
					return false;
				}
			}

			$builder = $db->table('cashback_log');
			$builder->insert($param);
			$message = $data['game_type'] == 'check_in' ? "You have successfully check-in your alert" : "You have successfully staked your alert";
			$param['reference_hash'] = $data['reference_hash'];
			$extra = $param;

			$this->processInfluncerEarning();
		}

		return true;
	}

	private function handleGamePlayBonus($count){
		$calc = ($count % 5 == 0) ? true : false;
		return $calc;
	}

	private function handleCheckInBonus($amount){
		$calcValue = getBoomWalletAmount('checkin');
		$amount = ($amount <= 100000) ? $amount * $calcValue : 10000;
		return $amount;
	}

	private function handleCashbackBonuses($db,$userId,$data){
		$referral = loadClass('referral');
		$bonusWallet = loadClass('bonus_wallet');
		$cashback = loadClass('cashback');
		$referee = $referral->getWhere(['referee' => $userId],$count,0,null,false);
		$cashback = $cashback->getWhere(['user_id' => $userId],$cashbackCount,0,null,false); // first user play
		if(count($cashback) == 1 && $referee){
			$referee = $referee[0];
			$referrer = $referee->referrer;
			$refBonus = getBoomWalletAmount('referral_code');
			$bonusWallet->updateWallet($referrer,$refBonus,'referral');
		}

		if($cashbackCount && $this->handleGamePlayBonus($cashbackCount)){
			$gameBonus = getBoomWalletAmount('five_times_game');
			$bonusWallet->updateWallet($userId,$gameBonus,'cashback');
		}

	}

	private function createCommission(object $db,$agentId,$userId,$amount,&$message=null){
		$agent = loadClass('agent');
		$wallet = loadClass('wallet');
		$agent->ID = $agentId;
		$agent->load();
		$superagentUserId = $agent->superagent?->user?->ID;
		if(!$superagentUserId){
			$message = "The superagent code is missing. Kindly reach out to the administrator.";
			log_message('info', "NAIRABOOM_SUPERAGENTCODE_ERROR: Superagent is missing for user: {$userId}");
			return false;
		}

		$agentAmount = $amount*0.1; // 10 percent
		$superagentAmount = $amount*0.02; // 2 percent
		$date = formatToUTC();
		if(!$wallet->updateWallet($superagentUserId,$superagentAmount,'game_commission','wallet')){
			log_message('info',"NAIRABOOM_WALLET_ERROR: can't update wallet table-$superagentUserId");
		}
		if(!$wallet->updateWallet($userId,$agentAmount,'game_commission','wallet')){
			log_message('info',"NAIRABOOM_WALLET_ERROR: can't update wallet table-$userId");
		}
		// REMARK: thinking of removing this and write sql to calc it on cashback table for commission earned on each ticket by the agent
		// although tracking the amount might not be possible
		$query = "insert into commission (user_id,amount,date_created,user_type,orig_amount) values ('$userId','$agentAmount','$date','agent', '$amount' ), ('$superagentUserId','$superagentAmount','$date','superagent', '$amount' )";
		if(!$db->query($query)){
			return false;
		}
		return true;
	}

	private function processInfluncerEarning(){
		$cashback = loadClass('cashback');
		$wallet = loadClass('wallet');
		$users = $cashback->getAllUserInfluencer();
		if(!empty($users)){
			foreach($users as $user){
				$userID = $user['ID'];
				$amount = 10;
				$wallet->updateWallet($userID,$amount,'influencer_commission','wallet');
			}
		}

		return true;
	}

	public function onWithdrawal_requestInserted($data,$type,&$db,&$message,&$extra)
	{
		if($type == 'insert'){
			$user_kyc = loadClass('user_kyc_details');
			$wallet = loadClass('wallet');
			// deduct the amount (charges included) from the user wallet
			$wallet->deductWallet($data['user_id'],$data['amount'],'bloc_withdrawal','withdrawal','0');

			$amount = $data['amount'] * 100; // in kobo
			$transfer = null;
			$transfer = $user_kyc->transferFromMonoIssueToVirtual($amount,$data['reference'],$data['user_id'],$data['bank_code'],$data['account_number'],$response);
			if(!$transfer){
				$builder = $db->table('withdrawal_request');
				$builder->update(['message' => $response],['id'=>$data['LAST_INSERT_ID']]);
				$message = "Unable to transfer fund at the moment, try again later";
				$db->transCommit();
				return false;
			}
			$builder = $db->table('withdrawal_request');
			$param = [
				'transfer_ref' => $transfer // the return ref of bloc endpoint
			];
			$builder->update($param,['id'=>$data['LAST_INSERT_ID']]);
			$message = "Your transfer is being processed";
		}
		return true;
	}

	public function onSpinwheel_settingInserted($data,$type,&$db,&$message,&$extra){
		if($type == 'update'){
			if(isset($data['updateSetting']) && $data['updateSetting']){
				$cashback = $db->query("SELECT * from cashback order by date_created desc limit 1");
				$cashback = $cashback->getRow();
				$counter = 0;
				if(isset($cashback)){
					$counter = $cashback->ID;
				}

				$builder = $db->table('settings');
				$builder->update(['settings_value' => 0], ['settings_name' => 'last_wheel_counter']);
				$builder->update(['settings_value' => $counter], ['settings_name' => 'last_cashback_counter']);
			}
		}
		return true;
	}
}

