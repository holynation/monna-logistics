<?php
namespace App\Controllers;

use App\Entities\Customers;
use App\Models\WebSessionManager;
use App\Models\ModelFormBuilder;
use App\Models\TableWithHeaderModel;
use App\Models\QueryHtmlTableObjModel;
use App\Models\FormConfig;
use App\Models\Custom\AdminData;
use CodeIgniter\I18n\Time;

class Viewcontroller extends BaseController{

  private $errorMessage; // the error message currently produced from this cal if it is set, it can be used to produce relevant error to the user.
  private $access = array();
  private $appData;
  private $webSessionManager;
  private $modelFormBuilder;
  private $tableWithHeaderModel;
  private $queryHtmlTableObjModel;
  private $adminData;
  private $crudNameSpace = 'App\Models\Crud';

  public function __construct(){
    $this->webSessionManager = new WebSessionManager;
    $this->modelFormBuilder = new ModelFormBuilder;
    $this->tableWithHeaderModel = new TableWithHeaderModel;
    $this->queryHtmlTableObjModel = new QueryHtmlTableObjModel;
    $this->adminData = new AdminData;

    if (!$this->webSessionManager->isSessionActive()) {
      header("Location:".base_url());exit;
    }
	}

// bootstrapping functions 
public function view($model,$page='index',$third='',$fourth=''){
  if ( !(file_exists(APPPATH."Views/$model/") && file_exists(APPPATH."Views/$model/$page".'.php')))
  {
    throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
  }
  // this number is the default arg that ID is the last arg i.e 3 = id

  $defaultArgNum = 4;
  if($defaultArgNum < func_num_args()){
    $data['extra'] = func_get_args();
    $data['entityName'] = ($fourth != '') ? $third : "";
  }else{
    $modelID = ($fourth == '') ? $third : $fourth;
    $data['id'] = urldecode($modelID);
    $data['entityName'] = ($fourth != '') ? $third : "";
  }
  $tempTitle = removeUnderscore($model);
  $title = $page=='index'?$tempTitle:ucfirst($page)." $tempTitle";
  $exceptions = array(); // pages that does not need active session
  $data['model'] = $page;

  if (!in_array($page, $exceptions)) {
    if (!$this->webSessionManager->isSessionActive()) {
      redirect(base_url());exit;
    }
  }

  if (method_exists($this, $model)) {
    $this->$model($page,$data);
  }
  $methodName = $model.ucfirst($page);

  if (method_exists($this, $model.ucfirst($page))) {
    $this->$methodName($data);
  }

  $data['message'] = $this->webSessionManager->getFlashMessage('message');
  $data['webSessionManager'] = $this->webSessionManager;

  echo view("$model/$page", $data);
}

private function admin($page,&$data)
{
  $role_id = $this->webSessionManager->getCurrentUserProp('role_id');
  if (!$role_id) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }

  $role = false;
  if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin') {
    $admin = loadClass('admin');
    $admin->id = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $data['admin'] = $admin;
    $role = $admin->role;
    if(!$role){
      exit("Kindly ensure a role is assigned to this admin user");
    }
  }
  $data['currentRole'] = $role;
  if (!$role) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }
  $path = 'vc/admin/'.$page;
  $extraPage = ($data['entityName']) ? $data['entityName'] : $data['id'];
  if($extraPage != ''){
    $path .= "/".$extraPage;
  }

  if(isset($data['extra'][3])){
    $path .= "/".$data['extra'][3];;
  }
  if($this->request->getGet('type')){
    $path .= "?type=".$this->request->getGet('type');
  }

  // this approach is use so as to allow this page pass through using a path
  // that is already permitted
  if ($page == 'permission') {
    $path = 'vc/create/role';
  }

  if (!$role->canView($path)) {
    echo show_access_denied();exit;
  }
  // caching this role pages
  if(!$canView = cache('canView')){
    $canView = $this->adminData->getCanViewPages($role);
    cache()->save('canView',$canView, 900); // cache for 15mins
  }

  // print_r($canView);exit;
  $data['canView'] = $canView;
}

private function adminDashboard(&$data)
{
  $data = array_merge($data,$this->adminData->loadDashboardData());
}

