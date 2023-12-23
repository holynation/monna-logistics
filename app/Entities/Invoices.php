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
public static $typeArray = ['customers_id' => 'int unsigned','invoice_no' => 'varchar','track_number' => 'varchar','bill_from_name' => 'varchar','bill_from_phone' => 'varchar','bill_from_address' => 'text','bill_to_name' => 'varchar','bill_to_phone' => 'varchar','bill_to_email' => 'varchar','bill_to_city' => 'varchar','bill_to_country' => 'varchar','bill_to_postalcode' => 'varchar','invoice_subtotal' => 'decimal','invoice_tax' => 'decimal','invoice_discount' => 'decimal','invoice_total' => 'decimal','invoice_date' => 'date','invoice_notes' => 'text','status' => 'tinyint','date_created' => 'timestamp','date_modified' => 'timestamp','track_number' => 'varchar','bill_to_address' => 'text','invoice_status' => 'varchar'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['id' => '','customers_id' => '','invoice_no' => '','track_number' => '','bill_from_name' => '','bill_from_phone' => '','bill_from_address' => '','bill_to_name' => '','bill_to_phone' => '','bill_to_email' => '','bill_to_city' => '','bill_to_country' => '','bill_to_postalcode' => '','invoice_subtotal' => '','invoice_tax' => '','invoice_discount' => '','invoice_total' => '','invoice_date' => '','invoice_notes' => '','status' => '','date_created' => '','date_modified' => '','bill_to_address' => '','invoice_status' => ''];

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
public static $tableAction = ['delete' => 'delete/invoices', 'preview' => 'vc/admin/preview', 'mail' => 'getMail'];

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
		
		$result ="<div class='form-floating'>";
		$option = $this->loadOption($fk,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='customers_id' id='customers_id' class='form-select'>
					$option
				</select>
			<label for='customers_id'>Customers</label>";
			$result.="</div>";
		return $result;
	}
		
}

public function getInvoice_noFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_no' id='invoice_no' value='$value' class='form-control' placeholder='Invoice No' required readonly />
		<label for='invoice_no'>Invoice No</label>
	</div>";
} 

public function getBill_from_nameFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_from_name' id='bill_from_name' value='$value' class='form-control' placeholder='Bill From Name' required />
		<label for='bill_from_name'>Bill From Name</label>
	</div>";
} 

public function getBill_from_phoneFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_from_phone' id='bill_from_phone' value='$value' class='form-control' placeholder='Bill From Phone' required />
		<label for='bill_from_phone'>Bill From Phone</label>
	</div>";
} 

public function getBill_from_addressFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_from_address' id='bill_from_address' value='$value' class='form-control' placeholder='Bill From Address' required />
		<label for='bill_from_address'>Bill From Address</label>
	</div>";
} 

public function getBill_to_nameFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_name' id='bill_to_name' value='$value' class='form-control' placeholder='Bill To Name' required />
		<label for='bill_to_name'>Bill To Name</label>
	</div>";
} 

public function getBill_to_phoneFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_phone' id='bill_to_phone' value='$value' class='form-control' placeholder='Bill To Phone' required />
		<label for='bill_to_phone'>Bill To Phone</label>
	</div>";
} 

public function getBill_to_emailFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_email' id='bill_to_email' value='$value' class='form-control' placeholder='Bill To Email' required />
		<label for='bill_to_email'>Bill To Email</label>
	</div>";
} 

public function getBill_to_cityFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_city' id='bill_to_city' value='$value' class='form-control' placeholder='Bill To City' required />
		<label for='bill_to_city'>Bill To City</label>
	</div>";
} 

public function getBill_to_countryFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_country' id='bill_to_country' value='$value' class='form-control' placeholder='Bill To Country' required />
		<label for='bill_to_country'>Bill To Country</label>
	</div>";
} 

public function getBill_to_postalcodeFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_postalcode' id='bill_to_postalcode' value='$value' class='form-control' placeholder='Bill To Postalcode' required />
		<label for='bill_to_postalcode'>Bill To Postalcode</label>
	</div>";
} 

public function getInvoice_subtotalFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_subtotal' id='invoice_subtotal' value='$value' class='form-control' placeholder='Invoice Subtotal' required />
		<label for='invoice_subtotal'>Invoice Subtotal</label>
	</div>";
} 

public function getInvoice_taxFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_tax' id='invoice_tax' value='$value' class='form-control' placeholder='Invoice Tax' required />
		<label for='invoice_tax'>Invoice Tax</label>
	</div>";
} 

public function getInvoice_discountFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_discount' id='invoice_discount' value='$value' class='form-control' placeholder='Invoice Discount' required />
		<label for='invoice_discount'>Invoice Discount</label>
	</div>";
} 

public function getInvoice_totalFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_total' id='invoice_total' value='$value' class='form-control' placeholder='Invoice Total' required />
		<label for='invoice_total'>Invoice Total</label>
	</div>";
} 

public function getInvoice_dateFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_date' id='invoice_date' value='$value' class='form-control' placeholder='Invoice Date' required />
		<label for='invoice_date'>Invoice Date</label>
	</div>";
} 

