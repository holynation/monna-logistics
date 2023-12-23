<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the admin table
*/
class Admin extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Admin"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['middlename','email','phone_number','address'];

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
public static $displayField = ['firstname','lastname'];

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = ['email','phone_number'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['firstname' => 'varchar','middlename' => 'varchar','lastname' => 'varchar','email' => 'varchar','phone_number' => 'varchar','role_id' => 'int','status' => 'tinyint'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['id' => '','firstname' => '','middlename' => '','lastname' => '','email' => '','phone_number' => '','role_id' => '','status' => ''];

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
public static $relation = ['role' => array('role_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/admin', 'edit' => 'edit/admin'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getFirstnameFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='firstname' id='firstname' value='$value' class='form-control' placeholder='Firstname' required />
		<label for='firstname'>Firstname</label>
	</div>";
} 

public function getLastnameFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='lastname' id='lastname' value='$value' class='form-control' placeholder='Lastname' required />
		<label for='lastname'>Lastname</label>
	</div>";
} 

public function getMiddlenameFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='middlename' id='middlename' value='$value' class='form-control' placeholder='Middlename'  />
		<label for='middlename'>Middlename</label>
	</div>";
} 

public function getEmailFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='email' id='email' value='$value' class='form-control' placeholder='Email' required />
		<label for='email'>Email</label>
	</div>";
} 

public function getPhone_numberFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='phone_number' id='phone_number' value='$value' class='form-control' placeholder='Phone Number' required />
		<label for='phone_number'>Phone Number</label>
	</div>";
} 

public function getRole_idFormField($value = ''){
$fk = ['table' => 'role', 'display' => 'role_title']; 
 	//change the value of this variable to array('table'=>'role','display'=>'role_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'role_name' as value from 'role' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('role', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

	if(is_null($fk)){
		return $result = "<input type='hidden' name='role_id' id='role_id' value='$value' class='form-control' />";
	}

	if(is_array($fk)){
		
		$result ="<div class='form-floating mb-7'>";
		$option = $this->loadOption($fk,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='role_id' id='role_id' class='form-select'>
					$option
				</select>
			<label for='role_id'>Role</label>";
			$result.="</div>";
		return $result;
	}
		
}

public function getStatusFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='status' id='status' value='$value' class='form-control' placeholder='Status' required />
		<label for='status'>Status</label>
	</div>";
} 

protected function getRole(){
	$query = 'SELECT * FROM role WHERE id=?';
	if (!isset($this->array['role_id'])) {
		return null;
	}
	$id = $this->array['role_id'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Role($result[0]);
	return $resultObject;
}

public function delete($id=null,&$db=null)
{
	$db = db_connect();
	$db->transBegin();
	if(parent::delete($id,$db)){
		$query="delete from user where user_table_id=? and user_type='admin'";
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


 
}

?>