private function adminPermission(&$data)
{
  $data['id'] = urldecode($data['id']);
  if (!isset($data['id']) || !$data['id'] || $data['id']==1) {
    $this->show_404();exit;
  }
  $role = loadClass('role');
  $newRole = new $role(array('ID'=>$data['id']));
  $newRole->load();
  $data['role'] = $newRole;
  $data['allPages'] = $this->adminData->getAdminSidebar(true);
  $sidebarContent = $this->adminData->getCanViewPages($data['role'],true);
  $data['permitPages'] = $sidebarContent;
  $data['allStates'] = $data['role']->getPermissionArray();
  $data['pageTitle'] = 'permission';
  $data['modelName'] = 'Permission';
}

private function adminProfile(&$data)
{
  $admin = loadClass('admin');
  $admin = new $admin();
  $admin->id = $this->webSessionManager->getCurrentUserProp('user_table_id');
  $admin->load();
  $data['admin'] = $admin;
}

private function adminInvoices(&$data)
{
  $data['invoiceNum'] = generateReceipt();
  $data['trackNum'] = date('ymd').uniqueString(13);
  $query = "SELECT id, concat(lastname, ' ', firstname) as value from customers where status = ?";
  $options = buildOptionFromQuery($this->db, $query, [1], isset($_GET['customer']) ? $_GET['customer'] : null);
  $data['customerOptions'] = $options;
}

public function processInvoices(){
  // print_r($_POST);exit;
  if(!$this->validate([
    'invoice_date' => [
      'label' => 'invoice date',
      'rules' => 'required|valid_date',
    ],
    'invoice_no' => [
      'label' => 'invoice number',
      'rules' => 'required|alpha_numeric',
    ],
    'track_number' => [
      'label' => 'track number',
      'rules' => 'required|alpha_numeric',
    ],
    'customer' => 'required',
    'bill_to_name' => [
      'label' => 'bill to name',
      'rules' => 'required|string|min_length[3]'
    ],
    'bill_to_email' => [
      'label' => 'bill to email',
      'rules' => 'required|valid_email',
    ],
    'bill_to_phone' => [
      'label' => 'bill to phone',
      'rules' => 'required|min_length[10]',
    ],
    'bill_to_city' => [
      'label' => 'bill to city',
      'rules' => 'required|string'
    ],
    'bill_to_country' => [
      'label' => 'bill to country',
      'rules' => 'required|string'
    ],
    'bill_to_address' => [
      'label' => 'bill to address',
      'rules' => 'required|string'
    ],
    'bill_to_postalcode' => [
      'label' => 'bill to postalcode',
      'rules' => 'permit_empty|alpha_numeric'
    ],
    'description' => [
      'label' => 'description',
      'rules' => 'permit_empty|required'
    ],
    'quantity' => [
      'label' => 'quantity',
      'rules' => 'required_with[description]'
    ],
    'price' => [
      'label' => 'price',
      'rules' => 'required_with[description]'
    ],
    'invoice_notes' => [
      'label' => 'invoice notes',
      'rules' => 'permit_empty|string'
    ],
  ])){
    foreach($this->validator->getErrors() as $error){
      displayJson(false, $error);return;
    }
    $this->webSessionManager->setFlashMessage('error', $this->validator->getErrors());
    return redirect()->back()->withInput();
  }

  $validData = $this->validator->getValidated();
  $discount  = $this->request->getPost('discount') ?? 0;
  $mustPay  = $this->request->getPost('must_pay');
  $tax  = $this->request->getPost('tax') ?? 0;
  $quantity = $validData['quantity'];
  $price = $validData['price'];

  $customers = loadClass('customers');
  $customers = $customers->getWhere(['id' => $validData['customer']],$c,0,null,false);
  if(!$customers){
    $message = "Unable to locate the customer, please try agian later";
    displayJson(false, $message);return;
    $this->webSessionManager->setFlashMessage('error', $message);
    return redirect()->back()->withInput();
  }
  $customers = $customers[0];

  $subTotal = 0;
  $total = 0;
  for($i = 0; $i < count($validData['description']); $i++){
    $validData['price'][$i] = str_replace(',', '', $validData['price'][$i]);
    $subTotal += (float)$validData['quantity'][$i] * (float)$validData['price'][$i];
  }

  $total += ($subTotal + $tax);
  $total -= $discount;

  $param = [
    'customers_id' => $validData['customer'],
    'invoice_no' => $validData['invoice_no'],
    'bill_from_name' => $customers->firstname." ".$customers->lastname." ".$customers->middlename,
    'bill_from_phone' => $customers->phone_number,
    'bill_from_address' => $customers->address ?: '',
    'bill_to_name' => $validData['bill_to_name'],
    'bill_to_email' => $validData['bill_to_email'],
    'bill_to_phone' => $validData['bill_to_phone'],
    'bill_to_city' => $validData['bill_to_city'],
    'bill_to_country' => $validData['bill_to_country'],
    'bill_to_address' => $validData['bill_to_address'],
    'bill_to_postalcode' => $validData['bill_to_postalcode'],
    'invoice_subtotal' => $subTotal,
    'invoice_tax' => $tax,
    'invoice_discount' => $discount,
    'invoice_total' => $total,
    'invoice_date' => $validData['invoice_date'],
    'invoice_notes' => $validData['invoice_notes'],
    'track_number' => $validData['track_number'],
  ];

  // $this->db->transBegin();
  $inserted = $this->db->table('invoices')->insert($param);
  $inserted = $this->db->insertID();

  $insertParam = [];
  for($i = 0; $i < count($validData['description']); $i++){
    $description = $validData['description'][$i];
    $price = $validData['price'][$i];
    $quantity = $validData['quantity'][$i];
    if($description != '' && $price != '' && $quantity != ''){
      $insertParam[] = [
        'invoices_id' => $inserted,
        'description' => $description,
        'quantity' => $quantity,
        'price' => $price
      ];
    }
  }

  // if(!$this->db->table('invoice_items')->insertBatch($insertParam)){
  //   $this->db->transRollback();
  //   displayJson(false, "Something went wrong, please try again later");
  //   return;
  // }

  $this->db->table('invoice_items')->insertBatch($insertParam);
  // $this->db->transCommit();
  displayJson(true, "You have successfully created the invoice");return;
}

