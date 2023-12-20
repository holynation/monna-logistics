<?php

/**
 * The controller that link to the model.
 *all response in this class returns a json object return
 */

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\AccessControl;
use App\Models\ModelControllerCallback;
use App\Models\ModelControllerDataValidator;
use CodeIgniter\I18n\Time;
use Exception;


class Modelcontroller extends BaseController
{
	private $_rootUploadsDirectory = ROOTPATH."writable/uploads/";
	private $_publicDirectory = ROOTPATH."public/uploads/";

	// RULE: date_created comes first,then date_modified or any named date
	// NOTE: it only accept two diff date,nothing more than that.
	private $modelTimestamp = [
		'default' => ['date_created', 'date_modified'],
	];
	
	private $accessControl;
	private $webSessionManager;
	private $modelControllerCallback;
	private $modelControllerDataValidator;
	private $crudNameSpace = 'App\Models\Crud';

	public function __construct()
	{
		$this->accessControl = new AccessControl; //for authentication authorization validation
		$this->modelControllerCallback = new ModelControllerCallback;
		$this->modelControllerDataValidator = new ModelControllerDataValidator;
		$this->webSessionManager = new WebSessionManager;

		if (!$this->webSessionManager->isSessionActive()) {
			header("Location:" . base_url());
			exit;
		}

		if ($this->webSessionManager->getCurrentuserProp('user_type') == 'admin') {
			$role = loadClass('role');
			$role->checkWritePermission();
		}
	}

	public function add($model, $filter = false, $parent = '')
	{
		// the parent field is optional
		try {
			if (empty($model)) { //make sure empty value for model is not allowed.
				echo createJsonMessage('status', false, 'message', 'An error occured while processing information', 'description', 'The model parameter is null so it must not be null');
				return;
			}

			unset($_POST['MAX_FILE_SIZE']);
			$this->insertSingle($model);
		} catch (\Exception $ex) {
			echo $ex->getMessage();
			$this->db->transRollback();
		}
	}

	//this function is used to  document
	private function processFormUpload(string $model, $parameter, $insertType = false,&$message=null)
	{
		$modelName = $model;
		$newModel = loadClass($model);
		$paramFile= $newModel::$documentField;

		if (empty($paramFile) || empty($_FILES)) {
			return $parameter;
		}
		$fields = array_keys($_FILES);
		foreach ($paramFile as $name => $value) {
			if (in_array($name, $fields)) {
				// this is a precaution if no keys of this name are not set in the array
				$preserve = false;
				$max_width = 0;
				$max_height = 0;
				$directory = "";
				extract($value);

				$method = "get".ucfirst($modelName)."Directory";
				$uploadDirectoryManager = new \App\Models\UploadDirectoryManager;
				if (method_exists($uploadDirectoryManager, $method)) {
					$dir  = $uploadDirectoryManager->$method($parameter);
					if ($dir === false) {
						exit(createJsonMessage('status', false, 'message', 'Error while uploading file'));
					}
					$directory .= $dir;
				}

				$currentUpload = $this->uploadFile($modelName, $name, $type, $size, $directory, $message, $insertType, $preserve, $max_width, $max_height);
				if ($currentUpload == false) {
					return false;
				}
				$parameter[$name] = $message;
			} else {
				continue;
			}
		}
		return $parameter;
	}

