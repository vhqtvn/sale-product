<?php
/*********************************************************************
    category.php
	water3

**********************************************************************/

require('staff.inc.php');
if(!$thisuser->canManageKb() && !$thisuser->isadmin()) die('Access denied');

$page='';
$answer=null; //clean start.
if(($id=$_REQUEST['id']?$_REQUEST['id']:$_POST['id']) && is_numeric($id)) {
    $replyID=0;
    $resp=db_query('SELECT * FROM ost_category WHERE category_id='.db_input($id));
    if($resp && db_num_rows($resp))
        $answer=db_fetch_array($resp);
    else
        $errors['err']='Unknown ID#'.$id; //Sucker...invalid id
    
    if(!$errors && $answer['category_id']==$id)
        $page='category.inc.php';
}

if($_POST):
    $errors=array();
    switch(strtolower($_POST['a'])):
    case 'update':
    case 'add':
        if(!$_POST['id'] && $_POST['a']=='update')
            $errors['err']='Missing or invalid group ID';

        if(!$_POST['parent'])
            $errors['parent']='Parent required';
 
        if(!$_POST['category'])
        	$errors['category']='Category Name required';

        if(!$errors){
            $sql=' SET name='.db_input(Format::striptags($_POST['category'])).
                ', ordernum='.db_input($_POST['ordernum']).
                 ', parent_id='.db_input($_POST['parent']);
        
            if($_POST['a']=='add'){ //create
                $res=db_query('INSERT INTO ost_category '.$sql);
                if(!$res or !($replyID=db_insert_id()))
                    $errors['err']='Unable to create the reply category. Internal error';
                else
                    $msg='Category created';
            }elseif($_POST['a']=='update'){ //update
                $res=db_query('UPDATE ost_category '.$sql.' WHERE category_id='.db_input($_POST['id']));
                if($res && db_affected_rows()){
                    $msg='Category updated';
                    $answer=db_fetch_array(db_query('SELECT * FROM ost_category WHERE category_id='.db_input($id)));
                }
                else
                    $errors['err']='Internal update error occured. Try again';
            }
            if($errors['err'] && db_errno()==1062)
                $errors['title']='Title already exists!';
            
        }else{
            $errors['err']=$errors['err']?$errors['err']:'Error(s) occured. Try again';
        }
        break;
    case 'process':
        if(!$_POST['canned'] || !is_array($_POST['canned']))
            $errors['err']='You must select at least one item';
        else{
            $msg='';
            $ids=implode(',',$_POST['canned']);
            $selected=count($_POST['canned']);
            if(isset($_POST['delete'])) {
                if(db_query('DELETE FROM ost_category WHERE category_id IN('.$ids.')'))
                    $msg=db_affected_rows()." of  $selected selected categories deleted";
            }

            if(!$msg)
                $errors['err']='Error occured. Try again';
        }
        break;
    default:
        $errors['err']='Unknown action';
    endswitch;
endif;
//new reply??
if(!$page && $_REQUEST['a']=='add' && !$replyID)
    $page='category.inc.php';

    $inc=$page?$page:'categories.inc.php';

$nav->setTabActive('category');
$nav->addSubMenu(array('desc'=>'Categoryes','href'=>'category.php','iconclass'=>'premade'));
$nav->addSubMenu(array('desc'=>'New Category','href'=>'category.php?a=add','iconclass'=>'newPremade'));
require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');

?>