/**
 * This would ensure that custom query on table model is allowed based
 * on different type of the entity
 * @param  [type] &$data [description]
 * @return [type]        [description]
 */
private function adminView_model(&$data){
  $id = (is_numeric($data['id'])) ? $data['id'] : null;
  $model  = ($data['entityName']) ? $data['entityName'] : $data['id'];
  
  $newModel = loadClass($model);
  $modelType = '';
  $modelType = @$_GET['type'] ?? "";

  $pageTitle = $this->getTitlePage($model) ?? ucfirst($modelType).' '.removeUnderscore($model);

  $result = $newModel->viewList($id,$modelType,200,false,$this->request);
  $data['modelName'] = $model;
  $data['pageTitle'] = $pageTitle;
  $data['queryString'] = $result;
  $data['dataParam'] = $id;
  $data['queryHtmlTableObjModel'] = $this->queryHtmlTableObjModel;
  $data['modelFormBuilder'] = $this->modelFormBuilder;
  $formConfig = new FormConfig(true);
  $data['configData'] = $formConfig->getInsertConfig($model);
  $data['model'] = $model;
  $data['modelObj'] = $newModel;
  $data['db'] = $this->db;
}

private function adminView_more(&$data){
  if($this->webSessionManager->getCurrentUserProp('user_type') == 'customer' && isset($data['customerID'])){
    $modelName = 'customer';
    $id = $data['customerID'];
    $modelType = 'profile';
  }
  else{
    $id = $data['extra'][4];
    $modelType = $data['extra'][3]; // this is the index of the type
    $modelName = $data['entityName'];
    if($modelName == 'profile'){
      $modelName = $this->request->getGet('made_by') ?? 'customer';
    }
  }

  $newModel = loadClass($modelName);

  $result = $newModel->viewList($id,$modelType,1,true);
  if($modelName == 'customer'){
    $customer = new Customer($result);
    $userID = $customer->user->ID;
    $data['user_id'] = $userID;
  }

  $data['modelStatus'] = (!$result) ? false : true;
  unset($result['ID']);
  $data['modelPayload'] = $result;
  $data['modelName'] = $modelName;
  $data['pageTitle'] = $this->getTitlePage($modelName) ?? $modelType.' '.removeUnderscore($modelName);
}

private function getTitlePage(string $modelName){
  $result = [
    'cashback'=>'cashback',
  ];
  return array_key_exists($modelName, $result) ? $result[$modelName] : null;
}

