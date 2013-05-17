<?php
if(!defined('OSTSCPINC') or !is_object($thisuser) or !$thisuser->canManageKb()) die('Access Denied');

//List premade answers.
$select='SELECT ost_category.* ';
$from='FROM ost_category ';

//make sure the search query is 3 chars min...defaults to no query with warning message
if($_REQUEST['a']=='search') {
    if(!$_REQUEST['query'] || strlen($_REQUEST['query'])<3) {
        $errors['err']='Search term must be more than 3 chars';
    }else{
        //fulltext search.
        $search=true;
        $qstr.='&a='.urlencode($_REQUEST['a']);
        $qstr.='&query='.urlencode($_REQUEST['query']);
        $where=' WHERE MATCH(title,answer) AGAINST ('.db_input($_REQUEST['query']).')';
        if($_REQUEST['dept'])
            $where.=' AND dept_id='.db_input($_REQUEST['dept']);
    }
}

//I admit this crap sucks...but who cares??
$sortOptions=array('createdate'=>'premade.created','updatedate'=>'premade.updated','title'=>'premade.title');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
//Sorting options...
if($_REQUEST['sort']) {
    $order_column =$sortOptions[$_REQUEST['sort']];
}

if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}


$order_column=$order_column?$order_column:'premade.title';
$order=$order?$order:'DESC';

$order_by=$search?'':" ORDER BY $order_column $order ";


$total=db_count('SELECT count(*) '.$from.' '.$where);
$pagelimit=$thisuser->getPageLimit();
$pagelimit=$pagelimit?$pagelimit:PAGE_LIMIT; //true default...if all fails.
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('category.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
//Ok..lets roll...create the actual query
$query="$select $from $where order by parent_id,ordernum LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
$replies = db_query($query);
$showing=db_num_rows($replies)?$pageNav->showing():'';
$results_type=($search)?'Search Results':'Premade/Canned Replies';
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>

<div class="msg"><?=$result_type?>&nbsp;<?=$showing?></div>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
   <form action="category.php" method="POST" name="premade" onSubmit="return checkbox_checker(document.forms['premade'],1,0);">
   <input type=hidden name='a' value='process'>
   <tr><td>
     <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
                <th>Category Name</th>
                <th>Position</th>
	        <th>Parent Name</th> 
            
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $grps=($errors && is_array($_POST['grps']))?$_POST['grps']:null;
        if($replies && db_num_rows($replies)):
            while ($row = db_fetch_array($replies)) {
                $sel=false;
                if($canned && in_array($row['category_id'],$canned)){
                    $class="$class highlight";
                    $sel=true;
                }elseif($replyID && $replyID==$row['category_id']) {
                    $class="$class highlight";
                }
                ?>
            <tr class="<?=$category?>" id="<?=$row['category_id']?>">
                <td width=7px>
                  <input type="checkbox" name="canned[]" value="<?=$row['category_id']?>" <?=$sel?'checked':''?> 
                        onClick="highLight(this.value,this.checked);">
               
                <td><a href="category.php?id=<?$categoryId=$row['category_id'];echo $categoryId?>"><?=$categoryId?>:<?=$row['name']?></a></td>
                <td><?=$row['ordernum']?></td>
                <?php $parentId=$row['parent_id']?>
                <td><?=$parentId?>:
                <?php if($parentId!=0){
                		$parentName=db_query('select name from ost_category where category_id='.$parentId.' order by ordernum');
               		   $pana=db_fetch_array($parentName);
               		   echo $pana['name'];
                	}else echo 'No Parent';
                ?></td>
 
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //nothin' found!! ?> 
            <tr class="<?=$class?>"><td colspan=6><b>Query returned 0 results</b></td></tr>
        <?
        endif; ?>
    </table>
   </td></tr>
   <?
   if(db_num_rows($replies)>0): //Show options..
    ?>
   <tr><td style="padding-left:20px">
        Select:&nbsp;
        <a href="#" onclick="return select_all(document.forms['premade'],true)">All</a>&nbsp;
        <a href="#" onclick="return toogle_all(document.forms['premade'],true)">Toggle</a>&nbsp;
        <a href="#" onclick="return reset_all(document.forms['premade'])">None</a>&nbsp;
        &nbsp;page:<?=$pageNav->getPageLinks()?>&nbsp;
    </td></tr>
    <tr><td align="center"> 
            <input class="button" type="submit" name="delete" value="Delete" 
                onClick='return confirm("Are you sure you want to DELETE selected entries?");'>
    </td></tr>
    <?
    endif;
    ?>
   </form>
 </table>
