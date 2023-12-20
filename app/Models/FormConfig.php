<?php 
/**
* this class help save the configuration needed by the form in order to use a single file for all the form code.
* you only need to include the configuration data that matters. the default value will be substituted for other configuration value that does not have a key  for a particular entity.
*/
namespace App\Models;

use App\Models\WebSessionManager;

class FormConfig
{
	private $insertConfig=[];
	private $updateConfig;
	private $webSessionManager;
	public $currentRole;
	private $apiEntity = false;
	
	public function __construct(bool $currentRole=false,bool $apiEntity=false)
	{
		$this->currentRole=$currentRole;
		$this->apiEntity = $apiEntity;
		$this->webSessionManager = new WebSessionManager;
		if ($currentRole) {
			$this->buildInsertConfig();
			$this->buildUpdateConfig();
		}
		
	}

	/**
	 * this is the function to change when an entry for a particular entitiy needed to be addded. this is only necessary for entities that has a custom configuration for the form.Each of the key for the form model append insert option is included. This option inculde:
	 * form_name the value to set as the name and as the id of the form. The value will be overridden by the default value if the value if false.
	 * has_upload this field is used to determine if the form should include a form upload section for the table form list
	 * hidden this  are the field that should be pre-filled. This must contain an associative array where the key of the array is the field and the value is the value to be pre-filled on the value.
	 * showStatus field is used to show the status flag on the form. once the value is true the status field will be visible on the form and false otherwise.
	 * exclude contains the list of entities field name that should not be shown in the form. The filed for this form will not be display on the form.
	 * submit_label is the label that is going to be displayed on the submit button
	 * 	table_exclude is the list of field that should be removed when displaying the table.
	 * table_action contains an associative arrays action to be displayed on the action table and the link to perform the action.
	 * the query paramete is used to specify a query for getting the data out of the entity
	 * upload_param contains the name of the function to be called to perform
	 * 
	 */ 
	private function buildInsertConfig()
	{
		if($this->apiEntity){
			$this->insertConfig = array
			(
				'customer' => array(
					'search' => array('fullname')
				),
				//add new entry to this array
				'transaction_history' => array(
					'search' => array('tranx_name','tranx_type')
				),
				'daily_winner' => array(
					'search' => array('phone_number')
				),
			);
		}
		else{
			$type = @$_GET['type'] ?? 'detail';
			$withdrawalAction = @$type == 'pending' ? [
				'User Details' => "vc/admin/view_more/profile/withdrawal",
				'change status' => 'getEnumStatus'
			] : [];
			$walletAction = @$type == 'detail' ? [
				'User Details' => "vc/admin/view_more/profile/wallet",
			] : [];
			$cashbackAction = @$type == 'checkin' ? [] : [
				'view page' => "vc/admin/view_more/cashback/{$type}",
				'edit' => "edit/cashback",
            ];

			$this->insertConfig = array
			(
				'customers'=>array
				(
					'show_add' => false,
					'exclude'=> [],
					'table_exclude'=> ['date_modified', 'middlename'],
					'header_title'=>'Manage registered customer(s)',
					'table_title'=>'Manage registered customer(s)',
					'has_upload'=>false,
					'hidden'=>array(),
					'show_status'=>false,
					'search'=>array('firstname'),
					'search_placeholder'=>array('Search...'),
					'order_by' => array('firstname'),
                    'query_string' => [],
				),
				'admin'=>array
				(
					// 'table_title' => 'Admin Page',
					'show_status' => true,
					'show_add' => true,
					'table_exclude' => array('middlename'),
					'header_title' => 'Manage Admin(s)'
				),
				'role'=>array(
						'query'=>'select * from role where ID<>1',
						'show_add' => true
				),
				'user' => array(
					'query' => "SELECT ID,username,last_login,status,date_created from user where user_type = 'nlrc'",
					'exclude' => ['username_2', 'user_type','token','last_logout','last_login','date_created','referral_code'],
					'show_add' => true,
					'show_add_caption' => 'This page is strictly from creating NLRC login details',
					'table_title' => 'NLRC User Page'
				),
				'agent'=>array(
					'show_add'=> false,
					'table_action' => [
                      	'view profile' => "vc/admin/view_more/agent/{$type}",
                      	'enable' => 'getEnabled',
                    ],
                    'table_exclude' => ['gender','address','residence_state','country','agent_path','date_modified','first_wallet_pay','date_created'],
				),
				'bank_lists'=>array(
						'show_add'=> true,
						'has_upload' => true
				),
				'time_percentage'=>array(
						'show_add'=> true
				),
				'notices'=>array(
						'show_add'=> true
				),
				'superagent'=>array(
					'show_add'=> true,
					'table_action' => [
                      	'view profile' => "vc/admin/view_more/superagent/{$type}",
                      	'view agents' => 'vc/admin/view_model/agent',
                      	'enable' => 'getEnabled',
                      	'edit profile' => "edit/superagent",
                    ],
                    'query_string' => ['super_code'],
				),
				'influencer'=>array(
					'show_add'=> true,
					'table_title' => 'Manage Influencer Page'
				),
				'withdrawal_request' => array(
					'table_action' => $withdrawalAction,
					'query_string' => ['reference','made_by'],
				),
				'wallet_payment_history' => array(
					'table_action' => $walletAction,
					'query_string' => ['reference_number','made_by'],
				),
				'cashback' => array(
					'table_action' => $cashbackAction,
				),
				'transaction_history' => array(
					'table_exclude' => ['status'],
					'table_title' => 'Wallet Transaction History'
				),
				'daily_timestamp' => array(
					'table_title' => 'Results For Daily Boom Numbers',
					'table_exclude' => ['status', 'percentage']
				),
				'spinwheel_setting' => array(
					'show_add' => false,
					'table_exclude' => ['last_cashback_counter'],
					'table_title' => 'Spinwheel Setting'
				),
				'giveaway_setting' => array(
					'show_add' => false,
					'table_title' => 'Crossover Setting',
					'show_add_caption' => "Do not add new one of the same type, you can easily edit",
					'table_exclude' => ['crossover_date', 'crossover_time']
				),
				'notification' => array(
					'show_add' => true,
				),
				'boomcode_setting' => array(
					'show_add' => true,
				),
				'boom_points' => array(
					'table_title' => 'Boom points Winning Page'
				),
				'share_ads' => array(
					'show_add' => false,
				),
				//add new entry to this array
			);
		}
	}

