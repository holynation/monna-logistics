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
	$db = db_connect();
	$db->transBegin();
	
	if ($id == null) {
		$id = $this->id;
	}
	if ($id == 1) {
		return false;
	}
	if(parent::delete($id,$db)){
		$query = "delete from permission where role_id = ?";
		if($this->query($query,array($id))){
			$db->transCommit();
			return true;
		}
		else{
			$db->transRollback();
			return false;
		}
	}
	else{
		$db->transRollback();
		return false;
	}
}

protected function getAdmin(){
	$query ='SELECT * FROM admin WHERE role_id=?';
	$id = $this->array['id'];
	$result = $this->query($query,array($id));
	if (!$result) {
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
	$result = $this->query($query,array($id));
	if ($result){
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
	$result = $this->query($query,array($this->ID));
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
	$db = db_connect();
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
	$db = db_connect();
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
	$db = db_connect();
	$path = $db->escape($path);
	$query = "select * from permission where role_id=? and $path like concat('%',path,'%')";
	$result = $this->query($query,[$this->id]);
	return $result;
}

public function canWrite($path)
{
	$db = db_connect();
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
	$db = db_connect();
	$db->transBegin();
	$query="insert into role(id,role_title) values(1,'superadmin') on duplicate key update role_title=values(role_title)";
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
		'Invoice Management' => array(
			'class' => 'ni ni-wallet',
			'children' => array(
				'Invoice' => 'vc/admin/invoices',
			),
		),
		'Finance Management' => array(
			'class' => 'ni ni-wallet',
			'children' => array(
				'Invoice Transaction' => 'vc/create/invoice_transaction',
			),
		),
		'Customer Management'=>array(
			'class'=>'ni ni-users-fill',
			'children'=>array(
				'All Customers'=>'vc/create/customers',
				'Customer Report' => 'vc/admin/customer_report' 
			)
		),
		'Admin Management'=>array(
			'class'=>'ni ni-user-list',
			'children'=>array(
				'Manage Admin'=>'vc/create/admin',
				'Role'=>'vc/create/role',
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
				'Customer Profile' => 'vc/admin/view_more/customer/detail',
			)
		)
	);
	return $result;
}

 
}

?>
