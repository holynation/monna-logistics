<?php 
	/**
	* This class like other controller class will have full access control capability
	*/
namespace App\Controllers;

use App\Models\Mailer;
use App\Models\WebSessionManager;
use Exception;

class Actioncontroller extends BaseController
{
	private $uploadedFolderName = 'public/uploads';
	private $crudNameSpace = 'App\Models\Crud';
	private $webSessionManager;
	private $mailer;
	/**
	 | NOTE: 1. To return things, it must be in json type using the provided function - createJsonMessage
	 */

	public function __construct()
	{
		helper('string');
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
		// basically the admin should be the one accessing this module
		if ($this->webSessionManager->getCurrentUserprop('user_type') == 'admin') {
			$role = loadClass('role');
			$role->checkWritePermission();
		}
	}

	// TODO: I WANNA WRAP EACH ACTION METHOD FOR BATCH OPERATION SUCH THAT WE CAN PERFORM MULTIPLE OPERATIONS ON THEM E.G MULTIPLE DELETE|DISABLE|ENABLE

	/**
	 * @param string 	$model
	 * @param int 		$id
	 * @return json|array
	 */
	public function disable(string $model,$id){
		$model = loadClass($model);
		//check that model is actually a subclass
		if ( empty($id)===false && is_subclass_of($model,$this->crudNameSpace)) {
			if($model->disable($id,$this->db)){
				echo createJsonMessage('status',true,'message',"action successfully performed",'flagAction',true);
			}else{
				echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
			}
		}
		else{
			echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
		}
	}

	/**
	 * @param string 	$model
	 * @param int 		$id
	 * @return json|array
	 */
	public function enable(string $model,$id){
		$tempModel = $model;
		$model = loadClass($model);
		//check that model is actually a subclass
		if ( !empty($id) && is_subclass_of($model,$this->crudNameSpace ) && $model->enable($id,$this->db)) {
			echo createJsonMessage('status',true,'message',"action successfully performed",'flagAction',true);
		}
		else{
			echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
		}
	}

	public function view($model,$id){

	}

	/**
	 * @param string $model
	 * @return json|array
	 */
	public function truncate(string $model){
		if($model){
			$builder = $this->db->table($model);
			if($builder->truncate()){
				echo createJsonMessage('status',true,'message',"item successfully truncated...",'flagAction',true);
			}else{
				echo createJsonMessage('status',false,'message',"cannot truncate item...",'flagAction',false);
			}
		}	
	}

	/**
	 * @param string 		$model
	 * @param string 		$field
	 * @param string|int	$value
	 * @return json|array
	 */
	public function deleteModelByUserId(string $model,$field,$value){
		$db=$this->db;
	    $db->transBegin();
	    $query="delete from $model where $field=?";
	    if($db->query($query,[$value])){
	        $db->transCommit();
	        echo createJsonMessage('status',true,'message','item deleted successfully...','flagAction',true);
	        return true;
	    }
	    else{
	        $db->transRollback();
	        echo createJsonMessage('status',false,'message','cannot delete item(s)...','flagAction',true);
	        return false;
	    }
	}

	/**
	 * @param string 	$model
	 * @param string 	$extra - This is to remove any files attached to this single *  entity
	 * @param int 		$id
	 * @return json|array
	 */
	public function delete(string $model,$extra='',$id=''){
		// verifying this action before performing it
		$id = ($id == '') ? $extra : $id;
		$extra = ($extra != '' && $id != '') ? base64_decode(urldecode($extra)) : $id;
		// this extra param is a method to find a file and removing it from the server
		if($extra){
			$newModel = loadClass($model);
			$paramFile = $newModel::$documentField;
			$directoryName = $model.'_path';
			$filePath =  $this->uploadedFolderName.'/'.@$paramFile[$directoryName]['directory'].$extra;
			$filePath = ROOTPATH.$filePath;
			if(file_exists($filePath)){
				@chmod($filePath, 0777);
				@unlink($filePath); // remove the symlink only
			}
			$filePath = ROOTPATH.'/'.@$paramFile[$directoryName]['directory'].$extra;
			if(file_exists($filePath)){
				@chmod($filePath, 0777);
				@unlink($filePath); // remove the original file image
			}
		}
		$newModel = loadClass($model);
		// check that model is actually a subclass
		if ( !empty($id) && is_subclass_of($newModel,$this->crudNameSpace ) && $newModel->delete($id)) {
			$desc = "deleting the model $model with id {$id}";
			// $this->logAction($this->webSessionManager->getCurrentUserProp('ID'),$model,$desc);
			echo createJsonMessage('status',true,'message','item deleted successfully...','flagAction',true);
			return true;
		}
		else{
			echo createJsonMessage('status',false,'message','cannot delete item...','flagAction',true);
			return false;
		}
	}

