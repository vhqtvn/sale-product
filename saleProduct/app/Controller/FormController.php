<?php

class FormController extends AppController {
   	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
    
    var $uses = array('Config','Form','SqlUtils');
    
    public function ajaxSave(){
    	$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	
    	$this->Form->ajaxSave($params) ;
    	
    	$this->response->type("json");
		$this->response->body("execute complete");
		return $this->response;
    }
    
    public function dataService(){
    	
    	$user =  $this->getCookUser() ;
    	$params = $this->request->data  ;
    	$params['loginId'] = $user['LOGIN_ID'] ;
    	$command = $params['CommandName'] ;
    	
		
    	if(strpos($command,'model:')===0){
    		$command = str_replace("model:","",$command) ;
    		
    		$as = explode(".",$command) ;
			$clsName = "" ;
			$method  = "" ;
			$simpleClassName = "" ;
	 		if( count($as)<=2 ){
				$simpleClassName = $as[0] ;
				$clsName = $as[0] ;
				$method = $as[1] ;
			}else{
				$clsName = $as[0]."/".$as[1] ;
				$simpleClassName = $as[1] ;
				$method = $as[2] ;
			}
			try{
				App::import("model",$clsName) ;
				$r = new ReflectionClass($simpleClassName);
				
				$instance = $r->newInstance(); 
				$method = $r->getMethod($method) ;
				
				$result = $method->invoke($instance, $params); 
			}catch(Exception $e){
				echo $e->getMessage();
			}
				
		
    		/*$service = ClassRegistry::init($clsName)  ;
    		$r = new ReflectionClass($service); 
    		
    		$method = $r->getMethod($method) ;
    		$result = $method->invoke($service, $params); */
    	}else if(strpos($command,'sqlId:') === 0){
    		$sqlId = str_replace("sqlId:","",$command) ;
    		$params['sqlId'] = $sqlId ;
    		$params['start'] = 0 ;
    		$params['limit'] = 1000 ;
    		
    		$recordSql = $this->SqlUtils->getRecordSql( $params) ;
    		$result = $this->SqlUtils->query($recordSql) ;
    		$result = json_encode($result) ;
    	}
    	
    	$this->response->type("json");
		$this->response->body(empty($result)?'':$result);
		return $this->response;
    }
}