	/**
	 * This is to get the entity filter for a model using certain pattern
	 * @example 'entity_name'=>array(
	 * array(
	 * 'filter_label'=>'request_status', # this is the field to call for the filter
	 * 'filter_display'=>'active_status' # this is the query param supplied
	 * )),
	 * @param  string $tablename [description]
	 * @return [type]            [description]
	 */
	private function getFilter(string $tablename)
	{	
		$result = [];
		if($this->apiEntity){
			$result = array(
				
			);
		}
		else{
			$result = array(
				
			);
		}

		if (array_key_exists($tablename, $result)) {
			return $result[$tablename];
		}
		return false;
	}

	/**
	 * This is the configuration for the edit form of the entities.
	 * exclude take an array of fields in the entities that should be removed from the form.
	 */
	private function buildUpdateConfig()
	{
		$userType = $this->webSessionManager->getCurrentUserProp('user_type');
		$exclude = [];
		if($userType == 'customer'){
			$exclude = array('email','customer_path');
		}
		$this->updateConfig = array
		(
			'user' => array(
				'exclude' => ['username_2','user_type','token','last_logout','last_login','date_created','referral_code','password'],
			)
			//add new entry to this array
		);
	}

	public function getInsertConfig(?string $entities)
	{
		if (array_key_exists($entities, $this->insertConfig)) {
			$result=$this->insertConfig[$entities];
			if (($fil=$this->getFilter($entities))) {
				$result['filter']=$fil;
			}
			$this->apiEntity = false;
			return $result;
		}
		if (($fil=$this->getFilter($entities))) {
			return array('filter'=>$fil);
		}
		return false;
	}

	public function getUpdateConfig(?string $entities)
	{
		if (array_key_exists($entities, $this->updateConfig)) {
			$result=$this->updateConfig[$entities];
			if (($fil=$this->getFilter($entities))) {
				$result['filter']=$fil;
			}
			return $result;
		}
		if (($fil = $this->getFilter($entities))) {
			return array('filter'=>$fil);
		}
		return false;
	}
}
 ?>