<?php

class HomeController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
	}

	public function index() {
		$this->layout="index";
    }
}