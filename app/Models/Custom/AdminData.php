<?php 
/**
* This is the class that manages all information and data retrieval needed by the admin section of this application.
*/
namespace App\Models\Custom;

use CodeIgniter\Model;
use App\Models\WebSessionManager;
use App\Entities\Transaction_history;
use App\Entities\Wallet;
use App\Entities\Customer;
use App\Entities\User_kyc_details;
use App\Entities\Superagent;
use App\Entities\Agent;
use App\Entities\Daily_winner;
use App\Entities\Cashback;
use App\Entities\Cashback_log;
use App\Entities\Disputes;
use App\Entities\Withdrawal_request;

class AdminData extends Model
{	
	protected $db;
	private $webSessionManager;

	public function __construct()
	{
		$this->db = db_connect();
		$this->webSessionManager = new WebSessionManager;
	}

	public function loadDashboardData()
	{
		$result = [];
		$totalCustomer = Customer::totalCount();
		$totalVerifiedCustomer = User_kyc_details::init()->totalVerifiedUsers('customer');
		$totalUnverifiedCustomer =  $totalCustomer - $totalVerifiedCustomer;

		$totalAgent = Agent::totalCount();
		$totalVerifiedAgent = User_kyc_details::init()->totalVerifiedUsers('agent');
		$totalUnverifiedAgent =  $totalAgent - $totalVerifiedAgent;

		$result['countData'] = [
			'customer' => $totalCustomer,
			'verifiedCustomer' => $totalVerifiedCustomer,
			'unverifiedCustomer' => $totalUnverifiedCustomer,
			'superagent' => Superagent::totalCount() ?? 0,
			'agent' => $totalAgent,
			'verifiedAgent' => $totalVerifiedAgent,
			'unverifiedAgent' => $totalUnverifiedAgent,
			'walletBalance'=> Wallet::totalSum('amount') ?? 0,
			'customerWallet' => Wallet::init()->totalSumWallet('customer') ?? 0,
			'superagentWallet' => Wallet::init()->totalSumWallet('superagent') ?? 0,
			'agentWallet' => Wallet::init()->totalSumWallet('agent') ?? 0,
			'customerCashback' => Cashback::totalCount("where cashback_type = 'customer'") ?? 0,
			'agentCashback' => Cashback::totalCount("where cashback_type = 'agent'") ?? 0,
			'alert_winners' => Daily_winner::totalCount(" where match_sequence = 'three_unseq' ") ?? 0,
			'jackpot_winners' => Daily_winner::totalCount(" where match_sequence = 'four_consec' ") ?? 0,
			'cashback' => Cashback::totalSum('deducted_amount') ?? 0,
			'pendingPayout' => Withdrawal_request::totalCount("where request_status = 'pending' or request_status = 'processing'"),
			'pendingDisputes' => Disputes::totalCount("where dispute_status = 'pending'"),
			'salesCount' => Cashback::totalCount("where cast(date_created as date) = now()") ?? 0,
			'salesAmount' => Cashback::totalSum('deducted_amount', "where cast(date_created as date) = now()") ?? 0,
			'checkinAmountDaily' => Cashback_log::totalSum('checkin_amount', "where game_type = 'check_in' and cast(date_created as date) = now()") ?? 0,
			'checkinAmount' => Cashback_log::totalSum('checkin_amount', "where game_type = 'check_in' and year(cast(date_created as date)) = year(now())") ?? 0
		];
		$result['cashbackDistrix'] = Cashback::init()->getCashbackDistrixByDay();
		$result['fundDistrix'] = Transaction_history::init()->getFundDistrixByMonth('credit');
		$result['withdrawalDistrix'] = Transaction_history::init()->getFundDistrixByMonth('debit');
		// print_r($result);exit;
		return $result;
	}

	public function loadGraphData(?string $whereClause){
		$result = [];

		$result['cashbackDistrix'] = Cashback::init()->getCashbackDistrixByDay($whereClause);
		$result['fundDistrix'] = Transaction_history::init()->getFundDistrixByMonth('credit',$whereClause);
		$result['withdrawalDistrix'] = Transaction_history::init()->getFundDistrixByMonth('debit',$whereClause);
		// print_r($result);exit;
		return $result;
	}

	public function getAdminSidebar($combine = false)
	{
		$role = loadClass('role');
		$role = new $role();
		// using $combine parameter to take into consideration path that're not captured in the admin sidebar
		$output = ($combine) ? array_merge($role->getModules(),$role->getExtraModules()) : $role->getModules();
		return $output;
	}

	public function getCanViewPages(object $role,$merge=false)
	{
		$result = array();
		$allPages = $this->getAdminSidebar($merge);
		$permissions = $role->getPermissionArray();
		
		foreach ($allPages as $module => $pages) {
			$has = $this->hasModule($permissions,$pages,$inter);
			$allowedModule = $this->getAllowedModules($inter,$pages['children']);
			$allPages[$module]['children'] = $allowedModule;
			$allPages[$module]['state'] = $has;
		}
		return $allPages;
	}

	private function getAllowedModules($includesPermission,$children)
	{
		$result = $children;
		$result=array();
		foreach($children as $key=>$child){
			if(is_array($child)){
				foreach($child as $childKey => $childValue){
					if (in_array($childValue, $includesPermission)) {
						$result[$key]=$child;
					}
				}
			}else{
				if (in_array($child, $includesPermission)) {
					$result[$key]=$child;
				}
			}
			
		}
		return $result;
	}

	private function hasModule($permission,$module,&$res)
	{
		if(is_array(array_values($module['children']))){
			$res =array_intersect(array_keys($permission), array_values_recursive($module['children']));
		}else{
			$res =array_intersect(array_keys($permission), array_values($module['children']));
		}
		
		if (count($res)==count($module['children'])) {
			return 2;
		}
		if (count($res)==0) {
			return 0;
		}
		else{
			return 1;
		}
	}

}

 ?>