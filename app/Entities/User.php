<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the user table
*/
class User extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "User"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['token','last_logout','referral_code','username_2','user_table_id','last_login','status'];

/** 
* This are fields that must be unique across a row in a table.
* Similar to composite primary key in sql(oracle,mysql)
* @var array
*/
public static $compositePrimaryKey = ['username', 'user_type'];

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
public static $displayField = 'ID';

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = [];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['username' => 'varchar','username_2' => 'varchar','password' => 'varchar','user_type' => 'enum','user_table_id' => 'int','token' => 'text','last_login' => 'timestamp','last_logout' => 'timestamp','date_created' => 'timestamp','status' => 'tinyint', 'referral_code' => 'varchar'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','username' => '','username_2' => '','password' => '','user_type' => '','user_table_id' => '','token' => '','last_login' => '','last_logout' => '','date_created' => '','status' => '','referral_code'=>''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['user_type' => 'customer','last_login' => 'current_timestamp()','date_created' => 'current_timestamp()','status' => '1'];

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
public static $relation = ['user_table' => array('user_table_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/user', 'edit' => 'edit/user'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getUsernameFormField($value = ''){
	return "<div class='form-group'>
				<label for='username'>Username</label>
				<input type='text' name='username' id='username' value='$value' class='form-control' required />
			</div>";
}
public function getUsername_2FormField($value = ''){
	return "<div class='form-group'>
				<label for='username_2'>Username 2</label>
				<input type='text' name='username_2' id='username_2' value='$value' class='form-control' required />
			</div>";
} 
public function getPasswordFormField($value = ''){
	return "<div class='form-group'>
				<label for='password'>Password</label>
				<input type='password' name='password' id='password' value='$value' class='form-control' required />
			</div>";
} 
public function getUser_typeFormField($value = ''){
	return "<div class='form-group'>
				<label for='user_type'>User Type</label>
				<input type='text' name='user_type' id='user_type' value='$value' class='form-control' required />
			</div>";
} 
public function getUser_table_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'user_table','display'=>'user_table_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'user_table_name' as value from 'user_table' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('user_table', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

		if(is_null($fk)){
			return $result = "<input type='hidden' name='user_table_id' id='user_table_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='user_table_id'>User Table</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='user_table_id' id='user_table_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getTokenFormField($value = ''){
	return "<div class='form-group'>
				<label for='token'>Token</label>
				<input type='text' name='token' id='token' value='$value' class='form-control' required />
			</div>";
} 
public function getLast_loginFormField($value = ''){
	return "<div class='form-group'>
				<label for='last_login'>Last Login</label>
				<input type='text' name='last_login' id='last_login' value='$value' class='form-control' required />
			</div>";
} 
public function getLast_logoutFormField($value = ''){
	return "<div class='form-group'>
				<label for='last_logout'>Last Logout</label>
				<input type='text' name='last_logout' id='last_logout' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_createdFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_created'>Date Created</label>
				<input type='text' name='date_created' id='date_created' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
			</div>";
} 
public function getReferral_codeFormField($value = ''){
	return "<div class='form-group'>
				<label for='referral_code'>Referral Code</label>
				<input type='text' name='referral_code' id='referral_code' value='$value' class='form-control' required />
			</div>";
} 

protected function getUser_table(){
	$query = 'SELECT * FROM user_table WHERE id=?';
	if (!isset($this->array['id'])) {
		return null;
	}
	$id = $this->array['id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\User_table($result[0]);
	return $resultObject;
}

protected function getCustomer(){
	$query = 'SELECT * FROM customer WHERE id=?';
	if (!isset($this->array['user_table_id'])) {
		return null;
	}
	$db = $this->db;
	$result = $db->query($query,[$this->array['user_table_id']]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return null;
	}
	return new \App\Entities\Customer($result[0]);
}

//this function will return the last auto generated id of the last insert statement
public function getLastInsertId(){
	return getLastInsertId($this->db);
}

public function updatePassword($dataID,$password,$type=null)
{
	if(isset($dataID,$password)){
		$password = encode_password(trim($password));
		$field = (is_numeric($dataID)) ? 'user_table_id' : 'username';
		$dateChange = date('Y-m-d H:i:s');
		$query = "update user set password = ?,last_change_password='$dateChange' where $field=? and user_type=?";
		$db = $this->db;
		$db->transBegin();
		
		$param = array($password,$dataID,$type);
		if($db->query($query,$param)){
			$db->transCommit();
			return true;
		}else{
			$db->transRollback();
			return false;
		}
	}
}

public function updateStatus(int $id,string $userType){
	if(isset($id,$userType)){
		$query = "update user,$userType set user.status = '1',$userType.status='1' where user.user_table_id = $userType.id and user.id = ? and user.user_type=?";
		$db = $this->db;
		$db->transBegin();
		if($this->query($query,[$id,$userType])){
			$db->transCommit();
			return true;
		}else{
			$db->transRollback();
			return false;
		}
	}
}

public function disableAllPasswordOTPs(string $userType, int $user_id)
{
	$query = "update password_otp set status=1 where user_table_id=? and user_type = ?";
	$this->query($query,[$user_id, $userType]);
}

public function find($user = null){
	if($user){
		$field = (is_numeric($user)) ? 'username' : 'username';
		$db = $this->db;
		$builder = $db->table('user');
	   $data = $builder->getWhere(array($field => $user));
	 
	   if($data->getNumRows() > 0){
	   	$this->_data = $data->getResultArray(); // set data value for user to make it public
	   	return true;
	   }	
	}

 	return false;		
}

public function findBothOld($user, string $userType){
	if($user){
		$query = "SELECT * from user where (username = :username: or username_2 = :username:) and user_type = :user_type:";
	   $data = $this->db->query($query,['username'=>$user, 'user_type'=>$userType]);
	   if($data->getNumRows() > 0){
	   	$this->_data = new User($data->getResultArray()[0]); // setting the data value of a user to making it public
	   	return true;
	   }	
	}
 	return false;
}

public function findBoth($user = null){
	if($user){
		$query = "SELECT * from user where (username = :username:) and (user_type = :admin:)";
	   $data = $this->db->query($query,
	   	[
	   		'username'=> $user,
	   		'admin'=>'admin',
		   ]
		);
	   if($data->getNumRows() > 0){
	   	$this->_data = new User($data->getResultArray()[0]); // setting the data value of a user to making it public
	   	return true;
	   }	
	}
 	return false;
}

public function findByUserTypeID(int $userID,string $userType){
	if($userID){
		$db = $this->db;
		$builder = $db->table('user');
	   $data = $builder->getWhere(array('user_type'=>$userType,'user_table_id'=>$userID));
	 
	   if($data->getNumRows() > 0){
	   	  $this->_data = $data->getResultArray(); // setting the data value of a user to making it public
	   	   return true;
	   }	
	}
 	return false;	
}

public function findUserProp($user = null){
	if($user){
		$field = (is_numeric($user)) ? 'ID' : 'username';
		$db = $this->db;
		$builder = $db->table('user');
	   $data = $builder->getWhere(array($field => $user));
	 
	   if($data->getNumRows() > 0){
	   	$this->_data = $data->getResultArray(); // setting the data of a user to making it public
	   	return true;
	   }	
	}

 	return false;		
}

public function memberLogin(){
	// this handles the remember me function
	if($this->dataExists()){
		$userID = $this->data()->id;
		$newUser = new User();
		$userRes = $newUser->getWhere(array('ID'=>$userID,'status'=>'1'),$count,0,1,false);
		$userRes = $userRes[0];
		$this->webSessionManager->saveCurrentUser($userRes);
	}
}

public function dataExists(){
   return (!empty($this->_data)) ? true : false;
}

public function data(){
 	return $this->_data;
}

public function getRealUserData(int $uid, bool $returnOriginal=false)
{
	$user = new User();
	$user->id = $uid;
	if(!$user->load()){
		return null;
	}
	$user = $user->toArray();
	$userType = $user['user_type'];

   $moreInfo = [];
   $userType = loadClass($userType);

   $userInfo = $userType->getWhere(array('id'=>$user['user_table_id'],'status'=>1),$c,0,null,false);
   if (!$userInfo) {
      return null;
   }
   $userInfo = $userInfo[0]->toArray();
   if($returnOriginal){
   	return $userInfo;
   }
   return array_merge($user,$userInfo);
}


 
}

?>
