<?php

namespace App\Controllers;

class Logistic extends BaseController {

	public function index()
	{
		return $this->home();
	}

	public function home(){
		$data = array();
		helper('form');
		return view('logistic/login', $data);
	}

}
