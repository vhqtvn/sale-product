<?php
if(!defined('OSTSCPINC') or !$thisuser->canManageKb()) die('Access Denied');
$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($answer);
if($answer && $_REQUEST['a']!='add') {
    $title='Edit Reply Category';
    $action='update';
}else {
    $title='Add New Reply Category';
    $action='add';
    $info['isenabled']=1;
}
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
<div class="msg"><?=$title?></div>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="category.php" method="POST" name="group">
        <input type="hidden" name="a" value="<?=$action?>">
        <input type="hidden" name="id" value="<?=$info['category_id']?>">
        <tr>
            <td width=80px>Category Name:</td>
            <td><input type="text" size=20 name="category" value="<?=$info['name']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['category']?></font>
            </td>
        </tr>
        <tr>
            <td width=80px>Position:</td>
            <td><input type="text" size=20 name="ordernum" value="<?=$info['ordernum']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['ordernum']?></font>
            </td>
        </tr>
        <tr>
            <td width=80px>Parent Category:</td>
            <td><br/>
                <select name="parent">
                    <option value="-1">No Parent</option>
                    <?
                    $categories= db_query('SELECT category_id,name FROM ost_category ORDER BY ordernum');
                    while (list($id,$name) = db_fetch_row($categories)) {
                        $ck=($info['parent_id']==$id)?'selected':''; ?>
                    <option value="<?=$id?>" <?=$ck?>><?=$id?>:<?=$name?></option>
                        <?
                    }?>
                </select>
            </td>
        </tr>
        <tr>
            <td nowrap>&nbsp;</td>
            <td><br>
                <input class="button" type="submit" name="submit" value="Submit">
                <input class="button" type="reset" name="reset" value="Reset">
                <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="category.php"'>
            </td>
        </tr>
    </form>
</table>
