<?php

App :: import('Vendor', 'UploadHandler');

class UploadController extends AppController {
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	
	var $uses = array('Sale', 'Product','Amazonaccount');
	
	public function image(){
		$uploadHandler = new UploadHandler() ;
	}
	
}