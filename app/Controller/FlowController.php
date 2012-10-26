<?php

class FlowController extends AppController {
    public function upload(){
    	
    }
    public function lists($taskid = null){
    	$this->set("taskId" ,$taskid ) ;
    }
}