public function getInvoice_notesFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='invoice_notes' id='invoice_notes' value='$value' class='form-control' placeholder='Invoice Notes' required />
		<label for='invoice_notes'>Invoice Notes</label>
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

public function getTrack_numberFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='track_number' id='track_number' value='$value' class='form-control' placeholder='Track Number' required readonly />
		<label for='track_number'>Track Number</label>
	</div>";
} 

public function getBill_to_addressFormField($value = ''){
return "<div class='form-floating mb-7'>
		<input type='text' name='bill_to_address' id='bill_to_address' value='$value' class='form-control' placeholder='Bill To Address' required />
		<label for='bill_to_address'>Bill To Address</label>
	</div>";
} 

public function getInvoice_statusFormField($value = ''){
	$optionArr = [
		\App\Enums\InvoiceStatusEnum::PENDING->value,
		\App\Enums\InvoiceStatusEnum::PROCESSING->value,
		\App\Enums\InvoiceStatusEnum::INTRANSIT->value,
		\App\Enums\InvoiceStatusEnum::CANCELLED->value,
		\App\Enums\InvoiceStatusEnum::COMPLETED->value,
	];
	$options = buildOptionUnassoc($optionArr, $value);
	$result ="<div class='form-floating mb-7'>";
	$result.="<select name='invoice_status' id='invoice_status' class='form-select' required>
					$options
				</select>
				<label for='invoice_status'>Invoice Status</label>";
	$result.="</div>";
	return $result;
} 

protected function getCustomers(){
	$query = 'SELECT * FROM customers WHERE id=?';
	if (!isset($this->array['customers_id'])) {
		return null;
	}
	$id = $this->array['customers_id'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return null;
	}
	$resultObject = new \App\Entities\Customers($result[0]);
	return $resultObject;
}

protected function getInvoice_items(){
	$query = 'SELECT * FROM invoice_items WHERE invoices_id=?';
	if (!isset($this->array['id'])) {
		return null;
	}
	$id = $this->array['id'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return [];
	}
	return $result;
}

public function buildInvoiceData($id){
	$invoices = new \App\Entities\Invoices();
	$invoices->id = $id;
	if(!$invoices->load()){
	  return false;
	}
	$customers = $invoices->customers;
	$variables = [
		'bill_from_email' => $customers->email,
		'bill_from_name' => $invoices->bill_from_name,
	  	'bill_to_name' => $invoices->bill_to_name,
	  	'bill_to_address' => $invoices->bill_to_address,
	  	'order_number' => $invoices->invoice_no,
	  	'ship_date' => $invoices->invoice_date,
	  	'track_number' => $invoices->track_number,
	  	'invoice_date' => $invoices->date_created,
	  	'sub_total' => number_format($invoices->invoice_subtotal, 2),
	  	'tax' => number_format($invoices->invoice_tax, 2),
	  	'total' => number_format($invoices->invoice_total, 2),
	  	'total_amount' => number_format($invoices->invoice_total, 2)
	];

	$items = $invoices->invoice_items;
	$itemsParam = ['items' => []];
	if(!empty($items)){
	  foreach($items as $item){
	    $itemsParam['items'][] = array(
	      'desc' => $item['description'],
	      'quantity' => $item['quantity'],
	      'amount' => number_format($item['price'], 2),
	    );
	  }
	}else{
	  $itemsParam = array(
	    'items' => array(
	      array(
	        'desc' => '',
	        'quantity' => '',
	        'amount' => ''
	      )
	    )
	  );
	}
	
	$templateVariables = $variables + $itemsParam;
	return $templateVariables;
}

public function initMpdfLibrary($html,$filename=null,$outputFormat='I',$title=null,$format='A4-P'){
	$mpdf = new \Mpdf\Mpdf([
		'tempDir' => ROOTPATH . 'temp',
		'mode' => 'utf-8',
		'format' => $format,
		'margin_top' => 7,
	]);
	$title = $title ?: 'Shipping Invoice';
	$mpdf->SetAuthor('MonnaExpress');
	$mpdf->SetTitle($title);
	$mpdf->SetSubject($title);
	$mpdf->SetKeywords('PDF');
	$mpdf->SetDisplayMode('fullpage');

	$mpdf->WriteHTML(utf8_encode($html));
	$filename = $filename ?: randStrGen(11)."_document";
	if($outputFormat == 'S'){
		$content = $mpdf->Output('', 'S');
		return $content;
	}
	return $mpdf->Output($filename.'.pdf', $outputFormat);
}

public function getInvoicesOption($value){
	$value = ($value != "") ? $value : "";
	$disable = ($value != '') ? "disabled" : ""; // this means edit function has passed down the value
	$where = ($value != '') ? " where ID= '$value' " : " where status = '1'";
	$db = db_connect();
	$query = "SELECT id,concat(bill_from_name, ' [#', invoice_no, ']') as value from invoices $where order by value asc";
	$result ="<div class='form-floating mb-7'>";
		$option = buildOptionFromQuery($db,$query,null,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='invoices_id' id='invoices_id' class='form-select' required>
					$option
				</select>
				<label for='invoices_id'>Invoice List</label>";
	$result.="</div>";
	return $result;	
}

 
}

?>
