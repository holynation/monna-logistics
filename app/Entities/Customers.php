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
public static $nullArray = ['middlename','address'];

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
public static $labelArray = ['ID' => '','firstname' => '','lastname' => '','middlename' => '','email' => '','phone_number' => '','address' => '','status' => '','date_created' => '','date_modified' => ''];

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
	return "<div class='form-group'>
				<label for='firstname'>Firstname</label>
				<input type='text' name='firstname' id='firstname' value='$value' class='form-control' required />
			</div>";
} 
public function getLastnameFormField($value = ''){
	return "<div class='form-group'>
				<label for='lastname'>Lastname</label>
				<input type='text' name='lastname' id='lastname' value='$value' class='form-control' required />
			</div>";
} 
public function getMiddlenameFormField($value = ''){
	return "<div class='form-group'>
				<label for='middlename'>Middlename</label>
				<input type='text' name='middlename' id='middlename' value='$value' class='form-control' required />
			</div>";
} 
public function getEmailFormField($value = ''){
	return "<div class='form-group'>
				<label for='email'>Email</label>
				<input type='text' name='email' id='email' value='$value' class='form-control' required />
			</div>";
} 
public function getPhone_numberFormField($value = ''){
	return "<div class='form-group'>
				<label for='phone_number'>Phone Number</label>
				<input type='text' name='phone_number' id='phone_number' value='$value' class='form-control' required />
			</div>";
} 
public function getAddressFormField($value = ''){
	return "<div class='form-group'>
				<label for='address'>Address</label>
				<input type='text' name='address' id='address' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_createdFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_created'>Date Created</label>
				<input type='text' name='date_created' id='date_created' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_modifiedFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_modified'>Date Modified</label>
				<input type='text' name='date_modified' id='date_modified' value='$value' class='form-control' required />
			</div>";
} 

protected function getRole(){
	$query = 'SELECT * FROM role WHERE id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Role($result[0]);
	return $resultObject;
}


 
}

?>
