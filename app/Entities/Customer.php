<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the customer table
*/
class Customer extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Customer"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['email','gender','address','date_created','status','residence_state','country','customer_path'];

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
public static $uniqueArray = ['phone_number'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['fullname' => 'varchar','email' => 'varchar','phone_number' => 'varchar','gender' => 'enum','address' => 'text','residence_state'=>'varchar','country'=>'varchar','customer_path' => 'varchar','status' => 'tinyint','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','fullname' => '','email' => '','phone_number' => '','gender' => '','address' => '','residence_state'=>'','country'=>'','customer_path' => 'Customer Image','status' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['status' => '1','date_created' => 'current_timestamp()'];

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
public static $documentField = ['customer_path'=>['type'=>['jpeg','jpg','png'],'size'=>'5242880','directory'=>'customer/','preserve'=>false]]; 

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
public static $tableAction = ['enable'=>'getEnabled','delete' => 'delete/customer', 'edit' => 'edit/customer'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getFullnameFormField($value = ''){
	return "<div class='form-group'>
				<label for='fullname'>Fullname</label>
				<input type='text' name='fullname' id='fullname' value='$value' class='form-control' required />
			</div>";
} 
public function getEmailFormField($value = ''){
	return "<div class='form-group'>
				<label for='email'>Email</label>
				<input type='email' name='email' id='email' value='$value' class='form-control'  />
			</div>";
} 
public function getPhone_numberFormField($value = ''){
	return "<div class='form-group'>
				<label for='phone_number'>Phone Number</label>
				<input type='text' name='phone_number' id='phone_number' value='$value' class='form-control'  />
			</div>";
}
public function getResidence_stateFormField($value = ''){
	return "<div class='form-group'>
				<label for='residence_state'>Residence State</label>
				<input type='text' name='residence_state' id='residence_state' value='$value' class='form-control' />
			</div>";
}
public function getCountryFormField($value = ''){
	return "<div class='form-group'>
				<label for='country'>Country</label>
				<input type='text' name='country' id='country' value='$value' class='form-control' />
			</div>";
} 
public function getGenderFormField($value = ''){
	$arr =array('Male','Female','Other');
       $option = buildOptionUnassoc($arr,$value);
       return "<div class='form-group'>
       		<label for='gender' >Gender</label>
              <select name='gender' id='gender' class='form-control'>
              $option
              </select>
</div>";
} 
public function getAddressFormField($value = ''){
	return "<div class='form-group'>
				<label for='address'>Address</label>
				<textarea name='address' id='address' class='form-control'>$value</textarea>
			</div>";
} 
public function getCustomer_pathFormField($value = ''){
	$path =  ($value != '') ? $value : "";
       return "<div class='row'>
                <div class='col-lg-8'>
                    <div class='form-group'>
                    <label>Customer Profile</label>
                <input type='file' class='file-input' data-show-caption='false' data-show-upload='false' data-fouc name='customer_path' id='customer_path' />
                <span class='form-text text-muted'>Max File size is 5MB. Supported formats: <code> jpeg,jpg,png</code></span></div></div>
                <div class='col-sm-4'><img src='$path' alt='customer profile' class='img-responsive' width='30%'/></div>
            </div><br>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
	<label class='form-checkbox'>Status</label>
	<select class='form-control' id='status' name='status' >
		<option value='1'>Yes</option>
		<option value='0' selected='selected'>No</option>
	</select>
	</div> ";
} 
public function getDate_createdFormField($value = ''){
	return "";
} 

protected function getUser()
{
	$query ="SELECT * FROM user WHERE user_table_id=? and user_type='customer' ";
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\User($result[0]);
	return $resultObject;
}

public function enable($id=null,&$db=null)
{
	if ($id == NULL && !isset($this->array['ID'])) {
		throw new Exception("object does not have id");
	}
	if ($id == NULL) {
		$id = $this->array["ID"];
	}
	$db = $this->db;
	$db->transBegin();
	return $this->setEnabled($id,1,$db);
}

public function delete($id=null,&$db=null)
{  
    $db = $db ?? $this->db;
    $db->transBegin();
    $customer = new Customer(['ID'=>$id]);
    $customer->load();
    $userKyc = $customer->user->user_kyc_details;
    $accountHolder = null;
    if($userKyc){
    	$accountHolder = $userKyc->account_holder;
    }
    if(parent::delete($id,$db)){
        $query="delete from user where user_table_id=? and user_type='customer'";
        if($this->query($query,array($id))){
        	if(!removeModelImage($db,'customer','ID',$id)){
        		// this would mean it doesn't exists
        	}
        	if($accountHolder){
        		$userKyc->deleteMonoAccountHolder(); // if failed, fail gracefully
        	}
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

public function getCustomerOption($value){
	$value = ($value != "") ? $value : "";
	$disable = ($value != '') ? "disabled" : ""; // this means edit function has passed down the value
	$where = ($value != '') ? " where ID= '$value' " : " where status = '1'";
	$db = db_connect();
	$query = "select id,fullname as value from customer $where order by value asc";
	$result ="<div class='form-group'>
		<label for='customer_id'>Customer's name</label>";
		$option = buildOptionFromQuery($db,$query,null,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='customer_id' id='customer_id' class='form-control select' required>
				<option value=''>..choose customer....</option>
					$option
				</select>";
		
	$result.="</div>";
	return $result;	
}

public function viewList(int $id=null, ?string $type,int $limit=200,bool $runQuery=false){
	$query = null;
	$param = null;
	$whereClause = null;

	if($runQuery){
		$whereClause = ($id != null) ? " where customer.ID = '$id'" : "";
		$query = "SELECT ID,fullname,email,phone_number,upper(gender) gender,address,residence_state,country,customer_path,if(status, 'Active', 'Inactive') status,date_created,date_modified from customer $whereClause order by ID desc limit $limit";
		$result = $this->query($query);
		return (!empty($result)) ? $result[0] : false;
	}

	// this is for verified customer
	if($type == 'verified'){
		$query = "SELECT a.ID,fullname,email,phone_number,upper(gender) gender,a.status from customer a join user b on b.user_table_id = a.id join user_kyc_details c on c.user_id = b.id where b.user_type = 'customer' and c.bvn_status = '1' order by a.ID desc limit $limit";
		return $query;
	}
	else if($type == 'unverified'){
		$query = "SELECT a.ID,fullname,email,phone_number,upper(gender) gender,a.status from customer a join user b on b.user_table_id = a.id left join user_kyc_details c on c.user_id = b.id where b.user_type = 'customer' and c.bvn_status is null order by a.ID desc limit $limit";
		return $query;
	}
	else{
		$query = "SELECT a.ID,fullname,phone_number as phone_no,email,IFNULL(d.amount,0.00) as 'wallet_balance(N)',IFNULL(e.amount,0.00) as 'giveaway_wallet_balance(N)',(select distinct count(*) from customer join user on user.user_table_id = customer.id join cashback on cashback.user_id = user.id where user.user_type = 'customer' and user.id = b.id) as number_of_games_played,if(bvn_status = 1, '<span class=\'badge badge-success text-success\'>VERIFIED</span>', '<span class=\'badge badge-danger text-white\'>NOT VERIFIED</span>') as verified_status,a.status from customer a join user b on b.user_table_id = a.id left join user_kyc_details c on c.user_id = b.id left join wallet d on d.user_id = b.id left join bonus_wallet e on e.user_id = b.id where b.user_type = 'customer' order by a.date_created desc limit 10000";
		return $query;
	}
}



 
}

?>
