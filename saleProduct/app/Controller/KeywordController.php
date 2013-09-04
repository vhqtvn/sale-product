<?php
ignore_user_abort(1);
set_time_limit(0);

ini_set("memory_limit", "62M");
ini_set("post_max_size", "24M");

class KeywordController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array('Keyword', "Utils","Log");
	
	public function doUpload($accountId){
		
    	$params = $this->request->data  ;
    	$keywordId = $params['keywordId'] ;
    	$keywordType = $params['keywordType'] ;
    	
    	$user =  $this->getCookUser() ;
    	$loginId = $user["LOGIN_ID"] ;
    	
    	$ps = array() ;
    	$ps['taskId'] = $params['taskId'] ;
    	$ps['loginId'] = $loginId ;
    	$ps['site'] = $params['site'] ;
    	
    	if(isset($_FILES['keywordFile'])){
    		$fileName = $_FILES['keywordFile']["name"] ;
			$myfile = $_FILES['keywordFile']['tmp_name'] ;
			$user =  $this->getCookUser() ;
			//save db
			$id = "O_".date('U') ;
			
			$file_handle = fopen($myfile , "r");
			$count = 0 ;
			$isFirst = true ;
			while (!feof($file_handle)) {
			   $line = fgets($file_handle);
			   
			   if( trim($line) != "" ){
			   		if( $isFirst ){
			   			$isFirst = false ;
			   		}else{
			   			$line = trim($line) ;
			   			$this->Keyword->parseKeywordRow($line,  $ps , $keywordId , $keywordType ,$count) ;
			   		}
			   }
			}
			fclose($file_handle);
    		
    		$this->response->type("html");
			$this->response->body("<script type='text/javascript'>window.close();</script>");
    	}
				
		
		return $this->response;
    
	}
}