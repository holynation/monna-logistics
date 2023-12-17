<?php 

/**
* The controller that validate forms that should be inserted into a table based on the request url.
* each method wil have the structure validate[modelname]Data
*/
namespace App\Models;

class ModelControllerDataValidator
{

	public function __construct(){
		helper('string');
	}

	public function validateQuestionsData(&$data,$type,&$db,&$message)
	{
		$validation = \Config\Services::validation();
		$otherData = [
			'option_a' => request()->getPost('option_a'),
			'option_b' => request()->getPost('option_b'),
			'option_c' => request()->getPost('option_c'),
			'option_d' => request()->getPost('option_d'),
			'temperament_id' => request()->getPost('temperament_id'),
		];
		$validationData = array_merge($data, $otherData);
		$testType = $db->table('test_type');
		$slug = $testType->getWhere(['id' => $data['test_type_id']]);
		if($slug->getNumRows() <= 0){
			$message = "Please ensure Psychometric type has been populated with data";
			return false;
		}

		$validation->setRule('question', 'Question', 'trim|required');
		$validation->setRule('test_type_id', 'Psychometric type', 'trim|required');
		$validation->setRule('option_a', 'Option A', 'trim|required');
		$validation->setRule('option_b', 'Option B', 'trim|required');
		$validation->setRule('option_d', 'Option d', 'trim');
		$validation->setRule('option_e', 'Option e', 'trim');
		$validation->setRule('option_f', 'Option f', 'trim');
		$validation->setRule('option_g', 'Option g', 'trim');
		($slug->getRow()->slug === 'PSY-PER') ? $validation->setRule('temperament_id', 'Temperament', 'trim|required') : null;
		($slug->getRow()->slug === 'PSY-PER') ? $validation->setRule('option_c', 'Option C', 'trim|required') : $validation->setRule('option_c', 'Option C', 'trim');
		$validation->setRule('model_update_id', '', 'if_exist|is_natural_no_zero');
		$validation->setRule('question_order', 'Question Order', 'trim|required|numeric|is_unique[questions.question_order,ID,{model_update_id}]');

		if (! $validation->run($validationData)) {
			$errors = $validation->getErrors();
			foreach($errors as $error){
				$message = $error;
				return false;
			}
		}

		$data['question'] = ucfirst($data['question']);
		$data['option_a'] = ucfirst(request()->getPost('option_a'));
		$data['option_b'] = ucfirst(request()->getPost('option_b'));
		$data['option_c'] = ucfirst(request()->getPost('option_c'));
		$data['option_d'] = ucfirst(request()->getPost('option_d'));
		$data['temperament_id'] = ucfirst(request()->getPost('temperament_id'));
		$data['test_slug'] = $slug->getRow()->slug;
		if($data['test_slug'] == 'PSY-COM'){
			$data['option_e'] = ucfirst(request()->getPost('option_e'));
			$data['option_f'] = ucfirst(request()->getPost('option_f'));
			$data['option_g'] = ucfirst(request()->getPost('option_g'));
		}
		return true;
	}

	public function validateCustomer_transactionData(&$data,$type,&$db,&$message)
	{
		if($type == 'insert'){
			$desc = $this->getPaymentDescription($db, $data['payment_description_id']);
			$data['transaction_ref'] = generateHashRef('receipt');
			$data['description'] = $desc->description;
			$data['fee_description_id'] = $desc->fee_description_id;
		}

		return true;
	}

	public function validateTemperament_detailsData(&$data,$type,&$db,&$message)
	{
		$validation = \Config\Services::validation();
		$otherData = [
			'temparament_count' => count($data['temparament'])
		];
		$validationData = array_merge($data, $otherData);
		$validation->setRule('temparament.*', 'temparament', 'required');
		$validation->setRule('temparament_count', 'temparament', 'less_than_equal_to[2]|greater_than[1]', [
			'less_than_equal_to' => 'You are to choose only two option for temperament field',
			'greater_than' => 'You are to choose only two option for temperament field'
		]);
		$validation->setRule('detail', 'detail', 'required');

		if (!$validation->run($validationData)) {
			$errors = $validation->getErrors();
			foreach($errors as $error){
				$message = $error;
				return false;
			}
		}
		$temperament = implode('-', $data['temparament']);
		$data['temparament'] = $temperament;
		
		return true;
	}

	private function getPaymentDescription($db, $paymentDescriptionID){
		$query = "SELECT fee_description_id,description from payment_description join fee_description on fee_description.id = payment_description.fee_description_id where payment_description.id = ?";
		$result = $db->query($query, [$paymentDescriptionID]);
		$result = $result->getRow();
		return $result;
	}

}


?>