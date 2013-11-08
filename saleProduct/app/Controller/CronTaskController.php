<?php

class CronTaskController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Utils');
    
    public function clearLimitPrice(){
    	$this->Utils->exeSql("delete from sc_sale_schedule",array()) ;
    }
	
}