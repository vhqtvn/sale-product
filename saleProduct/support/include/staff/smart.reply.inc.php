<?php
if(!defined('OSTSCPINC') or !$thisuser->canManageKb()) die('Access Denied');
$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($answer);
if($answer && $_REQUEST['a']!='add') {
    $title='Edit Premade Reply';
    $action='update';
}else {
    $title='Add New Premade Reply';
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
<div style="text-align: right">
     <?php
    $one = db_query("select isadmin from ost_staff where username='".$_SESSION['_staff']['userID']."'");
    $row = db_fetch_row($one);
    if($row[0]==1) {
        ?>
        <form action="importcsv.php" method="post" enctype="multipart/form-data" target="uploadIframe">
        IMPORT CSV:<input name="file" type="file" />
        <input type="submit" name="submit" value="submit"/>
    </form>
    <?php
    }
    ?>
    <iframe name="uploadIframe" id="uploadIframe" style="display:none"></iframe>
</div>
<script type="text/javascript">
    function cate()
    {
        var sel = document.forms["group"].categoryname;
        var s = "";
        for(var i=0;i<sel.options.length;i++){
            if(sel.options[i].selected) s += sel.options[i].value + ",";
        }
        if(s!="") s = s.substr(0,s.length-1);
        document.getElementById('category').value = s;
    }
</script>
<?php
$replyId=$_GET['id']?$_GET['id']:$_POST['id'];
if($replyId>0) {
    $getClassesSql='SELECT category_id FROM ost_premade_category WHERE premade_id='.$replyId;
    $classes=db_query($getClassesSql);
    $arr_cate = array();
    while (list($category_id) = db_fetch_row($classes)) {
        $arr_cate[] = $category_id;
    }
}
?>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
<?php
    function cate($parent,$nbsp='',$arr_cate=array()) {
        $cate= db_query('SELECT category_id,name,parent_id FROM ost_category where parent_id = '.$parent.' ORDER BY ordernum');
        echo '<div id="catediv_'.$parent.'">';
        while (list($id,$name,$parent_id) = db_fetch_row($cate)) {
            if(in_array($id,$arr_cate)) {
                echo $nbsp.'<input type="checkbox" name="category[]" value="'.$id.'" checked />'.$name.'<br />';
            }else {
                echo $nbsp.'<input type="checkbox" name="category[]" value="'.$id.'" />'.$name.'<br />';
            }
            cate($id,$nbsp.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$arr_cate);
        }
        echo '</div>';
    }
    ?>
    <script type="text/javascript" src="dtree_1.js"></script>
    <script type="text/javascript" src="js/jquery-1.6.1.js"></script>
    <script type="text/javascript">
        function checkall()
        {
            $("input:[type='checkbox']").prop({
                checked:true
            });
        }

        function checkoff()
        {
            $("input:[type='checkbox']").prop({
                checked:false
            });
        }

        function dchge(id)
        {
            var object = document.getElementById(id);
            $.get('ajax.category.php?id='+id, function(data) {
                var strs= new Array();
                strs = data.split(",");
                for (i=0;i<strs.length ;i++ )
                {
                    if(strs[i]>0)
                    {
                        if(object.checked)
                        {
                            $("#"+strs[i]).prop({
                                checked:true
                            });
                        }
                        else
                        {
                            $("#"+strs[i]).prop({
                                checked:false
                            });
                        }
                    }
                }
            });
        }

        function dchgeaa(id)
        {
            var object = document.getElementById(id);
            if(object.checked)
            {
                $("#"+id).prop({
                    checked:false
                });
            }else
            {
                $("#"+id).prop({
                    checked:true
                });
            }
            $.get('ajax.category.php?id='+id, function(data) {
                var strs= new Array();
                strs = data.split(",");
                for (i=0;i<strs.length ;i++ )
                {
                    if(strs[i]>0)
                    {
                        if(object.checked)
                        {
                            $("#"+strs[i]).prop({
                                checked:true
                            });
                        }
                        else
                        {
                            $("#"+strs[i]).prop({
                                checked:false
                            });
                        }
                    }
                }
            });
        }

        function check(obje)
        {
            if(obje.no.value.length!=4)
            {
                alert('must not be four number');
                obje.no.focus();
                return false;
            }
            return true;
        }
    </script>
    <form action="kb.php" method="POST" name="group" onsubmit="return check(this);">
        <input type="hidden" name="a" value="<?=$action?>">
        <input type="hidden" name="id" value="<?=$info['premade_id']?>">

        <tr><td width=80px>Category:</td>
            <td>
<?
                $class_sql='SELECT * FROM ost_category order by ordernum';
                $class=db_query($class_sql);
                if($class && db_num_rows($class)) {
                    ?>
                <p><a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a> | <a href="javascript: checkall();">check all</a> | <a href="javascript: checkoff();">check off</a></p>
    <?
                    $tempTreeStr="<script type=\"text/javascript\">d = new dTree('d');d.add(0,-1,'Category');";
                    if(!empty($_REQUEST['keyword'])) {
                        $tempTreeStr=$tempTreeStr.'d.closeAll();';
                    }
                    while(list($classId,$name,$parentId)=db_fetch_row($class)) {
                        if(in_array($classId, $arr_cate)) {
                            $checkbox = '<input type="checkbox" checked id="'.$classId.'" onclick="dchge('.$classId.')" name="category[]" value="'.$classId.'" />';
                        }else {
                            $checkbox = '<input type="checkbox" id="'.$classId.'" onclick="dchge('.$classId.')" name="category[]" value="'.$classId.'" />';
                        }
                        $tempTreeStr=$tempTreeStr."d.add($classId,$parentId,'&nbsp;$checkbox $name','#');";

//                        $tempTreeStr=$tempTreeStr."d.add($classId,$parentId,'&nbsp;$checkbox $name','javascript:dchgeaa(".$classId.")');";
                    }
                    $tempTreeStr=$tempTreeStr."document.write(d);</script>";
                    echo $tempTreeStr;
                }
                ?>
                &nbsp;<font class="error">*&nbsp;<?=$errors['category']?></font>
            </td>
        </tr>
        <tr>
            <td width=80px>No:</td>
            <td><input type="text" size=25 name="no" value="<?=$info['no']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['no']?></font>
            </td>
        </tr>
        <tr>
            <td width=80px>Title:</td>
            <td><input type="text" size=75 name="title" value="<?=$info['title']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['title']?></font>
            </td>
        </tr>
        <tr><td width=80px>Description:</td>
            <td><input type="text" size=75 name="description" value="<?=$info['description']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['title']?></font>
            </td>
        </tr>
        <tr>
            <td>Status:</td>
            <td>
                <input type="radio" name="isenabled"  value="1"   <?=$info['isenabled']?'checked':''?> /> Active
                <input type="radio" name="isenabled"  value="0"   <?=!$info['isenabled']?'checked':''?> />Offline
                &nbsp;<font class="error">&nbsp;<?=$errors['isenabled']?></font>
            </td>
        </tr>
        <tr><td valign="top">Category/Dept:</td>
            <td>Department under which the 'answer' will be made available.&nbsp;<font class="error">&nbsp;<?=$errors['depts']?></font><br/>
                <select name=dept_id>
                    <option value=0 selected>All Departments</option>
<?
                    $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name');
                    while (list($id,$name) = db_fetch_row($depts)) {
                        $ck=($info['dept_id']==$id)?'selected':''; ?>
                    <option value="<?=$id?>" <?=$ck?>><?=$name?></option>
    <?
                    }?>
                </select>
            </td>
        </tr>
        <tr><td valign="top">Answer:</td>
            <td>Premade Reply - Ticket's base variables are supported.&nbsp;<font class="error">*&nbsp;<?=$errors['answer']?></font><br/>
                <textarea name="answer" id="answer" cols="90" rows="9" wrap="soft" style="width:80%"><?=$info['answer'
        ]?></textarea>
            </td>
        </tr>
        <tr>
            <td nowrap>&nbsp;</td>
            <td><br>
                <input class="button" type="submit" name="submit" value="Submit">
                <input class="button" type="reset" name="reset" value="Reset">
                <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="kb.php"'>
            </td>
        </tr>
    </form>
</table>
