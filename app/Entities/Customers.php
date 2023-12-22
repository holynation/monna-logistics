<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the customers table
*/
class Customers extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Customers"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['middlename','address','status','date_created','date_modified'];

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
public static $displayField = 'email';

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
public static $typeArray = ['firstname' => 'varchar','lastname' => 'varchar','middlename' => 'varchar','email' => 'varchar','phone_number' => 'varchar','address' => 'text','status' => 'tinyint','date_created' => 'timestamp','date_modified' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['id' => '','firstname' => '','lastname' => '','middlename' => '','email' => '','phone_number' => '','address' => '','status' => '','date_created' => '','date_modified' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['status' => '1','date_created' => 'current_timestamp()','date_modified' => 'current_timestamp()'];

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
public static $tableAction = ['delete' => 'delete/customers', 'edit' => 'edit/customers'];

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

public function getAddressFormField($value = ''){
	return "<div class='form-floating mb-7'>
		<textarea name='address' id='address' class='form-control' placeholder='Address' required>$value</textarea>
		<label for='address'>Address</label>
	</div>";
} 

public function getStatusFormField($value = ''){
	return "";
} 

public function getDate_createdFormField($value = ''){
	return "";
} 

public function getDate_modifiedFormField($value = ''){
	return "";
} 

protected function getRole(){
	$query = 'SELECT * FROM role WHERE id=?';
	if (!isset($this->array['id'])) {
		return null;
	}
	$id = $this->array['id'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Role($result[0]);
	return $resultObject;
}

public function delete($id=null,&$db=null)
{  
    $db = $db ?? $this->db;
    $db->transBegin();
    $customer = new Customers(['ID'=>$id]);
    $customer->load();
    if(parent::delete($id,$db)){
        $query = "DELETE from user where user_table_id=? and user_type='customers'";
        if($this->query($query,array($id))){
            $db->transCommit();
            return true;
        }
    }
    $db->transRollback();
    return false;
}

public function getCustomerOption($value){
	$value = ($value != "") ? $value : "";
	$disable = ($value != '') ? "disabled" : ""; // this means edit function has passed down the value
	$where = ($value != '') ? " where ID= '$value' " : " where status = '1'";
	$db = db_connect();
	$query = "SELECT id,concat(firstname, ' ', lastname) as value from customers $where order by value asc";
	$result ="<div class='form-floating'>";
		$option = buildOptionFromQuery($db,$query,null,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='customer_id' id='customer_id' class='form-select' required>
					$option
				</select>
				<label for='customer_id'>Customer's name</label>";
	$result.="</div>";
	return $result;	
}




 
}

?>