	/**
	 * @param string 	$model
	 * @param string 	$value
	 * @param int 		$id
	 * @return json|array
	 */
	public function changeStatus(string $model, string $value ,int $id)
	{
		if($model == 'invoice_transaction'){
			$model = loadClass($model);
			$model->id = $id;
			$value = ($value == 'approved') ? 'paid' : 'not paid';
			$message = ($value == 'paid') ? "Payment has been approved" : "Payment has been disapproved";
			if($model->load()){
				$model->payment_status = $value;
				if($model->update($id)){
					if($value == 'paid'){
						$this->invoicesTransactionSuccess($model->invoices_id);
					}
					echo createJsonMessage('status',true,'message',$message,'flagAction',true);
				}else{
					echo createJsonMessage('status',false,'message',"Something went wrong, action can't be performed",'flagAction',false);
				}
			}else{
				echo createJsonMessage('status',false,'message',"Action can't be performed",'flagAction',false);
			}
		}
	}

	/**
	 * @param string 	$model
	 * @param int 		$id
	 * @return json|array
	 */
	public function mail(string $model, $id)
	{
		if($model == 'invoices'){
			if(!$this->sendInvoiceMail($model, $id)){
				echo createJsonMessage('status',false,'message','Something went wrong and unable to perform the action. Please try again later','flagAction',false);
				return false;
			}
			echo createJsonMessage('status',true,'message', "You have successfully sent the invoice via mail",'flagAction',true);
			return true;
		}
	}

	private function logAction($user,$model,$description){
		$applicationLog = loadClass('application_log');
		$applicationLog->log($user,$model,$description);
	}

	private function invoicesTransactionSuccess($id){
		$invoices = loadClass('invoices');
		$templateVariables = $invoices->buildInvoiceData($id);
		if(!$templateVariables){
		 	return false;
		}

		$email = $templateVariables['bill_from_email'];
		$param = [
			'customer_name' => $templateVariables['bill_from_name'],
			'order_number' => $templateVariables['order_number'],
			'invoice_number' => $templateVariables['order_number'],
			'invoice_date' => $templateVariables['invoice_date'],
			'tracking_number' => $templateVariables['track_number']
		];
		$template = $this->mailer->mailTemplateRender($param, 'invoice-payment');
		$this->mailer->sendNotificationMail($email, 'payment_success');
		return true;
	}

	private function invoiceTransactionValidation($id){
		$transaction = loadClass('invoice_transaction');
		$transaction = $transaction->geWhere(['invoices_id' => $id], $c, 0, null, false, 'order by payment_date desc');
		if(!$transaction){
			return false;
		}
		$transaction = $transaction[0];
		if($transaction->payment_status == 'not paid'){
			return false;
		}
		return true;
	}

	private function sendInvoiceMail(string $model, $id)
	{
		$invoices = loadClass('invoices');
		$templateVariables = $invoices->buildInvoiceData($id);
		if(!$templateVariables){
		 	return false;
		}

		$emailBuffer = $this->prepInvoicePdf($templateVariables);

		$email = $templateVariables['bill_from_email'];
		$param = [
			'customer_name' => $templateVariables['bill_from_name'],
			'order_number' => $templateVariables['order_number'],
			'invoice_number' => $templateVariables['order_number'],
			'invoice_date' => $templateVariables['invoice_date'],
			'tracking_number' => $templateVariables['track_number']
		];
		$template = $this->mailer->mailTemplateRender($param, 'invoice-shipping');
		$this->mailer->setAttachment([
			'content' => $emailBuffer,
			'filename' => underscore($templateVariables['bill_from_name'])."_shipment_invoice.pdf",
			'content_type' => 'application/pdf'
		]);
		$this->mailer->sendNotificationMail($email, 'payment_invoice', ['order_number' => $param['order_number']]);
		return true;
	}

	private function prepInvoicePdf(array $templateVariables){
		$invoices = loadClass('invoices');
		$parser = \Config\Services::parser();
		// $content = $parser->setData($templateVariables)->render('admin/preview');
		
		$template = view('admin/invoice-template');
		$template = '
			<html>

			<head>
			  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			  <meta http-equiv="Content-Style-Type" content="text/css" />
			  <title></title>
			</head>
				<body style="margin-bottom:10px;">'.$template.'</body>
			</html>
		';
		$html = $parser->setData($templateVariables)->renderString($template);
		$pdfname = ROOTPATH. 'temp/'.underscore($templateVariables['bill_from_name'])."_shipment_invoice";
		return $invoices->initMpdfLibrary($html, $pdfname, 'S');
	}


}
