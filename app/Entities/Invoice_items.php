<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the invoice_items table
*/
class Invoice_items extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Invoice_items"; 

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
public static $displayField = 'email';

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
public static $typeArray = ['invoices_id' => 'int unsigned','description' => 'text','quantity' => 'int','price' => 'decimal','status' => 'tinyint','date_created' => 'timestamp','date_modified' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['id' => '','invoices_id' => '','description' => '','quantity' => '','price' => '','status' => '','date_created' => '','date_modified' => ''];

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
public static $relation = ['invoices' => array('invoices_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/invoice_items', 'edit' => 'edit/invoice_items'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getInvoices_idFormField($value = ''){
$fk = null; 
 	//change the value of this variable to array('table'=>'invoices','display'=>'invoices_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'invoices_name' as value from 'invoices' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('invoices', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

	if(is_null($fk)){
		return $result = "<input type='hidden' name='invoices_id' id='invoices_id' value='$value' class='form-control' />";
	}

	if(is_array($fk)){
		
		$result ="<div class='form-floating'>";
		$option = $this->loadOption($fk,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='invoices_id' id='invoices_id' class='form-select'>
					$option
				</select>
			<label for='invoices_id'>Invoices</label>";
			$result.="</div>";
		return $result;
	}
		
}

public function getDescriptionFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='description' id='description' value='$value' class='form-control' placeholder='Description' required />
		<label for='description'>Description</label>
	</div>";
} 

public function getQuantityFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='quantity' id='quantity' value='$value' class='form-control' placeholder='Quantity' required />
		<label for='quantity'>Quantity</label>
	</div>";
} 

public function getPriceFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='price' id='price' value='$value' class='form-control' placeholder='Price' required />
		<label for='price'>Price</label>
	</div>";
} 

public function getStatusFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='status' id='status' value='$value' class='form-control' placeholder='Status' required />
		<label for='status'>Status</label>
	</div>";
} 

public function getDate_createdFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='date_created' id='date_created' value='$value' class='form-control' placeholder='Date Created' required />
		<label for='date_created'>Date Created</label>
	</div>";
} 

public function getDate_modifiedFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='date_modified' id='date_modified' value='$value' class='form-control' placeholder='Date Modified' required />
		<label for='date_modified'>Date Modified</label>
	</div>";
}  

protected function getInvoices(){
	$query = 'SELECT * FROM invoices WHERE id=?';
	if (!isset($this->array['invoices_id'])) {
		return null;
	}
	$id = $this->array['invoices_id'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Invoices($result[0]);
	return $resultObject;
}


 
}

?>
