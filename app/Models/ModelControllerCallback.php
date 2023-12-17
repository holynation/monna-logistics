<?php 
	/**
	* This is the class that contain the method that will be called whenever any data is inserted for a particular table.
	* the url path should be linked to this page so that the correct operation is performed ultimately. T
	*/
namespace App\Models;

use App\Models\WebSessionManager;

class ModelControllerCallback
{
	private $webSessionManager;

	function __construct()
	{
		helper(['string','url','array']);
		$this->webSessionManager = new WebSessionManager;
	}

	public function onAdminInserted($data,$type,&$db,&$message)
	{
		//remember to remove the file if an error occured here
		//the user type should be admin
		$user = loadClass('user');
		if ($type == 'insert') {
			// login details as follow: username = email, password = firstname(in lowercase)
			$password = encode_password(strtolower($data['lastname']));
			$param = array('user_type'=>'admin','username'=>$data['email'],'username_2'=>'','password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID'],'status'=>'1');
			$std = new $user($param);
			if ($std->insert($db,$message)) {
				return true;
			}
			return false;
		}
		return true;
	}

	public function onCustomersInserted($data,$type,&$db,&$message)
	{
		//remember to remove the file if an error occured here
		//the user type should be admin
		$user = loadClass('user');
		if ($type == 'insert') {
			// login details as follow: username = email, password = firstname(in lowercase)
			$password = encode_password('_12345678');
			$param = array('user_type'=>'customers','username'=>$data['email'],'username_2'=>'','password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID'],'status'=>'1');
			$std = new $user($param);
			if ($std->insert($db,$message)) {
				return true;
			}
			return false;
		}
		return true;
	}

}

