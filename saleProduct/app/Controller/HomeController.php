<?php

class HomeController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    var $uses = array('Salegrid','Widget');
    
    function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('*');
	}

	public function index() {
		$this->layout="index";
    }
    
    public function widgets(){
    	
    }
    
    public function phone(){
    	/*$user =  $this->getCookUser() ;
    	$query = array('limit'=>100,'start'=>0,'curPage'=>0,'end'=>100) ;
		$records=  $this->Salegrid->getPurchasePlanRecords( $query ,$user,2 ) ;//2采购执行列表
		print_r($records) ;*/
    }
}