	private function uploadFile($model, $name, $type, $maxSize, $destination, &$message = '', $insertType = false, $preserve = false, $max_width = 0, $max_height = 0)
	{
		if (!$this->checkFile($name, $message)) {
			return false;
		}
		$filename = $_FILES[$name]['name'];
		$ext = strtolower(getFileExtension($filename));
		$fileSize = $_FILES[$name]['size'];
		$typeValid = is_array($type) ? in_array(strtolower($ext), $type) : strtolower($ext) == strtolower($type);
		if (!empty($filename) &&  $typeValid  && !empty($destination)) {
			if (!is_null($maxSize) && $fileSize > $maxSize) {
				$calcsize = calc_size($maxSize);
				$message = "The file you are attempting to upload is larger than the permitted size ($calcsize)";return false;
			}

			$publicDestination = $this->_publicDirectory . $destination;
			if(!is_dir($publicDestination)){
				mkdir($publicDestination, 0777, true);
			}
			$publicDestination = $destination;
			$destination = $this->_rootUploadsDirectory . $destination;
			if (!is_dir($destination)) {
				mkdir($destination, 0777, true);
			}

			// using this is to check whether max_width or max_height was passed
			if (($max_width !== 0 && $max_height !== 0) || $max_width !== 0 || $max_height !== 0) {
				$config['max_width'] = $max_width;
				$config['max_height'] = $max_height;
				$temp_name = $_FILES[$name]['tmp_name'];

				if (!$this->isAllowedDimensions($temp_name, $max_width, $max_height)) {
					$message = "The image you are attempting to upload doesn't fit into the allowed dimensions (max_width:$max_width x max_height:$max_height).";return false;
				}
			}

			$naming = '';
			$new_name = $this->webSessionManager->getCurrentuserProp('user_table_id') . '_' . uniqid() . "_" . date('Y-m-d') . '.' . $ext;
			if ($insertType) {
				$getUpload = $this->getUploadID($model, $insertType, $name);
				if ($getUpload === 'insert') {
					// this means inserting
					$naming = ($preserve) ? $filename : $new_name;
				} else {
					$naming = basename($getUpload); // this means updating
				}
			} else {
				// this means inserting
				$naming = ($preserve) ? $filename : $new_name;
			}
			$destination .= $naming;
			$publicDestination .= $naming;
			if (move_uploaded_file($_FILES[$name]['tmp_name'], $destination)) {
				$destination = $this->createFileSymlink($publicDestination, $destination);
				$message = base_url($destination);
				return true;
			} else {
				$message = "Error while uploading file. please try again";
				return false;
			}
		} else {
			$message = "Error while uploading file. please try again";return false;
		}
		$message = 'Error while uploading file. please try again';return false;
	}

	private function isAllowedDimensions($temp, $max_width = 0, $max_height = 0)
	{

		if (function_exists('getimagesize')) {
			$D = @getimagesize($temp);

			if ($max_width > 0 && $D[0] > $max_width) {
				return FALSE;
			}

			if ($max_height > 0 && $D[1] > $max_height) {
				return FALSE;
			}
		}

		return TRUE;
	}

	private function createFileSymlink(string $link, string $target)
	{
        return createSymlink($link, $target);
	}

	private function getUploadID($model, $id, $name = '')
	{
		if ($id) {
			// return $id;
			// this means that it is updating
			$query = "select $name from $model where id = ?";
			$result = $this->db->query($query, array($id));
			$result = $result->getResultArray();

			// the return message 'insert' is a rare case whereby there is no media file at first
			// yet one want to add the media file through update action
			return (!empty($result[0][$name])) ? $result[0][$name] : 'insert';
		} else {
			// this means it is inserting
			$query = "select id from $model order by id desc limit 1";
			$result = $this->db->query($query);
			$result = $result->getResultArray();
			if ($result) {
				return $result[0]['id'];
			}
			return 1; //if no initial record
		}
	}

