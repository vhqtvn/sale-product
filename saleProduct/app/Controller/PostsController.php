<?php
class PostsController extends AppController {
    public $helpers = array('Html', 'Form');//,'Ajax','Javascript

	public function grid(){
		// if ( $this ->RequestHandler->isAjax()){ //判断是否是ajax请求 
              //    $this ->set( 'knownusers' , "aabbcc"); 
                //  $this ->render(); //使用render将数据返回给视图 
       //    }
 $my_array2= array ('one'=>'1','two'=>'2','3');

	$this->response->type("json") ;
	$this->response->body(  json_encode($my_array2) )   ;

    return $this->response ;
	  //return new CakeResponse( array('body'=> $array  , 'type' =>'json')  );
	}

	public function index() {
        $this->set('posts', $this->Post->find('all'));
    }

	public function view($id = null) {
        $this->Post->id = $id;
        $this->set('post', $this->Post->read());
    }

	public function add() {
        if ($this->request->is('post')) {
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash('Your post has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your post.');
            }
        }
    }
}