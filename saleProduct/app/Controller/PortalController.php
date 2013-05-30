<?php

class PortalController extends AppController {
	
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
	}

	public function index() {
		$this->layout="../Portal/index";
    }
   
}