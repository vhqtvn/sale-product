<?php   
class UsersController extends AppController   
{   
	public $helpers = array('Html', 'Form');//,'Ajax','Javascript
	var $uses = array('User');

    function login()   
    {   
        $this->set('error', false);   
        // If a user has submitted form data:  
        if (!empty($this->data))  
        {  
        	//输入密码
        	$passwd = $this->data['password'] ;
            $md5passwd = md5($passwd) ;
        	
            //数据库查询对象 
            $someone = $this->User->queryUserByUserName($this->data['username']);  
            $dbpasswd = $someone[0]['sc_user']['PASSWORD'] ;
            
            if(!empty($dbpasswd) &&$dbpasswd == $md5passwd ){  
            	
            	setcookie("userId",$someone[0]['sc_user']['LOGIN_ID'],null ,"/") ;
                //$this->Session->write('User', $someone[0]['sc_user']);  
                $this->redirect("/home");
            }  
            // Else, they supplied incorrect data:  
            else  
            {  
                // Remember the $error var in the view? Let's set that to true:   
                $this->set('error', true);   
            }   
        }   
    }   
  
    function logout()   
    {   
        // Redirect users to this action if they click on a Logout button.   
        // All we need to do here is trash the session information:   
       //$this->Session->delete('User'); 
        setcookie("userId", "", time() - 3600,"/");  
        
        print_r($_COOKIE) ;
        // And we should probably forward them somewhere, too...   
        $this->redirect('/');   
    }  
    
    function listUsers(){
    } 
    
    function selectUsers(){
    	
    }
    
    function listGroups(){
    }
    
    function listFunctions($id=null){
    }
    
    function saveFunctoin(){
    	$this->User->saveFunctoin($this->request->data) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    function editFunction($id = null){
    	if($id == null){
    		$this->set("function",null) ;
    	}else{
    		$someone = $this->User->getFunctionById($id);  
    	$this->set("function",$someone) ;
    	}
    	
    }
    
    function assignFunctions( $code ){
		$someone = $this->User->getFunctionRelGroups($code);  
    	$this->set("Functions",$someone) ;
    	$this->set("GroupCode",$code) ;
	}
	
	function saveAssignFunctions($code , $ids ){
		//$ids = $this->request->data['id'] ;
		$someone = $this->User->saveAssignFunctions($ids , $code ); 
		
		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ; 
	}
    
    function editUser($username = null ){
    	$someone = $this->User->queryUserByUserName($username);  
    	$groups = $this->User->queryUserGroups();  

    	$this->set("User",$someone) ;
    	$this->set("Groups",$groups) ;
    }
    
    function saveUser(){
		$this->User->saveUser($this->request->data) ;

		//$this->Product->save( $params ) ;
		$this->response->type("json") ;
		$this->response->body( "Save Success" )   ;

		return $this->response ;
    }
    
    function hasAuth($url){
    	
    }
} 