//function for loading edit page for general application
public function edit($model,$id){
  $userType = $this->webSessionManager->getCurrentUserProp('user_type');
  if($userType == 'admin'){
    $admin = loadClass('admin');
    $admin->id = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $role = $admin->role;
    $role->checkWritePermission();
    $role = true;
  }
  
  $ref = @$_SERVER['HTTP_REFERER'];
  if ($ref && !startsWith($ref,base_url())) {
    $this->show_404();
  }
  $exceptionList = array();
  if (empty($id) || in_array($model, $exceptionList)) {
    $this->show_404();
  }
  $formConfig = new FormConfig($role);
  $configData = $formConfig->getUpdateConfig($model);
  $exclude = ($configData && array_key_exists('exclude', $configData))?$configData['exclude']:[];

  $formContent = $this->modelFormBuilder->start($model.'_edit')
      ->appendUpdateForm($model,true,$id,$exclude,'')
      ->addSubmitLink(null,false)
      ->appendSubmitButton('Update','btn btn-success')
      ->build();
  $result = $formContent;
  displayJson(true,$result);
  return;
}

private function show_404(){
  throw new \CodeIgniter\Exceptions\PageNotFoundException();
}

// this method is for creation of form either in single or combine based on the page desire
public function create($model,$type='add',$data=null){
  if(!empty($type)){
    if($type=='add'){
      // this is useful for a page that doesn't follow normal procedure of a modal page
      $this->add($model,'add');
      return;
    }else{
      // this uses modal to show it content
      $this->add($model,$type,$data);
      return;
    }
  }
  return "please specify a type to be created (single page or combine page with view inclusive...)";
}

private function add($model,$type,$param=null)
{
  if (!$this->webSessionManager->isSessionActive()) {
    header("Location:".base_url());exit;
  }
  $role_id = $this->webSessionManager->getCurrentUserProp('role_id');
  $userType = $this->webSessionManager->getCurrentUserProp('user_type');
  if($userType == 'admin'){
    if (!$role_id) {
      $this->show_404();
    }
  }
  $role =false;
  if($userType == 'admin'){
    $admin = loadClass('admin');
    $admin->id = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $role = $admin->role;
    $data['admin'] = $admin;
    $data['currentRole'] = $role;
    $type = ($type == 'add') ? 'create' : $type;
    $path ="vc/$type/".$model;

    if (!$role->canView($path)) {
      echo show_access_denied();exit;
    }
    $type = ($type == 'create') ? 'add' : $type;
    $sidebarContent = $this->adminData->getCanViewPages($role);
    $data['canView'] = $sidebarContent;
    $role = true;
  }

  if ($model == false) {
    $this->show_404();
  }

  $newModel = loadClass($model);
  $modelClass = new $newModel;
  if (!is_subclass_of($modelClass ,$this->crudNameSpace)) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }
  $formConfig = new FormConfig($role);
  $data['configData'] = $formConfig->getInsertConfig($model);
  $data['model'] = $model;
  $data['modelObj'] = $newModel;
  $data['appConfig'] = $this->appData;

  // defining some object parameters
  $data['db'] = $this->db;
  $data['webSessionManager'] = $this->webSessionManager;
  $data['queryHtmlTableObjModel'] = $this->queryHtmlTableObjModel;
  $data['tableWithHeaderModel'] = $this->tableWithHeaderModel;
  $data['modelFormBuilder'] = $this->modelFormBuilder;

  echo view($type,$data);
}

public function changePassword()
{
  if(isset($_POST) && count($_POST) > 0 && !empty($_POST)){
    $curr_password = trim($_POST['current_password']);
    $new = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if (!isNotEmpty($curr_password,$new,$confirm)) {
      echo createJsonMessage('status',false,'message',"Empty field detected.please fill all required field and try again");
      return;
    }
    
    $id = $this->webSessionManager->getCurrentUserProp('ID');
    $user = loadClass('user');
    if($user->findUserProp($id)){
      $check = decode_password(trim($curr_password), $user->data()[0]['password']);
      if(!$check){
        echo createJsonMessage('status',false,'message','Please type-in your password correctly.','flagAction',false);
        return;
      }
    }

    if ($new !== $confirm) {
      echo createJsonMessage('status',false,'message','New password does not match with the confirmation password','flagAction',false);exit;
    }
    $new = encode_password($new);
    $passDate = date('Y-m-d H:i:s');
    $query = "update user set password = '$new',has_change_password = '1' where ID=?";
    if ($this->db->query($query,array($id))) {
      $this->webSessionManager->setContent('has_change_password','1');
      $arr['status'] = true;
      $arr['message'] = 'Operation successfull';
      $arr['flagAction'] = true;
      echo json_encode($arr);
      return;
    }
    else{
      $arr['status'] = false;
      $arr['message'] = 'Error occured during operation...';
      $arr['flagAction'] = false;
      echo json_encode($arr);
      return;
    }
  }
  return false;
}

}