	private function checkFile($name, &$message = '')
	{
		$error = !$_FILES[$name]['name'] || $_FILES[$name]['error'];
		if ($error) {
			if ((int)$error === 2) {
				$message = 'File larger than expected';
				return false;
			}
			$message = "Please do check the file [{$_FILES[$name]['name']}] complies with requirement";
			return false;
		}

		if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			$this->db->transRollback();
			$message = 'Uploaded file not found';
			return false;
		}
		return true;
	}

	//this function will return the last auto generated id of the last insert statement
	private function getLastInsertId()
	{
		return getLastInsertId($this->db);
	}

	private function DoAfterInsertion($model, $type, $data, &$db, &$message = '', &$redirect = '')
	{
		$method = 'on' . ucfirst($model) . 'Inserted';
		if (method_exists($this->modelControllerCallback, $method)) {
			return $this->modelControllerCallback->$method($data, $type, $db, $message, $redirect);
		}
		return true;
	}

	// the message variable will give the eror message if there is an error and the variable is passed
	private function validateModelData($model, $type, &$data, &$db, &$message = '')
	{
		$method = 'validate' . ucfirst($model) . 'Data';
		if (method_exists($this->modelControllerDataValidator, $method)) {
			$result = $this->modelControllerDataValidator->$method($data,$type,$db,$message);
			return $result;
		}
		return true;
	}

	/**
	 * [insertSingle description]
	 * @param  string $model [description]
	 * @return [type]        [description]
	 */
	private function insertSingle(string $model)
	{
		$this->modelCheck($model, 'c');
		$message = '';
		$data = $this->request->getPost(null);
		$newModel = loadClass($model);
		unset($data["edu-submit"]);
		$parameter = $data;
		$parameter = removeEmptyAssoc($parameter);
		if ($this->validateModelData($model, 'insert', $parameter, $this->db, $message) == false) {
			echo createJsonMessage('status', false, 'message', $message);
			return;
		}

		// this is to ensure data being passed from validateModelData is merged
		// with the rest of the data if available
		if($tempData = $this->getHiddenParameter($newModel, $parameter)){
			if(!empty($tempData)){
				$parameter = array_merge($parameter,$tempData);
			}
		}

		// ensuring to populate model timestamp
		if ($tempParameter = $this->createModelTimestamp($newModel, $model)){
			if(!empty($tempParameter)){
				$parameter = array_merge($parameter,$tempParameter);
			}
		}

		if(!empty($_FILES)){
			$parameter = $this->processFormUpload($model,$parameter,false,$message);
			if(!$parameter && $message != ''){
				echo createJsonMessage('status', false, 'message', $message);return;
			}
		}

		$data = $parameter;
		$parameter = $this->extractSubset($parameter, $newModel);
		$newModel->setArray($parameter);
		if (!$this->validateModel($newModel, $message)) {
			echo createJsonMessage('status', false, 'message', $message);
			return;
		}
		$message = '';
		$this->db->transBegin();
		if ($newModel->insert($this->db, $message)) {
			$inserted = $this->getLastInsertId($this->db);
			$data['LAST_INSERT_ID'] = $inserted;

			if ($this->DoAfterInsertion($model, 'insert', $data, $this->db, $message, $redirect)) {
				$this->db->transCommit();
				if ($redirect != '') {
					$arr = array();
					$arr['status'] = true;
					$arr['message'] = $redirect;
					echo json_encode($arr);
					return;
				} else {
					$message = empty($message) ? 'Operation Successful ' : $message;
				}
				echo createJsonMessage('status', true, 'message', $message, 'data', $inserted);
				return;
			}
		}
		$this->db->transRollback();
		$message = empty($message) ? "Error occured while saving information" : $message;
		echo createJsonMessage('status', false, 'message', $message);
	}

	private function log($model,$description){
		// $this->application_log->log($model,$description);
	}

	public function update($model, $id = '', $filter = false, $flagAction = false)
	{
		if (empty($id) || empty($model)) {
			echo createJsonMessage('status', false, 'message', 'an error occured while processing information', 'description', 'the model parameter is null so it must not be null');
			return;
		}
		$this->updateSingle($model, $id, $filter, $flagAction);
	}

	private function updateSingle($model, $id, $filter, $flagAction = false)
	{
		$this->modelCheck($model, 'u');
		$newModel = loadClass($model);
		$data = $this->request->getPost(null);
		unset($data["edu-submit"], $data["edu-reset"]);
		$parameter = $data;
		if(!empty($_FILES)){
			$res = $this->processFormUpload($model,$data,$id,$message);
			if(!$res && $message != ''){
				$arr['status'] = false;
				$arr['message'] = $message;
				echo json_encode($arr);return;
			}
			$data = $res;
		}

		// ensuring to populate model timestamp
		if ($tempParameter = $this->createModelTimestamp($newModel,$model,'update')){
			if(!empty($tempParameter)){
				$parameter = array_merge($parameter,$tempParameter);
			}
		}

		$this->db->transBegin();
		$parameter['model_update_id'] = $id;
		if ($this->validateModelData($model, 'update', $parameter, $this->db, $message)) {
			// this is to ensure data being passed from validateModelData is merged
			// with the rest of the data if available
			if($tempData = $this->getHiddenParameter($newModel, $parameter)){
				if(!empty($tempData)){
					$parameter = array_merge($parameter,$tempData);
				}
			}

			$data = $parameter;
			// pass in the value needed by the model itself and discard the rest.
			$parameter = $this->extractSubset($parameter, $newModel);
			$newModel->setArray($parameter);
			if (!$newModel->update($id, $this->db)) {
				$this->db->transRollback();
				$arr['status'] = false;
				$arr['message'] = 'Unable to update, please try again later';
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			}
			$data['ID'] = $id;
			if ($this->DoAfterInsertion($model, 'update', $data, $this->db, $message, $redirect)) {
				$this->db->transCommit();
				if ($redirect != '') {
					$arr = array();
					$arr['status'] = true;
					$arr['message'] = $redirect;
					echo json_encode($arr);
					return;
				} else {
					$message = empty($message) ? 'Operation Successful ' : $message;
				}
				$arr['status'] = true;
				$arr['message'] = $message;
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			} else {
				$this->db->transRollback();
				$arr['status'] = false;
				$arr['message'] = $message;
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			}
		} else {
			$this->db->transRollback();
			$arr['status'] = false;
			$arr['message'] = $message;
			if ($flagAction) {
				$arr['flagAction'] = $flagAction;
			}
			echo json_encode($arr);
			return;
		}
	}

	/**
	 * This is to upto create timestamp on model
	 * @param  object $model 
	 * @param  [type] $label [description]
	 * @return [type]        [description]
	 */
	public function createModelTimestamp(object $model,string $label,string $type='insert'){
		$parameter = [];
		$labelArray = array_keys($model::$labelArray);
		$dateLabel = "default";
		if (array_key_exists($label, $this->modelTimestamp)) {
			$dateLabel = $this->modelTimestamp[$label];
		}

		$dateParam = $this->modelTimestamp[$dateLabel];
		$dateString = 'now';
		if (in_array($dateParam[0], $labelArray) && $type == 'insert') { // date_created
			$date = new Time($dateString);
			$parameter[$dateParam[0]] = $date->format('Y-m-d H:i:s');
		}
		if (in_array($dateParam[1], $labelArray)) { // date_modified
			$date = new Time($dateString);
			$parameter[$dateParam[1]] = $date->format('Y-m-d H:i:s');
		}
		return $parameter;
	}

	/**
	 * This is to get parameter not originally in the request
	 * @param  object $model [description]
	 * @param  array  $data  [description]
	 * @return [type]        [description]
	 */
	private function getHiddenParameter(object $model, array $data){
		return array_intersect_key($data, $model::$labelArray);
	}

	public function delete($model, $id = '')
	{
		if (isset($_POST['ID'])) {
			$id = $_POST['ID'];
		}
		if (empty($id)) {
			echo createJsonMessage('status', false, 'message', 'error occured while deleting information');
			return;
		}

		$this->modelCheck($model, 'd');
		$newModel = loadClass("$model");
		if ($newModel->delete($id)) {
			echo createJsonMessage('status', true, 'message', 'information deleted successfully');
		} else {
			echo createJsonMessage('status', false, 'message', 'error occured while deleting information');
		}
	}

	private function modelCheck($model, $method)
	{
		if (!$this->isModel($model)) {
			echo createJsonMessage('status', false, 'message', "{$model} is not an entity model");
			exit;
		}
		// echo "got here";
		// if (!$this->accessControl->moduleAccess($model,$method)) {
		// 	echo createJsonMessage('status',false,'message','operation access denied');
		// 	exit;
		// }
	}

	//this function checks if the argument id actually  a model
	private function isModel($model)
	{
		$model = loadClass("$model");
		if (!empty($model) && $model instanceof $this->crudNameSpace) {
			return true;
		}
		return false;
	}

	//check that the algorithm fit and that required data are not empty
	private function validateModel($model, &$message)
	{
		return $model->validateInsert($message);
	}
	
	//function to extract a subset of fields from a particular field
	private function extractSubset($array, $model)
	{
		//check that the model is instance of crud
		//take care of user upload substitute the necessary value for the username
		//dont specify username directly
		$result = array();
		if ($model instanceof $this->crudNameSpace) {
			$keys = array_keys($model::$labelArray);
			$valueKeys = array_keys($array);
			$temp = array_intersect($valueKeys, $keys);
			foreach ($temp as $value) {
				$result[$value] = $array[$value];
			}
		}
		return $result;
	}

	private function goPrevious($message, $path = '')
	{
		$location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if (empty($location) || !startsWith($location, base_url())) {
			$location = $path;
		}
		$this->session->set_flashdata('message', $message);
		header("location:$location");
	}

	//function for downloading data template
	public function template($model)
	{
		//validate permission here too.
		if (empty($model)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model = loadClass("$model");
		$model = new $model;
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$exception = null;
		if (isset($_GET['exc'])) {
			$exception = explode('-', $_GET['exc']);
		}
		$model->downloadTemplate($exception);
	}

	public function export($model)
	{
		$condition = null;
		$args  = func_get_args();
		if (count($args) > 1) {
			$method = 'export' . ucfirst($args[1]);
			if (method_exists($this, $method)) {
				$condition = $this->$method();
			}
		}
		if (empty($model)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model = loadClass("$model");
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model->export($condition);
	}

	private function loadUploadedFileContent($filePath = false, $filename = '')
	{
		$filename = ($filename != '') ? $filename : 'bulk-upload';
		$status = $this->checkFile($filename, $message);
		if ($status) {
			if (!endsWith($_FILES[$filename]['name'], '.csv')) {
				echo "Invalid file format";
				exit;
			}
			$path = $_FILES[$filename]['tmp_name'];
			$content = file_get_contents($path);
			if ($filePath) {
				$res = move_uploaded_file($_FILES[$filename]['tmp_name'], $filePath);
				if (!$res) {
					exit("error occured while performing file upload");
				}
			}
			return $content;
		} else {
			echo "$message";
			exit;
		}
	}

	/**
	 * @param string $model
	 * @return \App\Views\upload_report
	 */
	public function modelFileUpload(string $model){
		$content = $this->loadUploadedFileContent();
		$content = trim($content);
		$array = stringToCsv($content);
		$header = array_shift($array);
		$defaultValues = null;
		$args = func_get_args();
		if (count($args) > 1) {
			$method = 'upload'.ucfirst($args[1]);
			if (method_exists($this, $method)) {
				$defaultValues = $this->$method();
				$keys = array_keys($defaultValues);
				for ($i=0; $i < count($keys); $i++) { 
					$header[]=$keys[$i];
				}
				foreach ($defaultValues as $field => $value) {
					replaceIndexWith($array,$field,$value);
				}
			}
		}
		//check for rarecases when the information in one of the fields needed to be replaces
		if (isset($_GET['rp'] ) && $_GET['rp']) {
			$funcName = $_GET['rp'];
			# go ahead and call the function make the change
			$funcName = 'replace'.ucfirst($funcName);
			if (method_exists($this, $funcName)) {
				//the function must accept the parameter as a reference
				$this->$funcName($header,$array);
			}
		}
		$db=null;
		$arr =array('admin');
		if (in_array($model, $arr)) {
			$this->db->transBegin();
			$db=$this->db;
		}
		$oldModel = $model;
		$model = loadClass($model);
		$result = $model->upload($header,$array,$message,$db);
		$data=array();
		$data['pageTitle']='file upload report';
		$data['backLink'] = $_SERVER['HTTP_REFERER'];
		if ($result) {
			$data['status']=true;
			$data['message']= ($message != '') ? $message : 'You have successfully performed the operation...';
			$data['model']=$oldModel;
			if ($result && in_array($oldModel, $arr)) {
				$db->transCommit();
			}
		}
		else{
			$data['status']=false;
			$data['message']=$message;
			$data['model']=$oldModel;
			if (!$result && in_array($oldModel, $arr)) {
				$db->transRollback();
			}
		}

		if ($this->webSessionManager->getCurrentuserProp('user_type')=='admin') {
			$data['canView']=$this->getAdminSidebar();
		}
		$data['webSessionManager'] = $this->webSessionManager;
		return view('uploadreport',$data);
	}

	/**
	 * @return array
	 */
	private function getAdminSidebar()
	{
		$adminData = new \App\Models\Custom\AdminData;
		$admin = loadClass('admin');
		$admin = new $admin();
		$admin->ID= $this->webSessionManager->getCurrentuserProp('user_table_id');
		$admin->load();
		$role = $admin->role;
		return $adminData->getCanViewPages($role);
	}

	
}
