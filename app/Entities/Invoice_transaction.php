<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the invoice_transaction table
*/
class Invoice_transaction extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Invoice_transaction"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['date_created', 'date_modified'];

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
public static $typeArray = ['customers_id' => 'int unsigned','invoices_id' => 'int','description' => 'varchar','transaction_ref' => 'varchar','amount_paid' => 'decimal','payment_status' => 'varchar','payment_date' => 'timestamp','date_created' => 'timestamp','date_modified' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['id' => '','customers_id' => '','invoices_id' => '','description' => '','transaction_ref' => '','amount_paid' => '','payment_status' => '','payment_date' => '','date_created' => '','date_modified' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['payment_status' => 'Not Paid','payment_date' => 'current_timestamp()','date_created' => 'current_timestamp()','date_modified' => 'current_timestamp()'];

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
,'invoices' => array('invoices_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['edit' => 'edit/invoice_transaction'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}

public function getCustomers_idFormField($value = ''){
	return getCustomerOption($value)		;
}

public function getInvoices_idFormField($value = ''){
	return getInvoicesOption($value);
}

public function getDescriptionFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='description' id='description' value='$value' class='form-control' placeholder='Description' required />
		<label for='description'>Description</label>
	</div>";
} 

public function getTransaction_refFormField($value = ''){
	$value = ($value) ?: generateHashRef('receipt');
return "<div class='form-floating mb-7'>
		<input type='text' name='transaction_ref' id='transaction_ref' value='$value' class='form-control' placeholder='Transaction Ref' required readonly />
		<label for='transaction_ref'>Transaction Ref</label>
	</div>";
} 

public function getAmount_paidFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='number' name='amount_paid' id='amount_paid' value='$value' class='form-control' placeholder='Amount Paid' required step='any' />
		<label for='amount_paid'>Amount Paid</label>
	</div>";
} 

public function getPayment_statusFormField($value = ''){
	$options = buildOptionUnassoc2([
		'pending' => 'pending',
		'not paid' => 'Not Paid',
		'paid' => 'paid'
	], $value);

	$result ="<div class='form-floating mb-7'>";
	$result.="<select name='payment_status' id='payment_status' class='form-select' required>
					$options
				</select>
				<label for='payment_status'>Payment Status</label>";
	$result.="</div>";
	return $result;
} 

public function getPayment_dateFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='date' name='payment_date' id='payment_date' value='$value' class='form-control' placeholder='Payment Date' required readonly />
		<label for='payment_date'>Payment Date</label>
	</div>";
} 

public function getDate_createdFormField($value = ''){
return "";
} 

public function getDate_modifiedFormField($value = ''){
return "";
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

protected function getInvoices(){
	$query = 'SELECT * FROM invoices WHERE id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Invoices($result[0]);
	return $resultObject;
}

public function getTransactions(){
	$query = "SELECT a.*,concat(b.firstname,' ',b.lastname) as fullname,b.email,c.invoice_no from invoice_transaction a join customers b on b.id = a.customers_id join invoices c on c.id = a.invoices_id order by payment_date desc";
	$result = $this->query($query);
	return $result;
}


 
}

?>
