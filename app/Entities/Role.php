<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the role table
*/
class Role extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Role"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = [];

/** 
* This are fields that must be unique across a row in a table.
* Similar to composite primary key in sql(oracle,mysql)
* @var array
*/
public static $compositePrimaryKey = [];

/** 
* This is to provided an array of fields that can be used for building a
* template header for batch upload using csv format
* @var array
*/
public static $uploadDependency = [];

/** 
* If there is a relationship between this table and another table, this display field properties is used as a column in the query.
* A field in the other table that displays the connection between this name and this table's name,something along these lines
* table_id. We cannot use a name similar to table id in the table that is displayed to the user, so the display field is used in
* place of it. To ensure that the other model queries use that field name as a column to be fetched with the query rather than the
* table id alone, the display field name provided must be a column in the table to replace the table id shown to the user.
* @var array|string
*/
public static $displayField = 'role_title';

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = ['role_title'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['role_title' => 'varchar','status' => 'tinyint'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','role_title' => '','status' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['status' => '1'];

/** 
*  This is an array containing an associative array of field that should be regareded as document field.
* it will contain the setting for max size and data type. Example: populate this array with fields that
* are meant to be displayed as document in the format
* array('fieldname'=>array('type'=>array('jpeg','jpg','png','gif'),'size'=>'1048576','directory'=>'directoryName/','preserve'=>false,'max_width'=>'1000','max_height'=>'500')).
* the folder to save must represent a path from the basepath. it should be a relative path,preserve
* filename will be either true or false. when true,the file will be uploaded with it default filename
* else the system will pick the current user id in the session as the name of the file 
* @var array
*/
public static $documentField = []; 

/** 
* This is an associative array of fields showing relationship between
* entities
* @var array
*/
public static $relation = [];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['permissions'=>'vc/admin/permission','enable'=>'getEnabled','delete' => 'delete/role', 'edit' => 'edit/role'];

public function __construct(array $array = [])
{
	parent::__construct($array);
	$this->createSuperUser();
}
 
public function getRole_titleFormField($value = ''){
	return "<div class='form-group'>
				<label for='role_title'>Role Title</label>
				<input type='text' name='role_title' id='role_title' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
	<label class='form-checkbox'>Status</label>
	<select class='form-control' id='status' name='status' required>
		<option value='1' selected='selected'>Yes</option>
		<option value='0'>No</option>
	</select>
	</div> ";
}

public function delete($id=null,&$db=null)
{
	if ($id==null) {
		$id=$this->id;
	}
	if ($id==1) {
		return false;
	}
	return parent::delete($id,$db);
}

protected function getAdmin(){
	$query ='SELECT * FROM admin WHERE role_id=?';
	$id = $this->array['id'];
	$db = $this->db;
	$result = $db->query($query,array($id));
	$result =$result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultobjects = array();
	foreach ($result as  $value) {
		$resultObjects[] = new \App\Entities\Admin($value);
	}

	return $resultObjects;
}
	
protected function getPermission(){
	$query ='SELECT * FROM permission WHERE role_id=?';
	$id = $this->array['id'];
	$db = $this->db;
	$result = $db->query($query,array($id));
	$result =$result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultobjects = array();
	foreach ($result as  $value) {
		$resultObjects[] = new \App\Entities\Permission($value);
	}

	return $resultObjects;
}
public function getPermissionArray()
{
	$query = "select * from permission where role_id=?";
	$result = $this->query($query,array($this->id));
	$toReturn = array();
	if (!$result) {
		return array();
	}
	foreach ($result as $res) {
		$toReturn[$res['path']]=$res['permission'];
	}
	return $toReturn;
}

public function processPermission($update,$remove)
{
	$db = $this->db;
	$id = $db->escape($this->id);
	$removeQuery=$this->buildRemoveQuery($remove,$id);
	$updateQuery = $this->buildUpdateQuery($update,$id);
	$db->transBegin();
	if ($remove) {
		if (!$db->query($removeQuery)) {
			$db->transRollback();
			return false;
		}
	}
	if ($updateQuery) {
		if (!$db->query($updateQuery)) {
			$db->transRollback();
			return false;
		}
	}
	$db->transCommit();
	return true;
}

private function buildUpdateQuery($update,$id)
{
	$query = "insert into permission(role_id,path,permission) values ";
	$additional = '';
	$db = $this->db;
	foreach ($update as $value) {
		$path = $db->escape($value['path']);
		$permission = $db->escape($value['permission']);
		$additional.=$additional?",($id,$path,$permission)":"($id,$path,$permission)";
	}
	if (!$additional) {
		return false;
	}
	return $query.$additional.' on duplicate key update permission=values(permission) ';
}

private function buildRemoveQuery($remove,$id)
{
	$content = implode(',', $remove);
	if ($content) {
		$content = str_replace(',', "','", $content);
		$content = "'$content'";
	}
	$result = "delete from permission where path in ($content) and role_ID={$this->id}";
	return $result;
}

public function canView($path)
{
	$db = $this->db;
	$path = $db->escape($path);
	$query = "select * from permission where role_id=? and $path like concat('%',path,'%')";
	$result = $this->query($query,[$this->id]);
	return $result;
}

public function canWrite($path)
{
	$db = $this->db;
	$path = $db->escape($path);
	$query = "select * from permission where role_id=? and $path like concat('%',path,'%') and permission='w'";
	$result = $this->query($query,[$this->id]);
	return $result;
}

public function checkWritePermission(){
	$admin = loadClass('admin');
	$webSessionManager = new \App\Models\WebSessionManager;
	$admin->id = $webSessionManager->getCurrentUserProp('user_table_id');
	$admin->load();
	$role = $admin->role;
	// get the page referer and use it as the
	$path = @$_SERVER['HTTP_REFERER'];
	$path = $this->extractBase($path);
	if (!$role->canWrite($path)) {
	  echo createJsonMessage('status',false,'message','sorry, you do not have permission to perform operation');exit;
	}
}

private function extractBase($path)
{
	if(!$path) return null;
	$base = base_url();
	$ind = strpos($path, $base);
	if ($ind === false) {
		return false;
	}
	$result = substr($path, $ind+strlen($base));
	return $result;
}


public function createSuperUser()
{
	$db = $this->db;
	$db->transBegin();
	$query="insert into role(ID,role_title) values(1,'superadmin') on duplicate key update role_title=values(role_title)";
	if ($this->query($query)) {
		$modules = array_merge($this->getModules(),$this->getExtraModules());
		$q = "insert into permission(role_id,path,permission) values(?,?,?) on duplicate key update permission=values(permission)";
		$role_id=1;
		foreach ($modules as $val) {
			foreach ($val['children'] as $child) {
				if(is_array($child)){
					foreach($child as $childValue){
						if(!$this->query($q,array($role_id,$childValue,'w'))){
							$db->transRollback();
							return false;
						}
					}
				}else{
					if (!$this->query($q,array($role_id,$child,'w'))) {
						$db->transRollback();
						return false;
					}
				}
			}
		}
		$db->transCommit();
		return true;
	}
	else{
		$db->transRollback();
		return false;
	}
}

public function getModules(){
	$result = array(
		'Manage Wallet' => array(
			'class' => 'ni ni-wallet',
			'children' => array(
				'Interswitch Payments' => 'vc/admin/verify_payment',
				'Bloc Payments' => 'vc/admin/view_model/mono_transaction',
				'Wallet Transaction' => 'vc/create/transaction_history'
			)
		),
		'Manage Payouts' => array(
			'class' => 'ni ni-wallet-out',
			'children' => array(
				'Pending Withdrawals' => 'vc/admin/view_model/withdrawal_request?type=pending',
				'Cancelled Withdrawals' => 'vc/admin/view_model/withdrawal_request?type=failed',
				'Approved Withdrawals' => 'vc/admin/view_model/withdrawal_request?type=approved'
			)
		),
		'Manage Gameplays' => array(
			'class' => 'ni ni-ticket-plus',
			'children' => array(
				'Gameplay (Users)' => 'vc/admin/view_model/cashback?type=customer',
				'Gameplay (Agent)' => 'vc/admin/view_model/cashback?type=agent',
				'Check-in Data' => 'vc/admin/view_model/cashback?type=checkin'
			)
		),
		'Manage Winner(s)' => array(
			'class' => 'ni ni-ticket-plus',
			'children' => array(
				'Daily Winner' => 'vc/admin/view_model/daily_winner?type=daily',
				'Daily Winner Archives' => 'vc/admin/view_model/daily_winner?type=archive',
				'Daily Timestamp' => 'vc/create/daily_timestamp',
				'Trench Burster' => 'vc/admin/view_model/trench_burster',
				'Boom code Winner' => 'vc/create/boom_points',
			)
		),
		'Users Management'=>array(
			'class'=>'ni ni-users-fill',
			'children'=>array(
				'All Users'=>'vc/admin/view_model/customer',
				'Verified Users'=>'vc/admin/view_model/customer?type=verified',
				'Unverified Users'=>'vc/admin/view_model/customer?type=unverified',
				'Agents'=>'vc/create/agent',
				// 'Users Kyc' => 'vc/create/user_kyc_details'
			)
		),
		'Admin Management'=>array(
			'class'=>'ni ni-user-list',
			'children'=>array(
				'Manage Admin'=>'vc/create/admin',
				'Manage Super Agent'=>'vc/admin/view_model/superagent',
				'Manage NLRC' => 'vc/create/user',
				'Manage Influencer' => 'vc/create/influencer',
				'Role'=>'vc/create/role',
			)
		),
		'Support' => array(
			'class' => 'ni ni-shield',
			'children' => array(
				'Notification' => 'vc/create/notification',
				'Notices' => 'vc/create/notices',
				'Disputes' => 'vc/create/disputes',
				'Statistics' => 'vc/admin/graph',
				// 'Audit Logs' => 'vc/create/audit_log',
				// 'Webhook Logs' => 'vc/create/webhook_logs',
			)
		),
		'App Setting' => array(
			'class' => 'ni ni-grid-alt-fill',
			'children' => array(
				'Share Ads' => 'vc/create/share_ads',
				'General' => 'vc/admin/settings',
				'Spinwheel' => 'vc/create/spinwheel_setting',
				'BoomCode' => 'vc/create/boomcode_setting',
				'Crossover' => 'vc/create/giveaway_setting',
				'Bank Lists' => 'vc/create/bank_lists',
				// 'Time Percentage' => 'vc/create/time_percentage',
				// 'Boom Numbers' => 'vc/admin/upload_timestamp',
				// 'Generated Numbers' => 'vc/admin/upload_numbers',
			)
		),

	);
	return $result;
}

public function getExtraModules(){
	$result = array(
		'Extra Section' => array(
			'class' => 'bx-layout',
			'children' => array(
				'Customer Details' => 'vc/admin/view_more/customer/verified',
				'Customer Profile' => 'vc/admin/view_more/customer/detail',
				'Customer Wallet' => 'vc/admin/view_more/customer/wallet',
				'View Profile' => 'vc/admin/view_more/profile',
				'View Agent' => 'vc/admin/view_more/agent',
				'View Superagent Agent' => 'vc/admin/view_model/agent',
				'View Transaction History' => 'vc/admin/view_model/transaction_history',
				'View Superagent' => 'vc/admin/view_more/superagent',
				'View Cashbacks' => 'vc/create/cashback',
				'Cashback Users' => 'vc/admin/view_more/cashback/customer',
				'Cashback Detail' => 'vc/admin/view_more/cashback/detail'
			)
		)
	);
	return $result;
}

 
}

?>
