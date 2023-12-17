<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the invoices table
*/
class Invoices extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Invoices"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['bill_to_email','bill_to_postalcode','invoice_notes'];

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
public static $displayField = 'bill_to_email';

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
public static $typeArray = ['customers_id' => 'int unsigned','invoice_no' => 'varchar','bill_from_name' => 'varchar','bill_from_phone' => 'varchar','bill_from_address' => 'text','bill_to_name' => 'varchar','bill_to_phone' => 'varchar','bill_to_email' => 'varchar','bill_to_city' => 'varchar','bill_to_country' => 'varchar','bill_to_postalcode' => 'varchar','invoice_subtotal' => 'decimal','invoice_tax' => 'decimal','invoice_discount' => 'decimal','invoice_total' => 'decimal','invoice_date' => 'date','invoice_notes' => 'text','status' => 'tinyint','date_created' => 'timestamp','date_modified' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','customers_id' => '','invoice_no' => '','bill_from_name' => '','bill_from_phone' => '','bill_from_address' => '','bill_to_name' => '','bill_to_phone' => '','bill_to_email' => '','bill_to_city' => '','bill_to_country' => '','bill_to_postalcode' => '','invoice_subtotal' => '','invoice_tax' => '','invoice_discount' => '','invoice_total' => '','invoice_date' => '','invoice_notes' => '','status' => '','date_created' => '','date_modified' => ''];

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
public static $relation = ['customers' => array('customers_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/invoices', 'edit' => 'edit/invoices'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getCustomers_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'customers','display'=>'customers_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'customers_name' as value from 'customers' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('customers', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

		if(is_null($fk)){
			return $result = "<input type='hidden' name='customers_id' id='customers_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='customers_id'>Customers</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='customers_id' id='customers_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getInvoice_noFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_no'>Invoice No</label>
				<input type='text' name='invoice_no' id='invoice_no' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_from_nameFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_from_name'>Bill From Name</label>
				<input type='text' name='bill_from_name' id='bill_from_name' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_from_phoneFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_from_phone'>Bill From Phone</label>
				<input type='text' name='bill_from_phone' id='bill_from_phone' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_from_addressFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_from_address'>Bill From Address</label>
				<input type='text' name='bill_from_address' id='bill_from_address' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_nameFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_name'>Bill To Name</label>
				<input type='text' name='bill_to_name' id='bill_to_name' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_phoneFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_phone'>Bill To Phone</label>
				<input type='text' name='bill_to_phone' id='bill_to_phone' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_emailFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_email'>Bill To Email</label>
				<input type='text' name='bill_to_email' id='bill_to_email' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_cityFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_city'>Bill To City</label>
				<input type='text' name='bill_to_city' id='bill_to_city' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_countryFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_country'>Bill To Country</label>
				<input type='text' name='bill_to_country' id='bill_to_country' value='$value' class='form-control' required />
			</div>";
} 
public function getBill_to_postalcodeFormField($value = ''){
	return "<div class='form-group'>
				<label for='bill_to_postalcode'>Bill To Postalcode</label>
				<input type='text' name='bill_to_postalcode' id='bill_to_postalcode' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_subtotalFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_subtotal'>Invoice Subtotal</label>
				<input type='text' name='invoice_subtotal' id='invoice_subtotal' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_taxFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_tax'>Invoice Tax</label>
				<input type='text' name='invoice_tax' id='invoice_tax' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_discountFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_discount'>Invoice Discount</label>
				<input type='text' name='invoice_discount' id='invoice_discount' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_totalFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_total'>Invoice Total</label>
				<input type='text' name='invoice_total' id='invoice_total' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_dateFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_date'>Invoice Date</label>
				<input type='text' name='invoice_date' id='invoice_date' value='$value' class='form-control' required />
			</div>";
} 
public function getInvoice_notesFormField($value = ''){
	return "<div class='form-group'>
				<label for='invoice_notes'>Invoice Notes</label>
				<input type='text' name='invoice_notes' id='invoice_notes' value='$value' class='form-control' required />
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

protected function getCustomers(){
	$query = 'SELECT * FROM customers WHERE id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Customers($result[0]);
	return $resultObject;
}


 
}

?>
